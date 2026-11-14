<?php

namespace App\Services\Push;

use App\Models\MemberNotification;
use App\Models\PushDevice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class PushDeliveryService
{
    public function __construct(
        private readonly ExpoPushService $expo,
        private readonly FcmPushService $fcm,
        private readonly WebPushService $webpush,
    ) {}

    public function deliver(string $notificationId): void
    {
        if (! config('push.delivery_enabled')) {
            return;
        }

        $claimed = MemberNotification::query()->whereKey($notificationId)->whereNull('push_attempted_at')->update([
            'push_attempted_at' => now(),
            'push_accepted_count' => 0,
            'push_failed_count' => 0,
        ]);
        if ($claimed === 0) {
            return;
        }
        $notification = MemberNotification::query()->findOrFail($notificationId);

        $devices = PushDevice::query()
            ->where(function ($query) use ($notification): void {
                if ($notification->user_id !== null) {
                    $query->where('user_id', $notification->user_id);

                    if ($notification->p_anggota_id !== null) {
                        $query->orWhere(function ($query) use ($notification): void {
                            $query->whereNull('user_id')
                                ->where('p_anggota_id', $notification->p_anggota_id);
                        });
                    }

                    return;
                }

                $query->where('p_anggota_id', $notification->p_anggota_id);
            })
            ->where('is_active', true)
            ->get();

        if ($devices->isEmpty()) {
            return;
        }

        $data = $this->normalizeData($notification);
        $result = new PushDeliveryResult;
        $webDevices = $devices->where('provider', 'webpush')->values();
        $nativeDevices = $devices->whereIn('provider', ['expo', 'fcm'])->values();
        $payloadBytes = strlen((string) json_encode([
            'notification' => [
                'title' => $notification->title,
                'body' => $notification->body,
            ],
            'data' => $data,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        if ($payloadBytes > config('push.max_provider_payload_bytes')) {
            foreach ($nativeDevices as $device) {
                $result->fail($device->id, 'Payload push melebihi batas 4096 byte.');
            }
        } else {
            foreach (['expo' => $this->expo, 'fcm' => $this->fcm] as $provider => $sender) {
                $subset = $nativeDevices->where('provider', $provider)->values();
                if ($subset->isNotEmpty()) {
                    $result->merge($this->sendSafely($sender, $subset, $notification, $data));
                }
            }
        }

        if (config('push.webpush.enabled') && $webDevices->isNotEmpty()) {
            $result->merge($this->sendSafely($this->webpush, $webDevices, $notification, $data));
        }

        if ($result->acceptedDeviceIds !== []) {
            foreach ($result->acceptedDeviceIds as $deviceId) {
                $query = PushDevice::query()->whereKey($deviceId);
                $snapshot = $devices->find($deviceId);
                if ($snapshot?->provider === 'webpush') {
                    $query->where('binding_id', $snapshot->binding_id);
                }
                $query->update(['last_error' => null]);
            }
        }

        foreach ($result->failedDeviceErrors as $deviceId => $error) {
            $updates = ['last_error' => $error];

            if (in_array($deviceId, $result->invalidDeviceIds, true)) {
                $updates['is_active'] = false;
                $updates['disabled_at'] = now();
            }

            $query = PushDevice::query()->whereKey($deviceId);
            $snapshot = $devices->find($deviceId);
            if ($snapshot?->provider === 'webpush') {
                $query->where('binding_id', $snapshot->binding_id);
            }
            $query->update($updates);
        }

        $notification->update([
            'push_accepted_count' => $result->acceptedCount(),
            'push_failed_count' => $result->failedCount(),
        ]);

        if ($result->failedCount() > 0) {
            Log::warning('Sebagian push notification gagal dikirim.', [
                'notification_id' => $notificationId,
                'accepted_count' => $result->acceptedCount(),
                'failed_count' => $result->failedCount(),
            ]);
        }
    }

    private function sendSafely(object $sender, Collection $devices, MemberNotification $notification, array $data): PushDeliveryResult
    {
        try {
            return $sender->send($devices, $notification, $data);
        } catch (Throwable) {
            $result = new PushDeliveryResult;
            foreach ($devices as $device) {
                $result->fail($device->id, 'Push provider gagal mengirim.');
            }

            return $result;
        }
    }

    /**
     * @return array<string, string>
     */
    private function normalizeData(MemberNotification $notification): array
    {
        $data = collect($notification->data ?? [])
            ->map(fn ($value): string => $this->stringify($value))
            ->all();

        return array_merge($data, [
            'notification_id' => (string) $notification->id,
            'source' => (string) $notification->source,
            'type' => (string) $notification->type,
        ]);
    }

    private function stringify(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

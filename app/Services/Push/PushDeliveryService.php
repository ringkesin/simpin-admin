<?php

namespace App\Services\Push;

use App\Models\MemberNotification;
use App\Models\PushDevice;
use Illuminate\Support\Facades\Log;
use Throwable;

class PushDeliveryService
{
    public function __construct(
        private readonly ExpoPushService $expo,
        private readonly FcmPushService $fcm,
    ) {}

    public function deliver(string $notificationId): void
    {
        if (! config('push.delivery_enabled')) {
            return;
        }

        $notification = MemberNotification::query()->find($notificationId);

        if (! $notification || $notification->push_attempted_at !== null) {
            return;
        }

        $notification->update([
            'push_attempted_at' => now(),
            'push_accepted_count' => 0,
            'push_failed_count' => 0,
        ]);

        $devices = PushDevice::query()
            ->where('p_anggota_id', $notification->p_anggota_id)
            ->where('is_active', true)
            ->get();

        if ($devices->isEmpty()) {
            return;
        }

        $data = $this->normalizeData($notification);
        $result = new PushDeliveryResult;
        $payloadBytes = strlen((string) json_encode([
            'notification' => [
                'title' => $notification->title,
                'body' => $notification->body,
            ],
            'data' => $data,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        if ($payloadBytes > config('push.max_provider_payload_bytes')) {
            foreach ($devices as $device) {
                $result->fail($device->id, 'Payload push melebihi batas 4096 byte.');
            }
        } else {
            try {
                $expoDevices = $devices->where('provider', 'expo')->values();
                $fcmDevices = $devices->where('provider', 'fcm')->values();

                if ($expoDevices->isNotEmpty()) {
                    $result->merge($this->expo->send($expoDevices, $notification, $data));
                }

                if ($fcmDevices->isNotEmpty()) {
                    $result->merge($this->fcm->send($fcmDevices, $notification, $data));
                }
            } catch (Throwable $exception) {
                Log::error('Push delivery gagal.', [
                    'notification_id' => $notificationId,
                    'error' => $exception->getMessage(),
                ]);

                foreach ($devices as $device) {
                    if (! in_array($device->id, $result->acceptedDeviceIds, true)
                        && ! array_key_exists($device->id, $result->failedDeviceErrors)) {
                        $result->fail($device->id, 'Push delivery gagal: '.$exception->getMessage());
                    }
                }
            }
        }

        if ($result->acceptedDeviceIds !== []) {
            PushDevice::query()
                ->whereIn('id', $result->acceptedDeviceIds)
                ->update(['last_error' => null]);
        }

        foreach ($result->failedDeviceErrors as $deviceId => $error) {
            $updates = ['last_error' => $error];

            if (in_array($deviceId, $result->invalidDeviceIds, true)) {
                $updates['is_active'] = false;
                $updates['disabled_at'] = now();
            }

            PushDevice::query()->whereKey($deviceId)->update($updates);
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

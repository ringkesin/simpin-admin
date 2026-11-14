<?php

namespace App\Services\Push;

use App\Models\Master\AnggotaModels;
use App\Models\MemberNotification;
use App\Models\PushDevice;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class WebPushService
{
    public function __construct(
        private readonly WebPushConfiguration $configuration,
        private readonly WebPushSubscription $subscriptions,
        private readonly WebPushEndpointPolicy $policy,
        private readonly WebPushTransport $transport,
    ) {}

    public function send(Collection $devices, MemberNotification $notification, array $data): PushDeliveryResult
    {
        $result = new PushDeliveryResult;
        // The main dispatcher excludes disabled Web Push entirely.
        if (! $this->configuration->enabled()) {
            foreach ($devices as $device) {
                $result->fail($device->id, 'Konfigurasi Web Push tidak tersedia.');
            }

            return $result;
        }
        $recipientId = $notification->user_id ?? AnggotaModels::query()->whereKey($notification->p_anggota_id)->value('user_id');

        foreach ($devices as $device) {
            try {
                if (! $recipientId || (int) $device->user_id !== (int) $recipientId || ! $device->binding_id) {
                    $result->fail($device->id, 'Binding penerima Web Push tidak valid.');

                    continue;
                }
                $subscription = json_decode($device->provider_identifier, true, flags: JSON_THROW_ON_ERROR);
                $subscription = json_decode($this->subscriptions->normalize($subscription), true, flags: JSON_THROW_ON_ERROR);
                $payload = json_encode([
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'data' => array_merge($data, [
                        'recipient_user_id' => (string) $recipientId,
                        'binding_id' => $device->binding_id,
                    ]),
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                if (strlen($payload) > config('push.webpush.max_payload_bytes')) {
                    $result->fail($device->id, 'Payload Web Push melebihi batas 3000 byte.');

                    continue;
                }

                for ($attempt = 1; $attempt <= 2; $attempt++) {
                    $current = PushDevice::query()->find($device->id);
                    if (! $current || ! $current->is_active || $current->binding_id !== $device->binding_id
                        || (int) $current->user_id !== (int) $recipientId) {
                        $result->fail($device->id, 'Binding Web Push telah berubah/nonaktif.');
                        break;
                    }
                    $resolved = $this->policy->resolve($subscription['endpoint']);
                    try {
                        $report = $this->transport->send($subscription, $payload, $this->configuration->auth(), $resolved);
                    } catch (Throwable) {
                        // Unexpected transport/configuration errors are sanitized and are not retried.
                        $result->fail($device->id, 'Transport Web Push gagal.');
                        break;
                    }
                    if ($report['accepted']) {
                        $result->accept($device->id);
                        break;
                    }
                    $status = $report['status'];
                    $delay = $this->retryDelay($report['retry_after']);
                    if ($attempt === 1 && ($status === null || $status === 429 || $status >= 500) && $delay <= 2) {
                        $this->pause($delay);

                        continue;
                    }
                    $result->fail($device->id, $status === null ? 'Web Push timeout/koneksi gagal.' : 'Web Push HTTP '.(int) $status.'.', in_array($status, [404, 410], true));
                    break;
                }
            } catch (Throwable) {
                $result->fail($device->id, 'Subscription atau konfigurasi Web Push tidak valid.');
            }
        }

        return $result;
    }

    private function retryDelay(?string $value): int
    {
        if ($value === null || trim($value) === '') {
            return 1;
        }
        $value = trim($value);
        if (ctype_digit($value)) {
            return max(1, (int) $value);
        }
        $time = strtotime($value);

        return $time === false ? 1 : max(1, $time - time());
    }

    protected function pause(int $seconds): void
    {
        sleep($seconds);
    }
}

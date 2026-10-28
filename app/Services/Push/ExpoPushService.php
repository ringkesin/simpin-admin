<?php

namespace App\Services\Push;

use App\Models\MemberNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Throwable;

class ExpoPushService
{
    /**
     * @param  Collection<int, \App\Models\PushDevice>  $devices
     * @param  array<string, string>  $data
     */
    public function send(Collection $devices, MemberNotification $notification, array $data): PushDeliveryResult
    {
        $result = new PushDeliveryResult;
        $accessToken = (string) config('push.expo.access_token');

        if ($accessToken === '') {
            foreach ($devices as $device) {
                $result->fail($device->id, 'EXPO_PUSH_ACCESS_TOKEN belum dikonfigurasi.');
            }

            return $result;
        }

        foreach ($devices->chunk((int) config('push.expo.batch_size', 100)) as $chunk) {
            $messages = $chunk->map(fn ($device): array => [
                'to' => $device->provider_identifier,
                'title' => $notification->title,
                'body' => $notification->body,
                'sound' => 'default',
                'data' => $data,
            ])->values()->all();

            try {
                $response = Http::withToken($accessToken)
                    ->acceptJson()
                    ->connectTimeout((int) config('push.connect_timeout'))
                    ->timeout((int) config('push.http_timeout'))
                    ->post((string) config('push.expo.endpoint'), $messages);

                if (! $response->successful()) {
                    $error = 'Expo HTTP '.$response->status().': '.$response->body();

                    foreach ($chunk as $device) {
                        $result->fail($device->id, $error);
                    }

                    continue;
                }

                $tickets = $response->json('data');
                $tickets = is_array($tickets) ? array_values($tickets) : [];

                foreach ($chunk->values() as $index => $device) {
                    $ticket = $tickets[$index] ?? null;

                    if (is_array($ticket) && ($ticket['status'] ?? null) === 'ok') {
                        $result->accept($device->id);

                        continue;
                    }

                    $providerError = is_array($ticket) ? ($ticket['details']['error'] ?? null) : null;
                    $message = is_array($ticket)
                        ? (string) ($ticket['message'] ?? 'Expo tidak mengembalikan push ticket yang valid.')
                        : 'Expo tidak mengembalikan push ticket yang valid.';

                    $result->fail($device->id, $message, $providerError === 'DeviceNotRegistered');
                }
            } catch (Throwable $exception) {
                foreach ($chunk as $device) {
                    $result->fail($device->id, 'Expo request gagal: '.$exception->getMessage());
                }
            }
        }

        return $result;
    }
}

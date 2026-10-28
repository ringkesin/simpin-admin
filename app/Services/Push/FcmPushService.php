<?php

namespace App\Services\Push;

use App\Models\MemberNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Throwable;

class FcmPushService
{
    public function __construct(private readonly FcmAccessTokenProvider $tokens) {}

    /**
     * @param  Collection<int, \App\Models\PushDevice>  $devices
     * @param  array<string, string>  $data
     */
    public function send(Collection $devices, MemberNotification $notification, array $data): PushDeliveryResult
    {
        $result = new PushDeliveryResult;

        try {
            $accessToken = $this->tokens->get();
        } catch (Throwable $exception) {
            foreach ($devices as $device) {
                $result->fail($device->id, 'FCM auth gagal: '.$exception->getMessage());
            }

            return $result;
        }

        $projectId = (string) config('push.fcm.project_id');
        $endpoint = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        foreach ($devices as $device) {
            try {
                $response = Http::withToken($accessToken)
                    ->acceptJson()
                    ->connectTimeout((int) config('push.connect_timeout'))
                    ->timeout((int) config('push.http_timeout'))
                    ->post($endpoint, [
                        'message' => [
                            'token' => $device->provider_identifier,
                            'notification' => [
                                'title' => $notification->title,
                                'body' => $notification->body,
                            ],
                            'data' => $data,
                        ],
                    ]);

                if ($response->successful() && is_string($response->json('name'))) {
                    $result->accept($device->id);

                    continue;
                }

                $responseBody = $response->json();
                $serializedError = (string) json_encode($responseBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $isUnregistered = str_contains($serializedError, 'UNREGISTERED');
                $result->fail(
                    $device->id,
                    'FCM HTTP '.$response->status().': '.($serializedError ?: $response->body()),
                    $isUnregistered
                );
            } catch (Throwable $exception) {
                $result->fail($device->id, 'FCM request gagal: '.$exception->getMessage());
            }
        }

        return $result;
    }
}

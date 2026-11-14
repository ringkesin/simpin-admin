<?php

namespace App\Services\Push;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushTransport
{
    public function send(array $subscription, string $payload, array $auth, array $resolved): array
    {
        $address = str_contains($resolved['address'], ':') ? '['.$resolved['address'].']' : $resolved['address'];
        $sender = new WebPush(['VAPID' => $auth], ['TTL' => 300, 'urgency' => 'normal'], (int) config('push.http_timeout'), $this->clientOptions([
            'handler' => HandlerStack::create(new CurlHandler),
            'connect_timeout' => (int) config('push.connect_timeout'),
            'allow_redirects' => false,
            'proxy' => '',
            'curl' => [
                CURLOPT_RESOLVE => [$resolved['host'].':443:'.$address],
                CURLOPT_PROXY => '',
            ],
        ]));
        $subscription['contentEncoding'] = 'aes128gcm';
        $report = $sender->sendOneNotification(Subscription::create($subscription), $payload);
        $response = $report->getResponse();

        // Never return the provider's reason/body/request: they may contain subscription secrets.
        return [
            'accepted' => $report->isSuccess() && $response && $response->getStatusCode() >= 200 && $response->getStatusCode() < 300,
            'status' => $response?->getStatusCode(),
            'retry_after' => $response?->getHeaderLine('Retry-After') ?: null,
        ];
    }

    protected function clientOptions(array $options): array
    {
        return $options;
    }
}

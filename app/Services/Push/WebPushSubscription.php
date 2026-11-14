<?php

namespace App\Services\Push;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class WebPushSubscription
{
    public function __construct(private readonly WebPushEndpointPolicy $policy) {}

    public function normalize(array $subscription): string
    {
        $input = Validator::make($subscription, [
            'endpoint' => ['required', 'string', 'max:2048'],
            'expirationTime' => ['nullable', 'numeric', 'min:0'],
            'keys' => ['required', 'array:p256dh,auth'],
            'keys.p256dh' => ['required', 'string', 'size:87'],
            'keys.auth' => ['required', 'string', 'size:22'],
        ])->validate();
        try {
            $this->policy->resolve($input['endpoint']);
            $public = WebPushConfiguration::decode($input['keys']['p256dh'], 65);
            WebPushConfiguration::decode($input['keys']['auth'], 16);
            $der = hex2bin('3059301306072a8648ce3d020106082a8648ce3d030107034200').$public;
            $pem = "-----BEGIN PUBLIC KEY-----\n".chunk_split(base64_encode($der), 64, "\n")."-----END PUBLIC KEY-----\n";
            if ($public[0] !== "\x04" || ! @openssl_pkey_get_public($pem)) {
                throw new \InvalidArgumentException;
            }
        } catch (Throwable) {
            throw ValidationException::withMessages(['subscription' => 'Endpoint atau encryption keys Web Push tidak valid/diizinkan.']);
        }

        return json_encode([
            'endpoint' => $input['endpoint'],
            'expirationTime' => $input['expirationTime'] ?? null,
            'keys' => ['p256dh' => $input['keys']['p256dh'], 'auth' => $input['keys']['auth']],
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}

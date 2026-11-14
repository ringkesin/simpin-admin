<?php

namespace App\Services\Push;

use InvalidArgumentException;
use Throwable;

class WebPushConfiguration
{
    public static function decode(string $value, int $length): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        if (! preg_match('/^[A-Za-z0-9_-]+$/D', $value) || $decoded === false
            || strlen($decoded) !== $length || self::encode($decoded) !== $value) {
            throw new InvalidArgumentException('Encoding Web Push tidak valid.');
        }

        return $decoded;
    }

    public static function encode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    public function auth(): array
    {
        $public = (string) config('push.webpush.public_key');
        $private = (string) config('push.webpush.private_key');
        $subject = (string) config('push.webpush.subject');
        $publicBytes = self::decode($public, 65);
        $privateBytes = self::decode($private, 32);
        $der = hex2bin('30310201010420').$privateBytes.hex2bin('a00a06082a8648ce3d030107');
        $key = @openssl_pkey_get_private("-----BEGIN EC PRIVATE KEY-----\n".chunk_split(base64_encode($der), 64, "\n")."-----END EC PRIVATE KEY-----\n");
        $details = $key ? openssl_pkey_get_details($key) : false;
        $derived = $details && isset($details['ec']['x'], $details['ec']['y'])
            ? "\x04".str_pad($details['ec']['x'], 32, "\0", STR_PAD_LEFT).str_pad($details['ec']['y'], 32, "\0", STR_PAD_LEFT) : '';
        $validSubject = (str_starts_with($subject, 'mailto:') && filter_var(substr($subject, 7), FILTER_VALIDATE_EMAIL))
            || (filter_var($subject, FILTER_VALIDATE_URL) && parse_url($subject, PHP_URL_SCHEME) === 'https'
                && ! parse_url($subject, PHP_URL_USER) && ! parse_url($subject, PHP_URL_PASS));

        if (! hash_equals($publicBytes, $derived) || ! $validSubject) {
            throw new InvalidArgumentException('Konfigurasi VAPID tidak valid.');
        }

        return ['subject' => $subject, 'publicKey' => $public, 'privateKey' => $private];
    }

    public function enabled(): bool
    {
        if (! config('push.delivery_enabled') || ! config('push.webpush.enabled')) {
            return false;
        }
        try {
            $this->auth();

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}

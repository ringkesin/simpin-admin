<?php

namespace App\Services\Push;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Cache;
use JsonException;
use RuntimeException;

class FcmAccessTokenProvider
{
    /**
     * @throws JsonException
     */
    public function get(): string
    {
        $projectId = (string) config('push.fcm.project_id');
        $encodedCredentials = (string) config('push.fcm.service_account_base64');

        if ($projectId === '' || $encodedCredentials === '') {
            throw new RuntimeException('Konfigurasi FCM belum lengkap.');
        }

        $decodedCredentials = base64_decode($encodedCredentials, true);

        if ($decodedCredentials === false) {
            throw new RuntimeException('FCM_SERVICE_ACCOUNT_BASE64 tidak valid.');
        }

        $credentials = json_decode($decodedCredentials, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($credentials)) {
            throw new RuntimeException('Credential FCM harus berupa objek JSON.');
        }

        if (($credentials['type'] ?? null) !== 'service_account') {
            throw new RuntimeException('Credential FCM harus berupa service account.');
        }

        if (($credentials['project_id'] ?? null) !== $projectId) {
            throw new RuntimeException('Project service account FCM tidak sesuai dengan FCM_PROJECT_ID.');
        }

        $cacheKey = 'push:fcm-access-token:'.hash('sha256', $projectId);
        $cachedToken = Cache::get($cacheKey);

        if (is_string($cachedToken) && $cachedToken !== '') {
            return $cachedToken;
        }

        $serviceAccount = new ServiceAccountCredentials(
            (string) config('push.fcm.scope'),
            $credentials
        );
        $token = $serviceAccount->fetchAuthToken();
        $accessToken = $token['access_token'] ?? null;

        if (! is_string($accessToken) || $accessToken === '') {
            throw new RuntimeException('Google Auth tidak mengembalikan access token FCM.');
        }

        $expiresIn = max(60, ((int) ($token['expires_in'] ?? 3600)) - 300);
        Cache::put($cacheKey, $accessToken, now()->addSeconds($expiresIn));

        return $accessToken;
    }
}

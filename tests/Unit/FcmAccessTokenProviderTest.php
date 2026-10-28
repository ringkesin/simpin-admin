<?php

namespace Tests\Unit;

use App\Services\Push\FcmAccessTokenProvider;
use RuntimeException;
use Tests\TestCase;

class FcmAccessTokenProviderTest extends TestCase
{
    public function test_it_rejects_invalid_base64_credentials(): void
    {
        config([
            'push.fcm.project_id' => 'kkba-project',
            'push.fcm.service_account_base64' => 'not-valid-base64!',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('FCM_SERVICE_ACCOUNT_BASE64 tidak valid.');

        app(FcmAccessTokenProvider::class)->get();
    }

    public function test_it_rejects_credentials_from_a_different_project(): void
    {
        config([
            'push.fcm.project_id' => 'kkba-project',
            'push.fcm.service_account_base64' => base64_encode(json_encode([
                'type' => 'service_account',
                'project_id' => 'another-project',
            ])),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Project service account FCM tidak sesuai');

        app(FcmAccessTokenProvider::class)->get();
    }
}

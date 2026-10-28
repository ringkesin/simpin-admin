<?php

namespace Tests\Feature;

use App\Models\PushDevice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class DeviceLogoutTest extends TestCase
{
    use CreatesPushNotificationSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema(withAuthTables: true);
    }

    public function test_logout_disables_current_installation_and_only_revokes_current_token(): void
    {
        $user = User::create([
            'name' => 'Mobile User',
            'username' => 'mobile-user',
            'email' => 'mobile@example.com',
            'password' => 'secret-password',
            'mobile' => '08123456789',
        ]);
        DB::table('p_anggota')->insert([
            'p_anggota_id' => 12345,
            'user_id' => $user->id,
        ]);

        $installationId = '0192f416-5f3c-7d45-a8c2-5ecf465728c5';
        $device = PushDevice::create([
            'p_anggota_id' => 12345,
            'client_installation_id' => $installationId,
            'provider' => 'fcm',
            'platform' => 'web',
            'provider_identifier' => 'logout-token',
            'provider_identifier_hash' => hash('sha256', 'logout-token'),
            'is_active' => true,
            'last_seen_at' => now(),
        ]);

        $currentToken = $user->createToken('current-device', ['state:anggota']);
        $otherToken = $user->createToken('other-device', ['state:anggota']);

        $this->withToken($currentToken->plainTextToken)
            ->postJson('/api/logout', ['client_installation_id' => $installationId])
            ->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $currentToken->accessToken->id]);
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $otherToken->accessToken->id]);
        $this->assertFalse($device->fresh()->is_active);
        $this->assertNotNull($device->fresh()->disabled_at);
    }
}

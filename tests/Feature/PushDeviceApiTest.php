<?php

namespace Tests\Feature;

use App\Models\PushDevice;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class PushDeviceApiTest extends TestCase
{
    use CreatesPushNotificationSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema();
    }

    public function test_registration_requires_authentication(): void
    {
        $this->postJson('/api/push/devices', [])->assertUnauthorized();
    }

    public function test_member_can_register_and_refresh_an_expo_device(): void
    {
        $this->actingAsAnggota();
        $installationId = '0192f416-5f3c-7d45-a8c2-5ecf465728c1';
        $firstToken = 'ExponentPushToken[first-token]';

        $this->postJson('/api/push/devices', [
            'client_installation_id' => $installationId,
            'provider' => 'expo',
            'platform' => 'android',
            'provider_identifier' => $firstToken,
            'app_version' => '1.0.0',
        ])->assertCreated()
            ->assertJsonPath('data.device.client_installation_id', $installationId)
            ->assertJsonPath('data.device.is_active', true)
            ->assertJsonMissingPath('data.device.provider_identifier');

        $storedIdentifier = DB::table('t_push_devices')->value('provider_identifier');
        $this->assertNotSame($firstToken, $storedIdentifier);

        $secondToken = 'ExponentPushToken[rotated-token]';
        $this->postJson('/api/push/devices', [
            'client_installation_id' => $installationId,
            'provider' => 'expo',
            'platform' => 'android',
            'provider_identifier' => $secondToken,
            'app_version' => '1.1.0',
        ])->assertOk()
            ->assertJsonPath('data.device.app_version', '1.1.0');

        $this->assertDatabaseCount('t_push_devices', 1);
        $device = PushDevice::firstOrFail();
        $this->assertSame($secondToken, $device->provider_identifier);
        $this->assertSame(hash('sha256', $secondToken), $device->provider_identifier_hash);
    }

    public function test_registration_moves_an_installation_to_the_latest_member(): void
    {
        $installationId = '0192f416-5f3c-7d45-a8c2-5ecf465728c2';

        $this->actingAsAnggota(1001, 1);
        $this->postJson('/api/push/devices', [
            'client_installation_id' => $installationId,
            'provider' => 'fcm',
            'platform' => 'web',
            'provider_identifier' => 'first-fcm-token',
            'app_version' => '1.0.0',
        ])->assertCreated();

        $this->actingAsAnggota(1002, 2);
        $this->postJson('/api/push/devices', [
            'client_installation_id' => $installationId,
            'provider' => 'fcm',
            'platform' => 'web',
            'provider_identifier' => 'second-fcm-token',
            'app_version' => '1.0.0',
        ])->assertOk();

        $this->assertDatabaseCount('t_push_devices', 1);
        $this->assertDatabaseHas('t_push_devices', [
            'p_anggota_id' => 1002,
            'client_installation_id' => $installationId,
            'provider_identifier_hash' => hash('sha256', 'second-fcm-token'),
            'is_active' => true,
        ]);
    }

    public function test_registration_moves_a_provider_identifier_to_the_latest_member(): void
    {
        $sharedToken = 'ExponentPushToken[shared-token]';

        $this->actingAsAnggota(1001, 1);
        $this->postJson('/api/push/devices', [
            'client_installation_id' => '0192f416-5f3c-7d45-a8c2-5ecf465728d1',
            'provider' => 'expo',
            'platform' => 'android',
            'provider_identifier' => $sharedToken,
            'app_version' => '1.0.0',
        ])->assertCreated();

        $this->actingAsAnggota(1002, 2);
        $newInstallationId = '0192f416-5f3c-7d45-a8c2-5ecf465728d2';
        $this->postJson('/api/push/devices', [
            'client_installation_id' => $newInstallationId,
            'provider' => 'expo',
            'platform' => 'ios',
            'provider_identifier' => $sharedToken,
            'app_version' => '1.0.0',
        ])->assertOk();

        $this->assertDatabaseCount('t_push_devices', 1);
        $this->assertDatabaseHas('t_push_devices', [
            'p_anggota_id' => 1002,
            'client_installation_id' => $newInstallationId,
            'provider_identifier_hash' => hash('sha256', $sharedToken),
        ]);
    }

    public function test_provider_and_platform_pair_is_validated(): void
    {
        $this->actingAsAnggota();

        $this->postJson('/api/push/devices', [
            'client_installation_id' => '0192f416-5f3c-7d45-a8c2-5ecf465728c3',
            'provider' => 'expo',
            'platform' => 'web',
            'provider_identifier' => 'not-an-expo-token',
            'app_version' => '1.0.0',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['platform', 'provider_identifier']);
    }

    public function test_member_can_only_disable_their_own_installation(): void
    {
        $installationId = '0192f416-5f3c-7d45-a8c2-5ecf465728c4';
        $this->actingAsAnggota(1001, 1);
        $this->postJson('/api/push/devices', [
            'client_installation_id' => $installationId,
            'provider' => 'fcm',
            'platform' => 'web',
            'provider_identifier' => 'owner-token',
            'app_version' => '1.0.0',
        ])->assertCreated();

        $this->actingAsAnggota(1002, 2);
        $this->deleteJson('/api/push/devices/'.$installationId)->assertNotFound();

        $this->actingAsAnggota(1001, 1);
        $this->deleteJson('/api/push/devices/'.$installationId)->assertOk();

        $this->assertDatabaseHas('t_push_devices', [
            'client_installation_id' => $installationId,
            'is_active' => false,
        ]);
    }
}

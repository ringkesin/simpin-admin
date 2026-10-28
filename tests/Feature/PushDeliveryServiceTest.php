<?php

namespace Tests\Feature;

use App\Models\MemberNotification;
use App\Models\PushDevice;
use App\Services\Push\ExpoPushService;
use App\Services\Push\FcmAccessTokenProvider;
use App\Services\Push\PushDeliveryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class PushDeliveryServiceTest extends TestCase
{
    use CreatesPushNotificationSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema();
        $this->actingAsAnggota(12345, 1);

        config([
            'push.delivery_enabled' => true,
            'push.expo.access_token' => 'expo-access-token',
            'push.fcm.project_id' => 'kkba-project',
        ]);
    }

    public function test_expo_delivery_updates_statistics_and_disables_invalid_device(): void
    {
        $notification = $this->notification();
        $acceptedDevice = $this->device('expo', 'android', 'ExponentPushToken[accepted]');
        $invalidDevice = $this->device('expo', 'ios', 'ExponentPushToken[invalid]');

        Http::fake([
            config('push.expo.endpoint') => Http::response([
                'data' => [
                    ['status' => 'ok', 'id' => 'ticket-1'],
                    [
                        'status' => 'error',
                        'message' => 'Device is not registered.',
                        'details' => ['error' => 'DeviceNotRegistered'],
                    ],
                ],
            ]),
        ]);

        app(PushDeliveryService::class)->deliver($notification->id);

        $notification->refresh();
        $this->assertNotNull($notification->push_attempted_at);
        $this->assertSame(1, $notification->push_accepted_count);
        $this->assertSame(1, $notification->push_failed_count);
        $this->assertTrue($acceptedDevice->fresh()->is_active);
        $this->assertFalse($invalidDevice->fresh()->is_active);
        $this->assertNotNull($invalidDevice->fresh()->disabled_at);
    }

    public function test_fcm_delivery_uses_string_data_and_disables_unregistered_device(): void
    {
        $this->mock(FcmAccessTokenProvider::class, function (MockInterface $mock): void {
            $mock->shouldReceive('get')->once()->andReturn('google-access-token');
        });

        $notification = $this->notification(['paid' => true, 'attempt' => 2]);
        $acceptedDevice = $this->device('fcm', 'web', 'fcm-token-accepted');
        $invalidDevice = $this->device('fcm', 'web', 'fcm-token-invalid');

        Http::fakeSequence()
            ->push(['name' => 'projects/kkba-project/messages/1'], 200)
            ->push([
                'error' => [
                    'status' => 'NOT_FOUND',
                    'details' => [['errorCode' => 'UNREGISTERED']],
                ],
            ], 404);

        app(PushDeliveryService::class)->deliver($notification->id);

        $notification->refresh();
        $this->assertSame(1, $notification->push_accepted_count);
        $this->assertSame(1, $notification->push_failed_count);
        $this->assertTrue($acceptedDevice->fresh()->is_active);
        $this->assertFalse($invalidDevice->fresh()->is_active);

        Http::assertSent(function (HttpRequest $request): bool {
            $data = $request->data()['message']['data'];

            return $data['paid'] === 'true'
                && $data['attempt'] === '2'
                && $data['source'] === 'mart';
        });
    }

    public function test_expo_requests_are_chunked_at_one_hundred_messages(): void
    {
        $devices = new Collection;

        foreach (range(1, 101) as $index) {
            $device = new PushDevice([
                'provider_identifier' => "ExponentPushToken[token-{$index}]",
            ]);
            $device->id = (string) Str::ulid();
            $device->exists = true;
            $devices->add($device);
        }

        Http::fake(function (HttpRequest $request) {
            return Http::response([
                'data' => array_fill(0, count($request->data()), ['status' => 'ok', 'id' => 'ticket']),
            ]);
        });

        $result = app(ExpoPushService::class)->send(
            $devices,
            $this->notification(),
            ['source' => 'mart']
        );

        Http::assertSentCount(2);
        $this->assertSame(101, $result->acceptedCount());
        $this->assertSame(0, $result->failedCount());
    }

    public function test_delivery_disabled_leaves_inbox_unattempted(): void
    {
        config(['push.delivery_enabled' => false]);
        Http::fake();
        $notification = $this->notification();
        $this->device('expo', 'android', 'ExponentPushToken[disabled]');

        app(PushDeliveryService::class)->deliver($notification->id);

        $this->assertNull($notification->fresh()->push_attempted_at);
        Http::assertNothingSent();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function notification(array $data = ['target' => 'order_detail']): MemberNotification
    {
        return MemberNotification::create([
            'p_anggota_id' => 12345,
            'source' => 'mart',
            'event_id' => 'event-'.Str::ulid(),
            'type' => 'order_status',
            'title' => 'Pesanan diperbarui',
            'body' => 'Status pesanan Anda telah diperbarui.',
            'data' => $data,
            'occurred_at' => now(),
        ]);
    }

    private function device(string $provider, string $platform, string $identifier): PushDevice
    {
        return PushDevice::create([
            'p_anggota_id' => 12345,
            'client_installation_id' => (string) Str::uuid(),
            'provider' => $provider,
            'platform' => $platform,
            'provider_identifier' => $identifier,
            'provider_identifier_hash' => hash('sha256', $identifier),
            'is_active' => true,
            'last_seen_at' => now(),
        ]);
    }
}

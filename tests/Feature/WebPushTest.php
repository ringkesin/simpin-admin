<?php

namespace Tests\Feature;

use App\Models\MemberNotification;
use App\Models\PushDevice;
use App\Services\Push\ExpoPushService;
use App\Services\Push\PushDeliveryService;
use App\Services\Push\WebPushConfiguration;
use App\Services\Push\WebPushEndpointPolicy;
use App\Services\Push\WebPushService;
use App\Services\Push\WebPushTransport;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Minishlink\WebPush\VAPID;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class WebPushTest extends TestCase
{
    use CreatesPushNotificationSchema;

    private array $keys;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema();
        $this->actingAsAnggota(1233, 12);
        $this->keys = VAPID::createVapidKeys();
        config([
            'push.delivery_enabled' => true,
            'push.webpush.enabled' => true,
            'push.webpush.public_key' => $this->keys['publicKey'],
            'push.webpush.private_key' => $this->keys['privateKey'],
            'push.webpush.subject' => 'mailto:push@example.com',
        ]);
        $this->app->instance(WebPushEndpointPolicy::class, new class extends WebPushEndpointPolicy
        {
            protected function addresses(string $host): array
            {
                return ['17.57.146.10'];
            }
        });
        $this->app->bind(WebPushService::class, fn ($app) => new class($app->make(WebPushConfiguration::class), $app->make(\App\Services\Push\WebPushSubscription::class), $app->make(WebPushEndpointPolicy::class), $app->make(WebPushTransport::class)) extends WebPushService
        {
            protected function pause(int $seconds): void {}
        });
    }

    private function subscription(string $endpoint = 'https://web.push.apple.com/test-only'): array
    {
        return ['endpoint' => $endpoint, 'expirationTime' => null, 'keys' => [
            'p256dh' => $this->keys['publicKey'], 'auth' => WebPushConfiguration::encode(str_repeat('a', 16)),
        ]];
    }

    private function register(array $overrides = [])
    {
        return $this->postJson('/api/push/devices', array_replace([
            'client_installation_id' => '577c04cf-84c4-4d6b-8175-3ae2d8d383ae',
            'provider' => 'webpush', 'platform' => 'web', 'app_version' => '1.3.0',
            'subscription' => $this->subscription(),
        ], $overrides));
    }

    private function notification(array $data = []): MemberNotification
    {
        // Mart notifications currently identify the recipient by anggota, without user_id.
        return MemberNotification::create([
            'p_anggota_id' => 1233, 'source' => 'mart', 'event_id' => (string) Str::uuid(),
            'type' => 'order_status', 'title' => 'Pesanan dikonfirmasi', 'body' => 'Silakan lanjutkan pembayaran.',
            'data' => array_merge(['target' => 'order_detail', 'cart_mobile_id' => 'test-cart'], $data),
            'occurred_at' => now(),
        ]);
    }

    private function report(?int $status, ?string $retryAfter = null): array
    {
        return ['accepted' => $status !== null && $status >= 200 && $status < 300, 'status' => $status, 'retry_after' => $retryAfter];
    }

    public function test_config_requires_flags_and_matching_valid_vapid_pair_without_leaking_private_key(): void
    {
        $response = $this->getJson('/api/push/web/config')->assertOk()->assertJsonPath('data.enabled', true)
            ->assertJsonPath('data.vapid_public_key', $this->keys['publicKey'])->assertHeader('Cache-Control', 'no-store, private');
        $this->assertStringNotContainsString($this->keys['privateKey'], $response->getContent());
        foreach ([['push.delivery_enabled', false], ['push.webpush.enabled', false], ['push.webpush.subject', 'bad'],
            ['push.webpush.private_key', VAPID::createVapidKeys()['privateKey']]] as [$name, $value]) {
            $old = config($name);
            config([$name => $value]);
            $this->getJson('/api/push/web/config')->assertOk()->assertJsonPath('data.enabled', false)->assertJsonPath('data.vapid_public_key', null);
            config([$name => $old]);
        }
    }

    public function test_registration_encrypts_and_hides_subscription_and_keeps_identical_binding(): void
    {
        $response = $this->register()->assertCreated()->assertJsonPath('data.device.recipient_user_id', '12');
        $binding = $response->json('data.device.binding_id');
        $this->assertTrue(Str::isUuid($binding));
        $this->assertStringNotContainsString('test-only', $response->getContent());
        $stored = DB::table('t_push_devices')->first();
        $this->assertStringNotContainsString('test-only', $stored->provider_identifier);
        $this->assertSame(hash('sha256', 'webpush:https://web.push.apple.com/test-only'), $stored->provider_identifier_hash);
        $this->assertSame($this->subscription(), json_decode(PushDevice::first()->provider_identifier, true));
        $this->register()->assertOk()->assertJsonPath('data.device.binding_id', $binding);
        $this->assertSame(1, PushDevice::count());
    }

    public function test_key_rotation_reactivation_endpoint_rotation_and_account_reassignment_rotate_binding(): void
    {
        $this->register()->assertCreated();
        $id = PushDevice::first()->id;
        $binding = PushDevice::first()->binding_id;
        $sub = $this->subscription();
        $sub['keys']['auth'] = WebPushConfiguration::encode(str_repeat('b', 16));
        $this->register(['subscription' => $sub])->assertOk();
        $this->assertNotSame($binding, PushDevice::first()->binding_id);
        $binding = PushDevice::first()->binding_id;
        $this->deleteJson('/api/push/devices/577c04cf-84c4-4d6b-8175-3ae2d8d383ae')->assertOk();
        $this->deleteJson('/api/push/devices/577c04cf-84c4-4d6b-8175-3ae2d8d383ae')->assertNotFound();
        $this->register(['subscription' => $sub])->assertOk();
        $this->assertNotSame($binding, PushDevice::first()->binding_id);
        $this->register(['subscription' => $this->subscription('https://web.push.apple.com/rotated')])->assertOk();
        $this->assertSame($id, PushDevice::first()->id);
        $binding = PushDevice::first()->binding_id;
        $this->actingAsAnggota(987, 99);
        $this->register(['subscription' => $this->subscription('https://web.push.apple.com/rotated')])->assertOk();
        $this->assertSame(99, PushDevice::first()->user_id);
        $this->assertNotSame($binding, PushDevice::first()->binding_id);
        $this->assertSame(1, PushDevice::count());
        $this->actingAsAnggota(1233, 12);
        $this->deleteJson('/api/push/devices/577c04cf-84c4-4d6b-8175-3ae2d8d383ae')->assertNotFound();
    }

    public function test_registration_auth_platform_structure_and_keys_are_enforced(): void
    {
        $this->register(['platform' => 'ios'])->assertUnprocessable();
        $this->register(['subscription' => null])->assertUnprocessable();
        $sub = $this->subscription();
        $sub['keys']['auth'] = 'bad';
        $this->register(['subscription' => $sub])->assertUnprocessable();
        $sub = $this->subscription();
        $sub['keys']['p256dh'] = WebPushConfiguration::encode(str_repeat('x', 65));
        $this->register(['subscription' => $sub])->assertUnprocessable();
        $this->actingAsAdmin(99);
        $this->register()->assertForbidden();
        $this->assertSame(0, PushDevice::count());
        config(['push.webpush.enabled' => false]);
        $this->actingAsAnggota(1233, 12);
        $this->register()->assertStatus(503);
    }

    public function test_subscription_endpoint_rejects_ssrf_and_host_bypasses(): void
    {
        foreach (['http://web.push.apple.com/a', 'https://localhost/a', 'https://127.0.0.1/a',
            'https://push.apple.com.evil.test/a', 'https://evilpush.apple.com/a', 'https://web.push.apple.com:444/a',
            'https://user@web.push.apple.com/a', 'https://web.push.apple.com/a#fragment',
            'https://web.push.apple.com./a', 'https://web.push.apple.com%2eevil.test/a'] as $url) {
            $this->register(['subscription' => $this->subscription($url)])->assertUnprocessable();
        }
        $this->assertSame(0, PushDevice::count());
    }

    public function test_dns_rejects_any_private_reserved_or_mapped_address(): void
    {
        foreach ([[], ['127.0.0.1'], ['10.0.0.1'], ['169.254.169.254'], ['224.0.0.1'], ['::1'], ['fc00::1'],
            ['::ffff:127.0.0.1'], ['17.57.146.10', '192.168.1.1']] as $addresses) {
            $policy = new class($addresses) extends WebPushEndpointPolicy
            {
                public function __construct(private array $values) {}

                protected function addresses(string $host): array
                {
                    return $this->values;
                }
            };
            try {
                $policy->resolve('https://web.push.apple.com/a');
                $this->fail('Unsafe DNS accepted');
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_mart_envelope_contains_server_recipient_binding_and_delivery_is_claimed_once(): void
    {
        $this->register()->assertCreated();
        $device = PushDevice::first();
        $notification = $this->notification(['recipient_user_id' => 'attacker', 'binding_id' => 'attacker']);
        $this->mock(WebPushTransport::class)->shouldReceive('send')->once()->withArgs(function ($subscription, $payload, $auth, $resolved) use ($device, $notification) {
            $data = json_decode($payload, true)['data'];
            $this->assertSame('12', $data['recipient_user_id']);
            $this->assertSame($device->binding_id, $data['binding_id']);
            $this->assertSame($notification->id, $data['notification_id']);
            $this->assertSame('mart', $data['source']);
            $this->assertSame('test-cart', $data['cart_mobile_id']);
            $this->assertSame('17.57.146.10', $resolved['address']);

            return true;
        })->andReturn($this->report(201));
        app(PushDeliveryService::class)->deliver($notification->id);
        app(PushDeliveryService::class)->deliver($notification->id);
        $this->assertSame(1, $notification->fresh()->push_accepted_count);
        $this->assertSame(0, $notification->fresh()->push_failed_count);
    }

    public function test_transient_failures_retry_once_but_permanent_and_long_retry_after_do_not(): void
    {
        $this->register()->assertCreated();
        foreach ([[null, null, 2], [429, '1', 2], [503, null, 2], [429, '60', 1], [403, null, 1], [401, null, 1]] as [$status, $after, $attempts]) {
            $transport = \Mockery::mock(WebPushTransport::class);
            $transport->shouldReceive('send')->times($attempts)->andReturn($this->report($status, $after));
            $this->app->instance(WebPushTransport::class, $transport);
            // Rebuild service to use this transport and avoid sleeps.
            $service = new class(app(WebPushConfiguration::class), app(\App\Services\Push\WebPushSubscription::class), app(WebPushEndpointPolicy::class), $transport) extends WebPushService
            {
                protected function pause(int $seconds): void {}
            };
            $result = $service->send(PushDevice::all(), $this->notification(), []);
            $this->assertSame(1, $result->failedCount());
            $this->assertSame([], $result->invalidDeviceIds);
        }
    }

    public function test_retry_acceptance(): void
    {
        $this->register()->assertCreated();
        $this->mock(WebPushTransport::class)->shouldReceive('send')->twice()->andReturn($this->report(503), $this->report(201));
        $notification = $this->notification();
        app(PushDeliveryService::class)->deliver($notification->id);
        $this->assertSame(1, $notification->fresh()->push_accepted_count);
        $this->assertSame(0, $notification->fresh()->push_failed_count);
    }

    public function test_binding_change_between_attempts_stops_retry_and_preserves_new_device_state(): void
    {
        $this->register()->assertCreated();
        $notification = $this->notification();
        $this->mock(WebPushTransport::class)->shouldReceive('send')->once()->andReturnUsing(function () use ($notification) {
            // Simulate both a concurrent callback and a subscription reassignment/rotation.
            app(PushDeliveryService::class)->deliver($notification->id);
            PushDevice::query()->update(['binding_id' => (string) Str::uuid(), 'last_error' => 'new-binding-error']);

            return $this->report(503);
        });
        app(PushDeliveryService::class)->deliver($notification->id);
        $this->assertSame(1, $notification->fresh()->push_failed_count);
        $this->assertSame('new-binding-error', PushDevice::first()->last_error);
        $this->assertTrue(PushDevice::first()->is_active);
    }

    public function test_exception_in_expo_does_not_skip_webpush_and_does_not_leak_provider_message(): void
    {
        $this->register()->assertCreated();
        PushDevice::create([
            'user_id' => 12, 'p_anggota_id' => 1233, 'client_installation_id' => (string) Str::uuid(),
            'provider' => 'expo', 'platform' => 'ios', 'provider_identifier' => 'ExpoPushToken[isolated]',
            'provider_identifier_hash' => hash('sha256', 'ExpoPushToken[isolated]'), 'is_active' => true,
        ]);
        $this->mock(ExpoPushService::class)->shouldReceive('send')->once()->andThrow(new \RuntimeException('secret-endpoint-and-token'));
        $this->mock(WebPushTransport::class)->shouldReceive('send')->once()->andReturn($this->report(201));
        $notification = $this->notification();
        app(PushDeliveryService::class)->deliver($notification->id);
        $this->assertSame(1, $notification->fresh()->push_accepted_count);
        $this->assertSame(1, $notification->fresh()->push_failed_count);
        $this->assertSame('Push provider gagal mengirim.', PushDevice::where('provider', 'expo')->first()->last_error);
    }

    public function test_real_transport_classifies_timeout_and_redirect_without_exposing_response_body(): void
    {
        $transport = new class extends WebPushTransport
        {
            public array $responses;

            protected function clientOptions(array $options): array
            {
                $options['handler'] = HandlerStack::create(new MockHandler($this->responses));

                return $options;
            }
        };
        foreach ([new \GuzzleHttp\Exception\ConnectException('sensitive endpoint', new \GuzzleHttp\Psr7\Request('POST', 'https://web.push.apple.com/test-only')),
            new Response(302, ['Location' => 'http://127.0.0.1/'], 'secret response'),
            new Response(429, ['Retry-After' => '60'], 'secret response')] as $response) {
            $transport->responses = [$response];
            $report = $transport->send($this->subscription(), '{}', app(WebPushConfiguration::class)->auth(), ['host' => 'web.push.apple.com', 'address' => '17.57.146.10']);
            $this->assertFalse($report['accepted']);
            $this->assertSame(['accepted', 'status', 'retry_after'], array_keys($report));
            $this->assertStringNotContainsString('secret', json_encode($report));
        }
    }

    public function test_multiple_installations_receive_independently(): void
    {
        $this->register()->assertCreated();
        $this->register(['client_installation_id' => (string) Str::uuid(), 'subscription' => $this->subscription('https://web.push.apple.com/second')])->assertCreated();
        $this->mock(WebPushTransport::class)->shouldReceive('send')->twice()->andReturn($this->report(201));
        $notification = $this->notification();
        app(PushDeliveryService::class)->deliver($notification->id);
        $this->assertSame(2, $notification->fresh()->push_accepted_count);
        $this->assertSame(2, PushDevice::where('is_active', true)->count());
    }

    public function test_token_expiry_preserves_subscription_and_explicit_logout_only_disables_current_installation(): void
    {
        $this->register()->assertCreated();
        $this->register(['client_installation_id' => (string) Str::uuid(), 'subscription' => $this->subscription('https://web.push.apple.com/other')])->assertCreated();
        (require database_path('migrations/2025_02_08_042525_create_personal_access_tokens_table.php'))->up();
        $user = \App\Models\User::findOrFail(12);
        $expired = $user->createToken('expired', ['state:anggota'], now()->subMinute());
        $this->app['auth']->forgetGuards();
        $this->withToken($expired->plainTextToken)->getJson('/api/notifications')->assertUnauthorized();
        $this->assertSame(2, PushDevice::where('is_active', true)->count());
        $this->withToken('')->postJson('/api/push/devices', [])->assertUnauthorized();
        $token = $user->createToken('current', ['state:anggota']);
        $this->app['auth']->forgetGuards();
        $this->withToken($token->plainTextToken)->postJson('/api/logout', [
            'client_installation_id' => '577c04cf-84c4-4d6b-8175-3ae2d8d383ae',
        ])->assertOk();
        $this->assertSame(1, PushDevice::where('is_active', true)->count());
    }

    public function test_preflight_allows_pwa_origin_and_authorization_header(): void
    {
        $this->options('/api/push/devices', [], [
            'Origin' => 'https://koperasi-abipraya.com',
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'Authorization,Content-Type,Accept',
        ])->assertSuccessful()->assertHeader('Access-Control-Allow-Origin', 'https://koperasi-abipraya.com');
    }

    public function test_invalid_subscriptions_are_disabled_but_auth_failures_are_not(): void
    {
        $this->register()->assertCreated();
        foreach ([401, 403, 404, 410] as $status) {
            $this->register()->assertOk();
            $this->mock(WebPushTransport::class)->shouldReceive('send')->once()->andReturn($this->report($status));
            $this->app->forgetInstance(WebPushService::class);
            $notification = $this->notification();
            app(PushDeliveryService::class)->deliver($notification->id);
            $this->assertSame(! in_array($status, [404, 410]), PushDevice::first()->is_active);
            $this->assertSame(1, $notification->fresh()->push_failed_count);
            $this->app->forgetInstance(PushDeliveryService::class);
        }
    }

    public function test_real_library_encrypts_payload_signs_vapid_and_transport_pins_dns_and_disables_redirects(): void
    {
        $transport = new class extends WebPushTransport
        {
            public array $options;

            public array $requests = [];

            protected function clientOptions(array $options): array
            {
                $this->options = $options;
                $handler = new MockHandler([function ($request) {
                    $this->requests[] = $request;

                    return new Response(201);
                }]);
                $options['handler'] = HandlerStack::create($handler);

                return $options;
            }
        };
        $report = $transport->send($this->subscription(), '{"title":"test"}', app(WebPushConfiguration::class)->auth(), [
            'host' => 'web.push.apple.com', 'address' => '17.57.146.10',
        ]);
        $this->assertTrue($report['accepted']);
        $this->assertFalse($transport->options['allow_redirects']);
        $this->assertSame('', $transport->options['proxy']);
        $this->assertSame(['web.push.apple.com:443:17.57.146.10'], $transport->options['curl'][CURLOPT_RESOLVE]);
        $this->assertSame(2, $transport->options['connect_timeout']);
        $this->assertStringStartsWith('vapid t=', $transport->requests[0]->getHeaderLine('Authorization'));
        $this->assertSame('aes128gcm', $transport->requests[0]->getHeaderLine('Content-Encoding'));
        $this->assertStringNotContainsString('test', (string) $transport->requests[0]->getBody());
    }

    public function test_webpush_size_limit_and_failure_do_not_block_expo(): void
    {
        $this->register()->assertCreated();
        PushDevice::create([
            'user_id' => 12, 'p_anggota_id' => 1233, 'client_installation_id' => (string) Str::uuid(),
            'provider' => 'expo', 'platform' => 'ios', 'provider_identifier' => 'ExpoPushToken[test]',
            'provider_identifier_hash' => hash('sha256', 'ExpoPushToken[test]'), 'is_active' => true,
        ]);
        config(['push.expo.access_token' => 'test']);
        Http::fake(['*' => Http::response(['data' => [['status' => 'ok']]])]);
        $this->mock(WebPushTransport::class)->shouldNotReceive('send');
        $notification = $this->notification(['large' => str_repeat('x', 3000)]);
        app(PushDeliveryService::class)->deliver($notification->id);
        $this->assertSame(1, $notification->fresh()->push_accepted_count);
        $this->assertSame(1, $notification->fresh()->push_failed_count);
        $this->assertStringContainsString('3000', PushDevice::where('provider', 'webpush')->first()->last_error);
    }

    public function test_webpush_disabled_and_main_switch_disabled_skip_transport(): void
    {
        $this->register()->assertCreated();
        $this->mock(WebPushTransport::class)->shouldNotReceive('send');
        config(['push.webpush.enabled' => false]);
        $notification = $this->notification();
        app(PushDeliveryService::class)->deliver($notification->id);
        $this->assertSame(0, $notification->fresh()->push_failed_count);
        config(['push.delivery_enabled' => false]);
        $notification = $this->notification();
        app(PushDeliveryService::class)->deliver($notification->id);
        $this->assertNull($notification->fresh()->push_attempted_at);
    }

    public function test_binding_migration_can_rollback(): void
    {
        $this->assertTrue(Schema::hasColumn('t_push_devices', 'binding_id'));
        (require database_path('migrations/2026_11_15_000000_add_binding_id_to_push_devices_table.php'))->down();
        $this->assertFalse(Schema::hasColumn('t_push_devices', 'binding_id'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\MemberNotification;
use App\Models\PushDevice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class InternalNotificationApiTest extends TestCase
{
    use CreatesPushNotificationSchema;

    private const SECRET = 'test-notification-secret';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema();

        config([
            'push.delivery_enabled' => false,
            'push.internal.mart_client_id' => 'mart',
            'push.internal.mart_secret' => self::SECRET,
            'push.internal.allowed_skew_seconds' => 300,
        ]);

        $this->actingAsAnggota(12345, 1);
    }

    public function test_valid_signed_event_creates_inbox_and_duplicate_is_idempotent(): void
    {
        $payload = $this->payload();

        $this->signedRequest($payload)
            ->assertCreated()
            ->assertJsonPath('data.duplicate', false);

        $this->signedRequest($payload)
            ->assertOk()
            ->assertJsonPath('data.duplicate', true);

        $this->assertDatabaseCount('t_notifications', 1);
        $notification = MemberNotification::firstOrFail();
        $this->assertNull($notification->push_attempted_at);
        $this->assertSame('mart:cart:01KTEST:confirmed', $notification->event_id);
    }

    public function test_invalid_signature_and_stale_timestamp_are_rejected(): void
    {
        $payload = $this->payload();
        $this->signedRequest($payload, signature: str_repeat('0', 64))->assertUnauthorized();
        $this->signedRequest($payload, timestamp: now()->subMinutes(6)->timestamp)->assertUnauthorized();
        $this->assertDatabaseCount('t_notifications', 0);
    }

    public function test_signature_must_use_lowercase_hex(): void
    {
        $payload = $this->payload();
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $timestamp = now()->timestamp;
        $signature = hash_hmac(
            'sha256',
            $timestamp."\n".$payload['event_id']."\n".$body,
            self::SECRET
        );

        $this->signedRequest($payload, timestamp: $timestamp, signature: strtoupper($signature))
            ->assertUnauthorized();
        $this->assertDatabaseCount('t_notifications', 0);
    }

    public function test_event_header_must_match_payload(): void
    {
        $this->signedRequest($this->payload(), headerEventId: 'different-event')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('event_id');
    }

    public function test_missing_member_and_oversized_provider_payload_are_rejected(): void
    {
        $missingMember = $this->payload();
        $missingMember['recipient_id'] = 99999;
        $this->signedRequest($missingMember)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('recipient_id');

        $oversized = $this->payload();
        $oversized['data']['content'] = str_repeat('x', 5000);
        $this->signedRequest($oversized)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('data');
    }

    public function test_raw_request_body_over_32_kib_is_rejected(): void
    {
        $payload = $this->payload();
        $payload['data']['content'] = str_repeat('x', 33 * 1024);

        $this->signedRequest($payload)
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Payload notifikasi terlalu besar.');
    }

    public function test_deferred_provider_failure_does_not_change_business_response(): void
    {
        config([
            'push.delivery_enabled' => true,
            'push.expo.access_token' => 'expo-access-token',
        ]);
        PushDevice::create([
            'p_anggota_id' => 12345,
            'client_installation_id' => (string) Str::uuid(),
            'provider' => 'expo',
            'platform' => 'android',
            'provider_identifier' => 'ExponentPushToken[provider-failure]',
            'provider_identifier_hash' => hash('sha256', 'ExponentPushToken[provider-failure]'),
            'app_version' => '1.0.0',
            'is_active' => true,
            'last_seen_at' => now(),
        ]);
        Http::fake([
            config('push.expo.endpoint') => Http::response(['errors' => [['message' => 'Unavailable']]], 503),
        ]);

        $this->signedRequest($this->payload())->assertCreated();

        $notification = MemberNotification::firstOrFail();
        $this->assertNotNull($notification->push_attempted_at);
        $this->assertSame(0, $notification->push_accepted_count);
        $this->assertSame(1, $notification->push_failed_count);
    }

    public function test_duplicate_event_does_not_send_push_twice(): void
    {
        config([
            'push.delivery_enabled' => true,
            'push.expo.access_token' => 'expo-access-token',
        ]);
        PushDevice::create([
            'p_anggota_id' => 12345,
            'client_installation_id' => (string) Str::uuid(),
            'provider' => 'expo',
            'platform' => 'android',
            'provider_identifier' => 'ExponentPushToken[idempotent]',
            'provider_identifier_hash' => hash('sha256', 'ExponentPushToken[idempotent]'),
            'app_version' => '1.0.0',
            'is_active' => true,
            'last_seen_at' => now(),
        ]);
        Http::fake([
            config('push.expo.endpoint') => Http::response([
                'data' => [['status' => 'ok', 'id' => 'ticket-1']],
            ]),
        ]);

        $payload = $this->payload();
        $this->signedRequest($payload)->assertCreated();
        $this->signedRequest($payload)->assertOk()->assertJsonPath('data.duplicate', true);

        Http::assertSentCount(1);
        $this->assertDatabaseCount('t_notifications', 1);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'event_id' => 'mart:cart:01KTEST:confirmed',
            'recipient_id' => 12345,
            'type' => 'order_status',
            'title' => 'Pesanan dikonfirmasi',
            'body' => 'Pesanan Anda telah dikonfirmasi.',
            'data' => [
                'source' => 'mart',
                'target' => 'order_detail',
                'cart_mobile_id' => '01KTEST',
                'status' => 'confirmed',
            ],
            'occurred_at' => '2026-10-24T10:00:00+07:00',
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function signedRequest(
        array $payload,
        ?int $timestamp = null,
        ?string $headerEventId = null,
        ?string $signature = null,
    ): TestResponse {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $timestamp ??= now()->timestamp;
        $headerEventId ??= $payload['event_id'];
        $signature ??= hash_hmac(
            'sha256',
            $timestamp."\n".$headerEventId."\n".$body,
            self::SECRET
        );

        return $this->call(
            'POST',
            '/api/internal/notifications',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X_KKBA_SERVICE' => 'mart',
                'HTTP_X_KKBA_TIMESTAMP' => (string) $timestamp,
                'HTTP_X_KKBA_EVENT_ID' => $headerEventId,
                'HTTP_X_KKBA_SIGNATURE' => $signature,
            ],
            $body
        );
    }
}

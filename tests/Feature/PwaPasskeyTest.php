<?php

namespace Tests\Feature;

use App\Models\PwaPasskey;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use lbuchs\WebAuthn\Binary\ByteBuffer;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class PwaPasskeyTest extends TestCase
{
    use CreatesPushNotificationSchema;

    private const TEST_PRIVATE_KEY = <<<'PEM'
-----BEGIN EC PRIVATE KEY-----
MHcCAQEEIKuuBuTNwfmQl7ZCMhgFn/zAov8sVcVBeEVBusAov8kjoAoGCCqGSM49
AwEHoUQDQgAENnq7EcJun2WK/j7Mj1/x/BWljtFlhmC/sbeMXSnVnoOO8KaXUve4
sErAm9P4UX182PMauuDKAzfjkEpQPGUVnw==
-----END EC PRIVATE KEY-----
PEM;

    private string $origin = 'https://koperasi-abipraya.com';

    private User $member;

    private string $bearer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema(withAuthTables: true);
        (require database_path('migrations/2026_11_11_000000_create_pwa_passkeys_tables.php'))->up();
        Schema::create('apps', function (Blueprint $t) {
            $t->increments('id');
            $t->string('code');
            $t->softDeletes();
        });
        Schema::create('rbac_role', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('apps_id');
            $t->string('code');
            $t->date('valid_from')->nullable();
            $t->date('valid_until')->nullable();
            $t->softDeletes();
        });
        Schema::create('rbac_role_users', function (Blueprint $t) {
            $t->ulid('id')->primary();
            $t->unsignedBigInteger('user_id');
            $t->unsignedInteger('role_id');
            $t->date('valid_from')->nullable();
            $t->date('valid_until')->nullable();
        });
        DB::table('apps')->insert(['id' => 1, 'code' => 'mobile']);
        DB::table('rbac_role')->insert(['id' => 1, 'apps_id' => 1, 'code' => 'mobile_anggota']);
        $this->member = User::create(['name' => 'Anggota Passkey', 'username' => '12345', 'email' => 'test@example.test', 'password' => 'known-password', 'mobile' => '0800000000']);
        DB::table('rbac_role_users')->insert(['id' => (string) Str::ulid(), 'user_id' => $this->member->id, 'role_id' => 1]);
        DB::table('p_anggota')->insert(['p_anggota_id' => 1233, 'user_id' => $this->member->id, 'is_registered' => true]);
        $this->bearer = $this->member->createToken('test', ['state:anggota'])->plainTextToken;
        config(['passkeys.enabled' => true, 'passkeys.rp_id' => 'koperasi-abipraya.com', 'passkeys.origins' => [$this->origin]]);
        $this->withHeaders(['Origin' => $this->origin]);
    }

    private function registrationOptions(): array
    {
        return $this->withToken($this->bearer)->postJson('/api/auth/passkeys/register/options', ['current_password' => 'known-password', 'name' => 'iPhone saya'])
            ->assertOk()->assertJsonPath('data.publicKey.authenticatorSelection.userVerification', 'required')
            ->assertJsonPath('data.publicKey.authenticatorSelection.residentKey', 'required')->json('data');
    }

    private function b64(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    private function cbor(mixed $value): string
    {
        $header = function (int $major, int $length) {
            return $length < 24 ? chr(($major << 5) | $length) : ($length < 256 ? chr(($major << 5) | 24).chr($length) : chr(($major << 5) | 25).pack('n', $length));
        };
        if ($value instanceof ByteBuffer) {
            $bytes = $value->getBinaryString();

            return $header(2, strlen($bytes)).$bytes;
        }
        if (is_int($value)) {
            return $header($value < 0 ? 1 : 0, $value < 0 ? -1 - $value : $value);
        }
        if (is_string($value)) {
            return $header(3, strlen($value)).$value;
        }
        $out = $header(5, count($value));
        foreach ($value as $key => $entry) {
            $out .= $this->cbor($key).$this->cbor($entry);
        }

        return $out;
    }

    private function registration(array $options, string $origin = 'https://koperasi-abipraya.com', int $flags = 0x45): array
    {
        $private = openssl_pkey_get_private(self::TEST_PRIVATE_KEY);
        $ec = openssl_pkey_get_details($private)['ec'];
        $id = random_bytes(32);
        $cose = $this->cbor([1 => 2, 3 => -7, -1 => 1, -2 => new ByteBuffer($ec['x']), -3 => new ByteBuffer($ec['y'])]);
        $data = hash('sha256', 'koperasi-abipraya.com', true).chr($flags).pack('N', 0).str_repeat("\0", 16).pack('n', strlen($id)).$id.$cose;

        return ['private' => $private, 'handle' => $options['publicKey']['user']['id'], 'body' => [
            'challenge_id' => $options['challenge_id'], 'credential' => [
                'id' => $this->b64($id), 'rawId' => $this->b64($id), 'type' => 'public-key', 'response' => [
                    'clientDataJSON' => $this->b64(json_encode(['type' => 'webauthn.create', 'challenge' => $options['publicKey']['challenge'], 'origin' => $origin, 'crossOrigin' => false])),
                    'attestationObject' => $this->b64($this->cbor(['fmt' => 'none', 'attStmt' => [], 'authData' => new ByteBuffer($data)])),
                ],
            ],
        ]];
    }

    private function registered(): array
    {
        $registration = $this->registration($this->registrationOptions());
        $this->withToken($this->bearer)->postJson('/api/auth/passkeys/register/verify', $registration['body'])->assertOk();

        return $registration;
    }

    private function assertion(array $registration, array $options, string $origin = 'https://koperasi-abipraya.com', int $flags = 0x05, int $counter = 1, string $rpId = 'koperasi-abipraya.com', bool $crossOrigin = false): array
    {
        $client = json_encode(['type' => 'webauthn.get', 'challenge' => $options['publicKey']['challenge'], 'origin' => $origin, 'crossOrigin' => $crossOrigin]);
        $data = hash('sha256', $rpId, true).chr($flags).pack('N', $counter);
        openssl_sign($data.hash('sha256', $client, true), $signature, $registration['private'], OPENSSL_ALGO_SHA256);
        $id = $registration['body']['credential']['id'];

        return ['challenge_id' => $options['challenge_id'], 'credential' => ['type' => 'public-key', 'id' => $id, 'rawId' => $id, 'response' => [
            'clientDataJSON' => $this->b64($client), 'authenticatorData' => $this->b64($data), 'signature' => $this->b64($signature), 'userHandle' => $registration['handle'],
        ]]];
    }

    private function loginOptions(): array
    {
        return $this->postJson('/api/auth/passkeys/login/options')->assertOk()->assertJsonPath('data.publicKey.userVerification', 'required')->json('data');
    }

    public function test_real_registration_and_signed_login_issue_member_session_and_finite_token(): void
    {
        $registration = $this->registered();
        $payload = $this->assertion($registration, $this->loginOptions());
        $response = $this->postJson('/api/auth/passkeys/login/verify', $payload)->assertOk()
            ->assertJsonPath('data.role', 'mobile_anggota')->assertJsonPath('data.anggota.p_anggota_id', 1233);
        $this->assertNotEmpty($response->json('data.token'));
        $this->assertSame(1, PwaPasskey::first()->sign_count);
        $token = $this->member->tokens()->where('name', 'like', 'pwa-passkey:%')->first();
        $this->assertSame(['state:anggota'], $token->abilities);
        $this->assertNotNull($token->expires_at);
        $this->assertTrue($token->expires_at->isFuture());
        $this->postJson('/api/auth/passkeys/login/verify', $payload)->assertStatus(422);
    }

    public function test_registration_requires_password_and_member_bearer(): void
    {
        $this->postJson('/api/auth/passkeys/register/options', ['current_password' => 'known-password', 'name' => 'iPhone'])->assertUnauthorized();
        $this->withToken($this->bearer)->postJson('/api/auth/passkeys/register/options', ['current_password' => 'wrong-password', 'name' => 'iPhone'])
            ->assertStatus(422)->assertHeader('Cache-Control', 'no-store, private');
        $adminToken = $this->member->createToken('admin', ['state:admin'])->plainTextToken;
        $this->app['auth']->forgetGuards();
        $this->withToken($adminToken)->postJson('/api/auth/passkeys/register/options', ['current_password' => 'known-password', 'name' => 'iPhone'])->assertForbidden();
        $this->assertSame(0, DB::table('t_pwa_passkey_challenges')->count());
    }

    public function test_a_member_cannot_register_more_than_ten_passkeys(): void
    {
        foreach (range(1, 10) as $number) {
            $credentialId = $this->b64(hash('sha256', 'credential-'.$number, true));
            PwaPasskey::create([
                'user_id' => $this->member->id,
                'name' => 'Passkey '.$number,
                'credential_id' => $credentialId,
                'credential_id_hash' => hash('sha256', $credentialId),
                'user_handle' => $this->b64(random_bytes(32)),
                'public_key' => 'synthetic-test-key',
                'sign_count' => 0,
            ]);
        }

        $this->withToken($this->bearer)->postJson('/api/auth/passkeys/register/options', [
            'current_password' => 'known-password',
            'name' => 'Passkey ke-11',
        ])->assertStatus(422);
        $this->assertSame(0, DB::table('t_pwa_passkey_challenges')->count());
    }

    public function test_wrong_origin_missing_verification_and_replayed_challenges_are_rejected(): void
    {
        foreach ([['https://evil.example', 0x45], [$this->origin, 0x41]] as [$origin, $flags]) {
            $registration = $this->registration($this->registrationOptions(), $origin, $flags);
            $this->withToken($this->bearer)->postJson('/api/auth/passkeys/register/verify', $registration['body'])->assertStatus(422);
            $this->assertDatabaseMissing('t_pwa_passkey_challenges', ['id' => $registration['body']['challenge_id']]);
        }
        $this->withHeaders(['Origin' => 'https://evil.example'])->postJson('/api/auth/passkeys/login/options')->assertForbidden();
        $this->assertSame(0, PwaPasskey::count());
    }

    public function test_expired_challenge_and_registration_token_switch_are_rejected(): void
    {
        $options = $this->registrationOptions();
        DB::table('t_pwa_passkey_challenges')->where('id', $options['challenge_id'])->update(['expires_at' => now()->subSecond()]);
        $this->withToken($this->bearer)->postJson('/api/auth/passkeys/register/verify', $this->registration($options)['body'])->assertStatus(422);
        $registration = $this->registration($this->registrationOptions());
        $different = $this->member->createToken('different', ['state:anggota'])->plainTextToken;
        $this->app['auth']->forgetGuards();
        $this->withToken($different)->postJson('/api/auth/passkeys/register/verify', $registration['body'])->assertStatus(422);
        $this->assertSame(0, PwaPasskey::count());
    }

    public function test_invalid_signature_user_handle_and_missing_user_verification_never_issue_tokens(): void
    {
        $registration = $this->registered();
        foreach (['signature', 'userHandle', 'uv', 'origin', 'challenge', 'rp', 'crossOrigin'] as $mode) {
            $payload = $this->assertion($registration, $this->loginOptions(), $mode === 'origin' ? 'https://evil.example' : $this->origin, $mode === 'uv' ? 0x01 : 0x05,
                rpId: $mode === 'rp' ? 'evil.example' : 'koperasi-abipraya.com', crossOrigin: $mode === 'crossOrigin');
            if (in_array($mode, ['signature', 'userHandle'])) {
                $payload['credential']['response'][$mode] = $this->b64(random_bytes(32));
            }
            if ($mode === 'challenge') {
                $client = json_decode(base64_decode(strtr($payload['credential']['response']['clientDataJSON'], '-_', '+/')), true);
                $client['challenge'] = $this->b64(random_bytes(32));
                $payload['credential']['response']['clientDataJSON'] = $this->b64(json_encode($client));
            }
            $this->postJson('/api/auth/passkeys/login/verify', $payload)->assertStatus(422);
        }
        $this->assertSame(0, $this->member->tokens()->where('name', 'like', 'pwa-passkey:%')->count());
    }

    public function test_suspended_or_revoked_member_roles_cannot_login_and_password_flow_still_works(): void
    {
        $this->postJson('/api/login', ['username' => '12345', 'password' => 'known-password'])->assertOk()->assertJsonPath('data.role', 'mobile_anggota');
        $registration = $this->registered();
        $this->member->forceFill(['valid_until' => now()->subDay()])->save();
        $this->postJson('/api/auth/passkeys/login/verify', $this->assertion($registration, $this->loginOptions()))->assertForbidden();
        $this->member->forceFill(['valid_until' => null])->save();
        DB::table('rbac_role_users')->where('user_id', $this->member->id)->delete();
        $this->postJson('/api/auth/passkeys/login/verify', $this->assertion($registration, $this->loginOptions(), counter: 2))->assertUnauthorized();
        $this->assertSame(0, $this->member->tokens()->where('name', 'like', 'pwa-passkey:%')->count());
    }

    public function test_list_hides_cryptographic_material_and_owner_can_revoke_passkey(): void
    {
        $registration = $this->registered();
        $this->postJson('/api/auth/passkeys/login/verify', $this->assertion($registration, $this->loginOptions()))->assertOk();
        $response = $this->withToken($this->bearer)->getJson('/api/auth/passkeys')->assertOk();
        $this->assertSame(['id', 'name', 'created_at', 'last_used_at'], array_keys($response->json('data.0')));
        $id = PwaPasskey::first()->id;
        $this->withToken($this->bearer)->deleteJson('/api/auth/passkeys/'.$id, ['current_password' => 'wrong'])->assertStatus(422);
        $this->withToken($this->bearer)->deleteJson('/api/auth/passkeys/'.$id, ['current_password' => 'known-password'])->assertOk();
        $this->assertSame(0, PwaPasskey::count());
        $this->assertSame(0, $this->member->tokens()->where('name', 'like', 'pwa-passkey:%')->count());
        $this->postJson('/api/auth/passkeys/login/verify', $this->assertion($registration, $this->loginOptions(), counter: 2))->assertStatus(422);
    }

    public function test_deleting_the_passkey_used_by_the_current_session_requires_login_again(): void
    {
        $registration = $this->registered();
        $login = $this->postJson('/api/auth/passkeys/login/verify', $this->assertion($registration, $this->loginOptions()))
            ->assertOk();
        $passkeyToken = $login->json('data.token');
        $id = PwaPasskey::firstOrFail()->id;

        $this->app['auth']->forgetGuards();
        $this->withToken($passkeyToken)->deleteJson('/api/auth/passkeys/'.$id, [
            'current_password' => 'known-password',
        ])->assertOk()->assertJsonPath('data.requires_login', true);

        $this->app['auth']->forgetGuards();
        $this->withToken($passkeyToken)->getJson('/api/auth/passkeys')->assertUnauthorized();
        $this->assertDatabaseMissing('t_pwa_passkeys', ['id' => $id]);
    }

    public function test_disabled_feature_returns_capability_false_and_no_authentication_options(): void
    {
        config(['passkeys.enabled' => false]);
        $this->getJson('/api/auth/passkeys/config')->assertOk()->assertJsonPath('data.enabled', false)
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->postJson('/api/auth/passkeys/login/options')->assertNotFound()
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_a_different_member_cannot_delete_another_members_passkey(): void
    {
        $this->registered();
        $id = PwaPasskey::first()->id;
        $other = User::create(['name' => 'Other', 'username' => 'other', 'email' => 'other@example.test', 'mobile' => '0812345678', 'password' => 'other-password']);
        $token = $other->createToken('other', ['state:anggota'])->plainTextToken;
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->deleteJson('/api/auth/passkeys/'.$id, ['current_password' => 'other-password'])->assertNotFound();
        $this->assertSame(1, PwaPasskey::count());
    }

    public function test_reused_signature_counter_and_expired_login_challenge_are_rejected(): void
    {
        $registration = $this->registered();
        $this->postJson('/api/auth/passkeys/login/verify', $this->assertion($registration, $this->loginOptions()))->assertOk()->assertHeader('Cache-Control', 'no-store, private');
        $this->postJson('/api/auth/passkeys/login/verify', $this->assertion($registration, $this->loginOptions()))->assertStatus(422);
        $options = $this->loginOptions();
        DB::table('t_pwa_passkey_challenges')->where('id', $options['challenge_id'])->update(['expires_at' => now()->subSecond()]);
        $this->postJson('/api/auth/passkeys/login/verify', $this->assertion($registration, $options, counter: 2))->assertStatus(422);
        $this->assertSame(1, $this->member->tokens()->where('name', 'like', 'pwa-passkey:%')->count());
    }

    public function test_passkey_migration_rolls_back_both_tables(): void
    {
        (require database_path('migrations/2026_11_11_000000_create_pwa_passkeys_tables.php'))->down();

        $this->assertFalse(Schema::hasTable('t_pwa_passkeys'));
        $this->assertFalse(Schema::hasTable('t_pwa_passkey_challenges'));
    }
}

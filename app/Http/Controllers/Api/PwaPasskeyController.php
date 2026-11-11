<?php

namespace App\Http\Controllers\Api;

use App\Models\PwaPasskey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\WebAuthnException;

class PwaPasskeyController extends BaseController
{
    public function capabilities()
    {
        return $this->sendResponse(['enabled' => (bool) config('passkeys.enabled'), 'rp_id' => config('passkeys.rp_id')], 'Konfigurasi passkey.');
    }

    public function index(Request $request)
    {
        $this->guardOrigin($request);

        return $this->sendResponse(PwaPasskey::where('user_id', $request->user()->id)->latest()->get(['id', 'name', 'created_at', 'last_used_at']), 'Daftar passkey.');
    }

    public function registerOptions(Request $request)
    {
        $this->guardOrigin($request);
        $input = $request->validate(['current_password' => ['required', 'string', 'max:1024'], 'name' => ['required', 'string', 'max:100']]);
        $user = $request->user();
        abort_unless($request->bearerToken() && $user->currentAccessToken()?->id, 401, 'Gunakan sesi login anggota yang aktif.');
        $this->verifyPassword($user, $input['current_password']);
        abort_unless($user->anggota?->exists, 403, 'Akun anggota tidak tersedia.');
        $keys = PwaPasskey::where('user_id', $user->id)->get();
        abort_if($keys->count() >= 10, 422, 'Maksimal 10 passkey. Hapus passkey lama terlebih dahulu.');
        $handle = $keys->first()?->user_handle ?? self::encode(random_bytes(32));
        $webauthn = $this->webauthn();
        $options = $webauthn->getCreateArgs(self::decode($handle), $user->username, $user->name, 60, true, true, false,
            $keys->map(fn ($key) => self::decode($key->credential_id))->all());
        $id = $this->challenge($webauthn, 'register', [
            'user_id' => $user->id, 'token_id' => $user->currentAccessToken()?->id,
            'user_handle' => $handle, 'name' => $input['name'],
        ]);

        return $this->sendResponse(['challenge_id' => $id, 'publicKey' => $options->publicKey], 'Verifikasi passkey pada perangkat Anda.');
    }

    public function registerVerify(Request $request)
    {
        $this->guardOrigin($request);
        $input = $this->credentialInput($request, true);
        $pending = $this->consumeChallenge($input['challenge_id'], 'register');
        abort_unless((int) $pending->user_id === (int) $request->user()->id
            && (int) $pending->token_id === (int) $request->user()->currentAccessToken()?->id, 422, 'Sesi pendaftaran passkey berubah. Ulangi pendaftaran.');
        $credential = $input['credential'];
        try {
            $client = $this->clientData($credential['response']['clientDataJSON']);
            $result = $this->webauthn()->processCreate($client, self::decode($credential['response']['attestationObject']), self::decode($pending->challenge), true, true);
            abort_unless(hash_equals($result->credentialId, self::decode($credential['rawId'])), 422, 'Identitas passkey tidak sesuai.');
        } catch (WebAuthnException|\JsonException $error) {
            throw ValidationException::withMessages(['credential' => 'Verifikasi passkey gagal. Ulangi pendaftaran dari perangkat Anda.']);
        }
        DB::transaction(function () use ($request, $pending, $result) {
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            abort_if(PwaPasskey::where('user_id', $request->user()->id)->count() >= 10, 422, 'Maksimal 10 passkey.');
            $id = self::encode($result->credentialId);
            abort_if(PwaPasskey::where('credential_id_hash', hash('sha256', $id))->exists(), 422, 'Passkey sudah terdaftar.');
            PwaPasskey::create([
                'user_id' => $request->user()->id, 'name' => $pending->name,
                'credential_id' => $id, 'credential_id_hash' => hash('sha256', $id),
                'user_handle' => $pending->user_handle, 'public_key' => $result->credentialPublicKey,
                'sign_count' => $result->signatureCounter ?? 0,
            ]);
        });

        return $this->sendResponse(null, 'Passkey berhasil diaktifkan. Anda dapat masuk menggunakan Face ID atau verifikasi perangkat.');
    }

    public function loginOptions(Request $request)
    {
        $this->guardOrigin($request);
        $webauthn = $this->webauthn();
        $options = $webauthn->getGetArgs([], 60, false, false, false, false, true, true);

        return $this->sendResponse(['challenge_id' => $this->challenge($webauthn, 'login'), 'publicKey' => $options->publicKey], 'Pilih passkey KKBA Mobile.');
    }

    public function loginVerify(Request $request, AuthController $auth)
    {
        $this->guardOrigin($request);
        $input = $this->credentialInput($request, false);
        $pending = $this->consumeChallenge($input['challenge_id'], 'login');
        $credential = $input['credential'];

        return DB::transaction(function () use ($credential, $pending, $auth) {
            $key = PwaPasskey::where('credential_id_hash', hash('sha256', $credential['rawId']))->lockForUpdate()->first();
            abort_unless($key && hash_equals($key->credential_id, $credential['rawId'])
                && hash_equals($key->user_handle, $credential['response']['userHandle']), 422, 'Passkey tidak dikenali. Gunakan username dan password.');
            try {
                $webauthn = $this->webauthn();
                $webauthn->processGet($this->clientData($credential['response']['clientDataJSON']),
                    self::decode($credential['response']['authenticatorData']), self::decode($credential['response']['signature']),
                    $key->public_key, self::decode($pending->challenge), $key->sign_count, true, true);
                $key->update(['sign_count' => $webauthn->getSignatureCounter() ?? 0, 'last_used_at' => now()]);
            } catch (WebAuthnException|\JsonException $error) {
                throw ValidationException::withMessages(['credential' => 'Verifikasi passkey gagal. Silakan coba lagi atau gunakan password.']);
            }
            $user = User::find($key->user_id);
            abort_unless($user && $user->anggota?->exists, 403, 'Akun anggota tidak tersedia.');

            return $auth->issueMobileSession($user, 'mobile_anggota', 'pwa-passkey:'.$key->id, now()->addHours((int) config('passkeys.token_hours')));
        });
    }

    public function destroy(Request $request, string $id)
    {
        $this->guardOrigin($request);
        $input = $request->validate(['current_password' => ['required', 'string', 'max:1024']]);
        $this->verifyPassword($request->user(), $input['current_password']);
        $requiresLogin = DB::transaction(function () use ($request, $id) {
            $key = PwaPasskey::where('user_id', $request->user()->id)->whereKey($id)->lockForUpdate()->firstOrFail();
            $requiresLogin = $request->user()->currentAccessToken()?->name === 'pwa-passkey:'.$key->id;
            $request->user()->tokens()->where('name', 'pwa-passkey:'.$key->id)->delete();
            $key->delete();

            return $requiresLogin;
        });

        return $this->sendResponse(['requires_login' => $requiresLogin], 'Passkey berhasil dihapus.');
    }

    private function guardOrigin(Request $request): void
    {
        abort_unless(config('passkeys.enabled'), 404, 'Passkey belum diaktifkan.');
        abort_unless(in_array($request->header('Origin'), config('passkeys.origins'), true), 403, 'Origin passkey tidak diizinkan.');
    }

    private function verifyPassword(User $user, string $password): void
    {
        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'Password saat ini tidak sesuai.']);
        }
    }

    private function webauthn(): WebAuthn
    {
        return new WebAuthn(config('passkeys.rp_name'), config('passkeys.rp_id'), ['none'], true);
    }

    private function challenge(WebAuthn $webauthn, string $purpose, array $extra = []): string
    {
        DB::table('t_pwa_passkey_challenges')->where('expires_at', '<=', now())->delete();
        $id = (string) Str::uuid();
        DB::table('t_pwa_passkey_challenges')->insert([
            'id' => $id, 'purpose' => $purpose,
            'challenge' => self::encode($webauthn->getChallenge()->getBinaryString()),
            'expires_at' => now()->addSeconds((int) config('passkeys.challenge_seconds')),
            ...$extra,
        ]);

        return $id;
    }

    private function consumeChallenge(string $id, string $purpose): object
    {
        // Commit consumption before verification, so failed or simultaneous attempts cannot replay it.
        $row = DB::transaction(function () use ($id) {
            $row = DB::table('t_pwa_passkey_challenges')->where('id', $id)->lockForUpdate()->first();
            if ($row) {
                DB::table('t_pwa_passkey_challenges')->where('id', $id)->delete();
            }

            return $row;
        });
        abort_unless($row && $row->purpose === $purpose && now()->lt($row->expires_at), 422, 'Verifikasi sudah kedaluwarsa atau telah digunakan. Silakan coba lagi.');

        return $row;
    }

    private function credentialInput(Request $request, bool $registration): array
    {
        $rules = [
            'challenge_id' => ['required', 'uuid'],
            'credential.type' => ['required', 'in:public-key'],
            'credential.id' => ['required', 'string', 'max:2048', 'regex:/^[A-Za-z0-9_-]+$/'],
            'credential.rawId' => ['required', 'string', 'max:2048', 'same:credential.id', 'regex:/^[A-Za-z0-9_-]+$/'],
            'credential.response.clientDataJSON' => ['required', 'string', 'max:16384', 'regex:/^[A-Za-z0-9_-]+$/'],
        ];
        foreach ($registration ? ['attestationObject'] : ['authenticatorData', 'signature', 'userHandle'] as $field) {
            $rules['credential.response.'.$field] = ['required', 'string', 'max:32768', 'regex:/^[A-Za-z0-9_-]+$/'];
        }

        return $request->validate($rules);
    }

    private function clientData(string $encoded): string
    {
        $raw = self::decode($encoded);
        $data = json_decode($raw, true, 16, JSON_THROW_ON_ERROR);
        abort_unless(is_array($data) && in_array($data['origin'] ?? null, config('passkeys.origins'), true)
            && ($data['crossOrigin'] ?? false) === false, 422, 'Origin verifikasi passkey tidak sesuai.');

        return $raw;
    }

    private static function encode(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    private static function decode(string $value): string
    {
        $bytes = base64_decode(strtr($value, '-_', '+/'), true);
        if ($bytes === false || ! hash_equals(self::encode($bytes), $value)) {
            throw ValidationException::withMessages(['credential' => 'Format passkey tidak valid.']);
        }

        return $bytes;
    }
}

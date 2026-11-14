<?php

namespace App\Http\Controllers\Api;

use App\Services\Push\PushDeviceService;
use App\Services\Push\WebPushConfiguration;
use App\Services\Push\WebPushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PushDeviceController extends BaseController
{
    public function __construct(private readonly PushDeviceService $devices) {}

    public function webConfig(WebPushConfiguration $configuration): JsonResponse
    {
        $enabled = $configuration->enabled();

        return $this->sendResponse([
            'enabled' => $enabled,
            'vapid_public_key' => $enabled ? config('push.webpush.public_key') : null,
        ], 'Konfigurasi Web Push berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $anggotaId = $this->anggotaId($request);

        if ($request->input('provider') === 'webpush') {
            abort_unless($request->user()->tokenCan('state:anggota') && $anggotaId !== null, 403, 'Web Push hanya untuk anggota.');
        }

        if ($request->user()->tokenCan('state:anggota') && $anggotaId === null) {
            return $this->sendError('Data anggota tidak ditemukan.', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'client_installation_id' => ['required', 'uuid'],
            'provider' => ['required', Rule::in(['expo', 'fcm', 'webpush'])],
            'platform' => ['required', Rule::in(['android', 'ios', 'web'])],
            'provider_identifier' => ['exclude_if:provider,webpush', 'required', 'string', 'max:4096'],
            'subscription' => ['exclude_unless:provider,webpush', 'required', 'array:endpoint,expirationTime,keys'],
            'app_version' => ['required', 'string', 'max:50'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            $provider = $request->input('provider');
            $platform = $request->input('platform');
            $identifier = $request->input('provider_identifier');

            if (($provider === 'expo' && ! in_array($platform, ['android', 'ios'], true))
                || (in_array($provider, ['fcm', 'webpush'], true) && $platform !== 'web')) {
                $validator->errors()->add('platform', 'Kombinasi provider dan platform tidak valid.');
            }

            if ($provider === 'expo' && is_string($identifier) && ! preg_match('/^(ExponentPushToken|ExpoPushToken)\[[^\]]+\]$/', $identifier)) {
                $validator->errors()->add('provider_identifier', 'Format Expo Push Token tidak valid.');
            }
        });

        $attributes = $validator->validate();
        if ($attributes['provider'] === 'webpush') {
            abort_unless(app(WebPushConfiguration::class)->enabled(), 503, 'Web Push belum tersedia.');
            $attributes['provider_identifier'] = app(WebPushSubscription::class)->normalize($attributes['subscription']);
            unset($attributes['subscription']);
        }
        $result = $this->devices->register((int) $request->user()->id, $anggotaId, $attributes);
        $device = $result['device'];

        return response()->json([
            'success' => true,
            'data' => [
                'device' => [
                    'id' => $device->id,
                    'client_installation_id' => $device->client_installation_id,
                    'provider' => $device->provider,
                    'platform' => $device->platform,
                    'app_version' => $device->app_version,
                    'is_active' => $device->is_active,
                    'last_seen_at' => $device->last_seen_at?->toIso8601String(),
                    ...($device->provider === 'webpush' ? [
                        'binding_id' => $device->binding_id,
                        'recipient_user_id' => (string) $device->user_id,
                    ] : []),
                ],
            ],
            'message' => $result['created'] ? 'Perangkat berhasil didaftarkan.' : 'Perangkat berhasil diperbarui.',
        ], $result['created'] ? 201 : 200);
    }

    public function destroy(Request $request, string $clientInstallationId): JsonResponse
    {
        $anggotaId = $this->anggotaId($request);

        if ($request->user()->tokenCan('state:anggota') && $anggotaId === null) {
            return $this->sendError('Data anggota tidak ditemukan.', [], 404);
        }

        if ($this->devices->deactivate(
            (int) $request->user()->id,
            $clientInstallationId,
            $anggotaId,
        ) === 0) {
            return $this->sendError('Perangkat tidak ditemukan.', [], 404);
        }

        return $this->sendResponse([], 'Perangkat berhasil dinonaktifkan.');
    }

    private function anggotaId(Request $request): ?int
    {
        $anggota = $request->user()?->anggota;

        return $anggota?->exists ? (int) $anggota->p_anggota_id : null;
    }
}

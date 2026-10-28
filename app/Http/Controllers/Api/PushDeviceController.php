<?php

namespace App\Http\Controllers\Api;

use App\Services\Push\PushDeviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PushDeviceController extends BaseController
{
    public function __construct(private readonly PushDeviceService $devices) {}

    public function store(Request $request): JsonResponse
    {
        $anggotaId = $this->anggotaId($request);

        if ($anggotaId === null) {
            return $this->sendError('Data anggota tidak ditemukan.', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'client_installation_id' => ['required', 'uuid'],
            'provider' => ['required', Rule::in(['expo', 'fcm'])],
            'platform' => ['required', Rule::in(['android', 'ios', 'web'])],
            'provider_identifier' => ['required', 'string', 'max:4096'],
            'app_version' => ['required', 'string', 'max:50'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            $provider = $request->input('provider');
            $platform = $request->input('platform');
            $identifier = (string) $request->input('provider_identifier');

            if (($provider === 'expo' && ! in_array($platform, ['android', 'ios'], true))
                || ($provider === 'fcm' && $platform !== 'web')) {
                $validator->errors()->add('platform', 'Kombinasi provider dan platform tidak valid.');
            }

            if ($provider === 'expo' && ! preg_match('/^(ExponentPushToken|ExpoPushToken)\[[^\]]+\]$/', $identifier)) {
                $validator->errors()->add('provider_identifier', 'Format Expo Push Token tidak valid.');
            }
        });

        $attributes = $validator->validate();
        $result = $this->devices->register($anggotaId, $attributes);
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
                ],
            ],
            'message' => $result['created'] ? 'Perangkat berhasil didaftarkan.' : 'Perangkat berhasil diperbarui.',
        ], $result['created'] ? 201 : 200);
    }

    public function destroy(Request $request, string $clientInstallationId): JsonResponse
    {
        $anggotaId = $this->anggotaId($request);

        if ($anggotaId === null) {
            return $this->sendError('Data anggota tidak ditemukan.', [], 404);
        }

        if ($this->devices->deactivate($anggotaId, $clientInstallationId) === 0) {
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

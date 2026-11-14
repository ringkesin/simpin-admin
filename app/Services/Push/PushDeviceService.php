<?php

namespace App\Services\Push;

use App\Models\PushDevice;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PushDeviceService
{
    /**
     * @param  array{client_installation_id:string,provider:string,platform:string,provider_identifier:string,app_version:string}  $attributes
     * @return array{device:PushDevice,created:bool}
     */
    public function register(int $userId, ?int $anggotaId, array $attributes): array
    {
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                return $this->registerWithinTransaction($userId, $anggotaId, $attributes);
            } catch (QueryException $exception) {
                $sqlState = (string) ($exception->errorInfo[0] ?? $exception->getCode());

                if ($attempt === 2 || ! in_array($sqlState, ['23000', '23505'], true)) {
                    throw $exception;
                }
            }
        }

        throw new \RuntimeException('Registrasi perangkat gagal setelah percobaan ulang.');
    }

    /**
     * @param  array{client_installation_id:string,provider:string,platform:string,provider_identifier:string,app_version:string}  $attributes
     * @return array{device:PushDevice,created:bool}
     */
    private function registerWithinTransaction(int $userId, ?int $anggotaId, array $attributes): array
    {
        return DB::transaction(function () use ($userId, $anggotaId, $attributes): array {
            $isWebPush = $attributes['provider'] === 'webpush';
            $identifierHash = hash('sha256', $isWebPush
                ? 'webpush:'.json_decode($attributes['provider_identifier'], true, flags: JSON_THROW_ON_ERROR)['endpoint']
                : $attributes['provider_identifier']);

            $matchingDevices = PushDevice::query()
                ->where(function ($query) use ($attributes, $identifierHash) {
                    $query->where('provider_identifier_hash', $identifierHash)
                        ->orWhere(function ($query) use ($attributes) {
                            $query->where('provider', $attributes['provider'])
                                ->where('client_installation_id', $attributes['client_installation_id']);
                        });
                })
                ->lockForUpdate()
                ->get();

            $tokenDevice = $matchingDevices->firstWhere('provider_identifier_hash', $identifierHash);
            $installationDevice = $matchingDevices->first(
                fn (PushDevice $candidate): bool => $candidate->client_installation_id === $attributes['client_installation_id']
                    && $candidate->provider === $attributes['provider']
            );
            $device = $tokenDevice ?? $installationDevice ?? new PushDevice;
            $created = ! $device->exists;

            if ($tokenDevice && $installationDevice && ! $tokenDevice->is($installationDevice)) {
                $installationDevice->delete();
            }

            if ($isWebPush && (! $device->binding_id || ! $device->is_active
                || (int) $device->user_id !== $userId || (int) $device->p_anggota_id !== $anggotaId
                || $device->client_installation_id !== $attributes['client_installation_id']
                || $device->provider_identifier !== $attributes['provider_identifier'])) {
                $device->binding_id = (string) Str::uuid();
            }

            $device->fill([
                'p_anggota_id' => $anggotaId,
                'user_id' => $userId,
                'client_installation_id' => $attributes['client_installation_id'],
                'provider' => $attributes['provider'],
                'platform' => $attributes['platform'],
                'provider_identifier' => $attributes['provider_identifier'],
                'provider_identifier_hash' => $identifierHash,
                'app_version' => $attributes['app_version'],
                'is_active' => true,
                'last_seen_at' => now(),
                'disabled_at' => null,
                'last_error' => null,
            ])->save();

            return ['device' => $device->fresh(), 'created' => $created];
        }, 3);
    }

    public function deactivate(int $userId, string $installationId, ?int $anggotaId = null): int
    {
        return PushDevice::query()
            ->where(function ($query) use ($userId, $anggotaId): void {
                $query->where('user_id', $userId);

                if ($anggotaId !== null) {
                    $query->orWhere(function ($query) use ($anggotaId): void {
                        $query->whereNull('user_id')
                            ->where('p_anggota_id', $anggotaId);
                    });
                }
            })
            ->where('client_installation_id', $installationId)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'disabled_at' => now(),
            ]);
    }
}

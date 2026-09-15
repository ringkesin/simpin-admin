<?php

namespace App\Services;

use App\Models\Master\AnggotaModels;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AnggotaRegistrationService
{
    /**
     * Menyetujui calon anggota dan menerbitkan nomor anggota pada saat yang sama.
     */
    public function approve(int $anggotaId, ?int $actorId, array $attributes = []): AnggotaModels
    {
        return DB::transaction(function () use ($anggotaId, $actorId, $attributes) {
            $anggota = AnggotaModels::query()
                ->whereKey($anggotaId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($anggota->user_id && ! $anggota->is_registered) {
                throw ValidationException::withMessages([
                    'is_registered' => 'Anggota yang sudah memiliki user tidak dapat berstatus belum menjadi anggota.',
                ]);
            }

            if (empty($anggota->nomor_anggota)) {
                // Nomor milik anggota nonaktif tetap diperhitungkan agar tidak digunakan ulang.
                $lastAnggota = AnggotaModels::withTrashed()
                    ->whereNotNull('nomor_anggota')
                    ->where('p_anggota_id', '!=', $anggotaId)
                    ->orderByDesc('nomor_anggota')
                    ->lockForUpdate()
                    ->first();

                $anggota->nomor_anggota = $lastAnggota
                    ? (string) (((int) $lastAnggota->nomor_anggota) + 1)
                    : '100001';
            }

            $anggota->fill($attributes);
            $anggota->is_registered = true;
            $anggota->updated_by = $actorId;
            $anggota->save();

            return $anggota->fresh();
        }, 3);
    }

    /**
     * Menolak registrasi yang belum pernah disetujui.
     */
    public function reject(int $anggotaId, ?int $actorId): AnggotaModels
    {
        return DB::transaction(function () use ($anggotaId, $actorId) {
            $anggota = AnggotaModels::query()
                ->whereKey($anggotaId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($anggota->is_registered || $anggota->user_id || $anggota->nomor_anggota) {
                throw ValidationException::withMessages([
                    'anggota' => 'Hanya registrasi yang masih menunggu persetujuan yang dapat ditolak.',
                ]);
            }

            $anggota->deleted_by = $actorId;
            $anggota->save();
            $anggota->delete();

            return $anggota;
        });
    }

    /**
     * Mengaktifkan kembali anggota beserta user yang ikut dinonaktifkan.
     */
    public function restore(int $anggotaId, ?int $actorId): AnggotaModels
    {
        return DB::transaction(function () use ($anggotaId, $actorId) {
            $anggota = AnggotaModels::withTrashed()
                ->whereKey($anggotaId)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $anggota->trashed()) {
                throw ValidationException::withMessages([
                    'anggota' => 'Anggota masih aktif dan tidak perlu diaktifkan kembali.',
                ]);
            }

            if ($anggota->user_id) {
                $user = User::withTrashed()
                    ->whereKey($anggota->user_id)
                    ->lockForUpdate()
                    ->first();

                if ($user?->trashed()) {
                    $user->restore();
                    $user->deleted_by = null;
                    $user->updated_by = $actorId;
                    $user->save();
                }
            }

            $anggota->restore();
            $anggota->deleted_by = null;
            $anggota->updated_by = $actorId;
            $anggota->save();

            return $anggota->fresh();
        });
    }
}

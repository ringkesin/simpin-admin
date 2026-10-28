<?php

namespace App\Services\Push;

use App\Models\Main\PinjamanModels;
use App\Models\Main\TabunganPengambilanModels;
use App\Models\Main\TabunganPenyertaanModels;
use App\Models\Main\TabunganPerubahanPenyertaanModels;
use App\Models\Master\AnggotaModels;
use App\Models\MemberNotification;
use Illuminate\Support\Facades\DB;

use function Illuminate\Support\defer;

class ApprovalNotificationService
{
    public function anggotaApproved(AnggotaModels $anggota): MemberNotification
    {
        return $this->create(
            anggotaId: (int) $anggota->p_anggota_id,
            eventId: 'simpin:anggota:'.$anggota->p_anggota_id.':approved',
            type: 'anggota_approved',
            title: 'Keanggotaan Disetujui',
            body: 'Pendaftaran keanggotaan Anda telah disetujui.',
            data: [
                'target' => 'profile',
                'p_anggota_id' => (string) $anggota->p_anggota_id,
                'status' => 'approved',
            ],
        );
    }

    public function pinjamanApproved(PinjamanModels $pinjaman): MemberNotification
    {
        return $this->create(
            anggotaId: (int) $pinjaman->p_anggota_id,
            eventId: 'simpin:pinjaman:'.$pinjaman->t_pinjaman_id.':approved',
            type: 'pinjaman_approved',
            title: 'Pinjaman Disetujui',
            body: 'Pengajuan pinjaman Anda telah disetujui.',
            data: [
                'target' => 'pinjaman_detail',
                't_pinjaman_id' => (string) $pinjaman->t_pinjaman_id,
                'status' => 'approved',
            ],
        );
    }

    public function pencairanApproved(TabunganPengambilanModels $pencairan): MemberNotification
    {
        return $this->create(
            anggotaId: (int) $pencairan->p_anggota_id,
            eventId: 'simpin:pencairan:'.$pencairan->t_tabungan_pengambilan_id.':approved',
            type: 'pencairan_tabungan_approved',
            title: 'Pencairan Tabungan Disetujui',
            body: 'Pengajuan pencairan tabungan Anda telah disetujui.',
            data: [
                'target' => 'pencairan_tabungan_detail',
                't_tabungan_pengambilan_id' => (string) $pencairan->t_tabungan_pengambilan_id,
                'status' => 'approved',
            ],
        );
    }

    public function penyertaanApproved(TabunganPenyertaanModels $penyertaan): MemberNotification
    {
        return $this->create(
            anggotaId: (int) $penyertaan->p_anggota_id,
            eventId: 'simpin:penyertaan:'.$penyertaan->t_tabungan_penyertaan_id.':approved',
            type: 'penyertaan_approved',
            title: 'Penyertaan Disetujui',
            body: 'Pengajuan penyertaan Anda telah disetujui.',
            data: [
                'target' => 'penyertaan_detail',
                't_tabungan_penyertaan_id' => (string) $penyertaan->t_tabungan_penyertaan_id,
                'status' => 'approved',
            ],
        );
    }

    public function perubahanPenyertaanApproved(TabunganPerubahanPenyertaanModels $perubahan): MemberNotification
    {
        return $this->create(
            anggotaId: (int) $perubahan->p_anggota_id,
            eventId: 'simpin:perubahan-penyertaan:'.$perubahan->t_tabungan_perubahan_penyertaan_id.':approved',
            type: 'perubahan_penyertaan_approved',
            title: 'Perubahan Penyertaan Disetujui',
            body: 'Pengajuan perubahan penyertaan Anda telah disetujui.',
            data: [
                'target' => 'perubahan_penyertaan_detail',
                't_tabungan_perubahan_penyertaan_id' => (string) $perubahan->t_tabungan_perubahan_penyertaan_id,
                'status' => 'approved',
            ],
        );
    }

    /**
     * @param  array<string, string>  $data
     */
    private function create(
        int $anggotaId,
        string $eventId,
        string $type,
        string $title,
        string $body,
        array $data,
    ): MemberNotification {
        $notification = MemberNotification::query()->firstOrCreate(
            [
                'source' => 'simpin',
                'event_id' => $eventId,
            ],
            [
                'p_anggota_id' => $anggotaId,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'occurred_at' => now(),
            ],
        );

        if ($notification->wasRecentlyCreated && config('push.delivery_enabled')) {
            $notificationId = $notification->id;

            DB::afterCommit(function () use ($notificationId): void {
                defer(
                    fn () => app(PushDeliveryService::class)->deliver($notificationId),
                    'push-notification:'.$notificationId,
                );
            });
        }

        return $notification;
    }
}

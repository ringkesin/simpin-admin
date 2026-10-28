<?php

namespace Tests\Feature;

use App\Models\Main\PinjamanModels;
use App\Models\Main\TabunganPengambilanModels;
use App\Models\Main\TabunganPenyertaanModels;
use App\Models\Main\TabunganPerubahanPenyertaanModels;
use App\Models\Master\AnggotaModels;
use App\Models\MemberNotification;
use App\Services\Push\ApprovalNotificationService;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class ApprovalNotificationServiceTest extends TestCase
{
    use CreatesPushNotificationSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema();
        $this->actingAsAnggota(12345, 1);
        config(['push.delivery_enabled' => false]);
    }

    public function test_approval_events_create_idempotent_member_inbox_notifications(): void
    {
        $service = app(ApprovalNotificationService::class);
        $anggota = $this->model(AnggotaModels::class, [
            'p_anggota_id' => 12345,
        ]);
        $pinjaman = $this->model(PinjamanModels::class, [
            't_pinjaman_id' => 10,
            'p_anggota_id' => 12345,
        ]);
        $pencairan = $this->model(TabunganPengambilanModels::class, [
            't_tabungan_pengambilan_id' => '01KTESTPENCAIRAN0000000001',
            'p_anggota_id' => 12345,
        ]);
        $penyertaan = $this->model(TabunganPenyertaanModels::class, [
            't_tabungan_penyertaan_id' => '01KTESTPENYERTAAN000000001',
            'p_anggota_id' => 12345,
        ]);
        $perubahan = $this->model(TabunganPerubahanPenyertaanModels::class, [
            't_tabungan_perubahan_penyertaan_id' => '01KTESTPERUBAHAN000000001',
            'p_anggota_id' => 12345,
        ]);

        $createAll = function () use ($service, $anggota, $pinjaman, $pencairan, $penyertaan, $perubahan): void {
            $service->anggotaApproved($anggota);
            $service->pinjamanApproved($pinjaman);
            $service->pencairanApproved($pencairan);
            $service->penyertaanApproved($penyertaan);
            $service->perubahanPenyertaanApproved($perubahan);
        };

        $createAll();
        $createAll();

        $this->assertDatabaseCount('t_notifications', 5);
        $this->assertSame([
            'anggota_approved',
            'pencairan_tabungan_approved',
            'penyertaan_approved',
            'perubahan_penyertaan_approved',
            'pinjaman_approved',
        ], MemberNotification::query()->orderBy('type')->pluck('type')->all());
        $this->assertTrue(MemberNotification::query()->where('source', 'simpin')->get()->every(
            fn (MemberNotification $notification): bool => $notification->p_anggota_id === 12345
                && $notification->push_attempted_at === null
                && $notification->data['status'] === 'approved'
        ));
    }

    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TModel>  $modelClass
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    private function model(string $modelClass, array $attributes)
    {
        $model = new $modelClass;
        $model->setRawAttributes($attributes, true);
        $model->exists = true;

        return $model;
    }
}

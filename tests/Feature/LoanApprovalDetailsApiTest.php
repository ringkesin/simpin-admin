<?php

namespace Tests\Feature;

use App\Models\Main\PinjamanModels;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanApprovalDetailsApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        Schema::connection('sqlite')->create('t_pinjaman', function (Blueprint $table) {
            $table->id('t_pinjaman_id');
            $table->unsignedBigInteger('p_anggota_id');
            $table->unsignedBigInteger('p_jenis_pinjaman_id');
            $table->string('nomor_pinjaman');
            $table->integer('tenor');
            $table->decimal('biaya_admin', 5, 2);
            $table->decimal('margin', 5, 2);
            $table->double('ri_jumlah_pinjaman', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Sanctum::actingAs(
            new User(['name' => 'Admin', 'email' => 'admin@example.com']),
            ['state:admin'],
        );
    }

    public function test_it_returns_the_same_general_loan_amount_details_as_the_blade(): void
    {
        $pinjaman = PinjamanModels::create([
            'p_anggota_id' => 1,
            'p_jenis_pinjaman_id' => 1,
            'nomor_pinjaman' => '001/PJ/PU/IX/2026',
            'tenor' => 12,
            'biaya_admin' => 1.5,
            'margin' => 12,
            'ri_jumlah_pinjaman' => 12_000_000,
        ]);

        $this->postJson('/api/pinjaman/approval/rincian', [
            't_pinjaman_id' => $pinjaman->t_pinjaman_id,
            'ri_jumlah_pinjaman' => 12_000_000,
            'tenor' => 12,
            'margin' => 12,
            'biaya_admin' => 1.5,
        ])
            ->assertOk()
            ->assertJsonPath('data.rincian_approval.jumlah_pinjaman_disetujui.nilai', 12_000_000)
            ->assertJsonPath('data.rincian_approval.jumlah_pinjaman_disetujui.rupiah', 'Rp. 12.000.000')
            ->assertJsonPath('data.rincian_approval.margin.nilai', 1_440_000)
            ->assertJsonPath('data.rincian_approval.biaya_admin.nilai', 180_000)
            ->assertJsonPath('data.rincian_approval.total_jumlah_disetujui_dan_margin.nilai', 13_440_000)
            ->assertJsonPath('data.rincian_approval.estimasi_cicilan_per_bulan.nilai', 1_135_000);
    }

    public function test_preview_uses_input_values_without_saving_the_approval(): void
    {
        $pinjaman = PinjamanModels::create([
            'p_anggota_id' => 1,
            'p_jenis_pinjaman_id' => 2,
            'nomor_pinjaman' => '002/PJ/PK/IX/2026',
            'tenor' => 10,
            'biaya_admin' => 1.5,
            'margin' => 8,
            'ri_jumlah_pinjaman' => 10_000_000,
        ]);

        $this->postJson('/api/pinjaman/approval/rincian', [
            't_pinjaman_id' => $pinjaman->t_pinjaman_id,
            'ri_jumlah_pinjaman' => 20_000_000,
            'tenor' => 20,
            'margin' => 10,
            'biaya_admin' => 2,
        ])
            ->assertOk()
            ->assertJsonPath('data.rincian_approval.margin.persen', 10)
            ->assertJsonPath('data.rincian_approval.margin.nilai', 2_000_000)
            ->assertJsonPath('data.rincian_approval.biaya_admin.nilai', 400_000)
            ->assertJsonPath('data.rincian_approval.total_jumlah_disetujui_dan_margin.nilai', 22_000_000)
            ->assertJsonPath('data.rincian_approval.estimasi_cicilan_per_bulan.nilai', 1_100_000);

        $pinjaman->refresh();
        $this->assertSame(10_000_000.0, $pinjaman->ri_jumlah_pinjaman);
        $this->assertSame(10, $pinjaman->tenor);
        $this->assertSame(8.0, (float) $pinjaman->margin);
        $this->assertSame(1.5, $pinjaman->biaya_admin);
    }

    public function test_preview_requires_all_approval_calculation_inputs(): void
    {
        $this->postJson('/api/pinjaman/approval/rincian', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                't_pinjaman_id',
                'ri_jumlah_pinjaman',
                'tenor',
                'margin',
                'biaya_admin',
            ]);
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\Page\Master\RekeningKkba\RekeningKkbaCreate;
use App\Livewire\Page\Master\RekeningKkba\RekeningKkbaEdit;
use App\Livewire\Page\Master\RekeningKkba\RekeningKkbaList;
use App\Livewire\Page\Master\RekeningKkba\RekeningKkbaShow;
use App\Models\Master\RekeningKkba;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;
use Tests\TestCase;

class MasterRekeningKkbaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        Schema::connection('sqlite')->create('p_rekening_kkba', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank', 100);
            $table->string('nomor_rekening', 50);
            $table->string('atas_nama', 150);
            $table->boolean('is_active')->default(true);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['nama_bank', 'nomor_rekening']);
        });

        Sanctum::actingAs(new User(['name' => 'Admin', 'email' => 'admin@example.com']));
    }

    public function test_api_can_manage_rekening_kkba(): void
    {
        $created = $this->postJson('/api/master/rekening-kkba', [
            'nama_bank' => 'Bank Mandiri',
            'nomor_rekening' => '0012345678',
            'atas_nama' => 'KKBA',
            'is_active' => true,
            'keterangan' => 'Rekening utama',
        ])->assertCreated()
            ->assertJsonPath('data.rekening_kkba.nomor_rekening', '0012345678');

        $id = $created->json('data.rekening_kkba.id');

        $this->getJson('/api/master/rekening-kkba')
            ->assertOk()
            ->assertJsonCount(1, 'data.rekening_kkba');

        $this->getJson("/api/master/rekening-kkba/{$id}")
            ->assertOk()
            ->assertJsonPath('data.rekening_kkba.atas_nama', 'KKBA');

        $this->putJson("/api/master/rekening-kkba/{$id}", [
            'nama_bank' => 'Bank Mandiri',
            'nomor_rekening' => '0012345678',
            'atas_nama' => 'Koperasi KKBA',
            'is_active' => false,
            'keterangan' => null,
        ])->assertOk()
            ->assertJsonPath('data.rekening_kkba.is_active', false);

        $this->deleteJson("/api/master/rekening-kkba/{$id}")->assertOk();
        $this->assertSoftDeleted('p_rekening_kkba', ['id' => $id]);
    }

    public function test_api_rejects_invalid_and_duplicate_account_numbers(): void
    {
        RekeningKkba::create([
            'nama_bank' => 'BCA',
            'nomor_rekening' => '12345',
            'atas_nama' => 'KKBA',
            'is_active' => true,
        ]);

        $this->postJson('/api/master/rekening-kkba', [
            'nama_bank' => 'BCA',
            'nomor_rekening' => '12345',
            'atas_nama' => 'KKBA',
            'is_active' => true,
        ])->assertUnprocessable()->assertJsonValidationErrors('nomor_rekening');

        $this->postJson('/api/master/rekening-kkba', [
            'nama_bank' => 'BRI',
            'nomor_rekening' => '12-ABC',
            'atas_nama' => 'KKBA',
            'is_active' => true,
        ])->assertUnprocessable()->assertJsonValidationErrors('nomor_rekening');
    }

    public function test_livewire_form_preserves_leading_zero_in_account_number(): void
    {
        Livewire::test(RekeningKkbaCreate::class)
            ->set('nama_bank', 'BNI')
            ->set('nomor_rekening', '000123456')
            ->set('atas_nama', 'KKBA')
            ->set('is_active', true)
            ->call('saveInsert')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('p_rekening_kkba', [
            'nama_bank' => 'BNI',
            'nomor_rekening' => '000123456',
        ]);
    }

    public function test_livewire_pages_can_list_update_and_delete_an_account(): void
    {
        $rekening = RekeningKkba::create([
            'nama_bank' => 'BRI',
            'nomor_rekening' => '987654321',
            'atas_nama' => 'KKBA',
            'is_active' => true,
        ]);

        Livewire::test(RekeningKkbaList::class)
            ->assertOk()
            ->assertSee('987654321');

        Livewire::test(RekeningKkbaEdit::class, ['id' => $rekening->id])
            ->set('atas_nama', 'Koperasi KKBA')
            ->set('is_active', false)
            ->call('saveUpdate')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('p_rekening_kkba', [
            'id' => $rekening->id,
            'atas_nama' => 'Koperasi KKBA',
            'is_active' => false,
        ]);

        Livewire::test(RekeningKkbaShow::class, ['id' => $rekening->id])
            ->call('delete')
            ->assertRedirect(route('master.rekening-kkba.list'));

        $this->assertSoftDeleted('p_rekening_kkba', ['id' => $rekening->id]);
    }
}

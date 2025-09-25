<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_tabungan_penyertaan', function (Blueprint $table) {
            $table->ulid('t_tabungan_penyertaan_id')->primary();
            $table->unsignedBigInteger('p_anggota_id');
            $table->unsignedBigInteger('p_jenis_tabungan_id');
            $table->double('jumlah', 15, 2);
            $table->date('penyertaan_date')->nullable(); // Tanggal mulai berlaku
            $table->string('status_penyertaan')->comment('PENDING, DIVERIFIKASI, DISETUJUI, DITOLAK');
            $table->longText('catatan_user')->nullable();
            $table->longText('catatan_approver')->nullable();
            $table->timestamps($precision = 0);
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_tabungan_penyertaan');
    }
};

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
        Schema::create('t_tabungan_saldo', function (Blueprint $table) {
            $table->ulid('t_tabungan_saldo_id')->primary();
            $table->unsignedBigInteger('p_anggota_id');
            $table->double('simpanan_pokok', 15, 2);
            $table->double('simpanan_wajib', 15, 2);
            $table->double('tabungan_sukarela', 15, 2);
            $table->double('tabungan_indir', 15, 2);
            $table->double('kompensasi_masa_kerja', 15, 2);
            $table->integer('tahun');
            $table->timestamps($precision = 0);
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->foreign('p_anggota_id')->references('p_anggota_id')->on('p_anggota')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_tabungan_saldo');
    }
};

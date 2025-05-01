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
            $table->unsignedBigInteger('p_jenis_tabungan_id');
            $table->integer('tahun');
            $table->double('total_sd', 15, 2);
            $table->timestamps($precision = 0);
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->foreign('p_anggota_id')->references('p_anggota_id')->on('p_anggota')->onDelete('restrict');
            $table->foreign('p_jenis_tabungan_id')->references('p_jenis_tabungan_id')->on('p_jenis_tabungan')->onDelete('restrict');
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

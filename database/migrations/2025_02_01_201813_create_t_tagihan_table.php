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
        Schema::create('t_tagihan', function (Blueprint $table) {
            $table->bigIncrements('t_tagihan_id');
            $table->unsignedBigInteger('p_anggota_id');
            $table->mediumText('uraian');
            $table->double('jumlah', 15, 2);
            $table->mediumText('remarks');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->timestamps($precision = 0);
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->foreign('p_anggota_id')
                ->references('p_anggota_id')->on('p_anggota')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_tagihan');
    }
};

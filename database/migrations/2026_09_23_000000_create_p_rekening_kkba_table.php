<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('p_rekening_kkba', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank', 100);
            $table->string('nomor_rekening', 50);
            $table->string('atas_nama', 150);
            $table->boolean('is_active')->default(true);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['nama_bank', 'nomor_rekening'], 'rekening_kkba_bank_nomor_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_rekening_kkba');
    }
};

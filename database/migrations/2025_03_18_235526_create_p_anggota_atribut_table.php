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
        Schema::create('p_anggota_atribut', function (Blueprint $table) {
            $table->bigIncrements('p_anggota_atribut_id');
            $table->unsignedBigInteger('p_anggota_id');
            $table->string('atribut_kode')->comment('Contoh : ktp, npwp, kk, dll');
            $table->longText('atribut_value')->nullable();
            $table->longText('atribut_attachment')->nullable();
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
        Schema::dropIfExists('p_anggota_atribut');
    }
};

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
        Schema::create('t_shu', function (Blueprint $table) {
            $table->bigIncrements('t_shu_id');
            $table->unsignedBigInteger('p_anggota_id');
            $table->integer('tahun');
            $table->double('shu_diterima', 15, 2)->default(0);
            $table->double('shu_dibagi', 15, 2)->default(0);
            $table->double('shu_ditabung', 15, 2)->default(0);
            $table->double('shu_tahun_lalu', 15, 2)->default(0);
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
        Schema::dropIfExists('t_shu');
    }
};

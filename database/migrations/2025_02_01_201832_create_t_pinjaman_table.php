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
        Schema::create('t_pinjaman', function (Blueprint $table) {
            $table->bigIncrements('t_pinjaman_id');
            $table->unsignedBigInteger('p_anggota_id');
            $table->unsignedBigInteger('p_jenis_pinjaman_id');
            $table->double('ra_jumlah_pinjaman', 15, 2);
            $table->double('ri_jumlah_pinjaman', 15, 2);
            $table->string('alamat')->nullable();
            $table->double('prakiraan_nilai_pasar', 15, 2);
            $table->string('no_rekening')->nullable();
            $table->string('bank')->nullable();
            $table->string('doc_ktp')->nullable();
            $table->string('doc_surat_nikah')->nullable();
            $table->string('dok_kk')->nullable();
            $table->string('dok_kartu_anggota')->nullable();
            $table->string('dok_slip_gaji')->nullable();
            $table->unsignedBigInteger('p_status_pengajuan_id');
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
        Schema::dropIfExists('t_pinjaman');
    }
};

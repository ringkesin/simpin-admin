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
            $table->string('nomor_pinjaman');
            $table->json('p_pinjaman_keperluan_ids')->nullable();
            $table->string('jenis_barang')->nullable()->comment('Diisi Jika Jenis Pinjaman Kredit Barang');
            $table->string('merk_type')->nullable()->comment('Diisi Jika Jenis Pinjaman Kredit Barang');
            $table->integer('tenor')->comment('Dalam Bulan');
            $table->decimal('biaya_admin', 5, 2);

            $table->double('ra_jumlah_pinjaman', 15, 2);
            $table->double('ri_jumlah_pinjaman', 15, 2);

            $table->string('jaminan')->nullable();
            $table->string('jaminan_keterangan')->nullable();
            $table->double('jaminan_perkiraan_nilai', 15, 2);

            $table->string('no_rekening')->nullable();
            $table->string('bank')->nullable();

            $table->decimal('margin', 5, 2);
            $table->date('tgl_pencairan')->nullable();
            $table->date('tgl_pelunasan')->nullable();

            $table->string('doc_ktp')->nullable();
            $table->string('doc_ktp_suami_istri')->nullable();
            $table->string('doc_kk')->nullable();
            $table->string('doc_kartu_anggota')->nullable();
            $table->string('doc_slip_gaji')->nullable();

            $table->unsignedBigInteger('p_status_pengajuan_id');
            $table->text('remarks')->nullable();

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

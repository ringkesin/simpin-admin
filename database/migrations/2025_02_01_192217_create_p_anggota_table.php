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
        Schema::create('p_anggota', function (Blueprint $table) {
            $table->bigIncrements('p_anggota_id');
            $table->string('nomor_anggota', 255)->unique();
            $table->date('valid_from'); // Tanggal mulai berlaku
            $table->date('valid_to')->nullable(); // Tanggal berakhir (opsional)
            $table->date('tanggal_masuk'); // Tanggal masuk anggota koperasi
            $table->string('nama', 255); // Nama anggota koperasi
            $table->string('nik', 255)->unique()->nullable(); // Nomor Induk Karyawan (unik)
            $table->string('alamat')->nullable();
            $table->string('ktp')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->string('tgl_lahir')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('is_registered');
            $table->unsignedBigInteger('p_jenis_kelamin_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('p_company_id')->nullable();
            $table->unsignedBigInteger('p_unit_id')->nullable();
            $table->timestamps($precision = 0);
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });

        // Set nilai awal AUTO_INCREMENT ke 100001
        //DB::statement('ALTER TABLE p_anggota AUTO_INCREMENT = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_anggota');
    }
};

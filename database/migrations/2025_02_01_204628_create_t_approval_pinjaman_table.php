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
        Schema::create('t_approval_pinjaman', function (Blueprint $table) {
            $table->bigIncrements('t_approval_pinjaman_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('level');
            $table->unsignedBigInteger('p_status_pengajuan_id');
            $table->date('tanggal_approval');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_approval_pinjaman');
    }
};

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
        Schema::create('t_simulasi_pinjaman', function (Blueprint $table) {
            $table->increments('id');
            $table->double('pinjaman');
            $table->integer('tenor');
            $table->decimal('margin', 5, 2);
            $table->double('angsuran', 15, 2);
            $table->integer('tahun_margin');
            $table->enum('status', ['aktif', 'tidak'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
   /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_simulasi_pinjaman');
    }
};

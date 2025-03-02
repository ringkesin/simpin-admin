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
            $table->id();
            $table->integer('maksimum_rp');
            $table->year('tahun');
            $table->decimal('bunga_12', 5, 2)->default(6.91);
            $table->decimal('bunga_24', 5, 2)->default(6.99);
            $table->decimal('bunga_36', 5, 2)->default(7.13);
            $table->decimal('bunga_48', 5, 2)->default(7.27);
            $table->integer('angsuran_12');
            $table->integer('angsuran_24');
            $table->integer('angsuran_36');
            $table->integer('angsuran_48');
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
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

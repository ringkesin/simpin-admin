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
        Schema::create('t_tabungan_penyertaan_file', function (Blueprint $table) {
            $table->ulid('t_tabungan_penyertaan_file_id')->primary();
            $table->foreignUlid('t_tabungan_penyertaan_id')
                ->constrained('t_tabungan_penyertaan', 't_tabungan_penyertaan_id');
            $table->string('nama_file');
            $table->longText('path_file');
            $table->string('mime_type', 100)->nullable();
            $table->timestamps($precision = 0);
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_tabungan_penyertaan_file');
    }
};

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
        Schema::create('t_delete_account_requests', function (Blueprint $table) {
            // kolom primary key pakai UUID
            $table->ulid('t_delete_account_requests_id')->primary();

            // relasi ke anggota
            $table->unsignedBigInteger('p_anggota_id');

            // catatan tambahan
            $table->text('remarks')->nullable();

            // status request
            $table->enum('status', ['open', 'approved', 'rejected'])->default('open');
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
        Schema::dropIfExists('t_delete_account_requests');
    }
};

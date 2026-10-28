<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_push_devices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedBigInteger('p_anggota_id');
            $table->uuid('client_installation_id');
            $table->string('provider', 16);
            $table->string('platform', 16);
            $table->text('provider_identifier');
            $table->char('provider_identifier_hash', 64)->unique();
            $table->string('app_version', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampTz('last_seen_at')->nullable();
            $table->timestampTz('disabled_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestampsTz();

            $table->unique(['client_installation_id', 'provider']);
            $table->index(['p_anggota_id', 'is_active']);
            $table->foreign('p_anggota_id')
                ->references('p_anggota_id')
                ->on('p_anggota')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_push_devices');
    }
};

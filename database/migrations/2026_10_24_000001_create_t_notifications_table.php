<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_notifications', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedBigInteger('p_anggota_id');
            $table->string('source', 32);
            $table->string('event_id', 255);
            $table->string('type', 100);
            $table->string('title', 120);
            $table->string('body', 500);
            $table->json('data');
            $table->timestampTz('occurred_at');
            $table->timestampTz('read_at')->nullable();
            $table->timestampTz('push_attempted_at')->nullable();
            $table->unsignedInteger('push_accepted_count')->default(0);
            $table->unsignedInteger('push_failed_count')->default(0);
            $table->timestampsTz();

            $table->unique(['source', 'event_id']);
            $table->index(['p_anggota_id', 'created_at']);
            $table->index(['p_anggota_id', 'read_at']);
            $table->foreign('p_anggota_id')
                ->references('p_anggota_id')
                ->on('p_anggota')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_notifications');
    }
};

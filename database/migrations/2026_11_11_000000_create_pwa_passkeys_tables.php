<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_pwa_passkeys', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 100);
            $table->text('credential_id');
            $table->char('credential_id_hash', 64)->unique();
            $table->string('user_handle', 64);
            $table->text('public_key');
            $table->unsignedBigInteger('sign_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
        Schema::create('t_pwa_passkey_challenges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('purpose', 20);
            $table->string('challenge', 128);
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('token_id')->nullable();
            $table->string('user_handle', 64)->nullable();
            $table->string('name', 100)->nullable();
            $table->timestamp('expires_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_pwa_passkey_challenges');
        Schema::dropIfExists('t_pwa_passkeys');
    }
};

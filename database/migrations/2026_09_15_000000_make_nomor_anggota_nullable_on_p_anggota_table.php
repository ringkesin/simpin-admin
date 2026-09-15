<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('p_anggota', function (Blueprint $table) {
            $table->string('nomor_anggota', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('p_anggota', function (Blueprint $table) {
            $table->string('nomor_anggota', 255)->nullable(false)->change();
        });
    }
};

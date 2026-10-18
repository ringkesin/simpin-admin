<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('p_feature_flags', function (Blueprint $table) {
            $table->json('allowed_anggota_ids')->nullable()->after('coming_soon_message');
        });
    }

    public function down(): void
    {
        Schema::table('p_feature_flags', function (Blueprint $table) {
            $table->dropColumn('allowed_anggota_ids');
        });
    }
};

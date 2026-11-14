<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_push_devices', function (Blueprint $table) {
            $table->uuid('binding_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('t_push_devices', function (Blueprint $table) {
            $table->dropColumn('binding_id');
        });
    }
};

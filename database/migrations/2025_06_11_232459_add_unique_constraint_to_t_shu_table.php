<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('t_shu', function (Blueprint $table) {
            $table->unique(['p_anggota_id', 'tahun']);
        });
    }

    public function down()
    {
        Schema::table('t_shu', function (Blueprint $table) {
            $table->dropUnique(['t_shu_p_anggota_id_tahun_unique']);
        });
    }
};

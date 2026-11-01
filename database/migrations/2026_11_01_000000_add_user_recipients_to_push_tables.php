<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_push_devices', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('p_anggota_id');
            $table->index(['user_id', 'is_active'], 't_push_devices_user_active_index');
            $table->foreign('user_id', 't_push_devices_user_id_foreign')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });

        Schema::table('t_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('p_anggota_id');
            $table->index(['user_id', 'created_at'], 't_notifications_user_created_index');
            $table->index(['user_id', 'read_at'], 't_notifications_user_read_index');
            $table->foreign('user_id', 't_notifications_user_id_foreign')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });

        $anggotaUsers = DB::table('p_anggota')
            ->whereNotNull('user_id')
            ->pluck('user_id', 'p_anggota_id');

        foreach ($anggotaUsers as $anggotaId => $userId) {
            DB::table('t_push_devices')
                ->where('p_anggota_id', $anggotaId)
                ->whereNull('user_id')
                ->update(['user_id' => $userId]);

            DB::table('t_notifications')
                ->where('p_anggota_id', $anggotaId)
                ->whereNull('user_id')
                ->update(['user_id' => $userId]);
        }

        Schema::table('t_push_devices', function (Blueprint $table) {
            $table->unsignedBigInteger('p_anggota_id')->nullable()->change();
        });

        Schema::table('t_notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('p_anggota_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('t_push_devices')->whereNull('p_anggota_id')->delete();
        DB::table('t_notifications')->whereNull('p_anggota_id')->delete();

        Schema::table('t_push_devices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex('t_push_devices_user_active_index');
            $table->dropColumn('user_id');
            $table->unsignedBigInteger('p_anggota_id')->nullable(false)->change();
        });

        Schema::table('t_notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex('t_notifications_user_created_index');
            $table->dropIndex('t_notifications_user_read_index');
            $table->dropColumn('user_id');
            $table->unsignedBigInteger('p_anggota_id')->nullable(false)->change();
        });
    }
};

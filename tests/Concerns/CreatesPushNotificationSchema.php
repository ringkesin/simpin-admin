<?php

namespace Tests\Concerns;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;

trait CreatesPushNotificationSchema
{
    protected function setUpPushNotificationSchema(bool $withAuthTables = false): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        if ($withAuthTables) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('username')->unique();
                $table->string('email')->unique();
                $table->string('password');
                $table->string('mobile')->unique();
                $table->date('valid_from')->nullable();
                $table->date('valid_until')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            $personalAccessTokenMigration = require database_path('migrations/2025_02_08_042525_create_personal_access_tokens_table.php');
            $personalAccessTokenMigration->up();
        }

        Schema::create('p_anggota', function (Blueprint $table) {
            $table->bigIncrements('p_anggota_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->softDeletes();
        });

        $pushDeviceMigration = require database_path('migrations/2026_10_24_000000_create_t_push_devices_table.php');
        $notificationMigration = require database_path('migrations/2026_10_24_000001_create_t_notifications_table.php');
        $pushDeviceMigration->up();
        $notificationMigration->up();
    }

    protected function actingAsAnggota(int $anggotaId = 1001, int $userId = 1): User
    {
        DB::table('p_anggota')->insertOrIgnore([
            'p_anggota_id' => $anggotaId,
            'user_id' => $userId,
        ]);

        $user = new User([
            'name' => 'Mobile User '.$userId,
            'username' => 'mobile'.$userId,
            'email' => 'mobile'.$userId.'@example.com',
        ]);
        $user->forceFill(['id' => $userId]);
        $user->exists = true;

        Sanctum::actingAs($user, ['state:anggota']);

        return $user;
    }
}

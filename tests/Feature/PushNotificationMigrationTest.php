<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class PushNotificationMigrationTest extends TestCase
{
    use CreatesPushNotificationSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema();
    }

    public function test_push_notification_migrations_can_be_rolled_back(): void
    {
        $this->assertTrue(Schema::hasColumns('t_push_devices', [
            'id',
            'p_anggota_id',
            'provider_identifier',
            'provider_identifier_hash',
        ]));
        $this->assertTrue(Schema::hasColumns('t_notifications', [
            'id',
            'p_anggota_id',
            'event_id',
            'occurred_at',
            'push_attempted_at',
        ]));

        $notificationMigration = require database_path('migrations/2026_10_24_000001_create_t_notifications_table.php');
        $pushDeviceMigration = require database_path('migrations/2026_10_24_000000_create_t_push_devices_table.php');
        $notificationMigration->down();
        $pushDeviceMigration->down();

        $this->assertFalse(Schema::hasTable('t_notifications'));
        $this->assertFalse(Schema::hasTable('t_push_devices'));
    }
}

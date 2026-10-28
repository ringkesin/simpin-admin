<?php

namespace Tests\Feature;

use App\Models\MemberNotification;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class NotificationInboxApiTest extends TestCase
{
    use CreatesPushNotificationSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema();
    }

    public function test_inbox_is_paginated_newest_first_and_isolated_by_member(): void
    {
        $this->actingAsAnggota(1001, 1);
        $this->actingAsAnggota(1002, 2);

        $older = $this->notification(1001, 'event-older', 'Notifikasi lama');
        $newer = $this->notification(1001, 'event-newer', 'Notifikasi baru');
        $older->update(['created_at' => now()->subMinute()]);
        $this->notification(1002, 'event-other', 'Milik anggota lain');

        $this->actingAsAnggota(1001, 1);
        $this->getJson('/api/notifications?per_page=1')
            ->assertOk()
            ->assertJsonPath('data.notifications.per_page', 1)
            ->assertJsonPath('data.notifications.total', 2)
            ->assertJsonPath('data.notifications.data.0.id', $newer->id)
            ->assertJsonMissing(['title' => 'Milik anggota lain']);
    }

    public function test_read_operations_are_idempotent_and_isolated_by_member(): void
    {
        $this->actingAsAnggota(1001, 1);
        $first = $this->notification(1001, 'event-first', 'Pertama');
        $second = $this->notification(1001, 'event-second', 'Kedua');
        $this->actingAsAnggota(1002, 2);
        $other = $this->notification(1002, 'event-other', 'Anggota lain');
        $this->actingAsAnggota(1001, 1);

        $this->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('data.unread_count', 2);

        $this->postJson('/api/notifications/'.$other->id.'/read')->assertNotFound();

        $this->postJson('/api/notifications/'.$first->id.'/read')
            ->assertOk()
            ->assertJsonPath('data.notification.id', $first->id);
        $this->postJson('/api/notifications/'.$first->id.'/read')->assertOk();

        $this->postJson('/api/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('data.updated_count', 1);

        $this->assertNotNull($first->fresh()->read_at);
        $this->assertNotNull($second->fresh()->read_at);
        $this->assertNull($other->fresh()->read_at);
    }

    private function notification(int $anggotaId, string $eventId, string $title): MemberNotification
    {
        return MemberNotification::create([
            'p_anggota_id' => $anggotaId,
            'source' => 'mart',
            'event_id' => $eventId,
            'type' => 'order_status',
            'title' => $title,
            'body' => 'Isi notifikasi.',
            'data' => ['target' => 'order_detail'],
            'occurred_at' => now(),
        ]);
    }
}

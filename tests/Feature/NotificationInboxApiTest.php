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

    public function test_admin_inbox_is_scoped_by_user_id_and_supports_read_operations(): void
    {
        $this->actingAsAdmin(1);
        $mine = $this->userNotification(1, 'admin-event', 'Pesan untuk admin');

        $this->actingAsAdmin(2);
        $other = $this->userNotification(2, 'other-admin-event', 'Pesan admin lain');

        $this->actingAsAdmin(1);
        $this->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonPath('data.notifications.total', 1)
            ->assertJsonPath('data.notifications.data.0.id', $mine->id)
            ->assertJsonMissing(['id' => $other->id]);

        $this->postJson('/api/notifications/'.$mine->id.'/read')->assertOk();
        $this->postJson('/api/notifications/'.$other->id.'/read')->assertNotFound();
        $this->assertNotNull($mine->fresh()->read_at);
        $this->assertNull($other->fresh()->read_at);
    }

    public function test_list_exposes_actionable_navigation_and_can_filter_read_status(): void
    {
        $this->actingAsAnggota(1001, 1);
        $notification = MemberNotification::create([
            'p_anggota_id' => 1001,
            'user_id' => 1,
            'source' => 'simpin',
            'event_id' => 'pinjaman-approved',
            'type' => 'pinjaman_approved',
            'title' => 'Pinjaman Disetujui',
            'body' => 'Pengajuan pinjaman Anda telah disetujui.',
            'data' => [
                'target' => 'pinjaman_detail',
                't_pinjaman_id' => '987',
                'status' => 'approved',
            ],
            'occurred_at' => now(),
        ]);

        $this->getJson('/api/notifications?status=unread')
            ->assertOk()
            ->assertJsonPath('data.notifications.total', 1)
            ->assertJsonPath('data.notifications.data.0.navigation.action', 'open_screen')
            ->assertJsonPath('data.notifications.data.0.navigation.is_actionable', true)
            ->assertJsonPath('data.notifications.data.0.navigation.scope', 'pinjaman')
            ->assertJsonPath('data.notifications.data.0.navigation.target', 'pinjaman_detail')
            ->assertJsonPath('data.notifications.data.0.navigation.params.t_pinjaman_id', '987');

        $notification->update(['read_at' => now()]);

        $this->getJson('/api/notifications?status=unread')
            ->assertOk()
            ->assertJsonPath('data.notifications.total', 0);
        $this->getJson('/api/notifications?status=read')
            ->assertOk()
            ->assertJsonPath('data.notifications.total', 1);
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

    private function userNotification(int $userId, string $eventId, string $title): MemberNotification
    {
        return MemberNotification::create([
            'user_id' => $userId,
            'source' => 'simpin',
            'event_id' => $eventId,
            'type' => 'chat_message',
            'title' => $title,
            'body' => 'Ada chat baru.',
            'data' => [
                'target' => 'chat',
                't_chat_id' => '01KTESTCHAT000000000000001',
            ],
            'occurred_at' => now(),
        ]);
    }
}

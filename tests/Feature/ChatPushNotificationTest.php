<?php

namespace Tests\Feature;

use App\Models\Main\ChatModels;
use App\Models\MemberNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class ChatPushNotificationTest extends TestCase
{
    use CreatesPushNotificationSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema();

        Schema::create('rbac_role', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('code')->unique();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->softDeletes();
        });
        Schema::create('rbac_role_users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->unsignedInteger('role_id');
            $table->unsignedBigInteger('user_id');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
        });

        $chatMigration = require database_path('migrations/2025_04_30_031226_create_t_chat_table.php');
        $conversationMigration = require database_path('migrations/2025_04_30_035230_create_t_chat_conversations_table.php');
        $chatMigration->up();
        $conversationMigration->up();

        DB::table('rbac_role')->insert([
            'id' => 1,
            'code' => 'mobile_admin',
            'valid_from' => now()->subDay()->toDateString(),
        ]);

        config(['push.delivery_enabled' => false]);
    }

    public function test_admin_reply_notifies_the_ticket_owner(): void
    {
        $this->actingAsAnggota(1233, 2);
        $chat = $this->ticket(2);

        $this->actingAsAdmin(1);
        $this->assignAdminRole(1);

        $this->postJson('/api/chat/message/add', [
            't_chat_id' => $chat->t_chat_id,
            'message_text' => 'Pengajuan Anda sudah kami periksa.',
        ])->assertOk()
            ->assertJsonPath('data.is_read_user', false)
            ->assertJsonPath('data.is_read_admin', true);

        $notification = MemberNotification::query()->sole();

        $this->assertSame(2, $notification->user_id);
        $this->assertSame(1233, $notification->p_anggota_id);
        $this->assertSame('chat_message', $notification->type);
        $this->assertSame('admin', $notification->data['sender_role']);
        $this->assertSame($chat->t_chat_id, $notification->data['t_chat_id']);
    }

    public function test_member_reply_notifies_mobile_admin_user_one(): void
    {
        $this->actingAsAdmin(1);
        $this->assignAdminRole(1);

        $this->actingAsAnggota(1233, 2);
        $chat = $this->ticket(2);

        $this->postJson('/api/chat/message/add', [
            't_chat_id' => $chat->t_chat_id,
            'message_text' => 'Baik admin, terima kasih.',
        ])->assertOk()
            ->assertJsonPath('data.is_read_user', true)
            ->assertJsonPath('data.is_read_admin', false);

        $notification = MemberNotification::query()->sole();

        $this->assertSame(1, $notification->user_id);
        $this->assertNull($notification->p_anggota_id);
        $this->assertSame('anggota', $notification->data['sender_role']);
        $this->assertStringContainsString(':user:1', $notification->event_id);
    }

    public function test_member_cannot_reply_to_another_members_ticket(): void
    {
        $this->actingAsAnggota(1233, 2);
        $chat = $this->ticket(2);

        $this->actingAsAnggota(1234, 3);

        $this->postJson('/api/chat/message/add', [
            't_chat_id' => $chat->t_chat_id,
            'message_text' => 'Pesan tidak sah.',
        ])->assertForbidden();

        $this->assertDatabaseCount('t_chat_conversations', 0);
        $this->assertDatabaseCount('t_notifications', 0);
    }

    private function ticket(int $ownerUserId): ChatModels
    {
        return ChatModels::query()->create([
            'ticket_code' => '#TEST01',
            'subject' => 'Pengujian chat',
            'status' => 0,
            'created_by' => $ownerUserId,
        ]);
    }

    private function assignAdminRole(int $userId): void
    {
        DB::table('rbac_role_users')->insert([
            'id' => (string) Str::ulid(),
            'role_id' => 1,
            'user_id' => $userId,
            'valid_from' => now()->subDay()->toDateString(),
        ]);
    }
}

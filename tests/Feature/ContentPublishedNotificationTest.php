<?php

namespace Tests\Feature;

use App\Livewire\Page\Main\Konten\KontenCreate;
use App\Models\Main\ContentModels;
use App\Models\Master\ContentTypeModels;
use App\Models\MemberNotification;
use App\Services\Push\ContentPublishedNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\Concerns\CreatesPushNotificationSchema;
use Tests\TestCase;

class ContentPublishedNotificationTest extends TestCase
{
    use CreatesPushNotificationSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPushNotificationSchema();
        config(['push.delivery_enabled' => false]);
    }

    public function test_created_content_notifies_every_active_registered_member_idempotently(): void
    {
        $this->actingAsAnggota(1001, 1);
        $this->actingAsAnggota(1002, 2);
        $this->actingAsAnggota(1003, 3);
        $this->actingAsAnggota(1004, 4);

        DB::table('p_anggota')->where('p_anggota_id', 1003)->update(['is_registered' => false]);
        DB::table('p_anggota')->where('p_anggota_id', 1004)->update([
            'valid_to' => now()->subDay()->toDateString(),
        ]);

        $contentType = new ContentTypeModels([
            'content_code' => 'berita',
            'content_name' => 'Berita',
        ]);
        $contentType->forceFill(['p_content_type_id' => 1]);
        $contentType->exists = true;

        $content = new ContentModels([
            'p_content_type_id' => 1,
            'content_title' => 'Kegiatan Tahunan KKBA',
            'content_text' => '<p>Informasi terbaru untuk seluruh anggota.</p>',
        ]);
        $content->forceFill(['t_content_id' => '01KCONTENT0000000000000001']);
        $content->exists = true;
        $content->setRelation('contentType', $contentType);

        $service = app(ContentPublishedNotificationService::class);

        $this->assertSame(2, $service->contentCreated($content));
        $this->assertSame(2, $service->contentCreated($content));
        $this->assertDatabaseCount('t_notifications', 2);

        $notifications = MemberNotification::query()->orderBy('p_anggota_id')->get();
        $this->assertSame([1001, 1002], $notifications->pluck('p_anggota_id')->all());
        $this->assertTrue($notifications->every(
            fn (MemberNotification $notification): bool => $notification->type === 'content_published'
                && $notification->data['target'] === 'content_detail'
                && $notification->data['t_content_id'] === $content->t_content_id
                && $notification->data['content_type'] === 'berita'
        ));
    }

    public function test_create_page_triggers_content_notification_service(): void
    {
        Storage::fake('kkba_simpin');

        $contentTypeMigration = require database_path('migrations/2025_04_26_035550_create_p_content_type_table.php');
        $contentMigration = require database_path('migrations/2025_04_26_035609_create_t_content_table.php');
        $contentTypeMigration->up();
        $contentMigration->up();

        DB::table('p_content_type')->insert([
            'p_content_type_id' => 1,
            'content_code' => 'berita',
            'content_name' => 'Berita',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->mock(ContentPublishedNotificationService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('contentCreated')
                ->once()
                ->withArgs(fn (ContentModels $content): bool => $content->content_title === 'Berita Baru')
                ->andReturn(0);
        });

        Livewire::test(KontenCreate::class)
            ->set('p_content_type_id', 1)
            ->set('content_title', 'Berita Baru')
            ->set('content_text', 'Isi berita baru.')
            ->set('valid_from', now()->toDateString())
            ->call('saveInsert')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('t_content', [
            'content_title' => 'Berita Baru',
            'content_text' => 'Isi berita baru.',
        ]);
    }
}

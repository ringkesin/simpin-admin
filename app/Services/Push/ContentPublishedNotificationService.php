<?php

namespace App\Services\Push;

use App\Models\Main\ContentModels;
use App\Models\Master\AnggotaModels;
use Illuminate\Support\Str;

class ContentPublishedNotificationService
{
    public function __construct(private readonly NotificationPublisher $publisher) {}

    public function contentCreated(ContentModels $content): int
    {
        $recipientCount = 0;
        $today = now()->toDateString();
        $content->loadMissing('contentType');

        AnggotaModels::query()
            ->where('is_registered', true)
            ->whereNotNull('user_id')
            ->whereDate('valid_from', '<=', $today)
            ->where(function ($query) use ($today): void {
                $query->whereNull('valid_to')->orWhereDate('valid_to', '>=', $today);
            })
            ->whereHas('userId', function ($query) use ($today): void {
                $query->where(function ($query) use ($today): void {
                    $query->whereNull('valid_from')->orWhereDate('valid_from', '<=', $today);
                })->where(function ($query) use ($today): void {
                    $query->whereNull('valid_until')->orWhereDate('valid_until', '>=', $today);
                });
            })
            ->select(['p_anggota_id', 'user_id'])
            ->chunkById(200, function ($members) use ($content, &$recipientCount): void {
                foreach ($members as $member) {
                    $userId = (int) $member->user_id;

                    $this->publisher->publishToUser(
                        userId: $userId,
                        anggotaId: (int) $member->p_anggota_id,
                        eventId: 'simpin:content:'.$content->t_content_id.':published:user:'.$userId,
                        type: 'content_published',
                        title: Str::limit('Konten Baru: '.$content->content_title, 120, ''),
                        body: $this->body($content),
                        data: array_filter([
                            'target' => 'content_detail',
                            't_content_id' => (string) $content->t_content_id,
                            'p_content_type_id' => (string) $content->p_content_type_id,
                            'content_type' => $content->contentType?->content_code,
                        ], fn ($value): bool => $value !== null && $value !== ''),
                    );

                    $recipientCount++;
                }
            }, 'p_anggota_id');

        return $recipientCount;
    }

    private function body(ContentModels $content): string
    {
        $plainText = html_entity_decode(strip_tags((string) $content->content_text));
        $plainText = preg_replace('/\s+/', ' ', trim($plainText)) ?? '';

        return $plainText !== ''
            ? Str::limit($plainText, 200)
            : 'Konten terbaru telah tersedia di aplikasi KKBA.';
    }
}

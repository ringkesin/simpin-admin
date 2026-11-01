<?php

namespace App\Services\Push;

use App\Models\Master\AnggotaModels;
use App\Models\MemberNotification;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

use function Illuminate\Support\defer;

class NotificationPublisher
{
    /**
     * @param  array<string, string>  $data
     */
    public function publishToAnggota(
        int $anggotaId,
        string $eventId,
        string $type,
        string $title,
        string $body,
        array $data,
    ): MemberNotification {
        $userId = AnggotaModels::query()
            ->whereKey($anggotaId)
            ->value('user_id');

        return $this->publish(
            anggotaId: $anggotaId,
            userId: $userId !== null ? (int) $userId : null,
            eventId: $eventId,
            type: $type,
            title: $title,
            body: $body,
            data: $data,
        );
    }

    /**
     * @param  array<string, string>  $data
     */
    public function publishToUser(
        int $userId,
        string $eventId,
        string $type,
        string $title,
        string $body,
        array $data,
        ?int $anggotaId = null,
    ): MemberNotification {
        return $this->publish(
            anggotaId: $anggotaId,
            userId: $userId,
            eventId: $eventId,
            type: $type,
            title: $title,
            body: $body,
            data: $data,
        );
    }

    /**
     * @param  array<string, string>  $data
     */
    private function publish(
        ?int $anggotaId,
        ?int $userId,
        string $eventId,
        string $type,
        string $title,
        string $body,
        array $data,
    ): MemberNotification {
        if ($anggotaId === null && $userId === null) {
            throw new InvalidArgumentException('Notifikasi harus memiliki penerima.');
        }

        $notification = MemberNotification::query()->firstOrCreate(
            [
                'source' => 'simpin',
                'event_id' => $eventId,
            ],
            [
                'p_anggota_id' => $anggotaId,
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'occurred_at' => now(),
            ],
        );

        if ($notification->wasRecentlyCreated && config('push.delivery_enabled')) {
            $notificationId = $notification->id;

            DB::afterCommit(function () use ($notificationId): void {
                defer(
                    fn () => app(PushDeliveryService::class)->deliver($notificationId),
                    'push-notification:'.$notificationId,
                );
            });
        }

        return $notification;
    }
}

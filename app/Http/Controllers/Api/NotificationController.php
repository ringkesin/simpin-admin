<?php

namespace App\Http\Controllers\Api;

use App\Models\MemberNotification;
use App\Services\Push\NotificationNavigationResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function __construct(private readonly NotificationNavigationResolver $navigation) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'status' => ['sometimes', 'string', 'in:all,read,unread'],
        ]);

        $query = $this->recipientQuery($request);

        if (($validated['status'] ?? 'all') === 'read') {
            $query->whereNotNull('read_at');
        } elseif (($validated['status'] ?? 'all') === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query
            ->latest('created_at')
            ->latest('id')
            ->paginate($validated['per_page'] ?? 20)
            ->through(fn (MemberNotification $notification): array => $this->serialize($notification));

        return $this->sendResponse(
            ['notifications' => $notifications],
            'Daftar notifikasi berhasil diambil.'
        );
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $this->recipientQuery($request)
            ->whereNull('read_at')
            ->count();

        return $this->sendResponse(
            ['unread_count' => $count],
            'Jumlah notifikasi belum dibaca berhasil diambil.'
        );
    }

    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $notification = $this->recipientQuery($request)
            ->find($notificationId);

        if (! $notification) {
            return $this->sendError('Notifikasi tidak ditemukan.', [], 404);
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return $this->sendResponse(
            ['notification' => $this->serialize($notification->fresh())],
            'Notifikasi berhasil ditandai sudah dibaca.'
        );
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $updatedCount = $this->recipientQuery($request)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->sendResponse(
            ['updated_count' => $updatedCount],
            'Semua notifikasi berhasil ditandai sudah dibaca.'
        );
    }

    public function destroy(Request $request, string $notificationId): JsonResponse
    {
        $deletedCount = $this->recipientQuery($request)
            ->whereKey($notificationId)
            ->update(['inbox_deleted_at' => now()]);

        if ($deletedCount === 0) {
            return $this->sendError('Notifikasi tidak ditemukan.', [], 404);
        }

        return $this->sendResponse(
            ['deleted_count' => $deletedCount],
            'Notifikasi berhasil dihapus.'
        );
    }

    public function deleteBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'notification_ids' => ['required', 'array', 'min:1', 'max:100'],
            'notification_ids.*' => ['required', 'ulid', 'distinct'],
        ]);

        $deletedCount = $this->recipientQuery($request)
            ->whereIn('id', $validated['notification_ids'])
            ->update(['inbox_deleted_at' => now()]);

        return $this->sendResponse(
            ['deleted_count' => $deletedCount],
            'Notifikasi terpilih berhasil dihapus.'
        );
    }

    public function deleteAll(Request $request): JsonResponse
    {
        $deletedCount = $this->recipientQuery($request)
            ->update(['inbox_deleted_at' => now()]);

        return $this->sendResponse(
            ['deleted_count' => $deletedCount],
            'Semua notifikasi berhasil dihapus.'
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function serialize(MemberNotification $notification): array
    {
        $timezone = config('app.timezone');

        return [
            'id' => $notification->id,
            'source' => $notification->source,
            'type' => $notification->type,
            'title' => $notification->title,
            'body' => $notification->body,
            'data' => $notification->data,
            'navigation' => $this->navigation->resolve($notification),
            'occurred_at' => $notification->occurred_at?->timezone($timezone)->toIso8601String(),
            'read_at' => $notification->read_at?->timezone($timezone)->toIso8601String(),
            'created_at' => $notification->created_at?->timezone($timezone)->toIso8601String(),
        ];
    }

    private function anggotaId(Request $request): ?int
    {
        $anggota = $request->user()?->anggota;

        return $anggota?->exists ? (int) $anggota->p_anggota_id : null;
    }

    private function recipientQuery(Request $request): Builder
    {
        $userId = (int) $request->user()->id;
        $anggotaId = $request->user()->tokenCan('state:anggota')
            ? $this->anggotaId($request)
            : null;

        return MemberNotification::query()
            ->whereNull('inbox_deleted_at')
            ->where(function (Builder $query) use ($userId, $anggotaId): void {
                $query->where('user_id', $userId);

                if ($anggotaId !== null) {
                    $query->orWhere(function (Builder $query) use ($anggotaId): void {
                        $query->whereNull('user_id')
                            ->where('p_anggota_id', $anggotaId);
                    });
                }
            });
    }
}

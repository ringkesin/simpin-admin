<?php

namespace App\Http\Controllers\Api;

use App\Models\MemberNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $anggotaId = $this->anggotaId($request);

        if ($anggotaId === null) {
            return $this->sendError('Data anggota tidak ditemukan.', [], 404);
        }

        $validated = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $notifications = MemberNotification::query()
            ->where('p_anggota_id', $anggotaId)
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
        $anggotaId = $this->anggotaId($request);

        if ($anggotaId === null) {
            return $this->sendError('Data anggota tidak ditemukan.', [], 404);
        }

        $count = MemberNotification::query()
            ->where('p_anggota_id', $anggotaId)
            ->whereNull('read_at')
            ->count();

        return $this->sendResponse(
            ['unread_count' => $count],
            'Jumlah notifikasi belum dibaca berhasil diambil.'
        );
    }

    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $anggotaId = $this->anggotaId($request);

        if ($anggotaId === null) {
            return $this->sendError('Data anggota tidak ditemukan.', [], 404);
        }

        $notification = MemberNotification::query()
            ->where('p_anggota_id', $anggotaId)
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
        $anggotaId = $this->anggotaId($request);

        if ($anggotaId === null) {
            return $this->sendError('Data anggota tidak ditemukan.', [], 404);
        }

        $updatedCount = MemberNotification::query()
            ->where('p_anggota_id', $anggotaId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->sendResponse(
            ['updated_count' => $updatedCount],
            'Semua notifikasi berhasil ditandai sudah dibaca.'
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
}

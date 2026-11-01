<?php

namespace App\Services\Push;

use App\Models\Main\ChatConversationsModels;
use App\Models\Main\ChatModels;
use App\Models\Master\AnggotaModels;
use App\Models\User;
use Illuminate\Support\Str;

class ChatNotificationService
{
    public function __construct(private readonly NotificationPublisher $publisher) {}

    public function messageCreated(
        ChatModels $chat,
        ChatConversationsModels $message,
        bool $sentByAdmin,
    ): void {
        if ($sentByAdmin) {
            $this->notifyAnggota($chat, $message);

            return;
        }

        $this->notifyAdmins($chat, $message);
    }

    private function notifyAnggota(ChatModels $chat, ChatConversationsModels $message): void
    {
        $anggotaId = AnggotaModels::query()
            ->where('user_id', $chat->created_by)
            ->value('p_anggota_id');

        if ($anggotaId === null || $chat->created_by === null) {
            return;
        }

        $this->publisher->publishToUser(
            userId: (int) $chat->created_by,
            anggotaId: (int) $anggotaId,
            eventId: $this->eventId($message, (int) $chat->created_by),
            type: 'chat_message',
            title: 'Balasan Chat dari Admin',
            body: $this->messagePreview($message),
            data: $this->data($chat, $message, 'admin'),
        );
    }

    private function notifyAdmins(ChatModels $chat, ChatConversationsModels $message): void
    {
        $today = now()->toDateString();

        User::query()
            ->where(function ($query) use ($today): void {
                $query->whereNull('valid_from')->orWhereDate('valid_from', '<=', $today);
            })
            ->where(function ($query) use ($today): void {
                $query->whereNull('valid_until')->orWhereDate('valid_until', '>=', $today);
            })
            ->whereHas('roleUser', function ($query) use ($today): void {
                $query->where(function ($query) use ($today): void {
                    $query->whereNull('valid_from')->orWhereDate('valid_from', '<=', $today);
                })->where(function ($query) use ($today): void {
                    $query->whereNull('valid_until')->orWhereDate('valid_until', '>=', $today);
                })->whereHas('role', function ($query) use ($today): void {
                    $query->where('code', 'mobile_admin')
                        ->where(function ($query) use ($today): void {
                            $query->whereNull('valid_from')->orWhereDate('valid_from', '<=', $today);
                        })->where(function ($query) use ($today): void {
                            $query->whereNull('valid_until')->orWhereDate('valid_until', '>=', $today);
                        });
                });
            })
            ->pluck('id')
            ->each(function (int $adminUserId) use ($chat, $message): void {
                $this->publisher->publishToUser(
                    userId: $adminUserId,
                    eventId: $this->eventId($message, $adminUserId),
                    type: 'chat_message',
                    title: 'Pesan Chat dari Anggota',
                    body: $this->messagePreview($message),
                    data: $this->data($chat, $message, 'anggota'),
                );
            });
    }

    private function eventId(ChatConversationsModels $message, int $recipientUserId): string
    {
        return 'simpin:chat-message:'.$message->t_chat_conversations_id.':user:'.$recipientUserId;
    }

    /**
     * @return array<string, string>
     */
    private function data(
        ChatModels $chat,
        ChatConversationsModels $message,
        string $senderRole,
    ): array {
        return [
            'target' => 'chat',
            't_chat_id' => (string) $chat->t_chat_id,
            't_chat_conversations_id' => (string) $message->t_chat_conversations_id,
            'ticket_code' => (string) $chat->ticket_code,
            'sender_role' => $senderRole,
        ];
    }

    private function messagePreview(ChatConversationsModels $message): string
    {
        $text = preg_replace('/\s+/', ' ', trim((string) $message->message_text)) ?? '';

        return Str::limit($text, 200);
    }
}

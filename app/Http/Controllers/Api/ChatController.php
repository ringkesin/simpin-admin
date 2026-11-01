<?php

namespace App\Http\Controllers\Api;

use App\Models\Main\ChatConversationsModels;
use App\Models\Main\ChatModels;
use App\Services\Push\ChatNotificationService;
use App\Traits\MyHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends BaseController
{
    use MyHelpers;

    public function __construct(private readonly ChatNotificationService $chatNotifications) {}

    public function createTicket(Request $request)
    {
        try {
            $user = Auth::user();
            $ticket_code = '#'.$this->generateRandomCode(6);
            $p_chat_reference_table_id = $request->p_chat_reference_table_id;
            $transaction_id = $request->transaction_id;
            $subject = $request->transaction_id;

            $check_duplicate_ticket = ChatModels::where('ticket_code', $ticket_code)->count();

            if ($check_duplicate_ticket > 0) {
                return $this->sendError('Ticket Duplicate', ['error' => 'Duplikat tiket'], 409);
            }

            DB::beginTransaction();

            $chat = ChatModels::create([
                'ticket_code' => $ticket_code,
                'p_chat_reference_table_id' => $request->p_chat_reference_table_id,
                'transaction_id' => $request->transaction_id,
                'subject' => $request->subject,
                'created_by' => $user->id,
            ]);

            DB::commit();

            $chat->makeHidden([
                'created_at',
                'updated_at',
                'deleted_at',
                'updated_by',
                'deleted_by',
            ]);

            return $this->sendResponse($chat, 'Tiket Berhasil Dibuat');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function closeTicket($id)
    {
        try {
            $user = Auth::user();
            $tokenAbilities = $user->currentAccessToken()->abilities;

            if (in_array('state:admin', $tokenAbilities)) {
                $update = ChatModels::where('t_chat_id', $id)->update(['status' => 1]);

                return $this->sendResponse($update, 'Tiket Berhasil Di Close');
            } else {
                return $this->sendError('Unauthorize', ['error' => 'Anda tidak memiliki akses.'], 403);
            }
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }

    }

    public function getGrid(Request $request)
    {
        try {
            $user = Auth::user();
            $tokenAbilities = $user->currentAccessToken()->abilities;

            $page = $request->page; // default page 1 jika tidak ada
            $perPage = $request->perpage;
            $offset = ($page - 1) * $perPage;
            $data = $request->data;

            $getData = ChatModels::whereRaw('1 = 1');

            if (isset($data['ticket_code'])) {
                $getData = $getData->where('ticket_code', 'like', '%'.$data['ticket_code'].'%');
            }

            if (isset($data['p_chat_reference_table_id'])) {
                $getData = $getData->where('p_chat_reference_table_id', $data['p_chat_reference_table_id']);
                $getData = $getData->where('transaction_id', $data['transaction_id']);
            }

            if (isset($data['subject'])) {
                $getData = $getData->where('subject', $data['subject']);
            }

            if (isset($data['status'])) {
                $getData = $getData->where('status', $data['status']);
            }

            if (in_array('state:anggota', $tokenAbilities)) {
                $getData = $getData->where('created_by', $user->id);
            }

            $getData = $getData->offset($offset)
                ->limit($perPage)
                ->orderByDesc('created_at')
                ->get();

            if (count($getData) < 1) {
                return $this->sendError('Data kosong', ['error' => 'Data tidak ditemukan'], 404);
            }

            $getData->makeHidden([
                // 'created_at',
                'updated_at',
                'deleted_at',
            ]);

            $countNotRead = 0;

            foreach ($getData as $chats) {
                if (in_array('state:admin', $tokenAbilities)) {
                    $countNotRead = ChatConversationsModels::where('t_chat_id', $chats['t_chat_id'])
                        ->where('is_read_admin', 0)
                        ->count();
                } elseif (in_array('state:anggota', $tokenAbilities)) {
                    $countNotRead = ChatConversationsModels::where('t_chat_id', $chats['t_chat_id'])
                        ->where('is_read_user', 0)
                        ->count();
                }

                $chats['count_notif'] = $countNotRead;
            }

            return $this->sendResponse($getData, 'Data berhasil digenerate.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function createMessage(Request $request)
    {
        $validated = $request->validate([
            't_chat_id' => ['required', 'string'],
            'message_text' => ['required', 'string', 'max:4000'],
        ]);

        try {
            $user = $request->user();
            $isAdmin = $user->tokenCan('state:admin');
            $isAnggota = $user->tokenCan('state:anggota');

            if (! $isAdmin && ! $isAnggota) {
                return $this->sendError('Unauthorize', ['error' => 'Anda tidak memiliki akses.'], 403);
            }

            $addMessages = DB::transaction(function () use ($validated, $user, $isAdmin) {
                $chat = ChatModels::query()
                    ->lockForUpdate()
                    ->find($validated['t_chat_id']);

                if (! $chat) {
                    return null;
                }

                if (! $isAdmin && (int) $chat->created_by !== (int) $user->id) {
                    return false;
                }

                $message = ChatConversationsModels::create([
                    't_chat_id' => $chat->t_chat_id,
                    'message_text' => $validated['message_text'],
                    'is_read_user' => ! $isAdmin,
                    'is_read_admin' => $isAdmin,
                    'created_by' => $user->id,
                ]);

                $this->chatNotifications->messageCreated($chat, $message, $isAdmin);

                return $message;
            });

            if ($addMessages === null) {
                return $this->sendError('Data tidak ditemukan.', ['error' => 'Tiket chat tidak ditemukan.'], 404);
            }

            if ($addMessages === false) {
                return $this->sendError('Unauthorize', ['error' => 'Anda tidak memiliki akses ke tiket ini.'], 403);
            }

            return $this->sendResponse($addMessages, 'Pesan Dikirim.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }

    public function openMessage($chatid)
    {
        try {
            $user = Auth::user();
            $tokenAbilities = $user->currentAccessToken()->abilities;

            $getMessages = ChatConversationsModels::with(['createdBy'])
                ->where('t_chat_id', $chatid)
                ->orderByDesc('created_at')
                ->get();

            if (empty($getMessages)) {
                return $this->sendResponse($getMessages, 'Pesan Dibuka.');
            }

            $updateIsRead = ChatConversationsModels::where('t_chat_id', $chatid);

            if (in_array('state:admin', $tokenAbilities)) {
                $updateIsRead = $updateIsRead->where('is_read_admin', 0)
                    ->update(['is_read_admin' => 1]);

            } elseif (in_array('state:anggota', $tokenAbilities)) {
                $updateIsRead = $updateIsRead->where('is_read_user', 0)
                    ->update(['is_read_user' => 1]);
            }

            return $this->sendResponse($getMessages, 'Pesan Dibuka.');
        } catch (\Exception $e) {
            return $this->sendError('Oopsie, Terjadi kesalahan.', ['error' => $e->getMessage()], 500);
        }
    }
}

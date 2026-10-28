<?php

namespace App\Http\Controllers\Api;

use App\Models\MemberNotification;
use App\Services\Push\PushDeliveryService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function Illuminate\Support\defer;

class InternalNotificationController extends BaseController
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_id' => ['required', 'string', 'max:255'],
            'recipient_id' => [
                'required',
                'integer',
                Rule::exists('p_anggota', 'p_anggota_id')->whereNull('deleted_at'),
            ],
            'type' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:500'],
            'data' => ['required', 'array'],
            'occurred_at' => ['required', 'date'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            if ($request->header('X-KKBA-Event-Id') !== $request->input('event_id')) {
                $validator->errors()->add('event_id', 'Event ID header dan payload harus sama.');
            }

            if ($this->providerPayloadSize($request->all()) > config('push.max_provider_payload_bytes')) {
                $validator->errors()->add('data', 'Payload push melebihi batas 4096 byte.');
            }
        });

        $validated = $validator->validate();
        $source = 'mart';

        $existing = MemberNotification::query()
            ->where('source', $source)
            ->where('event_id', $validated['event_id'])
            ->first();

        if ($existing) {
            return $this->response($existing, true, 200);
        }

        try {
            $notification = DB::transaction(fn (): MemberNotification => MemberNotification::create([
                'p_anggota_id' => $validated['recipient_id'],
                'source' => $source,
                'event_id' => $validated['event_id'],
                'type' => $validated['type'],
                'title' => $validated['title'],
                'body' => $validated['body'],
                'data' => $validated['data'],
                'occurred_at' => $validated['occurred_at'],
            ]));
        } catch (QueryException $exception) {
            $notification = MemberNotification::query()
                ->where('source', $source)
                ->where('event_id', $validated['event_id'])
                ->first();

            if (! $notification) {
                throw $exception;
            }

            return $this->response($notification, true, 200);
        }

        if (config('push.delivery_enabled')) {
            $notificationId = $notification->id;

            defer(
                fn () => app(PushDeliveryService::class)->deliver($notificationId),
                'push-notification:'.$notificationId
            );
        }

        return $this->response($notification, false, 201);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function providerPayloadSize(array $payload): int
    {
        $data = collect($payload['data'] ?? [])
            ->map(fn ($value): string => $this->stringify($value))
            ->all();

        $providerPayload = [
            'notification' => [
                'title' => (string) ($payload['title'] ?? ''),
                'body' => (string) ($payload['body'] ?? ''),
            ],
            'data' => array_merge($data, [
                'notification_id' => str_repeat('0', 26),
                'source' => 'mart',
                'type' => (string) ($payload['type'] ?? ''),
            ]),
        ];

        return strlen((string) json_encode($providerPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function stringify(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function response(MemberNotification $notification, bool $duplicate, int $status): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'notification_id' => $notification->id,
                'duplicate' => $duplicate,
            ],
            'message' => $duplicate
                ? 'Event notifikasi sudah pernah diterima.'
                : 'Notifikasi berhasil dibuat.',
        ], $status);
    }
}

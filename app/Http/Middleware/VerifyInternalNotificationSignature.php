<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyInternalNotificationSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $rawBody = $request->getContent();

        if (strlen($rawBody) > config('push.internal.max_payload_bytes')) {
            return $this->error('Payload notifikasi terlalu besar.', 422);
        }

        $service = (string) $request->header('X-KKBA-Service');
        $timestamp = (string) $request->header('X-KKBA-Timestamp');
        $eventId = (string) $request->header('X-KKBA-Event-Id');
        $signature = (string) $request->header('X-KKBA-Signature');
        $expectedService = (string) config('push.internal.mart_client_id');
        $secret = (string) config('push.internal.mart_secret');

        if ($secret === '') {
            report(new \RuntimeException('MART_NOTIFICATION_SECRET belum dikonfigurasi.'));

            return $this->error('Layanan notifikasi belum dikonfigurasi.', 503);
        }

        if ($service === '' || ! hash_equals($expectedService, $service)) {
            return $this->error('Service notifikasi tidak valid.', 401);
        }

        if (! ctype_digit($timestamp)
            || abs(now()->timestamp - (int) $timestamp) > config('push.internal.allowed_skew_seconds')) {
            return $this->error('Timestamp notifikasi tidak valid.', 401);
        }

        if ($eventId === '' || strlen($eventId) > 255 || ! preg_match('/^[a-f0-9]{64}$/', $signature)) {
            return $this->error('Signature notifikasi tidak valid.', 401);
        }

        $expectedSignature = hash_hmac(
            'sha256',
            $timestamp."\n".$eventId."\n".$rawBody,
            $secret
        );

        if (! hash_equals($expectedSignature, $signature)) {
            return $this->error('Signature notifikasi tidak valid.', 401);
        }

        return $next($request);
    }

    private function error(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}

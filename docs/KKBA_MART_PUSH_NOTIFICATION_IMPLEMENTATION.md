# Instruksi Implementasi Push Notification untuk Backend KKBA Mart

Dokumen ini ditujukan kepada AI/developer yang akan mengimplementasikan integrasi push notification di repository backend KKBA Mart.

## 1. Tujuan

Backend KKBA Mart harus mengirim event bisnis ke SimPin ketika status transaksi Mart berubah. SimPin kemudian akan:

- memvalidasi signature HMAC dari KKBA Mart;
- menyimpan notifikasi ke inbox anggota;
- menjaga idempotensi event;
- mencari perangkat aktif milik anggota;
- mengirim push melalui Expo atau FCM;
- menangani token perangkat yang tidak valid;
- menyediakan daftar notifikasi untuk KKBA Mobile.

KKBA Mart berperan sebagai **event producer**. SimPin berperan sebagai **notification gateway**.

```text
KKBA Mart
   │
   │ HMAC-signed HTTPS event
   ▼
SimPin POST /api/internal/notifications
   ├── Inbox anggota
   ├── Expo Push Service
   └── FCM HTTP v1
          │
          ▼
      KKBA Mobile
```

## 2. Keputusan arsitektur yang wajib diikuti

AI/developer KKBA Mart harus mengikuti ketentuan berikut:

1. Jangan mengirim langsung ke Expo atau Firebase dari KKBA Mart.
2. Jangan menyimpan Expo Push Token, FCM token, Firebase service account, atau Expo access token di KKBA Mart.
3. Jangan membuat tabel perangkat push baru di KKBA Mart.
4. Jangan menduplikasi inbox notifikasi SimPin di KKBA Mart kecuali Mart memang sudah mempunyai notification center terpisah untuk kebutuhan lain.
5. Gunakan `p_anggota_id` SimPin sebagai `recipient_id`, bukan `users.id`, nomor anggota, email, atau ID customer internal Mart.
6. Kirim event hanya setelah transaksi database bisnis Mart berhasil commit.
7. Kegagalan mengirim notifikasi tidak boleh menggagalkan transaksi order/pembayaran yang sudah berhasil.
8. Gunakan event ID deterministik dan unik agar retry tidak membuat push duplikat.
9. Secret HMAC hanya disimpan di environment/secret manager, bukan repository.
10. Gunakan HTTPS untuk seluruh environment selain pengembangan lokal.

## 3. Endpoint tujuan di SimPin

```http
POST {SIMPIN_BASE_URL}/api/internal/notifications
Content-Type: application/json
Accept: application/json
X-KKBA-Service: mart
X-KKBA-Timestamp: {unix_timestamp_seconds}
X-KKBA-Event-Id: {event_id}
X-KKBA-Signature: {lowercase_hex_hmac_sha256}
```

Endpoint ini bukan endpoint bearer token. Autentikasi dilakukan dengan HMAC pada raw request body.

## 4. Konfigurasi environment KKBA Mart

Tambahkan konfigurasi berikut ke `.env.example` di KKBA Mart:

```env
# SimPin Notification Gateway
SIMPIN_NOTIFICATION_ENABLED=false
SIMPIN_NOTIFICATION_BASE_URL=https://simpin.example.com
SIMPIN_NOTIFICATION_ENDPOINT=/api/internal/notifications
SIMPIN_NOTIFICATION_CLIENT_ID=mart
SIMPIN_NOTIFICATION_SECRET=
SIMPIN_NOTIFICATION_CONNECT_TIMEOUT=2
SIMPIN_NOTIFICATION_TIMEOUT=5
SIMPIN_NOTIFICATION_RETRY_TIMES=3
SIMPIN_NOTIFICATION_RETRY_DELAY_MS=500
```

Ketentuan konfigurasi:

- `SIMPIN_NOTIFICATION_SECRET` harus sama persis dengan `MART_NOTIFICATION_SECRET` di environment SimPin.
- `SIMPIN_NOTIFICATION_CLIENT_ID` harus sama dengan `MART_NOTIFICATION_CLIENT_ID` di SimPin. Nilai default saat ini adalah `mart`.
- `SIMPIN_NOTIFICATION_ENABLED` mengontrol pengiriman event keluar dari Mart, sedangkan `PUSH_DELIVERY_ENABLED` di SimPin mengontrol pengiriman dari SimPin ke Expo/FCM.
- Gunakan secret berbeda untuk local, staging, dan production.
- Jangan menambahkan nilai secret production ke `.env.example`.
- Secret sebaiknya berupa random string minimal 32 byte.
- Pastikan clock server Mart tersinkron melalui NTP karena SimPin hanya menerima timestamp dalam toleransi ±300 detik.

Contoh membuat secret:

```bash
openssl rand -hex 32
```

Nilai hasil perintah tersebut dipasang pada:

```text
KKBA Mart: SIMPIN_NOTIFICATION_SECRET
SimPin:    MART_NOTIFICATION_SECRET
```

## 5. Contoh file konfigurasi Laravel

Jika KKBA Mart menggunakan Laravel, buat file `config/simpin-notifications.php`:

```php
<?php

return [
    'enabled' => (bool) env('SIMPIN_NOTIFICATION_ENABLED', false),
    'base_url' => rtrim((string) env('SIMPIN_NOTIFICATION_BASE_URL', ''), '/'),
    'endpoint' => env('SIMPIN_NOTIFICATION_ENDPOINT', '/api/internal/notifications'),
    'client_id' => env('SIMPIN_NOTIFICATION_CLIENT_ID', 'mart'),
    'secret' => env('SIMPIN_NOTIFICATION_SECRET'),
    'connect_timeout' => (int) env('SIMPIN_NOTIFICATION_CONNECT_TIMEOUT', 2),
    'timeout' => (int) env('SIMPIN_NOTIFICATION_TIMEOUT', 5),
    'retry_times' => (int) env('SIMPIN_NOTIFICATION_RETRY_TIMES', 3),
    'retry_delay_ms' => (int) env('SIMPIN_NOTIFICATION_RETRY_DELAY_MS', 500),
];
```

Sesudah mengubah environment production:

```bash
php artisan optimize:clear
php artisan config:cache
```

## 6. Kontrak payload

Contoh payload event perubahan status order:

```json
{
  "event_id": "mart:cart:01KABCDEF1234567890:confirmed",
  "recipient_id": 1233,
  "type": "order_status",
  "title": "Pesanan dikonfirmasi",
  "body": "Pesanan Anda telah dikonfirmasi dan sedang diproses.",
  "data": {
    "source": "mart",
    "target": "order_detail",
    "cart_mobile_id": "01KABCDEF1234567890",
    "status": "confirmed"
  },
  "occurred_at": "2026-11-02T10:30:00+07:00"
}
```

### Validasi field

| Field | Ketentuan |
|---|---|
| `event_id` | Wajib, string, maksimum 255 karakter dan sama dengan header `X-KKBA-Event-Id` |
| `recipient_id` | Wajib, integer, berisi `p_anggota_id` yang valid dan tidak dihapus di SimPin |
| `type` | Wajib, string, maksimum 100 karakter |
| `title` | Wajib, string, maksimum 120 karakter |
| `body` | Wajib, string, maksimum 500 karakter |
| `data` | Wajib berupa object/array JSON |
| `occurred_at` | Wajib berupa datetime ISO 8601 dengan timezone |

Ketentuan ukuran:

- raw HTTP body maksimum 32 KiB;
- payload provider push maksimum 4 KiB;
- jangan mengirim object transaksi lengkap di dalam `data`;
- kirim hanya ID, status, dan parameter navigasi yang dibutuhkan aplikasi.

## 7. Identitas penerima

`recipient_id` selalu berarti:

```text
p_anggota.p_anggota_id di database SimPin
```

Jangan mengirim:

- `users.id` SimPin;
- user ID Mart;
- customer ID Mart;
- nomor anggota;
- email;
- nomor telepon.

Jika tabel transaksi Mart belum menyimpan `p_anggota_id`, AI/developer harus terlebih dahulu menentukan mapping yang stabil antara customer Mart dan anggota SimPin. Jangan menebak ID penerima.

Contoh valid:

```json
{
  "recipient_id": 1233
}
```

## 8. Pembuatan event ID

Event ID harus unik untuk satu kejadian bisnis dan tetap sama ketika request yang sama di-retry.

Format yang direkomendasikan:

```text
mart:cart:{cart_mobile_id}:{status}
```

Contoh:

```text
mart:cart:01KABCDEF1234567890:confirmed
mart:cart:01KABCDEF1234567890:processing
mart:cart:01KABCDEF1234567890:ready
mart:cart:01KABCDEF1234567890:completed
mart:cart:01KABCDEF1234567890:cancelled
```

Jika satu status dapat terjadi lebih dari sekali, tambahkan ID transition/outbox yang stabil:

```text
mart:cart:{cart_mobile_id}:{status}:{transition_id}
```

Jangan menggunakan timestamp baru atau UUID acak setiap retry karena akan membuat SimPin menganggap retry sebagai notifikasi baru.

## 9. Pembuatan signature HMAC

Canonical string yang ditandatangani adalah:

```text
{timestamp}\n{event_id}\n{raw_json_body}
```

Contoh PHP:

```php
$rawBody = json_encode(
    $payload,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
);

$timestamp = (string) now()->timestamp;

$signature = hash_hmac(
    'sha256',
    $timestamp."\n".$payload['event_id']."\n".$rawBody,
    config('simpin-notifications.secret'),
);
```

Signature harus:

- memakai algoritma HMAC SHA-256;
- berbentuk 64 karakter lowercase hexadecimal;
- dihitung dari raw JSON yang benar-benar dikirim;
- tidak dihitung ulang dari JSON yang sudah berubah urutan/formatnya.

### Kesalahan implementasi yang harus dihindari

Jangan menandatangani `$rawBody`, lalu mengirim menggunakan serializer lain:

```php
// Salah: Http::post() dapat melakukan serialisasi ulang payload.
$signature = sign(json_encode($payload));
Http::post($url, $payload);
```

Kirim raw body yang sama persis:

```php
Http::withHeaders($headers)
    ->withBody($rawBody, 'application/json')
    ->post($url);
```

## 10. Contoh service client Laravel

Buat service terpisah, misalnya:

```text
app/Services/SimPin/SimPinNotificationClient.php
```

Contoh implementasi dasar:

```php
<?php

namespace App\Services\SimPin;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SimPinNotificationClient
{
    /**
     * @param array<string, mixed> $payload
     */
    public function send(array $payload): ?Response
    {
        if (! config('simpin-notifications.enabled')) {
            return null;
        }

        $baseUrl = config('simpin-notifications.base_url');
        $endpoint = config('simpin-notifications.endpoint');
        $clientId = config('simpin-notifications.client_id');
        $secret = config('simpin-notifications.secret');

        if (! is_string($baseUrl) || $baseUrl === ''
            || ! is_string($secret) || $secret === '') {
            throw new RuntimeException('Konfigurasi notifikasi SimPin belum lengkap.');
        }

        $rawBody = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );
        $timestamp = (string) now()->timestamp;
        $eventId = (string) $payload['event_id'];
        $signature = hash_hmac(
            'sha256',
            $timestamp."\n".$eventId."\n".$rawBody,
            $secret,
        );

        return Http::acceptJson()
            ->connectTimeout(config('simpin-notifications.connect_timeout'))
            ->timeout(config('simpin-notifications.timeout'))
            ->withHeaders([
                'X-KKBA-Service' => $clientId,
                'X-KKBA-Timestamp' => $timestamp,
                'X-KKBA-Event-Id' => $eventId,
                'X-KKBA-Signature' => $signature,
            ])
            ->withBody($rawBody, 'application/json')
            ->post($baseUrl.$endpoint);
    }
}
```

Implementasi production harus menambahkan:

- DTO atau validator payload;
- klasifikasi error permanen dan sementara;
- logging tanpa membocorkan secret atau signature;
- retry terkontrol;
- metrik/monitoring;
- pengiriman setelah commit.

## 11. Contoh notification service untuk status order

Buat service/domain listener yang mengubah status bisnis menjadi payload notifikasi.

```php
<?php

namespace App\Services\Notifications;

use App\Models\Cart;
use App\Services\SimPin\SimPinNotificationClient;

class OrderStatusNotificationService
{
    public function __construct(
        private readonly SimPinNotificationClient $client,
    ) {}

    public function send(Cart $cart, string $status, string $title, string $body): void
    {
        $this->client->send([
            'event_id' => "mart:cart:{$cart->cart_mobile_id}:{$status}",
            'recipient_id' => (int) $cart->p_anggota_id,
            'type' => 'order_status',
            'title' => $title,
            'body' => $body,
            'data' => [
                'source' => 'mart',
                'target' => 'order_detail',
                'cart_mobile_id' => (string) $cart->cart_mobile_id,
                'status' => $status,
            ],
            'occurred_at' => now()->toIso8601String(),
        ]);
    }
}
```

Nama model dan field pada contoh harus disesuaikan dengan schema aktual KKBA Mart. Jangan membuat asumsi nama tabel tanpa memeriksa repository Mart.

## 12. Kapan event dikirim

Kirim event hanya saat status benar-benar berubah, bukan setiap model disimpan.

Contoh event yang direkomendasikan:

| Kejadian Mart | `type` | `status` | Contoh title |
|---|---|---|---|
| Order berhasil dibuat | `order_status` | `created` | Pesanan berhasil dibuat |
| Order dikonfirmasi admin/merchant | `order_status` | `confirmed` | Pesanan dikonfirmasi |
| Pembayaran terverifikasi | `order_status` | `paid` | Pembayaran terverifikasi |
| Pesanan sedang diproses | `order_status` | `processing` | Pesanan sedang diproses |
| Pesanan siap diambil/dikirim | `order_status` | `ready` | Pesanan siap |
| Pesanan dikirim | `order_status` | `shipped` | Pesanan sedang dikirim |
| Pesanan selesai | `order_status` | `completed` | Pesanan selesai |
| Pesanan dibatalkan | `order_status` | `cancelled` | Pesanan dibatalkan |
| Pesanan ditolak | `order_status` | `rejected` | Pesanan ditolak |
| Dana dikembalikan | `order_status` | `refunded` | Pengembalian dana diproses |

Sesuaikan daftar tersebut dengan state machine/status yang benar-benar ada di KKBA Mart. Jangan membuat status baru hanya untuk notifikasi.

Hindari spam:

- jangan kirim saat admin hanya mengedit catatan internal;
- jangan kirim saat model disimpan tanpa perubahan status;
- jangan kirim event duplikat untuk status yang sama;
- jangan kirim informasi sensitif di title/body lock screen.

## 13. Pengiriman setelah transaksi commit

Jangan melakukan HTTP request ke SimPin di dalam transaksi database utama Mart.

Pilihan yang direkomendasikan:

### Jika KKBA Mart sudah memakai queue

1. Commit transaksi order.
2. Dispatch job dengan `afterCommit()`.
3. Job mengirim event ke SimPin.
4. Terapkan retry/backoff untuk error sementara.

Contoh:

```php
SendSimPinOrderNotification::dispatch($eventPayload)->afterCommit();
```

Simpan hanya data serializable/ID yang diperlukan pada job. Jangan memasukkan request object atau model besar.

### Jika KKBA Mart belum memakai queue

Gunakan callback setelah commit dan deferred execution:

```php
use Illuminate\Support\Facades\DB;

use function Illuminate\Support\defer;

DB::afterCommit(function () use ($payload): void {
    defer(
        fn () => app(SimPinNotificationClient::class)->send($payload),
        'simpin-notification:'.$payload['event_id'],
    );
});
```

Kegagalan deferred request wajib dicatat ke log. Untuk jaminan pengiriman yang lebih kuat, queue atau transactional outbox tetap lebih disarankan.

## 14. Strategi retry

Retry hanya untuk kegagalan sementara:

- connection timeout;
- request timeout;
- HTTP `408`;
- HTTP `429`;
- HTTP `500`–`599`.

Jangan melakukan retry otomatis tanpa batas untuk:

- HTTP `401`: client ID, timestamp, secret, atau signature salah;
- HTTP `422`: payload atau recipient salah;
- HTTP `404`: endpoint/base URL salah;
- error validasi lokal.

Gunakan exponential backoff, misalnya:

```text
Percobaan 1: langsung
Percobaan 2: +1 detik
Percobaan 3: +5 detik
Percobaan 4: +30 detik
```

Event ID harus tetap sama pada seluruh percobaan.

Jika menerima `429`, hormati header `Retry-After` bila tersedia. SimPin saat ini membatasi sekitar 120 request per menit berdasarkan service dan IP.

## 15. Response API SimPin

### Event baru — `201 Created`

```json
{
  "success": true,
  "data": {
    "notification_id": "01K8NOTIFICATION00000000001",
    "duplicate": false
  },
  "message": "Notifikasi berhasil dibuat."
}
```

### Event duplikat — `200 OK`

```json
{
  "success": true,
  "data": {
    "notification_id": "01K8NOTIFICATION00000000001",
    "duplicate": true
  },
  "message": "Event notifikasi sudah pernah diterima."
}
```

Response `200` dengan `duplicate=true` adalah hasil sukses. Jangan mengirim ulang event tersebut.

### Signature/service/timestamp salah — `401 Unauthorized`

```json
{
  "success": false,
  "message": "Signature notifikasi tidak valid."
}
```

### Payload salah — `422 Unprocessable Entity`

```json
{
  "message": "The recipient id field is invalid.",
  "errors": {
    "recipient_id": [
      "The selected recipient id is invalid."
    ]
  }
}
```

### Gateway belum dikonfigurasi — `503 Service Unavailable`

```json
{
  "success": false,
  "message": "Layanan notifikasi belum dikonfigurasi."
}
```

## 16. Logging dan observability

Log minimal KKBA Mart harus menyimpan:

- `event_id`;
- `recipient_id`;
- `cart_mobile_id`;
- HTTP status;
- `notification_id` dari SimPin bila berhasil;
- nilai `duplicate`;
- jumlah attempt;
- durasi request;
- error category.

Jangan mencatat:

- HMAC secret;
- header signature penuh;
- Expo/FCM token;
- data pembayaran sensitif;
- data pribadi yang tidak diperlukan.

Contoh log context:

```php
Log::info('SimPin notification accepted.', [
    'event_id' => $payload['event_id'],
    'recipient_id' => $payload['recipient_id'],
    'notification_id' => $response->json('data.notification_id'),
    'duplicate' => $response->json('data.duplicate'),
    'http_status' => $response->status(),
]);
```

## 17. Pengujian otomatis yang wajib dibuat

Gunakan HTTP fake/mock. Test tidak boleh memanggil production SimPin.

### Unit test signature

- canonical string menggunakan `{timestamp}\n{event_id}\n{raw_body}`;
- signature merupakan lowercase hex sepanjang 64 karakter;
- perubahan satu byte pada body menghasilkan signature berbeda;
- raw body yang ditandatangani sama dengan raw body yang dikirim.

### Unit test payload

- `recipient_id` berasal dari `p_anggota_id`;
- title maksimum 120 karakter;
- body maksimum 500 karakter;
- `target=order_detail`;
- `cart_mobile_id` selalu tersedia;
- `occurred_at` ISO 8601;
- event ID deterministik.

### Feature/integration test

- perubahan status yang relevan mengirim tepat satu request;
- save tanpa perubahan status tidak mengirim request;
- transaksi rollback tidak mengirim request;
- response `201` dianggap sukses;
- response `200 duplicate=true` dianggap sukses;
- timeout/`429`/`5xx` dijadwalkan retry;
- `401` dan `422` tidak di-retry tanpa batas;
- integrasi disabled tidak melakukan HTTP request;
- secret tidak muncul di log;
- retry mempertahankan event ID yang sama.

Contoh assertion Laravel HTTP fake:

```php
Http::assertSent(function ($request): bool {
    $rawBody = $request->body();
    $timestamp = $request->header('X-KKBA-Timestamp')[0];
    $eventId = $request->header('X-KKBA-Event-Id')[0];
    $expected = hash_hmac(
        'sha256',
        $timestamp."\n".$eventId."\n".$rawBody,
        'test-secret',
    );

    return $request->header('X-KKBA-Service')[0] === 'mart'
        && hash_equals($expected, $request->header('X-KKBA-Signature')[0]);
});
```

## 18. Pengujian manual dengan environment staging

Gunakan anggota staging yang benar-benar ada di SimPin dan mempunyai perangkat terdaftar.

Checklist:

1. Pastikan base URL menunjuk ke SimPin staging.
2. Pastikan secret Mart dan SimPin sama.
3. Aktifkan `SIMPIN_NOTIFICATION_ENABLED=true` di Mart staging.
4. Ubah status satu order staging.
5. Pastikan Mart menerima `201`.
6. Pastikan row baru muncul di `t_notifications` SimPin dengan `source=mart`.
7. Pastikan push diterima perangkat.
8. Tekan push dan pastikan aplikasi membuka order berdasarkan `cart_mobile_id`.
9. Kirim ulang event dengan event ID yang sama.
10. Pastikan response `200 duplicate=true` dan tidak ada push kedua.

Query verifikasi di database SimPin:

```sql
SELECT
    id,
    p_anggota_id,
    source,
    event_id,
    type,
    title,
    data,
    push_attempted_at,
    push_accepted_count,
    push_failed_count,
    created_at
FROM t_notifications
WHERE source = 'mart'
ORDER BY created_at DESC
LIMIT 50;
```

## 19. Rollout yang disarankan

1. Implementasikan config, HMAC client, payload DTO, dan tests di KKBA Mart.
2. Tambahkan environment staging dengan integrasi disabled.
3. Samakan secret staging Mart dan SimPin.
4. Aktifkan integrasi staging.
5. Uji satu status order dan retry/idempotensi.
6. Pastikan KKBA Mobile sudah menangani `target=order_detail`.
7. Tambahkan secret production ke secret manager kedua service.
8. Deploy Mart production dengan `SIMPIN_NOTIFICATION_ENABLED=false`.
9. Verifikasi config dan konektivitas production.
10. Aktifkan `SIMPIN_NOTIFICATION_ENABLED=true`.
11. Pantau response code, latency, retry, dan kegagalan push.
12. Tambahkan event status secara bertahap agar tidak menghasilkan spam.

## 20. Kriteria penerimaan

Implementasi dianggap selesai jika:

- KKBA Mart tidak memiliki credential Expo/FCM;
- secret HMAC tersedia hanya melalui environment;
- event dikirim setelah transaksi commit;
- transaksi Mart tetap sukses ketika gateway notifikasi gagal;
- event ID stabil pada retry;
- `recipient_id` menggunakan `p_anggota_id` SimPin;
- raw JSON yang ditandatangani sama dengan yang dikirim;
- response `201` dan duplicate `200` diperlakukan sebagai sukses;
- retry hanya dilakukan untuk error sementara;
- perubahan status yang sama tidak menghasilkan push duplikat;
- push membuka halaman order yang benar melalui `cart_mobile_id`;
- seluruh unit/feature test integrasi lulus.

## 21. Ringkasan tugas untuk AI KKBA Mart

AI yang mengerjakan repository KKBA Mart harus:

1. Mempelajari schema order/cart dan menemukan field `p_anggota_id`, `cart_mobile_id`, serta state machine status yang sebenarnya.
2. Menambahkan konfigurasi SimPin notification gateway dan `.env.example`.
3. Membuat HMAC client yang mengirim raw JSON tanpa serialisasi ulang.
4. Membuat DTO/validator payload.
5. Menghubungkan event hanya ke transition status yang relevan.
6. Mengirim setelah database commit melalui queue job atau deferred callback.
7. Menambahkan retry/backoff dan klasifikasi error.
8. Menambahkan logging aman dan monitoring.
9. Menambahkan unit serta integration tests dengan HTTP fake.
10. Mendokumentasikan mapping status Mart ke title/body notifikasi.
11. Tidak mengubah kontrak SimPin tanpa koordinasi dengan backend SimPin dan KKBA Mobile.

# Push Notification SimPin

SimPin menyimpan inbox notifikasi anggota/admin dan mengirim push ke perangkat Android/iOS melalui Expo serta PWA melalui FCM HTTP v1.

## Konfigurasi

```env
PUSH_DELIVERY_ENABLED=false
PUSH_HTTP_TIMEOUT=5
PUSH_CONNECT_TIMEOUT=2

EXPO_PUSH_ACCESS_TOKEN=

FCM_PROJECT_ID=
FCM_SERVICE_ACCOUNT_BASE64=

MART_NOTIFICATION_CLIENT_ID=mart
MART_NOTIFICATION_SECRET=
INTERNAL_NOTIFICATION_ALLOWED_SKEW_SECONDS=300
```

`FCM_SERVICE_ACCOUNT_BASE64` berisi JSON service account yang sudah di-base64. Jangan menyimpan JSON service account, Expo token, atau secret Mart di repository.

## API KKBA Mobile

Semua endpoint berikut membutuhkan bearer token Sanctum dengan ability `state:anggota` atau `state:admin`.

### Registrasi perangkat

```http
POST /api/push/devices
```

```json
{
  "client_installation_id": "0192f416-5f3c-7d45-a8c2-5ecf465728c1",
  "provider": "expo",
  "platform": "android",
  "provider_identifier": "ExponentPushToken[...]",
  "app_version": "1.0.0"
}
```

Pasangan yang didukung adalah `expo` dengan `android`/`ios`, serta `fcm` dengan `web`. Registrasi ulang memperbarui record yang sama. Installation ID atau provider token yang berpindah akun akan dipindahkan ke anggota terbaru.

### Menonaktifkan perangkat

```http
DELETE /api/push/devices/{client_installation_id}
```

### Logout

```http
POST /api/logout
```

```json
{
  "client_installation_id": "0192f416-5f3c-7d45-a8c2-5ecf465728c1"
}
```

Installation ID sementara bersifat opsional untuk kompatibilitas aplikasi lama. Logout hanya mencabut access token yang sedang digunakan.

### Inbox

```http
GET  /api/notifications?page=1&per_page=20
GET  /api/notifications/unread-count
POST /api/notifications/{notification_id}/read
POST /api/notifications/read-all
```

`per_page` memiliki nilai awal 20 dan maksimum 100. Parameter `status` dapat diisi `all`, `read`, atau `unread`. Semua operasi dibatasi ke user yang sedang login; admin tidak dapat membaca notifikasi admin lain dan anggota tidak dapat membaca notifikasi anggota lain.

Setiap item inbox memiliki objek `navigation` untuk navigasi aplikasi:

```json
{
  "action": "open_screen",
  "is_actionable": true,
  "scope": "pinjaman",
  "target": "pinjaman_detail",
  "params": {
    "t_pinjaman_id": "987",
    "status": "approved"
  },
  "missing_params": [],
  "fallback_target": "notifications"
}
```

Target yang didukung adalah `home`, `profile`, `content`, `content_detail`, `pinjaman`, `pinjaman_detail`, `tagihan`, `tagihan_detail`, `pencairan_tabungan`, `pencairan_tabungan_detail`, `penyertaan`, `penyertaan_detail`, `perubahan_penyertaan`, `perubahan_penyertaan_detail`, `chat`, dan `order_detail`. Aplikasi membuka halaman berdasarkan `target` dan meneruskan seluruh `params`. Jika `is_actionable=false`, aplikasi tetap berada di halaman inbox melalui `fallback_target`.

## Internal API KKBA Mart

```http
POST /api/internal/notifications
Content-Type: application/json
X-KKBA-Service: mart
X-KKBA-Timestamp: 1792807200
X-KKBA-Event-Id: mart:cart:01K...:confirmed
X-KKBA-Signature: lowercase-hex-hmac-sha256
```

String yang ditandatangani harus menggunakan raw JSON body yang sama persis dengan yang dikirim:

```text
{timestamp}\n{event_id}\n{raw_json_body}
```

Contoh pembuatan signature PHP:

```php
$signature = hash_hmac(
    'sha256',
    $timestamp."\n".$eventId."\n".$rawJsonBody,
    $secret,
);
```

Payload:

```json
{
  "event_id": "mart:cart:01K...:confirmed",
  "recipient_id": 12345,
  "type": "order_status",
  "title": "Pesanan dikonfirmasi",
  "body": "Pesanan Anda telah dikonfirmasi.",
  "data": {
    "source": "mart",
    "target": "order_detail",
    "cart_mobile_id": "01K...",
    "status": "confirmed"
  },
  "occurred_at": "2026-10-24T10:00:00+07:00"
}
```

Respons `201` berarti inbox baru dibuat. Respons `200` dengan `duplicate: true` berarti event sudah pernah diproses dan push tidak dikirim ulang. Payload dibatasi 32 KiB dan timestamp hanya diterima dalam rentang 300 detik dari waktu server.

## Rollout

1. Jalankan migration biasa dengan `php artisan migrate`; jangan gunakan `migrate:fresh`.
2. Deploy dengan `PUSH_DELIVERY_ENABLED=false` dan uji HMAC/inbox.
3. Rilis registrasi perangkat serta inbox pada KKBA Mobile.
4. Konfigurasikan Expo, Firebase, dan secret Laravel Cloud.
5. Aktifkan `PUSH_DELIVERY_ENABLED=true` setelah pengujian perangkat nyata berhasil.

Pengiriman memakai Laravel `defer()` dan tidak membutuhkan `queue:work`. Kegagalan provider tidak menghapus inbox dan tidak mengubah respons sukses yang sudah diberikan kepada KKBA Mart.

## Event approval internal SimPin

SimPin otomatis membuat inbox dan menjadwalkan push setelah transaksi approval berhasil untuk event berikut:

- `anggota_approved`
- `pinjaman_approved`
- `pencairan_tabungan_approved`
- `penyertaan_approved`
- `perubahan_penyertaan_approved`

Event menggunakan `source=simpin` dan event ID deterministik berdasarkan jenis serta ID transaksi. Menyimpan approval yang sama berulang kali tidak membuat inbox atau push duplikat. Field `data.target` dan ID transaksi disediakan agar aplikasi mobile dapat membuka halaman detail yang sesuai saat notifikasi ditekan.

## Publikasi konten

Ketika admin membuat konten melalui halaman `main.konten.create`, SimPin membuat event `content_published` untuk setiap anggota terdaftar yang masih aktif dan memiliki akun user. Event menggunakan target `content_detail` dengan parameter `t_content_id`, `p_content_type_id`, dan `content_type`. Mengedit konten melalui `main.konten.edit` tidak mengirim notifikasi baru.

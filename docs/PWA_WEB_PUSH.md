# Web Push PWA KKBA — backend dan handoff frontend

Backend SimPin: `https://simpin.koperasi-abipraya.com/api`.
Frontend PWA: `https://koperasi-abipraya.com/app/`.
Backend ini mendukung Web Push standar dengan VAPID khusus anggota. Implementasi frontend dilakukan di proyek KKBA Mobile.
Inbox dan event SimPin/Mart tetap sama; native Expo/FCM tetap tersedia.

## Konfigurasi dan deployment operator

Dependency `minishlink/web-push` dikunci di Composer; memerlukan PHP 8.2+, curl, mbstring,
dan OpenSSL P-256. Gunakan runtime yang didukung seluruh dependency proyek (pembaruan lokal memakai PHP 8.4).
Deploy backend dengan `WEB_PUSH_ENABLED=false`, install dependency, lalu jalankan migration tambahan:

```sh
composer install --no-dev --optimize-autoloader
php artisan migrate --path=database/migrations/2026_11_15_000000_add_binding_id_to_push_devices_table.php --force
```

Migration hanya menambahkan UUID nullable `binding_id` ke `t_push_devices`.
Encrypted cast memerlukan APP_KEY backend yang konsisten antar-instance/deployment.
Generate pasangan VAPID sekali per environment dari terminal operator yang aman:

```sh
php artisan push:web-vapid
```

Command mencetak public/private key untuk disalin ke secret environment. Jangan jalankan di build/deployment
otomatis atau simpan output ke log CI. Jangan mengganti key pada setiap deploy; rotasi memerlukan resubscribe PWA.

```dotenv
PUSH_DELIVERY_ENABLED=true
WEB_PUSH_ENABLED=true
WEB_PUSH_VAPID_PUBLIC_KEY=<hasil-generator>
WEB_PUSH_VAPID_PRIVATE_KEY=<hasil-generator>
WEB_PUSH_VAPID_SUBJECT=mailto:<email-kontak-yang-dikelola>
```

Subject berupa alamat mailto valid atau URL HTTPS. Backend memeriksa public/private key cocok.
Private key hanya di backend, terpisah dari konfigurasi Expo, Firebase, dan passkey.
Bangun config cache sesuai prosedur deployment dan izinkan outbound HTTPS ke `*.push.apple.com`.
Implementasi lokal tidak menjalankan migration, mengubah `.env`, atau deploy production.

## Kontrak API

### GET /api/push/web/config — publik

```json
{
  "success": true,
  "data": {"enabled": true, "vapid_public_key": "<public-vapid-key>"},
  "message": "Konfigurasi Web Push berhasil diambil."
}
```

HTTP 200 dengan `enabled:false,vapid_public_key:null` jika sakelar utama/Web Push off atau konfigurasi
tidak valid/lengkap. Private key tidak dikembalikan. Cache-Control `no-store, private`; rate limit 30/menit.

### POST /api/push/devices — bearer anggota

Header: `Authorization: Bearer ...`, `Accept: application/json`, `Content-Type: application/json`.
Identitas penerima dari bearer. Admin tetap dapat mendaftarkan Expo/FCM; Web Push hanya untuk ability
`state:anggota` dengan relasi anggota nyata.

```json
{
  "client_installation_id": "577c04cf-84c4-4d6b-8175-3ae2d8d383ae",
  "provider": "webpush",
  "platform": "web",
  "app_version": "1.3.0",
  "subscription": {
    "endpoint": "https://web.push.apple.com/<subscription-path>",
    "expirationTime": null,
    "keys": {"p256dh": "<base64url-65-byte-P256-public-key>", "auth": "<base64url-16-byte-secret>"}
  }
}
```

Placeholder bukan subscription valid. Gunakan `PushSubscription.toJSON()` nyata, tanpa `provider_identifier`.
Binary memakai base64url tanpa padding. Endpoint maksimum 2048 karakter, app version maksimum 50 karakter.
HTTP 201 untuk baru, HTTP 200 untuk pembaruan:

```json
{
  "success": true,
  "data": {
    "device": {
      "id": "<device-ulid>",
      "client_installation_id": "577c04cf-84c4-4d6b-8175-3ae2d8d383ae",
      "provider": "webpush",
      "platform": "web",
      "app_version": "1.3.0",
      "is_active": true,
      "last_seen_at": "2026-11-15T10:00:00+00:00",
      "binding_id": "<server-generated-uuid>",
      "recipient_user_id": "12"
    }
  },
  "message": "Perangkat berhasil didaftarkan."
}
```

`recipient_user_id` berarti `users.id`, bukan `p_anggota_id`. Simpan metadata user/binding yang dapat dibaca
worker tanpa bearer. Gunakan binding respons terbaru: tetap saat registrasi identik; berubah saat pemilik,
instalasi, subscription, atau status nonaktif menjadi aktif berubah.
Subscription dinormalisasi dan dienkripsi; hash `sha256('webpush:' + endpoint)` menghindari duplikasi endpoint.
Endpoint yang sama dapat dipindah atomik ke pemilik/instalasi baru. Urutan JSON tidak memengaruhi hash.

401 tanpa bearer valid; 403 admin/bukan anggota; 422 validasi; 503 jika fitur belum aktif/siap.
Format error mengikuti handler API eksisting (`message`, ditambah `errors` untuk validation error).
Registrasi dan DELETE memakai `Cache-Control: no-store, private`; kegagalan registrasi tidak menghalangi login.

Allowlist awal hanya subdomain `*.push.apple.com`. Layanan push Chrome/Firefox ditolak sampai dukungan
ditambahkan eksplisit di backend. Tolak userinfo, fragment, port non-443, DNS private/reserved, dan redirect.
DNS diperiksa kembali saat pengiriman lalu dipin ke alamat tervalidasi melalui curl, tanpa proxy environment.
CORS origin PWA, Authorization/Content-Type/Accept, serta POST/DELETE telah diuji dengan preflight.

### DELETE /api/push/devices/{client_installation_id}

Bearer pemilik. HTTP 200:
`{"success":true,"data":[],"message":"Perangkat berhasil dinonaktifkan."}`.
404 boleh dianggap sudah nonaktif. Logout eksisting menerima `client_installation_id` dan menonaktifkan
instalasi sebelum mencabut token saat ini. DELETE berlaku untuk semua provider milik akun pada UUID itu;
gunakan UUID instalasi PWA yang terpisah dari aplikasi native.
Idle, token expired, atau PWA ditutup tidak menonaktifkan subscription secara otomatis.

## Envelope dan pengiriman

```json
{
  "title": "Pesanan dikonfirmasi",
  "body": "Silakan lanjutkan pembayaran.",
  "data": {
    "notification_id": "<notification-ulid>",
    "recipient_user_id": "12",
    "binding_id": "<device-binding-uuid>",
    "source": "mart",
    "type": "order_status",
    "target": "order_detail",
    "cart_mobile_id": "<cart-id>",
    "status": "confirmed"
  }
}
```

User/binding selalu diisi server, menimpa input event. Untuk Mart tanpa `user_id`, backend mengambil
user dari relasi anggota. Metadata routing sama dengan native, nilai `data` berupa string.
Status order terbaru tetap harus diambil dengan bearer aktif melalui API Mart.

Satu event menghasilkan satu Inbox. Delivery setelah commit memakai `defer()` tanpa job/scheduler baru.
Klaim `push_attempted_at` menggunakan conditional update atomik agar callback bersamaan tidak memulai
pengiriman kedua. Event Mart duplicate tidak membuat Inbox/push tambahan. Setiap provider diisolasi.

- AES128GCM, VAPID, TTL 300 detik. JSON maksimum 3000 byte sebelum enkripsi; payload berlebih gagal hanya untuk Web Push.
- Connect timeout 2 detik; request timeout 5 detik. HTTP 2xx berarti accepted, bukan bukti tampil di perangkat.
- 404/410 menonaktifkan subscription; 401/403 tidak menonaktifkan perangkat.
- Timeout/koneksi gagal, 429, 5xx: paling banyak satu retry dengan jeda default 1 detik. `Retry-After`
  numerik/tanggal dihormati sampai 2 detik; jika lebih lama, tidak dijadwalkan retry.
- Status aktif/binding diperiksa ulang sebelum setiap percobaan. Hasil binding lama tidak boleh menimpa binding baru.
- Redirect dan error lain tidak di-retry. Statistik per perangkat dan diagnosis tersanitasi, tanpa endpoint/keys/respons mentah.

Retry tidak persisten: proses yang berhenti setelah klaim tidak dipulihkan otomatis. Timeout bisa terjadi
setelah provider menerima, sehingga retry dapat menduplikasi delivery. Gunakan tag notification ID dan
deduplikasi ketukan di PWA; tidak ada jaminan exactly-once. Web Push off melewati provider ini saja.

## Instruksi untuk AI KKBA Mobile

1. Implementasikan khusus anggota web, terpisah dari Expo native. Perluas `/app/sw.js` yang sudah ada;
   pertahankan precache, scope/start_url/id `/app/`, gate Home Screen, passkey, deep link, serta larangan cache API.
2. Ambil config dengan `cache:no-store`; siapkan `serviceWorker.ready` dan public key sebelum tombol aktif.
   Minta izin/`PushManager.subscribe({userVisibleOnly:true,applicationServerKey})` langsung dari ketukan pengguna,
   bukan effect login. Setelah login, sinkronkan subscription yang sudah diizinkan.
3. Bedakan izin OS, subscription browser, dan registrasi backend. Sediakan switch/retry di Pengaturan Notifikasi
   serta tombol aktivasi Inbox. Penolakan tidak menghalangi aplikasi; jika denied, arahkan ke pengaturan iPhone.
4. Simpan UUID instalasi persisten dan gunakan respons 200/201 untuk memperbarui metadata user/binding worker.
   Jangan menyimpan bearer di service worker, Cache Storage, atau IndexedDB.
5. Handler `push` memakai `event.waitUntil`, parse defensif, dan cocokkan user **serta binding**. Jika berbeda/tidak
   tersedia, tampilkan notifikasi umum ke Inbox tanpa teks/routing akun lama. Jika sesuai, `showNotification`
   dengan ikon serta tag notification ID. Foreground refresh Inbox/unread tanpa navigasi/banner ganda.
6. `notificationclick` fokus window PWA atau buka `/app/`. Gunakan router allowlist; tolak URL arbitrer.
   Pending navigation menunggu login, kelengkapan profil, dan akses Mart. Periksa user/binding lagi saat dibuka.
7. Detail memakai bearer aktif; order memakai Mart `/api/cart/{id}`. Tangani missing ID, 404, offline, cold start,
   ketukan ganda. Mark-read/refresh badge; kegagalan mark-read tidak menghalangi detail yang diotorisasi.
8. Off/logout eksplisit menghentikan operasi lama, DELETE menggunakan bearer lama, unsubscribe, dan bersihkan
   binding/pending/notifikasi lama. Request tertunda membawa snapshot bearer/akun, tidak memakai bearer akun baru.
   Idle/menutup PWA/token expired mempertahankan subscription.
9. Bangun ulang bundle/precache/hash/paket PWA lalu impor website ke `public/app/`. Tidak perlu AAB/IPA baru;
   jangan force reload ketika pengguna sedang membayar.

## Pengujian dan tes operator

Tes otomatis memakai SQLite in-memory, key sintetis, DNS/transport palsu. Ada tes enkripsi/VAPID library nyata
tanpa koneksi push production:

```sh
php vendor/bin/phpunit tests/Feature/WebPushTest.php
```

Sesudah backend dan PWA deploy, gunakan akun uji dan iPhone iOS 16.4+ dari Home Screen. Aktifkan push,
periksa record webpush/web, picu event approval/chat/Mart terotorisasi, lalu verifikasi Inbox/statistik,
notifikasi foreground/background/closed, dan ketukan membuka detail benar setelah login.
Uji denied/off/logout, akun berbeda, serta multi-device. Accepted belum membuktikan delivery iPhone.

Tes langsung dari Tinker oleh operator: buat notifikasi akun uji memakai `NotificationPublisher::publishToAnggota`
dengan event ID unik dan target `home`, lalu panggil `app(PushDeliveryService::class)->deliver($notification->id)`.
Refresh statistik. Callback deferred berikutnya tidak mengirim ulang karena klaim atomik.
Jangan mencetak token/subscription atau mengirim ke anggota lain.

Referensi: [WebKit](https://webkit.org/blog/13878/web-push-for-web-apps-on-ios-and-ipados/),
[PushManager.subscribe](https://developer.mozilla.org/en-US/docs/Web/API/PushManager/subscribe),
[web-push-php](https://github.com/web-push-libs/web-push-php).

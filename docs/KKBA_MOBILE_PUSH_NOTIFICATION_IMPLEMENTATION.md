# Panduan Implementasi Push Notification di KKBA Mobile

Dokumen ini menjelaskan perubahan yang perlu diterapkan pada aplikasi KKBA Mobile agar mendukung:

- registrasi perangkat anggota dan admin;
- push notification Expo;
- inbox/daftar notifikasi;
- jumlah notifikasi belum dibaca;
- navigasi ke halaman spesifik ketika notifikasi ditekan;
- push chat dua arah antara anggota dan admin;
- push approval dan publikasi konten baru;
- penonaktifan perangkat ketika logout.

## 1. Ringkasan alur aplikasi

Setelah login berhasil, aplikasi harus menjalankan alur berikut:

1. Simpan bearer token hasil login.
2. Kenali role dari `data.role`:
   - `mobile_anggota`; atau
   - `mobile_admin`.
3. Minta izin notifikasi dari pengguna.
4. Ambil Expo Push Token.
5. Ambil atau buat `client_installation_id` yang permanen untuk instalasi aplikasi tersebut.
6. Daftarkan perangkat ke `POST /api/push/devices`.
7. Ambil unread count dari `GET /api/notifications/unread-count`.
8. Pasang listener untuk notifikasi foreground dan ketika notifikasi ditekan.

> **Penting untuk admin:** user dengan role `mobile_admin`, termasuk admin dengan `user_id = 1`, tetap wajib memanggil `POST /api/push/devices`. Admin biasanya tidak mempunyai `p_anggota_id`, tetapi backend sekarang mendukung registrasi perangkat berdasarkan `user_id`.

Endpoint registrasi perangkat tidak boleh dibatasi hanya untuk `mobile_anggota` pada kode aplikasi.

## 2. Environment dan autentikasi

Semua endpoint pada dokumen ini, kecuali login, membutuhkan bearer token:

```http
Authorization: Bearer {access_token}
Accept: application/json
Content-Type: application/json
```

Contoh URL menggunakan placeholder:

```text
{API_BASE_URL}/api
```

## 3. Installation ID

`client_installation_id` adalah UUID yang mengidentifikasi satu instalasi aplikasi.

Ketentuan:

- Buat UUID satu kali ketika aplikasi pertama kali dijalankan.
- Simpan secara persisten, misalnya dengan SecureStore atau penyimpanan lokal aplikasi.
- Jangan membuat UUID baru setiap login atau setiap aplikasi dibuka.
- UUID boleh digunakan kembali ketika user logout lalu user lain login pada perangkat yang sama.
- Backend akan memindahkan kepemilikan installation ID secara atomik ke user yang terakhir login.
- Jika aplikasi dihapus dan dipasang ulang, installation ID baru diperbolehkan.

Contoh nilai:

```text
0192f416-5f3c-7d45-a8c2-5ecf465728c1
```

## 4. Registrasi perangkat

Registrasi harus dijalankan setelah login untuk **anggota maupun admin**. Sebaiknya registrasi juga dijalankan kembali ketika:

- aplikasi dibuka dan user masih login;
- Expo Push Token berubah;
- aplikasi selesai diperbarui;
- user lain login pada perangkat yang sama.

### Endpoint

```http
POST /api/push/devices
```

### Request

```json
{
  "client_installation_id": "0192f416-5f3c-7d45-a8c2-5ecf465728c1",
  "provider": "expo",
  "platform": "android",
  "provider_identifier": "ExponentPushToken[xxxxxxxxxxxxxxxxxxxxxx]",
  "app_version": "1.2.0"
}
```

Nilai yang didukung:

| Field | Nilai |
|---|---|
| `provider` | `expo` untuk Android/iOS, `fcm` untuk web/PWA |
| `platform` | `android`, `ios`, atau `web` |
| `provider_identifier` | Expo Push Token atau FCM token |
| `app_version` | Versi aplikasi yang sedang dipakai |

### Response perangkat baru — `201 Created`

```json
{
  "success": true,
  "data": {
    "device": {
      "id": "01K8ABCDEF1234567890ABCDE",
      "client_installation_id": "0192f416-5f3c-7d45-a8c2-5ecf465728c1",
      "provider": "expo",
      "platform": "android",
      "app_version": "1.2.0",
      "is_active": true,
      "last_seen_at": "2026-11-01T23:25:17+07:00"
    }
  },
  "message": "Perangkat berhasil didaftarkan."
}
```

Registrasi ulang perangkat yang sama menghasilkan `200 OK` dengan pesan `Perangkat berhasil diperbarui.`

### Contoh alur TypeScript

Contoh berikut adalah pseudocode dan perlu disesuaikan dengan service API, SecureStore, serta konfigurasi Expo pada project KKBA Mobile.

```ts
async function registerPushDevice(accessToken: string) {
  const installationId = await getOrCreateInstallationId();
  const expoPushToken = await getExpoPushToken();
  const appVersion = getApplicationVersion();

  await api.post(
    "/api/push/devices",
    {
      client_installation_id: installationId,
      provider: "expo",
      platform: Platform.OS,
      provider_identifier: expoPushToken,
      app_version: appVersion,
    },
    {
      headers: { Authorization: `Bearer ${accessToken}` },
    },
  );
}
```

Jangan menggunakan kondisi seperti ini:

```ts
if (role === "mobile_anggota") {
  await registerPushDevice(token);
}
```

Gunakan anggota dan admin:

```ts
if (["mobile_anggota", "mobile_admin"].includes(role)) {
  await registerPushDevice(token);
}
```

## 5. Menonaktifkan perangkat dan logout

### Menonaktifkan perangkat tanpa logout

```http
DELETE /api/push/devices/{client_installation_id}
```

Response:

```json
{
  "success": true,
  "data": [],
  "message": "Perangkat berhasil dinonaktifkan."
}
```

### Logout

```http
POST /api/logout
```

Request:

```json
{
  "client_installation_id": "0192f416-5f3c-7d45-a8c2-5ecf465728c1"
}
```

Response:

```json
{
  "success": true,
  "data": [],
  "message": "Successfully logged out."
}
```

Saat logout, backend akan:

- menonaktifkan installation ID milik user tersebut; dan
- mencabut hanya token Sanctum yang sedang digunakan.

Urutan logout di aplikasi:

1. Kirim `POST /api/logout` beserta installation ID.
2. Setelah response diterima, hapus access token dan data session lokal.
3. Installation ID tidak perlu dihapus karena mewakili instalasi, bukan akun.

## 6. API inbox notifikasi

Endpoint inbox yang sama digunakan oleh anggota dan admin. Backend otomatis menentukan penerima berdasarkan bearer token; aplikasi tidak perlu mengirim `p_anggota_id` atau `user_id`.

### 6.1 Daftar notifikasi

```http
GET /api/notifications?page=1&per_page=20&status=all
```

Query parameter:

| Parameter | Default | Keterangan |
|---|---:|---|
| `page` | `1` | Nomor halaman |
| `per_page` | `20` | Jumlah data, maksimum `100` |
| `status` | `all` | `all`, `read`, atau `unread` |

Contoh response:

```json
{
  "success": true,
  "data": {
    "notifications": {
      "current_page": 1,
      "data": [
        {
          "id": "01K8NOTIFICATION00000000001",
          "source": "simpin",
          "type": "pinjaman_approved",
          "title": "Pinjaman Disetujui",
          "body": "Pengajuan pinjaman Anda telah disetujui.",
          "data": {
            "target": "pinjaman_detail",
            "t_pinjaman_id": "987",
            "status": "approved"
          },
          "navigation": {
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
          },
          "occurred_at": "2026-11-01T20:15:00+07:00",
          "read_at": null,
          "created_at": "2026-11-01T20:15:01+07:00"
        }
      ],
      "first_page_url": "{API_BASE_URL}/api/notifications?page=1",
      "from": 1,
      "last_page": 1,
      "last_page_url": "{API_BASE_URL}/api/notifications?page=1",
      "links": [],
      "next_page_url": null,
      "path": "{API_BASE_URL}/api/notifications",
      "per_page": 20,
      "prev_page_url": null,
      "to": 1,
      "total": 1
    }
  },
  "message": "Daftar notifikasi berhasil diambil."
}
```

### 6.2 Jumlah belum dibaca

```http
GET /api/notifications/unread-count
```

Response:

```json
{
  "success": true,
  "data": {
    "unread_count": 3
  },
  "message": "Jumlah notifikasi belum dibaca berhasil diambil."
}
```

Gunakan nilai ini untuk badge pada icon notifikasi atau bottom navigation.

### 6.3 Menandai satu notifikasi sudah dibaca

```http
POST /api/notifications/{notification_id}/read
```

Request body boleh kosong.

Response:

```json
{
  "success": true,
  "data": {
    "notification": {
      "id": "01K8NOTIFICATION00000000001",
      "source": "simpin",
      "type": "pinjaman_approved",
      "title": "Pinjaman Disetujui",
      "body": "Pengajuan pinjaman Anda telah disetujui.",
      "data": {
        "target": "pinjaman_detail",
        "t_pinjaman_id": "987",
        "status": "approved"
      },
      "navigation": {
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
      },
      "occurred_at": "2026-11-01T20:15:00+07:00",
      "read_at": "2026-11-01T20:20:00+07:00",
      "created_at": "2026-11-01T20:15:01+07:00"
    }
  },
  "message": "Notifikasi berhasil ditandai sudah dibaca."
}
```

Endpoint bersifat idempotent: memanggilnya kembali untuk notifikasi yang sama tetap menghasilkan response sukses.

### 6.4 Menandai semua sudah dibaca

```http
POST /api/notifications/read-all
```

Response:

```json
{
  "success": true,
  "data": {
    "updated_count": 3
  },
  "message": "Semua notifikasi berhasil ditandai sudah dibaca."
}
```

### 6.5 Menghapus satu notifikasi

Endpoint ini digunakan untuk aksi swipe/slide ke kiri pada satu item inbox:

```http
DELETE /api/notifications/{notification_id}
```

Response:

```json
{
  "success": true,
  "data": {
    "deleted_count": 1
  },
  "message": "Notifikasi berhasil dihapus."
}
```

Jika notifikasi bukan milik user login atau sudah tidak tersedia pada inbox, API menghasilkan `404`.

Contoh optimistic swipe:

```ts
async function deleteNotification(item: NotificationItem) {
  removeNotificationOptimistically(item.id);

  try {
    await api.delete(`/api/notifications/${item.id}`);
    decrementUnreadBadgeWhenNeeded(item);
  } catch (error) {
    restoreNotification(item);
    showDeleteError(error);
  }
}
```

### 6.6 Menghapus beberapa notifikasi

Gunakan selection mode pada inbox, lalu kirim maksimum 100 ID dalam satu request:

```http
POST /api/notifications/delete-batch
```

Request:

```json
{
  "notification_ids": [
    "01K8ABCDEF1234567890ABCDEF",
    "01K8ABCDEF1234567890ABCDEG"
  ]
}
```

Response:

```json
{
  "success": true,
  "data": {
    "deleted_count": 2
  },
  "message": "Notifikasi terpilih berhasil dihapus."
}
```

Ketentuan:

- `notification_ids` wajib berupa array;
- minimal satu ID dan maksimal 100 ID;
- setiap ID wajib berupa ULID dan tidak boleh duplikat;
- backend hanya menghapus notifikasi milik user login;
- ID milik user lain tidak dihitung dan tidak dihapus.

### 6.7 Menghapus semua notifikasi

```http
DELETE /api/notifications/all
```

Response:

```json
{
  "success": true,
  "data": {
    "deleted_count": 25
  },
  "message": "Semua notifikasi berhasil dihapus."
}
```

Tampilkan dialog konfirmasi sebelum memanggil endpoint ini. Setelah berhasil:

- kosongkan cache/list inbox lokal;
- set unread badge menjadi `0`;
- lakukan refetch halaman pertama untuk memastikan state sinkron.

Penghapusan pada ketiga endpoint bersifat logis di backend. Notifikasi hilang dari inbox dan unread count, tetapi event tetap disimpan untuk audit dan mencegah push duplikat ketika service lain melakukan retry.

## 7. Perlakuan ketika notifikasi diklik

Ada dua sumber klik yang perlu ditangani:

1. User menekan system push notification.
2. User menekan item pada halaman inbox notifikasi.

### 7.1 Klik dari halaman inbox

Urutan yang disarankan:

1. Terapkan optimistic update pada item dan badge unread.
2. Panggil `POST /api/notifications/{notification_id}/read`.
3. Baca objek `navigation` dari item.
4. Jika `navigation.is_actionable === true`, buka screen berdasarkan `navigation.target` dan kirim `navigation.params`.
5. Jika tidak actionable, arahkan ke `navigation.fallback_target`, yaitu halaman notifications.
6. Kegagalan request mark-as-read tidak perlu menghalangi navigasi, tetapi perlu dicoba ulang ketika koneksi tersedia.

Contoh:

```ts
async function onInboxNotificationPress(item: NotificationItem) {
  markAsReadOptimistically(item.id);

  api.post(`/api/notifications/${item.id}/read`).catch(() => {
    queueMarkAsReadRetry(item.id);
  });

  openNotificationTarget(item.navigation);
}
```

### 7.2 Klik dari system push notification

Push payload menyediakan data seperti berikut:

```json
{
  "notification_id": "01K8NOTIFICATION00000000001",
  "source": "simpin",
  "type": "chat_message",
  "target": "chat",
  "t_chat_id": "01K8CHAT0000000000000001",
  "t_chat_conversations_id": "01K8MESSAGE0000000000001",
  "ticket_code": "#A1B2C3",
  "sender_role": "anggota"
}
```

Urutan yang disarankan:

1. Ambil data dari `notification.request.content.data`.
2. Jika user belum login, simpan payload sebagai pending navigation dan arahkan ke login.
3. Setelah login berhasil, proses pending navigation.
4. Jika terdapat `notification_id`, panggil endpoint mark-as-read.
5. Navigasikan berdasarkan `target` dan parameter ID terkait.

Contoh listener:

```ts
const responseSubscription = Notifications.addNotificationResponseReceivedListener(
  async (response) => {
    const data = response.notification.request.content.data;

    if (!isAuthenticated()) {
      await savePendingNotificationNavigation(data);
      navigate("Login");
      return;
    }

    if (data.notification_id) {
      api.post(`/api/notifications/${data.notification_id}/read`).catch(() => undefined);
    }

    openPushTarget(data);
  },
);
```

Pastikan subscription dilepas ketika provider/root component di-unmount:

```ts
return () => responseSubscription.remove();
```

## 8. Mapping target ke halaman KKBA Mobile

Nama screen berikut adalah contoh. Sesuaikan nama route dengan React Navigation atau Expo Router yang digunakan dalam project KKBA Mobile.

| `target` | Scope | Parameter penting | Tujuan |
|---|---|---|---|
| `home` | Home | - | Halaman utama |
| `profile` | Profil | `p_anggota_id` bila tersedia | Profil anggota |
| `content` | Konten | - | Daftar konten/berita |
| `content_detail` | Konten | `t_content_id` | Detail berita/konten |
| `pinjaman` | Pinjaman | - | Daftar pinjaman |
| `pinjaman_detail` | Pinjaman | `t_pinjaman_id` | Detail pinjaman |
| `tagihan` | Tagihan | - | Daftar tagihan |
| `tagihan_detail` | Tagihan | `t_tagihan_id` | Detail tagihan |
| `pencairan_tabungan` | Pencairan | - | Daftar pencairan tabungan |
| `pencairan_tabungan_detail` | Pencairan | `t_tabungan_pengambilan_id` | Detail pencairan |
| `penyertaan` | Penyertaan | - | Daftar penyertaan |
| `penyertaan_detail` | Penyertaan | `t_tabungan_penyertaan_id` | Detail penyertaan |
| `perubahan_penyertaan` | Perubahan penyertaan | - | Daftar perubahan penyertaan |
| `perubahan_penyertaan_detail` | Perubahan penyertaan | `t_tabungan_perubahan_penyertaan_id` | Detail perubahan penyertaan |
| `chat` | Chat | `t_chat_id` | Ruang chat/tiket tertentu |
| `order_detail` | KKBA Mart | `cart_mobile_id` | Detail order Mart |

Contoh router:

```ts
function openNotificationTarget(navigation: NotificationNavigation) {
  if (!navigation.is_actionable) {
    router.push("/notifications");
    return;
  }

  const { target, params } = navigation;

  switch (target) {
    case "profile":
      router.push("/profile");
      break;
    case "content_detail":
      router.push(`/content/${params.t_content_id}`);
      break;
    case "pinjaman_detail":
      router.push(`/pinjaman/${params.t_pinjaman_id}`);
      break;
    case "tagihan":
      router.push("/tagihan");
      break;
    case "tagihan_detail":
      router.push(`/tagihan/${params.t_tagihan_id}`);
      break;
    case "pencairan_tabungan_detail":
      router.push(`/pencairan/${params.t_tabungan_pengambilan_id}`);
      break;
    case "penyertaan_detail":
      router.push(`/penyertaan/${params.t_tabungan_penyertaan_id}`);
      break;
    case "perubahan_penyertaan_detail":
      router.push(`/perubahan-penyertaan/${params.t_tabungan_perubahan_penyertaan_id}`);
      break;
    case "chat":
      router.push(`/chat/${params.t_chat_id}`);
      break;
    case "order_detail":
      router.push(`/mart/order/${params.cart_mobile_id}`);
      break;
    default:
      router.push("/notifications");
  }
}
```

## 9. Jenis notifikasi yang sudah tersedia

| `type` | Penerima | `target` |
|---|---|---|
| `anggota_approved` | Anggota | `profile` |
| `pinjaman_approved` | Anggota | `pinjaman_detail` |
| `pencairan_tabungan_approved` | Anggota | `pencairan_tabungan_detail` |
| `penyertaan_approved` | Anggota | `penyertaan_detail` |
| `perubahan_penyertaan_approved` | Anggota | `perubahan_penyertaan_detail` |
| `chat_message` | Anggota atau admin | `chat` |
| `content_published` | Seluruh anggota aktif | `content_detail` |
| `order_status` | Anggota | `order_detail` |

Target `tagihan` dan `tagihan_detail` sudah dikenali oleh API inbox, tetapi event push tagihan belum dibuat di backend.

## 10. Push chat dua arah

Endpoint mengirim pesan tetap:

```http
POST /api/chat/message/add
```

Request:

```json
{
  "t_chat_id": "01K8CHAT0000000000000001",
  "message_text": "Baik admin, terima kasih."
}
```

Response:

```json
{
  "success": true,
  "data": {
    "t_chat_id": "01K8CHAT0000000000000001",
    "message_text": "Baik admin, terima kasih.",
    "is_read_user": true,
    "is_read_admin": false,
    "created_by": 123,
    "t_chat_conversations_id": "01K8MESSAGE0000000000001",
    "created_at": "2026-11-01T21:00:00.000000Z",
    "updated_at": "2026-11-01T21:00:00.000000Z"
  },
  "message": "Pesan Dikirim."
}
```

Perilaku backend:

- admin membalas chat → anggota pemilik tiket menerima push;
- anggota membalas chat → seluruh user aktif dengan role `mobile_admin` menerima push;
- anggota tidak dapat mengirim pesan ke tiket anggota lain.

Untuk membuka isi chat:

```http
GET /api/chat/message/open/{t_chat_id}
```

Karena admin menerima push chat, registrasi perangkat admin ke `/api/push/devices` wajib dilakukan.

## 11. API terkait halaman tujuan

Beberapa endpoint yang dapat digunakan setelah aplikasi menerima navigasi:

```http
GET /api/konten
GET /api/konten/{t_content_id}
GET /api/pinjaman/preview/{t_pinjaman_id}
GET /api/chat/message/open/{t_chat_id}
GET /api/notifications
```

Untuk scope yang belum mempunyai endpoint detail khusus, aplikasi dapat membuka halaman daftar terkait dan memakai ID pada `navigation.params` untuk memilih atau mencari data yang sesuai.

## 12. UI inbox yang disarankan

Halaman notifications minimal memiliki:

- pagination atau infinite scrolling;
- indikator read/unread;
- judul, body, dan waktu notifikasi;
- pull-to-refresh;
- tombol "Tandai semua dibaca";
- empty state;
- loading dan retry state;
- badge unread pada icon notifikasi;
- optimistic update ketika item ditekan;
- fallback ke halaman notifications jika target tidak dikenali.

Gunakan `occurred_at` sebagai waktu kejadian bisnis dan `created_at` sebagai waktu notifikasi diterima server.

## 13. Checklist implementasi KKBA Mobile

- [ ] Generate dan simpan installation UUID secara permanen.
- [ ] Meminta permission notifikasi.
- [ ] Mengambil Expo Push Token dengan project ID EAS yang benar.
- [ ] Memanggil `/api/push/devices` setelah login anggota.
- [ ] Memanggil `/api/push/devices` setelah login admin.
- [ ] Memperbarui registrasi ketika push token atau versi aplikasi berubah.
- [ ] Membuat halaman inbox notifications.
- [ ] Menampilkan unread badge.
- [ ] Mengimplementasikan filter read/unread bila dibutuhkan.
- [ ] Menandai satu notifikasi sudah dibaca ketika ditekan.
- [ ] Menambahkan tombol read-all.
- [ ] Menambahkan swipe/slide kiri untuk menghapus satu notifikasi.
- [ ] Menambahkan selection mode dan batch delete maksimum 100 notifikasi.
- [ ] Menambahkan tombol hapus semua dengan dialog konfirmasi.
- [ ] Mengembalikan item jika optimistic delete gagal.
- [ ] Menangani tap system push saat foreground, background, dan cold start.
- [ ] Menyimpan pending navigation bila user belum login.
- [ ] Mengimplementasikan seluruh mapping `target`.
- [ ] Menambahkan route detail konten berdasarkan `t_content_id`.
- [ ] Menambahkan route chat berdasarkan `t_chat_id` untuk anggota dan admin.
- [ ] Mengirim installation ID ketika logout.
- [ ] Menguji menggunakan akun anggota dan akun admin user ID 1 pada perangkat nyata.

## 14. Skenario pengujian penerimaan

1. Login sebagai anggota dan pastikan perangkat tercatat aktif.
2. Login sebagai admin user ID 1 dan pastikan perangkat admin juga tercatat aktif.
3. Admin membalas chat anggota dan pastikan anggota menerima push.
4. Anggota membalas chat dan pastikan admin menerima push.
5. Approve pinjaman/pencairan/penyertaan dan pastikan notifikasi membuka halaman terkait.
6. Buat konten dari `main.konten.create` dan pastikan anggota menerima push serta dapat membuka detail konten.
7. Pastikan edit konten tidak mengirim push baru.
8. Pastikan item yang ditekan berubah menjadi read dan unread badge berkurang.
9. Pastikan user tidak dapat membaca notifikasi milik user lain.
10. Logout dan pastikan perangkat menjadi nonaktif serta tidak menerima push untuk session lama.

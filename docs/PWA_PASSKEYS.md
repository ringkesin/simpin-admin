# Passkey PWA KKBA Mobile

Frontend: `https://koperasi-abipraya.com/app/`. Backend: `https://simpin.koperasi-abipraya.com/api`.

Fitur ini menambahkan login passkey untuk **anggota**, bukan akun CMS atau mobile admin. Pendaftaran dimulai dari Profil → Passkey & Face ID setelah login password. Password saat ini diverifikasi ulang sebelum pendaftaran/penghapusan. Pada login berikutnya anggota menekan **Masuk dengan Passkey / Face ID**; browser/OS melakukan verifikasi pengguna. Face ID dapat digantikan kode sandi perangkat sesuai kebijakan iPhone. Backend tidak menerima data wajah, private key, atau password yang disimpan untuk login otomatis.

## Deployment SimPin

1. Deploy perubahan repository ini, termasuk `composer.json`/`composer.lock`, controller, config, route dan migration.
2. Jalankan `composer install --no-dev --optimize-autoloader` pada build deployment. Library verifikasi yang ditambahkan adalah `lbuchs/webauthn` v2.2.0.
3. Jalankan migration Laravel melalui prosedur deployment biasa: `php artisan migrate --force`. Migration baru hanya membuat `t_pwa_passkeys` dan `t_pwa_passkey_challenges`; tidak mengubah transaksi koperasi.
4. Atur environment Laravel Cloud:

```dotenv
PWA_PASSKEYS_ENABLED=true
PWA_PASSKEYS_RP_ID=koperasi-abipraya.com
PWA_PASSKEYS_ORIGINS=https://koperasi-abipraya.com
```

5. Bangun ulang config/route cache melalui deployment biasa. Pastikan CORS API tetap mengizinkan origin tersebut dan Authorization/Content-Type/Accept untuk GET/POST/DELETE.
6. Impor paket Expo PWA terbaru ke repository website Laravel, lalu deploy website. UI login tidak menampilkan tombol passkey sebelum endpoint config mengembalikan `enabled: true`. Jika backend belum dideploy, login password tetap tersedia.

Fitur default **nonaktif** sampai deployment/config selesai. Tidak ada migration, perubahan environment production, atau deployment otomatis yang dijalankan sebagai bagian implementasi lokal ini. RP ID adalah domain tanpa `/app`, skema, atau port; jangan diubah setelah passkey anggota terdaftar. Origin yang diizinkan harus tepat, bukan wildcard. Untuk pengujian lokal terisolasi, override RP ID/origin pada konfigurasi test saja.

## Kontrak API

| Method | Path (awalan `/api`) | Otorisasi / request |
|---|---|---|
| GET | `/auth/passkeys/config` | Publik, hasil `data.enabled` dan `data.rp_id` |
| POST | `/auth/passkeys/login/options` | Publik; hasil `data.challenge_id`, `data.publicKey` |
| POST | `/auth/passkeys/login/verify` | Publik; `{challenge_id, credential}`; hasil sama seperti `/login` |
| GET | `/auth/passkeys` | Bearer `state:anggota`; daftar id/nama/waktu, tanpa public key/credential ID |
| POST | `/auth/passkeys/register/options` | Bearer anggota, `{current_password, name}`; hasil challenge/options |
| POST | `/auth/passkeys/register/verify` | Bearer yang sama dengan awal pendaftaran, `{challenge_id, credential}` |
| DELETE | `/auth/passkeys/{id}` | Bearer pemilik, `{current_password}` |

Semua operasi kecuali config memeriksa HTTP Origin. Binary WebAuthn memakai base64url tanpa padding. `credential` berisi `id`, `rawId`, `type: public-key`, dan `response`. Pendaftaran mengirim `clientDataJSON`/`attestationObject`; login mengirim `clientDataJSON`/`authenticatorData`/`signature`/`userHandle`.

Challenge acak disimpan di DB bersama TTL lima menit dan dikonsumsi satu kali sebelum verifikasi. Pendaftaran terikat user dan personal access token awal. Origin dalam client data, RP ID hash, user presence, user verification, signature, user handle, ownership, dan sign counter diperiksa. Resident/platform credential dengan attestation none digunakan untuk privasi. Maksimal 10 passkey per anggota; endpoint diberi rate limit. Challenge kedaluwarsa dibersihkan saat challenge baru dibuat.

Login passkey menggunakan kebijakan validity/role yang sama dengan password melalui `AuthController::issueMobileSession`, dan wajib menghasilkan role `mobile_anggota`. Token PWA baru memiliki ability `state:anggota` dan kedaluwarsa delapan jam. Session web tetap dikunci setelah idle lima menit; login passkey membuat sesi baru tanpa menyimpan bearer permanen ke localStorage. Penghapusan passkey juga mencabut token yang diterbitkan oleh passkey tersebut, tanpa mencabut token native lain. Pertahankan password sebagai jalur pemulihan; penghapusan perangkat dari Apple Passwords dan pencabutan credential di server adalah dua operasi berbeda.

## Pengujian

Respons passkey memakai `Cache-Control: no-store, private`. Penghapusan mengembalikan `data.requires_login`; jika bernilai true, token sesi saat ini ikut dicabut dan frontend kembali ke login dengan pesan sukses. Tidak perlu mencoba refresh daftar memakai token yang sudah dicabut.

Pengujian UI lokal memakai Chrome dengan resident authenticator virtual dan server Laravel/SQLite terisolasi. Pendaftaran melalui PWA, tutup/buka jendela, login signature tanpa password, perubahan warna saat navigasi Home/Mutasi Tabungan, serta penghapusan passkey aktif berhasil. Kunci dan akun pada tes ini sintetis; Face ID perangkat nyata dan production masih perlu diuji setelah deployment.

```sh
php vendor/bin/phpunit --filter 'PwaPasskeyTest|DeviceLogoutTest'
```

Test menggunakan SQLite in-memory dan key ECDSA sintetis. Meliputi pendaftaran dan signature login nyata, password/bearer, replay/TTL, token pergantian sesi, origin, UV, signature/challenge/user handle salah, validity/role dicabut, expiry token, revocation, dan format login password lama. Tidak menggunakan akun atau transaksi production. Uji iPhone fisik setelah deployment: aktifkan passkey, tutup aplikasi Home Screen, buka kembali, masuk via Face ID, batalkan prompt, uji fallback password, logout, dan hapus passkey.

# LAPORAN AUDIT COMPREHENSIF & PETA CELAH
# Project Begawi Pengadaan - Pemerintah Provinsi Lampung

**Tanggal audit:** 31 Oktober 2026
**Ruang lingkup:** `c:\laragon\www\begawipengadaan`
**Stack:** CodeIgniter 4.7.4 - PHP ^8.2 - MySQLi - Bootstrap 5 / WowDash / Tailwind
**Mode:** READ-ONLY - tidak ada perubahan pada kode, migration, atau database.

---

## 1. RINGKASAN EKSEKUTIF

Project ini merupakan aplikasi pengadaan publik (portal publik + dashboard admin + workspace
peserta/trainer/operator) dengan cakupan fungsional sangat luas: ~1.092 baris route,
~150 controller, ~90 service, ~60 model, ~300 view, dan 17 migration aditif.

Secara umum, kualitas keamanan fondasi sudah respectable. Beberapa kontrol penting sudah
benar-benar diimplementasikan (bukan sekadar didokumentasikan): filter otorisasi berlapis,
fail-closed session sync, proteksi path traversal pada file streamer, atomic password-reset
token, throttling login, allowlist MIME upload, dan scoping notifikasi berbasis kepemilikan.

Namun audit menemukan celah nyata pada fitur baru (RUP Audit v4.0 yang sedang dikembangkan),
kebocoran artefak rahasia ke Git, serta kesenjangan yang cukup besar antara dokumentasi dan
implementasi.

| Severity | Jumlah | Ringkasan |
|---|---:|---|
| **CRITICAL** | 1 | Hapus batch audit via `GET` (CSRF tanpa token) |
| **HIGH** | 4 | `session_version` tidak pernah divalidasi; artefak rahasia ter-commit Git; pipeline deploy tidak menjalankan migration |
| **MEDIUM** | 15 | Data yatim, IP spoofing, DDL di runtime, kontrol yang hanya didokumentasikan, redaction audit tak lengkap, SVG upload, konfigurasi produksi |
| **MEDIUM (XSS)** | 1 | Parameter filter RUP disisipkan mentah ke atribut `href` |
| **LOW** | 8 | Mojibake UTF-8, artefak repo, duplikasi library, konsistensi escaping |
| **INFO (positif)** | 14 | Kontrol yang sudah benar - pertahankan |

**Tren risiko:** temuan severity MEDIUM/HIGH terkonsentrasi pada area RUP Audit (kode
aktif berubah - `git status` menunjukkan 18 file termodifikasi belum di-commit) dan pada
operasional repo/CI-CD - bukan pada inti autentikasi yang justru paling matang.

> **Peringatan khusus.** Migration `2026-10-31-000002_UpgradeRupAuditToV4.php` adalah file
> **untracked** (belum di-commit), sementara `.github/workflows/deploy.yml` tidak menjalankan
> `php spark migrate`. Kombinasi ini berarti rilis fitur RUP v4.0 di server akan gagal
> karena kolom-kolom baru tidak pernah ada.

---

## 2. METODOLOGI & BATASAN

**Yang dilakukan:**
- Pemetaan arsitektur: konfigurasi, route, filter, controller, service, model, migration, view, CI.
- Audit keamanan statis: authn/authz, input handling, output escaping, upload, SQL, session, CSRF, CSRF token, secret.
- Audit kualitas: konsistensi konvensi, error handling, transaksi, test coverage, schema management.
- Verifikasi silang terhadap 13 dokumen `laporan_audit_*.md` yang sudah ada di repo.

**Yang TIDAK dilakukan (sesuai instruksi):**
- Tidak ada edit pada kode, migration, atau database.
- Tidak ada query ke database (`db_connect()` tidak dipanggil), tidak ada `migrate`, tidak ada seed.
- Tidak ada penetration testing aktif / eksploitasi pada environment berjalan.

**Bats yang perlu diketahui pembaca:**
- Konfigurasi `.env` produksi di server tidak dapat diverifikasi (file `.env` tidak di-track Git; yang terlihat adalah `.env` development lokal).
- Skema DB produksi tidak dapat dibandingkan langsung dengan `schema-fresh-v4-3.sql`; analisis skema dilakukan terhadap file dump dan file migration.
- Temuan XSS/CSRF dinilai dari alur kode statis; konfirmasi runtime tetap disarankan pada staging.

---

## 3. TEMUAN CRITICAL

### C-1 - Penghapusan batch audit RUP dilakukan via `GET`, tanpa proteksi CSRF

| | |
|---|---|
| **Severity** | CRITICAL |
| **File** | `app/Config/Routes.php:313` - `app/Controllers/Workspace/RupAuditController.php:286-301` |
| **Tipe** | CSRF / Broken Access Control / Unsafe HTTP Method |
| **Dampak** | Penghapusan data audit (`rup_audit_batches`, paket, pelanggaran, klaster) tanpa persetujuan eksplisit pengguna |

**Bukti**

```php
// app/Config/Routes.php:313
$routes->get('delete/(:segment)', 'RupAuditController::deleteBatch/$1', ['as' => 'workspace.rup-audit.delete']);
```

```php
// app/Controllers/Workspace/RupAuditController.php:286-301
public function deleteBatch(string $batchUuid)
{
    if ($res = $this->checkAccess()) { return $res; }
    $batch = $this->auditService->getBatch($batchUuid);
    if (! $batch) { return redirect()->back()->with('error', 'Batch audit tidak ditemukan.'); }
    $this->auditService->deleteBatch((int) $batch['id']);   // tidak ada validateSafeMutation()
    return redirect()->to(site_url('workspace/audit-rup'))->with('message', '...');
}
```

Tautan hapus juga dirender sebagai anchor biasa:

```php
// app/Views/main/workspace/pages/rup_audit/index.php:216
<a href="<?= site_url('workspace/audit-rup/delete/' . esc($b['batch_uuid'])) ?>" class="btn btn-outline-danger" ...
```

**Analisis**

1. Filter CSRF bawaan CodeIgniter hanya memvalidasi token pada method `POST/PUT/DELETE/PATCH`.
   Method `GET` sepenuhnya di luar proteksi CSRF - ini perilaku framework, bukan bug CI.
2. Controller tidak memanggil `validateSafeMutation()`, helper yang memang sudah tersedia
   di `app/Controllers/BaseController.php:43-55` dan sudah dipakai oleh 14 controller lain
   (Slider, Testimonial, TeamMember, Announcement, ExternalPortal, Newsletter, dll.).
3. Resultanya: halaman mana pun di internet dapat memuat `<img src="https://host/workspace/audit-rup/delete/RUP-20261031-abcd1234">`.
   Jika admin/operator sedang login dan memiliki akses RUP Audit, batch terhapus otomatis.
4. Tidak ada audit log untuk aksi hapus ini (lihat juga M-2).

**Rekomendasi (prioritas P0)**

- [ ] Ubah route menjadi `POST` dengan `csrf_field()`, atau
- [ ] Minimal: tambahkan `$this->validateSafeMutation()` di `deleteBatch()` (mitigasi Referer internal), dan ubah ke `POST`.
- [ ] Tambahkan pencatatan `AuditService::log($actorId, 'rup_audit.batch.delete', ...)`.
- [ ] Audit ulang seluruh route `GET` bermuatan destruktif (hasil pemindaian: hanya 1 yang bermasalah - route ini).

---

## 4. TEMUAN HIGH

### H-1 - `session_version` ditulis tetapi tidak pernah divalidasi - reset kata sandi tidak mengakhiri sesi aktif

| | |
|---|---|
| **Severity** | HIGH |
| **File** | `app/Controllers/Admin/UserController.php:379-383` - `app/Models/UserModel.php:27` |
| **Dampak** | Sesi milik akun yang sudah di-reset admin tetap valid - account takeover tidak bisa dihentikan |

**Bukti**

```php
// app/Controllers/Admin/UserController.php:379-383  (reset password oleh admin)
$currentSessionVersion = (int) ($user['session_version'] ?? 1);
(new UserModel())->update($userId, [
    'password_hash'   => password_hash($newPassword, PASSWORD_BCRYPT),
    'session_version' => $currentSessionVersion + 1,   // dinaikkan...
    'updated_at'      => date('Y-m-d H:i:s'),
]);
```

Pencarian menyeluruh (`session_version`) menemukan hanya 3 lokasi relevan: penulisan di atas,
daftar `$allowedFields` di `UserModel`, dan satu daftar `$skipKeys` di view. Tidak ada satu
pula kode yang membaca dan membandingkan nilai ini terhadap session.

Dokumentasi yang ada justru menjanjikan sebaliknya:

> `dokumen/data-dictionary.md:174` - `session_version` | BIGINT UNSIGNED | NN | `1` | - | S | **Marker invalidasi session**
> `dokumen/database-blueprint.md:135` - session_version | **Marker invalidasi session**

**Dampak**

Skenario resmi: admin menemukan akun dipakai pihak tak dikenal, melakukan reset password,
namun pelaku masih login dengan sesi lamanya. Kontrol "revoke session" tidak ada.
Hal yang sama berlaku pada `PasswordResetService::resetPassword()`
(`app/Services/Identity/PasswordResetService.php:120-158`) yang juga tidak menaikkan
`session_version`.

**Rekomendasi (P0)**

- [ ] Simpan `session_version` ke session saat login (`AuthenticationService::authenticate()`).
- [ ] Bandingkan di `AuthenticationFilter::before()` dan `WorkspaceAuthFilter::before()`: bila `session_version` di DB berbeda dengan session, jalankan `session()->destroy()` lalu redirect.
- [ ] Naikkan `session_version` pada: reset password admin, reset password mandiri, pencabutan role/izin, dan penonaktifan akun.
- [ ] Tambahkan regression test untuk skenario ini.

---

### H-2 - `writable/settings/system_settings.json` ter-track Git dan menjadi tempat penyimpanan API key

| | |
|---|---|
| **Severity** | HIGH |
| **File** | `writable/settings/system_settings.json` (tracked) - `app/Services/SystemSettingService.php:289-290` |
| **Dampak** | API key integrasi (`lkpp_api_key`), URL webhook, dan konfigurasi sensitif ter-commit ke repository |

**Bukti**

```bash
$ git ls-files | grep writable/
writable/.htaccess
writable/cache/index.html
writable/debugbar/index.html
writable/index.html
writable/logs/index.html
writable/settings/system_settings.json     <- tracked
writable/uploads/index.html
```

```php
// app/Services/SystemSettingService.php:289-290
$json  = json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$saved = (bool) @file_put_contents($this->storagePath, $json);
```

File tersebut memuat grup `integration`:

```json
"integration": {
    "lkpp_api_endpoint": "https://api.lkpp.go.id/v1",
    "lkpp_api_key": "",
    "webhook_url": "",
    "sso_enabled": "0"
}
```

**Analisis**

`.gitignore` mengabaikan `/writable/*`, tetapi file yang sudah terlacak tetap terlacak
dan perubahannya tetap muncul di `git status`, sehingga otomatis ikut ter-commit pada
commit berikutnya. Begitu admin menyimpan pengaturan melalui UI, nilai `lkpp_api_key`
masuk ke working tree bersama seluruh repo.

**Rekomendasi (P0)**

- [ ] `git rm --cached writable/settings/system_settings.json` dan tambahkan pengecualian eksplisit di `.gitignore` bila memang perlu ada default.
- [ ] Pindahkan seluruh secret (`lkpp_api_key`, `webhook_url`, kredensial SMTP) ke `.env` / `Config\Email` / storage privat di luar docroot.
- [ ] Tambahkan `gitleaks` atau `trufflehog` sebagai pre-commit dan step CI.
- [ ] Rotasi `lkpp_api_key` bila pernah diisi (nilai saat ini kosong, tetapi riwayat commit perlu diperiksa).

---

### H-3 - `cookies.txt` berisi token sesi aktif ter-commit ke Git

| | |
|---|---|
| **Severity** | HIGH |
| **File** | `cookies.txt` (tracked, commit `7bdfac3` "sertifikat") |
| **Dampak** | Cookie `ci_session` dapat dipakai untuk session hijacking; menunjukkan token tidak pernah disanitasi sebelum di-commit |

**Bukti**

```
# Netscape HTTP Cookie File
# This file was generated by libcurl! Edit at your own risk.
#HttpOnly_localhost	FALSE	/	FALSE	1791560404	ci_session	af22e454b3548d899a73e6604808b848
#HttpOnly_localhost	FALSE	/	FALSE	1791560404	csrf_cookie_name	e490ea52ed2133c74c83585e3ff0f5b2
```

**Dampak**

`ci_session` adalah session ID. Siapa pun yang memperoleh repo (termasuk mirror, cache CI,
atau fork) berpotensi mengambil alih sesi tersebut bila masih valid. Selain itu, keberadaan
file ini menunjukkan alur kerja "login via curl lalu simpan cookie" tanpa tahap sanitasi.

**Rekomendasi (P0)**

- [ ] Hapus dari repo (`git rm --cached cookies.txt`) dan tambahkan ke `.gitignore`.
- [ ] Segera putuskan sesi dengan ID di atas (memang kemungkinan sudah kedaluwarsa, tetapi tetapkan prosedur).
- [ ] Audit riwayat commit untuk token lain yang sejenis (`*.txt`, `*.har`, `*.env`).
- [ ] Bila commit sudah ter-push ke remote publik, jalankan `git filter-repo` untuk scrubbing.

---

### H-4 - Pipeline deploy tidak menjalankan migration maupun test

| | |
|---|---|
| **Severity** | HIGH |
| **File** | `.github/workflows/deploy.yml` |
| **Dampak** | Rilis fitur RUP v4.0 akan gagal di server; tidak ada gerbang regresi sebelum deploy |

**Bukti** - seluruh job deploy:

```yaml
cd /opt/docker/apps/begawi-pengadaan
git pull origin main
docker compose run --rm app composer install --no-dev --optimize-autoloader --no-interaction
chown -R www-data:www-data writable
chmod -R 775 writable
docker compose up -d --build
docker compose ps
```

Tidak ada `php spark migrate`, tidak ada `php spark test`, tidak ada health-check setelah deploy.

**Dampak**

`app/Database/Migrations/2026-10-31-000002_UpgradeRupAuditToV4.php` adalah file untracked
(`git status` menunjukkan `??`). Bahkan bila di-commit, tanpa `migrate` di pipeline, kolom-kolom
baru (`input_schema_version`, `rule_set_version`, `file_sha256`, `risk_profile_json`,
`regime_status`, `finding_status`, `cluster_confidence`, dan lainnya) tidak akan pernah ada
di database server. Hasilnya `RupAuditService::processUpload()` akan gagal pada tahap insert batch.

**Rekomendasi (P0)**

- [ ] Tambahkan step `docker compose run --rm app php spark migrate --force` sebelum `up -d`.
- [ ] Tambahkan step verifikasi `php spark migrate:status` dan smoke test HTTP.
- [ ] Tambahkan `composer install` dengan dev dependency pada job CI terpisah yang menjalankan test.
- [ ] Bila migrasi otomatis berisiko, tetap jalankan dalam release job terpisah yang dapat di-rollback.

---

## 5. TEMUAN MEDIUM

### M-1 - Penghapusan batch RUP meninggalkan data yatim (orphan) di 4 tabel anak

| | |
|---|---|
| **File** | `app/Services/RupAudit/RupAuditService.php:492-495` - `app/Models/RupAuditBatchModel.php` - `app/Database/Migrations/2026-10-29-000001_CreateRupAuditTables.php` |
| **Dampak** | Ribuan baris yatim menumpuk, database membengkak, diff-tracking batch berikutnya bisa salah hitung |

```php
// RupAuditService.php:492-495
public function deleteBatch(int $batchId): bool
{
    return (bool) $this->batchModel->delete($batchId);   // hanya baris batch
}
```

`RupAuditBatchModel` menyetel `$useSoftDeletes = false` (hard delete), dan tidak ada foreign key
`ON DELETE CASCADE` pada `rup_audit_packages`, `rup_audit_violations`, `rup_audit_clusters`,
maupun `rup_audit_opd_summaries` (migration `2026-10-29-000001` tidak mendefinisikan constraint
FK apa pun untuk kolom `batch_id`).

**Rekomendasi**

- [ ] Bungkus penghapusan dalam transaksi: hapus `violations`, `packages`, `clusters`, `opd_summaries`, lalu `batches`.
- [ ] Alternatif yang lebih baik: tambahkan FK dengan `ON DELETE CASCADE` pada migration baru.
- [ ] Jalankan query pembersihan data yatim yang sudah terlanjur ada (read-only check dulu).

---

### M-2 - `deleteBatch()` dapat menghapus batch milik pengguna lain, tanpa jejak audit

| | |
|---|---|
| **File** | `app/Services/RupAudit/RupAuditService.php:317-330, 492-495` - `app/Controllers/Workspace/RupAuditController.php:286` |

`getBatches()` dan `getBatch()` tidak memfilter `user_id`; otorisasi hanya berupa
`RupAccessHelper::canAccess()` (grup terbatas: admin, Biro PBJ, Inspektorat). Selain itu,
tidak ada `AuditService::log()` pada aksi hapus, berbeda dengan service lain di project yang
konsisten mencatat setiap mutasi.

**Rekomendasi**

- [ ] Tambahkan `AuditService::log($actorId, 'rup_audit.batch.delete', 'rup_audit_batches', 'batch', $id, ['batch_uuid' => ...])`.
- [ ] Batasi `deleteBatch` pada batch milik actor, kecuali actor adalah admin atau super admin.

---

### M-3 - DDL dan seed dijalankan dari dalam request HTTP (bukan migration)

| | |
|---|---|
| **File** | `app/Services/VisitorStatisticService.php:12, 19-38, 109` |
| **Dampak** | Butuh privilege `CREATE TABLE`; kegagalan ditelan diam-diam; drift skema |

```php
// constructor - dipanggil setiap kali service diinstansiasi
public function __construct()
{
    $this->db = db_connect();
    $this->ensureIpLogsTableExists();   // CREATE TABLE IF NOT EXISTS
    $this->seedInitialIfEmpty();
}
```

```php
public function ensureIpLogsTableExists(): void
{
    try {
        $sql = "CREATE TABLE IF NOT EXISTS visitor_ip_logs ( ... ) ENGINE=InnoDB ...";
        $this->db->query($sql);
    } catch (\Throwable) {
        // Silently recover if DB has transient issue   <- error ditelan
    }
}
```

Migration `2026-10-14-000002_CreateVisitorStatisticsTable.php` hanya membuat tabel
`visitor_statistics`; `visitor_ip_logs` tidak ada di migration mana pun.

**Dampak**

- Pada DB production dengan least-privilege (user MySQL tanpa `CREATE`), `CREATE TABLE` gagal
  setiap kali, `catch (\Throwable)` menelan error, sehingga tidak ada satu pun statistik
  visitor yang tercatat, tanpa jejak di log.
- Bertentangan dengan prinsip schema dikelola migration yang dipakai project lain.

**Rekomendasi**

- [ ] Buat migration resmi untuk `visitor_ip_logs`.
- [ ] Hapus pemanggilan DDL dan seed dari constructor; panggil hanya dari command atau migration.
- [ ] Ganti `catch (\Throwable) {}` menjadi `log_message('error', ...)` agar kegagalan terlihat.

---

### M-4 - Header IP klien dipercaya tanpa whitelist proxy

| | |
|---|---|
| **File** | `app/Services/VisitorStatisticService.php:43-76` - `app/Config/App.php:183` (`$proxyIPs = []`) |
| **Dampak** | Statistik visitor dapat dipalsukan/dibanjiri; `ip_hash` pada audit log ikut berisiko |

```php
$candidates = [
    $server['HTTP_CF_CONNECTING_IP'] ?? null,
    $server['HTTP_X_REAL_IP']         ?? null,
    $server['HTTP_X_FORWARDED_FOR']   ?? null,
    $server['HTTP_CLIENT_IP']         ?? null,
];
foreach ($candidates as $candidate) { ... return $cleanIp; }   // dipercaya begitu saja
```

`Config\App::$proxyIPs` kosong, sehingga header spoofing dari klien langsung diterima.
Karena tabel `visitor_ip_logs` punya `UNIQUE KEY uk_ip_feature_date (ip_address, feature, visit_date)`,
penyerang cukup mengacak `X-Forwarded-For` untuk membanjiri statistik visitor.

**Rekomendasi**

- [ ] Gunakan `$request->getIPAddress()` (CI4 sudah menghormati `$proxyIPs`) sebagai sumber utama.
- [ ] Isi `Config\App::$proxyIPs` dengan daftar IP reverse-proxy yang sah.
- [ ] Bila Cloudflare dipakai, masukkan rentang IP resminya.

---

### M-5 - Filter `guest` dan `rateLimit:*` yang didokumentasikan tidak ada di implementasi

| | |
|---|---|
| **File** | `dokumen/route-contract.md:210, 228-229, 244-246` versus `app/Config/Filters.php:29-41, 113-120` |
| **Dampak** | Dokumen kontrol keamanan tidak dapat dipercaya; endpoint registrasi dan verifikasi email tanpa throttling |

`dokumen/route-contract.md` secara eksplisit mencantumkan filter per-route:

```
POST /dashboard/daftar/tinjau      -> filter: guest, rateLimit:registration
GET  /dashboard/verifikasi/{token} -> filter: rateLimit:email-verify
POST /dashboard/lupa-kata-sandi    -> filter: guest, rateLimit:password-reset
```

Realitas kode:

```php
// app/Config/Filters.php:29-41 - alias yang tersedia
public array $aliases = ['csrf','toolbar','honeypot','invalidchars','secureheaders',
                          'cors','forcehttps','pagecache','performance','auth','workspace-auth'];

// app/Config/Filters.php:113-120 - hanya 2 filter berbasis path
public array $filters = [
    'auth'           => ['before' => ['dashboard', 'dashboard/*']],
    'workspace-auth' => ['before' => ['workspace', 'workspace/*']],
];
```

Tidak ada alias `guest` maupun `rateLimit:*`, dan `app/Config/Routes.php` tidak memakai opsi
`'filter'` sama sekali pada route mana pun (hasil `Select-String "'filter'"` mengembalikan kosong).

**Dampak**

- Rate limiting hanya diimplementasikan secara inline di `LoginController` (L97-104),
  `PasswordController` (L63+), dan `CommunicationService` (presensi). Endpoint registrasi dan
  verifikasi email tidak memiliki throttle sama sekali.
- `guest` sebagai filter bermakna "hanya untuk belum login"; tanpa itu, halaman `dashboard/masuk`
  tetap bisa diakses oleh pengguna yang sudah login (persoalan UX, bukan keamanan).
- Reviewer baru yang memercayai `route-contract.md` akan mengira ada proteksi yang sebenarnya tidak ada.

**Rekomendasi**

- [ ] Implementasikan `App\Filters\RateLimitFilter` generik plus alias (`rateLimit:registration`,
      `rateLimit:email-verify`, `rateLimit:password-reset`) dan `App\Filters\GuestFilter`.
- [ ] Terapkan pada route yang relevan di `Routes.php`.
- [ ] Alternatif minimal: koreksi `route-contract.md` agar sesuai implementasi, dan beri penanda
      "PLANNED / NOT IMPLEMENTED" pada kontrol yang belum ada.

---

### M-6 - Throttle login bergantung pada file-cache lokal

| | |
|---|---|
| **File** | `app/Controllers/Dashboard/Auth/LoginController.php:97-104` - `app/Config/Cache.php:25` |
| **Dampak** | Rate limit tidak dibagi antar replica; hash IP tanpa salt dapat di-dictionary |

```php
$throttler = service('throttler');
if ($throttler->check('auth_login_ip_' . md5($ip), 25, 60) === false) { ... }
if ($throttler->check('auth_login_' . md5($ip . '_' . $email), 10, 60) === false) { ... }
```

Driver cache default adalah `file` (`Config\Cache.php:25` bernilai `'file'`), disimpan di `writable/cache`.

**Dampak**

- Pada deployment multi-container atau multi-replica, rate limit tidak dibagi antar instance,
  sehingga limit efektifnya dikali jumlah replica.
- Cache file terhapus pada setiap deploy, sehingga limit ter-reset.
- `md5($ip)` tanpa salt, sehingga IP dapat di-dictionary dari hash bila file cache dapat dibaca
  (misalnya backup, volume container, atau path traversal).

**Rekomendasi**

- [ ] Gunakan Redis atau Memcached untuk cache pada environment production (`Config\Cache::$handler`).
- [ ] Ganti `md5()` dengan `hash_hmac('sha256', $ip, $encryptionKey)`.
- [ ] Naikkan TTL dan tambahkan counter kegagalan per-akumen (lockout akun) di luar cache file.

---

### M-7 - Redaction audit log tidak menutup PII (NIK, NIP, email, token)

| | |
|---|---|
| **File** | `app/Services/AuditService.php:81-106` |
| **Dampak** | Data pribadi pendaftar dan ASN bocor ke tabel `audit_logs` (kolom `reason`, `before_snapshot`, `after_snapshot`) |

```php
$sensitiveKeys = [
    'password', 'password_confirmation', 'password_hash',
    'token', 'plain_token', 'session_id', 'secret', 'credential',
];
foreach ($metadata as $key => $value) {
    if (in_array(strtolower((string) $key), $sensitiveKeys, true)) { $metadata[$key] = '[REDACTED]'; continue; }
    if (is_array($value)) { $metadata[$key] = $this->redact($value); }
}
```

Tidak tertutup: `nik`, `nip`, `employee_identifier`, `identity_number`, `email`, `phone`, `otp`,
`verification_token`, `token_hash`, `api_key`, `authorization`, `signature`.
Kolom `reason` menyimpan seluruh metadata sebagai JSON (`AuditService.php:55`), sehingga apa pun
yang diteruskan controller ke audit akan tersimpan apa adanya.

**Rekomendasi**

- [ ] Ganti pencocokan exact-match dengan pola substring, misalnya
      `preg_match('/(pass|token|secret|credential|otp|nik|nip|email|phone|api_key|signature)/i', $key)`.
- [ ] Terapkan allowlist untuk audit pada endpoint sensitif, atau minimal redaksi berdasarkan
      klasifikasi data (`dokumen/privacy-and-data-classification.md` sudah tersedia).
- [ ] Tambahkan regression test pada `AuditService::redact()`.

---

### M-8 - SVG diizinkan pada upload landing (permukaan Stored XSS)

| | |
|---|---|
| **File** | `app/Services/LandingImageUploadService.php:33` - `public/uploads/.htaccess` |
| **Dampak** | SVG dapat dieksekusi sebagai script di origin bila proteksi server tidak berlaku |

```php
$allowedMimes = [
    'image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'image/gif',
    'image/svg+xml',      // SVG dapat membawa skrip
];
```

Berkas disimpan ke `public/uploads/landing/<subfolder>/<random>.svg`, yaitu di dalam docroot dan
di-serve langsung oleh web server.

**Mitigasi yang ada (dan terbatas)**

`public/uploads/.htaccess` menolak `svg|html|htm|php|...` via `FilesMatch` plus `Require all denied`.
Namun:

1. `.htaccess` hanya berlaku pada Apache dengan `mod_authz_core`. Deploy script memakai
   `docker compose`; bila web server-nya Nginx, `.htaccess` sama sekali diabaikan.
2. Proteksi itu hanya berlaku pada `public/uploads/`, bukan pada `writable/uploads/` yang dipakai
   `FileStorageService` (mitigasi kedua ada di `writable/.htaccess`, juga Apache-only).

**Rekomendasi**

- [ ] Cabut `image/svg+xml` dari allowlist. Bila SVG dibutuhkan, sanitasi (buang `<script>`,
      atribut `on*`, `<foreignObject>`, `xlink:href="javascript:"`) sebelum disimpan.
- [ ] Tambahkan proteksi setara di konfigurasi Nginx:
      `location ~* ^/uploads/.*\.(php|svg|html?|phtml|phar)$ { deny all; }`.
- [ ] Audit seluruh direktori upload yang berada di dalam `public/`.

---

### M-9 - Konfigurasi produksi tidak aman secara default (cookie, HTTPS, CSP, Host header)

| | |
|---|---|
| **File** | `app/Config/Cookie.php:57` - `app/Config/App.php:32, 160, 201` - `public/.htaccess` |

| Setting | Nilai | Catatan |
|---|---|---|
| `Config\Cookie::$secure` | `false` | Cookie sesi dikirim tanpa flag `Secure`, bocor bila HTTP bisa diakses |
| `Config\Cookie::$samesite` | `Lax` | OK, pertahankan |
| `Config\App::$forceGlobalSecureRequests` | `false` | Tidak memaksa HTTPS meski filter `forcehttps` aktif |
| `Config\App::$CSPEnabled` | `false` | `ContentSecurityPolicy.php` sudah disiapkan rapi tapi tidak diaktifkan |
| `Config\App::$allowedHostnames` | `[]` | Tidak ada proteksi Host header, padahal `Config\Hostnames.php` sudah ada |
| `public/.htaccess` | rewrite `www.` | `RewriteCond %{HTTPS} !=on` plus `RewriteRule ^ http://%1...` memaksa turun ke HTTP |

**Dampak**

Rewrite `www` yang memaksa HTTP berarti domain `www.*` tidak pernah melayani HTTPS, padahal cookie
sesi juga dikirim ke `www`. Ini adalah downgrade yang substantif, bukan sekadar kosmetik.

**Rekomendasi**

- [ ] Jadikan `Config\Cookie` dan `Config\App` environment-aware:
      `secure = (ENVIRONMENT !== 'development')`.
- [ ] Aktifkan `forceGlobalSecureRequests = true` di production.
- [ ] Aktifkan CSP setelah inventory inline script (nonce `autoNonce` sudah dikonfigurasi).
- [ ] Isi `allowedHostnames` dengan domain produksi.
- [ ] Perbaiki rewrite `www` agar mempertahankan HTTPS.

---

### M-10 - `DBDebug = true` di-hardcode pada config yang di-commit

| | |
|---|---|
| **File** | `app/Config/Database.php:36` |

```php
'DBDebug' => true,
```

Bila terjadi exception database di production, CI4 menyertakan SQL lengkap beserta nilai binding
pada halaman error, sehingga nama database, nama tabel, dan nilai parameter terekspos ke pengguna.

**Rekomendasi**

- [ ] Jadikan environment-driven: `'DBDebug' => (ENVIRONMENT !== 'production')`.
- [ ] Tambahkan `Config\Exceptions::$maskDatabaseErrors = true` untuk production.

---

### M-11 - `RupAuditController::upload()` tidak memvalidasi hasil `move()` dan batas ukuran berkas

| | |
|---|---|
| **File** | `app/Controllers/Workspace/RupAuditController.php:93-124` |

```php
$ext = strtolower($file->getClientExtension());            // dari klien, bisa dipalsukan
if (! in_array($ext, ['csv', 'txt'], true)) { ... }
$targetDir = WRITEPATH . 'uploads/rup_audit/';
if (! is_dir($targetDir)) { mkdir($targetDir, 0775, true); }
$newFileName = 'RUP_' . date('Ymd_His') . '_' . substr(bin2hex(random_bytes(4)), 0, 6) . '.csv';
$file->move($targetDir, $newFileName);                       // hasil TIDAK diperiksa
$fullPath = $targetDir . $newFileName;
...
'file_size' => filesize($fullPath),                          // bisa warning jika file tak ada
```

**Dampak**

- Bila `move()` gagal (izin disk, quota, nama bentrok), kode tetap melanjut. `filesize()` pada
  path yang tidak ada menghasilkan warning PHP dan nilai `false`, lalu proses audit dijalankan
  terhadap file yang tidak ada sehingga pesan error tidak jelas bagi pengguna.
- Tidak ada validasi ukuran maksimum pada layer aplikasi. Batas `post_max_size` 128M di
  `.htaccess` terlalu longgar untuk CSV yang diproses penuh ke memory dengan `memory_limit` 512M.

**Rekomendasi**

- [ ] Periksa hasil `move()`: `if (! $file->move(...)) { return redirect()->back()->with('error', ...); }`.
- [ ] Validasi MIME server-side (`text/csv`, `text/plain`) selain ekstensi.
- [ ] Batasi ukuran berkas (misal 50 MB) dan beri pesan yang jelas.
- [ ] Bungkus `filesize()` dengan pengecekan `is_file()`.

---

### M-12 - Skema baseline hanya ada sebagai dump SQL, migration bersifat aditif

| | |
|---|---|
| **File** | `schema-fresh-v4-3.sql` (120 KB, tracked) - `app/Database/Migrations/` (17 file) |

`app/Database/Migrations` berisi 17 migration penambahan kolom dan tabel, sementara skema dasar
lengkap (puluhan tabel: `users`, `roles`, `permissions`, `role_permissions`, `user_roles`,
`learning_*`, `regulations`, `events`, dan lainnya) hanya ada di `schema-fresh-v4-3.sql` di root repo.

**Dampak**

- Instalasi baru tidak bisa dibangun dari migration saja.
- `php spark migrate:status` dan `migrate:rollback` tidak merepresentasikan keadaan skema sebenarnya.
- Risiko drift skema tinggi: dump dan DB produksi bisa berbeda tanpa terdeteksi.
- Catatan positif: dump berisi `-- Data exporting was unselected.` sehingga 0 baris `INSERT INTO`
  (tidak ada kebocoran data di dalam dump).

**Rekomendasi**

- [ ] Buat migration baseline dari dump (dapat dibangkitkan), lalu jadikan migration satu-satunya sumber kebenaran.
- [ ] Bila dump tetap dipertahankan, jadikan artefak wajib di release checklist dan jalankan diff skema berkala terhadap production.
- [ ] Tambahkan `php spark migrate:status` sebagai gate di pipeline.

---

### M-13 - Tidak ada pipeline test otomatis; 98 "test" berada di `app/Commands`, bukan PHPUnit

| | |
|---|---|
| **File** | `app/Commands/` (98 file `*Test.php`) - `tests/` (4 file) - `.gitignore` (baris `/phpunit.xml`) |

```bash
$ ls app/Commands/ | wc -l
98      # AboutContactCertTest.php, ArenaE2ETest.php, AuthSystemTest.php, ...

$ find tests -name '*.php'
tests/database/ExampleDatabaseTest.php
tests/feature/Auth/CompatibilityFoundationTest.php
tests/session/ExampleSessionTest.php
tests/unit/HealthTest.php
tests/_support/...   (stub bawaan CodeIgniter)
```

Seluruh file di `tests/` adalah stub bawaan CodeIgniter appstarter yang belum diimplementasikan.
Selain 98 skrip "test" yang sesungguhnya berada di `app/Commands`, seluruhnya merupakan CLI bespoke
yang memodifikasi database secara langsung, bukan test otomatis.
database secara langsung, bukan test otomatis.

```json
// composer.json
"scripts": { "test": "phpunit" }
```

```bash
$ ls phpunit.xml
False     # tidak ada; dan .gitignore memuat "/phpunit.xml" sehingga sengaja diabaikan VCS
```

**Dampak**

- `composer test` tidak dapat dijalankan (PHPUnit tanpa konfigurasi).
- Tidak ada regression gate: `deploy.yml` tidak menjalankan test sama sekali.
- Skrip di `app/Commands` memodifikasi DB secara langsung (lihat M-14) sehingga tidak aman
  dijalankan di production.

**Rekomendasi**

- [ ] Commit `phpunit.xml.dist` dan hapus `/phpunit.xml` dari `.gitignore`.
- [ ] Pindahkan skrip `app/Commands/*Test.php` ke `tests/` sebagai PHPUnit test yang benar
      (menggunakan `DatabaseTestTrait` dan `CITestStreamFilter`).
- [ ] Tambahkan job CI: install dev dependencies, jalankan test, lalu deploy.
- [ ] Tetapkan target cakupan (coverage), misal 60% pada modul kritikal (Identity, RupAudit, Notification).

---

### M-14 - Skrip destruktif berada di jalur kode produksi dan dapat dipanggil di server

| | |
|---|---|
| **File** | `app/Commands/DatabaseResetContent.php` - `DatabaseCleanAudit.php` - `ArenaDemoSeed.php` - `EventSeed.php` - `NewsSeed.php` - `KnowledgeSeed.php` - `RegulationSeed.php` - `OpdRoleSeed.php` - `SecurityRoleMigration.php` - `PermissionSync.php` |

`composer install --no-dev` tetap memuat seluruh `app/Commands` (bukan `require-dev`), sehingga
perintah-perintah ini tetap tersedia di production via `php spark`.

**Dampak**

`php spark database:reset-content` atau `database:clean-audit` yang dijalankan salah oleh operator
(atau oleh proses CI/CD yang salah konfigurasi) dapat menghapus konten produksi.

**Rekomendasi**

- [ ] Guard setiap command destruktif dengan:

  ```php
  if (ENVIRONMENT === 'production') {
      CLI::error('Perintah ini dinonaktifkan pada production.');
      return Command::FAILURE;
  }
  ```

- [ ] Pindahkan ke `app/Database/Seeds/` atau direktori `dev-tools/` yang tidak di-deploy.
- [ ] Tambahkan prompt konfirmasi eksplisit (opsi `--force` untuk skip).

---

### M-15 - `validateSafeMutation()` hanya memeriksa prefix Referer

| | |
|---|---|
| **File** | `app/Controllers/BaseController.php:43-55` |

```php
$siteOrigin = rtrim(site_url(), '/');
if ($referer === '' || ! str_starts_with($referer, $siteOrigin)) { return 403; }
```

`site_url()` menghasilkan `http://begawipengadaan.test/` sehingga `$siteOrigin` bernilai
`http://begawipengadaan.test`. Referer `http://begawipengadaan.test.evil.com/x` lolos pemeriksaan prefix.

**Dampak**

Mitigasi ini adalah defence-in-depth, bukan kontrol utama. Karena C-1 tidak memakainya sama sekali,
dan route lain memakainya sebagai satu-satunya barrier untuk mutasi via GET, kelemahan ini relevan.

**Rekomendasi**

- [ ] Parse Referer dengan `parse_url()` dan bandingkan `host` serta `scheme` secara ketat dengan
      `parse_url(site_url())`.
- [ ] Utamakan POST plus CSRF token untuk seluruh mutasi; jadikan Referer hanya lapisan tambahan.

---

## 6. XSS REFLEKTIF PADA PARAMETER FILTER RUP

### M-16 - Parameter filter RUP disisipkan mentah ke atribut `href`

| | |
|---|---|
| **Severity** | MEDIUM |
| **File** | `app/Views/main/workspace/pages/rup_audit/detail.php:378` - `app/Controllers/Workspace/RupAuditController.php:158-168` |

```php
// Controller - nilai diambil apa adanya dari query string
$severity    = trim((string) $this->request->getGet('severity')) ?: 'ALL';
$budgetCycle = trim((string) $this->request->getGet('cycle')) ?: 'ALL';
```

```php
// View - disisipkan ke href TANPA esc() dan TANPA urlencode()
<a class="page-link" href="<?= site_url('workspace/audit-rup/detail/' . esc($batch['batch_uuid'])
   . '?tab=action-sheet&page=' . $p
   . '&opd=' . urlencode($filters['opd'])
   . '&severity=' . $filters['severity']          // tanpa esc
   . '&cycle=' . $filters['budget_cycle']         // tanpa esc
   . '&q=' . urlencode($filters['search']) ...
```

Perhatikan: `opd` dan `q` sudah di-`urlencode`, tetapi `severity` dan `cycle` tidak. Ini menandakan
kelalaian, bukan keputusan desain.

**Analisis exploitabilitas**

- `Config\App::$permittedURIChars = 'a-z 0-9~%.:_\-'` hanya diterapkan pada route segment
  (`vendor/codeigniter4/framework/system/Router.php:763`), bukan pada query string.
- Karakter `"` dan `>` adalah karakter query string yang valid, sehingga payload seperti
  `?severity="><img src=x onerror=alert(document.domain)>` diteruskan apa adanya.
- Karakter `"` memutus atribut `href`, sehingga payload masuk ke konteks HTML.
- **Dampak**: reflected XSS pada halaman yang dipakai APIP atau Administrator. Bisa dipakai untuk
  mencuri token, escalate privileges, atau mengarahkan admin ke halaman phishing.
- **Mitigasi parsial**: `RupAccessHelper::canAccess()` membatasi akses ke grup kecil (Admin,
  Biro PBJ, Inspektorat). Jadi bukan stored XSS dan bukan publik, tetapi targetnya justru user
  paling berkuasa.

**Rekomendasi (P1)**

- [ ] Gunakan `esc(urlencode($filters['severity']))` dan `esc(urlencode($filters['budget_cycle']))`,
      atau lebih baik bangun query string dengan `http_build_query()`.
- [ ] Whitelist-kan nilai filter di controller (hanya `ALL|KRITIS|TINGGI|SEDANG|RENDAH|AMAN` dan
      `ALL|MURNI|PERUBAHAN` yang diizinkan). Ini sekaligus menutup kemungkinan SQL injection via filter.
- [ ] Audit view lain dengan pola serupa. Contoh `app/Views/dashboard/learning/assessments/management.php:132,142,152,168`
      memakai `$filters['class_id']`, yang aman karena sudah di-cast `(int)` di
      `AssessmentManagementController.php:34`.

---

## 7. TEMUAN LOW

### L-1 - Encoding UTF-8 rusak (mojibake) pada file settings yang ter-track

**File:** `writable/settings/system_settings.json`

Gejala pada `writable/settings/system_settings.json`:

- `copyright_text` memuat karakter `A-copyright` (byte `C3 82` + `C2 A9`), sehingga simbol
  copyright (U+00A9) tampil didahului karakter `A` beraccent.
- `vision_title` memuat en-dash U+2013 yang ter-decode dua kali, sehingga tampil sebagai
  `2024 a-kutip- 2029`.
- `wa_welcome_text` memuat byte emoji yang ter-decode dua kali sehingga tampil sebagai
  karakter Replacement Character.

Seluruhnya adalah gejala khas **double-encoded UTF-8**, yaitu byte UTF-8 yang lalu diperlakukan
sebagai Latin-1 atau Windows-1252 dan di-encode ulang. Penyebabnya adalah data ter-encode dua kali
ketika ditulis atau dipindahkan antar sistem.

**Dampak:** teks tampil salah di landing page, PDF, dan email.

**Rekomendasi:** regenerasi file dengan UTF-8 yang benar; tambahkan normalisasi
(`mb_convert_encoding`) saat load bila sumber tidak dipercaya.

### L-2 - Nilai uji bocor ke file ter-track

**File:** `writable/settings/system_settings.json` dengan nilai
`"app_name": "BegawiPengadaan Lampung Testing 081025"`.

**Rekomendasi:** reset ke nama produksi sebelum commit; tambahkan file ini ke `.gitignore` (lihat H-2).

### L-3 - CSV data procurement 1,5 MB menggantung di root project

**File:** `data_dashboard_rup_table_20261031_063857_0986d8ff.csv` (untracked, tetapi ada di working tree).

**Dampak:** data sensitif (nama paket, nilai pagu, nama OPD) mudah ikut ter-commit atau ter-backup.

**Rekomendasi:** pindahkan ke `writable/uploads/rup_audit/` (di luar docroot) dan tambahkan pola
`data_dashboard_*.csv` ke `.gitignore`.

### L-4 - Library pihak ketiga diduplikasi di dalam app dan docroot

**File:** `public/assets/main/inc/PHPMailer/` sekaligus `app/Views/reference/fistudy/assets/inc/PHPMailer/`;
seluruh `app/Views/reference/wowdash/` (ratusan file HTML template) dan `app/Views/reference/fistudy/`.

**Dampak:** PHPMailer di `public/` adalah file PHP di dalam docroot, sehingga bila ada path traversal
atau upload salah konfigurasi menjadi vektor RCE; versi duplikat tidak pernah dipatch.

**Rekomendasi:** pindahkan PHPMailer ke `vendor/` (Composer sudah mengelola dependency), hapus salinan
duplikat, keluarkan `app/Views/reference/` dari build produksi.

### L-5 - Pola raw SQL `EXISTS` yang fragile di `Admin/UserController`

**File:** `app/Controllers/Admin/UserController.php:53, 63, 108, 116`.

`$db->escape($opd)` sudah benar sehingga aman dari injeksi, tetapi pola raw SQL ini sulit direview
secara statis dan tidak portabel.

**Rekomendasi:** gunakan query builder `whereExists()` atau subquery, dan konsistenkan format.

### L-6 - `EventRegistrationStatus::applyFilter()` menyusun SQL literal

**File:** `app/Services/Communication/EventRegistrationStatus.php:188-207`.

Nilai berasal dari konstanta sehingga aman dari injeksi, tetapi pola `where('(' . $sql . ')', null, false)`
berpasangan dengan alias (`er`, `a`) yang rapuh.

**Rekomendasi:** tulis ulang dengan `groupStart()` dan `groupEnd()` agar type-safe dan portable.

### L-7 - Output view tanpa `esc()` (residual)

Contoh: `app/Views/dashboard/communication/inbox/detail.php:435-437` (`$message['status']`),
`app/Views/main/workspace/pages/trainer/quiz_attempts.php:255-274` (`$item['accuracy_rate']`,
`$item['difficulty_badge']`).

**Dampak:** saat ini nilainya berasal dari enum atau DB sehingga risikonya rendah, tetapi pola ini
rapuh terhadap data yang tidak terkontrol.

**Rekomendasi:** terapkan `esc()` seragam; tambahkan lint rule (PHP_CodeSniffer custom sniff) untuk
mendeteksi `<?= $var` tanpa `esc()`.

### L-8 - Inkonsistensi pembentukan query string pada view RUP

**File:** `app/Views/main/workspace/pages/rup_audit/detail.php:378`.

**Dampak:** rendah, input sudah dibatasi whitelist di controller untuk sebagian parameter, tetapi
 prone to error saat menambah parameter baru.

**Rekomendasi:** bangun query string dengan `http_build_query()`.

---

## 8. KONTROL YANG SUDAH BENAR (PERTAHANKAN)

Temuan positif berikut sudah terimplementasikan dengan benar dan sebaiknya dipertahankan saat
melakukan remediasi:

| # | Kontrol | Bukti |
|---|---|---|
| 1 | Filter otorisasi berlapis berbasis zona | `app/Filters/AuthenticationFilter.php:87-288` - 18 zona modul, fail-closed, plus pemeriksaan write-versus-read pada route mutasi (L252-282) |
| 2 | Session role dan permission di-refresh real-time dari DB (anti-stale) | `AuthenticationFilter.php:50-57`, `WorkspaceAuthFilter.php:58-83` |
| 3 | Isolasi sub-grup URL Workspace (operator, trainer, admin) | `WorkspaceAuthFilter.php:91-123` |
| 4 | Proteksi path traversal pada file streamer | `app/Controllers/Landing/FileController.php:228-267` - `realpath()` plus `strncmp()` containment check |
| 5 | Password reset token: `random_bytes(32)`, disimpan sebagai SHA-256, TTL 1 jam, one-time-use atomik | `app/Services/Identity/PasswordResetService.php:49-51, 120-158` - atomic conditional invalidation dengan pengecekan `affectedRows` |
| 6 | Session ID di-regenerate setelah login | `app/Services/Identity/AuthenticationService.php:40-42` |
| 7 | Throttling login dual-tier (IP 25/menit + IP dan email 10/menit) | `app/Controllers/Dashboard/Auth/LoginController.php:96-104` |
| 8 | Allowlist MIME upload untuk dokumen | `app/Services/FileStorageService.php:15-32` |
| 9 | Scoping notifikasi berbasis kepemilikan (anti-IDOR) | `app/Services/NotificationService.php:283-325` - `applyUserScope()` |
| 10 | Auto-routing dimatikan (tidak ada controller atau method tak ter-map) | `app/Config/Routing.php:97` - `autoRoute = false` |
| 11 | Penghapusan aset file storage dibersihkan bila insert DB gagal | `app/Services/FileStorageService.php:72-76` |
| 12 | `writable/` dan `app/` dilindungi dari akses web | `writable/.htaccess`, `app/.htaccess` (`Require all denied`) |
| 13 | Skema dump tidak memuat data produksi | `schema-fresh-v4-3.sql` - 0 `INSERT INTO` |
| 14 | Token reset tidak pernah ditulis ke audit log | `PasswordResetService.php:69` - hanya mencatat `password_reset_requested` |

---

## 9. REKOMENDASI TERPRIORITAS

### Gelombang 0 - sebelum rilis berikutnya (estimasi 1 hari)

1. **C-1** Ubah hapus batch RUP ke `POST` plus `csrf_field()` dan `AuditService::log()`.
2. **H-2** `git rm --cached writable/settings/system_settings.json`; pindahkan secret ke `.env`.
3. **H-3** `git rm --cached cookies.txt`; audit riwayat commit untuk token lain.
4. **H-4** Tambahkan `php spark migrate --force` ke `deploy.yml`.
5. **M-16** `esc(urlencode())` pada parameter filter RUP plus whitelist nilai filter di controller.

### Gelombang 1 - hardening inti (estimasi 3 hari)

6. **H-1** Implementasikan validasi `session_version` di kedua auth filter.
7. **M-5** Implementasikan filter `rateLimit:*` dan `guest`, atau koreksi `route-contract.md`.
8. **M-3** Pindahkan `visitor_ip_logs` ke migration resmi; hapus DDL runtime.
9. **M-7** Perluas redaksi `AuditService` (PII dan token).
10. **M-8** Cabut SVG dari allowlist upload plus proteksi Nginx pada direktori upload.
11. **M-9 dan M-10** Environment-aware: `Cookie.secure`, `forceGlobalSecureRequests`, `DBDebug`,
    `allowedHostnames`, perbaiki rewrite `www`.

### Gelombang 2 - konsistensi data dan operasional (estimasi 1 minggu)

12. **M-1** Hapus anak secara transaksional atau tambah FK cascade.
13. **M-11** Validasi hasil `move()`, MIME, dan ukuran berkas upload RUP.
14. **M-2** Scope `deleteBatch` per actor plus audit log.
15. **M-4** Perbaiki resolusi IP dengan memanfaatkan `$proxyIPs`.
16. **M-6** Cache Redis untuk throttle plus HMAC key.
17. **M-12** Tentukan sumber kebenaran skema (migration baseline atau dump).

### Gelombang 3 - test dan preventif (estimasi 2 minggu)

18. **M-13** Commit `phpunit.xml.dist`; pindahkan 98 skrip ke `tests/` sebagai PHPUnit; tambahkan CI test gate.
19. **M-14** Guard `ENVIRONMENT === 'production'` pada seluruh command destruktif.
20. **L-1 sampai L-8** Perbaiki mojibake, bersihkan artefak repo, relokasi PHPMailer, konsistensi escaping.
21. Tambahkan `gitleaks` di pre-commit dan CI; jalankan `composer audit` secara berkala.

---

## 10. MATRIKS TEMUAN RINGKAS

| ID | Severity | Kategori | File Utama | Status |
|---|---|---|---|---|
| C-1 | CRITICAL | CSRF / HTTP Method | `Routes.php:313`, `RupAuditController.php:286` | Belum |
| H-1 | HIGH | AuthN / Session | `Admin/UserController.php:379` | Belum |
| H-2 | HIGH | Secret Management | `writable/settings/system_settings.json` | Belum |
| H-3 | HIGH | Secret Management | `cookies.txt` | Belum |
| H-4 | HIGH | CI/CD | `.github/workflows/deploy.yml` | Belum |
| M-1 | MEDIUM | Integritas Data | `RupAuditService.php:492` | Belum |
| M-2 | MEDIUM | Audit Trail | `RupAuditController.php:286` | Belum |
| M-3 | MEDIUM | Schema Mgmt | `VisitorStatisticService.php:19` | Belum |
| M-4 | MEDIUM | Trust Boundary | `VisitorStatisticService.php:43` | Belum |
| M-5 | MEDIUM | Doc vs Code Drift | `Filters.php:29` versus `route-contract.md` | Belum |
| M-6 | MEDIUM | Rate Limit | `LoginController.php:97` | Belum |
| M-7 | MEDIUM | Privacy | `AuditService.php:81` | Belum |
| M-8 | MEDIUM | Upload / XSS | `LandingImageUploadService.php:33` | Belum |
| M-9 | MEDIUM | Hardening | `Cookie.php:57`, `App.php:160,201` | Belum |
| M-10 | MEDIUM | Info Leak | `Database.php:36` | Belum |
| M-11 | MEDIUM | Error Handling | `RupAuditController.php:109` | Belum |
| M-12 | MEDIUM | Schema Mgmt | `app/Database/Migrations/` | Belum |
| M-13 | MEDIUM | Test Coverage | `tests/`, `composer.json` | Belum |
| M-14 | MEDIUM | Ops Safety | `app/Commands/*` | Belum |
| M-15 | MEDIUM | AuthZ | `BaseController.php:43` | Belum |
| M-16 | MEDIUM | XSS Reflektif | `rup_audit/detail.php:378` | Belum |
| L-1 | LOW | Data Integrity | `writable/settings/*.json` | Belum |
| L-2 | LOW | Config | `writable/settings/*.json` | Belum |
| L-3 | LOW | Data Hygiene | root `*.csv` | Belum |
| L-4 | LOW | Dependency | `public/.../PHPMailer` | Belum |
| L-5 | LOW | SQL Hygiene | `Admin/UserController.php:53` | Belum |
| L-6 | LOW | SQL Hygiene | `EventRegistrationStatus.php:188` | Belum |
| L-7 | LOW | Output Escaping | beberapa view | Belum |
| L-8 | LOW | Konsistensi | `rup_audit/detail.php:378` | Belum |

---

## 11. CATATAN PENUTUP

Audit ini bersifat statis dan read-only. Tidak ada kode, migration, maupun data yang diubah.
Seluruh temuan disertai referensi `file:line` sehingga dapat diverifikasi langsung.

Prioritas absolut adalah **C-1** (hapus via GET) dan **H-4** (migration tidak pernah jalan),
karena keduanya berada di jalur yang paling mungkin menyebabkan insiden produksi segera
setelah rilis fitur RUP Audit v4.0.

Rekomendasi berikut bersifat opsional dan memerlukan keputusan tim, bukan sekadar perubahan
teknis: integrasi SSO, penetration test eksternal berjadwal, dan pemisahan environment staging
dengan database terisolasi.

---

*Dokumen ini dibuat sebagai bagian dari audit read-only. Tidak ada file kode, migration, maupun
database yang dimodifikasi selama penyusunan laporan ini.*

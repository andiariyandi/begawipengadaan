# LAPORAN AUDIT KEAMANAN & KUALITAS KODE — BEGAWIPENGADAAN (CODEIGNITER 4)
**Tanggal Pemeriksaan:** 23 Oktober 2026
**Ruang Lingkup:** Panel Administrasi (`app/Controllers/Admin/**`, 28 kontroler), layanan RBAC/Identity, konfigurasi (`App`, `Filters`, `Security`, `Routing`, `Routes`, `Session`, `Cookie`), filter autentikasi, model, view, JavaScript dashboard, serta probe integritas basis data **read-only** terhadap `db_begawipbj`.
**Metodologi:** Pemindaian regex lintas repo, penelusuran manual `file:line`, simulasi konfigurasi filter, dan kueri SQL langsung (SELECT/COUNT saja) untuk memverifikasi konsistensi RBAC.
**Sifat Audit:** **Read-only.** Tidak ada perubahan pada kode aplikasi. Skrip probe sementara telah dihapus setelah digunakan.

---

## 1. Ringkasan Eksekutif

Audit menemukan **3 temuan KRITIS**, **5 temuan TINGGI**, **4 temuan SEDANG**, dan **2 temuan RENDAH**. Masalah struktural paling mendasar adalah **ketiadaan proteksi CSRF di seluruh aplikasi** yang dikombinasikan dengan **ketiadaan otorisasi pada 25 dari 28 kontroler admin**.

Paling kritis: **CSRF dinonaktifkan secara global** (`Filters.php:80`) dan **0 dari 738 rute** mendeklarasikan filter, sehingga setiap aksi state-changing dapat dipicu oleh halaman pihak ketiga. Kondisi ini diperparah oleh **21 rute destruktif yang menggunakan HTTP GET** — yang tidak akan terlindungi meski CSRF kemudian diaktifkan.

Konteks kedua yang penting: basis data memuat **dua taksonomi role yang saling bertentangan** (`super_administrator` vs `superadmin`), sedangkan `AuthorizationService` hanya mengenali satu. Akibatnya enforcement otorisasi dan tampilan UI bisa **saling bertentangan** tergantung role row mana yang diberikan.

### Skor Kesehatan

| Dimensi | Skor | Dasar Penilaian |
| :--- | :---: | :--- |
| Autentikasi | 6/10 | Filter solid; hardening session lemah |
| Otorisasi (RBAC) | **2/10** | 25/28 kontroler admin tanpa authz |
| Proteksi CSRF | **0/10** | Nonaktif global, 0 filter rute |
| Validasi Input | 4/10 | `getPost()` diteruskan mentah di banyak jalur |
| Upload Berkas | 3/10 | Avatar tanpa validasi + SVG di webroot |
| Integritas Data | 4/10 | Taksonomi role konflik di DB |
| Sesi/Cookie | 5/10 | `secure=false`, `regenerateDestroy=false` |
| Kualitas Kode | 4/10 | `transComplete()` salah guna, bug logika |

---

## 2. Temuan KRITIS

> [!CAUTION]
> Ketiga temuan berikut harus diperbaiki sebelum aplikasi dideploy ke produksi.

### C1. Proteksi CSRF Sepenuhnya Absen
**Lokasi:** `app/Config/Filters.php:77-87`

```php
public array $globals = [
    'before' => [
        // 'honeypot',
        // 'csrf',
        // 'invalidchars',
    ],
```

**Bukti pendukung:**
- `app/Config/Routing.php:97` → `autoRoute = false` (bagus — mencegah bypass filter khas auto-routing).
- **0 dari 738 rute** di `app/Config/Routes.php` mendeklarasikan opsi `'filter'`.
- `app/Config/Security.php:18` → `$csrfProtection = 'cookie'` — **tidak sinkron** selama filter nonaktif.
- `app/Config/Security.php:27` → `$tokenRandomize = false` (token mudah diprediksi).

**Dampak:** Admin yang sedang login dapat dipaksa melakukan POST perubahan state hanya dengan membuka halaman pihak ketiga. Digabung dengan **H1** (rute destruktif GET), vektor ini menjadi sangat berbahaya.

**Perbaikan:**
1. Aktifkan `'csrf'` pada `$globals['before']` di `Filters.php:80`.
2. Ubah `$tokenRandomize` menjadi `true` di `Security.php:27`.
3. Tambahkan pengecualian per-rute **hanya** jika benar-benar diperlukan (form publik).

---

### C2. 25 dari 28 Kontroler Admin Tidak Memiliki Otorisasi
**Lokasi:** `app/Controllers/Admin/**`

Pemindaian `hasPermission|requirePermission|canAccess|isSuperAdmin|authorize|ensureRole|hasRole` atas seluruh folder hanya menemukan kecocokan pada **tiga** file:
`RoleController.php`, `PermissionController.php`, dan `UserController.php`.

| Kontroler | Metode | Sensitivitas |
| :--- | :---: | :--- |
| `Admin/UserProfileVerificationController.php` | 10 | Memverifikasi identitas pengguna |
| `Admin/ProcurementRoleController.php` | 12 | Menugaskan peran pengadaan |
| `Admin/RoleRequestReviewController.php` | 5 | Menyetujui permintaan peran → **jalur eskalasi** |
| `Admin/SettingController.php` | 16 | Konfigurasi sistem global |
| `Admin/ApprovalController.php` | 7 | Persetujuan (`approve()` L114, `reject()` L191) |
| `Admin/OrganizationController.php` | 8 | Siklus hidup unit organisasi |
| `Admin/NotificationController.php` | 8 | Broadcast pesan |
| `Admin/AuditController.php` | 5 | Log audit (risiko kebocoran/manipulasi) |
| `Admin/RegulationController.php` | 47 | Permukaan serangan terbesar |

**Gate satu-satunya** adalah `AuthenticationFilter` (`Filters.php:114-116`), yang hanya memverifikasi *autentikasi* dan akses dashboard — **bukan** *apa* yang boleh dilakukan pengguna.

```php
public array $filters = [
    'auth' => [
        'before' => ['dashboard', 'dashboard/*'],
    ],
```

Selain itu, `AuthorizationService::hasDashboardAccess()` (`app/Services/Identity/AuthorizationService.php:34-36`) bersifat sangat permisif:
```php
$permissions = (array) session()->get('permissions');
return (bool) array_filter($permissions, static fn (string $p): bool => str_contains($p, '.'));
```
Permission apa pun yang mengandung titik akan memberi akses dashboard penuh. **Tidak ada gate per-modul maupun per-aksi.**

**Dampak:** Akun dengan hak akses rendah (mis. `operator`, `trainer`) dapat menyetujui permintaan peran miliknya sendiri, mengubah pengaturan sistem, atau memverifikasi identitasnya sendiri.

**Perbaikan:** Tambahkan method `before()` pada `BaseAdminController` (semua 28 kontroler harus meng-extend) yang menegakkan peta permission per-metode, dan jadikan `RoleRequestReviewController` menolak persetujuan atas permintaan sendiri.

---

### C3. Integritas RBAC — Dua Taksonomi Role yang Bertentangan
Basis data memuat **dua kelompok role terpisah** dengan konvensi key berbeda, keduanya berstatus `is_system = 1`:

```
id=10 super_administrator | id=11 administrator | id=12 operator
id=13 trainer  | id=14 evaluator | id=15 peserta | id=16 public_user
--- seeded 2026-10-01 ---
id=20 superadmin | id=21 admin | id=22 user        --- seeded 2026-10-20
```

`AuthorizationService` hanya mengenali **kelompok pertama**:
- `isSuperAdmin()` → `in_array('super_administrator', ...)` (`AuthorizationService.php:15`)
- `isAdmin()` → `in_array('administrator', ...)` (`AuthorizationService.php:21`)

Sementara itu, kode lain memakai konvensi **kedua** atau keduanya:
- `app/Commands/ArenaDemoSeed.php:30-32` → men-seed dan memberi `superadmin`/`admin`
- `app/Views/main/workspace/components/sidebar.php:8-11` → memeriksa **kedua** konvensi
- `app/Controllers/Admin/UserController.php:331` → memfilter `['super_administrator', 'administrator']`
- `app/Commands/DatabaseCleanAudit.php:34` → daftar gabungan `['super_administrator','super_administrator','admin','administrator','superadmin']`

**Dampak:** Pengguna yang diberi role id=20 (`superadmin`) **tidak** dikenali `isSuperAdmin()`, sehingga UI RBAC di `RoleController`/`PermissionController`/`sidebar` tersembunyi untuk superadmin yang sah — sementara `ArenaDemoSeed` tetap memprovisinya. **Ketidakkonsisten authz sekaligus risiko lockout.**

**Tambahan:** permission `id=42` memiliki **key kosong** (`key: ""`, name "Test Codex Permission") — permission sistem yang cacat.

**Perbaikan:** Migrasikan ke satu kelompok kanonik, hapus id=20-22 (atau id=10-16), dan pastikan seeder memanggil resolver yang sama. Bersihkan permission id=42.

---

## 3. Temuan TINGGI

### H1. Aksi Destruktif pada Rute HTTP GET
**21 rute GET mengubah state.** Contoh representatif:

| Rute | Handler |
| :--- | :--- |
| `Routes.php:739` | `ContactMessageController::delete/$1` |
| `Routes.php:747-748` | `HelpdeskWidgetController::deleteTopic`, `toggleTopic` |
| `Routes.php:753` | `NewsletterController::delete/$1` |
| `Routes.php:859-860` | `SliderController::delete`, `toggle` |
| `Routes.php:869-870` | `AnnouncementController::delete`, `toggle` |
| `Routes.php:882-883` | `HighlightFeatureController::delete`, `toggle` |
| `Routes.php:891-892` | `ExternalPortalController::delete`, `toggle` |
| `Routes.php:900-901` | `TestimonialController::delete`, `toggle` |
| `Routes.php:909-910` | `TeamMemberController::delete`, `toggle` |
| `Routes.php:923-924` | `ServiceCategoryController::delete`, `toggle` |
| `Routes.php:931` | `SatisfactionSurveyAdminController::deleteResponse/$1` |

**Dampak:** `<img src=".../slider/hapus/5">` di halaman mana pun menghapus record 5. **Token CSRF tidak diperlukan**, sehingga perbaikan C1 **tidak** melindungi rute-rute ini. Link-prefetcher dan crawler dapat memicu secara tak sengaja.

**Perbaikan:** Konversi semua ke `POST` + `csrf`, atau minimal mewajibkan token CSRF sebagai query parameter.

---

### H2. Upload Avatar Pengguna Tanpa Validasi Sama Sekali
**Lokasi:** `app/Controllers/Admin/UserController.php:493-506`

```php
$file = $this->request->getFile('avatar');
if (! $file || ! $file->isValid() || $file->hasMoved()) { return true; }
$dir = WRITEPATH . 'uploads/users';
...
$stored = $file->getRandomName();
if (! $file->move($dir, $stored)) { return false; }
```

Tidak ada allow-list MIME, tidak ada cek ekstensi, tidak ada batas ukuran. Bandingkan dengan `RegulationController.php:1216` yang **melakukan** pemanggilan `getMimeType()`.

**Dampak:** Upload berkas sewenang-wenang (PHP, HTML) ke `writable/uploads/users`. Digabung dengan **H3**, berkas `.svg`/`.html` menjadi jalur staging stored-XSS/RCE.

**Perbaikan:** Terapkan allow-list MIME + whitelist ekstensi + batas ukuran (mis. 2 MB), dan tambahkan validasi `getClientExtension()`.

---

### H3. Upload SVG Diizinkan ke Webroot Publik
**Lokasi:** `app/Services/LandingImageUploadService.php:28-41`

```php
$allowedMimes = ['image/jpeg','image/png','image/webp','image/jpg','image/gif','image/svg+xml'];
```

Direktori target berada di bawah `FCPATH` (`LandingImageUploadService.php:49`) — yang berarti **langsung dilayani web**.

**Dampak:** Stored XSS. SVG yang berisi `<script>` dieksekusi same-origin saat dirender sebagai gambar dalam `<img>` tag (atau lewat navigasi langsung). Tidak ada CSP yang dikonfigurasi untuk mitigasi.

**Perbaikan:** Hapus `image/svg+xml` dari allow-list, dan sajikan upload dari direktori non-executable dengan `Content-Disposition: attachment` atau host aset terpisah.

---

### H4. Mass-Assignment — `$this->request->getPost()` Diteruskan Mentah
Beberapa kontroler menyerahkan `getPost()` langsung ke service:

| Lokasi | Pola |
| :--- | :--- |
| `Admin/OrganizationController.php:54, 118` | `->create($this->request->getPost(), ...)` |
| `Admin/PermissionController.php:85, 132` | `->updatePermission($permId, $this->request->getPost(), ...)` |
| `Admin/ProcurementRoleController.php:47, 106` | `(array) ($this->request->getPost() ?: $_POST)` |
| `Admin/ProfileController.php:98, 227` | `->skipValidation(true)->update($userId, [...])` |

**Dampak:** Field apa pun yang kebetulan dipersist oleh service dapat disetel klien, termasuk `is_system`, `key`, atau kolom kepemilikan. `PermissionController.php:132` **[KRITIS]** karena menulis ke tabel permission yang mengatur seluruh otorisasi aplikasi.

**Perbaikan:** Terapkan allow-list eksplisit per aksi dan tolak key yang tidak dikenal.

---

### H5. Sesi Rentan terhadap Fixation — `regenerateDestroy` Nonaktif
**Lokasi:**
- `app/Config/Session.php:93` → `public bool $regenerateDestroy = false;`
- `app/Config/Cookie.php:57` → `public bool $secure = false;`

Tidak ada konfigurasi regenerasi session saat login. Cookie session juga terkirim dalam cleartext.

**Perbaikan:**
1. `secure = true` di belakang TLS.
2. `samesite = 'Strict'` untuk area admin.
3. Panggil `session()->regenerate(true)` segera setelah login berhasil.

---

## 4. Temuan SEDANG

### M1. Cakupan Konfigurasi Produksi
- `.env` → `CI_ENVIRONMENT = development` dan `database.default.username = root` dengan **password kosong**. Jika file ini ter-deploy, dampaknya adalah compromise penuh basis data.
- Tidak ada CSP, `X-Frame-Options`, maupun `SecureHeaders` (`Filters.php:83-86` — `secureheaders` dikomentari).
- `app/Views/dashboard/layouts/dashboard.php` tidak menyetel security header apa pun.

### M2. `syncRolePermissions` Memakai `transComplete()` Tanpa Cek Status
**Lokasi:** `app/Services/Admin/RoleManagementService.php:171-192`

```php
$db->transStart();
$db->table('role_permissions')->where('role_id', $roleId)->delete();
...
$db->transComplete();   // nilai balik dibuang
```

Hasil transaksi tidak pernah diperiksa — insert yang gagal diam-diam meninggalkan role dengan **nol permission**, sementara audit log di L194 tetap mencatat sukses. Pola serupa ada di `ProfileChangeRequestService.php` dan `RoleRequestService.php` (masing-masing hanya 1 referensi transaksi — tanpa jalur rollback).

**Perbaikan:** Periksa `$db->transComplete()` / `transStatus()` dan bail out dengan pesan error bila gagal.

### M3. Query Tanpa Batas pada Tabel yang Tumbuh
`file_assets` (104 baris) dan `audit_logs` (219 baris) di-query tanpa batas paginasi di beberapa service. `audit_logs` tumbuh monotonik dan paling jarang di-prune, sehingga degradasi performa akan meningkat seiring waktu.

### M4. Data Uji pada Database Produksi
Role `20-22` dan permission `42-43` ("Test Codex Permission", domain `test`) adalah artefak seeding, bukan konfigurasi sah.

---

## 5. Temuan RENDAH

### L1. Bug Logika yang Selalu Benar
`app/Controllers/Admin/RegulationController.php:583`:
```php
'is_active' => isset($post['is_active']) ? 1 : 1,   // kedua cabang identik
```
Cabang `else` seharusnya `0`. Bandingkan dengan bentuk yang benar di `L658`. Akibatnya item **selalu** aktif.

### L2. PHP Notice pada Audit Tooling
`app/Commands/LandingCmsAuditTest.php:29, 39` dan `AdminNotificationTest.php:85, 91` mengandung referensi variabel undefined yang akan gagal di bawah `E_ALL` pada CI.

---

## 6. Temuan Positif (Sudah Bersih)

- **Tidak ada SQL injection terkonfirmasi.** Seluruh kontroler memakai query builder atau bound parameter; tidak ditemukan interpolasi string mentah pada kolom SQL.
- **Escaping output XSS konsisten.** Sweep `<?=` pada `app/Views/**` menunjukkan pemakaian `esc()` yang sistematis.
- **`autoRoute = false`** (`Routing.php:97`) dengan benar mencegah bypass filter khas auto-routing.
- **`AuthenticationFilter.php:39-47`** dengan benar mengecek ulang `account_status` dari basis data pada tiap request dan menghancurkan sesi saat akun dinonaktifkan.
- **Integritas referensial utuh.** `user_roles` tidak memiliki baris orphan dan tidak ada pengguna multi-peran.

---

## 7. Rencana Perbaikan (Prioritas)

| # | Tindakan | Temuan | Prioritas |
| ---: | :--- | :---: | :--- |
| 1 | Aktifkan CSRF global + `tokenRandomize = true` | C1 | P0 |
| 2 | Tambah `BaseAdminController` dengan permission per-metode; tolak self-approval di `RoleRequestReviewController` | C2 | P0 |
| 3 | Konversi 21 rute destruktif GET ke POST + CSRF | H1 | P0 |
| 4 | Resolusi taksonomi role — satu kelompok kanonik, bersihkan id=20-22 & permission id=42 | C3 | P0 |
| 5 | Harden upload — hapus SVG, validasi MIME/ukuran avatar, pindahkan upload keluar webroot | H2, H3 | P1 |
| 6 | Ganti `getPost()` mentah dengan allow-list eksplisit | H4 | P1 |
| 7 | Hardening sesi — `secure=true`, `SameSite=Strict`, `regenerate(true)` saat login | H5 | P1 |
| 8 | Perbaiki `.env` — environment produksi, user DB non-root, rotasi kredensial bila sempat ter-deploy | M1 | P1 |
| 9 | Periksa nilai balik `transComplete()` di service terkait | M2 | P2 |
| 10 | Perbaiki ternary `RegulationController.php:583` | L1 | P2 |
| 11 | Tambahkan pagination pada query `audit_logs` / `file_assets` | M3 | P2 |
| 12 | Aktifkan `SecureHeaders` (CSP, X-Frame-Options) | M1 | P2 |
| 13 | Bersihkan artefak data uji (role 20-22, permission 42-43) | M4 | P3 |
| 14 | Perbaiki variabel undefined pada command audit | L2 | P3 |

---

## 8. Catatan Metodologis & Asumsi

- **Akses DB:** Temuan RBAC (C3, M4) berasal dari kueri **read-only** langsung ke `db_begawipbj` (SELECT/COUNT, tanpa write). Skrip probe `scratch/_audit_rbac.php` bersifat sementara dan **sudah dihapus** setelah digunakan.
- **Jika database tersebut tidak mewakili target deployment Anda**, temuan C3 dan M4 perlu diverifikasi ulang.
- **Status workspace:** Tidak ada perubahan kode aplikasi yang dilakukan selama audit ini. Modifikasi yang sudah ada sebelumnya pada `app/Controllers/Landing/HomeController.php` dan `app/Views/landing/components/satisfaction_survey_modal.php` **tidak berkaitan** dengan audit ini dan dibiarkan apa adanya.
- **Counting reference:** Angka "21 rute GET destruktif" dan "25/28 kontroler tanpa authz" dihitung otomatis via regex, namun setiap temuan telah diverifikasi ulang secara manual terhadap `file:line` sebelum dicantumkan.

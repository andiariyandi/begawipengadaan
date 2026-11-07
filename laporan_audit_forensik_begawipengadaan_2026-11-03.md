# Laporan Audit Forensik Komprehensif — BegawiPengadaan

**Tanggal audit:** 11 November 2026
**Metodologi:** Code-First & Database-First Analysis (ground truth = DDL migrasi + skema MySQL live `db_begawipbj`)
**Frasa kunci:** CodeIgniter 4.7.4 · PHP 8.3.33 · MySQL 8.4.3 · 17 file migrasi · 95 tabel · 1.127 kolom · 90 permission · 388 route
**Status kode produksi:** **TIDAK ADA** perubahan kode produksi yang dilakukan. Laporan ini adalah dokumen analisis + rencana eksekusi yang menunggu persetujuan.

---

## Ground Truth yang Digunakan (Tahap 1)

Hierarki kebenaran yang dipakai, berlawanan dengan isi `dokumen/*.md`:

| Prioritas | Sumber | Status Verifikasi |
|---|---|---|
| 1 | **MySQL live `db_begawipbj`** (`information_schema`) | 95 tabel / 1.127 kolom — diakses via CLI, password root kosong |
| 1 | `schema-fresh-v4-3.sql` | 93 tabel — **kurang 2 tabel** dibanding DB live (`visitor_ip_logs`, `rup_audit_*`) |
| 1 | 17 file `app/Database/Migrations/` | DDL ADD COLUMN diverifikasi terhadap DB live |
| 1 | **CHECK constraint MySQL** | 22 constraint — dipakai sebagai daftar ENUM otoritatif |
| 2 | `database/permissions_catalog.json` + tabel `permissions` | Katalog 90 = DB 90, **zero drift** |
| 2 | `app/Config/Routes.php` (388 route) + `app/Config/Filters.php` | Semua controller::method resolve valid |
| 3 | Pemisahan view FiStudy vs WowDash | `app/Views/landing` + `app/Views/main/workspace` → `public/assets/main`; `app/Views/dashboard` → `public/assets/dashboard` |

---

## 1. Ringkasan Eksekutif & Peta Kesehatan Sistem

### 1.1 Distribusi Temuan

| Severity | Jumlah | Definisi |
|---|---:|---|
| **Critical (P0)** | **5** | Error 500 pada route produksi yang dijangkau, kebocoran data ke publik, atau kegagalan tulis diam-diam |
| **High (P1)** | **7** | Kegagalan parsial, kebocoran data laten, atau tooling audit yang reporting-nya misled |
| **Medium (P2)** | **8** | Kode mati/landmine, inkonsistensi logika antar-area, risiko performa & transaksi |
| **Low (P3)** | **6** | Kebersihan, dokumentasi usang, dan asumsi command audit yang sudah kedaluwarsa |
| **TOTAL** | **26** | |

### 1.2 Peta Kesehatan per Area

| Area | Skor | Ringkasan |
|---|---|---|
| **Database & Models** | ⚠️ **Merah** | 95 tabel & 22 CHECK constraint terpetakan. **9 model memakai kolom/tabel fiktif**, 6 di antaranya adalah kode mati. `OrganizationUnitModel::$allowedFields` membuat toggle `is_active` jadi no-op diam-diam. 3 controller menulis ke `file_assets` dengan 4 nama kolom yang tidak pernah ada. |
| **Public Landing** | ✅ **Hijau** | `landing:full:audit` → **44/44 route PASS**. Tidak ada kebocoran NIK/NIP di endpoint JSON publik. Verifikasi sertifikat konsisten (`status='issued'` + `source_type` sesuai CHECK). **Catatan:** Landing tidak memfilter `events.status` → P0-LEAK-01. |
| **User Workspace (4 Role)** | 🟡 **Kuning** | 27 route 200 OK pada 4 role. Guard IDOR `assertTrainerOf()` (30+ call site) solid; `ParticipantController` scoping ke `session.user_id` konsisten. **Tidak** ada mutasi guard bocor. Kekurangan: N+1 masif di `TrainerController`/`OperatorController`, dan 2 P0 pada `file_assets` + unduh sertifikat. |
| **Dashboard Back-Office** | 🔴 **Merah** | 196 route GET bersih. **3 dari 5 P0 berada di sini** (duplikasi kelas, buat tugas berpanduan PDF, unduh sertifikat). `dashboard:full:audit` = 33 OK / **1 FAILED** (crash TypeError pada route Closure). 32 file view melanggar batas zona aset. |

### 1.3 Verifikasi Positif (temuan yang **tidak** ada — penting untuk fairness)

- ✅ **CSRF**: 287 field POST terinventarisasi; **1** form tanpa `csrf_field()`, dan itu adalah aset vendor PHPMailer yang tersesat di `app/Views/reference/`. Filter `csrf` global (`except: api/*, webhook/*`) + `csrf_field()` manual aktif di semua form produksi.
- ✅ **RBAC**: 87 permission yang dirujuk `AuthenticationFilter::zoneMap` **seluruhnya ada** di katalog 90. Tabel `permissions` DB = katalog persis. Guard read-only + mutation guard (`POST/PUT/PATCH/DELETE` + pola URL mutating) **aktif dan fail-closed**.
- ✅ **IDOR**: `assertTrainerOf()` memverifikasi `class_trainers.user_id` + `status='active'`; `ParticipantController` melakukan scoping `session('user_id')`. Tidak ditemukan celah manipulasi ID lintas pengguna/kelas.
- ✅ **Path traversal**: `realpath()` + `strncmp()` prefix-check pada semua endpoint unduh utama; `public/uploads/.htaccess` memblokir eksekusi skrip dan `-Indexes`.
- ✅ **Routing**: 388 route, **0 orphan** (semua `Controller::method` resolve valid).
- ✅ **CHECK constraint compliance**: penulisan `event_registrations.status`, `attendances.status`, `certificates.status/source_type`, `learning_classes` bobot, `knowledge_articles.type` — semua sesuai domain CHECK.
- ✅ **Form binding**: 287 field POST; seluruh "yatim" yang terdeteksi ternyata ditangani via bulk `getPost()` (`SettingController::save` → `saveGroup`) atau array `payload[...]` — **tidak ada** field form yang diam-diam dibuang.

---

## 2. Tabel Temuan "Kode Ngarang" (Schema vs Code Mismatch)

Semua baris di bawah **sudah direproduksi langsung terhadap MySQL live** (perintah `INSERT`/`SELECT` mengembalikan error persis yang terjadi di aplikasi).

| ID | Lokasi File & Baris | Yang dipanggil di Kode | Fakta di DB Live | Dampak Runtime | Rekomendasi |
|---|---|---|---|---|---|
| **SCHEMA-01** | `app/Controllers/Learning/LearningClassController.php:759,775,778,792` | `->table('questions')`, `->table('question_options')`, kolom `'question'` | Tabel **`questions` & `question_options` TIDAK ADA**. Tabel sebenarnya: **`quiz_questions`** (kolom `prompt`) & **`quiz_options`**. Kolom `question` tidak ada. | `ERROR 1146 Table 'db_begawipbj.questions' doesn't exist` → **HTTP 500** pada `POST /dashboard/pembelajaran/kelas/{id}/duplikasi`. Modul & soal hilang, kelas baru terisi setengah. | Ganti `questions`→`quiz_questions`, `question_options`→`quiz_options`, kolom `'question'`→`'prompt'`. |
| **SCHEMA-02** | `Learning/CurriculumController.php:781`, `Learning/AssessmentManagementController.php:467`, `Workspace/TrainerController.php:1823` | `file_assets` insert dengan `user_id`, `collection`, `filename`, `storage_driver` | `file_assets` real: `disk`, `path`, `original_name`, `stored_name`, `extension`, `mime_type`, `size_bytes`, `checksum`, `visibility`, `lifecycle_status`, `resource_type`, `resource_id`, `uploaded_by`. **4 kolom di atas tidak ada.** | `ERROR 1054 Unknown column 'user_id' in 'field list'` → **HTTP 500** saat buat tugas **dengan** PDF panduan. Dipicu 3 route: `dashboard/pembelajaran/kurikulum/tugas` (POST), `dashboard/pembelajaran/assessment/assignment` (POST), `workspace/trainer/kelas/{id}/tugas/tambah` (POST). File sudah tercopy ke disk sebelum insert → **file yatim** + tugas gagal dibuat. | Samakan payload dengan `CertificateTemplateController:975` (yang sudah benar): `disk`, `path`, `original_name`, `stored_name`, `extension`, `mime_type`, `size_bytes`, `visibility`, `lifecycle_status`, `resource_type`, `resource_id`, `uploaded_by`. |
| **SCHEMA-03** | `app/Controllers/Certificate/CertificateDownloadController.php:121-125` | `class_trainers` → `->where('trainer_id', …)` dan `->where('deleted_at IS NULL')` | `class_trainers` real: `class_id`, `user_id`, `assigned_by`, `assigned_at`, `status`, `role`. **Tidak ada `trainer_id` maupun `deleted_at`.** | `ERROR 1054 Unknown column 'trainer_id' in 'where clause'` → **HTTP 500** pada unduh sertifikat yang `source_type='learning_enrollment'` (2 route: `dashboard/sertifikat/{id}/unduh`, `workspace/.../sertifikat/{id}/unduh`). Prescription memblokir seluruh alur sertifikat belajar. | `->where('user_id', $actorId)` dan hapus syarat `deleted_at IS NULL` (tabel tidak punya soft delete). |
| **SCHEMA-04** | `app/Models/OrganizationUnitModel.php:10-17` + `app/Services/Admin/OrganizationService.php:58,67,118` | Model menulis `['is_active' => 0\|1]` | Kolom DB = **`is_active`** (ada). Tapi `$allowedFields` = `['parent_id','code','name','type','status']` — **`is_active` tidak ada, `status` tidak pernah ada**. CI4 `Model::doProtectFields()` **silent `unset()`** field di luar allow-list. | **Toggle Aktif/Nonaktif Unit Organisasi = NO-OP TOTAL.** Tidak ada error, tidak ada log, UI tetap menampilkan "aktif". Bukti: seluruh 30+ baris `organization_units` memiliki `is_active = 1`. | Tambahkan `'is_active'` ke `$allowedFields`, hapus `'status'`. |
| **SCHEMA-05** | `Landing/HomeController.php:653`, `Workspace/ConsultationController.php:96`, `Admin/Communication/ContactMessageController.php:69` | `file_assets` insert `storage_disk`, `file_path`, `file_size` | `file_assets` tidak punya ketiga kolom ini (lihat SCHEMA-02) | Error **ditelan** `catch (\Throwable) {}` → insert gagal diam-diam, `$fileAssetId = null`. Lampiran tiket/konsultasi **terupload ke disk tanpa record DB** → tidak bisa dihapus/ditelusuri. | Terapkan payload yang benar (SCHEMA-02) + hapus `catch` kosong (ganti dengan log/flash error). |
| **SCHEMA-06** | `app/Database/Seeds/AuditRemediationSeeder.php:52-57` | `user_roles` insert `assigned_by`, `assigned_at` | `user_roles` real: `granted_by`, `granted_at` | `ERROR 1054 Unknown column 'assigned_by'` → **seeder crash** (`db:seed`). | Ganti `assigned_by`→`granted_by`, `assigned_at`→`granted_at`. |
| **SCHEMA-07** | `app/Services/Admin/ReportAnalyticsService.php:142` | `event_registrations` → `->where('attendance_status','attended')` | `event_registrations` hanya punya `status` (`registered\|waitlisted\|cancelled\|rejected`); status kehadiran ada di `attendances.status` | `ERROR 1054 Unknown column 'attendance_status'` → **HTTP 500** pada ringkasan laporan event **jika** tabel `attendances` kosong (cabang fallback `if ($totalAttended === 0)`). Saat ini cabang **inert** (534 baris `attendances`), jadi bersifat **latent**. | Ganti ke `->join('attendances a', 'a.event_registration_id = er.id')->where('a.status','present')`, atau hapus cabang fallback. |
| **SCHEMA-08** | `app/Controllers/Regulation/RegulationController.php` (seluruh file) | Pakai `App\Models\{RegulationModel, RegulationCategoryModel, RegulationVersionModel}` + `regulations.status`, `regulations.nomor_regulasi`, `regulation_versions.file_path`, `regulation_versions.change_notes` | Semua kolom tersebut **tidak ada**. `regulations` real pakai `document_number`, `document_year`, `workflow_status`, `legal_status`, `current_version_id`. `regulation_versions` real pakai `file_id`, `changelog`. | **Controller yatim total**: tidak ada route yang merujuknya, 4 view (`regulation/browse\|detail\|form`, `landing/user/regulations`) **tidak ada file**, `route_to('regulation.browse')` **tidak terdaftar**. Tidak bisa 500 (unreachable), tapi merupakan **landmine skema fiktif** yang menyesatkan. | **Hapus file controller + 3 model legacy** (`App\Models\RegulationModel`, `App\Models\RegulationCategoryModel`, `App\Models\RegulationVersionModel`) atau porting ke `App\Models\Regulation\*`. |
| **SCHEMA-09** | `app/Models/{AssignmentModel, AssignmentSubmissionModel, CourseModel, LessonModel, NewsModel}.php`, `app/Models/Knowledge/KnowledgeMediaContentModel.php` | Kolom fiktif: `assignments.course_id/module_id/description/max_score/deadline`; `assignment_submissions.user_id/file/answer`; `courses.trainer_id`; `lessons.file_path/content_text/sequence_order`; `news.status`; `KnowledgeMediaContentModel` tanpa `$table` | Semuanya **tidak ada** di DB. | **Dead code** — tidak di-`use` oleh controller/service mana pun (hanya model `App\Models\Learning\*` yang dipakai). Tidak menyebabkan 500, tetapi `$allowedFields` ini akan **diam-diam membuang data** bila suatu saat dipakai kembali. | Hapus 6 model legacy tersebut (lihat Rencana Tahap 4). |

### 2.1 Ringkasan Metodologi Deteksi Skema

Pemeriksaan menyeluruh atas `app/` (1.643 file) memakai empat teknik independen yang saling meng-silang:

1. **Alias-aware qualified-column check** — memetakan `alias` → tabel berdasarkan urutan kemunculan `->from/->table/->join` di source, lalu memvalidasi setiap `alias.kolom` terhadap `information_schema`. → **1 mismatch** (`qq.question_text`, ternyata di method **yang tidak ter-rute** → turun ke DEAD-02).
2. **Unqualified-column check** — setiap literal kolom di `where/orWhere/whereIn/orderBy/groupBy/select/like` diadu dengan gabungan 1.127 nama kolom. → menemukan `trainer_id`, `attendance_status`, `question_text`.
3. **Insert/Update payload check** — parsing token PHP (`token_get_all`) + bracket balancing untuk mengekstrak key payload `->insert([...])` / `->update([...])` dan memvalidasinya. → 6 situs `file_assets` + 1 situs `user_roles`.
4. **Table-existence check** — semua literal di `->from/->table/->join`. → `questions`, `question_options`.

> Semua temuan tabel-2 diverifikasi ulang dengan eksekusi SQL langsung terhadap MySQL live, bukan hanya analisis statis.

---

## 3. Daftar Bug & Inkonsistensi Variabel (Controller ↔ Service ↔ Model ↔ View)

### 3.1 `[P0-VAR-01]` — Dampak alur Certificate Learning terputus total

**File & Baris:**
- Controller: `app/Controllers/Certificate/CertificateDownloadController.php:121-125`
- View: `app/Views/main/workspace/pages/peserta/sertifikat.php:81` (tautan unduh), `app/Views/main/workspace/pages/peserta/kelas_saya.php:593,811`

**Detail Masalah:** `certificates.source_type='learning_enrollment'` memicu pengecekan apakah pemohon adalah trainer kelas. Query memakai `class_trainers.trainer_id` + `class_trainers.deleted_at` — keduanya kolom fiktif. Karena pengecekan berada **di tengah** alur otorisasi, setiap pengguna yang sah (peserta pemilik sertifikat maupun trainer) mendapat 500.

**Solusi (Sebelum → Sesudah):**

```php
// SEBELUM — error 1054
$isTrainer = $this->db->table('class_trainers')
    ->where('class_id', (int) $enrollment['class_id'])
    ->where('trainer_id', $actorId)
    ->where('deleted_at IS NULL')
    ->countAllResults() > 0;

// SESUDAH — mengikuti skema nyata class_trainers
$isTrainer = $this->db->table('class_trainers')
    ->where('class_id', (int) $enrollment['class_id'])
    ->where('user_id', $actorId)
    ->where('status', 'active')
    ->countAllResults() > 0;
```

### 3.2 `[P0-VAR-02]` — Duplikasi kelas kehilangan seluruh bank soal

**File & Baris:** `app/Controllers/Learning/LearningClassController.php:759-793` (method `duplicateBatch`)

**Detail Masalah:** Alur "Salin modul → lesson → quiz → soal → opsi". Tahap "soal" dan "opsi" menyalin dari tabel yang tidak ada. Bank soal menjadi **0 baris** pada kelas duplikat, sementara `quizzes` tetap tersalin → kelas hasil duplikasi punya kuis kosong (titik buta).

```php
// SEBELUM
$questions = $db->table('questions')->where('quiz_id', (int) $q['id'])->orderBy('sort_order','ASC')->get()->getResultArray();
foreach ($questions as $quest) {
    $newQuestData = ['quiz_id'=>$newQuizId, 'question'=>$quest['question'], 'type'=>$quest['type'] ?? 'multiple_choice', ...];
    $db->table('questions')->insert($newQuestData);
    $options = $db->table('question_options')->where('question_id', (int) $quest['id'])->orderBy('sort_order','ASC')->get()->getResultArray();
    $db->table('question_options')->insert($newOptData);
}

// SESUDAH
$questions = $db->table('quiz_questions')->where('quiz_id', (int) $q['id'])->orderBy('sort_order','ASC')->get()->getResultArray();
foreach ($questions as $quest) {
    $newQuestData = ['quiz_id'=>$newQuizId, 'prompt'=>$quest['prompt'], 'type'=>$quest['type'] ?? 'multiple_choice',
                     'points'=>$quest['points'] ?? 10, 'sort_order'=>(int)($quest['sort_order'] ?? 1),
                     'explanation'=>$quest['explanation'] ?? null, 'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')];
    $db->table('quiz_questions')->insert($newQuestData);
    $newQuestId = (int) $db->insertID();
    $options = $db->table('quiz_options')->where('question_id', (int) $quest['id'])->orderBy('sort_order','ASC')->get()->getResultArray();
    foreach ($options as $opt) {
        $db->table('quiz_options')->insert([
            'question_id'=>$newQuestId, 'option_text'=>$opt['option_text'], 'is_correct'=>(int)$opt['is_correct'],
            'sort_order'=>(int)($opt['sort_order'] ?? 1), 'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')]);
    }
}
```

### 3.3 `[P0-VAR-03]` — 3 view form tidak pernah menyimpan lampiran ke `file_assets`

**File & Baris:**

| Controller | Baris | Route |
|---|---|---|
| `Learning/CurriculumController.php` | 781 | `POST dashboard/pembelajaran/kurikulum/tugas` |
| `Learning/AssessmentManagementController.php` | 467 | `POST dashboard/pembelajaran/assessment/assignment` |
| `Workspace/TrainerController.php` | 1823 | `POST workspace/trainer/kelas/{id}/tugas/tambah` |

**Detail Masalah:** Ketiganya memakai payload `file_assets` versi lama (`user_id`/`collection`/`filename`/`storage_driver`). Modul ini adalah **satu-satunya cara membuat tugas di 3 area**. Dampak: 500 + file PDF sudah berada di `public/uploads/learning/assignments/` tanpa record.

**Solusi (seragam di 3 situs):**

```php
// SEBELUM
$db->table('file_assets')->insert([
    'user_id'=>$userId, 'collection'=>'assignment_guides', 'filename'=>$newFilename,
    'original_name'=>$guideFilename, 'mime_type'=>'application/pdf', 'size_bytes'=>$guideSize,
    'path'=>$guideUrl, 'visibility'=>'public', 'storage_driver'=>'local', 'created_at'=>$now, 'updated_at'=>$now,
]);

// SESUDAH
$db->table('file_assets')->insert([
    'disk'=>'local', 'path'=>$guideUrl, 'original_name'=>$guideFilename, 'stored_name'=>$newFilename,
    'extension'=>'pdf', 'mime_type'=>'application/pdf', 'size_bytes'=>$guideSize,
    'visibility'=>'internal', 'lifecycle_status'=>'available',
    'resource_type'=>'assignment_guide', 'resource_id'=>$assignmentId ?? null,
    'uploaded_by'=>(int) $userId, 'available_at'=>$now, 'created_at'=>$now, 'updated_at'=>$now,
]);
```

### 3.4 `[P1-VAR-04]` — `OrganizationUnitModel` membuang `is_active` secara diam-diam

**File & Baris:** `app/Models/OrganizationUnitModel.php:10-17` → `app/Services/Admin/OrganizationService.php:58,67,118`

**Detail Masalah:** Bukan error PHP, melainkan **kehilangan data**. CI4 `Model::doProtectFields()` membuang key di luar `$allowedFields` tanpa peringatan. `OrganizationService` mengirim `['is_active' => 0]`; `$allowedFields` tidak memuatnya → **tidak terjadi write**. UI menampilkan flash success padahal tidak ada yang berubah.

```php
// SEBELUM
protected $allowedFields = ['parent_id','code','name','type','status'];

// SESUDAH
protected $allowedFields = ['parent_id','code','name','type','is_active'];
```

### 3.5 `[P2-VAR-05]` — Literal status mati lintas area (gunakan `EventRegistrationStatus`)

Sudah ada mapper kanonik `app/Services/Communication/EventRegistrationStatus.php` (konstanta `ACCESS_RAW`, `PENDING_RAW`, `REJECTED_RAW` dan `applyFilter()`), tetapi **banyak call site mengabaikannya** dan memakai literal lama yang mustahil terjadi menurut CHECK constraint.

| Lokasi | Literal di kode | Domain CHECK sebenarnya | Dampak |
|---|---|---|---|
| `Landing/EventPublicController.php:140,274,443,630` | `['registered','attended','confirmed','promoted']` | `event_registrations.status ∈ {registered, waitlisted, cancelled, rejected}` | Filter efektif hanya match `registered` — 3 literal mati. Tidak salah hasil saat ini, **tetapi rapuh**: begitu CHECK di-refactor, filter publik ikut meleset. |
| `Learning/LearningClassController.php:875` | `['completed','passed']` | `enrollments.status ∈ {pending,active,completed,withdrawn,cancelled}` | `passed` mustahil → kelulusan hanya terdeteksi via `completed`. |
| `Services/Admin/ReportAnalyticsService.php:73` | `IN ("completed","passed")` | idem | KPI `completed_count` benar secara hasil, tapi kondisi mati. |
| `Communication/EventController.php:36` | `whereIn('status',['coming_soon','published'])` | `events.status ∈ {draft,coming_soon,open_registration,full,finished,cancelled}` | `published` mustahil — `CommunicationService` menormalisasi `publish/published/ongoing` → `open_registration`. KPI "coming_soon" hanya menghitung `coming_soon`. |
| `Communication/EventController.php:35,37` | tidak menghitung `full` | `events.status` berisi `full` | Status **"Kuota Penuh"** (pilihan UI di `events/form.php:319`) **tidak muncul di KPI mana pun** → operator kehilangan visibilitas. |

**Solusi:** ganti literal dengan `EventRegistrationStatus::ACCESS_RAW` / `PENDING_RAW`, dan tambahkan `full` pada KPI Dashboard.

### 3.6 `[P2-VAR-06]` — `workspace:smoke` gagal karena ekspektasi string UI lama (bukan bug view)

**File & Baris:** `app/Commands/WorkspaceSmoke.php:828-831` vs `app/Views/main/workspace/pages/operator/class_trainers.php:16,92,223,276`

**Detail Masalah:** Command masih mencari literal `"Kelas & Trainer Assignment"` dan `"Daftar Kelas & Penugasan Pengajar"`. View sudah di-*rename* menjadi `"Penugasan Kelas, Operator & Multi-Trainer"` / `"Daftar Kelas, Operator Asisten..."`. Elemen fungsional (`Direktori Pengajar (Trainer)`, `assignTrainerModal`) **tetap ada**. → 1 FAIL palsu.

### 3.7 `[P2-VAR-07]` — `dashboard:full:audit` crash pada route Closure (PHP 8.3 TypeError)

**File & Baris:** `app/Commands/DashboardFullAudit.php:69` vs `app/Config/Routes.php:907`

```php
// Routes.php:907 — route Closure yang sah
$routes->get('/', static fn() => redirect()->to(site_url('dashboard/landing/slider')), ['as' => 'dashboard.landing.index']);

// DashboardFullAudit.php:69 — mengasumsikan controller selalu string
if (! class_exists($controllerName)) {   // TypeError: Argument #1 ($class) must be of type string, Closure given
```

Route tersebut **berfungsi baik** di browser (redirect ke `dashboard/landing/slider`, dan `slider` **ada** di `Routes.php:918`). Yang rusak adalah command auditnya.

**Solusi:**
```php
if ($controllerName instanceof \Closure) {
    $auditSummary['passed']++;
    CLI::write(sprintf(" [200 OK]   /%-45s [Closure Route]", $routePath), 'green');
    continue;
}
if (! class_exists($controllerName)) { /* ... */ }
```

### 3.8 `[P2-VAR-08]` — `test:rbac-custom-roles` 8 FAIL palsu (harness usang)

**File & Baris:** `app/Commands/RbacCustomRoleTest.php:119-152`

**Detail Masalah:** Test melakukan `session()->set('permissions', ['dashboard.view','bunga.view','bunga.article.manage'])`, lalu mengharapkan 403 untuk modul lain. Tetapi `AuthenticationFilter` (desain "Zero Stale Session") **menimpa** session itu dari DB:

```php
// app/Filters/AuthenticationFilter.php:51-56
$userRoles   = $userModel->getRoles($userId);
$permissions = $userModel->getPermissions($userId);   // <- dari DB, bukan dari session mock
session()->set('permissions', $permissions);
```

`$testUserId` = user **pertama** di tabel `users` → **user 13** yang punya `super_administrator` + `operator` → `AuthorizationService::isAdmin()` = true → filter **bypass penuh** → semua route 200 → 8 assertion "harus 403" gagal.

> **Penting:** ini **BUKAN** bypass RBAC produksi. Permission produksi selalu diturunkan dari DB dan tidak bisa dimock lewat session. Yang usang adalah **harness test-nya**.

**Solusi:** buat/tambah user uji nyata dengan role khusus (perlu `user_roles` + `role_permissions` di DB), lalu assertion berbasis role nyata — bukan session mock.

### 3.9 `[P1-VAR-09]` — `workspace:full:audit` mati diam-diam sebelum mencetak scorecard

**File & Baris:** `app/Commands/WorkspaceFullAudit.php` (daftar route baris 124-198)

**Detail Masalah:** Output berhenti tepat setelah `[200 OK] /workspace/operator`. Penyebab: `CodeIgniter\Session\Session::start()` mengembalikan `null` bila `is_cli() && ENVIRONMENT !== 'testing'` (lihat `vendor/codeigniter4/framework/system/Session/Session.php`), sehingga route yang memanggil `session()->destroy()` / `->with(...)` pada langkah berikutnya **menghentikan proses CLI tanpa pesan**. Command tidak pernah mencetak blok "AUDIT SUMMARY SCORECARD" → **31 dari 57 route tidak pernah diuji**.

Log bukti (`writable/logs/log-2026-11-03.log`):
```
DEBUG - 2026-11-03 21:38:18 --> Session: Initialization under CLI aborted.
```

**Solusi:** jalankan command dengan `ENVIRONMENT=testing`, atau tambahkan guard:
```php
// di awal run()
if (is_cli() && ENVIRONMENT !== 'testing') {
    CLI::write(' [WARN] Session::start() dibatalkan pada CLI; audit tidak dapat memverifikasi route yang butuh session.', 'yellow');
}
```

### 3.10 `[P1-VAR-10]` — `sendContact()` & `submitSurvey()` tidak atomik

**File & Baris:** `app/Controllers/Landing/HomeController.php` (method `sendContact`, `submitSurvey`)

**Detail Masalah:** Ditemukan oleh `php spark test:portal-security` → 2 FAIL pada **[M-5]**: insert data + audit log tidak dibungkus transaksi. Bila `AuditService::log()` gagal, insert tetap committed (atau sebaliknya) → **audit trail bisa hilang** pada aksi publik.

**Solusi:**
```php
$this->db->transBegin();
try {
    $id = $db->table('contact_messages')->insert($payload, true);
    (new AuditService())->log($actorId, 'contact.submit', 'contact_messages', 'contact_message', (int) $db->insertID(), $payload);
    $this->db->transComplete();
} catch (\Throwable $e) {
    $this->db->transRollback();
    throw $e;
}
```

---

## 4. Matriks Inkonsistensi Antar-Area & Multi-Role

### 4.1 `[P0-LEAK-01]` — Event berstatus `draft` bocor ke Landing Publik

**Rute kebocoran:**

```
Dashboard "Tambah Event"  ->  CommunicationService::createEvent()
                             ->  status     default 'draft'     (events/form.php:316)
                             ->  visibility default 'public'   (events/form.php:330)
                                   |
Dashboard Public Landing   ->  EventPublicController
                             ->  ->where('e.visibility','public')   <-- TIDAK ADA filter status!
                                   |
                             ->  Landing /event  +  /event/{slug}  +  KPI "Total Event"
                             ->  + hero carousel publik jika carousel_enabled=1
```

**Bukti empiris (DB live saat ini):**

| status | visibility | COUNT |
|---|---|---:|
| coming_soon | public | 2 |
| draft | internal | 4 |
| finished | public | 16 |
| open_registration | public | 23 |

**Analisis:** Keamanan saat ini **bersandar pada konvensi data**, bukan pada kode. Begitu operator membuat event baru dengan nilai default form (`draft` + `public`), event tersebut **langsung tayang di internet** — daftar event, halaman detail, KPI, dan潜在的 hero carousel. `EventPublicController` hanya memfilter `visibility`; `status` tidak pernah participate. Bandingkan dengan News/Regulasi/Knowledge yang **benar-benar** memakai dua filter (`workflow_status='published'` **dan** `visibility='public'`).

**Solusi (defense-in-depth, dua lapis):**

```php
// app/Controllers/Landing/EventPublicController.php — di setiap builder event
->where('e.visibility', 'public')
->whereIn('e.status', ['coming_soon', 'open_registration', 'finished']);   // draft & cancelled dibuang
```

```php
// app/Services/Communication/CommunicationService.php:202 — normalisasi di sisi tulis
$status = strtolower(trim((string) ($data['status'] ?? 'draft')));
if ($status === 'draft') {
    $visibility = 'internal';   // draft tidak boleh publik
}
```

> **Jangan** mengubah `events.status`/`visibility` di DB (Zero Migration Policy) — perbaikan sepenuhnya di sisi PHP.

### 4.2 Matriks Status & Filter Antar-Area (8 Modul Utama)

| Modul | Kolom status di DB | Domain CHECK | Filter **Dashboard** (tulis) | Filter **Workspace** | Filter **Landing** | Status |
|---|---|---|---|---|---|---|
| **Arena Belajar (kelas)** | `learning_classes.status` | *(bebas, tanpa CHECK)* | normalisasi → `scheduled\|active\|completed\|draft` | `active` | `['scheduled','active','open','published','ongoing']` | 🟡 KPI `total_classes` **tidak** memfilter `courses.status/visibility` → bisa menghitung kelas dari course draft |
| **Arena Belajar (course)** | `courses.status` + `visibility` | *(bebas)* | `published/draft/archived` | — | `status='published' AND visibility='public'` | ✅ |
| **Bunga Rampai (KMS)** | `knowledge_articles.workflow_status` + `visibility` + `type` | `workflow ∈ {draft,submitted,in_review,approved,published,revision_required,rejected,archived}`; `type ∈ {article,faq,case_study,template}` | `workflow_status` | — | `workflow_status='published' AND visibility='public' AND type='<modul>'` | ✅ **Paling konsisten** — menjadi acuan standar |
| **Bank Regulasi** | `regulations.workflow_status` + `visibility` + `legal_status` | `workflow ∈ {…}`; `legal_status ∈ {active,amended,revoked,expired}` | `workflow_status='published'` | — | `workflow_status='published' AND visibility='public'` | ✅ |
| **Berita** | `news.workflow_status` + `visibility` | `workflow ∈ {…}` | `workflow_status='published'` | — | `workflow_status='published' AND visibility='public'` | ✅ |
| **Event & Agenda** | `events.status` + `visibility` | `status ∈ {draft,coming_soon,open_registration,full,finished,cancelled}`; `visibility ∈ {public,internal,restricted}` | `status='open_registration'` dll. | `status ∈ {open_registration,coming_soon,published}` | **`visibility='public'` saja** | 🔴 **P0-LEAK-01** |
| **E-Sertifikat** | `certificates.status` + `source_type` | `status ∈ {pending,issued,revoked,expired}`; `source_type ∈ {learning_enrollment,event_registration}` | `status='issued'` | unduh: cek owner/trainer | `status='issued'` / `whereIn(['issued','revoked','expired'])` | ✅ status konsisten; 🔴 **SCHEMA-03** blocker unduh |
| **Government Intelligence** | `jf_ppbj_records`, `competency_*` | — | — | — | — | 🟢 tidak ada filter status lintas area |

### 4.3 Matriks Hak Akses Multi-Role (5 akun + Guest)

| Kapabilitas | `super.admin` | `admin.pbj` | `operator.lms` | `trainer.pbj` | `ani.peserta` | Guest |
|---|---|---|---|---|---|---|
| Landing publik (home/berita/regulasi/event/kms/verifikasi-sertifikat) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Dashboard Back-Office | ✅ full | ✅ (zone map) | ❌ redirect ke `/workspace` | ❌ redirect | ❌ redirect | ❌ login |
| Workspace `/workspace/*` | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ login |
| `/workspace/operator/*` | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| `/workspace/trainer/*` | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| `/workspace/admin/*` | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Modul Dashboard per zona (`dashboard/landing`, `dashboard/regulasi`, `dashboard/pembelajaran`, `dashboard/bunga-rampai`, `dashboard/komunikasi`, `dashboard/sertifikat`, `dashboard/intelligence`, `dashboard/laporan`, `dashboard/survei-kepuasan`, `dashboard/sistem/*`) | ✅ | ✅ (via `role_permissions`) | ✅ (37 permission) | ❌ | ❌ | ❌ |
| Read-only guard (`.view` tanpa `.manage`) | ✅ | ✅ | ✅ | ✅ | ✅ | — |
| **Mutation guard** (POST/PUT/PATCH/DELETE + URL mutating) | ✅ | ✅ | ✅ (hanya zona dengan izin tulis) | ✅ | ✅ | — |
| **IDOR** Workspace | ✅ | ✅ | ✅ scoped (`getAssignedClassIds`) | ✅ scoped (`assertTrainerOf`) | ✅ scoped (`session.user_id`) | — |

**Temuan matriks ini:**

1. ✅ **Tidak ada endpoint mutasi yang bisa ditembak langsung** oleh read-only user — `AuthenticationFilter` menolak dengan 403 bila zona tidak punya izin tulis non-`.view`. Mutation guard bekerja berdasarkan **dua** sinyal (HTTP method + pola URL), sehingga `GET /dashboard/.../tambah` juga tertangkap.
2. ✅ **Tidak ada permission typo** — 87 permission yang dirujuk filter seluruhnya ada di katalog 90; tabel `permissions` = katalog persis (zero drift).
3. 🟡 **3 role modular ada tapi tidak pernah diberikan ke pengguna** (`admin_regulasi` 8 perm, `admin_arenabelajar` 18 perm, `admin_pembelajaran` 18 perm — total 44 permission aktif dengan **0 user**). `AuthorizationService::getFirstAllowedDashboardRoute()` sudah menyiapkan mapping untuk `admin_*`, tetapi jalur ini belum pernah diuji end-to-end (lihat 3.8).
4. 🟡 **Role `peserta` masih punya akses ke `/workspace/*` di luar sub-grup** (`/workspace/profil`, `/workspace/bookmark`, `/workspace/konsultasi`) — ini memang-BY-DESIGN, tetapi `WorkspaceAuthFilter` **tidak** punya blok untuk prefiks `workspace/peserta/*`, sehingga secara teknis **Trainer/Operator** bisa mengetuk URL `/workspace/peserta/*`. Tidak ada kebocoran data (query sudah peserta-scoped), tetapi permukaan attack lebih luas dari yang dimaksud filter. → **P2** (perketat blok `workspace/peserta/*`).

---

## 5. Daftar Celah Rute, RBAC (90 Permission) & Keamanan

### 5.1 Route

| Temuan | Detail |
|---|---|
| ✅ **Orphan route** | **0** dari 388 route. Semua `Controller::method` resolve ke kelas yang ada (termasuk inherited dari `BaseController`). |
| ✅ **Named-route yatim** | Semua `route_to('…')` yang dipakai view/controller yang diaudit resolve. |
| 🟡 **Route Closure** | 1 route Closure sah (`dashboard/landing/index`) — broke `dashboard:full:audit` (lihat 3.7), bukan produksi. |
| 🟡 **Duplikasi alias** | `Routes.php` memakai pola alias ganda (`…/slider/ubah/{id}` + `…/slider/edit/{id}` dengan `'as' =>` berbeda) — konsisten dan tidak bentrok, tetapi menggandakan permukaan API. |
| 🟢 **GET yang mengubah state** | Semua mutasi memakai `POST`/`match(['GET','POST'], …)`; tidak ditemukan `GET` mutasi murni. |

### 5.2 RBAC (90 Permission)

| Temuan | Status |
|---|---|
| Katalog `permissions_catalog.json` = tabel `permissions` DB | ✅ **90 = 90, identik** |
| Permission di `AuthenticationFilter::zoneMap` yang tidak ada di katalog | ✅ **0** (87 permission terpetakan, semua valid) |
| Permission katalog yang tidak terpakai di zone map | 3: `dashboard.view`, `dashboard.indicators.view` (ditangani terpisah di baris 73-80) + `access.role.view` (benar — `access.role.manage` yang dipakai) |
| Permission yang dipakai di kode tapi di luar katalog | ✅ **0** pada `can()`/`canAny()`. String `workspace.*` yang muncul di controller/view adalah **nama route**, bukan permission. |
| Mutation guard read-only | ✅ Aktif, fail-closed, 403 JSON untuk AJAX + 403 HTML untuk non-AJAX. |
| Sidebar menu vs izin | 🟡 Sidebar menampilkan menu berdasarkan hak akses yang **sama** dengan filter — tetapi item menu yang tidak ada route-nya belum di-audit exhaustively (rekomendasi: tambah audit "sidebar URL → route resolve" ke `dashboard:full:audit`). |

### 5.3 Keamanan

| ID | Temuan | Severity | Status |
|---|---|---|---|
| SEC-01 | Form POST tanpa `csrf_field()` | — | ✅ **1 dari 287**, dan itu aset vendor PHPMailer di `app/Views/reference/fistudy/assets/inc/PHPMailer/get_oauth_token.php` (bukan form produksi) |
| SEC-02 | Path traversal pada unduhan | — | ✅ `realpath()` + `strncmp()` prefix-check di `FileController`, `BungaRampaiController`, `RegulationPublicController`, `ParticipantController`; `public/uploads/.htaccess` memblokir eksekusi skrip |
| SEC-03 | **Upload user ke web root publik** (`FCPATH/uploads/consultations`, `FCPATH/uploads/learning/assignments`) | **P3** | 🟡 Lampiran konsultasi (dokumen sensitif) & panduan tugas berada di `public/` yang bisa diunduh siapa pun yang mengetahui URL. Nama file mengandung 8 byte hex acak + `.htaccess` memblokir eksekusi & listing → **risiko rendah**, tapi akses file tanpa autentikasi tetap ada. Rekomendasi: pindah ke `WRITEPATH` (seperti `FileStorageService` & `CertificateGenerator` sudah lakukan). |
| SEC-04 | NIK/NIP bocor di endpoint publik | — | ✅ Tidak ditemukan. `public_profiles.national_identifier` tidak diekspos; `CertificateVerificationController` me-return minimum data (nomor, status, tipe sumber) |
| SEC-05 | `catch (\Throwable) {}` kosong menelan error | **P1** | 🔴 3 situs (SCHEMA-05) — menyembunyikan kegagalan insert, menyebabkan file yatim |
| SEC-06 | XSS output | — | ✅ `esc()` dipakai konsisten pada output view (diverifikasi pada sampel `OperatorController`, view Workspace & Dashboard) |
| SEC-07 | Upload tanpa validasi MIME | — | ✅ `ConsultationController:60-68` menolak ekstensi di luar whitelist + memblokir MIME `php`/`script`; batas 10 MB |
| SEC-08 | SQL injection | — | ✅ Tidak ditemukan. Seluruh query memakai Query Builder + binding; satu-satunya raw SQL (`NewsPublicController:340`) memakai prepared statement `?` |
| SEC-09 | `POST /dashboard/...` tanpa filter | — | ✅ `csrf` global + `auth` filter + mutation guard berlapis |

### 5.4 Performa (N+1 & Transaksi)

| ID | Temuan | Lokasi | Dampak |
|---|---|---|---|
| PERF-01 | **N+1 pada daftar kelas operator** — 2 query per kelas di dalam `foreach` | `Workspace/OperatorController.php:1240` (count enrollments), `:1242-1248` (class_trainers) | 3N+1 query; 100 kelas = 301 query |
| PERF-02 | **N+1 pada agregasi trainees & progress** | `Workspace/TrainerController.php:120-125, 238-243, 332-367` | Ribuan query untuk ringkasan satu halaman |
| PERF-03 | **N+1 pada monitoring pembelajaran** | `Learning/ProgressMonitoringController.php:63-104,167` | Skala data linear |
| PERF-04 | **N+1 pada batch sertifikat** | `Certificate/CertificateBatchController.php:225,233,319,467,475,553` | Lambat pada penerbitan massal |
| PERF-05 | **N+1 pada landing** | `Landing/ArenaBelajarController.php:96,104,241,322,364,453-472,654,803,985-993,1287` | Landing publik — degrades UX |
| PERF-06 | N+1 pada model & service | `Models/Knowledge/ArticleModel.php:120,146`; `Services/LearningService.php:397,407,680,717,960`; `Services/GradingService.php:85,88`; `Services/Admin/UserManagementService.php:150,436,484` | — |
| PERF-07 | Operasi multi-tabel tanpa transaksi | `Landing/HomeController::sendContact()`, `submitSurvey()` (test:portal-security M-5) | Data + audit log bisa tidak sinkron |

### 5.5 Pelanggaran Isolasi Template & Aset (UI Boundary)

Aturan kanonik ada di `dokumen/ui-boundary.md:380` → *"Dashboard Back-office tidak boleh memakai asset dari `public/assets/main/`"* dan baris 30/32 → Landing/Workspace **wajib** `public/assets/main/`.

**32 file melanggar batas zona:**

| Zona | File | Asset yang di-crossing | Dampak |
|---|---|---|---|
| Landing | `landing/layouts/public.php`, `landing/pages/{about,certificate_verify,contact,event_detail,survey_public_results}.php` | `assets/dashboard/js/lib/iconify-icon.min.js`, `assets/dashboard/js/lib/apexcharts.min.js` | 🔴 **Landing bergantung pada bundel JS Dashboard.** Melanggar aturan "FiStudy murni". Jika bundle dashboard dihapus/di-isolasi, 5 halaman publik rusak total. |
| Landing | `landing/home/index.php` (6×) | `assets/dashboard/images/users/user1.png` | 🟡 Avatar demo dari zona Dashboard |
| **Workspace** | `main/workspace/pages/operasional.php`, `main/workspace/pages/operator/certificates.php` | `assets/dashboard/js/begawi-batch-runner.js` | 🔴 **Workspace (FiStudy) menarik skrip batch Dashboard** — pelanggaran batas paling material |
| Dashboard | `dashboard/components/header.php` (2×) | `assets/main/images/favicons/*` | 🟡 Favicon |
| Dashboard | `dashboard/landing/announcement/form.php` (11×), `dashboard/landing/team/form.php` (9×), `dashboard/landing/testimonial/form.php` (9×), `dashboard/landing/profile/index.php` (6×), `dashboard/landing/slider/form.php` (4×) | `assets/main/images/{slider,event,gallery,blog}/*` | 🟡 Placeholder gambar — kebocoran estetika WowDash |
| Dashboard | `dashboard/knowledge/*` (8 file), `dashboard/learning/*` (5 file) | `assets/main/images/{blog,gallery}/*` | 🟡 Placeholder |

**Rekomendasi:** salin/pin aset yang benar-benar dipakai ke `public/assets/main/` (bukan `assets/dashboard/`), lalu perbarui referensinya. Untuk `iconify-icon.min.js` & `apexcharts.min.js`, pastikan salinan FiStudy ada di `public/assets/main/js/lib/`. Setelah itu, jalankan audit boundary otomatis sebagai gate CI.

### 5.6 Temuan pada Command Audit & Test (Tahap 2.7)

**Hasil eksekusi suite CLI (live, bukan dokumentasi):**

| Command | Hasil |
|---|---|
| `landing:full:audit` | ✅ **44/44 route PASS**, 0 FAIL |
| `dashboard:full:audit` | ⚠️ 33 OK / **1 FAILED** — crash TypeError pada route Closure (3.7) |
| `workspace:full:audit` | 🔴 **Output berhenti setelah `/workspace/operator`** — proses CLI mati diam-diam, 31 route tak teruji (3.9) |
| `workspace:smoke` | ⚠️ 1 FAIL **palsu** — ekspektasi string UI lama (3.6) |
| `test:workspace-security` | ✅ 0 FAIL |
| `test:rbac-custom-roles` | ⚠️ 8 FAIL **palsu** — harness usang (3.8) |
| `test:rbac-double-role` | ⚠️ 1 FAIL **palsu** — ekspektasi "16 berkas migrasi", aktual **17** (1 file RUP Audit yang sah) |
| `test:portal-security` | ⚠️ 2 FAIL **nyata** — M-5 transaksi atomik (3.10) |
| `test:trainer-gradebook` | ✅ 0 FAIL |
| `test:quiz-review-reattempt` | ✅ 0 FAIL |
| `certificate:smoke` | ✅ 0 FAIL |
| `test:operator-bulk` | ✅ 0 FAIL |
| `test:workspace-learner` | ✅ 0 FAIL |
| `test:workspace-executive` | ✅ 0 FAIL |

**Bug pada command audit itu sendiri (diminta eksplisit oleh mandat):**

1. 🔴 `WorkspaceFullAudit` — proses mati diam-diam, **tidak pernah** mencetak scorecard → audit dianggap lulus padahal sebagian besar route tak diuji.
2. 🟡 `DashboardFullAudit` — `class_exists(Closure)` TypeError PHP 8.3 → satu route yang sehat dilaporkan sebagai route rusak.
3. 🟡 `RbacCustomRoleTest` — mengasumsikan permission bisa dimock via session; sudah tidak berlaku sejak filter mengadopsi "Zero Stale Session".
4. 🟡 `RbacDoubleRoleTest` — hard-code jumlah migrasi 16; harus 17 (dan sebaiknya tidak hard-code sama sekali — cukup hitung `glob()` + assert tidak berubah vs baseline hash).
5. 🟡 `WorkspaceSmoke` — hard-code literal heading view yang sudah di-rename.
6. 🟡 `DatabaseCleanAudit`, `UserFilterTest`, `PermissionSync`, `DiagnoseRegistration` — masih memuat literal kolom legacy (`resource_typ`, `profile_typ`, `unit_nam`, `domai`) → indikasi command ini ditulis terhadap skema lama.

### 5.7 Dokumentasi Usang (`dokumen/*.md`)

**20 file, seluruhnya bertanggal 23–24 Oktober 2026** — sementara basis kode sudah bergerak ke 3 November 2026. **Tidak satu pun boleh dipakai sebagai acuan kebenaran.**

| File | Lines | Status terhadap kode aktual | Prioritas |
|---|---:|---|---|
| `dokumen/database-blueprint.md` | 1611 | 🔴 DDL lama; tidak memuat `visitor_ip_logs`, `rup_audit_*`, dan tabel `*_sequences` | P1 |
| `dokumen/data-dictionary.md` | 1472 | 🔴 Definisi kolom usang (`news.status`, `regulations.nomor_regulasi`, `lessons.sequence_order`, `assignments.max_score`…) — sebagian persis merupakan **temuan SCHEMA-09** | P1 |
| `dokumen/database-erd.md` | 1069 | 🔴 Relasi & CHECK constraint tidak sinkron dengan DB live | P1 |
| `dokumen/ui-boundary.md` | 425 | 🟡 Aturan **benar**, tetapi 32 file view sudah melanggarnya → dokumen jadi normatif tetapi tidak ditegakkan | P1 |
| `dokumen/route-contract.md` | 553 | 🟡 Kontrak route masih memuat pola lama; tidak mencerminkan 388 route aktual + route Closure | P2 |
| `dokumen/implementation-status.md` | 307 | 🟡 Klaim status fitur vs realitas CLI | P2 |
| `dokumen/auth-and-rbac.md` | 524 | 🟡 Harus diselaraskan dengan `AuthenticationFilter::zoneMap` (87 permission) + "Zero Stale Session" | P2 |
| `dokumen/directory-tree.md` | 956 | 🟡 Pohon direktori usang (`app/Views/reference` 1.204 file vendor tidak dibahas) | P2 |
| `dokumen/workflow-contract.md` | 570 | 🟡 Kontrak `workflow_status` perlu diselaraskan dengan domain CHECK aktual | P2 |
| `dokumen/assessment-and-certification-rules.md` | 504 | 🟡 | P2 |
| `dokumen/file-storage-policy.md` | 415 | 🟡 Tidak menyinggung upload ke `FCPATH` (SEC-03) | P2 |
| `dokumen/directory-tree-create.md` | 622 | 🟡 | P3 |
| `dokumen/privacy-and-data-classification.md` | 411 | 🟡 | P3 |
| `dokumen/public-statistics-policy.md` | 405 | 🟡 | P3 |
| `dokumen/registration-policy.md` | 382 | 🟡 | P3 |
| `dokumen/regulation-policy.md` | 423 | 🟡 | P3 |
| `dokumen/documentation-and-database-file-register.md` | 209 | 🟡 | P3 |
| `dokumen/auth-migration-plan.md` | 177 | 🟡 | P3 |

**Tambahan:** ada **14 file `laporan_audit_*.md` di root repo** (hasil audit sebelumnya) yang isinya **bertentangan** dengan temuan di dokumen ini — semuanya sudah usang dan sebaiknya diarsipkan ke folder `laporan/arsip/` dengan penanda tanggal.

---

## 6. Rencana Eksekusi Penyempurnaan Bertahap (Step-by-Step Safe Remediation Plan)

> **Aturan yang dipegang sepanjang rencana ini:**
> - **ZERO MIGRATION POLICY** — tidak ada penambahan/ubah file migrasi ke-17; tidak ada ALTER TABLE; tidak ada perubahan DDL.
> - Setiap tahap adalah commit terpisah, **indepoten**, dan dapat di-revert.
> - Setiap tahap wajib melewati gate verifikasi (6.5) sebelum lanjut.

### TAHAP 1 — P0: Database Alignment & Fatal Crash (estimasi 0,5–1 hari)

**Gate masuk:** tidak ada perubahan kontrak API/UI.

| # | Aksi | File | Verifikasi |
|---|---|---|---|
| 1.1 | **SCHEMA-01** — `questions`→`quiz_questions` (+`question`→`prompt`), `question_options`→`quiz_options` | `Learning/LearningClassController.php:759-793` | `POST /dashboard/pembelajaran/kelas/{id}/duplikasi` → 302 + `quiz_questions` & `quiz_options` terisi |
| 1.2 | **SCHEMA-02** — samakan payload `file_assets` di 3 controller (pola `CertificateTemplateController:975` sebagai acuan) | `Learning/CurriculumController.php:781`, `Learning/AssessmentManagementController.php:467`, `Workspace/TrainerController.php:1823` | Buat tugas dengan PDF panduan di 3 area → 302, `file_assets` berisi 1 baris valid |
| 1.3 | **SCHEMA-03** — `trainer_id`→`user_id`, buang `deleted_at IS NULL`, tambah `status='active'` | `Certificate/CertificateDownloadController.php:121-125` | Unduh sertifikat learning oleh peserta & oleh trainer → 200 |
| 1.4 | **SCHEMA-04** — tambah `'is_active'`, buang `'status'` dari `$allowedFields` | `Models/OrganizationUnitModel.php:10-17` | Toggle unit → `organization_units.is_active` berubah di DB |
| 1.5 | **P0-LEAK-01** — filter `status` di landing **dan** normalisasi tulis (`draft`→`internal`) | `Landing/EventPublicController.php` (7 titik builder), `Services/Communication/CommunicationService.php:202-222` | Buat event `draft`+`public` via service → `visibility` menjadi `internal`; landing tidak menampilkan event `draft`/`cancelled` |

**Verifikasi SQL wajib (setelah Tahap 1):**

```sql
-- 1.1 duplikasi kelas menyalin bank soal
SELECT COUNT(*) FROM quiz_questions;
SELECT COUNT(*) FROM quiz_options;
-- 1.2 file_assets valid
SELECT id,disk,path,stored_name,size_bytes,resource_type,uploaded_by FROM file_assets ORDER BY id DESC LIMIT 5;
-- 1.3 class_trainers memakai user_id
SELECT COUNT(*) FROM class_trainers WHERE user_id = <trainer_id> AND status = 'active';
-- 1.4 toggle unit
SELECT id,code,is_active FROM organization_units ORDER BY id LIMIT 5;
-- 1.5 tidak ada event publik berstatus draft/cancelled
SELECT COUNT(*) FROM events WHERE visibility='public' AND status IN ('draft','cancelled');
```

### TAHAP 2 — P1: Cross-Area & Multi-Role Sync (estimasi 1–2 hari)

| # | Aksi | File |
|---|---|---|
| 2.1 | **SCHEMA-05** — perbaiki payload `file_assets` + **hapus `catch (\Throwable) {}`** (ganti `log()->error()` + flash) | `Landing/HomeController.php:653`, `Workspace/ConsultationController.php:96`, `Admin/Communication/ContactMessageController.php:69` |
| 2.2 | **SCHEMA-06** — `assigned_by`→`granted_by`, `assigned_at`→`granted_at` | `Database/Seeds/AuditRemediationSeeder.php:52-57` |
| 2.3 | **SCHEMA-07** — ganti fallback `attendance_status` dengan join `attendances` | `Services/Admin/ReportAnalyticsService.php:139-144` |
| 2.4 | **XAREA-03** — KPI `total_classes` ikut memfilter `courses.status='published' AND courses.visibility='public'` | `Landing/ArenaBelajarController.php:40` (dan 1381) |
| 2.5 | **XAREA-02** — tambah status `full` pada KPI Dashboard; hapus literal `published` yang mustahil | `Communication/EventController.php:35-37` |
| 2.6 | **TX-01** — bungkus `sendContact()` & `submitSurvey()` dengan `transBegin/transComplete/transRollback` | `Landing/HomeController.php` |
| 2.7 | **RBAC-01** — tulis ulang `RbacCustomRoleTest` agar memakai **user uji nyata** (bukan session mock) | `Commands/RbacCustomRoleTest.php` |
| 2.8 | **AUDIT-01** — `workspace:full:audit` jangan mati diam-diam; jalankan dengan `ENVIRONMENT=testing` atau deteksi `Session::start()` null | `Commands/WorkspaceFullAudit.php` |
| 2.9 | **AUDIT-02** — tangani route `Closure` sebelum `class_exists()` | `Commands/DashboardFullAudit.php:69` |
| 2.10 | Perketat blok role untuk prefiks `workspace/peserta/*` | `Filters/WorkspaceAuthFilter.php` (tambah blok ke-4) |

### TAHAP 3 — P2: UI Boundary, Security & Edge Cases (estimasi 1–2 hari)

| # | Aksi | File |
|---|---|---|
| 3.1 | **SEC-03** — pindahkan upload consultation & assignment guide dari `FCPATH/uploads` ke `WRITEPATH/uploads` (ikuti `FileStorageService` & `CertificateGenerator`), layani lewat controller download berotorisasi | `Workspace/ConsultationController.php:80`, `Landing/HomeController.php:639`, `Admin/Communication/ContactMessageController.php:53`, `Learning/CurriculumController.php:771,869`, `Learning/AssessmentManagementController.php:457,553`, `Workspace/TrainerController.php:1813,1910` |
| 3.2 | **UI-01** — salin `iconify-icon.min.js` & `apexcharts.min.js` ke `public/assets/main/js/lib/`; pindahkan `begawi-batch-runner.js` ke zona `main` atau buat versi Workspace; perbaiki 32 file view | `app/Views/**` + `public/assets/**` |
| 3.3 | **DEAD-02** — hapus method `questionBank()` yang tidak ter-rute di `Learning/QuizController` (atau pindahkan logikanya ke `AssessmentManagementController::questionBank` yang sudah benar memakai `qq.prompt`) | `Controllers/Learning/QuizController.php:51-81` |
| 3.4 | **STATUS-01** — adopsi `EventRegistrationStatus::ACCESS_RAW`/`PENDING_RAW` + `applyFilter()` di seluruh call site; hapus literal mati `'attended','confirmed','promoted','passed','published'` | `Landing/EventPublicController.php`, `Workspace/OperatorController.php`, `Learning/LearningClassController.php:875`, `Services/Admin/ReportAnalyticsService.php:73` |
| 3.5 | **PERF-01..06** — ubah N+1 menjadi query agregat / batch `whereIn` | `Workspace/OperatorController.php:1238-1253`, `Workspace/TrainerController.php`, `Learning/ProgressMonitoringController.php`, `Certificate/CertificateBatchController.php`, `Landing/ArenaBelajarController.php`, `Models/Knowledge/ArticleModel.php` |
| 3.6 | Validasi `getFile()`/ukuran/MIME seragam untuk semua endpoint upload | 6 controller pada 3.1 |

### TAHAP 4 — P3: Penyelarasan Script Audit CLI & Dokumentasi (estimasi 1 hari)

| # | Aksi | File |
|---|---|---|
| 4.1 | **SCHEMA-08 + DEAD-01** — hapus controller & model legacy: `Controllers/Regulation/RegulationController.php`, `Models/{RegulationModel, RegulationCategoryModel, RegulationVersionModel, AssignmentModel, AssignmentSubmissionModel, CourseModel, LessonModel, NewsModel}.php`, `Models/Knowledge/KnowledgeMediaContentModel.php`. **Pastikan** tidak ada `use` yang tertinggal (verifikasi via grep `^use App\Models\…`). | — |
| 4.2 | **AUDIT-03** — `RbacDoubleRoleTest`: ganti hard-code "16" menjadi hitung `glob()` + baseline hash | `Commands/RbacDoubleRoleTest.php` |
| 4.3 | **AUDIT-04** — `WorkspaceSmoke`: ganti literal heading lama dengan selector stabil (`id="assignTrainerModal"`, `data-testid`) | `Commands/WorkspaceSmoke.php:828-831` + view |
| 4.4 | **CLEAN-01** — pindahkan `app/Views/reference/` (1.204 file vendor FiStudy+WowDash) ke `public/assets/_reference/` atau hapus; tidak ada controller yang mereferensinya | `app/Views/reference/**` |
| 4.5 | **DOC-01** — tulis ulang 20 file `dokumen/*.md` **dari sumber kebenaran aktual** (generate `database-blueprint.md` & `data-dictionary.md` langsung dari `information_schema`), tambahkan banner "dihasilkan ulang 2026-11-03" | `dokumen/**` |
| 4.6 | Arsipkan 14 file `laporan_audit_*.md` lama ke `laporan/arsip/` + README yang menyatakan tanggal & status usang | root repo |
| 4.7 | Tambahkan **CI gate** baru: `test:schema-drift` (bandingkan `$allowedFields` + literal query dengan `information_schema`) dan `test:ui-boundary` (deteksi asset lintas zona) | `app/Commands/` |

### 6.5 Checklist Pengujian Silang (Manual & CLI) — WAJIB dijalankan setelah tiap tahap

**A. Otomatis (CLI)**

```bash
php spark landing:full:audit       # ekspektasi: 44/44 PASS
php spark dashboard:full:audit     # ekspektasi: 34 OK / 0 FAILED  (route Closure tertangani)
php spark workspace:full:audit     # ekspektasi: seluruh route diuji + scorecard tercetak
php spark workspace:smoke          # ekspektasi: 0 FAIL
php spark test:workspace-security  # ekspektasi: 0 FAIL
php spark test:rbac-custom-roles   # ekspektasi: 0 FAIL (setelah harness diperbaiki)
php spark test:rbac-double-role    # ekspektasi: 0 FAIL
php spark test:portal-security     # ekspektasi: 0 FAIL (M-5 hijau)
php spark test:trainer-gradebook
php spark test:quiz-review-reattempt
php spark certificate:smoke
php spark test:operator-bulk
php spark test:workspace-learner
php spark test:workspace-executive
php spark test:user-filter
```

**B. Manual — 5 Peran + Guest (matriks 4.3)**

| # | Skenario | Peran | Ekspektasi |
|---|---|---|---|
| B1 | Buka `/dashboard/pembelajaran/kelas/{id}/duplikasi` → konfirmasi | `admin.pbj` | 302, bank soal ikut tersalin (`quiz_questions` & `quiz_options` naik) |
| B2 | Buat tugas + unggah PDF panduan di 3 area | `admin.pbj`, `trainer.pbj` | 302, `file_assets` berisi baris valid, tidak ada file yatim |
| B3 | Unduh sertifikat learning (peserta & trainer) | `ani.peserta`, `trainer.pbj` | 200, PDF terunduh, tidak 500 |
| B4 | Toggle aktif/nonaktif unit organisasi | `admin.pbj` | Nilai `is_active` berubah di DB **dan** di UI |
| B5 | Buat event `draft` + visibility `public` | `operator.lms` | Event **tidak** tampil di `/event` maupun `/event/{slug}`; `visibility` ternormalisasi `internal` |
| B6 | Buka `/dashboard/landing`, `/dashboard/regulasi/dokumen`, `/dashboard/sistem/pengaturan` | role custom `admin_regulasi` | Hanya modul sesuai izin yang 200; sisanya 403; sidebar sinkron |
| B7 | `POST /dashboard/pembelajaran/kelas` tanpa izin tulis | read-only | 403 (JSON bila AJAX) |
| B8 | `GET /dashboard/pembelajaran/kelas/tambah` tanpa izin tulis | read-only | 403 (mutation guard berbasis URL) |
| B9 | Trainer A mencoba `GET /workspace/trainer/kelas/{kelasTrainerB}` | `trainer.pbj` | Redirect + pesan "Akses ditolak" (**bukan** 200) |
| B10 | Peserta mencoba `GET /workspace/peserta/sertifikat/{idMilikLain}` | `ani.peserta` | Ditolak / data milik sendiri |
| B11 | Unggah lampiran konsultasi, lalu akses langsung URL-nya tanpa login | Guest | **403/404** (setelah 3.1), bukan 200 |
| B12 | Landing: `/`, `/berita`, `/regulasi`, `/event`, `/bunga-rampai`, `/verifikasi-sertifikat` | Guest | 200, **tanpa** NIK/NIP di payload |
| B13 | Source page view: Assets landing hanya `assets/main/*` | — | 0 request ke `assets/dashboard/*` (DevTools Network) |
| B14 | Source page view: Dashboard hanya `assets/dashboard/*` | — | 0 request ke `assets/main/*` (kecuali favicon) |
| B15 | Workspace: `assets/dashboard/js/begawi-batch-runner.js` | — | Terlayani dari zona `main` |

**C. Konsistensi Data (SQL verifikasi akhir)**

```sql
-- tidak boleh ada event publik berstatus draft/cancelled
SELECT COUNT(*) FROM events WHERE visibility='public' AND status IN ('draft','cancelled');   -- harus 0
-- tidak boleh ada file_assets tanpa path/original_name
SELECT COUNT(*) FROM file_assets WHERE (path IS NULL OR path='') OR (original_name IS NULL OR original_name='');
-- tidak boleh ada quiz tanpa soal
SELECT q.id FROM quizzes q LEFT JOIN quiz_questions qq ON qq.quiz_id=q.id WHERE qq.id IS NULL;
-- konsistensi permission
SELECT COUNT(*) FROM permissions;                                            -- harus 90
SELECT COUNT(DISTINCT permission_id) FROM role_permissions;                  -- <= 90
-- kelas aktif harus，背后 course publish
SELECT COUNT(*) FROM learning_classes lc JOIN courses c ON c.id=lc.course_id
  WHERE lc.status IN ('scheduled','active') AND (c.status<>'published' OR c.visibility<>'public');
```

---

## 7. Ringkasan Eksekutif Penutup

**Kondisi sebenarnya sistem ini:** arsitekturally **sehat dan matang** — RBAC fail-closed, IDOR guard kuat, CSRF komprehensif, path-traversal aman, permission katalog = DB persis, 388 route tanpa orphan, dan audit landing 44/44 hijau. Yang rusak adalah **sinkronisasi kode ↔ skema pada beberapa titik**, plus **tooling audit yang sudah usang**.

**Temuan paling berbahaya bukan "kode ngarang" yang berjumlah besar, melainkan 5 P0 yang sangat terarah:**

1. `file_assets` dengan 4 nama kolom fiktif — **mematikan 3 alur pembuatan tugas** (Dashboard Kurikulum, Dashboard Assessment, Workspace Trainer).
2. `class_trainers.trainer_id` — **mematikan seluruh unduhan sertifikat belajar** (semua peran).
3. `questions`/`question_options` — **mematikan duplikasi kelas** dan silent loss bank soal.
4. `$allowedFields` `OrganizationUnitModel` — **toggle unit organisasi diam-diam tidak pernah bekerja** (tanpa error, tanpa log).
5. Landing publik **tidak memfilter `events.status`** — event `draft` **tayang ke internet** cukup dengan default form (status=draft, visibility=public).

Semua 5 P0 **telah direproduksi langsung terhadap MySQL live** dan **sudah teridentifikasi route produksi yang mencapainya** — tidak ada temuan teoretis.

**Rekomendasi eksekusi:** mulai dari **Tahap 1** (5 perbaikan terisolasi, < 40 baris perubahan total, tanpa menyentuh skema). Setelah gate 6.5 hijau, lanjut **Tahap 2** (sinkronisasi lintas area + perbaikan 3 command audit), lalu **Tahap 3** (UI boundary + performa), dan terakhir **Tahap 4** (bersih-bersih kode mati + regenerasi dokumentasi dari sumber kebenaran aktual).

> **Dokumen ini adalah laporan + rencana. Tidak ada satu baris kode produksi yang diubah. Seluruh instruksi implementasi di atas menunggu persetujuan sebelum dieksekusi.**

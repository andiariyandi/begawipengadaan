# PETA INVENTARIS PERMISSION & MODUL
## Audit Komprehensif — Flexible RBAC, Dashboard Admin BegawiPengadaan

| | |
|---|---|
| **Judul** | Peta Inventaris Permission & Modul (Permission Mapping Matrix) |
| **Proyek** | BegawiPengadaan — Dashboard Admin |
| **Tanggal Audit** | 23 Oktober 2026 |
| **Metode** | Static scan (`app/Controllers/`, `app/Config/Routes.php`, `app/Views/dashboard/`) + DB probe read-only |
| **Perubahan Kode** | **NOL** — seluruh audit bersifat read-only |
| **Status** | Draft untuk review — fondasi implementasi Flexible RBAC |

> **Dokumen ini tidak mengubah kode apa pun.** Seluruh SQL probe dijalankan dari `%TEMP%`, bukan repository.

---

## 0. Ringkasan Eksekutif

| Metrik | Nilai |
|---|:--:|
| Controller di bawah namespace `dashboard/` | **76** |
| Route bernama `dashboard.*` | **356** |
| View di `app/Views/dashboard/` | **226** (21 direktori) |
| Link menu aktif di sidebar | **36** |
| Permission di DB saat ini | **16** |
| **Permission yang benar-benar di-enforce saat runtime** | **1** (`kms.manage`) |
| Total call site `->can()` di seluruh aplikasi | **10** |
| Permission key yang dicek tapi **tidak ada** di DB | **3** |

### Kesimpulan Utama

Infrastruktur RBAC (**tabel `permissions`, `role_permissions`, filter, UI matrix**) **sudah lengkap dan berfungsi**. Yang belum ada adalah **inventaris permission yang granular**, dan **enforcement**-nya. Saat ini ~70% aplikasi dijaga oleh **4 blok `if` hardcoded** di `AuthenticationFilter`.

### Temuan Kritis

| ID | Temuan | Dampak |
|:--:|---|---|
| **W1** | `system.settings`, `access.manage`, `approvals.review` dipanggil filter tapi **tidak ada di DB** | 3 dead branch — role kustom non-admin pasti ditolak |
| **W2** | **15 dari 16 permission tidak dibaca di mana pun** | Permission matrix saat ini bersifat dekoratif |
| **W3** | Bunga Rampai & Arena Belajar **bukan modul terpisah** — reuse namespace `Knowledge`/`Learning` | Role "Bunga Rampai saja" tidak bisa pakai `knowledge.*` |
| **W4** | `hasDashboardAccess()` tidak mengenali prefix `knowledge.`, `intelligence.`, `bunga.` | Role kustom dengan permission sah akan **403** |
| **W5** | `BaseKnowledgeController.php:38` masih pakai role key legacy (`super_admin`/`admin`) | Escape hatch gagal saat flexible role aktif |

---

# I. RINGKASAN ARSITEKTUR KONTROL AKSES SAAT INI

## I.1 Alur Request Saat Ini

```
Request
  |
  v
[Filters.php: 'auth' => ['dashboard', 'dashboard/*']]
  |
  v
AuthenticationFilter::before()
  |- 1. Whitelist publik (masuk/daftar/reset/keluar/status-akun)
  |- 2. Cek session isLoggedIn
  |- 3. Cek user masih ada & account_status = 'active'
  |- 4. AuthorizationService::hasDashboardAccess()  <-- izin masuk dashboard
  |- 5. Zona 1: akses/role|access/permission        -> SuperAdmin ONLY
  |- 6. Zona 2: sistem/pengaturan|maintenance|settings -> isAdmin() || can('system.settings')
  |- 7. Zona 3: akses/pengguna|unit-organisasi|peran-pengadaan|audit -> isAdmin() || operator || can('access.manage')
  +-- 8. Zona 4: verifikasi-profil|permohonan-*|approvals -> isAdmin() || operator || can('approvals.review')
  |
  v
Controller::__construct()   <-- TIDAK ADA guard generik
  |
  v
Method spesifik             <-- hanya 10 titik can() di seluruh aplikasi
```

**Referensi:** `app/Config/Filters.php` (grup `auth`), `app/Filters/AuthenticationFilter.php:13-118`

## I.2 Struktur Controller Base

| Temuan | Lokasi | Status |
|---|---|:--:|
| `BaseAdminController` | - | **TIDAK ADA** |
| `App\Controllers\BaseController` | `app/Controllers/BaseController.php` | ada (dasar CI4, tanpa guard) |
| `BaseKnowledgeController` | `app/Controllers/Knowledge/BaseKnowledgeController.php` | **satu-satunya** abstract guard |
| `checkSuperAdmin()` | `RoleController` (9x), `PermissionController` (6x) | ada, hardcoded |

**Artinya:** 14 controller di `app/Controllers/Admin/` berdiri sendiri tanpa base class. Setiap modul harus menduplikasi logika guard-nya sendiri.

## I.3 Mekanisme Resolved Permission Saat Ini

`app/Models/UserModel::getPermissions(int $userId)` - di-resolve saat login via `AuthenticationService.php:37-51`:

```sql
SELECT permissions.key
FROM permissions
JOIN role_permissions ON role_permissions.permission_id = permissions.id
JOIN user_roles       ON user_roles.role_id = role_permissions.role_id
WHERE user_roles.user_id = ? AND user_roles.status = 'active'
```

Hasil disimpan di `session('permissions')`, lalu dibaca oleh `AuthorizationService::can()`:

```php
public function can(string $permission): bool
{
    return in_array($permission, (array) session()->get('permissions'), true);
}
```

## I.4 Kelemahan Fatal untuk Flexible Role

### W1 - Permission Yang Dipanggil Filter Tidak Ada di DB

`AuthenticationFilter` memanggil tiga key yang **tidak ada** di tabel `permissions` (dibuktikan query `SELECT key FROM permissions WHERE key IN (...)` mengembalikan **0 baris**):

| Dipanggil di | Key | Ada di DB? | Efek |
|---|---|:--:|---|
| `AuthenticationFilter.php:84`, `SettingController.php:23` | `system.settings` | Tidak | `can()` selalu `false` |
| `AuthenticationFilter.php:98` | `access.manage` | Tidak | selalu `false` |
| `AuthenticationFilter.php:111` | `approvals.review` | Tidak | selalu `false` |

Ketiganya **dead branch** - akses aktual jatuh ke hardcoded `isAdmin()` / `in_array('operator')`. Admin yang sudah authorized **tetap bisa masuk**, jadi tidak ada celah keamanan, tetapi **role kustom non-admin pasti ditolak**.

### W2 - 15 dari 16 Permission Tidak Berdampak

Hanya `kms.manage` yang benar-benar menolak request (`BaseKnowledgeController.php:36`).

`learning.class.{create,update,delete}` (`LearningClassController.php:294-300`) **hanya mengatur visibility tombol di UI** - endpoint tetap terbuka bagi siapa pun yang lolos filter.

Sisanya (`user.manage`, `role.manage`, `audit.view`, `regulation.manage`, `certificate.manage`, `lms.manage`, `enrollment.verify`, `assessment.evaluate`, `dashboard.view`, `certificate.approve`, `user.verify`) **tidak dibaca di mana pun** sebagai penjaga.

### W3 - Bunga Rampai & Arena Belajar Bukan Modul Terpisah

Keduanya memakai controller modul lain, hanya berbeda route group:

| Modul di sidebar | Route group | Controller namespace | Route name prefix |
|---|---|---|---|
| Bunga Rampai | `dashboard/bunga-rampai` (`Routes.php:550`) | `App\Controllers\Knowledge` | `dashboard.knowledge.*` |
| Arena Belajar | `dashboard/pembelajaran` (`Routes.php:429-548`) | `App\Controllers\Learning` | `dashboard.learning.*` |

**Konsekuensi:** role "Bunga Rampai saja" tidak bisa mudah memakai `knowledge.*`, karena key yang sama juga governs modul `Knowledge` di landing. Wajib membuat namespace key terpisah (mis. `bunga.*`) dan memetakan route-nya ke controller yang sama.

### W4 - `hasDashboardAccess()` Blind terhadap Prefix Baru

`AuthorizationService.php:41-45` hanya mengenali **12 prefix**:

```php
$validPrefixes = [
    'dashboard.', 'admin.', 'system.', 'access.', 'content.',
    'regulation.', 'learning.', 'event.', 'survey.', 'communication.',
    'certificate.', 'approvals.',
];
```

Prefix **`knowledge.` `intelligence.` `user.` `role.` `audit.` `lms.` `kms.` `training.` `profile.` `bunga.`** tidak terdaftar. Role kustom yang hanya diberi `knowledge.view` akan **ditolak masuk dashboard (403)**, walau permission-nya sah.

### W5 - Residual Role-Key Legacy (BUG)

`BaseKnowledgeController.php:38` masih memakai key lama:

```php
if (! in_array('super_admin', $roles, true) && ! in_array('admin', $roles, true)) {
```

Padahal DB hanya berisi `super_administrator` / `administrator`. Saat ini **tertutup** karena `super_administrator` sudah memegang `kms.manage` - tetapi begitu flexible role aktif, **escape hatch ini akan gagal** dan admin biasa akan terkunci dari Bunga Rampai.

### Distribusi Permission Saat Ini

| Key | Domain | is_system | Roles Granted |
|---|---|:--:|:--:|
| `certificate.template.manage` | certificate | 0 | 1 |
| `learning.class.create` | lms | 0 | 1 |
| `learning.class.delete` | lms | 0 | 1 |
| `learning.class.update` | lms | 0 | 1 |
| `audit.view` | audit | 1 | 2 |
| `enrollment.verify` | lms | 1 | 2 |
| `regulation.manage` | regulation | 1 | 2 |
| `role.manage` | rbac | 1 | 2 |
| `user.manage` | user | 1 | 2 |
| `user.verify` | user | 1 | 2 |
| `assessment.evaluate` | lms | 1 | 3 |
| `certificate.manage` | certificate | 1 | 3 |
| `kms.manage` | kms | 1 | 3 |
| `lms.manage` | lms | 1 | 3 |
| `certificate.approve` | certificate | 1 | 4 |
| `dashboard.view` | dashboard | 1 | 4 |

**Grant per role:**

| Role ID | Key | Granted Permissions |
|:--:|---|:--:|
| 10 | `super_administrator` | 12 |
| 11 | `administrator` | 12 |
| 12 | `operator` | 2 |
| 13 | `trainer` | 3 |
| 14 | `evaluator` | 7 |
| 15 | `peserta` | 0 |
| 16 | `public_user` | 0 |

**User uji:** `superadmin@begawipbj.go.id` (id 13, username `superadmin`, role `super_administrator`, 12 permission).

---

# II. TABEL MATRIKS PERMISSION STANDAR

## Konvensi Penamaan

**Format key:** `<domain>.<subfitur>.<aksi>`

| Suffix | Makna |
|:--:|---|
| `.view` | Melihat daftar / halaman |
| `.create` | Membuat data baru |
| `.edit` | Mengubah data existing |
| `.delete` | Menghapus data |
| `.approve` | Persetujuan workflow (dipisah dari `.edit`) |
| `.export` | Mengekspor data |
| `.settings` | Mengatur konfigurasi modul |

## Schema `permissions` Saat Ini - Sudah Memadai, Tidak Perlu Migrasi

| Kolom | Tipe | Catatan |
|---|---|---|
| `id` | bigint unsigned AI | PK |
| `key` | varchar(128) | UNIQUE (`permissions_key_unique`) - target ~301 baris |
| `name` | varchar(255) | label UI Bahasa Indonesia |
| `domain` | varchar(64) | **sudah ada** - dipakai `orderBy('domain, key')` di matrix UI |
| `description` | text | nullable - **saat ini kosong semua**, isi saat seeding |
| `is_system` | tinyint(1) | **sudah ada** - set `1` agar terkunci dari UI hapus |
| `created_at` / `updated_at` | datetime | |

**Tabel pendukung:**

| Tabel | Kolom | Constraint |
|---|---|---|
| `role_permissions` | `role_id`, `permission_id`, `granted_by`, `created_at` | PK komposit (`role_id`,`permission_id`) |
| `roles` | `id`, `key` (UNIQUE), `name`, `description`, `is_system` | |

**Sudah tersedia & berfungsi:**

- `RoleController::matrix()` - merender `dashboard/access/role_permission_matrix`
- `RoleController::syncMatrix()` - `POST dashboard/akses/role/matrix/sync`
- `RoleManagementService::syncRolePermissions()`
- `RoleManagementService::listPermissions()` - `orderBy('domain, key')`

**Kesimpulan:** UI *Role & Hak Akses* **sudah siap**. Yang dibutuhkan hanya **katalog permission yang granular**.

---

## Modul 1 - Dashboard / Utamanya

**Route group:** `dashboard.home`, `dashboard.summary.kpi` | **Controller:** `Admin\DashboardController`

| Sub-Fitur | Controller | Permission Key | Deskripsi |
|---|---|---|---|
| Ringkasan KPI | `DashboardController::index` | `dashboard.view` | Sudah ada - Lihat dashboard utama |
| Indikator Sistem | `DashboardController::systemIndicators` | `dashboard.indicators.view` | Lihat kartu indikator sistem |
| Aktivitas Terkini | `DashboardController::recentActivity` | `dashboard.activity.view` | Lihat jejak aktivitas |
| Antrean Persetujuan | `DashboardController::approvalQueue` | `approvals.review` | Lihat antrean approval (konsisten dgn filter) |
| Riwayat Export | `DashboardController::exportHistory` | `reports.export.view` | Lihat riwayat export |
| Unduh Report | `DashboardController::exportReport` | `reports.export.download` | Jalankan & unduh laporan |

> **Catatan:** `dashboard/indikator-sistem` reachable via sidebar link.

---

## Modul 2 - Kelola Landing Page

**Route group:** `dashboard.landing.*` - **60 route, 10 controller** | modul terbesar #2

| Sub-Fitur | Controller | Permission Key |
|---|---|---|
| Slider Hero | `Admin\Landing\SliderController` | `landing.slider.view` / `.create` / `.edit` / `.delete` / `.toggle` |
| Pengumuman | `Admin\Landing\AnnouncementController` | `landing.announcement.view` / `.create` / `.edit` / `.delete` / `.toggle` / `.settings` |
| Sorotan / Highlight | `Admin\Landing\HighlightFeatureController` | `landing.highlight.view` / `.create` / `.edit` / `.delete` / `.toggle` |
| Testimoni | `Admin\Landing\TestimonialController` | `landing.testimonial.view` / `.create` / `.edit` / `.delete` / `.toggle` |
| Tim | `Admin\Landing\TeamMemberController` | `landing.team.view` / `.create` / `.edit` / `.delete` / `.toggle` |
| Banner Halaman | `Admin\Landing\PageBannerController` | `landing.banner.view` / `.edit` |
| Kategori Layanan | `Admin\Landing\ServiceCategoryController` | `landing.category.view` / `.create` / `.edit` / `.delete` / `.toggle` |
| Profil Lembaga | `Admin\Landing\InstitutionProfileController` | `landing.profile.view` / `.edit` |
| Portal Eksternal | `Admin\Landing\ExternalPortalController` | `landing.portal.view` / `.create` / `.edit` / `.delete` / `.toggle` |
| Spotlight Hub | `Admin\Landing\SpotlightHubController` | `landing.spotlight.view` / `.toggle` / `.order` / `.settings` |

**Total: 52 permission key**

---

## Modul 3 - Manajemen Akses

**Route group:** `dashboard.access.*` (26) + `dashboard.profile.*` (8)

| Sub-Fitur | Controller | Permission Key | Status |
|---|---|---|---|
| Manajemen Pengguna | `Admin\UserController` | `access.user.view` / `.create` / `.edit` / `.delete` / `.activate` / `.suspend` / `.resetpassword` | legacy `user.manage` |
| Role & Permission | `Admin\RoleController` | `access.role.view` / `.create` / `.edit` / `.delete` / `.matrix` | legacy `role.manage` |
| **Matrix Sinkronisasi** | `RoleController::syncMatrix` | `access.role.matrix.sync` | SuperAdmin only |
| CRUD Permission | `Admin\PermissionController` | `access.permission.view` / `.create` / `.edit` / `.delete` | SuperAdmin only |
| Unit Organisasi | `Admin\OrganizationController` | `access.organization.view` / `.create` / `.edit` / `.activate` / `.deactivate` | - |
| Peran Pengadaan | `Admin\ProcurementRoleController` | `access.procurementrole.view` / `.create` / `.edit` / `.delete` / `.activate` / `.deactivate` / `.assign` / `.verify` | - |
| Verifikasi Profil | `Admin\UserProfileVerificationController` | `access.verification.view` / `.approve` / `.reject` / `.revision` | legacy `user.verify` |
| Permohonan Role | `Admin\RoleRequestReviewController` | `access.rolerequest.view` / `.approve` / `.reject` / `.revision` | - |
| Audit Trail | `Admin\AuditController` | `audit.view` (sudah ada) / `audit.export` / `audit.detail` | legacy ada |
| Profil Saya | `Admin\ProfileController` | `profile.view` / `.edit` / `profile.password` / `profile.preferences` | - |

**Total: 38 permission key**

---

## Modul 4 - Arena Belajar

**Route group:** `dashboard.learning.*` - **84 route, 19 controller** | **modul terbesar #1**

| Sub-Fitur | Controller | Permission Key |
|---|---|---|
| Ringkasan | `Learning\LearningOverviewController` | `learning.view` |
| Program | `Learning\ProgramController` | `learning.program.view` / `.create` / `.edit` / `.delete` |
| Mata Kuliah | `Learning\CourseController` | `learning.course.view` / `.create` / `.edit` / `.delete` |
| **Kelas** | `Learning\LearningClassController` | `learning.class.view` / `.create` / `.update` / `.delete` (sudah ada) + `learning.class.assign` / `.duplicate` / `.gradebook` |
| Kurikulum | `Learning\CurriculumController` | `learning.curriculum.view` / `.module.*` / `.lesson.*` / `.quiz.*` / `.question.*` / `.assignment.*` |
| Modul | `Learning\ModuleController` | `learning.module.view` / `.create` / `.edit` / `.update` |
| Materi / Lesson | `Learning\LessonController` | `learning.lesson.view` / `.create` / `.edit` / `.update` |
| Assessment | `Learning\AssessmentManagementController` | `learning.assessment.view` / `.quiz.*` / `.question.import` / `.assignment.*` / `.grade` |
| Bank Soal | `Learning\QuizController::questionBank` | `learning.questionbank.view` |
| Enrollment | `Learning\EnrollmentController` | `learning.enrollment.view` / `.create` / `.approve` / `.reject` / `.status` / `.delete` |
| Penilaian | `Learning\GradingController` | `learning.grading.view` / `.grade` |
| Progress Monitoring | `Learning\ProgressMonitoringController` | `learning.progress.view` / `.detail` |
| Assign Pengajar | `Learning\TrainerAssignmentController` | `learning.trainer.assign` / `.revoke` |
| Kelayakan Sertifikat | `Learning\CertificateEligibilityController` | `learning.certificate.generate` / `.batch` |
| Laporan Belajar | `Learning\LearningReportController` | `learning.report.view` |

**Total: 45 permission key**

> **Catatan:** `learning.class.{create,update,delete}` sudah ada di DB (`is_system=0`) tetapi **hanya dipakai untuk UI visibility** di `LearningClassController.php:294-300` - belum menjadi penjaga endpoint.

---

## Modul 5 - Bunga Rampai

**Route group:** `dashboard/bunga-rampai` (`Routes.php:550`) - **26 route, 13 controller**

**PENTING:** Memakai namespace `App\Controllers\Knowledge` - lihat temuan **W3**

| Sub-Fitur | Controller | Permission Key |
|---|---|---|
| Ringkasan | `Knowledge\KnowledgeOverviewController` | `bunga.view` |
| Artikel | `Knowledge\ArticleController` | `bunga.article.view` / `.create` / `.edit` / `.delete` |
| FAQ | `Knowledge\FaqController` | `bunga.faq.view` / `.create` / `.edit` / `.delete` |
| Studi Kasus | `Knowledge\CaseStudyController` | `bunga.casestudy.view` / `.create` / `.edit` / `.delete` |
| Template Dokumen | `Knowledge\DocumentTemplateController` | `bunga.template.view` / `.create` / `.edit` / `.delete` |
| Media & Galeri | `Knowledge\KnowledgeMediaController` | `bunga.media.view` / `.create` / `.edit` / `.delete` |
| Kategori | `Knowledge\CategoryController` | `bunga.category.view` / `.create` / `.edit` / `.delete` |
| Tag | `Knowledge\TagController` | `bunga.tag.view` / `.create` / `.edit` / `.delete` |
| Sorotan Carousel | `Knowledge\KnowledgeCarouselController` | `bunga.carousel.view` / `.create` / `.disable` |

**Total: 35 permission key**

> **`kms.manage` (lama) tetap dipertahankan** sebagai *superset guard* di `BaseKnowledgeController.php:36` sampai semua sub-controller selesai dimigrasikan. Predictor dibiarkan bekerja selama transisi.

---

## Modul 6 - Regulasi PBJ

**Route group:** `dashboard.regulations.*` - **38 route, 44 method** | Controller: `Admin\RegulationController`

| Sub-Fitur | Controller | Permission Key |
|---|---|---|
| Dokumen Regulasi | `Admin\RegulationController` | `regulation.view` / `.create` / `.edit` / `.delete` |
| **Alur Persetujuan** | `RegulationController::{submitReview, approve, reject, requestRevision, publish, archive}` | `regulation.approve` / `regulation.reject` / `regulation.revision` / `regulation.publish` / `regulation.archive` |
| Versi | `RegulationController::{versions, versionShow, addVersion, setCurrentVersion}` | `regulation.version.view` / `.add` / `.current` |
| Relasi | `RegulationController::relations*` | `regulation.relation.view` / `.create` / `.delete` |
| Kategori | `RegulationController::category*` | `regulation.category.view` / `.create` / `.edit` / `.update` / `.delete` / `.toggle` |
| Tahapan PBJ | `RegulationController::procurementStage*` | `regulation.stage.view` / `.create` / `.edit` / `.update` / `.delete` / `.toggle` |
| Kompetensi | `RegulationController::competenc*` | `regulation.competency.view` / `.create` / `.edit` / `.update` / `.delete` / `.toggle` / `.attach` |
| Log Unduhan | `RegulationController::downloadLogs` | `regulation.download.view` |
| Regulasi Publik | `Regulation\RegulationController` | *(public - tidak perlu permission)* |

**Total: 38 permission key**

> **Controller terbesar di aplikasi:** 44 public method dalam 1 file - kandidat kuat untuk dipecah saat implementasi.

---

## Modul 7 - Komunikasi & Event

**Route group:** `dashboard.communication.*` - **24 route, 7 controller**

| Sub-Fitur | Controller | Permission Key |
|---|---|---|
| Berita | `Communication\NewsController` | `communication.news.view` / `.create` / `.edit` / `.delete` |
| Event & Agenda | `Communication\EventController` | `communication.event.view` / `.create` / `.edit` / `.delete` |
| Registrasi Event | `Communication\EventRegistrationController` | `communication.registration.view` |
| Presensi | `Communication\AttendanceController` | `communication.attendance.view` |
| Pesan Masuk | `Admin\Communication\ContactMessageController` | `communication.inbox.view` / `.reply` / `.status` / `.delete` |
| Newsletter | `Admin\Communication\NewsletterController` | `communication.newsletter.view` / `.delete` / `.export` |
| Helpdesk Widget | `Admin\Communication\HelpdeskWidgetController` | `communication.helpdesk.view` / `.settings` / `.topic.create` / `.topic.update` / `.topic.delete` / `.topic.toggle` |

**Total: 26 permission key**

---

## Modul 8 - Sertifikat

**Route group:** `dashboard.certificates.*` - **29 route, 7 controller**

| Sub-Fitur | Controller | Permission Key | Status |
|---|---|---|---|
| Kelola Sertifikat | `Certificate\CertificateController` | `certificate.view` / `.create` / `.edit` / `.revoke` | legacy `certificate.manage` |
| Template | `Certificate\CertificateTemplateController` | `certificate.template.view` / `.create` / `.edit` / `.activate` / `.deactivate` | sudah ada |
| Batch Event | `Certificate\CertificateBatchController` | `certificate.batch.view` / `.event.*` | - |
| Batch Learning | `Certificate\CertificateLearningController` | `certificate.batch.view` / `.learning.*` | - |
| Event Sertifikat | `Certificate\CertificateEventController` | `certificate.event.view` | - |
| **Persetujuan** | `Certificate\CertificateApprovalController` | `certificate.approve` / `.reject` | legacy sudah ada |
| Unduh | `Certificate\CertificateDownloadController` | `certificate.download` | - |
| Verifikasi | `Landing\CertificateVerificationController` | *(public)* | - |

**Total: 20 permission key**

---

## Modul 9 - Government Intelligence

**Route group:** `dashboard.intelligence.*` - **3 route, 4 controller** | *read-mostly*

| Sub-Fitur | Controller | Permission Key |
|---|---|---|
| Dashboard Intelijen | `Intelligence\IntelligenceDashboardController` | `intelligence.view` |
| Kesenjangan Kompetensi | `IntelligenceDashboardController::competencyGap` | `intelligence.gap.view` |
| SDM JF PPBJ | `Intelligence\JfPpbjController` | `intelligence.jfppbj.view` / `.detail` / `.histories` / `.levels` |
| Sertifikat Kompetensi | `Intelligence\CompetencyCertificateController` | `intelligence.competency.view` / `.detail` / `.types` |
| Data OPD | `Intelligence\OpdController` | `intelligence.opd.view` / `.create` / `.detail` |

**Total: 12 permission key**

> **Prefix `intelligence.` tidak ada** di `$validPrefixes` `AuthorizationService` - lihat **W4**.

---

## Modul 10 - Pusat Laporan

**Route group:** `dashboard.reports.*` - **6 route**

| Sub-Fitur | Controller | Permission Key |
|---|---|---|
| Laporan Pembelajaran | `DashboardController::learningReport` / `LearningReportController` | `reports.learning.view` |
| Laporan Event | `DashboardController::eventReport` | `reports.event.view` |
| Laporan Sertifikat | `DashboardController::certificateReport` | `reports.certificate.view` |
| Laporan Kompetensi | `DashboardController::competencyReport` | `reports.competency.view` |
| Laporan Pengguna | `DashboardController::userReport` | `reports.user.view` |
| Export | `DashboardController::export{Report,History}` | `reports.export.view` / `.download` |

**Total: 8 permission key**

---

## Modul 11 - Survei Kepuasan (IKM)

**Route group:** `dashboard.survey.*` - **3 route** | Controller: `Admin\Survey\SatisfactionSurveyAdminController`

| Sub-Fitur | Controller | Permission Key |
|---|---|---|
| Hasil & Statistik | `SatisfactionSurveyAdminController::index` | `survey.view` |
| Aspek Survei | `SatisfactionSurveyAdminController::updateAspect` | `survey.aspect.edit` |
| Hapus Respons | `SatisfactionSurveyAdminController::deleteResponse` | `survey.response.delete` |

**Total: 3 permission key**

---

## Modul 12 - Sistem & Pengaturan

**Route group:** `dashboard.system.*` - **26 route**

| Sub-Fitur | Controller | Permission Key |
|---|---|---|
| Pengaturan Umum | `SettingController::general` | `system.settings` (belum ada) + `system.settings.general.edit` |
| Organisasi | `SettingController::organization` | `system.settings.organization.edit` |
| Learning | `SettingController::learning` | `system.settings.learning.edit` |
| Knowledge | `SettingController::knowledge` | `system.settings.knowledge.edit` |
| Regulasi | `SettingController::regulation` | `system.settings.regulation.edit` |
| Berita | `SettingController::news` | `system.settings.news.edit` |
| Event | `SettingController::event` | `system.settings.event.edit` |
| Sertifikat | `SettingController::certificate` | `system.settings.certificate.edit` |
| Notifikasi | `SettingController::notification` | `system.settings.notification.edit` |
| Integrasi | `SettingController::integration` | `system.settings.integration.edit` |
| Keamanan | `SettingController::security` | `system.settings.security.edit` |
| Maintenance | `SettingController::maintenance` | `system.maintenance` |
| Persetujuan | `Admin\ApprovalController` | `approval.view` / `.approve` / `.reject` / `.bulk` / `.history` |
| Notifikasi | `Admin\NotificationController` | `notification.view` / `.send` / `.delete` / `.read` |

**Total: 18 permission key**

> `SettingController.php:23` memakai `system.settings` - key ini **belum ada** di DB (lihat W1).

---

## II.13 Ringkasan Katalog Permission (Per Domain)

| Domain | Prefix Key | Jumlah |
|---|---|:--:|
| `dashboard` | `dashboard.*` | 6 |
| `landing` | `landing.*` | 52 |
| `access` | `access.*` + `audit.*` + `profile.*` | 38 |
| `learning` | `learning.*` | 45 |
| `bunga` | `bunga.*` | 35 |
| `regulation` | `regulation.*` | 38 |
| `communication` | `communication.*` | 26 |
| `certificate` | `certificate.*` | 20 |
| `intelligence` | `intelligence.*` | 12 |
| `reports` | `reports.*` | 8 |
| `survey` | `survey.*` | 3 |
| `system` | `system.*` + `approval.*` + `notification.*` | 18 |
| | **TOTAL** | **~301** |

**Rekomendasi:** seed **bertahap per modul** (~25 key/modul) agar `role_permission_matrix.php` tetap terbaca. Mulai dari **Modul 12 + Modul 3** karena paling dekat ke mekanisme yang sudah ada.

---

# III. REKOMENDASI DESAIN IMPLEMENTASI (Action Plan)

## III.1 Menyimpan Permission TANPA Migrasi Baru

Skema `permissions` **sudah lengkap** (`key`, `name`, `domain`, `description`, `is_system`). Cukup pakai **CI4 Seeder** + 1 command idempotent.

### Step 1 - `app/Database/Seeds/PermissionCatalogSeeder.php`

```php
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionCatalogSeeder extends Seeder
{
    /**
     * Format: 'key' => ['name', 'domain', 'description']
     * is_system = 1 -> terkunci dari UI hapus PermissionController
     */
    private const CATALOG = [
        // -- Modul 1: Dashboard --
        'dashboard.view' => ['Lihat Dashboard', 'dashboard', 'Melihat ringkasan dashboard utama'],

        // -- Modul 12: Sistem --
        'system.settings' => ['Akses Pengaturan Sistem', 'system', 'Mengakses halaman pengaturan sistem'],

        // ... total +/-301 key, dikelompokkan per modul
    ];

    public function run()
    {
        $db      = db_connect();
        $now     = date('Y-m-d H:i:s');
        $added   = 0;
        $skipped = 0;

        foreach (self::CATALOG as $key => [$name, $domain, $desc]) {
            $exists = $db->table('permissions')->where('key', $key)->countAllResults();

            if ($exists > 0) {
                $skipped++;   // idempotent - aman diulang
                continue;
            }

            $db->table('permissions')->insert([
                'key'         => $key,
                'name'        => $name,
                'domain'      => $domain,
                'description' => $desc,
                'is_system'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            $added++;
        }

        service('logger')->info("PermissionCatalogSeeder: +{$added} permission, {$skipped} skip.");
    }
}
```

### Step 2 - Command wrapper (idempotent, bisa dipanggil ulang)

```php
<?php

namespace App\Commands;

use App\Database\Seeds\PermissionCatalogSeeder;
use CodeIgniter\CLI\Command;

class PermissionSync extends Command
{
    protected $group       = 'RBAC';
    protected $name        = 'permission:sync';
    protected $description = 'Seed katalog permission modular (idempotent).';

    protected $arguments = [
        ['--report', 'Tampilkan key yang belum di-grant ke role manapun.'],
    ];

    public function index(): int
    {
        (new PermissionCatalogSeeder())->run();

        if ($this->option('report')) {
            $ungranted = db_connect()->table('permissions')
                ->select('permissions.*, COUNT(role_permissions.role_id) AS roles', false)
                ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
                ->groupBy('permissions.id')
                ->having('roles', 0)
                ->get()
                ->getResultArray();

            $this->warn('Permission belum di-grant ke role manapun:');
            foreach ($ungranted as $p) {
                $this->warn('  - ' . $p['key']);
            }
        }

        return EXIT_SUCCESS;
    }
}
```

### Step 3 - Jalankan

```bash
php spark permission:sync             # seed permission baru (aman diulang)
php spark permission:sync --report    # audit: key yang belum di-grant
```

### Step 4 - Auto-grant ke role existing (sangat penting)

```sql
-- role 10 = super_administrator (bypass penuh, grant semua)
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT 10, p.id, 13, NOW() FROM permissions p;

-- role 11 = administrator (grant semua)
INSERT IGNORE INTO role_permissions (role_id, permission_id, granted_by, created_at)
SELECT 11, p.id, 13, NOW() FROM permissions p;
```

> **Jalankan SEBELUM mengaktifkan filter dinamis**, agar admin tidak terkunci (fail-open saat transisi).

---

## III.2 Memasang Guard Permission Dinamis

### OPSI A - Perluas `AuthenticationFilter` (PALING REKOMENDASI)

Ganti 4 blok hardcoded dengan **pemapangan path -> permission key** yang digabung dengan cek role.

```php
// app/Filters/AuthenticationFilter.php

/**
 * Path prefix -> daftar permission key yang diterima (salah satu cukup).
 * Super Administrator selalu bypass.
 */
private array $zonePermissionMap = [
    // -- Modul 3: Manajemen Akses --
    'dashboard/akses/pengguna'           => ['access.user.view', 'user.manage'],
    'dashboard/akses/role'              => ['access.role.view', 'role.manage'],
    'dashboard/akses/permission'        => ['access.permission.view'],
    'dashboard/akses/unit-organisasi'   => ['access.organization.view'],
    'dashboard/akses/peran-pengadaan'   => ['access.procurementrole.view'],
    'dashboard/akses/audit'             => ['audit.view'],
    'dashboard/akses/verifikasi-profil' => ['access.verification.view', 'user.verify'],
    'dashboard/akses/permohonan-role'   => ['access.rolerequest.view'],

    // -- Modul 12: Sistem --
    'dashboard/sistem/pengaturan'       => ['system.settings'],
    'dashboard/sistem/maintenance'      => ['system.maintenance'],
    'dashboard/sistem/approval'         => ['approval.view', 'approvals.review'],
    'dashboard/sistem/audit-trail'      => ['audit.view'],

    // -- Modul 4: Arena Belajar --
    'dashboard/pembelajaran/kelas'       => ['learning.class.view'],
    'dashboard/pembelajaran/assessment'  => ['learning.assessment.view'],
    'dashboard/pembelajaran/enrollments' => ['learning.enrollment.view'],

    // -- Modul 5: Bunga Rampai --
    'dashboard/bunga-rampai/artikel'     => ['bunga.article.view', 'kms.manage'],
    'dashboard/bunga-rampai/faq'         => ['bunga.faq.view', 'kms.manage'],
    'dashboard/bunga-rampai/studi-kasus' => ['bunga.casestudy.view', 'kms.manage'],
    'dashboard/bunga-rampai/kategori'    => ['bunga.category.view', 'kms.manage'],
    'dashboard/bunga-rampai/media-galeri'=> ['bunga.media.view', 'kms.manage'],

    // -- Modul 6: Regulasi --
    'dashboard/regulasi/dokumen'         => ['regulation.view'],
    'dashboard/regulasi/kategori'        => ['regulation.category.view'],
    'dashboard/regulasi/tahapan-pbj'     => ['regulation.stage.view'],

    // -- Modul 7: Komunikasi --
    'dashboard/komunikasi/berita'        => ['communication.news.view'],
    'dashboard/komunikasi/event-agenda'  => ['communication.event.view'],
    'dashboard/komunikasi/pesan-masuk'   => ['communication.inbox.view'],

    // -- Modul 8: Sertifikat --
    'dashboard/sertifikat/riwayat'       => ['certificate.view'],
    'dashboard/sertifikat/template'      => ['certificate.template.view'],
    'dashboard/sertifikat/batch'         => ['certificate.batch.view'],

    // -- Modul 9: Intelligence --
    'dashboard/intelligence'             => ['intelligence.view'],
    'dashboard/intelligence/sdm-jf-ppbj' => ['intelligence.jfppbj.view'],

    // -- Modul 11: Survei --
    'dashboard/survei-kepuasan'          => ['survey.view'],
];

public function before(RequestInterface $request, $arguments = null)
{
    // ... Fase 1-4 yang sudah ada, TIDAK diubah ...

    // -- FASE 5: Dynamic Zone Guard (menggantikan blok 1-4 hardcoded) --
    return $this->enforceZone($path);
}

private function enforceZone(string $path)
{
    $auth = new AuthorizationService();

    // Super Administrator = bypass total
    if ($auth->isSuperAdmin()) {
        return null;
    }

    foreach ($this->zonePermissionMap as $prefix => $acceptedKeys) {
        if (! str_starts_with($path, $prefix)) {
            continue;
        }

        foreach ($acceptedKeys as $key) {
            if ($auth->can($key)) {
                return null;              // punya salah satu -> lolos
            }
        }

        return $this->deny();           // tidak punya satu pun -> tolak
    }

    return null;
}

private function deny()
{
    return service('response')
        ->setStatusCode(403)
        ->setBody(view('errors/html/error_403', [
            'message' => 'Akses ditolak: role Anda tidak memiliki izin untuk modul ini.',
        ]));
}
```

**Keunggulan:**

- Satu tempat, mudah dipelihara
- `super_administrator` tetap bypass total
- Role kustom langsung bekerja tanpa ubah controller
- Support multi-key (backward compatible dengan `kms.manage` legacy)

---

### OPSI B - `BaseAdminController` (granularitas level aksi)

```php
<?php

namespace App\Controllers;

use App\Services\Identity\AuthorizationService;
use CodeIgniter\Exceptions\ForbiddenException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseAdminController extends BaseController
{
    protected ?AuthorizationService $authz = null;

    public function initController(RequestInterface $r, ResponseInterface $s, LoggerInterface $l): void
    {
        parent::initController($r, $s, $l);
        $this->authz = new AuthorizationService();
    }

    /** Guard level aksi - mis. $this->mustCan('learning.class.create'); */
    protected function mustCan(string $permission): void
    {
        if ($this->can($permission)) {
            return;
        }

        throw ForbiddenException::forPageNotFound();
    }

    /** Guard multi-key - cukup salah satu */
    protected function mustCanAny(string ...$permissions): void
    {
        foreach ($permissions as $p) {
            if ($this->can($p)) {
                return;
            }
        }

        throw ForbiddenException::forPageNotFound();
    }

    /** Helper untuk view - sembunyikan tombol */
    protected function can(string $permission): bool
    {
        return $this->authz->isSuperAdmin() || $this->authz->can($permission);
    }
}
```

Penerapan di controller target:

```php
class LearningClassController extends BaseAdminController
{
    public function create()
    {
        $this->mustCan('learning.class.create');   // 1 baris guard
        // ...
    }

    public function delete($id)
    {
        $this->mustCan('learning.class.delete');
        // ...
    }
}
```

---

### OPSI C - Perbaiki `AuthorizationService` (WAJIB - menutup W4)

```php
// AuthorizationService.php:41-45 - tambahkan prefix baru:
$validPrefixes = [
    'dashboard.', 'admin.', 'system.', 'access.', 'content.',
    'regulation.', 'learning.', 'lms.', 'kms.', 'bunga.',
    'event.', 'survey.', 'communication.', 'certificate.',
    'intelligence.', 'reports.', 'approval.', 'notification.',
    'user.', 'role.', 'audit.', 'profile.',
];
```

---

### OPSI D - Perbaiki `BaseKnowledgeController` (WAJIB - menutup W5)

```php
// BaseKnowledgeController.php:38 - GANTI role key legacy
if (! in_array('super_administrator', $roles, true) && ! in_array('administrator', $roles, true)) {
if (! $this->authz->isSuperAdmin() && ! $this->authz->isAdmin()) {
```

---

## III.3 Menyembunyikan Menu Sidebar

`app/Views/dashboard/components/sidebar.php` - bungkus blok menu dengan cek permission:

```php
<?php
$perms = (array) session('permissions');
$isSu  = $isSuperAdmin ?? false;
?>

<?php if ($isSu || in_array('bunga.article.view', $perms, true)): ?>
    <a href="<?= site_url('dashboard/bunga-rampai/artikel') ?>">Artikel</a>
<?php endif; ?>
```

Atau lebih bersih: sediakan helper `$this->can('bunga.article.view')` bila layout mewarisi `BaseAdminController`.

**36 link sidebar** yang perlu dibungkus:

`dashboard/akses/pengguna` - `dashboard/akses/role` - `dashboard/akses/unit-organisasi` - `dashboard/akses/verifikasi-profil` - `dashboard/bunga-rampai/artikel` - `dashboard/bunga-rampai/kategori` - `dashboard/bunga-rampai/media-galeri` - `dashboard/indikator-sistem` - `dashboard/intelligence` - `dashboard/intelligence/sdm-jf-ppbj` - `dashboard/intelligence/sertifikat-kompetensi` - `dashboard/komunikasi/berita` - `dashboard/komunikasi/event-agenda` - `dashboard/komunikasi/pesan-masuk` - `dashboard/landing/portal-eksternal` - `dashboard/landing/profil-lembaga` - `dashboard/landing/slider` - `dashboard/landing/tim` - `dashboard/laporan/pembelajaran` - `dashboard/laporan/riwayat-export` - `dashboard/pembelajaran` - `dashboard/pembelajaran/assessment` - `dashboard/pembelajaran/kelas` - `dashboard/pembelajaran/kelayakan-sertifikat` - `dashboard/regulasi/dokumen` - `dashboard/regulasi/kategori` - `dashboard/regulasi/tahapan-pbj` - `dashboard/sertifikat/batch` - `dashboard/sertifikat/riwayat` - `dashboard/sertifikat/template` - `dashboard/sistem/approval` - `dashboard/sistem/audit-trail` - `dashboard/sistem/notifikasi` - `dashboard/sistem/pengaturan` - `dashboard/survei-kepuasan`

---

## III.4 Urutan Implementasi (Roadmap)

| Fase | Pekerjaan | File yang Disentuh | Estimasi |
|:--:|---|---|:--:|
| **0** | Fix `AuthorizationService` prefix (W4) + `BaseKnowledgeController` role key (W5) | 2 file | 15 menit |
| **1** | `PermissionCatalogSeeder` + `spark permission:sync` (seed +/-301 key) | 2 file baru | 2 jam |
| **2** | Auto-grant ke `super_administrator` + `administrator` | 1 SQL | 10 menit |
| **3** | `BaseAdminController` + `mustCan()` | 1 file baru | 1 jam |
| **4** | `AuthenticationFilter::enforceZone()` (ganti blok hardcoded) | 1 file | 2 jam |
| **5** | Sidebar menu hiding (36 link) | 1 file | 2 jam |
| **6** | Uji role kustom (Bunga Rampai / Regulasi / Arena Belajar) | - | 1 jam |

**Total estimasi: ~8 jam kerja**

### Skenario Uji (Fase 6)

| Role Kustom | Diberikan Permission | Harus Bisa | Harus Ditolak |
|---|---|---|---|
| **Bunga Rampai** | `bunga.*` + `dashboard.view` | `/dashboard/bunga-rampai/artikel`, `/faq`, `/studi-kasus`, `/kategori`, `/media-galeri` | `/dashboard/regulasi/dokumen`, `/dashboard/pembelajaran/kelas`, `/dashboard/sistem/pengaturan` |
| **Regulasi** | `regulation.*` + `dashboard.view` | `/dashboard/regulasi/{dokumen,kategori,tahapan-pbj}` | `/dashboard/bunga-rampai/artikel`, `/dashboard/sertifikat/riwayat` |
| **Arena Belajar** | `learning.view`, `learning.class.view` + `dashboard.view` | `/dashboard/pembelajaran`, `/kelas`, `/assessment` | `/dashboard/regulasi/*`, `/dashboard/sistem/*` |
| **Read-only Audit** | `audit.view` + `dashboard.view` | `/dashboard/akses/audit`, `/dashboard/sistem/audit-trail` | semua aksi tulis |

---

# IV. CATATAN & REKOMENDASI TAMBAHAN

1. **Jalankan `php spark permission:sync --report`** setelah tiap seeding untuk melihat key yang belum diberikan ke role mana pun - mencegah role kustom baru langsung terkunci.

2. **Selalu grant `super_administrator` semua permission** di Fase 2. Filter ini sudah bypass super admin, dan `BaseAdminController::mustCan()` juga akan - jadi konsisten.

3. **Set `is_system = 1`** untuk semua key hasil seeder -> `PermissionController::delete` sebaiknya menolak penghapusan key ini agar tidak merusak role matrix.

4. **Isi `description`** saat seeding - semua permission saat ini kosong, dan matrix UI akan jauh lebih informatif.

5. **Pengguna `superadmin@begawipbj.go.id`** (id 13, role `super_administrator`) adalah akun yang tepat untuk pengujian peran baru setelah Fase 2.

6. **`learning.class.*`** sudah ada di DB dengan `is_system=0` - pertahankan, tambahkan key granular di sebelahnya, jangan ganggu yang existing.

7. **Jalankan regression suite** setelah tiap fase:

   ```bash
   php spark dashboard:full:audit      # harus tetap 36/36
   php spark landing:full:audit        # harus tetap 40/40
   php spark test:admin-approvals       # harus tetap 11/11
   php spark test:admin-system          # harus tetap 8/8
   php spark test:admin-shell          # harus tetap 5/5
   php spark test:admin-learning-cert   # harus tetap 10/10
   ```

---

# V. LAMPIRAN - PETA ROUTE PER MODUL

Distribusi route bernama `dashboard.*` (**356 total**):

| Prefix Route Name | Jumlah | Modul |
|---|:--:|---|
| `dashboard.learning.*` | 84 | Arena Belajar |
| `dashboard.landing.*` | 60 | Kelola Landing Page |
| `dashboard.regulations.*` | 38 | Regulasi PBJ |
| `dashboard.certificates.*` | 29 | Sertifikat |
| `dashboard.access.*` | 26 | Manajemen Akses |
| `dashboard.system.*` | 26 | Sistem & Pengaturan |
| `dashboard.knowledge.*` | 26 | Bunga Rampai |
| `dashboard.communication.*` | 24 | Komunikasi & Event |
| `dashboard.auth.*` | 21 | *(auth - tidak perlu permission)* |
| `dashboard.profile.*` | 8 | Manajemen Akses |
| `dashboard.reports.*` | 6 | Pusat Laporan |
| `dashboard.survey.*` | 3 | Survei Kepuasan |
| `dashboard.intelligence.*` | 3 | Government Intelligence |
| `dashboard.home` | 1 | Dashboard / Utamanya |
| `dashboard.summary.*` | 1 | Dashboard / Utamanya |
| **TOTAL** | **356** | |

### Distribusi View (`app/Views/dashboard/`)

| Direktori | File | Modul |
|---|:--:|---|
| `learning` | 33 | Arena Belajar |
| `knowledge` | 27 | Bunga Rampai |
| `landing` | 20 | Kelola Landing Page |
| `regulation` | 18 | Regulasi PBJ |
| `components` | 17 | *(shared: sidebar, header, dll)* |
| `communication` | 17 | Komunikasi & Event |
| `certificates` | 16 | Sertifikat |
| `auth` | 15 | *(auth)* |
| `settings` | 14 | Sistem & Pengaturan |
| `access` | 14 | Manajemen Akses |
| `intelligence` | 13 | Government Intelligence |
| `users` | 10 | Manajemen Akses |
| `reports` | 8 | Pusat Laporan |
| `profile` | 4 | Manajemen Akses |
| `approvals` | 4 | Sistem & Pengaturan |
| `audit` | 4 | Manajemen Akses |
| `errors` | 3 | *(shared)* |
| `layouts` | 3 | *(shared)* |
| `home` | 2 | Dashboard / Utamanya |
| `organization` | 1 | Manajemen Akses |
| `survey` | 1 | Survei Kepuasan |
| **TOTAL** | **226** | |

---

# VI. REFERENSI FILE KUNCI

| File | Peran dalam Audit |
|---|---|
| `app/Config/Filters.php` | Registrasi grup filter `auth` untuk `dashboard/*` |
| `app/Filters/AuthenticationFilter.php` | **4 zona guard hardcoded** - target refactor Opsi A |
| `app/Services/Identity/AuthorizationService.php` | `can()`, `isSuperAdmin()`, `isAdmin()`, `hasDashboardAccess()` - **W4** |
| `app/Models/UserModel.php` | `getPermissions()` - resolve permission via join 3 tabel |
| `app/Services/Identity/AuthenticationService.php` | Login: set session `roles` + `permissions`, regenerate session |
| `app/Controllers/Knowledge/BaseKnowledgeController.php` | **Satu-satunya** abstract guard - **W5** di line 38 |
| `app/Controllers/Admin/RoleController.php` | `matrix()` + `syncMatrix()` - UI sudah siap |
| `app/Services/Admin/RoleManagementService.php` | CRUD role/permission, `syncRolePermissions()` |
| `app/Views/dashboard/components/sidebar.php` | 36 link menu - target Fase 5 |
| `app/Config/Routes.php` | 884 baris, 356 route bernama `dashboard.*` |

---

## Kesimpulan

**Tidak ada file kode yang diubah dalam pembuatan dokumen ini.**

Fondasi RBAC (tabel, service, UI matrix) **sudah ada dan siap**. Yang dibutuhkan:

1. **Fase 0** - 2 perbaikan kecil yang menutup W4 & W5 (15 menit)
2. **Fase 1-2** - seed katalog permission + auto-grant ke admin (2 jam 10 menit)
3. **Fase 3-5** - guard dinamis + penyembunyian menu (5 jam)
4. **Fase 6** - validasi dengan role kustom (1 jam)

**Langkah berikutnya yang disarankan: Fase 0.**




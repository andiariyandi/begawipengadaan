Viewed TrainerController.php:1-60
Ran command: Find render in TrainerController (`Get-Content app\Controllers\Workspace\TrainerController.php | Select-String -Pattern "function render\(" -Context 0, 15`)
Ran command: List trainer views (`Get-ChildItem -Path app\Views\main\workspace\pages\trainer -File | Select-Object Name, Length`)
Viewed WorkspaceHomeController.php:1-60
Viewed ProfileController.php:1-60
Viewed profile.php:1-20
Ran command: Search profile.update in Routes (`Select-String -Path app\Config\Routes.php -Pattern "profile\.update"`)
Created test_route.php
Ran command: Run test_route.php (`php "C:\Users\Asus\.gemini\antigravity\brain\0b2652f0-971e-4ff4-a22c-6da8ef1dc8b9\scratch\test_route.php"`)
Viewed RoleRequestController.php:1-34
Viewed RoleRequestReviewController.php:1-60
Viewed RoleRequestService.php:1-60
Viewed UserController.php:1-60
Viewed RoleController.php:1-60
Viewed RoleManagementService.php:1-60
Viewed RegulationController.php:1-60
Viewed RegulationService.php:1-60
Viewed CommunicationService.php:1-60
Viewed EventService.php:1-17
Ran command: Search EventService usage (`Get-ChildItem -Path app -Recurse -Filter "*.php" | Select-String -Pattern "EventService"`)
Viewed NotificationService.php:1-14
Ran command: Search NotificationService usage (`Get-ChildItem -Path app -Recurse -Filter "*.php" | Select-String -Pattern "NotificationService"`)
Viewed StatisticService.php:1-16
Ran command: Search StatisticService usage (`Get-ChildItem -Path app -Recurse -Filter "*.php" | Select-String -Pattern "StatisticService"`)
Viewed ApiResponseService.php:1-23
Viewed WorkspaceService.php:1-17
Viewed ManualAssessmentService.php:1-60
Viewed GradingService.php:1-11
Viewed ApprovalService.php:1-60
Viewed ArenaBelajarController.php:1-60
Viewed BungaRampaiController.php:1-60
Viewed NewsPublicController.php:1-15
Viewed EventPublicController.php:1-16
Viewed StatisticPublicController.php:1-15
Viewed SettingController.php:1-60
Viewed AuditController.php:1-60
Viewed ApprovalController.php:1-60
Ran command: List test files (`Get-ChildItem -Path tests -Recurse -File | Select-Object FullName`)
Ran command: Run phpunit (`vendor\bin\phpunit`)
Ran command: Check phpunit config (`Get-ChildItem -Filter "*phpunit*"`)
Viewed CompatibilityFoundationTest.php:1-29
Ran command: Check AdminAuthController.php (`Test-Path app\Controllers\Admin\AdminAuthController.php`)
Viewed composer.json:1-46
Viewed ArticleController.php:1-60
Viewed PreTestController.php:1-20
Viewed QuizController.php:1-30
Viewed GradingController.php:1-30
Viewed EnrollmentController.php:1-60
Viewed StudyController.php:1-29
Viewed OpdController.php:1-40
Viewed BookmarkController.php:1-60
Viewed PasswordController.php:1-60
Viewed PasswordResetService.php:1-60
Viewed AuthTokenModel.php:1-17
Ran command: Check password_resets schema (`Select-String -Path dokumen\schema-fresh-v4-1.sql -Pattern "CREATE TABLE IF NOT EXISTS .password_resets." -Context 0, 15`)
Viewed AccountStatusController.php:1-52
Viewed ProfileController.php:1-60

# LAPORAN AUDIT ARSITEKTUR & CODEBASE BEGAWIPBJ
**Framework:** CodeIgniter 4.7.4 (AppStarter) | **PHP:** ^8.2 | **Database Target:** `schema-fresh-v4-1.sql` (74 Tabel)  
**Status Audit:** Belum Production / Pra-Launching | **Analisis:** Senior Software Architect & Lead Developer

---

## RINGKASAN EKSEKUTIF

Berdasarkan audit statis terhadap 82 Controller, 51 Service, 90 Model, 608 baris routing (`Routes.php`), serta inspeksi runtime terhadap database lokal `db_begawipbj`, ditemukan kondisi sebagai berikut:

1. **Dual-Architecture State (Kondisi Transisi yang Menggantung):**  
   Codebase berada di tengah transisi antara arsitektur lama (*flat prototype/legacy*) dan arsitektur baru (*modular subdomain V4*). Akibatnya, terdapat puluhan file legacy (seperti `ClassModel`, `AssessmentModel`, `TemplateModel`, `AuthTokenModel`) yang masih merujuk ke tabel yang sudah **dihapus** di `schema-fresh-v4-1.sql`.
2. **Ghost Files & Stub 9-Byte:**  
   Ditemukan setidaknya **35 file PHP berukuran 9 byte** (`<?php\n\n`) yang terdaftar di sistem namun tidak memiliki deklarasi class sama sekali (contoh: `Workspace\OperatorController`, `Workspace\NotificationController`, seluruh `Services\Intelligence\*`, seluruh `Models\Intelligence\*`, dan seluruh `Models\Organization\*`). Membuka route yang memanggil class ini menyebabkan *Fatal Error / Class Not Found*.
3. **Critical Flow Blockers (P0):**  
   - **Login Redirect Trap:** Peserta (`peserta`/`public_user`) yang berhasil login langsung diredirect ke `/dashboard`. Karena filter `/dashboard/*` mewajibkan izin admin, peserta langsung dihantam HTTP 403 (*Forbidden*).
   - **Role Key Mismatch:** Kode memeriksa string `'super_admin'` dan `'admin'`, padahal data master tabel `roles` menggunakan key `'super_administrator'` dan `'administrator'`.
   - **Registrasi Akademisi Crash:** Kontroller registrasi mengirimkan `profile_type = 'academic_practitioner'`, yang ditolak oleh `RegistrationService` (ekspektasi `'academic'`) dan melanggar MySQL `CHECK` constraint `users_profile_type_chk`.
   - **Password Reset Fatal Error:** `PasswordResetService` masih memanggil `AuthTokenModel` (tabel `auth_tokens`), padahal tabel di skema V4 adalah `password_resets`.
4. **Modul Unggulan yang Siap:**  
   Modul **Regulasi PBJ** (`Admin\RegulationController`, `RegulationService`), **Sertifikat Digital** (`CertificateController`, `CertificateGenerator`, `CertificatePdfGenerator`), dan **Workspace Trainer** (`Workspace\TrainerController`) telah diimplementasikan dengan sangat rapi dan matang mengikuti kaidah V4.

---

## 1. PROJECT MAP

Berikut adalah pemetaan aktual seluruh modul yang benar-benar ada di codebase project:

| Modul | Controller Terkait | Service Terkait | Model Terkait | Tabel Database Terkait |
|---|---|---|---|---|
| **1. Auth, Identity & RBAC** | `Dashboard\Auth\LoginController`<br>`Dashboard\Auth\RegisterController`<br>`Dashboard\Auth\PasswordController`<br>`Dashboard\Auth\AccountStatusController`<br>`Admin\UserController`<br>`Admin\RoleController`<br>`Admin\PermissionController`<br>`Admin\UserProfileVerificationController`<br>`Admin\RoleRequestReviewController`<br>`Workspace\ProfileController`<br>`Workspace\RoleRequestController` | `Identity\AuthenticationService`<br>`Identity\RegistrationService`<br>`Identity\PasswordResetService`<br>`Identity\AuthorizationService`<br>`Admin\UserManagementService`<br>`Admin\RoleManagementService`<br>`Admin\ProfileVerificationService`<br>`Admin\ProfileChangeRequestService`<br>`Admin\RoleRequestService` | `UserModel`<br>`UserProfileModel`<br>`UserRoleModel`<br>`RoleModel`<br>`PermissionModel`<br>`RolePermissionModel`<br>`ProfileVerificationModel`<br>`ProfileChangeRequestModel`<br>`RoleRequestModel`<br>`UserAcademicProfileModel`<br>`UserAsnProfileModel`<br>`UserProviderProfileModel`<br>`UserPublicProfileModel`<br>`WilayahModel`<br>*(Legacy: `AuthTokenModel`)* | `users`<br>`user_profiles`<br>`user_roles`<br>`roles`<br>`permissions`<br>`role_permissions`<br>`profile_verifications`<br>`profile_change_requests`<br>`role_requests`<br>`academic_profiles`<br>`asn_profiles`<br>`provider_profiles`<br>`public_profiles`<br>`password_resets`<br>`email_verifications`<br>`wilayah` |
| **2. Kelembagaan & Peran Pengadaan** | `Admin\OrganizationController`<br>`Admin\ProcurementRoleController` | `Admin\OrganizationService`<br>`Admin\ProcurementRoleService`<br>*(Stub 9-byte: `Services\Organization\*`)*<br>*(Legacy: `OrganizationService` root)* | `OrganizationUnitModel`<br>`ProcurementRoleModel`<br>`UserProcurementRoleModel`<br>*(Stub 9-byte: `Models\Organization\*`)* | `organization_units`<br>`procurement_roles`<br>`user_procurement_roles` |
| **3. Arena Belajar (LMS)** | `Learning\ProgramController`<br>`Learning\CourseController`<br>`Learning\LearningClassController`<br>`Learning\ModuleController`<br>`Learning\LessonController`<br>`Learning\EnrollmentController`<br>`Learning\QuizController`<br>`Learning\PreTestController`<br>`Learning\PostTestController`<br>`Learning\GradingController`<br>`Learning\StudyController`<br>`Workspace\ParticipantController`<br>`Workspace\TrainerController`<br>`Workspace\EvaluatorController`<br>`Landing\ArenaBelajarController`<br>*(Legacy: `Learning\ClassController`, `Learning\AssessmentController`, `Learning\ManualAssessmentController`, `Workspace\PesertaController`)* | `LearningVerticalSliceService`<br>*(Legacy: `LearningService`, `QuizService`, `ProgressService`, `ManualAssessmentService`, `GradingService`)* | `Learning\ProgramModel`<br>`Learning\CourseModel`<br>`Learning\LearningClassModel`<br>`Learning\LearningModuleModel`<br>`Learning\LessonModel`<br>`Learning\QuizModel`<br>`Learning\QuestionModel`<br>`Learning\QuestionOptionModel`<br>`Learning\QuizAttemptModel`<br>`Learning\QuizAnswerModel`<br>`Learning\ClassTrainerModel`<br>`Learning\LearningProgressModel`<br>`Learning\EnrollmentModel`<br>*(Legacy: `ClassModel`, `AssessmentModel`, `AssessmentResultModel`, `QuestionModel`, `AnswerModel`, `ProgressModel`, `ModuleModel`, `CourseModel`)* | `programs`<br>`courses`<br>`learning_classes`<br>`learning_modules`<br>`lessons`<br>`quizzes`<br>`quiz_questions`<br>`quiz_options`<br>`quiz_attempts`<br>`quiz_answers`<br>`pre_tests`<br>`assignments`<br>`assignment_submissions`<br>`assignment_submission_files`<br>`class_trainers`<br>`class_evaluators`<br>`enrollments`<br>`learning_progress`<br>`feedbacks` |
| **4. Bunga Rampai (KMS)** | `Knowledge\ArticleController`<br>`Knowledge\FaqController`<br>`Knowledge\CaseStudyController`<br>`Knowledge\DocumentTemplateController`<br>`Knowledge\KnowledgeMediaController`<br>`Knowledge\KnowledgeMediaItemController`<br>`Knowledge\KnowledgeCarouselController`<br>`Knowledge\CategoryController`<br>`Knowledge\TagController`<br>`Landing\BungaRampaiController`<br>*(Legacy: `Knowledge\TemplateController`, `Knowledge\BookmarkController`)* | Direct Query via Models & `AuditService` / `FileStorageService` | `Knowledge\ArticleModel`<br>`Knowledge\KnowledgeCategoryModel`<br>`Knowledge\TagModel`<br>`Knowledge\ArticleTagModel`<br>`Knowledge\CaseStudyModel`<br>`Knowledge\DocumentTemplateModel`<br>`Knowledge\FaqModel`<br>`Knowledge\KnowledgeMediaItemModel`<br>*(Legacy: `ArticleModel`, `CategoryModel`, `TemplateModel`, `CaseStudyModel`, `FaqModel`)* | `knowledge_articles`<br>`knowledge_categories`<br>`knowledge_tags`<br>`knowledge_article_categories`<br>`knowledge_article_tags`<br>`knowledge_media` |
| **5. Regulasi PBJ** | `Admin\RegulationController`<br>`Landing\RegulationPublicController`<br>*(Legacy: `Regulation\RegulationController`)* | `Regulation\RegulationService`<br>`Regulation\RegulationPublicService` | `Regulation\RegulationModel`<br>`Regulation\RegulationCategoryModel`<br>`Regulation\RegulationVersionModel`<br>`Regulation\RegulationRelationModel`<br>`Regulation\ProcurementStageModel`<br>`Regulation\CompetencyModel`<br>`Regulation\RegulationProcurementStageModel`<br>`Regulation\RegulationCompetencyModel`<br>`Regulation\RegulationDownloadLogModel`<br>*(Legacy duplicate: `RegulationModel`, `RegulationCategoryModel`, `RegulationVersionModel`)* | `regulations`<br>`regulation_categories`<br>`regulation_versions`<br>`regulation_relations`<br>`procurement_stages`<br>`regulation_procurement_stages`<br>`competencies`<br>`regulation_competencies`<br>`regulation_downloads` |
| **6. Komunikasi & Agenda** | `Communication\NewsController`<br>`Communication\EventController`<br>`Communication\EventRegistrationController`<br>`Communication\AttendanceController`<br>`Landing\NewsPublicController`<br>`Landing\EventPublicController` | `Communication\CommunicationService`<br>*(Legacy: `EventService`)* | `Communication\NewsModel`<br>`Communication\NewsCategoryModel`<br>`Communication\EventModel`<br>`Communication\EventRegistrationModel`<br>`Communication\EventAttendanceModel`<br>*(Legacy duplicate: `NewsModel`)* | `news`<br>`news_categories`<br>`events`<br>`event_registrations`<br>`attendances` |
| **7. Sertifikat Digital** | `Certificate\CertificateController`<br>`Certificate\CertificateApprovalController`<br>`Certificate\CertificateEventController`<br>`Certificate\CertificateTemplateController`<br>`Certificate\CertificateDownloadController`<br>`Landing\CertificateVerificationController` | `CertificateEligibilityService`<br>`CertificateGenerator`<br>`CertificateTemplateRenderer`<br>`CertificatePdfGenerator`<br>`CertificateQrGenerator` | `Certificate\CertificateModel`<br>`Certificate\CertificateTemplateModel`<br>`Certificate\CertificateSequenceModel`<br>*(Stub 9-byte: `Certificate\CertificateVerificationLogModel`)*<br>*(Legacy: `CertificateVerificationModel`)* | `certificates`<br>`certificate_templates`<br>`certificate_sequences` |
| **8. Government Intelligence PBJ** | `Intelligence\IntelligenceDashboardController`<br>`Intelligence\OpdController`<br>`Intelligence\JfPpbjController`<br>`Intelligence\CompetencyCertificateController`<br>`Landing\StatisticPublicController` | *(Stub 9-byte: seluruh `Services\Intelligence\*`)*<br>*(Legacy/Mock: `StatisticService`, `PPBJService`)* | *(Stub 9-byte: seluruh `Models\Intelligence\*`)* | `jf_ppbj_levels`<br>`jf_ppbj_records`<br>`competency_certificates`<br>`competency_certificate_types`<br>`competency_gap_snapshots` |
| **9. Workspace Interaktif** | `Workspace\WorkspaceHomeController`<br>`Workspace\ParticipantController`<br>`Workspace\TrainerController`<br>`Workspace\BookmarkController`<br>`Workspace\ProfileController`<br>`Workspace\RoleRequestController`<br>*(Stub 9-byte: `Workspace\OperatorController`, `Workspace\NotificationController`)*<br>*(Broken/Stub: `Workspace\EvaluatorController`)* | `LearningVerticalSliceService`<br>*(Legacy: `WorkspaceService`)* | `Knowledge\BookmarkModel`<br>*(Stub 9-byte: `NotificationModel`)* | `bookmarks`<br>`notifications`<br>`enrollments`<br>`learning_progress`<br>`quiz_attempts`<br>`user_profiles` |
| **10. Sistem, Audit & File Storage** | `Admin\DashboardController`<br>`Admin\ApprovalController`<br>`Admin\AuditController`<br>`Admin\SettingController`<br>`BookmarkController` (root)<br>`Landing\FileController`<br>`Landing\HomeController`<br>*(Stub: `ExportController`)* | `AuditService`<br>`FileStorageService`<br>`Workflow\ApprovalService`<br>*(Legacy: `DashboardService`)* | `AuditLogModel`<br>`WorkflowTransitionModel`<br>`ApprovalModel`<br>*(Stub 9-byte: `FileAssetModel`)*<br>*(Legacy: `ApprovalHistoryModel`)* | `audit_logs`<br>`workflow_transitions`<br>`approvals`<br>`file_assets`<br>`exports` |

---

## 2. FEATURE STATUS

Audit status fitur berdasarkan implementasi kode aktual (bukan sekadar keberadaan file UI/route):

```
Kategori Status:
[Selesai]            : Logic controller, service, model, database, dan view sinkron & fungsional
[Sebagian Selesai]   : Fitur utama berjalan, namun ada flow terputus, validasi bolong, atau aksi POST belum ada
[Hanya Skeleton]     : Controller/view ada namun hanya merender mock UI statis tanpa interaksi DB nyata
[Belum Ada / Stub]   : File kosong 9 byte, method hilang di controller, atau tabel DB belum dijamah
```

### 1. Auth, Identity & RBAC
- **Login:** `Sebagian Selesai` — Autentikasi hash & audit log berjalan, namun redirect memblokir peserta non-admin (403 trap) dan role key `super_administrator` tidak cocok.
- **Registrasi Multi-Step:** `Sebagian Selesai` — Alur 4 langkah dan validasi wilayah hierarkis berjalan, namun submission gagal saat memilih akademisi dan field mapping array tidak sinkron.
- **Verifikasi Profil Pengguna:** `Selesai` — Antrean verifikasi, detail user hierarkis, approve/reject/revision dengan audit log & workflow transition berjalan penuh.
- **Permohonan Perubahan Profil (Admin):** `Sebagian Selesai` — Tampilan antrean ada, namun handler aksi approve/reject belum ada logic persistence.
- **Manajemen User (Admin):** `Selesai` — CRUD user, filter pencarian, aktivasi/suspend, dan trigger reset password berjalan.
- **Manajemen Role & Permission:** `Selesai` — CRUD role/permission dan sinkronisasi matriks permission aktif.
- **Permohonan Role (Admin Review):** `Selesai` — Antrean, review permohonan, persetujuan dan pemberian role atomik berjalan di `RoleRequestService`.
- **Permohonan Role (User Workspace):** `Hanya Skeleton` — `RoleRequestController` hanya placeholder view, tidak ada form submit.
- **Lupa & Reset Kata Sandi:** `Sebagian Selesai` — Alur UI lengkap, namun service memanggil tabel `auth_tokens` yang tidak ada (SQL error).
- **Status Akun:** `Sebagian Selesai` — View pending/revision/rejected ada, namun unauthenticated guard menyebabkan redirect loop.

### 2. Kelembagaan & Peran Pengadaan
- **Unit Organisasi (OPD):** `Selesai` — Tree view hierarkis unit kerja, form CRUD, aktivasi/nonaktivasi berjalan via `Admin\OrganizationService`.
- **Peran Pengadaan (Procurement Roles):** `Selesai` — CRUD peran pengadaan, penugasan SK ke personel, dan verifikasi peran aktif via `Admin\ProcurementRoleService`.

### 3. Arena Belajar (LMS)
- **Program & Course (Admin):** `Selesai` — CRUD program, course, dan detail materi berjalan via `Learning\ProgramModel` dan `CourseModel`.
- **Kelas Pelatihan (Admin):** `Selesai` — CRUD kelas, assign pengajar (`class_trainers`), revoke pengajar via `LearningClassController`.
- **Modul & Lesson (Admin):** `Selesai` — Pengelolaan modul dan lesson per kelas berjalan via `ModuleController` dan `LessonController`.
- **Pendaftaran / Enrollment (Admin):** `Selesai` — List enrollment peserta, detail, approve/reject aktivasi via `LearningVerticalSliceService`.
- **Assessment / Bank Soal (Admin):** `Hanya Skeleton` — `QuizController`, `PreTestController`, `PostTestController` hanya mengembalikan view statis tanpa CRUD.
- **Penilaian & Progres (Admin):** `Hanya Skeleton` — `GradingController` dan `StudyController` hanya merender template HTML statis.
- **Workspace Peserta (Belajar):** `Selesai` — Alur belajar (`ParticipantController`), tracking progres per lesson, pre-test, quiz attempt, submit quiz, dan my-classes berjalan via `LearningVerticalSliceService`.
- **Workspace Trainer (Pengajar):** `Selesai` — Alur manajemen kelas pengajar, modul, lesson, pembuatan kuis dan bank soal (`TrainerController`) berjalan penuh dengan view dedicated.
- **Workspace Evaluator:** `Belum Ada / Stub` — `EvaluatorController` hanya memiliki 1 method `index()`; route untuk submissions, penilaian, dan feedback belum ada implementasinya (akan throw Exception).
- **Katalog Publik Arena Belajar:** `Selesai` — List program, courses, kelas, detail trainer di landing page terkoneksi database.

### 4. Bunga Rampai (KMS)
- **Artikel, Studi Kasus, FAQ, Template (Admin):** `Selesai` — CRUD lengkap, filtering status, tagging, kategorisasi, dan slug generator menggunakan tabel terpadu `knowledge_articles`.
- **Media Galeri & Items (Admin):** `Selesai` — Manajemen asset media dan item galeri berjalan via `KnowledgeMediaController`.
- **Carousel Konten Unggulan:** `Selesai` — Pengaturan spotlight carousel aktif.
- **Kategori & Tag KMS:** `Selesai` — CRUD kategori hierarkis dan tag artikel berjalan.
- **Bunga Rampai Publik:** `Selesai` — Halaman landing, katalog artikel, FAQ, studi kasus, template download, dan galeri media terkoneksi database.

### 5. Regulasi PBJ
- **Dokumen Regulasi (Admin):** `Selesai` — CRUD regulasi, upload versi dokumen via `file_assets`, workflow transition lengkap (draft, review, approve, publish, archive).
- **Kategori, Relasi, Tahapan PBJ, Kompetensi (Admin):** `Selesai` — Seluruh relasi N:M (`regulation_relations`, `regulation_procurement_stages`, `regulation_competencies`) berjalan penuh.
- **Download Log:** `Selesai` — Pencatatan log unduhan publik terekam di database.
- **Regulasi Publik:** `Selesai` — Pencarian regulasi, filter kategori, preview dokumen, unduh PDF, riwayat versi, dan relasi regulasi terkoneksi database.

### 6. Komunikasi & Agenda
- **Berita & Kategori Berita (Admin):** `Selesai` — CRUD berita, cover upload, scheduling, publish/unpublish via `CommunicationService`.
- **Event & Agenda (Admin):** `Selesai` — CRUD event, registrasi peserta, dan pencatatan kehadiran via `CommunicationService`.
- **Berita & Event Publik:** `Hanya Skeleton` — `NewsPublicController` dan `EventPublicController` hanya memparsing slug ke judul dan merender mockup statis tanpa query database.

### 7. Sertifikat Digital
- **Daftar Sertifikat (Admin):** `Selesai` — Listing, detail, audit log, soft-revoke berjalan.
- **Penerbitan Sertifikat (Admin Approval):** `Selesai` — Transaksi atomik penerbitan sertifikat dari kelulusan kelas via `CertificateGenerator` & sequence atomik (`SERT/{YEAR}/{NNNNN}`).
- **Template Sertifikat (Admin):** `Selesai` — CRUD template sertifikat, aktivasi/deaktivasi template berjalan.
- **Download Sertifikat PDF:** `Selesai` — Stream langsung PDF privat via Dompdf (`CertificatePdfGenerator`) dengan guard kepemilikan user.
- **Verifikasi Publik Sertifikat:** `Selesai` — Lookup token QR via SHA-256 hash di `Landing\CertificateVerificationController`.

### 8. Government Intelligence PBJ
- **Dashboard Intelligence:** `Hanya Skeleton` — View merender chart ApexCharts dengan data dummy hardcoded ("Data dummy untuk pratinjau UI internal").
- **SDM JF-PPBJ, OPD, Gap Kompetensi:** `Belum Ada / Stub` — Seluruh service dan model di subdomain `Intelligence` berukuran 9 byte (kosong).

### 9. Workspace Pengguna
- **Workspace Home:** `Selesai` — Redirect dashboard personal berdasarkan role.
- **Bookmark User:** `Selesai` — Bookmark polimorfik (pembelajaran, KMS, regulasi) dan aksi hapus bookmark berjalan.
- **Profil User (Workspace):** `Sebagian Selesai` — Form data user tampil, namun aksi submit form mengarah ke route non-existent `profile.update`.
- **Operator Workspace:** `Belum Ada / Stub` — File `OperatorController.php` berukuran 9 byte (kosong).
- **Notifikasi Workspace:** `Belum Ada / Stub` — File `NotificationController.php` berukuran 9 byte (kosong).

### 10. Sistem & Cross-Cutting
- **Audit Trail:** `Selesai` — Browser log audit komprehensif dengan filter aktor, modul, tanggal, dan hashing IP/User-Agent.
- **Approval Center:** `Hanya Skeleton` — Controller hanya menampilkan view HTML tanpa memproses mutasi persetujuan.
- **Pengaturan Sistem (Settings):** `Hanya Skeleton` — Halaman setting 10 tab hanya memiliki method GET, tidak ada method POST untuk menyimpan konfigurasi.
- **Export Data:** `Belum Ada / Stub` — File `ExportController.php` berukuran 9 byte (kosong).

---

## 3. BUSINESS FLOW

Berikut adalah pemetaan alur lintas layer (Controller → Service → Model → Database) untuk fitur-fitur utama:

---

### Feature: Autentikasi Pengguna (Login)

```
Flow:
Dashboard\Auth\LoginController::attemptLogin()
↓
Identity\AuthenticationService::authenticate($email, $password)
↓
UserModel::findByEmail() & getRoles() & getPermissions()
↓
Database (`users`, `user_roles`, `roles`, `role_permissions`, `audit_logs`)
```

- **Status:** `Sebagian Selesai`
- **Missing / Broken:**
  1. Cek role admin di controller: `in_array('super_admin', $roles)` & `'admin'`, padahal di DB tersimpan `'super_administrator'` dan `'administrator'`.
  2. Peserta biasa yang login langsung diredirect ke `site_url('dashboard')` yang kemudian diblokir oleh `AuthenticationFilter` dengan HTTP 403.
  3. `last_login_at` di-update tapi `session_version` tidak dicek.

---

### Feature: Pendaftaran Mandiri Multi-Step (Self-Registration)

```
Flow:
Dashboard\Auth\RegisterController::registerReviewAttempt()
↓
Identity\RegistrationService::register($payload)
↓
Raw Transaction DB Table Inserts
↓
Database (`users` → `user_profiles` → `{type}_profiles` → `profile_verifications` → `audit_logs`)
```

- **Status:** `Sebagian Selesai`
- **Missing / Broken:**
  1. Registrasi Akademisi mengirim `profile_type = 'academic_practitioner'`, sedangkan Service dan DB CHECK Constraint hanya menerima `'academic'`.
  2. Input form profile ASN (`nip_nippk`, `position`, `asn_origin`) tidak ter-mapping sempurna dengan `FIELD_MAP` di `RegistrationService`, menyebabkan exception `Field wajib belum diisi`.
  3. Transaksi registrasi **tidak membuat entri di tabel `user_roles`**, sehingga user baru tidak memiliki role awal sama sekali (bahkan bukan `peserta` atau `public_user`).

---

### Feature: Verifikasi Akun Pengguna Baru

```
Flow:
Admin\UserProfileVerificationController::approve($id)
↓
Admin\ProfileVerificationService::approve($verificationId, $actorId)
↓
Admin\UserManagementService::updateStatus($userId, 'active')
↓
Database (`profile_verifications`, `users`, `workflow_transitions`, `audit_logs`)
```

- **Status:** `Selesai (Perlu Penyesuaian Role)`
- **Missing / Broken:**
  1. Saat disetujui, `account_status` berubah menjadi `active`, namun service tidak memberikan role default `peserta` ke `user_roles`. User aktif tanpa role tidak dapat mengakses fitur spesifik.

---

### Feature: Alur Belajar Peserta (LMS Study Flow)

```
Flow:
Workspace\ParticipantController (study, completeLesson, submitQuiz)
↓
LearningVerticalSliceService (activateEnrollment, submitQuizAttempt)
↓
Direct Query & Models (`Learning\QuizAttemptModel`, `Learning\QuizAnswerModel`)
↓
Database (`enrollments`, `learning_progress`, `quiz_attempts`, `quiz_answers`, `audit_logs`)
```

- **Status:** `Selesai`
- **Missing / Broken:**
  1. Menggunakan temporary service `LearningVerticalSliceService` yang mem-bypass `QuizService` dan `ProgressService` lama.
  2. Fitur upload tugas mandiri (`assignments`) belum tersambung ke `assignment_submissions`.

---

### Feature: Pembuatan & Penerbitan Sertifikat Kelulusan

```
Flow:
Certificate\CertificateApprovalController::issue($id)
↓
CertificateGenerator::generateFromApproval($approvalId)
↓
CertificateModel::insert() & CertificateSequenceModel::nextNumber()
↓
Database (`certificates`, `certificate_sequences`, `approvals`, `workflow_transitions`, `file_assets`)
```

- **Status:** `Selesai`
- **Missing / Broken:**
  1. Trigger kelulusan otomatis dari `ParticipantController` ke tabel `approvals` sertifikat belum tersambung secara otomatis (masih manual approval).

---

### Feature: Manajemen Regulasi PBJ (Lifecycle & Publikasi)

```
Flow:
Admin\RegulationController (store, addVersion, approve, publish)
↓
Regulation\RegulationService (create, writeTransition, fileStorage)
↓
Regulation\RegulationModel & RegulationVersionModel
↓
Database (`regulations`, `regulation_versions`, `regulation_relations`, `file_assets`, `workflow_transitions`, `audit_logs`)
```

- **Status:** `Selesai`
- **Missing / Broken:**
  - Tidak ada issue fatal. Modul ini menjadi *gold standard* implementasi di project ini.

---

### Feature: Evaluasi Tugas & Grading (Evaluator Workspace)

```
Flow:
Workspace\EvaluatorController
↓
[Missing Service] (hanya ada stub GradingService & ManualAssessmentService legacy)
↓
[Missing Model] (hanya ada stub 9-byte AssignmentGradeModel)
↓
Database (`assignments`, `assignment_submissions`, `class_evaluators`)
```

- **Status:** `Belum Ada Implementasi`
- **Missing / Broken:**
  - Route sudah terdaftar di `Routes.php` baris 170-178, namun Controller `EvaluatorController` belum memiliki method `assignments`, `submissions`, `grading`, dsb.

---

### Feature: Government Intelligence PBJ

```
Flow:
Intelligence\IntelligenceDashboardController
↓
[Missing Service] (seluruh file di Services/Intelligence berukuran 9 byte)
↓
[Missing Model] (seluruh file di Models/Intelligence berukuran 9 byte)
↓
Database (`jf_ppbj_records`, `competency_certificates`, `competency_gap_snapshots`)
```

- **Status:** `Hanya Skeleton`
- **Missing / Broken:**
  - Seluruh backend layer belum dibuat; view hanya menampilkan visual chart statis.

---

## 4. ARCHITECTURE REVIEW

### 4.1. Apakah Controller Terlalu Gemuk?
- **Sebagian Terlalu Gemuk (Fat Controller):**  
  - `Admin\RegulationController.php` (908 baris): Menangani 7 sub-menu sekaligus (regulasi, versi, kategori, relasi, tahapan, kompetensi, log). Sebaiknya dipecah menjadi beberapa controller terfokus (misal: `RegulationDocumentController`, `RegulationCategoryController`, dsb.).
  - `Workspace\TrainerController.php` (675 baris): Menangani kelas, modul, lesson, kuis, soal kuis, dan opsi jawaban secara monolitik.
  - `Workspace\ParticipantController.php` (686 baris): Menggabungkan pengerjaan lesson, kuis, pre-test, post-test, feedback, dan sertifikat dalam satu controller.
- **Sebagian Terlalu Kurus / Dummy:**  
  - Controller seperti `Admin\SettingController`, `Admin\ApprovalController`, `Learning\GradingController`, dan `Learning\StudyController` hanya mengembalikan `view(...)` tanpa data atau logic, bahkan tanpa method POST.

### 4.2. Apakah Service Layer Sudah Konsisten?
- **Sangat Tidak Konsisten:**
  - **Namespace Berantakan:** Sebagian service berada di root `App\Services` (seperti `AuditService`, `FileStorageService`), sebagian di subdomain yang rapi (seperti `App\Services\Identity`, `App\Services\Regulation`, `App\Services\Admin`), dan ada direktori seperti `App\Services\Organization\` yang berisi 4 file kosong 9-byte.
  - **Duplikasi Service:** Terdapat 3 `OrganizationService`:
    1. `App\Services\OrganizationService.php` (root, CRUD flat)
    2. `App\Services\Admin\OrganizationService.php` (aktif dipakai di admin)
    3. `App\Services\Organization\OrganizationService.php` (kosong 9-byte)
  - **Toy Services:** Terdapat file service berukuran 10–20 baris dengan fungsi sepele yang tidak pernah dipanggil, seperti `SearchService` (`strtolower(trim())`), `StatisticService` (`($a/$b)*100`), `GradingService`, `PPBJService`, dan `EventService`.
  - **Service Bypass:** Banyak controller (termasuk `Admin\DashboardController` dan `Landing\BungaRampaiController`) mem-bypass Service layer dan langsung melakukan query DB (`db_connect()->table(...)`) di dalam controller.

### 4.3. Apakah Model Bertanggung Jawab Sesuai Fungsi (Single Responsibility Principle)?
- **Pelanggaran SRP dan Dualisme Model:**
  - Terdapat model flat di root `App\Models` yang menduplikasi model di subdomain:
    - `App\Models\CourseModel.php` vs `App\Models\Learning\CourseModel.php`
    - `App\Models\NewsModel.php` vs `App\Models\Communication\NewsModel.php`
    - `App\Models\RegulationModel.php` vs `App\Models\Regulation\RegulationModel.php`
  - Model root lama masih merujuk ke tabel yang **tidak ada** di skema database target (`schema-fresh-v4-1.sql`), seperti `assessments`, `questions`, `classes`, `modules`, `templates`, `auth_tokens`.
  - Tidak ada satupun **Entity** class di `app/Entities`. Semua model mengembalikan raw PHP `array`, menyebabkan type safety rendah dan data casting dilakukan berulang-ulang di controller.

### 4.4. Apakah Ada Duplikasi Logic?
- **Redirection Logic Duplication:** Logika pemetaan redirect berdasarkan role (`trainer`, `operator`, `evaluator`, `admin`) ditulis berulang di `LoginController`, `WorkspaceHomeController`, dan `WorkspaceService`.
- **Wilayah Hierarchy Validation:** Logika validasi kode wilayah berjenjang ditulis manual di `RegisterController::isValidWilayahChain` dan ditulis ulang di `RegistrationService::assertWilayahChain`.
- **File Storage Logic:** Logika pemindahan upload file ditulis di `FileStorageService`, namun ditulis ulang secara privat di `CommunicationService::storeFile` dan `Landing\BungaRampaiController`.

### 4.5. Apakah Ada Code Legacy?
- **Sangat Banyak:**
  - `LandingController.php` (mockup HTML lama dari template FiStudy).
  - `Home.php` (default template welcome CodeIgniter).
  - `Learning\ClassController.php` (merujuk ke kolom `roles.slug` dan tabel `classes` lama).
  - `Learning\AssessmentController.php` & `Learning\ManualAssessmentController.php`.
  - `Regulation\RegulationController.php` (controller lama sebelum dipindah ke `Admin\RegulationController`).
  - `Knowledge\TemplateController.php` & `Knowledge\BookmarkController.php`.
  - `Workspace\PesertaController.php`.
  - `Services\DashboardService.php` (query ke tabel `assessment_results` & kolom `user_type` yang tidak ada).

### 4.6. Apakah Ada Modul dengan Pola Berbeda?
- **Pola Arsitektur Terpecah Dua:**
  1. **Pola Modern V4 (Domain-Driven):** Digunakan pada `Admin\RegulationController`, `Certificate\CertificateController`, dan `Admin\UserProfileVerificationController`. Pola ini mematuhi Service Layer, mencatat `audit_logs`, mencatat `workflow_transitions`, menggunakan relasi via Model domain, dan validasi server-side ketat.
  2. **Pola Raw Prototype:** Digunakan pada `Intelligence\*`, `Admin\SettingController`, `Admin\ApprovalController`, dan `Landing\NewsPublicController`. Hanya merender view mock tanpa persistence dan tanpa service layer.

---

## 5. REFACTOR PLAN

Berdasarkan temuan di atas, rencana refactor disusun berdasarkan prioritas tegas:

```
P0 (Showstoppers)    : Harus diperbaiki SEBELUM development fitur berlanjut
P1 (Architectural)   : Konsolidasi arsitektur, eliminasi duplikasi & penataan layer
P2 (Polishing)       : UI/UX refinement, visual state, optimasi & dokumentasi
```

---

### PRIORITAS P0: Harus Diperbaiki Sebelum Development Lanjut

#### 1. Perbaikan Login Flow & Redirect Trap (P0-01)
- **Lokasi File:**  
  [app/Controllers/Dashboard/Auth/LoginController.php](file:///c:/laragon/www/begawipengadaan/app/Controllers/Dashboard/Auth/LoginController.php)  
  [app/Filters/AuthenticationFilter.php](file:///c:/laragon/www/begawipengadaan/app/Filters/AuthenticationFilter.php)
- **Masalah:**  
  1. Pemeriksaan role admin menggunakan `'super_admin'` dan `'admin'`, sedangkan tabel `roles` berisi `'super_administrator'` dan `'administrator'`.
  2. Pengguna selain trainer/operator/evaluator (misal: peserta, ASN, umum) di-redirect ke `/dashboard`, yang langsung memicu HTTP 403 dari `AuthenticationFilter`.
- **Dampak:** Pengguna non-admin tidak dapat masuk ke sistem; admin tidak terdeteksi dengan benar.
- **Solusi Disarankan:**
  - Ubah pengecekan role di `LoginController`:
    ```php
    $isAdmin = in_array('super_administrator', $loggedRoles, true) || in_array('administrator', $loggedRoles, true);
    if ($isAdmin) return redirect()->to(site_url('dashboard'));
    if (in_array('trainer', $loggedRoles, true)) return redirect()->to(route_to('workspace.trainer.home'));
    if (in_array('operator', $loggedRoles, true)) return redirect()->to(route_to('workspace.operator.home'));
    if (in_array('evaluator', $loggedRoles, true)) return redirect()->to(route_to('workspace.evaluator.home'));
    return redirect()->to(route_to('workspace.participant.home')); // Default fallback untuk peserta/user umum
    ```

#### 2. Perbaikan Registrasi Akademisi & Pemetaan Field (P0-02)
- **Lokasi File:**  
  [app/Controllers/Dashboard/Auth/RegisterController.php](file:///c:/laragon/www/begawipengadaan/app/Controllers/Dashboard/Auth/RegisterController.php)  
  [app/Services/Identity/RegistrationService.php](file:///c:/laragon/www/begawipengadaan/app/Services/Identity/RegistrationService.php)
- **Masalah:**  
  1. `RegisterController` menggunakan kunci `'academic_practitioner'`, sedangkan skema database (`users_profile_type_chk`) dan `RegistrationService` mewajibkan string `'academic'`.
  2. Input field pada step ASN mengirimkan nama kolom database langsung (`nip_nippk`, `asn_origin`), namun `RegistrationService::mapProfile` mencari form key alias (`employee_identifier`, `origin_type`), sehingga validasi required field gagal.
  3. Transaksi registrasi tidak meng-insert role default ke `user_roles`.
- **Dampak:** Pendaftaran gagal total untuk tipe profil akademisi dan ASN.
- **Solusi Disarankan:**
  - Standarisasi tipe profil menjadi `'academic'` pada `PROFILE_REGISTRY` di `RegisterController`.
  - Selaraskan `FIELD_MAP` di `RegistrationService` agar mendukung input langsung atau form-alias secara aman.
  - Tambahkan insert default `user_roles` dengan role `peserta` (atau `public_user`) saat registrasi berhasil.

#### 3. Perbaikan Password Reset Service & Model (P0-03)
- **Lokasi File:**  
  [app/Services/Identity/PasswordResetService.php](file:///c:/laragon/www/begawipengadaan/app/Services/Identity/PasswordResetService.php)  
  [app/Models/AuthTokenModel.php](file:///c:/laragon/www/begawipengadaan/app/Models/AuthTokenModel.php)
- **Masalah:**  
  `PasswordResetService` menginstansiasi `AuthTokenModel` yang merujuk ke tabel `auth_tokens`. Tabel ini tidak ada di database; tabel resmi di `schema-fresh-v4-1.sql` adalah `password_resets`.
- **Dampak:** Fitur "Lupa Kata Sandi" throw SQL Exception fatal saat dijalankan.
- **Solusi Disarankan:**
  - Buat model baru `App\Models\PasswordResetModel` yang merujuk ke tabel `password_resets` (`id`, `user_id`, `token_hash`, `expires_at`, `used_at`, `created_at`).
  - Update `PasswordResetService` untuk menggunakan model baru tersebut.
  - Hapus `AuthTokenModel.php`.

#### 4. Pembersihan Crash Routes & Ghost Controllers (P0-04)
- **Lokasi File:**  
  [app/Controllers/Workspace/OperatorController.php](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/OperatorController.php)  
  [app/Controllers/Workspace/NotificationController.php](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/NotificationController.php)  
  [app/Controllers/Workspace/EvaluatorController.php](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/EvaluatorController.php)  
  [app/Controllers/ExportController.php](file:///c:/laragon/www/begawipengadaan/app/Controllers/ExportController.php)  
  [app/Controllers/Admin/AuditController.php](file:///c:/laragon/www/begawipengadaan/app/Controllers/Admin/AuditController.php)
- **Masalah:**  
  1. `OperatorController.php` dan `NotificationController.php` kosong 9-byte tanpa deklarasi class.
  2. `EvaluatorController.php` tidak memiliki method `assignments`, `submissions`, `submissionDetail`, `grading`, `feedback`.
  3. `AuditController.php` tidak memiliki method `files` dan `notifications` yang dipanggil di route `dashboard/sistem/file-media` dan `dashboard/sistem/notifikasi`.
  4. `ExportController.php` kosong 9-byte.
- **Dampak:** Membuka route-route tersebut langsung memicu unhandled 500 error / fatal exception.
- **Solusi Disarankan:**
  - Buat deklarasi class dasar dan placeholder response yang aman pada controller tersebut, atau tutup route sementara di `Routes.php` sampai implementasi siap.

#### 5. Perbaikan Action Form Profile Workspace (P0-05)
- **Lokasi File:**  
  [app/Views/landing/user/profile.php](file:///c:/laragon/www/begawipengadaan/app/Views/landing/user/profile.php)  
  [app/Config/Routes.php](file:///c:/laragon/www/begawipengadaan/app/Config/Routes.php)  
  [app/Controllers/Workspace/ProfileController.php](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/ProfileController.php)
- **Masalah:**  
  Form profil mengarah ke `route_to('profile.update')` yang tidak terdaftar, dan grup route `/workspace/profil` tidak memiliki method POST.
- **Dampak:** Profil pengguna tidak dapat diperbarui sama sekali.
- **Solusi Disarankan:**
  - Tambahkan route `POST workspace/profil/ubah` dengan nama `workspace.profile.update` di `Routes.php`.
  - Tambahkan method `update()` di `Workspace\ProfileController` yang mengupdate data akun dan profil via `UserModel` & `UserProfileModel`.

---

### PRIORITAS P1: Perbaikan Arsitektur & Konsolidasi

#### 1. Eliminasi File Legacy & Model Duplikat
- **Target Tindakan:**  
  Hapus model flat yang sudah tergantikan oleh model subdomain V4 dan merujuk ke tabel mati:
  - Hapus: `App\Models\AssessmentModel`, `AssessmentResultModel`, `QuestionModel`, `AnswerModel`, `ProgressModel`, `ClassModel`, `ModuleModel`.
  - Hapus: `App\Models\CaseStudyModel`, `FaqModel`, `TemplateModel`, `CategoryModel` (semua sudah digantikan oleh `App\Models\Knowledge\*`).
  - Hapus: `App\Models\RegulationModel`, `RegulationCategoryModel`, `RegulationVersionModel` di root (gunakan namespace `App\Models\Regulation\*`).
  - Hapus controller tak terpakai: `LandingController.php`, `Home.php`, `Learning\ClassController.php`, `Learning\AssessmentController.php`, `Regulation\RegulationController.php`, `Knowledge\TemplateController.php`, `Workspace\PesertaController.php`.

#### 2. Konsolidasi Service Layer & Eliminasi Toy Services
- **Target Tindakan:**  
  - Hapus direktori kosong `app/Services/Organization/` dan `app/Models/Organization/`.
  - Hapus toy services tidak terpakai: `SearchService`, `StatisticService`, `PPBJService`, `EventService`, `NotificationService`, `AccessControlService`, `RoleAccessService`, `ApiResponseService`, `OrganizationService` (root).
  - Refactor `Services\DashboardService.php`: Tulis ulang query statistiknya agar menggunakan tabel `learning_classes`, `enrollments.status`, `users.profile_type`, dan `quiz_attempts` skema V4, lalu gunakan service ini di `Admin\DashboardController`.
  - Integrasikan `LearningVerticalSliceService` ke dalam `LearningService` utama agar penamaan service menjadi representasi domain permanen, bukan lagi slice eksperimental.

#### 3. Standardisasi Error Handling & 403 Response
- **Target Tindakan:**  
  - Di [app/Filters/AuthenticationFilter.php](file:///c:/laragon/www/begawipengadaan/app/Filters/AuthenticationFilter.php) baris 50, ganti `setBody('Forbidden')` teks polos menjadi rendering halaman error custom:
    ```php
    return service('response')->setStatusCode(403)->setBody(view('errors/html/error_403'));
    ```

#### 4. Hubungkan Workflow Antrean Evaluator & Penilaian Tugas
- **Target Tindakan:**  
  - Buat model `Learning\AssignmentModel` dan `Learning\AssignmentSubmissionModel` yang benar-benar memetakan ke tabel `assignments` dan `assignment_submissions`.
  - Lengkapi `Workspace\EvaluatorController` dengan logic pemeriksaan antrean penugasan, pemberian nilai, dan feedback kelulusan peserta.

---

### PRIORITAS P2: Polishing & Production Readiness

#### 1. Migrasi Database Schema ke CodeIgniter Migrations & Seeders
- **Target Tindakan:**  
  Saat ini folder `app/Database/Migrations` kosong dan database hanya berdiri dari file SQL mentah. Buat migration runner resmi atau minimal file seeder master:
  - `RoleSeeder` (memastikan role `super_administrator`, `administrator`, `operator`, `trainer`, `evaluator`, `peserta`, `public_user` terpasang).
  - `PermissionSeeder` & `RolePermissionSeeder`.
  - `AdminUserSeeder` (akun Super Admin default).

#### 2. Implementasi Real-Data pada Government Intelligence
- **Target Tindakan:**  
  - Buat model `JfPpbjModel`, `CompetencyCertificateModel`, dan `OpdModel` yang sesungguhnya.
  - Ubah view chart di `dashboard/intelligence/home/index.php` dari data hardcoded menjadi dynamic JSON feed dari controller.

#### 3. Implementasi Real-Data pada Berita & Event Publik
- **Target Tindakan:**  
  - Sambungkan `Landing\NewsPublicController` ke `Communication\NewsModel` (hanya artikel berstatus `published`).
  - Sambungkan `Landing\EventPublicController` ke `Communication\EventModel`.

#### 4. Penyusunan Test Suite & PHPUnit Configuration
- **Target Tindakan:**  
  - Buat file konfigurasi `phpunit.xml.dist` standar CodeIgniter 4.
  - Perbaiki `tests/feature/Auth/CompatibilityFoundationTest.php` yang masih mencari file yang sudah tidak ada (`AdminAuthController.php`).

---

## 6. ROADMAP IMPLEMENTASI

Roadmap terarah untuk menyelesaikan seluruh sisa pekerjaan hingga aplikasi siap diluncurkan (*ready to launch*):

```mermaid
flowchart TD
    subgraph Fase 1: Pemulihan Fondasi & P0 Blockers
        A1[Fix Role Key Mismatch & Login Redirect] --> A2[Fix Registrasi Profil & Field Mapping]
        A2 --> A3[Fix Password Reset Model & Schema]
        A3 --> A4[Tutup/Lengkapi Ghost Controllers & Routes]
        A4 --> A5[Fix Profile Update Form & POST Route]
    end

    subgraph Fase 2: Konsolidasi Arsitektur & Pembersihan Legacy
        B1[Hapus 20+ Model & Controller Legacy] --> B2[Hapus 10+ Toy Services Unused]
        B2 --> B3[Hapus Direktori Stub 9-Byte]
        B3 --> B4[Refactor DashboardService ke Skema V4]
        B4 --> B5[Standardisasi Layout & 403/404 Error Pages]
    end

    subgraph Fase 3: Penyambungan Workflow Antar-Modul
        C1[Sambungkan Alur Evaluator & Penilaian Tugas] --> C2[Sambungkan Auto-Trigger Sertifikat Pasca-Lulus]
        C2 --> C3[Aktifkan Berita & Event Publik dengan Data Nyata]
        C3 --> C4[Implementasi Form Pengaturan Sistem POST Handler]
    end

    subgraph Fase 4: Finalisasi & Production Launching
        D1[Generate Database Seeders Resmi] --> D2[Implementasi Modul Intelligence Nyata]
        D2 --> D3[Audit Keamanan, CSRF & Upload Sanitasi]
        D3 --> D4[Setup Test Suite & Smoke Verification]
        D4 --> D5[Production Launch Ready]
    end

    Fase 1 --> Fase 2
    Fase 2 --> Fase 3
    Fase 3 --> Fase 4
```

---

## 7. DAFTAR MASALAH DETAIL (FILE-BY-FILE AUDIT EVIDENCE)

Berikut adalah rincian bukti konkret dari source code untuk setiap temuan:

---

### Masalah 1: Role Key Mismatch Menghambat Deteksi Admin
- **Lokasi File:**  
  `app/Controllers/Dashboard/Auth/LoginController.php` (baris 13-14, 56-57)
- **Kode Aktual:**
  ```php
  $loggedRoles = (array) session()->get('roles');
  $isAdmin = in_array('super_admin', $loggedRoles, true) || in_array('admin', $loggedRoles, true);
  ```
- **Masalah:**  
  Kode memeriksa `'super_admin'` dan `'admin'`. Namun, data riil pada tabel `roles` di database adalah `super_administrator` dan `administrator`.
- **Dampak:**  
  Admin yang berhasil login dianggap bukan admin oleh controller, menyebabkan navigasi dan otorisasi menu salah arah.
- **Solusi:**  
  Sesuaikan string pengecekan menjadi `super_administrator` dan `administrator` sesuai dengan isi master table `roles`.

---

### Masalah 2: Redirect Trap HTTP 403 bagi Peserta Login
- **Lokasi File:**  
  `app/Controllers/Dashboard/Auth/LoginController.php` (baris 68)  
  `app/Filters/AuthenticationFilter.php` (baris 49-51)
- **Kode Aktual:**
  ```php
  // LoginController.php:68
  return redirect()->to(site_url('dashboard'))->with('success', 'Berhasil masuk sebagai ' . $userName . '.');

  // AuthenticationFilter.php:49-51
  if (! (new AuthorizationService())->hasDashboardAccess()) {
      return service('response')->setStatusCode(403)->setBody('Forbidden');
  }
  ```
- **Masalah:**  
  `LoginController` mengarahkan semua role selain `trainer`, `operator`, dan `evaluator` ke `/dashboard`. Peserta biasa (`peserta`) tidak memiliki izin dashboard (tidak punya permission dengan format `domain.action`). Filter langsung menghentikan request dengan HTTP 403 ("Forbidden").
- **Dampak:**  
  Pengguna dengan peran peserta atau masyarakat umum tidak bisa login dan mengira aplikasi rusak.
- **Solusi:**  
  Arahkan user peserta ke route `workspace.participant.home` atau `workspace.home`.

---

### Masalah 3: Registrasi Profil Akademisi Crash pada Service dan DB Constraint
- **Lokasi File:**  
  `app/Controllers/Dashboard/Auth/RegisterController.php` (baris 61-63)  
  `app/Services/Identity/RegistrationService.php` (baris 47, 78-80)  
  `dokumen/schema-fresh-v4-1.sql` (baris 1614)
- **Kode Aktual:**
  ```php
  // RegisterController.php:61-63
  'academic_practitioner' => [
      'profile_type' => 'academic_practitioner',
      ...

  // RegistrationService.php:78-80
  if (! isset(self::FIELD_MAP[$profileType])) {
      throw new \InvalidArgumentException("Tipe profil tidak dikenali: {$profileType}");
  }

  // schema-fresh-v4-1.sql:1614
  CONSTRAINT `users_profile_type_chk` CHECK ((`profile_type` in (_utf8mb4'asn',_utf8mb4'provider',_utf8mb4'academic',_utf8mb4'public')))
  ```
- **Masalah:**  
  Nama tipe profil tidak konsisten: `RegisterController` memakai `'academic_practitioner'`, `RegistrationService` hanya mengenali `'academic'`, dan skema database me-reject nilai selain `'academic'`.
- **Dampak:**  
  Setiap pendaftar bertipe akademisi akan mengalami kegagalan pendaftaran dengan pesan error fatal.
- **Solusi:**  
  Ubah kunci dan nilai `profile_type` di `RegisterController` dari `'academic_practitioner'` menjadi `'academic'`.

---

### Masalah 4: Lupa Kata Sandi Mengakses Tabel yang Tidak Ada
- **Lokasi File:**  
  `app/Services/Identity/PasswordResetService.php` (baris 21, 29, 52)  
  `app/Models/AuthTokenModel.php` (baris 9)
- **Kode Aktual:**
  ```php
  // AuthTokenModel.php:9
  protected $table = 'auth_tokens';

  // PasswordResetService.php:52
  $this->tokens->insert([...]);
  ```
- **Masalah:**  
  Model `AuthTokenModel` menargetkan tabel `auth_tokens`. Tabel ini tidak ada pada skema V4 (`schema-fresh-v4-1.sql`). Skema resmi menggunakan tabel `password_resets`.
- **Dampak:**  
  Fitur lupa kata sandi langsung menghasilkan SQL Error: `Table 'db_begawipbj.auth_tokens' doesn't exist`.
- **Solusi:**  
  Buat `PasswordResetModel` yang terhubung ke tabel `password_resets` dan update `PasswordResetService` agar menggunakan model tersebut.

---

### Masalah 5: Ghost File 9-Byte Memicu Fatal Error pada Rute Operator & Notifikasi
- **Lokasi File:**  
  `app/Controllers/Workspace/OperatorController.php` (9 bytes)  
  `app/Controllers/Workspace/NotificationController.php` (9 bytes)  
  `app/Config/Routes.php` (baris 90, 180-186)
- **Kode Aktual:**
  File hanya berisi:
  ```php
  <?php

  
  ```
- **Masalah:**  
  Route didaftarkan di `Routes.php` ke `OperatorController` dan `NotificationController`, namun file fisiknya kosong tanpa namespace maupun class definition.
- **Dampak:**  
  Mengakses URL `/workspace/operator` atau `/workspace/notifikasi` langsung memicu PHP Fatal Error: *Class "App\Controllers\Workspace\OperatorController" not found*.
- **Solusi:**  
  Lengkapi class controller dengan minimal extends ke `BaseController` dan kembalikan view workspace yang sesuai.

---

### Masalah 6: Method Tidak Ditemukan pada EvaluatorController
- **Lokasi File:**  
  `app/Controllers/Workspace/EvaluatorController.php` (baris 7-13)  
  `app/Config/Routes.php` (baris 172-177)
- **Kode Aktual:**
  ```php
  // Routes.php:172-177
  $routes->get('penugasan', 'EvaluatorController::assignments');
  $routes->get('kelas/(:segment)/submission', 'EvaluatorController::submissions/$1');
  $routes->get('kelas/(:segment)/penilaian', 'EvaluatorController::grading/$1');

  // EvaluatorController.php:7-13
  class EvaluatorController extends BaseController {
      public function index() { return view('workspace/evaluator/index'); }
  }
  ```
- **Masalah:**  
  `Routes.php` memetakan rute ke method `assignments()`, `submissions()`, `grading()`, dsb., namun di controller hanya ada method `index()`.
- **Dampak:**  
  `BadMethodCallException: Method App\Controllers\Workspace\EvaluatorController::assignments does not exist`.
- **Solusi:**  
  Implementasikan method-method tersebut untuk menampilkan antrean tugas evaluasi yang ditugaskan kepada evaluator.

---

### Masalah 7: Dead-End Bounce pada Halaman Status Akun
- **Lokasi File:**  
  `app/Controllers/Dashboard/Auth/AccountStatusController.php` (baris 24-34)  
  `app/Filters/AuthenticationFilter.php` (baris 39-43)
- **Kode Aktual:**
  ```php
  // AuthenticationFilter.php:40-42
  session()->destroy();
  return redirect()->to(site_url('dashboard/status-akun'));

  // AccountStatusController.php:31-34
  if (! $user) {
      return redirect()->to(site_url('dashboard/masuk'))->with('error', 'Silakan masuk terlebih dahulu.');
  }
  ```
- **Masalah:**  
  Ketika user non-aktif mencoba mengakses dashboard, filter menghancurkan sesi (`session()->destroy()`) dan me-redirect ke `dashboard/status-akun`. Namun saat tiba di `AccountStatusController`, controller memeriksa apakah user login (`if (! $user)`). Karena sesinya sudah dihancurkan, user dilempar kembali ke `dashboard/masuk`.
- **Dampak:**  
  User dengan status `pending_verification` atau `revision_required` tidak pernah bisa melihat halaman penjelasan status akun mereka sendiri.
- **Solusi:**  
  Jangan hancurkan session di filter jika ingin menampilkan status akun; pertahankan session `user_id` atau simpan flash session data status akun agar dapat dibaca oleh `AccountStatusController`.

---

## KESIMPULAN ARSITEKTUR

Proyek **BegawiPBJ** memiliki pondasi domain modeling yang sangat kaya dan skema basis data (`schema-fresh-v4-1.sql`) yang matang. Bagian terkuat dari sistem saat ini adalah **Modul Regulasi**, **Modul Sertifikat**, dan **Workspace Trainer**.

Hambatan terbesar menuju launching adalah adanya **sisa-sisa implementasi legacy/prototype** yang belum dibersihkan dan **beberapa missing link pada alur autentikasi dan workspace**.

Dengan mengeksekusi rencana refaktor mulai dari **P0 (Bug Perbaikan Kritis)** lalu **P1 (Pembersihan File Legacy & Konsolidasi Service)**, codebase BegawiPBJ akan menjadi solid, bersih, konsisten, dan siap untuk tahap pengujian akhir menuju production.
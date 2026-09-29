# BegawiPBJ Directory Architecture

## Arsitektur Direktori Platform Ekosistem Digital PBJ Pemerintah Provinsi Lampung

Dokumen ini menetapkan arsitektur direktori resmi **BegawiPBJ V4**, platform ekosistem digital Pengadaan Barang/Jasa Pemerintah (PBJ) Pemerintah Provinsi Lampung.

BegawiPBJ dibangun sebagai fresh **CodeIgniter 4 AppStarter monolith** dengan organisasi kode berbasis feature/domain dalam namespace `App\...`. Struktur ini mempertahankan fondasi standar CodeIgniter 4 serta memisahkan domain bisnis, boundary antarmuka, source template, asset, penyimpanan file, dokumentasi, dan test secara jelas.

FiStudy dan WowDash adalah source template HTML lengkap yang tersedia. Keduanya digunakan ulang serta dimodifikasi menjadi view/component/asset produksi BegawiPBJ sesuai boundary UI, tanpa merender file HTML source secara langsung dari route produksi.

Dokumen ini menjadi acuan penempatan kode produksi. Aturan assessment dan sertifikat berada pada `assessment-and-certification-rules.md`. Rancangan database berada pada `begawipbj-database-blueprint.md`, `begawipbj-database-erd.md`, dan `begawipbj-data-dictionary.md`.

---

# 1. Prinsip Arsitektur

## 1.1 CodeIgniter 4 AppStarter

Proyek dimulai dari:

```bash
composer create-project codeigniter4/appstarter begawipbj
```

Aplikasi menggunakan monolith full-stack:

```text
Satu repository
+ Satu aplikasi CodeIgniter 4
+ Satu database aplikasi utama
+ Feature/domain folder
+ View boundary Main UI dan Dashboard UI
```

Aplikasi tidak menggunakan package `Modules\...` sebagai struktur utama. Semua kode aplikasi memakai namespace `App\...`.

```php
namespace App\Controllers\Learning;
namespace App\Models\Learning;
namespace App\Services\Learning;
```

## 1.2 Feature/domain folder

Domain bisnis dikelompokkan konsisten di controller, model, service, view administratif, test, dan dokumentasi.

| Domain | Cakupan |
|---|---|
| `Auth` | Identitas akun, registrasi, verifikasi email, password, login, profil, RBAC, permohonan role |
| `Organization` | OPD/unit organisasi, organisasi user, wilayah, profil ASN internal/eksternal, peran pengadaan |
| `Learning` | Program, course, class, module, lesson, enrollment, assessment, grading, progress |
| `Knowledge` | Bunga Rampai: artikel, FAQ, studi kasus, template, media, gallery, link sosial, bookmark |
| `Regulation` | Regulasi, versi, status hukum, relasi hukum, tahapan PBJ, kompetensi, keyword, download log |
| `Communication` | Berita, agenda, event, registrasi, waitlist, absensi |
| `Certificate` | Template, eligibility, penerbitan, verifikasi, expiry, revocation |
| `Intelligence` | SDM JF PPBJ, sertifikat kompetensi, kebutuhan serta gap kompetensi |
| `Shared` | File asset, preview/download, approval, audit, notification, export, search, API response |

## 1.3 Batas antarmuka produksi

```text
Main UI — FiStudy
├── Landing Page publik
├── Public Area
├── Autentikasi Main UI
└── Workspace
    ├── Peserta
    ├── Trainer
    ├── Evaluator
    └── Operator

Dashboard UI — WowDash
├── Autentikasi Dashboard
└── Back-office
    ├── Super Admin
    └── Admin
```

| Boundary | Ketentuan |
|---|---|
| Main UI | Mengadaptasi HTML, CSS, module CSS, JavaScript, GSAP, font, icon, image, favicon, vendor, dan pola responsif FiStudy |
| Dashboard UI | Mengadaptasi HTML, CSS, SCSS jika digunakan sebagai source build, JavaScript, font/webfont, icon, image, vendor, tabel, form, chart, dan pola responsif WowDash |
| Workspace | Bagian Main UI; bukan bagian Dashboard |
| Dashboard | Hanya untuk Super Admin/Admin aktif dan berizin |
| Public Area | Hanya konten published/public dan verifikasi sertifikat publik |
| Shared asset | Asset identitas yang benar-benar dipakai kedua UI |

## 1.4 Source template dan hasil integrasi

```text
app/Views/reference/fistudy/  → Source template HTML lengkap FiStudy
app/Views/reference/wowdash/  → Source template HTML lengkap WowDash
```

Aturan:

- Folder source template tidak memiliki route produksi.
- Controller produksi dilarang memanggil `view('reference/...')`.
- Hasil adaptasi FiStudy ditempatkan di `app/Views/main/` dan `public/assets/main/`.
- Hasil adaptasi WowDash ditempatkan di `app/Views/dashboard/` dan `public/assets/dashboard/`.
- Source template dipertahankan sebagai katalog halaman/komponen yang dapat digunakan ulang saat fitur berkembang.
- Asset yang tidak dipakai fitur produksi tidak wajib disalin ke asset produksi.
- `reference/fistudy/inc/sendemail.php` dan `reference/fistudy/inc/PHPMailer/` tidak boleh digunakan sebagai email production.
- Dependency PHP production dikelola Composer; email memakai layanan/configuration CodeIgniter 4.
- Lisensi template, asset, font, icon, serta vendor pihak ketiga wajib dicatat dan dipatuhi.

## 1.5 Tanggung jawab layer

| Layer | Tanggung jawab | Tidak boleh dilakukan |
|---|---|---|
| Controller | HTTP request/response, validasi awal, pemanggilan service, memilih view/response | Logika bisnis lintas domain, query kompleks, transaction besar |
| Service | Use case, aturan bisnis, workflow, authorization lanjutan, transaction, idempotensi | Render view atau ketergantungan request HTTP tanpa kebutuhan |
| Model | Persistence, query tabel, allowed fields, casting, query helper | Use case panjang dan workflow lintas domain |
| Filter | Authentication, account status, role, permission, rate limit, CSRF, headers | Logika bisnis berat |
| Library | Kemampuan teknis reusable: PDF, QR, file delivery, nomor, export | Menjadi lokasi aturan bisnis domain tanpa batas |
| Helper | Fungsi kecil/stateless/formatting | Query database kompleks atau mutasi data |
| View | Presentasi UI dan escaping output | Query database, authorization, logika bisnis |

## 1.6 Konvensi penamaan

| Objek | Pola | Contoh |
|---|---|---|
| Controller | PascalCase + `Controller` | `CourseController.php` |
| Service | PascalCase + `Service` | `CourseService.php` |
| Model | PascalCase + `Model` | `LearningClassModel.php` |
| Filter | PascalCase + `Filter` | `PermissionFilter.php` |
| Library | PascalCase | `PdfGenerator.php` |
| View folder | lowercase, UI/domain-oriented | `main/workspace/participant/` |
| Migration | CI4 timestamp + PascalCase | `CreateIdentityAndRbacTables.php` |
| Seeder | PascalCase + `Seeder` | `RoleSeeder.php` |
| Route name | lowercase dot notation | `learning.course.index` |
| Permission | domain.resource.action | `knowledge.media.publish` |
| Table | plural `snake_case` | `regulation_relations` |
| Column | `snake_case` | `profile_verification_status` |

---

# 2. Struktur Root Repository

```text
begawipbj/
├── app/                              # Kode aplikasi CodeIgniter 4
├── public/                           # Satu-satunya document root web
├── writable/                         # Cache, logs, session, uploads, exports
├── tests/                            # Unit, feature, integration tests
├── database/                         # Reference data, sample tersanitasi, arsip non-runtime
├── docs/                             # Dokumentasi arsitektur, database, deployment, API, ADR
├── build/                            # Artefak build yang dapat dibuat ulang
├── .env.example                      # Template environment tanpa secret
├── .gitignore
├── composer.json
├── composer.lock
├── LICENSE
├── phpunit.xml.dist
├── preload.php                        # Opsional
├── README.md
└── spark
```

Aturan root:

- `public/` adalah satu-satunya document root web server.
- `.env` tidak boleh di-commit.
- `writable/` tidak boleh menjadi document root.
- Data production, dump database production, data ASN terbatas, upload user, sertifikat personal, token, dan key tidak boleh disimpan di Git.
- `build/` hanya memuat artefak yang dapat dibuat ulang.
- Source template FiStudy/WowDash dapat disimpan di repository apabila lisensi mengizinkan.

---

# 3. Struktur `app/`

```text
app/
├── Config/
├── Controllers/
├── Database/
├── Filters/
├── Helpers/
├── Language/
├── Libraries/
├── Models/
├── Services/
├── Views/
├── BaseController.php
├── Common.php
├── .htaccess
└── index.html
```

## 3.1 Namespace standar

```php
namespace App\Controllers\Landing;
namespace App\Controllers\Auth;
namespace App\Controllers\Workspace;
namespace App\Controllers\Admin;
namespace App\Controllers\Learning;
namespace App\Controllers\Knowledge;
namespace App\Controllers\Regulation;
namespace App\Controllers\Communication;
namespace App\Controllers\Certificate;
namespace App\Controllers\Intelligence;
namespace App\Controllers\Shared;

namespace App\Models\Auth;
namespace App\Models\Learning;
namespace App\Models\Knowledge;
namespace App\Models\Regulation;
namespace App\Models\Communication;
namespace App\Models\Certificate;
namespace App\Models\Intelligence;
namespace App\Models\Organization;
namespace App\Models\Shared;

namespace App\Services\Auth;
namespace App\Services\Learning;
namespace App\Services\Knowledge;
namespace App\Services\Regulation;
namespace App\Services\Communication;
namespace App\Services\Certificate;
namespace App\Services\Intelligence;
namespace App\Services\Organization;
namespace App\Services\Shared;
```

---

# 4. Controllers

```text
app/Controllers/
├── Landing/
├── Auth/
├── Workspace/
├── Admin/
├── Learning/
├── Knowledge/
├── Regulation/
├── Communication/
├── Certificate/
├── Intelligence/
└── Shared/
```

## 4.1 `Controllers/Landing/`

```text
Landing/
├── HomeController.php
├── ArenaBelajarController.php
├── BungaRampaiController.php
├── RegulationPublicController.php
├── NewsPublicController.php
├── EventPublicController.php
├── StatisticPublicController.php
└── CertificateVerificationController.php
```

| Controller | Tanggung jawab |
|---|---|
| `HomeController` | Landing Page, konten unggulan, KPI publik, CTA |
| `ArenaBelajarController` | Katalog, detail program/course/class publik |
| `BungaRampaiController` | List/detail konten knowledge dan media published |
| `RegulationPublicController` | Katalog regulasi, detail metadata, status hukum, relasi, versi dokumen |
| `NewsPublicController` | Daftar/detail berita published |
| `EventPublicController` | Daftar/detail agenda dan event publik |
| `StatisticPublicController` | Statistik agregat aman untuk publik |
| `CertificateVerificationController` | Verifikasi sertifikat berbasis token/QR |

## 4.2 `Controllers/Auth/`

```text
Auth/
├── LoginController.php
├── RegisterController.php
├── RegisterProfileController.php
├── EmailVerificationController.php
├── ProfileController.php
├── ProfileChangeRequestController.php
├── RoleRequestController.php
└── PasswordController.php
```

| Controller | Tanggung jawab |
|---|---|
| `LoginController` | Login, logout, session regeneration, redirect sesuai akses |
| `RegisterController` | Akun dasar registrasi dan pemilihan tipe profil |
| `RegisterProfileController` | Step profil ASN/Penyedia/Akademisi/Masyarakat Umum |
| `EmailVerificationController` | Verifikasi email dan resend verification |
| `ProfileController` | Profil aktif pengguna dan perubahan non-sensitif |
| `ProfileChangeRequestController` | Pengajuan perubahan identitas/organisasi sensitif |
| `RoleRequestController` | Pengajuan role Trainer/Evaluator oleh pengguna |
| `PasswordController` | Lupa/reset/ubah password |

Aturan registrasi:

```text
ASN
→ wajib NIP/NIPPK
→ tidak wajib NIK
→ tidak wajib upload Surat Tugas

Penyedia Barang/Jasa
→ wajib NIK

Akademisi
→ wajib NIK
→ tidak wajib CV/bukti dukung saat registrasi

Masyarakat Umum
→ wajib NIK
→ tidak wajib upload dokumen saat registrasi
```

## 4.3 `Controllers/Workspace/`

```text
Workspace/
├── WorkspaceHomeController.php
├── ParticipantController.php
├── TrainerController.php
├── EvaluatorController.php
├── OperatorController.php
├── BookmarkController.php
└── NotificationController.php
```

| Controller | Tanggung jawab |
|---|---|
| `WorkspaceHomeController` | Ringkasan workspace berdasarkan role/permission |
| `ParticipantController` | Class saya, belajar, quiz, assignment, progres, feedback, sertifikat |
| `TrainerController` | Class penugasan, lesson, assessment, peserta, monitoring progres |
| `EvaluatorController` | Antrean grading, nilai, feedback, request revision, histori evaluasi |
| `OperatorController` | Verifikasi profil, enrollment, absensi, approval delegasi |
| `BookmarkController` | Bookmark user, filter bookmark type, urutan terbaru, add/remove |
| `NotificationController` | Inbox notifikasi user dan mark as read |

## 4.4 `Controllers/Admin/`

```text
Admin/
├── AdminAuthController.php
├── DashboardController.php
├── UserController.php
├── UserProfileVerificationController.php
├── RoleController.php
├── PermissionController.php
├── RoleRequestReviewController.php
├── ProcurementRoleController.php
├── ApprovalController.php
├── AuditController.php
├── OrganizationController.php
└── SettingController.php
```

| Controller | Tanggung jawab |
|---|---|
| `AdminAuthController` | Login Dashboard UI |
| `DashboardController` | KPI internal, approval queue, aktivitas, indikator data |
| `UserController` | Akun, status akun, user role, user detail, tindakan administratif |
| `UserProfileVerificationController` | Review profil pending/revision/verified/rejected |
| `RoleController` | Master role dan role-permission |
| `PermissionController` | Katalog permission |
| `RoleRequestReviewController` | Approve/reject pengajuan Trainer/Evaluator |
| `ProcurementRoleController` | Master peran pengadaan ASN |
| `ApprovalController` | Approval lintas domain |
| `AuditController` | Audit log read-only |
| `OrganizationController` | OPD/unit organisasi Pemprov Lampung |
| `SettingController` | Branding, sistem, file, sertifikat, keamanan, notifikasi |

## 4.5 `Controllers/Learning/`

```text
Learning/
├── ProgramController.php
├── CourseController.php
├── LearningClassController.php
├── ModuleController.php
├── LessonController.php
├── EnrollmentController.php
├── PreTestController.php
├── QuizController.php
├── AssignmentController.php
├── GradingController.php
├── PostTestController.php
└── StudyController.php
```

Aturan assessment dikelola melalui service:

```text
Pre-test      → satu attempt, auto-grading, tidak dihitung
Quiz           → best attempt
Assignment     → optional per class; wajib bila tersedia; revisi/upload ulang
Post-test      → best attempt, bobot 60%
Passing grade  → minimum 70
```

## 4.6 `Controllers/Knowledge/`

```text
Knowledge/
├── ArticleController.php
├── FaqController.php
├── CaseStudyController.php
├── DocumentTemplateController.php
├── KnowledgeMediaController.php
├── KnowledgeMediaItemController.php
├── KnowledgeCarouselController.php
└── BookmarkController.php
```

| Controller | Tanggung jawab |
|---|---|
| `KnowledgeMediaController` | Poster, flyer, infografik, gallery, video, YouTube, sosial media, external link |
| `KnowledgeMediaItemController` | Item media/gallery, caption, alt text, sequence |
| `KnowledgeCarouselController` | Featured content, carousel enable, carousel order |
| `BookmarkController` | Bookmark konten knowledge dan resource lintas domain melalui service bersama |

## 4.7 `Controllers/Regulation/`

```text
Regulation/
├── RegulationController.php
├── RegulationCategoryController.php
├── RegulationVersionController.php
├── RegulationRelationController.php
├── RegulationDownloadController.php
└── RegulationBookmarkController.php
```

| Controller | Tanggung jawab |
|---|---|
| `RegulationController` | Metadata regulasi, status hukum, workflow, pencarian/filter |
| `RegulationVersionController` | Versioning file, current version, changelog |
| `RegulationRelationController` | Relasi hukum: amend, revoke, replace, implement, reference |
| `RegulationDownloadController` | Log domain download bila endpoint regulasi khusus digunakan |
| `RegulationBookmarkController` | Bookmark regulasi/versi regulasi |

Regulasi dengan `workflow_status = published` tetap tampil publik tanpa membedakan `legal_status`.

```text
active
amended
revoked
expired
```

Status hukum adalah konteks, bukan pembatas akses file. Preview/download tetap tersedia jika file tersedia dan visibility mengizinkan.

## 4.8 `Controllers/Communication/`

```text
Communication/
├── NewsController.php
├── EventController.php
├── EventRegistrationController.php
└── AttendanceController.php
```

## 4.9 `Controllers/Certificate/`

```text
Certificate/
├── CertificateController.php
├── CertificateApprovalController.php
└── CertificateTemplateController.php
```

## 4.10 `Controllers/Intelligence/`

```text
Intelligence/
├── OpdController.php
├── JfPpbjController.php
├── CompetencyCertificateController.php
└── IntelligenceDashboardController.php
```

## 4.11 `Controllers/Shared/`

```text
Shared/
├── FileController.php
├── SearchController.php
└── ExportController.php
```

| Controller | Tanggung jawab |
|---|---|
| `FileController` | Preview inline, buka file, download attachment dengan authorization |
| `SearchController` | Pencarian lintas domain sesuai permission |
| `ExportController` | Export internal yang berizin dan diaudit |

Route file generik:

```text
/files/{fileAssetId}/preview
/files/{fileAssetId}/download
```

---

# 5. Models

```text
app/Models/
├── Auth/
├── Learning/
├── Knowledge/
├── Regulation/
├── Communication/
├── Certificate/
├── Intelligence/
├── Organization/
└── Shared/
```

## 5.1 `Models/Auth/`

```text
Auth/
├── UserModel.php
├── UserRoleModel.php
├── RoleModel.php
├── PermissionModel.php
├── RolePermissionModel.php
├── UserProfileModel.php
├── UserAsnProfileModel.php
├── UserProviderProfileModel.php
├── UserAcademicProfileModel.php
├── UserPublicProfileModel.php
├── ProfileVerificationModel.php
├── ProfileChangeRequestModel.php
├── RoleRequestModel.php
├── PasswordResetTokenModel.php
└── LoginAttemptModel.php
```

## 5.2 `Models/Organization/`

```text
Organization/
├── OrganizationUnitModel.php
├── UserOrganizationModel.php
├── WilayahModel.php
├── ProcurementRoleModel.php
└── UserProcurementRoleModel.php
```

`WilayahModel` adalah read-oriented model untuk master resmi `wilayah(kode, nama)`. Seeder development/dummy dilarang menambah, mengubah, menghapus, atau truncate master wilayah.

## 5.3 `Models/Learning/`

```text
Learning/
├── ProgramModel.php
├── CourseModel.php
├── LearningClassModel.php
├── ClassTrainerModel.php
├── ClassEvaluatorModel.php
├── LearningModuleModel.php
├── LessonModel.php
├── LessonMaterialModel.php
├── EnrollmentModel.php
├── LearningProgressModel.php
├── AssessmentConfigurationModel.php
├── PreTestAttemptModel.php
├── QuizModel.php
├── QuestionModel.php
├── QuestionOptionModel.php
├── QuizAttemptModel.php
├── QuizAnswerModel.php
├── QuizAnswerOptionModel.php
├── AssignmentModel.php
├── AssignmentSubmissionModel.php
├── AssignmentSubmissionFileModel.php
├── AssignmentGradeModel.php
├── PostTestAttemptModel.php
├── ParticipantFeedbackModel.php
└── FinalGradeModel.php
```

## 5.4 `Models/Knowledge/`

```text
Knowledge/
├── KnowledgeCategoryModel.php
├── TagModel.php
├── ArticleModel.php
├── ArticleTagModel.php
├── FaqModel.php
├── CaseStudyModel.php
├── DocumentTemplateModel.php
├── KnowledgeMediaContentModel.php
├── KnowledgeMediaItemModel.php
└── BookmarkModel.php
```

## 5.5 `Models/Regulation/`

```text
Regulation/
├── RegulationCategoryModel.php
├── ProcurementStageModel.php
├── CompetencyModel.php
├── RegulationModel.php
├── RegulationVersionModel.php
├── RegulationRelationModel.php
├── RegulationProcurementStageModel.php
├── RegulationCompetencyModel.php
├── RegulationKeywordModel.php
└── RegulationDownloadLogModel.php
```

## 5.6 Domain model lain

```text
Communication/
├── NewsCategoryModel.php
├── NewsModel.php
├── EventModel.php
├── EventRegistrationModel.php
└── EventAttendanceModel.php

Certificate/
├── CertificateTemplateModel.php
├── CertificateModel.php
├── CertificateSequenceModel.php
├── CertificateVerificationLogModel.php
└── CertificateRevocationModel.php

Intelligence/
├── JfPpbjLevelModel.php
├── JfPpbjPersonnelModel.php
├── JfPpbjHistoryModel.php
├── CompetencyCertificateTypeModel.php
├── CompetencyCertificateModel.php
└── CompetencyGapSnapshotModel.php

Shared/
├── ApprovalModel.php
├── ApprovalHistoryModel.php
├── AuditLogModel.php
├── NotificationModel.php
└── FileAssetModel.php
```

---

# 6. Services

```text
app/Services/
├── Auth/
├── Learning/
├── Knowledge/
├── Regulation/
├── Communication/
├── Certificate/
├── Intelligence/
├── Organization/
└── Shared/
```

## 6.1 `Services/Auth/`

```text
Auth/
├── AuthenticationService.php
├── RegistrationService.php
├── RegistrationProfileService.php
├── EmailVerificationService.php
├── ProfileService.php
├── ProfileVerificationService.php
├── ProfileChangeRequestService.php
├── RoleRequestService.php
├── PasswordService.php
└── AccessControlService.php
```

| Service | Fungsi utama |
|---|---|
| `RegistrationService` | Membuat akun pending, akun dasar, verifikasi email awal |
| `RegistrationProfileService` | Validasi profil berdasarkan tipe akun dan field bersyarat |
| `ProfileVerificationService` | Review pending/revision/verified/rejected oleh Operator/Admin |
| `ProfileChangeRequestService` | Perubahan NIP/NIPPK, NIK, NIB, organisasi, atau identitas sensitif |
| `RoleRequestService` | Pengajuan Trainer/Evaluator dan review Admin |
| `AccessControlService` | Role, permission, role assignment, account access |

Aturan profile ASN:

```text
institution_scope = pemprov_lampung
→ organization_unit_id wajib
→ external organization field kosong

institution_scope != pemprov_lampung
→ organization_unit_id kosong
→ external institution type/name/work unit wajib
```

## 6.2 `Services/Organization/`

```text
Organization/
├── OrganizationService.php
├── WilayahService.php
├── ProcurementRoleService.php
└── AsnOrganizationProfileService.php
```

| Service | Fungsi utama |
|---|---|
| `WilayahService` | Validasi hierarki kode wilayah |
| `ProcurementRoleService` | Master/riwayat peran pengadaan ASN |
| `AsnOrganizationProfileService` | Validasi ASN Pemprov Lampung versus ASN luar Pemprov Lampung |

## 6.3 `Services/Learning/`

```text
Learning/
├── ProgramService.php
├── CourseService.php
├── ClassService.php
├── EnrollmentService.php
├── LessonService.php
├── AssessmentConfigurationService.php
├── PreTestService.php
├── QuizService.php
├── AssignmentService.php
├── GradingService.php
├── PostTestService.php
├── ProgressService.php
└── LearningCompletionService.php
```

## 6.4 `Services/Knowledge/`

```text
Knowledge/
├── ArticleService.php
├── KnowledgeSearchService.php
├── BookmarkService.php
├── DocumentTemplateService.php
├── KnowledgeMediaService.php
├── KnowledgeMediaGalleryService.php
├── KnowledgeCarouselService.php
└── ExternalMediaUrlService.php
```

| Service | Fungsi utama |
|---|---|
| `BookmarkService` | Bookmark lintas domain, uniqueness, latest-first, authorization ulang resource |
| `KnowledgeMediaService` | Poster, flyer, infografik, video, YouTube, sosial media, external link |
| `KnowledgeMediaGalleryService` | Multiple media item, alt text, caption, urutan gallery |
| `KnowledgeCarouselService` | Featured, carousel enabled, urutan carousel |
| `ExternalMediaUrlService` | Validasi provider/URL YouTube, sosial media, external link; membentuk embed URL aman |

## 6.5 `Services/Regulation/`

```text
Regulation/
├── RegulationService.php
├── RegulationVersionService.php
├── RegulationRelationService.php
├── RegulationSearchService.php
└── RegulationDownloadService.php
```

| Service | Fungsi utama |
|---|---|
| `RegulationVersionService` | Versioning file, current version tunggal, changelog |
| `RegulationRelationService` | Relasi amend/revoke/replace/implement/reference |
| `RegulationDownloadService` | Pencatatan download regulasi bila file diakses/download |

## 6.6 Service domain lain

```text
Communication/
├── NewsService.php
├── EventService.php
├── EventRegistrationService.php
└── AttendanceService.php

Certificate/
├── CertificateEligibilityService.php
├── CertificateGenerationService.php
├── CertificateVerificationService.php
├── CertificateApprovalService.php
└── CertificateRevocationService.php

Intelligence/
├── OpdService.php
├── JfPpbjService.php
├── CompetencyCertificateService.php
├── CompetencyGapService.php
└── IntelligenceStatisticService.php
```

## 6.7 `Services/Shared/`

```text
Shared/
├── ApprovalService.php
├── AuditService.php
├── FileStorageService.php
├── FileAccessService.php
├── FilePreviewService.php
├── FileDownloadService.php
├── NotificationService.php
├── SearchService.php
├── ExportService.php
├── DashboardMetricService.php
├── ApiResponseService.php
└── WorkspaceNavigationService.php
```

| Service | Fungsi utama |
|---|---|
| `FileStorageService` | Upload validation, physical storage, checksum, metadata, retention |
| `FileAccessService` | Authorization file berdasarkan visibility, ownership, role, permission, domain resource |
| `FilePreviewService` | Preview inline PDF aman |
| `FileDownloadService` | Download attachment aman |
| `AuditService` | Audit aksi sensitif, before/after data tersanitasi |
| `ApprovalService` | Draft → Submit → Review → Approve → Publish |

Aturan file:

```text
file_assets.path = NULL
→ File belum tersedia
→ Preview/download nonaktif

PDF preview
→ MIME application/pdf
+ path tersedia
+ file fisik tersedia
+ user authorized
→ Content-Disposition: inline

Download
→ path tersedia
+ file fisik tersedia
+ user authorized
→ Content-Disposition: attachment
```

---

# 7. Views dan Source Template

```text
app/Views/
├── main/                         # Produksi: hasil adaptasi FiStudy
├── dashboard/                    # Produksi: hasil adaptasi WowDash
├── errors/
└── reference/                    # Source template; non-production
    ├── fistudy/
    └── wowdash/
```

## 7.1 `Views/main/` — Produksi FiStudy

```text
main/
├── layouts/
│   ├── public.php
│   ├── auth.php
│   ├── workspace.php
│   └── minimal.php
├── components/
│   ├── head.php
│   ├── scripts.php
│   ├── public_navigation.php
│   ├── workspace_navigation.php
│   ├── workspace_sidebar.php
│   ├── footer.php
│   ├── breadcrumb.php
│   ├── pagination.php
│   ├── flash_message.php
│   ├── empty_state.php
│   ├── confirmation_modal.php
│   ├── bookmark_button.php
│   ├── file_preview.php
│   ├── pdf_viewer.php
│   ├── media_card.php
│   ├── carousel_controls.php
│   ├── search_form.php
│   └── assessment_status.php
├── public/
│   ├── home/
│   │   ├── index.php
│   │   └── sections/
│   ├── learning/
│   ├── knowledge/
│   │   ├── index.php
│   │   ├── articles/
│   │   ├── faqs/
│   │   ├── case_studies/
│   │   ├── templates/
│   │   ├── media/
│   │   ├── gallery/
│   │   └── videos/
│   ├── regulation/
│   │   ├── index.php
│   │   ├── detail.php
│   │   ├── version_history.php
│   │   └── relation_list.php
│   ├── communication/
│   ├── statistics/
│   └── certificate/
├── workspace/
│   ├── shared/
│   ├── participant/
│   ├── trainer/
│   ├── evaluator/
│   ├── operator/
│   ├── bookmarks/
│   └── profile/
└── auth/
    ├── login.php
    ├── register_account.php
    ├── register_profile_type.php
    ├── register_profile_asn.php
    ├── register_profile_provider.php
    ├── register_profile_academic.php
    ├── register_profile_public.php
    ├── register_review.php
    ├── register_success.php
    ├── pending_verification.php
    ├── revision_required.php
    ├── email_verification.php
    ├── forgot_password.php
    ├── reset_password.php
    ├── change_password.php
    └── profile.php
```

### Workspace Peserta

```text
main/workspace/participant/
├── index.php
├── my_classes.php
├── class_detail.php
├── study.php
├── pre_test.php
├── pre_test_result.php
├── quiz.php
├── quiz_result.php
├── assignments.php
├── assignment_submission.php
├── assignment_revision.php
├── post_test.php
├── post_test_result.php
├── progress.php
├── feedback.php
├── certificates.php
└── profile.php
```

### Workspace Bookmark

```text
main/workspace/bookmarks/
├── index.php
├── learning.php
├── knowledge.php
├── regulations.php
├── communication.php
└── certificates.php
```

Bookmark default diurutkan berdasarkan `bookmarks.created_at DESC`.

### Workspace Profile

```text
main/workspace/profile/
├── index.php
├── account.php
├── asn_profile.php
├── provider_profile.php
├── academic_profile.php
├── public_profile.php
├── procurement_roles.php
├── change_requests.php
└── role_requests.php
```

## 7.2 `Views/dashboard/` — Produksi WowDash

```text
dashboard/
├── layouts/
│   ├── dashboard.php
│   ├── auth.php
│   └── print.php
├── components/
│   ├── head.php
│   ├── scripts.php
│   ├── header.php
│   ├── sidebar.php
│   ├── breadcrumb.php
│   ├── footer.php
│   ├── flash_message.php
│   ├── data_table.php
│   ├── filter_bar.php
│   ├── status_badge.php
│   ├── approval_timeline.php
│   ├── chart_container.php
│   ├── pdf_viewer_modal.php
│   └── confirmation_modal.php
├── auth/
│   └── login.php
├── home/
│   └── index.php
├── users/
│   ├── index.php
│   ├── detail.php
│   ├── profile_verifications.php
│   ├── profile_change_requests.php
│   └── role_requests.php
├── access/
├── organization/
│   ├── units/
│   └── procurement_roles/
├── approvals/
├── audit/
├── learning/
├── knowledge/
│   ├── media/
│   ├── galleries/
│   └── carousel/
├── regulation/
│   ├── regulations/
│   ├── versions/
│   └── relations/
├── communication/
├── certificates/
├── intelligence/
├── reports/
└── settings/
```

## 7.3 `Views/reference/fistudy/` — Source FiStudy

```text
reference/fistudy/
├── 404.html
├── about.html
├── become-a-teacher.html
├── blog-carousel.html
├── blog-details.html
├── blog-list.html
├── blog.html
├── cart.html
├── checkout.html
├── coming-soon.html
├── contact.html
├── course-carousel.html
├── course-details.html
├── course-list.html
├── course.html
├── event-details.html
├── events-carousel.html
├── events.html
├── faq.html
├── gallery.html
├── index.html
├── index-dark.html
├── index-one-page.html
├── index2.html
├── index2-one-page.html
├── index3.html
├── index3-one-page.html
├── instructor-carousel.html
├── instructor-details.html
├── instructor.html
├── login.html
├── pricing.html
├── product-details.html
├── products.html
├── sign-up.html
├── testimonials-carousel.html
├── testimonials.html
├── wishlist.html
├── assets/
│   ├── css/
│   │   ├── module-css/
│   │   └── *.css
│   ├── fonts/
│   └── images/
├── inc/
│   ├── sendemail.php
│   └── PHPMailer/
└── js/
    ├── gsap/
    └── *.js
```

## 7.4 `Views/reference/wowdash/` — Source WowDash

```text
reference/wowdash/
├── access-denied.html
├── add-blog.html
├── add-user.html
├── alert.html
├── assign-role.html
├── avatar.html
├── bad-request.html
├── badges.html
├── blank-page.html
├── blog-details.html
├── blog.html
├── button.html
├── calendar-main.html
├── calendar.html
├── card.html
├── carousel.html
├── chat-empty.html
├── chat-message.html
├── chat-profile.html
├── code-generator-new.html
├── code-generator.html
├── colors.html
├── column-chart.html
├── coming-soon.html
├── company.html
├── currencies.html
├── dropdown.html
├── email.html
├── error.html
├── faq.html
├── forbidden.html
├── forgot-password.html
├── form-layout.html
├── form-validation.html
├── form.html
├── gallery-grid.html
├── gallery-hover.html
├── gallery-masonry.html
├── gallery.html
├── image-generator.html
├── image-upload.html
├── index.html
├── index-2.html ... index-20.html
├── internal-server.html
├── invoice-add.html
├── invoice-edit.html
├── invoice-list.html
├── invoice-preview.html
├── kanban.html
├── language.html
├── line-chart.html
├── list.html
├── maintenance.html
├── marketplace-details.html
├── marketplace.html
├── notification-alert.html
├── notification.html
├── pagination.html
├── payment-gateway.html
├── pie-chart.html
├── portfolio.html
├── pricing.html
├── progress.html
├── radio.html
├── role-access.html
├── service-unavailable.html
├── sign-in.html
├── sign-up.html
├── star-rating.html
├── starred.html
├── switch.html
├── table-basic.html
├── table-data.html
├── tabs.html
├── tags.html
├── terms-condition.html
├── testimonials.html
├── text-generator-new.html
├── text-generator.html
├── theme.html
├── tooltip.html
├── typography.html
├── users-grid.html
├── users-list.html
├── users-role-permission.html
├── veiw-details.html
├── video-generator.html
├── videos.html
├── view-profile.html
├── voice-generator.html
├── wallet.html
├── widgets.html
├── wizard.html
└── assets/
    ├── css/
    │   └── lib/
    ├── fonts/
    ├── images/
    ├── js/
    │   └── lib/
    ├── sass/
    │   ├── abstracts/
    │   ├── components/
    │   ├── layout/
    │   └── utilities/
    └── webfonts/
```

---

# 8. Database: Migration dan Seeder

```text
app/Database/
├── Migrations/
│   ├── CreateIdentityAndRbacTables.php
│   ├── CreateOrganizationAndProfileVerificationTables.php
│   ├── CreateSharedWorkflowAuditFileTables.php
│   ├── CreateLearningCoreTables.php
│   ├── CreateLearningAssessmentTables.php
│   ├── CreateKnowledgeAndMediaTables.php
│   ├── CreateRegulationAndRelationTables.php
│   ├── CreateCommunicationTables.php
│   ├── CreateCertificateTables.php
│   ├── CreateGovernmentIntelligenceTables.php
│   └── AddPerformanceIndexesAndConstraints.php
└── Seeds/
    ├── DatabaseSeeder.php
    ├── Reference/
    ├── System/
    ├── Development/
    └── Import/
```

```text
Seeds/
├── DatabaseSeeder.php
├── Reference/
│   ├── RoleSeeder.php
│   ├── PermissionSeeder.php
│   ├── RolePermissionSeeder.php
│   ├── OrganizationSeeder.php
│   ├── ProcurementRoleSeeder.php
│   ├── KnowledgeCategorySeeder.php
│   ├── RegulationCategorySeeder.php
│   ├── ProcurementStageSeeder.php
│   ├── CompetencySeeder.php
│   ├── JfPpbjLevelSeeder.php
│   └── CompetencyCertificateTypeSeeder.php
├── System/
│   ├── InitialSuperAdminSeeder.php
│   └── SystemSettingSeeder.php
├── Development/
│   ├── DevelopmentUserSeeder.php
│   ├── LearningDemoSeeder.php
│   ├── KnowledgeDemoSeeder.php
│   ├── RegulationDemoSeeder.php
│   ├── CommunicationDemoSeeder.php
│   └── IntelligenceDemoSeeder.php
└── Import/
    ├── WilayahImportSeeder.php
    └── OpdImportSeeder.php
```

Aturan:

- `DatabaseSeeder` hanya memanggil reference/system seeder yang aman dan deterministik.
- Development seeder dijalankan eksplisit pada environment development/testing.
- `WilayahImportSeeder` hanya mengimpor master resmi `database/reference/wilayah.sql` atau sumber resmi disetujui.
- Seeder development/dummy dilarang menyentuh tabel `wilayah`.
- Seeder profile/organization harus mencakup status dan scope yang valid.

---

# 9. Filters

```text
app/Filters/
├── AuthFilter.php
├── GuestFilter.php
├── AdminFilter.php
├── WorkspaceFilter.php
├── RoleFilter.php
├── PermissionFilter.php
├── AccountStatusFilter.php
├── EmailVerifiedFilter.php
├── ProfileVerifiedFilter.php
├── CsrfGuardFilter.php
├── RateLimitFilter.php
├── AuditRequestFilter.php
└── SecurityHeadersFilter.php
```

| Filter | Fungsi |
|---|---|
| `AuthFilter` | Memastikan user login |
| `GuestFilter` | Membatasi auth page untuk user belum login |
| `AdminFilter` | Dashboard hanya Super Admin/Admin aktif |
| `WorkspaceFilter` | Workspace hanya role operasional aktif |
| `RoleFilter` | Memeriksa role spesifik bila diperlukan |
| `PermissionFilter` | Memeriksa permission granular |
| `AccountStatusFilter` | Menolak account status yang tidak berhak mengakses |
| `EmailVerifiedFilter` | Memastikan email telah diverifikasi untuk aksi tertentu |
| `ProfileVerifiedFilter` | Memastikan profil telah diverifikasi untuk aksi tertentu |
| `CsrfGuardFilter` | Perlindungan request perubahan state |
| `RateLimitFilter` | Membatasi login, register, reset, upload, preview/verification rawan abuse |
| `AuditRequestFilter` | Menandai request administratif untuk audit |
| `SecurityHeadersFilter` | CSP dan security headers |

Filter tidak menggantikan authorization service. Service tetap memvalidasi ownership, penugasan, tipe profile, status workflow, dan rule domain.

---

# 10. Libraries, Helpers, Language, dan Config

## 10.1 Libraries

```text
app/Libraries/
├── PdfGenerator.php
├── QRGenerator.php
├── CertificateNumberGenerator.php
├── FileNameGenerator.php
├── PdfFileInspector.php
├── ExternalUrlValidator.php
├── YouTubeEmbedUrlBuilder.php
├── SpreadsheetExporter.php
└── SignedUrlGenerator.php
```

## 10.2 Helpers

```text
app/Helpers/
├── access_helper.php
├── asset_helper.php
├── bookmark_helper.php
├── format_helper.php
├── file_helper.php
├── certificate_helper.php
├── regulation_helper.php
├── workflow_helper.php
└── statistic_helper.php
```

## 10.3 Language

```text
app/Language/
├── id/
│   ├── Auth.php
│   ├── Registration.php
│   ├── Learning.php
│   ├── Knowledge.php
│   ├── Regulation.php
│   ├── Workflow.php
│   ├── Certificate.php
│   ├── File.php
│   ├── Validation.php
│   └── General.php
└── en/
    ├── Validation.php
    └── General.php
```

## 10.4 Config

```text
app/Config/
├── App.php
├── Autoload.php
├── Database.php
├── Filters.php
├── Routes.php
├── Security.php
├── Validation.php
├── Services.php
├── Email.php
├── Session.php
├── ContentSecurityPolicy.php
├── Certificate.php
├── FileStorage.php
├── KnowledgeMedia.php
├── Registration.php
├── Workflow.php
└── Notification.php
```

Credential, API key, secret, dan encryption key tidak boleh disimpan dalam config repository; gunakan environment/secret manager.

---

# 11. Public Asset

```text
public/
├── index.php
├── .htaccess
├── favicon.ico
├── robots.txt
└── assets/
    ├── main/
    │   ├── css/
    │   │   └── module-css/
    │   ├── js/
    │   │   └── gsap/
    │   ├── images/
    │   ├── fonts/
    │   └── vendor/
    ├── dashboard/
    │   ├── css/
    │   │   └── lib/
    │   ├── js/
    │   │   └── lib/
    │   ├── images/
    │   ├── fonts/
    │   ├── webfonts/
    │   └── vendor/
    └── shared/
        ├── images/
        └── fonts/
```

| Asset group | Dipakai oleh | Source utama |
|---|---|---|
| `assets/main/` | Landing, Public Area, auth Main UI, Workspace | FiStudy terpilih/diadaptasi |
| `assets/dashboard/` | Auth Dashboard dan Back-office | WowDash terpilih/diadaptasi |
| `assets/shared/` | Kedua UI | Asset identitas umum |

Aturan:

- Upload user, dokumen runtime, export, dan sertifikat generated tidak disimpan di `public/assets/`.
- Asset source tidak harus disalin seluruhnya ke production.
- Path source template disesuaikan menggunakan helper/base URL CI4.
- Jika SCSS WowDash digunakan, source SCSS berada pada jalur build internal; CSS hasil build berada di `public/assets/dashboard/css/`.

---

# 12. Writable Storage

```text
writable/
├── cache/
├── logs/
├── session/
├── uploads/
│   ├── learning/
│   │   ├── lessons/
│   │   ├── materials/
│   │   └── assignments/
│   ├── knowledge/
│   │   ├── templates/
│   │   ├── media/
│   │   ├── gallery/
│   │   └── attachments/
│   ├── regulations/
│   │   ├── originals/
│   │   └── versions/
│   ├── communication/
│   │   ├── news/
│   │   └── events/
│   ├── certificates/
│   │   ├── generated/
│   │   └── templates/
│   └── temporary/
└── exports/
    ├── reports/
    └── spreadsheets/
```

Aturan file:

- Nama fisik file menggunakan UUID/hash dari `FileNameGenerator`.
- Nama asli disimpan pada `file_assets.original_name`.
- Path fisik tidak dibentuk langsung dari input user.
- File private/protected hanya disajikan melalui `FileController` dan `FileAccessService`.
- `file_assets.path = NULL` berarti metadata tersedia tanpa file fisik; UI menampilkan **File belum tersedia**.
- Preview PDF menggunakan MIME validation, signature check, authorization, dan response inline.
- Download menggunakan authorization dan response attachment.
- File temporary mempunyai cleanup/retention job.
- Upload file PDF memvalidasi extension, MIME server-side, magic bytes `%PDF-`, ukuran, checksum, dan malware scan bila tersedia.

---

# 13. Tests

```text
tests/
├── unit/
│   ├── Services/
│   │   ├── Auth/
│   │   ├── Learning/
│   │   ├── Knowledge/
│   │   ├── Regulation/
│   │   ├── Certificate/
│   │   └── Shared/
│   └── Libraries/
├── feature/
│   ├── Auth/
│   ├── Registration/
│   ├── Landing/
│   ├── Workspace/
│   ├── Admin/
│   ├── Learning/
│   ├── Knowledge/
│   ├── Regulation/
│   ├── Communication/
│   ├── Certificate/
│   └── Shared/
├── integration/
│   ├── Database/
│   ├── FileStorage/
│   ├── Registration/
│   └── Workflow/
└── _support/
    ├── Factories/
    └── Fixtures/
```

Cakupan minimum:

- Registrasi ASN, Penyedia, Akademisi, dan Masyarakat Umum.
- ASN wajib NIP/NIPPK tanpa NIK; non-ASN wajib NIK.
- ASN Pemprov Lampung memakai `organization_units`; ASN luar Pemprov memakai organisasi manual.
- Profile verification, revision request, account approval, email verification, dan activation.
- Role default `public_user + participant` setelah approval.
- User tidak dapat menambah/menghapus role/permission sendiri.
- Permohonan Trainer/Evaluator dan review Admin.
- Permission server-side, ownership, dan class assignment.
- Bookmark lintas domain, uniqueness, latest-first, dan re-check access.
- Knowledge media, gallery item, carousel/featured order, YouTube/social URL validation.
- Regulasi published dengan `active`, `amended`, `revoked`, `expired` tetap tampil publik.
- Regulation relation, versioning, changelog, PDF preview, PDF download, dan download log.
- `file_assets.path = NULL` tidak mengirim file/download.
- Pre-test satu attempt, quiz best attempt, revision assignment, post-test 60%, feedback, final grade, eligibility sertifikat.
- Certificate source uniqueness, verification, expiry, revocation.
- Statistik publik tidak memuat data personal ASN.
- Development seeder tidak mengubah master wilayah.
- Route produksi tidak merender `app/Views/reference/`.

---

# 14. Dokumentasi dan Data Reference

```text
database/
├── reference/
│   └── wilayah.sql
├── archive/
│   └── legacy/
└── sanitized-samples/

docs/
├── architecture/
├── database/
├── deployment/
├── api/
└── decisions/
```

Aturan:

- `database/reference/wilayah.sql` memuat master wilayah resmi dan bukan dummy data.
- Reference file tidak memuat data pribadi, password, token, encryption key, atau dump production.
- `database/sanitized-samples/` hanya berisi sample yang dianonimkan.

---

# 15. Routing Architecture

```text
/                               Landing Page publik
/login                          Login Main UI
/register                       Registrasi bertahap
/register/profile/*             Step profil sesuai tipe akun
/email/verify/*                 Verifikasi email
/password/*                     Pemulihan password

/arena-belajar/*                Katalog Arena Belajar publik
/bunga-rampai/*                 Knowledge/multimedia publik
/regulasi/*                     Repository regulasi publik
/berita/*                       Berita publik
/agenda/*                       Agenda dan event publik
/statistik-pbj                  Statistik publik agregat
/sertifikat/verifikasi/*        Verifikasi sertifikat publik

/workspace/*                    Workspace role operasional
/workspace/bookmarks/*          Bookmark personal user
/workspace/profile/*            Profil, change request, role request

/admin/login                    Login Dashboard UI
/admin/*                        Dashboard Super Admin dan Admin

/files/{id}/preview             Preview file PDF berizin
/files/{id}/download            Download file berizin

/learning/*                     Manajemen pembelajaran berbasis permission
/knowledge/*                    Manajemen Bunga Rampai/media berbasis permission
/regulation/*                   Manajemen regulasi/relasi berbasis permission
/communication/*                Manajemen berita/event berbasis permission
/certificates/*                 Manajemen sertifikat berbasis permission
/intelligence/*                 Government Intelligence berbasis permission
```

Route produksi tidak memakai path/nama halaman source template bawaan.

---

# 16. Keamanan dan Larangan Teknis

## Wajib

- Gunakan validation, authorization, CSRF, output escaping, Query Builder/prepared statement.
- Lindungi route sensitif melalui filter server-side dan service authorization.
- Terapkan audit pada approval, profile verification, role assignment, grading, sertifikat, file, dan export sensitif.
- Gunakan transaction untuk enrollment/quota, assessment submit, grading, profile verification, role assignment, approval, nomor sertifikat, dan event registration.
- Simpan file user pada `writable/uploads/` atau private object storage.
- Gunakan environment/secret manager untuk credential dan secret.
- Lindungi NIP/NIPPK, NIK, NIB, email, telepon, identifier SDM, serta dokumen bukti sebagai data restricted.
- Periksa lisensi source template/vendor sebelum produksi.

## Dilarang

- Merender `Views/reference/fistudy` atau `Views/reference/wowdash` dari route produksi.
- Menggunakan PHPMailer/sendemail demo FiStudy sebagai email production.
- Menyimpan upload user di `public/assets/`.
- Mengandalkan menu tersembunyi sebagai authorization.
- Menaruh query database atau logic bisnis berat pada view.
- Menaruh workflow lintas domain dalam controller.
- Menyimpan plaintext password, encryption key, token, NIP/NIK data production, atau dump production di Git.
- Mengizinkan seeder dummy mengubah master wilayah.
- Menghapus sertifikat, audit log, approval history, profile verification history, atau assignment submission history tanpa policy retention yang disetujui.

---

# 17. Ringkasan Arsitektur

```text
Pola aplikasi        : CodeIgniter 4 AppStarter monolith + feature/domain folder
Namespace utama      : App\...
Main UI              : FiStudy → Views/main + assets/main
Dashboard UI         : WowDash → Views/dashboard + assets/dashboard
Source FiStudy       : reference/fistudy (flat HTML + assets + js + inc; non-production)
Source WowDash       : reference/wowdash (flat HTML + assets; non-production)
Registrasi           : Multi-step, profile type, email verification, profile approval
ASN                  : Pemprov Lampung memakai organization_units; eksternal memakai instansi manual
Procurement role     : Opsional, multi-role, master dan riwayat terpisah
Bookmark             : Polymorphic, personal, latest-first, authorization-aware
Knowledge            : Artikel, FAQ, case, template, poster/flyer/infografik/gallery/video/link
Regulasi             : Semua published tampil; status hukum + relasi hukum + versioning PDF
File                 : Private storage, generic preview/download, path NULL = file belum tersedia
Assessment           : Pre-test, quiz best attempt, assignment revision, post-test 60%
Certificate          : Eligibility-based, unique source, verifiable, revocable
Database             : MySQL, migration, controlled seeder/import
Wilayah              : wilayah(kode, nama), data resmi, tidak disentuh dummy seeder
Public data          : Konten published, statistik agregat, verifikasi sertifikat
```

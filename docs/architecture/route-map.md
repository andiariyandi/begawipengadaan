# BegawiPBJ Route Map

Dokumen ini adalah kontrak route aplikasi BegawiPBJ berbasis CodeIgniter 4. Route dipisah antara Main UI (Landingpage + Workspace), Dashboard Admin, dan endpoint domain. Pemetaan ini adalah spesifikasi implementasi; controller/action yang disebut harus tersedia sebelum route diaktifkan.

## Konvensi

- Named route memakai pola `domain.resource.action`; `{id}` adalah bilangan positif dan `{slug}`/`{token}` divalidasi.
- GET tidak mengubah state. POST membuat/menjalankan aksi; PATCH/PUT mengubah; DELETE hanya untuk penghapusan yang secara kebijakan diizinkan.
- Form server-rendered dapat memakai POST dengan method spoofing CI4 untuk PATCH/DELETE.
- `G` = GuestFilter; `A` = AuthFilter + AccountStatusFilter; `D` = A + AdminFilter; `W` = A + WorkspaceFilter; `P:x` = PermissionFilter:x. Semua perubahan state memakai CSRF untuk session-authenticated browser.
- Visibilitas menu bukan pengganti izin. Filter group dan pemeriksaan ownership/penugasan pada service keduanya wajib.
- `public`, `workspace`, dan `dashboard` pada kolom UI merujuk layout Main atau Dashboard, bukan template reference.

## Landingpage publik

| Method | Path | Named route | Controller::action | Filter | UI / data |
|---|---|---|---|---|---|
| GET | `/` | `landing.home` | `Landing\HomeController::index` | Publik | Main/public/home; konten published |
| GET | `/arena-belajar` | `landing.learning.index` | `Landing\ArenaBelajarController::index` | Publik | Katalog program/course published |
| GET | `/arena-belajar/{slug}` | `landing.learning.show` | `Landing\ArenaBelajarController::show` | Publik | Detail course published |
| GET | `/bunga-rampai` | `landing.knowledge.index` | `Landing\BungaRampaiController::index` | Publik | Pencarian kategori/konten published |
| GET | `/bunga-rampai/{slug}` | `landing.knowledge.show` | `Landing\BungaRampaiController::show` | Publik | Detail artikel/studi kasus published; tipe URL harus tidak ambigu |
| GET | `/regulasi` | `landing.regulation.index` | `Landing\RegulationPublicController::index` | Publik | List regulasi published; status hukum terlihat |
| GET | `/regulasi/{id}` | `landing.regulation.show` | `Landing\RegulationPublicController::show` | Publik | Detail metadata dan versi yang published |
| GET | `/berita` | `landing.news.index` | `Landing\NewsPublicController::index` | Publik | List berita published |
| GET | `/berita/{slug}` | `landing.news.show` | `Landing\NewsPublicController::show` | Publik | Detail berita published |
| GET | `/agenda` | `landing.events.index` | `Landing\EventPublicController::index` | Publik | Agenda/event published |
| GET | `/agenda/{slug}` | `landing.events.show` | `Landing\EventPublicController::show` | Publik | Detail event; registrasi butuh login |
| GET | `/statistik-pbj` | `landing.statistics.index` | `Landing\StatisticPublicController::index` | Publik | Agregat, tanpa identitas individu |
| GET | `/sertifikat/verifikasi` | `landing.certificate.verify.form` | `Landing\CertificateVerificationController::index` | Rate limit | Form verifikasi |
| GET | `/sertifikat/verifikasi/{token}` | `landing.certificate.verify.result` | `Landing\CertificateVerificationController::show` | Rate limit | Status dan identitas minimum sesuai kebijakan |

Nama route khusus studi kasus, FAQ, dan template dapat diberi path tipe terpisah (`/bunga-rampai/studi-kasus/{slug}` dsb.) agar slug antarentitas tidak berbenturan. Route spesifik didaftarkan sebelum route wildcard.

## Autentikasi dan profil

| Method | Path | Named route | Controller::action | Filter | Hasil |
|---|---|---|---|---|---|
| GET | `/login` | `auth.login.form` | `Auth\LoginController::index` | G | Form Main/auth |
| POST | `/login` | `auth.login.submit` | `Auth\LoginController::login` | G + rate limit + CSRF | Session baru; redirect berdasarkan role |
| POST | `/logout` | `auth.logout` | `Auth\LoginController::logout` | A + CSRF | Session dihapus |
| GET | `/register` | `auth.register.form` | `Auth\RegisterController::index` | G | Form kategori profil |
| POST | `/register` | `auth.register.submit` | `Auth\RegisterController::store` | G + rate limit + CSRF | Akun pending |
| GET | `/password/forgot` | `auth.password.forgot` | `Auth\PasswordController::forgotForm` | G | Form permintaan reset |
| POST | `/password/forgot` | `auth.password.request` | `Auth\PasswordController::requestReset` | G + rate limit + CSRF | Respons tanpa enumerasi email |
| GET | `/password/reset/{token}` | `auth.password.reset.form` | `Auth\PasswordController::resetForm` | G + rate limit | Form reset |
| POST | `/password/reset` | `auth.password.reset.submit` | `Auth\PasswordController::reset` | G + rate limit + CSRF | Token sekali pakai |
| GET | `/profil` | `auth.profile.show` | `Auth\ProfileController::show` | A | Data diri milik sendiri |
| POST | `/profil` | `auth.profile.update` | `Auth\ProfileController::update` | A + CSRF | Perubahan profil sendiri |
| GET | `/api/wilayah` | `reference.wilayah.children` | `Reference\WilayahController::children` | Publik + rate limit | JSON; `parent` kode 2/5/8 karakter, anak menurut prefix dan panjang |

Verifikasi status akun dilakukan pada setiap request terlindungi, bukan hanya ketika login. Prioritas redirect multi-role harus ditetapkan jelas: Super Admin/Admin ke `/admin`, selain itu Workspace per role yang dipilih/ditugaskan.

## Workspace Main UI

| Method | Path | Named route | Controller::action | Filter | Scope |
|---|---|---|---|---|---|
| GET | `/workspace` | `workspace.index` | `Workspace\ParticipantController::redirectByRole` | W | Redirect aman ke workspace berizin |
| GET | `/workspace/peserta` | `workspace.participant.index` | `Workspace\ParticipantController::index` | W + role:participant | Hanya data sendiri |
| GET | `/workspace/peserta/kelas` | `workspace.participant.classes` | `Workspace\ParticipantController::myClasses` | W + role:participant | Enrollment sendiri |
| GET | `/workspace/peserta/kelas/{id}/belajar` | `workspace.participant.study` | `Learning\StudyController::show` | W + role:participant | Enrollment approved dan kelas cocok |
| POST | `/workspace/peserta/quiz/{id}/mulai` | `workspace.participant.quiz.start` | `Learning\QuizController::startAttempt` | W + role:participant + CSRF | Attempt limit/availability/ownership |
| POST | `/workspace/peserta/attempt/{id}/jawaban` | `workspace.participant.quiz.answer` | `Learning\QuizController::saveAnswer` | W + role:participant + CSRF | Attempt sendiri in_progress |
| POST | `/workspace/peserta/attempt/{id}/submit` | `workspace.participant.quiz.submit` | `Learning\QuizController::submitAttempt` | W + role:participant + CSRF | Idempotent submit |
| POST | `/workspace/peserta/tugas/{id}/submit` | `workspace.participant.assignment.submit` | `Learning\AssignmentController::submit` | W + role:participant + CSRF | Enrollment sendiri, deadline, file |
| GET | `/workspace/peserta/progres` | `workspace.participant.progress` | `Workspace\ParticipantController::progress` | W + role:participant | Progres sendiri |
| GET | `/workspace/peserta/sertifikat` | `workspace.participant.certificates` | `Workspace\ParticipantController::certificates` | W + role:participant | Sertifikat sendiri |
| GET | `/workspace/trainer` | `workspace.trainer.index` | `Workspace\TrainerController::index` | W + role:trainer | Kelas terdaftar pada class_trainers |
| GET | `/workspace/trainer/kelas/{id}` | `workspace.trainer.class.show` | `Workspace\TrainerController::showClass` | W + role:trainer | Ditugaskan pada kelas |
| GET | `/workspace/trainer/kelas/{id}/peserta` | `workspace.trainer.roster` | `Workspace\TrainerController::participants` | W + role:trainer | Data minimum peserta kelas |
| GET | `/workspace/evaluator` | `workspace.evaluator.index` | `Workspace\EvaluatorController::index` | W + role:evaluator | Kelas terdaftar pada class_evaluators |
| GET | `/workspace/evaluator/penilaian/{id}` | `workspace.evaluator.grade.form` | `Workspace\EvaluatorController::gradeForm` | W + role:evaluator | Submission kelas ditugaskan |
| POST | `/workspace/evaluator/penilaian/{id}` | `workspace.evaluator.grade.store` | `Learning\GradingController::gradeAssignment` | W + P:learning.assignment.grade + CSRF | Nilai 0..max_score; audit |
| GET | `/workspace/operator` | `workspace.operator.index` | `Workspace\OperatorController::index` | W + role:operator | Tugas delegasi |
| GET | `/workspace/operator/verifikasi` | `workspace.operator.registrations` | `Workspace\OperatorController::registrationQueue` | W + role:operator | Akun pending sesuai mandat |
| POST | `/workspace/operator/verifikasi/{id}` | `workspace.operator.registration.decide` | `Workspace\OperatorController::decideRegistration` | W + role:operator + CSRF | Approve/reject beralasan, audit |
| GET | `/workspace/operator/enrollment` | `workspace.operator.enrollments` | `Workspace\OperatorController::enrollmentQueue` | W + role:operator | Enrollment sesuai mandat |

Fitur manajemen Trainer/Operator yang berbagi controller domain tetap menggunakan layout Main dan memeriksa scope data pada service.

## Dashboard Admin

Semua `/admin/*` wajib memakai `D`. Permission tambahan per route seperti di bawah; Super Admin dapat memiliki seluruh izin melalui seed role-permission.

| Method | Path | Named route | Controller::action | Permission |
|---|---|---|---|---|
| GET | `/admin` | `admin.dashboard` | `Admin\DashboardController::index` | Izin akses dashboard administratif tersendiri `dashboard.view` |
| GET | `/admin/users` | `admin.users.index` | `Admin\UserController::index` | `users.view` |
| GET | `/admin/users/{id}` | `admin.users.show` | `Admin\UserController::show` | `users.view` |
| POST | `/admin/users` | `admin.users.store` | `Admin\UserController::store` | `users.create` |
| POST | `/admin/users/{id}` | `admin.users.update` | `Admin\UserController::update` | `users.update` |
| POST | `/admin/users/{id}/roles` | `admin.users.roles.assign` | `Admin\UserController::assignRole` | `users.assign_role` |
| GET | `/admin/roles` | `admin.roles.index` | `Admin\RoleController::index` | `roles.manage` |
| POST | `/admin/roles` | `admin.roles.store` | `Admin\RoleController::store` | `roles.manage` |
| GET | `/admin/permissions` | `admin.permissions.index` | `Admin\PermissionController::index` | `permissions.manage` |
| POST | `/admin/roles/{id}/permissions` | `admin.roles.permissions` | `Admin\RoleController::syncPermissions` | `permissions.manage` |
| GET | `/admin/approvals` | `admin.approvals.index` | `Admin\ApprovalController::index` | `approval.review` |
| POST | `/admin/approvals/{id}/decision` | `admin.approvals.decide` | `Admin\ApprovalController::decide` | `approval.review` + domain publish permission |
| GET | `/admin/audit` | `admin.audit.index` | `Admin\AuditController::index` | `audit.view` |
| GET | `/admin/organisasi` | `admin.organization.index` | `Admin\OrganizationController::index` | `intelligence.opd.manage` atau izin master OPD khusus |
| POST | `/admin/organisasi` | `admin.organization.store` | `Admin\OrganizationController::store` | `intelligence.opd.manage` |
| GET | `/admin/laporan` | `admin.reports.index` | `Admin\DashboardController::reports` | `report.export` untuk unduh; `dashboard.view` untuk tampilan |
| GET | `/admin/pengaturan` | `admin.settings.index` | `Admin\SettingController::index` | `settings.manage` |
| POST | `/admin/pengaturan` | `admin.settings.update` | `Admin\SettingController::update` | `settings.manage` |

`dashboard.view` harus masuk katalog permission dan role seed sebelum route `/admin` diaktifkan. Jangan memakai `intelligence.view` untuk dashboard administratif umum.

## Domain management

| Method | Prefix / Contoh | Controller | Permission | UI/scope |
|---|---|---|---|---|
| GET/POST | `/learning/programs` | `Learning\ProgramController` | `learning.program.manage` | Dashboard Admin atau Main Workspace Trainer sesuai actor |
| GET/POST | `/learning/courses` | `Learning\CourseController` | `learning.course.manage` | Owner/admin; publish butuh `learning.course.publish` |
| GET/POST | `/learning/classes` | `Learning\LearningClassController` | `learning.class.manage` | Class trainer assignment/admin |
| GET/POST | `/learning/modules` | `Learning\ModuleController` | `learning.lesson.manage` | Course/class scope |
| GET/POST | `/learning/lessons` | `Learning\LessonController` | `learning.lesson.manage` | Course/class scope |
| GET/POST | `/learning/quizzes` | `Learning\QuizController` | `learning.quiz.manage` | Trainer class/admin |
| GET/POST | `/learning/assignments` | `Learning\AssignmentController` | `learning.assignment.manage` | Trainer class/admin |
| POST | `/learning/enrollments/{id}/decision` | `Learning\EnrollmentController::decide` | `learning.enrollment.manage` | Operator delegate/trainer class/admin |
| POST | `/learning/grades/{id}/finalize` | `Learning\GradingController::finalize` | `learning.grade.finalize` | Evaluator assigned/admin; audit |
| GET/POST | `/knowledge/articles` | `Knowledge\ArticleController` | `knowledge.article.manage` | Owner/assigned/admin |
| GET/POST | `/knowledge/faqs` | `Knowledge\FaqController` | `knowledge.faq.manage` | Assigned/admin |
| GET/POST | `/knowledge/case-studies` | `Knowledge\CaseStudyController` | `knowledge.case_study.manage` | Assigned/admin |
| GET/POST | `/knowledge/templates` | `Knowledge\DocumentTemplateController` | `knowledge.template.manage` | Assigned/admin |
| GET/POST | `/regulation/records` | `Regulation\RegulationController` | `regulation.manage` | Assigned/admin; publish terpisah |
| POST | `/regulation/records/{id}/versions` | `Regulation\RegulationVersionController::store` | `regulation.version.upload` | File dan workflow |
| GET/POST | `/communication/news` | `Communication\NewsController` | `communication.news.manage` | Assigned/admin |
| GET/POST | `/communication/events` | `Communication\EventController` | `communication.event.manage` | Assigned/admin |
| POST | `/communication/events/{id}/register` | `Communication\EventRegistrationController::register` | A + CSRF; no admin permission | User sendiri, kuota/availability |
| POST | `/communication/events/{id}/attendance` | `Communication\AttendanceController::record` | `communication.event.attendance` | Operator/admin/assigned |
| GET/POST | `/certificates` | `Certificate\CertificateController` | `certificate.view` / `certificate.issue` | Scope penerima/admin |
| POST | `/certificates/{id}/revoke` | `Certificate\CertificateApprovalController::revoke` | `certificate.revoke` | Alasan, audit |
| GET | `/intelligence` | `Intelligence\IntelligenceDashboardController::index` | `intelligence.view` | Data internal |
| GET/POST | `/intelligence/jf-ppbj` | `Intelligence\JfPpbjController` | `intelligence.jf_ppbj.manage` | Biro PBJ/internal |
| GET/POST | `/intelligence/kompetensi` | `Intelligence\CompetencyCertificateController` | `intelligence.competency.manage` | Data internal |
| GET | `/intelligence/export` | `Intelligence\IntelligenceDashboardController::export` | `intelligence.export` | Audit dan filter |

Rincian tiap action CRUD ditambahkan ke route map bersamaan implementasinya; tidak boleh membuat route aktif yang controller/action atau permission-nya belum terdefinisi.

## Download file

| Method | Path | Controller::action | Guard |
|---|---|---|---|
| GET | `/files/{id}/download` | `Shared\FileController::download` | `path IS NOT NULL`, file ada, visibility, ownership, enrollment/assignment, permission |
| GET | `/certificates/{id}/download` | `Certificate\CertificateController::download` | Penerima atau admin berizin; sertifikat dan PDF tersedia |
| GET | `/regulasi/{id}/versi/{version}/download` | `Landing\RegulationPublicController::download` | Regulasi/versi published dan file ada; log unduhan |

`Shared\FileController` adalah controller yang perlu ditambahkan pada `app/Controllers/Shared/` jika endpoint sentral ini dipilih; alternatifnya download per domain melalui `FileStorageService` dengan guard setara. Hasil `path = NULL`: respons File belum tersedia (404 atau 409 sesuai kebijakan), tanpa membocorkan path server.

## Route testing minimum

- Guest ditolak dari `/workspace/*` dan `/admin/*`.
- Operator/Trainer/Evaluator/Peserta ditolak dari `/admin/*`.
- Admin tanpa izin spesifik ditolak oleh PermissionFilter.
- Trainer/Evaluator tidak dapat mengubah ID kelas lain.
- Peserta tidak dapat membuka enrollment/attempt/sertifikat milik user lain.
- Statistik publik tidak mengembalikan data pribadi.
- Reference FiStudy dan WowDash tidak memiliki route produksi.
- Method GET tidak menerbitkan sertifikat, membuat enrollment, atau mengubah nilai.

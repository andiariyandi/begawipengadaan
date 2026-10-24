# BegawiPengadaan — Route Contract

## Tujuan

Dokumen ini menetapkan kontrak route HTTP untuk aplikasi BegawiPengadaan. Kontrak mencakup path, method, controller, boundary antarmuka, filter, akses minimum, tujuan view/response, serta aturan penamaan route.

Dokumen ini berlaku untuk seluruh area proyek:

- Landing dan Public Area berbasis FiStudy.
- Auth terpadu berbasis WowDash.
- Workspace berbasis FiStudy.
- Dashboard Back-office berbasis WowDash.
- Aksi domain Learning, Knowledge, Regulation, Communication, Certificate, Intelligence, dan Shared.

Kontrak ini mengikuti arsitektur yang ditetapkan dalam `README.md` dan lokasi file pada `directory-tree.md`. Route tidak boleh dibuat di luar boundary atau prefix yang dinyatakan di dokumen ini tanpa memperbarui kontrak terlebih dahulu.

---

## Prinsip Route

- Route menggunakan URL bahasa Indonesia yang jelas, stabil, dan berorientasi pada domain bisnis.
- Route name memakai format dot notation berbahasa Inggris dan menggambarkan area/domain/action secara konsisten.
- Aksi yang mengubah state hanya menggunakan `POST`, `PUT`, `PATCH`, atau `DELETE`; route `GET` tidak boleh mengubah data.
- Semua request browser yang mengubah state wajib memakai CSRF protection.
- Filter, permission, ownership, assignment, account status, dan state workflow selalu diperiksa pada server-side.
- Menyembunyikan menu atau tombol tidak menggantikan authorization.
- Controller Landing hanya menyajikan data berstatus publik/dipublikasikan.
- Controller Workspace hanya menyajikan resource sesuai role, ownership, enrollment, atau assignment pengguna aktif.
- Controller Dashboard hanya dapat diakses melalui permission Dashboard yang sah.
- Download, preview, dan export selalu divalidasi pada server-side; URL tidak menjadi mekanisme authorization.
- Route tidak menggunakan nama file template reference seperti `index.html`, `course.html`, atau `table-data.html`.

---

## Konvensi Route Name

| Area | Prefix nama route | Contoh |
|---|---|---|
| Landing/Public | `public.*` | `public.home`, `public.regulations.index`, `public.news.index`, `public.events.index` |
| Auth | `dashboard.auth.*` | `dashboard.auth.login`, `dashboard.auth.register`, `dashboard.auth.register.address` |
| Workspace | `workspace.*` | `workspace.home`, `workspace.participant.classes`, `workspace.participant.attendance` |
| Dashboard Back-office | `dashboard.*` | `dashboard.home`, `dashboard.communication.events`, `dashboard.learning.classes` |
| Shared file | `public.file.*` / `shared.file.*` | `public.file.show`, `shared.file.preview`, `shared.file.download` |
| Shared export | `shared.export.*` | `shared.export.download` |

Route name `dashboard.auth.*` secara eksplisit membedakan Auth UI dari route Back-office `dashboard.*`.

---

## Filter dan Access Rule

| Filter/Rule | Fungsi |
|---|---|
| `guest` | Hanya pengguna yang belum terautentikasi; dipakai untuk login, registrasi, dan reset password |
| `auth` | Memerlukan session autentikasi yang valid |
| `verifiedEmail` | Memerlukan email yang telah diverifikasi bila route mensyaratkannya |
| `accountStatus:active` | Memerlukan account status `active` |
| `workspaceAccess` | Memastikan user aktif boleh masuk Workspace dan memiliki role Workspace yang relevan |
| `dashboardAccess` | Memastikan user memiliki dashboard permission yang sah |
| `permission:{key}` | Memastikan permission granular untuk aksi/resource tertentu |
| `role:{key}` | Membatasi route pada role tertentu bila diperlukan selain permission |
| `rateLimit:{profile}` | Rate limiting untuk endpoint sensitif atau publik |
| `auditContext` | Menetapkan konteks audit untuk aksi terautentikasi/administratif |

Filter adalah baseline. Controller, service, dan policy tetap wajib memeriksa ownership, assignment, workflow state, visibility, serta hubungan user dengan resource sebelum melakukan tindakan.

### Account status yang dapat mengakses area

| Area | Status minimum |
|---|---|
| Landing/Public | Tidak memerlukan autentikasi; data harus public/published |
| Auth | Guest atau user yang belum menyelesaikan lifecycle akun sesuai route |
| Workspace | `active`, email terverifikasi bila diwajibkan, dan role/assignment relevan |
| Dashboard Back-office | `active`, email terverifikasi, dan dashboard permission sah |
| File/Export restricted | `active` serta permission/ownership/assignment sesuai resource |

---

## Route Landing dan Public Area

Seluruh route pada bagian ini dirender menggunakan FiStudy di `app/Views/landing/` dan memakai asset dari `public/assets/main/`.

### Landing dan halaman umum

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/` | `public.home` | `Landing\HomeController::index` | — | `landing/home/index` |
| GET | `/tentang` | `public.about` | `Landing\HomeController::about` | — | `landing/pages/about` |
| GET | `/kontak` | `public.contact` | `Landing\HomeController::contact` | — | `landing/pages/contact` |
| POST | `/kontak` | `public.contact.send` | `Landing\HomeController::sendContact` | `rateLimit:contact` | Redirect + flash message; notification/log |
| GET | `/file/{segment}` | `public.file.show` | `Landing\FileController::show/$1` | — | Inline/download file publik terverifikasi |

### Arena Belajar publik

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/arena-belajar` | `public.learning.index` | `Landing\ArenaBelajarController::index` | — | `landing/learning/index` |
| GET | `/arena-belajar/program` | `public.learning.programs` | `Landing\ArenaBelajarController::programs` | — | `landing/learning/programs` |
| GET | `/arena-belajar/program/{slug}` | `public.learning.program.detail` | `Landing\ArenaBelajarController::programDetail` | — | `landing/learning/program_detail` |
| GET | `/arena-belajar/course` | `public.learning.courses` | `Landing\ArenaBelajarController::courses` | — | `landing/learning/courses` |
| GET | `/arena-belajar/course/{slug}` | `public.learning.course.detail` | `Landing\ArenaBelajarController::courseDetail` | — | `landing/learning/course_detail` |
| GET | `/arena-belajar/kelas` | `public.learning.classes` | `Landing\ArenaBelajarController::classes` | — | `landing/learning/classes` |
| GET | `/arena-belajar/kelas/{slug}` | `public.learning.class.detail` | `Landing\ArenaBelajarController::classDetail` | — | `landing/learning/class_detail` |
| POST | `/arena-belajar/kelas/{slug}/daftar` | — | `Landing\ArenaBelajarController::enroll` | `auth` | Redirect pendaftaran kelas |
| GET | `/arena-belajar/pengajar` | `public.learning.trainers` | `Landing\ArenaBelajarController::trainers` | — | `landing/learning/trainers` |
| GET | `/arena-belajar/pengajar/{slug}` | `public.learning.trainer.detail` | `Landing\ArenaBelajarController::trainerDetail` | — | `landing/learning/trainer_detail` |

Hanya program, course, class, atau profil pengajar yang memenuhi kebijakan visibility/publikasi yang boleh dirender pada route publik.

### Bunga Rampai publik

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/bunga-rampai` | `public.knowledge.index` | `Landing\BungaRampaiController::index` | — | `landing/knowledge/index` |
| GET | `/bunga-rampai/artikel` | `public.knowledge.articles` | `Landing\BungaRampaiController::articles` | — | `landing/knowledge/articles` |
| GET | `/bunga-rampai/artikel/{slug}` | `public.knowledge.article.detail` | `Landing\BungaRampaiController::articleDetail` | — | `landing/knowledge/article_detail` |
| GET | `/bunga-rampai/faq` | `public.knowledge.faqs` | `Landing\BungaRampaiController::faqs` | — | `landing/knowledge/faqs` |
| GET | `/bunga-rampai/studi-kasus` | `public.knowledge.case-studies` | `Landing\BungaRampaiController::caseStudies` | — | `landing/knowledge/case_studies` |
| GET | `/bunga-rampai/studi-kasus/{slug}` | `public.knowledge.case-study.detail` | `Landing\BungaRampaiController::caseStudyDetail` | — | `landing/knowledge/case_study_detail` |
| GET | `/bunga-rampai/template-dokumen` | `public.knowledge.templates` | `Landing\BungaRampaiController::templates` | — | `landing/knowledge/templates` |
| GET | `/bunga-rampai/template-dokumen/{slug}` | `public.knowledge.templates.detail` | `Landing\BungaRampaiController::templateDetail` | — | `landing/knowledge/template_detail` |
| GET | `/bunga-rampai/template-dokumen/{slug}/unduh` | `public.knowledge.templates.download` | `Landing\BungaRampaiController::templateDownload` | `rateLimit:download` | Download template publik |
| GET | `/bunga-rampai/media` | `public.knowledge.media` | `Landing\BungaRampaiController::media` | — | `landing/knowledge/media` |
| GET | `/bunga-rampai/video` | `public.knowledge.videos` | `Landing\BungaRampaiController::videos` | — | `landing/knowledge/videos` |
| GET | `/bunga-rampai/galeri` | `public.knowledge.gallery` | `Landing\BungaRampaiController::gallery` | — | `landing/knowledge/gallery` |

Route publik hanya boleh menampilkan konten dengan publication status dan visibility yang diizinkan.

### Regulasi publik

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/regulasi` | `public.regulations.index` | `Landing\RegulationPublicController::index` | — | `landing/regulations/index` |
| GET | `/regulasi/kategori/{slug}` | `public.regulations.category` | `Landing\RegulationPublicController::category` | — | `landing/regulations/index` (filtered) |
| GET | `/regulasi/{slug}/versi` | `public.regulations.versions` | `Landing\RegulationPublicController::versions` | — | `landing/regulations/versions` |
| GET | `/regulasi/{slug}/relasi` | `public.regulations.relations` | `Landing\RegulationPublicController::relations` | — | `landing/regulations/relations` |
| GET | `/regulasi/{slug}/preview` | `public.regulations.preview` | `Landing\RegulationPublicController::preview` | — | Inline PDF/Dokumen preview |
| GET | `/regulasi/{slug}/unduh` | `public.regulations.download` | `Landing\RegulationPublicController::download` | `rateLimit:download` | Download dokumen regulasi + increment counter |
| GET | `/regulasi/{slug}` | `public.regulations.detail` | `Landing\RegulationPublicController::detail` | — | `landing/regulations/detail` |

Hanya regulasi yang memenuhi `workflow_status = published` dan visibility publik dapat ditampilkan. Legal status `active`, `amended`, `revoked`, atau `expired` tidak dengan sendirinya menyembunyikan regulasi yang sudah dipublikasikan.

### Berita publik

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/berita` | `public.news.index` | `Landing\NewsPublicController::index` | — | `landing/news/index` (Top 5 Featured carousel, hit counter +1) |
| GET | `/berita/kategori/{slug}` | `public.news.category` | `Landing\NewsPublicController::category` | — | `landing/news/index` (kategori filter) |
| GET | `/berita/{slug}` | `public.news.detail` | `Landing\NewsPublicController::detail` | — | `landing/news/detail` (view counter increment) |

### Agenda dan event publik

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/event` | `public.events.index` | `Landing\EventPublicController::index` | — | `landing/events/index` |
| GET | `/event/agenda` | `public.events.agenda` | `Landing\EventPublicController::agenda` | — | `landing/events/agenda` |
| GET | `/event/kalender` | `public.events.calendar` | `Landing\EventPublicController::calendar` | — | `landing/events/calendar` |
| GET | `/event/{slug}` | `public.events.detail` | `Landing\EventPublicController::detail` | — | `landing/events/detail` |
| POST | `/event/{slug}/daftar` | `public.events.register` | `Landing\EventPublicController::register` | `auth` | Registrasi event (registered/waitlist) |

### Statistik publik

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/statistik` | `public.statistics.index` | `Landing\StatisticPublicController::index` | — | `landing/statistics/index` |
| GET | `/statistik/pembelajaran` | `public.statistics.learning` | `Landing\StatisticPublicController::learning` | — | `landing/statistics/learning` |
| GET | `/statistik/event` | `public.statistics.events` | `Landing\StatisticPublicController::events` | — | `landing/statistics/events` |
| GET | `/statistik/kompetensi` | `public.statistics.competency` | `Landing\StatisticPublicController::competency` | — | `landing/statistics/competency` |

### Verifikasi sertifikat publik

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/verifikasi-sertifikat` | `public.certificate.verify` | `Landing\CertificateVerificationController::index` | — | `landing/certificates/verify` (form cari nomor / hash) |
| GET/POST | `/verifikasi-sertifikat/hasil` | `public.certificate.verify.result` | `Landing\CertificateVerificationController::result` | `rateLimit:certificate-verify` | `landing/certificates/result` |
| GET | `/verifikasi-sertifikat/{token}` | `public.certificate.verify.detail` | `Landing\CertificateVerificationController::detail` | `rateLimit:certificate-verify` | `landing/certificates/detail` |
| GET | `/verify/certificate/{token}` | `public.certificate.verify.qr` | `Landing\CertificateVerificationController::detail` | `rateLimit:certificate-verify` | Quick QR scan verification lookup via SHA-256 hash |

### Bookmark Publik

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| POST | `/bookmark/toggle` | `public.bookmark.toggle` | `BookmarkController::toggle` | `auth` | JSON toggle response (pembelajaran, berita, event, regulasi, dokumen) |

---

## Route Auth Terpadu

Seluruh route ini memakai WowDash pada `app/Views/dashboard/auth/`, layout `app/Views/dashboard/auth/layouts/auth.php`, dan asset `public/assets/dashboard/`.

### Login dan logout

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/masuk` | `dashboard.auth.login` | `Dashboard\Auth\LoginController::index` | `guest`, `rateLimit:auth` | `dashboard/auth/login` |
| POST | `/dashboard/masuk` | `dashboard.auth.login.attempt` | `Dashboard\Auth\LoginController::attempt` | `guest`, `rateLimit:auth` | Redirect sesuai hasil autentikasi |
| POST | `/dashboard/keluar` | `dashboard.auth.logout` | `Dashboard\Auth\LogoutController::logout` | `auth`, `auditContext` | Session invalidated; redirect ke landing |

### Registrasi multi-step

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/daftar` | `dashboard.auth.register.account` | `Dashboard\Auth\RegisterController::account` | `guest`, `rateLimit:registration` | `dashboard/auth/register_account` |
| POST | `/dashboard/daftar` | `dashboard.auth.register.account.store` | `Dashboard\Auth\RegisterController::storeAccount` | `guest`, `rateLimit:registration` | Redirect ke wilayah |
| GET | `/dashboard/daftar/wilayah` | `dashboard.auth.register.region` | `Dashboard\Auth\RegisterController::region` | `guest` | `dashboard/auth/register_region` |
| POST | `/dashboard/daftar/wilayah` | `dashboard.auth.register.region.store` | `Dashboard\Auth\RegisterController::storeRegion` | `guest` | Redirect ke profil sesuai tipe |
| GET | `/dashboard/daftar/profil/{tipe}` | `dashboard.auth.register.profile` | `Dashboard\Auth\RegisterProfileController::form` | `guest` | Profil: ASN/Penyedia/Akademisi/Masyarakat |
| POST | `/dashboard/daftar/profil/{tipe}` | `dashboard.auth.register.profile.store` | `Dashboard\Auth\RegisterProfileController::store` | `guest` | Redirect ke tinjau |
| GET | `/dashboard/daftar/tinjau` | `dashboard.auth.register.review` | `Dashboard\Auth\RegisterController::review` | `guest` | `dashboard/auth/register_review` |
| POST | `/dashboard/daftar/tinjau` | `dashboard.auth.register.submit` | `Dashboard\Auth\RegisterController::submit` | `guest`, `rateLimit:registration` | Registrasi submitted; kirim verifikasi email |
| GET | `/dashboard/daftar/berhasil` | `dashboard.auth.register.success` | `Dashboard\Auth\RegisterController::success` | `guest` | `dashboard/auth/register_success` |

Mapping view profil `{tipe}`:

```text
asn       → dashboard/auth/register_asn
provider  → dashboard/auth/register_provider
academic  → dashboard/auth/register_academic
public    → dashboard/auth/register_public
```

Nilai parameter `{tipe}` harus divalidasi terhadap daftar yang diizinkan. Route tidak menerima tipe profil bebas.

### Verifikasi email dan status akun

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/verifikasi/{token}` | `dashboard.auth.verify-email` | `Dashboard\Auth\EmailVerificationController::verify` | `rateLimit:email-verify` | `dashboard/auth/verify_email` atau redirect hasil |
| POST | `/dashboard/verifikasi/kirim-ulang` | `dashboard.auth.verify-email.resend` | `Dashboard\Auth\EmailVerificationController::resend` | `rateLimit:email-verify` | Redirect + flash message |
| GET | `/dashboard/status-akun` | `dashboard.auth.account-status` | `Dashboard\Auth\AccountStatusController::show` | `auth` | View sesuai account status |

Mapping view status akun:

```text
pending_verification → dashboard/auth/account_pending
revision_required    → dashboard/auth/account_revision_required
rejected             → dashboard/auth/account_rejected
```

### Lupa dan reset password

| Method | Path | Route name | Controller | Filter | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/lupa-kata-sandi` | `dashboard.auth.password.forgot` | `Dashboard\Auth\PasswordController::forgotForm` | `guest`, `rateLimit:password-reset` | `dashboard/auth/forgot_password` |
| POST | `/dashboard/lupa-kata-sandi` | `dashboard.auth.password.send-reset` | `Dashboard\Auth\PasswordController::sendReset` | `guest`, `rateLimit:password-reset` | Redirect + generic success message |
| GET | `/dashboard/reset-kata-sandi/{token}` | `dashboard.auth.password.reset` | `Dashboard\Auth\PasswordController::resetForm` | `guest`, `rateLimit:password-reset` | `dashboard/auth/reset_password` |
| POST | `/dashboard/reset-kata-sandi/{token}` | `dashboard.auth.password.update` | `Dashboard\Auth\PasswordController::updatePassword` | `guest`, `rateLimit:password-reset` | `dashboard/auth/reset_password_success` |

Password reset response tidak boleh mengungkap apakah email tertentu terdaftar atau tidak. Token harus sekali pakai, memiliki kedaluwarsa, dan tidak dicatat sebagai plain token pada log/audit.

---

## Route Workspace

Seluruh route Workspace memakai FiStudy pada `app/Views/main/workspace/`, layout `app/Views/main/layouts/workspace.php`, dan asset `public/assets/main/`.

Baseline filter Workspace:

```text
auth
+ verifiedEmail bila route memerlukannya
+ accountStatus:active
+ workspaceAccess
+ auditContext untuk aksi perubahan state
```

### Workspace umum dan profil

| Method | Path | Route name | Controller | Akses minimum | View/response |
|---|---|---|---|---|---|
| GET | `/workspace` | `workspace.home` | `Workspace\WorkspaceHomeController::index` | Workspace access | `main/workspace/home/index` |
| GET | `/workspace/profil` | `workspace.profile.index` | `Workspace\ProfileController::index` | Workspace access | `main/workspace/profile/index` |
| GET | `/workspace/profil/akun` | `workspace.profile.account` | `Workspace\ProfileController::account` | Ownership | `main/workspace/profile/account` |
| PATCH | `/workspace/profil/akun` | `workspace.profile.account.update` | `Workspace\ProfileController::updateAccount` | Ownership + active | Redirect + audit |
| GET | `/workspace/profil/asn` | `workspace.profile.asn` | `Workspace\ProfileController::asn` | Profile type ASN + ownership | `main/workspace/profile/asn_profile` |
| GET | `/workspace/profil/penyedia` | `workspace.profile.provider` | `Workspace\ProfileController::provider` | Profile type provider + ownership | `main/workspace/profile/provider_profile` |
| GET | `/workspace/profil/akademik` | `workspace.profile.academic` | `Workspace\ProfileController::academic` | Profile type academic + ownership | `main/workspace/profile/academic_profile` |
| GET | `/workspace/profil/masyarakat` | `workspace.profile.public` | `Workspace\ProfileController::public` | Profile type public + ownership | `main/workspace/profile/public_profile` |
| GET | `/workspace/profil/peran-pengadaan` | `workspace.profile.procurement-roles` | `Workspace\ProfileController::procurementRoles` | Ownership | `main/workspace/profile/procurement_roles` |
| GET | `/workspace/profil/perubahan` | `workspace.profile.change-requests` | `Workspace\ProfileChangeRequestController::index` | Ownership | `main/workspace/profile/change_requests` |
| POST | `/workspace/profil/perubahan` | `workspace.profile.change-requests.store` | `Workspace\ProfileChangeRequestController::store` | Ownership + active | Create request + audit |
| GET | `/workspace/profil/pengajuan-role` | `workspace.profile.role-requests` | `Workspace\RoleRequestController::index` | Ownership | `main/workspace/profile/role_requests` |
| POST | `/workspace/profil/pengajuan-role` | `workspace.profile.role-requests.store` | `Workspace\RoleRequestController::store` | Ownership + active | Create request + audit |

### Bookmark dan notifikasi

| Method | Path | Route name | Controller | Akses minimum | View/response |
|---|---|---|---|---|---|
| GET | `/workspace/bookmark` | `workspace.bookmarks.index` | `Workspace\BookmarkController::index` | Workspace access | `main/workspace/bookmarks/index` |
| POST | `/workspace/bookmark/{type}/{id}` | `workspace.bookmarks.store` | `Workspace\BookmarkController::store` | Ownership + active | JSON/redirect |
| DELETE | `/workspace/bookmark/{type}/{id}` | `workspace.bookmarks.delete` | `Workspace\BookmarkController::delete` | Ownership + active | JSON/redirect |
| GET | `/workspace/notifikasi` | `workspace.notifications.index` | `Workspace\NotificationController::index` | Workspace access | `main/workspace/notifications/index` |
| PATCH | `/workspace/notifikasi/{id}/dibaca` | `workspace.notifications.read` | `Workspace\NotificationController::markRead` | Ownership | JSON/redirect |
| POST | `/workspace/notifikasi/baca-semua` | `workspace.notifications.read-all` | `Workspace\NotificationController::markAllRead` | Ownership | JSON/redirect |

Parameter bookmark `{type}` harus memakai whitelist domain yang diizinkan. Resource bookmark/notifikasi harus selalu dibatasi oleh ownership user aktif.

### Participant Workspace

| Method | Path | Route name | Controller | Akses minimum | View/response |
|---|---|---|---|---|---|
| GET | `/workspace/peserta` | `workspace.participant.dashboard` | `Workspace\ParticipantController::dashboard` | `role:participant` | `main/workspace/participant/dashboard` |
| GET | `/workspace/peserta/enrollment` | `workspace.participant.enrollments` | `Workspace\ParticipantController::enrollments` | `role:participant` | `main/workspace/participant/enrollments` |
| GET | `/workspace/peserta/pembelajaran/{enrollmentId}` | `workspace.participant.learning.show` | `Workspace\ParticipantController::learningDetail` | Enrollment ownership | `main/workspace/participant/learning_detail` |
| GET | `/workspace/peserta/belajar/{lessonId}` | `workspace.participant.study` | `Workspace\ParticipantController::study` | Enrollment + lesson access | `main/workspace/participant/study` |
| PATCH | `/workspace/peserta/belajar/{lessonId}/progres` | `workspace.participant.progress.update` | `Learning\LearningProgressController::update` | Enrollment ownership + lesson access | JSON + audit |
| GET | `/workspace/peserta/pre-test/{classId}` | `workspace.participant.pre-test` | `Learning\PreTestController::form` | Enrollment + assessment access | `main/workspace/participant/pre_test` |
| POST | `/workspace/peserta/pre-test/{classId}` | `workspace.participant.pre-test.submit` | `Learning\PreTestController::submit` | Enrollment + assessment access | Redirect result + audit |
| GET | `/workspace/peserta/pre-test/{classId}/hasil` | `workspace.participant.pre-test.result` | `Learning\PreTestController::result` | Enrollment ownership | `main/workspace/participant/pre_test_result` |
| GET | `/workspace/peserta/quiz/{quizId}` | `workspace.participant.quiz` | `Learning\QuizController::form` | Enrollment + quiz access | `main/workspace/participant/quiz` |
| POST | `/workspace/peserta/quiz/{quizId}` | `workspace.participant.quiz.submit` | `Learning\QuizController::submit` | Enrollment + quiz access | Redirect result + audit |
| GET | `/workspace/peserta/quiz/{quizId}/hasil` | `workspace.participant.quiz.result` | `Learning\QuizController::result` | Attempt ownership | `main/workspace/participant/quiz_result` |
| GET | `/workspace/peserta/tugas/{assignmentId}` | `workspace.participant.assignments.show` | `Learning\AssignmentController::show` | Enrollment + assignment access | `main/workspace/participant/assignment` |
| POST | `/workspace/peserta/tugas/{assignmentId}` | `workspace.participant.assignments.submit` | `Learning\AssignmentController::submit` | Enrollment + assignment access | `main/workspace/participant/assignment_submission` + audit |
| GET | `/workspace/peserta/tugas/{assignmentId}/pengumpulan` | `workspace.participant.assignments.submission` | `Learning\AssignmentController::submission` | Submission ownership | `main/workspace/participant/assignment_submission` |
| GET | `/workspace/peserta/feedback/{classId}` | `workspace.participant.feedback` | `Learning\FeedbackController::form` | Enrollment ownership | `main/workspace/participant/feedback` |
| POST | `/workspace/peserta/feedback/{classId}` | `workspace.participant.feedback.submit` | `Learning\FeedbackController::submit` | Enrollment ownership | Redirect + audit |
| GET | `/workspace/peserta/sertifikat` | `workspace.participant.certificates` | `Workspace\ParticipantController::certificates` | `role:participant` | `main/workspace/participant/certificates` |

### Trainer Workspace

| Method | Path | Route name | Controller | Akses minimum | View/response |
|---|---|---|---|---|---|
| GET | `/workspace/pelatih` | `workspace.trainer.dashboard` | `Workspace\TrainerController::dashboard` | `role:trainer` + assignment | `main/workspace/trainer/dashboard` |
| GET | `/workspace/pelatih/kelas` | `workspace.trainer.classes.index` | `Workspace\TrainerController::classes` | `role:trainer` | `main/workspace/trainer/class_list` |
| GET | `/workspace/pelatih/kelas/{classId}` | `workspace.trainer.classes.show` | `Workspace\TrainerController::classDetail` | Trainer assignment | `main/workspace/trainer/class_detail` |
| GET | `/workspace/pelatih/kelas/{classId}/materi/baru` | `workspace.trainer.lessons.create` | `Learning\LessonController::create` | Trainer assignment + `permission:learning.lesson.manage` | `main/workspace/trainer/lesson_form` |
| POST | `/workspace/pelatih/kelas/{classId}/materi` | `workspace.trainer.lessons.store` | `Learning\LessonController::store` | Trainer assignment + permission | Redirect + audit |
| GET | `/workspace/pelatih/kelas/{classId}/quiz/baru` | `workspace.trainer.quizzes.create` | `Learning\QuizController::create` | Trainer assignment + `permission:learning.quiz.manage` | `main/workspace/trainer/quiz_form` |
| POST | `/workspace/pelatih/kelas/{classId}/quiz` | `workspace.trainer.quizzes.store` | `Learning\QuizController::store` | Trainer assignment + permission | Redirect + audit |
| GET | `/workspace/pelatih/kelas/{classId}/tugas/baru` | `workspace.trainer.assignments.create` | `Learning\AssignmentController::create` | Trainer assignment + `permission:learning.assignment.manage` | `main/workspace/trainer/assignment_form` |
| POST | `/workspace/pelatih/kelas/{classId}/tugas` | `workspace.trainer.assignments.store` | `Learning\AssignmentController::store` | Trainer assignment + permission | Redirect + audit |
| GET | `/workspace/pelatih/kelas/{classId}/progres-peserta` | `workspace.trainer.participants.progress` | `Workspace\TrainerController::participantProgress` | Trainer assignment | `main/workspace/trainer/participant_progress` |

### Evaluator Workspace

| Method | Path | Route name | Controller | Akses minimum | View/response |
|---|---|---|---|---|---|
| GET | `/workspace/evaluator` | `workspace.evaluator.dashboard` | `Workspace\EvaluatorController::dashboard` | `role:evaluator` + assignment | `main/workspace/evaluator/dashboard` |
| GET | `/workspace/evaluator/antrian-penilaian` | `workspace.evaluator.assignments.queue` | `Workspace\EvaluatorController::assignmentQueue` | Evaluator assignment | `main/workspace/evaluator/assignment_queue` |
| GET | `/workspace/evaluator/pengumpulan/{submissionId}` | `workspace.evaluator.assignments.grade` | `Learning\GradeController::form` | Evaluator assignment | `main/workspace/evaluator/assignment_grade` |
| POST | `/workspace/evaluator/pengumpulan/{submissionId}/nilai` | `workspace.evaluator.assignments.grade.store` | `Learning\GradeController::store` | Evaluator assignment + `permission:learning.assignment.grade` | Redirect + audit |
| GET | `/workspace/evaluator/riwayat-penilaian` | `workspace.evaluator.assessments.history` | `Workspace\EvaluatorController::assessmentHistory` | `role:evaluator` | `main/workspace/evaluator/assessment_history` |

### Operator Workspace

| Method | Path | Route name | Controller | Akses minimum | View/response |
|---|---|---|---|---|---|
| GET | `/workspace/operator` | `workspace.operator.dashboard` | `Workspace\OperatorController::dashboard` | `role:operator` + scope | `main/workspace/operator/dashboard` |
| GET | `/workspace/operator/antrian-verifikasi` | `workspace.operator.verification.queue` | `Workspace\OperatorController::verificationQueue` | `permission:profile.verify` | `main/workspace/operator/verification_queue` |
| POST | `/workspace/operator/verifikasi/{requestId}` | `workspace.operator.verification.decide` | `Workspace\OperatorController::decideVerification` | Delegated approval + permission | Redirect + audit |
| GET | `/workspace/operator/enrollment` | `workspace.operator.enrollments` | `Workspace\OperatorController::enrollmentManagement` | `permission:learning.enrollment.manage` | `main/workspace/operator/enrollment_management` |
| GET | `/workspace/operator/absensi` | `workspace.operator.attendance` | `Workspace\OperatorController::attendanceManagement` | `permission:event.attendance.manage` | `main/workspace/operator/attendance_management` |
| GET | `/workspace/operator/approval-terdelegasi` | `workspace.operator.approvals` | `Workspace\OperatorController::delegatedApprovals` | Delegated approval scope | `main/workspace/operator/delegated_approvals` |

---

## Route Dashboard Back-office

Seluruh route Dashboard Back-office memakai WowDash di `app/Views/dashboard/` di luar `auth/`, layout `app/Views/dashboard/layouts/dashboard.php`, serta asset `public/assets/dashboard/`.

Baseline filter Dashboard:

```text
auth
+ verifiedEmail
+ accountStatus:active
+ dashboardAccess
+ permission:{key} sesuai aksi
+ auditContext untuk aksi perubahan state
```

### Dashboard, pengguna, dan RBAC

| Method | Path | Route name | Controller | Permission minimum | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard` | `dashboard.home` | `Dashboard\DashboardController::index` | `dashboard.view` | `dashboard/home/index` |
| GET | `/dashboard/pengguna` | `dashboard.users.index` | `Dashboard\UserController::index` | `user.view` | `dashboard/users/index` |
| GET | `/dashboard/pengguna/baru` | `dashboard.users.create` | `Dashboard\UserController::create` | `user.create` | `dashboard/users/create` |
| POST | `/dashboard/pengguna` | `dashboard.users.store` | `Dashboard\UserController::store` | `user.create` | Redirect + audit |
| GET | `/dashboard/pengguna/{userId}` | `dashboard.users.show` | `Dashboard\UserController::show` | `user.view` | `dashboard/users/detail` |
| GET | `/dashboard/pengguna/{userId}/ubah` | `dashboard.users.edit` | `Dashboard\UserController::edit` | `user.update` | `dashboard/users/edit` |
| PATCH | `/dashboard/pengguna/{userId}` | `dashboard.users.update` | `Dashboard\UserController::update` | `user.update` | Redirect + audit |
| GET | `/dashboard/pengguna/verifikasi-profil` | `dashboard.users.profile-verification-queue` | `Dashboard\UserProfileVerificationController::index` | `profile.verify` | `dashboard/users/profile_verification_queue` |
| POST | `/dashboard/pengguna/{userId}/verifikasi-profil` | `dashboard.users.profile-verification.decide` | `Dashboard\UserProfileVerificationController::decide` | `profile.verify` | Redirect + audit |
| GET | `/dashboard/pengguna/pengajuan-role` | `dashboard.role-requests.index` | `Dashboard\RoleRequestReviewController::index` | `role.request.review` | `dashboard/users/role_request_queue` |
| POST | `/dashboard/pengguna/pengajuan-role/{requestId}` | `dashboard.role-requests.decide` | `Dashboard\RoleRequestReviewController::decide` | `role.request.review` | Redirect + audit |
| GET | `/dashboard/role` | `dashboard.roles.index` | `Dashboard\RoleController::index` | `role.view` | `dashboard/rbac/roles` |
| GET | `/dashboard/role/baru` | `dashboard.roles.create` | `Dashboard\RoleController::create` | `role.create` | `dashboard/rbac/role_form` |
| POST | `/dashboard/role` | `dashboard.roles.store` | `Dashboard\RoleController::store` | `role.create` | Redirect + audit |
| GET | `/dashboard/role/{roleId}/ubah` | `dashboard.roles.edit` | `Dashboard\RoleController::edit` | `role.update` | `dashboard/rbac/role_form` |
| PATCH | `/dashboard/role/{roleId}` | `dashboard.roles.update` | `Dashboard\RoleController::update` | `role.update` | Redirect + audit |
| GET | `/dashboard/permission` | `dashboard.permissions.index` | `Dashboard\PermissionController::index` | `permission.view` | `dashboard/rbac/permissions` |
| GET | `/dashboard/permission/baru` | `dashboard.permissions.create` | `Dashboard\PermissionController::create` | `permission.create` | `dashboard/rbac/permission_form` |
| POST | `/dashboard/permission` | `dashboard.permissions.store` | `Dashboard\PermissionController::store` | `permission.create` | Redirect + audit |
| GET | `/dashboard/permission/{permissionId}/ubah` | `dashboard.permissions.edit` | `Dashboard\PermissionController::edit` | `permission.update` | `dashboard/rbac/permission_form` |
| PATCH | `/dashboard/permission/{permissionId}` | `dashboard.permissions.update` | `Dashboard\PermissionController::update` | `permission.update` | Redirect + audit |
| GET | `/dashboard/role-permission` | `dashboard.rbac.matrix` | `Dashboard\PermissionController::matrix` | `permission.assign` | `dashboard/rbac/role_permission_matrix` |
| PUT | `/dashboard/role/{roleId}/permission` | `dashboard.rbac.permissions.sync` | `Dashboard\PermissionController::syncRolePermissions` | `permission.assign` | Redirect + audit |

### Kelola Landing Page (CMS Terpadu)

Seluruh konten dinamis pada portal publik dikelola melalui single-menu `/dashboard/landing` dengan controller pada namespace `App\Controllers\Admin\Landing`.

| Method | Path | Route name | Controller | Permission minimum | Keterangan / View |
|---|---|---|---|---|---|
| GET | `/dashboard/landing` | `dashboard.landing.index` | Closure redirect | `landing.view` | Redirect ke `/dashboard/landing/slider` |
| GET | `/dashboard/landing/sorotan-carousel` | `dashboard.landing.spotlight_hub` | `SpotlightHubController::index` | `landing.view` | Pusat Sorotan (Badge Unggulan) & Hero Slider untuk 5 modul |
| POST | `/dashboard/landing/sorotan-carousel/toggle` | `dashboard.landing.spotlight_hub.toggle` | `SpotlightHubController::toggle` | `landing.slider.manage` | Toggle status Sorotan atau Slider per item |
| POST | `/dashboard/landing/sorotan-carousel/update-order` | `dashboard.landing.spotlight_hub.order` | `SpotlightHubController::updateOrder` | `landing.slider.manage` | Urutkan prioritas item carousel/slider |
| POST | `/dashboard/landing/sorotan-carousel/pengaturan` | `dashboard.landing.spotlight_hub.settings` | `SpotlightHubController::saveSettings` | `landing.slider.manage` | Simpan kuota hero dan mode slider per modul |
| GET | `/dashboard/landing/sorotan-carousel/search-available` | `dashboard.landing.spotlight_hub.search` | `SpotlightHubController::searchAvailable` | `landing.view` | Cari konten yang tersedia untuk ditambahkan ke sorotan |
| POST | `/dashboard/landing/sorotan-carousel/bulk-action` | `dashboard.landing.spotlight_hub.bulk` | `SpotlightHubController::bulkAction` | `landing.slider.manage` | Aksi massal aktifkan/nonaktifkan sorotan |
| GET | `/dashboard/landing/slider` | `dashboard.landing.sliders` | `SliderController::index` | `landing.view` | Daftar Banner Slider Beranda Utama |
| GET | `/dashboard/landing/slider/tambah` | `dashboard.landing.sliders.create` | `SliderController::create` | `landing.slider.manage` | Form tambah slider beranda |
| POST | `/dashboard/landing/slider/simpan` | `dashboard.landing.sliders.store` | `SliderController::store` | `landing.slider.manage` | Simpan slider baru |
| GET | `/dashboard/landing/slider/ubah/{id}` | `dashboard.landing.sliders.edit` | `SliderController::edit` | `landing.slider.manage` | Form ubah banner slider |
| POST | `/dashboard/landing/slider/ubah/{id}` | `dashboard.landing.sliders.update` | `SliderController::update` | `landing.slider.manage` | Simpan pembaruan slider |
| POST/GET | `/dashboard/landing/slider/duplikat/{id}` | `dashboard.landing.sliders.duplicate` | `SliderController::duplicate` | `landing.slider.manage` | Duplikasi banner slider |
| POST | `/dashboard/landing/slider/update-order` | `dashboard.landing.sliders.order` | `SliderController::updateOrder` | `landing.slider.manage` | Urutkan posisi slider beranda |
| POST/GET | `/dashboard/landing/slider/toggle/{id}` | `dashboard.landing.sliders.toggle` | `SliderController::toggle` | `landing.slider.manage` | Toggle aktif/nonaktif banner slider |
| POST/GET | `/dashboard/landing/slider/hapus/{id}` | `dashboard.landing.sliders.delete` | `SliderController::delete` | `landing.slider.manage` | Hapus banner slider |
| GET | `/dashboard/landing/pengumuman` | `dashboard.landing.announcements` | `AnnouncementController::index` | `landing.view` | Daftar Popup Pengumuman & Promo |
| GET | `/dashboard/landing/pengumuman/tambah` | `dashboard.landing.announcements.create` | `AnnouncementController::create` | `landing.announcement.manage` | Form tambah pengumuman |
| POST | `/dashboard/landing/pengumuman/simpan` | `dashboard.landing.announcements.store` | `AnnouncementController::store` | `landing.announcement.manage` | Simpan pengumuman baru |
| GET | `/dashboard/landing/pengumuman/ubah/{id}` | `dashboard.landing.announcements.edit` | `AnnouncementController::edit` | `landing.announcement.manage` | Form ubah pengumuman |
| POST | `/dashboard/landing/pengumuman/ubah/{id}` | `dashboard.landing.announcements.update` | `AnnouncementController::update` | `landing.announcement.manage` | Simpan pembaruan pengumuman |
| POST/GET | `/dashboard/landing/pengumuman/toggle/{id}` | `dashboard.landing.announcements.toggle` | `AnnouncementController::toggle` | `landing.announcement.manage` | Toggle status aktif popup pengumuman |
| POST/GET | `/dashboard/landing/pengumuman/hapus/{id}` | `dashboard.landing.announcements.delete` | `AnnouncementController::delete` | `landing.announcement.manage` | Hapus popup pengumuman |
| POST | `/dashboard/landing/pengumuman/pengaturan` | `dashboard.landing.announcements.settings` | `AnnouncementController::saveSettings` | `landing.announcement.manage` | Konfigurasi global popup pengumuman |
| GET | `/dashboard/landing/profil-lembaga` | `dashboard.landing.profile` | `InstitutionProfileController::index` | `landing.view` | Form Profil Lembaga, Visi Misi, Filosofi |
| POST | `/dashboard/landing/profil-lembaga` | `dashboard.landing.profile.save` | `InstitutionProfileController::save` | `landing.profile.manage` | Simpan profil lembaga |
| POST/GET | `/dashboard/landing/profil-lembaga/reset` | `dashboard.landing.profile.reset` | `InstitutionProfileController::resetDefault` | `landing.profile.manage` | Reset profil ke default sistem |
| GET | `/dashboard/landing/pilar-hierarki` | `dashboard.landing.highlights` | `HighlightFeatureController::index` | `landing.view` | Kelola Pilar PATIK, Hierarki, Roadmap |
| GET | `/dashboard/landing/pilar-hierarki/tambah` | `dashboard.landing.highlights.create` | `HighlightFeatureController::create` | `landing.profile.manage` | Form tambah pilar fitur |
| POST | `/dashboard/landing/pilar-hierarki/simpan` | `dashboard.landing.highlights.store` | `HighlightFeatureController::store` | `landing.profile.manage` | Simpan pilar fitur |
| GET | `/dashboard/landing/pilar-hierarki/ubah/{id}` | `dashboard.landing.highlights.edit` | `HighlightFeatureController::edit` | `landing.profile.manage` | Form ubah pilar fitur |
| POST | `/dashboard/landing/pilar-hierarki/ubah/{id}` | `dashboard.landing.highlights.update` | `HighlightFeatureController::update` | `landing.profile.manage` | Simpan pembaruan pilar fitur |
| POST/GET | `/dashboard/landing/pilar-hierarki/toggle/{id}` | `dashboard.landing.highlights.toggle` | `HighlightFeatureController::toggle` | `landing.profile.manage` | Toggle aktif pilar fitur |
| POST/GET | `/dashboard/landing/pilar-hierarki/hapus/{id}` | `dashboard.landing.highlights.delete` | `HighlightFeatureController::delete` | `landing.profile.manage` | Hapus pilar fitur |
| GET | `/dashboard/landing/portal-eksternal` | `dashboard.landing.portals` | `ExternalPortalController::index` | `landing.view` | Kelola Tautan Portal Eksternal PBJ |
| GET | `/dashboard/landing/portal-eksternal/tambah` | `dashboard.landing.portals.create` | `ExternalPortalController::create` | `landing.portal.manage` | Form tambah portal eksternal |
| POST | `/dashboard/landing/portal-eksternal/simpan` | `dashboard.landing.portals.store` | `ExternalPortalController::store` | `landing.portal.manage` | Simpan portal eksternal baru |
| GET | `/dashboard/landing/portal-eksternal/ubah/{id}` | `dashboard.landing.portals.edit` | `ExternalPortalController::edit` | `landing.portal.manage` | Form ubah portal eksternal |
| POST | `/dashboard/landing/portal-eksternal/ubah/{id}` | `dashboard.landing.portals.update` | `ExternalPortalController::update` | `landing.portal.manage` | Simpan pembaruan portal eksternal |
| POST/GET | `/dashboard/landing/portal-eksternal/toggle/{id}` | `dashboard.landing.portals.toggle` | `ExternalPortalController::toggle` | `landing.portal.manage` | Toggle status aktif portal |
| POST/GET | `/dashboard/landing/portal-eksternal/hapus/{id}` | `dashboard.landing.portals.delete` | `ExternalPortalController::delete` | `landing.portal.manage` | Hapus portal eksternal |
| POST/GET | `/dashboard/landing/portal-eksternal/muat-rekomendasi` | `dashboard.landing.portals.recommendations` | `ExternalPortalController::seedRecommendations` | `landing.portal.manage` | Muat rekomendasi portal eksternal resmi |
| GET | `/dashboard/landing/testimoni` | `dashboard.landing.testimonials` | `TestimonialController::index` | `landing.view` | Daftar Testimoni Pengguna |
| GET | `/dashboard/landing/testimoni/tambah` | `dashboard.landing.testimonials.create` | `TestimonialController::create` | `landing.team.manage` | Form tambah testimoni |
| POST | `/dashboard/landing/testimoni/simpan` | `dashboard.landing.testimonials.store` | `TestimonialController::store` | `landing.team.manage` | Simpan testimoni baru |
| GET | `/dashboard/landing/testimoni/ubah/{id}` | `dashboard.landing.testimonials.edit` | `TestimonialController::edit` | `landing.team.manage` | Form ubah testimoni |
| POST | `/dashboard/landing/testimoni/ubah/{id}` | `dashboard.landing.testimonials.update` | `TestimonialController::update` | `landing.team.manage` | Simpan pembaruan testimoni |
| POST/GET | `/dashboard/landing/testimoni/toggle/{id}` | `dashboard.landing.testimonials.toggle` | `TestimonialController::toggle` | `landing.team.manage` | Toggle aktif testimoni |
| POST/GET | `/dashboard/landing/testimoni/hapus/{id}` | `dashboard.landing.testimonials.delete` | `TestimonialController::delete` | `landing.team.manage` | Hapus testimoni |
| POST/GET | `/dashboard/landing/testimoni/muat-rekomendasi` | `dashboard.landing.testimonials.recommendations` | `TestimonialController::seedRecommendations` | `landing.team.manage` | Muat rekomendasi testimoni resmi |
| GET | `/dashboard/landing/tim` | `dashboard.landing.team` | `TeamMemberController::index` | `landing.view` | Kelola Tim Inovator & Pengembang |
| GET | `/dashboard/landing/tim/tambah` | `dashboard.landing.team.create` | `TeamMemberController::create` | `landing.team.manage` | Form tambah anggota tim |
| POST | `/dashboard/landing/tim/simpan` | `dashboard.landing.team.store` | `TeamMemberController::store` | `landing.team.manage` | Simpan anggota tim baru |
| GET | `/dashboard/landing/tim/ubah/{id}` | `dashboard.landing.team.edit` | `TeamMemberController::edit` | `landing.team.manage` | Form ubah data tim |
| POST | `/dashboard/landing/tim/ubah/{id}` | `dashboard.landing.team.update` | `TeamMemberController::update` | `landing.team.manage` | Simpan pembaruan tim |
| POST/GET | `/dashboard/landing/tim/toggle/{id}` | `dashboard.landing.team.toggle` | `TeamMemberController::toggle` | `landing.team.manage` | Toggle status aktif anggota tim |
| POST/GET | `/dashboard/landing/tim/hapus/{id}` | `dashboard.landing.team.delete` | `TeamMemberController::delete` | `landing.team.manage` | Hapus anggota tim |
| POST/GET | `/dashboard/landing/tim/muat-rekomendasi` | `dashboard.landing.team.recommendations` | `TeamMemberController::seedRecommendations` | `landing.team.manage` | Muat rekomendasi susunan tim |

*Catatan Redirection Rute Usang:*
- `/dashboard/landing/banner-halaman` dan `/dashboard/landing/carousel` dialihkan permanen (301/302) ke `/dashboard/landing/sorotan-carousel`.
- `/dashboard/landing/kategori-layanan` dialihkan permanen ke `/dashboard/landing`.

### Approval, audit, organisasi, laporan, dan pengaturan

| Method | Path | Route name | Controller | Permission minimum | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/persetujuan` | `dashboard.approvals.index` | `Dashboard\ApprovalController::index` | `approval.view` | `dashboard/approvals/index` |
| GET | `/dashboard/persetujuan/{approvalId}` | `dashboard.approvals.show` | `Dashboard\ApprovalController::show` | `approval.view` | `dashboard/approvals/detail` |
| POST | `/dashboard/persetujuan/{approvalId}/setujui` | `dashboard.approvals.approve` | `Dashboard\ApprovalController::approve` | `approval.decide` | Redirect + audit |
| POST | `/dashboard/persetujuan/{approvalId}/tolak` | `dashboard.approvals.reject` | `Dashboard\ApprovalController::reject` | `approval.decide` | Redirect + audit |
| POST | `/dashboard/persetujuan/{approvalId}/minta-revisi` | `dashboard.approvals.revision` | `Dashboard\ApprovalController::requestRevision` | `approval.decide` | Redirect + audit |
| GET | `/dashboard/audit` | `dashboard.audits.index` | `Dashboard\AuditController::index` | `audit.view` | `dashboard/audits/index` |
| GET | `/dashboard/audit/{auditId}` | `dashboard.audits.show` | `Dashboard\AuditController::show` | `audit.view` | `dashboard/audits/detail` |
| GET | `/dashboard/organisasi` | `dashboard.organizations.index` | `Dashboard\OrganizationController::index` | `organization.view` | `dashboard/organizations/index` |
| GET | `/dashboard/organisasi/baru` | `dashboard.organizations.create` | `Dashboard\OrganizationController::create` | `organization.create` | `dashboard/organizations/form` |
| POST | `/dashboard/organisasi` | `dashboard.organizations.store` | `Dashboard\OrganizationController::store` | `organization.create` | Redirect + audit |
| GET | `/dashboard/organisasi/{organizationId}/ubah` | `dashboard.organizations.edit` | `Dashboard\OrganizationController::edit` | `organization.update` | `dashboard/organizations/form` |
| PATCH | `/dashboard/organisasi/{organizationId}` | `dashboard.organizations.update` | `Dashboard\OrganizationController::update` | `organization.update` | Redirect + audit |
| GET | `/dashboard/laporan` | `dashboard.reports.index` | `Dashboard\InternalReportController::index` | `report.view` | `dashboard/reports/index` |
| GET | `/dashboard/laporan/statistik-internal` | `dashboard.reports.statistics` | `Dashboard\InternalReportController::internalStatistics` | `report.statistics.view` | `dashboard/reports/internal_statistics` |
| GET | `/dashboard/pengaturan` | `dashboard.settings.index` | `Dashboard\SettingController::index` | `setting.view` | `dashboard/settings/index` |
| GET | `/dashboard/pengaturan/aplikasi` | `dashboard.settings.application` | `Dashboard\SettingController::application` | `setting.application.update` | `dashboard/settings/application` |
| PATCH | `/dashboard/pengaturan/aplikasi` | `dashboard.settings.application.update` | `Dashboard\SettingController::updateApplication` | `setting.application.update` | Redirect + audit |
| GET | `/dashboard/pengaturan/notifikasi` | `dashboard.settings.notification` | `Dashboard\SettingController::notification` | `setting.notification.update` | `dashboard/settings/notification` |
| PATCH | `/dashboard/pengaturan/notifikasi` | `dashboard.settings.notification.update` | `Dashboard\SettingController::updateNotification` | `setting.notification.update` | Redirect + audit |
| GET | `/dashboard/pengaturan/storage` | `dashboard.settings.storage` | `Dashboard\SettingController::storage` | `setting.storage.update` | `dashboard/settings/storage` |
| PATCH | `/dashboard/pengaturan/storage` | `dashboard.settings.storage.update` | `Dashboard\SettingController::updateStorage` | `setting.storage.update` | Redirect + audit |
| GET | `/dashboard/pengaturan/keamanan` | `dashboard.settings.security` | `Dashboard\SettingController::security` | `setting.security.update` | `dashboard/settings/security` |
| PATCH | `/dashboard/pengaturan/keamanan` | `dashboard.settings.security.update` | `Dashboard\SettingController::updateSecurity` | `setting.security.update` | Redirect + audit |
| GET | `/dashboard/kesehatan-sistem` | `dashboard.system-health` | `Dashboard\SystemHealthController::index` | `system.health.view` | System health response |

### Learning Back-office

| Method | Path | Route name | Controller | Permission minimum | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/pembelajaran/program` | `dashboard.learning.programs.index` | `Learning\ProgramController::index` | `learning.program.view` | `dashboard/learning/programs` |
| GET | `/dashboard/pembelajaran/program/baru` | `dashboard.learning.programs.create` | `Learning\ProgramController::create` | `learning.program.create` | `dashboard/learning/program_form` |
| POST | `/dashboard/pembelajaran/program` | `dashboard.learning.programs.store` | `Learning\ProgramController::store` | `learning.program.create` | Redirect + audit |
| GET | `/dashboard/pembelajaran/program/{programId}/ubah` | `dashboard.learning.programs.edit` | `Learning\ProgramController::edit` | `learning.program.update` | `dashboard/learning/program_form` |
| PATCH | `/dashboard/pembelajaran/program/{programId}` | `dashboard.learning.programs.update` | `Learning\ProgramController::update` | `learning.program.update` | Redirect + audit |
| GET | `/dashboard/pembelajaran/course` | `dashboard.learning.courses.index` | `Learning\CourseController::index` | `learning.course.view` | `dashboard/learning/courses` |
| GET | `/dashboard/pembelajaran/course/baru` | `dashboard.learning.courses.create` | `Learning\CourseController::create` | `learning.course.create` | `dashboard/learning/course_form` |
| POST | `/dashboard/pembelajaran/course` | `dashboard.learning.courses.store` | `Learning\CourseController::store` | `learning.course.create` | Redirect + audit |
| GET | `/dashboard/pembelajaran/course/{courseId}/ubah` | `dashboard.learning.courses.edit` | `Learning\CourseController::edit` | `learning.course.update` | `dashboard/learning/course_form` |
| PATCH | `/dashboard/pembelajaran/course/{courseId}` | `dashboard.learning.courses.update` | `Learning\CourseController::update` | `learning.course.update` | Redirect + audit |
| GET | `/dashboard/pembelajaran/kelas` | `dashboard.learning.classes.index` | `Learning\LearningClassController::index` | `learning.class.view` | `dashboard/learning/classes` |
| GET | `/dashboard/pembelajaran/kelas/baru` | `dashboard.learning.classes.create` | `Learning\LearningClassController::create` | `learning.class.create` | `dashboard/learning/class_form` |
| POST | `/dashboard/pembelajaran/kelas` | `dashboard.learning.classes.store` | `Learning\LearningClassController::store` | `learning.class.create` | Redirect + audit |
| GET | `/dashboard/pembelajaran/kelas/{classId}/ubah` | `dashboard.learning.classes.edit` | `Learning\LearningClassController::edit` | `learning.class.update` | `dashboard/learning/class_form` |
| PATCH | `/dashboard/pembelajaran/kelas/{classId}` | `dashboard.learning.classes.update` | `Learning\LearningClassController::update` | `learning.class.update` | Redirect + audit |
| GET | `/dashboard/pembelajaran/kelas/{classId}/modul` | `dashboard.learning.modules.index` | `Learning\ModuleController::index` | `learning.module.view` | `dashboard/learning/modules` |
| GET | `/dashboard/pembelajaran/kelas/{classId}/materi` | `dashboard.learning.lessons.index` | `Learning\LessonController::index` | `learning.lesson.view` | `dashboard/learning/lessons` |
| GET | `/dashboard/pembelajaran/enrollment` | `dashboard.learning.enrollments.index` | `Learning\EnrollmentController::index` | `learning.enrollment.view` | `dashboard/learning/enrollment_management` |
| GET | `/dashboard/pembelajaran/nilai` | `dashboard.learning.grades.index` | `Learning\GradeController::index` | `learning.grade.view` | `dashboard/learning/grades` |
| GET | `/dashboard/pembelajaran/laporan` | `dashboard.learning.reports` | `Learning\LearningProgressController::report` | `learning.report.view` | `dashboard/learning/learning_reports` |

### Knowledge Back-office

| Method | Path | Route name | Controller | Permission minimum | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/bunga-rampai/artikel` | `dashboard.knowledge.articles.index` | `Knowledge\KnowledgeArticleController::index` | `knowledge.article.view` | `dashboard/knowledge/articles` |
| GET | `/dashboard/bunga-rampai/artikel/baru` | `dashboard.knowledge.articles.create` | `Knowledge\KnowledgeArticleController::create` | `knowledge.article.create` | `dashboard/knowledge/article_form` |
| POST | `/dashboard/bunga-rampai/artikel` | `dashboard.knowledge.articles.store` | `Knowledge\KnowledgeArticleController::store` | `knowledge.article.create` | Redirect + audit |
| GET | `/dashboard/bunga-rampai/artikel/{articleId}/ubah` | `dashboard.knowledge.articles.edit` | `Knowledge\KnowledgeArticleController::edit` | `knowledge.article.update` | `dashboard/knowledge/article_form` |
| PATCH | `/dashboard/bunga-rampai/artikel/{articleId}` | `dashboard.knowledge.articles.update` | `Knowledge\KnowledgeArticleController::update` | `knowledge.article.update` | Redirect + audit |
| GET | `/dashboard/bunga-rampai/faq` | `dashboard.knowledge.faq.index` | `Knowledge\KnowledgeFaqController::index` | `knowledge.faq.view` | `dashboard/knowledge/faq` |
| GET | `/dashboard/bunga-rampai/media` | `dashboard.knowledge.media.index` | `Knowledge\KnowledgeMediaController::index` | `knowledge.media.view` | `dashboard/knowledge/media` |
| GET | `/dashboard/bunga-rampai/kategori` | `dashboard.knowledge.categories.index` | `Knowledge\KnowledgeCategoryController::index` | `knowledge.category.view` | `dashboard/knowledge/categories` |
| GET | `/dashboard/bunga-rampai/antrian-publikasi` | `dashboard.knowledge.publication-queue` | `Knowledge\KnowledgeArticleController::publicationQueue` | `knowledge.publish.review` | `dashboard/knowledge/publication_queue` |

### Regulasi Back-office

| Method | Path | Route name | Controller | Permission minimum | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/regulasi` | `dashboard.regulations.index` | `Regulation\RegulationController::index` | `regulation.view` | `dashboard/regulations/index` |
| GET | `/dashboard/regulasi/baru` | `dashboard.regulations.create` | `Regulation\RegulationController::create` | `regulation.create` | `dashboard/regulations/form` |
| POST | `/dashboard/regulasi` | `dashboard.regulations.store` | `Regulation\RegulationController::store` | `regulation.create` | Redirect + audit |
| GET | `/dashboard/regulasi/{regulationId}/ubah` | `dashboard.regulations.edit` | `Regulation\RegulationController::edit` | `regulation.update` | `dashboard/regulations/form` |
| PATCH | `/dashboard/regulasi/{regulationId}` | `dashboard.regulations.update` | `Regulation\RegulationController::update` | `regulation.update` | Redirect + audit |
| GET | `/dashboard/regulasi/{regulationId}/versi` | `dashboard.regulations.versions` | `Regulation\RegulationVersionController::index` | `regulation.version.view` | `dashboard/regulations/versions` |
| GET | `/dashboard/regulasi/{regulationId}/relasi` | `dashboard.regulations.relations` | `Regulation\RegulationRelationController::index` | `regulation.relation.view` | `dashboard/regulations/relations` |
| GET | `/dashboard/regulasi/antrian-publikasi` | `dashboard.regulations.publication-queue` | `Regulation\RegulationController::publicationQueue` | `regulation.publish.review` | `dashboard/regulations/publication_queue` |

### Berita dan Event Back-office

| Method | Path | Route name | Controller | Permission minimum | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/komunikasi/berita` | `dashboard.communication.news` | `Communication\NewsController::index` | `news.view` | `dashboard/communication/news` |
| GET | `/dashboard/komunikasi/berita/tambah` | `dashboard.communication.news.create` | `Communication\NewsController::create` | `news.create` | `dashboard/communication/news_form` |
| POST | `/dashboard/komunikasi/berita/tambah` | — | `Communication\NewsController::store` | `news.create` | Redirect + audit |
| GET | `/dashboard/komunikasi/berita/{newsId}/ubah` | `dashboard.communication.news.edit` | `Communication\NewsController::edit` | `news.update` | `dashboard/communication/news_form` |
| POST | `/dashboard/komunikasi/berita/{newsId}/ubah` | — | `Communication\NewsController::update` | `news.update` | Redirect + audit |
| GET | `/dashboard/komunikasi/berita/{newsId}` | `dashboard.communication.news.detail` | `Communication\NewsController::detail` | `news.view` | `dashboard/communication/news_detail` |
| POST | `/dashboard/komunikasi/berita/{newsId}/hapus` | `dashboard.communication.news.delete` | `Communication\NewsController::delete` | `news.delete` | Redirect + audit |
| GET | `/dashboard/komunikasi/kategori-berita` | `dashboard.communication.news-categories` | `Communication\NewsController::categories` | `news.view` | `dashboard/communication/categories` |
| GET | `/dashboard/komunikasi/kategori-berita/tambah` | `dashboard.communication.news-categories.create` | `Communication\NewsController::categoryCreate` | `news.create` | `dashboard/communication/category_form` |
| POST | `/dashboard/komunikasi/kategori-berita/tambah` | — | `Communication\NewsController::categoryStore` | `news.create` | Redirect + audit |
| GET | `/dashboard/komunikasi/kategori-berita/{id}/ubah` | `dashboard.communication.news-categories.edit` | `Communication\NewsController::categoryEdit` | `news.update` | `dashboard/communication/category_form` |
| POST | `/dashboard/komunikasi/kategori-berita/{id}/ubah` | — | `Communication\NewsController::categoryUpdate` | `news.update` | Redirect + audit |
| POST | `/dashboard/komunikasi/kategori-berita/{id}/hapus` | — | `Communication\NewsController::categoryDelete` | `news.delete` | Redirect + audit |
| GET | `/dashboard/komunikasi/event-agenda` | `dashboard.communication.events` | `Communication\EventController::index` | `event.view` | `dashboard/communication/events` |
| GET | `/dashboard/komunikasi/event-agenda/tambah` | `dashboard.communication.events.create` | `Communication\EventController::create` | `event.create` | `dashboard/communication/event_form` |
| POST | `/dashboard/komunikasi/event-agenda/tambah` | — | `Communication\EventController::store` | `event.create` | Redirect + audit |
| GET | `/dashboard/komunikasi/event-agenda/{eventId}/ubah` | `dashboard.communication.events.edit` | `Communication\EventController::edit` | `event.update` | `dashboard/communication/event_form` |
| POST | `/dashboard/komunikasi/event-agenda/{eventId}/ubah` | — | `Communication\EventController::update` | `event.update` | Redirect + audit |
| GET | `/dashboard/komunikasi/event-agenda/{eventId}` | `dashboard.communication.events.detail` | `Communication\EventController::detail` | `event.view` | `dashboard/communication/event_detail` |
| POST | `/dashboard/komunikasi/event-agenda/{eventId}/generate-kode-presensi` | `dashboard.communication.events.generate-attendance-code` | `Communication\EventController::generateAttendanceCode` | `event.update` | JSON / Redirect (kode 6 digit + hash) |
| POST | `/dashboard/komunikasi/event-agenda/{eventId}/buka-presensi` | `dashboard.communication.events.open-attendance` | `Communication\EventController::openAttendance` | `event.update` | Redirect + audit |
| POST | `/dashboard/komunikasi/event-agenda/{eventId}/tutup-presensi` | `dashboard.communication.events.close-attendance` | `Communication\EventController::closeAttendance` | `event.update` | Redirect + audit |
| POST | `/dashboard/komunikasi/event-agenda/{eventId}/batch-certificates` | `dashboard.communication.events.batch-certificates` | `Communication\EventController::batchCertificates` | `certificate.create` | JSON / Redirect (atomic issue attended) |
| POST | `/dashboard/komunikasi/event-agenda/{eventId}/hapus` | `dashboard.communication.events.delete` | `Communication\EventController::delete` | `event.delete` | Redirect + audit |
| GET | `/dashboard/komunikasi/registrasi-event` | `dashboard.communication.event-registrations` | `Communication\EventRegistrationController::index` | `event.registration.view` | `dashboard/communication/registrations` |
| GET | `/dashboard/komunikasi/registrasi-event/{id}` | `dashboard.communication.event-registrations.detail` | `Communication\EventRegistrationController::detail` | `event.registration.view` | `dashboard/communication/registration_detail` |
| POST | `/dashboard/komunikasi/registrasi-event/{id}/status` | — | `Communication\EventRegistrationController::updateStatus` | `event.registration.manage` | Redirect status change |
| GET | `/dashboard/komunikasi/kehadiran` | `dashboard.communication.attendance` | `Communication\AttendanceController::index` | `event.attendance.view` | `dashboard/communication/attendance` |
| POST | `/dashboard/komunikasi/kehadiran/{id}/catatan` | — | `Communication\AttendanceController::record` | `event.attendance.manage` | Redirect attendance record update |
| GET | `/dashboard/komunikasi/waitlist-pembatalan` | `dashboard.communication.waitlist-cancellations` | `Communication\EventRegistrationController::waitlistAndCancellation` | `event.registration.view` | `dashboard/communication/waitlist_cancellations` |

### Sertifikat Back-office

| Method | Path | Route name | Controller | Permission minimum | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/sertifikat` | `dashboard.certificates.index` | `Certificate\CertificateController::index` | `certificate.view` | `dashboard/certificates/index` |
| GET | `/dashboard/sertifikat/{certificateId}` | `dashboard.certificates.show` | `Certificate\CertificateController::show` | `certificate.view` | `dashboard/certificates/detail` |
| GET | `/dashboard/sertifikat/template` | `dashboard.certificates.templates.index` | `Certificate\CertificateTemplateController::index` | `certificate.template.view` | `dashboard/certificates/templates` |
| GET | `/dashboard/sertifikat/template/baru` | `dashboard.certificates.templates.create` | `Certificate\CertificateTemplateController::create` | `certificate.template.create` | `dashboard/certificates/template_form` |
| POST | `/dashboard/sertifikat/template` | `dashboard.certificates.templates.store` | `Certificate\CertificateTemplateController::store` | `certificate.template.create` | Redirect + audit |
| GET | `/dashboard/sertifikat/antrian-persetujuan` | `dashboard.certificates.approval-queue` | `Certificate\CertificateApprovalController::index` | `certificate.approve` | `dashboard/certificates/approval_queue` |
| GET | `/dashboard/sertifikat/dicabut` | `dashboard.certificates.revoked` | `Certificate\CertificateController::revoked` | `certificate.view` | `dashboard/certificates/revoked` |

### Government Intelligence Back-office

| Method | Path | Route name | Controller | Permission minimum | View/response |
|---|---|---|---|---|---|
| GET | `/dashboard/intelligence` | `dashboard.intelligence.index` | `Intelligence\IntelligenceDashboardController::index` | `intelligence.view` | `dashboard/intelligence/index` |
| GET | `/dashboard/intelligence/organisasi` | `dashboard.intelligence.organizations` | `Intelligence\OpdController::index` | `intelligence.organization.view` | `dashboard/intelligence/organizations` |
| GET | `/dashboard/intelligence/jf-ppbj` | `dashboard.intelligence.jf-ppbj` | `Intelligence\JfPpbjController::index` | `intelligence.jf_ppbj.view` | `dashboard/intelligence/jf_ppbj` |
| GET | `/dashboard/intelligence/sertifikat-kompetensi` | `dashboard.intelligence.competency-certificates` | `Intelligence\CompetencyCertificateController::index` | `intelligence.certificate.view` | `dashboard/intelligence/competency_certificates` |
| GET | `/dashboard/intelligence/gap-kompetensi` | `dashboard.intelligence.competency-gaps` | `Intelligence\IntelligenceDashboardController::competencyGaps` | `intelligence.gap.view` | `dashboard/intelligence/competency_gaps` |
| GET | `/dashboard/intelligence/export` | `dashboard.intelligence.exports` | `Shared\ExportController::intelligence` | `intelligence.export` | `dashboard/intelligence/exports` atau download terproteksi |

---

## Route File, Preview, Download, dan Export

File private/internal/restricted berada pada `writable/uploads/` dan tidak boleh memiliki URL publik langsung. Semua operasi file memakai authorization server-side, visibility, ownership, assignment, permission, dan workflow state yang sesuai.

| Method | Path | Route name | Controller | Filter/Akses | Response |
|---|---|---|---|---|---|
| GET | `/file/{fileId}/preview` | `shared.file.preview` | `Shared\FileController::preview` | `auth` + FileAccessService | Inline preview bila diizinkan |
| GET | `/file/{fileId}/download` | `shared.file.download` | `Shared\FileController::download` | `auth` + FileAccessService | Download terproteksi |
| GET | `/file/{fileId}/metadata` | `shared.file.metadata` | `Shared\FileController::metadata` | `auth` + FileAccessService | Metadata minimal sesuai akses |
| POST | `/file/upload` | `shared.file.upload` | `Shared\FileController::upload` | `auth` + permission/domain policy | JSON/redirect; validasi upload |
| GET | `/export/{exportId}/download` | `shared.export.download` | `Shared\ExportController::download` | `auth` + ownership/permission | Download export terproteksi |

Public asset yang secara eksplisit berstatus public dapat berada di `public/assets/main/uploads-public/`. Keberadaan file public tidak membolehkan file internal/restricted dipindahkan ke public asset root.

---

## Error Route dan HTTP Semantics

Error view menggunakan `app/Views/error/` dengan tampilan minimal/custom dan tidak bergantung pada Auth UI.

| Kondisi | HTTP | View |
|---|---:|---|
| Request tidak valid | 400 | `error/400` |
| Tidak terautentikasi atau akses tidak cukup | 403 bila identitas ada namun dilarang; redirect login untuk guest pada route browser terproteksi | `error/403` bila relevan |
| Resource tidak ditemukan/tidak visible | 404 | `error/404` |
| CSRF/session invalid untuk request state-changing | 419 | `error/419` atau response yang sesuai |
| Rate limit terlampaui | 429 | `error/429` |
| Kesalahan server tak terduga | 500 | `error/500` atau `error/exception` |
| Maintenance | 503 | `error/maintenance` |

Untuk resource restricted, sistem boleh mengembalikan 404 alih-alih 403 jika pengungkapan keberadaan resource dapat menimbulkan risiko keamanan atau privasi.

---

## Aturan Implementasi Route

1. Semua definisi route ditempatkan pada `app/Config/Routes.php` atau file route modular yang didaftarkan melalui konfigurasi proyek.
2. Route name harus unik dan mengikuti konvensi pada dokumen ini.
3. Route parameter menggunakan nama deskriptif, misalnya `{userId}`, `{classId}`, `{certificateId}`, `{slug}`, atau `{token}`.
4. Parameter ID harus divalidasi dan resource harus diperiksa terhadap ownership, assignment, visibility, serta permission pada server-side.
5. Identifier publik memakai `slug` atau verification token; ID internal digunakan pada area terautentikasi/admin jika sesuai kebutuhan.
6. Route `POST`, `PUT`, `PATCH`, dan `DELETE` wajib memakai CSRF protection untuk browser flow serta mencatat audit pada aksi sensitif.
7. Route yang mengubah workflow harus memvalidasi transisi state melalui service/workflow policy; controller tidak boleh mengubah status secara langsung tanpa validasi.
8. Route Dashboard memerlukan `dashboardAccess` selain permission granular yang relevan.
9. Route Workspace memerlukan `workspaceAccess` serta role/assignment/ownership yang relevan.
10. Route Landing hanya merender data public/published dan tidak memanggil view reference.
11. Download, preview, upload, serta export wajib melewati `FileAccessService`, `FileStorageService`, atau `ExportService` sesuai tanggung jawabnya.
12. Detail implementasi permission, account status, workflow, storage, dan audit mengikuti dokumen `auth-and-rbac.md`, `workflow-contract.md`, serta `file-storage-policy.md`.

---

## Status Implementasi Route

Status route harus dicatat di `dokumen/implementation-status.md` dengan definisi berikut:

| Status | Kriteria route |
|---|---|
| Planned | Route belum terdaftar atau hanya tercantum pada kontrak |
| UI Implemented | GET/view tersedia dengan data dummy atau static; aksi backend belum lengkap |
| Backend Ready | Method, validation, service, persistence, filter, policy, dan authorization utama tersedia |
| Production Ready | Test feature, audit, rate limit, error handling, security review, logging/monitoring, serta acceptance test telah dipenuhi |

Tidak ada route yang dinyatakan selesai hanya karena controller atau view telah dibuat.
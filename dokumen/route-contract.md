# BegawiPengadaan — Route Contract & Endpoint Inventory

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual
> - **Total Rute Terpetakan:** 388 Rute Aktif

---

## 1. Ringkasan Eksekutif Kontrak Rute

BegawiPengadaan mengoperasikan **388 Rute Aktif** yang didefinisikan pada `app/Config/Routes.php` dan terbagi ke dalam 5 zona operasional. Setiap rute diproteksi oleh pipeline filter terpadu (`forcehttps`, `pagecache`, `csrf`, `invalidchars`, `auth`, `workspaceAuth`, dan `secureheaders`) tanpa adanya rute orphan/mati.

---

## Zona 1: Public Landing Page & Layanan Terbuka (FiStudy) (73 Rute)

| No | HTTP Method | Pola Rute / URI | Nama Rute | Controller Handler | Filter Sebelum / Sesudah |
|---|:---:|---|---|---|---|
| 1 | `GET` | `/` | `public.home` | `\App\Controllers\Landing\HomeController::index` | csrf invalidchars -> secureheaders |
| 2 | `GET` | `file/([^/]+)` | `public.file.show` | `\App\Controllers\Landing\FileController::show/$1` | csrf invalidchars -> secureheaders |
| 3 | `GET` | `tentang` | `public.about` | `\App\Controllers\Landing\HomeController::about` | csrf invalidchars -> secureheaders |
| 4 | `GET` | `kontak` | `public.contact` | `\App\Controllers\Landing\HomeController::contact` | csrf invalidchars -> secureheaders |
| 5 | `GET` | `survei-kepuasan` | `public.survey` | `\App\Controllers\Landing\HomeController::survey` | csrf invalidchars -> secureheaders |
| 6 | `GET` | `arena-belajar` | `public.learning.index` | `\App\Controllers\Landing\ArenaBelajarController::index` | csrf invalidchars -> secureheaders |
| 7 | `GET` | `arena-belajar/program` | `public.learning.programs` | `\App\Controllers\Landing\ArenaBelajarController::programs` | csrf invalidchars -> secureheaders |
| 8 | `GET` | `arena-belajar/program/([^/]+)` | `public.learning.program.detail` | `\App\Controllers\Landing\ArenaBelajarController::programDetail/$1` | csrf invalidchars -> secureheaders |
| 9 | `GET` | `arena-belajar/course` | `public.learning.courses` | `\App\Controllers\Landing\ArenaBelajarController::courses` | csrf invalidchars -> secureheaders |
| 10 | `GET` | `arena-belajar/course/([^/]+)` | `public.learning.course.detail` | `\App\Controllers\Landing\ArenaBelajarController::courseDetail/$1` | csrf invalidchars -> secureheaders |
| 11 | `GET` | `arena-belajar/kelas` | `public.learning.classes` | `\App\Controllers\Landing\ArenaBelajarController::classes` | csrf invalidchars -> secureheaders |
| 12 | `GET` | `arena-belajar/kelas/([^/]+)` | `public.learning.class.detail` | `\App\Controllers\Landing\ArenaBelajarController::classDetail/$1` | csrf invalidchars -> secureheaders |
| 13 | `GET` | `arena-belajar/pengajar` | `public.learning.trainers` | `\App\Controllers\Landing\ArenaBelajarController::trainers` | csrf invalidchars -> secureheaders |
| 14 | `GET` | `arena-belajar/pengajar/([^/]+)` | `public.learning.trainer.detail` | `\App\Controllers\Landing\ArenaBelajarController::trainerDetail/$1` | csrf invalidchars -> secureheaders |
| 15 | `GET` | `bunga-rampai` | `public.knowledge.index` | `\App\Controllers\Landing\BungaRampaiController::index` | csrf invalidchars -> secureheaders |
| 16 | `GET` | `bunga-rampai/artikel` | `public.knowledge.articles` | `\App\Controllers\Landing\BungaRampaiController::articles` | csrf invalidchars -> secureheaders |
| 17 | `GET` | `bunga-rampai/artikel/([^/]+)` | `public.knowledge.article.detail` | `\App\Controllers\Landing\BungaRampaiController::articleDetail/$1` | csrf invalidchars -> secureheaders |
| 18 | `GET` | `bunga-rampai/faq` | `public.knowledge.faqs` | `\App\Controllers\Landing\BungaRampaiController::faqs` | csrf invalidchars -> secureheaders |
| 19 | `GET` | `bunga-rampai/studi-kasus` | `public.knowledge.case-studies` | `\App\Controllers\Landing\BungaRampaiController::caseStudies` | csrf invalidchars -> secureheaders |
| 20 | `GET` | `bunga-rampai/studi-kasus/([^/]+)` | `public.knowledge.case-study.detail` | `\App\Controllers\Landing\BungaRampaiController::caseStudyDetail/$1` | csrf invalidchars -> secureheaders |
| 21 | `GET` | `bunga-rampai/template-dokumen` | `public.knowledge.templates` | `\App\Controllers\Landing\BungaRampaiController::templates` | csrf invalidchars -> secureheaders |
| 22 | `GET` | `bunga-rampai/template-dokumen/([^/]+)` | `public.knowledge.templates.detail` | `\App\Controllers\Landing\BungaRampaiController::templateDetail/$1` | csrf invalidchars -> secureheaders |
| 23 | `GET` | `bunga-rampai/template-dokumen/([^/]+)/unduh` | `public.knowledge.templates.download` | `\App\Controllers\Landing\BungaRampaiController::templateDownload/$1` | csrf invalidchars -> secureheaders |
| 24 | `GET` | `bunga-rampai/multimedia` | `public.knowledge.multimedia` | `\App\Controllers\Landing\BungaRampaiController::multimedia` | csrf invalidchars -> secureheaders |
| 25 | `GET` | `bunga-rampai/media` | `public.knowledge.media` | `\App\Controllers\Landing\BungaRampaiController::media` | csrf invalidchars -> secureheaders |
| 26 | `GET` | `bunga-rampai/video` | `public.knowledge.videos` | `\App\Controllers\Landing\BungaRampaiController::videos` | csrf invalidchars -> secureheaders |
| 27 | `GET` | `bunga-rampai/galeri` | `public.knowledge.gallery` | `\App\Controllers\Landing\BungaRampaiController::gallery` | csrf invalidchars -> secureheaders |
| 28 | `GET` | `regulasi` | `public.regulations.index` | `\App\Controllers\Landing\RegulationPublicController::index` | csrf invalidchars -> secureheaders |
| 29 | `GET` | `regulasi/kategori/([^/]+)` | `public.regulations.category` | `\App\Controllers\Landing\RegulationPublicController::category/$1` | csrf invalidchars -> secureheaders |
| 30 | `GET` | `regulasi/([^/]+)/versi` | `public.regulations.versions` | `\App\Controllers\Landing\RegulationPublicController::versions/$1` | csrf invalidchars -> secureheaders |
| 31 | `GET` | `regulasi/([^/]+)/relasi` | `public.regulations.relations` | `\App\Controllers\Landing\RegulationPublicController::relations/$1` | csrf invalidchars -> secureheaders |
| 32 | `GET` | `regulasi/([^/]+)/preview` | `public.regulations.preview` | `\App\Controllers\Landing\RegulationPublicController::preview/$1` | csrf invalidchars -> secureheaders |
| 33 | `GET` | `regulasi/([^/]+)/unduh` | `public.regulations.download` | `\App\Controllers\Landing\RegulationPublicController::download/$1` | csrf invalidchars -> secureheaders |
| 34 | `GET` | `regulasi/([^/]+)` | `public.regulations.detail` | `\App\Controllers\Landing\RegulationPublicController::detail/$1` | csrf invalidchars -> secureheaders |
| 35 | `GET` | `berita` | `public.news.index` | `\App\Controllers\Landing\NewsPublicController::index` | csrf invalidchars -> secureheaders |
| 36 | `GET` | `berita/kategori/([^/]+)` | `public.news.category` | `\App\Controllers\Landing\NewsPublicController::category/$1` | csrf invalidchars -> secureheaders |
| 37 | `GET` | `berita/([^/]+)` | `public.news.detail` | `\App\Controllers\Landing\NewsPublicController::detail/$1` | csrf invalidchars -> secureheaders |
| 38 | `GET` | `event` | `public.events.index` | `\App\Controllers\Landing\EventPublicController::index` | csrf invalidchars -> secureheaders |
| 39 | `GET` | `event/agenda` | `public.events.agenda` | `\App\Controllers\Landing\EventPublicController::agenda` | csrf invalidchars -> secureheaders |
| 40 | `GET` | `event/kalender` | `public.events.calendar` | `\App\Controllers\Landing\EventPublicController::calendar` | csrf invalidchars -> secureheaders |
| 41 | `GET` | `event/([^/]+)/presensi` | `public.events.checkin-qr` | `\App\Controllers\Landing\EventPublicController::checkinByQr/$1` | csrf invalidchars -> secureheaders |
| 42 | `GET` | `event/([^/]+)` | `public.events.detail` | `\App\Controllers\Landing\EventPublicController::detail/$1` | csrf invalidchars -> secureheaders |
| 43 | `GET` | `statistik` | `public.statistics.index` | `\App\Controllers\Landing\StatisticPublicController::index` | csrf invalidchars -> secureheaders |
| 44 | `GET` | `statistik/pembelajaran` | `public.statistics.learning` | `\App\Controllers\Landing\StatisticPublicController::learning` | csrf invalidchars -> secureheaders |
| 45 | `GET` | `statistik/event` | `public.statistics.events` | `\App\Controllers\Landing\StatisticPublicController::events` | csrf invalidchars -> secureheaders |
| 46 | `GET` | `statistik/kompetensi` | `public.statistics.competency` | `\App\Controllers\Landing\StatisticPublicController::competency` | csrf invalidchars -> secureheaders |
| 47 | `GET` | `statistik/survei-kepuasan` | `public.statistics.survey` | `\App\Controllers\Landing\StatisticPublicController::satisfactionSurvey` | csrf invalidchars -> secureheaders |
| 48 | `GET` | `survei-kepuasan/hasil` | `public.survey.results` | `\App\Controllers\Landing\StatisticPublicController::satisfactionSurvey` | csrf invalidchars -> secureheaders |
| 49 | `GET` | `verifikasi-sertifikat` | `public.certificate.verify` | `\App\Controllers\Landing\CertificateVerificationController::index` | csrf invalidchars -> secureheaders |
| 50 | `GET` | `verifikasi-sertifikat/hasil` | `public.certificate.verify.result` | `\App\Controllers\Landing\CertificateVerificationController::result` | csrf invalidchars -> secureheaders |
| 51 | `GET` | `verifikasi-sertifikat/(.*)` | `public.certificate.verify.detail` | `\App\Controllers\Landing\CertificateVerificationController::detail/$1` | csrf invalidchars -> secureheaders |
| 52 | `GET` | `verify/certificate/([^/]+)` | `public.certificate.verify.qr` | `\App\Controllers\Landing\CertificateVerificationController::detail/$1` | csrf invalidchars -> secureheaders |
| 53 | `GET` | `masuk` | `Â»` | `(Closure)` | csrf invalidchars -> secureheaders |
| 54 | `GET` | `login` | `Â»` | `(Closure)` | csrf invalidchars -> secureheaders |
| 55 | `GET` | `daftar` | `Â»` | `(Closure)` | csrf invalidchars -> secureheaders |
| 56 | `GET` | `register` | `Â»` | `(Closure)` | csrf invalidchars -> secureheaders |
| 57 | `GET` | `lupa-kata-sandi` | `Â»` | `(Closure)` | csrf invalidchars -> secureheaders |
| 58 | `GET` | `lupa-password` | `Â»` | `(Closure)` | csrf invalidchars -> secureheaders |
| 59 | `GET` | `lupa-sandi` | `Â»` | `(Closure)` | csrf invalidchars -> secureheaders |
| 60 | `GET` | `lupa-sandi/reset/([^/]+)` | `Â»` | `\App\Controllers\Dashboard\Auth\PasswordController::resetForm/$1` | csrf invalidchars -> secureheaders |
| 61 | `GET` | `reset-kata-sandi/([^/]+)` | `Â»` | `\App\Controllers\Dashboard\Auth\PasswordController::resetForm/$1` | csrf invalidchars -> secureheaders |
| 62 | `GET` | `pratinjau/403` | `preview.403` | `\App\Controllers\Landing\HomeController::forbiddenPreview` | csrf invalidchars -> secureheaders |
| 63 | `GET` | `pratinjau/404` | `preview.404` | `\App\Controllers\Landing\HomeController::notFoundPreview` | csrf invalidchars -> secureheaders |
| 64 | `GET` | `pratinjau/500` | `preview.500` | `\App\Controllers\Landing\HomeController::serverErrorPreview` | csrf invalidchars -> secureheaders |
| 65 | `POST` | `kontak` | `public.contact.send` | `\App\Controllers\Landing\HomeController::sendContact` | csrf invalidchars -> secureheaders |
| 66 | `POST` | `survei-kepuasan` | `public.survey.submit` | `\App\Controllers\Landing\HomeController::submitSurvey` | csrf invalidchars -> secureheaders |
| 67 | `POST` | `newsletter/subscribe` | `public.newsletter.subscribe` | `\App\Controllers\Landing\HomeController::subscribeNewsletter` | csrf invalidchars -> secureheaders |
| 68 | `POST` | `arena-belajar/kelas/([^/]+)/daftar` | `Â»` | `\App\Controllers\Landing\ArenaBelajarController::enroll/$1` | csrf invalidchars -> secureheaders |
| 69 | `POST` | `event/([^/]+)/daftar` | `public.events.register` | `\App\Controllers\Landing\EventPublicController::register/$1` | csrf invalidchars -> secureheaders |
| 70 | `POST` | `verifikasi-sertifikat/hasil` | `public.certificate.verify.result` | `\App\Controllers\Landing\CertificateVerificationController::result` | csrf invalidchars -> secureheaders |
| 71 | `POST` | `bookmark/toggle` | `public.bookmark.toggle` | `\App\Controllers\BookmarkController::toggle` | csrf invalidchars -> secureheaders |
| 72 | `POST` | `lupa-sandi/reset/([^/]+)` | `Â»` | `\App\Controllers\Dashboard\Auth\PasswordController::updatePassword/$1` | csrf invalidchars -> secureheaders |
| 73 | `POST` | `reset-kata-sandi/([^/]+)` | `Â»` | `\App\Controllers\Dashboard\Auth\PasswordController::updatePassword/$1` | csrf invalidchars -> secureheaders |

---

## Zona 2: Pintu Masuk Autentikasi Tunggal (WowDash) (23 Rute)

| No | HTTP Method | Pola Rute / URI | Nama Rute | Controller Handler | Filter Sebelum / Sesudah |
|---|:---:|---|---|---|---|
| 1 | `GET` | `dashboard/masuk` | `dashboard.auth.login` | `\App\Controllers\Dashboard\Auth\LoginController::index` | csrf invalidchars auth -> secureheaders |
| 2 | `GET` | `dashboard/lupa-kata-sandi` | `dashboard.auth.password.forgot` | `\App\Controllers\Dashboard\Auth\PasswordController::forgotForm` | csrf invalidchars auth -> secureheaders |
| 3 | `GET` | `dashboard/reset-kata-sandi/([^/]+)` | `dashboard.auth.password.reset` | `\App\Controllers\Dashboard\Auth\PasswordController::resetForm/$1` | csrf invalidchars auth -> secureheaders |
| 4 | `GET` | `dashboard/status-akun` | `dashboard.auth.account-status` | `\App\Controllers\Dashboard\Auth\AccountStatusController::show` | csrf invalidchars auth -> secureheaders |
| 5 | `GET` | `dashboard/keluar` | `dashboard.auth.logout.preview` | `\App\Controllers\Dashboard\Auth\LoginController::logoutPreview` | csrf invalidchars auth -> secureheaders |
| 6 | `GET` | `dashboard/daftar` | `dashboard.auth.register` | `\App\Controllers\Dashboard\Auth\RegisterController::index` | csrf invalidchars auth -> secureheaders |
| 7 | `GET` | `dashboard/daftar/wilayah` | `dashboard.auth.register.address` | `\App\Controllers\Dashboard\Auth\RegisterController::registerAddress` | csrf invalidchars auth -> secureheaders |
| 8 | `GET` | `dashboard/daftar/wilayah/options` | `dashboard.auth.register.address.options.root` | `\App\Controllers\Dashboard\Auth\RegisterController::wilayahOptions` | csrf invalidchars auth -> secureheaders |
| 9 | `GET` | `dashboard/daftar/wilayah/options/([^/]+)` | `dashboard.auth.register.address.options` | `\App\Controllers\Dashboard\Auth\RegisterController::wilayahOptions/$1` | csrf invalidchars auth -> secureheaders |
| 10 | `GET` | `dashboard/daftar/profil/asn` | `dashboard.auth.register.profile.asn` | `\App\Controllers\Dashboard\Auth\RegisterController::registerAsn` | csrf invalidchars auth -> secureheaders |
| 11 | `GET` | `dashboard/daftar/profil/penyedia` | `dashboard.auth.register.profile.provider` | `\App\Controllers\Dashboard\Auth\RegisterController::registerProvider` | csrf invalidchars auth -> secureheaders |
| 12 | `GET` | `dashboard/daftar/profil/akademisi` | `dashboard.auth.register.profile.academic` | `\App\Controllers\Dashboard\Auth\RegisterController::registerAcademic` | csrf invalidchars auth -> secureheaders |
| 13 | `GET` | `dashboard/daftar/profil/masyarakat` | `dashboard.auth.register.profile.public` | `\App\Controllers\Dashboard\Auth\RegisterController::registerPublic` | csrf invalidchars auth -> secureheaders |
| 14 | `GET` | `dashboard/daftar/tinjau` | `dashboard.auth.register.review` | `\App\Controllers\Dashboard\Auth\RegisterController::registerReview` | csrf invalidchars auth -> secureheaders |
| 15 | `GET` | `dashboard/daftar/berhasil` | `dashboard.auth.register.success` | `\App\Controllers\Dashboard\Auth\RegisterController::registerSuccess` | csrf invalidchars auth -> secureheaders |
| 16 | `POST` | `dashboard/masuk` | `dashboard.auth.login.attempt` | `\App\Controllers\Dashboard\Auth\LoginController::attemptLogin` | csrf invalidchars auth -> secureheaders |
| 17 | `POST` | `dashboard/lupa-kata-sandi` | `dashboard.auth.password.send-reset` | `\App\Controllers\Dashboard\Auth\PasswordController::sendReset` | csrf invalidchars auth -> secureheaders |
| 18 | `POST` | `dashboard/reset-kata-sandi/([^/]+)` | `dashboard.auth.password.update` | `\App\Controllers\Dashboard\Auth\PasswordController::updatePassword/$1` | csrf invalidchars auth -> secureheaders |
| 19 | `POST` | `dashboard/keluar` | `dashboard.auth.logout` | `\App\Controllers\Dashboard\Auth\LoginController::logout` | csrf invalidchars auth -> secureheaders |
| 20 | `POST` | `dashboard/daftar` | `dashboard.auth.register.attempt` | `\App\Controllers\Dashboard\Auth\RegisterController::attempt` | csrf invalidchars auth -> secureheaders |
| 21 | `POST` | `dashboard/daftar/wilayah` | `dashboard.auth.register.address.attempt` | `\App\Controllers\Dashboard\Auth\RegisterController::registerAddressAttempt` | csrf invalidchars auth -> secureheaders |
| 22 | `POST` | `dashboard/daftar/profil/([^/]+)` | `dashboard.auth.register.profile.attempt` | `\App\Controllers\Dashboard\Auth\RegisterController::registerProfileAttempt/$1` | csrf invalidchars auth -> secureheaders |
| 23 | `POST` | `dashboard/daftar/tinjau` | `dashboard.auth.register.review.attempt` | `\App\Controllers\Dashboard\Auth\RegisterController::registerReviewAttempt` | csrf invalidchars auth -> secureheaders |

---

## Zona 3: Ruang Kerja Multi-Peran / Workspace (FiStudy) (176 Rute)

| No | HTTP Method | Pola Rute / URI | Nama Rute | Controller Handler | Filter Sebelum / Sesudah |
|---|:---:|---|---|---|---|
| 1 | `GET` | `workspace` | `workspace.home` | `\App\Controllers\Workspace\WorkspaceHomeController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 2 | `GET` | `workspace/section/([^/]+)` | `workspace.section` | `\App\Controllers\Workspace\WorkspaceHomeController::section/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 3 | `GET` | `workspace/aktivitas` | `workspace.activity` | `\App\Controllers\Workspace\WorkspaceHomeController::activity` | csrf invalidchars workspace-auth -> secureheaders |
| 4 | `GET` | `workspace/search/spotlight` | `workspace.search.spotlight` | `\App\Controllers\Workspace\WorkspaceHomeController::spotlightSearch` | csrf invalidchars workspace-auth -> secureheaders |
| 5 | `GET` | `workspace/konsultasi` | `workspace.consultation.index` | `\App\Controllers\Workspace\ConsultationController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 6 | `GET` | `workspace/konsultasi/buat` | `workspace.consultation.create` | `\App\Controllers\Workspace\ConsultationController::create` | csrf invalidchars workspace-auth -> secureheaders |
| 7 | `GET` | `workspace/konsultasi/detail/([^/]+)` | `workspace.consultation.detail` | `\App\Controllers\Workspace\ConsultationController::detail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 8 | `GET` | `workspace/konsultasi/([^/]+)` | `Â»` | `\App\Controllers\Workspace\ConsultationController::detail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 9 | `GET` | `workspace/notifikasi` | `workspace.notifications` | `\App\Controllers\Workspace\NotificationController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 10 | `GET` | `workspace/notifikasi-api/feed` | `workspace.notifications.feed` | `\App\Controllers\Workspace\NotificationController::feed` | csrf invalidchars workspace-auth -> secureheaders |
| 11 | `GET` | `workspace/notifikasi/([^/]+)` | `workspace.notifications.detail` | `\App\Controllers\Workspace\NotificationController::detail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 12 | `GET` | `workspace/bookmark` | `workspace.bookmarks.index` | `\App\Controllers\Workspace\BookmarkController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 13 | `GET` | `workspace/bookmark/pembelajaran` | `workspace.bookmarks.learning` | `\App\Controllers\Workspace\BookmarkController::learning` | csrf invalidchars workspace-auth -> secureheaders |
| 14 | `GET` | `workspace/bookmark/bunga-rampai` | `workspace.bookmarks.knowledge` | `\App\Controllers\Workspace\BookmarkController::knowledge` | csrf invalidchars workspace-auth -> secureheaders |
| 15 | `GET` | `workspace/bookmark/regulasi` | `workspace.bookmarks.regulations` | `\App\Controllers\Workspace\BookmarkController::regulations` | csrf invalidchars workspace-auth -> secureheaders |
| 16 | `GET` | `workspace/bookmark/komunikasi` | `workspace.bookmarks.communication` | `\App\Controllers\Workspace\BookmarkController::communication` | csrf invalidchars workspace-auth -> secureheaders |
| 17 | `GET` | `workspace/bookmark/sertifikat` | `workspace.bookmarks.certificates` | `\App\Controllers\Workspace\BookmarkController::certificates` | csrf invalidchars workspace-auth -> secureheaders |
| 18 | `GET` | `workspace/profil` | `workspace.profile.index` | `\App\Controllers\Workspace\ProfileController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 19 | `GET` | `workspace/profil/akun` | `workspace.profile.account` | `\App\Controllers\Workspace\ProfileController::account` | csrf invalidchars workspace-auth -> secureheaders |
| 20 | `GET` | `workspace/profil/asn` | `workspace.profile.asn` | `\App\Controllers\Workspace\ProfileController::asn` | csrf invalidchars workspace-auth -> secureheaders |
| 21 | `GET` | `workspace/profil/penyedia` | `workspace.profile.provider` | `\App\Controllers\Workspace\ProfileController::provider` | csrf invalidchars workspace-auth -> secureheaders |
| 22 | `GET` | `workspace/profil/akademik` | `workspace.profile.academic` | `\App\Controllers\Workspace\ProfileController::academic` | csrf invalidchars workspace-auth -> secureheaders |
| 23 | `GET` | `workspace/profil/masyarakat` | `workspace.profile.public` | `\App\Controllers\Workspace\ProfileController::public` | csrf invalidchars workspace-auth -> secureheaders |
| 24 | `GET` | `workspace/profil/peran-pengadaan` | `workspace.profile.procurement-roles` | `\App\Controllers\Workspace\ProfileController::procurementRoles` | csrf invalidchars workspace-auth -> secureheaders |
| 25 | `GET` | `workspace/profil/pengajuan-role` | `workspace.profile.role-requests` | `\App\Controllers\Workspace\RoleRequestController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 26 | `GET` | `workspace/profil/pengajuan-role/baru` | `workspace.profile.role-requests.create` | `\App\Controllers\Workspace\RoleRequestController::create` | csrf invalidchars workspace-auth -> secureheaders |
| 27 | `GET` | `workspace/peserta` | `workspace.participant.home` | `\App\Controllers\Workspace\ParticipantController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 28 | `GET` | `workspace/peserta/kelas-saya` | `workspace.participant.classes` | `\App\Controllers\Workspace\ParticipantController::myClasses` | csrf invalidchars workspace-auth -> secureheaders |
| 29 | `GET` | `workspace/peserta/tugas` | `workspace.participant.all-assignments` | `\App\Controllers\Workspace\ParticipantController::allAssignments` | csrf invalidchars workspace-auth -> secureheaders |
| 30 | `GET` | `workspace/peserta/assessment` | `workspace.participant.all-assessments` | `\App\Controllers\Workspace\ParticipantController::allAssessments` | csrf invalidchars workspace-auth -> secureheaders |
| 31 | `GET` | `workspace/peserta/event-saya` | `workspace.participant.events` | `\App\Controllers\Workspace\ParticipantController::myEvents` | csrf invalidchars workspace-auth -> secureheaders |
| 32 | `GET` | `workspace/peserta/event-saya/([^/]+)` | `workspace.participant.event.detail` | `\App\Controllers\Workspace\ParticipantController::eventDetail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 33 | `GET` | `workspace/peserta/event-saya/([^/]+)/materi/([0-9]+)/download` | `workspace.participant.event.material.download` | `\App\Controllers\Workspace\ParticipantController::downloadEventMaterial/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 34 | `GET` | `workspace/peserta/event/([^/]+)` | `Â»` | `\App\Controllers\Workspace\ParticipantController::eventDetail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 35 | `GET` | `workspace/peserta/presensi-saya` | `workspace.participant.attendance` | `\App\Controllers\Workspace\ParticipantController::myAttendance` | csrf invalidchars workspace-auth -> secureheaders |
| 36 | `GET` | `workspace/peserta/kelas/([^/]+)` | `workspace.participant.class.detail` | `\App\Controllers\Workspace\ParticipantController::classDetail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 37 | `GET` | `workspace/peserta/kelas/([^/]+)/belajar` | `workspace.participant.study` | `\App\Controllers\Workspace\ParticipantController::study/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 38 | `GET` | `workspace/peserta/lesson/([^/]+)` | `workspace.participant.lesson.detail` | `\App\Controllers\Workspace\ParticipantController::lessonDetail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 39 | `GET` | `workspace/peserta/kelas/([^/]+)/progres` | `workspace.participant.progress` | `\App\Controllers\Workspace\ParticipantController::progress/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 40 | `GET` | `workspace/peserta/kelas/([^/]+)/transkrip` | `workspace.participant.transcript` | `\App\Controllers\Workspace\ParticipantController::transcript/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 41 | `GET` | `workspace/peserta/kelas/([^/]+)/pre-test` | `workspace.participant.pre-test` | `\App\Controllers\Workspace\ParticipantController::preTest/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 42 | `GET` | `workspace/peserta/kelas/([^/]+)/pre-test/hasil` | `workspace.participant.pre-test.result` | `\App\Controllers\Workspace\ParticipantController::preTestResult/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 43 | `GET` | `workspace/peserta/kelas/([^/]+)/quiz` | `workspace.participant.quiz` | `\App\Controllers\Workspace\ParticipantController::quiz/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 44 | `GET` | `workspace/peserta/kelas/([^/]+)/quiz/hasil` | `workspace.participant.quiz.result` | `\App\Controllers\Workspace\ParticipantController::quizResult/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 45 | `GET` | `workspace/peserta/quiz/([^/]+)` | `workspace.participant.quiz.take` | `\App\Controllers\Workspace\ParticipantController::takeQuiz/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 46 | `GET` | `workspace/peserta/kelas/([^/]+)/tugas` | `workspace.participant.assignments` | `\App\Controllers\Workspace\ParticipantController::assignments/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 47 | `GET` | `workspace/peserta/kelas/([^/]+)/tugas/kirim` | `workspace.participant.assignment.submission` | `\App\Controllers\Workspace\ParticipantController::assignmentSubmission/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 48 | `GET` | `workspace/peserta/kelas/([^/]+)/tugas/revisi` | `workspace.participant.assignment.revision` | `\App\Controllers\Workspace\ParticipantController::assignmentRevision/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 49 | `GET` | `workspace/peserta/kelas/([^/]+)/post-test` | `workspace.participant.post-test` | `\App\Controllers\Workspace\ParticipantController::postTest/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 50 | `GET` | `workspace/peserta/kelas/([^/]+)/post-test/hasil` | `workspace.participant.post-test.result` | `\App\Controllers\Workspace\ParticipantController::postTestResult/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 51 | `GET` | `workspace/peserta/kelas/([^/]+)/feedback` | `workspace.participant.feedback` | `\App\Controllers\Workspace\ParticipantController::feedback/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 52 | `GET` | `workspace/peserta/sertifikat` | `workspace.participant.certificates` | `\App\Controllers\Workspace\ParticipantController::certificates` | csrf invalidchars workspace-auth -> secureheaders |
| 53 | `GET` | `workspace/peserta/sertifikat/([^/]+)` | `workspace.participant.certificate.detail` | `\App\Controllers\Workspace\ParticipantController::certificateDetail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 54 | `GET` | `workspace/peserta/sertifikat/([^/]+)/unduh` | `workspace.participant.certificate.download` | `\App\Controllers\Certificate\CertificateDownloadController::download/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 55 | `GET` | `workspace/trainer` | `workspace.trainer.home` | `\App\Controllers\Workspace\TrainerController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 56 | `GET` | `workspace/trainer/kelas-saya` | `workspace.trainer.classes` | `\App\Controllers\Workspace\TrainerController::myClasses` | csrf invalidchars workspace-auth -> secureheaders |
| 57 | `GET` | `workspace/trainer/materi` | `workspace.trainer.all-materials` | `\App\Controllers\Workspace\TrainerController::allMaterials` | csrf invalidchars workspace-auth -> secureheaders |
| 58 | `GET` | `workspace/trainer/assessment` | `workspace.trainer.all-quizzes` | `\App\Controllers\Workspace\TrainerController::allQuizzes` | csrf invalidchars workspace-auth -> secureheaders |
| 59 | `GET` | `workspace/trainer/peserta` | `workspace.trainer.all-participants` | `\App\Controllers\Workspace\TrainerController::allParticipants` | csrf invalidchars workspace-auth -> secureheaders |
| 60 | `GET` | `workspace/trainer/progres` | `workspace.trainer.all-progress` | `\App\Controllers\Workspace\TrainerController::allProgress` | csrf invalidchars workspace-auth -> secureheaders |
| 61 | `GET` | `workspace/trainer/kelas/([^/]+)` | `workspace.trainer.class.detail` | `\App\Controllers\Workspace\TrainerController::classDetail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 62 | `GET` | `workspace/trainer/kelas/([^/]+)/edit` | `workspace.trainer.class.edit` | `\App\Controllers\Workspace\TrainerController::editClass/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 63 | `GET` | `workspace/trainer/kelas/([^/]+)/peserta` | `workspace.trainer.participants` | `\App\Controllers\Workspace\TrainerController::participants/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 64 | `GET` | `workspace/trainer/kelas/([^/]+)/peserta/([^/]+)` | `workspace.trainer.participant.detail` | `\App\Controllers\Workspace\TrainerController::participantDetail/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 65 | `GET` | `workspace/trainer/kelas/([^/]+)/materi` | `workspace.trainer.materials` | `\App\Controllers\Workspace\TrainerController::materials/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 66 | `GET` | `workspace/trainer/kelas/([^/]+)/modul` | `workspace.trainer.modules` | `\App\Controllers\Workspace\TrainerController::modules/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 67 | `GET` | `workspace/trainer/kelas/([^/]+)/modul/tambah` | `workspace.trainer.module.create` | `\App\Controllers\Workspace\TrainerController::createModule/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 68 | `GET` | `workspace/trainer/kelas/([^/]+)/modul/([^/]+)/ubah` | `workspace.trainer.module.edit` | `\App\Controllers\Workspace\TrainerController::editModule/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 69 | `GET` | `workspace/trainer/kelas/([^/]+)/modul/([^/]+)/lesson/tambah` | `workspace.trainer.lesson.create` | `\App\Controllers\Workspace\TrainerController::createLesson/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 70 | `GET` | `workspace/trainer/kelas/([^/]+)/lesson/([^/]+)/ubah` | `workspace.trainer.lesson.edit` | `\App\Controllers\Workspace\TrainerController::editLesson/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 71 | `GET` | `workspace/trainer/kelas/([^/]+)/quiz` | `workspace.trainer.quizzes` | `\App\Controllers\Workspace\TrainerController::quizzes/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 72 | `GET` | `workspace/trainer/kelas/([^/]+)/quiz/tambah` | `workspace.trainer.quiz.create` | `\App\Controllers\Workspace\TrainerController::createQuiz/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 73 | `GET` | `workspace/trainer/kelas/([^/]+)/quiz/([^/]+)/soal` | `workspace.trainer.quiz.edit` | `\App\Controllers\Workspace\TrainerController::editQuiz/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 74 | `GET` | `workspace/trainer/kelas/([^/]+)/quiz/([^/]+)/soal/tambah` | `workspace.trainer.question.create` | `\App\Controllers\Workspace\TrainerController::addQuestion/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 75 | `GET` | `workspace/trainer/kelas/([^/]+)/quiz/([^/]+)/soal/template` | `workspace.trainer.quiz.question.template` | `\App\Controllers\Workspace\TrainerController::downloadQuestionTemplate/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 76 | `GET` | `workspace/trainer/kelas/([^/]+)/quiz/([^/]+)/hasil` | `workspace.trainer.quiz.attempts` | `\App\Controllers\Workspace\TrainerController::quizAttempts/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 77 | `GET` | `workspace/trainer/kelas/([^/]+)/tugas` | `workspace.trainer.assignments` | `\App\Controllers\Workspace\TrainerController::assignments/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 78 | `GET` | `workspace/trainer/kelas/([^/]+)/tugas/tambah` | `workspace.trainer.assignment.create` | `\App\Controllers\Workspace\TrainerController::createAssignment/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 79 | `GET` | `workspace/trainer/kelas/([^/]+)/tugas/([^/]+)/ubah` | `workspace.trainer.assignment.edit` | `\App\Controllers\Workspace\TrainerController::editAssignment/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 80 | `GET` | `workspace/trainer/submissions` | `workspace.trainer.all-submissions` | `\App\Controllers\Workspace\TrainerController::allSubmissions` | csrf invalidchars workspace-auth -> secureheaders |
| 81 | `GET` | `workspace/trainer/kelas/([^/]+)/tugas/([^/]+)/submissions` | `workspace.trainer.assignment.submissions` | `\App\Controllers\Workspace\TrainerController::assignmentSubmissions/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 82 | `GET` | `workspace/trainer/submission/([^/]+)/review` | `workspace.trainer.submission.review` | `\App\Controllers\Workspace\TrainerController::reviewSubmission/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 83 | `GET` | `workspace/trainer/kelas/([^/]+)/progres` | `workspace.trainer.progress` | `\App\Controllers\Workspace\TrainerController::progress/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 84 | `GET` | `workspace/trainer/kelas/([^/]+)/gradebook/export` | `workspace.trainer.class.gradebook.export` | `\App\Controllers\Workspace\TrainerController::exportGradebook/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 85 | `GET` | `workspace/trainer/event-saya` | `workspace.trainer.events` | `\App\Controllers\Workspace\ParticipantController::myEvents` | csrf invalidchars workspace-auth -> secureheaders |
| 86 | `GET` | `workspace/trainer/event-saya/([^/]+)` | `workspace.trainer.event.detail` | `\App\Controllers\Workspace\ParticipantController::eventDetail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 87 | `GET` | `workspace/trainer/presensi-saya` | `workspace.trainer.attendance` | `\App\Controllers\Workspace\ParticipantController::myAttendance` | csrf invalidchars workspace-auth -> secureheaders |
| 88 | `GET` | `workspace/operator` | `workspace.operator.home` | `\App\Controllers\Workspace\OperatorController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 89 | `GET` | `workspace/operator/approval` | `workspace.operator.approvals` | `\App\Controllers\Workspace\OperatorController::approvals` | csrf invalidchars workspace-auth -> secureheaders |
| 90 | `GET` | `workspace/operator/verifikasi-profil` | `workspace.operator.profile-verifications` | `\App\Controllers\Workspace\OperatorController::profileVerifications` | csrf invalidchars workspace-auth -> secureheaders |
| 91 | `GET` | `workspace/operator/perubahan-profil` | `workspace.operator.profile-change-requests` | `\App\Controllers\Workspace\OperatorController::profileChangeRequests` | csrf invalidchars workspace-auth -> secureheaders |
| 92 | `GET` | `workspace/operator/perubahan-profil/([^/]+)` | `workspace.operator.profile-change-requests.detail` | `\App\Controllers\Workspace\OperatorController::profileChangeRequestDetail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 93 | `GET` | `workspace/operator/penugasan-trainer` | `workspace.operator.class-trainers` | `\App\Controllers\Workspace\OperatorController::classTrainers` | csrf invalidchars workspace-auth -> secureheaders |
| 94 | `GET` | `workspace/operator/event` | `workspace.operator.events` | `\App\Controllers\Workspace\OperatorController::events` | csrf invalidchars workspace-auth -> secureheaders |
| 95 | `GET` | `workspace/operator/event/([^/]+)/peserta` | `workspace.operator.events.participants` | `\App\Controllers\Workspace\OperatorController::eventParticipants/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 96 | `GET` | `workspace/operator/event/([^/]+)/peserta/cetak` | `workspace.operator.events.roster.print` | `\App\Controllers\Workspace\OperatorController::printEventRoster/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 97 | `GET` | `workspace/operator/kehadiran` | `workspace.operator.attendance` | `\App\Controllers\Workspace\OperatorController::attendance` | csrf invalidchars workspace-auth -> secureheaders |
| 98 | `GET` | `workspace/operator/monitoring-pembelajaran` | `workspace.operator.learning-monitoring` | `\App\Controllers\Workspace\OperatorController::learningMonitoring` | csrf invalidchars workspace-auth -> secureheaders |
| 99 | `GET` | `workspace/operator/verifikasi-sertifikat` | `workspace.operator.certificates` | `\App\Controllers\Workspace\OperatorController::certificateVerification` | csrf invalidchars workspace-auth -> secureheaders |
| 100 | `GET` | `workspace/operator/enrollment` | `workspace.operator.enrollments` | `\App\Controllers\Workspace\OperatorController::enrollments` | csrf invalidchars workspace-auth -> secureheaders |
| 101 | `GET` | `workspace/operator/evaluasi` | `workspace.operator.evaluations` | `\App\Controllers\Workspace\OperatorController::evaluations` | csrf invalidchars workspace-auth -> secureheaders |
| 102 | `GET` | `workspace/operator/evaluasi/submission/([^/]+)` | `workspace.operator.submission.review` | `\App\Controllers\Workspace\OperatorController::reviewSubmission/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 103 | `GET` | `workspace/operator/monitoring-aktivitas` | `workspace.operator.activity-monitoring` | `\App\Controllers\Workspace\OperatorController::activityMonitoring` | csrf invalidchars workspace-auth -> secureheaders |
| 104 | `GET` | `workspace/admin` | `workspace.admin.home` | `\App\Controllers\Workspace\AdminWorkspaceController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 105 | `GET` | `workspace/audit-rup` | `workspace.rup-audit.index` | `\App\Controllers\Workspace\RupAuditController::index` | csrf invalidchars workspace-auth -> secureheaders |
| 106 | `GET` | `workspace/audit-rup/detail/([^/]+)` | `workspace.rup-audit.detail` | `\App\Controllers\Workspace\RupAuditController::detail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 107 | `GET` | `workspace/audit-rup/api-row-detail/([0-9]+)` | `workspace.rup-audit.api-row-detail` | `\App\Controllers\Workspace\RupAuditController::apiRowDetail/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 108 | `GET` | `workspace/audit-rup/export-excel/([^/]+)` | `workspace.rup-audit.export-excel` | `\App\Controllers\Workspace\RupAuditController::exportExcel/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 109 | `GET` | `workspace/audit-rup/export-pdf/([^/]+)/(.*)` | `workspace.rup-audit.export-pdf` | `\App\Controllers\Workspace\RupAuditController::exportPdf/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 110 | `GET` | `workspace/audit-rup/delete/([^/]+)` | `workspace.rup-audit.delete` | `\App\Controllers\Workspace\RupAuditController::deleteBatch/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 111 | `POST` | `workspace/konsultasi/buat` | `workspace.consultation.store` | `\App\Controllers\Workspace\ConsultationController::store` | csrf invalidchars workspace-auth -> secureheaders |
| 112 | `POST` | `workspace/konsultasi/detail/([^/]+)/balas` | `workspace.consultation.reply` | `\App\Controllers\Workspace\ConsultationController::reply/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 113 | `POST` | `workspace/konsultasi/detail/([^/]+)/selesai` | `workspace.consultation.resolve` | `\App\Controllers\Workspace\ConsultationController::resolve/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 114 | `POST` | `workspace/notifikasi/([^/]+)/baca` | `workspace.notifications.read` | `\App\Controllers\Workspace\NotificationController::markAsRead/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 115 | `POST` | `workspace/notifikasi/baca-semua` | `workspace.notifications.read-all` | `\App\Controllers\Workspace\NotificationController::markAllAsRead` | csrf invalidchars workspace-auth -> secureheaders |
| 116 | `POST` | `workspace/bookmark` | `workspace.bookmarks.remove` | `\App\Controllers\Workspace\BookmarkController::remove` | csrf invalidchars workspace-auth -> secureheaders |
| 117 | `POST` | `workspace/profil/pengajuan-role` | `workspace.profile.role-requests.store` | `\App\Controllers\Workspace\RoleRequestController::store` | csrf invalidchars workspace-auth -> secureheaders |
| 118 | `POST` | `workspace/profil/ubah` | `workspace.profile.update` | `\App\Controllers\Workspace\ProfileController::update` | csrf invalidchars workspace-auth -> secureheaders |
| 119 | `POST` | `workspace/profil/kata-sandi` | `workspace.profile.password.update` | `\App\Controllers\Workspace\ProfileController::updatePassword` | csrf invalidchars workspace-auth -> secureheaders |
| 120 | `POST` | `workspace/peserta/event-saya/([^/]+)/quiz/submit` | `workspace.participant.event.quiz.submit` | `\App\Controllers\Workspace\ParticipantController::submitEventQuiz/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 121 | `POST` | `workspace/peserta/event-saya/([^/]+)/klaim-sertifikat-mandiri` | `workspace.participant.event.claim-certificate` | `\App\Controllers\Workspace\ParticipantController::claimSelfPacedCertificate/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 122 | `POST` | `workspace/peserta/presensi/checkin` | `workspace.participant.attendance.checkin` | `\App\Controllers\Workspace\ParticipantController::submitAttendanceCode` | csrf invalidchars workspace-auth -> secureheaders |
| 123 | `POST` | `workspace/peserta/kelas/([^/]+)/daftar` | `workspace.participant.enroll` | `\App\Controllers\Workspace\ParticipantController::enroll/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 124 | `POST` | `workspace/peserta/kelas/([^/]+)/tugas/kirim` | `workspace.participant.assignment.submit` | `\App\Controllers\Workspace\ParticipantController::submitAssignment/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 125 | `POST` | `workspace/peserta/kelas/([^/]+)/feedback` | `workspace.participant.feedback.submit` | `\App\Controllers\Workspace\ParticipantController::submitFeedback/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 126 | `POST` | `workspace/peserta/lesson/([^/]+)/mulai` | `workspace.participant.lesson.start` | `\App\Controllers\Workspace\ParticipantController::startLesson/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 127 | `POST` | `workspace/peserta/lesson/([^/]+)/selesai` | `workspace.participant.lesson.complete` | `\App\Controllers\Workspace\ParticipantController::completeLesson/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 128 | `POST` | `workspace/peserta/quiz/([^/]+)/mulai` | `workspace.participant.quiz.start` | `\App\Controllers\Workspace\ParticipantController::startQuiz/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 129 | `POST` | `workspace/peserta/quiz/attempt/([^/]+)/kirim` | `workspace.participant.quiz.submit` | `\App\Controllers\Workspace\ParticipantController::submitQuiz/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 130 | `POST` | `workspace/trainer/kelas/([^/]+)/edit` | `workspace.trainer.class.update` | `\App\Controllers\Workspace\TrainerController::updateClass/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 131 | `POST` | `workspace/trainer/kelas/([^/]+)/peserta/([^/]+)/approve` | `workspace.trainer.participant.approve` | `\App\Controllers\Workspace\TrainerController::approveParticipant/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 132 | `POST` | `workspace/trainer/kelas/([^/]+)/peserta/([^/]+)/reject` | `workspace.trainer.participant.reject` | `\App\Controllers\Workspace\TrainerController::rejectParticipant/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 133 | `POST` | `workspace/trainer/kelas/([^/]+)/peserta/([^/]+)/kick` | `workspace.trainer.participant.kick` | `\App\Controllers\Workspace\TrainerController::kickParticipant/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 134 | `POST` | `workspace/trainer/kelas/([^/]+)/peserta/([^/]+)/reactivate` | `workspace.trainer.participant.reactivate` | `\App\Controllers\Workspace\TrainerController::reactivateParticipant/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 135 | `POST` | `workspace/trainer/kelas/([^/]+)/peserta/([^/]+)/feedback` | `workspace.trainer.participant.feedback` | `\App\Controllers\Workspace\TrainerController::submitParticipantFeedback/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 136 | `POST` | `workspace/trainer/kelas/([^/]+)/modul/tambah` | `Â»` | `\App\Controllers\Workspace\TrainerController::createModule/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 137 | `POST` | `workspace/trainer/kelas/([^/]+)/modul/([^/]+)/ubah` | `Â»` | `\App\Controllers\Workspace\TrainerController::editModule/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 138 | `POST` | `workspace/trainer/kelas/([^/]+)/modul/([^/]+)/hapus` | `workspace.trainer.module.delete` | `\App\Controllers\Workspace\TrainerController::deleteModule/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 139 | `POST` | `workspace/trainer/kelas/([^/]+)/modul/([^/]+)/toggle-status` | `workspace.trainer.module.toggle-status` | `\App\Controllers\Workspace\TrainerController::toggleModuleStatus/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 140 | `POST` | `workspace/trainer/kelas/([^/]+)/modul/([^/]+)/lesson/tambah` | `Â»` | `\App\Controllers\Workspace\TrainerController::createLesson/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 141 | `POST` | `workspace/trainer/kelas/([^/]+)/lesson/([^/]+)/ubah` | `Â»` | `\App\Controllers\Workspace\TrainerController::editLesson/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 142 | `POST` | `workspace/trainer/kelas/([^/]+)/lesson/([^/]+)/hapus` | `workspace.trainer.lesson.delete` | `\App\Controllers\Workspace\TrainerController::deleteLesson/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 143 | `POST` | `workspace/trainer/kelas/([^/]+)/lesson/([^/]+)/toggle-status` | `workspace.trainer.lesson.toggle-status` | `\App\Controllers\Workspace\TrainerController::toggleLessonStatus/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 144 | `POST` | `workspace/trainer/kelas/([^/]+)/quiz/tambah` | `Â»` | `\App\Controllers\Workspace\TrainerController::createQuiz/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 145 | `POST` | `workspace/trainer/kelas/([^/]+)/quiz/([^/]+)/soal` | `Â»` | `\App\Controllers\Workspace\TrainerController::editQuiz/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 146 | `POST` | `workspace/trainer/kelas/([^/]+)/quiz/([^/]+)/soal/tambah` | `Â»` | `\App\Controllers\Workspace\TrainerController::addQuestion/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 147 | `POST` | `workspace/trainer/kelas/([^/]+)/quiz/([^/]+)/soal/import` | `workspace.trainer.quiz.question.import` | `\App\Controllers\Workspace\TrainerController::importQuestions/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 148 | `POST` | `workspace/trainer/kelas/([^/]+)/quiz/([^/]+)/soal/([^/]+)/hapus` | `workspace.trainer.question.delete` | `\App\Controllers\Workspace\TrainerController::deleteQuestion/$1/$2/$3` | csrf invalidchars workspace-auth -> secureheaders |
| 149 | `POST` | `workspace/trainer/kelas/([^/]+)/tugas/tambah` | `Â»` | `\App\Controllers\Workspace\TrainerController::createAssignment/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 150 | `POST` | `workspace/trainer/kelas/([^/]+)/tugas/([^/]+)/ubah` | `Â»` | `\App\Controllers\Workspace\TrainerController::editAssignment/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 151 | `POST` | `workspace/trainer/kelas/([^/]+)/tugas/([^/]+)/hapus` | `workspace.trainer.assignment.delete` | `\App\Controllers\Workspace\TrainerController::deleteAssignment/$1/$2` | csrf invalidchars workspace-auth -> secureheaders |
| 152 | `POST` | `workspace/trainer/submission/([^/]+)/review` | `workspace.trainer.submission.review.submit` | `\App\Controllers\Workspace\TrainerController::submitReviewSubmission/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 153 | `POST` | `workspace/operator/approval/bulk` | `workspace.operator.approvals.bulk` | `\App\Controllers\Workspace\OperatorController::bulkApproval` | csrf invalidchars workspace-auth -> secureheaders |
| 154 | `POST` | `workspace/operator/approval/([^/]+)/setujui` | `workspace.operator.approvals.approve` | `\App\Controllers\Workspace\OperatorController::approveApproval/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 155 | `POST` | `workspace/operator/approval/([^/]+)/tolak` | `workspace.operator.approvals.reject` | `\App\Controllers\Workspace\OperatorController::rejectApproval/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 156 | `POST` | `workspace/operator/verifikasi-profil/bulk` | `workspace.operator.profile-verifications.bulk` | `\App\Controllers\Workspace\OperatorController::bulkProfileVerification` | csrf invalidchars workspace-auth -> secureheaders |
| 157 | `POST` | `workspace/operator/verifikasi-profil/([^/]+)/setujui` | `workspace.operator.profile-verifications.approve` | `\App\Controllers\Workspace\OperatorController::approveVerification/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 158 | `POST` | `workspace/operator/verifikasi-profil/([^/]+)/tolak` | `workspace.operator.profile-verifications.reject` | `\App\Controllers\Workspace\OperatorController::rejectVerification/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 159 | `POST` | `workspace/operator/perubahan-profil/([^/]+)/setujui` | `workspace.operator.profile-change-requests.approve` | `\App\Controllers\Workspace\OperatorController::approveProfileChangeRequest/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 160 | `POST` | `workspace/operator/perubahan-profil/([^/]+)/tolak` | `workspace.operator.profile-change-requests.reject` | `\App\Controllers\Workspace\OperatorController::rejectProfileChangeRequest/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 161 | `POST` | `workspace/operator/perubahan-profil/([^/]+)/revisi` | `workspace.operator.profile-change-requests.revision` | `\App\Controllers\Workspace\OperatorController::revisionProfileChangeRequest/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 162 | `POST` | `workspace/operator/penugasan-trainer/assign` | `workspace.operator.class-trainers.assign` | `\App\Controllers\Workspace\OperatorController::assignTrainer` | csrf invalidchars workspace-auth -> secureheaders |
| 163 | `POST` | `workspace/operator/penugasan-trainer/revoke` | `workspace.operator.class-trainers.revoke` | `\App\Controllers\Workspace\OperatorController::revokeTrainer` | csrf invalidchars workspace-auth -> secureheaders |
| 164 | `POST` | `workspace/operator/event/penugasan-trainer` | `workspace.operator.events.assign-trainer` | `\App\Controllers\Workspace\OperatorController::assignEventTrainer` | csrf invalidchars workspace-auth -> secureheaders |
| 165 | `POST` | `workspace/operator/event/([^/]+)/registrasi/bulk` | `workspace.operator.events.registration.bulk` | `\App\Controllers\Workspace\OperatorController::bulkEventRegistration/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 166 | `POST` | `workspace/operator/event/registrasi/([^/]+)/approve` | `workspace.operator.events.registration.approve` | `\App\Controllers\Workspace\OperatorController::approveEventRegistration/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 167 | `POST` | `workspace/operator/event/registrasi/([^/]+)/reject` | `workspace.operator.events.registration.reject` | `\App\Controllers\Workspace\OperatorController::rejectEventRegistration/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 168 | `POST` | `workspace/operator/event/registrasi/([^/]+)/presensi` | `workspace.operator.events.registration.attendance` | `\App\Controllers\Workspace\OperatorController::recordEventAttendance/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 169 | `POST` | `workspace/operator/kehadiran/([^/]+)/update` | `workspace.operator.attendance.update` | `\App\Controllers\Workspace\OperatorController::updateAttendance/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 170 | `POST` | `workspace/operator/verifikasi-sertifikat/bulk-issue` | `workspace.operator.certificates.bulk-issue` | `\App\Controllers\Workspace\OperatorController::bulkIssueCertificates` | csrf invalidchars workspace-auth -> secureheaders |
| 171 | `POST` | `workspace/operator/verifikasi-sertifikat/([^/]+)/terbitkan` | `workspace.operator.certificates.issue` | `\App\Controllers\Workspace\OperatorController::issueCertificate/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 172 | `POST` | `workspace/operator/verifikasi-sertifikat/([^/]+)/batalkan` | `workspace.operator.certificates.revoke` | `\App\Controllers\Workspace\OperatorController::revokeCertificate/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 173 | `POST` | `workspace/operator/enrollment/([^/]+)/aktifkan` | `workspace.operator.enrollments.activate` | `\App\Controllers\Workspace\OperatorController::activateEnrollment/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 174 | `POST` | `workspace/operator/enrollment/([^/]+)/override-nilai` | `workspace.operator.enrollments.override-score` | `\App\Controllers\Workspace\OperatorController::overrideEnrollmentScore/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 175 | `POST` | `workspace/operator/evaluasi/submission/([^/]+)/override` | `workspace.operator.submission.override` | `\App\Controllers\Workspace\OperatorController::overrideSubmission/$1` | csrf invalidchars workspace-auth -> secureheaders |
| 176 | `POST` | `workspace/audit-rup/upload` | `workspace.rup-audit.upload` | `\App\Controllers\Workspace\RupAuditController::upload` | csrf invalidchars workspace-auth -> secureheaders |

---

## Zona 4: Dashboard Administratif Back-Office & Eksekutif (WowDash) (671 Rute)

| No | HTTP Method | Pola Rute / URI | Nama Rute | Controller Handler | Filter Sebelum / Sesudah |
|---|:---:|---|---|---|---|
| 1 | `GET` | `dashboard` | `dashboard.home` | `\App\Controllers\Admin\DashboardController::index` | csrf invalidchars auth -> secureheaders |
| 2 | `GET` | `dashboard/approvals` | `Â»` | `\App\Controllers\Admin\ApprovalController::index` | csrf invalidchars auth -> secureheaders |
| 3 | `GET` | `dashboard/approvals/history` | `Â»` | `\App\Controllers\Admin\ApprovalController::history` | csrf invalidchars auth -> secureheaders |
| 4 | `GET` | `dashboard/approvals/([^/]+)` | `Â»` | `\App\Controllers\Admin\ApprovalController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 5 | `GET` | `dashboard/approvals/([^/]+)/approve` | `Â»` | `\App\Controllers\Admin\ApprovalController::approve/$1` | csrf invalidchars auth -> secureheaders |
| 6 | `GET` | `dashboard/approvals/([^/]+)/reject` | `Â»` | `\App\Controllers\Admin\ApprovalController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 7 | `GET` | `dashboard/profile` | `dashboard.profile.index` | `\App\Controllers\Admin\ProfileController::index` | csrf invalidchars auth -> secureheaders |
| 8 | `GET` | `dashboard/profile/edit` | `dashboard.profile.edit` | `\App\Controllers\Admin\ProfileController::edit` | csrf invalidchars auth -> secureheaders |
| 9 | `GET` | `dashboard/profile/password` | `dashboard.profile.password` | `\App\Controllers\Admin\ProfileController::password` | csrf invalidchars auth -> secureheaders |
| 10 | `GET` | `dashboard/profile/preferences` | `dashboard.profile.preferences` | `\App\Controllers\Admin\ProfileController::preferences` | csrf invalidchars auth -> secureheaders |
| 11 | `GET` | `dashboard/akses/pengguna` | `dashboard.access.users` | `\App\Controllers\Admin\UserController::index` | csrf invalidchars auth -> secureheaders |
| 12 | `GET` | `dashboard/akses/pengguna/tambah` | `dashboard.access.users.create` | `\App\Controllers\Admin\UserController::create` | csrf invalidchars auth -> secureheaders |
| 13 | `GET` | `dashboard/akses/pengguna/([^/]+)/ubah` | `dashboard.access.users.edit` | `\App\Controllers\Admin\UserController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 14 | `GET` | `dashboard/akses/pengguna/([^/]+)/impact-summary` | `dashboard.access.users.impact-summary` | `\App\Controllers\Admin\UserController::impactSummary/$1` | csrf invalidchars auth -> secureheaders |
| 15 | `GET` | `dashboard/akses/pengguna/([^/]+)` | `dashboard.access.users.detail` | `\App\Controllers\Admin\UserController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 16 | `GET` | `dashboard/akses/verifikasi-profil` | `dashboard.access.profile-verifications` | `\App\Controllers\Admin\UserProfileVerificationController::index` | csrf invalidchars auth -> secureheaders |
| 17 | `GET` | `dashboard/akses/verifikasi-profil/([^/]+)` | `dashboard.access.profile-verifications.detail` | `\App\Controllers\Admin\UserProfileVerificationController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 18 | `GET` | `dashboard/akses/permohonan-perubahan-profil` | `dashboard.access.profile-change-requests` | `\App\Controllers\Admin\UserProfileVerificationController::changeRequests` | csrf invalidchars auth -> secureheaders |
| 19 | `GET` | `dashboard/akses/permohonan-perubahan-profil/([^/]+)` | `dashboard.access.profile-change-requests.detail` | `\App\Controllers\Admin\UserProfileVerificationController::changeRequestDetail/$1` | csrf invalidchars auth -> secureheaders |
| 20 | `GET` | `dashboard/akses/role` | `dashboard.access.roles` | `\App\Controllers\Admin\RoleController::index` | csrf invalidchars auth -> secureheaders |
| 21 | `GET` | `dashboard/akses/role/tambah` | `dashboard.access.roles.create` | `\App\Controllers\Admin\RoleController::create` | csrf invalidchars auth -> secureheaders |
| 22 | `GET` | `dashboard/akses/role/([^/]+)/ubah` | `dashboard.access.roles.edit` | `\App\Controllers\Admin\RoleController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 23 | `GET` | `dashboard/akses/role/matrix` | `dashboard.access.roles.matrix` | `\App\Controllers\Admin\RoleController::matrix` | csrf invalidchars auth -> secureheaders |
| 24 | `GET` | `dashboard/akses/role/([^/]+)` | `dashboard.access.roles.detail` | `\App\Controllers\Admin\RoleController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 25 | `GET` | `dashboard/akses/permission` | `dashboard.access.permissions` | `\App\Controllers\Admin\PermissionController::index` | csrf invalidchars auth -> secureheaders |
| 26 | `GET` | `dashboard/akses/permission/tambah` | `dashboard.access.permissions.create` | `\App\Controllers\Admin\PermissionController::create` | csrf invalidchars auth -> secureheaders |
| 27 | `GET` | `dashboard/akses/permission/([^/]+)` | `dashboard.access.permissions.detail` | `\App\Controllers\Admin\PermissionController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 28 | `GET` | `dashboard/akses/permohonan-role` | `dashboard.access.role-requests` | `\App\Controllers\Admin\RoleRequestReviewController::index` | csrf invalidchars auth -> secureheaders |
| 29 | `GET` | `dashboard/akses/permohonan-role/([^/]+)` | `dashboard.access.role-requests.detail` | `\App\Controllers\Admin\RoleRequestReviewController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 30 | `GET` | `dashboard/akses/unit-organisasi` | `dashboard.access.organization-units` | `\App\Controllers\Admin\OrganizationController::index` | csrf invalidchars auth -> secureheaders |
| 31 | `GET` | `dashboard/akses/unit-organisasi/tambah` | `dashboard.access.organization-units.create` | `\App\Controllers\Admin\OrganizationController::create` | csrf invalidchars auth -> secureheaders |
| 32 | `GET` | `dashboard/akses/unit-organisasi/([^/]+)/ubah` | `dashboard.access.organization-units.edit` | `\App\Controllers\Admin\OrganizationController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 33 | `GET` | `dashboard/akses/unit-organisasi/([^/]+)` | `dashboard.access.organization-units.detail` | `\App\Controllers\Admin\OrganizationController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 34 | `GET` | `dashboard/akses/peran-pengadaan` | `dashboard.access.procurement-roles` | `\App\Controllers\Admin\ProcurementRoleController::index` | csrf invalidchars auth -> secureheaders |
| 35 | `GET` | `dashboard/akses/peran-pengadaan/tambah` | `dashboard.access.procurement-roles.create` | `\App\Controllers\Admin\ProcurementRoleController::create` | csrf invalidchars auth -> secureheaders |
| 36 | `GET` | `dashboard/akses/peran-pengadaan/([^/]+)/ubah` | `dashboard.access.procurement-roles.edit` | `\App\Controllers\Admin\ProcurementRoleController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 37 | `GET` | `dashboard/akses/penugasan-pengadaan` | `dashboard.access.procurement-assignments` | `\App\Controllers\Admin\ProcurementRoleController::assignments` | csrf invalidchars auth -> secureheaders |
| 38 | `GET` | `dashboard/akses/peran-pengadaan/([^/]+)` | `dashboard.access.procurement-roles.detail` | `\App\Controllers\Admin\ProcurementRoleController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 39 | `GET` | `dashboard/akses/audit` | `dashboard.access.audit` | `\App\Controllers\Admin\AuditController::index` | csrf invalidchars auth -> secureheaders |
| 40 | `GET` | `dashboard/akses/audit/export` | `dashboard.access.audit.export` | `\App\Controllers\Admin\AuditController::exportCsv` | csrf invalidchars auth -> secureheaders |
| 41 | `GET` | `dashboard/akses/audit/([^/]+)` | `dashboard.access.audit.detail` | `\App\Controllers\Admin\AuditController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 42 | `GET` | `dashboard/pembelajaran` | `dashboard.learning.overview` | `\App\Controllers\Learning\LearningOverviewController::index` | csrf invalidchars auth -> secureheaders |
| 43 | `GET` | `dashboard/pembelajaran/program` | `dashboard.learning.programs` | `\App\Controllers\Learning\ProgramController::index` | csrf invalidchars auth -> secureheaders |
| 44 | `GET` | `dashboard/pembelajaran/program/tambah` | `dashboard.learning.programs.create` | `\App\Controllers\Learning\ProgramController::create` | csrf invalidchars auth -> secureheaders |
| 45 | `GET` | `dashboard/pembelajaran/program/([^/]+)/ubah` | `dashboard.learning.programs.edit` | `\App\Controllers\Learning\ProgramController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 46 | `GET` | `dashboard/pembelajaran/program/([^/]+)` | `dashboard.learning.programs.detail` | `\App\Controllers\Learning\ProgramController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 47 | `GET` | `dashboard/pembelajaran/course` | `dashboard.learning.courses` | `\App\Controllers\Learning\CourseController::index` | csrf invalidchars auth -> secureheaders |
| 48 | `GET` | `dashboard/pembelajaran/course/tambah` | `dashboard.learning.courses.create` | `\App\Controllers\Learning\CourseController::create` | csrf invalidchars auth -> secureheaders |
| 49 | `GET` | `dashboard/pembelajaran/course/([^/]+)/ubah` | `dashboard.learning.courses.edit` | `\App\Controllers\Learning\CourseController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 50 | `GET` | `dashboard/pembelajaran/course/([^/]+)` | `dashboard.learning.courses.detail` | `\App\Controllers\Learning\CourseController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 51 | `GET` | `dashboard/pembelajaran/kelas` | `dashboard.learning.classes` | `\App\Controllers\Learning\LearningClassController::index` | csrf invalidchars auth -> secureheaders |
| 52 | `GET` | `dashboard/pembelajaran/kelas/tambah` | `dashboard.learning.classes.create` | `\App\Controllers\Learning\LearningClassController::create` | csrf invalidchars auth -> secureheaders |
| 53 | `GET` | `dashboard/pembelajaran/kelas/([^/]+)/ubah` | `dashboard.learning.classes.edit` | `\App\Controllers\Learning\LearningClassController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 54 | `GET` | `dashboard/pembelajaran/kelas/([^/]+)` | `dashboard.learning.classes.detail` | `\App\Controllers\Learning\LearningClassController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 55 | `GET` | `dashboard/pembelajaran/kelas/([^/]+)/gradebook/export` | `dashboard.learning.classes.gradebook.export` | `\App\Controllers\Learning\LearningClassController::exportGradebook/$1` | csrf invalidchars auth -> secureheaders |
| 56 | `GET` | `dashboard/pembelajaran/trainer` | `dashboard.learning.trainers` | `\App\Controllers\Learning\TrainerAssignmentController::index` | csrf invalidchars auth -> secureheaders |
| 57 | `GET` | `dashboard/pembelajaran/enrollment` | `dashboard.learning.enrollments` | `\App\Controllers\Learning\EnrollmentController::index` | csrf invalidchars auth -> secureheaders |
| 58 | `GET` | `dashboard/pembelajaran/enrollment/([^/]+)` | `dashboard.learning.enrollments.detail` | `\App\Controllers\Learning\EnrollmentController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 59 | `GET` | `dashboard/pembelajaran/kurikulum` | `dashboard.learning.curriculum` | `\App\Controllers\Learning\CurriculumController::index` | csrf invalidchars auth -> secureheaders |
| 60 | `GET` | `dashboard/pembelajaran/kurikulum/kelas/([^/]+)` | `dashboard.learning.curriculum.class` | `\App\Controllers\Learning\CurriculumController::index/$1` | csrf invalidchars auth -> secureheaders |
| 61 | `GET` | `dashboard/pembelajaran/kurikulum/quiz/([^/]+)/soal/template` | `dashboard.learning.curriculum.question.template` | `\App\Controllers\Learning\CurriculumController::downloadQuestionTemplate/$1` | csrf invalidchars auth -> secureheaders |
| 62 | `GET` | `dashboard/pembelajaran/materi-modul` | `dashboard.learning.modules` | `\App\Controllers\Learning\ModuleController::index` | csrf invalidchars auth -> secureheaders |
| 63 | `GET` | `dashboard/pembelajaran/materi-modul/tambah` | `dashboard.learning.modules.create` | `\App\Controllers\Learning\ModuleController::create` | csrf invalidchars auth -> secureheaders |
| 64 | `GET` | `dashboard/pembelajaran/materi-modul/([^/]+)/ubah` | `dashboard.learning.modules.edit` | `\App\Controllers\Learning\ModuleController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 65 | `GET` | `dashboard/pembelajaran/materi-modul/([^/]+)` | `dashboard.learning.modules.detail` | `\App\Controllers\Learning\ModuleController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 66 | `GET` | `dashboard/pembelajaran/lesson` | `dashboard.learning.lessons` | `\App\Controllers\Learning\LessonController::index` | csrf invalidchars auth -> secureheaders |
| 67 | `GET` | `dashboard/pembelajaran/lesson/tambah` | `dashboard.learning.lessons.create` | `\App\Controllers\Learning\LessonController::create` | csrf invalidchars auth -> secureheaders |
| 68 | `GET` | `dashboard/pembelajaran/lesson/([^/]+)/ubah` | `dashboard.learning.lessons.edit` | `\App\Controllers\Learning\LessonController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 69 | `GET` | `dashboard/pembelajaran/lesson/([^/]+)` | `dashboard.learning.lessons.detail` | `\App\Controllers\Learning\LessonController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 70 | `GET` | `dashboard/pembelajaran/assessment` | `dashboard.learning.assessments` | `\App\Controllers\Learning\AssessmentManagementController::index` | csrf invalidchars auth -> secureheaders |
| 71 | `GET` | `dashboard/pembelajaran/assessment/quiz/([^/]+)/soal/template` | `dashboard.learning.assessments.quiz.question.template` | `\App\Controllers\Learning\AssessmentManagementController::downloadQuestionTemplate/$1` | csrf invalidchars auth -> secureheaders |
| 72 | `GET` | `dashboard/pembelajaran/assessment/bank-soal` | `dashboard.learning.question-bank` | `\App\Controllers\Learning\AssessmentManagementController::questionBank` | csrf invalidchars auth -> secureheaders |
| 73 | `GET` | `dashboard/pembelajaran/assessment/pre-test` | `dashboard.learning.pre-tests` | `\App\Controllers\Learning\PreTestController::index` | csrf invalidchars auth -> secureheaders |
| 74 | `GET` | `dashboard/pembelajaran/assessment/quiz` | `dashboard.learning.quizzes` | `\App\Controllers\Learning\QuizController::index` | csrf invalidchars auth -> secureheaders |
| 75 | `GET` | `dashboard/pembelajaran/assessment/post-test` | `dashboard.learning.post-tests` | `\App\Controllers\Learning\PostTestController::index` | csrf invalidchars auth -> secureheaders |
| 76 | `GET` | `dashboard/pembelajaran/penilaian` | `dashboard.learning.gradings` | `\App\Controllers\Learning\GradingController::index` | csrf invalidchars auth -> secureheaders |
| 77 | `GET` | `dashboard/pembelajaran/penilaian/([^/]+)` | `dashboard.learning.gradings.detail` | `\App\Controllers\Learning\GradingController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 78 | `GET` | `dashboard/pembelajaran/progres` | `dashboard.learning.progress` | `\App\Controllers\Learning\ProgressMonitoringController::index` | csrf invalidchars auth -> secureheaders |
| 79 | `GET` | `dashboard/pembelajaran/progres/detail/([^/]+)` | `dashboard.learning.progress.detail` | `\App\Controllers\Learning\ProgressMonitoringController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 80 | `GET` | `dashboard/pembelajaran/progres-kelulusan` | `dashboard.learning.progress-completion` | `\App\Controllers\Learning\ProgressMonitoringController::index` | csrf invalidchars auth -> secureheaders |
| 81 | `GET` | `dashboard/pembelajaran/feedback-peserta` | `dashboard.learning.participant-feedback` | `\App\Controllers\Learning\StudyController::feedback` | csrf invalidchars auth -> secureheaders |
| 82 | `GET` | `dashboard/pembelajaran/kelayakan-sertifikat` | `dashboard.learning.certificate-eligibility` | `\App\Controllers\Learning\CertificateEligibilityController::index` | csrf invalidchars auth -> secureheaders |
| 83 | `GET` | `dashboard/pembelajaran/laporan` | `dashboard.learning.reports` | `\App\Controllers\Learning\LearningReportController::index` | csrf invalidchars auth -> secureheaders |
| 84 | `GET` | `dashboard/bunga-rampai` | `dashboard.knowledge.overview` | `\App\Controllers\Knowledge\KnowledgeOverviewController::index` | csrf invalidchars auth -> secureheaders |
| 85 | `GET` | `dashboard/bunga-rampai/artikel` | `dashboard.knowledge.articles` | `\App\Controllers\Knowledge\ArticleController::index` | csrf invalidchars auth -> secureheaders |
| 86 | `GET` | `dashboard/bunga-rampai/artikel/tambah` | `dashboard.knowledge.articles.create` | `\App\Controllers\Knowledge\ArticleController::create` | csrf invalidchars auth -> secureheaders |
| 87 | `GET` | `dashboard/bunga-rampai/artikel/([^/]+)/ubah` | `dashboard.knowledge.articles.edit` | `\App\Controllers\Knowledge\ArticleController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 88 | `GET` | `dashboard/bunga-rampai/artikel/([^/]+)` | `dashboard.knowledge.articles.detail` | `\App\Controllers\Knowledge\ArticleController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 89 | `GET` | `dashboard/bunga-rampai/faq` | `dashboard.knowledge.faqs` | `\App\Controllers\Knowledge\FaqController::index` | csrf invalidchars auth -> secureheaders |
| 90 | `GET` | `dashboard/bunga-rampai/faq/tambah` | `dashboard.knowledge.faqs.create` | `\App\Controllers\Knowledge\FaqController::create` | csrf invalidchars auth -> secureheaders |
| 91 | `GET` | `dashboard/bunga-rampai/faq/([^/]+)/ubah` | `dashboard.knowledge.faqs.edit` | `\App\Controllers\Knowledge\FaqController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 92 | `GET` | `dashboard/bunga-rampai/faq/([^/]+)` | `dashboard.knowledge.faqs.detail` | `\App\Controllers\Knowledge\FaqController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 93 | `GET` | `dashboard/bunga-rampai/studi-kasus` | `dashboard.knowledge.case-studies` | `\App\Controllers\Knowledge\CaseStudyController::index` | csrf invalidchars auth -> secureheaders |
| 94 | `GET` | `dashboard/bunga-rampai/studi-kasus/tambah` | `dashboard.knowledge.case-studies.create` | `\App\Controllers\Knowledge\CaseStudyController::create` | csrf invalidchars auth -> secureheaders |
| 95 | `GET` | `dashboard/bunga-rampai/studi-kasus/([^/]+)/ubah` | `dashboard.knowledge.case-studies.edit` | `\App\Controllers\Knowledge\CaseStudyController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 96 | `GET` | `dashboard/bunga-rampai/studi-kasus/([^/]+)` | `dashboard.knowledge.case-studies.detail` | `\App\Controllers\Knowledge\CaseStudyController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 97 | `GET` | `dashboard/bunga-rampai/template-dokumen` | `dashboard.knowledge.document-templates` | `\App\Controllers\Knowledge\DocumentTemplateController::index` | csrf invalidchars auth -> secureheaders |
| 98 | `GET` | `dashboard/bunga-rampai/template-dokumen/tambah` | `dashboard.knowledge.document-templates.create` | `\App\Controllers\Knowledge\DocumentTemplateController::create` | csrf invalidchars auth -> secureheaders |
| 99 | `GET` | `dashboard/bunga-rampai/template-dokumen/([^/]+)/ubah` | `dashboard.knowledge.document-templates.edit` | `\App\Controllers\Knowledge\DocumentTemplateController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 100 | `GET` | `dashboard/bunga-rampai/template-dokumen/([^/]+)` | `dashboard.knowledge.document-templates.detail` | `\App\Controllers\Knowledge\DocumentTemplateController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 101 | `GET` | `dashboard/bunga-rampai/media-galeri` | `dashboard.knowledge.media-gallery` | `\App\Controllers\Knowledge\KnowledgeMediaController::index` | csrf invalidchars auth -> secureheaders |
| 102 | `GET` | `dashboard/bunga-rampai/media` | `dashboard.knowledge.media` | `\App\Controllers\Knowledge\KnowledgeMediaController::infographics` | csrf invalidchars auth -> secureheaders |
| 103 | `GET` | `dashboard/bunga-rampai/video` | `dashboard.knowledge.videos` | `\App\Controllers\Knowledge\KnowledgeMediaController::videos` | csrf invalidchars auth -> secureheaders |
| 104 | `GET` | `dashboard/bunga-rampai/galeri` | `dashboard.knowledge.gallery` | `\App\Controllers\Knowledge\KnowledgeMediaController::gallery` | csrf invalidchars auth -> secureheaders |
| 105 | `GET` | `dashboard/bunga-rampai/media-galeri/tambah` | `dashboard.knowledge.media-gallery.create` | `\App\Controllers\Knowledge\KnowledgeMediaController::create` | csrf invalidchars auth -> secureheaders |
| 106 | `GET` | `dashboard/bunga-rampai/media-galeri/([^/]+)/ubah` | `dashboard.knowledge.media-gallery.edit` | `\App\Controllers\Knowledge\KnowledgeMediaController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 107 | `GET` | `dashboard/bunga-rampai/media-galeri/([^/]+)` | `dashboard.knowledge.media-gallery.detail` | `\App\Controllers\Knowledge\KnowledgeMediaController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 108 | `GET` | `dashboard/bunga-rampai/item-media` | `dashboard.knowledge.media-items` | `\App\Controllers\Knowledge\KnowledgeMediaItemController::index` | csrf invalidchars auth -> secureheaders |
| 109 | `GET` | `dashboard/bunga-rampai/item-media/([^/]+)` | `dashboard.knowledge.media-items.detail` | `\App\Controllers\Knowledge\KnowledgeMediaItemController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 110 | `GET` | `dashboard/bunga-rampai/carousel-konten-unggulan` | `dashboard.knowledge.carousel` | `\App\Controllers\Knowledge\KnowledgeCarouselController::index` | csrf invalidchars auth -> secureheaders |
| 111 | `GET` | `dashboard/bunga-rampai/carousel-konten-unggulan/tambah` | `dashboard.knowledge.carousel.create` | `\App\Controllers\Knowledge\KnowledgeCarouselController::create` | csrf invalidchars auth -> secureheaders |
| 112 | `GET` | `dashboard/bunga-rampai/kategori` | `dashboard.knowledge.categories` | `\App\Controllers\Knowledge\CategoryController::index` | csrf invalidchars auth -> secureheaders |
| 113 | `GET` | `dashboard/bunga-rampai/kategori/tambah` | `dashboard.knowledge.categories.create` | `\App\Controllers\Knowledge\CategoryController::create` | csrf invalidchars auth -> secureheaders |
| 114 | `GET` | `dashboard/bunga-rampai/kategori/([^/]+)/ubah` | `dashboard.knowledge.categories.edit` | `\App\Controllers\Knowledge\CategoryController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 115 | `GET` | `dashboard/bunga-rampai/tag` | `dashboard.knowledge.tags` | `\App\Controllers\Knowledge\TagController::index` | csrf invalidchars auth -> secureheaders |
| 116 | `GET` | `dashboard/bunga-rampai/tag/tambah` | `dashboard.knowledge.tags.create` | `\App\Controllers\Knowledge\TagController::create` | csrf invalidchars auth -> secureheaders |
| 117 | `GET` | `dashboard/bunga-rampai/tag/([^/]+)/ubah` | `dashboard.knowledge.tags.edit` | `\App\Controllers\Knowledge\TagController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 118 | `GET` | `dashboard/bunga-rampai/bookmark` | `dashboard.knowledge.bookmarks` | `\App\Controllers\Knowledge\BookmarkController::index` | csrf invalidchars auth -> secureheaders |
| 119 | `GET` | `dashboard/regulasi` | `dashboard.regulations.root` | `\App\Controllers\Admin\RegulationController::index` | csrf invalidchars auth -> secureheaders |
| 120 | `GET` | `dashboard/regulasi/dokumen` | `dashboard.regulations.index` | `\App\Controllers\Admin\RegulationController::index` | csrf invalidchars auth -> secureheaders |
| 121 | `GET` | `dashboard/regulasi/dokumen/tambah` | `dashboard.regulations.create` | `\App\Controllers\Admin\RegulationController::create` | csrf invalidchars auth -> secureheaders |
| 122 | `GET` | `dashboard/regulasi/dokumen/([^/]+)/ubah` | `dashboard.regulations.edit` | `\App\Controllers\Admin\RegulationController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 123 | `GET` | `dashboard/regulasi/dokumen/([^/]+)` | `dashboard.regulations.detail` | `\App\Controllers\Admin\RegulationController::show/$1` | csrf invalidchars auth -> secureheaders |
| 124 | `GET` | `dashboard/regulasi/kategori` | `dashboard.regulations.categories` | `\App\Controllers\Admin\RegulationController::categories` | csrf invalidchars auth -> secureheaders |
| 125 | `GET` | `dashboard/regulasi/kategori/tambah` | `dashboard.regulations.categories.create` | `\App\Controllers\Admin\RegulationController::categoryCreate` | csrf invalidchars auth -> secureheaders |
| 126 | `GET` | `dashboard/regulasi/kategori/([^/]+)/ubah` | `dashboard.regulations.categories.edit` | `\App\Controllers\Admin\RegulationController::categoryEdit/$1` | csrf invalidchars auth -> secureheaders |
| 127 | `GET` | `dashboard/regulasi/kategori/([^/]+)` | `dashboard.regulations.categories.detail` | `\App\Controllers\Admin\RegulationController::categoryShow/$1` | csrf invalidchars auth -> secureheaders |
| 128 | `GET` | `dashboard/regulasi/versi` | `dashboard.regulations.versions` | `\App\Controllers\Admin\RegulationController::versions` | csrf invalidchars auth -> secureheaders |
| 129 | `GET` | `dashboard/regulasi/versi/([^/]+)` | `dashboard.regulations.versions.detail` | `\App\Controllers\Admin\RegulationController::versionShow/$1` | csrf invalidchars auth -> secureheaders |
| 130 | `GET` | `dashboard/regulasi/relasi` | `dashboard.regulations.relations` | `\App\Controllers\Admin\RegulationController::relations` | csrf invalidchars auth -> secureheaders |
| 131 | `GET` | `dashboard/regulasi/relasi/tambah` | `dashboard.regulations.relations.create` | `\App\Controllers\Admin\RegulationController::relationCreate` | csrf invalidchars auth -> secureheaders |
| 132 | `GET` | `dashboard/regulasi/relasi/([^/]+)` | `dashboard.regulations.relations.detail` | `\App\Controllers\Admin\RegulationController::relationShow/$1` | csrf invalidchars auth -> secureheaders |
| 133 | `GET` | `dashboard/regulasi/tahapan-pbj` | `dashboard.regulations.procurement-stages` | `\App\Controllers\Admin\RegulationController::procurementStages` | csrf invalidchars auth -> secureheaders |
| 134 | `GET` | `dashboard/regulasi/tahapan-pbj/tambah` | `dashboard.regulations.procurement-stages.create` | `\App\Controllers\Admin\RegulationController::procurementStageCreate` | csrf invalidchars auth -> secureheaders |
| 135 | `GET` | `dashboard/regulasi/tahapan-pbj/([^/]+)/ubah` | `dashboard.regulations.procurement-stages.edit` | `\App\Controllers\Admin\RegulationController::procurementStageEdit/$1` | csrf invalidchars auth -> secureheaders |
| 136 | `GET` | `dashboard/regulasi/kompetensi-terkait` | `dashboard.regulations.competencies` | `\App\Controllers\Admin\RegulationController::competencies` | csrf invalidchars auth -> secureheaders |
| 137 | `GET` | `dashboard/regulasi/kompetensi/tambah` | `dashboard.regulations.competencies.create` | `\App\Controllers\Admin\RegulationController::competencyCreate` | csrf invalidchars auth -> secureheaders |
| 138 | `GET` | `dashboard/regulasi/kompetensi/([^/]+)/ubah` | `dashboard.regulations.competencies.edit` | `\App\Controllers\Admin\RegulationController::competencyEdit/$1` | csrf invalidchars auth -> secureheaders |
| 139 | `GET` | `dashboard/regulasi/log-download` | `dashboard.regulations.download-logs` | `\App\Controllers\Admin\RegulationController::downloadLogs` | csrf invalidchars auth -> secureheaders |
| 140 | `GET` | `dashboard/komunikasi/berita` | `dashboard.communication.news` | `\App\Controllers\Communication\NewsController::index` | csrf invalidchars auth -> secureheaders |
| 141 | `GET` | `dashboard/komunikasi/berita/tambah` | `dashboard.communication.news.create` | `\App\Controllers\Communication\NewsController::create` | csrf invalidchars auth -> secureheaders |
| 142 | `GET` | `dashboard/komunikasi/berita/([^/]+)/ubah` | `dashboard.communication.news.edit` | `\App\Controllers\Communication\NewsController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 143 | `GET` | `dashboard/komunikasi/berita/([^/]+)` | `dashboard.communication.news.detail` | `\App\Controllers\Communication\NewsController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 144 | `GET` | `dashboard/komunikasi/kategori-berita` | `dashboard.communication.news-categories` | `\App\Controllers\Communication\NewsController::categories` | csrf invalidchars auth -> secureheaders |
| 145 | `GET` | `dashboard/komunikasi/kategori-berita/tambah` | `dashboard.communication.news-categories.create` | `\App\Controllers\Communication\NewsController::categoryCreate` | csrf invalidchars auth -> secureheaders |
| 146 | `GET` | `dashboard/komunikasi/kategori-berita/([^/]+)/ubah` | `dashboard.communication.news-categories.edit` | `\App\Controllers\Communication\NewsController::categoryEdit/$1` | csrf invalidchars auth -> secureheaders |
| 147 | `GET` | `dashboard/komunikasi/event-agenda` | `dashboard.communication.events` | `\App\Controllers\Communication\EventController::index` | csrf invalidchars auth -> secureheaders |
| 148 | `GET` | `dashboard/komunikasi/event-agenda/tambah` | `dashboard.communication.events.create` | `\App\Controllers\Communication\EventController::create` | csrf invalidchars auth -> secureheaders |
| 149 | `GET` | `dashboard/komunikasi/event-agenda/([^/]+)/ubah` | `dashboard.communication.events.edit` | `\App\Controllers\Communication\EventController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 150 | `GET` | `dashboard/komunikasi/event-agenda/([^/]+)` | `dashboard.communication.events.detail` | `\App\Controllers\Communication\EventController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 151 | `GET` | `dashboard/komunikasi/registrasi-event` | `dashboard.communication.event-registrations` | `\App\Controllers\Communication\EventRegistrationController::index` | csrf invalidchars auth -> secureheaders |
| 152 | `GET` | `dashboard/komunikasi/registrasi-event/([^/]+)` | `dashboard.communication.event-registrations.detail` | `\App\Controllers\Communication\EventRegistrationController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 153 | `GET` | `dashboard/komunikasi/kehadiran` | `dashboard.communication.attendance` | `\App\Controllers\Communication\AttendanceController::index` | csrf invalidchars auth -> secureheaders |
| 154 | `GET` | `dashboard/komunikasi/waitlist-pembatalan` | `dashboard.communication.waitlist-cancellations` | `\App\Controllers\Communication\EventRegistrationController::waitlistAndCancellation` | csrf invalidchars auth -> secureheaders |
| 155 | `GET` | `dashboard/komunikasi/pesan-masuk` | `dashboard.communication.inbox` | `\App\Controllers\Admin\Communication\ContactMessageController::index` | csrf invalidchars auth -> secureheaders |
| 156 | `GET` | `dashboard/komunikasi/pesan-masuk/detail/([^/]+)` | `dashboard.communication.inbox.detail` | `\App\Controllers\Admin\Communication\ContactMessageController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 157 | `GET` | `dashboard/komunikasi/pesan-masuk/hapus/([^/]+)` | `dashboard.communication.inbox.delete` | `\App\Controllers\Admin\Communication\ContactMessageController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 158 | `GET` | `dashboard/komunikasi/whatsapp-widget` | `dashboard.communication.helpdesk` | `\App\Controllers\Admin\Communication\HelpdeskWidgetController::index` | csrf invalidchars auth -> secureheaders |
| 159 | `GET` | `dashboard/komunikasi/whatsapp-widget/hapus-topic/([^/]+)` | `dashboard.communication.helpdesk.topic.delete` | `\App\Controllers\Admin\Communication\HelpdeskWidgetController::deleteTopic/$1` | csrf invalidchars auth -> secureheaders |
| 160 | `GET` | `dashboard/komunikasi/whatsapp-widget/toggle-topic/([^/]+)` | `dashboard.communication.helpdesk.topic.toggle` | `\App\Controllers\Admin\Communication\HelpdeskWidgetController::toggleTopic/$1` | csrf invalidchars auth -> secureheaders |
| 161 | `GET` | `dashboard/komunikasi/newsletter` | `dashboard.communication.newsletter` | `\App\Controllers\Admin\Communication\NewsletterController::index` | csrf invalidchars auth -> secureheaders |
| 162 | `GET` | `dashboard/komunikasi/newsletter/export` | `dashboard.communication.newsletter.export` | `\App\Controllers\Admin\Communication\NewsletterController::exportCsv` | csrf invalidchars auth -> secureheaders |
| 163 | `GET` | `dashboard/komunikasi/newsletter/hapus/([^/]+)` | `dashboard.communication.newsletter.delete` | `\App\Controllers\Admin\Communication\NewsletterController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 164 | `GET` | `dashboard/komunikasi/email` | `dashboard.communication.email` | `\App\Controllers\Admin\Communication\EmailBroadcastController::index` | csrf invalidchars auth -> secureheaders |
| 165 | `GET` | `dashboard/komunikasi/email/count-audience` | `dashboard.communication.email.count-audience` | `\App\Controllers\Admin\Communication\EmailBroadcastController::countAudience` | csrf invalidchars auth -> secureheaders |
| 166 | `GET` | `dashboard/sertifikat` | `dashboard.certificates.overview` | `\App\Controllers\Certificate\CertificateController::overview` | csrf invalidchars auth -> secureheaders |
| 167 | `GET` | `dashboard/sertifikat/overview` | `Â»` | `\App\Controllers\Certificate\CertificateController::overview` | csrf invalidchars auth -> secureheaders |
| 168 | `GET` | `dashboard/sertifikat/learning` | `dashboard.certificates.learning` | `\App\Controllers\Certificate\CertificateLearningController::index` | csrf invalidchars auth -> secureheaders |
| 169 | `GET` | `dashboard/sertifikat/learning/kelas/([^/]+)` | `dashboard.certificates.learning.class-detail` | `\App\Controllers\Certificate\CertificateLearningController::classDetail/$1` | csrf invalidchars auth -> secureheaders |
| 170 | `GET` | `dashboard/sertifikat/event` | `dashboard.certificates.events` | `\App\Controllers\Certificate\CertificateEventController::index` | csrf invalidchars auth -> secureheaders |
| 171 | `GET` | `dashboard/sertifikat/event/([^/]+)` | `dashboard.certificates.events.detail` | `\App\Controllers\Certificate\CertificateEventController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 172 | `GET` | `dashboard/sertifikat/pengajuan-event` | `dashboard.certificates.event-approval` | `\App\Controllers\Certificate\CertificateEventController::select` | csrf invalidchars auth -> secureheaders |
| 173 | `GET` | `dashboard/sertifikat/pengajuan-event/([^/]+)/preview` | `dashboard.certificates.event-approval.preview` | `\App\Controllers\Certificate\CertificateEventController::preview/$1` | csrf invalidchars auth -> secureheaders |
| 174 | `GET` | `dashboard/sertifikat/batch` | `dashboard.certificates.batch` | `\App\Controllers\Certificate\CertificateBatchController::index` | csrf invalidchars auth -> secureheaders |
| 175 | `GET` | `dashboard/sertifikat/template` | `dashboard.certificates.templates` | `\App\Controllers\Certificate\CertificateTemplateController::index` | csrf invalidchars auth -> secureheaders |
| 176 | `GET` | `dashboard/sertifikat/template/tambah` | `dashboard.certificates.templates.create` | `\App\Controllers\Certificate\CertificateTemplateController::create` | csrf invalidchars auth -> secureheaders |
| 177 | `GET` | `dashboard/sertifikat/template/([^/]+)/background-file` | `dashboard.certificates.templates.background-file` | `\App\Controllers\Certificate\CertificateTemplateController::serveBackgroundFile/$1` | csrf invalidchars auth -> secureheaders |
| 178 | `GET` | `dashboard/sertifikat/template/([^/]+)/preview` | `dashboard.certificates.templates.preview` | `\App\Controllers\Certificate\CertificateTemplateController::preview/$1` | csrf invalidchars auth -> secureheaders |
| 179 | `GET` | `dashboard/sertifikat/template/([^/]+)/preview-pdf` | `dashboard.certificates.templates.preview-pdf` | `\App\Controllers\Certificate\CertificateTemplateController::previewPdf/$1` | csrf invalidchars auth -> secureheaders |
| 180 | `GET` | `dashboard/sertifikat/template/([^/]+)/ubah` | `dashboard.certificates.templates.edit` | `\App\Controllers\Certificate\CertificateTemplateController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 181 | `GET` | `dashboard/sertifikat/template/([^/]+)` | `dashboard.certificates.templates.detail` | `\App\Controllers\Certificate\CertificateTemplateController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 182 | `GET` | `dashboard/sertifikat/verifikasi` | `dashboard.certificates.verification` | `\App\Controllers\Certificate\CertificateController::verification` | csrf invalidchars auth -> secureheaders |
| 183 | `GET` | `dashboard/sertifikat/verification` | `Â»` | `\App\Controllers\Certificate\CertificateController::verification` | csrf invalidchars auth -> secureheaders |
| 184 | `GET` | `dashboard/sertifikat/riwayat` | `dashboard.certificates.riwayat` | `\App\Controllers\Certificate\CertificateController::riwayat` | csrf invalidchars auth -> secureheaders |
| 185 | `GET` | `dashboard/sertifikat/index` | `dashboard.certificates.index` | `\App\Controllers\Certificate\CertificateController::riwayat` | csrf invalidchars auth -> secureheaders |
| 186 | `GET` | `dashboard/sertifikat/approval` | `dashboard.certificates.approvals` | `\App\Controllers\Admin\ApprovalController::index` | csrf invalidchars auth -> secureheaders |
| 187 | `GET` | `dashboard/sertifikat/approval/([^/]+)` | `dashboard.certificates.approvals.detail` | `\App\Controllers\Admin\ApprovalController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 188 | `GET` | `dashboard/sertifikat/approval/([^/]+)/terbitkan` | `dashboard.certificates.approvals.issue` | `\App\Controllers\Admin\ApprovalController::approve/$1` | csrf invalidchars auth -> secureheaders |
| 189 | `GET` | `dashboard/sertifikat/approval/([^/]+)/tolak` | `dashboard.certificates.approvals.reject` | `\App\Controllers\Admin\ApprovalController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 190 | `GET` | `dashboard/sertifikat/([^/]+)/ubah` | `dashboard.certificates.edit` | `\App\Controllers\Certificate\CertificateController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 191 | `GET` | `dashboard/sertifikat/([^/]+)/unduh` | `dashboard.certificates.download` | `\App\Controllers\Certificate\CertificateDownloadController::download/$1` | csrf invalidchars auth -> secureheaders |
| 192 | `GET` | `dashboard/sertifikat/([^/]+)/preview` | `dashboard.certificates.preview` | `\App\Controllers\Certificate\CertificateController::preview/$1` | csrf invalidchars auth -> secureheaders |
| 193 | `GET` | `dashboard/sertifikat/([^/]+)` | `dashboard.certificates.detail` | `\App\Controllers\Certificate\CertificateController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 194 | `GET` | `dashboard/intelligence` | `dashboard.intelligence.home` | `\App\Controllers\Intelligence\IntelligenceDashboardController::index` | csrf invalidchars auth -> secureheaders |
| 195 | `GET` | `dashboard/intelligence/opd-unit` | `dashboard.intelligence.organization-units` | `\App\Controllers\Intelligence\OpdController::index` | csrf invalidchars auth -> secureheaders |
| 196 | `GET` | `dashboard/intelligence/opd-unit/tambah` | `dashboard.intelligence.organization-units.create` | `\App\Controllers\Intelligence\OpdController::create` | csrf invalidchars auth -> secureheaders |
| 197 | `GET` | `dashboard/intelligence/opd-unit/([^/]+)` | `dashboard.intelligence.organization-units.detail` | `\App\Controllers\Intelligence\OpdController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 198 | `GET` | `dashboard/intelligence/sdm-jf-ppbj` | `dashboard.intelligence.personnel` | `\App\Controllers\Intelligence\JfPpbjController::index` | csrf invalidchars auth -> secureheaders |
| 199 | `GET` | `dashboard/intelligence/sdm-jf-ppbj/tambah` | `dashboard.intelligence.personnel.create` | `\App\Controllers\Intelligence\JfPpbjController::create` | csrf invalidchars auth -> secureheaders |
| 200 | `GET` | `dashboard/intelligence/sdm-jf-ppbj/([^/]+)` | `dashboard.intelligence.personnel.detail` | `\App\Controllers\Intelligence\JfPpbjController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 201 | `GET` | `dashboard/intelligence/riwayat-jf-ppbj` | `dashboard.intelligence.personnel-histories` | `\App\Controllers\Intelligence\JfPpbjController::histories` | csrf invalidchars auth -> secureheaders |
| 202 | `GET` | `dashboard/intelligence/jenjang-jf-ppbj` | `dashboard.intelligence.personnel-levels` | `\App\Controllers\Intelligence\JfPpbjController::levels` | csrf invalidchars auth -> secureheaders |
| 203 | `GET` | `dashboard/intelligence/sertifikat-kompetensi` | `dashboard.intelligence.competency-certificates` | `\App\Controllers\Intelligence\CompetencyCertificateController::index` | csrf invalidchars auth -> secureheaders |
| 204 | `GET` | `dashboard/intelligence/sertifikat-kompetensi/tambah` | `dashboard.intelligence.competency-certificates.create` | `\App\Controllers\Intelligence\CompetencyCertificateController::create` | csrf invalidchars auth -> secureheaders |
| 205 | `GET` | `dashboard/intelligence/sertifikat-kompetensi/([^/]+)` | `dashboard.intelligence.competency-certificates.detail` | `\App\Controllers\Intelligence\CompetencyCertificateController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 206 | `GET` | `dashboard/intelligence/jenis-sertifikat-kompetensi` | `dashboard.intelligence.competency-certificate-types` | `\App\Controllers\Intelligence\CompetencyCertificateController::types` | csrf invalidchars auth -> secureheaders |
| 207 | `GET` | `dashboard/intelligence/gap-kompetensi` | `dashboard.intelligence.competency-gap` | `\App\Controllers\Intelligence\IntelligenceDashboardController::competencyGap` | csrf invalidchars auth -> secureheaders |
| 208 | `GET` | `dashboard/laporan/pembelajaran` | `dashboard.reports.learning` | `\App\Controllers\Admin\DashboardController::learningReport` | csrf invalidchars auth -> secureheaders |
| 209 | `GET` | `dashboard/laporan/event` | `dashboard.reports.events` | `\App\Controllers\Admin\DashboardController::eventReport` | csrf invalidchars auth -> secureheaders |
| 210 | `GET` | `dashboard/laporan/sertifikat` | `dashboard.reports.certificates` | `\App\Controllers\Admin\DashboardController::certificateReport` | csrf invalidchars auth -> secureheaders |
| 211 | `GET` | `dashboard/laporan/kompetensi` | `dashboard.reports.competency` | `\App\Controllers\Admin\DashboardController::competencyReport` | csrf invalidchars auth -> secureheaders |
| 212 | `GET` | `dashboard/laporan/pengguna` | `dashboard.reports.users` | `\App\Controllers\Admin\DashboardController::userReport` | csrf invalidchars auth -> secureheaders |
| 213 | `GET` | `dashboard/laporan/riwayat-export` | `dashboard.reports.export-history` | `\App\Controllers\Admin\DashboardController::exportHistory` | csrf invalidchars auth -> secureheaders |
| 214 | `GET` | `dashboard/laporan/export/([^/]+)` | `dashboard.reports.export.download` | `\App\Controllers\Admin\DashboardController::exportReport/$1` | csrf invalidchars auth -> secureheaders |
| 215 | `GET` | `dashboard/landing` | `dashboard.landing.index` | `(Closure)` | csrf invalidchars auth -> secureheaders |
| 216 | `GET` | `dashboard/landing/sorotan-carousel` | `dashboard.landing.spotlight_hub` | `\App\Controllers\Admin\Landing\SpotlightHubController::index` | csrf invalidchars auth -> secureheaders |
| 217 | `GET` | `dashboard/landing/sorotan-carousel/search-available` | `dashboard.landing.spotlight_hub.search` | `\App\Controllers\Admin\Landing\SpotlightHubController::searchAvailable` | csrf invalidchars auth -> secureheaders |
| 218 | `GET` | `dashboard/landing/slider` | `dashboard.landing.sliders` | `\App\Controllers\Admin\Landing\SliderController::index` | csrf invalidchars auth -> secureheaders |
| 219 | `GET` | `dashboard/landing/slider/tambah` | `dashboard.landing.sliders.create` | `\App\Controllers\Admin\Landing\SliderController::create` | csrf invalidchars auth -> secureheaders |
| 220 | `GET` | `dashboard/landing/slider/ubah/([^/]+)` | `dashboard.landing.sliders.edit` | `\App\Controllers\Admin\Landing\SliderController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 221 | `GET` | `dashboard/landing/slider/edit/([^/]+)` | `dashboard.landing.sliders.edit_alias` | `\App\Controllers\Admin\Landing\SliderController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 222 | `GET` | `dashboard/landing/slider/duplikat/([^/]+)` | `dashboard.landing.sliders.duplicate` | `\App\Controllers\Admin\Landing\SliderController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 223 | `GET` | `dashboard/landing/slider/hapus/([^/]+)` | `dashboard.landing.sliders.delete` | `\App\Controllers\Admin\Landing\SliderController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 224 | `GET` | `dashboard/landing/slider/toggle/([^/]+)` | `dashboard.landing.sliders.toggle` | `\App\Controllers\Admin\Landing\SliderController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 225 | `GET` | `dashboard/landing/pengumuman` | `dashboard.landing.announcements` | `\App\Controllers\Admin\Landing\AnnouncementController::index` | csrf invalidchars auth -> secureheaders |
| 226 | `GET` | `dashboard/landing/pengumuman/tambah` | `dashboard.landing.announcements.create` | `\App\Controllers\Admin\Landing\AnnouncementController::create` | csrf invalidchars auth -> secureheaders |
| 227 | `GET` | `dashboard/landing/pengumuman/ubah/([^/]+)` | `dashboard.landing.announcements.edit` | `\App\Controllers\Admin\Landing\AnnouncementController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 228 | `GET` | `dashboard/landing/pengumuman/edit/([^/]+)` | `dashboard.landing.announcements.edit_alias` | `\App\Controllers\Admin\Landing\AnnouncementController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 229 | `GET` | `dashboard/landing/pengumuman/duplikat/([^/]+)` | `dashboard.landing.announcements.duplicate` | `\App\Controllers\Admin\Landing\AnnouncementController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 230 | `GET` | `dashboard/landing/pengumuman/hapus/([^/]+)` | `dashboard.landing.announcements.delete` | `\App\Controllers\Admin\Landing\AnnouncementController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 231 | `GET` | `dashboard/landing/pengumuman/toggle/([^/]+)` | `dashboard.landing.announcements.toggle` | `\App\Controllers\Admin\Landing\AnnouncementController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 232 | `GET` | `dashboard/landing/profil-lembaga` | `dashboard.landing.profile` | `\App\Controllers\Admin\Landing\InstitutionProfileController::index` | csrf invalidchars auth -> secureheaders |
| 233 | `GET` | `dashboard/landing/profil-lembaga/reset` | `dashboard.landing.profile.reset` | `\App\Controllers\Admin\Landing\InstitutionProfileController::resetDefault` | csrf invalidchars auth -> secureheaders |
| 234 | `GET` | `dashboard/landing/pilar-hierarki` | `dashboard.landing.highlights` | `\App\Controllers\Admin\Landing\HighlightFeatureController::index` | csrf invalidchars auth -> secureheaders |
| 235 | `GET` | `dashboard/landing/pilar-hierarki/tambah` | `dashboard.landing.highlights.create` | `\App\Controllers\Admin\Landing\HighlightFeatureController::create` | csrf invalidchars auth -> secureheaders |
| 236 | `GET` | `dashboard/landing/pilar-hierarki/ubah/([^/]+)` | `dashboard.landing.highlights.edit` | `\App\Controllers\Admin\Landing\HighlightFeatureController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 237 | `GET` | `dashboard/landing/pilar-hierarki/edit/([^/]+)` | `dashboard.landing.highlights.edit_alias` | `\App\Controllers\Admin\Landing\HighlightFeatureController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 238 | `GET` | `dashboard/landing/pilar-hierarki/duplikat/([^/]+)` | `dashboard.landing.highlights.duplicate` | `\App\Controllers\Admin\Landing\HighlightFeatureController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 239 | `GET` | `dashboard/landing/pilar-hierarki/muat-rekomendasi/([^/]+)` | `dashboard.landing.highlights.recommendations` | `\App\Controllers\Admin\Landing\HighlightFeatureController::seedRecommendations/$1` | csrf invalidchars auth -> secureheaders |
| 240 | `GET` | `dashboard/landing/pilar-hierarki/hapus/([^/]+)` | `dashboard.landing.highlights.delete` | `\App\Controllers\Admin\Landing\HighlightFeatureController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 241 | `GET` | `dashboard/landing/pilar-hierarki/toggle/([^/]+)` | `dashboard.landing.highlights.toggle` | `\App\Controllers\Admin\Landing\HighlightFeatureController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 242 | `GET` | `dashboard/landing/portal-eksternal` | `dashboard.landing.portals` | `\App\Controllers\Admin\Landing\ExternalPortalController::index` | csrf invalidchars auth -> secureheaders |
| 243 | `GET` | `dashboard/landing/portal-eksternal/tambah` | `dashboard.landing.portals.create` | `\App\Controllers\Admin\Landing\ExternalPortalController::create` | csrf invalidchars auth -> secureheaders |
| 244 | `GET` | `dashboard/landing/portal-eksternal/ubah/([^/]+)` | `dashboard.landing.portals.edit` | `\App\Controllers\Admin\Landing\ExternalPortalController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 245 | `GET` | `dashboard/landing/portal-eksternal/edit/([^/]+)` | `dashboard.landing.portals.edit_alias` | `\App\Controllers\Admin\Landing\ExternalPortalController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 246 | `GET` | `dashboard/landing/portal-eksternal/duplikat/([^/]+)` | `dashboard.landing.portals.duplicate` | `\App\Controllers\Admin\Landing\ExternalPortalController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 247 | `GET` | `dashboard/landing/portal-eksternal/muat-rekomendasi` | `dashboard.landing.portals.recommendations` | `\App\Controllers\Admin\Landing\ExternalPortalController::seedRecommendations` | csrf invalidchars auth -> secureheaders |
| 248 | `GET` | `dashboard/landing/portal-eksternal/hapus/([^/]+)` | `dashboard.landing.portals.delete` | `\App\Controllers\Admin\Landing\ExternalPortalController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 249 | `GET` | `dashboard/landing/portal-eksternal/toggle/([^/]+)` | `dashboard.landing.portals.toggle` | `\App\Controllers\Admin\Landing\ExternalPortalController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 250 | `GET` | `dashboard/landing/testimoni` | `dashboard.landing.testimonials` | `\App\Controllers\Admin\Landing\TestimonialController::index` | csrf invalidchars auth -> secureheaders |
| 251 | `GET` | `dashboard/landing/testimoni/tambah` | `dashboard.landing.testimonials.create` | `\App\Controllers\Admin\Landing\TestimonialController::create` | csrf invalidchars auth -> secureheaders |
| 252 | `GET` | `dashboard/landing/testimoni/ubah/([^/]+)` | `dashboard.landing.testimonials.edit` | `\App\Controllers\Admin\Landing\TestimonialController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 253 | `GET` | `dashboard/landing/testimoni/edit/([^/]+)` | `Â»` | `\App\Controllers\Admin\Landing\TestimonialController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 254 | `GET` | `dashboard/landing/testimoni/duplikat/([^/]+)` | `dashboard.landing.testimonials.duplicate` | `\App\Controllers\Admin\Landing\TestimonialController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 255 | `GET` | `dashboard/landing/testimoni/muat-rekomendasi` | `dashboard.landing.testimonials.recommendations` | `\App\Controllers\Admin\Landing\TestimonialController::seedRecommendations` | csrf invalidchars auth -> secureheaders |
| 256 | `GET` | `dashboard/landing/testimoni/hapus/([^/]+)` | `dashboard.landing.testimonials.delete` | `\App\Controllers\Admin\Landing\TestimonialController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 257 | `GET` | `dashboard/landing/testimoni/toggle/([^/]+)` | `dashboard.landing.testimonials.toggle` | `\App\Controllers\Admin\Landing\TestimonialController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 258 | `GET` | `dashboard/landing/tim` | `dashboard.landing.team` | `\App\Controllers\Admin\Landing\TeamMemberController::index` | csrf invalidchars auth -> secureheaders |
| 259 | `GET` | `dashboard/landing/tim/tambah` | `dashboard.landing.team.create` | `\App\Controllers\Admin\Landing\TeamMemberController::create` | csrf invalidchars auth -> secureheaders |
| 260 | `GET` | `dashboard/landing/tim/ubah/([^/]+)` | `dashboard.landing.team.edit` | `\App\Controllers\Admin\Landing\TeamMemberController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 261 | `GET` | `dashboard/landing/tim/edit/([^/]+)` | `Â»` | `\App\Controllers\Admin\Landing\TeamMemberController::edit/$1` | csrf invalidchars auth -> secureheaders |
| 262 | `GET` | `dashboard/landing/tim/duplikat/([^/]+)` | `dashboard.landing.team.duplicate` | `\App\Controllers\Admin\Landing\TeamMemberController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 263 | `GET` | `dashboard/landing/tim/muat-rekomendasi` | `dashboard.landing.team.recommendations` | `\App\Controllers\Admin\Landing\TeamMemberController::seedRecommendations` | csrf invalidchars auth -> secureheaders |
| 264 | `GET` | `dashboard/landing/tim/hapus/([^/]+)` | `dashboard.landing.team.delete` | `\App\Controllers\Admin\Landing\TeamMemberController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 265 | `GET` | `dashboard/landing/tim/toggle/([^/]+)` | `dashboard.landing.team.toggle` | `\App\Controllers\Admin\Landing\TeamMemberController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 266 | `GET` | `dashboard/survei-kepuasan` | `dashboard.survey.index` | `\App\Controllers\Admin\Survey\SatisfactionSurveyAdminController::index` | csrf invalidchars auth -> secureheaders |
| 267 | `GET` | `dashboard/survei-kepuasan/hasil` | `Â»` | `\App\Controllers\Admin\Survey\SatisfactionSurveyAdminController::index` | csrf invalidchars auth -> secureheaders |
| 268 | `GET` | `dashboard/survei-kepuasan/export` | `dashboard.survey.export` | `\App\Controllers\Admin\Survey\SatisfactionSurveyAdminController::exportCsv` | csrf invalidchars auth -> secureheaders |
| 269 | `GET` | `dashboard/sistem/approval` | `dashboard.system.approvals` | `\App\Controllers\Admin\ApprovalController::index` | csrf invalidchars auth -> secureheaders |
| 270 | `GET` | `dashboard/sistem/approval/riwayat` | `dashboard.system.approvals.history` | `\App\Controllers\Admin\ApprovalController::history` | csrf invalidchars auth -> secureheaders |
| 271 | `GET` | `dashboard/sistem/approval/([^/]+)/setujui` | `dashboard.system.approvals.approve` | `\App\Controllers\Admin\ApprovalController::approve/$1` | csrf invalidchars auth -> secureheaders |
| 272 | `GET` | `dashboard/sistem/approval/([^/]+)/tolak` | `dashboard.system.approvals.reject` | `\App\Controllers\Admin\ApprovalController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 273 | `GET` | `dashboard/sistem/approval/([^/]+)` | `dashboard.system.approvals.detail` | `\App\Controllers\Admin\ApprovalController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 274 | `GET` | `dashboard/sistem/audit-trail` | `dashboard.system.audit-trail` | `\App\Controllers\Admin\AuditController::index` | csrf invalidchars auth -> secureheaders |
| 275 | `GET` | `dashboard/sistem/audit-trail/export` | `dashboard.system.audit-trail.export` | `\App\Controllers\Admin\AuditController::exportCsv` | csrf invalidchars auth -> secureheaders |
| 276 | `GET` | `dashboard/sistem/audit-trail/([^/]+)` | `dashboard.system.audit-trail.detail` | `\App\Controllers\Admin\AuditController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 277 | `GET` | `dashboard/sistem/notifikasi` | `dashboard.system.notifications` | `\App\Controllers\Admin\NotificationController::index` | csrf invalidchars auth -> secureheaders |
| 278 | `GET` | `dashboard/sistem/notifikasi/export` | `dashboard.system.notifications.export` | `\App\Controllers\Admin\NotificationController::exportCsv` | csrf invalidchars auth -> secureheaders |
| 279 | `GET` | `dashboard/sistem/notifikasi/([^/]+)` | `dashboard.system.notifications.detail` | `\App\Controllers\Admin\NotificationController::detail/$1` | csrf invalidchars auth -> secureheaders |
| 280 | `GET` | `dashboard/sistem/notifikasi-api/feed` | `dashboard.system.notifications.feed` | `\App\Controllers\Admin\NotificationController::feed` | csrf invalidchars auth -> secureheaders |
| 281 | `GET` | `dashboard/sistem/pengaturan` | `dashboard.system.settings` | `\App\Controllers\Admin\SettingController::index` | csrf invalidchars auth -> secureheaders |
| 282 | `GET` | `dashboard/sistem/pengaturan-umum` | `dashboard.system.settings.general` | `\App\Controllers\Admin\SettingController::general` | csrf invalidchars auth -> secureheaders |
| 283 | `GET` | `dashboard/sistem/pengaturan-organisasi` | `dashboard.system.settings.organization` | `\App\Controllers\Admin\SettingController::organization` | csrf invalidchars auth -> secureheaders |
| 284 | `GET` | `dashboard/sistem/pengaturan-pembelajaran` | `dashboard.system.settings.learning` | `\App\Controllers\Admin\SettingController::learning` | csrf invalidchars auth -> secureheaders |
| 285 | `GET` | `dashboard/sistem/pengaturan-bunga-rampai` | `dashboard.system.settings.knowledge` | `\App\Controllers\Admin\SettingController::knowledge` | csrf invalidchars auth -> secureheaders |
| 286 | `GET` | `dashboard/sistem/pengaturan-regulasi` | `dashboard.system.settings.regulation` | `\App\Controllers\Admin\SettingController::regulation` | csrf invalidchars auth -> secureheaders |
| 287 | `GET` | `dashboard/sistem/pengaturan-berita` | `dashboard.system.settings.news` | `\App\Controllers\Admin\SettingController::news` | csrf invalidchars auth -> secureheaders |
| 288 | `GET` | `dashboard/sistem/pengaturan-event` | `dashboard.system.settings.event` | `\App\Controllers\Admin\SettingController::event` | csrf invalidchars auth -> secureheaders |
| 289 | `GET` | `dashboard/sistem/pengaturan-sertifikat` | `dashboard.system.settings.certificate` | `\App\Controllers\Admin\SettingController::certificate` | csrf invalidchars auth -> secureheaders |
| 290 | `GET` | `dashboard/sistem/pengaturan-notifikasi` | `dashboard.system.settings.notification` | `\App\Controllers\Admin\SettingController::notification` | csrf invalidchars auth -> secureheaders |
| 291 | `GET` | `dashboard/sistem/pengaturan-integrasi` | `dashboard.system.settings.integration` | `\App\Controllers\Admin\SettingController::integration` | csrf invalidchars auth -> secureheaders |
| 292 | `GET` | `dashboard/sistem/pengaturan-keamanan` | `dashboard.system.settings.security` | `\App\Controllers\Admin\SettingController::security` | csrf invalidchars auth -> secureheaders |
| 293 | `GET` | `dashboard/sistem/maintenance` | `dashboard.system.maintenance` | `\App\Controllers\Admin\SettingController::maintenance` | csrf invalidchars auth -> secureheaders |
| 294 | `GET` | `dashboard/sistem/statistik-kunjungan` | `dashboard.system.visitor_stats` | `\App\Controllers\Admin\VisitorStatisticAdminController::index` | csrf invalidchars auth -> secureheaders |
| 295 | `GET` | `dashboard/sistem/statistik-trafik` | `Â»` | `\App\Controllers\Admin\VisitorStatisticAdminController::index` | csrf invalidchars auth -> secureheaders |
| 296 | `GET` | `dashboard/sistem/visitor-stats` | `Â»` | `\App\Controllers\Admin\VisitorStatisticAdminController::index` | csrf invalidchars auth -> secureheaders |
| 297 | `GET` | `dashboard/sistem/pengaturan/([^/]+)` | `Â»` | `\App\Controllers\Admin\SettingController::groupView/$1` | csrf invalidchars auth -> secureheaders |
| 298 | `GET` | `dashboard/sistem/settings` | `Â»` | `\App\Controllers\Admin\SettingController::index` | csrf invalidchars auth -> secureheaders |
| 299 | `GET` | `dashboard/sistem/settings/([^/]+)` | `Â»` | `\App\Controllers\Admin\SettingController::groupView/$1` | csrf invalidchars auth -> secureheaders |
| 300 | `GET` | `dashboard/pengaturan` | `Â»` | `\App\Controllers\Admin\SettingController::index` | csrf invalidchars auth -> secureheaders |
| 301 | `GET` | `dashboard/pengaturan/([^/]+)` | `Â»` | `\App\Controllers\Admin\SettingController::groupView/$1` | csrf invalidchars auth -> secureheaders |
| 302 | `GET` | `dashboard/settings` | `Â»` | `\App\Controllers\Admin\SettingController::index` | csrf invalidchars auth -> secureheaders |
| 303 | `GET` | `dashboard/settings/general` | `Â»` | `\App\Controllers\Admin\SettingController::general` | csrf invalidchars auth -> secureheaders |
| 304 | `GET` | `dashboard/settings/organization` | `Â»` | `\App\Controllers\Admin\SettingController::organization` | csrf invalidchars auth -> secureheaders |
| 305 | `GET` | `dashboard/settings/learning` | `Â»` | `\App\Controllers\Admin\SettingController::learning` | csrf invalidchars auth -> secureheaders |
| 306 | `GET` | `dashboard/settings/knowledge` | `Â»` | `\App\Controllers\Admin\SettingController::knowledge` | csrf invalidchars auth -> secureheaders |
| 307 | `GET` | `dashboard/settings/regulation` | `Â»` | `\App\Controllers\Admin\SettingController::regulation` | csrf invalidchars auth -> secureheaders |
| 308 | `GET` | `dashboard/settings/news` | `Â»` | `\App\Controllers\Admin\SettingController::news` | csrf invalidchars auth -> secureheaders |
| 309 | `GET` | `dashboard/settings/event` | `Â»` | `\App\Controllers\Admin\SettingController::event` | csrf invalidchars auth -> secureheaders |
| 310 | `GET` | `dashboard/settings/certificate` | `Â»` | `\App\Controllers\Admin\SettingController::certificate` | csrf invalidchars auth -> secureheaders |
| 311 | `GET` | `dashboard/settings/notification` | `Â»` | `\App\Controllers\Admin\SettingController::notification` | csrf invalidchars auth -> secureheaders |
| 312 | `GET` | `dashboard/settings/integration` | `Â»` | `\App\Controllers\Admin\SettingController::integration` | csrf invalidchars auth -> secureheaders |
| 313 | `GET` | `dashboard/settings/security` | `Â»` | `\App\Controllers\Admin\SettingController::security` | csrf invalidchars auth -> secureheaders |
| 314 | `GET` | `dashboard/settings/maintenance` | `Â»` | `\App\Controllers\Admin\SettingController::maintenance` | csrf invalidchars auth -> secureheaders |
| 315 | `GET` | `dashboard/settings/([^/]+)` | `Â»` | `\App\Controllers\Admin\SettingController::groupView/$1` | csrf invalidchars auth -> secureheaders |
| 316 | `GET` | `dashboard/pembelajaran/pengajar` | `Â»` | `dashboard/pembelajaran/kelas` | - |
| 317 | `GET` | `dashboard/bunga-rampai/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 318 | `GET` | `dashboard/komunikasi/helpdesk` | `Â»` | `dashboard/komunikasi/whatsapp-widget` | - |
| 319 | `GET` | `dashboard/landing/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 320 | `GET` | `dashboard/landing/banner-halaman` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 321 | `GET` | `dashboard/landing/banner-halaman/(.*)` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 322 | `GET` | `dashboard/landing/kategori-layanan` | `Â»` | `dashboard/landing` | - |
| 323 | `GET` | `dashboard/landing/kategori-layanan/(.*)` | `Â»` | `dashboard/landing` | - |
| 324 | `HEAD` | `dashboard/pembelajaran/pengajar` | `Â»` | `dashboard/pembelajaran/kelas` | - |
| 325 | `HEAD` | `dashboard/bunga-rampai/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 326 | `HEAD` | `dashboard/komunikasi/helpdesk` | `Â»` | `dashboard/komunikasi/whatsapp-widget` | - |
| 327 | `HEAD` | `dashboard/landing/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 328 | `HEAD` | `dashboard/landing/banner-halaman` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 329 | `HEAD` | `dashboard/landing/banner-halaman/(.*)` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 330 | `HEAD` | `dashboard/landing/kategori-layanan` | `Â»` | `dashboard/landing` | - |
| 331 | `HEAD` | `dashboard/landing/kategori-layanan/(.*)` | `Â»` | `dashboard/landing` | - |
| 332 | `POST` | `dashboard/approvals/([^/]+)/approve` | `Â»` | `\App\Controllers\Admin\ApprovalController::approve/$1` | csrf invalidchars auth -> secureheaders |
| 333 | `POST` | `dashboard/approvals/([^/]+)/reject` | `Â»` | `\App\Controllers\Admin\ApprovalController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 334 | `POST` | `dashboard/profile/edit` | `dashboard.profile.update` | `\App\Controllers\Admin\ProfileController::update` | csrf invalidchars auth -> secureheaders |
| 335 | `POST` | `dashboard/profile/update` | `dashboard.profile.update.alt` | `\App\Controllers\Admin\ProfileController::update` | csrf invalidchars auth -> secureheaders |
| 336 | `POST` | `dashboard/profile/password` | `dashboard.profile.password.update` | `\App\Controllers\Admin\ProfileController::updatePassword` | csrf invalidchars auth -> secureheaders |
| 337 | `POST` | `dashboard/profile/preferences` | `dashboard.profile.preferences.update` | `\App\Controllers\Admin\ProfileController::updatePreferences` | csrf invalidchars auth -> secureheaders |
| 338 | `POST` | `dashboard/akses/pengguna/tambah` | `dashboard.access.users.store` | `\App\Controllers\Admin\UserController::store` | csrf invalidchars auth -> secureheaders |
| 339 | `POST` | `dashboard/akses/pengguna/([^/]+)/ubah` | `dashboard.access.users.update` | `\App\Controllers\Admin\UserController::update/$1` | csrf invalidchars auth -> secureheaders |
| 340 | `POST` | `dashboard/akses/pengguna/([^/]+)/aktifkan` | `dashboard.access.users.activate` | `\App\Controllers\Admin\UserController::activate/$1` | csrf invalidchars auth -> secureheaders |
| 341 | `POST` | `dashboard/akses/pengguna/([^/]+)/tolak` | `dashboard.access.users.reject` | `\App\Controllers\Admin\UserController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 342 | `POST` | `dashboard/akses/pengguna/([^/]+)/suspend` | `dashboard.access.users.suspend` | `\App\Controllers\Admin\UserController::suspend/$1` | csrf invalidchars auth -> secureheaders |
| 343 | `POST` | `dashboard/akses/pengguna/([^/]+)/reset-password` | `dashboard.access.users.reset-password` | `\App\Controllers\Admin\UserController::resetPassword/$1` | csrf invalidchars auth -> secureheaders |
| 344 | `POST` | `dashboard/akses/pengguna/([^/]+)/hapus` | `dashboard.access.users.delete` | `\App\Controllers\Admin\UserController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 345 | `POST` | `dashboard/akses/verifikasi-profil/([^/]+)/setujui` | `dashboard.access.profile-verifications.approve` | `\App\Controllers\Admin\UserProfileVerificationController::approve/$1` | csrf invalidchars auth -> secureheaders |
| 346 | `POST` | `dashboard/akses/verifikasi-profil/([^/]+)/tolak` | `dashboard.access.profile-verifications.reject` | `\App\Controllers\Admin\UserProfileVerificationController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 347 | `POST` | `dashboard/akses/verifikasi-profil/([^/]+)/revisi` | `dashboard.access.profile-verifications.revision` | `\App\Controllers\Admin\UserProfileVerificationController::requestRevision/$1` | csrf invalidchars auth -> secureheaders |
| 348 | `POST` | `dashboard/akses/permohonan-perubahan-profil/([^/]+)/setujui` | `dashboard.access.profile-change-requests.approve` | `\App\Controllers\Admin\UserProfileVerificationController::changeApprove/$1` | csrf invalidchars auth -> secureheaders |
| 349 | `POST` | `dashboard/akses/permohonan-perubahan-profil/([^/]+)/tolak` | `dashboard.access.profile-change-requests.reject` | `\App\Controllers\Admin\UserProfileVerificationController::changeReject/$1` | csrf invalidchars auth -> secureheaders |
| 350 | `POST` | `dashboard/akses/permohonan-perubahan-profil/([^/]+)/revisi` | `dashboard.access.profile-change-requests.revision` | `\App\Controllers\Admin\UserProfileVerificationController::changeRequestRevision/$1` | csrf invalidchars auth -> secureheaders |
| 351 | `POST` | `dashboard/akses/role/tambah` | `dashboard.access.roles.store` | `\App\Controllers\Admin\RoleController::store` | csrf invalidchars auth -> secureheaders |
| 352 | `POST` | `dashboard/akses/role/([^/]+)/ubah` | `dashboard.access.roles.update` | `\App\Controllers\Admin\RoleController::update/$1` | csrf invalidchars auth -> secureheaders |
| 353 | `POST` | `dashboard/akses/role/([^/]+)/hapus` | `dashboard.access.roles.delete` | `\App\Controllers\Admin\RoleController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 354 | `POST` | `dashboard/akses/role/matrix` | `dashboard.access.roles.matrix.sync` | `\App\Controllers\Admin\RoleController::syncMatrix` | csrf invalidchars auth -> secureheaders |
| 355 | `POST` | `dashboard/akses/permission/sync-katalog` | `dashboard.access.permissions.syncCatalog` | `\App\Controllers\Admin\PermissionController::syncCatalog` | csrf invalidchars auth -> secureheaders |
| 356 | `POST` | `dashboard/akses/permission/import` | `dashboard.access.permissions.import` | `\App\Controllers\Admin\PermissionController::import` | csrf invalidchars auth -> secureheaders |
| 357 | `POST` | `dashboard/akses/permission/tambah` | `dashboard.access.permissions.store` | `\App\Controllers\Admin\PermissionController::store` | csrf invalidchars auth -> secureheaders |
| 358 | `POST` | `dashboard/akses/permission/([^/]+)/ubah` | `dashboard.access.permissions.update` | `\App\Controllers\Admin\PermissionController::update/$1` | csrf invalidchars auth -> secureheaders |
| 359 | `POST` | `dashboard/akses/permission/([^/]+)/nonaktifkan` | `dashboard.access.permissions.deactivate` | `\App\Controllers\Admin\PermissionController::deactivate/$1` | csrf invalidchars auth -> secureheaders |
| 360 | `POST` | `dashboard/akses/permission/([^/]+)/aktifkan` | `dashboard.access.permissions.activate` | `\App\Controllers\Admin\PermissionController::activate/$1` | csrf invalidchars auth -> secureheaders |
| 361 | `POST` | `dashboard/akses/permission/([^/]+)/grant-role` | `dashboard.access.permissions.grantRole` | `\App\Controllers\Admin\PermissionController::grantRole/$1` | csrf invalidchars auth -> secureheaders |
| 362 | `POST` | `dashboard/akses/permission/([^/]+)/revoke-role/([^/]+)` | `dashboard.access.permissions.revokeRole` | `\App\Controllers\Admin\PermissionController::revokeRole/$1/$2` | csrf invalidchars auth -> secureheaders |
| 363 | `POST` | `dashboard/akses/permission/([^/]+)/hapus` | `dashboard.access.permissions.delete` | `\App\Controllers\Admin\PermissionController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 364 | `POST` | `dashboard/akses/permohonan-role/([^/]+)/setujui` | `dashboard.access.role-requests.approve` | `\App\Controllers\Admin\RoleRequestReviewController::approve/$1` | csrf invalidchars auth -> secureheaders |
| 365 | `POST` | `dashboard/akses/permohonan-role/([^/]+)/tolak` | `dashboard.access.role-requests.reject` | `\App\Controllers\Admin\RoleRequestReviewController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 366 | `POST` | `dashboard/akses/permohonan-role/([^/]+)/revisi` | `dashboard.access.role-requests.revision` | `\App\Controllers\Admin\RoleRequestReviewController::requestRevision/$1` | csrf invalidchars auth -> secureheaders |
| 367 | `POST` | `dashboard/akses/unit-organisasi/tambah` | `dashboard.access.organization-units.store` | `\App\Controllers\Admin\OrganizationController::store` | csrf invalidchars auth -> secureheaders |
| 368 | `POST` | `dashboard/akses/unit-organisasi/([^/]+)/ubah` | `dashboard.access.organization-units.update` | `\App\Controllers\Admin\OrganizationController::update/$1` | csrf invalidchars auth -> secureheaders |
| 369 | `POST` | `dashboard/akses/unit-organisasi/([^/]+)/nonaktifkan` | `dashboard.access.organization-units.deactivate` | `\App\Controllers\Admin\OrganizationController::deactivate/$1` | csrf invalidchars auth -> secureheaders |
| 370 | `POST` | `dashboard/akses/unit-organisasi/([^/]+)/aktifkan` | `dashboard.access.organization-units.activate` | `\App\Controllers\Admin\OrganizationController::activate/$1` | csrf invalidchars auth -> secureheaders |
| 371 | `POST` | `dashboard/akses/peran-pengadaan/tambah` | `dashboard.access.procurement-roles.store` | `\App\Controllers\Admin\ProcurementRoleController::store` | csrf invalidchars auth -> secureheaders |
| 372 | `POST` | `dashboard/akses/peran-pengadaan/([^/]+)/ubah` | `dashboard.access.procurement-roles.update` | `\App\Controllers\Admin\ProcurementRoleController::update/$1` | csrf invalidchars auth -> secureheaders |
| 373 | `POST` | `dashboard/akses/peran-pengadaan/([^/]+)/nonaktifkan` | `dashboard.access.procurement-roles.deactivate` | `\App\Controllers\Admin\ProcurementRoleController::deactivate/$1` | csrf invalidchars auth -> secureheaders |
| 374 | `POST` | `dashboard/akses/peran-pengadaan/([^/]+)/aktifkan` | `dashboard.access.procurement-roles.activate` | `\App\Controllers\Admin\ProcurementRoleController::activate/$1` | csrf invalidchars auth -> secureheaders |
| 375 | `POST` | `dashboard/akses/peran-pengadaan/([^/]+)/hapus` | `dashboard.access.procurement-roles.delete` | `\App\Controllers\Admin\ProcurementRoleController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 376 | `POST` | `dashboard/akses/penugasan-pengadaan` | `dashboard.access.procurement-assignments.store` | `\App\Controllers\Admin\ProcurementRoleController::storeAssignment` | csrf invalidchars auth -> secureheaders |
| 377 | `POST` | `dashboard/akses/penugasan-pengadaan/([^/]+)/verifikasi` | `dashboard.access.procurement-assignments.verify` | `\App\Controllers\Admin\ProcurementRoleController::verifyAssignment/$1` | csrf invalidchars auth -> secureheaders |
| 378 | `POST` | `dashboard/pembelajaran/program` | `dashboard.learning.programs.store` | `\App\Controllers\Learning\ProgramController::store` | csrf invalidchars auth -> secureheaders |
| 379 | `POST` | `dashboard/pembelajaran/program/([^/]+)/ubah` | `dashboard.learning.programs.update` | `\App\Controllers\Learning\ProgramController::update/$1` | csrf invalidchars auth -> secureheaders |
| 380 | `POST` | `dashboard/pembelajaran/program/([^/]+)/hapus` | `dashboard.learning.programs.delete` | `\App\Controllers\Learning\ProgramController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 381 | `POST` | `dashboard/pembelajaran/course` | `dashboard.learning.courses.store` | `\App\Controllers\Learning\CourseController::store` | csrf invalidchars auth -> secureheaders |
| 382 | `POST` | `dashboard/pembelajaran/course/([^/]+)/ubah` | `dashboard.learning.courses.update` | `\App\Controllers\Learning\CourseController::update/$1` | csrf invalidchars auth -> secureheaders |
| 383 | `POST` | `dashboard/pembelajaran/course/([^/]+)/hapus` | `dashboard.learning.courses.delete` | `\App\Controllers\Learning\CourseController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 384 | `POST` | `dashboard/pembelajaran/kelas` | `dashboard.learning.classes.store` | `\App\Controllers\Learning\LearningClassController::store` | csrf invalidchars auth -> secureheaders |
| 385 | `POST` | `dashboard/pembelajaran/kelas/([^/]+)/ubah` | `dashboard.learning.classes.update` | `\App\Controllers\Learning\LearningClassController::update/$1` | csrf invalidchars auth -> secureheaders |
| 386 | `POST` | `dashboard/pembelajaran/kelas/([^/]+)/hapus` | `dashboard.learning.classes.delete` | `\App\Controllers\Learning\LearningClassController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 387 | `POST` | `dashboard/pembelajaran/kelas/([^/]+)/trainer` | `dashboard.learning.classes.assign-trainer` | `\App\Controllers\Learning\LearningClassController::assignTrainer/$1` | csrf invalidchars auth -> secureheaders |
| 388 | `POST` | `dashboard/pembelajaran/kelas/([^/]+)/trainer/([^/]+)/cabut` | `dashboard.learning.classes.revoke-trainer` | `\App\Controllers\Learning\LearningClassController::revokeTrainer/$1/$2` | csrf invalidchars auth -> secureheaders |
| 389 | `POST` | `dashboard/pembelajaran/kelas/([^/]+)/duplikasi` | `dashboard.learning.classes.duplicate` | `\App\Controllers\Learning\LearningClassController::duplicateBatch/$1` | csrf invalidchars auth -> secureheaders |
| 390 | `POST` | `dashboard/pembelajaran/trainer/assign` | `dashboard.learning.trainers.assign` | `\App\Controllers\Learning\TrainerAssignmentController::assign` | csrf invalidchars auth -> secureheaders |
| 391 | `POST` | `dashboard/pembelajaran/trainer/([^/]+)/([^/]+)/cabut` | `dashboard.learning.trainers.revoke` | `\App\Controllers\Learning\TrainerAssignmentController::revoke/$1/$2` | csrf invalidchars auth -> secureheaders |
| 392 | `POST` | `dashboard/pembelajaran/enrollment/tambah` | `dashboard.learning.enrollments.store` | `\App\Controllers\Learning\EnrollmentController::store` | csrf invalidchars auth -> secureheaders |
| 393 | `POST` | `dashboard/pembelajaran/enrollment/([^/]+)/approve` | `dashboard.learning.enrollments.approve` | `\App\Controllers\Learning\EnrollmentController::approve/$1` | csrf invalidchars auth -> secureheaders |
| 394 | `POST` | `dashboard/pembelajaran/enrollment/([^/]+)/reject` | `dashboard.learning.enrollments.reject` | `\App\Controllers\Learning\EnrollmentController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 395 | `POST` | `dashboard/pembelajaran/enrollment/([^/]+)/status` | `dashboard.learning.enrollments.status` | `\App\Controllers\Learning\EnrollmentController::status/$1` | csrf invalidchars auth -> secureheaders |
| 396 | `POST` | `dashboard/pembelajaran/enrollment/([^/]+)/hapus` | `dashboard.learning.enrollments.delete` | `\App\Controllers\Learning\EnrollmentController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 397 | `POST` | `dashboard/pembelajaran/kurikulum/modul` | `dashboard.learning.curriculum.module.store` | `\App\Controllers\Learning\CurriculumController::storeModule` | csrf invalidchars auth -> secureheaders |
| 398 | `POST` | `dashboard/pembelajaran/kurikulum/modul/([^/]+)/ubah` | `dashboard.learning.curriculum.module.update` | `\App\Controllers\Learning\CurriculumController::updateModule/$1` | csrf invalidchars auth -> secureheaders |
| 399 | `POST` | `dashboard/pembelajaran/kurikulum/modul/([^/]+)/hapus` | `dashboard.learning.curriculum.module.delete` | `\App\Controllers\Learning\CurriculumController::deleteModule/$1` | csrf invalidchars auth -> secureheaders |
| 400 | `POST` | `dashboard/pembelajaran/kurikulum/lesson` | `dashboard.learning.curriculum.lesson.store` | `\App\Controllers\Learning\CurriculumController::storeLesson` | csrf invalidchars auth -> secureheaders |
| 401 | `POST` | `dashboard/pembelajaran/kurikulum/lesson/([^/]+)/ubah` | `dashboard.learning.curriculum.lesson.update` | `\App\Controllers\Learning\CurriculumController::updateLesson/$1` | csrf invalidchars auth -> secureheaders |
| 402 | `POST` | `dashboard/pembelajaran/kurikulum/lesson/([^/]+)/hapus` | `dashboard.learning.curriculum.lesson.delete` | `\App\Controllers\Learning\CurriculumController::deleteLesson/$1` | csrf invalidchars auth -> secureheaders |
| 403 | `POST` | `dashboard/pembelajaran/kurikulum/quiz` | `dashboard.learning.curriculum.quiz.store` | `\App\Controllers\Learning\CurriculumController::storeQuiz` | csrf invalidchars auth -> secureheaders |
| 404 | `POST` | `dashboard/pembelajaran/kurikulum/quiz/([^/]+)/ubah` | `dashboard.learning.curriculum.quiz.update` | `\App\Controllers\Learning\CurriculumController::updateQuiz/$1` | csrf invalidchars auth -> secureheaders |
| 405 | `POST` | `dashboard/pembelajaran/kurikulum/quiz/([^/]+)/toggle` | `dashboard.learning.curriculum.quiz.toggle` | `\App\Controllers\Learning\CurriculumController::toggleQuizStatus/$1` | csrf invalidchars auth -> secureheaders |
| 406 | `POST` | `dashboard/pembelajaran/kurikulum/quiz/([^/]+)/hapus` | `dashboard.learning.curriculum.quiz.delete` | `\App\Controllers\Learning\CurriculumController::deleteQuiz/$1` | csrf invalidchars auth -> secureheaders |
| 407 | `POST` | `dashboard/pembelajaran/kurikulum/quiz/([^/]+)/soal/import` | `dashboard.learning.curriculum.question.import` | `\App\Controllers\Learning\CurriculumController::importQuestions/$1` | csrf invalidchars auth -> secureheaders |
| 408 | `POST` | `dashboard/pembelajaran/kurikulum/soal` | `dashboard.learning.curriculum.question.store` | `\App\Controllers\Learning\CurriculumController::storeQuestion` | csrf invalidchars auth -> secureheaders |
| 409 | `POST` | `dashboard/pembelajaran/kurikulum/soal/([^/]+)/ubah` | `dashboard.learning.curriculum.question.update` | `\App\Controllers\Learning\CurriculumController::updateQuestion/$1` | csrf invalidchars auth -> secureheaders |
| 410 | `POST` | `dashboard/pembelajaran/kurikulum/soal/([^/]+)/hapus` | `dashboard.learning.curriculum.question.delete` | `\App\Controllers\Learning\CurriculumController::deleteQuestion/$1` | csrf invalidchars auth -> secureheaders |
| 411 | `POST` | `dashboard/pembelajaran/kurikulum/tugas` | `dashboard.learning.curriculum.assignment.store` | `\App\Controllers\Learning\CurriculumController::storeAssignment` | csrf invalidchars auth -> secureheaders |
| 412 | `POST` | `dashboard/pembelajaran/kurikulum/tugas/([^/]+)/ubah` | `dashboard.learning.curriculum.assignment.update` | `\App\Controllers\Learning\CurriculumController::updateAssignment/$1` | csrf invalidchars auth -> secureheaders |
| 413 | `POST` | `dashboard/pembelajaran/kurikulum/tugas/([^/]+)/hapus` | `dashboard.learning.curriculum.assignment.delete` | `\App\Controllers\Learning\CurriculumController::deleteAssignment/$1` | csrf invalidchars auth -> secureheaders |
| 414 | `POST` | `dashboard/pembelajaran/materi-modul` | `dashboard.learning.modules.store` | `\App\Controllers\Learning\ModuleController::store` | csrf invalidchars auth -> secureheaders |
| 415 | `POST` | `dashboard/pembelajaran/materi-modul/([^/]+)/ubah` | `dashboard.learning.modules.update` | `\App\Controllers\Learning\ModuleController::update/$1` | csrf invalidchars auth -> secureheaders |
| 416 | `POST` | `dashboard/pembelajaran/lesson` | `dashboard.learning.lessons.store` | `\App\Controllers\Learning\LessonController::store` | csrf invalidchars auth -> secureheaders |
| 417 | `POST` | `dashboard/pembelajaran/lesson/([^/]+)/ubah` | `dashboard.learning.lessons.update` | `\App\Controllers\Learning\LessonController::update/$1` | csrf invalidchars auth -> secureheaders |
| 418 | `POST` | `dashboard/pembelajaran/assessment/quiz/([^/]+)/soal/import` | `dashboard.learning.assessments.quiz.question.import` | `\App\Controllers\Learning\AssessmentManagementController::importQuestions/$1` | csrf invalidchars auth -> secureheaders |
| 419 | `POST` | `dashboard/pembelajaran/assessment/quiz` | `dashboard.learning.assessments.quiz.store` | `\App\Controllers\Learning\AssessmentManagementController::storeQuiz` | csrf invalidchars auth -> secureheaders |
| 420 | `POST` | `dashboard/pembelajaran/assessment/quiz/([^/]+)/ubah` | `dashboard.learning.assessments.quiz.update` | `\App\Controllers\Learning\AssessmentManagementController::updateQuiz/$1` | csrf invalidchars auth -> secureheaders |
| 421 | `POST` | `dashboard/pembelajaran/assessment/quiz/([^/]+)/toggle` | `dashboard.learning.assessments.quiz.toggle` | `\App\Controllers\Learning\AssessmentManagementController::toggleQuizStatus/$1` | csrf invalidchars auth -> secureheaders |
| 422 | `POST` | `dashboard/pembelajaran/assessment/quiz/([^/]+)/hapus` | `dashboard.learning.assessments.quiz.delete` | `\App\Controllers\Learning\AssessmentManagementController::deleteQuiz/$1` | csrf invalidchars auth -> secureheaders |
| 423 | `POST` | `dashboard/pembelajaran/assessment/assignment` | `dashboard.learning.assessments.assignment.store` | `\App\Controllers\Learning\AssessmentManagementController::storeAssignment` | csrf invalidchars auth -> secureheaders |
| 424 | `POST` | `dashboard/pembelajaran/assessment/assignment/([^/]+)/ubah` | `dashboard.learning.assessments.assignment.update` | `\App\Controllers\Learning\AssessmentManagementController::updateAssignment/$1` | csrf invalidchars auth -> secureheaders |
| 425 | `POST` | `dashboard/pembelajaran/assessment/assignment/([^/]+)/hapus` | `dashboard.learning.assessments.assignment.delete` | `\App\Controllers\Learning\AssessmentManagementController::deleteAssignment/$1` | csrf invalidchars auth -> secureheaders |
| 426 | `POST` | `dashboard/pembelajaran/assessment/submission/([^/]+)/nilai` | `dashboard.learning.assessments.submission.grade` | `\App\Controllers\Learning\AssessmentManagementController::gradeSubmission/$1` | csrf invalidchars auth -> secureheaders |
| 427 | `POST` | `dashboard/pembelajaran/penilaian/([^/]+)/nilai` | `dashboard.learning.gradings.grade` | `\App\Controllers\Learning\GradingController::grade/$1` | csrf invalidchars auth -> secureheaders |
| 428 | `POST` | `dashboard/pembelajaran/kelayakan-sertifikat/generate/([^/]+)` | `dashboard.learning.certificate-eligibility.generate` | `\App\Controllers\Learning\CertificateEligibilityController::generate/$1` | csrf invalidchars auth -> secureheaders |
| 429 | `POST` | `dashboard/pembelajaran/kelayakan-sertifikat/batch-generate` | `dashboard.learning.certificate-eligibility.batch-generate` | `\App\Controllers\Learning\CertificateEligibilityController::batchGenerate` | csrf invalidchars auth -> secureheaders |
| 430 | `POST` | `dashboard/bunga-rampai/artikel/tambah` | `Â»` | `\App\Controllers\Knowledge\ArticleController::store` | csrf invalidchars auth -> secureheaders |
| 431 | `POST` | `dashboard/bunga-rampai/artikel/([^/]+)/ubah` | `Â»` | `\App\Controllers\Knowledge\ArticleController::update/$1` | csrf invalidchars auth -> secureheaders |
| 432 | `POST` | `dashboard/bunga-rampai/artikel/([^/]+)/hapus` | `dashboard.knowledge.articles.delete` | `\App\Controllers\Knowledge\ArticleController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 433 | `POST` | `dashboard/bunga-rampai/faq/tambah` | `Â»` | `\App\Controllers\Knowledge\FaqController::store` | csrf invalidchars auth -> secureheaders |
| 434 | `POST` | `dashboard/bunga-rampai/faq/([^/]+)/ubah` | `Â»` | `\App\Controllers\Knowledge\FaqController::update/$1` | csrf invalidchars auth -> secureheaders |
| 435 | `POST` | `dashboard/bunga-rampai/faq/([^/]+)/hapus` | `dashboard.knowledge.faqs.delete` | `\App\Controllers\Knowledge\FaqController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 436 | `POST` | `dashboard/bunga-rampai/studi-kasus/tambah` | `Â»` | `\App\Controllers\Knowledge\CaseStudyController::store` | csrf invalidchars auth -> secureheaders |
| 437 | `POST` | `dashboard/bunga-rampai/studi-kasus/([^/]+)/ubah` | `Â»` | `\App\Controllers\Knowledge\CaseStudyController::update/$1` | csrf invalidchars auth -> secureheaders |
| 438 | `POST` | `dashboard/bunga-rampai/studi-kasus/([^/]+)/hapus` | `dashboard.knowledge.case-studies.delete` | `\App\Controllers\Knowledge\CaseStudyController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 439 | `POST` | `dashboard/bunga-rampai/template-dokumen/tambah` | `Â»` | `\App\Controllers\Knowledge\DocumentTemplateController::store` | csrf invalidchars auth -> secureheaders |
| 440 | `POST` | `dashboard/bunga-rampai/template-dokumen/([^/]+)/ubah` | `Â»` | `\App\Controllers\Knowledge\DocumentTemplateController::update/$1` | csrf invalidchars auth -> secureheaders |
| 441 | `POST` | `dashboard/bunga-rampai/template-dokumen/([^/]+)/hapus` | `dashboard.knowledge.document-templates.delete` | `\App\Controllers\Knowledge\DocumentTemplateController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 442 | `POST` | `dashboard/bunga-rampai/media-galeri/tambah` | `Â»` | `\App\Controllers\Knowledge\KnowledgeMediaController::store` | csrf invalidchars auth -> secureheaders |
| 443 | `POST` | `dashboard/bunga-rampai/media-galeri/([^/]+)/ubah` | `Â»` | `\App\Controllers\Knowledge\KnowledgeMediaController::update/$1` | csrf invalidchars auth -> secureheaders |
| 444 | `POST` | `dashboard/bunga-rampai/media-galeri/([^/]+)/hapus` | `dashboard.knowledge.media-gallery.delete` | `\App\Controllers\Knowledge\KnowledgeMediaController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 445 | `POST` | `dashboard/bunga-rampai/carousel-konten-unggulan/tambah` | `Â»` | `\App\Controllers\Knowledge\KnowledgeCarouselController::store` | csrf invalidchars auth -> secureheaders |
| 446 | `POST` | `dashboard/bunga-rampai/carousel-konten-unggulan/([^/]+)/([^/]+)/nonaktifkan` | `dashboard.knowledge.carousel.disable-item` | `\App\Controllers\Knowledge\KnowledgeCarouselController::disable/$1/$2` | csrf invalidchars auth -> secureheaders |
| 447 | `POST` | `dashboard/bunga-rampai/carousel-konten-unggulan/([^/]+)/nonaktifkan` | `dashboard.knowledge.carousel.disable` | `\App\Controllers\Knowledge\KnowledgeCarouselController::disable/$1` | csrf invalidchars auth -> secureheaders |
| 448 | `POST` | `dashboard/bunga-rampai/kategori/tambah` | `Â»` | `\App\Controllers\Knowledge\CategoryController::store` | csrf invalidchars auth -> secureheaders |
| 449 | `POST` | `dashboard/bunga-rampai/kategori/([^/]+)/ubah` | `Â»` | `\App\Controllers\Knowledge\CategoryController::update/$1` | csrf invalidchars auth -> secureheaders |
| 450 | `POST` | `dashboard/bunga-rampai/kategori/([^/]+)/hapus` | `dashboard.knowledge.categories.delete` | `\App\Controllers\Knowledge\CategoryController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 451 | `POST` | `dashboard/bunga-rampai/tag/tambah` | `Â»` | `\App\Controllers\Knowledge\TagController::store` | csrf invalidchars auth -> secureheaders |
| 452 | `POST` | `dashboard/bunga-rampai/tag/([^/]+)/ubah` | `Â»` | `\App\Controllers\Knowledge\TagController::update/$1` | csrf invalidchars auth -> secureheaders |
| 453 | `POST` | `dashboard/bunga-rampai/tag/([^/]+)/hapus` | `dashboard.knowledge.tags.delete` | `\App\Controllers\Knowledge\TagController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 454 | `POST` | `dashboard/regulasi/dokumen` | `dashboard.regulations.store` | `\App\Controllers\Admin\RegulationController::store` | csrf invalidchars auth -> secureheaders |
| 455 | `POST` | `dashboard/regulasi/dokumen/([^/]+)/ubah` | `dashboard.regulations.update` | `\App\Controllers\Admin\RegulationController::update/$1` | csrf invalidchars auth -> secureheaders |
| 456 | `POST` | `dashboard/regulasi/dokumen/([^/]+)/hapus` | `dashboard.regulations.delete` | `\App\Controllers\Admin\RegulationController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 457 | `POST` | `dashboard/regulasi/dokumen/([^/]+)/submit` | `dashboard.regulations.submit` | `\App\Controllers\Admin\RegulationController::submitReview/$1` | csrf invalidchars auth -> secureheaders |
| 458 | `POST` | `dashboard/regulasi/dokumen/([^/]+)/approve` | `dashboard.regulations.approve` | `\App\Controllers\Admin\RegulationController::approve/$1` | csrf invalidchars auth -> secureheaders |
| 459 | `POST` | `dashboard/regulasi/dokumen/([^/]+)/reject` | `dashboard.regulations.reject` | `\App\Controllers\Admin\RegulationController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 460 | `POST` | `dashboard/regulasi/dokumen/([^/]+)/revision` | `dashboard.regulations.revision` | `\App\Controllers\Admin\RegulationController::requestRevision/$1` | csrf invalidchars auth -> secureheaders |
| 461 | `POST` | `dashboard/regulasi/dokumen/([^/]+)/publish` | `dashboard.regulations.publish` | `\App\Controllers\Admin\RegulationController::publish/$1` | csrf invalidchars auth -> secureheaders |
| 462 | `POST` | `dashboard/regulasi/dokumen/([^/]+)/versi` | `dashboard.regulations.version.add` | `\App\Controllers\Admin\RegulationController::addVersion/$1` | csrf invalidchars auth -> secureheaders |
| 463 | `POST` | `dashboard/regulasi/dokumen/([^/]+)/versi/current` | `dashboard.regulations.version.current` | `\App\Controllers\Admin\RegulationController::setCurrentVersion/$1` | csrf invalidchars auth -> secureheaders |
| 464 | `POST` | `dashboard/regulasi/dokumen/([^/]+)/kompetensi` | `dashboard.regulations.competency.attach` | `\App\Controllers\Admin\RegulationController::attachCompetency/$1` | csrf invalidchars auth -> secureheaders |
| 465 | `POST` | `dashboard/regulasi/kategori/tambah` | `dashboard.regulations.categories.store` | `\App\Controllers\Admin\RegulationController::categoryStore` | csrf invalidchars auth -> secureheaders |
| 466 | `POST` | `dashboard/regulasi/kategori/([^/]+)/ubah` | `dashboard.regulations.categories.update` | `\App\Controllers\Admin\RegulationController::categoryUpdate/$1` | csrf invalidchars auth -> secureheaders |
| 467 | `POST` | `dashboard/regulasi/kategori/([^/]+)/hapus` | `dashboard.regulations.categories.delete` | `\App\Controllers\Admin\RegulationController::categoryDelete/$1` | csrf invalidchars auth -> secureheaders |
| 468 | `POST` | `dashboard/regulasi/kategori/([^/]+)/toggle` | `dashboard.regulations.categories.toggle` | `\App\Controllers\Admin\RegulationController::categoryToggle/$1` | csrf invalidchars auth -> secureheaders |
| 469 | `POST` | `dashboard/regulasi/relasi/tambah` | `dashboard.regulations.relations.store` | `\App\Controllers\Admin\RegulationController::relationStore` | csrf invalidchars auth -> secureheaders |
| 470 | `POST` | `dashboard/regulasi/relasi/([^/]+)/hapus` | `dashboard.regulations.relations.delete` | `\App\Controllers\Admin\RegulationController::relationDelete/$1` | csrf invalidchars auth -> secureheaders |
| 471 | `POST` | `dashboard/regulasi/tahapan-pbj/tambah` | `dashboard.regulations.procurement-stages.store` | `\App\Controllers\Admin\RegulationController::procurementStageStore` | csrf invalidchars auth -> secureheaders |
| 472 | `POST` | `dashboard/regulasi/tahapan-pbj/([^/]+)/ubah` | `dashboard.regulations.procurement-stages.update` | `\App\Controllers\Admin\RegulationController::procurementStageUpdate/$1` | csrf invalidchars auth -> secureheaders |
| 473 | `POST` | `dashboard/regulasi/tahapan-pbj/([^/]+)/hapus` | `dashboard.regulations.procurement-stages.delete` | `\App\Controllers\Admin\RegulationController::procurementStageDelete/$1` | csrf invalidchars auth -> secureheaders |
| 474 | `POST` | `dashboard/regulasi/tahapan-pbj/([^/]+)/toggle` | `dashboard.regulations.procurement-stages.toggle` | `\App\Controllers\Admin\RegulationController::procurementStageToggle/$1` | csrf invalidchars auth -> secureheaders |
| 475 | `POST` | `dashboard/regulasi/kompetensi/tambah` | `dashboard.regulations.competencies.store` | `\App\Controllers\Admin\RegulationController::competencyStore` | csrf invalidchars auth -> secureheaders |
| 476 | `POST` | `dashboard/regulasi/kompetensi/([^/]+)/ubah` | `dashboard.regulations.competencies.update` | `\App\Controllers\Admin\RegulationController::competencyUpdate/$1` | csrf invalidchars auth -> secureheaders |
| 477 | `POST` | `dashboard/regulasi/kompetensi/([^/]+)/hapus` | `dashboard.regulations.competencies.delete` | `\App\Controllers\Admin\RegulationController::competencyDelete/$1` | csrf invalidchars auth -> secureheaders |
| 478 | `POST` | `dashboard/regulasi/kompetensi/([^/]+)/toggle` | `dashboard.regulations.competencies.toggle` | `\App\Controllers\Admin\RegulationController::competencyToggle/$1` | csrf invalidchars auth -> secureheaders |
| 479 | `POST` | `dashboard/regulasi/kompetensi-terkait/([^/]+)/hapus` | `Â»` | `\App\Controllers\Admin\RegulationController::competencyDelete/$1` | csrf invalidchars auth -> secureheaders |
| 480 | `POST` | `dashboard/regulasi/kompetensi-terkait/([^/]+)/toggle` | `Â»` | `\App\Controllers\Admin\RegulationController::competencyToggle/$1` | csrf invalidchars auth -> secureheaders |
| 481 | `POST` | `dashboard/komunikasi/berita/tambah` | `Â»` | `\App\Controllers\Communication\NewsController::store` | csrf invalidchars auth -> secureheaders |
| 482 | `POST` | `dashboard/komunikasi/berita/([^/]+)/ubah` | `Â»` | `\App\Controllers\Communication\NewsController::update/$1` | csrf invalidchars auth -> secureheaders |
| 483 | `POST` | `dashboard/komunikasi/berita/([^/]+)/hapus` | `dashboard.communication.news.delete` | `\App\Controllers\Communication\NewsController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 484 | `POST` | `dashboard/komunikasi/kategori-berita/tambah` | `Â»` | `\App\Controllers\Communication\NewsController::categoryStore` | csrf invalidchars auth -> secureheaders |
| 485 | `POST` | `dashboard/komunikasi/kategori-berita/([^/]+)/ubah` | `Â»` | `\App\Controllers\Communication\NewsController::categoryUpdate/$1` | csrf invalidchars auth -> secureheaders |
| 486 | `POST` | `dashboard/komunikasi/kategori-berita/([^/]+)/hapus` | `Â»` | `\App\Controllers\Communication\NewsController::categoryDelete/$1` | csrf invalidchars auth -> secureheaders |
| 487 | `POST` | `dashboard/komunikasi/event-agenda/tambah` | `Â»` | `\App\Controllers\Communication\EventController::store` | csrf invalidchars auth -> secureheaders |
| 488 | `POST` | `dashboard/komunikasi/event-agenda/([^/]+)/ubah` | `Â»` | `\App\Controllers\Communication\EventController::update/$1` | csrf invalidchars auth -> secureheaders |
| 489 | `POST` | `dashboard/komunikasi/event-agenda/([^/]+)/generate-kode-presensi` | `dashboard.communication.events.generate-attendance-code` | `\App\Controllers\Communication\EventController::generateAttendanceCode/$1` | csrf invalidchars auth -> secureheaders |
| 490 | `POST` | `dashboard/komunikasi/event-agenda/([^/]+)/buka-presensi` | `dashboard.communication.events.open-attendance` | `\App\Controllers\Communication\EventController::openAttendance/$1` | csrf invalidchars auth -> secureheaders |
| 491 | `POST` | `dashboard/komunikasi/event-agenda/([^/]+)/tutup-presensi` | `dashboard.communication.events.close-attendance` | `\App\Controllers\Communication\EventController::closeAttendance/$1` | csrf invalidchars auth -> secureheaders |
| 492 | `POST` | `dashboard/komunikasi/event-agenda/([^/]+)/batch-certificates` | `dashboard.communication.events.batch-certificates` | `\App\Controllers\Communication\EventController::batchCertificates/$1` | csrf invalidchars auth -> secureheaders |
| 493 | `POST` | `dashboard/komunikasi/event-agenda/([^/]+)/materi/upload` | `dashboard.communication.events.upload-material` | `\App\Controllers\Communication\EventController::uploadMaterial/$1` | csrf invalidchars auth -> secureheaders |
| 494 | `POST` | `dashboard/komunikasi/event-agenda/([^/]+)/materi/([0-9]+)/hapus` | `dashboard.communication.events.delete-material` | `\App\Controllers\Communication\EventController::deleteMaterial/$1/$2` | csrf invalidchars auth -> secureheaders |
| 495 | `POST` | `dashboard/komunikasi/event-agenda/([^/]+)/assessment` | `dashboard.communication.events.save-assessment` | `\App\Controllers\Communication\EventController::saveAssessment/$1` | csrf invalidchars auth -> secureheaders |
| 496 | `POST` | `dashboard/komunikasi/event-agenda/([^/]+)/hapus` | `dashboard.communication.events.delete` | `\App\Controllers\Communication\EventController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 497 | `POST` | `dashboard/komunikasi/registrasi-event/([^/]+)/status` | `Â»` | `\App\Controllers\Communication\EventRegistrationController::updateStatus/$1` | csrf invalidchars auth -> secureheaders |
| 498 | `POST` | `dashboard/komunikasi/kehadiran/([^/]+)/catatan` | `Â»` | `\App\Controllers\Communication\AttendanceController::record/$1` | csrf invalidchars auth -> secureheaders |
| 499 | `POST` | `dashboard/komunikasi/pesan-masuk/reply/([^/]+)` | `dashboard.communication.inbox.reply` | `\App\Controllers\Admin\Communication\ContactMessageController::reply/$1` | csrf invalidchars auth -> secureheaders |
| 500 | `POST` | `dashboard/komunikasi/pesan-masuk/update-status/([^/]+)` | `dashboard.communication.inbox.update-status` | `\App\Controllers\Admin\Communication\ContactMessageController::updateStatus/$1` | csrf invalidchars auth -> secureheaders |
| 501 | `POST` | `dashboard/komunikasi/pesan-masuk/hapus/([^/]+)` | `dashboard.communication.inbox.delete` | `\App\Controllers\Admin\Communication\ContactMessageController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 502 | `POST` | `dashboard/komunikasi/whatsapp-widget/pengaturan` | `dashboard.communication.helpdesk.settings` | `\App\Controllers\Admin\Communication\HelpdeskWidgetController::saveSettings` | csrf invalidchars auth -> secureheaders |
| 503 | `POST` | `dashboard/komunikasi/whatsapp-widget/simpan-topic` | `dashboard.communication.helpdesk.topic.store` | `\App\Controllers\Admin\Communication\HelpdeskWidgetController::storeTopic` | csrf invalidchars auth -> secureheaders |
| 504 | `POST` | `dashboard/komunikasi/whatsapp-widget/ubah-topic/([^/]+)` | `dashboard.communication.helpdesk.topic.update` | `\App\Controllers\Admin\Communication\HelpdeskWidgetController::updateTopic/$1` | csrf invalidchars auth -> secureheaders |
| 505 | `POST` | `dashboard/komunikasi/whatsapp-widget/hapus-topic/([^/]+)` | `dashboard.communication.helpdesk.topic.delete` | `\App\Controllers\Admin\Communication\HelpdeskWidgetController::deleteTopic/$1` | csrf invalidchars auth -> secureheaders |
| 506 | `POST` | `dashboard/komunikasi/whatsapp-widget/toggle-topic/([^/]+)` | `dashboard.communication.helpdesk.topic.toggle` | `\App\Controllers\Admin\Communication\HelpdeskWidgetController::toggleTopic/$1` | csrf invalidchars auth -> secureheaders |
| 507 | `POST` | `dashboard/komunikasi/newsletter/hapus/([^/]+)` | `dashboard.communication.newsletter.delete` | `\App\Controllers\Admin\Communication\NewsletterController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 508 | `POST` | `dashboard/komunikasi/email/broadcast` | `dashboard.communication.email.broadcast` | `\App\Controllers\Admin\Communication\EmailBroadcastController::sendBroadcast` | csrf invalidchars auth -> secureheaders |
| 509 | `POST` | `dashboard/komunikasi/email/send-one` | `dashboard.communication.email.send-one` | `\App\Controllers\Admin\Communication\EmailBroadcastController::sendOne` | csrf invalidchars auth -> secureheaders |
| 510 | `POST` | `dashboard/komunikasi/email/dispatch-item/([0-9]+)` | `dashboard.communication.email.dispatch-item` | `\App\Controllers\Admin\Communication\EmailBroadcastController::dispatchItem/$1` | csrf invalidchars auth -> secureheaders |
| 511 | `POST` | `dashboard/komunikasi/email/dispatch-ready` | `dashboard.communication.email.dispatch-ready` | `\App\Controllers\Admin\Communication\EmailBroadcastController::dispatchReady` | csrf invalidchars auth -> secureheaders |
| 512 | `POST` | `dashboard/komunikasi/email/retry/([0-9]+)` | `dashboard.communication.email.retry` | `\App\Controllers\Admin\Communication\EmailBroadcastController::retry/$1` | csrf invalidchars auth -> secureheaders |
| 513 | `POST` | `dashboard/komunikasi/email/delete/([0-9]+)` | `dashboard.communication.email.delete` | `\App\Controllers\Admin\Communication\EmailBroadcastController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 514 | `POST` | `dashboard/komunikasi/email/preview` | `dashboard.communication.email.preview` | `\App\Controllers\Admin\Communication\EmailBroadcastController::preview` | csrf invalidchars auth -> secureheaders |
| 515 | `POST` | `dashboard/sertifikat/learning/issue/([^/]+)` | `dashboard.certificates.learning.issue-single` | `\App\Controllers\Certificate\CertificateLearningController::issueSingle/$1` | csrf invalidchars auth -> secureheaders |
| 516 | `POST` | `dashboard/sertifikat/learning/kelas/([^/]+)/batch` | `dashboard.certificates.learning.issue-batch` | `\App\Controllers\Certificate\CertificateLearningController::issueClassBatch/$1` | csrf invalidchars auth -> secureheaders |
| 517 | `POST` | `dashboard/sertifikat/event/issue/([^/]+)` | `dashboard.certificates.events.issue-single` | `\App\Controllers\Certificate\CertificateEventController::issueSingle/$1` | csrf invalidchars auth -> secureheaders |
| 518 | `POST` | `dashboard/sertifikat/event/([^/]+)/batch` | `dashboard.certificates.events.issue-batch` | `\App\Controllers\Certificate\CertificateEventController::issueBatch/$1` | csrf invalidchars auth -> secureheaders |
| 519 | `POST` | `dashboard/sertifikat/pengajuan-event/([^/]+)/generate` | `dashboard.certificates.event-approval.generate` | `\App\Controllers\Certificate\CertificateEventController::generate/$1` | csrf invalidchars auth -> secureheaders |
| 520 | `POST` | `dashboard/sertifikat/batch/learning` | `dashboard.certificates.batch.learning` | `\App\Controllers\Certificate\CertificateBatchController::processLearningBatch` | csrf invalidchars auth -> secureheaders |
| 521 | `POST` | `dashboard/sertifikat/batch/event` | `dashboard.certificates.batch.event` | `\App\Controllers\Certificate\CertificateBatchController::processEventBatch` | csrf invalidchars auth -> secureheaders |
| 522 | `POST` | `dashboard/sertifikat/template/simpan` | `dashboard.certificates.templates.store` | `\App\Controllers\Certificate\CertificateTemplateController::store` | csrf invalidchars auth -> secureheaders |
| 523 | `POST` | `dashboard/sertifikat/template/inspect-pdf` | `dashboard.certificates.templates.inspect-pdf` | `\App\Controllers\Certificate\CertificateTemplateController::inspectPdf` | csrf invalidchars auth -> secureheaders |
| 524 | `POST` | `dashboard/sertifikat/template/live-preview-pdf` | `dashboard.certificates.templates.live-preview-pdf` | `\App\Controllers\Certificate\CertificateTemplateController::livePreviewPdf` | csrf invalidchars auth -> secureheaders |
| 525 | `POST` | `dashboard/sertifikat/template/([^/]+)/ubah` | `dashboard.certificates.templates.update` | `\App\Controllers\Certificate\CertificateTemplateController::update/$1` | csrf invalidchars auth -> secureheaders |
| 526 | `POST` | `dashboard/sertifikat/template/([^/]+)/aktifkan` | `dashboard.certificates.templates.activate` | `\App\Controllers\Certificate\CertificateTemplateController::activate/$1` | csrf invalidchars auth -> secureheaders |
| 527 | `POST` | `dashboard/sertifikat/template/([^/]+)/nonaktifkan` | `dashboard.certificates.templates.deactivate` | `\App\Controllers\Certificate\CertificateTemplateController::deactivate/$1` | csrf invalidchars auth -> secureheaders |
| 528 | `POST` | `dashboard/sertifikat/verifikasi/cari` | `dashboard.certificates.verification.check` | `\App\Controllers\Certificate\CertificateController::checkVerification` | csrf invalidchars auth -> secureheaders |
| 529 | `POST` | `dashboard/sertifikat/approval/([^/]+)/terbitkan` | `dashboard.certificates.approvals.issue` | `\App\Controllers\Admin\ApprovalController::approve/$1` | csrf invalidchars auth -> secureheaders |
| 530 | `POST` | `dashboard/sertifikat/approval/([^/]+)/tolak` | `dashboard.certificates.approvals.reject` | `\App\Controllers\Admin\ApprovalController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 531 | `POST` | `dashboard/sertifikat/([^/]+)/ubah` | `Â»` | `\App\Controllers\Certificate\CertificateController::update/$1` | csrf invalidchars auth -> secureheaders |
| 532 | `POST` | `dashboard/sertifikat/([^/]+)/cabut` | `dashboard.certificates.revoke` | `\App\Controllers\Certificate\CertificateController::revoke/$1` | csrf invalidchars auth -> secureheaders |
| 533 | `POST` | `dashboard/sertifikat/([^/]+)/hapus-dan-requeue` | `dashboard.certificates.delete-requeue` | `\App\Controllers\Certificate\CertificateController::deleteAndRequeue/$1` | csrf invalidchars auth -> secureheaders |
| 534 | `POST` | `dashboard/intelligence/opd-unit/tambah` | `dashboard.intelligence.organization-units.create` | `\App\Controllers\Intelligence\OpdController::create` | csrf invalidchars auth -> secureheaders |
| 535 | `POST` | `dashboard/intelligence/sdm-jf-ppbj/tambah` | `dashboard.intelligence.personnel.create` | `\App\Controllers\Intelligence\JfPpbjController::create` | csrf invalidchars auth -> secureheaders |
| 536 | `POST` | `dashboard/intelligence/sertifikat-kompetensi/tambah` | `dashboard.intelligence.competency-certificates.create` | `\App\Controllers\Intelligence\CompetencyCertificateController::create` | csrf invalidchars auth -> secureheaders |
| 537 | `POST` | `dashboard/intelligence/gap-kompetensi/snapshot` | `dashboard.intelligence.competency-gap.snapshot` | `\App\Controllers\Intelligence\IntelligenceDashboardController::generateSnapshot` | csrf invalidchars auth -> secureheaders |
| 538 | `POST` | `dashboard/landing/sorotan-carousel/toggle` | `dashboard.landing.spotlight_hub.toggle` | `\App\Controllers\Admin\Landing\SpotlightHubController::toggle` | csrf invalidchars auth -> secureheaders |
| 539 | `POST` | `dashboard/landing/sorotan-carousel/update-order` | `dashboard.landing.spotlight_hub.order` | `\App\Controllers\Admin\Landing\SpotlightHubController::updateOrder` | csrf invalidchars auth -> secureheaders |
| 540 | `POST` | `dashboard/landing/sorotan-carousel/pengaturan` | `dashboard.landing.spotlight_hub.settings` | `\App\Controllers\Admin\Landing\SpotlightHubController::saveSettings` | csrf invalidchars auth -> secureheaders |
| 541 | `POST` | `dashboard/landing/sorotan-carousel/bulk-action` | `dashboard.landing.spotlight_hub.bulk` | `\App\Controllers\Admin\Landing\SpotlightHubController::bulkAction` | csrf invalidchars auth -> secureheaders |
| 542 | `POST` | `dashboard/landing/slider/simpan` | `dashboard.landing.sliders.store` | `\App\Controllers\Admin\Landing\SliderController::store` | csrf invalidchars auth -> secureheaders |
| 543 | `POST` | `dashboard/landing/slider/ubah/([^/]+)` | `dashboard.landing.sliders.update` | `\App\Controllers\Admin\Landing\SliderController::update/$1` | csrf invalidchars auth -> secureheaders |
| 544 | `POST` | `dashboard/landing/slider/edit/([^/]+)` | `dashboard.landing.sliders.update_alias` | `\App\Controllers\Admin\Landing\SliderController::update/$1` | csrf invalidchars auth -> secureheaders |
| 545 | `POST` | `dashboard/landing/slider/duplikat/([^/]+)` | `dashboard.landing.sliders.duplicate` | `\App\Controllers\Admin\Landing\SliderController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 546 | `POST` | `dashboard/landing/slider/update-order` | `dashboard.landing.sliders.order` | `\App\Controllers\Admin\Landing\SliderController::updateOrder` | csrf invalidchars auth -> secureheaders |
| 547 | `POST` | `dashboard/landing/slider/hapus/([^/]+)` | `dashboard.landing.sliders.delete` | `\App\Controllers\Admin\Landing\SliderController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 548 | `POST` | `dashboard/landing/slider/toggle/([^/]+)` | `dashboard.landing.sliders.toggle` | `\App\Controllers\Admin\Landing\SliderController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 549 | `POST` | `dashboard/landing/pengumuman/pengaturan` | `dashboard.landing.announcements.settings` | `\App\Controllers\Admin\Landing\AnnouncementController::saveSettings` | csrf invalidchars auth -> secureheaders |
| 550 | `POST` | `dashboard/landing/pengumuman/simpan` | `dashboard.landing.announcements.store` | `\App\Controllers\Admin\Landing\AnnouncementController::store` | csrf invalidchars auth -> secureheaders |
| 551 | `POST` | `dashboard/landing/pengumuman/ubah/([^/]+)` | `dashboard.landing.announcements.update` | `\App\Controllers\Admin\Landing\AnnouncementController::update/$1` | csrf invalidchars auth -> secureheaders |
| 552 | `POST` | `dashboard/landing/pengumuman/edit/([^/]+)` | `dashboard.landing.announcements.update_alias` | `\App\Controllers\Admin\Landing\AnnouncementController::update/$1` | csrf invalidchars auth -> secureheaders |
| 553 | `POST` | `dashboard/landing/pengumuman/duplikat/([^/]+)` | `dashboard.landing.announcements.duplicate` | `\App\Controllers\Admin\Landing\AnnouncementController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 554 | `POST` | `dashboard/landing/pengumuman/update-order` | `dashboard.landing.announcements.order` | `\App\Controllers\Admin\Landing\AnnouncementController::updateOrder` | csrf invalidchars auth -> secureheaders |
| 555 | `POST` | `dashboard/landing/pengumuman/hapus/([^/]+)` | `dashboard.landing.announcements.delete` | `\App\Controllers\Admin\Landing\AnnouncementController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 556 | `POST` | `dashboard/landing/pengumuman/toggle/([^/]+)` | `dashboard.landing.announcements.toggle` | `\App\Controllers\Admin\Landing\AnnouncementController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 557 | `POST` | `dashboard/landing/profil-lembaga` | `dashboard.landing.profile.save` | `\App\Controllers\Admin\Landing\InstitutionProfileController::save` | csrf invalidchars auth -> secureheaders |
| 558 | `POST` | `dashboard/landing/profil-lembaga/reset` | `dashboard.landing.profile.reset` | `\App\Controllers\Admin\Landing\InstitutionProfileController::resetDefault` | csrf invalidchars auth -> secureheaders |
| 559 | `POST` | `dashboard/landing/pilar-hierarki/simpan` | `dashboard.landing.highlights.store` | `\App\Controllers\Admin\Landing\HighlightFeatureController::store` | csrf invalidchars auth -> secureheaders |
| 560 | `POST` | `dashboard/landing/pilar-hierarki/ubah/([^/]+)` | `dashboard.landing.highlights.update` | `\App\Controllers\Admin\Landing\HighlightFeatureController::update/$1` | csrf invalidchars auth -> secureheaders |
| 561 | `POST` | `dashboard/landing/pilar-hierarki/edit/([^/]+)` | `dashboard.landing.highlights.update_alias` | `\App\Controllers\Admin\Landing\HighlightFeatureController::update/$1` | csrf invalidchars auth -> secureheaders |
| 562 | `POST` | `dashboard/landing/pilar-hierarki/duplikat/([^/]+)` | `dashboard.landing.highlights.duplicate` | `\App\Controllers\Admin\Landing\HighlightFeatureController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 563 | `POST` | `dashboard/landing/pilar-hierarki/update-order` | `dashboard.landing.highlights.order` | `\App\Controllers\Admin\Landing\HighlightFeatureController::updateOrder` | csrf invalidchars auth -> secureheaders |
| 564 | `POST` | `dashboard/landing/pilar-hierarki/muat-rekomendasi/([^/]+)` | `dashboard.landing.highlights.recommendations` | `\App\Controllers\Admin\Landing\HighlightFeatureController::seedRecommendations/$1` | csrf invalidchars auth -> secureheaders |
| 565 | `POST` | `dashboard/landing/pilar-hierarki/hapus/([^/]+)` | `dashboard.landing.highlights.delete` | `\App\Controllers\Admin\Landing\HighlightFeatureController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 566 | `POST` | `dashboard/landing/pilar-hierarki/toggle/([^/]+)` | `dashboard.landing.highlights.toggle` | `\App\Controllers\Admin\Landing\HighlightFeatureController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 567 | `POST` | `dashboard/landing/portal-eksternal/simpan` | `dashboard.landing.portals.store` | `\App\Controllers\Admin\Landing\ExternalPortalController::store` | csrf invalidchars auth -> secureheaders |
| 568 | `POST` | `dashboard/landing/portal-eksternal/ubah/([^/]+)` | `dashboard.landing.portals.update` | `\App\Controllers\Admin\Landing\ExternalPortalController::update/$1` | csrf invalidchars auth -> secureheaders |
| 569 | `POST` | `dashboard/landing/portal-eksternal/edit/([^/]+)` | `dashboard.landing.portals.update_alias` | `\App\Controllers\Admin\Landing\ExternalPortalController::update/$1` | csrf invalidchars auth -> secureheaders |
| 570 | `POST` | `dashboard/landing/portal-eksternal/duplikat/([^/]+)` | `dashboard.landing.portals.duplicate` | `\App\Controllers\Admin\Landing\ExternalPortalController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 571 | `POST` | `dashboard/landing/portal-eksternal/update-order` | `dashboard.landing.portals.order` | `\App\Controllers\Admin\Landing\ExternalPortalController::updateOrder` | csrf invalidchars auth -> secureheaders |
| 572 | `POST` | `dashboard/landing/portal-eksternal/muat-rekomendasi` | `dashboard.landing.portals.recommendations` | `\App\Controllers\Admin\Landing\ExternalPortalController::seedRecommendations` | csrf invalidchars auth -> secureheaders |
| 573 | `POST` | `dashboard/landing/portal-eksternal/hapus/([^/]+)` | `dashboard.landing.portals.delete` | `\App\Controllers\Admin\Landing\ExternalPortalController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 574 | `POST` | `dashboard/landing/portal-eksternal/toggle/([^/]+)` | `dashboard.landing.portals.toggle` | `\App\Controllers\Admin\Landing\ExternalPortalController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 575 | `POST` | `dashboard/landing/testimoni/simpan` | `dashboard.landing.testimonials.store` | `\App\Controllers\Admin\Landing\TestimonialController::store` | csrf invalidchars auth -> secureheaders |
| 576 | `POST` | `dashboard/landing/testimoni/ubah/([^/]+)` | `dashboard.landing.testimonials.update` | `\App\Controllers\Admin\Landing\TestimonialController::update/$1` | csrf invalidchars auth -> secureheaders |
| 577 | `POST` | `dashboard/landing/testimoni/edit/([^/]+)` | `Â»` | `\App\Controllers\Admin\Landing\TestimonialController::update/$1` | csrf invalidchars auth -> secureheaders |
| 578 | `POST` | `dashboard/landing/testimoni/duplikat/([^/]+)` | `dashboard.landing.testimonials.duplicate` | `\App\Controllers\Admin\Landing\TestimonialController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 579 | `POST` | `dashboard/landing/testimoni/update-order` | `dashboard.landing.testimonials.update-order` | `\App\Controllers\Admin\Landing\TestimonialController::updateOrder` | csrf invalidchars auth -> secureheaders |
| 580 | `POST` | `dashboard/landing/testimoni/muat-rekomendasi` | `dashboard.landing.testimonials.recommendations` | `\App\Controllers\Admin\Landing\TestimonialController::seedRecommendations` | csrf invalidchars auth -> secureheaders |
| 581 | `POST` | `dashboard/landing/testimoni/hapus/([^/]+)` | `dashboard.landing.testimonials.delete` | `\App\Controllers\Admin\Landing\TestimonialController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 582 | `POST` | `dashboard/landing/testimoni/toggle/([^/]+)` | `dashboard.landing.testimonials.toggle` | `\App\Controllers\Admin\Landing\TestimonialController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 583 | `POST` | `dashboard/landing/tim/simpan` | `dashboard.landing.team.store` | `\App\Controllers\Admin\Landing\TeamMemberController::store` | csrf invalidchars auth -> secureheaders |
| 584 | `POST` | `dashboard/landing/tim/ubah/([^/]+)` | `dashboard.landing.team.update` | `\App\Controllers\Admin\Landing\TeamMemberController::update/$1` | csrf invalidchars auth -> secureheaders |
| 585 | `POST` | `dashboard/landing/tim/edit/([^/]+)` | `Â»` | `\App\Controllers\Admin\Landing\TeamMemberController::update/$1` | csrf invalidchars auth -> secureheaders |
| 586 | `POST` | `dashboard/landing/tim/duplikat/([^/]+)` | `dashboard.landing.team.duplicate` | `\App\Controllers\Admin\Landing\TeamMemberController::duplicate/$1` | csrf invalidchars auth -> secureheaders |
| 587 | `POST` | `dashboard/landing/tim/update-order` | `dashboard.landing.team.update-order` | `\App\Controllers\Admin\Landing\TeamMemberController::updateOrder` | csrf invalidchars auth -> secureheaders |
| 588 | `POST` | `dashboard/landing/tim/muat-rekomendasi` | `dashboard.landing.team.recommendations` | `\App\Controllers\Admin\Landing\TeamMemberController::seedRecommendations` | csrf invalidchars auth -> secureheaders |
| 589 | `POST` | `dashboard/landing/tim/hapus/([^/]+)` | `dashboard.landing.team.delete` | `\App\Controllers\Admin\Landing\TeamMemberController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 590 | `POST` | `dashboard/landing/tim/toggle/([^/]+)` | `dashboard.landing.team.toggle` | `\App\Controllers\Admin\Landing\TeamMemberController::toggle/$1` | csrf invalidchars auth -> secureheaders |
| 591 | `POST` | `dashboard/survei-kepuasan/update-aspect/([^/]+)` | `dashboard.survey.aspect.update` | `\App\Controllers\Admin\Survey\SatisfactionSurveyAdminController::updateAspect/$1` | csrf invalidchars auth -> secureheaders |
| 592 | `POST` | `dashboard/survei-kepuasan/hapus/([^/]+)` | `dashboard.survey.response.delete` | `\App\Controllers\Admin\Survey\SatisfactionSurveyAdminController::deleteResponse/$1` | csrf invalidchars auth -> secureheaders |
| 593 | `POST` | `dashboard/sistem/approval/bulk` | `dashboard.system.approvals.bulk` | `\App\Controllers\Admin\ApprovalController::bulk` | csrf invalidchars auth -> secureheaders |
| 594 | `POST` | `dashboard/sistem/approval/([^/]+)/setujui` | `dashboard.system.approvals.approve` | `\App\Controllers\Admin\ApprovalController::approve/$1` | csrf invalidchars auth -> secureheaders |
| 595 | `POST` | `dashboard/sistem/approval/([^/]+)/tolak` | `dashboard.system.approvals.reject` | `\App\Controllers\Admin\ApprovalController::reject/$1` | csrf invalidchars auth -> secureheaders |
| 596 | `POST` | `dashboard/sistem/notifikasi/kirim` | `dashboard.system.notifications.send` | `\App\Controllers\Admin\NotificationController::sendBroadcast` | csrf invalidchars auth -> secureheaders |
| 597 | `POST` | `dashboard/sistem/notifikasi/([^/]+)/baca` | `dashboard.system.notifications.read` | `\App\Controllers\Admin\NotificationController::markAsRead/$1` | csrf invalidchars auth -> secureheaders |
| 598 | `POST` | `dashboard/sistem/notifikasi/baca-semua` | `dashboard.system.notifications.read-all` | `\App\Controllers\Admin\NotificationController::markAllAsRead` | csrf invalidchars auth -> secureheaders |
| 599 | `POST` | `dashboard/sistem/notifikasi/([^/]+)/hapus` | `dashboard.system.notifications.delete` | `\App\Controllers\Admin\NotificationController::delete/$1` | csrf invalidchars auth -> secureheaders |
| 600 | `POST` | `dashboard/sistem/pengaturan-notifikasi/test-email` | `dashboard.system.settings.notification.test` | `\App\Controllers\Admin\SettingController::testEmail` | csrf invalidchars auth -> secureheaders |
| 601 | `POST` | `dashboard/sistem/statistik-kunjungan` | `dashboard.system.visitor_stats.update` | `\App\Controllers\Admin\VisitorStatisticAdminController::update` | csrf invalidchars auth -> secureheaders |
| 602 | `POST` | `dashboard/sistem/statistik-kunjungan/reset` | `dashboard.system.visitor_stats.reset` | `\App\Controllers\Admin\VisitorStatisticAdminController::resetData` | csrf invalidchars auth -> secureheaders |
| 603 | `POST` | `dashboard/sistem/statistik-kunjungan/reset-today` | `dashboard.system.visitor_stats.reset_today` | `\App\Controllers\Admin\VisitorStatisticAdminController::resetToday` | csrf invalidchars auth -> secureheaders |
| 604 | `POST` | `dashboard/sistem/pengaturan/([^/]+)` | `dashboard.system.settings.save` | `\App\Controllers\Admin\SettingController::save/$1` | csrf invalidchars auth -> secureheaders |
| 605 | `POST` | `dashboard/sistem/settings/([^/]+)` | `Â»` | `\App\Controllers\Admin\SettingController::save/$1` | csrf invalidchars auth -> secureheaders |
| 606 | `POST` | `dashboard/pengaturan/([^/]+)` | `Â»` | `\App\Controllers\Admin\SettingController::save/$1` | csrf invalidchars auth -> secureheaders |
| 607 | `POST` | `dashboard/settings/([^/]+)` | `Â»` | `\App\Controllers\Admin\SettingController::save/$1` | csrf invalidchars auth -> secureheaders |
| 608 | `POST` | `dashboard/pembelajaran/pengajar` | `Â»` | `dashboard/pembelajaran/kelas` | - |
| 609 | `POST` | `dashboard/bunga-rampai/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 610 | `POST` | `dashboard/komunikasi/helpdesk` | `Â»` | `dashboard/komunikasi/whatsapp-widget` | - |
| 611 | `POST` | `dashboard/landing/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 612 | `POST` | `dashboard/landing/banner-halaman` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 613 | `POST` | `dashboard/landing/banner-halaman/(.*)` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 614 | `POST` | `dashboard/landing/kategori-layanan` | `Â»` | `dashboard/landing` | - |
| 615 | `POST` | `dashboard/landing/kategori-layanan/(.*)` | `Â»` | `dashboard/landing` | - |
| 616 | `PATCH` | `dashboard/pembelajaran/pengajar` | `Â»` | `dashboard/pembelajaran/kelas` | - |
| 617 | `PATCH` | `dashboard/bunga-rampai/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 618 | `PATCH` | `dashboard/komunikasi/helpdesk` | `Â»` | `dashboard/komunikasi/whatsapp-widget` | - |
| 619 | `PATCH` | `dashboard/landing/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 620 | `PATCH` | `dashboard/landing/banner-halaman` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 621 | `PATCH` | `dashboard/landing/banner-halaman/(.*)` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 622 | `PATCH` | `dashboard/landing/kategori-layanan` | `Â»` | `dashboard/landing` | - |
| 623 | `PATCH` | `dashboard/landing/kategori-layanan/(.*)` | `Â»` | `dashboard/landing` | - |
| 624 | `PUT` | `dashboard/pembelajaran/pengajar` | `Â»` | `dashboard/pembelajaran/kelas` | - |
| 625 | `PUT` | `dashboard/bunga-rampai/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 626 | `PUT` | `dashboard/komunikasi/helpdesk` | `Â»` | `dashboard/komunikasi/whatsapp-widget` | - |
| 627 | `PUT` | `dashboard/landing/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 628 | `PUT` | `dashboard/landing/banner-halaman` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 629 | `PUT` | `dashboard/landing/banner-halaman/(.*)` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 630 | `PUT` | `dashboard/landing/kategori-layanan` | `Â»` | `dashboard/landing` | - |
| 631 | `PUT` | `dashboard/landing/kategori-layanan/(.*)` | `Â»` | `dashboard/landing` | - |
| 632 | `DELETE` | `dashboard/pembelajaran/pengajar` | `Â»` | `dashboard/pembelajaran/kelas` | - |
| 633 | `DELETE` | `dashboard/bunga-rampai/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 634 | `DELETE` | `dashboard/komunikasi/helpdesk` | `Â»` | `dashboard/komunikasi/whatsapp-widget` | - |
| 635 | `DELETE` | `dashboard/landing/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 636 | `DELETE` | `dashboard/landing/banner-halaman` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 637 | `DELETE` | `dashboard/landing/banner-halaman/(.*)` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 638 | `DELETE` | `dashboard/landing/kategori-layanan` | `Â»` | `dashboard/landing` | - |
| 639 | `DELETE` | `dashboard/landing/kategori-layanan/(.*)` | `Â»` | `dashboard/landing` | - |
| 640 | `OPTIONS` | `dashboard/pembelajaran/pengajar` | `Â»` | `dashboard/pembelajaran/kelas` | - |
| 641 | `OPTIONS` | `dashboard/bunga-rampai/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 642 | `OPTIONS` | `dashboard/komunikasi/helpdesk` | `Â»` | `dashboard/komunikasi/whatsapp-widget` | - |
| 643 | `OPTIONS` | `dashboard/landing/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 644 | `OPTIONS` | `dashboard/landing/banner-halaman` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 645 | `OPTIONS` | `dashboard/landing/banner-halaman/(.*)` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 646 | `OPTIONS` | `dashboard/landing/kategori-layanan` | `Â»` | `dashboard/landing` | - |
| 647 | `OPTIONS` | `dashboard/landing/kategori-layanan/(.*)` | `Â»` | `dashboard/landing` | - |
| 648 | `TRACE` | `dashboard/pembelajaran/pengajar` | `Â»` | `dashboard/pembelajaran/kelas` | - |
| 649 | `TRACE` | `dashboard/bunga-rampai/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 650 | `TRACE` | `dashboard/komunikasi/helpdesk` | `Â»` | `dashboard/komunikasi/whatsapp-widget` | - |
| 651 | `TRACE` | `dashboard/landing/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 652 | `TRACE` | `dashboard/landing/banner-halaman` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 653 | `TRACE` | `dashboard/landing/banner-halaman/(.*)` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 654 | `TRACE` | `dashboard/landing/kategori-layanan` | `Â»` | `dashboard/landing` | - |
| 655 | `TRACE` | `dashboard/landing/kategori-layanan/(.*)` | `Â»` | `dashboard/landing` | - |
| 656 | `CONNECT` | `dashboard/pembelajaran/pengajar` | `Â»` | `dashboard/pembelajaran/kelas` | - |
| 657 | `CONNECT` | `dashboard/bunga-rampai/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 658 | `CONNECT` | `dashboard/komunikasi/helpdesk` | `Â»` | `dashboard/komunikasi/whatsapp-widget` | - |
| 659 | `CONNECT` | `dashboard/landing/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 660 | `CONNECT` | `dashboard/landing/banner-halaman` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 661 | `CONNECT` | `dashboard/landing/banner-halaman/(.*)` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 662 | `CONNECT` | `dashboard/landing/kategori-layanan` | `Â»` | `dashboard/landing` | - |
| 663 | `CONNECT` | `dashboard/landing/kategori-layanan/(.*)` | `Â»` | `dashboard/landing` | - |
| 664 | `CLI` | `dashboard/pembelajaran/pengajar` | `Â»` | `dashboard/pembelajaran/kelas` | - |
| 665 | `CLI` | `dashboard/bunga-rampai/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 666 | `CLI` | `dashboard/komunikasi/helpdesk` | `Â»` | `dashboard/komunikasi/whatsapp-widget` | - |
| 667 | `CLI` | `dashboard/landing/carousel` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 668 | `CLI` | `dashboard/landing/banner-halaman` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 669 | `CLI` | `dashboard/landing/banner-halaman/(.*)` | `Â»` | `dashboard/landing/sorotan-carousel` | - |
| 670 | `CLI` | `dashboard/landing/kategori-layanan` | `Â»` | `dashboard/landing` | - |
| 671 | `CLI` | `dashboard/landing/kategori-layanan/(.*)` | `Â»` | `dashboard/landing` | - |

---

## Zona 5: API, Webhook & Operasional Sistem (0 Rute)

| No | HTTP Method | Pola Rute / URI | Nama Rute | Controller Handler | Filter Sebelum / Sesudah |
|---|:---:|---|---|---|---|

---

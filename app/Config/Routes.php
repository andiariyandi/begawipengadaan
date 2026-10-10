<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('', ['namespace' => 'App\Controllers\Landing'], static function ($routes) {
    $routes->get('/', 'HomeController::index', ['as' => 'public.home']);
    $routes->get('file/(:segment)', 'FileController::show/$1', ['as' => 'public.file.show']);
    $routes->get('tentang', 'HomeController::about', ['as' => 'public.about']);
    $routes->get('kontak', 'HomeController::contact', ['as' => 'public.contact']);

    $routes->group('arena-belajar', static function ($routes) {
        $routes->get('/', 'ArenaBelajarController::index', ['as' => 'public.learning.index']);
        $routes->get('program', 'ArenaBelajarController::programs', ['as' => 'public.learning.programs']);
        $routes->get('program/(:segment)', 'ArenaBelajarController::programDetail/$1', ['as' => 'public.learning.program.detail']);
        $routes->get('course', 'ArenaBelajarController::courses', ['as' => 'public.learning.courses']);
        $routes->get('course/(:segment)', 'ArenaBelajarController::courseDetail/$1', ['as' => 'public.learning.course.detail']);
        $routes->get('kelas', 'ArenaBelajarController::classes', ['as' => 'public.learning.classes']);
        $routes->get('kelas/(:segment)', 'ArenaBelajarController::classDetail/$1', ['as' => 'public.learning.class.detail']);
        $routes->post('kelas/(:segment)/daftar', 'ArenaBelajarController::enroll/$1');
        $routes->get('pengajar', 'ArenaBelajarController::trainers', ['as' => 'public.learning.trainers']);
        $routes->get('pengajar/(:segment)', 'ArenaBelajarController::trainerDetail/$1', ['as' => 'public.learning.trainer.detail']);
    });

    $routes->group('bunga-rampai', static function ($routes) {
        $routes->get('/', 'BungaRampaiController::index', ['as' => 'public.knowledge.index']);
        $routes->get('artikel', 'BungaRampaiController::articles', ['as' => 'public.knowledge.articles']);
        $routes->get('artikel/(:segment)', 'BungaRampaiController::articleDetail/$1', ['as' => 'public.knowledge.article.detail']);
        $routes->get('faq', 'BungaRampaiController::faqs', ['as' => 'public.knowledge.faqs']);
        $routes->get('studi-kasus', 'BungaRampaiController::caseStudies', ['as' => 'public.knowledge.case-studies']);
        $routes->get('studi-kasus/(:segment)', 'BungaRampaiController::caseStudyDetail/$1', ['as' => 'public.knowledge.case-study.detail']);
        $routes->get('template-dokumen', 'BungaRampaiController::templates', ['as' => 'public.knowledge.templates']);
        $routes->get('template-dokumen/(:segment)', 'BungaRampaiController::templateDetail/$1', ['as' => 'public.knowledge.templates.detail']);
        $routes->get('template-dokumen/(:segment)/unduh', 'BungaRampaiController::templateDownload/$1', ['as' => 'public.knowledge.templates.download']);
        $routes->get('media', 'BungaRampaiController::media', ['as' => 'public.knowledge.media']);
        $routes->get('video', 'BungaRampaiController::videos', ['as' => 'public.knowledge.videos']);
        $routes->get('galeri', 'BungaRampaiController::gallery', ['as' => 'public.knowledge.gallery']);
    });

    $routes->group('regulasi', static function ($routes) {
        $routes->get('/', 'RegulationPublicController::index', ['as' => 'public.regulations.index']);
        $routes->get('kategori/(:segment)', 'RegulationPublicController::category/$1', ['as' => 'public.regulations.category']);
        $routes->get('(:segment)/versi', 'RegulationPublicController::versions/$1', ['as' => 'public.regulations.versions']);
        $routes->get('(:segment)/relasi', 'RegulationPublicController::relations/$1', ['as' => 'public.regulations.relations']);
        $routes->get('(:segment)/preview', 'RegulationPublicController::preview/$1', ['as' => 'public.regulations.preview']);
        $routes->get('(:segment)/unduh', 'RegulationPublicController::download/$1', ['as' => 'public.regulations.download']);
        $routes->get('(:segment)', 'RegulationPublicController::detail/$1', ['as' => 'public.regulations.detail']);
    });

    $routes->group('berita', static function ($routes) {
        $routes->get('/', 'NewsPublicController::index', ['as' => 'public.news.index']);
        $routes->get('kategori/(:segment)', 'NewsPublicController::category/$1', ['as' => 'public.news.category']);
        $routes->get('(:segment)', 'NewsPublicController::detail/$1', ['as' => 'public.news.detail']);
    });

    $routes->group('event', static function ($routes) {
        $routes->get('/', 'EventPublicController::index', ['as' => 'public.events.index']);
        $routes->get('agenda', 'EventPublicController::agenda', ['as' => 'public.events.agenda']);
        $routes->get('kalender', 'EventPublicController::calendar', ['as' => 'public.events.calendar']);
        $routes->get('(:segment)', 'EventPublicController::detail/$1', ['as' => 'public.events.detail']);
        $routes->post('(:segment)/daftar', 'EventPublicController::register/$1', ['as' => 'public.events.register']);
    });

    $routes->group('statistik', static function ($routes) {
        $routes->get('/', 'StatisticPublicController::index', ['as' => 'public.statistics.index']);
        $routes->get('pembelajaran', 'StatisticPublicController::learning', ['as' => 'public.statistics.learning']);
        $routes->get('event', 'StatisticPublicController::events', ['as' => 'public.statistics.events']);
        $routes->get('kompetensi', 'StatisticPublicController::competency', ['as' => 'public.statistics.competency']);
    });

    $routes->group('verifikasi-sertifikat', static function ($routes) {
        $routes->get('/', 'CertificateVerificationController::index', ['as' => 'public.certificate.verify']);
        $routes->post('hasil', 'CertificateVerificationController::result', ['as' => 'public.certificate.verify.result']);
        $routes->get('(:segment)', 'CertificateVerificationController::detail/$1', ['as' => 'public.certificate.verify.detail']);
    });
});

$routes->post('bookmark/toggle', 'BookmarkController::toggle', ['as' => 'public.bookmark.toggle']);

// Public QR verification route. Token tidak boleh mengandung slash.
// {token} adalah token klar hasil QR Code — di-hash (sha256) lalu di-lookup.
$routes->get('verify/certificate/(:segment)', 'Landing\\CertificateVerificationController::detail/$1', ['as' => 'public.certificate.verify.qr']);

// Auth boundary is unified on App\Controllers\Dashboard\Auth — see dashboard group below.
// Legacy App\Controllers\Auth namespace is no longer mounted.

$routes->group('workspace', ['namespace' => 'App\Controllers\Workspace'], static function ($routes) {
    $routes->get('/', 'WorkspaceHomeController::index', ['as' => 'workspace.home']);
    $routes->get('section/(:segment)', 'WorkspaceHomeController::section/$1', ['as' => 'workspace.section']);
    $routes->get('aktivitas', 'WorkspaceHomeController::activity', ['as' => 'workspace.activity']);
    $routes->get('notifikasi', 'NotificationController::index', ['as' => 'workspace.notifications']);
    $routes->get('notifikasi/(:segment)', 'NotificationController::detail/$1', ['as' => 'workspace.notifications.detail']);
    $routes->post('notifikasi/(:segment)/baca', 'NotificationController::markAsRead/$1', ['as' => 'workspace.notifications.read']);
    $routes->post('notifikasi/baca-semua', 'NotificationController::markAllAsRead', ['as' => 'workspace.notifications.read-all']);

    $routes->group('bookmark', static function ($routes) {
        $routes->get('/', 'BookmarkController::index', ['as' => 'workspace.bookmarks.index']);
        $routes->get('pembelajaran', 'BookmarkController::learning', ['as' => 'workspace.bookmarks.learning']);
        $routes->get('bunga-rampai', 'BookmarkController::knowledge', ['as' => 'workspace.bookmarks.knowledge']);
        $routes->get('regulasi', 'BookmarkController::regulations', ['as' => 'workspace.bookmarks.regulations']);
        $routes->get('komunikasi', 'BookmarkController::communication', ['as' => 'workspace.bookmarks.communication']);
        $routes->get('sertifikat', 'BookmarkController::certificates', ['as' => 'workspace.bookmarks.certificates']);
        $routes->post('/', 'BookmarkController::remove', ['as' => 'workspace.bookmarks.remove']);
    });

    // Workspace profile — migrated from /profil/* (route-contract.md §6.3).
    $routes->group('profil', static function ($routes) {
        $routes->get('/',                  'ProfileController::index',           ['as' => 'workspace.profile.index']);
        $routes->get('akun',               'ProfileController::account',         ['as' => 'workspace.profile.account']);
        $routes->get('asn',                'ProfileController::asn',             ['as' => 'workspace.profile.asn']);
        $routes->get('penyedia',           'ProfileController::provider',        ['as' => 'workspace.profile.provider']);
        $routes->get('akademik',           'ProfileController::academic',        ['as' => 'workspace.profile.academic']);
        $routes->get('masyarakat',         'ProfileController::public',          ['as' => 'workspace.profile.public']);
        $routes->get('peran-pengadaan',    'ProfileController::procurementRoles',['as' => 'workspace.profile.procurement-roles']);
        $routes->get('pengajuan-role',     'RoleRequestController::index',       ['as' => 'workspace.profile.role-requests']);
        $routes->get('pengajuan-role/baru','RoleRequestController::create',      ['as' => 'workspace.profile.role-requests.create']);
        $routes->post('ubah',              'ProfileController::update',          ['as' => 'workspace.profile.update']);
        $routes->post('kata-sandi',        'ProfileController::updatePassword',  ['as' => 'workspace.profile.password.update']);
    });

    $routes->group('peserta', static function ($routes) {
        $routes->get('/', 'ParticipantController::index', ['as' => 'workspace.participant.home']);
        $routes->get('kelas-saya', 'ParticipantController::myClasses', ['as' => 'workspace.participant.classes']);
        $routes->get('tugas', 'ParticipantController::allAssignments', ['as' => 'workspace.participant.all-assignments']);
        $routes->get('assessment', 'ParticipantController::allAssessments', ['as' => 'workspace.participant.all-assessments']);
        $routes->get('event-saya', 'ParticipantController::myEvents', ['as' => 'workspace.participant.events']);
        $routes->get('event-saya/(:segment)', 'ParticipantController::eventDetail/$1', ['as' => 'workspace.participant.event.detail']);
        $routes->get('event/(:segment)', 'ParticipantController::eventDetail/$1');
        $routes->get('presensi-saya', 'ParticipantController::myAttendance', ['as' => 'workspace.participant.attendance']);
        $routes->post('presensi/checkin', 'ParticipantController::submitAttendanceCode', ['as' => 'workspace.participant.attendance.checkin']);
        $routes->get('kelas/(:segment)', 'ParticipantController::classDetail/$1', ['as' => 'workspace.participant.class.detail']);
        $routes->get('kelas/(:segment)/belajar', 'ParticipantController::study/$1', ['as' => 'workspace.participant.study']);
        $routes->get('lesson/(:segment)', 'ParticipantController::lessonDetail/$1', ['as' => 'workspace.participant.lesson.detail']);
        $routes->get('kelas/(:segment)/progres', 'ParticipantController::progress/$1', ['as' => 'workspace.participant.progress']);
        $routes->get('kelas/(:segment)/pre-test', 'ParticipantController::preTest/$1', ['as' => 'workspace.participant.pre-test']);
        $routes->get('kelas/(:segment)/pre-test/hasil', 'ParticipantController::preTestResult/$1', ['as' => 'workspace.participant.pre-test.result']);
        $routes->get('kelas/(:segment)/quiz', 'ParticipantController::quiz/$1', ['as' => 'workspace.participant.quiz']);
        $routes->get('kelas/(:segment)/quiz/hasil', 'ParticipantController::quizResult/$1', ['as' => 'workspace.participant.quiz.result']);
        $routes->get('quiz/(:segment)', 'ParticipantController::takeQuiz/$1', ['as' => 'workspace.participant.quiz.take']);
        $routes->get('kelas/(:segment)/tugas', 'ParticipantController::assignments/$1', ['as' => 'workspace.participant.assignments']);
        $routes->get('kelas/(:segment)/tugas/kirim', 'ParticipantController::assignmentSubmission/$1', ['as' => 'workspace.participant.assignment.submission']);
        $routes->get('kelas/(:segment)/tugas/revisi', 'ParticipantController::assignmentRevision/$1', ['as' => 'workspace.participant.assignment.revision']);
        $routes->get('kelas/(:segment)/post-test', 'ParticipantController::postTest/$1', ['as' => 'workspace.participant.post-test']);
        $routes->get('kelas/(:segment)/post-test/hasil', 'ParticipantController::postTestResult/$1', ['as' => 'workspace.participant.post-test.result']);
        $routes->get('kelas/(:segment)/feedback', 'ParticipantController::feedback/$1', ['as' => 'workspace.participant.feedback']);
        $routes->post('kelas/(:segment)/daftar', 'ParticipantController::enroll/$1', ['as' => 'workspace.participant.enroll']);
        $routes->post('kelas/(:segment)/tugas/kirim', 'ParticipantController::submitAssignment/$1', ['as' => 'workspace.participant.assignment.submit']);
        $routes->post('kelas/(:segment)/feedback', 'ParticipantController::submitFeedback/$1', ['as' => 'workspace.participant.feedback.submit']);
        $routes->post('lesson/(:segment)/mulai', 'ParticipantController::startLesson/$1', ['as' => 'workspace.participant.lesson.start']);
        $routes->post('lesson/(:segment)/selesai', 'ParticipantController::completeLesson/$1', ['as' => 'workspace.participant.lesson.complete']);
        $routes->post('quiz/(:segment)/mulai', 'ParticipantController::startQuiz/$1', ['as' => 'workspace.participant.quiz.start']);
        $routes->post('quiz/attempt/(:segment)/kirim', 'ParticipantController::submitQuiz/$1', ['as' => 'workspace.participant.quiz.submit']);
        $routes->get('sertifikat', 'ParticipantController::certificates', ['as' => 'workspace.participant.certificates']);
        $routes->get('sertifikat/(:segment)', 'ParticipantController::certificateDetail/$1', ['as' => 'workspace.participant.certificate.detail']);
        $routes->get('sertifikat/(:segment)/unduh', '\App\Controllers\Certificate\CertificateDownloadController::download/$1', ['as' => 'workspace.participant.certificate.download']);
    });

    $routes->group('trainer', static function ($routes) {
        $routes->get('/', 'TrainerController::index', ['as' => 'workspace.trainer.home']);
        $routes->get('kelas-saya', 'TrainerController::myClasses', ['as' => 'workspace.trainer.classes']);
        $routes->get('materi', 'TrainerController::allMaterials', ['as' => 'workspace.trainer.all-materials']);
        $routes->get('assessment', 'TrainerController::allQuizzes', ['as' => 'workspace.trainer.all-quizzes']);
        $routes->get('peserta', 'TrainerController::allParticipants', ['as' => 'workspace.trainer.all-participants']);
        $routes->get('progres', 'TrainerController::allProgress', ['as' => 'workspace.trainer.all-progress']);
        $routes->get('kelas/(:segment)', 'TrainerController::classDetail/$1', ['as' => 'workspace.trainer.class.detail']);
        $routes->get('kelas/(:segment)/peserta', 'TrainerController::participants/$1', ['as' => 'workspace.trainer.participants']);
        $routes->get('kelas/(:segment)/peserta/(:segment)', 'TrainerController::participantDetail/$1/$2', ['as' => 'workspace.trainer.participant.detail']);
        $routes->post('kelas/(:segment)/peserta/(:segment)/feedback', 'TrainerController::submitParticipantFeedback/$1/$2', ['as' => 'workspace.trainer.participant.feedback']);
        $routes->get('kelas/(:segment)/materi', 'TrainerController::materials/$1', ['as' => 'workspace.trainer.materials']);
        $routes->get('kelas/(:segment)/modul', 'TrainerController::modules/$1', ['as' => 'workspace.trainer.modules']);
        $routes->get('kelas/(:segment)/modul/tambah', 'TrainerController::createModule/$1', ['as' => 'workspace.trainer.module.create']);
        $routes->post('kelas/(:segment)/modul/tambah', 'TrainerController::createModule/$1');
        $routes->get('kelas/(:segment)/modul/(:segment)/ubah', 'TrainerController::editModule/$1/$2', ['as' => 'workspace.trainer.module.edit']);
        $routes->post('kelas/(:segment)/modul/(:segment)/ubah', 'TrainerController::editModule/$1/$2');
        $routes->post('kelas/(:segment)/modul/(:segment)/hapus', 'TrainerController::deleteModule/$1/$2', ['as' => 'workspace.trainer.module.delete']);
        $routes->post('kelas/(:segment)/modul/(:segment)/toggle-status', 'TrainerController::toggleModuleStatus/$1/$2', ['as' => 'workspace.trainer.module.toggle-status']);
        $routes->get('kelas/(:segment)/modul/(:segment)/lesson/tambah', 'TrainerController::createLesson/$1/$2', ['as' => 'workspace.trainer.lesson.create']);
        $routes->post('kelas/(:segment)/modul/(:segment)/lesson/tambah', 'TrainerController::createLesson/$1/$2');
        $routes->get('kelas/(:segment)/lesson/(:segment)/ubah', 'TrainerController::editLesson/$1/$2', ['as' => 'workspace.trainer.lesson.edit']);
        $routes->post('kelas/(:segment)/lesson/(:segment)/ubah', 'TrainerController::editLesson/$1/$2');
        $routes->post('kelas/(:segment)/lesson/(:segment)/hapus', 'TrainerController::deleteLesson/$1/$2', ['as' => 'workspace.trainer.lesson.delete']);
        $routes->post('kelas/(:segment)/lesson/(:segment)/toggle-status', 'TrainerController::toggleLessonStatus/$1/$2', ['as' => 'workspace.trainer.lesson.toggle-status']);
        $routes->get('kelas/(:segment)/quiz', 'TrainerController::quizzes/$1', ['as' => 'workspace.trainer.quizzes']);
        $routes->get('kelas/(:segment)/quiz/tambah', 'TrainerController::createQuiz/$1', ['as' => 'workspace.trainer.quiz.create']);
        $routes->post('kelas/(:segment)/quiz/tambah', 'TrainerController::createQuiz/$1');
        $routes->get('kelas/(:segment)/quiz/(:segment)/soal', 'TrainerController::editQuiz/$1/$2', ['as' => 'workspace.trainer.quiz.edit']);
        $routes->post('kelas/(:segment)/quiz/(:segment)/soal', 'TrainerController::editQuiz/$1/$2');
        $routes->get('kelas/(:segment)/quiz/(:segment)/soal/tambah', 'TrainerController::addQuestion/$1/$2', ['as' => 'workspace.trainer.question.create']);
        $routes->post('kelas/(:segment)/quiz/(:segment)/soal/tambah', 'TrainerController::addQuestion/$1/$2');
        $routes->post('kelas/(:segment)/quiz/(:segment)/soal/(:segment)/hapus', 'TrainerController::deleteQuestion/$1/$2/$3', ['as' => 'workspace.trainer.question.delete']);
        $routes->get('kelas/(:segment)/tugas', 'TrainerController::assignments/$1', ['as' => 'workspace.trainer.assignments']);
        $routes->get('kelas/(:segment)/tugas/tambah', 'TrainerController::createAssignment/$1', ['as' => 'workspace.trainer.assignment.create']);
        $routes->post('kelas/(:segment)/tugas/tambah', 'TrainerController::createAssignment/$1');
        $routes->get('kelas/(:segment)/tugas/(:segment)/ubah', 'TrainerController::editAssignment/$1/$2', ['as' => 'workspace.trainer.assignment.edit']);
        $routes->post('kelas/(:segment)/tugas/(:segment)/ubah', 'TrainerController::editAssignment/$1/$2');
        $routes->post('kelas/(:segment)/tugas/(:segment)/hapus', 'TrainerController::deleteAssignment/$1/$2', ['as' => 'workspace.trainer.assignment.delete']);
        $routes->get('submissions', 'TrainerController::allSubmissions', ['as' => 'workspace.trainer.all-submissions']);
        $routes->get('kelas/(:segment)/tugas/(:segment)/submissions', 'TrainerController::assignmentSubmissions/$1/$2', ['as' => 'workspace.trainer.assignment.submissions']);
        $routes->get('submission/(:segment)/review', 'TrainerController::reviewSubmission/$1', ['as' => 'workspace.trainer.submission.review']);
        $routes->post('submission/(:segment)/review', 'TrainerController::submitReviewSubmission/$1', ['as' => 'workspace.trainer.submission.review.submit']);
        $routes->get('kelas/(:segment)/progres', 'TrainerController::progress/$1', ['as' => 'workspace.trainer.progress']);
    });

    $routes->group('operator', static function ($routes) {
        $routes->get('/', 'OperatorController::index', ['as' => 'workspace.operator.home']);
        $routes->get('approval', 'OperatorController::approvals', ['as' => 'workspace.operator.approvals']);
        $routes->post('approval/(:segment)/setujui', 'OperatorController::approveApproval/$1', ['as' => 'workspace.operator.approvals.approve']);
        $routes->post('approval/(:segment)/tolak', 'OperatorController::rejectApproval/$1', ['as' => 'workspace.operator.approvals.reject']);
        $routes->get('verifikasi-profil', 'OperatorController::profileVerifications', ['as' => 'workspace.operator.profile-verifications']);
        $routes->post('verifikasi-profil/(:segment)/setujui', 'OperatorController::approveVerification/$1', ['as' => 'workspace.operator.profile-verifications.approve']);
        $routes->post('verifikasi-profil/(:segment)/tolak', 'OperatorController::rejectVerification/$1', ['as' => 'workspace.operator.profile-verifications.reject']);
        $routes->get('penugasan-trainer', 'OperatorController::classTrainers', ['as' => 'workspace.operator.class-trainers']);
        $routes->post('penugasan-trainer/assign', 'OperatorController::assignTrainer', ['as' => 'workspace.operator.class-trainers.assign']);
        $routes->post('penugasan-trainer/revoke', 'OperatorController::revokeTrainer', ['as' => 'workspace.operator.class-trainers.revoke']);
        $routes->get('event', 'OperatorController::events', ['as' => 'workspace.operator.events']);
        $routes->get('event/(:segment)/peserta', 'OperatorController::eventParticipants/$1', ['as' => 'workspace.operator.events.participants']);
        $routes->get('kehadiran', 'OperatorController::attendance', ['as' => 'workspace.operator.attendance']);
        $routes->post('kehadiran/(:segment)/update', 'OperatorController::updateAttendance/$1', ['as' => 'workspace.operator.attendance.update']);
        $routes->get('monitoring-pembelajaran', 'OperatorController::learningMonitoring', ['as' => 'workspace.operator.learning-monitoring']);
        $routes->get('verifikasi-sertifikat', 'OperatorController::certificateVerification', ['as' => 'workspace.operator.certificates']);
        $routes->post('verifikasi-sertifikat/(:segment)/terbitkan', 'OperatorController::issueCertificate/$1', ['as' => 'workspace.operator.certificates.issue']);
        $routes->post('verifikasi-sertifikat/(:segment)/batalkan', 'OperatorController::revokeCertificate/$1', ['as' => 'workspace.operator.certificates.revoke']);
        $routes->get('enrollment', 'OperatorController::enrollments', ['as' => 'workspace.operator.enrollments']);
        $routes->post('enrollment/(:segment)/aktifkan', 'OperatorController::activateEnrollment/$1', ['as' => 'workspace.operator.enrollments.activate']);
        $routes->post('enrollment/(:segment)/override-nilai', 'OperatorController::overrideEnrollmentScore/$1', ['as' => 'workspace.operator.enrollments.override-score']);
        $routes->get('evaluasi', 'OperatorController::evaluations', ['as' => 'workspace.operator.evaluations']);
        $routes->get('evaluasi/submission/(:segment)', 'OperatorController::reviewSubmission/$1', ['as' => 'workspace.operator.submission.review']);
        $routes->post('evaluasi/submission/(:segment)/override', 'OperatorController::overrideSubmission/$1', ['as' => 'workspace.operator.submission.override']);
        $routes->get('monitoring-aktivitas', 'OperatorController::activityMonitoring', ['as' => 'workspace.operator.activity-monitoring']);
    });
});

// /profil/* routes were migrated to /workspace/profil/* (route-contract.md §6.3).
// See Workspace group below.

$routes->group('dashboard', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('masuk', '\App\Controllers\Dashboard\Auth\LoginController::index', ['as' => 'dashboard.auth.login']);
    $routes->post('masuk', '\App\Controllers\Dashboard\Auth\LoginController::attemptLogin', ['as' => 'dashboard.auth.login.attempt']);
    // Password reset (unified to Dashboard\Auth\PasswordController)
    $routes->get('lupa-kata-sandi', '\App\Controllers\Dashboard\Auth\PasswordController::forgotForm', ['as' => 'dashboard.auth.password.forgot']);
    $routes->post('lupa-kata-sandi', '\App\Controllers\Dashboard\Auth\PasswordController::sendReset', ['as' => 'dashboard.auth.password.send-reset']);
    $routes->get('reset-kata-sandi/(:segment)', '\App\Controllers\Dashboard\Auth\PasswordController::resetForm/$1', ['as' => 'dashboard.auth.password.reset']);
    $routes->post('reset-kata-sandi/(:segment)', '\App\Controllers\Dashboard\Auth\PasswordController::updatePassword/$1', ['as' => 'dashboard.auth.password.update']);

    // Account status (unified to Dashboard\Auth\AccountStatusController)
    $routes->get('status-akun', '\App\Controllers\Dashboard\Auth\AccountStatusController::show', ['as' => 'dashboard.auth.account-status']);

    // NOTE: dashboard/verifikasi/{token} (email verification) is intentionally
    // removed. CLAUDE.md §9 forbids email-token gate; account activation goes
    // through admin/operator profile_verifications approval.
    $routes->get('keluar', '\App\Controllers\Dashboard\Auth\LoginController::logoutPreview', ['as' => 'dashboard.auth.logout.preview']);
    $routes->post('keluar', '\App\Controllers\Dashboard\Auth\LoginController::logout', ['as' => 'dashboard.auth.logout']);

    $routes->group('daftar', static function ($routes) {
        // Langkah 1: Akun dasar + tipe profil
        $routes->get('/', '\App\Controllers\Dashboard\Auth\RegisterController::index', ['as' => 'dashboard.auth.register',]);
        $routes->post('/', '\App\Controllers\Dashboard\Auth\RegisterController::attempt', ['as' => 'dashboard.auth.register.attempt',]);

        // Langkah 2: Wilayah dan alamat ringkas
        $routes->get('wilayah', '\App\Controllers\Dashboard\Auth\RegisterController::registerAddress', ['as' => 'dashboard.auth.register.address',]);
        $routes->post('wilayah', '\App\Controllers\Dashboard\Auth\RegisterController::registerAddressAttempt', ['as' => 'dashboard.auth.register.address.attempt',]);
        $routes->get('wilayah/options', '\App\Controllers\Dashboard\Auth\RegisterController::wilayahOptions', ['as' => 'dashboard.auth.register.address.options.root',]);
        $routes->get('wilayah/options/(:segment)', '\App\Controllers\Dashboard\Auth\RegisterController::wilayahOptions/$1', ['as' => 'dashboard.auth.register.address.options',]);

        // Langkah 3: Profil khusus berdasarkan tipe profil
        $routes->get('profil/asn', '\App\Controllers\Dashboard\Auth\RegisterController::registerAsn', ['as' => 'dashboard.auth.register.profile.asn',]);

        $routes->get('profil/penyedia', '\App\Controllers\Dashboard\Auth\RegisterController::registerProvider', ['as' => 'dashboard.auth.register.profile.provider',]);

        $routes->get('profil/akademisi', '\App\Controllers\Dashboard\Auth\RegisterController::registerAcademic', ['as' => 'dashboard.auth.register.profile.academic',]);

        $routes->get('profil/masyarakat', '\App\Controllers\Dashboard\Auth\RegisterController::registerPublic', ['as' => 'dashboard.auth.register.profile.public',]);
        $routes->post('profil/(:segment)', '\App\Controllers\Dashboard\Auth\RegisterController::registerProfileAttempt/$1', ['as' => 'dashboard.auth.register.profile.attempt',]);

        // Langkah 4: Tinjau data dan persetujuan
        $routes->get('tinjau', '\App\Controllers\Dashboard\Auth\RegisterController::registerReview', ['as' => 'dashboard.auth.register.review',]);
        $routes->post('tinjau', '\App\Controllers\Dashboard\Auth\RegisterController::registerReviewAttempt', ['as' => 'dashboard.auth.register.review.attempt',]);

        // Hasil submit UI-only
        $routes->get('berhasil', '\App\Controllers\Dashboard\Auth\RegisterController::registerSuccess', ['as' => 'dashboard.auth.register.success',]);
    });

    $routes->get('/', 'DashboardController::index', ['as' => 'dashboard.home']);
    $routes->get('kpi-internal', 'DashboardController::internalKpi', ['as' => 'dashboard.summary.kpi']);
    $routes->get('antrean-persetujuan', 'DashboardController::approvalQueue', ['as' => 'dashboard.summary.approval-queue']);
    $routes->get('aktivitas-terbaru', 'DashboardController::recentActivity', ['as' => 'dashboard.summary.recent-activity']);
    $routes->get('indikator-sistem', 'DashboardController::systemIndicators', ['as' => 'dashboard.summary.system-indicators']);

    $routes->group('profile', static function ($routes) {
        $routes->get('/', 'ProfileController::index', ['as' => 'dashboard.profile.index']);
        $routes->get('edit', 'ProfileController::edit', ['as' => 'dashboard.profile.edit']);
        $routes->get('password', 'ProfileController::password', ['as' => 'dashboard.profile.password']);
        $routes->get('preferences', 'ProfileController::preferences', ['as' => 'dashboard.profile.preferences']);
    });

    $routes->group('akses', static function ($routes) {
        $routes->get('pengguna', 'UserController::index', ['as' => 'dashboard.access.users']);
        $routes->get('pengguna/tambah', 'UserController::create', ['as' => 'dashboard.access.users.create']);
        $routes->post('pengguna/tambah', 'UserController::store', ['as' => 'dashboard.access.users.store']);
        $routes->post('pengguna', 'UserController::store', ['as' => 'dashboard.access.users.store']);
        $routes->get('pengguna/(:segment)/ubah', 'UserController::edit/$1', ['as' => 'dashboard.access.users.edit']);
        $routes->post('pengguna/(:segment)/ubah', 'UserController::update/$1', ['as' => 'dashboard.access.users.update']);
        $routes->post('pengguna/(:segment)/aktifkan', 'UserController::activate/$1', ['as' => 'dashboard.access.users.activate']);
        $routes->post('pengguna/(:segment)/suspend', 'UserController::suspend/$1', ['as' => 'dashboard.access.users.suspend']);
        $routes->post('pengguna/(:segment)/reset-password', 'UserController::resetPassword/$1', ['as' => 'dashboard.access.users.reset-password']);
        $routes->get('pengguna/(:segment)', 'UserController::detail/$1', ['as' => 'dashboard.access.users.detail']);
        $routes->get('verifikasi-profil', 'UserProfileVerificationController::index', ['as' => 'dashboard.access.profile-verifications']);
        $routes->get('verifikasi-profil/(:segment)', 'UserProfileVerificationController::detail/$1', ['as' => 'dashboard.access.profile-verifications.detail']);
        $routes->post('verifikasi-profil/(:segment)/setujui', 'UserProfileVerificationController::approve/$1', ['as' => 'dashboard.access.profile-verifications.approve']);
        $routes->post('verifikasi-profil/(:segment)/tolak', 'UserProfileVerificationController::reject/$1', ['as' => 'dashboard.access.profile-verifications.reject']);
        $routes->post('verifikasi-profil/(:segment)/revisi', 'UserProfileVerificationController::requestRevision/$1', ['as' => 'dashboard.access.profile-verifications.revision']);
        $routes->get('permohonan-perubahan-profil', 'UserProfileVerificationController::changeRequests', ['as' => 'dashboard.access.profile-change-requests']);
        $routes->get('permohonan-perubahan-profil/(:segment)', 'UserProfileVerificationController::changeRequestDetail/$1', ['as' => 'dashboard.access.profile-change-requests.detail']);
        $routes->post('permohonan-perubahan-profil/(:segment)/setujui', 'UserProfileVerificationController::changeApprove/$1', ['as' => 'dashboard.access.profile-change-requests.approve']);
        $routes->post('permohonan-perubahan-profil/(:segment)/tolak', 'UserProfileVerificationController::changeReject/$1', ['as' => 'dashboard.access.profile-change-requests.reject']);
        $routes->post('permohonan-perubahan-profil/(:segment)/revisi', 'UserProfileVerificationController::changeRequestRevision/$1', ['as' => 'dashboard.access.profile-change-requests.revision']);
        $routes->get('role', 'RoleController::index', ['as' => 'dashboard.access.roles']);
        $routes->get('role/tambah', 'RoleController::create', ['as' => 'dashboard.access.roles.create']);
        $routes->post('role/tambah', 'RoleController::store', ['as' => 'dashboard.access.roles.store']);
        $routes->get('role/(:segment)/ubah', 'RoleController::edit/$1', ['as' => 'dashboard.access.roles.edit']);
        $routes->post('role/(:segment)/ubah', 'RoleController::update/$1', ['as' => 'dashboard.access.roles.update']);
        $routes->post('role/(:segment)/hapus', 'RoleController::delete/$1', ['as' => 'dashboard.access.roles.delete']);
        $routes->get('role/matrix', 'RoleController::matrix', ['as' => 'dashboard.access.roles.matrix']);
        $routes->post('role/matrix', 'RoleController::syncMatrix', ['as' => 'dashboard.access.roles.matrix.sync']);
        $routes->get('role/(:segment)', 'RoleController::detail/$1', ['as' => 'dashboard.access.roles.detail']);
        $routes->get('permission', 'PermissionController::index', ['as' => 'dashboard.access.permissions']);
        $routes->get('permission/tambah', 'PermissionController::create', ['as' => 'dashboard.access.permissions.create']);
        $routes->post('permission/tambah', 'PermissionController::store', ['as' => 'dashboard.access.permissions.store']);
        $routes->post('permission/(:segment)/ubah', 'PermissionController::update/$1', ['as' => 'dashboard.access.permissions.update']);
        $routes->post('permission/(:segment)/hapus', 'PermissionController::delete/$1', ['as' => 'dashboard.access.permissions.delete']);
        $routes->get('permission/(:segment)', 'PermissionController::detail/$1', ['as' => 'dashboard.access.permissions.detail']);
        $routes->get('permohonan-role', 'RoleRequestReviewController::index', ['as' => 'dashboard.access.role-requests']);
        $routes->get('permohonan-role/(:segment)', 'RoleRequestReviewController::detail/$1', ['as' => 'dashboard.access.role-requests.detail']);
        $routes->post('permohonan-role/(:segment)/setujui', 'RoleRequestReviewController::approve/$1', ['as' => 'dashboard.access.role-requests.approve']);
        $routes->post('permohonan-role/(:segment)/tolak', 'RoleRequestReviewController::reject/$1', ['as' => 'dashboard.access.role-requests.reject']);
        $routes->post('permohonan-role/(:segment)/revisi', 'RoleRequestReviewController::requestRevision/$1', ['as' => 'dashboard.access.role-requests.revision']);
        $routes->get('unit-organisasi', 'OrganizationController::index', ['as' => 'dashboard.access.organization-units']);
        $routes->get('unit-organisasi/tambah', 'OrganizationController::create', ['as' => 'dashboard.access.organization-units.create']);
        $routes->post('unit-organisasi/tambah', 'OrganizationController::store', ['as' => 'dashboard.access.organization-units.store']);
        $routes->get('unit-organisasi/(:segment)/ubah', 'OrganizationController::edit/$1', ['as' => 'dashboard.access.organization-units.edit']);
        $routes->post('unit-organisasi/(:segment)/ubah', 'OrganizationController::update/$1', ['as' => 'dashboard.access.organization-units.update']);
        $routes->post('unit-organisasi/(:segment)/nonaktifkan', 'OrganizationController::deactivate/$1', ['as' => 'dashboard.access.organization-units.deactivate']);
        $routes->post('unit-organisasi/(:segment)/aktifkan', 'OrganizationController::activate/$1', ['as' => 'dashboard.access.organization-units.activate']);
        $routes->get('unit-organisasi/(:segment)', 'OrganizationController::detail/$1', ['as' => 'dashboard.access.organization-units.detail']);
        $routes->get('peran-pengadaan', 'ProcurementRoleController::index', ['as' => 'dashboard.access.procurement-roles']);
        $routes->get('peran-pengadaan/tambah', 'ProcurementRoleController::create', ['as' => 'dashboard.access.procurement-roles.create']);
        $routes->post('peran-pengadaan/tambah', 'ProcurementRoleController::store', ['as' => 'dashboard.access.procurement-roles.store']);
        $routes->post('peran-pengadaan/(:segment)/ubah', 'ProcurementRoleController::update/$1', ['as' => 'dashboard.access.procurement-roles.update']);
        $routes->get('penugasan-pengadaan', 'ProcurementRoleController::assignments', ['as' => 'dashboard.access.procurement-assignments']);
        $routes->post('penugasan-pengadaan', 'ProcurementRoleController::storeAssignment', ['as' => 'dashboard.access.procurement-assignments.store']);
        $routes->post('penugasan-pengadaan/(:segment)/verifikasi', 'ProcurementRoleController::verifyAssignment/$1', ['as' => 'dashboard.access.procurement-assignments.verify']);
        $routes->get('peran-pengadaan/(:segment)', 'ProcurementRoleController::detail/$1', ['as' => 'dashboard.access.procurement-roles.detail']);

        // Audit akses (per spec: /dashboard/akses/audit)
        $routes->group('audit', static function ($routes) {
            $routes->get('/', 'AuditController::index', ['as' => 'dashboard.access.audit']);
            $routes->get('(:segment)', 'AuditController::detail/$1', ['as' => 'dashboard.access.audit.detail']);
        });
    });

    $routes->group('pembelajaran', ['namespace' => 'App\Controllers\Learning'], static function ($routes) {
        // 1. Overview
        $routes->get('', 'LearningOverviewController::index', ['as' => 'dashboard.learning.overview']);

        // 2. Program
        $routes->get('program', 'ProgramController::index', ['as' => 'dashboard.learning.programs']);
        $routes->post('program', 'ProgramController::store', ['as' => 'dashboard.learning.programs.store']);
        $routes->get('program/tambah', 'ProgramController::create', ['as' => 'dashboard.learning.programs.create']);
        $routes->get('program/(:segment)/ubah', 'ProgramController::edit/$1', ['as' => 'dashboard.learning.programs.edit']);
        $routes->post('program/(:segment)/ubah', 'ProgramController::update/$1', ['as' => 'dashboard.learning.programs.update']);
        $routes->post('program/(:segment)/hapus', 'ProgramController::delete/$1', ['as' => 'dashboard.learning.programs.delete']);
        $routes->get('program/(:segment)', 'ProgramController::detail/$1', ['as' => 'dashboard.learning.programs.detail']);

        // 3. Course
        $routes->get('course', 'CourseController::index', ['as' => 'dashboard.learning.courses']);
        $routes->post('course', 'CourseController::store', ['as' => 'dashboard.learning.courses.store']);
        $routes->get('course/tambah', 'CourseController::create', ['as' => 'dashboard.learning.courses.create']);
        $routes->get('course/(:segment)/ubah', 'CourseController::edit/$1', ['as' => 'dashboard.learning.courses.edit']);
        $routes->post('course/(:segment)/ubah', 'CourseController::update/$1', ['as' => 'dashboard.learning.courses.update']);
        $routes->post('course/(:segment)/hapus', 'CourseController::delete/$1', ['as' => 'dashboard.learning.courses.delete']);
        $routes->get('course/(:segment)', 'CourseController::detail/$1', ['as' => 'dashboard.learning.courses.detail']);

        // 4. Class / Batch
        $routes->get('kelas', 'LearningClassController::index', ['as' => 'dashboard.learning.classes']);
        $routes->post('kelas', 'LearningClassController::store', ['as' => 'dashboard.learning.classes.store']);
        $routes->get('kelas/tambah', 'LearningClassController::create', ['as' => 'dashboard.learning.classes.create']);
        $routes->get('kelas/(:segment)/ubah', 'LearningClassController::edit/$1', ['as' => 'dashboard.learning.classes.edit']);
        $routes->post('kelas/(:segment)/ubah', 'LearningClassController::update/$1', ['as' => 'dashboard.learning.classes.update']);
        $routes->post('kelas/(:segment)/hapus', 'LearningClassController::delete/$1', ['as' => 'dashboard.learning.classes.delete']);
        $routes->get('kelas/(:segment)', 'LearningClassController::detail/$1', ['as' => 'dashboard.learning.classes.detail']);
        $routes->post('kelas/(:segment)/trainer', 'LearningClassController::assignTrainer/$1', ['as' => 'dashboard.learning.classes.assign-trainer']);
        $routes->post('kelas/(:segment)/trainer/(:segment)/cabut', 'LearningClassController::revokeTrainer/$1/$2', ['as' => 'dashboard.learning.classes.revoke-trainer']);

        // 5. Trainer Assignment
        $routes->get('trainer', 'TrainerAssignmentController::index', ['as' => 'dashboard.learning.trainers']);
        $routes->post('trainer/assign', 'TrainerAssignmentController::assign', ['as' => 'dashboard.learning.trainers.assign']);
        $routes->post('trainer/(:segment)/(:segment)/cabut', 'TrainerAssignmentController::revoke/$1/$2', ['as' => 'dashboard.learning.trainers.revoke']);

        // 6. Peserta & Enrollment
        $routes->get('enrollment', 'EnrollmentController::index', ['as' => 'dashboard.learning.enrollments']);
        $routes->get('enrollment/(:segment)', 'EnrollmentController::detail/$1', ['as' => 'dashboard.learning.enrollments.detail']);
        $routes->post('enrollment/tambah', 'EnrollmentController::store', ['as' => 'dashboard.learning.enrollments.store']);
        $routes->post('enrollment/(:segment)/approve', 'EnrollmentController::approve/$1', ['as' => 'dashboard.learning.enrollments.approve']);
        $routes->post('enrollment/(:segment)/reject', 'EnrollmentController::reject/$1', ['as' => 'dashboard.learning.enrollments.reject']);
        $routes->post('enrollment/(:segment)/status', 'EnrollmentController::status/$1', ['as' => 'dashboard.learning.enrollments.status']);
        $routes->post('enrollment/(:segment)/hapus', 'EnrollmentController::delete/$1', ['as' => 'dashboard.learning.enrollments.delete']);

        // 7. Curriculum Management
        $routes->get('kurikulum', 'CurriculumController::index', ['as' => 'dashboard.learning.curriculum']);
        $routes->get('kurikulum/kelas/(:segment)', 'CurriculumController::index/$1', ['as' => 'dashboard.learning.curriculum.class']);
        $routes->post('kurikulum/modul', 'CurriculumController::storeModule', ['as' => 'dashboard.learning.curriculum.module.store']);
        $routes->post('kurikulum/modul/(:segment)/ubah', 'CurriculumController::updateModule/$1', ['as' => 'dashboard.learning.curriculum.module.update']);
        $routes->post('kurikulum/modul/(:segment)/hapus', 'CurriculumController::deleteModule/$1', ['as' => 'dashboard.learning.curriculum.module.delete']);
        $routes->post('kurikulum/lesson', 'CurriculumController::storeLesson', ['as' => 'dashboard.learning.curriculum.lesson.store']);
        $routes->post('kurikulum/lesson/(:segment)/ubah', 'CurriculumController::updateLesson/$1', ['as' => 'dashboard.learning.curriculum.lesson.update']);
        $routes->post('kurikulum/lesson/(:segment)/hapus', 'CurriculumController::deleteLesson/$1', ['as' => 'dashboard.learning.curriculum.lesson.delete']);

        // Legacy module and lesson routes
        $routes->get('materi-modul', 'ModuleController::index', ['as' => 'dashboard.learning.modules']);
        $routes->post('materi-modul', 'ModuleController::store', ['as' => 'dashboard.learning.modules.store']);
        $routes->get('materi-modul/tambah', 'ModuleController::create', ['as' => 'dashboard.learning.modules.create']);
        $routes->get('materi-modul/(:segment)/ubah', 'ModuleController::edit/$1', ['as' => 'dashboard.learning.modules.edit']);
        $routes->post('materi-modul/(:segment)/ubah', 'ModuleController::update/$1', ['as' => 'dashboard.learning.modules.update']);
        $routes->get('materi-modul/(:segment)', 'ModuleController::detail/$1', ['as' => 'dashboard.learning.modules.detail']);
        $routes->get('lesson', 'LessonController::index', ['as' => 'dashboard.learning.lessons']);
        $routes->post('lesson', 'LessonController::store', ['as' => 'dashboard.learning.lessons.store']);
        $routes->get('lesson/tambah', 'LessonController::create', ['as' => 'dashboard.learning.lessons.create']);
        $routes->get('lesson/(:segment)/ubah', 'LessonController::edit/$1', ['as' => 'dashboard.learning.lessons.edit']);
        $routes->post('lesson/(:segment)/ubah', 'LessonController::update/$1', ['as' => 'dashboard.learning.lessons.update']);
        $routes->get('lesson/(:segment)', 'LessonController::detail/$1', ['as' => 'dashboard.learning.lessons.detail']);

        // 8. Assessment
        $routes->get('assessment', 'AssessmentManagementController::index', ['as' => 'dashboard.learning.assessments']);
        $routes->post('assessment/quiz', 'AssessmentManagementController::storeQuiz', ['as' => 'dashboard.learning.assessments.quiz.store']);
        $routes->post('assessment/assignment', 'AssessmentManagementController::storeAssignment', ['as' => 'dashboard.learning.assessments.assignment.store']);
        $routes->get('assessment/bank-soal', 'AssessmentManagementController::questionBank', ['as' => 'dashboard.learning.question-bank']);
        $routes->get('assessment/pre-test', 'PreTestController::index', ['as' => 'dashboard.learning.pre-tests']);
        $routes->get('assessment/quiz', 'QuizController::index', ['as' => 'dashboard.learning.quizzes']);
        $routes->get('assessment/post-test', 'PostTestController::index', ['as' => 'dashboard.learning.post-tests']);
        $routes->get('penilaian', 'GradingController::index', ['as' => 'dashboard.learning.gradings']);
        $routes->get('penilaian/(:segment)', 'GradingController::detail/$1', ['as' => 'dashboard.learning.gradings.detail']);

        // 9. Learning Progress
        $routes->get('progres', 'ProgressMonitoringController::index', ['as' => 'dashboard.learning.progress']);
        $routes->get('progres/detail/(:segment)', 'ProgressMonitoringController::detail/$1', ['as' => 'dashboard.learning.progress.detail']);
        $routes->get('progres-kelulusan', 'ProgressMonitoringController::index', ['as' => 'dashboard.learning.progress-completion']);
        $routes->get('feedback-peserta', 'StudyController::feedback', ['as' => 'dashboard.learning.participant-feedback']);

        // 10. Certificate Eligibility
        $routes->get('kelayakan-sertifikat', 'CertificateEligibilityController::index', ['as' => 'dashboard.learning.certificate-eligibility']);
        $routes->post('kelayakan-sertifikat/generate/(:segment)', 'CertificateEligibilityController::generate/$1', ['as' => 'dashboard.learning.certificate-eligibility.generate']);
        $routes->post('kelayakan-sertifikat/batch-generate', 'CertificateEligibilityController::batchGenerate', ['as' => 'dashboard.learning.certificate-eligibility.batch-generate']);

        // 11. Reports
        $routes->get('laporan', 'LearningReportController::index', ['as' => 'dashboard.learning.reports']);
    });

    $routes->group('bunga-rampai', ['namespace' => 'App\Controllers\Knowledge'], static function ($routes) {
        $routes->get('artikel', 'ArticleController::index', ['as' => 'dashboard.knowledge.articles']);
        $routes->get('artikel/tambah', 'ArticleController::create', ['as' => 'dashboard.knowledge.articles.create']);
        $routes->post('artikel/tambah', 'ArticleController::store');
        $routes->get('artikel/(:segment)/ubah', 'ArticleController::edit/$1', ['as' => 'dashboard.knowledge.articles.edit']);
        $routes->post('artikel/(:segment)/ubah', 'ArticleController::update/$1');
        $routes->get('artikel/(:segment)', 'ArticleController::detail/$1', ['as' => 'dashboard.knowledge.articles.detail']);
        $routes->post('artikel/(:segment)/hapus', 'ArticleController::delete/$1', ['as' => 'dashboard.knowledge.articles.delete']);

        $routes->get('faq', 'FaqController::index', ['as' => 'dashboard.knowledge.faqs']);
        $routes->get('faq/tambah', 'FaqController::create', ['as' => 'dashboard.knowledge.faqs.create']);
        $routes->post('faq/tambah', 'FaqController::store');
        $routes->get('faq/(:segment)/ubah', 'FaqController::edit/$1', ['as' => 'dashboard.knowledge.faqs.edit']);
        $routes->post('faq/(:segment)/ubah', 'FaqController::update/$1');
        $routes->get('faq/(:segment)', 'FaqController::detail/$1', ['as' => 'dashboard.knowledge.faqs.detail']);
        $routes->post('faq/(:segment)/hapus', 'FaqController::delete/$1', ['as' => 'dashboard.knowledge.faqs.delete']);

        $routes->get('studi-kasus', 'CaseStudyController::index', ['as' => 'dashboard.knowledge.case-studies']);
        $routes->get('studi-kasus/tambah', 'CaseStudyController::create', ['as' => 'dashboard.knowledge.case-studies.create']);
        $routes->post('studi-kasus/tambah', 'CaseStudyController::store');
        $routes->get('studi-kasus/(:segment)/ubah', 'CaseStudyController::edit/$1', ['as' => 'dashboard.knowledge.case-studies.edit']);
        $routes->post('studi-kasus/(:segment)/ubah', 'CaseStudyController::update/$1');
        $routes->get('studi-kasus/(:segment)', 'CaseStudyController::detail/$1', ['as' => 'dashboard.knowledge.case-studies.detail']);
        $routes->post('studi-kasus/(:segment)/hapus', 'CaseStudyController::delete/$1', ['as' => 'dashboard.knowledge.case-studies.delete']);

        $routes->get('template-dokumen', 'DocumentTemplateController::index', ['as' => 'dashboard.knowledge.document-templates']);
        $routes->get('template-dokumen/tambah', 'DocumentTemplateController::create', ['as' => 'dashboard.knowledge.document-templates.create']);
        $routes->post('template-dokumen/tambah', 'DocumentTemplateController::store');
        $routes->get('template-dokumen/(:segment)/ubah', 'DocumentTemplateController::edit/$1', ['as' => 'dashboard.knowledge.document-templates.edit']);
        $routes->post('template-dokumen/(:segment)/ubah', 'DocumentTemplateController::update/$1');
        $routes->get('template-dokumen/(:segment)', 'DocumentTemplateController::detail/$1', ['as' => 'dashboard.knowledge.document-templates.detail']);
        $routes->post('template-dokumen/(:segment)/hapus', 'DocumentTemplateController::delete/$1', ['as' => 'dashboard.knowledge.document-templates.delete']);

        $routes->get('media-galeri', 'KnowledgeMediaController::index', ['as' => 'dashboard.knowledge.media-gallery']);
        $routes->get('media-galeri/tambah', 'KnowledgeMediaController::create', ['as' => 'dashboard.knowledge.media-gallery.create']);
        $routes->post('media-galeri/tambah', 'KnowledgeMediaController::store');
        $routes->get('media-galeri/(:segment)/ubah', 'KnowledgeMediaController::edit/$1', ['as' => 'dashboard.knowledge.media-gallery.edit']);
        $routes->post('media-galeri/(:segment)/ubah', 'KnowledgeMediaController::update/$1');
        $routes->get('media-galeri/(:segment)', 'KnowledgeMediaController::detail/$1', ['as' => 'dashboard.knowledge.media-gallery.detail']);
        $routes->post('media-galeri/(:segment)/hapus', 'KnowledgeMediaController::delete/$1', ['as' => 'dashboard.knowledge.media-gallery.delete']);

        $routes->get('item-media', 'KnowledgeMediaItemController::index', ['as' => 'dashboard.knowledge.media-items']);
        $routes->get('item-media/(:segment)', 'KnowledgeMediaItemController::detail/$1', ['as' => 'dashboard.knowledge.media-items.detail']);

        $routes->get('carousel-konten-unggulan', 'KnowledgeCarouselController::index', ['as' => 'dashboard.knowledge.carousel']);
        $routes->get('carousel-konten-unggulan/tambah', 'KnowledgeCarouselController::create', ['as' => 'dashboard.knowledge.carousel.create']);
        $routes->post('carousel-konten-unggulan/tambah', 'KnowledgeCarouselController::store');
        $routes->post('carousel-konten-unggulan/(:segment)/(:segment)/nonaktifkan', 'KnowledgeCarouselController::disable/$1/$2', ['as' => 'dashboard.knowledge.carousel.disable-item']);
        $routes->post('carousel-konten-unggulan/(:segment)/nonaktifkan', 'KnowledgeCarouselController::disable/$1', ['as' => 'dashboard.knowledge.carousel.disable']);

        $routes->get('kategori', 'CategoryController::index', ['as' => 'dashboard.knowledge.categories']);
        $routes->get('kategori/tambah', 'CategoryController::create', ['as' => 'dashboard.knowledge.categories.create']);
        $routes->post('kategori/tambah', 'CategoryController::store');
        $routes->get('kategori/(:segment)/ubah', 'CategoryController::edit/$1', ['as' => 'dashboard.knowledge.categories.edit']);
        $routes->post('kategori/(:segment)/ubah', 'CategoryController::update/$1');
        $routes->post('kategori/(:segment)/hapus', 'CategoryController::delete/$1', ['as' => 'dashboard.knowledge.categories.delete']);

        $routes->get('tag', 'TagController::index', ['as' => 'dashboard.knowledge.tags']);
        $routes->get('tag/tambah', 'TagController::create', ['as' => 'dashboard.knowledge.tags.create']);
        $routes->post('tag/tambah', 'TagController::store');
        $routes->get('tag/(:segment)/ubah', 'TagController::edit/$1', ['as' => 'dashboard.knowledge.tags.edit']);
        $routes->post('tag/(:segment)/ubah', 'TagController::update/$1');
        $routes->post('tag/(:segment)/hapus', 'TagController::delete/$1', ['as' => 'dashboard.knowledge.tags.delete']);
    });

    $routes->group('regulasi', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
        // CRUD Regulation
        $routes->get('dokumen', 'RegulationController::index', ['as' => 'dashboard.regulations.index']);
        $routes->get('dokumen/tambah', 'RegulationController::create', ['as' => 'dashboard.regulations.create']);
        $routes->post('dokumen', 'RegulationController::store', ['as' => 'dashboard.regulations.store']);
        $routes->get('dokumen/(:segment)/ubah', 'RegulationController::edit/$1', ['as' => 'dashboard.regulations.edit']);
        $routes->post('dokumen/(:segment)/ubah', 'RegulationController::update/$1', ['as' => 'dashboard.regulations.update']);
        $routes->get('dokumen/(:segment)', 'RegulationController::show/$1', ['as' => 'dashboard.regulations.detail']);
        $routes->post('dokumen/(:segment)/hapus', 'RegulationController::delete/$1', ['as' => 'dashboard.regulations.delete']);

        // Workflow actions
        $routes->post('dokumen/(:segment)/submit', 'RegulationController::submitReview/$1', ['as' => 'dashboard.regulations.submit']);
        $routes->post('dokumen/(:segment)/approve', 'RegulationController::approve/$1', ['as' => 'dashboard.regulations.approve']);
        $routes->post('dokumen/(:segment)/reject', 'RegulationController::reject/$1', ['as' => 'dashboard.regulations.reject']);
        $routes->post('dokumen/(:segment)/revision', 'RegulationController::requestRevision/$1', ['as' => 'dashboard.regulations.revision']);
        $routes->post('dokumen/(:segment)/publish', 'RegulationController::publish/$1', ['as' => 'dashboard.regulations.publish']);

        // Version upload + set-current
        $routes->post('dokumen/(:segment)/versi', 'RegulationController::addVersion/$1', ['as' => 'dashboard.regulations.version.add']);
        $routes->post('dokumen/(:segment)/versi/current', 'RegulationController::setCurrentVersion/$1', ['as' => 'dashboard.regulations.version.current']);

        // Competency attach
        $routes->post('dokumen/(:segment)/kompetensi', 'RegulationController::attachCompetency/$1', ['as' => 'dashboard.regulations.competency.attach']);

        // Categories
        $routes->get('kategori', 'RegulationController::categories', ['as' => 'dashboard.regulations.categories']);
        $routes->get('kategori/tambah', 'RegulationController::categoryCreate', ['as' => 'dashboard.regulations.categories.create']);
        $routes->post('kategori/tambah', 'RegulationController::categoryStore', ['as' => 'dashboard.regulations.categories.store']);
        $routes->get('kategori/(:segment)/ubah', 'RegulationController::categoryEdit/$1', ['as' => 'dashboard.regulations.categories.edit']);
        $routes->post('kategori/(:segment)/ubah', 'RegulationController::categoryUpdate/$1', ['as' => 'dashboard.regulations.categories.update']);
        $routes->post('kategori/(:segment)/hapus', 'RegulationController::categoryDelete/$1', ['as' => 'dashboard.regulations.categories.delete']);
        $routes->get('kategori/(:segment)', 'RegulationController::categoryShow/$1', ['as' => 'dashboard.regulations.categories.detail']);

        // Versions list/detail
        $routes->get('versi', 'RegulationController::versions', ['as' => 'dashboard.regulations.versions']);
        $routes->get('versi/(:segment)', 'RegulationController::versionShow/$1', ['as' => 'dashboard.regulations.versions.detail']);

        // Relations
        $routes->get('relasi', 'RegulationController::relations', ['as' => 'dashboard.regulations.relations']);
        $routes->get('relasi/tambah', 'RegulationController::relationCreate', ['as' => 'dashboard.regulations.relations.create']);
        $routes->post('relasi/tambah', 'RegulationController::relationStore', ['as' => 'dashboard.regulations.relations.store']);
        $routes->get('relasi/(:segment)', 'RegulationController::relationShow/$1', ['as' => 'dashboard.regulations.relations.detail']);
        $routes->post('relasi/(:segment)/hapus', 'RegulationController::relationDelete/$1', ['as' => 'dashboard.regulations.relations.delete']);

        // Procurement stages
        $routes->get('tahapan-pbj', 'RegulationController::procurementStages', ['as' => 'dashboard.regulations.procurement-stages']);
        $routes->get('tahapan-pbj/tambah', 'RegulationController::procurementStageCreate', ['as' => 'dashboard.regulations.procurement-stages.create']);
        $routes->post('tahapan-pbj/tambah', 'RegulationController::procurementStageStore', ['as' => 'dashboard.regulations.procurement-stages.store']);
        $routes->get('tahapan-pbj/(:segment)/ubah', 'RegulationController::procurementStageEdit/$1', ['as' => 'dashboard.regulations.procurement-stages.edit']);
        $routes->post('tahapan-pbj/(:segment)/ubah', 'RegulationController::procurementStageUpdate/$1', ['as' => 'dashboard.regulations.procurement-stages.update']);
        $routes->post('tahapan-pbj/(:segment)/hapus', 'RegulationController::procurementStageDelete/$1', ['as' => 'dashboard.regulations.procurement-stages.delete']);

        // Competencies
        $routes->get('kompetensi-terkait', 'RegulationController::competencies', ['as' => 'dashboard.regulations.competencies']);
        $routes->get('kompetensi/tambah', 'RegulationController::competencyCreate', ['as' => 'dashboard.regulations.competencies.create']);
        $routes->post('kompetensi/tambah', 'RegulationController::competencyStore', ['as' => 'dashboard.regulations.competencies.store']);
        $routes->get('kompetensi/(:segment)/ubah', 'RegulationController::competencyEdit/$1', ['as' => 'dashboard.regulations.competencies.edit']);
        $routes->post('kompetensi/(:segment)/ubah', 'RegulationController::competencyUpdate/$1', ['as' => 'dashboard.regulations.competencies.update']);
        $routes->post('kompetensi/(:segment)/hapus', 'RegulationController::competencyDelete/$1', ['as' => 'dashboard.regulations.competencies.delete']);

        // Download logs
        $routes->get('log-download', 'RegulationController::downloadLogs', ['as' => 'dashboard.regulations.download-logs']);
    });

    $routes->group('komunikasi', ['namespace' => 'App\Controllers\Communication'], static function ($routes) {
        $routes->get('berita', 'NewsController::index', ['as' => 'dashboard.communication.news']);
        $routes->get('berita/tambah', 'NewsController::create', ['as' => 'dashboard.communication.news.create']);
        $routes->post('berita/tambah', 'NewsController::store');
        $routes->get('berita/(:segment)/ubah', 'NewsController::edit/$1', ['as' => 'dashboard.communication.news.edit']);
        $routes->post('berita/(:segment)/ubah', 'NewsController::update/$1');
        $routes->get('berita/(:segment)', 'NewsController::detail/$1', ['as' => 'dashboard.communication.news.detail']);
        $routes->post('berita/(:segment)/hapus', 'NewsController::delete/$1', ['as' => 'dashboard.communication.news.delete']);

        $routes->get('kategori-berita', 'NewsController::categories', ['as' => 'dashboard.communication.news-categories']);
        $routes->get('kategori-berita/tambah', 'NewsController::categoryCreate', ['as' => 'dashboard.communication.news-categories.create']);
        $routes->post('kategori-berita/tambah', 'NewsController::categoryStore');
        $routes->get('kategori-berita/(:segment)/ubah', 'NewsController::categoryEdit/$1', ['as' => 'dashboard.communication.news-categories.edit']);
        $routes->post('kategori-berita/(:segment)/ubah', 'NewsController::categoryUpdate/$1');
        $routes->post('kategori-berita/(:segment)/hapus', 'NewsController::categoryDelete/$1');

        $routes->get('event-agenda', 'EventController::index', ['as' => 'dashboard.communication.events']);
        $routes->get('event-agenda/tambah', 'EventController::create', ['as' => 'dashboard.communication.events.create']);
        $routes->post('event-agenda/tambah', 'EventController::store');
        $routes->get('event-agenda/(:segment)/ubah', 'EventController::edit/$1', ['as' => 'dashboard.communication.events.edit']);
        $routes->post('event-agenda/(:segment)/ubah', 'EventController::update/$1');
        $routes->get('event-agenda/(:segment)', 'EventController::detail/$1', ['as' => 'dashboard.communication.events.detail']);
        $routes->post('event-agenda/(:segment)/generate-kode-presensi', 'EventController::generateAttendanceCode/$1', ['as' => 'dashboard.communication.events.generate-attendance-code']);
        $routes->post('event-agenda/(:segment)/buka-presensi', 'EventController::openAttendance/$1', ['as' => 'dashboard.communication.events.open-attendance']);
        $routes->post('event-agenda/(:segment)/tutup-presensi', 'EventController::closeAttendance/$1', ['as' => 'dashboard.communication.events.close-attendance']);
        $routes->post('event-agenda/(:segment)/batch-certificates', 'EventController::batchCertificates/$1', ['as' => 'dashboard.communication.events.batch-certificates']);
        $routes->post('event-agenda/(:segment)/hapus', 'EventController::delete/$1', ['as' => 'dashboard.communication.events.delete']);

        $routes->get('registrasi-event', 'EventRegistrationController::index', ['as' => 'dashboard.communication.event-registrations']);
        $routes->get('registrasi-event/(:segment)', 'EventRegistrationController::detail/$1', ['as' => 'dashboard.communication.event-registrations.detail']);
        $routes->post('registrasi-event/(:segment)/status', 'EventRegistrationController::updateStatus/$1');

        $routes->get('kehadiran', 'AttendanceController::index', ['as' => 'dashboard.communication.attendance']);
        $routes->post('kehadiran/(:segment)/catatan', 'AttendanceController::record/$1');

        $routes->get('waitlist-pembatalan', 'EventRegistrationController::waitlistAndCancellation', ['as' => 'dashboard.communication.waitlist-cancellations']);
    });

    $routes->group('sertifikat', ['namespace' => 'App\Controllers\Certificate'], static function ($routes) {
        // 1. Overview Sertifikat
        $routes->get('/', 'CertificateController::overview', ['as' => 'dashboard.certificates.overview']);

        // 2. Learning Certificate
        $routes->get('learning', 'CertificateLearningController::index', ['as' => 'dashboard.certificates.learning']);
        $routes->get('learning/kelas/(:segment)', 'CertificateLearningController::classDetail/$1', ['as' => 'dashboard.certificates.learning.class-detail']);
        $routes->post('learning/issue/(:segment)', 'CertificateLearningController::issueSingle/$1', ['as' => 'dashboard.certificates.learning.issue-single']);
        $routes->post('learning/kelas/(:segment)/batch', 'CertificateLearningController::issueClassBatch/$1', ['as' => 'dashboard.certificates.learning.issue-batch']);

        // 3. Event / Agenda Certificate
        $routes->get('event', 'CertificateEventController::index', ['as' => 'dashboard.certificates.events']);
        $routes->get('event/(:segment)', 'CertificateEventController::detail/$1', ['as' => 'dashboard.certificates.events.detail']);
        $routes->post('event/issue/(:segment)', 'CertificateEventController::issueSingle/$1', ['as' => 'dashboard.certificates.events.issue-single']);
        $routes->post('event/(:segment)/batch', 'CertificateEventController::issueBatch/$1', ['as' => 'dashboard.certificates.events.issue-batch']);
        // Event approval compatibility
        $routes->get('pengajuan-event', 'CertificateEventController::select', ['as' => 'dashboard.certificates.event-approval']);
        $routes->get('pengajuan-event/(:segment)/preview', 'CertificateEventController::preview/$1', ['as' => 'dashboard.certificates.event-approval.preview']);
        $routes->post('pengajuan-event/(:segment)/generate', 'CertificateEventController::generate/$1', ['as' => 'dashboard.certificates.event-approval.generate']);

        // 4. Batch Generation
        $routes->get('batch', 'CertificateBatchController::index', ['as' => 'dashboard.certificates.batch']);
        $routes->post('batch/learning', 'CertificateBatchController::processLearningBatch', ['as' => 'dashboard.certificates.batch.learning']);
        $routes->post('batch/event', 'CertificateBatchController::processEventBatch', ['as' => 'dashboard.certificates.batch.event']);

        // 5. Template Sertifikat
        $routes->get('template', 'CertificateTemplateController::index', ['as' => 'dashboard.certificates.templates']);
        $routes->get('template/tambah', 'CertificateTemplateController::create', ['as' => 'dashboard.certificates.templates.create']);
        $routes->post('template/simpan', 'CertificateTemplateController::store', ['as' => 'dashboard.certificates.templates.store']);
        $routes->get('template/(:segment)/ubah', 'CertificateTemplateController::edit/$1', ['as' => 'dashboard.certificates.templates.edit']);
        $routes->post('template/(:segment)/ubah', 'CertificateTemplateController::update/$1', ['as' => 'dashboard.certificates.templates.update']);
        $routes->post('template/(:segment)/aktifkan', 'CertificateTemplateController::activate/$1', ['as' => 'dashboard.certificates.templates.activate']);
        $routes->post('template/(:segment)/nonaktifkan', 'CertificateTemplateController::deactivate/$1', ['as' => 'dashboard.certificates.templates.deactivate']);
        $routes->get('template/(:segment)', 'CertificateTemplateController::detail/$1', ['as' => 'dashboard.certificates.templates.detail']);

        // 6. Verification (Internal Dashboard)
        $routes->get('verifikasi', 'CertificateController::verification', ['as' => 'dashboard.certificates.verification']);
        $routes->post('verifikasi/cari', 'CertificateController::checkVerification', ['as' => 'dashboard.certificates.verification.check']);

        // 7. Riwayat Sertifikat (Archive)
        $routes->get('riwayat', 'CertificateController::riwayat', ['as' => 'dashboard.certificates.riwayat']);
        $routes->get('index', 'CertificateController::riwayat', ['as' => 'dashboard.certificates.index']);

        // Legacy / Approval Queue compatibility
        $routes->get('approval', 'CertificateApprovalController::index', ['as' => 'dashboard.certificates.approvals']);
        $routes->get('approval/(:segment)', 'CertificateApprovalController::detail/$1', ['as' => 'dashboard.certificates.approvals.detail']);
        $routes->post('approval/(:segment)/terbitkan', 'CertificateApprovalController::issue/$1', ['as' => 'dashboard.certificates.approvals.issue']);
        $routes->post('approval/(:segment)/tolak', 'CertificateApprovalController::reject/$1', ['as' => 'dashboard.certificates.approvals.reject']);

        // Certificate Operations (detail, edit, revoke, download)
        $routes->get('(:segment)/ubah', 'CertificateController::edit/$1', ['as' => 'dashboard.certificates.edit']);
        $routes->post('(:segment)/ubah', 'CertificateController::update/$1');
        $routes->post('(:segment)/cabut', 'CertificateController::revoke/$1', ['as' => 'dashboard.certificates.revoke']);
        $routes->get('(:segment)/unduh', 'CertificateDownloadController::download/$1', ['as' => 'dashboard.certificates.download']);
        $routes->get('(:segment)', 'CertificateController::detail/$1', ['as' => 'dashboard.certificates.detail']);
    });

    $routes->group('intelligence', ['namespace' => 'App\Controllers\Intelligence'], static function ($routes) {
        $routes->get('/', 'IntelligenceDashboardController::index', ['as' => 'dashboard.intelligence.home']);
        $routes->get('opd-unit', 'OpdController::index', ['as' => 'dashboard.intelligence.organization-units']);
        $routes->get('opd-unit/tambah', 'OpdController::create', ['as' => 'dashboard.intelligence.organization-units.create']);
        $routes->get('opd-unit/(:segment)', 'OpdController::detail/$1', ['as' => 'dashboard.intelligence.organization-units.detail']);
        $routes->get('sdm-jf-ppbj', 'JfPpbjController::index', ['as' => 'dashboard.intelligence.personnel']);
        $routes->get('sdm-jf-ppbj/(:segment)', 'JfPpbjController::detail/$1', ['as' => 'dashboard.intelligence.personnel.detail']);
        $routes->get('riwayat-jf-ppbj', 'JfPpbjController::histories', ['as' => 'dashboard.intelligence.personnel-histories']);
        $routes->get('jenjang-jf-ppbj', 'JfPpbjController::levels', ['as' => 'dashboard.intelligence.personnel-levels']);
        $routes->get('sertifikat-kompetensi', 'CompetencyCertificateController::index', ['as' => 'dashboard.intelligence.competency-certificates']);
        $routes->get('sertifikat-kompetensi/(:segment)', 'CompetencyCertificateController::detail/$1', ['as' => 'dashboard.intelligence.competency-certificates.detail']);
        $routes->get('jenis-sertifikat-kompetensi', 'CompetencyCertificateController::types', ['as' => 'dashboard.intelligence.competency-certificate-types']);
        $routes->get('gap-kompetensi', 'IntelligenceDashboardController::competencyGap', ['as' => 'dashboard.intelligence.competency-gap']);
    });

    $routes->group('laporan', static function ($routes) {
        $routes->get('pembelajaran', 'DashboardController::learningReport', ['as' => 'dashboard.reports.learning']);
        $routes->get('event', 'DashboardController::eventReport', ['as' => 'dashboard.reports.events']);
        $routes->get('sertifikat', 'DashboardController::certificateReport', ['as' => 'dashboard.reports.certificates']);
        $routes->get('kompetensi', 'DashboardController::competencyReport', ['as' => 'dashboard.reports.competency']);
        $routes->get('pengguna', 'DashboardController::userReport', ['as' => 'dashboard.reports.users']);
        $routes->get('riwayat-export', 'DashboardController::exportHistory', ['as' => 'dashboard.reports.export-history']);
    });

    $routes->group('sistem', static function ($routes) {
        $routes->get('approval', 'ApprovalController::index', ['as' => 'dashboard.system.approvals']);
        $routes->get('approval/riwayat', 'ApprovalController::history', ['as' => 'dashboard.system.approvals.history']);
        $routes->get('approval/(:segment)/setujui', 'ApprovalController::approve/$1', ['as' => 'dashboard.system.approvals.approve']);
        $routes->get('approval/(:segment)/tolak', 'ApprovalController::reject/$1', ['as' => 'dashboard.system.approvals.reject']);
        $routes->get('approval/(:segment)', 'ApprovalController::detail/$1', ['as' => 'dashboard.system.approvals.detail']);

        $routes->get('audit-trail', 'AuditController::index', ['as' => 'dashboard.system.audit-trail']);
        $routes->get('audit-trail/(:segment)', 'AuditController::detail/$1', ['as' => 'dashboard.system.audit-trail.detail']);
        $routes->get('file-media', 'AuditController::files', ['as' => 'dashboard.system.files-media']);
        $routes->get('notifikasi', 'AuditController::notifications', ['as' => 'dashboard.system.notifications']);

        $routes->get('pengaturan', 'SettingController::index', ['as' => 'dashboard.system.settings']);
        $routes->get('pengaturan-umum', 'SettingController::general', ['as' => 'dashboard.system.settings.general']);
        $routes->get('pengaturan-organisasi', 'SettingController::organization', ['as' => 'dashboard.system.settings.organization']);
        $routes->get('pengaturan-pembelajaran', 'SettingController::learning', ['as' => 'dashboard.system.settings.learning']);
        $routes->get('pengaturan-event', 'SettingController::event', ['as' => 'dashboard.system.settings.event']);
        $routes->get('pengaturan-sertifikat', 'SettingController::certificate', ['as' => 'dashboard.system.settings.certificate']);
        $routes->get('pengaturan-notifikasi', 'SettingController::notification', ['as' => 'dashboard.system.settings.notification']);
        $routes->get('pengaturan-integrasi', 'SettingController::integration', ['as' => 'dashboard.system.settings.integration']);
        $routes->get('pengaturan-keamanan', 'SettingController::security', ['as' => 'dashboard.system.settings.security']);
        $routes->get('maintenance', 'SettingController::maintenance', ['as' => 'dashboard.system.maintenance']);
        $routes->post('pengaturan/(:segment)', 'SettingController::save/$1', ['as' => 'dashboard.system.settings.save']);
        $routes->get('settings', 'SettingController::index');
        $routes->get('settings/(:segment)', 'SettingController::groupView/$1');
        $routes->post('settings/(:segment)', 'SettingController::save/$1');
    });
});

$routes->get('pratinjau/403', 'Landing\HomeController::forbiddenPreview', ['as' => 'preview.403']);
$routes->get('pratinjau/404', 'Landing\HomeController::notFoundPreview', ['as' => 'preview.404']);
$routes->get('pratinjau/500', 'Landing\HomeController::serverErrorPreview', ['as' => 'preview.500']);

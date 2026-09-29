<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

$routes->setDefaultNamespace('App\\Controllers');
$routes->setDefaultController('Landing\\HomeController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->setAutoRoute(false);
$routes->set404Override(static function () {
    return view('error/404');
});

/*
|--------------------------------------------------------------------------
| Landing dan Public Area — FiStudy
|--------------------------------------------------------------------------
*/
$routes->group('', ['namespace' => 'App\\Controllers\\Landing'], static function (RouteCollection $routes): void {
    // Landing dan halaman umum.
    $routes->get('/', 'HomeController::index', ['as' => 'landing.home']);
    $routes->get('tentang', 'PublicPageController::about', ['as' => 'landing.pages.about']);
    $routes->get('kontak', 'PublicPageController::contact', ['as' => 'landing.pages.contact']);
    $routes->post('kontak', 'PublicPageController::sendContact', ['as' => 'landing.pages.contact.send']);
    $routes->get('privasi', 'PublicPageController::privacy', ['as' => 'landing.pages.privacy']);
    $routes->get('syarat-ketentuan', 'PublicPageController::terms', ['as' => 'landing.pages.terms']);
    $routes->get('aksesibilitas', 'PublicPageController::accessibility', ['as' => 'landing.pages.accessibility']);

    // Arena Belajar publik.
    $routes->group('arena-belajar', static function (RouteCollection $routes): void {
        $routes->get('/', 'ArenaBelajarController::index', ['as' => 'landing.learning.index']);
        $routes->get('program', 'ArenaBelajarController::programs', ['as' => 'landing.learning.programs.index']);
        $routes->get('program/(:segment)', 'ArenaBelajarController::program/$1', ['as' => 'landing.learning.program.show']);
        $routes->get('course', 'ArenaBelajarController::courses', ['as' => 'landing.learning.courses.index']);
        $routes->get('course/(:segment)', 'ArenaBelajarController::course/$1', ['as' => 'landing.learning.courses.show']);
        $routes->get('kelas', 'ArenaBelajarController::classes', ['as' => 'landing.learning.classes.index']);
        $routes->get('kelas/(:segment)', 'ArenaBelajarController::classDetail/$1', ['as' => 'landing.learning.classes.show']);
        $routes->get('instruktur', 'ArenaBelajarController::instructors', ['as' => 'landing.learning.instructors.index']);
        $routes->get('instruktur/(:segment)', 'ArenaBelajarController::instructor/$1', ['as' => 'landing.learning.instructors.show']);
    });

    // Bunga Rampai publik.
    $routes->group('bunga-rampai', static function (RouteCollection $routes): void {
        $routes->get('/', 'BungaRampaiController::index', ['as' => 'landing.knowledge.index']);
        $routes->get('cari', 'BungaRampaiController::search', ['as' => 'landing.knowledge.search']);
        $routes->get('artikel', 'BungaRampaiController::articles', ['as' => 'landing.knowledge.articles.index']);
        $routes->get('artikel/(:segment)', 'BungaRampaiController::article/$1', ['as' => 'landing.knowledge.articles.show']);
        $routes->get('faq', 'BungaRampaiController::faq', ['as' => 'landing.knowledge.faq']);
        $routes->get('studi-kasus', 'BungaRampaiController::caseStudies', ['as' => 'landing.knowledge.case-studies.index']);
        $routes->get('studi-kasus/(:segment)', 'BungaRampaiController::caseStudy/$1', ['as' => 'landing.knowledge.case-studies.show']);
        $routes->get('template', 'BungaRampaiController::templates', ['as' => 'landing.knowledge.templates.index']);
        $routes->get('template/(:segment)', 'BungaRampaiController::templateDetail/$1', ['as' => 'landing.knowledge.templates.show']);
        $routes->get('media', 'BungaRampaiController::media', ['as' => 'landing.knowledge.media.index']);
        $routes->get('video', 'BungaRampaiController::videos', ['as' => 'landing.knowledge.videos.index']);
        $routes->get('galeri', 'BungaRampaiController::gallery', ['as' => 'landing.knowledge.gallery.index']);
    });

    // Regulasi publik.
    $routes->group('regulasi', static function (RouteCollection $routes): void {
        $routes->get('/', 'RegulationPublicController::index', ['as' => 'landing.regulations.index']);
        $routes->get('cari', 'RegulationPublicController::search', ['as' => 'landing.regulations.search']);
        $routes->get('kategori/(:segment)', 'RegulationPublicController::category/$1', ['as' => 'landing.regulations.category']);
        $routes->get('(:segment)/versi', 'RegulationPublicController::versions/$1', ['as' => 'landing.regulations.versions']);
        $routes->get('(:segment)/relasi', 'RegulationPublicController::relations/$1', ['as' => 'landing.regulations.relations']);
        $routes->get('(:segment)/preview', 'RegulationPublicController::preview/$1', ['as' => 'landing.regulations.preview']);
        $routes->get('(:segment)', 'RegulationPublicController::show/$1', ['as' => 'landing.regulations.show']);
    });

    // Berita publik.
    $routes->group('berita', static function (RouteCollection $routes): void {
        $routes->get('/', 'NewsPublicController::index', ['as' => 'landing.news.index']);
        $routes->get('kategori/(:segment)', 'NewsPublicController::category/$1', ['as' => 'landing.news.category']);
        $routes->get('(:segment)', 'NewsPublicController::show/$1', ['as' => 'landing.news.show']);
    });

    // Event publik.
    $routes->group('event', static function (RouteCollection $routes): void {
        $routes->get('/', 'EventPublicController::index', ['as' => 'landing.events.index']);
        $routes->get('agenda', 'EventPublicController::agenda', ['as' => 'landing.events.agenda']);
        $routes->get('kalender', 'EventPublicController::calendar', ['as' => 'landing.events.calendar']);
        $routes->get('(:segment)', 'EventPublicController::show/$1', ['as' => 'landing.events.show']);
    });

    // Statistik publik.
    $routes->group('statistik', static function (RouteCollection $routes): void {
        $routes->get('/', 'StatisticPublicController::index', ['as' => 'landing.statistics.index']);
        $routes->get('data', 'StatisticPublicController::data', ['as' => 'landing.statistics.data']);
        $routes->get('pembelajaran', 'StatisticPublicController::learning', ['as' => 'landing.statistics.learning']);
        $routes->get('event', 'StatisticPublicController::events', ['as' => 'landing.statistics.events']);
        $routes->get('kompetensi', 'StatisticPublicController::competency', ['as' => 'landing.statistics.competency']);
    });

    // Verifikasi sertifikat publik.
    $routes->group('sertifikat/verifikasi', static function (RouteCollection $routes): void {
        $routes->get('/', 'CertificateVerificationController::form', ['as' => 'landing.certificates.verify.form']);
        $routes->post('/', 'CertificateVerificationController::verify', ['as' => 'landing.certificates.verify.submit']);
        $routes->get('(:segment)', 'CertificateVerificationController::verifyToken/$1', ['as' => 'landing.certificates.verify.token']);
    });
});

/*
|--------------------------------------------------------------------------
| Auth Terpadu — WowDash
|--------------------------------------------------------------------------
*/
$routes->group('dashboard', ['namespace' => 'App\\Controllers\\Dashboard\\Auth'], static function (RouteCollection $routes): void {
    // Login dan logout.
    $routes->get('masuk', 'LoginController::index', ['as' => 'dashboard.auth.login']);
    $routes->post('masuk', 'LoginController::attempt', ['as' => 'dashboard.auth.login.attempt']);
    $routes->post('keluar', 'LogoutController::logout', ['as' => 'dashboard.auth.logout']);

    // Registrasi multi-step.
    $routes->get('daftar', 'RegisterController::account', ['as' => 'dashboard.auth.register.account']);
    $routes->post('daftar', 'RegisterController::storeAccount', ['as' => 'dashboard.auth.register.account.store']);
    $routes->get('daftar/wilayah', 'RegisterController::region', ['as' => 'dashboard.auth.register.region']);
    $routes->post('daftar/wilayah', 'RegisterController::storeRegion', ['as' => 'dashboard.auth.register.region.store']);
    $routes->get('daftar/profil/(:alpha)', 'RegisterProfileController::form/$1', ['as' => 'dashboard.auth.register.profile']);
    $routes->post('daftar/profil/(:alpha)', 'RegisterProfileController::store/$1', ['as' => 'dashboard.auth.register.profile.store']);
    $routes->get('daftar/tinjau', 'RegisterController::review', ['as' => 'dashboard.auth.register.review']);
    $routes->post('daftar/tinjau', 'RegisterController::submit', ['as' => 'dashboard.auth.register.submit']);
    $routes->get('daftar/berhasil', 'RegisterController::success', ['as' => 'dashboard.auth.register.success']);

    // Verifikasi email dan status akun.
    $routes->get('verifikasi/(:segment)', 'EmailVerificationController::verify/$1', ['as' => 'dashboard.auth.verify-email']);
    $routes->post('verifikasi/kirim-ulang', 'EmailVerificationController::resend', ['as' => 'dashboard.auth.verify-email.resend']);
    $routes->get('status-akun', 'AccountStatusController::show', ['as' => 'dashboard.auth.account-status']);

    // Lupa dan reset kata sandi.
    $routes->get('lupa-kata-sandi', 'PasswordController::forgotForm', ['as' => 'dashboard.auth.password.forgot']);
    $routes->post('lupa-kata-sandi', 'PasswordController::sendReset', ['as' => 'dashboard.auth.password.send-reset']);
    $routes->get('reset-kata-sandi/(:segment)', 'PasswordController::resetForm/$1', ['as' => 'dashboard.auth.password.reset']);
    $routes->post('reset-kata-sandi/(:segment)', 'PasswordController::updatePassword/$1', ['as' => 'dashboard.auth.password.update']);
});

/*
|--------------------------------------------------------------------------
| Workspace — FiStudy
|--------------------------------------------------------------------------
*/
$routes->group('workspace', ['namespace' => 'App\\Controllers\\Workspace'], static function (RouteCollection $routes): void {
    // Workspace umum dan profil.
    $routes->get('/', 'WorkspaceHomeController::index', ['as' => 'workspace.home']);
    $routes->get('profil', 'ProfileController::index', ['as' => 'workspace.profile.index']);
    $routes->get('profil/akun', 'ProfileController::account', ['as' => 'workspace.profile.account']);
    $routes->patch('profil/akun', 'ProfileController::updateAccount', ['as' => 'workspace.profile.account.update']);
    $routes->get('profil/asn', 'ProfileController::asn', ['as' => 'workspace.profile.asn']);
    $routes->get('profil/penyedia', 'ProfileController::provider', ['as' => 'workspace.profile.provider']);
    $routes->get('profil/akademik', 'ProfileController::academic', ['as' => 'workspace.profile.academic']);
    $routes->get('profil/masyarakat', 'ProfileController::public', ['as' => 'workspace.profile.public']);
    $routes->get('profil/peran-pengadaan', 'ProfileController::procurementRoles', ['as' => 'workspace.profile.procurement-roles']);
    $routes->get('profil/perubahan', 'ProfileChangeRequestController::index', ['as' => 'workspace.profile.change-requests']);
    $routes->post('profil/perubahan', 'ProfileChangeRequestController::store', ['as' => 'workspace.profile.change-requests.store']);
    $routes->get('profil/pengajuan-role', 'RoleRequestController::index', ['as' => 'workspace.profile.role-requests']);
    $routes->post('profil/pengajuan-role', 'RoleRequestController::store', ['as' => 'workspace.profile.role-requests.store']);

    // Bookmark dan notifikasi.
    $routes->group('bookmark', static function (RouteCollection $routes): void {
        $routes->get('/', 'BookmarkController::index', ['as' => 'workspace.bookmarks.index']);
        $routes->get('pembelajaran', 'BookmarkController::learning', ['as' => 'workspace.bookmarks.learning']);
        $routes->get('bunga-rampai', 'BookmarkController::knowledge', ['as' => 'workspace.bookmarks.knowledge']);
        $routes->get('regulasi', 'BookmarkController::regulations', ['as' => 'workspace.bookmarks.regulations']);
        $routes->get('komunikasi', 'BookmarkController::communication', ['as' => 'workspace.bookmarks.communication']);
        $routes->get('sertifikat', 'BookmarkController::certificates', ['as' => 'workspace.bookmarks.certificates']);
        $routes->post('(:segment)/(:num)', 'BookmarkController::store/$1/$2', ['as' => 'workspace.bookmarks.store']);
        $routes->delete('(:segment)/(:num)', 'BookmarkController::delete/$1/$2', ['as' => 'workspace.bookmarks.delete']);
    });

    $routes->group('notifikasi', static function (RouteCollection $routes): void {
        $routes->get('/', 'NotificationController::index', ['as' => 'workspace.notifications.index']);
        $routes->get('(:num)', 'NotificationController::detail/$1', ['as' => 'workspace.notifications.detail']);
        $routes->patch('(:num)/dibaca', 'NotificationController::markRead/$1', ['as' => 'workspace.notifications.read']);
        $routes->post('baca-semua', 'NotificationController::markAllRead', ['as' => 'workspace.notifications.read-all']);
    });

    // Participant Workspace.
    $routes->group('peserta', static function (RouteCollection $routes): void {
        $routes->get('/', 'ParticipantController::dashboard', ['as' => 'workspace.participant.dashboard']);
        $routes->get('enrollment', 'ParticipantController::enrollments', ['as' => 'workspace.participant.enrollments']);
        $routes->get('pembelajaran/(:num)', 'ParticipantController::learningDetail/$1', ['as' => 'workspace.participant.learning.show']);
        $routes->get('belajar/(:num)', 'ParticipantController::study/$1', ['as' => 'workspace.participant.study']);
        $routes->patch('belajar/(:num)/progres', '\\App\\Controllers\\Learning\\LearningProgressController::update/$1', ['as' => 'workspace.participant.progress.update']);
        $routes->get('pre-test/(:num)', '\\App\\Controllers\\Learning\\PreTestController::form/$1', ['as' => 'workspace.participant.pre-test']);
        $routes->post('pre-test/(:num)', '\\App\\Controllers\\Learning\\PreTestController::submit/$1', ['as' => 'workspace.participant.pre-test.submit']);
        $routes->get('pre-test/(:num)/hasil', '\\App\\Controllers\\Learning\\PreTestController::result/$1', ['as' => 'workspace.participant.pre-test.result']);
        $routes->get('quiz/(:num)', '\\App\\Controllers\\Learning\\QuizController::form/$1', ['as' => 'workspace.participant.quiz']);
        $routes->post('quiz/(:num)', '\\App\\Controllers\\Learning\\QuizController::submit/$1', ['as' => 'workspace.participant.quiz.submit']);
        $routes->get('quiz/(:num)/hasil', '\\App\\Controllers\\Learning\\QuizController::result/$1', ['as' => 'workspace.participant.quiz.result']);
        $routes->get('tugas/(:num)', '\\App\\Controllers\\Learning\\AssignmentController::show/$1', ['as' => 'workspace.participant.assignments.show']);
        $routes->post('tugas/(:num)', '\\App\\Controllers\\Learning\\AssignmentController::submit/$1', ['as' => 'workspace.participant.assignments.submit']);
        $routes->get('tugas/(:num)/pengumpulan', '\\App\\Controllers\\Learning\\AssignmentController::submission/$1', ['as' => 'workspace.participant.assignments.submission']);
        $routes->get('feedback/(:num)', '\\App\\Controllers\\Learning\\FeedbackController::form/$1', ['as' => 'workspace.participant.feedback']);
        $routes->post('feedback/(:num)', '\\App\\Controllers\\Learning\\FeedbackController::submit/$1', ['as' => 'workspace.participant.feedback.submit']);
        $routes->get('sertifikat', 'ParticipantController::certificates', ['as' => 'workspace.participant.certificates']);
        $routes->get('sertifikat/(:num)', 'ParticipantController::certificateDetail/$1', ['as' => 'workspace.participant.certificates.show']);
    });

    // Trainer Workspace.
    $routes->group('pelatih', static function (RouteCollection $routes): void {
        $routes->get('/', 'TrainerController::dashboard', ['as' => 'workspace.trainer.dashboard']);
        $routes->get('kelas', 'TrainerController::classes', ['as' => 'workspace.trainer.classes.index']);
        $routes->get('kelas/(:num)', 'TrainerController::classDetail/$1', ['as' => 'workspace.trainer.classes.show']);
        $routes->get('kelas/(:num)/materi/baru', '\\App\\Controllers\\Learning\\LessonController::create/$1', ['as' => 'workspace.trainer.lessons.create']);
        $routes->post('kelas/(:num)/materi', '\\App\\Controllers\\Learning\\LessonController::store/$1', ['as' => 'workspace.trainer.lessons.store']);
        $routes->get('kelas/(:num)/quiz/baru', '\\App\\Controllers\\Learning\\QuizController::create/$1', ['as' => 'workspace.trainer.quizzes.create']);
        $routes->post('kelas/(:num)/quiz', '\\App\\Controllers\\Learning\\QuizController::store/$1', ['as' => 'workspace.trainer.quizzes.store']);
        $routes->get('kelas/(:num)/tugas/baru', '\\App\\Controllers\\Learning\\AssignmentController::create/$1', ['as' => 'workspace.trainer.assignments.create']);
        $routes->post('kelas/(:num)/tugas', '\\App\\Controllers\\Learning\\AssignmentController::store/$1', ['as' => 'workspace.trainer.assignments.store']);
        $routes->get('kelas/(:num)/progres-peserta', 'TrainerController::participantProgress/$1', ['as' => 'workspace.trainer.participants.progress']);
    });

    // Evaluator Workspace.
    $routes->group('evaluator', static function (RouteCollection $routes): void {
        $routes->get('/', 'EvaluatorController::dashboard', ['as' => 'workspace.evaluator.dashboard']);
        $routes->get('antrian-penilaian', 'EvaluatorController::assignmentQueue', ['as' => 'workspace.evaluator.assignments.queue']);
        $routes->get('pengumpulan/(:num)', '\\App\\Controllers\\Learning\\GradeController::form/$1', ['as' => 'workspace.evaluator.assignments.grade']);
        $routes->post('pengumpulan/(:num)/nilai', '\\App\\Controllers\\Learning\\GradeController::store/$1', ['as' => 'workspace.evaluator.assignments.grade.store']);
        $routes->get('riwayat-penilaian', 'EvaluatorController::assessmentHistory', ['as' => 'workspace.evaluator.assessments.history']);
    });

    // Operator Workspace.
    $routes->group('operator', static function (RouteCollection $routes): void {
        $routes->get('/', 'OperatorController::dashboard', ['as' => 'workspace.operator.dashboard']);
        $routes->get('antrian-verifikasi', 'OperatorController::verificationQueue', ['as' => 'workspace.operator.verification.queue']);
        $routes->post('verifikasi/(:num)', 'OperatorController::decideVerification/$1', ['as' => 'workspace.operator.verification.decide']);
        $routes->get('enrollment', 'OperatorController::enrollmentManagement', ['as' => 'workspace.operator.enrollments']);
        $routes->get('absensi', 'OperatorController::attendanceManagement', ['as' => 'workspace.operator.attendance']);
        $routes->get('approval-terdelegasi', 'OperatorController::delegatedApprovals', ['as' => 'workspace.operator.approvals']);
    });
});

/*
|--------------------------------------------------------------------------
| Dashboard Back-office — WowDash
|--------------------------------------------------------------------------
*/
$routes->group('dashboard', ['namespace' => 'App\\Controllers\\Dashboard'], static function (RouteCollection $routes): void {
    // Dashboard home dan summary internal.
    $routes->get('/', 'DashboardController::index', ['as' => 'dashboard.home']);
    $routes->get('kpi-internal', 'DashboardController::internalKpi', ['as' => 'dashboard.summary.kpi']);
    $routes->get('antrean-persetujuan', 'DashboardController::approvalQueue', ['as' => 'dashboard.summary.approval-queue']);
    $routes->get('aktivitas-terbaru', 'DashboardController::recentActivity', ['as' => 'dashboard.summary.recent-activity']);
    $routes->get('indikator-sistem', 'DashboardController::systemIndicators', ['as' => 'dashboard.summary.system-indicators']);

    // Profil Back-office.
    $routes->group('profil', static function (RouteCollection $routes): void {
        $routes->get('/', 'ProfileController::index', ['as' => 'dashboard.profile.index']);
        $routes->get('ubah', 'ProfileController::edit', ['as' => 'dashboard.profile.edit']);
        $routes->patch('/', 'ProfileController::update', ['as' => 'dashboard.profile.update']);
        $routes->get('kata-sandi', 'ProfileController::password', ['as' => 'dashboard.profile.password']);
        $routes->patch('kata-sandi', 'ProfileController::updatePassword', ['as' => 'dashboard.profile.password.update']);
        $routes->get('preferensi', 'ProfileController::preferences', ['as' => 'dashboard.profile.preferences']);
        $routes->patch('preferensi', 'ProfileController::updatePreferences', ['as' => 'dashboard.profile.preferences.update']);
    });

    // User dan verifikasi profil.
    $routes->group('pengguna', static function (RouteCollection $routes): void {
        $routes->get('/', 'UserController::index', ['as' => 'dashboard.users.index']);
        $routes->get('baru', 'UserController::create', ['as' => 'dashboard.users.create']);
        $routes->post('/', 'UserController::store', ['as' => 'dashboard.users.store']);
        $routes->get('verifikasi-profil', 'UserProfileVerificationController::index', ['as' => 'dashboard.users.profile-verification-queue']);
        $routes->get('pengajuan-role', 'RoleRequestReviewController::index', ['as' => 'dashboard.role-requests.index']);
        $routes->get('(:num)', 'UserController::show/$1', ['as' => 'dashboard.users.show']);
        $routes->get('(:num)/ubah', 'UserController::edit/$1', ['as' => 'dashboard.users.edit']);
        $routes->patch('(:num)', 'UserController::update/$1', ['as' => 'dashboard.users.update']);
        $routes->post('(:num)/verifikasi-profil', 'UserProfileVerificationController::decide/$1', ['as' => 'dashboard.users.profile-verification.decide']);
        $routes->get('pengajuan-role/(:num)', 'RoleRequestReviewController::show/$1', ['as' => 'dashboard.role-requests.show']);
        $routes->post('pengajuan-role/(:num)', 'RoleRequestReviewController::decide/$1', ['as' => 'dashboard.role-requests.decide']);
    });

    // RBAC.
    $routes->group('rbac', static function (RouteCollection $routes): void {
        $routes->get('role', 'RoleController::index', ['as' => 'dashboard.roles.index']);
        $routes->get('role/baru', 'RoleController::create', ['as' => 'dashboard.roles.create']);
        $routes->post('role', 'RoleController::store', ['as' => 'dashboard.roles.store']);
        $routes->get('role/(:num)/ubah', 'RoleController::edit/$1', ['as' => 'dashboard.roles.edit']);
        $routes->patch('role/(:num)', 'RoleController::update/$1', ['as' => 'dashboard.roles.update']);
        $routes->get('permission', 'PermissionController::index', ['as' => 'dashboard.permissions.index']);
        $routes->get('permission/baru', 'PermissionController::create', ['as' => 'dashboard.permissions.create']);
        $routes->post('permission', 'PermissionController::store', ['as' => 'dashboard.permissions.store']);
        $routes->get('matrix', 'RoleController::permissionMatrix', ['as' => 'dashboard.roles.permission-matrix']);
        $routes->patch('matrix', 'RoleController::syncPermissionMatrix', ['as' => 'dashboard.roles.permission-matrix.sync']);
    });

    // Approval dan audit.
    $routes->group('approval', static function (RouteCollection $routes): void {
        $routes->get('/', 'ApprovalController::index', ['as' => 'dashboard.approvals.index']);
        $routes->get('riwayat', 'ApprovalController::history', ['as' => 'dashboard.approvals.history']);
        $routes->get('(:num)', 'ApprovalController::show/$1', ['as' => 'dashboard.approvals.show']);
        $routes->post('(:num)/setujui', 'ApprovalController::approve/$1', ['as' => 'dashboard.approvals.approve']);
        $routes->post('(:num)/tolak', 'ApprovalController::reject/$1', ['as' => 'dashboard.approvals.reject']);
    });

    $routes->group('audit', static function (RouteCollection $routes): void {
        $routes->get('/', 'AuditController::index', ['as' => 'dashboard.audits.index']);
        $routes->get('(:num)', 'AuditController::show/$1', ['as' => 'dashboard.audits.show']);
    });

    // Organisasi.
    $routes->group('organisasi', static function (RouteCollection $routes): void {
        $routes->get('/', 'OrganizationController::index', ['as' => 'dashboard.organizations.index']);
        $routes->get('baru', 'OrganizationController::create', ['as' => 'dashboard.organizations.create']);
        $routes->post('/', 'OrganizationController::store', ['as' => 'dashboard.organizations.store']);
        $routes->get('(:num)/ubah', 'OrganizationController::edit/$1', ['as' => 'dashboard.organizations.edit']);
        $routes->patch('(:num)', 'OrganizationController::update/$1', ['as' => 'dashboard.organizations.update']);
    });

    // Arena Belajar Back-office.
    $routes->group('pembelajaran', ['namespace' => 'App\\Controllers\\Learning'], static function (RouteCollection $routes): void {
        $routes->get('program', 'ProgramController::index', ['as' => 'dashboard.learning.programs.index']);
        $routes->get('program/baru', 'ProgramController::create', ['as' => 'dashboard.learning.programs.create']);
        $routes->post('program', 'ProgramController::store', ['as' => 'dashboard.learning.programs.store']);
        $routes->get('program/(:num)/ubah', 'ProgramController::edit/$1', ['as' => 'dashboard.learning.programs.edit']);
        $routes->patch('program/(:num)', 'ProgramController::update/$1', ['as' => 'dashboard.learning.programs.update']);
        $routes->get('course', 'CourseController::index', ['as' => 'dashboard.learning.courses.index']);
        $routes->get('course/baru', 'CourseController::create', ['as' => 'dashboard.learning.courses.create']);
        $routes->post('course', 'CourseController::store', ['as' => 'dashboard.learning.courses.store']);
        $routes->get('course/(:num)/ubah', 'CourseController::edit/$1', ['as' => 'dashboard.learning.courses.edit']);
        $routes->patch('course/(:num)', 'CourseController::update/$1', ['as' => 'dashboard.learning.courses.update']);
        $routes->get('kelas', 'LearningClassController::index', ['as' => 'dashboard.learning.classes.index']);
        $routes->get('kelas/baru', 'LearningClassController::create', ['as' => 'dashboard.learning.classes.create']);
        $routes->post('kelas', 'LearningClassController::store', ['as' => 'dashboard.learning.classes.store']);
        $routes->get('kelas/(:num)/ubah', 'LearningClassController::edit/$1', ['as' => 'dashboard.learning.classes.edit']);
        $routes->patch('kelas/(:num)', 'LearningClassController::update/$1', ['as' => 'dashboard.learning.classes.update']);
        $routes->get('modul', 'ModuleController::index', ['as' => 'dashboard.learning.modules.index']);
        $routes->get('materi', 'LessonController::index', ['as' => 'dashboard.learning.lessons.index']);
        $routes->get('enrollment', 'EnrollmentController::index', ['as' => 'dashboard.learning.enrollments.index']);
        $routes->get('nilai', 'GradeController::index', ['as' => 'dashboard.learning.grades.index']);
        $routes->get('laporan', 'LearningClassController::reports', ['as' => 'dashboard.learning.reports.index']);
    });

    // Bunga Rampai Back-office.
    $routes->group('bunga-rampai', ['namespace' => 'App\\Controllers\\Knowledge'], static function (RouteCollection $routes): void {
        $routes->get('artikel', 'KnowledgeArticleController::index', ['as' => 'dashboard.knowledge.articles.index']);
        $routes->get('artikel/baru', 'KnowledgeArticleController::create', ['as' => 'dashboard.knowledge.articles.create']);
        $routes->post('artikel', 'KnowledgeArticleController::store', ['as' => 'dashboard.knowledge.articles.store']);
        $routes->get('artikel/(:num)/ubah', 'KnowledgeArticleController::edit/$1', ['as' => 'dashboard.knowledge.articles.edit']);
        $routes->patch('artikel/(:num)', 'KnowledgeArticleController::update/$1', ['as' => 'dashboard.knowledge.articles.update']);
        $routes->get('faq', 'KnowledgeFaqController::index', ['as' => 'dashboard.knowledge.faq.index']);
        $routes->get('kategori', 'KnowledgeCategoryController::index', ['as' => 'dashboard.knowledge.categories.index']);
        $routes->get('media', 'KnowledgeMediaController::index', ['as' => 'dashboard.knowledge.media.index']);
        $routes->get('antrian-publikasi', 'KnowledgeArticleController::publicationQueue', ['as' => 'dashboard.knowledge.publication-queue']);
    });

    // Regulasi Back-office.
    $routes->group('regulasi', ['namespace' => 'App\\Controllers\\Regulation'], static function (RouteCollection $routes): void {
        $routes->get('/', 'RegulationController::index', ['as' => 'dashboard.regulations.index']);
        $routes->get('baru', 'RegulationController::create', ['as' => 'dashboard.regulations.create']);
        $routes->post('/', 'RegulationController::store', ['as' => 'dashboard.regulations.store']);
        $routes->get('(:num)/ubah', 'RegulationController::edit/$1', ['as' => 'dashboard.regulations.edit']);
        $routes->patch('(:num)', 'RegulationController::update/$1', ['as' => 'dashboard.regulations.update']);
        $routes->get('kategori', 'RegulationCategoryController::index', ['as' => 'dashboard.regulations.categories.index']);
        $routes->get('versi', 'RegulationVersionController::index', ['as' => 'dashboard.regulations.versions.index']);
        $routes->get('relasi', 'RegulationRelationController::index', ['as' => 'dashboard.regulations.relations.index']);
        $routes->get('antrian-publikasi', 'RegulationController::publicationQueue', ['as' => 'dashboard.regulations.publication-queue']);
    });

    // Berita dan Event Back-office.
    $routes->group('komunikasi', ['namespace' => 'App\\Controllers\\Communication'], static function (RouteCollection $routes): void {
        $routes->get('berita', 'NewsController::index', ['as' => 'dashboard.communication.news.index']);
        $routes->get('berita/baru', 'NewsController::create', ['as' => 'dashboard.communication.news.create']);
        $routes->post('berita', 'NewsController::store', ['as' => 'dashboard.communication.news.store']);
        $routes->get('berita/(:num)/ubah', 'NewsController::edit/$1', ['as' => 'dashboard.communication.news.edit']);
        $routes->patch('berita/(:num)', 'NewsController::update/$1', ['as' => 'dashboard.communication.news.update']);
        $routes->get('event', 'EventController::index', ['as' => 'dashboard.communication.events.index']);
        $routes->get('event/baru', 'EventController::create', ['as' => 'dashboard.communication.events.create']);
        $routes->post('event', 'EventController::store', ['as' => 'dashboard.communication.events.store']);
        $routes->get('event/(:num)/ubah', 'EventController::edit/$1', ['as' => 'dashboard.communication.events.edit']);
        $routes->patch('event/(:num)', 'EventController::update/$1', ['as' => 'dashboard.communication.events.update']);
        $routes->get('registrasi', 'EventRegistrationController::index', ['as' => 'dashboard.communication.registrations.index']);
        $routes->get('absensi', 'AttendanceController::index', ['as' => 'dashboard.communication.attendance.index']);
    });

    // Sertifikat Back-office.
    $routes->group('sertifikat', ['namespace' => 'App\\Controllers\\Certificate'], static function (RouteCollection $routes): void {
        $routes->get('/', 'CertificateController::index', ['as' => 'dashboard.certificates.index']);
        $routes->get('(:num)', 'CertificateController::show/$1', ['as' => 'dashboard.certificates.show']);
        $routes->get('antrian-approval', 'CertificateApprovalController::index', ['as' => 'dashboard.certificates.approval-queue']);
        $routes->get('dicabut', 'CertificateController::revoked', ['as' => 'dashboard.certificates.revoked']);
        $routes->get('template', 'CertificateTemplateController::index', ['as' => 'dashboard.certificates.templates.index']);
        $routes->get('template/baru', 'CertificateTemplateController::create', ['as' => 'dashboard.certificates.templates.create']);
        $routes->post('template', 'CertificateTemplateController::store', ['as' => 'dashboard.certificates.templates.store']);
    });

    // Government Intelligence dan laporan internal.
    $routes->group('intelligence', ['namespace' => 'App\\Controllers\\Intelligence'], static function (RouteCollection $routes): void {
        $routes->get('/', 'IntelligenceDashboardController::index', ['as' => 'dashboard.intelligence.index']);
        $routes->get('organisasi', 'OpdController::index', ['as' => 'dashboard.intelligence.organizations.index']);
        $routes->get('jf-ppbj', 'JfPpbjController::index', ['as' => 'dashboard.intelligence.jf-ppbj.index']);
        $routes->get('sertifikat-kompetensi', 'CompetencyCertificateController::index', ['as' => 'dashboard.intelligence.competency-certificates.index']);
        $routes->get('gap-kompetensi', 'IntelligenceDashboardController::competencyGap', ['as' => 'dashboard.intelligence.competency-gaps.index']);
        $routes->get('export', 'IntelligenceDashboardController::exports', ['as' => 'dashboard.intelligence.exports.index']);
    });

    $routes->group('laporan', static function (RouteCollection $routes): void {
        $routes->get('/', 'InternalReportController::index', ['as' => 'dashboard.reports.index']);
        $routes->get('statistik-internal', 'InternalReportController::internalStatistics', ['as' => 'dashboard.reports.internal-statistics']);
        $routes->get('pembelajaran', 'InternalReportController::learning', ['as' => 'dashboard.reports.learning']);
        $routes->get('event', 'InternalReportController::events', ['as' => 'dashboard.reports.events']);
        $routes->get('sertifikat', 'InternalReportController::certificates', ['as' => 'dashboard.reports.certificates']);
        $routes->get('kompetensi', 'InternalReportController::competency', ['as' => 'dashboard.reports.competency']);
        $routes->get('pengguna', 'InternalReportController::users', ['as' => 'dashboard.reports.users']);
    });

    // Settings dan system health.
    $routes->group('pengaturan', static function (RouteCollection $routes): void {
        $routes->get('/', 'SettingController::index', ['as' => 'dashboard.settings.index']);
        $routes->get('aplikasi', 'SettingController::application', ['as' => 'dashboard.settings.application']);
        $routes->get('notifikasi', 'SettingController::notification', ['as' => 'dashboard.settings.notification']);
        $routes->get('storage', 'SettingController::storage', ['as' => 'dashboard.settings.storage']);
        $routes->get('keamanan', 'SettingController::security', ['as' => 'dashboard.settings.security']);
        $routes->get('kesehatan-sistem', 'SystemHealthController::index', ['as' => 'dashboard.system-health.index']);
    });
});

/*
|--------------------------------------------------------------------------
| Shared File, Search, Export, dan Health
|--------------------------------------------------------------------------
*/
$routes->group('', ['namespace' => 'App\\Controllers\\Shared'], static function (RouteCollection $routes): void {
    $routes->get('file/(:num)/preview', 'FileController::preview/$1', ['as' => 'shared.file.preview']);
    $routes->get('file/(:num)/unduh', 'FileController::download/$1', ['as' => 'shared.file.download']);
    $routes->post('file/upload', 'FileController::upload', ['as' => 'shared.file.upload']);

    $routes->get('cari', 'SearchController::index', ['as' => 'shared.search.index']);

    $routes->get('export', 'ExportController::index', ['as' => 'shared.export.index']);
    $routes->post('export', 'ExportController::create', ['as' => 'shared.export.create']);
    $routes->get('export/(:num)/unduh', 'ExportController::download/$1', ['as' => 'shared.export.download']);

    $routes->get('health', 'HealthController::index', ['as' => 'shared.health.index']);
});

/*
|--------------------------------------------------------------------------
| Preview Error — Development/UI QA
|--------------------------------------------------------------------------
*/
$routes->group('pratinjau', ['namespace' => 'App\\Controllers\\Landing'], static function (RouteCollection $routes): void {
    $routes->get('400', 'HomeController::badRequestPreview', ['as' => 'preview.400']);
    $routes->get('403', 'HomeController::forbiddenPreview', ['as' => 'preview.403']);
    $routes->get('404', 'HomeController::notFoundPreview', ['as' => 'preview.404']);
    $routes->get('419', 'HomeController::csrfPreview', ['as' => 'preview.419']);
    $routes->get('429', 'HomeController::rateLimitPreview', ['as' => 'preview.429']);
    $routes->get('500', 'HomeController::serverErrorPreview', ['as' => 'preview.500']);
    $routes->get('maintenance', 'HomeController::maintenancePreview', ['as' => 'preview.maintenance']);
});

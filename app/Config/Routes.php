<?php

declare(strict_types=1);

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
    $routes->get('/', 'HomeController::index', ['as' => 'landing.home']);
    $routes->get('tentang', 'PublicPageController::about', ['as' => 'landing.pages.about']);
    $routes->get('kontak', 'PublicPageController::contact', ['as' => 'landing.pages.contact']);
    $routes->post('kontak', 'PublicPageController::sendContact', ['as' => 'landing.pages.contact.send', 'filter' => 'rateLimit:contact']);
    $routes->get('privasi', 'PublicPageController::privacy', ['as' => 'landing.pages.privacy']);
    $routes->get('syarat-ketentuan', 'PublicPageController::terms', ['as' => 'landing.pages.terms']);
    $routes->get('aksesibilitas', 'PublicPageController::accessibility', ['as' => 'landing.pages.accessibility']);

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

    $routes->group('bunga-rampai', static function (RouteCollection $routes): void {
        $routes->get('/', 'BungaRampaiController::index', ['as' => 'landing.knowledge.index']);
        $routes->get('cari', 'BungaRampaiController::search', ['as' => 'landing.knowledge.search', 'filter' => 'rateLimit:public-search']);
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

    $routes->group('regulasi', static function (RouteCollection $routes): void {
        $routes->get('/', 'RegulationPublicController::index', ['as' => 'landing.regulations.index']);
        $routes->get('cari', 'RegulationPublicController::search', ['as' => 'landing.regulations.search', 'filter' => 'rateLimit:public-search']);
        $routes->get('kategori/(:segment)', 'RegulationPublicController::category/$1', ['as' => 'landing.regulations.category']);
        $routes->get('(:segment)/versi', 'RegulationPublicController::versions/$1', ['as' => 'landing.regulations.versions']);
        $routes->get('(:segment)/relasi', 'RegulationPublicController::relations/$1', ['as' => 'landing.regulations.relations']);
        $routes->get('(:segment)/preview', 'RegulationPublicController::preview/$1', ['as' => 'landing.regulations.preview']);
        $routes->get('(:segment)', 'RegulationPublicController::show/$1', ['as' => 'landing.regulations.show']);
    });

    $routes->group('berita', static function (RouteCollection $routes): void {
        $routes->get('/', 'NewsPublicController::index', ['as' => 'landing.news.index']);
        $routes->get('kategori/(:segment)', 'NewsPublicController::category/$1', ['as' => 'landing.news.category']);
        $routes->get('(:segment)', 'NewsPublicController::show/$1', ['as' => 'landing.news.show']);
    });

    $routes->group('event', static function (RouteCollection $routes): void {
        $routes->get('/', 'EventPublicController::index', ['as' => 'landing.events.index']);
        $routes->get('agenda', 'EventPublicController::agenda', ['as' => 'landing.events.agenda']);
        $routes->get('kalender', 'EventPublicController::calendar', ['as' => 'landing.events.calendar']);
        $routes->get('(:segment)', 'EventPublicController::show/$1', ['as' => 'landing.events.show']);
    });

    $routes->group('statistik', static function (RouteCollection $routes): void {
        $routes->get('/', 'StatisticPublicController::index', ['as' => 'landing.statistics.index']);
        $routes->get('data', 'StatisticPublicController::data', ['as' => 'landing.statistics.data', 'filter' => 'rateLimit:public-statistics']);
        $routes->get('pembelajaran', 'StatisticPublicController::learning', ['as' => 'landing.statistics.learning', 'filter' => 'rateLimit:public-statistics']);
        $routes->get('event', 'StatisticPublicController::events', ['as' => 'landing.statistics.events', 'filter' => 'rateLimit:public-statistics']);
        $routes->get('kompetensi', 'StatisticPublicController::competency', ['as' => 'landing.statistics.competency', 'filter' => 'rateLimit:public-statistics']);
    });

    $routes->group('sertifikat/verifikasi', static function (RouteCollection $routes): void {
        $routes->get('/', 'CertificateVerificationController::form', ['as' => 'landing.certificates.verify.form']);
        $routes->post('/', 'CertificateVerificationController::verify', ['as' => 'landing.certificates.verify.submit', 'filter' => 'rateLimit:certificate-verify']);
        $routes->get('(:segment)', 'CertificateVerificationController::verifyToken/$1', ['as' => 'landing.certificates.verify.token', 'filter' => 'rateLimit:certificate-verify']);
    });
});

/*
|--------------------------------------------------------------------------
| Auth Terpadu — WowDash
|--------------------------------------------------------------------------
*/
$routes->group('dashboard', ['namespace' => 'App\\Controllers\\Dashboard\\Auth'], static function (RouteCollection $routes): void {
    $routes->get('daftar/wilayah/options/(:segment)', '\\App\\Controllers\\Reference\\WilayahController::children/$1', ['as' => 'dashboard.auth.register.region.options']);
    $routes->get('masuk', 'LoginController::index', ['as' => 'dashboard.auth.login', 'filter' => 'guest']);
    $routes->post('masuk', 'LoginController::attempt', ['as' => 'dashboard.auth.login.attempt', 'filter' => ['guest', 'rateLimit:auth']]);
    $routes->post('keluar', 'LogoutController::logout', ['as' => 'dashboard.auth.logout', 'filter' => ['auth', 'auditContext']]);

    $routes->get('daftar', 'RegisterController::account', ['as' => 'dashboard.auth.register.account', 'filter' => 'guest']);
    $routes->post('daftar', 'RegisterController::storeAccount', ['as' => 'dashboard.auth.register.account.store', 'filter' => ['guest', 'rateLimit:registration']]);
    $routes->get('daftar/wilayah', 'RegisterController::region', ['as' => 'dashboard.auth.register.region', 'filter' => 'guest']);
    $routes->post('daftar/wilayah', 'RegisterController::storeRegion', ['as' => 'dashboard.auth.register.region.store', 'filter' => ['guest', 'rateLimit:registration']]);
    $routes->get('daftar/profil/(:alpha)', 'RegisterProfileController::form/$1', ['as' => 'dashboard.auth.register.profile', 'filter' => 'guest']);
    $routes->post('daftar/profil/(:alpha)', 'RegisterProfileController::store/$1', ['as' => 'dashboard.auth.register.profile.store', 'filter' => ['guest', 'rateLimit:registration']]);
    $routes->get('daftar/tinjau', 'RegisterController::review', ['as' => 'dashboard.auth.register.review', 'filter' => 'guest']);
    $routes->post('daftar/tinjau', 'RegisterController::submit', ['as' => 'dashboard.auth.register.submit', 'filter' => ['guest', 'rateLimit:registration']]);
    $routes->get('daftar/berhasil', 'RegisterController::success', ['as' => 'dashboard.auth.register.success', 'filter' => 'guest']);

    $routes->get('verifikasi/(:segment)', 'EmailVerificationController::verify/$1', ['as' => 'dashboard.auth.verify-email', 'filter' => 'rateLimit:email-verify']);
    $routes->post('verifikasi/kirim-ulang', 'EmailVerificationController::resend', ['as' => 'dashboard.auth.verify-email.resend', 'filter' => 'rateLimit:email-verify']);
    $routes->get('status-akun', 'AccountStatusController::show', ['as' => 'dashboard.auth.account-status', 'filter' => 'auth']);

    $routes->get('lupa-kata-sandi', 'PasswordController::forgotForm', ['as' => 'dashboard.auth.password.forgot', 'filter' => 'guest']);
    $routes->post('lupa-kata-sandi', 'PasswordController::sendReset', ['as' => 'dashboard.auth.password.send-reset', 'filter' => ['guest', 'rateLimit:password-reset']]);
    $routes->get('reset-kata-sandi/(:segment)', 'PasswordController::resetForm/$1', ['as' => 'dashboard.auth.password.reset', 'filter' => 'guest']);
    $routes->post('reset-kata-sandi/(:segment)', 'PasswordController::updatePassword/$1', ['as' => 'dashboard.auth.password.update', 'filter' => ['guest', 'rateLimit:password-reset']]);
});

/*
|--------------------------------------------------------------------------
| Workspace — FiStudy
|--------------------------------------------------------------------------
*/
$workspaceFilters = ['auth', 'verifiedEmail', 'accountStatus:active', 'workspaceAccess'];
$workspaceWriteFilters = ['auth', 'verifiedEmail', 'accountStatus:active', 'workspaceAccess', 'auditContext'];

$routes->group('workspace', ['namespace' => 'App\\Controllers\\Workspace', 'filter' => $workspaceFilters], static function (RouteCollection $routes) use ($workspaceWriteFilters): void {
    $routes->get('/', 'WorkspaceHomeController::index', ['as' => 'workspace.home']);

    $routes->group('profil', static function (RouteCollection $routes) use ($workspaceWriteFilters): void {
        $routes->get('/', 'ProfileController::index', ['as' => 'workspace.profile.index']);
        $routes->get('akun', 'ProfileController::account', ['as' => 'workspace.profile.account']);
        $routes->patch('akun', 'ProfileController::updateAccount', ['as' => 'workspace.profile.account.update', 'filter' => $workspaceWriteFilters]);
        $routes->get('asn', 'ProfileController::asn', ['as' => 'workspace.profile.asn']);
        $routes->get('penyedia', 'ProfileController::provider', ['as' => 'workspace.profile.provider']);
        $routes->get('akademik', 'ProfileController::academic', ['as' => 'workspace.profile.academic']);
        $routes->get('masyarakat', 'ProfileController::public', ['as' => 'workspace.profile.public']);
        $routes->get('peran-pengadaan', 'ProfileController::procurementRoles', ['as' => 'workspace.profile.procurement-roles']);
        $routes->get('perubahan', 'ProfileChangeRequestController::index', ['as' => 'workspace.profile.change-requests']);
        $routes->post('perubahan', 'ProfileChangeRequestController::store', ['as' => 'workspace.profile.change-requests.store', 'filter' => $workspaceWriteFilters]);
        $routes->get('pengajuan-role', 'RoleRequestController::index', ['as' => 'workspace.profile.role-requests']);
        $routes->post('pengajuan-role', 'RoleRequestController::store', ['as' => 'workspace.profile.role-requests.store', 'filter' => $workspaceWriteFilters]);
    });

    $routes->group('bookmark', static function (RouteCollection $routes) use ($workspaceWriteFilters): void {
        $routes->get('/', 'BookmarkController::index', ['as' => 'workspace.bookmarks.index']);
        $routes->get('pembelajaran', 'BookmarkController::learning', ['as' => 'workspace.bookmarks.learning']);
        $routes->get('bunga-rampai', 'BookmarkController::knowledge', ['as' => 'workspace.bookmarks.knowledge']);
        $routes->get('regulasi', 'BookmarkController::regulations', ['as' => 'workspace.bookmarks.regulations']);
        $routes->get('komunikasi', 'BookmarkController::communication', ['as' => 'workspace.bookmarks.communication']);
        $routes->get('sertifikat', 'BookmarkController::certificates', ['as' => 'workspace.bookmarks.certificates']);
        $routes->post('(:segment)/(:num)', 'BookmarkController::store/$1/$2', ['as' => 'workspace.bookmarks.store', 'filter' => $workspaceWriteFilters]);
        $routes->delete('(:segment)/(:num)', 'BookmarkController::delete/$1/$2', ['as' => 'workspace.bookmarks.delete', 'filter' => $workspaceWriteFilters]);
    });

    $routes->group('notifikasi', static function (RouteCollection $routes) use ($workspaceWriteFilters): void {
        $routes->get('/', 'NotificationController::index', ['as' => 'workspace.notifications.index']);
        $routes->get('(:num)', 'NotificationController::detail/$1', ['as' => 'workspace.notifications.detail']);
        $routes->patch('(:num)/dibaca', 'NotificationController::markRead/$1', ['as' => 'workspace.notifications.read', 'filter' => $workspaceWriteFilters]);
        $routes->post('baca-semua', 'NotificationController::markAllRead', ['as' => 'workspace.notifications.read-all', 'filter' => $workspaceWriteFilters]);
    });

    $routes->group('peserta', ['filter' => 'role:participant'], static function (RouteCollection $routes) use ($workspaceWriteFilters): void {
        $routes->get('/', 'ParticipantController::dashboard', ['as' => 'workspace.participant.dashboard']);
        $routes->get('enrollment', 'ParticipantController::enrollments', ['as' => 'workspace.participant.enrollments']);
        $routes->get('pembelajaran/(:num)', 'ParticipantController::learningDetail/$1', ['as' => 'workspace.participant.learning.show']);
        $routes->get('belajar/(:num)', 'ParticipantController::study/$1', ['as' => 'workspace.participant.study']);
        $routes->patch('belajar/(:num)/progres', '\\App\\Controllers\\Learning\\LearningProgressController::update/$1', ['as' => 'workspace.participant.progress.update', 'filter' => $workspaceWriteFilters]);
        $routes->get('pre-test/(:num)', '\\App\\Controllers\\Learning\\PreTestController::form/$1', ['as' => 'workspace.participant.pre-test']);
        $routes->post('pre-test/(:num)', '\\App\\Controllers\\Learning\\PreTestController::submit/$1', ['as' => 'workspace.participant.pre-test.submit', 'filter' => $workspaceWriteFilters]);
        $routes->get('pre-test/(:num)/hasil', '\\App\\Controllers\\Learning\\PreTestController::result/$1', ['as' => 'workspace.participant.pre-test.result']);
        $routes->get('quiz/(:num)', '\\App\\Controllers\\Learning\\QuizController::form/$1', ['as' => 'workspace.participant.quiz']);
        $routes->post('quiz/(:num)', '\\App\\Controllers\\Learning\\QuizController::submit/$1', ['as' => 'workspace.participant.quiz.submit', 'filter' => $workspaceWriteFilters]);
        $routes->get('quiz/(:num)/hasil', '\\App\\Controllers\\Learning\\QuizController::result/$1', ['as' => 'workspace.participant.quiz.result']);
        $routes->get('tugas/(:num)', '\\App\\Controllers\\Learning\\AssignmentController::show/$1', ['as' => 'workspace.participant.assignments.show']);
        $routes->post('tugas/(:num)', '\\App\\Controllers\\Learning\\AssignmentController::submit/$1', ['as' => 'workspace.participant.assignments.submit', 'filter' => $workspaceWriteFilters]);
        $routes->get('tugas/(:num)/pengumpulan', '\\App\\Controllers\\Learning\\AssignmentController::submission/$1', ['as' => 'workspace.participant.assignments.submission']);
        $routes->get('feedback/(:num)', '\\App\\Controllers\\Learning\\FeedbackController::form/$1', ['as' => 'workspace.participant.feedback']);
        $routes->post('feedback/(:num)', '\\App\\Controllers\\Learning\\FeedbackController::submit/$1', ['as' => 'workspace.participant.feedback.submit', 'filter' => $workspaceWriteFilters]);
        $routes->get('sertifikat', 'ParticipantController::certificates', ['as' => 'workspace.participant.certificates']);
        $routes->get('sertifikat/(:num)', 'ParticipantController::certificateDetail/$1', ['as' => 'workspace.participant.certificates.show']);
    });

    $routes->group('pelatih', ['filter' => 'role:trainer'], static function (RouteCollection $routes) use ($workspaceWriteFilters): void {
        $routes->get('/', 'TrainerController::dashboard', ['as' => 'workspace.trainer.dashboard']);
        $routes->get('kelas', 'TrainerController::classes', ['as' => 'workspace.trainer.classes.index']);
        $routes->get('kelas/(:num)', 'TrainerController::classDetail/$1', ['as' => 'workspace.trainer.classes.show']);
        $routes->get('kelas/(:num)/materi/baru', '\\App\\Controllers\\Learning\\LessonController::create/$1', ['as' => 'workspace.trainer.lessons.create', 'filter' => 'permission:learning.lesson.manage']);
        $routes->post('kelas/(:num)/materi', '\\App\\Controllers\\Learning\\LessonController::store/$1', ['as' => 'workspace.trainer.lessons.store', 'filter' => array_merge(['permission:learning.lesson.manage'], $workspaceWriteFilters)]);
        $routes->get('kelas/(:num)/quiz/baru', '\\App\\Controllers\\Learning\\QuizController::create/$1', ['as' => 'workspace.trainer.quizzes.create', 'filter' => 'permission:learning.quiz.manage']);
        $routes->post('kelas/(:num)/quiz', '\\App\\Controllers\\Learning\\QuizController::store/$1', ['as' => 'workspace.trainer.quizzes.store', 'filter' => array_merge(['permission:learning.quiz.manage'], $workspaceWriteFilters)]);
        $routes->get('kelas/(:num)/tugas/baru', '\\App\\Controllers\\Learning\\AssignmentController::create/$1', ['as' => 'workspace.trainer.assignments.create', 'filter' => 'permission:learning.assignment.manage']);
        $routes->post('kelas/(:num)/tugas', '\\App\\Controllers\\Learning\\AssignmentController::store/$1', ['as' => 'workspace.trainer.assignments.store', 'filter' => array_merge(['permission:learning.assignment.manage'], $workspaceWriteFilters)]);
        $routes->get('kelas/(:num)/progres-peserta', 'TrainerController::participantProgress/$1', ['as' => 'workspace.trainer.participants.progress']);
    });

    $routes->group('evaluator', ['filter' => 'role:evaluator'], static function (RouteCollection $routes) use ($workspaceWriteFilters): void {
        $routes->get('/', 'EvaluatorController::dashboard', ['as' => 'workspace.evaluator.dashboard']);
        $routes->get('antrian-penilaian', 'EvaluatorController::assignmentQueue', ['as' => 'workspace.evaluator.assignments.queue']);
        $routes->get('pengumpulan/(:num)', '\\App\\Controllers\\Learning\\GradeController::form/$1', ['as' => 'workspace.evaluator.assignments.grade']);
        $routes->post('pengumpulan/(:num)/nilai', '\\App\\Controllers\\Learning\\GradeController::store/$1', ['as' => 'workspace.evaluator.assignments.grade.store', 'filter' => array_merge(['permission:learning.assignment.grade'], $workspaceWriteFilters)]);
        $routes->get('riwayat-penilaian', 'EvaluatorController::assessmentHistory', ['as' => 'workspace.evaluator.assessments.history']);
    });

    $routes->group('operator', ['filter' => 'role:operator'], static function (RouteCollection $routes) use ($workspaceWriteFilters): void {
        $routes->get('/', 'OperatorController::dashboard', ['as' => 'workspace.operator.dashboard']);
        $routes->get('antrian-verifikasi', 'OperatorController::verificationQueue', ['as' => 'workspace.operator.verification.queue', 'filter' => 'permission:profile.verify']);
        $routes->post('verifikasi/(:num)', 'OperatorController::decideVerification/$1', ['as' => 'workspace.operator.verification.decide', 'filter' => array_merge(['permission:profile.verify'], $workspaceWriteFilters)]);
        $routes->get('enrollment', 'OperatorController::enrollmentManagement', ['as' => 'workspace.operator.enrollments', 'filter' => 'permission:learning.enrollment.manage']);
        $routes->get('absensi', 'OperatorController::attendanceManagement', ['as' => 'workspace.operator.attendance', 'filter' => 'permission:event.attendance.manage']);
        $routes->get('approval-terdelegasi', 'OperatorController::delegatedApprovals', ['as' => 'workspace.operator.approvals']);
    });
});

/*
|--------------------------------------------------------------------------
| Dashboard Back-office — WowDash
|--------------------------------------------------------------------------
*/
$dashboardFilters = ['auth', 'verifiedEmail', 'accountStatus:active', 'dashboardAccess'];
$dashboardWriteFilters = ['auth', 'verifiedEmail', 'accountStatus:active', 'dashboardAccess', 'auditContext'];

$routes->group('dashboard', ['namespace' => 'App\\Controllers\\Dashboard', 'filter' => $dashboardFilters], static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
    $routes->get('/', 'DashboardController::index', ['as' => 'dashboard.home', 'filter' => 'permission:dashboard.view']);
    $routes->get('kpi-internal', 'DashboardController::internalKpi', ['as' => 'dashboard.summary.kpi', 'filter' => 'permission:dashboard.view']);
    $routes->get('antrean-persetujuan', 'DashboardController::approvalQueue', ['as' => 'dashboard.summary.approval-queue', 'filter' => 'permission:approval.view']);
    $routes->get('aktivitas-terbaru', 'DashboardController::recentActivity', ['as' => 'dashboard.summary.recent-activity', 'filter' => 'permission:dashboard.view']);
    $routes->get('indikator-sistem', 'DashboardController::systemIndicators', ['as' => 'dashboard.summary.system-indicators', 'filter' => 'permission:system.health.view']);

    $routes->group('profil', static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('/', 'ProfileController::index', ['as' => 'dashboard.profile.index']);
        $routes->get('ubah', 'ProfileController::edit', ['as' => 'dashboard.profile.edit']);
        $routes->patch('/', 'ProfileController::update', ['as' => 'dashboard.profile.update', 'filter' => $dashboardWriteFilters]);
        $routes->get('kata-sandi', 'ProfileController::password', ['as' => 'dashboard.profile.password']);
        $routes->patch('kata-sandi', 'ProfileController::updatePassword', ['as' => 'dashboard.profile.password.update', 'filter' => $dashboardWriteFilters]);
        $routes->get('preferensi', 'ProfileController::preferences', ['as' => 'dashboard.profile.preferences']);
        $routes->patch('preferensi', 'ProfileController::updatePreferences', ['as' => 'dashboard.profile.preferences.update', 'filter' => $dashboardWriteFilters]);
    });

    $routes->group('pengguna', static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('/', 'UserController::index', ['as' => 'dashboard.users.index', 'filter' => 'permission:user.view']);
        $routes->get('baru', 'UserController::create', ['as' => 'dashboard.users.create', 'filter' => 'permission:user.create']);
        $routes->post('/', 'UserController::store', ['as' => 'dashboard.users.store', 'filter' => array_merge(['permission:user.create'], $dashboardWriteFilters)]);
        $routes->get('verifikasi-profil', 'UserProfileVerificationController::index', ['as' => 'dashboard.users.profile-verification-queue', 'filter' => 'permission:profile.verify']);
        $routes->post('(:num)/verifikasi-profil', 'UserProfileVerificationController::decide/$1', ['as' => 'dashboard.users.profile-verification.decide', 'filter' => array_merge(['permission:profile.verify'], $dashboardWriteFilters)]);
        $routes->get('perubahan-profil', 'ProfileChangeRequestReviewController::index', ['as' => 'dashboard.users.profile-change-requests.index', 'filter' => 'permission:profile.verify']);
        $routes->get('perubahan-profil/(:num)', 'ProfileChangeRequestReviewController::show/$1', ['as' => 'dashboard.users.profile-change-requests.show', 'filter' => 'permission:profile.verify']);
        $routes->post('perubahan-profil/(:num)', 'ProfileChangeRequestReviewController::decide/$1', ['as' => 'dashboard.users.profile-change-requests.decide', 'filter' => array_merge(['permission:profile.verify'], $dashboardWriteFilters)]);
        $routes->get('pengajuan-role', 'RoleRequestReviewController::index', ['as' => 'dashboard.role-requests.index', 'filter' => 'permission:role.request.review']);
        $routes->get('pengajuan-role/(:num)', 'RoleRequestReviewController::show/$1', ['as' => 'dashboard.role-requests.show', 'filter' => 'permission:role.request.review']);
        $routes->post('pengajuan-role/(:num)', 'RoleRequestReviewController::decide/$1', ['as' => 'dashboard.role-requests.decide', 'filter' => array_merge(['permission:role.request.review'], $dashboardWriteFilters)]);
        $routes->get('(:num)', 'UserController::show/$1', ['as' => 'dashboard.users.show', 'filter' => 'permission:user.view']);
        $routes->get('(:num)/ubah', 'UserController::edit/$1', ['as' => 'dashboard.users.edit', 'filter' => 'permission:user.update']);
        $routes->patch('(:num)', 'UserController::update/$1', ['as' => 'dashboard.users.update', 'filter' => array_merge(['permission:user.update'], $dashboardWriteFilters)]);
    });

    $routes->group('rbac', static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('role', 'RoleController::index', ['as' => 'dashboard.roles.index', 'filter' => 'permission:role.view']);
        $routes->get('role/baru', 'RoleController::create', ['as' => 'dashboard.roles.create', 'filter' => 'permission:role.create']);
        $routes->post('role', 'RoleController::store', ['as' => 'dashboard.roles.store', 'filter' => array_merge(['permission:role.create'], $dashboardWriteFilters)]);
        $routes->get('role/(:num)/ubah', 'RoleController::edit/$1', ['as' => 'dashboard.roles.edit', 'filter' => 'permission:role.update']);
        $routes->patch('role/(:num)', 'RoleController::update/$1', ['as' => 'dashboard.roles.update', 'filter' => array_merge(['permission:role.update'], $dashboardWriteFilters)]);
        $routes->get('permission', 'PermissionController::index', ['as' => 'dashboard.permissions.index', 'filter' => 'permission:permission.view']);
        $routes->get('permission/baru', 'PermissionController::create', ['as' => 'dashboard.permissions.create', 'filter' => 'permission:permission.create']);
        $routes->post('permission', 'PermissionController::store', ['as' => 'dashboard.permissions.store', 'filter' => array_merge(['permission:permission.create'], $dashboardWriteFilters)]);
        $routes->get('permission/(:num)/ubah', 'PermissionController::edit/$1', ['as' => 'dashboard.permissions.edit', 'filter' => 'permission:permission.update']);
        $routes->patch('permission/(:num)', 'PermissionController::update/$1', ['as' => 'dashboard.permissions.update', 'filter' => array_merge(['permission:permission.update'], $dashboardWriteFilters)]);
        $routes->get('matrix', 'RoleController::permissionMatrix', ['as' => 'dashboard.roles.permission-matrix', 'filter' => 'permission:permission.assign']);
        $routes->patch('matrix', 'RoleController::syncPermissionMatrix', ['as' => 'dashboard.roles.permission-matrix.sync', 'filter' => array_merge(['permission:permission.assign'], $dashboardWriteFilters)]);
    });

    $routes->group('organisasi', static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('/', 'OrganizationController::index', ['as' => 'dashboard.organizations.index', 'filter' => 'permission:organization.view']);
        $routes->get('baru', 'OrganizationController::create', ['as' => 'dashboard.organizations.create', 'filter' => 'permission:organization.create']);
        $routes->post('/', 'OrganizationController::store', ['as' => 'dashboard.organizations.store', 'filter' => array_merge(['permission:organization.create'], $dashboardWriteFilters)]);
        $routes->get('(:num)/ubah', 'OrganizationController::edit/$1', ['as' => 'dashboard.organizations.edit', 'filter' => 'permission:organization.update']);
        $routes->patch('(:num)', 'OrganizationController::update/$1', ['as' => 'dashboard.organizations.update', 'filter' => array_merge(['permission:organization.update'], $dashboardWriteFilters)]);
    });

    $routes->group('peran-pengadaan', static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('/', 'ProcurementRoleController::index', ['as' => 'dashboard.procurement-roles.index', 'filter' => 'permission:organization.view']);
        $routes->get('baru', 'ProcurementRoleController::create', ['as' => 'dashboard.procurement-roles.create', 'filter' => 'permission:organization.create']);
        $routes->post('/', 'ProcurementRoleController::store', ['as' => 'dashboard.procurement-roles.store', 'filter' => array_merge(['permission:organization.create'], $dashboardWriteFilters)]);
        $routes->get('(:num)/ubah', 'ProcurementRoleController::edit/$1', ['as' => 'dashboard.procurement-roles.edit', 'filter' => 'permission:organization.update']);
        $routes->patch('(:num)', 'ProcurementRoleController::update/$1', ['as' => 'dashboard.procurement-roles.update', 'filter' => array_merge(['permission:organization.update'], $dashboardWriteFilters)]);
    });

    $routes->group('approval', static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('/', 'ApprovalController::index', ['as' => 'dashboard.approvals.index', 'filter' => 'permission:approval.view']);
        $routes->get('riwayat', 'ApprovalController::history', ['as' => 'dashboard.approvals.history', 'filter' => 'permission:approval.view']);
        $routes->get('(:num)', 'ApprovalController::show/$1', ['as' => 'dashboard.approvals.show', 'filter' => 'permission:approval.view']);
        $routes->post('(:num)/setujui', 'ApprovalController::approve/$1', ['as' => 'dashboard.approvals.approve', 'filter' => array_merge(['permission:approval.decide'], $dashboardWriteFilters)]);
        $routes->post('(:num)/tolak', 'ApprovalController::reject/$1', ['as' => 'dashboard.approvals.reject', 'filter' => array_merge(['permission:approval.decide'], $dashboardWriteFilters)]);
    });

    $routes->group('audit', static function (RouteCollection $routes): void {
        $routes->get('/', 'AuditController::index', ['as' => 'dashboard.audits.index', 'filter' => 'permission:audit.view']);
        $routes->get('(:num)', 'AuditController::show/$1', ['as' => 'dashboard.audits.show', 'filter' => 'permission:audit.view']);
    });

    $routes->group('pembelajaran', ['namespace' => 'App\\Controllers\\Learning'], static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('program', 'ProgramController::index', ['as' => 'dashboard.learning.programs.index', 'filter' => 'permission:learning.program.view']);
        $routes->get('program/baru', 'ProgramController::create', ['as' => 'dashboard.learning.programs.create', 'filter' => 'permission:learning.program.create']);
        $routes->post('program', 'ProgramController::store', ['as' => 'dashboard.learning.programs.store', 'filter' => array_merge(['permission:learning.program.create'], $dashboardWriteFilters)]);
        $routes->get('program/(:num)/ubah', 'ProgramController::edit/$1', ['as' => 'dashboard.learning.programs.edit', 'filter' => 'permission:learning.program.update']);
        $routes->patch('program/(:num)', 'ProgramController::update/$1', ['as' => 'dashboard.learning.programs.update', 'filter' => array_merge(['permission:learning.program.update'], $dashboardWriteFilters)]);
        $routes->get('course', 'CourseController::index', ['as' => 'dashboard.learning.courses.index', 'filter' => 'permission:learning.course.view']);
        $routes->get('course/baru', 'CourseController::create', ['as' => 'dashboard.learning.courses.create', 'filter' => 'permission:learning.course.create']);
        $routes->post('course', 'CourseController::store', ['as' => 'dashboard.learning.courses.store', 'filter' => array_merge(['permission:learning.course.create'], $dashboardWriteFilters)]);
        $routes->get('course/(:num)/ubah', 'CourseController::edit/$1', ['as' => 'dashboard.learning.courses.edit', 'filter' => 'permission:learning.course.update']);
        $routes->patch('course/(:num)', 'CourseController::update/$1', ['as' => 'dashboard.learning.courses.update', 'filter' => array_merge(['permission:learning.course.update'], $dashboardWriteFilters)]);
        $routes->get('kelas', 'LearningClassController::index', ['as' => 'dashboard.learning.classes.index', 'filter' => 'permission:learning.class.view']);
        $routes->get('kelas/baru', 'LearningClassController::create', ['as' => 'dashboard.learning.classes.create', 'filter' => 'permission:learning.class.create']);
        $routes->post('kelas', 'LearningClassController::store', ['as' => 'dashboard.learning.classes.store', 'filter' => array_merge(['permission:learning.class.create'], $dashboardWriteFilters)]);
        $routes->get('kelas/(:num)/ubah', 'LearningClassController::edit/$1', ['as' => 'dashboard.learning.classes.edit', 'filter' => 'permission:learning.class.update']);
        $routes->patch('kelas/(:num)', 'LearningClassController::update/$1', ['as' => 'dashboard.learning.classes.update', 'filter' => array_merge(['permission:learning.class.update'], $dashboardWriteFilters)]);
        $routes->get('modul', 'ModuleController::index', ['as' => 'dashboard.learning.modules.index', 'filter' => 'permission:learning.module.view']);
        $routes->get('materi', 'LessonController::index', ['as' => 'dashboard.learning.lessons.index', 'filter' => 'permission:learning.lesson.view']);
        $routes->get('enrollment', 'EnrollmentController::index', ['as' => 'dashboard.learning.enrollments.index', 'filter' => 'permission:learning.enrollment.view']);
        $routes->get('nilai', 'GradeController::index', ['as' => 'dashboard.learning.grades.index', 'filter' => 'permission:learning.grade.view']);
        $routes->get('laporan', 'LearningClassController::reports', ['as' => 'dashboard.learning.reports.index', 'filter' => 'permission:learning.report.view']);
    });

    $routes->group('bunga-rampai', ['namespace' => 'App\\Controllers\\Knowledge'], static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('artikel', 'KnowledgeArticleController::index', ['as' => 'dashboard.knowledge.articles.index', 'filter' => 'permission:knowledge.article.view']);
        $routes->get('artikel/baru', 'KnowledgeArticleController::create', ['as' => 'dashboard.knowledge.articles.create', 'filter' => 'permission:knowledge.article.create']);
        $routes->post('artikel', 'KnowledgeArticleController::store', ['as' => 'dashboard.knowledge.articles.store', 'filter' => array_merge(['permission:knowledge.article.create'], $dashboardWriteFilters)]);
        $routes->get('artikel/(:num)/ubah', 'KnowledgeArticleController::edit/$1', ['as' => 'dashboard.knowledge.articles.edit', 'filter' => 'permission:knowledge.article.update']);
        $routes->patch('artikel/(:num)', 'KnowledgeArticleController::update/$1', ['as' => 'dashboard.knowledge.articles.update', 'filter' => array_merge(['permission:knowledge.article.update'], $dashboardWriteFilters)]);
        $routes->get('faq', 'KnowledgeFaqController::index', ['as' => 'dashboard.knowledge.faq.index', 'filter' => 'permission:knowledge.faq.view']);
        $routes->get('kategori', 'KnowledgeCategoryController::index', ['as' => 'dashboard.knowledge.categories.index', 'filter' => 'permission:knowledge.category.view']);
        $routes->get('media', 'KnowledgeMediaController::index', ['as' => 'dashboard.knowledge.media.index', 'filter' => 'permission:knowledge.media.view']);
        $routes->get('antrian-publikasi', 'KnowledgeArticleController::publicationQueue', ['as' => 'dashboard.knowledge.publication-queue', 'filter' => 'permission:knowledge.publish.review']);
    });

    $routes->group('regulasi', ['namespace' => 'App\\Controllers\\Regulation'], static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('/', 'RegulationController::index', ['as' => 'dashboard.regulations.index', 'filter' => 'permission:regulation.view']);
        $routes->get('baru', 'RegulationController::create', ['as' => 'dashboard.regulations.create', 'filter' => 'permission:regulation.create']);
        $routes->post('/', 'RegulationController::store', ['as' => 'dashboard.regulations.store', 'filter' => array_merge(['permission:regulation.create'], $dashboardWriteFilters)]);
        $routes->get('(:num)/ubah', 'RegulationController::edit/$1', ['as' => 'dashboard.regulations.edit', 'filter' => 'permission:regulation.update']);
        $routes->patch('(:num)', 'RegulationController::update/$1', ['as' => 'dashboard.regulations.update', 'filter' => array_merge(['permission:regulation.update'], $dashboardWriteFilters)]);
        $routes->get('kategori', 'RegulationCategoryController::index', ['as' => 'dashboard.regulations.categories.index', 'filter' => 'permission:regulation.view']);
        $routes->get('versi', 'RegulationVersionController::index', ['as' => 'dashboard.regulations.versions.index', 'filter' => 'permission:regulation.version.view']);
        $routes->get('relasi', 'RegulationRelationController::index', ['as' => 'dashboard.regulations.relations.index', 'filter' => 'permission:regulation.relation.view']);
        $routes->get('antrian-publikasi', 'RegulationController::publicationQueue', ['as' => 'dashboard.regulations.publication-queue', 'filter' => 'permission:regulation.publish.review']);
    });

    $routes->group('komunikasi', ['namespace' => 'App\\Controllers\\Communication'], static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('berita', 'NewsController::index', ['as' => 'dashboard.communication.news.index', 'filter' => 'permission:news.view']);
        $routes->get('berita/baru', 'NewsController::create', ['as' => 'dashboard.communication.news.create', 'filter' => 'permission:news.create']);
        $routes->post('berita', 'NewsController::store', ['as' => 'dashboard.communication.news.store', 'filter' => array_merge(['permission:news.create'], $dashboardWriteFilters)]);
        $routes->get('berita/(:num)/ubah', 'NewsController::edit/$1', ['as' => 'dashboard.communication.news.edit', 'filter' => 'permission:news.update']);
        $routes->patch('berita/(:num)', 'NewsController::update/$1', ['as' => 'dashboard.communication.news.update', 'filter' => array_merge(['permission:news.update'], $dashboardWriteFilters)]);
        $routes->get('event', 'EventController::index', ['as' => 'dashboard.communication.events.index', 'filter' => 'permission:event.view']);
        $routes->get('event/baru', 'EventController::create', ['as' => 'dashboard.communication.events.create', 'filter' => 'permission:event.create']);
        $routes->post('event', 'EventController::store', ['as' => 'dashboard.communication.events.store', 'filter' => array_merge(['permission:event.create'], $dashboardWriteFilters)]);
        $routes->get('event/(:num)/ubah', 'EventController::edit/$1', ['as' => 'dashboard.communication.events.edit', 'filter' => 'permission:event.update']);
        $routes->patch('event/(:num)', 'EventController::update/$1', ['as' => 'dashboard.communication.events.update', 'filter' => array_merge(['permission:event.update'], $dashboardWriteFilters)]);
        $routes->get('event/(:num)/registrasi', 'EventRegistrationController::index/$1', ['as' => 'dashboard.communication.event-registrations.index', 'filter' => 'permission:event.registration.view']);
        $routes->get('event/(:num)/absensi', 'AttendanceController::index/$1', ['as' => 'dashboard.communication.attendance.index', 'filter' => 'permission:event.attendance.view']);
        $routes->post('event/(:num)/absensi', 'AttendanceController::store/$1', ['as' => 'dashboard.communication.attendance.store', 'filter' => array_merge(['permission:event.attendance.manage'], $dashboardWriteFilters)]);
    });

    $routes->group('sertifikat', ['namespace' => 'App\\Controllers\\Certificate'], static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('/', 'CertificateController::index', ['as' => 'dashboard.certificates.index', 'filter' => 'permission:certificate.view']);
        $routes->get('persetujuan', 'CertificateApprovalController::index', ['as' => 'dashboard.certificates.approval-queue', 'filter' => 'permission:certificate.approve']);
        $routes->post('(:num)/setujui', 'CertificateApprovalController::approve/$1', ['as' => 'dashboard.certificates.approve', 'filter' => array_merge(['permission:certificate.approve'], $dashboardWriteFilters)]);
        $routes->post('(:num)/cabut', 'CertificateController::revoke/$1', ['as' => 'dashboard.certificates.revoke', 'filter' => array_merge(['permission:certificate.approve'], $dashboardWriteFilters)]);
        $routes->get('(:num)', 'CertificateController::show/$1', ['as' => 'dashboard.certificates.show', 'filter' => 'permission:certificate.view']);
        $routes->get('template', 'CertificateTemplateController::index', ['as' => 'dashboard.certificates.templates.index', 'filter' => 'permission:certificate.template.view']);
        $routes->get('template/baru', 'CertificateTemplateController::create', ['as' => 'dashboard.certificates.templates.create', 'filter' => 'permission:certificate.template.create']);
        $routes->post('template', 'CertificateTemplateController::store', ['as' => 'dashboard.certificates.templates.store', 'filter' => array_merge(['permission:certificate.template.create'], $dashboardWriteFilters)]);
        $routes->get('template/(:num)/ubah', 'CertificateTemplateController::edit/$1', ['as' => 'dashboard.certificates.templates.edit', 'filter' => 'permission:certificate.template.create']);
        $routes->patch('template/(:num)', 'CertificateTemplateController::update/$1', ['as' => 'dashboard.certificates.templates.update', 'filter' => array_merge(['permission:certificate.template.create'], $dashboardWriteFilters)]);
    });

    $routes->group('inteligensi', ['namespace' => 'App\\Controllers\\Intelligence'], static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('/', 'IntelligenceDashboardController::index', ['as' => 'dashboard.intelligence.index', 'filter' => 'permission:intelligence.view']);
        $routes->get('organisasi', 'OpdController::index', ['as' => 'dashboard.intelligence.organizations.index', 'filter' => 'permission:intelligence.organization.view']);
        $routes->get('jf-ppbj', 'JfPpbjController::index', ['as' => 'dashboard.intelligence.jf-ppbj.index', 'filter' => 'permission:intelligence.jf_ppbj.view']);
        $routes->get('sertifikat-kompetensi', 'CompetencyCertificateController::index', ['as' => 'dashboard.intelligence.competency-certificates.index', 'filter' => 'permission:intelligence.certificate.view']);
        $routes->get('gap-kompetensi', 'IntelligenceDashboardController::competencyGaps', ['as' => 'dashboard.intelligence.competency-gaps.index', 'filter' => 'permission:intelligence.gap.view']);
        $routes->post('ekspor', '\\App\\Controllers\\Shared\\ExportController::request', ['as' => 'dashboard.intelligence.exports.request', 'filter' => array_merge(['permission:intelligence.export'], $dashboardWriteFilters)]);
    });

    $routes->group('laporan', static function (RouteCollection $routes): void {
        $routes->get('/', 'InternalReportController::index', ['as' => 'dashboard.reports.index', 'filter' => 'permission:report.view']);
        $routes->get('statistik-internal', 'InternalReportController::statistics', ['as' => 'dashboard.reports.internal-statistics', 'filter' => 'permission:report.statistics.view']);
    });

    $routes->group('pengaturan', static function (RouteCollection $routes) use ($dashboardWriteFilters): void {
        $routes->get('/', 'SettingController::index', ['as' => 'dashboard.settings.index', 'filter' => 'permission:setting.view']);
        $routes->get('aplikasi', 'SettingController::application', ['as' => 'dashboard.settings.application', 'filter' => 'permission:setting.view']);
        $routes->patch('aplikasi', 'SettingController::updateApplication', ['as' => 'dashboard.settings.application.update', 'filter' => array_merge(['permission:setting.application.update'], $dashboardWriteFilters)]);
        $routes->get('notifikasi', 'SettingController::notification', ['as' => 'dashboard.settings.notification', 'filter' => 'permission:setting.view']);
        $routes->patch('notifikasi', 'SettingController::updateNotification', ['as' => 'dashboard.settings.notification.update', 'filter' => array_merge(['permission:setting.notification.update'], $dashboardWriteFilters)]);
        $routes->get('penyimpanan', 'SettingController::storage', ['as' => 'dashboard.settings.storage', 'filter' => 'permission:setting.view']);
        $routes->patch('penyimpanan', 'SettingController::updateStorage', ['as' => 'dashboard.settings.storage.update', 'filter' => array_merge(['permission:setting.storage.update'], $dashboardWriteFilters)]);
        $routes->get('keamanan', 'SettingController::security', ['as' => 'dashboard.settings.security', 'filter' => 'permission:setting.view']);
        $routes->patch('keamanan', 'SettingController::updateSecurity', ['as' => 'dashboard.settings.security.update', 'filter' => array_merge(['permission:setting.security.update'], $dashboardWriteFilters)]);
        $routes->get('kesehatan-sistem', 'SystemHealthController::index', ['as' => 'dashboard.system-health.index', 'filter' => 'permission:system.health.view']);
    });
});

/*
|--------------------------------------------------------------------------
| Shared File dan Export
|--------------------------------------------------------------------------
*/
$routes->group('file', ['namespace' => 'App\\Controllers\\Shared', 'filter' => ['auth', 'accountStatus:active']], static function (RouteCollection $routes): void {
    $routes->post('unggah', 'FileController::upload', ['as' => 'shared.file.upload', 'filter' => ['permission:file.upload', 'auditContext']]);
    $routes->get('(:num)/preview', 'FileController::preview/$1', ['as' => 'shared.file.preview', 'filter' => 'permission:file.preview']);
    $routes->get('(:num)/unduh', 'FileController::download/$1', ['as' => 'shared.file.download', 'filter' => 'permission:file.download']);
});

$routes->group('ekspor', ['namespace' => 'App\\Controllers\\Shared', 'filter' => ['auth', 'accountStatus:active']], static function (RouteCollection $routes): void {
    $routes->post('/', 'ExportController::request', ['as' => 'shared.export.request', 'filter' => 'auditContext']);
    $routes->get('/', 'ExportController::index', ['as' => 'shared.export.index']);
    $routes->get('(:num)/unduh', 'ExportController::download/$1', ['as' => 'shared.export.download', 'filter' => 'permission:export.download']);
});

<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('', ['namespace' => 'App\Controllers\Landing'], static function ($routes) {
    $routes->get('/', 'HomeController::index', ['as' => 'public.home']);
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
    });

    $routes->group('statistik', static function ($routes) {
        $routes->get('/', 'StatisticPublicController::index', ['as' => 'public.statistics.index']);
        $routes->get('pembelajaran', 'StatisticPublicController::learning', ['as' => 'public.statistics.learning']);
        $routes->get('event', 'StatisticPublicController::events', ['as' => 'public.statistics.events']);
        $routes->get('kompetensi', 'StatisticPublicController::competency', ['as' => 'public.statistics.competency']);
    });

    $routes->group('verifikasi-sertifikat', static function ($routes) {
        $routes->get('/', 'CertificateVerificationController::index', ['as' => 'public.certificate.verify']);
        $routes->get('hasil', 'CertificateVerificationController::result', ['as' => 'public.certificate.verify.result']);
        $routes->get('(:segment)', 'CertificateVerificationController::detail/$1', ['as' => 'public.certificate.verify.detail']);
    });
});

$routes->group('', ['namespace' => 'App\Controllers\Auth'], static function ($routes) {
    // $routes->get('masuk', 'LoginController::index', ['as' => 'auth.login']);
    // $routes->get('keluar', 'LoginController::logoutPreview', ['as' => 'auth.logout.preview']);

    $routes->group('daftar', static function ($routes) {
        $routes->get('/', 'RegisterController::index', ['as' => 'auth.register.account']);
        $routes->get('tipe-profil', 'RegisterProfileController::type', ['as' => 'auth.register.profile-type']);
        $routes->get('profil/asn', 'RegisterProfileController::asn', ['as' => 'auth.register.profile.asn']);
        $routes->get('profil/penyedia', 'RegisterProfileController::provider', ['as' => 'auth.register.profile.provider']);
        $routes->get('profil/akademisi', 'RegisterProfileController::academic', ['as' => 'auth.register.profile.academic']);
        $routes->get('profil/masyarakat', 'RegisterProfileController::public', ['as' => 'auth.register.profile.public']);
        $routes->get('tinjau', 'RegisterController::review', ['as' => 'auth.register.review']);
        $routes->get('berhasil', 'RegisterController::success', ['as' => 'auth.register.success']);
    });

    $routes->group('verifikasi-email', static function ($routes) {
        $routes->get('/', 'EmailVerificationController::index', ['as' => 'auth.email-verification']);
        $routes->get('terkirim', 'EmailVerificationController::sent', ['as' => 'auth.email-verification.sent']);
        $routes->get('berhasil', 'EmailVerificationController::success', ['as' => 'auth.email-verification.success']);
    });

    $routes->get('menunggu-verifikasi', 'RegisterController::pendingVerification', ['as' => 'auth.pending-verification']);
    $routes->get('perlu-revisi', 'RegisterController::revisionRequired', ['as' => 'auth.revision-required']);

    $routes->group('kata-sandi', static function ($routes) {
        $routes->get('lupa', 'PasswordController::forgot', ['as' => 'auth.password.forgot']);
        $routes->get('reset', 'PasswordController::reset', ['as' => 'auth.password.reset']);
        $routes->get('ubah', 'PasswordController::change', ['as' => 'auth.password.change']);
        $routes->get('berhasil', 'PasswordController::success', ['as' => 'auth.password.success']);
    });
});

$routes->group('workspace', ['namespace' => 'App\Controllers\Workspace'], static function ($routes) {
    $routes->get('/', 'WorkspaceHomeController::index', ['as' => 'workspace.home']);
    $routes->get('notifikasi', 'NotificationController::index', ['as' => 'workspace.notifications']);
    $routes->get('notifikasi/(:segment)', 'NotificationController::detail/$1', ['as' => 'workspace.notifications.detail']);

    $routes->group('bookmark', static function ($routes) {
        $routes->get('/', 'BookmarkController::index', ['as' => 'workspace.bookmarks.index']);
        $routes->get('pembelajaran', 'BookmarkController::learning', ['as' => 'workspace.bookmarks.learning']);
        $routes->get('bunga-rampai', 'BookmarkController::knowledge', ['as' => 'workspace.bookmarks.knowledge']);
        $routes->get('regulasi', 'BookmarkController::regulations', ['as' => 'workspace.bookmarks.regulations']);
        $routes->get('komunikasi', 'BookmarkController::communication', ['as' => 'workspace.bookmarks.communication']);
        $routes->get('sertifikat', 'BookmarkController::certificates', ['as' => 'workspace.bookmarks.certificates']);
    });

    $routes->group('peserta', static function ($routes) {
        $routes->get('/', 'ParticipantController::index', ['as' => 'workspace.participant.home']);
        $routes->get('kelas-saya', 'ParticipantController::myClasses', ['as' => 'workspace.participant.classes']);
        $routes->get('kelas/(:segment)', 'ParticipantController::classDetail/$1', ['as' => 'workspace.participant.class.detail']);
        $routes->get('kelas/(:segment)/belajar', 'ParticipantController::study/$1', ['as' => 'workspace.participant.study']);
        $routes->get('kelas/(:segment)/progres', 'ParticipantController::progress/$1', ['as' => 'workspace.participant.progress']);
        $routes->get('kelas/(:segment)/pre-test', 'ParticipantController::preTest/$1', ['as' => 'workspace.participant.pre-test']);
        $routes->get('kelas/(:segment)/pre-test/hasil', 'ParticipantController::preTestResult/$1', ['as' => 'workspace.participant.pre-test.result']);
        $routes->get('kelas/(:segment)/quiz', 'ParticipantController::quiz/$1', ['as' => 'workspace.participant.quiz']);
        $routes->get('kelas/(:segment)/quiz/hasil', 'ParticipantController::quizResult/$1', ['as' => 'workspace.participant.quiz.result']);
        $routes->get('kelas/(:segment)/tugas', 'ParticipantController::assignments/$1', ['as' => 'workspace.participant.assignments']);
        $routes->get('kelas/(:segment)/tugas/kirim', 'ParticipantController::assignmentSubmission/$1', ['as' => 'workspace.participant.assignment.submission']);
        $routes->get('kelas/(:segment)/tugas/revisi', 'ParticipantController::assignmentRevision/$1', ['as' => 'workspace.participant.assignment.revision']);
        $routes->get('kelas/(:segment)/post-test', 'ParticipantController::postTest/$1', ['as' => 'workspace.participant.post-test']);
        $routes->get('kelas/(:segment)/post-test/hasil', 'ParticipantController::postTestResult/$1', ['as' => 'workspace.participant.post-test.result']);
        $routes->get('kelas/(:segment)/feedback', 'ParticipantController::feedback/$1', ['as' => 'workspace.participant.feedback']);
        $routes->get('sertifikat', 'ParticipantController::certificates', ['as' => 'workspace.participant.certificates']);
        $routes->get('sertifikat/(:segment)', 'ParticipantController::certificateDetail/$1', ['as' => 'workspace.participant.certificate.detail']);
    });

    $routes->group('trainer', static function ($routes) {
        $routes->get('/', 'TrainerController::index', ['as' => 'workspace.trainer.home']);
        $routes->get('kelas-saya', 'TrainerController::myClasses', ['as' => 'workspace.trainer.classes']);
        $routes->get('kelas/(:segment)', 'TrainerController::classDetail/$1', ['as' => 'workspace.trainer.class.detail']);
        $routes->get('kelas/(:segment)/peserta', 'TrainerController::participants/$1', ['as' => 'workspace.trainer.participants']);
        $routes->get('kelas/(:segment)/materi', 'TrainerController::materials/$1', ['as' => 'workspace.trainer.materials']);
        $routes->get('kelas/(:segment)/modul', 'TrainerController::modules/$1', ['as' => 'workspace.trainer.modules']);
        $routes->get('kelas/(:segment)/quiz', 'TrainerController::quizzes/$1', ['as' => 'workspace.trainer.quizzes']);
        $routes->get('kelas/(:segment)/tugas', 'TrainerController::assignments/$1', ['as' => 'workspace.trainer.assignments']);
        $routes->get('kelas/(:segment)/progres', 'TrainerController::progress/$1', ['as' => 'workspace.trainer.progress']);
    });

    $routes->group('evaluator', static function ($routes) {
        $routes->get('/', 'EvaluatorController::index', ['as' => 'workspace.evaluator.home']);
        $routes->get('penugasan', 'EvaluatorController::assignments', ['as' => 'workspace.evaluator.assignments']);
        $routes->get('kelas/(:segment)', 'EvaluatorController::classDetail/$1', ['as' => 'workspace.evaluator.class.detail']);
        $routes->get('kelas/(:segment)/submission', 'EvaluatorController::submissions/$1', ['as' => 'workspace.evaluator.submissions']);
        $routes->get('kelas/(:segment)/submission/(:segment)', 'EvaluatorController::submissionDetail/$1/$2', ['as' => 'workspace.evaluator.submission.detail']);
        $routes->get('kelas/(:segment)/penilaian', 'EvaluatorController::grading/$1', ['as' => 'workspace.evaluator.grading']);
        $routes->get('kelas/(:segment)/feedback', 'EvaluatorController::feedback/$1', ['as' => 'workspace.evaluator.feedback']);
    });

    $routes->group('operator', static function ($routes) {
        $routes->get('/', 'OperatorController::index', ['as' => 'workspace.operator.home']);
        $routes->get('verifikasi-profil', 'OperatorController::profileVerifications', ['as' => 'workspace.operator.profile-verifications']);
        $routes->get('enrollment', 'OperatorController::enrollments', ['as' => 'workspace.operator.enrollments']);
        $routes->get('event', 'OperatorController::events', ['as' => 'workspace.operator.events']);
        $routes->get('kehadiran', 'OperatorController::attendance', ['as' => 'workspace.operator.attendance']);
        $routes->get('approval', 'OperatorController::approvals', ['as' => 'workspace.operator.approvals']);
    });
});

$routes->group('profil', ['namespace' => 'App\Controllers\Auth'], static function ($routes) {
    $routes->get('/', 'ProfileController::index', ['as' => 'profile.index']);
    $routes->get('akun', 'ProfileController::account', ['as' => 'profile.account']);
    $routes->get('asn', 'ProfileController::asn', ['as' => 'profile.asn']);
    $routes->get('penyedia', 'ProfileController::provider', ['as' => 'profile.provider']);
    $routes->get('akademisi', 'ProfileController::academic', ['as' => 'profile.academic']);
    $routes->get('masyarakat', 'ProfileController::public', ['as' => 'profile.public']);
    $routes->get('peran-pengadaan', 'ProfileController::procurementRoles', ['as' => 'profile.procurement-roles']);
    $routes->get('permohonan-perubahan', 'ProfileChangeRequestController::index', ['as' => 'profile.change-requests']);
    $routes->get('permohonan-perubahan/buat', 'ProfileChangeRequestController::create', ['as' => 'profile.change-requests.create']);
    $routes->get('permohonan-role', 'RoleRequestController::index', ['as' => 'profile.role-requests']);
    $routes->get('permohonan-role/buat', 'RoleRequestController::create', ['as' => 'profile.role-requests.create']);
});

$routes->group('dashboard', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('masuk', 'AdminAuthController::index', ['as' => 'dashboard.auth.login']);
    $routes->get('lupa-kata-sandi', 'AdminAuthController::forgotPassword', ['as' => 'dashboard.auth.password.forgot']);
    $routes->get('keluar', 'AdminAuthController::logoutPreview', ['as' => 'dashboard.auth.logout.preview']);

    $routes->group('daftar', static function ($routes) {
        // Langkah 1: Akun dasar + tipe profil
        $routes->get('/', 'AdminAuthController::register', ['as' => 'dashboard.auth.register',]);

        // Langkah 2: Wilayah dan alamat ringkas
        $routes->get('wilayah', 'AdminAuthController::registerAddress', ['as' => 'dashboard.auth.register.address',]);

        // Langkah 3: Profil khusus berdasarkan tipe profil
        $routes->get('profil/asn', 'AdminAuthController::registerAsn', ['as' => 'dashboard.auth.register.profile.asn',]);

        $routes->get('profil/penyedia', 'AdminAuthController::registerProvider', ['as' => 'dashboard.auth.register.profile.provider',]);

        $routes->get('profil/akademisi', 'AdminAuthController::registerAcademic', ['as' => 'dashboard.auth.register.profile.academic',]);

        $routes->get('profil/masyarakat', 'AdminAuthController::registerPublic', ['as' => 'dashboard.auth.register.profile.public',]);

        // Langkah 4: Tinjau data dan persetujuan
        $routes->get('tinjau', 'AdminAuthController::registerReview', ['as' => 'dashboard.auth.register.review',]);

        // Hasil submit UI-only
        $routes->get('berhasil', 'AdminAuthController::registerSuccess', ['as' => 'dashboard.auth.register.success',]);
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
        $routes->get('pengguna/(:segment)/ubah', 'UserController::edit/$1', ['as' => 'dashboard.access.users.edit']);
        $routes->get('pengguna/(:segment)', 'UserController::detail/$1', ['as' => 'dashboard.access.users.detail']);
        $routes->get('verifikasi-profil', 'UserProfileVerificationController::index', ['as' => 'dashboard.access.profile-verifications']);
        $routes->get('verifikasi-profil/(:segment)', 'UserProfileVerificationController::detail/$1', ['as' => 'dashboard.access.profile-verifications.detail']);
        $routes->get('permohonan-perubahan-profil', 'UserProfileVerificationController::changeRequests', ['as' => 'dashboard.access.profile-change-requests']);
        $routes->get('permohonan-perubahan-profil/(:segment)', 'UserProfileVerificationController::changeRequestDetail/$1', ['as' => 'dashboard.access.profile-change-requests.detail']);
        $routes->get('role', 'RoleController::index', ['as' => 'dashboard.access.roles']);
        $routes->get('role/tambah', 'RoleController::create', ['as' => 'dashboard.access.roles.create']);
        $routes->get('role/(:segment)/ubah', 'RoleController::edit/$1', ['as' => 'dashboard.access.roles.edit']);
        $routes->get('role/(:segment)', 'RoleController::detail/$1', ['as' => 'dashboard.access.roles.detail']);
        $routes->get('permission', 'PermissionController::index', ['as' => 'dashboard.access.permissions']);
        $routes->get('permission/tambah', 'PermissionController::create', ['as' => 'dashboard.access.permissions.create']);
        $routes->get('permission/(:segment)', 'PermissionController::detail/$1', ['as' => 'dashboard.access.permissions.detail']);
        $routes->get('permohonan-role', 'RoleRequestReviewController::index', ['as' => 'dashboard.access.role-requests']);
        $routes->get('permohonan-role/(:segment)', 'RoleRequestReviewController::detail/$1', ['as' => 'dashboard.access.role-requests.detail']);
        $routes->get('unit-organisasi', 'OrganizationController::index', ['as' => 'dashboard.access.organization-units']);
        $routes->get('unit-organisasi/tambah', 'OrganizationController::create', ['as' => 'dashboard.access.organization-units.create']);
        $routes->get('unit-organisasi/(:segment)', 'OrganizationController::detail/$1', ['as' => 'dashboard.access.organization-units.detail']);
        $routes->get('peran-pengadaan', 'ProcurementRoleController::index', ['as' => 'dashboard.access.procurement-roles']);
        $routes->get('peran-pengadaan/tambah', 'ProcurementRoleController::create', ['as' => 'dashboard.access.procurement-roles.create']);
        $routes->get('peran-pengadaan/(:segment)', 'ProcurementRoleController::detail/$1', ['as' => 'dashboard.access.procurement-roles.detail']);
    });

    $routes->group('pembelajaran', ['namespace' => 'App\Controllers\Learning'], static function ($routes) {
        $routes->get('program', 'ProgramController::index', ['as' => 'dashboard.learning.programs']);
        $routes->get('program/tambah', 'ProgramController::create', ['as' => 'dashboard.learning.programs.create']);
        $routes->get('program/(:segment)/ubah', 'ProgramController::edit/$1', ['as' => 'dashboard.learning.programs.edit']);
        $routes->get('program/(:segment)', 'ProgramController::detail/$1', ['as' => 'dashboard.learning.programs.detail']);
        $routes->get('course', 'CourseController::index', ['as' => 'dashboard.learning.courses']);
        $routes->get('course/tambah', 'CourseController::create', ['as' => 'dashboard.learning.courses.create']);
        $routes->get('course/(:segment)/ubah', 'CourseController::edit/$1', ['as' => 'dashboard.learning.courses.edit']);
        $routes->get('course/(:segment)', 'CourseController::detail/$1', ['as' => 'dashboard.learning.courses.detail']);
        $routes->get('kelas', 'LearningClassController::index', ['as' => 'dashboard.learning.classes']);
        $routes->get('kelas/tambah', 'LearningClassController::create', ['as' => 'dashboard.learning.classes.create']);
        $routes->get('kelas/(:segment)/ubah', 'LearningClassController::edit/$1', ['as' => 'dashboard.learning.classes.edit']);
        $routes->get('kelas/(:segment)', 'LearningClassController::detail/$1', ['as' => 'dashboard.learning.classes.detail']);
        $routes->get('materi-modul', 'ModuleController::index', ['as' => 'dashboard.learning.modules']);
        $routes->get('materi-modul/tambah', 'ModuleController::create', ['as' => 'dashboard.learning.modules.create']);
        $routes->get('materi-modul/(:segment)', 'ModuleController::detail/$1', ['as' => 'dashboard.learning.modules.detail']);
        $routes->get('lesson', 'LessonController::index', ['as' => 'dashboard.learning.lessons']);
        $routes->get('lesson/tambah', 'LessonController::create', ['as' => 'dashboard.learning.lessons.create']);
        $routes->get('lesson/(:segment)', 'LessonController::detail/$1', ['as' => 'dashboard.learning.lessons.detail']);
        $routes->get('assessment', 'QuizController::index', ['as' => 'dashboard.learning.assessments']);
        $routes->get('assessment/pre-test', 'PreTestController::index', ['as' => 'dashboard.learning.pre-tests']);
        $routes->get('assessment/quiz', 'QuizController::index', ['as' => 'dashboard.learning.quizzes']);
        $routes->get('assessment/post-test', 'PostTestController::index', ['as' => 'dashboard.learning.post-tests']);
        $routes->get('assessment/bank-soal', 'QuizController::questionBank', ['as' => 'dashboard.learning.question-bank']);
        $routes->get('enrollment', 'EnrollmentController::index', ['as' => 'dashboard.learning.enrollments']);
        $routes->get('enrollment/(:segment)', 'EnrollmentController::detail/$1', ['as' => 'dashboard.learning.enrollments.detail']);
        $routes->get('penilaian', 'GradingController::index', ['as' => 'dashboard.learning.gradings']);
        $routes->get('penilaian/(:segment)', 'GradingController::detail/$1', ['as' => 'dashboard.learning.gradings.detail']);
        $routes->get('progres-kelulusan', 'StudyController::progress', ['as' => 'dashboard.learning.progress-completion']);
        $routes->get('feedback-peserta', 'StudyController::feedback', ['as' => 'dashboard.learning.participant-feedback']);
    });

    $routes->group('bunga-rampai', ['namespace' => 'App\Controllers\Knowledge'], static function ($routes) {
        $routes->get('artikel', 'ArticleController::index', ['as' => 'dashboard.knowledge.articles']);
        $routes->get('artikel/tambah', 'ArticleController::create', ['as' => 'dashboard.knowledge.articles.create']);
        $routes->get('artikel/(:segment)/ubah', 'ArticleController::edit/$1', ['as' => 'dashboard.knowledge.articles.edit']);
        $routes->get('artikel/(:segment)', 'ArticleController::detail/$1', ['as' => 'dashboard.knowledge.articles.detail']);
        $routes->get('faq', 'FaqController::index', ['as' => 'dashboard.knowledge.faqs']);
        $routes->get('faq/tambah', 'FaqController::create', ['as' => 'dashboard.knowledge.faqs.create']);
        $routes->get('faq/(:segment)', 'FaqController::detail/$1', ['as' => 'dashboard.knowledge.faqs.detail']);
        $routes->get('studi-kasus', 'CaseStudyController::index', ['as' => 'dashboard.knowledge.case-studies']);
        $routes->get('studi-kasus/tambah', 'CaseStudyController::create', ['as' => 'dashboard.knowledge.case-studies.create']);
        $routes->get('studi-kasus/(:segment)', 'CaseStudyController::detail/$1', ['as' => 'dashboard.knowledge.case-studies.detail']);
        $routes->get('template-dokumen', 'DocumentTemplateController::index', ['as' => 'dashboard.knowledge.document-templates']);
        $routes->get('template-dokumen/tambah', 'DocumentTemplateController::create', ['as' => 'dashboard.knowledge.document-templates.create']);
        $routes->get('template-dokumen/(:segment)', 'DocumentTemplateController::detail/$1', ['as' => 'dashboard.knowledge.document-templates.detail']);
        $routes->get('media-galeri', 'KnowledgeMediaController::index', ['as' => 'dashboard.knowledge.media-gallery']);
        $routes->get('media-galeri/tambah', 'KnowledgeMediaController::create', ['as' => 'dashboard.knowledge.media-gallery.create']);
        $routes->get('media-galeri/(:segment)', 'KnowledgeMediaController::detail/$1', ['as' => 'dashboard.knowledge.media-gallery.detail']);
        $routes->get('carousel-konten-unggulan', 'KnowledgeCarouselController::index', ['as' => 'dashboard.knowledge.carousel']);
        $routes->get('carousel-konten-unggulan/tambah', 'KnowledgeCarouselController::create', ['as' => 'dashboard.knowledge.carousel.create']);
        $routes->get('kategori', 'ArticleController::categories', ['as' => 'dashboard.knowledge.categories']);
        $routes->get('tag', 'ArticleController::tags', ['as' => 'dashboard.knowledge.tags']);
    });

    $routes->group('regulasi', ['namespace' => 'App\Controllers\Regulation'], static function ($routes) {
        $routes->get('dokumen', 'RegulationController::index', ['as' => 'dashboard.regulations.index']);
        $routes->get('dokumen/tambah', 'RegulationController::create', ['as' => 'dashboard.regulations.create']);
        $routes->get('dokumen/(:segment)/ubah', 'RegulationController::edit/$1', ['as' => 'dashboard.regulations.edit']);
        $routes->get('dokumen/(:segment)', 'RegulationController::detail/$1', ['as' => 'dashboard.regulations.detail']);
        $routes->get('kategori', 'RegulationCategoryController::index', ['as' => 'dashboard.regulations.categories']);
        $routes->get('kategori/tambah', 'RegulationCategoryController::create', ['as' => 'dashboard.regulations.categories.create']);
        $routes->get('kategori/(:segment)', 'RegulationCategoryController::detail/$1', ['as' => 'dashboard.regulations.categories.detail']);
        $routes->get('versi', 'RegulationVersionController::index', ['as' => 'dashboard.regulations.versions']);
        $routes->get('versi/(:segment)', 'RegulationVersionController::detail/$1', ['as' => 'dashboard.regulations.versions.detail']);
        $routes->get('relasi', 'RegulationRelationController::index', ['as' => 'dashboard.regulations.relations']);
        $routes->get('relasi/(:segment)', 'RegulationRelationController::detail/$1', ['as' => 'dashboard.regulations.relations.detail']);
        $routes->get('tahapan-pbj', 'RegulationController::procurementStages', ['as' => 'dashboard.regulations.procurement-stages']);
        $routes->get('kompetensi-terkait', 'RegulationController::competencies', ['as' => 'dashboard.regulations.competencies']);
        $routes->get('log-download', 'RegulationDownloadController::index', ['as' => 'dashboard.regulations.download-logs']);
    });

    $routes->group('komunikasi', ['namespace' => 'App\Controllers\Communication'], static function ($routes) {
        $routes->get('berita', 'NewsController::index', ['as' => 'dashboard.communication.news']);
        $routes->get('berita/tambah', 'NewsController::create', ['as' => 'dashboard.communication.news.create']);
        $routes->get('berita/(:segment)/ubah', 'NewsController::edit/$1', ['as' => 'dashboard.communication.news.edit']);
        $routes->get('berita/(:segment)', 'NewsController::detail/$1', ['as' => 'dashboard.communication.news.detail']);
        $routes->get('kategori-berita', 'NewsController::categories', ['as' => 'dashboard.communication.news-categories']);
        $routes->get('event-agenda', 'EventController::index', ['as' => 'dashboard.communication.events']);
        $routes->get('event-agenda/tambah', 'EventController::create', ['as' => 'dashboard.communication.events.create']);
        $routes->get('event-agenda/(:segment)/ubah', 'EventController::edit/$1', ['as' => 'dashboard.communication.events.edit']);
        $routes->get('event-agenda/(:segment)', 'EventController::detail/$1', ['as' => 'dashboard.communication.events.detail']);
        $routes->get('registrasi-event', 'EventRegistrationController::index', ['as' => 'dashboard.communication.event-registrations']);
        $routes->get('registrasi-event/(:segment)', 'EventRegistrationController::detail/$1', ['as' => 'dashboard.communication.event-registrations.detail']);
        $routes->get('kehadiran', 'AttendanceController::index', ['as' => 'dashboard.communication.attendance']);
        $routes->get('waitlist-pembatalan', 'EventRegistrationController::waitlistAndCancellation', ['as' => 'dashboard.communication.waitlist-cancellations']);
    });

    $routes->group('sertifikat', ['namespace' => 'App\Controllers\Certificate'], static function ($routes) {
        $routes->get('/', 'CertificateController::index', ['as' => 'dashboard.certificates.index']);
        $routes->get('tambah', 'CertificateController::create', ['as' => 'dashboard.certificates.create']);
        $routes->get('eligibility', 'CertificateController::eligibility', ['as' => 'dashboard.certificates.eligibility']);
        $routes->get('template', 'CertificateTemplateController::index', ['as' => 'dashboard.certificates.templates']);
        $routes->get('template/tambah', 'CertificateTemplateController::create', ['as' => 'dashboard.certificates.templates.create']);
        $routes->get('template/(:segment)', 'CertificateTemplateController::detail/$1', ['as' => 'dashboard.certificates.templates.detail']);
        $routes->get('approval', 'CertificateApprovalController::index', ['as' => 'dashboard.certificates.approvals']);
        $routes->get('approval/(:segment)', 'CertificateApprovalController::detail/$1', ['as' => 'dashboard.certificates.approvals.detail']);
        $routes->get('verifikasi', 'CertificateController::verification', ['as' => 'dashboard.certificates.verification']);
        $routes->get('revocation', 'CertificateController::revocation', ['as' => 'dashboard.certificates.revocation']);
        $routes->get('nomor', 'CertificateController::numbers', ['as' => 'dashboard.certificates.numbers']);
        $routes->get('(:segment)/ubah', 'CertificateController::edit/$1', ['as' => 'dashboard.certificates.edit']);
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
    });
});

$routes->get('pratinjau/403', 'Landing\HomeController::forbiddenPreview', ['as' => 'preview.403']);
$routes->get('pratinjau/404', 'Landing\HomeController::notFoundPreview', ['as' => 'preview.404']);
$routes->get('pratinjau/500', 'Landing\HomeController::serverErrorPreview', ['as' => 'preview.500']);

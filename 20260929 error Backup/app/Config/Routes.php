<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
 * =========================================================================
 * BEGAWIPBJ — Route Auth & Modul (menimpa/menambah alias WowDash yang sudah
 * dipakai app/Views/partials/sidebar.php & navbar.php: index, usersList,
 * roleAaccess, assignRole, signin, signup, dan route modul aplikasi.
 * =========================================================================
 */

// --- Auth (publik) ---
$routes->get('login', 'Auth\LoginController::index', ['as' => 'signin']);
$routes->post('login', 'Auth\LoginController::attempt', ['as' => 'login.attempt']);
$routes->post('logout', 'Auth\LoginController::logout', ['as' => 'logout']);
$routes->get('register', 'Auth\RegisterController::index', ['as' => 'signup']);
$routes->post('register', 'Auth\RegisterController::attempt', ['as' => 'register.attempt']);
$routes->get('reference/wilayah/(:segment)', 'Reference\WilayahController::children/$1', ['as' => 'reference.wilayah.children']);
$routes->get('sertifikat/verifikasi/(:segment)', 'Certificate\CertificateController::verify/$1', ['as' => 'certificate.verify']);

// --- Pembaca publik: KMS dan regulasi ---
$routes->group('knowledge', static function ($routes) {
    $routes->get('articles', 'Knowledge\ArticleController::browse', ['as' => 'knowledge.articles.browse']);
    $routes->get('articles/(:num)', 'Knowledge\ArticleController::detail/$1', ['as' => 'knowledge.articles.detail']);
    $routes->get('faq', 'Knowledge\FaqController::index', ['as' => 'knowledge.faq']);
    $routes->get('case-studies', 'Knowledge\CaseStudyController::browse', ['as' => 'knowledge.caseStudies.browse']);
    $routes->get('case-studies/(:num)', 'Knowledge\CaseStudyController::detail/$1', ['as' => 'knowledge.caseStudies.detail']);
    $routes->get('templates', 'Knowledge\TemplateController::browse', ['as' => 'knowledge.templates.browse']);
    $routes->get('templates/(:num)/download', 'Knowledge\TemplateController::download/$1', ['as' => 'knowledge.templates.download']);
});

$routes->group('regulation', static function ($routes) {
    $routes->get('', 'Regulation\RegulationController::browse', ['as' => 'regulation.browse']);
    $routes->get('(:num)', 'Regulation\RegulationController::detail/$1', ['as' => 'regulation.detail']);
    $routes->get('versions/(:num)/download', 'Regulation\RegulationController::download/$1', ['as' => 'regulation.download']);
});

$routes->get('/', static function () {
    $roles = session()->get('roles') ?? [];
    return session()->get('isLoggedIn') && array_intersect($roles, ['super_administrator', 'administrator'])
        ? redirect()->to(route_to('dashboard.pbj'))
        : redirect()->to(route_to('landing'));
}, ['as' => 'index']);
$routes->get('landing', 'LandingController::index', ['as' => 'landing']);
$landingPages = [
    '404',
    'about',
    'become-a-teacher',
    'blog',
    'blog-carousel',
    'blog-details',
    'blog-list',
    'cart',
    'checkout',
    'coming-soon',
    'contact',
    'course',
    'course-carousel',
    'course-details',
    'course-list',
    'event-details',
    'events',
    'events-carousel',
    'faq',
    'gallery',
    'index-dark',
    'instructor',
    'instructor-carousel',
    'instructor-details',
    'login',
    'pricing',
    'product-details',
    'products',
    'sign-up',
    'testimonials',
    'testimonials-carousel',
    'wishlist',
];
foreach ($landingPages as $landingPage) {
    $routes->get($landingPage . '.php', 'LandingController::landingPage/' . $landingPage);
}
$routes->get('index.php', 'LandingController::index');
// --- Area terproteksi (wajib login) ---
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('profile', 'Auth\ProfileController::index', ['as' => 'profile']);
    $routes->post('profile', 'Auth\ProfileController::update', ['as' => 'profile.update']);
    $routes->post('profile/password', 'Auth\ProfileController::updatePassword', ['as' => 'profile.password.update']);
    $routes->get('arena-belajar', 'Learning\StudyController::myClasses', ['as' => 'user.arena']);
    $routes->get('buku-pintar', 'Knowledge\BookmarkController::myBookmarks', ['as' => 'user.bukuPintar']);
    $routes->get('regulasi-saya', 'Regulation\RegulationController::myRegulations', ['as' => 'user.regulasi']);
    $routes->get('sertifikat-saya', 'Certificate\CertificateController::my', ['as' => 'user.sertifikat']);

    // RBAC — alias 'roleAaccess' & 'assignRole' dipakai ulang dari sidebar.php
    $routes->group('', ['filter' => ['role:super_administrator,administrator', 'permission:role.manage']], static function ($routes) {
        $routes->get('role-and-access/role-access', 'Admin\RoleController::index', ['as' => 'roleAaccess']);
        $routes->post('role-and-access/role-access', 'Admin\RoleController::store', ['as' => 'admin.roles.store']);
        $routes->post('role-and-access/role-access/(:num)/delete', 'Admin\RoleController::delete/$1', ['as' => 'admin.roles.delete']);
        $routes->get('role-and-access/role-access/(:num)/permissions', 'Admin\RoleController::permissions/$1', ['as' => 'role.permissions']);
        $routes->post('role-and-access/role-access/(:num)/permissions', 'Admin\RoleController::updatePermissions/$1', ['as' => 'role.permissions.update']);
        $routes->get('role-and-access/assign-role', 'Admin\RoleController::assignPage', ['as' => 'assignRole']);
        $routes->post('role-and-access/assign-role', 'Admin\RoleController::assign', ['as' => 'admin.roles.assign.store']);
    });

    // Audit Log — permission 'audit.view' sudah ada dari PermissionSeeder awal
    $routes->group('', ['filter' => ['role:super_administrator,administrator', 'permission:audit.view']], static function ($routes) {
        $routes->get('admin/audit-logs', 'Admin\AuditController::index', ['as' => 'admin.audit.index']);
    });
    // User Management + verifikasi registrasi
    $routes->group('', ['filter' => ['role:super_administrator,administrator,operator', 'permission:user.manage']], static function ($routes) {
        $routes->get('users/users-list', 'Admin\UserController::index', ['as' => 'usersList']);
        $routes->post('users/users-list/(:num)/verify', 'Admin\UserController::verify/$1', ['as' => 'admin.users.verify']);
        $routes->post('users/users-list/(:num)/status', 'Admin\UserController::updateStatus/$1', ['as' => 'admin.users.status']);
    });

    // LMS — Program Pelatihan, Kelas, Modul, Materi, Assessment (Administrator + Trainer, sesuai konfirmasi)
    $routes->group('learning', ['filter' => ['role:super_administrator,administrator,trainer', 'permission:lms.manage']], static function ($routes) {
        $routes->get('courses', 'Learning\CourseController::index', ['as' => 'learning.courses']);
        $routes->get('courses/create', 'Learning\CourseController::create', ['as' => 'learning.courses.create']);
        $routes->post('courses', 'Learning\CourseController::store', ['as' => 'learning.courses.store']);
        $routes->get('courses/(:num)/edit', 'Learning\CourseController::edit/$1', ['as' => 'learning.courses.edit']);
        $routes->post('courses/(:num)', 'Learning\CourseController::update/$1', ['as' => 'learning.courses.update']);
        $routes->post('courses/(:num)/delete', 'Learning\CourseController::delete/$1', ['as' => 'learning.courses.delete']);

        $routes->get('courses/(:num)/classes', 'Learning\ClassController::index/$1', ['as' => 'learning.classes']);
        $routes->get('courses/(:num)/classes/create', 'Learning\ClassController::create/$1', ['as' => 'learning.classes.create']);
        $routes->post('courses/(:num)/classes', 'Learning\ClassController::store/$1', ['as' => 'learning.classes.store']);
        $routes->post('courses/(:num)/classes/(:num)/delete', 'Learning\ClassController::delete/$1/$2', ['as' => 'learning.classes.delete']);

        $routes->get('classes/(:num)/modules', 'Learning\ModuleController::index/$1', ['as' => 'learning.modules']);
        $routes->post('classes/(:num)/modules', 'Learning\ModuleController::storeModule/$1', ['as' => 'learning.modules.store']);
        $routes->post('classes/(:num)/modules/(:num)/lessons', 'Learning\ModuleController::storeLesson/$1/$2', ['as' => 'learning.lessons.store']);

        $routes->get('enrollments/pending', 'Learning\EnrollmentController::pending', ['as' => 'learning.enrollments.pending', 'filter' => 'permission:enrollment.verify']);
        $routes->post('enrollments/(:num)/verify', 'Learning\EnrollmentController::verify/$1', ['as' => 'learning.enrollments.verify', 'filter' => 'permission:enrollment.verify']);

        // Assessment/Quiz — kelola soal & jawaban (Administrator + Trainer)
        $routes->get('modules/(:num)/assessments', 'Learning\AssessmentController::index/$1', ['as' => 'learning.assessments']);
        $routes->post('modules/(:num)/assessments', 'Learning\AssessmentController::store/$1', ['as' => 'learning.assessments.store']);
        $routes->get('assessments/(:num)/questions', 'Learning\AssessmentController::questions/$1', ['as' => 'learning.assessments.questions']);
        $routes->post('assessments/(:num)/questions', 'Learning\AssessmentController::storeQuestion/$1', ['as' => 'learning.assessments.questions.store']);
        $routes->post('assessments/(:num)/questions/(:num)/answers', 'Learning\AssessmentController::storeAnswer/$1/$2', ['as' => 'learning.assessments.answers.store']);
    });

    // Enrollment (daftar/lihat kelas) — terbuka untuk semua user yang login (Peserta, dsb), bukan hanya admin/trainer
    $routes->group('learning', static function ($routes) {
        $routes->get('enrollments/browse', 'Learning\EnrollmentController::browse', ['as' => 'learning.enrollments.browse']);
        $routes->post('enrollments/(:num)/enroll', 'Learning\EnrollmentController::store/$1', ['as' => 'learning.enrollments.store']);

        // Sisi Peserta: materi kelas sendiri, progress, dan quiz
        $routes->get('my-classes', 'Learning\StudyController::myClasses', ['as' => 'learning.study.myClasses']);
        $routes->get('my-classes/(:num)', 'Learning\StudyController::modules/$1', ['as' => 'learning.study.modules']);
        $routes->post('my-classes/(:num)/lessons/(:num)/complete', 'Learning\StudyController::completeLesson/$1/$2', ['as' => 'learning.study.completeLesson']);
        $routes->get('my-classes/(:num)/lessons/(:num)/download', 'Learning\StudyController::downloadLesson/$1/$2', ['as' => 'learning.study.lessons.download']);
        $routes->get('my-classes/(:num)/quiz/(:num)', 'Learning\QuizController::take/$1/$2', ['as' => 'learning.study.quiz.take']);
        $routes->post('my-classes/(:num)/quiz/(:num)/submit', 'Learning\QuizController::submit/$1/$2', ['as' => 'learning.study.quiz.submit']);
        $routes->get('my-classes/(:num)/assessment/(:num)', 'Learning\ManualAssessmentController::show/$1/$2', ['as' => 'learning.study.assessment.show']);
        $routes->post('my-classes/(:num)/assessment/(:num)', 'Learning\ManualAssessmentController::submit/$1/$2', ['as' => 'learning.study.assessment.submit']);
    });

    $routes->group('learning/evaluator', ['filter' => ['role:super_administrator,administrator,evaluator', 'permission:assessment.evaluate']], static function ($routes) {
        $routes->get('assessments', 'Learning\ManualAssessmentController::pending', ['as' => 'learning.evaluator.pending']);
        $routes->post('assessments/(:num)', 'Learning\ManualAssessmentController::evaluate/$1', ['as' => 'learning.evaluator.evaluate']);
    });

    // KMS — kelola konten (Administrator + Trainer)
    $routes->group('knowledge', ['filter' => ['role:super_administrator,administrator,trainer', 'permission:kms.manage']], static function ($routes) {
        $routes->get('categories', 'Knowledge\CategoryController::index', ['as' => 'knowledge.categories']);
        $routes->post('categories', 'Knowledge\CategoryController::store', ['as' => 'knowledge.categories.store']);
        $routes->post('categories/(:num)/delete', 'Knowledge\CategoryController::delete/$1', ['as' => 'knowledge.categories.delete']);

        $routes->get('articles/manage', 'Knowledge\ArticleController::index', ['as' => 'knowledge.articles.manage']);
        $routes->get('articles/create', 'Knowledge\ArticleController::create', ['as' => 'knowledge.articles.create']);
        $routes->post('articles', 'Knowledge\ArticleController::store', ['as' => 'knowledge.articles.store']);
        $routes->get('articles/(:num)/edit', 'Knowledge\ArticleController::edit/$1', ['as' => 'knowledge.articles.edit']);
        $routes->post('articles/(:num)', 'Knowledge\ArticleController::update/$1', ['as' => 'knowledge.articles.update']);
        $routes->post('articles/(:num)/delete', 'Knowledge\ArticleController::delete/$1', ['as' => 'knowledge.articles.delete']);

        $routes->post('faq', 'Knowledge\FaqController::store', ['as' => 'knowledge.faq.store']);
        $routes->get('faq/(:num)/edit', 'Knowledge\FaqController::edit/$1', ['as' => 'knowledge.faq.edit']);
        $routes->post('faq/(:num)', 'Knowledge\FaqController::update/$1', ['as' => 'knowledge.faq.update']);
        $routes->post('faq/(:num)/delete', 'Knowledge\FaqController::delete/$1', ['as' => 'knowledge.faq.delete']);

        $routes->get('case-studies/create', 'Knowledge\CaseStudyController::create', ['as' => 'knowledge.caseStudies.create']);
        $routes->get('case-studies/(:num)/edit', 'Knowledge\CaseStudyController::edit/$1', ['as' => 'knowledge.caseStudies.edit']);
        $routes->post('case-studies', 'Knowledge\CaseStudyController::store', ['as' => 'knowledge.caseStudies.store']);
        $routes->post('case-studies/(:num)', 'Knowledge\CaseStudyController::update/$1', ['as' => 'knowledge.caseStudies.update']);
        $routes->post('case-studies/(:num)/delete', 'Knowledge\CaseStudyController::delete/$1', ['as' => 'knowledge.caseStudies.delete']);

        $routes->get('templates/create', 'Knowledge\TemplateController::create', ['as' => 'knowledge.templates.create']);
        $routes->post('templates', 'Knowledge\TemplateController::store', ['as' => 'knowledge.templates.store']);
        $routes->post('templates/(:num)/delete', 'Knowledge\TemplateController::delete/$1', ['as' => 'knowledge.templates.delete']);
    });

    // KMS — bookmark memerlukan login
    $routes->group('knowledge', static function ($routes) {
        $routes->post('articles/(:num)/bookmark', 'Knowledge\BookmarkController::toggle/$1', ['as' => 'knowledge.articles.bookmark']);
        $routes->get('bookmarks', 'Knowledge\BookmarkController::myBookmarks', ['as' => 'knowledge.bookmarks']);
    });

    // Regulation Repository — kelola (Administrator + Operator, sesuai dokumen awal)
    $routes->group('regulation', ['filter' => ['role:super_administrator,administrator,operator', 'permission:regulation.manage']], static function ($routes) {
        $routes->get('create', 'Regulation\RegulationController::create', ['as' => 'regulation.create']);
        $routes->post('', 'Regulation\RegulationController::store', ['as' => 'regulation.store']);
        $routes->post('(:num)/versions', 'Regulation\RegulationController::storeVersion/$1', ['as' => 'regulation.versions.store']);
        $routes->post('(:num)/toggle-status', 'Regulation\RegulationController::toggleStatus/$1', ['as' => 'regulation.toggleStatus']);
    });

    // Sertifikat — baca, download, persetujuan (semua user yang login)
    $routes->group('sertifikat', static function ($routes) {
        $routes->get('saya', 'Certificate\CertificateController::my', ['as' => 'certificate.my']);
        $routes->get('(:num)/unduh', 'Certificate\CertificateController::download/$1', ['as' => 'certificate.download']);
    });
    // Sertifikat — persetujuan (hanya untuk super_administrator, administrator, evaluator)
    $routes->group('sertifikat', ['filter' => ['role:super_administrator,administrator,evaluator', 'permission:certificate.approve']], static function ($routes) {
        $routes->get('persetujuan', 'Certificate\CertificateController::pending', ['as' => 'certificate.pending']);
        $routes->post('persetujuan/(:num)', 'Certificate\CertificateController::verifyDecision/$1', ['as' => 'certificate.decision']);
        $routes->get('kelola', 'Certificate\CertificateController::manage', ['as' => 'certificate.manage']); // BARU
        $routes->post('(:num)/cabut', 'Certificate\CertificateController::revoke/$1', ['as' => 'certificate.revoke']);
    });
    // Dashboard PBJ — semua akun aktif memiliki halaman awal setelah login.
    // Export laporan tetap dibatasi oleh role dan permission dashboard.
    $routes->group('dashboard-pbj', ['filter' => ['auth', 'role:super_administrator,administrator']], static function ($routes) {
        $routes->get('', 'Dashboard\DashboardController::index', ['as' => 'dashboard.pbj']);
        $routes->group('', ['filter' => ['role:super_administrator,administrator,operator,evaluator', 'permission:dashboard.view']], static function ($routes) {
            $routes->get('export/pdf', 'Dashboard\DashboardController::exportPdf', ['as' => 'dashboard.export.pdf']);
            $routes->get('export/excel', 'Dashboard\DashboardController::exportExcel', ['as' => 'dashboard.export.excel']);
        });
    });
});

$routes->group('workspace', ['filter' => 'auth'], static function ($routes) {
    $routes->get('trainer', 'Workspace\TrainerController::index');
    $routes->get('evaluator', 'Workspace\EvaluatorController::index');
    $routes->get('peserta', 'Workspace\PesertaController::index');
});

$routes->group('api/v1', static function ($routes) {
    $routes->get('certificate/(:segment)', 'Api\CertificateController::verify/$1');
    $routes->get('statistics', 'Api\StatisticController::index');
});

$routes->group('dashboard', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'DashboardController::index');
});

<?php

// Test script for FASE 7 UI/UX Refinement & Functional Modules
define('FCPATH', realpath(__DIR__ . '/../public') . DIRECTORY_SEPARATOR);

require_once __DIR__ . '/../app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/Boot.php';

defined('ENVIRONMENT') || define('ENVIRONMENT', 'development');
CodeIgniter\Boot::bootConsole($paths);

echo "=== MEMULAI TEST AUDIT & VERIFIKASI FASE 7 BACK-OFFICE ===\n\n";

$tests = [];

// 1. Lint check / syntax check across key modified controllers and views
$filesToLint = [
    'app/Controllers/Admin/UserController.php',
    'app/Controllers/Admin/RoleController.php',
    'app/Controllers/Admin/PermissionController.php',
    'app/Controllers/Admin/RegulationController.php',
    'app/Controllers/Admin/AuditController.php',
    'app/Controllers/Learning/ProgramController.php',
    'app/Controllers/Learning/CourseController.php',
    'app/Controllers/Learning/LearningClassController.php',
    'app/Controllers/Learning/EnrollmentController.php',
    'app/Controllers/Learning/QuizController.php',
    'app/Controllers/Learning/GradingController.php',
    'app/Controllers/Learning/StudyController.php',
    'app/Controllers/Knowledge/ArticleController.php',
    'app/Controllers/Knowledge/FaqController.php',
    'app/Controllers/Knowledge/CaseStudyController.php',
    'app/Controllers/Knowledge/DocumentTemplateController.php',
    'app/Controllers/Communication/NewsController.php',
    'app/Controllers/Communication/EventController.php',
    'app/Controllers/Certificate/CertificateController.php',
    'app/Views/dashboard/components/status_badge.php',
    'app/Views/dashboard/components/empty_state.php',
    'app/Views/dashboard/users/index.php',
    'app/Views/dashboard/learning/programs/index.php',
    'app/Views/dashboard/learning/courses/index.php',
    'app/Views/dashboard/learning/classes/index.php',
    'app/Views/dashboard/learning/classes/detail.php',
    'app/Views/dashboard/knowledge/articles/index.php',
    'app/Views/dashboard/knowledge/articles/detail.php',
    'app/Views/dashboard/knowledge/faqs/index.php',
    'app/Views/dashboard/knowledge/case_studies/index.php',
    'app/Views/dashboard/knowledge/templates/index.php',
    'app/Views/dashboard/regulation/regulations/index.php',
    'app/Views/dashboard/regulation/regulations/detail.php',
    'app/Views/dashboard/communication/news/index.php',
    'app/Views/dashboard/communication/events/index.php',
    'app/Views/dashboard/certificates/index.php',
    'app/Views/dashboard/certificates/detail.php',
    'app/Views/dashboard/audit/index.php',
];

$lintErrors = 0;
foreach ($filesToLint as $file) {
    $fullPath = realpath(__DIR__ . '/../' . $file);
    if (! $fullPath || ! file_exists($fullPath)) {
        echo "[-] File tidak ditemukan: {$file}\n";
        $lintErrors++;
        continue;
    }
    $cmd = "php -l \"" . $fullPath . "\" 2>&1";
    $output = shell_exec($cmd);
    if (strpos($output, 'No syntax errors detected') === false) {
        echo "[-] Syntax error di {$file}: {$output}\n";
        $lintErrors++;
    }
}

if ($lintErrors === 0) {
    echo "[✓] Semua file Controller & View berhasil lulus validasi PHP Lint (0 error).\n";
} else {
    echo "[x] Terdapat {$lintErrors} error pada pemeriksaan PHP Lint.\n";
}

// 2. Test Component status_badge.php
echo "\n--- Menguji View Component: status_badge.php ---\n";
try {
    $statuses = ['active', 'inactive', 'draft', 'submitted', 'in_review', 'approved', 'published', 'revoked', 'passed', 'failed', 'completed'];
    $badgeSuccess = true;
    foreach ($statuses as $st) {
        $html = view('dashboard/components/status_badge', ['status' => $st]);
        if (empty($html) || strpos($html, 'badge') === false) {
            $badgeSuccess = false;
            echo "[-] Gagal render status: {$st}\n";
        }
    }
    if ($badgeSuccess) {
        echo "[✓] status_badge.php berhasil merender 11 status uji dengan benar.\n";
    }
} catch (\Throwable $e) {
    echo "[-] Exception pada status_badge: " . $e->getMessage() . "\n";
}

// 3. Test Component empty_state.php
echo "\n--- Menguji View Component: empty_state.php ---\n";
try {
    $emptyHtml = view('dashboard/components/empty_state', [
        'icon' => 'solar:box-minimalistic-outline',
        'title' => 'Uji Kosong',
        'message' => 'Pesan pengujian empty state',
        'actionUrl' => '#',
        'actionText' => 'Aksi Uji'
    ]);
    if (strpos($emptyHtml, 'Uji Kosong') !== false && strpos($emptyHtml, 'Aksi Uji') !== false) {
        echo "[✓] empty_state.php berhasil merender dengan ikon, teks, dan tombol aksi.\n";
    } else {
        echo "[-] Gagal render empty_state.php\n";
    }
} catch (\Throwable $e) {
    echo "[-] Exception pada empty_state: " . $e->getMessage() . "\n";
}

// 4. Test Database Connectivity & Tables existence
echo "\n--- Menguji Konektivitas Tabel Modul Bisnis ---\n";
$db = db_connect();
$tables = [
    'users', 'roles', 'permissions', 'approvals',
    'programs', 'courses', 'learning_classes', 'enrollments', 'quizzes', 'assignment_submissions',
    'knowledge_articles', 'knowledge_categories',
    'regulations', 'regulation_categories', 'regulation_versions', 'regulation_relations',
    'news', 'news_categories', 'events',
    'certificates', 'certificate_templates',
    'audit_logs'
];
$allTablesExist = true;
foreach ($tables as $t) {
    if (! $db->tableExists($t)) {
        echo "[-] Tabel tidak ditemukan di DB: {$t}\n";
        $allTablesExist = false;
    }
}
if ($allTablesExist) {
    echo "[✓] Seluruh 22 tabel modul bisnis terverifikasi dan ada di database MySQL.\n";
}

// 5. Test Route Definitions
echo "\n--- Menguji Resolusi Routes Modul Bisnis ---\n";
$routes = service('routes');
$testRoutes = [
    'dashboard.access.users',
    'dashboard.access.roles',
    'dashboard.access.permissions',
    'dashboard.learning.programs',
    'dashboard.learning.courses',
    'dashboard.learning.classes',
    'dashboard.learning.enrollments',
    'dashboard.learning.assessments',
    'dashboard.knowledge.articles',
    'dashboard.knowledge.faqs',
    'dashboard.knowledge.case-studies',
    'dashboard.knowledge.document-templates',
    'dashboard.regulations.index',
    'dashboard.regulations.categories',
    'dashboard.communication.news',
    'dashboard.communication.events',
    'dashboard.certificates.index',
    'dashboard.certificates.approvals',
    'dashboard.intelligence.home',
    'dashboard.reports.learning',
    'dashboard.system.audit-trail',
];

$routePass = 0;
foreach ($testRoutes as $rn) {
    try {
        $uri = route_to($rn);
        if ($uri !== null && $uri !== false) {
            $routePass++;
        }
    } catch (\Throwable $e) {
        echo "[-] Route tidak ditemukan: {$rn} (" . $e->getMessage() . ")\n";
    }
}
echo "[✓] {$routePass} dari " . count($testRoutes) . " route inti back-office terdaftar dan teresolusi dengan benar.\n";

echo "\n=== RINGKASAN VERIFIKASI SELESAI ===\n";

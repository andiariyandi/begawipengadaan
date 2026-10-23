<?php

namespace App\Commands;

use App\Controllers\Landing\ArenaBelajarController;
use App\Controllers\Landing\BungaRampaiController;
use App\Controllers\Landing\EventPublicController;
use App\Controllers\Landing\FileController;
use App\Controllers\Landing\HomeController;
use App\Controllers\Landing\NewsPublicController;
use App\Controllers\Landing\RegulationPublicController;
use App\Services\Communication\CommunicationService;
use App\Services\Identity\PasswordResetService;
use App\Services\LearningService;
use App\Services\Regulation\RegulationPublicService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PortalSecurityTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:portal-security';
    protected $description = 'Comprehensive test suite verifying all audit findings across Public Portal & Authentication layers';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "yellow");
        CLI::write("   BEGAWIPENGADAAN PUBLIC PORTAL & AUTH SECURITY TEST SUITE       ", "yellow");
        CLI::write("==================================================================", "yellow");

        $db = \Config\Database::connect();
        $passed = 0;
        $failed = 0;

        $assert = function (bool $condition, string $message) use (&$passed, &$failed): void {
            if ($condition) {
                CLI::write("  [PASS] " . $message, "green");
                $passed++;
            } else {
                CLI::write("  [FAIL] " . $message, "red");
                $failed++;
            }
        };

        // ====================================================================
        // [1] CRITICAL C-1: FileController Public Isolation & Path Containment
        // ====================================================================
        CLI::write("\n--- [SCENARIO 1] C-1: FileController Public Isolation & Path Containment ---", "cyan");

        $fileCtrlSource = (string) file_get_contents(APPPATH . 'Controllers/Landing/FileController.php');
        $assert(str_contains($fileCtrlSource, "->where('visibility', 'public')"), "FileController memfilter visibility = 'public'");
        $assert(str_contains($fileCtrlSource, "->where('lifecycle_status', 'available')"), "FileController memfilter lifecycle_status = 'available'");
        $assert(str_contains($fileCtrlSource, "->where('deleted_at', null)"), "FileController memfilter deleted_at = null");
        $assert(str_contains($fileCtrlSource, 'resolveSafePath'), "FileController menerapkan resolveSafePath() path containment");
        $assert(str_contains($fileCtrlSource, 'strncmp($candidate, $baseReal, strlen($baseReal))'), "resolveSafePath memverifikasi containment direktori via strncmp");

        // Reflection test on resolveSafePath
        $fileCtrl = new FileController();
        $refMethod = new \ReflectionMethod(FileController::class, 'resolveSafePath');
        $refMethod->setAccessible(true);
        $traversalAttempt = $refMethod->invoke($fileCtrl, '../../../../../../windows/system32/cmd.exe');
        $assert($traversalAttempt === null, "Path traversal attempt '../../../../windows/system32/cmd.exe' diblokir (return null)");

        // ====================================================================
        // [2] CRITICAL C-2: Stored XSS Mitigation in News Content
        // ====================================================================
        CLI::write("\n--- [SCENARIO 2] C-2: Stored XSS Mitigation in News Content ---", "cyan");

        $newsDetailView = (string) file_get_contents(APPPATH . 'Views/landing/pages/news_detail.php');
        $assert(! str_contains($newsDetailView, '<?= $news[\'content\'] /* HTML allowed in article body */ ?>'), "View news_detail.php tidak lagi merender raw HTML content tanpa sanitasi");
        $assert(str_contains($newsDetailView, 'strip_tags') || str_contains($newsDetailView, 'preg_replace'), "View news_detail.php menerapkan pembersihan/sanitasi tag HTML");

        $commServiceSource = (string) file_get_contents(APPPATH . 'Services/Communication/CommunicationService.php');
        $assert(str_contains($commServiceSource, 'sanitizeHtmlContent'), "CommunicationService memiliki method sanitizeHtmlContent() pada write-time");

        $commService = new CommunicationService();
        $refSanitize = new \ReflectionMethod(CommunicationService::class, 'sanitizeHtmlContent');
        $refSanitize->setAccessible(true);
        $xssPayload = '<p>Halo</p><script>alert("XSS")</script><img src=x onerror=alert(document.cookie)><a href="javascript:alert(1)">Klik</a>';
        $sanitized = (string) $refSanitize->invoke($commService, $xssPayload);
        $assert(! str_contains($sanitized, '<script>'), "Tag <script> berhasil dibersihkan dari payload berita");
        $assert(! str_contains($sanitized, 'onerror'), "Attribute event onerror berhasil dibersihkan dari payload berita");
        $assert(! str_contains($sanitized, 'javascript:'), "Skema javascript: URI berhasil dinetralkan dari payload berita");

        // ====================================================================
        // [3] CRITICAL C-3: News Detail Draft/Internal Isolation
        // ====================================================================
        CLI::write("\n--- [SCENARIO 3] C-3: News Detail Draft/Internal Isolation ---", "cyan");

        $newsCtrlSource = (string) file_get_contents(APPPATH . 'Controllers/Landing/NewsPublicController.php');
        $refNewsClass = new \ReflectionClass(NewsPublicController::class);
        $detailMethod = $refNewsClass->getMethod('detail');
        $startLine = $detailMethod->getStartLine();
        $length = $detailMethod->getEndLine() - $startLine;
        $detailLines = array_slice(file(APPPATH . 'Controllers/Landing/NewsPublicController.php'), $startLine, $length);
        $detailCode = implode('', $detailLines);

        $assert(str_contains($detailCode, "workflow_status") && str_contains($detailCode, "'published'"), "NewsPublicController::detail() mewajibkan workflow_status = published");
        $assert(str_contains($detailCode, "visibility") && str_contains($detailCode, "'public'"), "NewsPublicController::detail() mewajibkan visibility = public");

        // ====================================================================
        // [4] HIGH H-1, H-2, H-3 & M-1, M-8: Event Public Security
        // ====================================================================
        CLI::write("\n--- [SCENARIO 4] H-1, H-2, H-3 & M-1, M-8: Event Security ---", "cyan");

        $eventCtrlSource = (string) file_get_contents(APPPATH . 'Controllers/Landing/EventPublicController.php');
        $refEventClass = new \ReflectionClass(EventPublicController::class);

        // H-1: Detail & Register filter visibility
        $eventDetailCode = implode('', array_slice(file(APPPATH . 'Controllers/Landing/EventPublicController.php'), $refEventClass->getMethod('detail')->getStartLine(), 60));
        $assert(str_contains($eventDetailCode, "visibility") && str_contains($eventDetailCode, "'public'"), "[H-1] EventPublicController::detail() memfilter e.visibility = 'public'");

        $eventRegCode = implode('', array_slice(file(APPPATH . 'Controllers/Landing/EventPublicController.php'), $refEventClass->getMethod('register')->getStartLine(), 100));
        $assert(str_contains($eventRegCode, "visibility") && str_contains($eventRegCode, "'public'"), "[H-1] EventPublicController::register() memfilter visibility = 'public'");

        // H-2: Status waitlisted
        $assert(! str_contains($eventCtrlSource, "\$status = 'waitlist';"), "[H-2] Status waitlist diganti menjadi 'waitlisted' mematuhi CHECK constraint");
        $assert(str_contains($eventCtrlSource, "\$status = 'waitlisted';"), "[H-2] Status yang digunakan adalah 'waitlisted'");

        // H-3: Cancelled/Draft event rejection
        $assert(str_contains($eventRegCode, "'cancelled'") && (str_contains($eventRegCode, "Pendaftaran tidak dapat") || str_contains($eventRegCode, "dibatalkan")), "[H-3] EventPublicController::register() menolak pendaftaran pada event berstatus cancelled/tidak valid");

        // M-1 & M-8: Atomic Transaction
        $assert(str_contains($eventRegCode, "transStart()") && str_contains($eventRegCode, "transComplete()"), "[M-1 & M-8] EventPublicController::register() membungkus quota, insert, dan audit dalam transStart()/transComplete()");

        // ====================================================================
        // [5] HIGH H-4: JavaScript String Breakout Mitigation
        // ====================================================================
        CLI::write("\n--- [SCENARIO 5] H-4: JavaScript String Breakout Mitigation ---", "cyan");

        $knowledgeCatalogView = (string) file_get_contents(APPPATH . 'Views/landing/pages/knowledge_catalog.php');
        $assert(! str_contains($knowledgeCatalogView, "copyMediaShareUrl('<?= esc(addslashes"), "[H-4] knowledge_catalog.php tidak lagi memakai esc(addslashes()) dalam event onclick JS");
        $assert(str_contains($knowledgeCatalogView, 'data-share-title='), "[H-4] knowledge_catalog.php memakai HTML5 data-share-title untuk passing string judul aman");

        // ====================================================================
        // [6] HIGH H-5: Regulation Relations Published & Public Filter
        // ====================================================================
        CLI::write("\n--- [SCENARIO 6] H-5: Regulation Relations Published & Public Filter ---", "cyan");

        $regSvcSource = (string) file_get_contents(APPPATH . 'Services/Regulation/RegulationPublicService.php');
        $refRegSvc = new \ReflectionClass(RegulationPublicService::class);
        $relCode = implode('', array_slice(file(APPPATH . 'Services/Regulation/RegulationPublicService.php'), $refRegSvc->getMethod('relations')->getStartLine(), 80));

        $assert(str_contains($relCode, "r_other.workflow_status") && str_contains($relCode, "'published'"), "[H-5] RegulationPublicService::relations() memfilter r_other.workflow_status = 'published'");
        $assert(str_contains($relCode, "r_other.visibility") && str_contains($relCode, "'public'"), "[H-5] RegulationPublicService::relations() memfilter r_other.visibility = 'public'");

        // ====================================================================
        // [7] MEDIUM M-2 & M-3 & M-6: Learning Enrollment & Soft-delete
        // ====================================================================
        CLI::write("\n--- [SCENARIO 7] M-2, M-3, M-6: Learning Enrollment & Soft-delete ---", "cyan");

        $learningSvcSource = (string) file_get_contents(APPPATH . 'Services/LearningService.php');
        $assert(str_contains($learningSvcSource, 'registration_open_at') && str_contains($learningSvcSource, 'registration_close_at'), "[M-2] LearningService::enroll() mengecek jendela registrasi kelas");
        $assert(str_contains($learningSvcSource, 'transStart()') && str_contains($learningSvcSource, 'transComplete()'), "[M-2] LearningService::enroll() atomik dalam transStart()/transComplete()");

        $arenaCtrlSource = (string) file_get_contents(APPPATH . 'Controllers/Landing/ArenaBelajarController.php');
        $refArena = new \ReflectionClass(ArenaBelajarController::class);
        $arenaEnrollCode = implode('', array_slice(file(APPPATH . 'Controllers/Landing/ArenaBelajarController.php'), $refArena->getMethod('enroll')->getStartLine(), 45));
        $assert(str_contains($arenaEnrollCode, 'registration_open_at') && str_contains($arenaEnrollCode, 'registration_close_at'), "[M-3] ArenaBelajarController::enroll() mengecek registration_open_at & close_at");

        $assert(str_contains($arenaCtrlSource, "courses.deleted_at IS NULL"), "[M-6] ArenaBelajarController query memeriksa courses.deleted_at IS NULL");
        $assert(str_contains($arenaCtrlSource, "lc.deleted_at IS NULL"), "[M-6] ArenaBelajarController query memeriksa lc.deleted_at IS NULL");

        // ====================================================================
        // [8] MEDIUM M-4 & M-5: Home Rate Limiting & Atomic Transactions
        // ====================================================================
        CLI::write("\n--- [SCENARIO 8] M-4 & M-5: Home Rate Limiting & Atomic Transactions ---", "cyan");

        $homeCtrlSource = (string) file_get_contents(APPPATH . 'Controllers/Landing/HomeController.php');
        $assert(str_contains($homeCtrlSource, "service('throttler')"), "[M-4] HomeController mengaktifkan service('throttler')");
        $assert(str_contains($homeCtrlSource, "'contact_'"), "[M-4] sendContact() memiliki rate limiting key");
        $assert(str_contains($homeCtrlSource, "'newsletter_'"), "[M-4] subscribeNewsletter() memiliki rate limiting key");
        $assert(str_contains($homeCtrlSource, "'survey_'"), "[M-4] submitSurvey() memiliki rate limiting key");

        $refHome = new \ReflectionClass(HomeController::class);
        $contactCode = implode('', array_slice(file(APPPATH . 'Controllers/Landing/HomeController.php'), $refHome->getMethod('sendContact')->getStartLine(), 80));
        $assert(str_contains($contactCode, "transStart()") && str_contains($contactCode, "transComplete()"), "[M-5] sendContact() membungkus insert dan log audit dalam transaksi atomik");

        $surveyCode = implode('', array_slice(file(APPPATH . 'Controllers/Landing/HomeController.php'), $refHome->getMethod('submitSurvey')->getStartLine(), 100));
        $assert(str_contains($surveyCode, "transStart()") && str_contains($surveyCode, "transComplete()"), "[M-5] submitSurvey() membungkus insert dan log audit dalam transaksi atomik");

        // ====================================================================
        // [9] MEDIUM M-7 & M-12: Bunga Rampai Template Download & Media Filter
        // ====================================================================
        CLI::write("\n--- [SCENARIO 9] M-7 & M-12: Bunga Rampai Template Download & Media ---", "cyan");

        $bungaCtrlSource = (string) file_get_contents(APPPATH . 'Controllers/Landing/BungaRampaiController.php');
        $assert(str_contains($bungaCtrlSource, "resolveSafePath"), "[M-7] BungaRampaiController::templateDownload() menerapkan resolveSafePath()");
        $assert(str_contains($bungaCtrlSource, "fa.deleted_at IS NULL"), "[M-12] Query media BungaRampaiController memfilter fa.deleted_at IS NULL");
        $assert(str_contains($bungaCtrlSource, "ft.deleted_at IS NULL"), "[M-12] Query media BungaRampaiController memfilter ft.deleted_at IS NULL");

        // ====================================================================
        // [10] LOW M-9, M-10, M-11, M-13, M-14: Hardening Details
        // ====================================================================
        CLI::write("\n--- [SCENARIO 10] M-9, M-10, M-11, M-13, M-14: Hardening Details ---", "cyan");

        // M-9: News category is_active
        $assert(str_contains($newsCtrlSource, "nc.is_active = 1") || str_contains($newsCtrlSource, "is_active") && str_contains($newsCtrlSource, "category"), "[M-9] NewsPublicController::category() memfilter kategori aktif");

        // M-10: News stats published & public
        $assert(str_contains($newsCtrlSource, "total_views") && str_contains($newsCtrlSource, "'published'"), "[M-10] News stats renderCatalog memfilter workflow_status = published");

        // M-11: Regulation document_number sanitization
        $regCtrlSource = (string) file_get_contents(APPPATH . 'Controllers/Landing/RegulationPublicController.php');
        $assert(str_contains($regCtrlSource, "\$safeDocNumber = preg_replace('/[^\\w.\\-]/', '_', (string) (\$regulation['document_number'] ?? 'preview'));"), "[M-11] RegulationPublicController inline preview membersihkan filename Content-Disposition");
        $assert(str_contains($regCtrlSource, "\$safeDocNumber = preg_replace('/[^\\w.\\-]/', '_', (string) (\$regulation['document_number'] ?? 'regulasi'));"), "[M-11] RegulationPublicController download attachment membersihkan filename Content-Disposition");

        // M-13: Login Rate Limit
        $loginCtrlSource = (string) file_get_contents(APPPATH . 'Controllers/Dashboard/Auth/LoginController.php');
        $assert(str_contains($loginCtrlSource, "service('throttler')") && str_contains($loginCtrlSource, "auth_login_"), "[M-13] LoginController::attemptLogin() menerapkan rate limit throttler");

        // M-14: Password reset atomic invalidation
        $pwSvcSource = (string) file_get_contents(APPPATH . 'Services/Identity/PasswordResetService.php');
        $assert(str_contains($pwSvcSource, "transStart()") && str_contains($pwSvcSource, "transComplete()"), "[M-14] PasswordResetService::resetPassword() dijalankan dalam transaksi atomik");
        $assert(str_contains($pwSvcSource, "used_at IS NULL") && str_contains($pwSvcSource, "affectedRows()"), "[M-14] PasswordResetService::resetPassword() memeriksa affectedRows() setelah update token kondisional");

        // ====================================================================
        // SUMMARY
        // ====================================================================
        CLI::write("\n==================================================================", "yellow");
        CLI::write("                 PORTAL SECURITY AUDIT SUMMARY                   ", "yellow");
        CLI::write("==================================================================", "yellow");
        CLI::write("  TOTAL CHECKS: " . ($passed + $failed));
        CLI::write("  PASSED      : {$passed}", "green");
        CLI::write("  FAILED      : {$failed}", $failed > 0 ? "red" : "green");
        CLI::write("==================================================================\n", "yellow");

        return $failed > 0 ? 1 : 0;
    }
}

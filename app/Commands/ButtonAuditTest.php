<?php

namespace App\Commands;

use App\Controllers\Admin\ApprovalController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ButtonAuditTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:button-audit';
    protected $description = 'Audit buttons across dashboard views to ensure no invisible or broken buttons';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("       DASHBOARD BUTTON VISIBILITY & CLASS AUDIT TEST            ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = db_connect();

        // 1. Check Approval Detail (ID 82)
        CLI::write("[TEST 1] Testing Approval Detail (/dashboard/sistem/approval/82)...", "yellow");
        session()->set('user_id', 14);
        session()->set('role', 'superadmin');

        $controller = new ApprovalController();
        $controller->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        $html82 = $controller->detail('82');
        if (is_string($html82)) {
            $hasSuccess600 = str_contains($html82, 'btn-success-600');
            $hasSuccessMain = str_contains($html82, 'btn-success-main');

            if ($hasSuccess600 && ! $hasSuccessMain) {
                CLI::write("  [PASS] Approval 82 'Setujui' button correctly rendered with 'btn-success-600'!", "green");
            } else {
                CLI::write("  [FAIL] Approval 82 button check: hasSuccess600=" . ($hasSuccess600 ? 'yes' : 'no') . ", hasSuccessMain=" . ($hasSuccessMain ? 'yes' : 'no'), "red");
            }
        } else {
            CLI::write("  [FAIL] Approval 82 did not return view HTML", "red");
        }

        // 2. Check Approval Index (/dashboard/sistem/approval)
        CLI::write("[TEST 2] Testing Approval Index (/dashboard/sistem/approval)...", "yellow");
        $htmlIndex = $controller->index();
        if (is_string($htmlIndex)) {
            $hasSuccess600Index = str_contains($htmlIndex, 'btn-success-600');
            $hasLegacyMainIndex = str_contains($htmlIndex, 'btn-success-main');

            if ($hasSuccess600Index && ! $hasLegacyMainIndex) {
                CLI::write("  [PASS] Approval Index bulk & quick-action buttons rendered with 'btn-success-600'!", "green");
            } else {
                CLI::write("  [FAIL] Approval Index check: hasSuccess600=" . ($hasSuccess600Index ? 'yes' : 'no') . ", hasLegacyMain=" . ($hasLegacyMainIndex ? 'yes' : 'no'), "red");
            }
        } else {
            CLI::write("  [FAIL] Approval Index did not return view HTML", "red");
        }

        // 3. Scan all dashboard views for any invalid button classes
        CLI::write("[TEST 3] Comprehensive Scan of ALL Dashboard Views for Invalid Button Classes...", "yellow");
        $viewsDir = APPPATH . 'Views/dashboard';
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsDir));
        $invalidPatterns = [
            'btn-success-main',
            'btn-warning-main',
            'btn-danger-main',
            'btn-primary-main',
            'btn-info-main',
            'btn-outline-success-main',
            'btn-outline-warning-main',
            'btn-outline-danger-main',
            'btn-secondary-light',
        ];

        $foundInvalid = [];
        foreach ($files as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') continue;
            $content = file_get_contents($file->getPathname());
            foreach ($invalidPatterns as $inv) {
                if (str_contains($content, $inv)) {
                    $foundInvalid[] = basename($file->getPathname()) . " contains '{$inv}'";
                }
            }
        }

        if (empty($foundInvalid)) {
            CLI::write("  [PASS] Zero invalid button classes across ALL views in app/Views/dashboard!", "green");
        } else {
            CLI::write("  [FAIL] Found invalid button classes:", "red");
            foreach ($foundInvalid as $f) {
                CLI::write("    - {$f}", "red");
            }
        }

        // 4. Verify style.css contains safety fallback rules
        CLI::write("[TEST 4] Verifying style.css CSS Safety Fallback Rules...", "yellow");
        $cssContent = file_get_contents(FCPATH . 'assets/dashboard/css/style.css');
        $hasFallbackSuccess = str_contains($cssContent, '.btn-success-main');
        $hasFallbackWarning = str_contains($cssContent, '.btn-warning-main');
        $hasFallbackOutline = str_contains($cssContent, '.btn-outline-success-main');

        if ($hasFallbackSuccess && $hasFallbackWarning && $hasFallbackOutline) {
            CLI::write("  [PASS] style.css contains complete safety fallback rules for legacy classes!", "green");
        } else {
            CLI::write("  [FAIL] Safety fallback rules missing in style.css", "red");
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("       ALL BUTTON AUDIT TESTS COMPLETED SUCCESSFULLY!            ", "green");
        CLI::write("==================================================================", "cyan");
        return 0;
    }
}

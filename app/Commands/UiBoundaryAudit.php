<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UiBoundaryAudit extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'audit:ui-boundary';
    protected $description = 'Audit sterilisasi batas UI tokens: assets/dashboard di landing & workspace, assets/main di dashboard, dan isolasi folder reference.';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("        BEGAWIPENGADAAN UI BOUNDARY STERILIZATION AUDIT           ", 'cyan');
        CLI::write("==================================================================", 'cyan');
        CLI::newLine();

        $errors = [];
        $totalChecks = 4;
        $passedChecks = 0;

        // 1. Cek folder reference/ (harus tidak ada di APPPATH . 'Views/reference')
        CLI::write("--- [1] MEMERIKSA ISOLASI FOLDER REFERENCE ---", 'yellow');
        $referenceDir = APPPATH . 'Views/reference';
        if (is_dir($referenceDir)) {
            $errors[] = "Folder terlarang '$referenceDir' terdeteksi di dalam app/Views/! Direktori ini harus dihapus agar tidak menjadi dead-code/security bypass.";
            CLI::write(" [FAIL] Folder terlarang '$referenceDir' masih ditemukan!", 'red');
        } else {
            $passedChecks++;
            CLI::write(" [PASS] Folder '$referenceDir' bersih (tidak ada di sistem file).", 'green');
        }
        CLI::newLine();

        // 2. Cek assets/dashboard/ di app/Views/landing/
        CLI::write("--- [2] MEMERIKSA LEAK ASSETS/DASHBOARD DI APP/VIEWS/LANDING ---", 'yellow');
        $landingDir = APPPATH . 'Views/landing';
        $landingViolations = $this->scanDirectoryForForbiddenString($landingDir, 'assets/dashboard/');
        if (!empty($landingViolations)) {
            foreach ($landingViolations as $v) {
                $errors[] = "Kebocoran assets/dashboard/ di {$v['file']}:{$v['line']} -> {$v['snippet']}";
                CLI::write(" [FAIL] {$v['file']}:{$v['line']} -> {$v['snippet']}", 'red');
            }
        } else {
            $passedChecks++;
            CLI::write(" [PASS] 0 kebocoran assets/dashboard/ pada seluruh view landing.", 'green');
        }
        CLI::newLine();

        // 3. Cek assets/dashboard/ di app/Views/main/workspace/
        CLI::write("--- [3] MEMERIKSA LEAK ASSETS/DASHBOARD DI APP/VIEWS/MAIN/WORKSPACE ---", 'yellow');
        $workspaceDir = APPPATH . 'Views/main/workspace';
        $workspaceViolations = $this->scanDirectoryForForbiddenString($workspaceDir, 'assets/dashboard/');
        if (!empty($workspaceViolations)) {
            foreach ($workspaceViolations as $v) {
                $errors[] = "Kebocoran assets/dashboard/ di {$v['file']}:{$v['line']} -> {$v['snippet']}";
                CLI::write(" [FAIL] {$v['file']}:{$v['line']} -> {$v['snippet']}", 'red');
            }
        } else {
            $passedChecks++;
            CLI::write(" [PASS] 0 kebocoran assets/dashboard/ pada seluruh view workspace.", 'green');
        }
        CLI::newLine();

        // 4. Cek assets/main/ di app/Views/dashboard/
        CLI::write("--- [4] MEMERIKSA LEAK ASSETS/MAIN DI APP/VIEWS/DASHBOARD ---", 'yellow');
        $dashboardDir = APPPATH . 'Views/dashboard';
        $dashboardViolations = $this->scanDirectoryForForbiddenString($dashboardDir, 'assets/main/');
        if (!empty($dashboardViolations)) {
            foreach ($dashboardViolations as $v) {
                $errors[] = "Kebocoran assets/main/ di {$v['file']}:{$v['line']} -> {$v['snippet']}";
                CLI::write(" [FAIL] {$v['file']}:{$v['line']} -> {$v['snippet']}", 'red');
            }
        } else {
            $passedChecks++;
            CLI::write(" [PASS] 0 kebocoran assets/main/ pada seluruh view dashboard.", 'green');
        }
        CLI::newLine();

        // Ringkasan
        CLI::write("==================================================================", 'cyan');
        CLI::write("                 HASIL AUDIT UI BOUNDARY                          ", 'cyan');
        CLI::write("==================================================================", 'cyan');
        CLI::write(" Total Kategori Audit : {$totalChecks}");
        CLI::write(" Kategori Berhasil    : {$passedChecks}", $passedChecks === $totalChecks ? 'green' : 'yellow');
        CLI::write(" Kategori Gagal       : " . count($errors), count($errors) === 0 ? 'green' : 'red');
        CLI::write("==================================================================", 'cyan');

        if (count($errors) > 0) {
            CLI::error("Audit UI Boundary GAGAL dengan " . count($errors) . " pelanggaran token!");
            return 1;
        }

        CLI::write("SEMUA CEK UI BOUNDARY STERIL & SESUAI STANDAR ARSITEKTUR!", 'green');
        return 0;
    }

    private function scanDirectoryForForbiddenString(string $dir, string $forbidden): array
    {
        $violations = [];
        if (!is_dir($dir)) {
            return $violations;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $lines = file($file->getPathname());
                foreach ($lines as $lineNum => $line) {
                    if (str_contains($line, $forbidden)) {
                        $violations[] = [
                            'file' => str_replace(APPPATH, 'app/', $file->getPathname()),
                            'line' => $lineNum + 1,
                            'snippet' => trim($line)
                        ];
                    }
                }
            }
        }

        return $violations;
    }
}

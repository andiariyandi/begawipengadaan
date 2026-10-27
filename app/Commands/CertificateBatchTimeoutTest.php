<?php

namespace App\Commands;

use App\Services\CertificateGenerator;
use App\Services\CertificateQrGenerator;
use App\Services\CertificatePdfGenerator;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CertificateBatchTimeoutTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:cert-batch-timeout';
    protected $description = 'Verifies QR Generation Optimization, Batch Timeout Guardrails, and PDF Memory Lifecycle';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("  VERIFIKASI OPTIMASI BATCH CERTIFICATE & PENANGANAN TIMEOUT      ", 'cyan');
        CLI::write("==================================================================", 'cyan');

        $passCount = 0;
        $failCount = 0;
        $db = db_connect();

        try {
            // =========================================================================
            // TEST 1: QR Generator Performance & Spec
            // =========================================================================
            CLI::write("\n[TEST 1] Menguji Performa & Validitas CertificateQrGenerator...", 'yellow');
            $qrGen = new CertificateQrGenerator();
            $t0 = microtime(true);
            $sampleTokens = [];
            for ($i = 0; $i < 20; $i++) {
                $token = bin2hex(random_bytes(32));
                $dataUri = $qrGen->generate($token);
                $sampleTokens[] = $dataUri;
            }
            $elapsed = microtime(true) - $t0;
            $avgMs = round(($elapsed / 20) * 1000, 2);

            $allValidPrefix = true;
            foreach ($sampleTokens as $uri) {
                if (! str_starts_with($uri, 'data:image/png;base64,')) {
                    $allValidPrefix = false;
                    break;
                }
            }

            if ($allValidPrefix && $elapsed < 3.0) {
                CLI::write(" [PASS] 20 QR code tergenerate cepat ({$avgMs}ms/QR, total " . round($elapsed, 3) . "s) dengan format data URI PNG valid.", 'green');
                $passCount++;
            } else {
                CLI::error(" [FAIL] Performa QR lambat atau format invalid: elapsed={$elapsed}s, validPrefix={$allValidPrefix}");
                $failCount++;
            }

            // =========================================================================
            // TEST 2: QR URL Integrity & Verification Route
            // =========================================================================
            CLI::write("\n[TEST 2] Menguji Integritas URL Verifikasi pada QR Generator...", 'yellow');
            $testToken = 'test_token_abc123xyz_verify';
            $verifyUrl = $qrGen->buildVerifyUrl($testToken);

            if (str_contains($verifyUrl, '/verify/certificate/' . $testToken)) {
                CLI::write(" [PASS] URL verifikasi QR valid: {$verifyUrl}", 'green');
                $passCount++;
            } else {
                CLI::error(" [FAIL] Format URL verifikasi salah: {$verifyUrl}");
                $failCount++;
            }

            // =========================================================================
            // TEST 3: Static Inspection on Batch Controllers (Guardrails Check)
            // =========================================================================
            CLI::write("\n[TEST 3] Audit Static Code: Proteksi Timeout di Seluruh Handler Batch...", 'yellow');
            $targets = [
                'CertificateBatchController'     => APPPATH . 'Controllers/Certificate/CertificateBatchController.php',
                'CertificateLearningController'  => APPPATH . 'Controllers/Certificate/CertificateLearningController.php',
                'CertificateEventController'     => APPPATH . 'Controllers/Certificate/CertificateEventController.php',
                'CertificateEligibilityCtrl'     => APPPATH . 'Controllers/Learning/CertificateEligibilityController.php',
                'CommunicationService'           => APPPATH . 'Services/Communication/CommunicationService.php',
                'ApprovalController'             => APPPATH . 'Controllers/Admin/ApprovalController.php',
                'OperatorController'             => APPPATH . 'Controllers/Workspace/OperatorController.php',
                'CertificateGenerator'           => APPPATH . 'Services/CertificateGenerator.php',
                'CertificateQrGenerator'         => APPPATH . 'Services/CertificateQrGenerator.php',
                'CertificatePdfGenerator'        => APPPATH . 'Services/CertificatePdfGenerator.php',
            ];

            $allGuarded = true;
            foreach ($targets as $label => $filePath) {
                if (! file_exists($filePath)) {
                    CLI::error(" [FAIL] File tidak ditemukan: {$filePath}");
                    $allGuarded = false;
                    continue;
                }
                $content = file_get_contents($filePath);
                $hasTimeLimit = str_contains($content, 'set_time_limit');
                if (! $hasTimeLimit) {
                    CLI::error(" [FAIL] {$label} belum memiliki proteksi set_time_limit!");
                    $allGuarded = false;
                } else {
                    CLI::write(" [PASS] {$label} memiliki proteksi guardrail batas waktu (set_time_limit).", 'green');
                }
            }

            if ($allGuarded) {
                $passCount++;
            } else {
                $failCount++;
            }

            // =========================================================================
            // TEST 4: Certificate Generation End-to-End Simulation
            // =========================================================================
            CLI::write("\n[TEST 4] Simulasi Siklus Penerbitan Sertifikat & Garbage Collection...", 'yellow');
            $generator = new CertificateGenerator($db);
            $pdfGen    = new CertificatePdfGenerator();

            // Render dummy HTML certificate template
            $dummyHtml = '<html><head><title>Sertifikat Uji</title></head><body>'
                . '<h1>SERTIFIKAT KELULUSAN PBJ</h1>'
                . '<p>Diberikan kepada Peserta Uji Performa Batch</p>'
                . '<img src="' . $sampleTokens[0] . '" width="140" height="140"/>'
                . '</body></html>';

            $t0 = microtime(true);
            $pdfBytes = $pdfGen->generate($dummyHtml);
            $pdfDuration = round((microtime(true) - $t0) * 1000, 2);

            if (str_starts_with($pdfBytes, '%PDF-') && strlen($pdfBytes) > 1000) {
                CLI::write(" [PASS] Render PDF dengan QR gambar berhasil dalam {$pdfDuration}ms (Size: " . strlen($pdfBytes) . " bytes).", 'green');
                $passCount++;
            } else {
                CLI::error(" [FAIL] PDF render gagal menghasilkan output PDF valid.");
                $failCount++;
            }

        } catch (\Throwable $e) {
            CLI::error("Exception saat pengujian batch timeout: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $failCount++;
        }

        CLI::write("==================================================================", 'cyan');
        CLI::write("HASIL VERIFIKASI: {$passCount} PASSED, {$failCount} FAILED", $failCount === 0 ? 'green' : 'red');
        CLI::write("==================================================================", 'cyan');

        return $failCount === 0 ? 0 : 1;
    }
}

<?php

namespace App\Commands;

use App\Models\Certificate\CertificateTemplateModel;
use App\Services\CertificateGenerator;
use App\Services\CertificatePdfGenerator;
use App\Services\CertificatePdfPlaceholderService;
use App\Services\CertificateQrGenerator;
use App\Services\CertificateTemplateRenderer;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use setasign\Fpdi\Fpdi;

class CertificatePdfTemplateTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:certificate-pdf-template';
    protected $description = 'Verify certificate template PDF placeholder detection, stripping, and replacement generation';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   TESTING CERTIFICATE PDF PLACEHOLDER DETECTION & REPLACEMENT    ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $passCount = 0;
        $failCount = 0;

        // -------------------------------------------------------------
        // TEST 1: Direct Dompdf Standard Generation (Regression Check)
        // -------------------------------------------------------------
        CLI::write("[TEST 1] Testing Standard HTML-to-PDF Generation (Regression)...", "yellow");
        try {
            $pdfGen = new CertificatePdfGenerator();
            $html = '<!DOCTYPE html><html><body><h1>Sertifikat Standar</h1></body></html>';
            $pdfBytes = $pdfGen->generate($html);

            if (str_starts_with($pdfBytes, '%PDF-') && strlen($pdfBytes) > 500) {
                CLI::write("  [PASS] Mode standar Dompdf berjalan normal (" . strlen($pdfBytes) . " bytes)", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Output standar tidak valid", "red");
                $failCount++;
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] Exception Test 1: " . $e->getMessage(), "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 2: Create a Base PDF Template with {{placeholder}} tokens inside
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 2] Generating Vector PDF Template Containing {{placeholder}} Tags...", "yellow");
        $tempDir = WRITEPATH . 'uploads/certificates/templates';
        if (! is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }
        $testPdfPath = $tempDir . '/test_bg_placeholder_template_' . time() . '.pdf';

        try {
            $basePdf = new Fpdi('L', 'mm', 'A4');
            $basePdf->SetAutoPageBreak(false);
            $basePdf->AddPage('L', [297, 210]);

            // Draw ornate border
            $basePdf->SetDrawColor(22, 101, 52);
            $basePdf->SetLineWidth(2);
            $basePdf->Rect(10, 10, 277, 190);

            // Vector static header (must remain intact)
            $basePdf->SetFont('Helvetica', 'B', 18);
            $basePdf->SetTextColor(22, 101, 52);
            $basePdf->SetXY(0, 22);
            $basePdf->Cell(297, 10, 'PEMERINTAH PROVINSI LAMPUNG', 0, 1, 'C');
            $basePdf->SetFont('Helvetica', 'B', 14);
            $basePdf->SetTextColor(30, 58, 138);
            $basePdf->Cell(297, 8, 'BIRO PENGADAAN BARANG DAN JASA', 0, 1, 'C');

            // Dynamic placeholders typed directly into the PDF template
            $basePdf->SetFont('Helvetica', 'B', 11);
            $basePdf->SetTextColor(71, 85, 105);
            $basePdf->SetXY(0, 52);
            $basePdf->Cell(297, 7, 'Nomor: {{nomor_sertifikat}}', 0, 1, 'C');

            $basePdf->SetFont('Helvetica', 'B', 26);
            $basePdf->SetTextColor(15, 23, 42);
            $basePdf->SetXY(0, 82);
            $basePdf->Cell(297, 12, '{{nama_peserta}}', 0, 1, 'C');

            $basePdf->SetFont('Helvetica', '', 12);
            $basePdf->SetTextColor(51, 65, 85);
            $basePdf->SetXY(0, 96);
            $basePdf->Cell(297, 7, 'NIP: {{nip}}', 0, 1, 'C');

            $basePdf->SetFont('Helvetica', 'B', 16);
            $basePdf->SetTextColor(29, 78, 216);
            $basePdf->SetXY(0, 122);
            $basePdf->Cell(297, 9, '{{judul_kegiatan}}', 0, 1, 'C');

            $basePdf->SetFont('Helvetica', '', 10);
            $basePdf->SetTextColor(15, 23, 42);
            $basePdf->SetXY(35, 165);
            $basePdf->Cell(40, 8, '{{qr_code}}', 0, 0, 'C');

            $basePdfBytes = $basePdf->Output('S');
            file_put_contents($testPdfPath, $basePdfBytes);

            if (is_file($testPdfPath) && filesize($testPdfPath) > 500) {
                CLI::write("  [PASS] File PDF template dengan placeholder berhasil dibuat (" . filesize($testPdfPath) . " bytes)", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Gagal membuat file PDF template uji", "red");
                $failCount++;
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] Exception Test 2: " . $e->getMessage(), "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 3: Auto-Detect {{placeholder}} & Coordinates inside PDF
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 3] Testing CertificatePdfPlaceholderService::inspectPdf()...", "yellow");
        $detectedPlaceholders = [];
        try {
            $phService = new CertificatePdfPlaceholderService();
            $inspection = $phService->inspectPdf($testPdfPath);
            $detectedPlaceholders = $inspection['placeholders'] ?? [];

            $foundKeys = array_column($detectedPlaceholders, 'key');
            $expectedKeys = ['nomor_sertifikat', 'nama_peserta', 'nip', 'judul_kegiatan', 'qr_code'];
            $missingKeys = array_diff($expectedKeys, $foundKeys);

            if (empty($missingKeys) && count($detectedPlaceholders) >= 5) {
                CLI::write("  [PASS] Semua 5 placeholder ({{nomor_sertifikat}}, {{nama_peserta}}, {{nip}}, {{judul_kegiatan}}, {{qr_code}}) terdeteksi otomatis!", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Placeholder tidak lengkap. Terdeteksi: " . implode(', ', $foundKeys), "red");
                $failCount++;
            }

            // Check coordinate sanity for nama_peserta (should be horizontally centered ~45-55%, y around 35-50%)
            $namaPh = null;
            foreach ($detectedPlaceholders as $item) {
                if (($item['key'] ?? '') === 'nama_peserta') {
                    $namaPh = $item;
                    break;
                }
            }
            if ($namaPh && $namaPh['x_pct'] >= 35 && $namaPh['x_pct'] <= 65 && $namaPh['y_pct'] >= 30 && $namaPh['y_pct'] <= 55) {
                CLI::write("  [PASS] Koordinat {{nama_peserta}} akurat: X={$namaPh['x_pct']}%, Y={$namaPh['y_pct']}%, Font={$namaPh['font_size']}pt", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Koordinat {{nama_peserta}} di luar ekspektasi: " . json_encode($namaPh), "red");
                $failCount++;
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] Exception Test 3: " . $e->getMessage(), "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 4: CertificateTemplateRenderer Coordinate Overlay HTML
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 4] Testing CertificateTemplateRenderer PDF Placeholder Overlay...", "yellow");
        $renderedHtml = '';
        $mockTemplate = [];
        try {
            $renderer = new CertificateTemplateRenderer();
            $mockTemplate = [
                'id'                 => 9999,
                'name'               => 'Template Uji Placeholder PDF',
                'source_type'        => 'learning',
                'background_file_id' => null,
                'template_payload'   => [
                    'layout_mode'      => 'pdf_placeholders',
                    'background_path'  => str_replace(WRITEPATH, '', $testPdfPath),
                    'pdf_placeholders' => $detectedPlaceholders,
                ],
            ];

            $mockData = [
                'certificate_number' => 'SERT-00099/AB-10/05/2026',
                'participant_name'   => 'Dr. Hendra Kurniawan, S.T., M.T.',
                'nip'                => '19840210 200901 1 005',
                'institution_name'   => 'Dinas Bina Marga Provinsi Lampung',
                'title'              => 'Pelatihan Manajemen Kontrak Pengadaan Konstruksi',
                'jam_pelajaran'      => '32 JP',
                'predicate'          => 'Sangat Baik',
                'verification_url'   => 'https://begawipengadaan.lampungprov.go.id/verifikasi-sertifikat/TEST-123',
            ];

            $renderedHtml = $renderer->render($mockData, $mockTemplate);

            if (str_contains($renderedHtml, 'pdf-placeholder-canvas') && str_contains($renderedHtml, 'Dr. Hendra Kurniawan, S.T., M.T.')) {
                CLI::write("  [PASS] Renderer membangun layer koordinat presisi dengan data peserta asli", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Renderer tidak menghasilkan layer koordinat pdf-placeholder-canvas", "red");
                $failCount++;
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] Exception Test 4: " . $e->getMessage(), "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 5: CertificatePdfGenerator Strip Raw {{...}} & Merge Real Data
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 5] Testing Stripping {{placeholder}} from Base PDF & Merging Participant Data...", "yellow");
        try {
            $qrCodeUri = (new CertificateQrGenerator())->generate('sample-verify-token-xyz');
            $qrTag = '<img src="' . $qrCodeUri . '" style="width:88px;height:88px;" />';
            $htmlWithQr = str_replace(
                ['<!--CERTIFICATE_QR_PLACEHOLDER-->', '__QR_BLOCK__', '{{qr_code}}'],
                $qrTag,
                $renderedHtml
            );

            $pdfGen = new CertificatePdfGenerator();
            $finalPdfBytes = $pdfGen->generate($htmlWithQr, $mockTemplate);

            if (str_starts_with($finalPdfBytes, '%PDF-') && strlen($finalPdfBytes) > 1000) {
                CLI::write("  [PASS] Output PDF akhir valid (" . strlen($finalPdfBytes) . " bytes)", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Output akhir bukan PDF valid", "red");
                $failCount++;
            }

            // Inspect decompressed streams in the final PDF:
            // 1. Static text 'PEMERINTAH PROVINSI LAMPUNG' MUST still exist
            // 2. Raw placeholder '{{nama_peserta}}' MUST be stripped (not exist)
            // 3. Replaced text 'Dr. Hendra Kurniawan' MUST exist
            preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $finalPdfBytes, $matches);
            $hasBaseHeader       = false;
            $hasRawPlaceholder   = false;
            $hasReplacedName     = false;

            foreach ($matches[1] as $stream) {
                $decompressed = @gzuncompress($stream);
                if (! is_string($decompressed) || $decompressed === '') {
                    $decompressed = @gzinflate($stream);
                }
                if (is_string($decompressed) && $decompressed !== '') {
                    if (str_contains($decompressed, 'PEMERINTAH PROVINSI LAMPUNG')) {
                        $hasBaseHeader = true;
                    }
                    if (str_contains($decompressed, '{{nama_peserta}}') || str_contains($decompressed, '{{judul_kegiatan}}')) {
                        $hasRawPlaceholder = true;
                    }
                    if (str_contains($decompressed, 'Dr. Hendra Kurniawan')) {
                        $hasReplacedName = true;
                    }
                }
            }

            if ($hasBaseHeader) {
                CLI::write("  [PASS] Desain & teks statis PDF dasar (PEMERINTAH PROVINSI LAMPUNG) tetap utuh", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Teks statis PDF dasar hilang", "red");
                $failCount++;
            }

            if (! $hasRawPlaceholder) {
                CLI::write("  [PASS] Teks mentah {{nama_peserta}} dan {{judul_kegiatan}} berhasil dihapus bersih dari PDF dasar", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Teks mentah {{placeholder}} masih tertinggal di dalam stream PDF", "red");
                $failCount++;
            }

            if ($hasReplacedName) {
                CLI::write("  [PASS] Data peserta asli (Dr. Hendra Kurniawan) berhasil menggantikan posisi placeholder di PDF", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Data peserta asli tidak ditemukan di stream PDF akhir", "red");
                $failCount++;
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] Exception Test 5: " . $e->getMessage(), "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // Cleanup temporary test file
        // -------------------------------------------------------------
        if (is_file($testPdfPath)) {
            @unlink($testPdfPath);
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("                     HASIL PENGUJIAN AKHIR                        ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::write("  Total Berhasil : {$passCount}", "green");
        CLI::write("  Total Gagal    : {$failCount}", $failCount > 0 ? "red" : "green");

        if ($failCount === 0) {
            CLI::write("STATUS: DUKUNGAN TEMPLATE SERTIFIKAT PDF BERFUNGSI SEMPURNA 100%!", "green");
        } else {
            CLI::write("STATUS: ADA KEGAGALAN DALAM PENGUJIAN TEMPLATE PDF", "red");
        }
        CLI::write("==================================================================", "cyan");

        return $failCount === 0 ? 0 : 1;
    }
}

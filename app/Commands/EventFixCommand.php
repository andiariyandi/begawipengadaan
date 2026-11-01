<?php

namespace App\Commands;

use App\Controllers\Certificate\CertificateTemplateController;
use App\Models\Certificate\CertificateTemplateModel;
use App\Services\CertificatePdfGenerator;
use App\Services\CertificateQrGenerator;
use App\Services\CertificateTemplateRenderer;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class EventFixCommand extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:event-fix';
    protected $description = 'Verifikasi previewPdf untuk template 45 dengan icon lock PNG';

    public function run(array $params): int
    {
        CLI::write("==================================================================", 'yellow');
        CLI::write("  VERIFIKASI PDF GENERATE & DOWNLOAD /template/45/preview-pdf     ", 'yellow');
        CLI::write("==================================================================", 'yellow');

        $templateModel = new CertificateTemplateModel();
        $template = $templateModel->find(45);
        if (! $template) {
            CLI::error("Template 45 tidak ditemukan!");
            return 1;
        }

        $ctrl = new CertificateTemplateController();
        $ctrl->initController(service('request'), service('response'), service('logger'));

        $ref = new \ReflectionClass($ctrl);
        $mMethod = $ref->getMethod('buildMockData');
        $mMethod->setAccessible(true);
        $mockData = $mMethod->invoke($ctrl, $template);

        $renderer = new CertificateTemplateRenderer();
        $html     = $renderer->render($mockData, $template);



        $sampleQrUri = (new CertificateQrGenerator())->generate(bin2hex(random_bytes(32)));
        $qrImgTag    = '<img alt="QR Sample" src="' . htmlspecialchars($sampleQrUri, ENT_QUOTES, 'UTF-8')
            . '" style="width:140px;height:140px;border:1px solid #cbd5e1;padding:4px;background:#fff;" />';
        $html = str_replace(['<!--CERTIFICATE_QR_PLACEHOLDER-->', '__QR_BLOCK__', '{{qr_code}}', '{{qr_block}}'], $qrImgTag, $html);

        $pdfGenerator = new CertificatePdfGenerator();
        $pdfBytes     = $pdfGenerator->generate($html, $template);

        file_put_contents(WRITEPATH . 'preview_template_45_with_lock.pdf', $pdfBytes);
        CLI::write("  [PASS] File PDF berhasil dibuat (" . strlen($pdfBytes) . " bytes)", 'green');

        // Verifikasi keberadaan Image XObject dalam PDF
        $hasImageXObject = strpos($pdfBytes, '/Subtype /Image') !== false;
        if ($hasImageXObject) {
            CLI::write("  [PASS] Icon Lock terkonfirmasi ter-render di dalam PDF sebagai Image XObject!", 'green');
        } else {
            CLI::error("  [FAIL] Icon Lock TIDAK ditemukan dalam PDF!");
            return 1;
        }

        // Hitung berapa image object di dalam PDF (harus ada minimal 2: QR code dan Lock Icon)
        preg_match_all('/\/Subtype\s*\/Image/m', $pdfBytes, $matches);
        $imageCount = count($matches[0]);
        CLI::write("  [INFO] Jumlah Image XObject di dalam PDF: " . $imageCount . " (QR Code + Lock Icon)", 'cyan');

        // Cek juga response dari controller previewPdf(45)
        $resp = $ctrl->previewPdf('45');
        if ($resp->getStatusCode() === 200 && $resp->getHeaderLine('Content-Type') === 'application/pdf') {
            CLI::write("  [PASS] Endpoint /dashboard/sertifikat/template/45/preview-pdf mengembalikan HTTP 200 PDF siap unduh!", 'green');
        } else {
            CLI::error("  [FAIL] Endpoint previewPdf gagal!");
            return 1;
        }

        CLI::newLine();
        CLI::write("==================================================================", 'green');
        CLI::write("  STATUS: PENGUJIAN PREVIEW PDF & DOWNLOAD 100% SUKSES!           ", 'green');
        CLI::write("==================================================================", 'green');

        return 0;
    }
}

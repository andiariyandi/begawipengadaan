<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;
use setasign\Fpdi\PdfParser\StreamReader;

/**
 * CertificatePdfGenerator — render HTML & template PDF menjadi binary PDF resmi.
 *
 * Mendukung 2 mode operasi:
 *   1. Direct HTML-to-PDF via Dompdf (untuk template polos & background PNG/JPG).
 *   2. Vector PDF Placeholder Replacement & Merge via CleanTemplateFpdi + Dompdf
 *      (bila template menggunakan file PDF):
 *      - Seluruh teks {{placeholder}} asli di dalam stream PDF dasar dihapus secara otomatis
 *        agar tidak bertumpuk di belakang teks hasil generate.
 *      - Halaman pertama PDF asli dipertahankan sebagai base layer vektor utuh (100% tajam).
 *      - Nilai pengganti placeholder (nama peserta, nomor sertifikat, NIP, pejabat, QR code)
 *        ditempatkan tepat pada koordinat placeholder PDF tersebut.
 */
class CertificatePdfGenerator
{
    public function __construct()
    {
    }

    /**
     * Render HTML string menjadi PDF binary.
     * Mendukung opsi background file PDF (string path fisik atau array template).
     *
     * @param string                            $html
     * @param string|array<string, mixed>|null $backgroundPdfOrTemplate
     * @return string Byte binary PDF (%PDF-...)
     *
     * @throws RuntimeException bila PDF tidak berhasil di-render.
     */
    public function generate(string $html, mixed $backgroundPdfOrTemplate = null): string
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }

        $pdfBackgroundPath = $this->resolveBackgroundPdfPath($backgroundPdfOrTemplate);

        if ($pdfBackgroundPath !== null && is_file($pdfBackgroundPath)) {
            return $this->generateWithPdfBackground($html, $pdfBackgroundPath);
        }

        return $this->generateStandardHtmlPdf($html);
    }

    /**
     * Mode 1: Render standar Dompdf HTML ke PDF.
     */
    private function generateStandardHtmlPdf(string $html): string
    {
        $dompdf = $this->createDompdfInstance();

        $cleanHtml = $this->sanitizeHtml($html);
        $dompdf->loadHtml($cleanHtml, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Injeksi Metadata TTE & Keamanan Dokumen setelah render (sesuai spesifikasi Dompdf canvas)
        $dompdf->addInfo('Title', 'Sertifikat Kelulusan Resmi PBJ — BegawiPengadaan');
        $dompdf->addInfo('Author', 'Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa');
        $dompdf->addInfo('Subject', 'Dokumen Sertifikat Digital Ber-Tanda Tangan Elektronik');
        $dompdf->addInfo('Keywords', 'TTE, BegawiPengadaan, Sertifikat Digital, PBJ, Pemprov Lampung');
        $dompdf->addInfo('Creator', 'BegawiPengadaan Electronic Signature System');

        $output = $dompdf->output();
        unset($dompdf);
        if (! is_string($output) || $output === '') {
            throw new RuntimeException('PDF generator tidak menghasilkan output.');
        }

        if (! str_starts_with($output, '%PDF-')) {
            throw new RuntimeException('Output PDF tidak valid (bukan %PDF- header).');
        }

        return $output;
    }

    /**
     * Mode 2: Bersihkan token {{placeholder}} dari PDF asli + gabungkan dengan layer nilai pengganti.
     */
    private function generateWithPdfBackground(string $html, string $backgroundPdfPath): string
    {
        try {
            $fpdi = new CleanTemplateFpdi();
            $fpdi->setStripPlaceholders(true);

            $pageCount = $fpdi->setSourceFile($backgroundPdfPath);
            if ($pageCount < 1) {
                throw new RuntimeException('File PDF template tidak memiliki halaman valid.');
            }

            // Ambil halaman 1 dari background PDF (dengan token {{...}} telah dibersihkan dari stream)
            $bgTplId = $fpdi->importPage(1);
            $bgSize  = $fpdi->getTemplateSize($bgTplId);

            $orientation = $bgSize['orientation'] ?? 'L';
            $widthMm     = (float) ($bgSize['width'] ?? 297.0);
            $heightMm    = (float) ($bgSize['height'] ?? 210.0);

            if ($widthMm <= 10.0) {
                $widthMm = 297.0;
            }
            if ($heightMm <= 10.0) {
                $heightMm = 210.0;
            }

            // Render HTML overlay tepat sesuai dimensi halaman PDF template (1 mm = 72 / 25.4 pt)
            $widthPt  = round($widthMm * 72.0 / 25.4, 2);
            $heightPt = round($heightMm * 72.0 / 25.4, 2);

            $transparentHtml = $this->injectTransparentStyle($html, $widthMm, $heightMm);

            $dompdf = $this->createDompdfInstance();
            $dompdf->loadHtml($transparentHtml, 'UTF-8');
            $dompdf->setPaper([0.0, 0.0, $widthPt, $heightPt]);
            $dompdf->render();

            $overlayPdfBytes = $dompdf->output();
            if (! is_string($overlayPdfBytes) || ! str_starts_with($overlayPdfBytes, '%PDF-')) {
                throw new RuntimeException('Gagal menghasilkan overlay PDF dari template HTML.');
            }

            $fpdi->AddPage($orientation, [$widthMm, $heightMm]);

            // Layer 1: Template PDF asli di bawah (sudah bersih dari teks {{placeholder}})
            $fpdi->useTemplate($bgTplId, 0, 0, $widthMm, $heightMm);

            // Layer 2: Nilai pengganti placeholder (Nama peserta, nomor, NIP, QR code) di atasnya
            $fpdi->setStripPlaceholders(false);
            $fpdi->setSourceFile(StreamReader::createByString($overlayPdfBytes));
            $overlayTplId = $fpdi->importPage(1);
            $fpdi->useTemplate($overlayTplId, 0, 0, $widthMm, $heightMm);

            // Injeksi Metadata TTE & Keamanan Dokumen ke FPDI
            $fpdi->SetTitle('Sertifikat Kelulusan Resmi PBJ — BegawiPengadaan');
            $fpdi->SetAuthor('Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa');
            $fpdi->SetSubject('Dokumen Sertifikat Digital Ber-Tanda Tangan Elektronik');
            $fpdi->SetKeywords('TTE, BegawiPengadaan, Sertifikat Digital, PBJ, Pemprov Lampung');
            $fpdi->SetCreator('BegawiPengadaan Electronic Signature System');

            $finalPdf = $fpdi->Output('S');
            if (! is_string($finalPdf) || ! str_starts_with($finalPdf, '%PDF-')) {
                throw new RuntimeException('FPDI gagal menggabungkan template PDF.');
            }

            return $finalPdf;
        } catch (\Throwable $e) {
            log_message('error', 'PDF merge with CleanTemplateFpdi failed: ' . $e->getMessage() . '. Fallback to Dompdf standard.');
            return $this->generateStandardHtmlPdf($html);
        }
    }

    /**
     * Resolusi path file PDF background dari parameter (string path atau array template).
     */
    public function resolveBackgroundPdfPath(mixed $param): ?string
    {
        if (is_string($param) && $param !== '') {
            $abs = $param;
            if (! is_file($abs)) {
                $abs = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, WRITEPATH . ltrim($param, '/\\'));
            }
            if (is_file($abs) && strtolower(pathinfo($abs, PATHINFO_EXTENSION)) === 'pdf') {
                return $abs;
            }
        }

        if (is_array($param)) {
            $renderer = new CertificateTemplateRenderer();
            $path     = $renderer->getBackgroundPhysicalPath($param);
            if ($path && is_file($path) && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf') {
                return $path;
            }
        }

        return null;
    }

    private function createDompdfInstance(): Dompdf
    {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('chroot', [APPPATH, WRITEPATH, FCPATH]);

        return new Dompdf($options);
    }

    private function injectTransparentStyle(string $html, float $widthMm = 297.0, float $heightMm = 210.0): string
    {
        $style = '<style>
          @page {
            size: ' . $widthMm . 'mm ' . $heightMm . 'mm;
            margin: 0;
          }
          html, body {
            width: ' . $widthMm . 'mm !important;
            height: ' . $heightMm . 'mm !important;
            background: transparent !important;
            background-color: transparent !important;
          }
          .cert-frame-default, .cert-frame-inner, .cert-overlay-container, .cert-pdf-placeholder-canvas {
            width: ' . $widthMm . 'mm !important;
            height: ' . $heightMm . 'mm !important;
            background: transparent !important;
            background-color: transparent !important;
          }
        </style>';

        if (stripos($html, '</head>') !== false) {
            return str_ireplace('</head>', $style . '</head>', $html);
        }

        return $style . $html;
    }

    /**
     * Validasi HTML minimal agar Dompdf tidak bingung.
     */
    private function sanitizeHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            throw new RuntimeException('HTML kosong — tidak bisa render PDF.');
        }
        return $html;
    }
}

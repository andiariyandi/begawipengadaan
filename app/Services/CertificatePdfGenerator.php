<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;

/**
 * CertificatePdfGenerator — render HTML menjadi binary PDF.
 *
 * Library: dompdf/dompdf (sudah ada di composer.json).
 * Konfigurasi: A4 landscape, font fallback Helvetica, remote OFF
 * (gambar lokal via filesystem path aman dipakai di setup ini),
 * HTML5 parser aktif.
 *
 * Output: string byte PDF siap tulis ke disk.
 */
class CertificatePdfGenerator
{
    public function __construct()
    {
        // tidak ada dependency injection — Dompdf self-contained.
    }

    /**
     * Render HTML string menjadi PDF binary.
     *
     * @throws RuntimeException bila PDF tidak berhasil di-render.
     */
    public function generate(string $html): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false);    // local filesystem/img only
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('chroot', [APPPATH, WRITEPATH, FCPATH]);

        $dompdf = new Dompdf($options);

        // Bersihkan tag dasar agar konsisten.
        $cleanHtml = $this->sanitizeHtml($html);
        $dompdf->loadHtml($cleanHtml, 'UTF-8');

        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $output = $dompdf->output();
        if (! is_string($output) || $output === '') {
            throw new RuntimeException('PDF generator tidak menghasilkan output.');
        }

        // Dompdf biasanya emit sebagai application/pdf stream. Validasi minimal:
        if (! str_starts_with($output, '%PDF-')) {
            throw new RuntimeException('Output PDF tidak valid (bukan %PDF- header).');
        }

        return $output;
    }

    /**
     * Validasi HTML minimal agar Dompdf tidak bingung. Beberapa template
     * yang dibuat admin berpotensi lupa <html> / <head>; renderer kita
     * sudah menyediakannya, tapi kalau HTML sudah dimulai dengan doctype
     * kita biarkan apa adanya.
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

<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    /**
     * Render HTML jadi PDF dan simpan langsung ke file.
     * $orientation: 'landscape' (default, dipakai sertifikat) atau 'portrait' (dipakai laporan Dashboard).
     */
    public function generateToFile(string $html, string $fullPath, string $orientation = 'landscape'): void
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true); // supaya <img src="data:..."> (QR code) ikut ter-render
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();

        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_put_contents($fullPath, $dompdf->output()) === false) {
            throw new \RuntimeException('File PDF tidak dapat ditulis.');
        }
    }
}

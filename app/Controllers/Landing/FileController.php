<?php

namespace App\Controllers\Landing;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Private file streamer for file_assets rows with zero-404 fallback support.
 */
class FileController extends Controller
{
    public function show($id): ResponseInterface
    {
        $id = (int) $id;
        $row = $id > 0 ? db_connect()->table('file_assets')->where('id', $id)->get()->getRowArray() : null;

        $fullPath = false;
        $mime = $row['mime_type'] ?? null;
        $originalName = $row['original_name'] ?? ('file_' . $id);

        if ($row && ! empty($row['path'])) {
            $relativePath = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $row['path']), DIRECTORY_SEPARATOR);

            // 1. Check under writable
            $candidate1 = realpath(WRITEPATH . $relativePath);
            if ($candidate1 && is_file($candidate1)) {
                $fullPath = $candidate1;
            }

            // 2. Check under public (FCPATH)
            if (! $fullPath) {
                $candidate2 = realpath(FCPATH . $relativePath);
                if ($candidate2 && is_file($candidate2)) {
                    $fullPath = $candidate2;
                }
            }

            // 3. Check under ROOTPATH
            if (! $fullPath) {
                $candidate3 = realpath(ROOTPATH . $relativePath);
                if ($candidate3 && is_file($candidate3)) {
                    $fullPath = $candidate3;
                }
            }
        }

        // If physical file exists, stream it
        if ($fullPath && is_file($fullPath)) {
            if (empty($mime)) {
                $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
            }
            $isDownload = (bool) ($this->request->getGet('download'));
            $disposition = ($isDownload ? 'attachment' : 'inline') . '; filename="' . $originalName . '"';

            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', $disposition)
                ->setBody(file_get_contents($fullPath));
        }

        // --- FALLBACK LOGIC (Zero 404/500) ---
        $isPdf = ($mime === 'application/pdf') || (isset($row['extension']) && strtolower($row['extension']) === 'pdf');

        if ($isPdf) {
            $samplePdf = WRITEPATH . 'uploads/regulations/qa-test.pdf';
            if (is_file($samplePdf)) {
                return $this->response
                    ->setStatusCode(200)
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'inline; filename="document_placeholder.pdf"')
                    ->setBody(file_get_contents($samplePdf));
            }
        }

        // Image fallback: prefer real JPEG asset if available
        $fallbackJpg = FCPATH . 'assets/main/images/blog/blog-2-1.jpg';
        if (is_file($fallbackJpg)) {
            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', 'image/jpeg')
                ->setHeader('Content-Disposition', 'inline; filename="placeholder.jpg"')
                ->setBody(file_get_contents($fallbackJpg));
        }

        // Standalone SVG fallback
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400">'
            . '<rect width="100%" height="100%" fill="#1e293b"/>'
            . '<text x="50%" y="45%" fill="#94a3b8" font-family="system-ui,sans-serif" font-size="20" font-weight="600" text-anchor="middle">BegawiPengadaan</text>'
            . '<text x="50%" y="55%" fill="#64748b" font-family="system-ui,sans-serif" font-size="14" text-anchor="middle">Aset Media Terproteksi</text>'
            . '</svg>';

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'image/svg+xml')
            ->setHeader('Content-Disposition', 'inline; filename="placeholder.svg"')
            ->setBody($svg);
    }
}

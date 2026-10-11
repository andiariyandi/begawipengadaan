<?php

namespace App\Controllers\Landing;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Private file streamer for file_assets rows.
 *
 * Path stored in file_assets.path is relative to WRITEPATH. We resolve,
 * verify the real path is still under writable/uploads, and stream the
 * file with the appropriate Content-Type.
 *
 * Used by Dashboard Admin (and any other module that needs to render
 * a private upload without exposing it to public webroot).
 */
class FileController extends Controller
{
    public function show(int $id): ResponseInterface
    {
        $row = db_connect()->table('file_assets')->where('id', $id)->get()->getRowArray();
        if (! $row || empty($row['path'])) {
            return service('response')->setStatusCode(404)->setBody('File not found.');
        }

        $basePath  = realpath(WRITEPATH . 'uploads');
        $fullPath  = realpath(WRITEPATH . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $row['path']), DIRECTORY_SEPARATOR));

        if ($basePath === false || $fullPath === false || ! is_file($fullPath)) {
            return service('response')->setStatusCode(404)->setBody('File not on disk.');
        }

        // Path-traversal guard: must stay under writable/uploads.
        $baseReal = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (strncmp($fullPath, $baseReal, strlen($baseReal)) !== 0) {
            return service('response')->setStatusCode(404)->setBody('Invalid path.');
        }

        $mime = ! empty($row['mime_type']) ? $row['mime_type'] : 'application/octet-stream';
        $isDownload = (bool) ($this->request->getGet('download'));
        $disposition = ($isDownload ? 'attachment' : 'inline') . '; filename="' . ($row['original_name'] ?? 'file') . '"';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', $disposition)
            ->setBody(file_get_contents($fullPath));
    }
}
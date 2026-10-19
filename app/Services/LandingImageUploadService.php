<?php

namespace App\Services;

use CodeIgniter\HTTP\Files\UploadedFile;

class LandingImageUploadService
{
    /**
     * Handle image upload for Landing CMS modules.
     *
     * @param UploadedFile|null $file
     * @param string $subfolder
     * @param string|null $currentPath
     * @param string $resourceType
     * @return string|null The relative path to the image, or existing path if unchanged.
     */
    public static function upload(
        ?UploadedFile $file,
        string $subfolder = 'general',
        ?string $currentPath = null,
        string $resourceType = 'landing_image'
    ): ?string {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return $currentPath;
        }

        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/jpg',
            'image/gif',
            'image/svg+xml'
        ];

        $mime = $file->getMimeType();
        if (! in_array($mime, $allowedMimes, true)) {
            session()->setFlashdata('error', 'Format file gambar harus JPG, JPEG, PNG, WEBP, atau SVG.');
            return $currentPath;
        }

        // Limit size to 5MB (5 * 1024 * 1024)
        if ($file->getSize() > 5242880) {
            session()->setFlashdata('error', 'Ukuran file gambar maksimal 5 MB.');
            return $currentPath;
        }

        $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . $subfolder;
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $storedName = $file->getRandomName();
        if ($file->move($targetDir, $storedName)) {
            $relativePath = 'uploads/landing/' . $subfolder . '/' . $storedName;

            // Register asset in file_assets table
            try {
                $now = date('Y-m-d H:i:s');
                $db  = db_connect();
                $db->table('file_assets')->insert([
                    'disk'             => 'local',
                    'path'             => $relativePath,
                    'original_name'    => $file->getClientName() ?: $storedName,
                    'stored_name'      => $storedName,
                    'extension'        => $file->getClientExtension() ?: 'jpg',
                    'mime_type'        => $mime,
                    'size_bytes'       => (int) ($file->getSize() ?: 0),
                    'visibility'       => 'public',
                    'lifecycle_status' => 'available',
                    'resource_type'    => $resourceType,
                    'resource_id'      => null,
                    'uploaded_by'      => (int) (session()->get('user_id') ?: 1),
                    'available_at'     => $now,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'Landing image asset registration failed: ' . $e->getMessage());
            }

            return $relativePath;
        }

        return $currentPath;
    }
}

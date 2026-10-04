<?php

namespace App\Services;

use CodeIgniter\Files\File;

/**
 * File storage service: persist file_assets metadata + move uploaded file
 * to the configured disk (private: writable/uploads).
 *
 * Used by RegulationService, CertificateGenerationService, etc.
 */
class FileStorageService
{
    private const ALLOWED_MIMES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/png',
        'image/jpeg',
    ];

    public function storeUpload(\CodeIgniter\HTTP\Files\UploadedFile $file, string $resourceType, ?int $resourceId, ?int $uploaderId, string $visibility = 'internal'): ?array
    {
        if (! $file->isValid()) {
            log_message('error', '[FileStorage] file->isValid() returned false: ' . $file->getErrorString());
            return null;
        }
        $mime = $file->getMimeType();
        if (! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new \InvalidArgumentException('Tipe file tidak diizinkan: ' . $mime);
        }

        $diskName = 'local';
        $targetDir = WRITEPATH . 'uploads/' . trim($resourceType, '/');
        if (! is_dir($targetDir) && ! @mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            throw new \RuntimeException('Direktori upload tidak dapat dibuat: ' . $targetDir);
        }

        $originalName = $file->getClientName() ?: basename($file->getPath() ?? '');
        $extension    = $file->getClientExtension() ?: ($file->getExtension() ?: null);
        $storedName   = $file->hasMoved() ? $file->getFilename() : $file->getRandomName();
        $fullPath     = $targetDir . DIRECTORY_SEPARATOR . $storedName;

        if (! $file->hasMoved() && ! $file->move($targetDir, $storedName)) {
            log_message('error', '[FileStorage] move failed: ' . $file->getErrorString());
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $relativePath = 'uploads/' . trim($resourceType, '/') . '/' . $storedName;

        $ok = db_connect()->table('file_assets')->insert([
            'disk'             => $diskName,
            'path'             => $relativePath,
            'original_name'    => $originalName,
            'stored_name'      => $storedName,
            'extension'        => $extension,
            'mime_type'        => $mime,
            'size_bytes'       => (int) $file->getSize(),
            'checksum'         => @hash_file('sha256', $fullPath) ?: null,
            'visibility'       => $visibility,
            'lifecycle_status' => 'available',
            'resource_type'    => $resourceType,
            'resource_id'      => $resourceId,
            'uploaded_by'      => $uploaderId,
            'available_at'     => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ], true);

        if (! $ok) {
            log_message('error', '[FileStorage] file_assets insert failed for resource ' . $resourceType);
            @unlink($fullPath);
            return null;
        }
        $inserted = (int) db_connect()->insertID();

        try {
            (new AuditService())->log($uploaderId, 'file.upload', 'file_assets', 'file_asset', $inserted, [
                'resource_type' => $resourceType,
                'resource_id'   => $resourceId,
                'size_bytes'    => (int) $file->getSize(),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', '[FileStorage] audit log failed: ' . $e->getMessage());
        }

        return [
            'id'             => $inserted,
            'path'           => $relativePath,
            'full_path'      => $fullPath,
            'original_name'  => $originalName,
            'stored_name'    => $storedName,
            'extension'      => $extension,
            'mime_type'      => $mime,
            'size_bytes'     => (int) $file->getSize(),
        ];
    }
}
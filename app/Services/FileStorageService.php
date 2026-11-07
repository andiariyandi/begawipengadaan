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

    /**
     * Daftarkan berkas aset ke tabel `file_assets` secara terpusat dengan standarisasi 16 kolom kanonikal.
     * Dilengkapi mekanisme Atomic File Rollback: bila insert gagal, otomatis menghapus berkas fisik di disk.
     *
     * @param array<string, mixed> $meta
     * @param string|null $physicalFilePath Path absolut ke berkas fisik untuk atomic rollback jika gagal
     * @return int Insert ID dari record file_assets yang baru dibuat
     * @throws \RuntimeException jika insert database gagal
     */
    public function registerFileAsset(array $meta, ?string $physicalFilePath = null): int
    {
        $now  = date('Y-m-d H:i:s');
        $path = (string) ($meta['path'] ?? '');

        // Otomatis deteksi berkas fisik jika path fisik tidak dipasok secara eksplisit
        if ($physicalFilePath === null && $path !== '') {
            $candidateFc = FCPATH . ltrim($path, '/\\');
            $candidateWr = WRITEPATH . ltrim($path, '/\\');
            if (is_file($candidateFc)) {
                $physicalFilePath = $candidateFc;
            } elseif (is_file($candidateWr)) {
                $physicalFilePath = $candidateWr;
            }
        }

        $storedName = $meta['stored_name'] ?? ($path !== '' ? basename($path) : 'file_' . time());
        $origName   = $meta['original_name'] ?? $storedName;
        $ext        = $meta['extension'] ?? ($path !== '' ? strtolower((string) pathinfo($path, PATHINFO_EXTENSION)) : null);
        $sizeBytes  = isset($meta['size_bytes']) ? (int) $meta['size_bytes'] : ($physicalFilePath && is_file($physicalFilePath) ? (int) filesize($physicalFilePath) : 0);
        $checksum   = $meta['checksum'] ?? ($physicalFilePath && is_file($physicalFilePath) ? (@hash_file('sha256', $physicalFilePath) ?: null) : null);

        $row = [
            'disk'             => (string) ($meta['disk'] ?? 'local'),
            'path'             => $path,
            'original_name'    => (string) $origName,
            'stored_name'      => (string) $storedName,
            'extension'        => $ext ?: null,
            'mime_type'        => (string) ($meta['mime_type'] ?? 'application/octet-stream'),
            'size_bytes'       => $sizeBytes,
            'checksum'         => $checksum,
            'visibility'       => (string) ($meta['visibility'] ?? 'internal'),
            'lifecycle_status' => (string) ($meta['lifecycle_status'] ?? 'available'),
            'resource_type'    => isset($meta['resource_type']) ? (string) $meta['resource_type'] : null,
            'resource_id'      => isset($meta['resource_id']) && (int) $meta['resource_id'] > 0 ? (int) $meta['resource_id'] : null,
            'uploaded_by'      => isset($meta['uploaded_by']) && (int) $meta['uploaded_by'] > 0 ? (int) $meta['uploaded_by'] : null,
            'available_at'     => $meta['available_at'] ?? $now,
            'created_at'       => $meta['created_at'] ?? $now,
            'updated_at'       => $meta['updated_at'] ?? $now,
        ];

        try {
            $db = db_connect();
            $ok = $db->table('file_assets')->insert($row);
            if (! $ok) {
                throw new \RuntimeException('Gagal menyisipkan metadata file_assets ke database.');
            }
            return (int) $db->insertID();
        } catch (\Throwable $e) {
            log_message('error', '[FileStorageService] registerFileAsset failed: ' . $e->getMessage());
            // Atomic File Rollback
            if ($physicalFilePath !== null && is_file($physicalFilePath)) {
                @unlink($physicalFilePath);
                log_message('error', '[FileStorageService] Atomic File Rollback executed. Deleted orphaned physical file: ' . $physicalFilePath);
            }
            throw $e;
        }
    }
}

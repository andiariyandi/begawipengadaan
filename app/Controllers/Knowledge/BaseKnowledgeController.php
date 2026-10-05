<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;
use App\Services\Identity\AuthorizationService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Shared gate untuk seluruh controller Bunga Rampai.
 *
 * Setiap method yang butuh write akses memanggil assertCanManage().
 * Read akses (index, detail) otomatis aman karena sudah lewat 'auth' filter
 * (dashboard filter) + permission dashboard.view.
 */
abstract class BaseKnowledgeController extends BaseController
{
    protected ?AuthorizationService $authz = null;

    /**
     * @var \CodeIgniter\Database\BaseConnection
     */
    protected $db;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->authz = new AuthorizationService();
        $this->db = db_connect();
    }

    protected function assertCanManage()
    {
        if ($this->authz === null || ! $this->authz->can('kms.manage')) {
            $roles = (array) session()->get('roles');
            if (! in_array('super_admin', $roles, true) && ! in_array('admin', $roles, true)) {
                return service('response')->setStatusCode(403)->setBody(
                    '<h1>403 Forbidden</h1><p>Anda tidak memiliki izin mengelola Bunga Rampai.</p>'
                );
            }
        }
        return null;
    }

    protected function currentUserId(): int
    {
        return (int) session()->get('user_id');
    }

    /**
     * Upload file ke file_assets (record). File fisik disimpan ke writable/uploads/knowledge/.
     * Mengembalikan file_assets.id atau null jika tidak ada file diupload.
     */
    protected function uploadFileAsset(
        ?\CodeIgniter\HTTP\Files\UploadedFile $file,
        string $resourceType,
        string $visibility = 'internal',
        string $lifecycleStatus = 'available'
    ): ?int {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }
        $now = date('Y-m-d H:i:s');
        $uploadDir = WRITEPATH . 'uploads/knowledge';
        if (! is_dir($uploadDir) && ! @mkdir($uploadDir, 0755, true)) {
            return null;
        }
        $storedName = $file->getRandomName();
        $file->move($uploadDir, $storedName);
        $relativePath = 'uploads/knowledge/' . $storedName;
        $data = [
            'disk'            => 'local',
            'path'            => $relativePath,
            'original_name'   => $file->getClientName() ?: $storedName,
            'stored_name'     => $storedName,
            'extension'       => $file->getClientExtension() ?: 'bin',
            'mime_type'       => $file->getClientMimeType() ?: 'application/octet-stream',
            'size_bytes'      => (int) ($file->getSize() ?: 0),
            'visibility'      => $visibility,
            'lifecycle_status'=> $lifecycleStatus,
            'resource_type'   => $resourceType,
            'uploaded_by'     => $this->currentUserId() ?: null,
            'created_at'      => $now,
            'updated_at'      => $now,
        ];
        $this->db->table('file_assets')->insert($data);
        return (int) $this->db->insertID();
    }
}
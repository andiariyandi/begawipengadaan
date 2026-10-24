<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Models\Certificate\CertificateModel;
use App\Services\AuditService;
use App\Services\CertificateGenerator;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

/**
 * Controlled PDF download for certificates.
 *
 * Endpoints:
 *   GET /dashboard/sertifikat/{id}/unduh
 *   GET /workspace/peserta/sertifikat/{id}/unduh
 */
class CertificateDownloadController extends BaseController
{
    private ConnectionInterface $db;
    private CertificateModel $certificateModel;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db               = db_connect();
        $this->certificateModel = new CertificateModel();
        $this->auditService     = new AuditService();
    }

    /**
     * GET /dashboard/sertifikat/{id}/unduh
     */
    public function download(string $id)
    {
        $actorId = (int) (session()->get('user_id') ?? 0);
        if ($actorId < 1) {
            return redirect()->to('/dashboard/masuk')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        if (! ctype_digit($id) || (int) $id < 1) {
            return $this->notFoundResponse('ID sertifikat tidak valid.');
        }
        $certificateId = (int) $id;

        $certificate = $this->certificateModel->find($certificateId);
        if (! $certificate) {
            return $this->notFoundResponse('Sertifikat tidak ditemukan.');
        }
        if (($certificate['status'] ?? '') !== 'issued') {
            return $this->forbiddenResponse('Sertifikat belum dapat diunduh (status: ' . $certificate['status'] . ').');
        }

        // Authorization
        try {
            $this->assertDownloadAccess($actorId, $certificate);
        } catch (RuntimeException $e) {
            return $this->forbiddenResponse($e->getMessage());
        }

        $pdfFileId = (int) ($certificate['pdf_file_id'] ?? 0);
        $fileRow   = $pdfFileId > 0
            ? $this->db->table('file_assets')->where('id', $pdfFileId)->get()->getRowArray()
            : null;
        $fullPath  = $fileRow ? (WRITEPATH . ltrim((string) $fileRow['path'], '/')) : '';

        // Auto-regenerate PDF jika belum tersedia di DB atau file fisik hilang di disk
        if ($pdfFileId < 1 || ! $fileRow || ! is_file($fullPath)) {
            try {
                $regen = (new CertificateGenerator())->regeneratePdfForCertificate($certificateId, $actorId, true);
                $pdfFileId = (int) $regen['pdf_file_id'];
                $certificate['certificate_number'] = $regen['certificate_number'];
                $fileRow = $this->db->table('file_assets')->where('id', $pdfFileId)->get()->getRowArray();
                $fullPath = $fileRow ? (WRITEPATH . ltrim((string) $fileRow['path'], '/')) : '';
            } catch (\Throwable $e) {
                return $this->notFoundResponse('File PDF sertifikat belum tersedia: ' . $e->getMessage());
            }
        }

        if (! $fileRow || ($fileRow['lifecycle_status'] ?? '') !== 'available' || ! is_file($fullPath)) {
            return $this->notFoundResponse('File fisik sertifikat tidak ditemukan di disk.');
        }

        // Audit
        $this->auditService->log($actorId, 'certificate.downloaded', 'certificate', 'certificate', $certificateId, [
            'certificate_number' => $certificate['certificate_number'] ?? null,
            'pdf_file_id'        => $pdfFileId,
        ]);

        // Stream
        $basename = basename($fullPath);
        return $this->response->download($fullPath, null)
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $basename . '"')
            ->setHeader('Cache-Control', 'private, max-age=0, must-revalidate')
            ->setHeader('X-Content-Type-Options', 'nosniff');
    }

    /**
     * Authorization: admin/super_administrator/operator OR pemilik sertifikat sendiri.
     */
    private function assertDownloadAccess(int $actorId, array $certificate): void
    {
        $isOwner = (int) ($certificate['user_id'] ?? 0) === $actorId;
        if ($isOwner) {
            $row = $this->db->table('users')->where('id', $actorId)->get()->getRowArray();
            if (! $row || ($row['account_status'] ?? '') !== 'active') {
                throw new RuntimeException('Akun tidak aktif.');
            }
            return;
        }

        $hasPerm = $this->db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where(['ur.user_id' => $actorId, 'ur.status' => 'active'])
            ->groupStart()
            ->whereIn('r.key', ['administrator', 'super_administrator', 'operator'])
            ->orWhereIn('r.name', ['Administrator', 'Super Administrator', 'Operator'])
            ->groupEnd()
            ->countAllResults() > 0;
        if (! $hasPerm) {
            throw new RuntimeException('Anda tidak berhak mengunduh sertifikat ini.');
        }
    }

    private function notFoundResponse(string $msg)
    {
        $response = service('response');
        if ($response instanceof ResponseInterface) {
            $response->setStatusCode(404);
        }
        return view('errors/html/error_404', [
            'message' => $msg,
        ]);
    }

    private function forbiddenResponse(string $msg)
    {
        $response = service('response');
        if ($response instanceof ResponseInterface) {
            $response->setStatusCode(403);
        }
        return view('errors/html/error_403', [
            'message' => $msg,
        ]);
    }
}
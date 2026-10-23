<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Models\Certificate\CertificateModel;
use App\Services\AuditService;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

/**
 * Controlled PDF download for certificates.
 *
 * Endpoints:
 *   GET /dashboard/sertifikat/{id}/unduh
 *
 * Authorization:
 *   - admin / super_administrator → semua sertifikat yang issued
 *   - pemilik sertifikat sendiri → sertifikat miliknya
 *
 * Audit: certificate.downloaded.
 *
 * TIDAK expose path upload publik. Stream langsung dari
 * writable/uploads/certificate/{file} dengan header
 * Content-Type application/pdf dan Content-Disposition attachment.
 */
class CertificateDownloadController extends BaseController
{
    private ConnectionInterface $db;
    private CertificateModel $certificateModel;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db = db_connect();
        $this->certificateModel = new CertificateModel();
        $this->auditService     = new AuditService();
    }

    /**
     * GET /dashboard/sertifikat/{id}/unduh
     */
    public function download(string $id)
    {
        $actorId = (int) session()->get('user_id');
        if (session()->get('isLoggedIn') !== true || $actorId < 1) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Silakan masuk untuk mengunduh sertifikat.');
        }

        $certificateId = (int) $id;
        if ($certificateId < 1) {
            return $this->notFoundResponse('ID sertifikat tidak valid.');
        }

        // Ambil sertifikat + relasi pdf
        $certificate = $this->certificateModel->withFullDetail($certificateId);
        if (! $certificate) {
            return $this->notFoundResponse('Sertifikat tidak ditemukan.');
        }
        if (($certificate['status'] ?? '') !== 'issued') {
            return $this->forbiddenResponse('Sertifikat belum dapat diunduh (status: ' . $certificate['status'] . ').');
        }

        $pdfFileId = (int) ($certificate['pdf_file_id'] ?? 0);
        if ($pdfFileId < 1) {
            return $this->notFoundResponse('File PDF sertifikat belum tersedia.');
        }

        // Authorization
        $this->assertDownloadAccess($actorId, $certificate);

        // Ambil file_assets row
        $fileRow = $this->db->table('file_assets')
            ->where('id', $pdfFileId)
            ->get()
            ->getRowArray();
        if (! $fileRow) {
            return $this->notFoundResponse('Asset file sertifikat tidak ditemukan.');
        }
        if (($fileRow['lifecycle_status'] ?? '') !== 'available') {
            return $this->forbiddenResponse('File sertifikat tidak tersedia.');
        }

        $relative = ltrim((string) $fileRow['path'], '/');
        $fullPath = WRITEPATH . $relative;
        if (! is_file($fullPath)) {
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
     * Authorization: admin/super_administrator OR pemilik sertifikat sendiri.
     */
    private function assertDownloadAccess(int $actorId, array $certificate): void
    {
        $isOwner = (int) ($certificate['user_id'] ?? 0) === $actorId;
        if ($isOwner) {
            // pemilik boleh unduh miliknya sendiri selama akun aktif
            $row = $this->db->table('users')->where('id', $actorId)->get()->getRowArray();
            if (! $row || ($row['account_status'] ?? '') !== 'active') {
                throw new RuntimeException('Akun tidak aktif.');
            }
            return;
        }

        // Bukan pemilik → cek role administrator/super_administrator (r.key sesuai schema v4)
        $hasPerm = $this->db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where(['ur.user_id' => $actorId, 'ur.status' => 'active'])
            ->groupStart()
            ->where('r.key', 'administrator')
            ->orWhere('r.key', 'super_administrator')
            ->orWhere('r.name', 'Administrator')
            ->orWhere('r.name', 'Super Administrator')
            ->groupEnd()
            ->countAllResults() > 0;
        if (! $hasPerm) {
            throw new RuntimeException('Anda tidak berhak mengunduh sertifikat ini.');
        }
    }

    private function notFoundResponse(string $msg)
    {
        return view('dashboard/errors/404', [
            'title'      => 'Sertifikat tidak ditemukan',
            'pageTitle'  => 'Sertifikat tidak ditemukan',
            'activeMenu' => 'certificates',
            'message'    => $msg,
        ]);
    }

    private function forbiddenResponse(string $msg)
    {
        $response = service('response');
        if ($response instanceof ResponseInterface) {
            $response->setStatusCode(403);
        }
        return view('dashboard/errors/403', [
            'title'      => 'Akses Ditolak',
            'pageTitle'  => 'Akses Ditolak',
            'activeMenu' => 'certificates',
            'message'    => $msg,
        ]);
    }
}

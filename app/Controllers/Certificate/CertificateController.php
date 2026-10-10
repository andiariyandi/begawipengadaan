<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Models\Certificate\CertificateModel;
use App\Services\AuditService;
use RuntimeException;

/**
 * Dashboard admin sertifikat (v4 schema).
 *
 * Active flow (5): index, detail, edit, update, revoke.
 *
 * CATATAN PENTING:
 *   - Tidak ada create()/store(). Sertifikat TIDAK dibuat manual dari
 *     form admin. Lifecycle sertifikat:
 *
 *         Eligibility  →  Approval  →  CertificateGenerator  →  Issued
 *
 *     Baris certificates hanya dibuat oleh CertificateGenerator dari
 *     approval yang sudah berstatus 'approved'
 *     (lihat CertificateApprovalController::issue()).
 *
 *   - certificate_number: atomik via CertificateSequenceModel
 *     (SERT/{YEAR}/{NNNNN}). TIDAK pernah MAX()+UPDATE.
 *   - verification_token: random_bytes(32), hanya hash sha256 di DB
 *     (verification_token_hash). Token clear hanya muncul SEKALI di flash
 *     session setelah generator().
 *   - Revoke = soft-action: status='revoked' + revoked_at + revoked_by +
 *     revocation_reason. Tidak ada delete.
 *
 * Audit:
 *   certificate.updated, certificate.revoked
 *   (certificate.created di-emit oleh CertificateGenerator).
 */
class CertificateController extends BaseController
{
    private CertificateModel $certificateModel;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db               = db_connect();
        $this->certificateModel = new CertificateModel();
        $this->auditService     = new AuditService();
    }

    // =============================================================
    // Active v4 dashboard flow
    // =============================================================

    public function index()
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status  = (string) $this->request->getGet('status');
        if ($keyword === '') { $keyword = null; }
        if ($status  === '') { $status  = null; }

        $db = db_connect();
        $stats = [
            'total'            => (int) $db->table('certificates')->countAllResults(),
            'issued'           => (int) $db->table('certificates')->where('status', 'issued')->countAllResults(),
            'revoked'          => (int) $db->table('certificates')->where('status', 'revoked')->countAllResults(),
            'pending_approval' => (int) $db->table('certificate_approvals')->where('status', 'pending')->countAllResults(),
        ];

        $allCertificates = $this->certificateModel->listForAdmin($keyword, $status);
        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_certificates') ?: 1));
        $total = count($allCertificates);
        $certificates = array_slice($allCertificates, ($page - 1) * $perPage, $perPage);

        $pager = service('pager');
        $pager->store('certificates', $page, $perPage, $total, 0);

        return view('dashboard/certificates/index', [
            'title'         => 'Sertifikat | Dashboard BegawiPBJ',
            'pageTitle'     => 'Sertifikat Terbit',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'index',
            'certificates'  => $certificates,
            'stats'         => $stats,
            'pager'         => $pager,
            'perPage'       => $perPage,
            'keyword'       => $keyword ?? '',
            'statusFilter'  => $status  ?? 'all',
        ]);
    }

    public function detail(string $id)
    {
        $certificate = $this->certificateModel->withFullDetail((int) $id);
        if (! $certificate) {
            return $this->notFound();
        }

        // Ambil flash token (satu kali) jika baru diterbitkan.
        $issuedToken = null;
        $flash = session()->getFlashdata('certificate_issued_token');
        if (is_array($flash) && (int) ($flash['id'] ?? 0) === (int) $id) {
            $issuedToken = $flash['token'];
        }

        // Siapkan link verifikasi publik (untuk QR display di view)
        $verifyTokenForDisplay = $issuedToken ?? null;

        return view('dashboard/certificates/detail', [
            'title'                  => 'Detail Sertifikat | Dashboard BegawiPBJ',
            'pageTitle'              => 'Detail Sertifikat',
            'activeMenu'             => 'certificates',
            'activeSubmenu'          => 'index',
            'certificate'            => $certificate,
            'issuedToken'            => $issuedToken,
            'verifyTokenForDisplay'  => $verifyTokenForDisplay,
        ]);
    }

    public function edit(string $id)
    {
        $certificate = $this->certificateModel->withFullDetail((int) $id);
        if (! $certificate) {
            return $this->notFound();
        }

        return view('dashboard/certificates/form', [
            'title'         => 'Ubah Sertifikat | Dashboard BegawiPBJ',
            'pageTitle'     => 'Ubah Sertifikat',
            'formMode'      => 'edit',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'index',
            'certificate'   => $certificate,
        ]);
    }

    public function update(string $id)
    {
        $idInt = (int) $id;
        $before = $this->certificateModel->withFullDetail($idInt);
        if (! $before) {
            return $this->notFound();
        }

        $rules = [
            'status'              => 'required|in_list[pending,issued,revoked,expired]',
            'issued_at'           => 'permit_empty|valid_date',
            'expires_at'          => 'permit_empty|valid_date',
            'revocation_reason'   => 'permit_empty|string|max_length[1000]',
        ];
        if (! $this->validateData((array) $this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = (array) $this->request->getPost();
        $data = [
            'status'            => $post['status'],
            'issued_at'         => $this->normalizeDateTime($post['issued_at']   ?? null, $before['issued_at']  ?? 'now'),
            'expires_at'        => $this->normalizeDateTime($post['expires_at']  ?? null, $before['expires_at'] ?? null),
            'revocation_reason' => $post['revocation_reason'] ?? null,
        ];

        try {
            $this->certificateModel->update($idInt, $data);
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui sertifikat: ' . $e->getMessage());
        }

        $this->auditService->log(
            (int) session()->get('user_id'),
            'certificate.updated',
            'certificate',
            'certificate',
            $idInt,
            [
                'before' => array_intersect_key((array) $before, array_flip(['status','issued_at','expires_at','revocation_reason'])),
                'after'  => $data,
            ]
        );

        return redirect()->to(site_url('dashboard/sertifikat/' . $idInt))
            ->with('success', 'Sertifikat berhasil diperbarui.');
    }

    public function revoke(string $id)
    {
        $idInt = (int) $id;
        $before = $this->certificateModel->withFullDetail($idInt);
        if (! $before) {
            return $this->notFound();
        }

        $rules = [
            'revocation_reason' => 'required|string|min_length[5]|max_length[1000]',
        ];
        if (! $this->validateData((array) $this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $reason  = trim((string) $this->request->getPost('revocation_reason'));
        $actorId = (int) session()->get('user_id');

        $data = [
            'status'            => 'revoked',
            'revoked_at'        => date('Y-m-d H:i:s'),
            'revoked_by'        => $actorId,
            'revocation_reason' => $reason,
        ];

        try {
            $this->certificateModel->update($idInt, $data);
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mencabut sertifikat: ' . $e->getMessage());
        }

        $this->auditService->log(
            $actorId,
            'certificate.revoked',
            'certificate',
            'certificate',
            $idInt,
            [
                'reason'        => $reason,
                'before_status' => $before['status'] ?? null,
            ]
        );

        return redirect()->to(site_url('dashboard/sertifikat'))
            ->with('success', 'Sertifikat telah dicabut.');
    }

    // =============================================================
    // Helpers
    // =============================================================

    private function normalizeDateTime(?string $value, $fallback): ?string
    {
        if ($value === null || $value === '') {
            if ($fallback === null) { return null; }
            if ($fallback === 'now') { return date('Y-m-d H:i:s'); }
            return is_string($fallback) ? $fallback : null;
        }
        $ts = strtotime($value);
        if ($ts === false) { return is_string($fallback) ? $fallback : null; }
        return date('Y-m-d H:i:s', $ts);
    }

    private function notFound()
    {
        return view('dashboard/errors/404', [
            'title'      => 'Sertifikat tidak ditemukan',
            'pageTitle'  => 'Sertifikat tidak ditemukan',
            'activeMenu' => 'certificates',
        ]);
    }
}

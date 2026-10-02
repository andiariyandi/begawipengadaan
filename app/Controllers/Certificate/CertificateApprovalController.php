<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Services\LearningVerticalSliceService;
use RuntimeException;

class CertificateApprovalController extends BaseController
{
    public function index(): string
    {
        $items = db_connect()->table('certificates c')
            ->select('c.id, c.certificate_number, c.status, c.created_at, users.name AS user_name, learning_classes.name AS class_name')
            ->join('enrollments', 'enrollments.id = c.source_id AND c.source_type = \'learning_enrollment\'')
            ->join('users', 'users.id = c.user_id')
            ->join('learning_classes', 'learning_classes.id = enrollments.class_id')
            ->where('c.status', 'pending')
            ->orderBy('c.created_at', 'DESC')->get()->getResultArray();
        return view('dashboard/certificates/approvals/index', [
            'title' => 'Approval Sertifikat | Dashboard BegawiPBJ',
            'pageTitle' => 'Approval Sertifikat',
            'activeMenu' => 'certificates',
            'activeSubmenu' => 'approvals',
            'items' => $items,
        ]);
    }

    public function detail(string $id): string
    {
        $certificate = db_connect()->table('certificates c')
            ->select('c.*, users.name AS user_name, learning_classes.name AS class_name')
            ->join('enrollments', 'enrollments.id = c.source_id AND c.source_type = \'learning_enrollment\'')
            ->join('users', 'users.id = c.user_id')
            ->join('learning_classes', 'learning_classes.id = enrollments.class_id')
            ->where('c.id', $id)->get()->getRowArray();
        if (! $certificate) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Certificate tidak ditemukan.');
        }
        return view('dashboard/certificates/approvals/detail', [
            'title' => 'Detail Approval Sertifikat | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Approval Sertifikat',
            'approvalId' => $id,
            'certificate' => $certificate,
            'activeMenu' => 'certificates',
            'activeSubmenu' => 'approvals',
        ]);
    }

    public function issue(string $id)
    {
        try {
            $this->assertCertificateApprovalAccess();
            (new LearningVerticalSliceService())->issuePendingCertificate((int) $id, (int) session()->get('user_id'));
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to(route_to('dashboard.certificates.approvals.detail', $id))
            ->with('success', 'Certificate berhasil diterbitkan.');
    }

    private function assertCertificateApprovalAccess(): void
    {
        $actorId = (int) session()->get('user_id');
        if (session()->get('isLoggedIn') !== true || $actorId < 1) {
            throw new RuntimeException('Akses ditolak.');
        }

        $actor = db_connect()->table('users')->where('id', $actorId)->get()->getRowArray();
        if (! $actor || $actor['account_status'] !== 'active') {
            throw new RuntimeException('Akun tidak aktif.');
        }

        $hasPermission = db_connect()->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->join('role_permissions rp', 'rp.role_id = r.id')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where(['ur.user_id' => $actorId, 'ur.status' => 'active', 'p.name' => 'certificate.approve'])
            ->countAllResults() > 0;
        if (! $hasPermission) {
            throw new RuntimeException('Anda tidak memiliki permission issue certificate.');
        }
    }
}

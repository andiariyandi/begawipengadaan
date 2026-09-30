<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Models\ApprovalModel;
use App\Models\CertificateModel;
use App\Models\CertificateVerificationModel;
use App\Models\UserModel;
use App\Services\CertificateService;
use RuntimeException;

class CertificateController extends BaseController
{
    public function my()
    {
        return view('landing/user/certificates', [
            'title'        => 'Sertifikat Saya',
            'subTitle'     => 'Sertifikat Saya',
            'certificates' => (new CertificateModel())->myCertificates(session()->get('user_id')),
        ]);
    }

    public function download(int $certificateId)
    {
        $certificateModel = new CertificateModel();
        $certificate      = $certificateModel->withDetail($certificateId);

        if (! $certificate || ! $certificate['file_path']) {
            return redirect()->back()->with('error', 'Sertifikat tidak ditemukan.');
        }

        $userId = (int) session()->get('user_id');
        $roles  = (new UserModel())->getRoles($userId);
        $slugs  = array_column($roles, 'slug');
        $isPrivileged = (bool) array_intersect($slugs, ['super_administrator', 'administrator', 'evaluator']);

        if (! $isPrivileged && (int) $certificate['user_id'] !== $userId) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke sertifikat ini.');
        }

        if ($certificate['status'] !== 'issued') {
            return redirect()->back()->with('error', 'Sertifikat ini sudah tidak berlaku.');
        }

        $basePath = realpath(WRITEPATH . 'certificates');
        $fullPath = realpath(WRITEPATH . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $certificate['file_path']), DIRECTORY_SEPARATOR));

        if ($basePath === false || $fullPath === false || ! is_file($fullPath)) {
            return redirect()->back()->with('error', 'File sertifikat tidak ditemukan di server.');
        }

        $basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (strncmp($fullPath, $basePath, strlen($basePath)) !== 0) {
            return redirect()->back()->with('error', 'File sertifikat tidak ditemukan di server.');
        }

        return $this->response->download($fullPath, null)->setFileName($certificate['certificate_number'] . '.pdf');
    }

    public function pending()
    {
        $approvalModel = new ApprovalModel();

        $pendingApprovals = $approvalModel->db->table('approvals')
            ->select('approvals.id as approval_id, approvals.approvable_id as enrollment_id, approvals.created_at,
                      users.username, courses.title as course_title')
            ->join('enrollments', 'enrollments.id = approvals.approvable_id')
            ->join('users', 'users.id = enrollments.user_id')
            ->join('classes', 'classes.id = enrollments.class_id')
            ->join('courses', 'courses.id = classes.course_id')
            ->where('approvals.approvable_type', 'certificate')
            ->where('approvals.current_status', 'pending')
            ->get()->getResultArray();

        return view('certificate/pending', [
            'title'    => 'Persetujuan Sertifikat',
            'subTitle' => 'Persetujuan Sertifikat',
            'items'    => $pendingApprovals,
        ]);
    }

    public function verifyDecision(int $enrollmentId)
    {
        $decision = $this->request->getPost('decision');
        $actedBy  = session()->get('user_id');

        if (! in_array($decision, ['approve', 'reject'], true)) {
            return redirect()->back()->with('error', 'Keputusan tidak valid.');
        }

        $service = new CertificateService();

        try {
            if ($decision === 'approve') {
                $service->approve($enrollmentId, $actedBy);
                $message = 'Sertifikat berhasil diterbitkan.';
            } else {
                $service->reject($enrollmentId, $actedBy, $this->request->getPost('notes'));
                $message = 'Pengajuan sertifikat ditolak.';
            }
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to(route_to('certificate.pending'))->with('message', $message);
    }

    /**
     * Kelola SEMUA sertifikat yang sudah terbit (issued + revoked) —
     * beda dari my() yang hanya menampilkan milik sendiri. Di sinilah
     * tombol "Cabut" dipasang.
     */
    public function manage()
    {
        return view('certificate/manage', [
            'title'        => 'Kelola Sertifikat',
            'subTitle'     => 'Kelola Sertifikat',
            'certificates' => (new CertificateModel())->allWithDetail(),
        ]);
    }

    public function revoke(int $certificateId)
    {
        $reason = $this->request->getPost('reason');
        $certificate = (new CertificateModel())->find($certificateId);

        if (! $certificate) {
            return redirect()->back()->with('error', 'Sertifikat tidak ditemukan.');
        }

        if ($certificate['status'] !== 'issued') {
            return redirect()->back()->with('error', 'Sertifikat sudah tidak aktif.');
        }

        if (! is_string($reason) || trim($reason) === '') {
            return redirect()->back()->with('error', 'Alasan pencabutan wajib diisi.');
        }

        try {
            (new CertificateService())->revoke($certificateId, session()->get('user_id'), trim($reason));
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to(route_to('certificate.manage'))->with('message', 'Sertifikat telah dicabut.');
    }

    // PUBLIK — tanpa login, diakses lewat scan QR
    public function verify(string $token)
    {
        $certificate = (new CertificateModel())->withDetailByToken($token);

        if ($certificate) {
            (new CertificateVerificationModel())->insert([
                'certificate_id' => $certificate['id'],
                'verified_at'    => date('Y-m-d H:i:s'),
                'ip_address'     => $this->request->getIPAddress(),
            ]);
        }

        return view('certificate/verify', [
            'title'       => 'Verifikasi Sertifikat',
            'subTitle'    => 'Verifikasi Sertifikat',
            'certificate' => $certificate,
        ]);
    }
}

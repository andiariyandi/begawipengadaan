<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Services\LearningVerticalSliceService;
use RuntimeException;

class EnrollmentController extends BaseController
{
    protected EnrollmentModel $enrollmentModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->enrollmentModel = new EnrollmentModel();
    }

    public function index(): string
    {
        $enrollments = $this->enrollmentModel->withClassDetail()->orderBy('enrollments.enrolled_at', 'DESC')->findAll();

        return view('dashboard/learning/enrollments/index', [
            'title' => 'Enrollment | Dashboard BegawiPBJ',
            'pageTitle' => 'Enrollment',
            'enrollments' => $enrollments,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'enrollments',
        ]);
    }

    public function detail(string $id): string
    {
        $enrollment = $this->enrollmentModel->withClassDetail()->find($id);
        if ($enrollment === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Enrollment tidak ditemukan.');
        }

        return view('dashboard/learning/enrollments/detail', [
            'title' => 'Detail Enrollment | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Enrollment',
            'enrollmentId' => $id,
            'enrollment' => $enrollment,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'enrollments',
        ]);
    }

    public function approve(string $id)
    {
        try {
            $this->assertEnrollmentApprovalAccess();
            (new LearningVerticalSliceService())->activateEnrollment((int) $id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to(route_to('dashboard.learning.enrollments.detail', $id))
            ->with('success', 'Enrollment berhasil diaktifkan.');
    }

    public function reject(string $id)
    {
        return $this->changeStatus($id, 'cancelled');
    }

    public function status(string $id)
    {
        $newStatus = (string) $this->request->getPost('status');
        if (! in_array($newStatus, ['pending', 'active', 'completed', 'withdrawn', 'cancelled'], true)) {
            return redirect()->back()->withInput()->with('error', 'Status enrollment tidak valid.');
        }

        return $this->changeStatus($id, $newStatus);
    }

    private function changeStatus(string $id, string $status)
    {
        $enrollment = $this->enrollmentModel->find($id);
        if ($enrollment === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Enrollment tidak ditemukan.');
        }

        $data = ['status' => $status];
        if ($status === 'active') {
            $data['activated_at'] = date('Y-m-d H:i:s');
            $data['cancelled_at'] = null;
        } elseif ($status === 'cancelled') {
            $data['cancelled_at'] = date('Y-m-d H:i:s');
            $data['activated_at'] = null;
        }

        $this->enrollmentModel->update($id, $data);

        return redirect()->to(route_to('dashboard.learning.enrollments.detail', $id))
            ->with('success', 'Status enrollment berhasil diperbarui.');
    }

    private function assertEnrollmentApprovalAccess(): void
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
            ->where(['ur.user_id' => $actorId, 'ur.status' => 'active', 'p.key' => 'enrollment.verify'])
            ->countAllResults() > 0;
        if (! $hasPermission) {
            throw new RuntimeException('Anda tidak memiliki permission approval enrollment.');
        }
    }
}

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
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status = trim((string) $this->request->getGet('status'));

        $builder = $this->enrollmentModel->withClassDetail()->orderBy('enrollments.enrolled_at', 'DESC');
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('users.name', $keyword)
                ->orLike('users.username', $keyword)
                ->orLike('learning_classes.name', $keyword)
                ->orLike('courses.title', $keyword)
                ->groupEnd();
        }
        if ($status !== '') {
            $builder->where('enrollments.status', $status);
        }

        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_enrollments') ?: 1));
        $total = $this->enrollmentModel->countAllResults(false);
        $enrollments = $this->enrollmentModel->findAll($perPage, ($page - 1) * $perPage);

        $pager = service('pager');
        $pager->store('enrollments', $page, $perPage, $total, 0);

        $db = db_connect();
        $stats = [
            'total'     => (int) $db->table('enrollments')->countAllResults(),
            'active'    => (int) $db->table('enrollments')->whereIn('status', ['active', 'enrolled', 'in_progress'])->countAllResults(),
            'completed' => (int) $db->table('enrollments')->whereIn('status', ['completed', 'passed'])->countAllResults(),
            'pending'   => (int) $db->table('enrollments')->where('status', 'pending')->countAllResults(),
        ];

        return view('dashboard/learning/enrollments/index', [
            'title'         => 'Enrollment | Dashboard BegawiPBJ',
            'pageTitle'     => 'Pendaftaran Peserta (Enrollment)',
            'enrollments'   => $enrollments,
            'stats'         => $stats,
            'pager'         => $pager,
            'filters'       => [
                'keyword' => $keyword,
                'status'  => $status,
            ],
            'activeMenu'    => 'learning',
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

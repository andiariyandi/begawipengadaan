<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\Learning\LearningClassModel;
use App\Services\LearningVerticalSliceService;
use RuntimeException;

class EnrollmentController extends BaseController
{
    protected EnrollmentModel $enrollmentModel;
    protected LearningClassModel $classModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new LearningClassModel();
    }

    public function index(): string
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status = trim((string) $this->request->getGet('status'));
        $classId = (int) $this->request->getGet('class_id');

        $builder = $db->table('enrollments')
            ->select('enrollments.*, 
                      learning_classes.name AS class_name, 
                      learning_classes.code AS class_code, 
                      courses.title AS course_title, 
                      users.name AS user_name, 
                      users.username,
                      users.email AS user_email')
            ->join('learning_classes', 'learning_classes.id = enrollments.class_id', 'left')
            ->join('courses', 'courses.id = learning_classes.course_id', 'left')
            ->join('users', 'users.id = enrollments.user_id', 'left')
            ->orderBy('enrollments.enrolled_at', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('users.name', $keyword)
                ->orLike('users.email', $keyword)
                ->orLike('users.username', $keyword)
                ->orLike('learning_classes.name', $keyword)
                ->orLike('courses.title', $keyword)
                ->groupEnd();
        }
        if ($status !== '') {
            $builder->where('enrollments.status', $status);
        }
        if ($classId > 0) {
            $builder->where('enrollments.class_id', $classId);
        }

        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_enrollments') ?: $this->request->getGet('page') ?: 1));
        $total = (clone $builder)->countAllResults();
        $enrollments = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $pager = service('pager');
        $pager->store('enrollments', $page, $perPage, $total, 0);

        // Calculate progress for each enrollment
        foreach ($enrollments as &$enr) {
            $clsId = (int) $enr['class_id'];
            $enrId = (int) $enr['id'];

            $totalLessons = (int) $db->table('lessons l')
                ->join('learning_modules lm', 'lm.id = l.module_id')
                ->where('lm.class_id', $clsId)
                ->countAllResults();

            $completedLessons = (int) $db->table('learning_progress')
                ->where('enrollment_id', $enrId)
                ->where('status', 'completed')
                ->countAllResults();

            $enr['total_lessons'] = $totalLessons;
            $enr['completed_lessons'] = $completedLessons;
            $enr['progress_pct'] = $totalLessons > 0
                ? (int) round(($completedLessons / $totalLessons) * 100)
                : ($enr['status'] === 'completed' || $enr['status'] === 'passed' ? 100 : 0);
        }
        unset($enr);

        $stats = [
            'total'     => (int) $db->table('enrollments')->countAllResults(),
            'active'    => (int) $db->table('enrollments')->whereIn('status', ['active', 'enrolled', 'in_progress'])->countAllResults(),
            'completed' => (int) $db->table('enrollments')->whereIn('status', ['completed', 'passed'])->countAllResults(),
            'pending'   => (int) $db->table('enrollments')->where('status', 'pending')->countAllResults(),
        ];

        // Load classes and users for manual enrollment modal
        $classes = $this->classModel->orderBy('name', 'ASC')->findAll();
        $users = $db->table('users')
            ->where('account_status', 'active')
            ->orderBy('name', 'ASC')
            ->limit(100)
            ->get()->getResultArray();

        return view('dashboard/learning/enrollments/index', [
            'title'         => 'Enrollment | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Pendaftaran Peserta (Enrollment)',
            'enrollments'   => $enrollments,
            'stats'         => $stats,
            'pager'         => $pager,
            'classes'       => $classes,
            'users'         => $users,
            'filters'       => [
                'keyword'  => $keyword,
                'status'   => $status,
                'class_id' => $classId,
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

        $db = db_connect();
        $user = $db->table('users')->where('id', $enrollment['user_id'])->get()->getRowArray();

        // Progress details
        $totalLessons = (int) $db->table('lessons l')
            ->join('learning_modules lm', 'lm.id = l.module_id')
            ->where('lm.class_id', $enrollment['class_id'])
            ->countAllResults();

        $completedLessons = (int) $db->table('learning_progress')
            ->where('enrollment_id', $id)
            ->where('status', 'completed')
            ->countAllResults();

        $progressPct = $totalLessons > 0
            ? (int) round(($completedLessons / $totalLessons) * 100)
            : ($enrollment['status'] === 'completed' || $enrollment['status'] === 'passed' ? 100 : 0);

        // Certificate check
        $certificate = $db->table('certificates')
            ->where('source_type', 'learning_enrollment')
            ->where('source_id', $id)
            ->get()->getRowArray();

        return view('dashboard/learning/enrollments/detail', [
            'title'            => 'Detail Enrollment | Dashboard BegawiPengadaan',
            'pageTitle'        => 'Detail Enrollment',
            'enrollmentId'     => $id,
            'enrollment'       => $enrollment,
            'user'             => $user,
            'totalLessons'     => $totalLessons,
            'completedLessons' => $completedLessons,
            'progressPct'      => $progressPct,
            'certificate'      => $certificate,
            'activeMenu'       => 'learning',
            'activeSubmenu'    => 'enrollments',
        ]);
    }

    public function store()
    {
        $userId = (int) $this->request->getPost('user_id');
        $classId = (int) $this->request->getPost('class_id');

        if ($userId < 1 || $classId < 1) {
            return redirect()->back()->withInput()->with('error', 'Pilih peserta dan kelas dengan benar.');
        }

        $existing = $this->enrollmentModel
            ->where('user_id', $userId)
            ->where('class_id', $classId)
            ->first();

        if ($existing !== null) {
            if ($existing['status'] === 'active') {
                return redirect()->back()->with('error', 'Peserta sudah terdaftar aktif pada kelas ini.');
            }
            $this->enrollmentModel->update($existing['id'], [
                'status'       => 'active',
                'activated_at' => date('Y-m-d H:i:s'),
                'cancelled_at' => null,
            ]);
            return redirect()->to(site_url('dashboard/pembelajaran/enrollment'))
                ->with('success', 'Status pendaftaran peserta berhasil diaktifkan kembali.');
        }

        $this->enrollmentModel->insert([
            'class_id'     => $classId,
            'user_id'      => $userId,
            'status'       => 'active',
            'enrolled_at'  => date('Y-m-d H:i:s'),
            'activated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/enrollment'))
            ->with('success', 'Peserta berhasil didaftarkan ke kelas.');
    }

    public function approve(string $id)
    {
        try {
            $this->assertEnrollmentApprovalAccess();
            (new LearningVerticalSliceService())->activateEnrollment((int) $id);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()
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

    public function delete(string $id)
    {
        $enrollment = $this->enrollmentModel->find($id);
        if ($enrollment === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Enrollment tidak ditemukan.');
        }

        $this->enrollmentModel->update($id, [
            'status'       => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()
            ->with('success', 'Pendaftaran peserta berhasil dibatalkan / dihapus.');
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

        return redirect()->back()
            ->with('success', 'Status enrollment berhasil diperbarui.');
    }

    private function assertEnrollmentApprovalAccess(): void
    {
        $actorId = (int) session()->get('user_id');
        if (session()->get('isLoggedIn') !== true || $actorId < 1) {
            throw new RuntimeException('Akses ditolak.');
        }

        $roles = (array) session()->get('roles');
        if (in_array('super_admin', $roles, true) || in_array('admin', $roles, true) || in_array('super_administrator', $roles, true) || in_array('administrator', $roles, true) || in_array('operator', $roles, true)) {
            return;
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

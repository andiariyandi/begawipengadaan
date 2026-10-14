<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\ClassTrainerModel;
use App\Models\Learning\LearningClassModel;
use App\Services\Identity\AuthorizationService;

class TrainerAssignmentController extends BaseController
{
    protected ClassTrainerModel $classTrainerModel;
    protected LearningClassModel $classModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->classTrainerModel = new ClassTrainerModel();
        $this->classModel = new LearningClassModel();
    }

    public function index(): string
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status = trim((string) $this->request->getGet('status'));
        $classId = (int) $this->request->getGet('class_id');

        $builder = $db->table('class_trainers ct')
            ->select('ct.*, lc.name AS class_name, lc.code AS class_code, lc.status AS class_status,
                      c.title AS course_title, u.name AS trainer_name, u.email AS trainer_email, u.username AS trainer_username,
                      u.profile_type AS trainer_profile_type, assigner.name AS assigner_name')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->join('users u', 'u.id = ct.user_id')
            ->join('users assigner', 'assigner.id = ct.assigned_by', 'left')
            ->orderBy('ct.assigned_at', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('u.email', $keyword)
                ->orLike('lc.name', $keyword)
                ->orLike('lc.code', $keyword)
                ->groupEnd();
        }
        if ($status !== '') {
            $builder->where('ct.status', $status);
        }
        if ($classId > 0) {
            $builder->where('ct.class_id', $classId);
        }

        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_trainers') ?: $this->request->getGet('page') ?: 1));
        $total = (clone $builder)->countAllResults();
        $assignments = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $pager = service('pager');
        $pager->store('trainers', $page, $perPage, $total, 0);

        $stats = [
            'total'          => (int) $db->table('class_trainers')->countAllResults(),
            'active'         => (int) $db->table('class_trainers')->where('status', 'active')->countAllResults(),
            'unique_classes' => (int) $db->table('class_trainers')->where('status', 'active')->select('COUNT(DISTINCT class_id) as cnt')->get()->getRow()->cnt ?? 0,
            'revoked'        => (int) $db->table('class_trainers')->where('status', 'revoked')->countAllResults(),
        ];

        // Classes for dropdown
        $classes = $this->classModel->orderBy('name', 'ASC')->findAll();

        // Trainer candidates (users with role 'trainer' and active status)
        $trainers = $db->table('user_roles ur')
            ->select('u.id, u.name, u.email, u.username')
            ->join('roles r', 'r.id = ur.role_id')
            ->join('users u', 'u.id = ur.user_id')
            ->where('ur.status', 'active')
            ->where('r.`key`', 'trainer')
            ->where('u.account_status', 'active')
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();

        return view('dashboard/learning/trainers/index', [
            'title'         => 'Penugasan Trainer | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Penugasan Trainer (Trainer Assignment)',
            'assignments'   => $assignments,
            'stats'         => $stats,
            'classes'       => $classes,
            'trainers'      => $trainers,
            'pager'         => $pager,
            'filters'       => [
                'keyword'  => $keyword,
                'status'   => $status,
                'class_id' => $classId,
            ],
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'trainers',
        ]);
    }

    public function assign()
    {
        $classId = (int) $this->request->getPost('class_id');
        $trainerId = (int) $this->request->getPost('trainer_user_id');

        if ($classId < 1 || $trainerId < 1) {
            return redirect()->back()->withInput()->with('error', 'Pilih kelas dan trainer dengan benar.');
        }

        $class = $this->classModel->find($classId);
        if ($class === null) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }

        $db = db_connect();
        $isTrainer = $db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where(['ur.user_id' => $trainerId, 'ur.status' => 'active', 'r.key' => 'trainer'])
            ->countAllResults() > 0;

        if (! $isTrainer) {
            return redirect()->back()->with('error', 'Pengguna yang dipilih tidak memiliki role trainer aktif.');
        }

        $actorId = (int) session()->get('user_id');

        try {
            $db->table('class_trainers')->replace([
                'class_id'    => $classId,
                'user_id'     => $trainerId,
                'assigned_by' => $actorId > 0 ? $actorId : null,
                'assigned_at' => date('Y-m-d H:i:s'),
                'status'      => 'active',
                'role'        => $this->request->getPost('role') ?: 'primary',
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menugaskan trainer: ' . $e->getMessage());
        }

        return redirect()->to(site_url('dashboard/pembelajaran/trainer'))
            ->with('success', 'Trainer berhasil ditugaskan ke kelas.');
    }

    public function revoke(string $classId, string $userId)
    {
        $db = db_connect();

        try {
            $db->table('class_trainers')
                ->where('class_id', (int) $classId)
                ->where('user_id', (int) $userId)
                ->update(['status' => 'revoked']);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mencabut penugasan: ' . $e->getMessage());
        }

        return redirect()->to(site_url('dashboard/pembelajaran/trainer'))
            ->with('success', 'Penugasan trainer berhasil dicabut.');
    }
}

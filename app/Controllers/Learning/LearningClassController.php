<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\ClassTrainerModel;
use App\Models\Learning\CourseModel;
use App\Models\Learning\LearningClassModel;
use App\Services\Identity\AuthorizationService;

class LearningClassController extends BaseController
{
    protected LearningClassModel $classModel;
    protected CourseModel $courseModel;
    protected ClassTrainerModel $classTrainerModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->classModel = new LearningClassModel();
        $this->courseModel = new CourseModel();
        $this->classTrainerModel = new ClassTrainerModel();
    }

    public function index(): string
    {
        $classes = $this->classModel
            ->select('learning_classes.*, courses.title AS course_title')
            ->join('courses', 'courses.id = learning_classes.course_id', 'left')
            ->orderBy('learning_classes.created_at', 'DESC')
            ->findAll();

        return view('dashboard/learning/classes/index', [
            'title' => 'Kelas | Dashboard BegawiPBJ',
            'pageTitle' => 'Kelas',
            'classes' => $classes,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'classes',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/classes/form', [
            'title' => 'Tambah Kelas | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Kelas',
            'formMode' => 'create',
            'class' => null,
            'courses' => $this->courseModel->orderBy('title', 'ASC')->findAll(),
            'activeMenu' => 'learning',
            'activeSubmenu' => 'classes',
        ]);
    }

    public function store()
    {
        if (! $this->canManageClasses()) {
            return $this->forbidClassManagement();
        }
        $data = $this->validatedClassData();
        if ($data === null) {
            return redirect()->back()->withInput();
        }

        $userId = session()->get('user_id');
        if ($userId !== null) {
            $data['created_by'] = (int) $userId;
        }

        $this->classModel->insert($data);

        return redirect()->to(route_to('dashboard.learning.classes'))->with('success', 'Kelas berhasil disimpan.');
    }

    public function detail(string $id): string
    {
        $class = $this->classModel
            ->select('learning_classes.*, courses.title AS course_title')
            ->join('courses', 'courses.id = learning_classes.course_id', 'left')
            ->find($id);
        if ($class === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $trainers = $this->loadAssignedTrainers((int) $id);
        $trainerCandidates = $this->loadTrainerCandidates();

        return view('dashboard/learning/classes/detail', [
            'title' => 'Detail Kelas | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Kelas',
            'classId' => $id,
            'class' => $class,
            'trainers' => $trainers,
            'trainerCandidates' => $trainerCandidates,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'classes',
        ]);
    }

    public function assignTrainer(string $id)
    {
        if (! $this->canManageClasses()) {
            return $this->forbidClassManagement();
        }
        if ($this->classModel->find($id) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }
        $trainerId = (int) $this->request->getPost('trainer_user_id');
        if ($trainerId < 1) {
            return redirect()->back()->withInput()->with('error', 'Pilih trainer terlebih dahulu.');
        }
        if (! $this->isTrainerUser($trainerId)) {
            return redirect()->back()->withInput()->with('error', 'User yang dipilih tidak memiliki role trainer.');
        }
        $existing = $this->classTrainerModel
            ->where('class_id', (int) $id)
            ->where('user_id', $trainerId)
            ->first();
        if ($existing !== null && $existing['status'] === 'active') {
            return redirect()->back()->with('error', 'Trainer sudah ditugaskan pada kelas ini.');
        }
        $actorId = (int) session()->get('user_id');
        try {
            db_connect()->table('class_trainers')->replace([
                'class_id'    => (int) $id,
                'user_id'     => $trainerId,
                'assigned_by' => $actorId > 0 ? $actorId : null,
                'assigned_at' => date('Y-m-d H:i:s'),
                'status'      => 'active',
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal assign trainer: ' . $e->getMessage());
        }
        return redirect()->to(route_to('dashboard.learning.classes.detail', $id))
            ->with('success', 'Trainer berhasil ditugaskan.');
    }

    public function revokeTrainer(string $id, string $trainerId)
    {
        if (! $this->canManageClasses()) {
            return $this->forbidClassManagement();
        }
        if ($this->classModel->find($id) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }
        try {
            db_connect()->table('class_trainers')
                ->where('class_id', (int) $id)
                ->where('user_id', (int) $trainerId)
                ->update(['status' => 'revoked']);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mencabut trainer: ' . $e->getMessage());
        }
        return redirect()->to(route_to('dashboard.learning.classes.detail', $id))
            ->with('success', 'Penugasan trainer dicabut.');
    }

    private function loadAssignedTrainers(int $classId): array
    {
        return db_connect()->table('class_trainers ct')
            ->select('ct.user_id, ct.status, ct.assigned_at, users.name, users.email, users.username')
            ->join('users', 'users.id = ct.user_id')
            ->where('ct.class_id', $classId)
            ->orderBy('users.name', 'ASC')
            ->get()->getResultArray();
    }

    private function loadTrainerCandidates(): array
    {
        return db_connect()->table('user_roles ur')
            ->select('users.id, users.name, users.email, users.username')
            ->join('roles r', 'r.id = ur.role_id')
            ->join('users', 'users.id = ur.user_id')
            ->where('ur.status', 'active')
            ->where('r.`key`', 'trainer')
            ->where('users.account_status', 'active')
            ->orderBy('users.name', 'ASC')
            ->get()->getResultArray();
    }

    private function isTrainerUser(int $userId): bool
    {
        return db_connect()->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where(['ur.user_id' => $userId, 'ur.status' => 'active'])
            ->where('r.`key`', 'trainer')
            ->countAllResults() > 0;
    }

    /**
     * Gate untuk aksi manajemen kelas (CRUD + assign trainer).
     * Mengikuti pola CLAUDE.md §28: role + permission + scope.
     * Boleh dilakukan oleh permission learning.class.create/update atau role super_admin/admin
     * (di mana admin tradisional hanya butuh satu permission saja).
     */
    private function canManageClasses(): bool
    {
        $auth = new AuthorizationService();
        if ($auth->can('learning.class.create')) {
            return true;
        }
        if ($auth->can('learning.class.update')) {
            return true;
        }
        if ($auth->can('learning.class.delete')) {
            return true;
        }
        $roles = (array) session()->get('roles');
        return in_array('super_admin', $roles, true) || in_array('admin', $roles, true);
    }

    private function forbidClassManagement()
    {
        return redirect()->to(site_url('dashboard'))
            ->with('error', 'Anda tidak memiliki izin untuk mengelola kelas.');
    }

    public function edit(string $id): string
    {
        $class = $this->classModel->find($id);
        if ($class === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        return view('dashboard/learning/classes/form', [
            'title' => 'Ubah Kelas | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Kelas',
            'formMode' => 'edit',
            'classId' => $id,
            'class' => $class,
            'courses' => $this->courseModel->orderBy('title', 'ASC')->findAll(),
            'activeMenu' => 'learning',
            'activeSubmenu' => 'classes',
        ]);
    }

    public function update(string $id)
    {
        if (! $this->canManageClasses()) {
            return $this->forbidClassManagement();
        }
        if ($this->classModel->find($id) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $data = $this->validatedClassData();
        if ($data === null) {
            return redirect()->back()->withInput();
        }

        $userId = session()->get('user_id');
        if ($userId !== null) {
            $data['updated_by'] = (int) $userId;
        }

        $this->classModel->update($id, $data);

        return redirect()->to(route_to('dashboard.learning.classes'))->with('success', 'Kelas berhasil diperbarui.');
    }

    private function validatedClassData(): ?array
    {
        if (! $this->validate([
            'course_id' => 'required|is_natural_no_zero',
            'code' => 'required',
            'name' => 'required',
            'status' => 'permit_empty',
            'capacity' => 'permit_empty|is_natural',
            'passing_grade' => 'permit_empty|decimal',
            'quiz_weight' => 'permit_empty|decimal',
            'assignment_weight' => 'permit_empty|decimal',
            'post_test_weight' => 'permit_empty|decimal',
        ])) {
            return null;
        }

        return [
            'course_id' => (int) $this->request->getPost('course_id'),
            'code' => trim((string) $this->request->getPost('code')),
            'name' => trim((string) $this->request->getPost('name')),
            'status' => $this->request->getPost('status') ?: 'draft',
            'registration_open_at' => $this->request->getPost('registration_open_at') ?: null,
            'registration_close_at' => $this->request->getPost('registration_close_at') ?: null,
            'start_at' => $this->request->getPost('start_at') ?: null,
            'end_at' => $this->request->getPost('end_at') ?: null,
            'capacity' => $this->request->getPost('capacity') ?: null,
            'certificate_enabled' => $this->request->getPost('certificate_enabled') ? 1 : 0,
            'passing_grade' => $this->request->getPost('passing_grade') ?: 70,
            'quiz_weight' => $this->request->getPost('quiz_weight') ?: 40,
            'assignment_weight' => $this->request->getPost('assignment_weight') ?: 0,
            'post_test_weight' => $this->request->getPost('post_test_weight') ?: 60,
        ];
    }
}

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
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status = trim((string) $this->request->getGet('status'));
        $courseId = (int) $this->request->getGet('course_id');

        $builder = $this->classModel
            ->select('learning_classes.*, courses.title AS course_title,
                      (SELECT COUNT(1) FROM class_trainers ct WHERE ct.class_id = learning_classes.id AND ct.status = "active") AS trainer_count,
                      (SELECT GROUP_CONCAT(u.name SEPARATOR ", ") FROM class_trainers ct JOIN users u ON u.id = ct.user_id WHERE ct.class_id = learning_classes.id AND ct.status = "active") AS trainer_names,
                      (SELECT COUNT(1) FROM enrollments e WHERE e.class_id = learning_classes.id) AS student_count,
                      (SELECT COUNT(1) FROM enrollments e WHERE e.class_id = learning_classes.id AND e.status IN ("completed", "passed")) AS completed_count')
            ->join('courses', 'courses.id = learning_classes.course_id', 'left')
            ->orderBy('learning_classes.created_at', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('learning_classes.name', $keyword)
                ->orLike('learning_classes.code', $keyword)
                ->orLike('learning_classes.description', $keyword)
                ->groupEnd();
        }
        if ($status !== '') {
            if ($status === 'scheduled' || $status === 'open') {
                $builder->whereIn('learning_classes.status', ['scheduled', 'open']);
            } elseif ($status === 'active' || $status === 'ongoing') {
                $builder->whereIn('learning_classes.status', ['active', 'ongoing']);
            } elseif ($status === 'completed' || $status === 'closed') {
                $builder->whereIn('learning_classes.status', ['completed', 'closed']);
            } else {
                $builder->where('learning_classes.status', $status);
            }
        }
        if ($courseId > 0) {
            $builder->where('learning_classes.course_id', $courseId);
        }

        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_classes') ?: 1));
        $total = $this->classModel->countAllResults(false);
        $classes = $this->classModel->findAll($perPage, ($page - 1) * $perPage);

        $pager = service('pager');
        $pager->store('classes', $page, $perPage, $total, 0);

        $db = db_connect();
        $stats = [
            'total'     => (int) $db->table('learning_classes')->countAllResults(),
            'open'      => (int) $db->table('learning_classes')->whereIn('status', ['open', 'scheduled', 'active', 'ongoing'])->countAllResults(),
            'completed' => (int) $db->table('learning_classes')->whereIn('status', ['completed', 'closed'])->countAllResults(),
            'draft'     => (int) $db->table('learning_classes')->where('status', 'draft')->countAllResults(),
        ];

        return view('dashboard/learning/classes/index', [
            'title'         => 'Kelas | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Pelaksanaan Kelas',
            'classes'       => $classes,
            'stats'         => $stats,
            'pager'         => $pager,
            'courses'       => $this->courseModel->orderBy('title', 'ASC')->findAll(),
            'filters'       => [
                'keyword'   => $keyword,
                'status'    => $status,
                'course_id' => $courseId,
            ],
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'classes',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/classes/form', [
            'title' => 'Tambah Kelas | Dashboard BegawiPengadaan',
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

        $db = db_connect();
        $enrollmentsCount = (int) $db->table('enrollments')->where('class_id', $id)->countAllResults();
        $modulesCount = (int) $db->table('learning_modules')->where('class_id', $id)->countAllResults();

        return view('dashboard/learning/classes/detail', [
            'title'             => 'Detail Kelas | Dashboard BegawiPengadaan',
            'pageTitle'         => 'Detail Kelas',
            'classId'           => $id,
            'class'             => $class,
            'trainers'          => $trainers,
            'trainerCandidates' => $trainerCandidates,
            'enrollmentsCount'  => $enrollmentsCount,
            'modulesCount'      => $modulesCount,
            'activeMenu'        => 'learning',
            'activeSubmenu'     => 'classes',
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
                'role'        => $this->request->getPost('role') ?: 'primary',
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
            ->select('ct.user_id, ct.status, ct.role, ct.assigned_at, users.name, users.email, users.username')
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
        return in_array('super_admin', $roles, true)
            || in_array('admin', $roles, true)
            || in_array('super_administrator', $roles, true)
            || in_array('administrator', $roles, true);
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
            'title' => 'Ubah Kelas | Dashboard BegawiPengadaan',
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
        $class = $this->classModel->find($id);
        if ($class === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $data = $this->validatedClassData($class['cover_url'] ?? null, $class['cover_file_id'] ?? null);
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

    public function delete(string $id)
    {
        if (! $this->canManageClasses()) {
            return $this->forbidClassManagement();
        }

        $class = $this->classModel->find($id);
        if ($class === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $db = db_connect();
        $enrollmentCount = (int) $db->table('enrollments')->where('class_id', $id)->countAllResults();
        if ($enrollmentCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus kelas: Terdapat {$enrollmentCount} peserta terdaftar pada kelas ini.");
        }

        $db->table('class_trainers')->where('class_id', $id)->delete();
        $this->classModel->delete($id);

        return redirect()->to(route_to('dashboard.learning.classes'))->with('success', 'Kelas berhasil dihapus.');
    }

    private function validatedClassData(?string $existingCoverUrl = null, ?int $existingCoverFileId = null): ?array
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

        // Handle uploaded cover image file
        $file = $this->request->getFile('cover_image');
        $uploadedUrl = null;
        $uploadedFileId = null;

        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $mime = $file->getMimeType();
            if (! in_array($mime, $allowedMimes, true)) {
                session()->setFlashdata('error', 'Format file gambar harus JPG, PNG, atau WEBP.');
                return null;
            }
            if ($file->getSize() > 2097152) {
                session()->setFlashdata('error', 'Ukuran file gambar maksimal 2 MB.');
                return null;
            }

            $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'learning' . DIRECTORY_SEPARATOR . 'classes';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $storedName = $file->getRandomName();
            if ($file->move($targetDir, $storedName)) {
                $uploadedUrl = 'uploads/learning/classes/' . $storedName;

                try {
                    $now = date('Y-m-d H:i:s');
                    $db = db_connect();
                    $db->table('file_assets')->insert([
                        'disk'             => 'local',
                        'path'             => $uploadedUrl,
                        'original_name'    => $file->getClientName() ?: $storedName,
                        'stored_name'      => $storedName,
                        'extension'        => $file->getClientExtension() ?: 'jpg',
                        'mime_type'        => $mime,
                        'size_bytes'       => (int) ($file->getSize() ?: 0),
                        'visibility'       => 'public',
                        'lifecycle_status' => 'available',
                        'resource_type'    => 'class_cover',
                        'resource_id'      => null,
                        'uploaded_by'      => (int) session()->get('user_id'),
                        'available_at'     => $now,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                        'deleted_at'       => null,
                    ]);
                    $uploadedFileId = (int) $db->insertID();
                } catch (\Throwable $e) {
                    log_message('error', 'Class cover asset registration failed: ' . $e->getMessage());
                }
            }
        }

        $urlPost = $this->request->getPost('cover_url');
        $coverUrl = $uploadedUrl ?: (trim((string) $urlPost) ?: $existingCoverUrl);
        $coverFileId = $uploadedFileId ?: ($this->request->getPost('cover_file_id') ? (int) $this->request->getPost('cover_file_id') : $existingCoverFileId);

        $rawStatus = (string) $this->request->getPost('status');
        $status = match ($rawStatus) {
            'open', 'scheduled'   => 'scheduled',
            'active', 'ongoing'   => 'active',
            'completed', 'closed' => 'completed',
            default               => $rawStatus ?: 'draft',
        };

        return [
            'course_id'             => (int) $this->request->getPost('course_id'),
            'code'                  => trim((string) $this->request->getPost('code')),
            'name'                  => trim((string) $this->request->getPost('name')),
            'description'           => $this->request->getPost('description'),
            'status'                => $status,
            'featured'              => $this->request->getPost('featured') ? 1 : 0,
            'cover_file_id'         => $coverFileId,
            'cover_url'             => $coverUrl,
            'learning_method'       => $this->request->getPost('learning_method') ?: 'online',
            'location_or_link'      => trim((string) $this->request->getPost('location_or_link')) ?: null,
            'target_audience'       => $this->request->getPost('target_audience'),
            'registration_open_at'  => $this->request->getPost('registration_open_at') ?: null,
            'registration_close_at' => $this->request->getPost('registration_close_at') ?: null,
            'start_at'              => $this->request->getPost('start_at') ?: null,
            'end_at'                => $this->request->getPost('end_at') ?: null,
            'capacity'              => $this->request->getPost('capacity') ?: null,
            'certificate_enabled'   => $this->request->getPost('certificate_enabled') ? 1 : 0,
            'passing_grade'         => $this->request->getPost('passing_grade') ?: 70,
            'quiz_weight'           => $this->request->getPost('quiz_weight') ?: 40,
            'assignment_weight'     => $this->request->getPost('assignment_weight') ?: 0,
            'post_test_weight'      => $this->request->getPost('post_test_weight') ?: 60,
        ];
    }
}

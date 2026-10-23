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
            'title'               => 'Tambah Kelas | Dashboard BegawiPengadaan',
            'pageTitle'           => 'Tambah Kelas',
            'formMode'            => 'create',
            'class'               => null,
            'courses'             => $this->courseModel->orderBy('title', 'ASC')->findAll(),
            'batchStages'         => default_batch_stages(),
            'batchStagesSubtitle' => 'Alur pengerjaan yang harus dilalui oleh peserta selama periode kelas berlangsung:',
            'customDuration'      => null,
            'activeMenu'          => 'learning',
            'activeSubmenu'       => 'classes',
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

        try {
            $this->classModel->insert($data);
            return redirect()->to(route_to('dashboard.learning.classes'))->with('success', 'Kelas berhasil disimpan.');
        } catch (\Throwable $e) {
            log_message('error', '[LearningClassController] insert failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan kelas: ' . $e->getMessage());
        }
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

        $modules = $db->table('learning_modules lm')
            ->select('lm.*,
                      (SELECT COUNT(1) FROM lessons l WHERE l.module_id = lm.id) AS lesson_count,
                      (SELECT COUNT(1) FROM quizzes q WHERE q.class_id = lm.class_id AND q.lesson_id IN (SELECT id FROM lessons WHERE module_id = lm.id)) AS quiz_count')
            ->where('lm.class_id', (int) $id)
            ->orderBy('lm.sort_order', 'ASC')
            ->get()->getResultArray();

        $enrollments = $db->table('enrollments e')
            ->select('e.*, u.name AS user_name, u.email AS user_email, u.username, asn.employee_identifier AS nip, COALESCE(asn.institution_name, asn.unit_name) AS instansi,
                      cert.id AS certificate_id, cert.certificate_number, cert.status AS cert_status')
            ->join('users u', 'u.id = e.user_id')
            ->join('asn_profiles asn', 'asn.user_id = u.id', 'left')
            ->join('certificates cert', 'cert.source_type = "learning_enrollment" AND cert.source_id = e.id AND cert.status = "issued"', 'left')
            ->where('e.class_id', (int) $id)
            ->orderBy('e.enrolled_at', 'DESC')
            ->get()->getResultArray();

        $parsed = parse_class_batch_data($class['description'] ?? '');
        $class['description']             = $parsed['description'];
        $class['batch_stages']            = $parsed['stages'];
        $class['batch_stages_subtitle']   = $parsed['stages_subtitle'];
        $class['custom_duration_minutes'] = $parsed['custom_duration_minutes'];

        return view('dashboard/learning/classes/detail', [
            'title'             => 'Detail Kelas | Dashboard BegawiPengadaan',
            'pageTitle'         => 'Detail Kelas',
            'classId'           => $id,
            'class'             => $class,
            'batchStages'       => $parsed['stages'],
            'batchStagesSubtitle' => $parsed['stages_subtitle'],
            'trainers'          => $trainers,
            'trainerCandidates' => $trainerCandidates,
            'enrollmentsCount'  => $enrollmentsCount,
            'modulesCount'      => $modulesCount,
            'modules'           => $modules,
            'enrollments'       => $enrollments,
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
     * Boleh dilakukan oleh permission learning.class.create/update atau role super_administrator/admin
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
        return in_array('super_administrator', $roles, true)
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

        $parsed = parse_class_batch_data($class['description'] ?? '');
        $class['description']             = $parsed['description'];
        $class['batch_stages']            = $parsed['stages'];
        $class['batch_stages_subtitle']   = $parsed['stages_subtitle'];
        $class['custom_duration_minutes'] = $parsed['custom_duration_minutes'];

        return view('dashboard/learning/classes/form', [
            'title'               => 'Ubah Kelas | Dashboard BegawiPengadaan',
            'pageTitle'           => 'Ubah Kelas',
            'formMode'            => 'edit',
            'classId'             => $id,
            'class'               => $class,
            'courses'             => $this->courseModel->orderBy('title', 'ASC')->findAll(),
            'batchStages'         => $parsed['stages'],
            'batchStagesSubtitle' => $parsed['stages_subtitle'],
            'customDuration'      => $parsed['custom_duration_minutes'],
            'activeMenu'          => 'learning',
            'activeSubmenu'       => 'classes',
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

        try {
            $this->classModel->update($id, $data);
            return redirect()->to(route_to('dashboard.learning.classes'))->with('success', 'Kelas berhasil diperbarui.');
        } catch (\Throwable $e) {
            log_message('error', '[LearningClassController] update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kelas: ' . $e->getMessage());
        }
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

            $targetDir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'learning' . DIRECTORY_SEPARATOR . 'classes';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0775, true);
                @chmod($targetDir, 0775);
            }

            $storedName = $file->getRandomName();
            try {
                if ($file->move($targetDir, $storedName)) {
                    $uploadedUrl = 'uploads/learning/classes/' . $storedName;
                    @chmod($targetDir . DIRECTORY_SEPARATOR . $storedName, 0664);

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
            } catch (\Throwable $e) {
                log_message('error', '[LearningClassController] Cover move exception: ' . $e->getMessage());
                session()->setFlashdata('error', 'Gagal memindahkan file cover kelas: ' . $e->getMessage());
                return null;
            }
        }

        $urlPost = $this->request->getPost('cover_url');
        $coverUrl = $uploadedUrl ?: (trim((string) $urlPost) ?: $existingCoverUrl);
        $coverFileId = $uploadedFileId ?: ($this->request->getPost('cover_file_id') ? (int) $this->request->getPost('cover_file_id') : $existingCoverFileId);

        $rawStatus = (string) $this->request->getPost('status');
        $status = match (strtolower(trim($rawStatus))) {
            'open', 'scheduled', 'published', 'terbuka' => 'scheduled',
            'active', 'ongoing', 'berlangsung', 'aktif'  => 'active',
            'completed', 'closed', 'selesai'             => 'completed',
            'draft', 'archived', 'draf'                  => 'draft',
            default                                      => in_array($rawStatus, ['scheduled', 'active', 'completed', 'draft'], true) ? $rawStatus : 'scheduled',
        };

        $rawQuiz = $this->request->getPost('quiz_weight');
        $quizWeight = ($rawQuiz !== null && $rawQuiz !== '') ? (float) $rawQuiz : 40.0;

        $rawAssignment = $this->request->getPost('assignment_weight');
        $assignmentWeight = ($rawAssignment !== null && $rawAssignment !== '') ? (float) $rawAssignment : 0.0;

        $rawPostTest = $this->request->getPost('post_test_weight');
        $postTestWeight = ($rawPostTest !== null && $rawPostTest !== '') ? (float) $rawPostTest : 60.0;

        // Validasi aturan kurikulum PBJ: Bobot Post-Test tepat 60% dan total bobot tepat 100%
        if (abs($postTestWeight - 60.00) > 0.01) {
            session()->setFlashdata('error', 'Sesuai regulasi standar kurikulum PBJ, Bobot Post-Test harus bernilai tepat 60%.');
            return null;
        }

        $totalWeight = round($quizWeight + $assignmentWeight + $postTestWeight, 2);
        if (abs($totalWeight - 100.00) > 0.01) {
            session()->setFlashdata('error', "Total akumulasi bobot penilaian (Kuis + Tugas + Post-Test) harus berjumlah tepat 100%. Total saat ini: {$totalWeight}%. (Kuis + Tugas harus berjumlah 40%)");
            return null;
        }

        $rawGrade = $this->request->getPost('passing_grade');
        $passingGrade = ($rawGrade !== null && $rawGrade !== '') ? (float) $rawGrade : 70.0;

        $rawCap = $this->request->getPost('capacity');
        $capacity = ($rawCap !== null && $rawCap !== '') ? (int) $rawCap : null;

        $rawDesc        = trim((string) $this->request->getPost('description'));
        $stagesSubtitle = trim((string) $this->request->getPost('batch_stages_subtitle'));
        $rawStages      = (array) ($this->request->getPost('batch_stages') ?: []);
        $customDuration = $this->request->getPost('custom_duration_minutes') ? (int) $this->request->getPost('custom_duration_minutes') : null;

        $cleanedStages = [];
        foreach ($rawStages as $stg) {
            if (! is_array($stg)) {
                continue;
            }
            $t = trim((string) ($stg['title'] ?? ''));
            $d = trim((string) ($stg['description'] ?? ''));
            if ($t !== '' || $d !== '') {
                $cleanedStages[] = [
                    'number'      => count($cleanedStages) + 1,
                    'title'       => $t,
                    'description' => $d,
                ];
            }
        }

        $packedDescription = build_class_batch_description($rawDesc, $cleanedStages, $stagesSubtitle, $customDuration);

        return [
            'course_id'             => (int) $this->request->getPost('course_id'),
            'code'                  => trim((string) $this->request->getPost('code')),
            'name'                  => trim((string) $this->request->getPost('name')),
            'description'           => $packedDescription,
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
            'capacity'              => $capacity,
            'certificate_enabled'   => $this->request->getPost('certificate_enabled') ? 1 : 0,
            'passing_grade'         => $passingGrade,
            'quiz_weight'           => $quizWeight,
            'assignment_weight'     => $assignmentWeight,
            'post_test_weight'      => $postTestWeight,
        ];
    }

    public function duplicateBatch(string $id)
    {
        if (! $this->canManageClasses()) {
            return $this->forbidClassManagement();
        }

        $source = $this->classModel->find($id);
        if ($source === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas sumber tidak ditemukan.');
        }

        $newName = trim((string) $this->request->getPost('new_name'));
        $newCode = strtoupper(trim((string) $this->request->getPost('new_code')));
        $startAt = $this->request->getPost('start_at') ?: null;
        $endAt   = $this->request->getPost('end_at') ?: null;
        $copyTrainers = (bool) $this->request->getPost('copy_trainers');

        if ($newName === '' || $newCode === '') {
            return redirect()->back()->withInput()->with('error', 'Nama dan kode kelas baru wajib diisi.');
        }

        $existing = $this->classModel->where('code', $newCode)->first();
        if ($existing !== null) {
            return redirect()->back()->withInput()->with('error', "Kode kelas '{$newCode}' sudah digunakan. Silakan gunakan kode unik lainnya.");
        }

        $db = db_connect();
        $db->transStart();

        $userId = (int) (session()->get('user_id') ?: 1);

        $newClassData = [
            'course_id'             => $source['course_id'],
            'code'                  => $newCode,
            'name'                  => $newName,
            'description'           => $source['description'],
            'cover_file_id'         => $source['cover_file_id'] ?? null,
            'cover_url'             => $source['cover_url'] ?? null,
            'featured'              => 0,
            'carousel_enabled'      => 0,
            'status'                => 'draft',
            'learning_method'       => $source['learning_method'] ?? 'online',
            'location_or_link'      => $source['location_or_link'] ?? null,
            'target_audience'       => $source['target_audience'] ?? null,
            'registration_open_at'  => $this->request->getPost('registration_open_at') ?: null,
            'registration_close_at' => $this->request->getPost('registration_close_at') ?: null,
            'start_at'              => $startAt,
            'end_at'                => $endAt,
            'capacity'              => (int) ($this->request->getPost('capacity') ?: $source['capacity']),
            'certificate_enabled'   => (int) ($source['certificate_enabled'] ?? 1),
            'passing_grade'         => (int) ($source['passing_grade'] ?? 70),
            'quiz_weight'           => (float) ($source['quiz_weight'] ?? 40),
            'assignment_weight'     => (float) ($source['assignment_weight'] ?? 0),
            'post_test_weight'      => (float) ($source['post_test_weight'] ?? 60),
            'created_by'            => $userId,
            'updated_by'            => $userId,
        ];

        $newClassId = (int) $this->classModel->insert($newClassData, true);

        // Duplicate modules & lessons
        $modules = $db->table('learning_modules')
            ->where('class_id', (int) $id)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $lessonMap = [];

        foreach ($modules as $m) {
            $newModData = [
                'class_id'    => $newClassId,
                'title'       => $m['title'],
                'description' => $m['description'],
                'sort_order'  => (int) $m['sort_order'],
                'is_required' => (int) $m['is_required'],
                'status'      => $m['status'],
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
            $db->table('learning_modules')->insert($newModData);
            $newModId = (int) $db->insertID();

            $lessons = $db->table('lessons')
                ->where('module_id', (int) $m['id'])
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();

            foreach ($lessons as $l) {
                $newLessonData = [
                    'module_id'    => $newModId,
                    'title'        => $l['title'],
                    'slug'         => $l['slug'] . '-b' . $newClassId,
                    'content_type' => $l['content_type'],
                    'content'      => $l['content'],
                    'file_id'      => $l['file_id'],
                    'external_url' => $l['external_url'],
                    'sort_order'   => (int) $l['sort_order'],
                    'is_required'  => (int) $l['is_required'],
                    'status'       => $l['status'],
                    'visibility'   => $l['visibility'],
                    'created_by'   => $userId,
                    'updated_by'   => $userId,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ];
                $db->table('lessons')->insert($newLessonData);
                $newLessonId = (int) $db->insertID();
                $lessonMap[(int) $l['id']] = $newLessonId;
            }
        }

        // Duplicate quizzes
        $quizzes = $db->table('quizzes')
            ->where('class_id', (int) $id)
            ->get()->getResultArray();

        foreach ($quizzes as $q) {
            $mappedLessonId = ! empty($q['lesson_id']) && isset($lessonMap[(int) $q['lesson_id']])
                ? $lessonMap[(int) $q['lesson_id']]
                : null;

            $newQuizData = [
                'class_id'         => $newClassId,
                'lesson_id'        => $mappedLessonId,
                'type'             => $q['type'],
                'title'            => $q['title'],
                'instructions'     => $q['instructions'],
                'duration_minutes' => (int) $q['duration_minutes'],
                'attempt_limit'    => (int) $q['attempt_limit'],
                'is_required'      => (int) $q['is_required'],
                'status'           => $q['status'],
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ];
            $db->table('quizzes')->insert($newQuizData);
            $newQuizId = (int) $db->insertID();

            $questions = $db->table('questions')
                ->where('quiz_id', (int) $q['id'])
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();

            foreach ($questions as $quest) {
                $newQuestData = [
                    'quiz_id'     => $newQuizId,
                    'question'    => $quest['question'],
                    'type'        => $quest['type'] ?? 'multiple_choice',
                    'points'      => $quest['points'] ?? 10,
                    'sort_order'  => (int) ($quest['sort_order'] ?? 1),
                    'explanation' => $quest['explanation'] ?? null,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
                $db->table('questions')->insert($newQuestData);
                $newQuestId = (int) $db->insertID();

                $options = $db->table('question_options')
                    ->where('question_id', (int) $quest['id'])
                    ->orderBy('sort_order', 'ASC')
                    ->get()->getResultArray();

                foreach ($options as $opt) {
                    $newOptData = [
                        'question_id' => $newQuestId,
                        'option_text' => $opt['option_text'],
                        'is_correct'  => (int) $opt['is_correct'],
                        'sort_order'  => (int) ($opt['sort_order'] ?? 1),
                        'created_at'  => date('Y-m-d H:i:s'),
                        'updated_at'  => date('Y-m-d H:i:s'),
                    ];
                    $db->table('question_options')->insert($newOptData);
                }
            }
        }

        // Copy trainers if requested
        if ($copyTrainers) {
            $trainers = $db->table('class_trainers')
                ->where('class_id', (int) $id)
                ->where('status', 'active')
                ->get()->getResultArray();

            foreach ($trainers as $tr) {
                $db->table('class_trainers')->insert([
                    'class_id'    => $newClassId,
                    'user_id'     => $tr['user_id'],
                    'role'        => $tr['role'] ?? 'lead',
                    'status'      => 'active',
                    'assigned_by' => $userId,
                    'assigned_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Audit log
        $auditService = new \App\Services\AuditService();
        $auditService->log(
            $userId,
            'learning.class.duplicated',
            'learning',
            'class',
            $newClassId,
            [
                'source_class_id' => (int) $id,
                'source_code'     => $source['code'],
                'new_class_id'    => $newClassId,
                'new_code'        => $newCode,
                'modules_copied'  => count($modules),
            ]
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan basis data saat menduplikasi kelas.');
        }

        return redirect()->to(site_url('dashboard/pembelajaran/kelas/' . $newClassId))
            ->with('success', "Angkatan kelas baru '{$newName}' ({$newCode}) berhasil diduplikasi dari Batch #{$id} beserta seluruh modul dan kurikulum.");
    }

    public function exportGradebook(string $id)
    {
        $class = $this->classModel
            ->select('learning_classes.*, courses.title AS course_title')
            ->join('courses', 'courses.id = learning_classes.course_id', 'left')
            ->find($id);

        if ($class === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $db = db_connect();
        $enrollments = $db->table('enrollments e')
            ->select('e.*, u.name AS user_name, u.email AS user_email, u.username, asn.employee_identifier AS nip, COALESCE(asn.institution_name, asn.unit_name) AS instansi,
                      cert.id AS certificate_id, cert.certificate_number, cert.status AS cert_status')
            ->join('users u', 'u.id = e.user_id')
            ->join('asn_profiles asn', 'asn.user_id = u.id', 'left')
            ->join('certificates cert', 'cert.source_type = "learning_enrollment" AND cert.source_id = e.id AND cert.status = "issued"', 'left')
            ->where('e.class_id', (int) $id)
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();

        $passingGrade = (int) ($class['passing_grade'] ?? 70);

        // UTF-8 BOM
        $csv = "\xEF\xBB\xBF";
        $csv .= "GRADEBOOK NILAI KELAS PBJ - " . str_replace(',', ' ', $class['name']) . " (" . $class['code'] . ")\n";
        $csv .= "No,Nama Peserta,NIP,Email,Instansi / OPD,Nilai Akhir,Passing Grade,Status Kelulusan,Nomor Sertifikat,Status Sertifikat,Tanggal Terdaftar\n";

        $no = 1;
        foreach ($enrollments as $row) {
            $finalScore = $row['final_score'] !== null ? (float) $row['final_score'] : 0.0;
            $isPassed = in_array($row['status'], ['completed', 'passed'], true) || ($finalScore >= $passingGrade && $row['status'] === 'active');
            $passStatus = $isPassed ? 'LULUS' : 'BELUM LULUS';

            $certStatus = ! empty($row['certificate_id'])
                ? 'DITERBITKAN'
                : ($isPassed ? 'LAYAK SERTIFIKAT' : 'BELUM LAYAK');

            $certNum = ! empty($row['certificate_number']) ? $row['certificate_number'] : '-';

            $line = [
                $no++,
                '"' . str_replace('"', '""', (string) $row['user_name']) . '"',
                '"' . str_replace('"', '""', (string) ($row['nip'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string) $row['user_email']) . '"',
                '"' . str_replace('"', '""', (string) ($row['instansi'] ?? '-')) . '"',
                number_format($finalScore, 1, '.', ''),
                $passingGrade,
                $passStatus,
                '"' . str_replace('"', '""', $certNum) . '"',
                $certStatus,
                ! empty($row['enrolled_at']) ? date('d/m/Y H:i', strtotime($row['enrolled_at'])) : '-',
            ];

            $csv .= implode(',', $line) . "\n";
        }

        $auditService = new \App\Services\AuditService();
        $auditService->log(
            (int) (session()->get('user_id') ?: 1),
            'learning.gradebook.exported',
            'learning',
            'class',
            (int) $id,
            [
                'class_code' => $class['code'],
                'total_rows' => count($enrollments),
            ]
        );

        $safeCode = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $class['code']);
        $filename = 'Buku_Nilai_' . $safeCode . '_' . date('Ymd_His') . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }
}

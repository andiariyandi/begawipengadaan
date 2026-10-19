<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\CourseModel;
use App\Models\Learning\ProgramModel;

class CourseController extends BaseController
{
    protected CourseModel $courseModel;
    protected ProgramModel $programModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->courseModel = new CourseModel();
        $this->programModel = new ProgramModel();
    }

    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status = trim((string) $this->request->getGet('status'));
        $programId = (int) $this->request->getGet('program_id');

        $builder = $this->courseModel
            ->select('courses.*, programs.title AS program_title,
                      (SELECT COUNT(1) FROM learning_classes lc WHERE lc.course_id = courses.id) AS class_count,
                      (SELECT COUNT(DISTINCT e.user_id) FROM enrollments e JOIN learning_classes lc ON lc.id = e.class_id WHERE lc.course_id = courses.id) AS student_count,
                      (SELECT COUNT(1) FROM enrollments e JOIN learning_classes lc ON lc.id = e.class_id WHERE lc.course_id = courses.id AND e.status IN ("completed", "passed")) AS completed_count')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->orderBy('courses.created_at', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('courses.title', $keyword)
                ->orLike('courses.slug', $keyword)
                ->orLike('courses.summary', $keyword)
                ->groupEnd();
        }
        if ($status !== '') {
            $builder->where('courses.status', $status);
        }
        if ($programId > 0) {
            $builder->where('courses.program_id', $programId);
        }

        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_courses') ?: 1));
        $total = $this->courseModel->countAllResults(false);
        $courses = $this->courseModel->findAll($perPage, ($page - 1) * $perPage);

        $pager = service('pager');
        $pager->store('courses', $page, $perPage, $total, 0);

        $db = db_connect();
        $stats = [
            'total'     => (int) $db->table('courses')->countAllResults(),
            'published' => (int) $db->table('courses')->where('status', 'published')->countAllResults(),
            'draft'     => (int) $db->table('courses')->where('status', 'draft')->countAllResults(),
            'archived'  => (int) $db->table('courses')->where('status', 'archived')->countAllResults(),
        ];

        return view('dashboard/learning/courses/index', [
            'title'         => 'Course | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Course Pembelajaran',
            'courses'       => $courses,
            'stats'         => $stats,
            'pager'         => $pager,
            'programs'      => $this->programModel->orderBy('title', 'ASC')->findAll(),
            'filters'       => [
                'keyword'    => $keyword,
                'status'     => $status,
                'program_id' => $programId,
            ],
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'courses',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/courses/form', [
            'title' => 'Tambah Course | Dashboard BegawiPengadaan',
            'pageTitle' => 'Tambah Course',
            'formMode' => 'create',
            'course' => null,
            'programs' => $this->programModel->orderBy('title', 'ASC')->findAll(),
            'activeMenu' => 'learning',
            'activeSubmenu' => 'courses',
        ]);
    }

    public function store()
    {
        $data = $this->validatedCourseData();
        if ($data === null) {
            return redirect()->back()->withInput();
        }

        $userId = session()->get('user_id');
        if ($userId !== null) {
            $data['created_by'] = (int) $userId;
        }

        $this->courseModel->insert($data);

        return redirect()->to(route_to('dashboard.learning.courses'))->with('success', 'Course berhasil disimpan.');
    }

    public function detail(string $id): string
    {
        $course = $this->courseModel
            ->select('courses.*, programs.title AS program_title')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->find($id);
        if ($course === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Course tidak ditemukan.');
        }

        return view('dashboard/learning/courses/detail', [
            'title' => 'Detail Course | Dashboard BegawiPengadaan',
            'pageTitle' => 'Detail Course',
            'courseId' => $id,
            'course' => $course,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'courses',
        ]);
    }

    public function edit(string $id): string
    {
        $course = $this->courseModel->find($id);
        if ($course === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Course tidak ditemukan.');
        }

        return view('dashboard/learning/courses/form', [
            'title' => 'Ubah Course | Dashboard BegawiPengadaan',
            'pageTitle' => 'Ubah Course',
            'formMode' => 'edit',
            'courseId' => $id,
            'course' => $course,
            'programs' => $this->programModel->orderBy('title', 'ASC')->findAll(),
            'activeMenu' => 'learning',
            'activeSubmenu' => 'courses',
        ]);
    }

    public function update(string $id)
    {
        $existing = $this->courseModel->find($id);
        if ($existing === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Course tidak ditemukan.');
        }

        $data = $this->validatedCourseData($existing['cover_url'] ?? null, $existing['cover_file_id'] ?? null);
        if ($data === null) {
            return redirect()->back()->withInput();
        }

        $userId = session()->get('user_id');
        if ($userId !== null) {
            $data['updated_by'] = (int) $userId;
        }

        $this->courseModel->update($id, $data);

        return redirect()->to(route_to('dashboard.learning.courses'))->with('success', 'Course berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        $course = $this->courseModel->find($id);
        if ($course === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Course tidak ditemukan.');
        }

        $db = db_connect();
        $classCount = (int) $db->table('learning_classes')->where('course_id', $id)->countAllResults();
        if ($classCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus course: Masih terdapat {$classCount} kelas terkait pada course ini.");
        }

        $this->courseModel->delete($id);

        return redirect()->to(route_to('dashboard.learning.courses'))->with('success', 'Course berhasil dihapus.');
    }

    private function validatedCourseData(?string $existingCoverUrl = null, ?int $existingCoverFileId = null): ?array
    {
        if (! $this->validate([
            'program_id' => 'required|is_natural_no_zero',
            'title'      => 'required',
            'slug'       => 'required',
            'status'     => 'permit_empty',
            'visibility' => 'permit_empty',
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

            $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'learning' . DIRECTORY_SEPARATOR . 'courses';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $storedName = $file->getRandomName();
            if ($file->move($targetDir, $storedName)) {
                $uploadedUrl = 'uploads/learning/courses/' . $storedName;

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
                        'resource_type'    => 'course_cover',
                        'resource_id'      => null,
                        'uploaded_by'      => (int) session()->get('user_id'),
                        'available_at'     => $now,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                    $uploadedFileId = (int) $db->insertID();
                } catch (\Throwable $e) {
                    log_message('error', 'File asset registration failed: ' . $e->getMessage());
                }
            }
        }

        $urlPost = $this->request->getPost('cover_url');
        $coverUrl = $uploadedUrl ?: (trim((string) $urlPost) ?: $existingCoverUrl);
        $coverFileId = $uploadedFileId ?: ($this->request->getPost('cover_file_id') ? (int) $this->request->getPost('cover_file_id') : $existingCoverFileId);

        return [
            'program_id'          => (int) $this->request->getPost('program_id'),
            'title'               => trim((string) $this->request->getPost('title')),
            'slug'                => trim((string) $this->request->getPost('slug')),
            'summary'             => $this->request->getPost('summary'),
            'description'         => $this->request->getPost('description'),
            'target_audience'     => $this->request->getPost('target_audience'),
            'prerequisites'       => $this->request->getPost('prerequisites'),
            'learning_objectives' => $this->request->getPost('learning_objectives'),
            'level'               => $this->request->getPost('level') ?: 'basic',
            'duration_minutes'    => $this->request->getPost('duration_minutes') ? (int) $this->request->getPost('duration_minutes') : null,
            'status'              => $this->request->getPost('status') ?: 'draft',
            'visibility'          => $this->request->getPost('visibility') ?: 'public',
            'featured'            => $this->request->getPost('featured') ? 1 : 0,
            'cover_file_id'       => $coverFileId,
            'cover_url'           => $coverUrl,
        ];
    }
}

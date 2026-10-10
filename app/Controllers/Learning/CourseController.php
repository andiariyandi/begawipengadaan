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
            'title'         => 'Course | Dashboard BegawiPBJ',
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
            'title' => 'Tambah Course | Dashboard BegawiPBJ',
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
            'title' => 'Detail Course | Dashboard BegawiPBJ',
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
            'title' => 'Ubah Course | Dashboard BegawiPBJ',
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
        if ($this->courseModel->find($id) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Course tidak ditemukan.');
        }

        $data = $this->validatedCourseData();
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

    private function validatedCourseData(): ?array
    {
        if (! $this->validate([
            'program_id' => 'required|is_natural_no_zero',
            'title' => 'required',
            'slug' => 'required',
            'status' => 'permit_empty',
            'visibility' => 'permit_empty',
        ])) {
            return null;
        }

        return [
            'program_id' => (int) $this->request->getPost('program_id'),
            'title' => trim((string) $this->request->getPost('title')),
            'slug' => trim((string) $this->request->getPost('slug')),
            'summary' => $this->request->getPost('summary'),
            'description' => $this->request->getPost('description'),
            'level' => $this->request->getPost('level') ?: null,
            'duration_minutes' => $this->request->getPost('duration_minutes') ?: null,
            'status' => $this->request->getPost('status') ?: null,
            'visibility' => $this->request->getPost('visibility') ?: null,
            'cover_file_id' => $this->request->getPost('cover_file_id') ?: null,
        ];
    }
}

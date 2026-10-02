<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\CourseModel;
use App\Models\Learning\LearningClassModel;

class LearningClassController extends BaseController
{
    protected LearningClassModel $classModel;
    protected CourseModel $courseModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->classModel = new LearningClassModel();
        $this->courseModel = new CourseModel();
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

        return view('dashboard/learning/classes/detail', [
            'title' => 'Detail Kelas | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Kelas',
            'classId' => $id,
            'class' => $class,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'classes',
        ]);
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

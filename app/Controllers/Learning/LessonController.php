<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\LessonModel;
use App\Models\Learning\LearningModuleModel;

class LessonController extends BaseController
{
    protected LessonModel $lessonModel;
    protected LearningModuleModel $moduleModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->lessonModel = new LessonModel();
        $this->moduleModel = new LearningModuleModel();
    }

    public function index(): string
    {
        $lessons = $this->lessonModel
            ->select('lessons.*, learning_modules.title AS module_title')
            ->join('learning_modules', 'learning_modules.id = lessons.module_id', 'left')
            ->orderBy('lessons.sort_order', 'ASC')
            ->findAll();

        return view('dashboard/learning/lessons/index', [
            'title' => 'Lesson | Dashboard BegawiPengadaan',
            'pageTitle' => 'Lesson',
            'lessons' => $lessons,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/lessons/form', [
            'title' => 'Tambah Lesson | Dashboard BegawiPengadaan',
            'pageTitle' => 'Tambah Lesson',
            'formMode' => 'create',
            'lesson' => null,
            'modules' => $this->moduleModel->orderBy('title', 'ASC')->findAll(),
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function store()
    {
        $data = $this->validatedLessonData();
        if ($data === null) {
            return redirect()->back()->withInput();
        }

        if (($userId = session()->get('user_id')) !== null) {
            $data['created_by'] = (int) $userId;
        }
        $this->lessonModel->insert($data);
        return redirect()->to(route_to('dashboard.learning.lessons'))->with('success', 'Lesson berhasil disimpan.');
    }

    public function detail(string $id): string
    {
        $lesson = $this->lessonModel
            ->select('lessons.*, learning_modules.title AS module_title')
            ->join('learning_modules', 'learning_modules.id = lessons.module_id', 'left')
            ->find($id);
        if ($lesson === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Lesson tidak ditemukan.');
        }

        return view('dashboard/learning/lessons/detail', [
            'title' => 'Detail Lesson | Dashboard BegawiPengadaan',
            'pageTitle' => 'Detail Lesson',
            'lessonId' => $id,
            'lesson' => $lesson,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function edit(string $id): string
    {
        $lesson = $this->lessonModel->find($id);
        if ($lesson === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Lesson tidak ditemukan.');
        }

        return view('dashboard/learning/lessons/form', [
            'title' => 'Ubah Lesson | Dashboard BegawiPengadaan',
            'pageTitle' => 'Ubah Lesson',
            'formMode' => 'edit',
            'lesson' => $lesson,
            'modules' => $this->moduleModel->orderBy('title', 'ASC')->findAll(),
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function update(string $id)
    {
        if ($this->lessonModel->find($id) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Lesson tidak ditemukan.');
        }

        $data = $this->validatedLessonData();
        if ($data === null) {
            return redirect()->back()->withInput();
        }

        if (($userId = session()->get('user_id')) !== null) {
            $data['updated_by'] = (int) $userId;
        }
        $this->lessonModel->update($id, $data);
        return redirect()->to(route_to('dashboard.learning.lessons'))->with('success', 'Lesson berhasil diperbarui.');
    }

    private function validatedLessonData(): ?array
    {
        if (! $this->validate([
            'module_id' => 'required|is_natural_no_zero',
            'title' => 'required',
            'content_type' => 'required|in_list[text,document,video,link]',
            'sort_order' => 'required|is_natural',
            'status' => 'permit_empty',
            'visibility' => 'permit_empty|in_list[public,internal,restricted]',
        ])) {
            return null;
        }

        return [
            'module_id' => (int) $this->request->getPost('module_id'),
            'title' => trim((string) $this->request->getPost('title')),
            'slug' => $this->request->getPost('slug') ?: null,
            'content_type' => $this->request->getPost('content_type'),
            'content' => $this->request->getPost('content'),
            'file_id' => $this->request->getPost('file_id') ?: null,
            'external_url' => $this->request->getPost('external_url') ?: null,
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'status' => $this->request->getPost('status') ?: 'draft',
            'visibility' => $this->request->getPost('visibility') ?: 'internal',
        ];
    }
}

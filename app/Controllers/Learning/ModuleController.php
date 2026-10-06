<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\LearningModuleModel;
use App\Models\Learning\LessonModel;

class ModuleController extends BaseController
{
    protected LearningModuleModel $moduleModel;
    protected LearningClassModel $classModel;
    protected LessonModel $lessonModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->moduleModel = new LearningModuleModel();
        $this->classModel = new LearningClassModel();
        $this->lessonModel = new LessonModel();
    }

    public function index(): string
    {
        $modules = $this->moduleModel
            ->select('learning_modules.*, learning_classes.name AS class_name, courses.title AS course_title')
            ->join('learning_classes', 'learning_classes.id = learning_modules.class_id', 'left')
            ->join('courses', 'courses.id = learning_classes.course_id', 'left')
            ->orderBy('learning_modules.sort_order', 'ASC')
            ->findAll();

        return view('dashboard/learning/modules/index', [
            'title' => 'Materi dan Modul | Dashboard BegawiPBJ',
            'pageTitle' => 'Materi dan Modul',
            'modules' => $modules,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/modules/form', [
            'title' => 'Tambah Materi atau Modul | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Materi atau Modul',
            'formMode' => 'create',
            'module' => null,
            'classes' => $this->classModel->orderBy('name', 'ASC')->findAll(),
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function store()
    {
        $data = $this->validatedModuleData();
        if ($data === null) {
            return redirect()->back()->withInput();
        }

        $this->moduleModel->insert($data);
        return redirect()->to(route_to('dashboard.learning.modules'))->with('success', 'Modul berhasil disimpan.');
    }

    public function detail(string $id): string
    {
        $module = $this->moduleModel
            ->select('learning_modules.*, learning_classes.name AS class_name, courses.title AS course_title')
            ->join('learning_classes', 'learning_classes.id = learning_modules.class_id', 'left')
            ->join('courses', 'courses.id = learning_classes.course_id', 'left')
            ->find($id);
        if ($module === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Modul tidak ditemukan.');
        }

        return view('dashboard/learning/modules/detail', [
            'title' => 'Detail Materi atau Modul | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Materi atau Modul',
            'moduleId' => $id,
            'module' => $module,
            'lessons' => $this->lessonModel->where('module_id', $id)->orderBy('sort_order', 'ASC')->findAll(),
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function edit(string $id): string
    {
        $module = $this->moduleModel->find($id);
        if ($module === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Modul tidak ditemukan.');
        }

        return view('dashboard/learning/modules/form', [
            'title' => 'Ubah Modul | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Modul',
            'formMode' => 'edit',
            'module' => $module,
            'classes' => $this->classModel->orderBy('name', 'ASC')->findAll(),
            'activeMenu' => 'learning',
            'activeSubmenu' => 'modules',
        ]);
    }

    public function update(string $id)
    {
        if ($this->moduleModel->find($id) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Modul tidak ditemukan.');
        }

        $data = $this->validatedModuleData();
        if ($data === null) {
            return redirect()->back()->withInput();
        }

        $this->moduleModel->update($id, $data);
        return redirect()->to(route_to('dashboard.learning.modules'))->with('success', 'Modul berhasil diperbarui.');
    }

    private function validatedModuleData(): ?array
    {
        if (! $this->validate([
            'class_id' => 'required|is_natural_no_zero',
            'title' => 'required',
            'sort_order' => 'required|is_natural',
            'status' => 'permit_empty',
        ])) {
            return null;
        }

        return [
            'class_id' => (int) $this->request->getPost('class_id'),
            'title' => trim((string) $this->request->getPost('title')),
            'description' => $this->request->getPost('description'),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'status' => $this->request->getPost('status') ?: 'draft',
        ];
    }
}

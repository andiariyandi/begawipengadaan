<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\LearningModuleModel;
use App\Models\Learning\LessonModel;

class CurriculumController extends BaseController
{
    protected LearningClassModel $classModel;
    protected LearningModuleModel $moduleModel;
    protected LessonModel $lessonModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->classModel = new LearningClassModel();
        $this->moduleModel = new LearningModuleModel();
        $this->lessonModel = new LessonModel();
    }

    public function index(?string $classId = null): string
    {
        $db = db_connect();
        $classes = $this->classModel
            ->select('learning_classes.*, courses.title AS course_title')
            ->join('courses', 'courses.id = learning_classes.course_id', 'left')
            ->orderBy('learning_classes.created_at', 'DESC')
            ->findAll();

        $selectedClassId = (int) ($classId ?? $this->request->getGet('class_id') ?? ($classes[0]['id'] ?? 0));
        $selectedClass = null;

        foreach ($classes as $c) {
            if ((int) $c['id'] === $selectedClassId) {
                $selectedClass = $c;
                break;
            }
        }

        if (! $selectedClass && ! empty($classes)) {
            $selectedClass = $classes[0];
            $selectedClassId = (int) $selectedClass['id'];
        }

        $modules = [];
        $totalLessons = 0;

        if ($selectedClassId > 0) {
            $modules = $this->moduleModel
                ->where('class_id', $selectedClassId)
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            foreach ($modules as &$mod) {
                $lessons = $this->lessonModel
                    ->where('module_id', $mod['id'])
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
                $mod['lessons'] = $lessons;
                $totalLessons += count($lessons);
            }
            unset($mod);
        }

        return view('dashboard/learning/curriculum/index', [
            'title'           => 'Manajemen Kurikulum | Dashboard BegawiPBJ',
            'pageTitle'       => 'Manajemen Kurikulum Pembelajaran',
            'classes'         => $classes,
            'selectedClass'   => $selectedClass,
            'selectedClassId' => $selectedClassId,
            'modules'         => $modules,
            'totalLessons'    => $totalLessons,
            'activeMenu'      => 'learning',
            'activeSubmenu'   => 'curriculum',
        ]);
    }

    public function storeModule()
    {
        $classId = (int) $this->request->getPost('class_id');
        $title = trim((string) $this->request->getPost('title'));
        $sortOrder = (int) $this->request->getPost('sort_order');

        if ($classId < 1 || $title === '') {
            return redirect()->back()->withInput()->with('error', 'Nama modul dan kelas wajib diisi.');
        }

        $this->moduleModel->insert([
            'class_id'    => $classId,
            'title'       => $title,
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => $sortOrder > 0 ? $sortOrder : 1,
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'status'      => $this->request->getPost('status') ?: 'published',
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId))
            ->with('success', 'Modul pembelajaran berhasil ditambahkan.');
    }

    public function updateModule(string $id)
    {
        $module = $this->moduleModel->find($id);
        if ($module === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Modul tidak ditemukan.');
        }

        $title = trim((string) $this->request->getPost('title'));
        if ($title === '') {
            return redirect()->back()->withInput()->with('error', 'Nama modul tidak boleh kosong.');
        }

        $this->moduleModel->update($id, [
            'title'       => $title,
            'description' => $this->request->getPost('description') ?: null,
            'sort_order'  => (int) $this->request->getPost('sort_order'),
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'status'      => $this->request->getPost('status') ?: 'published',
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $module['class_id']))
            ->with('success', 'Modul berhasil diperbarui.');
    }

    public function deleteModule(string $id)
    {
        $module = $this->moduleModel->find($id);
        if ($module === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Modul tidak ditemukan.');
        }

        $classId = $module['class_id'];
        $lessonCount = $this->lessonModel->where('module_id', $id)->countAllResults();
        if ($lessonCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus modul: Masih ada {$lessonCount} materi/lesson di dalam modul ini. Hapus materi terlebih dahulu.");
        }

        $this->moduleModel->delete($id);

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId))
            ->with('success', 'Modul berhasil dihapus.');
    }

    public function storeLesson()
    {
        $moduleId = (int) $this->request->getPost('module_id');
        $title = trim((string) $this->request->getPost('title'));
        $contentType = $this->request->getPost('content_type') ?: 'text';

        if ($moduleId < 1 || $title === '') {
            return redirect()->back()->withInput()->with('error', 'Judul materi dan modul wajib diisi.');
        }

        $module = $this->moduleModel->find($moduleId);
        if ($module === null) {
            return redirect()->back()->with('error', 'Modul tidak ditemukan.');
        }

        $slug = url_title($title, '-', true) . '-' . substr(uniqid(), -4);
        $userId = session()->get('user_id');

        $this->lessonModel->insert([
            'module_id'    => $moduleId,
            'title'        => $title,
            'slug'         => $slug,
            'content_type' => $contentType,
            'content'      => $this->request->getPost('content') ?: null,
            'external_url' => $this->request->getPost('external_url') ?: null,
            'sort_order'   => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_required'  => $this->request->getPost('is_required') ? 1 : 0,
            'status'       => $this->request->getPost('status') ?: 'published',
            'visibility'   => 'internal',
            'created_by'   => $userId ? (int) $userId : null,
        ]);

        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $module['class_id']))
            ->with('success', 'Materi pembelajaran (lesson) berhasil ditambahkan.');
    }

    public function updateLesson(string $id)
    {
        $lesson = $this->lessonModel->find($id);
        if ($lesson === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Materi tidak ditemukan.');
        }

        $module = $this->moduleModel->find($lesson['module_id']);
        $title = trim((string) $this->request->getPost('title'));

        if ($title === '') {
            return redirect()->back()->withInput()->with('error', 'Judul materi tidak boleh kosong.');
        }

        $this->lessonModel->update($id, [
            'title'        => $title,
            'content_type' => $this->request->getPost('content_type') ?: $lesson['content_type'],
            'content'      => $this->request->getPost('content') ?: null,
            'external_url' => $this->request->getPost('external_url') ?: null,
            'sort_order'   => (int) $this->request->getPost('sort_order'),
            'is_required'  => $this->request->getPost('is_required') ? 1 : 0,
            'status'       => $this->request->getPost('status') ?: 'published',
            'updated_by'   => (int) session()->get('user_id'),
        ]);

        $redirectClass = $module ? $module['class_id'] : '';
        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $redirectClass))
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function deleteLesson(string $id)
    {
        $lesson = $this->lessonModel->find($id);
        if ($lesson === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Materi tidak ditemukan.');
        }

        $module = $this->moduleModel->find($lesson['module_id']);
        $this->lessonModel->delete($id);

        $redirectClass = $module ? $module['class_id'] : '';
        return redirect()->to(site_url('dashboard/pembelajaran/kurikulum/kelas/' . $redirectClass))
            ->with('success', 'Materi berhasil dihapus.');
    }
}

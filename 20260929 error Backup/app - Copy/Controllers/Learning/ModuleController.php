<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\ClassModel;
use App\Services\AuditService;

class ModuleController extends BaseController
{
    public function index(int $classId)
    {
        $class       = (new ClassModel())->withCourseTitle()->find($classId);
        $moduleModel = new ModuleModel();
        $lessonModel = new LessonModel();

        $modules = $moduleModel->forClass($classId);
        foreach ($modules as &$module) {
            $module['lessons'] = $lessonModel->forModule($module['id']);
        }
        unset($module);

        return view('learning/modules/index', [
            'title'    => 'Modul & Materi',
            'subTitle' => 'Modul & Materi',
            'class'    => $class,
            'modules'  => $modules,
        ]);
    }

    public function storeModule(int $classId)
    {
        $rules = ['title' => 'required|max_length[200]'];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Judul modul wajib diisi.');
        }

        $moduleModel = new ModuleModel();
        $lastOrder   = $moduleModel->where('class_id', $classId)->countAllResults();

        $moduleId = $moduleModel->insert([
            'class_id'       => $classId,
            'title'          => $this->request->getPost('title'),
            'sequence_order' => $lastOrder + 1,
        ], true);

        (new AuditService())->log(session()->get('user_id'), 'create', 'lms', 'modules', $moduleId);

        return redirect()->to(route_to('learning.modules', $classId))->with('message', 'Modul berhasil ditambahkan.');
    }

    public function storeLesson(int $classId, int $moduleId)
    {
        $contentType = $this->request->getPost('content_type');

        $rules = [
            'title'        => 'required|max_length[200]',
            'content_type' => 'required|in_list[document,video,text]',
        ];

        if ($contentType === 'text') {
            $rules['content_text'] = 'required';
        }

        if (! $this->validate($rules)) {
            $message = $contentType === 'text'
                ? 'Judul dan isi teks materi wajib diisi.'
                : 'Judul, tipe, dan file materi wajib diisi.';
            return redirect()->back()->with('error', $message);
        }

        $filePath = null;
        $file     = $this->request->getFile('file');

        if ($contentType !== 'text') {
            if (! $file || ! $file->isValid() || $file->hasMoved()) {
                return redirect()->back()->with('error', 'File wajib diunggah untuk tipe Dokumen/Video.');
            }

            $allowedMimes = [
                'application/pdf', 'video/mp4', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];

            if (! in_array($file->getMimeType(), $allowedMimes, true)) {
                return redirect()->back()->with('error', 'Tipe file tidak diizinkan.');
            }

            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/lessons', $newName);
            $filePath = 'uploads/lessons/' . $newName;
        }

        $lessonModel = new LessonModel();
        $lastOrder   = $lessonModel->where('module_id', $moduleId)->countAllResults();

        $lessonId = $lessonModel->insert([
            'module_id'      => $moduleId,
            'title'          => $this->request->getPost('title'),
            'content_type'   => $contentType,
            'file_path'      => $filePath,
            'content_text'   => $contentType === 'text' ? $this->request->getPost('content_text') : null,
            'sequence_order' => $lastOrder + 1,
        ], true);

        (new AuditService())->log(session()->get('user_id'), 'create', 'lms', 'lessons', $lessonId);

        return redirect()->to(route_to('learning.modules', $classId))->with('message', 'Materi berhasil ditambahkan.');
    }
}

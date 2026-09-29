<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Services\AuditService;

class CourseController extends BaseController
{
    public function index()
    {
        $courseModel = new CourseModel();

        return view('learning/courses/index', [
            'title'    => 'Program Pelatihan',
            'subTitle' => 'Program Pelatihan',
            'courses'  => $courseModel->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function create()
    {
        return view('learning/courses/form', [
            'title'    => 'Tambah Program Pelatihan',
            'subTitle' => 'Program Pelatihan',
            'course'   => null,
        ]);
    }

    public function store()
    {
        $rules = ['title' => 'required|min_length[5]|max_length[200]'];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul program pelatihan wajib diisi (minimal 5 karakter).');
        }

        $courseModel = new CourseModel();
        $courseId    = $courseModel->insert([
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'category'    => $this->request->getPost('category'),
            'status'      => 'draft',
            'created_by'  => session()->get('user_id'),
        ], true);

        (new AuditService())->log(session()->get('user_id'), 'create', 'lms', 'courses', $courseId);

        return redirect()->to(route_to('learning.courses'))->with('message', 'Program pelatihan berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $course = (new CourseModel())->find($id);

        if (! $course) {
            return redirect()->to(route_to('learning.courses'))->with('error', 'Program pelatihan tidak ditemukan.');
        }

        return view('learning/courses/form', [
            'title'    => 'Edit Program Pelatihan',
            'subTitle' => 'Program Pelatihan',
            'course'   => $course,
        ]);
    }

    public function update(int $id)
    {
        $courseModel = new CourseModel();

        if (! $courseModel->find($id)) {
            return redirect()->to(route_to('learning.courses'))->with('error', 'Program pelatihan tidak ditemukan.');
        }

        $courseModel->update($id, [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'category'    => $this->request->getPost('category'),
            'status'      => $this->request->getPost('status'),
        ]);

        (new AuditService())->log(session()->get('user_id'), 'update', 'lms', 'courses', $id);

        return redirect()->to(route_to('learning.courses'))->with('message', 'Program pelatihan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        (new CourseModel())->delete($id);

        (new AuditService())->log(session()->get('user_id'), 'delete', 'lms', 'courses', $id);

        return redirect()->to(route_to('learning.courses'))->with('message', 'Program pelatihan berhasil dihapus.');
    }
}

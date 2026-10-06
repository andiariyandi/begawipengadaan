<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\CourseModel;
use App\Models\UserModel;
use App\Services\AuditService;

class ClassController extends BaseController
{
    public function index(int $courseId)
    {
        $classModel = new ClassModel();
        $course     = (new CourseModel())->find($courseId);

        if (! $course) {
            return redirect()->to(route_to('learning.courses'))->with('error', 'Program pelatihan tidak ditemukan.');
        }

        return view('learning/classes/index', [
            'title'    => 'Kelas',
            'subTitle' => 'Kelas — ' . $course['title'],
            'course'   => $course,
            'classes'  => $classModel->where('course_id', $courseId)->findAll(),
        ]);
    }

    public function create(int $courseId)
    {
        $userModel = new UserModel();

        $trainers = $userModel->db->table('users')
            ->select('users.id, users.username')
            ->join('user_roles', 'user_roles.user_id = users.id')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('roles.slug', 'trainer')
            ->get()->getResultArray();

        return view('learning/classes/form', [
            'title'    => 'Tambah Kelas',
            'subTitle' => 'Kelas',
            'courseId' => $courseId,
            'trainers' => $trainers,
            'class'    => null,
        ]);
    }

    public function store(int $courseId)
    {
        $rules = [
            'trainer_id' => 'required|is_natural_no_zero',
            'start_date' => 'required|valid_date',
            'end_date'   => 'required|valid_date',
            'quota'      => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Semua field kelas wajib diisi dengan benar.');
        }

        $classId = (new ClassModel())->insert([
            'course_id'  => $courseId,
            'trainer_id' => $this->request->getPost('trainer_id'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date'   => $this->request->getPost('end_date'),
            'quota'      => $this->request->getPost('quota'),
            'status'     => 'scheduled',
        ], true);

        (new AuditService())->log(session()->get('user_id'), 'create', 'lms', 'classes', $classId);

        return redirect()->to(route_to('learning.classes', $courseId))->with('message', 'Kelas berhasil dibuat.');
    }

    public function delete(int $courseId, int $classId)
    {
        (new ClassModel())->delete($classId);

        (new AuditService())->log(session()->get('user_id'), 'delete', 'lms', 'classes', $classId);

        return redirect()->to(route_to('learning.classes', $courseId))->with('message', 'Kelas berhasil dihapus.');
    }
}

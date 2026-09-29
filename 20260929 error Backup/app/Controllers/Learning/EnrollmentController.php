<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\EnrollmentModel;
use App\Services\ApprovalService;
use RuntimeException;

class EnrollmentController extends BaseController
{
    public function browse()
    {
        $classModel      = new ClassModel();
        $enrollmentModel = new EnrollmentModel();
        $userId          = session()->get('user_id');

        $classes = $classModel->withCourseTitle()
            ->whereIn('classes.status', ['scheduled', 'ongoing'])
            ->findAll();

        foreach ($classes as &$class) {
            $class['is_full']     = $classModel->isFull($class['id']);
            $class['is_enrolled'] = $enrollmentModel->alreadyEnrolled($userId, $class['id']);
        }
        unset($class);

        return view('learning/enrollments/browse', [
            'title'    => 'Daftar Kelas',
            'subTitle' => 'Daftar Kelas',
            'classes'  => $classes,
        ]);
    }

    public function store(int $classId)
    {
        $classModel      = new ClassModel();
        $enrollmentModel = new EnrollmentModel();
        $userId          = session()->get('user_id');

        $class = $classModel->find($classId);

        if (! $class) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }
        if ($enrollmentModel->alreadyEnrolled($userId, $classId)) {
            return redirect()->back()->with('error', 'Anda sudah terdaftar di kelas ini.');
        }
        if ($classModel->isFull($classId)) {
            return redirect()->back()->with('error', 'Kuota kelas ini sudah penuh.');
        }

        $enrollmentId = $enrollmentModel->insert([
            'user_id'           => $userId,
            'class_id'          => $classId,
            'enrollment_status' => 'pending',
            'final_status'      => 'in_progress',
        ], true);

        (new ApprovalService())->createApproval('enrollment', $enrollmentId, $userId);

        return redirect()->to(route_to('learning.enrollments.browse'))->with('message', 'Pendaftaran berhasil, menunggu persetujuan.');
    }

    public function pending()
    {
        return view('learning/enrollments/pending', [
            'title'       => 'Persetujuan Pendaftaran',
            'subTitle'    => 'Persetujuan Pendaftaran',
            'enrollments' => (new EnrollmentModel())->pendingList(),
        ]);
    }

    public function verify(int $enrollmentId)
    {
        $decision        = $this->request->getPost('decision');
        $enrollmentModel = new EnrollmentModel();
        $classModel      = new ClassModel();
        $actedBy         = session()->get('user_id');

        if (! in_array($decision, ['approve', 'reject'], true)) {
            return redirect()->back()->with('error', 'Keputusan tidak valid.');
        }

        $enrollment = $enrollmentModel->find($enrollmentId);

        if (! $enrollment) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan.');
        }

        if ($enrollment['enrollment_status'] !== 'pending') {
            return redirect()->back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        if ($decision === 'approve' && $classModel->isFull($enrollment['class_id'])) {
            return redirect()->back()->with('error', 'Kuota kelas sudah penuh, tidak bisa approve lagi.');
        }

        $service = new ApprovalService();

        try {
            if ($decision === 'approve') {
                $service->approve('enrollment', $enrollmentId, $actedBy, null, function (int $id) use ($enrollmentModel) {
                    $enrollmentModel->update($id, ['enrollment_status' => 'approved']);
                });
                $message = 'Pendaftaran disetujui.';
            } else {
                $service->reject('enrollment', $enrollmentId, $actedBy, null, function (int $id) use ($enrollmentModel) {
                    $enrollmentModel->update($id, ['enrollment_status' => 'rejected']);
                });
                $message = 'Pendaftaran ditolak.';
            }
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to(route_to('learning.enrollments.pending'))->with('message', $message);
    }
}

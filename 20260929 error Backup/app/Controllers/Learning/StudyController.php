<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\AssessmentModel;
use App\Models\AssessmentResultModel;
use App\Models\EnrollmentModel;
use App\Models\LessonModel;
use App\Models\ModuleModel;
use App\Models\ProgressModel;
use App\Services\ProgressService;

/**
 * Sisi PESERTA untuk "mengikuti pelatihan": melihat modul/materi kelas yang
 * sudah di-approve enrollment-nya, menandai materi selesai, dan melihat
 * status quiz. Terpisah dari ModuleController (sisi admin/trainer, CRUD
 * modul) supaya wewenang tidak tercampur.
 */
class StudyController extends BaseController
{
    private function ownedApprovedEnrollment(int $enrollmentId): ?array
    {
        $enrollment = (new EnrollmentModel())->find($enrollmentId);

        if (! $enrollment) {
            return null;
        }
        if ((int) $enrollment['user_id'] !== (int) session()->get('user_id')) {
            return null;
        }
        if ($enrollment['enrollment_status'] !== 'approved') {
            return null;
        }

        return $enrollment;
    }

    public function myClasses()
    {
        $enrollments = (new EnrollmentModel())->withClassDetail()
            ->where('enrollments.user_id', session()->get('user_id'))
            ->where('enrollments.enrollment_status', 'approved')
            ->findAll();

        return view('landing/user/arena', [
            'title'       => 'Kelas Saya',
            'subTitle'    => 'Kelas Saya',
            'enrollments' => $enrollments,
        ]);
    }

    public function modules(int $enrollmentId)
    {
        $enrollment = $this->ownedApprovedEnrollment($enrollmentId);

        if (! $enrollment) {
            return redirect()->to(route_to('learning.study.myClasses'))->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        $moduleModel     = new ModuleModel();
        $lessonModel     = new LessonModel();
        $assessmentModel = new AssessmentModel();
        $progressModel   = new ProgressModel();
        $resultModel     = new AssessmentResultModel();

        $completedLessonIds = $progressModel->completedLessonIds($enrollmentId);

        $modules = $moduleModel->forClass((int) $enrollment['class_id']);

        foreach ($modules as &$module) {
            $lessons = $lessonModel->forModule((int) $module['id']);
            foreach ($lessons as &$lesson) {
                $lesson['is_completed'] = in_array((int) $lesson['id'], $completedLessonIds, true);
            }
            unset($lesson);
            $module['lessons'] = $lessons;

            $quizzes = $assessmentModel->quizzesForModule((int) $module['id']);
            foreach ($quizzes as &$quiz) {
                $latest              = $resultModel->latestAttempt($enrollmentId, (int) $quiz['id']);
                $quiz['last_score']  = $latest['score'] ?? null;
                $quiz['is_passed']   = $latest['is_passed'] ?? null;
            }
            unset($quiz);
            $module['quizzes'] = $quizzes;

            $manualAssessments = $assessmentModel->forModule((int) $module['id']);
            $manualAssessments = array_values(array_filter(
                $manualAssessments,
                static fn(array $assessment): bool => in_array($assessment['type'], ['exam', 'assignment'], true)
            ));
            foreach ($manualAssessments as &$assessment) {
                $latest = $resultModel->latestManualResult($enrollmentId, (int) $assessment['id']);
                $assessment['last_score'] = $latest['score'] ?? null;
                $assessment['evaluation_status'] = $latest['evaluation_status'] ?? null;
                $assessment['is_passed'] = $latest['is_passed'] ?? null;
            }
            unset($assessment);
            $module['manual_assessments'] = $manualAssessments;
        }
        unset($module);

        return view('learning/study/modules', [
            'title'         => 'Materi Pelatihan',
            'subTitle'      => 'Materi Pelatihan',
            'enrollment'    => $enrollment,
            'modules'       => $modules,
            'enrollmentId'  => $enrollmentId,
        ]);
    }

    public function completeLesson(int $enrollmentId, int $lessonId)
    {
        $enrollment = $this->ownedApprovedEnrollment($enrollmentId);

        if (! $enrollment) {
            return redirect()->to(route_to('learning.study.myClasses'))->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        $lesson = (new LessonModel())->find($lessonId);
        $module = $lesson ? (new ModuleModel())->find((int) $lesson['module_id']) : null;

        if (! $lesson || ! $module || (int) $module['class_id'] !== (int) $enrollment['class_id']) {
            return redirect()->back()->with('error', 'Materi tidak ditemukan pada kelas ini.');
        }

        (new ProgressService())->markLessonComplete($enrollmentId, $lessonId, session()->get('user_id'));

        return redirect()->to(route_to('learning.study.modules', $enrollmentId))->with('message', 'Materi ditandai selesai.');
    }

    /**
     * File materi disimpan di WRITEPATH (bukan public/), jadi tidak bisa
     * diakses langsung lewat base_url(). Endpoint ini yang men-stream file,
     * dengan pengecekan akses sama seperti completeLesson() — peserta harus
     * enrollment approved miliknya sendiri, dan materi harus benar berada
     * di kelas yang sama.
     */
    public function downloadLesson(int $enrollmentId, int $lessonId)
    {
        $enrollment = $this->ownedApprovedEnrollment($enrollmentId);

        if (! $enrollment) {
            return redirect()->to(route_to('learning.study.myClasses'))->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        $lesson = (new LessonModel())->find($lessonId);
        $module = $lesson ? (new ModuleModel())->find((int) $lesson['module_id']) : null;

        if (! $lesson || ! $module || (int) $module['class_id'] !== (int) $enrollment['class_id'] || empty($lesson['file_path'])) {
            return redirect()->back()->with('error', 'File materi tidak ditemukan pada kelas ini.');
        }

        $fullPath = WRITEPATH . $lesson['file_path'];

        if (! is_file($fullPath)) {
            return redirect()->back()->with('error', 'File materi tidak ditemukan di server.');
        }

        return $this->response->download($fullPath, null)->setFileName(basename($fullPath));
    }
}

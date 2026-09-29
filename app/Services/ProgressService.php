<?php

namespace App\Services;

use App\Models\AssessmentModel;
use App\Models\AssessmentResultModel;
use App\Models\EnrollmentModel;
use App\Models\LessonModel;
use App\Models\ModuleModel;
use App\Models\ProgressModel;
use App\Services\CertificateService;
use RuntimeException;

/**
 * Menandai lesson selesai dan menghitung ulang final_status enrollment.
 *
 * ASUMSI (belum ada keputusan bisnis eksplisit, jadi didokumentasikan di
 * sini supaya mudah diubah):
 * - final_status = 'passed' HANYA jika seluruh lesson di seluruh modul
 *   kelas sudah completed DAN seluruh assessment bertipe 'quiz' di kelas
 *   itu percobaan TERBARUnya is_passed. Assessment tipe exam/assignment harus
 *   sudah berstatus evaluated oleh Evaluator.
 * - Jika kelas tidak punya lesson maupun quiz sama sekali, status TIDAK
 *   diubah otomatis (tidak ada yang bisa "diselesaikan").
 * - final_status dihitung ulang setiap kali (bisa turun dari 'passed' ke
 *   'in_progress' lagi kalau peserta mengulang assessment dan belum lulus). 'failed'
 *   tidak pernah di-set otomatis di tahap ini — belum ada aturan batas
 *   percobaan/tenggat yang menentukan kapan peserta dinyatakan gagal permanen.
 */
class ProgressService
{
    protected ProgressModel $progressModel;
    protected ModuleModel $moduleModel;
    protected LessonModel $lessonModel;
    protected AssessmentModel $assessmentModel;
    protected AssessmentResultModel $resultModel;
    protected EnrollmentModel $enrollmentModel;
    protected AuditService $auditService;

    public function __construct()
    {
        $this->progressModel   = new ProgressModel();
        $this->moduleModel     = new ModuleModel();
        $this->lessonModel     = new LessonModel();
        $this->assessmentModel = new AssessmentModel();
        $this->resultModel     = new AssessmentResultModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->auditService    = new AuditService();
    }

    public function markLessonComplete(int $enrollmentId, int $lessonId, ?int $actedBy): void
    {
        $this->progressModel->markComplete($enrollmentId, $lessonId);

        $this->auditService->log($actedBy, 'complete_lesson', 'lms', 'progress', $lessonId, [
            'enrollment_id' => $enrollmentId,
        ]);

        $this->recomputeFinalStatus($enrollmentId);
    }

    public function recomputeFinalStatus(int $enrollmentId): string
    {
        $enrollment = $this->enrollmentModel->find($enrollmentId);

        if (! $enrollment) {
            throw new RuntimeException('Enrollment tidak ditemukan.');
        }

        $modules = $this->moduleModel->forClass((int) $enrollment['class_id']);

        $allLessonIds = [];
        $allAssessments = [];

        foreach ($modules as $module) {
            foreach ($this->lessonModel->forModule((int) $module['id']) as $lesson) {
                $allLessonIds[] = (int) $lesson['id'];
            }
            foreach ($this->assessmentModel->forModule((int) $module['id']) as $assessment) {
                $allAssessments[] = $assessment;
            }
        }

        if (empty($allLessonIds) && empty($allAssessments)) {
            return $enrollment['final_status']; // tidak ada yang bisa dinilai, jangan ubah status
        }

        $completedLessonIds = $this->progressModel->completedLessonIds($enrollmentId);
        $lessonsDone         = empty(array_diff($allLessonIds, $completedLessonIds));

        $assessmentsPassed = true;
        foreach ($allAssessments as $assessment) {
            $latest = $this->resultModel->latestAttempt($enrollmentId, (int) $assessment['id']);

            if (! $latest || ! $latest['is_passed']) {
                $assessmentsPassed = false;
                break;
            }

            if (in_array($assessment['type'], ['exam', 'assignment'], true) && $latest['evaluation_status'] !== 'evaluated') {
                $assessmentsPassed = false;
                break;
            }
        }

        $newStatus = ($lessonsDone && $assessmentsPassed) ? 'passed' : 'in_progress';

        if ($newStatus !== $enrollment['final_status']) {
            $this->enrollmentModel->update($enrollmentId, ['final_status' => $newStatus]);

            if ($newStatus === 'passed') {
                (new CertificateService())->requestIfEligible($enrollmentId);
            }
        }

        return $newStatus;
    }
}

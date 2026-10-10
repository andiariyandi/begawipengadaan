<?php

namespace App\Services;

use App\Models\Learning\AssignmentModel;
use App\Models\Learning\AssignmentSubmissionModel;
use App\Models\Learning\AssignmentSubmissionFileModel;
use App\Models\Learning\ClassEvaluatorModel;
use App\Models\Learning\ParticipantFeedbackModel;
use CodeIgniter\Database\BaseConnection;
use RuntimeException;

class EvaluatorService
{
    protected BaseConnection $db;
    protected LearningService $learningService;
    protected AuditService $auditService;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db              = $db ?? db_connect();
        $this->learningService = new LearningService($this->db);
        $this->auditService    = new AuditService();
    }

    /**
     * Dapatkan ringkasan statistik kerja evaluator.
     */
    public function getSummary(int $evaluatorId): array
    {
        $assignedClasses = $this->getAssignedClasses($evaluatorId);
        $classIds = array_column($assignedClasses, 'class_id');

        $pendingCount = 0;
        $gradedCount  = 0;
        $avgScore     = 0.0;

        if (! empty($classIds)) {
            $pendingCount = $this->db->table('assignment_submissions s')
                ->join('assignments a', 'a.id = s.assignment_id')
                ->whereIn('a.class_id', $classIds)
                ->where('s.status', 'submitted')
                ->countAllResults();

            $gradedSubmissions = $this->db->table('assignment_submissions s')
                ->join('assignments a', 'a.id = s.assignment_id')
                ->whereIn('a.class_id', $classIds)
                ->where('s.status', 'graded')
                ->get()->getResultArray();

            $gradedCount = count($gradedSubmissions);
            if ($gradedCount > 0) {
                $totalScore = array_sum(array_column($gradedSubmissions, 'score'));
                $avgScore = round($totalScore / $gradedCount, 1);
            }
        }

        return [
            'classes'       => $assignedClasses,
            'pending_count' => $pendingCount,
            'graded_count'  => $gradedCount,
            'avg_score'     => $avgScore,
        ];
    }

    /**
     * Daftar tugas di seluruh kelas yang diampu evaluator.
     */
    public function getAssignments(int $evaluatorId): array
    {
        $assignedClasses = $this->getAssignedClasses($evaluatorId);
        $classIds = array_column($assignedClasses, 'class_id');

        if (empty($classIds)) {
            return [];
        }

        $assignments = $this->db->table('assignments a')
            ->select('a.*, lc.name AS class_name')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->whereIn('a.class_id', $classIds)
            ->orderBy('a.due_at', 'ASC')
            ->get()->getResultArray();

        foreach ($assignments as &$asg) {
            $asg['pending_count'] = $this->db->table('assignment_submissions')
                ->where(['assignment_id' => $asg['id'], 'status' => 'submitted'])
                ->countAllResults();
            $asg['graded_count'] = $this->db->table('assignment_submissions')
                ->where(['assignment_id' => $asg['id'], 'status' => 'graded'])
                ->countAllResults();
        }
        unset($asg);

        return $assignments;
    }

    /**
     * Daftar pengumpulan tugas pada satu kelas.
     */
    public function getClassSubmissions(int $classId, ?string $status = null): array
    {
        $builder = $this->db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title, a.class_id, u.name AS participant_name, u.email AS participant_email')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->where('a.class_id', $classId)
            ->orderBy('s.submitted_at', 'DESC');

        if ($status !== null && $status !== '') {
            $builder->where('s.status', $status);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Detail submission beserta berkas lampiran pendukung.
     */
    public function getSubmissionDetail(int $submissionId): ?array
    {
        $submission = $this->db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title, a.instructions AS assignment_instructions, a.class_id, u.name AS participant_name, u.email AS participant_email')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->where('s.id', $submissionId)
            ->get()->getRowArray();

        if (! $submission) {
            return null;
        }

        $files = $this->db->table('assignment_submission_files sf')
            ->select('sf.*, fa.original_name, fa.mime_type, fa.size_bytes')
            ->join('file_assets fa', 'fa.id = sf.file_id', 'left')
            ->where('sf.submission_id', $submissionId)
            ->get()->getResultArray();

        return [
            'submission' => $submission,
            'files'      => $files,
        ];
    }

    /**
     * Penilaian submission oleh Evaluator:
     * - Validasi score (0 - 100) dan status
     * - Mutasi status, score, feedback, graded_by, graded_at
     * - Catat audit trail evaluator
     * - Trigger sinkronisasi kelulusan belajar peserta (evaluateCompletion)
     */
    public function gradeSubmission(int $evaluatorId, int $submissionId, float $score, string $status = 'graded', ?string $feedback = null): array
    {
        if ($score < 0.00 || $score > 100.00) {
            throw new RuntimeException('Nilai harus berupa angka antara 0 hingga 100.');
        }

        $validStatuses = ['draft', 'submitted', 'in_review', 'revision_required', 'graded', 'cancelled'];
        if (! in_array($status, $validStatuses, true)) {
            $status = 'graded';
        }

        $submission = $this->db->table('assignment_submissions')->where('id', $submissionId)->get()->getRowArray();
        if (! $submission) {
            throw new RuntimeException('Submission tugas tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('assignment_submissions')->where('id', $submissionId)->update([
            'score'      => $score,
            'status'     => $status,
            'feedback'   => $feedback,
            'graded_at'  => $now,
            'graded_by'  => $evaluatorId,
            'updated_at' => $now,
        ]);

        // Audit aktivitas evaluator
        $this->auditService->log(
            $evaluatorId,
            'assignment.graded',
            'learning',
            'assignment_submission',
            $submissionId,
            [
                'score'         => $score,
                'status'        => $status,
                'enrollment_id' => $submission['enrollment_id'],
            ]
        );

        // Memicu sinkronisasi kelulusan peserta jika penilaian selesai
        $isCompleted = false;
        if ($status === 'graded') {
            $isCompleted = $this->learningService->evaluateCompletion((int) $submission['enrollment_id']);
        }

        // Kirim notifikasi internal ke peserta
        try {
            $enrollmentRow = $this->db->table('enrollments')->where('id', $submission['enrollment_id'])->get()->getRowArray();
            $assignmentRow = $this->db->table('assignments')->where('id', $submission['assignment_id'])->get()->getRowArray();
            if ($enrollmentRow && ! empty($enrollmentRow['user_id'])) {
                (new NotificationService($this->db))->send(
                    (int) $enrollmentRow['user_id'],
                    'assignment_graded',
                    'Tugas Telah Dinilai',
                    "Tugas '" . ($assignmentRow['title'] ?? 'Mandiri') . "' telah dinilai oleh Evaluator dengan skor {$score}." . ($feedback ? " Catatan: {$feedback}" : '')
                );
            }
        } catch (\Throwable $e) {
            log_message('warning', 'Failed to send notification for graded assignment: ' . $e->getMessage());
        }

        return [
            'submission_id' => $submissionId,
            'score'         => $score,
            'status'        => $status,
            'is_completed'  => $isCompleted,
        ];
    }

    /**
     * Rekap nilai seluruh peserta dalam kelas.
     */
    public function getClassGradingSheet(int $classId): array
    {
        return $this->db->table('enrollments e')
            ->select('e.*, u.name AS user_name, u.email AS user_email')
            ->join('users u', 'u.id = e.user_id')
            ->where('e.class_id', $classId)
            ->orderBy('e.final_score', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Ulasan dan feedback yang diberikan peserta untuk kelas.
     */
    public function getClassFeedbacks(int $classId): array
    {
        return $this->db->table('feedbacks f')
            ->select('f.*, u.name AS user_name')
            ->join('enrollments e', 'e.id = f.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->where('e.class_id', $classId)
            ->where('f.status', 'submitted')
            ->orderBy('f.submitted_at', 'DESC')
            ->get()->getResultArray();
    }

    public function getAssignedClasses(int $evaluatorId): array
    {
        $classes = $this->db->table('class_evaluators ce')
            ->select('ce.class_id, lc.name AS class_name, lc.code AS class_code, lc.status AS class_status')
            ->join('learning_classes lc', 'lc.id = ce.class_id')
            ->where(['ce.user_id' => $evaluatorId, 'ce.status' => 'active'])
            ->get()->getResultArray();

        // Fallback jika belum di-assign spesifik: izinkan akses kelas published
        if (empty($classes)) {
            $classes = $this->db->table('learning_classes lc')
                ->select('lc.id AS class_id, lc.name AS class_name, lc.code AS class_code, lc.status AS class_status')
                ->where('lc.status', 'published')
                ->get()->getResultArray();
        }

        return $classes;
    }
}

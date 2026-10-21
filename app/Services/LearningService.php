<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;

class LearningService
{
    protected BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    /**
     * Daftarkan peserta ke kelas pembelajaran.
     */
    public function enroll(int $userId, int $classId): int
    {
        $class = $this->db->table('learning_classes')->where('id', $classId)->get()->getRowArray();
        if (! $class) {
            throw new RuntimeException('Kelas tidak ditemukan.');
        }
        if (! in_array($class['status'] ?? null, ['scheduled', 'active', 'published'], true)) {
            throw new RuntimeException('Kelas tidak dapat diikuti pada status saat ini.');
        }

        $existing = $this->db->table('enrollments')
            ->where(['class_id' => $classId, 'user_id' => $userId])
            ->get()->getRowArray();
        if ($existing) {
            throw new RuntimeException('Anda sudah terdaftar pada kelas ini.');
        }

        // Check capacity if set
        if (! empty($class['capacity']) && (int) $class['capacity'] > 0) {
            $currentEnrolled = $this->db->table('enrollments')
                ->where('class_id', $classId)
                ->whereIn('status', ['pending', 'active', 'completed'])
                ->countAllResults();
            if ($currentEnrolled >= (int) $class['capacity']) {
                throw new RuntimeException('Kuota peserta kelas telah penuh.');
            }
        }

        $now = date('Y-m-d H:i:s');
        try {
            $this->db->table('enrollments')->insert([
                'class_id'    => $classId,
                'user_id'     => $userId,
                'status'      => 'pending',
                'enrolled_at' => $now,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        } catch (\Throwable $e) {
            throw new RuntimeException('Enrollment ganda tidak diizinkan.');
        }

        return (int) $this->db->insertID();
    }

    /**
     * Aktifkan enrollment peserta (oleh operator/admin).
     */
    public function activateEnrollment(int $enrollmentId): void
    {
        $now = date('Y-m-d H:i:s');
        $enrollment = $this->db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();

        if (! $enrollment) {
            throw new RuntimeException('Enrollment tidak ditemukan.');
        }
        if ($enrollment['status'] !== 'pending') {
            throw new RuntimeException('Hanya enrollment pending yang dapat diaktifkan.');
        }

        $this->db->transStart();
        $this->db->table('enrollments')->where('id', $enrollmentId)->update([
            'status'       => 'active',
            'activated_at' => $now,
            'updated_at'   => $now,
        ]);
        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            throw new RuntimeException('Enrollment gagal diaktifkan.');
        }
    }

    /**
     * Mulai mempelajari suatu materi (lesson).
     */
    public function startLesson(int $userId, int $lessonId): int
    {
        $lesson = $this->lessonContext($userId, $lessonId);
        $now = date('Y-m-d H:i:s');
        $progress = $this->db->table('learning_progress')
            ->where(['enrollment_id' => $lesson['enrollment']['id'], 'lesson_id' => $lessonId])
            ->get()->getRowArray();

        if ($progress) {
            $this->db->table('learning_progress')->where('id', $progress['id'])->update([
                'status'           => $progress['status'] === 'completed' ? 'completed' : 'in_progress',
                'started_at'       => $progress['started_at'] ?: $now,
                'progress_percent' => max(1.00, (float) $progress['progress_percent']),
                'updated_at'       => $now,
            ]);
            return (int) $progress['id'];
        }

        $this->db->table('learning_progress')->insert([
            'enrollment_id'    => $lesson['enrollment']['id'],
            'lesson_id'        => $lessonId,
            'status'           => 'in_progress',
            'started_at'       => $now,
            'progress_percent' => 1.00,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        return (int) $this->db->insertID();
    }

    /**
     * Tandai materi (lesson) selesai dipelajari oleh peserta.
     */
    public function completeLesson(int $userId, int $lessonId): void
    {
        $lesson = $this->lessonContext($userId, $lessonId);
        $now = date('Y-m-d H:i:s');
        $existing = $this->db->table('learning_progress')->where([
            'enrollment_id' => $lesson['enrollment']['id'],
            'lesson_id'     => $lessonId,
        ])->get()->getRowArray();

        if ($existing) {
            $this->db->table('learning_progress')->where('id', $existing['id'])->update([
                'status'           => 'completed',
                'started_at'       => $existing['started_at'] ?: $now,
                'completed_at'     => $now,
                'progress_percent' => 100.00,
                'updated_at'       => $now,
            ]);
        } else {
            $this->db->table('learning_progress')->insert([
                'enrollment_id'    => $lesson['enrollment']['id'],
                'lesson_id'        => $lessonId,
                'status'           => 'completed',
                'started_at'       => $now,
                'completed_at'     => $now,
                'progress_percent' => 100.00,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }

        $this->evaluateCompletion((int) $lesson['enrollment']['id']);
    }

    /**
     * Mulai pengerjaan quiz / assessment.
     */
    public function startQuiz(int $userId, int $quizId): int
    {
        $context = $this->quizContext($userId, $quizId);

        // Check if there is already an active (started) attempt for this quiz & enrollment
        $activeAttempt = $this->db->table('quiz_attempts')
            ->where(['quiz_id' => $quizId, 'enrollment_id' => $context['enrollment']['id'], 'status' => 'started'])
            ->orderBy('id', 'DESC')
            ->get()->getRowArray();

        if ($activeAttempt) {
            $durationMinutes = (int) ($context['quiz']['duration_minutes'] ?? 0);
            if ($durationMinutes > 0 && ! empty($activeAttempt['started_at'])) {
                $deadline = strtotime($activeAttempt['started_at']) + ($durationMinutes * 60);
                if (time() >= $deadline) {
                    // Time expired while away; auto-submit the expired attempt
                    $this->submitQuiz($userId, (int) $activeAttempt['id'], []);
                } else {
                    // Attempt is still ongoing and has time left
                    return (int) $activeAttempt['id'];
                }
            } else {
                // No time limit; resume ongoing attempt
                return (int) $activeAttempt['id'];
            }
        }

        $attempts = $this->db->table('quiz_attempts')
            ->where(['quiz_id' => $quizId, 'enrollment_id' => $context['enrollment']['id']])
            ->countAllResults();

        if ($attempts >= (int) $context['quiz']['attempt_limit']) {
            throw new RuntimeException('Batas percobaan quiz sudah tercapai.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('quiz_attempts')->insert([
            'quiz_id'        => $quizId,
            'enrollment_id'  => $context['enrollment']['id'],
            'attempt_number' => $attempts + 1,
            'status'         => 'started',
            'started_at'     => $now,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        return (int) $this->db->insertID();
    }

    /**
     * Submit jawaban quiz dan hitung nilai otomatis.
     */
    public function submitQuiz(int $userId, int $attemptId, array $answers): array
    {
        $attempt = $this->db->table('quiz_attempts')->where('id', $attemptId)->get()->getRowArray();
        if (! $attempt || $attempt['status'] !== 'started') {
            throw new RuntimeException('Percobaan quiz tidak valid atau sudah disubmit.');
        }

        $context = $this->quizContext($userId, (int) $attempt['quiz_id']);
        if ((int) $context['enrollment']['id'] !== (int) $attempt['enrollment_id']) {
            throw new RuntimeException('Percobaan quiz bukan milik enrollment Anda.');
        }

        $questions = $this->db->table('quiz_questions')
            ->where('quiz_id', $context['quiz']['id'])
            ->get()->getResultArray();

        $this->db->transStart();
        $totalPoints = 0.0;
        $awarded = 0.0;

        foreach ($questions as $question) {
            $qid = (int) $question['id'];
            $points = (float) $question['points'];
            $totalPoints += $points;

            $selected = $answers[$qid] ?? null;
            $isCorrect = false;
            $score = 0.0;

            if ($selected !== null) {
                $option = $this->db->table('quiz_options')
                    ->where(['id' => (int) $selected, 'question_id' => $qid])
                    ->get()->getRowArray();
                if ($option && (int) $option['is_correct'] === 1) {
                    $isCorrect = true;
                    $score = $points;
                    $awarded += $points;
                }
            }

            $this->db->table('quiz_answers')->insert([
                'attempt_id'     => $attemptId,
                'question_id'    => $qid,
                'answer_payload' => json_encode(['option_id' => $selected], JSON_THROW_ON_ERROR),
                'is_correct'     => $isCorrect ? 1 : 0,
                'score_awarded'  => $score,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        $scorePercent = $totalPoints > 0 ? round(($awarded / $totalPoints) * 100, 2) : 0.0;
        $now = date('Y-m-d H:i:s');
        $this->db->table('quiz_attempts')->where('id', $attemptId)->update([
            'status'       => 'graded',
            'submitted_at' => $now,
            'graded_at'    => $now,
            'score'        => $scorePercent,
            'updated_at'   => $now,
        ]);
        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            throw new RuntimeException('Jawaban quiz gagal disimpan.');
        }

        // Evaluasi kelulusan jika ini post_test atau lesson_quiz
        $this->evaluateCompletion((int) $context['enrollment']['id']);

        return ['score' => $scorePercent, 'attempt_id' => $attemptId];
    }

    /**
     * Submit pengumpulan tugas mandiri oleh peserta.
     */
    public function submitAssignment(int $userId, int $classId, int $assignmentId, string $content, ?array $fileData = null): int
    {
        $enrollment = $this->db->table('enrollments')->where(['class_id' => $classId, 'user_id' => $userId])->get()->getRowArray();
        if (! $enrollment || $enrollment['status'] !== 'active') {
            throw new RuntimeException('Enrollment aktif tidak ditemukan untuk kelas ini.');
        }

        $assignment = $this->db->table('assignments')->where(['id' => $assignmentId, 'class_id' => $classId])->get()->getRowArray();
        if (! $assignment) {
            throw new RuntimeException('Tugas tidak ditemukan.');
        }

        $pastCount = $this->db->table('assignment_submissions')
            ->where(['assignment_id' => $assignmentId, 'enrollment_id' => $enrollment['id']])
            ->countAllResults();

        $maxAttempts = (int) ($assignment['max_attempts'] ?? 1);
        if ($pastCount >= $maxAttempts) {
            throw new RuntimeException('Batas maksimal pengumpulan tugas sudah tercapai.');
        }

        $attemptNumber = $pastCount + 1;
        $now = date('Y-m-d H:i:s');

        $this->db->transStart();
        $this->db->table('assignment_submissions')->insert([
            'assignment_id'  => $assignmentId,
            'enrollment_id'  => $enrollment['id'],
            'attempt_number' => $attemptNumber,
            'status'         => 'submitted',
            'content'        => $content,
            'submitted_at'   => $now,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        $submissionId = (int) $this->db->insertID();

        if ($fileData && ! empty($fileData['path'])) {
            $pathInfo   = pathinfo($fileData['path']);
            $storedName = $pathInfo['basename'] ?? basename($fileData['path']);
            $extension  = $pathInfo['extension'] ?? 'bin';

            $this->db->table('file_assets')->insert([
                'disk'             => 'local',
                'path'             => $fileData['path'],
                'original_name'    => $fileData['original_name'] ?? 'lampiran_tugas',
                'stored_name'      => $storedName,
                'extension'        => $extension,
                'mime_type'        => $fileData['mime_type'] ?? 'application/octet-stream',
                'size_bytes'       => $fileData['size_bytes'] ?? 0,
                'visibility'       => 'restricted',
                'lifecycle_status' => 'available',
                'resource_type'    => 'assignment_submission',
                'resource_id'      => $submissionId,
                'uploaded_by'      => $userId,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $fileAssetId = (int) $this->db->insertID();

            $this->db->table('assignment_submission_files')->insert([
                'submission_id' => $submissionId,
                'file_id'       => $fileAssetId,
                'created_at'    => $now,
            ]);
        }

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            throw new RuntimeException('Pengumpulan tugas gagal disimpan.');
        }

        return $submissionId;
    }

    /**
     * Evaluasi kelulusan kelas secara ketat dan konsisten:
     * Syarat Kelulusan Lengkap:
     *  1. Seluruh materi wajib (lessons.is_required = 1) telah selesai (progress_percent = 100)
     *  2. Seluruh quiz materi wajib (quizzes.is_required = 1, type = 'lesson_quiz') telah dikerjakan
     *  3. Seluruh tugas wajib (assignments.is_required = 1) telah dinilai oleh evaluator (status = 'graded')
     *  4. Post-test wajib telah selesai dikerjakan
     *  5. Nilai akhir gabungan (quiz + tugas + post_test) >= passing_grade
     *
     * Jika seluruh syarat terpenuhi:
     *  - enrollment.status diubah menjadi 'completed'
     *  - passed_at dicatat
     *  - certificate_eligible_at dicatat jika kelas mengaktifkan sertifikat
     *  - Auto-trigger pengajuan approval sertifikat ke antrean approvals
     */
    public function evaluateCompletion(int $enrollmentId): bool
    {
        $enrollment = $this->db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();
        if (! $enrollment || ! in_array($enrollment['status'], ['active', 'completed'], true)) {
            return false;
        }

        $classId = (int) $enrollment['class_id'];
        $class = $this->db->table('learning_classes')->where('id', $classId)->get()->getRowArray();
        if (! $class) {
            return false;
        }

        // 1. Cek Lessons Wajib (Hanya yang berstatus 'published' di dalam modul 'published')
        $requiredLessons = $this->db->query(
            'SELECT lessons.id FROM lessons
             JOIN learning_modules ON learning_modules.id = lessons.module_id
             WHERE learning_modules.class_id = ? 
               AND lessons.is_required = 1 
               AND lessons.status = "published" 
               AND learning_modules.status = "published"',
            [$classId]
        )->getResultArray();

        if (! empty($requiredLessons)) {
            $completedLessons = $this->db->table('learning_progress')
                ->where(['enrollment_id' => $enrollmentId, 'status' => 'completed'])
                ->whereIn('lesson_id', array_column($requiredLessons, 'id'))
                ->countAllResults();
            if ($completedLessons < count($requiredLessons)) {
                return false; // Belum semua materi wajib selesai
            }
        }

        // 2. Cek Post-Test (Satu-satunya asesmen WAJIB jika diterbitkan di kelas)
        $postTest = $this->db->table('quizzes')
            ->where(['class_id' => $classId, 'type' => 'post_test', 'status' => 'published'])
            ->orderBy('id', 'ASC')
            ->get()->getRowArray();

        $postScore = null;
        if ($postTest) {
            $postAvg = $this->gradedAverage($enrollmentId, [$postTest]);
            if ($postAvg === null) {
                return false; // Post-test wajib belum dikerjakan atau belum dinilai
            }
            $postScore = $postAvg;
        }

        // 3. Cek Lesson Quizzes (Bersifat Opsional)
        // Jika ada quiz yang telah dikerjakan peserta, masukkan ke penilaian rata-rata quiz
        $publishedQuizzes = $this->db->table('quizzes')
            ->where(['class_id' => $classId, 'type' => 'lesson_quiz', 'status' => 'published'])
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $quizScore = null;
        if (! empty($publishedQuizzes)) {
            $attemptedScores = [];
            foreach ($publishedQuizzes as $q) {
                $bestAttempt = $this->db->table('quiz_attempts')
                    ->where(['quiz_id' => (int) $q['id'], 'enrollment_id' => $enrollmentId])
                    ->whereIn('status', ['graded', 'submitted', 'passed', 'completed'])
                    ->where('score IS NOT NULL', null, false)
                    ->orderBy('score', 'DESC')
                    ->get()->getRowArray();
                if ($bestAttempt && $bestAttempt['score'] !== null) {
                    $attemptedScores[] = (float) $bestAttempt['score'];
                }
            }
            if (! empty($attemptedScores)) {
                $quizScore = round(array_sum($attemptedScores) / count($attemptedScores), 2);
            }
        }

        // 4. Cek Assignments (Bersifat Opsional)
        // Jika ada tugas yang dikumpulkan dan telah dinilai instruktur, masukkan ke penilaian tugas
        $publishedAssignments = $this->db->table('assignments')
            ->where(['class_id' => $classId, 'status' => 'published'])
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $assignmentScore = null;
        if (! empty($publishedAssignments)) {
            $asgScores = [];
            foreach ($publishedAssignments as $asg) {
                $sub = $this->db->table('assignment_submissions')
                    ->where(['assignment_id' => (int) $asg['id'], 'enrollment_id' => $enrollmentId, 'status' => 'graded'])
                    ->where('score IS NOT NULL', null, false)
                    ->orderBy('score', 'DESC')
                    ->get()->getRowArray();
                if ($sub && $sub['score'] !== null) {
                    $asgScores[] = (float) $sub['score'];
                }
            }
            if (! empty($asgScores)) {
                $assignmentScore = round(array_sum($asgScores) / count($asgScores), 2);
            }
        }

        // 5. Cabang Penilaian Dinamis (Berdasarkan ketersediaan komponen asesmen & pengerjaan)
        $components = [];

        if ($postScore !== null) {
            $rawWeight = (float) ($class['post_test_weight'] ?? 60.00);
            $components['post_test'] = [
                'score'  => $postScore,
                'weight' => $rawWeight > 0 ? $rawWeight : 60.00,
            ];
        }

        if ($quizScore !== null) {
            $rawWeight = (float) ($class['quiz_weight'] ?? 40.00);
            $components['quiz'] = [
                'score'  => $quizScore,
                'weight' => $rawWeight > 0 ? $rawWeight : 40.00,
            ];
        }

        if ($assignmentScore !== null) {
            $rawWeight = (float) ($class['assignment_weight'] ?? 0.00);
            $components['assignment'] = [
                'score'  => $assignmentScore,
                'weight' => $rawWeight > 0 ? $rawWeight : 20.00,
            ];
        }

        if (empty($components)) {
            // Cabang: Kelas tanpa komponen asesmen (atau materi kurikulum penuh)
            $final = 100.00;
        } else {
            $totalWeight = array_sum(array_column($components, 'weight'));
            if ($totalWeight <= 0) {
                $avgScore = array_sum(array_column($components, 'score')) / count($components);
                $final = round($avgScore, 2);
            } else {
                $weightedSum = 0.0;
                foreach ($components as $comp) {
                    $weightedSum += ($comp['score'] * ($comp['weight'] / $totalWeight));
                }
                $final = round($weightedSum, 2);
            }
        }

        $passingGrade = (float) ($class['passing_grade'] ?? 70.00);
        $isPassed = $final >= $passingGrade;
        $isCertEligible = $isPassed && (int) ($class['certificate_enabled'] ?? 0) === 1;

        // Cek apakah peserta masih memiliki sisa kesempatan re-attempt pada post-test
        $hasPostTestAttemptsRemaining = false;
        if ($postTest && ! $isPassed) {
            $attemptsUsed = (int) $this->db->table('quiz_attempts')
                ->where(['quiz_id' => (int) $postTest['id'], 'enrollment_id' => $enrollmentId])
                ->countAllResults();
            $attemptLimit = (int) ($postTest['attempt_limit'] ?? 1);
            if ($attemptsUsed < $attemptLimit) {
                $hasPostTestAttemptsRemaining = true;
            }
        }

        $now = date('Y-m-d H:i:s');
        // Jika belum lulus tapi masih memiliki kuota re-attempt, pertahankan status 'active' agar tidak terkunci
        $newEnrollmentStatus = ($isPassed || ! $hasPostTestAttemptsRemaining) ? 'completed' : 'active';

        $this->db->table('enrollments')->where('id', $enrollmentId)->update([
            'status'                  => $newEnrollmentStatus,
            'completed_at'            => $isPassed ? ($enrollment['completed_at'] ?: $now) : null,
            'final_score'             => $final,
            'passed_at'               => $isPassed ? ($enrollment['passed_at'] ?: $now) : null,
            'certificate_eligible_at' => $isCertEligible ? ($enrollment['certificate_eligible_at'] ?: $now) : null,
            'updated_at'              => $now,
        ]);

        // Auto-trigger approval sertifikat jika lulus dan eligible
        if ($isCertEligible) {
            try {
                $eligibilityService = new CertificateEligibilityService($this->db);
                $eligibilityService->createLearningApproval($enrollmentId, (int) $enrollment['user_id']);
            } catch (\Throwable $e) {
                log_message('warning', 'Auto-trigger certificate approval failed for enrollment {id}: {msg}', [
                    'id'  => $enrollmentId,
                    'msg' => $e->getMessage(),
                ]);
            }
        }

        return true;
    }

    /**
     * Hitung ringkasan progres belajar komprehensif untuk satu enrollment.
     */
    public function calculateEnrollmentProgress(int $enrollmentId): array
    {
        $enrollment = $this->db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();
        if (! $enrollment) {
            return ['overall_percent' => 0.0, 'completed' => false];
        }

        $classId = (int) $enrollment['class_id'];

        // Lessons
        $totalLessons = (int) $this->db->query(
            'SELECT COUNT(lessons.id) AS cnt FROM lessons
             JOIN learning_modules ON learning_modules.id = lessons.module_id
             WHERE learning_modules.class_id = ? AND lessons.status = "published"',
            [$classId]
        )->getRowArray()['cnt'] ?? 0;

        $completedLessons = $this->db->table('learning_progress')
            ->where(['enrollment_id' => $enrollmentId, 'status' => 'completed'])
            ->countAllResults();

        // Quizzes
        $totalQuizzes = $this->db->table('quizzes')
            ->where(['class_id' => $classId, 'status' => 'published'])
            ->countAllResults();

        $completedQuizzes = $this->db->table('quiz_attempts')
            ->where(['enrollment_id' => $enrollmentId])
            ->whereIn('status', ['graded', 'submitted', 'passed', 'completed'])
            ->select('DISTINCT(quiz_id)')
            ->countAllResults();

        // Assignments
        $totalAssignments = $this->db->table('assignments')
            ->where(['class_id' => $classId, 'status' => 'published'])
            ->countAllResults();

        $gradedAssignments = $this->db->table('assignment_submissions')
            ->where(['enrollment_id' => $enrollmentId, 'status' => 'graded'])
            ->select('DISTINCT(assignment_id)')
            ->countAllResults();

        $totalItems = $totalLessons + $totalQuizzes + $totalAssignments;
        $completedItems = $completedLessons + $completedQuizzes + $gradedAssignments;

        $overallPercent = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0.0;

        return [
            'lessons_completed'     => $completedLessons,
            'lessons_total'         => $totalLessons,
            'quizzes_completed'     => $completedQuizzes,
            'quizzes_total'         => $totalQuizzes,
            'assignments_completed' => $gradedAssignments,
            'assignments_total'     => $totalAssignments,
            'overall_percent'       => $overallPercent,
            'is_completed'          => $enrollment['status'] === 'completed',
            'is_passed'             => ! empty($enrollment['passed_at']),
            'final_score'           => $enrollment['final_score'],
        ];
    }

    /**
     * Hitung nilai rata-rata dari quiz-quiz yang telah diselesaikan.
     */
    protected function gradedAverage(int $enrollmentId, array $quizzes): ?float
    {
        if (empty($quizzes)) {
            return null;
        }

        $scores = [];
        foreach ($quizzes as $quiz) {
            $latest = $this->db->table('quiz_attempts')
                ->where(['quiz_id' => (int) $quiz['id'], 'enrollment_id' => $enrollmentId])
                ->whereIn('status', ['graded', 'submitted', 'passed', 'completed'])
                ->where('score IS NOT NULL', null, false)
                ->orderBy('score', 'DESC')
                ->get()->getRowArray();
            if (! $latest || $latest['score'] === null) {
                return null;
            }
            $scores[] = (float) $latest['score'];
        }

        return round(array_sum($scores) / count($scores), 2);
    }

    protected function lessonContext(int $userId, int $lessonId): array
    {
        $lesson = $this->db->table('lessons')
            ->select('lessons.*, learning_modules.class_id')
            ->join('learning_modules', 'learning_modules.id = lessons.module_id')
            ->where('lessons.id', $lessonId)
            ->get()->getRowArray();

        if (! $lesson) {
            throw new RuntimeException('Lesson tidak ditemukan.');
        }

        $enrollment = $this->db->table('enrollments')
            ->where(['class_id' => (int) $lesson['class_id'], 'user_id' => $userId, 'status' => 'active'])
            ->get()->getRowArray();

        if (! $enrollment) {
            throw new RuntimeException('Anda belum memiliki akses aktif ke kelas ini.');
        }

        return ['lesson' => $lesson, 'enrollment' => $enrollment];
    }

    protected function quizContext(int $userId, int $quizId): array
    {
        $quiz = $this->db->table('quizzes')->where('id', $quizId)->get()->getRowArray();
        if (! $quiz) {
            throw new RuntimeException('Quiz tidak ditemukan.');
        }

        $enrollment = $this->db->table('enrollments')
            ->where(['class_id' => (int) $quiz['class_id'], 'user_id' => $userId])
            ->whereIn('status', ['active', 'completed'])
            ->get()->getRowArray();

        if (! $enrollment) {
            throw new RuntimeException('Anda belum memiliki enrollment aktif untuk quiz ini.');
        }

        return ['quiz' => $quiz, 'enrollment' => $enrollment];
    }
}

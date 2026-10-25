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

        // M-3: Penegakan batas waktu jendela pendaftaran kelas
        $nowTime = time();
        $regOpenAt = ! empty($class['registration_open_at']) ? strtotime((string) $class['registration_open_at']) : null;
        $regCloseAt = ! empty($class['registration_close_at']) ? strtotime((string) $class['registration_close_at']) : null;

        if ($regOpenAt && $nowTime < $regOpenAt) {
            throw new RuntimeException('Pendaftaran kelas belum dibuka.');
        }
        if ($regCloseAt && $nowTime > $regCloseAt) {
            throw new RuntimeException('Pendaftaran kelas telah ditutup.');
        }

        // M-2: Transaksi atomik untuk mencegah TOCTOU kuota enrollment
        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        $existing = $this->db->table('enrollments')
            ->where(['class_id' => $classId, 'user_id' => $userId])
            ->get()->getRowArray();
        if ($existing) {
            $this->db->transRollback();
            throw new RuntimeException('Anda sudah terdaftar pada kelas ini.');
        }

        // Check capacity if set
        if (! empty($class['capacity']) && (int) $class['capacity'] > 0) {
            $currentEnrolled = $this->db->table('enrollments')
                ->where('class_id', $classId)
                ->whereIn('status', ['pending', 'active', 'completed'])
                ->countAllResults();
            if ($currentEnrolled >= (int) $class['capacity']) {
                $this->db->transRollback();
                throw new RuntimeException('Kuota peserta kelas telah penuh.');
            }
        }

        $this->db->table('enrollments')->insert([
            'class_id'    => $classId,
            'user_id'     => $userId,
            'status'      => 'pending',
            'enrolled_at' => $now,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $newId = (int) $this->db->insertID();
        $this->db->transComplete();

        if (! $this->db->transStatus() || $newId < 1) {
            throw new RuntimeException('Enrollment ganda tidak diizinkan atau gagal diproses.');
        }

        // Shared notification ke Trainer & Operator pool kelas
        try {
            $userRow = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            $userName = $userRow['name'] ?? 'Peserta';
            $className = $class['name'] ?? 'Kelas Pembelajaran';
            (new NotificationService($this->db))->sendShared(
                'shared:class:' . $classId,
                'class_enrollment_requested',
                'Permohonan Pendaftaran Kelas Baru',
                "{$userName} telah mengajukan pendaftaran pada '{$className}'.",
                [
                    'class_id'      => $classId,
                    'enrollment_id' => $newId,
                    'user_id'       => $userId,
                    'url'           => route_to('workspace.operator.enrollments'),
                ],
                $userId
            );
        } catch (\Throwable) {
        }

        return $newId;
    }

    /**
     * Aktifkan enrollment peserta (oleh operator/administrator).
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

        // Kirim personal notifikasi ke peserta & tandai notifikasi permohonan bersama sebagai terbaca
        try {
            $class = $this->db->table('learning_classes')->where('id', $enrollment['class_id'])->get()->getRowArray();
            $className = $class['name'] ?? 'Kelas Pembelajaran';
            (new NotificationService($this->db))->send(
                (int) $enrollment['user_id'],
                'class_enrolled',
                'Pendaftaran Kelas Disetujui',
                "Selamat! Pendaftaran Anda di '{$className}' telah aktif. Silakan masuk dan mulai pembelajaran.",
                [
                    'class_id'      => (int) $enrollment['class_id'],
                    'enrollment_id' => $enrollmentId,
                    'url'           => site_url('workspace/peserta/kelas-saya'),
                ]
            );

            $this->db->table('notifications')
                ->where('channel', 'shared:class:' . $enrollment['class_id'])
                ->where('type', 'class_enrollment_requested')
                ->where('user_id', (int) $enrollment['user_id'])
                ->where('read_at IS NULL')
                ->update(['read_at' => $now]);
        } catch (\Throwable) {
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

        $this->db->transStart();
        try {
            $attempts = $this->db->table('quiz_attempts')
                ->where(['quiz_id' => $quizId, 'enrollment_id' => $context['enrollment']['id']])
                ->countAllResults();

            if ($attempts >= (int) $context['quiz']['attempt_limit']) {
                $this->db->transRollback();
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

            $newAttemptId = (int) $this->db->insertID();
            $this->db->transComplete();

            if (! $this->db->transStatus() || $newAttemptId < 1) {
                $concurrent = $this->db->table('quiz_attempts')
                    ->where(['quiz_id' => $quizId, 'enrollment_id' => $context['enrollment']['id'], 'status' => 'started'])
                    ->orderBy('id', 'DESC')
                    ->get()->getRowArray();
                if ($concurrent) {
                    return (int) $concurrent['id'];
                }
                throw new RuntimeException('Gagal memulai sesi pengerjaan kuis.');
            }

            // Forensic audit log (M-10)
            try {
                $req = service('request');
                $ip = ($req instanceof \CodeIgniter\HTTP\IncomingRequest) ? $req->getIPAddress() : '127.0.0.1';
                $userAgent = ($req instanceof \CodeIgniter\HTTP\IncomingRequest) ? mb_substr((string) $req->getUserAgent(), 0, 255) : 'CLI/BegawiPengadaan';
                $this->db->table('audit_logs')->insert([
                    'actor_id'        => $userId,
                    'action'          => 'learning.quiz.start',
                    'resource_type'   => 'quiz_attempt',
                    'resource_id'     => (string) $newAttemptId,
                    'result'          => 'success',
                    'before_snapshot' => null,
                    'after_snapshot'  => json_encode([
                        'quiz_id'          => $quizId,
                        'attempt_id'       => $newAttemptId,
                        'enrollment_id'    => (int) $context['enrollment']['id'],
                        'attempt_number'   => $attempts + 1,
                        'score'            => null,
                        'duration_seconds' => null,
                        'started_at'       => $now,
                    ], JSON_UNESCAPED_UNICODE),
                    'reason'          => 'Forensik pengerjaan kuis peserta',
                    'ip_hash'         => hash('sha256', (string) $ip),
                    'user_agent_hash' => hash('sha256', (string) $userAgent),
                    'request_id'      => bin2hex(random_bytes(8)),
                    'created_at'      => $now,
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'Quiz forensic audit failed: {message}', ['message' => $e->getMessage()]);
            }

            return $newAttemptId;
        } catch (\Throwable $e) {
            $this->db->transRollback();
            $active = $this->db->table('quiz_attempts')
                ->where(['quiz_id' => $quizId, 'enrollment_id' => $context['enrollment']['id'], 'status' => 'started'])
                ->orderBy('id', 'DESC')
                ->get()->getRowArray();
            if ($active) {
                return (int) $active['id'];
            }
            throw $e;
        }
    }

    /**
     * Submit jawaban quiz dan hitung nilai otomatis.
     */
    public function submitQuiz(int $userId, int $attemptId, array $answers): array
    {
        $attempt = $this->db->table('quiz_attempts')->where('id', $attemptId)->get()->getRowArray();
        if (! $attempt || $attempt['status'] !== 'started') {
            throw new RuntimeException('Data percobaan kuis tidak ditemukan atau tidak dapat diakses.');
        }

        $context = $this->quizContext($userId, (int) $attempt['quiz_id']);
        if ((int) $context['enrollment']['id'] !== (int) $attempt['enrollment_id']) {
            throw new RuntimeException('Data percobaan kuis tidak ditemukan atau tidak dapat diakses.');
        }

        // H-1: Penegakan batas waktu kuis di server (+ toleransi grace period 120 detik)
        $duration = (int) ($context['quiz']['duration_minutes'] ?? 0);
        if ($duration > 0 && ! empty($attempt['started_at'])) {
            $deadline = strtotime($attempt['started_at']) + ($duration * 60);
            if (time() > ($deadline + 120)) {
                throw new RuntimeException('Batas waktu pengerjaan quiz telah berakhir.');
            }
        }

        $questions = $this->db->table('quiz_questions')
            ->where('quiz_id', $context['quiz']['id'])
            ->get()->getResultArray();

        $this->db->transStart();

        // M-1 & M-2: Atomic conditional lock/update untuk mencegah race condition & double submit
        $this->db->table('quiz_attempts')
            ->where('id', $attemptId)
            ->where('status', 'started')
            ->update(['status' => 'submitted', 'updated_at' => date('Y-m-d H:i:s')]);

        if ($this->db->affectedRows() === 0) {
            $this->db->transRollback();
            throw new RuntimeException('Data percobaan kuis tidak valid atau sudah disubmit.');
        }

        // Bersihkan jawaban parsial jika ada untuk mencegah duplikasi (attempt_id, question_id)
        $this->db->table('quiz_answers')->where('attempt_id', $attemptId)->delete();

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
        $durationSeconds = ! empty($attempt['started_at']) ? (time() - strtotime($attempt['started_at'])) : 0;

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

        // M-10: Forensic audit trail (dilakukan setelah transaksi kuis sukses)
        try {
            $req = service('request');
            $ip = ($req instanceof \CodeIgniter\HTTP\IncomingRequest) ? $req->getIPAddress() : '127.0.0.1';
            $userAgent = ($req instanceof \CodeIgniter\HTTP\IncomingRequest) ? mb_substr((string) $req->getUserAgent(), 0, 255) : 'CLI/BegawiPengadaan';
            $this->db->table('audit_logs')->insert([
                'actor_id'        => $userId,
                'action'          => 'learning.quiz.submit',
                'resource_type'   => 'quiz_attempt',
                'resource_id'     => (string) $attemptId,
                'result'          => 'success',
                'before_snapshot' => null,
                'after_snapshot'  => json_encode([
                    'quiz_id'          => (int) $context['quiz']['id'],
                    'attempt_id'       => $attemptId,
                    'enrollment_id'    => (int) $context['enrollment']['id'],
                    'score'            => $scorePercent,
                    'duration_seconds' => $durationSeconds,
                    'submitted_at'     => $now,
                ], JSON_UNESCAPED_UNICODE),
                'reason'          => 'Forensik pengerjaan kuis peserta',
                'ip_hash'         => hash('sha256', (string) $ip),
                'user_agent_hash' => hash('sha256', (string) $userAgent),
                'request_id'      => bin2hex(random_bytes(8)),
                'created_at'      => $now,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Quiz forensic audit failed: {message}', ['message' => $e->getMessage()]);
        }

        // Evaluasi kelulusan jika ini post_test atau lesson_quiz (Pre-test tidak mengevaluasi kelulusan)
        if (($context['quiz']['type'] ?? '') !== 'pre_test') {
            $this->evaluateCompletion((int) $context['enrollment']['id']);
        }

        // Kirim notifikasi personal ke peserta terkait hasil pengerjaan kuis/ujian
        try {
            $quizTitle = $context['quiz']['title'] ?? 'Kuis Pembelajaran';
            $quizTypeLabel = match ($context['quiz']['type'] ?? '') {
                'pre_test'  => 'Pre-Test',
                'post_test' => 'Post-Test',
                default     => 'Kuis Modul',
            };
            (new NotificationService($this->db))->send(
                $userId,
                'quiz_graded',
                "Hasil {$quizTypeLabel} Tersedia",
                "Pengerjaan {$quizTypeLabel} '{$quizTitle}' telah selesai dievaluasi dengan skor {$scorePercent}.",
                [
                    'quiz_id'    => (int) $context['quiz']['id'],
                    'attempt_id' => $attemptId,
                    'score'      => $scorePercent,
                    'url'        => site_url('workspace/peserta/quiz/' . $context['quiz']['id'] . '?attempt=' . $attemptId),
                ]
            );
        } catch (\Throwable $e) {
            log_message('warning', 'Quiz notification failed: ' . $e->getMessage());
        }

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

        $assignment = $this->db->table('assignments')->where(['id' => $assignmentId, 'class_id' => $classId, 'status' => 'published'])->get()->getRowArray();
        if (! $assignment) {
            throw new RuntimeException('Tugas tidak ditemukan atau belum dipublikasikan.');
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

        // Shared notification ke Trainer & Operator pool kelas
        try {
            $userRow = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            $userName = $userRow['name'] ?? 'Peserta';
            (new NotificationService($this->db))->sendShared(
                'shared:class:' . $classId,
                'assignment_submitted',
                'Pengumpulan Tugas Baru',
                "Peserta {$userName} telah mengumpulkan tugas '{$assignment['title']}'.",
                [
                    'class_id'      => $classId,
                    'assignment_id' => $assignmentId,
                    'submission_id' => $submissionId,
                    'user_id'       => $userId,
                    'url'           => route_to('workspace.operator.submission.review', (string) $submissionId),
                ],
                $userId
            );
        } catch (\Throwable) {
        }

        return $submissionId;
    }

    /**
     * Evaluasi kelulusan belajar peserta dan penerbitan sertifikat resmi PBJ.
     * Aturan Standar Kurikulum:
     *  1. Wajib Pre-Test (jika ada pre-test yang diterbitkan di kelas)
     *  2. Wajib seluruh Materi Pelajaran tuntas 100%
     *  3. Wajib seluruh Kuis Modul dikerjakan (tidak boleh bypass)
     *  4. Opsional Tugas (jika kelas ada tugas, wajib dikumpulkan & dinilai instruktur; jika tidak ada, di-skip)
     *  5. Wajib Post-Test (bobot Post-Test dikunci di 60%)
     *  6. Bobot Kuis % dan Bobot Tugas % disetel di kelas (total akumulasi 100%)
     *  7. Kelulusan berdasarkan passing grade yang ditentukan admin kelas
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

        // 1. Cek Pre-Test: Wajib diselesaikan jika ada pre-test yang diterbitkan di kelas
        $preTest = $this->db->table('quizzes')
            ->where(['class_id' => $classId, 'type' => 'pre_test', 'status' => 'published'])
            ->orderBy('id', 'ASC')
            ->get()->getRowArray();

        if ($preTest) {
            $preAttemptCount = (int) $this->db->table('quiz_attempts')
                ->where(['quiz_id' => (int) $preTest['id'], 'enrollment_id' => $enrollmentId])
                ->whereIn('status', ['graded', 'submitted', 'passed', 'completed'])
                ->countAllResults();
            if ($preAttemptCount === 0) {
                return false; // Pre-test wajib belum dikerjakan
            }
        }

        // 2. Cek Lessons Wajib (Hanya yang berstatus 'published' di dalam modul 'published')
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

        // 3. Cek Kuis Modul: Setiap modul bisa ada kuis. Seluruh kuis modul yang diterbitkan di kelas WAJIB dikerjakan!
        $publishedQuizzes = $this->db->table('quizzes q')
            ->select('q.*')
            ->join('lessons l', 'l.id = q.lesson_id', 'left')
            ->join('learning_modules m', 'm.id = l.module_id', 'left')
            ->where('q.class_id', $classId)
            ->where('q.type', 'lesson_quiz')
            ->where('q.status', 'published')
            ->groupStart()
                ->where('q.lesson_id IS NULL', null, false)
                ->orGroupStart()
                    ->where('l.status', 'published')
                    ->where('m.status', 'published')
                ->groupEnd()
            ->groupEnd()
            ->orderBy('q.id', 'ASC')
            ->get()->getResultArray();

        $quizScores = [];
        if (! empty($publishedQuizzes)) {
            foreach ($publishedQuizzes as $q) {
                $bestAttempt = $this->db->table('quiz_attempts')
                    ->where(['quiz_id' => (int) $q['id'], 'enrollment_id' => $enrollmentId])
                    ->whereIn('status', ['graded', 'submitted', 'passed', 'completed'])
                    ->where('score IS NOT NULL', null, false)
                    ->orderBy('score', 'DESC')
                    ->get()->getRowArray();

                if (! $bestAttempt || $bestAttempt['score'] === null) {
                    return false; // Ada kuis modul yang belum dikerjakan! Tidak boleh bypass!
                }
                $quizScores[] = (float) $bestAttempt['score'];
            }
        }
        $avgQuiz = ! empty($quizScores) ? (array_sum($quizScores) / count($quizScores)) : null;

        // 4. Cek Tugas (Bersifat Opsional)
        // Jika ada tugas yang diterbitkan di kelas ini, peserta wajib mengumpulkan dan dinilai instruktur
        $publishedAssignments = $this->db->table('assignments a')
            ->select('a.*')
            ->join('lessons l', 'l.id = a.lesson_id', 'left')
            ->join('learning_modules m', 'm.id = l.module_id', 'left')
            ->where('a.class_id', $classId)
            ->where('a.status', 'published')
            ->groupStart()
                ->where('a.lesson_id IS NULL', null, false)
                ->orGroupStart()
                    ->where('l.status', 'published')
                    ->where('m.status', 'published')
                ->groupEnd()
            ->groupEnd()
            ->orderBy('a.id', 'ASC')
            ->get()->getResultArray();

        $asgScores = [];
        $hasAssignments = ! empty($publishedAssignments);
        if ($hasAssignments) {
            foreach ($publishedAssignments as $asg) {
                $sub = $this->db->table('assignment_submissions')
                    ->where(['assignment_id' => (int) $asg['id'], 'enrollment_id' => $enrollmentId])
                    ->where('score IS NOT NULL', null, false)
                    ->orderBy('score', 'DESC')
                    ->get()->getRowArray();

                // Tugas harus sudah dinilai untuk finalisasi kelulusan
                if (! $sub || $sub['status'] !== 'graded' || $sub['score'] === null) {
                    return false; // Tugas belum dikumpulkan atau belum dinilai oleh instruktur
                }
                $asgScores[] = (float) $sub['score'];
            }
        }
        $avgAssignment = ! empty($asgScores) ? (array_sum($asgScores) / count($asgScores)) : null;

        // 5. Cek Post-Test (WAJIB ADA & DIKERJAKAN)
        $postTest = $this->db->table('quizzes')
            ->where(['class_id' => $classId, 'type' => 'post_test', 'status' => 'published'])
            ->orderBy('id', 'ASC')
            ->get()->getRowArray();

        if (! $postTest) {
            // Jika kelas belum memiliki post-test yang diterbitkan, tidak boleh lulus otomatis
            return false;
        }

        $bestPostAttempt = $this->db->table('quiz_attempts')
            ->where(['quiz_id' => (int) $postTest['id'], 'enrollment_id' => $enrollmentId])
            ->whereIn('status', ['graded', 'submitted', 'passed', 'completed'])
            ->where('score IS NOT NULL', null, false)
            ->orderBy('score', 'DESC')
            ->get()->getRowArray();

        if (! $bestPostAttempt || $bestPostAttempt['score'] === null) {
            return false; // Post-test wajib belum dikerjakan!
        }
        $postScore = (float) $bestPostAttempt['score'];

        // 6. Perhitungan Nilai Akhir Terbobot (Standar Kurikulum PBJ)
        // Bobot Post-Test dikunci di 60%
        $wPost = 60.00;
        $hasQuiz = ! empty($publishedQuizzes) && $avgQuiz !== null;
        $hasAssignment = $hasAssignments && $avgAssignment !== null;

        if ($hasQuiz && $hasAssignment) {
            $wAsg = (float) ($class['assignment_weight'] ?? 20.00);
            if ($wAsg <= 0 || $wAsg > 40.00) {
                $wAsg = 20.00;
            }
            $wQuiz = max(0.00, 100.00 - $wPost - $wAsg);
        } elseif ($hasQuiz && ! $hasAssignment) {
            $wQuiz = 40.00;
            $wAsg  = 0.00;
        } elseif (! $hasQuiz && $hasAssignment) {
            $wQuiz = 0.00;
            $wAsg  = 40.00;
        } else {
            $wQuiz = 0.00;
            $wAsg  = 0.00;
        }

        // Jika kelas tidak memiliki kuis modul sama sekali dan tidak ada tugas (hanya post-test):
        if (! $hasQuiz && ! $hasAssignment) {
            $final = $postScore; // 100% dari post-test
        } else {
            $calcQuizPart = ($avgQuiz !== null) ? ($avgQuiz * ($wQuiz / 100.00)) : 0.00;
            $calcAsgPart  = ($avgAssignment !== null) ? ($avgAssignment * ($wAsg / 100.00)) : 0.00;
            $calcPostPart = ($postScore * ($wPost / 100.00));
            $final = round($calcQuizPart + $calcAsgPart + $calcPostPart, 2);
        }

        $passingGrade = (float) ($class['passing_grade'] ?? 70.00);
        $isPassed = ($final >= $passingGrade);
        $isCertEligible = $isPassed && (int) ($class['certificate_enabled'] ?? 0) === 1;

        // Cek apakah peserta masih memiliki sisa kuota re-attempt pada post-test jika belum lulus
        $attemptsUsed = (int) $this->db->table('quiz_attempts')
            ->where(['quiz_id' => (int) $postTest['id'], 'enrollment_id' => $enrollmentId])
            ->countAllResults();
        $attemptLimit = (int) ($postTest['attempt_limit'] ?? 1);
        $hasPostTestAttemptsRemaining = (! $isPassed && $attemptsUsed < $attemptLimit);

        $now = date('Y-m-d H:i:s');
        // Jika belum lulus tapi masih memiliki kuota re-attempt, pertahankan status 'active' agar tidak terkunci
        $newEnrollmentStatus = ($isPassed || ! $hasPostTestAttemptsRemaining) ? 'completed' : 'active';

        $this->db->table('enrollments')->where('id', $enrollmentId)->update([
            'status'                  => $newEnrollmentStatus,
            'completed_at'            => $isPassed ? ($enrollment['completed_at'] ?: $now) : (! $hasPostTestAttemptsRemaining ? ($enrollment['completed_at'] ?: $now) : null),
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

                // Kirim notifikasi personal bahwa peserta memenuhi syarat sertifikat
                // Cegah duplikasi notifikasi eligible untuk enrollment yang sama
                $alreadyNotified = $this->db->table('notifications')
                    ->where('user_id', (int) $enrollment['user_id'])
                    ->where('type', 'certificate_eligible')
                    ->like('data', '"enrollment_id":' . $enrollmentId)
                    ->countAllResults() > 0;

                if (! $alreadyNotified) {
                    (new NotificationService($this->db))->send(
                        (int) $enrollment['user_id'],
                        'certificate_eligible',
                        'Memenuhi Syarat Kelulusan & Sertifikat',
                        "Selamat! Anda telah dinyatakan lulus pada kelas '{$class['name']}' dengan nilai akhir {$final}. Penerbitan sertifikat Anda sedang diproses oleh sistem.",
                        [
                            'class_id'      => $classId,
                            'enrollment_id' => $enrollmentId,
                            'final_score'   => $final,
                            'url'           => site_url('workspace/peserta/sertifikat'),
                        ]
                    );
                }
            } catch (\Throwable $e) {
                log_message('warning', 'Auto-trigger certificate approval failed for enrollment {id}: {msg}', [
                    'id'  => $enrollmentId,
                    'msg' => $e->getMessage(),
                ]);
            }
        }

        return $isPassed;
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

        // Lessons: hanya berstatus published di modul published
        $totalLessons = (int) $this->db->query(
            'SELECT COUNT(lessons.id) AS cnt FROM lessons
             JOIN learning_modules ON learning_modules.id = lessons.module_id
             WHERE learning_modules.class_id = ? 
               AND lessons.status = "published" 
               AND learning_modules.status = "published"',
            [$classId]
        )->getRowArray()['cnt'] ?? 0;

        $completedLessons = (int) $this->db->query(
            'SELECT COUNT(DISTINCT lp.lesson_id) AS cnt FROM learning_progress lp
             JOIN lessons ON lessons.id = lp.lesson_id
             JOIN learning_modules ON learning_modules.id = lessons.module_id
             WHERE lp.enrollment_id = ? 
               AND lp.status = "completed" 
               AND lessons.status = "published" 
               AND learning_modules.status = "published"',
            [$enrollmentId]
        )->getRowArray()['cnt'] ?? 0;

        // Quizzes: hanya berstatus published (dan jika terikat modul/lesson, modul & lesson harus published)
        $totalQuizzes = (int) $this->db->query(
            'SELECT COUNT(q.id) AS cnt FROM quizzes q
             LEFT JOIN lessons l ON l.id = q.lesson_id
             LEFT JOIN learning_modules m ON m.id = l.module_id
             WHERE q.class_id = ? 
               AND q.status = "published"
               AND (q.lesson_id IS NULL OR (l.status = "published" AND m.status = "published"))',
            [$classId]
        )->getRowArray()['cnt'] ?? 0;

        $completedQuizzes = (int) $this->db->query(
            'SELECT COUNT(DISTINCT qa.quiz_id) AS cnt FROM quiz_attempts qa
             JOIN quizzes q ON q.id = qa.quiz_id
             LEFT JOIN lessons l ON l.id = q.lesson_id
             LEFT JOIN learning_modules m ON m.id = l.module_id
             WHERE qa.enrollment_id = ? 
               AND qa.status IN ("graded", "submitted", "passed", "completed")
               AND q.status = "published"
               AND (q.lesson_id IS NULL OR (l.status = "published" AND m.status = "published"))',
            [$enrollmentId]
        )->getRowArray()['cnt'] ?? 0;

        // Assignments: hanya berstatus published
        $totalAssignments = (int) $this->db->query(
            'SELECT COUNT(a.id) AS cnt FROM assignments a
             LEFT JOIN lessons l ON l.id = a.lesson_id
             LEFT JOIN learning_modules m ON m.id = l.module_id
             WHERE a.class_id = ? 
               AND a.status = "published"
               AND (a.lesson_id IS NULL OR (l.status = "published" AND m.status = "published"))',
            [$classId]
        )->getRowArray()['cnt'] ?? 0;

        $gradedAssignments = (int) $this->db->query(
            'SELECT COUNT(DISTINCT asub.assignment_id) AS cnt FROM assignment_submissions asub
             JOIN assignments a ON a.id = asub.assignment_id
             LEFT JOIN lessons l ON l.id = a.lesson_id
             LEFT JOIN learning_modules m ON m.id = l.module_id
             WHERE asub.enrollment_id = ? 
               AND asub.status = "graded"
               AND a.status = "published"
               AND (a.lesson_id IS NULL OR (l.status = "published" AND m.status = "published"))',
            [$enrollmentId]
        )->getRowArray()['cnt'] ?? 0;

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
            ->where('lessons.status', 'published')
            ->where('learning_modules.status', 'published')
            ->get()->getRowArray();

        if (! $lesson) {
            throw new RuntimeException('Lesson tidak ditemukan atau belum dipublikasikan.');
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
        $quiz = $this->db->table('quizzes')
            ->where(['id' => $quizId, 'status' => 'published'])
            ->get()->getRowArray();
        if (! $quiz) {
            throw new RuntimeException('Quiz tidak ditemukan atau belum dipublikasikan.');
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

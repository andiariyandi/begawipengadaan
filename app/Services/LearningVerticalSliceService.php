<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use RuntimeException;

/**
 * Temporary Schema V4 adapter for the participant learning vertical slice.
 *
 * Existing learning and certificate services still target legacy tables, so
 * this adapter keeps the V4 verification flow isolated until those services
 * can be migrated deliberately.
 */
class LearningVerticalSliceService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function enroll(int $userId, int $classId): int
    {
        $class = $this->db->table('learning_classes')->where('id', $classId)->get()->getRowArray();
        if (! $class) {
            throw new RuntimeException('Kelas tidak ditemukan.');
        }

        $existing = $this->db->table('enrollments')
            ->where(['class_id' => $classId, 'user_id' => $userId])
            ->get()->getRowArray();
        if ($existing) {
            throw new RuntimeException('Anda sudah terdaftar pada kelas ini.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('enrollments')->insert([
            'class_id' => $classId,
            'user_id' => $userId,
            'status' => 'pending',
            'enrolled_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (int) $this->db->insertID();
    }

    public function activateEnrollment(int $enrollmentId): void
    {
        $now = date('Y-m-d H:i:s');
        $enrollment = $this->db->table('enrollments')
            ->where('id', $enrollmentId)
            ->get()->getRowArray();

        if (! $enrollment) {
            throw new RuntimeException('Enrollment tidak ditemukan.');
        }
        if ($enrollment['status'] !== 'pending') {
            throw new RuntimeException('Hanya enrollment pending yang dapat diaktifkan.');
        }

        $this->db->transStart();
        $this->db->table('enrollments')->where('id', $enrollmentId)->update([
            'status' => 'active',
            'activated_at' => $now,
            'updated_at' => $now,
        ]);
        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            throw new RuntimeException('Enrollment gagal diaktifkan.');
        }
    }

    public function issuePendingCertificate(int $certificateId, int $actorId): void
    {
        $certificate = $this->db->table('certificates')
            ->where('id', $certificateId)
            ->get()->getRowArray();

        if (! $certificate) {
            throw new RuntimeException('Certificate tidak ditemukan.');
        }
        if ($certificate['status'] !== 'pending') {
            throw new RuntimeException('Hanya certificate pending yang dapat diterbitkan.');
        }
        if ($certificate['source_type'] !== 'learning_enrollment') {
            throw new RuntimeException('Source certificate tidak valid.');
        }

        $enrollment = $this->db->table('enrollments')
            ->where('id', $certificate['source_id'])
            ->get()->getRowArray();
        if (! $enrollment || $enrollment['status'] !== 'completed' || $enrollment['passed_at'] === null || $enrollment['certificate_eligible_at'] === null) {
            throw new RuntimeException('Enrollment belum memenuhi syarat penerbitan certificate.');
        }

        $class = $this->db->table('learning_classes')
            ->where('id', $enrollment['class_id'])
            ->get()->getRowArray();
        if (! $class || (int) $class['certificate_enabled'] !== 1) {
            throw new RuntimeException('Certificate tidak diaktifkan untuk kelas ini.');
        }

        $this->db->transStart();
        $this->db->table('certificates')->where('id', $certificateId)->update([
            'status' => 'issued',
            'issued_at' => date('Y-m-d H:i:s'),
            'issued_by' => $actorId,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            throw new RuntimeException('Certificate gagal diterbitkan.');
        }
    }

    public function startQuiz(int $userId, int $quizId): int
    {
        $context = $this->quizContext($userId, $quizId);
        $attempts = $this->db->table('quiz_attempts')
            ->where(['quiz_id' => $quizId, 'enrollment_id' => $context['enrollment']['id']])
            ->countAllResults();

        if ($attempts >= (int) $context['quiz']['attempt_limit']) {
            throw new RuntimeException('Batas percobaan quiz sudah tercapai.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('quiz_attempts')->insert([
            'quiz_id' => $quizId,
            'enrollment_id' => $context['enrollment']['id'],
            'attempt_number' => $attempts + 1,
            'status' => 'started',
            'started_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (int) $this->db->insertID();
    }

    /**
     * @param array<int, int|string|null> $answers question_id => option_id
     */
    public function submitQuiz(int $userId, int $attemptId, array $answers): array
    {
        $attempt = $this->db->table('quiz_attempts')->where('id', $attemptId)->get()->getRowArray();
        if (! $attempt || $attempt['status'] !== 'started') {
            throw new RuntimeException('Percobaan quiz tidak dapat disubmit.');
        }

        $context = $this->quizContext($userId, (int) $attempt['quiz_id'], (int) $attempt['enrollment_id']);
        $questions = $this->db->table('quiz_questions')
            ->where('quiz_id', $attempt['quiz_id'])->orderBy('sort_order')->get()->getResultArray();
        if (! $questions) {
            throw new RuntimeException('Quiz belum memiliki soal.');
        }

        $this->db->transStart();
        $totalPoints = 0.0;
        $awarded = 0.0;
        foreach ($questions as $question) {
            $options = $this->db->table('quiz_options')
                ->where('question_id', $question['id'])->orderBy('sort_order')->get()->getResultArray();
            $correct = array_values(array_filter($options, static fn (array $option): bool => (int) $option['is_correct'] === 1));
            if ($question['type'] !== 'multiple_choice' || count($correct) !== 1) {
                throw new RuntimeException('Setiap soal pilihan ganda harus memiliki tepat satu jawaban benar.');
            }

            $totalPoints += (float) $question['points'];
            $selected = isset($answers[$question['id']]) ? (int) $answers[$question['id']] : null;
            $validOption = $selected !== null && (bool) $this->db->table('quiz_options')
                ->where(['id' => $selected, 'question_id' => $question['id']])->countAllResults();
            $isCorrect = $validOption && $selected === (int) $correct[0]['id'];
            $score = $isCorrect ? (float) $question['points'] : 0.0;
            $awarded += $score;

            $this->db->table('quiz_answers')->insert([
                'attempt_id' => $attemptId,
                'question_id' => $question['id'],
                'answer_payload' => json_encode(['option_id' => $selected], JSON_THROW_ON_ERROR),
                'is_correct' => $isCorrect ? 1 : 0,
                'score_awarded' => $score,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $score = $totalPoints > 0 ? round(($awarded / $totalPoints) * 100, 2) : 0.0;
        $now = date('Y-m-d H:i:s');
        $this->db->table('quiz_attempts')->where('id', $attemptId)->update([
            'status' => 'graded',
            'submitted_at' => $now,
            'graded_at' => $now,
            'score' => $score,
            'updated_at' => $now,
        ]);
        $this->db->transComplete();
        if (! $this->db->transStatus()) {
            throw new RuntimeException('Jawaban quiz gagal disimpan.');
        }

        if ($context['quiz']['type'] === 'post_test') {
            $this->evaluateCompletion((int) $context['enrollment']['id']);
        }

        return ['score' => $score, 'attempt_id' => $attemptId];
    }

    public function startLesson(int $userId, int $lessonId): int
    {
        $lesson = $this->lessonContext($userId, $lessonId);
        $now = date('Y-m-d H:i:s');
        $progress = $this->db->table('learning_progress')
            ->where(['enrollment_id' => $lesson['enrollment']['id'], 'lesson_id' => $lessonId])
            ->get()->getRowArray();

        if ($progress) {
            $this->db->table('learning_progress')->where('id', $progress['id'])->update([
                'status' => $progress['status'] === 'completed' ? 'completed' : 'in_progress',
                'started_at' => $progress['started_at'] ?: $now,
                'progress_percent' => max(1, (float) $progress['progress_percent']),
                'updated_at' => $now,
            ]);
            return (int) $progress['id'];
        }

        $this->db->table('learning_progress')->insert([
            'enrollment_id' => $lesson['enrollment']['id'],
            'lesson_id' => $lessonId,
            'status' => 'in_progress',
            'started_at' => $now,
            'progress_percent' => 1.00,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (int) $this->db->insertID();
    }

    public function completeLesson(int $userId, int $lessonId): void
    {
        $lesson = $this->lessonContext($userId, $lessonId);
        $now = date('Y-m-d H:i:s');
        $this->db->table('learning_progress')->where([
            'enrollment_id' => $lesson['enrollment']['id'],
            'lesson_id' => $lessonId,
        ])->update([
            'status' => 'completed',
            'started_at' => $now,
            'completed_at' => $now,
            'progress_percent' => 100.00,
            'updated_at' => $now,
        ]);
        $this->evaluateCompletion((int) $lesson['enrollment']['id']);
    }

    public function evaluateCompletion(int $enrollmentId): bool
    {
        $enrollment = $this->db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();
        if (! $enrollment || $enrollment['status'] !== 'active') {
            return false;
        }

        $requiredLessons = $this->db->query(
            'SELECT lessons.id FROM lessons
             JOIN learning_modules ON learning_modules.id = lessons.module_id
             WHERE learning_modules.class_id = ? AND lessons.is_required = 1',
            [$enrollment['class_id']]
        )->getResultArray();
        if ($requiredLessons) {
            $completed = $this->db->table('learning_progress')
                ->where(['enrollment_id' => $enrollmentId, 'status' => 'completed'])
                ->whereIn('lesson_id', array_column($requiredLessons, 'id'))->countAllResults();
            if ($completed !== count($requiredLessons)) {
                return false;
            }
        }

        $lessonQuizzes = $this->db->table('quizzes')
            ->select('quizzes.id')
            ->join('lessons', 'lessons.id = quizzes.lesson_id')
            ->where(['quizzes.class_id' => $enrollment['class_id'], 'quizzes.type' => 'lesson_quiz', 'quizzes.is_required' => 1])
            ->get()->getResultArray();
        $postTest = $this->db->table('quizzes')->where(['class_id' => $enrollment['class_id'], 'type' => 'post_test', 'is_required' => 1])->get()->getRowArray();
        $quizScore = $this->gradedAverage($enrollmentId, $lessonQuizzes);
        $postScore = $this->gradedAverage($enrollmentId, $postTest ? [$postTest] : []);
        if ($quizScore === null || $postScore === null) {
            return false;
        }

        $class = $this->db->table('learning_classes')->where('id', $enrollment['class_id'])->get()->getRowArray();
        $final = round(($quizScore * (float) $class['quiz_weight'] / 100) + ($postScore * (float) $class['post_test_weight'] / 100), 2);
        $now = date('Y-m-d H:i:s');
        $this->db->table('enrollments')->where('id', $enrollmentId)->update([
            'status' => 'completed',
            'completed_at' => $now,
            'final_score' => $final,
            'passed_at' => $final >= (float) $class['passing_grade'] ? $now : null,
            'certificate_eligible_at' => $final >= (float) $class['passing_grade'] && (int) $class['certificate_enabled'] === 1 ? $now : null,
            'updated_at' => $now,
        ]);
        return true;
    }

    public function createPendingCertificate(int $enrollmentId): int
    {
        $row = $this->db->query(
            'SELECT enrollments.*, learning_classes.certificate_enabled
             FROM enrollments JOIN learning_classes ON learning_classes.id = enrollments.class_id
             WHERE enrollments.id = ?',
            [$enrollmentId]
        )->getRowArray();
        if (! $row || $row['status'] !== 'completed' || ! $row['passed_at'] || ! $row['certificate_eligible_at'] || (int) $row['certificate_enabled'] !== 1) {
            throw new RuntimeException('Enrollment belum eligible untuk sertifikat.');
        }

        if ($this->db->table('certificates')->where(['source_type' => 'learning_enrollment', 'source_id' => $enrollmentId])->countAllResults()) {
            throw new RuntimeException('Sertifikat untuk enrollment ini sudah dibuat.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('certificates')->insert([
            'source_type' => 'learning_enrollment',
            'source_id' => $enrollmentId,
            'user_id' => $row['user_id'],
            'certificate_number' => 'SERT-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4))),
            'verification_token_hash' => hash('sha256', bin2hex(random_bytes(32))),
            'status' => 'pending',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        return (int) $this->db->insertID();
    }

    private function quizContext(int $userId, int $quizId, ?int $enrollmentId = null): array
    {
        $quiz = $this->db->table('quizzes')->where('id', $quizId)->get()->getRowArray();
        if (! $quiz) {
            throw new RuntimeException('Quiz tidak ditemukan.');
        }
        $builder = $this->db->table('enrollments')->where([
            'user_id' => $userId,
            'class_id' => $quiz['class_id'],
            'status' => 'active',
        ]);
        if ($enrollmentId !== null) {
            $builder->where('id', $enrollmentId);
        }
        $enrollment = $builder->get()->getRowArray();
        if (! $enrollment) {
            throw new RuntimeException('Enrollment aktif tidak ditemukan.');
        }
        return ['quiz' => $quiz, 'enrollment' => $enrollment];
    }

    private function lessonContext(int $userId, int $lessonId): array
    {
        $row = $this->db->query(
            'SELECT lessons.*, learning_modules.class_id, enrollments.id AS enrollment_id
             FROM lessons JOIN learning_modules ON learning_modules.id = lessons.module_id
             JOIN enrollments ON enrollments.class_id = learning_modules.class_id
             WHERE lessons.id = ? AND enrollments.user_id = ? AND enrollments.status = ?',
            [$lessonId, $userId, 'active']
        )->getRowArray();
        if (! $row) {
            throw new RuntimeException('Lesson tidak dapat diakses.');
        }
        return ['lesson' => $row, 'enrollment' => ['id' => $row['enrollment_id'], 'class_id' => $row['class_id']]];
    }

    private function gradedAverage(int $enrollmentId, array $quizzes): ?float
    {
        if (! $quizzes) {
            return null;
        }
        $scores = [];
        foreach ($quizzes as $quiz) {
            $attempt = $this->db->table('quiz_attempts')->where([
                'quiz_id' => $quiz['id'],
                'enrollment_id' => $enrollmentId,
                'status' => 'graded',
            ])->orderBy('attempt_number', 'DESC')->get()->getRowArray();
            if (! $attempt) {
                return null;
            }
            $scores[] = (float) $attempt['score'];
        }
        return round(array_sum($scores) / count($scores), 2);
    }
}

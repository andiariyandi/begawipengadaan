<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LearningVerticalSliceSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $admin = $this->db->table('users')->where('account_status', 'active')->orderBy('id')->get()->getRowArray();
        if (! $admin) {
            throw new \RuntimeException('Active admin fixture prerequisite tidak ditemukan.');
        }

        $participant = $this->db->table('users')->where('email', 'participant.v4@begawipbj.test')->get()->getRowArray();
        if (! $participant) {
            $this->db->table('users')->insert([
                'name' => 'Participant Schema V4',
                'email' => 'participant.v4@begawipbj.test',
                'username' => 'participant_v4',
                'password_hash' => password_hash('ChangeMe123!', PASSWORD_DEFAULT),
                'phone' => '081234567890',
                'profile_type' => 'public',
                'account_status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $participant = $this->db->table('users')->where('email', 'participant.v4@begawipbj.test')->get()->getRowArray();
        }

        $participantRole = $this->db->table('roles')->where('name', 'Peserta')->get()->getRowArray();
        if (! $participantRole) {
            throw new \RuntimeException('Role Peserta prerequisite tidak ditemukan.');
        }
        $this->db->table('user_roles')->ignore(true)->insert([
            'user_id' => $participant['id'],
            'role_id' => $participantRole['id'],
            'status' => 'active',
            'granted_by' => $admin['id'],
            'granted_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $program = $this->firstOrInsert('programs', ['slug' => 'fixture-program-v4'], [
            'title' => 'Fixture Program Schema V4',
            'slug' => 'fixture-program-v4',
            'summary' => 'Fixture minimum learning vertical slice.',
            'description' => 'Data development untuk verifikasi Schema V4.',
            'status' => 'published',
            'visibility' => 'public',
            'created_by' => $admin['id'],
            'published_by' => $admin['id'],
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $course = $this->firstOrInsert('courses', ['slug' => 'fixture-course-v4'], [
            'program_id' => $program['id'],
            'title' => 'Fixture Course Schema V4',
            'slug' => 'fixture-course-v4',
            'summary' => 'Fixture course.',
            'level' => 'basic',
            'duration_minutes' => 120,
            'status' => 'published',
            'visibility' => 'public',
            'created_by' => $admin['id'],
            'published_by' => $admin['id'],
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $class = $this->firstOrInsert('learning_classes', ['course_id' => $course['id'], 'code' => 'FIXTURE-V4'], [
            'course_id' => $course['id'],
            'code' => 'FIXTURE-V4',
            'name' => 'Fixture Class Schema V4',
            'status' => 'active',
            'capacity' => 30,
            'certificate_enabled' => 1,
            'passing_grade' => 70.00,
            'quiz_weight' => 40.00,
            'assignment_weight' => 0.00,
            'post_test_weight' => 60.00,
            'created_by' => $admin['id'],
            'updated_by' => $admin['id'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $modules = [];
        foreach ([1 => 'Module 1', 2 => 'Module 2'] as $order => $title) {
            $modules[$order] = $this->firstOrInsert('learning_modules', ['class_id' => $class['id'], 'sort_order' => $order], [
                'class_id' => $class['id'],
                'title' => $title,
                'description' => 'Fixture ' . $title,
                'sort_order' => $order,
                'is_required' => 1,
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $lessons = [];
        foreach ([1 => 'Lesson 1', 2 => 'Lesson 2'] as $order => $title) {
            $lessons[$order] = $this->firstOrInsert('lessons', ['module_id' => $modules[$order]['id'], 'sort_order' => $order], [
                'module_id' => $modules[$order]['id'],
                'title' => $title,
                'slug' => 'fixture-lesson-' . $order . '-v4',
                'content_type' => 'text',
                'content' => 'Konten fixture ' . $title . '.',
                'sort_order' => $order,
                'is_required' => 1,
                'status' => 'published',
                'visibility' => 'internal',
                'created_by' => $admin['id'],
                'updated_by' => $admin['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->firstOrInsert('pre_tests', ['class_id' => $class['id']], [
            'class_id' => $class['id'],
            'is_enabled' => 1,
            'attempt_limit' => 1,
            'status' => 'published',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->createQuiz($class['id'], null, 'pre_test', 'Fixture Pre-test V4', false, $now);
        $this->createQuiz($class['id'], $lessons[1]['id'], 'lesson_quiz', 'Fixture Lesson Quiz V4', true, $now);
        $this->createQuiz($class['id'], null, 'post_test', 'Fixture Post-test V4', true, $now);

        $template = $this->db->table('certificate_templates')->where('name', 'Fixture Learning Certificate V4')->get()->getRowArray();
        if (! $template) {
            $this->db->table('certificate_templates')->insert([
                'name' => 'Fixture Learning Certificate V4',
                'source_type' => 'learning',
                'template_payload' => json_encode(['title' => 'Sertifikat Pembelajaran']),
                'is_active' => 1,
                'created_by' => $admin['id'],
                'updated_by' => $admin['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function firstOrInsert(string $table, array $where, array $data): array
    {
        $row = $this->db->table($table)->where($where)->get()->getRowArray();
        if ($row) {
            return $row;
        }
        $this->db->table($table)->insert($data);
        return $this->db->table($table)->where($where)->get()->getRowArray();
    }

    private function createQuiz(int $classId, ?int $lessonId, string $type, string $title, bool $required, string $now): void
    {
        $where = ['class_id' => $classId, 'type' => $type, 'title' => $title];
        $quiz = $this->db->table('quizzes')->where($where)->get()->getRowArray();
        if (! $quiz) {
            $this->db->table('quizzes')->insert([
                'class_id' => $classId,
                'lesson_id' => $lessonId,
                'type' => $type,
                'title' => $title,
                'instructions' => 'Pilih satu jawaban yang paling tepat.',
                'attempt_limit' => 1,
                'is_required' => $required ? 1 : 0,
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $quiz = $this->db->table('quizzes')->where($where)->get()->getRowArray();
        }

        $question = $this->db->table('quiz_questions')->where(['quiz_id' => $quiz['id'], 'sort_order' => 1])->get()->getRowArray();
        if ($question) {
            return;
        }
        $this->db->table('quiz_questions')->insert([
            'quiz_id' => $quiz['id'],
            'type' => 'multiple_choice',
            'prompt' => 'Jawaban benar fixture?',
            'points' => 1.00,
            'sort_order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $questionId = $this->db->insertID();
        foreach (['Jawaban A', 'Jawaban B', 'Jawaban C', 'Jawaban D'] as $order => $text) {
            $this->db->table('quiz_options')->insert([
                'question_id' => $questionId,
                'option_text' => $text,
                'is_correct' => $order === 0 ? 1 : 0,
                'sort_order' => $order + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

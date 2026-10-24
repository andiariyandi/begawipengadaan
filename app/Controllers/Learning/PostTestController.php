<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class PostTestController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));

        $builder = $db->table('quizzes q')
            ->select('q.*, lm.title AS module_title, c.title AS course_title,
                      (SELECT COUNT(1) FROM quiz_questions qq WHERE qq.quiz_id = q.id) AS question_count')
            ->join('lessons l', 'l.id = q.lesson_id', 'left')
            ->join('learning_modules lm', 'lm.id = l.module_id', 'left')
            ->join('learning_classes lc2', 'lc2.id = COALESCE(q.class_id, lm.class_id)', 'left')
            ->join('courses c', 'c.id = lc2.course_id', 'left')
            ->where('q.type', 'post_test')
            ->orderBy('q.created_at', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('q.title', $keyword)
                ->orLike('c.title', $keyword)
                ->orLike('lm.title', $keyword)
                ->groupEnd();
        }

        $quizzes = $builder->get()->getResultArray();

        $stats = [
            'total'          => count($quizzes),
            'totalQuestions' => (int) $db->table('quiz_questions qq')
                ->join('quizzes q', 'q.id = qq.quiz_id')
                ->where('q.type', 'post_test')
                ->countAllResults(),
            'totalAttempts'  => (int) $db->table('quiz_attempts qa')
                ->join('quizzes q', 'q.id = qa.quiz_id')
                ->where('q.type', 'post_test')
                ->countAllResults(),
        ];

        return view('dashboard/learning/assessments/index', [
            'title'          => 'Post-Test | Dashboard BegawiPengadaan',
            'pageTitle'      => 'Post-Test',
            'assessmentType' => 'Post-Test',
            'quizzes'        => $quizzes,
            'stats'          => $stats,
            'filters'        => ['keyword' => $keyword],
            'activeMenu'     => 'learning',
            'activeSubmenu'  => 'assessments',
        ]);
    }
}

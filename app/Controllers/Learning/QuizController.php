<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class QuizController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));

        $builder = $db->table('quizzes q')
            ->select('q.*, lm.title AS module_title, c.title AS course_title,
                      (SELECT COUNT(1) FROM quiz_questions qq WHERE qq.quiz_id = q.id) AS question_count')
            ->join('learning_modules lm', 'lm.id = q.module_id', 'left')
            ->join('courses c', 'c.id = lm.course_id', 'left')
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
            'total'          => (int) $db->table('quizzes')->countAllResults(),
            'totalQuestions' => (int) $db->table('quiz_questions')->countAllResults(),
            'totalAttempts'  => (int) $db->table('quiz_attempts')->countAllResults(),
        ];

        return view('dashboard/learning/assessments/index', [
            'title'          => 'Assessment dan Bank Soal | Dashboard BegawiPBJ',
            'pageTitle'      => 'Assessment dan Bank Soal',
            'assessmentType' => 'Semua Assessment',
            'quizzes'        => $quizzes,
            'stats'          => $stats,
            'filters'        => ['keyword' => $keyword],
            'activeMenu'     => 'learning',
            'activeSubmenu'  => 'assessments',
        ]);
    }

    public function questionBank(): string
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));

        $builder = $db->table('quiz_questions qq')
            ->select('qq.*, q.title AS quiz_title, c.title AS course_title')
            ->join('quizzes q', 'q.id = qq.quiz_id', 'left')
            ->join('learning_modules lm', 'lm.id = q.module_id', 'left')
            ->join('courses c', 'c.id = lm.course_id', 'left')
            ->orderBy('qq.id', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('qq.question_text', $keyword)
                ->orLike('q.title', $keyword)
                ->groupEnd();
        }

        $questions = $builder->get()->getResultArray();

        return view('dashboard/learning/assessments/question_bank', [
            'title'         => 'Bank Soal | Dashboard BegawiPBJ',
            'pageTitle'     => 'Bank Soal Evaluasi',
            'questions'     => $questions,
            'filters'       => ['keyword' => $keyword],
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'assessments',
        ]);
    }
}

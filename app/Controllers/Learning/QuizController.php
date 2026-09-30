<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class QuizController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/learning/assessments/index', [
            'title' => 'Assessment dan Bank Soal | Dashboard BegawiPBJ',
            'pageTitle' => 'Assessment dan Bank Soal',
            'assessmentType' => 'Semua Assessment',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'assessments',
        ]);
    }

    public function questionBank(): string
    {
        return view('dashboard/learning/assessments/question_bank', [
            'title' => 'Bank Soal | Dashboard BegawiPBJ',
            'pageTitle' => 'Bank Soal',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'assessments',
        ]);
    }
}

<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class StudyController extends BaseController
{
    public function progress(): string
    {
        return view('dashboard/learning/progress/index', [
            'title' => 'Progres dan Kelulusan | Dashboard BegawiPBJ',
            'pageTitle' => 'Progres dan Kelulusan',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'progress-completion',
        ]);
    }

    public function feedback(): string
    {
        return view('dashboard/learning/feedback/index', [
            'title' => 'Feedback Peserta | Dashboard BegawiPBJ',
            'pageTitle' => 'Feedback Peserta',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'participant-feedback',
        ]);
    }
}

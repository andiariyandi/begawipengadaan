<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class EnrollmentController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/learning/enrollments/index', [
            'title' => 'Enrollment | Dashboard BegawiPBJ',
            'pageTitle' => 'Enrollment',
            'activeMenu' => 'learning',
            'activeSubmenu' => 'enrollments',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/learning/enrollments/detail', [
            'title' => 'Detail Enrollment | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Enrollment',
            'enrollmentId' => $id,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'enrollments',
        ]);
    }
}

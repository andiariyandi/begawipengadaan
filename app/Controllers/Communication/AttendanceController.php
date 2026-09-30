<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;

class AttendanceController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/communication/attendance/index', [
            'title' => 'Kehadiran Event | Dashboard BegawiPBJ',
            'pageTitle' => 'Kehadiran',
            'activeMenu' => 'communication',
            'activeSubmenu' => 'attendance',
        ]);
    }
}

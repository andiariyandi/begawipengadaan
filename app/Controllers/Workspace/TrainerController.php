<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;

class TrainerController extends BaseController
{
    public function index()
    {
        return view('workspace/trainer/index');
    }
}

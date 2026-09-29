<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;

class EvaluatorController extends BaseController
{
    public function index()
    {
        return view('workspace/evaluator/index');
    }
}

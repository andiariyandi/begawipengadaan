<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;

class PesertaController extends BaseController
{
    public function index()
    {
        return view('workspace/peserta/index');
    }
}

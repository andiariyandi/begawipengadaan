<?php

namespace App\Controllers\Regulation;

use App\Controllers\BaseController;

class RegulationDownloadController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/regulation/download_logs/index', [
            'title' => 'Log Download Regulasi | Dashboard BegawiPBJ',
            'pageTitle' => 'Log Download Regulasi',
            'activeMenu' => 'regulations',
            'activeSubmenu' => 'download-logs',
        ]);
    }
}

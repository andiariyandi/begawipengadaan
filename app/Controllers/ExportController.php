<?php

namespace App\Controllers;

class ExportController extends BaseController
{
    public function index()
    {
        return redirect()->to(site_url('dashboard/laporan/riwayat-export'));
    }
}

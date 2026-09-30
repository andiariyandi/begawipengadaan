<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;

class CertificateApprovalController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/certificates/approvals/index', [
            'title' => 'Approval Sertifikat | Dashboard BegawiPBJ',
            'pageTitle' => 'Approval Sertifikat',
            'activeMenu' => 'certificates',
            'activeSubmenu' => 'approvals',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/certificates/approvals/detail', [
            'title' => 'Detail Approval Sertifikat | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Approval Sertifikat',
            'approvalId' => $id,
            'activeMenu' => 'certificates',
            'activeSubmenu' => 'approvals',
        ]);
    }
}

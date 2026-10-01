<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class CertificateVerificationController extends BaseController
{
    public function index(): string { return view('landing/pages/certificate_verify', ['title' => 'Verifikasi Sertifikat | BegawiPBJ', 'pageTitle' => 'Verifikasi Sertifikat']); }
    public function result(): string { return view('landing/pages/certificate_result', ['title' => 'Hasil Verifikasi | BegawiPBJ', 'pageTitle' => 'Hasil Verifikasi']); }
    public function detail(string $slug): string { return view('landing/pages/certificate_result', ['title' => 'Detail Sertifikat | BegawiPBJ', 'pageTitle' => 'Detail Sertifikat', 'certificateId' => $slug]); }
}

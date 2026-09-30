<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AdminAuthController extends BaseController
{
    public function index(): string
    {
        return $this->authView(
            'Masuk | BegawiPBJ',
            'Masuk ke Akun Anda',
            'Silakan masukkan email dan kata sandi untuk mengakses layanan BegawiPBJ.',
            'login'
        );
    }

    public function register(): string
    {
        return $this->registerView(
            'Daftar Akun | BegawiPBJ',
            'Buat Akun BegawiPBJ',
            'Langkah 1 dari 4 — Lengkapi akun dasar dan pilih tipe profil Anda.',
            'register',
            1,
            'akun'
        );
    }

    public function registerAddress(): string
    {
        return $this->registerView(
            'Wilayah dan Alamat | BegawiPBJ',
            'Wilayah dan Alamat Ringkas',
            'Langkah 2 dari 4 — Lengkapi wilayah domisili dan alamat ringkas Anda.',
            'register_address',
            2,
            'wilayah'
        );
    }

    public function registerAsn(): string
    {
        return $this->registerView(
            'Profil ASN | BegawiPBJ',
            'Lengkapi Profil ASN',
            'Langkah 3 dari 4 — Lengkapi data kepegawaian dan instansi Anda.',
            'register_asn',
            3,
            'profil',
            ['profileType' => 'ASN']
        );
    }

    public function registerProvider(): string
    {
        return $this->registerView(
            'Profil Penyedia Barang/Jasa | BegawiPBJ',
            'Lengkapi Profil Penyedia Barang/Jasa',
            'Langkah 3 dari 4 — Lengkapi data badan usaha dan penanggung jawab.',
            'register_provider',
            3,
            'profil',
            ['profileType' => 'Penyedia Barang/Jasa']
        );
    }

    public function registerAcademic(): string
    {
        return $this->registerView(
            'Profil Akademisi | BegawiPBJ',
            'Lengkapi Profil Akademisi',
            'Langkah 3 dari 4 — Lengkapi institusi, peran, dan bidang keahlian Anda.',
            'register_academic',
            3,
            'profil',
            ['profileType' => 'Akademisi']
        );
    }

    public function registerPublic(): string
    {
        return $this->registerView(
            'Profil Masyarakat Umum | BegawiPBJ',
            'Lengkapi Profil Masyarakat Umum',
            'Langkah 3 dari 4 — Lengkapi pekerjaan atau status serta minat Anda.',
            'register_public',
            3,
            'profil',
            ['profileType' => 'Masyarakat Umum']
        );
    }

    public function registerReview(): string
    {
        return $this->registerView(
            'Tinjau Pendaftaran | BegawiPBJ',
            'Tinjau Data Pendaftaran',
            'Langkah 4 dari 4 — Pastikan seluruh data yang Anda masukkan sudah benar.',
            'register_review',
            4,
            'tinjau'
        );
    }

    public function registerSuccess(): string
    {
        return view('dashboard/auth/register_success', [
            'title' => 'Pendaftaran Berhasil | BegawiPBJ',
            'pageTitle' => 'Pendaftaran Berhasil Dikirim',
            'pageDescription' => 'Akun Anda sedang menunggu verifikasi email dan pemeriksaan Operator/Admin.',
        ]);
    }

    public function forgotPassword(): string
    {
        return $this->authView(
            'Lupa Kata Sandi | BegawiPBJ',
            'Lupa Kata Sandi',
            'Masukkan alamat email akun Anda untuk menerima instruksi pengaturan ulang kata sandi.',
            'forgot_password'
        );
    }

    public function logoutPreview(): string
    {
        return view('dashboard/auth/logout_preview', [
            'title' => 'Keluar | BegawiPBJ',
        ]);
    }

    private function authView(
        string $title,
        string $pageTitle,
        string $pageDescription,
        string $viewName
    ): string {
        return view('dashboard/auth/' . $viewName, [
            'title' => $title,
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
        ]);
    }

    private function registerView(
        string $title,
        string $pageTitle,
        string $pageDescription,
        string $viewName,
        int $currentStep,
        string $currentKey,
        array $data = []
    ): string {
        return view('dashboard/auth/' . $viewName, array_merge([
            'title' => $title,
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'currentStep' => $currentStep,
            'currentKey' => $currentKey,
        ], $data));
    }
}

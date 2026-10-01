<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index(): string
    {
        return view('landing/home/index', [
            'title' => 'BegawiPBJ | Platform Pengadaan Barang/Jasa Provinsi Lampung',
            'pageTitle' => 'BegawiPBJ | Platform Pengadaan Barang/Jasa Provinsi Lampung',
            'metaDescription' => 'Platform informasi, pembelajaran, regulasi, dan ekosistem Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.',
            'metaKeywords' => 'BegawiPBJ, pengadaan barang jasa, PBJ, Provinsi Lampung, pembelajaran PBJ',
        ]);
    }

    public function about(): string
    {
        return view('landing/pages/about', [
            'title' => 'Tentang BegawiPBJ | Provinsi Lampung',
            'metaDescription' => 'Tentang visi, misi, nilai, dan ekosistem layanan BegawiPBJ.',
            'metaKeywords' => 'tentang BegawiPBJ, Biro PBJ Lampung, pengadaan barang jasa Lampung',
            'breadcrumbs' => [
                ['label' => 'Tentang BegawiPBJ'],
            ],
        ]);
    }

    public function contact(): string
    {
        return view('landing/home/contact', [
            'title' => 'Kontak BegawiPBJ | Provinsi Lampung',
            'metaDescription' => 'Hubungi tim BegawiPBJ untuk informasi dan layanan Pengadaan Barang/Jasa.',
            'metaKeywords' => 'kontak BegawiPBJ, Biro PBJ Lampung, pengadaan barang jasa',
            'breadcrumbs' => [
                ['label' => 'Kontak'],
            ],
        ]);
    }

    public function forbiddenPreview(): string
    {
        return $this->errorPreview(
            'Akses Ditolak',
            'Anda tidak memiliki izin untuk mengakses halaman ini.',
            '403'
        );
    }

    public function notFoundPreview(): string
    {
        return $this->errorPreview(
            'Halaman Tidak Ditemukan',
            'Halaman yang Anda cari tidak tersedia atau telah dipindahkan.',
            '404'
        );
    }

    public function serverErrorPreview(): string
    {
        return $this->errorPreview(
            'Terjadi Gangguan Sistem',
            'Sistem sedang mengalami kendala. Silakan coba kembali beberapa saat lagi.',
            '500'
        );
    }

    private function errorPreview(
        string $pageTitle,
        string $message,
        string $statusCode
    ): string {
        return view('landing/home/error_preview', [
            'title' => $statusCode . ' | ' . $pageTitle . ' | BegawiPBJ',
            'metaDescription' => $message,
            'pageTitle' => $pageTitle,
            'message' => $message,
            'statusCode' => $statusCode,
        ]);
    }
}

<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index(): string
    {
        return $this->render('landing/home/index', [
            'pageTitle' => 'BegawiPBJ — Portal Pembelajaran PBJ',
            'pageDescription' => 'Portal pembelajaran, pengetahuan, regulasi, event, dan sertifikat digital Pengadaan Barang/Jasa.',
            'activeMenu' => 'home',
            'heroPrograms' => [
                [
                    'title' => 'Dasar-Dasar Pengadaan Barang/Jasa',
                    'category' => 'Arena Belajar',
                    'image' => 'assets/main/assets/img/course/home-3/course-1.jpg',
                    'url' => site_url('arena-belajar/course/dasar-pengadaan-barang-jasa'),
                ],
                [
                    'title' => 'Manajemen Kontrak PBJ',
                    'category' => 'Arena Belajar',
                    'image' => 'assets/main/assets/img/course/home-3/course-2.jpg',
                    'url' => site_url('arena-belajar/course/manajemen-kontrak-pbj'),
                ],
                [
                    'title' => 'Perencanaan Pengadaan',
                    'category' => 'Arena Belajar',
                    'image' => 'assets/main/assets/img/course/home-3/course-3.jpg',
                    'url' => site_url('arena-belajar/course/perencanaan-pengadaan'),
                ],
            ],
            'services' => [
                [
                    'icon' => 'flaticon-puzzle',
                    'title' => 'Arena Belajar',
                    'description' => 'Program, course, kelas, materi, dan evaluasi pembelajaran PBJ.',
                    'url' => site_url('arena-belajar'),
                ],
                [
                    'icon' => 'flaticon-online-learning',
                    'title' => 'Bunga Rampai',
                    'description' => 'Artikel, FAQ, studi kasus, template, media, dan referensi pengetahuan.',
                    'url' => site_url('bunga-rampai'),
                ],
                [
                    'icon' => 'flaticon-certificate',
                    'title' => 'Regulasi PBJ',
                    'description' => 'Repository regulasi, versi, relasi, serta informasi status hukum.',
                    'url' => site_url('regulasi'),
                ],
            ],
            'statistics' => [
                ['value' => '120+', 'label' => 'Materi Pembelajaran'],
                ['value' => '48', 'label' => 'Kelas Aktif'],
                ['value' => '36', 'label' => 'Regulasi Tersedia'],
                ['value' => '1.250+', 'label' => 'Pengguna Terdaftar'],
            ],
        ]);
    }

    public function badRequestPreview(): string
    {
        return view('error/400');
    }

    public function forbiddenPreview(): string
    {
        return view('error/403');
    }

    public function notFoundPreview(): string
    {
        return view('error/404');
    }

    public function csrfPreview(): string
    {
        return view('error/419');
    }

    public function rateLimitPreview(): string
    {
        return view('error/429');
    }

    public function serverErrorPreview(): string
    {
        return view('error/500');
    }

    public function maintenancePreview(): string
    {
        return view('error/maintenance');
    }
}
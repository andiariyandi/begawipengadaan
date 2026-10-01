<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class ArenaBelajarController extends BaseController
{
    public function index(): string
    {
        return view('landing/pages/learning', [
            'title' => 'Arena Belajar | BegawiPBJ',
            'metaDescription' => 'Pusat pembelajaran, program, kelas, dan pengajar Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.',
            'metaKeywords' => 'Arena Belajar, pembelajaran PBJ, pelatihan pengadaan, BegawiPBJ',
        ]);
    }

    public function programs(): string
    {
        return $this->catalog('Program Pembelajaran', 'programs', 'Program terstruktur untuk membangun kompetensi PBJ dari dasar hingga lanjutan.');
    }

    public function courses(): string
    {
        return $this->catalog('Course PBJ', 'courses', 'Materi mandiri tentang regulasi, perencanaan, pelaksanaan, dan evaluasi pengadaan.');
    }

    public function classes(): string
    {
        return $this->catalog('Kelas dan Diskusi', 'classes', 'Kelas aktif dan forum diskusi untuk belajar bersama pengajar serta peserta PBJ.');
    }

    public function trainers(): string
    {
        return $this->catalog('Pengajar PBJ', 'trainers', 'Pengajar dan praktisi yang berbagi pengalaman serta praktik baik Pengadaan Barang/Jasa.');
    }

    public function programDetail(string $slug): string
    {
        return $this->detail('Detail Program Pembelajaran', 'Program ' . $this->formatSlug($slug), 'programs');
    }

    public function courseDetail(string $slug): string
    {
        return $this->detail('Detail Course PBJ', 'Course ' . $this->formatSlug($slug), 'courses');
    }

    public function classDetail(string $slug): string
    {
        return $this->detail('Detail Kelas PBJ', 'Kelas ' . $this->formatSlug($slug), 'classes');
    }

    public function trainerDetail(string $slug): string
    {
        return $this->detail('Profil Pengajar PBJ', $this->formatSlug($slug), 'trainers');
    }

    private function catalog(string $pageTitle, string $catalogType, string $description): string
    {
        return view('landing/pages/learning_catalog', [
            'title' => $pageTitle . ' | Arena Belajar BegawiPBJ',
            'metaDescription' => $description,
            'metaKeywords' => 'Arena Belajar, ' . strtolower($pageTitle) . ', BegawiPBJ',
            'pageTitle' => $pageTitle,
            'catalogType' => $catalogType,
            'description' => $description,
        ]);
    }

    private function detail(string $pageTitle, string $itemTitle, string $catalogType): string
    {
        return view('landing/pages/learning_detail', [
            'title' => $itemTitle . ' | Arena Belajar BegawiPBJ',
            'metaDescription' => 'Informasi ' . strtolower($pageTitle) . ' pada Arena Belajar BegawiPBJ.',
            'metaKeywords' => 'BegawiPBJ, Arena Belajar, ' . strtolower($pageTitle),
            'pageTitle' => $pageTitle,
            'itemTitle' => $itemTitle,
            'catalogType' => $catalogType,
        ]);
    }

    private function formatSlug(string $slug): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }
}

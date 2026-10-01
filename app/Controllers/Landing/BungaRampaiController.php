<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class BungaRampaiController extends BaseController
{
    public function index(): string
    {
        return view('landing/pages/knowledge', [
            'title' => 'Bunga Rampai | BegawiPBJ',
            'metaDescription' => 'Pusat pengetahuan, artikel, studi kasus, FAQ, template, dan media Pengadaan Barang/Jasa.',
            'metaKeywords' => 'Bunga Rampai, artikel PBJ, studi kasus pengadaan, BegawiPBJ',
        ]);
    }

    public function articles(): string
    {
        return $this->catalog('Artikel PBJ', 'articles', 'Wawasan dan praktik baik untuk mendukung proses Pengadaan Barang/Jasa.');
    }

    public function faqs(): string
    {
        return $this->catalog('Tanya Jawab PBJ', 'faqs', 'Jawaban ringkas atas pertanyaan umum seputar layanan dan proses PBJ.');
    }

    public function caseStudies(): string
    {
        return $this->catalog('Studi Kasus', 'cases', 'Pembelajaran dari pengalaman dan tantangan Pengadaan Barang/Jasa di lapangan.');
    }

    public function templates(): string
    {
        return $this->catalog('Template Dokumen', 'templates', 'Contoh format dokumen yang membantu menyiapkan proses PBJ secara tertib.');
    }

    public function media(): string
    {
        return $this->catalog('Media PBJ', 'media', 'Kumpulan infografik, dokumentasi, dan materi visual edukasi PBJ.');
    }

    public function videos(): string
    {
        return $this->catalog('Video PBJ', 'videos', 'Video pembelajaran dan rekaman berbagi praktik baik Pengadaan Barang/Jasa.');
    }

    public function gallery(): string
    {
        return $this->catalog('Galeri Kegiatan', 'gallery', 'Dokumentasi kegiatan pembelajaran dan kolaborasi ekosistem PBJ Lampung.');
    }

    public function articleDetail(string $slug): string
    {
        return $this->detail('Detail Artikel PBJ', $slug, 'articles');
    }

    public function caseStudyDetail(string $slug): string
    {
        return $this->detail('Detail Studi Kasus', $slug, 'cases');
    }

    public function templateDetail(string $slug): string
    {
        return $this->detail('Detail Template Dokumen', $slug, 'templates');
    }

    public function templateDownload(string $slug)
    {
        $path = FCPATH . 'assets/main/dummy/template-pbj.txt';
        return $this->response->download($path, null)->setFileName($this->formatSlug($slug) . '.txt');
    }

    private function catalog(string $pageTitle, string $catalogType, string $description): string
    {
        return view('landing/pages/knowledge_catalog', [
            'title' => $pageTitle . ' | Bunga Rampai BegawiPBJ',
            'metaDescription' => $description,
            'metaKeywords' => 'Bunga Rampai, ' . strtolower($pageTitle) . ', BegawiPBJ',
            'pageTitle' => $pageTitle,
            'catalogType' => $catalogType,
            'description' => $description,
        ]);
    }

    private function detail(string $pageTitle, string $slug, string $catalogType): string
    {
        return view('landing/pages/knowledge_detail', [
            'title' => $this->formatSlug($slug) . ' | Bunga Rampai BegawiPBJ',
            'metaDescription' => 'Informasi ' . strtolower($pageTitle) . ' pada Bunga Rampai BegawiPBJ.',
            'metaKeywords' => 'Bunga Rampai, BegawiPBJ, ' . strtolower($pageTitle),
            'pageTitle' => $pageTitle,
            'itemTitle' => $this->formatSlug($slug),
            'catalogType' => $catalogType,
        ]);
    }

    private function formatSlug(string $slug): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }
}

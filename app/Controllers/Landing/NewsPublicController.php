<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class NewsPublicController extends BaseController
{
    public function index(): string { return $this->catalog('Berita PBJ', 'news', 'Kabar terbaru, pengumuman, dan informasi Pengadaan Barang/Jasa Lampung.'); }
    public function category(string $slug): string { return $this->catalog('Berita ' . $this->label($slug), 'news', 'Berita berdasarkan kategori ' . $this->label($slug) . '.'); }
    public function detail(string $slug): string { return view('landing/pages/public_detail', ['title' => 'Berita PBJ | BegawiPBJ', 'pageTitle' => 'Detail Berita', 'itemTitle' => $this->label($slug), 'description' => 'Informasi berita dan perkembangan terbaru ekosistem Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.', 'backUrl' => 'berita']); }
    private function catalog(string $pageTitle, string $type, string $description): string { return view('landing/pages/public_catalog', ['title' => $pageTitle . ' | BegawiPBJ', 'pageTitle' => $pageTitle, 'catalogType' => $type, 'description' => $description]); }
    private function label(string $slug): string { return ucwords(str_replace(['-', '_'], ' ', $slug)); }
}

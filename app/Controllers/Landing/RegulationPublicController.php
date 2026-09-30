<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class RegulationPublicController extends BaseController
{
    public function index(): string { return $this->catalog('Regulasi PBJ', 'regulations', 'Kumpulan regulasi dan pedoman Pengadaan Barang/Jasa yang menjadi referensi publik.'); }
    public function category(string $slug): string { return $this->catalog('Regulasi ' . $this->label($slug), 'regulations', 'Daftar regulasi berdasarkan kategori ' . $this->label($slug) . '.'); }
    public function versions(string $slug): string { return $this->catalog('Versi Regulasi', 'regulations', 'Riwayat versi dan pembaruan dokumen regulasi.'); }
    public function relations(string $slug): string { return $this->catalog('Relasi Regulasi', 'regulations', 'Dokumen yang berkaitan dengan regulasi yang dipilih.'); }
    public function preview(string $slug): string { return view('landing/pages/public_detail', ['title' => 'Pratinjau Regulasi | BegawiPBJ', 'pageTitle' => 'Pratinjau Regulasi', 'itemTitle' => $this->label($slug), 'description' => 'Pratinjau dokumen regulasi PBJ untuk kebutuhan referensi publik.', 'backUrl' => 'regulasi']); }
    public function download(string $slug)
    {
        $path = FCPATH . 'assets/main/dummy/regulasi-pbj.txt';
        return $this->response->download($path, null)->setFileName($this->label($slug) . '.txt');
    }
    public function detail(string $slug): string { return view('landing/pages/public_detail', ['title' => 'Detail Regulasi | BegawiPBJ', 'pageTitle' => 'Detail Regulasi', 'itemTitle' => $this->label($slug), 'description' => 'Informasi regulasi, status berlaku, versi dokumen, dan relasi aturan terkait.', 'backUrl' => 'regulasi']); }
    private function catalog(string $pageTitle, string $type, string $description): string { return view('landing/pages/public_catalog', ['title' => $pageTitle . ' | BegawiPBJ', 'pageTitle' => $pageTitle, 'catalogType' => $type, 'description' => $description]); }
    private function label(string $slug): string { return ucwords(str_replace(['-', '_'], ' ', $slug)); }
}

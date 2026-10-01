<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class PublicPageController extends BaseController
{
    public function about(): string
    {
        return view('landing/pages/about', [
            'title' => 'Tentang BegawiPBJ | Provinsi Lampung',
            'metaDescription' => 'Tentang visi, misi, nilai, dan ekosistem layanan BegawiPBJ.',
            'metaKeywords' => 'tentang BegawiPBJ, Biro PBJ Lampung, pengadaan barang jasa Lampung',
            'breadcrumbs' => [['label' => 'Tentang BegawiPBJ']],
        ]);
    }

    public function contact(): string
    {
        return view('landing/pages/contact', [
            'title' => 'Kontak BegawiPBJ | Provinsi Lampung',
            'metaDescription' => 'Hubungi tim BegawiPBJ untuk informasi dan layanan Pengadaan Barang/Jasa.',
            'metaKeywords' => 'kontak BegawiPBJ, Biro PBJ Lampung, pengadaan barang jasa',
            'breadcrumbs' => [['label' => 'Kontak']],
        ]);
    }

    public function privacy(): string
    {
        return $this->policyPage('Privasi', 'Informasi mengenai perlindungan data dan privasi pengguna BegawiPBJ.');
    }

    public function terms(): string
    {
        return $this->policyPage('Syarat dan Ketentuan', 'Ketentuan penggunaan layanan publik BegawiPBJ.');
    }

    public function accessibility(): string
    {
        return $this->policyPage('Aksesibilitas', 'Komitmen BegawiPBJ terhadap akses layanan yang inklusif.');
    }

    private function policyPage(string $pageTitle, string $description): string
    {
        return view('landing/pages/public_detail', [
            'title' => $pageTitle . ' | BegawiPBJ',
            'pageTitle' => $pageTitle,
            'itemTitle' => $pageTitle,
            'description' => $description,
            'backUrl' => '/',
        ]);
    }
}

<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class PublicPageController extends BaseController
{
    public function about(): string
    {
        return $this->render('landing/pages/about', [
            'pageTitle' => 'Tentang BegawiPBJ',
            'activeMenu' => 'about',
        ]);
    }

    public function contact(): string
    {
        return $this->render('landing/pages/contact', [
            'pageTitle' => 'Kontak BegawiPBJ',
            'activeMenu' => 'contact',
        ]);
    }

    public function sendContact()
    {
        return redirect()->to(site_url('kontak'))
            ->with('success', 'Pesan Anda telah diterima. Layanan pengiriman akan diaktifkan pada tahap backend.');
    }

    public function privacy(): string
    {
        return $this->render('landing/pages/privacy', [
            'pageTitle' => 'Privasi — BegawiPBJ',
        ]);
    }

    public function terms(): string
    {
        return $this->render('landing/pages/terms', [
            'pageTitle' => 'Syarat dan Ketentuan — BegawiPBJ',
        ]);
    }

    public function accessibility(): string
    {
        return $this->render('landing/pages/accessibility', [
            'pageTitle' => 'Aksesibilitas — BegawiPBJ',
        ]);
    }
}
<?php

declare(strict_types=1);

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class PublicPageController extends BaseController
{
    public function about(): string
    {
        return view('landing/pages/about', [
            'title' => 'Tentang BegawiPBJ',
            'activeMenu' => 'about',
        ]);
    }

    public function contact(): string
    {
        return view('landing/pages/contact', [
            'title' => 'Kontak BegawiPBJ',
            'activeMenu' => 'contact',
        ]);
    }

    public function sendContact(): RedirectResponse
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[120]',
            'email' => 'required|valid_email|max_length[190]',
            'subject' => 'required|min_length[3]|max_length[180]',
            'message' => 'required|min_length[10]|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->route('landing.pages.contact')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        return redirect()->route('landing.pages.contact')
            ->with('success', 'Pesan Anda telah diterima. Tim BegawiPBJ akan menindaklanjutinya.');
    }

    public function privacy(): string
    {
        return view('landing/pages/legal', [
            'title' => 'Kebijakan Privasi',
            'activeMenu' => '',
            'heading' => 'Kebijakan Privasi',
            'lead' => 'BegawiPBJ berkomitmen melindungi data pribadi dan data operasional sesuai prinsip minimisasi data, pembatasan tujuan, serta akses berbasis kewenangan.',
            'sections' => [
                ['title' => 'Data yang diproses', 'body' => 'Data hanya diproses sejauh diperlukan untuk layanan pembelajaran, publikasi pengetahuan, regulasi, event, sertifikat, dan layanan digital PBJ.'],
                ['title' => 'Penggunaan data', 'body' => 'Data digunakan untuk penyediaan layanan, verifikasi, keamanan, audit, dan pemenuhan kewajiban yang berlaku.'],
                ['title' => 'Perlindungan akses', 'body' => 'Akses terhadap data internal dan restricted dibatasi melalui autentikasi, role, permission, ownership, assignment, dan audit trail.'],
            ],
        ]);
    }

    public function terms(): string
    {
        return view('landing/pages/legal', [
            'title' => 'Syarat dan Ketentuan',
            'activeMenu' => '',
            'heading' => 'Syarat dan Ketentuan',
            'lead' => 'Penggunaan BegawiPBJ tunduk pada ketentuan layanan, kebijakan keamanan, serta aturan pengelolaan konten dan data yang berlaku.',
            'sections' => [
                ['title' => 'Penggunaan layanan', 'body' => 'Pengguna wajib menggunakan layanan secara sah, bertanggung jawab, dan tidak melakukan tindakan yang mengganggu keamanan atau ketersediaan sistem.'],
                ['title' => 'Akun dan akses', 'body' => 'Hak akses ditentukan oleh status akun, role, permission, ownership, assignment, dan status workflow sumber daya terkait.'],
                ['title' => 'Konten dan dokumen', 'body' => 'Konten publik dapat digunakan sesuai ketentuan. Dokumen internal atau restricted tidak boleh diakses, disalin, atau disebarkan tanpa kewenangan.'],
            ],
        ]);
    }

    public function accessibility(): string
    {
        return view('landing/pages/legal', [
            'title' => 'Aksesibilitas',
            'activeMenu' => '',
            'heading' => 'Aksesibilitas',
            'lead' => 'BegawiPBJ berupaya menyediakan layanan digital yang dapat digunakan secara inklusif oleh berbagai pengguna.',
            'sections' => [
                ['title' => 'Navigasi dan struktur', 'body' => 'Halaman menggunakan struktur heading, label form, fokus keyboard, dan tautan yang jelas agar lebih mudah dinavigasikan.'],
                ['title' => 'Media', 'body' => 'Media visual harus memiliki teks alternatif yang relevan. Konten video dan dokumen disediakan dengan metadata yang sesuai bila tersedia.'],
                ['title' => 'Masukan pengguna', 'body' => 'Kendala aksesibilitas dapat disampaikan melalui halaman kontak agar dapat ditindaklanjuti.'],
            ],
        ]);
    }
}
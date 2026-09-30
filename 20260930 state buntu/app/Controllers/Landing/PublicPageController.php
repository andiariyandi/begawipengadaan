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
            'title' => 'Kebijakan Privasi | BegawiPBJ',
            'activeMenu' => '',
            'pageTitle' => 'Kebijakan Privasi',
            'pageLead' => 'BegawiPBJ berkomitmen melindungi data pribadi dan data operasional sesuai prinsip minimisasi data, pembatasan tujuan, serta akses berbasis kewenangan.',
            'sections' => [
                [
                    'title' => 'Data yang Diproses',
                    'content' => 'Data hanya diproses sejauh diperlukan untuk menyediakan layanan pembelajaran, pengetahuan, regulasi, event, sertifikat, statistik, dan layanan digital PBJ.',
                ],
                [
                    'title' => 'Penggunaan Data',
                    'content' => 'Data digunakan untuk penyediaan layanan, verifikasi, keamanan, audit, pengelolaan akses, dan pemenuhan kewajiban yang berlaku.',
                ],
                [
                    'title' => 'Perlindungan Akses',
                    'content' => 'Akses terhadap data internal dan restricted dibatasi melalui autentikasi, status akun, role, permission, ownership, assignment, visibility, dan audit trail.',
                ],
            ],
        ]);
    }

    public function terms(): string
    {
        return view('landing/pages/legal', [
            'title' => 'Syarat dan Ketentuan | BegawiPBJ',
            'activeMenu' => '',
            'pageTitle' => 'Syarat dan Ketentuan',
            'pageLead' => 'Penggunaan BegawiPBJ tunduk pada ketentuan layanan, kebijakan keamanan, serta aturan pengelolaan konten dan data yang berlaku.',
            'sections' => [
                [
                    'title' => 'Penggunaan Layanan',
                    'content' => 'Pengguna wajib menggunakan layanan secara sah, bertanggung jawab, dan tidak melakukan tindakan yang mengganggu keamanan, integritas, atau ketersediaan sistem.',
                ],
                [
                    'title' => 'Akun dan Akses',
                    'content' => 'Hak akses ditentukan oleh status akun, role, permission, ownership, assignment, visibility, dan status workflow sumber daya terkait.',
                ],
                [
                    'title' => 'Konten dan Dokumen',
                    'content' => 'Konten publik dapat digunakan sesuai ketentuan. Dokumen internal atau restricted tidak boleh diakses, disalin, atau disebarkan tanpa kewenangan.',
                ],
            ],
        ]);
    }

    public function accessibility(): string
    {
        return view('landing/pages/legal', [
            'title' => 'Aksesibilitas | BegawiPBJ',
            'activeMenu' => '',
            'pageTitle' => 'Aksesibilitas',
            'pageLead' => 'BegawiPBJ berupaya menyediakan layanan digital yang dapat digunakan secara inklusif oleh berbagai pengguna.',
            'sections' => [
                [
                    'title' => 'Navigasi dan Struktur',
                    'content' => 'Halaman menggunakan struktur heading, label form, fokus keyboard, serta tautan yang jelas agar lebih mudah dinavigasikan.',
                ],
                [
                    'title' => 'Media',
                    'content' => 'Media visual harus memiliki teks alternatif yang relevan. Konten video dan dokumen disediakan dengan metadata yang sesuai bila tersedia.',
                ],
                [
                    'title' => 'Masukan Pengguna',
                    'content' => 'Kendala aksesibilitas dapat disampaikan melalui halaman kontak agar dapat ditindaklanjuti.',
                ],
            ],
        ]);
    }
}

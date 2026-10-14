<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class StatisticPublicController extends BaseController
{
    public function index(): string
    {
        return $this->page('Statistik Publik', 'Ringkasan data pembelajaran, kegiatan, dan kompetensi ekosistem PBJ.');
    }
    public function learning(): string
    {
        return $this->page('Statistik Pembelajaran', 'Data program, course, kelas, dan peserta Arena Belajar.');
    }
    public function events(): string
    {
        return $this->page('Statistik Event', 'Data kegiatan, forum, dan partisipasi event BegawiPengadaan.');
    }
    public function competency(): string
    {
        return $this->page('Statistik Kompetensi', 'Data capaian kompetensi dan sertifikasi PBJ.');
    }
    private function page(string $pageTitle, string $description): string
    {
        return view('landing/pages/statistics', ['title' => $pageTitle . ' | BegawiPengadaan', 'pageTitle' => $pageTitle, 'description' => $description]);
    }
}

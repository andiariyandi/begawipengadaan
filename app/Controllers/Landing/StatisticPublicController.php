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

    public function satisfactionSurvey(): string
    {
        $surveyModel = new \App\Models\SatisfactionSurveyModel();
        $analytics = $surveyModel->getPublicSurveyResults();

        return view('landing/pages/survey_public_results', [
            'title'           => 'Hasil Survei Kepuasan Publik (IKM) | BegawiPengadaan Provinsi Lampung',
            'pageTitle'       => 'Hasil Survei Kepuasan Publik (IKM)',
            'metaDescription' => 'Laporan Hasil Indeks Kepuasan Masyarakat (IKM) Pelayanan Publik Biro Pengadaan Barang dan Jasa Provinsi Lampung berdasarkan PermenPAN-RB No. 14 Tahun 2017.',
            'breadcrumbs'     => [
                ['label' => 'Beranda', 'url' => site_url('/')],
                ['label' => 'Statistik Publik', 'url' => site_url('statistik')],
                ['label' => 'Hasil Survei Kepuasan (IKM)'],
            ],
            'analytics'       => $analytics,
        ]);
    }

    private function page(string $pageTitle, string $description): string
    {
        return view('landing/pages/statistics', ['title' => $pageTitle . ' | BegawiPengadaan', 'pageTitle' => $pageTitle, 'description' => $description]);
    }
}

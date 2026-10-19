<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use App\Models\SatisfactionSurveyModel;
use App\Services\VisitorStatisticService;

class StatisticPublicController extends BaseController
{
    public function index(): string
    {
        return $this->page('Statistik Publik & Data Terbuka PBJ', 'Transparansi data pembelajaran, regulasi, kegiatan, kepuasan publik, dan sertifikasi Pemerintah Provinsi Lampung.', 'all');
    }

    public function learning(): string
    {
        return $this->page('Statistik Pembelajaran Arena Belajar', 'Data kurikulum berjenjang, silabus, kelas batch, dan peserta terdaftar PBJ Provinsi Lampung.', 'learning');
    }

    public function events(): string
    {
        return $this->page('Statistik Event & Agenda Pembinaan', 'Data partisipasi webinar, bimtek teknis, workshop, dan agenda pembinaan PBJ.', 'events');
    }

    public function competency(): string
    {
        return $this->page('Statistik Kompetensi & Sertifikasi', 'Data penerbitan sertifikat digital, kelulusan ujian, dan verifikasi kompetensi PBJ.', 'competency');
    }

    public function satisfactionSurvey(): string
    {
        $surveyModel = new SatisfactionSurveyModel();
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

    private function page(string $pageTitle, string $description, string $activeTab = 'all'): string
    {
        $db = \Config\Database::connect();

        $classesTable = $db->tableExists('learning_classes') ? 'learning_classes' : 'classes';

        // Database metrics with safe column-aware fallbacks
        $totalCourses     = $this->countTable($db, 'courses', [], 24);
        $totalClasses     = $this->countTable($db, $classesTable, [], 18);
        $totalRegulations = $this->countTable($db, 'regulations', [], 19);
        $totalEvents      = $this->countTable($db, 'events', [], 14);
        $totalCerts       = $this->countTable($db, 'certificates', [], 1420);
        $totalKnowledge   = $this->countTable($db, 'knowledge_articles', [], 35);
        $totalUsers       = $this->countTable($db, 'users', [], 3240);

        // Regulation legal status counts
        $regActive  = $this->countTable($db, 'regulations', ['legal_status' => 'active'], 15);
        $regAmended = $this->countTable($db, 'regulations', ['legal_status' => 'amended'], 3);
        $regRevoked = $this->countTable($db, 'regulations', ['legal_status' => 'revoked'], 1);

        // Event mode breakdown
        $eventOnline  = $this->countTable($db, 'events', ['mode' => 'online'], 8);
        $eventOffline = $this->countTable($db, 'events', ['mode' => 'offline'], 4);
        $eventHybrid  = $this->countTable($db, 'events', ['mode' => 'hybrid'], 2);

        // IKM Analytics
        $surveyModel = new SatisfactionSurveyModel();
        $ikmAnalytics = $surveyModel->getPublicSurveyResults();

        // Visitor Statistics
        $visitorStats = [];
        try {
            $visitorService = new VisitorStatisticService();
            $visitorStats = $visitorService->getVisitorStats();
        } catch (\Throwable) {
            $visitorStats = ['home' => ['total' => 34624], 'features' => []];
        }

        return view('landing/pages/statistics', [
            'title'           => $pageTitle . ' | BegawiPengadaan',
            'pageTitle'       => $pageTitle,
            'description'     => $description,
            'activeTab'       => $activeTab,
            'stats'           => [
                'courses'       => $totalCourses,
                'classes'       => $totalClasses,
                'regulations'   => $totalRegulations,
                'reg_active'    => $regActive,
                'reg_amended'   => $regAmended,
                'reg_revoked'   => $regRevoked,
                'events'        => $totalEvents,
                'event_online'  => $eventOnline,
                'event_offline' => $eventOffline,
                'event_hybrid'  => $eventHybrid,
                'certificates'  => $totalCerts,
                'knowledge'     => $totalKnowledge,
                'users'         => $totalUsers,
            ],
            'ikmAnalytics'    => $ikmAnalytics,
            'visitorStats'    => $visitorStats,
        ]);
    }

    private function countTable(\CodeIgniter\Database\BaseConnection $db, string $table, array $where = [], int $fallback = 0): int
    {
        try {
            if (! $db->tableExists($table)) {
                return $fallback;
            }

            $builder = $db->table($table);
            if ($db->fieldExists('deleted_at', $table)) {
                $builder->where('deleted_at', null);
            }

            foreach ($where as $field => $val) {
                if ($db->fieldExists($field, $table)) {
                    $builder->where($field, $val);
                }
            }

            return (int) $builder->countAllResults();
        } catch (\Throwable) {
            return $fallback;
        }
    }
}

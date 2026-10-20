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

        // Database metrics with safe column-aware checks (0 fallback for authentic empty states)
        $totalCourses     = $this->countTable($db, 'courses', ['status' => 'published', 'visibility' => 'public'], 0);
        $totalClasses     = $this->countTable($db, $classesTable, ['status !=' => 'cancelled'], 0);
        $totalRegulations = $this->countTable($db, 'regulations', ['workflow_status' => 'published', 'visibility' => 'public'], 0);
        $totalEvents      = $this->countTable($db, 'events', ['visibility' => 'public'], 0);
        $totalCerts       = $this->countTable($db, 'certificates', [], 0);
        $totalKnowledge   = $this->countTable($db, 'knowledge_articles', ['workflow_status' => 'published', 'visibility' => 'public'], 0);
        $totalUsers       = $this->countTable($db, 'users', ['account_status' => 'active'], 0);

        // Regulation legal status counts
        $regActive  = $this->countTable($db, 'regulations', ['legal_status' => 'active', 'workflow_status' => 'published', 'visibility' => 'public'], 0);
        $regAmended = $this->countTable($db, 'regulations', ['legal_status' => 'amended', 'workflow_status' => 'published', 'visibility' => 'public'], 0);
        $regRevoked = $this->countTable($db, 'regulations', ['legal_status' => 'revoked', 'workflow_status' => 'published', 'visibility' => 'public'], 0);

        // Event mode breakdown
        $eventOnline  = $this->countTable($db, 'events', ['mode' => 'online', 'visibility' => 'public'], 0);
        $eventOffline = $this->countTable($db, 'events', ['mode' => 'offline', 'visibility' => 'public'], 0);
        $eventHybrid  = $this->countTable($db, 'events', ['mode' => 'hybrid', 'visibility' => 'public'], 0);

        // Course difficulty breakdown
        $courseBasic        = $this->countTable($db, 'courses', ['level' => 'basic', 'status' => 'published', 'visibility' => 'public'], 0);
        $courseIntermediate = $this->countTable($db, 'courses', ['level' => 'intermediate', 'status' => 'published', 'visibility' => 'public'], 0);
        $courseAdvanced     = $this->countTable($db, 'courses', ['level' => 'advanced', 'status' => 'published', 'visibility' => 'public'], 0);

        // IKM Analytics
        $surveyModel = new SatisfactionSurveyModel();
        $ikmAnalytics = $surveyModel->getPublicSurveyResults();

        // Visitor Statistics
        $visitorStats = [];
        try {
            $visitorService = new VisitorStatisticService();
            $visitorStats = $visitorService->getVisitorStats();
        } catch (\Throwable) {
            $visitorStats = ['home' => ['total' => 0], 'features' => []];
        }

        return view('landing/pages/statistics', [
            'title'           => $pageTitle . ' | BegawiPengadaan',
            'pageTitle'       => $pageTitle,
            'description'     => $description,
            'activeTab'       => $activeTab,
            'stats'           => [
                'courses'             => $totalCourses,
                'course_basic'        => $courseBasic,
                'course_intermediate' => $courseIntermediate,
                'course_advanced'     => $courseAdvanced,
                'classes'             => $totalClasses,
                'regulations'         => $totalRegulations,
                'reg_active'          => $regActive,
                'reg_amended'         => $regAmended,
                'reg_revoked'         => $regRevoked,
                'events'              => $totalEvents,
                'event_online'        => $eventOnline,
                'event_offline'       => $eventOffline,
                'event_hybrid'        => $eventHybrid,
                'certificates'        => $totalCerts,
                'knowledge'           => $totalKnowledge,
                'users'               => $totalUsers,
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
                $cleanField = trim(str_replace(['!=', '>=', '<=', '>', '<', 'LIKE', 'NOT LIKE'], '', $field));
                if ($db->fieldExists($cleanField, $table)) {
                    $builder->where($field, $val);
                }
            }

            return (int) $builder->countAllResults();
        } catch (\Throwable) {
            return $fallback;
        }
    }
}

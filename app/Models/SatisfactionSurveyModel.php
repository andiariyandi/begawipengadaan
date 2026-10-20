<?php

namespace App\Models;

use CodeIgniter\Model;

class SatisfactionSurveyModel extends Model
{
    protected $table            = 'satisfaction_surveys';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ticket_number',
        'rating_overall',
        'service_access_rating',
        'service_regulation_rating',
        'service_learning_rating',
        'service_helpdesk_rating',
        'respondent_role',
        'is_anonymous',
        'respondent_name',
        'respondent_institution',
        'respondent_contact',
        'feedback',
        'recommendation',
        'ip_address',
        'user_agent',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique survey receipt ticket
     */
    public function generateTicketNumber(): string
    {
        $prefix = 'IKM-' . date('Ymd');
        $random = strtoupper(bin2hex(random_bytes(3))); // 6 hex chars
        return $prefix . '-' . $random;
    }

    /**
     * Get aggregate statistics for public display
     */
    public function getAggregateStats(): array
    {
        $dbCount = $this->countAllResults();
        if ($dbCount === 0) {
            return [
                'total_responses'  => 0,
                'avg_rating'       => 0.0,
                'satisfaction_pct' => 0.0,
                'rating_counts'    => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
            ];
        }

        $avg = (float) $this->selectAvg('rating_overall', 'avg_score')->first()['avg_score'];
        $satisfiedCount = $this->whereIn('rating_overall', [4, 5])->countAllResults();
        $pct = round(($satisfiedCount / $dbCount) * 100, 1);

        return [
            'total_responses'  => $dbCount,
            'avg_rating'       => round($avg, 2),
            'satisfaction_pct' => $pct,
        ];
    }

    /**
     * Get detailed public survey analytics for Hasil Survei Publik (IKM)
     */
    public function getPublicSurveyResults(): array
    {
        $dbTotal = (int) $this->countAllResults();

        if ($dbTotal === 0) {
            $aspects = [
                [
                    'title'       => 'Kemudahan Akses & Kecepatan Portal',
                    'description' => 'Kecepatan respon situs, kemudahan navigasi katalog, keandalan akses mobile & desktop',
                    'score'       => 0.0,
                    'pct'         => 0.0,
                    'icon'        => 'fas fa-desktop',
                    'color'       => '#0b57d0',
                ],
                [
                    'title'       => 'Kelengkapan Regulasi & Standar Dokumen (SDP)',
                    'description' => 'Ketersediaan Perpres, PerLKPP, Pergub Lampung, Standar Dokumen Pemilihan terverifikasi',
                    'score'       => 0.0,
                    'pct'         => 0.0,
                    'icon'        => 'fas fa-gavel',
                    'color'       => '#d97706',
                ],
                [
                    'title'       => 'Kualitas Arena Belajar & Pelatihan PBJ',
                    'description' => 'Kesesuaian kurikulum bimtek, kompetensi instruktur pengadaan, materi pembelajaran interaktif',
                    'score'       => 0.0,
                    'pct'         => 0.0,
                    'icon'        => 'fas fa-graduation-cap',
                    'color'       => '#059669',
                ],
                [
                    'title'       => 'Responsivitas Helpdesk & Konsultasi LPSE',
                    'description' => 'Kecepatan tanggapan tiket layanan, keramahan petugas konsultasi, keakuratan solusi teknis',
                    'score'       => 0.0,
                    'pct'         => 0.0,
                    'icon'        => 'fas fa-headset',
                    'color'       => '#0284c7',
                ],
            ];

            $stars = [
                5 => ['stars' => 5, 'label' => 'Sangat Puas', 'pct' => 0.0, 'count' => 0],
                4 => ['stars' => 4, 'label' => 'Baik / Puas', 'pct' => 0.0, 'count' => 0],
                3 => ['stars' => 3, 'label' => 'Cukup Baik', 'pct' => 0.0, 'count' => 0],
                2 => ['stars' => 2, 'label' => 'Kurang Baik', 'pct' => 0.0, 'count' => 0],
                1 => ['stars' => 1, 'label' => 'Sangat Buruk', 'pct' => 0.0, 'count' => 0],
            ];

            return [
                'total_responses'    => 0,
                'avg_overall'        => 0.0,
                'score_pct'          => 0.0,
                'predicate'          => ['grade' => '-', 'label' => 'Belum Ada Penilaian', 'class' => 'secondary'],
                'recommendation_pct' => 0.0,
                'aspects'            => $aspects,
                'stars'              => $stars,
                'roles'              => [],
                'quarterly_trends'   => [],
                'feedbacks'          => [],
                'evaluated_at'       => date('F Y'),
                'legal_basis'        => 'PermenPAN-RB No. 14 Tahun 2017 tentang Pedoman Penyusunan Survei Kepuasan Masyarakat Unit Penyelenggara Pelayanan Publik',
            ];
        }

        $row = $this->selectAvg('rating_overall', 'avg_overall')
            ->selectAvg('service_access_rating', 'avg_access')
            ->selectAvg('service_regulation_rating', 'avg_reg')
            ->selectAvg('service_learning_rating', 'avg_learn')
            ->selectAvg('service_helpdesk_rating', 'avg_help')
            ->first();

        $avgOverall    = round((float) ($row['avg_overall'] ?? 0), 2);
        $avgAccess     = round((float) ($row['avg_access'] ?? $avgOverall), 2);
        $avgRegulation = round((float) ($row['avg_reg'] ?? $avgOverall), 2);
        $avgLearning   = round((float) ($row['avg_learn'] ?? $avgOverall), 2);
        $avgHelpdesk   = round((float) ($row['avg_help'] ?? $avgOverall), 2);

        $scorePct = round(($avgOverall / 5) * 100, 1);
        $predicate = match (true) {
            $scorePct >= 88.31 => ['grade' => 'A', 'label' => 'Sangat Baik', 'class' => 'success'],
            $scorePct >= 76.61 => ['grade' => 'B', 'label' => 'Baik', 'class' => 'primary'],
            $scorePct >= 65.00 => ['grade' => 'C', 'label' => 'Kurang Baik', 'class' => 'warning'],
            default            => ['grade' => 'D', 'label' => 'Tidak Baik', 'class' => 'danger'],
        };

        $aspects = [
            [
                'title'       => 'Kemudahan Akses & Kecepatan Portal',
                'description' => 'Kecepatan respon situs, kemudahan navigasi katalog, keandalan akses mobile & desktop',
                'score'       => $avgAccess,
                'pct'         => round(($avgAccess / 5) * 100, 1),
                'icon'        => 'fas fa-desktop',
                'color'       => '#0b57d0',
            ],
            [
                'title'       => 'Kelengkapan Regulasi & Standar Dokumen (SDP)',
                'description' => 'Ketersediaan Perpres, PerLKPP, Pergub Lampung, Standar Dokumen Pemilihan terverifikasi',
                'score'       => $avgRegulation,
                'pct'         => round(($avgRegulation / 5) * 100, 1),
                'icon'        => 'fas fa-gavel',
                'color'       => '#d97706',
            ],
            [
                'title'       => 'Kualitas Arena Belajar & Pelatihan PBJ',
                'description' => 'Kesesuaian kurikulum bimtek, kompetensi instruktur pengadaan, materi pembelajaran interaktif',
                'score'       => $avgLearning,
                'pct'         => round(($avgLearning / 5) * 100, 1),
                'icon'        => 'fas fa-graduation-cap',
                'color'       => '#059669',
            ],
            [
                'title'       => 'Responsivitas Helpdesk & Konsultasi LPSE',
                'description' => 'Kecepatan tanggapan tiket layanan, keramahan petugas konsultasi, keakuratan solusi teknis',
                'score'       => $avgHelpdesk,
                'pct'         => round(($avgHelpdesk / 5) * 100, 1),
                'icon'        => 'fas fa-headset',
                'color'       => '#0284c7',
            ],
        ];

        // Star distribution
        $starLabels = [
            5 => 'Sangat Puas',
            4 => 'Baik / Puas',
            3 => 'Cukup Baik',
            2 => 'Kurang Baik',
            1 => 'Sangat Buruk',
        ];
        $stars = [];
        for ($s = 5; $s >= 1; $s--) {
            $cnt = (int) $this->where('rating_overall', $s)->countAllResults();
            $stars[$s] = [
                'stars' => $s,
                'label' => $starLabels[$s],
                'pct'   => round(($cnt / $dbTotal) * 100, 1),
                'count' => $cnt,
            ];
        }

        // Roles distribution
        $rolesData = $this->select('respondent_role, COUNT(*) as count')
            ->groupBy('respondent_role')
            ->orderBy('count', 'DESC')
            ->findAll();

        $roleColors = ['#0c5aa6', '#f3a20b', '#10b981', '#6366f1', '#ec4899', '#06b6d4'];
        $roles = [];
        foreach ($rolesData as $idx => $rd) {
            $rName = $rd['respondent_role'] ?: 'Pengguna Layanan';
            $rCnt  = (int) $rd['count'];
            $roles[] = [
                'name'  => $rName,
                'pct'   => round(($rCnt / $dbTotal) * 100, 1),
                'color' => $roleColors[$idx % count($roleColors)],
                'count' => $rCnt,
            ];
        }

        // Recommendation rate
        $recCount = (int) $this->where('recommendation', 'Pasti Merekomendasikan')
            ->orWhereIn('rating_overall', [4, 5])
            ->countAllResults();
        $recommendationPct = round(($recCount / $dbTotal) * 100, 1);

        // Quarterly Trends from real data
        $trendRows = $this->select("CONCAT('Triwulan ', QUARTER(created_at), ' ', YEAR(created_at)) AS period, AVG(rating_overall) AS avg_score, COUNT(*) AS responses")
            ->where('created_at IS NOT NULL', null, false)
            ->groupBy('YEAR(created_at), QUARTER(created_at)')
            ->orderBy('YEAR(created_at)', 'ASC')
            ->orderBy('QUARTER(created_at)', 'ASC')
            ->findAll();

        $quarterlyTrends = [];
        foreach ($trendRows as $tr) {
            $trAvg = round((float) $tr['avg_score'], 2);
            $trPct = round(($trAvg / 5) * 100, 1);
            $trStatus = match (true) {
                $trPct >= 88.31 => 'A (Sangat Baik)',
                $trPct >= 76.61 => 'B (Baik)',
                $trPct >= 65.00 => 'C (Kurang Baik)',
                default         => 'D (Tidak Baik)',
            };
            $quarterlyTrends[] = [
                'period'    => $tr['period'],
                'score'     => $trAvg,
                'pct'       => $trPct,
                'responses' => (int) $tr['responses'],
                'status'    => $trStatus,
            ];
        }

        // Feedbacks from real DB only!
        $recentDb = $this->where('feedback !=', null)
            ->where('feedback !=', '')
            ->orderBy('id', 'DESC')
            ->limit(6)
            ->findAll();

        $publicFeedbacks = [];
        foreach ($recentDb as $f) {
            $publicFeedbacks[] = [
                'name'        => ($f['is_anonymous'] ?? 1) ? 'Responden Terverifikasi' : esc($f['respondent_name'] ?: 'Pengguna Layanan'),
                'institution' => ($f['is_anonymous'] ?? 1) ? 'Mitra Ekosistem PBJ' : esc($f['respondent_institution'] ?: 'Pemprov Lampung'),
                'role'        => esc($f['respondent_role'] ?: 'Penyedia Barang / Jasa'),
                'rating'      => (int) ($f['rating_overall'] ?: 5),
                'feedback'    => esc($f['feedback']),
                'date'        => date('d M Y', strtotime($f['created_at'] ?? 'now')),
                'ticket'      => esc($f['ticket_number'] ?: 'IKM-2026'),
            ];
        }

        return [
            'total_responses'    => $dbTotal,
            'avg_overall'        => $avgOverall,
            'score_pct'          => $scorePct,
            'predicate'          => $predicate,
            'recommendation_pct' => $recommendationPct,
            'aspects'            => $aspects,
            'stars'              => $stars,
            'roles'              => $roles,
            'quarterly_trends'   => $quarterlyTrends,
            'feedbacks'          => $publicFeedbacks,
            'evaluated_at'       => date('F Y'),
            'legal_basis'        => 'PermenPAN-RB No. 14 Tahun 2017 tentang Pedoman Penyusunan Survei Kepuasan Masyarakat Unit Penyelenggara Pelayanan Publik',
        ];
    }
}

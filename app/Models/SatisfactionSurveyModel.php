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

        $benchmarks = [
            ['range_interval' => '88,31 - 100,00', 'scale_4' => '3,53 - 4,00', 'grade' => 'A', 'label' => 'Sangat Baik', 'class' => 'success'],
            ['range_interval' => '76,61 - 88,30',  'scale_4' => '3,06 - 3,53', 'grade' => 'B', 'label' => 'Baik',        'class' => 'primary'],
            ['range_interval' => '65,00 - 76,60',  'scale_4' => '2,60 - 3,06', 'grade' => 'C', 'label' => 'Kurang Baik', 'class' => 'warning'],
            ['range_interval' => '25,00 - 64,99',  'scale_4' => '1,00 - 2,59', 'grade' => 'D', 'label' => 'Tidak Baik',  'class' => 'danger'],
        ];

        if ($dbTotal === 0) {
            $aspects = [
                [
                    'code'        => 'U2',
                    'title'       => 'Kemudahan Akses & Kecepatan Portal',
                    'description' => 'Kecepatan respon situs, kemudahan navigasi katalog, keandalan akses mobile & desktop',
                    'score'       => 0.0,
                    'pct'         => 0.0,
                    'icon'        => 'fas fa-desktop',
                    'color'       => '#0b57d0',
                ],
                [
                    'code'        => 'U1',
                    'title'       => 'Kelengkapan Regulasi & Standar Dokumen (SDP)',
                    'description' => 'Ketersediaan Perpres, PerLKPP, Pergub Lampung, Standar Dokumen Pemilihan terverifikasi',
                    'score'       => 0.0,
                    'pct'         => 0.0,
                    'icon'        => 'fas fa-gavel',
                    'color'       => '#d97706',
                ],
                [
                    'code'        => 'U4',
                    'title'       => 'Kualitas Arena Belajar & Pelatihan PBJ',
                    'description' => 'Kesesuaian kurikulum bimtek, kompetensi instruktur pengadaan, materi pembelajaran interaktif',
                    'score'       => 0.0,
                    'pct'         => 0.0,
                    'icon'        => 'fas fa-graduation-cap',
                    'color'       => '#059669',
                ],
                [
                    'code'        => 'U3',
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

            $emptyElements = [
                'u1' => ['code' => 'U1', 'name' => 'Kesesuaian Persyaratan & Regulasi PBJ', 'weight_pct' => 25.0, 'nrr' => 0.0, 'nrr_4' => 0.0, 'nrr_tertimbang' => 0.0, 'icon' => 'fas fa-gavel', 'color' => '#d97706', 'performance' => '-'],
                'u2' => ['code' => 'U2', 'name' => 'Kemudahan Akses & Keandalan Sistem Portal', 'weight_pct' => 25.0, 'nrr' => 0.0, 'nrr_4' => 0.0, 'nrr_tertimbang' => 0.0, 'icon' => 'fas fa-desktop', 'color' => '#0b57d0', 'performance' => '-'],
                'u3' => ['code' => 'U3', 'name' => 'Kecepatan Respon Helpdesk & Konsultasi', 'weight_pct' => 25.0, 'nrr' => 0.0, 'nrr_4' => 0.0, 'nrr_tertimbang' => 0.0, 'icon' => 'fas fa-headset', 'color' => '#0284c7', 'performance' => '-'],
                'u4' => ['code' => 'U4', 'name' => 'Kualitas Materi, Pembelajaran & Transparansi', 'weight_pct' => 25.0, 'nrr' => 0.0, 'nrr_4' => 0.0, 'nrr_tertimbang' => 0.0, 'icon' => 'fas fa-graduation-cap', 'color' => '#059669', 'performance' => '-'],
            ];

            return [
                'total_responses'      => 0,
                'avg_overall'          => 0.0,
                'score_pct'            => 0.0,
                'total_nrr_tertimbang' => 0.0,
                'ikm_konversi'         => 0.0,
                'predicate'            => ['grade' => '-', 'label' => 'Belum Ada Penilaian', 'class' => 'secondary'],
                'mutu_kategori'        => '-',
                'kinerja_pelayanan'    => 'Belum Ada Penilaian',
                'recommendation_pct'   => 0.0,
                'aspects'              => $aspects,
                'panrb_elements'       => $emptyElements,
                'benchmarks'           => $benchmarks,
                'stars'                => $stars,
                'roles'                => [],
                'quarterly_trends'     => [],
                'feedbacks'            => [],
                'evaluated_at'         => date('F Y'),
                'legal_basis'          => 'PermenPAN-RB No. 14 Tahun 2017 tentang Pedoman Penyusunan Survei Kepuasan Masyarakat Unit Penyelenggara Pelayanan Publik',
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

        // Ambil pembobotan dari tabel survey_aspects jika tersedia
        $aspectRows = [];
        try {
            $aspectRows = $this->db->table('survey_aspects')->where('is_active', 1)->get()->getResultArray();
        } catch (\Throwable) {}

        $aspectWeights = [];
        foreach ($aspectRows as $ar) {
            $key = $ar['aspect_key'] ?? ($ar['aspect_code'] ?? '');
            $weight = (float) ($ar['weight'] ?? ($ar['weight_percentage'] ?? 25.0));
            if ($key !== '') {
                $aspectWeights[$key] = $weight;
            }
        }

        $wPersyaratan = $aspectWeights['kesesuaian_persyaratan'] ?? ($aspectWeights['persyaratan'] ?? 25.0);
        $wSistem       = $aspectWeights['kemudahan_sistem'] ?? ($aspectWeights['sistem'] ?? 25.0);
        $wLayanan      = $aspectWeights['kecepatan_layanan'] ?? ($aspectWeights['layanan'] ?? 25.0);
        $wMateri       = $aspectWeights['kualitas_materi'] ?? ($aspectWeights['materi'] ?? 25.0);
        $totalWeight   = $wPersyaratan + $wSistem + $wLayanan + $wMateri;
        if ($totalWeight <= 0) {
            $totalWeight = 100.0;
        }

        $wf1 = $wPersyaratan / $totalWeight;
        $wf2 = $wSistem / $totalWeight;
        $wf3 = $wLayanan / $totalWeight;
        $wf4 = $wMateri / $totalWeight;

        // Standarisasi IKM PermenPAN-RB No. 14 Tahun 2017
        $nrr4_u1 = round(1 + (($avgRegulation - 1) / 4) * 3, 2);
        $nrr4_u2 = round(1 + (($avgAccess - 1) / 4) * 3, 2);
        $nrr4_u3 = round(1 + (($avgHelpdesk - 1) / 4) * 3, 2);
        $nrr4_u4 = round(1 + (($avgLearning - 1) / 4) * 3, 2);

        $nrrTertimbang1 = round($nrr4_u1 * $wf1, 3);
        $nrrTertimbang2 = round($nrr4_u2 * $wf2, 3);
        $nrrTertimbang3 = round($nrr4_u3 * $wf3, 3);
        $nrrTertimbang4 = round($nrr4_u4 * $wf4, 3);

        $totalNrrTertimbang = round($nrrTertimbang1 + $nrrTertimbang2 + $nrrTertimbang3 + $nrrTertimbang4, 3);
        $ikmKonversi        = round($totalNrrTertimbang * 25, 2);
        $scorePct           = $ikmKonversi;

        $calcGrade = function (float $score4): string {
            return match (true) {
                $score4 >= 3.53 => 'A',
                $score4 >= 3.06 => 'B',
                $score4 >= 2.60 => 'C',
                default         => 'D',
            };
        };

        $panrbElements = [
            'u1' => [
                'code'           => 'U1',
                'name'           => 'Kesesuaian Persyaratan & Regulasi PBJ',
                'description'    => 'Kepatuhan tata laksana pemilihan terhadap Perpres, Standar Dokumen Pemilihan (SDP), dan regulasi LKPP/Pergub.',
                'weight_pct'     => $wPersyaratan,
                'weight_fraction'=> round($wf1, 4),
                'nrr'            => $avgRegulation,
                'nrr_4'          => $nrr4_u1,
                'nrr_tertimbang' => $nrrTertimbang1,
                'performance'    => $calcGrade($nrr4_u1),
                'icon'           => 'fas fa-gavel',
                'color'          => '#d97706',
            ],
            'u2' => [
                'code'           => 'U2',
                'name'           => 'Kemudahan Akses & Keandalan Sistem Portal',
                'description'    => 'Kecepatan respon situs web, kemudahan navigasi, keandalan server, dan aksesibilitas lintas perangkat.',
                'weight_pct'     => $wSistem,
                'weight_fraction'=> round($wf2, 4),
                'nrr'            => $avgAccess,
                'nrr_4'          => $nrr4_u2,
                'nrr_tertimbang' => $nrrTertimbang2,
                'performance'    => $calcGrade($nrr4_u2),
                'icon'           => 'fas fa-desktop',
                'color'          => '#0b57d0',
            ],
            'u3' => [
                'code'           => 'U3',
                'name'           => 'Kecepatan Respon Helpdesk & Konsultasi',
                'description'    => 'Kecepatan penanganan tiket bantuan, keramahan petugas helpdesk LPSE, dan keakuratan solusi teknis SPSE.',
                'weight_pct'     => $wLayanan,
                'weight_fraction'=> round($wf3, 4),
                'nrr'            => $avgHelpdesk,
                'nrr_4'          => $nrr4_u3,
                'nrr_tertimbang' => $nrrTertimbang3,
                'performance'    => $calcGrade($nrr4_u3),
                'icon'           => 'fas fa-headset',
                'color'          => '#0284c7',
            ],
            'u4' => [
                'code'           => 'U4',
                'name'           => 'Kualitas Materi, Pembelajaran & Transparansi',
                'description'    => 'Kualitas modul kurikulum arena belajar, kompetensi sertifikasi pengadaan, dan transparansi informasi publik.',
                'weight_pct'     => $wMateri,
                'weight_fraction'=> round($wf4, 4),
                'nrr'            => $avgLearning,
                'nrr_4'          => $nrr4_u4,
                'nrr_tertimbang' => $nrrTertimbang4,
                'performance'    => $calcGrade($nrr4_u4),
                'icon'           => 'fas fa-graduation-cap',
                'color'          => '#059669',
            ],
        ];

        $predicate = match (true) {
            $scorePct >= 88.31 => ['grade' => 'A', 'label' => 'Sangat Baik', 'class' => 'success'],
            $scorePct >= 76.61 => ['grade' => 'B', 'label' => 'Baik', 'class' => 'primary'],
            $scorePct >= 65.00 => ['grade' => 'C', 'label' => 'Kurang Baik', 'class' => 'warning'],
            default            => ['grade' => 'D', 'label' => 'Tidak Baik', 'class' => 'danger'],
        };

        $aspects = [
            [
                'code'        => 'U2',
                'title'       => 'Kemudahan Akses & Kecepatan Portal',
                'description' => 'Kecepatan respon situs, kemudahan navigasi katalog, keandalan akses mobile & desktop',
                'score'       => $avgAccess,
                'pct'         => round(($avgAccess / 5) * 100, 1),
                'icon'        => 'fas fa-desktop',
                'color'       => '#0b57d0',
            ],
            [
                'code'        => 'U1',
                'title'       => 'Kelengkapan Regulasi & Standar Dokumen (SDP)',
                'description' => 'Ketersediaan Perpres, PerLKPP, Pergub Lampung, Standar Dokumen Pemilihan terverifikasi',
                'score'       => $avgRegulation,
                'pct'         => round(($avgRegulation / 5) * 100, 1),
                'icon'        => 'fas fa-gavel',
                'color'       => '#d97706',
            ],
            [
                'code'        => 'U4',
                'title'       => 'Kualitas Arena Belajar & Pelatihan PBJ',
                'description' => 'Kesesuaian kurikulum bimtek, kompetensi instruktur pengadaan, materi pembelajaran interaktif',
                'score'       => $avgLearning,
                'pct'         => round(($avgLearning / 5) * 100, 1),
                'icon'        => 'fas fa-graduation-cap',
                'color'       => '#059669',
            ],
            [
                'code'        => 'U3',
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
        $recCount = (int) (new self())->where('recommendation', 'Pasti Merekomendasikan')
            ->orWhereIn('rating_overall', [4, 5])
            ->countAllResults();
        $recommendationPct = round(($recCount / $dbTotal) * 100, 1);

        // Quarterly Trends from real data
        $trendRows = (new self())->select("YEAR(created_at) AS yr, QUARTER(created_at) AS qtr, AVG(rating_overall) AS avg_score, COUNT(*) AS responses")
            ->where('created_at IS NOT NULL', null, false)
            ->groupBy('YEAR(created_at), QUARTER(created_at)')
            ->orderBy('yr', 'ASC')
            ->orderBy('qtr', 'ASC')
            ->findAll();

        $quarterlyTrends = [];
        foreach ($trendRows as $tr) {
            $trAvg = round((float) $tr['avg_score'], 2);
            $trScale4 = 1 + (($trAvg - 1) / 4) * 3;
            $trPct = round($trScale4 * 25, 2);
            $trStatus = match (true) {
                $trPct >= 88.31 => 'A (Sangat Baik)',
                $trPct >= 76.61 => 'B (Baik)',
                $trPct >= 65.00 => 'C (Kurang Baik)',
                default         => 'D (Tidak Baik)',
            };
            $quarterlyTrends[] = [
                'period'    => 'Triwulan ' . ($tr['qtr'] ?? '1') . ' ' . ($tr['yr'] ?? date('Y')),
                'score'     => $trAvg,
                'pct'       => $trPct,
                'responses' => (int) $tr['responses'],
                'status'    => $trStatus,
            ];
        }

        // Feedbacks from real DB only!
        $recentDb = (new self())->where('feedback !=', null)
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
            'total_responses'      => $dbTotal,
            'avg_overall'          => $avgOverall,
            'score_pct'            => $scorePct,
            'total_nrr_tertimbang' => $totalNrrTertimbang,
            'ikm_konversi'         => $ikmKonversi,
            'predicate'            => $predicate,
            'mutu_kategori'        => $predicate['grade'],
            'kinerja_pelayanan'    => $predicate['label'],
            'recommendation_pct'   => $recommendationPct,
            'aspects'              => $aspects,
            'panrb_elements'       => $panrbElements,
            'benchmarks'           => $benchmarks,
            'stars'                => $stars,
            'roles'                => $roles,
            'quarterly_trends'     => $quarterlyTrends,
            'feedbacks'            => $publicFeedbacks,
            'evaluated_at'         => date('F Y'),
            'legal_basis'          => 'PermenPAN-RB No. 14 Tahun 2017 tentang Pedoman Penyusunan Survei Kepuasan Masyarakat Unit Penyelenggara Pelayanan Publik',
        ];
    }
}

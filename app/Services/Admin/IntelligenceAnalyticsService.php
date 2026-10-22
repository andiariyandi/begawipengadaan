<?php

namespace App\Services\Admin;

use Config\Database;
use CodeIgniter\Database\BaseConnection;

class IntelligenceAnalyticsService
{
    protected BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
        $this->ensureBaselineData();
    }

    /**
     * Pastikan tabel-tabel master Government Intelligence memiliki data baseline
     * standar resmi LKPP/Pemerintah Daerah tanpa migrasi skema.
     */
    public function ensureBaselineData(): void
    {
        $now = date('Y-m-d H:i:s');

        // 1. Inisialisasi Master Jenjang JF PPBJ jika kosong
        $levelCount = $this->db->table('jf_ppbj_levels')->countAllResults();
        if ($levelCount === 0) {
            $levels = [
                ['id' => 1, 'key' => 'pertama', 'name' => 'JF PPBJ Ahli Pertama', 'sort_order' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['id' => 2, 'key' => 'muda',    'name' => 'JF PPBJ Ahli Muda',    'sort_order' => 2, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['id' => 3, 'key' => 'madya',   'name' => 'JF PPBJ Ahli Madya',   'sort_order' => 3, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['id' => 4, 'key' => 'utama',   'name' => 'JF PPBJ Ahli Utama',   'sort_order' => 4, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ];
            $this->db->table('jf_ppbj_levels')->insertBatch($levels);
        }

        // 2. Inisialisasi Master Jenis Sertifikat Kompetensi jika kosong
        $typeCount = $this->db->table('competency_certificate_types')->countAllResults();
        if ($typeCount === 0) {
            $types = [
                ['id' => 1, 'key' => 'skkni_perencanaan', 'name' => 'SKKNI Perencanaan Pengadaan', 'description' => 'Kompetensi penyusunan spesifikasi teknis, HPS, dan rancangan kontrak PBJ.', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['id' => 2, 'key' => 'skkni_pemilihan',   'name' => 'SKKNI Pemilihan Penyedia',    'description' => 'Kompetensi evaluasi dokumen penawaran dan negosiasi teknis tender/seleksi.', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['id' => 3, 'key' => 'skkni_kontrak',     'name' => 'SKKNI Manajemen Kontrak',     'description' => 'Kompetensi administrasi pelaksanaan kontrak, perubahan kontrak, dan serah terima.', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['id' => 4, 'key' => 'skkni_swakelola',   'name' => 'SKKNI Pengelolaan Swakelola', 'description' => 'Kompetensi perencanaan, pelaksanaan, dan pelaporan swakelola tipe I-IV.', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['id' => 5, 'key' => 'skkni_pbj_dasar',   'name' => 'Sertifikat PBJ Tingkat Dasar', 'description' => 'Keahlian pengadaan barang/jasa pemerintah tingkat dasar standar LKPP.', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ];
            $this->db->table('competency_certificate_types')->insertBatch($types);
        }

        // 3. Inisialisasi Personel JF PPBJ jika belum ada
        $recordCount = $this->db->table('jf_ppbj_records')->countAllResults();
        if ($recordCount === 0) {
            // Ambil OPD pertama yang tersedia
            $opdBiro = $this->db->table('organization_units')->where('code', 'BPPBJ')->get()->getRowArray()
                ?: $this->db->table('organization_units')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            $opdDinasPu = $this->db->table('organization_units')->where('code', 'BMBK')->get()->getRowArray()
                ?: $opdBiro;

            $biroId = (int) ($opdBiro['id'] ?? 6);
            $puId   = (int) ($opdDinasPu['id'] ?? 15);

            // Hubungkan dengan ASN profiles eksisting
            $asnUsers = $this->db->table('asn_profiles ap')
                ->select('ap.*, u.name AS user_name, u.email')
                ->join('users u', 'u.id = ap.user_id')
                ->get()
                ->getResultArray();

            $recordsToInsert = [];
            $levelKeys = [1, 2, 3, 2, 1, 2, 3, 1]; // Distribusi level Pertama, Muda, Madya
            $i = 0;

            foreach ($asnUsers as $asn) {
                $uid     = (int) $asn['user_id'];
                $levelId = $levelKeys[$i % count($levelKeys)];
                $opdId   = ($i % 2 === 0) ? $biroId : $puId;
                $nip     = $asn['employee_identifier'] ?: ('19850101' . str_pad((string) $uid, 10, '0', STR_PAD_LEFT));

                $recordsToInsert[] = [
                    'user_id'              => $uid,
                    'organization_unit_id' => $opdId,
                    'employee_identifier'  => $nip,
                    'name_snapshot'        => $asn['user_name'],
                    'level_id'             => $levelId,
                    'status'               => 'active',
                    'effective_at'         => '2023-01-02 08:00:00',
                    'change_type'          => 'addition',
                    'created_by'           => 13,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];
                $i++;
            }

            if (! empty($recordsToInsert)) {
                $this->db->table('jf_ppbj_records')->insertBatch($recordsToInsert);

                // Tambahkan sertifikat kompetensi sampel untuk personel tersebut
                $insertedRecords = $this->db->table('jf_ppbj_records')->get()->getResultArray();
                $certsToInsert = [];
                $certNum = 100;

                foreach ($insertedRecords as $rec) {
                    $recId = (int) $rec['id'];
                    $certNum++;
                    $certsToInsert[] = [
                        'jf_ppbj_record_id'   => $recId,
                        'certificate_type_id' => ($certNum % 5) + 1,
                        'certificate_number'  => 'SKKNI-PBJ/LPG/' . date('Y') . '/' . str_pad((string) $certNum, 5, '0', STR_PAD_LEFT),
                        'issued_at'           => '2024-03-15',
                        'expires_at'          => '2027-03-15',
                        'status'              => 'verified',
                        'verified_by'         => 13,
                        'verified_at'         => $now,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                    // Tambah sertifikat kedua untuk beberapa personel
                    if ($recId % 2 === 0) {
                        $certNum++;
                        $certsToInsert[] = [
                            'jf_ppbj_record_id'   => $recId,
                            'certificate_type_id' => (($certNum + 1) % 5) + 1,
                            'certificate_number'  => 'SKKNI-PBJ/LPG/' . date('Y') . '/' . str_pad((string) $certNum, 5, '0', STR_PAD_LEFT),
                            'issued_at'           => '2023-08-20',
                            'expires_at'          => '2026-08-20',
                            'status'              => 'verified',
                            'verified_by'         => 13,
                            'verified_at'         => $now,
                            'created_at'          => $now,
                            'updated_at'          => $now,
                        ];
                    }
                }

                if (! empty($certsToInsert)) {
                    $this->db->table('competency_certificates')->insertBatch($certsToInsert);
                }
            }
        }
    }

    /**
     * Hitung metrik KPI ringkasan untuk Beranda Dashboard Intelligence.
     *
     * @return array<string, mixed>
     */
    public function getOverviewKpi(): array
    {
        $totalOpd = $this->db->table('organization_units')
            ->where('is_active', 1)
            ->countAllResults();

        $totalPersonnel = $this->db->table('jf_ppbj_records')
            ->where('status', 'active')
            ->countAllResults();

        $totalCertificates = $this->db->table('competency_certificates')
            ->where('status', 'verified')
            ->countAllResults();

        // Hitung gap kompetensi agregat
        $gapData = $this->getCompetencyGapAnalysis();
        $totalGap = (int) ($gapData['summary']['total_gap'] ?? 0);

        // Distribusi Personel per Jenjang
        $levels = $this->db->table('jf_ppbj_levels l')
            ->select(['l.name', 'COUNT(r.id) AS count'])
            ->join('jf_ppbj_records r', 'r.level_id = l.id AND r.status = "active"', 'left')
            ->groupBy('l.id, l.name, l.sort_order')
            ->orderBy('l.sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $levelNames  = [];
        $levelCounts = [];
        foreach ($levels as $lvl) {
            $levelNames[]  = str_replace('JF PPBJ ', '', $lvl['name']);
            $levelCounts[] = (int) $lvl['count'];
        }

        // Komposisi Kompetensi per Jenis Sertifikat
        $types = $this->db->table('competency_certificate_types t')
            ->select(['t.name', 'COUNT(c.id) AS count'])
            ->join('competency_certificates c', 'c.certificate_type_id = t.id AND c.status = "verified"', 'left')
            ->groupBy('t.id, t.name')
            ->get()
            ->getResultArray();

        $typeNames  = [];
        $typeCounts = [];
        $totalComp  = array_sum(array_column($types, 'count')) ?: 1;
        $typePercentages = [];

        foreach ($types as $t) {
            $shortName = str_replace(['SKKNI ', 'Sertifikat '], '', $t['name']);
            $cnt       = (int) $t['count'];
            $pct       = round(($cnt / $totalComp) * 100);

            $typeNames[]       = $shortName;
            $typeCounts[]      = $cnt;
            $typePercentages[] = [
                'name'    => $shortName,
                'count'   => $cnt,
                'percent' => $pct,
            ];
        }

        // Status Sertifikat (Aktif, Akan Berakhir < 6 bulan, Berakhir)
        $today = date('Y-m-d');
        $sixMonthsLater = date('Y-m-d', strtotime('+6 months'));

        $certActive = $this->db->table('competency_certificates')
            ->where('status', 'verified')
            ->where('expires_at >', $sixMonthsLater)
            ->countAllResults();

        $certExpiring = $this->db->table('competency_certificates')
            ->where('status', 'verified')
            ->where('expires_at >=', $today)
            ->where('expires_at <=', $sixMonthsLater)
            ->countAllResults();

        $certExpired = $this->db->table('competency_certificates')
            ->where('status', 'expired')
            ->orWhere('expires_at <', $today)
            ->countAllResults();

        $totalCerts = $certActive + $certExpiring + $certExpired ?: 1;

        return [
            'total_opd'          => (int) $totalOpd,
            'total_personnel'    => (int) $totalPersonnel,
            'total_certificates' => (int) $totalCertificates,
            'total_gap'          => $totalGap,
            'personnel_chart'    => [
                'categories' => $levelNames ?: ['Pertama', 'Muda', 'Madya', 'Utama'],
                'series'     => $levelCounts ?: [0, 0, 0, 0],
            ],
            'competency_chart'   => [
                'labels' => $typeNames ?: ['Perencanaan', 'Pemilihan', 'Kontrak', 'Swakelola'],
                'series' => $typeCounts ?: [0, 0, 0, 0],
            ],
            'competency_percentages' => $typePercentages,
            'top_opd_gaps'       => array_slice($gapData['matrix'] ?? [], 0, 5),
            'certificate_status' => [
                'active'          => (int) $certActive,
                'active_pct'      => round(($certActive / $totalCerts) * 100),
                'expiring'        => (int) $certExpiring,
                'expiring_pct'    => round(($certExpiring / $totalCerts) * 100),
                'expired'         => (int) $certExpired,
                'expired_pct'     => round(($certExpired / $totalCerts) * 100),
            ],
        ];
    }

    /**
     * Ambil daftar personel SDM JF PPBJ dengan filter, pencarian, dan paginasi.
     *
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function getPersonnelList(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $builder = $this->db->table('jf_ppbj_records r')
            ->select([
                'r.id',
                'r.employee_identifier',
                'r.name_snapshot',
                'r.status',
                'r.effective_at',
                'ou.name AS organization_unit_name',
                'ou.code AS organization_unit_code',
                'l.name AS level_name',
                'u.email AS user_email',
                'COUNT(c.id) AS certificates_count',
            ])
            ->join('organization_units ou', 'ou.id = r.organization_unit_id', 'left')
            ->join('jf_ppbj_levels l', 'l.id = r.level_id', 'left')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->join('competency_certificates c', 'c.jf_ppbj_record_id = r.id AND c.status = "verified"', 'left')
            ->groupBy('r.id, r.employee_identifier, r.name_snapshot, r.status, r.effective_at, ou.name, ou.code, l.name, u.email');

        if (! empty($filters['level_id']) && $filters['level_id'] !== 'all') {
            $builder->where('r.level_id', (int) $filters['level_id']);
        }

        if (! empty($filters['organization_unit_id']) && $filters['organization_unit_id'] !== 'all') {
            $builder->where('r.organization_unit_id', (int) $filters['organization_unit_id']);
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $builder->where('r.status', (string) $filters['status']);
        }

        if (! empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $builder->groupStart()
                ->like('r.name_snapshot', $q)
                ->orLike('r.employee_identifier', $q)
                ->orLike('ou.name', $q)
                ->orLike('u.email', $q)
                ->groupEnd();
        }

        return $builder->orderBy('r.id', 'ASC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil rincian profil lengkap personel JF PPBJ beserta riwayat sertifikat.
     */
    public function getPersonnelDetail(int $id): ?array
    {
        $record = $this->db->table('jf_ppbj_records r')
            ->select([
                'r.*',
                'ou.name AS organization_unit_name',
                'ou.code AS organization_unit_code',
                'ou.type AS organization_unit_type',
                'l.name AS level_name',
                'u.name AS user_name',
                'u.email AS user_email',
                'ap.position_name',
                'ap.rank_grade',
            ])
            ->join('organization_units ou', 'ou.id = r.organization_unit_id', 'left')
            ->join('jf_ppbj_levels l', 'l.id = r.level_id', 'left')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->join('asn_profiles ap', 'ap.user_id = r.user_id', 'left')
            ->where('r.id', $id)
            ->get()
            ->getRowArray();

        if (! $record) {
            return null;
        }

        // Ambil daftar sertifikat kompetensi personel ini
        $certificates = $this->db->table('competency_certificates cc')
            ->select([
                'cc.*',
                't.name AS type_name',
                't.description AS type_description',
            ])
            ->join('competency_certificate_types t', 't.id = cc.certificate_type_id', 'left')
            ->where('cc.jf_ppbj_record_id', $id)
            ->orderBy('cc.issued_at', 'DESC')
            ->get()
            ->getResultArray();

        $record['certificates'] = $certificates;

        return $record;
    }

    /**
     * Ambil daftar OPD dan unit kerja beserta agregasi SDM PBJ & gap.
     *
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function getOpdList(array $filters = []): array
    {
        $builder = $this->db->table('organization_units ou')
            ->select([
                'ou.id',
                'ou.code',
                'ou.name',
                'ou.type',
                'ou.is_active',
                'COUNT(DISTINCT r.id) AS personnel_count',
                'COUNT(DISTINCT c.id) AS certificates_count',
            ])
            ->join('jf_ppbj_records r', 'r.organization_unit_id = ou.id AND r.status = "active"', 'left')
            ->join('competency_certificates c', 'c.jf_ppbj_record_id = r.id AND c.status = "verified"', 'left')
            ->groupBy('ou.id, ou.code, ou.name, ou.type, ou.is_active');

        if (! empty($filters['type']) && $filters['type'] !== 'all') {
            $builder->where('ou.type', (string) $filters['type']);
        }

        if (! empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $builder->groupStart()
                ->like('ou.name', $q)
                ->orLike('ou.code', $q)
                ->groupEnd();
        }

        $list = $builder->orderBy('ou.sort_order', 'ASC')
            ->orderBy('ou.name', 'ASC')
            ->get()
            ->getResultArray();

        // Estimasi kebutuhan standar per OPD: Biro PBJ = 20, Dinas = 15, Badan/Lainnya = 10
        foreach ($list as &$item) {
            $personnel = (int) $item['personnel_count'];
            $req = match (strtolower((string) $item['type'])) {
                'biro'  => 20,
                'dinas' => 15,
                default => 10,
            };
            $gap = max(0, $req - $personnel);

            $item['required_count'] = $req;
            $item['gap_count']      = $gap;
        }
        unset($item);

        return $list;
    }

    /**
     * Ambil rincian profil OPD dan daftar personel yang bertugas di unit tersebut.
     */
    public function getOpdDetail(int $id): ?array
    {
        $opd = $this->db->table('organization_units ou')
            ->select([
                'ou.*',
                'p.name AS parent_name',
            ])
            ->join('organization_units p', 'p.id = ou.parent_id', 'left')
            ->where('ou.id', $id)
            ->get()
            ->getRowArray();

        if (! $opd) {
            return null;
        }

        // Ambil personel pada OPD ini
        $personnel = $this->db->table('jf_ppbj_records r')
            ->select([
                'r.id',
                'r.employee_identifier',
                'r.name_snapshot',
                'r.status',
                'l.name AS level_name',
                'COUNT(c.id) AS certificates_count',
            ])
            ->join('jf_ppbj_levels l', 'l.id = r.level_id', 'left')
            ->join('competency_certificates c', 'c.jf_ppbj_record_id = r.id AND c.status = "verified"', 'left')
            ->where('r.organization_unit_id', $id)
            ->groupBy('r.id, r.employee_identifier, r.name_snapshot, r.status, l.name')
            ->get()
            ->getResultArray();

        $personnelCount = count($personnel);
        $totalCerts = array_sum(array_column($personnel, 'certificates_count'));
        $req = match (strtolower((string) $opd['type'])) {
            'biro'  => 20,
            'dinas' => 15,
            default => 10,
        };
        $gap = max(0, $req - $personnelCount);

        $opd['personnel']          = $personnel;
        $opd['personnel_count']    = $personnelCount;
        $opd['certificates_count'] = $totalCerts;
        $opd['required_count']     = $req;
        $opd['gap_count']          = $gap;

        return $opd;
    }

    /**
     * Analisis matriks gap kompetensi unit kerja (OPD) Pemprov Lampung.
     *
     * @return array<string, mixed>
     */
    public function getCompetencyGapAnalysis(int $year = 2026): array
    {
        $opds = $this->db->table('organization_units ou')
            ->select([
                'ou.id',
                'ou.code',
                'ou.name',
                'ou.type',
                'COUNT(DISTINCT r.id) AS personnel_count',
            ])
            ->join('jf_ppbj_records r', 'r.organization_unit_id = ou.id AND r.status = "active"', 'left')
            ->where('ou.is_active', 1)
            ->groupBy('ou.id, ou.code, ou.name, ou.type')
            ->orderBy('ou.sort_order', 'ASC')
            ->get()
            ->getResultArray();

        $matrix = [];
        $totalNeed = 0;
        $totalAvailable = 0;
        $totalGap = 0;

        $chartLabels    = [];
        $chartNeed      = [];
        $chartAvailable = [];

        foreach ($opds as $opd) {
            $available = (int) $opd['personnel_count'];
            $need = match (strtolower((string) $opd['type'])) {
                'biro'  => 20,
                'dinas' => 15,
                default => 10,
            };
            $gap = max(0, $need - $available);

            $totalNeed      += $need;
            $totalAvailable += $available;
            $totalGap       += $gap;

            // Tentukan prioritas
            if ($gap >= 5) {
                $priority = 'Tinggi';
                $priorityStatus = 'rejected';
            } elseif ($gap >= 2) {
                $priority = 'Menengah';
                $priorityStatus = 'review';
            } else {
                $priority = 'Rendah';
                $priorityStatus = 'approved';
            }

            $matrix[] = [
                'opd_id'          => (int) $opd['id'],
                'unit'            => $opd['name'],
                'code'            => $opd['code'],
                'type'            => $opd['type'],
                'competency'      => 'Pengelolaan Pengadaan Terpadu',
                'need'            => $need,
                'available'       => $available,
                'gap'             => $gap,
                'priority'        => $priority,
                'priority_status' => $priorityStatus,
            ];
        }

        // Urutkan berdasarkan gap terbesar untuk chart dan spotlight
        usort($matrix, static fn($a, $b) => $b['gap'] <=> $a['gap']);

        // Ambil 8 OPD teratas untuk visualisasi chart
        $top8 = array_slice($matrix, 0, 8);
        foreach ($top8 as $item) {
            $shortName = str_replace(['Dinas ', 'Biro ', 'Badan '], '', $item['unit']);
            $chartLabels[]    = substr($shortName, 0, 15);
            $chartNeed[]      = $item['need'];
            $chartAvailable[] = $item['available'];
        }

        return [
            'summary' => [
                'total_need'      => $totalNeed,
                'total_available' => $totalAvailable,
                'total_gap'       => $totalGap,
                'coverage_pct'    => $totalNeed > 0 ? round(($totalAvailable / $totalNeed) * 100) : 0,
            ],
            'chart' => [
                'categories' => $chartLabels,
                'need'       => $chartNeed,
                'available'  => $chartAvailable,
            ],
            'matrix' => $matrix,
        ];
    }

    /**
     * Ambil daftar sertifikat kompetensi personel.
     *
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function getCompetencyCertificates(array $filters = []): array
    {
        $builder = $this->db->table('competency_certificates cc')
            ->select([
                'cc.*',
                't.name AS certificate_type_name',
                'r.name_snapshot AS owner_name',
                'r.employee_identifier AS owner_nip',
                'ou.name AS organization_unit_name',
            ])
            ->join('competency_certificate_types t', 't.id = cc.certificate_type_id', 'left')
            ->join('jf_ppbj_records r', 'r.id = cc.jf_ppbj_record_id', 'left')
            ->join('organization_units ou', 'ou.id = r.organization_unit_id', 'left');

        if (! empty($filters['type_id']) && $filters['type_id'] !== 'all') {
            $builder->where('cc.certificate_type_id', (int) $filters['type_id']);
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $builder->where('cc.status', (string) $filters['status']);
        }

        if (! empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $builder->groupStart()
                ->like('cc.certificate_number', $q)
                ->orLike('r.name_snapshot', $q)
                ->orLike('r.employee_identifier', $q)
                ->orLike('ou.name', $q)
                ->groupEnd();
        }

        $list = $builder->orderBy('cc.issued_at', 'DESC')
            ->limit(100)
            ->get()
            ->getResultArray();

        $today = date('Y-m-d');
        foreach ($list as &$item) {
            $validUntil = $item['expires_at'] ? date('d M Y', strtotime($item['expires_at'])) : 'Seumur Hidup';
            $validFrom  = $item['issued_at'] ? date('d M Y', strtotime($item['issued_at'])) : '-';
            $item['validity_label'] = $validFrom . ' &ndash; ' . $validUntil;

            // Status evaluasi waktu
            if ($item['expires_at'] && $item['expires_at'] < $today) {
                $item['computed_status'] = 'expired';
                $item['status_label']    = 'Kedaluwarsa';
            } else {
                $item['computed_status'] = $item['status'] ?: 'active';
                $item['status_label']    = $item['status'] === 'active' ? 'Aktif' : ucfirst($item['status']);
            }
        }
        unset($item);

        return $list;
    }

    /**
     * Ambil daftar jenjang JF PPBJ beserta jumlah personel.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLevelsList(): array
    {
        return $this->db->table('jf_ppbj_levels l')
            ->select([
                'l.*',
                'COUNT(r.id) AS personnel_count',
            ])
            ->join('jf_ppbj_records r', 'r.level_id = l.id AND r.status = "active"', 'left')
            ->groupBy('l.id, l.key, l.name, l.sort_order, l.is_active, l.created_at, l.updated_at')
            ->orderBy('l.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil daftar jenis sertifikat kompetensi beserta jumlah kepemilikan.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCertificateTypesList(): array
    {
        return $this->db->table('competency_certificate_types t')
            ->select([
                't.*',
                'COUNT(cc.id) AS certificate_count',
            ])
            ->join('competency_certificates cc', 'cc.certificate_type_id = t.id AND cc.status = "verified"', 'left')
            ->groupBy('t.id, t.key, t.name, t.description, t.is_active, t.created_at, t.updated_at')
            ->orderBy('t.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil riwayat penugasan / promosi jenjang JF PPBJ.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getHistoriesList(int $limit = 50): array
    {
        return $this->db->table('jf_ppbj_records r')
            ->select([
                'r.id',
                'r.name_snapshot AS name',
                'r.employee_identifier AS nip',
                'r.effective_at',
                'r.change_type',
                'ou.name AS unit_name',
                'l.name AS level_name',
            ])
            ->join('organization_units ou', 'ou.id = r.organization_unit_id', 'left')
            ->join('jf_ppbj_levels l', 'l.id = r.level_id', 'left')
            ->orderBy('r.effective_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}

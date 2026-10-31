<?php

namespace App\Services;

class VisitorStatisticService
{
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
        $this->ensureIpLogsTableExists();
        $this->seedInitialIfEmpty();
    }

    /**
     * Pastikan tabel audit visitor_ip_logs tersedia tanpa memerlukan file migrasi baru.
     */
    public function ensureIpLogsTableExists(): void
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS visitor_ip_logs (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                ip_address VARCHAR(45) NOT NULL,
                feature VARCHAR(50) NOT NULL DEFAULT 'home',
                visit_date DATE NOT NULL,
                user_agent VARCHAR(255) NULL,
                created_at DATETIME NOT NULL,
                UNIQUE KEY uk_ip_feature_date (ip_address, feature, visit_date),
                KEY idx_feature_date (feature, visit_date),
                KEY idx_visit_date (visit_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            $this->db->query($sql);
        } catch (\Throwable) {
            // Silently recover if DB has transient issue
        }
    }

    /**
     * Resolusi alamat IP Client secara presisi dan aman dari berbagai proxy header / reverse proxy.
     */
    public function resolveClientIp($request = null): string
    {
        if ($request === null) {
            $request = service('request');
        }

        $server = $_SERVER ?? [];
        $candidates = [
            $server['HTTP_CF_CONNECTING_IP'] ?? ($request instanceof \CodeIgniter\HTTP\IncomingRequest ? $request->getHeaderLine('CF-Connecting-IP') : null),
            $server['HTTP_X_REAL_IP'] ?? ($request instanceof \CodeIgniter\HTTP\IncomingRequest ? $request->getHeaderLine('X-Real-IP') : null),
            $server['HTTP_X_FORWARDED_FOR'] ?? ($request instanceof \CodeIgniter\HTTP\IncomingRequest ? $request->getHeaderLine('X-Forwarded-For') : null),
            $server['HTTP_CLIENT_IP'] ?? ($request instanceof \CodeIgniter\HTTP\IncomingRequest ? $request->getHeaderLine('Client-IP') : null),
        ];

        foreach ($candidates as $candidate) {
            if (! empty($candidate)) {
                $parts = explode(',', (string) $candidate);
                $cleanIp = trim($parts[0]);
                if (str_contains($cleanIp, ':') && ! str_contains($cleanIp, '::') && substr_count($cleanIp, ':') === 1) {
                    $cleanIp = explode(':', $cleanIp)[0];
                }
                if (filter_var($cleanIp, FILTER_VALIDATE_IP)) {
                    return $cleanIp;
                }
            }
        }

        try {
            $ip = $request->getIPAddress();
            if (! empty($ip) && filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        } catch (\Throwable) {
        }

        return '127.0.0.1';
    }

    /**
     * Record a page visit / hit for a specific feature based on Client IP.
     * 1 Unique IP per feature per day = 1 hit increment.
     * Subsequent hits from the same IP on the same day for that feature are silently ignored.
     *
     * @return bool True if a new hit was counted, false if duplicate today.
     */
    public function recordHit(string $feature = 'home', ?string $ip = null, ?string $userAgent = null): bool
    {
        $allowed = ['home', 'arena_belajar', 'bunga_rampai', 'regulasi', 'berita', 'event'];
        if (! in_array($feature, $allowed, true)) {
            $feature = 'home';
        }

        // Resolusi IP Client cerdas: jika $ip kosong atau bernilai loopback (127.0.0.1 / ::1),
        // prioritaskan pemeriksaan header reverse proxy (CF-Connecting-IP, X-Real-IP, X-Forwarded-For).
        if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
            $resolved = $this->resolveClientIp();
            if (! empty($resolved) && $resolved !== '127.0.0.1' && $resolved !== '::1') {
                $ip = $resolved;
            } elseif (empty($ip)) {
                $ip = $resolved;
            }
        }

        if ($userAgent === null && isset($_SERVER['HTTP_USER_AGENT'])) {
            $userAgent = substr(trim($_SERVER['HTTP_USER_AGENT']), 0, 250);
        }

        // Penanganan simulasi dan pengujian multi-klien di lingkungan lokal (Laragon / localhost):
        // Jika IP terdeteksi loopback (127.0.0.1 / ::1), bedakan sesi/klien menggunakan cookie bp_vid atau User-Agent
        // agar browser / device / sesi berbeda tidak terjebak di 1 slot IP yang sama.
        if (($ip === '127.0.0.1' || $ip === '::1') && ! is_cli()) {
            $clientToken = $_COOKIE['bp_vid'] ?? null;
            if ($clientToken === null && ! headers_sent()) {
                $clientToken = bin2hex(random_bytes(6));
                setcookie('bp_vid', $clientToken, time() + 86400 * 30, '/');
                $_COOKIE['bp_vid'] = $clientToken;
            }

            $seed = ($clientToken ?? '') . '_' . ($userAgent ?? '');
            if (! empty($seed) && $seed !== '_') {
                $slot = (abs(crc32($seed)) % 250) + 2; // Range 127.0.0.2 - 127.0.0.251
                $ip   = '127.0.0.' . $slot;
            }
        }

        $today = date('Y-m-d');
        $now   = date('Y-m-d H:i:s');

        // Fast In-Memory / File Cache check to minimize SQL overhead
        $cacheKey = 'visitor_ip_' . md5($ip . '_' . $feature . '_' . $today);
        if (cache($cacheKey)) {
            return false;
        }

        $this->ensureIpLogsTableExists();

        // Attempt insert to IP log table
        try {
            $insertSql = "INSERT INTO visitor_ip_logs (ip_address, feature, visit_date, user_agent, created_at)
                          VALUES (?, ?, ?, ?, ?)";
            $this->db->query($insertSql, [$ip, $feature, $today, $userAgent, $now]);
        } catch (\Throwable) {
            // If duplicate key error (already recorded for today), cache and return false
            cache()->save($cacheKey, 1, 86400);
            return false;
        }

        // Fresh IP visit! Increment visitor_statistics counter
        try {
            $sql = "INSERT INTO visitor_statistics (feature, visit_date, hit_count, created_at, updated_at)
                    VALUES (?, ?, 1, ?, ?)
                    ON DUPLICATE KEY UPDATE hit_count = hit_count + 1, updated_at = ?";

            $this->db->query($sql, [$feature, $today, $now, $now, $now]);
        } catch (\Throwable) {
            // Silently recover if DB has transient issue
        }

        // Cache for the rest of today
        cache()->save($cacheKey, 1, 86400);
        return true;
    }

    /**
     * Retrieve visitor statistics for home landing page and features.
     */
    public function getVisitorStats(): array
    {
        $todayStr     = date('Y-m-d');
        // Monday of current week
        $weekStart    = date('Y-m-d', strtotime('monday this week'));
        $monthStart   = date('Y-m-01');
        $yearStart    = date('Y-01-01');

        // 1. Home stats
        $homeStats = [
            'today'      => 0,
            'this_week'  => 0,
            'this_month' => 0,
            'this_year'  => 0,
            'total'      => 0,
        ];

        try {
            // Today
            $rowToday = $this->db->table('visitor_statistics')
                ->selectSum('hit_count', 'total_hits')
                ->where('feature', 'home')
                ->where('visit_date', $todayStr)
                ->get()->getRowArray();
            $homeStats['today'] = (int) ($rowToday['total_hits'] ?? 0);

            // Week
            $rowWeek = $this->db->table('visitor_statistics')
                ->selectSum('hit_count', 'total_hits')
                ->where('feature', 'home')
                ->where('visit_date >=', $weekStart)
                ->get()->getRowArray();
            $homeStats['this_week'] = (int) ($rowWeek['total_hits'] ?? 0);

            // Month
            $rowMonth = $this->db->table('visitor_statistics')
                ->selectSum('hit_count', 'total_hits')
                ->where('feature', 'home')
                ->where('visit_date >=', $monthStart)
                ->get()->getRowArray();
            $homeStats['this_month'] = (int) ($rowMonth['total_hits'] ?? 0);

            // Year
            $rowYear = $this->db->table('visitor_statistics')
                ->selectSum('hit_count', 'total_hits')
                ->where('feature', 'home')
                ->where('visit_date >=', $yearStart)
                ->get()->getRowArray();
            $homeStats['this_year'] = (int) ($rowYear['total_hits'] ?? 0);

            // Total
            $rowTotal = $this->db->table('visitor_statistics')
                ->selectSum('hit_count', 'total_hits')
                ->where('feature', 'home')
                ->get()->getRowArray();
            $homeStats['total'] = (int) ($rowTotal['total_hits'] ?? 0);
        } catch (\Throwable) {
        }

        // 2. Feature stats
        $featureConfig = [
            'arena_belajar' => [
                'label'       => 'Arena Belajar',
                'description' => 'Kursus, silabus mandiri & sertifikasi kompetensi',
                'icon'        => 'solar:square-academic-cap-2-bold',
                'color'       => '#0c5aa6',
                'bg'          => '#eff6ff',
                'border'      => '#bfdbfe',
                'url'         => site_url('arena-belajar'),
            ],
            'bunga_rampai' => [
                'label'       => 'Bunga Rampai PBJ',
                'description' => 'Template dokumen KAK/HPS, studi kasus & literasi',
                'icon'        => 'solar:book-bookmark-bold',
                'color'       => '#10b981',
                'bg'          => '#ecfdf5',
                'border'      => '#a7f3d0',
                'url'         => site_url('bunga-rampai'),
            ],
            'regulasi' => [
                'label'       => 'Regulasi PBJ',
                'description' => 'Bank naskah hukum, Perpres, PerLKPP & Pergub',
                'icon'        => 'solar:scale-bold',
                'color'       => '#f59e0b',
                'bg'          => '#fffbeb',
                'border'      => '#fde68a',
                'url'         => site_url('regulasi'),
            ],
            'berita' => [
                'label'       => 'Berita & Informasi',
                'description' => 'Siaran pers, kabar kepengadaan & rilis Pemprov',
                'icon'        => 'solar:newspaper-bold',
                'color'       => '#ec4899',
                'bg'          => '#fdf2f8',
                'border'      => '#fbcfe8',
                'url'         => site_url('berita'),
            ],
            'event' => [
                'label'       => 'Event & Agenda',
                'description' => 'Bimtek, webinar nasional & sosialisasi regulasi',
                'icon'        => 'solar:calendar-bold',
                'color'       => '#8b5cf6',
                'bg'          => '#f5f3ff',
                'border'      => '#ddd6fe',
                'url'         => site_url('event'),
            ],
        ];

        $featuresData = [];
        $totalFeatureHits = 0;

        foreach ($featureConfig as $key => $meta) {
            $hitsToday = 0;
            $hitsMonth = 0;
            $hitsTotal = 0;

            try {
                $rowT = $this->db->table('visitor_statistics')
                    ->selectSum('hit_count', 'total_hits')
                    ->where('feature', $key)
                    ->where('visit_date', $todayStr)
                    ->get()->getRowArray();
                $hitsToday = (int) ($rowT['total_hits'] ?? 0);

                $rowM = $this->db->table('visitor_statistics')
                    ->selectSum('hit_count', 'total_hits')
                    ->where('feature', $key)
                    ->where('visit_date >=', $monthStart)
                    ->get()->getRowArray();
                $hitsMonth = (int) ($rowM['total_hits'] ?? 0);

                $rowAll = $this->db->table('visitor_statistics')
                    ->selectSum('hit_count', 'total_hits')
                    ->where('feature', $key)
                    ->get()->getRowArray();
                $hitsTotal = (int) ($rowAll['total_hits'] ?? 0);
            } catch (\Throwable) {
            }

            $totalFeatureHits += $hitsTotal;

            $featuresData[$key] = array_merge($meta, [
                'hits_today' => $hitsToday,
                'hits_month' => $hitsMonth,
                'hits_total' => $hitsTotal,
                'percentage' => 0,
            ]);
        }

        // Calculate percentage distribution
        if ($totalFeatureHits > 0) {
            foreach ($featuresData as $key => &$f) {
                $f['percentage'] = round(($f['hits_total'] / $totalFeatureHits) * 100, 1);
            }
            unset($f);
        }

        return [
            'home'               => $homeStats,
            'features'           => $featuresData,
            'total_feature_hits' => $totalFeatureHits,
        ];
    }

    /**
     * Update visitor statistics numbers (called from Super Admin System submenu).
     * Automatically adjusts the underlying database rows so that today, week, month,
     * year, and total SQL aggregates match the admin's exact desired figures.
     *
     * @param array<string, mixed> $data
     * @return bool
     */
    public function updateStats(array $data): bool
    {
        $todayStr   = date('Y-m-d');
        $weekStart  = date('Y-m-d', strtotime('monday this week'));
        $monthStart = date('Y-m-01');
        $yearStart  = date('Y-01-01');

        $this->db->transStart();

        // 1. Update Home Stats
        if (isset($data['home_today']) || isset($data['home_total'])) {
            $targetToday = max(0, (int) ($data['home_today'] ?? 0));
            $targetWeek  = max($targetToday, (int) ($data['home_week'] ?? $targetToday));
            $targetMonth = max($targetWeek, (int) ($data['home_month'] ?? $targetWeek));
            $targetYear  = max($targetMonth, (int) ($data['home_year'] ?? $targetMonth));
            $targetTotal = max($targetYear, (int) ($data['home_total'] ?? $targetYear));

            // Set today's hit_count
            $this->setRowHitCount('home', $todayStr, $targetToday);

            // Remainder for current week (if weekStart < todayStr)
            $diffWeek = $targetWeek - $targetToday;
            if ($weekStart < $todayStr) {
                $this->db->table('visitor_statistics')
                    ->where('feature', 'home')
                    ->where('visit_date >=', $weekStart)
                    ->where('visit_date <', $todayStr)
                    ->delete();
                if ($diffWeek > 0) {
                    $this->setRowHitCount('home', $weekStart, $diffWeek);
                }
            }

            // Remainder for current month (dates in this month before weekStart)
            $diffMonth = $targetMonth - $targetWeek;
            if ($monthStart < $weekStart) {
                $this->db->table('visitor_statistics')
                    ->where('feature', 'home')
                    ->where('visit_date >=', $monthStart)
                    ->where('visit_date <', $weekStart)
                    ->delete();
                if ($diffMonth > 0) {
                    $this->setRowHitCount('home', $monthStart, $diffMonth);
                }
            }

            // Remainder for current year (dates in this year before monthStart)
            $diffYear = $targetYear - $targetMonth;
            if ($yearStart < $monthStart) {
                $this->db->table('visitor_statistics')
                    ->where('feature', 'home')
                    ->where('visit_date >=', $yearStart)
                    ->where('visit_date <', $monthStart)
                    ->delete();
                if ($diffYear > 0) {
                    $janDate = date('Y-01-15');
                    $this->setRowHitCount('home', $janDate, $diffYear);
                }
            }

            // Remainder for total accumulation (dates prior to current year)
            $diffTotal = $targetTotal - $targetYear;
            $this->db->table('visitor_statistics')
                ->where('feature', 'home')
                ->where('visit_date <', $yearStart)
                ->delete();
            if ($diffTotal > 0) {
                $prevYearDate = (date('Y') - 1) . '-12-31';
                $this->setRowHitCount('home', $prevYearDate, $diffTotal);
            }
        }

        // 2. Update Feature Stats
        $features = $data['features'] ?? [];
        $validFeatures = ['arena_belajar', 'bunga_rampai', 'regulasi', 'berita', 'event'];

        foreach ($validFeatures as $featKey) {
            if (! isset($features[$featKey])) {
                continue;
            }

            $fToday = max(0, (int) ($features[$featKey]['today'] ?? 0));
            $fTotal = max($fToday, (int) ($features[$featKey]['total'] ?? $fToday));
            $fMonth = isset($features[$featKey]['month']) ? max($fToday, (int) $features[$featKey]['month']) : null;

            // Set today's hit_count
            $this->setRowHitCount($featKey, $todayStr, $fToday);

            // Handle month and total distribution
            if ($fMonth !== null) {
                $diffMonth = max(0, $fMonth - $fToday);
                $diffRest  = max(0, $fTotal - max($fMonth, $fToday));

                // Clear other rows in this month before today
                $this->db->table('visitor_statistics')
                    ->where('feature', $featKey)
                    ->where('visit_date >=', $monthStart)
                    ->where('visit_date <', $todayStr)
                    ->delete();

                if ($diffMonth > 0 && $monthStart < $todayStr) {
                    $this->setRowHitCount($featKey, $monthStart, $diffMonth);
                }

                // Clear rows before monthStart
                $this->db->table('visitor_statistics')
                    ->where('feature', $featKey)
                    ->where('visit_date <', $monthStart)
                    ->delete();

                if ($diffRest > 0) {
                    $baselineDate = date('Y-01-15');
                    if ($monthStart <= $baselineDate) {
                        $baselineDate = (date('Y') - 1) . '-12-31';
                    }
                    $this->setRowHitCount($featKey, $baselineDate, $diffRest);
                }
            } else {
                $diffTotal = max(0, $fTotal - $fToday);

                // Clear other rows for this feature
                $this->db->table('visitor_statistics')
                    ->where('feature', $featKey)
                    ->where('visit_date !=', $todayStr)
                    ->delete();

                if ($diffTotal > 0) {
                    $baselineDate = date('Y-01-15');
                    $this->setRowHitCount($featKey, $baselineDate, $diffTotal);
                }
            }
        }

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }

    /**
     * Helper to insert or update a row in visitor_statistics for a given feature & date.
     */
    protected function setRowHitCount(string $feature, string $date, int $count): void
    {
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO visitor_statistics (feature, visit_date, hit_count, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE hit_count = ?, updated_at = ?";
        $this->db->query($sql, [$feature, $date, $count, $now, $now, $count, $now]);
    }

    /**
     * Reset atau bersihkan riwayat data pengunjung berdasarkan cakupan waktu (scope).
     *
     * Opsi cakupan ($scope):
     * - 'before_this_month' (Default): Hanya ambil data bulan ini, hapus seluruh data sebelum bulan berjalan (visit_date < Y-m-01).
     * - 'before_this_year': Hanya ambil data tahun ini, hapus seluruh data sebelum tahun berjalan (visit_date < Y-01-01).
     * - 'today_only': Reset hit counter dan log IP khusus tanggal hari ini menjadi 0.
     * - 'all_history': Reset total / bersihkan seluruh data riwayat kunjungan.
     *
     * @param string $scope 'before_this_month' | 'before_this_year' | 'today_only' | 'all_history'
     * @param string $feature 'all' | 'home' | 'arena_belajar' | 'bunga_rampai' | 'regulasi' | 'berita' | 'event'
     * @return array{success: bool, deleted_stat_rows: int, deleted_ip_rows: int, message: string}
     */
    public function resetVisitorData(string $scope = 'before_this_month', string $feature = 'all'): array
    {
        $this->ensureIpLogsTableExists();
        $todayStr   = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $yearStart  = date('Y-01-01');

        $this->db->transStart();

        $deletedStatRows = 0;
        $deletedIpRows   = 0;
        $message         = '';

        $statBuilder = $this->db->table('visitor_statistics');
        $ipBuilder   = $this->db->table('visitor_ip_logs');

        if ($feature !== 'all') {
            $statBuilder->where('feature', $feature);
            $ipBuilder->where('feature', $feature);
        }

        switch ($scope) {
            case 'before_this_month':
                // Hapus data kunjungan sebelum bulan berjalan
                $statBuilder->where('visit_date <', $monthStart)->delete();
                $deletedStatRows = $this->db->affectedRows();

                $ipBuilder->where('visit_date <', $monthStart)->delete();
                $deletedIpRows = $this->db->affectedRows();

                $bulanNames = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $currentMonthName = ($bulanNames[(int) date('n')] ?? date('F')) . ' ' . date('Y');
                $message = "Pembersihan berhasil: Riwayat sebelum bulan {$currentMonthName} telah dihapus. Hanya data bulan berjalan yang dipertahankan.";
                break;

            case 'before_this_year':
                // Hapus data kunjungan sebelum tahun berjalan
                $statBuilder->where('visit_date <', $yearStart)->delete();
                $deletedStatRows = $this->db->affectedRows();

                $ipBuilder->where('visit_date <', $yearStart)->delete();
                $deletedIpRows = $this->db->affectedRows();

                $currentYear = date('Y');
                $message = "Pembersihan berhasil: Riwayat sebelum tahun {$currentYear} telah dihapus. Data tahun {$currentYear} tetap dipertahankan.";
                break;

            case 'today_only':
                // Reset hit hari ini ke 0 dan bersihkan log IP hari ini
                $statBuilder->where('visit_date', $todayStr)->update([
                    'hit_count'  => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $deletedStatRows = $this->db->affectedRows();

                $ipBuilder->where('visit_date', $todayStr)->delete();
                $deletedIpRows = $this->db->affectedRows();

                $message = "Hit counter dan log IP untuk hari ini (" . date('d/m/Y') . ") berhasil direset ke 0.";
                break;

            case 'all_history':
                // Bersihkan seluruh riwayat statistik & IP
                // Sisakan row hari ini dengan hit 0 untuk konsistensi query
                $statBuilder->where('visit_date !=', $todayStr)->delete();
                $deletedStatRows = $this->db->affectedRows();

                $statReset = $this->db->table('visitor_statistics')->where('visit_date', $todayStr);
                if ($feature !== 'all') {
                    $statReset->where('feature', $feature);
                }
                $statReset->update([
                    'hit_count'  => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $ipBuilder->delete();
                $deletedIpRows = $this->db->affectedRows();

                $message = "Seluruh riwayat statistik pengunjung dan log IP telah dibersihkan secara permanen (reset total).";
                break;

            default:
                $message = "Opsi cakupan pembersihan tidak valid.";
                break;
        }

        $this->db->transComplete();
        $success = $this->db->transStatus() !== false;

        return [
            'success'           => $success,
            'deleted_stat_rows' => $deletedStatRows,
            'deleted_ip_rows'   => $deletedIpRows,
            'message'           => $message,
        ];
    }

    /**
     * Reset hit counter dan log IP untuk hari ini.
     */
    public function resetToday(string $feature = 'all'): bool
    {
        $res = $this->resetVisitorData('today_only', $feature);
        return $res['success'];
    }

    /**
     * Ambil riwayat log kunjungan IP terkini.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRecentIpLogs(int $limit = 20): array
    {
        $this->ensureIpLogsTableExists();
        try {
            return $this->db->table('visitor_ip_logs')
                ->orderBy('id', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Ambil ringkasan statistik keunikan IP Client.
     *
     * @return array<string, int>
     */
    public function getIpStatsSummary(): array
    {
        $this->ensureIpLogsTableExists();
        $todayStr   = date('Y-m-d');
        $weekStart  = date('Y-m-d', strtotime('monday this week'));
        $monthStart = date('Y-m-01');

        $summary = [
            'unique_today' => 0,
            'unique_week'  => 0,
            'unique_month' => 0,
            'total_logs'   => 0,
        ];

        try {
            // Count unique IPs today
            $rowToday = $this->db->query(
                "SELECT COUNT(DISTINCT ip_address) AS cnt FROM visitor_ip_logs WHERE visit_date = ?",
                [$todayStr]
            )->getRowArray();
            $summary['unique_today'] = (int) ($rowToday['cnt'] ?? 0);

            // Count unique IPs this week
            $rowWeek = $this->db->query(
                "SELECT COUNT(DISTINCT ip_address) AS cnt FROM visitor_ip_logs WHERE visit_date >= ?",
                [$weekStart]
            )->getRowArray();
            $summary['unique_week'] = (int) ($rowWeek['cnt'] ?? 0);

            // Count unique IPs this month
            $rowMonth = $this->db->query(
                "SELECT COUNT(DISTINCT ip_address) AS cnt FROM visitor_ip_logs WHERE visit_date >= ?",
                [$monthStart]
            )->getRowArray();
            $summary['unique_month'] = (int) ($rowMonth['cnt'] ?? 0);

            // Total logs
            $summary['total_logs'] = $this->db->table('visitor_ip_logs')->countAllResults();
        } catch (\Throwable) {
        }

        return $summary;
    }

    /**
     * Seeds initial realistic baseline historical rows if table is freshly created.
     */
    protected function seedInitialIfEmpty(): void
    {
        try {
            $count = $this->db->table('visitor_statistics')->countAllResults();
            if ($count > 0) {
                return;
            }

            // Generate past 30 days of baseline realistic traffic
            $today = new \DateTime();
            $batch = [];

            for ($i = 30; $i >= 0; $i--) {
                $dateObj = (clone $today)->modify("-{$i} days");
                $dateStr = $dateObj->format('Y-m-d');
                $isToday = ($i === 0);

                // Home hits: 110-180 per day, today starts around 140
                $homeHits = $isToday ? rand(135, 160) : rand(110, 195);
                $batch[] = [
                    'feature'    => 'home',
                    'visit_date' => $dateStr,
                    'hit_count'  => $homeHits,
                    'created_at' => $dateStr . ' 08:00:00',
                    'updated_at' => $dateStr . ' 18:00:00',
                ];

                // Arena Belajar: 60-95 hits/day
                $batch[] = [
                    'feature'    => 'arena_belajar',
                    'visit_date' => $dateStr,
                    'hit_count'  => $isToday ? rand(45, 65) : rand(55, 95),
                    'created_at' => $dateStr . ' 08:00:00',
                    'updated_at' => $dateStr . ' 18:00:00',
                ];

                // Bunga Rampai: 40-75 hits/day
                $batch[] = [
                    'feature'    => 'bunga_rampai',
                    'visit_date' => $dateStr,
                    'hit_count'  => $isToday ? rand(30, 50) : rand(40, 75),
                    'created_at' => $dateStr . ' 08:00:00',
                    'updated_at' => $dateStr . ' 18:00:00',
                ];

                // Regulasi: 50-80 hits/day
                $batch[] = [
                    'feature'    => 'regulasi',
                    'visit_date' => $dateStr,
                    'hit_count'  => $isToday ? rand(35, 60) : rand(48, 85),
                    'created_at' => $dateStr . ' 08:00:00',
                    'updated_at' => $dateStr . ' 18:00:00',
                ];

                // Berita: 25-50 hits/day
                $batch[] = [
                    'feature'    => 'berita',
                    'visit_date' => $dateStr,
                    'hit_count'  => $isToday ? rand(20, 35) : rand(25, 52),
                    'created_at' => $dateStr . ' 08:00:00',
                    'updated_at' => $dateStr . ' 18:00:00',
                ];

                // Event: 20-45 hits/day
                $batch[] = [
                    'feature'    => 'event',
                    'visit_date' => $dateStr,
                    'hit_count'  => $isToday ? rand(15, 30) : rand(20, 48),
                    'created_at' => $dateStr . ' 08:00:00',
                    'updated_at' => $dateStr . ' 18:00:00',
                ];
            }

            // Also seed earlier months in the current year to give accurate 'this_year' total
            // E.g. Jan-Sep baseline totals
            $earlierMonths = ['2026-01-15', '2026-02-15', '2026-03-15', '2026-04-15', '2026-05-15', '2026-06-15', '2026-07-15', '2026-08-15', '2026-09-15'];
            foreach ($earlierMonths as $mDate) {
                $batch[] = ['feature' => 'home', 'visit_date' => $mDate, 'hit_count' => rand(3200, 4100), 'created_at' => $mDate . ' 12:00:00', 'updated_at' => $mDate . ' 12:00:00'];
                $batch[] = ['feature' => 'arena_belajar', 'visit_date' => $mDate, 'hit_count' => rand(1400, 2100), 'created_at' => $mDate . ' 12:00:00', 'updated_at' => $mDate . ' 12:00:00'];
                $batch[] = ['feature' => 'bunga_rampai', 'visit_date' => $mDate, 'hit_count' => rand(950, 1500), 'created_at' => $mDate . ' 12:00:00', 'updated_at' => $mDate . ' 12:00:00'];
                $batch[] = ['feature' => 'regulasi', 'visit_date' => $mDate, 'hit_count' => rand(1100, 1800), 'created_at' => $mDate . ' 12:00:00', 'updated_at' => $mDate . ' 12:00:00'];
                $batch[] = ['feature' => 'berita', 'visit_date' => $mDate, 'hit_count' => rand(600, 1100), 'created_at' => $mDate . ' 12:00:00', 'updated_at' => $mDate . ' 12:00:00'];
                $batch[] = ['feature' => 'event', 'visit_date' => $mDate, 'hit_count' => rand(500, 950), 'created_at' => $mDate . ' 12:00:00', 'updated_at' => $mDate . ' 12:00:00'];
            }

            foreach ($batch as $item) {
                $this->db->table('visitor_statistics')->ignore(true)->insert($item);
            }
        } catch (\Throwable) {
        }
    }
}

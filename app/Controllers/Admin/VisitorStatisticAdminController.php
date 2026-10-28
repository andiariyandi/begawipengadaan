<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\Identity\AuthorizationService;
use App\Services\VisitorStatisticService;
use CodeIgniter\HTTP\ResponseInterface;

class VisitorStatisticAdminController extends BaseController
{
    protected VisitorStatisticService $visitorService;
    protected AuditService $auditService;
    protected AuthorizationService $auth;

    public function __construct()
    {
        $this->visitorService = new VisitorStatisticService();
        $this->auditService   = new AuditService();
        $this->auth           = new AuthorizationService();
    }

    /**
     * Verifikasi otorisasi akses (Super Admin / Administrator Sistem).
     */
    private function checkAccess(): ?ResponseInterface
    {
        if (! $this->auth->isAdmin() && ! $this->auth->can('system.settings')) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak: Memerlukan hak akses Administrator untuk Pengaturan Statistik Kunjungan.');
        }
        return null;
    }

    /**
     * Tampilan form manajemen & audit statistik kunjungan portal.
     */
    public function index()
    {
        if ($res = $this->checkAccess()) {
            return $res;
        }

        $stats     = $this->visitorService->getVisitorStats();
        $ipSummary = $this->visitorService->getIpStatsSummary();
        $ipLogs    = $this->visitorService->getRecentIpLogs(25);
        $currentIp = $this->visitorService->resolveClientIp($this->request);

        return view('dashboard/settings/visitor_stats', [
            'pageTitle'     => 'Statistik Trafik & Kunjungan Portal',
            'activeSetting' => 'visitor_stats',
            'stats'         => $stats,
            'ipSummary'     => $ipSummary,
            'ipLogs'        => $ipLogs,
            'currentIp'     => $currentIp,
        ]);
    }

    /**
     * Simpan pembaruan angka statistik trafik.
     * Mendukung pembaruan terisolasi: section 'home' (hanya beranda),
     * section 'features' (hanya 5 fitur unggulan), atau kombinasi keduanya.
     */
    public function update()
    {
        if ($res = $this->checkAccess()) {
            return $res;
        }

        $post    = $this->request->getPost();
        $section = strtolower(trim((string) ($post['section'] ?? '')));

        $payload    = [];
        $auditMsg   = '';
        $successMsg = '';
        $anchor     = '';

        $isHomeUpdate     = ($section === 'home') || ($section === '' && (isset($post['home_today']) || isset($post['home_total'])));
        $isFeaturesUpdate = ($section === 'features') || ($section === '' && isset($post['features']));

        // Isolasi ketat: jika user menekan tombol simpan khusus satu section
        if ($section === 'home') {
            $isFeaturesUpdate = false;
        } elseif ($section === 'features') {
            $isHomeUpdate = false;
        }

        // 1. Ekstraksi angka beranda (HANYA jika masuk dalam cakupan update)
        if ($isHomeUpdate) {
            $homeData = [
                'home_today' => max(0, (int) ($post['home_today'] ?? 0)),
                'home_week'  => max(0, (int) ($post['home_week'] ?? 0)),
                'home_month' => max(0, (int) ($post['home_month'] ?? 0)),
                'home_year'  => max(0, (int) ($post['home_year'] ?? 0)),
                'home_total' => max(0, (int) ($post['home_total'] ?? 0)),
            ];

            // Validasi hierarki matematis home
            if ($homeData['home_week'] < $homeData['home_today']) {
                $homeData['home_week'] = $homeData['home_today'];
            }
            if ($homeData['home_month'] < $homeData['home_week']) {
                $homeData['home_month'] = $homeData['home_week'];
            }
            if ($homeData['home_year'] < $homeData['home_month']) {
                $homeData['home_year'] = $homeData['home_month'];
            }
            if ($homeData['home_total'] < $homeData['home_year']) {
                $homeData['home_total'] = $homeData['home_year'];
            }

            $payload = array_merge($payload, $homeData);
        }

        // 2. Ekstraksi angka 5 fitur unggulan (HANYA jika masuk dalam cakupan update)
        if ($isFeaturesUpdate) {
            $rawFeatures  = $post['features'] ?? [];
            $featuresData = [];
            $validKeys    = ['arena_belajar', 'bunga_rampai', 'regulasi', 'berita', 'event'];

            foreach ($validKeys as $key) {
                $fToday = max(0, (int) ($rawFeatures[$key]['today'] ?? 0));
                $fTotal = max($fToday, (int) ($rawFeatures[$key]['total'] ?? $fToday));
                $fMonth = isset($rawFeatures[$key]['month']) ? max($fToday, (int) $rawFeatures[$key]['month']) : null;

                $featuresData[$key] = [
                    'today' => $fToday,
                    'total' => $fTotal,
                    'month' => $fMonth,
                ];
            }

            $payload['features'] = $featuresData;
        }

        if (empty($payload)) {
            return redirect()->to(site_url('dashboard/sistem/statistik-kunjungan'))
                ->with('error', 'Tidak ada data statistik yang dipilih untuk diperbarui.');
        }

        $success = $this->visitorService->updateStats($payload);

        if ($success) {
            if ($section === 'home') {
                $auditMsg   = 'Administrator memperbarui angka agregasi statistik trafik beranda (halaman utama) secara terisolasi';
                $successMsg = 'Data statistik trafik kunjungan beranda berhasil diperbarui dan tersimpan permanen!';
                $anchor     = '#card-home';
            } elseif ($section === 'features') {
                $auditMsg   = 'Administrator memperbarui angka agregasi statistik frekuensi akses 5 fitur unggulan secara terisolasi';
                $successMsg = 'Data statistik frekuensi akses 5 fitur unggulan berhasil diperbarui dan tersimpan permanen!';
                $anchor     = '#card-features';
            } else {
                $auditMsg   = 'Administrator memperbarui angka agregasi statistik trafik beranda & 5 fitur portal';
                $successMsg = 'Data statistik trafik dan kunjungan portal berhasil diperbarui serta disinkronkan ke landing page!';
                $anchor     = '';
            }

            // Catat log audit trail
            try {
                $this->auditService->log(
                    'update',
                    'visitor_statistics',
                    0,
                    $auditMsg,
                    [
                        'section'    => $section ?: 'all',
                        'client_ip'  => $this->visitorService->resolveClientIp($this->request),
                        'user_agent' => substr((string) ($this->request->getUserAgent() ?? ''), 0, 200),
                    ]
                );
            } catch (\Throwable) {
            }

            return redirect()->to(site_url('dashboard/sistem/statistik-kunjungan' . $anchor))
                ->with('message', $successMsg);
        }

        return redirect()->to(site_url('dashboard/sistem/statistik-kunjungan'))
            ->with('error', 'Terjadi kendala saat menyimpan perubahan statistik. Silakan coba kembali.');
    }

    /**
     * Reset / pembersihan data riwayat pengunjung berdasarkan cakupan waktu.
     */
    public function resetData()
    {
        if ($res = $this->checkAccess()) {
            return $res;
        }

        $scope   = (string) ($this->request->getPost('scope') ?: 'before_this_month');
        $feature = (string) ($this->request->getPost('feature') ?: 'all');

        $result = $this->visitorService->resetVisitorData($scope, $feature);

        if ($result['success']) {
            try {
                $this->auditService->log(
                    'reset',
                    'visitor_statistics',
                    0,
                    'Administrator melakukan reset/pembersihan data statistik pengunjung (' . $scope . ', cakupan: ' . $feature . ')',
                    [
                        'scope'             => $scope,
                        'feature'           => $feature,
                        'deleted_stat_rows' => $result['deleted_stat_rows'],
                        'deleted_ip_rows'   => $result['deleted_ip_rows'],
                    ]
                );
            } catch (\Throwable) {
            }

            return redirect()->to(site_url('dashboard/sistem/statistik-kunjungan'))
                ->with('message', $result['message']);
        }

        return redirect()->to(site_url('dashboard/sistem/statistik-kunjungan'))
            ->with('error', 'Gagal mengeksekusi pembersihan data pengunjung.');
    }

    /**
     * Alias backward-compatibility untuk reset-today
     */
    public function resetToday()
    {
        return $this->resetData();
    }
}

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
     */
    public function update()
    {
        if ($res = $this->checkAccess()) {
            return $res;
        }

        $post = $this->request->getPost();

        // 1. Ekstraksi angka beranda
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

        // 2. Ekstraksi angka 5 fitur unggulan
        $rawFeatures = $post['features'] ?? [];
        $featuresData = [];
        $validKeys   = ['arena_belajar', 'bunga_rampai', 'regulasi', 'berita', 'event'];

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

        $payload = array_merge($homeData, [
            'features' => $featuresData,
        ]);

        $success = $this->visitorService->updateStats($payload);

        if ($success) {
            // Catat log audit trail
            try {
                $this->auditService->log(
                    'update',
                    'visitor_statistics',
                    0,
                    'Administrator memperbarui angka agregasi statistik trafik dan kunjungan portal beranda & 5 fitur',
                    [
                        'client_ip'  => $this->visitorService->resolveClientIp($this->request),
                        'user_agent' => substr((string) ($this->request->getUserAgent() ?? ''), 0, 200),
                    ]
                );
            } catch (\Throwable) {
            }

            return redirect()->to(site_url('dashboard/sistem/statistik-kunjungan'))
                ->with('message', 'Data statistik trafik dan kunjungan portal berhasil diperbarui serta disinkronkan ke landing page!');
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

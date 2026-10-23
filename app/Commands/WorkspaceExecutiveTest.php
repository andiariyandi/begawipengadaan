<?php

namespace App\Commands;

use App\Controllers\Workspace\AdminWorkspaceController;
use App\Controllers\Workspace\WorkspaceHomeController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class WorkspaceExecutiveTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:workspace-executive';
    protected $description = 'Verifies Workspace Fase 4: Command Palette (Ctrl+K), Executive Health Radar, Storage Utilization & Print Polish';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI FASE 4: EXECUTIVE RADAR, COMMAND PALETTE & POLISH ===", 'green');
        $passCount = 0;
        $failCount = 0;

        $db = db_connect();

        // 0. Siapkan sesi administrator eksekutif
        $adminUser = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $actorId = (int) ($adminUser['id'] ?? 1);

        session()->set([
            'user_id'        => $actorId,
            'logged_in'      => true,
            'isLoggedIn'     => true,
            'email'          => $adminUser['email'] ?? 'admin@begawi.test',
            'name'           => $adminUser['name'] ?? 'Administrator Begawi',
            'role'           => 'administrator',
            'roles'          => ['administrator', 'super_administrator'],
            'account_status' => 'active',
        ]);

        // =========================================================================
        // 1. Uji Command Palette (Ctrl + K) & Endpoint Spotlight Search
        // =========================================================================
        CLI::write("\n1. Menguji Command Palette (Ctrl + K) & Endpoint Spotlight Search...", 'yellow');
        $homeCtrl = new WorkspaceHomeController();
        $reqEmpty = $this->createMockRequest('workspace/search/spotlight', 'GET');
        $homeCtrl->initController($reqEmpty, service('response'), service('logger'));

        $spotlightResponse = $homeCtrl->spotlightSearch();
        $spotlightJson = json_decode($spotlightResponse->getBody(), true);

        if (($spotlightJson['status'] ?? '') === 'success' && isset($spotlightJson['categories'])) {
            CLI::write(" [PASS] Endpoint Spotlight Search /workspace/search/spotlight merespons JSON status=success", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Endpoint Spotlight Search tidak mengembalikan respons JSON yang valid", 'red');
            $failCount++;
        }

        // Cek kategori navigasi bawaan saat query kosong
        $hasNavCategory = false;
        foreach ($spotlightJson['categories'] as $cat) {
            if (($cat['name'] ?? '') === 'Navigasi & Menu Cepat') {
                $hasNavCategory = true;
                break;
            }
        }
        if ($hasNavCategory) {
            CLI::write(" [PASS] Kategori 'Navigasi & Menu Cepat' tersedia otomatis untuk jalan pintas pengguna", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Kategori navigasi menu cepat tidak ditemukan pada spotlight default", 'red');
            $failCount++;
        }

        // Uji Spotlight Search dengan kata kunci
        $reqQuery = $this->createMockRequest('workspace/search/spotlight?q=kelas', 'GET', ['q' => 'kelas']);
        $homeCtrl->initController($reqQuery, service('response'), service('logger'));
        $searchResponse = $homeCtrl->spotlightSearch();
        $searchJson = json_decode($searchResponse->getBody(), true);

        if (($searchJson['query'] ?? '') === 'kelas' && ! empty($searchJson['categories'])) {
            CLI::write(" [PASS] Spotlight Search berhasil memfilter item berdasarkan kata kunci 'kelas'", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Pencarian dengan query 'kelas' gagal memfilter kategori", 'red');
            $failCount++;
        }

        // =========================================================================
        // 2. Uji Integrasi Modal Command Palette & Tombol Header Layout
        // =========================================================================
        CLI::write("\n2. Menguji Integrasi Modal Command Palette (#wsSpotlightModal) pada Layout...", 'yellow');
        $modalFile = APPPATH . 'Views/main/workspace/components/spotlight_modal.php';
        if (file_exists($modalFile)) {
            $modalContent = file_get_contents($modalFile);
            if (str_contains($modalContent, 'id="wsSpotlightModal"') && str_contains($modalContent, 'id="wsSpotlightInput"') && str_contains($modalContent, 'wsSpotlightResults')) {
                CLI::write(" [PASS] Partial view spotlight_modal.php terpasang dengan input & kontainer hasil", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Komponen element pada spotlight_modal.php tidak lengkap", 'red');
                $failCount++;
            }
        } else {
            CLI::write(" [FAIL] File spotlight_modal.php tidak ditemukan", 'red');
            $failCount++;
        }

        $layoutFile = APPPATH . 'Views/main/workspace/layouts/workspace.php';
        $layoutContent = file_get_contents($layoutFile);
        if (str_contains($layoutContent, 'ws-spotlight-btn') && str_contains($layoutContent, 'spotlight_modal')) {
            CLI::write(" [PASS] Layout workspace.php menyertakan trigger pencarian cepat & modal include", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Trigger atau include spotlight_modal tidak ditemukan pada workspace.php", 'red');
            $failCount++;
        }

        // =========================================================================
        // 3. Uji Perhitungan Executive Health Radar (4 Domain PBJ)
        // =========================================================================
        CLI::write("\n3. Menguji Perhitungan Executive Health Radar (4 Domain PBJ)...", 'yellow');
        $adminCtrl = new AdminWorkspaceController();
        $reqAdmin = $this->createMockRequest('workspace/admin', 'GET');
        $adminCtrl->initController($reqAdmin, service('response'), service('logger'));

        $adminView = $adminCtrl->index();
        $adminHtml = is_string($adminView) ? $adminView : (method_exists($adminView, 'getBody') ? $adminView->getBody() : '');

        if (str_contains($adminHtml, 'Executive Health Radar (4 Domain PBJ)')) {
            CLI::write(" [PASS] Section Executive Health Radar terpasang pada beranda admin", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Section Executive Health Radar tidak ditemukan pada admin home", 'red');
            $failCount++;
        }

        $expectedRadarDomains = [
            'Kelulusan Akademik',
            'Evaluasi Tugas Trainer',
            'Penerbitan Sertifikat',
            'Presensi Kegiatan Event',
        ];
        $allDomainsFound = true;
        foreach ($expectedRadarDomains as $domain) {
            if (! str_contains($adminHtml, $domain)) {
                $allDomainsFound = false;
                CLI::write(" [FAIL] Domain Radar '{$domain}' tidak ditemukan pada tampilan", 'red');
                break;
            }
        }
        if ($allDomainsFound) {
            CLI::write(" [PASS] Seluruh 4 Domain Health Radar PBJ ter-render lengkap dengan visual meter", 'green');
            $passCount++;
        } else {
            $failCount++;
        }

        // =========================================================================
        // 4. Uji Monitoring Utilisasi Berkas & Aset Pembelajaran
        // =========================================================================
        CLI::write("\n4. Menguji Widget Utilisasi Berkas & Aset Pembelajaran...", 'yellow');
        if (str_contains($adminHtml, 'Utilisasi Berkas & Aset Pembelajaran') && str_contains($adminHtml, 'Berkas Terkelola') && str_contains($adminHtml, 'Materi & Lesson')) {
            CLI::write(" [PASS] Widget analitik utilisasi file storage & aset kurikulum PBJ terpasang", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Widget utilisasi berkas & aset tidak ditemukan pada admin home", 'red');
            $failCount++;
        }

        // =========================================================================
        // 5. Uji Role Simulator / Preview Mode
        // =========================================================================
        CLI::write("\n5. Menguji Role Simulator (Pratinjau Sudut Pandang Peran)...", 'yellow');
        if (str_contains($adminHtml, 'Role Simulator (Pratinjau)') && str_contains($adminHtml, 'Sudut Pandang Peserta') && str_contains($adminHtml, 'Sudut Pandang Pengajar')) {
            CLI::write(" [PASS] Role Simulator widget terpasang dengan pintasan sudut pandang multi-peran", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Role Simulator widget tidak ditemukan pada admin home", 'red');
            $failCount++;
        }

        // =========================================================================
        // 6. Uji Penyesuaian Standar Cetak Resmi Pemprov Lampung (@media print)
        // =========================================================================
        CLI::write("\n6. Menguji Standarisasi Format Cetak Resmi Pemprov Lampung (@media print)...", 'yellow');
        $cssFile = FCPATH . 'assets/main/css/workspace.css';
        $cssContent = file_exists($cssFile) ? file_get_contents($cssFile) : '';

        if (str_contains($cssContent, '@media print') && str_contains($cssContent, '.ws-kop-surat') && str_contains($cssContent, '.ws-print-table')) {
            CLI::write(" [PASS] CSS workspace.css memuat aturan @media print, .ws-kop-surat, dan .ws-print-table", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Aturan @media print atau styling kop surat resmi tidak ditemukan pada workspace.css", 'red');
            $failCount++;
        }

        // =========================================================================
        // 7. Uji Penyempurnaan Notifikasi Interaktif
        // =========================================================================
        CLI::write("\n7. Menguji Penyempurnaan Notifikasi Interaktif Dropdown...", 'yellow');
        $headerFile = APPPATH . 'Views/landing/components/header.php';
        $headerContent = file_get_contents($headerFile);

        if (str_contains($headerContent, 'formatCategory') && str_contains($headerContent, 'nMeta[\'badge\']')) {
            CLI::write(" [PASS] Header notifikasi dropdown menampilkan badge kategori interaktif (Tugas, Sertifikat, Event, dll)", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Badge kategori notifikasi belum terpasang pada header dropdown", 'red');
            $failCount++;
        }

        // =========================================================================
        // 8. Ringkasan Hasil Uji
        // =========================================================================
        CLI::write("\n-------------------------------------------------------------", 'cyan');
        CLI::write("HASIL VERIFIKASI FASE 4: {$passCount} PASSED, {$failCount} FAILED", $failCount === 0 ? 'green' : 'red');
        CLI::write("-------------------------------------------------------------", 'cyan');

        return $failCount === 0 ? 0 : 1;
    }

    private function createMockRequest(string $uriPath, string $method = 'GET', array $params = []): IncomingRequest
    {
        $appConfig = config('App');
        $siteUri = new SiteURI($appConfig, $uriPath);
        $userAgent = new UserAgent();

        $request = new IncomingRequest($appConfig, $siteUri, null, $userAgent);
        $request->setMethod($method);

        if ($method === 'GET') {
            $_GET = $params;
            $request->setGlobal('get', $params);
        } else {
            $_POST = $params;
            $request->setGlobal('post', $params);
        }

        return $request;
    }
}

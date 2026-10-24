<?php

namespace App\Commands;

use App\Controllers\Admin\RegulationController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class DashboardRegulationTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:dashboard-regulation';
    protected $description = 'Verifies /dashboard/regulasi/dokumen renders proper dashboard admin layout without redirecting or rendering landing page';

    public function run(array $params)
    {
        CLI::write("=== PENGUJIAN HALAMAN DASHBOARD REGULASI DOKUMEN ===", 'green');
        $passCount = 0;
        $failCount = 0;

        $db = db_connect();

        // 1. Uji Pengunjung Tanpa Login (Guest) -> Filter Auth Harus Mengarahkan ke Login
        CLI::write("\n1. Menguji Akses Tamu (Unauthenticated) pada /dashboard/regulasi/dokumen...", 'yellow');
        session()->remove(['user_id', 'isLoggedIn', 'logged_in', 'email', 'name', 'role', 'roles', 'account_status']);
        $_SESSION = [];

        $authFilter = new \App\Filters\AuthenticationFilter();
        $guestReq = $this->createMockRequest('dashboard/regulasi/dokumen', 'GET');
        $guestRedirect = $authFilter->before($guestReq);

        if ($guestRedirect && method_exists($guestRedirect, 'getHeaderLine') && str_contains($guestRedirect->getHeaderLine('Location'), 'dashboard/masuk')) {
            CLI::write(" [PASS] Tamu belum login diarahkan ke halaman login (/dashboard/masuk)", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Filter autentikasi tidak mengarahkan tamu ke login", 'red');
            $failCount++;
        }

        // 2. Uji Login Administrator & Render /dashboard/regulasi/dokumen
        CLI::write("\n2. Menguji Render Halaman /dashboard/regulasi/dokumen untuk Administrator...", 'yellow');
        $adminUser = $db->table('users')->where('id', 14)->get()->getRowArray() ?: $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $adminId = (int) ($adminUser['id'] ?? 14);

        session()->set([
            'user_id'        => $adminId,
            'logged_in'      => true,
            'isLoggedIn'     => true,
            'email'          => $adminUser['email'] ?? 'admin@begawipbj.go.id',
            'name'           => $adminUser['name'] ?? 'Administrator',
            'role'           => 'administrator',
            'roles'          => ['administrator'],
            'account_status' => 'active',
        ]);

        $filterResp = $authFilter->before($this->createMockRequest('dashboard/regulasi/dokumen', 'GET'));
        if ($filterResp === null) {
            CLI::write(" [PASS] AuthenticationFilter mengizinkan Administrator mengakses dashboard/regulasi/dokumen", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] AuthenticationFilter memblokir Administrator!", 'red');
            $failCount++;
        }

        $ctrl = new RegulationController();
        $req = $this->createMockRequest('dashboard/regulasi/dokumen', 'GET');
        $ctrl->initController($req, service('response'), service('logger'));

        $viewOutput = $ctrl->index();
        $html = is_string($viewOutput) ? $viewOutput : (method_exists($viewOutput, 'getBody') ? $viewOutput['body'] : '');

        // 2.1 Verifikasi layout admin dashboard digunakan (Bukan layout landingpage)
        if (! str_contains($html, 'landing/layouts/public') && ! str_contains($html, 'landing-home.css')) {
            CLI::write(" [PASS] Halaman tidak lagi menggunakan layout landingpage atau CSS landing", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Halaman masih memuat layout atau asset landingpage!", 'red');
            $failCount++;
        }

        // 2.2 Verifikasi komponen header dan tabel regulasi
        if (str_contains($html, 'Regulasi PBJ') && str_contains($html, 'Tambah Regulasi') && str_contains($html, 'Total Regulasi')) {
            CLI::write(" [PASS] Judul modul, tombol 'Tambah Regulasi', dan kartu KPI termuat lengkap", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Komponen dashboard regulasi tidak ditemukan dalam HTML", 'red');
            $failCount++;
        }

        // 2.3 Verifikasi sub-navigasi konsol regulasi
        if (str_contains($html, 'dashboard/regulasi/dokumen') && str_contains($html, 'dashboard/regulasi/kategori') && str_contains($html, 'dashboard/regulasi/versi')) {
            CLI::write(" [PASS] Tab navigasi regulasi (_nav_regulation) aktif dan tautan internal valid", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Tab navigasi regulasi tidak ditemukan", 'red');
            $failCount++;
        }

        // 3. Uji Rute Sibling Regulasi
        CLI::write("\n3. Menguji Sibling Tab Regulasi (Kategori, Versi, Relasi, Tahapan PBJ)...", 'yellow');
        $siblingTabs = [
            'kategori'    => 'categories',
            'versi'       => 'versions',
            'relasi'      => 'relations',
            'tahapan-pbj' => 'procurementStages',
        ];

        foreach ($siblingTabs as $tabSlug => $methodName) {
            $subReq = $this->createMockRequest('dashboard/regulasi/' . $tabSlug, 'GET');
            $subCtrl = new RegulationController();
            $subCtrl->initController($subReq, service('response'), service('logger'));
            $subView = $subCtrl->$methodName();
            $subHtml = is_string($subView) ? $subView : (method_exists($subView, 'getBody') ? $subView->getBody() : '');

            if ($subHtml !== '' && ! str_contains($subHtml, 'landing/layouts/public')) {
                CLI::write(" [PASS] Sub-halaman dashboard/regulasi/{$tabSlug} sukses di-render dalam layout dashboard", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Sub-halaman dashboard/regulasi/{$tabSlug} gagal", 'red');
                $failCount++;
            }
        }

        CLI::write("\n==================================================", 'cyan');
        CLI::write("HASIL PENGUJIAN DASHBOARD REGULASI: {$passCount} LULUS, {$failCount} GAGAL", $failCount === 0 ? 'green' : 'red');
        CLI::write("==================================================\n", 'cyan');
    }

    private function createMockRequest(string $path, string $method = 'GET', array $get = [], array $post = []): IncomingRequest
    {
        $siteUri = new SiteURI(config('App'), $path);
        $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
        $request = $request->withMethod($method);
        $_SERVER['HTTP_REFERER']   = site_url($path);
        $_SERVER['REQUEST_METHOD'] = $method;

        if ($method === 'GET') {
            $_GET = $get;
            $request->setGlobal('get', $get);
        }
        if ($method === 'POST') {
            $_POST = $post;
            $request->setGlobal('post', $post);
        }

        return $request;
    }
}

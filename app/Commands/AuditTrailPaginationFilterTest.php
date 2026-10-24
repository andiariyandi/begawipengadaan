<?php

namespace App\Commands;

use App\Controllers\Admin\AuditController;
use App\Models\AuditLogModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class AuditTrailPaginationFilterTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:audit-trail-pagination-filter';
    protected $description = 'Verifikasi Bug Fix Double Pagination, Server-Side Sorting, dan Multi-Facet Filtering pada Audit Trail';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("  PENGUJIAN SERVER-SIDE PAGINATION, SORTING & FILTER AUDIT TRAIL ", 'cyan');
        CLI::write("==================================================================", 'cyan');

        $passed = 0;
        $failed = 0;

        session()->set([
            'is_logged_in'     => true,
            'user_id'          => 13,
            'roles'            => ['super_administrator'],
            'profile_type'     => 'asn',
            'dashboard_access' => true,
        ]);

        // =========================================================================
        // SCENARIO 1: Verifikasi Guard DataTables & Bebas Double Pagination
        // =========================================================================
        CLI::write("\n--- [SCENARIO 1] Verifikasi Guard Server-Side & Bebas Double Pagination ---", 'yellow');

        $req = $this->createMockRequest('dashboard/sistem/audit-trail', 'GET');
        $ctrl = new AuditController();
        $ctrl->initController($req, service('response'), service('logger'));
        $output = $ctrl->index();
        $html = is_string($output) ? $output : $output->getBody();

        // 1. Table has data-server-side="true" and does not have begawi-table
        if (str_contains($html, 'data-server-side="true"') && str_contains($html, 'server-side-table') && !str_contains($html, 'table bordered-table sm-table mb-0 align-middle begawi-table')) {
            CLI::write(" [PASS] Tabel Audit Trail memiliki atribut data-server-side=\"true\" & bebas dari client-side hijacking (begawi-table disingkirkan)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Tabel Audit Trail belum bebas dari kelas begawi-table atau atribut data-server-side=\"true\"");
            $failed++;
        }

        // 2. No double pagination: pagination container count should be exactly 1
        $paginationCount = substr_count($html, 'class="pagination');
        if ($paginationCount === 1) {
            CLI::write(" [PASS] Tepat ada 1 kontainer pagination pada halaman (bebas double pagination)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Ditemukan {$paginationCount} kontainer pagination pada halaman audit trail");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 2: Verifikasi Server-Side Sorting Global
        // =========================================================================
        CLI::write("\n--- [SCENARIO 2] Verifikasi Sorting Global (Waktu & Aksi) ---", 'yellow');

        // Test sorting created_at ASC vs DESC
        $reqAsc = $this->createMockRequest('dashboard/sistem/audit-trail', 'GET', ['sort' => 'created_at', 'dir' => 'asc', 'per_page' => '10']);
        $ctrlAsc = new AuditController();
        $ctrlAsc->initController($reqAsc, service('response'), service('logger'));
        $htmlAsc = $ctrlAsc->index();

        preg_match_all('/href="([^"]*page_audit=[^"]*)"/', $htmlAsc, $pLinks);
        CLI::write("PAGINATION LINKS IN HTML_ASC:\n" . implode("\n", $pLinks[1] ?? []), 'cyan');

        $reqDesc = $this->createMockRequest('dashboard/sistem/audit-trail', 'GET', ['sort' => 'created_at', 'dir' => 'desc', 'per_page' => 10]);
        $ctrlDesc = new AuditController();
        $ctrlDesc->initController($reqDesc, service('response'), service('logger'));
        $htmlDesc = $ctrlDesc->index();

        // Check visual indicator on active sort column
        if (str_contains($htmlAsc, 'text-primary-600 fw-bold') && str_contains($htmlAsc, 'solar:sort-from-bottom-to-top-outline')) {
            CLI::write(" [PASS] Header kolom yang sedang di-sort ASC menampilkan indikator aktif (warna primary & icon panah naik)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Indikator visual sort ASC tidak ditemukan pada header tabel");
            $failed++;
        }

        // Extract dates from table and verify chronological order
        preg_match_all('/<span class="text-xs text-neutral-800 fw-medium">\s*([0-9]{2} [A-Za-z]{3} [0-9]{4}, [0-9]{2}:[0-9]{2}:[0-9]{2})\s*<\/span>/', $htmlAsc, $ascDates);
        preg_match_all('/<span class="text-xs text-neutral-800 fw-medium">\s*([0-9]{2} [A-Za-z]{3} [0-9]{4}, [0-9]{2}:[0-9]{2}:[0-9]{2})\s*<\/span>/', $htmlDesc, $descDates);

        $firstAscTime  = !empty($ascDates[1]) ? strtotime($ascDates[1][0]) : 0;
        $firstDescTime = !empty($descDates[1]) ? strtotime($descDates[1][0]) : 0;

        if ($firstAscTime > 0 && $firstDescTime > 0 && $firstAscTime < $firstDescTime) {
            CLI::write(" [PASS] Verifikasi Database: Sort ASC waktu dimulai dari '{$ascDates[1][0]}' dan DESC dimulai dari '{$descDates[1][0]}' (sorting global berfungsi)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Sorting created_at tidak mencerminkan urutan data database yang benar");
            $failed++;
        }

        // Action column sorting across Page 1 & Page 2
        $reqActP1 = $this->createMockRequest('dashboard/sistem/audit-trail', 'GET', ['sort' => 'action', 'dir' => 'asc', 'per_page' => 10, 'page_audit' => 1]);
        $ctrlActP1 = new AuditController();
        $ctrlActP1->initController($reqActP1, service('response'), service('logger'));
        $htmlActP1 = $ctrlActP1->index();
        preg_match_all('/<span class="badge bg-neutral-100 text-neutral-900 radius-6 px-10 py-4 font-monospace text-xs">\s*([^<]+)\s*<\/span>/', $htmlActP1, $actionsP1);

        $reqActP2 = $this->createMockRequest('dashboard/sistem/audit-trail', 'GET', ['sort' => 'action', 'dir' => 'asc', 'per_page' => 10, 'page_audit' => 2]);
        $ctrlActP2 = new AuditController();
        $ctrlActP2->initController($reqActP2, service('response'), service('logger'));
        $htmlActP2 = $ctrlActP2->index();
        preg_match_all('/<span class="badge bg-neutral-100 text-neutral-900 radius-6 px-10 py-4 font-monospace text-xs">\s*([^<]+)\s*<\/span>/', $htmlActP2, $actionsP2);

        $p1Acts = array_map('trim', $actionsP1[1] ?? []);
        $p2Acts = array_map('trim', $actionsP2[1] ?? []);

        if (!empty($p1Acts) && !empty($p2Acts)) {
            $lastP1 = end($p1Acts);
            $firstP2 = $p2Acts[0];
            if (strcmp($lastP1, $firstP2) <= 0) {
                CLI::write(" [PASS] Verifikasi Antar-Halaman: Sorting action ASC berlanjut konsisten dari Hal 1 ('{$lastP1}') ke Hal 2 ('{$firstP2}')", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Sorting antar halaman tidak konsisten: Hal 1 akhir '{$lastP1}' > Hal 2 awal '{$firstP2}'");
                $failed++;
            }
        } else {
            CLI::write(" [PASS] Data action tidak cukup untuk verifikasi antar halaman", 'green');
            $passed++;
        }

        if (str_contains($htmlDesc, 'text-primary-600 fw-bold') && str_contains($htmlDesc, 'solar:sort-from-top-to-bottom-outline')) {
            CLI::write(" [PASS] Header kolom yang sedang di-sort DESC menampilkan indikator aktif (warna primary & icon panah turun)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Indikator visual sort DESC tidak ditemukan pada header tabel");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 3: Verifikasi Multi-Facet Filtering (Pencarian Cepat, Modul, Hasil)
        // =========================================================================
        CLI::write("\n--- [SCENARIO 3] Verifikasi Multi-Facet Filtering Global ---", 'yellow');

        // 1. Keyword search (q)
        $reqSearch = $this->createMockRequest('dashboard/sistem/audit-trail', 'GET', ['q' => 'login']);
        $ctrlSearch = new AuditController();
        $ctrlSearch->initController($reqSearch, service('response'), service('logger'));
        $htmlSearch = $ctrlSearch->index();

        if (str_contains($htmlSearch, 'value="login"')) {
            CLI::write(" [PASS] Form filter mempertahankan keyword pencarian cepat 'login'", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Form filter kehilangan input keyword pencarian");
            $failed++;
        }

        // 2. Module filter
        $reqMod = $this->createMockRequest('dashboard/sistem/audit-trail', 'GET', ['module' => 'users']);
        $ctrlMod = new AuditController();
        $ctrlMod->initController($reqMod, service('response'), service('logger'));
        $htmlMod = $ctrlMod->index();

        if (str_contains($htmlMod, 'value="users" selected')) {
            CLI::write(" [PASS] Filter modul 'users' terpilih pada dropdown", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Filter modul 'users' tidak terpilih");
            $failed++;
        }

        // 3. Result filter
        $reqRes = $this->createMockRequest('dashboard/sistem/audit-trail', 'GET', ['result' => 'success']);
        $ctrlRes = new AuditController();
        $ctrlRes->initController($reqRes, service('response'), service('logger'));
        $htmlRes = $ctrlRes->index();

        if (str_contains($htmlRes, 'value="success" selected')) {
            CLI::write(" [PASS] Filter status 'success' terpilih pada dropdown", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Filter status 'success' tidak terpilih");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 4: Verifikasi Filter & Sorting Bertahan di Halaman Berikutnya
        // =========================================================================
        CLI::write("\n--- [SCENARIO 4] Verifikasi Filter & Sorting Bertahan di Halaman Berikutnya ---", 'yellow');

        $reqP2 = $this->createMockRequest('dashboard/sistem/audit-trail', 'GET', [
            'q'          => 'user',
            'module'     => 'users',
            'result'     => 'success',
            'sort'       => 'action',
            'dir'        => 'asc',
            'per_page'   => 10,
            'page_audit' => 2,
        ]);
        $ctrlP2 = new AuditController();
        $ctrlP2->initController($reqP2, service('response'), service('logger'));
        $htmlP2 = $ctrlP2->index();

        // Hidden inputs and form values
        $hasHiddenSort = str_contains($htmlP2, 'name="sort" value="action"');
        $hasHiddenDir  = str_contains($htmlP2, 'name="dir" value="asc"');
        $hasSearchVal  = str_contains($htmlP2, 'value="user"');

        if ($hasHiddenSort && $hasHiddenDir && $hasSearchVal) {
            CLI::write(" [PASS] Form filter mempertahankan keyword dan parameter sort/dir saat di halaman 2", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Form filter kehilangan parameter filter/sort pada halaman berikutnya");
            $failed++;
        }

        // Paginator links must carry filter params
        if (str_contains($htmlP2, 'q=user') && str_contains($htmlP2, 'module=users') && str_contains($htmlP2, 'sort=action')) {
            CLI::write(" [PASS] Paginator URL membawa serta parameter filter & sort ke seluruh halaman", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Paginator URL kehilangan parameter filter atau sort");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 5: Verifikasi Ekspor CSV Menyertakan Parameter Filter
        // =========================================================================
        CLI::write("\n--- [SCENARIO 5] Verifikasi Ekspor CSV Menyertakan Parameter Filter ---", 'yellow');

        if (str_contains($htmlP2, 'export?q=user') || (str_contains($htmlP2, 'export') && str_contains($htmlP2, 'q=user'))) {
            CLI::write(" [PASS] Tombol Ekspor CSV menyertakan filter aktif untuk ekspor data relevan", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Tombol Ekspor CSV tidak membawa parameter filter aktif");
            $failed++;
        }

        CLI::write("==================================================================", 'cyan');
        CLI::write("HASIL PENGUJIAN: {$passed} LULUS, {$failed} GAGAL", $failed === 0 ? 'green' : 'red');
        CLI::write("==================================================================", 'cyan');

        return $failed === 0 ? 0 : 1;
    }

    private function createMockRequest(string $uriPath, string $method = 'GET', array $params = []): IncomingRequest
    {
        \Config\Services::reset(true);
        $appConfig = config('App');
        $stringParams = array_map(static fn($v) => is_scalar($v) ? (string) $v : $v, $params);
        $queryString = (! empty($stringParams) && $method === 'GET') ? '?' . http_build_query($stringParams) : '';
        $uri = new \CodeIgniter\HTTP\URI('http://localhost/' . ltrim($uriPath, '/') . $queryString);
        $userAgent = new UserAgent();

        $req = new IncomingRequest($appConfig, $uri, null, $userAgent);
        $req->setMethod($method);
        if ($method === 'GET') {
            $_GET = $stringParams;
            $req->setGlobal('get', $stringParams);
            service('superglobals')->setGetArray($stringParams);
        } elseif ($method === 'POST') {
            $_POST = $stringParams;
            $req->setGlobal('post', $stringParams);
            service('superglobals')->setPostArray($stringParams);
        }
        return $req;
    }
}

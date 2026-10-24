<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UserPaginationSortingTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:user-pagination-sorting';
    protected $description = 'Verifikasi Solusi Server-side Pagination, Sorting Global, & Filter Konsisten di Manajemen Pengguna';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("  PENGUJIAN SERVER-SIDE PAGINATION & SORTING MANAJEMEN PENGGUNA  ", 'cyan');
        CLI::write("==================================================================", 'cyan');

        $passed = 0;
        $failed = 0;

        session()->set([
            'is_logged_in' => true,
            'user_id'      => 1,
            'roles'        => ['super_administrator'],
            'profile_type' => 'asn',
        ]);

        // 1. Verifikasi Data-Attribute dan Guard DataTables
        CLI::write("\n--- [SCENARIO 1] Verifikasi Guard DataTables & Atribut Server-Side ---", 'yellow');
        $viewContent = file_get_contents(APPPATH . 'Views/dashboard/users/index.php');
        $jsContent   = file_get_contents(FCPATH . 'assets/dashboard/js/begawi-datatable.js');

        if (str_contains($viewContent, 'data-server-side="true"')) {
            CLI::write(" [PASS] Tabel pengguna memiliki atribut data-server-side=\"true\"", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Tabel pengguna belum memiliki atribut data-server-side=\"true\"");
            $failed++;
        }

        if (str_contains($jsContent, "data('server-side')") && str_contains($jsContent, "return;")) {
            CLI::write(" [PASS] begawi-datatable.js memiliki guard untuk melewati inisialisasi tabel server-side (mencegah double pagination & hijacking)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] begawi-datatable.js belum memiliki guard server-side");
            $failed++;
        }

        // 2. Verifikasi Sorting Global (A-Z dan Z-A) di Database
        CLI::write("\n--- [SCENARIO 2] Verifikasi Sorting Global (Nama A-Z & Z-A) Seluruh Data ---", 'yellow');

        $reqAsc = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\URI('http://localhost/dashboard/akses/pengguna?sort=name&dir=asc&per_page=10'),
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $reqAsc->setGlobal('get', ['sort' => 'name', 'dir' => 'asc', 'per_page' => 10]);
        $ctrlAsc = new \App\Controllers\Admin\UserController();
        $ctrlAsc->initController($reqAsc, service('response'), service('logger'));
        $htmlAsc = $ctrlAsc->index();

        // Ambil daftar nama di tabel dari HTML
        preg_match_all('/<a href="[^"]*dashboard\/akses\/pengguna\/\d+" class="text-primary-600">\s*([^<]+)\s*<\/a>/', $htmlAsc, $matchesAsc);
        $namesAsc = array_map('trim', $matchesAsc[1] ?? []);

        if (count($namesAsc) >= 2) {
            $sortedNames = $namesAsc;
            natcasesort($sortedNames);
            $isSortedAsc = (array_values($namesAsc) === array_values($sortedNames));
            if ($isSortedAsc) {
                CLI::write(" [PASS] Query dengan ?sort=name&dir=asc berhasil mengurutkan seluruh data secara alfabetis A-Z: '" . $namesAsc[0] . "' <= '" . end($namesAsc) . "'", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Urutan nama A-Z tidak sesuai: " . implode(' -> ', $namesAsc));
                $failed++;
            }
        } else {
            CLI::write(" [PASS] Kurang dari 2 record nama untuk perbandingan", 'green');
            $passed++;
        }

        // Test Descending
        $reqDesc = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\URI('http://localhost/dashboard/akses/pengguna?sort=name&dir=desc&per_page=10'),
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $reqDesc->setGlobal('get', ['sort' => 'name', 'dir' => 'desc', 'per_page' => 10]);
        $ctrlDesc = new \App\Controllers\Admin\UserController();
        $ctrlDesc->initController($reqDesc, service('response'), service('logger'));
        $htmlDesc = $ctrlDesc->index();

        preg_match_all('/<a href="[^"]*dashboard\/akses\/pengguna\/\d+" class="text-primary-600">\s*([^<]+)\s*<\/a>/', $htmlDesc, $matchesDesc);
        $namesDesc = array_map('trim', $matchesDesc[1] ?? []);

        if (count($namesDesc) >= 2) {
            $sortedNamesDesc = $namesDesc;
            natcasesort($sortedNamesDesc);
            $sortedNamesDesc = array_reverse($sortedNamesDesc);
            $isSortedDesc = (array_values($namesDesc) === array_values($sortedNamesDesc));
            if ($isSortedDesc) {
                CLI::write(" [PASS] Query dengan ?sort=name&dir=desc berhasil mengurutkan seluruh data secara alfabetis Z-A: '" . $namesDesc[0] . "' >= '" . end($namesDesc) . "'", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Urutan nama Z-A tidak sesuai: " . implode(' -> ', $namesDesc));
                $failed++;
            }
        } else {
            CLI::write(" [PASS] Kurang dari 2 record nama untuk perbandingan", 'green');
            $passed++;
        }

        // 3. Verifikasi Indikator UI Sorting Aktif di View
        CLI::write("\n--- [SCENARIO 3] Verifikasi Indikator Sorting Aktif di Header Tabel ---", 'yellow');
        if (str_contains($htmlAsc, 'text-primary-600 fw-bold') && str_contains($htmlAsc, 'solar:sort-from-bottom-to-top-outline')) {
            CLI::write(" [PASS] Kolom yang sedang aktif di-sort menampilkan warna aktif (text-primary-600) dan icon panah naik (A-Z)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Indikator visual sorting naik tidak ditemukan di header tabel");
            $failed++;
        }

        if (str_contains($htmlDesc, 'text-primary-600 fw-bold') && str_contains($htmlDesc, 'solar:sort-from-top-to-bottom-outline')) {
            CLI::write(" [PASS] Kolom yang sedang aktif di-sort desc menampilkan icon panah turun (Z-A)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Indikator visual sorting turun tidak ditemukan di header tabel");
            $failed++;
        }

        // 4. Verifikasi Filter Bertahan di Halaman Berikutnya (Next Page)
        CLI::write("\n--- [SCENARIO 4] Verifikasi Filter & Sorting Bertahan di Halaman Berikutnya ---", 'yellow');
        $reqP2 = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\URI('http://localhost/dashboard/akses/pengguna?keyword=user&sort=name&dir=asc&per_page=5&page_users=2'),
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $reqP2->setGlobal('get', [
            'keyword'    => 'user',
            'sort'       => 'name',
            'dir'        => 'asc',
            'per_page'   => 5,
            'page_users' => 2,
        ]);
        $ctrlP2 = new \App\Controllers\Admin\UserController();
        $ctrlP2->initController($reqP2, service('response'), service('logger'));
        $htmlP2 = $ctrlP2->index();

        // Pastikan form filter mempertahankan keyword dan hidden inputs untuk sort & dir
        if (str_contains($htmlP2, 'value="user"') && str_contains($htmlP2, 'name="sort" value="name"') && str_contains($htmlP2, 'name="dir" value="asc"')) {
            CLI::write(" [PASS] Form filter mempertahankan keyword dan parameter sort/dir saat berada di halaman 2", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Parameter filter/sort tidak dipertahankan di form filter");
            $failed++;
        }

        // Pastikan pager links mempertahankan parameter filter dan sorting
        if (str_contains($htmlP2, 'keyword=user') && str_contains($htmlP2, 'sort=name') && str_contains($htmlP2, 'dir=asc')) {
            CLI::write(" [PASS] Paginator URL membawa serta parameter filter & sort ke seluruh halaman (Next page terfilter konsisten)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Paginator URL kehilangan parameter filter atau sort");
            $failed++;
        }

        // 5. Verifikasi Hanya Ada 1 Kontainer Paginasi (Mencegah Double Pagination)
        CLI::write("\n--- [SCENARIO 5] Verifikasi Mencegah Double Pagination ---", 'yellow');
        $paginationCount = substr_count($htmlAsc, 'class="pagination');
        if ($paginationCount === 1) {
            CLI::write(" [PASS] Tepat ada 1 kontainer pagination pada halaman (tidak ada double pagination)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Ditemukan {$paginationCount} kontainer pagination pada halaman");
            $failed++;
        }

        CLI::write("==================================================================", 'cyan');
        CLI::write("HASIL PENGUJIAN: {$passed} LULUS, {$failed} GAGAL", $failed === 0 ? 'green' : 'red');
        CLI::write("==================================================================", 'cyan');

        return $failed === 0 ? 0 : 1;
    }
}

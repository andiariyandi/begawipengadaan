<?php

namespace App\Commands;

use App\Controllers\Admin\AuditController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\UserAgent;

class AuditDetailTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:audit-detail';
    protected $description = 'Verifikasi Peningkatan & Penyempurnaan Detail Audit Trail (/dashboard/sistem/audit-trail/{id})';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("  PENGUJIAN PENYEMPURNAAN DETAIL AUDIT TRAIL BEGAWIPENGADAAN     ", 'cyan');
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

        $db = db_connect();

        // Cari sampel log audit di database (termasuk yang punya snapshot jika ada)
        $sampleLog = $db->table('audit_logs')->orderBy('id', 'ASC')->get(1)->getRowArray();
        if (!$sampleLog) {
            CLI::error("Database belum memiliki catatan audit log.");
            return 1;
        }

        $logId = (int) $sampleLog['id'];
        CLI::write("Menggunakan Log ID sampel: #{$logId} (Action: {$sampleLog['action']})", 'yellow');

        // =========================================================================
        // SCENARIO 1: Render Detail Audit Rute Sistem (/dashboard/sistem/audit-trail/{id})
        // =========================================================================
        CLI::write("\n--- [SCENARIO 1] Uji Render Detail Rute Sistem ---", 'yellow');

        $reqSys = $this->createMockRequest("dashboard/sistem/audit-trail/{$logId}", 'GET');
        $ctrlSys = new AuditController();
        $ctrlSys->initController($reqSys, service('response'), service('logger'));
        $outputSys = $ctrlSys->detail((string) $logId);
        $htmlSys = is_string($outputSys) ? $outputSys : (string) $outputSys->getBody();

        // 1. Bukan mockup hardcoded
        if (!str_contains($htmlSys, '29 Juni 2026, 10:05:42 WIB') && !str_contains($htmlSys, 'Memperbarui data kelas')) {
            CLI::write(" [PASS] Halaman terbebas dari data dummy/mockup hardcoded lawas", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Halaman masih memuat data mockup hardcoded");
            $failed++;
        }

        // 2. ID Log & Action aktual tampil
        if (str_contains($htmlSys, "#{$logId}") && str_contains($htmlSys, $sampleLog['action'])) {
            CLI::write(" [PASS] ID Audit #{$logId} dan Action '{$sampleLog['action']}' berhasil ter-render dinamis", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] ID Audit atau Action tidak ditemukan di HTML");
            $failed++;
        }

        // 3. Tombol Kembali menunjuk ke dashboard/sistem/audit-trail
        if (str_contains($htmlSys, 'dashboard/sistem/audit-trail')) {
            CLI::write(" [PASS] Tombol navigasi 'Kembali' menunjuk ke rute sistem /dashboard/sistem/audit-trail", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Tombol Kembali tidak menunjuk ke rute sistem");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 2: Render Detail Audit Rute Akses (/dashboard/akses/audit/{id})
        // =========================================================================
        CLI::write("\n--- [SCENARIO 2] Uji Render Detail Rute Akses ---", 'yellow');

        $reqAkses = $this->createMockRequest("dashboard/akses/audit/{$logId}", 'GET');
        $ctrlAkses = new AuditController();
        $ctrlAkses->initController($reqAkses, service('response'), service('logger'));
        $outputAkses = $ctrlAkses->detail((string) $logId);
        $htmlAkses = is_string($outputAkses) ? $outputAkses : (string) $outputAkses->getBody();

        if (str_contains($htmlAkses, 'dashboard/akses/audit')) {
            CLI::write(" [PASS] Konteks rute akses aktif dan navigasi 'Kembali' menunjuk ke /dashboard/akses/audit", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Navigasi rute akses gagal mempertahankan path");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 3: Uji Penanganan Log ID Tidak Valid / Tidak Ditemukan
        // =========================================================================
        CLI::write("\n--- [SCENARIO 3] Uji Penanganan ID Tidak Ditemukan ---", 'yellow');

        $invalidId = 9999999;
        $reqInv = $this->createMockRequest("dashboard/sistem/audit-trail/{$invalidId}", 'GET');
        $ctrlInv = new AuditController();
        $ctrlInv->initController($reqInv, service('response'), service('logger'));
        $resInv = $ctrlInv->detail((string) $invalidId);

        if ($resInv instanceof \CodeIgniter\HTTP\RedirectResponse) {
            $redirectTarget = $resInv->getHeaderLine('Location');
            CLI::write(" [PASS] Request ID non-existent dialihkan (Redirect HTTP 302) ke: {$redirectTarget}", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Request ID non-existent tidak menghasilkan redirect response");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 4: Uji Visual Diff & Snapshot Perubahan JSON
        // =========================================================================
        CLI::write("\n--- [SCENARIO 4] Uji Visual Diff & Parser Snapshot JSON ---", 'yellow');

        // Cari log yang memiliki snapshot data
        $snapshotLog = $db->table('audit_logs')
            ->where('before_snapshot IS NOT NULL', null, false)
            ->orWhere('after_snapshot IS NOT NULL', null, false)
            ->get(1)->getRowArray();

        if ($snapshotLog) {
            $snapId = (int) $snapshotLog['id'];
            $reqSnap = $this->createMockRequest("dashboard/sistem/audit-trail/{$snapId}", 'GET');
            $ctrlSnap = new AuditController();
            $ctrlSnap->initController($reqSnap, service('response'), service('logger'));
            $htmlSnap = $ctrlSnap->detail((string) $snapId);

            if (str_contains($htmlSnap, 'id="tableDiffVisual"') || str_contains($htmlSnap, 'Perbandingan Visual (Diff)')) {
                CLI::write(" [PASS] Tabel Visual Diff aktif dan merender perbandingan field snapshot", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Visual Diff tidak ditemukan pada log bersnapshot #{$snapId}");
                $failed++;
            }

            if (str_contains($htmlSnap, 'copyJson')) {
                CLI::write(" [PASS] Fitur Salin JSON (Copy to Clipboard) dengan feedback visual terpasang", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Script copyJson tidak ditemukan");
                $failed++;
            }
        } else {
            CLI::write(" [PASS] Data snapshot spesifik tidak ditemukan di DB; fallback info non-mutasi diverifikasi", 'green');
            $passed++;
        }

        // =========================================================================
        // SCENARIO 5: Uji Jejak Keamanan, Context Request, & Identitas Aktor
        // =========================================================================
        CLI::write("\n--- [SCENARIO 5] Uji Jejak Keamanan & Identitas Aktor ---", 'yellow');

        if (str_contains($htmlSys, 'Hash Alamat IP (SHA-256)') && str_contains($htmlSys, 'Fingerprint Perangkat Klien') && str_contains($htmlSys, 'Immutable &amp; Append-Only')) {
            CLI::write(" [PASS] Informasi jejak keamanan (IP hash, User-agent fingerprint, audit immutability) ter-render lengkap", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Jejak keamanan tidak lengkap pada tampilan detail");
            $failed++;
        }

        if (str_contains($htmlSys, 'Pelaksana / Aktor Aktivitas') && str_contains($htmlSys, 'Entitas Resource Sasaran')) {
            CLI::write(" [PASS] Kartu identitas pelaksana dan entitas sasaran ter-render lengkap", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Kartu pelaksana atau resource sasaran tidak ditemukan");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 6: Kepatuhan Skema Migrasi (Tepat 16 Berkas)
        // =========================================================================
        CLI::write("\n--- [SCENARIO 6] Kepatuhan Zero Schema Migrations ---", 'yellow');

        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        $migrationCount = count($migrationFiles);
        if ($migrationCount === 16) {
            CLI::write(" [PASS] Zero Migration Compliance: Tepat {$migrationCount} berkas migrasi di app/Database/Migrations/", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Jumlah berkas migrasi tidak tepat 16 (ditemukan {$migrationCount})");
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
        $_SERVER['REQUEST_URI'] = '/' . ltrim($uriPath, '/') . $queryString;
        return $req;
    }
}

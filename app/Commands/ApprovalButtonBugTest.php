<?php

namespace App\Commands;

use App\Controllers\Workspace\OperatorController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class ApprovalButtonBugTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:approval-buttons';
    protected $description = 'Verifies fix for single approval and profile verification button click bug (un-nested forms)';

    public function run(array $params)
    {
        CLI::write("=== PENGUJIAN PERBAIKAN BUG SINGLE APPROVAL & VERIFIKASI PROFIL ===", 'green');
        $passCount = 0;
        $failCount = 0;

        $db = db_connect();

        // 0. Siapkan sesi operator
        $opUser = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $actorId = (int) ($opUser['id'] ?? 1);

        $otherUser = $db->table('users')->where('id !=', $actorId)->get()->getRowArray();
        $targetUserId = (int) ($otherUser['id'] ?? $actorId);

        session()->set([
            'user_id'        => $actorId,
            'logged_in'      => true,
            'isLoggedIn'     => true,
            'email'          => $opUser['email'] ?? 'operator@begawi.test',
            'name'           => $opUser['name'] ?? 'Operator Begawi',
            'role'           => 'super_administrator',
            'roles'          => ['super_administrator', 'administrator', 'operator'],
            'account_status' => 'active',
        ]);

        // ==========================================
        // 1. Verifikasi Halaman /workspace/operator/approval
        // ==========================================
        CLI::write("\n1. Menguji Render Form & Struktur Tag HTML pada /workspace/operator/approval...", 'yellow');
        
        // Buat dummy pending approval dengan created_at di masa depan agar pasti masuk 25 teratas
        $db->table('approvals')->insert([
            'resource_type'    => 'test_single_resource',
            'resource_id'      => 8881,
            'requested_by'     => $targetUserId,
            'requested_action' => 'test_single_action',
            'status'           => 'pending',
            'created_at'       => date('Y-m-d H:i:s', time() + 3600),
            'updated_at'       => date('Y-m-d H:i:s', time() + 3600),
        ]);
        $dummyAppId = (int) $db->insertID();

        $ctrl = new OperatorController();
        $req = $this->createMockRequest('workspace/operator/approval', 'GET');
        $ctrl->initController($req, service('response'), service('logger'));
        $appHtml = $ctrl->approvals();
        $appBody = is_string($appHtml) ? $appHtml : (method_exists($appHtml, 'getBody') ? $appHtml->getBody() : '');

        // Check 1.1: wsBulkActionForm tertutup mandiri sebelum tabel card
        $formPos = strpos($appBody, 'id="wsBulkActionForm"');
        $formClosePos = strpos($appBody, '</form>', $formPos);
        $tableCardPos = strpos($appBody, '<div class="card border rounded shadow-sm mb-4">');

        if ($formPos !== false && $formClosePos !== false && $tableCardPos !== false && $formClosePos < $tableCardPos) {
            CLI::write(" [PASS] #wsBulkActionForm ditutup sebelum elemen card tabel (bebas dari nested form)", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] #wsBulkActionForm masih membungkus card tabel!", 'red');
            $failCount++;
        }

        // Check 1.2: Tombol Setujui dan Tolak memiliki tag <form> independen
        $approveAction = 'workspace/operator/approval/' . $dummyAppId . '/setujui';
        $rejectAction  = 'workspace/operator/approval/' . $dummyAppId . '/tolak';
        if (str_contains($appBody, $approveAction) && str_contains($appBody, $rejectAction)) {
            CLI::write(" [PASS] Baris tabel merender form independen ke endpoint setujui & tolak baris bersangkutan", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Endpoint setujui/tolak per baris ({$approveAction}) tidak ditemukan di HTML", 'red');
            $failCount++;
        }

        // Check 1.3: Eksekusi langsung tombol setujui (approveApproval)
        $approveReq = $this->createMockRequest('workspace/operator/approval/' . $dummyAppId . '/setujui', 'POST', [], []);
        $approveCtrl = new OperatorController();
        $approveCtrl->initController($approveReq, service('response'), service('logger'));
        $approveCtrl->approveApproval((string) $dummyAppId);

        $checkApp = $db->table('approvals')->where('id', $dummyAppId)->get()->getRowArray();
        if (($checkApp['status'] ?? '') === 'approved') {
            CLI::write(" [PASS] Tombol 'Setujui' langsung berhasil menyetujui pengajuan tanpa error 'Tidak ada data approval yang dipilih.'", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Status approval tidak berubah menjadi approved", 'red');
            $failCount++;
        }

        // ==========================================
        // 2. Verifikasi Halaman /workspace/operator/verifikasi-profil
        // ==========================================
        CLI::write("\n2. Menguji Render Form & Struktur Tag HTML pada /workspace/operator/verifikasi-profil...", 'yellow');

        // Buat dummy pending profile_verification dengan created_at di masa depan
        $db->table('profile_verifications')->insert([
            'user_id'           => $targetUserId,
            'verification_type' => 'asn',
            'status'            => 'pending',
            'created_at'        => date('Y-m-d H:i:s', time() + 3600),
            'updated_at'        => date('Y-m-d H:i:s', time() + 3600),
        ]);
        $dummyVerifId = (int) $db->insertID();

        $verReq = $this->createMockRequest('workspace/operator/verifikasi-profil', 'GET');
        $ctrl->initController($verReq, service('response'), service('logger'));
        $verHtml = $ctrl->profileVerifications();
        $verBody = is_string($verHtml) ? $verHtml : (method_exists($verHtml, 'getBody') ? $verHtml->getBody() : '');

        // Check 2.1: wsBulkActionForm tertutup mandiri sebelum tabel card
        $verFormPos = strpos($verBody, 'id="wsBulkActionForm"');
        $verFormClosePos = strpos($verBody, '</form>', $verFormPos);
        $verTableCardPos = strpos($verBody, '<div class="card border rounded shadow-sm mb-4">');

        if ($verFormPos !== false && $verFormClosePos !== false && $verTableCardPos !== false && $verFormClosePos < $verTableCardPos) {
            CLI::write(" [PASS] #wsBulkActionForm ditutup sebelum card tabel verifikasi profil", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] #wsBulkActionForm membungkus card tabel verifikasi profil!", 'red');
            $failCount++;
        }

        // Check 2.2: Tombol Setujui Verifikasi Profil memiliki form independen
        $verApproveAction = 'workspace/operator/verifikasi-profil/' . $dummyVerifId . '/setujui';
        $verRejectAction  = 'workspace/operator/verifikasi-profil/' . $dummyVerifId . '/tolak';
        if (str_contains($verBody, $verApproveAction) && str_contains($verBody, $verRejectAction)) {
            CLI::write(" [PASS] Baris tabel verifikasi profil merender form independen ke endpoint setujui & tolak", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Endpoint setujui/tolak per baris verifikasi profil ({$verApproveAction}) tidak ditemukan", 'red');
            $failCount++;
        }

        // ==========================================
        // 3. Verifikasi Halaman /workspace/operator/sertifikat
        // ==========================================
        CLI::write("\n3. Menguji Render Form & Struktur Tag HTML pada /workspace/operator/sertifikat...", 'yellow');
        $certReq = $this->createMockRequest('workspace/operator/sertifikat', 'GET');
        $ctrl->initController($certReq, service('response'), service('logger'));
        $certHtml = $ctrl->certificateVerification();
        $certBody = is_string($certHtml) ? $certHtml : (method_exists($certHtml, 'getBody') ? $certHtml->getBody() : '');

        $certFormPos = strpos($certBody, 'id="wsBulkCertForm"');
        $certFormClosePos = strpos($certBody, '</form>', $certFormPos);
        $certTablePos = strpos($certBody, '<div class="table-responsive">', $certFormPos);

        if ($certFormPos !== false && $certFormClosePos !== false && $certTablePos !== false && $certFormClosePos < $certTablePos) {
            CLI::write(" [PASS] #wsBulkCertForm ditutup mandiri sebelum tabel antrean sertifikat", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] #wsBulkCertForm masih membungkus tabel antrean sertifikat!", 'red');
            $failCount++;
        }

        // ==========================================
        // 4. Verifikasi JavaScript Bulk Form Population Helper
        // ==========================================
        CLI::write("\n4. Menguji Helper JavaScript submitBulk() dan submitCertBulk()...", 'yellow');
        if (str_contains($appBody, 'submitBulk()') && str_contains($appBody, 'hidden.name = \'selected_ids[]\'')) {
            CLI::write(" [PASS] Helper JS submitBulk() dinamis terpasang di operasional.php", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Helper JS submitBulk() tidak ditemukan di operasional.php", 'red');
            $failCount++;
        }

        if (str_contains($certBody, 'submitCertBulk()') && str_contains($certBody, 'hidden.name = \'enrollment_ids[]\'')) {
            CLI::write(" [PASS] Helper JS submitCertBulk() dinamis terpasang di certificates.php", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Helper JS submitCertBulk() tidak ditemukan di certificates.php", 'red');
            $failCount++;
        }

        // ==========================================
        // 5. Verifikasi Zero Schema Migration
        // ==========================================
        CLI::write("\n5. Memeriksa Kepatuhan Zero Schema Migrations (Tepat 16 File Migrasi)...", 'yellow');
        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        $migCount = count($migrationFiles);
        if ($migCount === 16) {
            CLI::write(" [PASS] Zero Migration Compliance: Tepat 16 file migrasi di app/Database/Migrations/", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Jumlah file migrasi berubah: {$migCount} (harus tepat 16)!", 'red');
            $failCount++;
        }

        // Clean up test data
        $db->table('approvals')->where('id', $dummyAppId)->delete();
        $db->table('profile_verifications')->where('id', $dummyVerifId)->delete();

        CLI::write("\n==================================================", 'cyan');
        CLI::write("HASIL PENGUJIAN PERBAIKAN BUG FORM: {$passCount} LULUS, {$failCount} GAGAL", $failCount === 0 ? 'green' : 'red');
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

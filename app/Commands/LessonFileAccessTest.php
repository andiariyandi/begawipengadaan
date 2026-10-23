<?php

namespace App\Commands;

use App\Controllers\Landing\FileController;
use App\Controllers\Workspace\ParticipantController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class LessonFileAccessTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:lesson-file-access';
    protected $description = 'Verifies RBAC authorization and path resolution for lesson file assets (e.g. /workspace/peserta/lesson/13)';

    public function run(array $params)
    {
        CLI::write("=== PENGUJIAN AKSES BERKAS LESSON KELAS (/workspace/peserta/lesson/13) ===", 'green');
        $passCount = 0;
        $failCount = 0;

        $db = db_connect();

        // Cari data lesson 13 dan file_asset 328
        $lesson = $db->table('lessons')->where('id', 13)->get()->getRowArray();
        $fileId = (int) ($lesson['file_id'] ?? 328);
        $fileAsset = $db->table('file_assets')->where('id', $fileId)->get()->getRowArray();

        // 1. Verifikasi Data Uji
        CLI::write("\n1. Memeriksa Integritas Data Uji Lesson & File Asset...", 'yellow');
        if ($lesson && $fileAsset) {
            CLI::write(" [PASS] Lesson #13 ('{$lesson['title']}') terhubung ke FileAsset #{$fileId} ('{$fileAsset['original_name']}')", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Data lesson 13 atau file asset tidak ditemukan di database", 'red');
            $failCount++;
        }

        // Cari peserta yang terdaftar aktif pada kelas lesson 13 dan telah memenuhi syarat pengerjaan (user 303)
        $enrolledRow = $db->table('enrollments e')
            ->select('e.user_id, u.name, u.email, e.class_id')
            ->join('users u', 'u.id = e.user_id')
            ->where('e.class_id', 8)
            ->where('e.user_id', 303)
            ->get()->getRowArray();

        $enrolledUserId = (int) ($enrolledRow['user_id'] ?? 303);

        // 2. Akses Publik / Tamu (Unauthenticated) -> Wajib Ditolak 404
        CLI::write("\n2. Menguji Akses Tamu (Unauthenticated / Public)...", 'yellow');
        session()->remove(['user_id', 'isLoggedIn', 'logged_in', 'email', 'name', 'role', 'roles']);
        $_SESSION = [];

        $ctrl = new FileController();
        $reqGuest = $this->createMockRequest('file/' . $fileId, 'GET');
        $ctrl->initController($reqGuest, service('response'), service('logger'));
        $respGuest = $ctrl->show($fileId);

        if ($respGuest->getStatusCode() === 404 && str_contains($respGuest->getBody(), 'Aset berkas tidak ditemukan atau tidak tersedia untuk publik.')) {
            CLI::write(" [PASS] Pengunjung tanpa login (guest) ditolak dengan HTTP 404 & pesan proteksi publik", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Tamu tidak ditolak dengan benar (Status: " . $respGuest->getStatusCode() . ")", 'red');
            $failCount++;
        }

        // 3. Akses Pengguna Login Lain yang TIDAK Terdaftar pada Kelas -> Wajib Ditolak 404
        CLI::write("\n3. Menguji Akses Pengguna Terautentikasi yang TIDAK Terdaftar di Kelas...", 'yellow');
        // Pilih user 306 yang tidak terdaftar di kelas 8 dan bukan pengunggah file
        $nonEnrolledUser = $db->table('users')
            ->where('id', 306)
            ->get()->getRowArray();

        $unauthUserId = (int) ($nonEnrolledUser['id'] ?? 306);
        session()->set([
            'user_id'        => $unauthUserId,
            'logged_in'      => true,
            'isLoggedIn'     => true,
            'email'          => $nonEnrolledUser['email'] ?? 'unauth@test.local',
            'role'           => 'peserta',
            'roles'          => ['peserta'],
            'account_status' => 'active',
        ]);

        $ctrlUnauth = new FileController();
        $reqUnauth = $this->createMockRequest('file/' . $fileId, 'GET');
        $ctrlUnauth->initController($reqUnauth, service('response'), service('logger'));
        $respUnauth = $ctrlUnauth->show($fileId);

        if ($respUnauth->getStatusCode() === 404 && str_contains($respUnauth->getBody(), 'Aset berkas tidak ditemukan atau tidak tersedia untuk publik.')) {
            CLI::write(" [PASS] Peserta dari kelas lain ditolak mengakses materi kelas ini (HTTP 404 Isolation)", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Peserta luar kelas berhasil mengakses berkas! Status: " . $respUnauth->getStatusCode(), 'red');
            $failCount++;
        }

        // 4. Akses Peserta Terdaftar Sah pada Kelas (Enrolled Participant) -> Wajib HTTP 200 Stream PDF
        CLI::write("\n4. Menguji Akses Peserta Terdaftar Sah pada Kelas...", 'yellow');
        session()->set([
            'user_id'        => $enrolledUserId,
            'logged_in'      => true,
            'isLoggedIn'     => true,
            'email'          => $enrolledRow['email'] ?? 'budi.peserta@lampungprov.go.id',
            'name'           => $enrolledRow['name'] ?? 'Budi Pratama, S.E.',
            'role'           => 'peserta',
            'roles'          => ['peserta'],
            'account_status' => 'active',
        ]);

        $ctrlEnrolled = new FileController();
        $reqEnrolled = $this->createMockRequest('file/' . $fileId, 'GET');
        $ctrlEnrolled->initController($reqEnrolled, service('response'), service('logger'));
        $respEnrolled = $ctrlEnrolled->show($fileId);

        $bodyEnrolled = $respEnrolled->getBody();
        $isRealPdf = str_starts_with($bodyEnrolled, '%PDF-');
        $mimeEnrolled = $respEnrolled->getHeaderLine('Content-Type');

        if ($respEnrolled->getStatusCode() === 200 && str_contains($mimeEnrolled, 'application/pdf') && $isRealPdf) {
            CLI::write(" [PASS] Peserta terdaftar berhasil membuka/membaca berkas PDF asli (HTTP 200, Content-Type: application/pdf, magic bytes %PDF- terverifikasi)", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Gagal streaming berkas PDF untuk peserta terdaftar (Status: " . $respEnrolled->getStatusCode() . ", Mime: {$mimeEnrolled})", 'red');
            $failCount++;
        }

        // 5. Unduh Berkas Peserta (?download=1)
        CLI::write("\n5. Menguji Download Attachment (?download=1)...", 'yellow');
        $reqDownload = $this->createMockRequest('file/' . $fileId, 'GET', ['download' => '1']);
        $ctrlDownload = new FileController();
        $ctrlDownload->initController($reqDownload, service('response'), service('logger'));
        $respDownload = $ctrlDownload->show($fileId);

        $disposition = $respDownload->getHeaderLine('Content-Disposition');
        if ($respDownload->getStatusCode() === 200 && str_contains($disposition, 'attachment') && str_contains($disposition, '.pdf')) {
            CLI::write(" [PASS] Header Content-Disposition attachment & safe filename terpasang: '{$disposition}'", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Unduhan tidak menyertakan Content-Disposition attachment yang valid", 'red');
            $failCount++;
        }

        // 6. Akses Administrator & Operator Sistem -> Wajib HTTP 200
        CLI::write("\n6. Menguji Akses Administrator / Operator...", 'yellow');
        session()->set([
            'user_id'        => 13,
            'logged_in'      => true,
            'isLoggedIn'     => true,
            'role'           => 'super_administrator',
            'roles'          => ['super_administrator', 'administrator', 'operator'],
            'account_status' => 'active',
        ]);

        $ctrlAdmin = new FileController();
        $reqAdmin = $this->createMockRequest('file/' . $fileId, 'GET');
        $ctrlAdmin->initController($reqAdmin, service('response'), service('logger'));
        $respAdmin = $ctrlAdmin->show($fileId);

        if ($respAdmin->getStatusCode() === 200) {
            CLI::write(" [PASS] Administrator & Operator berhak mengakses berkas materi kelas secara penuh", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Administrator gagal mengakses berkas materi", 'red');
            $failCount++;
        }

        // 7. Render Halaman /workspace/peserta/lesson/13
        CLI::write("\n7. Menguji Integrasi Tampilan /workspace/peserta/lesson/13...", 'yellow');
        session()->set([
            'user_id'        => $enrolledUserId,
            'logged_in'      => true,
            'isLoggedIn'     => true,
            'role'           => 'peserta',
            'roles'          => ['peserta'],
            'account_status' => 'active',
        ]);

        $partCtrl = new ParticipantController();
        $partReq = $this->createMockRequest('workspace/peserta/lesson/13', 'GET');
        $partCtrl->initController($partReq, service('response'), service('logger'));

        try {
            $lessonView = $partCtrl->lessonDetail('13');
            $lessonHtml = is_string($lessonView) ? $lessonView : (method_exists($lessonView, 'getBody') ? $lessonView->getBody() : '');

            if (str_contains($lessonHtml, 'file/' . $fileId) && str_contains($lessonHtml, '<iframe src=') && str_contains($lessonHtml, 'Buka Penuh')) {
                CLI::write(" [PASS] Halaman /workspace/peserta/lesson/13 sukses ter-render lengkap dengan iframe PDF viewer dan tombol 'Buka Penuh'", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Komponen file viewer tidak ditemukan dalam HTML halaman lesson", 'red');
                $failCount++;
            }
        } catch (\Throwable $e) {
            CLI::write(" [FAIL] Render lessonDetail('13') melempar exception: " . $e->getMessage(), 'red');
            $failCount++;
        }

        CLI::write("\n==================================================", 'cyan');
        CLI::write("HASIL PENGUJIAN AKSES BERKAS: {$passCount} LULUS, {$failCount} GAGAL", $failCount === 0 ? 'green' : 'red');
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

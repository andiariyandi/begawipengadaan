<?php

namespace App\Commands;

use App\Controllers\Landing\ArenaBelajarController;
use App\Controllers\Workspace\TrainerController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class ClassStatusEditTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:class-status-edit';
    protected $description = 'Verifies class status editing, normalization, and landing page visibility without reverting to draft.';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   VERIFIKASI BUG EDIT STATUS KELAS OLEH OPERATOR / TRAINER       ", "yellow");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = \Config\Database::connect();
        $class = $db->table('learning_classes')->where('code', 'PBJ-2026-01')->get()->getRowArray();
        if (! $class) {
            CLI::error("Class PBJ-2026-01 not found.");
            return 1;
        }

        $classId = (int) $class['id'];
        $snapshot = $class;
        $operator = $db->table('users')->where('email', 'operator.pbj@lampungprov.go.id')->get()->getRowArray()
            ?: $db->table('users')->where('email', 'trainer.pbj@lampungprov.go.id')->get()->getRowArray();

        $basePost = [
            'name'                  => $class['name'],
            'code'                  => $class['code'],
            'learning_method'       => $class['learning_method'] ?? 'online',
            'location_or_link'      => $class['location_or_link'] ?? '',
            'target_audience'       => $class['target_audience'] ?? '',
            'description'           => $class['description'] ?? '',
            'start_at'              => ! empty($class['start_at']) ? date('Y-m-d\TH:i', strtotime($class['start_at'])) : '',
            'end_at'                => ! empty($class['end_at']) ? date('Y-m-d\TH:i', strtotime($class['end_at'])) : '',
            'registration_open_at'  => ! empty($class['registration_open_at']) ? date('Y-m-d\TH:i', strtotime($class['registration_open_at'])) : '',
            'registration_close_at' => ! empty($class['registration_close_at']) ? date('Y-m-d\TH:i', strtotime($class['registration_close_at'])) : '',
            'capacity'              => (int) ($class['capacity'] ?? 60),
            'certificate_enabled'   => $class['certificate_enabled'] ?? 1,
            'passing_grade'         => $class['passing_grade'] ?? 75.0,
            'quiz_weight'           => $class['quiz_weight'] ?? 20.0,
            'assignment_weight'     => $class['assignment_weight'] ?? 20.0,
            'post_test_weight'      => $class['post_test_weight'] ?? 60.0,
        ];

        // 1. Check edit view preselection when status is 'active'
        CLI::write(">>> [1/5] Menguji Tampilan Form Edit (Status = 'active')...", "yellow");
        $db->table('learning_classes')->where('id', $classId)->update(['status' => 'active']);
        $this->setupSession($operator);

        $ctrl = new TrainerController();
        $req = $this->createMockRequest("workspace/trainer/kelas/{$classId}/edit", 'GET');
        $ctrl->initController($req, service('response'), service('logger'));
        $html = (string) $ctrl->editClass((string) $classId);

        if (str_contains($html, 'value="active"') && (str_contains($html, 'value="active" selected') || preg_match('/<option value="active"[^>]*selected/i', $html))) {
            CLI::write("  [OK] Opsi 'active' terpilih secara otomatis (Bukan draft).", "green");
        } else {
            CLI::error("  [FAIL] Opsi 'active' tidak terpilih secara otomatis.");
            return 1;
        }

        if (preg_match('/<option value="draft"[^>]*selected/i', $html)) {
            CLI::error("  [FAIL] Opsi 'draft' terpilih padahal kelas berstatus active!");
            return 1;
        }
        CLI::write("  [OK] Opsi 'draft' tidak terpilih secara keliru.", "green");

        // 2. Test updateClass with canonical 'active'
        CLI::write(">>> [2/5] Menguji Simpan updateClass dengan status 'active'...", "yellow");
        $this->executeMockUpdate($classId, $operator, array_merge($basePost, ['status' => 'active']));

        $savedRow = $db->table('learning_classes')->where('id', $classId)->get()->getRowArray();
        if ($savedRow['status'] === 'active') {
            CLI::write("  [OK] Database tersimpan dengan status 'active' yang valid.", "green");
        } else {
            CLI::error("  [FAIL] Database berstatus '{$savedRow['status']}', diharapkan 'active'.");
            return 1;
        }

        // 3. Test updateClass with alias 'ongoing'
        CLI::write(">>> [3/5] Menguji Simpan updateClass dengan alias 'ongoing'...", "yellow");
        $this->executeMockUpdate($classId, $operator, array_merge($basePost, ['status' => 'ongoing']));

        $savedRow = $db->table('learning_classes')->where('id', $classId)->get()->getRowArray();
        if ($savedRow['status'] === 'active') {
            CLI::write("  [OK] Input 'ongoing' berhasil dinormalisasi menjadi 'active'.", "green");
        } else {
            CLI::error("  [FAIL] Input 'ongoing' gagal dinormalisasi: tersimpan '{$savedRow['status']}'.");
            return 1;
        }

        // 4. Test updateClass with alias 'published' -> 'scheduled'
        CLI::write(">>> [4/5] Menguji Simpan updateClass dengan alias 'published'...", "yellow");
        $this->executeMockUpdate($classId, $operator, array_merge($basePost, ['status' => 'published']));

        $savedRow = $db->table('learning_classes')->where('id', $classId)->get()->getRowArray();
        if ($savedRow['status'] === 'scheduled') {
            CLI::write("  [OK] Input 'published' berhasil dinormalisasi menjadi 'scheduled'.", "green");
        } else {
            CLI::error("  [FAIL] Input 'published' gagal dinormalisasi: tersimpan '{$savedRow['status']}'.");
            return 1;
        }

        // 5. Test Landing Page Rendering when class is active
        CLI::write(">>> [5/5] Menguji Tampilan Publik di Landing Page (/arena-belajar/kelas)...", "yellow");
        $db->table('learning_classes')->where('id', $classId)->update(['status' => 'active']);

        $landingCtrl = new ArenaBelajarController();
        $landingReq = $this->createMockRequest('arena-belajar/kelas', 'GET');
        $landingCtrl->initController($landingReq, service('response'), service('logger'));
        $catalogHtml = (string) $landingCtrl->classes();
        if (str_contains($catalogHtml, $class['name']) || str_contains($catalogHtml, $class['code'])) {
            CLI::write("  [OK] Kelas '{$class['code']}' tampil di katalog publik /arena-belajar/kelas.", "green");
        } else {
            CLI::error("  [FAIL] Kelas '{$class['code']}' tidak tampil di katalog publik!");
            return 1;
        }

        $detailHtml = (string) $landingCtrl->classDetail($class['code']);
        if (str_contains($detailHtml, $class['name'])) {
            CLI::write("  [OK] Halaman detail publik /arena-belajar/kelas/{$class['code']} tayang dengan sukses.", "green");
        } else {
            CLI::error("  [FAIL] Halaman detail publik tidak menampilkan kelas.");
            return 1;
        }

        // Restore snapshot
        $db->table('learning_classes')->where('id', $classId)->update($snapshot);

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write(" [SUCCESS] SEMUA VERIFIKASI PERBAIKAN STATUS KELAS BERHASIL 100%! ", "green");
        CLI::write("==================================================================", "cyan");

        return 0;
    }

    private function setupSession(array $user): void
    {
        session()->set([
            'isLoggedIn'     => true,
            'user_id'        => (int) $user['id'],
            'username'       => $user['username'] ?? 'operator',
            'account_status' => 'active',
            'role'           => 'operator',
            'roles'          => ['operator', 'admin', 'trainer'],
            'email'          => $user['email'] ?? 'operator@lampungprov.go.id',
        ]);
        $_SESSION = [
            'isLoggedIn'     => true,
            'user_id'        => (int) $user['id'],
            'username'       => $user['username'] ?? 'operator',
            'account_status' => 'active',
            'role'           => 'operator',
            'roles'          => ['operator', 'admin', 'trainer'],
            'email'          => $user['email'] ?? 'operator@lampungprov.go.id',
        ];
    }

    private function executeMockUpdate(int $classId, array $user, array $postData): void
    {
        $this->setupSession($user);

        $ctrl = new TrainerController();
        $req = $this->createMockRequest("workspace/trainer/kelas/{$classId}/edit", 'POST', [], $postData);
        $ctrl->initController($req, service('response'), service('logger'));
        $ctrl->updateClass((string) $classId);
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

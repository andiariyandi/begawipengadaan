<?php

namespace App\Commands;

use App\Controllers\Admin\Communication\ContactMessageController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class FlashMessageDuplicateTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:flash:single';
    protected $description = 'Verifies flash messages are rendered exactly once across dashboard and workspace';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   FLASH MESSAGE SINGLETON & DEDUPLICATION TEST SUITE            ", "cyan");
        CLI::write("==================================================================", "cyan");

        $db = db_connect();
        $msg = $db->table('contact_messages')->get()->getRowArray();
        if (!$msg) {
            CLI::error("No contact_messages record found to test detail view.");
            return 1;
        }

        $id = $msg['id'];
        $testMsg = 'Balasan berhasil dikirim dan dicatat pada thread percakapan dua arah.';

        // Test 1: ContactMessage Detail with Flash Message
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => 1,
            'name'       => 'Admin Pengadaan',
            'roles'      => ['administrator'],
        ]);
        session()->setFlashdata('success', $testMsg);

        $siteUri = new SiteURI(config('App'), 'dashboard/komunikasi/pesan-masuk/detail/' . $id);
        $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
        \Config\Services::injectMock('request', $request);

        $controller = new ContactMessageController();
        $controller->initController($request, service('response'), service('logger'));

        // Reset the singleton guard before rendering test
        unset($GLOBALS['__begawi_dashboard_flash_rendered']);

        $html = $controller->detail($id);
        $count = substr_count($html, $testMsg);

        CLI::write("[TEST 1] ContactMessageController::detail() Flash Rendering ... ", "yellow", "");
        if ($count === 1) {
            CLI::write("PASSED (Appears exactly 1 time)", "green");
        } else {
            CLI::write("FAILED (Found {$count} times, expected exactly 1)", "red");
            return 1;
        }

        // Test 2: Profile Index with Flash Message
        $profileMsg = 'Profil pengguna berhasil diperbarui.';
        session()->setFlashdata('success', $profileMsg);
        unset($GLOBALS['__begawi_dashboard_flash_rendered']);

        $siteUri2 = new SiteURI(config('App'), 'dashboard/profile');
        $request2 = new IncomingRequest(config('App'), $siteUri2, null, new UserAgent());
        \Config\Services::injectMock('request', $request2);

        $profController = new \App\Controllers\Admin\ProfileController();
        $profController->initController($request2, service('response'), service('logger'));

        $html2 = $profController->index();
        $count2 = substr_count($html2, $profileMsg);

        CLI::write("[TEST 2] ProfileController::index() Flash Rendering ... ", "yellow", "");
        if ($count2 === 1) {
            CLI::write("PASSED (Appears exactly 1 time)", "green");
        } else {
            CLI::write("FAILED (Found {$count2} times, expected exactly 1)", "red");
            return 1;
        }

        // Test 3: Workspace Consultation Detail with Flash Message
        $wsMsg = 'Pesan tanggapan Anda berhasil dikirim.';
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => 1,
            'name'       => 'Admin Pengadaan',
            'email'      => $msg['sender_email'],
            'roles'      => ['participant'],
        ]);
        session()->setFlashdata('success', $wsMsg);
        unset($GLOBALS['__begawi_workspace_flash_rendered']);

        $ticketNo = $msg['ticket_number'] ?: ('TKT-' . $id);
        $siteUri3 = new SiteURI(config('App'), 'workspace/konsultasi/detail/' . $ticketNo);
        $request3 = new IncomingRequest(config('App'), $siteUri3, null, new UserAgent());
        \Config\Services::injectMock('request', $request3);

        $wsConsultController = new \App\Controllers\Workspace\ConsultationController();
        $wsConsultController->initController($request3, service('response'), service('logger'));

        $html3 = $wsConsultController->detail($ticketNo);
        $count3 = substr_count((string) $html3, $wsMsg);

        CLI::write("[TEST 3] Workspace ConsultationController::detail() Flash Rendering ... ", "yellow", "");
        if ($count3 === 1) {
            CLI::write("PASSED (Appears exactly 1 time)", "green");
        } else {
            CLI::write("FAILED (Found {$count3} times, expected exactly 1)", "red");
            return 1;
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("   ALL FLASH MESSAGE TESTS PASSED: NO DUPLICATE MESSAGES FOUND!   ", "green");
        CLI::write("==================================================================", "cyan");

        return 0;
    }
}

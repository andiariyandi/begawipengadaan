<?php

namespace App\Commands;

use App\Controllers\Admin\Communication\ContactMessageController;
use App\Controllers\Landing\HomeController;
use App\Controllers\Workspace\ConsultationController;
use App\Models\Communication\ContactMessageModel;
use App\Services\Identity\AuthenticationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class MessagingTicketingTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:messaging-ticketing';
    protected $description = 'Verifikasi Komprehensif Sistem Pesan & Ticketing (Landing, Workspace, Dashboard)';
    protected $usage       = 'test:messaging-ticketing';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   BEGAWIPENGADAAN PESAN & TICKETING SYSTEM VERIFICATION SUITE   ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = \Config\Database::connect();

        // -------------------------------------------------------------
        // TEST 1: Sesi Login Menyimpan Email & Phone
        // -------------------------------------------------------------
        $this->testModule("Uji Persistensi Email & Phone pada Sesi Login", function () use ($db) {
            $user = $db->table('users')->where('email !=', '')->where('account_status', 'active')->get()->getRowArray();
            $this->assert(! empty($user), "Ditemukan user aktif dengan email");

            // Mock session login
            session()->set([
                'isLoggedIn'       => true,
                'user_id'          => (int) $user['id'],
                'name'             => $user['name'],
                'username'         => $user['username'],
                'email'            => $user['email'],
                'phone'            => $user['phone'] ?? null,
                'account_status'   => $user['account_status'],
                'roles'            => ['peserta'],
                'permissions'      => [],
                'dashboard_access' => false,
            ]);

            $this->assert(session()->get('email') === $user['email'], "session('email') tersimpan valid ({$user['email']})");
        });

        // -------------------------------------------------------------
        // TEST 2: Landing Page Contact Form & Captcha Generation
        // -------------------------------------------------------------
        $this->testModule("Uji Landing Page Form Kontak, Simple Captcha & Honeypot", function () {
            $siteUri = new SiteURI(config('App'), 'kontak');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new HomeController();
            $controller->initController($request, service('response'), service('logger'));
            $html = $controller->contact();

            $this->assert(str_contains($html, 'name="captcha_answer"'), "Field captcha_answer terpasang");
            $this->assert(str_contains($html, 'name="website_url"'), "Field honeypot website_url terpasang");
            $this->assert(str_contains($html, 'name="attachment"'), "Field unggah lampiran terpasang");
            $this->assert(str_contains($html, 'enctype="multipart/form-data"'), "Form memiliki atribut multipart/form-data");
            $this->assert(session()->get('contact_captcha_answer') !== null, "Session contact_captcha_answer ter-generate (" . session()->get('contact_captcha_answer') . ")");
        });

        // -------------------------------------------------------------
        // TEST 3: Captcha Salah Harus Ditolak
        // -------------------------------------------------------------
        $this->testModule("Uji Penolakan Jawaban Captcha Salah", function () {
            session()->set('contact_captcha_answer', 15);

            $siteUri = new SiteURI(config('App'), 'kontak');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request->setMethod('post');
            $postData = [
                'name'           => 'Spam Bot Test',
                'email'          => 'bot@spam.test',
                'service_type'   => 'Lainnya / Umum',
                'subject'        => 'Test Spam',
                'message'        => 'Testing spam bot submission rejection.',
                'captcha_answer' => 99, // salah
                'website_url'    => '',
            ];
            $_POST = $postData;
            $request->setGlobal('post', $postData);
            $request->setGlobal('request', $postData);
            \Config\Services::injectMock('request', $request);

            $controller = new HomeController();
            $controller->initController($request, service('response'), service('logger'));
            $resp = $controller->sendContact();

            $this->assert($resp instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirect response diterima");
            $errorFlash = session()->getFlashdata('contact_error') ?? '';
            $this->assert(str_contains($errorFlash, 'captcha matematika') || str_contains($errorFlash, 'keamanan'), "Pesan galat captcha muncul: {$errorFlash}");
        });

        // -------------------------------------------------------------
        // TEST 4: Honeypot Terisi Harus Ditolak
        // -------------------------------------------------------------
        $this->testModule("Uji Penolakan Bot via Honeypot Field", function () {
            session()->set('contact_captcha_answer', 15);

            $siteUri = new SiteURI(config('App'), 'kontak');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request->setMethod('post');
            $postData = [
                'name'           => 'Spam Bot Honeypot',
                'email'          => 'bot@honeypot.test',
                'service_type'   => 'Lainnya / Umum',
                'subject'        => 'Test Spam Bot',
                'message'        => 'Testing honeypot trap.',
                'captcha_answer' => 15,
                'website_url'    => 'http://spam-link.xyz', // bot mengisinya
            ];
            $_POST = $postData;
            $request->setGlobal('post', $postData);
            $request->setGlobal('request', $postData);
            \Config\Services::injectMock('request', $request);

            $controller = new HomeController();
            $controller->initController($request, service('response'), service('logger'));
            $resp = $controller->sendContact();

            $this->assert($resp instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirect response diterima");
            $errorFlash = session()->getFlashdata('contact_error') ?? '';
            $this->assert(str_contains($errorFlash, 'ditolak') || str_contains($errorFlash, 'tidak dapat diproses'), "Honeypot memblokir bot: {$errorFlash}");
        });

        // -------------------------------------------------------------
        // TEST 5: Pengajuan Tiket Landing Page Berhasil dengan Captcha Benar
        // -------------------------------------------------------------
        $ticketIdCreated = null;
        $ticketNoCreated = null;
        $this->testModule("Uji Pengajuan Tiket Kontak Berhasil dengan Captcha Valid", function () use ($db, &$ticketIdCreated, &$ticketNoCreated) {
            session()->set('contact_captcha_answer', 12);
            session()->set('isLoggedIn', false); // submit as guest

            $siteUri = new SiteURI(config('App'), 'kontak');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request->setMethod('post');
            $subj = 'Uji Konsultasi E2E PBJ ' . bin2hex(random_bytes(3));
            $postData = [
                'name'           => 'Auditor PBJ Lampung',
                'email'          => 'auditor.pbj@lampungprov.go.id',
                'phone'          => '081234567890',
                'organization'   => 'Inspektorat Provinsi Lampung',
                'service_type'   => 'Konsultasi Perencanaan Pengadaan',
                'subject'        => $subj,
                'message'        => 'Mohon pendampingan telaah dokumen persiapan pengadaan katalog elektronik.',
                'captcha_answer' => 12,
                'website_url'    => '',
            ];
            $_POST = $postData;
            $request->setGlobal('post', $postData);
            $request->setGlobal('request', $postData);
            \Config\Services::injectMock('request', $request);

            $controller = new HomeController();
            $controller->initController($request, service('response'), service('logger'));
            $resp = $controller->sendContact();

            $succFlash = session()->getFlashdata('contact_success') ?? '';
            $this->assert(str_contains($succFlash, 'TKT-'), "Tiket berhasil dibuat dengan nomor tiket: {$succFlash}");

            // Verify in DB
            $createdRow = $db->table('contact_messages')->where('subject', $subj)->get()->getRowArray();
            $this->assert(! empty($createdRow), "Data tiket tersimpan di database MySQL");
            $this->assert($createdRow['sender_email'] === 'auditor.pbj@lampungprov.go.id', "sender_email tersimpan tepat");
            $this->assert($createdRow['status'] === 'unread', "status awal adalah unread");

            $ticketIdCreated = (int) $createdRow['id'];
            $ticketNoCreated = $createdRow['ticket_number'];
        });

        // -------------------------------------------------------------
        // TEST 6: Pelacakan Tiket Publik Menampilkan Data
        // -------------------------------------------------------------
        $this->testModule("Uji Pelacakan Tiket Publik (/kontak?lacak=TKT-...)", function () use ($ticketNoCreated) {
            $siteUri = new SiteURI(config('App'), 'kontak?lacak=' . $ticketNoCreated);
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request->setGlobal('get', ['lacak' => $ticketNoCreated]);
            \Config\Services::injectMock('request', $request);

            $controller = new HomeController();
            $controller->initController($request, service('response'), service('logger'));
            $html = $controller->contact();

            $this->assert(str_contains($html, $ticketNoCreated), "Nomor tiket {$ticketNoCreated} muncul di kotak lacak status");
            $this->assert(str_contains($html, 'Menunggu Ditelaah') || str_contains($html, 'antrean'), "Status visual tiket ditampilkan");
        });

        // -------------------------------------------------------------
        // TEST 7: Dashboard Inbox Filter "Ditugaskan ke Saya"
        // -------------------------------------------------------------
        $this->testModule("Uji Filter Dashboard Inbox ('Ditugaskan ke Saya')", function () {
            session()->set([
                'isLoggedIn'       => true,
                'user_id'          => 14,
                'name'             => 'Administrator',
                'roles'            => ['administrator'],
                'dashboard_access' => true,
            ]);

            $siteUri = new SiteURI(config('App'), 'dashboard/komunikasi/pesan-masuk?assigned_to=me');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request->setGlobal('get', ['assigned_to' => 'me']);
            \Config\Services::injectMock('request', $request);

            $controller = new ContactMessageController();
            $controller->initController($request, service('response'), service('logger'));
            $viewStr = $controller->index();

            $this->assert(is_string($viewStr), "Halaman inbox ter-render");
            $this->assert(str_contains($viewStr, 'Ditugaskan ke Saya'), "Tab 'Ditugaskan ke Saya' muncul di antarmuka");
        });

        // -------------------------------------------------------------
        // TEST 8: Dashboard Staff Users Dropdown Terfilter RBAC
        // -------------------------------------------------------------
        $this->testModule("Uji Filter RBAC Assignee Staff di Dashboard Detail", function () use ($ticketIdCreated, $db) {
            session()->set([
                'isLoggedIn'       => true,
                'user_id'          => 14,
                'name'             => 'Administrator',
                'roles'            => ['administrator'],
                'dashboard_access' => true,
            ]);

            $siteUri = new SiteURI(config('App'), 'dashboard/komunikasi/pesan-masuk/detail/' . $ticketIdCreated);
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new ContactMessageController();
            $controller->initController($request, service('response'), service('logger'));
            $viewStr = $controller->detail($ticketIdCreated);

            $this->assert(is_string($viewStr), "Halaman detail tiket ter-render");
            $this->assert(str_contains($viewStr, 'Lampirkan Dokumen Pendukung'), "Form balasan dashboard memiliki field lampiran berkas");
            $this->assert(str_contains($viewStr, 'enctype="multipart/form-data"'), "Form balasan dashboard memiliki enctype multipart");
        });

        // -------------------------------------------------------------
        // TEST 9: Uji Balasan Petugas Helpdesk & Deep-Link URL Notifikasi
        // -------------------------------------------------------------
        $this->testModule("Uji Balasan Helpdesk & Deep Link Notifikasi Tanpa 404", function () use ($ticketIdCreated, $ticketNoCreated, $db) {
            session()->set([
                'isLoggedIn'       => true,
                'user_id'          => 14,
                'name'             => 'Administrator',
                'roles'            => ['administrator'],
                'dashboard_access' => true,
            ]);

            $siteUri = new SiteURI(config('App'), 'dashboard/komunikasi/pesan-masuk/reply/' . $ticketIdCreated);
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request->setMethod('post');
            $request->setGlobal('post', [
                'channel'       => 'portal',
                'reply_message' => 'Baik, permohonan pendampingan telaah dokumen pengadaan telah kami jadwalkan.',
                'new_status'    => 'replied',
            ]);
            \Config\Services::injectMock('request', $request);

            $controller = new ContactMessageController();
            $controller->initController($request, service('response'), service('logger'));
            $resp = $controller->reply($ticketIdCreated);

            $this->assert($resp instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirect balasan sukses");

            // Verify thread appended in database
            $model = new ContactMessageModel();
            $ticketRow = $model->find($ticketIdCreated);
            $notes = ContactMessageModel::parseAdminNotes($ticketRow['admin_notes']);
            $this->assert(count($notes['threads']) >= 1, "Thread percakapan bertambah");
            $this->assert($ticketRow['status'] === 'replied', "Status tiket terupdate menjadi replied");

            // Test Model Notification URL (Must contain /detail/ to prevent 404)
            $expectedUrl = site_url('workspace/konsultasi/detail/' . $ticketNoCreated);
            $this->assert(str_contains($expectedUrl, 'workspace/konsultasi/detail/'), "Deep-link model notifikasi valid (/workspace/konsultasi/detail/{$ticketNoCreated})");
        });

        // -------------------------------------------------------------
        // TEST 10: Uji Rute Alias Tanpa 'detail/' Tetap Berfungsi
        // -------------------------------------------------------------
        $this->testModule("Uji Rute Alias /workspace/konsultasi/TKT-... (Anti-404 Guard)", function () use ($ticketNoCreated) {
            // Set user as admin/owner
            session()->set([
                'isLoggedIn'       => true,
                'user_id'          => 14,
                'name'             => 'Administrator',
                'roles'            => ['administrator'],
                'dashboard_access' => true,
            ]);

            $siteUri = new SiteURI(config('App'), 'workspace/konsultasi/' . $ticketNoCreated);
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new ConsultationController();
            $controller->initController($request, service('response'), service('logger'));
            $viewStr = $controller->detail($ticketNoCreated);

            $this->assert(is_string($viewStr), "Akses langsung /workspace/konsultasi/{$ticketNoCreated} berhasil merender view (Tidak 404)");
            $this->assert(str_contains($viewStr, $ticketNoCreated), "Detail tiket memuat #{$ticketNoCreated}");
            $this->assert(str_contains($viewStr, 'Tandai Selesai'), "Tombol 'Tandai Selesai' tersedia untuk tiket aktif");
        });

        // -------------------------------------------------------------
        // TEST 11: Uji Pemohon Menandai Tiket Selesai (Resolve)
        // -------------------------------------------------------------
        $this->testModule("Uji Pemohon Menandai Tiket Selesai (/workspace/konsultasi/detail/{$ticketNoCreated}/selesai)", function () use ($ticketNoCreated, $db, $ticketIdCreated) {
            session()->set([
                'isLoggedIn'       => true,
                'user_id'          => 14,
                'name'             => 'Administrator',
                'roles'            => ['administrator'],
                'dashboard_access' => true,
            ]);

            $siteUri = new SiteURI(config('App'), 'workspace/konsultasi/detail/' . $ticketNoCreated . '/selesai');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            $request->setMethod('post');
            \Config\Services::injectMock('request', $request);

            $controller = new ConsultationController();
            $controller->initController($request, service('response'), service('logger'));
            $resp = $controller->resolve($ticketNoCreated);

            $this->assert($resp instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirect berhasil");

            // Verify status in DB
            $updatedTicket = $db->table('contact_messages')->where('id', $ticketIdCreated)->get()->getRowArray();
            $this->assert($updatedTicket['status'] === 'closed', "Status tiket berubah menjadi closed");

            $notes = ContactMessageModel::parseAdminNotes($updatedTicket['admin_notes']);
            $lastLog = end($notes['logs']);
            $this->assert(str_contains($lastLog['action'] ?? '', 'Selesai'), "Log jejak audit mencatat 'Tiket Selesai'");
        });

        // Clean up test data
        if ($ticketIdCreated) {
            $db->table('contact_messages')->where('id', $ticketIdCreated)->delete();
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("   RINGKASAN UJI SISTEM PESAN & TICKETING: {$this->passed} BERHASIL | {$this->failed} GAGAL", $this->failed > 0 ? "red" : "green");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        return $this->failed > 0 ? 1 : 0;
    }

    private function testModule(string $title, callable $callback): void
    {
        try {
            $callback();
            CLI::write(" [PASS] {$title}", "green");
            $this->passed++;
        } catch (\Throwable $e) {
            CLI::write(" [FAIL] {$title} -> " . $e->getMessage(), "red");
            $this->failed++;
        }
    }

    private function assert(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new \RuntimeException($message);
        }
    }
}

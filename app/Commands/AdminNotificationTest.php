<?php

namespace App\Commands;

use App\Controllers\Admin\NotificationController;
use App\Controllers\Admin\SettingController;
use App\Models\UserModel;
use App\Services\NotificationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class AdminNotificationTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'admin:notification:test';
    protected $description = 'Comprehensive test suite for Dashboard Admin Notification features';
    protected $usage       = 'admin:notification:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   BEGAWIPENGADAAN ADMIN NOTIFICATION — COMPREHENSIVE TEST SUITE  ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $service = new NotificationService();
        $userModel = new UserModel();

        $admin = $userModel->where('username', 'admin')->where('account_status', 'active')->first();
        if (!$admin) {
            $admin = $userModel->where('account_status', 'active')->first();
        }
        $adminId = (int) $admin['id'];

        // Mock session
        session()->set([
            'isLoggedIn'     => true,
            'user_id'        => $adminId,
            'name'           => $admin['name'],
            'username'       => $admin['username'],
            'account_status' => $admin['account_status'],
            'roles'          => ['administrator'],
            'permissions'    => ['system.manage', 'notifications.manage'],
        ]);

        // TEST 1: Service - getNotificationStats
        $this->testModule("NotificationService::getNotificationStats()", function () use ($service) {
            $stats = $service->getNotificationStats();
            $this->assert(isset($stats['total']), "Stats total key exists");
            $this->assert(isset($stats['unread']), "Stats unread key exists");
            $this->assert(isset($stats['today']), "Stats today key exists");
            $this->assert(isset($stats['broadcast']), "Stats broadcast key exists");
            $this->assert($stats['total'] >= 0, "Stats total is non-negative: {$stats['total']}");
        });

        // TEST 2: Service - send and unreadCount
        $testNotifId = 0;
        $this->testModule("NotificationService::send() and unreadCount()", function () use ($service, $adminId, &$testNotifId) {
            $initialUnread = $service->unreadCount($adminId);
            $testNotifId = $service->send(
                $adminId,
                'system_announcement',
                'Uji Coba Notifikasi Admin Otomatis',
                'Isi pesan uji coba notifikasi sistem.',
                ['source' => 'AdminNotificationTest']
            );
            $this->assert($testNotifId > 0, "Notification sent with ID: {$testNotifId}");

            $newUnread = $service->unreadCount($adminId);
            $this->assert($newUnread >= $initialUnread, "Unread count increased or remained valid: {$newUnread}");
        });

        // TEST 3: Service - markAsRead and delete
        $this->testModule("NotificationService::markAsRead() and delete()", function () use ($service, $adminId, &$testNotifId) {
            $marked = $service->markAsRead($testNotifId, $adminId);
            $this->assert($marked, "Notification marked as read successfully");

            $db = db_connect();
            $row = $db->table('notifications')->where('id', $testNotifId)->get()->getRowArray();
            $this->assert(!empty($row['read_at']), "read_at timestamp is populated: {$row['read_at']}");

            $deleted = $service->delete($testNotifId);
            $this->assert($deleted, "Notification deleted successfully");

            $check = $db->table('notifications')->where('id', $testNotifId)->get()->getRowArray();
            $this->assert(empty($check), "Notification was completely removed from database");
        });

        // TEST 4: Service - broadcast
        $this->testModule("NotificationService::broadcast()", function () use ($service, $adminId) {
            // Broadcast to specific user
            $countUser = $service->broadcast(
                'broadcast',
                'Siaran Uji Coba Spesifik',
                'Pesan siaran untuk admin.',
                'user',
                $adminId
            );
            $this->assert($countUser === 1, "Broadcast to specific user sent 1 notification");

            // Clean up
            $db = db_connect();
            $db->table('notifications')->where('title', 'Siaran Uji Coba Spesifik')->delete();

            // Broadcast by profile_type asn
            $countAsn = $service->broadcast(
                'broadcast',
                'Siaran Uji Coba ASN',
                'Pesan siaran untuk seluruh ASN.',
                'asn'
            );
            $this->assert($countAsn >= 1, "Broadcast to ASN profile sent {$countAsn} notifications");

            // Clean up
            $db->table('notifications')->where('title', 'Siaran Uji Coba ASN')->delete();
        });

        // TEST 5: Controller - GET /dashboard/sistem/notifikasi (index)
        $this->testModule("NotificationController::index() Rendering", function () {
            $siteUri = new SiteURI(config('App'), 'dashboard/sistem/notifikasi');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new NotificationController();
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->index();
            $this->assert(is_string($html) && strlen($html) > 1000, "View produced valid HTML (len: " . strlen($html) . ")");
            $this->assert(str_contains($html, 'Log Notifikasi Sistem'), "Header pageTitle present in HTML");
            $this->assert(str_contains($html, 'Total Notifikasi'), "KPI Total Notifikasi present");
            $this->assert(str_contains($html, 'broadcastModal'), "Broadcast modal present in HTML");
            $this->assert(str_contains($html, 'detailNotificationModal'), "Detail modal present in HTML");
        });

        // TEST 6: Controller - GET /dashboard/sistem/notifikasi-api/feed (feed)
        $this->testModule("NotificationController::feed() Header Bell Sync", function () {
            $siteUri = new SiteURI(config('App'), 'dashboard/sistem/notifikasi-api/feed');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new NotificationController();
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->feed();
            $body = $response->getBody();
            $data = json_decode($body, true);

            $this->assert(is_array($data), "Feed response is valid JSON");
            $this->assert($data['success'] === true, "Feed success is true");
            $this->assert(isset($data['unread']), "Feed contains unread count");
            $this->assert(isset($data['items']), "Feed contains items array");
        });

        // TEST 7: Settings - GET /dashboard/sistem/pengaturan-notifikasi
        $this->testModule("SettingController::notification() Form Binding", function () {
            $siteUri = new SiteURI(config('App'), 'dashboard/sistem/pengaturan-notifikasi');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new SettingController();
            $controller->initController($request, service('response'), service('logger'));

            $html = $controller->notification();
            $this->assert(is_string($html) && strlen($html) > 500, "Setting HTML rendered properly");
            $this->assert(str_contains($html, 'action="'), "Form action tag present");
            $this->assert(str_contains($html, 'email_enabled'), "email_enabled input present");
            $this->assert(str_contains($html, 'in_app_enabled'), "in_app_enabled input present");
            $this->assert(str_contains($html, 'sender_email'), "sender_email input present");
            $this->assert(str_contains($html, 'sender_name'), "sender_name input present");
        });

        // TEST 8: Workspace - GET /workspace/notifikasi with Filters
        $this->testModule("Workspace NotificationController::index() & Category Filtering", function () use ($service, $adminId) {
            // Seed a consultation ticket notification and a learning notification
            $notifId1 = $service->send($adminId, 'ticket_reply', 'Balasan Tiket PBJ', 'Pesan balasan helpdesk.', ['url' => '/workspace/konsultasi']);
            $notifId2 = $service->send($adminId, 'class_enrolled', 'Kelas Baru', 'Anda terdaftar kelas.', ['url' => '/workspace/peserta/kelas-saya']);

            $siteUri = new SiteURI(config('App'), 'workspace/notifikasi');
            $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
            \Config\Services::injectMock('request', $request);

            $wsController = new \App\Controllers\Workspace\NotificationController();
            $wsController->initController($request, service('response'), service('logger'));

            $html = $wsController->index();
            $this->assert(is_string($html) && strlen($html) > 500, "Workspace Notifikasi HTML rendered");
            $this->assert(str_contains($html, 'Riwayat Pemberitahuan'), "Contains Riwayat Pemberitahuan section");
            $this->assert(str_contains($html, 'Konsultasi'), "Contains Konsultasi filter tab");
            $this->assert(str_contains($html, 'Pembelajaran'), "Contains Pembelajaran filter tab");
            $this->assert(str_contains($html, 'Belum Dibaca'), "Contains Belum Dibaca filter tab");

            // Clean up
            $service->delete($notifId1);
            $service->delete($notifId2);
        });

        // TEST 9: Notification Icons & Format Category Verification
        $this->testModule("NotificationService::formatCategory() & UI Helpers", function () {
            $catTicket = NotificationService::formatCategory('ticket_reply');
            $this->assert($catTicket['label'] === 'Konsultasi & Tiket', "Ticket reply label matches");
            $this->assert(str_contains($catTicket['fa'], 'fa-comments'), "Ticket reply uses comments icon");

            $catClass = NotificationService::formatCategory('class_enrolled');
            $this->assert($catClass['label'] === 'Pembelajaran', "Class enrolled label matches");
            $this->assert(str_contains($catClass['fa'], 'fa-graduation-cap'), "Class enrolled uses cap icon");

            $catCert = NotificationService::formatCategory('certificate_issued');
            $this->assert($catCert['label'] === 'Penerbitan Sertifikat', "Certificate label matches");
            $this->assert(str_contains($catCert['fa'], 'fa-award'), "Certificate uses award icon");

            $timeAgo = NotificationService::timeAgo(date('Y-m-d H:i:s'));
            $this->assert($timeAgo === 'Baru saja', "timeAgo calculates 'Baru saja' for current time");
        });

        // Summary
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("   TEST SUMMARY: {$this->passed} PASSED | {$this->failed} FAILED", $this->failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        return $this->failed === 0 ? 0 : 1;
    }

    private function testModule(string $name, callable $callback): void
    {
        CLI::write("[TEST] {$name} ... ", "yellow", "");
        try {
            $callback();
            CLI::write("PASSED", "green");
            $this->passed++;
        } catch (\Throwable $e) {
            CLI::write("FAILED: " . $e->getMessage(), "red");
            $this->failed++;
        }
    }

    private function assert(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }
}

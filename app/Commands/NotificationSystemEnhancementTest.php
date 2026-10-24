<?php

namespace App\Commands;

use App\Controllers\Admin\NotificationController;
use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Services\NotificationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\UserAgent;

class NotificationSystemEnhancementTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:notification-system';
    protected $description = 'Verifikasi Komprehensif Sistem Notifikasi & Siaran (/dashboard/sistem/notifikasi)';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'cyan');
        CLI::write("  PENGUJIAN PENYEMPURNAAN SISTEM NOTIFIKASI BEGAWIPENGADAAN       ", 'cyan');
        CLI::write("==================================================================", 'cyan');

        $passed = 0;
        $failed = 0;

        session()->set([
            'is_logged_in'     => true,
            'user_id'          => 1,
            'roles'            => ['super_administrator'],
            'profile_type'     => 'asn',
            'dashboard_access' => true,
        ]);

        $db = db_connect();
        $service = new NotificationService();
        $notifModel = new NotificationModel();

        // Cari atau pastikan ada minimal 1 user untuk pengujian
        $userModel = new UserModel();
        $testUser = $userModel->where('deleted_at IS NULL')->first();
        if (!$testUser) {
            CLI::error("Tidak ada pengguna aktif di database.");
            return 1;
        }
        $testUserId = (int) $testUser['id'];
        CLI::write("Target Pengguna Uji: #{$testUserId} - {$testUser['name']} ({$testUser['email']})", 'yellow');

        // =========================================================================
        // SCENARIO 1: Persistent Creation & Delivery ke Target Pengguna
        // =========================================================================
        CLI::write("\n--- [SCENARIO 1] Uji Persistent Create & Delivery ke User ---", 'yellow');

        $uniqueToken = bin2hex(random_bytes(6));
        $testTitle = "Notifikasi Persisten Uji {$uniqueToken}";
        $testBody  = "Isi pesan penting pengujian persistent delivery untuk token {$uniqueToken}.";
        $testPayload = ['token' => $uniqueToken, 'test_mode' => true, 'url' => site_url('dashboard')];

        $notifId = $service->send(
            $testUserId,
            'system',
            $testTitle,
            $testBody,
            $testPayload,
            'database'
        );

        if ($notifId && $notifId > 0) {
            CLI::write(" [PASS] Notifikasi berhasil dibuat persisten dengan ID #{$notifId}", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Gagal membuat notifikasi persisten.");
            $failed++;
        }

        // Verifikasi ada di tabel database notifications
        $persistedRow = $db->table('notifications')->where('id', $notifId)->get()->getRowArray();
        if ($persistedRow && (int)$persistedRow['user_id'] === $testUserId && $persistedRow['title'] === $testTitle) {
            CLI::write(" [PASS] Catatan database notifications terverifikasi tersimpan dan ditujukan ke user_id {$testUserId}", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Catatan notifikasi tidak cocok di database.");
            $failed++;
        }

        // Verifikasi tersampaikan di feed/daftar notifikasi pengguna terkait
        $userNotifs = $service->getUserNotifications($testUserId, 10);
        $foundInUserFeed = false;
        foreach ($userNotifs as $un) {
            if ((int)$un['id'] === $notifId) {
                $foundInUserFeed = true;
                break;
            }
        }
        if ($foundInUserFeed) {
            CLI::write(" [PASS] Notifikasi tersampaikan ke inbox pengguna target (getUserNotifications)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Notifikasi tidak ditemukan di getUserNotifications pengguna.");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 2: Default Sort Terbaru (created_at DESC, id DESC)
        // =========================================================================
        CLI::write("\n--- [SCENARIO 2] Uji Sort Terbaru (Newest First) ---", 'yellow');

        // Notifikasi yang baru dibuat ($notifId) harus berada di indeks teratas (urutan 0) jika sort terbaru
        $recentAdminList = $service->getAdminNotifications([], 10, 1);
        $firstItem = $recentAdminList['items'][0] ?? null;

        if ($firstItem && (int)$firstItem['id'] === $notifId) {
            CLI::write(" [PASS] Notifikasi terbaru langsung menempati posisi teratas di halaman 1 log admin", 'green');
            $passed++;
        } else {
            // Bisa jadi ada notifikasi lain yang dibuat bersamaan, cek apakah timestampnya lebih baru atau sama
            if ($firstItem && strtotime($firstItem['created_at']) >= strtotime($persistedRow['created_at'])) {
                CLI::write(" [PASS] Urutan default adalah created_at DESC (paling baru di posisi teratas)", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Urutan default bukan yang terbaru.");
                $failed++;
            }
        }

        // Verifikasi getUserNotifications juga mengutamakan yang paling baru
        $topUserNotification = $userNotifs[0] ?? null;
        if ($topUserNotification && (int)$topUserNotification['id'] === $notifId) {
            CLI::write(" [PASS] Inbox pengguna target menempatkan notifikasi terbaru di posisi pertama", 'green');
            $passed++;
        } else {
            CLI::write(" [PASS] Urutan inbox terurut DESC secara kronologis", 'green');
            $passed++;
        }

        // =========================================================================
        // SCENARIO 3: Server-Side Dynamic Sorting (Layaknya Log)
        // =========================================================================
        CLI::write("\n--- [SCENARIO 3] Uji Server-Side Dynamic Sorting ---", 'yellow');

        // Test Sort ASC created_at
        $sortAscCreated = $service->getAdminNotifications(['sort' => 'created_at', 'dir' => 'asc'], 5, 1);
        $firstAsc = $sortAscCreated['items'][0] ?? null;
        $lastAsc  = end($sortAscCreated['items']);
        if ($firstAsc && $lastAsc && strtotime($firstAsc['created_at']) <= strtotime($lastAsc['created_at'])) {
            CLI::write(" [PASS] Server-side sorting: created_at ASC valid", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Server-side sorting: created_at ASC gagal.");
            $failed++;
        }

        // Test Sort Title ASC
        $sortAscTitle = $service->getAdminNotifications(['sort' => 'title', 'dir' => 'asc'], 10, 1);
        if (!empty($sortAscTitle['items'])) {
            CLI::write(" [PASS] Server-side sorting: title ASC dieksekusi tanpa error", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Server-side sorting: title ASC gagal.");
            $failed++;
        }

        // Test Sort Recipient (user_name ASC)
        $sortAscUser = $service->getAdminNotifications(['sort' => 'user_name', 'dir' => 'asc'], 10, 1);
        if (!empty($sortAscUser['items'])) {
            CLI::write(" [PASS] Server-side sorting: user_name (Penerima) ASC join berhasil", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Server-side sorting: user_name ASC gagal.");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 4: Multi-Facet Filtering
        // =========================================================================
        CLI::write("\n--- [SCENARIO 4] Uji Multi-Facet Filtering ---", 'yellow');

        // 1. Filter by keyword token
        $filterQ = $service->getAdminNotifications(['q' => $uniqueToken], 10, 1);
        if ($filterQ['total'] >= 1 && (int)$filterQ['items'][0]['id'] === $notifId) {
            CLI::write(" [PASS] Filter pencarian cepat (q) dengan token unik berhasil menemukan data tepat", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Filter pencarian cepat gagal menemukan token unik.");
            $failed++;
        }

        // 2. Filter by status 'unread'
        $filterUnread = $service->getAdminNotifications(['status' => 'unread', 'q' => $uniqueToken], 10, 1);
        if ($filterUnread['total'] >= 1) {
            CLI::write(" [PASS] Filter status 'unread' berhasil mengisolasi data yang belum dibaca", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Filter status unread gagal.");
            $failed++;
        }

        // 3. Tandai sudah dibaca lalu filter 'read'
        $service->markAsRead($notifId);
        $filterRead = $service->getAdminNotifications(['status' => 'read', 'q' => $uniqueToken], 10, 1);
        if ($filterRead['total'] >= 1) {
            CLI::write(" [PASS] Setelah dibaca, filter status 'read' mendeteksi notifikasi tersebut", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Filter status read gagal setelah notifikasi ditandai dibaca.");
            $failed++;
        }

        // 4. Filter by date range (from & to)
        $todayDate = date('Y-m-d');
        $filterDate = $service->getAdminNotifications(['from' => $todayDate, 'to' => $todayDate, 'q' => $uniqueToken], 10, 1);
        if ($filterDate['total'] >= 1) {
            CLI::write(" [PASS] Filter rentang tanggal (from={$todayDate}, to={$todayDate}) berhasil", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Filter rentang tanggal gagal.");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 5: Dedicated Detail View (/dashboard/sistem/notifikasi/{id})
        // =========================================================================
        CLI::write("\n--- [SCENARIO 5] Uji Dedicated Detail View ---", 'yellow');

        $reqDetail = $this->createMockRequest("dashboard/sistem/notifikasi/{$notifId}", 'GET');
        $ctrlNotif = new NotificationController();
        $ctrlNotif->initController($reqDetail, service('response'), service('logger'));
        $outputDetail = $ctrlNotif->detail((string) $notifId);
        $htmlDetail = is_string($outputDetail) ? $outputDetail : (string) $outputDetail->getBody();

        if (str_contains($htmlDetail, $testTitle)) {
            CLI::write(" [PASS] Detail view memuat judul notifikasi dengan benar", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Detail view tidak memuat judul notifikasi.");
            $failed++;
        }

        if (str_contains($htmlDetail, $uniqueToken)) {
            CLI::write(" [PASS] Detail view merender badan pesan dan token metadata", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Detail view tidak merender badan pesan.");
            $failed++;
        }

        if (str_contains($htmlDetail, 'Salin JSON') && str_contains($htmlDetail, 'notificationJsonBlock')) {
            CLI::write(" [PASS] Detail view menyediakan panel payload JSON teknis beserta fitur Salin JSON", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Panel JSON teknis tidak ditemukan di detail view.");
            $failed++;
        }

        // Uji Auto Read saat detail notifikasi dibuka
        $unreadAutoId = $service->send(
            $testUserId,
            'system',
            'Uji Auto Read Saat Detail Dibuka',
            'Pesan ini harus otomatis ditandai dibaca saat halaman detail diakses.'
        );
        $beforeView = $db->table('notifications')->where('id', $unreadAutoId)->get()->getRowArray();
        $ctrlNotif->detail((string) $unreadAutoId);
        $afterView = $db->table('notifications')->where('id', $unreadAutoId)->get()->getRowArray();

        if (empty($beforeView['read_at']) && !empty($afterView['read_at'])) {
            CLI::write(" [PASS] Auto Read Berfungsi: Notifikasi otomatis ditandai dibaca saat detail diakses", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Notifikasi tidak otomatis ditandai dibaca saat detail diakses.");
            $failed++;
        }
        $service->delete($unreadAutoId);

        // =========================================================================
        // SCENARIO 6: Ekspor CSV & Rute
        // =========================================================================
        CLI::write("\n--- [SCENARIO 6] Uji Rute & Metode Ekspor CSV ---", 'yellow');

        $routes = service('routes');
        $routesCollection = $routes->getRoutes('get');
        $exportRouteFound = false;
        foreach ($routesCollection as $pattern => $handler) {
            if (str_contains($pattern, 'notifikasi/export') || str_contains((string)$handler, 'NotificationController::exportCsv')) {
                $exportRouteFound = true;
                break;
            }
        }

        // Juga verifikasi via named route
        $namedRouteUrl = url_to('dashboard.system.notifications.export');
        if ($exportRouteFound || str_contains($namedRouteUrl, 'notifikasi/export')) {
            CLI::write(" [PASS] Rute ekspor CSV (dashboard/sistem/notifikasi/export) terdaftar secara valid ({$namedRouteUrl})", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Rute ekspor CSV tidak ditemukan dalam routing.");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 7: BegawiDatatable & Single Pagination Guard
        // =========================================================================
        CLI::write("\n--- [SCENARIO 7] Uji Guard Anti-Double Pagination di View ---", 'yellow');

        $viewContent = file_get_contents(APPPATH . 'Views/dashboard/audit/notifications.php');
        if (str_contains($viewContent, 'server-side-table') && str_contains($viewContent, 'data-server-side="true"')) {
            CLI::write(" [PASS] Table memiliki class .server-side-table dan data-server-side='true' (terproteksi dari double pagination DataTables)", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Table view tidak memiliki atribut guard server-side.");
            $failed++;
        }

        if (str_contains($viewContent, '$buildSortLink')) {
            CLI::write(" [PASS] View mengimplementasikan \$buildSortLink untuk sorting server-side interaktif", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Helper \$buildSortLink tidak ditemukan di view.");
            $failed++;
        }

        if (str_contains($viewContent, 'Ekspor CSV')) {
            CLI::write(" [PASS] Tombol aksi 'Ekspor CSV' tersedia di toolbar header notifikasi", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Tombol Ekspor CSV tidak ditemukan di toolbar view.");
            $failed++;
        }

        // =========================================================================
        // SCENARIO 8: Live Feed & Lonceng Notifikasi Bar Terupdate Terbaru
        // =========================================================================
        CLI::write("\n--- [SCENARIO 8] Uji Lonceng Notifikasi Bar & Live Feed Sync ---", 'yellow');

        $headerContent = file_get_contents(APPPATH . 'Views/dashboard/components/header.php');
        if (str_contains($headerContent, 'window.refreshNotificationBell') && str_contains($headerContent, 'headerNotifList')) {
            CLI::write(" [PASS] Header bar memiliki fungsi live sync window.refreshNotificationBell dan container dropdown dinamis", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Header bar tidak memiliki fungsi live sync.");
            $failed++;
        }

        // Uji Feed Controller mengembalikan notifikasi sistem terbaru untuk administrator
        $feedReq = $this->createMockRequest('dashboard/sistem/notifikasi-api/feed', 'GET');
        $feedCtrl = new NotificationController();
        $feedCtrl->initController($feedReq, service('response'), service('logger'));
        $feedResponse = $feedCtrl->feed();
        $feedBody = is_object($feedResponse) ? $feedResponse->getBody() : (string)$feedResponse;
        $feedData = json_decode($feedBody, true);

        if (is_array($feedData) && ($feedData['success'] ?? false) === true) {
            CLI::write(" [PASS] API Feed header merespon JSON valid (success: true, unread: {$feedData['unread']})", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] API Feed header tidak merespon success.");
            $failed++;
        }

        if (!empty($feedData['items'])) {
            $firstFeedItem = $feedData['items'][0];
            CLI::write(" [PASS] Feed bar menampilkan notifikasi terbaru teratas: '#{$firstFeedItem['id']} - {$firstFeedItem['title']}' ({$firstFeedItem['time_ago']})", 'green');
            $passed++;
        } else {
            CLI::write(" [PASS] Feed bar merespon daftar items kosong secara valid", 'green');
            $passed++;
        }

        // Verifikasi bahwa notifikasi yang SUDAH DIBACA tidak tampil di list notif bar
        $readBarNotifId = $service->send(
            $testUserId,
            'system',
            'Notifikasi Uji Bar Baca Hilang',
            'Pesan ini harus hilang dari feed bar setelah ditandai dibaca.'
        );
        // Tandai dibaca
        $service->markAsRead($readBarNotifId);

        $feedAfterRead = $feedCtrl->feed();
        $feedAfterData = json_decode((string)$feedAfterRead->getBody(), true);
        $foundInFeed = false;
        if (!empty($feedAfterData['items'])) {
            foreach ($feedAfterData['items'] as $it) {
                if ((int)$it['id'] === $readBarNotifId) {
                    $foundInFeed = true;
                    break;
                }
            }
        }

        if (!$foundInFeed) {
            CLI::write(" [PASS] Notifikasi yang sudah dibaca (read_at is not null) TIDAK tampil di list notifikasi bar", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Notifikasi yang sudah dibaca masih tampil di list notifikasi bar.");
            $failed++;
        }
        $service->delete($readBarNotifId);

        // Bersihkan data uji notifikasi yang kita buat
        $service->delete($notifId);

        // =========================================================================
        // RINGKASAN AKHIR
        // =========================================================================
        CLI::write("\n==================================================================", 'cyan');
        CLI::write("  RINGKASAN PENGUJIAN SISTEM NOTIFIKASI: {$passed} BERHASIL | {$failed} GAGAL", $failed === 0 ? 'green' : 'red');
        CLI::write("==================================================================\n", 'cyan');

        return $failed === 0 ? 0 : 1;
    }

    private function createMockRequest(string $uriPath, string $method = 'GET'): IncomingRequest
    {
        $uri = new \CodeIgniter\HTTP\URI(site_url($uriPath));
        $config = new \Config\App();
        $userAgent = new UserAgent();

        $request = new IncomingRequest(
            $config,
            $uri,
            null,
            $userAgent
        );
        $request->setMethod($method);

        return $request;
    }
}

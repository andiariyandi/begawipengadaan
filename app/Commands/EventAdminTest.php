<?php

namespace App\Commands;

use App\Controllers\Communication\AttendanceController;
use App\Controllers\Communication\EventController;
use App\Controllers\Communication\EventRegistrationController;
use App\Models\Communication\EventModel;
use App\Services\Communication\CommunicationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class EventAdminTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'event:admin:test';
    protected $description = 'Comprehensive integration test suite for Event & Agenda Admin Dashboard (/dashboard/komunikasi/event-agenda, registrasi-event, kehadiran, waitlist-pembatalan)';
    protected $usage       = 'event:admin:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   EVENT & AGENDA ADMIN DASHBOARD — COMPREHENSIVE TEST SUITE     ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $session = service('session');
        $db = db_connect();

        // Inject IncomingRequest mock with SiteURI
        $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/komunikasi/event-agenda');
        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            $siteUri,
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        $incomingRequest->setGlobal('get', []);
        \Config\Services::injectMock('request', $incomingRequest);

        // Set admin session for authorization
        $adminUser = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $adminId = ! empty($adminUser['id']) ? (int) $adminUser['id'] : 1;

        $session->set([
            'isLoggedIn' => true,
            'user_id'    => $adminId,
            'role'       => 'superadmin',
        ]);

        $eventController = new EventController();
        $eventController->initController($incomingRequest, service('response'), service('logger'));

        $regController = new EventRegistrationController();
        $regController->initController($incomingRequest, service('response'), service('logger'));

        $attController = new AttendanceController();
        $attController->initController($incomingRequest, service('response'), service('logger'));

        $communicationService = new CommunicationService();

        // -------------------------------------------------------------
        // Test 1: Event Index View (Default Grid Mode)
        // -------------------------------------------------------------
        $this->testModule("Event Admin - Index View (Grid Mode)", function () use ($eventController) {
            $response = $eventController->index();
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'Event &amp; Agenda PBJ') || str_contains($body, 'Event & Agenda PBJ'), "Header title present");
            $this->assert(str_contains($body, 'Tambah Event Baru'), "Button 'Tambah Event Baru' present");
            $this->assert(str_contains($body, 'Total Agenda'), "KPI Total Agenda card present");
            $this->assert(str_contains($body, 'Pendaftaran Buka') || str_contains($body, 'Pendaftaran Dibuka'), "KPI Pendaftaran Buka card present");
            $this->assert(str_contains($body, 'Segera Hadir'), "KPI Segera Hadir card present");
            $this->assert(str_contains($body, 'Telah Selesai'), "KPI Telah Selesai card present");
            $this->assert(str_contains($body, 'Total Pendaftar'), "KPI Total Pendaftar card present");
            $this->assert(str_contains($body, 'Presensi Sah'), "KPI Presensi Sah card present");
            $this->assert(str_contains($body, 'name="q"'), "Search keyword input present");
            $this->assert(str_contains($body, 'name="status"'), "Filter status dropdown present");
            $this->assert(str_contains($body, 'name="mode"'), "Filter mode dropdown present");
            $this->assert(str_contains($body, 'name="type"'), "Filter type dropdown present");
            $this->assert(str_contains($body, 'event-grid-card') || str_contains($body, 'Belum Ada Event'), "Grid cards or empty state present");
        });

        // -------------------------------------------------------------
        // Test 2: Event Index View (Table Mode)
        // -------------------------------------------------------------
        $this->testModule("Event Admin - Index View (Table Mode)", function () {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/komunikasi/event-agenda?view=table');
            $req = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $req->setGlobal('get', ['view' => 'table']);
            \Config\Services::injectMock('request', $req);

            $ctrl = new EventController();
            $ctrl->initController($req, service('response'), service('logger'));

            $response = $ctrl->index();
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'TABLE VIEW') || str_contains($body, 'events-table-card') || str_contains($body, 'Belum Ada Event'), "Table view rendered");
            $this->assert(str_contains($body, 'view=grid'), "Switch to grid view toggle present");
        });

        // Reset request mock back to default
        \Config\Services::injectMock('request', $incomingRequest);

        // -------------------------------------------------------------
        // Test 3: Event Form Create View
        // -------------------------------------------------------------
        $this->testModule("Event Admin - Form Create View", function () use ($eventController) {
            $response = $eventController->create();
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'Tambah Event'), "Create page heading present");
            $this->assert(str_contains($body, '16:9'), "16:9 aspect ratio guideline present");
            $this->assert(str_contains($body, '1200'), "Recommended dimensions guideline present");
            $this->assert(str_contains($body, 'name="title"'), "Title input present");
            $this->assert(str_contains($body, 'name="type"'), "Type selector present");
            $this->assert(str_contains($body, 'name="mode"'), "Mode selector present");
            $this->assert(str_contains($body, 'name="status"'), "Status selector present");
            $this->assert(str_contains($body, 'name="visibility"'), "Visibility selector present");
            $this->assert(str_contains($body, 'name="start_at"'), "Start at datetime input present");
            $this->assert(str_contains($body, 'name="end_at"'), "End at datetime input present");
            $this->assert(str_contains($body, 'name="location"'), "Location input present");
            $this->assert(str_contains($body, 'name="meeting_url"'), "Meeting URL input present");
            $this->assert(str_contains($body, 'name="capacity"'), "Capacity input present");
            $this->assert(str_contains($body, 'name="waitlist_enabled"'), "Waitlist toggle present");
            $this->assert(str_contains($body, 'name="certificate_enabled"'), "Certificate toggle present");
            $this->assert(str_contains($body, 'name="summary"'), "Summary textarea present");
            $this->assert(str_contains($body, 'name="description"'), "Description textarea present");
            $this->assert(str_contains($body, 'name="cover"'), "Cover file input present");
        });

        // -------------------------------------------------------------
        // Test 4: Event Create & Store Database Execution
        // -------------------------------------------------------------
        $createdEventId = 0;
        $this->testModule("Event Admin - Store Event Execution", function () use (&$createdEventId, $adminId, $communicationService) {
            $createdEventId = $communicationService->createEvent([
                'title'                 => 'Bimtek Tata Kelola Pengadaan Khusus CI ' . time(),
                'type'                  => 'bimtek',
                'mode'                  => 'hybrid',
                'status'                => 'open_registration',
                'visibility'            => 'public',
                'start_at'              => date('Y-m-d H:i:s', strtotime('+3 days 09:00')),
                'end_at'                => date('Y-m-d H:i:s', strtotime('+3 days 16:00')),
                'registration_open_at'  => date('Y-m-d H:i:s', strtotime('-1 days')),
                'registration_close_at' => date('Y-m-d H:i:s', strtotime('+2 days 23:59')),
                'location'              => 'Gedung Pertemuan BegawiPBJ, Bandar Lampung',
                'meeting_url'           => 'https://zoom.us/j/9988776655',
                'capacity'              => 80,
                'waitlist_enabled'      => 1,
                'certificate_enabled'   => 1,
                'summary'               => 'Bimtek komprehensif pengadaan barang/jasa khusus.',
                'description'           => 'Rundown lengkap dan pedoman peserta pelatihan.',
            ], $adminId);

            $this->assert($createdEventId > 0, "Event successfully created in database with ID: " . $createdEventId);

            $eventModel = new EventModel();
            $check = $eventModel->find($createdEventId);
            $this->assert($check !== null, "Created event can be queried from database");
            $this->assert($check['mode'] === 'hybrid', "Event mode stored correctly as hybrid");
            $this->assert((int) $check['capacity'] === 80, "Event capacity stored correctly as 80");
        });

        // -------------------------------------------------------------
        // Test 5: Event Detail View
        // -------------------------------------------------------------
        $this->testModule("Event Admin - Detail View", function () use ($eventController, $createdEventId) {
            $response = $eventController->detail((string) $createdEventId);
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'Detail Event') || str_contains($body, 'Bimtek Tata Kelola'), "Detail page title present");
            $this->assert(str_contains($body, 'Presensi &amp; Kode Sandi Kehadiran') || str_contains($body, 'Presensi Event') || str_contains($body, 'Kode Sandi'), "Presensi management section present");
            $this->assert(str_contains($body, 'Penerbitan E-Sertifikat Event') || str_contains($body, 'Penerbitan Sertifikat'), "Batch certificate section present");
            $this->assert(str_contains($body, 'Daftar Peserta &amp; Status Presensi') || str_contains($body, 'Daftar Peserta'), "Participant list table present");
            $this->assert(str_contains($body, 'KAPASITAS PENDAFTARAN') || str_contains($body, 'Ringkasan Kapasitas'), "Capacity progress metric present");
        });

        // -------------------------------------------------------------
        // Test 6: Event Edit View
        // -------------------------------------------------------------
        $this->testModule("Event Admin - Form Edit View", function () use ($eventController, $createdEventId) {
            $response = $eventController->edit((string) $createdEventId);
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'Ubah Event'), "Edit page title present");
            $this->assert(str_contains($body, 'Bimtek Tata Kelola'), "Existing title populated in form");
            $this->assert(str_contains($body, 'Kontrol Presensi &amp; Kode Sandi') || str_contains($body, 'Presensi Event'), "Presensi quick panel present in edit form");
        });

        // -------------------------------------------------------------
        // Test 7: Event Update Execution
        // -------------------------------------------------------------
        $this->testModule("Event Admin - Update Event Execution", function () use ($createdEventId, $adminId, $communicationService) {
            $ok = $communicationService->updateEvent($createdEventId, [
                'title'    => 'Bimtek Tata Kelola PBJ Khusus (Updated)',
                'capacity' => 120,
                'status'   => 'open_registration',
            ], $adminId);

            $this->assert($ok === true, "Event updated successfully");
            $eventModel = new EventModel();
            $check = $eventModel->find($createdEventId);
            $this->assert($check['title'] === 'Bimtek Tata Kelola PBJ Khusus (Updated)', "Title updated properly");
            $this->assert((int) $check['capacity'] === 120, "Capacity updated properly to 120");
        });

        // -------------------------------------------------------------
        // Test 8: Attendance Code Lifecycle (Generate, Open, Close)
        // -------------------------------------------------------------
        $this->testModule("Event Admin - Attendance Code Lifecycle", function () use ($createdEventId, $adminId, $communicationService) {
            // Generate Code
            $code = $communicationService->generateAttendanceCode($createdEventId, $adminId);
            $this->assert(! empty($code) && str_starts_with($code, 'BGW-EVT-'), "Code generated with format BGW-EVT-XXXXX: " . $code);

            // Open Attendance
            $openedCode = $communicationService->openAttendance($createdEventId, $adminId);
            $this->assert(! empty($openedCode), "Attendance window opened with code: " . $openedCode);

            $eventModel = new EventModel();
            $evt = $eventModel->find($createdEventId);
            $this->assert(! empty($evt['attendance_open_at']), "attendance_open_at timestamp recorded");
            $this->assert($evt['attendance_close_at'] === null, "attendance_close_at is null when open");

            // Close Attendance
            $closedOk = $communicationService->closeAttendance($createdEventId, $adminId);
            $this->assert($closedOk === true, "Attendance window closed successfully");

            $evtClosed = $eventModel->find($createdEventId);
            $this->assert(! empty($evtClosed['attendance_close_at']), "attendance_close_at timestamp recorded");
        });

        // -------------------------------------------------------------
        // Test 9: Event Registrations Index View
        // -------------------------------------------------------------
        $this->testModule("Registrasi Event - Index View", function () use ($regController) {
            $response = $regController->index();
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'Registrasi &amp; Pendaftar Event') || str_contains($body, 'Registrasi Event'), "Registration page title present");
            $this->assert(str_contains($body, 'Total Registrasi'), "KPI Total Registrasi present");
            $this->assert(str_contains($body, 'Terdaftar'), "KPI Terdaftar present");
            $this->assert(str_contains($body, 'Peserta Hadir'), "KPI Peserta Hadir present");
            $this->assert(str_contains($body, 'Waitlist'), "KPI Waitlist present");
            $this->assert(str_contains($body, 'Dibatalkan'), "KPI Dibatalkan present");
            $this->assert(str_contains($body, 'name="event_id"'), "Event filter dropdown present");
            $this->assert(str_contains($body, 'name="status"'), "Status filter dropdown present");
            $this->assert(str_contains($body, 'name="q"'), "Search keyword input present");
            $this->assert(str_contains($body, 'reg-table-card') || str_contains($body, 'Belum Ada Data Pendaftar'), "Registration table or empty state rendered");
        });

        // -------------------------------------------------------------
        // Test 10: Event Registrations Detail View
        // -------------------------------------------------------------
        $this->testModule("Registrasi Event - Detail View", function () use ($regController, $db) {
            $reg = $db->table('event_registrations')->orderBy('id', 'ASC')->get()->getRowArray();
            if ($reg) {
                $response = $regController->detail((string) $reg['id']);
                $body = $this->extractBody($response);

                $this->assert(str_contains($body, 'Detail Registrasi Peserta') || str_contains($body, 'Detail Registrasi'), "Detail page header present");
                $this->assert(str_contains($body, 'Profil Peserta'), "Profile section present");
                $this->assert(str_contains($body, 'Agenda Kegiatan Terkait') || str_contains($body, 'Event:'), "Event info present");
                $this->assert(str_contains($body, 'Kelola Status Registrasi') || str_contains($body, 'Aksi Status'), "Status updater present");
            } else {
                $this->assert(true, "Skipping detail registration test (no registration records)");
            }
        });

        // -------------------------------------------------------------
        // Test 11: Attendance Index View
        // -------------------------------------------------------------
        $this->testModule("Kehadiran Event - Index View", function () use ($attController) {
            $response = $attController->index();
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'Log Kehadiran &amp; Presensi') || str_contains($body, 'Kehadiran Event') || str_contains($body, 'Kehadiran'), "Attendance page header present");
            $this->assert(str_contains($body, 'Total Presensi Tercatat') || str_contains($body, 'Total Presensi'), "KPI Total Presensi present");
            $this->assert(str_contains($body, 'Hadir Sah') || str_contains($body, 'Hadir'), "KPI Hadir Sah present");
            $this->assert(str_contains($body, 'name="event_id"'), "Event filter dropdown present");
            $this->assert(str_contains($body, 'name="status"'), "Status filter dropdown present");
            $this->assert(str_contains($body, 'att-table-card') || str_contains($body, 'Belum Ada Catatan Kehadiran'), "Attendance table or empty state rendered");
        });

        // -------------------------------------------------------------
        // Test 12: Waitlist & Cancellations View
        // -------------------------------------------------------------
        $this->testModule("Waitlist dan Pembatalan - View & Tabs", function () use ($regController) {
            $response = $regController->waitlistAndCancellation();
            $body = $this->extractBody($response);

            $this->assert(str_contains($body, 'Waitlist &amp; Pembatalan') || str_contains($body, 'Waitlist dan Pembatalan'), "Waitlist page header present");
            $this->assert(str_contains($body, 'Antrian Waitlist'), "Tab or KPI Antrian Waitlist present");
            $this->assert(str_contains($body, 'Daftar Pembatalan') || str_contains($body, 'Pembatalan'), "Tab or KPI Pembatalan present");
            $this->assert(str_contains($body, 'Berhasil Dipromosikan') || str_contains($body, 'Riwayat Promosi'), "Tab or KPI Promoted present");
            $this->assert(str_contains($body, 'name="event_id"'), "Event filter dropdown present");
        });

        // -------------------------------------------------------------
        // Test 13: Delete Event Execution (Cleanup)
        // -------------------------------------------------------------
        $this->testModule("Event Admin - Delete Event Cleanup", function () use ($createdEventId, $adminId, $communicationService) {
            $deleted = $communicationService->deleteEvent($createdEventId, $adminId);
            $this->assert($deleted === true, "Event successfully soft-deleted from database");

            $eventModel = new EventModel();
            $check = $eventModel->find($createdEventId);
            $this->assert($check === null, "Deleted event is soft-deleted and cannot be found via find()");
        });

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write(sprintf("   TEST RESULTS: %d PASSED, %d FAILED                            ", $this->passed, $this->failed), $this->failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        return $this->failed === 0 ? 0 : 1;
    }

    private function testModule(string $title, callable $callback): void
    {
        CLI::write("[RUN] " . $title . "...", "yellow");
        try {
            $callback();
            $this->passed++;
            CLI::write("  -> [PASS] " . $title, "green");
        } catch (\Throwable $e) {
            $this->failed++;
            CLI::write("  -> [FAIL] " . $title . ": " . $e->getMessage(), "red");
            CLI::write("     " . $e->getFile() . ":" . $e->getLine(), "light_gray");
        }
    }

    private function assert(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new \RuntimeException("Assertion failed: " . $message);
        }
    }

    private function extractBody($response): string
    {
        if (is_string($response)) {
            return $response;
        }
        if ($response instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $response->getBody();
        }
        return (string) $response;
    }
}

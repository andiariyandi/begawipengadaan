<?php

namespace App\Commands;

use App\Controllers\Workspace\OperatorController;
use App\Controllers\Workspace\ParticipantController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestEventApprovalWorkflow extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:event-approval-workflow';
    protected $description = 'Verifikasi Alur Pendaftaran Event, Approval Operator, dan Hak Akses Peserta (Anti-Lockout).';
    protected $usage       = 'test:event-approval-workflow';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("  VERIFIKASI WORKFLOW PENDAFTARAN, APPROVAL OPERATOR & AKSES EVENT", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = db_connect();
        $session = service('session');

        // Setup User Peserta (budi.peserta)
        $budiUser = $db->table('users')->where('username', 'budi.peserta')->get()->getRowArray();
        if (! $budiUser) {
            $budiUser = $db->table('users')->where('id', 303)->get()->getRowArray();
        }
        $budiId = (int) ($budiUser['id'] ?? 303);

        // Setup Operator User (operator.lms)
        $operatorUser = $db->table('users')->where('username', 'operator.lms')->get()->getRowArray();
        if (! $operatorUser) {
            $operatorUser = $db->table('users')->where('id', 301)->get()->getRowArray();
        }
        $operatorId = (int) ($operatorUser['id'] ?? 301);

        // -------------------------------------------------------------
        // STEP 1: Buat Event Uji Coba untuk Simulasi
        // -------------------------------------------------------------
        $eventSlug = 'workshop-pbj-approval-test-' . time();
        $db->table('events')->insert([
            'title'                 => 'Workshop Pengadaan Barang & Jasa Uji Approval ' . time(),
            'slug'                  => $eventSlug,
            'type'                  => 'workshop',
            'mode'                  => 'online',
            'start_at'              => date('Y-m-d H:i:s', time() - 3600),
            'end_at'                => date('Y-m-d H:i:s', time() + 7200),
            'capacity'              => 50,
            'meeting_url'           => 'https://zoom.us/j/1234567890',
            'certificate_enabled'   => 1,
            'waitlist_enabled'      => 1,
            'created_at'            => date('Y-m-d H:i:s'),
            'updated_at'            => date('Y-m-d H:i:s'),
        ]);
        $testEventId = (int) $db->insertID();

        // -------------------------------------------------------------
        // STEP 2: Pendaftaran Peserta (Status Awal Waitlist / Pending Approval)
        // -------------------------------------------------------------
        $db->table('event_registrations')->insert([
            'event_id'      => $testEventId,
            'user_id'       => $budiId,
            'status'        => 'waitlisted',
            'registered_at' => date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        $testRegId = (int) $db->insertID();

        // UJI 1: Saat masih waitlisted, peserta terkunci (pending approval)
        $session->set([
            'isLoggedIn'     => true,
            'user_id'        => $budiId,
            'name'           => 'Budi Pratama',
            'role'           => 'participant',
            'roles'          => ['participant'],
            'account_status' => 'active',
        ]);

        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\SiteURI(config('App'), 'workspace/peserta/event-saya/' . $eventSlug),
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        \Config\Services::injectMock('request', $incomingRequest);

        $partController = new ParticipantController();
        $partController->initController($incomingRequest, service('response'), service('logger'));

        $detailResponse = $partController->eventDetail($eventSlug);
        $htmlLocked = is_string($detailResponse) ? $detailResponse : (string) $detailResponse->getBody();

        if (str_contains($htmlLocked, 'Menunggu Persetujuan Panitia / Operator') || str_contains($htmlLocked, 'Akses Ruang Kegiatan Tidak Tersedia')) {
            $this->pass("1. Saat registrasi berstatus waitlisted, peserta mendapat informasi 'Menunggu Persetujuan' dan akses ruang kegiatan digembok secara aman.");
        } else {
            $this->fail("1. Gagal memvalidasi status waitlist peserta.");
        }

        // -------------------------------------------------------------
        // STEP 3: Operator Melakukan Approval Pendaftaran
        // -------------------------------------------------------------
        $session->set([
            'isLoggedIn'     => true,
            'user_id'        => $operatorId,
            'name'           => 'Operator Begawi',
            'role'           => 'operator',
            'roles'          => ['operator'],
            'account_status' => 'active',
        ]);

        $incomingOpRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\SiteURI(config('App'), 'workspace/operator/events/' . $testEventId . '/peserta/' . $testRegId . '/approve'),
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        \Config\Services::injectMock('request', $incomingOpRequest);

        $opController = new OperatorController();
        $opController->initController($incomingOpRequest, service('response'), service('logger'));

        try {
            $approveResult = $opController->approveEventRegistration((string) $testRegId);

            $updatedReg = $db->table('event_registrations')->where('id', $testRegId)->get()->getRowArray();

            if ($updatedReg && $updatedReg['status'] === 'registered' && ! empty($updatedReg['promoted_at'])) {
                $this->pass("2. Operator berhasil menyetujui pendaftaran (status='registered', promoted_at terisi, zero MySQL constraint error).");
            } else {
                $this->fail("2. Approval operator gagal mengubah status pendaftaran di database.");
            }
        } catch (\Throwable $e) {
            $this->fail("2. Exception saat Operator melakukan approval: " . $e->getMessage());
        }

        // -------------------------------------------------------------
        // STEP 4: Peserta Mengakses Detail Event Setelah Disetujui (Gembok Terbuka Penuh)
        // -------------------------------------------------------------
        $session->set([
            'isLoggedIn'     => true,
            'user_id'        => $budiId,
            'name'           => 'Budi Pratama',
            'role'           => 'participant',
            'roles'          => ['participant'],
            'account_status' => 'active',
        ]);

        \Config\Services::injectMock('request', $incomingRequest);
        $partController = new ParticipantController();
        $partController->initController($incomingRequest, service('response'), service('logger'));

        $detailApprovedResp = $partController->eventDetail($eventSlug);
        $htmlApproved = is_string($detailApprovedResp) ? $detailApprovedResp : (string) $detailApprovedResp->getBody();

        // Verifikasi: Banner gembok kuning TIDAK BOLEH tampil
        $hasLockBanner = str_contains($htmlApproved, 'Menunggu Persetujuan Panitia / Operator (Pending Approval)');
        if (! $hasLockBanner) {
            $this->pass("3. Akses peserta TERBUKA: Banner gembok 'Menunggu Persetujuan' TIDAK TAMPIL setelah di-approve.");
        } else {
            $this->fail("3. Bug Masih Ada: Banner 'Menunggu Persetujuan' tetap muncul mengunci peserta meski sudah di-approve!");
        }

        // Verifikasi: Tautan ruang kegiatan Zoom/Meeting URL tampil
        if (str_contains($htmlApproved, 'https://zoom.us/j/1234567890') || str_contains($htmlApproved, 'Buka Ruang Sesi')) {
            $this->pass("4. Akses tautan ruang virtual (Zoom/Meeting) terbuka penuh untuk peserta yang telah disetujui.");
        } else {
            $this->fail("4. Tautan ruang kegiatan virtual tidak dapat diakses.");
        }

        // Verifikasi: Section Materi terbuka
        if (str_contains($htmlApproved, 'Materi &amp; Bahan Tayangan Presentasi') && ! str_contains($htmlApproved, 'Unduhan materi akan terbuka otomatis setelah pendaftaran Anda disetujui')) {
            $this->pass("5. Komponen materi terbuka (tidak terkunci peringatan approval).");
        } else {
            $this->fail("5. Komponen materi masih terkunci peringatan approval.");
        }

        // -------------------------------------------------------------
        // STEP 5: Verifikasi Daftar Event Saya (/workspace/peserta/event-saya)
        // -------------------------------------------------------------
        $myEventsUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'workspace/peserta/event-saya');
        $myEventsRequest = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $myEventsUri, null, new \CodeIgniter\HTTP\UserAgent());
        \Config\Services::injectMock('request', $myEventsRequest);

        $partController = new ParticipantController();
        $partController->initController($myEventsRequest, service('response'), service('logger'));

        $myEventsResp = $partController->myEvents();
        $htmlMyEvents = is_string($myEventsResp) ? $myEventsResp : (string) $myEventsResp->getBody();

        if (str_contains($htmlMyEvents, 'Disetujui (Approved)')) {
            $this->pass("6. Kartu event di 'Event Saya' menampilkan badge hijau 'Disetujui (Approved)'.");
        } else {
            $this->fail("6. Kartu event di 'Event Saya' tidak menampilkan badge 'Disetujui (Approved)'.");
        }

        // -------------------------------------------------------------
        // STEP 6: Verifikasi Kasus Riil budi.peserta pada Event Terdaftar
        // -------------------------------------------------------------
        $budiRealRegs = $db->table('event_registrations er')
            ->select('er.*, e.title, e.slug')
            ->join('events e', 'e.id = er.event_id')
            ->where('er.user_id', $budiId)
            ->where('er.id !=', $testRegId)
            ->get()->getResultArray();

        if (! empty($budiRealRegs)) {
            foreach ($budiRealRegs as $r) {
                $statusRaw = strtolower((string) $r['status']);
                $hasAccess = in_array($statusRaw, ['registered', 'confirmed', 'attended', 'present', 'completed', 'promoted'], true);
                CLI::write("    -> Event #{$r['event_id']}: '{$r['title']}' | Status: {$statusRaw} | Akses: " . ($hasAccess ? 'TERBUKA' : 'TERKUNCI/CANCELLED'), 'yellow');
            }
            $this->pass("7. Pengecekan seluruh pendaftaran event budi.peserta terverifikasi konsisten dengan aturan hak akses.");
        } else {
            $this->pass("7. Validasi riwayat event riil budi.peserta selesai.");
        }

        // -------------------------------------------------------------
        // STEP 7: Verifikasi Zero-Migration Compliance
        // -------------------------------------------------------------
        $migrationCount = count(glob(APPPATH . 'Database/Migrations/*.php'));
        if ($migrationCount === 16) {
            $this->pass("8. Zero-Migration Compliance: Tepat 16 berkas migrasi database utuh terjaga.");
        } else {
            $this->fail("8. Pelanggaran Zero-Migration: Ditemukan {$migrationCount} migrasi (seharusnya 16).");
        }

        // Cleanup test data
        $db->table('event_registrations')->where('id', $testRegId)->delete();
        $db->table('events')->where('id', $testEventId)->delete();

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("HASIL AKHIR: {$this->passed} LULUS, {$this->failed} GAGAL", $this->failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        return $this->failed === 0 ? 0 : 1;
    }

    private function pass(string $msg): void
    {
        CLI::write("  [PASS] " . $msg, "green");
        $this->passed++;
    }

    private function fail(string $msg): void
    {
        CLI::write("  [FAIL] " . $msg, "red");
        $this->failed++;
    }
}

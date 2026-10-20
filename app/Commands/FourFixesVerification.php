<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FourFixesVerification extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:four-fixes';
    protected $description = 'Verifies the 4 bug fixes and workflows requested by user';
    protected $usage       = 'php spark test:four-fixes';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "yellow");
        CLI::write("      AUDIT & VERIFIKASI 4 ITEM BUG FIX & PERBAIKAN WORKSPACE     ", "yellow");
        CLI::write("==================================================================", "yellow");

        $db = \Config\Database::connect();
        $passed = 0;
        $failed = 0;

        // -------------------------------------------------------------
        // ITEM 1: Cover / Banner Gambar Kelas di /workspace/trainer/kelas/
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("--- [ITEM 1] Banner / Cover Gambar Kelas ---", "cyan");
        $fistudyImg = FCPATH . 'assets/fistudy/images/resources/course-1-1.png';
        $mainImg    = FCPATH . 'assets/main/images/resources/course-details-img-1.jpg';

        if (file_exists($fistudyImg) && file_exists($mainImg)) {
            CLI::write("  [OK] Asset cover fallback valid dan ditemukan di disk:", "green");
            CLI::write("       - " . $fistudyImg . " (" . filesize($fistudyImg) . " bytes)");
            CLI::write("       - " . $mainImg . " (" . filesize($mainImg) . " bytes)");
            $passed++;
        } else {
            CLI::error("  [FAIL] Missing image asset!");
            $failed++;
        }

        // -------------------------------------------------------------
        // ITEM 2: Sinkronisasi Kategori Layanan Workspace vs Landing Page Kontak
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("--- [ITEM 2] Kategori Layanan Helpdesk & Kontak ---", "cyan");
        $wsCategories = \App\Controllers\Workspace\ConsultationController::getCategories();
        CLI::write("  Kategori Helpdesk Workspace (" . count($wsCategories) . " kategori):");
        foreach ($wsCategories as $cat) {
            CLI::write("    - $cat");
        }

        if (count($wsCategories) === 7 && in_array('Konsultasi Perencanaan Pengadaan', $wsCategories, true)) {
            CLI::write("  [OK] ConsultationController::getCategories() menyediakan 7 kategori resmi.", "green");
            $passed++;
        } else {
            CLI::error("  [FAIL] Kategori helpdesk tidak lengkap!");
            $failed++;
        }

        // -------------------------------------------------------------
        // ITEM 3: Closed Ticket Response Restriction (View Only untuk Pengirim)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("--- [ITEM 3] Closed Ticket Response Restriction ---", "cyan");
        $testTicketNumber = 'TKT-CLOSED-AUDIT';
        $existingTicket = $db->table('contact_messages')->where('ticket_number', $testTicketNumber)->get()->getRowArray();
        if (! $existingTicket) {
            $db->table('contact_messages')->insert([
                'ticket_number'    => $testTicketNumber,
                'sender_name'      => 'Budi Peserta',
                'sender_email'     => 'budi.peserta@lampungprov.go.id',
                'sender_phone'     => '08123456789',
                'service_category' => 'Konsultasi Perencanaan Pengadaan',
                'subject'          => 'Uji Coba Closed Ticket Restriction',
                'message'          => 'Pertanyaan awal tiket audit.',
                'status'           => 'closed',
                'admin_notes'      => json_encode([
                    'user_id' => 303,
                    'threads' => [
                        [
                            'id'          => 1,
                            'sender_type' => 'user',
                            'sender_name' => 'Budi Peserta',
                            'channel'     => 'portal',
                            'message'     => 'Pertanyaan awal tiket audit.',
                            'created_at'  => date('Y-m-d H:i:s'),
                        ],
                        [
                            'id'          => 2,
                            'sender_type' => 'admin',
                            'sender_name' => 'Admin PBJ',
                            'channel'     => 'portal',
                            'message'     => 'Tiket telah diselesaikan secara resmi oleh tim PBJ Lampung.',
                            'created_at'  => date('Y-m-d H:i:s'),
                        ]
                    ],
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $ticketId = $db->insertID();
        } else {
            $ticketId = $existingTicket['id'];
            $db->table('contact_messages')->where('id', $ticketId)->update(['status' => 'closed']);
        }

        $ticket = $db->table('contact_messages')->where('id', $ticketId)->get()->getRowArray();
        $notes = \App\Models\Communication\ContactMessageModel::parseAdminNotes($ticket['admin_notes']);

        CLI::write("  Ticket status in DB: " . ($ticket['status'] ?? 'NULL'));

        // Check non-admin view
        $userHtml = view('main/workspace/pages/konsultasi_detail', [
            'title'          => 'Tiket #' . $ticket['ticket_number'],
            'pageTitle'      => 'Detail Tiket',
            'breadcrumbTail' => [],
            'ticket'         => $ticket,
            'notes'          => $notes,
            'isAdmin'        => false,
        ]);

        $hasNotice = strpos($userHtml, 'Tiket Telah Selesai & Ditutup') !== false;
        $hasReplyInput = strpos($userHtml, 'name="reply_message"') !== false;
        CLI::write("  hasNotice: " . ($hasNotice ? 'YES' : 'NO') . ", hasReplyInput: " . ($hasReplyInput ? 'YES' : 'NO'));

        if ($hasNotice && ! $hasReplyInput) {
            CLI::write("  [OK] Tampilan pengguna umum/pemohon pada tiket closed: Form balasan tertutup & status view only tampil.", "green");
            $passed++;
        } else {
            CLI::error("  [FAIL] Form balasan tidak tersembunyi untuk tiket closed!");
            $failed++;
        }

        // Check admin view
        $adminHtml = view('main/workspace/pages/konsultasi_detail', [
            'title'          => 'Tiket #' . $ticket['ticket_number'],
            'pageTitle'      => 'Detail Tiket',
            'breadcrumbTail' => [],
            'ticket'         => $ticket,
            'notes'          => $notes,
            'isAdmin'        => true,
        ]);

        if (strpos($adminHtml, 'Mode Administrator:') !== false && strpos($adminHtml, 'name="reply_message"') !== false) {
            CLI::write("  [OK] Tampilan Administrator pada tiket closed: Admin tetap memiliki akses mengirimkan tanggapan.", "green");
            $passed++;
        } else {
            CLI::error("  [FAIL] Akses administrator tidak sesuai pada tiket closed!");
            $failed++;
        }

        // -------------------------------------------------------------
        // ITEM 4: Alur Ajukan Perubahan Profil Workspace & Persetujuan Admin/Operator
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("--- [ITEM 4] Alur Ajukan Perubahan Profil & Persetujuan ---", "cyan");

        $testUser = $db->table('users')->where('id', 303)->get()->getRowArray();
        $origName = $testUser['name'];
        $origPhone = $testUser['phone'] ?? '';

        $db->table('profile_change_requests')->where('user_id', 303)->delete();

        $service = new \App\Services\Admin\ProfileChangeRequestService();
        $requestedPayload = [
            'full_name'     => 'Budi Pratama S.T., M.M.',
            'phone'         => '081299998888',
            'position_name' => 'Pejabat Pembuat Komitmen (PPK)',
            'unit_name'     => 'Dinas Bina Marga Provinsi Lampung',
            'address_line'  => 'Jl. Pangeran Emir M. Noer No. 45, Teluk Betung',
            'province_code' => '18',
        ];
        $reason = 'Penyesuaian gelar magister dan mutasi jabatan menjadi PPK.';

        $requestId = $service->submit(303, $requestedPayload, $reason);
        $savedReq = $db->table('profile_change_requests')->where('id', $requestId)->get()->getRowArray();

        if ($savedReq && $savedReq['status'] === 'submitted') {
            CLI::write("  [OK] Pengajuan perubahan profil tersimpan di profile_change_requests dengan status 'submitted'.", "green");
            $passed++;
        } else {
            CLI::error("  [FAIL] Pengajuan gagal disimpan!");
            $failed++;
        }

        // Test view user profil showing pending status
        $profileViewHtml = view('main/workspace/pages/profil', [
            'title'         => 'Profil Saya',
            'subTitle'      => 'Profil Saya',
            'user'          => $testUser,
            'profile'       => $db->table('user_profiles')->where('user_id', 303)->get()->getRowArray() ?: [],
            'detail'        => $db->table('asn_profiles')->where('user_id', 303)->get()->getRowArray() ?: [],
            'profileType'   => 'asn',
            'provinces'     => (new \App\Models\WilayahModel())->provinces(),
            'latestRequest' => $savedReq,
        ]);

        if (strpos($profileViewHtml, 'Permohonan Perubahan Profil Sedang Diproses') !== false) {
            CLI::write("  [OK] Halaman /workspace/profil menampilkan banner status pengajuan sedang diproses.", "green");
            $passed++;
        } else {
            CLI::error("  [FAIL] Banner status pengajuan tidak muncul di halaman profil!");
            $failed++;
        }

        // Test approve request
        $service->approve($requestId, 13, 'Disetujui oleh Super Admin / Operator.');
        $updatedUser = $db->table('users')->where('id', 303)->get()->getRowArray();
        $updatedProfile = $db->table('user_profiles')->where('user_id', 303)->get()->getRowArray();
        $updatedAsn = $db->table('asn_profiles')->where('user_id', 303)->get()->getRowArray();

        if ($updatedUser['name'] === 'Budi Pratama S.T., M.M.' && $updatedUser['phone'] === '081299998888') {
            CLI::write("  [OK] Persetujuan berhasil: tabel users terupdate atomik (Nama & Telepon).", "green");
            $passed++;
        } else {
            CLI::error("  [FAIL] Tabel users tidak terupdate!");
            $failed++;
        }

        if ($updatedProfile['address_line'] === 'Jl. Pangeran Emir M. Noer No. 45, Teluk Betung') {
            CLI::write("  [OK] Tabel user_profiles terupdate atomik (Alamat domisili).", "green");
            $passed++;
        } else {
            CLI::error("  [FAIL] Tabel user_profiles tidak terupdate!");
            $failed++;
        }

        if ($updatedAsn['position_name'] === 'Pejabat Pembuat Komitmen (PPK)') {
            CLI::write("  [OK] Tabel asn_profiles terupdate atomik (Jabatan PPK).", "green");
            $passed++;
        } else {
            CLI::error("  [FAIL] Tabel asn_profiles tidak terupdate!");
            $failed++;
        }

        // Check user notification
        $notif = $db->table('notifications')
            ->where('user_id', 303)
            ->where('type', 'profile_change_approved')
            ->orderBy('created_at', 'DESC')
            ->get()->getRowArray();

        if ($notif) {
            CLI::write("  [OK] Notifikasi persetujuan berhasil terkirim ke akun pemohon (User 303).", "green");
            $passed++;
        } else {
            CLI::error("  [FAIL] Notifikasi tidak terkirim!");
            $failed++;
        }

        // Revert test user data
        $db->table('users')->where('id', 303)->update(['name' => $origName, 'phone' => $origPhone]);
        $db->table('asn_profiles')->where('user_id', 303)->update(['position_name' => 'Staf Perencanaan PBJ', 'unit_name' => 'Dinas Pendidikan Provinsi Lampung']);
        $db->table('contact_messages')->where('ticket_number', $testTicketNumber)->delete();
        $db->table('profile_change_requests')->where('id', $requestId)->delete();
        if ($notif) {
            $db->table('notifications')->where('id', $notif['id'])->delete();
        }

        CLI::newLine();
        CLI::write("==================================================================", "yellow");
        CLI::write("   RINGKASAN HASIL PENGUJIAN: $passed BERHASIL, $failed GAGAL     ", $failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "yellow");

        return $failed === 0 ? 0 : 1;
    }
}

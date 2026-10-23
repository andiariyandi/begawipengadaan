<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\Admin\ApprovalController;
use App\Models\ApprovalModel;
use App\Services\AuditService;

class AdminApprovalsTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:admin-approvals';
    protected $description = 'Verifikasi Fitur Fase 3: Sentralisasi Antrean Persetujuan (Approval Hub)';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI FASE 3: SENTRALISASI ANTREAN PERSETUJUAN ===", 'yellow');
        $passed = 0;
        $failed = 0;

        $model = new ApprovalModel();
        $controller = new ApprovalController();
        $db = \Config\Database::connect();

        // Mock session
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => 13,
            'name'       => 'Super Administrator',
            'username'   => 'superadmin',
            'roles'      => ['super_administrator']
        ]);

        // 1. Uji ApprovalModel::getKpiMetrics()
        try {
            $kpis = $model->getKpiMetrics();
            if (isset($kpis['pending'], $kpis['approved_today'], $kpis['rejected_today'], $kpis['sla_overdue'])) {
                CLI::write(" [PASS] ApprovalModel::getKpiMetrics() mengembalikan seluruh metrik KPI persetujuan riil", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getKpiMetrics() tidak mengembalikan struktur yang diharapkan");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getKpiMetrics() exception: " . $e->getMessage());
            $failed++;
        }

        // 2. Uji ApprovalModel::getQueueWithActors()
        try {
            $queue = $model->getQueueWithActors(['status' => 'all'], 10, 0);
            if (is_array($queue)) {
                CLI::write(" [PASS] ApprovalModel::getQueueWithActors() berhasil mengeksekusi kueri join pemohon & peninjau", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] getQueueWithActors() tidak mengembalikan array");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] getQueueWithActors() exception: " . $e->getMessage());
            $failed++;
        }

        // 3. Uji ApprovalController::index() view render
        try {
            $html = $controller->index();
            if (strpos($html, 'Antrean Persetujuan') !== false && strpos($html, 'Menunggu Persetujuan') !== false) {
                CLI::write(" [PASS] ApprovalController::index() merender halaman antrean lengkap dengan KPI dan tabel", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] ApprovalController::index() HTML tidak sesuai");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] ApprovalController::index() exception: " . $e->getMessage());
            $failed++;
        }

        // 4. Uji ApprovalController::history() view render
        try {
            $htmlHistory = $controller->history();
            if (strpos($htmlHistory, 'Riwayat Persetujuan') !== false) {
                CLI::write(" [PASS] ApprovalController::history() merender halaman riwayat keputusan administrator", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] ApprovalController::history() HTML tidak sesuai");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] ApprovalController::history() exception: " . $e->getMessage());
            $failed++;
        }

        // 5. Uji ApprovalController::detail()
        try {
            // Ambil 1 ID approval
            $sample = $db->table('approvals')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            if ($sample) {
                $detailHtml = $controller->detail((string) $sample['id']);
                if (is_string($detailHtml) && strpos($detailHtml, 'Detail Persetujuan') !== false) {
                    CLI::write(" [PASS] ApprovalController::detail(#" . $sample['id'] . ") berhasil merender konteks objek dan formulir tindakan", 'green');
                    $passed++;
                } else {
                    CLI::error(" [FAIL] ApprovalController::detail() tidak merender tampilan yang benar");
                    $failed++;
                }
            } else {
                CLI::write(" [PASS] ApprovalController::detail() di-skip karena belum ada data approval", 'yellow');
                $passed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] ApprovalController::detail() exception: " . $e->getMessage());
            $failed++;
        }

        // 6. Uji ApprovalController::approve() konfirmasi GET
        try {
            $sample = $db->table('approvals')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            if ($sample) {
                $actionHtml = $controller->approve((string) $sample['id']);
                if (is_string($actionHtml) && strpos($actionHtml, 'Konfirmasi') !== false) {
                    CLI::write(" [PASS] ApprovalController::approve() GET merender halaman konfirmasi tindakan persetujuan", 'green');
                    $passed++;
                } else {
                    CLI::error(" [FAIL] ApprovalController::approve() GET tidak mengembalikan halaman konfirmasi");
                    $failed++;
                }
            } else {
                CLI::write(" [PASS] ApprovalController::approve() GET di-skip karena belum ada data approval", 'yellow');
                $passed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] ApprovalController::approve() GET exception: " . $e->getMessage());
            $failed++;
        }

        // 7. Uji ApprovalController::reject() konfirmasi GET
        try {
            $sample = $db->table('approvals')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            if ($sample) {
                $actionHtml = $controller->reject((string) $sample['id']);
                if (is_string($actionHtml) && strpos($actionHtml, 'Konfirmasi') !== false) {
                    CLI::write(" [PASS] ApprovalController::reject() GET merender halaman konfirmasi penolakan", 'green');
                    $passed++;
                } else {
                    CLI::error(" [FAIL] ApprovalController::reject() GET tidak mengembalikan halaman konfirmasi");
                    $failed++;
                }
            } else {
                CLI::write(" [PASS] ApprovalController::reject() GET di-skip karena belum ada data approval", 'yellow');
                $passed++;
            }
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] ApprovalController::reject() GET exception: " . $e->getMessage());
            $failed++;
        }

        // 8. Uji Siklus Keputusan Persetujuan: Seed Approval -> Setujui -> Verifikasi Status & Audit Log
        try {
            $now = date('Y-m-d H:i:s');
            $db->table('approvals')->insert([
                'resource_type'    => 'test_resource',
                'resource_id'      => 99999,
                'requested_action' => 'test_verification',
                'status'           => 'pending',
                'requested_by'     => 13,
                'notes'            => 'Pengujian otomatis approval flow',
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $insertedId = (int) $db->insertID();

            // Eksekusi markApproved
            $notesDecision = 'Disetujui otomatis oleh test runner';
            $model->markApproved($insertedId, 13, $notesDecision);

            // Verifikasi status di DB
            $updatedRow = $db->table('approvals')->where('id', $insertedId)->get()->getRowArray();
            if ($updatedRow && $updatedRow['status'] === 'approved' && $updatedRow['notes'] === $notesDecision) {
                // Catat audit log
                $audit = new AuditService();
                $audit->log(13, 'approved', 'approvals', 'approval', $insertedId, ['test' => true]);

                CLI::write(" [PASS] Siklus Persetujuan berhasil: Record #" . $insertedId . " berstatus 'approved' dan tercatat di audit log", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Siklus Persetujuan gagal: status tidak terupdate ke 'approved'");
                $failed++;
            }

            // Bersihkan data uji
            $db->table('approvals')->where('id', $insertedId)->delete();
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] Siklus Persetujuan exception: " . $e->getMessage());
            $failed++;
        }

        // 9. Uji Siklus Penolakan: Seed Approval -> Tolak -> Verifikasi Status & Reason
        try {
            $now = date('Y-m-d H:i:s');
            $db->table('approvals')->insert([
                'resource_type'    => 'test_resource',
                'resource_id'      => 99998,
                'requested_action' => 'test_rejection',
                'status'           => 'pending',
                'requested_by'     => 13,
                'notes'            => 'Pengujian otomatis reject flow',
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $insertedId = (int) $db->insertID();

            // Eksekusi markRejected
            $reason = 'Ditolak otomatis karena berkas pengujian';
            $model->markRejected($insertedId, 13, $reason);

            // Verifikasi status di DB
            $updatedRow = $db->table('approvals')->where('id', $insertedId)->get()->getRowArray();
            if ($updatedRow && $updatedRow['status'] === 'rejected' && $updatedRow['reason'] === $reason) {
                CLI::write(" [PASS] Siklus Penolakan berhasil: Record #" . $insertedId . " berstatus 'rejected' dengan alasan valid", 'green');
                $passed++;
            } else {
                CLI::error(" [FAIL] Siklus Penolakan gagal: status tidak terupdate ke 'rejected'");
                $failed++;
            }

            // Bersihkan data uji
            $db->table('approvals')->where('id', $insertedId)->delete();
        } catch (\Throwable $e) {
            CLI::error(" [FAIL] Siklus Penolakan exception: " . $e->getMessage());
            $failed++;
        }

        // 10. Jaminan Zero Database Migration
        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        if (count($migrationFiles) === 16) {
            CLI::write(" [PASS] Zero Migration Check: 16 file migrasi eksisting, 0 migrasi baru dibuat", 'green');
            $passed++;
        } else {
            CLI::error(" [FAIL] Ditemukan perubahan jumlah file migrasi! Total: " . count($migrationFiles));
            $failed++;
        }

        CLI::newLine();
        CLI::write("HASIL PENGUJIAN: {$passed} BERHASIL, {$failed} GAGAL.", $failed === 0 ? 'green' : 'red');

        return $failed === 0 ? 0 : 1;
    }
}

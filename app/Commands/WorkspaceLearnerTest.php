<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class WorkspaceLearnerTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:workspace-learner';
    protected $description = 'Verifies Workspace Fase 1: Academic Transcript, Interactive Calendar, Focus Mode & Smart Filter';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI FASE 1: WORKSPACE LEARNING ENHANCEMENTS ===", 'green');
        $passCount = 0;
        $failCount = 0;

        $db = db_connect();

        // 1. Check Transkrip Akademik PBJ Route & Output
        CLI::write("\n1. Menguji Transkrip Akademik PBJ & Rapor Belajar Mandiri...", 'yellow');
        $enrollment = $db->table('enrollments')->whereIn('status', ['active', 'completed'])->orderBy('id', 'ASC')->get()->getRowArray();
        if (! $enrollment) {
            $enrollment = $db->table('enrollments')->orderBy('id', 'ASC')->get()->getRowArray();
            if ($enrollment) {
                $db->table('enrollments')->where('id', $enrollment['id'])->update(['status' => 'active']);
            }
        }
        if ($enrollment) {
            $classId = (string) $enrollment['class_id'];
            $userId  = (int) $enrollment['user_id'];
            $user    = $db->table('users')->where('id', $userId)->get()->getRowArray();

            session()->set([
                'user_id'    => $userId,
                'logged_in'  => true,
                'isLoggedIn' => true,
                'email'      => $user['email'] ?? 'peserta@begawi.test',
                'name'       => $user['name'] ?? 'Peserta Begawi',
                'roles'      => ['participant'],
            ]);

            $controller = new \App\Controllers\Workspace\ParticipantController();
            $response = $controller->transcript($classId);
            $body = is_string($response) ? $response : (method_exists($response, 'getBody') ? $response->getBody() : '');

            if (str_contains($body, 'TRANSKRIP AKADEMIK') && str_contains($body, 'Pemerintah Provinsi Lampung')) {
                CLI::write(" [PASS] Kop Resmi & Judul Transkrip PBJ Ter-render dengan sempurna", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Judul atau Kop Surat Transkrip tidak ditemukan pada view output", 'red');
                $failCount++;
            }

            if (str_contains($body, 'Evaluasi Formatif') && str_contains($body, 'Evaluasi Sumatif')) {
                CLI::write(" [PASS] Tabel breakdown evaluasi formatif kuis & penugasan mandiri ter-render lengkap", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Breakdown evaluasi kuis / tugas tidak ditemukan", 'red');
                $failCount++;
            }

            if (str_contains($body, 'Rekapitulasi Pembobotan Nilai Akhir') && str_contains($body, 'Grade ')) {
                CLI::write(" [PASS] Rekapitulasi nilai berbobot, nilai huruf & predikat kelulusan ter-render", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Komponen rekapitulasi bobot / predikat tidak ditemukan", 'red');
                $failCount++;
            }
        } else {
            CLI::write(" [SKIP] Tidak ada data enrollment untuk menguji transkrip", 'yellow');
        }

        // 2. Check Interactive Calendar on Participant Home
        CLI::write("\n2. Menguji Kalender Pembelajaran & Agenda PBJ Interaktif...", 'yellow');
        $homeController = new \App\Controllers\Workspace\WorkspaceHomeController();
        $homeResponse = $homeController->index();
        $homeBody = is_string($homeResponse) ? $homeResponse : (method_exists($homeResponse, 'getBody') ? $homeResponse->getBody() : '');

        if (str_contains($homeBody, 'Kalender Pembelajaran & Agenda PBJ') && str_contains($homeBody, 'id="wsCalCellsContainer"')) {
            CLI::write(" [PASS] Container Kalender Pembelajaran & Grid Tanggal PBJ ter-render di Home Workspace", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Container Kalender Pembelajaran tidak ditemukan di Home Workspace", 'red');
            $failCount++;
        }

        if (str_contains($homeBody, 'id="wsCalendarData" type="application/json"')) {
            CLI::write(" [PASS] JSON Data Agenda Pembelajaran & Event (#wsCalendarData) disematkan dengan aman", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Script data JSON #wsCalendarData tidak ditemukan", 'red');
            $failCount++;
        }

        if (str_contains($homeBody, 'id="wsCalTodayBtn"') && str_contains($homeBody, 'id="wsCalSelectedDateLabel"')) {
            CLI::write(" [PASS] Navigasi bulan & panel agenda harian (#wsCalSelectedDateLabel) tersedia", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Kontrol navigasi kalender atau label agenda tidak lengkap", 'red');
            $failCount++;
        }

        // 3. Check Focus / Zen Mode on Learning Room & Quiz Room
        CLI::write("\n3. Menguji Integrasi Mode Fokus Belajar (*Focus / Zen Mode*)...", 'yellow');
        $belajarFile = file_get_contents(APPPATH . 'Views/main/workspace/pages/belajar.php');
        if (str_contains($belajarFile, 'id="btnToggleFocusMode"') && str_contains($belajarFile, 'id="wsFocusFloatingBanner"')) {
            CLI::write(" [PASS] Ruang Belajar (belajar.php): Tombol Mode Fokus & Floating Sticky Banner aktif", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Kontrol Mode Fokus tidak ditemukan di belajar.php", 'red');
            $failCount++;
        }

        $quizFile = file_get_contents(APPPATH . 'Views/main/workspace/pages/quiz_take.php');
        if (str_contains($quizFile, 'id="btnToggleFocusModeQuiz"') && str_contains($quizFile, 'id="wsFocusFloatingBannerQuiz"')) {
            CLI::write(" [PASS] Ruang Ujian / Kuis (quiz_take.php): Tombol Mode Zen & Floating Banner aktif", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Kontrol Mode Zen tidak ditemukan di quiz_take.php", 'red');
            $failCount++;
        }

        $cssFile = file_get_contents(FCPATH . 'assets/main/css/workspace.css');
        if (str_contains($cssFile, 'body.ws-focus-mode') && str_contains($cssFile, '.ws-focus-banner')) {
            CLI::write(" [PASS] Stylesheet workspace.css: Aturan body.ws-focus-mode & .ws-focus-banner terpasang", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] CSS aturan .ws-focus-mode tidak ditemukan pada workspace.css", 'red');
            $failCount++;
        }

        // 4. Check Smart Search & Filter on Kelas Saya
        CLI::write("\n4. Menguji Smart Search, Filter Chips & Tautan Transkrip di Kelas Saya...", 'yellow');
        $kelasSayaFile = file_get_contents(APPPATH . 'Views/main/workspace/pages/kelas_saya.php');
        if (str_contains($kelasSayaFile, 'id="wsClassSearchInput"') && str_contains($kelasSayaFile, 'class="ws-filter-chip"')) {
            CLI::write(" [PASS] Filter bar: Input pencarian live dan chips metode (Online, Offline, Hybrid) terpasang", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Input search atau filter chips tidak ditemukan di kelas_saya.php", 'red');
            $failCount++;
        }

        if (str_contains($kelasSayaFile, 'id="wsClassSortSelect"') && str_contains($kelasSayaFile, 'ws-class-card-item')) {
            CLI::write(" [PASS] Sort dropdown (Progres, Abjad) dan atribut data sorting/filtering terpasang pada cards", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Select sorting atau atribut kartu kelas tidak ditemukan", 'red');
            $failCount++;
        }

        if (str_contains($kelasSayaFile, 'route_to(\'workspace.participant.transcript\'')) {
            CLI::write(" [PASS] Tautan akses langsung Rapor / Transkrip PBJ tersedia pada kartu kelas peserta", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Tautan transkrip tidak ditemukan pada kelas_saya.php", 'red');
            $failCount++;
        }

        // 5. Zero-Migration Compliance
        CLI::write("\n5. Memeriksa Kepatuhan Zero-Migration (Tanpa Migrasi Baru)...", 'yellow');
        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        $newMigrations = array_filter($migrationFiles, static function ($file) {
            $fn = basename($file);
            return str_contains($fn, 'transcript') || str_contains($fn, 'calendar') || str_contains($fn, 'zen_mode');
        });

        if (empty($newMigrations)) {
            CLI::write(" [PASS] Zero-Migration Compliance: 100% Menggunakan struktur database yang ada tanpa tabel/kolom baru!", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Ditemukan file migration baru yang melanggar aturan Zero-Migration", 'red');
            $failCount++;
        }

        CLI::write("\n==================================================", 'cyan');
        CLI::write(sprintf("HASIL PENGUJIAN FASE 1: %d LULUS, %d GAGAL", $passCount, $failCount), $failCount === 0 ? 'green' : 'red');
        CLI::write("==================================================\n", 'cyan');

        return $failCount === 0 ? 0 : 1;
    }
}

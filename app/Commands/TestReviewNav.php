<?php

namespace App\Commands;

use App\Controllers\Workspace\ParticipantController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestReviewNav extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:review-nav';
    protected $description = 'Verifies review navigation for modules, lessons, and quizzes without force-redirecting to post-test.';

    public function run(array $params): int
    {
        $db = \Config\Database::connect();
        $class = $db->table('learning_classes')->where('id', 8)->get()->getRowArray();
        $user = $db->table('users')->where('id', 13)->get()->getRowArray()
            ?: $db->table('users')->where('email', 'budi.santoso@peserta.begawi.id')->get()->getRowArray();

        $userId = (int) $user['id'];
        $classId = (int) $class['id'];

        CLI::write("==================================================================", "cyan");
        CLI::write("   VERIFIKASI NAVIGASI REVIEW MATERI, MODUL, & KUIS TANPA REDIRECT", "yellow");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        session()->set([
            'isLoggedIn'     => true,
            'user_id'        => $userId,
            'username'       => $user['username'] ?? 'peserta_test',
            'account_status' => 'active',
            'role'           => 'participant',
            'roles'          => ['participant'],
            'email'          => $user['email'],
        ]);

        $ctrl = new ParticipantController();
        $ctrl->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        // 1. Test resolveClassLearningState: all completed and post-test reached items must be unlocked
        $ref = new \ReflectionClass($ctrl);
        $m = $ref->getMethod('resolveClassLearningState');
        $m->setAccessible(true);
        $state = $m->invoke($ctrl, $classId, $userId);

        $allUnlocked = true;
        foreach ($state['modules'] as $mod) {
            if (! $mod['is_unlocked']) {
                $allUnlocked = false;
            }
            foreach ($mod['lessons'] as $les) {
                if (! $les['is_unlocked']) {
                    $allUnlocked = false;
                }
            }
            if (! empty($mod['quiz']) && ! $mod['quiz']['is_unlocked']) {
                $allUnlocked = false;
            }
        }

        if ($allUnlocked && $state['post_test']['is_unlocked']) {
            CLI::write("[PASS] Seluruh modul, materi, dan quiz berstatus unlocked (reviewable) saat berada di tahap lanjut / pasca post-test.", "green");
        } else {
            CLI::error("[FAIL] Ditemukan materi atau kuis yang terkunci padahal sudah melewati tahap tersebut.");
            return 1;
        }

        // 2. Test lessonDetail('5') direct access
        $resLesson5 = $ctrl->lessonDetail('5');
        if ($resLesson5 instanceof \CodeIgniter\HTTP\RedirectResponse) {
            $dest = $resLesson5->getHeaderLine('Location');
            CLI::error("[FAIL] lessonDetail(5) me-redirect ke: {$dest}");
            return 1;
        }
        CLI::write("[PASS] lessonDetail(5) berhasil dibuka tanpa auto-redirect ke post-test.", "green");

        // 3. Test lessonDetail('6') direct access
        $resLesson6 = $ctrl->lessonDetail('6');
        if ($resLesson6 instanceof \CodeIgniter\HTTP\RedirectResponse) {
            $dest = $resLesson6->getHeaderLine('Location');
            CLI::error("[FAIL] lessonDetail(6) me-redirect ke: {$dest}");
            return 1;
        }
        CLI::write("[PASS] lessonDetail(6) berhasil dibuka tanpa auto-redirect ke post-test.", "green");

        // 4. Test takeQuiz('16') (Modular quiz review access)
        $resQuiz16 = $ctrl->takeQuiz('16');
        if ($resQuiz16 instanceof \CodeIgniter\HTTP\RedirectResponse) {
            $dest = $resQuiz16->getHeaderLine('Location');
            CLI::error("[FAIL] takeQuiz(16) me-redirect ke: {$dest}");
            return 1;
        }
        CLI::write("[PASS] takeQuiz(16) (Kuis Modul) berhasil dibuka langsung untuk review tanpa force-redirect.", "green");

        // 5. Test study('8') Learning Room view (Must NOT force-redirect to quiz 17)
        $resStudy = $ctrl->study('8');
        if ($resStudy instanceof \CodeIgniter\HTTP\RedirectResponse) {
            $dest = $resStudy->getHeaderLine('Location');
            CLI::error("[FAIL] study(8) me-redirect ke: {$dest}");
            return 1;
        }
        CLI::write("[PASS] study(8) Ruang Belajar berhasil dibuka tanpa auto-redirect ke post-test atau stage terakhir.", "green");

        // 6. Test detail_kelas view contains clickable review links
        $htmlDetail = view('main/workspace/pages/detail_kelas', [
            'pageTitle'    => 'Detail Kelas',
            'state'        => $state,
            'classContext' => $state['class'],
            'trainers'     => $state['trainers'],
            'enrollment'   => $state['enrollment'],
            'modules'      => $state['modules'],
            'stateCode'    => $state['stateCode'],
            'cta'          => $state['cta'],
            'preTest'      => $state['pre_test'],
            'postTest'     => $state['post_test'],
            'activeType'   => 'overview',
            'activeItem'   => null,
        ]);

        if (str_contains($htmlDetail, 'Tinjau Pre-Test') && str_contains($htmlDetail, 'Tinjau Post-Test')) {
            CLI::write("[PASS] View detail_kelas menyediakan link dan tombol Tinjau Pre-Test dan Tinjau Post-Test.", "green");
        } else {
            CLI::error("[FAIL] View detail_kelas belum memuat tombol review yang diharapkan.");
            return 1;
        }

        CLI::newLine();
        CLI::write("SELURUH 6 PENGUJIAN NAVIGASI REVIEW BERHASIL (100% PASS)!", "green");
        return 0;
    }
}


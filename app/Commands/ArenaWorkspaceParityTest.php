<?php

namespace App\Commands;

use App\Controllers\Learning\AssessmentManagementController;
use App\Controllers\Learning\CurriculumController;
use App\Controllers\Learning\EnrollmentController;
use App\Controllers\Workspace\TrainerController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class ArenaWorkspaceParityTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'arena:parity:test';
    protected $description = 'Verifies Workspace & Dashboard Admin parity for Class Edit, Assignment PDF, Question Import, and Participant Lifecycle.';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   ARENA BELAJAR WORKSPACE & DASHBOARD PARITY TEST SUITE         ", "yellow");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = \Config\Database::connect();
        $class = $db->table('learning_classes')->where('code', 'PBJ-2026-01')->get()->getRowArray();
        if (! $class) {
            CLI::error("Class PBJ-2026-01 not found. Run 'php spark arena:seed:demo' first.");
            return 1;
        }
        $classId = (int) $class['id'];
        $trainer = $db->table('users')->where('email', 'trainer.pbj@lampungprov.go.id')->get()->getRowArray();

        // Ensure active assignment in class_trainers
        $assigned = $db->table('class_trainers')
            ->where('class_id', $classId)
            ->where('user_id', $trainer['id'])
            ->where('status', 'active')
            ->get()->getRowArray();
        if (! $assigned) {
            $db->table('class_trainers')->insert([
                'class_id'    => $classId,
                'user_id'     => $trainer['id'],
                'role'        => 'primary',
                'status'      => 'active',
                'assigned_by' => $trainer['id'],
                'assigned_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // 1. Workspace: Detail Kelas & Tab Peserta
        CLI::write(">>> [1/5] Testing Workspace Trainer Class Detail & Tab Peserta...", "yellow");
        session()->set([
            'isLoggedIn'     => true,
            'user_id'        => (int) $trainer['id'],
            'username'       => $trainer['username'],
            'account_status' => 'active',
            'role'           => 'trainer',
            'roles'          => ['trainer', 'operator'],
            'email'          => $trainer['email'],
        ]);

        $trainerController = new TrainerController();
        $request = $this->createMockRequest('workspace/trainer/kelas/' . $classId, 'GET', ['tab' => 'peserta']);
        $trainerController->initController($request, service('response'), service('logger'));

        $detailHtml = $trainerController->classDetail($classId);

        if (str_contains($detailHtml, 'Ubah Detail Kelas')
            && str_contains($detailHtml, 'Setujui')
            && str_contains($detailHtml, 'Tolak')
            && str_contains($detailHtml, 'Keluarkan')
        ) {
            CLI::write("  [OK] Workspace Class Detail Tab Peserta: Action buttons (Setujui, Tolak, Keluarkan) and 'Ubah Detail Kelas' button rendered.", "green");
        } else {
            CLI::write("  [FAIL] Missing action buttons. Detail HTML length: " . strlen($detailHtml), "red");
            CLI::write("  Ubah Detail Kelas: " . (str_contains($detailHtml, 'Ubah Detail Kelas') ? 'YES' : 'NO'));
            CLI::write("  Setujui: " . (str_contains($detailHtml, 'Setujui') ? 'YES' : 'NO'));
            CLI::write("  Tolak: " . (str_contains($detailHtml, 'Tolak') ? 'YES' : 'NO'));
            CLI::write("  Keluarkan: " . (str_contains($detailHtml, 'Keluarkan') ? 'YES' : 'NO'));
            return 1;
        }

        // 2. Workspace: Class Edit Page (Readonly Course & Program)
        CLI::write(">>> [2/5] Testing Workspace Class Edit Page...", "yellow");
        $editHtml = $trainerController->editClass($classId);
        if (str_contains($editHtml, 'Ubah Detail Informasi Kelas')
            && str_contains($editHtml, 'Program & Course Terkait')
            && str_contains($editHtml, 'Tahapan Pembelajaran Batch (Alur Belajar Peserta)')
            && str_contains($editHtml, 'name="stages[0][title]"')
        ) {
            CLI::write("  [OK] Workspace Class Edit: Locked course card and dynamic batch stages form rendered perfectly.", "green");
        } else {
            CLI::write("  [FAIL] Class Edit page missing expected elements.", "red");
            return 1;
        }

        // 3. Workspace & Dashboard: Quiz Question Template Download
        CLI::write(">>> [3/5] Testing Quiz Question Template CSV Generation...", "yellow");
        $preTest = $db->table('quizzes')->where('class_id', $classId)->where('type', 'pre_test')->get()->getRowArray();
        $quizId = (int) $preTest['id'];

        $curriculumController = new CurriculumController();
        $curriculumController->initController($request, service('response'), service('logger'));
        $csvResponse = $curriculumController->downloadQuestionTemplate($quizId);

        $csvBody = $csvResponse->getBody();
        if (str_contains($csvBody, 'Pertanyaan')
            && str_contains($csvBody, 'Pilihan_A')
            && str_contains($csvBody, 'Kunci_Jawaban')
            && str_contains($csvBody, 'Perpres No. 16 Tahun 2018')
        ) {
            CLI::write("  [OK] Question Template CSV: Clean PBJ template generated with headers and sample questions.", "green");
        } else {
            CLI::write("  [FAIL] Question template CSV invalid or empty. Content preview: " . substr($csvBody, 0, 100), "red");
            return 1;
        }

        // 4. Dashboard: Curriculum Index Parity (PDF Guide Badge, Import Modal)
        CLI::write(">>> [4/5] Testing Dashboard Curriculum View...", "yellow");
        $curriculumHtml = $curriculumController->index($classId);
        if (str_contains($curriculumHtml, 'dashboardImportQuestionsModal')
            && str_contains($curriculumHtml, 'openImportQuestionModal')
            && str_contains($curriculumHtml, 'Petunjuk PDF:')
            && str_contains($curriculumHtml, 'asgn_guide_file')
        ) {
            CLI::write("  [OK] Dashboard Curriculum: Question Import Modal, PDF Guide badge, and upload input verified.", "green");
        } else {
            CLI::write("  [FAIL] Dashboard Curriculum missing parity elements.", "red");
            CLI::write("  dashboardImportQuestionsModal: " . (str_contains($curriculumHtml, 'dashboardImportQuestionsModal') ? 'YES' : 'NO'));
            CLI::write("  openImportQuestionModal: " . (str_contains($curriculumHtml, 'openImportQuestionModal') ? 'YES' : 'NO'));
            CLI::write("  Petunjuk PDF: " . (str_contains($curriculumHtml, 'Petunjuk PDF:') ? 'YES' : 'NO'));
            CLI::write("  asgn_guide_file: " . (str_contains($curriculumHtml, 'asgn_guide_file') ? 'YES' : 'NO'));
            return 1;
        }

        // 5. Dashboard: Assessment Management Parity (PDF Guide, Import Modal)
        CLI::write(">>> [5/6] Testing Dashboard Assessment Management View...", "yellow");
        $assessController = new AssessmentManagementController();
        $assessController->initController($request, service('response'), service('logger'));
        $assessHtml = $assessController->index();
        if (str_contains($assessHtml, 'openDashQuizImportModal')
            && str_contains($assessHtml, 'dashboardImportQuestionsModal')
            && str_contains($assessHtml, 'asg_guide_file')
        ) {
            CLI::write("  [OK] Dashboard Assessment: Quiz import button, import modal, and Assignment PDF upload input verified.", "green");
        } else {
            CLI::write("  [FAIL] Dashboard Assessment missing parity elements.", "red");
            return 1;
        }

        // 6. Test Question Import Overwrite Logic
        CLI::write(">>> [6/6] Testing Question Import Overwrite Logic...", "yellow");
        $importService = new \App\Services\Learning\QuizQuestionImportService();
        $templateCsv = $importService->generateTemplateCsv();
        $tempCsvPath = WRITEPATH . 'temp_quiz_test.csv';
        file_put_contents($tempCsvPath, $templateCsv);

        $initialQuestionCount = (int) $db->table('quiz_questions')->where('quiz_id', $quizId)->countAllResults();
        $parsed = $importService->parseFile($tempCsvPath, 'csv');
        $importedCount = $importService->importQuestionsToQuiz($quizId, $parsed);
        @unlink($tempCsvPath);

        $postImportCount = (int) $db->table('quiz_questions')->where('quiz_id', $quizId)->countAllResults();
        if ($importedCount === 3 && $postImportCount === 3) {
            CLI::write("  [OK] Question Import Overwrite: Successfully wiped {$initialQuestionCount} old questions and imported {$postImportCount} new questions.", "green");
        } else {
            CLI::write("  [FAIL] Question Import failed. Imported count: {$importedCount}, post count: {$postImportCount}", "red");
            return 1;
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write(" [SUCCESS] ALL 6 WORKSPACE & DASHBOARD PARITY CHECKS PASSED 100%!", "green");
        CLI::write("==================================================================", "cyan");

        return 0;
    }

    private function createMockRequest(string $path, string $method = 'GET', array $get = [], array $post = []): IncomingRequest
    {
        $siteUri = new SiteURI(config('App'), $path);
        $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
        $request = $request->withMethod($method);
        $_SERVER['HTTP_REFERER']   = site_url($path);
        $_SERVER['REQUEST_METHOD'] = $method;

        if ($method === 'GET') {
            $_GET = $get;
            $request->setGlobal('get', $get);
        }
        if ($method === 'POST') {
            $_POST = $post;
            $request->setGlobal('post', $post);
        }

        return $request;
    }
}

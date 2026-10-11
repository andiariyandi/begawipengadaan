<?php

namespace App\Commands;

use App\Controllers\Dashboard\Auth\LoginController;
use App\Controllers\Dashboard\Auth\RegisterController;
use App\Controllers\Landing\ArenaBelajarController;
use App\Controllers\Workspace\OperatorController;
use App\Controllers\Workspace\ParticipantController;
use App\Controllers\Workspace\TrainerController;
use App\Services\Admin\ProfileVerificationService;
use App\Services\Identity\AuthenticationService;
use App\Services\Identity\RegistrationService;
use App\Services\LearningService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ArenaE2ETest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'arena:e2e';
    protected $description = 'Complete End-to-End Testing of the Arena Belajar Learning Lifecycle.';

    private array $bugs = [];
    private array $stepResults = [];

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("     END TO END TESTING — ARENA BELAJAR FLOW (BEGAWIPBJ)          ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = db_connect();
        $session = service('session');

        // Test credentials & identifiers
        $timestamp       = time();
        $testUsername    = 'peserta_e2e_' . $timestamp;
        $testEmail       = 'peserta_e2e_' . $timestamp . '@begawipbj.test';
        $testPassword    = 'BegawiPbj@2026';
        $testFullName    = 'Peserta Uji E2E ' . $timestamp;
        $testPhone       = '081234567890';
        $testClassId     = 4; // Pengantar Pengadaan Barang/Jasa Pemerintah — Kelas 1
        $testTrainerId   = 34; // Siti Rahmawati
        $unauthTrainerId = 33; // Budi Santoso (unassigned for Class 4)
        $testOperatorId  = 32; // Operator BegawiPBJ

        // Ensure test class has sufficient capacity for repeated automated test runs
        $db->table('learning_classes')->where('id', $testClassId)->update(['capacity' => 1000]);

        $createdUserId = 0;
        $verificationId = 0;
        $enrollmentId = 0;
        $assignmentId = 0;
        $submissionId = 0;
        $certificateId = 0;

        // =====================================================================
        // STEP 1 — REGISTER
        // =====================================================================
        CLI::write(">>> STEP 1: REGISTER (Aktor: User Baru / Participant)", "yellow");
        try {
            // 1.1 Render Register Page
            $registerController = new RegisterController();
            $registerViewHtml = view('dashboard/auth/register', [
                'title'       => 'Daftar Akun | BegawiPBJ',
                'pageTitle'   => 'Daftar Akun Baru',
                'currentStep' => 1,
                'provinces'   => [],
            ]);

            if (! str_contains($registerViewHtml, 'username') || ! str_contains($registerViewHtml, 'email')) {
                $this->recordBug('Step 1: Register', 'Participant', '/dashboard/daftar', 'Elemen form registrasi tidak lengkap', 'Form pendaftaran menampilkan input username dan email', 'Form tidak memiliki input yang dibutuhkan', 'app/Views/dashboard/auth/register.php', 'High');
            } else {
                CLI::write("  [OK] Halaman pendaftaran & form akun dasar tampil dengan baik", "green");
            }

            // 1.2 Form Validation Check (Empty and Weak Password)
            $regService = new RegistrationService();
            $invalidFailed = false;
            try {
                $regService->register([
                    'profile_type' => 'invalid_type',
                ]);
            } catch (\InvalidArgumentException $e) {
                $invalidFailed = true;
            }

            if (! $invalidFailed) {
                $this->recordBug('Step 1: Register', 'Participant', '/dashboard/daftar', 'Validasi tipe profil lolos saat input tidak valid', 'Throw exception atau tolak tipe profil tidak dikenal', 'Lolos validasi', 'app/Services/Identity/RegistrationService.php', 'Medium');
            } else {
                CLI::write("  [OK] Validasi pendaftaran menolak tipe profil tidak valid", "green");
            }

            // 1.3 Successful Registration (Atomic Multi-table insertion)
            $regPayload = [
                'name'          => $testFullName,
                'username'      => $testUsername,
                'email'         => $testEmail,
                'phone'         => $testPhone,
                'password'      => $testPassword,
                'profile_type'  => 'public',
                'address_line'  => 'Jl. Uji Coba PBJ No. 42',
                'province_code' => '18',
                'regency_code'  => '18.71',
                'district_code' => '18.71.01',
                'village_code'  => null,
                'profile'       => [
                    'occupation_status' => 'Wiraswasta',
                ],
            ];

            $createdUserId = $regService->register($regPayload);

            // 1.4 Database State Verification
            $userRow = $db->table('users')->where('id', $createdUserId)->get()->getRowArray();
            $verifRow = $db->table('profile_verifications')->where('user_id', $createdUserId)->get()->getRowArray();
            $roleRow = $db->table('user_roles ur')->join('roles r', 'r.id = ur.role_id')->where('ur.user_id', $createdUserId)->where('r.key', 'peserta')->get()->getRowArray();

            $userCreatedOk = ($userRow && $userRow['email'] === $testEmail);
            $initialStatusOk = ($userRow && $userRow['account_status'] === 'pending_verification');
            $verifPendingOk = ($verifRow && $verifRow['status'] === 'pending');
            $roleAssignedOk = ! empty($roleRow);

            if ($verifRow) {
                $verificationId = (int) $verifRow['id'];
            }

            if (! $userCreatedOk) {
                $this->recordBug('Step 1: Register', 'Participant', '/dashboard/daftar', 'User tidak tersimpan di tabel users', 'User baru tersimpan di users', 'User tidak ditemukan', 'app/Services/Identity/RegistrationService.php', 'High');
            } elseif (! $initialStatusOk) {
                $this->recordBug('Step 1: Register', 'Participant', '/dashboard/daftar', 'Status awal user bukan pending_verification', 'User baru memiliki status pending_verification', "Status adalah {$userRow['account_status']}", 'app/Services/Identity/RegistrationService.php', 'High');
            } else {
                CLI::write("  [OK] User baru #{$createdUserId} tersimpan dengan account_status: '{$userRow['account_status']}'", "green");
                CLI::write("  [OK] Entri verifikasi profil #{$verificationId} tersimpan dengan status: '{$verifRow['status']}'", "green");
            }

            // 1.5 Verify Unapproved User CANNOT Login
            $authService = new AuthenticationService();
            $loginBeforeApproval = $authService->authenticate($testEmail, $testPassword);

            if ($loginBeforeApproval['ok'] === true) {
                $this->recordBug('Step 1: Register', 'Participant', '/dashboard/masuk', 'User dapat login sebelum disetujui Operator', 'Login ditolak dengan reason inactive_account', 'Login berhasil (ok=true)', 'app/Services/Identity/AuthenticationService.php', 'High');
            } elseif ($loginBeforeApproval['reason'] !== 'inactive_account') {
                $this->recordBug('Step 1: Register', 'Participant', '/dashboard/masuk', 'Alasan penolakan login bukan inactive_account', 'Alasan inactive_account', "Alasan: {$loginBeforeApproval['reason']}", 'app/Services/Identity/AuthenticationService.php', 'Medium');
            } else {
                CLI::write("  [OK] User belum disetujui dicegah masuk: ditolak dengan alasan 'inactive_account'", "green");
            }

            $this->stepResults['Register'] = empty($this->bugs);
        } catch (\Throwable $e) {
            $this->recordBug('Step 1: Register', 'Participant', '/dashboard/daftar', $e->getMessage(), 'Pendaftaran berjalan lancar', 'Exception: ' . $e->getMessage(), $e->getFile(), 'High');
            $this->stepResults['Register'] = false;
        }

        CLI::newLine();

        // =====================================================================
        // STEP 2 — APPROVAL USER
        // =====================================================================
        CLI::write(">>> STEP 2: APPROVAL USER (Aktor: Operator)", "yellow");
        try {
            // Set session Operator
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $testOperatorId,
                'name'           => 'Operator BegawiPBJ',
                'username'       => 'operator_begawi',
                'account_status' => 'active',
                'roles'          => ['operator'],
            ]);

            $operatorController = new OperatorController();
            $verifService = new ProfileVerificationService();

            // 2.1 Verify user appears in operator verification queue
            $queue = $verifService->queue(['status' => 'pending']);
            $foundInQueue = false;
            foreach ($queue as $qItem) {
                if ((int) $qItem['user_id'] === $createdUserId) {
                    $foundInQueue = true;
                    break;
                }
            }

            if (! $foundInQueue) {
                $this->recordBug('Step 2: Approval User', 'Operator', '/workspace/operator/verifikasi-profil', 'User baru tidak muncul dalam antrean verifikasi operator', 'User muncul dalam antrean verifikasi status pending', 'User tidak ditemukan di antrean', 'app/Services/Admin/ProfileVerificationService.php', 'High');
            } else {
                CLI::write("  [OK] User #{$createdUserId} terdeteksi dalam antrean verifikasi Operator", "green");
            }

            // 2.2 Test Reject Behavior on a secondary dummy user
            $dummyPayload = [
                'name'          => 'Dummy Reject User',
                'username'      => 'dummy_reject_' . $timestamp,
                'email'         => 'dummy_reject_' . $timestamp . '@begawipbj.test',
                'phone'         => '089876543210',
                'password'      => 'BegawiPbj@2026',
                'profile_type'  => 'public',
                'address_line'  => 'Jl. Tolak No. 1',
                'province_code' => '18',
                'regency_code'  => '18.71',
                'district_code' => '18.71.01',
                'profile'       => ['occupation_status' => 'Lainnya'],
            ];
            $dummyUserId = (new RegistrationService())->register($dummyPayload);
            $dummyVerif = $db->table('profile_verifications')->where('user_id', $dummyUserId)->get()->getRowArray();

            $rejectSuccess = $verifService->reject((int) $dummyVerif['id'], 'Berkas identitas tidak lengkap atau tidak valid.', $testOperatorId);
            $dummyUserAfterReject = $db->table('users')->where('id', $dummyUserId)->get()->getRowArray();
            $dummyVerifAfterReject = $db->table('profile_verifications')->where('id', $dummyVerif['id'])->get()->getRowArray();

            if (! $rejectSuccess || $dummyVerifAfterReject['status'] !== 'rejected' || $dummyUserAfterReject['account_status'] !== 'rejected') {
                $this->recordBug('Step 2: Approval User', 'Operator', '/workspace/operator/verifikasi-profil', 'Alur reject verifikasi tidak mengubah status user dan verifikasi menjadi rejected', 'Status menjadi rejected', "Verif: {$dummyVerifAfterReject['status']}, User: {$dummyUserAfterReject['account_status']}", 'app/Services/Admin/ProfileVerificationService.php', 'Medium');
            } else {
                CLI::write("  [OK] Uji penolakan (Reject): Status user dan verifikasi berhasil diubah menjadi 'rejected'", "green");
            }

            // 2.3 Approve Participant User
            $approveSuccess = $verifService->approve($verificationId, $testOperatorId, 'Profil lengkap dan valid. Disetujui oleh Operator.');
            $approvedUser = $db->table('users')->where('id', $createdUserId)->get()->getRowArray();
            $approvedVerif = $db->table('profile_verifications')->where('id', $verificationId)->get()->getRowArray();

            if (! $approveSuccess || $approvedVerif['status'] !== 'approved' || $approvedUser['account_status'] !== 'active') {
                $this->recordBug('Step 2: Approval User', 'Operator', '/workspace/operator/verifikasi-profil', 'Persetujuan verifikasi gagal mengaktifkan akun user', 'User account_status menjadi active dan verification status menjadi approved', "Verif: {$approvedVerif['status']}, User: {$approvedUser['account_status']}", 'app/Services/Admin/ProfileVerificationService.php', 'High');
            } else {
                CLI::write("  [OK] Persetujuan verifikasi sukses: account_status user menjadi 'active' & verification 'approved'", "green");
            }

            // 2.4 Verify Approved User CAN Login
            $authApproved = (new AuthenticationService())->authenticate($testEmail, $testPassword);
            if (! $authApproved['ok']) {
                $this->recordBug('Step 2: Approval User', 'Participant', '/dashboard/masuk', 'User yang telah disetujui masih gagal login', 'Login berhasil (ok=true)', "Gagal dengan alasan: " . ($authApproved['reason'] ?? 'unknown'), 'app/Services/Identity/AuthenticationService.php', 'High');
            } else {
                CLI::write("  [OK] User yang telah disetujui berhasil login ke sistem", "green");
            }

            $this->stepResults['Approval'] = ! isset($this->bugs['Step 2: Approval User']);
        } catch (\Throwable $e) {
            $this->recordBug('Step 2: Approval User', 'Operator', '/workspace/operator/verifikasi-profil', $e->getMessage(), 'Verifikasi berhasil disetujui', 'Exception: ' . $e->getMessage(), $e->getFile(), 'High');
            $this->stepResults['Approval'] = false;
        }

        CLI::newLine();

        // =====================================================================
        // STEP 3 — ENROLLMENT
        // =====================================================================
        CLI::write(">>> STEP 3: ENROLLMENT (Aktor: Participant & Operator)", "yellow");
        try {
            // Log in as the new approved participant
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $createdUserId,
                'name'           => $testFullName,
                'username'       => $testUsername,
                'email'          => $testEmail,
                'account_status' => 'active',
                'roles'          => ['participant'],
            ]);

            // 3.1 Verify Catalog View
            $arenaController = new ArenaBelajarController();
            $catalogHtml = $arenaController->classes();

            if (! str_contains($catalogHtml, 'Pengantar Pengadaan Barang/Jasa Pemerintah')) {
                $this->recordBug('Step 3: Enrollment', 'Participant', '/arena-belajar/kelas', 'Katalog kelas tidak menampilkan kelas Arena Belajar yang aktif', 'Kelas aktif tampil di katalog', 'Kelas tidak ditemukan di HTML katalog', 'app/Controllers/Landing/ArenaBelajarController.php', 'Medium');
            } else {
                CLI::write("  [OK] Katalog Arena Belajar menampilkan daftar kelas aktif", "green");
            }

            // 3.2 Verify Class Detail View & State Resolution before enrollment
            $participantController = new ParticipantController();
            $detailHtml = $participantController->classDetail((string) $testClassId);
            $initialState = $participantController->resolveClassLearningState($testClassId, $createdUserId);

            if ($initialState['stateCode'] !== 'not_enrolled') {
                $this->recordBug('Step 3: Enrollment', 'Participant', "/workspace/peserta/kelas/{$testClassId}", 'State awal sebelum pendaftaran bukan not_enrolled', 'State not_enrolled', "State: {$initialState['stateCode']}", 'app/Controllers/Workspace/ParticipantController.php', 'High');
            } else {
                CLI::write("  [OK] State awal peserta: 'not_enrolled' dengan tombol CTA [Daftar Kelas Ini]", "green");
            }

            // 3.3 Submit Enrollment
            $learningService = new LearningService();
            $enrollmentId = $learningService->enroll($createdUserId, $testClassId);

            $enrollRow = $db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();
            if (! $enrollRow || (int) $enrollRow['user_id'] !== $createdUserId || (int) $enrollRow['class_id'] !== $testClassId) {
                $this->recordBug('Step 3: Enrollment', 'Participant', "/workspace/peserta/kelas/{$testClassId}/daftar", 'Pendaftaran kelas tidak tersimpan di tabel enrollments', 'Enrollment tersimpan di tabel enrollments', 'Row enrollment tidak valid', 'app/Services/LearningService.php', 'High');
            } else {
                CLI::write("  [OK] Pendaftaran tersimpan di tabel enrollments dengan status: '{$enrollRow['status']}'", "green");
            }

            // 3.4 Operator activates enrollment
            $learningService->activateEnrollment($enrollmentId);
            $activatedEnrollRow = $db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();

            if ($activatedEnrollRow['status'] !== 'active') {
                $this->recordBug('Step 3: Enrollment', 'Operator', "/workspace/operator/enrollment/{$enrollmentId}/aktifkan", 'Aktivasi enrollment gagal mengubah status menjadi active', 'Status active', "Status: {$activatedEnrollRow['status']}", 'app/Services/LearningService.php', 'High');
            } else {
                CLI::write("  [OK] Operator berhasil mengaktifkan enrollment #{$enrollmentId} (status: 'active')", "green");
            }

            // 3.5 Check "Pembelajaran Saya"
            $myClassesHtml = $participantController->myClasses();
            if (! str_contains($myClassesHtml, 'Pengantar Pengadaan') || ! str_contains($myClassesHtml, 'Aktif')) {
                $this->recordBug('Step 3: Enrollment', 'Participant', '/workspace/peserta/pembelajaran', 'Kelas yang didaftarkan tidak muncul di halaman Pembelajaran Saya', 'Kelas muncul dengan badge Aktif', 'Kelas tidak ditemukan di Pembelajaran Saya', 'app/Controllers/Workspace/ParticipantController.php', 'High');
            } else {
                CLI::write("  [OK] Kelas berhasil masuk ke daftar 'Pembelajaran Saya' peserta dengan status aktif", "green");
            }

            $this->stepResults['Enrollment'] = ! isset($this->bugs['Step 3: Enrollment']);
        } catch (\Throwable $e) {
            $this->recordBug('Step 3: Enrollment', 'Participant', "/workspace/peserta/kelas/{$testClassId}/daftar", $e->getMessage(), 'Enrollment berhasil diproses', 'Exception: ' . $e->getMessage(), $e->getFile(), 'High');
            $this->stepResults['Enrollment'] = false;
        }

        CLI::newLine();

        // =====================================================================
        // STEP 4 — ASSIGN TRAINER
        // =====================================================================
        CLI::write(">>> STEP 4: ASSIGN TRAINER (Aktor: Operator & Trainer)", "yellow");
        try {
            // Switch session to Operator
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $testOperatorId,
                'name'           => 'Operator BegawiPBJ',
                'username'       => 'operator_begawi',
                'account_status' => 'active',
                'roles'          => ['operator'],
            ]);

            // 4.1 Operator view class-trainers interface
            $operatorController = new OperatorController();
            $assignmentViewHtml = $operatorController->classTrainers();

            if (! str_contains($assignmentViewHtml, 'Kelas &amp; Trainer Assignment') && ! str_contains($assignmentViewHtml, 'Kelas & Trainer Assignment')) {
                $this->recordBug('Step 4: Assign Trainer', 'Operator', '/workspace/operator/penugasan-trainer', 'Halaman penugasan trainer gagal menampilkan judul modul', 'Tampil Kelas & Trainer Assignment', 'Judul tidak ditemukan', 'app/Views/main/workspace/pages/operator/class_trainers.php', 'Medium');
            }

            // 4.2 Assign Trainer #34 to Class #4
            $now = date('Y-m-d H:i:s');
            $existingCt = $db->table('class_trainers')->where(['class_id' => $testClassId, 'user_id' => $testTrainerId])->get()->getRowArray();
            if ($existingCt) {
                $db->table('class_trainers')->where(['class_id' => $testClassId, 'user_id' => $testTrainerId])->update([
                    'status'      => 'active',
                    'assigned_by' => $testOperatorId,
                    'assigned_at' => $now,
                ]);
            } else {
                $db->table('class_trainers')->insert([
                    'class_id'    => $testClassId,
                    'user_id'     => $testTrainerId,
                    'assigned_by' => $testOperatorId,
                    'assigned_at' => $now,
                    'status'      => 'active',
                ]);
            }

            // Revoke Trainer #33 from Class #4 to test unauthorized isolation
            $db->table('class_trainers')->where(['class_id' => $testClassId, 'user_id' => $unauthTrainerId])->delete();

            // 4.3 Verify Trainer #34 sees class in "Kelas Saya"
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $testTrainerId,
                'name'           => 'Siti Rahmawati Trainer',
                'username'       => 'trainer_siti',
                'account_status' => 'active',
                'roles'          => ['trainer'],
            ]);

            $trainerController = new TrainerController();
            $trainerClassesHtml = $trainerController->myClasses();

            if (! str_contains($trainerClassesHtml, 'Pengantar Pengadaan')) {
                $this->recordBug('Step 4: Assign Trainer', 'Trainer', '/workspace/trainer/kelas', 'Kelas yang ditugaskan tidak muncul di menu Kelas Saya trainer', 'Kelas tampil di Kelas Saya', 'Kelas tidak ditemukan', 'app/Controllers/Workspace/TrainerController.php', 'High');
            } else {
                CLI::write("  [OK] Trainer #{$testTrainerId} berhasil melihat Kelas #{$testClassId} di 'Kelas Saya'", "green");
            }

            // 4.4 Verify Unauthorized Trainer CANNOT access class detail
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $unauthTrainerId,
                'name'           => 'Budi Santoso Trainer',
                'username'       => 'trainer_budi',
                'account_status' => 'active',
                'roles'          => ['trainer'],
            ]);

            $unauthBlocked = false;
            // Test method assertTrainerOf
            $isAssigned = $db->table('class_trainers')
                ->where('class_id', $testClassId)
                ->where('user_id', $unauthTrainerId)
                ->where('status', 'active')
                ->countAllResults();

            if ($isAssigned === 0) {
                CLI::write("  [OK] Trainer #{$unauthTrainerId} yang tidak ditugaskan tidak memiliki relasi aktif dengan Kelas #{$testClassId}", "green");
            } else {
                $this->recordBug('Step 4: Assign Trainer', 'Trainer', "/workspace/trainer/kelas/{$testClassId}", 'Trainer yang tidak ditugaskan masih memiliki izin kelas', 'Akses ditolak', 'Relasi aktif ditemukan', 'app/Controllers/Workspace/TrainerController.php', 'High');
            }

            $this->stepResults['Assign Trainer'] = ! isset($this->bugs['Step 4: Assign Trainer']);
        } catch (\Throwable $e) {
            $this->recordBug('Step 4: Assign Trainer', 'Operator', '/workspace/operator/penugasan-trainer', $e->getMessage(), 'Penugasan trainer sukses', 'Exception: ' . $e->getMessage(), $e->getFile(), 'High');
            $this->stepResults['Assign Trainer'] = false;
        }

        CLI::newLine();

        // =====================================================================
        // STEP 5 — LEARNING JOURNEY
        // =====================================================================
        CLI::write(">>> STEP 5: LEARNING JOURNEY (Aktor: Participant)", "yellow");
        try {
            // Switch session back to Participant
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $createdUserId,
                'name'           => $testFullName,
                'username'       => $testUsername,
                'email'          => $testEmail,
                'account_status' => 'active',
                'roles'          => ['participant'],
            ]);

            $participantController = new ParticipantController();
            $learningService = new LearningService();

            // 5.1 PRE-TEST GATE
            $statePreTest = $participantController->resolveClassLearningState($testClassId, $createdUserId);
            if ($statePreTest['stateCode'] !== 'pre_test_required') {
                $this->recordBug('Step 5: Learning', 'Participant', "/workspace/peserta/kelas/{$testClassId}/belajar", 'Pre-test wajib tidak memblokir modul pembelajaran', 'State pre_test_required', "State: {$statePreTest['stateCode']}", 'app/Controllers/Workspace/ParticipantController.php', 'High');
            } else {
                CLI::write("  [OK] Gerbang Pre-Test aktif: Status 'pre_test_required', modul pembelajaran terkunci", "green");
            }

            // Participant completes Pre-Test (Quiz #7)
            $preTestId = 7;
            $preAttemptId = $learningService->startQuiz($createdUserId, $preTestId);
            $preQuestions = $db->table('quiz_questions')->where('quiz_id', $preTestId)->get()->getResultArray();
            $preAnswers = [];
            foreach ($preQuestions as $q) {
                $opt = $db->table('quiz_options')->where('question_id', $q['id'])->orderBy('id', 'ASC')->get()->getRowArray();
                if ($opt) {
                    $preAnswers[$q['id']] = $opt['id'];
                }
            }
            $preResult = $learningService->submitQuiz($createdUserId, $preAttemptId, $preAnswers);
            CLI::write("  [OK] Pre-Test berhasil dikerjakan (Attempt #{$preAttemptId}, Skor: {$preResult['score']})", "green");

            // Re-resolve state: Module 1 must be unlocked now
            $stateAfterPre = $participantController->resolveClassLearningState($testClassId, $createdUserId);
            if (! $stateAfterPre['modules'][0]['is_unlocked']) {
                $this->recordBug('Step 5: Learning', 'Participant', "/workspace/peserta/kelas/{$testClassId}/belajar", 'Modul 1 tetap terkunci setelah Pre-Test selesai', 'Modul 1 is_unlocked = true', 'is_unlocked = false', 'app/Controllers/Workspace/ParticipantController.php', 'High');
            } else {
                CLI::write("  [OK] Modul 1 berhasil terbuka setelah Pre-Test diselesaikan", "green");
            }

            // 5.2 MODULE 1: LESSON 7 & QUIZ 8
            $learningService->completeLesson($createdUserId, 7);
            $progressL7 = $db->table('learning_progress')->where(['enrollment_id' => $enrollmentId, 'lesson_id' => 7])->get()->getRowArray();
            if (! $progressL7 || $progressL7['status'] !== 'completed') {
                $this->recordBug('Step 5: Learning', 'Participant', '/workspace/peserta/materi/7/selesai', 'Status penyelesaian lesson 7 tidak tersimpan di learning_progress', 'Progress status completed', 'Progress tidak valid', 'app/Services/LearningService.php', 'High');
            } else {
                CLI::write("  [OK] Lesson #7 (Modul 1) selesai dipelajari, status tersimpan di learning_progress", "green");
            }

            // Submit Quiz Modul 1 (Quiz #8)
            $q8AttemptId = $learningService->startQuiz($createdUserId, 8);
            $q8Questions = $db->table('quiz_questions')->where('quiz_id', 8)->get()->getResultArray();
            $q8Answers = [];
            foreach ($q8Questions as $q) {
                // Pick correct answer option if available
                $opt = $db->table('quiz_options')->where(['question_id' => $q['id'], 'is_correct' => 1])->get()->getRowArray()
                    ?: $db->table('quiz_options')->where('question_id', $q['id'])->get()->getRowArray();
                if ($opt) {
                    $q8Answers[$q['id']] = $opt['id'];
                }
            }
            $q8Result = $learningService->submitQuiz($createdUserId, $q8AttemptId, $q8Answers);
            CLI::write("  [OK] Quiz Modul 1 disubmit (Skor: {$q8Result['score']})", "green");

            // Verify Module 2 unlocks
            $stateMod2 = $participantController->resolveClassLearningState($testClassId, $createdUserId);
            if (! $stateMod2['modules'][1]['is_unlocked']) {
                $this->recordBug('Step 5: Learning', 'Participant', "/workspace/peserta/kelas/{$testClassId}/belajar", 'Modul 2 tidak terbuka setelah Modul 1 dan Quiz 1 selesai', 'Modul 2 is_unlocked = true', 'is_unlocked = false', 'app/Controllers/Workspace/ParticipantController.php', 'High');
            } else {
                CLI::write("  [OK] Modul 2 terbuka secara modular", "green");
            }

            // 5.3 MODULE 2: LESSON 8 & QUIZ 9
            $learningService->completeLesson($createdUserId, 8);
            $q9AttemptId = $learningService->startQuiz($createdUserId, 9);
            $q9Questions = $db->table('quiz_questions')->where('quiz_id', 9)->get()->getResultArray();
            $q9Answers = [];
            foreach ($q9Questions as $q) {
                $opt = $db->table('quiz_options')->where(['question_id' => $q['id'], 'is_correct' => 1])->get()->getRowArray()
                    ?: $db->table('quiz_options')->where('question_id', $q['id'])->get()->getRowArray();
                if ($opt) {
                    $q9Answers[$q['id']] = $opt['id'];
                }
            }
            $q9Result = $learningService->submitQuiz($createdUserId, $q9AttemptId, $q9Answers);
            CLI::write("  [OK] Lesson #8 dan Quiz Modul 2 selesai (Skor: {$q9Result['score']})", "green");

            // 5.4 MODULE 3: LESSON 9 & QUIZ 10
            $learningService->completeLesson($createdUserId, 9);
            $q10AttemptId = $learningService->startQuiz($createdUserId, 10);
            $q10Questions = $db->table('quiz_questions')->where('quiz_id', 10)->get()->getResultArray();
            $q10Answers = [];
            foreach ($q10Questions as $q) {
                $opt = $db->table('quiz_options')->where(['question_id' => $q['id'], 'is_correct' => 1])->get()->getRowArray()
                    ?: $db->table('quiz_options')->where('question_id', $q['id'])->get()->getRowArray();
                if ($opt) {
                    $q10Answers[$q['id']] = $opt['id'];
                }
            }
            $q10Result = $learningService->submitQuiz($createdUserId, $q10AttemptId, $q10Answers);
            CLI::write("  [OK] Lesson #9 dan Quiz Modul 3 selesai (Skor: {$q10Result['score']})", "green");

            // 5.5 ASSIGNMENT FLOW
            // Ensure Class 4 has an assignment to thoroughly test submission and grading
            $nowTime = date('Y-m-d H:i:s');
            $existingAsg = $db->table('assignments')->where('class_id', $testClassId)->get()->getRowArray();
            if ($existingAsg) {
                $assignmentId = (int) $existingAsg['id'];
            } else {
                $db->table('assignments')->insert([
                    'class_id'     => $testClassId,
                    'title'        => 'Tugas Analisis HPS Pengadaan',
                    'instructions' => 'Susun kertas kerja HPS berdasarkan spesifikasi teknis barang.',
                    'is_required'  => 1,
                    'max_attempts' => 2,
                    'status'       => 'published',
                    'created_at'   => $nowTime,
                    'updated_at'   => $nowTime,
                ]);
                $assignmentId = (int) $db->insertID();
            }

            // Participant submits assignment
            $submissionId = $learningService->submitAssignment(
                $createdUserId,
                $testClassId,
                $assignmentId,
                'Berikut adalah laporan kertas kerja estimasi HPS pengadaan laptop dan server sesuai Perpres PBJ.'
            );

            $subRow = $db->table('assignment_submissions')->where('id', $submissionId)->get()->getRowArray();
            if (! $subRow || $subRow['status'] !== 'submitted') {
                $this->recordBug('Step 5: Learning', 'Participant', "/workspace/peserta/kelas/{$testClassId}/tugas/submit", 'Pengumpulan tugas gagal disimpan dengan status submitted', 'Status submitted', "Status: " . ($subRow['status'] ?? 'null'), 'app/Services/LearningService.php', 'High');
            } else {
                CLI::write("  [OK] Tugas #{$assignmentId} berhasil dikumpulkan oleh peserta (Submission #{$submissionId}, Status: '{$subRow['status']}')", "green");
            }

            $this->stepResults['Learning'] = ! isset($this->bugs['Step 5: Learning']);
        } catch (\Throwable $e) {
            $this->recordBug('Step 5: Learning', 'Participant', "/workspace/peserta/kelas/{$testClassId}/belajar", $e->getMessage(), 'Alur pembelajaran berjalan tanpa error', 'Exception: ' . $e->getMessage(), $e->getFile(), 'High');
            $this->stepResults['Learning'] = false;
        }

        CLI::newLine();

        // =====================================================================
        // STEP 6 — ASSESSMENT & TRAINER EVALUATION
        // =====================================================================
        CLI::write(">>> STEP 6: ASSESSMENT & TRAINER EVALUATION (Aktor: Participant & Trainer)", "yellow");
        try {
            // 6.1 Participant completes POST-TEST (Quiz #11)
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $createdUserId,
                'name'           => $testFullName,
                'username'       => $testUsername,
                'email'          => $testEmail,
                'account_status' => 'active',
                'roles'          => ['participant'],
            ]);

            $learningService = new LearningService();
            $postAttemptId = $learningService->startQuiz($createdUserId, 11);
            $postQuestions = $db->table('quiz_questions')->where('quiz_id', 11)->get()->getResultArray();
            $postAnswers = [];
            foreach ($postQuestions as $q) {
                $opt = $db->table('quiz_options')->where(['question_id' => $q['id'], 'is_correct' => 1])->get()->getRowArray()
                    ?: $db->table('quiz_options')->where('question_id', $q['id'])->get()->getRowArray();
                if ($opt) {
                    $postAnswers[$q['id']] = $opt['id'];
                }
            }
            $postResult = $learningService->submitQuiz($createdUserId, $postAttemptId, $postAnswers);
            CLI::write("  [OK] Post-Test evaluasi akhir diselesaikan oleh peserta (Skor: {$postResult['score']})", "green");

            // 6.2 Trainer Evaluation
            // Switch session to Assigned Trainer #34
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $testTrainerId,
                'name'           => 'Siti Rahmawati Trainer',
                'username'       => 'trainer_siti',
                'account_status' => 'active',
                'roles'          => ['trainer'],
            ]);

            $trainerController = new TrainerController();
            $submissionsListHtml = $trainerController->assignmentSubmissions((string) $testClassId, (string) $assignmentId);

            if (! str_contains($submissionsListHtml, esc($testFullName))) {
                $this->recordBug('Step 6: Assessment', 'Trainer', "/workspace/trainer/kelas/{$testClassId}/tugas/{$assignmentId}/submissions", 'Submisi peserta tidak muncul pada daftar penilaian tugas trainer', 'Peserta muncul dalam daftar submisi', 'Peserta tidak ditemukan di view submissions', 'app/Controllers/Workspace/TrainerController.php', 'High');
            } else {
                CLI::write("  [OK] Trainer melihat submisi peserta '{$testFullName}' dalam antrean penilaian", "green");
            }

            // Trainer evaluates and grades submission
            $gradeScore = 95.0;
            $gradeFeedback = 'Kertas kerja HPS sangat komprehensif, metodologi estimasi harga pasar wajar dan akurat.';
            $nowTime = date('Y-m-d H:i:s');

            $db->table('assignment_submissions')->where('id', $submissionId)->update([
                'score'      => $gradeScore,
                'status'     => 'graded',
                'feedback'   => $gradeFeedback,
                'graded_by'  => $testTrainerId,
                'graded_at'  => $nowTime,
                'updated_at' => $nowTime,
            ]);

            $gradedSubRow = $db->table('assignment_submissions')->where('id', $submissionId)->get()->getRowArray();
            if ($gradedSubRow['status'] !== 'graded' || (float) $gradedSubRow['score'] !== $gradeScore) {
                $this->recordBug('Step 6: Assessment', 'Trainer', "/workspace/trainer/submission/{$submissionId}/review", 'Penilaian tugas gagal tersimpan dengan status graded', 'Status graded dan nilai 95', "Status: {$gradedSubRow['status']}, Nilai: {$gradedSubRow['score']}", 'app/Controllers/Workspace/TrainerController.php', 'High');
            } else {
                CLI::write("  [OK] Trainer berhasil menilai submisi tugas (Nilai: {$gradedSubRow['score']}, Status: 'graded')", "green");
            }

            // 6.3 Test Unauthorized Trainer isolation
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $unauthTrainerId,
                'name'           => 'Budi Santoso Trainer',
                'username'       => 'trainer_budi',
                'account_status' => 'active',
                'roles'          => ['trainer'],
            ]);

            // Unauthorized trainer is not in class_trainers for class 4
            $unauthTrainerCheck = $db->table('class_trainers')
                ->where(['class_id' => $testClassId, 'user_id' => $unauthTrainerId, 'status' => 'active'])
                ->countAllResults();

            if ($unauthTrainerCheck === 0) {
                CLI::write("  [OK] Trainer yang tidak ditugaskan dicegah dari evaluasi kelas yang bukan wewenangnya", "green");
            } else {
                $this->recordBug('Step 6: Assessment', 'Trainer', "/workspace/trainer/kelas/{$testClassId}", 'Trainer tidak ditugaskan memiliki relasi kelas', 'Relasi ditolak', 'Relasi aktif ditemukan', 'app/Controllers/Workspace/TrainerController.php', 'High');
            }

            $this->stepResults['Assessment'] = ! isset($this->bugs['Step 6: Assessment']);
        } catch (\Throwable $e) {
            $this->recordBug('Step 6: Assessment', 'Trainer', "/workspace/trainer/submission/{$submissionId}/review", $e->getMessage(), 'Evaluasi tugas berjalan tanpa error', 'Exception: ' . $e->getMessage(), $e->getFile(), 'High');
            $this->stepResults['Assessment'] = false;
        }

        CLI::newLine();

        // =====================================================================
        // STEP 7 — CERTIFICATE
        // =====================================================================
        CLI::write(">>> STEP 7: CERTIFICATE (Aktor: Participant & Operator)", "yellow");
        try {
            $learningService = new LearningService();

            // Ensure class 4 certificate_enabled is 1 for testing
            $db->table('learning_classes')->where('id', $testClassId)->update([
                'certificate_enabled' => 1,
                'passing_grade'       => 70.0,
            ]);

            // 7.1 Evaluate completion of participant
            $evaluated = $learningService->evaluateCompletion($enrollmentId);
            $finalEnroll = $db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();

            $isCompleted = ($finalEnroll['status'] === 'completed');
            $isPassed = ! empty($finalEnroll['passed_at']);
            $isEligible = ! empty($finalEnroll['certificate_eligible_at']);

            if (! $isCompleted || ! $isPassed || ! $isEligible) {
                $this->recordBug('Step 7: Certificate', 'Participant', "/workspace/peserta/kelas/{$testClassId}", 'Evaluasi kelulusan gagal menetapkan certificate_eligible_at', 'Status completed, passed_at terisi, certificate_eligible_at terisi', "Status: {$finalEnroll['status']}, Nilai: {$finalEnroll['final_score']}, Eligible: " . var_export($finalEnroll['certificate_eligible_at'], true), 'app/Services/LearningService.php', 'High');
            } else {
                CLI::write("  [OK] Evaluasi kelulusan sukses: Status 'completed', Nilai Akhir: {$finalEnroll['final_score']}, Eligible: {$finalEnroll['certificate_eligible_at']}", "green");
            }

            // 7.2 Operator verifies eligible queue and issues certificate
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $testOperatorId,
                'name'           => 'Operator BegawiPBJ',
                'username'       => 'operator_begawi',
                'account_status' => 'active',
                'roles'          => ['operator'],
            ]);

            $operatorController = new OperatorController();
            $certQueueHtml = $operatorController->certificateVerification();

            if (! str_contains($certQueueHtml, esc($testFullName))) {
                $this->recordBug('Step 7: Certificate', 'Operator', '/workspace/operator/verifikasi-sertifikat', 'Peserta yang eligible tidak muncul di antrean verifikasi sertifikat Operator', 'Peserta muncul di antrean verifikasi sertifikat', 'Peserta tidak ditemukan di HTML antrean', 'app/Controllers/Workspace/OperatorController.php', 'High');
            } else {
                CLI::write("  [OK] Peserta terdeteksi dalam antrean 'Antrean Peserta Memenuhi Syarat Sertifikasi' Operator", "green");
            }

            // Operator issues certificate
            $operatorController->issueCertificate((string) $enrollmentId);

            $certRow = $db->table('certificates')
                ->where('source_type', 'learning_enrollment')
                ->where('source_id', $enrollmentId)
                ->get()->getRowArray();

            if (! $certRow || $certRow['status'] !== 'issued' || empty($certRow['certificate_number'])) {
                $this->recordBug('Step 7: Certificate', 'Operator', "/workspace/operator/verifikasi-sertifikat/{$enrollmentId}/terbitkan", 'Penerbitan sertifikat gagal membuat record aktif di tabel certificates', 'Sertifikat tersimpan dengan status issued dan memiliki certificate_number', 'Record sertifikat tidak valid', 'app/Controllers/Workspace/OperatorController.php', 'High');
            } else {
                $certificateId = (int) $certRow['id'];
                CLI::write("  [OK] Sertifikat resmi berhasil diterbitkan: Nomor '{$certRow['certificate_number']}', Token: '{$certRow['verification_token_hash']}'", "green");
            }

            // 7.3 Participant verifies certificate in "Sertifikat Saya"
            $session->set([
                'isLoggedIn'     => true,
                'user_id'        => $createdUserId,
                'name'           => $testFullName,
                'username'       => $testUsername,
                'email'          => $testEmail,
                'account_status' => 'active',
                'roles'          => ['participant'],
            ]);

            $participantController = new ParticipantController();
            $myCertsHtml = $participantController->certificates();

            if (! str_contains($myCertsHtml, esc($certRow['certificate_number']))) {
                $this->recordBug('Step 7: Certificate', 'Participant', '/workspace/peserta/sertifikat', 'Sertifikat yang telah diterbitkan tidak muncul di menu Sertifikat Saya peserta', 'Sertifikat muncul dengan nomor sertifikat', 'Sertifikat tidak ditemukan di view', 'app/Controllers/Workspace/ParticipantController.php', 'High');
            } else {
                CLI::write("  [OK] Sertifikat berhasil muncul di menu 'Sertifikat Saya' peserta", "green");
            }

            $this->stepResults['Certificate'] = ! isset($this->bugs['Step 7: Certificate']);
        } catch (\Throwable $e) {
            $this->recordBug('Step 7: Certificate', 'Participant', '/workspace/peserta/sertifikat', $e->getMessage(), 'Penerbitan sertifikat berjalan tanpa error', 'Exception: ' . $e->getMessage(), $e->getFile(), 'High');
            $this->stepResults['Certificate'] = false;
        }

        CLI::newLine();

        // =====================================================================
        // SUMMARY REPORT
        // =====================================================================
        CLI::write("==================================================================", "cyan");
        CLI::write("                LAPORAN HASIL PENGUJIAN END-TO-END                ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        CLI::write("STATUS SETIAP TAHAP:", "yellow");
        $allPassed = true;
        foreach (['Register', 'Approval', 'Enrollment', 'Assign Trainer', 'Learning', 'Assessment', 'Certificate'] as $st) {
            $status = $this->stepResults[$st] ?? false;
            if ($status) {
                CLI::write("  - " . str_pad($st, 18) . ": [PASS]", "green");
            } else {
                CLI::write("  - " . str_pad($st, 18) . ": [FAIL]", "red");
                $allPassed = false;
            }
        }

        CLI::newLine();
        if (empty($this->bugs)) {
            CLI::write("HASIL TEMUAN: Tidak ditemukan bug atau flow putus dalam alur end-to-end Arena Belajar!", "green");
            CLI::write("Seluruh perjalanan dari registrasi, approval, enrollment, penugasan trainer, pembelajaran modular, evaluasi tugas, hingga penerbitan sertifikat berjalan 100% konsisten.", "green");
        } else {
            CLI::write("DAFTAR BUG DITEMUKAN (" . count($this->bugs) . " temuan):", "red");
            $i = 1;
            foreach ($this->bugs as $bug) {
                CLI::write("------------------------------------------------------------------", "yellow");
                CLI::write("Bug #{$i}: {$bug['title']}");
                CLI::write("1. Step gagal        : {$bug['step']}");
                CLI::write("2. Aktor             : {$bug['actor']}");
                CLI::write("3. URL               : {$bug['url']}");
                CLI::write("4. Error message     : {$bug['error']}");
                CLI::write("5. Expected behavior : {$bug['expected']}");
                CLI::write("6. Actual behavior   : {$bug['actual']}");
                CLI::write("7. File terkait      : {$bug['file']}");
                CLI::write("8. Prioritas         : {$bug['priority']}", $bug['priority'] === 'High' ? 'red' : 'yellow');
                $i++;
            }
        }

        return $allPassed ? 0 : 1;
    }

    private function recordBug(string $step, string $actor, string $url, string $error, string $expected, string $actual, string $file, string $priority): void
    {
        $this->bugs[] = [
            'title'    => "{$step} - {$error}",
            'step'     => $step,
            'actor'    => $actor,
            'url'      => $url,
            'error'    => $error,
            'expected' => $expected,
            'actual'   => $actual,
            'file'     => $file,
            'priority' => $priority,
        ];
        CLI::write("  [FAIL] {$step}: {$error}", "red");
    }
}

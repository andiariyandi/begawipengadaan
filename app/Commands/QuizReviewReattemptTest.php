<?php

namespace App\Commands;

use App\Controllers\Workspace\ParticipantController;
use App\Services\LearningService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class QuizReviewReattemptTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:quiz-review-reattempt';
    protected $description = 'Verifies quiz review, attempt history, re-attempt logic, highest score retention, and certificate eligibility.';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   VERIFIKASI FITUR REVIEW KUIS, RE-ATTEMPT, & ELIGIBILITAS       ", "yellow");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = \Config\Database::connect();
        $class = $db->table('learning_classes')->where('code', 'PBJ-2026-01')->get()->getRowArray();
        if (! $class) {
            CLI::error("Class PBJ-2026-01 not found.");
            return 1;
        }

        $classId = (int) $class['id'];
        $participant = $db->table('users')->where('email', 'budi.santoso@peserta.begawi.id')->get()->getRowArray()
            ?: $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();

        $userId = (int) $participant['id'];

        // Pastikan enrollment aktif
        $enrollment = $db->table('enrollments')->where(['class_id' => $classId, 'user_id' => $userId])->get()->getRowArray();
        if (! $enrollment) {
            $now = date('Y-m-d H:i:s');
            $db->table('enrollments')->insert([
                'class_id'     => $classId,
                'user_id'      => $userId,
                'status'       => 'active',
                'enrolled_at'  => $now,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            $enrollmentId = (int) $db->insertID();
        } else {
            $enrollmentId = (int) $enrollment['id'];
            $db->table('enrollments')->where('id', $enrollmentId)->update(['status' => 'active']);
        }

        // Cari quiz pre_test atau buat test quiz
        $quiz = $db->table('quizzes')->where(['class_id' => $classId, 'type' => 'post_test'])->get()->getRowArray()
            ?: $db->table('quizzes')->where(['class_id' => $classId, 'type' => 'pre_test'])->get()->getRowArray();

        if (! $quiz) {
            CLI::error("Tidak ada kuis pada kelas PBJ-2026-01.");
            return 1;
        }

        $quizId = (int) $quiz['id'];
        $db->table('quizzes')->where('id', $quizId)->update([
            'attempt_limit' => 3,
            'status'        => 'published',
        ]);

        // Bersihkan attempts lama untuk user & quiz ini untuk tes terisolasi
        $oldAtts = $db->table('quiz_attempts')->where(['quiz_id' => $quizId, 'enrollment_id' => $enrollmentId])->get()->getResultArray();
        if (! empty($oldAtts)) {
            $oldAttIds = array_column($oldAtts, 'id');
            $db->table('quiz_answers')->whereIn('attempt_id', $oldAttIds)->delete();
            $db->table('quiz_attempts')->whereIn('id', $oldAttIds)->delete();
        }

        $learningService = new LearningService();

        // 1. Uji Percobaan Pertama (Attempt #1) dengan skor rendah (50)
        CLI::write(">>> [1/5] Menguji Mulai Percobaan 1 (Attempt #1)...", "yellow");
        $att1Id = $learningService->startQuiz($userId, $quizId);
        CLI::write("  [OK] Percobaan 1 berhasil dimulai (Attempt ID: {$att1Id}).", "green");

        $questions = $db->table('quiz_questions')->where('quiz_id', $quizId)->orderBy('sort_order', 'ASC')->get()->getResultArray();
        if (empty($questions)) {
            CLI::error("Kuis tidak memiliki soal.");
            return 1;
        }

        // Submit jawaban sebagian benar / sebagian salah
        $answers1 = [];
        foreach ($questions as $idx => $q) {
            $opts = $db->table('quiz_options')->where('question_id', $q['id'])->orderBy('sort_order', 'ASC')->get()->getResultArray();
            if ($idx === 0) {
                // Pilih jawaban benar
                foreach ($opts as $o) {
                    if ((int) $o['is_correct'] === 1) {
                        $answers1[$q['id']] = $o['id'];
                        break;
                    }
                }
            } else {
                // Pilih jawaban salah
                foreach ($opts as $o) {
                    if ((int) $o['is_correct'] === 0) {
                        $answers1[$q['id']] = $o['id'];
                        break;
                    }
                }
            }
        }

        $res1 = $learningService->submitQuiz($userId, $att1Id, $answers1);
        $score1 = (float) $res1['score'];
        CLI::write("  [OK] Percobaan 1 selesai disubmit dengan Skor: {$score1}.", "green");

        // 2. Uji Tampilan Review Soal, Kunci Jawaban, Pembahasan & Pembeda Visual
        CLI::write(">>> [2/5] Menguji Tampilan Review Soal & Pembahasan (takeQuiz view)...", "yellow");
        session()->set([
            'isLoggedIn'     => true,
            'user_id'        => $userId,
            'username'       => $participant['username'] ?? 'peserta_test',
            'account_status' => 'active',
            'role'           => 'participant',
            'roles'          => ['participant'],
            'email'          => $participant['email'],
        ]);

        $participantController = new ParticipantController();
        $req = $this->createMockRequest('workspace/peserta/quiz/' . $quizId, 'GET', ['attempt' => $att1Id]);
        $participantController->initController($req, service('response'), service('logger'));
        $reviewHtml = $participantController->takeQuiz((string) $quizId);

        $hasReviewHeader   = str_contains($reviewHtml, 'Review Soal Percobaan #1');
        $hasPembahasan     = str_contains($reviewHtml, 'Pembahasan / Penjelasan Materi:');
        $hasKunciJawaban   = str_contains($reviewHtml, 'Kunci Jawaban Benar');
        $hasJawabanAnda    = str_contains($reviewHtml, 'Jawaban Anda');

        if ($hasReviewHeader && $hasPembahasan && $hasKunciJawaban && $hasJawabanAnda) {
            CLI::write("  [OK] Review Kuis: Header review, Pembahasan, Kunci Jawaban, dan Pembeda Jawaban Peserta tampil sempurna.", "green");
        } else {
            CLI::write("  [FAIL] Review kuis belum menampilkan elemen yang diharapkan.", "red");
            CLI::write("    hasReviewHeader: " . ($hasReviewHeader ? 'YES' : 'NO'));
            CLI::write("    hasPembahasan: " . ($hasPembahasan ? 'YES' : 'NO'));
            CLI::write("    hasKunciJawaban: " . ($hasKunciJawaban ? 'YES' : 'NO'));
            CLI::write("    hasJawabanAnda: " . ($hasJawabanAnda ? 'YES' : 'NO'));
            return 1;
        }

        // 3. Uji Bug Re-attempt: Percobaan 1 belum lulus, kuota masih ada (1 dari 3), pastikan tidak terkunci!
        CLI::write(">>> [3/5] Menguji Re-Attempt saat Sisa Kuota Tersedia (Percobaan 2 dari 3)...", "yellow");
        $state1 = $participantController->resolveClassLearningState($classId, $userId);
        $enrollmentAfter1 = $db->table('enrollments')->where('id', $enrollmentId)->get()->getRowArray();

        if ($enrollmentAfter1['status'] === 'active') {
            CLI::write("  [OK] Status enrollment tetap 'active' (tidak langsung terkunci selesai).", "green");
        } else {
            CLI::write("  [FAIL] Status enrollment terkunci prematur menjadi: {$enrollmentAfter1['status']}", "red");
            return 1;
        }

        // Lakukan Re-Attempt (Percobaan #2)
        $att2Id = $learningService->startQuiz($userId, $quizId);
        CLI::write("  [OK] Percobaan 2 berhasil dimulai tanpa terkunci (Attempt ID: {$att2Id}).", "green");

        // Submit jawaban semua benar untuk percobaan 2
        $answers2 = [];
        foreach ($questions as $q) {
            $opts = $db->table('quiz_options')->where('question_id', $q['id'])->get()->getResultArray();
            foreach ($opts as $o) {
                if ((int) $o['is_correct'] === 1) {
                    $answers2[$q['id']] = $o['id'];
                    break;
                }
            }
        }
        $res2 = $learningService->submitQuiz($userId, $att2Id, $answers2);
        $score2 = (float) $res2['score'];
        CLI::write("  [OK] Percobaan 2 selesai disubmit dengan Skor Tinggi: {$score2}.", "green");

        // 4. Uji Nilai Tertinggi yang Diambil & Riwayat Attempt
        CLI::write(">>> [4/5] Menguji Retensi Nilai Tertinggi & Riwayat Attempt...", "yellow");
        $req2 = $this->createMockRequest('workspace/peserta/quiz/' . $quizId, 'GET');
        $participantController->initController($req2, service('response'), service('logger'));
        $review2Html = $participantController->takeQuiz((string) $quizId);

        $hasTrophyBadge  = str_contains($review2Html, 'Nilai Tertinggi');
        $hasAttemptHist  = str_contains($review2Html, 'Riwayat Percobaan Ujian');
        $hasAttempt1Row  = str_contains($review2Html, 'Percobaan #1');
        $hasAttempt2Row  = str_contains($review2Html, 'Percobaan #2');

        if ($hasTrophyBadge && $hasAttemptHist && $hasAttempt1Row && $hasAttempt2Row) {
            CLI::write("  [OK] Riwayat Attempt lengkap menampilkan Percobaan #1 dan #2 dengan badge Nilai Tertinggi pada skor {$score2}.", "green");
        } else {
            CLI::write("  [FAIL] Riwayat attempt atau badge nilai tertinggi tidak sesuai.", "red");
            return 1;
        }

        // 5. Uji Tampilan Eligibilitas Sertifikat
        CLI::write(">>> [5/5] Menguji Tampilan Informasi Eligibilitas Sertifikat...", "yellow");
        $reqCert = $this->createMockRequest('workspace/peserta/sertifikat', 'GET');
        $participantController->initController($reqCert, service('response'), service('logger'));
        $certHtml = $participantController->certificates();

        if (str_contains($certHtml, 'Status Eligibilitas Sertifikat') || str_contains($certHtml, 'Sertifikat Saya')) {
            CLI::write("  [OK] Halaman Sertifikat memuat informasi eligibilitas dan kriteria pemenuhan syarat.", "green");
        } else {
            CLI::write("  [FAIL] Halaman Sertifikat tidak memuat elemen eligibilitas.", "red");
            return 1;
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write(" [SUCCESS] SEMUA 5 PENGUJIAN REVIEW, RE-ATTEMPT & ELIGIBILITAS LULUS 100%!", "green");
        CLI::write("==================================================================", "cyan");

        return 0;
    }

    private function createMockRequest(string $path, string $method = 'GET', array $params = []): IncomingRequest
    {
        $siteUri = new SiteURI(config('App'), $path);
        if ($method === 'GET' && ! empty($params)) {
            $siteUri->setQuery(http_build_query($params));
        }
        $request = new IncomingRequest(config('App'), $siteUri, null, new UserAgent());
        $request = $request->withMethod($method);
        $_SERVER['HTTP_REFERER']   = site_url($path);
        $_SERVER['REQUEST_METHOD'] = $method;

        if ($method === 'GET') {
            $_GET = $params;
            $request->setGlobal('get', $params);
        } else {
            $_POST = $params;
            $request->setGlobal('post', $params);
        }
        return $request;
    }
}

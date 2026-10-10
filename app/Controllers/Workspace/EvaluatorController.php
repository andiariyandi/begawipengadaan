<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Services\EvaluatorService;
use CodeIgniter\Exceptions\PageNotFoundException;

class EvaluatorController extends BaseController
{
    private EvaluatorService $evaluatorService;

    public function __construct()
    {
        $this->evaluatorService = new EvaluatorService();
    }

    public function index(): string
    {
        $evaluatorId = $this->userId();
        $summary = $this->evaluatorService->getSummary($evaluatorId);

        $items = [];
        foreach ($summary['classes'] as $ac) {
            $items[] = [
                'title' => 'Kelas: ' . ($ac['class_name'] ?? 'Kelas PBJ') . ' (' . ($ac['class_code'] ?? '-') . ')',
                'url'   => site_url('workspace/evaluator/kelas/' . $ac['class_id']),
            ];
        }

        return $this->renderPenilaian('Dashboard Evaluator', 'Pantau dan kelola penilaian serta feedback pembelajaran.', [
            'cards' => [
                ['title' => 'Menunggu Dinilai', 'value' => str_pad((string) $summary['pending_count'], 2, '0', STR_PAD_LEFT), 'meta' => 'submission tugas peserta'],
                ['title' => 'Telah Dinilai', 'value' => str_pad((string) $summary['graded_count'], 2, '0', STR_PAD_LEFT), 'meta' => 'evaluasi selesai'],
                ['title' => 'Rata-rata Nilai', 'value' => $summary['avg_score'] > 0 ? (string) $summary['avg_score'] : '0.0', 'meta' => 'skala 100'],
            ],
            'items' => $items ?: ['Belum ada kelas pembelajaran yang ditugaskan kepada Anda.'],
        ]);
    }

    public function assignments(): string
    {
        $evaluatorId = $this->userId();
        $assignments = $this->evaluatorService->getAssignments($evaluatorId);

        $items = [];
        foreach ($assignments as $asg) {
            $items[] = [
                'title' => ($asg['title'] ?? 'Tugas') . ' · ' . ($asg['class_name'] ?? '-') . ' (' . ($asg['pending_count'] ?? 0) . ' menunggu periksa)',
                'url'   => site_url('workspace/evaluator/kelas/' . $asg['class_id'] . '/submission'),
            ];
        }

        return $this->renderPenilaian('Daftar Penugasan Evaluator', 'Daftar tugas pembelajaran yang perlu diperiksa dan dinilai.', [
            'cards' => [
                ['title' => 'Total Tugas', 'value' => (string) count($assignments), 'meta' => 'tugas aktif di kelas Anda'],
            ],
            'items' => $items ?: ['Belum ada tugas yang aktif di kelas yang ditugaskan.'],
        ]);
    }

    public function classDetail(string $classId): string
    {
        $classIdInt = (int) $classId;
        $class = db_connect()->table('learning_classes lc')
            ->select('lc.*, c.title AS course_title')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->where('lc.id', $classIdInt)
            ->get()->getRowArray();

        if (! $class) {
            throw PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }

        $enrolledCount   = db_connect()->table('enrollments')->where('class_id', $classIdInt)->countAllResults();
        $assignmentCount = db_connect()->table('assignments')->where('class_id', $classIdInt)->countAllResults();
        $submissionsCount = db_connect()->table('assignment_submissions s')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->where('a.class_id', $classIdInt)
            ->countAllResults();

        $items = [
            ['title' => 'Periksa Submission Tugas Peserta (' . $submissionsCount . ' pengumpulan)', 'url' => site_url('workspace/evaluator/kelas/' . $classId . '/submission')],
            ['title' => 'Rekap Lembar Penilaian & Kelulusan Kelas', 'url' => site_url('workspace/evaluator/kelas/' . $classId . '/penilaian')],
            ['title' => 'Feedback & Ulasan Peserta Kelas', 'url' => site_url('workspace/evaluator/kelas/' . $classId . '/feedback')],
        ];

        return $this->renderPenilaian('Detail Evaluasi Kelas #' . esc($classId), ($class['course_title'] ?? '') . ' - ' . ($class['name'] ?? ''), [
            'cards' => [
                ['title' => 'Total Peserta', 'value' => (string) $enrolledCount, 'meta' => 'peserta terdaftar'],
                ['title' => 'Total Tugas', 'value' => (string) $assignmentCount, 'meta' => 'tugas terbit'],
                ['title' => 'Total Pengumpulan', 'value' => (string) $submissionsCount, 'meta' => 'submission tugas'],
            ],
            'items' => $items,
        ]);
    }

    public function submissions(string $classId): string
    {
        $classIdInt = (int) $classId;
        $submissions = $this->evaluatorService->getClassSubmissions($classIdInt);

        return $this->renderPenilaian('Submission Tugas Kelas #' . esc($classId), 'Daftar pengumpulan tugas peserta yang perlu dinilai.', [
            'cards' => [
                ['title' => 'Total Submission', 'value' => (string) count($submissions), 'meta' => 'tugas terkirim'],
            ],
        ], [
            'submissionsList' => $submissions,
        ]);
    }

    public function submissionDetail(string $classId, string $submissionId): string
    {
        $submissionIdInt = (int) $submissionId;
        $detail = $this->evaluatorService->getSubmissionDetail($submissionIdInt);

        if (! $detail) {
            throw PageNotFoundException::forPageNotFound('Submission tugas tidak ditemukan.');
        }

        return $this->renderPenilaian('Detail Submission #' . esc($submissionId), 'Evaluasi dan pemberian feedback submission tugas.', [], [
            'submission'      => $detail['submission'],
            'submissionFiles' => $detail['files'],
        ]);
    }

    public function gradeSubmission(string $classId, string $submissionId)
    {
        $evaluatorId = $this->userId();
        $submissionIdInt = (int) $submissionId;

        $score = $this->request->getPost('score');
        $status = (string) $this->request->getPost('status') ?: 'graded';
        $feedback = (string) $this->request->getPost('feedback');

        if ($score === null || ! is_numeric($score) || (float) $score < 0 || (float) $score > 100) {
            return redirect()->back()->withInput()->with('error', 'Nilai harus berupa angka antara 0 hingga 100.');
        }

        try {
            $this->evaluatorService->gradeSubmission(
                $evaluatorId,
                $submissionIdInt,
                (float) $score,
                $status,
                $feedback
            );

            return redirect()->to(site_url('workspace/evaluator/kelas/' . $classId . '/submission'))
                ->with('message', 'Penilaian dan umpan balik tugas berhasil disimpan.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function grading(string $classId): string
    {
        $classIdInt = (int) $classId;
        $enrollments = $this->evaluatorService->getClassGradingSheet($classIdInt);

        return $this->renderPenilaian('Penilaian Kelas #' . esc($classId), 'Lembar rekap penilaian dan kelulusan peserta kelas.', [
            'cards' => [
                ['title' => 'Jumlah Peserta', 'value' => (string) count($enrollments), 'meta' => 'peserta dalam kelas'],
            ],
        ], [
            'gradingList' => $enrollments,
        ]);
    }

    public function feedback(string $classId): string
    {
        $classIdInt = (int) $classId;
        $feedbacks = $this->evaluatorService->getClassFeedbacks($classIdInt);

        $items = [];
        foreach ($feedbacks as $fb) {
            $items[] = [
                'title' => ($fb['user_name'] ?? 'Peserta') . ' (Rating: ' . ($fb['rating'] ?? 5) . '/5): "' . ($fb['comment'] ?? '-') . '"',
                'url'   => '',
            ];
        }

        return $this->renderPenilaian('Feedback Kelas #' . esc($classId), 'Ulasan dan masukan peserta kelas pembelajaran.', [
            'cards' => [
                ['title' => 'Ulasan Masuk', 'value' => (string) count($feedbacks), 'meta' => 'ulasan peserta'],
            ],
            'items' => $items ?: ['Belum ada peserta yang mengirim feedback untuk kelas ini.'],
        ]);
    }

    public function submitFeedback(string $classId)
    {
        return redirect()->back()->with('message', 'Catatan evaluator berhasil direkam.');
    }

    public function allSubmissions(): string
    {
        $evaluatorId = $this->userId();
        $db = db_connect();

        $classRows = $db->table('class_evaluators')
            ->select('class_id')
            ->where('user_id', $evaluatorId)
            ->where('status', 'active')
            ->get()->getResultArray();
        $classIdList = array_map(static fn ($r) => (int) $r['class_id'], $classRows);

        $submissions = [];
        if ($classIdList !== []) {
            $submissions = $db->table('assignment_submissions s')
                ->select('s.*, a.title AS assignment_title, a.class_id, lc.name AS class_name, u.name AS participant_name, u.email AS participant_email')
                ->join('assignments a', 'a.id = s.assignment_id')
                ->join('learning_classes lc', 'lc.id = a.class_id')
                ->join('enrollments e', 'e.id = s.enrollment_id')
                ->join('users u', 'u.id = e.user_id')
                ->whereIn('a.class_id', $classIdList)
                ->orderBy('s.created_at', 'DESC')
                ->get()->getResultArray();
        }

        $pendingCount = count(array_filter($submissions, static fn ($s) => ($s['status'] ?? '') === 'submitted'));

        return $this->renderPenilaian('Review Submission Tugas', 'Daftar seluruh tugas peserta yang dikumpulkan dari kelas yang Anda ampu.', [
            'cards' => [
                ['title' => 'Total Submission', 'value' => sprintf('%02d', count($submissions)), 'meta' => 'pengumpulan tugas'],
                ['title' => 'Menunggu Review', 'value' => sprintf('%02d', $pendingCount), 'meta' => 'perlu dinilai'],
            ],
        ], [
            'submissionsList' => $submissions,
        ]);
    }

    public function history(): string
    {
        $evaluatorId = $this->userId();
        $db = db_connect();

        $graded = $db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title, a.class_id, lc.name AS class_name, u.name AS participant_name, u.email AS participant_email')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->where('s.graded_by', $evaluatorId)
            ->orderBy('s.graded_at', 'DESC')
            ->get()->getResultArray();

        return $this->renderPenilaian('Riwayat Penilaian Evaluator', 'Rekap riwayat penilaian dan feedback tugas yang telah Anda selesaikan.', [
            'cards' => [
                ['title' => 'Tugas Dinilai', 'value' => sprintf('%02d', count($graded)), 'meta' => 'riwayat evaluasi selesai'],
            ],
        ], [
            'submissionsList' => $graded,
        ]);
    }

    public function allGrading(): string
    {
        $evaluatorId = $this->userId();
        $db = db_connect();

        $classes = $db->table('class_evaluators ce')
            ->select('lc.*, c.title AS course_title, (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = lc.id) AS student_count')
            ->join('learning_classes lc', 'lc.id = ce.class_id')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->where('ce.user_id', $evaluatorId)
            ->where('ce.status', 'active')
            ->get()->getResultArray();

        $items = [];
        foreach ($classes as $cl) {
            $items[] = [
                'title' => ($cl['name'] ?? 'Kelas') . ' (' . ($cl['code'] ?? '-') . ') · ' . ($cl['student_count'] ?? 0) . ' peserta',
                'url'   => site_url('workspace/evaluator/kelas/' . $cl['id'] . '/penilaian'),
            ];
        }

        return $this->renderPenilaian('Lembar Penilaian Kelas', 'Pilih kelas pembelajaran untuk mengelola lembar penilaian dan kelulusan peserta.', [
            'cards' => [
                ['title' => 'Kelas Dievaluasi', 'value' => sprintf('%02d', count($classes)), 'meta' => 'kelas aktif yang diampu'],
            ],
            'items' => $items ?: ['Belum ada kelas pembelajaran yang ditugaskan kepada Anda.'],
        ]);
    }

    public function allFeedback(): string
    {
        $evaluatorId = $this->userId();
        $db = db_connect();

        $classes = $db->table('class_evaluators ce')
            ->select('lc.id, lc.name, lc.code')
            ->join('learning_classes lc', 'lc.id = ce.class_id')
            ->where('ce.user_id', $evaluatorId)
            ->where('ce.status', 'active')
            ->get()->getResultArray();

        $classIdList = array_map(static fn ($c) => (int) $c['id'], $classes);
        $feedbacks = [];
        if ($classIdList !== []) {
            $feedbacks = $db->table('enrollments e')
                ->select('e.feedback, e.feedback_rating, e.updated_at, lc.name AS class_name, u.name AS user_name')
                ->join('learning_classes lc', 'lc.id = e.class_id')
                ->join('users u', 'u.id = e.user_id')
                ->whereIn('e.class_id', $classIdList)
                ->where('e.feedback IS NOT NULL')
                ->orderBy('e.updated_at', 'DESC')
                ->get()->getResultArray();
        }

        $items = [];
        foreach ($feedbacks as $fb) {
            $items[] = [
                'title' => ($fb['user_name'] ?? 'Peserta') . ' (' . ($fb['class_name'] ?? '') . ' - Rating ' . ($fb['feedback_rating'] ?? 5) . '/5): "' . ($fb['feedback'] ?? '-') . '"',
                'url'   => '',
            ];
        }

        return $this->renderPenilaian('Feedback Pembelajaran', 'Ulasan dan masukan peserta kelas pembelajaran yang Anda ampu.', [
            'cards' => [
                ['title' => 'Total Feedback', 'value' => sprintf('%02d', count($feedbacks)), 'meta' => 'ulasan masuk'],
            ],
            'items' => $items ?: ['Belum ada feedback peserta yang diterima untuk kelas Anda.'],
        ]);
    }

    private function userId(): int
    {
        $id = (int) session()->get('user_id');
        if ($id > 0) {
            return $id;
        }
        $firstUser = db_connect()->table('users')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();
        return $firstUser ? (int) $firstUser['id'] : 1;
    }

    private function renderPenilaian(string $title, string $description, array $section = [], array $extra = []): string
    {
        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Evaluator');

        $defaultSection = [
            'eyebrow'     => 'Aktivitas Evaluator',
            'title'       => $title,
            'description' => $description,
            'cards'       => $section['cards'] ?? [],
            'items'       => $section['items'] ?? [],
        ];

        return view('main/workspace/pages/penilaian', array_merge([
            'title'          => $title . ' | Evaluator BegawiPBJ',
            'pageTitle'      => $title,
            'userName'       => $userName,
            'roles'          => ['evaluator'],
            'primaryRole'    => 'evaluator',
            'roleLabel'      => 'Evaluator',
            'menuItems'      => $this->menuItems(),
            'sectionContent' => $defaultSection,
        ], $extra));
    }

    private function menuItems(): array
    {
        $workspace = route_to('workspace.home');
        $section = static fn (string $name): string => route_to('workspace.section', $name);

        return [
            ['label' => 'Dashboard', 'items' => [
                ['label' => 'Ringkasan', 'url' => $workspace],
                ['label' => 'Aktivitas Terakhir', 'url' => $section('aktivitas')],
            ]],
            ['label' => 'Evaluasi', 'items' => [
                ['label' => 'Penugasan Evaluator', 'url' => route_to('workspace.evaluator.assignments')],
                ['label' => 'Penilaian Peserta', 'url' => route_to('workspace.evaluator.home')],
            ]],
        ];
    }
}

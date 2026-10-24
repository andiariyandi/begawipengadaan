<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;

class GradingController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status = trim((string) $this->request->getGet('status'));

        $builder = $db->table('assignment_submissions asub')
            ->select('asub.*, a.title AS assignment_title,
                      u.name AS student_name, u.username, lc.name AS class_name, evaluator.name AS evaluator_name')
            ->join('assignments a', 'a.id = asub.assignment_id')
            ->join('enrollments e', 'e.id = asub.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('users evaluator', 'evaluator.id = asub.graded_by', 'left')
            ->orderBy('asub.created_at', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('u.username', $keyword)
                ->orLike('a.title', $keyword)
                ->orLike('lc.name', $keyword)
                ->groupEnd();
        }
        if ($status !== '') {
            $builder->where('asub.status', $status);
        }

        $submissions = $builder->get()->getResultArray();

        $stats = [
            'total'     => (int) $db->table('assignment_submissions')->countAllResults(),
            'graded'    => (int) $db->table('assignment_submissions')->where('status', 'graded')->countAllResults(),
            'submitted' => (int) $db->table('assignment_submissions')->whereIn('status', ['submitted', 'in_review'])->countAllResults(),
            'revision'  => (int) $db->table('assignment_submissions')->where('status', 'revision')->countAllResults(),
        ];

        return view('dashboard/learning/gradings/index', [
            'title'       => 'Penilaian | Dashboard BegawiPengadaan',
            'pageTitle'   => 'Penilaian dan Evaluasi Tugas',
            'submissions' => $submissions,
            'stats'       => $stats,
            'filters'     => [
                'keyword' => $keyword,
                'status'  => $status,
            ],
            'activeMenu'  => 'learning',
            'activeSubmenu' => 'gradings',
        ]);
    }

    public function detail(string $id): string
    {
        $db = db_connect();
        $submission = $db->table('assignment_submissions asub')
            ->select('asub.*, a.title AS assignment_title, a.instructions AS assignment_description,
                      u.name AS student_name, u.username, u.email AS student_email,
                      lc.name AS class_name, evaluator.name AS evaluator_name')
            ->join('assignments a', 'a.id = asub.assignment_id')
            ->join('enrollments e', 'e.id = asub.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('users evaluator', 'evaluator.id = asub.graded_by', 'left')
            ->where('asub.id', $id)
            ->get()->getRowArray();

        $files = $db->table('assignment_submission_files asf')
            ->select('asf.*, fa.original_name, fa.mime_type, fa.size_bytes, fa.path')
            ->join('file_assets fa', 'fa.id = asf.file_id', 'left')
            ->where('asf.submission_id', (int) $id)
            ->get()->getResultArray();

        return view('dashboard/learning/gradings/detail', [
            'title'         => 'Detail Penilaian | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Penilaian Tugas',
            'gradingId'     => $id,
            'submission'    => $submission,
            'files'         => $files,
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'gradings',
        ]);
    }

    public function grade(string $id)
    {
        $submissionId = (int) $id;
        $score        = (float) $this->request->getPost('score');
        $status       = (string) ($this->request->getPost('status') ?: 'graded');
        $feedback     = trim((string) $this->request->getPost('feedback'));

        if ($score < 0.0 || $score > 100.0) {
            return redirect()->back()->withInput()->with('error', 'Nilai harus berada dalam rentang 0.00 hingga 100.00.');
        }

        $graderId = (int) (session()->get('user_id') ?: 1);

        try {
            $gradingService = new \App\Services\GradingService();
            $gradingService->gradeSubmission($graderId, $submissionId, $score, $status, $feedback ?: null);

            return redirect()->to(site_url('dashboard/pembelajaran/penilaian/' . $submissionId))
                ->with('success', "Penilaian submisi #{$submissionId} berhasil diperbarui dengan nilai {$score}.");
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }
}

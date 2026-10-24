<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\Learning\LearningClassModel;
use App\Services\CertificateGenerator;

class CertificateEligibilityController extends BaseController
{
    protected EnrollmentModel $enrollmentModel;
    protected LearningClassModel $classModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new LearningClassModel();
    }

    public function index(): string
    {
        $db = db_connect();
        $keyword = trim((string) $this->request->getGet('keyword'));
        $eligibilityFilter = trim((string) $this->request->getGet('eligibility_status'));
        $classId = (int) $this->request->getGet('class_id');

        $builder = $db->table('enrollments e')
            ->select('e.*, u.name AS student_name, u.username, u.email AS student_email,
                      lc.name AS class_name, lc.code AS class_code, lc.passing_grade, lc.certificate_enabled,
                      cert.id AS certificate_id, cert.certificate_number, cert.status AS cert_status,
                      cert.issued_at AS cert_issued_at, cert.pdf_file_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('certificates cert', 'cert.source_type = "learning_enrollment" AND cert.source_id = e.id AND cert.status = "issued"', 'left')
            ->orderBy('e.completed_at', 'DESC')
            ->orderBy('e.enrolled_at', 'DESC');

        if ($classId > 0) {
            $builder->where('e.class_id', $classId);
        }
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('u.email', $keyword)
                ->orLike('lc.name', $keyword)
                ->orLike('cert.certificate_number', $keyword)
                ->groupEnd();
        }

        $allRows = $builder->get()->getResultArray();

        // Categorize each row
        $eligibleCount = 0;
        $issuedCount = 0;
        $notEligibleCount = 0;

        foreach ($allRows as &$row) {
            $isIssued     = ! empty($row['certificate_id']);
            $certEnabled  = (int) ($row['certificate_enabled'] ?? 0) === 1;
            $passingGrade = (float) ($row['passing_grade'] ?? 70.0);
            $finalScore   = (float) ($row['final_score'] ?? 0);

            $isEligible = (! $isIssued) && $certEnabled && (
                ! empty($row['certificate_eligible_at']) ||
                (in_array($row['status'], ['completed', 'passed'], true) && ! empty($row['passed_at']) && $finalScore >= $passingGrade)
            );

            if ($isIssued) {
                $row['calculated_status'] = 'issued';
                $issuedCount++;
            } elseif ($isEligible) {
                $row['calculated_status'] = 'eligible';
                $eligibleCount++;
            } else {
                $row['calculated_status'] = 'not_eligible';
                $notEligibleCount++;
            }
        }
        unset($row);

        // Apply filter if specified
        $filteredRows = $allRows;
        if ($eligibilityFilter !== '') {
            $filteredRows = array_filter($allRows, static fn($r) => $r['calculated_status'] === $eligibilityFilter);
        }

        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_eligibility') ?: $this->request->getGet('page') ?: 1));
        $total = count($filteredRows);
        $paginatedRecords = array_slice(array_values($filteredRows), ($page - 1) * $perPage, $perPage);

        $pager = service('pager');
        $pager->store('eligibility', $page, $perPage, $total, 0);

        $stats = [
            'total'        => count($allRows),
            'eligible'     => $eligibleCount,
            'issued'       => $issuedCount,
            'not_eligible' => $notEligibleCount,
        ];

        $classes = $this->classModel->orderBy('name', 'ASC')->findAll();

        return view('dashboard/learning/certificate_eligibility/index', [
            'title'        => 'Kelayakan Sertifikat | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Kelayakan Penerbitan Sertifikat Pembelajaran',
            'records'      => $paginatedRecords,
            'stats'        => $stats,
            'classes'      => $classes,
            'pager'        => $pager,
            'filters'      => [
                'keyword'            => $keyword,
                'class_id'           => $classId,
                'eligibility_status' => $eligibilityFilter,
            ],
            'activeMenu'   => 'learning',
            'activeSubmenu' => 'certificate-eligibility',
        ]);
    }

    public function generate(string $enrollmentId)
    {
        $actorId = (int) session()->get('user_id');
        $db = db_connect();

        $enr = $db->table('enrollments e')
            ->select('e.*, lc.passing_grade, lc.certificate_enabled')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->where('e.id', (int) $enrollmentId)
            ->get()->getRowArray();

        if (! $enr) {
            return redirect()->back()->with('error', 'Enrollment tidak ditemukan.');
        }

        $certEnabled  = (int) ($enr['certificate_enabled'] ?? 0) === 1;
        $passingGrade = (float) ($enr['passing_grade'] ?? 70.0);
        $finalScore   = (float) ($enr['final_score'] ?? 0);
        $isEligible   = $certEnabled && (
            ! empty($enr['certificate_eligible_at']) ||
            (in_array($enr['status'], ['completed', 'passed'], true) && ! empty($enr['passed_at']) && $finalScore >= $passingGrade)
        );

        if (! $isEligible) {
            return redirect()->back()->with('error', 'Peserta belum memenuhi syarat kelulusan kelas atau sertifikat kelas dinonaktifkan.');
        }

        $generator = new CertificateGenerator();

        try {
            $result = $generator->generateDirect('learning_enrollment', (int) $enrollmentId, $actorId);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menerbitkan sertifikat: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Sertifikat berhasil diterbitkan dengan nomor: ' . ($result['certificate_number'] ?? ''));
    }

    public function batchGenerate()
    {
        $classId = (int) $this->request->getPost('class_id');
        $actorId = (int) session()->get('user_id');
        $generator = new CertificateGenerator();

        $db = db_connect();
        $builder = $db->table('enrollments e')
            ->select('e.id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('certificates cert', 'cert.source_type = "learning_enrollment" AND cert.source_id = e.id AND cert.status = "issued"', 'left')
            ->where('cert.id IS NULL', null, false)
            ->where('lc.certificate_enabled', 1)
            ->groupStart()
                ->where('e.certificate_eligible_at IS NOT NULL', null, false)
                ->orGroupStart()
                    ->whereIn('e.status', ['completed', 'passed'])
                    ->where('e.passed_at IS NOT NULL')
                    ->where('e.final_score >= lc.passing_grade', null, false)
                ->groupEnd()
            ->groupEnd();

        if ($classId > 0) {
            $builder->where('e.class_id', $classId);
        }

        $eligibleEnrollments = $builder->get()->getResultArray();

        if (empty($eligibleEnrollments)) {
            return redirect()->back()->with('info', 'Tidak ada peserta yang memenuhi syarat untuk diterbitkan saat ini.');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($eligibleEnrollments as $enr) {
            try {
                $generator->generateDirect('learning_enrollment', (int) $enr['id'], $actorId);
                $successCount++;
            } catch (\Throwable $e) {
                $errorCount++;
            }
        }

        $message = "Proses batch selesai: {$successCount} sertifikat berhasil diterbitkan.";
        if ($errorCount > 0) {
            $message .= " ({$errorCount} peserta dilewati/gagal).";
        }

        return redirect()->back()->with('success', $message);
    }
}

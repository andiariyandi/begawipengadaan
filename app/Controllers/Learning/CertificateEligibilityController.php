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
                      lc.name AS class_name, lc.code AS class_code, lc.passing_grade,
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
            $isIssued = ! empty($row['certificate_id']);
            $isEligible = (! $isIssued) && (
                in_array($row['status'], ['completed', 'passed'], true) ||
                ! empty($row['certificate_eligible_at'])
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

        $stats = [
            'total'        => count($allRows),
            'eligible'     => $eligibleCount,
            'issued'       => $issuedCount,
            'not_eligible' => $notEligibleCount,
        ];

        $classes = $this->classModel->orderBy('name', 'ASC')->findAll();

        return view('dashboard/learning/certificate_eligibility/index', [
            'title'        => 'Kelayakan Sertifikat | Dashboard BegawiPBJ',
            'pageTitle'    => 'Kelayakan Penerbitan Sertifikat Pembelajaran',
            'records'      => $filteredRows,
            'stats'        => $stats,
            'classes'      => $classes,
            'filters'      => [
                'keyword'            => $keyword,
                'class_id'           => $classId,
                'eligibility_status' => $eligibilityFilter,
            ],
            'activeMenu'   => 'learning',
            'activeSubmenu'=> 'certificate-eligibility',
        ]);
    }

    public function generate(string $enrollmentId)
    {
        $actorId = (int) session()->get('user_id');
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
            ->join('certificates cert', 'cert.source_type = "learning_enrollment" AND cert.source_id = e.id AND cert.status = "issued"', 'left')
            ->where('cert.id IS NULL', null, false)
            ->groupStart()
                ->whereIn('e.status', ['completed', 'passed'])
                ->orWhere('e.certificate_eligible_at IS NOT NULL', null, false)
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

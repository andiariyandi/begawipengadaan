<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\ProgramModel;

class LearningReportController extends BaseController
{
    protected LearningClassModel $classModel;
    protected ProgramModel $programModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->classModel   = new LearningClassModel();
        $this->programModel = new ProgramModel();
    }

    public function index()
    {
        $db = db_connect();

        $keyword   = trim((string) $this->request->getGet('keyword'));
        $programId = (int) $this->request->getGet('program_id');
        $status    = trim((string) $this->request->getGet('status'));
        $export    = trim((string) $this->request->getGet('export'));

        // 1. Aggregate KPI Summary
        $totalEnrollments     = (int) $db->table('enrollments')->countAllResults();
        $activeEnrollments    = (int) $db->table('enrollments')->whereIn('status', ['active', 'enrolled', 'in_progress'])->countAllResults();
        $completedEnrollments = (int) $db->table('enrollments')->whereIn('status', ['completed', 'passed'])->countAllResults();
        $overallCompletionRate = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0.0;

        $avgScoreRow = $db->table('enrollments')
            ->select('AVG(final_score) as avg_score')
            ->where('final_score IS NOT NULL', null, false)
            ->where('final_score >', 0)
            ->get()->getRow();
        $averageScore = $avgScoreRow && $avgScoreRow->avg_score !== null ? round((float) $avgScoreRow->avg_score, 1) : 0.0;

        $issuedCertificates = (int) $db->table('certificates')
            ->where('source_type', 'learning_enrollment')
            ->where('status', 'issued')
            ->countAllResults();

        $totalClasses = (int) $db->table('learning_classes')->countAllResults();
        $activeTrainers = (int) $db->table('class_trainers')
            ->where('status', 'active')
            ->select('COUNT(DISTINCT user_id) as total')
            ->get()->getRow()->total ?? 0;

        $summary = [
            'total_enrollments'       => $totalEnrollments,
            'active_enrollments'      => $activeEnrollments,
            'completed_enrollments'   => $completedEnrollments,
            'overall_completion_rate' => $overallCompletionRate,
            'average_score'           => $averageScore,
            'issued_certificates'     => $issuedCertificates,
            'total_classes'           => $totalClasses,
            'active_trainers'         => $activeTrainers,
        ];

        // 2. Class Performance Analytics
        $classBuilder = $db->table('learning_classes lc')
            ->select('lc.id, lc.name, lc.code, lc.quota, lc.passing_grade, lc.status, lc.start_date, lc.end_date,
                      c.title AS course_title, p.title AS program_title,
                      COUNT(e.id) AS total_enrolled,
                      SUM(CASE WHEN e.status IN ("completed", "passed") THEN 1 ELSE 0 END) AS completed_count,
                      SUM(CASE WHEN e.status = "passed" OR (e.final_score IS NOT NULL AND e.final_score >= lc.passing_grade) THEN 1 ELSE 0 END) AS passed_count,
                      AVG(CASE WHEN e.final_score IS NOT NULL AND e.final_score > 0 THEN e.final_score ELSE NULL END) AS avg_final_score')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->join('programs p', 'p.id = c.program_id', 'left')
            ->join('enrollments e', 'e.class_id = lc.id', 'left')
            ->groupBy('lc.id, lc.name, lc.code, lc.quota, lc.passing_grade, lc.status, lc.start_date, lc.end_date, c.title, p.title')
            ->orderBy('lc.created_at', 'DESC');

        if ($keyword !== '') {
            $classBuilder->groupStart()
                ->like('lc.name', $keyword)
                ->orLike('lc.code', $keyword)
                ->orLike('c.title', $keyword)
                ->groupEnd();
        }
        if ($programId > 0) {
            $classBuilder->where('c.program_id', $programId);
        }
        if ($status !== '') {
            $classBuilder->where('lc.status', $status);
        }

        $classReports = $classBuilder->get()->getResultArray();

        foreach ($classReports as &$cr) {
            $enrolled = (int) $cr['total_enrolled'];
            $completed = (int) $cr['completed_count'];
            $passed = (int) $cr['passed_count'];
            $cr['completion_rate'] = $enrolled > 0 ? round(($completed / $enrolled) * 100, 1) : 0.0;
            $cr['passing_rate']    = $enrolled > 0 ? round(($passed / $enrolled) * 100, 1) : 0.0;
            $cr['avg_final_score'] = $cr['avg_final_score'] !== null ? round((float) $cr['avg_final_score'], 1) : 0.0;
        }
        unset($cr);

        // 3. Trainer Performance Analytics
        $trainerReports = $db->table('class_trainers ct')
            ->select('u.id AS user_id, u.name, u.email, u.username,
                      COUNT(DISTINCT ct.class_id) AS total_classes,
                      COUNT(DISTINCT CASE WHEN lc.status IN ("open", "active", "ongoing") THEN ct.class_id END) AS active_classes,
                      COUNT(DISTINCT e.id) AS total_students,
                      COUNT(DISTINCT CASE WHEN e.status IN ("completed", "passed") THEN e.id END) AS completed_students,
                      AVG(CASE WHEN e.final_score IS NOT NULL AND e.final_score > 0 THEN e.final_score END) AS avg_student_score')
            ->join('users u', 'u.id = ct.user_id')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->join('enrollments e', 'e.class_id = lc.id', 'left')
            ->where('ct.status', 'active')
            ->groupBy('u.id, u.name, u.email, u.username')
            ->orderBy('total_students', 'DESC')
            ->get()->getResultArray();

        foreach ($trainerReports as &$tr) {
            $students = (int) $tr['total_students'];
            $comp = (int) $tr['completed_students'];
            $tr['completion_rate'] = $students > 0 ? round(($comp / $students) * 100, 1) : 0.0;
            $tr['avg_student_score'] = $tr['avg_student_score'] !== null ? round((float) $tr['avg_student_score'], 1) : 0.0;
        }
        unset($tr);

        // 4. Program Popularity & Stats
        $programReports = $db->table('programs p')
            ->select('p.id, p.title, p.code, p.category, p.status,
                      COUNT(DISTINCT c.id) AS total_courses,
                      COUNT(DISTINCT lc.id) AS total_classes,
                      COUNT(DISTINCT e.id) AS total_enrollments,
                      COUNT(DISTINCT CASE WHEN e.status IN ("completed", "passed") THEN e.id END) AS total_completed')
            ->join('courses c', 'c.id = p.id OR c.program_id = p.id', 'left')
            ->join('learning_classes lc', 'lc.course_id = c.id', 'left')
            ->join('enrollments e', 'e.class_id = lc.id', 'left')
            ->groupBy('p.id, p.title, p.code, p.category, p.status')
            ->orderBy('total_enrollments', 'DESC')
            ->get()->getResultArray();

        foreach ($programReports as &$pr) {
            $enr = (int) $pr['total_enrollments'];
            $comp = (int) $pr['total_completed'];
            $pr['completion_rate'] = $enr > 0 ? round(($comp / $enr) * 100, 1) : 0.0;
        }
        unset($pr);

        // Handle CSV Export
        if ($export === 'classes') {
            return $this->exportClassesCsv($classReports);
        }
        if ($export === 'trainers') {
            return $this->exportTrainersCsv($trainerReports);
        }

        $programs = $this->programModel->orderBy('title', 'ASC')->findAll();

        return view('dashboard/learning/reports/index', [
            'title'          => 'Laporan & Analitik Pembelajaran | Dashboard BegawiPBJ',
            'pageTitle'      => 'Laporan & Analitik Pembelajaran',
            'summary'        => $summary,
            'classReports'   => $classReports,
            'trainerReports' => $trainerReports,
            'programReports' => $programReports,
            'programs'       => $programs,
            'filters'        => [
                'keyword'    => $keyword,
                'program_id' => $programId,
                'status'     => $status,
            ],
            'activeMenu'     => 'learning',
            'activeSubmenu'  => 'reports',
        ]);
    }

    private function exportClassesCsv(array $data)
    {
        $filename = 'laporan_kinerja_kelas_' . date('Ymd_His') . '.csv';
        $output   = fopen('php://temp', 'r+');

        // CSV Header
        fputcsv($output, [
            'Kode Kelas',
            'Nama Kelas',
            'Course',
            'Program',
            'Status',
            'Kapasitas (Quota)',
            'Total Terdaftar',
            'Selesai',
            'Lulus',
            'Tingkat Penyelesaian (%)',
            'Tingkat Kelulusan (%)',
            'Rata-rata Nilai Akhir',
        ]);

        foreach ($data as $row) {
            fputcsv($output, [
                $row['code'] ?? '-',
                $row['name'] ?? '-',
                $row['course_title'] ?? '-',
                $row['program_title'] ?? '-',
                $row['status'] ?? '-',
                $row['quota'] ?? 0,
                $row['total_enrolled'],
                $row['completed_count'],
                $row['passed_count'],
                $row['completion_rate'] . '%',
                $row['passing_rate'] . '%',
                $row['avg_final_score'],
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    private function exportTrainersCsv(array $data)
    {
        $filename = 'laporan_kinerja_trainer_' . date('Ymd_His') . '.csv';
        $output   = fopen('php://temp', 'r+');

        // CSV Header
        fputcsv($output, [
            'Nama Trainer',
            'Email',
            'Username',
            'Total Kelas Diampu',
            'Kelas Aktif',
            'Total Peserta Dibina',
            'Peserta Selesai',
            'Tingkat Penyelesaian (%)',
            'Rata-rata Nilai Peserta',
        ]);

        foreach ($data as $row) {
            fputcsv($output, [
                $row['name'] ?? '-',
                $row['email'] ?? '-',
                $row['username'] ?? '-',
                $row['total_classes'],
                $row['active_classes'],
                $row['total_students'],
                $row['completed_students'],
                $row['completion_rate'] . '%',
                $row['avg_student_score'],
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }
}

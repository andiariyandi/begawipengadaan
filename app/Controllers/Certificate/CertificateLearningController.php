<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Models\Certificate\CertificateModel;
use App\Services\AuditService;
use App\Services\CertificateGenerator;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;

/**
 * CertificateLearningController
 *
 * Mengelola penerbitan sertifikat berbasis hierarki pembelajaran:
 * PROGRAM -> COURSE -> CLASS -> ENROLLMENT PESERTA -> COMPLETION -> CERTIFICATE
 */
class CertificateLearningController extends BaseController
{
    private ConnectionInterface $db;
    private CertificateModel $certificateModel;
    private CertificateGenerator $generator;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db               = db_connect();
        $this->certificateModel = new CertificateModel();
        $this->generator        = new CertificateGenerator();
        $this->auditService     = new AuditService();
    }

    /**
     * GET /dashboard/sertifikat/learning
     * Menampilkan daftar kelas dengan status sertifikasi.
     */
    public function index()
    {
        $keyword   = trim((string) $this->request->getGet('keyword'));
        $programId = (int) $this->request->getGet('program_id');
        $courseId  = (int) $this->request->getGet('course_id');

        $builder = $this->db->table('learning_classes lc')
            ->select([
                'lc.id',
                'lc.name AS class_name',
                'lc.code AS class_code',
                'lc.start_at AS start_date',
                'lc.end_at AS end_date',
                'lc.status AS class_status',
                'co.id   AS course_id',
                'co.title AS course_title',
                'p.id    AS program_id',
                'p.title  AS program_title',
                'COUNT(e.id) AS total_enrolled',
                'SUM(CASE WHEN e.status = "completed" THEN 1 ELSE 0 END) AS total_completed',
                'SUM(CASE WHEN c.id IS NOT NULL AND c.status = "issued" THEN 1 ELSE 0 END) AS total_certified',
            ])
            ->join('courses co',   'co.id = lc.course_id',  'left')
            ->join('programs p',   'p.id = co.program_id',  'left')
            ->join('enrollments e', 'e.class_id = lc.id',   'left')
            ->join('certificates c', 'c.source_type = "learning_enrollment" AND c.source_id = e.id', 'left')
            ->groupBy('lc.id')
            ->orderBy('lc.id', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('lc.name', $keyword)
                ->orLike('co.title', $keyword)
                ->orLike('p.title', $keyword)
                ->groupEnd();
        }

        if ($programId > 0) {
            $builder->where('p.id', $programId);
        }

        if ($courseId > 0) {
            $builder->where('co.id', $courseId);
        }

        $classes = $builder->get()->getResultArray();

        // Load dropdown options
        $programs = $this->db->table('programs')->select('id, title')->orderBy('title', 'ASC')->get()->getResultArray();
        $courses  = $this->db->table('courses')->select('id, title, program_id')->orderBy('title', 'ASC')->get()->getResultArray();

        return view('dashboard/certificates/learning', [
            'title'         => 'Learning Certificate | Dashboard BegawiPBJ',
            'pageTitle'     => 'Learning Certificate',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'learning',
            'classes'       => $classes,
            'programs'      => $programs,
            'courses'       => $courses,
            'keyword'       => $keyword,
            'selectedProg'  => $programId,
            'selectedCourse'=> $courseId,
        ]);
    }

    /**
     * GET /dashboard/sertifikat/learning/kelas/{classId}
     * Menampilkan daftar peserta pada suatu kelas dengan status kelayakan sertifikat.
     */
    public function classDetail(string $classId)
    {
        $classIdInt = (int) $classId;
        $class = $this->db->table('learning_classes lc')
            ->select([
                'lc.*',
                'co.title AS course_title',
                'co.code AS course_code',
                'p.title AS program_title',
            ])
            ->join('courses co', 'co.id = lc.course_id', 'left')
            ->join('programs p',  'p.id = co.program_id', 'left')
            ->where('lc.id', $classIdInt)
            ->get()
            ->getRowArray();

        if (! $class) {
            return redirect()->to(site_url('dashboard/sertifikat/learning'))
                ->with('error', 'Kelas pembelajaran tidak ditemukan.');
        }

        // Get trainers
        $trainers = $this->db->table('class_trainers ct')
            ->select('u.name, u.email')
            ->join('users u', 'u.id = ct.user_id', 'left')
            ->where('ct.class_id', $classIdInt)
            ->get()
            ->getResultArray();

        // Get enrollments with completion and certificate status
        $participants = $this->db->table('enrollments e')
            ->select([
                'e.id AS enrollment_id',
                'e.user_id',
                'e.status AS enrollment_status',
                'COALESCE(lp.progress_percent, CASE WHEN e.status = "completed" THEN 100.0 ELSE 0.0 END) AS progress_percent',
                'e.final_score',
                'e.completed_at',
                'u.name AS user_name',
                'u.email AS user_email',
                'c.id AS certificate_id',
                'c.certificate_number',
                'c.status AS certificate_status',
                'c.issued_at AS certificate_issued_at',
            ])
            ->join('learning_progress lp', 'lp.enrollment_id = e.id', 'left')
            ->join('users u', 'u.id = e.user_id', 'left')
            ->join('certificates c', 'c.source_type = "learning_enrollment" AND c.source_id = e.id', 'left')
            ->where('e.class_id', $classIdInt)
            ->orderBy('u.name', 'ASC')
            ->get()
            ->getResultArray();

        // Calculate eligibility
        $eligibleCount = 0;
        foreach ($participants as &$p) {
            $isCompleted = ($p['enrollment_status'] === 'completed' || (float) ($p['progress_percent'] ?? 0) >= 100.0);
            $hasCert     = (! empty($p['certificate_id']) && $p['certificate_status'] === 'issued');

            if ($hasCert) {
                $p['eligibility_status'] = 'certified';
                $p['eligibility_label']  = 'Sudah Terbit';
            } elseif ($isCompleted) {
                $p['eligibility_status'] = 'eligible';
                $p['eligibility_label']  = 'Layak';
                $eligibleCount++;
            } else {
                $p['eligibility_status'] = 'not_eligible';
                $p['eligibility_label']  = 'Belum Layak';
            }
        }
        unset($p);

        return view('dashboard/certificates/learning_detail', [
            'title'         => 'Peserta & Kelayakan Sertifikat | ' . ($class['name'] ?? 'Kelas'),
            'pageTitle'     => 'Learning Certificate — ' . ($class['name'] ?? 'Kelas'),
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'learning',
            'class'         => $class,
            'trainers'      => $trainers,
            'participants'  => $participants,
            'eligibleCount' => $eligibleCount,
        ]);
    }

    /**
     * POST /dashboard/sertifikat/learning/issue/{enrollmentId}
     * Penerbitan single sertifikat untuk enrollment tertentu.
     */
    public function issueSingle(string $enrollmentId)
    {
        $enrollmentIdInt = (int) $enrollmentId;
        $actorId         = (int) session()->get('user_id');

        try {
            $enrollment = $this->db->table('enrollments')->where('id', $enrollmentIdInt)->get()->getRowArray();
            if (! $enrollment) {
                throw new RuntimeException('Data pendaftaran kelas (enrollment) tidak ditemukan.');
            }

            // Validasi kelayakan
            if ($enrollment['status'] !== 'completed' && (float) ($enrollment['progress_percent'] ?? 0) < 100.0) {
                throw new RuntimeException('Peserta belum menyelesaikan pembelajaran (progress belum 100% / belum lulus).');
            }

            $result = $this->generator->generateDirect('learning_enrollment', $enrollmentIdInt, $actorId);

            return redirect()->back()->with(
                'success',
                "Sertifikat berhasil diterbitkan dengan nomor: {$result['certificate_number']}."
            );
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menerbitkan sertifikat: ' . $e->getMessage());
        }
    }

    /**
     * POST /dashboard/sertifikat/learning/kelas/{classId}/batch
     * Penerbitan sertifikat secara batch untuk seluruh peserta kelas yang layak.
     */
    public function issueClassBatch(string $classId)
    {
        $classIdInt = (int) $classId;
        $actorId    = (int) session()->get('user_id');

        try {
            $enrollments = $this->db->table('enrollments e')
                ->select('e.id, e.user_id')
                ->where('e.class_id', $classIdInt)
                ->where('e.status', 'completed')
                ->get()
                ->getResultArray();

            if (empty($enrollments)) {
                // Fallback check 100% progress
                $enrollments = $this->db->table('enrollments e')
                    ->select('e.id, e.user_id')
                    ->join('learning_progress lp', 'lp.enrollment_id = e.id', 'left')
                    ->where('e.class_id', $classIdInt)
                    ->where('lp.progress_percent >=', 100.0)
                    ->get()
                    ->getResultArray();
            }

            if (empty($enrollments)) {
                return redirect()->back()->with('warning', 'Tidak ada peserta yang memenuhi syarat kelulusan (100% selesai) di kelas ini.');
            }

            $successCount = 0;
            $skippedCount = 0;
            $failedCount  = 0;

            foreach ($enrollments as $e) {
                $enrId = (int) $e['id'];
                // Check if already certified
                $exists = $this->db->table('certificates')
                    ->where('source_type', 'learning_enrollment')
                    ->where('source_id', $enrId)
                    ->countAllResults();

                if ($exists > 0) {
                    $skippedCount++;
                    continue;
                }

                try {
                    $this->generator->generateDirect('learning_enrollment', $enrId, $actorId);
                    $successCount++;
                } catch (\Throwable $err) {
                    $failedCount++;
                    log_message('error', '[BatchLearningCert] Failed for enrollment #' . $enrId . ': ' . $err->getMessage());
                }
            }

            $msg = "Batch selesai. Berhasil diterbitkan: {$successCount} sertifikat.";
            if ($skippedCount > 0) {
                $msg .= " Dilewati (sudah memiliki sertifikat): {$skippedCount}.";
            }
            if ($failedCount > 0) {
                $msg .= " Gagal: {$failedCount}.";
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses batch sertifikat kelas: ' . $e->getMessage());
        }
    }
}

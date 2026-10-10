<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Models\Certificate\CertificateTemplateModel;
use App\Services\AuditService;
use App\Services\CertificateGenerator;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;

/**
 * CertificateBatchController
 *
 * Konsol generator batch sertifikat dengan 2 tab:
 * Tab 1: Learning Class Batch
 * Tab 2: Event Batch
 */
class CertificateBatchController extends BaseController
{
    private ConnectionInterface $db;
    private CertificateGenerator $generator;
    private CertificateTemplateModel $templateModel;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db            = db_connect();
        $this->generator     = new CertificateGenerator();
        $this->templateModel = new CertificateTemplateModel();
        $this->auditService  = new AuditService();
    }

    /**
     * GET /dashboard/sertifikat/batch
     */
    public function index()
    {
        $activeTab = (string) ($this->request->getGet('tab') ?: 'learning');
        if (! in_array($activeTab, ['learning', 'event'], true)) {
            $activeTab = 'learning';
        }

        // Data for Learning Tab
        $learningClasses = $this->db->table('learning_classes lc')
            ->select([
                'lc.id',
                'lc.name AS class_name',
                'co.title AS course_title',
                'p.title AS program_title',
                'COUNT(DISTINCT e.id) AS total_enrolled',
                'COUNT(DISTINCT CASE WHEN e.status = "completed" OR lp.progress_percent >= 100 THEN e.id END) AS total_eligible',
                'COUNT(DISTINCT CASE WHEN c.id IS NOT NULL AND c.status = "issued" THEN e.id END) AS total_certified',
            ])
            ->join('courses co',          'co.id = lc.course_id',         'left')
            ->join('programs p',          'p.id = co.program_id',         'left')
            ->join('enrollments e',        'e.class_id = lc.id',          'left')
            ->join('learning_progress lp', 'lp.enrollment_id = e.id',      'left')
            ->join('certificates c',       'c.source_type = "learning_enrollment" AND c.source_id = e.id', 'left')
            ->groupBy('lc.id')
            ->orderBy('lc.id', 'DESC')
            ->get()
            ->getResultArray();

        // Data for Event Tab
        $events = $this->db->table('events ev')
            ->select([
                'ev.id',
                'ev.title',
                'ev.start_at',
                'ev.end_at',
                'COUNT(er.id) AS total_registered',
                'SUM(CASE WHEN a.status = "present" THEN 1 ELSE 0 END) AS total_present',
                'SUM(CASE WHEN c.id IS NOT NULL AND c.status = "issued" THEN 1 ELSE 0 END) AS total_certified',
            ])
            ->join('event_registrations er', 'er.event_id = ev.id', 'left')
            ->join('attendances a', 'a.event_registration_id = er.id', 'left')
            ->join('certificates c', 'c.source_type = "event_registration" AND c.source_id = er.id', 'left')
            ->where('ev.deleted_at IS NULL', null, false)
            ->groupBy('ev.id')
            ->orderBy('ev.start_at', 'DESC')
            ->get()
            ->getResultArray();

        // Templates for dropdowns
        $learningTemplates = $this->templateModel->where('is_active', 1)->where('source_type', 'learning')->findAll();
        $eventTemplates    = $this->templateModel->where('is_active', 1)->where('source_type', 'event')->findAll();

        return view('dashboard/certificates/batch', [
            'title'             => 'Batch Certificate Generator | Dashboard BegawiPBJ',
            'pageTitle'         => 'Batch Certificate Generator',
            'activeMenu'        => 'certificates',
            'activeSubmenu'     => 'batch',
            'activeTab'         => $activeTab,
            'learningClasses'   => $learningClasses,
            'events'            => $events,
            'learningTemplates' => $learningTemplates,
            'eventTemplates'    => $eventTemplates,
        ]);
    }

    /**
     * POST /dashboard/sertifikat/batch/learning
     */
    public function processLearningBatch()
    {
        $classId    = (int) $this->request->getPost('class_id');
        $templateId = (int) $this->request->getPost('template_id');
        $actorId    = (int) session()->get('user_id');

        if ($classId < 1) {
            return redirect()->to(site_url('dashboard/sertifikat/batch?tab=learning'))
                ->with('error', 'Silakan pilih kelas pembelajaran terlebih dahulu.');
        }

        try {
            $enrollments = $this->db->table('enrollments e')
                ->distinct()
                ->select('e.id, e.user_id')
                ->join('learning_progress lp', 'lp.enrollment_id = e.id', 'left')
                ->where('e.class_id', $classId)
                ->groupStart()
                    ->where('e.status', 'completed')
                    ->orWhere('lp.progress_percent >=', 100.0)
                ->groupEnd()
                ->get()
                ->getResultArray();

            if (empty($enrollments)) {
                return redirect()->to(site_url('dashboard/sertifikat/batch?tab=learning'))
                    ->with('warning', 'Tidak ada peserta yang memenuhi syarat kelulusan di kelas ini.');
            }

            $totalTarget  = count($enrollments);
            $successCount = 0;
            $skippedCount = 0;
            $failedCount  = 0;
            $options      = $templateId > 0 ? ['template_id' => $templateId] : [];

            foreach ($enrollments as $e) {
                $enrId = (int) $e['id'];

                $exists = $this->db->table('certificates')
                    ->where('source_type', 'learning_enrollment')
                    ->where('source_id', $enrId)
                    ->countAllResults();

                if ($exists > 0) {
                    $skippedCount++;
                    continue;
                }

                try {
                    $this->generator->generateDirect('learning_enrollment', $enrId, $actorId, $options);
                    $successCount++;
                } catch (\Throwable $err) {
                    $failedCount++;
                    log_message('error', '[BatchLearning] Error for enrollment #' . $enrId . ': ' . $err->getMessage());
                }
            }

            $summary = [
                'target'  => $totalTarget,
                'success' => $successCount,
                'skipped' => $skippedCount,
                'failed'  => $failedCount,
            ];

            session()->setFlashdata('batch_summary', $summary);

            return redirect()->to(site_url('dashboard/sertifikat/batch?tab=learning'))
                ->with('success', "Batch generator kelas selesai. Berhasil: {$successCount}, Dilewati: {$skippedCount}, Gagal: {$failedCount}.");
        } catch (\Throwable $e) {
            return redirect()->to(site_url('dashboard/sertifikat/batch?tab=learning'))
                ->with('error', 'Gagal memproses batch sertifikat kelas: ' . $e->getMessage());
        }
    }

    /**
     * POST /dashboard/sertifikat/batch/event
     */
    public function processEventBatch()
    {
        $eventId    = (int) $this->request->getPost('event_id');
        $templateId = (int) $this->request->getPost('template_id');
        $actorId    = (int) session()->get('user_id');

        if ($eventId < 1) {
            return redirect()->to(site_url('dashboard/sertifikat/batch?tab=event'))
                ->with('error', 'Silakan pilih event terlebih dahulu.');
        }

        try {
            $attendees = $this->db->table('event_registrations er')
                ->select('er.id, er.user_id')
                ->join('attendances a', 'a.event_registration_id = er.id')
                ->where('er.event_id', $eventId)
                ->where('er.status', 'registered')
                ->where('a.status', 'present')
                ->get()
                ->getResultArray();

            if (empty($attendees)) {
                return redirect()->to(site_url('dashboard/sertifikat/batch?tab=event'))
                    ->with('warning', 'Tidak ada peserta yang terkonfirmasi hadir (status: present) pada event ini.');
            }

            $totalTarget  = count($attendees);
            $successCount = 0;
            $skippedCount = 0;
            $failedCount  = 0;
            $options      = $templateId > 0 ? ['template_id' => $templateId] : [];

            foreach ($attendees as $att) {
                $regId = (int) $att['id'];

                $exists = $this->db->table('certificates')
                    ->where('source_type', 'event_registration')
                    ->where('source_id', $regId)
                    ->countAllResults();

                if ($exists > 0) {
                    $skippedCount++;
                    continue;
                }

                try {
                    $this->generator->generateDirect('event_registration', $regId, $actorId, $options);
                    $successCount++;
                } catch (\Throwable $err) {
                    $failedCount++;
                    log_message('error', '[BatchEvent] Error for reg #' . $regId . ': ' . $err->getMessage());
                }
            }

            $summary = [
                'target'  => $totalTarget,
                'success' => $successCount,
                'skipped' => $skippedCount,
                'failed'  => $failedCount,
            ];

            session()->setFlashdata('batch_summary', $summary);

            return redirect()->to(site_url('dashboard/sertifikat/batch?tab=event'))
                ->with('success', "Batch generator event selesai. Berhasil: {$successCount}, Dilewati: {$skippedCount}, Gagal: {$failedCount}.");
        } catch (\Throwable $e) {
            return redirect()->to(site_url('dashboard/sertifikat/batch?tab=event'))
                ->with('error', 'Gagal memproses batch sertifikat event: ' . $e->getMessage());
        }
    }
}

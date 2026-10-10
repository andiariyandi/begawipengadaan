<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\CertificateEligibilityService;
use App\Services\CertificateGenerator;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;

/**
 * CertificateEventController
 *
 * Mengelola sertifikat event/agenda:
 * Hirarki: Event / Agenda -> Peserta Terdaftar -> Presensi / Kehadiran (Hadir / Present) -> Sertifikat.
 */
class CertificateEventController extends BaseController
{
    private ConnectionInterface $db;
    private CertificateEligibilityService $eligibility;
    private CertificateGenerator $generator;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db           = db_connect();
        $this->eligibility  = new CertificateEligibilityService();
        $this->generator    = new CertificateGenerator();
        $this->auditService = new AuditService();
    }

    /**
     * GET /dashboard/sertifikat/event
     * Menampilkan daftar event beserta ringkasan peserta hadir dan sertifikat terbit.
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status  = (string) $this->request->getGet('status');

        $builder = $this->db->table('events ev')
            ->select([
                'ev.id',
                'ev.title',
                'ev.slug',
                'ev.start_at',
                'ev.end_at',
                'ev.mode',
                'ev.location',
                'ev.certificate_enabled',
                'ev.status AS event_status',
                'COUNT(er.id) AS total_registered',
                'SUM(CASE WHEN a.status = "present" THEN 1 ELSE 0 END) AS total_present',
                'SUM(CASE WHEN c.id IS NOT NULL AND c.status = "issued" THEN 1 ELSE 0 END) AS total_certified',
            ])
            ->join('event_registrations er', 'er.event_id = ev.id', 'left')
            ->join('attendances a', 'a.event_registration_id = er.id', 'left')
            ->join('certificates c', 'c.source_type = "event_registration" AND c.source_id = er.id', 'left')
            ->where('ev.deleted_at IS NULL', null, false)
            ->groupBy('ev.id')
            ->orderBy('ev.start_at', 'DESC');

        if ($keyword !== '') {
            $builder->like('ev.title', $keyword);
        }

        if ($status !== '' && $status !== 'all') {
            $builder->where('ev.status', $status);
        }

        $events = $builder->get()->getResultArray();

        return view('dashboard/certificates/events/select', [
            'title'         => 'Event Certificate | Dashboard BegawiPBJ',
            'pageTitle'     => 'Event / Agenda Certificate',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'event',
            'events'        => $events,
            'keyword'       => $keyword,
            'statusFilter'  => $status ?: 'all',
        ]);
    }

    /**
     * GET /dashboard/sertifikat/event/{eventId}
     * Menampilkan preview peserta event, status presensi, dan status sertifikat.
     */
    public function detail(string $eventId)
    {
        $eventIdInt = (int) $eventId;
        $event      = $this->db->table('events')->where('id', $eventIdInt)->get()->getRowArray();
        if (! $event) {
            return $this->notFound('Event tidak ditemukan.');
        }

        $attendanceFilter = (string) $this->request->getGet('attendance');

        $builder = $this->db->table('event_registrations er')
            ->select([
                'er.id          AS registration_id',
                'er.user_id',
                'er.status      AS registration_status',
                'er.created_at  AS registered_at',
                'u.name         AS user_name',
                'u.email        AS user_email',
                'a.status       AS attendance_status',
                'a.created_at   AS attended_at',
                'c.id           AS certificate_id',
                'c.certificate_number',
                'c.status       AS certificate_status',
                'c.issued_at    AS certificate_issued_at',
            ])
            ->join('attendances a',   'a.event_registration_id = er.id', 'left')
            ->join('users u',          'u.id = er.user_id',               'left')
            ->join('certificates c',   'c.source_type = "event_registration" AND c.source_id = er.id', 'left')
            ->where('er.event_id', $eventIdInt)
            ->where('er.status', 'registered')
            ->orderBy('u.name', 'ASC');

        if ($attendanceFilter === 'present') {
            $builder->where('a.status', 'present');
        } elseif ($attendanceFilter === 'absent') {
            $builder->groupStart()
                ->where('a.status IS NULL', null, false)
                ->orWhere('a.status !=', 'present')
                ->groupEnd();
        }

        $participants = $builder->get()->getResultArray();

        $eligibleCount = 0;
        foreach ($participants as &$p) {
            $isPresent = ($p['attendance_status'] === 'present');
            $hasCert   = (! empty($p['certificate_id']) && $p['certificate_status'] === 'issued');

            if ($hasCert) {
                $p['eligibility_status'] = 'certified';
                $p['eligibility_label']  = 'Sudah Terbit';
            } elseif ($isPresent) {
                $p['eligibility_status'] = 'eligible';
                $p['eligibility_label']  = 'Layak (Hadir)';
                $eligibleCount++;
            } else {
                $p['eligibility_status'] = 'not_eligible';
                $p['eligibility_label']  = 'Tidak Hadir';
            }
        }
        unset($p);

        return view('dashboard/certificates/events/preview', [
            'title'            => 'Peserta & Presensi Event | ' . ($event['title'] ?? 'Event'),
            'pageTitle'        => 'Sertifikat Event — ' . ($event['title'] ?? 'Event'),
            'activeMenu'       => 'certificates',
            'activeSubmenu'    => 'event',
            'event'            => $event,
            'eventId'          => $eventIdInt,
            'participants'     => $participants,
            'eligibleCount'    => $eligibleCount,
            'attendanceFilter' => $attendanceFilter ?: 'all',
        ]);
    }

    /**
     * POST /dashboard/sertifikat/event/issue/{registrationId}
     * Penerbitan single sertifikat untuk peserta event yang hadir.
     */
    public function issueSingle(string $registrationId)
    {
        $regIdInt = (int) $registrationId;
        $actorId  = (int) session()->get('user_id');

        try {
            // Validasi kehadiran
            $attendance = $this->db->table('attendances')
                ->where('event_registration_id', $regIdInt)
                ->where('status', 'present')
                ->get()
                ->getRowArray();

            if (! $attendance) {
                throw new RuntimeException('Peserta belum terkonfirmasi hadir (status presensi bukan present). Hanya peserta hadir yang berhak menerima sertifikat.');
            }

            $result = $this->generator->generateDirect('event_registration', $regIdInt, $actorId);

            return redirect()->back()->with(
                'success',
                "Sertifikat event berhasil diterbitkan dengan nomor: {$result['certificate_number']}."
            );
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menerbitkan sertifikat: ' . $e->getMessage());
        }
    }

    /**
     * POST /dashboard/sertifikat/event/{eventId}/batch
     * Penerbitan sertifikat batch untuk seluruh peserta yang hadir di event.
     */
    public function issueBatch(string $eventId)
    {
        $eventIdInt = (int) $eventId;
        $actorId    = (int) session()->get('user_id');

        try {
            // Cari peserta yang hadir (status = 'present')
            $attendees = $this->db->table('event_registrations er')
                ->select('er.id, er.user_id')
                ->join('attendances a', 'a.event_registration_id = er.id')
                ->where('er.event_id', $eventIdInt)
                ->where('er.status', 'registered')
                ->where('a.status', 'present')
                ->get()
                ->getResultArray();

            if (empty($attendees)) {
                return redirect()->back()->with('warning', 'Tidak ada peserta dengan presensi "Hadir" pada event ini.');
            }

            $successCount = 0;
            $skippedCount = 0;
            $failedCount  = 0;

            foreach ($attendees as $att) {
                $regId = (int) $att['id'];

                // Cek apakah sudah pernah terbit
                $exists = $this->db->table('certificates')
                    ->where('source_type', 'event_registration')
                    ->where('source_id', $regId)
                    ->countAllResults();

                if ($exists > 0) {
                    $skippedCount++;
                    continue;
                }

                try {
                    $this->generator->generateDirect('event_registration', $regId, $actorId);
                    $successCount++;
                } catch (\Throwable $err) {
                    $failedCount++;
                    log_message('error', '[BatchEventCert] Failed for reg #' . $regId . ': ' . $err->getMessage());
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
            return redirect()->back()->with('error', 'Gagal memproses batch sertifikat event: ' . $e->getMessage());
        }
    }

    // =============================================================
    // Compatibility methods
    // =============================================================

    public function select()
    {
        return $this->index();
    }

    public function preview(string $eventId)
    {
        return $this->detail($eventId);
    }

    public function generate(string $eventId)
    {
        return $this->issueBatch($eventId);
    }

    private function notFound(string $msg)
    {
        return view('dashboard/errors/404', [
            'title'      => 'Event tidak ditemukan',
            'pageTitle'  => 'Event tidak ditemukan',
            'activeMenu' => 'certificates',
            'message'    => $msg,
        ]);
    }
}

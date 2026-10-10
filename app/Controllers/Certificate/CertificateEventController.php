<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\CertificateEligibilityService;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;

/**
 * Pengajuan sertifikat EVENT batch.
 *
 * Menampilkan event yang eligible untuk pengajuan, preview peserta,
 * dan eksekusi generate yang HANYA membuat approvals (pending) —
 * bukan certificates. Approvals kemudian diproses di
 * CertificateApprovalController::issue() oleh approver.
 *
 * Flow:
 *   Event selesai
 *     → select()           : pilih event
 *     → preview($eventId)  : peserta eligible
 *     → generate($eventId) : buat approvals (pending)
 *     → admin/operator approve di CertificateApprovalController::issue()
 *     → CertificateGenerator bikin certificates
 */
class CertificateEventController extends BaseController
{
    private ConnectionInterface $db;
    private CertificateEligibilityService $eligibility;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db = db_connect();
        $this->eligibility  = new CertificateEligibilityService();
        $this->auditService = new AuditService();
    }

    /**
     * GET /dashboard/sertifikat/pengajuan-event
     */
    public function select()
    {
        $events = $this->eligibility->listEligibleEvents();

        // Tandai event yang sudah punya approval aktif (supaya admin tidak
        // double-submit untuk event yang sama).
        if (! empty($events)) {
            $ids = array_map(static fn ($r) => (int) $r['id'], $events);
            $approvalStats = $this->db->table('approvals a')
                ->select('a.resource_id, COUNT(*) AS pending_count')
                ->join('event_registrations er', 'er.id = a.resource_id', 'left')
                ->where('a.resource_type', 'certificate')
                ->where('a.requested_action', CertificateEligibilityService::APPROVAL_ACTION)
                ->where('a.status', 'pending')
                ->whereIn('er.event_id', $ids)
                ->groupBy('a.resource_id')
                ->get()
                ->getResultArray();

            $pendingByEventReg = [];
            foreach ($approvalStats as $row) {
                $pendingByEventReg[(int) $row['resource_id']] = (int) $row['pending_count'];
            }

            // Hitung per event: jumlah approval pending
            $pendingByEvent = [];
            $regs = $this->db->table('event_registrations')
                ->select('id, event_id')
                ->whereIn('event_id', $ids)
                ->get()
                ->getResultArray();
            foreach ($regs as $r) {
                $eventId = (int) $r['event_id'];
                if (! isset($pendingByEvent[$eventId])) {
                    $pendingByEvent[$eventId] = 0;
                }
                $pendingByEvent[$eventId] += (int) ($pendingByEventReg[(int) $r['id']] ?? 0);
            }

            foreach ($events as &$ev) {
                $ev['pending_approval_count'] = $pendingByEvent[(int) $ev['id']] ?? 0;
            }
            unset($ev);
        }

        return view('dashboard/certificates/events/select', [
            'title'         => 'Pengajuan Sertifikat Event | Dashboard BegawiPBJ',
            'pageTitle'     => 'Pengajuan Sertifikat Event',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'event-approval',
            'events'        => $events,
        ]);
    }

    /**
     * GET /dashboard/sertifikat/pengajuan-event/{eventId}/preview
     */
    public function preview(string $eventId)
    {
        $eventId = (int) $eventId;
        $event   = $this->db->table('events')->where('id', $eventId)->get()->getRowArray();
        if (! $event) {
            return $this->notFound('Event tidak ditemukan.');
        }

        $participants = $this->db->table('event_registrations er')
            ->select([
                'er.id          AS registration_id',
                'er.user_id',
                'er.status      AS registration_status',
                'u.name         AS user_name',
                'u.email        AS user_email',
                'a.status       AS attendance_status',
                $this->db->raw("CASE WHEN a.status = 'present' THEN 1 ELSE 0 END AS eligible"),
                $this->db->raw("(SELECT COUNT(*) FROM approvals ap WHERE ap.resource_type = 'certificate' AND ap.resource_id = er.id AND ap.status = 'pending') AS pending_approval"),
                $this->db->raw("(SELECT COUNT(*) FROM certificates c WHERE c.source_type = 'event_registration' AND c.source_id = er.id AND c.status = 'issued') AS existing_certificate"),
            ])
            ->join('attendances a', 'a.event_registration_id = er.id', 'left')
            ->join('users u',        'u.id = er.user_id',          'left')
            ->where('er.event_id', $eventId)
            ->where('er.status', 'registered')
            ->orderBy('u.name', 'ASC')
            ->get()
            ->getResultArray();

        return view('dashboard/certificates/events/preview', [
            'title'         => 'Preview Peserta Event | Dashboard BegawiPBJ',
            'pageTitle'     => 'Preview Peserta Event',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'event-approval',
            'event'         => $event,
            'eventId'       => $eventId,
            'participants'  => $participants,
        ]);
    }

    /**
     * POST /dashboard/sertifikat/pengajuan-event/{eventId}/generate
     *
     * Hasil: HANYA approvals (status pending). Tidak membuat baris
     * certificates. Approvals akan muncul di antrean
     * CertificateApprovalController::index().
     */
    public function generate(string $eventId)
    {
        $eventId  = (int) $eventId;
        $actorId  = (int) session()->get('user_id');
        $now      = date('Y-m-d H:i:s');

        try {
            if ($actorId < 1 || session()->get('isLoggedIn') !== true) {
                throw new RuntimeException('Akses ditolak.');
            }

            $created = $this->eligibility->scanEventParticipantsEligible($eventId, $actorId);

            $this->auditService->log($actorId, 'approval.batch_created', 'approval', 'event', $eventId, [
                'resource_type'    => 'certificate',
                'requested_action' => CertificateEligibilityService::APPROVAL_ACTION,
                'count'            => count($created),
            ]);

            return redirect()->to(site_url('dashboard/sertifikat/approval'))
                ->with(
                    'success',
                    count($created) . ' approval sertifikat berhasil dibuat dan menunggu persetujuan.'
                );
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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

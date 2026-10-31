<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use App\Models\Communication\EventModel;
use App\Services\AuditService;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Exceptions\PageNotFoundException;

class EventPublicController extends BaseController
{
    private ConnectionInterface $db;
    private EventModel $eventModel;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db           = db_connect();
        $this->eventModel   = new EventModel();
        $this->auditService = new AuditService();
    }

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        if ($this->request->getMethod() === 'GET' && ! $this->request->isAJAX() && ! is_cli()) {
            try {
                (new \App\Services\VisitorStatisticService())->recordHit('event');
            } catch (\Throwable) {
            }
        }
    }

    /**
     * Main Event & Agenda index with filters, pagination, and KPI ribbon
     */
    public function index(): string
    {
        return $this->renderCatalog('all');
    }

    /**
     * Upcoming agenda view
     */
    public function agenda(): string
    {
        return $this->renderCatalog('upcoming');
    }

    /**
     * Monthly calendar / timeline overview
     */
    public function calendar(): string
    {
        return $this->renderCatalog('calendar');
    }

    /**
     * Shared catalog renderer with full pagination and filtering
     */
    private function renderCatalog(string $viewMode = 'all'): string
    {
        $keyword  = trim((string) ($this->request->getGet('q') ?: ''));
        $type     = trim((string) ($this->request->getGet('tipe') ?: $this->request->getGet('type') ?: ''));
        $mode     = trim((string) ($this->request->getGet('mode') ?: ''));
        $timeline = trim((string) ($this->request->getGet('timeline') ?: ''));
        $page     = max(1, (int) ($this->request->getGet('page') ?: 1));
        $perPage  = 6;

        if ($viewMode === 'upcoming' && empty($timeline)) {
            $timeline = 'upcoming';
        }

        $now = date('Y-m-d H:i:s');

        // Base builder for events
        $builder = $this->db->table('events e')
            ->select('e.*, COALESCE(u.name, u.username, "Biro PBJ Setda Lampung") AS creator_name, fa.path AS cover_path')
            ->join('users u', 'u.id = e.created_by', 'left')
            ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
            ->where('e.deleted_at', null)
            ->where('e.visibility', 'public');

        // Type filter
        if (! empty($type)) {
            $builder->where('e.type', $type);
        }

        // Mode filter (online, offline, hybrid)
        if (! empty($mode)) {
            $builder->where('e.mode', $mode);
        }

        // Timeline filter
        if ($timeline === 'upcoming') {
            $builder->where('e.end_at >=', $now);
        } elseif ($timeline === 'past') {
            $builder->where('e.end_at <', $now);
        }

        // Keyword filter
        if (! empty($keyword)) {
            $builder->groupStart()
                ->like('e.title', $keyword)
                ->orLike('e.summary', $keyword)
                ->orLike('e.description', $keyword)
                ->orLike('e.location', $keyword)
                ->groupEnd();
        }

        // Count total matching items
        $countBuilder = clone $builder;
        $totalItems   = (int) $countBuilder->countAllResults(false);
        $totalPages   = max(1, (int) ceil($totalItems / $perPage));
        $currentPage  = min($page, $totalPages);
        $offset       = ($currentPage - 1) * $perPage;

        // Sorting: upcoming events first by start_at ASC, past by start_at DESC
        if ($timeline === 'past') {
            $builder->orderBy('e.start_at', 'DESC');
        } else {
            $builder->orderBy('CASE WHEN e.end_at >= "' . $now . '" THEN 0 ELSE 1 END', 'ASC', false)
                ->orderBy('e.start_at', 'ASC');
        }

        $rawItems = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Format event items
        $items = array_map(function ($row) use ($now) {
            $start = ! empty($row['start_at']) ? strtotime($row['start_at']) : time();
            $end   = ! empty($row['end_at']) ? strtotime($row['end_at']) : $start;

            $regCount = $this->db->table('event_registrations')
                ->where('event_id', $row['id'])
                ->whereIn('status', ['registered', 'attended', 'confirmed', 'promoted'])
                ->countAllResults();

            $capacity    = (int) ($row['capacity'] ?? 0);
            $isUnlimited = ($capacity <= 0);
            $remaining   = $isUnlimited ? null : max(0, $capacity - $regCount);
            $isPast      = strtotime($row['end_at']) < strtotime($now);

            return [
                'id'                   => (int) $row['id'],
                'title'                => $row['title'],
                'slug'                 => $row['slug'],
                'type'                 => $row['type'],
                'type_label'           => $this->formatTypeLabel($row['type']),
                'mode'                 => $row['mode'],
                'mode_label'           => $this->formatModeLabel($row['mode']),
                'status'               => $row['status'],
                'status_label'         => $this->formatStatusLabel($row['status'], $isPast),
                'summary'              => $row['summary'] ?: mb_strimwidth(strip_tags((string) $row['description']), 0, 150, '...'),
                'description'          => $row['description'],
                'start_at'             => $row['start_at'],
                'end_at'               => $row['end_at'],
                'start_date'           => date('d M Y', $start),
                'start_day'            => date('d', $start),
                'start_month'          => date('M', $start),
                'start_year'           => date('Y', $start),
                'time_range'           => date('H:i', $start) . ' - ' . date('H:i', $end) . ' WIB',
                'location'             => $row['location'] ?: ($row['mode'] === 'online' ? 'Daring via Zoom / Platform Online' : 'Bandar Lampung'),
                'meeting_url'          => $row['meeting_url'] ?? '',
                'capacity'             => $capacity,
                'is_unlimited'         => $isUnlimited,
                'registered'           => $regCount,
                'remaining'            => $remaining,
                'is_past'              => $isPast,
                'waitlist_enabled'     => (int) ($row['waitlist_enabled'] ?? 0),
                'certificate_enabled'  => (int) ($row['certificate_enabled'] ?? 0),
                'registration_open_at' => $row['registration_open_at'],
                'registration_close_at' => $row['registration_close_at'],
                'is_featured'          => (int) ($row['is_featured'] ?? 0),
                'cover_url'            => ! empty($row['cover_file_id']) ? site_url('file/' . (int) $row['cover_file_id']) : (! empty($row['cover_path']) ? (str_starts_with($row['cover_path'], 'http') ? $row['cover_path'] : base_url($row['cover_path'])) : null),
            ];
        }, $rawItems);

        // Featured Slider Events (respect spotlight_mode & spotlight_limit settings)
        $sliderEvents = [];
        $spotlightMode  = (string) (setting('event.spotlight_mode') ?? 'all');
        $spotlightLimit = (int) (setting('event.spotlight_limit') ?? 5);
        if ($spotlightLimit < 3) {
            $spotlightLimit = 3;
        } elseif ($spotlightLimit > 15) {
            $spotlightLimit = 15;
        }
        if ($currentPage === 1 && empty($keyword) && empty($type) && empty($mode)) {
            $selectCols = 'e.*, COALESCE(u.name, u.username, "Biro PBJ Setda Lampung") AS creator_name, fa.path AS cover_path';

            // 1. Priority 1: Manual active slider events (carousel_enabled = 1)
            $manualBuilder = $this->db->table('events e')
                ->select($selectCols)
                ->join('users u', 'u.id = e.created_by', 'left')
                ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
                ->where('e.deleted_at', null)
                ->where('e.visibility', 'public')
                ->where('e.carousel_enabled', 1);

            if ($spotlightMode === 'upcoming') {
                $manualBuilder->where('e.end_at >=', $now);
            } elseif ($spotlightMode !== 'all' && ! empty($spotlightMode) && $spotlightMode !== 'featured') {
                $manualBuilder->where('e.type', $spotlightMode);
            }

            $manualEvents = $manualBuilder
                ->orderBy('CASE WHEN e.carousel_order IS NULL THEN 9999 ELSE e.carousel_order END', 'ASC', false)
                ->orderBy('e.id', 'DESC')
                ->limit($spotlightLimit)
                ->get()->getResultArray();

            $sliderRaw = $manualEvents;

            // 2. Priority 2: Fill remaining quota with newest events according to mode
            if (count($sliderRaw) < $spotlightLimit) {
                $remaining = $spotlightLimit - count($sliderRaw);
                $usedIds   = array_column($sliderRaw, 'id');

                $fillerBuilder = $this->db->table('events e')
                    ->select($selectCols)
                    ->join('users u', 'u.id = e.created_by', 'left')
                    ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
                    ->where('e.deleted_at', null)
                    ->where('e.visibility', 'public');

                if (! empty($usedIds)) {
                    $fillerBuilder->whereNotIn('e.id', $usedIds);
                }

                if ($spotlightMode === 'upcoming') {
                    $fillerBuilder->where('e.end_at >=', $now);
                } elseif ($spotlightMode !== 'all' && ! empty($spotlightMode) && $spotlightMode !== 'featured') {
                    $fillerBuilder->where('e.type', $spotlightMode);
                }

                $fillerEvents = $fillerBuilder
                    ->orderBy('CASE WHEN e.status = "open_registration" THEN 0 WHEN e.status = "coming_soon" THEN 1 ELSE 2 END', 'ASC', false)
                    ->orderBy('e.start_at', 'ASC')
                    ->orderBy('e.id', 'DESC')
                    ->limit($remaining)
                    ->get()->getResultArray();

                $sliderRaw = array_merge($sliderRaw, $fillerEvents);
            }

            // Fallback to all published events if specific filter returned empty
            if (empty($sliderRaw) && ! empty($rawItems)) {
                $sliderRaw = array_slice($rawItems, 0, min($spotlightLimit, count($rawItems)));
            }

            $sliderEvents = array_map(function ($row) use ($now) {
                $start = ! empty($row['start_at']) ? strtotime($row['start_at']) : time();
                $end   = ! empty($row['end_at']) ? strtotime($row['end_at']) : $start;

                $regCount = $this->db->table('event_registrations')
                    ->where('event_id', $row['id'])
                    ->whereIn('status', ['registered', 'attended', 'confirmed', 'promoted'])
                    ->countAllResults();

                $capacity    = (int) ($row['capacity'] ?? 0);
                $isUnlimited = ($capacity <= 0);
                $remaining   = $isUnlimited ? null : max(0, $capacity - $regCount);
                $isPast      = strtotime($row['end_at']) < strtotime($now);

                $coverUrl = null;
                if (! empty($row['cover_file_id'])) {
                    $coverUrl = site_url('file/' . (int) $row['cover_file_id']);
                } elseif (! empty($row['cover_path'])) {
                    $coverUrl = str_starts_with($row['cover_path'], 'http') ? $row['cover_path'] : base_url($row['cover_path']);
                }

                return [
                    'id'                   => (int) $row['id'],
                    'title'                => $row['title'],
                    'slug'                 => $row['slug'],
                    'type'                 => $row['type'],
                    'type_label'           => $this->formatTypeLabel($row['type']),
                    'mode'                 => $row['mode'],
                    'mode_label'           => $this->formatModeLabel($row['mode']),
                    'status'               => $row['status'],
                    'status_label'         => $this->formatStatusLabel($row['status'], $isPast),
                    'summary'              => $row['summary'] ?: mb_strimwidth(strip_tags((string) $row['description']), 0, 150, '...'),
                    'description'          => $row['description'],
                    'start_at'             => $row['start_at'],
                    'end_at'               => $row['end_at'],
                    'start_date'           => date('d M Y', $start),
                    'start_day'            => date('d', $start),
                    'start_month'          => date('M', $start),
                    'start_year'           => date('Y', $start),
                    'time_range'           => date('H:i', $start) . ' - ' . date('H:i', $end) . ' WIB',
                    'location'             => $row['location'] ?: ($row['mode'] === 'online' ? 'Daring via Zoom / Platform Online' : 'Bandar Lampung'),
                    'meeting_url'          => $row['meeting_url'] ?? '',
                    'capacity'             => $capacity,
                    'is_unlimited'         => $isUnlimited,
                    'registered'           => $regCount,
                    'remaining'            => $remaining,
                    'is_past'              => $isPast,
                    'waitlist_enabled'     => (int) ($row['waitlist_enabled'] ?? 0),
                    'certificate_enabled'  => (int) ($row['certificate_enabled'] ?? 0),
                    'registration_open_at' => $row['registration_open_at'],
                    'registration_close_at' => $row['registration_close_at'],
                    'cover_url'            => $coverUrl,
                ];
            }, $sliderRaw);
        }

        $spotlightEvent = ! empty($sliderEvents) ? $sliderEvents[0] : null;

        // Live KPI Metrics (strictly authentic database counts, zero dummy data)
        $stats = [
            'total_events'      => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->countAllResults(),
            'upcoming_events'   => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->where('end_at >=', $now)->countAllResults(),
            'open_registration' => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->where('status', 'open_registration')->countAllResults(),
            'total_attendees'   => (int) $this->db->table('event_registrations')->whereIn('status', ['registered', 'attended', 'confirmed', 'promoted'])->countAllResults(),
        ];

        // Type counts for all 8 supported dashboard event types
        $allEventTypes = ['webinar', 'bimtek', 'workshop', 'sosialisasi', 'seminar', 'pelatihan', 'rakor', 'klinik'];
        $typeCounts = [];
        foreach ($allEventTypes as $tKey) {
            $typeCounts[$tKey] = (int) $this->db->table('events')
                ->where('deleted_at', null)
                ->where('visibility', 'public')
                ->where('type', $tKey)
                ->countAllResults();
        }

        // Upcoming Top 3 Events for Sidebar
        $upcomingSidebar = $this->db->table('events e')
            ->select('e.title, e.slug, e.start_at, e.mode, e.type, e.location, e.certificate_enabled')
            ->where('e.deleted_at', null)
            ->where('e.visibility', 'public')
            ->where('e.end_at >=', $now)
            ->orderBy('e.start_at', 'ASC')
            ->limit(3)
            ->get()->getResultArray();

        // Calendar grouped events for calendar tab view
        $calendarGrouped = [];
        if ($viewMode === 'calendar') {
            $allCalendarEvents = $this->db->table('events e')
                ->select('e.id, e.title, e.slug, e.start_at, e.end_at, e.type, e.mode, e.location, e.status, e.certificate_enabled')
                ->where('e.deleted_at', null)
                ->where('e.visibility', 'public')
                ->orderBy('e.start_at', 'ASC')
                ->get()->getResultArray();

            foreach ($allCalendarEvents as $ce) {
                $monthKey = date('F Y', strtotime($ce['start_at']));
                $calendarGrouped[$monthKey][] = $ce;
            }
        }

        $pageTitle = match ($viewMode) {
            'upcoming' => 'Agenda PBJ Mendatang',
            'calendar' => 'Kalender Kegiatan PBJ',
            default    => 'Event & Agenda PBJ Lampung',
        };

        return view('landing/pages/events', [
            'title'            => $pageTitle . ' | BegawiPengadaan Provinsi Lampung',
            'metaDescription'  => 'Jadwal bimbingan teknis, webinar nasional, workshop probity audit, dan agenda kolaborasi pengadaan barang dan jasa Pemerintah Provinsi Lampung.',
            'pageTitle'        => $pageTitle,
            'viewMode'         => $viewMode,
            'items'            => $items,
            'spotlightEvent'   => $spotlightEvent,
            'sliderEvents'     => $sliderEvents,
            'totalItems'       => $totalItems,
            'currentPage'      => $currentPage,
            'totalPages'       => $totalPages,
            'perPage'          => $perPage,
            'keyword'          => $keyword,
            'selectedType'     => $type,
            'selectedMode'     => $mode,
            'selectedTimeline' => $timeline,
            'stats'            => $stats,
            'typeCounts'       => $typeCounts,
            'upcomingSidebar'  => $upcomingSidebar,
            'calendarGrouped'  => $calendarGrouped,
        ]);
    }

    /**
     * Event Detail page with countdown, rundown, speaker info, and registration widget
     */
    public function detail(string $slug): string
    {
        $event = $this->db->table('events e')
            ->select('e.*, COALESCE(u.name, u.username, "Biro Pengadaan Barang dan Jasa Provinsi Lampung") AS creator_name, fa.path AS cover_path')
            ->join('users u', 'u.id = e.created_by', 'left')
            ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
            ->where('e.slug', $slug)
            ->where('e.visibility', 'public')
            ->where('e.deleted_at', null)
            ->get()->getRowArray();

        if (! $event) {
            throw PageNotFoundException::forPageNotFound('Event atau agenda tidak ditemukan: ' . esc($slug));
        }

        $now       = time();
        $startTime = ! empty($event['start_at']) ? strtotime($event['start_at']) : $now;
        $endTime   = ! empty($event['end_at']) ? strtotime($event['end_at']) : $startTime;
        $isPast    = $endTime < $now;
        $isOngoing = ($startTime <= $now && $endTime >= $now);

        // Registration count
        $regCount = $this->db->table('event_registrations')
            ->where('event_id', $event['id'])
            ->whereIn('status', ['registered', 'attended', 'confirmed', 'promoted'])
            ->countAllResults();

        $capacity    = (int) ($event['capacity'] ?? 0);
        $isUnlimited = ($capacity <= 0);
        $remaining   = $isUnlimited ? null : max(0, $capacity - $regCount);

        // Check if user is logged in and already registered
        $userId         = (int) session()->get('user_id');
        $isRegistered   = false;
        $userRegData    = null;
        $userAttendance = null;

        if ($userId > 0) {
            $userRegData = $this->db->table('event_registrations')
                ->where(['event_id' => $event['id'], 'user_id' => $userId])
                ->get()->getRowArray();
            $isRegistered = ! empty($userRegData) && in_array($userRegData['status'], ['registered', 'attended', 'confirmed', 'promoted', 'waitlisted', 'waitlist']);

            if ($userRegData) {
                $userAttendance = $this->db->table('attendances')
                    ->where('event_registration_id', $userRegData['id'])
                    ->get()->getRowArray();
            }
        }

        // Registration window checks
        $regOpenAt  = ! empty($event['registration_open_at']) ? strtotime($event['registration_open_at']) : null;
        $regCloseAt = ! empty($event['registration_close_at']) ? strtotime($event['registration_close_at']) : null;

        $isRegOpen = true;
        if ($regOpenAt && $now < $regOpenAt) {
            $isRegOpen = false;
        }
        if ($regCloseAt && $now > $regCloseAt) {
            $isRegOpen = false;
        }
        if ($isPast || $event['status'] === 'cancelled') {
            $isRegOpen = false;
        }

        // Assessment & Self-Paced Learning Configuration Check
        $assessmentSvc = new \App\Services\EventAssessmentService();
        $assessmentConfig = $assessmentSvc->getAssessmentConfig((int) $event['id']);
        $selfPaced = $assessmentConfig['self_paced'] ?? [];
        $isSelfPacedEnabled = ! empty($selfPaced['enabled']);

        // Check if self-paced learning is valid / extended past real schedule
        $isSelfPacedOpen = false;
        if ($isSelfPacedEnabled && $isPast) {
            if (empty($selfPaced['extended_until']) || strtotime($selfPaced['extended_until']) >= $now) {
                $isSelfPacedOpen = true;
                $isRegOpen = true; // Open registration for self-paced learner
            }
        }

        // Attendance / Presensi session active check
        $attOpenAt      = ! empty($event['attendance_open_at']) ? strtotime($event['attendance_open_at']) : null;
        $attCloseAt     = ! empty($event['attendance_close_at']) ? strtotime($event['attendance_close_at']) : null;
        $isPresensiOpen = false;
        if (! empty($event['attendance_code']) && $attOpenAt !== null) {
            if ($attOpenAt <= $now && ($attCloseAt === null || $attCloseAt > $now)) {
                $isPresensiOpen = true;
            }
        }

        // Related upcoming events
        $relatedEvents = $this->db->table('events e')
            ->select('e.id, e.title, e.slug, e.start_at, e.end_at, e.type, e.mode, e.location, e.certificate_enabled, fa.path AS cover_path')
            ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
            ->where('e.deleted_at', null)
            ->where('e.visibility', 'public')
            ->where('e.id !=', $event['id'])
            ->where('e.end_at >=', date('Y-m-d H:i:s'))
            ->orderBy('e.start_at', 'ASC')
            ->limit(3)
            ->get()->getResultArray();

        return view('landing/pages/event_detail', [
            'title'           => $event['title'] . ' | Event PBJ Lampung',
            'metaDescription' => $event['summary'] ?: ('Jadwal dan pendaftaran resmi: ' . $event['title']),
            'pageTitle'       => 'Detail Event & Agenda',
            'event'           => $event,
            'typeLabel'       => $this->formatTypeLabel($event['type']),
            'modeLabel'       => $this->formatModeLabel($event['mode']),
            'statusLabel'     => $this->formatStatusLabel($event['status'], $isPast),
            'startTime'       => $startTime,
            'endTime'         => $endTime,
            'isPast'          => $isPast,
            'isOngoing'       => $isOngoing,
            'registeredCount' => $regCount,
            'capacity'        => $capacity,
            'isUnlimited'     => $isUnlimited,
            'remainingSeats'  => $remaining,
            'isRegistered'    => $isRegistered,
            'userRegData'     => $userRegData,
            'userAttendance'  => $userAttendance,
            'isRegOpen'       => $isRegOpen,
            'regOpenAt'       => $regOpenAt,
            'regCloseAt'      => $regCloseAt,
            'isPresensiOpen'  => $isPresensiOpen,
            'attOpenAt'       => $attOpenAt,
            'attCloseAt'      => $attCloseAt,
            'relatedEvents'   => $relatedEvents,
            'shareUrl'        => site_url('event/' . $event['slug']),
            'isSelfPacedOpen' => $isSelfPacedOpen,
            'selfPaced'       => $selfPaced,
            'assessmentConfig'=> $assessmentConfig,
        ]);
    }

    /**
     * Handle user event registration
     */
    public function register(string $slug)
    {
        $userId = (int) session()->get('user_id');
        if ($userId < 1 || session()->get('isLoggedIn') !== true) {
            session()->set('redirect_url', site_url('event/' . $slug));
            return redirect()->to(site_url('masuk'))->with('error', 'Silakan masuk ke akun Anda terlebih dahulu untuk mendaftar kegiatan ini.');
        }

        $event = $this->db->table('events')->where(['slug' => $slug, 'visibility' => 'public', 'deleted_at' => null])->get()->getRowArray();
        if (! $event) {
            throw PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }

        // H-3: Cegah pendaftaran jika event dibatalkan, draft, atau diarsipkan
        if (in_array(($event['status'] ?? ''), ['cancelled', 'draft', 'archived'], true)) {
            $statusLabel = ($event['status'] === 'cancelled') ? 'telah dibatalkan' : 'tidak dibuka untuk umum';
            return redirect()->back()->with('error', "Pendaftaran tidak dapat diproses karena kegiatan ini {$statusLabel}.");
        }

        // Check if self-paced learning is active for past events
        $assessmentSvc = new \App\Services\EventAssessmentService();
        $assessmentConfig = $assessmentSvc->getAssessmentConfig((int) $event['id']);
        $selfPaced = $assessmentConfig['self_paced'] ?? [];
        $isSelfPacedActive = false;
        if (! empty($selfPaced['enabled']) && strtotime($event['end_at']) < time()) {
            if (empty($selfPaced['extended_until']) || strtotime($selfPaced['extended_until']) >= time()) {
                $isSelfPacedActive = true;
            }
        }

        // Check if registration window is closed (only enforce for standard live events)
        if (! $isSelfPacedActive) {
            if (! empty($event['registration_close_at']) && strtotime($event['registration_close_at']) < time()) {
                return redirect()->back()->with('error', 'Pendaftaran event ini sudah ditutup.');
            }
            if (! empty($event['registration_open_at']) && strtotime($event['registration_open_at']) > time()) {
                return redirect()->back()->with('error', 'Pendaftaran event ini belum dibuka.');
            }
            if (strtotime($event['end_at']) < time()) {
                return redirect()->back()->with('error', 'Kegiatan ini telah selesai diselenggarakan.');
            }
        }

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        // Check if already registered
        $existing = $this->db->table('event_registrations')
            ->where(['event_id' => $event['id'], 'user_id' => $userId])
            ->get()->getRowArray();

        if ($existing) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Anda sudah terdaftar pada kegiatan ini.');
        }

        // M-1: Check capacity & waitlist under transaction
        $currentCount = $this->db->table('event_registrations')
            ->where('event_id', $event['id'])
            ->whereIn('status', ['registered', 'attended', 'confirmed', 'promoted'])
            ->countAllResults();

        $capacity    = (int) ($event['capacity'] ?? 0);
        $isUnlimited = ($capacity <= 0);

        if (! $isUnlimited && $currentCount >= $capacity) {
            if (empty($event['waitlist_enabled'])) {
                $this->db->transRollback();
                return redirect()->back()->with('error', 'Mohon maaf, kuota peserta telah terpenuhi dan antrian waitlist tidak dibuka untuk kegiatan ini.');
            }
            $status = 'waitlisted'; // H-2: Gunakan 'waitlisted' sesuai database check constraint
        } else {
            $status = 'registered';
        }

        $this->db->table('event_registrations')->insert([
            'event_id'      => $event['id'],
            'user_id'       => $userId,
            'status'        => $status,
            'registered_at' => $now,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        $newRegId = (string) $this->db->insertID();

        // M-8: Audit log terpadu dalam alur transaksi pendaftaran event
        try {
            $this->auditService->log(
                $userId,
                'event.registered',
                'event_registration',
                $newRegId,
                null,
                ['event_id' => $event['id'], 'event_title' => $event['title'], 'status' => $status]
            );
        } catch (\Throwable $e) {
            log_message('warning', 'Event registration audit log error: {message}', ['message' => $e->getMessage()]);
        }

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal memproses pendaftaran event. Silakan coba kembali.');
        }

        // Kirim notifikasi personal ke peserta & shared ke panitia/operator event
        try {
            $userRow = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            $userName = $userRow['name'] ?? 'Peserta';
            $notifService = new \App\Services\NotificationService($this->db);

            $notifService->send(
                $userId,
                'event_registered',
                'Pendaftaran Event Terkonfirmasi',
                "Selamat! Pendaftaran Anda pada kegiatan '{$event['title']}' telah berhasil.",
                [
                    'event_id' => (int) $event['id'],
                    'url'      => site_url('workspace/peserta/event-saya'),
                ]
            );

            $notifService->sendShared(
                'shared:event:' . $event['id'],
                'event_registration_submitted',
                'Pendaftaran Peserta Event Baru',
                "Peserta {$userName} telah mendaftar pada kegiatan '{$event['title']}'.",
                [
                    'event_id'        => (int) $event['id'],
                    'registration_id' => $newRegId,
                    'user_id'         => $userId,
                    'url'             => route_to('workspace.operator.events.participants', (string) $event['id']),
                ],
                $userId
            );
        } catch (\Throwable) {
        }

        if ($isSelfPacedActive && $status === 'registered') {
            return redirect()->to(site_url('workspace/peserta/event-saya/' . $event['slug']))
                ->with('success', 'Pendaftaran berhasil! Anda kini terdaftar pada Mode Pembelajaran Mandiri. Simak rekaman sesi dan selesaikan kuis untuk klaim E-Sertifikat resmi.');
        }

        $msg = ($status === 'waitlisted')
            ? 'Kuota utama telah terpenuhi. Anda otomatis tercatat dalam antrian tunggu (waitlist) resmi.'
            : 'Selamat! Pendaftaran Anda berhasil dikonfirmasi. Simpan jadwal pada kalender Anda.';

        return redirect()->back()->with('message', $msg);
    }

    private function formatTypeLabel(string $type): string
    {
        return match (strtolower($type)) {
            'webinar'     => 'Webinar',
            'bimtek'      => 'Bimtek',
            'workshop'    => 'Workshop',
            'sosialisasi' => 'Sosialisasi Regulasi',
            'seminar'     => 'Seminar',
            'pelatihan'   => 'Pelatihan',
            'rakor'       => 'Rapat Koordinasi',
            'klinik'      => 'Klinik Pengadaan',
            default       => ucfirst($type),
        };
    }

    private function formatModeLabel(string $mode): string
    {
        return match (strtolower($mode)) {
            'online'  => 'Daring (Online)',
            'offline' => 'Tatap Muka (Luring)',
            'hybrid'  => 'Hybrid (Luring & Daring)',
            default   => ucfirst($mode),
        };
    }

    private function formatStatusLabel(string $status, bool $isPast): string
    {
        if ($isPast) {
            return 'Selesai';
        }
        return match (strtolower($status)) {
            'open_registration' => 'Pendaftaran Dibuka',
            'coming_soon'       => 'Segera Dibuka',
            'published'         => 'Terjadwal',
            'full'              => 'Kuota Penuh',
            'finished'          => 'Selesai',
            'cancelled'         => 'Dibatalkan',
            default             => 'Terjadwal',
        };
    }
}

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
                ->whereIn('status', ['registered', 'confirmed'])
                ->countAllResults();

            $capacity  = (int) ($row['capacity'] ?? 100);
            $remaining = max(0, $capacity - $regCount);
            $isPast    = strtotime($row['end_at']) < strtotime($now);

            return [
                'id'            => (int) $row['id'],
                'title'         => $row['title'],
                'slug'          => $row['slug'],
                'type'          => $row['type'],
                'type_label'    => $this->formatTypeLabel($row['type']),
                'mode'          => $row['mode'],
                'mode_label'    => $this->formatModeLabel($row['mode']),
                'status'        => $row['status'],
                'status_label'  => $this->formatStatusLabel($row['status'], $isPast),
                'summary'       => $row['summary'] ?: mb_strimwidth(strip_tags((string) $row['description']), 0, 150, '...'),
                'description'   => $row['description'],
                'start_at'      => $row['start_at'],
                'end_at'        => $row['end_at'],
                'start_date'    => date('d M Y', $start),
                'start_day'     => date('d', $start),
                'start_month'   => date('M', $start),
                'start_year'    => date('Y', $start),
                'time_range'    => date('H:i', $start) . ' - ' . date('H:i', $end) . ' WIB',
                'location'      => $row['location'] ?: ($row['mode'] === 'online' ? 'Daring via Zoom' : 'Bandar Lampung'),
                'capacity'      => $capacity,
                'registered'    => $regCount,
                'remaining'     => $remaining,
                'is_past'       => $isPast,
                'cover_url'     => ! empty($row['cover_path']) ? base_url($row['cover_path']) : null,
            ];
        }, $rawItems);

        // Spotlight Event (Closest upcoming open event on page 1 without search)
        $spotlightEvent = null;
        if ($currentPage === 1 && empty($keyword) && empty($type) && empty($mode) && ! empty($items)) {
            // Find first upcoming open event
            foreach ($items as $idx => $it) {
                if (! $it['is_past'] && in_array($it['status'], ['open_registration', 'coming_soon'])) {
                    $spotlightEvent = $it;
                    // Remove spotlight from list so it doesn't duplicate
                    array_splice($items, $idx, 1);
                    break;
                }
            }
        }

        // Live KPI Metrics
        $stats = [
            'total_events'      => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->countAllResults(),
            'upcoming_events'   => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->where('end_at >=', $now)->countAllResults(),
            'open_registration' => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->where('status', 'open_registration')->countAllResults(),
            'total_attendees'   => (int) $this->db->table('event_registrations')->whereIn('status', ['registered', 'confirmed'])->countAllResults(),
        ];

        // Type counts for sidebar/pills
        $typeCounts = [
            'webinar'     => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->where('type', 'webinar')->countAllResults(),
            'bimtek'      => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->where('type', 'bimtek')->countAllResults(),
            'workshop'    => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->where('type', 'workshop')->countAllResults(),
            'sosialisasi' => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->where('type', 'sosialisasi')->countAllResults(),
            'rakor'       => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->where('type', 'rakor')->countAllResults(),
            'klinik'      => (int) $this->db->table('events')->where('deleted_at', null)->where('visibility', 'public')->where('type', 'klinik')->countAllResults(),
        ];

        // Upcoming Top 3 Events for Sidebar
        $upcomingSidebar = $this->db->table('events e')
            ->select('e.title, e.slug, e.start_at, e.mode, e.type, e.location')
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
                ->select('e.id, e.title, e.slug, e.start_at, e.end_at, e.type, e.mode, e.location, e.status')
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
            'title'            => $pageTitle . ' | BegawiPBJ Provinsi Lampung',
            'metaDescription'  => 'Jadwal bimbingan teknis, webinar nasional, workshop probity audit, dan agenda kolaborasi pengadaan barang dan jasa Pemerintah Provinsi Lampung.',
            'pageTitle'        => $pageTitle,
            'viewMode'         => $viewMode,
            'items'            => $items,
            'spotlightEvent'   => $spotlightEvent,
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
            ->where('e.deleted_at', null)
            ->get()->getRowArray();

        if (! $event) {
            throw PageNotFoundException::forPageNotFound('Event atau agenda tidak ditemukan: ' . esc($slug));
        }

        $now = time();
        $startTime = strtotime($event['start_at']);
        $endTime   = strtotime($event['end_at']);
        $isPast    = $endTime < $now;
        $isOngoing = ($startTime <= $now && $endTime >= $now);

        // Registration status
        $regCount = $this->db->table('event_registrations')
            ->where('event_id', $event['id'])
            ->whereIn('status', ['registered', 'confirmed'])
            ->countAllResults();

        $capacity  = (int) ($event['capacity'] ?? 100);
        $remaining = max(0, $capacity - $regCount);

        // Check if user is logged in and already registered
        $userId       = (int) session()->get('user_id');
        $isRegistered = false;
        $userRegData  = null;
        if ($userId > 0) {
            $userRegData = $this->db->table('event_registrations')
                ->where(['event_id' => $event['id'], 'user_id' => $userId])
                ->get()->getRowArray();
            $isRegistered = ! empty($userRegData) && in_array($userRegData['status'], ['registered', 'confirmed', 'waitlist']);
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

        // Related upcoming events
        $relatedEvents = $this->db->table('events e')
            ->select('e.id, e.title, e.slug, e.start_at, e.end_at, e.type, e.mode, e.location, fa.path AS cover_path')
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
            'remainingSeats'  => $remaining,
            'isRegistered'    => $isRegistered,
            'userRegData'     => $userRegData,
            'isRegOpen'       => $isRegOpen,
            'relatedEvents'   => $relatedEvents,
            'shareUrl'        => site_url('event/' . $event['slug']),
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

        $event = $this->db->table('events')->where(['slug' => $slug, 'deleted_at' => null])->get()->getRowArray();
        if (! $event) {
            throw PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }

        // Check if registration window is closed
        if (! empty($event['registration_close_at']) && strtotime($event['registration_close_at']) < time()) {
            return redirect()->back()->with('error', 'Pendaftaran event ini sudah ditutup.');
        }
        if (strtotime($event['end_at']) < time()) {
            return redirect()->back()->with('error', 'Kegiatan ini telah selesai diselenggarakan.');
        }

        // Check if already registered
        $existing = $this->db->table('event_registrations')
            ->where(['event_id' => $event['id'], 'user_id' => $userId])
            ->get()->getRowArray();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah terdaftar pada kegiatan ini.');
        }

        // Check capacity
        $currentCount = $this->db->table('event_registrations')
            ->where('event_id', $event['id'])
            ->whereIn('status', ['registered', 'confirmed'])
            ->countAllResults();

        $capacity = (int) ($event['capacity'] ?? 100);
        $status   = ($currentCount >= $capacity) ? 'waitlist' : 'registered';

        $now = date('Y-m-d H:i:s');
        $this->db->table('event_registrations')->insert([
            'event_id'      => $event['id'],
            'user_id'       => $userId,
            'status'        => $status,
            'registered_at' => $now,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        $this->auditService->log(
            $userId,
            'event.registered',
            'event_registration',
            (string) $this->db->insertID(),
            null,
            ['event_id' => $event['id'], 'event_title' => $event['title'], 'status' => $status]
        );

        $msg = ($status === 'waitlist')
            ? 'Kuota utama telah terpenuhi. Anda otomatis tercatat dalam daftar tunggu (waitlist).'
            : 'Selamat! Pendaftaran Anda berhasil tercatat. Simpan tanggal acara pada kalender Anda.';

        return redirect()->back()->with('message', $msg);
    }

    private function formatTypeLabel(string $type): string
    {
        return match (strtolower($type)) {
            'webinar'     => 'Webinar',
            'bimtek'      => 'Bimtek',
            'workshop'    => 'Workshop',
            'sosialisasi' => 'Sosialisasi',
            'rakor'       => 'Rapat Koordinasi',
            'klinik'      => 'Klinik Konsultasi',
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
            'full'              => 'Kuota Penuh',
            'finished'          => 'Selesai',
            'cancelled'         => 'Dibatalkan',
            default             => 'Terjadwal',
        };
    }
}

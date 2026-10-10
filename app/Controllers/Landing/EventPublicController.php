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

    public function index(): string
    {
        return $this->loadCatalog('Event dan Agenda PBJ', 'events', 'Agenda pelatihan, forum kolaboratif, dan kegiatan ekosistem Pengadaan Barang/Jasa Provinsi Lampung.');
    }

    public function agenda(): string
    {
        return $this->loadCatalog('Agenda PBJ Mendatang', 'events', 'Jadwal kegiatan PBJ yang akan datang dan sedang berlangsung.');
    }

    public function calendar(): string
    {
        return $this->loadCatalog('Kalender Kegiatan PBJ', 'events', 'Kalender agenda pembelajaran dan kolaborasi publik.');
    }

    public function detail(string $slug): string
    {
        $event = $this->db->table('events e')
            ->select('e.*, u.name AS creator_name, fa.path AS cover_path')
            ->join('users u', 'u.id = e.created_by', 'left')
            ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
            ->where('e.slug', $slug)
            ->where('e.deleted_at', null)
            ->get()->getRowArray();

        if (! $event) {
            throw PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }

        $regCount = $this->db->table('event_registrations')
            ->where('event_id', $event['id'])
            ->whereIn('status', ['registered', 'confirmed'])
            ->countAllResults();

        $userId = (int) session()->get('user_id');
        $isRegistered = false;
        if ($userId > 0) {
            $isRegistered = $this->db->table('event_registrations')
                ->where(['event_id' => $event['id'], 'user_id' => $userId])
                ->whereIn('status', ['registered', 'confirmed'])
                ->countAllResults() > 0;
        }

        $startDate = ! empty($event['start_at']) ? date('d F Y', strtotime($event['start_at'])) : date('d F Y');
        $startTime = ! empty($event['start_at']) && ! empty($event['end_at'])
            ? date('H:i', strtotime($event['start_at'])) . ' - ' . date('H:i', strtotime($event['end_at'])) . ' WIB'
            : '09:00 - 12:00 WIB';

        $locationLabel = match ($event['mode'] ?? 'online') {
            'offline' => ($event['location'] ?: 'Bandar Lampung') . ' (Tatap Muka)',
            'hybrid'  => ($event['location'] ?: 'Bandar Lampung') . ' & Daring (Hybrid)',
            default   => 'Daring / Online (Tautan disediakan setelah terdaftar)',
        };

        return view('landing/pages/public_detail', [
            'title'           => $event['title'] . ' | BegawiPBJ',
            'pageTitle'       => 'Detail Event & Agenda',
            'itemTitle'       => $event['title'],
            'category'        => 'Event ' . ucfirst($event['type'] ?? 'PBJ'),
            'description'     => $event['summary'] ?: ($startDate . ' · ' . $startTime),
            'content'         => $event['description'] ?? '',
            'authorLabel'     => $event['creator_name'] ?? 'Biro Pengadaan Barang dan Jasa Provinsi Lampung',
            'dateLabel'       => $startDate . ' (' . $startTime . ')',
            'locationLabel'   => $locationLabel,
            'statusLabel'     => ucfirst($event['status'] ?? 'Open'),
            'coverUrl'        => ! empty($event['cover_path']) ? base_url($event['cover_path']) : null,
            'event'           => $event,
            'registeredCount' => $regCount,
            'isRegistered'    => $isRegistered,
            'backUrl'         => 'event',
        ]);
    }

    public function register(string $slug)
    {
        $userId = (int) session()->get('user_id');
        if ($userId < 1 || session()->get('isLoggedIn') !== true) {
            session()->set('redirect_url', current_url());
            return redirect()->to(site_url('masuk'))->with('error', 'Silakan masuk ke akun Anda terlebih dahulu untuk mendaftar event.');
        }

        $event = $this->db->table('events')->where(['slug' => $slug, 'deleted_at' => null])->get()->getRowArray();
        if (! $event) {
            throw PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }

        // Check if registration is open
        if (! empty($event['registration_close_at']) && strtotime($event['registration_close_at']) < time()) {
            return redirect()->back()->with('error', 'Pendaftaran event ini sudah ditutup.');
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
        $status = ($currentCount >= $capacity) ? 'waitlist' : 'registered';

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
            ['event_id' => $event['id'], 'event_title' => $event['title']]
        );

        $msg = ($status === 'waitlist')
            ? 'Kuota utama penuh. Anda terdaftar dalam daftar tunggu (waitlist).'
            : 'Pendaftaran event berhasil! Sampai jumpa di kegiatan.';

        return redirect()->back()->with('message', $msg);
    }

    private function loadCatalog(string $pageTitle, string $type, string $description): string
    {
        $keyword = $this->request->getGet('q');
        $rawEvents = $this->eventModel->listEvents(null, $keyword);

        $items = [];
        foreach ($rawEvents as $e) {
            $start = ! empty($e['start_at']) ? strtotime($e['start_at']) : time();
            $items[] = [
                'title'    => $e['title'],
                'slug'     => $e['slug'],
                'text'     => $e['summary'] ?: ($e['location'] ?: 'Bandar Lampung'),
                'date'     => date('d M Y', $start),
                'day'      => date('d', $start),
                'month'    => date('M', $start),
                'location' => $e['location'] ?: 'Bandar Lampung · Daring',
                'time'     => date('H.i', $start) . ' WIB',
            ];
        }

        return view('landing/pages/public_catalog', [
            'title'       => $pageTitle . ' | BegawiPBJ',
            'pageTitle'   => $pageTitle,
            'catalogType' => $type,
            'description' => $description,
            'items'       => $items,
        ]);
    }
}

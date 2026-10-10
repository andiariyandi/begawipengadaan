<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;
use App\Models\Communication\EventModel;
use App\Services\Communication\CommunicationService;

class EventController extends BaseController
{
    public function index(): string
    {
        $status   = $this->request->getGet('status') ?: null;
        $keyword  = $this->request->getGet('q') ?: null;

        $items = (new EventModel())->listEvents($status, $keyword);
        $db = db_connect();
        $stats = [
            'total'       => (int) $db->table('events')->where('deleted_at IS NULL', null, false)->countAllResults(),
            'open'        => (int) $db->table('events')->where('deleted_at IS NULL', null, false)->where('status', 'open_registration')->countAllResults(),
            'coming_soon' => (int) $db->table('events')->where('deleted_at IS NULL', null, false)->whereIn('status', ['coming_soon', 'published'])->countAllResults(),
            'finished'    => (int) $db->table('events')->where('deleted_at IS NULL', null, false)->where('status', 'finished')->countAllResults(),
        ];

        return view('dashboard/communication/events/index', [
            'title'         => 'Event & Agenda PBJ | Dashboard BegawiPBJ',
            'pageTitle'     => 'Event & Agenda PBJ',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'events',
            'items'         => $items,
            'stats'         => $stats,
            'filterStatus'  => $status,
            'filterKeyword' => $keyword,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/communication/events/form', [
            'title'         => 'Tambah Event | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Event',
            'formMode'      => 'create',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'events',
            'item'          => null,
        ]);
    }

    public function store()
    {
        $svc = new CommunicationService();
        $post = $this->request->getPost();
        $files = $this->request->getFiles();
        $post['cover_file'] = $files['cover'] ?? null;

        $rules = [
            'title' => 'required|max_length[255]',
            'mode'  => 'permit_empty|in_list[online,offline,hybrid]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }
        try {
            $id = $svc->createEvent($post, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat event: ' . $e->getMessage());
        }
        return redirect()->to(route_to('dashboard.communication.events.detail', (string) $id))
            ->with('success', 'Event berhasil ditambahkan.');
    }

    public function detail(string $id)
    {
        $db = db_connect();
        $row = (new EventModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }

        $eventId = (int) $id;
        $registeredCount = (int) $db->table('event_registrations')
            ->where('event_id', $eventId)
            ->whereIn('status', ['registered', 'confirmed'])
            ->countAllResults();

        $presentCount = (int) $db->table('event_registrations er')
            ->join('attendances a', 'a.event_registration_id = er.id')
            ->where('er.event_id', $eventId)
            ->where('a.status', 'present')
            ->countAllResults();

        $certifiedCount = (int) $db->table('certificates c')
            ->join('event_registrations er', 'er.id = c.source_id AND c.source_type = \'event_registration\'')
            ->where('er.event_id', $eventId)
            ->where('c.status', 'issued')
            ->countAllResults();

        $participants = $db->table('event_registrations er')
            ->select('er.id AS registration_id, er.status AS registration_status, er.registered_at, u.name AS user_name, u.email AS user_email, u.phone AS user_phone, a.status AS attendance_status, a.recorded_at AS attendance_recorded_at, c.id AS certificate_id, c.certificate_number, c.status AS certificate_status')
            ->join('users u', 'u.id = er.user_id')
            ->join('attendances a', 'a.event_registration_id = er.id', 'left')
            ->join('certificates c', 'c.source_id = er.id AND c.source_type = \'event_registration\'', 'left')
            ->where('er.event_id', $eventId)
            ->orderBy('er.registered_at', 'ASC')
            ->get()->getResultArray();

        return view('dashboard/communication/events/detail', [
            'title'            => 'Detail Event | Dashboard BegawiPBJ',
            'pageTitle'        => 'Detail Event',
            'activeMenu'       => 'communication',
            'activeSubmenu'    => 'events',
            'item'             => $row,
            'eventId'          => $eventId,
            'registeredCount'  => $registeredCount,
            'presentCount'     => $presentCount,
            'certifiedCount'   => $certifiedCount,
            'participants'     => $participants,
        ]);
    }

    public function generateAttendanceCode(string $id)
    {
        $svc = new CommunicationService();
        $actorId = (int) session()->get('user_id');
        try {
            $code = $svc->generateAttendanceCode((int) $id, $actorId);
            return redirect()->back()->with('success', 'Kode presensi berhasil di-generate: ' . $code);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membuat kode presensi: ' . $e->getMessage());
        }
    }

    public function openAttendance(string $id)
    {
        $svc = new CommunicationService();
        $actorId = (int) session()->get('user_id');
        try {
            $code = $svc->openAttendance((int) $id, $actorId);
            return redirect()->back()->with('success', 'Presensi event berhasil DIBUKA dengan kode: ' . $code);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membuka presensi: ' . $e->getMessage());
        }
    }

    public function closeAttendance(string $id)
    {
        $svc = new CommunicationService();
        $actorId = (int) session()->get('user_id');
        try {
            $svc->closeAttendance((int) $id, $actorId);
            return redirect()->back()->with('success', 'Presensi event berhasil DITUTUP. Kode presensi tidak dapat digunakan lagi.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menutup presensi: ' . $e->getMessage());
        }
    }

    public function batchCertificates(string $id)
    {
        $svc = new CommunicationService();
        $actorId = (int) session()->get('user_id');
        try {
            $generated = $svc->batchGenerateEventCertificates((int) $id, $actorId);
            $count = count($generated);
            if ($count === 0) {
                return redirect()->back()->with('warning', 'Tidak ada sertifikat baru yang diterbitkan. Pastikan ada peserta yang hadir (status: present) dan belum memiliki sertifikat.');
            }
            return redirect()->back()->with('success', 'Berhasil menerbitkan ' . $count . ' sertifikat secara batch untuk peserta yang hadir!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menerbitkan sertifikat batch: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $row = (new EventModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }
        return view('dashboard/communication/events/form', [
            'title'         => 'Ubah Event | Dashboard BegawiPBJ',
            'pageTitle'     => 'Ubah Event',
            'formMode'      => 'edit',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'events',
            'item'          => $row,
            'eventId'       => (int) $id,
        ]);
    }

    public function update(string $id)
    {
        $svc = new CommunicationService();
        $post = $this->request->getPost();
        $files = $this->request->getFiles();
        $post['cover_file'] = $files['cover'] ?? null;
        $rules = [
            'title' => 'required|max_length[255]',
            'mode'  => 'permit_empty|in_list[online,offline,hybrid]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }
        try {
            $svc->updateEvent((int) $id, $post, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah event: ' . $e->getMessage());
        }
        return redirect()->to(route_to('dashboard.communication.events.detail', (string) $id))
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        $svc = new CommunicationService();
        try {
            $svc->deleteEvent((int) $id, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to(route_to('dashboard.communication.events'))
            ->with('success', 'Event dihapus.');
    }
}
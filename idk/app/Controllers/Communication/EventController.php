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

        return view('dashboard/communication/events/index', [
            'title'         => 'Event dan Agenda | Dashboard BegawiPBJ',
            'pageTitle'     => 'Event dan Agenda',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'events',
            'items'         => $items,
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

        $rules = ['title' => 'required|max_length[255]'];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul wajib diisi.');
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
        $row = (new EventModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Event tidak ditemukan.');
        }
        return view('dashboard/communication/events/detail', [
            'title'         => 'Detail Event | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Event',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'events',
            'item'          => $row,
            'eventId'       => (int) $id,
        ]);
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
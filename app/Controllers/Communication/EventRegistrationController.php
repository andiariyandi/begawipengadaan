<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;
use App\Models\Communication\EventRegistrationModel;
use App\Services\Communication\CommunicationService;

class EventRegistrationController extends BaseController
{
    public function index(): string
    {
        $eventId = $this->request->getGet('event_id') ? (int) $this->request->getGet('event_id') : null;
        $status  = $this->request->getGet('status') ?: null;
        $keyword = $this->request->getGet('q') ?: null;

        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 15;

        $model = new EventRegistrationModel();
        $total = $model->countRegistrations($eventId, $status, $keyword);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;
        $items  = $model->listRegistrations($eventId, $status, $keyword, $perPage, $offset);

        $db = db_connect();
        $stats = [
            'total'      => (int) $db->table('event_registrations')->countAllResults(),
            'registered' => (int) $db->table('event_registrations')->where('status', 'registered')->countAllResults(),
            'attended'   => (int) $db->table('event_registrations')->where('status', 'attended')->countAllResults(),
            'waiting'    => (int) $db->table('event_registrations')->where('status', 'waiting')->countAllResults(),
            'cancelled'  => (int) $db->table('event_registrations')->where('status', 'cancelled')->countAllResults(),
        ];

        $eventsList = $db->table('events')
            ->select('id, title, start_at')
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('start_at', 'DESC')
            ->get()->getResultArray();

        $pagination = [
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'totalPages' => $totalPages,
            'from'       => $total > 0 ? $offset + 1 : 0,
            'to'         => min($offset + $perPage, $total),
        ];

        return view('dashboard/communication/registrations/index', [
            'title'         => 'Registrasi Event | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Registrasi Event',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'event-registrations',
            'items'         => $items,
            'stats'         => $stats,
            'eventsList'    => $eventsList,
            'filterEventId' => $eventId,
            'filterStatus'  => $status,
            'filterKeyword' => $keyword,
            'pagination'    => $pagination,
        ]);
    }

    public function detail(string $id)
    {
        $row = (new EventRegistrationModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Registrasi tidak ditemukan.');
        }
        return view('dashboard/communication/registrations/detail', [
            'title'         => 'Detail Registrasi | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Registrasi',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'event-registrations',
            'item'          => $row,
            'regId'         => (int) $id,
        ]);
    }

    public function updateStatus(string $id)
    {
        $svc = new CommunicationService();
        $newStatus = (string) $this->request->getPost('status');
        $redirectUrl = (string) $this->request->getPost('redirect_to');
        try {
            $svc->updateRegistrationStatus((int) $id, $newStatus, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if (! empty($redirectUrl)) {
            return redirect()->to($redirectUrl)->with('success', 'Status registrasi berhasil diperbarui ke ' . esc($newStatus) . '.');
        }

        return redirect()->to(route_to('dashboard.communication.event-registrations.detail', (string) $id))
            ->with('success', 'Status registrasi berhasil diperbarui.');
    }

    public function waitlistAndCancellation()
    {
        $db = db_connect();
        $model = new EventRegistrationModel();
        $keyword = $this->request->getGet('q') ?: null;
        $eventId = $this->request->getGet('event_id') ? (int) $this->request->getGet('event_id') : null;
        $tab     = $this->request->getGet('tab') ?: 'waiting';

        $waiting   = $model->listRegistrations($eventId, 'waiting', $keyword);
        $cancelled = $model->listRegistrations($eventId, 'cancelled', $keyword);
        $promoted  = $model->listRegistrations($eventId, 'promoted', $keyword);

        $eventsList = $db->table('events')
            ->select('id, title, start_at')
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('start_at', 'DESC')
            ->get()->getResultArray();

        $stats = [
            'waiting'   => count($waiting),
            'cancelled' => count($cancelled),
            'promoted'  => count($promoted),
        ];

        return view('dashboard/communication/waitlist_cancellations/index', [
            'title'         => 'Waitlist dan Pembatalan | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Waitlist dan Pembatalan',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'waitlist-cancellations',
            'waiting'       => $waiting,
            'cancelled'     => $cancelled,
            'promoted'      => $promoted,
            'stats'         => $stats,
            'eventsList'    => $eventsList,
            'activeTab'     => $tab,
            'filterEventId' => $eventId,
            'filterKeyword' => $keyword,
        ]);
    }
}

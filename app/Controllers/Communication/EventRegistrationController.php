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

        $items = (new EventRegistrationModel())->listRegistrations($eventId, $status);

        return view('dashboard/communication/registrations/index', [
            'title'         => 'Registrasi Event | Dashboard BegawiPBJ',
            'pageTitle'     => 'Registrasi Event',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'event-registrations',
            'items'         => $items,
            'filterEventId' => $eventId,
            'filterStatus'  => $status,
        ]);
    }

    public function detail(string $id)
    {
        $row = (new EventRegistrationModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Registrasi tidak ditemukan.');
        }
        return view('dashboard/communication/registrations/detail', [
            'title'         => 'Detail Registrasi | Dashboard BegawiPBJ',
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
        try {
            $svc->updateRegistrationStatus((int) $id, $newStatus, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to(route_to('dashboard.communication.event-registrations.detail', (string) $id))
            ->with('success', 'Status registrasi diperbarui.');
    }

    public function waitlistAndCancellation()
    {
        $model = new EventRegistrationModel();
        $waiting = $model->listRegistrations(null, 'waiting');
        $cancelled = $model->listRegistrations(null, 'cancelled');
        $promoted = $model->listRegistrations(null, 'promoted');

        return view('dashboard/communication/waitlist_cancellations/index', [
            'title'         => 'Waitlist dan Pembatalan | Dashboard BegawiPBJ',
            'pageTitle'     => 'Waitlist dan Pembatalan',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'waitlist-cancellations',
            'waiting'       => $waiting,
            'cancelled'     => $cancelled,
            'promoted'      => $promoted,
        ]);
    }
}
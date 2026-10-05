<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;
use App\Models\Communication\EventAttendanceModel;
use App\Services\Communication\CommunicationService;

class AttendanceController extends BaseController
{
    public function index(): string
    {
        $status = $this->request->getGet('status') ?: null;
        $items  = (new EventAttendanceModel())->listAttendance($status);

        return view('dashboard/communication/attendance/index', [
            'title'         => 'Kehadiran | Dashboard BegawiPBJ',
            'pageTitle'     => 'Kehadiran',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'attendance',
            'items'         => $items,
            'filterStatus'  => $status,
        ]);
    }

    public function record(string $regId)
    {
        $svc = new CommunicationService();
        $status = (string) $this->request->getPost('status');
        $notes  = (string) $this->request->getPost('notes');
        try {
            $svc->recordAttendance((int) $regId, $status, (int) session()->get('user_id'), $notes);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('/dashboard/komunikasi/kehadiran')
            ->with('success', 'Kehadiran tercatat.');
    }
}
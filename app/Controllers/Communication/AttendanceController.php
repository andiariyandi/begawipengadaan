<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;
use App\Models\Communication\EventAttendanceModel;
use App\Services\Communication\CommunicationService;

class AttendanceController extends BaseController
{
    public function index(): string
    {
        $status  = $this->request->getGet('status') ?: null;
        $eventId = $this->request->getGet('event_id') ? (int) $this->request->getGet('event_id') : null;
        $keyword = $this->request->getGet('q') ?: null;

        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 15;

        $model = new EventAttendanceModel();
        $total = $model->countAttendance($status, $eventId, $keyword);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;
        $items  = $model->listAttendance($status, $eventId, $keyword, $perPage, $offset);

        $db = db_connect();
        $stats = [
            'total'   => (int) $db->table('attendances')->countAllResults(),
            'present' => (int) $db->table('attendances')->where('status', 'present')->countAllResults(),
            'excused' => (int) $db->table('attendances')->where('status', 'excused')->countAllResults(),
            'absent'  => (int) $db->table('attendances')->where('status', 'absent')->countAllResults(),
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

        return view('dashboard/communication/attendance/index', [
            'title'         => 'Kehadiran Event | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Kehadiran Event',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'attendance',
            'items'         => $items,
            'stats'         => $stats,
            'eventsList'    => $eventsList,
            'filterStatus'  => $status,
            'filterEventId' => $eventId,
            'filterKeyword' => $keyword,
            'pagination'    => $pagination,
        ]);
    }

    public function record(string $regId)
    {
        $svc = new CommunicationService();
        $status = (string) $this->request->getPost('status');
        $notes  = (string) $this->request->getPost('notes');
        $redirectUrl = (string) $this->request->getPost('redirect_to');
        try {
            $svc->recordAttendance((int) $regId, $status, (int) session()->get('user_id'), $notes);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if (! empty($redirectUrl)) {
            return redirect()->to($redirectUrl)->with('success', 'Kehadiran berhasil dicatat.');
        }

        return redirect()->to('/dashboard/komunikasi/kehadiran')
            ->with('success', 'Kehadiran tercatat.');
    }
}

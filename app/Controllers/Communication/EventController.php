<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;
use App\Models\Communication\EventModel;
use App\Services\Communication\CommunicationService;
use App\Services\EventAssessmentService;

class EventController extends BaseController
{
    public function index(): string
    {
        $status   = $this->request->getGet('status') ?: null;
        $keyword  = $this->request->getGet('q') ?: null;
        $mode     = $this->request->getGet('mode') ?: null;
        $type     = $this->request->getGet('type') ?: null;
        $viewMode = $this->request->getGet('view') === 'table' ? 'table' : 'grid';

        $page     = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage  = 12;

        $model    = new EventModel();
        $total    = $model->countEvents($status, $keyword, $mode, $type);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset   = ($page - 1) * $perPage;
        $items    = $model->listEvents($status, $keyword, $mode, $type, $perPage, $offset);

        $db = db_connect();
        $stats = [
            'total'            => (int) $db->table('events')->where('deleted_at IS NULL', null, false)->countAllResults(),
            'open'             => (int) $db->table('events')->where('deleted_at IS NULL', null, false)->where('status', 'open_registration')->countAllResults(),
            'coming_soon'      => (int) $db->table('events')->where('deleted_at IS NULL', null, false)->whereIn('status', ['coming_soon', 'published'])->countAllResults(),
            'finished'         => (int) $db->table('events')->where('deleted_at IS NULL', null, false)->where('status', 'finished')->countAllResults(),
            'total_registered' => (int) $db->table('event_registrations')->countAllResults(),
            'total_attended'   => (int) $db->table('attendances')->where('status', 'present')->countAllResults(),
        ];

        $pagination = [
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'totalPages' => $totalPages,
            'from'       => $total > 0 ? $offset + 1 : 0,
            'to'         => min($offset + $perPage, $total),
        ];

        return view('dashboard/communication/events/index', [
            'title'         => 'Event & Agenda PBJ | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Event & Agenda PBJ',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'events',
            'items'         => $items,
            'stats'         => $stats,
            'filterStatus'  => $status,
            'filterKeyword' => $keyword,
            'filterMode'    => $mode,
            'filterType'    => $type,
            'viewMode'      => $viewMode,
            'pagination'    => $pagination,
        ]);
    }

    public function create(): string
    {
        $assessmentSvc = new EventAssessmentService();

        return view('dashboard/communication/events/form', [
            'title'            => 'Tambah Event | Dashboard BegawiPengadaan',
            'pageTitle'        => 'Tambah Event',
            'formMode'         => 'create',
            'activeMenu'       => 'communication',
            'activeSubmenu'    => 'events',
            'item'             => null,
            'assessmentConfig' => $assessmentSvc->getAssessmentConfig(0),
            'materials'        => [],
            'assessmentStats'  => [],
            'rundown'          => [],
        ]);
    }

    public function store()
    {
        $svc = new CommunicationService();
        $post = $this->request->getPost();
        $files = $this->request->getFiles();
        $post['cover_file'] = $files['cover'] ?? null;
        $actorId = (int) session()->get('user_id');

        $rules = [
            'title' => 'required|max_length[255]',
            'mode'  => 'permit_empty|in_list[online,offline,hybrid]',
            'cover' => 'permit_empty|is_image[cover]|max_size[cover,5120]|mime_in[cover,image/jpg,image/jpeg,image/png,image/webp]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        try {
            $id = $svc->createEvent($post, $actorId);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat event: ' . $e->getMessage());
        }

        // Save optional Pre-Test, Post-Test, and Self-Paced configuration
        $assessmentSvc = new EventAssessmentService();
        $preTestData = $this->request->getPost('pre_test_data');
        $postTestData = $this->request->getPost('post_test_data');
        $selfPacedData = $this->request->getPost('self_paced_data');
        $selfPacedPost = $this->request->getPost('self_paced');
        $selfPacedArray = is_string($selfPacedData) ? json_decode($selfPacedData, true) : ($selfPacedData ?: $selfPacedPost);

        if (! empty($preTestData) || ! empty($postTestData) || ! empty($selfPacedArray)) {
            $config = [
                'pre_test'   => is_string($preTestData) ? json_decode($preTestData, true) : ($preTestData ?: []),
                'post_test'  => is_string($postTestData) ? json_decode($postTestData, true) : ($postTestData ?: []),
                'self_paced' => is_array($selfPacedArray) ? $selfPacedArray : [],
            ];
            $assessmentSvc->saveAssessmentConfig($id, $config, $actorId);
        }

        // Upload any initial materials attached
        $materialFiles = $this->request->getFileMultiple('materials');
        $materialTitles = (array) $this->request->getPost('material_titles');
        $materialSpeakers = (array) $this->request->getPost('material_speakers');
        if ($materialFiles) {
            foreach ($materialFiles as $idx => $mFile) {
                if ($mFile && $mFile->isValid() && ! $mFile->hasMoved()) {
                    $assessmentSvc->uploadMaterial($id, $mFile, [
                        'title'   => $materialTitles[$idx] ?? '',
                        'speaker' => $materialSpeakers[$idx] ?? '',
                    ], $actorId);
                }
            }
        }

        return redirect()->to(site_url('dashboard/sertifikat/template/tambah?source_type=event&event_id=' . $id))
            ->with('success', 'Event "' . esc($post['title']) . '" berhasil dibuat! Silakan atur template sertifikat khusus untuk event ini.');
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
            ->select('er.id AS registration_id, er.user_id, er.status AS registration_status, er.registered_at, u.name AS user_name, u.email AS user_email, u.phone AS user_phone, a.status AS attendance_status, a.recorded_at AS attendance_recorded_at, c.id AS certificate_id, c.certificate_number, c.status AS certificate_status')
            ->join('users u', 'u.id = er.user_id')
            ->join('attendances a', 'a.event_registration_id = er.id', 'left')
            ->join('certificates c', 'c.source_id = er.id AND c.source_type = \'event_registration\'', 'left')
            ->where('er.event_id', $eventId)
            ->orderBy('er.registered_at', 'ASC')
            ->get()->getResultArray();

        $assessmentSvc = new EventAssessmentService();
        $materials = $assessmentSvc->getMaterials($eventId);
        $assessmentConfig = $assessmentSvc->getAssessmentConfig($eventId);
        $assessmentStats = $assessmentSvc->getAssessmentStats($eventId);
        $templateModel = new \App\Models\Certificate\CertificateTemplateModel();
        $certTemplate  = $templateModel->findForEvent($eventId);
        $rundown       = (new \App\Services\EventRundownService())->getRundown($eventId, $row);

        return view('dashboard/communication/events/detail', [
            'title'            => 'Detail Event | Dashboard BegawiPengadaan',
            'pageTitle'        => 'Detail Event',
            'activeMenu'       => 'communication',
            'activeSubmenu'    => 'events',
            'item'             => $row,
            'eventId'          => $eventId,
            'registeredCount'  => $registeredCount,
            'presentCount'     => $presentCount,
            'certifiedCount'   => $certifiedCount,
            'participants'     => $participants,
            'materials'        => $materials,
            'assessmentConfig' => $assessmentConfig,
            'assessmentStats'  => $assessmentStats,
            'certTemplate'     => $certTemplate,
            'rundown'          => $rundown,
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

        $eventId = (int) $id;
        $assessmentSvc = new EventAssessmentService();
        $materials = $assessmentSvc->getMaterials($eventId);
        $assessmentConfig = $assessmentSvc->getAssessmentConfig($eventId);
        $assessmentStats = $assessmentSvc->getAssessmentStats($eventId);
        $templateModel = new \App\Models\Certificate\CertificateTemplateModel();
        $certTemplate  = $templateModel->findForEvent($eventId);
        $rundown       = (new \App\Services\EventRundownService())->getRundown($eventId, $row);

        return view('dashboard/communication/events/form', [
            'title'            => 'Ubah Event | Dashboard BegawiPengadaan',
            'pageTitle'        => 'Ubah Event',
            'formMode'         => 'edit',
            'activeMenu'       => 'communication',
            'activeSubmenu'    => 'events',
            'item'             => $row,
            'eventId'          => $eventId,
            'materials'        => $materials,
            'assessmentConfig' => $assessmentConfig,
            'assessmentStats'  => $assessmentStats,
            'certTemplate'     => $certTemplate,
            'rundown'          => $rundown,
        ]);
    }

    public function update(string $id)
    {
        $svc = new CommunicationService();
        $post = $this->request->getPost();
        $files = $this->request->getFiles();
        $post['cover_file'] = $files['cover'] ?? null;
        $actorId = (int) session()->get('user_id');
        $eventId = (int) $id;

        $rules = [
            'title' => 'required|max_length[255]',
            'mode'  => 'permit_empty|in_list[online,offline,hybrid]',
            'cover' => 'permit_empty|is_image[cover]|max_size[cover,5120]|mime_in[cover,image/jpg,image/jpeg,image/png,image/webp]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        try {
            $svc->updateEvent($eventId, $post, $actorId);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah event: ' . $e->getMessage());
        }

        // Save optional Pre-Test, Post-Test, and Self-Paced configuration
        $assessmentSvc = new EventAssessmentService();
        $preTestData = $this->request->getPost('pre_test_data');
        $postTestData = $this->request->getPost('post_test_data');
        $selfPacedData = $this->request->getPost('self_paced_data');
        $selfPacedPost = $this->request->getPost('self_paced');
        $selfPacedArray = is_string($selfPacedData) ? json_decode($selfPacedData, true) : ($selfPacedData ?: $selfPacedPost);

        if ($preTestData !== null || $postTestData !== null || $selfPacedArray !== null) {
            $config = [];
            if ($preTestData !== null) {
                $config['pre_test'] = is_string($preTestData) ? json_decode($preTestData, true) : ($preTestData ?: []);
            }
            if ($postTestData !== null) {
                $config['post_test'] = is_string($postTestData) ? json_decode($postTestData, true) : ($postTestData ?: []);
            }
            if ($selfPacedArray !== null) {
                $config['self_paced'] = is_array($selfPacedArray) ? $selfPacedArray : [];
            }
            $assessmentSvc->saveAssessmentConfig($eventId, $config, $actorId);
        }

        // Upload any newly attached materials
        $materialFiles = $this->request->getFileMultiple('materials');
        $materialTitles = (array) $this->request->getPost('material_titles');
        $materialSpeakers = (array) $this->request->getPost('material_speakers');
        if ($materialFiles) {
            foreach ($materialFiles as $idx => $mFile) {
                if ($mFile && $mFile->isValid() && ! $mFile->hasMoved()) {
                    $assessmentSvc->uploadMaterial($eventId, $mFile, [
                        'title'   => $materialTitles[$idx] ?? '',
                        'speaker' => $materialSpeakers[$idx] ?? '',
                    ], $actorId);
                }
            }
        }

        return redirect()->to(route_to('dashboard.communication.events.detail', (string) $eventId))
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function uploadMaterial(string $id)
    {
        $assessmentSvc = new EventAssessmentService();
        $actorId = (int) session()->get('user_id');
        $eventId = (int) $id;

        $file = $this->request->getFile('material_file');
        $title = (string) $this->request->getPost('title');
        $speaker = (string) $this->request->getPost('speaker');
        $notes = (string) $this->request->getPost('notes');

        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'File materi tidak valid atau tidak dipilih.');
        }

        $fileId = $assessmentSvc->uploadMaterial($eventId, $file, [
            'title'   => $title,
            'speaker' => $speaker,
            'notes'   => $notes,
        ], $actorId);

        if (! $fileId) {
            return redirect()->back()->with('error', 'Format file tidak didukung. Harap unggah format PDF, PPT, PPTX, XLS, XLSX, DOC, atau DOCX.');
        }

        return redirect()->back()->with('success', 'Materi tayang berhasil diunggah.');
    }

    public function deleteMaterial(string $id, string $materialId)
    {
        $assessmentSvc = new EventAssessmentService();
        $eventId = (int) $id;
        $matId = (int) $materialId;

        $ok = $assessmentSvc->deleteMaterial($matId, $eventId);
        if ($ok) {
            return redirect()->back()->with('success', 'Materi tayang berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Gagal menghapus materi.');
    }

    public function saveAssessment(string $id)
    {
        $assessmentSvc = new EventAssessmentService();
        $actorId = (int) session()->get('user_id');
        $eventId = (int) $id;

        $preTestData = $this->request->getPost('pre_test_data');
        $postTestData = $this->request->getPost('post_test_data');
        $selfPacedData = $this->request->getPost('self_paced_data');
        $selfPacedPost = $this->request->getPost('self_paced');
        $selfPacedArray = is_string($selfPacedData) ? json_decode($selfPacedData, true) : ($selfPacedData ?: $selfPacedPost);

        $config = [];
        if ($preTestData !== null) {
            $config['pre_test'] = is_string($preTestData) ? json_decode($preTestData, true) : ($preTestData ?: []);
        }
        if ($postTestData !== null) {
            $config['post_test'] = is_string($postTestData) ? json_decode($postTestData, true) : ($postTestData ?: []);
        }
        if ($selfPacedArray !== null) {
            $config['self_paced'] = is_array($selfPacedArray) ? $selfPacedArray : [];
        }

        $assessmentSvc->saveAssessmentConfig($eventId, $config, $actorId);

        return redirect()->back()->with('success', 'Pengaturan Pre-Test & Post-Test berhasil disimpan.');
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

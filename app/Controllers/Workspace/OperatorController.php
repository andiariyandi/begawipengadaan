<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use CodeIgniter\Database\ConnectionInterface;

class OperatorController extends BaseController
{
    private ConnectionInterface $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        $pendingVerif = $this->db->table('profile_verifications')->where('status', 'pending')->countAllResults();
        $pendingEnroll = $this->db->table('enrollments')->where('status', 'pending')->countAllResults();
        $pendingApprov = $this->db->table('approvals')->where('status', 'pending')->countAllResults();
        $activeEvents  = $this->db->table('events')->whereIn('status', ['published', 'ongoing'])->countAllResults();

        // Recent operational activities across queues
        $recentApprovals = $this->db->table('approvals a')
            ->select('a.id, a.resource_type, a.requested_action, a.status, a.created_at, u.name AS requester_name')
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $rows = [];
        foreach ($recentApprovals as $row) {
            $badge = match ($row['status']) {
                'approved' => '<span class="badge bg-success-subtle text-success">Disetujui</span>',
                'rejected' => '<span class="badge bg-danger-subtle text-danger">Ditolak</span>',
                default    => '<span class="badge bg-warning-subtle text-warning">Menunggu</span>',
            };
            $rows[] = [
                '#' . $row['id'],
                esc($row['resource_type'] ?? '-'),
                esc($row['requested_action'] ?? '-'),
                esc($row['requester_name'] ?? 'Sistem'),
                $badge,
                esc(date('d/m/Y H:i', strtotime($row['created_at']))),
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Operasional',
            'title'       => 'Dashboard Operator',
            'description' => 'Ringkasan antrean kerja, verifikasi, enrollment, dan persetujuan operasional.',
            'cards'       => [
                ['title' => 'Verifikasi Profil', 'value' => sprintf('%02d', $pendingVerif), 'meta' => 'menunggu persetujuan'],
                ['title' => 'Enrollment Kelas', 'value' => sprintf('%02d', $pendingEnroll), 'meta' => 'pendaftaran baru'],
                ['title' => 'Antrean Approval', 'value' => sprintf('%02d', $pendingApprov), 'meta' => 'perlu tindakan'],
                ['title' => 'Event Aktif', 'value' => sprintf('%02d', $activeEvents), 'meta' => 'sedang berjalan / tayang'],
            ],
            'tableTitle'  => 'Antrean Persetujuan & Operasional Terkini',
            'table'       => [
                'headers' => ['ID', 'Resource', 'Tindakan', 'Pemohon', 'Status', 'Waktu Pengajuan'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Tinjau Antrean Verifikasi Profil Pengguna', 'url' => route_to('workspace.operator.profile-verifications'), 'btn' => 'Buka Verifikasi'],
                ['label' => 'Validasi Pendaftaran Enrollment Kelas', 'url' => route_to('workspace.operator.enrollments'), 'btn' => 'Buka Enrollment'],
                ['label' => 'Monitoring Event & Presensi Kehadiran', 'url' => route_to('workspace.operator.events'), 'btn' => 'Buka Event'],
                ['label' => 'Persetujuan Operasional & Sertifikat', 'url' => route_to('workspace.operator.approvals'), 'btn' => 'Buka Approval'],
            ],
        ];

        return $this->renderOperasional('Dashboard Operator', $sectionContent);
    }

    public function profileVerifications()
    {
        $pending  = $this->db->table('profile_verifications')->where('status', 'pending')->countAllResults();
        $approved = $this->db->table('profile_verifications')->where('status', 'approved')->countAllResults();
        $rejected = $this->db->table('profile_verifications')->where('status', 'rejected')->countAllResults();

        $list = $this->db->table('profile_verifications pv')
            ->select('pv.id, pv.verification_type, pv.status, pv.created_at, u.name, u.email')
            ->join('users u', 'u.id = pv.user_id')
            ->orderBy('pv.created_at', 'DESC')
            ->limit(25)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'approved' => '<span class="badge bg-success-subtle text-success">Disetujui</span>',
                'rejected' => '<span class="badge bg-danger-subtle text-danger">Ditolak</span>',
                default    => '<span class="badge bg-warning-subtle text-warning">Menunggu</span>',
            };

            $actionBtn = '-';
            if ($item['status'] === 'pending') {
                $approveUrl = route_to('workspace.operator.profile-verifications.approve', $item['id']);
                $rejectUrl  = route_to('workspace.operator.profile-verifications.reject', $item['id']);
                $csrf       = csrf_field();
                $actionBtn  = '<div class="d-flex gap-1">' .
                    '<form action="' . $approveUrl . '" method="post" class="d-inline">' . $csrf . '<button type="submit" class="btn btn-sm btn-success py-0 px-2">Setujui</button></form>' .
                    '<form action="' . $rejectUrl . '" method="post" class="d-inline">' . $csrf . '<input type="hidden" name="reason" value="Data profil ditolak oleh Operator."><button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm(\'Tolak verifikasi ini?\')">Tolak</button></form>' .
                    '</div>';
            }

            $rows[] = [
                '#' . $item['id'],
                '<strong>' . esc($item['name'] ?? '-') . '</strong><br><small class="text-muted">' . esc($item['email'] ?? '') . '</small>',
                '<span class="badge bg-light text-dark">' . esc($item['verification_type'] ?? 'umum') . '</span>',
                $badge,
                esc(date('d/m/Y H:i', strtotime($item['created_at']))),
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Operasional Akses',
            'title'       => 'Verifikasi Profil',
            'description' => 'Daftar pengajuan verifikasi identitas dan tipe profil pengguna.',
            'cards'       => [
                ['title' => 'Menunggu Verifikasi', 'value' => sprintf('%02d', $pending), 'meta' => 'pengajuan aktif'],
                ['title' => 'Telah Disetujui', 'value' => sprintf('%02d', $approved), 'meta' => 'terverifikasi'],
                ['title' => 'Ditolak / Revisi', 'value' => sprintf('%02d', $rejected), 'meta' => 'membutuhkan perbaikan'],
            ],
            'tableTitle'  => 'Daftar Pengajuan Verifikasi Profil',
            'table'       => [
                'headers' => ['ID', 'Nama / Email', 'Tipe Verifikasi', 'Status', 'Tanggal Pengajuan', 'Aksi'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Kelola Seluruh Profil Pengguna di Dashboard Akses', 'url' => site_url('dashboard/akses/verifikasi-profil'), 'btn' => 'Buka Admin'],
            ],
        ];

        return $this->renderOperasional('Verifikasi Profil', $sectionContent);
    }

    public function enrollments()
    {
        $pending   = $this->db->table('enrollments')->where('status', 'pending')->countAllResults();
        $active    = $this->db->table('enrollments')->where('status', 'active')->countAllResults();
        $completed = $this->db->table('enrollments')->where('status', 'completed')->countAllResults();

        $list = $this->db->table('enrollments e')
            ->select('e.id, e.status, e.enrolled_at, e.final_score, u.name, u.email, lc.name AS class_name')
            ->join('users u', 'u.id = e.user_id')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->orderBy('e.created_at', 'DESC')
            ->limit(25)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'active'    => '<span class="badge bg-primary-subtle text-primary">Aktif</span>',
                'completed' => '<span class="badge bg-success-subtle text-success">Selesai</span>',
                'cancelled' => '<span class="badge bg-danger-subtle text-danger">Dibatalkan</span>',
                default     => '<span class="badge bg-warning-subtle text-warning">Menunggu</span>',
            };

            $actionBtn = '-';
            if ($item['status'] === 'pending') {
                $actUrl    = route_to('workspace.operator.enrollments.activate', $item['id']);
                $csrf      = csrf_field();
                $actionBtn = '<form action="' . $actUrl . '" method="post" class="d-inline">' . $csrf . '<button type="submit" class="btn btn-sm btn-primary py-0 px-2">Aktifkan</button></form>';
            }

            $rows[] = [
                '#' . $item['id'],
                '<strong>' . esc($item['name'] ?? '-') . '</strong><br><small class="text-muted">' . esc($item['email'] ?? '') . '</small>',
                esc($item['class_name'] ?? '-'),
                $badge,
                $item['final_score'] !== null ? number_format((float) $item['final_score'], 1) : '-',
                esc(date('d/m/Y H:i', strtotime($item['enrolled_at'] ?? 'now'))),
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Operasional Pembelajaran',
            'title'       => 'Enrollment Kelas',
            'description' => 'Monitoring kepesertaan kelas dan progres enrollment peserta.',
            'cards'       => [
                ['title' => 'Pending Enrollment', 'value' => sprintf('%02d', $pending), 'meta' => 'pendaftaran menunggu aktivasi'],
                ['title' => 'Enrollment Aktif', 'value' => sprintf('%02d', $active), 'meta' => 'peserta aktif belajar'],
                ['title' => 'Lulus / Selesai', 'value' => sprintf('%02d', $completed), 'meta' => 'telah menyelesaikan kurikulum'],
            ],
            'tableTitle'  => 'Daftar Kepesertaan Kelas Terkini',
            'table'       => [
                'headers' => ['ID', 'Peserta', 'Kelas', 'Status', 'Nilai Akhir', 'Waktu Daftar', 'Aksi'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Lihat Katalog Kelas Pembelajaran', 'url' => site_url('katalog'), 'btn' => 'Buka Katalog'],
            ],
        ];

        return $this->renderOperasional('Enrollment Kelas', $sectionContent);
    }

    public function events()
    {
        $published = $this->db->table('events')->where('status', 'published')->countAllResults();
        $draft     = $this->db->table('events')->where('status', 'draft')->countAllResults();
        $ongoing   = $this->db->table('events')->where('status', 'ongoing')->countAllResults();

        $list = $this->db->table('events')
            ->select('id, title, type, mode, status, start_at, capacity')
            ->orderBy('created_at', 'DESC')
            ->limit(25)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'published' => '<span class="badge bg-success-subtle text-success">Tayang</span>',
                'ongoing'   => '<span class="badge bg-primary-subtle text-primary">Berlangsung</span>',
                'completed' => '<span class="badge bg-secondary-subtle text-secondary">Selesai</span>',
                default     => '<span class="badge bg-warning-subtle text-warning">Draft</span>',
            };
            $rows[] = [
                '#' . $item['id'],
                '<strong>' . esc($item['title'] ?? '-') . '</strong>',
                '<span class="badge bg-light text-dark">' . esc($item['type'] ?? '-') . ' / ' . esc($item['mode'] ?? '-') . '</span>',
                $badge,
                (int) ($item['capacity'] ?? 0) . ' Kursi',
                $item['start_at'] ? esc(date('d/m/Y H:i', strtotime($item['start_at']))) : '-',
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Operasional Event',
            'title'       => 'Manajemen Event',
            'description' => 'Operasional seminar, workshop, bimbingan teknis, dan event PBJ.',
            'cards'       => [
                ['title' => 'Event Tayang', 'value' => sprintf('%02d', $published), 'meta' => 'pendaftaran terbuka'],
                ['title' => 'Event Berlangsung', 'value' => sprintf('%02d', $ongoing), 'meta' => 'sedang diselenggarakan'],
                ['title' => 'Draft Event', 'value' => sprintf('%02d', $draft), 'meta' => 'dalam persiapan'],
            ],
            'tableTitle'  => 'Daftar Event & Workshop PBJ',
            'table'       => [
                'headers' => ['ID', 'Judul Event', 'Tipe / Moda', 'Status', 'Kapasitas', 'Jadwal Mulai'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Lihat Agenda Event Publik', 'url' => site_url('event'), 'btn' => 'Buka Event Publik'],
            ],
        ];

        return $this->renderOperasional('Manajemen Event', $sectionContent);
    }

    public function attendance()
    {
        $recorded = $this->db->table('attendances')->countAllResults();
        $registrations = $this->db->table('event_registrations')->countAllResults();

        $list = $this->db->table('attendances a')
            ->select('a.id, a.status, a.recorded_at, ev.title AS event_title, u.name AS user_name, u.email AS user_email')
            ->join('event_registrations er', 'er.id = a.event_registration_id')
            ->join('events ev', 'ev.id = er.event_id')
            ->join('users u', 'u.id = er.user_id')
            ->orderBy('a.created_at', 'DESC')
            ->limit(25)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'present' => '<span class="badge bg-success-subtle text-success">Hadir</span>',
                'absent'  => '<span class="badge bg-danger-subtle text-danger">Tidak Hadir</span>',
                default   => '<span class="badge bg-secondary-subtle text-secondary">' . esc($item['status']) . '</span>',
            };
            $rows[] = [
                '#' . $item['id'],
                esc($item['event_title'] ?? '-'),
                '<strong>' . esc($item['user_name'] ?? '-') . '</strong><br><small class="text-muted">' . esc($item['user_email'] ?? '') . '</small>',
                $badge,
                $item['recorded_at'] ? esc(date('d/m/Y H:i', strtotime($item['recorded_at']))) : '-',
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Presensi & Kehadiran',
            'title'       => 'Pencatatan Kehadiran',
            'description' => 'Monitoring presensi peserta dalam kegiatan pembelajaran dan event PBJ.',
            'cards'       => [
                ['title' => 'Total Presensi Tercatat', 'value' => sprintf('%02d', $recorded), 'meta' => 'catatan kehadiran'],
                ['title' => 'Pendaftaran Event', 'value' => sprintf('%02d', $registrations), 'meta' => 'total peserta terdaftar'],
            ],
            'tableTitle'  => 'Catatan Presensi Terkini',
            'table'       => [
                'headers' => ['ID', 'Nama Event', 'Peserta', 'Status', 'Waktu Tercatat'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Kelola Event & Pendaftaran Peserta', 'url' => route_to('workspace.operator.events'), 'btn' => 'Buka Event'],
            ],
        ];

        return $this->renderOperasional('Kehadiran', $sectionContent);
    }

    public function approvals()
    {
        $pending  = $this->db->table('approvals')->where('status', 'pending')->countAllResults();
        $approved = $this->db->table('approvals')->where('status', 'approved')->countAllResults();
        $rejected = $this->db->table('approvals')->where('status', 'rejected')->countAllResults();

        $list = $this->db->table('approvals a')
            ->select('a.id, a.resource_type, a.requested_action, a.status, a.reason, a.created_at, u.name AS requester_name')
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(25)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'approved' => '<span class="badge bg-success-subtle text-success">Disetujui</span>',
                'rejected' => '<span class="badge bg-danger-subtle text-danger">Ditolak</span>',
                default    => '<span class="badge bg-warning-subtle text-warning">Menunggu</span>',
            };

            $actionBtn = '-';
            if ($item['status'] === 'pending') {
                $approveUrl = route_to('workspace.operator.approvals.approve', $item['id']);
                $rejectUrl  = route_to('workspace.operator.approvals.reject', $item['id']);
                $csrf       = csrf_field();
                $actionBtn  = '<div class="d-flex gap-1">' .
                    '<form action="' . $approveUrl . '" method="post" class="d-inline">' . $csrf . '<button type="submit" class="btn btn-sm btn-success py-0 px-2">Setujui</button></form>' .
                    '<form action="' . $rejectUrl . '" method="post" class="d-inline">' . $csrf . '<input type="hidden" name="reason" value="Approval ditolak oleh Operator."><button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm(\'Tolak approval ini?\')">Tolak</button></form>' .
                    '</div>';
            }

            $rows[] = [
                '#' . $item['id'],
                '<span class="badge bg-light text-dark">' . esc($item['resource_type'] ?? '-') . '</span>',
                '<strong>' . esc($item['requested_action'] ?? '-') . '</strong>',
                esc($item['requester_name'] ?? 'Sistem'),
                $badge,
                esc(date('d/m/Y H:i', strtotime($item['created_at']))),
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Antrean Persetujuan',
            'title'       => 'Antrean Approval',
            'description' => 'Persetujuan operasional sertifikat, verifikasi profil, dan perubahan hak akses.',
            'cards'       => [
                ['title' => 'Menunggu Approval', 'value' => sprintf('%02d', $pending), 'meta' => 'perlu tindakan'],
                ['title' => 'Telah Disetujui', 'value' => sprintf('%02d', $approved), 'meta' => 'disetujui'],
                ['title' => 'Ditolak', 'value' => sprintf('%02d', $rejected), 'meta' => 'ditolak'],
            ],
            'tableTitle'  => 'Daftar Pengajuan Approval Sistem',
            'table'       => [
                'headers' => ['ID', 'Tipe Objek', 'Tindakan', 'Diajukan Oleh', 'Status', 'Waktu Pengajuan', 'Aksi'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Buka Modul Sertifikat Pembelajaran', 'url' => site_url('dashboard/sertifikat'), 'btn' => 'Buka Sertifikat'],
            ],
        ];

        return $this->renderOperasional('Antrean Approval', $sectionContent);
    }

    public function approveVerification(string $id)
    {
        $verificationId = (int) $id;
        $actorId = (int) (session()->get('user_id') ?: 1);
        $notes = (string) $this->request->getPost('notes');

        $service = new \App\Services\Admin\ProfileVerificationService();
        if ($service->approve($verificationId, $actorId, $notes)) {
            return redirect()->back()->with('message', 'Pengajuan verifikasi profil #' . $verificationId . ' berhasil disetujui.');
        }
        return redirect()->back()->with('error', 'Verifikasi profil tidak ditemukan.');
    }

    public function rejectVerification(string $id)
    {
        $verificationId = (int) $id;
        $actorId = (int) (session()->get('user_id') ?: 1);
        $reason = trim((string) $this->request->getPost('reason')) ?: 'Data profil tidak sesuai.';
        $notes = (string) $this->request->getPost('notes');

        $service = new \App\Services\Admin\ProfileVerificationService();
        try {
            if ($service->reject($verificationId, $reason, $actorId, $notes)) {
                return redirect()->back()->with('message', 'Pengajuan verifikasi profil #' . $verificationId . ' telah ditolak.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('error', 'Verifikasi profil tidak ditemukan.');
    }

    public function activateEnrollment(string $id)
    {
        $enrollmentId = (int) $id;
        $learning = new \App\Services\LearningService($this->db);
        try {
            $learning->activateEnrollment($enrollmentId);
            return redirect()->back()->with('message', 'Enrollment #' . $enrollmentId . ' berhasil diaktifkan.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approveApproval(string $id)
    {
        $approvalId = (int) $id;
        $actorId = (int) (session()->get('user_id') ?: 1);
        $notes = (string) $this->request->getPost('notes') ?: 'Disetujui oleh Operator.';

        $approval = $this->db->table('approvals')->where('id', $approvalId)->get()->getRowArray();
        if (! $approval) {
            return redirect()->back()->with('error', 'Approval tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('approvals')->where('id', $approvalId)->update([
            'status'      => 'approved',
            'reviewed_by' => $actorId,
            'reviewed_at' => $now,
            'notes'       => $notes,
            'updated_at'  => $now,
        ]);

        if (($approval['resource_type'] ?? '') === 'certificate' && ($approval['requested_action'] ?? '') === 'issue_certificate') {
            try {
                $generator = new \App\Services\CertificateGenerator();
                $result = $generator->generateFromApproval($approvalId, $actorId);
                return redirect()->back()->with('message', 'Approval disetujui dan Sertifikat #' . ($result['certificate_number'] ?? '') . ' berhasil diterbitkan.');
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Approval disetujui, namun pembuatan file sertifikat mengalami kendala: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('message', 'Approval #' . $approvalId . ' berhasil disetujui.');
    }

    public function rejectApproval(string $id)
    {
        $approvalId = (int) $id;
        $actorId = (int) (session()->get('user_id') ?: 1);
        $reason = trim((string) $this->request->getPost('reason')) ?: 'Pengajuan ditolak oleh Operator.';

        $approval = $this->db->table('approvals')->where('id', $approvalId)->get()->getRowArray();
        if (! $approval) {
            return redirect()->back()->with('error', 'Approval tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('approvals')->where('id', $approvalId)->update([
            'status'      => 'rejected',
            'reviewed_by' => $actorId,
            'reviewed_at' => $now,
            'reason'      => $reason,
            'updated_at'  => $now,
        ]);

        return redirect()->back()->with('message', 'Approval #' . $approvalId . ' telah ditolak.');
    }

    public function activityMonitoring()
    {
        $logs = $this->db->table('audit_logs a')
            ->select('a.*, u.name AS actor_name, u.email AS actor_email')
            ->join('users u', 'u.id = a.actor_id', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        $rows = [];
        foreach ($logs as $log) {
            $badge = match ($log['result']) {
                'success' => '<span class="badge bg-success-subtle text-success">Berhasil</span>',
                'failed', 'failure' => '<span class="badge bg-danger-subtle text-danger">Gagal</span>',
                default   => '<span class="badge bg-info-subtle text-info">' . esc($log['result']) . '</span>',
            };
            $rows[] = [
                '#' . $log['id'],
                esc($log['actor_name'] ?: 'Sistem'),
                esc($log['action']),
                esc($log['resource_type'] ? $log['resource_type'] . ' #' . $log['resource_id'] : '-'),
                $badge,
                esc(date('d/m/Y H:i', strtotime($log['created_at']))),
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Monitoring Operasional',
            'title'       => 'Monitoring Aktivitas Sistem',
            'description' => 'Jejak audit log dan aktivitas operasional seluruh pengguna dan sistem.',
            'cards'       => [
                ['title' => 'Total Log Terkini', 'value' => sprintf('%02d', count($logs)), 'meta' => '50 aktivitas terakhir'],
            ],
            'tableTitle'  => 'Log Aktivitas dan Jejak Audit',
            'table'       => [
                'headers' => ['ID', 'Pengguna', 'Aktivitas', 'Objek', 'Hasil', 'Waktu'],
                'rows'    => $rows,
            ],
            'items'       => [],
        ];

        return $this->renderOperasional('Monitoring Aktivitas', $sectionContent);
    }

    public function evaluations()
    {
        $pendingReviews   = $this->db->table('assignment_submissions')->whereIn('status', ['submitted', 'in_review'])->countAllResults();
        $gradedCount      = $this->db->table('assignment_submissions')->where('status', 'graded')->countAllResults();
        $revisionCount    = $this->db->table('assignment_submissions')->where('status', 'revision_required')->countAllResults();
        $totalSubmissions = $this->db->table('assignment_submissions')->countAllResults();

        $list = $this->db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title, lc.name AS class_name, lc.code AS class_code, u.name AS participant_name, u.email AS participant_email, grader.name AS grader_name')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('users grader', 'grader.id = s.graded_by', 'left')
            ->orderBy('s.submitted_at', 'DESC')
            ->limit(35)
            ->get()->getResultArray();

        $rows = [];
        foreach ($list as $item) {
            $badge = match ($item['status']) {
                'graded'            => '<span class="badge bg-success-subtle text-success">Graded</span>',
                'revision_required' => '<span class="badge bg-danger-subtle text-danger">Revisi</span>',
                'in_review'         => '<span class="badge bg-info-subtle text-info">In Review</span>',
                default             => '<span class="badge bg-warning-subtle text-warning">Menunggu</span>',
            };

            $reviewUrl = route_to('workspace.operator.submission.review', (string) $item['id']);
            $actionBtn = '<a href="' . $reviewUrl . '" class="btn btn-sm btn-outline-warning py-0 px-2 fw-medium text-dark"><i class="fas fa-edit me-1"></i>Review / Override</a>';

            $rows[] = [
                '#' . $item['id'],
                '<strong>' . esc($item['participant_name'] ?? 'Peserta') . '</strong><br><small class="text-muted">' . esc($item['participant_email'] ?? '') . '</small>',
                '<strong>' . esc($item['assignment_title'] ?? '-') . '</strong><br><small class="text-muted">' . esc($item['class_name'] ?? '-') . '</small>',
                $badge,
                $item['score'] !== null ? '<strong>' . number_format((float) $item['score'], 1) . '</strong>' : '<span class="text-muted">-</span>',
                esc($item['grader_name'] ?? 'Belum dinilai'),
                $item['submitted_at'] ? esc(date('d/m/Y H:i', strtotime($item['submitted_at']))) : '-',
                $actionBtn,
            ];
        }

        $sectionContent = [
            'eyebrow'     => 'Kewenangan Override Operasional',
            'title'       => 'Evaluasi Pembelajaran & Override Nilai',
            'description' => 'Monitoring seluruh penugasan dan hasil evaluasi trainer dengan hak override operasional.',
            'cards'       => [
                ['title' => 'Perlu Review', 'value' => sprintf('%02d', $pendingReviews), 'meta' => 'antrean review submission'],
                ['title' => 'Telah Dinilai', 'value' => sprintf('%02d', $gradedCount), 'meta' => 'selesai evaluasi'],
                ['title' => 'Minta Revisi', 'value' => sprintf('%02d', $revisionCount), 'meta' => 'perlu perbaikan peserta'],
                ['title' => 'Total Submission', 'value' => sprintf('%02d', $totalSubmissions), 'meta' => 'arsip tugas terkumpul'],
            ],
            'tableTitle'  => 'Antrean Evaluasi Tugas & Submisi Lintas Kelas',
            'table'       => [
                'headers' => ['ID', 'Peserta', 'Tugas & Kelas', 'Status', 'Nilai', 'Penilai', 'Waktu Submit', 'Aksi'],
                'rows'    => $rows,
            ],
            'items'       => [
                ['label' => 'Monitoring Kelulusan Peserta pada Enrollment Kelas', 'url' => route_to('workspace.operator.enrollments'), 'btn' => 'Buka Enrollment'],
                ['label' => 'Persetujuan Sertifikat & Hasil Kelulusan', 'url' => route_to('workspace.operator.approvals'), 'btn' => 'Buka Approval'],
            ],
        ];

        return $this->renderOperasional('Evaluasi & Override', $sectionContent);
    }

    public function reviewSubmission(string $submissionId)
    {
        $submission = $this->db->table('assignment_submissions s')
            ->select('s.*, a.title AS assignment_title, a.instructions AS assignment_instructions, lc.name AS class_name, lc.code AS class_code, u.name AS participant_name, u.email AS participant_email, grader.name AS grader_name')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->join('learning_classes lc', 'lc.id = a.class_id')
            ->join('enrollments e', 'e.id = s.enrollment_id')
            ->join('users u', 'u.id = e.user_id')
            ->join('users grader', 'grader.id = s.graded_by', 'left')
            ->where('s.id', (int) $submissionId)
            ->get()->getRowArray();

        if (! $submission) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Submission tidak ditemukan.');
        }

        $files = $this->db->table('assignment_submission_files sf')
            ->select('sf.*, fa.original_name, fa.mime_type, fa.size_bytes')
            ->join('file_assets fa', 'fa.id = sf.file_id', 'left')
            ->where('sf.submission_id', (int) $submissionId)
            ->get()->getResultArray();

        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operator/override_submission', [
            'title'       => 'Override Penilaian #' . $submissionId . ' | Operator BegawiPBJ',
            'pageTitle'   => 'Override Penilaian Tugas',
            'userName'    => $userName,
            'roles'       => ['operator'],
            'primaryRole' => 'operator',
            'roleLabel'   => 'Operator',
            'submission'  => $submission,
            'files'       => $files,
        ]);
    }

    public function overrideSubmission(string $submissionId)
    {
        $actorId = (int) (session()->get('user_id') ?: 1);
        $score   = (float) $this->request->getPost('score');
        $status  = (string) $this->request->getPost('status');
        $reason  = trim((string) $this->request->getPost('override_reason')) ?: 'Penyesuaian nilai oleh kewenangan Operator.';

        $submission = $this->db->table('assignment_submissions s')
            ->select('s.*, a.class_id')
            ->join('assignments a', 'a.id = s.assignment_id')
            ->where('s.id', (int) $submissionId)
            ->get()->getRowArray();

        if (! $submission) {
            return redirect()->back()->with('error', 'Submission tugas tidak ditemukan.');
        }

        if (! in_array($status, ['graded', 'revision_required', 'in_review'], true)) {
            $status = 'graded';
        }
        $score = max(0.0, min(100.0, $score));

        $now = date('Y-m-d H:i:s');
        $existingFeedback = trim((string) ($submission['feedback'] ?? ''));
        $overrideTag = "[OVERRIDE OPERATOR " . date('d/m/Y H:i') . "]: " . $reason;
        $updatedFeedback = $existingFeedback !== '' ? ($existingFeedback . "\n\n" . $overrideTag) : $overrideTag;

        $this->db->table('assignment_submissions')->where('id', (int) $submissionId)->update([
            'score'      => $score,
            'status'     => $status,
            'feedback'   => $updatedFeedback,
            'graded_by'  => $actorId,
            'graded_at'  => $now,
            'updated_at' => $now,
        ]);

        if ($status === 'graded') {
            try {
                (new \App\Services\LearningService($this->db))->evaluateCompletion((int) $submission['enrollment_id']);
            } catch (\Throwable $e) {
                log_message('error', 'evaluateCompletion failed on override: {m}', ['m' => $e->getMessage()]);
            }
        }

        // Catat Audit Trail
        $this->db->table('audit_logs')->insert([
            'actor_id'      => $actorId,
            'action'        => 'evaluation.operator_override',
            'resource_type' => 'assignment_submission',
            'resource_id'   => (int) $submissionId,
            'result'        => 'success',
            'context'       => json_encode([
                'old_score'  => $submission['score'],
                'new_score'  => $score,
                'old_status' => $submission['status'],
                'new_status' => $status,
                'reason'     => $reason,
            ], JSON_UNESCAPED_SLASHES),
            'ip_address'    => $this->request->getIPAddress(),
            'user_agent'    => mb_substr((string) $this->request->getUserAgent(), 0, 255),
            'created_at'    => $now,
        ]);

        return redirect()->to(route_to('workspace.operator.evaluations'))
            ->with('message', 'Penilaian submission #' . $submissionId . ' berhasil di-override oleh Operator (Nilai: ' . $score . ', Status: ' . $status . ').');
    }

    public function overrideEnrollmentScore(string $enrollmentId)
    {
        $actorId = (int) (session()->get('user_id') ?: 1);
        $score   = (float) $this->request->getPost('final_score');
        $status  = (string) $this->request->getPost('status') ?: 'completed';
        $reason  = trim((string) $this->request->getPost('reason')) ?: 'Penyesuaian nilai akhir / kelulusan khusus oleh Operator.';

        $enrollment = $this->db->table('enrollments')->where('id', (int) $enrollmentId)->get()->getRowArray();
        if (! $enrollment) {
            return redirect()->back()->with('error', 'Enrollment tidak ditemukan.');
        }

        if (! in_array($status, ['active', 'completed', 'withdrawn', 'cancelled'], true)) {
            $status = 'completed';
        }
        $score = max(0.0, min(100.0, $score));
        $now = date('Y-m-d H:i:s');

        $updateData = [
            'final_score' => $score,
            'status'      => $status,
            'updated_at'  => $now,
        ];

        if ($status === 'completed') {
            $updateData['completed_at'] = $now;
            $updateData['passed_at']    = $now;
            $updateData['certificate_eligible_at'] = $now;
        }

        $this->db->table('enrollments')->where('id', (int) $enrollmentId)->update($updateData);

        // Catat Audit Trail
        $this->db->table('audit_logs')->insert([
            'actor_id'      => $actorId,
            'action'        => 'enrollment.operator_override',
            'resource_type' => 'enrollment',
            'resource_id'   => (int) $enrollmentId,
            'result'        => 'success',
            'context'       => json_encode([
                'old_score'  => $enrollment['final_score'],
                'new_score'  => $score,
                'old_status' => $enrollment['status'],
                'new_status' => $status,
                'reason'     => $reason,
            ], JSON_UNESCAPED_SLASHES),
            'ip_address'    => $this->request->getIPAddress(),
            'user_agent'    => mb_substr((string) $this->request->getUserAgent(), 0, 255),
            'created_at'    => $now,
        ]);

        return redirect()->back()->with('message', 'Nilai akhir dan status kelulusan enrollment #' . $enrollmentId . ' berhasil di-override oleh Operator.');
    }

    private function renderOperasional(string $title, array $sectionContent): string
    {
        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Operator');

        return view('main/workspace/pages/operasional', [
            'title'          => $title . ' | Operator BegawiPBJ',
            'pageTitle'      => $title,
            'userName'       => $userName,
            'roles'          => ['operator'],
            'primaryRole'    => 'operator',
            'roleLabel'      => 'Operator',
            'menuItems'      => $this->menuItems(),
            'sectionContent' => $sectionContent,
        ]);
    }

    private function menuItems(): array
    {
        $workspace = route_to('workspace.home');
        $section = static fn (string $name): string => route_to('workspace.section', $name);

        return [
            ['label' => 'Dashboard', 'items' => [
                ['label' => 'Ringkasan', 'url' => $workspace],
                ['label' => 'Aktivitas Terakhir', 'url' => $section('aktivitas')],
            ]],
            ['label' => 'Operasional', 'items' => [
                ['label' => 'Verifikasi Profil', 'url' => route_to('workspace.operator.profile-verifications')],
                ['label' => 'Enrollment', 'url' => route_to('workspace.operator.enrollments')],
                ['label' => 'Evaluasi & Override', 'url' => route_to('workspace.operator.evaluations')],
                ['label' => 'Event', 'url' => route_to('workspace.operator.events')],
                ['label' => 'Kehadiran', 'url' => route_to('workspace.operator.attendance')],
                ['label' => 'Approval', 'url' => route_to('workspace.operator.approvals')],
            ]],
        ];
    }
}

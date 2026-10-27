<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ApprovalModel;
use App\Services\AuditService;
use App\Services\CertificateGenerator;

class ApprovalController extends BaseController
{
    protected ApprovalModel $approvalModel;
    protected AuditService $auditService;

    public function __construct()
    {
        $this->approvalModel = new ApprovalModel();
        $this->auditService  = new AuditService();
    }

    /**
     * Halaman Utama Antrean Persetujuan (Approval Hub).
     */
    public function index(): string
    {
        $req = $this->request ?? service('request');
        $statusParam = $req->getGet('status') ?: 'pending';
        $typeParam   = $req->getGet('type') ?: 'all';
        $searchParam = trim((string) ($req->getGet('q') ?? ''));

        $filters = [
            'status'        => $statusParam,
            'resource_type' => $typeParam,
            'q'             => $searchParam,
        ];

        $kpis  = $this->approvalModel->getKpiMetrics();
        $items = $this->approvalModel->getQueueWithActors($filters, 100, 0);

        // Format items untuk konsumsi view
        $formattedItems = [];
        $now = time();

        foreach ($items as $item) {
            $createdTs = ! empty($item['created_at']) ? strtotime($item['created_at']) : $now;
            $ageDays   = ($now - $createdTs) / 86400;

            // Prioritas dinamis berdasarkan SLA & jenis
            if ($item['status'] === 'pending' && $ageDays >= 3) {
                $priority = 'Tinggi';
                $priorityStatus = 'rejected'; // Warna merah
            } elseif ($item['resource_type'] === 'certificate' || $item['resource_type'] === 'profile_verification') {
                $priority = 'Menengah';
                $priorityStatus = 'review'; // Warna biru/info
            } else {
                $priority = 'Normal';
                $priorityStatus = 'approved'; // Warna hijau/soft
            }

            $requesterLabel = $item['requested_by_name'] ?: 'Sistem / Anonim';
            $requesterEmail = $item['requested_by_email'] ?: '-';

            // Jika resource_type adalah certificate, pastikan nama & email pemohon menampilkan peserta asli
            if (($item['resource_type'] ?? '') === 'certificate') {
                $participant = $this->resolveCertificateParticipant((int) ($item['resource_id'] ?? 0), (string) ($item['notes'] ?? ''));
                if ($participant) {
                    $requesterLabel = $participant['name'];
                    $requesterEmail = $participant['email'] ?: '-';
                }
            }

            $formattedItems[] = array_merge($item, [
                'type_label'         => $this->resolveTypeLabel($item['resource_type'] ?? '', $item['requested_action'] ?? ''),
                'requester_label'    => $requesterLabel,
                'requested_by_email' => $requesterEmail,
                'priority'           => $priority,
                'priority_status'    => $priorityStatus,
                'formatted_date'     => $createdTs ? date('d M Y, H:i', $createdTs) : '-',
                'relative_time'      => $this->formatRelativeTime($createdTs),
            ]);
        }

        return $this->view('Antrean Persetujuan', 'approvals', 'index', [
            'kpis'        => $kpis,
            'items'       => $formattedItems,
            'statusParam' => $statusParam,
            'typeParam'   => $typeParam,
            'searchParam' => $searchParam,
        ]);
    }

    /**
     * Detail Permohonan Persetujuan beserta konteks resource.
     */
    public function detail(string $approvalId)
    {
        $id = (int) $approvalId;
        $approval = $this->approvalModel->findWithActor($id);

        if (! $approval) {
            return redirect()->to(site_url('dashboard/sistem/approval'))
                ->with('error', 'Permohonan persetujuan tidak ditemukan.');
        }

        // Jika approval jenis certificate, pastikan nama & email pemohon adalah peserta asli
        if (($approval['resource_type'] ?? '') === 'certificate') {
            $participant = $this->resolveCertificateParticipant((int) ($approval['resource_id'] ?? 0), (string) ($approval['notes'] ?? ''));
            if ($participant) {
                $approval['requested_by_name']  = $participant['name'];
                $approval['requested_by_email'] = $participant['email'];
                $approval['requested_by_type']  = $participant['profile_type'] ?? 'public';
            }
        }

        // Resolusi konteks resource dinamis
        $resourceContext = $this->resolveResourceContext($approval['resource_type'] ?? '', (int) ($approval['resource_id'] ?? 0), $approval);

        $createdTs  = ! empty($approval['created_at']) ? strtotime($approval['created_at']) : time();
        $reviewedTs = ! empty($approval['reviewed_at']) ? strtotime($approval['reviewed_at']) : null;

        return $this->view('Detail Persetujuan #' . $id, 'approvals', 'detail', [
            'approvalId'      => $id,
            'approval'        => $approval,
            'resourceContext' => $resourceContext,
            'typeLabel'       => $this->resolveTypeLabel($approval['resource_type'] ?? '', $approval['requested_action'] ?? ''),
            'createdDate'     => date('d F Y, H:i', $createdTs) . ' WIB',
            'reviewedDate'    => $reviewedTs ? date('d F Y, H:i', $reviewedTs) . ' WIB' : null,
            'isPending'       => ($approval['status'] ?? '') === 'pending',
            'isApproved'      => ($approval['status'] ?? '') === 'approved',
            'isRejected'      => ($approval['status'] ?? '') === 'rejected',
        ]);
    }

    /**
     * Konfirmasi / Eksekusi Persetujuan.
     */
    public function approve(string $approvalId)
    {
        $req = $this->request ?? service('request');
        $id  = (int) $approvalId;

        $approval = $this->approvalModel->find($id);
        if (! $approval) {
            return redirect()->to(site_url('dashboard/sistem/approval'))
                ->with('error', 'Data persetujuan tidak ditemukan.');
        }

        // Jika bukan POST (misalnya GET atau via URL langsung), render tampilan konfirmasi
        if (strtoupper($req->getMethod()) !== 'POST') {
            return $this->view('Konfirmasi Persetujuan', 'approvals', 'action', [
                'approvalId'  => $id,
                'approval'    => $approval,
                'action'      => 'approve',
                'actionLabel' => 'Setujui',
                'typeLabel'   => $this->resolveTypeLabel($approval['resource_type'] ?? '', $approval['requested_action'] ?? ''),
            ]);
        }

        // Request POST: Eksekusi persetujuan
        $actorId = (int) (session()->get('user_id') ?: (session()->get('id') ?: 13));
        $notes   = trim((string) ($req->getPost('notes') ?? '')) ?: 'Disetujui oleh Administrator.';

        // Anti Self-Approval: tolak jika pemohon atau target user adalah akun sendiri kecuali super_administrator
        $isSuperAdmin = (new \App\Services\Identity\AuthorizationService())->isSuperAdmin();
        $requesterId  = (int) ($approval['requested_by'] ?? 0);
        $targetUserId = (int) ($approval['resource_id'] ?? 0);
        if (! $isSuperAdmin && ($requesterId === $actorId || ($approval['resource_type'] === 'user_registration' && $targetUserId === $actorId))) {
            return redirect()->to(site_url('dashboard/sistem/approval'))
                ->with('error', 'Anti Self-Approval: Anda tidak diizinkan menyetujui permohonan persetujuan milik Anda sendiri.');
        }

        $this->approvalModel->markApproved($id, $actorId, $notes);

        $extraNotice = '';

        // Trigger otomatis jika jenis persetujuan adalah sertifikat
        if (($approval['resource_type'] ?? '') === 'certificate' && ($approval['requested_action'] ?? '') === 'issue_certificate') {
            try {
                $generator = new CertificateGenerator();
                $result = $generator->generateFromApproval($id, $actorId);
                $extraNotice = ' Sertifikat #' . ($result['certificate_number'] ?? '') . ' berhasil diterbitkan.';
            } catch (\Throwable $e) {
                log_message('error', 'Auto-generation certificate failed on approval #' . $id . ': ' . $e->getMessage());
                $extraNotice = ' Persetujuan dicatat, namun penerbitan sertifikat mengalami kendala: ' . $e->getMessage();
            }
        }

        // Trigger otomatis jika jenis persetujuan adalah verifikasi user / profil
        if (in_array($approval['resource_type'] ?? '', ['profile_verification', 'user_registration'], true)) {
            $targetUserId = (int) ($approval['resource_id'] ?? 0);
            if ($targetUserId > 0) {
                db_connect()->table('users')->where('id', $targetUserId)->update([
                    'account_status'    => 'active',
                    'email_verified_at' => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ]);
                $extraNotice = ' Akun pengguna berhasil diaktifkan.';
            }
        }

        // Catat jejak audit
        $this->auditService->log(
            $actorId,
            'approved',
            'approvals',
            'approval',
            $id,
            [
                'resource_type' => $approval['resource_type'],
                'resource_id'   => $approval['resource_id'],
                'action'        => $approval['requested_action'],
                'notes'         => $notes,
            ]
        );

        return redirect()->to(site_url('dashboard/sistem/approval'))
            ->with('message', 'Permohonan persetujuan #' . $id . ' berhasil disetujui.' . $extraNotice);
    }

    /**
     * Konfirmasi / Eksekusi Penolakan.
     */
    public function reject(string $approvalId)
    {
        $req = $this->request ?? service('request');
        $id  = (int) $approvalId;

        $approval = $this->approvalModel->find($id);
        if (! $approval) {
            return redirect()->to(site_url('dashboard/sistem/approval'))
                ->with('error', 'Data persetujuan tidak ditemukan.');
        }

        // Jika bukan POST (misalnya GET atau via URL langsung), render tampilan konfirmasi
        if (strtoupper($req->getMethod()) !== 'POST') {
            return $this->view('Konfirmasi Penolakan', 'approvals', 'action', [
                'approvalId'  => $id,
                'approval'    => $approval,
                'action'      => 'reject',
                'actionLabel' => 'Tolak',
                'typeLabel'   => $this->resolveTypeLabel($approval['resource_type'] ?? '', $approval['requested_action'] ?? ''),
            ]);
        }

        // Request POST: Eksekusi penolakan
        $actorId = (int) (session()->get('user_id') ?: (session()->get('id') ?: 13));
        $reason  = trim((string) ($req->getPost('reason') ?? ''));

        if ($reason === '') {
            $reason = 'Pengajuan ditolak oleh Administrator.';
        }

        $this->approvalModel->markRejected($id, $actorId, $reason);

        // Jika verifikasi profil, update status akun user
        if (in_array($approval['resource_type'] ?? '', ['profile_verification', 'user_registration'], true)) {
            $targetUserId = (int) ($approval['resource_id'] ?? 0);
            if ($targetUserId > 0) {
                db_connect()->table('users')->where('id', $targetUserId)->update([
                    'account_status' => 'rejected',
                    'status_reason'  => $reason,
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Catat jejak audit
        $this->auditService->log(
            $actorId,
            'rejected',
            'approvals',
            'approval',
            $id,
            [
                'resource_type' => $approval['resource_type'],
                'resource_id'   => $approval['resource_id'],
                'action'        => $approval['requested_action'],
                'reason'        => $reason,
            ]
        );

        return redirect()->to(site_url('dashboard/sistem/approval'))
            ->with('message', 'Permohonan persetujuan #' . $id . ' telah ditolak.');
    }

    /**
     * Tindakan Massal (Bulk Action): Approve / Reject banyak pengajuan sekaligus.
     */
    public function bulk()
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

        $req = $this->request ?? service('request');
        $actorId = (int) (session()->get('user_id') ?: (session()->get('id') ?: 13));
        $action  = (string) $req->getPost('bulk_action');
        $rawIds  = (array) $req->getPost('selected_ids');
        $notes   = trim((string) $req->getPost('notes'));

        $ids = array_filter(array_map('intval', $rawIds), static fn ($v) => $v > 0);
        if ($ids === []) {
            return redirect()->to(site_url('dashboard/sistem/approval'))
                ->with('error', 'Tidak ada data persetujuan yang dipilih.');
        }

        if (! in_array($action, ['approve', 'reject'], true)) {
            return redirect()->to(site_url('dashboard/sistem/approval'))
                ->with('error', 'Tindakan massal tidak valid.');
        }

        $now     = date('Y-m-d H:i:s');
        $status  = $action === 'approve' ? 'approved' : 'rejected';
        $defaultNote = $action === 'approve' ? 'Disetujui massal oleh Administrator.' : 'Ditolak massal oleh Administrator.';
        $finalNote   = $notes ?: $defaultNote;

        $successCount = 0;
        $certCount    = 0;

        foreach ($ids as $id) {
            if (function_exists('set_time_limit')) {
                @set_time_limit(120);
            }
            $approval = $this->approvalModel->find($id);
            if (! $approval || $approval['status'] !== 'pending') {
                continue;
            }

            if ($action === 'approve') {
                $this->approvalModel->markApproved($id, $actorId, $finalNote);

                if (($approval['resource_type'] ?? '') === 'certificate' && ($approval['requested_action'] ?? '') === 'issue_certificate') {
                    try {
                        $generator = new CertificateGenerator();
                        $generator->generateFromApproval($id, $actorId);
                        $certCount++;
                    } catch (\Throwable $e) {
                        log_message('error', 'Bulk approval certificate failed for ID ' . $id . ': ' . $e->getMessage());
                    }
                }
            } else {
                $this->approvalModel->markRejected($id, $actorId, $finalNote);
            }

            $this->auditService->log(
                $actorId,
                $status,
                'approvals',
                'approval',
                $id,
                ['bulk' => true, 'notes' => $finalNote]
            );

            $successCount++;
        }

        $msg = "Berhasil memproses {$successCount} pengajuan (" . ($action === 'approve' ? 'Disetujui' : 'Ditolak') . ").";
        if ($certCount > 0) {
            $msg .= " Serta otomatis menerbitkan {$certCount} sertifikat.";
        }

        return redirect()->to(site_url('dashboard/sistem/approval'))->with('message', $msg);
    }

    /**
     * Riwayat Keputusan Persetujuan (Approved / Rejected).
     */
    public function history(): string
    {
        $req = $this->request ?? service('request');
        $statusParam = $req->getGet('status') ?: 'all';
        $searchParam = trim((string) ($req->getGet('q') ?? ''));

        $filters = [
            'q' => $searchParam,
        ];

        if ($statusParam !== 'all') {
            $filters['status'] = $statusParam;
        }

        $builder = db_connect()->table('approvals a')
            ->select([
                'a.*',
                'u.name  AS requested_by_name',
                'u.email AS requested_by_email',
                'r.name  AS reviewed_by_name',
                'r.email AS reviewed_by_email',
            ])
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->join('users r', 'r.id = a.reviewed_by', 'left');

        if ($statusParam === 'all') {
            $builder->whereIn('a.status', ['approved', 'rejected']);
        } else {
            $builder->where('a.status', $statusParam);
        }

        if ($searchParam !== '') {
            $builder->groupStart()
                ->like('u.name', $searchParam)
                ->orLike('r.name', $searchParam)
                ->orLike('a.requested_action', $searchParam)
                ->orLike('a.notes', $searchParam)
                ->orLike('a.reason', $searchParam)
                ->groupEnd();
        }

        $historyList = $builder->orderBy('a.reviewed_at', 'DESC')
            ->limit(100)
            ->get()
            ->getResultArray();

        $formattedHistory = [];
        foreach ($historyList as $item) {
            $reviewedTs = ! empty($item['reviewed_at']) ? strtotime($item['reviewed_at']) : strtotime($item['updated_at']);

            $applicantName = $item['requested_by_name'] ?: 'Sistem';
            if (($item['resource_type'] ?? '') === 'certificate') {
                $participant = $this->resolveCertificateParticipant((int) ($item['resource_id'] ?? 0), (string) ($item['notes'] ?? ''));
                if ($participant) {
                    $applicantName = $participant['name'];
                }
            }

            $formattedHistory[] = array_merge($item, [
                'type_label'     => $this->resolveTypeLabel($item['resource_type'] ?? '', $item['requested_action'] ?? ''),
                'applicant'      => $applicantName,
                'reviewer'       => $item['reviewed_by_name'] ?: 'Administrator',
                'formatted_time' => $reviewedTs ? date('d M Y, H:i', $reviewedTs) : '-',
                'decision_label' => $item['status'] === 'approved' ? 'Disetujui' : 'Ditolak',
                'note_display'   => $item['status'] === 'approved' ? ($item['notes'] ?: 'Disetujui tanpa catatan khusus') : ($item['reason'] ?: 'Ditolak tanpa rincian alasan'),
            ]);
        }

        return $this->view('Riwayat Persetujuan', 'approvals', 'history', [
            'historyList' => $formattedHistory,
            'statusParam' => $statusParam,
            'searchParam' => $searchParam,
            'kpis'        => $this->approvalModel->getKpiMetrics(),
        ]);
    }

    /**
     * Resolusi konteks detail dari objek yang diajukan persetujuannya.
     *
     * @return array<string, mixed>
     */
    private function resolveResourceContext(string $resourceType, int $resourceId, ?array $approval = null): array
    {
        $db = db_connect();

        if ($resourceType === 'certificate') {
            $preferEvent = false;
            if ($approval !== null) {
                $notes = (string) ($approval['notes'] ?? '');
                if (str_contains($notes, '[source:event_registration]') || str_contains($notes, 'event') || str_contains($notes, 'Event')) {
                    $preferEvent = true;
                }
            }

            $lookupLearning = static function () use ($db, $resourceId) {
                $enrollment = $db->table('enrollments e')
                    ->select([
                        'e.id',
                        'e.final_score',
                        'e.completed_at',
                        'e.passed_at',
                        'lc.name AS class_name',
                        'c.title AS course_title',
                        'u.name AS student_name',
                        'u.email AS student_email',
                    ])
                    ->join('learning_classes lc', 'lc.id = e.class_id', 'left')
                    ->join('courses c', 'c.id = lc.course_id', 'left')
                    ->join('users u', 'u.id = e.user_id', 'left')
                    ->where('e.id', $resourceId)
                    ->get()
                    ->getRowArray();

                if ($enrollment) {
                    return [
                        'category'    => 'Pembelajaran Arena Belajar',
                        'title'       => $enrollment['course_title'] ?? 'Kursus PBJ',
                        'subtitle'    => $enrollment['class_name'] ?? 'Kelas Pembelajaran',
                        'participant' => $enrollment['student_name'] ?? '-',
                        'score'       => ! empty($enrollment['final_score']) ? number_format((float) $enrollment['final_score'], 1) : '—',
                        'completed'   => ! empty($enrollment['passed_at']) ? date('d F Y', strtotime($enrollment['passed_at'])) : (! empty($enrollment['completed_at']) ? date('d F Y', strtotime($enrollment['completed_at'])) : '-'),
                        'details'     => [
                            'Nama Kursus'      => $enrollment['course_title'] ?? '-',
                            'Kelas Pelatihan'  => $enrollment['class_name'] ?? '-',
                            'Peserta'          => ($enrollment['student_name'] ?? '-') . ' (' . ($enrollment['student_email'] ?? '-') . ')',
                            'Nilai Akhir'      => ($enrollment['final_score'] ?? '-') . ' / 100',
                            'Status Kelulusan' => 'Memenuhi Syarat (Eligible)',
                        ],
                    ];
                }
                return null;
            };

            $lookupEvent = static function () use ($db, $resourceId) {
                $eventReg = $db->table('event_registrations er')
                    ->select([
                        'er.id',
                        'er.status',
                        'ev.title AS event_title',
                        'ev.start_at',
                        'ev.location',
                        'ev.mode',
                        'u.name AS participant_name',
                        'u.email AS participant_email',
                        'a.status AS attendance_status',
                    ])
                    ->join('events ev', 'ev.id = er.event_id', 'left')
                    ->join('attendances a', 'a.event_registration_id = er.id', 'left')
                    ->join('users u', 'u.id = er.user_id', 'left')
                    ->where('er.id', $resourceId)
                    ->get()
                    ->getRowArray();

                if ($eventReg) {
                    $modeLabel = ucfirst($eventReg['mode'] ?? 'Online');
                    $dateLabel = ! empty($eventReg['start_at']) ? date('d F Y', strtotime($eventReg['start_at'])) : '-';
                    $attLabel  = ($eventReg['attendance_status'] === 'present') ? 'Tervalidasi Hadir' : 'Menunggu / Belum Presensi';
                    return [
                        'category'    => 'Agenda / Event PBJ',
                        'title'       => $eventReg['event_title'] ?? 'Agenda PBJ',
                        'subtitle'    => $eventReg['location'] ?: $modeLabel,
                        'participant' => $eventReg['participant_name'] ?? '-',
                        'score'       => ($eventReg['attendance_status'] === 'present') ? 'Hadir' : 'Belum Hadir',
                        'completed'   => $dateLabel,
                        'details'     => [
                            'Judul Event'       => $eventReg['event_title'] ?? '-',
                            'Peserta'           => ($eventReg['participant_name'] ?? '-') . ' (' . ($eventReg['participant_email'] ?? '-') . ')',
                            'Lokasi / Mode'     => ($eventReg['location'] ?: $modeLabel),
                            'Status Kehadiran'  => $attLabel,
                        ],
                    ];
                }
                return null;
            };

            if ($preferEvent) {
                $res = $lookupEvent();
                if ($res) {
                    return $res;
                }
                $res = $lookupLearning();
                if ($res) {
                    return $res;
                }
            } else {
                $res = $lookupLearning();
                if ($res) {
                    return $res;
                }
                $res = $lookupEvent();
                if ($res) {
                    return $res;
                }
            }
        }

        if (in_array($resourceType, ['profile_verification', 'user_registration'], true)) {
            $user = $db->table('users u')
                ->select([
                    'u.id',
                    'u.name',
                    'u.email',
                    'u.phone',
                    'u.profile_type',
                    'u.account_status',
                    'u.created_at',
                    'ou.name AS opd_name',
                    'ap.nip',
                    'ap.position',
                ])
                ->join('asn_profiles ap', 'ap.user_id = u.id', 'left')
                ->join('organization_units ou', 'ou.id = ap.organization_unit_id', 'left')
                ->where('u.id', $resourceId)
                ->get()
                ->getRowArray();

            if ($user) {
                return [
                    'category'    => 'Verifikasi Akun Pengguna',
                    'title'       => $user['name'],
                    'subtitle'    => strtoupper($user['profile_type'] ?? 'UMUM'),
                    'participant' => $user['name'],
                    'score'       => $user['account_status'],
                    'completed'   => date('d F Y', strtotime($user['created_at'])),
                    'details'     => [
                        'Nama Lengkap' => $user['name'],
                        'Email Dinas'  => $user['email'],
                        'NIP'          => $user['nip'] ?: '— (Bukan ASN)',
                        'Jabatan'      => $user['position'] ?: '—',
                        'Instansi/OPD' => $user['opd_name'] ?: 'Pemerintah Provinsi Lampung',
                        'Tipe Profil'  => strtoupper($user['profile_type']),
                    ],
                ];
            }
        }

        // Fallback generik
        return [
            'category'    => 'Objek Permintaan Sistem',
            'title'       => ucfirst(str_replace('_', ' ', $resourceType)),
            'subtitle'    => 'ID Objek: #' . $resourceId,
            'participant' => 'Entitas Terdaftar',
            'score'       => 'Siap Tinjau',
            'completed'   => date('d F Y'),
            'details'     => [
                'Tipe Resource' => $resourceType,
                'ID Resource'   => '#' . $resourceId,
                'Keterangan'    => 'Pengajuan tercatat pada antrean terpadu sistem.',
            ],
        ];
    }

    /**
     * Label representatif untuk jenis permintaan.
     */
    private function resolveTypeLabel(string $type, string $action): string
    {
        return match ($type) {
            'certificate'          => 'Verifikasi Sertifikat Kelulusan',
            'profile_verification' => 'Verifikasi Profil & Akun Pengguna',
            'user_registration'    => 'Registrasi Akun Baru',
            'role_request'         => 'Permohonan Peningkatan Role Akses',
            'content_publish'      => 'Persetujuan Publikasi Konten',
            'event_publish'        => 'Persetujuan Publikasi Event',
            'test_resource'        => 'Uji Integrasi Sistem',
            default                => ucfirst(str_replace(['_', '-'], ' ', $type)) . ($action ? ' (' . ucfirst($action) . ')' : ''),
        };
    }

    /**
     * Format waktu relatif (e.g. 5 menit lalu, 2 jam lalu).
     */
    /**
     * Resolusi identitas peserta sebenarnya untuk approval jenis certificate.
     *
     * @return array{id: int, name: string, email: string, profile_type: string}|null
     */
    private function resolveCertificateParticipant(int $resourceId, string $notes): ?array
    {
        $db = db_connect();
        $isEvent = str_contains($notes, '[source:event_registration]') || str_contains($notes, 'event') || str_contains($notes, 'Event');

        if ($isEvent) {
            $reg = $db->table('event_registrations er')
                ->select('u.id, u.name, u.email, u.profile_type')
                ->join('users u', 'u.id = er.user_id', 'inner')
                ->where('er.id', $resourceId)
                ->get()->getRowArray();
            if ($reg) {
                return $reg;
            }
        }

        // Learning lookup
        $enr = $db->table('enrollments e')
            ->select('u.id, u.name, u.email, u.profile_type')
            ->join('users u', 'u.id = e.user_id', 'inner')
            ->where('e.id', $resourceId)
            ->get()->getRowArray();
        if ($enr) {
            return $enr;
        }

        // Event fallback jika tadi belum dicoba
        if (! $isEvent) {
            $reg = $db->table('event_registrations er')
                ->select('u.id, u.name, u.email, u.profile_type')
                ->join('users u', 'u.id = er.user_id', 'inner')
                ->where('er.id', $resourceId)
                ->get()->getRowArray();
            if ($reg) {
                return $reg;
            }
        }

        return null;
    }

    private function formatRelativeTime(int $timestamp): string
    {
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Baru saja';
        }
        if ($diff < 3600) {
            return floor($diff / 60) . ' mnt lalu';
        }
        if ($diff < 86400) {
            return floor($diff / 3600) . ' jam lalu';
        }
        if ($diff < 604800) {
            return floor($diff / 86400) . ' hari lalu';
        }

        return date('d/m/Y', $timestamp);
    }

    private function view(
        string $pageTitle,
        string $activeSubmenu,
        string $viewName,
        array $data = []
    ): string {
        return view('dashboard/approvals/' . $viewName, array_merge([
            'title'         => $pageTitle . ' | BegawiPengadaan',
            'pageTitle'     => $pageTitle,
            'activeMenu'    => 'system',
            'activeSubmenu' => $activeSubmenu,
            'breadcrumbs'   => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Sistem'],
                ['label' => $pageTitle],
            ],
        ], $data));
    }
}

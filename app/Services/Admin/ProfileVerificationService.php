<?php

namespace App\Services\Admin;

use App\Models\ProfileVerificationModel;
use App\Models\UserModel;
use App\Services\AuditService;
use App\Services\NotificationService;

/**
 * Profile verification service (Manajemen Akses → Verifikasi Profil).
 *
 * Implements the approve/reject/revision flow:
 *   pending  -> in_review -> approved    -> users.account_status = 'active'
 *                              rejected    -> users.account_status = 'rejected'
 *                              revision_required -> users.account_status = 'revision_required'
 *
 * Every decision writes audit_logs AND workflow_transitions. Reject and
 * revision require a reason (CLAUDE.md §9 / §12).
 */
class ProfileVerificationService
{
    public function __construct(
        private readonly ProfileVerificationModel $verifications = new ProfileVerificationModel(),
        private readonly UserModel $users = new UserModel(),
        private readonly AuditService $audit = new AuditService(),
        private readonly UserManagementService $userService = new UserManagementService(),
        private readonly NotificationService $notificationService = new NotificationService(),
    ) {}

    public function queue(array $filters = []): array
    {
        $builder = db_connect()->table('profile_verifications pv')
            ->select('pv.*, u.name AS user_name, u.email, u.username, u.profile_type')
            ->join('users u', 'u.id = pv.user_id', 'left')
            ->orderBy('pv.created_at', 'DESC');

        if (! empty($filters['status'])) {
            $builder->where('pv.status', $filters['status']);
        }

        return $builder->get()->getResultArray();
    }

    public function find(int $id): ?array
    {
        return $this->verifications->find($id);
    }

    public function findByUser(int $userId): ?array
    {
        return $this->verifications
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function approve(int $verificationId, ?int $actorId = null, ?string $notes = null): bool
    {
        $row = $this->verifications->find($verificationId);
        if (! $row || ($row['status'] ?? '') !== 'pending') {
            return false;
        }

        $userId = (int) $row['user_id'];
        $now = date('Y-m-d H:i:s');

        $affected = $this->verifications
            ->where('id', $verificationId)
            ->where('status', 'pending')
            ->set([
                'status'      => 'approved',
                'reviewed_by' => $actorId,
                'reviewed_at' => $now,
                'notes'       => $notes,
                'updated_at'  => $now,
            ])->update();

        if (! $affected || db_connect()->affectedRows() < 1) {
            return false;
        }

        $this->userService->updateStatus($userId, 'active', $actorId, 'profile_verification.approved');
        $this->userService->recordTransition(
            'profile_verification',
            $verificationId,
            (string) $row['status'],
            'approved',
            'profile_verification.approve',
            $actorId,
            null,
            $notes,
        );

        $this->audit->log($actorId, 'profile_verification.approve', 'profile_verifications', 'profile_verification', $verificationId);

        $this->notificationService->send(
            $userId,
            'account_approved',
            'Akun Anda Telah Disetujui',
            'Selamat, pendaftaran akun Anda telah disetujui oleh Operator/Admin. Anda kini dapat masuk dan mengakses seluruh fitur pembelajaran BegawiPengadaan.'
        );

        return true;
    }

    public function reject(int $verificationId, string $reason, ?int $actorId = null, ?string $notes = null): bool
    {
        if (trim($reason) === '') {
            throw new \InvalidArgumentException('Alasan penolakan wajib diisi.');
        }

        $row = $this->verifications->find($verificationId);
        if (! $row || ($row['status'] ?? '') !== 'pending') {
            return false;
        }

        $userId = (int) $row['user_id'];
        $now = date('Y-m-d H:i:s');

        $affected = $this->verifications
            ->where('id', $verificationId)
            ->where('status', 'pending')
            ->set([
                'status'      => 'rejected',
                'reviewed_by' => $actorId,
                'reviewed_at' => $now,
                'reason'      => $reason,
                'notes'       => $notes,
                'updated_at'  => $now,
            ])->update();

        if (! $affected || db_connect()->affectedRows() < 1) {
            return false;
        }

        $this->userService->updateStatus($userId, 'rejected', $actorId, $reason);
        $this->userService->recordTransition(
            'profile_verification',
            $verificationId,
            (string) $row['status'],
            'rejected',
            'profile_verification.reject',
            $actorId,
            $reason,
            $notes,
        );

        $this->audit->log($actorId, 'profile_verification.reject', 'profile_verifications', 'profile_verification', $verificationId);

        $this->notificationService->send(
            $userId,
            'account_rejected',
            'Pengajuan Verifikasi Akun Ditolak',
            'Mohon maaf, pengajuan verifikasi akun Anda belum disetujui dengan alasan: ' . $reason
        );

        return true;
    }

    public function requestRevision(int $verificationId, string $note, ?int $actorId = null): bool
    {
        if (trim($note) === '') {
            throw new \InvalidArgumentException('Catatan revisi wajib diisi.');
        }

        $row = $this->verifications->find($verificationId);
        if (! $row) {
            return false;
        }

        $userId = (int) $row['user_id'];
        $now = date('Y-m-d H:i:s');

        $this->verifications->update($verificationId, [
            'status'      => 'revision_required',
            'reviewed_by' => $actorId,
            'reviewed_at' => $now,
            'notes'       => $note,
            'updated_at'  => $now,
        ]);

        $this->userService->updateStatus($userId, 'revision_required', $actorId, $note);
        $this->userService->recordTransition(
            'profile_verification',
            $verificationId,
            (string) $row['status'],
            'revision_required',
            'profile_verification.revision',
            $actorId,
            $note,
        );

        $this->audit->log($actorId, 'profile_verification.revision', 'profile_verifications', 'profile_verification', $verificationId);
        return true;
    }
}

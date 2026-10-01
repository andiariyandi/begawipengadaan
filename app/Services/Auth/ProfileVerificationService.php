<?php

namespace App\Services\Auth;

use App\Models\UserModel;
use App\Models\AuditLogModel;
use CodeIgniter\Database\BaseConnection;
use RuntimeException;
use Throwable;

class ProfileVerificationService
{
    private UserModel $userModel;
    private AuditLogModel $auditLogModel;
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->userModel = new UserModel();
        $this->auditLogModel = new AuditLogModel();
        $this->db = $db ?? db_connect();
    }

    public function approve(int $verificationId, int $reviewedBy, ?string $notes = null): bool
    {
        return $this->review($verificationId, $reviewedBy, 'approved', null, $notes);
    }

    public function reject(int $verificationId, int $reviewedBy, string $reason): bool
    {
        return $this->review($verificationId, $reviewedBy, 'rejected', $reason);
    }

    public function revisionRequired(int $verificationId, int $reviewedBy, string $reason): bool
    {
        return $this->review($verificationId, $reviewedBy, 'revision_required', $reason);
    }

    private function review(int $verificationId, int $reviewedBy, string $status, ?string $reason, ?string $notes = null): bool
    {
        $verification = $this->db->table('profile_verifications')
            ->where('id', $verificationId)->get()->getRowArray();
        if (! $verification || ! in_array($verification['status'], ['pending', 'in_review'], true)) {
            throw new RuntimeException('Verifikasi profil tidak ditemukan atau sudah diproses.');
        }

        $this->db->transBegin();

        try {
            $now = date('Y-m-d H:i:s');
            $this->db->table('profile_verifications')->where('id', $verificationId)->update([
                'status' => $status,
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => $now,
                'reason' => $reason,
                'notes' => $notes,
            ]);

            $accountStatus = [
                'approved' => 'active',
                'rejected' => 'rejected',
                'revision_required' => 'revision_required',
            ][$status];
            $this->userModel->update($verification['user_id'], [
                'account_status' => $accountStatus,
                'status_reason' => $reason,
            ]);

            $this->auditLogModel->insert([
                'actor_id' => $reviewedBy,
                'action' => 'profile_verification_' . $status,
                'resource_type' => 'profile_verification',
                'resource_id' => $verificationId,
                'reason' => $reason,
                'result' => 'success',
                'created_at' => $now,
            ]);

            $this->assertTransaction();
            $this->db->transCommit();
            return true;
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    private function assertTransaction(): void
    {
        if (! $this->db->transStatus()) {
            throw new RuntimeException('Verifikasi profil gagal disimpan.');
        }
    }
}

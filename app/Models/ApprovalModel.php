<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel approvals (v4).
 *
 * Schema v4 (kolom yang dipakai):
 *  - resource_type       VARCHAR  (mis. "user_registration", "learning_enrollment", "event_registration")
 *  - resource_id         UNSIGNED
 *  - requested_action    VARCHAR  (mis. "review", "issue")
 *  - status              ENUM/VARCHAR ("pending" | "approved" | "rejected")
 *  - requested_by        UNSIGNED (user yang mengajukan)
 *  - reviewed_by         UNSIGNED (nullable)
 *  - reason              TEXT/VARCHAR (alasan penolakan / catatan reviewer)
 *  - notes               TEXT (catatan requester)
 *  - expires_at          DATETIME
 *  - reviewed_at         DATETIME
 *  - created_at, updated_at
 *
 * CATATAN: Model ini dipakai oleh Certificate flow (learning_enrollment,
 * event_registration) dan flow lain (mis. user_registration). Method-method
 * di sini generic, tidak mengasumsikan resource_type tertentu kecuali
 * disebutkan eksplisit.
 */
class ApprovalModel extends Model
{
    protected $table         = 'approvals';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'resource_type',
        'resource_id',
        'requested_action',
        'status',
        'requested_by',
        'reviewed_by',
        'reason',
        'notes',
        'expires_at',
        'reviewed_at',
    ];

    /**
     * Ambil semua approval pending untuk resource_type tertentu, plus join
     * label user (requested_by) agar siap ditampilkan di view.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listPendingForResourceType(string $resourceType, int $limit = 200): array
    {
        return $this->db->table('approvals a')
            ->select([
                'a.*',
                'u.name  AS requested_by_name',
                'u.email AS requested_by_email',
            ])
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->where('a.resource_type', $resourceType)
            ->where('a.status', 'pending')
            ->orderBy('a.id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil approval dengan join label user (untuk halaman detail).
     */
    public function findWithActor(int $id): ?array
    {
        $row = $this->db->table('approvals a')
            ->select(['a.*', 'u.name AS requested_by_name', 'u.email AS requested_by_email'])
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->where('a.id', $id)
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    /**
     * Cek apakah ada approval yang masih aktif (pending atau approved)
     * untuk resource tertentu dan action tertentu. Dipakai untuk idempotency.
     */
    public function hasActive(string $resourceType, int $resourceId, ?string $requestedAction = null): bool
    {
        $builder = $this->builder();
        $builder->where('resource_type', $resourceType)
            ->where('resource_id', $resourceId)
            ->whereIn('status', ['pending', 'approved']);
        if ($requestedAction !== null) {
            $builder->where('requested_action', $requestedAction);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Tandai approval disetujui oleh reviewer.
     */
    public function markApproved(int $id, int $reviewerId, ?string $notes = null): bool
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            'status'      => 'approved',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => $now,
        ];
        if ($notes !== null) {
            $data['notes'] = $notes;
        }
        return $this->update($id, $data);
    }

    /**
     * Tandai approval ditolak oleh reviewer. Reason wajib.
     */
    public function markRejected(int $id, int $reviewerId, string $reason): bool
    {
        $now = date('Y-m-d H:i:s');
        return $this->update($id, [
            'status'      => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => $now,
            'reason'      => $reason,
        ]);
    }

    /**
     * Daftar approval (semua status) untuk halaman history audit.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listRecent(int $limit = 200): array
    {
        return $this->db->table('approvals a')
            ->select(['a.*', 'u.name AS requested_by_name', 'r.name AS reviewed_by_name'])
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->join('users r', 'r.id = a.reviewed_by', 'left')
            ->orderBy('a.id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
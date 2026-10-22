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
            ->select([
                'a.*',
                'u.name  AS requested_by_name',
                'u.email AS requested_by_email',
                'u.profile_type AS requested_by_type',
                'r.name  AS reviewed_by_name',
                'r.email AS reviewed_by_email',
            ])
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->join('users r', 'r.id = a.reviewed_by', 'left')
            ->where('a.id', $id)
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    /**
     * Ambil antrean persetujuan dengan filter dinamis dan join data user.
     *
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function getQueueWithActors(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $builder = $this->db->table('approvals a')
            ->select([
                'a.*',
                'u.name  AS requested_by_name',
                'u.email AS requested_by_email',
                'u.profile_type AS requested_by_type',
                'r.name  AS reviewed_by_name',
                'r.email AS reviewed_by_email',
            ])
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->join('users r', 'r.id = a.reviewed_by', 'left');

        $this->applyFilters($builder, $filters);

        return $builder->orderBy('a.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Hitung total data antrean dengan filter.
     *
     * @param array<string, mixed> $filters
     */
    public function countQueue(array $filters = []): int
    {
        $builder = $this->db->table('approvals a')
            ->join('users u', 'u.id = a.requested_by', 'left');

        $this->applyFilters($builder, $filters);

        return $builder->countAllResults();
    }

    /**
     * Ambil metrik ringkasan KPI persetujuan secara real-time.
     *
     * @return array<string, int>
     */
    public function getKpiMetrics(): array
    {
        $todayStart   = date('Y-m-d 00:00:00');
        $todayEnd     = date('Y-m-d 23:59:59');
        $slaThreshold = date('Y-m-d H:i:s', strtotime('-3 days'));

        $pending = $this->db->table('approvals')
            ->where('status', 'pending')
            ->countAllResults();

        $approvedToday = $this->db->table('approvals')
            ->where('status', 'approved')
            ->where('reviewed_at >=', $todayStart)
            ->where('reviewed_at <=', $todayEnd)
            ->countAllResults();

        $rejectedToday = $this->db->table('approvals')
            ->where('status', 'rejected')
            ->where('reviewed_at >=', $todayStart)
            ->where('reviewed_at <=', $todayEnd)
            ->countAllResults();

        $slaOverdue = $this->db->table('approvals')
            ->where('status', 'pending')
            ->where('created_at <=', $slaThreshold)
            ->countAllResults();

        $totalHistory = $this->db->table('approvals')
            ->whereIn('status', ['approved', 'rejected'])
            ->countAllResults();

        return [
            'pending'        => (int) $pending,
            'approved_today' => (int) $approvedToday,
            'rejected_today' => (int) $rejectedToday,
            'sla_overdue'    => (int) $slaOverdue,
            'total_history'  => (int) $totalHistory,
        ];
    }

    /**
     * Helper privat untuk menerapkan filter pada builder query.
     */
    private function applyFilters($builder, array $filters): void
    {
        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $builder->where('a.status', (string) $filters['status']);
        }

        if (! empty($filters['resource_type']) && $filters['resource_type'] !== 'all') {
            $builder->where('a.resource_type', (string) $filters['resource_type']);
        }

        if (! empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $builder->groupStart()
                ->like('u.name', $q)
                ->orLike('u.email', $q)
                ->orLike('a.requested_action', $q)
                ->orLike('a.notes', $q)
                ->orLike('a.reason', $q);
            if (is_numeric($q)) {
                $builder->orWhere('a.id', (int) $q)
                    ->orWhere('a.resource_id', (int) $q);
            }
            $builder->groupEnd();
        }
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

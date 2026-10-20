<?php

namespace App\Services\Admin;

use App\Models\ProcurementRoleModel;
use App\Models\UserProcurementRoleModel;
use App\Services\AuditService;

/**
 * Procurement role + user-procurement-role assignment service.
 *
 * Per CLAUDE.md §16, procurement roles are SEPARATE from RBAC roles and
 * do not auto-grant dashboard or workspace access. They are declarative
 * metadata about their position in the procurement process.
 */
class ProcurementRoleService
{
    public function __construct(
        private readonly ProcurementRoleModel $roles = new ProcurementRoleModel(),
        private readonly UserProcurementRoleModel $assignments = new UserProcurementRoleModel(),
        private readonly AuditService $audit = new AuditService(),
    ) {}

    public function listRoles(): array
    {
        $roles = $this->roles->orderBy('name')->findAll();
        if (empty($roles)) {
            return [];
        }

        $counts = db_connect()->table('user_procurement_roles')
            ->select('procurement_role_id, COUNT(DISTINCT user_id) as total_users')
            ->groupBy('procurement_role_id')
            ->get()->getResultArray();

        $countMap = array_column($counts, 'total_users', 'procurement_role_id');

        foreach ($roles as &$r) {
            $r['users_count'] = (int) ($countMap[$r['id']] ?? 0);
        }
        unset($r);

        return $roles;
    }

    public function getUserCountForRole(int $roleId): int
    {
        return (int) db_connect()->table('user_procurement_roles')
            ->where('procurement_role_id', $roleId)
            ->countAllResults();
    }

    public function findRole(int $id): ?array
    {
        return $this->roles->find($id);
    }

    public function createRole(array $data, ?int $actorId = null): int
    {
        $id = (int) $this->roles->insert($this->rolePayload($data), true);
        $this->audit->log($actorId, 'procurement_role.create', 'procurement_roles', 'procurement_role', $id);
        return $id;
    }

    public function updateRole(int $id, array $data, ?int $actorId = null): bool
    {
        $ok = (bool) $this->roles->update($id, $this->rolePayload($data, isUpdate: true));
        if ($ok) {
            $this->audit->log($actorId, 'procurement_role.update', 'procurement_roles', 'procurement_role', $id);
        }
        return $ok;
    }

    public function deleteRole(int $id, ?int $actorId = null): bool
    {
        $role = $this->findRole($id);
        $ok = (bool) $this->roles->delete($id);
        if ($ok) {
            $this->audit->log($actorId, 'procurement_role.delete', 'procurement_roles', 'procurement_role', $id, [
                'key'  => $role['key'] ?? null,
                'name' => $role['name'] ?? null,
            ]);
        }
        return $ok;
    }

    public function listAssignments(array $filters = []): array
    {
        $supportsUnit = $this->hasColumn('user_procurement_roles', 'organization_unit_id');

        $builder = db_connect()->table('user_procurement_roles upr')
            ->join('users u', 'u.id = upr.user_id', 'left')
            ->join('procurement_roles pr', 'pr.id = upr.procurement_role_id', 'left');

        if ($supportsUnit) {
            $builder->select('upr.*, u.name AS user_name, u.email, pr.key AS role_key, pr.name AS role_name, ou.name AS unit_name')
                ->join('organization_units ou', 'ou.id = upr.organization_unit_id', 'left');
        } else {
            // Skema standar live: OPD pengguna diambil dari asn_profiles tanpa kolom upr.organization_unit_id
            $builder->select("upr.*, u.name AS user_name, u.email, pr.key AS role_key, pr.name AS role_name, COALESCE(ou.name, ap.unit_name, '-') AS unit_name")
                ->join('asn_profiles ap', 'ap.user_id = upr.user_id', 'left')
                ->join('organization_units ou', 'ou.id = ap.organization_unit_id', 'left');
        }

        $builder->orderBy('upr.created_at', 'DESC');

        if (! empty($filters['verification_status'])) {
            $builder->where('upr.verification_status', $filters['verification_status']);
        }
        if (! empty($filters['user_id'])) {
            $builder->where('upr.user_id', (int) $filters['user_id']);
        }

        return $builder->get()->getResultArray();
    }

    public function assign(int $userId, int $procurementRoleId, ?int $organizationUnitId, ?int $actorId = null): int
    {
        $db = db_connect();

        $existing = $db->table('user_procurement_roles')
            ->where('user_id', $userId)
            ->where('procurement_role_id', $procurementRoleId)
            ->get()
            ->getRowArray();

        $now = date('Y-m-d H:i:s');

        // NOTE: DB live belum punya kolom organization_unit_id; tolerate if missing.
        $supportsUnit = $this->hasColumn('user_procurement_roles', 'organization_unit_id');

        if ($existing) {
            $update = [
                'verification_status' => 'pending',
                'assignment_start_at' => $now,
                'verified_by'         => null,
                'verified_at'         => null,
                'updated_at'          => $now,
            ];
            if ($supportsUnit) {
                $update['organization_unit_id'] = $organizationUnitId;
            }
            $db->table('user_procurement_roles')->where('id', $existing['id'])->update($update);
            $id = (int) $existing['id'];
        } else {
            $insert = [
                'user_id'             => $userId,
                'procurement_role_id' => $procurementRoleId,
                'verification_status' => 'pending',
                'assignment_start_at' => $now,
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
            if ($supportsUnit) {
                $insert['organization_unit_id'] = $organizationUnitId;
            }
            $id = (int) $db->table('user_procurement_roles')->insert($insert, true);
        }

        if (! $supportsUnit && $organizationUnitId) {
            $asnProfile = $db->table('asn_profiles')->where('user_id', $userId)->get()->getRowArray();
            if ($asnProfile && empty($asnProfile['organization_unit_id'])) {
                $db->table('asn_profiles')->where('user_id', $userId)->update([
                    'organization_unit_id' => $organizationUnitId,
                    'updated_at'           => $now,
                ]);
            }
        }

        $this->audit->log($actorId, 'procurement_role.assign', 'user_procurement_roles', 'user_procurement_role', $id);
        return $id;
    }

    private function hasColumn(string $table, string $column): bool
    {
        return in_array($column, db_connect()->getFieldNames($table), true);
    }

    public function getAssignmentErrors(int $assignmentId): array
    {
        return $this->assignments->errors();
    }

    public function verify(int $assignmentId, string $newStatus, ?int $actorId = null): bool
    {
        if (! in_array($newStatus, ['verified', 'rejected'], true)) {
            throw new \InvalidArgumentException('Status verifikasi tidak valid.');
        }

        $ok = (bool) $this->assignments->update($assignmentId, [
            'verification_status' => $newStatus,
            'verified_by'         => $actorId,
            'verified_at'         => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        if ($ok) {
            $this->audit->log($actorId, 'procurement_role.verify', 'user_procurement_roles', 'user_procurement_role', $assignmentId, ['status' => $newStatus]);
        }
        return $ok;
    }

    private function rolePayload(array $data, bool $isUpdate = false): array
    {
        $payload = [];
        foreach (['key', 'name', 'description'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }
        if (array_key_exists('is_active', $data)) {
            $payload['is_active'] = (int) ($data['is_active'] ?? 1);
        }
        $payload['updated_at'] = date('Y-m-d H:i:s');
        if (! $isUpdate) {
            $payload['created_at'] = date('Y-m-d H:i:s');
        }
        return $payload;
    }
}
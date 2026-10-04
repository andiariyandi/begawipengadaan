<?php

namespace App\Services\Admin;

use App\Models\OrganizationUnitModel;
use App\Services\AuditService;

/**
 * Organization unit service (Manajemen Akses → Unit Organisasi).
 *
 * Hierarchical CRUD with parent_id validation (no self-parent, no cycle).
 * Deactivate preferred over physical delete (CLAUDE.md §17).
 */
class OrganizationService
{
    public function __construct(
        private readonly OrganizationUnitModel $units = new OrganizationUnitModel(),
        private readonly AuditService $audit = new AuditService(),
    ) {}

    public function listTree(): array
    {
        return db_connect()->table('organization_units u')
            ->select('u.*, p.name AS parent_name')
            ->join('organization_units p', 'p.id = u.parent_id', 'left')
            ->orderBy('u.sort_order, u.name')
            ->get()
            ->getResultArray();
    }

    public function find(int $id): ?array
    {
        return $this->units->find($id);
    }

    public function create(array $data, ?int $actorId = null): int
    {
        $this->validateParent(null, $data['parent_id'] ?? null);

        $id = (int) $this->units->insert($this->payload($data), true);
        $this->audit->log($actorId, 'organization_unit.create', 'organization_units', 'organization_unit', $id);
        return $id;
    }

    public function update(int $id, array $data, ?int $actorId = null): bool
    {
        $this->validateParent($id, $data['parent_id'] ?? null);

        $ok = (bool) $this->units->update($id, $this->payload($data, isUpdate: true));
        if ($ok) {
            $this->audit->log($actorId, 'organization_unit.update', 'organization_units', 'organization_unit', $id);
        }
        return $ok;
    }

    public function deactivate(int $id, ?int $actorId = null): bool
    {
        $ok = (bool) $this->units->update($id, ['is_active' => 0]);
        if ($ok) {
            $this->audit->log($actorId, 'organization_unit.deactivate', 'organization_units', 'organization_unit', $id);
        }
        return $ok;
    }

    public function activate(int $id, ?int $actorId = null): bool
    {
        $ok = (bool) $this->units->update($id, ['is_active' => 1]);
        if ($ok) {
            $this->audit->log($actorId, 'organization_unit.activate', 'organization_units', 'organization_unit', $id);
        }
        return $ok;
    }

    private function validateParent(?int $ignoreId, $parentId): void
    {
        if (empty($parentId)) {
            return;
        }

        $parentId = (int) $parentId;
        if ($parentId <= 0) {
            return;
        }

        if ($ignoreId !== null && $parentId === $ignoreId) {
            throw new \InvalidArgumentException('Unit organisasi tidak boleh menjadi induk dirinya sendiri.');
        }

        // Cycle check: parent must not have $ignoreId in its ancestor chain.
        $cursor = $parentId;
        $db = db_connect();
        $seen = [];
        while ($cursor) {
            if (in_array($cursor, $seen, true)) {
                throw new \InvalidArgumentException('Strukmen akses parent membentuk siklus.');
            }
            $seen[] = $cursor;
            if ($ignoreId !== null && $cursor === $ignoreId) {
                throw new \InvalidArgumentException('Parent tidak valid (siklus hierarki).');
            }
            $row = $db->table('organization_units')->select('parent_id')->where('id', $cursor)->get()->getRowArray();
            $cursor = $row ? (int) ($row['parent_id'] ?? 0) : 0;
        }
    }

    private function payload(array $data, bool $isUpdate = false): array
    {
        $payload = [];
        foreach (['code', 'name', 'type', 'parent_id', 'address'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field] === '' ? null : $data[$field];
            }
        }
        if (array_key_exists('sort_order', $data)) {
            $payload['sort_order'] = (int) ($data['sort_order'] ?? 0);
        }
        if (array_key_exists('is_active', $data)) {
            $payload['is_active'] = (int) ($data['is_active'] ?? 1);
        }
        if (! $isUpdate) {
            $payload['created_at'] = date('Y-m-d H:i:s');
        }
        $payload['updated_at'] = date('Y-m-d H:i:s');
        return $payload;
    }
}
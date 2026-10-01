<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationUnitModel extends Model
{
    protected $table = 'organization_units';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'parent_id',
        'code',
        'name',
        'type',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $validationRules = [
        'parent_id' => 'permit_empty|is_natural_no_zero',
        'code' => 'required|max_length[64]|is_unique[organization_units.code,id,{id}]',
        'name' => 'required|max_length[255]',
        'type' => 'required|max_length[64]',
        'is_active' => 'permit_empty|in_list[0,1]',
        'sort_order' => 'permit_empty|is_natural',
    ];

    public function canSetParent(int $unitId, ?int $parentId): bool
    {
        if ($parentId === null || $parentId === 0) {
            return true;
        }

        if ($unitId === $parentId) {
            return false;
        }

        $visited = [];
        $currentId = $parentId;
        while ($currentId !== null && $currentId !== 0) {
            if (isset($visited[$currentId])) {
                return false;
            }
            $visited[$currentId] = true;

            $parent = $this->select('id, parent_id')->find($currentId);
            if (!$parent) {
                return false;
            }
            if ((int) $parent['id'] === $unitId) {
                return false;
            }
            $currentId = $parent['parent_id'] !== null ? (int) $parent['parent_id'] : null;
        }

        return true;
    }
}

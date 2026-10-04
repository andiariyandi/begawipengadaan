<?php

namespace App\Models\Regulation;

use CodeIgniter\Model;

class RegulationCategoryModel extends Model
{
    protected $table         = 'regulation_categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'name', 'slug', 'description', 'is_active',
    ];

    public function listActiveWithCount(): array
    {
        return $this->select('regulation_categories.*,
                          (SELECT COUNT(*) FROM regulations WHERE regulations.category_id = regulation_categories.id AND regulations.deleted_at IS NULL) AS regulation_count')
            ->where('is_active', 1)
            ->orderBy('name')
            ->findAll();
    }

    public function listAll(): array
    {
        return $this->select('regulation_categories.*,
                          (SELECT COUNT(*) FROM regulations WHERE regulations.category_id = regulation_categories.id AND regulations.deleted_at IS NULL) AS regulation_count')
            ->orderBy('name')
            ->findAll();
    }
}
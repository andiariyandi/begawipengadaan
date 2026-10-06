<?php

namespace App\Models\Knowledge;

use CodeIgniter\Model;

class KnowledgeCategoryModel extends Model
{
    protected $table         = 'knowledge_categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'parent_id', 'name', 'slug', 'description', 'sort_order', 'is_active',
    ];

    public function activeFlat(): array
    {
        return $this->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
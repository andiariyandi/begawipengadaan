<?php

namespace App\Models\Regulation;

use CodeIgniter\Model;

class ProcurementStageModel extends Model
{
    protected $table         = 'procurement_stages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'key', 'name', 'description', 'sort_order', 'is_active',
    ];

    public function listActive(): array
    {
        return $this->where('is_active', 1)->orderBy('sort_order, name')->findAll();
    }
}
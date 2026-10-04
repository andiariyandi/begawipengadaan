<?php

namespace App\Models\Regulation;

use CodeIgniter\Model;

class RegulationProcurementStageModel extends Model
{
    protected $table         = 'regulation_procurement_stages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'regulation_id', 'procurement_stage_id', 'relevance', 'created_at',
    ];
}
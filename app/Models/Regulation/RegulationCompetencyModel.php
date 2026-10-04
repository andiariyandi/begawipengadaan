<?php

namespace App\Models\Regulation;

use CodeIgniter\Model;

class RegulationCompetencyModel extends Model
{
    protected $table         = 'regulation_competencies';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'regulation_id', 'competency_id', 'relevance', 'created_at',
    ];
}
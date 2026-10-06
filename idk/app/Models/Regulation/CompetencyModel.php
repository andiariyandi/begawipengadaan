<?php

namespace App\Models\Regulation;

use CodeIgniter\Model;

class CompetencyModel extends Model
{
    protected $table         = 'competencies';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'key', 'name', 'description', 'is_active',
    ];

    public function listActive(): array
    {
        return $this->where('is_active', 1)->orderBy('name')->findAll();
    }
}
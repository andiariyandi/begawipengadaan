<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuleModel extends Model
{
    protected $table         = 'modules';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['class_id', 'title', 'sequence_order'];

    public function forClass(int $classId): array
    {
        return $this->where('class_id', $classId)->orderBy('sequence_order', 'ASC')->findAll();
    }
}

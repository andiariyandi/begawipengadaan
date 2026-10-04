<?php

namespace App\Models\Knowledge;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table         = 'knowledge_tags';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'slug'];

    public function listAll(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }
}
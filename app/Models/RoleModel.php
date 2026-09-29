<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'slug'];

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }
}

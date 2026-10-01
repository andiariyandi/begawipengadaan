<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['key', 'name', 'description', 'is_system'];
    protected $validationRules = [
        'key' => 'required|max_length[64]|is_unique[roles.key,id,{id}]',
        'name' => 'required|max_length[255]',
        'is_system' => 'permit_empty|in_list[0,1]',
    ];

    public function findByKey(string $key): ?array
    {
        return $this->where('key', $key)->first();
    }
}

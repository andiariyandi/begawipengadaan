<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table         = 'permissions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['key', 'name', 'domain', 'description', 'is_system'];
    protected $validationRules = [
        'key' => 'required|max_length[128]|is_unique[permissions.key,id,{id}]',
        'name' => 'required|max_length[255]',
        'domain' => 'required|max_length[64]',
        'is_system' => 'permit_empty|in_list[0,1]',
    ];
}

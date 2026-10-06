<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table         = 'role_permissions';
    protected $primaryKey    = null;
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'role_id',
        'permission_id',
        'granted_by',
        'created_at',
    ];
}
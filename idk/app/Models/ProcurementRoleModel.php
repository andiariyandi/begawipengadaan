<?php

namespace App\Models;

use CodeIgniter\Model;

class ProcurementRoleModel extends Model
{
    protected $table         = 'procurement_roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'key',
        'name',
        'description',
        'is_active',
    ];
}
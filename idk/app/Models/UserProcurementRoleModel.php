<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProcurementRoleModel extends Model
{
    protected $table         = 'user_procurement_roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'procurement_role_id',
        'verification_status',
        'verified_by',
        'verified_at',
        'assignment_start_at',
        'assignment_end_at',
        'evidence_file_id',
    ];
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleRequestModel extends Model
{
    protected $table         = 'role_requests';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'requested_role_id',
        'status',
        'reason',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'archived_at',
    ];
}
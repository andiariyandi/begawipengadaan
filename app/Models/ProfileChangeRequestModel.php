<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileChangeRequestModel extends Model
{
    protected $table         = 'profile_change_requests';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'status',
        'requested_payload',
        'reason',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'applied_at',
        'archived_at',
    ];
}
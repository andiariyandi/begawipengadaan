<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileVerificationModel extends Model
{
    protected $table         = 'profile_verifications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'verification_type',
        'status',
        'requested_by',
        'reviewed_by',
        'reason',
        'notes',
        'reviewed_at',
    ];
}
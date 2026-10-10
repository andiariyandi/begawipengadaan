<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class ParticipantFeedbackModel extends Model
{
    protected $table            = 'feedbacks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'enrollment_id',
        'rating',
        'comment',
        'status',
        'submitted_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

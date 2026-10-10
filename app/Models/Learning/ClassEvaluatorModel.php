<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class ClassEvaluatorModel extends Model
{
    protected $table            = 'class_evaluators';
    protected $primaryKey       = 'class_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'class_id',
        'user_id',
        'assigned_by',
        'assigned_at',
        'status',
    ];

    protected $useTimestamps = false;
}

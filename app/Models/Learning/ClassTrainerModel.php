<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class ClassTrainerModel extends Model
{
    protected $table         = 'class_trainers';
    protected $primaryKey    = ['class_id', 'user_id'];
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'class_id', 'user_id', 'assigned_by', 'assigned_at', 'status', 'role',
    ];
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkflowTransitionModel extends Model
{
    protected $table         = 'workflow_transitions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'resource_type',
        'resource_id',
        'from_state',
        'to_state',
        'action',
        'actor_id',
        'actor_type',
        'reason',
        'notes',
        'metadata',
        'created_at',
    ];
}
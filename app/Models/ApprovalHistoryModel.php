<?php

namespace App\Models;

use CodeIgniter\Model;

class ApprovalHistoryModel extends Model
{
    protected $table         = 'approval_histories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['approval_id', 'acted_by', 'action', 'notes'];
}

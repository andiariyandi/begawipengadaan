<?php

namespace App\Models\Intelligence;

use CodeIgniter\Model;

class JfPpbjRecordModel extends Model
{
    protected $table            = 'jf_ppbj_records';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'organization_unit_id',
        'employee_identifier',
        'name_snapshot',
        'level_id',
        'status',
        'effective_at',
        'ended_at',
        'change_type',
        'evidence_file_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

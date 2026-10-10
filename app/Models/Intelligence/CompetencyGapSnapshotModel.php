<?php

namespace App\Models\Intelligence;

use CodeIgniter\Model;

class CompetencyGapSnapshotModel extends Model
{
    protected $table            = 'competency_gap_snapshots';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'organization_unit_id',
        'competency_id',
        'period_date',
        'required_count',
        'available_count',
        'gap_count',
        'metadata',
        'generated_by',
        'created_at',
    ];

    protected $useTimestamps = false;
}

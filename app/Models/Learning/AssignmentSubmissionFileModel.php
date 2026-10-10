<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class AssignmentSubmissionFileModel extends Model
{
    protected $table            = 'assignment_submission_files';
    protected $primaryKey       = 'submission_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'submission_id',
        'file_id',
        'created_at',
    ];

    protected $useTimestamps = false;
}

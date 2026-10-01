<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleRequestModel extends Model
{
    protected $table = 'role_requests';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id', 'requested_role_id', 'status', 'reason',
        'reviewed_by', 'reviewed_at', 'review_notes', 'archived_at',
    ];
    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'requested_role_id' => 'required|is_natural_no_zero',
        'status' => 'required|in_list[draft,submitted,in_review,revision_required,approved,rejected,cancelled,archived]',
        'reason' => 'permit_empty|max_length[65535]',
    ];
}

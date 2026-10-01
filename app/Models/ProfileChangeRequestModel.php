<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileChangeRequestModel extends Model
{
    protected $table = 'profile_change_requests';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id', 'status', 'requested_payload', 'reason',
        'reviewed_by', 'reviewed_at', 'review_notes', 'applied_at', 'archived_at',
    ];
    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'status' => 'required|in_list[draft,submitted,in_review,revision_required,approved,rejected,cancelled,archived]',
        'requested_payload' => 'required',
        'reason' => 'permit_empty|max_length[65535]',
    ];
}

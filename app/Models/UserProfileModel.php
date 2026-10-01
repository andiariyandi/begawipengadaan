<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProfileModel extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'address_line', 'province_code', 'regency_code', 'district_code', 'village_code', 'profile_completion_status'];
    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'address_line' => 'required',
        'province_code' => 'required|max_length[13]',
        'regency_code' => 'required|max_length[13]',
        'district_code' => 'required|max_length[13]',
        'village_code' => 'permit_empty|max_length[13]',
        'profile_completion_status' => 'permit_empty|in_list[draft,in_progress,submitted,verified,revision_required]',
    ];
}

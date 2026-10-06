<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProfileModel extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'user_id',
        'address_line',
        'province_code',
        'regency_code',
        'district_code',
        'village_code',
        'profile_completion_status',
    ];
}

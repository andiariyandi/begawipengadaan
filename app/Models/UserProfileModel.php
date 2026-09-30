<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProfileModel extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'profile_category', 'full_name', 'title_prefix', 'title_suffix', 'phone', 'province', 'regency', 'district', 'province_code', 'regency_code', 'district_code', 'village_code'];
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPublicProfileModelModel extends Model
{
    protected $table = 'public_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'national_identifier', 'occupation_status', 'organization_name', 'interest_areas', 'usage_purpose'];
}

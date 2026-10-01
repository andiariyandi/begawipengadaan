<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAsnProfileModelModel extends Model
{
    protected $table = 'asn_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'organization_unit_id', 'employee_identifier', 'employment_status', 'origin_type', 'institution_name', 'unit_name', 'position_name', 'rank_grade', 'official_email'];
}

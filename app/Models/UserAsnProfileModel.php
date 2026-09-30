<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAsnProfileModel extends Model
{
    protected $table = 'user_asn_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'identity_number', 'identity_type', 'asn_status', 'institution', 'work_unit', 'position', 'grade', 'pbj_role'];
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAcademicProfileModelModel extends Model
{
    protected $table = 'academic_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'institution_name', 'institution_type', 'role_name', 'expertise', 'institution_email', 'professional_identifier', 'portfolio_url'];
}

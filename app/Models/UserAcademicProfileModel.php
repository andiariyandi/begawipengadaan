<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAcademicProfileModel extends Model
{
    protected $table = 'user_academic_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'institution', 'expertise', 'email_contact', 'phone_contact', 'province', 'regency'];
}

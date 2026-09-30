<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProviderProfileModel extends Model
{
    protected $table = 'user_provider_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'nib', 'business_name', 'responsible_person', 'company_email', 'company_phone', 'province', 'regency'];
}

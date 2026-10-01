<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProviderProfileModelModel extends Model
{
    protected $table = 'provider_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'business_name', 'business_form', 'business_field', 'business_identifier', 'registrant_position', 'responsible_person', 'business_email', 'business_address'];
}

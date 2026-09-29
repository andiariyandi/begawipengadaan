<?php

namespace App\Models;

use CodeIgniter\Model;

class UserPublicProfileModel extends Model
{
    protected $table = 'user_public_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'nik', 'email_contact', 'phone_contact', 'province', 'regency'];
}

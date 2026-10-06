<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthTokenModel extends Model
{
    protected $table = 'auth_tokens';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'email', 'token_type', 'token_hash', 'payload', 'expires_at', 'used_at', 'created_at',
    ];
}

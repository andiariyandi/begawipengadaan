<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'username', 'email', 'password_hash', 'account_status',
        'email_verified_at', 'last_login_at',
    ];

    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
    ];

    public function getRoles(int $userId): array
    {
        return $this->db->table('user_roles')
            ->select('roles.id, roles.name, roles.code')
            ->join('roles', 'roles.id = user_roles.role_id', 'inner')
            ->where('user_roles.user_id', $userId)
            ->get()
            ->getResultArray();
    }

    public function getPermissions(int $userId): array
    {
        return array_column($this->db->table('permissions')
            ->select('permissions.name')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
            ->join('user_roles', 'user_roles.role_id = role_permissions.role_id')
            ->where('user_roles.user_id', $userId)
            ->get()
            ->getResultArray(), 'name');
    }

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', strtolower(trim($email)))->first();
    }
}

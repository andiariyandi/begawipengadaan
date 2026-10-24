<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;

    protected $allowedFields = [
        'name',
        'username',
        'email',
        'phone',
        'password_hash',
        'profile_type',
        'account_status',
        'status_reason',
        'email_verified_at',
        'last_login_at',
        'session_version',
    ];

    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
    ];

    protected $beforeDelete = ['cleanupRelations'];

    protected function cleanupRelations(array $data): array
    {
        if (empty($data['id'])) {
            return $data;
        }
        $ids = is_array($data['id']) ? $data['id'] : [$data['id']];
        if (! empty($data['purge'])) {
            $db = db_connect();
            $db->table('asn_profiles')->whereIn('user_id', $ids)->delete();
            $db->table('provider_profiles')->whereIn('user_id', $ids)->delete();
            $db->table('academic_profiles')->whereIn('user_id', $ids)->delete();
            $db->table('public_profiles')->whereIn('user_id', $ids)->delete();
            $db->table('user_profiles')->whereIn('user_id', $ids)->delete();
            $db->table('user_roles')->whereIn('user_id', $ids)->delete();
        }
        return $data;
    }

    public function getRoles(int $userId): array
    {
        return $this->db->table('user_roles')
            ->select('roles.id, roles.name, roles.key')
            ->join('roles', 'roles.id = user_roles.role_id', 'inner')
            ->where('user_roles.user_id', $userId)
            ->where('user_roles.status', 'active')
            ->get()->getResultArray();
    }

    public function getPermissions(int $userId): array
    {
        return array_column(
            $this->db->table('permissions')
                ->select('permissions.key')
                ->join(
                    'role_permissions',
                    'role_permissions.permission_id = permissions.id'
                )
                ->join(
                    'user_roles',
                    'user_roles.role_id = role_permissions.role_id'
                )
                ->where('user_roles.user_id', $userId)
                ->where('user_roles.status', 'active')
                ->get()
                ->getResultArray(),
            'key'
        );
    }

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', strtolower(trim($email)))->first();
    }
}

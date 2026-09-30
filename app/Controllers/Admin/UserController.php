<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserProfileModel;
use App\Models\UserRoleModel;

class UserController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $builder = $db->table('users u')
            ->select('u.id,u.username,u.email,u.account_status,u.created_at,p.full_name,p.profile_category,GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ", ") roles')
            ->join('user_profiles p', 'p.user_id = u.id', 'left')
            ->join('user_roles ur', 'ur.user_id = u.id', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->groupBy('u.id,p.id')->orderBy('u.created_at', 'DESC');
        $perPage = 15;
        $page = max(1, (int) ($this->request->getGet('page_users') ?: 1));
        $total = (clone $builder)->countAllResults();
        $users = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        $pager = service('pager');
        $pager->store('users', $page, $perPage, $total, 0);
        return view('dashboard/users/index', [
            'title' => 'Pengguna | Dashboard BegawiPBJ',
            'pageTitle' => 'Pengguna',
            'activeMenu' => 'access',
            'activeSubmenu' => 'users',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Manajemen Akses'],
                ['label' => 'Pengguna'],
            ],
            'users' => $users,
            'pager' => $pager,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/users/form', $this->formData([
            'title' => 'Tambah Pengguna | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Pengguna',
            'formMode' => 'create',
            'activeMenu' => 'access',
            'activeSubmenu' => 'users',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Manajemen Akses'],
                ['label' => 'Pengguna', 'url' => site_url('dashboard/akses/pengguna')],
                ['label' => 'Tambah Pengguna'],
            ],
        ]));
    }

    public function store()
    {
        return $this->saveUser();
    }

    public function detail(string $id): string
    {
        $user = db_connect()->table('users u')
            ->select('u.*,p.full_name,p.phone,p.profile_category,p.province_code,p.regency_code,p.district_code,p.village_code,GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ", ") roles')
            ->join('user_profiles p', 'p.user_id = u.id', 'left')
            ->join('user_roles ur', 'ur.user_id = u.id', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->where('u.id', ctype_digit($id) ? (int) $id : 0)->groupBy('u.id,p.id')->get()->getRowArray();
        return view('dashboard/users/detail', [
            'title' => 'Detail Pengguna | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Pengguna',
            'userId' => $id,
            'activeMenu' => 'access',
            'activeSubmenu' => 'users',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Manajemen Akses'],
                ['label' => 'Pengguna', 'url' => site_url('dashboard/akses/pengguna')],
                ['label' => 'Detail'],
            ],
            'user' => $user,
        ]);
    }

    public function edit(string $id): string
    {
        $userId = ctype_digit($id) ? (int) $id : 0;
        $user = (new UserModel())->find($userId);
        if (! $user) {
            return redirect()->to('dashboard/akses/pengguna')->with('error', 'Pengguna tidak ditemukan.');
        }
        $profile = (new UserProfileModel())->where('user_id', $userId)->first() ?: [];
        $role = (new UserRoleModel())->where('user_id', $userId)->first();
        $organization = [];
        if (db_connect()->tableExists('user_organizations')) {
            $organization = db_connect()->table('user_organizations')->where('user_id', $userId)->where('is_primary', 1)->get()->getRowArray() ?: [];
        }
        return view('dashboard/users/form', $this->formData([
            'title' => 'Ubah Pengguna | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Pengguna',
            'formMode' => 'edit',
            'userId' => $id,
            'user' => array_merge($user, $profile, ['role_id' => $role['role_id'] ?? '', 'organization_unit_id' => $organization['organization_unit_id'] ?? '']),
            'activeMenu' => 'access',
            'activeSubmenu' => 'users',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => site_url('dashboard')],
                ['label' => 'Manajemen Akses'],
                ['label' => 'Pengguna', 'url' => site_url('dashboard/akses/pengguna')],
                ['label' => 'Ubah'],
            ],
        ]));
    }

    public function update(string $id)
    {
        return $this->saveUser(ctype_digit($id) ? (int) $id : 0);
    }

    private function formData(array $data): array
    {
        $data['roles'] = db_connect()->table('roles')->select('id,name')->orderBy('name')->get()->getResultArray();
        $data['organizations'] = db_connect()->table('organization_units')->select('id,name')->where('is_active', 1)->orderBy('name')->get()->getResultArray();
        return $data;
    }

    private function saveUser(?int $userId = null)
    {
        $post = $this->request->getPost();
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|max_length[150]',
            'full_name' => 'required|max_length[150]',
            'profile_category' => 'required|max_length[32]',
            'account_status' => 'required|in_list[pending,pending_verification,submitted,revision_required,active,inactive,suspended,rejected,archived]',
        ];
        if ($userId === null) {
            $rules['password'] = 'required|min_length[8]';
        } elseif ($post['password'] ?? '') {
            $rules['password'] = 'min_length[8]';
        }
        if (! $this->validateData($post, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $users = new UserModel();
        $email = strtolower(trim((string) $post['email']));
        $username = trim((string) $post['username']);
        $duplicate = $users->groupStart()->where('email', $email)->orWhere('username', $username)->groupEnd();
        if ($userId !== null) {
            $duplicate->where('id !=', $userId);
        }
        if ($duplicate->first()) {
            return redirect()->back()->withInput()->with('error', 'Username atau email sudah digunakan.');
        }

        $db = db_connect();
        $db->transStart();
        $account = ['username' => $username, 'email' => $email, 'account_status' => $post['account_status']];
        if (! empty($post['password'])) {
            $account['password_hash'] = password_hash((string) $post['password'], PASSWORD_DEFAULT);
        }
        if ($userId === null) {
            $userId = $users->insert($account, true);
        } else {
            $users->update($userId, $account);
        }
        if (! $userId) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Pengguna gagal disimpan.');
        }
        $profileData = ['user_id' => $userId, 'profile_category' => $post['profile_category'], 'full_name' => trim((string) $post['full_name']), 'phone' => trim((string) ($post['phone'] ?? ''))];
        $profile = new UserProfileModel();
        $existingProfile = $profile->where('user_id', $userId)->first();
        $existingProfile ? $profile->update($existingProfile['id'], $profileData) : $profile->insert($profileData);
        $roles = new UserRoleModel();
        $roles->where('user_id', $userId)->delete();
        if (ctype_digit((string) ($post['role_id'] ?? '')) && db_connect()->table('roles')->where('id', (int) $post['role_id'])->countAllResults()) {
            $roles->insert(['user_id' => $userId, 'role_id' => (int) $post['role_id'], 'created_at' => date('Y-m-d H:i:s')]);
        }
        if ($db->tableExists('user_organizations')) {
            $db->table('user_organizations')->where('user_id', $userId)->delete();
            if (ctype_digit((string) ($post['organization_unit_id'] ?? '')) && $db->table('organization_units')->where('id', (int) $post['organization_unit_id'])->countAllResults()) {
                $db->table('user_organizations')->insert(['user_id' => $userId, 'organization_unit_id' => (int) $post['organization_unit_id'], 'is_primary' => 1, 'start_date' => date('Y-m-d')]);
            }
        }
        $db->transComplete();
        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Perubahan pengguna gagal disimpan.');
        }
        return redirect()->to('dashboard/akses/pengguna/' . $userId)->with('success', 'Pengguna berhasil disimpan.');
    }
}

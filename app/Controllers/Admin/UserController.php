<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\WilayahModel;
use App\Services\Admin\UserManagementService;

/**
 * Admin Manajemen Akses → Pengguna.
 *
 * Implements CRUD + status actions (suspend/activate) + reset password
 * initiation. Detail view joins user_profiles, profile-type-specific
 * tables, roles, and recent audit_logs/workflow_transitions.
 *
 * Password change is NOT performed directly here — admin triggers a
 * PasswordResetService request (CLAUDE.md §Password).
 */
class UserController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $builder = $db->table('users u')
            ->select('u.id, u.username, u.email, u.name, u.phone, u.profile_type, u.account_status, u.created_at,
                      GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ", ") AS roles,
                      ou.name AS organization_name')
            ->join('user_roles ur', 'ur.user_id = u.id AND ur.status = "active"', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->join('user_profiles up', 'up.user_id = u.id', 'left')
            ->join('organization_units ou', 'ou.id = up.province_code OR ou.id = up.regency_code', 'left', false)
            ->groupBy('u.id, ou.id, r.id, ur.id')
            ->orderBy('u.created_at', 'DESC');

        // Filters
        $keyword = trim((string) $this->request->getGet('keyword'));
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('u.username', $keyword)
                ->orLike('u.email', $keyword)
                ->groupEnd();
        }
        $profileType = trim((string) $this->request->getGet('profile_type'));
        if ($profileType !== '' && in_array($profileType, UserManagementService::ALLOWED_PROFILE_TYPES, true)) {
            $builder->where('u.profile_type', $profileType);
        }
        $status = trim((string) $this->request->getGet('account_status'));
        if ($status !== '' && in_array($status, UserManagementService::ALLOWED_STATUSES, true)) {
            $builder->where('u.account_status', $status);
        }
        $roleId = (int) $this->request->getGet('role_id');
        if ($roleId > 0) {
            $builder->where('EXISTS (SELECT 1 FROM user_roles ur2 WHERE ur2.user_id = u.id AND ur2.role_id = ' . $roleId . ' AND ur2.status = "active")', null, false);
        }

        // Sorting
        $sort = (string) ($this->request->getGet('sort') ?: 'created_at');
        $dir = strtolower((string) ($this->request->getGet('dir') ?: 'desc')) === 'asc' ? 'ASC' : 'DESC';
        $allowedSorts = [
            'name'           => 'u.name',
            'created_at'     => 'u.created_at',
            'email'          => 'u.email',
            'profile_type'   => 'u.profile_type',
            'account_status' => 'u.account_status',
        ];
        $orderCol = $allowedSorts[$sort] ?? 'u.created_at';
        $builder->orderBy($orderCol, $dir);

        // Count without GROUP BY breaking count
        $countBuilder = $db->table('users u');
        if ($keyword !== '') {
            $countBuilder->groupStart()
                ->like('u.name', $keyword)
                ->orLike('u.username', $keyword)
                ->orLike('u.email', $keyword)
                ->groupEnd();
        }
        if ($profileType !== '' && in_array($profileType, UserManagementService::ALLOWED_PROFILE_TYPES, true)) {
            $countBuilder->where('u.profile_type', $profileType);
        }
        if ($status !== '' && in_array($status, UserManagementService::ALLOWED_STATUSES, true)) {
            $countBuilder->where('u.account_status', $status);
        }
        if ($roleId > 0) {
            $countBuilder->where('EXISTS (SELECT 1 FROM user_roles ur2 WHERE ur2.user_id = u.id AND ur2.role_id = ' . $roleId . ' AND ur2.status = "active")', null, false);
        }
        $total = $countBuilder->countAllResults();

        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_users') ?: 1));
        $users = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $pager = service('pager');
        $pager->store('users', $page, $perPage, $total, 0);

        $stats = [
            'total'   => (int) $db->table('users')->countAllResults(),
            'active'  => (int) $db->table('users')->where('account_status', 'active')->countAllResults(),
            'pending' => (int) $db->table('users')->whereIn('account_status', ['pending_approval', 'pending_verification', 'submitted'])->countAllResults(),
            'asn'     => (int) $db->table('users')->where('profile_type', 'asn')->countAllResults(),
        ];

        return view('dashboard/users/index', [
            'title'         => 'Pengguna | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Manajemen Pengguna',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'users',
            'users'         => $users,
            'stats'         => $stats,
            'pager'         => $pager,
            'perPage'       => $perPage,
            'sort'          => $sort,
            'dir'           => strtolower($dir),
            'filters'       => [
                'keyword'        => $keyword,
                'profile_type'   => $profileType,
                'account_status' => $status,
                'role_id'        => $roleId,
                'per_page'       => $perPage,
                'sort'           => $sort,
                'dir'            => strtolower($dir),
            ],
            'roles'         => $db->table('roles')->select('id, name')->orderBy('name')->get()->getResultArray(),
            'profileTypes'  => UserManagementService::ALLOWED_PROFILE_TYPES,
            'statuses'      => UserManagementService::ALLOWED_STATUSES,
        ]);
    }

    public function detail(string $id)
    {
        $userId = ctype_digit($id) ? (int) $id : 0;
        $db = db_connect();

        $user = $db->table('users u')
            ->select('u.*, GROUP_CONCAT(DISTINCT r.name ORDER BY r.name SEPARATOR ", ") AS roles')
            ->join('user_roles ur', 'ur.user_id = u.id AND ur.status = "active"', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->where('u.id', $userId)
            ->groupBy('u.id')
            ->get()->getRowArray();

        if (! $user) {
            return redirect()->to('dashboard/akses/pengguna')->with('error', 'Pengguna tidak ditemukan.');
        }

        $profile = $db->table('user_profiles up')
            ->select('up.*, wp.nama AS province_name, wr.nama AS regency_name, wd.nama AS district_name, wv.nama AS village_name')
            ->join('wilayah wp', 'wp.kode = up.province_code', 'left')
            ->join('wilayah wr', 'wr.kode = up.regency_code', 'left')
            ->join('wilayah wd', 'wd.kode = up.district_code', 'left')
            ->join('wilayah wv', 'wv.kode = up.village_code', 'left')
            ->where('up.user_id', $userId)
            ->get()->getRowArray() ?: [];

        $typeProfile = match ($user['profile_type'] ?? '') {
            'asn'      => $db->table('asn_profiles')->where('user_id', $userId)->get()->getRowArray(),
            'provider' => $db->table('provider_profiles')->where('user_id', $userId)->get()->getRowArray(),
            'academic' => $db->table('academic_profiles')->where('user_id', $userId)->get()->getRowArray(),
            'public'   => $db->table('public_profiles')->where('user_id', $userId)->get()->getRowArray(),
            default    => null,
        };

        $verifications = $db->table('profile_verifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(20)->get()
            ->getResultArray();

        $auditLogs = $db->table('audit_logs')
            ->where('resource_type', 'users')
            ->where('resource_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(20)->get()
            ->getResultArray();

        $transitions = $db->table('workflow_transitions')
            ->where('resource_type', 'user')
            ->where('resource_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(20)->get()
            ->getResultArray();

        return view('dashboard/users/detail', [
            'title'         => 'Detail Pengguna | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Pengguna',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'users',
            'user'          => $user,
            'userId'        => $userId,
            'profile'       => $profile,
            'typeProfile'   => $typeProfile,
            'verifications' => $verifications,
            'auditLogs'     => $auditLogs,
            'transitions'   => $transitions,
        ]);
    }

    public function activate(string $id)
    {
        $userId = ctype_digit($id) ? (int) $id : 0;
        $actorId = (int) session()->get('user_id');
        try {
            (new UserManagementService())->activate($userId, $actorId, 'admin.activate');
            return redirect()->to('dashboard/akses/pengguna/' . $userId)->with('success', 'Akun diaktifkan.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function suspend(string $id)
    {
        $userId = ctype_digit($id) ? (int) $id : 0;
        $reason = trim((string) $this->request->getPost('reason'));
        if ($reason === '') {
            return redirect()->back()->with('error', 'Alasan penangguhan wajib diisi.');
        }
        $actorId = (int) session()->get('user_id');
        try {
            (new UserManagementService())->suspend($userId, $reason, $actorId);
            return redirect()->to('dashboard/akses/pengguna/' . $userId)->with('success', 'Akun ditangguhkan.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function resetPassword(string $id)
    {
        $userId = ctype_digit($id) ? (int) $id : 0;
        $user = (new UserModel())->find($userId);
        if (! $user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        (new \App\Services\Identity\PasswordResetService())->requestReset((string) $user['email']);

        return redirect()->to('dashboard/akses/pengguna/' . $userId)
            ->with('success', 'Instruksi reset kata sandi telah dikirim ke email pengguna.');
    }

    public function create()
    {
        $isSuperAdmin = (new \App\Services\Identity\AuthorizationService())->isSuperAdmin();
        $roles = (new \App\Services\Admin\RoleManagementService())->listRoles();
        if (! $isSuperAdmin) {
            $roles = array_values(array_filter($roles, static fn($r) => ! in_array($r['key'] ?? '', ['super_administrator', 'administrator'], true)));
        }
        $categories = (new \App\Services\Admin\OrganizationService())->listTree();
        return view('dashboard/users/form', [
            'title'         => 'Tambah Pengguna | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Pengguna',
            'formMode'      => 'create',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'users',
            'item'          => null,
            'roles'         => $roles,
            'categories'    => $categories,
            'isSuperAdmin'  => $isSuperAdmin,
        ]);
    }

    public function store()
    {
        $rules = [
            'username'    => 'required|min_length[3]|max_length[255]|is_unique[users.username]',
            'email'       => 'required|valid_email|is_unique[users.email]',
            'name'        => 'required|max_length[255]',
            'password'    => 'required|min_length[12]|regex_match[/[A-Z]/]|regex_match[/[a-z]/]|regex_match[/[0-9]/]',
            'profile_type' => 'required|in_list[asn,provider,academic,public]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $actorId = (int) session()->get('user_id');
        $post = $this->request->getPost();

        $roleId = (int) ($post['role_id'] ?? 0);
        if ($roleId > 0) {
            $isSuperAdmin = (new \App\Services\Identity\AuthorizationService())->isSuperAdmin();
            if (! $isSuperAdmin) {
                $roleObj = (new \App\Services\Admin\RoleManagementService())->findRole($roleId);
                if ($roleObj && in_array($roleObj['key'] ?? '', ['super_administrator', 'administrator'], true)) {
                    return redirect()->back()->withInput()->with('error', 'Hanya Super Administrator yang berhak menetapkan role Administrator.');
                }
            }
        }

        if (! $this->saveUserAvatar($post)) {
            return redirect()->back()->withInput()->with('error', 'Upload avatar gagal.');
        }
        try {
            $id = (new \App\Services\Admin\UserManagementService())->saveUser($post, null, $actorId);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        return redirect()->to('dashboard/akses/pengguna/' . $id)->with('success', 'Pengguna dibuat.');
    }

    public function edit(string $id)
    {
        $uid = ctype_digit($id) ? (int) $id : 0;
        $user = (new \App\Models\UserModel())->find($uid);
        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengguna tidak ditemukan.');
        }

        // Ambil role aktif pengguna saat ini untuk form selector
        $userRoles = (new \App\Models\UserModel())->getRoles($uid);
        $user['role_id'] = ! empty($userRoles) ? (int) $userRoles[0]['id'] : null;

        $isSuperAdmin = (new \App\Services\Identity\AuthorizationService())->isSuperAdmin();
        $roles = (new \App\Services\Admin\RoleManagementService())->listRoles();
        if (! $isSuperAdmin) {
            $roles = array_values(array_filter($roles, static fn($r) => ! in_array($r['key'] ?? '', ['super_administrator', 'administrator'], true)));
        }
        $categories = (new \App\Services\Admin\OrganizationService())->listTree();
        return view('dashboard/users/form', [
            'title'         => 'Ubah Pengguna | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Pengguna',
            'formMode'      => 'edit',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'users',
            'item'          => $user,
            'userId'        => $uid,
            'roles'         => $roles,
            'categories'    => $categories,
            'isSuperAdmin'  => $isSuperAdmin,
        ]);
    }

    public function update(string $id)
    {
        $uid = ctype_digit($id) ? (int) $id : 0;
        $userModel = new \App\Models\UserModel();
        $targetUser = $uid > 0 ? $userModel->find($uid) : null;
        if (! $targetUser) {
            return redirect()->to('dashboard/akses/pengguna')->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'username'     => 'required|min_length[3]|max_length[255]',
            'email'        => 'required|valid_email',
            'name'         => 'required|max_length[255]',
            'profile_type' => 'required|in_list[asn,provider,academic,public]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost();

        // Validasi password jika diisi oleh admin
        if (! empty($post['password'])) {
            $passRules = [
                'password' => 'min_length[12]|regex_match[/[A-Z]/]|regex_match[/[a-z]/]|regex_match[/[0-9]/]',
            ];
            if (! $this->validate($passRules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }

        $actorId = (int) session()->get('user_id');

        $roleId = (int) ($post['role_id'] ?? 0);
        if ($roleId > 0) {
            $isSuperAdmin = (new \App\Services\Identity\AuthorizationService())->isSuperAdmin();
            if (! $isSuperAdmin) {
                $roleObj = (new \App\Services\Admin\RoleManagementService())->findRole($roleId);
                if ($roleObj && in_array($roleObj['key'] ?? '', ['super_administrator', 'administrator'], true)) {
                    return redirect()->back()->withInput()->with('error', 'Hanya Super Administrator yang berhak menetapkan role Administrator.');
                }
            }
        }

        if (! $this->saveUserAvatar($post)) {
            return redirect()->back()->withInput()->with('error', 'Upload avatar gagal.');
        }

        try {
            (new \App\Services\Admin\UserManagementService())->saveUser($post, $uid, $actorId);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('dashboard/akses/pengguna/' . $uid)->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        $uid = ctype_digit($id) ? (int) $id : 0;
        if ($uid <= 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengguna tidak ditemukan.');
        }
        $actorId = (int) session()->get('user_id');
        if ($uid === $actorId) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        try {
            (new \App\Models\UserModel())->delete($uid);
            (new \App\Services\AuditService())->log($actorId, 'user.delete', 'users', 'user', $uid);
            (new \App\Services\Admin\UserManagementService())->recordTransition('user', $uid, null, 'deleted', 'user.delete', $actorId);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('dashboard/akses/pengguna')->with('success', 'Pengguna dihapus.');
    }

    private function saveUserAvatar(array &$post): bool
    {
        $file = $this->request->getFile('avatar');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return true;
        }
        $dir = WRITEPATH . 'uploads/users';
        if (! is_dir($dir) && ! @mkdir($dir, 0755, true)) {
            return false;
        }
        $stored = $file->getRandomName();
        if (! $file->move($dir, $stored)) {
            return false;
        }
        $now = date('Y-m-d H:i:s');
        $rel = 'uploads/users/' . $stored;
        $db = db_connect();
        $ok = $db->table('file_assets')->insert([
            'disk' => 'local',
            'path' => $rel,
            'original_name' => $file->getClientName() ?: $stored,
            'stored_name'   => $stored,
            'extension'     => $file->getClientExtension() ?: 'bin',
            'mime_type'     => $file->getClientMimeType() ?: 'application/octet-stream',
            'size_bytes'    => (int) ($file->getSize() ?: 0),
            'visibility'    => 'public',
            'lifecycle_status' => 'available',
            'resource_type' => 'user_avatar',
            'resource_id'   => null,
            'uploaded_by'   => (int) session()->get('user_id'),
            'available_at'  => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ], true);
        if (! $ok) {
            return false;
        }
        $post['avatar_file_id'] = (int) $db->insertID();
        return true;
    }
}

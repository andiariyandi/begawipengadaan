<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\AuditService;

class LoginController extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            $isAdmin = (bool) array_intersect(session()->get('roles') ?? [], ['super_administrator', 'administrator']);

            return redirect()->to($isAdmin ? route_to('dashboard.pbj') : route_to('landing'));
        }

        return view('authentication/signin');
    }

    public function attempt()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Email dan password wajib diisi dengan benar.');
        }

        $userModel = new UserModel();
        $user      = $userModel->findByEmail($this->request->getPost('email'));

        if (! $user || ! password_verify($this->request->getPost('password'), $user['password_hash'])) {
            (new AuditService())->log(null, 'login_failed', 'auth', 'users', null, ['email' => $this->request->getPost('email')]);
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Akun Anda belum aktif atau sedang menunggu verifikasi Operator.');
        }

        $roles = $userModel->getRoles($user['id']);
        $permissions = $userModel->getPermissions($user['id']);

        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'user_type'  => $user['user_type'],
            'roles'      => array_column($roles, 'slug'),
            'permissions' => $permissions,
        ]);

        (new AuditService())->log($user['id'], 'login', 'auth', 'users', $user['id']);

        $isAdmin = (bool) array_intersect(array_column($roles, 'slug'), ['super_administrator', 'administrator']);

        return redirect()->to($isAdmin ? route_to('dashboard.pbj') : route_to('landing'));
    }

    public function logout()
    {
        $userId = session()->get('user_id');
        (new AuditService())->log($userId, 'logout', 'auth', 'users', $userId);

        session()->destroy();

        return redirect()->to(route_to('landing'));
    }
}

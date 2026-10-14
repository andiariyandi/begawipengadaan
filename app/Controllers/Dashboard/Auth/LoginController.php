<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;
use App\Services\Identity\AuthenticationService;

class LoginController extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            $loggedRoles = (array) session()->get('roles');
            $isAdmin     = in_array('super_administrator', $loggedRoles, true)
                || in_array('administrator', $loggedRoles, true)
                || in_array('super_admin', $loggedRoles, true)
                || in_array('admin', $loggedRoles, true);

            if ($isAdmin) {
                return redirect()->to(site_url('dashboard'));
            }
            if (in_array('trainer', $loggedRoles, true)) {
                return redirect()->to(route_to('workspace.trainer.home'));
            }
            if (in_array('operator', $loggedRoles, true)) {
                return redirect()->to(route_to('workspace.operator.home'));
            }
            return redirect()->to(route_to('workspace.home'));
        }

        return view('dashboard/auth/login', [
            'title' => 'Masuk | BegawiPengadaan',
            'pageTitle' => 'Masuk ke Akun Anda',
            'pageDescription' => 'Silakan masukkan email dan kata sandi untuk mengakses layanan BegawiPengadaan.',
        ]);
    }

    public function attemptLogin()
    {
        if (! $this->validate([
            'email' => 'required|valid_email|max_length[150]',
            'password' => 'required|max_length[255]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = (new AuthenticationService())->authenticate(
            (string) $this->request->getPost('email'),
            (string) $this->request->getPost('password')
        );

        if (! $result['ok']) {
            if ($result['reason'] === 'inactive_account') {
                $pendingUserId = (int) ($result['user']['id'] ?? 0);
                if ($pendingUserId > 0) {
                    session()->setFlashdata('pending_user_id', $pendingUserId);
                }
                return redirect()->to(site_url('dashboard/status-akun'));
            }

            return redirect()->back()->withInput()->with('error', 'Email atau kata sandi salah.');
        }

        $loggedRoles = (array) session()->get('roles');
        $isAdmin     = in_array('super_administrator', $loggedRoles, true)
            || in_array('administrator', $loggedRoles, true)
            || in_array('super_admin', $loggedRoles, true)
            || in_array('admin', $loggedRoles, true);
        $userName    = (string) session()->get('name');

        if ($isAdmin) {
            return redirect()->to(site_url('dashboard'))->with('success', 'Berhasil masuk sebagai ' . $userName . '.');
        }
        if (in_array('trainer', $loggedRoles, true)) {
            return redirect()->to(route_to('workspace.trainer.home'))->with('success', 'Berhasil masuk sebagai ' . $userName . '.');
        }
        if (in_array('operator', $loggedRoles, true)) {
            return redirect()->to(route_to('workspace.operator.home'))->with('success', 'Berhasil masuk sebagai ' . $userName . '.');
        }
        return redirect()->to(route_to('workspace.home'))->with('success', 'Berhasil masuk sebagai ' . $userName . '.');
    }

    public function logout()
    {
        (new AuthenticationService())->logout();

        return redirect()->to(site_url('dashboard/masuk'))->with('message', 'Anda telah keluar dari akun.');
    }

    public function logoutPreview()
    {
        return $this->logout();
    }
}

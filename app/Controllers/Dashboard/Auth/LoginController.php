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
            $isAdmin     = in_array('super_admin', $loggedRoles, true) || in_array('admin', $loggedRoles, true);
            if (! $isAdmin && in_array('trainer', $loggedRoles, true)) {
                return redirect()->to(route_to('workspace.trainer.home'));
            }
            if (! $isAdmin && in_array('operator', $loggedRoles, true)) {
                return redirect()->to(route_to('workspace.operator.home'));
            }
            if (! $isAdmin && in_array('evaluator', $loggedRoles, true)) {
                return redirect()->to(route_to('workspace.evaluator.home'));
            }
            return redirect()->to(site_url('dashboard'));
        }

        return view('dashboard/auth/login', [
            'title' => 'Masuk | BegawiPBJ',
            'pageTitle' => 'Masuk ke Akun Anda',
            'pageDescription' => 'Silakan masukkan email dan kata sandi untuk mengakses layanan BegawiPBJ.',
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
            $message = $result['reason'] === 'inactive_account'
                ? 'Akun belum aktif atau sedang tidak dapat digunakan.'
                : 'Email atau kata sandi salah.';

            return redirect()->back()->withInput()->with('error', $message);
        }

        $loggedRoles = (array) session()->get('roles');
        $isAdmin     = in_array('super_admin', $loggedRoles, true) || in_array('admin', $loggedRoles, true);
        $userName    = (string) session()->get('name');
        if (! $isAdmin && in_array('trainer', $loggedRoles, true)) {
            return redirect()->to(route_to('workspace.trainer.home'))->with('success', 'Berhasil masuk sebagai ' . $userName . '.');
        }
        if (! $isAdmin && in_array('operator', $loggedRoles, true)) {
            return redirect()->to(route_to('workspace.operator.home'))->with('success', 'Berhasil masuk sebagai ' . $userName . '.');
        }
        if (! $isAdmin && in_array('evaluator', $loggedRoles, true)) {
            return redirect()->to(route_to('workspace.evaluator.home'))->with('success', 'Berhasil masuk sebagai ' . $userName . '.');
        }
        return redirect()->to(site_url('dashboard'))->with('success', 'Berhasil masuk sebagai ' . $userName . '.');
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

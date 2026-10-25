<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;
use App\Services\Identity\AuthenticationService;
use App\Services\Identity\AuthorizationService;

class LoginController extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            $loggedRoles = (array) session()->get('roles');
            $authService = new AuthorizationService();

            // 1. Super Administrator & Administrator penuh
            $isFullAdmin = in_array('super_administrator', $loggedRoles, true)
                || in_array('administrator', $loggedRoles, true);

            // 2. Peran dinamis berprefix admin_{...} (e.g. admin_regulasi, admin_pembelajaran, dll)
            $isPrefixAdmin = $authService->isPrefixAdmin($loggedRoles);

            // 3. Hak akses dashboard dinamis
            $hasDashboardAccess = $isFullAdmin || $isPrefixAdmin || $authService->hasDashboardAccess();

            if ($isFullAdmin) {
                return redirect()->to(site_url('dashboard'));
            }

            if ($isPrefixAdmin || $hasDashboardAccess) {
                $targetRoute = $authService->getFirstAllowedDashboardRoute();
                return redirect()->to(site_url($targetRoute));
            }

            // 4. Trainer (Pengajar)
            if (in_array('trainer', $loggedRoles, true)) {
                return redirect()->to(route_to('workspace.trainer.home'));
            }

            // 5. Operator (Asisten Kelas / Admin Operasional)
            if (in_array('operator', $loggedRoles, true)) {
                return redirect()->to(route_to('workspace.operator.home'));
            }

            // 6. Peserta / Default Workspace
            return redirect()->to(route_to('workspace.home'));
        }

        return view('dashboard/auth/login', [
            'title'           => 'Masuk | BegawiPengadaan',
            'pageTitle'       => 'Masuk ke Akun Anda',
            'pageDescription' => 'Silakan masukkan email dan kata sandi untuk mengakses layanan BegawiPengadaan.',
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email|max_length[150]',
            'password' => 'required|max_length[255]',
        ];

        $messages = [
            'email' => [
                'required'    => 'Alamat email wajib diisi.',
                'valid_email' => 'Format alamat email tidak valid.',
            ],
            'password' => [
                'required' => 'Kata sandi akun wajib diisi.',
            ],
        ];

        $postData = (array) ($this->request->getPost() ?: $_POST);

        if (! $this->validateData($postData, $rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = strtolower(trim((string) ($postData['email'] ?? '')));
        $password = (string) ($postData['password'] ?? '');

        // M-13: Rate limit percobaan login (maks 10 percobaan per 60 detik per IP dan email)
        $throttler = service('throttler');
        $ip = $this->request->getIPAddress();
        if ($throttler->check('auth_login_' . md5($ip . '_' . $email), 10, 60) === false) {
            return redirect()->back()->withInput()->with('error', 'Terlalu banyak percobaan masuk. Silakan tunggu 1 menit sebelum mencoba kembali.');
        }

        $result = (new AuthenticationService())->authenticate($email, $password);

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
        $userName    = (string) session()->get('name');
        $authService = new AuthorizationService();

        // 1. Super Administrator & Administrator penuh (Dasbor Eksekutif Steril)
        $isFullAdmin = in_array('super_administrator', $loggedRoles, true)
            || in_array('administrator', $loggedRoles, true);

        if ($isFullAdmin) {
            return redirect()->to(site_url('dashboard'))->with('success', 'Berhasil masuk sebagai ' . $userName . '.');
        }

        // 2. Seluruh peran lainnya (admin_{...}, operator, trainer, peserta)
        // Memiliki peran dasar melekat sebagai Peserta dan direct login masuk ke Workspace
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

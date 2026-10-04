<?php

namespace App\Filters;

use App\Models\UserModel;
use App\Services\Identity\AuthorizationService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthenticationFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $path = trim($request->getUri()->getPath(), '/');
        $path = preg_replace('#^index\.php/?#', '', $path) ?? $path;
        $publicPaths = [
            'dashboard/masuk',
            'dashboard/lupa-kata-sandi',
            'dashboard/keluar',
            'dashboard/status-akun',
        ];

        if (
            in_array($path, $publicPaths, true)
            || str_starts_with($path, 'dashboard/daftar')
            || preg_match('#^dashboard/reset-kata-sandi/[^/]+$#', $path)
        ) {
            return null;
        }

        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk mengakses dashboard.');
        }

        $userId = (int) session()->get('user_id');
        $user = $userId > 0 ? (new UserModel())->find($userId) : null;
        if (! $user || ($user['account_status'] ?? null) !== 'active') {
            session()->destroy();

            return redirect()->to(site_url('dashboard/status-akun'));
        }

        if (session()->get('account_status') !== 'active') {
            session()->set('account_status', $user['account_status']);
        }

        if (! (new AuthorizationService())->hasDashboardAccess()) {
            return service('response')->setStatusCode(403)->setBody('Forbidden');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

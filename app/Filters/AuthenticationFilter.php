<?php

namespace App\Filters;

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
            || preg_match('#^dashboard/(verifikasi|reset-kata-sandi)/[^/]+$#', $path)
        ) {
            return null;
        }

        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk mengakses dashboard.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

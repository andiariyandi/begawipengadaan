<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(route_to('signin'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRoles    = session()->get('roles') ?? [];
        $allowedRoles = $arguments ?? [];

        if (empty(array_intersect($userRoles, $allowedRoles))) {
            return redirect()->to(route_to('dashboard.pbj'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}

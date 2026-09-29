<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(route_to('signin'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $required = $arguments ?? [];
        $granted  = session()->get('permissions') ?? [];

        if (empty(array_intersect($required, $granted))) {
            return redirect()->to(route_to('dashboard.pbj'))->with('error', 'Anda tidak memiliki permission untuk halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}

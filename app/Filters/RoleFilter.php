<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $roles = session()->get('roles') ?? [];
        if (! in_array($arguments[0] ?? '', $roles, true)) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'Anda tidak memiliki peran untuk mengakses halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

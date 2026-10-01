<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $permissions = session()->get('permissions') ?? [];
        $roles = session()->get('roles') ?? [];
        if (array_intersect(['administrator', 'super_administrator'], $roles)) {
            return null;
        }
        if (! in_array($arguments[0] ?? '', $permissions, true)) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

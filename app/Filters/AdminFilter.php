<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $roles = session()->get('roles') ?? [];
        if (! array_intersect(['administrator', 'super_administrator'], $roles)) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'Akses administrator diperlukan.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

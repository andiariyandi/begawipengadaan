<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AccountStatusFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $expected = $arguments[0] ?? 'active';
        if (session()->get('account_status') !== $expected) {
            return redirect()->to(site_url('dashboard/status-akun'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

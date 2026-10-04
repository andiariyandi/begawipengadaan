<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Gate untuk route group Workspace (workspace, workspace/peserta, workspace/trainer,
 * workspace/evaluator, workspace/operator).
 *
 * Hanya memeriksa:
 * - session login
 * - users.account_status = 'active'
 *
 * Tidak memeriksa dashboard permission (AuthFilter melakukan itu untuk /dashboard/*).
 * Tidak memeriksa role spesifik — role/permission gate tetap di controller method.
 */
class WorkspaceAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk mengakses Workspace.');
        }

        if (session()->get('account_status') !== 'active') {
            session()->destroy();
            return redirect()->to(site_url('dashboard/status-akun'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
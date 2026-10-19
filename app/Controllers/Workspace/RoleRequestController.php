<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;

/**
 * Workspace RoleRequest controller.
 *
 * Placeholder port of the legacy Auth\RoleRequestController stub. Renders a
 * minimal placeholder view. Full implementation is out of scope for the Auth
 * refactor — this controller only exists so the route does not crash when the
 * legacy namespace is deleted.
 */
class RoleRequestController extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        return redirect()->to(route_to('workspace.profile.index'))
            ->with('message', 'Pengajuan perubahan role dapat dilakukan melalui pembaruan kelengkapan profil akun Anda.');
    }

    public function create()
    {
        return $this->index();
    }
}
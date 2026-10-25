<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Gate untuk route group Workspace (workspace, workspace/peserta, workspace/trainer,
 * workspace/operator, workspace/admin).
 *
 * Mengamankan:
 * - session login valid
 * - real-time database check: users.account_status = 'active'
 * - real-time refresh role dari database (user_roles + roles)
 * - isolasi sub-grup URL (operator, trainer, admin)
 */
class WorkspaceAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk mengakses Workspace.');
        }

        $userId = (int) session()->get('user_id');
        if ($userId < 1) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session()->destroy();
            } else {
                $_SESSION = [];
            }
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Sesi login tidak valid.');
        }

        $db = db_connect();
        $user = $db->table('users')
            ->where('id', $userId)
            ->where('deleted_at IS NULL')
            ->get()->getRowArray();

        if (! $user || ($user['account_status'] ?? '') !== 'active') {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session()->destroy();
            } else {
                $_SESSION = [];
            }
            if ($userId > 0) {
                session()->setFlashdata('pending_user_id', $userId);
            }
            return redirect()->to(site_url('dashboard/status-akun'))
                ->with('error', 'Akun Anda tidak aktif atau sedang ditangguhkan. Silakan hubungi administrator.');
        }

        // Real-time synchronization of roles dari database (fail-closed single source of truth)
        $userRoles = $db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('ur.status', 'active')
            ->select('r.key')
            ->get()->getResultArray();

        $dbRoles = array_unique(array_filter(array_column($userRoles, 'key')));
        $effectiveRoles = $dbRoles;

        if (empty($effectiveRoles)) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session()->destroy();
            } else {
                $_SESSION = [];
            }
            if ($userId > 0) {
                session()->setFlashdata('pending_user_id', $userId);
            }
            return redirect()->to(site_url('dashboard/status-akun'))
                ->with('error', 'Akun Anda tidak memiliki peran aktif. Silakan hubungi administrator.');
        }

        session()->set('roles', $effectiveRoles);
        session()->set('account_status', $user['account_status']);

        // Enforce URL sub-group role isolation
        $req = $request instanceof \CodeIgniter\HTTP\IncomingRequest ? $request : service('request');
        $uri = $req->getUri();
        $path = trim($uri->getPath(), '/');
        $path = preg_replace('#^index\.php/?#', '', $path) ?? $path;

        // 1. /workspace/operator/* (Operator, Administrator, Super Administrator, serta peran modular admin_{...})
        if (str_starts_with($path, 'workspace/operator')) {
            $allowed = ['operator', 'administrator', 'super_administrator'];
            $hasPrefixAdmin = false;
            foreach ($effectiveRoles as $r) {
                if (str_starts_with(strtolower(trim((string) $r)), 'admin_')) {
                    $hasPrefixAdmin = true;
                    break;
                }
            }
            if (empty(array_intersect($allowed, $effectiveRoles)) && ! $hasPrefixAdmin) {
                return redirect()->to(site_url('workspace'))
                    ->with('error', 'Akses ditolak: Anda tidak memiliki wewenang Operator / Pengelola.');
            }
        }

        // 2. /workspace/trainer/* (Pengajar, Asisten Trainer/Operator pada kelas ditugaskan, serta Admin Pembelajaran)
        if (str_starts_with($path, 'workspace/trainer')) {
            $allowed = ['trainer', 'operator', 'administrator', 'super_administrator', 'admin_arenabelajar'];
            if (empty(array_intersect($allowed, $effectiveRoles))) {
                return redirect()->to(site_url('workspace'))
                    ->with('error', 'Akses ditolak: Anda tidak memiliki wewenang Pengajar / Asisten Trainer.');
            }
        }

        // 3. /workspace/admin/*
        if (str_starts_with($path, 'workspace/admin')) {
            $allowed = ['administrator', 'super_administrator'];
            if (empty(array_intersect($allowed, $effectiveRoles))) {
                return redirect()->to(site_url('workspace'))
                    ->with('error', 'Akses ditolak: Area ini dikhususkan untuk Administrator.');
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}

<?php

namespace App\Services\Identity;

class AuthorizationService
{
    public function can(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, (array) session()->get('permissions'), true);
    }

    public function canAny(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $userPermissions = (array) session()->get('permissions');
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions, true)) {
                return true;
            }
        }

        return false;
    }

    public function canModule(string $domainPrefix): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $prefix = rtrim($domainPrefix, '.') . '.';
        $userPermissions = (array) session()->get('permissions');
        foreach ($userPermissions as $p) {
            if (str_starts_with((string) $p, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function isSuperAdmin(): bool
    {
        $roles = (array) session()->get('roles');
        return in_array('super_administrator', $roles, true);
    }

    public function isAdmin(): bool
    {
        $roles = (array) session()->get('roles');
        return $this->isSuperAdmin() || in_array('administrator', $roles, true);
    }

    /**
     * Memeriksa apakah pengguna memiliki peran dinamis dengan format/prefix admin_{...}
     * Contoh: admin_regulasi, admin_pembelajaran, admin_berita, admin_sertifikat, dll.
     */
    public function isPrefixAdmin(?array $userRoles = null): bool
    {
        $roles = $userRoles ?? (array) session()->get('roles');
        foreach ($roles as $r) {
            if (str_starts_with(strtolower(trim((string) $r)), 'admin_')) {
                return true;
            }
        }
        return false;
    }

    public function hasDashboardAccess(
        ?int $userId = null,
        ?array $userRoles = null,
        ?array $userPermissions = null,
        ?string $accountStatus = null
    ): bool {
        $status = $accountStatus ?? session()->get('account_status');
        if ($status !== null && $status !== 'active') {
            return false;
        }

        $roles = $userRoles ?? (array) session()->get('roles');

        // 1. Super Administrator & Administrator (Akses Penuh)
        if (in_array('super_administrator', $roles, true) || in_array('administrator', $roles, true)) {
            return true;
        }

        // 2. Peran dinamis dengan format/prefix admin_{...} (Akses Modular Dashboard)
        if ($this->isPrefixAdmin($roles)) {
            return true;
        }

        // 3. Seluruh peran lainnya (Operator, Trainer, Peserta)
        // Murni beraktivitas di Workspace dan tidak diberikan akses ke Dashboard
        return false;
    }

    /**
     * Menentukan rute pendaratan (landing route) terbaik di Dashboard
     * untuk admin modular berprefix admin_{...} atau pengguna dengan hak akses tertentu.
     */
    public function getFirstAllowedDashboardRoute(?array $userPermissions = null, ?array $userRoles = null): string
    {
        $roles = $userRoles ?? (array) session()->get('roles');
        $permissions = $userPermissions ?? (array) session()->get('permissions');

        // Super Admin & Admin penuh selalu mendarat di Dashboard Utama
        if (in_array('super_administrator', $roles, true) || in_array('administrator', $roles, true)) {
            return 'dashboard';
        }

        // Jika memiliki izin melihat ringkasan dashboard utama
        if (in_array('dashboard.view', $permissions, true) || in_array('dashboard.indicators.view', $permissions, true)) {
            return 'dashboard';
        }

        // Pemetaan domain & alias bahasa Indonesia ke rute dashboard kanonik
        $moduleMappings = [
            'pembelajaran'  => 'dashboard/pembelajaran',
            'learning'      => 'dashboard/pembelajaran',
            'lms'           => 'dashboard/pembelajaran',
            'regulasi'      => 'dashboard/regulasi/dokumen',
            'regulation'    => 'dashboard/regulasi/dokumen',
            'event'         => 'dashboard/komunikasi/event-agenda',
            'berita'        => 'dashboard/komunikasi/berita',
            'communication' => 'dashboard/komunikasi/event-agenda',
            'bunga'         => 'dashboard/bunga-rampai/artikel',
            'kms'           => 'dashboard/bunga-rampai/artikel',
            'sertifikat'    => 'dashboard/sertifikat/riwayat',
            'certificate'   => 'dashboard/sertifikat/riwayat',
            'landing'       => 'dashboard/landing/sorotan-carousel',
            'access'        => 'dashboard/akses/pengguna',
            'user'          => 'dashboard/akses/pengguna',
            'intelligence'  => 'dashboard/intelligence',
            'reports'       => 'dashboard/laporan/pembelajaran',
            'laporan'       => 'dashboard/laporan/pembelajaran',
            'survey'        => 'dashboard/survei-kepuasan',
            'survei'        => 'dashboard/survei-kepuasan',
            'approval'      => 'dashboard/sistem/approval',
            'approvals'     => 'dashboard/sistem/approval',
            'audit'         => 'dashboard/sistem/audit-trail',
            'notification'  => 'dashboard/sistem/notifikasi',
            'notifikasi'    => 'dashboard/sistem/notifikasi',
            'system'        => 'dashboard/sistem/pengaturan',
            'pengaturan'    => 'dashboard/sistem/pengaturan',
        ];

        // 1. Prioritas pencocokan berdasarkan nama peran berprefix admin_{...}
        foreach ($roles as $r) {
            $r = strtolower(trim((string) $r));
            if (str_starts_with($r, 'admin_')) {
                $sub = substr($r, 6);
                foreach ($moduleMappings as $domain => $url) {
                    if (str_starts_with($sub, $domain) || str_starts_with($domain, $sub)) {
                        return $url;
                    }
                }
            }
        }

        // 2. Pencocokan berdasarkan permissions yang dimiliki pengguna
        foreach ($moduleMappings as $domain => $url) {
            $prefix = $domain . '.';
            foreach ($permissions as $p) {
                if (str_starts_with((string) $p, $prefix) || (string) $p === $domain) {
                    return $url;
                }
            }
        }

        return 'dashboard';
    }
}

<?php

namespace App\Filters;

use App\Models\UserModel;
use App\Services\Identity\AuthorizationService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthenticationFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $path = trim($request->getUri()->getPath(), '/');
        $path = preg_replace('#^index\.php/?#', '', $path) ?? $path;
        $publicPaths = [
            'dashboard/masuk',
            'dashboard/lupa-kata-sandi',
            'dashboard/keluar',
            'dashboard/status-akun',
        ];

        if (
            in_array($path, $publicPaths, true)
            || str_starts_with($path, 'dashboard/daftar')
            || preg_match('#^dashboard/reset-kata-sandi/[^/]+$#', $path)
        ) {
            return null;
        }

        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk mengakses dashboard.');
        }

        $userId = (int) session()->get('user_id');
        $userModel = new UserModel();
        $user = $userId > 0 ? $userModel->find($userId) : null;
        if (! $user || ($user['account_status'] ?? null) !== 'active') {
            $pendingUserId = $userId;
            session()->destroy();
            if ($pendingUserId > 0) {
                session()->setFlashdata('pending_user_id', $pendingUserId);
            }

            return redirect()->to(site_url('dashboard/status-akun'));
        }

        // Sinkronisasi real-time roles dan permissions dari database (Zero Stale Session)
        $userRoles = $userModel->getRoles($userId);
        $roleKeys = array_unique(array_filter(array_column($userRoles, 'key')));
        $permissions = $userModel->getPermissions($userId);

        session()->set('roles', $roleKeys);
        session()->set('permissions', $permissions);
        session()->set('account_status', $user['account_status']);

        $authService = new AuthorizationService();
        $hasDashboard = $authService->hasDashboardAccess($userId, $roleKeys, $permissions, $user['account_status']);

        if (! $hasDashboard) {
            $workspaceRoles = ['peserta', 'participant', 'trainer', 'operator'];
            if (array_intersect($roleKeys, $workspaceRoles)) {
                return redirect()->to(site_url('workspace'))
                    ->with('info', 'Anda dialihkan ke Workspace Fistudy sesuai hak akses peran akun Anda.');
            }

            return service('response')->setStatusCode(403)->setBody(view('errors/html/error_403'));
        }

        // Smart landing redirect: jika mengakses root dashboard tetapi tidak memiliki izin dashboard.view
        if ($path === 'dashboard' || $path === '') {
            if (! $authService->isAdmin() && ! $authService->canAny(['dashboard.view', 'dashboard.indicators.view'])) {
                $targetRoute = $authService->getFirstAllowedDashboardRoute($permissions, $roleKeys);
                if ($targetRoute !== 'dashboard' && $targetRoute !== '') {
                    return redirect()->to(site_url($targetRoute));
                }
            }
        }

        // Full bypass for Super Administrator
        if ($authService->isSuperAdmin()) {
            return null;
        }

        // Dynamic Zone-to-Permission Mapping across all Dashboard Modules
        $zoneMap = [
            // 1. Roles & Permissions (Super Admin or access.role.manage)
            [
                'pattern' => '#^dashboard/akses/(role|permission)#',
                'perms'   => ['access.role.manage', 'role.manage'],
                'label'   => 'Role & Hak Akses',
            ],
            // 2. Verifikasi Profil & Perubahan Profil
            [
                'pattern' => '#^dashboard/akses/(verifikasi-profil|permohonan-perubahan-profil)#',
                'perms'   => ['access.verification.view', 'access.verification.approve', 'user.verify', 'approvals.review'],
                'label'   => 'Verifikasi Profil',
            ],
            // 3. Permohonan Role
            [
                'pattern' => '#^dashboard/akses/permohonan-role#',
                'perms'   => ['access.role.manage', 'role.manage', 'approvals.review'],
                'label'   => 'Permohonan Role',
            ],
            // 4. Pengguna
            [
                'pattern' => '#^dashboard/akses/pengguna#',
                'perms'   => ['access.user.view', 'access.user.manage', 'access.manage', 'user.manage'],
                'label'   => 'Manajemen Pengguna',
            ],
            // 5. Unit Organisasi & Peran Pengadaan
            [
                'pattern' => '#^dashboard/akses/(unit-organisasi|peran-pengadaan|penugasan-pengadaan)#',
                'perms'   => ['access.organization.view', 'access.organization.manage', 'access.procurementrole.manage', 'access.manage'],
                'label'   => 'Unit Organisasi & Peran Pengadaan',
            ],
            // 6. Kelola Landing Page
            [
                'pattern' => '#^dashboard/landing#',
                'perms'   => ['landing.view', 'landing.slider.manage', 'landing.profile.manage', 'landing.portal.manage', 'landing.team.manage', 'landing.announcement.manage'],
                'label'   => 'Kelola Landing Page',
            ],
            // 7. Arena Belajar
            [
                'pattern' => '#^dashboard/pembelajaran#',
                'perms'   => [
                    'learning.view', 'lms.manage', 'learning.class.view', 'learning.class.create', 'learning.class.update', 'learning.class.delete',
                    'learning.assessment.view', 'learning.assessment.manage', 'learning.enrollment.view', 'learning.enrollment.verify',
                    'assessment.evaluate', 'enrollment.verify', 'learning.program.manage', 'learning.course.manage',
                    'learning.curriculum.manage', 'learning.certificate.manage', 'learning.report.view', 'learning.grading.manage',
                ],
                'label'   => 'Arena Belajar',
            ],
            // 8. Bunga Rampai
            [
                'pattern' => '#^dashboard/bunga-rampai#',
                'perms'   => [
                    'bunga.view', 'kms.manage', 'bunga.article.manage', 'bunga.faq.manage', 'bunga.casestudy.manage',
                    'bunga.template.manage', 'bunga.media.manage', 'bunga.taxonomy.manage',
                ],
                'label'   => 'Bunga Rampai',
            ],
            // 9. Regulasi PBJ
            [
                'pattern' => '#^dashboard/regulasi#',
                'perms'   => [
                    'regulation.view', 'regulation.manage', 'regulation.create', 'regulation.edit', 'regulation.delete',
                    'regulation.approve', 'regulation.category.manage', 'regulation.stage.manage',
                ],
                'label'   => 'Regulasi PBJ',
            ],
            // 10. Komunikasi
            [
                'pattern' => '#^dashboard/komunikasi#',
                'perms'   => [
                    'communication.view', 'communication.news.manage', 'communication.event.view', 'communication.event.manage',
                    'communication.attendance.manage', 'communication.inbox.manage', 'communication.helpdesk.manage', 'communication.newsletter.manage',
                ],
                'label'   => 'Komunikasi & Event',
            ],
            // 11. Sertifikat
            [
                'pattern' => '#^dashboard/sertifikat#',
                'perms'   => [
                    'certificate.view', 'certificate.manage', 'certificate.batch.manage', 'certificate.template.manage', 'certificate.approve',
                ],
                'label'   => 'Sertifikat',
            ],
            // 12. Government Intelligence
            [
                'pattern' => '#^dashboard/intelligence#',
                'perms'   => [
                    'intelligence.view', 'intelligence.jfppbj.manage', 'intelligence.competency.manage', 'intelligence.opd.manage',
                ],
                'label'   => 'Government Intelligence',
            ],
            // 13. Pusat Laporan
            [
                'pattern' => '#^dashboard/laporan#',
                'perms'   => [
                    'reports.view', 'reports.learning.view', 'reports.event.view', 'reports.certificate.view',
                    'reports.competency.view', 'reports.user.view', 'reports.export',
                ],
                'label'   => 'Pusat Laporan',
            ],
            // 14. Survei Kepuasan
            [
                'pattern' => '#^dashboard/survei-kepuasan#',
                'perms'   => ['survey.view', 'survey.manage'],
                'label'   => 'Survei Kepuasan',
            ],
            // 15. Pengaturan Sistem
            [
                'pattern' => '#^(dashboard/sistem/pengaturan|dashboard/settings|dashboard/pengaturan|dashboard/sistem/maintenance)#',
                'perms'   => ['system.settings', 'system.maintenance'],
                'label'   => 'Pengaturan Sistem',
            ],
            // 16. Approval Hub
            [
                'pattern' => '#^(dashboard/sistem/approval|dashboard/approvals)#',
                'perms'   => ['approval.view', 'approvals.review', 'approval.decide'],
                'label'   => 'Approval Hub',
            ],
            // 17. Audit Trail
            [
                'pattern' => '#^(dashboard/sistem/audit-trail|dashboard/akses/audit|dashboard/sistem/audit)#',
                'perms'   => ['audit.view', 'audit.export'],
                'label'   => 'Audit Trail',
            ],
            // 18. Pusat Notifikasi
            [
                'pattern' => '#^dashboard/sistem/notifikasi#',
                'perms'   => ['notification.view', 'notification.send'],
                'label'   => 'Pusat Notifikasi',
            ],
        ];

        foreach ($zoneMap as $zone) {
            if (preg_match($zone['pattern'], $path)) {
                // If Administrator role, grant access to all zones except Role & Permission
                if ($authService->isAdmin()) {
                    if (str_starts_with($path, 'dashboard/akses/role') || str_starts_with($path, 'dashboard/akses/permission')) {
                        if (! $authService->canAny($zone['perms'])) {
                            return service('response')->setStatusCode(403)->setBody(
                                view('dashboard/errors/403', [
                                    'message' => 'Hanya Super Administrator yang berhak mengelola Role & Permission.',
                                ])
                            );
                        }
                    }
                    return null;
                }

                // Check general zone permission
                if (! $authService->canAny($zone['perms'])) {
                    if ($request->isAJAX()) {
                        return service('response')->setStatusCode(403)->setJSON([
                            'status'  => 'error',
                            'message' => "Akses ditolak: Anda tidak memiliki izin untuk mengakses modul {$zone['label']}.",
                        ]);
                    }

                    return service('response')->setStatusCode(403)->setBody(
                        view('dashboard/errors/403', [
                            'message' => "Akses ditolak: Anda tidak memiliki izin untuk mengakses modul {$zone['label']}.",
                        ])
                    );
                }

                // Protect Mutating / Write Actions (POST or mutating URL patterns)
                $method = strtoupper($request->getMethod());
                $isMutatingMethod = in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true);
                $isMutatingRoute  = (bool) preg_match('#/(tambah|simpan|edit|update|hapus|delete|toggle|approve|reject|batal|create|store|sync)(\b|/|$)#i', $path);

                if ($isMutatingMethod || $isMutatingRoute) {
                    $hasWritePermission = false;
                    foreach ($zone['perms'] as $permKey) {
                        if (! str_ends_with($permKey, '.view') && ! str_ends_with($permKey, '.indicators.view')) {
                            if ($authService->can($permKey)) {
                                $hasWritePermission = true;
                                break;
                            }
                        }
                    }

                    if (! $hasWritePermission) {
                        if ($request->isAJAX()) {
                            return service('response')->setStatusCode(403)->setJSON([
                                'status'  => 'error',
                                'message' => "Anda hanya memiliki hak akses Lihat (Read-Only) pada modul {$zone['label']}.",
                            ]);
                        }

                        return service('response')->setStatusCode(403)->setBody(
                            view('dashboard/errors/403', [
                                'message' => "Anda hanya memiliki hak akses Lihat (Read-Only) pada modul {$zone['label']}.",
                            ])
                        );
                    }
                }

                break;
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

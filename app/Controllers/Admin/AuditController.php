<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Manajemen Akses → Audit Akses.
 *
 * Read-only audit log viewer with filters (actor, action, module, date).
 * Backed by AuditLogModel::browse() helper.
 */
class AuditController extends BaseController
{
    public function index()
    {
        $model = new \App\Models\AuditLogModel();
        $filters = [
            'userId' => $this->request->getGet('user_id') ? (int) $this->request->getGet('user_id') : null,
            'module' => $this->request->getGet('module') ?: null,
            'from'   => $this->request->getGet('from') ?: null,
            'to'     => $this->request->getGet('to') ?: null,
        ];

        $page = max(1, (int) ($this->request->getGet('page_audit') ?: 1));
        $perPage = 25;
        $builder = $model->browse($filters['module'], $filters['userId'], $filters['from'], $filters['to']);
        $total = (clone $builder)->countAllResults();
        $items = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $pager = service('pager');
        $pager->store('audit', $page, $perPage, $total, 0);

        $db = db_connect();
        $today = date('Y-m-d 00:00:00');
        $stats = [
            'total'       => (int) $db->table('audit_logs')->countAllResults(),
            'today'       => (int) $db->table('audit_logs')->where('created_at >=', $today)->countAllResults(),
            'success'     => (int) $db->table('audit_logs')->where('result', 'success')->countAllResults(),
            'risk_failed' => (int) $db->table('audit_logs')->whereIn('result', ['failure', 'error', 'denied'])->countAllResults(),
        ];

        return view('dashboard/audit/index', [
            'title'         => 'Audit Akses & Log Sistem | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Audit Trail & Log Aktivitas',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'audit',
            'items'         => $items,
            'stats'         => $stats,
            'filters'       => $filters,
            'pager'         => $pager,
            'modules'       => $model->distinctModules(),
        ]);
    }

    public function detail(string $auditId)
    {
        $id = ctype_digit($auditId) ? (int) $auditId : 0;
        $item = (new \App\Models\AuditLogModel())->find($id);

        return view('dashboard/audit/detail', [
            'title'         => 'Detail Audit | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Audit',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'audit',
            'item'          => $item,
            'auditId'       => $auditId,
        ]);
    }

    public function files()
    {
        return view('dashboard/audit/files', [
            'title'         => 'File dan Media | Dashboard BegawiPengadaan',
            'pageTitle'     => 'File dan Media',
            'activeMenu'    => 'system',
            'activeSubmenu' => 'files-media',
        ]);
    }

    public function notifications()
    {
        return (new NotificationController())->index();
    }
}

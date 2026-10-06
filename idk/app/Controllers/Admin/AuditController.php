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
            'module' => $this->request->getGet('module'),
            'from'   => $this->request->getGet('from'),
            'to'     => $this->request->getGet('to'),
        ];

        $page = max(1, (int) ($this->request->getGet('page_audit') ?: 1));
        $perPage = 25;
        $builder = $model->browse(...array_values($filters));
        $total = (clone $builder)->countAllResults();
        $items = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $pager = service('pager');
        $pager->store('audit', $page, $perPage, $total, 0);

        return view('dashboard/audit/index', [
            'title'         => 'Audit Akses | Dashboard BegawiPBJ',
            'pageTitle'     => 'Audit Akses',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'audit',
            'items'         => $items,
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
            'title'         => 'Detail Audit | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Audit',
            'activeMenu'    => 'access',
            'activeSubmenu' => 'audit',
            'item'          => $item,
            'auditId'       => $auditId,
        ]);
    }
}
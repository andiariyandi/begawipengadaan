<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class AuditController extends BaseController
{
    public function index()
    {
        $module = $this->request->getGet('module') ?: null;
        $from   = $this->request->getGet('from') ?: null;
        $to     = $this->request->getGet('to') ?: null;

        $auditLogModel = new AuditLogModel();
        $builder       = $auditLogModel->browse($module, null, $from, $to);

        $perPage = 25;
        $logs    = $builder->paginate($perPage);

        return view('admin/audit_logs', [
            'title'    => 'Audit Log',
            'subTitle' => 'Audit Log',
            'logs'     => $logs,
            'pager'    => $auditLogModel->pager,
            'modules'  => $auditLogModel->distinctModules(),
            'module'   => $module,
            'from'     => $from,
            'to'       => $to,
        ]);
    }
}

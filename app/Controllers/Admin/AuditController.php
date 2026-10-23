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
            'activeMenu'    => 'system',
            'activeSubmenu' => 'audit-trail',

            'items'         => $items,
            'stats'         => $stats,
            'filters'       => $filters,
            'pager'         => $pager,
            'modules'       => $model->distinctModules(),
        ]);
    }

    public function exportCsv()
    {
        $model = new \App\Models\AuditLogModel();
        $filters = [
            'userId' => $this->request->getGet('user_id') ? (int) $this->request->getGet('user_id') : null,
            'module' => $this->request->getGet('module') ?: null,
            'from'   => $this->request->getGet('from') ?: null,
            'to'     => $this->request->getGet('to') ?: null,
        ];

        $builder = $model->browse($filters['module'], $filters['userId'], $filters['from'], $filters['to']);
        $logs = $builder->limit(1000)->get()->getResultArray();

        // Audit the export action
        $actorId = (int) (session()->get('user_id') ?: 0);
        try {
            (new \App\Services\AuditService())->log(
                $actorId ?: null,
                'export_csv',
                'audit_logs',
                'audit_logs',
                null,
                [
                    'filters'   => array_filter($filters),
                    'row_count' => count($logs),
                ]
            );
        } catch (\Throwable $e) {
            // Silently continue export even if audit write fails
        }

        $filename = 'audit-trail-' . date('Ymd-His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // Output UTF-8 BOM for Microsoft Excel compatibility
        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, [
            'ID Log',
            'Waktu (WIB)',
            'ID Aktor',
            'Nama Aktor',
            'Email Aktor',
            'Tindakan (Action)',
            'Tipe Resource (Modul)',
            'ID Resource',
            'Hasil',
            'Keterangan / Reason',
        ]);

        foreach ($logs as $row) {
            $actorName = ! empty($row['actor_name'])
                ? $row['actor_name']
                : (! empty($row['username']) ? $row['username'] : 'Sistem / Anonim');

            fputcsv($out, [
                $row['id'] ?? '',
                $row['created_at'] ?? '',
                $row['actor_id'] ?? '',
                $actorName,
                $row['actor_email'] ?? '',
                $row['action'] ?? '',
                $row['resource_type'] ?? '',
                $row['resource_id'] ?? '',
                $row['result'] ?? '',
                $row['reason'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }

    public function detail(string $auditId)
    {
        $id = ctype_digit($auditId) ? (int) $auditId : 0;
        $item = (new \App\Models\AuditLogModel())->find($id);

        return view('dashboard/audit/detail', [
            'title'         => 'Detail Audit | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Audit',
            'activeMenu'    => 'system',
            'activeSubmenu' => 'audit-trail',

            'item'          => $item,
            'auditId'       => $auditId,
        ]);
    }

    public function files()
    {
        $db = db_connect();
        $files = $db->table('file_assets')
            ->orderBy('created_at', 'DESC')
            ->limit(500)
            ->get()->getResultArray();

        return view('dashboard/audit/files', [
            'files'         => $files,
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

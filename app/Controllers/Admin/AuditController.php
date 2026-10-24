<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Manajemen Akses → Audit Akses.
 *
 * Read-only audit log viewer with filters (actor, action, module, date, keyword, result, sorting).
 * Backed by AuditLogModel::browse() helper.
 */
class AuditController extends BaseController
{
    public function index()
    {
        $model = new \App\Models\AuditLogModel();

        $keyword  = trim((string) ($this->request->getGet('q') ?: $this->request->getGet('keyword') ?: ''));
        $module   = trim((string) ($this->request->getGet('module') ?: ''));
        $result   = trim((string) ($this->request->getGet('result') ?: ''));
        $from     = trim((string) ($this->request->getGet('from') ?: ''));
        $to       = trim((string) ($this->request->getGet('to') ?: ''));
        $userId   = $this->request->getGet('user_id') ? (int) $this->request->getGet('user_id') : null;
        $sort     = (string) ($this->request->getGet('sort') ?: 'created_at');
        $dir      = strtolower((string) ($this->request->getGet('dir') ?: 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage  = max(5, min(200, (int) ($this->request->getGet('per_page') ?: 25)));
        $page     = max(1, (int) ($this->request->getGet('page_audit') ?: 1));

        $filters = [
            'q'        => $keyword,
            'keyword'  => $keyword,
            'module'   => $module,
            'result'   => $result,
            'from'     => $from,
            'to'       => $to,
            'userId'   => $userId,
            'sort'     => $sort,
            'dir'      => $dir,
            'per_page' => $perPage,
        ];

        // 1. Hitung total baris yang cocok dengan filter (Count Query terpisah dan terisolasi)
        $countModel = new \App\Models\AuditLogModel();
        $countBuilder = $countModel->browse(
            $module ?: null,
            $userId,
            $from ?: null,
            $to ?: null,
            $keyword ?: null,
            $result ?: null
        );
        $total = $countBuilder->countAllResults();

        // 2. Ambil data halaman aktif dengan sorting server-side penuh
        $dataModel = new \App\Models\AuditLogModel();
        $itemsBuilder = $dataModel->browse(
            $module ?: null,
            $userId,
            $from ?: null,
            $to ?: null,
            $keyword ?: null,
            $result ?: null,
            $sort,
            $dir
        );
        $items = $itemsBuilder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $uriString = (string) $this->request->getUri()->getPath();
        $rawUri    = (string) ($_SERVER['REQUEST_URI'] ?? '');
        $isAksesRoute = (strpos($uriString, 'akses') !== false) || (strpos($rawUri, 'akses') !== false);
        $isSystemRoute = ! $isAksesRoute && (
            (strpos($uriString, 'sistem') !== false)
            || (strpos($rawUri, 'sistem') !== false)
            || (strpos(current_url(), 'sistem') !== false)
            || (strpos($uriString, 'audit-trail') !== false)
            || (strpos($rawUri, 'audit-trail') !== false)
        );

        $routePath   = $isSystemRoute ? 'dashboard/sistem/audit-trail' : 'dashboard/akses/audit';
        $baseUrl     = site_url($routePath);
        $exportUrl   = site_url($routePath . '/export');

        $pager = service('pager');
        $pager->setPath($routePath, 'audit');
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
            'total'         => $total,
            'page'          => $page,
            'perPage'       => $perPage,
            'modules'       => $model->distinctModules(),
            'routePath'     => $routePath,
            'baseUrl'       => $baseUrl,
            'exportBaseUrl' => $exportUrl,
            'isSystemRoute' => $isSystemRoute,
        ]);
    }

    public function exportCsv()
    {
        $model = new \App\Models\AuditLogModel();

        $keyword  = trim((string) ($this->request->getGet('q') ?: $this->request->getGet('keyword') ?: ''));
        $module   = trim((string) ($this->request->getGet('module') ?: ''));
        $result   = trim((string) ($this->request->getGet('result') ?: ''));
        $from     = trim((string) ($this->request->getGet('from') ?: ''));
        $to       = trim((string) ($this->request->getGet('to') ?: ''));
        $userId   = $this->request->getGet('user_id') ? (int) $this->request->getGet('user_id') : null;
        $sort     = (string) ($this->request->getGet('sort') ?: 'created_at');
        $dir      = strtolower((string) ($this->request->getGet('dir') ?: 'desc')) === 'asc' ? 'asc' : 'desc';

        $filters = [
            'q'        => $keyword,
            'keyword'  => $keyword,
            'module'   => $module,
            'result'   => $result,
            'from'     => $from,
            'to'       => $to,
            'userId'   => $userId,
            'sort'     => $sort,
            'dir'      => $dir,
        ];

        $builder = $model->browse(
            $module ?: null,
            $userId,
            $from ?: null,
            $to ?: null,
            $keyword ?: null,
            $result ?: null,
            $sort,
            $dir
        );

        $logs = $builder->limit(5000)->get()->getResultArray();

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
        $db = db_connect();

        $uriString = (string) $this->request->getUri()->getPath();
        $rawUri    = (string) ($_SERVER['REQUEST_URI'] ?? '');
        $isAksesRoute = (strpos($uriString, 'akses') !== false) || (strpos($rawUri, 'akses') !== false);
        $isSystemRoute = ! $isAksesRoute && (
            (strpos($uriString, 'sistem') !== false)
            || (strpos($rawUri, 'sistem') !== false)
            || (strpos(current_url(), 'sistem') !== false)
            || (strpos($uriString, 'audit-trail') !== false)
            || (strpos($rawUri, 'audit-trail') !== false)
        );

        $routePath = $isSystemRoute ? 'dashboard/sistem/audit-trail' : 'dashboard/akses/audit';
        $backUrl   = site_url($routePath);

        $item = $db->table('audit_logs')
            ->select('audit_logs.*, users.username, users.name as actor_name, users.email as actor_email, users.profile_type as actor_profile_type, users.account_status as actor_status')
            ->join('users', 'users.id = audit_logs.actor_id', 'left')
            ->where('audit_logs.id', $id)
            ->get()
            ->getRowArray();

        if (! $item) {
            return redirect()->to($backUrl)->with('error', "Catatan jejak audit #{$id} tidak ditemukan dalam sistem.");
        }

        // Ambil peran aktif pengguna / aktor jika ada
        $actorRoles = [];
        if (! empty($item['actor_id'])) {
            $actorRoles = $db->table('user_roles ur')
                ->select('r.name, r.description')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('ur.user_id', (int) $item['actor_id'])
                ->where('ur.status', 'active')
                ->get()
                ->getResultArray();
        }

        // Ambil riwayat log terkait (5 aktivitas sebelumnya atau sesudahnya pada resource atau aktor yang sama)
        $relatedLogs = [];
        if (! empty($item['resource_type']) && ! empty($item['resource_id'])) {
            $relatedLogs = $db->table('audit_logs')
                ->select('audit_logs.id, audit_logs.action, audit_logs.resource_type, audit_logs.resource_id, audit_logs.result, audit_logs.created_at, users.name as actor_name, users.username')
                ->join('users', 'users.id = audit_logs.actor_id', 'left')
                ->where('audit_logs.resource_type', $item['resource_type'])
                ->where('audit_logs.resource_id', $item['resource_id'])
                ->where('audit_logs.id !=', $id)
                ->orderBy('audit_logs.created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        } elseif (! empty($item['actor_id'])) {
            $relatedLogs = $db->table('audit_logs')
                ->select('audit_logs.id, audit_logs.action, audit_logs.resource_type, audit_logs.resource_id, audit_logs.result, audit_logs.created_at, users.name as actor_name, users.username')
                ->join('users', 'users.id = audit_logs.actor_id', 'left')
                ->where('audit_logs.actor_id', $item['actor_id'])
                ->where('audit_logs.id !=', $id)
                ->orderBy('audit_logs.created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        }

        // Hitung visual diff dan decode snapshot
        $beforeData = ! empty($item['before_snapshot']) ? json_decode((string) $item['before_snapshot'], true) : null;
        $afterData  = ! empty($item['after_snapshot']) ? json_decode((string) $item['after_snapshot'], true) : null;
        $reasonData = ! empty($item['reason']) ? json_decode((string) $item['reason'], true) : null;

        // Visual Diff Calculator
        $diffEntries = [];
        if (is_array($beforeData) || is_array($afterData)) {
            $beforeArr = is_array($beforeData) ? $beforeData : [];
            $afterArr  = is_array($afterData) ? $afterData : [];
            $allKeys   = array_unique(array_merge(array_keys($beforeArr), array_keys($afterArr)));
            sort($allKeys);

            foreach ($allKeys as $k) {
                $hasBefore = array_key_exists($k, $beforeArr);
                $hasAfter  = array_key_exists($k, $afterArr);
                $bVal      = $hasBefore ? $beforeArr[$k] : null;
                $aVal      = $hasAfter ? $afterArr[$k] : null;

                if (! $hasBefore && $hasAfter) {
                    $status = 'added';
                } elseif ($hasBefore && ! $hasAfter) {
                    $status = 'removed';
                } elseif (json_encode($bVal) !== json_encode($aVal)) {
                    $status = 'modified';
                } else {
                    $status = 'unchanged';
                }

                $diffEntries[] = [
                    'key'    => $k,
                    'before' => $bVal,
                    'after'  => $aVal,
                    'status' => $status,
                ];
            }
        }

        // Tentukan resource link jika entitas dapat diakses di dashboard
        $resourceLink = null;
        if (! empty($item['resource_type']) && ! empty($item['resource_id'])) {
            $rType = strtolower(trim((string) $item['resource_type']));
            $rId   = (int) $item['resource_id'];
            if (in_array($rType, ['user', 'users', 'pengguna'], true)) {
                $resourceLink = site_url('dashboard/akses/pengguna/' . $rId);
            } elseif (in_array($rType, ['role', 'roles'], true)) {
                $resourceLink = site_url('dashboard/akses/role/' . $rId);
            } elseif (in_array($rType, ['peran_pengadaan', 'procurement_role', 'procurement_roles'], true)) {
                $resourceLink = site_url('dashboard/akses/peran-pengadaan/' . $rId);
            } elseif (in_array($rType, ['unit_organisasi', 'organizations', 'organization'], true)) {
                $resourceLink = site_url('dashboard/akses/unit-organisasi/' . $rId);
            } elseif (in_array($rType, ['class', 'classes', 'kelas'], true)) {
                $resourceLink = site_url('dashboard/pembelajaran/kelas/' . $rId);
            } elseif (in_array($rType, ['regulation', 'regulations', 'regulasi'], true)) {
                $resourceLink = site_url('dashboard/regulasi/dokumen');
            } elseif (in_array($rType, ['system', 'settings'], true)) {
                $resourceLink = site_url('dashboard/sistem/pengaturan');
            }
        }

        return view('dashboard/audit/detail', [
            'title'         => 'Detail Audit Trail #' . $id . ' | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Detail Jejak Audit',
            'activeMenu'    => $isSystemRoute ? 'system' : 'access',
            'activeSubmenu' => $isSystemRoute ? 'audit-trail' : 'audit',

            'item'          => $item,
            'auditId'       => $id,
            'actorRoles'    => $actorRoles,
            'relatedLogs'   => $relatedLogs,
            'beforeData'    => $beforeData,
            'afterData'     => $afterData,
            'reasonData'    => $reasonData,
            'diffEntries'   => $diffEntries,
            'resourceLink'  => $resourceLink,
            'routePath'     => $routePath,
            'backUrl'       => $backUrl,
            'isSystemRoute' => $isSystemRoute,
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

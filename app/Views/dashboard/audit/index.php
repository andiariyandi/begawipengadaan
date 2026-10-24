<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items   = $items ?? [];
$filters = $filters ?? [];
$modules = $modules ?? [];
$stats   = $stats ?? ['total' => count($items), 'today' => 0, 'success' => 0, 'risk_failed' => 0];
$total   = $total ?? ($stats['total'] ?? count($items));
$page    = $page ?? 1;
$perPage = $perPage ?? 25;

$isSystemRoute = $isSystemRoute ?? (strpos(current_url(), 'sistem') !== false || strpos($_SERVER['REQUEST_URI'] ?? '', 'sistem') !== false || strpos($_SERVER['REQUEST_URI'] ?? '', 'audit-trail') !== false);
$routePath     = $routePath ?? ($isSystemRoute ? 'dashboard/sistem/audit-trail' : 'dashboard/akses/audit');
$baseUrl       = $baseUrl ?? site_url($routePath);
$exportBaseUrl = $exportBaseUrl ?? site_url($routePath . '/export');
$exportParams  = array_filter($filters ?? [], static fn($v) => $v !== null && $v !== '');
unset($exportParams['per_page']); // Ekspor mengambil seluruh data hasil filter
if (isset($exportParams['q']) && isset($exportParams['keyword'])) {
    unset($exportParams['keyword']);
}
$filterQuery   = http_build_query($exportParams);
$exportUrl     = $filterQuery !== '' ? ($exportBaseUrl . '?' . $filterQuery) : $exportBaseUrl;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Audit Trail & Log Aktivitas') ?></h6>
        <p class="text-secondary-light mb-0">Jejak audit kepatuhan, mutasi data, dan riwayat aktivitas pengguna back-office.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= esc($exportUrl) ?>" class="btn btn-outline-success-600 radius-8 px-16 py-9 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:download-square-outline" class="text-xl"></iconify-icon>
            <span>Ekspor CSV Log</span>
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Total Jejak Log</span>
                    <h5 class="fw-semibold mb-0"><?= number_format($stats['total'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:history-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Aktivitas Hari Ini</span>
                    <h5 class="fw-semibold text-primary-600 mb-0"><?= number_format($stats['today'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:calendar-date-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Eksekusi Berhasil</span>
                    <h5 class="fw-semibold text-success-main mb-0"><?= number_format($stats['success'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-success-50 text-success-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Gagal / Ditolak</span>
                    <h5 class="fw-semibold text-danger-main mb-0"><?= number_format($stats['risk_failed'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-danger-50 text-danger-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:shield-warning-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card radius-12 mb-24 border">
    <div class="card-body p-20">
        <form method="get" action="<?= esc($baseUrl) ?>">
            <input type="hidden" name="sort" value="<?= esc($filters['sort'] ?? 'created_at') ?>">
            <input type="hidden" name="dir" value="<?= esc($filters['dir'] ?? 'desc') ?>">

            <div class="row g-3 align-items-end">
                <!-- Pencarian Cepat -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Pencarian Cepat</label>
                    <div class="position-relative">
                        <input type="text" name="q" class="form-control radius-8 ps-36" placeholder="Aktor, email, aksi, ID..." value="<?= esc($filters['keyword'] ?? '') ?>">
                        <iconify-icon icon="solar:magnifer-outline" class="position-absolute top-50 start-0 translate-middle-y ms-12 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>

                <!-- Modul / Resource -->
                <div class="col-lg-2 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Modul / Resource</label>
                    <select name="module" class="form-select radius-8">
                        <option value="">Semua Modul</option>
                        <?php foreach ($modules as $m): ?>
                            <?php $val = $m['resource_type'] ?? ''; ?>
                            <option value="<?= esc($val) ?>" <?= ($filters['module'] ?? '') === $val ? 'selected' : '' ?>>
                                <?= esc(ucfirst(str_replace('_', ' ', $val))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status / Hasil -->
                <div class="col-lg-2 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Status / Hasil</label>
                    <select name="result" class="form-select radius-8">
                        <option value="">Semua Hasil</option>
                        <option value="success" <?= ($filters['result'] ?? '') === 'success' ? 'selected' : '' ?>>Berhasil (Success)</option>
                        <option value="failure" <?= ($filters['result'] ?? '') === 'failure' ? 'selected' : '' ?>>Gagal (Failure)</option>
                        <option value="error" <?= ($filters['result'] ?? '') === 'error' ? 'selected' : '' ?>>Error</option>
                        <option value="denied" <?= ($filters['result'] ?? '') === 'denied' ? 'selected' : '' ?>>Ditolak (Denied)</option>
                    </select>
                </div>

                <!-- Dari Tanggal -->
                <div class="col-lg-2 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control radius-8" value="<?= esc($filters['from'] ?? '') ?>">
                </div>

                <!-- Sampai Tanggal -->
                <div class="col-lg-2 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control radius-8" value="<?= esc($filters['to'] ?? '') ?>">
                </div>

                <!-- Baris Per Halaman -->
                <div class="col-lg-1 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Baris</label>
                    <select name="per_page" class="form-select radius-8">
                        <?php foreach ([10, 25, 50, 100] as $pp): ?>
                            <option value="<?= $pp ?>" <?= (int) ($filters['per_page'] ?? 25) === $pp ? 'selected' : '' ?>><?= $pp ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-12 d-flex justify-content-end gap-2 mt-8">
                    <a href="<?= esc($baseUrl) ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-9 d-inline-flex align-items-center gap-2" title="Reset Filter">
                        <iconify-icon icon="solar:restart-outline" class="text-lg"></iconify-icon>
                        <span>Reset</span>
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-9 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:filter-outline" class="text-lg"></iconify-icon>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Datatable -->
<?php
$currentSort = $filters['sort'] ?? 'created_at';
$currentDir  = strtolower($filters['dir'] ?? 'desc');
$buildSortLink = function (string $colKey, string $label, string $alignClass = '') use ($filters, $currentSort, $currentDir, $baseUrl) {
    $isActive    = ($currentSort === $colKey);
    $nextDir     = ($isActive && $currentDir === 'asc') ? 'desc' : 'asc';
    $params      = array_merge($filters, ['sort' => $colKey, 'dir' => $nextDir, 'page_audit' => 1]);
    if (isset($params['q']) && isset($params['keyword'])) {
        unset($params['keyword']);
    }
    $url         = $baseUrl . '?' . http_build_query(array_filter($params, static fn($v) => $v !== null && $v !== ''));
    $colorClass  = $isActive ? 'text-primary-600 fw-bold' : 'text-secondary-light hover-text-primary-600';
    $iconName    = $isActive 
        ? ($currentDir === 'asc' ? 'solar:sort-from-bottom-to-top-outline' : 'solar:sort-from-top-to-bottom-outline')
        : 'solar:sort-vertical-outline';
    $title       = 'Urutkan ' . $label . ' (' . ($isActive ? ($currentDir === 'asc' ? 'Z-A / Terbaru' : 'A-Z / Terlama') : 'Klik untuk mengurutkan') . ')';

    return '<a href="' . esc($url) . '" class="' . $colorClass . ' d-inline-flex align-items-center gap-1 ' . $alignClass . '" title="' . esc($title) . '">'
        . '<span>' . esc($label) . '</span>'
        . '<iconify-icon icon="' . $iconName . '" class="' . ($isActive ? 'text-sm' : 'text-xs') . '"></iconify-icon>'
        . '</a>';
};
?>
<div class="card radius-12 border">
    <div class="card-body p-24">
        <?php if (empty($items)): ?>
            <?= view('dashboard/components/empty_state', [
                'icon'        => 'solar:history-outline',
                'title'       => 'Belum Ada Log Audit',
                'message'     => 'Tidak ada catatan aktivitas sistem yang ditemukan untuk periode atau kriteria filter ini.',
                'actionUrl'   => $baseUrl,
                'actionLabel' => 'Reset Filter',
            ]) ?>
        <?php else: ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0 align-middle server-side-table dt-server-side" data-server-side="true" data-table-name="audit-trail-log" data-page-length="<?= (int) ($filters['per_page'] ?? 25) ?>">
                    <thead>
                        <tr>
                            <th><?= $buildSortLink('created_at', 'Waktu (WIB)') ?></th>
                            <th><?= $buildSortLink('actor', 'Pelaksana / Aktor') ?></th>
                            <th><?= $buildSortLink('action', 'Tindakan (Action)') ?></th>
                            <th><?= $buildSortLink('resource_type', 'Resource') ?></th>
                            <th><?= $buildSortLink('result', 'Hasil') ?></th>
                            <th class="text-center no-sort no-export action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $log): ?>
                            <tr>
                                <td>
                                    <span class="text-xs text-neutral-800 fw-medium">
                                        <?= esc(date('d M Y, H:i:s', strtotime($log['created_at'] ?? 'now'))) ?>
                                    </span>
                                </td>
                                <td>
                                    <h6 class="text-sm fw-semibold mb-0 text-neutral-900"><?= esc(!empty($log['actor_name']) ? $log['actor_name'] : ($log['username'] ?? 'Sistem / Anonim')) ?></h6>
                                    <span class="text-2xs text-secondary-light"><?= !empty($log['actor_email']) ? esc($log['actor_email']) . ' | ' : '' ?>Actor ID: #<?= esc($log['actor_id'] ?? '0') ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-100 text-neutral-900 radius-6 px-10 py-4 font-monospace text-xs">
                                        <?= esc($log['action'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-sm fw-medium text-neutral-800 d-block"><?= esc($log['resource_type'] ?? '-') ?></span>
                                    <span class="text-2xs text-secondary-light">ID: #<?= esc($log['resource_id'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <?= view('dashboard/components/status_badge', ['status' => $log['result'] ?? 'success']) ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 p-8 border">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= site_url(($isSystemRoute ? 'dashboard/sistem/audit-trail/' : 'dashboard/akses/audit/') . (int) $log['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Lihat Detail Log</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($pager)): ?>
                <div class="card-footer border-top bg-transparent pt-16 pb-0 px-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="text-secondary-light text-sm">
                        Menampilkan <?= number_format(min($total, ($page - 1) * $perPage + 1)) ?> - <?= number_format(min($total, $page * $perPage)) ?> dari <?= number_format($total) ?> jejak log
                    </div>
                    <div>
                        <?= $pager->links('audit', 'default_full') ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

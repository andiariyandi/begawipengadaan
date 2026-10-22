<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items   = $items ?? [];
$filters = $filters ?? [];
$modules = $modules ?? [];
$stats   = $stats ?? ['total' => count($items), 'today' => 0, 'success' => 0, 'risk_failed' => 0];

$isSystemRoute = (strpos(current_url(), 'sistem') !== false);
$exportBaseUrl = $isSystemRoute ? site_url('dashboard/sistem/audit-trail/export') : site_url('dashboard/akses/audit/export');
$filterQuery   = http_build_query(array_filter($filters ?? []));
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
        <form method="get" action="<?= current_url() ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control radius-8" value="<?= esc($filters['from'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control radius-8" value="<?= esc($filters['to'] ?? '') ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= current_url() ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset">
                        <iconify-icon icon="solar:restart-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Datatable -->
<div class="card radius-12 border">
    <div class="card-body p-24">
        <?php if (empty($items)): ?>
            <?= view('dashboard/components/empty_state', [
                'icon'        => 'solar:history-outline',
                'title'       => 'Belum Ada Log Audit',
                'message'     => 'Tidak ada catatan aktivitas sistem yang ditemukan untuk periode atau filter ini.',
            ]) ?>
        <?php else: ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0 align-middle begawi-table" data-table-name="audit-trail-log" data-page-length="25">
                    <thead>
                        <tr>
                            <th>Waktu (WIB)</th>
                            <th>Pelaksana / Aktor</th>
                            <th>Tindakan (Action)</th>
                            <th>Resource</th>
                            <th>Hasil</th>
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
                <?= $pager->links('audit', 'default_full') ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

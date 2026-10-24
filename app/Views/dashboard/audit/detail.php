<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$item          = $item ?? [];
$auditId       = $auditId ?? ($item['id'] ?? 0);
$actorRoles    = $actorRoles ?? [];
$relatedLogs   = $relatedLogs ?? [];
$beforeData    = $beforeData ?? null;
$afterData     = $afterData ?? null;
$reasonData    = $reasonData ?? null;
$diffEntries   = $diffEntries ?? [];
$resourceLink  = $resourceLink ?? null;
$routePath     = $routePath ?? 'dashboard/sistem/audit-trail';
$backUrl       = $backUrl ?? site_url($routePath);
$isSystemRoute = $isSystemRoute ?? true;

// Helper: Format Waktu Relatif
$timeAgo = static function ($datetime) {
    if (empty($datetime)) {
        return '-';
    }
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) {
        return 'Baru saja';
    }
    if ($diff < 3600) {
        return floor($diff / 60) . ' menit yang lalu';
    }
    if ($diff < 86400) {
        return floor($diff / 3600) . ' jam yang lalu';
    }
    if ($diff < 604800) {
        return floor($diff / 86400) . ' hari yang lalu';
    }
    return date('d M Y', $time);
};

// Helper: Format Nilai untuk Diff
$formatVal = static function ($val) {
    if ($val === null) {
        return '<span class="text-secondary-light fst-italic">(null)</span>';
    }
    if ($val === '') {
        return '<span class="text-secondary-light fst-italic">(kosong)</span>';
    }
    if (is_bool($val)) {
        return '<span class="badge ' . ($val ? 'bg-success-50 text-success-600' : 'bg-danger-50 text-danger-600') . ' font-monospace">' . ($val ? 'true' : 'false') . '</span>';
    }
    if (is_array($val)) {
        return '<pre class="mb-0 text-2xs font-monospace bg-neutral-100 p-8 radius-4">' . esc(json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . '</pre>';
    }
    return esc((string) $val);
};

// Helper: Humanize Action Name
$actionMap = [
    'login'                     => 'Autentikasi Pengguna Masuk',
    'logout'                    => 'Pengguna Keluar Sesi',
    'export_csv'                => 'Ekspor Berkas CSV Log',
    'system.clean_demo'         => 'Pembersihan Data Demo Sistem',
    'procurement_roles.created' => 'Pembuatan Peran Pengadaan Baru',
    'procurement_roles.updated' => 'Pembaruan Data Peran Pengadaan',
    'user.created'              => 'Pendaftaran / Pembuatan Pengguna',
    'user.updated'              => 'Pembaruan Data Pengguna',
    'user.status_changed'       => 'Perubahan Status Akun Pengguna',
    'role.assigned'             => 'Pemberian Role Hak Akses',
    'role.revoked'              => 'Pencabutan Role Hak Akses',
    'class.created'             => 'Pembuatan Kelas PBJ Baru',
    'class.updated'             => 'Pembaruan Data Kelas PBJ',
    'class.published'           => 'Publikasi Kelas PBJ',
];
$humanAction = $actionMap[$item['action'] ?? ''] ?? ucwords(str_replace(['.', '_', '-'], ' ', (string) ($item['action'] ?? 'Aktivitas Sistem')));

// Helper: Result Status Style
$resultType = strtolower((string) ($item['result'] ?? 'success'));
$resultConfig = match ($resultType) {
    'success' => [
        'card_bg'    => 'bg-success-50 border-success-200',
        'badge_cls'  => 'bg-success-600 text-white',
        'icon'       => 'solar:check-circle-bold',
        'icon_color' => 'text-success-600',
        'label'      => 'Eksekusi Berhasil',
        'desc'       => 'Aktivitas berhasil diverifikasi dan dieksekusi tanpa galat.',
    ],
    'failure' => [
        'card_bg'    => 'bg-danger-50 border-danger-200',
        'badge_cls'  => 'bg-danger-600 text-white',
        'icon'       => 'solar:close-circle-bold',
        'icon_color' => 'text-danger-600',
        'label'      => 'Eksekusi Gagal',
        'desc'       => 'Operasi gagal dieksekusi oleh sistem atau validasi tidak terpenuhi.',
    ],
    'error' => [
        'card_bg'    => 'bg-danger-50 border-danger-200',
        'badge_cls'  => 'bg-danger-600 text-white',
        'icon'       => 'solar:bug-bold',
        'icon_color' => 'text-danger-600',
        'label'      => 'Sistem Error',
        'desc'       => 'Terjadi kegagalan runtime atau pengecualian tidak terduga pada sistem.',
    ],
    'denied' => [
        'card_bg'    => 'bg-warning-50 border-warning-200',
        'badge_cls'  => 'bg-warning-600 text-white',
        'icon'       => 'solar:shield-warning-bold',
        'icon_color' => 'text-warning-600',
        'label'      => 'Akses Ditolak',
        'desc'       => 'Aksi diblokir oleh otorisasi RBAC atau pembatasan hak akses.',
    ],
    default => [
        'card_bg'    => 'bg-info-50 border-info-200',
        'badge_cls'  => 'bg-info-600 text-white',
        'icon'       => 'solar:info-circle-bold',
        'icon_color' => 'text-info-600',
        'label'      => ucfirst($resultType),
        'desc'       => 'Catatan hasil eksekusi tercatat dalam log audit.',
    ],
};
?>

<!-- Header & Breadcrumbs -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24 print-header">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 text-sm">
                <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>" class="text-secondary-light hover-text-primary-600">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= esc($backUrl) ?>" class="text-secondary-light hover-text-primary-600"><?= $isSystemRoute ? 'Sistem' : 'Manajemen Akses' ?></a></li>
                <li class="breadcrumb-item"><a href="<?= esc($backUrl) ?>" class="text-secondary-light hover-text-primary-600">Audit Trail</a></li>
                <li class="breadcrumb-item active text-primary-600 fw-semibold" aria-current="page">Log #<?= esc($auditId) ?></li>
            </ol>
        </nav>
        <h5 class="fw-semibold mb-1 d-flex align-items-center gap-2">
            <span>Detail Jejak Audit</span>
            <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-10 py-4 font-monospace text-sm border">#<?= esc($auditId) ?></span>
        </h5>
        <p class="text-secondary-light mb-0">Perekaman aktivitas sistem, mutasi snapshot data, dan kepatuhan audit kepemerintahan.</p>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2 no-print">
        <a href="<?= esc($backUrl) ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            <span>Kembali</span>
        </a>

        <?php if (!empty($item['actor_id'])): ?>
            <a href="<?= esc($backUrl . '?userId=' . (int) $item['actor_id']) ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" title="Filter seluruh aktivitas aktor ini">
                <iconify-icon icon="solar:user-outline" class="text-lg"></iconify-icon>
                <span>Log Aktor</span>
            </a>
        <?php endif; ?>

        <?php if (!empty($item['resource_type'])): ?>
            <a href="<?= esc($backUrl . '?module=' . urlencode((string) $item['resource_type'])) ?>" class="btn btn-outline-info-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" title="Filter seluruh aktivitas modul ini">
                <iconify-icon icon="solar:folder-outline" class="text-lg"></iconify-icon>
                <span>Log Modul</span>
            </a>
        <?php endif; ?>

        <button type="button" onclick="window.print()" class="btn btn-neutral-100 text-neutral-800 hover-bg-neutral-200 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2 border" title="Cetak lembar kepatuhan audit ini">
            <iconify-icon icon="solar:printer-outline" class="text-lg"></iconify-icon>
            <span>Cetak Log</span>
        </button>
    </div>
</div>

<!-- KPI Summary Highlights -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <!-- Status Hasil -->
    <div class="col">
        <div class="card p-20 radius-12 border <?= $resultConfig['card_bg'] ?> shadow-none h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary-light text-xs fw-semibold text-uppercase tracking-wider">Hasil Eksekusi</span>
                <iconify-icon icon="<?= $resultConfig['icon'] ?>" class="text-2xl <?= $resultConfig['icon_color'] ?>"></iconify-icon>
            </div>
            <h6 class="fw-bold mb-1 <?= $resultConfig['icon_color'] ?>"><?= esc($resultConfig['label']) ?></h6>
            <span class="text-2xs text-secondary-light d-block"><?= esc($resultConfig['desc']) ?></span>
        </div>
    </div>

    <!-- Waktu Kejadian -->
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary-light text-xs fw-semibold text-uppercase tracking-wider">Waktu Transaksi</span>
                <iconify-icon icon="solar:calendar-date-outline" class="text-2xl text-primary-600"></iconify-icon>
            </div>
            <h6 class="fw-bold mb-1 text-neutral-900"><?= esc(date('d M Y, H:i:s', strtotime($item['created_at'] ?? 'now'))) ?> WIB</h6>
            <span class="text-2xs text-secondary-light d-block"><?= esc($timeAgo($item['created_at'] ?? '')) ?></span>
        </div>
    </div>

    <!-- Tindakan / Action -->
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary-light text-xs fw-semibold text-uppercase tracking-wider">Tindakan (Action)</span>
                <iconify-icon icon="solar:bolt-circle-outline" class="text-2xl text-warning-main"></iconify-icon>
            </div>
            <div class="mb-1">
                <span class="badge bg-neutral-100 text-neutral-900 radius-6 px-8 py-3 font-monospace text-xs border">
                    <?= esc($item['action'] ?? '-') ?>
                </span>
            </div>
            <span class="text-2xs text-secondary-light d-block"><?= esc($humanAction) ?></span>
        </div>
    </div>

    <!-- Modul / Target Resource -->
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary-light text-xs fw-semibold text-uppercase tracking-wider">Target Entitas</span>
                <iconify-icon icon="solar:shield-check-outline" class="text-2xl text-info-600"></iconify-icon>
            </div>
            <h6 class="fw-bold mb-1 text-neutral-900 d-flex align-items-center gap-1">
                <span><?= esc(ucfirst(str_replace('_', ' ', (string) ($item['resource_type'] ?? 'Sistem')))) ?></span>
                <?php if (!empty($item['resource_id'])): ?>
                    <span class="text-secondary-light text-sm fw-normal">#<?= esc($item['resource_id']) ?></span>
                <?php endif; ?>
            </h6>
            <?php if (!empty($resourceLink)): ?>
                <a href="<?= esc($resourceLink) ?>" class="text-2xs text-primary-600 hover-text-primary-800 d-inline-flex align-items-center gap-1">
                    <span>Buka Entitas Terkait</span>
                    <iconify-icon icon="solar:arrow-right-up-outline"></iconify-icon>
                </a>
            <?php else: ?>
                <span class="text-2xs text-secondary-light d-block">Lingkup Operasi Modul</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Detail Content Layout -->
<div class="row g-4">
    <!-- Left Column (8 cols): Snapshot, Diff Viewer, Security Context -->
    <div class="col-xl-8 col-lg-7">
        <!-- Visual Diff & Snapshot Card -->
        <div class="card radius-12 border mb-24">
            <div class="card-header border-bottom bg-base p-20 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h6 class="text-md fw-semibold mb-1">Perubahan Data &amp; Snapshot Mutasi</h6>
                    <p class="text-secondary-light text-xs mb-0">Inspeksi perbandingan nilai sebelum vs sesudah operasi sistem dijalankan.</p>
                </div>
                <?php if (!empty($diffEntries)): ?>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input cursor-pointer" type="checkbox" id="toggleUnchangedFields">
                        <label class="form-check-label text-xs fw-medium cursor-pointer text-secondary-light" for="toggleUnchangedFields">Sembunyikan Field yang Tidak Berubah</label>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card-body p-20">
                <!-- Nav Tabs for Diff, After, Before, and Reason -->
                <ul class="nav nav-tabs nav-tabs-bordered border-bottom gap-2 mb-20" id="auditDetailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-16 py-8 text-sm fw-medium radius-8 d-inline-flex align-items-center gap-2" id="tab-diff" data-bs-toggle="tab" data-bs-target="#pane-diff" type="button" role="tab" aria-selected="true">
                            <iconify-icon icon="solar:diff-outline" class="text-base"></iconify-icon>
                            <span>Perbandingan Visual (Diff)</span>
                            <?php if (!empty($diffEntries)): ?>
                                <span class="badge bg-primary-50 text-primary-600 radius-4 px-6 py-2 text-2xs"><?= count($diffEntries) ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-16 py-8 text-sm fw-medium radius-8 d-inline-flex align-items-center gap-2" id="tab-after" data-bs-toggle="tab" data-bs-target="#pane-after" type="button" role="tab" aria-selected="false">
                            <iconify-icon icon="solar:document-text-outline" class="text-base text-success-600"></iconify-icon>
                            <span>Snapshot Sesudah (After)</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-16 py-8 text-sm fw-medium radius-8 d-inline-flex align-items-center gap-2" id="tab-before" data-bs-toggle="tab" data-bs-target="#pane-before" type="button" role="tab" aria-selected="false">
                            <iconify-icon icon="solar:history-outline" class="text-base text-warning-600"></iconify-icon>
                            <span>Snapshot Sebelum (Before)</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-16 py-8 text-sm fw-medium radius-8 d-inline-flex align-items-center gap-2" id="tab-reason" data-bs-toggle="tab" data-bs-target="#pane-reason" type="button" role="tab" aria-selected="false">
                            <iconify-icon icon="solar:chat-line-outline" class="text-base text-info-600"></iconify-icon>
                            <span>Metadata &amp; Alasan</span>
                        </button>
                    </li>
                </ul>

                <!-- Tab Panes Content -->
                <div class="tab-content" id="auditDetailTabContent">
                    <!-- Pane 1: Visual Diff Table -->
                    <div class="tab-pane fade show active" id="pane-diff" role="tabpanel" aria-labelledby="tab-diff">
                        <?php if (empty($diffEntries)): ?>
                            <div class="p-24 bg-neutral-50 radius-8 border text-center my-12">
                                <iconify-icon icon="solar:shield-check-outline" class="text-4xl text-neutral-400 mb-2"></iconify-icon>
                                <h6 class="text-sm fw-semibold text-neutral-800 mb-1">Aktivitas Non-Mutasi Data (Audit Log Sesi)</h6>
                                <p class="text-secondary-light text-xs mb-0">Log ini mencatat aktivitas sistem (misal autentikasi login/logout atau penarikan data) tanpa melakukan mutasi atribut baris basis data secara langsung.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive scroll-sm">
                                <table class="table bordered-table sm-table mb-0 align-middle" id="tableDiffVisual">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">Field / Properti</th>
                                            <th style="width: 32%;">Nilai Sebelum (Before)</th>
                                            <th style="width: 33%;">Nilai Sesudah (After)</th>
                                            <th class="text-center" style="width: 10%;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($diffEntries as $diff): ?>
                                            <?php
                                            $st = $diff['status'];
                                            $rowClass = ($st === 'unchanged') ? 'row-unchanged' : 'row-changed';
                                            $stBadge = match ($st) {
                                                'added'     => '<span class="badge bg-success-50 text-success-600 radius-6 px-8 py-3 text-2xs fw-medium">+ Ditambahkan</span>',
                                                'removed'   => '<span class="badge bg-danger-50 text-danger-600 radius-6 px-8 py-3 text-2xs fw-medium">- Dihapus</span>',
                                                'modified'  => '<span class="badge bg-warning-50 text-warning-main radius-6 px-8 py-3 text-2xs fw-medium">~ Diubah</span>',
                                                'unchanged' => '<span class="badge bg-neutral-100 text-secondary-light radius-6 px-8 py-3 text-2xs fw-medium">= Sama</span>',
                                            };
                                            ?>
                                            <tr class="<?= esc($rowClass) ?>">
                                                <td>
                                                    <span class="font-monospace text-xs fw-semibold text-neutral-900"><?= esc($diff['key']) ?></span>
                                                </td>
                                                <td>
                                                    <div class="p-8 radius-6 <?= ($st === 'modified' || $st === 'removed') ? 'bg-danger-50 text-danger-main border border-danger-200 text-decoration-line-through' : 'bg-neutral-50 text-neutral-700' ?>">
                                                        <?= $formatVal($diff['before']) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="p-8 radius-6 <?= ($st === 'modified' || $st === 'added') ? 'bg-success-50 text-success-main border border-success-200 fw-semibold' : 'bg-neutral-50 text-neutral-700' ?>">
                                                        <?= $formatVal($diff['after']) ?>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?= $stBadge ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pane 2: After Snapshot JSON -->
                    <div class="tab-pane fade" id="pane-after" role="tabpanel" aria-labelledby="tab-after">
                        <div class="position-relative">
                            <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-base position-absolute top-0 end-0 m-12 radius-6 px-10 py-4 d-inline-flex align-items-center gap-1 z-1" onclick="copyJson('jsonAfterBlock', this)">
                                <iconify-icon icon="solar:copy-outline" class="text-base"></iconify-icon>
                                <span>Salin JSON</span>
                            </button>
                            <pre id="jsonAfterBlock" class="bg-neutral-900 text-neutral-100 p-20 radius-8 text-xs font-monospace mb-0 scroll-sm" style="max-height: 480px; overflow-y: auto;"><?= esc(!empty($afterData) ? json_encode($afterData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '// Tidak ada data snapshot sesudah (after).') ?></pre>
                        </div>
                    </div>

                    <!-- Pane 3: Before Snapshot JSON -->
                    <div class="tab-pane fade" id="pane-before" role="tabpanel" aria-labelledby="tab-before">
                        <div class="position-relative">
                            <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-base position-absolute top-0 end-0 m-12 radius-6 px-10 py-4 d-inline-flex align-items-center gap-1 z-1" onclick="copyJson('jsonBeforeBlock', this)">
                                <iconify-icon icon="solar:copy-outline" class="text-base"></iconify-icon>
                                <span>Salin JSON</span>
                            </button>
                            <pre id="jsonBeforeBlock" class="bg-neutral-900 text-neutral-100 p-20 radius-8 text-xs font-monospace mb-0 scroll-sm" style="max-height: 480px; overflow-y: auto;"><?= esc(!empty($beforeData) ? json_encode($beforeData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '// Tidak ada data snapshot sebelum (before) - operasi entitas baru.') ?></pre>
                        </div>
                    </div>

                    <!-- Pane 4: Reason & Metadata JSON -->
                    <div class="tab-pane fade" id="pane-reason" role="tabpanel" aria-labelledby="tab-reason">
                        <div class="position-relative">
                            <button type="button" class="btn btn-sm btn-outline-neutral-300 bg-base position-absolute top-0 end-0 m-12 radius-6 px-10 py-4 d-inline-flex align-items-center gap-1 z-1" onclick="copyJson('jsonReasonBlock', this)">
                                <iconify-icon icon="solar:copy-outline" class="text-base"></iconify-icon>
                                <span>Salin JSON</span>
                            </button>
                            <pre id="jsonReasonBlock" class="bg-neutral-900 text-neutral-100 p-20 radius-8 text-xs font-monospace mb-0 scroll-sm" style="max-height: 480px; overflow-y: auto;"><?= esc(!empty($reasonData) ? json_encode($reasonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (!empty($item['reason']) ? $item['reason'] : '// Tidak ada alasan atau metadata payload tambahan.')) ?></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Context & Request Footprint Card -->
        <div class="card radius-12 border mb-24">
            <div class="card-header border-bottom bg-base p-20">
                <h6 class="text-md fw-semibold mb-1 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:shield-check-outline" class="text-xl text-primary-600"></iconify-icon>
                    <span>Jejak Keamanan &amp; Konteks Request (Security Audit Context)</span>
                </h6>
                <p class="text-secondary-light text-xs mb-0">Atribut verifikasi integritas, kriptografi anonimitas, dan penelusuran forensik digital.</p>
            </div>
            <div class="card-body p-20">
                <div class="row g-3">
                    <!-- IP Hash -->
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border bg-neutral-50 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-xs fw-semibold text-secondary-light">Hash Alamat IP (SHA-256)</span>
                                <span class="badge bg-neutral-200 text-neutral-800 radius-4 text-2xs">Privasi / UU PDP</span>
                            </div>
                            <p class="font-monospace text-xs text-neutral-900 text-break mb-1"><?= esc($item['ip_hash'] ?? 'unknown / tidak terekam') ?></p>
                            <span class="text-2xs text-secondary-light d-block">Dihash secara satu arah demi pematuhan regulasi privasi data pribadi.</span>
                        </div>
                    </div>

                    <!-- User Agent Hash -->
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border bg-neutral-50 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-xs fw-semibold text-secondary-light">Fingerprint Perangkat Klien</span>
                                <span class="badge bg-neutral-200 text-neutral-800 radius-4 text-2xs">Client Agent</span>
                            </div>
                            <p class="font-monospace text-xs text-neutral-900 text-break mb-1"><?= esc($item['user_agent_hash'] ?? 'unknown / tidak terekam') ?></p>
                            <span class="text-2xs text-secondary-light d-block">Identifikasi unik profil browser/klien saat permintaan dikirim.</span>
                        </div>
                    </div>

                    <!-- Request Correlation ID -->
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border bg-neutral-50 h-100">
                            <span class="text-xs fw-semibold text-secondary-light d-block mb-1">Request Correlation ID</span>
                            <p class="font-monospace text-xs text-neutral-900 text-break mb-1">
                                <?= esc(!empty($item['request_id']) ? $item['request_id'] : ('REQ-' . strtoupper(substr(md5((string) $item['id']), 0, 16)))) ?>
                            </p>
                            <span class="text-2xs text-secondary-light d-block">ID korelasi untuk tracing log di server backend &amp; reverse-proxy.</span>
                        </div>
                    </div>

                    <!-- Immutability Status -->
                    <div class="col-md-6">
                        <div class="p-16 radius-8 border bg-neutral-50 h-100">
                            <span class="text-xs fw-semibold text-secondary-light d-block mb-1">Integritas Audit Log</span>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-success-50 text-success-main radius-4 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:lock-bold"></iconify-icon>
                                    <span>Immutable &amp; Append-Only</span>
                                </span>
                            </div>
                            <span class="text-2xs text-secondary-light d-block">Log tidak dapat diubah (tamper-proof) sesuai standar ISO/IEC 27001.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column (4 cols): Actor Card, Target Entity Card, Related Audit History -->
    <div class="col-xl-4 col-lg-5">
        <!-- Actor Card -->
        <div class="card radius-12 border mb-24">
            <div class="card-header border-bottom bg-base p-20">
                <h6 class="text-md fw-semibold mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:user-circle-outline" class="text-xl text-primary-600"></iconify-icon>
                    <span>Pelaksana / Aktor Aktivitas</span>
                </h6>
            </div>
            <div class="card-body p-20">
                <div class="d-flex align-items-center gap-3 mb-16">
                    <span class="w-48-px h-48-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 fw-bold text-lg border border-primary-200">
                        <?= esc(strtoupper(substr(!empty($item['actor_name']) ? $item['actor_name'] : ($item['username'] ?? 'S'), 0, 1))) ?>
                    </span>
                    <div class="overflow-hidden">
                        <h6 class="text-sm fw-semibold mb-0 text-neutral-900 text-truncate">
                            <?= esc(!empty($item['actor_name']) ? $item['actor_name'] : ($item['username'] ?? 'Sistem / Anonim')) ?>
                        </h6>
                        <span class="text-xs text-secondary-light d-block text-truncate"><?= esc($item['actor_email'] ?? 'Aktivitas Layanan Otomatis') ?></span>
                    </div>
                </div>

                <ul class="list-group list-group-flush border-top border-bottom text-xs mb-16">
                    <li class="list-group-item px-0 py-8 d-flex justify-content-between align-items-center bg-transparent">
                        <span class="text-secondary-light">Actor ID</span>
                        <span class="fw-semibold font-monospace">#<?= esc($item['actor_id'] ?? '0 (Sistem)') ?></span>
                    </li>
                    <li class="list-group-item px-0 py-8 d-flex justify-content-between align-items-center bg-transparent">
                        <span class="text-secondary-light">Username</span>
                        <span class="fw-semibold">@<?= esc($item['username'] ?? 'sistem') ?></span>
                    </li>
                    <li class="list-group-item px-0 py-8 d-flex justify-content-between align-items-center bg-transparent">
                        <span class="text-secondary-light">Tipe Profil</span>
                        <span class="badge bg-neutral-100 text-neutral-800 radius-4">
                            <?= esc(strtoupper((string) ($item['actor_profile_type'] ?? 'SYSTEM'))) ?>
                        </span>
                    </li>
                    <li class="list-group-item px-0 py-8 d-flex justify-content-between align-items-center bg-transparent">
                        <span class="text-secondary-light">Status Akun</span>
                        <span class="badge <?= ($item['actor_status'] ?? '') === 'active' ? 'bg-success-50 text-success-600' : 'bg-neutral-100 text-neutral-600' ?>">
                            <?= esc(ucfirst((string) ($item['actor_status'] ?? 'Terdaftar'))) ?>
                        </span>
                    </li>
                </ul>

                <?php if (!empty($actorRoles)): ?>
                    <div class="mb-16">
                        <span class="text-2xs text-secondary-light text-uppercase fw-semibold mb-2 d-block">Peran / Hak Akses Aktor</span>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($actorRoles as $role): ?>
                                <span class="badge bg-primary-50 text-primary-600 radius-6 px-8 py-4 text-2xs fw-medium border border-primary-200">
                                    <?= esc($role['name'] ?? '-') ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($item['actor_id'])): ?>
                    <div class="d-grid gap-2">
                        <a href="<?= site_url('dashboard/akses/pengguna/' . (int) $item['actor_id']) ?>" class="btn btn-outline-primary-600 btn-sm radius-8 d-flex align-items-center justify-content-center gap-1">
                            <iconify-icon icon="solar:user-id-outline" class="text-base"></iconify-icon>
                            <span>Buka Profil Pengguna</span>
                        </a>
                        <a href="<?= esc($backUrl . '?userId=' . (int) $item['actor_id']) ?>" class="btn btn-neutral-100 text-neutral-800 hover-bg-neutral-200 btn-sm radius-8 border d-flex align-items-center justify-content-center gap-1">
                            <iconify-icon icon="solar:history-outline" class="text-base"></iconify-icon>
                            <span>Semua Log Aktor Ini</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Target Resource Card -->
        <div class="card radius-12 border mb-24">
            <div class="card-header border-bottom bg-base p-20">
                <h6 class="text-md fw-semibold mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:folder-with-files-outline" class="text-xl text-info-600"></iconify-icon>
                    <span>Entitas Resource Sasaran</span>
                </h6>
            </div>
            <div class="card-body p-20">
                <div class="mb-12">
                    <span class="text-secondary-light text-xs d-block mb-1">Jenis Modul / Resource</span>
                    <h6 class="text-sm fw-bold text-neutral-900 mb-0"><?= esc(ucfirst(str_replace('_', ' ', (string) ($item['resource_type'] ?? '-')))) ?></h6>
                </div>
                <div class="mb-16">
                    <span class="text-secondary-light text-xs d-block mb-1">ID Entitas Spesifik</span>
                    <span class="font-monospace text-sm fw-semibold text-primary-600">#<?= esc($item['resource_id'] ?? '-') ?></span>
                </div>

                <?php if (!empty($resourceLink)): ?>
                    <a href="<?= esc($resourceLink) ?>" class="btn btn-primary-600 btn-sm w-100 radius-8 d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:arrow-right-up-outline" class="text-base"></iconify-icon>
                        <span>Buka Halaman Entitas</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Related Audit Timeline Card -->
        <div class="card radius-12 border">
            <div class="card-header border-bottom bg-base p-20 d-flex align-items-center justify-content-between">
                <h6 class="text-md fw-semibold mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:clock-circle-outline" class="text-xl text-warning-main"></iconify-icon>
                    <span>Riwayat Terkait</span>
                </h6>
                <span class="badge bg-neutral-100 text-neutral-800 radius-4 text-2xs"><?= count($relatedLogs) ?> Log</span>
            </div>
            <div class="card-body p-20">
                <?php if (empty($relatedLogs)): ?>
                    <p class="text-secondary-light text-xs mb-0 text-center py-12">Tidak ada log aktivitas terkait lainnya.</p>
                <?php else: ?>
                    <div class="timeline position-relative ps-16 border-start border-2 border-neutral-200">
                        <?php foreach ($relatedLogs as $rel): ?>
                            <div class="timeline-item mb-16 position-relative">
                                <span class="position-absolute top-0 start-0 translate-middle-x w-8-px h-8-px rounded-circle <?= ($rel['result'] ?? '') === 'success' ? 'bg-success-600' : 'bg-danger-600' ?>" style="margin-left: -17px; margin-top: 6px;"></span>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-neutral-100 text-neutral-900 radius-4 px-6 py-2 font-monospace text-2xs">
                                        <?= esc($rel['action'] ?? '-') ?>
                                    </span>
                                    <span class="text-2xs text-secondary-light"><?= esc(date('d M, H:i', strtotime($rel['created_at'] ?? 'now'))) ?></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-xs text-neutral-700 text-truncate" style="max-width: 140px;">
                                        <?= esc(!empty($rel['actor_name']) ? $rel['actor_name'] : ($rel['username'] ?? 'Sistem')) ?>
                                    </span>
                                    <a href="<?= site_url(($isSystemRoute ? 'dashboard/sistem/audit-trail/' : 'dashboard/akses/audit/') . (int) $rel['id']) ?>" class="text-2xs text-primary-600 hover-text-primary-800 fw-medium">
                                        Detail &rarr;
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($item['resource_type'])): ?>
                    <div class="mt-16 pt-16 border-top">
                        <a href="<?= esc($backUrl . '?module=' . urlencode((string) $item['resource_type'])) ?>" class="btn btn-outline-neutral-300 btn-sm w-100 radius-8 text-xs d-flex align-items-center justify-content-center gap-1">
                            <iconify-icon icon="solar:list-outline"></iconify-icon>
                            <span>Semua Log Modul <?= esc(ucfirst(str_replace('_', ' ', (string) $item['resource_type']))) ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .dashboard-main {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .dashboard-header,
    .dashboard-sidebar,
    .dashboard-footer,
    .no-print {
        display: none !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
    .print-header {
        margin-bottom: 16px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Toggle filter untuk field yang tidak berubah pada tabel diff
    var toggleUnchanged = document.getElementById('toggleUnchangedFields');
    if (toggleUnchanged) {
        toggleUnchanged.addEventListener('change', function () {
            var unchangedRows = document.querySelectorAll('#tableDiffVisual tbody tr.row-unchanged');
            var isHidden = this.checked;
            unchangedRows.forEach(function (row) {
                row.style.display = isHidden ? 'none' : '';
            });
        });
    }
});

// 2. Fungsi Salin JSON ke Clipboard dengan Tooltip Feedback
function copyJson(elementId, btnElement) {
    var block = document.getElementById(elementId);
    if (!block) return;
    var text = block.innerText || block.textContent;

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
            showCopyFeedback(btnElement, 'Tersalin!');
        }).catch(function () {
            fallbackCopy(text, btnElement);
        });
    } else {
        fallbackCopy(text, btnElement);
    }
}

function fallbackCopy(text, btnElement) {
    var textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        showCopyFeedback(btnElement, 'Tersalin!');
    } catch (err) {
        showCopyFeedback(btnElement, 'Gagal');
    }
    document.body.removeChild(textArea);
}

function showCopyFeedback(btnElement, label) {
    if (!btnElement) return;
    var originalHtml = btnElement.innerHTML;
    btnElement.innerHTML = '<iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-base"></iconify-icon><span>' + label + '</span>';
    btnElement.classList.add('border-success-600');
    setTimeout(function () {
        btnElement.innerHTML = originalHtml;
        btnElement.classList.remove('border-success-600');
    }, 2000);
}
</script>
<?= $this->endSection() ?>

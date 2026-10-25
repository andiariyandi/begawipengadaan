<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$baseUrl = $baseUrl ?? site_url('dashboard/sistem/notifikasi');
$currentSort = $filters['sort'] ?? 'created_at';
$currentDir  = strtolower($filters['dir'] ?? 'desc');

$buildSortLink = function (string $colKey, string $label, string $alignClass = '') use ($filters, $currentSort, $currentDir, $baseUrl) {
    $isActive    = ($currentSort === $colKey);
    $nextDir     = ($isActive && $currentDir === 'asc') ? 'desc' : 'asc';
    $params      = array_merge($filters, [
        'sort'               => $colKey,
        'dir'                => $nextDir,
        'page_notifications' => 1,
        'page_notif'         => 1,
    ]);
    
    // Clean empty values
    $query = http_build_query(array_filter($params, static fn($v) => $v !== null && $v !== '' && $v !== 'all'));
    $url   = $baseUrl . ($query !== '' ? '?' . $query : '');

    $colorClass = $isActive ? 'text-primary-600 fw-bold' : 'text-secondary-light hover-text-primary-600';
    $iconName   = $isActive 
        ? ($currentDir === 'asc' ? 'solar:sort-from-bottom-to-top-outline' : 'solar:sort-from-top-to-bottom-outline')
        : 'solar:sort-vertical-outline';
    $title      = 'Urutkan ' . $label . ' (' . ($isActive ? ($currentDir === 'asc' ? 'Z-A / Terbaru' : 'A-Z / Terlama') : 'Klik untuk mengurutkan') . ')';

    return '<a href="' . esc($url) . '" class="' . $colorClass . ' d-inline-flex align-items-center gap-1 ' . $alignClass . '" title="' . esc($title) . '">'
        . '<span>' . esc($label) . '</span>'
        . '<iconify-icon icon="' . $iconName . '" class="' . ($isActive ? 'text-sm' : 'text-xs') . '"></iconify-icon>'
        . '</a>';
};
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Log Notifikasi Sistem') ?></h6>
        <p class="text-secondary-light mb-0">Riwayat pengiriman, audit status keterbacaan, ekspor data, dan manajemen siaran notifikasi sistem.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- Tombol Ekspor CSV (Layaknya Audit Trail Log) -->
        <a href="<?= esc($exportUrl) ?>" class="btn btn-outline-success-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" title="Unduh data notifikasi sesuai kriteria filter saat ini">
            <iconify-icon icon="solar:file-download-outline" class="text-xl"></iconify-icon>
            <span>Ekspor CSV</span>
        </a>

        <!-- Tandai Semua Dibaca -->
        <form action="<?= site_url('dashboard/sistem/notifikasi/baca-semua') ?>" method="post" class="d-inline" onsubmit="return confirm('Tandai seluruh notifikasi sistem sebagai telah dibaca?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-neutral-400 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2">
                <iconify-icon icon="solar:check-read-outline" class="text-xl"></iconify-icon>
                <span>Tandai Semua Dibaca</span>
            </button>
        </form>

        <!-- Kirim Siaran Notifikasi -->
        <button type="button" class="btn btn-primary-600 radius-8 px-20 py-10 d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#broadcastModal">
            <iconify-icon icon="solar:letter-unread-bold" class="text-xl"></iconify-icon>
            <span>+ Kirim Siaran Notifikasi</span>
        </button>
    </div>
</div>

<!-- KPI Metric Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between mb-8">
                    <p class="fw-medium text-primary-light mb-0">Total Notifikasi</p>
                    <span class="w-36-px h-36-px bg-primary-100 text-primary-600 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:bell-linear" class="text-xl"></iconify-icon>
                    </span>
                </div>
                <h5 class="mb-0 fw-bold"><?= number_format($stats['total'] ?? 0, 0, ',', '.') ?></h5>
                <p class="text-xs text-primary-light mt-8 mb-0">Tercatat di seluruh kanal komunikasi</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between mb-8">
                    <p class="fw-medium text-primary-light mb-0">Belum Dibaca</p>
                    <span class="w-36-px h-36-px bg-warning-100 text-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-xl"></iconify-icon>
                    </span>
                </div>
                <h5 class="mb-0 fw-bold"><?= number_format($stats['unread'] ?? 0, 0, ',', '.') ?></h5>
                <p class="text-xs text-primary-light mt-8 mb-0">Menunggu dibuka oleh pengguna</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between mb-8">
                    <p class="fw-medium text-primary-light mb-0">Hari Ini</p>
                    <span class="w-36-px h-36-px bg-success-100 text-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:calendar-date-outline" class="text-xl"></iconify-icon>
                    </span>
                </div>
                <h5 class="mb-0 fw-bold"><?= number_format($stats['today'] ?? 0, 0, ',', '.') ?></h5>
                <p class="text-xs text-primary-light mt-8 mb-0">Terkirim dalam 24 jam terakhir</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between mb-8">
                    <p class="fw-medium text-primary-light mb-0">Siaran / Broadcast</p>
                    <span class="w-36-px h-36-px bg-info-100 text-info-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:megaphone-outline" class="text-xl"></iconify-icon>
                    </span>
                </div>
                <h5 class="mb-0 fw-bold"><?= number_format($stats['broadcast'] ?? 0, 0, ',', '.') ?></h5>
                <p class="text-xs text-primary-light mt-8 mb-0">Pengumuman & informasi massal</p>
            </div>
        </div>
    </div>
</div>

<!-- Native Browser Desktop & Push Notification Control Center Card -->
<div class="card radius-12 mb-24 border shadow-none" id="dashPushNotificationCard">
    <div class="card-body p-20">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-start gap-3">
                <div class="w-48-px h-48-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                    <iconify-icon icon="solar:laptop-minimalistic-bold" class="text-2xl" id="dashPushIcon"></iconify-icon>
                </div>
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h6 class="fw-semibold mb-0 text-primary-light">Notifikasi Desktop Browser &amp; Service Worker</h6>
                        <span id="dashPushStatusBadge" class="badge bg-neutral-200 text-neutral-700 px-8 py-3 radius-4 text-xs fw-semibold">
                            <iconify-icon icon="eos-icons:loading" class="me-1 align-middle"></iconify-icon> Memeriksa Status...
                        </span>
                    </div>
                    <p class="text-secondary-light text-sm mb-0" id="dashPushStatusDesc">
                        Dapatkan peringatan seketika saat ada pendaftaran baru, verifikasi berkas peserta, tanggapan tiket konsultasi, atau aktivitas penting sistem langsung di desktop administrator.
                    </p>
                    <div id="dashPushInsecureNotice" class="text-xs text-secondary-light mt-1 d-none">
                        <iconify-icon icon="solar:info-circle-outline" class="text-info align-middle me-1"></iconify-icon> <span id="dashPushInsecureText">Browser mendeteksi domain lokal HTTP. Mode <strong>In-App Floating Toast &amp; Audio Chime</strong> aktif seketika. Notifikasi native desktop browser akan aktif optimal pada koneksi HTTPS atau <code>http://localhost</code>.</span>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0 align-self-start align-self-md-center">
                <button type="button" id="dashBtnActivatePush" class="btn btn-primary-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" onclick="window.dashActivatePushNotification()">
                    <iconify-icon icon="solar:bell-bing-bold" class="text-xl"></iconify-icon> <span id="dashBtnActivatePushText">Aktifkan Notifikasi Desktop</span>
                </button>
                <button type="button" id="dashBtnTestPush" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" title="Kirim notifikasi uji coba untuk mengetes suara chime dan banner desktop" onclick="window.dashTestPushNotification()">
                    <iconify-icon icon="solar:tuning-square-2-outline" class="text-xl"></iconify-icon> <span>Uji / Tes Notifikasi</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Multi-Facet Filter Toolbar -->
<div class="card radius-12 mb-24 border">
    <div class="card-body p-20">
        <form method="get" action="<?= esc($baseUrl) ?>">
            <!-- Hidden inputs mempertahankan sorting server-side -->
            <input type="hidden" name="sort" value="<?= esc($filters['sort'] ?? 'created_at') ?>">
            <input type="hidden" name="dir" value="<?= esc($filters['dir'] ?? 'desc') ?>">

            <div class="row g-3 align-items-end">
                <!-- Pencarian Cepat -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Pencarian Cepat</label>
                    <div class="position-relative">
                        <input type="text" name="q" value="<?= esc($filters['q'] ?? '') ?>" class="form-control radius-8 ps-36" placeholder="Judul, isi pesan, penerima, email...">
                        <iconify-icon icon="solar:magnifer-outline" class="position-absolute top-50 start-0 translate-middle-y ms-12 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>

                <!-- Kategori Notifikasi -->
                <div class="col-lg-2 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Kategori</label>
                    <select name="type" class="form-select radius-8">
                        <option value="all" <?= ($filters['type'] ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Kategori</option>
                        <option value="broadcast" <?= ($filters['type'] ?? '') === 'broadcast' ? 'selected' : '' ?>>Siaran Pengumuman</option>
                        <option value="account_approved" <?= ($filters['type'] ?? '') === 'account_approved' ? 'selected' : '' ?>>Akun Disetujui</option>
                        <option value="account_rejected" <?= ($filters['type'] ?? '') === 'account_rejected' ? 'selected' : '' ?>>Akun Ditolak</option>
                        <option value="assignment_graded" <?= ($filters['type'] ?? '') === 'assignment_graded' ? 'selected' : '' ?>>Penilaian Tugas</option>
                        <option value="certificate_issued" <?= ($filters['type'] ?? '') === 'certificate_issued' ? 'selected' : '' ?>>Penerbitan Sertifikat</option>
                        <option value="class_enrolled" <?= ($filters['type'] ?? '') === 'class_enrolled' ? 'selected' : '' ?>>Pembelajaran Kelas</option>
                        <option value="class_reminder" <?= ($filters['type'] ?? '') === 'class_reminder' ? 'selected' : '' ?>>Pengingat Kelas</option>
                        <option value="event_info" <?= ($filters['type'] ?? '') === 'event_info' ? 'selected' : '' ?>>Event & Agenda PBJ</option>
                        <option value="regulation_info" <?= ($filters['type'] ?? '') === 'regulation_info' ? 'selected' : '' ?>>Regulasi & Kebijakan</option>
                        <option value="system" <?= ($filters['type'] ?? '') === 'system' ? 'selected' : '' ?>>Sistem Begawi</option>
                    </select>
                </div>

                <!-- Kanal Distribusi -->
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Kanal</label>
                    <select name="channel" class="form-select radius-8">
                        <option value="all" <?= ($filters['channel'] ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Kanal</option>
                        <option value="database" <?= ($filters['channel'] ?? '') === 'database' ? 'selected' : '' ?>>In-App / Database</option>
                        <option value="email" <?= ($filters['channel'] ?? '') === 'email' ? 'selected' : '' ?>>Email & In-App</option>
                    </select>
                </div>

                <!-- Status Keterbacaan -->
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Status Baca</label>
                    <select name="status" class="form-select radius-8">
                        <option value="all" <?= ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="unread" <?= ($filters['status'] ?? '') === 'unread' ? 'selected' : '' ?>>Belum Dibaca</option>
                        <option value="read" <?= ($filters['status'] ?? '') === 'read' ? 'selected' : '' ?>>Telah Dibaca</option>
                    </select>
                </div>

                <!-- Profil Sasaran -->
                <div class="col-lg-2 col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Profil Penerima</label>
                    <select name="profile_type" class="form-select radius-8">
                        <option value="all" <?= ($filters['profile_type'] ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Profil</option>
                        <option value="asn" <?= ($filters['profile_type'] ?? '') === 'asn' ? 'selected' : '' ?>>ASN Pemprov</option>
                        <option value="provider" <?= ($filters['profile_type'] ?? '') === 'provider' ? 'selected' : '' ?>>Penyedia PBJ</option>
                        <option value="academic" <?= ($filters['profile_type'] ?? '') === 'academic' ? 'selected' : '' ?>>Akademisi</option>
                        <option value="public" <?= ($filters['profile_type'] ?? '') === 'public' ? 'selected' : '' ?>>Masyarakat</option>
                    </select>
                </div>

                <!-- Baris Per Halaman -->
                <div class="col-lg-1 col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Baris</label>
                    <select name="per_page" class="form-select radius-8">
                        <?php foreach ([10, 25, 50, 100] as $pp): ?>
                            <option value="<?= $pp ?>" <?= (int) ($filters['per_page'] ?? 25) === $pp ? 'selected' : '' ?>><?= $pp ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Rentang Tanggal -->
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control radius-8" value="<?= esc($filters['from'] ?? '') ?>">
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-sm fw-medium mb-1">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-control radius-8" value="<?= esc($filters['to'] ?? '') ?>">
                </div>

                <!-- Tombol Aksi Form Filter -->
                <div class="col-lg-6 col-md-12 d-flex justify-content-end gap-2 mt-md-0 mt-2">
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

<!-- Main Table Card -->
<div class="card radius-12 border">
    <div class="card-body p-24">
        <?php if (empty($items)): ?>
            <div class="text-center py-48 text-secondary-light">
                <iconify-icon icon="solar:bell-off-outline" class="text-5xl text-neutral-400 mb-12 d-block mx-auto"></iconify-icon>
                <h6 class="text-md fw-semibold mb-4">Tidak Ada Catatan Notifikasi Ditemukan</h6>
                <p class="text-sm text-secondary-light mb-16">Belum ada data notifikasi yang sesuai dengan kriteria penyaringan yang Anda pilih.</p>
                <a href="<?= esc($baseUrl) ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-16 py-8">
                    Reset Filter
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive scroll-sm">
                <!-- Table dengan data-server-side="true" dan class server-side-table dt-server-side agar BegawiDatatable tidak membuat pagination ganda di client -->
                <table class="table bordered-table sm-table mb-0 align-middle server-side-table dt-server-side" 
                       data-server-side="true" 
                       data-table-name="notifications-log" 
                       data-page-length="<?= (int) ($filters['per_page'] ?? 25) ?>">
                    <thead>
                        <tr>
                            <th style="min-width: 140px;"><?= $buildSortLink('created_at', 'Waktu Kirim') ?></th>
                            <th style="min-width: 180px;"><?= $buildSortLink('user_name', 'Penerima') ?></th>
                            <th style="min-width: 250px;"><?= $buildSortLink('title', 'Judul & Pesan') ?></th>
                            <th style="min-width: 130px;"><?= $buildSortLink('type', 'Kategori') ?></th>
                            <th style="min-width: 100px;"><?= $buildSortLink('channel', 'Kanal') ?></th>
                            <th style="min-width: 110px;" class="text-center"><?= $buildSortLink('status', 'Status Baca', 'justify-content-center') ?></th>
                            <th style="min-width: 130px;" class="text-end no-sort no-export action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): 
                            $isRead = !empty($item['read_at']);
                            $catInfo = \App\Services\NotificationService::formatCategory($item['type'] ?? 'system');
                            $timeAgo = \App\Services\NotificationService::timeAgo($item['created_at'] ?? null);
                            $profileLabel = match ($item['user_profile_type'] ?? '') {
                                'asn'      => ['label' => 'ASN', 'badge' => 'bg-primary-50 text-primary-600'],
                                'provider' => ['label' => 'Penyedia', 'badge' => 'bg-warning-50 text-warning-700'],
                                'academic' => ['label' => 'Akademisi', 'badge' => 'bg-info-50 text-info-700'],
                                'public'   => ['label' => 'Masyarakat', 'badge' => 'bg-neutral-100 text-neutral-700'],
                                default    => ['label' => 'User', 'badge' => 'bg-neutral-100 text-neutral-600'],
                            };
                            $detailUrl = site_url('dashboard/sistem/notifikasi/' . $item['id']);
                        ?>
                        <tr class="<?= $isRead ? '' : 'bg-primary-50/20' ?>">
                            <!-- Waktu Kirim -->
                            <td>
                                <span class="text-xs text-neutral-800 d-block fw-medium">
                                    <?= !empty($item['created_at']) ? date('d/m/Y H:i:s', strtotime($item['created_at'])) : '-' ?>
                                </span>
                                <span class="text-xxs text-secondary-light"><?= $timeAgo ?></span>
                            </td>

                            <!-- Penerima -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="w-32-px h-32-px rounded-circle bg-neutral-200 text-neutral-800 d-flex justify-content-center align-items-center fw-bold text-xxs flex-shrink-0">
                                        <?= strtoupper(substr($item['user_name'] ?? ($item['user_username'] ?? 'U'), 0, 2)) ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="text-xs fw-semibold mb-0 text-truncate" style="max-width: 140px;" title="<?= esc($item['user_name'] ?? ($item['user_username'] ?? 'Pengguna #' . $item['user_id'])) ?>">
                                            <?= esc($item['user_name'] ?? ($item['user_username'] ?? 'Pengguna #' . $item['user_id'])) ?>
                                        </h6>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="badge text-3xs px-6 py-1 radius-4 <?= $profileLabel['badge'] ?>"><?= $profileLabel['label'] ?></span>
                                            <span class="text-xxs text-secondary-light text-truncate" style="max-width: 90px;" title="<?= esc($item['user_email'] ?? '') ?>">
                                                <?= esc($item['user_email'] ?? '') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Judul & Pesan -->
                            <td>
                                <div class="d-flex flex-column">
                                    <a href="<?= esc($detailUrl) ?>" class="fw-semibold text-xs text-primary-600 hover-text-primary-800 text-truncate mb-1" style="max-width: 280px;" title="<?= esc($item['title'] ?? '-') ?>">
                                        <?= esc($item['title'] ?? '-') ?>
                                    </a>
                                    <span class="text-xxs text-secondary-light text-truncate" style="max-width: 280px;" title="<?= esc($item['body'] ?? '-') ?>">
                                        <?= esc($item['body'] ?? '-') ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Kategori -->
                            <td>
                                <span class="badge text-xxs px-8 py-3 radius-6 <?= $catInfo['badge'] ?>">
                                    <?= esc($catInfo['label']) ?>
                                </span>
                            </td>

                            <!-- Kanal -->
                            <td>
                                <span class="badge bg-neutral-100 text-neutral-800 border radius-6 text-xxs px-8 py-2">
                                    <?= esc(ucfirst($item['channel'] ?? 'database')) ?>
                                </span>
                            </td>

                            <!-- Status Baca -->
                            <td class="status-col-cell text-center">
                                <?php if ($isRead): ?>
                                    <span class="badge bg-success-focus text-success-main border-success px-8 py-3 radius-6 text-xxs d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                        Dibaca
                                    </span>
                                    <span class="d-block text-3xs text-secondary-light mt-1" title="Waktu dibaca: <?= esc($item['read_at']) ?>">
                                        <?= date('d/m H:i', strtotime($item['read_at'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-focus text-warning-main border-warning px-8 py-3 radius-6 text-xxs d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:unread-outline"></iconify-icon>
                                        Belum
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Aksi -->
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- Direct Detail Page -->
                                    <a href="<?= esc($detailUrl) ?>" 
                                       class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center hover-bg-primary-100 transition-2 border-0" 
                                       title="Buka Halaman Rincian Lengkap (Otomatis Menandai Dibaca)">
                                        <iconify-icon icon="solar:eye-outline" class="text-md"></iconify-icon>
                                    </a>

                                    <!-- Quick View Modal Trigger -->
                                    <button type="button" 
                                            class="w-32-px h-32-px bg-neutral-100 text-neutral-700 rounded-circle d-flex justify-content-center align-items-center hover-bg-neutral-200 transition-2 border-0 btn-show-detail" 
                                            title="Tinjau Cepat (Modal)"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailNotificationModal"
                                            data-id="<?= esc($item['id']) ?>"
                                            data-detail-url="<?= esc($detailUrl) ?>"
                                            data-title="<?= esc($item['title'] ?? '') ?>"
                                            data-body="<?= esc($item['body'] ?? '') ?>"
                                            data-recipient="<?= esc($item['user_name'] ?? ($item['user_username'] ?? 'User #' . $item['user_id'])) ?>"
                                            data-email="<?= esc($item['user_email'] ?? '-') ?>"
                                            data-profile="<?= esc($profileLabel['label']) ?>"
                                            data-type="<?= esc($catInfo['label']) ?>"
                                            data-channel="<?= esc(ucfirst($item['channel'] ?? 'database')) ?>"
                                            data-time="<?= !empty($item['created_at']) ? date('d F Y H:i:s', strtotime($item['created_at'])) : '-' ?>"
                                            data-read="<?= $isRead ? date('d F Y H:i:s', strtotime($item['read_at'])) : 'Belum dibaca' ?>"
                                            data-is-unread="<?= $isRead ? '0' : '1' ?>"
                                            data-json="<?= esc($item['data'] ?? '') ?>">
                                        <iconify-icon icon="solar:magnifer-linear" class="text-md"></iconify-icon>
                                    </button>

                                    <!-- Mark as Read Form (if unread) -->
                                    <?php if (!$isRead): ?>
                                    <form action="<?= site_url('dashboard/sistem/notifikasi/' . $item['id'] . '/baca') ?>" method="post" class="form-mark-read d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="w-32-px h-32-px bg-success-50 text-success-600 rounded-circle d-flex justify-content-center align-items-center hover-bg-success-100 transition-2 border-0" title="Tandai Sudah Dibaca">
                                            <iconify-icon icon="solar:check-read-outline" class="text-md"></iconify-icon>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- Delete Form -->
                                    <form action="<?= site_url('dashboard/sistem/notifikasi/' . $item['id'] . '/hapus') ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan notifikasi ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="w-32-px h-32-px bg-danger-50 text-danger-600 rounded-circle d-flex justify-content-center align-items-center hover-bg-danger-100 transition-2 border-0" title="Hapus Notifikasi">
                                            <iconify-icon icon="solar:trash-bin-trash-outline" class="text-md"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Server-Side Single Pagination Bar -->
            <?php
            $currentPage = (int) ($page ?? 1);
            $perPageNum  = (int) ($perPage ?? 25);
            $startRecord = $total > 0 ? (($currentPage - 1) * $perPageNum) + 1 : 0;
            $endRecord   = min($total, $currentPage * $perPageNum);
            ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-24">
                <span class="text-sm text-secondary-light">
                    Menampilkan <span class="fw-semibold text-neutral-800"><?= number_format($startRecord) ?></span> - <span class="fw-semibold text-neutral-800"><?= number_format($endRecord) ?></span> dari <span class="fw-semibold text-neutral-800"><?= number_format($total ?? 0, 0, ',', '.') ?></span> notifikasi
                </span>
                <div class="pagination-container">
                    <?= $pager->links('notifications', 'default_full') ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Broadcast Notifikasi -->
<div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="broadcastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 border-0 shadow">
            <div class="modal-header border-bottom border-neutral-200 py-16 px-24">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-40-px h-40-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                        <iconify-icon icon="solar:megaphone-bold" class="text-2xl"></iconify-icon>
                    </span>
                    <div>
                        <h6 class="modal-title fw-semibold text-lg mb-0" id="broadcastModalLabel">Kirim Siaran Notifikasi</h6>
                        <p class="text-xs text-secondary-light mb-0">Kirimkan pemberitahuan massal atau terarah secara persisten ke akun pengguna.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('dashboard/sistem/notifikasi/kirim') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="row gy-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="target_group">
                                Sasaran Penerima <span class="text-danger-600">*</span>
                            </label>
                            <select name="target_group" id="target_group" class="form-select radius-8" required onchange="toggleUserSelect(this.value)">
                                <option value="all">Semua Pengguna Aktif (Siaran Menyeluruh)</option>
                                <option value="asn">Khusus ASN Pemprov Lampung</option>
                                <option value="provider">Khusus Rekanan / Penyedia PBJ</option>
                                <option value="academic">Khusus Akademisi & Perguruan Tinggi</option>
                                <option value="public">Khusus Masyarakat Umum</option>
                                <option value="user">Pengguna Tertentu (Satu Orang)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="type">
                                Kategori Notifikasi <span class="text-danger-600">*</span>
                            </label>
                            <select name="type" id="type" class="form-select radius-8" required>
                                <option value="broadcast">Siaran Pengumuman Umum</option>
                                <option value="system">Pemberitahuan Sistem</option>
                                <option value="class_reminder">Pengingat Pembelajaran / Kelas</option>
                                <option value="event_info">Informasi Event & Agenda PBJ</option>
                                <option value="regulation_info">Sosialisasi Regulasi & Kebijakan</option>
                            </select>
                        </div>

                        <!-- User Select (conditional) -->
                        <div class="col-12 d-none" id="userSelectWrapper">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="target_user_id">
                                Pilih Pengguna Spesifik <span class="text-danger-600">*</span>
                            </label>
                            <select name="target_user_id" id="target_user_id" class="form-select radius-8">
                                <option value="">-- Pilih Pengguna Terdaftar --</option>
                                <?php if (!empty($targetUsers)): ?>
                                    <?php foreach ($targetUsers as $u): ?>
                                        <option value="<?= esc($u['id']) ?>">
                                            <?= esc($u['name']) ?> (<?= esc($u['email']) ?>) - <?= strtoupper($u['profile_type'] ?? 'USER') ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="title">
                                Judul Siaran <span class="text-danger-600">*</span>
                            </label>
                            <input type="text" name="title" id="title" class="form-control radius-8" placeholder="Contoh: Pemeliharaan Server PBJ Terjadwal" required maxlength="255">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="body">
                                Isi Pesan Notifikasi <span class="text-danger-600">*</span>
                            </label>
                            <textarea name="body" id="body" rows="4" class="form-control radius-8" placeholder="Tuliskan isi pesan notifikasi secara rinci dan jelas..." required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Kanal Pengiriman</label>
                            <div class="d-flex flex-wrap gap-16">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="channel" id="channel_db" value="database" checked>
                                    <label class="form-check-label text-sm fw-medium" for="channel_db">
                                        In-App / Database (Dashboard Pengguna)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="channel" id="channel_email" value="email">
                                    <label class="form-check-label text-sm fw-medium" for="channel_email">
                                        Email & In-App
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-neutral-200 py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:plain-bold" class="text-lg"></iconify-icon>
                        <span>Kirim Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Tinjau Cepat Detail Notifikasi -->
<div class="modal fade" id="detailNotificationModal" tabindex="-1" aria-labelledby="detailNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16 border-0 shadow">
            <div class="modal-header border-bottom border-neutral-200 py-16 px-24">
                <div class="d-flex align-items-center gap-3">
                    <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                        <iconify-icon icon="solar:info-circle-outline" class="text-xl"></iconify-icon>
                    </span>
                    <h6 class="modal-title fw-semibold text-lg mb-0" id="detailNotificationModalLabel">Tinjau Notifikasi</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <div class="mb-16">
                    <span class="badge bg-neutral-100 text-neutral-800 border radius-6 text-xs px-8 py-3 mb-8" id="modalCategoryBadge">-</span>
                    <h5 class="fw-bold text-primary-light mb-8" id="modalTitle">-</h5>
                </div>

                <div class="bg-neutral-50 radius-8 p-16 mb-16 border border-neutral-200">
                    <div class="row g-12 text-sm">
                        <div class="col-6">
                            <span class="text-secondary-light text-xs d-block">Penerima</span>
                            <span class="fw-semibold text-primary-light d-block text-truncate" id="modalRecipient">-</span>
                            <span class="text-xs text-secondary-light" id="modalEmail">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-secondary-light text-xs d-block">Profil / Peran</span>
                            <span class="fw-semibold text-primary-light d-block" id="modalProfile">-</span>
                            <span class="text-xs text-secondary-light" id="modalChannel">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-secondary-light text-xs d-block">Waktu Terkirim</span>
                            <span class="fw-semibold text-primary-light d-block" id="modalSentTime">-</span>
                        </div>
                        <div class="col-6">
                            <span class="text-secondary-light text-xs d-block">Status Dibaca</span>
                            <span class="fw-semibold text-primary-light d-block" id="modalReadStatus">-</span>
                        </div>
                    </div>
                </div>

                <div class="mb-16">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-4">Isi Pesan:</label>
                    <div class="p-16 radius-8 bg-neutral-50 border border-neutral-200 text-sm text-secondary-dark" style="white-space: pre-wrap;" id="modalBody">
                        -
                    </div>
                </div>

                <div id="modalDataWrapper" class="d-none">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-0">Payload / Metadata JSON:</label>
                        <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 px-8 py-2 text-xxs d-inline-flex align-items-center gap-1" id="modalBtnCopyJson">
                            <iconify-icon icon="solar:copy-outline"></iconify-icon>
                            <span id="modalBtnCopyJsonText">Salin JSON</span>
                        </button>
                    </div>
                    <pre class="p-12 radius-8 bg-neutral-100 text-neutral-800 text-xxs font-monospace mb-0" id="modalDataJson">-</pre>
                </div>
            </div>
            <div class="modal-footer border-top border-neutral-200 py-12 px-24 d-flex justify-content-between">
                <a href="#" id="modalDirectDetailLink" class="btn btn-outline-primary-600 radius-8 px-16 py-8 text-sm d-inline-flex align-items-center gap-1">
                    <span>Buka Rincian Lengkap</span>
                    <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                </a>
                <button type="button" class="btn btn-neutral-300 radius-8 px-20 py-8" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleUserSelect(targetGroup) {
    const wrapper = document.getElementById('userSelectWrapper');
    const userSelect = document.getElementById('target_user_id');
    if (targetGroup === 'user') {
        wrapper.classList.remove('d-none');
        userSelect.setAttribute('required', 'required');
    } else {
        wrapper.classList.add('d-none');
        userSelect.removeAttribute('required');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const detailButtons = document.querySelectorAll('.btn-show-detail');
    detailButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalTitle').textContent = this.dataset.title || '-';
            document.getElementById('modalCategoryBadge').textContent = (this.dataset.type || '-') + ' (' + (this.dataset.channel || '-') + ')';
            document.getElementById('modalRecipient').textContent = this.dataset.recipient || '-';
            document.getElementById('modalEmail').textContent = this.dataset.email || '-';
            document.getElementById('modalProfile').textContent = this.dataset.profile || '-';
            document.getElementById('modalChannel').textContent = 'Kanal: ' + (this.dataset.channel || '-');
            document.getElementById('modalSentTime').textContent = this.dataset.time || '-';
            document.getElementById('modalReadStatus').textContent = this.dataset.read || '-';
            document.getElementById('modalBody').textContent = this.dataset.body || '-';

            const directLink = document.getElementById('modalDirectDetailLink');
            if (directLink) {
                directLink.setAttribute('href', this.dataset.detailUrl || '#');
            }

            const rawJson = this.dataset.json;
            const dataWrapper = document.getElementById('modalDataWrapper');
            const dataPre = document.getElementById('modalDataJson');
            if (rawJson && rawJson.trim() !== '' && rawJson.trim() !== 'null') {
                try {
                    const parsed = JSON.parse(rawJson);
                    dataPre.textContent = JSON.stringify(parsed, null, 2);
                    dataWrapper.classList.remove('d-none');
                } catch (e) {
                    dataPre.textContent = rawJson;
                    dataWrapper.classList.remove('d-none');
                }
            } else {
                dataWrapper.classList.add('d-none');
            }

            // AUTO MARK AS READ saat Tinjau Cepat diklik
            const notifId = this.dataset.id;
            const isUnread = this.dataset.isUnread === '1';

            if (isUnread && notifId) {
                // Update tampilan modal
                document.getElementById('modalReadStatus').innerHTML = '<span class="text-success-main fw-semibold">Telah Dibaca (Baru saja)</span>';

                // Update tampilan baris tabel
                const row = this.closest('tr');
                if (row) {
                    row.classList.remove('bg-primary-50/20');
                    const statusCell = row.querySelector('.status-col-cell');
                    if (statusCell) {
                        statusCell.innerHTML = `
                            <span class="badge bg-success-focus text-success-main border-success px-8 py-3 radius-6 text-xxs d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                Dibaca
                            </span>
                            <span class="d-block text-3xs text-secondary-light mt-1">Baru saja</span>
                        `;
                    }
                    const readForm = row.querySelector('.form-mark-read');
                    if (readForm) readForm.remove();
                }

                this.dataset.isUnread = '0';
                this.dataset.read = 'Telah Dibaca (Baru saja)';

                // Kirim request baca ke server
                fetch('<?= site_url('dashboard/sistem/notifikasi') ?>/' + notifId + '/baca', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: '<?= csrf_token() ?>=' + encodeURIComponent('<?= csrf_hash() ?>')
                })
                .then(res => res.json())
                .then(data => {
                    // Segera update lonceng notifikasi header
                    if (typeof window.refreshNotificationBell === 'function') {
                        window.refreshNotificationBell();
                    }
                })
                .catch(err => {
                    console.error('Auto-read notification error:', err);
                });
            }
        });
    });

    const modalCopyBtn = document.getElementById('modalBtnCopyJson');
    if (modalCopyBtn) {
        modalCopyBtn.addEventListener('click', function () {
            const preEl = document.getElementById('modalDataJson');
            const textToCopy = preEl ? preEl.textContent : '';
            const btnText = document.getElementById('modalBtnCopyJsonText');
            navigator.clipboard.writeText(textToCopy).then(function () {
                const orig = btnText.textContent;
                btnText.textContent = 'Tersalin!';
                setTimeout(() => { btnText.textContent = orig; }, 1500);
            });
        });
    }

    // Dashboard Push Card Initialization & Logic
    window.updateDashboardPushCard = function() {
        const badge = document.getElementById('dashPushStatusBadge');
        const desc = document.getElementById('dashPushStatusDesc');
        const btnAct = document.getElementById('dashBtnActivatePush');
        const btnActText = document.getElementById('dashBtnActivatePushText');
        const notice = document.getElementById('dashPushInsecureNotice');
        const icon = document.getElementById('dashPushIcon');

        if (!badge) return;

        const isSecure = window.isSecureContext || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        if (!isSecure && notice) {
            notice.classList.remove('d-none');
        }

        if (!('Notification' in window)) {
            badge.className = 'badge bg-info-50 text-info-600 px-8 py-3 radius-4 text-xs fw-semibold';
            badge.innerHTML = '<iconify-icon icon="solar:volume-loud-outline" class="me-1 align-middle"></iconify-icon> Mode In-App Toast &amp; Suara Aktif';
            if (desc) desc.textContent = 'Browser ini tidak menyediakan Web Notification API native. Fitur notifikasi mengambang (In-App Toast) dan nada suara chime aktif penuh.';
            if (btnAct) btnAct.style.display = 'none';
            return;
        }

        if (Notification.permission === 'granted') {
            badge.className = 'badge bg-success-50 text-success-600 px-8 py-3 radius-4 text-xs fw-semibold';
            badge.innerHTML = '<iconify-icon icon="solar:check-circle-bold" class="me-1 align-middle text-success-main"></iconify-icon> 🟢 Aktif (Desktop Banner &amp; Toast)';
            if (desc) desc.textContent = 'Notifikasi browser dan desktop Windows/Chrome/Edge aktif. Anda akan menerima notifikasi mengambang seketika dan bunyi chime harmonik saat ada aktivitas baru.';
            if (btnAct) {
                btnAct.className = 'btn btn-outline-success-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2 disabled';
                btnAct.disabled = true;
                if (btnActText) btnActText.textContent = 'Sudah Diaktifkan';
            }
            if (icon) {
                icon.className = 'text-2xl text-success-main';
                icon.setAttribute('icon', 'solar:check-circle-bold');
            }
        } else if (Notification.permission === 'denied') {
            badge.className = 'badge bg-danger-50 text-danger-600 px-8 py-3 radius-4 text-xs fw-semibold';
            badge.innerHTML = '<iconify-icon icon="solar:close-circle-bold" class="me-1 align-middle text-danger-main"></iconify-icon> 🔴 Izin Diblokir Browser';
            if (desc) desc.textContent = 'Izin notifikasi diblokir di setelan browser Anda. Klik ikon gembok/setelan situs di sebelah URL browser untuk mengizinkan notifikasi, lalu muat ulang halaman.';
            if (btnAct) {
                btnAct.className = 'btn btn-outline-neutral-400 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2';
                btnAct.disabled = false;
                if (btnActText) btnActText.textContent = 'Cara Buka Blokir';
                btnAct.onclick = function() {
                    alert('Untuk mengizinkan notifikasi:\n1. Klik ikon setelan / gembok di samping URL browser.\n2. Ubah setelan "Notifikasi" menjadi "Izinkan" / "Allow".\n3. Muat ulang halaman ini.');
                };
            }
        } else {
            // default
            badge.className = 'badge bg-warning-50 text-warning-600 px-8 py-3 radius-4 text-xs fw-semibold';
            badge.innerHTML = '<iconify-icon icon="solar:bell-bing-bold" class="me-1 align-middle text-warning-main"></iconify-icon> 🟡 Belum Diaktifkan';
            if (desc) desc.textContent = 'Notifikasi desktop belum diizinkan. Klik tombol "Aktifkan Notifikasi Desktop" di samping agar browser dapat memunculkan popup pemberitahuan saat ada aktivitas baru.';
            if (btnAct) {
                btnAct.className = 'btn btn-primary-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2';
                btnAct.disabled = false;
                if (btnActText) btnActText.textContent = 'Aktifkan Notifikasi Desktop';
                btnAct.onclick = window.dashActivatePushNotification;
            }
        }
    };

    window.dashActivatePushNotification = function() {
        if (typeof window.dashRequestPushPermission === 'function') {
            window.dashRequestPushPermission();
        } else if ('Notification' in window) {
            Notification.requestPermission().then(function(perm) {
                window.updateDashboardPushCard();
                if (perm === 'granted') {
                    window.dashTestPushNotification('Notifikasi Desktop Admin Berhasil Diaktifkan!');
                }
            });
        }
    };

    window.dashTestPushNotification = function(customTitle) {
        if (typeof window.dashTestNotification === 'function') {
            window.dashTestNotification({
                id: 999999,
                title: customTitle || 'Uji Coba Notifikasi Admin BegawiPengadaan',
                body: 'Selamat! Audio chime harmonik, in-app floating toast, dan notifikasi desktop dashboard berhasil berjalan.',
                icon: 'solar:tuning-square-2-bold',
                url: window.location.href
            });
        } else {
            alert('Notifikasi uji coba terkirim.');
        }
    };

    window.updateDashboardPushCard();
});
</script>
<?= $this->endSection() ?>

<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/communication/_nav_communication', ['activeSub' => 'inbox']) ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pesan Masuk (Ticketing Helpdesk)') ?></h6>
        <p class="text-secondary-light mb-0">Kelola tiket konsultasi, aspirasi, dan percakapan dua arah dengan pemohon bantuan ekosistem PBJ.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="badge bg-danger-subtle text-danger-main border border-danger-subtle px-12 py-8 radius-8 text-xs fw-bold">
            <iconify-icon icon="solar:bell-bing-bold" class="me-1"></iconify-icon> <?= $unreadCount ?> Baru / Belum Dibaca
        </span>
        <?php if (! empty($waitingAdminCount)): ?>
            <span class="badge bg-warning-subtle text-warning-main border border-warning-subtle px-12 py-8 radius-8 text-xs fw-bold">
                <iconify-icon icon="solar:chat-round-line-bold" class="me-1"></iconify-icon> <?= $waitingAdminCount ?> Tanggapan Pemohon
            </span>
        <?php endif; ?>
        <span class="badge bg-success-subtle text-success-main border border-success-subtle px-12 py-8 radius-8 text-xs fw-bold">
            <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon> <?= $repliedCount ?> Terbalas
        </span>
    </div>
</div>

<!-- KPI Mini Summary Cards -->
<div class="row row-cols-xxl-5 row-cols-md-3 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border radius-12 p-16 h-100 bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="w-44-px h-44-px radius-10 bg-primary-subtle text-primary-600 d-flex justify-content-center align-items-center fs-4">
                    <iconify-icon icon="solar:inbox-line-bold"></iconify-icon>
                </span>
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block">Total Tiket</span>
                    <h6 class="fw-bold mb-0 text-primary-light"><?= number_format($totalCount ?? count($messages)) ?></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border radius-12 p-16 h-100 bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="w-44-px h-44-px radius-10 bg-danger-subtle text-danger-600 d-flex justify-content-center align-items-center fs-4">
                    <iconify-icon icon="solar:alarm-bold"></iconify-icon>
                </span>
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block">Belum Dibaca</span>
                    <h6 class="fw-bold mb-0 text-danger-main"><?= number_format($unreadCount) ?></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border radius-12 p-16 h-100 bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="w-44-px h-44-px radius-10 bg-warning-subtle text-warning-600 d-flex justify-content-center align-items-center fs-4">
                    <iconify-icon icon="solar:chat-dots-bold"></iconify-icon>
                </span>
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block">Tanggapan Pemohon</span>
                    <h6 class="fw-bold mb-0 text-warning-main"><?= number_format($waitingAdminCount ?? 0) ?></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border radius-12 p-16 h-100 bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="w-44-px h-44-px radius-10 bg-info-subtle text-info-600 d-flex justify-content-center align-items-center fs-4">
                    <iconify-icon icon="solar:refresh-circle-bold"></iconify-icon>
                </span>
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block">Sedang Diproses</span>
                    <h6 class="fw-bold mb-0 text-info-main"><?= number_format($inProgressCount ?? 0) ?></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border radius-12 p-16 h-100 bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="w-44-px h-44-px radius-10 bg-success-subtle text-success-600 d-flex justify-content-center align-items-center fs-4">
                    <iconify-icon icon="solar:check-read-bold"></iconify-icon>
                </span>
                <div>
                    <span class="text-secondary-light text-xs fw-medium d-block">Selesai / Dibalas</span>
                    <h6 class="fw-bold mb-0 text-success-main"><?= number_format(($repliedCount ?? 0) + ($closedCount ?? 0)) ?></h6>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs & Search Header -->
<div class="card mb-24">
    <div class="card-body p-16">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <ul class="nav nav-pills gap-2 flex-wrap mb-0">
                <li class="nav-item">
                    <a class="nav-link radius-8 text-xs fw-semibold <?= empty($currentStatus) ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk') ?>">
                        Semua Tiket
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 text-xs fw-semibold <?= $currentStatus === 'unread' ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk?status=unread') ?>">
                        Belum Dibaca <?= $unreadCount > 0 ? "({$unreadCount})" : '' ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 text-xs fw-semibold <?= $currentStatus === 'waiting_admin' ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk?status=waiting_admin') ?>">
                        Tanggapan Pemohon <?= ($waitingAdminCount ?? 0) > 0 ? "({$waitingAdminCount})" : '' ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 text-xs fw-semibold <?= $currentStatus === 'in_progress' ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk?status=in_progress') ?>">
                        Sedang Diproses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 text-xs fw-semibold <?= $currentStatus === 'replied' ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk?status=replied') ?>">
                        Sudah Dibalas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 text-xs fw-semibold <?= $currentStatus === 'closed' ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk?status=closed') ?>">
                        Selesai (Closed)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link radius-8 text-xs fw-semibold <?= $currentStatus === 'archived' ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk?status=archived') ?>">
                        Diarsipkan
                    </a>
                </li>
            </ul>

            <!-- Search Form -->
            <form method="get" action="<?= site_url('dashboard/komunikasi/pesan-masuk') ?>" class="d-flex align-items-center gap-2">
                <?php if (! empty($currentStatus)): ?>
                    <input type="hidden" name="status" value="<?= esc($currentStatus) ?>">
                <?php endif; ?>
                <div class="input-group input-group-sm" style="min-width: 260px;">
                    <span class="input-group-text bg-white border-end-0 radius-start-8">
                        <iconify-icon icon="solar:magnifer-linear" class="text-secondary"></iconify-icon>
                    </span>
                    <input type="text" name="q" class="form-control form-control-sm border-start-0" placeholder="Cari tiket, nama, email..." value="<?= esc($searchQuery ?? '') ?>">
                    <button type="submit" class="btn btn-primary-600 btn-sm radius-end-8 px-12" aria-label="Cari Tiket" title="Cari">
                        <iconify-icon icon="solar:magnifer-linear" class="me-1"></iconify-icon> Cari
                    </button>
                </div>
                <?php if (! empty($searchQuery) || ! empty($currentCategory)): ?>
                    <a href="<?= site_url('dashboard/komunikasi/pesan-masuk') ?>" class="btn btn-outline-secondary btn-sm radius-8 px-8 py-4 d-inline-flex align-items-center gap-1" title="Reset Pencarian" aria-label="Reset Pencarian">
                        <iconify-icon icon="solar:restart-bold" class="text-base"></iconify-icon>
                        <span class="d-none d-sm-inline text-xs">Reset</span>
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 150px;">No. Tiket</th>
                        <th style="width: 210px;">Pengirim</th>
                        <th>Perihal & Percakapan</th>
                        <th style="width: 140px;">Kategori</th>
                        <th style="width: 130px;">Waktu Masuk</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-40 text-muted">
                                <iconify-icon icon="solar:chat-line-broken" class="fs-1 d-block mx-auto mb-2 text-secondary-light"></iconify-icon>
                                Belum ada tiket atau pesan masuk pada folder ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <?php
                            $isWaitingAdmin = $msg['status'] === 'waiting_admin';
                            $isUnread       = $msg['status'] === 'unread';
                            $rowClass       = $isWaitingAdmin ? 'bg-warning-50' : ($isUnread ? 'bg-primary-50 fw-semibold' : '');
                            ?>
                            <tr class="<?= $rowClass ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-1 mb-1">
                                        <code class="text-xs fw-bold font-monospace"><?= esc($msg['ticket_number'] ?: ('#' . $msg['id'])) ?></code>
                                    </div>
                                    <?php
                                    $prioClass = match ($msg['priority'] ?? 'normal') {
                                        'urgent' => 'bg-danger text-white',
                                        'high'   => 'bg-danger-subtle text-danger-main border border-danger-subtle',
                                        'low'    => 'bg-neutral-200 text-neutral-700',
                                        default  => 'bg-neutral-100 text-neutral-600',
                                    };
                                    $prioLabel = match ($msg['priority'] ?? 'normal') {
                                        'urgent' => 'Mendesak',
                                        'high'   => 'Tinggi',
                                        'low'    => 'Rendah',
                                        default  => 'Normal',
                                    };
                                    ?>
                                    <span class="badge <?= $prioClass ?> text-2xs px-6 py-2 radius-4">
                                        <?= $prioLabel ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="w-32-px h-32-px radius-circle bg-neutral-200 text-neutral-900 fw-bold d-flex align-items-center justify-content-center text-xs flex-shrink-0">
                                            <?= strtoupper(mb_substr($msg['sender_name'], 0, 1)) ?>
                                        </span>
                                        <div>
                                            <strong class="text-sm d-block text-primary-light"><?= esc($msg['sender_name']) ?></strong>
                                            <small class="text-secondary-light text-2xs d-block text-truncate" style="max-width: 170px;"><?= esc($msg['sender_email']) ?></small>
                                            
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <?php if (! empty($msg['is_registered'])): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle text-2xs px-6 py-2" title="Pengguna Terdaftar BegawiPengadaan">
                                                        <iconify-icon icon="solar:verified-check-bold"></iconify-icon> Terdaftar
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-neutral-200 text-neutral-600 text-2xs px-6 py-2" title="Pengirim Tamu / Publik">
                                                        Tamu
                                                    </span>
                                                <?php endif; ?>

                                                <?php if (! empty($msg['sender_phone'])): ?>
                                                    <span class="text-muted text-2xs" title="No WA/Telp">
                                                        &middot; <?= esc($msg['sender_phone']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="text-xs fw-bold mb-0 text-primary-light"><?= esc($msg['subject']) ?></h6>
                                        <?php if (! empty($msg['threads_count']) && $msg['threads_count'] > 0): ?>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-2xs" title="Jumlah Balasan Percakapan">
                                                <iconify-icon icon="solar:chat-round-dots-bold"></iconify-icon> <?= $msg['threads_count'] ?> balasan
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-secondary-light mb-0 text-truncate" style="max-width: 360px;">
                                        <?= esc($msg['message']) ?>
                                    </p>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-200 text-neutral-800 text-2xs" style="max-width: 130px; white-space: normal; line-height: 1.3;">
                                        <?= esc($msg['service_category'] ?: 'Umum / PBJ') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-secondary text-xs d-block"><?= date('d M Y', strtotime($msg['created_at'])) ?></span>
                                    <small class="text-muted text-2xs"><?= date('H:i', strtotime($msg['created_at'])) ?> WIB</small>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = match ($msg['status']) {
                                        'unread'        => 'bg-danger-subtle text-danger-main border border-danger-subtle',
                                        'waiting_admin' => 'bg-warning-subtle text-warning-main border border-warning-subtle',
                                        'in_progress'   => 'bg-info-subtle text-info border border-info-subtle',
                                        'waiting_user'  => 'bg-info-subtle text-info border border-info-subtle',
                                        'read'          => 'bg-neutral-200 text-neutral-700',
                                        'replied'       => 'bg-success-subtle text-success-main border border-success-subtle',
                                        'closed'        => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                        'archived'      => 'bg-neutral-200 text-neutral-700',
                                        default         => 'bg-neutral-100 text-neutral-600',
                                    };
                                    $statusLabel = match ($msg['status']) {
                                        'unread'        => 'Belum Dibaca',
                                        'waiting_admin' => 'Tanggapan Pemohon',
                                        'in_progress'   => 'Sedang Diproses',
                                        'waiting_user'  => 'Menunggu Pemohon',
                                        'read'          => 'Sudah Dibaca',
                                        'replied'       => 'Sudah Dibalas',
                                        'closed'        => 'Selesai (Closed)',
                                        'archived'      => 'Diarsipkan',
                                        default         => ucfirst($msg['status']),
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-10 py-6 radius-4 text-xs d-inline-block">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/komunikasi/pesan-masuk/detail/' . $msg['id']) ?>" class="btn btn-primary-600 btn-sm radius-8 px-10 py-6 d-inline-flex align-items-center gap-1" title="Buka Percakapan & Tiket">
                                            <iconify-icon icon="solar:chat-round-line-bold"></iconify-icon>
                                            <span class="text-2xs fw-bold">Buka</span>
                                        </a>
                                        <a href="<?= site_url('dashboard/komunikasi/pesan-masuk/hapus/' . $msg['id']) ?>" onclick="return confirm('Hapus tiket pesan ini? Seluruh riwayat percakapan akan ikut terhapus.')" class="btn btn-outline-danger btn-sm radius-8 px-8 py-4" title="Hapus Tiket">
                                            <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

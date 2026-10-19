<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pesan Masuk (Inbox)') ?></h6>
        <p class="text-secondary-light mb-0">Daftar aspirasi, pertanyaan, permintaan bantuan, dan konsultasi dari formulir kontak portal publik.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-danger-subtle text-danger-main border border-danger-subtle px-12 py-8 radius-8 text-xs fw-bold">
            <?= $unreadCount ?> Pesan Belum Dibaca
        </span>
        <span class="badge bg-success-subtle text-success-main border border-success-subtle px-12 py-8 radius-8 text-xs fw-bold">
            <?= $repliedCount ?> Pesan Terbalas
        </span>
    </div>
</div>

<!-- Filter Tabs -->
<div class="card mb-24">
    <div class="card-body p-12">
        <ul class="nav nav-pills gap-2 flex-wrap">
            <li class="nav-item">
                <a class="nav-link radius-8 text-sm <?= empty($currentStatus) ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk') ?>">
                    Semua Pesan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 text-sm <?= $currentStatus === 'unread' ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk?status=unread') ?>">
                    Belum Dibaca
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 text-sm <?= $currentStatus === 'read' ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk?status=read') ?>">
                    Sudah Dibaca
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 text-sm <?= $currentStatus === 'replied' ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk?status=replied') ?>">
                    Sudah Dibalas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 text-sm <?= $currentStatus === 'archived' ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk?status=archived') ?>">
                    Diarsipkan
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 140px;">No. Tiket</th>
                        <th>Pengirim</th>
                        <th>Perihal & Kategori</th>
                        <th>Waktu Masuk</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada pesan pada folder ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <tr class="<?= $msg['status'] === 'unread' ? 'bg-primary-50 fw-semibold' : '' ?>">
                                <td>
                                    <code class="text-xs fw-bold"><?= esc($msg['ticket_number'] ?: ('#' . $msg['id'])) ?></code>
                                </td>
                                <td>
                                    <strong class="text-sm d-block text-dark"><?= esc($msg['sender_name']) ?></strong>
                                    <small class="text-secondary-light"><?= esc($msg['sender_email']) ?></small>
                                    <?php if (! empty($msg['sender_phone'])): ?>
                                        <small class="text-muted d-block"><?= esc($msg['sender_phone']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (! empty($msg['service_category'])): ?>
                                        <span class="badge bg-neutral-200 text-neutral-800 text-2xs mb-1"><?= esc($msg['service_category']) ?></span>
                                    <?php endif; ?>
                                    <h6 class="text-xs fw-bold mb-1 text-dark"><?= esc($msg['subject']) ?></h6>
                                    <p class="text-xs text-secondary-light mb-0 text-truncate" style="max-width: 380px;"><?= esc($msg['message']) ?></p>
                                </td>
                                <td>
                                    <small class="text-secondary text-xs"><?= date('d M Y, H:i', strtotime($msg['created_at'])) ?> WIB</small>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = match ($msg['status']) {
                                        'unread'   => 'bg-danger-subtle text-danger-main border border-danger-subtle',
                                        'read'     => 'bg-info-subtle text-info border border-info-subtle',
                                        'replied'  => 'bg-success-subtle text-success-main border border-success-subtle',
                                        'archived' => 'bg-neutral-200 text-neutral-700',
                                        default    => 'bg-neutral-100 text-neutral-600',
                                    };
                                    $statusLabel = match ($msg['status']) {
                                        'unread'   => 'Belum Dibaca',
                                        'read'     => 'Sudah Dibaca',
                                        'replied'  => 'Sudah Dibalas',
                                        'archived' => 'Diarsipkan',
                                        default    => ucfirst($msg['status']),
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-10 py-6 radius-4 text-xs"><?= $statusLabel ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/komunikasi/pesan-masuk/detail/' . $msg['id']) ?>" class="btn btn-primary-600 btn-sm radius-8 px-10 py-6" title="Buka Pesan">
                                            <iconify-icon icon="solar:eye-bold"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/komunikasi/pesan-masuk/hapus/' . $msg['id']) ?>" onclick="return confirm('Hapus pesan ini?')" class="btn btn-outline-danger btn-sm radius-8 px-8 py-4" title="Hapus">
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

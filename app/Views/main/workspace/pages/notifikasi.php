<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <p class="text-uppercase text-muted small mb-2"><?= esc($sectionContent['eyebrow'] ?? 'Pusat Informasi') ?></p>
            <h1 class="h3 mb-2"><?= esc($sectionContent['title'] ?? 'Notifikasi') ?></h1>
            <p class="text-muted mb-0"><?= esc($sectionContent['description'] ?? 'Pusat pembaruan pembelajaran, tugas, penilaian, dan sistem.') ?></p>
        </div>
        <?php if (! empty($sectionContent['unread'])): ?>
            <form action="<?= route_to('workspace.notifications.read-all') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-primary btn-sm">Tandai Semua Dibaca</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if (! empty($sectionContent['cards'])): ?>
    <div class="row g-3 mb-4">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 border rounded shadow-sm">
                    <div class="card-body">
                        <span class="text-muted small text-uppercase fw-semibold"><?= esc($card['title']) ?></span>
                        <div class="display-6 fw-bold my-2 text-primary"><?= esc($card['value']) ?></div>
                        <span class="text-secondary small"><?= esc($card['meta'] ?? '') ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card border rounded shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fs-6 fw-bold">Daftar Notifikasi</h5>
        <?php if (! empty($sectionContent['unread'])): ?>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                <?= esc($sectionContent['unread']) ?> belum dibaca
            </span>
        <?php else: ?>
            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Semua sudah dibaca</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if (empty($sectionContent['notifications'])): ?>
            <div class="text-center py-5 text-muted">
                <p class="mb-0">Belum ada notifikasi baru untuk akun Anda.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($sectionContent['notifications'] as $item): ?>
                    <?php $isUnread = empty($item['read_at']); ?>
                    <div class="list-group-item p-3 <?= $isUnread ? 'bg-light-subtle' : '' ?>">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <?php if ($isUnread): ?>
                                        <span class="badge bg-primary">Baru</span>
                                    <?php endif; ?>
                                    <h6 class="mb-0 fw-semibold text-dark"><?= esc($item['title']) ?></h6>
                                </div>
                                <p class="mb-1 text-secondary small"><?= esc($item['body'] ?? '') ?></p>
                                <small class="text-muted"><?= esc(date('d/m/Y H:i', strtotime($item['created_at']))) ?></small>
                            </div>
                            <?php if ($isUnread): ?>
                                <form action="<?= route_to('workspace.notifications.read', $item['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted py-0" title="Tandai dibaca">
                                        <small>Tandai dibaca</small>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

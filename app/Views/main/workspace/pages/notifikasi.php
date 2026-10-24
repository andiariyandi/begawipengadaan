<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="ws-page-header mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-bell text-primary"></i> <?= esc($sectionContent['eyebrow'] ?? 'Pusat Informasi') ?></span>
            <h1 class="h3 fw-bold mb-1" style="color: #082f5c;"><?= esc($sectionContent['title'] ?? 'Notifikasi') ?></h1>
            <p class="text-muted mb-0"><?= esc($sectionContent['description'] ?? 'Pusat pembaruan pembelajaran, tugas, konsultasi, dan sistem.') ?></p>
        </div>
        <?php if (! empty($sectionContent['unread'])): ?>
            <form action="<?= route_to('workspace.notifications.read-all') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-2xs">
                    <i class="fas fa-check-double me-1"></i> Tandai Semua Dibaca
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- KPI Metric Cards -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <span class="text-muted small text-uppercase fw-semibold">Belum Dibaca</span>
            <div class="display-6 fw-bold my-1 text-danger"><?= sprintf('%02d', $categoryCounts['unread'] ?? 0) ?></div>
            <span class="text-secondary small">membutuhkan perhatian Anda</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <span class="text-muted small text-uppercase fw-semibold">Pembelajaran &amp; Kelas</span>
            <div class="display-6 fw-bold my-1" style="color: #082f5c;"><?= sprintf('%02d', $categoryCounts['learning'] ?? 0) ?></div>
            <span class="text-secondary small">materi, kuis, &amp; tugas</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <span class="text-muted small text-uppercase fw-semibold">Konsultasi PBJ</span>
            <div class="display-6 fw-bold my-1 text-info"><?= sprintf('%02d', $categoryCounts['consultation'] ?? 0) ?></div>
            <span class="text-secondary small">tanggapan tiket helpdesk</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-3 h-100 bg-white">
            <span class="text-muted small text-uppercase fw-semibold">Total Notifikasi</span>
            <div class="display-6 fw-bold my-1 text-secondary"><?= sprintf('%02d', $categoryCounts['all'] ?? 0) ?></div>
            <span class="text-secondary small">tercatat di riwayat sistem</span>
        </div>
    </div>
</div>

<!-- Filter Tabs Navigation -->
<div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden mb-4">
    <div class="card-body p-2 p-md-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <ul class="nav nav-pills gap-1 flex-nowrap overflow-x-auto pb-1 pb-md-0" style="scrollbar-width: thin;">
                <?php
                $isAllActive = empty($currentCategory) && empty($currentStatus);
                $isUnreadActive = ($currentStatus === 'unread');
                $isLearningActive = ($currentCategory === 'learning');
                $isConsultActive = ($currentCategory === 'consultation');
                $isCertActive = ($currentCategory === 'certificate');
                $isSystemActive = ($currentCategory === 'system');
                ?>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isAllActive ? 'active bg-primary text-white' : 'text-secondary hover-primary' ?>">
                        <span>Semua</span>
                        <span class="badge rounded-pill <?= $isAllActive ? 'bg-white text-primary' : 'bg-light text-secondary' ?>"><?= $categoryCounts['all'] ?? 0 ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>?status=unread" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isUnreadActive ? 'active bg-danger text-white' : 'text-secondary hover-primary' ?>">
                        <span>Belum Dibaca</span>
                        <?php if (!empty($categoryCounts['unread'])): ?>
                            <span class="badge rounded-pill <?= $isUnreadActive ? 'bg-white text-danger' : 'bg-danger-subtle text-danger' ?>"><?= $categoryCounts['unread'] ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>?category=learning" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isLearningActive ? 'active bg-primary text-white' : 'text-secondary hover-primary' ?>">
                        <span><i class="fas fa-graduation-cap"></i> Pembelajaran</span>
                        <span class="badge rounded-pill <?= $isLearningActive ? 'bg-white text-primary' : 'bg-light text-secondary' ?>"><?= $categoryCounts['learning'] ?? 0 ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>?category=consultation" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isConsultActive ? 'active bg-info text-white' : 'text-secondary hover-primary' ?>">
                        <span><i class="fas fa-comments"></i> Konsultasi</span>
                        <span class="badge rounded-pill <?= $isConsultActive ? 'bg-white text-info' : 'bg-light text-secondary' ?>"><?= $categoryCounts['consultation'] ?? 0 ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>?category=certificate" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isCertActive ? 'active bg-success text-white' : 'text-secondary hover-primary' ?>">
                        <span><i class="fas fa-award"></i> Sertifikat</span>
                        <span class="badge rounded-pill <?= $isCertActive ? 'bg-white text-success' : 'bg-light text-secondary' ?>"><?= $categoryCounts['certificate'] ?? 0 ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route_to('workspace.notifications') ?>?category=system" class="nav-link rounded-pill px-3 py-1.5 fs-sm d-flex align-items-center gap-2 <?= $isSystemActive ? 'active bg-secondary text-white' : 'text-secondary hover-primary' ?>">
                        <span><i class="fas fa-bullhorn"></i> Sistem</span>
                        <span class="badge rounded-pill <?= $isSystemActive ? 'bg-white text-secondary' : 'bg-light text-secondary' ?>"><?= $categoryCounts['system'] ?? 0 ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Notification Feed List -->
<div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
    <div class="card-header bg-white border-bottom p-3 px-md-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <h2 class="h5 fw-bold mb-0" style="color: #082f5c;">Riwayat Pemberitahuan</h2>
            <?php if (! empty($sectionContent['unread'])): ?>
                <span class="badge bg-danger rounded-pill px-2.5 py-1 text-xs">
                    <?= esc($sectionContent['unread']) ?> baru
                </span>
            <?php endif; ?>
        </div>
        <span class="text-muted small">Menampilkan <?= count($sectionContent['notifications'] ?? []) ?> notifikasi</span>
    </div>

    <div class="card-body p-0">
        <?php if (empty($sectionContent['notifications'])): ?>
            <div class="text-center py-5 px-3 text-muted">
                <div class="w-64-px h-64-px rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3">
                    <i class="fas fa-bell-slash text-secondary fs-3"></i>
                </div>
                <h6 class="fw-semibold text-dark mb-1">Tidak Ada Notifikasi</h6>
                <p class="text-secondary small mb-3">Tidak ditemukan pemberitahuan pada filter atau status yang dipilih saat ini.</p>
                <?php if (!empty($currentCategory) || !empty($currentStatus)): ?>
                    <a href="<?= route_to('workspace.notifications') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-undo me-1"></i> Tampilkan Semua Notifikasi
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($sectionContent['notifications'] as $item): ?>
                    <?php
                    $isUnread = empty($item['read_at']);
                    $catMeta = \App\Services\NotificationService::formatCategory($item['type'] ?? 'system');
                    $timeAgo = \App\Services\NotificationService::timeAgo($item['created_at'] ?? null);
                    $itemData = !empty($item['data']) ? json_decode($item['data'], true) : [];
                    $targetUrl = !empty($itemData['url']) ? $itemData['url'] : null;
                    ?>
                    <div class="list-group-item p-3 p-md-4 border-bottom <?= $isUnread ? 'bg-primary-subtle bg-opacity-25' : '' ?>" style="<?= $isUnread ? 'border-left: 4px solid #0c5aa6 !important;' : '' ?>">
                        <div class="d-flex align-items-start gap-3">
                            <!-- Category Icon Circle -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1 shadow-2xs" style="width: 42px; height: 42px; background: rgba(12, 90, 166, 0.08);">
                                <i class="<?= esc($catMeta['fa']) ?>" style="font-size: 17px;"></i>
                            </div>

                            <!-- Content Area -->
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge rounded-pill px-2.5 py-1 text-xs <?= esc($catMeta['badge']) ?>">
                                            <?= esc($catMeta['label']) ?>
                                        </span>
                                        <?php if ($isUnread): ?>
                                            <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 10px;">Baru</span>
                                        <?php endif; ?>
                                        <h6 class="mb-0 fw-semibold text-dark"><?= esc($item['title']) ?></h6>
                                    </div>
                                    <span class="text-muted small text-nowrap" title="<?= esc($item['created_at']) ?>">
                                        <i class="far fa-clock me-1"></i><?= esc($timeAgo) ?>
                                    </span>
                                </div>

                                <p class="mb-2 text-secondary small text-break"><?= esc($item['body'] ?? '') ?></p>

                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-1">
                                    <div>
                                        <?php if ($targetUrl): ?>
                                            <a href="<?= esc($targetUrl) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 text-xs fw-semibold">
                                                <?php if (str_contains($item['type'], 'ticket') || str_contains($item['type'], 'contact')): ?>
                                                    <i class="fas fa-comments me-1"></i> Buka Tiket Konsultasi
                                                <?php elseif (str_contains($item['type'], 'certificate')): ?>
                                                    <i class="fas fa-award me-1"></i> Akses Sertifikat
                                                <?php elseif (str_contains($item['type'], 'account') || str_contains($item['type'], 'profile')): ?>
                                                    <i class="fas fa-id-card me-1"></i> Verifikasi Berkas
                                                <?php elseif (str_contains($item['type'], 'class') || str_contains($item['type'], 'lesson')): ?>
                                                    <i class="fas fa-graduation-cap me-1"></i> Akses Kelas
                                                <?php elseif (str_contains($item['type'], 'event')): ?>
                                                    <i class="fas fa-calendar-alt me-1"></i> Rincian Event
                                                <?php else: ?>
                                                    <i class="fas fa-external-link-alt me-1"></i> Lihat Rincian
                                                <?php endif; ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($isUnread): ?>
                                        <form action="<?= route_to('workspace.notifications.read', $item['id']) ?>" method="post" class="m-0">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted py-0 px-1 text-xs" title="Tandai notifikasi telah dibaca">
                                                <i class="fas fa-check me-1"></i> Tandai Dibaca
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

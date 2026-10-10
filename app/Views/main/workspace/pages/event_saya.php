<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="bg-white border rounded-4 p-4 p-lg-5 mb-4 shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <p class="text-uppercase text-muted small fw-semibold mb-2"><?= esc($sectionContent['eyebrow'] ?? 'Kegiatan & Pelatihan') ?></p>
            <h1 class="h3 fw-bold mb-2" style="color: #082f5c;"><?= esc($pageTitle ?? 'Event Saya') ?></h1>
            <p class="text-muted mb-0"><?= esc($sectionContent['description'] ?? 'Daftar seminar, workshop, webinar, dan bimbingan teknis yang Anda ikuti.') ?></p>
        </div>
        <div>
            <a href="<?= site_url('event') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-search me-1"></i> Cari Event Baru
            </a>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<?php if (!empty($sectionContent['cards'])): ?>
    <div class="row g-4 mb-4">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <span class="text-muted small fw-semibold text-uppercase"><?= esc($card['title']) ?></span>
                    <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= esc($card['value']) ?></div>
                    <small class="text-muted"><?= esc($card['meta'] ?? '') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Event List -->
<?php if (empty($events)): ?>
    <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center">
        <i class="fas fa-calendar-times fs-1 text-muted opacity-50 mb-3"></i>
        <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">Belum Ada Event Terdaftar</h2>
        <p class="text-muted mb-3">Anda belum mendaftar ke kegiatan seminar, webinar, atau workshop apapun.</p>
        <div>
            <a href="<?= site_url('event') ?>" class="btn btn-primary rounded-pill px-4">
                Jelajahi Event BegawiPBJ
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($events as $ev): ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column justify-content-between position-relative overflow-hidden">
                    <div>
                        <!-- Badges Header -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill small">
                                <i class="fas fa-tag me-1"></i> <?= esc(strtoupper($ev['type'] ?? 'EVENT')) ?>
                            </span>
                            <?php
                            $statusBadge = match ($ev['registration_status']) {
                                'registered' => 'bg-info-subtle text-info',
                                'confirmed'  => 'bg-primary-subtle text-primary',
                                'attended'   => 'bg-success-subtle text-success',
                                'cancelled'  => 'bg-danger-subtle text-danger',
                                default      => 'bg-light text-secondary',
                            };
                            $statusLabel = match ($ev['registration_status']) {
                                'registered' => 'Terdaftar',
                                'confirmed'  => 'Terkonfirmasi',
                                'attended'   => 'Telah Hadir',
                                'cancelled'  => 'Dibatalkan',
                                default      => ucfirst($ev['registration_status']),
                            };
                            ?>
                            <span class="badge <?= $statusBadge ?> px-3 py-1 rounded-pill small fw-semibold">
                                <?= esc($statusLabel) ?>
                            </span>
                        </div>

                        <!-- Title -->
                        <h2 class="h5 fw-bold mb-2" style="color: #082f5c;">
                            <?= esc($ev['title']) ?>
                        </h2>

                        <!-- Metadata -->
                        <ul class="list-unstyled text-muted small mb-4">
                            <li class="mb-2">
                                <i class="far fa-calendar-alt text-warning me-2"></i>
                                <?= esc(date('d M Y, H:i', strtotime($ev['start_at']))) ?> - <?= esc(date('H:i', strtotime($ev['end_at']))) ?> WIB
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-video text-info me-2"></i> Mode: <strong><?= esc(ucfirst($ev['mode'])) ?></strong>
                            </li>
                            <?php if (!empty($ev['location'])): ?>
                                <li class="mb-2">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i> <?= esc($ev['location']) ?>
                                </li>
                            <?php endif; ?>
                            <?php if (!empty($ev['certificate_enabled'])): ?>
                                <li>
                                    <i class="fas fa-award text-success me-2"></i> Sertifikat Tersedia
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Footer Action -->
                    <div class="pt-3 border-top d-flex gap-2 justify-content-between align-items-center">
                        <small class="text-muted">Daftar: <?= esc(date('d/m/Y', strtotime($ev['registered_at']))) ?></small>
                        <div class="d-flex gap-2">
                            <?php if (!empty($ev['meeting_url']) && in_array($ev['registration_status'], ['registered', 'confirmed'])): ?>
                                <a href="<?= esc($ev['meeting_url']) ?>" target="_blank" class="btn btn-success btn-sm rounded-pill px-3">
                                    <i class="fas fa-video me-1"></i> Buka Link
                                </a>
                            <?php endif; ?>
                            <a href="<?= site_url('event/' . esc($ev['slug'] ?: $ev['event_id'])) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

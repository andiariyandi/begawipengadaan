<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-clipboard-check"></i> <?= esc($sectionContent['eyebrow'] ?? 'Assessment dan Feedback') ?></span>
            <h1><?= esc($pageTitle ?? 'Penilaian') ?></h1>
            <p><?= esc($sectionContent['description'] ?? 'Pantau pre-test, quiz, post-test, grading, nilai, dan feedback pembelajaran.') ?></p>
        </div>
    </div>
</div>

<?php if (!empty($sectionContent['cards'])): ?>
    <div class="row g-4 mb-4">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-md-4">
                <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <span class="text-muted small fw-semibold text-uppercase"><?= esc($card['title']) ?></span>
                    <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= esc($card['value']) ?></div>
                    <small class="d-block text-muted"><?= esc($card['meta'] ?? '') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($gradingList)): ?>
    <!-- Class Grade Sheet -->
    <div class="bg-white border rounded p-4 mb-4">
        <h2 class="h4 mb-3">Rekap Nilai Peserta Kelas</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Peserta</th>
                        <th>Tanggal Terdaftar</th>
                        <th>Nilai Akhir</th>
                        <th>Status Kelulusan</th>
                        <th>Sertifikat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gradingList as $g): ?>
                        <tr>
                            <td><strong><?= esc($g['user_name'] ?? 'Peserta') ?></strong><br><small class="text-muted"><?= esc($g['user_email'] ?? '') ?></small></td>
                            <td><?= esc($g['enrolled_at'] ?? '-') ?></td>
                            <td><strong><?= $g['final_score'] !== null ? esc($g['final_score']) : '-' ?></strong></td>
                            <td>
                                <span class="badge <?= ($g['status'] ?? '') === 'completed' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= esc($g['status'] ?? 'active') ?>
                                </span>
                            </td>
                            <td><?= !empty($g['certificate_eligible_at']) ? '✅ Memenuhi Syarat' : 'Belum Eligible' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif (!empty($gradingList)): ?>
    <!-- Class Grade Sheet -->
    <div class="bg-white border rounded p-4 mb-4">
        <h2 class="h4 mb-3">Rekap Nilai Peserta Kelas</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Peserta</th>
                        <th>Tanggal Terdaftar</th>
                        <th>Nilai Akhir</th>
                        <th>Status Kelulusan</th>
                        <th>Sertifikat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gradingList as $g): ?>
                        <tr>
                            <td><strong><?= esc($g['user_name'] ?? 'Peserta') ?></strong><br><small class="text-muted"><?= esc($g['user_email'] ?? '') ?></small></td>
                            <td><?= esc($g['enrolled_at'] ?? '-') ?></td>
                            <td><strong><?= $g['final_score'] !== null ? esc($g['final_score']) : '-' ?></strong></td>
                            <td>
                                <span class="badge <?= ($g['status'] ?? '') === 'completed' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= esc($g['status'] ?? 'active') ?>
                                </span>
                            </td>
                            <td><?= !empty($g['certificate_eligible_at']) ? '✅ Memenuhi Syarat' : 'Belum Eligible' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif (!empty($sectionContent['items'])): ?>
    <div class="bg-white border rounded p-4">
        <h2 class="h4 mb-3">Daftar Item / Aktivitas</h2>
        <div class="list-group list-group-flush">
            <?php foreach ($sectionContent['items'] as $item): ?>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                    <span><?= is_array($item) ? esc($item['title'] ?? '') : esc($item) ?></span>
                    <?php if (is_array($item) && !empty($item['url'])): ?>
                        <a href="<?= esc($item['url']) ?>" class="btn btn-outline-primary btn-sm">Buka</a>
                    <?php else: ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Aktif</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

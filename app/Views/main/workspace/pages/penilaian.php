<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2"><?= esc($sectionContent['eyebrow'] ?? 'Assessment dan Feedback') ?></p>
    <h1 class="mb-3"><?= esc($pageTitle ?? 'Penilaian') ?></h1>
    <p class="mb-0"><?= esc($sectionContent['description'] ?? 'Pantau pre-test, quiz, post-test, grading, nilai, dan feedback pembelajaran.') ?></p>
</div>

<?php if (!empty($sectionContent['cards'])): ?>
    <div class="row g-4 mb-4">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-md-4">
                <div class="bg-white border rounded p-4 h-100">
                    <p class="text-muted mb-2"><?= esc($card['title']) ?></p>
                    <strong class="display-6"><?= esc($card['value']) ?></strong>
                    <small class="d-block text-muted mt-2"><?= esc($card['meta'] ?? '') ?></small>
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
                        <span class="badge bg-light text-success">Aktif</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

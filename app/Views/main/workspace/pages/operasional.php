<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2"><?= esc($sectionContent['eyebrow'] ?? 'Operator Workspace') ?></p>
    <h1 class="h3 mb-2"><?= esc($sectionContent['title'] ?? 'Operasional') ?></h1>
    <p class="text-muted mb-0"><?= esc($sectionContent['description'] ?? 'Pantau verifikasi operasional, enrollment, kelas, approval, dan laporan.') ?></p>
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

<?php if (! empty($sectionContent['table'])): ?>
    <div class="card border rounded shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fs-6 fw-bold"><?= esc($sectionContent['tableTitle'] ?? 'Data Operasional') ?></h5>
            <span class="badge bg-light text-dark border"><?= count($sectionContent['table']['rows'] ?? []) ?> Data Ditampilkan</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <?php foreach ($sectionContent['table']['headers'] as $header): ?>
                            <th class="small text-uppercase text-muted"><?= esc($header) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sectionContent['table']['rows'])): ?>
                        <tr>
                            <td colspan="<?= count($sectionContent['table']['headers']) ?>" class="text-center py-4 text-muted">
                                Belum ada antrean data pada kategori ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sectionContent['table']['rows'] as $row): ?>
                            <tr>
                                <?php foreach ($row as $cell): ?>
                                    <td><?= $cell ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if (! empty($sectionContent['items'])): ?>
    <div class="card border rounded shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="card-title mb-0 fs-6 fw-bold">Navigasi Cepat & Modul Terkait</h5>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                <?php foreach ($sectionContent['items'] as $item): ?>
                    <div class="list-group-item px-0 py-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <?php if (is_array($item)): ?>
                            <span class="fw-medium text-dark"><?= esc($item['label']) ?></span>
                            <?php if (! empty($item['url'])): ?>
                                <a href="<?= esc($item['url']) ?>" class="btn btn-sm btn-outline-primary"><?= esc($item['btn'] ?? 'Buka') ?></a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-dark"><?= esc($item) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

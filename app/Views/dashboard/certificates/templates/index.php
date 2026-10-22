<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Template Sertifikat PBJ</h6>
        <p class="text-secondary-light mb-0">
            Kelola template desain sertifikat resmi. Format penomoran otomatis: <code>SERT-{number}/{tipe}-{ID}/05/{YEAR}</code>.
        </p>
    </div>
    <a href="<?= site_url('dashboard/sertifikat/template/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-bold" class="text-lg"></iconify-icon>
        Tambah Template Desain
    </a>
</div>

<?php if (empty($items)): ?>
    <div class="card">
        <div class="card-body p-32 text-center text-secondary-light">
            <iconify-icon icon="solar:diploma-bold" class="text-5xl text-neutral-400 mb-3"></iconify-icon>
            <p class="mb-12">Belum ada template sertifikat yang tersimpan.</p>
            <a href="<?= site_url('dashboard/sertifikat/template/tambah') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-8 text-sm">
                Mulai Rancang Template Pertama
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0 begawi-table" data-table-name="template-sertifikat" data-page-length="10">
                    <thead>
                        <tr>
                            <th>Nama Template</th>
                            <th>Tipe Sumber &amp; Sasaran</th>
                            <th>Background Desain</th>
                            <th>Status</th>
                            <th>Terakhir Diperbarui</th>
                            <th class="text-center no-sort no-export action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $row): ?>
                        <?php
                        $hasBg = ! empty($row['background_file_id']);
                        if (! $hasBg && ! empty($row['template_payload'])) {
                            $decoded = is_array($row['template_payload']) ? $row['template_payload'] : json_decode((string)$row['template_payload'], true);
                            $hasBg = ! empty($decoded['background_path']);
                        }
                        ?>
                        <tr>
                            <td>
                                <h6 class="text-md mb-1 fw-semibold text-primary-light"><?= esc($row['name']) ?></h6>
                                <code class="text-xs text-secondary-light">ID: #<?= (int) $row['id'] ?></code>
                            </td>
                            <td>
                                <div class="mb-4">
                                    <?php if ($row['source_type'] === 'learning'): ?>
                                        <span class="badge bg-primary-50 text-primary-600 px-8 py-3 radius-4 text-xs fw-bold">
                                            Arena Belajar (AB)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-info-50 text-info-600 px-8 py-3 radius-4 text-xs fw-bold">
                                            Event / Agenda (EV)
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($row['source_type'] === 'event'): ?>
                                    <span class="badge bg-neutral-100 text-neutral-800 px-8 py-3 radius-4 text-xs fw-semibold d-inline-block text-truncate" style="max-width: 200px;" title="<?= esc($row['target_title']) ?>">
                                        <iconify-icon icon="solar:calendar-bold" class="text-info me-1"></iconify-icon><?= esc($row['target_title']) ?>
                                    </span>
                                <?php else: ?>
                                    <?php if (($row['target_scope'] ?? '') === 'program'): ?>
                                        <span class="badge bg-purple-50 text-purple-600 px-8 py-3 radius-4 text-xs fw-bold d-block mb-1 text-truncate" style="max-width: 220px;" title="<?= esc($row['target_title']) ?>">
                                            <iconify-icon icon="solar:layers-bold" class="me-1"></iconify-icon>Program: <?= esc($row['target_title']) ?>
                                        </span>
                                        <small class="text-2xs text-secondary-light d-block"><iconify-icon icon="solar:lock-bold" class="text-warning"></iconify-icon> Kunci course &amp; kelas di bawahnya</small>
                                    <?php elseif (($row['target_scope'] ?? '') === 'course'): ?>
                                        <span class="badge bg-primary-50 text-primary-700 px-8 py-3 radius-4 text-xs fw-bold d-block mb-1 text-truncate" style="max-width: 220px;" title="<?= esc($row['target_title']) ?>">
                                            <iconify-icon icon="solar:book-bookmark-bold" class="me-1"></iconify-icon>Course: <?= esc($row['target_title']) ?>
                                        </span>
                                        <small class="text-2xs text-secondary-light d-block"><iconify-icon icon="solar:lock-bold" class="text-warning"></iconify-icon> Kunci kelas di bawahnya</small>
                                    <?php elseif (($row['target_scope'] ?? '') === 'class'): ?>
                                        <span class="badge bg-success-50 text-success-700 px-8 py-3 radius-4 text-xs fw-bold d-block mb-1 text-truncate" style="max-width: 220px;" title="<?= esc($row['target_title']) ?>">
                                            <iconify-icon icon="solar:square-academic-cap-bold" class="me-1"></iconify-icon>Kelas: <?= esc($row['target_title']) ?>
                                        </span>
                                        <small class="text-2xs text-success-600 d-block">Khusus 1 kelas</small>
                                    <?php else: ?>
                                        <span class="badge bg-neutral-100 text-neutral-600 px-8 py-3 radius-4 text-xs fw-medium d-block">
                                            Standar / Global (Fallback)
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($hasBg): ?>
                                    <span class="badge bg-success-50 text-success-600 px-8 py-4 radius-4 text-xs fw-semibold d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Desain Terpasang
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-neutral-100 text-neutral-600 px-8 py-4 radius-4 text-xs fw-medium">
                                        Standar (Tanpa Background)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= view('dashboard/components/status_badge', ['status' => (int) $row['is_active'] === 1 ? 'active' : 'inactive']) ?>
                            </td>
                            <td>
                                <small class="text-secondary-light">
                                    <?= ! empty($row['updated_at']) ? esc(date('d M Y H:i', strtotime($row['updated_at']))) : '—' ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="<?= site_url('dashboard/sertifikat/template/' . (int) $row['id'] . '/preview') ?>"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-info text-xs radius-6 px-10 py-4"
                                       title="Pratinjau HTML">
                                        <iconify-icon icon="solar:eye-bold" class="text-sm"></iconify-icon> Pratinjau
                                    </a>
                                    <a href="<?= site_url('dashboard/sertifikat/template/' . (int) $row['id']) ?>"
                                       class="btn btn-sm btn-outline-primary text-xs radius-6 px-10 py-4"
                                       title="Detail Template">
                                        Detail
                                    </a>
                                    <a href="<?= site_url('dashboard/sertifikat/template/' . (int) $row['id'] . '/ubah') ?>"
                                       class="btn btn-sm btn-outline-secondary text-xs radius-6 px-10 py-4"
                                       title="Ubah Template">
                                        Ubah
                                    </a>
                                    <?php if ((int) $row['is_active'] === 1): ?>
                                        <form method="post"
                                              action="<?= site_url('dashboard/sertifikat/template/' . (int) $row['id'] . '/nonaktifkan') ?>"
                                              class="d-inline"
                                              onsubmit="return confirm('Nonaktifkan template ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-warning text-xs radius-6 px-8 py-4">
                                                Nonaktifkan
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post"
                                              action="<?= site_url('dashboard/sertifikat/template/' . (int) $row['id'] . '/aktifkan') ?>"
                                              class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success text-xs radius-6 px-8 py-4">
                                                Aktifkan
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

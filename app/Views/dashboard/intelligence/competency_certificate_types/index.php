<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/intelligence/_nav_intelligence', ['activeSub' => 'certificate-types']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Standar Jenis Sertifikat Kompetensi PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Katalog standar kompetensi kerja nasional Indonesia (SKKNI) bidang pengadaan barang/jasa.</p>
    </div>
    <a href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-9">
        Daftar Sertifikat Terbit
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kode Standar</th>
                        <th>Nama Sertifikasi Kompetensi</th>
                        <th>Deskripsi Cakupan Unit</th>
                        <th class="text-center">Sertifikat Terbit</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($types)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-24 text-secondary-light">Belum ada standar sertifikat yang terdaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($types as $t): ?>
                            <tr>
                                <td class="text-center text-secondary-light"><?= $no++ ?></td>
                                <td>
                                    <span class="text-primary-600 fw-bold font-monospace text-xs"><?= esc($t['key']) ?></span>
                                </td>
                                <td>
                                    <h6 class="text-sm fw-semibold mb-0 text-primary-light"><?= esc($t['name']) ?></h6>
                                </td>
                                <td>
                                    <span class="text-xs text-secondary-light"><?= esc($t['description']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-50 text-primary-600 px-10 py-6 radius-6 text-xs fw-bold">
                                        <?= (int) ($t['certificate_count'] ?? 0) ?> Dokumen
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', [
                                        'status' => $t['is_active'] ? 'active' : 'inactive',
                                    ]) ?>
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

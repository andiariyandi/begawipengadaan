<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/intelligence/_nav_intelligence', ['activeSub' => 'histories']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Riwayat Penugasan JF PPBJ') ?></h6>
        <p class="text-secondary-light mb-0">Jejak kronologis pengangkatan, mutasi unit kerja, dan kenaikan jenjang jabatan fungsional pengadaan.</p>
    </div>
    <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-9">
        Daftar Personel JF
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Personel &amp; NIP</th>
                        <th>Jenjang Jabatan</th>
                        <th>Unit Penugasan</th>
                        <th>Terhitung Mulai (TMT)</th>
                        <th>Jenis Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($histories)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-24 text-secondary-light">Belum ada riwayat penugasan tercatat.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($histories as $h): ?>
                            <tr>
                                <td class="text-center text-secondary-light"><?= $no++ ?></td>
                                <td>
                                    <h6 class="text-sm fw-semibold mb-2 text-primary-light"><?= esc($h['name']) ?></h6>
                                    <span class="text-xs text-secondary-light font-monospace">NIP: <?= esc($h['nip'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-50 text-primary-600 px-8 py-4 radius-4 text-xs fw-semibold">
                                        <?= esc($h['level_name'] ?: 'JF PPBJ') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-sm fw-medium text-primary-light"><?= esc($h['unit_name'] ?: 'Pemerintah Provinsi Lampung') ?></span>
                                </td>
                                <td>
                                    <span class="text-sm text-secondary-light">
                                        <?= ! empty($h['effective_at']) ? date('d F Y', strtotime($h['effective_at'])) : '-' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-100 text-primary-light px-8 py-4 radius-4 text-xs font-monospace">
                                        <?= esc($h['change_type'] ?: 'appointment') ?>
                                    </span>
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

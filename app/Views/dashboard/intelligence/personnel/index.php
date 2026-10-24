<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/intelligence/_nav_intelligence', ['activeSub' => 'personnel']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'SDM JF PPBJ') ?></h6>
        <p class="text-secondary-light mb-0">Direktori resmi aparatur Jabatan Fungsional Pengelola Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.</p>
    </div>
    <div class="d-flex flex-wrap gap-12">
        <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj/tambah') ?>" class="btn btn-primary-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-6"><iconify-icon icon="solar:add-circle-bold"></iconify-icon> Tambah Personel</a>
        <a href="<?= site_url('dashboard/intelligence/jenjang-jf-ppbj') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-9">
            Jenjang Jabatan
        </a>
        <a href="<?= site_url('dashboard/intelligence/riwayat-jf-ppbj') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-9">
            Riwayat Penugasan
        </a>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card mb-24">
    <div class="card-body p-20">
        <form method="get" action="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>" class="row gy-3 align-items-center">
            <div class="col-md-3">
                <select name="level_id" class="form-select form-select-sm radius-8" onchange="this.form.submit()">
                    <option value="all">Semua Jenjang Jabatan</option>
                    <?php foreach (($levels ?? []) as $lvl): ?>
                        <option value="<?= $lvl['id'] ?>" <?= (($filters['level_id'] ?? '') == $lvl['id']) ? 'selected' : '' ?>>
                            <?= esc($lvl['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="opd_id" class="form-select form-select-sm radius-8" onchange="this.form.submit()">
                    <option value="all">Semua OPD / Unit Kerja</option>
                    <?php foreach (($opds ?? []) as $opd): ?>
                        <option value="<?= $opd['id'] ?>" <?= (($filters['organization_unit_id'] ?? '') == $opd['id']) ? 'selected' : '' ?>>
                            <?= esc($opd['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <input type="search" name="q" value="<?= esc($filters['q'] ?? '') ?>" class="form-control radius-8" placeholder="Cari nama, NIP, atau email...">
                    <button class="btn btn-primary-600 px-12" type="submit">
                        <iconify-icon icon="solar:magnifer-outline"></iconify-icon>
                    </button>
                </div>
            </div>
            <div class="col-md-2 text-md-end">
                <?php if (! empty($filters['level_id']) || ! empty($filters['organization_unit_id']) || ! empty($filters['q'])): ?>
                    <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6">
                        Reset Filter
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-body p-24">
        <div class="d-flex justify-content-between align-items-center mb-16">
            <h6 class="text-lg mb-0">Daftar Personel JF PPBJ</h6>
            <span class="text-sm text-secondary-light">Ditemukan <?= count($personnelList ?? []) ?> personel</span>
        </div>

        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Personel &amp; NIP</th>
                        <th>OPD / Unit Penugasan</th>
                        <th>Jenjang JF</th>
                        <th>Sertifikat Kompetensi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($personnelList)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-40">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="w-56-px h-56-px bg-neutral-100 text-secondary-light rounded-circle d-inline-flex align-items-center justify-content-center mb-12">
                                        <iconify-icon icon="solar:user-cross-broken" class="text-2xl"></iconify-icon>
                                    </span>
                                    <h6 class="text-md fw-semibold mb-4">Tidak Ada Data Personel</h6>
                                    <p class="text-secondary-light text-sm mb-0">Tidak ditemukan personel JF PPBJ yang memenuhi kriteria pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($personnelList as $p): ?>
                            <tr>
                                <td class="text-center text-secondary-light"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-12">
                                        <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center fw-semibold text-sm">
                                            <?= esc(strtoupper(substr($p['name_snapshot'] ?: 'P', 0, 2))) ?>
                                        </span>
                                        <div>
                                            <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj/' . $p['id']) ?>" class="fw-semibold text-primary-light">
                                                <?= esc($p['name_snapshot']) ?>
                                            </a>
                                            <span class="text-xs text-secondary-light d-block font-monospace">NIP: <?= esc($p['employee_identifier'] ?: '-') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-primary-light"><?= esc($p['organization_unit_name'] ?: 'Pemerintah Provinsi Lampung') ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-50 text-primary-600 px-10 py-6 radius-6 text-xs fw-semibold">
                                        <?= esc($p['level_name'] ?: 'JF PPBJ Ahli Pertama') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-sm fw-semibold text-primary-light d-inline-flex align-items-center gap-6">
                                        <iconify-icon icon="solar:diploma-verified-bold" class="text-success-main text-md"></iconify-icon>
                                        <?= (int) ($p['certificates_count'] ?? 0) ?> Dokumen
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', [
                                        'status' => $p['status'],
                                    ]) ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-12 py-4">
                                        Detail
                                    </a>
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

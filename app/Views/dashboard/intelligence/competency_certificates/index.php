<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/intelligence/_nav_intelligence', ['activeSub' => 'certificates']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Sertifikat Kompetensi PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Direktori sertifikasi keahlian pengadaan barang/jasa resmi yang diakui secara nasional.</p>
    </div>
    <div class="d-flex flex-wrap gap-12">
        <a href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi/tambah') ?>" class="btn btn-primary-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-6"><iconify-icon icon="solar:add-circle-bold"></iconify-icon> Catat Sertifikat</a>
        <a href="<?= site_url('dashboard/intelligence/jenis-sertifikat-kompetensi') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-9">
            Standar Jenis Sertifikat
        </a>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card mb-24">
    <div class="card-body p-20">
        <form method="get" action="<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>" class="row gy-3 align-items-center">
            <div class="col-md-3">
                <select name="type_id" class="form-select form-select-sm radius-8" onchange="this.form.submit()">
                    <option value="all">Semua Jenis Sertifikat</option>
                    <?php foreach (($types ?? []) as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= (($filters['type_id'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                            <?= esc($t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm radius-8" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <option value="active" <?= (($filters['status'] ?? '') === 'active') ? 'selected' : '' ?>>Aktif Valid</option>
                    <option value="expired" <?= (($filters['status'] ?? '') === 'expired') ? 'selected' : '' ?>>Kedaluwarsa</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <input type="search" name="q" value="<?= esc($filters['q'] ?? '') ?>" class="form-control radius-8" placeholder="Cari nomor sertifikat, nama pemilik, atau OPD...">
                    <button class="btn btn-primary-600 px-12" type="submit">
                        <iconify-icon icon="solar:magnifer-outline"></iconify-icon>
                    </button>
                </div>
            </div>
            <div class="col-md-2 text-md-end">
                <?php if (! empty($filters['type_id']) || ! empty($filters['status']) || ! empty($filters['q'])): ?>
                    <a href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6">
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
            <h6 class="text-lg mb-0">Daftar Sertifikat Terverifikasi</h6>
            <span class="text-sm text-secondary-light">Total <?= count($certificates ?? []) ?> sertifikat terdaftar</span>
        </div>

        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Nomor Sertifikat</th>
                        <th>Pemilik &amp; NIP</th>
                        <th>Jenis Sertifikat</th>
                        <th>Unit Penugasan</th>
                        <th>Masa Berlaku</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($certificates)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-40">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="w-56-px h-56-px bg-neutral-100 text-secondary-light rounded-circle d-inline-flex align-items-center justify-content-center mb-12">
                                        <iconify-icon icon="solar:diploma-verified-outline" class="text-2xl"></iconify-icon>
                                    </span>
                                    <h6 class="text-md fw-semibold mb-4">Tidak Ada Sertifikat</h6>
                                    <p class="text-secondary-light text-sm mb-0">Tidak ditemukan sertifikat kompetensi yang memenuhi kriteria pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($certificates as $cert): ?>
                            <tr>
                                <td>
                                    <span class="text-primary-600 fw-bold font-monospace text-xs"><?= esc($cert['certificate_number']) ?></span>
                                </td>
                                <td>
                                    <h6 class="text-sm fw-semibold mb-2 text-primary-light"><?= esc($cert['owner_name']) ?></h6>
                                    <span class="text-xs text-secondary-light font-monospace">NIP: <?= esc($cert['owner_nip'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <span class="text-sm fw-medium text-primary-light"><?= esc($cert['certificate_type_name']) ?></span>
                                </td>
                                <td>
                                    <span class="text-sm text-secondary-light"><?= esc($cert['organization_unit_name'] ?: 'Pemerintah Provinsi Lampung') ?></span>
                                </td>
                                <td>
                                    <span class="text-xs text-secondary-light"><?= $cert['validity_label'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', [
                                        'status' => $cert['computed_status'],
                                        'label'  => $cert['status_label'],
                                    ]) ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi/' . $cert['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-12 py-4">
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

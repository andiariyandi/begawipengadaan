<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Tambah OPD atau Unit Kerja') ?></h6>
        <p class="text-secondary-light mb-0">Daftarkan satuan kerja atau perangkat daerah baru ke dalam pemetaan Government Intelligence.</p>
    </div>
    <a href="<?= site_url('dashboard/intelligence/opd-unit') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-6">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon> Kembali ke Daftar OPD
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/intelligence/opd-unit/tambah') ?>">
            <?= csrf_field() ?>

            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kode Singkatan OPD <span class="text-danger-main">*</span></label>
                    <input type="text" name="code" class="form-control radius-8 font-monospace" placeholder="Contoh: DISKOMINFO" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Unit Organisasi <span class="text-danger-main">*</span></label>
                    <select name="type" class="form-select radius-8" required>
                        <option value="Dinas">Dinas</option>
                        <option value="Biro">Biro</option>
                        <option value="Badan">Badan</option>
                        <option value="Sekretariat">Sekretariat</option>
                        <option value="Inspektorat">Inspektorat</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap OPD / Satuan Kerja <span class="text-danger-main">*</span></label>
                    <input type="text" name="name" class="form-control radius-8" placeholder="Contoh: Dinas Komunikasi, Informatika dan Statistik" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Unit Induk (Parent)</label>
                    <select name="parent_id" class="form-select radius-8">
                        <option value="">-- Tidak Ada / Tingkat Provinsi Utama --</option>
                        <?php foreach (($parentUnits ?? []) as $pu): ?>
                            <option value="<?= $pu['id'] ?>"><?= esc($pu['name']) ?> (<?= esc($pu['code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Aktif</label>
                    <input type="text" class="form-control radius-8 bg-neutral-100" value="Aktif (Default)" readonly>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12">
                    <a href="<?= site_url('dashboard/intelligence/opd-unit') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-8 fw-semibold">
                        <iconify-icon icon="solar:disk-bold"></iconify-icon> Simpan Unit Kerja
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

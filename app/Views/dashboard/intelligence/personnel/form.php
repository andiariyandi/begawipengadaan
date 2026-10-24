<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/intelligence/_nav_intelligence', ['activeSub' => 'personnel']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Tambah Personel SDM JF PPBJ') ?></h6>
        <p class="text-secondary-light mb-0">Daftarkan aparatur Jabatan Fungsional Pengelola Pengadaan Barang/Jasa baru ke dalam pangkalan data Provinsi Lampung.</p>
    </div>
    <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-6">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon> Kembali ke Direktori SDM
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/intelligence/sdm-jf-ppbj/tambah') ?>">
            <?= csrf_field() ?>

            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap &amp; Gelar Personel <span class="text-danger-main">*</span></label>
                    <input type="text" name="name_snapshot" class="form-control radius-8" placeholder="Contoh: Dr. Rahmat Hidayat, S.T., M.T." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">NIP (18 Digit) <span class="text-danger-main">*</span></label>
                    <input type="text" name="employee_identifier" class="form-control radius-8 font-monospace" placeholder="Contoh: 198604122011011004" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">OPD / Unit Kerja Penugasan <span class="text-danger-main">*</span></label>
                    <select name="organization_unit_id" class="form-select radius-8" required>
                        <option value="">-- Pilih OPD / Unit Kerja --</option>
                        <?php foreach (($opds ?? []) as $opd): ?>
                            <option value="<?= (int) $opd['id'] ?>"><?= esc($opd['name']) ?> (<?= esc($opd['code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenjang Jabatan Fungsional <span class="text-danger-main">*</span></label>
                    <select name="level_id" class="form-select radius-8" required>
                        <option value="">-- Pilih Jenjang JF PPBJ --</option>
                        <?php foreach (($levels ?? []) as $lvl): ?>
                            <option value="<?= (int) $lvl['id'] ?>"><?= esc($lvl['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tautkan Akun Pengguna (Opsional)</label>
                    <select name="user_id" class="form-select radius-8">
                        <option value="">-- Tidak Ditautkan / Manual --</option>
                        <?php foreach (($asnUsers ?? []) as $u): ?>
                            <option value="<?= (int) $u['id'] ?>"><?= esc($u['name']) ?> (<?= esc($u['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">TMT Efektif Jabatan</label>
                    <input type="date" name="effective_at" class="form-control radius-8" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Pengangkatan / Mutasi</label>
                    <select name="change_type" class="form-select radius-8">
                        <option value="addition">Pengangkatan Pertama / Inpassing</option>
                        <option value="promotion">Kenaikan Jenjang Jabatan</option>
                        <option value="mutation">Mutasi Antar OPD</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12">
                    <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-8 fw-semibold">
                        <iconify-icon icon="solar:disk-bold"></iconify-icon> Simpan Data Personel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

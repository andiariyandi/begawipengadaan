<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/intelligence/_nav_intelligence', ['activeSub' => 'certificates']) ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Catat Sertifikat Kompetensi PBJ') ?></h6>
        <p class="text-secondary-light mb-0">Tambahkan sertifikat kompetensi pengadaan barang/jasa resmi (SKKNI / LKPP) untuk personel JF PPBJ.</p>
    </div>
    <a href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-6">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon> Kembali ke Direktori Sertifikat
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/intelligence/sertifikat-kompetensi/tambah') ?>">
            <?= csrf_field() ?>

            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Personel Pemilik Sertifikat <span class="text-danger-main">*</span></label>
                    <select name="jf_ppbj_record_id" class="form-select radius-8" required>
                        <option value="">-- Pilih Personel JF PPBJ --</option>
                        <?php foreach (($personnelList ?? []) as $p): ?>
                            <option value="<?= (int) $p['id'] ?>"><?= esc($p['name_snapshot']) ?> — NIP: <?= esc($p['employee_identifier']) ?> (<?= esc($p['organization_unit_name'] ?? '') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jenis Sertifikat Kompetensi <span class="text-danger-main">*</span></label>
                    <select name="certificate_type_id" class="form-select radius-8" required>
                        <option value="">-- Pilih Standar Kompetensi --</option>
                        <?php foreach (($types ?? []) as $t): ?>
                            <option value="<?= (int) $t['id'] ?>"><?= esc($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Sertifikat Resmi <span class="text-danger-main">*</span></label>
                    <input type="text" name="certificate_number" class="form-control radius-8 font-monospace" placeholder="Contoh: SKKNI-PBJ/LPG/2026/00125" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Diterbitkan</label>
                    <input type="date" name="issued_at" class="form-control radius-8" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Berlaku Sampai</label>
                    <input type="date" name="expires_at" class="form-control radius-8" value="<?= date('Y-m-d', strtotime('+3 years')) ?>" required>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12">
                    <a href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-8 fw-semibold">
                        <iconify-icon icon="solar:disk-bold"></iconify-icon> Simpan Sertifikat Kompetensi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

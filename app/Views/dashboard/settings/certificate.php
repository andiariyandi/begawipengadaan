<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Sertifikat') ?></h6>
        <p class="text-secondary-light mb-0">Atur nomor, template, validasi, dan masa berlaku sertifikat.</p>
    </div>
    <a href="<?= site_url('dashboard/sistem/pengaturan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/sistem/pengaturan/certificate') ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Prefix Nomor Sertifikat</label>
                    <input type="text" name="sequence_prefix" class="form-control radius-8" value="<?= esc($settings['sequence_prefix'] ?? 'SERT') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Masa Berlaku Default</label>
                    <div class="input-group">
                        <input type="number" name="valid_period_years" class="form-control radius-8" value="<?= esc($settings['valid_period_years'] ?? '3') ?>" required>
                        <span class="input-group-text">Tahun</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pejabat Penanda Tangan</label>
                    <input type="text" name="signature_officer" class="form-control radius-8" value="<?= esc($settings['signature_officer'] ?? 'Kepala Biro Pengadaan Barang dan Jasa Provinsi Lampung') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jabatan Penanda Tangan</label>
                    <input type="text" name="signature_role" class="form-control radius-8" value="<?= esc($settings['signature_role'] ?? 'Pengguna Anggaran / Penanggung Jawab') ?>" required>
                </div>
                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center gap-12">
                        <input type="hidden" name="qr_verification" value="0">
                        <input class="form-check-input" type="checkbox" name="qr_verification" value="1" id="certificateQr" <?= ($settings['qr_verification'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="certificateQr">Tambahkan QR code verifikasi publik (SHA-256 token) pada setiap sertifikat</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center gap-12">
                        <input type="hidden" name="auto_numbering" value="0">
                        <input class="form-check-input" type="checkbox" name="auto_numbering" value="1" id="autoNumbering" <?= ($settings['auto_numbering'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="autoNumbering">Gunakan penomoran otomatis sekuensial (SERT/{YEAR}/{NNNNN})</label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/sistem/pengaturan-sertifikat') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Reset</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

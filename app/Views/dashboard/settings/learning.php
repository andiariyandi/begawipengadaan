<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pengaturan Pembelajaran') ?></h6>
        <p class="text-secondary-light mb-0">Atur parameter standar pelaksanaan kelas dan evaluasi pembelajaran.</p>
    </div>
    <a href="<?= site_url('dashboard/pengaturan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= site_url('dashboard/pengaturan/learning') ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nilai Kelulusan Default</label>
                    <div class="input-group">
                        <input type="number" step="0.1" name="passing_grade_default" class="form-control radius-8" value="<?= esc($settings['passing_grade_default'] ?? '70.00') ?>" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Bobot Quiz Default</label>
                    <div class="input-group">
                        <input type="number" step="0.1" name="quiz_weight_default" class="form-control radius-8" value="<?= esc($settings['quiz_weight_default'] ?? '30.00') ?>" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Bobot Post-Test Default</label>
                    <div class="input-group">
                        <input type="number" step="0.1" name="post_test_weight_default" class="form-control radius-8" value="<?= esc($settings['post_test_weight_default'] ?? '70.00') ?>" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Maksimum Percobaan Quiz / Evaluasi</label>
                    <input type="number" name="max_quiz_attempts" class="form-control radius-8" value="<?= esc($settings['max_quiz_attempts'] ?? '3') ?>" required>
                </div>
                <div class="col-12">
                    <div class="form-switch switch-primary d-flex align-items-center gap-12">
                        <input type="hidden" name="auto_approval" value="0">
                        <input class="form-check-input" type="checkbox" name="auto_approval" value="1" id="autoCertificate" <?= ($settings['auto_approval'] ?? '1') == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="autoCertificate">Kirim approval sertifikat secara otomatis setelah peserta memenuhi syarat kelulusan</label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/pengaturan/learning') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Reset</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

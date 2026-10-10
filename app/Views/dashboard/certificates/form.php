<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
    // Edit-only — tidak ada create manual lagi (lihat CertificateController).
    $c      = $certificate ?? [];
    $errors = session()->getFlashdata('errors') ?? [];
    $old    = static function (string $key, $default = '') {
        $val = old($key);
        return $val !== null ? $val : $default;
    };
    $allowedStatusKeys = ['pending', 'issued', 'revoked', 'expired'];
    $statusOptions = [
        'pending' => 'Belum Diterbitkan (system)',
        'issued'  => 'Diterbitkan',
        'revoked' => 'Dicabut',
        'expired' => 'Kedaluwarsa',
    ];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Ubah Sertifikat') ?></h6>
        <p class="text-secondary-light mb-0">
            Perbarui data sertifikat. Nomor sertifikat dan token verifikasi tidak dapat diubah dari halaman ini.
        </p>
    </div>
    <a href="<?= site_url('dashboard/sertifikat') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (! empty($errors)): ?>
    <div class="alert alert-danger radius-8 mb-16">
        <ul class="mb-0">
            <?php foreach ($errors as $msg): ?>
                <li><?= esc($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= esc(site_url('dashboard/sertifikat/' . ($certificate['id'] ?? 0) . '/ubah')) ?>" novalidate>
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pemilik (Peserta)</label>
                    <input type="text" class="form-control radius-8" readonly disabled
                           value="<?= esc($c['user_name'] ?? '—') ?> — <?= esc($c['user_email'] ?? '') ?>">
                    <small class="text-secondary-light">Pemilik tidak dapat diubah.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Sumber</label>
                    <input type="text" class="form-control radius-8" readonly disabled
                           value="<?= esc($c['source_label'] ?? '—') ?> (<?= esc($c['source_type'] ?? '—') ?>)">
                    <small class="text-secondary-light">Sumber sertifikat dikunci setelah diterbitkan.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Sertifikat</label>
                    <input type="text" class="form-control radius-8" readonly disabled value="<?= esc($c['certificate_number'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Template</label>
                    <input type="text" class="form-control radius-8" readonly disabled value="<?= esc($c['template_name'] ?? '—') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                    <select name="status" class="form-select radius-8" required>
                        <?php foreach ($statusOptions as $key => $label): ?>
                            <?php if (! in_array($key, $allowedStatusKeys, true)) { continue; } ?>
                            <option value="<?= esc($key) ?>" <?= $old('status', $c['status'] ?? 'issued') === $key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Terbit</label>
                    <input type="datetime-local" name="issued_at" class="form-control radius-8"
                           value="<?= esc($old('issued_at', ! empty($c['issued_at']) ? date('Y-m-d\TH:i', strtotime($c['issued_at'])) : date('Y-m-d\TH:i'))) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Kedaluwarsa</label>
                    <input type="datetime-local" name="expires_at" class="form-control radius-8"
                           value="<?= esc($old('expires_at', ! empty($c['expires_at']) ? date('Y-m-d\TH:i', strtotime($c['expires_at'])) : '')) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alasan Pencabutan / Catatan</label>
                    <textarea name="revocation_reason" class="form-control radius-8" rows="3"
                              placeholder="Wajib diisi jika status diubah ke 'Dicabut'."><?= esc($old('revocation_reason', $c['revocation_reason'] ?? '')) ?></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/sertifikat') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Perbarui Sertifikat</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

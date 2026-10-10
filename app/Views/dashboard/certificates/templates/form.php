<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
    $isCreate = ($formMode ?? 'create') === 'create';
    $t        = $template ?? [];
    $errors   = session()->getFlashdata('errors') ?? [];
    $payload  = $t['template_payload'] ?? ['body_html'=>'','css'=>'','issuer_name'=>'BegawiPBJ','qr_position'=>'right'];
    if (!is_array($payload)) {
        $decoded = json_decode((string) $payload, true);
        $payload = is_array($decoded) ? $decoded : ['body_html'=>'','css'=>'','issuer_name'=>'BegawiPBJ','qr_position'=>'right'];
    }
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Template') ?></h6>
        <p class="text-secondary-light mb-0">
            <?= $isCreate ? 'Tambah template baru. Hanya template aktif yang dapat dipakai.' : 'Ubah template sertifikat.' ?>
        </p>
    </div>
    <a href="<?= site_url('dashboard/sertifikat/template') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-24">
        <form method="post"
              action="<?= esc($isCreate
                  ? site_url('dashboard/sertifikat/template/simpan')
                  : site_url('dashboard/sertifikat/template/' . (int)($t['id'] ?? 0) . '/ubah')) ?>"
              novalidate>
            <?= csrf_field() ?>

            <div class="row gy-20">
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Template</label>
                    <input type="text" name="name" class="form-control radius-8" required maxlength="255"
                           value="<?= esc(old('name', $t['name'] ?? '')) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tipe Sumber</label>
                    <select name="source_type" class="form-select radius-8" required>
                        <?php
                            $currentType = old('source_type', $t['source_type'] ?? 'learning');
                        ?>
                        <option value="learning" <?= $currentType === 'learning' ? 'selected' : '' ?>>Learning</option>
                        <option value="event"    <?= $currentType === 'event'    ? 'selected' : '' ?>>Event</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Penerbit (Issuer Name)</label>
                    <input type="text" name="payload[issuer_name]" class="form-control radius-8"
                           value="<?= esc(old('payload.issuer_name', $payload['issuer_name'] ?? '')) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Posisi QR</label>
                    <select name="payload[qr_position]" class="form-select radius-8">
                        <?php
                            $currentQrPos = old('payload.qr_position', $payload['qr_position'] ?? 'right');
                        ?>
                        <option value="right"  <?= $currentQrPos === 'right'  ? 'selected' : '' ?>>Kanan (default)</option>
                        <option value="bottom" <?= $currentQrPos === 'bottom' ? 'selected' : '' ?>>Bawah</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Body HTML (opsional — gunakan placeholder seperti <code>{{participant_name}}</code>)
                    </label>
                    <textarea name="payload[body_html]" class="form-control radius-8" rows="8"
                              placeholder="<h1>SERTIFIKAT</h1><p>Diberikan kepada: {{participant_name}}</p>..."><?= esc(old('payload.body_html', $payload['body_html'] ?? '')) ?></textarea>
                    <small class="text-secondary-light">
                        Placeholder yang didukung: <code>{{participant_name}}</code>,
                        <code>{{certificate_number}}</code>, <code>{{certificate_title}}</code>,
                        <code>{{issued_date}}</code>, <code>{{issuer_name}}</code>,
                        <code>{{verification_url}}</code>, <code>{{source_label}}</code>.
                    </small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">CSS Tambahan (opsional)</label>
                    <textarea name="payload[css]" class="form-control radius-8" rows="4"
                              placeholder="body { ... }"><?= esc(old('payload.css', $payload['css'] ?? '')) ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                    <select name="is_active" class="form-select radius-8">
                        <?php
                            $currentActive = (string) (int) old('is_active', $t['is_active'] ?? 1);
                        ?>
                        <option value="1" <?= $currentActive === '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= $currentActive === '0' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12">
                    <a href="<?= site_url('dashboard/sertifikat/template') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">
                        <?= $isCreate ? 'Simpan Template' : 'Perbarui Template' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

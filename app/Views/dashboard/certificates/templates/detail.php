<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
    $t = $template ?? [];
    $isActive = (int) ($t['is_active'] ?? 0) === 1;
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Template</h6>
        <p class="text-secondary-light mb-0">ID Template: #<?= esc($templateId ?? ($t['id'] ?? '—')) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/sertifikat/template') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
        <a href="<?= site_url('dashboard/sertifikat/template/' . (int)($t['id'] ?? 0) . '/ubah') ?>"
           class="btn btn-primary-600 radius-8 px-20 py-11">Ubah</a>
        <?php if ($isActive): ?>
            <form method="post"
                  action="<?= site_url('dashboard/sertifikat/template/' . (int)($t['id'] ?? 0) . '/nonaktifkan') ?>"
                  class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-warning-600 radius-8 px-20 py-11">Nonaktifkan</button>
            </form>
        <?php else: ?>
            <form method="post"
                  action="<?= site_url('dashboard/sertifikat/template/' . (int)($t['id'] ?? 0) . '/aktifkan') ?>"
                  class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-success-600 radius-8 px-20 py-11">Aktifkan</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-24">
                <div class="row gy-20">
                    <div class="col-md-8">
                        <p class="text-secondary-light mb-4">Nama</p>
                        <h6 class="mb-0"><?= esc($t['name'] ?? '—') ?></h6>
                    </div>
                    <div class="col-md-4">
                        <p class="text-secondary-light mb-4">Tipe Sumber</p>
                        <h6 class="mb-0"><?= esc($t['source_type'] ?? '—') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Status</p>
                        <?= view('dashboard/components/status_badge', ['status' => $isActive ? 'active' : 'inactive']) ?>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Diperbarui</p>
                        <h6 class="mb-0">
                            <?= ! empty($t['updated_at']) ? esc(date('d M Y H:i', strtotime($t['updated_at']))) : '—' ?>
                        </h6>
                    </div>
                </div>

                <hr class="my-24">

                <h6 class="mb-12 fw-semibold">Template Payload (JSON)</h6>
                <pre class="radius-8 p-16" style="background:#0f172a;color:#e2e8f0;font-size:12px;max-height:340px;overflow:auto;"><?= esc($t['template_payload_pretty'] ?? $t['template_payload'] ?? '') ?></pre>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Informasi</h6>
                <p class="text-secondary-light text-sm mb-12">
                    Template yang <strong>aktif</strong> akan digunakan oleh
                    <code>CertificateGenerator</code> ketika approval sertifikat
                    disetujui. Tipe sumber <code>learning</code> cocok untuk
                    sertifikat kelulusan kelas, <code>event</code> cocok untuk
                    sertifikat kehadiran event.
                </p>
                <p class="text-secondary-light text-sm mb-12">
                    Background image dapat dipasang dengan menyetel
                    <code>background_file_id</code> ke asset gambar pada
                    tabel <code>file_assets</code>.
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

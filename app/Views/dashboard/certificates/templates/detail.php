<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$t        = $template ?? [];
$isActive = (int) ($t['is_active'] ?? 0) === 1;
$bgAsset  = $backgroundAsset ?? null;
$payload  = $t['template_payload'] ?? [];
if (! is_array($payload)) {
    $decoded = json_decode((string) $payload, true);
    $payload = is_array($decoded) ? $decoded : [];
}
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($t['name'] ?? 'Detail Template') ?></h6>
        <p class="text-secondary-light mb-0">ID Template: #<?= esc($templateId ?? ($t['id'] ?? '—')) ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= site_url('dashboard/sertifikat/template') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11">
            <iconify-icon icon="solar:arrow-left-linear" class="me-1"></iconify-icon> Kembali
        </a>
        <a href="<?= site_url('dashboard/sertifikat/template/' . (int)($t['id'] ?? 0) . '/preview') ?>" target="_blank"
           class="btn btn-outline-primary-600 radius-8 px-16 py-11 d-flex align-items-center gap-1">
            <iconify-icon icon="solar:eye-bold"></iconify-icon> Pratinjau HTML
        </a>
        <a href="<?= site_url('dashboard/sertifikat/template/' . (int)($t['id'] ?? 0) . '/preview-pdf') ?>" target="_blank"
           class="btn btn-outline-danger-600 radius-8 px-16 py-11 d-flex align-items-center gap-1">
            <iconify-icon icon="solar:file-download-bold"></iconify-icon> Unduh Sampel PDF
        </a>
        <a href="<?= site_url('dashboard/sertifikat/template/' . (int)($t['id'] ?? 0) . '/ubah') ?>"
           class="btn btn-primary-600 radius-8 px-20 py-11">
            <iconify-icon icon="solar:pen-bold" class="me-1"></iconify-icon> Ubah
        </a>
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
        <!-- Card Detail Informasi -->
        <div class="card mb-24">
            <div class="card-header border-bottom py-16 px-24">
                <h6 class="text-lg fw-semibold mb-0">Informasi Template</h6>
            </div>
            <div class="card-body p-24">
                <div class="row gy-20">
                    <div class="col-md-8">
                        <p class="text-secondary-light text-xs mb-4">Nama Template</p>
                        <h6 class="mb-0 text-md fw-semibold"><?= esc($t['name'] ?? '—') ?></h6>
                    </div>
                    <div class="col-md-4">
                        <p class="text-secondary-light text-xs mb-4">Tipe Sumber</p>
                        <span class="badge bg-primary-50 text-primary-600 px-10 py-5 radius-4 text-xs fw-semibold">
                            <?= esc($t['source_type'] === 'learning' ? 'Arena Belajar (AB)' : 'Event / Agenda (EV)') ?>
                        </span>
                    </div>
                    <div class="col-md-4">
                        <p class="text-secondary-light text-xs mb-4">Status Penggunaan</p>
                        <?= view('dashboard/components/status_badge', ['status' => $isActive ? 'active' : 'inactive']) ?>
                    </div>
                    <div class="col-md-4">
                        <p class="text-secondary-light text-xs mb-4">Format Penomoran</p>
                        <code class="text-xs text-primary-600 fw-bold">SERT-{num}/<?= $t['source_type'] === 'learning' ? 'AB-{id}' : '{tipe}-{id}' ?>/05/{thn}</code>
                    </div>
                    <div class="col-md-4">
                        <p class="text-secondary-light text-xs mb-4">Terakhir Diperbarui</p>
                        <p class="mb-0 text-sm fw-medium">
                            <?= ! empty($t['updated_at']) ? esc(date('d M Y H:i', strtotime($t['updated_at']))) : '—' ?>
                        </p>
                    </div>
                </div>

                <hr class="my-20">

                <div class="row gy-16">
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Instansi Penerbit</p>
                        <p class="mb-0 text-sm fw-semibold text-primary-light">
                            <?= esc($payload['issuer_name'] ?? 'Pemerintah Provinsi Lampung — Biro PBJ') ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Pejabat Penanda Tangan</p>
                        <p class="mb-0 text-sm fw-semibold text-primary-light">
                            <?= esc($payload['signer_name'] ?? '—') ?>
                        </p>
                        <small class="text-secondary-light">
                            <?= esc($payload['signer_position'] ?? '') ?> <?= ! empty($payload['signer_nip']) ? ('&bull; NIP: ' . esc($payload['signer_nip'])) : '' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Cakupan & Hierarki Template (Anti-Tumpang-Tindih) -->
        <div class="card mb-24">
            <div class="card-header border-bottom py-16 px-24 d-flex align-items-center justify-content-between">
                <h6 class="text-lg fw-semibold mb-0">Cakupan &amp; Aturan Hierarki</h6>
                <span class="badge bg-primary-100 text-primary-700 px-10 py-5 radius-6 text-xs fw-bold">
                    <?= esc($scopeDetails['entity_type'] ?? 'Standar') ?>
                </span>
            </div>
            <div class="card-body p-24">
                <div class="row gy-16">
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Tingkat Sasaran (Target Scope)</p>
                        <div class="mt-1">
                            <?php $sc = $scopeDetails['scope'] ?? 'global'; ?>
                            <?php if ($sc === 'program'): ?>
                                <span class="badge bg-purple-50 text-purple-700 border border-purple-200 px-10 py-5 radius-6 fw-bold">Tingkat Program Pembelajaran</span>
                            <?php elseif ($sc === 'course'): ?>
                                <span class="badge bg-info-50 text-info-700 border border-info-200 px-10 py-5 radius-6 fw-bold">Tingkat Course PBJ</span>
                            <?php elseif ($sc === 'class'): ?>
                                <span class="badge bg-success-50 text-success-700 border border-success-200 px-10 py-5 radius-6 fw-bold">Khusus 1 Kelas Pembelajaran</span>
                            <?php elseif ($sc === 'event'): ?>
                                <span class="badge bg-warning-50 text-warning-700 border border-warning-200 px-10 py-5 radius-6 fw-bold">Khusus 1 Event / Agenda (1:1)</span>
                            <?php else: ?>
                                <span class="badge bg-neutral-100 text-neutral-700 border border-neutral-300 px-10 py-5 radius-6 fw-bold">Standar / Global (Arena Belajar)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-4">Entitas Terkait</p>
                        <p class="mb-0 text-sm fw-bold text-neutral-900">
                            <?= esc($scopeDetails['title'] ?? 'Semua Pembelajaran') ?>
                        </p>
                    </div>
                    <div class="col-12">
                        <div class="p-12 radius-8 bg-neutral-50 border border-neutral-200 d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:shield-check-bold" class="text-primary-600 text-lg flex-shrink-0 mt-1"></iconify-icon>
                            <div class="text-xs text-neutral-700">
                                <strong class="d-block mb-1 text-neutral-900">Kebijakan Anti-Tumpang-Tindih &amp; Relasi:</strong>
                                <?= esc($scopeDetails['lock_info'] ?? 'Template ini aktif sesuai aturan hierarki sistem.') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Pratinjau Desain Background -->
        <?php if (! empty($payload['background_path']) || ! empty($bgAsset)): ?>
            <div class="card mb-24">
                <div class="card-header border-bottom py-16 px-24 d-flex align-items-center justify-content-between">
                    <h6 class="text-lg fw-semibold mb-0">File Desain Background Terpasang</h6>
                    <?php if (! empty($payload['pdf_placeholders']) && is_array($payload['pdf_placeholders'])): ?>
                        <span class="badge bg-success-50 text-success-700 border border-success-200 px-10 py-5 radius-6 text-xs fw-bold">
                            <?= count($payload['pdf_placeholders']) ?> Placeholder PDF Aktif
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-24">
                    <?php
                    $bgRel = $payload['background_path'] ?? ($bgAsset['path'] ?? '');
                    $bgUrl = ! empty($t['id'])
                        ? site_url('dashboard/sertifikat/template/' . (int) $t['id'] . '/background-file')
                        : base_url(ltrim($bgRel, '/'));
                    $ext   = strtolower(pathinfo($bgRel, PATHINFO_EXTENSION));
                    ?>
                    <div class="p-16 radius-8 bg-neutral-50 border text-center">
                        <?php if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)): ?>
                            <img src="<?= esc($bgUrl) ?>" alt="Background Template" class="img-fluid radius-8 shadow-sm" style="max-height: 380px;">
                        <?php else: ?>
                            <div class="py-24 text-secondary-light">
                                <iconify-icon icon="solar:file-check-bold" class="text-5xl text-primary-600 mb-2"></iconify-icon>
                                <p class="mb-1 fw-bold text-neutral-800">Template Vektor PDF: <?= esc(basename($bgRel)) ?></p>
                                <span class="badge bg-primary-50 text-primary-700 border border-primary-200 px-12 py-6 radius-4 text-xs fw-semibold">
                                    Teks <code>{{placeholder}}</code> di dalam PDF otomatis diganti dengan data peserta saat cetak
                                </span>
                                <div class="mt-12">
                                    <a href="<?= esc($bgUrl) ?>" target="_blank" class="btn btn-xs btn-outline-primary-600 radius-6 px-12 py-6">
                                        <iconify-icon icon="solar:eye-linear" class="me-1"></iconify-icon> Lihat File PDF Asli
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (! empty($payload['pdf_placeholders']) && is_array($payload['pdf_placeholders'])): ?>
                        <div class="mt-16">
                            <p class="text-xs fw-bold text-neutral-700 mb-8">Pemetaan Placeholder di Dalam PDF:</p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0 text-xs">
                                    <thead class="bg-neutral-50">
                                        <tr>
                                            <th>Tag Placeholder</th>
                                            <th>Posisi (X%, Y%)</th>
                                            <th>Gaya Teks / Ukuran</th>
                                            <th>Perataan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payload['pdf_placeholders'] as $ph): ?>
                                            <?php if (! is_array($ph) || empty($ph['tag'])) continue; ?>
                                            <tr>
                                                <td><code class="text-primary-600 fw-bold"><?= esc($ph['tag']) ?></code></td>
                                                <td><?= esc((string) ($ph['x_pct'] ?? 50)) ?>%, <?= esc((string) ($ph['y_pct'] ?? 50)) ?>%</td>
                                                <td>
                                                    <?php if (($ph['key'] ?? '') === 'qr_code'): ?>
                                                        QR <?= esc((string) ($ph['qr_size_px'] ?? 88)) ?> px
                                                    <?php else: ?>
                                                        <?= esc((string) ($ph['font_size'] ?? 16)) ?> pt &bull; <?= esc((string) ($ph['font_weight'] ?? 'bold')) ?>
                                                        <span class="d-inline-block rounded-circle ms-1 align-middle border" style="width:10px;height:10px;background:<?= esc($ph['color'] ?? '#0f172a') ?>;"></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-uppercase"><?= esc((string) ($ph['align'] ?? 'center')) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Card Template Payload JSON -->
        <div class="card">
            <div class="card-header border-bottom py-16 px-24">
                <h6 class="text-lg fw-semibold mb-0">Template Payload (Konfigurasi Mentah JSON)</h6>
            </div>
            <div class="card-body p-24">
                <pre class="radius-8 p-16 mb-0 font-monospace" style="background:#0f172a;color:#e2e8f0;font-size:12px;max-height:300px;overflow:auto;"><?= esc($t['template_payload_pretty'] ?? $t['template_payload'] ?? '') ?></pre>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Panduan Ringkas -->
    <div class="col-xl-4">
        <div class="card mb-24">
            <div class="card-header border-bottom py-16 px-20">
                <h6 class="text-md fw-bold mb-0">Pratinjau Cepat</h6>
            </div>
            <div class="card-body p-20">
                <p class="text-secondary-light text-sm mb-16">
                    Lihat hasil render mockup sertifikat sebelum diterbitkan ke peserta pembelajaran atau event.
                </p>
                <div class="d-grid gap-2">
                    <a href="<?= site_url('dashboard/sertifikat/template/' . (int)($t['id'] ?? 0) . '/preview') ?>" target="_blank"
                       class="btn btn-primary-600 radius-8 py-10 text-sm fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:eye-bold" class="text-base"></iconify-icon>
                        Buka Pratinjau HTML
                    </a>
                    <a href="<?= site_url('dashboard/sertifikat/template/' . (int)($t['id'] ?? 0) . '/preview-pdf') ?>" target="_blank"
                       class="btn btn-outline-danger-600 radius-8 py-10 text-sm fw-semibold d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:file-download-bold" class="text-base"></iconify-icon>
                        Unduh File PDF Cetak
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom py-16 px-20">
                <h6 class="text-md fw-bold mb-0">Spesifikasi Sertifikat</h6>
            </div>
            <div class="card-body p-20">
                <ul class="list-unstyled text-xs text-secondary-light mb-0 d-flex flex-column gap-12">
                    <li class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-base"></iconify-icon>
                        <span>Format: <strong>A4 Landscape (297 &times; 210 mm)</strong></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-base"></iconify-icon>
                        <span>Rekomendasi Cetak: <strong>3508 &times; 2480 px (300 DPI)</strong></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-base"></iconify-icon>
                        <span>Enkripsi: <strong>QR SHA-256 Verifikasi Publik</strong></span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-success-600 text-base"></iconify-icon>
                        <span>Penomoran: <strong>Sekuensial Otomatis Resmi</strong></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
    $c = $certificate;
    $isIssued = ($c['status'] ?? '') === 'issued';
    $issuedAtFmt = ! empty($c['issued_at'])
        ? date('d F Y H:i', strtotime($c['issued_at']))
        : '—';
    $expiresAtFmt = ! empty($c['expires_at'])
        ? date('d F Y H:i', strtotime($c['expires_at']))
        : '—';
    $revokedAtFmt = ! empty($c['revoked_at'])
        ? date('d F Y H:i', strtotime($c['revoked_at']))
        : '—';
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Sertifikat</h6>
        <p class="text-secondary-light mb-0">ID Sertifikat: #<?= esc($c['id']) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/sertifikat') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
        <?php if ($isIssued): ?>
            <a href="<?= site_url('dashboard/sertifikat/' . $c['id'] . '/unduh') ?>"
               class="btn btn-success-600 radius-8 px-20 py-11">Unduh PDF</a>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/sertifikat/' . $c['id'] . '/ubah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah Sertifikat</a>
    </div>
</div>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (! empty($issuedToken)): ?>
    <div class="alert alert-warning radius-8 mb-16">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
                <h6 class="mb-8 fw-semibold">Token Verifikasi (Klar)</h6>
                <p class="mb-8 text-sm">Simpan token ini sekarang. <strong>Tidak akan ditampilkan lagi.</strong> Token digunakan untuk verifikasi publik sertifikat.</p>
                <code style="user-select: all; word-break: break-all; display: inline-block; padding: 8px 12px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;"><?= esc($issuedToken) ?></code>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="row gy-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body p-24">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-24">
                    <div>
                        <p class="text-secondary-light mb-4">Nomor Sertifikat</p>
                        <h5 class="mb-0 text-primary-600"><?= esc($c['certificate_number']) ?></h5>
                    </div>
                    <?= view('dashboard/components/status_badge', ['status' => $c['status']]) ?>
                </div>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Pemilik Sertifikat</p>
                        <h6 class="mb-0"><?= esc($c['user_name'] ?? '—') ?></h6>
                        <small class="text-secondary-light"><?= esc($c['user_email'] ?? '') ?></small>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Program / Kelas</p>
                        <h6 class="mb-0"><?= esc($c['source_label'] ?? '—') ?></h6>
                        <small class="text-secondary-light">Enrollment #<?= esc($c['source_id']) ?></small>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Tanggal Terbit</p>
                        <h6 class="mb-0"><?= esc($issuedAtFmt) ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Tanggal Kedaluwarsa</p>
                        <h6 class="mb-0"><?= esc($expiresAtFmt) ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Token Verifikasi (Hash)</p>
                        <h6 class="mb-0"><code><?= esc($c['token_preview'] ?? '—') ?>…</code></h6>
                        <small class="text-secondary-light">SHA-256 tersimpan di DB. Token klar tidak ditampilkan di sini.</small>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light mb-4">Template</p>
                        <h6 class="mb-0"><?= esc($c['template_name'] ?? '—') ?></h6>
                    </div>
                    <?php if (! empty($c['revoked_at'])): ?>
                        <div class="col-md-6">
                            <p class="text-secondary-light mb-4">Dicabut Pada</p>
                            <h6 class="mb-0"><?= esc($revokedAtFmt) ?></h6>
                            <small class="text-secondary-light">oleh <?= esc($c['revoked_by_name'] ?? '—') ?></small>
                        </div>
                        <div class="col-12">
                            <p class="text-secondary-light mb-4">Alasan Pencabutan</p>
                            <div class="p-12 radius-8" style="background:#fef2f2; border:1px solid #fecaca;"><?= esc($c['revocation_reason'] ?? '—') ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if ($isIssued): ?>
        <div class="card mt-24">
            <div class="card-body p-24">
                <h6 class="fw-semibold mb-12">Cabut Sertifikat</h6>
                <p class="text-secondary-light mb-16">Pencabutan akan mengubah status menjadi <strong>Dicabut</strong> dan tidak dapat dibatalkan. Token verifikasi publik akan ditolak.</p>
                <form method="post" action="<?= esc(site_url('dashboard/sertifikat/' . $c['id'] . '/cabut')) ?>" onsubmit="return confirm('Yakin ingin mencabut sertifikat ini?');">
                    <?= csrf_field() ?>
                    <div class="row gy-12 align-items-end">
                        <div class="col-md-9">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Alasan Pencabutan</label>
                            <textarea name="revocation_reason" class="form-control radius-8" rows="2" required minlength="5" placeholder="Contoh: Ditemukan pelanggaran integritas data."></textarea>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-danger radius-8 px-20 py-11 w-100">Cabut Sertifikat</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body p-24 text-center">
                <span class="w-128-px h-128-px bg-neutral-100 d-inline-flex align-items-center justify-content-center radius-8 mb-16">
                    <iconify-icon icon="solar:qr-code-outline" class="text-primary-600 text-6xl"></iconify-icon>
                </span>
                <h6 class="mb-8">Verifikasi Publik</h6>
                <p class="text-secondary-light text-sm mb-12">
                    QR Code pada PDF membawa URL token. Halaman publik akan menampilkan:
                    nama peserta, nomor sertifikat, kegiatan, tanggal terbit, dan status.
                </p>
                <p class="text-secondary-light text-sm mb-20">
                    Halaman publik: <code>/verifikasi-sertifikat</code>
                </p>
                <a href="<?= site_url('verifikasi-sertifikat') ?>" target="_blank" class="btn btn-outline-primary-600 w-100 radius-8">
                    Buka Halaman Verifikasi
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
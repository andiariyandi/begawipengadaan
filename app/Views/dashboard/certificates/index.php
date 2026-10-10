<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Sertifikat</h6>
        <p class="text-secondary-light mb-0">Sertifikat terbit otomatis melalui alur eligibility → approval → generator. Tidak ada penerbitan manual.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('dashboard/sertifikat/approval') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
            Approval / Pengajuan
        </a>
        <a href="<?= site_url('dashboard/sertifikat/pengajuan-event') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
            Pengajuan Event
        </a>
    </div>
</div>
<?= $this->include('dashboard/components/filter_bar') ?>
<form method="get" action="<?= site_url('dashboard/sertifikat') ?>" class="card mb-24">
    <div class="card-body p-20">
        <div class="row gy-3 align-items-end">
            <div class="col-xl-4 col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pencarian</label>
                <input type="text" name="keyword" class="form-control radius-8" placeholder="Cari nomor sertifikat, nama, email, atau judul program..." value="<?= esc($keyword ?? '') ?>">
            </div>
            <div class="col-xl-3 col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                <select name="status" class="form-select radius-8">
                    <option value="all" <?= ($statusFilter ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="draft"   <?= ($statusFilter ?? '') === 'draft'   ? 'selected' : '' ?>>Draft</option>
                    <option value="issued"  <?= ($statusFilter ?? '') === 'issued'  ? 'selected' : '' ?>>Diterbitkan</option>
                    <option value="revoked" <?= ($statusFilter ?? '') === 'revoked' ? 'selected' : '' ?>>Dicabut</option>
                    <option value="expired" <?= ($statusFilter ?? '') === 'expired' ? 'selected' : '' ?>>Kedaluwarsa</option>
                </select>
            </div>
            <div class="col-xl-5 col-md-12 d-flex gap-2 justify-content-end">
                <a href="<?= site_url('dashboard/sertifikat') ?>" class="btn btn-outline-neutral-300 radius-8 px-16">Reset</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20">Terapkan Filter</button>
            </div>
        </div>
    </div>
</form>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-8 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success radius-8 mb-16"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Nomor Sertifikat</th>
                        <th>Pemilik</th>
                        <th>Program/Kelas</th>
                        <th>Tanggal Terbit</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($certificates)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary-light py-24">
                            Belum ada sertifikat yang diterbitkan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($certificates as $certificate): ?>
                        <tr>
                            <td>
                                <span class="text-primary-600 fw-semibold"><?= esc($certificate['certificate_number']) ?></span>
                            </td>
                            <td>
                                <h6 class="text-md mb-0 fw-medium"><?= esc($certificate['user_name'] ?? '—') ?></h6>
                                <small class="text-secondary-light"><?= esc($certificate['user_email'] ?? '') ?></small>
                            </td>
                            <td>
                                <?= esc($certificate['source_label'] ?? '—') ?>
                                <br>
                                <small class="text-secondary-light"><?= esc($certificate['template_name'] ?? '—') ?></small>
                            </td>
                            <td>
                                <?= esc($certificate['issued_at'] ? date('d M Y', strtotime($certificate['issued_at'])) : '—') ?>
                            </td>
                            <td class="text-center">
                                <?= view('dashboard/components/status_badge', ['status' => $certificate['status']]) ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('dashboard/sertifikat/' . $certificate['id']) ?>" class="text-primary-600 me-12">Detail</a>
                                <a href="<?= site_url('dashboard/sertifikat/' . $certificate['id'] . '/ubah') ?>" class="text-warning-main">Ubah</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
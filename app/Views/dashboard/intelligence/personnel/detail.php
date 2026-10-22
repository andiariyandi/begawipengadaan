<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Detail Personel SDM JF PPBJ') ?></h6>
        <p class="text-secondary-light mb-0">Rincian profil aparatur fungsional pengadaan dan rekam jejak kompetensi resmi.</p>
    </div>
    <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-6">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon> Kembali ke Direktori
    </a>
</div>

<div class="row gy-4">
    <!-- Left Profile Card -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body p-24 text-center">
                <span class="w-96-px h-96-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center text-3xl fw-bold mb-16">
                    <?= esc(strtoupper(substr($personnel['name_snapshot'] ?: 'P', 0, 2))) ?>
                </span>
                <h5 class="fw-semibold mb-4"><?= esc($personnel['name_snapshot']) ?></h5>
                <p class="text-secondary-light text-sm mb-12 font-monospace">NIP: <?= esc($personnel['employee_identifier'] ?: '-') ?></p>
                
                <div class="mb-20">
                    <span class="badge bg-primary-50 text-primary-600 px-12 py-6 radius-6 text-sm fw-semibold">
                        <?= esc($personnel['level_name'] ?: 'JF PPBJ') ?>
                    </span>
                </div>

                <?= view('dashboard/components/status_badge', [
                    'status' => $personnel['status'],
                ]) ?>

                <hr class="my-20">

                <div class="text-start">
                    <div class="mb-16">
                        <span class="text-xs text-secondary-light d-block uppercase fw-medium">Unit Kerja / OPD</span>
                        <h6 class="text-sm fw-semibold mb-0"><?= esc($personnel['organization_unit_name'] ?: 'Pemerintah Provinsi Lampung') ?></h6>
                    </div>
                    <div class="mb-16">
                        <span class="text-xs text-secondary-light d-block uppercase fw-medium">Jabatan Kedinasan</span>
                        <h6 class="text-sm fw-semibold mb-0"><?= esc($personnel['position_name'] ?: 'Pejabat Fungsional PBJ') ?></h6>
                    </div>
                    <div class="mb-16">
                        <span class="text-xs text-secondary-light d-block uppercase fw-medium">Email Kedinasan</span>
                        <h6 class="text-sm fw-semibold mb-0"><?= esc($personnel['user_email'] ?: '-') ?></h6>
                    </div>
                    <div class="mb-0">
                        <span class="text-xs text-secondary-light d-block uppercase fw-medium">Terhitung Mulai Tanggal (TMT)</span>
                        <h6 class="text-sm fw-semibold mb-0">
                            <?= ! empty($personnel['effective_at']) ? date('d F Y', strtotime($personnel['effective_at'])) : '-' ?>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Content: Certificates & Competencies -->
    <div class="col-xl-8">
        <!-- Certificates List -->
        <div class="card mb-24">
            <div class="card-body p-24">
                <div class="d-flex justify-content-between align-items-center mb-20">
                    <div>
                        <h6 class="text-lg mb-2">Sertifikat Kompetensi Terdaftar</h6>
                        <p class="text-secondary-light text-sm mb-0">Daftar sertifikat keahlian pengadaan barang/jasa yang dimiliki personel.</p>
                    </div>
                    <span class="badge bg-success-focus text-success-main radius-6 px-12 py-6 text-xs fw-semibold">
                        <?= count($personnel['certificates'] ?? []) ?> Sertifikat Aktif
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Jenis Kompetensi</th>
                                <th>Nomor Sertifikat</th>
                                <th>Masa Berlaku</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($personnel['certificates'])): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-24 text-secondary-light">Belum ada sertifikat kompetensi yang tercatat.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($personnel['certificates'] as $cert): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-10">
                                                <iconify-icon icon="solar:diploma-verified-bold" class="text-primary-600 text-lg"></iconify-icon>
                                                <div>
                                                    <h6 class="text-sm fw-semibold mb-0"><?= esc($cert['type_name']) ?></h6>
                                                    <span class="text-xs text-secondary-light"><?= esc($cert['type_description']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-xs font-monospace fw-semibold text-primary-light">
                                                <?= esc($cert['certificate_number']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs text-secondary-light">
                                                <?= date('d/m/Y', strtotime($cert['issued_at'])) ?> &ndash; <?= date('d/m/Y', strtotime($cert['expires_at'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?= view('dashboard/components/status_badge', [
                                                'status' => $cert['status'],
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Career History Card -->
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-4">Riwayat Pengangkatan &amp; Mutasi</h6>
                <p class="text-secondary-light text-sm mb-20">Jejak administratif penempatan jabatan fungsional.</p>

                <div class="p-16 bg-neutral-50 border border-neutral-200 radius-8 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-12">
                        <span class="w-40-px h-40-px bg-primary-50 text-primary-600 radius-8 d-inline-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:user-id-bold" class="text-xl"></iconify-icon>
                        </span>
                        <div>
                            <h6 class="text-sm fw-semibold mb-2">Penetapan Jabatan Pertama</h6>
                            <span class="text-xs text-secondary-light">
                                Terhitung sejak: <?= ! empty($personnel['effective_at']) ? date('d F Y', strtotime($personnel['effective_at'])) : '-' ?> &bull; Jenis: Pengangkatan Pertama
                            </span>
                        </div>
                    </div>
                    <span class="badge bg-success-focus text-success-main radius-6 px-10 py-4 text-xs">Terverifikasi BKD</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Detail OPD / Unit Kerja') ?></h6>
        <p class="text-secondary-light mb-0">Kode Unit: <strong class="text-primary-600 font-monospace"><?= esc($opd['code'] ?: 'OPD') ?></strong></p>
    </div>
    <a href="<?= site_url('dashboard/intelligence/opd-unit') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-6">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon> Kembali ke Daftar OPD
    </a>
</div>

<div class="row gy-4">
    <div class="col-xl-8">
        <!-- Profile Info -->
        <div class="card mb-24">
            <div class="card-body p-24">
                <div class="d-flex align-items-center justify-content-between mb-20">
                    <h5 class="mb-0 fw-semibold"><?= esc($opd['name']) ?></h5>
                    <?= view('dashboard/components/status_badge', [
                        'status' => $opd['is_active'] ? 'active' : 'inactive',
                    ]) ?>
                </div>

                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Kode Singkatan</p>
                        <h6 class="mb-0 text-primary-600 font-monospace"><?= esc($opd['code'] ?: '-') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Tipe Organisasi</p>
                        <h6 class="mb-0"><?= esc($opd['type'] ?: 'Dinas') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Unit Induk</p>
                        <h6 class="mb-0"><?= esc($opd['parent_name'] ?: 'Pemerintah Provinsi Lampung') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Standar Formasi Kebutuhan</p>
                        <h6 class="mb-0 fw-bold"><?= (int) ($opd['required_count'] ?? 15) ?> Orang</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Personnel Table -->
        <div class="card">
            <div class="card-body p-24">
                <div class="d-flex justify-content-between align-items-center mb-16">
                    <div>
                        <h6 class="text-lg mb-2">Personel JF PPBJ Ditugaskan</h6>
                        <p class="text-secondary-light text-sm mb-0">Daftar aparatur fungsional pengadaan yang aktif di unit kerja ini.</p>
                    </div>
                    <span class="badge bg-primary-50 text-primary-600 radius-6 px-12 py-6 text-xs fw-semibold">
                        <?= count($opd['personnel'] ?? []) ?> Personel
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table bordered-table sm-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Nama Personel</th>
                                <th>NIP</th>
                                <th>Jenjang Jabatan</th>
                                <th class="text-center">Sertifikat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($opd['personnel'])): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-24 text-secondary-light">Belum ada personel JF PPBJ yang ditempatkan pada unit ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($opd['personnel'] as $pers): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-primary-light"><?= esc($pers['name_snapshot']) ?></span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-monospace text-secondary-light"><?= esc($pers['employee_identifier']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-50 text-primary-600 px-8 py-4 radius-4 text-xs">
                                                <?= esc($pers['level_name']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center fw-semibold text-success-main">
                                            <?= (int) ($pers['certificates_count'] ?? 0) ?> Dok
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj/' . $pers['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-10 py-2 text-xs">
                                                Profil
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Summary Card -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-20">Indikator Kesiapan SDM</h6>
                <div class="d-flex justify-content-between mb-16 pb-12 border-bottom">
                    <span class="text-secondary-light">Standar Kebutuhan</span>
                    <strong class="text-primary-light"><?= (int) $opd['required_count'] ?> Org</strong>
                </div>
                <div class="d-flex justify-content-between mb-16 pb-12 border-bottom">
                    <span class="text-secondary-light">Personel Tersedia</span>
                    <strong class="text-success-main"><?= (int) $opd['personnel_count'] ?> Org</strong>
                </div>
                <div class="d-flex justify-content-between mb-16 pb-12 border-bottom">
                    <span class="text-secondary-light">Sertifikat Aktif</span>
                    <strong><?= (int) $opd['certificates_count'] ?> Sertifikat</strong>
                </div>
                <div class="d-flex justify-content-between mb-24">
                    <span class="text-secondary-light">Defisit Gap Formasi</span>
                    <strong class="<?= $opd['gap_count'] > 0 ? 'text-danger-main' : 'text-success-main' ?> fs-6">
                        <?= $opd['gap_count'] > 0 ? "-{$opd['gap_count']} Org" : 'Lengkap' ?>
                    </strong>
                </div>

                <a href="<?= site_url('dashboard/intelligence/gap-kompetensi') ?>" class="btn btn-outline-primary-600 w-100 radius-8 py-10">
                    Lihat Matriks Gap Keseluruhan
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

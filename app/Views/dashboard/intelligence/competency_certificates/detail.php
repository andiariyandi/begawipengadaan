<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Detail Sertifikat Kompetensi') ?></h6>
        <p class="text-secondary-light mb-0">Nomor Registrasi: <strong class="text-primary-600 font-monospace"><?= esc($certificate['certificate_number']) ?></strong></p>
    </div>
    <a href="<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-6">
        <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon> Kembali ke Direktori
    </a>
</div>

<div class="row gy-4">
    <div class="col-xl-8">
        <!-- Main Certificate Card -->
        <div class="card mb-24">
            <div class="card-body p-24">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-16 mb-24">
                    <div class="d-flex align-items-center gap-12">
                        <span class="w-48-px h-48-px bg-primary-50 text-primary-600 rounded-12 d-inline-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:diploma-verified-bold" class="text-2xl"></iconify-icon>
                        </span>
                        <div>
                            <span class="text-xs text-secondary-light fw-medium">STANDAR KOMPETENSI</span>
                            <h5 class="text-lg fw-semibold mb-0"><?= esc($certificate['type_name']) ?></h5>
                        </div>
                    </div>
                    <?= view('dashboard/components/status_badge', [
                        'status' => $certificate['status'],
                    ]) ?>
                </div>

                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Nomor Sertifikat</p>
                        <h6 class="mb-0 text-primary-600 font-monospace"><?= esc($certificate['certificate_number']) ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Tanggal Penerbitan</p>
                        <h6 class="mb-0"><?= ! empty($certificate['issued_at']) ? date('d F Y', strtotime($certificate['issued_at'])) : '-' ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Masa Berlaku Hingga</p>
                        <h6 class="mb-0"><?= ! empty($certificate['expires_at']) ? date('d F Y', strtotime($certificate['expires_at'])) : 'Seumur Hidup' ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Lembaga Akreditasi</p>
                        <h6 class="mb-0">Badan Kepegawaian Daerah &amp; LKPP</h6>
                    </div>
                </div>

                <?php if (! empty($certificate['type_description'])): ?>
                    <hr class="my-20">
                    <p class="text-secondary-light text-sm mb-4">Cakupan Unit Kompetensi</p>
                    <p class="text-primary-light text-sm mb-0"><?= esc($certificate['type_description']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Verification Info Card -->
        <div class="card">
            <div class="card-body p-24">
                <h6 class="text-lg mb-16 d-flex align-items-center gap-8">
                    <iconify-icon icon="solar:shield-check-bold" class="text-success-main text-xl"></iconify-icon>
                    Validasi &amp; Legalitas Dokumen
                </h6>
                <div class="row gy-16">
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Diverifikasi Oleh</p>
                        <h6 class="text-sm fw-semibold mb-0"><?= esc($certificate['verified_by_name'] ?: 'Administrator Sistem') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Waktu Verifikasi</p>
                        <h6 class="text-sm fw-semibold mb-0">
                            <?= ! empty($certificate['verified_at']) ? date('d F Y, H:i', strtotime($certificate['verified_at'])) . ' WIB' : '-' ?>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Profile Summary Card -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body p-24 text-center">
                <span class="w-80-px h-80-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center text-2xl fw-bold mb-16">
                    <?= esc(strtoupper(substr($certificate['owner_name'] ?: 'P', 0, 2))) ?>
                </span>
                <h5 class="fw-semibold mb-4"><?= esc($certificate['owner_name']) ?></h5>
                <p class="text-secondary-light text-sm font-monospace mb-16">NIP: <?= esc($certificate['owner_nip'] ?: '-') ?></p>

                <hr class="my-16">

                <div class="text-start">
                    <div class="mb-12">
                        <span class="text-xs text-secondary-light d-block uppercase fw-medium">Unit Penugasan</span>
                        <h6 class="text-sm fw-semibold mb-0"><?= esc($certificate['organization_unit_name'] ?: 'Pemerintah Provinsi Lampung') ?></h6>
                    </div>
                    <div class="mb-0">
                        <span class="text-xs text-secondary-light d-block uppercase fw-medium">Email</span>
                        <h6 class="text-sm fw-semibold mb-0"><?= esc($certificate['owner_email'] ?: '-') ?></h6>
                    </div>
                </div>

                <a href="<?= site_url('dashboard/intelligence/sdm-jf-ppbj/' . $certificate['jf_ppbj_record_id']) ?>" class="btn btn-outline-primary-600 w-100 radius-8 py-10 mt-24">
                    Lihat Profil Lengkap Personel
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

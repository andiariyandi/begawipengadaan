<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Survei Kepuasan Masyarakat (IKM)') ?></h6>
        <p class="text-secondary-light mb-0">Pemantauan skor indeks kepuasan responden publik, pembobotan 4 dimensi layanan, dan rekap ulasan pengguna.</p>
    </div>
</div>

<!-- PermenPAN-RB No. 14/2017 Official IKM Banner Card -->
<div class="card radius-16 border-0 shadow-sm mb-24 overflow-hidden position-relative bg-base border">
    <div class="card-body p-24">
        <div class="row align-items-center gy-4">
            <div class="col-xl-8 col-lg-7 border-end-lg pe-lg-24">
                <div class="d-flex align-items-center gap-2 mb-8">
                    <span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-6 text-2xs fw-bold text-uppercase">
                        Standar PermenPAN-RB No. 14 Tahun 2017
                    </span>
                    <span class="badge <?= esc($mutuBadgeClass ?? 'bg-success-50 text-success-600') ?> px-10 py-4 radius-6 text-2xs fw-bold">
                        Mutu <?= esc($mutuKategori ?? 'A') ?> (<?= esc($kinerjaPelayanan ?? 'Sangat Baik') ?>)
                    </span>
                </div>
                <h4 class="fw-bold mb-8 text-primary-light">Indeks Kepuasan Masyarakat (IKM) Unit Layanan PBJ</h4>
                <p class="text-secondary-light text-sm mb-16">
                    Hasil pengukuran berkala terhadap 4 unsur penyelenggaraan layanan pengadaan digital, regulasi, helpdesk konsultasi, dan pelatihan pada Biro Pengadaan Barang &amp; Jasa Provinsi Lampung.
                </p>
                <div class="d-flex flex-wrap align-items-center gap-4 text-xs text-secondary-light">
                    <span class="d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-primary-600 text-base"></iconify-icon>
                        <strong class="text-primary-light"><?= (int) ($totalResponses ?? 0) ?></strong> Responden Terdata
                    </span>
                    <span>&bull;</span>
                    <span class="d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:calendar-date-bold" class="text-success-600 text-base"></iconify-icon>
                        Tahun Anggaran <?= date('Y') ?>
                    </span>
                    <span>&bull;</span>
                    <span class="d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:shield-check-bold" class="text-warning-main text-base"></iconify-icon>
                        Validitas Sensus Internal
                    </span>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5 text-lg-center ps-lg-20">
                <div class="p-16 radius-12 bg-neutral-50 border border-neutral-200 d-inline-block w-100">
                    <span class="text-2xs fw-bold text-uppercase tracking-wider text-secondary-light d-block mb-1">NILAI IKM KONVERSI (SKALA 25-100)</span>
                    <h1 class="display-5 fw-bold text-primary-600 mb-0"><?= number_format((float) ($ikmScore ?? 0), 2) ?></h1>
                    <div class="d-flex justify-content-center align-items-center gap-2 mt-8">
                        <span class="badge <?= esc($mutuBadgeClass ?? 'bg-success-50 text-success-600') ?> px-12 py-4 radius-6 text-xs fw-bold">
                            Kinerja: <?= esc($kinerjaPelayanan ?? 'Sangat Baik') ?>
                        </span>
                        <span class="text-2xs text-secondary-light">NRR: <?= number_format((float) ($ikmScale4 ?? 0), 2) ?> / 4.00</span>
                    </div>
                </div>
                <div class="mt-12 d-flex justify-content-center gap-2 d-print-none flex-wrap">
                    <a href="<?= site_url('dashboard/survei-kepuasan/export') ?>" class="btn btn-sm btn-success radius-8 px-14 py-6 text-xs d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:file-download-bold"></iconify-icon> Unduh CSV Responden
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-secondary radius-8 px-14 py-6 text-xs d-inline-flex align-items-center gap-1" onclick="window.print()">
                        <iconify-icon icon="solar:printer-outline"></iconify-icon> Cetak Berita Acara IKM
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row gy-20 mb-24">
    <div class="col-xl-3 col-sm-6">
        <div class="card p-20 radius-12 border-0 bg-primary-subtle text-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-semibold text-primary-light d-block mb-1">Skor Rata-Rata IKM</span>
                    <h3 class="fw-bold mb-0 text-primary-600"><?= number_format($avgTotal, 1) ?> <span class="text-xs fw-normal text-muted">/ 5.0</span></h3>
                </div>
                <div class="w-48-px h-48-px bg-primary-600 text-white rounded-circle d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:star-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-12 text-2xs fw-semibold text-primary">
                <?= $totalResponses ?> Total Responden Berpartisipasi
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card p-20 radius-12 border-0 bg-info-subtle text-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-semibold text-info d-block mb-1">Kesesuaian Persyaratan</span>
                    <h3 class="fw-bold mb-0 text-info"><?= number_format($avgPersyaratan, 1) ?> <span class="text-xs fw-normal text-muted">/ 5.0</span></h3>
                </div>
                <div class="w-48-px h-48-px bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:document-text-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-12 text-2xs fw-semibold text-info">
                Dimensi 1 (Regulasi & Ketentuan)
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card p-20 radius-12 border-0 bg-success-subtle text-success-main">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-semibold text-success d-block mb-1">Keandalan Alur Sistem</span>
                    <h3 class="fw-bold mb-0 text-success-main"><?= number_format($avgSistem, 1) ?> <span class="text-xs fw-normal text-muted">/ 5.0</span></h3>
                </div>
                <div class="w-48-px h-48-px bg-success-main text-white rounded-circle d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:server-square-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-12 text-2xs fw-semibold text-success-main">
                Dimensi 2 (Akses & Navigasi Portal)
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card p-20 radius-12 border-0 bg-warning-subtle text-warning-main">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-xs fw-semibold text-warning d-block mb-1">Respon Helpdesk & Materi</span>
                    <h3 class="fw-bold mb-0 text-warning-main"><?= number_format(($avgLayanan + $avgMateri) / 2, 1) ?> <span class="text-xs fw-normal text-muted">/ 5.0</span></h3>
                </div>
                <div class="w-48-px h-48-px bg-warning-main text-white rounded-circle d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:headphones-round-bold" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-12 text-2xs fw-semibold text-warning-main">
                Dimensi 3 &amp; 4 (Pelayanan & Dokumen)
            </div>
        </div>
    </div>
</div>

<!-- Tabel Unsur Pelayanan Publik PermenPAN-RB No. 14 Tahun 2017 -->
<div class="card mb-24 radius-12 border-0 shadow-sm overflow-hidden">
    <div class="card-header border-bottom py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2 bg-base">
        <div>
            <h6 class="text-sm fw-bold mb-1 text-primary-light d-flex align-items-center gap-2">
                <iconify-icon icon="solar:document-bold" class="text-primary text-xl"></iconify-icon>
                Rekapitulasi Nilai Unsur Pelayanan (PermenPAN-RB No. 14 / 2017)
            </h6>
            <p class="text-secondary-light text-2xs mb-0">Rincian Nilai Rata-rata (NRR), NRR Konversi Skala 1-4, dan NRR Tertimbang per Unsur Pelayanan.</p>
        </div>
        <span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-6 text-2xs fw-bold">
            Total Bobot: 100%
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">Unsur</th>
                        <th>Ruang Lingkup Unsur Pelayanan PBJ</th>
                        <th style="width: 110px;" class="text-center">Bobot Riil</th>
                        <th style="width: 140px;" class="text-center">NRR (Skala 1-5)</th>
                        <th style="width: 140px;" class="text-center">NRR (Skala 1-4)</th>
                        <th style="width: 160px;" class="text-center">NRR Tertimbang</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($panrbElements)): ?>
                        <?php foreach ($panrbElements as $key => $el): ?>
                            <tr>
                                <td class="text-center fw-bold">
                                    <span class="badge bg-neutral-200 text-neutral-800 font-monospace text-xs px-2 py-1"><?= esc($el['code']) ?></span>
                                </td>
                                <td>
                                    <strong class="text-sm text-primary-light d-block"><?= esc($el['name']) ?></strong>
                                    <small class="text-secondary-light">Pilar pengujian kepuasan pengguna layanan pengadaan digital</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info fw-bold text-xs"><?= number_format($el['weight_pct'] ?? 25, 1) ?>%</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-primary-600 font-monospace"><?= number_format((float) ($el['nrr'] ?? 0), 2) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold text-secondary font-monospace"><?= number_format((float) ($el['nrr_4'] ?? 0), 2) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success-main font-monospace fw-bold text-xs px-2 py-1">
                                        <?= number_format((float) ($el['nrr_tertimbang'] ?? 0), 3) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-neutral-50 fw-bold border-top border-2">
                            <td colspan="3" class="text-end text-uppercase text-xs text-secondary-light pe-20">Total Nilai IKM &amp; NRR Tertimbang:</td>
                            <td class="text-center text-primary-600 font-monospace"><?= number_format($avgTotal, 2) ?></td>
                            <td class="text-center text-secondary font-monospace"><?= number_format((float) ($ikmScale4 ?? 0), 2) ?></td>
                            <td class="text-center text-success-main font-monospace fs-6">
                                <?= number_format((float) ($ikmScore ?? 0), 2) ?> <small class="text-2xs fw-normal text-muted">(Mutu <?= esc($mutuKategori) ?>)</small>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 4 Dimensi Aspek IKM -->
<div class="card mb-24">
    <div class="card-header border-bottom py-16 px-24">
        <h6 class="text-sm fw-bold mb-0 text-primary-light d-flex align-items-center gap-2">
            <iconify-icon icon="solar:tuning-square-2-bold" class="text-primary text-xl"></iconify-icon>
            4 Dimensi Aspek Penilaian IKM
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle" id="tableAspectsIkm">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kunci Aspek</th>
                        <th>Judul Dimensi Aspek</th>
                        <th>Deskripsi Pertanyaan</th>
                        <th style="width: 100px;">Bobot (%)</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($aspects)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-3 text-secondary-light">Belum ada aspek survei.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($aspects as $asp): ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary"><?= $no++ ?></td>
                                <td>
                                    <code class="text-xs"><?= esc($asp['aspect_key']) ?></code>
                                </td>
                                <td>
                                    <strong class="text-sm text-primary-light"><?= esc($asp['title']) ?></strong>
                                </td>
                                <td>
                                    <small class="text-secondary-light"><?= esc($asp['description']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-200 text-neutral-800 text-xs"><?= number_format($asp['weight'], 0) ?>%</span>
                                </td>
                                <td>
                                    <span class="badge <?= $asp['is_active'] ? 'bg-success-subtle text-success-main' : 'bg-neutral-100 text-neutral-600' ?> text-2xs">
                                        <?= $asp['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm radius-8 px-10 py-4 text-xs" data-bs-toggle="modal" data-bs-target="#modalEditAspect<?= $asp['id'] ?>">
                                        Ubah
                                    </button>

                                    <!-- Modal Edit Aspect -->
                                    <div class="modal fade" id="modalEditAspect<?= $asp['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <div class="modal-content radius-12 border-0">
                                                <form method="post" action="<?= site_url('dashboard/survei-kepuasan/update-aspect/' . $asp['id']) ?>">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-header border-bottom py-16 px-24">
                                                        <h6 class="modal-title text-base fw-bold text-primary-light">Ubah Dimensi: <?= esc($asp['title']) ?></h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-24">
                                                        <div class="mb-16">
                                                            <label class="form-label fw-semibold text-xs mb-6">Judul Dimensi</label>
                                                            <input type="text" name="title" class="form-control radius-8" value="<?= esc($asp['title']) ?>" required>
                                                        </div>
                                                        <div class="mb-16">
                                                            <label class="form-label fw-semibold text-xs mb-6">Deskripsi Uraian</label>
                                                            <textarea name="description" class="form-control radius-8" rows="2"><?= esc($asp['description']) ?></textarea>
                                                        </div>
                                                        <div class="row gy-16">
                                                            <div class="col-6">
                                                                <label class="form-label fw-semibold text-xs mb-6">Bobot (%)</label>
                                                                <input type="number" name="weight" class="form-control radius-8" value="<?= esc($asp['weight']) ?>" step="0.5">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label fw-semibold text-xs mb-6">Status</label>
                                                                <select name="is_active" class="form-select radius-8">
                                                                    <option value="1" <?= $asp['is_active'] ? 'selected' : '' ?>>Aktif</option>
                                                                    <option value="0" <?= ! $asp['is_active'] ? 'selected' : '' ?>>Nonaktif</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0 pb-24 px-24">
                                                        <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-10" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-10">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Rekap Data Masuk Responden -->
<div class="card basic-data-table">
    <div class="card-header border-bottom py-16 px-24 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="text-sm fw-bold mb-0 text-primary-light d-flex align-items-center gap-2">
            <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-primary text-xl"></iconify-icon>
            Riwayat Responden Survei (Total <?= $totalResponses ?> Responden)
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle begawi-table" id="tableSurveyResponses" data-table-name="riwayat-responden-ikm" data-page-length="10">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Responden & Instansi</th>
                    <th>Peran</th>
                    <th>Skor Aspek (1 - 5)</th>
                    <th>Rata-Rata</th>
                    <th>Kritik & Saran</th>
                    <th>Waktu</th>
                    <th style="width: 100px;" class="text-center no-sort no-export action-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($responses)): ?>
                        <?php $no = 1; foreach ($responses as $resp): ?>
                            <?php
                            $p = (int) ($resp['service_regulation_rating'] ?? $resp['score_persyaratan'] ?? $resp['rating_overall'] ?? 5);
                            $s = (int) ($resp['service_access_rating'] ?? $resp['score_sistem'] ?? $resp['rating_overall'] ?? 5);
                            $l = (int) ($resp['service_helpdesk_rating'] ?? $resp['score_layanan'] ?? $resp['rating_overall'] ?? 5);
                            $m = (int) ($resp['service_learning_rating'] ?? $resp['score_materi'] ?? $resp['rating_overall'] ?? 5);
                            $rAvg = round(($p + $s + $l + $m) / 4, 1);
                            $isAnon = (int) ($resp['is_anonymous'] ?? 1) === 1;
                            $respName = $isAnon ? 'Anonim' : ($resp['respondent_name'] ?: 'Anonim');
                            $respInst = $isAnon ? '-' : ($resp['respondent_institution'] ?? $resp['institution_name'] ?? '-');
                            $respContact = $isAnon ? '-' : ($resp['respondent_contact'] ?: '-');
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <strong class="text-sm text-primary-light d-block"><?= esc($respName) ?></strong>
                                        <?php if ($isAnon): ?>
                                            <span class="badge bg-neutral-100 text-neutral-600 text-3xs"><i class="fas fa-user-secret me-1"></i>Anonim</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-secondary-light"><?= esc($respInst) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-200 text-neutral-800 text-2xs"><?= esc($resp['respondent_role'] ?: 'Lainnya') ?></span>
                                </td>
                                <td>
                                    <div class="text-2xs font-monospace">
                                        P: <b><?= $p ?></b> &middot; S: <b><?= $s ?></b> &middot; L: <b><?= $l ?></b> &middot; M: <b><?= $m ?></b>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success-main border border-success-subtle text-xs fw-bold">
                                        ★ <?= $rAvg ?>
                                    </span>
                                </td>
                                <td>
                                    <p class="text-xs text-secondary-light mb-0 text-truncate" style="max-width: 250px;" title="<?= esc($resp['feedback'] ?: 'Tidak ada catatan saran.') ?>">
                                        <?= esc($resp['feedback'] ?: '-') ?>
                                    </p>
                                </td>
                                <td>
                                    <small class="text-muted text-2xs"><?= date('d/m/Y H:i', strtotime($resp['created_at'])) ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <!-- Tombol Detail -->
                                        <button type="button" class="btn btn-outline-info btn-sm radius-8 px-6 py-2" data-bs-toggle="modal" data-bs-target="#modalDetailResp<?= $resp['id'] ?>" title="Lihat Rincian Tanggapan">
                                            <iconify-icon icon="solar:eye-bold"></iconify-icon>
                                        </button>

                                        <!-- Tombol Hapus (POST dengan CSRF) -->
                                        <form action="<?= site_url('dashboard/survei-kepuasan/hapus/' . $resp['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data responden tiket <?= esc($resp['ticket_number'] ?: '#' . $resp['id']) ?>? Tindakan ini tidak dapat dibatalkan.');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm radius-8 px-6 py-2" title="Hapus Tanggapan">
                                                <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Modal Detail Responden -->
                                    <div class="modal fade" id="modalDetailResp<?= $resp['id'] ?>" tabindex="-1" aria-labelledby="modalDetailRespLabel<?= $resp['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg text-start">
                                            <div class="modal-content radius-16 border-0 shadow-lg">
                                                <div class="modal-header border-bottom py-16 px-24 bg-base">
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 mb-1">
                                                            <span class="badge bg-primary-subtle text-primary font-monospace text-xs px-2 py-1">
                                                                <i class="fas fa-ticket-alt me-1"></i><?= esc($resp['ticket_number'] ?: 'IKM-' . $resp['id']) ?>
                                                            </span>
                                                            <span class="badge <?= $isAnon ? 'bg-secondary-subtle text-secondary' : 'bg-success-subtle text-success' ?> text-xs">
                                                                <?= $isAnon ? 'Responden Anonim' : 'Identitas Terbuka' ?>
                                                            </span>
                                                        </div>
                                                        <h6 class="modal-title fw-bold text-primary-light" id="modalDetailRespLabel<?= $resp['id'] ?>">
                                                            Rincian Evaluasi Kepuasan Pengguna (IKM)
                                                        </h6>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body p-24">
                                                    <!-- Profil Responden -->
                                                    <div class="card bg-neutral-50 border radius-12 p-16 mb-20">
                                                        <div class="row g-3">
                                                            <div class="col-sm-6">
                                                                <span class="text-2xs text-secondary-light text-uppercase fw-semibold d-block">Nama Lengkap Responden</span>
                                                                <strong class="text-sm text-primary-light"><?= esc($respName) ?></strong>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <span class="text-2xs text-secondary-light text-uppercase fw-semibold d-block">Instansi / Perusahaan</span>
                                                                <span class="text-sm fw-medium text-dark"><?= esc($respInst) ?></span>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <span class="text-2xs text-secondary-light text-uppercase fw-semibold d-block">Kategori / Peran PBJ</span>
                                                                <span class="badge bg-primary-50 text-primary-600 text-xs mt-1"><?= esc($resp['respondent_role'] ?: 'Pengguna Layanan') ?></span>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <span class="text-2xs text-secondary-light text-uppercase fw-semibold d-block">Kontak (WhatsApp / Email)</span>
                                                                <span class="text-sm text-secondary"><?= esc($respContact) ?></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Skor 4 Pilar -->
                                                    <h6 class="text-xs fw-bold text-uppercase text-secondary-light mb-12">Penilaian 4 Pilar Layanan PBJ</h6>
                                                    <div class="row g-2 mb-20">
                                                        <div class="col-6 col-md-3">
                                                            <div class="p-12 radius-8 bg-white border text-center">
                                                                <small class="text-muted text-2xs d-block mb-1">Akses Portal</small>
                                                                <h5 class="fw-bold text-primary mb-0">★ <?= $s ?> <span class="text-2xs text-muted">/ 5</span></h5>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="p-12 radius-8 bg-white border text-center">
                                                                <small class="text-muted text-2xs d-block mb-1">Regulasi &amp; SDP</small>
                                                                <h5 class="fw-bold text-warning mb-0">★ <?= $p ?> <span class="text-2xs text-muted">/ 5</span></h5>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="p-12 radius-8 bg-white border text-center">
                                                                <small class="text-muted text-2xs d-block mb-1">Arena Belajar</small>
                                                                <h5 class="fw-bold text-success mb-0">★ <?= $m ?> <span class="text-2xs text-muted">/ 5</span></h5>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <div class="p-12 radius-8 bg-white border text-center">
                                                                <small class="text-muted text-2xs d-block mb-1">Helpdesk LPSE</small>
                                                                <h5 class="fw-bold text-info mb-0">★ <?= $l ?> <span class="text-2xs text-muted">/ 5</span></h5>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Feedback Masukan -->
                                                    <div class="mb-20">
                                                        <span class="text-2xs text-secondary-light text-uppercase fw-semibold d-block mb-1">Kritik, Masukan &amp; Ulasan Lengkap:</span>
                                                        <div class="p-16 radius-8 bg-light border text-sm" style="white-space: pre-wrap; line-height: 1.6;">
                                                            <?= esc($resp['feedback'] ?: 'Tidak ada catatan saran tambahan dari responden.') ?>
                                                        </div>
                                                    </div>

                                                    <!-- Rekomendasi & Metadata -->
                                                    <div class="row g-2 text-xs text-secondary-light pt-2 border-top">
                                                        <div class="col-sm-6">
                                                            <i class="fas fa-thumbs-up text-success me-1"></i> Rekomendasi: <strong><?= esc($resp['recommendation'] ?: 'Pasti Merekomendasikan') ?></strong>
                                                        </div>
                                                        <div class="col-sm-6 text-sm-end">
                                                            <i class="far fa-clock me-1"></i> Waktu: <?= date('d F Y, H:i', strtotime($resp['created_at'])) ?> WIB
                                                        </div>
                                                        <div class="col-12 mt-2">
                                                            <small class="text-muted d-block text-truncate">
                                                                <i class="fas fa-network-wired me-1"></i> IP: <code><?= esc($resp['ip_address'] ?: '-') ?></code> &middot; User Agent: <span class="text-secondary"><?= esc($resp['user_agent'] ?: '-') ?></span>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top py-12 px-24 bg-base">
                                                    <button type="button" class="btn btn-outline-secondary radius-8 px-20 py-8 text-xs" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

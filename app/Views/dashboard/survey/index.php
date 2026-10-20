<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Survei Kepuasan Masyarakat (IKM)') ?></h6>
        <p class="text-secondary-light mb-0">Pemantauan skor indeks kepuasan responden publik, pembobotan 4 dimensi layanan, dan rekap ulasan pengguna.</p>
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
            <table class="table bordered-table mb-0 align-middle">
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
                            <td colspan="7" class="text-center py-3 text-muted">Belum ada aspek survei.</td>
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
            <table class="table bordered-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Responden & Instansi</th>
                        <th>Peran</th>
                        <th>Skor Aspek (1 - 5)</th>
                        <th>Rata-Rata</th>
                        <th>Kritik & Saran</th>
                        <th>Waktu</th>
                        <th style="width: 60px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($responses)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada tanggapan survei masuk.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($responses as $resp): ?>
                            <?php
                            $p = (int) ($resp['service_regulation_rating'] ?? $resp['score_persyaratan'] ?? $resp['rating_overall'] ?? 5);
                            $s = (int) ($resp['service_access_rating'] ?? $resp['score_sistem'] ?? $resp['rating_overall'] ?? 5);
                            $l = (int) ($resp['service_helpdesk_rating'] ?? $resp['score_layanan'] ?? $resp['rating_overall'] ?? 5);
                            $m = (int) ($resp['service_learning_rating'] ?? $resp['score_materi'] ?? $resp['rating_overall'] ?? 5);
                            $rAvg = round(($p + $s + $l + $m) / 4, 1);
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary"><?= $no++ ?></td>
                                <td>
                                    <strong class="text-sm text-primary-light d-block"><?= esc($resp['respondent_name'] ?: 'Anonim') ?></strong>
                                    <small class="text-secondary-light"><?= esc($resp['respondent_institution'] ?? $resp['institution_name'] ?? '-') ?></small>
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
                                    <p class="text-xs text-secondary-light mb-0 text-truncate" style="max-width: 250px;">
                                        <?= esc($resp['feedback'] ?: '-') ?>
                                    </p>
                                </td>
                                <td>
                                    <small class="text-muted text-2xs"><?= date('d/m/Y H:i', strtotime($resp['created_at'])) ?></small>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('dashboard/survei-kepuasan/hapus/' . $resp['id']) ?>" onclick="return confirm('Hapus tanggapan ini?')" class="btn btn-outline-danger btn-sm radius-8 px-6 py-2" title="Hapus">
                                        <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
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
<?= $this->endSection() ?>

<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kelola Popup Pengumuman & Promo') ?></h6>
        <p class="text-secondary-light mb-0">Modal dialog interaktif berbasis carousel yang otomatis menyapa pengunjung di halaman utama.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/pengumuman/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
        <span>Tambah Slide Pengumuman</span>
    </a>
</div>

<!-- Global Popup Switch Card -->
<div class="card mb-24">
    <div class="card-body p-20">
        <form method="post" action="<?= site_url('dashboard/landing/pengumuman/pengaturan') ?>">
            <?= csrf_field() ?>
            <div class="row align-items-center gy-3">
                <div class="col-md-3">
                    <div class="form-switch switch-primary d-flex align-items-center gap-2">
                        <input class="form-check-input cursor-pointer" type="checkbox" name="popup_enabled" value="1" id="popupEnabledSwitch" <?= $popupEnabled === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold text-primary-light text-sm mb-0 cursor-pointer" for="popupEnabledSwitch">
                            Tayangkan Modal Popup
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-neutral-100 text-xs fw-semibold text-secondary-light">Teks Badge Header</span>
                        <input type="text" name="popup_badge" class="form-control" value="<?= esc($popupBadge) ?>" placeholder="PENGUMUMAN RESMI PBJ PROVINSI LAMPUNG">
                    </div>
                </div>
                <div class="col-md-3 text-md-end">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-16 py-8 text-sm">Simpan Pengaturan Modal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th style="width: 140px;">Gambar Media</th>
                        <th>Kategori & Judul Slide</th>
                        <th>Deskripsi & Tautan Aksi</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($announcements)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary-light">Belum ada slide pengumuman. Klik tombol di atas untuk menambah.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($announcements as $a): ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary-light"><?= esc($a['sort_order']) ?></td>
                                <td>
                                    <?php if (! empty($a['image_url'])): ?>
                                        <img src="<?= base_url($a['image_url']) ?>" alt="Slide" class="radius-8 object-fit-cover border" style="width: 120px; height: 60px;" onerror="this.src='<?= base_url('assets/main/images/event/event-details-top-img-1.jpg') ?>'">
                                    <?php else: ?>
                                        <div class="bg-neutral-100 text-neutral-400 d-flex align-items-center justify-content-center radius-8" style="width: 120px; height: 60px;">No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (! empty($a['badge_label'])): ?>
                                        <span class="badge bg-warning-100 text-warning-main radius-4 text-xs mb-1"><?= esc($a['badge_label']) ?></span>
                                    <?php endif; ?>
                                    <h6 class="text-sm fw-semibold mb-0 text-primary-light"><?= esc($a['title']) ?></h6>
                                </td>
                                <td>
                                    <p class="text-xs text-secondary-light mb-1 text-truncate" style="max-width: 400px;"><?= esc($a['description']) ?></p>
                                    <?php if (! empty($a['cta_label'])): ?>
                                        <div class="text-xs text-primary-600 fw-semibold">
                                            <iconify-icon icon="solar:link-circle-bold" class="me-1"></iconify-icon>
                                            <?= esc($a['cta_label']) ?> &rarr; <code class="text-2xs"><?= esc($a['cta_url']) ?></code>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('dashboard/landing/pengumuman/toggle/' . $a['id']) ?>" class="badge <?= $a['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-100 text-secondary-light' ?> px-10 py-6 radius-4 text-xs" title="Klik untuk ubah status">
                                        <?= $a['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/pengumuman/ubah/' . $a['id']) ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white" title="Ubah Pengumuman">
                                            <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/pengumuman/hapus/' . $a['id']) ?>" onclick="return confirm('Hapus slide pengumuman ini?')" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white" title="Hapus Pengumuman">
                                            <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                                        </a>
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

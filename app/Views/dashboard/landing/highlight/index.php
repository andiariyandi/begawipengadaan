<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Pilar, Budaya & Hierarki Regulasi') ?></h6>
        <p class="text-secondary-light mb-0">Kelola 5 Pilar BegawiPBJ, Budaya Kerja PATIK, 4 Tingkat Hierarki Hukum, Roadmap LKPP, dan Fitur Kriptografi.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/pilar-hierarki/tambah?section=' . urlencode($currentSection)) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
        <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
        <span>Tambah Item</span>
    </a>
</div>

<!-- Tabs Navigation for Feature Sections -->
<div class="card mb-24">
    <div class="card-body p-12">
        <ul class="nav nav-pills gap-2 flex-wrap">
            <?php foreach ($validSections as $key => $label): ?>
                <li class="nav-item">
                    <a class="nav-link radius-8 text-sm <?= $currentSection === $key ? 'active bg-primary-600 text-white' : 'text-secondary-light' ?>" href="<?= site_url('dashboard/landing/pilar-hierarki?section=' . urlencode($key)) ?>">
                        <?= esc($label) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="card basic-data-table">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th style="width: 60px;">Ikon</th>
                        <th>Kode & Badge</th>
                        <th>Judul & Subjudul</th>
                        <th>Deskripsi</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary-light">Belum ada item pada kategori ini. Klik "Tambah Item" untuk membuat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary-light"><?= esc($item['sort_order']) ?></td>
                                <td class="text-center">
                                    <div class="radius-8 d-inline-flex align-items-center justify-content-center border" style="width: 42px; height: 42px; background: <?= esc($item['color_hex'] ?: '#0c5aa6') ?>15; color: <?= esc($item['color_hex'] ?: '#0c5aa6') ?>;">
                                        <iconify-icon icon="<?= esc($item['icon_class'] ?: 'solar:star-bold') ?>" class="text-xl"></iconify-icon>
                                    </div>
                                </td>
                                <td>
                                    <?php if (! empty($item['code'])): ?>
                                        <span class="badge bg-neutral-200 text-neutral-800 font-monospace text-xs mb-1"><?= esc($item['code']) ?></span><br>
                                    <?php endif; ?>
                                    <span class="badge bg-primary-100 text-primary-600 radius-4 text-2xs"><?= esc($item['badge_text']) ?></span>
                                </td>
                                <td>
                                    <strong class="text-sm d-block text-primary-light"><?= esc($item['title']) ?></strong>
                                    <small class="text-secondary-light"><?= esc($item['subtitle']) ?></small>
                                </td>
                                <td>
                                    <p class="text-xs text-secondary-light mb-0" style="max-width: 420px; line-height: 1.5;"><?= esc($item['description']) ?></p>
                                    <?php if (! empty($item['link_url'])): ?>
                                        <small class="text-primary-600 text-2xs d-block mt-1">Tautan: <code><?= esc($item['link_url']) ?></code></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('dashboard/landing/pilar-hierarki/toggle/' . $item['id']) ?>" class="badge <?= $item['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-100 text-secondary-light' ?> px-10 py-6 radius-4 text-xs" title="Klik untuk ubah status">
                                        <?= $item['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/pilar-hierarki/ubah/' . $item['id']) ?>" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white" title="Ubah Item">
                                            <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/pilar-hierarki/hapus/' . $item['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white" title="Hapus Item">
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

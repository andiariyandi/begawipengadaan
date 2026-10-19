<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<?php
$isEdit = ! empty($item);
$actionUrl = $isEdit ? site_url('dashboard/landing/pilar-hierarki/ubah/' . $item['id']) : site_url('dashboard/landing/pilar-hierarki/simpan');
$section = $item['section_key'] ?? $currentSection;

$validSections = [
    'pilar_begawi'        => '5 Pilar Ekosistem Begawi',
    'budaya_patik'        => '5 Budaya Kerja PATIK',
    'hierarki_hukum'      => '4 Tingkat Hierarki Hukum PBJ',
    'roadmap_lkpp'        => '4 Level Roadmap Kompetensi LKPP',
    'keamanan_sertifikat' => '4 Pilar Kriptografi & Keamanan Sertifikat',
];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Highlight Fitur') ?></h6>
        <p class="text-secondary-light mb-0">Kelola judul, subjudul, ikon Iconify, kode badge, dan deskripsi.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/pilar-hierarki?section=' . urlencode($section)) ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= $actionUrl ?>">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kelompok Fitur (Section) <span class="text-danger">*</span></label>
                    <select name="section_key" class="form-select radius-8" <?= $isEdit ? 'disabled' : '' ?>>
                        <?php foreach ($validSections as $sKey => $sLabel): ?>
                            <option value="<?= esc($sKey) ?>" <?= $section === $sKey ? 'selected' : '' ?>><?= esc($sLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="section_key" value="<?= esc($section) ?>">
                    <?php endif; ?>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kode Singkat</label>
                    <input type="text" name="code" class="form-control radius-8" value="<?= esc($item['code'] ?? '') ?>" placeholder="Misal: 01, P, L1, UU">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Badge / Label Tag</label>
                    <input type="text" name="badge_text" class="form-control radius-8" value="<?= esc($item['badge_text'] ?? '') ?>" placeholder="Misal: Pilar 01, Tingkat I">
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Judul Utama <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control radius-8" value="<?= esc($item['title'] ?? '') ?>" placeholder="Misal: Arena Belajar LMS" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Subjudul / Kata Kunci</label>
                    <input type="text" name="subtitle" class="form-control radius-8" value="<?= esc($item['subtitle'] ?? '') ?>" placeholder="Misal: Silabus LKPP">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Uraian / Deskripsi Lengkap</label>
                    <textarea name="description" class="form-control radius-8" rows="3" placeholder="Uraian ringkas peran atau definisi fitur tersebut"><?= esc($item['description'] ?? '') ?></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Ikon Iconify</label>
                    <input type="text" name="icon_class" class="form-control radius-8" value="<?= esc($item['icon_class'] ?? 'solar:star-bold') ?>" placeholder="solar:graduation-cap-bold">
                    <small class="text-muted d-block mt-1">Dukung ratusan icon Solar seperti <code>solar:shield-check-bold</code>, <code>solar:document-bold</code></small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Warna Aksen Hex</label>
                    <div class="input-group">
                        <input type="color" class="form-control form-control-color radius-8 p-1" id="colorPicker" value="<?= esc($item['color_hex'] ?? '#0c5aa6') ?>" style="max-width: 50px;">
                        <input type="text" name="color_hex" id="colorHexInput" class="form-control radius-8" value="<?= esc($item['color_hex'] ?? '#0c5aa6') ?>" placeholder="#0c5aa6">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Tautan Navigasi (URL)</label>
                    <input type="text" name="link_url" class="form-control radius-8" value="<?= esc($item['link_url'] ?? '') ?>" placeholder="Misal: arena-belajar atau regulasi">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" class="form-control radius-8" value="<?= esc($item['sort_order'] ?? 1) ?>" min="1" max="99">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Tayang</label>
                    <select name="is_active" class="form-select radius-8">
                        <option value="1" <?= ($item['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Ditampilkan)</option>
                        <option value="0" <?= ($item['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif (Disembunyikan)</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12">
                    <a href="<?= site_url('dashboard/landing/pilar-hierarki?section=' . urlencode($section)) ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Perbarui Item' : 'Simpan Item' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const picker = document.getElementById('colorPicker');
    const input = document.getElementById('colorHexInput');
    if (picker && input) {
        picker.addEventListener('input', function () {
            input.value = picker.value;
        });
        input.addEventListener('input', function () {
            if (/^#[0-9A-Fa-f]{6}$/.test(input.value)) {
                picker.value = input.value;
            }
        });
    }
});
</script>
<?= $this->endSection() ?>

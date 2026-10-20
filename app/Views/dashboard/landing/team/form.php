<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($member);
$actionUrl = $isEdit ? site_url('dashboard/landing/tim/ubah/' . $member['id']) : site_url('dashboard/landing/tim/simpan');
$social = json_decode($member['social_links'] ?? '{}', true) ?: [];
$badge = $social['badge'] ?? '';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Anggota Tim Inovator') ?></h6>
        <p class="text-secondary-light mb-0">Kelola foto potret, nama lengkap, gelar, peran kepemimpinan, dan biografi profil tokoh.</p>
    </div>
    <a href="<?= site_url('dashboard/landing/tim') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control radius-8" value="<?= esc($member['name'] ?? '') ?>" placeholder="Misal: Dr. Ir. Fahrizal Darminto, M.A." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jabatan Kedinasan / Peran <span class="text-danger">*</span></label>
                    <input type="text" name="role_title" class="form-control radius-8" value="<?= esc($member['role_title'] ?? '') ?>" placeholder="Misal: Sekretaris Daerah Provinsi Lampung" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nomor Induk Pegawai (NIP)</label>
                    <input type="text" name="nip" class="form-control radius-8" value="<?= esc($member['nip'] ?? '') ?>" placeholder="19XXXXXXXXXXXXXX (Opsional)">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori Peran</label>
                    <select name="category" class="form-select radius-8">
                        <option value="pembina" <?= ($member['category'] ?? 'pembina') === 'pembina' ? 'selected' : '' ?>>Pembina & Pengarah Daerah</option>
                        <option value="pengembang" <?= ($member['category'] ?? '') === 'pengembang' ? 'selected' : '' ?>>Perekayasa & Pengembang Sistem</option>
                        <option value="narasumber" <?= ($member['category'] ?? '') === 'narasumber' ? 'selected' : '' ?>>Instruktur & Ahli LKPP</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Label Badge Kartu Spotlight</label>
                    <input type="text" name="badge" class="form-control radius-8" value="<?= esc($badge) ?>" placeholder="Misal: Pengarah Strategis, Penanggung Jawab">
                </div>

                <!-- Unggah Foto Potret Anggota Tim -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="photo_file">
                        <iconify-icon icon="solar:camera-bold" class="text-primary me-1"></iconify-icon>
                        Unggah File Foto Potret Tokoh / Anggota
                    </label>
                    <input type="file" name="photo_file" id="photo_file" class="form-control radius-8" accept="image/png,image/jpeg,image/jpg,image/webp" onchange="previewTeamPhoto(this, 'team_preview_img')">
                    <small class="text-muted d-block mt-1">Format: JPG, PNG, atau WEBP. Disarankan rasio tegak 3:4 atau persegi (maks. 5 MB).</small>

                    <!-- Accordion Path Alternatif -->
                    <div class="mt-12 p-12 radius-8 border bg-neutral-50">
                        <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapsePhotoUrl" role="button" aria-expanded="false" aria-controls="collapsePhotoUrl">
                            <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                            <span>Atau gunakan path gambar statis yang sudah ada</span>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                        </a>
                        <div class="collapse mt-8" id="collapsePhotoUrl">
                            <input type="text" name="photo_url" id="photo_url" class="form-control radius-8 text-sm" value="<?= esc($member['photo_url'] ?? 'assets/main/images/team/team-1-1.jpg') ?>" placeholder="assets/main/images/team/team-1-1.jpg">
                            <small class="text-muted text-xs">Kosongkan jika mengunggah file baru di atas.</small>
                        </div>
                    </div>
                </div>

                <!-- Pratinjau Foto Anggota -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pratinjau Foto</label>
                    <?php
                    $curPhoto = $member['photo_url'] ?? 'assets/main/images/team/team-1-1.jpg';
                    $curPhotoSrc = str_starts_with($curPhoto, 'http') ? $curPhoto : base_url($curPhoto);
                    ?>
                    <div class="p-12 radius-8 border bg-neutral-50 d-flex align-items-center gap-3">
                        <img id="team_preview_img" src="<?= esc($curPhotoSrc) ?>" alt="Foto Anggota" class="radius-8 shadow-xs border" style="width: 72px; height: 96px; object-fit: cover;" onerror="this.src='<?= base_url('assets/main/images/team/team-1-1.jpg') ?>'">
                        <div>
                            <span class="badge bg-primary-subtle text-primary text-2xs mb-1 d-inline-block">Spotlight Card</span>
                            <p class="text-2xs text-muted mb-0">Tampil pada kartu tim pengembang di halaman Tentang Kami.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" class="form-control radius-8" value="<?= esc($member['sort_order'] ?? 1) ?>" min="1" max="99">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status Tayang</label>
                    <select name="is_active" class="form-select radius-8">
                        <option value="1" <?= ($member['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Ditampilkan)</option>
                        <option value="0" <?= ($member['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif (Disembunyikan)</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Biografi Singkat / Ringkasan Pengabdian</label>
                    <textarea name="bio" class="form-control radius-8" rows="3" placeholder="Uraikan dedikasi dan kontribusi tokoh ini"><?= esc($member['bio'] ?? '') ?></textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12">
                    <a href="<?= site_url('dashboard/landing/tim') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11"><?= $isEdit ? 'Perbarui Anggota' : 'Simpan Anggota' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function previewTeamPhoto(input, previewImgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(previewImgId);
            if (img) {
                img.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?= $this->endSection() ?>

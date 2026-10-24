<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($member);
$actionUrl = $isEdit ? site_url('dashboard/landing/tim/ubah/' . $member['id']) : site_url('dashboard/landing/tim/simpan');
$social = json_decode($member['social_links'] ?? '{}', true) ?: [];
$badge = $social['badge'] ?? 'Pengarah Strategis';

$defaultSort = $member['sort_order'] ?? ($nextOrder ?? 1);
$curCategory = $member['category'] ?? 'pembina';
$curPhoto    = $member['photo_url'] ?? 'assets/main/images/team/team-1-1.jpg';
$curPhotoSrc = str_starts_with($curPhoto, 'http') ? $curPhoto : base_url($curPhoto);
?>

<!-- Header Page -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Anggota Tim Inovator') ?></h6>
        <p class="text-secondary-light mb-0">Kelola foto potret resmi, gelar kehormatan, jabatan kedinasan, dan biografi profil tokoh.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/landing/tim') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
            <span>Kembali</span>
        </a>
    </div>
</div>

<form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data" id="teamForm">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= esc($member['id']) ?>">
    <?php endif; ?>

    <div class="row g-24">
        <!-- Form Left Column -->
        <div class="col-xl-8 col-lg-7">
            <!-- Preset Quick Selector Card -->
            <div class="card border mb-24 bg-gradient-start-4">
                <div class="card-body p-20">
                    <div class="d-flex align-items-center justify-content-between mb-12 flex-wrap gap-2">
                        <span class="badge bg-primary-600 text-white text-2xs px-8 py-3 radius-4 fw-semibold">
                            <iconify-icon icon="solar:magic-stick-3-bold" class="me-1"></iconify-icon> Template Cepat Tokoh
                        </span>
                        <small class="text-secondary-light text-2xs">Klik untuk mengisi profil standar BegawiPengadaan:</small>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-team-btn shadow-xs" 
                                data-name="Dr. Ir. Fahrizal Darminto, M.A." 
                                data-role="Sekretaris Daerah Provinsi Lampung" 
                                data-nip="196410211990031002"
                                data-cat="pembina" 
                                data-badge="Pengarah Strategis"
                                data-bio="Pengarah strategis percepatan transformasi digital dan penguatan integritas tata kelola pengadaan barang dan jasa Pemerintah Provinsi Lampung."
                                data-photo="assets/main/images/team/team-1-1.jpg">
                            <iconify-icon icon="solar:crown-star-bold" class="text-warning me-1"></iconify-icon> Sekda Lampung
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-team-btn shadow-xs" 
                                data-name="Slamet Riadi, S.Sos., M.M." 
                                data-role="Kepala Biro Pengadaan Barang & Jasa" 
                                data-nip="197305141994031003"
                                data-cat="pembina" 
                                data-badge="Penanggung Jawab"
                                data-bio="Inisiator ekosistem BegawiPengadaan, memimpin transformasi UKPBJ Lampung menuju Level 3 (Proaktif) dan pusat keunggulan PBJ."
                                data-photo="assets/main/images/team/team-1-2.jpg">
                            <iconify-icon icon="solar:shield-star-bold" class="text-primary me-1"></iconify-icon> Kabiro PBJ Lampung
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-team-btn shadow-xs" 
                                data-name="Software Engineering & UI/UX Crew" 
                                data-role="Software Engineering & UX System" 
                                data-nip=""
                                data-cat="pengembang" 
                                data-badge="Tim Pengembang"
                                data-bio="Merancang arsitektur MVC CodeIgniter 4 yang gesit, integrasi database MySQL teroptimasi, dan tata letak responsif modern WowDash."
                                data-photo="assets/main/images/team/team-1-5.jpg">
                            <iconify-icon icon="solar:code-circle-bold" class="text-info me-1"></iconify-icon> Dev &amp; UX Team
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-team-btn shadow-xs" 
                                data-name="Tim LPSE & Pengelola SPSE" 
                                data-role="Infrastruktur & Layanan LPSE" 
                                data-nip=""
                                data-cat="pengembang" 
                                data-badge="Infrastruktur SPSE"
                                data-bio="Menjamin ketersediaan server 24/7 dengan standardisasi ISO 27001 dan keamanan siber pengadaan."
                                data-photo="assets/main/images/team/team-1-4.jpg">
                            <iconify-icon icon="solar:server-square-bold" class="text-success me-1"></iconify-icon> Tim LPSE &amp; SPSE
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-team-btn shadow-xs" 
                                data-name="Tim Pembinaan & Advokasi Hukum PBJ" 
                                data-role="Advokasi & Regulasi PBJ" 
                                data-nip=""
                                data-cat="narasumber" 
                                data-badge="Advokasi Hukum"
                                data-bio="Harmonisasi peraturan, telaah hukum pengadaan, mitigasi sanggahan tender, dan penyusunan panduan teknis."
                                data-photo="assets/main/images/team/team-1-3.jpg">
                            <iconify-icon icon="solar:scale-bold" class="text-danger me-1"></iconify-icon> Advokasi Hukum PBJ
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-team-btn shadow-xs" 
                                data-name="Tim Fasilitator & Master Trainer PBJ" 
                                data-role="Instruktur & Penjamin Mutu" 
                                data-nip=""
                                data-cat="narasumber" 
                                data-badge="Master Trainer"
                                data-bio="Penyusun silabus pembelajaran LMS berbasis kompetensi LKPP dan bank soal sertifikasi level-1."
                                data-photo="assets/main/images/team/team-1-6.jpg">
                            <iconify-icon icon="solar:diploma-verified-bold" class="text-warning me-1"></iconify-icon> Master Trainer LKPP
                        </button>
                    </div>
                </div>
            </div>

            <!-- Team Member Information Card -->
            <div class="card border mb-24">
                <div class="card-header bg-white border-bottom p-20">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:user-hand-up-bold" class="text-primary-600 text-xl"></iconify-icon>
                            <span>Informasi Profil Tokoh</span>
                        </h6>
                        <span class="badge bg-primary-100 text-primary-600 px-10 py-5 radius-4 text-xs fw-semibold">
                            <?= $isEdit ? 'Mode Ubah Data' : 'Mode Tokoh Baru' ?>
                        </span>
                    </div>
                </div>

                <div class="card-body p-24">
                    <div class="row gy-20">
                        <!-- Nama Lengkap & Gelar -->
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Nama Lengkap &amp; Gelar <span class="text-danger">*</span>
                                </label>
                                <span class="text-2xs text-secondary-light" id="nameCharCount">0/150</span>
                            </div>
                            <input type="text" 
                                   name="name" 
                                   id="teamNameInput" 
                                   class="form-control radius-8 fw-semibold" 
                                   value="<?= esc($member['name'] ?? '') ?>" 
                                   placeholder="Misal: Dr. Ir. Fahrizal Darminto, M.A." 
                                   required 
                                   maxlength="150">
                        </div>

                        <!-- NIP -->
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Nomor Induk Pegawai (NIP)
                            </label>
                            <input type="text" 
                                   name="nip" 
                                   id="teamNipInput" 
                                   class="form-control radius-8 font-monospace text-sm" 
                                   value="<?= esc($member['nip'] ?? '') ?>" 
                                   placeholder="19XXXXXXXXXXXXXX (Opsional)" 
                                   maxlength="30">
                            <small class="text-secondary-light text-2xs d-block mt-4">Kosongkan jika tokoh non-PNS atau tim eksternal.</small>
                        </div>

                        <!-- Jabatan Kedinasan / Peran -->
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Jabatan Kedinasan / Peran Sistem <span class="text-danger">*</span>
                                </label>
                                <span class="text-2xs text-secondary-light" id="roleCharCount">0/150</span>
                            </div>
                            <input type="text" 
                                   name="role_title" 
                                   id="teamRoleInput" 
                                   class="form-control radius-8 fw-semibold" 
                                   value="<?= esc($member['role_title'] ?? '') ?>" 
                                   placeholder="Misal: Sekretaris Daerah Provinsi Lampung" 
                                   required 
                                   maxlength="150">
                        </div>

                        <!-- Kategori Peran -->
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Kategori Peran Kepemimpinan
                            </label>
                            <select name="category" id="teamCategoryInput" class="form-select radius-8">
                                <option value="pembina" <?= ($curCategory === 'pembina') ? 'selected' : '' ?>>Pembina &amp; Pengarah Daerah</option>
                                <option value="pengembang" <?= ($curCategory === 'pengembang') ? 'selected' : '' ?>>Perekayasa &amp; Pengembang Sistem</option>
                                <option value="narasumber" <?= ($curCategory === 'narasumber') ? 'selected' : '' ?>>Instruktur &amp; Ahli LKPP</option>
                            </select>
                        </div>

                        <!-- Badge Spotlight -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Label Badge Spotlight Kartu
                                </label>
                                <span class="text-2xs text-secondary-light">Tampil di sudut kartu potret</span>
                            </div>
                            <input type="text" 
                                   name="badge" 
                                   id="teamBadgeInput" 
                                   class="form-control radius-8 fw-semibold" 
                                   value="<?= esc($badge) ?>" 
                                   placeholder="Misal: Pengarah Strategis, Penanggung Jawab" 
                                   maxlength="50">
                            <!-- Quick Badge Chips -->
                            <div class="d-flex align-items-center gap-2 flex-wrap mt-8">
                                <small class="text-secondary-light text-2xs me-1">Pilihan Cepat:</small>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 badge-chip" data-badge="Pengarah Strategis">Pengarah Strategis</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 badge-chip" data-badge="Penanggung Jawab">Penanggung Jawab</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 badge-chip" data-badge="Arsitek Sistem">Arsitek Sistem</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 badge-chip" data-badge="Lead Developer">Lead Developer</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 badge-chip" data-badge="Infrastruktur SPSE">Infrastruktur SPSE</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 badge-chip" data-badge="Master Trainer">Master Trainer</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 badge-chip" data-badge="Advokasi Hukum">Advokasi Hukum</button>
                            </div>
                        </div>

                        <!-- Unggah File Foto Potret -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="photoFileInput">
                                <iconify-icon icon="solar:camera-bold" class="text-primary me-1"></iconify-icon>
                                Foto Potret Tokoh / Tim Inovator
                            </label>
                            
                            <!-- Drag & Drop Zone -->
                            <div class="border border-2 border-dashed radius-12 p-24 text-center bg-neutral-50 position-relative transition-2" id="photoDropzone">
                                <input type="file" 
                                       name="photo_file" 
                                       id="photoFileInput" 
                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                       accept="image/png,image/jpeg,image/jpg,image/webp">
                                <div class="w-56-px h-56-px rounded-circle bg-primary-50 d-inline-flex align-items-center justify-content-center text-primary-600 mb-12">
                                    <iconify-icon icon="solar:cloud-upload-bold" class="text-2xl"></iconify-icon>
                                </div>
                                <h6 class="text-sm fw-semibold text-primary-light mb-4">Pilih file atau seret foto ke area ini</h6>
                                <p class="text-xs text-secondary-light mb-0">Format: PNG, JPG, JPEG, atau WEBP (Maksimal 5 MB). Disarankan rasio potret tegak 3:4.</p>
                                
                                <div id="selectedPhotoAlert" class="mt-12 p-8 radius-8 bg-success-50 text-success-700 text-xs d-none align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="solar:check-circle-bold" class="text-sm"></iconify-icon>
                                    <span id="selectedPhotoName">foto_terpilih.jpg</span>
                                    <button type="button" id="btnCancelPhoto" class="btn btn-link p-0 text-danger-main ms-2 text-2xs fw-bold">Hapus</button>
                                </div>
                            </div>

                            <!-- Drawer Alternatif: Path Gambar Statis -->
                            <div class="mt-12 p-12 radius-8 border bg-neutral-50">
                                <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapsePhotoUrl" role="button" aria-expanded="false" aria-controls="collapsePhotoUrl">
                                    <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                                    <span>Atau gunakan path gambar statis bawaan aset</span>
                                    <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                                </a>
                                <div class="collapse mt-8" id="collapsePhotoUrl">
                                    <input type="text" 
                                           name="photo_url" 
                                           id="photoUrlInput" 
                                           class="form-control radius-8 text-sm" 
                                           value="<?= esc($member['photo_url'] ?? 'assets/main/images/team/team-1-1.jpg') ?>" 
                                           placeholder="assets/main/images/team/team-1-1.jpg">
                                    <small class="text-secondary-light text-2xs d-block mt-4">Jika Anda mengunggah file di atas, field path ini akan digantikan secara otomatis.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Urutan Tampil & Status Tayang -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Urutan Tampil (Sort Order)
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="teamSortInput" 
                                   class="form-control radius-8" 
                                   value="<?= esc($defaultSort) ?>" 
                                   min="1" 
                                   max="99">
                            <small class="text-secondary-light text-2xs d-block mt-4">Nilai lebih kecil tampil paling kiri/awal.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Status Tayang
                            </label>
                            <select name="is_active" id="teamActiveInput" class="form-select radius-8">
                                <option value="1" <?= ($member['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Tampil di Carousel Beranda)</option>
                                <option value="0" <?= ($member['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif / Draf (Disembunyikan)</option>
                            </select>
                        </div>

                        <!-- Biografi Singkat -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Biografi Singkat / Ringkasan Pengabdian
                                </label>
                                <span class="text-2xs text-secondary-light" id="bioCharCount">0/500</span>
                            </div>
                            <textarea name="bio" 
                                      id="teamBioInput" 
                                      class="form-control radius-8" 
                                      rows="3" 
                                      maxlength="500" 
                                      placeholder="Uraikan dedikasi, kontribusi, dan latar belakang tokoh ini..."><?= esc($member['bio'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="card-footer bg-white border-top p-20 d-flex justify-content-end align-items-center gap-12">
                    <a href="<?= site_url('dashboard/landing/tim') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Perbarui Profil Tokoh' : 'Simpan Profil Tokoh' ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Real-Time Simulator Column (Right Side Sticky) -->
        <div class="col-xl-4 col-lg-5">
            <div class="position-sticky" style="top: 90px; z-index: 10;">
                <div class="card border shadow-sm">
                    <div class="card-header bg-white border-bottom p-16">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold text-primary-light mb-0 text-sm d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:eye-bold" class="text-primary-600 text-lg"></iconify-icon>
                                <span>Pratinjau Kartu Spotlight</span>
                            </h6>
                            <span class="badge bg-success-100 text-success-600 radius-4 text-2xs px-8 py-3 fw-semibold">
                                Live Interactive
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-24 bg-neutral-100">
                        <div class="text-center mb-16">
                            <small class="text-secondary-light text-2xs">
                                Tampilan kartu tokoh pada carousel Tim Inovator di beranda publik:
                            </small>
                        </div>

                        <!-- Live Component Preview matching Home index -->
                        <div class="live-spotlight-box mx-auto radius-16 overflow-hidden position-relative shadow-sm transition-2 border bg-dark" 
                             id="liveSpotlightCard"
                             style="width: 270px; height: 350px;">
                            
                            <!-- Photo Image -->
                            <img id="livePhotoPreview" 
                                 src="<?= esc($curPhotoSrc) ?>" 
                                 alt="Foto Profil" 
                                 class="w-100 h-100 object-fit-cover live-photo-img" 
                                 onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                            
                            <!-- Top Spotlight Badge -->
                            <div class="position-absolute top-0 start-0 m-12 z-2" id="liveBadgeWrap">
                                <span class="badge bg-white text-primary-900 shadow-sm radius-999 px-10 py-5 text-3xs fw-bold d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:star-bold" class="text-warning-main text-xs"></iconify-icon>
                                    <span id="liveBadgeText"><?= esc($badge) ?></span>
                                </span>
                            </div>

                            <!-- Gradient Overlay & Meta -->
                            <div class="position-absolute bottom-0 start-0 end-0 p-16 z-2 text-white" 
                                 style="background: linear-gradient(180deg, rgba(5,34,68,0) 0%, rgba(5,34,68,0.85) 50%, rgba(5,34,68,0.98) 100%);">
                                <div class="d-flex align-items-end justify-content-between gap-2">
                                    <div class="overflow-hidden">
                                        <h6 class="text-white fw-bold mb-1 text-sm lh-sm text-truncate" id="liveNameText">
                                            <?= esc($member['name'] ?? 'Nama Lengkap Tokoh') ?>
                                        </h6>
                                        <span class="text-white-50 text-2xs d-block text-truncate" id="liveRoleText">
                                            <?= esc($member['role_title'] ?? 'Jabatan Kedinasan') ?>
                                        </span>
                                        <small class="text-white-50 text-3xs font-monospace d-block mt-1 text-truncate" id="liveNipText">
                                            <?= !empty($member['nip']) ? ('NIP. ' . esc($member['nip'])) : 'Non-PNS / Eksternal' ?>
                                        </small>
                                    </div>
                                    <div class="w-32-px h-32-px rounded-circle bg-white-10 d-flex align-items-center justify-content-center text-white flex-shrink-0">
                                        <iconify-icon icon="solar:arrow-right-up-linear" class="text-sm"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications Summary -->
                        <div class="mt-20 p-16 radius-8 bg-white border">
                            <div class="d-flex justify-content-between align-items-center py-4 border-bottom text-xs">
                                <span class="text-secondary-light">Kategori:</span>
                                <span class="badge bg-purple-100 text-purple-700 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold" id="liveSummaryCategory">
                                    <?= esc($curCategory) ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-4 border-bottom text-xs">
                                <span class="text-secondary-light">Sumber Foto:</span>
                                <span class="font-monospace text-2xs text-truncate ms-2" style="max-width: 170px;" id="liveSummaryPhoto">
                                    <?= esc(basename($curPhoto)) ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-4 text-xs">
                                <span class="text-secondary-light">Urutan &amp; Status:</span>
                                <span class="fw-semibold">
                                    #<span id="liveSummaryOrder"><?= esc($defaultSort) ?></span> &bull; 
                                    <span id="liveSummaryActive" class="<?= ($member['is_active'] ?? 1) ? 'text-success-main' : 'text-warning-main' ?>">
                                        <?= ($member['is_active'] ?? 1) ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.live-spotlight-box {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.live-spotlight-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(5, 34, 68, 0.25) !important;
}
.live-photo-img {
    transition: transform 0.4s ease;
}
.live-spotlight-box:hover .live-photo-img {
    transform: scale(1.05);
}
.btn-2xs {
    padding: 2px 8px;
    font-size: 11px;
    line-height: 1.4;
}
.badge-chip {
    transition: all 0.2s ease;
}
.badge-chip:hover {
    background-color: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
#photoDropzone.dragover {
    border-color: #0284c7 !important;
    background-color: #eff6ff !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inputs
    const nameInput     = document.getElementById('teamNameInput');
    const roleInput     = document.getElementById('teamRoleInput');
    const nipInput      = document.getElementById('teamNipInput');
    const categoryInput = document.getElementById('teamCategoryInput');
    const badgeInput    = document.getElementById('teamBadgeInput');
    const photoFileInput= document.getElementById('photoFileInput');
    const photoUrlInput = document.getElementById('photoUrlInput');
    const sortInput     = document.getElementById('teamSortInput');
    const activeInput   = document.getElementById('teamActiveInput');
    const bioInput      = document.getElementById('teamBioInput');

    // Live preview elements
    const livePhoto     = document.getElementById('livePhotoPreview');
    const liveBadgeWrap = document.getElementById('liveBadgeWrap');
    const liveBadgeText = document.getElementById('liveBadgeText');
    const liveNameText  = document.getElementById('liveNameText');
    const liveRoleText  = document.getElementById('liveRoleText');
    const liveNipText   = document.getElementById('liveNipText');

    // Summary elements
    const summaryCategory = document.getElementById('liveSummaryCategory');
    const summaryPhoto    = document.getElementById('liveSummaryPhoto');
    const summaryOrder    = document.getElementById('liveSummaryOrder');
    const summaryActive   = document.getElementById('liveSummaryActive');

    // Counters
    const nameCharCount = document.getElementById('nameCharCount');
    const roleCharCount = document.getElementById('roleCharCount');
    const bioCharCount  = document.getElementById('bioCharCount');

    // Dropzone elements
    const dropzone      = document.getElementById('photoDropzone');
    const photoAlert    = document.getElementById('selectedPhotoAlert');
    const photoName     = document.getElementById('selectedPhotoName');
    const btnCancelPhoto= document.getElementById('btnCancelPhoto');

    const defaultPhotoSrc = '<?= esc($curPhotoSrc) ?>';

    // 1. Text & State Sync
    function updateLivePreview() {
        // Name
        const nameVal = nameInput ? nameInput.value.trim() : '';
        if (liveNameText) liveNameText.textContent = nameVal || 'Nama Lengkap Tokoh';
        if (nameCharCount && nameInput) nameCharCount.textContent = nameInput.value.length + '/150';

        // Role
        const roleVal = roleInput ? roleInput.value.trim() : '';
        if (liveRoleText) liveRoleText.textContent = roleVal || 'Jabatan Kedinasan';
        if (roleCharCount && roleInput) roleCharCount.textContent = roleInput.value.length + '/150';

        // NIP
        const nipVal = nipInput ? nipInput.value.trim() : '';
        if (liveNipText) {
            liveNipText.textContent = nipVal ? ('NIP. ' + nipVal) : 'Non-PNS / Eksternal';
        }

        // Badge
        const badgeVal = badgeInput ? badgeInput.value.trim() : '';
        if (liveBadgeText) liveBadgeText.textContent = badgeVal || 'Inovator';
        if (liveBadgeWrap) {
            liveBadgeWrap.style.display = badgeVal ? '' : 'none';
        }

        // Category
        const catVal = categoryInput ? categoryInput.value : 'pembina';
        if (summaryCategory) {
            summaryCategory.textContent = catVal;
            if (catVal === 'pembina') {
                summaryCategory.className = 'badge bg-purple-100 text-purple-700 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold';
            } else if (catVal === 'pengembang') {
                summaryCategory.className = 'badge bg-primary-100 text-primary-700 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold';
            } else {
                summaryCategory.className = 'badge bg-success-100 text-success-700 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold';
            }
        }

        // Sort & Active
        if (summaryOrder && sortInput) summaryOrder.textContent = sortInput.value || '1';
        if (summaryActive && activeInput) {
            const isActive = activeInput.value === '1';
            summaryActive.textContent = isActive ? 'Aktif' : 'Nonaktif';
            summaryActive.className = isActive ? 'text-success-main' : 'text-warning-main';
        }

        // Bio counter
        if (bioCharCount && bioInput) bioCharCount.textContent = bioInput.value.length + '/500';
    }

    [nameInput, roleInput, nipInput, badgeInput, categoryInput, sortInput, activeInput, bioInput].forEach(el => {
        if (el) {
            el.addEventListener('input', updateLivePreview);
            el.addEventListener('change', updateLivePreview);
        }
    });

    // 2. Badge Chips
    const badgeChips = document.querySelectorAll('.badge-chip');
    badgeChips.forEach(chip => {
        chip.addEventListener('click', function () {
            if (badgeInput) {
                badgeInput.value = this.getAttribute('data-badge');
                updateLivePreview();
                badgeInput.focus();
            }
        });
    });

    // 3. Preset Buttons
    const presetBtns = document.querySelectorAll('.preset-team-btn');
    presetBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            if (nameInput) nameInput.value = this.getAttribute('data-name');
            if (roleInput) roleInput.value = this.getAttribute('data-role');
            if (nipInput) nipInput.value = this.getAttribute('data-nip') || '';
            if (categoryInput) categoryInput.value = this.getAttribute('data-cat');
            if (badgeInput) badgeInput.value = this.getAttribute('data-badge');
            if (bioInput) bioInput.value = this.getAttribute('data-bio');
            
            const photoPreset = this.getAttribute('data-photo');
            if (photoPreset) {
                if (photoUrlInput) photoUrlInput.value = photoPreset;
                if (livePhoto) livePhoto.src = '<?= base_url() ?>/' + photoPreset;
                if (summaryPhoto) summaryPhoto.textContent = photoPreset.split('/').pop();
            }

            // Clear file upload selection if preset clicked
            if (photoFileInput) photoFileInput.value = '';
            if (photoAlert) photoAlert.classList.add('d-none');

            updateLivePreview();
        });
    });

    // 4. File Upload & FileReader Preview
    function handlePhotoFile(file) {
        if (!file) return;

        if (photoName) photoName.textContent = file.name;
        if (photoAlert) {
            photoAlert.classList.remove('d-none');
            photoAlert.classList.add('d-flex');
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            if (livePhoto) livePhoto.src = e.target.result;
            if (summaryPhoto) summaryPhoto.textContent = file.name;
        };
        reader.readAsDataURL(file);
    }

    if (photoFileInput) {
        photoFileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                handlePhotoFile(this.files[0]);
            }
        });
    }

    if (btnCancelPhoto) {
        btnCancelPhoto.addEventListener('click', function (e) {
            e.preventDefault();
            if (photoFileInput) photoFileInput.value = '';
            if (photoAlert) {
                photoAlert.classList.add('d-none');
                photoAlert.classList.remove('d-flex');
            }
            if (photoUrlInput && photoUrlInput.value) {
                const staticVal = photoUrlInput.value.trim();
                if (livePhoto) livePhoto.src = staticVal.startsWith('http') ? staticVal : ('<?= base_url() ?>/' + staticVal);
                if (summaryPhoto) summaryPhoto.textContent = staticVal.split('/').pop();
            } else {
                if (livePhoto) livePhoto.src = defaultPhotoSrc;
                if (summaryPhoto) summaryPhoto.textContent = defaultPhotoSrc.split('/').pop();
            }
        });
    }

    // Static URL input sync
    if (photoUrlInput) {
        photoUrlInput.addEventListener('input', function () {
            if (!photoFileInput.files || !photoFileInput.files[0]) {
                const staticVal = this.value.trim();
                if (staticVal) {
                    if (livePhoto) livePhoto.src = staticVal.startsWith('http') ? staticVal : ('<?= base_url() ?>/' + staticVal);
                    if (summaryPhoto) summaryPhoto.textContent = staticVal.split('/').pop();
                }
            }
        });
    }

    // Drag and Drop styling
    if (dropzone) {
        ['dragenter', 'dragover'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
                if (photoFileInput) photoFileInput.files = e.dataTransfer.files;
                handlePhotoFile(e.dataTransfer.files[0]);
            }
        });
    }

    // Initial trigger
    updateLivePreview();
});
</script>
<?= $this->endSection() ?>

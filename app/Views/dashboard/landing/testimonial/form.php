<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ! empty($testimonial);
$actionUrl = $isEdit ? site_url('dashboard/landing/testimoni/ubah/' . $testimonial['id']) : site_url('dashboard/landing/testimoni/simpan');

$defaultSort = $testimonial['sort_order'] ?? ($nextOrder ?? 1);
$curCategory = $testimonial['category'] ?? 'opd';
$curRating   = (int) ($testimonial['rating'] ?? 5);
$curAvatar   = $testimonial['avatar_url'] ?? 'assets/main/images/testimonial/testimonial-1-1.jpg';
$curAvatarSrc= str_starts_with($curAvatar, 'http') ? $curAvatar : base_url($curAvatar);
?>

<!-- Header Page -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Form Testimoni Pengguna') ?></h6>
        <p class="text-secondary-light mb-0">Kelola ulasan stakeholder, identitas responden, foto avatar, rating bintang, dan capaian dampak nyata terukur.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('dashboard/landing/testimoni') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon>
            <span>Kembali</span>
        </a>
    </div>
</div>

<form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data" id="testimonialForm">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= esc($testimonial['id']) ?>">
    <?php endif; ?>

    <div class="row g-24">
        <!-- Form Left Column -->
        <div class="col-xl-8 col-lg-7">
            <!-- Preset Quick Selector Card -->
            <div class="card border mb-24 bg-gradient-start-4">
                <div class="card-body p-20">
                    <div class="d-flex align-items-center justify-content-between mb-12 flex-wrap gap-2">
                        <span class="badge bg-primary-600 text-white text-2xs px-8 py-3 radius-4 fw-semibold">
                            <iconify-icon icon="solar:magic-stick-3-bold" class="me-1"></iconify-icon> Template Cepat Responden
                        </span>
                        <small class="text-secondary-light text-2xs">Klik untuk mengisi profil ulasan stakeholder secara otomatis:</small>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-testimony-btn shadow-xs" 
                                data-name="Ahmad Zulkarnaen, S.T., M.T." 
                                data-role="Pejabat Pembuat Komitmen (PPK)" 
                                data-org="Dinas Bina Marga & Bina Konstruksi Provinsi Lampung" 
                                data-cat="opd" 
                                data-rating="5"
                                data-quote="Penyusunan HPS dan spesifikasi teknis untuk paket infrastruktur jalan kini jauh lebih terstandarisasi berkat template dokumen di Bunga Rampai dan sinkronisasi regulasi terpusat. Risiko deviasi hukum saat audit probity berkurang drastis." 
                                data-impact="Efisiensi persiapan lelang 55% & probity nihil temuan" 
                                data-avatar="assets/main/images/testimonial/testimonial-1-1.jpg">
                            <iconify-icon icon="solar:user-bold" class="text-primary me-1"></iconify-icon> PPK Bina Marga
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-testimony-btn shadow-xs" 
                                data-name="Rina Widyastuti, S.E., M.Si." 
                                data-role="Pokja Pemilihan / UKPBJ" 
                                data-org="Biro Pengadaan Barang & Jasa Setda Provinsi Lampung" 
                                data-cat="pokja" 
                                data-rating="5"
                                data-quote="Arena Belajar dan simulasi evaluasi penawaran sangat membantu peningkatan kapasitas anggota Pokja. Ujian sertifikasi dan materi terstruktur meningkatkan kepercayaan diri kami dalam tender bernilai strategis di tingkat provinsi." 
                                data-impact="Evaluasi tender 100% tepat waktu dan bebas sanggahan banding" 
                                data-avatar="assets/main/images/testimonial/testimonial-1-2.jpg">
                            <iconify-icon icon="solar:users-group-two-rounded-bold" class="text-purple me-1"></iconify-icon> Pokja UKPBJ
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-testimony-btn shadow-xs" 
                                data-name="Hendra Kusuma, S.Kom." 
                                data-role="Mitra Penyedia & UMKM" 
                                data-org="PT Lampung Cipta Mandiri" 
                                data-cat="penyedia" 
                                data-rating="5"
                                data-quote="Sebagai pelaku usaha lokal di Lampung, portal BegawiPengadaan membuka transparansi penuh. Kami mudah memahami kriteria kualifikasi, etika penawaran, hingga tata cara etalase produk pada e-katalog lokal Pemprov Lampung." 
                                data-impact="Onboarding E-Katalog Lokal lebih cepat dan transparan" 
                                data-avatar="assets/main/images/testimonial/testimonial-2-1.jpg">
                            <iconify-icon icon="solar:shop-2-bold" class="text-info me-1"></iconify-icon> Penyedia UMKM
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-testimony-btn shadow-xs" 
                                data-name="Drs. Bambang Irawan, Ak., CA." 
                                data-role="Pengawas APIP & Auditor" 
                                data-org="Inspektorat Provinsi Lampung" 
                                data-cat="auditor" 
                                data-rating="5"
                                data-quote="BegawiPengadaan memfasilitasi audit kepatuhan secara objektif. Riwayat versi regulasi dan standar dokumen baku memudahkan kami memverifikasi kesesuaian prosedur dari tahap perencanaan sampai serah terima hasil." 
                                data-impact="Mitigasi deviasi hukum dan penguatan integritas pengadaan" 
                                data-avatar="assets/main/images/testimonial/testimonial-1-3.jpg">
                            <iconify-icon icon="solar:shield-check-bold" class="text-warning me-1"></iconify-icon> Auditor APIP
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-testimony-btn shadow-xs" 
                                data-name="dr. Nurul Hidayati, M.Kes." 
                                data-role="PPK Pengadaan Alkes" 
                                data-org="RSUD Dr. H. Abdul Moeloek" 
                                data-cat="opd" 
                                data-rating="5"
                                data-quote="Pengadaan obat dan alat medis memerlukan ketepatan waktu serta kepatuhan e-katalog nasional yang sangat ketat. Fitur konsultasi dan referensi regulasi di BegawiPengadaan memastikan ketersediaan logistik rumah sakit selalu terjamin." 
                                data-impact="Ketepatan pasokan medis 99.2% dengan e-katalog akurat" 
                                data-avatar="assets/main/images/testimonial/testimonial-2-2.jpg">
                            <iconify-icon icon="solar:hospital-bold" class="text-danger me-1"></iconify-icon> PPK RSUD
                        </button>
                        <button type="button" class="btn btn-xs btn-white border radius-8 px-10 py-6 text-xs fw-semibold preset-testimony-btn shadow-xs" 
                                data-name="Surya Darmawan, S.T." 
                                data-role="Tim Teknis & Pengawas" 
                                data-org="Dinas Perumahan & Kawasan Permukiman Lampung" 
                                data-cat="opd" 
                                data-rating="5"
                                data-quote="Standarisasi berita acara dan format pemeriksaan pekerjaan di Bunga Rampai mempermudah tim teknis melakukan serah terima fisik di lapangan secara presisi dan sesuai kontrak kerja." 
                                data-impact="100% Berita Acara terverifikasi digital" 
                                data-avatar="assets/main/images/testimonial/testimonial-2-3.jpg">
                            <iconify-icon icon="solar:clipboard-check-bold" class="text-success me-1"></iconify-icon> Tim Teknis Disperkim
                        </button>
                    </div>
                </div>
            </div>

            <!-- Testimonial Information Card -->
            <div class="card border mb-24">
                <div class="card-header bg-white border-bottom p-20">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:chat-round-dots-bold" class="text-primary-600 text-xl"></iconify-icon>
                            <span>Informasi Testimoni &amp; Responden</span>
                        </h6>
                        <span class="badge bg-primary-100 text-primary-600 px-10 py-5 radius-4 text-xs fw-semibold">
                            <?= $isEdit ? 'Mode Ubah Data' : 'Mode Testimoni Baru' ?>
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
                                   id="testimonialNameInput" 
                                   class="form-control radius-8 fw-semibold" 
                                   value="<?= esc($testimonial['name'] ?? '') ?>" 
                                   placeholder="Misal: Ahmad Zulkarnaen, S.T., M.T." 
                                   required 
                                   maxlength="150">
                        </div>

                        <!-- Kategori Stakeholder -->
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Kategori Stakeholder
                            </label>
                            <select name="category" id="testimonialCategoryInput" class="form-select radius-8">
                                <option value="opd" <?= in_array($curCategory, ['opd', 'PPK Pengadaan', 'PPK Rumah Sakit', 'Tim Teknis']) ? 'selected' : '' ?>>Perangkat Daerah (PPK/PA/PPTK)</option>
                                <option value="pokja" <?= in_array($curCategory, ['pokja', 'Pokja Pemilihan']) ? 'selected' : '' ?>>Pokja Pemilihan UKPBJ</option>
                                <option value="penyedia" <?= in_array($curCategory, ['penyedia', 'Penyedia UMKM']) ? 'selected' : '' ?>>Pelaku Usaha (Penyedia / UMKM)</option>
                                <option value="auditor" <?= in_array($curCategory, ['auditor', 'Auditor APIP']) ? 'selected' : '' ?>>Auditor / Pengawas APIP</option>
                                <option value="pimpinan" <?= ($curCategory === 'pimpinan') ? 'selected' : '' ?>>Pimpinan Daerah / Kepala UKPBJ</option>
                            </select>
                        </div>

                        <!-- Jabatan / Posisi -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Jabatan / Posisi
                                </label>
                                <span class="text-2xs text-secondary-light" id="roleCharCount">0/150</span>
                            </div>
                            <input type="text" 
                                   name="role_title" 
                                   id="testimonialRoleInput" 
                                   class="form-control radius-8" 
                                   value="<?= esc($testimonial['role_title'] ?? '') ?>" 
                                   placeholder="Misal: Pejabat Pembuat Komitmen (PPK)" 
                                   maxlength="150">
                        </div>

                        <!-- Instansi / Organisasi / Perusahaan -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Instansi / Organisasi / Perusahaan
                                </label>
                                <span class="text-2xs text-secondary-light" id="orgCharCount">0/150</span>
                            </div>
                            <input type="text" 
                                   name="organization" 
                                   id="testimonialOrgInput" 
                                   class="form-control radius-8" 
                                   value="<?= esc($testimonial['organization'] ?? '') ?>" 
                                   placeholder="Misal: Dinas Bina Marga Provinsi Lampung" 
                                   maxlength="150">
                        </div>

                        <!-- Rating Bintang -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Rating Kepuasan
                            </label>
                            <select name="rating" id="testimonialRatingInput" class="form-select radius-8">
                                <option value="5" <?= ($curRating === 5) ? 'selected' : '' ?>>5 Bintang - Sangat Memuaskan</option>
                                <option value="4" <?= ($curRating === 4) ? 'selected' : '' ?>>4 Bintang - Memuaskan</option>
                                <option value="3" <?= ($curRating === 3) ? 'selected' : '' ?>>3 Bintang - Cukup</option>
                                <option value="2" <?= ($curRating === 2) ? 'selected' : '' ?>>2 Bintang - Kurang</option>
                                <option value="1" <?= ($curRating === 1) ? 'selected' : '' ?>>1 Bintang - Sangat Kurang</option>
                            </select>
                        </div>

                        <!-- Pernyataan Dampak Nyata Terukur -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                                Pernyataan Dampak Nyata Terukur (*Measurable Impact*)
                            </label>
                            <input type="text" 
                                   name="impact_statement" 
                                   id="testimonialImpactInput" 
                                   class="form-control radius-8" 
                                   value="<?= esc($testimonial['impact_statement'] ?? '') ?>" 
                                   placeholder="Misal: Efisiensi waktu penyusunan lelang 55% & probity nihil temuan" 
                                   maxlength="255">
                            <!-- Quick Impact Chips -->
                            <div class="d-flex align-items-center gap-2 flex-wrap mt-8">
                                <small class="text-secondary-light text-2xs me-1">Pilihan Cepat:</small>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 impact-chip" data-impact="Efisiensi persiapan lelang 55% & probity nihil temuan">Efisiensi Lelang 55%</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 impact-chip" data-impact="Evaluasi tender 100% tepat waktu dan bebas sanggahan banding">Bebas Sanggahan Banding</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 impact-chip" data-impact="Onboarding E-Katalog Lokal lebih cepat dan transparan">Onboarding E-Katalog</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 impact-chip" data-impact="Mitigasi deviasi hukum dan penguatan integritas pengadaan">Mitigasi Deviasi Hukum</button>
                                <button type="button" class="btn btn-2xs btn-outline-neutral-300 radius-4 impact-chip" data-impact="100% Berita Acara terverifikasi digital">100% BAP Digital</button>
                            </div>
                        </div>

                        <!-- Kutipan Testimoni -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Kutipan Ulasan / Testimoni Responden <span class="text-danger">*</span>
                                </label>
                                <span class="text-2xs text-secondary-light" id="quoteCharCount">0/500</span>
                            </div>
                            <textarea name="quote" 
                                      id="testimonialQuoteInput" 
                                      class="form-control radius-8" 
                                      rows="3" 
                                      maxlength="500" 
                                      required 
                                      placeholder="Tuliskan pengalaman nyata responden terhadap ekosistem BegawiPengadaan..."><?= esc($testimonial['quote'] ?? '') ?></textarea>
                        </div>

                        <!-- Unggah Foto Avatar Responden -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="avatarFileInput">
                                <iconify-icon icon="solar:camera-bold" class="text-primary me-1"></iconify-icon>
                                Foto Avatar Responden / Tokoh
                            </label>
                            
                            <!-- Drag & Drop Zone -->
                            <div class="border border-2 border-dashed radius-12 p-24 text-center bg-neutral-50 position-relative transition-2" id="avatarDropzone">
                                <input type="file" 
                                       name="avatar_file" 
                                       id="avatarFileInput" 
                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                       accept="image/png,image/jpeg,image/jpg,image/webp">
                                <div class="w-56-px h-56-px rounded-circle bg-primary-50 d-inline-flex align-items-center justify-content-center text-primary-600 mb-12">
                                    <iconify-icon icon="solar:cloud-upload-bold" class="text-2xl"></iconify-icon>
                                </div>
                                <h6 class="text-sm fw-semibold text-primary-light mb-4">Pilih file atau seret foto avatar ke area ini</h6>
                                <p class="text-xs text-secondary-light mb-0">Format: PNG, JPG, JPEG, atau WEBP (Maksimal 5 MB). Disarankan rasio persegi 1:1.</p>
                                
                                <div id="selectedAvatarAlert" class="mt-12 p-8 radius-8 bg-success-50 text-success-700 text-xs d-none align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="solar:check-circle-bold" class="text-sm"></iconify-icon>
                                    <span id="selectedAvatarName">avatar.jpg</span>
                                    <button type="button" id="btnCancelAvatar" class="btn btn-link p-0 text-danger-main ms-2 text-2xs fw-bold">Hapus</button>
                                </div>
                            </div>

                            <!-- Drawer Alternatif: Path Gambar Statis -->
                            <div class="mt-12 p-12 radius-8 border bg-neutral-50">
                                <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseAvatarUrl" role="button" aria-expanded="false" aria-controls="collapseAvatarUrl">
                                    <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                                    <span>Atau gunakan path gambar statis bawaan aset</span>
                                    <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                                </a>
                                <div class="collapse mt-8" id="collapseAvatarUrl">
                                    <input type="text" 
                                           name="avatar_url" 
                                           id="avatarUrlInput" 
                                           class="form-control radius-8 text-sm" 
                                           value="<?= esc($testimonial['avatar_url'] ?? 'assets/main/images/testimonial/testimonial-1-1.jpg') ?>" 
                                           placeholder="assets/main/images/testimonial/testimonial-1-1.jpg">
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
                                   id="testimonialSortInput" 
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
                            <select name="is_active" id="testimonialActiveInput" class="form-select radius-8">
                                <option value="1" <?= ($testimonial['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif (Tampil di Carousel Beranda)</option>
                                <option value="0" <?= ($testimonial['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif / Draf (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="card-footer bg-white border-top p-20 d-flex justify-content-end align-items-center gap-12">
                    <a href="<?= site_url('dashboard/landing/testimoni') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:check-circle-bold" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Perbarui Testimoni' : 'Simpan Testimoni' ?></span>
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
                                <span>Pratinjau Gelembung Ulasan</span>
                            </h6>
                            <span class="badge bg-success-100 text-success-600 radius-4 text-2xs px-8 py-3 fw-semibold">
                                Live Interactive
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-24 bg-neutral-100">
                        <div class="text-center mb-16">
                            <small class="text-secondary-light text-2xs">
                                Tampilan kartu obrolan (*speech bubble*) pada carousel testimoni beranda publik:
                            </small>
                        </div>

                        <!-- Live Component Preview matching Home index speech bubble -->
                        <div class="live-bubble-card mx-auto" style="max-width: 330px;">
                            <!-- Speech Bubble -->
                            <div class="live-testimony-bubble radius-16 p-20 bg-white border shadow-xs position-relative mb-20 d-flex flex-column" 
                                 id="liveBubbleBox" 
                                 style="min-height: 200px; border: 1.5px solid #e2e8f0;">
                                <!-- Top Bar: Stars + Rating + Icon -->
                                <div class="d-flex align-items-center justify-content-between mb-12">
                                    <div class="d-flex align-items-center gap-1 text-warning-main text-xs" id="liveStarsContainer">
                                        <?php for ($s = 1; $s <= 5; $s++): ?>
                                            <iconify-icon icon="solar:star-bold" class="<?= $s <= $curRating ? 'text-warning-main' : 'text-neutral-300' ?> star-icon"></iconify-icon>
                                        <?php endfor; ?>
                                        <span class="text-xs fw-bold text-dark ms-1" id="liveRatingText"><?= number_format($curRating, 1) ?></span>
                                    </div>
                                    <iconify-icon icon="solar:chat-round-dots-bold" class="text-primary-300 text-xl"></iconify-icon>
                                </div>

                                <!-- Quote Text -->
                                <p class="text-xs text-secondary mb-16 flex-grow-1" id="liveQuoteText" style="line-height: 1.6;">
                                    &ldquo;<?= esc($testimonial['quote'] ?? 'Tuliskan ulasan atau pengalaman responden di formulir sebelah kiri...') ?>&rdquo;
                                </p>

                                <!-- Impact Statement Badge -->
                                <div class="p-8 bg-success-50 text-success-700 radius-8 border border-success-200 text-3xs fw-semibold d-inline-flex align-items-center gap-1 mt-auto" id="liveImpactWrap">
                                    <iconify-icon icon="solar:graph-up-bold" class="text-success-main text-xs"></iconify-icon>
                                    <span id="liveImpactText"><?= esc($testimonial['impact_statement'] ?? 'Dampak nyata terukur') ?></span>
                                </div>

                                <!-- Speech Bubble Tail -->
                                <div class="live-bubble-tail"></div>
                            </div>

                            <!-- Author Bar -->
                            <div class="d-flex align-items-center gap-12 ps-12">
                                <div class="w-48-px h-48-px rounded-circle overflow-hidden border border-2 border-white shadow-xs bg-neutral-100 flex-shrink-0">
                                    <img id="liveAvatarPreview" 
                                         src="<?= esc($curAvatarSrc) ?>" 
                                         alt="Avatar" 
                                         class="w-100 h-100 object-fit-cover" 
                                         onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="fw-bold text-primary-light mb-0 text-xs text-truncate" id="liveNameText">
                                        <?= esc($testimonial['name'] ?? 'Nama Lengkap Responden') ?>
                                    </h6>
                                    <span class="text-secondary-light text-3xs d-block text-truncate" id="liveOrgText">
                                        <?= esc($testimonial['organization'] ?? 'Instansi / Perusahaan') ?>
                                    </span>
                                    <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                        <span class="badge bg-primary-50 text-primary-700 text-3xs radius-999 px-8 py-2 fw-bold d-inline-block text-truncate" id="liveRoleText" style="max-width: 140px;">
                                            <?= esc($testimonial['role_title'] ?? 'Jabatan Responden') ?>
                                        </span>
                                        <span class="badge bg-purple-50 text-purple-700 text-3xs radius-999 px-8 py-2 fw-bold d-inline-block" id="liveCatBadge">
                                            <?= esc($curCategory) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications Summary -->
                        <div class="mt-20 p-16 radius-8 bg-white border">
                            <div class="d-flex justify-content-between align-items-center py-4 border-bottom text-xs">
                                <span class="text-secondary-light">Kategori:</span>
                                <span class="badge bg-primary-100 text-primary-700 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold" id="liveSummaryCategory">
                                    <?= esc($curCategory) ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-4 border-bottom text-xs">
                                <span class="text-secondary-light">Rating:</span>
                                <span class="fw-bold text-warning-main text-xs d-flex align-items-center gap-1" id="liveSummaryRating">
                                    <iconify-icon icon="solar:star-bold"></iconify-icon> <?= number_format($curRating, 1) ?> / 5.0
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-4 border-bottom text-xs">
                                <span class="text-secondary-light">Sumber Foto:</span>
                                <span class="font-monospace text-2xs text-truncate ms-2" style="max-width: 170px;" id="liveSummaryPhoto">
                                    <?= esc(basename($curAvatar)) ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-4 text-xs">
                                <span class="text-secondary-light">Urutan &amp; Status:</span>
                                <span class="fw-semibold">
                                    #<span id="liveSummaryOrder"><?= esc($defaultSort) ?></span> &bull; 
                                    <span id="liveSummaryActive" class="<?= ($testimonial['is_active'] ?? 1) ? 'text-success-main' : 'text-warning-main' ?>">
                                        <?= ($testimonial['is_active'] ?? 1) ? 'Aktif' : 'Nonaktif' ?>
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
.live-testimony-bubble {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.live-bubble-card:hover .live-testimony-bubble {
    border-color: #0c5aa6 !important;
    box-shadow: 0 14px 35px rgba(12, 90, 166, 0.12) !important;
    transform: translateY(-3px);
}
.live-bubble-tail {
    position: absolute;
    bottom: -8px;
    left: 28px;
    width: 15px;
    height: 15px;
    background: #ffffff;
    border-right: 1.5px solid #e2e8f0;
    border-bottom: 1.5px solid #e2e8f0;
    transform: rotate(45deg);
}
.live-bubble-card:hover .live-bubble-tail {
    border-color: #0c5aa6 !important;
}
.btn-2xs {
    padding: 2px 8px;
    font-size: 11px;
    line-height: 1.4;
}
.impact-chip {
    transition: all 0.2s ease;
}
.impact-chip:hover {
    background-color: #16a34a;
    color: #ffffff;
    border-color: #16a34a;
}
#avatarDropzone.dragover {
    border-color: #0284c7 !important;
    background-color: #eff6ff !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inputs
    const nameInput     = document.getElementById('testimonialNameInput');
    const roleInput     = document.getElementById('testimonialRoleInput');
    const orgInput      = document.getElementById('testimonialOrgInput');
    const categoryInput = document.getElementById('testimonialCategoryInput');
    const ratingInput   = document.getElementById('testimonialRatingInput');
    const impactInput   = document.getElementById('testimonialImpactInput');
    const quoteInput    = document.getElementById('testimonialQuoteInput');
    const avatarFileInput= document.getElementById('avatarFileInput');
    const avatarUrlInput = document.getElementById('avatarUrlInput');
    const sortInput     = document.getElementById('testimonialSortInput');
    const activeInput   = document.getElementById('testimonialActiveInput');

    // Live preview elements
    const liveAvatar    = document.getElementById('liveAvatarPreview');
    const liveNameText  = document.getElementById('liveNameText');
    const liveRoleText  = document.getElementById('liveRoleText');
    const liveOrgText   = document.getElementById('liveOrgText');
    const liveQuoteText = document.getElementById('liveQuoteText');
    const liveImpactWrap= document.getElementById('liveImpactWrap');
    const liveImpactText= document.getElementById('liveImpactText');
    const liveRatingText= document.getElementById('liveRatingText');
    const liveCatBadge  = document.getElementById('liveCatBadge');
    const liveStarsBox  = document.getElementById('liveStarsContainer');

    // Summary elements
    const summaryCategory = document.getElementById('liveSummaryCategory');
    const summaryRating   = document.getElementById('liveSummaryRating');
    const summaryPhoto    = document.getElementById('liveSummaryPhoto');
    const summaryOrder    = document.getElementById('liveSummaryOrder');
    const summaryActive   = document.getElementById('liveSummaryActive');

    // Counters
    const nameCharCount = document.getElementById('nameCharCount');
    const roleCharCount = document.getElementById('roleCharCount');
    const orgCharCount  = document.getElementById('orgCharCount');
    const quoteCharCount= document.getElementById('quoteCharCount');

    // Dropzone elements
    const dropzone      = document.getElementById('avatarDropzone');
    const avatarAlert   = document.getElementById('selectedAvatarAlert');
    const avatarName    = document.getElementById('selectedAvatarName');
    const btnCancelAvatar= document.getElementById('btnCancelAvatar');

    const defaultAvatarSrc = '<?= esc($curAvatarSrc) ?>';

    // 1. Text & State Sync
    function updateLivePreview() {
        // Name
        const nameVal = nameInput ? nameInput.value.trim() : '';
        if (liveNameText) liveNameText.textContent = nameVal || 'Nama Lengkap Responden';
        if (nameCharCount && nameInput) nameCharCount.textContent = nameInput.value.length + '/150';

        // Role
        const roleVal = roleInput ? roleInput.value.trim() : '';
        if (liveRoleText) liveRoleText.textContent = roleVal || 'Jabatan Responden';
        if (roleCharCount && roleInput) roleCharCount.textContent = roleInput.value.length + '/150';

        // Org
        const orgVal = orgInput ? orgInput.value.trim() : '';
        if (liveOrgText) liveOrgText.textContent = orgVal || 'Instansi / Perusahaan';
        if (orgCharCount && orgInput) orgCharCount.textContent = orgInput.value.length + '/150';

        // Quote
        const quoteVal = quoteInput ? quoteInput.value.trim() : '';
        if (liveQuoteText) {
            liveQuoteText.innerHTML = '&ldquo;' + (quoteVal || 'Tuliskan ulasan atau pengalaman responden di formulir sebelah kiri...') + '&rdquo;';
        }
        if (quoteCharCount && quoteInput) quoteCharCount.textContent = quoteInput.value.length + '/500';

        // Impact Statement
        const impactVal = impactInput ? impactInput.value.trim() : '';
        if (liveImpactText) liveImpactText.textContent = impactVal || 'Dampak nyata terukur';
        if (liveImpactWrap) {
            liveImpactWrap.style.display = impactVal ? 'inline-flex' : 'none';
        }

        // Rating
        const ratingVal = ratingInput ? parseInt(ratingInput.value) : 5;
        if (liveRatingText) liveRatingText.textContent = ratingVal.toFixed(1);
        if (summaryRating) {
            summaryRating.innerHTML = `<iconify-icon icon="solar:star-bold"></iconify-icon> ${ratingVal.toFixed(1)} / 5.0`;
        }

        // Render dynamic stars in container
        if (liveStarsBox) {
            let starHtml = '';
            for (let s = 1; s <= 5; s++) {
                const isGold = s <= ratingVal;
                starHtml += `<iconify-icon icon="solar:star-bold" class="${isGold ? 'text-warning-main' : 'text-neutral-300'} star-icon"></iconify-icon>`;
            }
            starHtml += `<span class="text-xs fw-bold text-dark ms-1" id="liveRatingText">${ratingVal.toFixed(1)}</span>`;
            liveStarsBox.innerHTML = starHtml;
        }

        // Category
        const catVal = categoryInput ? categoryInput.value : 'opd';
        if (liveCatBadge) liveCatBadge.textContent = catVal.toUpperCase();
        if (summaryCategory) {
            summaryCategory.textContent = catVal;
            if (catVal === 'opd') {
                summaryCategory.className = 'badge bg-primary-100 text-primary-700 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold';
            } else if (catVal === 'pokja') {
                summaryCategory.className = 'badge bg-purple-100 text-purple-700 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold';
            } else if (catVal === 'penyedia') {
                summaryCategory.className = 'badge bg-info-100 text-info-700 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold';
            } else if (catVal === 'auditor') {
                summaryCategory.className = 'badge bg-warning-100 text-warning-800 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold';
            } else {
                summaryCategory.className = 'badge bg-neutral-200 text-neutral-800 text-2xs text-uppercase radius-4 px-8 py-3 fw-bold';
            }
        }

        // Sort & Active
        if (summaryOrder && sortInput) summaryOrder.textContent = sortInput.value || '1';
        if (summaryActive && activeInput) {
            const isActive = activeInput.value === '1';
            summaryActive.textContent = isActive ? 'Aktif' : 'Nonaktif';
            summaryActive.className = isActive ? 'text-success-main' : 'text-warning-main';
        }
    }

    [nameInput, roleInput, orgInput, categoryInput, ratingInput, impactInput, quoteInput, sortInput, activeInput].forEach(el => {
        if (el) {
            el.addEventListener('input', updateLivePreview);
            el.addEventListener('change', updateLivePreview);
        }
    });

    // 2. Impact Chips
    const impactChips = document.querySelectorAll('.impact-chip');
    impactChips.forEach(chip => {
        chip.addEventListener('click', function () {
            if (impactInput) {
                impactInput.value = this.getAttribute('data-impact');
                updateLivePreview();
                impactInput.focus();
            }
        });
    });

    // 3. Preset Buttons
    const presetBtns = document.querySelectorAll('.preset-testimony-btn');
    presetBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            if (nameInput) nameInput.value = this.getAttribute('data-name');
            if (roleInput) roleInput.value = this.getAttribute('data-role');
            if (orgInput) orgInput.value = this.getAttribute('data-org');
            if (categoryInput) categoryInput.value = this.getAttribute('data-cat');
            if (ratingInput) ratingInput.value = this.getAttribute('data-rating') || '5';
            if (quoteInput) quoteInput.value = this.getAttribute('data-quote');
            if (impactInput) impactInput.value = this.getAttribute('data-impact');
            
            const avatarPreset = this.getAttribute('data-avatar');
            if (avatarPreset) {
                if (avatarUrlInput) avatarUrlInput.value = avatarPreset;
                if (liveAvatar) liveAvatar.src = '<?= base_url() ?>/' + avatarPreset;
                if (summaryPhoto) summaryPhoto.textContent = avatarPreset.split('/').pop();
            }

            // Clear file upload selection if preset clicked
            if (avatarFileInput) avatarFileInput.value = '';
            if (avatarAlert) avatarAlert.classList.add('d-none');

            updateLivePreview();
        });
    });

    // 4. File Upload & FileReader Preview
    function handleAvatarFile(file) {
        if (!file) return;

        if (avatarName) avatarName.textContent = file.name;
        if (avatarAlert) {
            avatarAlert.classList.remove('d-none');
            avatarAlert.classList.add('d-flex');
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            if (liveAvatar) liveAvatar.src = e.target.result;
            if (summaryPhoto) summaryPhoto.textContent = file.name;
        };
        reader.readAsDataURL(file);
    }

    if (avatarFileInput) {
        avatarFileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                handleAvatarFile(this.files[0]);
            }
        });
    }

    if (btnCancelAvatar) {
        btnCancelAvatar.addEventListener('click', function (e) {
            e.preventDefault();
            if (avatarFileInput) avatarFileInput.value = '';
            if (avatarAlert) {
                avatarAlert.classList.add('d-none');
                avatarAlert.classList.remove('d-flex');
            }
            if (avatarUrlInput && avatarUrlInput.value) {
                const staticVal = avatarUrlInput.value.trim();
                if (liveAvatar) liveAvatar.src = staticVal.startsWith('http') ? staticVal : ('<?= base_url() ?>/' + staticVal);
                if (summaryPhoto) summaryPhoto.textContent = staticVal.split('/').pop();
            } else {
                if (liveAvatar) liveAvatar.src = defaultAvatarSrc;
                if (summaryPhoto) summaryPhoto.textContent = defaultAvatarSrc.split('/').pop();
            }
        });
    }

    // Static URL input sync
    if (avatarUrlInput) {
        avatarUrlInput.addEventListener('input', function () {
            if (!avatarFileInput.files || !avatarFileInput.files[0]) {
                const staticVal = this.value.trim();
                if (staticVal) {
                    if (liveAvatar) liveAvatar.src = staticVal.startsWith('http') ? staticVal : ('<?= base_url() ?>/' + staticVal);
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
                if (avatarFileInput) avatarFileInput.files = e.dataTransfer.files;
                handleAvatarFile(e.dataTransfer.files[0]);
            }
        });
    }

    // Initial trigger
    updateLivePreview();
});
</script>
<?= $this->endSection() ?>

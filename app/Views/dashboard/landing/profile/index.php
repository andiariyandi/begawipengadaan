<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Profil Lembaga, Visi Misi & Filosofi') ?></h6>
        <p class="text-secondary-light mb-0">Kelola informasi resmi Biro PBJ Pemprov Lampung, filosofi etimologi "Begawi", 5 misi strategis, serta maklumat pelayanan.</p>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'profile', 'activeSub' => 'profil-lembaga']) ?>

<form method="post" action="<?= site_url('dashboard/landing/profil-lembaga') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row gy-24">
        <!-- 1. Identitas Instansi & Alamat -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header border-bottom py-16 px-24">
                    <h6 class="text-md fw-semibold mb-0 text-primary-light d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:city-bold" class="text-primary-600 text-xl"></iconify-icon>
                        Identitas Instansi Penyelenggara
                    </h6>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-16">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Nama Resmi Instansi</label>
                            <input type="text" name="organization[agency_name]" class="form-control radius-8" value="<?= esc($organization['agency_name'] ?? 'Biro Pengadaan Barang & Jasa Sekretariat Daerah Provinsi Lampung') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6">Predikat Kematangan UKPBJ</label>
                            <input type="text" name="organization[institution_level]" class="form-control radius-8" value="<?= esc($organization['institution_level'] ?? 'UKPBJ Level Proaktif (Tingkat Kematangan Proaktif)') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6">Status Integritas</label>
                            <input type="text" name="organization[institution_zone]" class="form-control radius-8" value="<?= esc($organization['institution_zone'] ?? 'Zona Integritas Wilayah Bebas Korupsi & Gratifikasi') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-sm mb-6">Alamat Kantor Lengkap</label>
                            <textarea name="organization[address]" class="form-control radius-8" rows="2"><?= esc($organization['address'] ?? 'Jl. Wolter Monginsidi No. 69, Teluk Betung, Kota Bandar Lampung, Lampung 35211') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6">Telepon Resmi</label>
                            <input type="text" name="organization[phone]" class="form-control radius-8" value="<?= esc($organization['phone'] ?? '+62 851-1777-9783') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6">Email Resmi</label>
                            <input type="email" name="organization[email]" class="form-control radius-8" value="<?= esc($organization['email'] ?? 'biropbj@lampungprov.go.id') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6">Jam Pelayanan</label>
                            <input type="text" name="organization[operational_hours]" class="form-control radius-8" value="<?= esc($organization['operational_hours'] ?? 'Senin - Jumat, 08.00 - 16.00 WIB') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6">Tautan Google Maps</label>
                            <input type="text" name="organization[google_maps_url]" class="form-control radius-8" value="<?= esc($organization['google_maps_url'] ?? '') ?>">
                        </div>

                        <!-- Bagian Unggah Logo Resmi & Cap Lembaga -->
                        <div class="col-12"><hr class="my-2"></div>
                        <div class="col-12">
                            <div class="p-16 radius-8 border bg-neutral-50">
                                <div class="row align-items-center gy-12">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="logo_file">
                                            <iconify-icon icon="solar:gallery-bold" class="text-primary me-1"></iconify-icon>
                                            Unggah File Logo Resmi Lembaga
                                        </label>
                                        <input type="file" name="logo_file" id="logo_file" class="form-control radius-8" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" onchange="previewImage(this, 'inst_logo_preview_img')">
                                        <small class="text-secondary-light d-block mt-1">Format: PNG, SVG, JPG, WEBP (disarankan PNG transparan, maks. 5 MB).</small>

                                        <div class="mt-8">
                                            <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseLogoUrl" role="button" aria-expanded="false" aria-controls="collapseLogoUrl">
                                                <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                                                <span>Atau gunakan path logo statis yang sudah ada</span>
                                                <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                                            </a>
                                            <div class="collapse mt-6" id="collapseLogoUrl">
                                                <input type="text" name="organization[logo_url]" id="org_logo_url" class="form-control radius-8 text-xs" value="<?= esc($organization['logo_url'] ?? 'assets/main/images/resources/logo-1.png') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <?php
                                        $curLogo = $organization['logo_url'] ?? 'assets/main/images/resources/logo-1.png';
                                        $curLogoSrc = str_starts_with($curLogo, 'http') ? $curLogo : base_url($curLogo);
                                        ?>
                                        <label class="form-label fw-semibold text-xs text-secondary-light mb-6 d-block">Pratinjau Logo</label>
                                        <div class="p-8 bg-white border radius-8 d-inline-block">
                                            <img id="inst_logo_preview_img" src="<?= esc($curLogoSrc) ?>" alt="Logo Instansi" style="max-height: 48px; max-width: 140px; object-fit: contain;" onerror="this.src='<?= base_url('assets/main/images/resources/logo-1.png') ?>'">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-16 radius-8 border bg-neutral-50">
                                <div class="row align-items-center gy-12">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold text-primary-light text-sm mb-6" for="seal_file">
                                            <iconify-icon icon="solar:stamp-bold" class="text-success me-1"></iconify-icon>
                                            Unggah File Cap / Stempel Resmi Lembaga
                                        </label>
                                        <input type="file" name="seal_file" id="seal_file" class="form-control radius-8" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml" onchange="previewImage(this, 'inst_seal_preview_img')">
                                        <small class="text-secondary-light d-block mt-1">Format: PNG transparan direkomendasikan (maks. 5 MB).</small>

                                        <div class="mt-8">
                                            <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseSealUrl" role="button" aria-expanded="false" aria-controls="collapseSealUrl">
                                                <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                                                <span>Atau gunakan path cap statis yang sudah ada</span>
                                                <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                                            </a>
                                            <div class="collapse mt-6" id="collapseSealUrl">
                                                <input type="text" name="organization[seal_url]" id="org_seal_url" class="form-control radius-8 text-xs" value="<?= esc($organization['seal_url'] ?? 'assets/main/images/resources/seal-prov-lampung.png') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <?php
                                        $curSeal = $organization['seal_url'] ?? 'assets/main/images/resources/seal-prov-lampung.png';
                                        $curSealSrc = str_starts_with($curSeal, 'http') ? $curSeal : base_url($curSeal);
                                        ?>
                                        <label class="form-label fw-semibold text-xs text-secondary-light mb-6 d-block">Pratinjau Stempel</label>
                                        <div class="p-8 bg-white border radius-8 d-inline-block">
                                            <img id="inst_seal_preview_img" src="<?= esc($curSealSrc) ?>" alt="Cap Lembaga" style="max-height: 52px; max-width: 52px; object-fit: contain;" onerror="this.src='<?= base_url('assets/main/images/resources/seal-prov-lampung.png') ?>'">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Media Sosial & Footer -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header border-bottom py-16 px-24">
                    <h6 class="text-md fw-semibold mb-0 text-primary-light d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:global-bold" class="text-info-600 text-xl"></iconify-icon>
                        Media Sosial &amp; Teks Footer
                    </h6>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6"><i class="fab fa-facebook text-primary-600 me-1"></i> Facebook URL</label>
                            <input type="text" name="social[facebook_url]" class="form-control radius-8" value="<?= esc($social['facebook_url'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6"><i class="fab fa-instagram text-danger-600 me-1"></i> Instagram URL</label>
                            <input type="text" name="social[instagram_url]" class="form-control radius-8" value="<?= esc($social['instagram_url'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6"><i class="fab fa-youtube text-danger-600 me-1"></i> YouTube URL</label>
                            <input type="text" name="social[youtube_url]" class="form-control radius-8" value="<?= esc($social['youtube_url'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6"><i class="fab fa-linkedin text-info-600 me-1"></i> LinkedIn URL</label>
                            <input type="text" name="social[linkedin_url]" class="form-control radius-8" value="<?= esc($social['linkedin_url'] ?? '') ?>">
                        </div>
                        <div class="col-12"><hr class="my-1"></div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Teks Hak Cipta (Footer Copyright)</label>
                            <input type="text" name="footer[copyright_text]" class="form-control radius-8" value="<?= esc($footer['copyright_text'] ?? 'Copyright © {year} BegawiPengadaan. Pemerintah Provinsi Lampung.') ?>">
                            <small class="text-secondary-light">Gunakan <code>{year}</code> untuk tahun berjalan otomatis.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Slogan Footer</label>
                            <input type="text" name="footer[subtext]" class="form-control radius-8" value="<?= esc($footer['subtext'] ?? 'Pengadaan kompeten, transparan, dan berdampak.') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Visi & Misi Strategis -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header border-bottom py-16 px-24">
                    <h6 class="text-md fw-semibold mb-0 text-primary-light d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:eye-bold" class="text-warning-main text-xl"></iconify-icon>
                        Visi &amp; 5 Misi Strategis
                    </h6>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-16">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Judul Periode Visi</label>
                            <input type="text" name="vision_mission[vision_title]" class="form-control radius-8" value="<?= esc($visionMission['vision_title'] ?? 'Visi Utama 2024–2029') ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Fokus Tag Badge</label>
                            <input type="text" name="vision_mission[vision_focus]" class="form-control radius-8" value="<?= esc($visionMission['vision_focus'] ?? 'Fokus: Kematangan Proaktif & E-Katalog Lokal') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Pernyataan Visi Utama</label>
                            <textarea name="vision_mission[vision_statement]" class="form-control radius-8" rows="3"><?= esc($visionMission['vision_statement'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Daftar Misi Strategis (1 baris per misi)</label>
                            <?php
                            $missionRaw = $visionMission['mission_items'] ?? '[]';
                            $missions = json_decode($missionRaw, true) ?: [];
                            $missionText = implode("\n", $missions);
                            ?>
                            <textarea name="vision_mission[mission_items_raw]" class="form-control radius-8" rows="6" placeholder="Ketik 1 poin misi per baris"><?= esc($missionText) ?></textarea>
                            <small class="text-secondary-light">Tiap baris akan ditampilkan sebagai item berbutir pada halaman Tentang Kami.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Filosofi "Begawi" & Maklumat Pelayanan -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header border-bottom py-16 px-24">
                    <h6 class="text-md fw-semibold mb-0 text-primary-light d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:diploma-verified-bold" class="text-success-600 text-xl"></iconify-icon>
                        Filosofi "Begawi" &amp; Maklumat Pelayanan
                    </h6>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-16">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Judul Bagian Filosofi</label>
                            <input type="text" name="philosophy[philosophy_title]" class="form-control radius-8" value="<?= esc($philosophy['philosophy_title'] ?? 'Makna "Begawi" dalam Pengadaan Modern') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-sm mb-6">Paragraf 1 Filosofi</label>
                            <textarea name="philosophy[philosophy_p1]" class="form-control radius-8" rows="3"><?= esc($philosophy['philosophy_p1'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-sm mb-6">Paragraf 2 Filosofi</label>
                            <textarea name="philosophy[philosophy_p2]" class="form-control radius-8" rows="3"><?= esc($philosophy['philosophy_p2'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-sm mb-6">Kutipan / Quote Kartu Kanan Filosofi</label>
                            <textarea name="philosophy[philosophy_quote]" class="form-control radius-8" rows="2"><?= esc($philosophy['philosophy_quote'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12"><hr class="my-1"></div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-sm mb-6">Maklumat Pelayanan Publik (Janji Integritas)</label>
                            <textarea name="pledge[maklumat_pelayanan]" class="form-control radius-8" rows="3"><?= esc($pledge['maklumat_pelayanan'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <div class="card">
                <div class="card-body p-20 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <p class="text-xs text-secondary-light mb-0">Semua perubahan pada form ini langsung disinkronkan ke basis data <code>site_settings</code> dan tampilan publik.</p>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-12 fw-semibold">
                        <iconify-icon icon="solar:diskette-bold" class="me-1"></iconify-icon>
                        Simpan Seluruh Pengaturan Profil
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
function previewImage(input, previewImgId) {
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

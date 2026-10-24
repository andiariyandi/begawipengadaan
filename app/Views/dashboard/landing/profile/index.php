<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Header Page -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Profil Lembaga, Visi Misi & Filosofi') ?></h6>
        <p class="text-secondary-light mb-0">Kelola identitas resmi Biro PBJ Pemprov Lampung, logo, cap lembaga, filosofi "Begawi", 5 misi strategis, serta maklumat pelayanan.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#profileSimulatorModal">
            <iconify-icon icon="solar:eye-bold" class="text-xl"></iconify-icon>
            <span>Pratinjau Publik</span>
        </button>
        <button type="button" class="btn btn-outline-warning-600 radius-8 px-16 py-10 d-flex align-items-center gap-2" id="btnResetDefault">
            <iconify-icon icon="solar:restart-bold" class="text-xl"></iconify-icon>
            <span>Pulihkan Standar</span>
        </button>
        <button type="button" class="btn btn-primary-600 radius-8 px-20 py-10 d-flex align-items-center gap-2" id="btnTopSave">
            <iconify-icon icon="solar:diskette-bold" class="text-xl"></iconify-icon>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'profile', 'activeSub' => 'profil-lembaga']) ?>

<!-- KPI Metric Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Kelengkapan Profil</p>
                        <h6 class="mb-0 fw-bold <?= ($stats['completion_pct'] >= 90) ? 'text-success-main' : 'text-warning-main' ?>">
                            <?= $stats['completion_pct'] ?>%
                        </h6>
                        <small class="text-secondary-light text-xs"><?= $stats['completed_count'] ?> dari <?= $stats['total_check'] ?> indikator terisi</small>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:shield-check-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Media Sosial</p>
                        <h6 class="mb-0 fw-bold text-info-main"><?= $stats['social_count'] ?> Akun</h6>
                        <small class="text-secondary-light text-xs">Terhubung ke kanal publik</small>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:global-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Misi Strategis PBJ</p>
                        <h6 class="mb-0 fw-bold text-primary-main"><?= $stats['mission_count'] ?> Pilar Aksi</h6>
                        <small class="text-secondary-light text-xs">Pernyataan rencana strategis</small>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:checklist-minimalistic-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Sinkronisasi Terakhir</p>
                        <h6 class="mb-0 fw-bold text-secondary-900 text-sm">
                            <?= ! empty($stats['last_updated']) ? date('d M Y, H:i', strtotime($stats['last_updated'])) : 'Aktif & Sah' ?>
                        </h6>
                        <small class="text-secondary-light text-xs">Basis data <code>site_settings</code></small>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="institutionProfileForm" method="post" action="<?= site_url('dashboard/landing/profil-lembaga') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- Sub-Nav Tab Pills for Profile Sections -->
    <div class="card mb-24 border">
        <div class="card-body p-12">
            <ul class="nav nav-pills gap-2 flex-wrap" id="profileTabList" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link radius-8 px-20 py-10 active d-flex align-items-center gap-2 text-sm fw-medium" 
                            id="tab-identity" data-bs-toggle="pill" data-bs-target="#pane-identity" type="button" role="tab" aria-selected="true">
                        <iconify-icon icon="solar:city-bold" class="text-lg"></iconify-icon>
                        <span>1. Identitas &amp; Logo Lembaga</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link radius-8 px-20 py-10 d-flex align-items-center gap-2 text-sm fw-medium" 
                            id="tab-vision" data-bs-toggle="pill" data-bs-target="#pane-vision" type="button" role="tab" aria-selected="false">
                        <iconify-icon icon="solar:eye-bold" class="text-lg"></iconify-icon>
                        <span>2. Visi &amp; 5 Misi Strategis</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link radius-8 px-20 py-10 d-flex align-items-center gap-2 text-sm fw-medium" 
                            id="tab-philosophy" data-bs-toggle="pill" data-bs-target="#pane-philosophy" type="button" role="tab" aria-selected="false">
                        <iconify-icon icon="solar:diploma-verified-bold" class="text-lg"></iconify-icon>
                        <span>3. Filosofi "Begawi" &amp; Maklumat</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link radius-8 px-20 py-10 d-flex align-items-center gap-2 text-sm fw-medium" 
                            id="tab-social" data-bs-toggle="pill" data-bs-target="#pane-social" type="button" role="tab" aria-selected="false">
                        <iconify-icon icon="solar:global-bold" class="text-lg"></iconify-icon>
                        <span>4. Media Sosial &amp; Footer</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content Container -->
    <div class="tab-content" id="profileTabContent">

        <!-- ========================================== -->
        <!-- TAB 1: IDENTITAS & LOGO RESMI LEMBAGA      -->
        <!-- ========================================== -->
        <div class="tab-pane fade show active" id="pane-identity" role="tabpanel" aria-labelledby="tab-identity">
            <div class="row g-24">
                <!-- Info Identitas Lembaga -->
                <div class="col-lg-7">
                    <div class="card border h-100">
                        <div class="card-header bg-white border-bottom p-20">
                            <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:city-bold" class="text-primary-600 text-xl"></iconify-icon>
                                <span>Informasi Kelembagaan Biro PBJ</span>
                            </h6>
                        </div>
                        <div class="card-body p-24">
                            <div class="row gy-20">
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Nama Resmi Instansi <span class="text-danger">*</span></label>
                                    <input type="text" name="organization[agency_name]" id="inpAgencyName" class="form-control radius-8 fw-semibold" 
                                           value="<?= esc($organization['agency_name'] ?? 'Biro Pengadaan Barang & Jasa Sekretariat Daerah Provinsi Lampung') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Pemerintah Daerah / Induk Organisasi</label>
                                    <input type="text" name="organization[provincial_unit]" class="form-control radius-8" 
                                           value="<?= esc($organization['provincial_unit'] ?? 'Pemerintah Provinsi Lampung') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Kode Singkat Instansi</label>
                                    <input type="text" name="organization[agency_code]" class="form-control radius-8 font-monospace" 
                                           value="<?= esc($organization['agency_code'] ?? 'PEMPROV-LPG') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Predikat Kematangan UKPBJ</label>
                                    <input type="text" name="organization[institution_level]" class="form-control radius-8" 
                                           value="<?= esc($organization['institution_level'] ?? 'UKPBJ Level Proaktif (Tingkat Kematangan Proaktif)') ?>">
                                    <small class="text-secondary-light text-2xs d-block mt-4">Standar maturitas kelembagaan LKPP RI.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Status Zona Integritas</label>
                                    <input type="text" name="organization[institution_zone]" class="form-control radius-8" 
                                           value="<?= esc($organization['institution_zone'] ?? 'Zona Integritas Wilayah Bebas Korupsi & Gratifikasi') ?>">
                                    <small class="text-secondary-light text-2xs d-block mt-4">Komitmen antikorupsi MenPAN-RB.</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Alamat Kantor Resmi</label>
                                    <textarea name="organization[address]" id="inpAddress" class="form-control radius-8" rows="2"><?= esc($organization['address'] ?? 'Jl. Wolter Monginsidi No. 69, Teluk Betung, Kota Bandar Lampung, Lampung 35211') ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Telepon / WhatsApp Layanan</label>
                                    <input type="text" name="organization[phone]" id="inpPhone" class="form-control radius-8" 
                                           value="<?= esc($organization['phone'] ?? '+62 851-1777-9783') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Email Resmi Lembaga</label>
                                    <input type="email" name="organization[email]" id="inpEmail" class="form-control radius-8" 
                                           value="<?= esc($organization['email'] ?? 'biropbj@lampungprov.go.id') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Jam Operasional Pelayanan</label>
                                    <input type="text" name="organization[operational_hours]" class="form-control radius-8" 
                                           value="<?= esc($organization['operational_hours'] ?? 'Senin - Jumat, 08.00 - 16.00 WIB') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Tautan Google Maps</label>
                                    <input type="text" name="organization[google_maps_url]" class="form-control radius-8" 
                                           value="<?= esc($organization['google_maps_url'] ?? 'https://maps.google.com/?q=Biro+Pengadaan+Barang+dan+Jasa+Provinsi+Lampung') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian Upload Logo & Stempel Cap Lembaga -->
                <div class="col-lg-5">
                    <div class="card border h-100">
                        <div class="card-header bg-white border-bottom p-20">
                            <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:gallery-bold" class="text-primary-600 text-xl"></iconify-icon>
                                <span>Logo &amp; Stempel Cap Resmi</span>
                            </h6>
                        </div>
                        <div class="card-body p-24">
                            <!-- Logo Upload Box -->
                            <div class="p-16 radius-12 border bg-neutral-50 mb-20">
                                <label class="form-label fw-bold text-primary-light text-sm mb-8 d-flex align-items-center justify-content-between">
                                    <span>Logo Resmi BegawiPengadaan</span>
                                    <span class="badge bg-primary-100 text-primary-600 text-2xs px-8 py-3 radius-4">Header &amp; Portal</span>
                                </label>
                                <div class="text-center p-16 bg-white radius-8 border mb-12">
                                    <?php
                                    $curLogo = $organization['logo_url'] ?? 'assets/main/images/resources/logo-1.png';
                                    $curLogoSrc = str_starts_with($curLogo, 'http') ? $curLogo : base_url($curLogo);
                                    ?>
                                    <img id="logoPreviewImg" src="<?= esc($curLogoSrc) ?>" alt="Logo Instansi" 
                                         style="max-height: 54px; max-width: 180px; object-fit: contain;" 
                                         onerror="this.src='<?= base_url('assets/main/images/resources/logo-1.png') ?>'">
                                </div>
                                <input type="file" name="logo_file" id="logoFileInput" class="form-control radius-8 text-sm" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                                <small class="text-secondary-light text-2xs d-block mt-4">Disarankan format PNG transparan, rasio horizontal, maks. 5MB.</small>

                                <div class="mt-8">
                                    <a class="text-2xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseLogoUrl">
                                        <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                                        <span>Gunakan path file statis</span>
                                        <iconify-icon icon="solar:alt-arrow-down-linear"></iconify-icon>
                                    </a>
                                    <div class="collapse mt-6" id="collapseLogoUrl">
                                        <input type="text" name="organization[logo_url]" id="orgLogoUrlInput" class="form-control radius-8 text-xs font-monospace" 
                                               value="<?= esc($organization['logo_url'] ?? 'assets/main/images/resources/logo-1.png') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Official Seal Upload Box -->
                            <div class="p-16 radius-12 border bg-neutral-50">
                                <label class="form-label fw-bold text-primary-light text-sm mb-8 d-flex align-items-center justify-content-between">
                                    <span>Cap / Stempel Resmi Lembaga</span>
                                    <span class="badge bg-success-100 text-success-600 text-2xs px-8 py-3 radius-4">Sertifikat &amp; Dokumen</span>
                                </label>
                                <div class="text-center p-16 bg-white radius-8 border mb-12">
                                    <?php
                                    $curSeal = $organization['seal_url'] ?? 'assets/main/images/resources/seal-prov-lampung.png';
                                    $curSealSrc = str_starts_with($curSeal, 'http') ? $curSeal : base_url($curSeal);
                                    ?>
                                    <img id="sealPreviewImg" src="<?= esc($curSealSrc) ?>" alt="Cap Lembaga" 
                                         style="max-height: 64px; max-width: 64px; object-fit: contain;" 
                                         onerror="this.src='<?= base_url('assets/main/images/resources/seal-prov-lampung.png') ?>'">
                                </div>
                                <input type="file" name="seal_file" id="sealFileInput" class="form-control radius-8 text-sm" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                                <small class="text-secondary-light text-2xs d-block mt-4">Disarankan PNG transparan melingkar lambang Lampung, maks. 5MB.</small>

                                <div class="mt-8">
                                    <a class="text-2xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseSealUrl">
                                        <iconify-icon icon="solar:link-circle-outline"></iconify-icon>
                                        <span>Gunakan path stempel statis</span>
                                        <iconify-icon icon="solar:alt-arrow-down-linear"></iconify-icon>
                                    </a>
                                    <div class="collapse mt-6" id="collapseSealUrl">
                                        <input type="text" name="organization[seal_url]" id="orgSealUrlInput" class="form-control radius-8 text-xs font-monospace" 
                                               value="<?= esc($organization['seal_url'] ?? 'assets/main/images/resources/seal-prov-lampung.png') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: VISI & 5 MISI STRATEGIS             -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="pane-vision" role="tabpanel" aria-labelledby="tab-vision">
            <div class="card border">
                <div class="card-header bg-white border-bottom p-20 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:eye-bold" class="text-warning-main text-xl"></iconify-icon>
                        <span>Arah Strategis: Visi Utama &amp; 5 Misi Pengadaan</span>
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-flex align-items-center gap-1 text-xs" id="btnLoadDefaultMissions">
                        <iconify-icon icon="solar:magic-stick-3-bold" class="text-base"></iconify-icon>
                        <span>Muat 5 Misi Standar PBJ</span>
                    </button>
                </div>
                <div class="card-body p-24">
                    <div class="row gy-20">
                        <!-- Periode & Fokus Tag -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Judul Periode Visi</label>
                            <input type="text" name="vision_mission[vision_title]" class="form-control radius-8" 
                                   value="<?= esc($visionMission['vision_title'] ?? 'Visi Utama 2024–2029') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-6">Fokus Tag Badge</label>
                            <input type="text" name="vision_mission[vision_focus]" class="form-control radius-8" 
                                   value="<?= esc($visionMission['vision_focus'] ?? 'Fokus: Kematangan Proaktif & E-Katalog Lokal') ?>">
                        </div>

                        <!-- Pernyataan Visi Utama -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-6">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Pernyataan Visi Utama Biro PBJ <span class="text-danger">*</span>
                                </label>
                                <span class="text-2xs text-secondary-light" id="visionCharCount">0/400</span>
                            </div>
                            <textarea name="vision_mission[vision_statement]" id="inpVisionStatement" class="form-control radius-8 fw-medium" rows="3" required><?= esc($visionMission['vision_statement'] ?? 'Terwujudnya Tata Kelola Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung yang Profesional, Transparan, Akuntabel, dan Menjadi Penggerak Utama Pertumbuhan Ekonomi Lokal yang Inklusif.') ?></textarea>
                            <small class="text-secondary-light text-2xs d-block mt-4">Visi ini akan ditampilkan secara menonjol pada blok utama Tentang Kami.</small>
                        </div>

                        <!-- Daftar Misi Strategis (Structured line-by-line) -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-6">
                                <label class="form-label fw-semibold text-primary-light text-sm mb-0">
                                    Daftar Misi Strategis (1 Baris per Misi)
                                </label>
                                <span class="badge bg-primary-100 text-primary-600 radius-4 text-2xs px-8 py-3 fw-semibold">
                                    Format 1 Baris = 1 Butir Misi
                                </span>
                            </div>
                            <?php
                            $missionRaw = $visionMission['mission_items'] ?? '[]';
                            $missions = json_decode((string) $missionRaw, true) ?: [];
                            if (empty($missions) && ! empty($missionRaw) && is_string($missionRaw)) {
                                $missions = array_filter(array_map('trim', explode("\n", $missionRaw)));
                            }
                            $missionText = implode("\n", $missions);
                            ?>
                            <textarea name="vision_mission[mission_items_raw]" id="inpMissionsRaw" class="form-control radius-8 font-monospace text-sm" rows="8" placeholder="Ketik 1 baris untuk tiap butir misi..."><?= esc($missionText) ?></textarea>
                            <small class="text-secondary-light text-2xs d-block mt-6">
                                Disarankan 5 butir misi strategis. Sistem akan otomatis menyusunnya dalam daftar kartu bernomor di halaman publik.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: FILOSOFI "BEGAWI" & MAKLUMAT        -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="pane-philosophy" role="tabpanel" aria-labelledby="tab-philosophy">
            <div class="row g-24">
                <!-- Filosofi Begawi -->
                <div class="col-lg-7">
                    <div class="card border h-100">
                        <div class="card-header bg-white border-bottom p-20">
                            <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:diploma-verified-bold" class="text-success-600 text-xl"></iconify-icon>
                                <span>Filosofi &amp; Kearifan Budaya "Begawi"</span>
                            </h6>
                        </div>
                        <div class="card-body p-24">
                            <div class="row gy-20">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Judul Bagian Filosofi</label>
                                    <input type="text" name="philosophy[philosophy_title]" class="form-control radius-8" 
                                           value="<?= esc($philosophy['philosophy_title'] ?? 'Makna "Begawi" dalam Pengadaan Modern') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Tag Badge</label>
                                    <input type="text" name="philosophy[philosophy_tag]" class="form-control radius-8" 
                                           value="<?= esc($philosophy['philosophy_tag'] ?? 'Filosofi Daerah') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Paragraf 1: Asal Usul &amp; Makna Adat</label>
                                    <textarea name="philosophy[philosophy_p1]" class="form-control radius-8" rows="3"><?= esc($philosophy['philosophy_p1'] ?? 'Kata "Begawi" berakar dari kearifan bahasa dan adat budaya Lampung yang mencerminkan etos kerja luhur, bergotong-royong, dan mengabdi dengan penuh kesungguhan serta kehormatan.') ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Paragraf 2: Transformasi Digital Pengadaan</label>
                                    <textarea name="philosophy[philosophy_p2]" class="form-control radius-8" rows="3"><?= esc($philosophy['philosophy_p2'] ?? 'Dalam konteks pengadaan barang dan jasa modern, BegawiPengadaan hadir mengintegrasikan semangat pengabdian tersebut dengan teknologi digital tingkat lanjut. Tujuannya adalah menjadikan seluruh proses pengadaan di Provinsi Lampung tidak sekadar taat administratif, melainkan memberikan nilai manfaat nyata bagi kemakmuran masyarakat Bumi Ruwa Jurai.') ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Kutipan Kartu Kanan Filosofi (Quote Emas)</label>
                                    <textarea name="philosophy[philosophy_quote]" class="form-control radius-8 font-monospace text-xs" rows="2"><?= esc($philosophy['philosophy_quote'] ?? '"Pengadaan Barang dan Jasa bukan sekadar membelanjakan anggaran negara, melainkan instrumen strategis pembangunan yang menggerakkan ekonomi lokal dan mensejahterakan rakyat."') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maklumat Pelayanan Publik -->
                <div class="col-lg-5">
                    <div class="card border h-100">
                        <div class="card-header bg-white border-bottom p-20">
                            <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:shield-check-bold" class="text-warning-main text-xl"></iconify-icon>
                                <span>Maklumat Pelayanan &amp; Integritas</span>
                            </h6>
                        </div>
                        <div class="card-body p-24">
                            <div class="row gy-20">
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Pernyataan Janji Integritas Pimpinan &amp; Aparatur</label>
                                    <textarea name="pledge[maklumat_pelayanan]" class="form-control radius-8 fst-italic" rows="7"><?= esc($pledge['maklumat_pelayanan'] ?? '"Dengan ini, kami pimpinan dan seluruh aparatur Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung menyatakan sanggup menyelenggarakan pelayanan pengadaan barang dan jasa sesuai standar pelayanan yang telah ditetapkan, memberikan pelayanan yang profesional, transparan, akuntabel, bebas dari gratifikasi dan korupsi, serta siap menerima sanksi sesuai peraturan perundang-undangan apabila tidak menepati janji ini."') ?></textarea>
                                </div>
                                <div class="col-12"><hr class="my-2"></div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">3 Poin Komitmen Utama</label>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-success-100 text-success-600 border-end-0"><iconify-icon icon="solar:check-circle-bold"></iconify-icon></span>
                                            <input type="text" name="pledge[point_1]" class="form-control" value="<?= esc($pledge['point_1'] ?? 'Layanan Cepat 1x24 Jam') ?>" placeholder="Poin 1">
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-danger-100 text-danger-600 border-end-0"><iconify-icon icon="solar:hand-money-bold"></iconify-icon></span>
                                            <input type="text" name="pledge[point_2]" class="form-control" value="<?= esc($pledge['point_2'] ?? 'Bebas Biaya (Tanpa Pungli)') ?>" placeholder="Poin 2">
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-primary-100 text-primary-600 border-end-0"><iconify-icon icon="solar:lock-keyhole-bold"></iconify-icon></span>
                                            <input type="text" name="pledge[point_3]" class="form-control" value="<?= esc($pledge['point_3'] ?? 'Perlindungan Kerahasiaan Pemohon') ?>" placeholder="Poin 3">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: MEDIA SOSIAL & FOOTER PUBLIK        -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="pane-social" role="tabpanel" aria-labelledby="tab-social">
            <div class="row g-24">
                <!-- Tautan Kanal Media Sosial -->
                <div class="col-lg-6">
                    <div class="card border h-100">
                        <div class="card-header bg-white border-bottom p-20">
                            <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:global-bold" class="text-info-600 text-xl"></iconify-icon>
                                <span>Kanal Media Sosial Resmi Biro PBJ</span>
                            </h6>
                        </div>
                        <div class="card-body p-24">
                            <div class="row gy-16">
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                        <i class="fab fa-facebook text-primary-600 me-2"></i> Facebook URL
                                    </label>
                                    <input type="text" name="social[facebook_url]" class="form-control radius-8 font-monospace text-sm" 
                                           value="<?= esc($social['facebook_url'] ?? 'https://facebook.com/biropbjlampung') ?>" placeholder="https://facebook.com/...">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                        <i class="fab fa-instagram text-danger-600 me-2"></i> Instagram URL
                                    </label>
                                    <input type="text" name="social[instagram_url]" class="form-control radius-8 font-monospace text-sm" 
                                           value="<?= esc($social['instagram_url'] ?? 'https://instagram.com/biropbj_lampung') ?>" placeholder="https://instagram.com/...">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                        <i class="fab fa-youtube text-danger-600 me-2"></i> YouTube Channel URL
                                    </label>
                                    <input type="text" name="social[youtube_url]" class="form-control radius-8 font-monospace text-sm" 
                                           value="<?= esc($social['youtube_url'] ?? 'https://youtube.com/@biropbjsetdaprovinsilampung') ?>" placeholder="https://youtube.com/...">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                        <i class="fab fa-linkedin text-info-600 me-2"></i> LinkedIn URL
                                    </label>
                                    <input type="text" name="social[linkedin_url]" class="form-control radius-8 font-monospace text-sm" 
                                           value="<?= esc($social['linkedin_url'] ?? 'https://linkedin.com/company/biropbj-lampung') ?>" placeholder="https://linkedin.com/...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                        <i class="fab fa-x-twitter text-dark me-2"></i> X / Twitter URL
                                    </label>
                                    <input type="text" name="social[twitter_url]" class="form-control radius-8 font-monospace text-sm" 
                                           value="<?= esc($social['twitter_url'] ?? '') ?>" placeholder="https://x.com/...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">
                                        <i class="fab fa-tiktok text-dark me-2"></i> TikTok URL
                                    </label>
                                    <input type="text" name="social[tiktok_url]" class="form-control radius-8 font-monospace text-sm" 
                                           value="<?= esc($social['tiktok_url'] ?? '') ?>" placeholder="https://tiktok.com/@...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Teks & Hak Cipta -->
                <div class="col-lg-6">
                    <div class="card border h-100">
                        <div class="card-header bg-white border-bottom p-20">
                            <h6 class="fw-bold text-primary-light mb-0 d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:document-text-bold" class="text-primary-600 text-xl"></iconify-icon>
                                <span>Teks Footer &amp; Hak Cipta Publik</span>
                            </h6>
                        </div>
                        <div class="card-body p-24">
                            <div class="row gy-20">
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Teks Hak Cipta (Footer Copyright)</label>
                                    <input type="text" name="footer[copyright_text]" class="form-control radius-8" 
                                           value="<?= esc($footer['copyright_text'] ?? 'Copyright © {year} BegawiPengadaan. Pemerintah Provinsi Lampung.') ?>">
                                    <small class="text-secondary-light text-2xs d-block mt-4">
                                        Gunakan penanda <code>{year}</code> agar angka tahun diperbarui secara otomatis.
                                    </small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-6">Slogan Footer (Subtext)</label>
                                    <input type="text" name="footer[subtext]" class="form-control radius-8" 
                                           value="<?= esc($footer['subtext'] ?? 'Pengadaan kompeten, transparan, dan berdampak.') ?>">
                                    <small class="text-secondary-light text-2xs d-block mt-4">Tagline ringkas yang muncul mendampingi teks copyright di kaki halaman.</small>
                                </div>
                                <div class="col-12">
                                    <div class="p-16 radius-8 bg-neutral-50 border">
                                        <h6 class="text-xs fw-bold text-primary-light mb-8">Pratinjau Kaki Halaman (Footer Bar):</h6>
                                        <div class="p-12 bg-neutral-900 text-white radius-8 text-center text-xs">
                                            <p class="mb-1 text-light opacity-75">
                                                Copyright © <?= date('Y') ?> BegawiPengadaan. Pemerintah Provinsi Lampung.
                                            </p>
                                            <p class="mb-0 text-warning text-2xs">
                                                Pengadaan kompeten, transparan, dan berdampak.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Submit Bar Sticky -->
    <div class="card border mt-24">
        <div class="card-body p-20 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <iconify-icon icon="solar:shield-check-bold" class="text-success-main text-2xl"></iconify-icon>
                <div>
                    <h6 class="fw-semibold text-primary-light mb-0 text-sm">Sinkronisasi Basis Data Site Settings</h6>
                    <small class="text-secondary-light text-2xs">Perubahan pada seluruh tab di atas akan langsung aktif di seluruh portal beranda, tentang kami, dan sertifikat.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary-600 radius-8 px-28 py-12 fw-semibold d-flex align-items-center gap-2" id="btnBottomSave">
                    <iconify-icon icon="solar:diskette-bold" class="text-xl"></iconify-icon>
                    <span>Simpan Seluruh Pengaturan Profil</span>
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Reset Default Confirmation Form -->
<form id="resetDefaultForm" method="post" action="<?= site_url('dashboard/landing/profil-lembaga/reset') ?>" class="d-none">
    <?= csrf_field() ?>
</form>

<!-- Public Profile & About Simulator Modal -->
<div class="modal fade" id="profileSimulatorModal" tabindex="-1" aria-labelledby="profileSimulatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content radius-16 border-0 shadow-lg">
            <div class="modal-header bg-neutral-50 px-24 py-16 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="w-36-px h-36-px rounded-8 bg-primary-600 d-flex align-items-center justify-content-center text-white">
                        <iconify-icon icon="solar:city-bold" class="text-xl"></iconify-icon>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-primary-light mb-0" id="profileSimulatorModalLabel">
                            Pratinjau Publik: Halaman Tentang Kami &amp; Profil Resmi
                        </h6>
                        <small class="text-secondary-light">Tampilan live sebagaimana pengunjung melihatnya di portal resmi BegawiPengadaan.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-32 bg-light">
                <!-- 1. Header Hero Simulation -->
                <div class="card border-0 shadow-sm radius-12 p-24 mb-24 bg-white">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom pb-16 mb-20">
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?= esc($curLogoSrc) ?>" alt="Logo" style="max-height: 44px; object-fit: contain;">
                            <div>
                                <h6 class="fw-bold mb-0 text-primary-light" id="simAgencyName"><?= esc($organization['agency_name'] ?? 'Biro Pengadaan Barang & Jasa') ?></h6>
                                <small class="text-secondary-light"><?= esc($organization['provincial_unit'] ?? 'Pemerintah Provinsi Lampung') ?></small>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark px-12 py-6 fw-bold radius-4">
                            <?= esc($organization['institution_level'] ?? 'UKPBJ Level Proaktif') ?>
                        </span>
                    </div>

                    <!-- 2. Visi Statement Box -->
                    <div class="p-20 radius-8 border mb-20" style="background: #f8fafc; border-left: 4px solid #0c5aa6 !important;">
                        <span class="badge bg-primary-100 text-primary-600 px-8 py-3 radius-4 text-2xs fw-bold mb-8">
                            <iconify-icon icon="solar:eye-bold" class="me-1"></iconify-icon>
                            <?= esc($visionMission['vision_title'] ?? 'Visi Utama 2024–2029') ?>
                        </span>
                        <h5 class="fw-bold text-neutral-900 mb-8 fst-italic" id="simVisionStatement">
                            "<?= esc($visionMission['vision_statement'] ?? '') ?>"
                        </h5>
                        <small class="text-secondary-light fw-medium">
                            <?= esc($visionMission['vision_focus'] ?? 'Fokus: Kematangan Proaktif & E-Katalog Lokal') ?>
                        </small>
                    </div>

                    <!-- 3. Missions Grid -->
                    <div class="mb-20">
                        <h6 class="fw-bold text-primary-light mb-12 d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:checklist-minimalistic-bold" class="text-primary-600"></iconify-icon>
                            <span>5 Misi Strategis UKPBJ Lampung</span>
                        </h6>
                        <div class="row g-12" id="simMissionsList">
                            <?php foreach ($missions as $idx => $m): ?>
                                <div class="col-12">
                                    <div class="p-12 radius-8 bg-neutral-50 border d-flex align-items-start gap-3">
                                        <span class="badge bg-primary-600 text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 11px;">
                                            <?= $idx + 1 ?>
                                        </span>
                                        <p class="text-xs text-secondary-light mb-0" style="line-height: 1.5;"><?= esc($m) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 4. Maklumat Pelayanan Box -->
                    <div class="p-20 radius-8 border bg-neutral-900 text-white text-center">
                        <span class="badge bg-warning text-dark px-10 py-4 radius-4 text-2xs fw-bold mb-8">
                            MAKLUMAT PELAYANAN PUBLIK
                        </span>
                        <h6 class="text-white fw-bold mb-8">Janji Integritas Pelayanan UKPBJ Lampung</h6>
                        <p class="text-light opacity-75 text-xs fst-italic mb-12 max-w-700 mx-auto" id="simPledgeStatement">
                            <?= esc($pledge['maklumat_pelayanan'] ?? '') ?>
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-3 text-2xs text-light opacity-90 fw-semibold">
                            <span><iconify-icon icon="solar:check-circle-bold" class="text-success-main me-1"></iconify-icon> Layanan Cepat 1x24 Jam</span>
                            <span><iconify-icon icon="solar:hand-money-bold" class="text-danger-main me-1"></iconify-icon> Bebas Biaya (Tanpa Pungli)</span>
                            <span><iconify-icon icon="solar:lock-keyhole-bold" class="text-primary-main me-1"></iconify-icon> Perlindungan Kerahasiaan Pemohon</span>
                        </div>
                    </div>
                </div>

                <!-- 5. Footer Simulation -->
                <div class="card border-0 shadow-sm radius-12 p-20 bg-white">
                    <h6 class="fw-bold text-xs text-secondary-light mb-12">Pratinjau Kaki Halaman (Footer Live):</h6>
                    <div class="p-16 radius-8 bg-neutral-900 text-white">
                        <div class="row align-items-center gy-12">
                            <div class="col-md-6">
                                <p class="text-xs mb-1 text-light opacity-90" id="simAddressText">
                                    <iconify-icon icon="solar:map-point-bold" class="me-1 text-primary-400"></iconify-icon>
                                    <?= esc($organization['address'] ?? '') ?>
                                </p>
                                <p class="text-xs mb-0 text-light opacity-75">
                                    <iconify-icon icon="solar:phone-bold" class="me-1 text-success-main"></iconify-icon> <?= esc($organization['phone'] ?? '') ?> &bull; 
                                    <iconify-icon icon="solar:letter-bold" class="me-1 text-warning-main"></iconify-icon> <?= esc($organization['email'] ?? '') ?>
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end text-xs">
                                <p class="mb-1 text-light opacity-90">
                                    <?= str_replace('{year}', date('Y'), esc($footer['copyright_text'] ?? 'Copyright © {year} BegawiPengadaan.')) ?>
                                </p>
                                <small class="text-warning text-2xs"><?= esc($footer['subtext'] ?? 'Pengadaan kompeten, transparan, dan berdampak.') ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white px-24 py-16 border-top">
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-8" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Image Preview Helper
    function handleFilePreview(input, imgElementId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.getElementById(imgElementId);
                if (img) img.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    const logoInput = document.getElementById('logoFileInput');
    if (logoInput) {
        logoInput.addEventListener('change', function () {
            handleFilePreview(this, 'logoPreviewImg');
        });
    }

    const sealInput = document.getElementById('sealFileInput');
    if (sealInput) {
        sealInput.addEventListener('change', function () {
            handleFilePreview(this, 'sealPreviewImg');
        });
    }

    // 2. Character counters
    const visionInput = document.getElementById('inpVisionStatement');
    const visionCounter = document.getElementById('visionCharCount');
    if (visionInput && visionCounter) {
        const updateVisionCount = () => {
            visionCounter.textContent = `${visionInput.value.length}/400`;
        };
        visionInput.addEventListener('input', updateVisionCount);
        updateVisionCount();
    }

    // 3. Top and Bottom Save Buttons trigger the form
    const form = document.getElementById('institutionProfileForm');
    const btnTopSave = document.getElementById('btnTopSave');
    if (btnTopSave && form) {
        btnTopSave.addEventListener('click', function () {
            form.submit();
        });
    }

    // 4. Load Default 5 Missions Button
    const btnLoadMissions = document.getElementById('btnLoadDefaultMissions');
    const missionsInput = document.getElementById('inpMissionsRaw');
    if (btnLoadMissions && missionsInput) {
        btnLoadMissions.addEventListener('click', function () {
            const defaultMissions = [
                'Peningkatan Kompetensi Berkelanjutan: Mengembangkan kapasitas SDM PBJ melalui Arena Belajar digital, bimtek teknis, dan sertifikasi terstandarisasi.',
                'Penguatan Tata Kelola & Advokasi: Memberikan layanan pembinaan, pendampingan hukum, dan mitigasi risiko pengadaan bagi seluruh OPD.',
                'Akselerasi E-Purchasing & UMKM: Mengoptimalkan E-Katalog Lokal Lampung dan Toko Daring guna menyerap produk dalam negeri (P3DN).',
                'Pusat Literasi & Regulasi Terbuka: Menjamin kemudahan akses regulasi, standar dokumen, dan telaah hukum melalui Bunga Rampai.',
                'Government Intelligence PBJ: Mengintegrasikan data pengadaan untuk pelaporan analitik, monitoring kinerja, dan pengambilan keputusan pimpinan.'
            ];
            missionsInput.value = defaultMissions.join('\n');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '5 Misi Standar Dimuat',
                    text: 'Daftar 5 misi strategis berhasil dimasukkan ke form.',
                    timer: 1600,
                    showConfirmButton: false
                });
            }
        });
    }

    // 5. Reset to Official Defaults Confirmation
    const btnReset = document.getElementById('btnResetDefault');
    const resetForm = document.getElementById('resetDefaultForm');
    if (btnReset && resetForm) {
        btnReset.addEventListener('click', function () {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Pulihkan Standar Resmi?',
                    text: 'Pengaturan profil lembaga, visi, 5 misi, dan filosofi akan dipulihkan ke standar resmi Biro PBJ Pemprov Lampung.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Pulihkan Sekarang',
                    cancelButtonText: 'Batal'
                }).then((res) => {
                    if (res.isConfirmed) {
                        resetForm.submit();
                    }
                });
            } else {
                if (confirm('Anda yakin ingin memulihkan profil ke standar resmi Biro PBJ Pemprov Lampung?')) {
                    resetForm.submit();
                }
            }
        });
    }

    // 6. Live Simulator Synchronization when modal opened
    const simulatorModal = document.getElementById('profileSimulatorModal');
    if (simulatorModal) {
        simulatorModal.addEventListener('show.bs.modal', function () {
            const agencyNameInput = document.getElementById('inpAgencyName');
            const visionStatementInput = document.getElementById('inpVisionStatement');
            const addressInput = document.getElementById('inpAddress');

            const simAgency = document.getElementById('simAgencyName');
            const simVision = document.getElementById('simVisionStatement');
            const simAddress = document.getElementById('simAddressText');

            if (agencyNameInput && simAgency) simAgency.textContent = agencyNameInput.value;
            if (visionStatementInput && simVision) simVision.textContent = `"${visionStatementInput.value}"`;
            if (addressInput && simAddress) {
                simAddress.innerHTML = `<iconify-icon icon="solar:map-point-bold" class="me-1 text-primary-400"></iconify-icon> ${addressInput.value}`;
            }
        });
    }
});
</script>

<?= $this->endSection() ?>

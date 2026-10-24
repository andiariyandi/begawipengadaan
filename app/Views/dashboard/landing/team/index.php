<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>

<!-- Header Page -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Tim Inovator & Pengembang') ?></h6>
        <p class="text-secondary-light mb-0">Tokoh pengarah strategis daerah, penanggung jawab birokrasi, perekayasa sistem perangkat lunak, dan instruktur ahli PBJ.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-primary-600 radius-8 px-16 py-10 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#teamPreviewModal">
            <iconify-icon icon="solar:eye-bold" class="text-xl"></iconify-icon>
            <span>Pratinjau Publik</span>
        </button>
        <?php if (empty($members)): ?>
            <a href="<?= site_url('dashboard/landing/tim/muat-rekomendasi') ?>" class="btn btn-warning-600 radius-8 px-16 py-10 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:magic-stick-3-bold" class="text-xl"></iconify-icon>
                <span>Muat Rekomendasi Tim</span>
            </a>
        <?php endif; ?>
        <a href="<?= site_url('dashboard/landing/tim/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-10 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-bold" class="text-xl"></iconify-icon>
            <span>Tambah Anggota Tim</span>
        </a>
    </div>
</div>

<!-- Reusable Tabbed Console Navigation -->
<?= view('dashboard/landing/_nav_landing', ['section' => 'team', 'activeSub' => 'tim']) ?>

<!-- KPI Metric Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Tokoh Tim</p>
                        <h6 class="mb-0 fw-bold"><?= number_format($stats['total'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Profil tokoh &amp; kolaborator</small>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:users-group-rounded-bold" class="text-white text-2xl mb-0"></iconify-icon>
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
                        <p class="fw-medium text-primary-light mb-1">Tokoh Aktif</p>
                        <h6 class="mb-0 fw-bold text-success-main"><?= number_format($stats['active_count'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Tayang di carousel beranda</small>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-bold" class="text-white text-2xl mb-0"></iconify-icon>
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
                        <p class="fw-medium text-primary-light mb-1">Draf / Nonaktif</p>
                        <h6 class="mb-0 fw-bold text-warning-main"><?= number_format($stats['inactive_count'] ?? 0) ?></h6>
                        <small class="text-secondary-light text-xs">Disembunyikan sementara</small>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:shield-warning-bold" class="text-white text-2xl mb-0"></iconify-icon>
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
                        <p class="fw-medium text-primary-light mb-1">Kategori Peran</p>
                        <h6 class="mb-0 fw-bold text-primary-main">
                            <?= $stats['pembina_count'] ?? 0 ?> <span class="text-secondary-light fw-normal text-xs">Pem</span> / 
                            <?= $stats['pengembang_count'] ?? 0 ?> <span class="text-secondary-light fw-normal text-xs">Dev</span> / 
                            <?= $stats['narasumber_count'] ?? 0 ?> <span class="text-secondary-light fw-normal text-xs">Ahli</span>
                        </h6>
                        <small class="text-secondary-light text-xs">Komposisi peran kepemimpinan</small>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:diploma-verified-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card Container -->
<div class="card border">
    <div class="card-header bg-white border-bottom p-20">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Filter Pills -->
            <div class="d-flex align-items-center gap-2 flex-wrap" id="teamFilterGroup">
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 active px-12 py-6 filter-btn" data-filter="all">
                    Semua (<span id="countAll"><?= count($members) ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="pembina">
                    Pembina (<span id="countPembina"><?= $stats['pembina_count'] ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="pengembang">
                    Pengembang (<span id="countPengembang"><?= $stats['pengembang_count'] ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="narasumber">
                    Instruktur LKPP (<span id="countNarasumber"><?= $stats['narasumber_count'] ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="active">
                    Aktif (<span id="countActive"><?= $stats['active_count'] ?></span>)
                </button>
                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 filter-btn" data-filter="inactive">
                    Nonaktif (<span id="countInactive"><?= $stats['inactive_count'] ?></span>)
                </button>
            </div>

            <!-- Quick Search Input -->
            <div class="position-relative" style="min-width: 260px;">
                <input type="text" id="teamSearchInput" class="form-control form-control-sm radius-8 ps-36 pe-32" placeholder="Cari nama, NIP, jabatan, badge...">
                <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-12 text-secondary-light text-base"></iconify-icon>
                <button type="button" id="clearTeamSearchBtn" class="btn btn-link p-0 position-absolute top-50 end-0 translate-middle-y me-10 text-secondary-light d-none" title="Bersihkan pencarian">
                    <iconify-icon icon="solar:close-circle-bold" class="text-base"></iconify-icon>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table bordered-table mb-0 align-middle" id="teamTable">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="text-center">Urutan</th>
                        <th style="width: 80px;" class="text-center">Foto Potret</th>
                        <th style="min-width: 220px;">Nama Tokoh &amp; NIP</th>
                        <th style="min-width: 220px;">Jabatan Kedinasan &amp; Kategori</th>
                        <th style="min-width: 260px;">Badge Spotlight &amp; Biografi</th>
                        <th style="width: 110px;" class="text-center">Status</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="teamTableBody">
                    <?php if (empty($members)): ?>
                        <tr id="emptyTeamRow">
                            <td colspan="7" class="text-center py-48">
                                <div class="max-w-400 mx-auto text-center">
                                    <div class="w-64-px h-64-px rounded-circle bg-neutral-100 d-inline-flex align-items-center justify-content-center text-neutral-400 mb-16">
                                        <iconify-icon icon="solar:users-group-rounded-linear" class="text-3xl"></iconify-icon>
                                    </div>
                                    <h6 class="fw-semibold text-primary-light mb-8">Belum Ada Anggota Tim Inovator</h6>
                                    <p class="text-secondary-light text-xs mb-16">Tambahkan tokoh pengarah atau muat profil rekomendasi standar inovator BegawiPengadaan.</p>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="<?= site_url('dashboard/landing/tim/muat-rekomendasi') ?>" class="btn btn-primary-600 radius-8 px-16 py-8 text-xs d-flex align-items-center gap-2">
                                            <iconify-icon icon="solar:magic-stick-3-bold" class="text-base"></iconify-icon>
                                            <span>Muat Rekomendasi Tim</span>
                                        </a>
                                        <a href="<?= site_url('dashboard/landing/tim/tambah') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs d-flex align-items-center gap-2">
                                            <iconify-icon icon="solar:add-circle-bold" class="text-base"></iconify-icon>
                                            <span>Tambah Manual</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $m): ?>
                            <?php
                            $social = json_decode($m['social_links'] ?? '{}', true) ?: [];
                            $badge = $social['badge'] ?? 'Inovator';
                            $photo = $m['photo_url'] ?: 'assets/main/images/team/team-1-1.jpg';
                            $photoSrc = str_starts_with($photo, 'http') ? $photo : base_url($photo);

                            $cat = $m['category'] ?? 'pembina';
                            $catBadge = match($cat) {
                                'pembina'     => 'bg-purple-100 text-purple-700',
                                'pengembang'  => 'bg-primary-100 text-primary-700',
                                'narasumber'  => 'bg-success-100 text-success-700',
                                default       => 'bg-neutral-100 text-neutral-700'
                            };
                            $catLabel = match($cat) {
                                'pembina'     => 'Pembina Daerah',
                                'pengembang'  => 'Perekayasa Sistem',
                                'narasumber'  => 'Instruktur LKPP',
                                default       => ucfirst($cat)
                            };
                            $statusClass = $m['is_active'] ? 'active-row' : 'inactive-row';
                            ?>
                            <tr class="team-row <?= $statusClass ?>" 
                                data-id="<?= $m['id'] ?>" 
                                data-status="<?= $m['is_active'] ? 'active' : 'inactive' ?>" 
                                data-category="<?= esc($cat) ?>">
                                
                                <!-- Urutan -->
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <input type="number" 
                                               class="form-control form-control-sm text-center fw-bold order-input px-1" 
                                               style="width: 50px;" 
                                               value="<?= esc($m['sort_order']) ?>" 
                                               min="1" 
                                               max="99" 
                                               data-id="<?= $m['id'] ?>" 
                                               title="Ubah urutan lalu tekan Enter">
                                        <span class="order-saved-check d-none text-success-main" title="Tersimpan!">
                                            <iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon>
                                        </span>
                                    </div>
                                </td>

                                <!-- Foto Potret -->
                                <td class="text-center">
                                    <div class="position-relative d-inline-block">
                                        <div class="w-48-px h-56-px rounded-8 overflow-hidden border border-2 border-white shadow-xs bg-neutral-100 mx-auto">
                                            <img src="<?= esc($photoSrc) ?>" 
                                                 alt="<?= esc($m['name']) ?>" 
                                                 class="w-100 h-100 object-fit-cover team-avatar-img"
                                                 loading="lazy"
                                                 onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                                        </div>
                                    </div>
                                </td>

                                <!-- Nama Tokoh & NIP -->
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <strong class="text-sm text-primary-light team-name"><?= esc($m['name']) ?></strong>
                                    </div>
                                    <?php if (! empty($m['nip'])): ?>
                                        <div class="d-flex align-items-center gap-1 text-secondary-light font-monospace text-2xs">
                                            <iconify-icon icon="solar:card-2-outline" class="text-xs"></iconify-icon>
                                            <span>NIP. <?= esc($m['nip']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-2xs text-secondary-light fst-italic">Non-PNS / Eksternal</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Jabatan Kedinasan & Kategori -->
                                <td>
                                    <strong class="text-xs text-primary-600 d-block mb-1"><?= esc($m['role_title']) ?></strong>
                                    <span class="badge <?= $catBadge ?> text-3xs text-uppercase px-8 py-3 radius-4 fw-bold">
                                        <?= esc($catLabel) ?>
                                    </span>
                                </td>

                                <!-- Badge Spotlight & Biografi -->
                                <td>
                                    <span class="badge bg-warning-100 text-warning-800 radius-4 text-2xs mb-1 d-inline-flex align-items-center">
                                        <iconify-icon icon="solar:star-bold" class="text-warning-main me-1 text-xs"></iconify-icon>
                                        <?= esc($badge) ?>
                                    </span>
                                    <?php if (! empty($m['bio'])): ?>
                                        <p class="text-xs text-secondary-light mb-0 text-truncate" style="max-width: 320px;" title="<?= esc($m['bio']) ?>">
                                            <?= esc($m['bio']) ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-2xs text-secondary-light mb-0 fst-italic">- Belum ada biografi -</p>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Tayang -->
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm btn-toggle-status px-10 py-4 radius-4 text-xs fw-semibold border-0 <?= $m['is_active'] ? 'bg-success-100 text-success-600' : 'bg-neutral-200 text-neutral-600' ?>"
                                            data-id="<?= $m['id'] ?>"
                                            data-active="<?= $m['is_active'] ? '1' : '0' ?>"
                                            title="Klik untuk ubah status tayang">
                                        <span class="status-label"><?= $m['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                                    </button>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <!-- Duplikat -->
                                        <a href="<?= site_url('dashboard/landing/tim/duplikat/' . $m['id']) ?>" 
                                           class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-info-main hover-text-white transition-2" 
                                           title="Duplikat sebagai Draf">
                                            <iconify-icon icon="solar:copy-linear" class="text-base"></iconify-icon>
                                        </a>

                                        <!-- Ubah -->
                                        <a href="<?= site_url('dashboard/landing/tim/ubah/' . $m['id']) ?>" 
                                           class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white transition-2" 
                                           title="Ubah Profil Tokoh">
                                            <iconify-icon icon="solar:pen-new-square-linear" class="text-base"></iconify-icon>
                                        </a>

                                        <!-- Hapus -->
                                        <button type="button" 
                                                class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-danger-main hover-text-white transition-2 border-0 btn-delete-team"
                                                data-id="<?= $m['id'] ?>" 
                                                data-name="<?= esc($m['name']) ?>" 
                                                title="Hapus Profil">
                                            <iconify-icon icon="solar:trash-bin-trash-linear" class="text-base"></iconify-icon>
                                        </button>
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

<!-- =========================================================
     MODAL PRATINJAU PUBLIK CAROUSEL SPOTLIGHT TIM
     ========================================================= -->
<div class="modal fade" id="teamPreviewModal" tabindex="-1" aria-labelledby="teamPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 radius-16 overflow-hidden">
            <div class="modal-header bg-primary-600 text-white p-20">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:users-group-rounded-bold" class="text-2xl text-warning-main"></iconify-icon>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="teamPreviewModalLabel">Simulator Carousel Tim Inovator</h6>
                        <small class="text-white-50 text-2xs">Pratinjau kartu potret tim spotlight sebagaimana tayang di beranda publik</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24 bg-neutral-50">
                <!-- Outer Container matching public landing -->
                <div class="p-24 radius-12 bg-white border">
                    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-20 border-bottom pb-16">
                        <div>
                            <span class="badge bg-primary-100 text-primary-600 px-10 py-5 radius-4 text-2xs fw-semibold mb-2 d-inline-flex align-items-center gap-1">
                                <iconify-icon icon="solar:users-group-rounded-bold"></iconify-icon>
                                Kolaborator &amp; Pengembang
                            </span>
                            <h5 class="fw-bold text-primary-light mb-1">Tim Inovator di Balik Ekosistem BegawiPengadaan</h5>
                            <p class="text-secondary-light text-xs mb-0">Sinergi kepemimpinan daerah, birokrasi profesional, tim perekayasa perangkat lunak, dan fasilitator ahli pengadaan barang/jasa.</p>
                        </div>
                    </div>

                    <!-- Simulator Spotlight Grid -->
                    <div class="row row-cols-xl-4 row-cols-lg-3 row-cols-md-2 row-cols-1 g-20" id="previewTeamGrid">
                        <?php 
                        $activeMembers = array_filter($members, fn($m) => (int) ($m['is_active'] ?? 0) === 1);
                        ?>
                        <?php if (empty($activeMembers)): ?>
                            <div class="col-12 py-32 text-center text-secondary-light">
                                <iconify-icon icon="solar:info-circle-linear" class="text-2xl mb-1"></iconify-icon>
                                <p class="text-xs mb-0">Tidak ada anggota tim yang aktif untuk ditampilkan pada pratinjau publik.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($activeMembers as $m): ?>
                                <?php
                                $social = json_decode($m['social_links'] ?? '{}', true) ?: [];
                                $badge = $social['badge'] ?? 'Inovator';
                                $photo = $m['photo_url'] ?: 'assets/main/images/team/team-1-1.jpg';
                                $photoSrc = str_starts_with($photo, 'http') ? $photo : base_url($photo);
                                ?>
                                <div class="col">
                                    <div class="sim-spotlight-card radius-16 overflow-hidden position-relative shadow-sm transition-2 border bg-dark" style="height: 320px;">
                                        <!-- Photo Background -->
                                        <img src="<?= esc($photoSrc) ?>" alt="<?= esc($m['name']) ?>" class="w-100 h-100 object-fit-cover sim-spotlight-img" onerror="this.src='<?= base_url('assets/dashboard/images/users/user1.png') ?>'">
                                        
                                        <!-- Top Badge Pill -->
                                        <?php if (! empty($badge)): ?>
                                            <div class="position-absolute top-0 start-0 m-12 z-2">
                                                <span class="badge bg-white text-primary-900 shadow-sm radius-999 px-10 py-5 text-3xs fw-bold d-inline-flex align-items-center gap-1">
                                                    <iconify-icon icon="solar:star-bold" class="text-warning-main text-xs"></iconify-icon>
                                                    <?= esc($badge) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Gradient Overlay -->
                                        <div class="position-absolute bottom-0 start-0 end-0 p-16 z-2 text-white" style="background: linear-gradient(180deg, rgba(5,34,68,0) 0%, rgba(5,34,68,0.85) 50%, rgba(5,34,68,0.98) 100%);">
                                            <h6 class="text-white fw-bold mb-1 text-sm lh-sm"><?= esc($m['name']) ?></h6>
                                            <span class="text-white-50 text-2xs d-block"><?= esc($m['role_title']) ?></span>
                                            <?php if (! empty($m['nip'])): ?>
                                                <small class="text-white-50 text-3xs font-monospace d-block mt-1">NIP: <?= esc($m['nip']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top p-16">
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs" data-bs-dismiss="modal">Tutup</button>
                <a href="<?= site_url('/#section-team') ?>" target="_blank" class="btn btn-primary-600 radius-8 px-16 py-8 text-xs d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:external-link-bold" class="text-sm"></iconify-icon>
                    <span>Kunjungi Beranda Asli</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.sim-spotlight-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}
.sim-spotlight-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(5, 34, 68, 0.25) !important;
}
.sim-spotlight-card:hover .sim-spotlight-img {
    transform: scale(1.05);
}
.sim-spotlight-img {
    transition: transform 0.5s ease;
}
.team-avatar-img {
    transition: transform 0.25s ease;
}
.team-row:hover .team-avatar-img {
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfTokenName = '<?= csrf_token() ?>';
    const csrfHash      = '<?= csrf_hash() ?>';

    // 1. Filter Pills
    const filterBtns = document.querySelectorAll('.filter-btn');
    const rows       = document.querySelectorAll('.team-row');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active', 'btn-primary-600', 'text-white'));
            this.classList.add('active', 'btn-primary-600', 'text-white');
            this.classList.remove('btn-outline-neutral-300');

            applyTeamFilters();
        });
    });

    // 2. Search Box with Clear Button
    const searchInput = document.getElementById('teamSearchInput');
    const clearBtn    = document.getElementById('clearTeamSearchBtn');

    if (searchInput && clearBtn) {
        searchInput.addEventListener('input', function () {
            if (this.value.trim().length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
            applyTeamFilters();
        });

        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            clearBtn.classList.add('d-none');
            applyTeamFilters();
            searchInput.focus();
        });
    }

    function applyTeamFilters() {
        const activeBtn = document.querySelector('.filter-btn.active');
        const filter = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
        const query  = searchInput ? searchInput.value.toLowerCase().trim() : '';

        rows.forEach(row => {
            const status   = row.getAttribute('data-status');
            const category = row.getAttribute('data-category');
            const text     = row.innerText.toLowerCase();

            let matchesFilter = true;
            if (filter === 'pembina') matchesFilter = (category === 'pembina');
            else if (filter === 'pengembang') matchesFilter = (category === 'pengembang');
            else if (filter === 'narasumber') matchesFilter = (category === 'narasumber');
            else if (filter === 'active') matchesFilter = (status === 'active');
            else if (filter === 'inactive') matchesFilter = (status === 'inactive');

            const matchesQuery = (query === '' || text.includes(query));

            if (matchesFilter && matchesQuery) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // 3. Inline Order Update
    const orderInputs = document.querySelectorAll('.order-input');
    orderInputs.forEach(input => {
        const handleOrderSave = function () {
            const id = input.getAttribute('data-id');
            const newOrder = input.value;
            const checkIcon = input.nextElementSibling;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('order', newOrder);
            formData.append(csrfTokenName, csrfHash);

            fetch('<?= site_url('dashboard/landing/tim/update-order') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (checkIcon) {
                        checkIcon.classList.remove('d-none');
                        setTimeout(() => checkIcon.classList.add('d-none'), 1800);
                    }
                }
            })
            .catch(err => console.error(err));
        };

        input.addEventListener('change', handleOrderSave);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                input.blur();
            }
        });
    });

    // 4. AJAX Toggle Status
    const toggleBtns = document.querySelectorAll('.btn-toggle-status');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const row = this.closest('tr');
            const labelSpan = this.querySelector('.status-label');

            btn.disabled = true;

            fetch('<?= site_url('dashboard/landing/tim/toggle/') ?>' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                if (data.success) {
                    const isActive = (data.is_active === 1);
                    btn.setAttribute('data-active', isActive ? '1' : '0');
                    if (row) {
                        row.setAttribute('data-status', isActive ? 'active' : 'inactive');
                        row.classList.toggle('active-row', isActive);
                        row.classList.toggle('inactive-row', !isActive);
                    }
                    if (isActive) {
                        btn.className = 'btn btn-sm btn-toggle-status px-10 py-4 radius-4 text-xs fw-semibold border-0 bg-success-100 text-success-600';
                        if (labelSpan) labelSpan.textContent = 'Aktif';
                    } else {
                        btn.className = 'btn btn-sm btn-toggle-status px-10 py-4 radius-4 text-xs fw-semibold border-0 bg-neutral-200 text-neutral-600';
                        if (labelSpan) labelSpan.textContent = 'Nonaktif';
                    }
                    updateFilterPillCounts();
                }
            })
            .catch(err => {
                btn.disabled = false;
                console.error(err);
            });
        });
    });

    function updateFilterPillCounts() {
        const allRows = document.querySelectorAll('.team-row');
        let activeCount = 0;
        let inactiveCount = 0;

        allRows.forEach(r => {
            if (r.getAttribute('data-status') === 'active') activeCount++;
            else inactiveCount++;
        });

        const countActiveEl = document.getElementById('countActive');
        const countInactiveEl = document.getElementById('countInactive');
        if (countActiveEl) countActiveEl.textContent = activeCount;
        if (countInactiveEl) countInactiveEl.textContent = inactiveCount;
    }

    // 5. SweetAlert2 Delete Confirmation
    const deleteBtns = document.querySelectorAll('.btn-delete-team');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const row = this.closest('tr');

            const proceedDelete = () => {
                fetch('<?= site_url('dashboard/landing/tim/hapus/') ?>' + id, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                        if (row) {
                            row.remove();
                            updateAllCounts();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal!', data.message || 'Tidak dapat menghapus data.', 'error');
                        }
                    }
                })
                .catch(err => {
                    window.location.href = '<?= site_url('dashboard/landing/tim/hapus/') ?>' + id;
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Anggota Tim?',
                    text: `Profil tokoh "${name}" akan dihapus dari sistem.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceedDelete();
                    }
                });
            } else {
                if (confirm(`Hapus anggota tim "${name}"?`)) {
                    proceedDelete();
                }
            }
        });
    });

    function updateAllCounts() {
        const remainingRows = document.querySelectorAll('.team-row');
        const total = remainingRows.length;
        let active = 0, inactive = 0, pembina = 0, pengembang = 0, narasumber = 0;

        remainingRows.forEach(r => {
            const status = r.getAttribute('data-status');
            const cat = r.getAttribute('data-category');
            if (status === 'active') active++; else inactive++;
            if (cat === 'pembina') pembina++;
            else if (cat === 'pengembang') pengembang++;
            else if (cat === 'narasumber') narasumber++;
        });

        const elAll = document.getElementById('countAll');
        const elPembina = document.getElementById('countPembina');
        const elPengembang = document.getElementById('countPengembang');
        const elNarasumber = document.getElementById('countNarasumber');
        const elActive = document.getElementById('countActive');
        const elInactive = document.getElementById('countInactive');

        if (elAll) elAll.textContent = total;
        if (elPembina) elPembina.textContent = pembina;
        if (elPengembang) elPengembang.textContent = pengembang;
        if (elNarasumber) elNarasumber.textContent = narasumber;
        if (elActive) elActive.textContent = active;
        if (elInactive) elInactive.textContent = inactive;
    }
});
</script>
<?= $this->endSection() ?>

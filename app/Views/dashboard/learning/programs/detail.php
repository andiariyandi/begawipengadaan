<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$program = $program ?? [];
$db = db_connect();
$courses = $db->table('courses')
    ->where('program_id', (int) ($program['id'] ?? 0))
    ->orderBy('title', 'ASC')
    ->get()->getResultArray();

$classCount = (int) $db->table('learning_classes lc')
    ->join('courses c', 'c.id = lc.course_id')
    ->where('c.program_id', (int) ($program['id'] ?? 0))
    ->countAllResults();

$studentCount = (int) $db->table('enrollments e')
    ->join('learning_classes lc', 'lc.id = e.class_id')
    ->join('courses c', 'c.id = lc.course_id')
    ->where('c.program_id', (int) ($program['id'] ?? 0))
    ->whereIn('e.status', ['active', 'completed', 'passed'])
    ->countAllResults();
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Detail Program') ?></h6>
        <p class="text-secondary-light mb-0">Rincian kurikulum dan konfigurasi pembelajaran.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= route_to('dashboard.learning.programs') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear" class="text-xl"></iconify-icon>
            <span>Kembali</span>
        </a>
        <a href="<?= site_url('arena-belajar/program/' . $program['slug']) ?>" target="_blank" class="btn btn-outline-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:link-circle-outline" class="text-xl"></iconify-icon>
            <span>Buka di Landing Page</span>
        </a>
        <a href="<?= route_to('dashboard.learning.programs.edit', $program['id']) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-new-square-outline" class="text-xl"></iconify-icon>
            <span>Ubah Program</span>
        </a>
    </div>
</div>

<!-- Stats Strip -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Total Course Terkait</p>
                <h5 class="mb-0 text-primary-600"><?= count($courses) ?> Course</h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Batch Kelas Berjalan</p>
                <h5 class="mb-0 text-purple"><?= $classCount ?> Batch</h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Peserta Terdaftar</p>
                <h5 class="mb-0 text-info-main"><?= $studentCount ?> Orang</h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Status Kurikulum</p>
                <div class="mt-1">
                    <?= view('dashboard/components/status_badge', ['status' => $program['status'] ?? 'draft']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4">
    <!-- Left Column: Detail Info -->
    <div class="col-xl-8">
        <div class="card mb-24">
            <div class="card-body p-24">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-20 pb-16 border-bottom">
                    <div>
                        <span class="badge bg-primary-50 text-primary-600 border border-primary-100 px-12 py-4 radius-20 text-xs fw-semibold">
                            Program Terstruktur
                        </span>
                        <?php if (! empty($program['featured'])): ?>
                            <span class="badge bg-warning-50 text-warning-600 border border-warning-200 px-12 py-4 radius-20 text-xs fw-semibold ms-1">
                                <i class="fas fa-star me-1"></i> Kurikulum Unggulan
                            </span>
                        <?php endif; ?>
                        <h5 class="text-xl fw-bold mt-2 mb-0"><?= esc($program['title']) ?></h5>
                    </div>
                    <span class="text-secondary-light text-sm font-monospace">/arena-belajar/program/<?= esc($program['slug']) ?></span>
                </div>

                <div class="row gy-20 mb-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Visibilitas</p>
                        <span class="badge bg-info-50 text-info-600 border border-info-200 px-12 py-4 radius-20 text-xs fw-semibold text-uppercase">
                            <?= esc($program['visibility'] ?? 'public') ?>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Waktu Dibuat</p>
                        <p class="fw-semibold mb-0 text-sm"><?= ! empty($program['created_at']) ? date('d M Y H:i', strtotime($program['created_at'])) : '-' ?></p>
                    </div>
                    <div class="col-12">
                        <p class="text-secondary-light text-sm mb-4">Ringkasan (Lead)</p>
                        <p class="lead mb-0 text-primary-light" style="font-size: 14.5px;"><?= esc($program['summary'] ?: '-') ?></p>
                    </div>
                    <div class="col-12">
                        <p class="text-secondary-light text-sm mb-4">Deskripsi Lengkap</p>
                        <div class="p-16 radius-8 bg-neutral-50 text-secondary-light text-sm" style="line-height: 1.7;">
                            <?= nl2br(esc($program['description'] ?: 'Tidak ada deskripsi rinci.')) ?>
                        </div>
                    </div>
                </div>

                <!-- Sasaran, Prasyarat, & Kompetensi -->
                <div class="pt-16 border-top">
                    <div class="row gy-20">
                        <div class="col-md-6">
                            <h6 class="text-sm fw-bold text-primary-600 mb-8 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:users-group-rounded-bold" class="text-base"></iconify-icon>
                                <span>Target Sasaran Peserta</span>
                            </h6>
                            <div class="p-16 radius-8 bg-neutral-50 text-secondary-light text-sm">
                                <?= nl2br(esc($program['target_audience'] ?: 'Terbuka untuk Aparatur Sipil Negara, Pejabat Pengadaan, dan Praktisi Publik.')) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-sm fw-bold text-primary-600 mb-8 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:checklist-minimalistic-bold" class="text-base"></iconify-icon>
                                <span>Prasyarat Program</span>
                            </h6>
                            <div class="p-16 radius-8 bg-neutral-50 text-secondary-light text-sm">
                                <?= nl2br(esc($program['prerequisites'] ?: 'Tidak ada prasyarat khusus. Pemahaman dasar administrasi PBJ disarankan.')) ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <h6 class="text-sm fw-bold text-primary-600 mb-8 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:diploma-verified-bold" class="text-base"></iconify-icon>
                                <span>Capaian Pembelajaran & Target Kompetensi</span>
                            </h6>
                            <div class="p-16 radius-8 bg-neutral-50 text-secondary-light text-sm">
                                <?= nl2br(esc($program['learning_objectives'] ?: 'Pemahaman regulasi Perpres PBJ, kepatuhan prosedur pemilihan penyedia, penyusunan HPS akuntabel, dan mitigasi risiko sengketa kontrak.')) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Course Terkait -->
        <div class="card">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <h6 class="text-lg fw-semibold mb-0">Course dalam Kurikulum Ini (<?= count($courses) ?>)</h6>
                <a href="<?= site_url('dashboard/pembelajaran/course/tambah?program_id=' . $program['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="solar:add-circle-outline" class="text-base"></iconify-icon>
                    <span>Tambah Course</span>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th>Judul Course</th>
                                <th>Level</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($courses)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-secondary-light py-20">Belum ada course pada program ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($courses as $c): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= site_url('dashboard/pembelajaran/course/' . $c['id']) ?>" class="fw-semibold text-primary-600">
                                                <?= esc($c['title']) ?>
                                            </a>
                                            <small class="d-block text-secondary-light font-monospace"><?= esc($c['slug']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-neutral-100 text-neutral-800 border radius-12 text-xs">
                                                <?= esc(ucfirst($c['level'] ?? 'Dasar')) ?>
                                            </span>
                                        </td>
                                        <td><?= (int) ($c['duration_minutes'] ?? 0) ?> Menit</td>
                                        <td><?= view('dashboard/components/status_badge', ['status' => $c['status'] ?? 'draft']) ?></td>
                                        <td class="text-center">
                                            <a href="<?= site_url('dashboard/pembelajaran/course/' . $c['id'] . '/ubah') ?>" class="btn btn-sm btn-outline-neutral-300 radius-8 px-10 py-4">Ubah</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Media Preview & Quick Actions -->
    <div class="col-xl-4">
        <!-- Media Card -->
        <div class="card mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-md fw-semibold mb-0">Gambar Cover / Sampul</h6>
            </div>
            <div class="card-body p-24 text-center">
                <?php
                $coverSrc = ! empty($program['cover_url'])
                    ? (str_starts_with($program['cover_url'], 'http') ? $program['cover_url'] : base_url($program['cover_url']))
                    : base_url('assets/main/images/resources/course-list-img-1.jpg');
                ?>
                <div class="radius-12 overflow-hidden border mb-16" style="max-height: 200px;">
                    <img src="<?= esc($coverSrc) ?>" alt="<?= esc($program['title']) ?>" class="w-100 h-100" style="object-fit: cover;">
                </div>
                <p class="text-secondary-light text-xs mb-0">Cover akan tampil pada kartu katalog dan hero halaman kurikulum publik.</p>
            </div>
        </div>

        <!-- Quick Help Card -->
        <div class="card">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-md fw-semibold mb-0">Petunjuk Pengelolaan</h6>
            </div>
            <div class="card-body p-24">
                <ul class="d-flex flex-column gap-12 ps-16 mb-0 text-sm text-secondary-light">
                    <li>Program berstatus <strong>Published</strong> dan bervisibilitas <strong>Public</strong> akan langsung muncul pada landing page Arena Belajar.</li>
                    <li>Centang <strong>Kurikulum Unggulan</strong> untuk menyorot program pada bagian atas beranda publik.</li>
                    <li>Pastikan minimal 1 Course terhubung agar calon peserta dapat melihat struktur materi.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

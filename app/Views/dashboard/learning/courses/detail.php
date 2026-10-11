<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$course = $course ?? [];
$db = db_connect();

$classes = $db->table('learning_classes lc')
    ->select('lc.*, (SELECT COUNT(1) FROM enrollments e WHERE e.class_id = lc.id) AS enrolled_count')
    ->where('lc.course_id', (int) ($course['id'] ?? 0))
    ->orderBy('lc.created_at', 'DESC')
    ->get()->getResultArray();

$studentCount = (int) $db->table('enrollments e')
    ->join('learning_classes lc', 'lc.id = e.class_id')
    ->where('lc.course_id', (int) ($course['id'] ?? 0))
    ->whereIn('e.status', ['active', 'completed', 'passed'])
    ->countAllResults();

$levelBadge = match (strtolower((string) ($course['level'] ?? 'basic'))) {
    'intermediate' => ['label' => 'Menengah (Intermediate)', 'badge' => 'bg-warning-50 text-warning-600 border border-warning-200'],
    'advanced'     => ['label' => 'Lanjutan (Advanced)', 'badge' => 'bg-danger-50 text-danger-600 border border-danger-200'],
    default        => ['label' => 'Dasar (Basic)', 'badge' => 'bg-info-50 text-info-600 border border-info-200'],
};
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Detail Course') ?></h6>
        <p class="text-secondary-light mb-0">Rincian materi kursus, capaian kompetensi, dan batch kelas terkait.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= route_to('dashboard.learning.courses') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-linear" class="text-xl"></iconify-icon>
            <span>Kembali</span>
        </a>
        <a href="<?= site_url('arena-belajar/course/' . $course['slug']) ?>" target="_blank" class="btn btn-outline-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:link-circle-outline" class="text-xl"></iconify-icon>
            <span>Buka di Landing Page</span>
        </a>
        <a href="<?= route_to('dashboard.learning.courses.edit', $course['id']) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-new-square-outline" class="text-xl"></iconify-icon>
            <span>Ubah Course</span>
        </a>
    </div>
</div>

<!-- Stats Strip -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Program Kurikulum Induk</p>
                <h6 class="mb-0 text-primary-600 text-truncate" title="<?= esc($course['program_title'] ?? '-') ?>"><?= esc($course['program_title'] ?? '-') ?></h6>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Tingkat & Durasi</p>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge <?= $levelBadge['badge'] ?> px-8 py-2 radius-12 text-xs"><?= esc($levelBadge['label']) ?></span>
                    <span class="text-secondary-light text-xs font-monospace"><?= (int) ($course['duration_minutes'] ?? 0) ?> Mnt</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Batch Kelas Dibuka</p>
                <h5 class="mb-0 text-info-main"><?= count($classes) ?> Batch</h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Status Publikasi</p>
                <div class="mt-1">
                    <?= view('dashboard/components/status_badge', ['status' => $course['status'] ?? 'draft']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4">
    <!-- Left Column: Detail Info & Classes -->
    <div class="col-xl-8">
        <div class="card mb-24">
            <div class="card-body p-24">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-20 pb-16 border-bottom">
                    <div>
                        <span class="badge bg-primary-50 text-primary-600 border border-primary-100 px-12 py-4 radius-20 text-xs fw-semibold">
                            Course Terstruktur
                        </span>
                        <h5 class="text-xl fw-bold mt-2 mb-0"><?= esc($course['title']) ?></h5>
                    </div>
                    <span class="text-secondary-light text-sm font-monospace">/arena-belajar/course/<?= esc($course['slug']) ?></span>
                </div>

                <div class="row gy-20 mb-20">
                    <div class="col-12">
                        <p class="text-secondary-light text-sm mb-4">Ringkasan Materi (Lead)</p>
                        <p class="lead mb-0 text-primary-light" style="font-size: 14.5px;"><?= esc($course['summary'] ?: '-') ?></p>
                    </div>
                    <div class="col-12">
                        <p class="text-secondary-light text-sm mb-4">Deskripsi Lengkap</p>
                        <div class="p-16 radius-8 bg-neutral-50 text-secondary-light text-sm" style="line-height: 1.7;">
                            <?= nl2br(esc($course['description'] ?: 'Tidak ada deskripsi rinci.')) ?>
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
                                <?= nl2br(esc($course['target_audience'] ?: 'Pejabat Pembuat Komitmen, Pokja Pemilihan, Pejabat Pengadaan, Penyedia.')) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-sm fw-bold text-primary-600 mb-8 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:checklist-minimalistic-bold" class="text-base"></iconify-icon>
                                <span>Prasyarat Kursus</span>
                            </h6>
                            <div class="p-16 radius-8 bg-neutral-50 text-secondary-light text-sm">
                                <?= nl2br(esc($course['prerequisites'] ?: 'Disarankan memahami alur perencanaan anggaran dan pengadaan dasar.')) ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <h6 class="text-sm fw-bold text-primary-600 mb-8 d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:diploma-verified-bold" class="text-base"></iconify-icon>
                                <span>Target Capaian Kompetensi Kursus</span>
                            </h6>
                            <div class="p-16 radius-8 bg-neutral-50 text-secondary-light text-sm">
                                <?= nl2br(esc($course['learning_objectives'] ?: 'Mampu menyusun spesifikasi teknis dan HPS wajar, mengoperasikan e-katalog, dan mengendalikan pelaksanaan kontrak.')) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Batch Kelas Terkait -->
        <div class="card">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <h6 class="text-lg fw-semibold mb-0">Batch Kelas untuk Course Ini (<?= count($classes) ?>)</h6>
                <a href="<?= site_url('dashboard/pembelajaran/kelas/tambah?course_id=' . $course['id']) ?>" class="btn btn-sm btn-primary-600 radius-8 px-12 py-6 d-inline-flex align-items-center gap-1">
                    <iconify-icon icon="solar:add-circle-outline" class="text-base"></iconify-icon>
                    <span>Buka Batch Kelas Baru</span>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th>Kode & Nama Batch</th>
                                <th>Jadwal Pelaksanaan</th>
                                <th>Kapasitas & Kuota</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($classes)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-secondary-light py-20">Belum ada batch kelas dibuka untuk course ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($classes as $cls): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= site_url('dashboard/pembelajaran/kelas/' . $cls['id']) ?>" class="fw-semibold text-primary-600">
                                                <?= esc($cls['name']) ?>
                                            </a>
                                            <small class="d-block text-secondary-light font-monospace"><?= esc($cls['code']) ?></small>
                                        </td>
                                        <td>
                                            <small class="d-block text-secondary-light">
                                                <?= ! empty($cls['start_at']) ? date('d M Y', strtotime($cls['start_at'])) : '-' ?> s/d
                                                <?= ! empty($cls['end_at']) ? date('d M Y', strtotime($cls['end_at'])) : '-' ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold"><?= (int) ($cls['enrolled_count'] ?? 0) ?></span> / <?= (int) ($cls['capacity'] ?? 0) ?> Kursi
                                        </td>
                                        <td><?= view('dashboard/components/status_badge', ['status' => $cls['status'] ?? 'draft']) ?></td>
                                        <td class="text-center">
                                            <a href="<?= site_url('dashboard/pembelajaran/kelas/' . $cls['id']) ?>" class="btn btn-sm btn-outline-neutral-300 radius-8 px-10 py-4">Detail</a>
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

    <!-- Right Column: Media Preview & Help -->
    <div class="col-xl-4">
        <div class="card mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-md fw-semibold mb-0">Gambar Cover Course</h6>
            </div>
            <div class="card-body p-24 text-center">
                <?php
                $coverSrc = ! empty($course['cover_url'])
                    ? (str_starts_with($course['cover_url'], 'http') ? $course['cover_url'] : base_url($course['cover_url']))
                    : base_url('assets/main/images/resources/course-details-img-1.jpg');
                ?>
                <div class="radius-12 overflow-hidden border mb-16" style="max-height: 200px;">
                    <img src="<?= esc($coverSrc) ?>" alt="<?= esc($course['title']) ?>" class="w-100 h-100" style="object-fit: cover;">
                </div>
                <p class="text-secondary-light text-xs mb-0">Cover akan tampil pada kartu katalog dan hero halaman course publik.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-md fw-semibold mb-0">Petunjuk Pengelolaan Course</h6>
            </div>
            <div class="card-body p-24">
                <ul class="d-flex flex-column gap-12 ps-16 mb-0 text-sm text-secondary-light">
                    <li>Pilih <strong>Program Kurikulum Induk</strong> yang sesuai agar materi terelasi di Arena Belajar.</li>
                    <li>Tentukan <strong>Tingkat Kesulitan</strong> (Level) secara tepat untuk memudahkan filter pencarian publik.</li>
                    <li>Buka <strong>Batch Kelas</strong> agar calon peserta dapat mendaftar dan mengikuti pembelajaran interaktif.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

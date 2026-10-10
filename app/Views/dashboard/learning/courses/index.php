<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$courses = $courses ?? [];
$programs = $programs ?? [];
$filters = $filters ?? [];
$pager = $pager ?? null;
$stats = $stats ?? [
    'total'     => count($courses),
    'published' => 0,
    'draft'     => 0,
    'archived'  => 0,
];
?>

<!-- Summary Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Course</p>
                        <h6 class="mb-0"><?= number_format($stats['total'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:book-bookmark-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Materi kursus pengadaan</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Dipublikasikan</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['published'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Tersedia untuk kelas</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Draft</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($stats['draft'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:document-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Dalam tahap kurasi</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Diarsipkan</p>
                        <h6 class="mb-0 text-secondary-light"><?= number_format($stats['archived'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-neutral-500 rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:archive-minimalistic-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Arsip kurikulum lama</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h6 class="text-lg mb-1">Course Pembelajaran</h6>
            <p class="text-secondary-light text-sm mb-0">Kelola daftar mata pembelajaran, silabus modul, dan tingkat keahlian.</p>
        </div>
        <a href="<?= route_to('dashboard.learning.courses.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            <span>Tambah Course</span>
        </a>
    </div>

    <div class="card-body p-24">
        <!-- Filter Form -->
        <form action="<?= route_to('dashboard.learning.courses') ?>" method="get" class="mb-20">
            <div class="row g-12 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pencarian</label>
                    <input type="text" class="form-control radius-8" name="keyword"
                           value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Cari judul course, slug, atau ringkasan…">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Program</label>
                    <select class="form-select radius-8" name="program_id">
                        <option value="">Semua Program</option>
                        <?php foreach ($programs as $prog): ?>
                            <option value="<?= (int) $prog['id'] ?>" <?= (int) ($filters['program_id'] ?? 0) === (int) $prog['id'] ? 'selected' : '' ?>>
                                <?= esc($prog['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label>
                    <select class="form-select radius-8" name="status">
                        <option value="">Semua Status</option>
                        <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Dipublikasikan</option>
                        <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="archived" <?= ($filters['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Diarsipkan</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:magnifer-outline" class="text-lg"></iconify-icon>
                        <span>Filter</span>
                    </button>
                    <a href="<?= route_to('dashboard.learning.courses') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center justify-content-center" title="Reset Filter">
                        <iconify-icon icon="solar:restart-outline" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Program Induk</th>
                        <th>Level</th>
                        <th>Durasi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'       => 'Course Tidak Ditemukan',
                                    'message'     => 'Belum ada data course yang sesuai dengan kriteria filter.',
                                    'actionUrl'   => route_to('dashboard.learning.courses.create'),
                                    'actionLabel' => 'Tambah Course Baru',
                                ]) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td>
                                    <h6 class="text-md mb-0 fw-medium">
                                        <a href="<?= route_to('dashboard.learning.courses.detail', $course['id']) ?>" class="text-primary-600">
                                            <?= esc($course['title']) ?>
                                        </a>
                                    </h6>
                                    <span class="text-secondary-light text-sm"><?= esc($course['summary'] ?? '') ?></span>
                                </td>
                                <td>
                                    <?php if (! empty($course['program_title'])): ?>
                                        <span class="fw-medium text-sm"><?= esc($course['program_title']) ?></span>
                                    <?php else: ?>
                                        <span class="text-secondary-light text-sm italic">Mandiri</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-neutral-100 text-secondary-light px-10 py-4 radius-4 fw-medium text-xs">
                                        <?= esc(ucfirst($course['level'] ?? 'Dasar')) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $course['duration_minutes'] !== null ? esc((string) $course['duration_minutes']) . ' menit' : '-' ?>
                                </td>
                                <td class="text-center">
                                    <?= view('dashboard/components/status_badge', ['status' => $course['status'] ?? 'draft']) ?>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-neutral-200 btn-sm radius-8 px-8 py-4 text-secondary-light" data-bs-toggle="dropdown" aria-expanded="false">
                                            <iconify-icon icon="solar:menu-dots-bold" class="text-lg"></iconify-icon>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm radius-8 p-8 border">
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= route_to('dashboard.learning.courses.detail', $course['id']) ?>">
                                                    <iconify-icon icon="solar:eye-outline" class="text-lg text-primary-600"></iconify-icon>
                                                    <span>Detail Course</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= route_to('dashboard.learning.courses.edit', $course['id']) ?>">
                                                    <iconify-icon icon="solar:pen-new-square-outline" class="text-lg text-warning-main"></iconify-icon>
                                                    <span>Ubah Course</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-4"></li>
                                            <li>
                                                <a class="dropdown-item px-12 py-8 radius-4 text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 text-sm" href="<?= route_to('dashboard.learning.classes') ?>?course_id=<?= (int) $course['id'] ?>">
                                                    <iconify-icon icon="solar:users-group-rounded-outline" class="text-lg text-info-main"></iconify-icon>
                                                    <span>Lihat Kelas</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pager): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-0">
                <?= $pager->links('courses', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

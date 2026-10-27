<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/learning/_nav_learning', ['activeSub' => 'assessments']) ?>
<?php
$currentTab     = $currentTab ?? 'quizzes';
$quizzes        = $quizzes ?? [];
$assignments    = $assignments ?? [];
$submissions    = $submissions ?? [];
$questions      = $questions ?? [];
$stats          = $stats ?? [];
$classes        = $classes ?? [];
$modulesByClass = $modulesByClass ?? [];
$filters        = $filters ?? [];
?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Assessment & Penilaian Pembelajaran') ?></h6>
        <p class="text-secondary-light mb-0">Kelola kuis (pre-test, modul, post-test), tugas evaluasi kelas & modul, bank soal, dan pemantauan submisi tugas peserta.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= site_url('dashboard/pembelajaran/kurikulum' . (! empty($filters['class_id']) ? '/kelas/' . $filters['class_id'] : '')) ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center gap-2" title="Buka Roadmap Kurikulum Terpadu">
            <iconify-icon icon="solar:structure-outline" class="text-xl"></iconify-icon>
            <span>Roadmap Kurikulum</span>
        </a>
        <a href="<?= site_url('dashboard/pembelajaran/assessment/bank-soal') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:folder-with-files-outline" class="text-xl"></iconify-icon>
            <span>Bank Soal</span>
        </a>
        <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#quizModal" onclick="openCreateQuizModal()">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            <span>+ Tambah Kuis</span>
        </button>
        <button type="button" class="btn btn-warning radius-8 px-20 py-11 text-primary-light fw-medium d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#assignmentModal" onclick="openCreateAssignmentModal()">
            <iconify-icon icon="solar:document-add-outline" class="text-xl"></iconify-icon>
            <span>+ Tambah Tugas</span>
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <!-- Card 1: Total Kuis -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100 radius-12">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Kuis & Ujian</p>
                        <h5 class="mb-0 fw-bold"><?= number_format($stats['total_quizzes'] ?? 0) ?></h5>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center text-white">
                        <iconify-icon icon="solar:question-circle-outline" class="text-2xl"></iconify-icon>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mt-12 text-xs text-primary-light">
                    <span class="badge bg-neutral-100 text-neutral-900 border fw-semibold"><?= (int) ($stats['pre_test_count'] ?? 0) ?> Pre-Test</span>
                    <span class="badge bg-neutral-100 text-neutral-900 border fw-semibold"><?= (int) ($stats['module_quiz_count'] ?? 0) ?> Modul</span>
                    <span class="badge bg-neutral-100 text-neutral-900 border fw-semibold"><?= (int) ($stats['post_test_count'] ?? 0) ?> Post-Test</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Bank Soal -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100 radius-12">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Bank Soal Aktif</p>
                        <h5 class="mb-0 fw-bold text-success-main"><?= number_format($stats['total_questions'] ?? 0) ?></h5>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center text-white">
                        <iconify-icon icon="solar:checklist-minimalistic-outline" class="text-2xl"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0">Butir soal pilihan ganda & kunci jawaban</p>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Tugas -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100 radius-12">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Penugasan</p>
                        <h5 class="mb-0 fw-bold text-info-main"><?= number_format($stats['total_assignments'] ?? 0) ?></h5>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center text-white">
                        <iconify-icon icon="solar:document-text-outline" class="text-2xl"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-xs text-primary-light mt-12 mb-0">Tugas Level Kelas & Modul Pelatihan</p>
            </div>
        </div>
    </div>

    <!-- Card 4: Submisi Masuk -->
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100 radius-12">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Submisi Masuk</p>
                        <h5 class="mb-0 fw-bold text-warning-main"><?= number_format($stats['total_submissions'] ?? 0) ?></h5>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-main rounded-circle d-flex justify-content-center align-items-center text-white">
                        <iconify-icon icon="solar:inbox-in-outline" class="text-2xl"></iconify-icon>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mt-12 text-xs">
                    <span class="badge bg-warning-50 text-warning-700 fw-bold"><?= (int) ($stats['pending_submissions'] ?? 0) ?> Perlu Dinilai</span>
                    <span class="badge bg-success-50 text-success-700 fw-bold"><?= (int) ($stats['graded_submissions'] ?? 0) ?> Dinilai</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Assessment Tabs Card -->
<div class="card border radius-12 shadow-none">
    <div class="card-header border-bottom bg-base py-12 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs card-header-tabs border-0 gap-1" id="assessmentTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'quizzes' ? 'active' : '' ?> fw-semibold radius-8 d-inline-flex align-items-center gap-2"
                   href="<?= site_url('dashboard/pembelajaran/assessment?tab=quizzes' . (! empty($filters['class_id']) ? '&class_id=' . $filters['class_id'] : '')) ?>">
                    <iconify-icon icon="solar:question-circle-outline"></iconify-icon>
                    <span>Kuis & Ujian</span>
                    <span class="badge <?= $currentTab === 'quizzes' ? 'bg-primary-600 text-white' : 'bg-neutral-100 text-secondary-light' ?> text-xxs radius-4 px-6 py-2">
                        <?= number_format($stats['total_quizzes'] ?? 0) ?>
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'assignments' ? 'active' : '' ?> fw-semibold radius-8 d-inline-flex align-items-center gap-2"
                   href="<?= site_url('dashboard/pembelajaran/assessment?tab=assignments' . (! empty($filters['class_id']) ? '&class_id=' . $filters['class_id'] : '')) ?>">
                    <iconify-icon icon="solar:document-text-outline"></iconify-icon>
                    <span>Tugas & Penugasan</span>
                    <span class="badge <?= $currentTab === 'assignments' ? 'bg-primary-600 text-white' : 'bg-neutral-100 text-secondary-light' ?> text-xxs radius-4 px-6 py-2">
                        <?= number_format($stats['total_assignments'] ?? 0) ?>
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'submissions' ? 'active' : '' ?> fw-semibold radius-8 d-inline-flex align-items-center gap-2"
                   href="<?= site_url('dashboard/pembelajaran/assessment?tab=submissions' . (! empty($filters['class_id']) ? '&class_id=' . $filters['class_id'] : '')) ?>">
                    <iconify-icon icon="solar:diploma-outline"></iconify-icon>
                    <span>Submisi Peserta</span>
                    <?php if ((int) ($stats['pending_submissions'] ?? 0) > 0): ?>
                        <span class="badge bg-warning-main text-white text-xxs radius-4 px-6 py-2">
                            <?= (int) $stats['pending_submissions'] ?> Baru
                        </span>
                    <?php else: ?>
                        <span class="badge <?= $currentTab === 'submissions' ? 'bg-primary-600 text-white' : 'bg-neutral-100 text-secondary-light' ?> text-xxs radius-4 px-6 py-2">
                            <?= number_format($stats['total_submissions'] ?? 0) ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'bank_soal' ? 'active' : '' ?> fw-semibold radius-8 d-inline-flex align-items-center gap-2"
                   href="<?= site_url('dashboard/pembelajaran/assessment?tab=bank_soal' . (! empty($filters['class_id']) ? '&class_id=' . $filters['class_id'] : '')) ?>">
                    <iconify-icon icon="solar:checklist-minimalistic-outline"></iconify-icon>
                    <span>Bank Soal</span>
                    <span class="badge <?= $currentTab === 'bank_soal' ? 'bg-primary-600 text-white' : 'bg-neutral-100 text-secondary-light' ?> text-xxs radius-4 px-6 py-2">
                        <?= number_format($stats['total_questions'] ?? 0) ?>
                    </span>
                </a>
            </li>
        </ul>

        <!-- Filter Dropdown & Search Form -->
        <form action="<?= site_url('dashboard/pembelajaran/assessment') ?>" method="get" class="d-flex align-items-center gap-2 flex-wrap">
            <input type="hidden" name="tab" value="<?= esc($currentTab) ?>">

            <select name="class_id" class="form-select form-select-sm radius-8" onchange="this.form.submit()" style="max-width: 200px;">
                <option value="">Semua Kelas</option>
                <?php foreach ($classes as $cls): ?>
                    <option value="<?= esc($cls['id']) ?>" <?= ((string) ($filters['class_id'] ?? '') === (string) $cls['id']) ? 'selected' : '' ?>>
                        <?= esc($cls['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($currentTab === 'quizzes'): ?>
                <select name="quiz_type" class="form-select form-select-sm radius-8" onchange="this.form.submit()" style="max-width: 150px;">
                    <option value="">Semua Tipe</option>
                    <option value="pre_test" <?= ($filters['quiz_type'] ?? '') === 'pre_test' ? 'selected' : '' ?>>Pre-Test</option>
                    <option value="lesson_quiz" <?= ($filters['quiz_type'] ?? '') === 'lesson_quiz' ? 'selected' : '' ?>>Kuis Modul</option>
                    <option value="post_test" <?= ($filters['quiz_type'] ?? '') === 'post_test' ? 'selected' : '' ?>>Post-Test</option>
                </select>
            <?php endif; ?>

            <?php if (in_array($currentTab, ['quizzes', 'assignments'], true)): ?>
                <select name="status" class="form-select form-select-sm radius-8" onchange="this.form.submit()" style="max-width: 130px;">
                    <option value="">Semua Status</option>
                    <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            <?php elseif ($currentTab === 'submissions'): ?>
                <select name="status" class="form-select form-select-sm radius-8" onchange="this.form.submit()" style="max-width: 150px;">
                    <option value="">Semua Status</option>
                    <option value="submitted" <?= ($filters['status'] ?? '') === 'submitted' ? 'selected' : '' ?>>Perlu Dinilai</option>
                    <option value="in_review" <?= ($filters['status'] ?? '') === 'in_review' ? 'selected' : '' ?>>Sedang Ditinjau</option>
                    <option value="graded" <?= ($filters['status'] ?? '') === 'graded' ? 'selected' : '' ?>>Selesai Dinilai</option>
                    <option value="revision_required" <?= ($filters['status'] ?? '') === 'revision_required' ? 'selected' : '' ?>>Perlu Revisi</option>
                </select>
            <?php endif; ?>

            <div class="input-group input-group-sm" style="max-width: 220px;">
                <input type="text" name="keyword" class="form-control radius-start-8" placeholder="Cari..." value="<?= esc($filters['keyword'] ?? '') ?>">
                <button class="btn btn-outline-secondary radius-end-8" type="submit" title="Cari">
                    <iconify-icon icon="solar:magnifer-outline"></iconify-icon>
                </button>
            </div>

            <?php if (! empty($filters['keyword']) || ! empty($filters['class_id']) || ! empty($filters['status']) || ! empty($filters['quiz_type'])): ?>
                <a href="<?= site_url('dashboard/pembelajaran/assessment?tab=' . $currentTab) ?>" class="btn btn-sm btn-outline-neutral-300 radius-8 px-8 py-5" title="Reset Filter">
                    <iconify-icon icon="solar:restart-outline"></iconify-icon>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card-body p-0">
        <!-- =================================================================== -->
        <!-- TAB 1: KUIS & UJIAN -->
        <!-- =================================================================== -->
        <?php if ($currentTab === 'quizzes'): ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Tipe Kuis</th>
                            <th>Judul Kuis & Penjelasan</th>
                            <th>Kelas & Course</th>
                            <th class="text-center">Jumlah Soal</th>
                            <th class="text-center">Total Bobot</th>
                            <th class="text-center">Attempt Limit</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quizzes)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-secondary-light py-32">
                                    <iconify-icon icon="solar:question-circle-outline" class="text-3xl text-secondary-light mb-2"></iconify-icon>
                                    <p class="mb-0 text-sm">Belum ada data kuis yang ditemukan. Klik tombol <strong>+ Tambah Kuis</strong> untuk membuat evaluasi baru.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($quizzes as $q): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $typeBadge = match($q['type'] ?? '') {
                                            'pre_test'  => 'bg-info-50 text-info-700 border-info-subtle',
                                            'post_test' => 'bg-success-50 text-success-700 border-success-subtle',
                                            default     => 'bg-primary-50 text-primary-700 border-primary-subtle',
                                        };
                                        $typeLabel = match($q['type'] ?? '') {
                                            'pre_test'  => 'Pre-Test',
                                            'post_test' => 'Post-Test',
                                            default     => 'Kuis Modul',
                                        };
                                        ?>
                                        <span class="badge <?= $typeBadge ?> border px-8 py-4 radius-4 text-xs font-monospace fw-semibold">
                                            <?= esc($typeLabel) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <h6 class="text-sm fw-semibold mb-0 text-neutral-900"><?= esc($q['title']) ?></h6>
                                        <?php if (! empty($q['instructions'])): ?>
                                            <span class="text-xs text-secondary-light line-clamp-1"><?= esc($q['instructions']) ?></span>
                                        <?php endif; ?>
                                        <?php if (! empty($q['module_title'])): ?>
                                            <span class="text-xxs text-primary-600 mt-1 d-block">Modul: <?= esc($q['module_title']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-medium text-xs text-neutral-900 d-block"><?= esc($q['class_name'] ?? 'Semua Kelas') ?></span>
                                        <span class="text-xxs text-secondary-light"><?= esc($q['course_title'] ?? '—') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-neutral-100 text-neutral-800 px-8 py-4 radius-4 text-xs font-monospace fw-semibold">
                                            <?= (int) ($q['question_count'] ?? 0) ?> Soal
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-50 text-primary-700 px-8 py-4 radius-4 text-xs font-monospace fw-semibold">
                                            <?= number_format((float) ($q['total_points'] ?? 0), 1) ?> Poin
                                        </span>
                                    </td>
                                    <td class="text-center text-sm font-monospace">
                                        <?= (int) ($q['attempt_limit'] ?? 1) ?>x
                                        <?php if (! empty($q['duration_minutes'])): ?>
                                            <div class="text-xxs text-primary fw-semibold mt-1"><?= (int) $q['duration_minutes'] ?> mnt</div>
                                        <?php endif; ?>
                                        <?php if ((int) ($q['attempt_count'] ?? 0) > 0): ?>
                                            <div class="text-xxs text-secondary-light mt-1"><?= (int) $q['attempt_count'] ?> pengerjaan</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= view('dashboard/components/status_badge', ['status' => $q['status']]) ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <!-- Toggle status -->
                                            <form action="<?= route_to('dashboard.learning.assessments.quiz.toggle', $q['id']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-neutral-300 radius-4 px-8 py-4" title="<?= $q['status'] === 'published' ? 'Jadikan Draft' : 'Publikasikan' ?>">
                                                    <iconify-icon icon="<?= $q['status'] === 'published' ? 'solar:eye-closed-outline' : 'solar:check-circle-outline' ?>"></iconify-icon>
                                                </button>
                                            </form>

                                            <!-- Kelola Soal di Kurikulum -->
                                            <?php if (! empty($q['class_id'])): ?>
                                                <a href="<?= site_url('dashboard/pembelajaran/kurikulum/kelas/' . $q['class_id']) ?>" class="btn btn-sm btn-outline-info radius-4 px-8 py-4" title="Kelola Butir Soal di Kurikulum">
                                                    <iconify-icon icon="solar:checklist-minimalistic-outline"></iconify-icon>
                                                </a>
                                            <?php endif; ?>

                                            <!-- Impor Soal Quiz Modal Trigger -->
                                            <button type="button" class="btn btn-sm btn-outline-success radius-4 px-8 py-4 text-success-main"
                                                    onclick="openDashQuizImportModal(<?= $q['id'] ?>, '<?= esc(addslashes($q['title'])) ?>')" title="Impor Butir Soal (Excel / CSV)">
                                                <iconify-icon icon="solar:file-download-outline"></iconify-icon>
                                            </button>

                                            <!-- Ubah Kuis Modal Trigger -->
                                            <button type="button" class="btn btn-sm btn-outline-warning-600 radius-4 px-8 py-4"
                                                    onclick="populateEditQuiz(<?= htmlspecialchars(json_encode($q), ENT_QUOTES, 'UTF-8') ?>)" title="Ubah Pengaturan Kuis">
                                                <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                            </button>

                                            <!-- Hapus Kuis -->
                                            <form action="<?= route_to('dashboard.learning.assessments.quiz.delete', $q['id']) ?>" method="post"
                                                  onsubmit="return confirm('Hapus kuis ini beserta butir soalnya?');" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger radius-4 px-8 py-4 text-danger-main" title="Hapus Kuis">
                                                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <!-- =================================================================== -->
        <!-- TAB 2: TUGAS & PENUGASAN -->
        <!-- =================================================================== -->
        <?php elseif ($currentTab === 'assignments'): ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th>Judul Tugas</th>
                            <th>Level Penugasan</th>
                            <th>Kelas</th>
                            <th>Batas Waktu (Deadline)</th>
                            <th class="text-center">Sifat</th>
                            <th class="text-center">Submisi Masuk</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($assignments)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-secondary-light py-32">
                                    <iconify-icon icon="solar:document-text-outline" class="text-3xl text-secondary-light mb-2"></iconify-icon>
                                    <p class="mb-0 text-sm">Belum ada penugasan yang dibuat. Klik tombol <strong>+ Tambah Tugas</strong> untuk membuat tugas baru.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($assignments as $a): ?>
                                <tr>
                                    <td>
                                        <?php $asgnGuide = parse_assignment_guide($a['instructions'] ?? ''); ?>
                                        <h6 class="text-sm fw-semibold mb-0 text-neutral-900"><?= esc($a['title']) ?></h6>
                                        <?php if (! empty($asgnGuide['instructions'])): ?>
                                            <span class="text-xs text-secondary-light line-clamp-1"><?= esc($asgnGuide['instructions']) ?></span>
                                        <?php endif; ?>
                                        <?php if (! empty($asgnGuide['has_guide']) && ! empty($asgnGuide['guide_url'])): ?>
                                            <a href="<?= base_url($asgnGuide['guide_url']) ?>" target="_blank" class="badge bg-danger-50 text-danger-600 border border-danger-200 radius-4 text-xxs mt-1 d-inline-flex align-items-center gap-1" title="Lihat Petunjuk PDF">
                                                <iconify-icon icon="solar:file-text-bold"></iconify-icon>
                                                <span>Petunjuk PDF: <?= esc($asgnGuide['guide_filename'] ?? 'PDF') ?></span>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (! empty($a['module_title'])): ?>
                                            <span class="badge bg-primary-50 text-primary-700 px-8 py-4 radius-4 text-xs font-monospace">
                                                Modul: <?= esc($a['module_title']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-neutral-100 text-neutral-800 px-8 py-4 radius-4 text-xs font-monospace">
                                                Level Kelas (Utama)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-medium text-xs text-neutral-900 d-block"><?= esc($a['class_name'] ?? '—') ?></span>
                                        <span class="text-xxs text-secondary-light"><?= esc($a['course_title'] ?? '') ?></span>
                                    </td>
                                    <td>
                                        <?= ! empty($a['due_at']) ? esc(date('d M Y, H:i', strtotime($a['due_at']))) : '<span class="text-secondary-light text-xs">Tanpa batas waktu</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <?= ! empty($a['is_required']) ? '<span class="badge bg-warning-50 text-warning-700 px-8 py-4 radius-4 text-xs fw-semibold">Wajib</span>' : '<span class="text-xs text-secondary-light">Opsional</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('dashboard/pembelajaran/assessment?tab=submissions&class_id=' . ($a['class_id'] ?? '')) ?>" class="badge bg-info-50 text-info-700 px-10 py-4 radius-4 text-xs fw-semibold text-decoration-none">
                                            <?= number_format($a['submission_count'] ?? 0) ?> Submisi
                                        </a>
                                        <?php if ((int) ($a['pending_grade_count'] ?? 0) > 0): ?>
                                            <div class="text-xxs text-warning-main mt-1"><?= (int) $a['pending_grade_count'] ?> perlu dinilai</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= view('dashboard/components/status_badge', ['status' => $a['status']]) ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <!-- Lihat Submisi -->
                                            <a href="<?= site_url('dashboard/pembelajaran/assessment?tab=submissions&class_id=' . ($a['class_id'] ?? '')) ?>" class="btn btn-sm btn-outline-info radius-4 px-8 py-4" title="Lihat Submisi Peserta">
                                                <iconify-icon icon="solar:users-group-two-rounded-outline"></iconify-icon>
                                            </a>

                                            <!-- Ubah Tugas -->
                                            <button type="button" class="btn btn-sm btn-outline-warning-600 radius-4 px-8 py-4"
                                                    onclick="populateEditAssignment(<?= htmlspecialchars(json_encode($a), ENT_QUOTES, 'UTF-8') ?>)" title="Ubah Pengaturan Tugas">
                                                <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                            </button>

                                            <!-- Hapus Tugas -->
                                            <form action="<?= route_to('dashboard.learning.assessments.assignment.delete', $a['id']) ?>" method="post"
                                                  onsubmit="return confirm('Hapus penugasan ini?');" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger radius-4 px-8 py-4 text-danger-main" title="Hapus Tugas">
                                                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <!-- =================================================================== -->
        <!-- TAB 3: SUBMISI TUGAS PESERTA -->
        <!-- =================================================================== -->
        <?php elseif ($currentTab === 'submissions'): ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th>Peserta</th>
                            <th>Judul Tugas & Kelas</th>
                            <th>Tanggal Pengumpulan</th>
                            <th>Isi / Berkas Jawaban</th>
                            <th class="text-center">Nilai</th>
                            <th class="text-center">Status</th>
                            <th>Penilai</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($submissions)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-secondary-light py-32">
                                    <iconify-icon icon="solar:inbox-in-outline" class="text-3xl text-secondary-light mb-2"></iconify-icon>
                                    <p class="mb-0 text-sm">Belum ada submisi tugas yang dikumpulkan peserta.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($submissions as $sub): ?>
                                <tr>
                                    <td>
                                        <h6 class="text-sm fw-semibold mb-0 text-neutral-900"><?= esc($sub['student_name']) ?></h6>
                                        <span class="text-xs text-secondary-light">@<?= esc($sub['username']) ?> &bull; <?= esc($sub['student_email'] ?? '') ?></span>
                                    </td>
                                    <td>
                                        <h6 class="text-sm fw-semibold mb-0 text-neutral-900"><?= esc($sub['assignment_title']) ?></h6>
                                        <span class="text-xs text-primary-600"><?= esc($sub['class_name']) ?></span>
                                    </td>
                                    <td>
                                        <span class="text-xs text-neutral-800 d-block">
                                            <?= ! empty($sub['submitted_at']) ? esc(date('d M Y, H:i', strtotime($sub['submitted_at']))) : (! empty($sub['created_at']) ? esc(date('d M Y, H:i', strtotime($sub['created_at']))) : '—') ?>
                                        </span>
                                        <span class="text-xxs text-secondary-light font-monospace">Percobaan #<?= (int) ($sub['attempt_number'] ?? 1) ?></span>
                                    </td>
                                    <td>
                                        <?php if (! empty($sub['content'])): ?>
                                            <p class="text-xs text-neutral-800 line-clamp-1 mb-1" title="<?= esc($sub['content']) ?>">
                                                <?= esc($sub['content']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ((int) ($sub['file_count'] ?? 0) > 0): ?>
                                            <span class="badge bg-neutral-100 text-neutral-800 text-xxs radius-4 d-inline-flex align-items-center gap-1">
                                                <iconify-icon icon="solar:paperclip-outline"></iconify-icon>
                                                <?= (int) $sub['file_count'] ?> File Terlampir
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($sub['score'] !== null): ?>
                                            <span class="badge bg-success-50 text-success-700 px-10 py-4 radius-4 text-xs font-monospace fw-bold">
                                                <?= number_format((float) $sub['score'], 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-neutral-100 text-secondary-light px-8 py-3 radius-4 text-xs font-monospace">Belum Dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= view('dashboard/components/status_badge', ['status' => $sub['status']]) ?>
                                    </td>
                                    <td>
                                        <span class="text-xs text-neutral-800"><?= esc($sub['evaluator_name'] ?? '—') ?></span>
                                        <?php if (! empty($sub['graded_at'])): ?>
                                            <span class="text-xxs text-secondary-light d-block"><?= date('d/m/y H:i', strtotime($sub['graded_at'])) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <!-- Beri Nilai / Quick Grade Modal Trigger -->
                                            <button type="button" class="btn btn-sm btn-primary-600 radius-4 px-10 py-4 text-xs d-inline-flex align-items-center gap-1"
                                                    onclick="openGradeModal(<?= htmlspecialchars(json_encode($sub), ENT_QUOTES, 'UTF-8') ?>)"
                                                    title="Beri Nilai & Evaluasi Submisi">
                                                <iconify-icon icon="solar:pen-bold"></iconify-icon>
                                                <span><?= $sub['score'] !== null ? 'Ubah Nilai' : 'Nilai' ?></span>
                                            </button>

                                            <!-- Detail Link -->
                                            <a href="<?= site_url('dashboard/pembelajaran/penilaian/' . $sub['id']) ?>" class="btn btn-sm btn-outline-info radius-4 px-8 py-4" title="Lihat Detail Penilaian Lengkap">
                                                <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <!-- =================================================================== -->
        <!-- TAB 4: BANK SOAL -->
        <!-- =================================================================== -->
        <?php elseif ($currentTab === 'bank_soal'): ?>
            <div class="p-20">
                <?php if (empty($questions)): ?>
                    <div class="text-center py-32 text-secondary-light">
                        <iconify-icon icon="solar:question-circle-outline" class="text-4xl mb-2 text-primary-main"></iconify-icon>
                        <p class="mb-0 text-sm">Belum ada butir pertanyaan yang terdaftar di bank soal kuis.</p>
                    </div>
                <?php else: ?>
                    <div class="accordion" id="accordionBankSoal">
                        <?php foreach ($questions as $qIdx => $q): ?>
                            <?php
                            $collapseId = 'collapseBankQ_' . $q['id'];
                            $headingId  = 'headingBankQ_' . $q['id'];
                            $opts       = $q['options'] ?? [];
                            $optLetters = ['A', 'B', 'C', 'D', 'E'];
                            $correctLetter = null;
                            foreach ($opts as $oi => $o) {
                                if (! empty($o['is_correct'])) {
                                    $correctLetter = $optLetters[$oi] ?? (string) ($oi + 1);
                                    break;
                                }
                            }
                            ?>
                            <div class="accordion-item border radius-8 mb-8 overflow-hidden">
                                <h2 class="accordion-header" id="<?= $headingId ?>">
                                    <div class="d-flex align-items-center justify-content-between p-12 bg-white gap-2 flex-wrap">
                                        <button class="accordion-button collapsed flex-grow-1 p-0 bg-transparent shadow-none text-neutral-900 border-0"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>"
                                                aria-expanded="false" aria-controls="<?= $collapseId ?>">
                                            <div class="d-flex align-items-center gap-10 flex-grow-1 me-12">
                                                <span class="badge bg-primary-50 text-primary-700 fw-bold font-monospace radius-4 px-8 py-3 text-xs">
                                                    #<?= $q['id'] ?>
                                                </span>
                                                <span class="text-xs fw-semibold text-neutral-800 text-truncate" style="max-width: 500px;" title="<?= esc($q['prompt']) ?>">
                                                    <?= esc($q['prompt']) ?>
                                                </span>
                                            </div>
                                        </button>

                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <span class="badge bg-neutral-100 text-neutral-800 text-xxs radius-4 font-monospace">
                                                <?= number_format((float) $q['points'], 1) ?> Poin
                                            </span>
                                            <span class="badge bg-info-50 text-info-700 text-xxs radius-4">
                                                <?= esc($q['quiz_title']) ?>
                                            </span>
                                            <?php if ($correctLetter !== null): ?>
                                                <span class="badge bg-success-50 text-success-700 text-xxs radius-4 fw-bold">
                                                    Kunci: <?= $correctLetter ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (! empty($q['class_id'])): ?>
                                                <a href="<?= site_url('dashboard/pembelajaran/kurikulum/kelas/' . $q['class_id']) ?>" class="btn btn-xs btn-outline-primary-600 radius-4 px-8 py-4" title="Buka di Kurikulum Kelas">
                                                    <iconify-icon icon="solar:arrow-right-up-outline"></iconify-icon>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </h2>

                                <div id="<?= $collapseId ?>" class="accordion-collapse collapse" aria-labelledby="<?= $headingId ?>" data-bs-parent="#accordionBankSoal">
                                    <div class="accordion-body p-16 pt-0 border-top bg-neutral-25">
                                        <div class="mb-12 pt-12">
                                            <span class="text-xxs text-secondary-light d-block text-uppercase fw-semibold mb-1">Pertanyaan Lengkap:</span>
                                            <div class="p-12 radius-6 bg-white border text-sm text-neutral-900 fw-medium">
                                                <?= nl2br(esc($q['prompt'])) ?>
                                            </div>
                                        </div>

                                        <span class="text-xxs text-secondary-light d-block text-uppercase fw-semibold mb-6">Pilihan Jawaban:</span>
                                        <div class="row g-8">
                                            <?php if (empty($opts)): ?>
                                                <div class="col-12">
                                                    <span class="text-xs text-danger-main italic">Belum ada pilihan jawaban untuk soal ini.</span>
                                                </div>
                                            <?php else: ?>
                                                <?php foreach ($opts as $oIdx => $opt): ?>
                                                    <?php
                                                    $letter = $optLetters[$oIdx] ?? (string) ($oIdx + 1);
                                                    $isCorrect = ! empty($opt['is_correct']);
                                                    ?>
                                                    <div class="col-md-6">
                                                        <div class="p-10 radius-6 border <?= $isCorrect ? 'bg-success-50 border-success-subtle text-success-900' : 'bg-white border-neutral-200 text-neutral-800' ?> d-flex align-items-center justify-content-between gap-2">
                                                            <div class="d-flex align-items-center gap-8">
                                                                <span class="w-24-px h-24-px rounded-circle <?= $isCorrect ? 'bg-success-600 text-white' : 'bg-neutral-100 text-neutral-700' ?> fw-bold text-xs d-flex justify-content-center align-items-center flex-shrink-0">
                                                                    <?= $letter ?>
                                                                </span>
                                                                <span class="text-xs <?= $isCorrect ? 'fw-bold' : '' ?>">
                                                                    <?= esc($opt['option_text']) ?>
                                                                </span>
                                                            </div>
                                                            <?php if ($isCorrect): ?>
                                                                <span class="badge bg-success-600 text-white text-xxs radius-4 flex-shrink-0 d-inline-flex align-items-center gap-1">
                                                                    <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                                                    Kunci Benar
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (! empty($q['explanation'])): ?>
                                            <div class="mt-12 p-10 radius-6 bg-info-50 border border-info-200 text-info-900 text-xs d-flex align-items-start gap-2">
                                                <iconify-icon icon="solar:info-circle-bold" class="text-base text-info-main mt-1 flex-shrink-0"></iconify-icon>
                                                <div>
                                                    <strong class="d-block mb-1 text-info-700">Pembahasan / Penjelasan:</strong>
                                                    <span><?= nl2br(esc($q['explanation'])) ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if (isset($pager)): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-16 px-24">
                <?= $pager->links('assessments', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ======================================================================= -->
<!-- MODALS -->
<!-- ======================================================================= -->

<!-- 1. Quiz Modal (Create & Edit) -->
<div class="modal fade" id="quizModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg" id="quizModalTitle">Tambah Kuis Pembelajaran</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quizForm" action="<?= route_to('dashboard.learning.assessments.quiz.store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="quiz_class_id">Kelas Pembelajaran <span class="text-danger-main">*</span></label>
                        <select id="quiz_class_id" name="class_id" class="form-select radius-8" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($classes as $cls): ?>
                                <option value="<?= esc($cls['id']) ?>" <?= ((string) ($filters['class_id'] ?? '') === (string) $cls['id']) ? 'selected' : '' ?>>
                                    <?= esc($cls['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="quiz_title">Judul Kuis <span class="text-danger-main">*</span></label>
                        <input id="quiz_title" type="text" name="title" class="form-control radius-8" required placeholder="Contoh: Kuis Modul 1: Pemahaman Regulasi PBJ">
                    </div>

                    <div class="row gy-16 mb-16">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="quiz_type">Tipe Kuis</label>
                            <select id="quiz_type" name="type" class="form-select radius-8">
                                <option value="lesson_quiz">Kuis Modul</option>
                                <option value="pre_test">Pre-Test (Diagnostik Awal)</option>
                                <option value="post_test">Post-Test (Ujian Kelulusan)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="quiz_duration">Durasi Timer (Menit)</label>
                            <input id="quiz_duration" type="number" min="0" name="duration_minutes" class="form-control radius-8" placeholder="0 = Bebas">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="quiz_attempts">Attempt Limit</label>
                            <input id="quiz_attempts" type="number" min="1" name="attempt_limit" class="form-control radius-8" value="3" required>
                        </div>
                    </div>

                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="quiz_instructions">Petunjuk Pengerjaan</label>
                        <textarea id="quiz_instructions" name="instructions" class="form-control radius-8" rows="3" placeholder="Pilihlah salah satu jawaban yang paling tepat..."></textarea>
                    </div>

                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="quiz_status">Status Publikasi</label>
                            <select id="quiz_status" name="status" class="form-select radius-8">
                                <option value="published">Published (Aktif)</option>
                                <option value="draft">Draft (Disembunyikan)</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center pt-24">
                            <div class="form-check">
                                <input id="quiz_req" type="checkbox" name="is_required" value="1" class="form-check-input" checked>
                                <label class="form-check-label text-sm fw-medium" for="quiz_req">Wajib untuk kelulusan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11" id="quizSubmitBtn">Simpan Kuis</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Assignment Modal (Create & Edit) -->
<div class="modal fade" id="assignmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg" id="assignmentModalTitle">Tambah Penugasan / Studi Kasus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignmentForm" action="<?= route_to('dashboard.learning.assessments.assignment.store') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="asg_class_id">Kelas Pembelajaran <span class="text-danger-main">*</span></label>
                            <select id="asg_class_id" name="class_id" class="form-select radius-8" required onchange="handleClassChangeForAssignment(this.value)">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($classes as $cls): ?>
                                    <option value="<?= esc($cls['id']) ?>" <?= ((string) ($filters['class_id'] ?? '') === (string) $cls['id']) ? 'selected' : '' ?>>
                                        <?= esc($cls['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="asg_level">Level Penugasan</label>
                            <select id="asg_level" name="level" class="form-select radius-8" onchange="toggleAssignmentLevel(this.value)">
                                <option value="class">Level Kelas (Tugas Akhir Pelatihan)</option>
                                <option value="module">Level Modul (Spesifik Modul)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-16 d-none" id="moduleSelectGroup">
                        <label class="form-label fw-semibold text-sm mb-6" for="asg_module_id">Modul Terkait</label>
                        <select id="asg_module_id" name="module_id" class="form-select radius-8">
                            <option value="">-- Pilih Modul --</option>
                        </select>
                    </div>

                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="asg_title">Judul Tugas <span class="text-danger-main">*</span></label>
                        <input id="asg_title" type="text" name="title" class="form-control radius-8" required placeholder="Contoh: Studi Kasus Penyusunan HPS dan Rancangan Kontrak">
                    </div>

                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="asg_due">Batas Waktu (Deadline)</label>
                            <input id="asg_due" type="datetime-local" name="due_at" class="form-control radius-8">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-sm mb-6" for="asg_max_attempts">Max Attempt</label>
                            <input id="asg_max_attempts" type="number" min="1" name="max_attempts" class="form-control radius-8" value="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-sm mb-6" for="asg_status">Status</label>
                            <select id="asg_status" name="status" class="form-select radius-8">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="asg_instructions">Instruksi & Panduan Pengerjaan</label>
                        <textarea id="asg_instructions" name="instructions" class="form-control radius-8" rows="4" placeholder="Jelaskan kriteria tugas, format berkas yang harus diunggah, dan indikator penilaian..."></textarea>
                    </div>

                    <!-- Upload File Petunjuk PDF -->
                    <div class="mb-16 p-16 radius-8 bg-neutral-50 border">
                        <label class="form-label fw-semibold text-sm mb-4 d-flex align-items-center gap-2" for="asg_guide_file">
                            <iconify-icon icon="solar:document-bold" class="text-danger-main text-lg"></iconify-icon>
                            <span>Upload Berkas Petunjuk Tugas (PDF)</span>
                        </label>
                        <input id="asg_guide_file" type="file" name="guide_file" accept=".pdf,application/pdf" class="form-control radius-8 bg-white">
                        <small class="text-secondary-light text-xxs d-block mt-4">
                            Format PDF (.pdf) maks. 10MB. File petunjuk studi kasus ini dapat diunduh dan dilihat langsung (View PDF) oleh peserta di LMS.
                        </small>
                        <div id="asg_existing_guide_wrapper" class="d-none mt-12 p-12 radius-8 bg-white border d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="solar:file-check-bold" class="text-success-main text-xl"></iconify-icon>
                                <div>
                                    <span class="text-xs fw-semibold text-neutral-900 d-block" id="asg_existing_guide_name">petunjuk.pdf</span>
                                    <span class="text-xxs text-secondary-light" id="asg_existing_guide_size"></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="#" target="_blank" id="asg_existing_guide_link" class="btn btn-xs btn-outline-primary radius-4 px-8 py-3">
                                    <iconify-icon icon="solar:eye-outline"></iconify-icon> Lihat File
                                </a>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="remove_guide_file" value="1" id="asg_remove_guide">
                                    <label class="form-check-label text-danger-main text-xs" for="asg_remove_guide">Hapus file</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-check">
                        <input id="asg_req" type="checkbox" name="is_required" value="1" class="form-check-input" checked>
                        <label class="form-check-label text-sm fw-medium" for="asg_req">Tugas wajib diselesaikan untuk syarat kelulusan sertifikat</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11" id="assignmentSubmitBtn">Simpan Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Quick Grade Modal (Evaluasi Submisi) -->
<div class="modal fade" id="gradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg">Evaluasi & Penilaian Submisi Peserta</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="gradeForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <!-- Participant & Assignment Meta Header -->
                    <div class="p-16 radius-8 bg-neutral-50 border mb-20">
                        <div class="row gy-12">
                            <div class="col-md-6">
                                <span class="text-xxs text-secondary-light text-uppercase fw-semibold d-block">Nama Peserta:</span>
                                <h6 class="text-sm fw-bold mb-0 text-neutral-900" id="grade_student_name"></h6>
                                <span class="text-xs text-secondary-light" id="grade_student_info"></span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-xxs text-secondary-light text-uppercase fw-semibold d-block">Tugas & Kelas:</span>
                                <h6 class="text-sm fw-bold mb-0 text-neutral-900" id="grade_assignment_title"></h6>
                                <span class="text-xs text-primary-600" id="grade_class_name"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Content / Answer Box -->
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-sm mb-6">Jawaban / Catatan Peserta:</label>
                        <div class="p-16 radius-8 bg-white border text-sm text-neutral-900" id="grade_content_preview" style="max-height: 160px; overflow-y: auto;">
                            <em class="text-secondary-light">Tidak ada catatan teks tertulis.</em>
                        </div>
                    </div>

                    <!-- Scoring Form Inputs -->
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="grade_score">Skor Nilai (0 - 100) <span class="text-danger-main">*</span></label>
                            <input id="grade_score" type="number" step="0.1" min="0" max="100" name="score" class="form-control radius-8 font-monospace fw-bold" required placeholder="Contoh: 85.5">
                            <span class="text-xxs text-secondary-light">Nilai akan otomatis tersinkronisasi ke progres pembelajaran dan kelulusan peserta.</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="grade_status">Status Penilaian</label>
                            <select id="grade_status" name="status" class="form-select radius-8">
                                <option value="graded">Graded (Selesai Dinilai)</option>
                                <option value="revision_required">Perlu Revisi (Tugas Belum Memenuhi Kriteria)</option>
                                <option value="in_review">Sedang Ditinjau</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-sm mb-6" for="grade_feedback">Umpan Balik / Catatan Penilai</label>
                        <textarea id="grade_feedback" name="feedback" class="form-control radius-8" rows="3" placeholder="Tuliskan apresiasi, koreksi analisis, atau arahan revisi bagi peserta..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Penilaian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. Modal Impor Soal Quiz Dashboard (WowDash UI) -->
<div class="modal fade" id="dashboardImportQuestionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <div>
                    <h6 class="modal-title fw-semibold text-lg mb-1" id="dashImportTitle">Impor Soal Ujian (Excel / CSV)</h6>
                    <small class="text-secondary-light" id="dashImportSubtitle">Kuis Pembelajaran</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dashImportForm" action="" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="alert alert-warning border border-warning-200 radius-8 p-12 mb-16 text-xs text-warning-800 d-flex gap-2">
                        <iconify-icon icon="solar:danger-triangle-bold" class="text-xl flex-shrink-0 text-warning-main"></iconify-icon>
                        <div>
                            <strong>PENTING:</strong> Mengimpor berkas butir soal akan <u>menimpa ulang</u> seluruh soal yang sudah ada pada kuis ini (hapus lalu ganti baru dengan data pada file).
                        </div>
                    </div>

                    <div class="mb-16 p-12 radius-8 bg-neutral-50 border d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-xs fw-semibold text-neutral-900 d-block">Format Berkas Impor:</span>
                            <span class="text-xxs text-secondary-light">Gunakan template resmi Begawi LMS (.csv / .xlsx)</span>
                        </div>
                        <a href="#" id="dashDownloadTemplateBtn" class="btn btn-sm btn-outline-primary radius-6 px-12 py-6 text-xs d-inline-flex align-items-center gap-1">
                            <iconify-icon icon="solar:download-square-outline"></iconify-icon>
                            <span>Unduh Template</span>
                        </a>
                    </div>

                    <div class="mb-12">
                        <label class="form-label fw-semibold text-sm mb-6" for="dash_question_file">Pilih Berkas Soal <span class="text-danger-main">*</span></label>
                        <input type="file" id="dash_question_file" name="question_file" accept=".csv,.xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv" class="form-control radius-8" required>
                        <small class="text-secondary-light text-xxs d-block mt-4">
                            Mendukung file Excel (.xlsx) atau CSV (pemisah koma / titik koma) dengan kolom: <em>question, option_a, option_b, option_c, option_d, option_e, correct_answer, points, explanation</em>.
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:upload-track-2-outline"></iconify-icon>
                        <span>Mulai Impor & Timpa Soal</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const modulesByClass = <?= json_encode($modulesByClass) ?>;

function openCreateQuizModal() {
    document.getElementById('quizForm').action = '<?= route_to('dashboard.learning.assessments.quiz.store') ?>';
    document.getElementById('quizModalTitle').innerText = 'Tambah Kuis Pembelajaran';
    document.getElementById('quiz_title').value = '';
    document.getElementById('quiz_type').value = 'lesson_quiz';
    document.getElementById('quiz_duration').value = '';
    document.getElementById('quiz_attempts').value = '3';
    document.getElementById('quiz_instructions').value = '';
    document.getElementById('quiz_status').value = 'published';
    document.getElementById('quiz_req').checked = true;
    document.getElementById('quizSubmitBtn').innerText = 'Simpan Kuis';
}

function populateEditQuiz(quiz) {
    document.getElementById('quizForm').action = '<?= site_url('dashboard/pembelajaran/assessment/quiz/') ?>' + quiz.id + '/ubah';
    document.getElementById('quizModalTitle').innerText = 'Ubah Kuis: ' + quiz.title;
    document.getElementById('quiz_class_id').value = quiz.class_id;
    document.getElementById('quiz_title').value = quiz.title || '';
    document.getElementById('quiz_type').value = quiz.type || 'lesson_quiz';
    document.getElementById('quiz_duration').value = quiz.duration_minutes || '';
    document.getElementById('quiz_attempts').value = quiz.attempt_limit || '1';
    document.getElementById('quiz_instructions').value = quiz.instructions || '';
    document.getElementById('quiz_status').value = quiz.status || 'published';
    document.getElementById('quiz_req').checked = (quiz.is_required == 1);
    document.getElementById('quizSubmitBtn').innerText = 'Perbarui Kuis';

    new bootstrap.Modal(document.getElementById('quizModal')).show();
}

function handleClassChangeForAssignment(classId, selectedModuleId = null) {
    const modSelect = document.getElementById('asg_module_id');
    modSelect.innerHTML = '<option value="">-- Pilih Modul --</option>';

    if (classId && modulesByClass[classId]) {
        modulesByClass[classId].forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.innerText = m.title;
            if (selectedModuleId && parseInt(selectedModuleId) === parseInt(m.id)) {
                opt.selected = true;
            }
            modSelect.appendChild(opt);
        });
    }
}

function toggleAssignmentLevel(level) {
    const modWrapper = document.getElementById('moduleSelectGroup');
    if (level === 'module') {
        modWrapper.classList.remove('d-none');
    } else {
        modWrapper.classList.add('d-none');
    }
}

function openCreateAssignmentModal() {
    document.getElementById('assignmentForm').action = '<?= route_to('dashboard.learning.assessments.assignment.store') ?>';
    document.getElementById('assignmentModalTitle').innerText = 'Tambah Penugasan / Studi Kasus';
    document.getElementById('asg_title').value = '';
    document.getElementById('asg_level').value = 'class';
    toggleAssignmentLevel('class');
    document.getElementById('asg_due').value = '';
    document.getElementById('asg_max_attempts').value = '1';
    document.getElementById('asg_status').value = 'published';
    document.getElementById('asg_instructions').value = '';
    document.getElementById('asg_req').checked = true;
    document.getElementById('asg_guide_file').value = '';
    document.getElementById('asg_existing_guide_wrapper').classList.add('d-none');
    if (document.getElementById('asg_remove_guide')) document.getElementById('asg_remove_guide').checked = false;
    document.getElementById('assignmentSubmitBtn').innerText = 'Simpan Tugas';

    const currentClassId = document.getElementById('asg_class_id').value;
    if (currentClassId) {
        handleClassChangeForAssignment(currentClassId);
    }
}

function populateEditAssignment(asgn) {
    document.getElementById('assignmentForm').action = '<?= site_url('dashboard/pembelajaran/assessment/assignment/') ?>' + asgn.id + '/ubah';
    document.getElementById('assignmentModalTitle').innerText = 'Ubah Tugas: ' + asgn.title;
    document.getElementById('asg_class_id').value = asgn.class_id;
    document.getElementById('asg_title').value = asgn.title || '';

    const isModuleLevel = asgn.module_id && parseInt(asgn.module_id) > 0;
    document.getElementById('asg_level').value = isModuleLevel ? 'module' : 'class';
    toggleAssignmentLevel(isModuleLevel ? 'module' : 'class');
    handleClassChangeForAssignment(asgn.class_id, asgn.module_id);

    if (asgn.due_at) {
        document.getElementById('asg_due').value = asgn.due_at.replace(' ', 'T').substring(0, 16);
    } else {
        document.getElementById('asg_due').value = '';
    }

    document.getElementById('asg_max_attempts').value = asgn.max_attempts || '1';
    document.getElementById('asg_status').value = asgn.status || 'published';
    document.getElementById('asg_req').checked = (asgn.is_required == 1);
    document.getElementById('asg_guide_file').value = '';
    if (document.getElementById('asg_remove_guide')) document.getElementById('asg_remove_guide').checked = false;

    const rawInstr = asgn.instructions || '';
    const guideMatch = rawInstr.match(/<!--ASSIGNMENT_GUIDE:(.*?):ASSIGNMENT_GUIDE-->/s);
    if (guideMatch) {
        try {
            const meta = JSON.parse(guideMatch[1]);
            document.getElementById('asg_existing_guide_name').innerText = meta.filename || 'petunjuk.pdf';
            const sizeKb = meta.size ? Math.round(meta.size / 1024) + ' KB' : '';
            document.getElementById('asg_existing_guide_size').innerText = sizeKb;
            document.getElementById('asg_existing_guide_link').href = '<?= base_url() ?>/' + (meta.url || '');
            document.getElementById('asg_existing_guide_wrapper').classList.remove('d-none');
        } catch (e) {
            document.getElementById('asg_existing_guide_wrapper').classList.add('d-none');
        }
        document.getElementById('asg_instructions').value = rawInstr.replace(/<!--ASSIGNMENT_GUIDE:.*?:ASSIGNMENT_GUIDE-->/s, '').trim();
    } else {
        document.getElementById('asg_existing_guide_wrapper').classList.add('d-none');
        document.getElementById('asg_instructions').value = rawInstr;
    }

    document.getElementById('assignmentSubmitBtn').innerText = 'Perbarui Tugas';

    new bootstrap.Modal(document.getElementById('assignmentModal')).show();
}

function openDashQuizImportModal(quizId, quizTitle) {
    const form = document.getElementById('dashImportForm');
    form.action = '<?= site_url('dashboard/pembelajaran/assessment/quiz/') ?>' + quizId + '/soal/impor';
    document.getElementById('dashDownloadTemplateBtn').href = '<?= site_url('dashboard/pembelajaran/assessment/quiz/') ?>' + quizId + '/soal/template';
    document.getElementById('dashImportSubtitle').innerText = quizTitle || 'Ujian';
    document.getElementById('dash_question_file').value = '';
    new bootstrap.Modal(document.getElementById('dashboardImportQuestionsModal')).show();
}

function openGradeModal(sub) {
    document.getElementById('gradeForm').action = '<?= site_url('dashboard/pembelajaran/assessment/submission/') ?>' + sub.id + '/nilai';
    document.getElementById('grade_student_name').innerText = sub.student_name || 'Peserta';
    document.getElementById('grade_student_info').innerText = '@' + (sub.username || '') + ' • ' + (sub.student_email || '');
    document.getElementById('grade_assignment_title').innerText = sub.assignment_title || 'Tugas';
    document.getElementById('grade_class_name').innerText = sub.class_name || '';

    const previewBox = document.getElementById('grade_content_preview');
    if (sub.content && sub.content.trim() !== '') {
        previewBox.innerText = sub.content;
    } else {
        previewBox.innerHTML = '<em class="text-secondary-light">Tidak ada catatan teks tertulis.</em>';
    }

    document.getElementById('grade_score').value = sub.score !== null ? sub.score : '';
    document.getElementById('grade_status').value = sub.status || 'graded';
    document.getElementById('grade_feedback').value = sub.feedback || '';

    new bootstrap.Modal(document.getElementById('gradeModal')).show();
}
</script>

<?= $this->endSection() ?>

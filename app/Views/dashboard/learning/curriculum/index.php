<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$classes          = $classes ?? [];
$selectedClass    = $selectedClass ?? null;
$selectedClassId  = (int) ($selectedClassId ?? 0);
$modules          = $modules ?? [];
$preTest          = $preTest ?? null;
$postTest         = $postTest ?? null;
$assignments      = $assignments ?? [];
$totalLessons     = (int) ($totalLessons ?? 0);
$totalQuizzes     = (int) ($totalQuizzes ?? 0);
$totalAssignments = (int) ($totalAssignments ?? 0);
$passingGrade     = (float) ($selectedClass['passing_grade'] ?? 70.0);
?>

<!-- Header Top Navigation -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Manajemen Kurikulum Pembelajaran') ?></h6>
        <p class="text-secondary-light mb-0">Kelola alur kurikulum lengkap: Pre-Test, Modul & Lesson, Kuis Pemahaman, Penugasan, dan Post-Test Kelulusan.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <?php if ($selectedClassId > 0): ?>
            <button type="button" class="btn btn-primary-600 radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                <iconify-icon icon="solar:folder-with-files-outline" class="text-lg"></iconify-icon>
                <span>+ Modul</span>
            </button>
            <?php if (! $preTest): ?>
                <button type="button" class="btn btn-outline-info radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" onclick="openCreateQuizModal('pre_test', 'Pre-Test: Evaluasi Awal Kelas')">
                    <iconify-icon icon="solar:clipboard-check-outline" class="text-lg"></iconify-icon>
                    <span>+ Pre-Test</span>
                </button>
            <?php endif; ?>
            <button type="button" class="btn btn-outline-warning radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" onclick="openCreateAssignmentModal()">
                <iconify-icon icon="solar:document-text-outline" class="text-lg"></iconify-icon>
                <span>+ Tugas</span>
            </button>
            <?php if (! $postTest): ?>
                <button type="button" class="btn btn-outline-success radius-8 px-16 py-10 d-inline-flex align-items-center gap-2" onclick="openCreateQuizModal('post_test', 'Post-Test: Evaluasi Kelulusan Akhir')">
                    <iconify-icon icon="solar:diploma-verified-outline" class="text-lg"></iconify-icon>
                    <span>+ Post-Test</span>
                </button>
            <?php endif; ?>
            <a href="<?= site_url('dashboard/pembelajaran/kelas/' . $selectedClassId) ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-10">
                Detail Kelas
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Class Switcher & Metrics Banner -->
<div class="card mb-24 border">
    <div class="card-body p-20">
        <div class="row gy-3 align-items-center justify-content-between">
            <div class="col-lg-5">
                <label class="form-label text-xs fw-semibold text-secondary-light text-uppercase mb-4">Pilih Kelas Pembelajaran</label>
                <select class="form-select radius-8 fw-medium" onchange="window.location.href='<?= site_url('dashboard/pembelajaran/kurikulum/kelas/') ?>' + this.value;">
                    <?php foreach ($classes as $cls): ?>
                        <option value="<?= esc($cls['id']) ?>" <?= ((int) $cls['id'] === $selectedClassId) ? 'selected' : '' ?>>
                            <?= esc($cls['name']) ?> (<?= esc($cls['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($selectedClass): ?>
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2 pt-2 pt-lg-0">
                        <div class="px-14 py-8 radius-8 bg-neutral-50 border text-center">
                            <span class="text-xxs text-secondary-light text-uppercase d-block fw-semibold">Course</span>
                            <span class="fw-semibold text-sm text-neutral-900"><?= esc($selectedClass['course_title'] ?? '—') ?></span>
                        </div>
                        <div class="px-14 py-8 radius-8 bg-neutral-50 border text-center">
                            <span class="text-xxs text-secondary-light text-uppercase d-block fw-semibold">Passing Grade</span>
                            <span class="fw-bold text-sm text-success-main"><?= number_format($passingGrade, 0) ?>%</span>
                        </div>
                        <div class="px-14 py-8 radius-8 bg-neutral-50 border text-center">
                            <span class="text-xxs text-secondary-light text-uppercase d-block fw-semibold">Struktur Kurikulum</span>
                            <span class="fw-bold text-sm text-primary-600"><?= count($modules) ?> Modul &bull; <?= $totalLessons ?> Lesson</span>
                        </div>
                        <div class="px-14 py-8 radius-8 bg-neutral-50 border text-center">
                            <span class="text-xxs text-secondary-light text-uppercase d-block fw-semibold">Evaluasi</span>
                            <span class="fw-bold text-sm text-info-main"><?= $totalQuizzes ?> Tes/Kuis &bull; <?= $totalAssignments ?> Tugas</span>
                        </div>
                        <div class="px-14 py-8 radius-8 bg-neutral-50 border text-center">
                            <span class="text-xxs text-secondary-light text-uppercase d-block fw-semibold">Status Kelas</span>
                            <?= view('dashboard/components/status_badge', ['status' => $selectedClass['status']]) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Hierarchy / Lifecycle Stepper Banner -->
<div class="card mb-24 bg-gradient-start-1 border-0 shadow-none">
    <div class="card-body p-20">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 text-white">
            <div>
                <h6 class="text-white fw-bold mb-1 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:route-outline" class="text-2xl"></iconify-icon>
                    <span>Alur Hirarki Kurikulum Pembelajaran</span>
                </h6>
                <p class="text-white-50 text-sm mb-0">Peserta melewati tahapan secara terstruktur dari evaluasi diagnostik hingga sertifikasi kelulusan.</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge bg-white text-primary-600 px-12 py-6 radius-6 text-xs fw-semibold">1. Pre-Test</span>
                <iconify-icon icon="solar:alt-arrow-right-linear" class="text-white-50"></iconify-icon>
                <span class="badge bg-white text-primary-600 px-12 py-6 radius-6 text-xs fw-semibold">2. Modul & Kuis</span>
                <iconify-icon icon="solar:alt-arrow-right-linear" class="text-white-50"></iconify-icon>
                <span class="badge bg-white text-primary-600 px-12 py-6 radius-6 text-xs fw-semibold">3. Tugas / Assignment</span>
                <iconify-icon icon="solar:alt-arrow-right-linear" class="text-white-50"></iconify-icon>
                <span class="badge bg-white text-success-main px-12 py-6 radius-6 text-xs fw-bold">4. Post-Test & Lulus</span>
            </div>
        </div>
    </div>
</div>

<?php if ($selectedClassId < 1): ?>
    <div class="card">
        <div class="card-body p-40 text-center text-secondary-light">
            <iconify-icon icon="solar:chalkboard-outline" class="text-5xl mb-2"></iconify-icon>
            <h6>Pilih kelas pembelajaran di atas untuk mulai menyusun kurikulum.</h6>
        </div>
    </div>
<?php else: ?>

    <div class="d-flex flex-column gap-24">

        <!-- =================================================================== -->
        <!-- 1. PRE-TEST SECTION (GERBANG AWAL KELAS) -->
        <!-- =================================================================== -->
        <div class="card border radius-12 shadow-none">
            <div class="card-header bg-base py-16 px-24 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-12">
                    <span class="w-36-px h-36-px rounded-circle bg-info-50 text-info-600 d-flex justify-content-center align-items-center fw-bold text-sm">
                        1
                    </span>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="text-md fw-bold mb-0 text-neutral-900">PRE-TEST KELAS</h6>
                            <span class="badge bg-info-50 text-info-600 text-xs radius-4 px-8 py-3">Asesmen Awal</span>
                            <?php if ($preTest): ?>
                                <?= view('dashboard/components/status_badge', ['status' => $preTest['status']]) ?>
                                <?php if (! empty($preTest['is_enabled'])): ?>
                                    <span class="badge bg-success-50 text-success-600 text-xs radius-4 px-8 py-3 d-inline-flex align-items-center gap-1">
                                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-neutral-100 text-secondary-light text-xs radius-4 px-8 py-3">Nonaktif</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-secondary-light mb-0 mt-1">Mengukur kompetensi awal peserta sebelum membuka modul pembelajaran. Modul terkunci sampai pre-test diselesaikan.</p>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <?php if ($preTest): ?>
                        <form action="<?= route_to('dashboard.learning.curriculum.quiz.toggle', $preTest['id']) ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <?php if ($preTest['status'] === 'published'): ?>
                                <button type="submit" class="btn btn-sm btn-outline-warning radius-6 px-12 py-6 d-inline-flex align-items-center gap-1" title="Nonaktifkan Pre-Test">
                                    <iconify-icon icon="solar:eye-closed-outline"></iconify-icon>
                                    <span>Jadikan Draft</span>
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-sm btn-outline-success radius-6 px-12 py-6 d-inline-flex align-items-center gap-1" title="Aktifkan & Publikasikan Pre-Test">
                                    <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                    <span>Publikasikan</span>
                                </button>
                            <?php endif; ?>
                        </form>

                        <button type="button" class="btn btn-sm btn-outline-primary-600 radius-6 px-12 py-6 d-inline-flex align-items-center gap-1"
                                onclick="openEditQuizModal(<?= htmlspecialchars(json_encode($preTest), ENT_QUOTES, 'UTF-8') ?>)">
                            <iconify-icon icon="solar:settings-outline"></iconify-icon>
                            <span>Pengaturan</span>
                        </button>

                        <button type="button" class="btn btn-sm btn-primary-600 radius-6 px-12 py-6 d-inline-flex align-items-center gap-1"
                                onclick="openAddQuestionModal(<?= (int) $preTest['id'] ?>, 'Pre-Test: <?= esc(addslashes($preTest['title'])) ?>')">
                            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                            <span>+ Tambah Soal</span>
                        </button>

                        <form action="<?= route_to('dashboard.learning.curriculum.quiz.delete', $preTest['id']) ?>" method="post" onsubmit="return confirm('Hapus Pre-Test ini beserta seluruh soalnya?');" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger radius-6 px-8 py-6 text-danger-main" title="Hapus Pre-Test">
                                <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                            </button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="btn btn-sm btn-info radius-6 px-14 py-7 text-white d-inline-flex align-items-center gap-1"
                                onclick="openCreateQuizModal('pre_test', 'Pre-Test Evaluasi Awal')">
                            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                            <span>Buat Pre-Test Kelas</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body p-20">
                <?php if (! $preTest): ?>
                    <div class="text-center py-20 text-secondary-light">
                        <iconify-icon icon="solar:clipboard-check-outline" class="text-4xl mb-2 text-info-main"></iconify-icon>
                        <p class="mb-2 text-sm">Kelas ini belum memiliki Pre-Test. Klik tombol <strong>Buat Pre-Test Kelas</strong> untuk membuat gerbang diagnostik awal.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-16 p-12 radius-8 bg-neutral-50 border">
                        <div>
                            <span class="text-xs text-secondary-light d-block">Judul Pre-Test</span>
                            <span class="fw-semibold text-sm text-neutral-900"><?= esc($preTest['title']) ?></span>
                            <?php if (! empty($preTest['instructions'])): ?>
                                <p class="text-xs text-secondary-light mb-0 mt-1"><?= esc($preTest['instructions']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="text-center">
                                <span class="text-xs text-secondary-light d-block">Batas Attempt</span>
                                <span class="badge bg-neutral-100 text-neutral-800 fw-semibold text-xs"><?= (int) $preTest['attempt_limit'] ?>x Pengerjaan</span>
                            </div>
                            <div class="text-center">
                                <span class="text-xs text-secondary-light d-block">Total Soal</span>
                                <span class="badge bg-info-50 text-info-700 fw-bold text-xs"><?= (int) $preTest['total_questions'] ?> Soal</span>
                            </div>
                            <div class="text-center">
                                <span class="text-xs text-secondary-light d-block">Total Bobot</span>
                                <span class="badge bg-primary-50 text-primary-700 fw-bold text-xs"><?= number_format($preTest['total_points'], 1) ?> Poin</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pre-Test Questions List -->
                    <?= view('dashboard/learning/curriculum/partials/questions_accordion', [
                        'quiz'            => $preTest,
                        'accordionId'     => 'accordionPreTestQuestions',
                        'selectedClassId' => $selectedClassId,
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- 2. MODUL PEMBELAJARAN (MODULAR TREE + QUIZ MODUL) -->
        <!-- =================================================================== -->
        <div class="d-flex flex-column gap-20">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="w-28-px h-28-px rounded-circle bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center fw-bold text-xs">
                        2
                    </span>
                    <h6 class="text-md fw-bold mb-0 text-neutral-900">MODUL PEMBELAJARAN & MATERI</h6>
                    <span class="badge bg-primary-50 text-primary-600 text-xs radius-4 px-8 py-2"><?= count($modules) ?> Modul</span>
                </div>
                <button type="button" class="btn btn-sm btn-primary-600 radius-6 px-14 py-7 d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                    <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                    <span>+ Tambah Modul</span>
                </button>
            </div>

            <?php if (empty($modules)): ?>
                <div class="card border radius-12 shadow-none">
                    <div class="card-body p-32 text-center">
                        <?= view('dashboard/components/empty_state', [
                            'title'       => 'Belum Ada Modul Pembelajaran',
                            'message'     => 'Klik tombol "+ Tambah Modul" untuk mulai membuat silabus pembelajaran berurutan.',
                            'icon'        => 'solar:book-bookmark-outline',
                        ]) ?>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($modules as $mIndex => $mod): ?>
                    <?php
                    $lessons = $mod['lessons'] ?? [];
                    $modQuiz = $mod['quiz'] ?? null;
                    ?>
                    <div class="card shadow-none border radius-12">
                        <!-- Module Header -->
                        <div class="card-header bg-base py-16 px-24 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-12">
                                <span class="w-32-px h-32-px rounded-circle bg-primary-50 text-primary-600 d-flex justify-content-center align-items-center fw-bold text-sm">
                                    <?= esc($mod['sort_order']) ?>
                                </span>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="text-md fw-semibold mb-0 text-neutral-900"><?= esc($mod['title']) ?></h6>
                                        <?php if (! empty($mod['is_required'])): ?>
                                            <span class="badge bg-warning-50 text-warning-600 px-8 py-2 radius-4 text-xxs fw-semibold">Wajib</span>
                                        <?php endif; ?>
                                        <?= view('dashboard/components/status_badge', ['status' => $mod['status']]) ?>
                                    </div>
                                    <?php if (! empty($mod['description'])): ?>
                                        <p class="text-xs text-secondary-light mb-0 mt-2"><?= esc($mod['description']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <!-- + Lesson Button -->
                                <button type="button" class="btn btn-sm btn-primary-50 text-primary-600 radius-6 px-12 py-6 d-inline-flex align-items-center gap-1"
                                        data-bs-toggle="modal" data-bs-target="#addLessonModal"
                                        onclick="document.getElementById('modal_lesson_module_id').value='<?= $mod['id'] ?>'; document.getElementById('lesson_module_name').innerText='<?= esc(addslashes($mod['title'])) ?>';">
                                    <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                                    <span>+ Lesson</span>
                                </button>

                                <!-- + Quiz Modul Button if not exists -->
                                <?php if (! $modQuiz): ?>
                                    <button type="button" class="btn btn-sm btn-outline-info radius-6 px-12 py-6 d-inline-flex align-items-center gap-1"
                                            onclick="openCreateModuleQuizModal(<?= (int) $mod['id'] ?>, 'Kuis Pemahaman: <?= esc(addslashes($mod['title'])) ?>')">
                                        <iconify-icon icon="solar:quiz-outline"></iconify-icon>
                                        <span>+ Quiz Modul</span>
                                    </button>
                                <?php endif; ?>

                                <!-- Edit Module -->
                                <button type="button" class="btn btn-sm btn-outline-neutral-300 radius-6 px-8 py-6 text-secondary-light"
                                        data-bs-toggle="modal" data-bs-target="#editModuleModal"
                                        onclick="populateEditModule(<?= htmlspecialchars(json_encode($mod), ENT_QUOTES, 'UTF-8') ?>)" title="Ubah Modul">
                                    <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                </button>

                                <!-- Delete Module -->
                                <form action="<?= route_to('dashboard.learning.curriculum.module.delete', $mod['id']) ?>" method="post" onsubmit="return confirm('Hapus modul ini beserta pengaturannya?');" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger radius-6 px-8 py-6 text-danger-main" title="Hapus Modul">
                                        <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Lessons Table inside Module -->
                        <div class="card-body p-0">
                            <?php if (empty($lessons)): ?>
                                <div class="p-20 text-center text-secondary-light text-sm">
                                    Belum ada materi pada modul ini. Klik tombol <strong>+ Lesson</strong> untuk menambahkan materi teks, video, atau dokumen.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive scroll-sm">
                                    <table class="table bordered-table sm-table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 60px;" class="text-center">Urutan</th>
                                                <th>Tipe</th>
                                                <th>Materi Pembelajaran (Lesson)</th>
                                                <th>Sifat</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center" style="width: 110px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($lessons as $les): ?>
                                                <tr>
                                                    <td class="text-center fw-semibold text-secondary-light"><?= esc($les['sort_order']) ?></td>
                                                    <td>
                                                        <?php
                                                        $icon = match($les['content_type'] ?? 'text') {
                                                            'video'          => 'solar:play-circle-outline',
                                                            'document', 'pdf'=> 'solar:document-outline',
                                                            default          => 'solar:text-square-outline',
                                                        };
                                                        ?>
                                                        <span class="badge bg-neutral-100 text-neutral-800 d-inline-flex align-items-center gap-1 radius-4 px-8 py-3 text-xs font-monospace">
                                                            <iconify-icon icon="<?= $icon ?>"></iconify-icon>
                                                            <?= esc(ucfirst($les['content_type'] ?? 'text')) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <h6 class="text-sm fw-medium mb-0 text-neutral-900"><?= esc($les['title']) ?></h6>
                                                        <span class="text-xs text-secondary-light font-monospace"><?= esc($les['slug'] ?? '') ?></span>
                                                    </td>
                                                    <td>
                                                        <?= ! empty($les['is_required']) ? '<span class="text-xs text-warning-main fw-medium">Wajib Selesai</span>' : '<span class="text-xs text-secondary-light">Opsional</span>' ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= view('dashboard/components/status_badge', ['status' => $les['status']]) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-inline-flex align-items-center gap-1">
                                                            <button type="button" class="btn btn-sm btn-outline-warning-main radius-4 px-8 py-4"
                                                                    data-bs-toggle="modal" data-bs-target="#editLessonModal"
                                                                    onclick="populateEditLesson(<?= htmlspecialchars(json_encode($les), ENT_QUOTES, 'UTF-8') ?>)" title="Ubah Lesson">
                                                                <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                                            </button>
                                                            <form action="<?= route_to('dashboard.learning.curriculum.lesson.delete', $les['id']) ?>" method="post" onsubmit="return confirm('Hapus materi ini?');" class="d-inline">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="btn btn-sm btn-outline-danger radius-4 px-8 py-4 text-danger-main" title="Hapus Lesson">
                                                                    <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- =============================================== -->
                            <!-- MODULE QUIZ CARD (SUB-CARD) -->
                            <!-- =============================================== -->
                            <div class="p-16 border-top bg-neutral-50">
                                <?php if ($modQuiz): ?>
                                    <div class="card border radius-8 shadow-none bg-white">
                                        <div class="card-body p-16">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-12">
                                                <div class="d-flex align-items-center gap-10">
                                                    <span class="w-30-px h-30-px rounded-circle bg-cyan-focus text-cyan d-flex justify-content-center align-items-center">
                                                        <iconify-icon icon="solar:quiz-outline" class="text-lg"></iconify-icon>
                                                    </span>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <h6 class="text-sm fw-bold mb-0 text-neutral-900"><?= esc($modQuiz['title']) ?></h6>
                                                            <span class="badge bg-cyan-focus text-cyan text-xxs radius-4 px-8 py-2">Kuis Modul</span>
                                                            <?= view('dashboard/components/status_badge', ['status' => $modQuiz['status']]) ?>
                                                        </div>
                                                        <span class="text-xs text-secondary-light">
                                                            <?= (int) ($modQuiz['attempt_limit'] ?? 1) ?>x attempt &bull;
                                                            <?= (int) ($modQuiz['total_questions'] ?? 0) ?> Soal &bull;
                                                            Total Bobot: <?= number_format((float) ($modQuiz['total_points'] ?? 0), 1) ?> Poin
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    <form action="<?= route_to('dashboard.learning.curriculum.quiz.toggle', $modQuiz['id']) ?>" method="post" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-neutral-300 radius-6 px-10 py-5 text-xs">
                                                            <?= $modQuiz['status'] === 'published' ? 'Draft' : 'Publikasi' ?>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-primary-600 radius-6 px-10 py-5 text-xs d-inline-flex align-items-center gap-1"
                                                            onclick="openEditQuizModal(<?= htmlspecialchars(json_encode($modQuiz), ENT_QUOTES, 'UTF-8') ?>)">
                                                        <iconify-icon icon="solar:settings-outline"></iconify-icon>
                                                        <span>Ubah</span>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary-600 radius-6 px-12 py-5 text-xs d-inline-flex align-items-center gap-1"
                                                            onclick="openAddQuestionModal(<?= (int) $modQuiz['id'] ?>, 'Kuis: <?= esc(addslashes($modQuiz['title'])) ?>')">
                                                        <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                                                        <span>+ Soal</span>
                                                    </button>
                                                    <form action="<?= route_to('dashboard.learning.curriculum.quiz.delete', $modQuiz['id']) ?>" method="post" onsubmit="return confirm('Hapus Kuis Modul ini?');" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger radius-6 px-8 py-5 text-danger-main">
                                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Questions list for this Module Quiz -->
                                            <?= view('dashboard/learning/curriculum/partials/questions_accordion', [
                                                'quiz'            => $modQuiz,
                                                'accordionId'     => 'accordionModQuiz_' . $mod['id'],
                                                'selectedClassId' => $selectedClassId,
                                            ]) ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-between p-12 radius-8 border border-dashed bg-white">
                                        <div class="d-flex align-items-center gap-2 text-secondary-light text-xs">
                                            <iconify-icon icon="solar:info-circle-outline" class="text-lg"></iconify-icon>
                                            <span>Modul ini belum memiliki Kuis Evaluasi Pemahaman.</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-info radius-6 px-12 py-6 text-xs d-inline-flex align-items-center gap-1"
                                                onclick="openCreateModuleQuizModal(<?= (int) $mod['id'] ?>, 'Kuis Pemahaman: <?= esc(addslashes($mod['title'])) ?>')">
                                            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                                            <span>+ Tambah Quiz Modul</span>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- =================================================================== -->
        <!-- 3. ASSIGNMENT / TUGAS SECTION (LEVEL KELAS & MODUL) -->
        <!-- =================================================================== -->
        <div class="card border radius-12 shadow-none">
            <div class="card-header bg-base py-16 px-24 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-12">
                    <span class="w-36-px h-36-px rounded-circle bg-warning-50 text-warning-600 d-flex justify-content-center align-items-center fw-bold text-sm">
                        3
                    </span>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="text-md fw-bold mb-0 text-neutral-900">PENUGASAN & STUDI KASUS (ASSIGNMENTS)</h6>
                            <span class="badge bg-warning-50 text-warning-600 text-xs radius-4 px-8 py-3"><?= count($assignments) ?> Tugas</span>
                        </div>
                        <p class="text-xs text-secondary-light mb-0 mt-1">Dapat berupa Tugas Level Kelas (Tugas Akhir Pelatihan) atau Tugas Level Modul. Peserta mengunggah file jawaban untuk dinilai.</p>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-warning radius-6 px-14 py-7 text-dark fw-medium d-inline-flex align-items-center gap-1" onclick="openCreateAssignmentModal()">
                    <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                    <span>+ Tambah Tugas</span>
                </button>
            </div>

            <div class="card-body p-0">
                <?php if (empty($assignments)): ?>
                    <div class="text-center py-24 text-secondary-light">
                        <iconify-icon icon="solar:document-text-outline" class="text-4xl mb-2 text-warning-main"></iconify-icon>
                        <p class="mb-0 text-sm">Belum ada tugas atau studi kasus pada kelas ini. Klik tombol <strong>+ Tambah Tugas</strong> untuk membuat penugasan.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table sm-table mb-0">
                            <thead>
                                <tr>
                                    <th>Judul Tugas</th>
                                    <th>Level Penugasan</th>
                                    <th>Batas Waktu (Deadline)</th>
                                    <th class="text-center">Sifat</th>
                                    <th class="text-center">Submisi Masuk</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $asgn): ?>
                                    <tr>
                                        <td>
                                            <h6 class="text-sm fw-semibold mb-0 text-neutral-900"><?= esc($asgn['title']) ?></h6>
                                            <?php if (! empty($asgn['instructions'])): ?>
                                                <span class="text-xs text-secondary-light line-clamp-1"><?= esc($asgn['instructions']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (! empty($asgn['module_title'])): ?>
                                                <span class="badge bg-primary-50 text-primary-600 px-8 py-4 radius-4 text-xs font-monospace">
                                                    Modul: <?= esc($asgn['module_title']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-neutral-100 text-neutral-800 px-8 py-4 radius-4 text-xs font-monospace">
                                                    Level Kelas (Utama)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-xs text-secondary-light">
                                                <?= ! empty($asgn['due_at']) ? date('d M Y, H:i', strtotime($asgn['due_at'])) : 'Tanpa batas waktu' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?= ! empty($asgn['is_required']) ? '<span class="badge bg-warning-50 text-warning-600 px-8 py-3 radius-4 text-xs">Wajib</span>' : '<span class="text-xs text-secondary-light">Opsional</span>' ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info-50 text-info-700 px-10 py-4 radius-4 text-xs fw-semibold">
                                                <?= (int) ($asgn['submission_count'] ?? 0) ?> Submisi
                                            </span>
                                            <?php if ((int) ($asgn['pending_count'] ?? 0) > 0): ?>
                                                <div class="text-xxs text-warning-main mt-1"><?= (int) $asgn['pending_count'] ?> perlu dinilai</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= view('dashboard/components/status_badge', ['status' => $asgn['status']]) ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <a href="<?= site_url('dashboard/pembelajaran/assessment?tab=submissions&class_id=' . $selectedClassId) ?>" class="btn btn-sm btn-outline-info radius-4 px-8 py-4" title="Lihat Submisi Peserta">
                                                    <iconify-icon icon="solar:users-group-two-rounded-outline"></iconify-icon>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-warning-main radius-4 px-8 py-4"
                                                        onclick="populateEditAssignment(<?= htmlspecialchars(json_encode($asgn), ENT_QUOTES, 'UTF-8') ?>)" title="Ubah Tugas">
                                                    <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                                                </button>
                                                <form action="<?= route_to('dashboard.learning.curriculum.assignment.delete', $asgn['id']) ?>" method="post" onsubmit="return confirm('Hapus penugasan ini?');" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger radius-4 px-8 py-4 text-danger-main" title="Hapus Tugas">
                                                        <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- 4. POST-TEST SECTION (EVALUASI AKHIR & KELULUSAN) -->
        <!-- =================================================================== -->
        <div class="card border radius-12 shadow-none">
            <div class="card-header bg-base py-16 px-24 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-12">
                    <span class="w-36-px h-36-px rounded-circle bg-success-50 text-success-600 d-flex justify-content-center align-items-center fw-bold text-sm">
                        4
                    </span>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="text-md fw-bold mb-0 text-neutral-900">POST-TEST KELAS</h6>
                            <span class="badge bg-success-50 text-success-600 text-xs radius-4 px-8 py-3">Ujian Kelulusan Akhir</span>
                            <?php if ($postTest): ?>
                                <?= view('dashboard/components/status_badge', ['status' => $postTest['status']]) ?>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-secondary-light mb-0 mt-1">
                            Evaluasi akhir penentu kelulusan. Nilai dihitung otomatis: <strong>(Jumlah Benar / Total Soal) &times; 100</strong>. Passing grade kelas: <strong><?= number_format($passingGrade, 0) ?>%</strong>.
                        </p>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <?php if ($postTest): ?>
                        <form action="<?= route_to('dashboard.learning.curriculum.quiz.toggle', $postTest['id']) ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <?php if ($postTest['status'] === 'published'): ?>
                                <button type="submit" class="btn btn-sm btn-outline-warning radius-6 px-12 py-6 d-inline-flex align-items-center gap-1" title="Nonaktifkan Post-Test">
                                    <iconify-icon icon="solar:eye-closed-outline"></iconify-icon>
                                    <span>Jadikan Draft</span>
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-sm btn-outline-success radius-6 px-12 py-6 d-inline-flex align-items-center gap-1" title="Publikasikan Post-Test">
                                    <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                    <span>Publikasikan</span>
                                </button>
                            <?php endif; ?>
                        </form>

                        <button type="button" class="btn btn-sm btn-outline-primary-600 radius-6 px-12 py-6 d-inline-flex align-items-center gap-1"
                                onclick="openEditQuizModal(<?= htmlspecialchars(json_encode($postTest), ENT_QUOTES, 'UTF-8') ?>)">
                            <iconify-icon icon="solar:settings-outline"></iconify-icon>
                            <span>Pengaturan</span>
                        </button>

                        <button type="button" class="btn btn-sm btn-success radius-6 px-12 py-6 d-inline-flex align-items-center gap-1 text-white"
                                onclick="openAddQuestionModal(<?= (int) $postTest['id'] ?>, 'Post-Test: <?= esc(addslashes($postTest['title'])) ?>')">
                            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                            <span>+ Tambah Soal</span>
                        </button>

                        <form action="<?= route_to('dashboard.learning.curriculum.quiz.delete', $postTest['id']) ?>" method="post" onsubmit="return confirm('Hapus Post-Test ini beserta seluruh soalnya?');" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger radius-6 px-8 py-6 text-danger-main" title="Hapus Post-Test">
                                <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon>
                            </button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="btn btn-sm btn-success radius-6 px-14 py-7 text-white d-inline-flex align-items-center gap-1"
                                onclick="openCreateQuizModal('post_test', 'Post-Test: Evaluasi Kelulusan Akhir')">
                            <iconify-icon icon="solar:add-circle-outline"></iconify-icon>
                            <span>Buat Post-Test Kelas</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body p-20">
                <?php if (! $postTest): ?>
                    <div class="text-center py-20 text-secondary-light">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-4xl mb-2 text-success-main"></iconify-icon>
                        <p class="mb-2 text-sm">Kelas ini belum memiliki Post-Test. Klik tombol <strong>Buat Post-Test Kelas</strong> untuk mengaktifkan evaluasi penentu kelulusan sertifikasi.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-16 p-12 radius-8 bg-neutral-50 border">
                        <div>
                            <span class="text-xs text-secondary-light d-block">Judul Post-Test</span>
                            <span class="fw-semibold text-sm text-neutral-900"><?= esc($postTest['title']) ?></span>
                            <?php if (! empty($postTest['instructions'])): ?>
                                <p class="text-xs text-secondary-light mb-0 mt-1"><?= esc($postTest['instructions']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="text-center">
                                <span class="text-xs text-secondary-light d-block">Syarat Lulus</span>
                                <span class="badge bg-success-50 text-success-700 fw-bold text-xs">Min <?= number_format($passingGrade, 0) ?>%</span>
                            </div>
                            <div class="text-center">
                                <span class="text-xs text-secondary-light d-block">Batas Attempt</span>
                                <span class="badge bg-neutral-100 text-neutral-800 fw-semibold text-xs"><?= (int) $postTest['attempt_limit'] ?>x Pengerjaan</span>
                            </div>
                            <div class="text-center">
                                <span class="text-xs text-secondary-light d-block">Total Soal</span>
                                <span class="badge bg-info-50 text-info-700 fw-bold text-xs"><?= (int) $postTest['total_questions'] ?> Soal</span>
                            </div>
                            <div class="text-center">
                                <span class="text-xs text-secondary-light d-block">Total Bobot</span>
                                <span class="badge bg-primary-50 text-primary-700 fw-bold text-xs"><?= number_format($postTest['total_points'], 1) ?> Poin</span>
                            </div>
                        </div>
                    </div>

                    <!-- Post-Test Questions List -->
                    <?= view('dashboard/learning/curriculum/partials/questions_accordion', [
                        'quiz'            => $postTest,
                        'accordionId'     => 'accordionPostTestQuestions',
                        'selectedClassId' => $selectedClassId,
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

<?php endif; ?>

<!-- =========================================================================== -->
<!-- MODALS -->
<!-- =========================================================================== -->

<!-- 1. Add Module Modal -->
<div class="modal fade" id="addModuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg">Tambah Modul Pembelajaran</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= route_to('dashboard.learning.curriculum.module.store') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" value="<?= $selectedClassId ?>">
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="mod_title">Nama Modul <span class="text-danger-main">*</span></label>
                        <input id="mod_title" type="text" name="title" class="form-control radius-8" required placeholder="Contoh: Modul 1 — Regulasi & Kebijakan PBJ">
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="mod_desc">Deskripsi Modul</label>
                        <textarea id="mod_desc" name="description" class="form-control radius-8" rows="3" placeholder="Ringkasan kompetensi yang dicakup pada modul ini..."></textarea>
                    </div>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="mod_sort">Urutan Modul</label>
                            <input id="mod_sort" type="number" min="1" name="sort_order" class="form-control radius-8" value="<?= count($modules) + 1 ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="mod_status">Status</label>
                            <select id="mod_status" name="status" class="form-select radius-8">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input id="mod_req" type="checkbox" name="is_required" value="1" class="form-check-input" checked>
                        <label class="form-check-label text-sm fw-medium" for="mod_req">Modul wajib diselesaikan peserta</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Modul</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Edit Module Modal -->
<div class="modal fade" id="editModuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg">Ubah Modul Pembelajaran</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editModuleForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="edit_mod_title">Nama Modul <span class="text-danger-main">*</span></label>
                        <input id="edit_mod_title" type="text" name="title" class="form-control radius-8" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="edit_mod_description">Deskripsi</label>
                        <textarea id="edit_mod_description" name="description" class="form-control radius-8" rows="3"></textarea>
                    </div>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_mod_sort">Urutan</label>
                            <input id="edit_mod_sort" type="number" min="1" name="sort_order" class="form-control radius-8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_mod_status">Status</label>
                            <select id="edit_mod_status" name="status" class="form-select radius-8">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input id="edit_mod_req" type="checkbox" name="is_required" value="1" class="form-check-input">
                        <label class="form-check-label text-sm fw-medium" for="edit_mod_req">Modul wajib diselesaikan peserta</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Perbarui Modul</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg">Tambah Materi Pembelajaran (Lesson)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= route_to('dashboard.learning.curriculum.lesson.store') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="module_id" id="modal_lesson_module_id" value="">
                <div class="modal-body p-24">
                    <p class="text-xs text-secondary-light mb-16">Menambahkan materi pada modul: <strong id="lesson_module_name" class="text-primary-600"></strong></p>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-sm mb-6" for="les_title">Judul Materi <span class="text-danger-main">*</span></label>
                            <input id="les_title" type="text" name="title" class="form-control radius-8" required placeholder="Contoh: Pengantar Kerangka Kerja Pengadaan">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="les_type">Tipe Konten</label>
                            <select id="les_type" name="content_type" class="form-select radius-8">
                                <option value="text">Artikel / Teks</option>
                                <option value="video">Video</option>
                                <option value="document">Dokumen / PDF</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="les_content">Isi Konten / Penjelasan</label>
                        <textarea id="les_content" name="content" class="form-control radius-8" rows="4" placeholder="Tuliskan rangkuman isi materi, instruksi baca, atau panduan..."></textarea>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="les_ext">Tautan Eksternal / Video URL (Opsional)</label>
                        <input id="les_ext" type="url" name="external_url" class="form-control radius-8" placeholder="https://youtube.com/watch?v=... atau link google drive / zoom">
                    </div>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="les_sort">Urutan Materi</label>
                            <input id="les_sort" type="number" min="1" name="sort_order" class="form-control radius-8" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="les_status">Status</label>
                            <select id="les_status" name="status" class="form-select radius-8">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input id="les_req" type="checkbox" name="is_required" value="1" class="form-check-input" checked>
                        <label class="form-check-label text-sm fw-medium" for="les_req">Materi ini wajib diselesaikan peserta</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Materi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. Edit Lesson Modal -->
<div class="modal fade" id="editLessonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg">Ubah Materi Pembelajaran</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editLessonForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="row gy-16 mb-16">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_les_title">Judul Materi <span class="text-danger-main">*</span></label>
                            <input id="edit_les_title" type="text" name="title" class="form-control radius-8" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_les_type">Tipe Konten</label>
                            <select id="edit_les_type" name="content_type" class="form-select radius-8">
                                <option value="text">Artikel / Teks</option>
                                <option value="video">Video</option>
                                <option value="document">Dokumen / PDF</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="edit_les_content">Isi Konten</label>
                        <textarea id="edit_les_content" name="content" class="form-control radius-8" rows="4"></textarea>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="edit_les_ext">Tautan Eksternal / Video URL</label>
                        <input id="edit_les_ext" type="url" name="external_url" class="form-control radius-8">
                    </div>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_les_sort">Urutan</label>
                            <input id="edit_les_sort" type="number" min="1" name="sort_order" class="form-control radius-8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="edit_les_status">Status</label>
                            <select id="edit_les_status" name="status" class="form-select radius-8">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input id="edit_les_req" type="checkbox" name="is_required" value="1" class="form-check-input">
                        <label class="form-check-label text-sm fw-medium" for="edit_les_req">Materi ini wajib diselesaikan peserta</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Perbarui Materi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 5. Unified Quiz / Test Form Modal -->
<div class="modal fade" id="quizFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg" id="quizModalTitle">Pengaturan Ujian / Kuis</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quizForm" action="<?= route_to('dashboard.learning.curriculum.quiz.store') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" value="<?= $selectedClassId ?>">
                <input type="hidden" name="type" id="quiz_form_type" value="pre_test">
                <input type="hidden" name="module_id" id="quiz_form_module_id" value="">
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="quiz_title">Judul Ujian / Kuis <span class="text-danger-main">*</span></label>
                        <input id="quiz_title" type="text" name="title" class="form-control radius-8" required>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="quiz_instructions">Instruksi Pengerjaan</label>
                        <textarea id="quiz_instructions" name="instructions" class="form-control radius-8" rows="3" placeholder="Petunjuk pengerjaan bagi peserta..."></textarea>
                    </div>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="quiz_duration">Durasi Timer (Menit)</label>
                            <input id="quiz_duration" type="number" min="0" name="duration_minutes" class="form-control radius-8" placeholder="0 = Bebas">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="quiz_attempts">Attempt Limit</label>
                            <input id="quiz_attempts" type="number" min="1" max="10" name="attempt_limit" class="form-control radius-8" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="quiz_status">Status</label>
                            <select id="quiz_status" name="status" class="form-select radius-8">
                                <option value="published">Published (Aktif)</option>
                                <option value="draft">Draft (Nonaktif)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input id="quiz_req" type="checkbox" name="is_required" value="1" class="form-check-input" checked>
                        <label class="form-check-label text-sm fw-medium" for="quiz_req">Wajib diselesaikan peserta</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11" id="quizSubmitBtn">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 6. Question & Multiple Choice Options Modal -->
<div class="modal fade" id="questionFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg" id="questionModalTitle">Tambah Soal Pilihan Ganda</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="questionForm" action="<?= route_to('dashboard.learning.curriculum.question.store') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="quiz_id" id="qform_quiz_id" value="">
                <div class="modal-body p-24">
                    <p class="text-xs text-secondary-light mb-16">Soal untuk: <strong id="question_quiz_name" class="text-primary-600"></strong></p>

                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="qform_prompt">Pertanyaan Soal <span class="text-danger-main">*</span></label>
                        <textarea id="qform_prompt" name="prompt" class="form-control radius-8" rows="3" required placeholder="Tuliskan pertanyaan... Contoh: Apa fungsi utama pengadaan barang/jasa pemerintah?"></textarea>
                    </div>

                    <div class="row gy-16 mb-20">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="qform_points">Bobot Nilai Poin <span class="text-danger-main">*</span></label>
                            <input id="qform_points" type="number" step="0.1" min="0.1" name="points" class="form-control radius-8" value="1.0" required>
                            <span class="text-xxs text-secondary-light">Bobot poin untuk pertanyaan ini. Nilai otomatis = total poin benar / total poin &times; 100.</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="qform_explanation">Pembahasan / Penjelasan Jawaban (Opsional)</label>
                            <input id="qform_explanation" type="text" name="explanation" class="form-control radius-8" placeholder="Alasan mengapa jawaban tersebut benar...">
                        </div>
                    </div>

                    <label class="form-label fw-semibold text-sm mb-8 d-block">
                        Pilihan Jawaban & Kunci Jawaban <span class="text-danger-main">*</span>
                        <span class="text-xs text-secondary-light fw-normal">(Pilih salah satu radio button sebagai kunci jawaban benar)</span>
                    </label>

                    <div class="d-flex flex-column gap-12" id="optionsContainer">
                        <?php $optionLabels = ['A', 'B', 'C', 'D']; ?>
                        <?php foreach ($optionLabels as $i => $lbl): ?>
                            <div class="input-group">
                                <span class="input-group-text bg-neutral-100 fw-bold text-sm" style="width: 45px; justify-content: center;"><?= $lbl ?></span>
                                <input type="text" name="options[]" class="form-control option-text-input" placeholder="Teks pilihan jawaban <?= $lbl ?>..." <?= $i < 2 ? 'required' : '' ?>>
                                <div class="input-group-text bg-white">
                                    <div class="form-check d-flex align-items-center gap-1 m-0">
                                        <input class="form-check-input correct-option-radio" type="radio" name="correct_option" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?> id="radio_opt_<?= $i ?>">
                                        <label class="form-check-label text-xs fw-semibold text-success-main ms-1" for="radio_opt_<?= $i ?>">Kunci</label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 7. Assignment Modal (Class / Module Level) -->
<div class="modal fade" id="assignmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg" id="assignmentModalTitle">Tambah Penugasan / Tugas</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignmentForm" action="<?= route_to('dashboard.learning.curriculum.assignment.store') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" value="<?= $selectedClassId ?>">
                <div class="modal-body p-24">
                    <div class="row gy-16 mb-16">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-sm mb-6" for="asgn_title">Judul Tugas <span class="text-danger-main">*</span></label>
                            <input id="asgn_title" type="text" name="title" class="form-control radius-8" required placeholder="Contoh: Studi Kasus Penyusunan HPS & Evaluasi Penawaran">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="asgn_level">Level Penugasan</label>
                            <select id="asgn_level" name="level" class="form-select radius-8" onchange="toggleAssignmentModuleSelect(this.value)">
                                <option value="class">Level Kelas (Tugas Akhir)</option>
                                <option value="module">Level Modul (Spesifik Modul)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-16 d-none" id="assignmentModuleSelectWrapper">
                        <label class="form-label fw-semibold text-sm mb-6" for="asgn_module_id">Pilih Modul Terkait</label>
                        <select id="asgn_module_id" name="module_id" class="form-select radius-8">
                            <option value="">-- Pilih Modul --</option>
                            <?php foreach ($modules as $m): ?>
                                <option value="<?= esc($m['id']) ?>"><?= esc($m['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="asgn_instructions">Panduan / Instruksi Tugas</label>
                        <textarea id="asgn_instructions" name="instructions" class="form-control radius-8" rows="4" placeholder="Jelaskan instruksi pengerjaan, format berkas yang harus diunggah, dan kriteria penilaian..."></textarea>
                    </div>

                    <div class="row gy-16 mb-16">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="asgn_due">Batas Waktu (Deadline)</label>
                            <input id="asgn_due" type="datetime-local" name="due_at" class="form-control radius-8">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="asgn_max_attempts">Maksimal Upload / Percobaan</label>
                            <input id="asgn_max_attempts" type="number" min="1" max="10" name="max_attempts" class="form-control radius-8" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-sm mb-6" for="asgn_status">Status</label>
                            <select id="asgn_status" name="status" class="form-select radius-8">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-check">
                        <input id="asgn_req" type="checkbox" name="is_required" value="1" class="form-check-input" checked>
                        <label class="form-check-label text-sm fw-medium" for="asgn_req">Tugas wajib dikumpulkan (syarat kelulusan evaluasi)</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning radius-8 px-20 py-11 text-dark fw-semibold" id="assignmentSubmitBtn">Simpan Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================== -->
<!-- JAVASCRIPT HANDLERS -->
<!-- =========================================================================== -->
<script>
function populateEditModule(mod) {
    document.getElementById('editModuleForm').action = '<?= site_url('dashboard/pembelajaran/kurikulum/modul/') ?>' + mod.id + '/ubah';
    document.getElementById('edit_mod_title').value = mod.title || '';
    document.getElementById('edit_mod_description').value = mod.description || '';
    document.getElementById('edit_mod_sort').value = mod.sort_order || 1;
    document.getElementById('edit_mod_status').value = mod.status || 'published';
    document.getElementById('edit_mod_req').checked = (mod.is_required == 1);
}

function populateEditLesson(les) {
    document.getElementById('editLessonForm').action = '<?= site_url('dashboard/pembelajaran/kurikulum/lesson/') ?>' + les.id + '/ubah';
    document.getElementById('edit_les_title').value = les.title || '';
    document.getElementById('edit_les_type').value = les.content_type || 'text';
    document.getElementById('edit_les_content').value = les.content || '';
    document.getElementById('edit_les_ext').value = les.external_url || '';
    document.getElementById('edit_les_sort').value = les.sort_order || 1;
    document.getElementById('edit_les_status').value = les.status || 'published';
    document.getElementById('edit_les_req').checked = (les.is_required == 1);
}

function openCreateQuizModal(type, title) {
    document.getElementById('quizForm').action = '<?= route_to('dashboard.learning.curriculum.quiz.store') ?>';
    document.getElementById('quizModalTitle').innerText = (type === 'pre_test' ? 'Buat Pre-Test Kelas' : (type === 'post_test' ? 'Buat Post-Test Kelas' : 'Buat Kuis'));
    document.getElementById('quiz_form_type').value = type;
    document.getElementById('quiz_form_module_id').value = '';
    document.getElementById('quiz_title').value = title;
    document.getElementById('quiz_instructions').value = '';
    document.getElementById('quiz_duration').value = '';
    document.getElementById('quiz_attempts').value = '1';
    document.getElementById('quiz_status').value = 'published';
    document.getElementById('quiz_req').checked = true;
    document.getElementById('quizSubmitBtn').innerText = 'Buat Ujian / Kuis';

    new bootstrap.Modal(document.getElementById('quizFormModal')).show();
}

function openCreateModuleQuizModal(moduleId, defaultTitle) {
    document.getElementById('quizForm').action = '<?= route_to('dashboard.learning.curriculum.quiz.store') ?>';
    document.getElementById('quizModalTitle').innerText = 'Buat Kuis Evaluasi Modul';
    document.getElementById('quiz_form_type').value = 'lesson_quiz';
    document.getElementById('quiz_form_module_id').value = moduleId;
    document.getElementById('quiz_title').value = defaultTitle;
    document.getElementById('quiz_instructions').value = 'Jawab seluruh pertanyaan pilihan ganda untuk menguji pemahaman materi pada modul ini.';
    document.getElementById('quiz_duration').value = '';
    document.getElementById('quiz_attempts').value = '3';
    document.getElementById('quiz_status').value = 'published';
    document.getElementById('quiz_req').checked = true;
    document.getElementById('quizSubmitBtn').innerText = 'Buat Kuis Modul';

    new bootstrap.Modal(document.getElementById('quizFormModal')).show();
}

function openEditQuizModal(quiz) {
    document.getElementById('quizForm').action = '<?= site_url('dashboard/pembelajaran/kurikulum/quiz/') ?>' + quiz.id + '/ubah';
    document.getElementById('quizModalTitle').innerText = 'Ubah Pengaturan: ' + quiz.title;
    document.getElementById('quiz_form_type').value = quiz.type;
    document.getElementById('quiz_form_module_id').value = '';
    document.getElementById('quiz_title').value = quiz.title || '';
    document.getElementById('quiz_instructions').value = quiz.instructions || '';
    document.getElementById('quiz_duration').value = quiz.duration_minutes || '';
    document.getElementById('quiz_attempts').value = quiz.attempt_limit || '1';
    document.getElementById('quiz_status').value = quiz.status || 'published';
    document.getElementById('quiz_req').checked = (quiz.is_required == 1);
    document.getElementById('quizSubmitBtn').innerText = 'Perbarui Pengaturan';

    new bootstrap.Modal(document.getElementById('quizFormModal')).show();
}

function openAddQuestionModal(quizId, quizTitle) {
    document.getElementById('questionForm').action = '<?= route_to('dashboard.learning.curriculum.question.store') ?>';
    document.getElementById('questionModalTitle').innerText = 'Tambah Soal Pilihan Ganda';
    document.getElementById('qform_quiz_id').value = quizId;
    document.getElementById('question_quiz_name').innerText = quizTitle;
    document.getElementById('qform_prompt').value = '';
    document.getElementById('qform_points').value = '1.0';
    document.getElementById('qform_explanation').value = '';

    // Reset options
    const optionInputs = document.querySelectorAll('.option-text-input');
    optionInputs.forEach(input => input.value = '');
    const radios = document.querySelectorAll('.correct-option-radio');
    if (radios.length > 0) radios[0].checked = true;

    new bootstrap.Modal(document.getElementById('questionFormModal')).show();
}

function populateEditQuestion(q, opts, quizTitle) {
    document.getElementById('questionForm').action = '<?= site_url('dashboard/pembelajaran/kurikulum/soal/') ?>' + q.id + '/ubah';
    document.getElementById('questionModalTitle').innerText = 'Ubah Soal Pilihan Ganda';
    document.getElementById('qform_quiz_id').value = q.quiz_id;
    document.getElementById('question_quiz_name').innerText = quizTitle || 'Ujian';
    document.getElementById('qform_prompt').value = q.prompt || '';
    document.getElementById('qform_points').value = q.points || '1.0';
    document.getElementById('qform_explanation').value = q.explanation || '';

    const optionInputs = document.querySelectorAll('.option-text-input');
    const radios = document.querySelectorAll('.correct-option-radio');

    optionInputs.forEach((input, index) => {
        if (opts && opts[index]) {
            input.value = opts[index].option_text || '';
            if (opts[index].is_correct == 1 && radios[index]) {
                radios[index].checked = true;
            }
        } else {
            input.value = '';
        }
    });

    new bootstrap.Modal(document.getElementById('questionFormModal')).show();
}

function openCreateAssignmentModal() {
    document.getElementById('assignmentForm').action = '<?= route_to('dashboard.learning.curriculum.assignment.store') ?>';
    document.getElementById('assignmentModalTitle').innerText = 'Tambah Penugasan / Studi Kasus';
    document.getElementById('asgn_title').value = '';
    document.getElementById('asgn_level').value = 'class';
    toggleAssignmentModuleSelect('class');
    document.getElementById('asgn_instructions').value = '';
    document.getElementById('asgn_due').value = '';
    document.getElementById('asgn_max_attempts').value = '1';
    document.getElementById('asgn_status').value = 'published';
    document.getElementById('asgn_req').checked = true;
    document.getElementById('assignmentSubmitBtn').innerText = 'Simpan Tugas';

    new bootstrap.Modal(document.getElementById('assignmentModal')).show();
}

function populateEditAssignment(asgn) {
    document.getElementById('assignmentForm').action = '<?= site_url('dashboard/pembelajaran/kurikulum/tugas/') ?>' + asgn.id + '/ubah';
    document.getElementById('assignmentModalTitle').innerText = 'Ubah Penugasan: ' + asgn.title;
    document.getElementById('asgn_title').value = asgn.title || '';

    const isModuleLevel = asgn.module_id && parseInt(asgn.module_id) > 0;
    document.getElementById('asgn_level').value = isModuleLevel ? 'module' : 'class';
    toggleAssignmentModuleSelect(isModuleLevel ? 'module' : 'class');

    if (isModuleLevel) {
        document.getElementById('asgn_module_id').value = asgn.module_id;
    }

    document.getElementById('asgn_instructions').value = asgn.instructions || '';
    if (asgn.due_at) {
        document.getElementById('asgn_due').value = asgn.due_at.substring(0, 16);
    } else {
        document.getElementById('asgn_due').value = '';
    }
    document.getElementById('asgn_max_attempts').value = asgn.max_attempts || '1';
    document.getElementById('asgn_status').value = asgn.status || 'published';
    document.getElementById('asgn_req').checked = (asgn.is_required == 1);
    document.getElementById('assignmentSubmitBtn').innerText = 'Perbarui Tugas';

    new bootstrap.Modal(document.getElementById('assignmentModal')).show();
}

function toggleAssignmentModuleSelect(level) {
    const wrapper = document.getElementById('assignmentModuleSelectWrapper');
    if (level === 'module') {
        wrapper.classList.remove('d-none');
    } else {
        wrapper.classList.add('d-none');
    }
}
</script>

<?= $this->endSection() ?>

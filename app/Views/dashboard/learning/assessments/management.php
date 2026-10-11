<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$currentTab  = $currentTab ?? 'quizzes';
$quizzes     = $quizzes ?? [];
$assignments = $assignments ?? [];
$submissions = $submissions ?? [];
$stats       = $stats ?? [];
$classes     = $classes ?? [];
$filters     = $filters ?? [];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Assessment & Evaluasi') ?></h6>
        <p class="text-secondary-light mb-0">Kelola kuis, tugas evaluasi, bank soal, dan pemantauan submisi tugas peserta.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= site_url('dashboard/pembelajaran/assessment/bank-soal') ?>" class="btn btn-outline-primary-600 radius-8 px-16 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:folder-with-files-outline" class="text-xl"></iconify-icon>
            <span>Bank Soal</span>
        </a>
        <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addQuizModal">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            <span>Tambah Kuis</span>
        </button>
        <button type="button" class="btn btn-outline-info radius-8 px-20 py-11 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
            <iconify-icon icon="solar:document-add-outline" class="text-xl"></iconify-icon>
            <span>Tambah Tugas</span>
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-sm-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Kuis</p>
                        <h6 class="mb-0"><?= number_format($stats['total_quizzes'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:question-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Pre-test, modul & post-test</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Bank Soal Aktif</p>
                        <h6 class="mb-0 text-success-main"><?= number_format($stats['total_questions'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:checklist-minimalistic-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Pertanyaan dalam bank soal</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Total Penugasan</p>
                        <h6 class="mb-0 text-info-main"><?= number_format($stats['total_assignments'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:document-text-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Tugas & studi kasus</p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Submisi Tugas</p>
                        <h6 class="mb-0 text-warning-main"><?= number_format($stats['total_submissions'] ?? 0) ?></h6>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:inbox-in-outline" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Total jawaban dikumpulkan</p>
            </div>
        </div>
    </div>
</div>

<!-- Nav Tabs -->
<div class="card">
    <div class="card-header border-bottom bg-base py-12 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <ul class="nav nav-tabs card-header-tabs border-0" id="assessmentTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'quizzes' ? 'active' : '' ?> fw-semibold" href="<?= site_url('dashboard/pembelajaran/assessment?tab=quizzes') ?>">
                    <iconify-icon icon="solar:question-circle-outline" class="me-1"></iconify-icon>
                    Kuis & Ujian (<?= count($quizzes) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'assignments' ? 'active' : '' ?> fw-semibold" href="<?= site_url('dashboard/pembelajaran/assessment?tab=assignments') ?>">
                    <iconify-icon icon="solar:document-text-outline" class="me-1"></iconify-icon>
                    Tugas & Studi Kasus (<?= count($assignments) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'submissions' ? 'active' : '' ?> fw-semibold" href="<?= site_url('dashboard/pembelajaran/assessment?tab=submissions') ?>">
                    <iconify-icon icon="solar:diploma-outline" class="me-1"></iconify-icon>
                    Submisi Peserta (<?= count($submissions) ?>)
                </a>
            </li>
        </ul>

        <!-- Filter Dropdown -->
        <form action="<?= site_url('dashboard/pembelajaran/assessment') ?>" method="get" class="d-flex align-items-center gap-2">
            <input type="hidden" name="tab" value="<?= esc($currentTab) ?>">
            <select name="class_id" class="form-select form-select-sm radius-8" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                <?php foreach ($classes as $cls): ?>
                    <option value="<?= esc($cls['id']) ?>" <?= ((string) ($filters['class_id'] ?? '') === (string) $cls['id']) ? 'selected' : '' ?>>
                        <?= esc($cls['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="card-body p-0">
        <?php if ($currentTab === 'quizzes'): ?>
            <!-- Tab Quizzes -->
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th>Tipe</th>
                            <th>Judul Kuis</th>
                            <th>Kelas</th>
                            <th>Course</th>
                            <th class="text-center">Jumlah Soal</th>
                            <th class="text-center">Batas Percobaan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quizzes)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-secondary-light py-32">
                                    Belum ada data kuis yang dibuat. Klik tombol "+ Tambah Kuis" di atas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($quizzes as $q): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $typeBadge = match($q['type'] ?? '') {
                                            'pre_test'  => 'bg-info-50 text-info-600',
                                            'post_test' => 'bg-success-50 text-success-600',
                                            default     => 'bg-primary-50 text-primary-600',
                                        };
                                        $typeLabel = match($q['type'] ?? '') {
                                            'pre_test'  => 'Pre-Test',
                                            'post_test' => 'Post-Test',
                                            default     => 'Kuis Modul',
                                        };
                                        ?>
                                        <span class="badge <?= $typeBadge ?> px-10 py-4 radius-4 text-xs font-monospace">
                                            <?= esc($typeLabel) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <h6 class="text-sm fw-semibold mb-0 text-neutral-900"><?= esc($q['title']) ?></h6>
                                        <?php if (! empty($q['lesson_title'])): ?>
                                            <span class="text-xs text-secondary-light">Materi: <?= esc($q['lesson_title']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($q['class_name'] ?? 'Semua') ?></td>
                                    <td><?= esc($q['course_title'] ?? '—') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-neutral-100 text-neutral-800 px-8 py-4 radius-4 text-xs">
                                            <?= number_format($q['question_count'] ?? 0) ?> Soal
                                        </span>
                                    </td>
                                    <td class="text-center text-sm">
                                        <?= esc((string) ($q['attempt_limit'] ?? 1)) ?>x
                                    </td>
                                    <td class="text-center">
                                        <?= view('dashboard/components/status_badge', ['status' => $q['status']]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($currentTab === 'assignments'): ?>
            <!-- Tab Assignments -->
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th>Judul Tugas</th>
                            <th>Kelas</th>
                            <th>Batas Waktu (Deadline)</th>
                            <th class="text-center">Sifat</th>
                            <th class="text-center">Submisi Masuk</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($assignments)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary-light py-32">
                                    Belum ada penugasan yang dibuat. Klik tombol "+ Tambah Tugas" di atas.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($assignments as $a): ?>
                                <tr>
                                    <td>
                                        <h6 class="text-sm fw-semibold mb-0 text-neutral-900"><?= esc($a['title']) ?></h6>
                                        <?php if (! empty($a['instructions'])): ?>
                                            <span class="text-xs text-secondary-light line-clamp-1"><?= esc($a['instructions']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($a['class_name'] ?? '-') ?></td>
                                    <td>
                                        <?= ! empty($a['due_at']) ? date('d M Y, H:i', strtotime($a['due_at'])) : '<span class="text-secondary-light text-xs">Tanpa batas</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <?= ! empty($a['is_required']) ? '<span class="badge bg-warning-50 text-warning-600 px-8 py-4 radius-4 text-xs">Wajib</span>' : '<span class="text-xs text-secondary-light">Opsional</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-50 text-info-700 px-10 py-4 radius-4 text-xs fw-semibold">
                                            <?= number_format($a['submission_count'] ?? 0) ?> Submisi
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?= view('dashboard/components/status_badge', ['status' => $a['status']]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <!-- Tab Submissions -->
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th>Peserta</th>
                            <th>Tugas</th>
                            <th>Kelas</th>
                            <th class="text-center">Nilai</th>
                            <th>Dinilai Oleh</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($submissions)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-secondary-light py-32">
                                    Belum ada submisi tugas yang dikumpulkan peserta.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($submissions as $sub): ?>
                                <tr>
                                    <td>
                                        <h6 class="text-sm fw-semibold mb-0"><?= esc($sub['student_name']) ?></h6>
                                        <span class="text-xs text-secondary-light">@<?= esc($sub['username']) ?></span>
                                    </td>
                                    <td><?= esc($sub['assignment_title']) ?></td>
                                    <td><?= esc($sub['class_name']) ?></td>
                                    <td class="text-center">
                                        <?php if ($sub['score'] !== null): ?>
                                            <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs fw-bold">
                                                <?= number_format((float) $sub['score'], 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-xs text-secondary-light">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($sub['evaluator_name'] ?? '—') ?></td>
                                    <td class="text-center">
                                        <?= view('dashboard/components/status_badge', ['status' => $sub['status']]) ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('dashboard/pembelajaran/penilaian/' . $sub['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-4 px-12 py-6">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (isset($pager)): ?>
            <div class="card-footer border-top bg-transparent pt-16 pb-0 px-24">
                <?= $pager->links('assessments', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Quiz Modal -->
<div class="modal fade" id="addQuizModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg">Tambah Kuis Pembelajaran</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('dashboard/pembelajaran/assessment/quiz') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="quiz_class_id">Kelas Pembelajaran <span class="text-danger-main">*</span></label>
                        <select id="quiz_class_id" name="class_id" class="form-select radius-8" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($classes as $cls): ?>
                                <option value="<?= esc($cls['id']) ?>"><?= esc($cls['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="quiz_title">Judul Kuis <span class="text-danger-main">*</span></label>
                        <input id="quiz_title" type="text" name="title" class="form-control radius-8" required placeholder="Contoh: Kuis Modul 1: Pemahaman Dasar">
                    </div>
                    <div class="row gy-16 mb-16">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="quiz_type">Tipe Kuis</label>
                            <select id="quiz_type" name="type" class="form-select radius-8">
                                <option value="lesson_quiz">Kuis Modul / Lesson</option>
                                <option value="pre_test">Pre-Test</option>
                                <option value="post_test">Post-Test</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-sm mb-6" for="quiz_attempts">Batas Percobaan</label>
                            <input id="quiz_attempts" type="number" min="1" name="attempt_limit" class="form-control radius-8" value="1">
                        </div>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="quiz_instructions">Petunjuk Pengerjaan</label>
                        <textarea id="quiz_instructions" name="instructions" class="form-control radius-8" rows="3" placeholder="Pilihlah salah satu jawaban yang paling tepat..."></textarea>
                    </div>
                    <div class="form-check">
                        <input id="quiz_req" type="checkbox" name="is_required" value="1" class="form-check-input" checked>
                        <label class="form-check-label text-sm fw-medium" for="quiz_req">Kuis wajib diselesaikan untuk kelulusan</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Kuis</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Assignment Modal -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-16">
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title fw-semibold text-lg">Tambah Tugas / Proyek</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('dashboard/pembelajaran/assessment/assignment') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-24">
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="asg_class_id">Kelas Pembelajaran <span class="text-danger-main">*</span></label>
                        <select id="asg_class_id" name="class_id" class="form-select radius-8" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($classes as $cls): ?>
                                <option value="<?= esc($cls['id']) ?>"><?= esc($cls['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="asg_title">Judul Tugas <span class="text-danger-main">*</span></label>
                        <input id="asg_title" type="text" name="title" class="form-control radius-8" required placeholder="Contoh: Studi Kasus Penyusunan HPS">
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="asg_due">Batas Waktu Pengumpulan (Deadline)</label>
                        <input id="asg_due" type="datetime-local" name="due_at" class="form-control radius-8">
                    </div>
                    <div class="mb-16">
                        <label class="form-label fw-semibold text-sm mb-6" for="asg_instructions">Instruksi Pengerjaan</label>
                        <textarea id="asg_instructions" name="instructions" class="form-control radius-8" rows="3" placeholder="Jelaskan format file, panduan jawaban, atau kriteria penilaian..."></textarea>
                    </div>
                    <div class="form-check">
                        <input id="asg_req" type="checkbox" name="is_required" value="1" class="form-check-input" checked>
                        <label class="form-check-label text-sm fw-medium" for="asg_req">Tugas wajib diselesaikan peserta</label>
                    </div>
                </div>
                <div class="modal-footer border-top py-16 px-24">
                    <button type="button" class="btn btn-outline-neutral-300 radius-8 px-20 py-11" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

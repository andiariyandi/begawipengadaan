<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$class = $class ?? [];
$isEdit = ($formMode ?? 'create') === 'edit';
$formAction = $isEdit ? route_to('dashboard.learning.classes.update', $class['id']) : route_to('dashboard.learning.classes.store');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kelas') ?></h6>
        <p class="text-secondary-light mb-0"><?= $isEdit ? 'Perbarui informasi kelas pembelajaran.' : 'Buka batch pelaksanaan kelas pembelajaran baru.' ?></p>
    </div>
    <a href="<?= route_to('dashboard.learning.classes') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-linear" class="text-xl"></iconify-icon>
        <span>Kembali</span>
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= esc($formAction) ?>">
            <?= csrf_field() ?>

            <div class="row gy-20">
                <div class="col-12">
                    <h6 class="text-md fw-semibold mb-0 text-primary-600 border-bottom pb-8">Informasi Dasar Kelas</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="course_id">Course Induk <span class="text-danger-main">*</span></label>
                    <select id="course_id" name="course_id" class="form-select radius-8" required>
                        <option value="">Pilih Course</option>
                        <?php $courseId = old('course_id', $class['course_id'] ?? ''); foreach ($courses as $course): ?>
                            <option value="<?= esc($course['id']) ?>" <?= (string) $courseId === (string) $course['id'] ? 'selected' : '' ?>>
                                <?= esc($course['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="code">Kode Kelas <span class="text-danger-main">*</span></label>
                    <input id="code" type="text" name="code" class="form-control radius-8" value="<?= esc(old('code', $class['code'] ?? 'CLS-' . strtoupper(substr(uniqid(), -6)))) ?>" required placeholder="Contoh: CLS-PBJ-01">
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="name">Nama Kelas / Batch <span class="text-danger-main">*</span></label>
                    <input id="name" type="text" name="name" class="form-control radius-8" value="<?= esc(old('name', $class['name'] ?? '')) ?>" required placeholder="Contoh: Batch I - Pelatihan Pengadaan Barang dan Jasa">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="capacity">Kapasitas Maksimal</label>
                    <input id="capacity" type="number" min="0" name="capacity" class="form-control radius-8" value="<?= esc(old('capacity', $class['capacity'] ?? 40)) ?>" placeholder="Kosongkan jika tak terbatas">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="status">Status Kelas</label>
                    <select id="status" name="status" class="form-select radius-8">
                        <?php $status = old('status', $class['status'] ?? 'draft'); ?>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="open" <?= in_array($status, ['open', 'scheduled'], true) ? 'selected' : '' ?>>Pendaftaran Dibuka (Open)</option>
                        <option value="active" <?= in_array($status, ['active', 'ongoing'], true) ? 'selected' : '' ?>>Sedang Berjalan (Active)</option>
                        <option value="completed" <?= in_array($status, ['completed', 'closed'], true) ? 'selected' : '' ?>>Selesai (Completed)</option>
                    </select>
                </div>

                <div class="col-12 mt-12">
                    <h6 class="text-md fw-semibold mb-0 text-primary-600 border-bottom pb-8">Periode & Jadwal Pelaksanaan</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="registration_open_at">Pendaftaran Dibuka</label>
                    <input id="registration_open_at" type="datetime-local" name="registration_open_at" class="form-control radius-8" value="<?= esc(old('registration_open_at', ! empty($class['registration_open_at']) ? date('Y-m-d\TH:i', strtotime($class['registration_open_at'])) : '')) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="registration_close_at">Pendaftaran Ditutup</label>
                    <input id="registration_close_at" type="datetime-local" name="registration_close_at" class="form-control radius-8" value="<?= esc(old('registration_close_at', ! empty($class['registration_close_at']) ? date('Y-m-d\TH:i', strtotime($class['registration_close_at'])) : '')) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="start_at">Kelas Dimulai</label>
                    <input id="start_at" type="datetime-local" name="start_at" class="form-control radius-8" value="<?= esc(old('start_at', ! empty($class['start_at']) ? date('Y-m-d\TH:i', strtotime($class['start_at'])) : '')) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="end_at">Kelas Selesai</label>
                    <input id="end_at" type="datetime-local" name="end_at" class="form-control radius-8" value="<?= esc(old('end_at', ! empty($class['end_at']) ? date('Y-m-d\TH:i', strtotime($class['end_at'])) : '')) ?>">
                </div>

                <div class="col-12 mt-12">
                    <h6 class="text-md fw-semibold mb-0 text-primary-600 border-bottom pb-8">Standar Kelulusan & Bobot Penilaian</h6>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="passing_grade">Nilai Minimum Kelulusan</label>
                    <input id="passing_grade" type="number" step="0.01" min="0" max="100" name="passing_grade" class="form-control radius-8" value="<?= esc(old('passing_grade', $class['passing_grade'] ?? 70)) ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="quiz_weight">Bobot Kuis (%)</label>
                    <input id="quiz_weight" type="number" step="0.01" min="0" max="100" name="quiz_weight" class="form-control radius-8" value="<?= esc(old('quiz_weight', $class['quiz_weight'] ?? 40)) ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="assignment_weight">Bobot Tugas (%)</label>
                    <input id="assignment_weight" type="number" step="0.01" min="0" max="100" name="assignment_weight" class="form-control radius-8" value="<?= esc(old('assignment_weight', $class['assignment_weight'] ?? 0)) ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="post_test_weight">Bobot Post-Test (%)</label>
                    <input id="post_test_weight" type="number" step="0.01" min="0" max="100" name="post_test_weight" class="form-control radius-8" value="<?= esc(old('post_test_weight', $class['post_test_weight'] ?? 60)) ?>">
                </div>

                <div class="col-12">
                    <div class="form-check form-switch d-flex align-items-center gap-12 p-0">
                        <input id="certificate_enabled" type="checkbox" name="certificate_enabled" value="1" class="form-check-input ms-0 mt-0" <?= old('certificate_enabled', $class['certificate_enabled'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium text-sm mb-0" for="certificate_enabled">
                            Aktifkan Penerbitan Sertifikat bagi Peserta yang Lulus
                        </label>
                    </div>
                </div>

                <div class="col-12 text-end pt-16 border-top">
                    <a href="<?= route_to('dashboard.learning.classes') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 me-8">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Kelas</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

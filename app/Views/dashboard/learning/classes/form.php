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
        <p class="text-secondary-light mb-0"><?= $isEdit ? 'Perbarui informasi dan jadwal pelaksanaan batch kelas.' : 'Buka batch pelaksanaan kelas pembelajaran baru.' ?></p>
    </div>
    <a href="<?= route_to('dashboard.learning.classes') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-linear" class="text-xl"></iconify-icon>
        <span>Kembali ke Daftar</span>
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= esc($formAction) ?>">
            <?= csrf_field() ?>

            <!-- 1. INFORMASI DASAR KELAS -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:chalkboard-teacher-outline" class="text-lg"></iconify-icon>
                    <span>1. Informasi Pokok Batch Kelas</span>
                </h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="course_id">Course Induk Kurikulum <span class="text-danger-main">*</span></label>
                        <select id="course_id" name="course_id" class="form-select radius-8" required>
                            <option value="">-- Pilih Course Induk --</option>
                            <?php $courseId = old('course_id', $class['course_id'] ?? ''); foreach ($courses as $c): ?>
                                <option value="<?= esc($c['id']) ?>" <?= (string) $courseId === (string) $c['id'] ? 'selected' : '' ?>>
                                    <?= esc($c['title']) ?> (Level: <?= esc(ucfirst($c['level'] ?? 'Dasar')) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="code">Kode Batch / Kelas <span class="text-danger-main">*</span></label>
                        <input id="code" type="text" name="code" class="form-control radius-8 font-monospace text-uppercase" value="<?= esc(old('code', $class['code'] ?? 'CLS-' . strtoupper(substr(uniqid(), -6)))) ?>" required placeholder="Contoh: CLS-PBJ-01">
                        <small class="text-muted text-xs">Digunakan sebagai identifier unik di Arena Belajar: <code>/arena-belajar/kelas/{code}</code></small>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="name">Nama Kelas / Batch <span class="text-danger-main">*</span></label>
                        <input id="name" type="text" name="name" class="form-control radius-8" value="<?= esc(old('name', $class['name'] ?? '')) ?>" required placeholder="Contoh: Batch I - Pelatihan Pengadaan Barang dan Jasa Pemerintah">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="capacity">Kapasitas Maksimal (Kursi)</label>
                        <input id="capacity" type="number" min="0" name="capacity" class="form-control radius-8" value="<?= esc(old('capacity', $class['capacity'] ?? 40)) ?>" placeholder="Contoh: 40">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="learning_method">Metode Pembelajaran</label>
                        <select id="learning_method" name="learning_method" class="form-select radius-8">
                            <?php $method = old('learning_method', $class['learning_method'] ?? 'online'); ?>
                            <option value="online" <?= $method === 'online' ? 'selected' : '' ?>>Online Penuh (LMS & Asinkron)</option>
                            <option value="hybrid" <?= $method === 'hybrid' ? 'selected' : '' ?>>Hybrid (LMS & Sesi Tatap Maya)</option>
                            <option value="offline" <?= $method === 'offline' ? 'selected' : '' ?>>Tatap Muka / Klasikal (Ruang Kelas)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="location_or_link">Lokasi / Tautan Sesi Tatap Maya</label>
                        <input id="location_or_link" type="text" name="location_or_link" class="form-control radius-8" value="<?= esc(old('location_or_link', $class['location_or_link'] ?? '')) ?>" placeholder="https://zoom.us/j/... atau Gedung Diklat BPSDM Lampung">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="status">Status Pelaksanaan</label>
                        <select id="status" name="status" class="form-select radius-8">
                            <?php $status = old('status', $class['status'] ?? 'scheduled'); ?>
                            <option value="scheduled" <?= in_array($status, ['scheduled', 'open'], true) ? 'selected' : '' ?>>Jadwal Terbuka (Pendaftaran Dibuka)</option>
                            <option value="active" <?= in_array($status, ['active', 'ongoing'], true) ? 'selected' : '' ?>>Sedang Berjalan (Aktif)</option>
                            <option value="completed" <?= in_array($status, ['completed', 'closed'], true) ? 'selected' : '' ?>>Selesai (Completed)</option>
                            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. DESKRIPSI & PERSYARATAN BATCH -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:document-text-outline" class="text-lg"></iconify-icon>
                    <span>2. Deskripsi Batch & Sasaran Khusus</span>
                </h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="description">Deskripsi & Catatan Batch</label>
                        <textarea id="description" name="description" class="form-control radius-8" rows="3" placeholder="Informasi spesifik mengenai batch ini, tata tertib, atau format kelas..."><?= esc(old('description', $class['description'] ?? '')) ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="target_audience">Sasaran Khusus Peserta Batch</label>
                        <textarea id="target_audience" name="target_audience" class="form-control radius-8" rows="3" placeholder="Contoh: Khusus Aparatur OPD Kabupaten/Kota se-Lampung, Pokja UKPBJ..."><?= esc(old('target_audience', $class['target_audience'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 3. PERIODE PENDAFTARAN & JADWAL KELAS -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:calendar-date-outline" class="text-lg"></iconify-icon>
                    <span>3. Periode Pendaftaran & Jadwal Pelaksanaan</span>
                </h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="registration_open_at">Pendaftaran Dibuka</label>
                        <input id="registration_open_at" type="datetime-local" name="registration_open_at" class="form-control radius-8" value="<?= esc(old('registration_open_at', ! empty($class['registration_open_at']) ? date('Y-m-d\TH:i', strtotime($class['registration_open_at'])) : '')) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="registration_close_at">Pendaftaran Ditutup</label>
                        <input id="registration_close_at" type="datetime-local" name="registration_close_at" class="form-control radius-8" value="<?= esc(old('registration_close_at', ! empty($class['registration_close_at']) ? date('Y-m-d\TH:i', strtotime($class['registration_close_at'])) : '')) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="start_at">Kelas Dimulai (Sesi Aktif)</label>
                        <input id="start_at" type="datetime-local" name="start_at" class="form-control radius-8" value="<?= esc(old('start_at', ! empty($class['start_at']) ? date('Y-m-d\TH:i', strtotime($class['start_at'])) : '')) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="end_at">Kelas Selesai (Batas Akhir)</label>
                        <input id="end_at" type="datetime-local" name="end_at" class="form-control radius-8" value="<?= esc(old('end_at', ! empty($class['end_at']) ? date('Y-m-d\TH:i', strtotime($class['end_at'])) : '')) ?>">
                    </div>
                </div>
            </div>

            <!-- 4. STANDAR KELULUSAN & BOBOT PENILAIAN -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:diploma-outline" class="text-lg"></iconify-icon>
                    <span>4. Standar Kelulusan & Bobot Komponen Penilaian</span>
                </h6>
                <div class="row gy-20">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="passing_grade">Nilai Minimum Kelulusan (Passing Grade)</label>
                        <input id="passing_grade" type="number" step="0.01" min="0" max="100" name="passing_grade" class="form-control radius-8" value="<?= esc(old('passing_grade', $class['passing_grade'] ?? 70)) ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="quiz_weight">Bobot Kuis (%)</label>
                        <input id="quiz_weight" type="number" step="0.01" min="0" max="100" name="quiz_weight" class="form-control radius-8" value="<?= esc(old('quiz_weight', $class['quiz_weight'] ?? 30)) ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="assignment_weight">Bobot Tugas Praktik (%)</label>
                        <input id="assignment_weight" type="number" step="0.01" min="0" max="100" name="assignment_weight" class="form-control radius-8" value="<?= esc(old('assignment_weight', $class['assignment_weight'] ?? 30)) ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="post_test_weight">Bobot Post-Test (%)</label>
                        <input id="post_test_weight" type="number" step="0.01" min="0" max="100" name="post_test_weight" class="form-control radius-8" value="<?= esc(old('post_test_weight', $class['post_test_weight'] ?? 40)) ?>">
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch d-flex align-items-center gap-12 p-0 mt-8">
                            <input id="certificate_enabled" type="checkbox" name="certificate_enabled" value="1" class="form-check-input ms-0 mt-0" <?= old('certificate_enabled', $class['certificate_enabled'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm mb-0" for="certificate_enabled">
                                Terbitkan Sertifikat Elektronik Resmi Ber-QR Code bagi Peserta yang Memenuhi Passing Grade
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-12 pt-16 border-top">
                <a href="<?= route_to('dashboard.learning.classes') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:diskette-bold" class="text-lg"></iconify-icon>
                    <span><?= $isEdit ? 'Perbarui Batch Kelas' : 'Buka Batch Kelas Baru' ?></span>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

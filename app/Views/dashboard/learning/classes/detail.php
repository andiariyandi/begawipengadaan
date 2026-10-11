<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$classId          = (int) ($class['id'] ?? 0);
$trainers         = $trainers ?? [];
$trainerCandidates = $trainerCandidates ?? [];
$db               = db_connect();

$enrolledCount = (int) $db->table('enrollments')
    ->where('class_id', $classId)
    ->whereIn('status', ['active', 'completed', 'passed'])
    ->countAllResults();

$capacity = (int) ($class['capacity'] ?? 0);
$remainingQuota = $capacity > 0 ? max(0, $capacity - $enrolledCount) : null;

$methodBadge = match ($class['learning_method'] ?? 'online') {
    'hybrid'  => ['label' => 'Hybrid (LMS & Tatap Maya)', 'badge' => 'bg-warning-50 text-warning-600 border border-warning-200'],
    'offline' => ['label' => 'Tatap Muka / Klasikal', 'badge' => 'bg-info-50 text-info-600 border border-info-200'],
    default   => ['label' => 'Online Penuh (LMS)', 'badge' => 'bg-success-50 text-success-600 border border-success-200'],
};
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($class['name'] ?? 'Detail Kelas') ?></h6>
        <p class="text-secondary-light mb-0">Kode Batch: <strong class="text-primary-600 font-monospace"><?= esc($class['code'] ?? '-') ?></strong> &bull; ID: #<?= esc($classId) ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= site_url('arena-belajar/kelas/' . ($class['code'] ?? '')) ?>" target="_blank" class="btn btn-outline-primary-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:link-circle-outline" class="text-lg"></iconify-icon>
            <span>Buka di Landing Page</span>
        </a>
        <a href="<?= site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId) ?>" class="btn btn-primary-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:list-check-outline" class="text-lg"></iconify-icon>
            <span>Kelola Kurikulum</span>
        </a>
        <a href="<?= route_to('dashboard.learning.enrollments') ?>?class_id=<?= $classId ?>" class="btn btn-outline-info radius-8 px-16 py-9 d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-lg"></iconify-icon>
            <span>Daftar Peserta</span>
        </a>
        <a href="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat?class_id=' . $classId) ?>" class="btn btn-outline-success radius-8 px-16 py-9 d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:diploma-verified-outline" class="text-lg"></iconify-icon>
            <span>Kelayakan Sertifikat</span>
        </a>
        <a href="<?= route_to('dashboard.learning.classes.edit', $classId) ?>" class="btn btn-outline-warning-main radius-8 px-16 py-9 d-inline-flex align-items-center gap-1">
            <iconify-icon icon="solar:pen-new-square-outline" class="text-lg"></iconify-icon>
            <span>Ubah</span>
        </a>
        <a href="<?= route_to('dashboard.learning.classes') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-9">Kembali</a>
    </div>
</div>

<!-- KPI Summary Strip -->
<div class="row row-cols-xxxl-4 row-cols-lg-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Status Batch</p>
                <div class="mt-1">
                    <?= view('dashboard/components/status_badge', ['status' => $class['status'] ?? 'draft']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Peserta Terdaftar</p>
                <h5 class="mb-0 text-primary-600"><?= $enrolledCount ?> <span class="text-sm font-normal text-secondary-light">/ <?= $capacity ?: '&infin;' ?> Kursi</span></h5>
                <?php if ($remainingQuota !== null): ?>
                    <small class="text-success text-xs font-semibold">Sisa <?= $remainingQuota ?> kursi</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Metode Belajar</p>
                <div class="mt-1">
                    <span class="badge <?= $methodBadge['badge'] ?> px-10 py-4 radius-12 text-xs"><?= esc($methodBadge['label']) ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Passing Grade Kelulusan</p>
                <h5 class="mb-0 text-success-main"><?= (int) ($class['passing_grade'] ?? 70) ?> / 100</h5>
                <small class="text-secondary-light text-xs"><?= ! empty($class['certificate_enabled']) ? 'Sertifikat Aktif' : 'Tanpa Sertifikat' ?></small>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4 mb-24">
    <!-- Left Column: Details -->
    <div class="col-xl-8">
        <div class="card mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-lg fw-semibold mb-0">Rincian Informasi Batch Pelaksanaan</h6>
            </div>
            <div class="card-body p-24">
                <div class="row gy-20">
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Course Induk</p>
                        <h6 class="mb-0 text-primary-600"><?= esc($class['course_title'] ?? '-') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Kode Batch</p>
                        <h6 class="mb-0 font-monospace"><?= esc($class['code']) ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Periode Pendaftaran</p>
                        <p class="mb-0 text-sm fw-semibold">
                            <?= ! empty($class['registration_open_at']) ? date('d M Y H:i', strtotime($class['registration_open_at'])) : '-' ?>
                            s/d
                            <?= ! empty($class['registration_close_at']) ? date('d M Y H:i', strtotime($class['registration_close_at'])) : '-' ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-sm mb-4">Jadwal Kelas Berjalan</p>
                        <p class="mb-0 text-sm fw-semibold">
                            <?= ! empty($class['start_at']) ? date('d M Y H:i', strtotime($class['start_at'])) : '-' ?>
                            s/d
                            <?= ! empty($class['end_at']) ? date('d M Y H:i', strtotime($class['end_at'])) : '-' ?>
                        </p>
                    </div>
                    <?php if (! empty($class['location_or_link'])): ?>
                        <div class="col-12">
                            <p class="text-secondary-light text-sm mb-4">Lokasi / Tautan Tatap Maya</p>
                            <p class="mb-0 text-sm fw-medium">
                                <?php if (str_starts_with($class['location_or_link'], 'http')): ?>
                                    <a href="<?= esc($class['location_or_link']) ?>" target="_blank" class="text-primary-600 text-decoration-underline">
                                        <iconify-icon icon="solar:link-bold" class="me-1"></iconify-icon><?= esc($class['location_or_link']) ?>
                                    </a>
                                <?php else: ?>
                                    <iconify-icon icon="solar:map-point-bold" class="text-danger-main me-1"></iconify-icon><?= esc($class['location_or_link']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($class['description'])): ?>
                        <div class="col-12">
                            <p class="text-secondary-light text-sm mb-4">Deskripsi / Catatan Batch</p>
                            <div class="p-16 radius-8 bg-neutral-50 text-secondary-light text-sm">
                                <?= nl2br(esc($class['description'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($class['target_audience'])): ?>
                        <div class="col-12">
                            <p class="text-secondary-light text-sm mb-4">Sasaran Khusus Peserta</p>
                            <div class="p-16 radius-8 bg-neutral-50 text-secondary-light text-sm">
                                <?= nl2br(esc($class['target_audience'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bobot Komponen Penilaian Strip -->
                <div class="pt-20 mt-20 border-top">
                    <h6 class="text-sm fw-semibold text-primary-600 mb-12">Komposisi Bobot Penilaian</h6>
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="p-12 radius-8 bg-neutral-50 border">
                                <small class="text-secondary-light d-block text-xs">Bobot Kuis</small>
                                <strong class="text-primary-600 fs-6"><?= (float) ($class['quiz_weight'] ?? 30) ?>%</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-12 radius-8 bg-neutral-50 border">
                                <small class="text-secondary-light d-block text-xs">Bobot Tugas</small>
                                <strong class="text-warning-main fs-6"><?= (float) ($class['assignment_weight'] ?? 30) ?>%</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-12 radius-8 bg-neutral-50 border">
                                <small class="text-secondary-light d-block text-xs">Bobot Post-Test</small>
                                <strong class="text-success-main fs-6"><?= (float) ($class['post_test_weight'] ?? 40) ?>%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengajar (Trainer) Section -->
        <div class="card">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-lg fw-semibold mb-0">Pengajar & Fasilitator Batch (<?= count($trainers) ?>)</h6>
                    <small class="text-secondary-light">Trainer mengelola modul, memfasilitasi diskusi, dan mengevaluasi tugas peserta.</small>
                </div>
            </div>
            <div class="card-body p-24">
                <form method="post" action="<?= route_to('dashboard.learning.classes.assign-trainer', $classId) ?>" class="row gy-12 align-items-end mb-24 pb-20 border-bottom">
                    <?= csrf_field() ?>
                    <div class="col-md-5">
                        <label for="trainer_user_id" class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Calon Trainer</label>
                        <select id="trainer_user_id" name="trainer_user_id" class="form-select radius-8" required>
                            <option value="">-- Pilih trainer aktif --</option>
                            <?php foreach ($trainerCandidates as $c): ?>
                                <option value="<?= esc($c['id']) ?>"><?= esc($c['name']) ?> (<?= esc($c['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="role" class="form-label fw-semibold text-primary-light text-sm mb-8">Peran Penugasan</label>
                        <select id="role" name="role" class="form-select radius-8">
                            <option value="primary">Instruktur Utama</option>
                            <option value="co_trainer">Asisten / Co-Trainer</option>
                            <option value="guest">Narasumber Tamu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary-600 radius-8 w-100 py-11 d-inline-flex align-items-center justify-content-center gap-1">
                            <iconify-icon icon="solar:user-plus-outline" class="text-lg"></iconify-icon>
                            <span>Tugaskan</span>
                        </button>
                    </div>
                </form>

                <?php if (empty($trainers)): ?>
                    <?= view('dashboard/components/empty_state', [
                        'title'   => 'Belum Ada Trainer Ditugaskan',
                        'message' => 'Silakan pilih trainer pada formulir di atas untuk mengampu kelas pembelajaran ini.',
                        'icon'    => 'solar:user-speak-outline',
                    ]) ?>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table bordered-table sm-table mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Instruktur</th>
                                    <th>Peran</th>
                                    <th>Status</th>
                                    <th>Waktu Penugasan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($trainers as $t): ?>
                                    <?php
                                    $roleBadge = match ($t['role'] ?? 'primary') {
                                        'co_trainer' => ['label' => 'Co-Trainer', 'badge' => 'bg-info-50 text-info-600 border border-info-200'],
                                        'guest'      => ['label' => 'Narasumber Tamu', 'badge' => 'bg-neutral-100 text-neutral-800 border'],
                                        default      => ['label' => 'Instruktur Utama', 'badge' => 'bg-primary-50 text-primary-600 border border-primary-200'],
                                    };
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark"><?= esc($t['name'] ?? '-') ?></span>
                                            <small class="d-block text-secondary-light"><?= esc($t['email'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge <?= $roleBadge['badge'] ?> px-8 py-2 radius-12 text-xs"><?= esc($roleBadge['label']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?= ($t['status'] ?? '') === 'active' ? 'bg-success-50 text-success-600' : 'bg-neutral-100 text-neutral-600' ?> px-8 py-2 radius-12 text-xs">
                                                <?= esc(ucfirst($t['status'] ?? '-')) ?>
                                            </span>
                                        </td>
                                        <td class="text-sm text-secondary-light"><?= ! empty($t['assigned_at']) ? date('d M Y H:i', strtotime($t['assigned_at'])) : '-' ?></td>
                                        <td class="text-end">
                                            <?php if (($t['status'] ?? '') === 'active'): ?>
                                                <button type="button" class="btn btn-outline-danger-600 btn-sm radius-8 px-12 py-6 d-inline-flex align-items-center gap-1"
                                                    data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                                    data-action="<?= route_to('dashboard.learning.classes.revoke-trainer', $classId, $t['user_id']) ?>"
                                                    data-method="POST"
                                                    data-title="Cabut Penugasan Trainer"
                                                    data-message="Apakah Anda yakin ingin mencabut penugasan trainer '<?= esc($t['name'] ?? '') ?>' dari batch kelas ini?"
                                                    data-btn-text="Cabut Penugasan"
                                                    data-btn-class="btn-danger-600"
                                                    data-icon="solar:user-cross-linear"
                                                    data-icon-color="danger">
                                                    <iconify-icon icon="solar:user-cross-linear"></iconify-icon>
                                                    <span>Cabut</span>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-xs text-muted">Dicabut</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Quick Shortcuts -->
    <div class="col-xl-4">
        <!-- Quick Links Card -->
        <div class="card mb-24">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-md fw-semibold mb-0">Menu Akselerasi Batch</h6>
            </div>
            <div class="card-body p-24 d-flex flex-column gap-12">
                <a href="<?= site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId) ?>" class="btn btn-outline-primary-600 radius-8 p-12 text-start d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:list-check-outline" class="text-xl text-primary-600"></iconify-icon>
                        <div>
                            <strong class="d-block text-sm text-dark">Modul, Silabus & Materi</strong>
                            <small class="text-secondary-light text-xs">Susun alur pelajaran, kuis, dan tugas</small>
                        </div>
                    </div>
                    <iconify-icon icon="solar:alt-arrow-right-linear" class="text-lg text-primary-600"></iconify-icon>
                </a>
                <a href="<?= route_to('dashboard.learning.enrollments') ?>?class_id=<?= $classId ?>" class="btn btn-outline-info radius-8 p-12 text-start d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:users-group-two-rounded-outline" class="text-xl text-info"></iconify-icon>
                        <div>
                            <strong class="d-block text-sm text-dark">Daftar & Status Peserta</strong>
                            <small class="text-secondary-light text-xs">Persetujuan pendaftaran & monitoring</small>
                        </div>
                    </div>
                    <iconify-icon icon="solar:alt-arrow-right-linear" class="text-lg text-info"></iconify-icon>
                </a>
                <a href="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat?class_id=' . $classId) ?>" class="btn btn-outline-success radius-8 p-12 text-start d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-xl text-success"></iconify-icon>
                        <div>
                            <strong class="d-block text-sm text-dark">Penerbitan Sertifikat</strong>
                            <small class="text-secondary-light text-xs">Verifikasi kelulusan & generate QR</small>
                        </div>
                    </div>
                    <iconify-icon icon="solar:alt-arrow-right-linear" class="text-lg text-success"></iconify-icon>
                </a>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card">
            <div class="card-header border-bottom bg-base py-16 px-24">
                <h6 class="text-md fw-semibold mb-0">Panduan Siklus Kelas</h6>
            </div>
            <div class="card-body p-24">
                <ol class="d-flex flex-column gap-12 ps-16 mb-0 text-sm text-secondary-light">
                    <li>Atur status ke <strong>Jadwal Terbuka</strong> untuk membuka pendaftaran publik di Arena Belajar.</li>
                    <li>Tugaskan <strong>Trainer</strong> agar silabus dan penugasan dapat dipandu secara terstruktur.</li>
                    <li>Ubah status ke <strong>Sedang Berjalan</strong> ketika sesi pembelajaran telah dimulai.</li>
                    <li>Peserta yang melampaui passing grade berhak diterbitkan sertifikat resmi.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?= view('dashboard/components/confirmation_modal') ?>
<?= $this->endSection() ?>
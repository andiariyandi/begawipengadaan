<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$classId          = (int) ($class['id'] ?? 0);
$trainers         = $trainers ?? [];
$trainerCandidates = $trainerCandidates ?? [];
$modules          = $modules ?? [];
$enrollments      = $enrollments ?? [];
$db               = db_connect();

$enrolledCount = count($enrollments);
$capacity = (int) ($class['capacity'] ?? 0);
$remainingQuota = $capacity > 0 ? max(0, $capacity - $enrolledCount) : null;

$passedCount = count(array_filter($enrollments, static fn($e) => in_array($e['status'], ['completed', 'passed'], true) || ((float)($e['final_score'] ?? 0) >= (int)($class['passing_grade'] ?? 70) && $e['status'] === 'active')));
$passRate = $enrolledCount > 0 ? round(($passedCount / $enrolledCount) * 100) : 0;

$methodBadge = match ($class['learning_method'] ?? 'online') {
    'hybrid'  => ['label' => 'Hybrid (LMS & Tatap Maya)', 'badge' => 'bg-warning-50 text-warning-600 border border-warning-200'],
    'offline' => ['label' => 'Tatap Muka / Klasikal', 'badge' => 'bg-info-50 text-info-600 border border-info-200'],
    default   => ['label' => 'Online Penuh (LMS)', 'badge' => 'bg-success-50 text-success-600 border border-success-200'],
};
?>

<!-- Reusable Learning Navigation -->
<?= view('dashboard/learning/_nav_learning', ['activeSub' => 'classes']) ?>

<!-- Header Toolbar -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($class['name'] ?? 'Detail Kelas') ?></h6>
        <p class="text-secondary-light mb-0">Kode Batch: <strong class="text-primary-600 font-monospace"><?= esc($class['code'] ?? '-') ?></strong> &bull; ID: #<?= esc($classId) ?> &bull; Course: <strong class="text-primary-light"><?= esc($class['course_title'] ?? '-') ?></strong></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-primary-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-1 text-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#duplicateBatchModal">
            <iconify-icon icon="solar:copy-outline" class="text-lg"></iconify-icon>
            <span>Duplikasi Angkatan</span>
        </button>
        <a href="<?= site_url('dashboard/pembelajaran/kelas/' . $classId . '/gradebook/export') ?>" class="btn btn-outline-success radius-8 px-16 py-9 d-inline-flex align-items-center gap-1 text-sm fw-semibold" title="Unduh spreadsheet nilai peserta UTF-8 BOM">
            <iconify-icon icon="solar:file-download-outline" class="text-lg"></iconify-icon>
            <span>Unduh Gradebook (CSV)</span>
        </a>
        <a href="<?= site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId) ?>" class="btn btn-primary-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-1 text-sm fw-semibold">
            <iconify-icon icon="solar:list-check-outline" class="text-lg"></iconify-icon>
            <span>Kelola Kurikulum</span>
        </a>
        <a href="<?= route_to('dashboard.learning.classes.edit', $classId) ?>" class="btn btn-outline-warning-600 radius-8 px-16 py-9 d-inline-flex align-items-center gap-1 text-sm fw-semibold">
            <iconify-icon icon="solar:pen-new-square-outline" class="text-lg"></iconify-icon>
            <span>Ubah</span>
        </a>
        <a href="<?= route_to('dashboard.learning.classes') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-9 text-sm">Kembali</a>
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
                <p class="fw-medium text-primary-light mb-1">Kelulusan Peserta</p>
                <h5 class="mb-0 text-success-main"><?= $passedCount ?> Peserta <span class="text-sm font-normal text-secondary-light">(<?= $passRate ?>%)</span></h5>
                <small class="text-secondary-light text-xs">Passing grade: <?= (int) ($class['passing_grade'] ?? 70) ?> / 100</small>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-4 h-100">
            <div class="card-body p-20">
                <p class="fw-medium text-primary-light mb-1">Metode Belajar &amp; JP</p>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge <?= $methodBadge['badge'] ?> px-10 py-4 radius-12 text-xs"><?= esc($methodBadge['label']) ?></span>
                </div>
                <small class="text-primary-600 text-xs mt-1 d-block fw-semibold"><?= format_learning_jp($class['custom_duration_minutes'] ?? null, true) ?></small>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- UNIFIED CLASS CONSOLE: 5 OPERATIONAL TABS                      -->
<!-- ============================================================== -->
<div class="card border radius-12 bg-base mb-24">
    <div class="card-header border-bottom bg-base p-16">
        <ul class="nav nav-pills gap-2 flex-wrap" id="classConsoleTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active radius-8 px-16 py-10 text-xs fw-semibold d-flex align-items-center gap-2" id="tab-jadwal" data-bs-toggle="pill" data-bs-target="#content-jadwal" type="button" role="tab">
                    <iconify-icon icon="solar:calendar-outline" class="text-base"></iconify-icon>
                    <span>1. Jadwal &amp; Kuota</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link radius-8 px-16 py-10 text-xs fw-semibold d-flex align-items-center gap-2" id="tab-kurikulum" data-bs-toggle="pill" data-bs-target="#content-kurikulum" type="button" role="tab">
                    <iconify-icon icon="solar:folder-with-files-outline" class="text-base"></iconify-icon>
                    <span>2. Kurikulum &amp; Modul (<?= count($modules) ?>)</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link radius-8 px-16 py-10 text-xs fw-semibold d-flex align-items-center gap-2" id="tab-pengajar" data-bs-toggle="pill" data-bs-target="#content-pengajar" type="button" role="tab">
                    <iconify-icon icon="solar:user-speak-rounded-outline" class="text-base"></iconify-icon>
                    <span>3. Pengajar &amp; Fasilitator (<?= count($trainers) ?>)</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link radius-8 px-16 py-10 text-xs fw-semibold d-flex align-items-center gap-2" id="tab-peserta" data-bs-toggle="pill" data-bs-target="#content-peserta" type="button" role="tab">
                    <iconify-icon icon="solar:users-group-rounded-outline" class="text-base"></iconify-icon>
                    <span>4. Peserta Terdaftar (<?= $enrolledCount ?>)</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link radius-8 px-16 py-10 text-xs fw-semibold d-flex align-items-center gap-2" id="tab-nilai" data-bs-toggle="pill" data-bs-target="#content-nilai" type="button" role="tab">
                    <iconify-icon icon="solar:diploma-verified-outline" class="text-base"></iconify-icon>
                    <span>5. Buku Nilai &amp; Kelulusan</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-24">
        <div class="tab-content" id="classConsoleTabContent">
            <!-- ---------------------------------------------------- -->
            <!-- TAB 1: JADWAL & KUOTA ANGKATAN                       -->
            <!-- ---------------------------------------------------- -->
            <div class="tab-pane fade show active" id="content-jadwal" role="tabpanel">
                <div class="row gy-20 mb-24">
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-1">Course Induk</p>
                        <h6 class="mb-0 text-primary-600"><?= esc($class['course_title'] ?? '-') ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-1">Kode Batch</p>
                        <h6 class="mb-0 font-monospace"><?= esc($class['code']) ?></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-1">Periode Pendaftaran</p>
                        <p class="mb-0 text-sm fw-semibold">
                            <?= ! empty($class['registration_open_at']) ? date('d M Y H:i', strtotime($class['registration_open_at'])) : '-' ?>
                            s/d
                            <?= ! empty($class['registration_close_at']) ? date('d M Y H:i', strtotime($class['registration_close_at'])) : '-' ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-secondary-light text-xs mb-1">Jadwal Kelas Berjalan</p>
                        <p class="mb-0 text-sm fw-semibold">
                            <?= ! empty($class['start_at']) ? date('d M Y H:i', strtotime($class['start_at'])) : '-' ?>
                            s/d
                            <?= ! empty($class['end_at']) ? date('d M Y H:i', strtotime($class['end_at'])) : '-' ?>
                        </p>
                    </div>
                    <?php if (! empty($class['location_or_link'])): ?>
                        <div class="col-12">
                            <p class="text-secondary-light text-xs mb-1">Lokasi / Tautan Tatap Maya</p>
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
                            <p class="text-secondary-light text-xs mb-1">Deskripsi / Catatan Batch</p>
                            <div class="p-16 radius-8 bg-neutral-50 text-secondary-light text-sm">
                                <?= nl2br(esc($class['description'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bobot Komposisi Penilaian -->
                <div class="p-20 radius-12 border bg-neutral-50 mb-24">
                    <h6 class="text-sm fw-semibold text-primary-600 mb-12">Komposisi Bobot Penilaian Akhir</h6>
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="p-12 radius-8 bg-base border">
                                <small class="text-secondary-light d-block text-xs">Bobot Kuis</small>
                                <strong class="text-primary-600 fs-5"><?= (float) ($class['quiz_weight'] ?? 40) ?>%</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-12 radius-8 bg-base border">
                                <small class="text-secondary-light d-block text-xs">Bobot Tugas</small>
                                <strong class="text-warning-main fs-5"><?= (float) ($class['assignment_weight'] ?? 0) ?>%</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-12 radius-8 bg-base border">
                                <small class="text-secondary-light d-block text-xs">Bobot Post-Test</small>
                                <strong class="text-success-main fs-5"><?= (float) ($class['post_test_weight'] ?? 60) ?>%</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alur Tahapan Belajar Peserta -->
                <div class="border radius-12 p-20 bg-base">
                    <h6 class="text-sm fw-semibold text-primary-light mb-12">Alur Tahapan Belajar Peserta (<?= count($batchStages ?? []) ?> Tahap)</h6>
                    <div class="row g-3">
                        <?php
                        $stagesList = ! empty($batchStages) ? $batchStages : default_batch_stages();
                        foreach ($stagesList as $idx => $stg):
                        ?>
                            <div class="col-md-6">
                                <div class="p-16 radius-8 border bg-neutral-50 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary-600 text-white fw-bold px-8 py-2 radius-4 text-xs">
                                            Tahap <?= $stg['number'] ?? ($idx + 1) ?>
                                        </span>
                                        <h6 class="fw-bold mb-0 text-primary-light text-sm"><?= esc($stg['title'] ?? '') ?></h6>
                                    </div>
                                    <p class="text-secondary-light text-xs mb-0">
                                        <?= nl2br(esc($stg['description'] ?? '')) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ---------------------------------------------------- -->
            <!-- TAB 2: KURIKULUM & MODUL MATERI                      -->
            <!-- ---------------------------------------------------- -->
            <div class="tab-pane fade" id="content-kurikulum" role="tabpanel">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
                    <div>
                        <h6 class="text-base fw-semibold mb-1">Struktur Kurikulum &amp; Modul Pembelajaran</h6>
                        <p class="text-secondary-light text-xs mb-0">Rangkaian modul materi, materi bacaan, dan kuis evaluasi untuk angkatan ini.</p>
                    </div>
                    <a href="<?= site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId) ?>" class="btn btn-sm btn-primary-600 radius-8 px-16 py-8 d-inline-flex align-items-center gap-1 text-xs fw-semibold">
                        <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                        <span>Kelola Kurikulum di Editor</span>
                    </a>
                </div>

                <?php if (empty($modules)): ?>
                    <div class="p-32 text-center text-secondary-light border radius-12 bg-neutral-50">
                        <iconify-icon icon="solar:folder-with-files-outline" class="text-4xl text-neutral-400 mb-2"></iconify-icon>
                        <h6 class="text-sm fw-semibold mb-1">Belum Ada Modul Terdaftar</h6>
                        <p class="text-xs mb-16">Kelas ini belum memiliki modul materi. Klik tombol di bawah untuk menambah kurikulum.</p>
                        <a href="<?= site_url('dashboard/pembelajaran/kurikulum/kelas/' . $classId) ?>" class="btn btn-sm btn-primary-600 radius-8 px-16 py-8 text-xs">Tambah Modul Pertama</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0 align-middle begawi-table" data-table-name="kurikulum-modul-kelas" data-page-length="10">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">Urutan</th>
                                    <th>Judul Modul</th>
                                    <th>Total Materi / Lesson</th>
                                    <th>Kuis Evaluasi</th>
                                    <th class="text-center">Sifat</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($modules as $m): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-secondary-light">#<?= (int) ($m['sort_order'] ?? 1) ?></td>
                                        <td>
                                            <h6 class="text-sm fw-semibold mb-1 text-primary-light"><?= esc($m['title']) ?></h6>
                                            <p class="text-xs text-secondary-light mb-0 text-truncate" style="max-width: 400px;"><?= esc($m['description'] ?? '-') ?></p>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-6 text-xs fw-semibold">
                                                <iconify-icon icon="solar:document-text-outline" class="me-1"></iconify-icon>
                                                <?= (int) ($m['lesson_count'] ?? 0) ?> Materi
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning-50 text-warning-700 px-10 py-4 radius-6 text-xs fw-semibold">
                                                <iconify-icon icon="solar:check-read-outline" class="me-1"></iconify-icon>
                                                <?= (int) ($m['quiz_count'] ?? 0) ?> Kuis
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= ! empty($m['is_required']) ? 'bg-danger-50 text-danger-600' : 'bg-neutral-100 text-secondary-light' ?> px-8 py-4 radius-4 text-2xs">
                                                <?= ! empty($m['is_required']) ? 'Wajib' : 'Opsional' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?= view('dashboard/components/status_badge', ['status' => $m['status'] ?? 'draft']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ---------------------------------------------------- -->
            <!-- TAB 3: PENGAJAR & FASILITATOR                        -->
            <!-- ---------------------------------------------------- -->
            <div class="tab-pane fade" id="content-pengajar" role="tabpanel">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
                    <div>
                        <h6 class="text-base fw-semibold mb-1">Pengajar &amp; Evaluator Terassign</h6>
                        <p class="text-secondary-light text-xs mb-0">Trainer memfasilitasi materi, menilai tugas, dan memantau perkembangan belajar peserta.</p>
                    </div>
                </div>

                <!-- Form Assign Trainer -->
                <form method="post" action="<?= route_to('dashboard.learning.classes.assign-trainer', $classId) ?>" class="card radius-12 border bg-neutral-50 p-20 mb-24">
                    <?= csrf_field() ?>
                    <h6 class="text-xs fw-bold text-uppercase text-secondary-light mb-12">Tugaskan Pengajar Baru:</h6>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="trainer_user_id" class="form-label fw-semibold text-primary-light text-xs mb-4">Pilih Calon Trainer Aktif</label>
                            <select id="trainer_user_id" name="trainer_user_id" class="form-select radius-8" required>
                                <option value="">-- Pilih trainer terdaftar --</option>
                                <?php foreach ($trainerCandidates as $tc): ?>
                                    <option value="<?= (int) $tc['id'] ?>"><?= esc($tc['name']) ?> (<?= esc($tc['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="trainer_role" class="form-label fw-semibold text-primary-light text-xs mb-4">Peran Penugasan</label>
                            <select id="trainer_role" name="role" class="form-select radius-8">
                                <option value="primary">Pengampu Utama (Lead)</option>
                                <option value="co-trainer">Co-Trainer (Pendamping)</option>
                                <option value="evaluator">Evaluator / Penilai Tugas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary-600 radius-8 w-100 py-10 text-xs fw-semibold">
                                <iconify-icon icon="solar:user-plus-outline" class="me-1"></iconify-icon> Tugaskan
                            </button>
                        </div>
                    </div>
                </form>

                <?php if (empty($trainers)): ?>
                    <div class="p-32 text-center text-secondary-light border radius-12">
                        <iconify-icon icon="solar:user-speak-rounded-outline" class="text-4xl text-neutral-400 mb-2"></iconify-icon>
                        <p class="text-xs mb-0">Belum ada pengajar yang ditugaskan pada angkatan kelas ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0 align-middle begawi-table" data-table-name="trainer-pengampu-kelas">
                            <thead>
                                <tr>
                                    <th>Nama Pengajar</th>
                                    <th>Email Akun</th>
                                    <th>Peran Penugasan</th>
                                    <th>Waktu Penugasan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($trainers as $tr): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center fw-bold text-xs">
                                                    <?= strtoupper(substr($tr['name'] ?? 'T', 0, 2)) ?>
                                                </div>
                                                <span class="fw-semibold text-sm text-primary-light"><?= esc($tr['name']) ?></span>
                                            </div>
                                        </td>
                                        <td><code class="text-xs"><?= esc($tr['email']) ?></code></td>
                                        <td>
                                            <span class="badge bg-primary-50 text-primary-600 px-10 py-4 radius-4 text-xs font-monospace">
                                                <?= esc(ucwords(str_replace('_', ' ', (string) ($tr['role'] ?? 'primary')))) ?>
                                            </span>
                                        </td>
                                        <td><span class="text-xs text-secondary-light"><?= ! empty($tr['assigned_at']) ? date('d M Y H:i', strtotime($tr['assigned_at'])) : '-' ?></span></td>
                                        <td class="text-center">
                                            <form method="post" action="<?= route_to('dashboard.learning.classes.revoke-trainer', $classId, $tr['user_id']) ?>" onsubmit="return confirm('Apakah Anda yakin ingin mencabut penugasan pengajar ini?')" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger radius-6 px-10 py-4 text-xs" title="Cabut Penugasan">
                                                    <iconify-icon icon="solar:trash-bin-trash-outline" class="me-1"></iconify-icon> Cabut
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ---------------------------------------------------- -->
            <!-- TAB 4: PESERTA TERDAFTAR & PRESENSI                  -->
            <!-- ---------------------------------------------------- -->
            <div class="tab-pane fade" id="content-peserta" role="tabpanel">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
                    <div>
                        <h6 class="text-base fw-semibold mb-1">Daftar Peserta Terdaftar (<?= $enrolledCount ?>)</h6>
                        <p class="text-secondary-light text-xs mb-0">Peserta yang telah terdaftar dalam angkatan kelas ini.</p>
                    </div>
                    <a href="<?= route_to('dashboard.learning.enrollments') ?>?class_id=<?= $classId ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-16 py-8 text-xs fw-semibold">
                        <iconify-icon icon="solar:users-group-two-rounded-outline" class="me-1"></iconify-icon> Kelola di Modul Enrollment
                    </a>
                </div>

                <?php if (empty($enrollments)): ?>
                    <div class="p-32 text-center text-secondary-light border radius-12 bg-neutral-50">
                        <iconify-icon icon="solar:users-group-rounded-outline" class="text-4xl text-neutral-400 mb-2"></iconify-icon>
                        <p class="text-xs mb-0">Belum ada peserta yang mendaftar pada kelas ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0 align-middle begawi-table" data-table-name="peserta-kelas-pbj" data-page-length="10">
                            <thead>
                                <tr>
                                    <th>Peserta</th>
                                    <th>NIP / Identitas</th>
                                    <th>Instansi / OPD</th>
                                    <th>Waktu Daftar</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Nilai Akhir</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrollments as $e): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center fw-bold text-xs">
                                                    <?= strtoupper(substr($e['user_name'] ?? 'P', 0, 2)) ?>
                                                </div>
                                                <div>
                                                    <span class="d-block fw-semibold text-sm text-primary-light"><?= esc($e['user_name']) ?></span>
                                                    <span class="d-block text-xs text-secondary-light"><?= esc($e['user_email']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code class="text-xs"><?= esc($e['nip'] ?? '-') ?></code></td>
                                        <td><span class="text-xs text-secondary-light"><?= esc($e['instansi'] ?? '-') ?></span></td>
                                        <td><span class="text-xs text-secondary-light"><?= ! empty($e['enrolled_at']) ? date('d M Y H:i', strtotime($e['enrolled_at'])) : '-' ?></span></td>
                                        <td class="text-center">
                                            <?= view('dashboard/components/status_badge', ['status' => $e['status'] ?? 'active']) ?>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-sm <?= ((float)($e['final_score'] ?? 0) >= (int)($class['passing_grade'] ?? 70)) ? 'text-success-main' : 'text-neutral-700' ?>">
                                                <?= $e['final_score'] !== null ? number_format((float)$e['final_score'], 1) : '-' ?>
                                            </strong>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= route_to('dashboard.learning.enrollments.detail', $e['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-10 py-4 text-xs" title="Detail Enrollment">
                                                <iconify-icon icon="solar:eye-outline"></iconify-icon>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ---------------------------------------------------- -->
            <!-- TAB 5: BUKU NILAI & KELAYAKAN SERTIFIKAT             -->
            <!-- ---------------------------------------------------- -->
            <div class="tab-pane fade" id="content-nilai" role="tabpanel">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
                    <div>
                        <h6 class="text-base fw-semibold mb-1">Buku Nilai (Gradebook) &amp; Kelayakan Sertifikat</h6>
                        <p class="text-secondary-light text-xs mb-0">Evaluasi pemenuhan standar passing grade (<?= (int) ($class['passing_grade'] ?? 70) ?> / 100) dan penerbitan sertifikat resmi.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?= site_url('dashboard/pembelajaran/kelas/' . $classId . '/gradebook/export') ?>" class="btn btn-sm btn-success-600 text-white radius-8 px-16 py-8 d-inline-flex align-items-center gap-1 text-xs fw-semibold">
                            <iconify-icon icon="solar:file-download-outline" class="text-base"></iconify-icon>
                            <span>Unduh Buku Nilai (CSV UTF-8 BOM)</span>
                        </a>
                        <a href="<?= site_url('dashboard/sertifikat/batch') ?>" class="btn btn-sm btn-outline-primary-600 radius-8 px-16 py-8 text-xs fw-semibold">
                            <iconify-icon icon="solar:diploma-outline" class="me-1"></iconify-icon>
                            <span>Penerbitan Batch Masal</span>
                        </a>
                    </div>
                </div>

                <?php if (empty($enrollments)): ?>
                    <div class="p-32 text-center text-secondary-light border radius-12 bg-neutral-50">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-4xl text-neutral-400 mb-2"></iconify-icon>
                        <p class="text-xs mb-0">Belum ada data nilai peserta pada angkatan kelas ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0 align-middle begawi-table" data-table-name="gradebook-kelas-pbj" data-page-length="10">
                            <thead>
                                <tr>
                                    <th>Peserta</th>
                                    <th>Instansi / NIP</th>
                                    <th class="text-center">Nilai Akhir</th>
                                    <th class="text-center">Passing Grade</th>
                                    <th class="text-center">Status Kelulusan</th>
                                    <th class="text-center">Nomor Sertifikat</th>
                                    <th class="text-center">Status Sertifikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrollments as $en): ?>
                                    <?php
                                    $finalScore = $en['final_score'] !== null ? (float) $en['final_score'] : 0.0;
                                    $passingGrade = (int) ($class['passing_grade'] ?? 70);
                                    $isPassed = in_array($en['status'], ['completed', 'passed'], true) || ($finalScore >= $passingGrade && $en['status'] === 'active');
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="d-block fw-semibold text-sm text-primary-light"><?= esc($en['user_name']) ?></span>
                                            <span class="d-block text-xs text-secondary-light"><?= esc($en['user_email']) ?></span>
                                        </td>
                                        <td>
                                            <span class="d-block text-xs text-primary-light"><?= esc($en['instansi'] ?? '-') ?></span>
                                            <code class="text-2xs text-secondary-light"><?= esc($en['nip'] ?? '-') ?></code>
                                        </td>
                                        <td class="text-center">
                                            <h6 class="text-sm mb-0 <?= $isPassed ? 'text-success-main' : 'text-danger-main' ?>">
                                                <?= $en['final_score'] !== null ? number_format($finalScore, 1) : '-' ?>
                                            </h6>
                                        </td>
                                        <td class="text-center text-xs text-secondary-light font-monospace"><?= $passingGrade ?></td>
                                        <td class="text-center">
                                            <?php if ($isPassed): ?>
                                                <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs fw-semibold">
                                                    <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon> LULUS
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-50 text-warning-700 px-10 py-4 radius-4 text-xs fw-semibold">
                                                    BELUM LULUS
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (! empty($en['certificate_number'])): ?>
                                                <a href="<?= site_url('dashboard/sertifikat/' . $en['certificate_id']) ?>" class="badge bg-primary-50 text-primary-600 font-monospace px-10 py-4 radius-4 text-xs">
                                                    <?= esc($en['certificate_number']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-secondary-light text-xs">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (! empty($en['certificate_id'])): ?>
                                                <span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs fw-semibold">Diterbitkan</span>
                                            <?php elseif ($isPassed): ?>
                                                <span class="badge bg-info-50 text-info-700 px-10 py-4 radius-4 text-xs fw-semibold">Layak Terbit</span>
                                            <?php else: ?>
                                                <span class="badge bg-neutral-100 text-secondary-light px-10 py-4 radius-4 text-xs">Belum Layak</span>
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
</div>

<!-- ============================================================== -->
<!-- MODAL: WIZARD DUPLIKASI ANGKATAN KELAS                         -->
<!-- ============================================================== -->
<div class="modal fade" id="duplicateBatchModal" tabindex="-1" aria-labelledby="duplicateBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="<?= site_url('dashboard/pembelajaran/kelas/' . $classId . '/duplikasi') ?>" class="modal-content radius-16 border-0 shadow bg-base">
            <?= csrf_field() ?>
            <div class="modal-header border-bottom py-16 px-24 bg-base">
                <div class="d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:copy-bold" class="text-primary-600 text-2xl"></iconify-icon>
                    <div>
                        <h6 class="modal-title fs-6 fw-bold mb-0" id="duplicateBatchModalLabel">Duplikasi Angkatan Kelas (Batch Duplication)</h6>
                        <span class="text-xs text-secondary-light">Gandakan seluruh kurikulum, modul, materi, dan soal kuis ke ID kelas baru tanpa input ulang</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <div class="alert alert-primary bg-primary-50 border-primary-200 radius-12 p-16 mb-20">
                    <div class="d-flex align-items-start gap-2 text-xs text-primary-900">
                        <iconify-icon icon="solar:info-circle-bold" class="text-primary-600 text-xl flex-shrink-0 mt-1"></iconify-icon>
                        <div>
                            <strong>Kelas Sumber Asal:</strong> <?= esc($class['name']) ?> (<code><?= esc($class['code']) ?></code>)
                            <div class="text-secondary-light mt-1">Sistem akan secara otomatis menyalin <?= count($modules) ?> modul, seluruh materi/lesson, dan soal kuis yang ada ke batch baru berstatus <code>Draft</code>.</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Nama Kelas Angkatan Baru <span class="text-danger">*</span></label>
                        <input type="text" name="new_name" class="form-control radius-8" value="<?= esc($class['name']) ?> - Batch Baru" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Kode Batch Baru (Unik) <span class="text-danger">*</span></label>
                        <input type="text" name="new_code" class="form-control radius-8 font-monospace text-uppercase" value="<?= esc($class['code']) ?>-B2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Kapasitas Kursi Peserta</label>
                        <input type="number" min="0" name="capacity" class="form-control radius-8" value="<?= (int) ($class['capacity'] ?? 30) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Jadwal Tanggal Mulai</label>
                        <input type="date" name="start_at" class="form-control radius-8" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-xs mb-4">Jadwal Tanggal Selesai</label>
                        <input type="date" name="end_at" class="form-control radius-8" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                    </div>
                    <div class="col-12">
                        <div class="p-16 radius-8 border bg-neutral-50">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="copy_trainers" value="1" id="copyTrainersCheck" checked>
                                <label class="form-check-label fw-semibold text-sm text-primary-light ms-2 cursor-pointer" for="copyTrainersCheck">
                                    Tugaskan juga <?= count($trainers) ?> pengajar (trainer) yang sama pada angkatan baru ini
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top py-12 px-24 bg-base justify-content-between">
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8 text-xs" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-8 text-xs fw-semibold d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                    <span>Mulai Duplikasi Batch Baru</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

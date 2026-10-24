<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<?php
$isAdmin = !empty($isAdmin);
$operators = $operators ?? [];
?>

<div class="operator-class-trainers-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary text-white px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-sitemap me-1"></i> <?= $isAdmin ? 'Hierarki Penugasan RBAC (Admin -> Operator & Multi-Trainer)' : 'Kelas Kelolaan & Asisten Trainer' ?>
                </span>
                <h1 class="h3 fw-bold text-navy mb-1"><?= $isAdmin ? 'Penugasan Kelas, Operator & Multi-Trainer' : 'Kelas Kelolaan & Penugasan Trainer' ?></h1>
                <p class="text-muted small mb-0">
                    <?php if ($isAdmin): ?>
                        Tugaskan <strong>Operator Pengelola (Asisten Trainer)</strong> dan/atau <strong>Multi-Trainer</strong> ke kelas secara bertahap maupun bersamaan. Operator hanya dapat mengakses kelas yang ditugaskan kepadanya.
                    <?php else: ?>
                        Daftar kelas yang ditugaskan kepada Anda sebagai <strong>Operator / Asisten Trainer</strong>. Anda dapat menugaskan Multi-Trainer serta ikut mengelola kurikulum, tugas, dan penilaian kelas.
                    <?php endif; ?>
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#assignTrainerModal">
                    <i class="fas fa-user-plus me-1"></i> <?= $isAdmin ? 'Tugaskan Operator / Trainer' : 'Tugaskan Multi-Trainer' ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Counters -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block"><?= $isAdmin ? 'Total Kelas' : 'Kelas Ditugaskan' ?></span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= count($classes) ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Kelas Bertrainer Aktif</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= count(array_filter($classes, static fn ($c) => !empty(array_filter($c['trainers'] ?? [], static fn($t) => ($t['status'] ?? '') === 'active')))) ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Kelas Ber-Operator</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= count(array_filter($classes, static fn ($c) => !empty(array_filter($c['operators'] ?? [], static fn($o) => ($o['status'] ?? '') === 'active')))) ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info-subtle text-info fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Trainer Tersedia</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= count($trainers) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table: Daftar Kelas, Operator & Multi-Trainer -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-chalkboard text-primary me-2"></i>Daftar Kelas, Operator Asisten & Multi-Trainer</h2>
            <span class="badge bg-light text-dark border px-3 py-2 small"><?= count($classes) ?> Kelas Terdata</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" data-export-title="Daftar Penugasan Kelas Operator dan Trainer">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Kelas & Course</th>
                        <th>Kode & Periode</th>
                        <th>Peserta</th>
                        <th>Operator Pengelola (Asisten)</th>
                        <th>Tim Trainer (Multi-Trainer)</th>
                        <th class="text-end" data-no-sort="true">Aksi & Co-Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <?= $isAdmin ? 'Belum ada kelas pembelajaran yang dibuat.' : 'Belum ada kelas yang ditugaskan kepada Anda oleh Administrator.' ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classes as $c): ?>
                            <?php
                            $activeOperators = array_values(array_filter($c['operators'] ?? [], static fn($o) => ($o['status'] ?? '') === 'active'));
                            $activeTrainers  = array_values(array_filter($c['trainers'] ?? [], static fn($t) => ($t['status'] ?? '') === 'active'));
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-navy small d-block"><?= esc($c['name']) ?></strong>
                                    <span class="text-muted d-block" style="font-size: 0.78rem;"><?= esc($c['course_title'] ?? 'Course PBJ') ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace mb-1"><?= esc($c['code'] ?? '-') ?></span>
                                    <div class="small text-muted" style="font-size: 0.74rem;">
                                        <?= !empty($c['start_at']) ? date('d/m/Y', strtotime($c['start_at'])) : '-' ?> &mdash; <?= !empty($c['end_at']) ? date('d/m/Y', strtotime($c['end_at'])) : '-' ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small">
                                        <?= (int) ($c['student_count'] ?? 0) ?> Peserta
                                    </span>
                                </td>
                                <td>
                                    <?php if (empty($activeOperators)): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1 small">
                                            <i class="fas fa-minus-circle me-1"></i> Belum Ditugaskan
                                        </span>
                                    <?php else: ?>
                                        <div class="d-flex flex-column gap-1">
                                            <?php foreach ($activeOperators as $op): ?>
                                                <div class="d-flex align-items-center justify-content-between gap-2 p-1 px-2 bg-warning-subtle rounded-2" style="font-size: 0.78rem;">
                                                    <div>
                                                        <i class="fas fa-user-shield text-warning-emphasis me-1"></i>
                                                        <strong class="text-navy"><?= esc($op['name']) ?></strong>
                                                        <span class="badge bg-warning text-dark py-0 px-1 ms-1" style="font-size: 0.65rem;">Asisten</span>
                                                    </div>
                                                    <?php if ($isAdmin): ?>
                                                        <form method="POST" action="<?= route_to('workspace.operator.class-trainers.revoke') ?>" class="d-inline" onsubmit="return confirm('Cabut penugasan Operator ini dari kelas?')">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="class_id" value="<?= esc($c['id']) ?>">
                                                            <input type="hidden" name="user_id" value="<?= esc($op['user_id']) ?>">
                                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" title="Cabut Operator">
                                                                <i class="fas fa-times-circle"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (empty($activeTrainers)): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 small">
                                            <i class="fas fa-exclamation-circle me-1"></i> Belum Ada Trainer
                                        </span>
                                    <?php else: ?>
                                        <div class="d-flex flex-column gap-1">
                                            <?php foreach ($activeTrainers as $tr): ?>
                                                <?php
                                                $roleLabelBadge = match ($tr['role'] ?? 'primary') {
                                                    'co_trainer' => ['label' => 'Co-Trainer', 'cls' => 'bg-info-subtle text-info'],
                                                    'guest'      => ['label' => 'Tamu', 'cls' => 'bg-secondary-subtle text-secondary'],
                                                    default      => ['label' => 'Utama', 'cls' => 'bg-primary-subtle text-primary'],
                                                };
                                                ?>
                                                <div class="d-flex align-items-center justify-content-between gap-2 p-1 px-2 bg-light border rounded-2" style="font-size: 0.78rem;">
                                                    <div>
                                                        <i class="fas fa-chalkboard-teacher text-primary me-1"></i>
                                                        <strong class="text-navy"><?= esc($tr['name']) ?></strong>
                                                        <span class="badge <?= $roleLabelBadge['cls'] ?> py-0 px-1 ms-1" style="font-size: 0.65rem;">
                                                            <?= $roleLabelBadge['label'] ?>
                                                        </span>
                                                    </div>
                                                    <form method="POST" action="<?= route_to('workspace.operator.class-trainers.revoke') ?>" class="d-inline" onsubmit="return confirm('Cabut penugasan trainer ini dari kelas?')">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="class_id" value="<?= esc($c['id']) ?>">
                                                        <input type="hidden" name="user_id" value="<?= esc($tr['user_id']) ?>">
                                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" title="Cabut Penugasan">
                                                            <i class="fas fa-times-circle"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex flex-wrap justify-content-end gap-1">
                                        <a href="<?= route_to('workspace.trainer.class.detail', (string) $c['id']) ?>" class="btn btn-sm btn-success rounded-pill px-3" title="Masuk ke Ruang Kerja Kelas (Kurikulum, Materi, Kuis, Tugas & Penilaian)">
                                            <i class="fas fa-laptop-house me-1"></i> Kelola Kelas
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#assignTrainerModal" onclick="setModalClass(<?= esc($c['id']) ?>)">
                                            <i class="fas fa-user-plus me-1"></i> Assign
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Secondary Table: Direktori Trainer Tersedia -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-users-cog text-info me-2"></i>Direktori Pengajar (Trainer)</h2>
            <span class="badge bg-light text-dark border px-3 py-2 small"><?= count($trainers) ?> Trainer Aktif</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" data-export-title="Direktori Trainer Begawi">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Nama Pengajar</th>
                        <th>Email</th>
                        <th>Status Akun</th>
                        <th>Kelas Aktif Diampu</th>
                        <th class="text-end" data-no-sort="true">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($trainers)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada user dengan role Trainer.</td></tr>
                    <?php else: ?>
                        <?php foreach ($trainers as $t): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle bg-primary-subtle text-primary rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                            <?= strtoupper(substr($t['name'], 0, 1)) ?>
                                        </div>
                                        <strong class="text-navy small"><?= esc($t['name']) ?></strong>
                                    </div>
                                </td>
                                <td class="small text-muted"><?= esc($t['email']) ?></td>
                                <td>
                                    <span class="badge <?= ($t['account_status'] === 'active') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' ?> rounded-pill px-2 py-1 small">
                                        <?= esc(ucfirst($t['account_status'] ?? 'active')) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-primary"><?= (int) ($t['active_classes_count'] ?? 0) ?></strong> Kelas
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#assignTrainerModal" onclick="setModalTrainer(<?= esc($t['id']) ?>)">
                                        <i class="fas fa-link me-1"></i> Hubungkan ke Kelas
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Assign Operator & Multi-Trainer -->
<div class="modal fade" id="assignTrainerModal" tabindex="-1" aria-labelledby="assignTrainerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="POST" action="<?= route_to('workspace.operator.class-trainers.assign') ?>">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-navy" id="assignTrainerModalLabel">
                        <i class="fas fa-sitemap text-primary me-2"></i><?= $isAdmin ? 'Penugasan Kelas: Operator & Multi-Trainer' : 'Penugasan Multi-Trainer ke Kelas' ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-navy small mb-1">Pilih Kelas Pembelajaran <span class="text-danger">*</span></label>
                        <select name="class_id" id="modalClassSelect" class="form-select rounded-3 py-2" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($classes as $cl): ?>
                                <option value="<?= esc($cl['id']) ?>">
                                    <?= esc($cl['name']) ?> (<?= esc($cl['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="ws-form-hint"><i class="fas fa-info-circle"></i> Pilih kelas target penugasan.</div>
                    </div>

                    <?php if ($isAdmin): ?>
                        <div class="mb-3 p-3 rounded-3 border bg-warning-subtle bg-opacity-25">
                            <label class="form-label fw-bold text-navy small mb-1">
                                <i class="fas fa-user-shield text-warning-emphasis me-1"></i> Tugaskan Operator Pengelola / Asisten Trainer (Opsional / Bersamaan)
                            </label>
                            <div class="row g-2 mt-1" style="max-height: 150px; overflow-y: auto;">
                                <?php foreach ($operators as $op): ?>
                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center gap-2 p-2 rounded-2 bg-white border small cursor-pointer">
                                            <input type="checkbox" name="operator_ids[]" value="<?= esc($op['id']) ?>" class="form-check-input mt-0">
                                            <span class="text-truncate">
                                                <strong class="text-navy"><?= esc($op['name']) ?></strong>
                                                <small class="text-muted d-block"><?= esc($op['email']) ?> (<?= (int) ($op['active_classes_count'] ?? 0) ?> kelas)</small>
                                            </span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="ws-form-hint mt-2">
                                <i class="fas fa-lightbulb"></i> Anda dapat menugaskan Operator saja terlebih dahulu (biar Operator yang memilih Trainer), atau memilih Operator dan Trainer sekaligus.
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3 p-3 rounded-3 border bg-light">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <label class="form-label fw-bold text-navy small mb-0">
                                <i class="fas fa-chalkboard-teacher text-primary me-1"></i> Pilih Pengajar / Multi-Trainer <?= $isAdmin ? '(Opsional jika sudah memilih Operator)' : '<span class="text-danger">*</span>' ?>
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-muted">Peran Trainer:</span>
                                <select name="trainer_role" class="form-select form-select-sm rounded-pill px-3" style="width: auto;">
                                    <option value="primary">Trainer Utama (Primary)</option>
                                    <option value="co_trainer">Co-Trainer (Pendamping)</option>
                                    <option value="guest">Dosen / Pengajar Tamu</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mt-1" style="max-height: 200px; overflow-y: auto;">
                            <?php foreach ($trainers as $tr): ?>
                                <div class="col-md-6">
                                    <label class="d-flex align-items-center gap-2 p-2 rounded-2 bg-white border small cursor-pointer">
                                        <input type="checkbox" name="trainer_ids[]" value="<?= esc($tr['id']) ?>" class="form-check-input mt-0 modal-trainer-checkbox">
                                        <span class="text-truncate">
                                            <strong class="text-navy"><?= esc($tr['name']) ?></strong>
                                            <small class="text-muted d-block"><?= esc($tr['email']) ?> &bull; <?= (int) ($tr['active_classes_count'] ?? 0) ?> Kelas Aktif</small>
                                        </span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="ws-form-hint mt-2">
                            <i class="fas fa-users"></i> Mendukung <strong>Multi-Trainer</strong>: centang lebih dari 1 pengajar untuk menugaskan tim pengajar ke dalam kelas yang sama.
                        </div>
                    </div>

                    <div class="form-check p-3 bg-light rounded-3 mt-3 border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="replace_existing" value="1" id="replaceExistingCheck">
                        <label class="form-check-label text-navy small fw-semibold" for="replaceExistingCheck">
                            Ganti Tim Trainer Sebelumnya pada Kelas Ini
                        </label>
                        <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                            Jika dicentang, trainer lama yang tidak ikut dicentang akan dicabut (revoked). Biarkan tidak dicentang jika ingin <strong>menambahkan</strong> trainer baru (Multi-Trainer).
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setModalClass(classId) {
    var select = document.getElementById('modalClassSelect');
    if (select) select.value = classId;
}
function setModalTrainer(trainerId) {
    document.querySelectorAll('.modal-trainer-checkbox').forEach(function (cb) {
        cb.checked = (String(cb.value) === String(trainerId));
    });
}
</script>

<?= $this->endSection() ?>

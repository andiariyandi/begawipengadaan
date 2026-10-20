<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="operator-class-trainers-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary text-white px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-chalkboard-teacher me-1"></i> Penugasan Pengajar
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Kelas & Trainer Assignment</h1>
                <p class="text-muted small mb-0">
                    Hubungkan kelas pembelajaran dengan trainer yang berwenang. Perubahan penugasan akan langsung tersinkronisasi ke ruang kerja pengajar.
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#assignTrainerModal">
                    <i class="fas fa-user-plus me-1"></i> Tugaskan Trainer
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <!-- Summary Counters -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Total Kelas</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= count($classes) ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Kelas Bertrainer</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= count(array_filter($classes, static fn ($c) => !empty($c['trainers']))) ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
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

    <!-- Main Table: Daftar Kelas & Pengajar Saat Ini -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-chalkboard text-primary me-2"></i>Daftar Kelas & Penugasan Pengajar</h2>
            <span class="badge bg-light text-dark border px-3 py-2 small"><?= count($classes) ?> Kelas Terdata</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Kelas & Course</th>
                        <th>Kode</th>
                        <th>Periode</th>
                        <th>Peserta</th>
                        <th>Trainer Saat Ini</th>
                        <th class="text-end">Aksi Penugasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada kelas pembelajaran yang dibuat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classes as $c): ?>
                            <tr>
                                <td>
                                    <strong class="text-navy small d-block"><?= esc($c['name']) ?></strong>
                                    <span class="text-muted" style="font-size: 0.78rem;"><?= esc($c['course_title'] ?? 'Course PBJ') ?></span>
                                </td>
                                <td><span class="badge bg-light text-dark border font-monospace"><?= esc($c['code'] ?? '-') ?></span></td>
                                <td class="small text-muted">
                                    <?= !empty($c['start_at']) ? date('d/m/Y', strtotime($c['start_at'])) : '-' ?> &mdash; <?= !empty($c['end_at']) ? date('d/m/Y', strtotime($c['end_at'])) : '-' ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small">
                                        <?= (int) ($c['student_count'] ?? 0) ?> Peserta
                                    </span>
                                </td>
                                <td>
                                    <?php if (empty($c['trainers'])): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 small">
                                            <i class="fas fa-exclamation-circle me-1"></i> Belum Ada Trainer
                                        </span>
                                    <?php else: ?>
                                        <div class="d-flex flex-column gap-1">
                                            <?php foreach ($c['trainers'] as $tr): ?>
                                                <div class="d-flex align-items-center justify-content-between gap-2 p-1 bg-light rounded-2" style="font-size: 0.8rem;">
                                                    <div>
                                                        <i class="fas fa-chalkboard-teacher text-primary me-1"></i>
                                                        <strong class="text-navy"><?= esc($tr['name']) ?></strong>
                                                        <span class="badge <?= ($tr['status'] === 'active') ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> py-0 px-1" style="font-size: 0.7rem;">
                                                            <?= esc($tr['status']) ?>
                                                        </span>
                                                    </div>
                                                    <?php if ($tr['status'] === 'active'): ?>
                                                        <form method="POST" action="<?= route_to('workspace.operator.class-trainers.revoke') ?>" class="d-inline" onsubmit="return confirm('Cabut penugasan trainer ini?')">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="class_id" value="<?= esc($c['id']) ?>">
                                                            <input type="hidden" name="user_id" value="<?= esc($tr['user_id']) ?>">
                                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" title="Cabut Penugasan">
                                                                <i class="fas fa-times-circle"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#assignTrainerModal" onclick="setModalClass(<?= esc($c['id']) ?>)">
                                        <i class="fas fa-user-plus me-1"></i> Assign Trainer
                                    </button>
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
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Nama Pengajar</th>
                        <th>Email</th>
                        <th>Status Akun</th>
                        <th>Kelas Aktif Diampu</th>
                        <th class="text-end">Aksi Cepat</th>
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

<!-- Modal: Assign Trainer -->
<div class="modal fade" id="assignTrainerModal" tabindex="-1" aria-labelledby="assignTrainerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="POST" action="<?= route_to('workspace.operator.class-trainers.assign') ?>">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-navy" id="assignTrainerModalLabel">
                        <i class="fas fa-chalkboard-teacher text-primary me-2"></i>Penugasan Trainer ke Kelas
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
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-navy small mb-1">Pilih Pengajar (Trainer) <span class="text-danger">*</span></label>
                        <select name="user_id" id="modalTrainerSelect" class="form-select rounded-3 py-2" required>
                            <option value="">-- Pilih Pengajar --</option>
                            <?php foreach ($trainers as $tr): ?>
                                <option value="<?= esc($tr['id']) ?>">
                                    <?= esc($tr['name']) ?> (<?= esc($tr['email']) ?>) &mdash; <?= (int) ($tr['active_classes_count'] ?? 0) ?> Kelas Aktif
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-check p-3 bg-light rounded-3 mt-3">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="replace_existing" value="1" id="replaceExistingCheck">
                        <label class="form-check-label text-navy small fw-semibold" for="replaceExistingCheck">
                            Ganti Trainer Sebelumnya
                        </label>
                        <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                            Jika dicentang, pengajar lama pada kelas ini akan dicabut (revoked) dan digantikan oleh pengajar yang dipilih.
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
    var select = document.getElementById('modalTrainerSelect');
    if (select) select.value = trainerId;
}
</script>

<?= $this->endSection() ?>

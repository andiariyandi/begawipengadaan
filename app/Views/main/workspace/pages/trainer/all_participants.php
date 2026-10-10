<?php
/**
 * Trainer - Peserta Kelas (Agregat)
 * FiStudy UI DNA
 */
$classes         = $classes ?? [];
$selectedClassId = $selectedClassId ?? 0;
$participants    = $participants ?? [];
?>

<div class="trainer-participants-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-info-subtle text-info px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-users me-1"></i> Data Peserta
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Peserta Pembelajaran</h1>
                <p class="text-muted small mb-0">Daftar seluruh peserta yang terdaftar pada kelas bimbingan Anda.</p>
            </div>

            <!-- Class Filter Dropdown -->
            <?php if (! empty($classes)): ?>
                <form method="GET" action="" class="d-flex align-items-center gap-2">
                    <label class="small text-muted text-nowrap fw-semibold">Filter Kelas:</label>
                    <select name="class_id" class="form-select form-select-sm rounded-pill pe-4" onchange="this.form.submit()">
                        <option value="0">Semua Kelas</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= esc($c['id']) ?>" <?= ($selectedClassId === (int) $c['id']) ? 'selected' : '' ?>>
                                <?= esc($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Participants Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted small">Menampilkan <strong><?= count($participants) ?></strong> peserta</span>
        </div>

        <?php if (empty($participants)): ?>
            <div class="text-center py-5 border rounded-4 bg-light">
                <i class="fas fa-user-slash fa-3x text-muted mb-3 opacity-25"></i>
                <h3 class="h6 fw-bold text-navy mb-1">Tidak Ada Peserta Ditemukan</h3>
                <p class="text-muted small mb-0">Belum ada peserta yang terdaftar pada kelas yang dipilih.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>Peserta</th>
                            <th>Kelas</th>
                            <th>Status Enrollment</th>
                            <th>Tanggal Daftar</th>
                            <th style="width: 200px;">Progres Materi</th>
                            <th>Nilai</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants as $p): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle bg-primary-subtle text-primary fw-bold" style="width: 38px; height: 38px;">
                                            <?= strtoupper(substr($p['name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong class="text-navy small d-block"><?= esc($p['name']) ?></strong>
                                            <span class="text-muted" style="font-size: 0.76rem;"><?= esc($p['email']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-navy border rounded-pill px-2 py-1 small">
                                        <?= esc($p['class_name'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $p['enrollment_status'] === 'active' ? 'bg-success-subtle text-success' : ($p['enrollment_status'] === 'completed' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary') ?> rounded-pill px-2 py-1 small">
                                        <?= esc(ucfirst($p['enrollment_status'])) ?>
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <?= esc(! empty($p['enrolled_at']) ? date('d M Y', strtotime($p['enrolled_at'])) : '-') ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                        <span><?= (int) ($p['done_lessons'] ?? 0) ?>/<?= (int) ($p['total_lessons'] ?? 0) ?></span>
                                        <span class="fw-semibold text-navy"><?= esc($p['progress_percent'] ?? 0.0) ?>%</span>
                                    </div>
                                    <div class="progress rounded-pill" style="height: 6px;">
                                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= esc($p['progress_percent'] ?? 0.0) ?>%"></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (! empty($p['passed_at']) || ! empty($p['final_score'])): ?>
                                        <strong class="text-success"><?= esc($p['final_score'] ?? '100.00') ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted small">&ndash;</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= route_to('workspace.trainer.participant.detail', (string) $p['class_id'], (string) $p['enrollment_id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Rincian & Catatan
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$classId = (int) ($class['id'] ?? 0);
$trainers        = $trainers ?? [];
$trainerCandidates = $trainerCandidates ?? [];
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div><h6 class="fw-semibold mb-1">Detail Kelas</h6><p class="text-secondary-light mb-0">ID Kelas: <?= esc($classId) ?></p></div>
    <div class="d-flex gap-2">
        <a href="<?= route_to('dashboard.learning.classes.edit', $classId) ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Ubah Kelas</a>
        <a href="<?= route_to('dashboard.learning.classes') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
    </div>
</div>

<div class="card mb-24"><div class="card-body p-24"><div class="row gy-20">
    <div class="col-md-6"><p class="text-secondary-light mb-4">Nama Kelas</p><h6 class="mb-0"><?= esc($class['name']) ?></h6></div>
    <div class="col-md-6"><p class="text-secondary-light mb-4">Course</p><h6 class="mb-0"><?= esc($class['course_title'] ?? '-') ?></h6></div>
    <div class="col-md-4"><p class="text-secondary-light mb-4">Kode</p><h6 class="mb-0 text-primary-600"><?= esc($class['code']) ?></h6></div>
    <div class="col-md-4"><p class="text-secondary-light mb-4">Kapasitas</p><h6 class="mb-0"><?= esc($class['capacity'] ?? '-') ?></h6></div>
    <div class="col-md-4"><p class="text-secondary-light mb-4">Status</p><?= view('dashboard/components/status_badge', ['status' => $class['status'] ?? '-']) ?></div>
    <div class="col-md-6"><p class="text-secondary-light mb-4">Mulai</p><h6 class="mb-0"><?= esc($class['start_at'] ?? '-') ?></h6></div>
    <div class="col-md-6"><p class="text-secondary-light mb-4">Selesai</p><h6 class="mb-0"><?= esc($class['end_at'] ?? '-') ?></h6></div>
    <div class="col-md-6"><p class="text-secondary-light mb-4">Nilai Lulus</p><h6 class="mb-0"><?= esc($class['passing_grade']) ?></h6></div>
    <div class="col-md-6"><p class="text-secondary-light mb-4">Sertifikat</p><h6 class="mb-0"><?= ! empty($class['certificate_enabled']) ? 'Aktif' : 'Tidak aktif' ?></h6></div>
</div></div></div>

<div class="card"><div class="card-body p-24">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-16">
        <div><h6 class="fw-semibold mb-1">Pengajar (Trainer)</h6><p class="text-secondary-light mb-0 small">Trainer yang di-assign akan mengelola modul, lesson, dan quiz pada kelas ini.</p></div>
    </div>

    <form method="post" action="<?= route_to('dashboard.learning.classes.assign-trainer', $classId) ?>" class="row gy-12 align-items-end mb-24">
        <?= csrf_field() ?>
        <div class="col-md-8">
            <label for="trainer_user_id" class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Trainer</label>
            <select id="trainer_user_id" name="trainer_user_id" class="form-select radius-8" required>
                <option value="">-- Pilih trainer aktif --</option>
                <?php foreach ($trainerCandidates as $c): ?>
                    <option value="<?= esc($c['id']) ?>"><?= esc($c['name']) ?> (<?= esc($c['email']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">Assign Trainer</button>
        </div>
    </form>

    <?php if (empty($trainers)): ?>
        <p class="text-secondary-light mb-0">Belum ada trainer yang ditugaskan.</p>
    <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Status</th><th>Ditugaskan</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($trainers as $t): ?>
                    <tr>
                        <td><?= esc($t['name'] ?? '-') ?></td>
                        <td><?= esc($t['username'] ?? '-') ?></td>
                        <td><?= esc($t['email'] ?? '-') ?></td>
                        <td class="text-center"><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc(ucfirst($t['status'] ?? '-')) ?></span></td>
                        <td><?= esc($t['assigned_at'] ?? '-') ?></td>
                        <td class="text-end">
                            <?php if (($t['status'] ?? '') === 'active'): ?>
                                <form method="post" action="<?= route_to('dashboard.learning.classes.revoke-trainer', $classId, $t['user_id']) ?>" class="d-inline" onsubmit="return confirm('Cabut penugasan trainer ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Cabut</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">Tidak aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div></div>
<?= $this->endSection() ?>
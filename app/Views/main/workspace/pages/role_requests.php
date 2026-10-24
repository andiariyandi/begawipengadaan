<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$myRequests     = $myRequests ?? [];
$availableRoles = $availableRoles ?? [];
$currentRoles   = $currentRoles ?? [];
$currentRoleIds = array_map('intval', array_column($currentRoles, 'id'));
?>

<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-user-shield"></i> Pengembangan Peran</span>
            <h1>Pengajuan Peran Tambahan</h1>
            <p>Ajukan peran fungsional sebagai Pengajar (Trainer) atau Operator PBJ beserta rekam jejak kompetensi Anda.</p>
        </div>
        <div>
            <a href="<?= route_to('workspace.profile.index') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Profil
            </a>
        </div>
    </div>
</div>

<div class="row g-3 g-md-4">
    <!-- Kolom Kiri: Form Pengajuan -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-3">
            <h2 class="h6 fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                <i class="fas fa-paper-plane text-primary me-2"></i> Formulir Pengajuan Peran
            </h2>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted d-block">Peran Aktif Anda Saat Ini:</label>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($currentRoles as $cr): ?>
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                            <i class="fas fa-check-circle me-1"></i> <?= esc($cr['name']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <form action="<?= route_to('workspace.profile.role-requests.store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Peran yang Diajukan <span class="text-danger">*</span></label>
                    <select name="requested_role_id" class="form-select rounded-3" required>
                        <option value="">-- Pilih Peran --</option>
                        <?php foreach ($availableRoles as $ar): ?>
                            <?php $owned = in_array((int) $ar['id'], $currentRoleIds, true); ?>
                            <option value="<?= (int) $ar['id'] ?>" <?= $owned ? 'disabled' : '' ?>>
                                <?= esc($ar['name']) ?><?= $owned ? ' (Sudah Aktif)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Alasan Pengajuan &amp; Kualifikasi Pendukung <span class="text-danger">*</span></label>
                    <textarea name="reason" rows="4" class="form-control rounded-3" placeholder="Tuliskan nomor SK / Sertifikat Kompetensi PBJ / pengalaman mengajar atau penugasan UKPBJ sebagai dasar pengajuan..." required><?= esc((string) old('reason')) ?></textarea>
                    <small class="text-muted d-block mt-1">Minimal 10 karakter. Tim Administrator akan meninjau permohonan Anda.</small>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                    <i class="fas fa-paper-plane me-1"></i> Kirim Permohonan Peran
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Riwayat Pengajuan -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <h2 class="h6 fw-bold mb-0" style="color: #082f5c;">
                    <i class="fas fa-history text-primary me-2"></i> Riwayat Permohonan Peran
                </h2>
                <span class="badge bg-light text-dark border"><?= count($myRequests) ?> Pengajuan</span>
            </div>

            <?php if (empty($myRequests)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fs-2 opacity-50 mb-2 d-block"></i>
                    <p class="small mb-0">Belum ada riwayat permohonan penambahan peran.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Peran Diminta</th>
                                <th>Alasan &amp; Catatan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myRequests as $req): ?>
                                <?php
                                $st = $req['status'] ?? 'submitted';
                                $badgeClass = match ($st) {
                                    'approved'          => 'bg-success-subtle text-success',
                                    'rejected'          => 'bg-danger-subtle text-danger',
                                    'revision_required' => 'bg-warning-subtle text-warning-emphasis',
                                    default             => 'bg-info-subtle text-info-emphasis',
                                };
                                $label = match ($st) {
                                    'approved'          => 'Disetujui',
                                    'rejected'          => 'Ditolak',
                                    'revision_required' => 'Perlu Revisi',
                                    'in_review'         => 'Ditinjau',
                                    default             => 'Diajukan',
                                };
                                ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($req['role_name'] ?? '-') ?></td>
                                    <td>
                                        <div><?= esc($req['reason'] ?? '-') ?></div>
                                        <?php if (! empty($req['review_notes'])): ?>
                                            <div class="mt-1 p-2 rounded bg-light border text-muted">
                                                <strong>Catatan Verifikator:</strong> <?= esc($req['review_notes']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $badgeClass ?> rounded-pill px-2 py-1"><?= esc($label) ?></span>
                                    </td>
                                    <td class="text-muted">
                                        <?= ! empty($req['created_at']) ? date('d M Y', strtotime($req['created_at'])) : '-' ?>
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
<?= $this->endSection() ?>

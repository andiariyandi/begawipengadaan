<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?php
$request = $request ?? [];
$payload = $payload ?? [];
$current = $current ?? [];
$history = $history ?? [];
$reqStatus = $request['status'] ?? 'submitted';
?>

<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-user-edit"></i> Verifikasi Perubahan Profil</span>
            <h1>Detail Permohonan #<?= esc($request['id'] ?? '-') ?></h1>
            <p>Tinjau dan bandingkan perubahan data profil yang diajukan oleh pengguna.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= route_to('workspace.operator.profile-change-requests') ?>" class="btn btn-outline-secondary radius-8 px-3 py-2 text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Antrean
            </a>
        </div>
    </div>
</div>

<div class="row g-3 g-md-4">
    <!-- Left Column: Data Comparison (8 cols) -->
    <div class="col-lg-8">
        <!-- Applicant Info Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                <i class="fas fa-id-card text-primary me-2"></i> Identitas Pemohon
            </h5>
            <div class="row g-3">
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Nama Lengkap</span>
                    <strong class="text-dark"><?= esc($request['user_name'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Email Terdaftar</span>
                    <strong class="text-dark"><?= esc($request['email'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Username</span>
                    <strong class="font-monospace text-dark"><?= esc($request['username'] ?? '-') ?></strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Tipe Profil</span>
                    <span class="badge bg-primary-subtle text-primary fw-semibold"><?= esc(strtoupper($request['profile_type'] ?? 'UMUM')) ?></span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Status Permohonan</span>
                    <?php
                    $badgeClass = match ($reqStatus) {
                        'approved'          => 'bg-success-subtle text-success',
                        'rejected'          => 'bg-danger-subtle text-danger',
                        'revision_required' => 'bg-warning text-dark',
                        default             => 'bg-warning-subtle text-warning',
                    };
                    $badgeLabel = match ($reqStatus) {
                        'approved'          => 'Disetujui',
                        'rejected'          => 'Ditolak',
                        'revision_required' => 'Perlu Revisi',
                        default             => 'Menunggu Persetujuan',
                    };
                    ?>
                    <span class="badge <?= $badgeClass ?> px-2 py-1"><?= $badgeLabel ?></span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Waktu Pengajuan</span>
                    <span class="text-dark"><?= date('d M Y, H:i', strtotime($request['created_at'])) ?> WIB</span>
                </div>
                <?php if (! empty($request['reason'])): ?>
                    <div class="col-12 mt-2">
                        <span class="text-muted small d-block">Alasan / Catatan Pengajuan Pemohon:</span>
                        <div class="p-2-5 bg-light rounded-3 border text-sm text-dark mt-1">
                            <?= esc($request['reason']) ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (! empty($request['review_notes'])): ?>
                    <div class="col-12 mt-2">
                        <span class="text-muted small d-block">Catatan Verifikator:</span>
                        <div class="p-2-5 bg-light rounded-3 border text-sm text-dark mt-1">
                            <?= esc($request['review_notes']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comparison Table Card (Visual Side-by-Side Diff Viewer) -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <?php
            $allKeys = array_unique(array_merge(array_keys($current), array_keys($payload)));
            $skipKeys = ['id', 'user_id', 'created_at', 'updated_at', 'deleted_at', 'password_hash', 'session_version', 'status_reason', 'last_login_at', 'email_verified_at'];
            $diffList = [];
            $changedCount = 0;

            foreach ($allKeys as $key) {
                if (in_array($key, $skipKeys, true)) continue;
                $oldVal = $current[$key] ?? null;
                $newVal = $payload[$key] ?? null;
                if (is_array($oldVal)) { $oldVal = implode(', ', $oldVal); }
                if (is_array($newVal)) { $newVal = implode(', ', $newVal); }
                $isChanged = array_key_exists($key, $payload) && ((string) ($oldVal ?? '') !== (string) ($newVal ?? ''));
                if ($isChanged) {
                    $changedCount++;
                }
                $diffList[] = [
                    'key'       => $key,
                    'old'       => $oldVal,
                    'new'       => $newVal,
                    'isChanged' => $isChanged,
                ];
            }
            ?>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1" style="color: #082f5c;">
                        <i class="fas fa-exchange-alt text-primary me-2"></i> Visual Side-by-Side Diff Viewer
                    </h5>
                    <p class="text-muted small mb-0">
                        Membandingkan data profil saat ini dengan usulan perubahan identitas yang diajukan pemohon.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 small fw-semibold">
                        <i class="fas fa-edit me-1"></i><?= $changedCount ?> Field Berubah
                    </span>
                    <div class="btn-group btn-group-sm rounded-pill p-1 bg-light border" role="group">
                        <button type="button" class="btn btn-sm btn-white rounded-pill px-3 fw-semibold active shadow-xs ws-diff-filter-btn" data-filter="all">
                            Semua (<?= count($diffList) ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-transparent rounded-pill px-3 fw-semibold ws-diff-filter-btn" data-filter="changed">
                            Berubah Saja (<?= $changedCount ?>)
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="wsDiffTable">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted" style="width: 25%;">Atribut Data</th>
                            <th class="small text-uppercase text-danger" style="width: 37%;">
                                <i class="fas fa-minus-circle me-1"></i> Data Saat Ini (Lama)
                            </th>
                            <th class="small text-uppercase text-success" style="width: 38%;">
                                <i class="fas fa-check-circle me-1"></i> Usulan Data Baru (Diajukan)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($diffList)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted small">Tidak ada data perubahan yang ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($diffList as $item): ?>
                                <tr class="ws-diff-row <?= $item['isChanged'] ? 'ws-diff-changed table-warning' : 'ws-diff-unchanged' ?>" data-changed="<?= $item['isChanged'] ? '1' : '0' ?>">
                                    <td class="fw-semibold small text-navy align-top pt-3">
                                        <?= esc(ucwords(str_replace('_', ' ', $item['key']))) ?>
                                        <?php if ($item['isChanged']): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-0-5 text-2xs d-block mt-1" style="width: fit-content;">
                                                <i class="fas fa-pen me-1"></i>Berubah
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted text-2xs d-block mt-1">Sama</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small align-top pt-3">
                                        <?php if ($item['isChanged']): ?>
                                            <div class="p-2-5 rounded-3 bg-danger-subtle text-danger border border-danger-subtle">
                                                <del class="d-block text-break">
                                                    <?= esc((string) ($item['old'] ?? '-') ?: '-') ?>
                                                </del>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted text-break"><?= esc((string) ($item['old'] ?? '-') ?: '-') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small align-top pt-3">
                                        <?php if ($item['isChanged']): ?>
                                            <div class="p-2-5 rounded-3 bg-success-subtle text-success border border-success-subtle fw-bold">
                                                <ins class="text-decoration-none d-block text-break">
                                                    <i class="fas fa-arrow-right me-1 text-primary"></i><?= esc((string) ($item['new'] ?? '-') ?: '-') ?>
                                                </ins>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted text-break"><?= esc((string) ($item['new'] ?? ($item['old'] ?? '-')) ?: '-') ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterBtns = document.querySelectorAll('.ws-diff-filter-btn');
                const rows = document.querySelectorAll('.ws-diff-row');

                filterBtns.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        filterBtns.forEach(function(b) {
                            b.classList.remove('active', 'btn-white', 'shadow-xs');
                            b.classList.add('btn-transparent');
                        });
                        this.classList.add('active', 'btn-white', 'shadow-xs');
                        this.classList.remove('btn-transparent');

                        const filter = this.getAttribute('data-filter');
                        rows.forEach(function(r) {
                            const isChg = r.getAttribute('data-changed') === '1';
                            if (filter === 'changed' && !isChg) {
                                r.style.display = 'none';
                            } else {
                                r.style.display = '';
                            }
                        });
                    });
                });
            });
            </script>
        </div>

        <!-- Workflow Transitions History -->
        <?php if (! empty($history)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                    <i class="fas fa-history text-secondary me-2"></i> Log Transisi Workflow
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr class="text-muted text-xs">
                                <th>Waktu</th>
                                <th>Tindakan</th>
                                <th>Transisi Status</th>
                                <th>Alasan / Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $h): ?>
                                <tr class="text-xs">
                                    <td><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                                    <td><code><?= esc($h['action']) ?></code></td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($h['from_state'] ?? '-') ?> &rarr; <?= esc($h['to_state'] ?? '-') ?></span></td>
                                    <td><?= esc($h['reason'] ?: '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Verification Actions (4 cols) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 90px;">
            <h5 class="fw-bold mb-3 pb-2 border-bottom" style="color: #082f5c;">
                <i class="fas fa-check-double text-primary me-2"></i> Tindakan Verifikasi
            </h5>

            <?php if (in_array($reqStatus, ['submitted', 'in_review', 'revision_required'], true)): ?>
                <!-- Approve Form -->
                <form action="<?= route_to('workspace.operator.profile-change-requests.approve', $request['id']) ?>" method="post" class="mb-3">
                    <?= csrf_field() ?>
                    <label class="form-label small fw-semibold text-muted">Catatan Persetujuan (Opsional)</label>
                    <textarea name="notes" class="form-control rounded-3 text-sm mb-2" rows="2" placeholder="Catatan persetujuan..."></textarea>
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold d-inline-flex align-items-center justify-content-center gap-2" onclick="return confirm('Setujui dan terapkan perubahan profil ini secara resmi ke akun pengguna?')">
                        <i class="fas fa-check-circle"></i>
                        <span>Setujui &amp; Terapkan</span>
                    </button>
                </form>

                <hr class="my-3">

                <!-- Request Revision Form -->
                <form action="<?= route_to('workspace.operator.profile-change-requests.revision', $request['id']) ?>" method="post" class="mb-3">
                    <?= csrf_field() ?>
                    <label class="form-label small fw-semibold text-muted">Catatan Permintaan Revisi <span class="text-danger">*</span></label>
                    <textarea name="revision_note" class="form-control rounded-3 text-sm mb-2" rows="2" placeholder="Jelaskan apa yang harus diperbaiki pemohon..." required></textarea>
                    <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-edit"></i>
                        <span>Minta Revisi Data</span>
                    </button>
                </form>

                <hr class="my-3">

                <!-- Reject Form -->
                <form action="<?= route_to('workspace.operator.profile-change-requests.reject', $request['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <label class="form-label small fw-semibold text-muted">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control rounded-3 text-sm mb-2" rows="2" placeholder="Alasan menolak permohonan..." required></textarea>
                    <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2" onclick="return confirm('Tolak permohonan perubahan profil ini?')">
                        <i class="fas fa-times-circle"></i>
                        <span>Tolak Permohonan</span>
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-light border rounded-3 text-center py-3">
                    <i class="fas fa-lock text-muted mb-2 d-block fa-lg"></i>
                    <span class="text-muted small">Status permohonan sudah final: <strong><?= strtoupper($reqStatus) ?></strong>.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

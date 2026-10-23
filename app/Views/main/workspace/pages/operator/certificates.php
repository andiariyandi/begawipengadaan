<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="operator-certificates-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-success text-white px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-award me-1"></i> Verifikasi Kelulusan
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Verifikasi & Penerbitan Sertifikat</h1>
                <p class="text-muted small mb-0">
                    Otorisasi penerbitan sertifikat bagi peserta yang telah menyelesaikan kurikulum dan dinyatakan memenuhi syarat kelulusan.
                </p>
            </div>
            <div>
                <a href="<?= route_to('workspace.operator.learning-monitoring') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-chart-line me-1"></i> Monitoring Kelas
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <!-- Summary Counters -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Menunggu Verifikasi</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= count($pendingQueue) ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Sertifikat Terbit</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= count(array_filter($issuedCertificates, static fn ($c) => $c['status'] === 'issued')) ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-danger-subtle text-danger fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-medium d-block">Sertifikat Dibatalkan</span>
                        <strong class="h4 fw-bold text-navy mb-0"><?= count(array_filter($issuedCertificates, static fn ($c) => $c['status'] === 'revoked')) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table 1: Antrean Peserta Memenuhi Syarat (Eligible) -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        <form id="wsBulkCertForm" method="POST" action="<?= route_to('workspace.operator.certificates.bulk-issue') ?>" class="d-none">
            <?= csrf_field() ?>
        </form>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-user-graduate text-warning me-2"></i>Antrean Peserta Memenuhi Syarat Sertifikasi</h2>
                    <small class="text-muted">Peserta yang telah lulus kurikulum dan siap diterbitkan sertifikatnya oleh Operator.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="wsCertCountBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 small d-none">
                        <i class="fas fa-check-square me-1"></i><span id="wsCertCount">0</span> peserta dipilih
                    </span>
                    <button type="button" id="wsBulkIssueCertBtn" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-semibold d-none">
                        <i class="fas fa-certificate me-1"></i> Terbitkan Terpilih
                    </button>
                    <span class="badge bg-warning text-dark px-3 py-2 small"><?= count($pendingQueue) ?> Menunggu Tindakan</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="wsCertSelectAll" class="form-check-input" title="Pilih Semua">
                            </th>
                            <th>Peserta</th>
                            <th>Kelas & Course</th>
                            <th>Nilai Akhir</th>
                            <th>Tanggal Eligible</th>
                            <th>Status Sertifikat</th>
                            <th class="text-end">Aksi Operator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingQueue)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada peserta dalam antrean verifikasi sertifikat saat ini.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pendingQueue as $item): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="enrollment_ids[]" value="<?= (int) $item['enrollment_id'] ?>" class="form-check-input ws-cert-check">
                                    </td>
                                    <td>
                                        <strong class="text-navy small d-block"><?= esc($item['user_name']) ?></strong>
                                        <span class="text-muted" style="font-size: 0.78rem;"><?= esc($item['user_email']) ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-navy small d-block"><?= esc($item['class_name']) ?></strong>
                                        <span class="text-muted" style="font-size: 0.78rem;"><?= esc($item['course_title'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-success"><?= $item['final_score'] !== null ? number_format((float) $item['final_score'], 1) : '-' ?></strong> / 100
                                    </td>
                                    <td class="small text-muted">
                                        <?= !empty($item['certificate_eligible_at']) ? date('d/m/Y H:i', strtotime($item['certificate_eligible_at'])) : '-' ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 small">
                                            Siap Diterbitkan
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <form method="POST" action="<?= route_to('workspace.operator.certificates.issue', (string) $item['enrollment_id']) ?>" class="d-inline" onsubmit="return confirm('Verifikasi kelulusan dan terbitkan sertifikat untuk peserta ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                                <i class="fas fa-check-circle me-1"></i> Verifikasi & Terbitkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <!-- Modal Konfirmasi Penerbitan Massal -->
        <div class="modal fade" id="wsCertConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom py-3">
                        <h5 class="modal-title fw-bold text-navy fs-6">
                            <i class="fas fa-certificate text-success me-2"></i>Konfirmasi Penerbitan Sertifikat Massal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="avatar-circle mx-auto mb-3 bg-success-subtle text-success fs-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="fas fa-award"></i>
                        </div>
                        <h6 class="fw-bold text-navy mb-2" id="wsCertModalTitle">Terbitkan Sertifikat untuk Peserta Terpilih?</h6>
                        <p class="text-muted small mb-0" id="wsCertModalDesc">
                            Sistem akan menerbitkan e-sertifikat resmi lengkap dengan nomor seri dan kode QR verifikasi untuk seluruh peserta yang dipilih.
                        </p>
                    </div>
                    <div class="modal-footer border-top p-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success rounded-pill px-4 fw-semibold" id="wsCertModalSubmitBtn">
                            Ya, Terbitkan Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('wsCertSelectAll');
            const checkboxes = document.querySelectorAll('.ws-cert-check');
            const countBadge = document.getElementById('wsCertCountBadge');
            const countNum = document.getElementById('wsCertCount');
            const bulkBtn = document.getElementById('wsBulkIssueCertBtn');
            const bulkForm = document.getElementById('wsBulkCertForm');
            const modalEl = document.getElementById('wsCertConfirmModal');
            const modalTitle = document.getElementById('wsCertModalTitle');
            const modalSubmitBtn = document.getElementById('wsCertModalSubmitBtn');

            function updateCertBulk() {
                let selected = 0;
                checkboxes.forEach(function(cb) { if (cb.checked) selected++; });

                if (countNum) countNum.textContent = selected;

                if (selected > 0) {
                    if (countBadge) countBadge.classList.remove('d-none');
                    if (bulkBtn) bulkBtn.classList.remove('d-none');
                } else {
                    if (countBadge) countBadge.classList.add('d-none');
                    if (bulkBtn) bulkBtn.classList.add('d-none');
                }

                if (selectAll && checkboxes.length > 0) {
                    selectAll.checked = (selected === checkboxes.length);
                    selectAll.indeterminate = (selected > 0 && selected < checkboxes.length);
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    const chk = this.checked;
                    checkboxes.forEach(function(cb) { cb.checked = chk; });
                    updateCertBulk();
                });
            }

            checkboxes.forEach(function(cb) {
                cb.addEventListener('change', updateCertBulk);
            });

            if (bulkBtn) {
                bulkBtn.addEventListener('click', function() {
                    let selected = 0;
                    checkboxes.forEach(function(cb) { if (cb.checked) selected++; });
                    if (selected === 0) return;

                    if (modalTitle) modalTitle.textContent = 'Terbitkan ' + selected + ' Sertifikat Sekaligus?';

                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal && modalEl) {
                        const bsModal = new bootstrap.Modal(modalEl);
                        bsModal.show();
                    } else if (confirm('Terbitkan sertifikat untuk ' + selected + ' peserta terpilih?')) {
                        submitCertBulk();
                    }
                });
            }

            function submitCertBulk() {
                if (!bulkForm) return;
                bulkForm.querySelectorAll('input[name="enrollment_ids[]"]').forEach(function(el) {
                    el.remove();
                });
                checkboxes.forEach(function(cb) {
                    if (cb.checked) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'enrollment_ids[]';
                        hidden.value = cb.value;
                        bulkForm.appendChild(hidden);
                    }
                });
                bulkForm.submit();
            }

            if (modalSubmitBtn && bulkForm) {
                modalSubmitBtn.addEventListener('click', function() {
                    modalSubmitBtn.disabled = true;
                    modalSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menerbitkan...';
                    submitCertBulk();
                });
            }
        });
        </script>
    </div>

    <!-- Table 2: Sertifikat yang Sudah Diterbitkan -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-navy mb-0"><i class="fas fa-award text-success me-2"></i>Daftar Sertifikat Terbit</h2>
            <span class="badge bg-light text-dark border px-3 py-2 small"><?= count($issuedCertificates) ?> Arsip Sertifikat</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th>Nomor Sertifikat</th>
                        <th>Penerima (Peserta)</th>
                        <th>Waktu Terbit</th>
                        <th>Diterbitkan Oleh</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($issuedCertificates)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada sertifikat yang diterbitkan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($issuedCertificates as $cert): ?>
                            <tr>
                                <td>
                                    <strong class="text-navy small font-monospace d-block"><?= esc($cert['certificate_number']) ?></strong>
                                    <span class="text-muted" style="font-size: 0.72rem;">Token: <?= substr(esc($cert['verification_token_hash']), 0, 16) ?>...</span>
                                </td>
                                <td>
                                    <strong class="text-navy small d-block"><?= esc($cert['user_name'] ?? 'Peserta') ?></strong>
                                    <span class="text-muted" style="font-size: 0.78rem;"><?= esc($cert['user_email'] ?? '') ?></span>
                                </td>
                                <td class="small text-muted">
                                    <?= !empty($cert['issued_at']) ? date('d/m/Y H:i', strtotime($cert['issued_at'])) : '-' ?>
                                </td>
                                <td class="small text-muted">
                                    <i class="fas fa-user-shield me-1 text-primary"></i> <?= esc($cert['issuer_name'] ?? 'Operator') ?>
                                </td>
                                <td>
                                    <?php if ($cert['status'] === 'issued'): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-1 small">Aktif / Terbit</span>
                                    <?php elseif ($cert['status'] === 'revoked'): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-1 small">Dibatalkan</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill px-3 py-1 small"><?= esc(ucfirst($cert['status'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($cert['status'] === 'issued'): ?>
                                        <form method="POST" action="<?= route_to('workspace.operator.certificates.revoke', (string) $cert['id']) ?>" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan (revoke) sertifikat ini?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="reason" value="Dibatalkan oleh Operator karena peninjauan ulang administratif.">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" style="font-size: 0.78rem;">
                                                <i class="fas fa-ban me-1"></i> Batalkan
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">Telah Dicabut</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$fmt = static fn(int $n): string => number_format($n, 0, ',', '.');
$flashMsg = session()->getFlashdata('message');
$flashErr = session()->getFlashdata('error');
?>

<!-- Alerts -->
<?php if ($flashMsg): ?>
    <div class="alert alert-success bg-success-50 text-success-600 border-success-200 px-24 py-11 mb-24 radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-12">
            <iconify-icon icon="solar:check-circle-bold" class="text-xl"></iconify-icon>
            <span class="text-md fw-medium"><?= esc($flashMsg) ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($flashErr): ?>
    <div class="alert alert-danger bg-danger-50 text-danger-600 border-danger-200 px-24 py-11 mb-24 radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-12">
            <iconify-icon icon="solar:danger-circle-bold" class="text-xl"></iconify-icon>
            <span class="text-md fw-medium"><?= esc($flashErr) ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Header & Nav -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Antrean Persetujuan Terpadu') ?></h6>
        <p class="text-secondary-light mb-0">Sentralisasi otorisasi penerbitan sertifikat, registrasi akun pengguna, dan perubahan hak akses sistem.</p>
    </div>
    <div class="d-flex flex-wrap gap-12">
        <a href="<?= site_url('dashboard/sistem/approval/riwayat') ?>" class="btn btn-outline-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-8">
            <iconify-icon icon="solar:history-bold" class="text-lg"></iconify-icon>
            <span>Riwayat Keputusan</span>
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxxl-4 row-cols-lg-2 row-cols-1 gy-4">
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-1 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Menunggu Persetujuan</p>
                        <h6 class="mb-0"><?= $fmt((int) ($kpis['pending'] ?? 0)) ?></h6>
                        <p class="text-sm text-primary-light mt-12 mb-0">Memerlukan tindakan segera</p>
                    </div>
                    <div class="w-50-px h-50-px bg-warning-focus text-warning-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:clock-circle-bold" class="text-2xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-2 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Disetujui Hari Ini</p>
                        <h6 class="mb-0"><?= $fmt((int) ($kpis['approved_today'] ?? 0)) ?></h6>
                        <p class="text-sm text-primary-light mt-12 mb-0">Otorisasi telah dieksekusi</p>
                    </div>
                    <div class="w-50-px h-50-px bg-success-focus text-success-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:check-circle-bold" class="text-2xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-3 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Ditolak Hari Ini</p>
                        <h6 class="mb-0"><?= $fmt((int) ($kpis['rejected_today'] ?? 0)) ?></h6>
                        <p class="text-sm text-primary-light mt-12 mb-0">Dengan alasan evaluasi</p>
                    </div>
                    <div class="w-50-px h-50-px bg-danger-focus text-danger-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:close-circle-bold" class="text-2xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-none border bg-gradient-start-5 h-100">
            <div class="card-body p-20">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="fw-medium text-primary-light mb-1">Melewati SLA (&gt;3 Hari)</p>
                        <h6 class="mb-0"><?= $fmt((int) ($kpis['sla_overdue'] ?? 0)) ?></h6>
                        <p class="text-sm text-primary-light mt-12 mb-0">Prioritas eskalasi administrasi</p>
                    </div>
                    <div class="w-50-px h-50-px bg-info-focus text-info-main rounded-circle d-flex justify-content-center align-items-center">
                        <iconify-icon icon="solar:danger-triangle-bold" class="text-2xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Queue Card -->
<div class="card mt-24">
    <div class="card-body p-24">
        <!-- Filter & Search Toolbar -->
        <form method="get" action="<?= site_url('dashboard/sistem/approval') ?>" class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <div>
                <h6 class="text-lg mb-4">Daftar Antrean Permohonan</h6>
                <p class="text-secondary-light text-sm mb-0">Menampilkan <?= count($items) ?> data pengajuan yang tersinkronisasi langsung dari database.</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-12">
                <select name="status" class="form-select form-select-sm w-auto radius-8" onchange="this.form.submit()">
                    <option value="pending" <?= ($statusParam ?? '') === 'pending' ? 'selected' : '' ?>>Menunggu Tindakan</option>
                    <option value="all" <?= ($statusParam ?? '') === 'all' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="approved" <?= ($statusParam ?? '') === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="rejected" <?= ($statusParam ?? '') === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                </select>

                <select name="type" class="form-select form-select-sm w-auto radius-8" onchange="this.form.submit()">
                    <option value="all" <?= ($typeParam ?? '') === 'all' ? 'selected' : '' ?>>Semua Kategori</option>
                    <option value="certificate" <?= ($typeParam ?? '') === 'certificate' ? 'selected' : '' ?>>Sertifikat Kelulusan</option>
                    <option value="profile_verification" <?= ($typeParam ?? '') === 'profile_verification' ? 'selected' : '' ?>>Verifikasi Profil</option>
                    <option value="user_registration" <?= ($typeParam ?? '') === 'user_registration' ? 'selected' : '' ?>>Registrasi Akun</option>
                    <option value="test_resource" <?= ($typeParam ?? '') === 'test_resource' ? 'selected' : '' ?>>Uji Integrasi Sistem</option>
                </select>

                <div class="input-group input-group-sm w-auto">
                    <input type="search" name="q" value="<?= esc($searchParam ?? '') ?>" class="form-control radius-8" placeholder="Cari pemohon/nomor...">
                    <button class="btn btn-primary-600 px-12" type="submit" aria-label="Cari" title="Cari">
                        <iconify-icon icon="solar:magnifer-outline"></iconify-icon>
                    </button>
                </div>

                <?php if (($statusParam ?? '') !== 'pending' || ($typeParam ?? '') !== 'all' || ! empty($searchParam)): ?>
                    <a href="<?= site_url('dashboard/sistem/approval') ?>" class="btn btn-sm btn-outline-neutral-300 radius-8 px-12 py-6 d-inline-flex align-items-center gap-4" title="Reset Filter">
                        <iconify-icon icon="solar:restart-bold"></iconify-icon> Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Bulk Action Form Container -->
        <form method="post" action="<?= site_url('dashboard/sistem/approval/bulk') ?>" id="bulkApprovalForm">
            <?= csrf_field() ?>
            <input type="hidden" name="bulk_action" id="bulkActionInput" value="">
            <input type="hidden" name="notes" id="bulkNotesInput" value="">

            <div class="d-none align-items-center justify-content-between p-12 mb-16 bg-primary-50 border border-primary-200 radius-8" id="bulkActionToolbar">
                <div class="d-flex align-items-center gap-12">
                    <span class="text-sm fw-semibold text-primary-600"><span id="selectedCount">0</span> permohonan terpilih</span>
                </div>
                <div class="d-flex align-items-center gap-8">
                    <button type="button" class="btn btn-sm btn-success-main text-white radius-8 px-16 py-6 d-inline-flex align-items-center gap-6" onclick="triggerBulkAction('approve')">
                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Setujui Terpilih
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger-600 radius-8 px-16 py-6 d-inline-flex align-items-center gap-6" onclick="triggerBulkAction('reject')">
                        <iconify-icon icon="solar:close-circle-bold"></iconify-icon> Tolak Terpilih
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Pilih Semua">
                            </th>
                            <th style="width: 70px;">ID</th>
                            <th>Jenis &amp; Objek Permintaan</th>
                            <th>Pemohon</th>
                            <th>Waktu Pengajuan</th>
                            <th>Prioritas</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-48">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <span class="w-64-px h-64-px bg-neutral-100 text-secondary-light rounded-circle d-inline-flex align-items-center justify-content-center mb-16">
                                            <iconify-icon icon="solar:inbox-line-bold" class="text-3xl"></iconify-icon>
                                        </span>
                                        <h6 class="text-md fw-semibold mb-4">Tidak Ada Antrean Persetujuan</h6>
                                        <p class="text-secondary-light text-sm mb-0">Seluruh permohonan telah diproses atau tidak ada kriteria yang sesuai filter.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $app): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if ($app['status'] === 'pending'): ?>
                                            <input type="checkbox" name="selected_ids[]" value="<?= (int) $app['id'] ?>" class="form-check-input approval-item-check">
                                        <?php else: ?>
                                            <span class="text-secondary-light">&ndash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('dashboard/sistem/approval/' . $app['id']) ?>" class="text-primary-600 fw-bold">
                                            #<?= esc((string) $app['id']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-12">
                                            <span class="w-36-px h-36-px radius-8 d-inline-flex align-items-center justify-content-center <?= ($app['resource_type'] === 'certificate') ? 'bg-primary-50 text-primary-600' : 'bg-info-focus text-info-main' ?>">
                                                <iconify-icon icon="<?= ($app['resource_type'] === 'certificate') ? 'solar:diploma-verified-bold' : 'solar:user-id-bold' ?>" class="text-lg"></iconify-icon>
                                            </span>
                                            <div>
                                                <h6 class="text-sm fw-semibold mb-2">
                                                    <a href="<?= site_url('dashboard/sistem/approval/' . $app['id']) ?>" class="text-primary-light">
                                                        <?= esc($app['type_label']) ?>
                                                    </a>
                                                </h6>
                                                <span class="text-xs text-secondary-light">
                                                    Aksi: <code><?= esc($app['requested_action'] ?: 'review') ?></code>
                                                    &bull; ID: #<?= esc((string) $app['resource_id']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-sm fw-medium text-primary-light"><?= esc($app['requester_label']) ?></span>
                                            <span class="text-xs text-secondary-light"><?= esc($app['requested_by_email'] ?: '-') ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-sm text-primary-light"><?= esc($app['formatted_date']) ?></span>
                                            <span class="text-xs text-secondary-light"><?= esc($app['relative_time']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?= view('dashboard/components/status_badge', [
                                            'status' => $app['priority_status'],
                                            'label'  => $app['priority'],
                                        ]) ?>
                                    </td>
                                    <td class="text-center">
                                        <?= view('dashboard/components/status_badge', [
                                            'status' => $app['status'],
                                        ]) ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-8">
                                            <a href="<?= site_url('dashboard/sistem/approval/' . $app['id']) ?>" class="btn btn-sm btn-outline-primary-600 radius-6 px-10 py-4" title="Tinjau Detail">
                                                Tinjau
                                            </a>
                                            <?php if ($app['status'] === 'pending'): ?>
                                                <button type="button" class="btn btn-sm btn-icon btn-success-main text-white radius-6 w-28-px h-28-px d-inline-flex align-items-center justify-content-center" title="Setujui Cepat" onclick="openQuickApprove(<?= (int) $app['id'] ?>, '<?= esc($app['type_label'], 'js') ?>')">
                                                    <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger-600 radius-6 w-28-px h-28-px d-inline-flex align-items-center justify-content-center" title="Tolak Pengajuan" onclick="openQuickReject(<?= (int) $app['id'] ?>, '<?= esc($app['type_label'], 'js') ?>')">
                                                    <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<!-- Modal Quick Approve -->
<div class="modal fade" id="quickApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" id="quickApproveForm" action="" class="modal-content radius-16 border-0">
            <?= csrf_field() ?>
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title text-md fw-semibold d-flex align-items-center gap-8">
                    <iconify-icon icon="solar:check-circle-bold" class="text-success-main text-xl"></iconify-icon>
                    Konfirmasi Persetujuan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <p class="text-secondary-light text-sm mb-16">
                    Anda akan menyetujui permohonan <strong id="approveModalType" class="text-primary-light"></strong> (<span id="approveModalId" class="fw-semibold"></span>).
                </p>
                <div class="mb-0">
                    <label class="form-label fw-medium text-sm text-primary-light mb-8">Catatan Persetujuan (Opsional)</label>
                    <textarea name="notes" class="form-control radius-8" rows="3" placeholder="Masukkan catatan atau instruksi tambahan..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-16 px-24">
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success-main text-white radius-8 px-20 py-8 d-inline-flex align-items-center gap-6">
                    <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Ya, Setujui
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Quick Reject -->
<div class="modal fade" id="quickRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" id="quickRejectForm" action="" class="modal-content radius-16 border-0">
            <?= csrf_field() ?>
            <div class="modal-header border-bottom py-16 px-24">
                <h6 class="modal-title text-md fw-semibold d-flex align-items-center gap-8">
                    <iconify-icon icon="solar:danger-circle-bold" class="text-danger-main text-xl"></iconify-icon>
                    Konfirmasi Penolakan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <p class="text-secondary-light text-sm mb-16">
                    Permohonan <strong id="rejectModalType" class="text-primary-light"></strong> (<span id="rejectModalId" class="fw-semibold"></span>) akan ditolak.
                </p>
                <div class="mb-0">
                    <label class="form-label fw-medium text-sm text-primary-light mb-8">Alasan Penolakan <span class="text-danger-main">*</span></label>
                    <textarea name="reason" class="form-control radius-8" rows="3" required placeholder="Jelaskan alasan penolakan agar pemohon dapat menindaklanjuti..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-16 px-24">
                <button type="button" class="btn btn-outline-neutral-300 radius-8 px-16 py-8" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger-600 radius-8 px-20 py-8 d-inline-flex align-items-center gap-6">
                    <iconify-icon icon="solar:close-circle-bold"></iconify-icon> Tolak Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllCheckbox');
    const itemChecks = document.querySelectorAll('.approval-item-check');
    const toolbar = document.getElementById('bulkActionToolbar');
    const countSpan = document.getElementById('selectedCount');

    function updateToolbar() {
        const checked = document.querySelectorAll('.approval-item-check:checked');
        const count = checked.length;
        if (countSpan) countSpan.textContent = count;
        if (toolbar) {
            if (count > 0) {
                toolbar.classList.remove('d-none');
                toolbar.classList.add('d-flex');
            } else {
                toolbar.classList.remove('d-flex');
                toolbar.classList.add('d-none');
            }
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            itemChecks.forEach(cb => { cb.checked = selectAll.checked; });
            updateToolbar();
        });
    }

    itemChecks.forEach(cb => {
        cb.addEventListener('change', function () {
            const allChecked = Array.from(itemChecks).every(c => c.checked);
            if (selectAll) selectAll.checked = allChecked;
            updateToolbar();
        });
    });
});

function openQuickApprove(id, typeLabel) {
    const form = document.getElementById('quickApproveForm');
    form.action = '<?= site_url('dashboard/sistem/approval') ?>/' + id + '/setujui';
    document.getElementById('approveModalId').textContent = '#' + id;
    document.getElementById('approveModalType').textContent = typeLabel;
    new bootstrap.Modal(document.getElementById('quickApproveModal')).show();
}

function openQuickReject(id, typeLabel) {
    const form = document.getElementById('quickRejectForm');
    form.action = '<?= site_url('dashboard/sistem/approval') ?>/' + id + '/tolak';
    document.getElementById('rejectModalId').textContent = '#' + id;
    document.getElementById('rejectModalType').textContent = typeLabel;
    new bootstrap.Modal(document.getElementById('quickRejectModal')).show();
}

function triggerBulkAction(action) {
    const count = document.querySelectorAll('.approval-item-check:checked').length;
    if (count === 0) {
        alert('Pilih setidaknya satu permohonan.');
        return;
    }
    const verb = action === 'approve' ? 'menyetujui' : 'menolak';
    const note = prompt(`Konfirmasi ${verb} ${count} permohonan terpilih. Masukkan catatan (opsional):`, action === 'approve' ? 'Disetujui secara massal.' : 'Ditolak secara massal.');
    if (note === null) return; // user cancelled

    document.getElementById('bulkActionInput').value = action;
    document.getElementById('bulkNotesInput').value = note;
    document.getElementById('bulkApprovalForm').submit();
}
</script>
<?= $this->endSection() ?>

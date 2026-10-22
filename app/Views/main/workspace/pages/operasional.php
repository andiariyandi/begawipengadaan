<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>

<div class="ws-page-header">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="ws-eyebrow"><i class="fas fa-cogs"></i> <?= esc($sectionContent['eyebrow'] ?? 'Operator Workspace') ?></span>
            <h1><?= esc($sectionContent['title'] ?? 'Operasional') ?></h1>
            <p><?= esc($sectionContent['description'] ?? 'Pantau verifikasi operasional, enrollment, kelas, approval, dan laporan.') ?></p>
        </div>
    </div>
</div>

<?php if (! empty($sectionContent['cards'])): ?>
    <div class="row g-3 mb-4">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card ws-stat-card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <span class="text-muted small text-uppercase fw-semibold"><?= esc($card['title']) ?></span>
                    <div class="display-6 fw-bold my-2" style="color: #082f5c;"><?= esc($card['value']) ?></div>
                    <span class="text-secondary small"><?= esc($card['meta'] ?? '') ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (! empty($sectionContent['table'])): ?>
    <?php $hasBulk = ! empty($sectionContent['bulkConfig']); ?>
    <?php if ($hasBulk): ?>
        <form id="wsBulkActionForm" method="POST" action="<?= esc($sectionContent['bulkConfig']['url']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="bulk_action" id="wsBulkActionInput" value="">
    <?php endif; ?>

    <div class="card border rounded shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <h5 class="card-title mb-0 fs-6 fw-bold"><?= esc($sectionContent['tableTitle'] ?? 'Data Operasional') ?></h5>
                <span class="badge bg-light text-dark border"><?= count($sectionContent['table']['rows'] ?? []) ?> Data Ditampilkan</span>
            </div>
            <?php if ($hasBulk): ?>
                <div class="d-flex align-items-center gap-2">
                    <span id="wsBulkCountBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 small d-none">
                        <i class="fas fa-check-square me-1"></i><span id="wsBulkCountNum">0</span> dipilih
                    </span>
                    <?php foreach ($sectionContent['bulkConfig']['actions'] as $bAct): ?>
                        <button type="button" class="btn btn-sm <?= esc($bAct['class'] ?? 'btn-primary') ?> rounded-pill px-3 fw-semibold ws-bulk-trigger-btn d-none" data-action="<?= esc($bAct['action']) ?>" data-label="<?= esc($bAct['label']) ?>">
                            <i class="<?= esc($bAct['icon'] ?? 'fas fa-cog') ?> me-1"></i><?= esc($bAct['label']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <?php foreach ($sectionContent['table']['headers'] as $header): ?>
                            <th class="small text-uppercase text-muted"><?= $header ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sectionContent['table']['rows'])): ?>
                        <tr>
                            <td colspan="<?= count($sectionContent['table']['headers']) ?>" class="text-center py-4 text-muted">
                                Belum ada antrean data pada kategori ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sectionContent['table']['rows'] as $row): ?>
                            <tr>
                                <?php foreach ($row as $cell): ?>
                                    <td><?= $cell ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($hasBulk): ?>
        </form>

        <!-- Modal Konfirmasi Tindakan Massal -->
        <div class="modal fade" id="wsBulkConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom py-3">
                        <h5 class="modal-title fw-bold text-navy fs-6">
                            <i class="fas fa-tasks text-primary me-2"></i>Konfirmasi Tindakan Massal
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="avatar-circle mx-auto mb-3 bg-primary-subtle text-primary fs-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="fas fa-question"></i>
                        </div>
                        <h6 class="fw-bold text-navy mb-2" id="wsBulkModalTitle">Proses Data Terpilih?</h6>
                        <p class="text-muted small mb-0" id="wsBulkModalDesc">
                            Anda akan memproses data yang telah dipilih sekaligus. Tindakan ini akan dicatat ke dalam audit log sistem.
                        </p>
                    </div>
                    <div class="modal-footer border-top p-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" id="wsBulkModalSubmitBtn">
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('wsBulkSelectAll');
            const checkboxes = document.querySelectorAll('.ws-bulk-check');
            const countBadge = document.getElementById('wsBulkCountBadge');
            const countNum = document.getElementById('wsBulkCountNum');
            const triggerBtns = document.querySelectorAll('.ws-bulk-trigger-btn');
            const bulkForm = document.getElementById('wsBulkActionForm');
            const actionInput = document.getElementById('wsBulkActionInput');
            const modalEl = document.getElementById('wsBulkConfirmModal');
            const modalTitle = document.getElementById('wsBulkModalTitle');
            const modalDesc = document.getElementById('wsBulkModalDesc');
            const modalSubmitBtn = document.getElementById('wsBulkModalSubmitBtn');

            function updateBulkToolbar() {
                let selected = 0;
                checkboxes.forEach(function(cb) {
                    if (cb.checked) selected++;
                });

                if (countNum) countNum.textContent = selected;

                if (selected > 0) {
                    if (countBadge) countBadge.classList.remove('d-none');
                    triggerBtns.forEach(function(b) { b.classList.remove('d-none'); });
                } else {
                    if (countBadge) countBadge.classList.add('d-none');
                    triggerBtns.forEach(function(b) { b.classList.add('d-none'); });
                }

                if (selectAll && checkboxes.length > 0) {
                    selectAll.checked = (selected === checkboxes.length);
                    selectAll.indeterminate = (selected > 0 && selected < checkboxes.length);
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    const isChecked = this.checked;
                    checkboxes.forEach(function(cb) {
                        cb.checked = isChecked;
                    });
                    updateBulkToolbar();
                });
            }

            checkboxes.forEach(function(cb) {
                cb.addEventListener('change', updateBulkToolbar);
            });

            triggerBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const act = this.getAttribute('data-action');
                    const lbl = this.getAttribute('data-label');
                    let selected = 0;
                    checkboxes.forEach(function(cb) { if (cb.checked) selected++; });

                    if (selected === 0) return;

                    if (actionInput) actionInput.value = act;
                    if (modalTitle) modalTitle.textContent = lbl + ' (' + selected + ' Data)?';
                    if (modalDesc) modalDesc.textContent = 'Apakah Anda yakin ingin memproses ' + selected + ' data terpilih sekaligus? Tindakan ini akan langsung diterapkan.';

                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal && modalEl) {
                        const bsModal = new bootstrap.Modal(modalEl);
                        bsModal.show();
                    } else if (confirm('Apakah Anda yakin ingin memproses ' + selected + ' data terpilih?')) {
                        if (bulkForm) bulkForm.submit();
                    }
                });
            });

            if (modalSubmitBtn && bulkForm) {
                modalSubmitBtn.addEventListener('click', function() {
                    modalSubmitBtn.disabled = true;
                    modalSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
                    bulkForm.submit();
                });
            }
        });
        </script>
    <?php endif; ?>
<?php endif; ?>

<?php if (! empty($sectionContent['items'])): ?>
    <div class="card border rounded shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="card-title mb-0 fs-6 fw-bold">Navigasi Cepat & Modul Terkait</h5>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                <?php foreach ($sectionContent['items'] as $item): ?>
                    <div class="list-group-item px-0 py-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <?php if (is_array($item)): ?>
                            <span class="fw-medium text-dark"><?= esc($item['label']) ?></span>
                            <?php if (! empty($item['url'])): ?>
                                <a href="<?= esc($item['url']) ?>" class="btn btn-sm btn-outline-primary"><?= esc($item['btn'] ?? 'Buka') ?></a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-dark"><?= esc($item) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

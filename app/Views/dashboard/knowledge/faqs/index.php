<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$items       = $items ?? [];
$categories  = $categories ?? [];
$status      = $filterStatus ?? null;
$keyword     = $filterKeyword ?? null;
$category    = $filterCategory !== null ? (string) $filterCategory : '';
$stats       = $stats ?? ['total' => count($items), 'published' => 0, 'draft' => 0, 'in_review' => 0];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Tanya Jawab (FAQ)</h6>
        <p class="text-secondary-light mb-0">Kelola basis tanya-jawab seputar regulasi dan pengadaan barang/jasa.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.faqs.create') ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
            Tambah FAQ
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Total FAQ</span>
                    <h5 class="fw-semibold mb-0"><?= number_format($stats['total'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:question-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Dipublikasikan</span>
                    <h5 class="fw-semibold text-success-main mb-0"><?= number_format($stats['published'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-success-50 text-success-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Dalam Peninjauan</span>
                    <h5 class="fw-semibold text-warning-main mb-0"><?= number_format($stats['in_review'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-warning-50 text-warning-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:clock-circle-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card p-20 radius-12 border bg-base shadow-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary-light text-sm mb-1 d-block">Draft Tersimpan</span>
                    <h5 class="fw-semibold text-neutral-600 mb-0"><?= number_format($stats['draft'] ?? 0) ?></h5>
                </div>
                <div class="w-48-px h-48-px bg-neutral-100 text-neutral-600 rounded-circle d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="solar:pen-new-square-outline"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card radius-12 mb-24 border">
    <div class="card-body p-20">
        <form method="get" action="<?= route_to('dashboard.knowledge.faqs') ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-sm fw-medium mb-1">Pencarian Pertanyaan</label>
                    <div class="position-relative">
                        <input type="text" name="q" class="form-control radius-8 ps-40" value="<?= esc($keyword ?? '') ?>" placeholder="Cari pertanyaan FAQ...">
                        <iconify-icon icon="solar:magnifer-linear" class="position-absolute top-50 start-0 translate-middle-y ms-16 text-secondary-light text-lg"></iconify-icon>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Status</label>
                    <select name="status" class="form-select radius-8">
                        <option value="">Semua Status</option>
                        <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Dipublikasikan</option>
                        <option value="in_review" <?= $status === 'in_review' ? 'selected' : '' ?>>Ditinjau</option>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Diarsipkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-medium mb-1">Kategori</label>
                    <select name="category" class="form-select radius-8">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= ($category === (string) $c['id']) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                        <iconify-icon icon="solar:filter-outline"></iconify-icon> Filter
                    </button>
                    <a href="<?= route_to('dashboard.knowledge.faqs') ?>" class="btn btn-outline-neutral-300 radius-8" title="Reset">
                        <iconify-icon icon="solar:restart-outline"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- FAQ Accordion List -->
<?php if (empty($items)): ?>
    <?= view('dashboard/components/empty_state', [
        'icon'        => 'solar:question-circle-outline',
        'title'       => 'Belum Ada FAQ',
        'message'     => 'Belum ada daftar tanya jawab yang tersedia sesuai filter yang dipilih.',
        'actionUrl'   => route_to('dashboard.knowledge.faqs.create'),
        'actionText'  => 'Tambah FAQ Baru',
    ]) ?>
<?php else: ?>
    <div class="card radius-12 border">
        <div class="card-body p-24">
            <div class="accordion" id="faqAccordion">
                <?php foreach ($items as $index => $faq): ?>
                    <?php
                        $collapseId = 'faqCollapse' . (int) $faq['id'];
                        $statusKey = $faq['workflow_status'] ?? 'draft';
                    ?>
                    <div class="accordion-item border radius-8 mb-16 overflow-hidden">
                        <h2 class="accordion-header" id="heading<?= (int) $faq['id'] ?>">
                            <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?> text-primary-light fw-medium px-20 py-16" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="<?= $collapseId ?>">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 w-100 me-16">
                                    <span class="text-md fw-semibold text-neutral-800"><?= esc($faq['title']) ?></span>
                                    <div>
                                        <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="<?= $collapseId ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-20 py-16 border-top bg-neutral-50">
                                <div class="text-secondary-light text-sm mb-16">
                                    <?= nl2br(esc($faq['content'] ?? '(Tidak ada isi jawaban)')) ?>
                                </div>
                                <?php if (! empty($faq['legal_basis'])): ?>
                                    <div class="p-12 radius-6 bg-primary-50 border border-primary-100 text-xs text-primary-800 mb-12">
                                        <i class="fas fa-book-reader me-1 text-primary"></i> <strong>Dasar Hukum:</strong> <?= esc($faq['legal_basis']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex align-items-center justify-content-between pt-12 border-top">
                                    <span class="text-xs text-secondary-light">
                                        <iconify-icon icon="solar:calendar-date-outline" class="me-1"></iconify-icon>
                                        Diperbarui: <?= esc(date('d M Y, H:i', strtotime($faq['updated_at'] ?? 'now'))) ?>
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="<?= route_to('dashboard.knowledge.faqs.detail', (string) $faq['id']) ?>" class="btn btn-outline-primary-600 btn-sm radius-6 px-12 py-4 text-xs d-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:eye-outline"></iconify-icon> Detail
                                        </a>
                                        <a href="<?= route_to('dashboard.knowledge.faqs.edit', (string) $faq['id']) ?>" class="btn btn-outline-warning-main btn-sm radius-6 px-12 py-4 text-xs d-flex align-items-center gap-1">
                                            <iconify-icon icon="solar:pen-2-outline"></iconify-icon> Ubah
                                        </a>
                                        <button type="button" class="btn btn-outline-danger-600 btn-sm radius-6 px-12 py-4 text-xs d-flex align-items-center gap-1"
                                            data-bs-toggle="modal" data-bs-target="#confirmationActionModal"
                                            data-action="<?= route_to('dashboard.knowledge.faqs.delete', (string) $faq['id']) ?>"
                                            data-method="POST"
                                            data-title="Hapus FAQ"
                                            data-message="Apakah Anda yakin ingin mengarsipkan atau menghapus FAQ ini?"
                                            data-btn-text="Hapus FAQ"
                                            data-btn-class="btn-danger-600"
                                            data-icon="solar:trash-bin-trash-outline"
                                            data-icon-color="danger">
                                            <iconify-icon icon="solar:trash-bin-trash-outline"></iconify-icon> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
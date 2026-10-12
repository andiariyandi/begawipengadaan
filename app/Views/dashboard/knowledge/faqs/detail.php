<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$faq = $faq ?? [];
$statusKey = $faq['workflow_status'] ?? 'draft';
$cats = array_column($faq['categories'] ?? [], 'name');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Detail Tanya Jawab (FAQ)</h6>
        <p class="text-secondary-light mb-0">ID FAQ: #<?= (int) ($faqId ?? 0) ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= route_to('dashboard.knowledge.faqs.edit', (string) ($faqId ?? '')) ?>" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:pen-2-outline" class="text-lg"></iconify-icon>
            Ubah FAQ
        </a>
        <a href="<?= route_to('dashboard.knowledge.faqs') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
            <iconify-icon icon="solar:arrow-left-outline" class="text-lg"></iconify-icon>
            Kembali
        </a>
    </div>
</div>

<div class="card radius-16 border mb-24 bg-white shadow-sm">
    <div class="card-body p-24">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-20">
            <div>
                <span class="badge bg-primary-50 text-primary-600 radius-6 px-10 py-5 text-xs mb-2">Tanya Jawab Resmi PBJ</span>
                <h4 class="fw-bold mb-2 text-primary-900"><?= esc($faq['title'] ?? '-') ?></h4>
                <div class="d-flex flex-wrap align-items-center gap-3 text-secondary-light text-sm">
                    <span><iconify-icon icon="solar:sort-vertical-bold" class="me-1"></iconify-icon>Prioritas Tampil: <strong><?= (int) ($faq['sort_order'] ?? 0) ?></strong></span>
                    <span>•</span>
                    <span><iconify-icon icon="solar:calendar-date-outline" class="me-1"></iconify-icon>Diperbarui: <?= esc(date('d M Y, H:i', strtotime($faq['updated_at'] ?? 'now'))) ?></span>
                </div>
            </div>
            <div>
                <?= view('dashboard/components/status_badge', ['status' => $statusKey]) ?>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 pt-16 border-top mb-20">
            <span class="text-secondary-light text-sm me-2">Kategori:</span>
            <?php if (!empty($cats)): ?>
                <?php foreach ($cats as $cat): ?>
                    <span class="badge bg-neutral-100 text-neutral-800 radius-6 px-12 py-6"><?= esc($cat) ?></span>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="text-secondary-light text-sm">Umum</span>
            <?php endif; ?>
        </div>

        <?php if (! empty($faq['summary'])): ?>
            <div class="p-16 radius-12 bg-neutral-50 border border-neutral-200 mb-20">
                <span class="fw-bold text-neutral-900 d-block mb-1 text-sm">Intisari Jawaban Singkat:</span>
                <p class="text-secondary-light mb-0 text-sm" style="line-height: 1.6;"><?= nl2br(esc($faq['summary'])) ?></p>
            </div>
        <?php endif; ?>

        <div class="mb-20">
            <h6 class="text-sm fw-bold text-primary-900 mb-8">Penjelasan Jawaban Lengkap:</h6>
            <div class="p-20 radius-12 border bg-base text-neutral-800" style="line-height: 1.7; font-size: 14.5px;">
                <?= nl2br(esc($faq['content'] ?? '(Belum ada jawaban tersimpan)')) ?>
            </div>
        </div>

        <?php if (! empty($faq['legal_basis'])): ?>
            <div class="p-16 radius-12 bg-primary-50 border border-primary-200 text-primary-900">
                <div class="d-flex align-items-start gap-2">
                    <iconify-icon icon="solar:book-bookmark-bold" class="text-primary-700 text-xl flex-shrink-0 mt-1"></iconify-icon>
                    <div>
                        <strong class="d-block text-sm mb-1">Dasar Hukum &amp; Referensi Regulasi:</strong>
                        <span class="text-sm" style="line-height: 1.5;"><?= nl2br(esc($faq['legal_basis'])) ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$feedbacks = $feedbacks ?? [];
$stats = $stats ?? [
    'total'     => count($feedbacks),
    'avgRating' => 0,
];
$filters = $filters ?? [];
?>

<!-- Header & Top Stats -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Feedback Peserta</h6>
        <p class="text-secondary-light mb-0">Tinjau ulasan, skor kepuasan, dan umpan balik materi dari peserta pembelajaran.</p>
    </div>
    <div class="d-flex align-items-center gap-3 bg-base border radius-8 px-20 py-12">
        <div class="text-warning-main text-2xl d-flex align-items-center">
            <iconify-icon icon="solar:star-bold"></iconify-icon>
        </div>
        <div>
            <div class="fw-bold text-lg mb-0 text-primary-light"><?= number_format($stats['avgRating'] ?? 0, 1) ?> <span class="text-xs fw-normal text-secondary-light">/ 5.0</span></div>
            <span class="text-xs text-secondary-light">Rata-rata dari <?= number_format($stats['total'] ?? 0) ?> ulasan</span>
        </div>
    </div>
</div>

<!-- Search Filter -->
<div class="card mb-24">
    <div class="card-body p-20">
        <form action="<?= site_url('dashboard/pembelajaran/feedback-peserta') ?>" method="get">
            <div class="row g-12 align-items-end">
                <div class="col-md-9">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pencarian</label>
                    <input type="text" class="form-control radius-8" name="keyword"
                           value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Cari nama peserta, kelas, atau isi komentar…">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:magnifer-outline" class="text-lg"></iconify-icon>
                        <span>Cari</span>
                    </button>
                    <a href="<?= site_url('dashboard/pembelajaran/feedback-peserta') ?>" class="btn btn-outline-neutral-300 radius-8 px-16 py-11 d-inline-flex align-items-center justify-content-center" title="Reset">
                        <iconify-icon icon="solar:restart-outline" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cards / Empty State -->
<?php if (empty($feedbacks)): ?>
    <div class="card">
        <div class="card-body p-24">
            <?= view('dashboard/components/empty_state', [
                'title'   => 'Belum Ada Feedback',
                'message' => 'Belum ada umpan balik yang dikirimkan oleh peserta kelas.',
                'icon'    => 'solar:chat-round-line-outline',
            ]) ?>
        </div>
    </div>
<?php else: ?>
    <div class="row gy-4">
        <?php foreach ($feedbacks as $fb): ?>
            <div class="col-xl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body p-24 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-16">
                                <div>
                                    <h6 class="text-md mb-2 fw-medium text-primary-light"><?= esc($fb['student_name'] ?? $fb['username'] ?? 'Peserta') ?></h6>
                                    <span class="text-xs text-secondary-light"><?= esc($fb['class_name'] ?? '-') ?></span>
                                </div>
                                <div class="badge bg-warning-focus text-warning-main px-10 py-4 rounded-pill fw-bold text-xs d-inline-flex align-items-center gap-1">
                                    <iconify-icon icon="solar:star-bold"></iconify-icon>
                                    <span><?= esc((string) ($fb['rating'] ?? 5)) ?>/5</span>
                                </div>
                            </div>
                            <p class="text-secondary-light text-sm mb-16 italic">
                                "<?= esc($fb['comment'] ?? 'Tidak ada komentar tertulis.') ?>"
                            </p>
                        </div>
                        <div class="pt-12 border-top text-xs text-secondary-light">
                            <?= ! empty($fb['created_at']) ? esc(date('d M Y H:i', strtotime($fb['created_at']))) : '-' ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

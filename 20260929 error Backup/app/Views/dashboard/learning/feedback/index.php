<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Feedback Peserta</h6>
        <p class="text-secondary-light mb-0">Tinjau umpan balik dari peserta pembelajaran.</p>
    </div>
</div><?= $this->include('dashboard/components/filter_bar') ?><div class="row gy-4"><?php foreach ([['name' => 'Rina Oktaviani', 'class' => 'PBJ Dasar Batch 1', 'rating' => '5', 'feedback' => 'Materi sangat relevan dan mudah dipahami. Contoh kasus membantu proses belajar.'], ['name' => 'Budi Santoso', 'class' => 'PBJ Dasar Batch 1', 'rating' => '4', 'feedback' => 'Materi sudah baik, namun saya berharap ada lebih banyak sesi diskusi langsung.'], ['name' => 'Siti Nurhaliza', 'class' => 'Manajemen Kontrak Batch 2', 'rating' => '5', 'feedback' => 'Trainer sangat komunikatif dan struktur modul sangat jelas.']] as $feedback): ?><div class="col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex justify-content-between align-items-start mb-16">
                        <div>
                            <h6 class="text-md mb-4"><?= esc($feedback['name']) ?></h6><span class="text-sm text-secondary-light"><?= esc($feedback['class']) ?></span>
                        </div><span class="text-warning-main"><iconify-icon icon="solar:star-bold"></iconify-icon> <?= esc($feedback['rating']) ?>/5</span>
                    </div>
                    <p class="text-secondary-light mb-0">“<?= esc($feedback['feedback']) ?>”</p>
                </div>
            </div>
        </div><?php endforeach; ?></div>
<?= $this->endSection() ?>
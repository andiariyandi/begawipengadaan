<?= $this->extend('main/workspace/layouts/workspace') ?>
<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2"><?= esc($sectionContent['eyebrow']) ?></p>
    <h1 class="mb-3">Kelas Saya</h1>
    <p class="mb-0"><?= esc($sectionContent['description']) ?></p>
</div>
<div class="row g-4 mb-4">
    <?php foreach ($sectionContent['cards'] as $card): ?>
        <div class="col-md-4"><div class="bg-white border rounded p-4 h-100"><p class="text-muted"><?= esc($card['title']) ?></p><strong class="display-6"><?= esc($card['value']) ?></strong><small class="d-block text-muted mt-2"><?= esc($card['meta']) ?></small></div></div>
    <?php endforeach; ?>
</div>
<div class="bg-white border rounded p-4">
    <h2 class="h4 mb-3"><?= $sectionContent['items'] && is_array($sectionContent['items'][0] ?? null) ? 'Kelas terdaftar' : 'Materi dan kelas yang tersedia' ?></h2>
    <?php if (empty($sectionContent['items'])): ?>
        <p class="text-muted mb-0">Belum ada kelas aktif.</p>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($sectionContent['items'] as $item): ?>
                <?php if (is_array($item)): ?>
                    <?php
                    $status = (string) ($item['enrollment_status'] ?? 'pending');
                    $statusClass = match ($status) {
                        'active', 'completed' => 'bg-success-subtle text-success-emphasis',
                        'cancelled', 'withdrawn' => 'bg-secondary-subtle text-secondary-emphasis',
                        default => 'bg-warning-subtle text-warning-emphasis',
                    };
                    ?>
                    <div class="col-lg-6">
                        <article class="border rounded p-3 h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                <span class="badge <?= $statusClass ?>"><?= esc(ucfirst($status)) ?></span>
                                <small class="text-muted"><?= esc($item['class_code'] ?? '-') ?></small>
                            </div>
                            <h3 class="h5 mb-2"><?= esc($item['class_name'] ?? '-') ?></h3>
                            <p class="mb-1"><?= esc($item['course_title'] ?? '-') ?></p>
                            <?php if (! empty($item['program_title'])): ?><p class="small text-muted mb-1">Program: <?= esc($item['program_title']) ?></p><?php endif; ?>
                            <p class="small text-muted mb-3"><?= esc($item['start_at'] ?? '-') ?> – <?= esc($item['end_at'] ?? '-') ?></p>
                            <a class="btn btn-outline-primary btn-sm mt-auto align-self-start" href="<?= route_to('workspace.participant.class.detail', $item['class_id']) ?>">Lihat Kelas</a>
                        </article>
                    </div>
                <?php else: ?>
                    <div class="col-12"><p class="text-muted mb-0"><?= esc($item) ?></p></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

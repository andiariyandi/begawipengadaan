<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24"><h6 class="mb-0">Penilaian Assessment</h6></div>
    <div class="card-body p-24">
        <?php if (session()->getFlashdata('message')): ?><div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div><?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
        <?php if (empty($submissions)): ?>
            <p class="text-secondary-light">Tidak ada submission yang menunggu penilaian.</p>
        <?php else: ?>
            <?php foreach ($submissions as $submission): ?>
                <div class="border-bottom py-16">
                    <div class="mb-8">
                        <strong><?= esc($submission['title']) ?></strong>
                        <span class="text-secondary-light"> - <?= esc($submission['username']) ?> / <?= esc($submission['course_title']) ?></span>
                    </div>
                    <div class="bg-neutral-100 p-12 radius-8 mb-12"><?= nl2br(esc($submission['submission_text'])) ?></div>
                    <form action="<?= route_to('learning.evaluator.evaluate', $submission['id']) ?>" method="post" class="row gy-8 align-items-end">
                        <?= csrf_field() ?>
                        <div class="col-md-2"><label class="form-label">Nilai (0-100)</label><input type="number" name="score" min="0" max="100" step="0.01" class="form-control" required></div>
                        <div class="col-md-7"><label class="form-label">Feedback</label><input type="text" name="feedback" maxlength="1000" class="form-control"></div>
                        <div class="col-md-3"><button type="submit" class="btn btn-primary w-100">Simpan Penilaian</button></div>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="card p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="mb-0">Submission Assessment</h6>
    </div>
    <div class="card-body p-24">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <form action="<?= route_to('learning.study.assessment.submit', $enrollmentId, $assessmentId) ?>" method="post">
            <?= csrf_field() ?>
            <label class="form-label">Jawaban / uraian submission</label>
            <textarea name="submission_text" class="form-control" rows="12" required></textarea>
            <button type="submit" class="btn btn-primary mt-16">Kirim Submission</button>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

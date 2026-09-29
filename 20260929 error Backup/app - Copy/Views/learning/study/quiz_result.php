<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card p-24 radius-12">
    <h6 class="mb-16">Hasil Quiz</h6>

    <table class="table bordered-table sm-table mb-24">
        <tbody>
            <tr>
                <td>Skor</td>
                <td><?= esc($result['score']) ?></td>
            </tr>
            <tr>
                <td>Jawaban Benar</td>
                <td><?= esc($result['correct']) ?> / <?= esc($result['total']) ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <?php if ($result['is_passed']): ?>
                        <span class="badge bg-success-focus text-success-600 px-16 py-6 radius-4">LULUS</span>
                    <?php else: ?>
                        <span class="badge bg-danger-focus text-danger-600 px-16 py-6 radius-4">BELUM LULUS</span>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <a href="<?= route_to('learning.study.modules', $enrollmentId) ?>" class="btn btn-outline-primary px-24 py-12 radius-8">Kembali ke Materi</a>
</div>

<?= $this->endSection(); ?>
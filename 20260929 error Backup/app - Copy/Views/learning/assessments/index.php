<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="mb-24">
    <h6 class="mb-4">Assessment — <?= esc($module['title'] ?? '-') ?></h6>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card p-24 radius-12 mb-24">
    <h6 class="mb-16">Tambah Assessment</h6>
    <form action="<?= route_to('learning.assessments.store', $module['id']) ?>" method="post" class="row g-3 align-items-end">
        <?= csrf_field() ?>
        <div class="col-md-5">
            <label class="form-label">Judul</label>
            <input type="text" name="title" class="form-control" placeholder="mis. Quiz Modul 1">
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipe</label>
            <select name="type" class="form-select">
                <option value="quiz">Quiz (pilihan ganda, auto-grading)</option>
                <option value="exam">Exam (dinilai evaluator)</option>
                <option value="assignment">Assignment (dinilai evaluator)</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Passing Score</label>
            <input type="number" step="0.01" min="0" max="100" name="passing_score" class="form-control" value="70">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary px-24 py-12 radius-8 w-100">Tambah</button>
        </div>
    </form>
</div>

<?php if (empty($assessments)): ?>
    <p class="text-secondary-light">Belum ada assessment di modul ini.</p>
<?php else: ?>
    <div class="card p-0 radius-12">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Passing Score</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assessments as $assessment): ?>
                        <tr>
                            <td><?= esc($assessment['title']) ?></td>
                            <td class="text-center">
                                <span class="bg-neutral-200 text-neutral-600 border border-neutral-400 px-16 py-4 radius-4 text-sm">
                                    <?= esc(ucfirst($assessment['type'])) ?>
                                </span>
                            </td>
                            <td class="text-center"><?= esc($assessment['passing_score']) ?></td>
                            <td class="text-center">
                                <?php if ($assessment['type'] === 'quiz'): ?>
                                    <a href="<?= route_to('learning.assessments.questions', $assessment['id']) ?>" class="btn btn-outline-primary btn-sm px-16 py-8 radius-8">Kelola Soal</a>
                                <?php else: ?>
                                    <span class="text-secondary-light text-sm">Penilaian melalui evaluator</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection(); ?>
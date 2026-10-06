<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2">Ujian</h1>
    <p class="text-muted mb-0">Kelas: <?= esc($className ?? '-') ?> &middot; Auto grading pilihan ABC.</p>
</div>

<div class="bg-white border rounded p-4 mb-4 d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0">Daftar Ujian</h2>
    <a class="btn btn-primary" href="<?= route_to('workspace.trainer.quiz.create', $classId) ?>">Tambah Ujian</a>
</div>

<div class="bg-white border rounded p-4">
    <?php if (empty($quizzes ?? [])): ?>
        <p class="text-muted mb-0">Belum ada ujian.</p>
    <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>#</th><th>Judul</th><th>Tipe</th><th>Attempt</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($quizzes as $q): ?>
                    <tr>
                        <td><?= (int) $q['id'] ?></td>
                        <td><?= esc($q['title']) ?></td>
                        <td><?= esc(ucfirst(str_replace('_', ' ', (string) $q['type']))) ?></td>
                        <td><?= (int) $q['attempt_limit'] ?></td>
                        <td class="text-center"><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc(ucfirst($q['status'] ?? 'draft')) ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="<?= route_to('workspace.trainer.quiz.edit', $classId, $q['id']) ?>">Soal</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2">Materi</h1>
    <p class="text-muted mb-0">Kelas: <?= esc($className ?? '-') ?></p>
</div>

<div class="bg-white border rounded p-4">
    <h2 class="h4 mb-3">Materi per Modul</h2>
    <?php if (empty($items ?? [])): ?>
        <p class="text-muted mb-0">Belum ada materi.</p>
    <?php else: ?>
        <?php $currentModule = null; ?>
        <?php foreach ($items as $row): ?>
            <?php if ($row['module_id'] !== $currentModule): ?>
                <?php if ($currentModule !== null): ?></tbody></table></div><?php endif; ?>
                <?php $currentModule = (int) $row['module_id']; ?>
                <h3 class="h6 mt-3"><?= esc($row['title'] ?? 'Modul') ?></h3>
                <div class="table-responsive scroll-sm mb-4">
                    <table class="table bordered-table sm-table mb-0">
                        <thead><tr><th>#</th><th>Lesson</th><th>Tipe</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                        <tbody>
            <?php endif; ?>
                        <tr>
                            <td><?= (int) ($row['sort_order'] ?? 0) ?></td>
                            <td><?= esc($row['lesson_title'] ?? '-') ?></td>
                            <td><?= esc(ucfirst($row['content_type'] ?? '-')) ?></td>
                            <td class="text-center"><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc(ucfirst($row['status'] ?? 'draft')) ?></span></td>
                            <td class="text-end">
                                <?php if (! empty($row['lesson_id'])): ?>
                                    <a class="btn btn-outline-primary btn-sm" href="<?= route_to('workspace.trainer.lesson.edit', $classId, $row['lesson_id']) ?>">Ubah</a>
                                    <form method="post" action="<?= route_to('workspace.trainer.lesson.delete', $classId, $row['lesson_id']) ?>" class="d-inline" onsubmit="return confirm('Hapus lesson ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                    </form>
                                <?php endif; ?>
                                <a class="btn btn-outline-primary btn-sm" href="<?= route_to('workspace.trainer.lesson.create', $classId, $row['module_id']) ?>">+ Lesson</a>
                            </td>
                        </tr>
        <?php endforeach; ?>
                </tbody></table></div>
    <?php endif; ?>
</div>
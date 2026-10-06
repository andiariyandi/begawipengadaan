<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2">Modul</h1>
    <p class="text-muted mb-0">Kelas: <?= esc($className ?? '-') ?></p>
</div>

<div class="bg-white border rounded p-4 mb-4 d-flex justify-content-between align-items-center">
    <h2 class="h4 mb-0">Daftar Modul</h2>
    <a class="btn btn-primary" href="<?= route_to('workspace.trainer.module.create', $classId) ?>">Tambah Modul</a>
</div>

<div class="bg-white border rounded p-4">
    <?php if (empty($modules ?? [])): ?>
        <p class="text-muted mb-0">Belum ada modul pada kelas ini.</p>
    <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>#</th><th>Judul</th><th>Status</th><th>Wajib</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($modules as $i => $m): ?>
                    <tr>
                        <td><?= (int) ($m['sort_order'] ?? ($i + 1)) ?></td>
                        <td><?= esc($m['title'] ?? '-') ?></td>
                        <td class="text-center"><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc(ucfirst($m['status'] ?? 'draft')) ?></span></td>
                        <td class="text-center"><?= ((int) ($m['is_required'] ?? 0)) === 1 ? 'Ya' : 'Tidak' ?></td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="<?= route_to('workspace.trainer.lesson.create', $classId, $m['id']) ?>">+ Lesson</a>
                            <a class="btn btn-outline-primary btn-sm" href="<?= route_to('workspace.trainer.module.edit', $classId, $m['id']) ?>">Ubah</a>
                            <form method="post" action="<?= route_to('workspace.trainer.module.delete', $classId, $m['id']) ?>" class="d-inline" onsubmit="return confirm('Hapus modul ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
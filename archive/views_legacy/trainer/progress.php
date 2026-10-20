<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2">Progress Peserta</h1>
    <p class="text-muted mb-0">Kelas: <?= esc($className ?? '-') ?></p>
</div>

<div class="bg-white border rounded p-4">
    <h2 class="h4 mb-3">Progress Lesson</h2>
    <?php if (empty($items ?? [])): ?>
        <p class="text-muted mb-0">Belum ada data progress.</p>
    <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>Peserta</th><th>Lesson</th><th>Status</th><th>Persen</th><th>Mulai</th><th>Selesai</th></tr></thead>
                <tbody>
                <?php foreach ($items as $row): ?>
                    <tr>
                        <td><?= esc($row['name'] ?? $row['username'] ?? '-') ?></td>
                        <td><?= esc($row['lesson_title'] ?? '-') ?></td>
                        <td class="text-center"><?= esc(ucfirst($row['status'] ?? '-')) ?></td>
                        <td><?= (float) ($row['progress_percent'] ?? 0) ?>%</td>
                        <td><?= esc($row['started_at'] ?? '-') ?></td>
                        <td><?= esc($row['completed_at'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
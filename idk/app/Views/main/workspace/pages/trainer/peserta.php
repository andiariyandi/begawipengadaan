<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2">Peserta</h1>
    <p class="text-muted mb-0">Kelas: <?= esc($className ?? '-') ?></p>
</div>

<div class="bg-white border rounded p-4">
    <h2 class="h4 mb-3">Daftar Peserta</h2>
    <?php if (empty($participants ?? [])): ?>
        <p class="text-muted mb-0">Belum ada enrollment pada kelas ini.</p>
    <?php else: ?>
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Status</th><th>Didaftar</th><th>Diaktifkan</th></tr></thead>
                <tbody>
                <?php foreach ($participants as $p): ?>
                    <tr>
                        <td><?= esc($p['name'] ?? $p['username'] ?? '-') ?></td>
                        <td><?= esc($p['username'] ?? '-') ?></td>
                        <td><?= esc($p['email'] ?? '-') ?></td>
                        <td class="text-center"><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc(ucfirst($p['enrollment_status'] ?? '-')) ?></span></td>
                        <td><?= esc($p['enrolled_at'] ?? '-') ?></td>
                        <td><?= esc($p['activated_at'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
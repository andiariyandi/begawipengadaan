<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<div class="card h-100 p-0 radius-12">
    <div class="card-header border-bottom bg-base py-16 px-24">
        <h6 class="mb-0">Audit Log</h6>
    </div>
    <div class="card-body p-24">

        <form method="get" class="row g-3 mb-24">
            <div class="col-md-3">
                <label class="form-label">Modul</label>
                <select name="module" class="form-select">
                    <option value="">Semua Modul</option>
                    <?php foreach ($modules as $m): ?>
                        <option value="<?= esc($m['module']) ?>" <?= $module === $m['module'] ? 'selected' : '' ?>><?= esc($m['module']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="from" class="form-control" value="<?= esc($from ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control" value="<?= esc($to ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary px-24 py-12 radius-8 w-100">Filter</button>
            </div>
        </form>

        <?php if (empty($logs)): ?>
            <p class="text-secondary-light">Tidak ada log ditemukan.</p>
        <?php else: ?>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Modul</th>
                            <th>Target</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= esc(date('d M Y H:i', strtotime($log['created_at']))) ?></td>
                                <td><?= esc($log['username'] ?? '-') ?></td>
                                <td><span class="bg-neutral-200 text-neutral-600 px-16 py-4 radius-4 text-sm"><?= esc($log['action']) ?></span></td>
                                <td><?= esc($log['module'] ?? '-') ?></td>
                                <td><?= esc($log['target_type'] ?? '-') ?> <?= $log['target_id'] ? '#' . esc($log['target_id']) : '' ?></td>
                                <td><?= esc($log['ip_address'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-16">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>
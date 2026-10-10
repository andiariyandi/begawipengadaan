<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$variant          = $variant ?? 'default';
$kpis             = $kpis ?? [];
$approvalQueue    = $approvalQueue ?? [];
$recentActivity   = $recentActivity ?? [];
$usersByProfile   = $usersByProfile ?? [];
$usersByAccount   = $usersByAccount ?? [];
$accountStatusDonut = $accountStatusDonut ?? ['series' => [0, 0, 0], 'labels' => ['Aktif', 'Pending', 'Lainnya'], 'total' => 0];
$enrollmentMonthly = $enrollmentMonthly ?? ['categories' => [], 'registration' => [], 'enrollment' => []];

$fmt = static fn(int $n): string => number_format($n, 0, ',', '.');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Ringkasan') ?></h6>
        <p class="text-secondary-light mb-0">Ikhtisar operasional dan pemantauan metrik BegawiPBJ.</p>
    </div>
    <ul class="d-flex align-items-center gap-2 mb-0">
        <li class="fw-medium"><a href="<?= site_url('dashboard') ?>" class="hover-text-primary">Beranda</a></li>
        <li>-</li>
        <li class="fw-medium"><?= esc($pageTitle ?? '') ?></li>
    </ul>
</div>

<?php if ($variant === 'approval-queue'): ?>
    <?php
    $approvedToday = (int) ($approvedToday ?? 0);
    $rejectedToday = (int) ($rejectedToday ?? 0);
    ?>
    <div class="row row-cols-xxxl-4 row-cols-lg-2 row-cols-1 gy-4">
        <div class="col"><div class="card h-100"><div class="card-body p-20"><p class="fw-medium text-primary-light mb-1">Menunggu</p><h6 class="mb-0"><?= $fmt((int) ($kpis['pendingApprovals'] ?? 0)) ?></h6><p class="text-sm text-primary-light mt-12 mb-0">Memerlukan tindakan</p></div></div></div>
        <div class="col"><div class="card h-100"><div class="card-body p-20"><p class="fw-medium text-primary-light mb-1">Disetujui Hari Ini</p><h6 class="mb-0"><?= $fmt($approvedToday) ?></h6><p class="text-sm text-primary-light mt-12 mb-0">Permintaan diproses</p></div></div></div>
        <div class="col"><div class="card h-100"><div class="card-body p-20"><p class="fw-medium text-primary-light mb-1">Ditolak Hari Ini</p><h6 class="mb-0"><?= $fmt($rejectedToday) ?></h6><p class="text-sm text-primary-light mt-12 mb-0">Dengan catatan tindak lanjut</p></div></div></div>
        <div class="col"><div class="card h-100"><div class="card-body p-20"><p class="fw-medium text-primary-light mb-1">Total Pending</p><h6 class="mb-0"><?= $fmt((int) ($kpis['pendingApprovals'] ?? 0)) ?></h6><p class="text-sm text-primary-light mt-12 mb-0">Seluruh antrean</p></div></div></div>
    </div>

    <div class="card mt-24">
        <div class="card-body p-24">
            <h6 class="text-lg mb-16">Daftar Antrean</h6>
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead><tr><th>ID</th><th>Resource</th><th>Action</th><th>Pemohon</th><th>Diajukan</th><th class="text-center">Status</th></tr></thead>
                    <tbody>
                        <?php if ($approvalQueue === []): ?>
                            <tr><td colspan="6" class="text-center text-secondary-light">Tidak ada antrian.</td></tr>
                        <?php else: ?>
                            <?php foreach ($approvalQueue as $row): ?>
                                <tr>
                                    <td>#<?= esc((string) $row['id']) ?></td>
                                    <td><?= esc($row['resource_type']) ?></td>
                                    <td><?= esc($row['requested_action']) ?></td>
                                    <td><?= esc($row['requester']) ?></td>
                                    <td><?= esc(date('d M Y H:i', strtotime($row['created_at']))) ?></td>
                                    <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $row['status']]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($variant === 'recent-activity'): ?>
    <div class="card">
        <div class="card-body p-24">
            <h6 class="text-lg mb-16">Aktivitas Sistem Terbaru</h6>
            <div class="d-flex flex-column gap-20">
                <?php if ($recentActivity === []): ?>
                    <p class="text-secondary-light text-sm mb-0">Belum ada aktivitas tercatat.</p>
                <?php else: ?>
                    <?php foreach ($recentActivity as $entry): ?>
                        <div class="d-flex gap-12">
                            <span class="w-40-px h-40-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"><iconify-icon icon="solar:history-2-outline" class="text-xl"></iconify-icon></span>
                            <div>
                                <h6 class="text-md mb-4"><?= esc($entry['action']) ?></h6>
                                <p class="text-sm text-secondary-light mb-0">
                                    <?= esc($entry['actor']) ?>
                                    <?php if ($entry['resource_type'] !== ''): ?>
                                        &middot; <?= esc($entry['resource_type']) ?>#<?= esc((string) $entry['resource_id']) ?>
                                    <?php endif; ?>
                                    &middot; <?= esc($entry['result']) ?>
                                </p>
                                <span class="text-xs text-secondary-light"><?= esc(date('d M Y H:i', strtotime($entry['created_at']))) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php elseif ($variant === 'system-indicators'): ?>
    <div class="row row-cols-xxxl-2 row-cols-1 gy-4">
        <div class="col">
            <div class="card h-100">
                <div class="card-body p-24">
                    <h6 class="mb-16 text-lg">Distribusi Tipe Profil</h6>
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table sm-table mb-0">
                            <thead><tr><th>Tipe</th><th class="text-end">Jumlah</th></tr></thead>
                            <tbody>
                                <?php if ($usersByProfile === []): ?>
                                    <tr><td colspan="2" class="text-center text-secondary-light">Belum ada data.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($usersByProfile as $pt => $total): ?>
                                        <tr><td><?= esc($pt) ?></td><td class="text-end"><strong><?= $fmt((int) $total) ?></strong></td></tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body p-24">
                    <h6 class="mb-16 text-lg">Distribusi Status Akun</h6>
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table sm-table mb-0">
                            <thead><tr><th>Status</th><th class="text-end">Jumlah</th></tr></thead>
                            <tbody>
                                <?php if ($usersByAccount === []): ?>
                                    <tr><td colspan="2" class="text-center text-secondary-light">Belum ada data.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($usersByAccount as $st => $total): ?>
                                        <tr><td><?= esc($st) ?></td><td class="text-end"><strong><?= $fmt((int) $total) ?></strong></td></tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="row gy-4">
        <?php foreach ([
            ['label' => 'Total Pengguna',          'value' => $kpis['totalUsers']          ?? 0, 'icon' => 'gridicons:multiple-users',                'color' => 'bg-cyan'],
            ['label' => 'Akun Aktif',             'value' => $kpis['activeUsers']         ?? 0, 'icon' => 'solar:user-check-rounded-outline',         'color' => 'bg-purple'],
            ['label' => 'Enrollment Aktif',       'value' => $kpis['activeEnrollments']   ?? 0, 'icon' => 'solar:book-2-outline',                     'color' => 'bg-info'],
            ['label' => 'Sertifikat Terbit',      'value' => $kpis['issuedCertificates']  ?? 0, 'icon' => 'solar:medal-ribbons-star-outline',         'color' => 'bg-success-main'],
            ['label' => 'Menunggu Approval',      'value' => $kpis['pendingApprovals']    ?? 0, 'icon' => 'solar:clock-circle-outline',               'color' => 'bg-red'],
        ] as $card): ?>
            <div class="col-xxl-4 col-md-6">
                <div class="card shadow-none border h-100">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1"><?= esc($card['label']) ?></p>
                                <h6 class="mb-0"><?= $fmt((int) $card['value']) ?></h6>
                            </div>
                            <div class="w-50-px h-50-px <?= esc($card['color']) ?> rounded-circle d-flex justify-content-center align-items-center"><iconify-icon icon="<?= esc($card['icon']) ?>" class="text-white text-2xl mb-0"></iconify-icon></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="card mt-24">
        <div class="card-body p-32 text-center">
            <span class="w-72-px h-72-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center mb-20"><iconify-icon icon="solar:chart-2-outline" class="text-4xl"></iconify-icon></span>
            <h5 class="mb-12"><?= esc($pageTitle ?? 'Ringkasan') ?></h5>
            <p class="text-secondary-light mb-0">Pilih sub-menu spesifik pada sidebar untuk ringkasan terperinci.</p>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
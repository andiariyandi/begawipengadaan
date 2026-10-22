<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/users/_nav_access', ['activeSub' => 'verification']) ?>
<?php $items = $items ?? []; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Permohonan Perubahan Profil</h6>
        <p class="text-secondary-light mb-0">Tinjau perubahan data profil dari pengguna aktif.</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Email</th>
                        <th>Tipe Profil</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <?= view('dashboard/components/empty_state', [
                                    'title'   => 'Tidak Ada Permohonan Perubahan',
                                    'message' => 'Saat ini tidak ada permohonan perubahan profil dari pengguna.',
                                    'icon'    => 'solar:user-id-outline',
                                ]) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><h6 class="text-md mb-0"><?= esc($item['user_name'] ?? '-') ?></h6></td>
                            <td><?= esc($item['email'] ?? '-') ?></td>
                            <td><?= esc(ucfirst($item['profile_type'] ?? '-')) ?></td>
                            <td><?= view('dashboard/components/status_badge', ['status' => $item['status'] ?? 'draft']) ?></td>
                            <td><?= esc(date('d M Y', strtotime($item['created_at']))) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/akses/permohonan-perubahan-profil/' . (int) ($item['id'] ?? 0)) ?>" class="text-primary-600">Tinjau</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= $this->include('dashboard/components/breadcrumb') ?>
<?= $this->include('dashboard/components/filter_bar') ?>

<div class="card">
    <div class="card-header border-bottom bg-base py-16 px-24 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <h6 class="text-lg mb-0">Daftar Pengguna</h6>
        <a href="<?= site_url('dashboard/akses/pengguna/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11"><iconify-icon icon="solar:add-circle-outline" class="text-xl me-4"></iconify-icon> Tambah Pengguna</a>
    </div>
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead><tr><th>Pengguna</th><th>Tipe Profil</th><th>Role</th><th>Terdaftar</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                    <?php $users = [
                        ['id' => 'USR-001', 'name' => 'Rina Oktaviani', 'email' => 'rina.oktaviani@lampungprov.go.id', 'profile' => 'ASN', 'role' => 'Peserta', 'date' => '28 Sep 2026', 'status' => 'active'],
                        ['id' => 'USR-002', 'name' => 'Andi Pratama', 'email' => 'andi.pratama@example.id', 'profile' => 'Penyedia Barang/Jasa', 'role' => 'Trainer', 'date' => '27 Sep 2026', 'status' => 'pending'],
                        ['id' => 'USR-003', 'name' => 'Dr. Maya Sari', 'email' => 'maya.sari@kampus.ac.id', 'profile' => 'Akademisi', 'role' => 'Evaluator', 'date' => '25 Sep 2026', 'status' => 'active'],
                        ['id' => 'USR-004', 'name' => 'Budi Santoso', 'email' => 'budi.santoso@example.id', 'profile' => 'Masyarakat Umum', 'role' => 'Public User', 'date' => '23 Sep 2026', 'status' => 'revision'],
                    ]; ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><div class="d-flex align-items-center"><span class="w-40-px h-40-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0 me-12 fw-semibold"><?= esc(substr($user['name'], 0, 1)) ?></span><div><h6 class="text-md mb-0 fw-medium"><?= esc($user['name']) ?></h6><span class="text-sm text-secondary-light fw-medium"><?= esc($user['email']) ?></span></div></div></td>
                            <td><?= esc($user['profile']) ?></td><td><?= esc($user['role']) ?></td><td><?= esc($user['date']) ?></td>
                            <td class="text-center"><?= view('dashboard/components/status_badge', ['status' => $user['status']]) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/akses/pengguna/' . $user['id']) ?>" class="w-32-px h-32-px bg-primary-50 text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center"><iconify-icon icon="solar:eye-outline"></iconify-icon></a><a href="<?= site_url('dashboard/akses/pengguna/' . $user['id'] . '/ubah') ?>" class="w-32-px h-32-px bg-warning-focus text-warning-main rounded-circle d-inline-flex align-items-center justify-content-center ms-6"><iconify-icon icon="solar:pen-new-square-outline"></iconify-icon></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

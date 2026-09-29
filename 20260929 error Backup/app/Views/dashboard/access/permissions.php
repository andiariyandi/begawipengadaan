<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Permission</h6>
        <p class="text-secondary-light mb-0">Daftar hak akses granular BegawiPBJ.</p>
    </div><a href="<?= site_url('dashboard/akses/permission/tambah') ?>" class="btn btn-primary-600 radius-8 px-20 py-11">Tambah Permission</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
                <thead>
                    <tr>
                        <th>Kode Permission</th>
                        <th>Modul</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody><?php foreach ([['users.manage', 'Manajemen Akses', 'Mengelola data pengguna'], ['learning.study', 'Arena Belajar', 'Mengakses aktivitas pembelajaran'], ['learning.assignment.grade', 'Arena Belajar', 'Menilai assignment peserta'], ['certificate.issue', 'Sertifikat', 'Menerbitkan sertifikat'], ['regulations.manage', 'Regulasi PBJ', 'Mengelola regulasi dan versi dokumen']] as $permission): ?><tr>
                            <td><span class="fw-semibold text-primary-600"><?= esc($permission[0]) ?></span></td>
                            <td><?= esc($permission[1]) ?></td>
                            <td><?= esc($permission[2]) ?></td>
                            <td class="text-center"><a href="<?= site_url('dashboard/akses/permission/' . str_replace('.', '-', $permission[0])) ?>" class="text-primary-600">Detail</a></td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
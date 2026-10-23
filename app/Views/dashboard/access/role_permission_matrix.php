<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?= view('dashboard/users/_nav_access', ['activeSub' => 'roles']) ?>
<?php
$roles = $roles ?? [];
$permissions = $permissions ?? [];
$matrix = $matrix ?? [];
$pager = $pager ?? null;

$domainTitles = [
    'dashboard'     => '1. Dashboard Utama',
    'landing'       => '2. Kelola Landing Page (CMS)',
    'access'        => '3. Manajemen Akses & Pengguna',
    'learning'      => '4. Arena Belajar (LMS)',
    'bunga'         => '5. Bunga Rampai & Pengetahuan (KMS)',
    'regulation'    => '6. Repositori Regulasi PBJ',
    'communication' => '7. Komunikasi, Berita, & Event',
    'certificate'   => '8. Sertifikat & Kelulusan',
    'intelligence'  => '9. Government Intelligence SDM PBJ',
    'reports'       => '10. Pusat Laporan Terpadu',
    'survey'        => '11. Survei Kepuasan (IKM)',
    'system'        => '12. Sistem, Approval, & Keamanan',
];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Role vs Permission Matrix</h6>
        <p class="text-secondary-light mb-0">Kelola dan sinkronkan hak akses secara granular per modul untuk seluruh role.</p>
    </div>
    <div class="d-flex gap-12">
        <a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Role
        </a>
    </div>
</div>

<div class="card border radius-12">
    <div class="card-body p-24">
        <form action="<?= site_url('dashboard/akses/role/matrix') ?>" method="post">
            <?= csrf_field() ?>

            <div class="alert alert-primary bg-primary-50 text-primary-900 border-primary-100 radius-8 p-16 mb-20 d-flex align-items-center gap-12">
                <i class="fas fa-info-circle text-primary-600 text-lg flex-shrink-0"></i>
                <div class="text-sm">
                    <strong>Tips Pengaturan Hak Akses:</strong> Matriks di bawah menampilkan seluruh hak akses aktif di sistem saat ini. Jika Anda hanya ingin mengatur izin untuk <strong>1 role tertentu</strong> secara terisolasi tanpa risiko menyentuh role lain, klik tombol <span class="badge bg-neutral-200 text-secondary-dark px-6 py-2"><i class="fas fa-edit me-1"></i> Edit</span> pada kolom role yang diinginkan.
                </div>
            </div>

            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0 align-middle">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th style="min-width: 280px;" class="py-16">
                                <span class="fw-bold text-neutral-800">Modul &amp; Permission</span>
                            </th>
                            <?php foreach ($roles as $role): ?>
                                <th class="text-center py-16" style="min-width: 140px;">
                                    <div class="fw-bold text-primary-600"><?= esc($role['name']) ?></div>
                                    <span class="badge bg-neutral-200 text-secondary-dark text-xs mb-8"><?= esc($role['key']) ?></span>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button"
                                                class="btn btn-xs btn-outline-primary px-8 py-2 toggle-all-role text-xs radius-4"
                                                data-role="<?= (int) $role['id'] ?>"
                                                title="Pilih atau batalkan semua permission untuk role <?= esc($role['name']) ?>">
                                            <i class="fas fa-check-double me-1"></i> Semua
                                        </button>
                                        <a href="<?= site_url('dashboard/akses/role/' . (int) $role['id'] . '/ubah') ?>"
                                           class="btn btn-xs btn-outline-secondary px-8 py-2 text-xs radius-4"
                                           title="Ubah khusus izin role <?= esc($role['name']) ?> saja secara terpisah">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Group permissions by domain for readability
                        $byDomain = [];
                        foreach ($permissions as $perm) {
                            $domain = $perm['domain'] ?? 'other';
                            $byDomain[$domain][] = $perm;
                        }
                        ?>
                        <?php foreach ($byDomain as $domain => $perms): ?>
                            <?php
                            $domainLabel = $domainTitles[$domain] ?? ucfirst($domain);
                            ?>
                            <tr class="table-light">
                                <td class="fw-bold bg-neutral-100 py-12 px-16 text-primary-700">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>
                                            <i class="fas fa-layer-group text-primary-600 me-2"></i>
                                            <?= esc($domainLabel) ?>
                                        </span>
                                        <span class="badge bg-primary-100 text-primary-700 text-xs px-8 py-4 radius-4">
                                            <?= count($perms) ?> Izin
                                        </span>
                                    </div>
                                </td>
                                <?php foreach ($roles as $role): ?>
                                    <td class="text-center bg-neutral-100 py-12">
                                        <button type="button"
                                                class="btn btn-xs btn-outline-neutral-400 px-8 py-2 toggle-domain-role text-xs radius-4"
                                                data-domain="<?= esc($domain) ?>"
                                                data-role="<?= (int) $role['id'] ?>"
                                                title="Pilih/Batalkan semua izin <?= esc($domainLabel) ?> untuk role <?= esc($role['name']) ?>">
                                            <i class="fas fa-check-square me-1"></i> Modul
                                        </button>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach ($perms as $perm): ?>
                                <tr>
                                    <td class="py-12 px-16">
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-semibold font-monospace text-primary-600 text-sm"><?= esc($perm['key']) ?></span>
                                                <?php if (! empty($perm['is_system'])): ?>
                                                    <span class="badge bg-neutral-100 text-neutral-600 text-xs border" title="Permission sistem dilindungi">Sistem</span>
                                                <?php endif; ?>
                                            </div>
                                            <span class="text-secondary-light text-xs mt-2"><?= esc($perm['name']) ?></span>
                                            <?php if (! empty($perm['description'])): ?>
                                                <small class="text-secondary-light text-xxs fst-italic"><?= esc($perm['description']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <?php foreach ($roles as $role): ?>
                                        <td class="text-center py-12">
                                            <div class="form-check style-check d-flex justify-content-center">
                                                <input class="form-check-input matrix-checkbox"
                                                       type="checkbox"
                                                       name="matrix[<?= (int) $role['id'] ?>][]"
                                                       id="m-<?= (int) $role['id'] ?>-<?= (int) $perm['id'] ?>"
                                                       data-domain="<?= esc($domain) ?>"
                                                       data-role="<?= (int) $role['id'] ?>"
                                                       value="<?= (int) $perm['id'] ?>"
                                                    <?= in_array((int) $perm['id'], array_map('intval', (array) ($matrix[$role['id']] ?? [])), true) ? 'checked' : '' ?>>
                                            </div>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-24 pt-16 border-top">
                <span class="text-secondary-light text-xs">
                    <i class="fas fa-info-circle me-1"></i> Perubahan hak akses akan segera aktif saat user memuat ulang halaman.
                </span>
                <div class="d-flex gap-12">
                    <a href="<?= site_url('dashboard/akses/role') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">
                        <i class="fas fa-save me-1"></i> Simpan Matriks Hak Akses
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Toggle semua permission dalam 1 modul (domain) untuk 1 role
    document.querySelectorAll('.toggle-domain-role').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var domain = this.getAttribute('data-domain');
            var roleId = this.getAttribute('data-role');
            var checkboxes = document.querySelectorAll('.matrix-checkbox[data-domain="' + domain + '"][data-role="' + roleId + '"]');
            var anyUnchecked = Array.from(checkboxes).some(function(cb) { return !cb.checked; });
            checkboxes.forEach(function(cb) {
                cb.checked = anyUnchecked;
            });
        });
    });

    // 2. Toggle seluruh permission untuk 1 role
    document.querySelectorAll('.toggle-all-role').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var roleId = this.getAttribute('data-role');
            var checkboxes = document.querySelectorAll('.matrix-checkbox[data-role="' + roleId + '"]');
            var anyUnchecked = Array.from(checkboxes).some(function(cb) { return !cb.checked; });
            checkboxes.forEach(function(cb) {
                cb.checked = anyUnchecked;
            });
        });
    });
});
</script>
<?= $this->endSection() ?>

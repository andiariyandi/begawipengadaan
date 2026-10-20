<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$formMode = $formMode ?? 'create';
$role     = $role ?? [];
$roleId   = $roleId ?? ($role['id'] ?? null);
$isEdit   = $formMode === 'edit';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Peran Pengadaan') ?></h6>
        <p class="text-secondary-light mb-0"><?= $isEdit ? 'Ubah informasi peran pengadaan di bawah ini.' : 'Tambahkan peran baru ke dalam master data peran pengadaan.' ?></p>
    </div>
    <a href="<?= site_url('dashboard/akses/peran-pengadaan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form action="<?= $isEdit ? site_url('dashboard/akses/peran-pengadaan/' . (int) $roleId . '/ubah') : site_url('dashboard/akses/peran-pengadaan/tambah') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row gy-20">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Kode Peran <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control radius-8" name="key"
                           value="<?= esc(old('key', $role['key'] ?? '')) ?>"
                           placeholder="Contoh: PPK, PA, POKJA_PEMILIHAN"
                           <?= $isEdit ? 'readonly' : 'required' ?> maxlength="64">
                    <span class="text-secondary-light text-xs mt-4 d-block">
                        <?= $isEdit ? 'Kode peran bersifat unik dan tidak dapat diubah setelah dibuat.' : 'Kode identifikasi sistem (huruf kapital/angka/underscore).' ?>
                    </span>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Status Keaktifan <span class="text-danger">*</span>
                    </label>
                    <select class="form-select radius-8" name="is_active">
                        <option value="1" <?= (string) old('is_active', $role['is_active'] ?? '1') === '1' ? 'selected' : '' ?>>Aktif (Dapat dipilih pengguna)</option>
                        <option value="0" <?= (string) old('is_active', $role['is_active'] ?? '1') === '0' ? 'selected' : '' ?>>Tidak Aktif (Dinonaktifkan)</option>
                    </select>
                    <span class="text-secondary-light text-xs mt-4 d-block">Peran aktif akan langsung muncul pada pilihan form pendaftaran ASN.</span>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">
                        Nama Peran Pengadaan <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control radius-8" name="name"
                           value="<?= esc(old('name', $role['name'] ?? '')) ?>"
                           placeholder="Masukkan nama resmi peran pengadaan" required maxlength="255">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Deskripsi & Tugas Pokok</label>
                    <textarea class="form-control radius-8" name="description" rows="4"
                              placeholder="Jelaskan fungsi, wewenang, dan tanggung jawab peran pengadaan ini..."><?= esc(old('description', $role['description'] ?? '')) ?></textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-12 mt-12 pt-16 border-top">
                    <a href="<?= site_url('dashboard/akses/peran-pengadaan') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-24 py-11 d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="solar:disk-check-outline" class="text-lg"></iconify-icon>
                        <span><?= $isEdit ? 'Simpan Perubahan' : 'Buat Peran' ?></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

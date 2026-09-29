<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Kelas') ?></h6>
        <p class="text-secondary-light mb-0">Form UI-only. Data belum disimpan.</p>
    </div><a href="<?= site_url('dashboard/pembelajaran/kelas') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <form action="#">
            <div class="row gy-20">
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Nama Kelas</label><input type="text" class="form-control radius-8" value="<?= ($formMode ?? '') === 'edit' ? 'PBJ Dasar Batch 1' : '' ?>" placeholder="Masukkan nama kelas"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Course</label><select class="form-select radius-8">
                        <option>Dasar-Dasar Pengadaan Barang/Jasa</option>
                        <option>Manajemen Kontrak Pengadaan</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Trainer</label><select class="form-select radius-8">
                        <option>Hendra Wijaya</option>
                        <option>Dr. Maya Sari</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kapasitas Peserta</label><input type="number" class="form-control radius-8" placeholder="40"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Mulai</label><input type="date" class="form-control radius-8"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Tanggal Selesai</label><input type="date" class="form-control radius-8"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label><select class="form-select radius-8">
                        <option>Draft</option>
                        <option>Aktif</option>
                        <option>Selesai</option>
                    </select></div>
                <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Keterangan</label><textarea class="form-control radius-8" rows="4" placeholder="Keterangan tambahan kelas"></textarea></div>
                <div class="col-12 d-flex justify-content-end gap-12"><a href="<?= site_url('dashboard/pembelajaran/kelas') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan Kelas</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
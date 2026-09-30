<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'FAQ') ?></h6>
        <p class="text-secondary-light mb-0">Form UI-only. Data belum disimpan.</p>
    </div><a href="<?= site_url('dashboard/bunga-rampai/faq') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Kembali</a>
</div>
<div class="card">
    <div class="card-body p-24">
        <form action="#">
            <div class="row gy-20">
                <div class="col-md-8"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Pertanyaan</label><input type="text" class="form-control radius-8" placeholder="Masukkan pertanyaan"></div>
                <div class="col-md-4"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Kategori</label><select class="form-select radius-8">
                        <option>Pembelajaran</option>
                        <option>Sertifikat</option>
                        <option>Akun</option>
                        <option>Regulasi</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Tampil</label><input type="number" class="form-control radius-8" placeholder="1"></div>
                <div class="col-md-6"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Status</label><select class="form-select radius-8">
                        <option>Draft</option>
                        <option>Aktif</option>
                    </select></div>
                <div class="col-12"><label class="form-label fw-semibold text-primary-light text-sm mb-8">Jawaban</label><textarea class="form-control radius-8" rows="6" placeholder="Masukkan jawaban FAQ"></textarea></div>
                <div class="col-12 d-flex justify-content-end gap-12"><a href="<?= site_url('dashboard/bunga-rampai/faq') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a><button type="button" class="btn btn-primary-600 radius-8 px-20 py-11">Simpan FAQ</button></div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
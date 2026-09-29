<?= $this->extend('layout/public'); ?>

<?= $this->section('content'); ?>

<div class="mb-24">
    <h6 class="mb-4">FAQ PBJ</h6>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success radius-12 mb-16"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card p-24 radius-12 mb-24">
    <form method="get" class="row g-3">
        <div class="col-md-6">
            <input type="text" name="q" class="form-control" placeholder="Cari FAQ..." value="<?= esc($keyword ?? '') ?>">
        </div>
        <div class="col-md-4">
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>" <?= ((string) $categoryId === (string) $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100 px-24 py-12 radius-8">Cari</button>
        </div>
    </form>
</div>

<?php if (in_array('kms.manage', session()->get('permissions') ?? [], true)): ?>
    <div class="card p-24 radius-12 mb-24">
        <h6 class="mb-16">Tambah FAQ</h6>
        <form action="<?= route_to('knowledge.faq.store') ?>" method="post" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-4">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= esc($category['id']) ?>"><?= esc($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label">Pertanyaan</label>
                <input type="text" name="question" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Jawaban</label>
                <textarea name="answer" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Simpan</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php if (empty($faqs)): ?>
    <p class="text-secondary-light">Belum ada FAQ.</p>
<?php else: ?>
    <div class="accordion" id="faqAccordion">
        <?php foreach ($faqs as $i => $faq): ?>
            <div class="accordion-item radius-12 mb-12 border">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>">
                        <?= esc($faq['question']) ?>
                        <span class="badge bg-neutral-200 text-neutral-600 ms-8"><?= esc($faq['category_name']) ?></span>
                    </button>
                </h2>
                <div id="faq<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body d-flex align-items-center justify-content-between gap-3">
                        <span><?= nl2br(esc($faq['answer'])) ?></span>
                        <?php if (in_array('kms.manage', session()->get('permissions') ?? [], true)): ?>
                            <form action="<?= route_to('knowledge.faq.delete', $faq['id']) ?>" method="post" onsubmit="return confirm('Hapus FAQ ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="bg-danger-focus text-danger-600 w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle border-0">
                                    <iconify-icon icon="fluent:delete-24-regular"></iconify-icon>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection(); ?>
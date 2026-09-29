<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>

<?php $faq = $faq ?? null; ?>
<div class="card p-24 radius-12">
    <h6 class="mb-24"><?= $faq ? 'Edit' : 'Tambah' ?> FAQ</h6>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger radius-12 mb-16"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <form action="<?= route_to('knowledge.faq.update', $faq['id']) ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-16">
            <label class="form-label">Kategori</label>
            <select name="category_id" class="form-select">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>" <?= isset($faq['category_id']) && (string) $faq['category_id'] === (string) $category['id'] ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-16">
            <label class="form-label">Pertanyaan</label>
            <input type="text" name="question" class="form-control" value="<?= esc($faq['question'] ?? old('question')) ?>">
        </div>
        <div class="mb-16">
            <label class="form-label">Jawaban</label>
            <textarea name="answer" class="form-control" rows="8"><?= esc($faq['answer'] ?? old('answer')) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary px-24 py-12 radius-8">Simpan</button>
        <a href="<?= route_to('knowledge.faq') ?>" class="btn btn-light px-24 py-12 radius-8">Batal</a>
    </form>
</div>

<?= $this->endSection(); ?>

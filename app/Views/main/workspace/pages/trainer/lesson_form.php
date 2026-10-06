<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2"><?= esc($mode === 'edit' ? 'Ubah Lesson' : 'Tambah Lesson') ?></h1>
    <p class="text-muted mb-0">Modul: <?= esc($module['title'] ?? '-') ?> &middot; Kelas: <?= esc($className ?? '-') ?></p>
</div>

<div class="bg-white border rounded p-4">
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert"><?= esc($error) ?></div>
    <?php endif; ?>
    <form method="post" action="<?= ($mode ?? 'create') === 'edit' ? route_to('workspace.trainer.lesson.edit', $classId, $lesson['id'] ?? 0) : route_to('workspace.trainer.lesson.create', $classId, $module['id'] ?? 0) ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Judul Lesson</label>
                <input type="text" name="title" class="form-control radius-8" value="<?= esc($lesson['title'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Tipe Konten</label>
                <select name="content_type" id="lesson-type" class="form-select radius-8">
                    <?php foreach (['text' => 'Teks', 'document' => 'Dokumen', 'video' => 'Video', 'link' => 'Tautan'] as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($lesson['content_type'] ?? 'text') === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12" data-show="text document video link">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Konten Teks</label>
                <textarea name="content" class="form-control radius-8" rows="6"><?= esc($lesson['content'] ?? '') ?></textarea>
            </div>
            <div class="col-12" data-show="document">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Label Dokumen</label>
                <input type="text" name="document_label" class="form-control radius-8" value="<?= esc($lesson['title'] ?? '') ?>" placeholder="contoh: Modul-1-Panduan.pdf">
                <small class="text-muted">File biner diunggah di luar vertical slice ini; record file_asset dibuat otomatis di sistem file.</small>
            </div>
            <div class="col-12" data-show="video link">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">URL</label>
                <input type="url" name="external_url" class="form-control radius-8" value="<?= esc($lesson['external_url'] ?? '') ?>" placeholder="https://">
            </div>
            <div class="col-12 d-flex justify-content-end gap-8">
                <a href="<?= route_to('workspace.trainer.materials', $classId) ?>" class="btn btn-outline-neutral-300 radius-8">Batal</a>
                <button type="submit" class="btn btn-primary radius-8">Simpan</button>
            </div>
        </div>
    </form>
</div>
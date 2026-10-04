<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2">Soal Ujian</h1>
    <p class="text-muted mb-0"><?= esc($quiz['title'] ?? '-') ?> &middot; <?= esc(ucfirst(str_replace('_', ' ', (string) $quiz['type']))) ?> &middot; Attempt: <?= (int) $quiz['attempt_limit'] ?></p>
</div>

<div class="bg-white border rounded p-4 mb-4">
    <h2 class="h5 mb-3">Pengaturan Ujian</h2>
    <form method="post" action="<?= route_to('workspace.trainer.quiz.edit', $classId, $quiz['id']) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="_action" value="update_meta">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Judul</label>
                <input type="text" name="title" class="form-control radius-8" value="<?= esc($quiz['title']) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Attempt Limit</label>
                <input type="number" name="attempt_limit" min="1" value="<?= (int) $quiz['attempt_limit'] ?>" class="form-control radius-8">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Status</label>
                <select name="status" class="form-select radius-8">
                    <?php foreach (['draft', 'published', 'archived'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($quiz['status'] ?? 'draft') === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Instruksi</label>
                <textarea name="instructions" class="form-control radius-8" rows="2"><?= esc($quiz['instructions'] ?? '') ?></textarea>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary radius-8">Simpan Pengaturan</button>
            </div>
        </div>
    </form>
</div>

<div class="bg-white border rounded p-4 mb-4 d-flex justify-content-between align-items-center">
    <h2 class="h5 mb-0">Daftar Soal</h2>
    <a class="btn btn-primary" href="<?= route_to('workspace.trainer.question.create', $classId, $quiz['id']) ?>">+ Soal</a>
</div>

<div class="bg-white border rounded p-4">
    <?php if (empty($questions ?? [])): ?>
        <p class="text-muted mb-0">Belum ada soal. Klik "+ Soal" untuk menambahkan.</p>
    <?php else: ?>
        <ol class="list-group list-group-numbered">
            <?php foreach ($questions as $q): ?>
                <?php $opts = $optionsByQuestion[(int) $q['id']] ?? []; ?>
                <li class="list-group-item d-block bg-transparent">
                    <div class="d-flex justify-content-between gap-8">
                        <strong><?= esc($q['prompt']) ?></strong>
                        <span class="badge bg-primary-subtle text-primary-emphasis"><?= (float) $q['points'] ?> pts</span>
                    </div>
                    <ul class="list-unstyled mt-2 mb-0 small">
                        <?php foreach ($opts as $o): ?>
                            <li>
                                <span class="<?= ((int) $o['is_correct'] === 1) ? 'fw-semibold text-success' : 'text-muted' ?>">
                                    <?= ((int) $o['is_correct'] === 1) ? '✓ ' : '• ' ?><?= esc($o['option_text']) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (! empty($q['explanation'])): ?>
                        <p class="small text-muted mt-2 mb-0">Penjelasan: <?= esc($q['explanation']) ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</div>
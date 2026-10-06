<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2">Tambah Soal</h1>
    <p class="text-muted mb-0">Ujian: <?= esc($quiz['title'] ?? '-') ?> &middot; Kelas: <?= esc($className ?? '-') ?></p>
</div>

<div class="bg-white border rounded p-4">
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert"><?= esc($error) ?></div>
    <?php endif; ?>
    <form method="post" action="<?= route_to('workspace.trainer.question.create', $classId, $quiz['id']) ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-9">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Pertanyaan</label>
                <textarea name="prompt" class="form-control radius-8" rows="3" required></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Poin</label>
                <input type="number" name="points" min="0.01" step="0.01" value="1.00" class="form-control radius-8">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Penjelasan (opsional)</label>
                <textarea name="explanation" class="form-control radius-8" rows="2"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Pilihan A</label>
                <input type="text" name="options[]" class="form-control radius-8" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input type="radio" name="correct_option" value="0" id="correct_0" class="form-check-input" required>
                    <label class="form-check-label ms-2" for="correct_0">Benar</label>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Pilihan B</label>
                <input type="text" name="options[]" class="form-control radius-8" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input type="radio" name="correct_option" value="1" id="correct_1" class="form-check-input">
                    <label class="form-check-label ms-2" for="correct_1">Benar</label>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Pilihan C</label>
                <input type="text" name="options[]" class="form-control radius-8">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input type="radio" name="correct_option" value="2" id="correct_2" class="form-check-input">
                    <label class="form-check-label ms-2" for="correct_2">Benar</label>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Pilihan D</label>
                <input type="text" name="options[]" class="form-control radius-8">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input type="radio" name="correct_option" value="3" id="correct_3" class="form-check-input">
                    <label class="form-check-label ms-2" for="correct_3">Benar</label>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end gap-8">
                <a href="<?= route_to('workspace.trainer.quiz.edit', $classId, $quiz['id']) ?>" class="btn btn-outline-neutral-300 radius-8">Batal</a>
                <button type="submit" class="btn btn-primary radius-8">Simpan Soal</button>
            </div>
        </div>
    </form>
</div>
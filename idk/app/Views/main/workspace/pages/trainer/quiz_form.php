<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2">Trainer</p>
    <h1 class="mb-2">Tambah Ujian</h1>
    <p class="text-muted mb-0">Kelas: <?= esc($className ?? '-') ?></p>
</div>

<div class="bg-white border rounded p-4">
    <form method="post" action="<?= route_to('workspace.trainer.quiz.create', $classId) ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Tipe Ujian</label>
                <select name="type" id="quiz-type" class="form-select radius-8" required>
                    <option value="pre_test">Pre-test</option>
                    <option value="lesson_quiz" selected>Quiz</option>
                    <option value="post_test">Post-test</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Judul</label>
                <input type="text" name="title" class="form-control radius-8" required>
            </div>
            <div class="col-12" data-show="lesson_quiz">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Lesson</label>
                <select name="lesson_id" class="form-select radius-8">
                    <option value="">-- Pilih lesson --</option>
                    <?php foreach (($lessons ?? []) as $ls): ?>
                        <option value="<?= esc($ls['id']) ?>"><?= esc(($ls['module_title'] ?? 'Modul') . ' &middot; ' . ($ls['title'] ?? 'Lesson')) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Quiz lesson wajib terkait dengan satu lesson agar skor terhitung pada completion.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Attempt Limit</label>
                <input type="number" name="attempt_limit" min="1" value="1" class="form-control radius-8">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" name="is_required" value="1" id="is_required" class="form-check-input" checked>
                    <label class="form-check-label ms-2" for="is_required">Wajib</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-2">Instruksi</label>
                <textarea name="instructions" class="form-control radius-8" rows="3"></textarea>
            </div>
            <div class="col-12 d-flex justify-content-end gap-8">
                <a href="<?= route_to('workspace.trainer.quizzes', $classId) ?>" class="btn btn-outline-neutral-300 radius-8">Batal</a>
                <button type="submit" class="btn btn-primary radius-8">Simpan &amp; Tambah Soal</button>
            </div>
        </div>
    </form>
</div>
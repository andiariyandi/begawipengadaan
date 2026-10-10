<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?= view('dashboard/components/flash_message') ?>

<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <p class="text-uppercase text-muted small mb-2"><?= esc($sectionContent['eyebrow'] ?? 'Assessment dan Feedback') ?></p>
    <h1 class="mb-3"><?= esc($pageTitle ?? 'Penilaian') ?></h1>
    <p class="mb-0"><?= esc($sectionContent['description'] ?? 'Pantau pre-test, quiz, post-test, grading, nilai, dan feedback pembelajaran.') ?></p>
</div>

<?php if (!empty($sectionContent['cards'])): ?>
    <div class="row g-4 mb-4">
        <?php foreach ($sectionContent['cards'] as $card): ?>
            <div class="col-md-4">
                <div class="bg-white border rounded p-4 h-100">
                    <p class="text-muted mb-2"><?= esc($card['title']) ?></p>
                    <strong class="display-6"><?= esc($card['value']) ?></strong>
                    <small class="d-block text-muted mt-2"><?= esc($card['meta'] ?? '') ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($submission)): ?>
    <!-- Single Submission Detail & Grading Form -->
    <div class="bg-white border rounded p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Evaluasi Pengumpulan Tugas</h2>
            <a href="<?= site_url('workspace/evaluator/kelas/' . esc($submission['class_id'] ?? 1) . '/submission') ?>" class="btn btn-outline-secondary btn-sm">Kembali ke Daftar Submission</a>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th class="w-25 text-muted">Peserta</th><td><strong><?= esc($submission['participant_name'] ?? 'Peserta') ?></strong> (<?= esc($submission['participant_email'] ?? '') ?>)</td></tr>
                    <tr><th class="text-muted">Tugas</th><td><?= esc($submission['assignment_title'] ?? '-') ?></td></tr>
                    <tr><th class="text-muted">Percobaan ke</th><td><?= esc($submission['attempt_number'] ?? 1) ?></td></tr>
                    <tr><th class="text-muted">Waktu Kumpul</th><td><?= esc($submission['submitted_at'] ?? '-') ?></td></tr>
                    <tr><th class="text-muted">Status Saat Ini</th><td><span class="badge bg-secondary"><?= esc($submission['status'] ?? 'submitted') ?></span></td></tr>
                </table>
                <div class="p-3 bg-light rounded mt-3">
                    <h6 class="text-muted small text-uppercase">Jawaban / Konten Tugas:</h6>
                    <div class="mt-2"><?= nl2br(esc($submission['content'] ?? 'Tidak ada teks jawaban.')) ?></div>
                </div>
                <?php if (!empty($submissionFiles)): ?>
                    <div class="mt-3">
                        <h6 class="text-muted small text-uppercase">Berkas Lampiran:</h6>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($submissionFiles as $f): ?>
                                <li><a href="<?= site_url('storage/file/' . esc($f['file_id'])) ?>" target="_blank" class="text-decoration-none">📎 <?= esc($f['original_name'] ?? 'Unduh Berkas') ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6 border-start">
                <h5 class="mb-3">Form Nilai & Umpan Balik</h5>
                <form method="post" action="<?= site_url('workspace/evaluator/kelas/' . esc($submission['class_id'] ?? 1) . '/submission/' . esc($submission['id']) . '/nilai') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nilai Angka (0 - 100)</label>
                        <input type="number" step="0.1" min="0" max="100" name="score" class="form-control" value="<?= esc($submission['score'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Evaluasi</label>
                        <select name="status" class="form-select">
                            <option value="graded" <?= ($submission['status'] ?? '') === 'graded' ? 'selected' : '' ?>>Telah Dinilai (Graded)</option>
                            <option value="revision_required" <?= ($submission['status'] ?? '') === 'revision_required' ? 'selected' : '' ?>>Perlu Revisi (Revision Required)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan / Umpan Balik (Feedback)</label>
                        <textarea name="feedback" class="form-control" rows="5" placeholder="Berikan arahan perbaikan atau apresiasi terhadap hasil kerja peserta..."><?= esc($submission['feedback'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Penilaian</button>
                </form>
            </div>
        </div>
    </div>
<?php elseif (!empty($submissionsList)): ?>
    <!-- Submissions Table -->
    <div class="bg-white border rounded p-4 mb-4">
        <h2 class="h4 mb-3">Daftar Pengumpulan Tugas Peserta</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Peserta</th>
                        <th>Tugas</th>
                        <th>Percobaan</th>
                        <th>Tanggal Kumpul</th>
                        <th>Nilai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissionsList)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada tugas yang dikumpulkan untuk kelas ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($submissionsList as $sub): ?>
                            <tr>
                                <td><strong><?= esc($sub['participant_name'] ?? 'Peserta') ?></strong><br><small class="text-muted"><?= esc($sub['participant_email'] ?? '') ?></small></td>
                                <td><?= esc($sub['assignment_title'] ?? '-') ?></td>
                                <td>Ke-<?= esc($sub['attempt_number'] ?? 1) ?></td>
                                <td><?= esc($sub['submitted_at'] ?? '-') ?></td>
                                <td><strong><?= $sub['score'] !== null ? esc($sub['score']) : '-' ?></strong></td>
                                <td>
                                    <span class="badge <?= ($sub['status'] ?? '') === 'graded' ? 'bg-success' : (($sub['status'] ?? '') === 'revision_required' ? 'bg-warning text-dark' : 'bg-primary') ?>">
                                        <?= esc($sub['status'] ?? 'submitted') ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= site_url('workspace/evaluator/kelas/' . esc($sub['class_id']) . '/submission/' . esc($sub['id'])) ?>" class="btn btn-sm btn-outline-primary">Periksa / Nilai</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif (!empty($gradingList)): ?>
    <!-- Class Grade Sheet -->
    <div class="bg-white border rounded p-4 mb-4">
        <h2 class="h4 mb-3">Rekap Nilai Peserta Kelas</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Peserta</th>
                        <th>Tanggal Terdaftar</th>
                        <th>Nilai Akhir</th>
                        <th>Status Kelulusan</th>
                        <th>Sertifikat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gradingList as $g): ?>
                        <tr>
                            <td><strong><?= esc($g['user_name'] ?? 'Peserta') ?></strong><br><small class="text-muted"><?= esc($g['user_email'] ?? '') ?></small></td>
                            <td><?= esc($g['enrolled_at'] ?? '-') ?></td>
                            <td><strong><?= $g['final_score'] !== null ? esc($g['final_score']) : '-' ?></strong></td>
                            <td>
                                <span class="badge <?= ($g['status'] ?? '') === 'completed' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= esc($g['status'] ?? 'active') ?>
                                </span>
                            </td>
                            <td><?= !empty($g['certificate_eligible_at']) ? '✅ Memenuhi Syarat' : 'Belum Eligible' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif (!empty($sectionContent['items'])): ?>
    <div class="bg-white border rounded p-4">
        <h2 class="h4 mb-3">Daftar Item / Aktivitas</h2>
        <div class="list-group list-group-flush">
            <?php foreach ($sectionContent['items'] as $item): ?>
                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                    <span><?= is_array($item) ? esc($item['title'] ?? '') : esc($item) ?></span>
                    <?php if (is_array($item) && !empty($item['url'])): ?>
                        <a href="<?= esc($item['url']) ?>" class="btn btn-outline-primary btn-sm">Buka</a>
                    <?php else: ?>
                        <span class="badge bg-light text-success">Aktif</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

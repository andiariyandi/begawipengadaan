<?php
/**
 * Trainer - Form Ubah Detail Kelas
 * FiStudy UI DNA
 */
$class      = $class ?? [];
$classId    = (int) ($class['id'] ?? 0);
$parsedDesc = $parsedDesc ?? parse_class_batch_description($class['description'] ?? '');
$stages     = $parsedDesc['stages'] ?? [];
$targetUrl  = route_to('workspace.trainer.class.update', (string) $classId);
?>

<div class="trainer-class-edit-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-edit me-1"></i> Manajemen Pengajar
                </span>
                <h1 class="h3 fw-bold text-navy mb-1">Ubah Detail Informasi Kelas</h1>
                <p class="text-muted small mb-0">Perbarui parameter jadwal, metode, kuota, tahapan pembelajaran, dan asesmen kelas.</p>
            </div>
            <div>
                <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Kelas
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Flash -->
    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $targetUrl ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- KARTU 1: PROGRAM & COURSE (TERKUNCI / ASSIGN ADMIN) -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-3">
                <h2 class="h5 fw-bold text-navy mb-0">
                    <i class="fas fa-lock text-muted me-2"></i>Program & Course Terkait
                </h2>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 small">
                    <i class="fas fa-shield-alt me-1"></i> Ditetapkan oleh Administrator (Terkunci)
                </span>
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted fw-semibold mb-1">Program Pelatihan / Sertifikasi</label>
                    <div class="p-3 rounded-3 bg-light border text-navy fw-semibold small d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-layer-group text-primary me-2"></i><?= esc($class['program_title'] ?? 'Program Umum PBJ') ?></span>
                        <i class="fas fa-lock text-muted opacity-50"></i>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Hanya Administrator yang dapat memindahkan program.</small>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted fw-semibold mb-1">Course / Kurikulum Induk</label>
                    <div class="p-3 rounded-3 bg-light border text-navy fw-semibold small d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-graduation-cap text-success me-2"></i><?= esc($class['course_title'] ?? 'Course PBJ') ?></span>
                        <i class="fas fa-lock text-muted opacity-50"></i>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Standar silabus, kompetensi, dan level course diatur oleh Administrator.</small>
                </div>
            </div>
        </div>

        <!-- KARTU 2: IDENTITAS & OPERASIONAL KELAS -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <h2 class="h5 fw-bold text-navy mb-3 pb-2 border-bottom">
                <i class="fas fa-info-circle text-primary me-2"></i>Informasi & Penyelenggaraan Kelas
            </h2>
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold text-navy small mb-1">Nama Kelas / Batch <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control rounded-3 py-2" value="<?= esc($class['name'] ?? old('name')) ?>" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Kode Kelas</label>
                    <input type="text" name="code" class="form-control rounded-3 py-2 font-monospace" value="<?= esc($class['code'] ?? old('code')) ?>" placeholder="Misal: PBJ-2026-01">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Metode Pembelajaran</label>
                    <select name="learning_method" class="form-select rounded-3 py-2">
                        <option value="online" <?= (($class['learning_method'] ?? 'online') === 'online') ? 'selected' : '' ?>>Online Penuh (LMS & Daring)</option>
                        <option value="offline" <?= (($class['learning_method'] ?? '') === 'offline') ? 'selected' : '' ?>>Tatap Muka / Klasikal</option>
                        <option value="blended" <?= (($class['learning_method'] ?? '') === 'blended' || ($class['learning_method'] ?? '') === 'hybrid') ? 'selected' : '' ?>>Blended / Hybrid (LMS + Tatap Muka)</option>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label fw-semibold text-navy small mb-1">Lokasi Ruang / Tautan Tatap Maya</label>
                    <input type="text" name="location_or_link" class="form-control rounded-3 py-2" value="<?= esc($class['location_or_link'] ?? old('location_or_link')) ?>" placeholder="Contoh: Zoom Meeting Link atau Ruang Lab Komputer BKD">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Kapasitas Kursi (Kuota)</label>
                    <input type="number" name="capacity" min="0" class="form-control rounded-3 py-2" value="<?= (int) ($class['capacity'] ?? old('capacity') ?? 0) ?>">
                    <small class="text-muted" style="font-size: 0.72rem;">0 = tanpa batas kuota</small>
                </div>

                <!-- Periode Belajar -->
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Buka Pendaftaran</label>
                    <input type="datetime-local" name="registration_open_at" class="form-control rounded-3 py-2" value="<?= ! empty($class['registration_open_at']) ? date('Y-m-d\TH:i', strtotime($class['registration_open_at'])) : '' ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Tutup Pendaftaran</label>
                    <input type="datetime-local" name="registration_close_at" class="form-control rounded-3 py-2" value="<?= ! empty($class['registration_close_at']) ? date('Y-m-d\TH:i', strtotime($class['registration_close_at'])) : '' ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Waktu Mulai Kelas</label>
                    <input type="datetime-local" name="start_at" class="form-control rounded-3 py-2" value="<?= ! empty($class['start_at']) ? date('Y-m-d\TH:i', strtotime($class['start_at'])) : '' ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Waktu Selesai Kelas</label>
                    <input type="datetime-local" name="end_at" class="form-control rounded-3 py-2" value="<?= ! empty($class['end_at']) ? date('Y-m-d\TH:i', strtotime($class['end_at'])) : '' ?>">
                </div>

                <!-- Status & Banner -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Status Penyelenggaraan</label>
                    <?php $curClassStatus = strtolower((string) (old('status', $class['status'] ?? 'active'))); ?>
                    <select name="status" class="form-select rounded-3 py-2 fw-semibold" id="classStatusSelect">
                        <option value="active" <?= in_array($curClassStatus, ['active', 'ongoing', 'berlangsung', 'aktif'], true) ? 'selected' : '' ?>>
                            Aktif / Sedang Berjalan (Tayang di Landing Page)
                        </option>
                        <option value="scheduled" <?= in_array($curClassStatus, ['scheduled', 'open', 'published', 'terbuka'], true) ? 'selected' : '' ?>>
                            Jadwal Terbuka / Pendaftaran Dibuka (Tayang di Landing Page)
                        </option>
                        <option value="completed" <?= in_array($curClassStatus, ['completed', 'closed', 'selesai'], true) ? 'selected' : '' ?>>
                            Selesai (Completed)
                        </option>
                        <option value="draft" <?= in_array($curClassStatus, ['draft', 'archived', 'draf'], true) ? 'selected' : '' ?>>
                            Draft (Draf Awal / Tidak Tayang Publik)
                        </option>
                    </select>
                    <small class="text-muted" style="font-size: 0.72rem;">Pilih <strong>Aktif</strong> atau <strong>Jadwal Terbuka</strong> agar kelas otomatis tayang di landing page publik.</small>
                </div>
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold text-navy small mb-1">Ganti Cover / Banner Kelas</label>
                    <input type="file" name="cover_image" accept="image/*" class="form-control rounded-3 py-2">
                    <small class="text-muted" style="font-size: 0.72rem;">Format JPG/PNG maks. 2MB. Kosongkan bila ingin mempertahankan cover saat ini.</small>
                </div>
            </div>
        </div>

        <!-- KARTU 3: STANDAR KELULUSAN & BOBOT PENILAIAN -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <h2 class="h5 fw-bold text-navy mb-3 pb-2 border-bottom">
                <i class="fas fa-award text-success me-2"></i>Standar Kelulusan & Bobot Asesmen
            </h2>
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Passing Grade (Nilai Minimal)</label>
                    <input type="number" step="0.1" min="0" max="100" name="passing_grade" class="form-control rounded-3 py-2" value="<?= esc($class['passing_grade'] ?? 70.0) ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Bobot Kuis Modul (%)</label>
                    <input type="number" step="1" min="0" max="100" name="quiz_weight" class="form-control rounded-3 py-2" value="<?= esc($class['quiz_weight'] ?? 20.0) ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Bobot Tugas / Kasus (%)</label>
                    <input type="number" step="1" min="0" max="100" name="assignment_weight" class="form-control rounded-3 py-2" value="<?= esc($class['assignment_weight'] ?? 40.0) ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-navy small mb-1">Bobot Post-Test (%)</label>
                    <input type="number" step="1" min="0" max="100" name="post_test_weight" class="form-control rounded-3 py-2" value="<?= esc($class['post_test_weight'] ?? 40.0) ?>">
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="certificate_enabled" value="1" id="certificate_enabled" <?= ! empty($class['certificate_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold text-navy small" for="certificate_enabled">
                            Terbitkan Sertifikat Resmi PBJ ber-QR Code bagi peserta yang lulus kelas ini
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- KARTU 4: SASARAN, DESKRIPSI & TAHAPAN PEMBELAJARAN BATCH -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <h2 class="h5 fw-bold text-navy mb-3 pb-2 border-bottom">
                <i class="fas fa-route text-primary me-2"></i>Sasaran, Deskripsi & Tahapan Pembelajaran
            </h2>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold text-navy small mb-1">Sasaran Khusus Peserta</label>
                    <textarea name="target_audience" class="form-control rounded-3" rows="2" placeholder="Contoh: PPK, Pejabat Pengadaan, Pokja Pemilihan UKPBJ, dan ASN yang mempersiapkan sertifikasi level 1."><?= esc($class['target_audience'] ?? old('target_audience')) ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold text-navy small mb-1">Deskripsi & Gambaran Umum Kelas</label>
                    <textarea name="description" class="form-control rounded-3" rows="4" placeholder="Jelaskan tujuan, materi pokok, dan fasilitas pembelajaran kelas ini..."><?= esc($parsedDesc['description'] ?? '') ?></textarea>
                </div>

                <!-- Dynamic Tahapan Pembelajaran -->
                <div class="col-12 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <strong class="text-navy small d-block">Tahapan Pembelajaran Batch (Alur Belajar Peserta)</strong>
                            <span class="text-muted" style="font-size: 0.74rem;">Tahapan ini ditampilkan pada halaman utama kelas dan ruang belajar peserta.</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addStageItem()">
                            <i class="fas fa-plus me-1"></i> Tambah Tahapan
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Subjudul / Pengantar Tahapan</label>
                        <input type="text" name="stages_subtitle" class="form-control rounded-3 form-control-sm" value="<?= esc($parsedDesc['stages_subtitle'] ?? '') ?>" placeholder="Contoh: Alur pengerjaan yang harus dilalui oleh peserta selama periode kelas berlangsung:">
                    </div>

                    <div id="stagesContainer" class="d-flex flex-column gap-2">
                        <?php foreach ($stages as $idx => $stg): ?>
                            <div class="card border rounded-3 p-3 bg-light stage-card">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-primary rounded-pill px-2 py-1 small stage-num-badge">Tahapan <?= $idx + 1 ?></span>
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" onclick="removeStageItem(this)">
                                        <i class="fas fa-trash-alt me-1"></i> Hapus
                                    </button>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <input type="text" name="stages[<?= $idx ?>][title]" class="form-control form-control-sm bg-white" placeholder="Judul Tahapan (Contoh: Orientasi & Pre-Test)" value="<?= esc($stg['title'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <textarea name="stages[<?= $idx ?>][description]" class="form-control form-control-sm bg-white" rows="2" placeholder="Uraian kegiatan tahapan..."><?= esc($stg['description'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Bawah -->
        <div class="d-flex justify-content-end gap-3 mb-5">
            <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>" class="btn btn-outline-secondary rounded-pill px-4">
                Batal
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                <i class="fas fa-save me-1"></i> Simpan Perubahan Detail Kelas
            </button>
        </div>
    </form>
</div>

<script>
function addStageItem() {
    const container = document.getElementById('stagesContainer');
    const count = container.querySelectorAll('.stage-card').length;
    const newIdx = count;

    const html = `
        <div class="card border rounded-3 p-3 bg-light stage-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge bg-primary rounded-pill px-2 py-1 small stage-num-badge">Tahapan ${count + 1}</span>
                <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" onclick="removeStageItem(this)">
                    <i class="fas fa-trash-alt me-1"></i> Hapus
                </button>
            </div>
            <div class="row g-2">
                <div class="col-12">
                    <input type="text" name="stages[${newIdx}][title]" class="form-control form-control-sm bg-white" placeholder="Judul Tahapan (Contoh: Modul Pembelajaran & Kuis)" required>
                </div>
                <div class="col-12">
                    <textarea name="stages[${newIdx}][description]" class="form-control form-control-sm bg-white" rows="2" placeholder="Uraian kegiatan tahapan..."></textarea>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function removeStageItem(btn) {
    const card = btn.closest('.stage-card');
    if (card) {
        card.remove();
        // Update stage number badges
        const cards = document.querySelectorAll('#stagesContainer .stage-card');
        cards.forEach((c, idx) => {
            const badge = c.querySelector('.stage-num-badge');
            if (badge) badge.innerText = 'Tahapan ' + (idx + 1);
        });
    }
}
</script>

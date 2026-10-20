<?php
/**
 * Trainer - Form Lesson / Materi
 * FiStudy UI DNA - Enhanced Multi-Format (PDF, Video/YouTube, Link, Text)
 */
$mode       = $mode ?? 'create';
$classId    = $classId ?? 0;
$module     = $module ?? [];
$lesson     = $lesson ?? null;
$fileAsset  = $fileAsset ?? null;
$className  = $className ?? 'Kelas';
$targetUrl  = ($mode === 'edit')
    ? route_to('workspace.trainer.lesson.edit', (string) $classId, (string) ($lesson['id'] ?? 0))
    : route_to('workspace.trainer.lesson.create', (string) $classId, (string) ($module['id'] ?? 0));
$currentAccess = $lesson['visibility'] ?? 'internal';
?>

<div class="trainer-lesson-form-wrapper">
    <!-- Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill small fw-semibold mb-2">
                    <i class="fas fa-book-reader me-1"></i> Materi Pembelajaran
                </span>
                <h1 class="h3 fw-bold text-navy mb-1"><?= $mode === 'edit' ? 'Ubah Materi Pembelajaran' : 'Tambah Materi Baru' ?></h1>
                <p class="text-muted small mb-0">
                    Modul: <strong><?= esc($module['title'] ?? '-') ?></strong> &middot; Kelas: <strong><?= esc($className) ?></strong>
                </p>
            </div>
            <div>
                <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=materi" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Silabus
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-lg-5">
        <?php if ($error = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?= esc($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $targetUrl ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-4">
                <!-- Judul Materi -->
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold text-navy small mb-1">Judul Materi <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3 py-2" placeholder="Contoh: Modul Regulasi PBJ Pemerintah Perpres 12/2021" value="<?= esc($lesson['title'] ?? old('title')) ?>" required>
                </div>

                <!-- Tipe Materi -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Tipe Materi <span class="text-danger">*</span></label>
                    <select name="content_type" id="content_type_select" class="form-select rounded-3 py-2" required onchange="toggleContentFields(this.value)">
                        <?php foreach (['text' => 'Teks / Catatan Pembelajaran', 'document' => 'Dokumen PDF (Upload & In-App Viewer)', 'video' => 'Video Pembelajaran (YouTube / Embed)', 'link' => 'Tautan / Referensi Luar'] as $key => $label): ?>
                            <option value="<?= $key ?>" <?= (($lesson['content_type'] ?? old('content_type') ?? 'text') === $key) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Urutan Pembelajaran -->
                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Urutan Pembelajaran</label>
                    <input type="number" name="sort_order" class="form-control rounded-3 py-2" value="<?= (int) ($lesson['sort_order'] ?? old('sort_order') ?? 0) ?>" min="0">
                    <small class="text-muted" style="font-size: 0.72rem;">0 = otomatis di urutan berikutnya</small>
                </div>

                <!-- Status Publikasi -->
                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Status Publikasi</label>
                    <select name="status" class="form-select rounded-3 py-2">
                        <option value="published" <?= (($lesson['status'] ?? old('status') ?? 'published') === 'published') ? 'selected' : '' ?>>Published (Langsung Dapat Diakses)</option>
                        <option value="draft" <?= (($lesson['status'] ?? old('status')) === 'draft') ? 'selected' : '' ?>>Draft (Belum Tampil di Peserta)</option>
                    </select>
                </div>

                <!-- Kelayakan Selesai -->
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-navy small mb-1">Kelayakan</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_required" value="1" id="is_required" class="form-check-input"
                            <?= ($mode === 'edit')
                                ? (((int) ($lesson['is_required'] ?? 0) === 1) ? 'checked' : '')
                                : 'checked' ?>>
                        <label class="form-check-label fw-semibold text-navy small" for="is_required">
                            Wajib Ditandai Selesai
                        </label>
                    </div>
                </div>

                <!-- ==================== BAGIAN DOKUMEN PDF ==================== -->
                <div class="col-12" id="field_document_section" style="display: none;">
                    <div class="card border rounded-4 bg-light p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-file-pdf text-danger fs-4"></i>
                            <div>
                                <h2 class="h6 fw-bold text-navy mb-0">Pengelolaan Berkas Materi PDF</h2>
                                <small class="text-muted">Unggah berkas PDF materi, atur hak akses peserta, dan tinjau berkas secara langsung.</small>
                            </div>
                        </div>

                        <!-- Existing File Card (Jika Mode Edit dan Berkas Ada) -->
                        <?php if ($fileAsset !== null): ?>
                            <div class="p-3 bg-white border rounded-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-3 shadow-sm" id="existing_file_box">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 p-2 bg-danger-subtle text-danger fs-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <strong class="text-navy small d-block"><?= esc($fileAsset['original_name'] ?? 'Berkas-Materi.pdf') ?></strong>
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            Ukuran: <?= round(($fileAsset['size_bytes'] ?? 0) / 1024, 1) ?> KB &middot; Format: Dokumen PDF
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="<?= base_url('file/' . $fileAsset['id']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-eye me-1"></i> Preview Berkas
                                    </a>
                                    <div class="form-check ms-2">
                                        <input type="checkbox" name="remove_pdf" value="1" id="remove_pdf_check" class="form-check-input" onchange="toggleRemoveFile(this.checked)">
                                        <label class="form-check-label text-danger small fw-semibold" for="remove_pdf_check">
                                            Hapus Berkas Ini
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- File Input -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-navy small mb-1" id="file_upload_label">
                                <?= $fileAsset !== null ? 'Ganti File PDF (Opsional)' : 'Unggah File PDF Materi <span class="text-danger">*</span>' ?>
                            </label>
                            <input type="file" name="pdf_file" id="pdf_file_input" class="form-control rounded-3 py-2 bg-white" accept=".pdf,application/pdf" onchange="handlePdfUploadPreview(this)">
                            <small class="text-muted d-block mt-1">Mendukung format PDF (.pdf). Maksimal ukuran yang disarankan 50 MB.</small>
                        </div>

                        <!-- Akses Peserta Terhadap Dokumen -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-navy small mb-2">Pengaturan Akses Unduh Peserta</label>
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <div class="form-check p-3 bg-white border rounded-3 h-100">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="document_access" id="access_preview_only" value="restricted" <?= in_array($currentAccess, ['restricted', 'preview_only'], true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="access_preview_only">
                                            <strong class="text-navy small d-block"><i class="fas fa-shield-alt text-warning me-1"></i> Hanya Lihat (Viewer In-App)</strong>
                                            <span class="text-muted" style="font-size: 0.75rem;">Peserta membaca dokumen melalui viewer platform. Tombol unduh tidak ditampilkan.</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-check p-3 bg-white border rounded-3 h-100">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="document_access" id="access_downloadable" value="internal" <?= ! in_array($currentAccess, ['restricted', 'preview_only'], true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="access_downloadable">
                                            <strong class="text-navy small d-block"><i class="fas fa-download text-success me-1"></i> Boleh Diunduh (Full Access)</strong>
                                            <span class="text-muted" style="font-size: 0.75rem;">Peserta dapat melihat dokumen dan mengunduh berkas fisik PDF untuk dipelajari offline.</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Live Client-side PDF Preview Box -->
                        <div id="pdf_client_preview_box" style="display: none;" class="mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <strong class="small text-navy"><i class="fas fa-search me-1"></i> Preview Dokumen Baru Sebelum Disimpan:</strong>
                                <span class="badge bg-primary-subtle text-primary small" id="preview_file_name_badge">Preview Aktif</span>
                            </div>
                            <div class="border rounded-3 overflow-hidden bg-white shadow-sm" style="height: 480px;">
                                <iframe id="pdf_preview_iframe" class="w-100 h-100 border-0" src="about:blank"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== BAGIAN VIDEO & LINK ==================== -->
                <div class="col-12" id="field_external_url" style="display: none;">
                    <div class="card border rounded-4 bg-light p-4">
                        <label class="form-label fw-semibold text-navy small mb-1" id="url_label">URL Tautan / Video <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-link"></i></span>
                            <input type="url" name="external_url" id="external_url_input" class="form-control rounded-end-3 py-2" placeholder="https://www.youtube.com/watch?v=... atau https://vimeo.com/..." value="<?= esc($lesson['external_url'] ?? old('external_url')) ?>" oninput="handleUrlLivePreview(this.value)">
                        </div>
                        <small class="text-muted mt-1" id="url_help_text">Mendukung tautan YouTube, Vimeo, atau media online.</small>

                        <!-- Live YouTube / Video Player Preview -->
                        <div id="video_preview_box" class="mt-3" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <strong class="small text-navy"><i class="fab fa-youtube text-danger me-1"></i> Live Player Preview:</strong>
                                <span class="badge bg-success-subtle text-success small">Player Aktif (Workspace Peserta)</span>
                            </div>
                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm border bg-black">
                                <iframe id="video_preview_iframe" src="" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== BAGIAN ISI KONTEN TEKS ==================== -->
                <div class="col-12" id="field_content">
                    <label class="form-label fw-semibold text-navy small mb-1">
                        <span id="content_field_label">Isi Konten Teks / Catatan Pembelajaran</span>
                    </label>
                    <textarea name="content" class="form-control rounded-3" rows="8" placeholder="Tuliskan uraian materi, penjelasan konsep, studi kasus, atau rangkuman materi di sini..."><?= esc($lesson['content'] ?? old('content')) ?></textarea>
                    <small class="text-muted mt-1 d-block">Catatan atau uraian materi ini akan ditampilkan sebagai panduan membaca bagi peserta.</small>
                </div>

                <!-- Tombol Aksi Simpan -->
                <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
                    <a href="<?= route_to('workspace.trainer.class.detail', (string) $classId) ?>?tab=materi" class="btn btn-outline-secondary rounded-pill px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="fas fa-save me-1"></i> <?= $mode === 'edit' ? 'Simpan Perubahan' : 'Tambahkan Materi' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleContentFields(type) {
    const urlField       = document.getElementById('field_external_url');
    const docField       = document.getElementById('field_document_section');
    const contentLabel   = document.getElementById('content_field_label');
    const urlLabel       = document.getElementById('url_label');
    const urlHelp        = document.getElementById('url_help_text');

    if (type === 'video') {
        urlField.style.display = 'block';
        docField.style.display = 'none';
        urlLabel.innerHTML = 'URL Video Pembelajaran (YouTube / Vimeo) <span class="text-danger">*</span>';
        urlHelp.textContent = 'Mendukung tautan YouTube (youtube.com atau youtu.be) dan Vimeo. Video dapat diputar langsung di workspace peserta tanpa keluar platform.';
        contentLabel.textContent = 'Catatan & Rangkuman Materi Video (Opsional)';
        handleUrlLivePreview(document.getElementById('external_url_input').value);
    } else if (type === 'link') {
        urlField.style.display = 'block';
        docField.style.display = 'none';
        urlLabel.innerHTML = 'URL Tautan Referensi Luar <span class="text-danger">*</span>';
        urlHelp.textContent = 'Masukkan URL tautan website, artikel jurnal, atau dokumen eksternal.';
        contentLabel.textContent = 'Penjelasan & Panduan Tautan (Opsional)';
        document.getElementById('video_preview_box').style.display = 'none';
    } else if (type === 'document') {
        urlField.style.display = 'none';
        docField.style.display = 'block';
        contentLabel.textContent = 'Panduan Membaca & Catatan Modul PDF (Opsional)';
        document.getElementById('video_preview_box').style.display = 'none';
    } else {
        urlField.style.display = 'none';
        docField.style.display = 'none';
        contentLabel.textContent = 'Isi Konten Teks Pembelajaran <span class="text-danger">*</span>';
        document.getElementById('video_preview_box').style.display = 'none';
    }
}

function handlePdfUploadPreview(input) {
    const previewBox = document.getElementById('pdf_client_preview_box');
    const iframe     = document.getElementById('pdf_preview_iframe');
    const badge      = document.getElementById('preview_file_name_badge');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
            alert('Silakan pilih berkas berekstensi .pdf!');
            input.value = '';
            previewBox.style.display = 'none';
            return;
        }

        const objectUrl = URL.createObjectURL(file);
        iframe.src = objectUrl;
        badge.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        previewBox.style.display = 'block';
    } else {
        previewBox.style.display = 'none';
        iframe.src = 'about:blank';
    }
}

function toggleRemoveFile(isRemove) {
    const uploadLabel = document.getElementById('file_upload_label');
    if (isRemove) {
        uploadLabel.innerHTML = 'Unggah File PDF Pengganti (Wajib Jika File Dihapus) <span class="text-danger">*</span>';
    } else {
        uploadLabel.innerHTML = 'Ganti File PDF (Opsional)';
    }
}

function handleUrlLivePreview(url) {
    const typeSelect = document.getElementById('content_type_select').value;
    const previewBox = document.getElementById('video_preview_box');
    const iframe     = document.getElementById('video_preview_iframe');

    if (typeSelect !== 'video' || !url || url.trim() === '') {
        previewBox.style.display = 'none';
        iframe.src = '';
        return;
    }

    url = url.trim();
    let embedUrl = null;

    // Deteksi YouTube
    const ytMatch = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|live)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
    if (ytMatch && ytMatch[1]) {
        embedUrl = 'https://www.youtube.com/embed/' + ytMatch[1] + '?rel=0&modestbranding=1';
    } else {
        // Deteksi Vimeo
        const vimeoMatch = url.match(/vimeo\.com\/(?:video\/)?([0-9]+)/);
        if (vimeoMatch && vimeoMatch[1]) {
            embedUrl = 'https://player.vimeo.com/video/' + vimeoMatch[1];
        }
    }

    if (embedUrl) {
        iframe.src = embedUrl;
        previewBox.style.display = 'block';
    } else {
        previewBox.style.display = 'none';
        iframe.src = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleContentFields(document.getElementById('content_type_select').value);
    const initialUrl = document.getElementById('external_url_input').value;
    if (initialUrl) {
        handleUrlLivePreview(initialUrl);
    }
});
</script>
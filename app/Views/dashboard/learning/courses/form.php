<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$course = $course ?? [];
$isEdit = ($formMode ?? 'create') === 'edit';
$formAction = $isEdit ? route_to('dashboard.learning.courses.update', $course['id']) : route_to('dashboard.learning.courses.store');
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Course') ?></h6>
        <p class="text-secondary-light mb-0">Kelola dan sesuaikan materi kursus pembelajaran PBJ.</p>
    </div>
    <a href="<?= route_to('dashboard.learning.courses') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-linear" class="text-xl"></iconify-icon>
        <span>Kembali ke Daftar</span>
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= esc($formAction) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- 1. IDENTITAS & PROGRAM INDUK -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:book-bookmark-outline" class="text-lg"></iconify-icon>
                    <span>1. Informasi Pokok & Induk Kurikulum</span>
                </h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="program_id">Program Kurikulum Induk <span class="text-danger-main">*</span></label>
                        <select id="program_id" name="program_id" class="form-select radius-8" required>
                            <option value="">-- Pilih Program Induk --</option>
                            <?php $programId = old('program_id', $course['program_id'] ?? ''); foreach ($programs as $prog): ?>
                                <option value="<?= esc($prog['id']) ?>" <?= (string) $programId === (string) $prog['id'] ? 'selected' : '' ?>>
                                    <?= esc($prog['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="title">Judul Course <span class="text-danger-main">*</span></label>
                        <input id="title" type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $course['title'] ?? '')) ?>" required placeholder="Contoh: Pengantar Pengadaan Barang & Jasa Pemerintah">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="slug">Slug URL <span class="text-danger-main">*</span></label>
                        <input id="slug" type="text" name="slug" class="form-control radius-8 font-monospace" value="<?= esc(old('slug', $course['slug'] ?? '')) ?>" required placeholder="Contoh: pengantar-pbj-dasar">
                        <small class="text-muted text-xs">Digunakan sebagai permalink di Arena Belajar: <code>/arena-belajar/course/{slug}</code></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="level">Tingkat Kesulitan (Level)</label>
                        <select id="level" name="level" class="form-select radius-8">
                            <?php $level = old('level', $course['level'] ?? 'basic'); ?>
                            <option value="basic" <?= $level === 'basic' ? 'selected' : '' ?>>Dasar (Basic) - Pemula & Pengantar</option>
                            <option value="intermediate" <?= $level === 'intermediate' ? 'selected' : '' ?>>Menengah (Intermediate) - Praktik & Pemilihan</option>
                            <option value="advanced" <?= $level === 'advanced' ? 'selected' : '' ?>>Lanjutan (Advanced) - Manajemen Kontrak & Audit</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="duration_minutes">
                            Estimasi Durasi Belajar (Menit)
                            <span class="badge bg-primary-100 text-primary-600 text-xs px-2 py-0.5 radius-4 ms-1" id="course_jp_badge">
                                <?= format_learning_jp(old('duration_minutes', $course['duration_minutes'] ?? 120)) ?>
                            </span>
                        </label>
                        <input id="duration_minutes" type="number" min="0" step="5" name="duration_minutes" class="form-control radius-8" value="<?= esc(old('duration_minutes', $course['duration_minutes'] ?? 120)) ?>" placeholder="Contoh: 120" oninput="updateCourseJpBadge(this.value)">
                        <small class="text-secondary-light text-xs mt-1 d-block">
                            <iconify-icon icon="solar:info-circle-linear" class="align-middle"></iconify-icon> Standar PBJ: <strong>1 JP = 60 menit pembelajaran</strong>.
                        </small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="status">Status Tayang</label>
                        <select id="status" name="status" class="form-select radius-8">
                            <?php $status = old('status', $course['status'] ?? 'draft'); ?>
                            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published (Tayang di Arena Belajar)</option>
                            <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="visibility">Visibilitas</label>
                        <select id="visibility" name="visibility" class="form-select radius-8">
                            <?php $vis = old('visibility', $course['visibility'] ?? 'public'); ?>
                            <option value="public" <?= $vis === 'public' ? 'selected' : '' ?>>Public (Katalog Terbuka)</option>
                            <option value="internal" <?= $vis === 'internal' ? 'selected' : '' ?>>Internal</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1" <?= old('featured', $course['featured'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-primary-light text-sm ms-2" for="featured">
                                <iconify-icon icon="solar:star-bold" class="text-warning align-middle"></iconify-icon>
                                Sorotan / Unggulan di Arena Belajar
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. DESKRIPSI & RINGKASAN -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:document-text-outline" class="text-lg"></iconify-icon>
                    <span>2. Ringkasan & Deskripsi Course</span>
                </h6>
                <div class="row gy-20">
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="summary">Ringkasan Singkat (Lead Summary)</label>
                        <textarea id="summary" name="summary" class="form-control radius-8" rows="2" placeholder="Tuliskan 1-2 kalimat ringkasan materi untuk kartu katalog course..."><?= esc(old('summary', $course['summary'] ?? '')) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="description">Deskripsi Lengkap Materi</label>
                        <textarea id="description" name="description" class="form-control radius-8" rows="5" placeholder="Penjelasan lengkap mengenai topik pembahasan, urgensi materi, dan silabus yang akan dipelajari..."><?= esc(old('description', $course['description'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 3. SASARAN, PRASYARAT & TARGET KOMPETENSI -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:diploma-verified-outline" class="text-lg"></iconify-icon>
                    <span>3. Sasaran Peserta, Prasyarat & Capaian Kompetensi</span>
                </h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="target_audience">Target Sasaran Peserta</label>
                        <textarea id="target_audience" name="target_audience" class="form-control radius-8" rows="3" placeholder="Contoh: Pengelola pengadaan pemula, ASN perencana, panitia pengadaan desa..."><?= esc(old('target_audience', $course['target_audience'] ?? '')) ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="prerequisites">Prasyarat Course</label>
                        <textarea id="prerequisites" name="prerequisites" class="form-control radius-8" rows="3" placeholder="Contoh: Memahami struktur anggaran APBD/APBN atau telah lulus course pengantar dasar..."><?= esc(old('prerequisites', $course['prerequisites'] ?? '')) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="learning_objectives">Target Capaian Kompetensi Course</label>
                        <textarea id="learning_objectives" name="learning_objectives" class="form-control radius-8" rows="4" placeholder="Tuliskan poin-poin kompetensi teknis yang dikuasai setelah menyelesaikan course ini..."><?= esc(old('learning_objectives', $course['learning_objectives'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 4. MEDIA & BANNER COURSE -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:gallery-wide-outline" class="text-lg"></iconify-icon>
                    <span>4. Gambar Cover & Media Sampul Course</span>
                </h6>

                <!-- Box Rekomendasi Dimensi & Ukuran -->
                <div class="alert alert-info border border-info-200 bg-info-50 text-info-900 radius-8 p-16 mb-20">
                    <div class="d-flex align-items-start gap-3">
                        <div class="w-36-px h-36-px rounded-circle bg-info-100 text-info-600 d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="solar:ruler-pen-bold" class="text-xl"></iconify-icon>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-sm fw-semibold text-info-900 mb-1">Panduan & Rekomendasi Ukuran Cover Kursus (Course)</h6>
                            <p class="text-xs text-info-800 mb-2">Gunakan proporsi visual berikut agar kartu materi di katalog dan halaman detail kursus tampil prima:</p>
                            <ul class="text-xs text-info-900 mb-0 ps-16">
                                <li><strong>Dimensi Kartu Katalog / Grid:</strong> <span class="badge bg-white text-primary-600 font-monospace border px-6 py-2 radius-4 text-xs fw-semibold">800 x 500 px</span> (Rasio 16:10) — <em>Paling direkomendasikan</em></li>
                                <li><strong>Alternatif Rasio 16:9:</strong> <code>1200 x 675 px</code> atau <code>960 x 540 px</code></li>
                                <li><strong>Format File:</strong> JPG, JPEG, PNG, atau WebP</li>
                                <li><strong>Batas Ukuran File:</strong> Maksimal <strong>2 MB</strong></li>
                                <li><strong>Kebutuhan Tampilan:</strong> Thumbnail kartu katalog 3-kolom Arena Belajar, showcase kursus terpopuler, dan banner samping silabus materi.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row gy-20">
                    <div class="col-lg-7">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="cover_image">
                            Unggah File Gambar Cover Course
                        </label>
                        <input id="cover_image" type="file" name="cover_image" class="form-control radius-8" accept="image/png,image/jpeg,image/jpg,image/webp" onchange="previewCourseCover(this)">
                        <small class="text-muted text-xs mt-4 d-block">Pilih file gambar berformat JPG, PNG, atau WEBP (maks. 2 MB).</small>

                        <!-- Accordion URL Alternatif -->
                        <div class="mt-16 p-12 radius-8 border bg-neutral-50">
                            <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseCourseUrl" role="button" aria-expanded="false" aria-controls="collapseCourseUrl">
                                <iconify-icon icon="solar:link-circle-outline" class="text-base"></iconify-icon>
                                <span>Atau gunakan URL Gambar Eksternal / Path Aset Statis</span>
                                <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                            </a>
                            <div class="collapse mt-10 <?= (! empty($course['cover_url']) && ! str_contains($course['cover_url'], 'uploads/learning/')) ? 'show' : '' ?>" id="collapseCourseUrl">
                                <label class="form-label fw-semibold text-primary-light text-xs mb-4" for="cover_url">URL Gambar / Path Aset</label>
                                <input id="cover_url" type="text" name="cover_url" class="form-control radius-8 text-sm" value="<?= esc(old('cover_url', $course['cover_url'] ?? '')) ?>" placeholder="https://... atau assets/main/images/resources/course-details-img-1.jpg" oninput="previewCourseUrl(this.value)">
                                <small class="text-muted text-xs">Kosongkan jika telah mengunggah file gambar di atas.</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pratinjau Cover Course</label>
                        <?php
                        $existingCover = old('cover_url', $course['cover_url'] ?? '');
                        $previewSrc = ! empty($existingCover)
                            ? (str_starts_with($existingCover, 'http') ? $existingCover : base_url($existingCover))
                            : '';
                        ?>
                        <div id="course_preview_wrapper" class="p-12 radius-8 border bg-neutral-50 text-center <?= empty($previewSrc) ? 'd-none' : '' ?>">
                            <div class="d-flex align-items-center justify-content-between mb-8">
                                <span class="text-xs fw-semibold text-secondary-light">Tampilan Cover:</span>
                                <span id="course_preview_badge" class="badge bg-success-50 text-success-600 text-xs px-8 py-2 radius-4">Gambar Terpasang</span>
                            </div>
                            <div class="ratio ratio-16x9 rounded-8 overflow-hidden border bg-neutral-200">
                                <img id="course_preview_img" src="<?= esc($previewSrc) ?>" alt="Cover Course" class="w-100 h-100 object-fit-cover">
                            </div>
                            <small class="text-muted text-xs mt-6 d-block">Pratinjau proporsi thumbnail kartu kursus di katalog.</small>
                        </div>
                        <div id="course_preview_placeholder" class="p-24 radius-8 border border-dashed bg-neutral-50 text-center text-secondary-light <?= ! empty($previewSrc) ? 'd-none' : '' ?>">
                            <iconify-icon icon="solar:gallery-wide-outline" class="text-4xl text-neutral-400 mb-8"></iconify-icon>
                            <p class="text-xs mb-0">Belum ada file gambar dipilih.<br>Pilih file atau masukkan tautan URL untuk melihat pratinjau di sini.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-12 pt-16 border-top">
                <a href="<?= route_to('dashboard.learning.courses') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:diskette-bold" class="text-lg"></iconify-icon>
                    <span><?= $isEdit ? 'Perbarui Course' : 'Simpan Course Baru' ?></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewCourseCover(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('course_preview_img');
            const wrapper = document.getElementById('course_preview_wrapper');
            const placeholder = document.getElementById('course_preview_placeholder');
            const badge = document.getElementById('course_preview_badge');
            previewImg.src = e.target.result;
            wrapper.classList.remove('d-none');
            placeholder.classList.add('d-none');
            badge.textContent = 'File Baru: ' + file.name;
            badge.className = 'badge bg-primary-50 text-primary-600 text-xs px-8 py-2 radius-4 text-truncate';
            badge.style.maxWidth = '180px';
        };
        reader.readAsDataURL(file);
    }
}

function previewCourseUrl(url) {
    const trimmed = url.trim();
    const previewImg = document.getElementById('course_preview_img');
    const wrapper = document.getElementById('course_preview_wrapper');
    const placeholder = document.getElementById('course_preview_placeholder');
    const badge = document.getElementById('course_preview_badge');
    if (trimmed) {
        previewImg.src = trimmed;
        wrapper.classList.remove('d-none');
        placeholder.classList.add('d-none');
        badge.textContent = 'URL Eksternal';
        badge.className = 'badge bg-info-50 text-info-600 text-xs px-8 py-2 radius-4';
    }
}

function updateCourseJpBadge(minutes) {
    const badge = document.getElementById('course_jp_badge');
    if (!badge) return;
    const min = parseFloat(minutes) || 0;
    if (min <= 0) {
        badge.textContent = 'Mandiri / Fleksibel';
        return;
    }
    const jp = (min % 60 === 0) ? (min / 60) : (Math.round((min / 60) * 10) / 10);
    badge.textContent = jp + ' JP (' + min + ' Menit)';
}
</script>
<?= $this->endSection() ?>

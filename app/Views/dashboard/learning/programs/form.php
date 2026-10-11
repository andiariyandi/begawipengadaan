<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$program = $program ?? [];
$isEdit = ($formMode ?? 'create') === 'edit';
$formAction = $isEdit
    ? route_to('dashboard.learning.programs.update', $program['id'])
    : route_to('dashboard.learning.programs.store');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Program') ?></h6>
        <p class="text-secondary-light mb-0">Kelola dan sesuaikan data kurikulum program pembelajaran.</p>
    </div>
    <a href="<?= route_to('dashboard.learning.programs') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-linear" class="text-xl"></iconify-icon>
        <span>Kembali ke Daftar</span>
    </a>
</div>

<div class="card">
    <div class="card-body p-24">
        <form method="post" action="<?= esc($formAction) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- 1. IDENTITAS PROGRAM -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:folder-with-files-outline" class="text-lg"></iconify-icon>
                    <span>1. Informasi Pokok & Identitas Program</span>
                </h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="title">Judul Program <span class="text-danger-main">*</span></label>
                        <input id="title" type="text" name="title" class="form-control radius-8" value="<?= esc(old('title', $program['title'] ?? '')) ?>" required placeholder="Contoh: Kurikulum Terpadu Pengadaan Barang/Jasa Dasar">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="slug">Slug URL <span class="text-danger-main">*</span></label>
                        <input id="slug" type="text" name="slug" class="form-control radius-8 font-monospace" value="<?= esc(old('slug', $program['slug'] ?? '')) ?>" required placeholder="Contoh: pbj-dasar">
                        <small class="text-muted text-xs">Digunakan sebagai permalink di Arena Belajar: <code>/arena-belajar/program/{slug}</code></small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="status">Status Kurikulum</label>
                        <select id="status" name="status" class="form-select radius-8">
                            <?php $status = old('status', $program['status'] ?? 'draft'); ?>
                            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft (Konseptual)</option>
                            <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published (Tayang di Arena Belajar)</option>
                            <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archived (Diarsipkan)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="visibility">Visibilitas Akses</label>
                        <select id="visibility" name="visibility" class="form-select radius-8">
                            <?php $vis = old('visibility', $program['visibility'] ?? 'public'); ?>
                            <option value="public" <?= $vis === 'public' ? 'selected' : '' ?>>Public (Dapat diakses siapapun)</option>
                            <option value="internal" <?= $vis === 'internal' ? 'selected' : '' ?>>Internal (Hanya pengguna login / ASN)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-check form-switch mt-md-24">
                            <input id="featured" type="checkbox" name="featured" value="1" class="form-check-input" <?= old('featured', $program['featured'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-sm" for="featured">
                                <iconify-icon icon="solar:star-fall-minimalistic-2-bold" class="text-warning-main text-md me-1"></iconify-icon>
                                Program Unggulan (Tampil di Hero/Highlight)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. DESKRIPSI & RINGKASAN -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:document-text-outline" class="text-lg"></iconify-icon>
                    <span>2. Ringkasan & Deskripsi Kurikulum</span>
                </h6>
                <div class="row gy-20">
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="summary">Ringkasan Singkat (Lead Summary)</label>
                        <textarea id="summary" name="summary" class="form-control radius-8" rows="2" placeholder="Tuliskan 1-2 kalimat ringkasan yang menarik untuk kartu katalog di Arena Belajar..."><?= esc(old('summary', $program['summary'] ?? '')) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="description">Deskripsi Lengkap & Latar Belakang</label>
                        <textarea id="description" name="description" class="form-control radius-8" rows="5" placeholder="Penjelasan lengkap kurikulum, landasan regulasi Perpres LKPP, ruang lingkup, dan manfaat bagi peserta..."><?= esc(old('description', $program['description'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- 3. SASARAN, PRASYARAT & TARGET KOMPETENSI -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:diploma-verified-outline" class="text-lg"></iconify-icon>
                    <span>3. Sasaran Peserta, Prasyarat & Capaian Pembelajaran</span>
                </h6>
                <div class="row gy-20">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="target_audience">Target Sasaran Peserta</label>
                        <textarea id="target_audience" name="target_audience" class="form-control radius-8" rows="3" placeholder="Contoh: PPK, Pejabat Pengadaan, Pokja Pemilihan, Tim Teknis, Penyedia Barang/Jasa Pemerintah..."><?= esc(old('target_audience', $program['target_audience'] ?? '')) ?></textarea>
                        <small class="text-muted text-xs">Akan ditampilkan pada tab rincian sasaran kurikulum di halaman publik.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="prerequisites">Prasyarat Program</label>
                        <textarea id="prerequisites" name="prerequisites" class="form-control radius-8" rows="3" placeholder="Contoh: Memiliki akun LPSE aktif, pemahaman dasar administrasi pemerintahan, atau telah menyelesaikan level pengantar..."><?= esc(old('prerequisites', $program['prerequisites'] ?? '')) ?></textarea>
                        <small class="text-muted text-xs">Prasyarat administratif atau kompetensi awal sebelum mengikuti program.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="learning_objectives">Capaian Pembelajaran & Target Kompetensi</label>
                        <textarea id="learning_objectives" name="learning_objectives" class="form-control radius-8" rows="4" placeholder="Tuliskan poin capaian kompetensi kelulusan (pisahkan per baris)..."><?= esc(old('learning_objectives', $program['learning_objectives'] ?? '')) ?></textarea>
                        <small class="text-muted text-xs">Capaian kompetensi kelulusan yang diharapkan setelah peserta menuntaskan program.</small>
                    </div>
                </div>
            </div>

            <!-- 4. MEDIA & BANNER PROGRAM -->
            <div class="mb-24">
                <h6 class="text-md fw-semibold text-primary-600 border-bottom pb-8 mb-16 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:gallery-wide-outline" class="text-lg"></iconify-icon>
                    <span>4. Gambar Sampul & Banner Program</span>
                </h6>

                <!-- Box Rekomendasi Dimensi & Ukuran -->
                <div class="alert alert-info border border-info-200 bg-info-50 text-info-900 radius-8 p-16 mb-20">
                    <div class="d-flex align-items-start gap-3">
                        <div class="w-36-px h-36-px rounded-circle bg-info-100 text-info-600 d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="solar:ruler-pen-bold" class="text-xl"></iconify-icon>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-sm fw-semibold text-info-900 mb-1">Panduan & Rekomendasi Ukuran Banner Program</h6>
                            <p class="text-xs text-info-800 mb-2">Gunakan visual landscape dengan rasio 16:9 agar banner header detail program dan kartu roadmap terlihat tajam dan proporsional:</p>
                            <ul class="text-xs text-info-900 mb-0 ps-16">
                                <li><strong>Dimensi Ideal:</strong> <span class="badge bg-white text-primary-600 font-monospace border px-6 py-2 radius-4 text-xs fw-semibold">1200 x 630 px</span> (Rasio 16:9 / Landscape)</li>
                                <li><strong>Alternatif Resolusi HD:</strong> <code>1280 x 720 px</code> atau <code>1920 x 1080 px</code></li>
                                <li><strong>Format File:</strong> JPG, JPEG, PNG, atau WebP</li>
                                <li><strong>Batas Ukuran File:</strong> Maksimal <strong>2 MB</strong></li>
                                <li><strong>Kebutuhan Tampilan:</strong> Banner hero header program & thumbnail kartu kurikulum di katalog Arena Belajar.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row gy-20">
                    <div class="col-lg-7">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8" for="cover_image">
                            Unggah File Gambar Cover / Banner
                        </label>
                        <input id="cover_image" type="file" name="cover_image" class="form-control radius-8" accept="image/png,image/jpeg,image/jpg,image/webp" onchange="previewProgramCover(this)">
                        <small class="text-muted text-xs mt-4 d-block">Pilih file gambar berformat JPG, PNG, atau WEBP (maks. 2 MB).</small>

                        <!-- Accordion URL Alternatif -->
                        <div class="mt-16 p-12 radius-8 border bg-neutral-50">
                            <a class="text-xs fw-semibold text-primary-600 d-inline-flex align-items-center gap-1 text-decoration-none" data-bs-toggle="collapse" href="#collapseProgramUrl" role="button" aria-expanded="false" aria-controls="collapseProgramUrl">
                                <iconify-icon icon="solar:link-circle-outline" class="text-base"></iconify-icon>
                                <span>Atau gunakan URL Gambar Eksternal / Path Aset Statis</span>
                                <iconify-icon icon="solar:alt-arrow-down-linear" class="text-xs"></iconify-icon>
                            </a>
                            <div class="collapse mt-10 <?= (! empty($program['cover_url']) && ! str_contains($program['cover_url'], 'uploads/learning/')) ? 'show' : '' ?>" id="collapseProgramUrl">
                                <label class="form-label fw-semibold text-primary-light text-xs mb-4" for="cover_url">URL Gambar / Path Aset</label>
                                <input id="cover_url" type="text" name="cover_url" class="form-control radius-8 text-sm" value="<?= esc(old('cover_url', $program['cover_url'] ?? '')) ?>" placeholder="https://... atau assets/main/images/resources/course-list-img-1.jpg" oninput="previewProgramUrl(this.value)">
                                <small class="text-muted text-xs">Kosongkan jika telah mengunggah file gambar di atas.</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pratinjau Cover Program</label>
                        <?php
                        $existingCover = old('cover_url', $program['cover_url'] ?? '');
                        $previewSrc = ! empty($existingCover)
                            ? (str_starts_with($existingCover, 'http') ? $existingCover : base_url($existingCover))
                            : '';
                        ?>
                        <div id="program_preview_wrapper" class="p-12 radius-8 border bg-neutral-50 text-center <?= empty($previewSrc) ? 'd-none' : '' ?>">
                            <div class="d-flex align-items-center justify-content-between mb-8">
                                <span class="text-xs fw-semibold text-secondary-light">Tampilan Cover:</span>
                                <span id="program_preview_badge" class="badge bg-success-50 text-success-600 text-xs px-8 py-2 radius-4">Gambar Terpasang</span>
                            </div>
                            <div class="ratio ratio-16x9 rounded-8 overflow-hidden border bg-neutral-200">
                                <img id="program_preview_img" src="<?= esc($previewSrc) ?>" alt="Cover Program" class="w-100 h-100 object-fit-cover">
                            </div>
                            <small class="text-muted text-xs mt-6 d-block">Rasio 16:9 — Pratinjau proporsi tampilan kartu & banner.</small>
                        </div>
                        <div id="program_preview_placeholder" class="p-24 radius-8 border border-dashed bg-neutral-50 text-center text-secondary-light <?= ! empty($previewSrc) ? 'd-none' : '' ?>">
                            <iconify-icon icon="solar:gallery-wide-outline" class="text-4xl text-neutral-400 mb-8"></iconify-icon>
                            <p class="text-xs mb-0">Belum ada file gambar dipilih.<br>Pilih file atau masukkan tautan URL untuk melihat pratinjau di sini.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-12 pt-16 border-top">
                <a href="<?= route_to('dashboard.learning.programs') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">Batal</a>
                <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                    <iconify-icon icon="solar:diskette-bold" class="text-lg"></iconify-icon>
                    <span><?= $isEdit ? 'Perbarui Program' : 'Simpan Program Baru' ?></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewProgramCover(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('program_preview_img');
            const wrapper = document.getElementById('program_preview_wrapper');
            const placeholder = document.getElementById('program_preview_placeholder');
            const badge = document.getElementById('program_preview_badge');
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

function previewProgramUrl(url) {
    const trimmed = url.trim();
    const previewImg = document.getElementById('program_preview_img');
    const wrapper = document.getElementById('program_preview_wrapper');
    const placeholder = document.getElementById('program_preview_placeholder');
    const badge = document.getElementById('program_preview_badge');
    if (trimmed) {
        previewImg.src = trimmed;
        wrapper.classList.remove('d-none');
        placeholder.classList.add('d-none');
        badge.textContent = 'URL Eksternal';
        badge.className = 'badge bg-info-50 text-info-600 text-xs px-8 py-2 radius-4';
    }
}
</script>
<?= $this->endSection() ?>

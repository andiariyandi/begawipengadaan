<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('pageStyles') ?>
<style>
/* Scoped News Form Styling for High Contrast & Clean Layout */
.news-form-container {
    color: #1e293b;
}
.news-form-card {
    background: #ffffff;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.news-form-label {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 6px;
    display: block;
}
.news-form-control {
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
    font-size: 14px;
    border-radius: 8px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.news-form-control:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
}
.news-helper-text {
    font-size: 12px;
    color: #475569;
    margin-top: 4px;
    display: block;
}
.news-upload-preview {
    height: 185px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$item   = $item ?? [];
$cats   = $newsCategories ?? $categories ?? [];
$coverUrl = ! empty($item['cover_file_id']) ? site_url('file/' . (int) $item['cover_file_id']) : (! empty($item['cover_path']) ? (str_starts_with($item['cover_path'], 'http') ? $item['cover_path'] : base_url($item['cover_path'])) : null);
?>

<div class="news-form-container">
    <!-- Top Bar Navigation -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h6 class="fw-bold text-neutral-900 mb-1"><?= esc($pageTitle ?? 'Formulir Berita') ?></h6>
            <p class="text-neutral-600 mb-0 fw-medium" style="color: #475569;">
                <?= $isEdit ? 'Perbarui informasi warta, naskah isi, dan media sampul berita publikasi.' : 'Publikasikan warta, siaran pers, dan informasi resmi ekosistem PBJ Provinsi Lampung.' ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 px-18 py-10 d-flex align-items-center gap-2 text-sm fw-semibold text-neutral-700" style="background:#ffffff; border-color:#cbd5e1;">
                <iconify-icon icon="solar:arrow-left-linear" class="text-lg text-primary-600"></iconify-icon> Kembali ke Daftar
            </a>
        </div>
    </div>

    <form action="<?= $isEdit ? route_to('dashboard.communication.news.edit', (string) ($newsId ?? $item['id'] ?? '')) : route_to('dashboard.communication.news.create') ?>"
          method="post" enctype="multipart/form-data" id="newsForm">
        <?= csrf_field() ?>

        <div class="row gy-4">
            <!-- Main Form Column (8 cols) -->
            <div class="col-xl-8">
                <div class="news-form-card p-24 mb-24">
                    <h6 class="fw-bold text-neutral-900 mb-20 pb-12 border-bottom d-flex align-items-center gap-2" style="border-color: #e2e8f0 !important;">
                        <iconify-icon icon="solar:document-text-bold" class="text-primary-600 text-xl"></iconify-icon>
                        <span>Konten &amp; Naskah Berita</span>
                    </h6>

                    <div class="row gy-20">
                        <!-- Title -->
                        <div class="col-12">
                            <label class="news-form-label">
                                Judul Berita <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="newsTitle" class="form-control news-form-control radius-8 fs-15 fw-bold"
                                   value="<?= esc(old('title', $item['title'] ?? '')) ?>"
                                   placeholder="Contoh: Biro PBJ Setda Lampung Percepat Integrasi E-Katalog V6..." required maxlength="255">
                            <small class="news-helper-text">Judul yang ringkas, jelas, dan memuat kata kunci utama (maksimal 255 karakter).</small>
                        </div>

                        <!-- Slug (If Edit) -->
                        <?php if ($isEdit && ! empty($item['slug'])): ?>
                            <div class="col-12">
                                <label class="form-label fw-bold text-neutral-700 text-xs mb-2">Slug URL Publik</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-neutral-100 text-neutral-700 text-xs fw-bold" style="border-color: #cbd5e1;">/berita/</span>
                                    <input type="text" class="form-control text-xs font-monospace bg-neutral-50 text-neutral-900 fw-semibold" style="border-color: #cbd5e1;" value="<?= esc($item['slug']) ?>" readonly>
                                    <a href="<?= site_url('berita/' . esc($item['slug'])) ?>" target="_blank" class="btn btn-outline-primary text-xs d-flex align-items-center gap-1" title="Buka di Landing Page">
                                        <iconify-icon icon="solar:link-external-linear"></iconify-icon> Buka Web
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Excerpt / Summary -->
                        <div class="col-12">
                            <label class="news-form-label">
                                Ringkasan / Teras Berita (Lead Paragraph)
                            </label>
                            <textarea name="summary" class="form-control news-form-control radius-8 text-sm" rows="3"
                                      placeholder="Ringkasan 2–3 kalimat yang menggambarkan inti warta. Tampil pada kartu pratinjau dan meta deskripsi."><?= esc(old('summary', $item['summary'] ?? '')) ?></textarea>
                            <small class="news-helper-text">Ringkasan padat yang memudahkan pembaca memahami pesan utama sebelum membaca seluruh artikel.</small>
                        </div>

                        <!-- Full Content -->
                        <div class="col-12">
                            <label class="news-form-label">
                                Isi Naskah Berita Lengkap <span class="text-danger">*</span>
                            </label>
                            <textarea name="content" class="form-control news-form-control radius-8 text-sm font-monospace" rows="14" style="line-height: 1.6;"
                                      placeholder="Tuliskan naskah berita lengkap di sini. Anda dapat menyusun paragraf secara runtut (5W+1H)..." required><?= esc(old('content', $item['content'] ?? '')) ?></textarea>
                            <div class="d-flex justify-content-between text-neutral-600 text-xs mt-2" style="color: #475569;">
                                <span>Mendukung teks terstruktur, paragraf berita, dan kutipan resmi jurnalistik.</span>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="col-md-6">
                            <label class="news-form-label">
                                Kata Kunci / Tagar Berita
                            </label>
                            <input type="text" name="tags" class="form-control news-form-control radius-8 text-sm"
                                   value="<?= esc(old('tags', $item['tags'] ?? '')) ?>"
                                   placeholder="Contoh: E-Katalog, Regulasi, P3DN, UMKM">
                            <small class="news-helper-text">Pisahkan dengan koma untuk multi-tagar.</small>
                        </div>

                        <!-- Reading Time -->
                        <div class="col-md-6">
                            <label class="news-form-label">
                                Estimasi Waktu Baca
                            </label>
                            <input type="text" name="reading_time" class="form-control news-form-control radius-8 text-sm"
                                   value="<?= esc(old('reading_time', $item['reading_time'] ?? '5 Menit Baca')) ?>"
                                   placeholder="Contoh: 5 Menit Baca">
                            <small class="news-helper-text">Durasi perkiraan pembaca menyelesaikan warta ini.</small>
                        </div>

                        <!-- Source Info -->
                        <div class="col-md-6">
                            <label class="news-form-label">
                                Nama Sumber / Redaksi Liputan
                            </label>
                            <input type="text" name="source_name" class="form-control news-form-control radius-8 text-sm"
                                   value="<?= esc(old('source_name', $item['source_name'] ?? 'Humas Biro PBJ Setda Provinsi Lampung')) ?>"
                                   placeholder="Contoh: Humas Biro PBJ Setda Lampung / LKPP RI">
                        </div>

                        <div class="col-md-6">
                            <label class="news-form-label">
                                Tautan Rujukan / Referensi Sumber (URL)
                            </label>
                            <input type="url" name="source_url" class="form-control news-form-control radius-8 text-sm font-monospace"
                                   value="<?= esc(old('source_url', $item['source_url'] ?? '')) ?>"
                                   placeholder="https://lampungprov.go.id atau https://jdih.lkpp.go.id">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Column (4 cols) -->
            <div class="col-xl-4">
                <!-- Card 1: Media Cover Upload & Guidelines -->
                <div class="news-form-card p-20 mb-24">
                    <h6 class="fw-bold text-neutral-900 mb-16 pb-10 border-bottom d-flex align-items-center justify-content-between" style="border-color: #e2e8f0 !important;">
                        <span class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:camera-bold" class="text-primary-600 text-lg"></iconify-icon>
                            Gambar Sampul (Cover)
                        </span>
                        <span class="badge bg-primary-50 text-primary-700 border border-primary-200 text-2xs fw-bold px-8 py-3">16:9 Media</span>
                    </h6>

                    <!-- Rekomendasi Format & Ukuran Alert -->
                    <div class="alert alert-info border border-info-200 p-14 radius-8 mb-16 text-xs" style="background-color: #f0f9ff !important; border-color: #bae6fd !important;">
                        <div class="d-flex align-items-center gap-2 mb-2 fw-bold" style="color: #0369a1;">
                            <iconify-icon icon="solar:info-circle-bold" class="text-base" style="color: #0284c7;"></iconify-icon>
                            Rekomendasi Format &amp; Ukuran Gambar:
                        </div>
                        <ul class="ps-16 mb-0 leading-normal" style="color: #0f172a; font-weight: 500;">
                            <li class="mb-1"><strong>Aspek Rasio:</strong> 16:9 Landscape</li>
                            <li class="mb-1"><strong>Dimensi Ideal:</strong> <code style="color: #0369a1; background: #e0f2fe; padding: 1px 5px; border-radius: 4px; font-weight: 600;">1200 x 630 px</code> (Feed Berita &amp; Web)</li>
                            <li class="mb-1"><strong>Dimensi Maksimal:</strong> <code style="color: #0369a1; background: #e0f2fe; padding: 1px 5px; border-radius: 4px; font-weight: 600;">1920 x 1080 px</code> (Headline Hero Slider)</li>
                            <li class="mb-1"><strong>Format Didukung:</strong> JPG, JPEG, PNG, WEBP</li>
                            <li><strong>Batas Ukuran:</strong> Maksimal 5 MB</li>
                        </ul>
                    </div>

                    <!-- Live Preview Element -->
                    <div class="news-upload-preview mb-14">
                        <img id="previewCoverImg"
                             src="<?= esc($coverUrl ?? '') ?>"
                             alt="Pratinjau Cover"
                             style="width:100%;height:100%;object-fit:cover;<?= $coverUrl ? '' : 'display:none;' ?>">
                        <div id="previewCoverPlaceholder" class="text-center p-16 <?= $coverUrl ? 'd-none' : '' ?>">
                            <iconify-icon icon="solar:gallery-send-outline" class="text-neutral-500 text-4xl mb-2"></iconify-icon>
                            <span class="d-block text-xs text-neutral-700 fw-bold">Belum ada gambar dipilih</span>
                            <small class="text-neutral-500 text-2xs d-block mt-1">Pilih berkas gambar di bawah ini</small>
                        </div>
                    </div>

                    <!-- File Input -->
                    <div class="mb-14">
                        <label class="news-form-label text-xs">Pilih Berkas Gambar</label>
                        <input type="file" name="cover" id="coverFileInput" class="form-control form-control-sm news-form-control radius-8" accept="image/jpeg,image/png,image/webp,image/jpg">
                    </div>

                    <!-- Cover Caption -->
                    <div>
                        <label class="news-form-label text-xs">Keterangan Foto (Caption)</label>
                        <input type="text" name="cover_caption" class="form-control form-control-sm news-form-control radius-8"
                               value="<?= esc(old('cover_caption', $item['cover_caption'] ?? '')) ?>"
                               placeholder="Contoh: Suasana pembukaan rapat koordinasi pengadaan...">
                        <small class="news-helper-text text-2xs">Tampil di bawah foto artikel untuk atribuasi visual.</small>
                    </div>
                </div>

                <!-- Card 2: Pengaturan Publikasi -->
                <div class="news-form-card p-20 mb-24">
                    <h6 class="fw-bold text-neutral-900 mb-16 pb-10 border-bottom d-flex align-items-center gap-2" style="border-color: #e2e8f0 !important;">
                        <iconify-icon icon="solar:settings-bold" class="text-primary-600 text-lg"></iconify-icon>
                        <span>Pengaturan &amp; Status</span>
                    </h6>

                    <div class="mb-16">
                        <label class="news-form-label text-xs">Kategori Berita <span class="text-danger">*</span></label>
                        <select class="form-select news-form-control radius-8 text-sm fw-medium" name="category_id" required>
                            <option value="">— Pilih Kategori Berita —</option>
                            <?php foreach ($cats as $c): ?>
                                <option value="<?= (int) $c['id'] ?>" <?= (int) ($item['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                                    <?= esc($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-16">
                        <label class="news-form-label text-xs">Status Publikasi</label>
                        <select class="form-select news-form-control radius-8 text-sm fw-medium" name="workflow_status">
                            <?php foreach ([
                                'draft'             => 'Draft (Konsep)',
                                'submitted'         => 'Diajukan (Submitted)',
                                'in_review'         => 'Ditinjau (In Review)',
                                'approved'          => 'Disetujui (Approved)',
                                'published'         => 'Dipublikasikan (Published)',
                                'revision_required' => 'Perlu Revisi',
                                'rejected'          => 'Ditolak',
                                'archived'          => 'Diarsipkan'
                            ] as $k => $l): ?>
                                <option value="<?= $k ?>" <?= ($item['workflow_status'] ?? 'draft') === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-16">
                        <label class="news-form-label text-xs">Visibilitas Akses</label>
                        <select class="form-select news-form-control radius-8 text-sm fw-medium" name="visibility">
                            <?php foreach (['public' => 'Publik (Semua Pengunjung)', 'internal' => 'Internal (Aparatur PBJ)', 'restricted' => 'Terbatas (Hak Akses Khusus)'] as $k => $l): ?>
                                <option value="<?= $k ?>" <?= ($item['visibility'] ?? 'public') === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Featured Headline (Maks 5 Slot) -->
                    <!-- Berita Utama Switch -->
                    <div class="mb-16 p-16 radius-8 border border-neutral-200 bg-neutral-50">
                        <div class="d-flex align-items-center justify-content-between mb-8">
                            <label class="form-check-label fw-semibold text-primary-light text-sm mb-0 cursor-pointer d-flex align-items-center gap-2" for="featuredSwitch">
                                <iconify-icon icon="solar:star-bold" class="text-warning-main"></iconify-icon>
                                <span>Jadikan Berita Utama</span>
                            </label>
                            <div class="form-switch switch-warning d-inline-flex">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="is_featured" value="1" id="featuredSwitch" <?= ! empty($item['is_featured']) ? 'checked' : '' ?>>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-8 border-top border-neutral-200 text-xs mb-8">
                            <span class="badge bg-warning-100 text-warning-main radius-4 px-8 py-4">
                                <iconify-icon icon="solar:slider-vertical-bold" class="me-1"></iconify-icon> Slot Berita Utama
                            </span>
                            <span class="fw-semibold text-primary-light">
                                <?= (int) ($headlineCount ?? 0) ?>/5 Terpakai
                            </span>
                        </div>
                        <p class="text-secondary-light text-xs mb-0">
                            Berita utama akan tampil pada slider hero teratas di portal publik untuk meningkatkan visibilitas warta prioritas.
                        </p>
                    </div>

                    <!-- Carousel Slider Switch -->
                    <div class="mb-16 p-16 radius-8 border border-neutral-200 bg-neutral-50">
                        <div class="d-flex align-items-center justify-content-between mb-8">
                            <label class="form-check-label fw-semibold text-primary-light text-sm mb-0 cursor-pointer d-flex align-items-center gap-2" for="carouselSwitch">
                                <iconify-icon icon="solar:slider-vertical-bold" class="text-primary-600"></iconify-icon>
                                <span>Tampilkan di Hero Slider</span>
                            </label>
                            <div class="form-switch switch-primary d-inline-flex">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="carousel_enabled" value="1" id="carouselSwitch" <?= ! empty($item['carousel_enabled']) ? 'checked' : '' ?>>
                            </div>
                        </div>
                        <p class="text-secondary-light text-xs mb-8">
                            Aktifkan untuk memprioritaskan warta ini masuk dalam slider sorotan headline di landing page berita.
                        </p>
                        <div class="pt-8 border-top border-neutral-200">
                            <label class="form-label text-xs fw-semibold text-primary-light mb-1" for="carousel_order">Urutan Slide (Order):</label>
                            <input type="number" min="1" max="99" id="carousel_order" name="carousel_order" class="form-control form-control-sm radius-8" value="<?= esc($item['carousel_order'] ?? '') ?>" placeholder="Urutan (cth: 1, 2, 3...)">
                        </div>
                    </div>

                    <div class="mb-16">
                        <label class="news-form-label text-xs">Tanggal / Jadwal Publikasi</label>
                        <input type="datetime-local" name="published_at" class="form-control news-form-control radius-8 text-sm fw-medium"
                               value="<?= esc(! empty($item['published_at']) ? date('Y-m-d\TH:i', strtotime($item['published_at'])) : old('published_at')) ?>">
                        <small class="news-helper-text text-2xs">Kosongkan jika ingin menggunakan waktu saat ini ketika diterbitkan.</small>
                    </div>
                </div>

                <!-- Card 3: Action Buttons -->
                <div class="news-form-card p-20">
                    <button type="submit" class="btn btn-primary-600 radius-8 w-100 py-12 fw-bold d-flex align-items-center justify-content-center gap-2 mb-10 text-sm shadow-sm">
                        <iconify-icon icon="solar:check-circle-outline" class="text-lg"></iconify-icon>
                        <?= $isEdit ? 'Simpan Pembaruan Berita' : 'Simpan &amp; Terbitkan Berita' ?>
                    </button>
                    <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 w-100 py-10 text-sm fw-semibold text-neutral-700 text-center" style="background:#ffffff; border-color:#cbd5e1;">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('coverFileInput');
        const previewImg = document.getElementById('previewCoverImg');
        const placeholder = document.getElementById('previewCoverPlaceholder');

        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    // Validasi batas ukuran client-side (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Peringatan: Ukuran berkas (' + (file.size / (1024*1024)).toFixed(2) + ' MB) melebihi batas rekomendasi 5 MB.');
                    }
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        previewImg.src = event.target.result;
                        previewImg.style.display = 'block';
                        if (placeholder) {
                            placeholder.classList.add('d-none');
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$isEdit = ($formMode ?? 'create') === 'edit';
$item   = $item ?? [];
$cats   = $newsCategories ?? $categories ?? [];
$coverUrl = ! empty($item['cover_path']) ? base_url('file/' . (int) $item['cover_file_id']) : null;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1"><?= esc($pageTitle ?? 'Formulir Berita') ?></h6>
        <p class="text-secondary-light mb-0">
            <?= $isEdit ? 'Perbarui informasi, konten naskah, dan media publikasi berita.' : 'Publikasikan warta, siaran pers, dan informasi resmi ekosistem PBJ Provinsi Lampung.' ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-flex align-items-center gap-2 text-sm">
            <iconify-icon icon="solar:arrow-left-linear" class="text-lg"></iconify-icon> Kembali ke Daftar
        </a>
    </div>
</div>

<form action="<?= $isEdit ? route_to('dashboard.communication.news.edit', (string) ($newsId ?? $item['id'] ?? '')) : route_to('dashboard.communication.news.create') ?>"
      method="post" enctype="multipart/form-data" id="newsForm">
    <?= csrf_field() ?>

    <div class="row gy-4">
        <!-- Main Form Column (8 cols) -->
        <div class="col-xl-8">
            <div class="card radius-12 border p-24 mb-24">
                <h6 class="fw-bold text-dark mb-20 pb-12 border-bottom">Konten &amp; Naskah Berita</h6>

                <div class="row gy-20">
                    <!-- Title -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-neutral-800 text-sm mb-8">
                            Judul Berita <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" id="newsTitle" class="form-control radius-8 fs-15 fw-semibold"
                               value="<?= esc(old('title', $item['title'] ?? '')) ?>"
                               placeholder="Contoh: Biro PBJ Setda Lampung Percepat Integrasi E-Katalog V6..." required maxlength="255">
                        <small class="text-neutral-600 mt-4 d-block">Judul yang ringkas, jelas, dan memuat kata kunci utama (maksimal 255 karakter).</small>
                    </div>

                    <!-- Slug (If Edit) -->
                    <?php if ($isEdit && ! empty($item['slug'])): ?>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-neutral-700 text-xs mb-4">Slug URL Publik</label>
                            <div class="input-group">
                                <span class="input-group-text bg-neutral-100 text-neutral-700 text-xs">/berita/</span>
                                <input type="text" class="form-control text-xs font-monospace bg-neutral-50" value="<?= esc($item['slug']) ?>" readonly>
                                <a href="<?= site_url('berita/' . esc($item['slug'])) ?>" target="_blank" class="btn btn-outline-primary text-xs" title="Buka di Landing Page">
                                    <iconify-icon icon="solar:link-external-linear"></iconify-icon>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Excerpt / Summary -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-neutral-800 text-sm mb-8">
                            Ringkasan / Teras Berita (Lead Paragraph)
                        </label>
                        <textarea name="summary" class="form-control radius-8 text-sm" rows="3"
                                  placeholder="Ringkasan 2–3 kalimat yang menggambarkan inti warta. Tampil pada kartu pratinjau dan meta deskripsi."><?= esc(old('summary', $item['summary'] ?? '')) ?></textarea>
                    </div>

                    <!-- Full Content -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-neutral-800 text-sm mb-8">
                            Isi Naskah Berita Lengkap <span class="text-danger">*</span>
                        </label>
                        <textarea name="content" class="form-control radius-8 text-sm font-monospace" rows="14"
                                  placeholder="Tuliskan naskah berita lengkap di sini. Anda dapat menyusun paragraf secara runtut (5W+1H)..." required><?= esc(old('content', $item['content'] ?? '')) ?></textarea>
                        <div class="d-flex justify-content-between text-neutral-600 text-2xs mt-4">
                            <span>Mendukung teks terstruktur, paragraf, dan kutipan jurnalistik resmi.</span>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-neutral-800 text-sm mb-8">
                            Kata Kunci / Tagar Berita
                        </label>
                        <input type="text" name="tags" class="form-control radius-8 text-sm"
                               value="<?= esc(old('tags', $item['tags'] ?? '')) ?>"
                               placeholder="Contoh: E-Katalog, Regulasi, P3DN, UMKM">
                        <small class="text-neutral-600 mt-4 d-block">Pisahkan dengan koma untuk multi-tagar.</small>
                    </div>

                    <!-- Reading Time -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-neutral-800 text-sm mb-8">
                            Estimasi Waktu Baca
                        </label>
                        <input type="text" name="reading_time" class="form-control radius-8 text-sm"
                               value="<?= esc(old('reading_time', $item['reading_time'] ?? '5 Menit Baca')) ?>"
                               placeholder="Contoh: 5 Menit Baca">
                    </div>

                    <!-- Source Info -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-neutral-800 text-sm mb-8">
                            Nama Sumber / Redaksi Liputan
                        </label>
                        <input type="text" name="source_name" class="form-control radius-8 text-sm"
                               value="<?= esc(old('source_name', $item['source_name'] ?? 'Humas Biro PBJ Setda Provinsi Lampung')) ?>"
                               placeholder="Contoh: Humas Biro PBJ Setda Lampung / LKPP RI">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-neutral-800 text-sm mb-8">
                            Tautan Rujukan / Referensi Sumber (URL)
                        </label>
                        <input type="url" name="source_url" class="form-control radius-8 text-sm font-monospace"
                               value="<?= esc(old('source_url', $item['source_url'] ?? '')) ?>"
                               placeholder="https://lampungprov.go.id atau https://jdih.lkpp.go.id">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column (4 cols) -->
        <div class="col-xl-4">
            <!-- Card 1: Media Cover Upload & Guidelines -->
            <div class="card radius-12 border p-20 mb-24">
                <h6 class="fw-bold text-dark mb-16 pb-10 border-bottom d-flex align-items-center justify-content-between">
                    <span>Gambar Sampul (Cover)</span>
                    <iconify-icon icon="solar:camera-outline" class="text-primary-600 text-lg"></iconify-icon>
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
                <div class="position-relative h-180-px bg-neutral-100 rounded-8 border overflow-hidden d-flex align-items-center justify-content-center mb-14">
                    <img id="previewCoverImg"
                         src="<?= esc($coverUrl ?? '') ?>"
                         alt="Pratinjau Cover"
                         style="width:100%;height:100%;object-fit:cover;<?= $coverUrl ? '' : 'display:none;' ?>">
                    <div id="previewCoverPlaceholder" class="text-center p-12 <?= $coverUrl ? 'd-none' : '' ?>">
                        <iconify-icon icon="solar:gallery-send-outline" class="text-neutral-400 text-4xl mb-1"></iconify-icon>
                        <span class="d-block text-2xs text-neutral-600 fw-medium">Belum ada gambar dipilih</span>
                    </div>
                </div>

                <!-- File Input -->
                <div class="mb-14">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-4">Pilih Berkas Gambar</label>
                    <input type="file" name="cover" id="coverFileInput" class="form-control form-control-sm radius-8" accept="image/jpeg,image/png,image/webp,image/jpg">
                </div>

                <!-- Cover Caption -->
                <div>
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-4">Keterangan Foto (Caption)</label>
                    <input type="text" name="cover_caption" class="form-control form-control-sm radius-8"
                           value="<?= esc(old('cover_caption', $item['cover_caption'] ?? '')) ?>"
                           placeholder="Contoh: Suasana pembukaan rapat koordinasi pengadaan...">
                    <small class="text-neutral-600 text-2xs mt-2 d-block">Tampil di bawah foto artikel untuk atribuasi visual.</small>
                </div>
            </div>

            <!-- Card 2: Pengaturan Publikasi -->
            <div class="card radius-12 border p-20 mb-24">
                <h6 class="fw-bold text-dark mb-16 pb-10 border-bottom">Pengaturan &amp; Status</h6>

                <div class="mb-16">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-4">Kategori Berita <span class="text-danger">*</span></label>
                    <select class="form-select radius-8 text-sm" name="category_id" required>
                        <option value="">— Pilih Kategori Berita —</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (int) ($item['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                                <?= esc($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-16">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-4">Status Publikasi</label>
                    <select class="form-select radius-8 text-sm" name="workflow_status">
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
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-4">Visibilitas Akses</label>
                    <select class="form-select radius-8 text-sm" name="visibility">
                        <?php foreach (['public' => 'Publik (Semua Pengunjung)', 'internal' => 'Internal (Aparatur PBJ)', 'restricted' => 'Terbatas (Hak Akses Khusus)'] as $k => $l): ?>
                            <option value="<?= $k ?>" <?= ($item['visibility'] ?? 'public') === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Featured Headline (Maks 5 Slot) -->
                <div class="mb-16 p-14 bg-neutral-50 rounded-10 border border-neutral-200">
                    <div class="d-flex align-items-center justify-content-between mb-8">
                        <label class="form-check-label fw-bold text-neutral-800 text-sm mb-0 cursor-pointer d-flex align-items-center gap-2" for="featuredSwitch">
                            <span class="w-28-px h-28-px rounded-circle bg-warning-100 text-warning-700 d-flex align-items-center justify-content-center">
                                <iconify-icon icon="solar:star-bold" class="text-base"></iconify-icon>
                            </span>
                            Jadikan Berita Utama
                        </label>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featuredSwitch" <?= ! empty($item['is_featured']) ? 'checked' : '' ?> style="cursor: pointer; width: 2.25rem; height: 1.2rem;">
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-8 border-top border-neutral-200 text-2xs">
                        <span class="badge bg-warning-subtle text-warning-800 border border-warning-300 fw-bold px-8 py-3">
                            <iconify-icon icon="solar:slider-vertical-bold" class="me-1"></iconify-icon> Slot Berita Utama (Maks. 5)
                        </span>
                        <span class="text-neutral-700 fw-bold">
                            <?= (int) ($headlineCount ?? 0) ?>/5 Terpakai
                        </span>
                    </div>
                    <small class="text-neutral-600 text-xs d-block mt-8 leading-snug">
                        Berita utama akan tampil pada slider hero teratas di portal publik (hingga 5 warta pilihan) untuk meningkatkan visibilitas warta prioritas.
                    </small>
                </div>

                <div class="mb-16">
                    <label class="form-label text-xs fw-bold text-neutral-800 mb-4">Tanggal / Jadwal Publikasi</label>
                    <input type="datetime-local" name="published_at" class="form-control radius-8 text-sm"
                           value="<?= esc(! empty($item['published_at']) ? date('Y-m-d\TH:i', strtotime($item['published_at'])) : old('published_at')) ?>">
                    <small class="text-neutral-600 text-2xs mt-2 d-block">Kosongkan jika ingin menggunakan waktu saat ini ketika diterbitkan.</small>
                </div>
            </div>

            <!-- Card 3: Action Buttons -->
            <div class="card radius-12 border p-20">
                <button type="submit" class="btn btn-primary-600 radius-8 w-100 py-12 fw-bold d-flex align-items-center justify-content-center gap-2 mb-10 text-sm">
                    <iconify-icon icon="solar:check-circle-outline" class="text-lg"></iconify-icon>
                    <?= $isEdit ? 'Simpan Pembaruan Berita' : 'Simpan &amp; Terbitkan Berita' ?>
                </button>
                <a href="<?= route_to('dashboard.communication.news') ?>" class="btn btn-outline-neutral-300 radius-8 w-100 py-10 text-sm text-center">
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>

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
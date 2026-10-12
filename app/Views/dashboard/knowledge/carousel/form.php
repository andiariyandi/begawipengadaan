<?= $this->extend('dashboard/layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$articles = (new \App\Models\Knowledge\ArticleModel())
    ->select('id, title, type, workflow_status, cover_file_id')
    ->where('deleted_at IS NULL', null, false)
    ->where('workflow_status', 'published')
    ->orderBy('title', 'ASC')->findAll();

$media = (new \App\Models\Knowledge\KnowledgeMediaItemModel())
    ->select('id, title, type, workflow_status, file_id, thumbnail_file_id')
    ->where('workflow_status', 'published')
    ->orderBy('title', 'ASC')->findAll();
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
        <h6 class="fw-semibold mb-1">Tambah Konten ke Carousel</h6>
        <p class="text-secondary-light mb-0">Pilih artikel atau materi visual yang dipublikasikan untuk dijadikan hero slider utama portal Bunga Rampai.</p>
    </div>
    <a href="<?= route_to('dashboard.knowledge.carousel') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
        <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
        <span>Kembali ke Carousel</span>
    </a>
</div>

<form method="post" action="<?= route_to('dashboard.knowledge.carousel.create') ?>">
    <?= csrf_field() ?>

    <div class="row gy-24">
        <!-- Kolom Kiri: Pemilihan Konten & Urutan -->
        <div class="col-xl-7 col-lg-7">
            <div class="card border-0 radius-16 shadow-sm bg-white mb-24">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:slider-vertical-bold" class="text-primary-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Konfigurasi Target Carousel</h6>
                </div>
                <div class="card-body p-24">
                    <!-- Pilih Tipe Target -->
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Tipe Sumber Konten <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <label class="border radius-8 p-16 flex-fill cursor-pointer bg-base hover-border-primary" style="cursor: pointer;">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="target_type" id="typeArticle" value="article" checked onchange="handleTargetTypeSwitch('article')">
                                    <label class="form-check-label fw-semibold text-dark ps-2" for="typeArticle">
                                        <i class="fas fa-newspaper text-primary me-1"></i> Artikel PBJ / Studi Kasus
                                    </label>
                                </div>
                                <span class="text-xs text-secondary-light ps-24 d-block mt-1">Artikel, studi kasus, atau panduan pengadaan</span>
                            </label>

                            <label class="border radius-8 p-16 flex-fill cursor-pointer bg-base hover-border-primary" style="cursor: pointer;">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="target_type" id="typeMedia" value="media" onchange="handleTargetTypeSwitch('media')">
                                    <label class="form-check-label fw-semibold text-dark ps-2" for="typeMedia">
                                        <i class="fas fa-photo-video text-info me-1"></i> Media &amp; Infografik
                                    </label>
                                </div>
                                <span class="text-xs text-secondary-light ps-24 d-block mt-1">Infografik, video edukasi, atau foto galeri</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dropdown Konten Artikel -->
                    <div class="mb-20" id="articleSelectContainer">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Artikel yang Dipublikasikan <span class="text-danger">*</span></label>
                        <select name="target_id_article" id="targetIdArticle" class="form-select radius-8" onchange="updateSelectedPreview('article', this)">
                            <option value="">— Pilih Artikel PBJ —</option>
                            <?php foreach ($articles as $a): ?>
                                <?php $coverUrl = ! empty($a['cover_file_id']) ? base_url('file/' . (int) $a['cover_file_id']) : ''; ?>
                                <option value="<?= (int) $a['id'] ?>" data-cover="<?= esc($coverUrl) ?>" data-title="<?= esc($a['title']) ?>" data-type="<?= esc($a['type']) ?>">
                                    [<?= esc(strtoupper($a['type'])) ?>] <?= esc($a['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="text-xs text-secondary-light mt-1 d-block">Hanya artikel berstatus <strong>published</strong> yang dapat dijadikan slider carousel.</span>
                    </div>

                    <!-- Dropdown Konten Media -->
                    <div class="mb-20 d-none" id="mediaSelectContainer">
                        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Pilih Materi Media yang Dipublikasikan <span class="text-danger">*</span></label>
                        <select name="target_id_media" id="targetIdMedia" class="form-select radius-8" onchange="updateSelectedPreview('media', this)">
                            <option value="">— Pilih Media / Infografik —</option>
                            <?php foreach ($media as $m): ?>
                                <?php 
                                $mCover = ! empty($m['thumbnail_file_id']) 
                                    ? base_url('file/' . (int) $m['thumbnail_file_id']) 
                                    : (! empty($m['file_id']) ? base_url('file/' . (int) $m['file_id']) : '');
                                ?>
                                <option value="<?= (int) $m['id'] ?>" data-cover="<?= esc($mCover) ?>" data-title="<?= esc($m['title'] ?: 'Item #' . $m['id']) ?>" data-type="<?= esc($m['type']) ?>">
                                    [<?= esc(strtoupper($m['type'])) ?>] <?= esc($m['title'] ?: '(Tanpa Judul)') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="text-xs text-secondary-light mt-1 d-block">Hanya materi media berstatus <strong>published</strong> yang dapat dijadikan slider carousel.</span>
                    </div>

                    <!-- Hidden target_id yang akan disubmit -->
                    <input type="hidden" name="target_id" id="finalTargetId" value="">

                    <!-- Urutan Tampilan Slider -->
                    <div class="row g-3 mb-24">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Urutan Prioritas Slider (Urutan)</label>
                            <input type="number" min="0" name="order" class="form-control radius-8" value="1" placeholder="1">
                            <span class="text-xs text-secondary-light mt-1 d-block">Angka lebih kecil tampil lebih awal pada slide carousel.</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center justify-content-end gap-12 pt-20 border-top">
                        <a href="<?= route_to('dashboard.knowledge.carousel') ?>" class="btn btn-outline-neutral-300 radius-8 px-20 py-11">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11 d-inline-flex align-items-center gap-2">
                            <iconify-icon icon="solar:check-circle-bold" class="text-lg"></iconify-icon>
                            <span>Aktifkan ke Carousel</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Panduan Ukuran Banner & Pratinjau Cover -->
        <div class="col-xl-5 col-lg-5">
            <!-- Box Rekomendasi Banner -->
            <div class="card border-0 radius-16 shadow-sm bg-white mb-24">
                <div class="card-header border-bottom p-20 bg-white d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:info-circle-bold" class="text-info-600 text-xl"></iconify-icon>
                    <h6 class="text-base fw-bold mb-0">Rekomendasi Format &amp; Ukuran Banner</h6>
                </div>
                <div class="card-body p-24">
                    <div class="alert alert-info bg-info-50 border-info-200 radius-12 p-16 mb-20">
                        <strong class="text-info-900 d-block text-sm mb-2"><i class="fas fa-ruler-combined me-1"></i> Ukuran Ideal Carousel:</strong>
                        <ul class="mb-0 ps-16 text-xs text-info-800 d-flex flex-column gap-2">
                            <li>
                                <strong>Panoramic Banner: 1920 x 600 px (Rasio 3.2:1)</strong><br>
                                <span class="text-secondary-light">Tampilan hero slider layar penuh desktop tanpa terpotong.</span>
                            </li>
                            <li>
                                <strong>Landscape Standar: 1200 x 630 px (Rasio 16:9)</strong><br>
                                <span class="text-secondary-light">Rasio gambar standar Open Graph / sosial media yang fleksibel di mobile.</span>
                            </li>
                            <li>
                                <strong>Fokus Objek di Tengah:</strong><br>
                                <span class="text-secondary-light">Pastikan teks penting atau subjek utama tidak mepet pinggir banner agar aman saat responsif.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Live Pratinjau Cover Konten Terpilih -->
                    <div class="border radius-12 p-16 bg-neutral-50 text-center">
                        <h6 class="text-xs fw-bold text-secondary-light text-uppercase mb-12">Pratinjau Cover Konten Terpilih</h6>
                        <div id="previewCoverBox" class="position-relative overflow-hidden radius-8 border bg-white mb-12 d-flex align-items-center justify-content-center" style="height: 160px;">
                            <img id="previewCoverImg" src="" alt="Pratinjau Banner" class="w-100 h-100 object-fit-cover d-none">
                            <div id="previewCoverPlaceholder" class="text-secondary-light p-20 text-center">
                                <iconify-icon icon="solar:gallery-wide-broken" class="text-4xl text-neutral-400 mb-1"></iconify-icon>
                                <span class="d-block text-xs">Pilih artikel atau media untuk melihat pratinjau gambar cover</span>
                            </div>
                        </div>
                        <div id="selectedMetaBox" class="text-start d-none">
                            <strong id="selectedMetaTitle" class="d-block text-dark text-xs mb-1"></strong>
                            <span id="selectedMetaType" class="badge bg-primary-subtle text-primary-emphasis text-xs"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function handleTargetTypeSwitch(type) {
    const artContainer = document.getElementById('articleSelectContainer');
    const medContainer = document.getElementById('mediaSelectContainer');
    const artSelect = document.getElementById('targetIdArticle');
    const medSelect = document.getElementById('targetIdMedia');

    if (type === 'article') {
        artContainer.classList.remove('d-none');
        medContainer.classList.add('d-none');
        updateSelectedPreview('article', artSelect);
    } else {
        artContainer.classList.add('d-none');
        medContainer.classList.remove('d-none');
        updateSelectedPreview('media', medSelect);
    }
}

function updateSelectedPreview(type, selectEl) {
    const finalInput = document.getElementById('finalTargetId');
    const selectedOpt = selectEl.options[selectEl.selectedIndex];
    const previewImg = document.getElementById('previewCoverImg');
    const placeholder = document.getElementById('previewCoverPlaceholder');
    const metaBox = document.getElementById('selectedMetaBox');
    const metaTitle = document.getElementById('selectedMetaTitle');
    const metaType = document.getElementById('selectedMetaType');

    if (!selectedOpt || !selectedOpt.value) {
        finalInput.value = '';
        previewImg.classList.add('d-none');
        previewImg.src = '';
        placeholder.classList.remove('d-none');
        metaBox.classList.add('d-none');
        return;
    }

    finalInput.value = selectedOpt.value;
    const coverUrl = selectedOpt.getAttribute('data-cover');
    const title = selectedOpt.getAttribute('data-title');
    const itemType = selectedOpt.getAttribute('data-type');

    metaBox.classList.remove('d-none');
    metaTitle.textContent = title || '(Tanpa Judul)';
    metaType.textContent = (itemType || type).toUpperCase();

    if (coverUrl && coverUrl.trim() !== '') {
        previewImg.src = coverUrl;
        previewImg.classList.remove('d-none');
        placeholder.classList.add('d-none');
    } else {
        previewImg.classList.add('d-none');
        previewImg.src = '';
        placeholder.classList.remove('d-none');
    }
}

// Inisialisasi awal saat halaman termuat
document.addEventListener('DOMContentLoaded', function() {
    const artSelect = document.getElementById('targetIdArticle');
    if (artSelect) {
        updateSelectedPreview('article', artSelect);
    }
});
</script>

<?= $this->endSection() ?>
<?= $this->extend('landing/layouts/public') ?>
<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>

<?php
$catalogs = [
    'articles' => [
        ['title' => 'Menyusun Spesifikasi yang Tepat', 'text' => 'Panduan menjaga kebutuhan tetap jelas, terukur, dan tidak mengarah.', 'slug' => 'menyusun-spesifikasi-yang-tepat'],
        ['title' => 'Strategi Pengadaan Efisien', 'text' => 'Langkah menghubungkan perencanaan dengan hasil pengadaan yang optimal.', 'slug' => 'strategi-pengadaan-efisien'],
        ['title' => 'Kontrak dan Pengendalian', 'text' => 'Praktik menjaga mutu, waktu, dan administrasi pelaksanaan kontrak.', 'slug' => 'kontrak-dan-pengendalian'],
        ['title' => 'Integritas dalam PBJ', 'text' => 'Membangun keputusan yang transparan, akuntabel, dan bebas konflik kepentingan.', 'slug' => 'integritas-dalam-pbj'],
    ],
    'faqs' => [
        ['title' => 'Apa itu RUP?', 'text' => 'Rencana Umum Pengadaan menjadi dasar informasi kebutuhan pengadaan.', 'slug' => 'apa-itu-rup'],
        ['title' => 'Kapan menggunakan e-purchasing?', 'text' => 'Gunakan sesuai ketentuan dan ketersediaan produk pada katalog elektronik.', 'slug' => 'kapan-menggunakan-e-purchasing'],
        ['title' => 'Bagaimana perubahan kontrak?', 'text' => 'Perubahan harus memiliki dasar, persetujuan, dan dokumentasi yang sesuai.', 'slug' => 'bagaimana-perubahan-kontrak'],
        ['title' => 'Di mana meminta bantuan?', 'text' => 'Gunakan kanal layanan dan forum diskusi BegawiPBJ.', 'slug' => 'di-mana-meminta-bantuan'],
    ],
    'cases' => [
        ['title' => 'Konsolidasi Pengadaan ATK', 'text' => 'Pelajaran dari penggabungan kebutuhan beberapa unit kerja.', 'slug' => 'konsolidasi-pengadaan-atk'],
        ['title' => 'Pengadaan Berkelanjutan', 'text' => 'Menimbang dampak lingkungan dan manfaat sosial dalam spesifikasi.', 'slug' => 'pengadaan-berkelanjutan'],
        ['title' => 'Keterlambatan Pelaksanaan', 'text' => 'Menganalisis risiko waktu dan langkah pengendalian kontrak.', 'slug' => 'keterlambatan-pelaksanaan'],
        ['title' => 'Evaluasi Penyedia', 'text' => 'Menjaga evaluasi tetap objektif dengan bukti yang dapat ditelusuri.', 'slug' => 'evaluasi-penyedia'],
    ],
    'templates' => [
        ['title' => 'Format Rencana Pengadaan', 'text' => 'Contoh struktur kebutuhan, jadwal, dan strategi pengadaan.', 'slug' => 'format-rencana-pengadaan'],
        ['title' => 'Matriks Risiko PBJ', 'text' => 'Template identifikasi, penilaian, dan mitigasi risiko.', 'slug' => 'matriks-risiko-pbj'],
        ['title' => 'Checklist Pengendalian Kontrak', 'text' => 'Daftar pemeriksaan untuk memantau kewajiban para pihak.', 'slug' => 'checklist-pengendalian-kontrak'],
        ['title' => 'Berita Acara Serah Terima', 'text' => 'Contoh elemen informasi penting dalam dokumentasi serah terima.', 'slug' => 'berita-acara-serah-terima'],
    ],
    'media' => [
        ['title' => 'Infografik Tahapan PBJ', 'text' => 'Ringkasan visual tahapan PBJ dari perencanaan sampai evaluasi.', 'slug' => 'infografik-tahapan-pbj'],
        ['title' => 'Kartu Tips Integritas', 'text' => 'Pesan singkat untuk menjaga integritas dalam pekerjaan harian.', 'slug' => 'kartu-tips-integritas'],
        ['title' => 'Peta Layanan BegawiPBJ', 'text' => 'Panduan mengenali kanal dan layanan yang tersedia.', 'slug' => 'peta-layanan-begawipbj'],
        ['title' => 'Kalender Kompetensi', 'text' => 'Visual agenda belajar dan kegiatan ekosistem PBJ.', 'slug' => 'kalender-kompetensi'],
    ],
    'videos' => [
        ['title' => 'Mengenal BegawiPBJ', 'text' => 'Video pengantar layanan dan manfaat ekosistem pembelajaran.', 'slug' => 'mengenal-begawipbj'],
        ['title' => 'Praktik Baik Perencanaan', 'text' => 'Berbagi pengalaman menyusun kebutuhan yang berkualitas.', 'slug' => 'praktik-baik-perencanaan'],
        ['title' => 'Diskusi Pengelolaan Kontrak', 'text' => 'Rekaman dialog praktisi tentang pengendalian kontrak.', 'slug' => 'diskusi-pengelolaan-kontrak'],
        ['title' => 'Klinik Tanya Jawab PBJ', 'text' => 'Jawaban pengajar atas pertanyaan peserta.', 'slug' => 'klinik-tanya-jawab-pbj'],
    ],
    'gallery' => [
        ['title' => 'Forum PBJ Lampung', 'text' => 'Dokumentasi forum berbagi antarunit kerja.', 'slug' => 'forum-pbj-lampung'],
        ['title' => 'Kelas Kompetensi', 'text' => 'Suasana pembelajaran dan diskusi peserta.', 'slug' => 'kelas-kompetensi'],
        ['title' => 'Klinik Dokumen', 'text' => 'Kolaborasi dalam meninjau dokumen pengadaan.', 'slug' => 'klinik-dokumen'],
        ['title' => 'Apresiasi Penggerak PBJ', 'text' => 'Momen apresiasi bagi penggerak ekosistem.', 'slug' => 'apresiasi-penggerak-pbj'],
    ],
];
$items = $catalogs[$catalogType] ?? [];
$detailPrefix = ['articles' => 'artikel', 'cases' => 'studi-kasus', 'templates' => 'template-dokumen'][$catalogType] ?? '';
?>

<section class="category-one knowledge-catalog">
    <div class="category-one__bg-shape" style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);"></div>
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Bunga Rampai BegawiPBJ</p></div>
            <h2 class="section-title__title"><?= esc($pageTitle) ?></h2>
            <p class="learning-catalog__intro"><?= esc($description) ?></p>
        </div>
        <?php if ($catalogType === 'faqs'): ?>
            <div class="row knowledge-faq-list">
                <?php foreach ($items as $item): ?>
                    <div class="col-xl-6">
                        <details class="knowledge-faq">
                            <summary><?= esc($item['title']) ?><span class="icon-plus"></span></summary>
                            <p><?= esc($item['text']) ?> BegawiPBJ menyediakan referensi dan kanal pembelajaran untuk membantu unit kerja menerapkannya dengan tertib.</p>
                        </details>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($catalogType === 'gallery'): ?>
            <div class="row gallery-page knowledge-gallery">
                <?php foreach ($items as $index => $item): ?>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="gallery-page__single">
                            <div class="gallery-page__img">
                                <div class="gallery-page__img-box"><img src="<?= base_url('assets/main/images/gallery/gallery-page-1-' . (($index % 4) + 1) . '.jpg') ?>" alt="<?= esc($item['title']) ?>"></div>
                                <div class="gallery-page__icon"><a href="<?= base_url('assets/main/images/gallery/gallery-page-1-' . (($index % 4) + 1) . '.jpg') ?>" class="img-popup" aria-label="Lihat <?= esc($item['title']) ?>"><span class="icon-plus"></span></a></div>
                            </div>
                            <h3 class="knowledge-gallery__title"><?= esc($item['title']) ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($catalogType === 'articles'): ?>
            <div class="row blog-page knowledge-blog">
                <?php foreach ($items as $index => $item): $url = 'bunga-rampai/artikel/' . $item['slug']; ?>
                    <div class="col-xl-6 col-lg-6">
                        <article class="blog-two__single">
                            <div class="blog-two__img"><img src="<?= base_url('assets/main/images/blog/blog-2-' . (($index % 4) + 1) . '.jpg') ?>" alt="<?= esc($item['title']) ?>"></div>
                            <div class="blog-two__content">
                                <ul class="blog-two__meta list-unstyled"><li><span class="icon-tags"></span> Pengetahuan PBJ</li><li><span class="icon-clock"></span> 8 menit</li></ul>
                                <h4 class="blog-two__title"><a href="<?= site_url($url) ?>"><?= esc($item['title']) ?></a></h4>
                                <p class="blog-two__text"><?= esc($item['text']) ?></p>
                                <a class="thm-btn" href="<?= site_url($url) ?>"><span class="icon-angles-right"></span> Baca Artikel</a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($catalogType === 'templates'): ?>
            <div class="knowledge-document-list">
                <?php foreach ($items as $item): ?>
                    <article class="knowledge-document">
                        <a class="knowledge-document__icon" href="<?= site_url('bunga-rampai/template-dokumen/' . $item['slug'] . '/unduh') ?>" aria-label="Unduh <?= esc($item['title']) ?>"><span class="icon-file"></span></a>
                        <div><h3><?= esc($item['title']) ?></h3><p><?= esc($item['text']) ?></p><small>Format dummy · 120 KB</small></div>
                        <a class="thm-btn" href="<?= site_url('bunga-rampai/template-dokumen/' . $item['slug'] . '/unduh') ?>"><span class="icon-download"></span> Unduh</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($items as $item): $url = $detailPrefix ? 'bunga-rampai/' . $detailPrefix . '/' . $item['slug'] : 'bunga-rampai/' . ($catalogType === 'videos' ? 'video' : $catalogType) . '#' . $item['slug']; ?>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <article class="category-one__single">
                            <div class="category-one__icon"><img src="<?= base_url('assets/main/images/icon/why-choose-one-icon-1.png') ?>" alt=""></div>
                            <h3 class="category-one__title"><a href="<?= site_url($url) ?>"><?= esc($item['title']) ?></a></h3>
                            <p class="category-one__text"><?= esc($item['text']) ?></p>
                            <a class="category-one__btn" href="<?= site_url($url) ?>"><span class="icon-angles-right"></span> Jelajahi</a>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?= view('landing/components/pagination', [
            'currentPage' => (int) (service('request')->getGet('page') ?? 1),
            'totalPages' => 3,
        ]) ?>
    </div>
</section>

<?= $this->endSection() ?>

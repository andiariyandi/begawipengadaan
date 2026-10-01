<?= $this->extend('landing/layouts/public') ?>
<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => 'Bunga Rampai']) ?>

<section class="category-one knowledge-home">
    <div class="category-one__bg-shape" style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);"></div>
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box"><div class="section-title__tagline-shape"></div><p class="section-title__tagline">Pengetahuan PBJ Lampung</p></div>
            <h2 class="section-title__title">Temukan Wawasan dan Referensi</h2>
        </div>
        <div class="row">
            <?php
            $items = [
                ['title' => 'Artikel PBJ', 'text' => 'Wawasan, regulasi, dan praktik baik pengadaan.', 'url' => 'bunga-rampai/artikel', 'icon' => 'why-choose-one-icon-1.png'],
                ['title' => 'Tanya Jawab PBJ', 'text' => 'Jawaban singkat untuk pertanyaan yang sering muncul.', 'url' => 'bunga-rampai/faq', 'icon' => 'why-choose-one-icon-2.png'],
                ['title' => 'Studi Kasus', 'text' => 'Belajar dari pengalaman dan tantangan di lapangan.', 'url' => 'bunga-rampai/studi-kasus', 'icon' => 'why-choose-one-icon-3.png'],
                ['title' => 'Template Dokumen', 'text' => 'Contoh dokumen pendukung proses PBJ.', 'url' => 'bunga-rampai/template-dokumen', 'icon' => 'why-choose-one-icon-4.png'],
                ['title' => 'Media PBJ', 'text' => 'Infografik dan materi visual yang mudah dipahami.', 'url' => 'bunga-rampai/media', 'icon' => 'why-choose-one-icon-1.png'],
                ['title' => 'Video PBJ', 'text' => 'Sajian video pembelajaran dan diskusi.', 'url' => 'bunga-rampai/video', 'icon' => 'why-choose-one-icon-2.png'],
                ['title' => 'Galeri Kegiatan', 'text' => 'Dokumentasi kolaborasi ekosistem PBJ Lampung.', 'url' => 'bunga-rampai/galeri', 'icon' => 'why-choose-one-icon-3.png'],
            ];
            foreach ($items as $item): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <article class="category-one__single">
                        <div class="category-one__icon"><img src="<?= base_url('assets/main/images/icon/' . $item['icon']) ?>" alt=""></div>
                        <h3 class="category-one__title"><a href="<?= site_url($item['url']) ?>"><?= esc($item['title']) ?></a></h3>
                        <p class="category-one__text"><?= esc($item['text']) ?></p>
                        <a class="category-one__btn" href="<?= site_url($item['url']) ?>"><span class="icon-angles-right"></span> Jelajahi</a>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->extend('landing/layouts/landing') ?>

<?= $this->section('content') ?>
<?= view('landing/partials/breadcrumbs', ['breadcrumbs' => $breadcrumbs ?? []]) ?>

<section class="about-one about-one--about-page">
    <div class="about-one__shape-1 float-bob-y"><img src="<?= base_url('assets/main/images/shapes/about-one-shape-1.png') ?>" alt=""></div>
    <div class="about-one__shape-2 float-bob-x"><img src="<?= base_url('assets/main/images/shapes/about-one-shape-2.png') ?>" alt=""></div>
    <div class="container"><div class="row align-items-center"><div class="col-xl-6"><div class="about-one__left"><div class="about-one__img-box"><div class="about-one__img"><img src="<?= base_url('assets/main/images/resources/abou-one-img-1.jpg') ?>" alt="Tentang BegawiPBJ"></div><div class="about-one__img-2"><img src="<?= base_url('assets/main/images/resources/abou-one-img-2.jpg') ?>" alt="Ekosistem PBJ"></div><div class="about-one__experience"><div class="about-one__experience-icon"><img src="<?= base_url('assets/main/images/icon/about-one-experience-icon.png') ?>" alt=""></div><div class="about-one__experience-content"><h3>1</h3><p>Platform Terintegrasi</p></div></div></div></div></div><div class="col-xl-6"><div class="about-one__right"><div class="section-title text-left"><div class="section-title__tagline-box"><span class="section-title__tagline">Tentang BegawiPBJ</span></div><h1 class="section-title__title">Ruang Digital untuk Ekosistem Pengadaan Barang/Jasa</h1></div><p class="about-one__text">BegawiPBJ adalah platform digital Pemerintah Provinsi Lampung yang dirancang untuk mendukung pembelajaran, pengelolaan pengetahuan, akses regulasi, serta kolaborasi dalam Pengadaan Barang/Jasa.</p><p class="about-one__text">Kami menyatukan layanan yang relevan bagi ASN, pengajar, evaluator, operator, penyedia, akademisi, dan masyarakat dalam satu pengalaman digital yang lebih mudah diakses.</p><ul class="about-one__points list-unstyled"><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Pembelajaran dan pengembangan kompetensi PBJ</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Informasi, regulasi, dan referensi pengadaan yang terorganisasi</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Event, sertifikat, verifikasi, dan statistik publik</p></div></li></ul></div></div></div></div>
</section>

<section class="why-choose-one">
    <div class="why-choose-one__shape-1 float-bob-x"><img src="<?= base_url('assets/main/images/shapes/why-choose-one-shape-1.png') ?>" alt=""></div>
    <div class="why-choose-one__shape-2 float-bob-y"><img src="<?= base_url('assets/main/images/shapes/why-choose-one-shape-2.png') ?>" alt=""></div>
    <div class="container"><div class="row"><div class="col-xl-6"><div class="why-choose-one__left"><div class="section-title text-left"><div class="section-title__tagline-box"><span class="section-title__tagline">Arah Strategis</span></div><h2 class="section-title__title">Visi dan Misi BegawiPBJ</h2></div><h3 class="why-choose-one__title">Visi</h3><p class="why-choose-one__text">Menjadi platform digital pengadaan yang inklusif, adaptif, dan tepercaya untuk mendukung peningkatan kompetensi serta tata kelola PBJ Pemerintah Provinsi Lampung.</p><h3 class="why-choose-one__title mt-4">Misi</h3><ul class="why-choose-one__points list-unstyled"><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Menyediakan pembelajaran PBJ yang mudah diakses dan relevan.</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Mengintegrasikan informasi, regulasi, serta pengetahuan pengadaan.</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Mendorong kolaborasi lintas pemangku kepentingan PBJ.</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Menyajikan layanan digital yang transparan dan akuntabel.</p></div></li></ul></div></div><div class="col-xl-6"><div class="why-choose-one__right"><div class="why-choose-one__img"><img src="<?= base_url('assets/main/images/resources/why-choose-one-img-1.jpg') ?>" alt="Visi BegawiPBJ"></div><div class="why-choose-one__img-2"><img src="<?= base_url('assets/main/images/resources/why-choose-one-img-2.jpg') ?>" alt="Misi BegawiPBJ"></div></div></div></div></div>
</section>

<section class="counter-two">
    <div class="container"><div class="row">
        <?php foreach ([['image' => 'counter-2-1.jpg', 'value' => '1248', 'label' => 'Pengguna Terhubung'], ['image' => 'counter-2-2.jpg', 'value' => '48', 'label' => 'OPD Terpetakan'], ['image' => 'counter-2-3.jpg', 'value' => '214', 'label' => 'Sertifikat Kompetensi'], ['image' => 'counter-2-4.jpg', 'value' => '36', 'label' => 'Event Diselenggarakan']] as $counter): ?>
            <div class="col-xl-3 col-lg-6 col-md-6"><div class="counter-two__single"><div class="counter-two__img"><img src="<?= base_url('assets/main/images/resources/' . $counter['image']) ?>" alt=""></div><div class="counter-two__content"><div class="counter-two__count-box"><h3 class="odometer" data-count="<?= esc($counter['value']) ?>">00</h3><span>+</span></div><p class="counter-two__text"><?= esc($counter['label']) ?></p></div></div></div>
        <?php endforeach; ?>
    </div></div>
</section>

<section class="category-one">
    <div class="category-one__bg-shape" style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);"></div>
    <div class="container">
        <?= view('landing/components/section_title', ['tagline' => 'Pilar Layanan', 'title' => 'Ekosistem yang Saling Menguatkan', 'description' => 'Setiap pilar dibangun untuk mendukung pengadaan yang kompeten, terbuka, dan berdampak.', 'alignment' => 'center']) ?>
        <div class="row">
            <?php foreach ([['icon' => 'categoyr-two-icon-1.png', 'title' => 'Pembelajaran', 'text' => 'Program, course, kelas, asesmen, dan perjalanan kompetensi PBJ.'], ['icon' => 'categoyr-two-icon-2.png', 'title' => 'Pengetahuan', 'text' => 'Artikel, FAQ, studi kasus, template, media, dan regulasi.'], ['icon' => 'categoyr-two-icon-4.png', 'title' => 'Komunitas dan Agenda', 'text' => 'Event, webinar, pelatihan, serta sosialisasi PBJ.'], ['icon' => 'categoyr-two-icon-6.png', 'title' => 'Kepercayaan Digital', 'text' => 'Sertifikat, verifikasi, statistik, dan layanan transparan.']] as $pillar): ?>
                <div class="col-xl-3 col-lg-6 col-md-6"><div class="category-one__single"><div class="category-one__icon"><img src="<?= base_url('assets/main/images/icon/' . $pillar['icon']) ?>" alt=""></div><h3 class="category-one__title"><?= esc($pillar['title']) ?></h3><p class="category-one__text"><?= esc($pillar['text']) ?></p></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="newsletter-one"><div class="newsletter-one__shape-1 float-bob-y"><img src="<?= base_url('assets/main/images/shapes/newsletter-one-shape-1.png') ?>" alt=""></div><div class="newsletter-one__bg-shape" style="background-image: url(<?= base_url('assets/main/images/shapes/newsletter-one-bg-shape.png') ?>);"></div><div class="container"><div class="newsletter-one__inner"><div class="newsletter-one__left"><h2 class="newsletter-one__title">Mari Bertumbuh Bersama<br>BegawiPBJ</h2></div><div class="newsletter-one__right"><p class="newsletter-one__text mb-3">Daftarkan akun Anda untuk mengakses pembelajaran, mengikuti event, dan mengelola perjalanan kompetensi PBJ.</p><a href="<?= site_url('daftar') ?>" class="thm-btn"><span class="icon-angles-right"></span>Daftar Sekarang</a></div></div></div></section>
<?= $this->endSection() ?>

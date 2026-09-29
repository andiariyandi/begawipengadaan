<?= $this->extend('landing/layouts/landing') ?>

<?= $this->section('content') ?>
<!-- Banner One Start -->
<section class="banner-one">
    <div class="banner-one__bg-shape-1" style="background-image: url(<?= base_url('assets/main/images/shapes/banner-one-bg-shape-1.png') ?>);"></div>
    <div class="banner-one__icon-1 img-bounce"><img src="<?= base_url('assets/main/images/icon/idea-bulb.png') ?>" alt=""></div>
    <div class="banner-one__icon-2 float-bob-x"><img src="<?= base_url('assets/main/images/icon/3d-alarm.png') ?>" alt=""></div>
    <div class="banner-one__icon-3 float-bob-y"><img src="<?= base_url('assets/main/images/icon/linke-icon.png') ?>" alt=""></div>
    <div class="banner-one__shape-4 float-bob-x"><img src="<?= base_url('assets/main/images/shapes/banner-one-shape-4.png') ?>" alt=""></div>

    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="banner-one__left">
                    <div class="banner-one__title-box">
                        <div class="banner-one__title-box-shape"><img src="<?= base_url('assets/main/images/shapes/banner-one-title-box-shape-1.png') ?>" alt=""></div>
                        <h1 class="banner-one__title">Belajar dan Bertumbuh<br>
                            <span class="banner-one__title-clr-1">Bersama</span>
                            <span class="banner-one__title-clr-2">Ekosistem<br>PBJ</span>
                        </h1>
                    </div>
                    <p class="banner-one__text">BegawiPBJ adalah platform informasi, pembelajaran, dan kolaborasi untuk memperkuat kompetensi Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.</p>
                    <div class="banner-one__thm-and-other-btn-box">
                        <div class="banner-one__btn-box"><a href="<?= site_url('arena-belajar') ?>" class="thm-btn"><span class="icon-angles-right"></span>Mulai Belajar</a></div>
                        <div class="banner-one__other-btn-box">
                            <a href="<?= site_url('regulasi') ?>" class="banner-one__other-btn-1"><span class="icon-thumbs-up"></span>Regulasi PBJ</a>
                            <a href="<?= site_url('event') ?>" class="banner-one__other-btn-1 banner-one__other-btn-2"><span class="icon-thumbs-up"></span>Event dan Agenda</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="banner-one__right">
                    <div class="banner-one__img-box">
                        <div class="banner-one__img">
                            <img src="<?= base_url('assets/main/images/resources/banner-two-img-1.png') ?>" alt="BegawiPBJ Arena Belajar">
                            <div class="banner-one__img-shape-box rotate-me">
                                <div class="banner-one__img-shape-1"><div class="banner-one__img-shape-2"></div></div>
                                <div class="banner-one__shape-1"><img src="<?= base_url('assets/main/images/shapes/banner-one-shape-1.png') ?>" alt=""></div>
                                <div class="banner-one__shape-2 rotate-me"><img src="<?= base_url('assets/main/images/shapes/banner-one-shape-2.png') ?>" alt=""></div>
                                <div class="banner-one__shape-3"><img src="<?= base_url('assets/main/images/shapes/banner-one-shape-3.png') ?>" alt=""></div>
                            </div>
                            <div class="banner-one__udemy-review">
                                <div class="banner-one__udemy-review-img"><img src="<?= base_url('assets/main/images/resources/banner-one-udemy-review-img.jpg') ?>" alt=""></div>
                                <div class="banner-one__udemy-review-content"><div class="banner-one__udemy-review-logo"><img src="<?= base_url('assets/main/images/resources/banner-one-udemy-review-logo.png') ?>" alt=""></div><p>1.248+ Peserta<br>Terhubung</p></div>
                            </div>
                            <div class="banner-one__student-trained">
                                <div class="banner-one__student-trained-img"><img src="<?= base_url('assets/main/images/resources/banner-one-student-trained-img-1-1.jpg') ?>" alt=""></div>
                                <div class="banner-one__student-trained-img banner-one__student-trained-img-2"><img src="<?= base_url('assets/main/images/resources/banner-one-student-trained-img-1-2.jpg') ?>" alt=""></div>
                                <div class="banner-one__student-trained-content"><h3>824+</h3><p>Peserta<br>Lulus</p></div>
                                <div class="banner-one__student-trained-shape"><img src="<?= base_url('assets/main/images/shapes/banner-one-student-trained-shape-1.png') ?>" alt=""></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Banner One End -->

<!-- Category One Start -->
<section class="category-one">
    <div class="category-one__bg-shape" style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);"></div>
    <div class="category-one__shape-1 float-bob-x"><img src="<?= base_url('assets/main/images/shapes/category-one-shape-1.png') ?>" alt=""></div>
    <div class="category-one__shape-2 float-bob-y"><img src="<?= base_url('assets/main/images/shapes/category-one-shape-2.png') ?>" alt=""></div>
    <div class="container">
        <?= view('landing/components/section_title', ['tagline' => 'Layanan Utama', 'title' => 'Jelajahi Ekosistem BegawiPBJ', 'description' => 'Layanan terintegrasi untuk pembelajaran, pengetahuan, regulasi, dan aktivitas PBJ.', 'alignment' => 'center']) ?>
        <div class="row">
            <?php foreach ([
                ['icon' => 'categoyr-two-icon-1.png', 'title' => 'Arena Belajar', 'text' => 'Program, course, kelas, dan pengajar PBJ.', 'url' => 'arena-belajar'],
                ['icon' => 'categoyr-two-icon-2.png', 'title' => 'Bunga Rampai', 'text' => 'Artikel, FAQ, studi kasus, dan template.', 'url' => 'bunga-rampai'],
                ['icon' => 'categoyr-two-icon-3.png', 'title' => 'Regulasi PBJ', 'text' => 'Dokumen regulasi dan referensi pengadaan.', 'url' => 'regulasi'],
                ['icon' => 'categoyr-two-icon-4.png', 'title' => 'Event dan Agenda', 'text' => 'Webinar, pelatihan, dan sosialisasi PBJ.', 'url' => 'event'],
            ] as $item): ?>
                <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                    <div class="category-one__single">
                        <div class="category-one__icon"><img src="<?= base_url('assets/main/images/icon/' . $item['icon']) ?>" alt=""></div>
                        <h3 class="category-one__title"><a href="<?= site_url($item['url']) ?>"><?= esc($item['title']) ?></a></h3>
                        <p class="category-one__text"><?= esc($item['text']) ?></p>
                        <a href="<?= site_url($item['url']) ?>" class="category-one__read-more">Jelajahi <span class="icon-right-arrow"></span></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- Category One End -->

<!-- About One Start -->
<section class="about-one">
    <div class="about-one__shape-1 float-bob-y"><img src="<?= base_url('assets/main/images/shapes/about-one-shape-1.png') ?>" alt=""></div>
    <div class="about-one__shape-2 float-bob-x"><img src="<?= base_url('assets/main/images/shapes/about-one-shape-2.png') ?>" alt=""></div>
    <div class="container"><div class="row"><div class="col-xl-6"><div class="about-one__left"><div class="about-one__img-box"><div class="about-one__img"><img src="<?= base_url('assets/main/images/resources/abou-one-img-1.jpg') ?>" alt="Ekosistem BegawiPBJ"></div><div class="about-one__img-2"><img src="<?= base_url('assets/main/images/resources/abou-one-img-2.jpg') ?>" alt="Pembelajaran PBJ"></div><div class="about-one__experience"><div class="about-one__experience-icon"><img src="<?= base_url('assets/main/images/icon/about-one-experience-icon.png') ?>" alt=""></div><div class="about-one__experience-content"><h3>48</h3><p>OPD Terhubung</p></div></div></div></div></div><div class="col-xl-6"><div class="about-one__right"><div class="section-title text-left"><div class="section-title__tagline-box"><span class="section-title__tagline">Tentang BegawiPBJ</span></div><h2 class="section-title__title">Satu Ruang Digital untuk Pengadaan yang Lebih Baik</h2></div><p class="about-one__text">BegawiPBJ menghubungkan pembelajaran, pengetahuan, regulasi, sertifikat, event, dan informasi pengadaan dalam satu platform yang mudah diakses.</p><ul class="about-one__points list-unstyled"><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Pembelajaran PBJ yang terstruktur dan relevan</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Referensi regulasi dan pengetahuan yang lebih mudah ditemukan</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Kolaborasi melalui kelas, event, dan agenda pengembangan</p></div></li></ul><a href="<?= site_url('tentang') ?>" class="thm-btn about-one__btn"><span class="icon-angles-right"></span>Selengkapnya</a></div></div></div></div>
</section>
<!-- About One End -->

<!-- Courses One Start -->
<section class="courses-one">
    <div class="container">
        <?= view('landing/components/section_title', ['tagline' => 'Arena Belajar', 'title' => 'Course Pilihan untuk Kompetensi PBJ', 'description' => 'Pratinjau course pembelajaran. Data aktual akan terhubung ke modul pembelajaran.', 'alignment' => 'center']) ?>
        <div class="row">
            <?php foreach ([
                ['image' => 'courses-1-1.jpg', 'category' => 'Fundamental', 'title' => 'Pengantar Pengadaan Barang/Jasa', 'lessons' => '8 Materi', 'duration' => '4 Jam', 'slug' => 'pengantar-pengadaan-pbj'],
                ['image' => 'courses-1-2.jpg', 'category' => 'Perencanaan', 'title' => 'Perencanaan Pengadaan Strategis', 'lessons' => '7 Materi', 'duration' => '4 Jam', 'slug' => 'perencanaan-pengadaan'],
                ['image' => 'courses-1-3.jpg', 'category' => 'Kontrak', 'title' => 'Manajemen Kontrak Pemerintah', 'lessons' => '10 Materi', 'duration' => '5 Jam', 'slug' => 'manajemen-kontrak-pemerintah'],
            ] as $course): ?>
                <div class="col-xl-4 col-lg-4 wow fadeInUp" data-wow-delay="100ms"><div class="courses-one__single"><div class="courses-one__img"><img src="<?= base_url('assets/main/images/resources/' . $course['image']) ?>" alt="<?= esc($course['title']) ?>"><div class="courses-one__img-link"><a href="<?= site_url('arena-belajar/course/' . $course['slug']) ?>"><span class="icon-plus"></span></a></div></div><div class="courses-one__content"><div class="courses-one__top"><p><?= esc($course['category']) ?></p><div class="courses-one__ratings"><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span></div></div><h3 class="courses-one__title"><a href="<?= site_url('arena-belajar/course/' . $course['slug']) ?>"><?= esc($course['title']) ?></a></h3><ul class="courses-one__meta list-unstyled"><li><a href="<?= site_url('arena-belajar/course/' . $course['slug']) ?>"><span class="icon-book"></span><?= esc($course['lessons']) ?></a></li><li><a href="<?= site_url('arena-belajar/course/' . $course['slug']) ?>"><span class="icon-clock"></span><?= esc($course['duration']) ?></a></li></ul></div></div></div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5"><a href="<?= site_url('arena-belajar/course') ?>" class="thm-btn"><span class="icon-angles-right"></span>Semua Course</a></div>
    </div>
</section>
<!-- Courses One End -->

<!-- Why Choose One Start -->
<section class="why-choose-one">
    <div class="why-choose-one__shape-1 float-bob-x"><img src="<?= base_url('assets/main/images/shapes/why-choose-one-shape-1.png') ?>" alt=""></div>
    <div class="why-choose-one__shape-2 float-bob-y"><img src="<?= base_url('assets/main/images/shapes/why-choose-one-shape-2.png') ?>" alt=""></div>
    <div class="container"><div class="row"><div class="col-xl-6"><div class="why-choose-one__left"><div class="section-title text-left"><div class="section-title__tagline-box"><span class="section-title__tagline">Mengapa BegawiPBJ</span></div><h2 class="section-title__title">Belajar, Berbagi, dan Berkembang Bersama</h2></div><p class="why-choose-one__text">Platform ini dirancang untuk membantu setiap pengguna menemukan pembelajaran, referensi, dan layanan yang relevan dengan perannya dalam ekosistem PBJ.</p><ul class="why-choose-one__points list-unstyled"><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Akses informasi dan pembelajaran dalam satu platform</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Mendukung pengembangan kompetensi secara berkelanjutan</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Memperkuat tata kelola pengadaan yang transparan dan akuntabel</p></div></li></ul><a href="<?= site_url('daftar') ?>" class="thm-btn"><span class="icon-angles-right"></span>Daftar Sekarang</a></div></div><div class="col-xl-6"><div class="why-choose-one__right"><div class="why-choose-one__img"><img src="<?= base_url('assets/main/images/resources/why-choose-one-img-1.jpg') ?>" alt="Kompetensi PBJ"></div><div class="why-choose-one__img-2"><img src="<?= base_url('assets/main/images/resources/why-choose-one-img-2.jpg') ?>" alt="Kolaborasi"></div></div></div></div></div>
</section>
<!-- Why Choose One End -->

<!-- Live Class One Start -->
<section class="live-class-one">
    <div class="live-class-one__shape-1 float-bob-y"><img src="<?= base_url('assets/main/images/shapes/live-class-shape-1.png') ?>" alt=""></div>
    <div class="container"><div class="row align-items-center"><div class="col-xl-6"><div class="live-class-one__left"><div class="live-class-one__img"><img src="<?= base_url('assets/main/images/resources/live-class-1-1.jpg') ?>" alt="Kelas BegawiPBJ"><div class="live-class-one__video-link"><a href="https://www.youtube.com/watch?v=Get7rqXYrbQ" class="video-popup"><span class="icon-play"></span></a></div></div></div></div><div class="col-xl-6"><div class="live-class-one__right"><div class="section-title text-left"><div class="section-title__tagline-box"><span class="section-title__tagline">Kelas Terjadwal</span></div><h2 class="section-title__title">Belajar Bersama Pengajar dan Peserta Lainnya</h2></div><p class="live-class-one__text">Ikuti kelas daring, tatap muka, atau hybrid untuk mendapatkan pengalaman belajar PBJ yang terarah dan kolaboratif.</p><ul class="live-class-one__points list-unstyled"><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Jadwal, materi, dan agenda pembelajaran yang jelas</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Interaksi dengan praktisi dan fasilitator PBJ</p></div></li><li><div class="icon"><span class="icon-check"></span></div><div class="text"><p>Progres dan capaian belajar yang terdokumentasi</p></div></li></ul><a href="<?= site_url('arena-belajar/kelas') ?>" class="thm-btn"><span class="icon-angles-right"></span>Lihat Kelas</a></div></div></div></div>
</section>
<!-- Live Class One End -->

<!-- Counter One Start -->
<section class="counter-one">
    <div class="counter-one__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/counter-one-bg.jpg') ?>);"></div>
    <div class="container"><div class="row">
        <?php foreach ([['icon' => 'icon-user', 'value' => '1248', 'label' => 'Peserta Terdaftar'], ['icon' => 'icon-book', 'value' => '24', 'label' => 'Kelas Aktif'], ['icon' => 'icon-calendar', 'value' => '36', 'label' => 'Event Tahunan'], ['icon' => 'icon-certificate', 'value' => '824', 'label' => 'Sertifikat Terbit']] as $counter): ?>
            <div class="col-xl-3 col-lg-6 col-md-6"><div class="counter-one__single"><div class="counter-one__icon"><span class="<?= esc($counter['icon']) ?>"></span></div><div class="counter-one__content"><div class="counter-one__count-box"><h3 class="odometer" data-count="<?= esc($counter['value']) ?>">00</h3><span>+</span></div><p class="counter-one__text"><?= esc($counter['label']) ?></p></div></div></div>
        <?php endforeach; ?>
    </div></div>
</section>
<!-- Counter One End -->

<!-- Team One Start -->
<section class="team-one">
    <div class="container"><div class="row align-items-end"><div class="col-xl-7"><div class="section-title text-left"><div class="section-title__tagline-box"><span class="section-title__tagline">Pengajar PBJ</span></div><h2 class="section-title__title">Belajar Bersama Praktisi dan Fasilitator</h2></div></div><div class="col-xl-5"><div class="text-xl-end mb-5"><a href="<?= site_url('arena-belajar/pengajar') ?>" class="thm-btn"><span class="icon-angles-right"></span>Semua Pengajar</a></div></div></div><div class="row">
        <?php foreach ([['image' => 'team-1-1.jpg', 'name' => 'Hendra Wijaya', 'role' => 'Praktisi Pengadaan Barang/Jasa', 'slug' => 'hendra-wijaya'], ['image' => 'team-1-2.jpg', 'name' => 'Rina Oktaviani', 'role' => 'Fasilitator Pembelajaran PBJ', 'slug' => 'rina-oktaviani'], ['image' => 'team-1-3.jpg', 'name' => 'Maya Sari', 'role' => 'Analis Kompetensi PBJ', 'slug' => 'maya-sari']] as $trainer): ?>
            <div class="col-xl-4 col-lg-4"><div class="team-one__single"><div class="team-one__img"><img src="<?= base_url('assets/main/images/team/' . $trainer['image']) ?>" alt="<?= esc($trainer['name']) ?>"><div class="team-one__social"><a href="<?= site_url('arena-belajar/pengajar/' . $trainer['slug']) ?>"><span class="icon-plus"></span></a></div></div><div class="team-one__content"><h3 class="team-one__title"><a href="<?= site_url('arena-belajar/pengajar/' . $trainer['slug']) ?>"><?= esc($trainer['name']) ?></a></h3><p class="team-one__sub-title"><?= esc($trainer['role']) ?></p></div></div></div>
        <?php endforeach; ?>
    </div></div>
</section>
<!-- Team One End -->

<!-- Newsletter One Start -->
<section class="newsletter-one">
    <div class="newsletter-one__shape-1 float-bob-y"><img src="<?= base_url('assets/main/images/shapes/newsletter-one-shape-1.png') ?>" alt=""></div>
    <div class="newsletter-one__shape-2 float-bob-x"><img src="<?= base_url('assets/main/images/shapes/newsletter-one-shape-2.png') ?>" alt=""></div>
    <div class="newsletter-one__bg-shape" style="background-image: url(<?= base_url('assets/main/images/shapes/newsletter-one-bg-shape.png') ?>);"></div>
    <div class="container"><div class="newsletter-one__inner"><div class="newsletter-one__left"><h2 class="newsletter-one__title">Dapatkan Informasi Terbaru<br>Seputar BegawiPBJ</h2></div><div class="newsletter-one__right"><form class="newsletter-one__form" action="#"><div class="newsletter-one__input-box"><input type="email" placeholder="Masukkan alamat email" aria-label="Alamat email"><button type="button" class="newsletter-one__btn thm-btn"><span class="icon-angles-right"></span>Berlangganan</button></div></form><p class="newsletter-one__text">Form newsletter masih UI-only.</p></div></div></div>
</section>
<!-- Newsletter One End -->
<?= $this->endSection() ?>

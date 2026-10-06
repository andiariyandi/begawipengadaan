<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => 'Arena Belajar']) ?>

<section class="category-one">
    <div class="category-one__bg-shape"
        style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);"></div>
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box">
                <div class="section-title__tagline-shape">
                    <img src="<?= base_url('assets/main/images/shapes/section-title-shape-1.png') ?>" alt="">
                </div>
                <p class="section-title__tagline">Pembelajaran PBJ Lampung</p>
            </div>
            <h2 class="section-title__title">Tingkatkan Kompetensi <br> Bersama Arena Belajar</h2>
        </div>

        <div class="row">
            <?php
            $programs = [
                [
                    'icon' => 'why-choose-one-icon-1.png',
                    'title' => 'Program Pembelajaran',
                    'description' => 'Ikuti program terstruktur untuk memperkuat kompetensi dan kesiapan menjalankan proses PBJ.',
                    'url' => 'arena-belajar/program',
                ],
                [
                    'icon' => 'why-choose-one-icon-2.png',
                    'title' => 'Course PBJ',
                    'description' => 'Pelajari materi pengadaan, regulasi, perencanaan, kontrak, dan pengadaan berkelanjutan.',
                    'url' => 'arena-belajar/course',
                ],
                [
                    'icon' => 'why-choose-one-icon-3.png',
                    'title' => 'Kelas dan Diskusi',
                    'description' => 'Temukan kelas aktif, agenda pembelajaran, dan ruang diskusi bersama peserta lainnya.',
                    'url' => 'arena-belajar/kelas',
                ],
                [
                    'icon' => 'why-choose-one-icon-4.png',
                    'title' => 'Pengajar PBJ',
                    'description' => 'Kenali pengajar dan praktisi yang berbagi pengalaman serta praktik baik PBJ.',
                    'url' => 'arena-belajar/pengajar',
                ],
            ];
            foreach ($programs as $program): ?>
                <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                    <div class="category-one__single">
                        <div class="category-one__icon">
                            <img src="<?= base_url('assets/main/images/icon/' . $program['icon']) ?>" alt="">
                        </div>
                        <h3 class="category-one__title">
                            <a href="<?= site_url($program['url']) ?>"><?= esc($program['title']) ?></a>
                        </h3>
                        <p class="category-one__text"><?= esc($program['description']) ?></p>
                        <a class="category-one__btn" href="<?= site_url($program['url']) ?>">
                            <span class="icon-angles-right"></span> Jelajahi
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="why-choose-one">
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="why-choose-one__left">
                    <div class="section-title text-left">
                        <div class="section-title__tagline-box">
                            <div class="section-title__tagline-shape">
                                <img src="<?= base_url('assets/main/images/shapes/section-title-shape-1.png') ?>" alt="">
                            </div>
                            <p class="section-title__tagline">Belajar dengan tujuan</p>
                        </div>
                        <h2 class="section-title__title">Dari Regulasi ke Praktik PBJ</h2>
                    </div>
                    <p class="why-choose-one__text">Arena Belajar membantu peserta memahami proses Pengadaan Barang/Jasa secara bertahap, kontekstual, dan dapat diterapkan dalam pekerjaan sehari-hari.</p>
                    <a href="<?= site_url('arena-belajar/course') ?>" class="why-choose-one__btn thm-btn">
                        <span class="icon-angles-right"></span> Lihat Semua Materi
                    </a>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="why-choose-one__right">
                    <ul class="why-choose-one__list list-unstyled">
                        <li><span class="icon-check"></span><div><h4>Materi terarah</h4><p>Materi disusun mengikuti kebutuhan kompetensi PBJ.</p></div></li>
                        <li><span class="icon-check"></span><div><h4>Praktik kontekstual</h4><p>Contoh dan studi kasus dekat dengan proses kerja pemerintah daerah.</p></div></li>
                        <li><span class="icon-check"></span><div><h4>Jejaring pembelajar</h4><p>Bangun kolaborasi dengan pengajar dan peserta dari berbagai unit kerja.</p></div></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

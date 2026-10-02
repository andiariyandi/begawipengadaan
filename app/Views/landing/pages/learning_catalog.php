<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>

<?php
$catalogs = [
    'programs' => [
        ['title' => 'Pengadaan Barang/Jasa Pemerintah', 'text' => 'Peta belajar menyeluruh dari perencanaan sampai evaluasi pengadaan.', 'icon' => 'why-choose-one-icon-1.png', 'slug' => 'pengadaan-barang-jasa-pemerintah'],
        ['title' => 'Kompetensi Pejabat Pengadaan', 'text' => 'Latihan terarah untuk memperkuat peran dan tanggung jawab pejabat pengadaan.', 'icon' => 'why-choose-one-icon-2.png', 'slug' => 'kompetensi-pejabat-pengadaan'],
        ['title' => 'Pengadaan Berkelanjutan', 'text' => 'Pahami prinsip ramah lingkungan dan manfaat sosial dalam proses PBJ.', 'icon' => 'why-choose-one-icon-3.png', 'slug' => 'pengadaan-berkelanjutan'],
        ['title' => 'Klinik PBJ Lampung', 'text' => 'Pendampingan berbasis kasus untuk menjawab kebutuhan unit kerja.', 'icon' => 'why-choose-one-icon-4.png', 'slug' => 'klinik-pbj-lampung'],
    ],
    'courses' => [
        ['title' => 'Perencanaan Pengadaan', 'text' => 'Menyusun kebutuhan, spesifikasi, dan strategi pengadaan yang akuntabel.', 'icon' => 'why-choose-one-icon-1.png', 'slug' => 'perencanaan-pengadaan'],
        ['title' => 'Pemilihan Penyedia', 'text' => 'Mengenali metode pemilihan dan tahapan evaluasi penawaran.', 'icon' => 'why-choose-one-icon-2.png', 'slug' => 'pemilihan-penyedia'],
        ['title' => 'Pengelolaan Kontrak', 'text' => 'Mengawal pelaksanaan, perubahan, pembayaran, dan serah terima kontrak.', 'icon' => 'why-choose-one-icon-3.png', 'slug' => 'pengelolaan-kontrak'],
        ['title' => 'Manajemen Risiko PBJ', 'text' => 'Mengidentifikasi dan mengendalikan risiko pada setiap tahapan pengadaan.', 'icon' => 'why-choose-one-icon-4.png', 'slug' => 'manajemen-risiko-pbj'],
    ],
    'classes' => [
        ['title' => 'Kelas Dasar PBJ', 'text' => 'Kelas pengantar untuk memahami alur dan istilah penting dalam PBJ.', 'icon' => 'why-choose-one-icon-1.png', 'slug' => 'kelas-dasar-pbj'],
        ['title' => 'Kelas Studi Kasus', 'text' => 'Diskusi kasus nyata untuk melatih analisis dan pengambilan keputusan.', 'icon' => 'why-choose-one-icon-2.png', 'slug' => 'kelas-studi-kasus'],
        ['title' => 'Forum Tanya Jawab', 'text' => 'Sesi interaktif bersama pengajar dan peserta dari unit kerja Lampung.', 'icon' => 'why-choose-one-icon-3.png', 'slug' => 'forum-tanya-jawab'],
        ['title' => 'Kelas Persiapan Sertifikasi', 'text' => 'Belajar mandiri dan berkelompok untuk mempersiapkan uji kompetensi.', 'icon' => 'why-choose-one-icon-4.png', 'slug' => 'kelas-persiapan-sertifikasi'],
    ],
    'trainers' => [
        ['title' => 'Rina Puspitasari', 'text' => 'Fasilitator perencanaan dan strategi Pengadaan Barang/Jasa.', 'icon' => 'why-choose-one-icon-1.png', 'slug' => 'rina-puspitasari'],
        ['title' => 'Budi Santoso', 'text' => 'Praktisi pengelolaan kontrak dan pengendalian pelaksanaan pekerjaan.', 'icon' => 'why-choose-one-icon-2.png', 'slug' => 'budi-santoso'],
        ['title' => 'Dewi Lestari', 'text' => 'Pengajar regulasi, kepatuhan, dan manajemen risiko PBJ.', 'icon' => 'why-choose-one-icon-3.png', 'slug' => 'dewi-lestari'],
        ['title' => 'Arif Rahman', 'text' => 'Mentor pengadaan berkelanjutan dan inovasi layanan publik.', 'icon' => 'why-choose-one-icon-4.png', 'slug' => 'arif-rahman'],
    ],
];
$items = $items ?? ($catalogs[$catalogType] ?? []);
$detailBase = [
    'programs' => 'arena-belajar/program',
    'courses' => 'arena-belajar/course',
    'classes' => 'arena-belajar/kelas',
    'trainers' => 'arena-belajar/pengajar',
][$catalogType];
?>

<section class="category-one learning-catalog">
    <div class="category-one__bg-shape" style="background-image: url(<?= base_url('assets/main/images/shapes/category-one-bg-shape.png') ?>);"></div>
    <div class="container">
        <div class="section-title text-center">
            <div class="section-title__tagline-box">
                <div class="section-title__tagline-shape"></div>
                <p class="section-title__tagline">Arena Belajar BegawiPBJ</p>
            </div>
            <h2 class="section-title__title"><?= esc($pageTitle) ?></h2>
            <p class="learning-catalog__intro"><?= esc($description) ?></p>
        </div>
        <?php if ($catalogType === 'courses'): ?>
            <div class="row course-list__right">
                <?php foreach ($items as $index => $item): ?>
                    <div class="col-xl-6 col-lg-6 col-md-6">
                        <article class="course-list__single">
                            <div class="course-list__img-box">
                                <div class="course-list__img">
                                    <img src="<?= base_url('assets/main/images/resources/course-list-img-' . (($index % 5) + 1) . '.jpg') ?>" alt="<?= esc($item['title']) ?>">
                                </div>
                            </div>
                            <div class="course-list__content">
                                <div class="course-list__doller-and-heart">
                                    <div class="course-list__doller"><p>PBJ Lampung</p></div>
                                    <div class="course-list__heart"><a href="<?= site_url($detailBase . '/' . $item['slug']) ?>" aria-label="Buka <?= esc($item['title']) ?>"><span class="icon-book"></span></a></div>
                                </div>
                                <h3 class="course-list__title"><a href="<?= site_url($detailBase . '/' . $item['slug']) ?>"><?= esc($item['title']) ?></a></h3>
                                <div class="course-list__ratting-box">
                                    <div class="course-list__ratting"><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span><span class="icon-star"></span></div>
                                    <p class="course-list__ratting-text"><?= esc($item['program_title'] ?? 'Materi terstruktur') ?></p>
                                </div>
                                <ul class="course-list__meta list-unstyled">
                                    <li><p><span class="icon-book"></span> <?= esc($item['level'] ?? 'PBJ') ?></p></li>
                                    <li><p><span class="icon-clock"></span> <?= $item['duration_minutes'] !== null ? esc((string) $item['duration_minutes']) . ' menit' : 'Mandiri' ?></p></li>
                                    <li><p><span class="icon-user"></span> <?= esc($item['visibility'] ?? 'Public') ?></p></li>
                                </ul>
                                <div class="course-list__btn-and-client-info">
                                    <div class="course-list__btn-box"><a class="thm-btn-two" href="<?= site_url($detailBase . '/' . $item['slug']) ?>"><span>Lihat Course</span><i class="icon-angles-right"></i></a></div>
                                    <div class="course-list__client-box"><div class="course-list__client-img"><img src="<?= base_url('assets/main/images/resources/course-list-client-img-' . (($index % 5) + 1) . '.jpg') ?>" alt=""></div><div class="course-list__client-content"><h4>Tim BegawiPBJ</h4><p>Pengajar PBJ</p></div></div>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($catalogType === 'classes'): ?>
            <div class="row">
                <?php foreach ($items as $item): ?>
                    <div class="col-xl-3 col-lg-6 col-md-6"><article class="category-one__single"><div class="category-one__icon"><img src="<?= base_url('assets/main/images/icon/' . $item['icon']) ?>" alt=""></div><h3 class="category-one__title"><a href="<?= site_url($detailBase . '/' . $item['slug']) ?>"><?= esc($item['title']) ?></a></h3><p class="category-one__text"><?= esc($item['course_title'] ?? '-') ?></p><p class="category-one__text">Periode: <?= esc($item['start_at'] ?? '-') ?> - <?= esc($item['end_at'] ?? '-') ?></p><p class="category-one__text">Kapasitas: <?= esc($item['capacity'] ?? '-') ?></p><?= view('landing/components/status_badge', ['status' => $item['status'] ?? '-']) ?><a class="category-one__btn" href="<?= site_url($detailBase . '/' . $item['slug']) ?>"><span class="icon-angles-right"></span> Lihat Detail</a></article></div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($catalogType === 'trainers'): ?>
            <div class="row">
                <?php foreach ($items as $item): ?>
                    <div class="col-xl-3 col-lg-6 col-md-6"><article class="category-one__single"><div class="category-one__icon"><img src="<?= base_url('assets/main/images/icon/' . $item['icon']) ?>" alt=""></div><h3 class="category-one__title"><a href="<?= site_url($detailBase . '/' . $item['slug']) ?>"><?= esc($item['title']) ?></a></h3><p class="category-one__text"><?= esc($item['text'] ?? 'Pengajar PBJ') ?></p><p class="category-one__text"><?= count($item['classes'] ?? []) ?> kelas aktif</p><a class="category-one__btn" href="<?= site_url($detailBase . '/' . $item['slug']) ?>"><span class="icon-angles-right"></span> Lihat Profil</a></article></div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($items as $item): ?>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <article class="category-one__single">
                            <div class="category-one__icon">
                                <img src="<?= base_url('assets/main/images/icon/' . $item['icon']) ?>" alt="">
                            </div>
                            <h3 class="category-one__title"><a href="<?= site_url($detailBase . '/' . $item['slug']) ?>"><?= esc($item['title']) ?></a></h3>
                            <p class="category-one__text"><?= esc($item['text']) ?></p>
                            <a class="category-one__btn" href="<?= site_url($detailBase . '/' . $item['slug']) ?>"><span class="icon-angles-right"></span> Lihat Detail</a>
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

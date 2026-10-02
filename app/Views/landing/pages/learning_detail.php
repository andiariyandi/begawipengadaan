<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>

<?php $item = $item ?? []; ?>
<?php if ($catalogType === 'courses'): ?>
<section class="course-details learning-detail">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="course-details__left">
                    <div class="course-details__img"><img src="<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>" alt="<?= esc($itemTitle) ?>"></div>
                    <div class="course-details__content">
                        <div class="course-details__tag-box"><div class="course-details__tag-shape"></div><span class="course-details__tag">Course PBJ</span></div>
                        <h3 class="course-details__title"><?= esc($item['title'] ?? $itemTitle) ?></h3>
                        <div class="course-details__client-and-ratting-box">
                            <div class="course-details__client-box"><div class="course-details__client-img"><img src="<?= base_url('assets/main/images/resources/course-details-client-img-1.jpg') ?>" alt=""></div><div class="course-details__client-content"><p>Pengajar BegawiPBJ</p><h4>Tim Fasilitator PBJ Lampung</h4></div></div>
                            <div class="course-details__ratting-box-1"><ul class="course-details__ratting-list-1 list-unstyled"><li><p>Modul</p><h4>6 Materi</h4></li><li><p>Format</p><h4>Mandiri</h4></li><li><p>Rating</p><ul class="course-details__ratting list-unstyled"><li><span class="icon-star"></span></li><li><span class="icon-star"></span></li><li><span class="icon-star"></span></li><li><span class="icon-star"></span></li><li><span class="icon-star"></span></li></ul></li></ul></div>
                        </div>
                        <div class="course-details__overview">
                            <h3 class="course-details__overview-title">Ringkasan Course</h3>
                            <p class="course-details__overview-text-1"><?= esc($item['summary'] ?? '') ?></p>
                            <p class="course-details__overview-text-2"><?= nl2br(esc($item['description'] ?? '')) ?></p>
                            <h3 class="course-details__overview-title-2">Yang akan dipelajari</h3>
                            <ul class="course-details__overview-points list-unstyled"><li><div class="course-details__overview-points-icon"><span class="fas fa-check"></span></div><div class="course-details__overview-points-content"><h5>Konsep dan regulasi</h5><p>Memahami dasar kebijakan dan prinsip pengadaan.</p></div></li><li><div class="course-details__overview-points-icon"><span class="fas fa-check"></span></div><div class="course-details__overview-points-content"><h5>Praktik dan studi kasus</h5><p>Menerapkan materi melalui contoh situasi PBJ.</p></div></li></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5"><div class="learning-detail__card"><h3>Mulai Belajar</h3><p>Akses course dan ikuti materi secara bertahap.</p><a href="<?= site_url('dashboard/daftar') ?>" class="thm-btn"><span class="icon-angles-right"></span> Daftar Peserta</a></div></div>
        </div>
    </div>
</section>
<?php elseif ($catalogType === 'classes'): ?>
<section class="about-three learning-detail"><div class="container"><div class="row"><div class="col-xl-7"><div class="about-three__right"><div class="section-title-two text-left"><h2 class="section-title-two__title"><?= esc($item['name'] ?? $itemTitle) ?></h2></div><p class="learning-detail__lead">Course: <?= esc($item['course_title'] ?? '-') ?></p><p>Periode: <?= esc($item['start_at'] ?? '-') ?> - <?= esc($item['end_at'] ?? '-') ?></p><p>Kapasitas: <?= esc($item['capacity'] ?? '-') ?></p></div></div><div class="col-xl-5"><div class="learning-detail__card"><h3>Status Kelas</h3><?= view('landing/components/status_badge', ['status' => $item['status'] ?? '-']) ?><p>Kelas tersedia untuk peserta sesuai jadwal dan kapasitas.</p></div></div></div></div></section>
<?php elseif ($catalogType === 'trainers'): ?>
<section class="about-three learning-detail"><div class="container"><div class="row"><div class="col-xl-7"><div class="about-three__right"><div class="section-title-two text-left"><h2 class="section-title-two__title"><?= esc($item['title'] ?? $itemTitle) ?></h2></div><p class="learning-detail__lead">Profil: <?= esc($item['profile_type'] ?? 'Pengajar PBJ') ?></p><h4>Kelas terkait</h4><ul><?php foreach (($item['classes'] ?? []) as $class): ?><li><?= esc($class['name']) ?> (<?= esc($class['code']) ?>)</li><?php endforeach; ?></ul></div></div></div></div></section>
<?php else: ?>
<section class="about-three learning-detail">
    <div class="container">
        <div class="row">
            <div class="col-xl-7">
                <div class="about-three__right">
                    <div class="section-title-two text-left">
                        <div class="section-title-two__tagline-box">
                            <div class="section-title-two__tagline-shape">
                                <img src="<?= base_url('assets/main/images/shapes/section-title-two-shape-1.png') ?>" alt="">
                            </div>
                            <span class="section-title-two__tagline">Arena Belajar BegawiPBJ</span>
                        </div>
                        <h2 class="section-title-two__title"><?= esc($itemTitle) ?></h2>
                    </div>
                    <p class="learning-detail__lead"><?= esc($item['summary'] ?? '') ?></p>
                    <ul class="list-unstyled learning-detail__list">
                        <li><span class="icon-check"></span><div><h4>Tujuan pembelajaran</h4><p>Memahami konsep, tahapan, dan praktik yang relevan dengan peran Anda.</p></div></li>
                        <li><span class="icon-check"></span><div><h4>Materi utama</h4><p>Regulasi, studi kasus, contoh dokumen, dan latihan refleksi.</p></div></li>
                        <li><span class="icon-check"></span><div><h4>Hasil yang diharapkan</h4><p>Mampu menerapkan pengetahuan secara tertib, transparan, dan akuntabel.</p></div></li>
                    </ul>
                    <a href="<?= site_url('dashboard/daftar') ?>" class="thm-btn"><span class="icon-angles-right"></span> Mulai Belajar</a>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="learning-detail__card">
                    <h3>Ringkasan <?= esc($catalogType === 'trainers' ? 'profil' : 'materi') ?></h3>
                    <p>Program</p><strong><?= esc($item['program_title'] ?? '-') ?></strong>
                    <p>Level</p><strong><?= esc($item['level'] ?? '-') ?></strong>
                    <p>Status</p><strong><?= esc($item['status'] ?? 'published') ?></strong>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>

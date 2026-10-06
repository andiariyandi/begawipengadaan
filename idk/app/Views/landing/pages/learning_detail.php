<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>

<?php
$item = $item ?? [];
$breadcrumb = $breadcrumb ?? [];

$enrollStatus = $item['enrollment_status'] ?? null;
$isLoggedIn   = session()->get('isLoggedIn') === true;
$success      = session()->getFlashdata('success');
$err          = session()->getFlashdata('error');
?>

<?php if ($breadcrumb !== []): ?>
<nav aria-label="breadcrumb" class="container pt-3">
    <ol class="breadcrumb">
        <?php foreach ($breadcrumb as $i => $crumb): ?>
            <?php if ($i > 0): ?><li class="breadcrumb-separator">/</li><?php endif; ?>
            <li class="breadcrumb-item <?= empty($crumb['url']) ? 'active' : '' ?>">
                <?php if (! empty($crumb['url'])): ?>
                    <a href="<?= esc($crumb['url']) ?>"><?= esc($crumb['label']) ?></a>
                <?php else: ?>
                    <?= esc($crumb['label']) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>

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
                            <div class="course-details__client-box"><div class="course-details__client-img"><img src="<?= base_url('assets/main/images/resources/course-list-client-img-1.jpg') ?>" alt=""></div><div class="course-details__client-content"><p>Pengajar BegawiPBJ</p><h4>Tim Fasilitator PBJ Lampung</h4></div></div>
                            <div class="course-details__ratting-box-1"><ul class="course-details__ratting-list-1 list-unstyled"><li><p>Level</p><h4><?= esc($item['level'] ?? '-') ?></h4></li><li><p>Durasi</p><h4><?= ($item['duration_minutes'] ?? null) !== null ? esc((string) $item['duration_minutes']) . ' menit' : 'Mandiri' ?></h4></li></ul></div>
                        </div>
                        <div class="course-details__overview">
                            <h3 class="course-details__overview-title">Ringkasan Course</h3>
                            <p class="course-details__overview-text-1"><?= esc($item['summary'] ?? '') ?></p>
                            <p class="course-details__overview-text-2"><?= nl2br(esc($item['description'] ?? '')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="learning-detail__card">
                    <h3>Kelas Tersedia</h3>
                    <?php $classes = $classes ?? []; ?>
                    <?php if ($classes === []): ?>
                        <p class="text-muted small">Belum ada kelas aktif untuk course ini.</p>
                    <?php else: ?>
                        <?php foreach ($classes as $class): ?>
                            <article class="learning-detail__class-card">
                                <h4 class="learning-detail__class-title"><a href="<?= route_to('public.learning.class.detail', $class['code']) ?>"><?= esc($class['name'] ?? '-') ?></a></h4>
                                <p class="small mb-1"><strong>Kode:</strong> <?= esc($class['code'] ?? '-') ?></p>
                                <p class="small mb-1"><strong>Periode:</strong> <?= esc(($class['start_at'] ?? '-') . ' &ndash; ' . ($class['end_at'] ?? '-')) ?></p>
                                <?php if (! empty($class['capacity'])): ?>
                                    <p class="small mb-2"><strong>Kapasitas:</strong> <?= esc((string) $class['capacity']) ?></p>
                                <?php endif; ?>
                                <?= view('landing/components/status_badge', ['status' => $class['status'] ?? '-']) ?>
                                <a class="thm-btn-two thm-btn-two--sm mt-2" href="<?= route_to('public.learning.class.detail', $class['code']) ?>"><span>Lihat Detail</span><i class="icon-angles-right"></i></a>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php elseif ($catalogType === 'programs'): ?>
<section class="course-details learning-detail">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="course-details__left">
                    <div class="course-details__img"><img src="<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>" alt="<?= esc($itemTitle) ?>"></div>
                    <div class="course-details__content">
                        <div class="course-details__tag-box"><div class="course-details__tag-shape"></div><span class="course-details__tag">Program PBJ</span></div>
                        <h3 class="course-details__title"><?= esc($item['title'] ?? $itemTitle) ?></h3>
                        <div class="course-details__client-and-ratting-box">
                            <div class="course-details__client-box"><div class="course-details__client-img"><img src="<?= base_url('assets/main/images/resources/course-list-client-img-1.jpg') ?>" alt=""></div><div class="course-details__client-content"><p>Program PBJ</p><h4><?= esc($item['title'] ?? '-') ?></h4></div></div>
                            <div class="course-details__ratting-box-1"><ul class="course-details__ratting-list-1 list-unstyled"><li><p>Status</p><h4><?= esc(ucfirst((string)($item['status'] ?? 'published'))) ?></h4></li><li><p>Visibilitas</p><h4><?= esc(ucfirst((string)($item['visibility'] ?? 'public'))) ?></h4></li></ul></div>
                        </div>
                        <div class="course-details__overview">
                            <h3 class="course-details__overview-title">Tentang Program</h3>
                            <p class="course-details__overview-text-1"><?= esc($item['summary'] ?? '') ?></p>
                            <p class="course-details__overview-text-2"><?= nl2br(esc($item['description'] ?? '')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="learning-detail__card">
                    <h3>Course dalam Program</h3>
                    <?php $courses = $courses ?? []; ?>
                    <?php if ($courses === []): ?>
                        <p class="text-muted small">Belum ada course untuk program ini.</p>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <article class="learning-detail__class-card">
                                <h4 class="learning-detail__class-title"><a href="<?= route_to('public.learning.course.detail', $course['slug']) ?>"><?= esc($course['title'] ?? '-') ?></a></h4>
                                <?php if (! empty($course['summary'])): ?>
                                    <p class="small mb-1"><?= esc($course['summary']) ?></p>
                                <?php endif; ?>
                                <p class="small mb-2"><strong>Level:</strong> <?= esc($course['level'] ?? '-') ?></p>
                                <a class="thm-btn-two thm-btn-two--sm mt-2" href="<?= route_to('public.learning.course.detail', $course['slug']) ?>"><span>Lihat Course</span><i class="icon-angles-right"></i></a>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php elseif ($catalogType === 'classes'):
    $trainers = $item['trainers'] ?? [];
?>
<section class="course-details learning-detail">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="course-details__left">
                    <div class="course-details__img"><img src="<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>" alt="<?= esc($itemTitle) ?>"></div>
                    <div class="course-details__content">
                        <div class="course-details__tag-box"><div class="course-details__tag-shape"></div><span class="course-details__tag">Kelas PBJ</span></div>
                        <h3 class="course-details__title"><?= esc($item['name'] ?? $itemTitle) ?></h3>
                        <div class="course-details__client-and-ratting-box">
                            <div class="course-details__client-box"><div class="course-details__client-img"><img src="<?= base_url('assets/main/images/resources/course-list-client-img-1.jpg') ?>" alt=""></div><div class="course-details__client-content"><p>Course</p><h4><?= esc($item['course_title'] ?? '-') ?></h4></div></div>
                            <div class="course-details__ratting-box-1"><ul class="course-details__ratting-list-1 list-unstyled"><li><p>Kode</p><h4><?= esc($item['code'] ?? '-') ?></h4></li><li><p>Periode</p><h4><?= esc(($item['start_at'] ?? '-') . ' &ndash; ' . ($item['end_at'] ?? '-')) ?></h4></li><li><p>Kapasitas</p><h4><?= esc((string) ($item['capacity'] ?? '-')) ?></h4></li></ul></div>
                        </div>
                        <div class="course-details__overview">
                            <h3 class="course-details__overview-title">Pengajar</h3>
                            <?php if ($trainers === []): ?>
                                <p class="text-muted">Belum ada pengajar terdaftar.</p>
                            <?php else: ?>
                                <ul class="list-unstyled">
                                    <?php foreach ($trainers as $trainer): ?>
                                        <li><strong><?= esc($trainer['name'] ?: $trainer['username']) ?></strong></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="learning-detail__card">
                    <h3>Enrollment Kelas</h3>
                    <?php if ($success): ?><p class="text-success small"><?= esc($success) ?></p><?php endif; ?>
                    <?php if ($err): ?><p class="text-danger small"><?= esc($err) ?></p><?php endif; ?>
                    <?php
                            $classStatus = (string) ($item['status'] ?? '');
                            $enrollable  = in_array($classStatus, ['scheduled', 'active'], true);
                        ?>
                    <?php if (! $enrollable): ?>
                        <span class="thm-btn thm-btn--disabled" aria-disabled="true"><span class="icon-ban"></span> Tidak dapat mengikuti kelas</span>
                    <?php elseif (! $isLoggedIn): ?>
                        <a href="<?= route_to('dashboard.auth.login') ?>" class="thm-btn"><span class="icon-angles-right"></span> Daftar Kelas</a>
                    <?php elseif ($enrollStatus === null): ?>
                        <form action="<?= site_url('arena-belajar/kelas/' . ($item['code'] ?? '') . '/daftar') ?>" method="post"><?= csrf_field() ?><button type="submit" class="thm-btn"><span class="icon-angles-right"></span> Daftar Kelas</button></form>
                    <?php elseif ($enrollStatus === 'pending'): ?>
                        <span class="thm-btn thm-btn--disabled" aria-disabled="true"><span class="icon-clock"></span> Menunggu Approval</span>
                    <?php elseif ($enrollStatus === 'active'): ?>
                        <a href="<?= route_to('workspace.participant.class.detail', $item['id']) ?>" class="thm-btn"><span class="icon-angles-right"></span> Mulai Belajar</a>
                        <?= view('landing/components/bookmark_button', ['type' => 'learning_class', 'id' => (int) ($item['id'] ?? 0), 'title' => $item['name'] ?? null, 'size' => 'sm']) ?>
                    <?php elseif ($enrollStatus === 'completed'): ?>
                        <span class="thm-btn thm-btn--disabled" aria-disabled="true"><span class="icon-check"></span> Selesai</span>
                    <?php else: ?>
                        <span class="thm-btn thm-btn--disabled" aria-disabled="true"><span class="icon-ban"></span> Ditolak</span>
                    <?php endif; ?>
                    <p class="small mt-2 text-muted">Course: <?= esc($item['course_title'] ?? '-') ?><br>Program: <?= esc($item['program_title'] ?? '-') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
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
                    <a href="<?= route_to('dashboard.auth.register') ?>" class="thm-btn"><span class="icon-angles-right"></span> Mulai Belajar</a>
                    <?php
                    $bmType = match ($catalogType) {
                        'programs' => 'program',
                        'courses'  => 'course',
                        'classes'  => 'learning_class',
                        default    => null,
                    };
                    ?>
                    <?php if ($bmType !== null && ! empty($item['id'])): ?>
                        <div class="mt-12"><?= view('landing/components/bookmark_button', ['type' => $bmType, 'id' => (int) $item['id'], 'title' => $itemTitle, 'size' => 'sm']) ?></div>
                    <?php endif; ?>
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
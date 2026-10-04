<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>

<?= view('landing/components/page_header', ['page_title' => $pageTitle]) ?>

<?php
$detailRoute = [
    'programs' => 'public.learning.program.detail',
    'courses'  => 'public.learning.course.detail',
    'classes'  => 'public.learning.class.detail',
    'trainers' => 'public.learning.trainer.detail',
][$catalogType] ?? null;

$pager = $pager ?? service('pager');
$page  = (int) ($_GET['page'] ?? 1);
$perPage = 15;
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

        <?php if ($items === []): ?>
            <div class="row"><div class="col-12"><p class="text-center text-muted">Belum ada data.</p></div></div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($items as $index => $item): ?>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <article class="learning-catalog__card h-100">
                            <?php if ($catalogType === 'courses'): ?>
                                <div class="learning-catalog__thumb">
                                    <img src="<?= base_url('assets/main/images/resources/course-list-img-' . (($index % 5) + 1) . '.jpg') ?>" alt="<?= esc($item['title'] ?? '') ?>">
                                </div>
                                <div class="learning-catalog__body">
                                    <div class="learning-catalog__badges">
                                        <?= view('landing/components/status_badge', ['status' => $item['status'] ?? 'draft']) ?>
                                    </div>
                                    <span class="learning-catalog__eyebrow"><?= esc($item['program_title'] ?? 'Materi terstruktur') ?></span>
                                    <h3 class="learning-catalog__title"><a href="<?= route_to($detailRoute, $item['slug']) ?>"><?= esc($item['title'] ?? '-') ?></a></h3>
                                    <p class="learning-catalog__text"><?= esc($item['summary'] ?? $item['text'] ?? '-') ?></p>
                                    <ul class="learning-catalog__meta list-unstyled">
                                        <li><span class="icon-book"></span> <?= esc($item['level'] ?? 'PBJ') ?></li>
                                        <li><span class="icon-clock"></span> <?= ($item['duration_minutes'] ?? null) !== null ? esc((string) $item['duration_minutes']) . ' menit' : 'Mandiri' ?></li>
                                    </ul>
                                    <a class="thm-btn-two thm-btn-two--sm" href="<?= route_to($detailRoute, $item['slug']) ?>"><span>Lihat Course</span><i class="icon-angles-right"></i></a>
                                </div>
                            <?php elseif ($catalogType === 'classes'): ?>
                                <div class="learning-catalog__thumb">
                                    <img src="<?= base_url('assets/main/images/resources/course-list-img-' . (($index % 5) + 1) . '.jpg') ?>" alt="<?= esc($item['title'] ?? '') ?>">
                                </div>
                                <div class="learning-catalog__body">
                                    <div class="learning-catalog__badges">
                                        <?= view('landing/components/status_badge', ['status' => $item['status'] ?? 'draft']) ?>
                                    </div>
                                    <span class="learning-catalog__eyebrow">Kelas &middot; <?= esc($item['code'] ?? '') ?></span>
                                    <h3 class="learning-catalog__title"><a href="<?= route_to($detailRoute, $item['slug']) ?>"><?= esc($item['title'] ?? '-') ?></a></h3>
                                    <p class="learning-catalog__text">Course: <?= esc($item['course_title'] ?? '-') ?></p>
                                    <ul class="learning-catalog__meta list-unstyled">
                                        <li><span class="icon-clock"></span> <?= esc(($item['start_at'] ?? '-') . ' &ndash; ' . ($item['end_at'] ?? '-')) ?></li>
                                        <li><span class="icon-user"></span> Kapasitas: <?= esc((string) ($item['capacity'] ?? '-')) ?></li>
                                    </ul>
                                    <div class="learning-catalog__actions">
                                        <a class="thm-btn-two thm-btn-two--sm" href="<?= route_to($detailRoute, $item['slug']) ?>"><span>Lihat Detail</span><i class="icon-angles-right"></i></a>
                                    </div>
                                </div>
                            <?php elseif ($catalogType === 'trainers'): ?>
                                <div class="learning-catalog__thumb learning-catalog__thumb--circle">
                                    <img src="<?= base_url('assets/main/images/resources/course-list-client-img-' . (($index % 5) + 1) . '.jpg') ?>" alt="">
                                </div>
                                <div class="learning-catalog__body">
                                    <span class="learning-catalog__eyebrow"><?= esc($item['profile_type'] ?? 'Pengajar PBJ') ?></span>
                                    <h3 class="learning-catalog__title"><a href="<?= route_to($detailRoute, $item['slug']) ?>"><?= esc($item['title'] ?? '-') ?></a></h3>
                                    <p class="learning-catalog__text"><?= count($item['classes'] ?? []) ?> kelas aktif</p>
                                    <a class="thm-btn-two thm-btn-two--sm" href="<?= route_to($detailRoute, $item['slug']) ?>"><span>Lihat Profil</span><i class="icon-angles-right"></i></a>
                                </div>
                            <?php else: ?>
                                <div class="learning-catalog__thumb learning-catalog__thumb--circle">
                                    <img src="<?= base_url('assets/main/images/icon/' . ($item['icon'] ?? 'why-choose-one-icon-1.png')) ?>" alt="">
                                </div>
                                <div class="learning-catalog__body">
                                    <div class="learning-catalog__badges">
                                        <?= view('landing/components/status_badge', ['status' => $item['status'] ?? 'draft']) ?>
                                    </div>
                                    <h3 class="learning-catalog__title"><a href="<?= route_to($detailRoute, $item['slug']) ?>"><?= esc($item['title'] ?? '-') ?></a></h3>
                                    <p class="learning-catalog__text"><?= esc($item['text'] ?? $item['summary'] ?? '-') ?></p>
                                    <a class="thm-btn-two thm-btn-two--sm" href="<?= route_to($detailRoute, $item['slug']) ?>"><span>Lihat Detail</span><i class="icon-angles-right"></i></a>
                                    <?php
                                    $bmType = match ($catalogType) {
                                        'programs' => 'program',
                                        'courses'  => 'course',
                                        'classes'  => 'learning_class',
                                        'trainers' => null,
                                        default    => null,
                                    };
                                    ?>
                                    <?php if ($bmType !== null && ! empty($item['id'])): ?>
                                        <?= view('landing/components/bookmark_button', ['type' => $bmType, 'id' => (int) $item['id'], 'title' => $item['title'] ?? null, 'size' => 'sm']) ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($pager): ?>
                <?php $links = $pager->links($catalogType, 'default_full'); ?>
                <?php if ($links): ?>
                    <div class="row mt-4"><div class="col-12 d-flex justify-content-center"><?= $links ?></div></div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
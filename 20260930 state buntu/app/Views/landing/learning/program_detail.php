<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => $program['title'],
    'breadcrumbs' => [
        ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
        ['label' => 'Program'],
    ],
]) ?>

<section class="course-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <div class="course-details__left">
                    <div class="course-details__img"><img src="<?= base_url('assets/main/' . $program['image']) ?>" alt="<?= esc($program['title']) ?>"></div>
                    <div class="course-details__content">
                        <p class="course-details__category"><?= esc($program['category']) ?> · Level <?= esc($program['level']) ?></p>
                        <h2 class="course-details__title"><?= esc($program['title']) ?></h2>
                        <p><?= esc($program['excerpt']) ?></p>
                        <h3>Ringkasan Program</h3>
                        <p>Program ini menyatukan beberapa course yang dirancang secara berurutan. Peserta dapat mempelajari materi melalui kelas yang tersedia setelah memenuhi ketentuan enrollment.</p>
                        <ul class="list-unstyled course-details__points">
                            <li><i class="fa fa-check"></i> <?= esc($program['courses']) ?></li>
                            <li><i class="fa fa-check"></i> <?= esc($program['duration']) ?></li>
                            <li><i class="fa fa-check"></i> Level <?= esc($program['level']) ?></li>
                        </ul>

                        <h3 class="mt-5">Course dalam Program</h3>
                        <div class="course-details__curriculum">
                            <?php foreach ($courses as $index => $course): ?>
                                <div class="course-details__curriculum-item">
                                    <div class="course-details__curriculum-left"><span><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><a href="<?= site_url('arena-belajar/course/' . $course['slug']) ?>"><?= esc($course['title']) ?></a></div>
                                    <div class="course-details__curriculum-right"><span><i class="fa fa-clock"></i> <?= esc($course['duration']) ?></span></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <h3 class="mt-5">Instruktur</h3>
                        <div class="row">
                            <?php foreach ($instructors as $instructor): ?>
                                <div class="col-md-4">
                                    <div class="team-one__single">
                                        <div class="team-one__img"><img src="<?= base_url('assets/main/' . $instructor['image']) ?>" alt="<?= esc($instructor['name']) ?>"></div>
                                        <div class="team-one__content"><h4><a href="<?= site_url('arena-belajar/instruktur/' . $instructor['slug']) ?>"><?= esc($instructor['name']) ?></a></h4><p><?= esc($instructor['role']) ?></p></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4">
                <aside class="course-details__sidebar">
                    <div class="course-details__sidebar__item">
                        <div class="course-details__video" style="background-image: url('<?= base_url('assets/main/images/backgrounds/course-details-video-link-bg.jpg') ?>');">
                            <a href="#" class="video-popup" aria-label="Preview program"><i class="fa fa-play"></i></a>
                        </div>
                        <h3 class="course-details__sidebar__title">Mulai Program</h3>
                        <p>Ikuti course pada program ini melalui kelas yang tersedia.</p>
                        <a href="<?= site_url('arena-belajar/kelas') ?>" class="thm-btn">Lihat Kelas</a>
                    </div>
                    <div class="course-details__sidebar__item">
                        <h3 class="course-details__sidebar__title">Informasi</h3>
                        <ul class="list-unstyled course-details__sidebar__list">
                            <li><span><i class="fa fa-book"></i> Course</span><strong><?= esc($program['courses']) ?></strong></li>
                            <li><span><i class="fa fa-clock"></i> Durasi</span><strong><?= esc($program['duration']) ?></strong></li>
                            <li><span><i class="fa fa-signal"></i> Level</span><strong><?= esc($program['level']) ?></strong></li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
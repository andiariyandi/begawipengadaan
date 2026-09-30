<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => $course['title'],
    'breadcrumbs' => [
        ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
        ['label' => 'Course', 'url' => site_url('arena-belajar/course')],
        ['label' => 'Detail'],
    ],
]) ?>

<section class="course-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <div class="course-details__left">
                    <div class="course-details__img"><img src="<?= base_url('assets/main/' . $course['detailImage']) ?>" alt="<?= esc($course['title']) ?>"></div>
                    <div class="course-details__content">
                        <p class="course-details__category"><?= esc($course['category']) ?> · <?= esc($course['level']) ?></p>
                        <h2 class="course-details__title"><?= esc($course['title']) ?></h2>
                        <div class="course-details__instructor"><img src="<?= base_url('assets/main/' . $course['instructorImage']) ?>" alt="<?= esc($course['instructor']) ?>"><div><span>Instruktur</span><h4><?= esc($course['instructor']) ?></h4></div></div>
                        <p><?= esc($course['excerpt']) ?></p>
                        <h3>Tentang Course</h3>
                        <p>Course ini menyediakan materi pembelajaran yang dapat diakses melalui kelas yang tersedia. Peserta mengikuti alur materi, assessment, dan aktivitas lain berdasarkan ketentuan kelas.</p>
                        <h3>Hasil Pembelajaran</h3>
                        <ul class="list-unstyled course-details__points"><li><i class="fa fa-check"></i> Memahami konsep utama sesuai topik course.</li><li><i class="fa fa-check"></i> Menerapkan pengetahuan pada konteks pekerjaan PBJ.</li><li><i class="fa fa-check"></i> Menyelesaikan materi dan assessment sesuai aturan kelas.</li></ul>

                        <h3 class="mt-5">Materi Pembelajaran</h3>
                        <div class="course-details__curriculum">
                            <?php foreach ($modules as $module): ?>
                                <div class="course-details__curriculum-item">
                                    <div class="course-details__curriculum-left"><span><?= esc($module['number']) ?></span><a href="#"><?= esc($module['title']) ?></a></div>
                                    <div class="course-details__curriculum-right"><span><i class="fa fa-clock"></i> <?= esc($module['duration']) ?></span><span><i class="fa fa-book"></i> <?= esc($module['lessons']) ?></span></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4">
                <aside class="course-details__sidebar">
                    <div class="course-details__sidebar__item">
                        <div class="course-details__video" style="background-image: url('<?= base_url('assets/main/images/backgrounds/course-details-video-link-bg.jpg') ?>');"><a href="#" class="video-popup" aria-label="Preview course"><i class="fa fa-play"></i></a></div>
                        <h3 class="course-details__sidebar__title">Ikuti Course Ini</h3>
                        <p>Pendaftaran course dilakukan melalui kelas yang tersedia dan memerlukan akun aktif.</p>
                        <a href="<?= site_url('arena-belajar/kelas') ?>" class="thm-btn">Pilih Kelas</a>
                    </div>
                    <div class="course-details__sidebar__item">
                        <h3 class="course-details__sidebar__title">Informasi Course</h3>
                        <ul class="list-unstyled course-details__sidebar__list">
                            <li><span><i class="fa fa-book"></i> Materi</span><strong><?= esc($course['lessons']) ?></strong></li>
                            <li><span><i class="fa fa-clock"></i> Durasi</span><strong><?= esc($course['duration']) ?></strong></li>
                            <li><span><i class="fa fa-users"></i> Peserta</span><strong><?= esc($course['students']) ?></strong></li>
                            <li><span><i class="fa fa-star"></i> Rating</span><strong><?= esc($course['rating']) ?></strong></li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>

<section class="course-one course-one--related">
    <div class="container">
        <div class="section-title text-center"><span class="section-title__tagline">Course Terkait</span><h2 class="section-title__title">Lanjutkan pembelajaran Anda.</h2></div>
        <div class="row">
            <?php foreach ($relatedCourses as $relatedCourse): ?>
                <div class="col-xl-4 col-lg-4 col-md-6"><div class="course-one__single"><div class="course-one__img"><img src="<?= base_url('assets/main/' . $relatedCourse['image']) ?>" alt="<?= esc($relatedCourse['title']) ?>"></div><div class="course-one__content"><p class="course-one__category"><?= esc($relatedCourse['category']) ?></p><h3 class="course-one__title"><a href="<?= site_url('arena-belajar/course/' . $relatedCourse['slug']) ?>"><?= esc($relatedCourse['title']) ?></a></h3></div></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
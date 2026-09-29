<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => 'Course Pembelajaran',
    'breadcrumbs' => [
        ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
        ['label' => 'Course'],
    ],
]) ?>

<section class="course-one course-one--inner">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <div class="course-filter mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <p class="mb-0">Menampilkan <?= count($courses) ?> course pembelajaran.</p>
                    <form action="<?= site_url('arena-belajar/course') ?>" method="get">
                        <select name="sort" class="selectpicker">
                            <option value="latest">Urutkan: Terbaru</option>
                            <option value="popular">Terpopuler</option>
                            <option value="title">Nama Course</option>
                        </select>
                    </form>
                </div>

                <div class="row">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="course-one__single">
                                <div class="course-one__img"><img src="<?= base_url('assets/main/' . $course['image']) ?>" alt="<?= esc($course['title']) ?>"></div>
                                <div class="course-one__content">
                                    <p class="course-one__category"><?= esc($course['category']) ?> · <?= esc($course['level']) ?></p>
                                    <h3 class="course-one__title"><a href="<?= site_url('arena-belajar/course/' . $course['slug']) ?>"><?= esc($course['title']) ?></a></h3>
                                    <p><?= esc($course['excerpt']) ?></p>
                                    <div class="course-one__teacher"><img src="<?= base_url('assets/main/' . $course['instructorImage']) ?>" alt="<?= esc($course['instructor']) ?>"><span><?= esc($course['instructor']) ?></span></div>
                                    <ul class="list-unstyled course-one__meta"><li><i class="fa fa-book"></i> <?= esc($course['lessons']) ?></li><li><i class="fa fa-star"></i> <?= esc($course['rating']) ?></li></ul>
                                    <a href="<?= site_url('arena-belajar/course/' . $course['slug']) ?>" class="course-one__read-more">Lihat Detail <i class="fa fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4">
                <aside class="course-details__sidebar">
                    <div class="course-details__sidebar__item">
                        <h3 class="course-details__sidebar__title">Cari Course</h3>
                        <form action="<?= site_url('arena-belajar/course') ?>" method="get" class="course-search-form"><input type="search" name="q" placeholder="Cari course..."><button type="submit" aria-label="Cari"><i class="fa fa-search"></i></button></form>
                    </div>
                    <div class="course-details__sidebar__item">
                        <h3 class="course-details__sidebar__title">Kategori</h3>
                        <ul class="list-unstyled course-details__sidebar__list"><?php foreach ($categories as $category): ?><li><a href="<?= site_url('arena-belajar/course') ?>"><i class="fa fa-angle-right"></i> <?= esc($category['name']) ?><span><?= esc($category['count']) ?></span></a></li><?php endforeach; ?></ul>
                    </div>
                    <div class="course-details__sidebar__item">
                        <h3 class="course-details__sidebar__title">Instruktur</h3>
                        <ul class="list-unstyled course-details__sidebar__list"><?php foreach (array_slice($instructors, 0, 4) as $instructor): ?><li><a href="<?= site_url('arena-belajar/instruktur/' . $instructor['slug']) ?>"><i class="fa fa-angle-right"></i> <?= esc($instructor['name']) ?></a></li><?php endforeach; ?></ul>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
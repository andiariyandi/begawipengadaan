<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => 'Kelas Pembelajaran',
    'breadcrumbs' => [
        ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
        ['label' => 'Kelas'],
    ],
]) ?>

<section class="course-one course-one--inner">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <div class="section-title text-left"><span class="section-title__tagline">Jadwal Kelas</span><h2 class="section-title__title">Pilih kelas yang tersedia.</h2></div>
                <p>Ketersediaan kelas, kapasitas, enrollment, dan status aktual akan terhubung ke backend pada tahap berikutnya.</p>
                <div class="row">
                    <?php foreach ($classes as $class): ?>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="course-one__single">
                                <div class="course-one__img"><img src="<?= base_url('assets/main/' . $class['image']) ?>" alt="<?= esc($class['title']) ?>"></div>
                                <div class="course-one__content">
                                    <p class="course-one__category"><?= esc($class['mode']) ?> · <?= esc($class['status']) ?></p>
                                    <h3 class="course-one__title"><a href="<?= site_url('arena-belajar/kelas/' . $class['slug']) ?>"><?= esc($class['title']) ?></a></h3>
                                    <p><?= esc($class['course']) ?></p>
                                    <p><i class="fa fa-user"></i> <?= esc($class['trainer']) ?></p>
                                    <ul class="list-unstyled course-one__meta"><li><i class="fa fa-calendar"></i> <?= esc($class['period']) ?></li><li><i class="fa fa-users"></i> <?= esc($class['capacity']) ?></li></ul>
                                    <a href="<?= site_url('arena-belajar/kelas/' . $class['slug']) ?>" class="course-one__read-more">Detail Kelas <i class="fa fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4">
                <aside class="course-details__sidebar">
                    <div class="course-details__sidebar__item"><h3 class="course-details__sidebar__title">Cari Kelas</h3><form action="<?= site_url('arena-belajar/kelas') ?>" method="get" class="course-search-form"><input type="search" name="q" placeholder="Cari kelas..."><button type="submit" aria-label="Cari"><i class="fa fa-search"></i></button></form></div>
                    <div class="course-details__sidebar__item"><h3 class="course-details__sidebar__title">Filter Kategori</h3><ul class="list-unstyled course-details__sidebar__list"><?php foreach ($categories as $category): ?><li><a href="<?= site_url('arena-belajar/kelas') ?>"><i class="fa fa-angle-right"></i> <?= esc($category['name']) ?><span><?= esc($category['count']) ?></span></a></li><?php endforeach; ?></ul></div>
                </aside>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => 'Arena Belajar',
    'breadcrumbs' => [['label' => 'Arena Belajar']],
]) ?>

<section class="course-one course-one--inner">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <div class="section-title text-left">
                    <span class="section-title__tagline">Program Pembelajaran</span>
                    <h2 class="section-title__title">Pilih program sesuai kebutuhan kompetensi PBJ Anda.</h2>
                </div>

                <div class="row">
                    <?php foreach ($programs as $program): ?>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="course-one__single">
                                <div class="course-one__img">
                                    <img src="<?= base_url('assets/main/' . $program['image']) ?>" alt="<?= esc($program['title']) ?>">
                                    <a href="<?= site_url('arena-belajar/program/' . $program['slug']) ?>"><span class="icon-plus"></span></a>
                                </div>
                                <div class="course-one__content">
                                    <p class="course-one__category"><?= esc($program['category']) ?> · <?= esc($program['level']) ?></p>
                                    <h3 class="course-one__title"><a href="<?= site_url('arena-belajar/program/' . $program['slug']) ?>"><?= esc($program['title']) ?></a></h3>
                                    <p><?= esc($program['excerpt']) ?></p>
                                    <ul class="list-unstyled course-one__meta">
                                        <li><i class="fa fa-book"></i> <?= esc($program['courses']) ?></li>
                                        <li><i class="fa fa-clock"></i> <?= esc($program['duration']) ?></li>
                                    </ul>
                                    <a href="<?= site_url('arena-belajar/program/' . $program['slug']) ?>" class="course-one__read-more">Lihat Detail <i class="fa fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4">
                <aside class="course-details__sidebar">
                    <div class="course-details__sidebar__item">
                        <h3 class="course-details__sidebar__title">Cari Pembelajaran</h3>
                        <form action="<?= site_url('arena-belajar/course') ?>" method="get" class="course-search-form">
                            <input type="search" name="q" placeholder="Cari course...">
                            <button type="submit" aria-label="Cari"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                    <div class="course-details__sidebar__item">
                        <h3 class="course-details__sidebar__title">Kategori</h3>
                        <ul class="list-unstyled course-details__sidebar__list">
                            <?php foreach ($categories as $category): ?>
                                <li><a href="<?= site_url('arena-belajar/course') ?>"><i class="fa fa-angle-right"></i> <?= esc($category['name']) ?><span><?= esc($category['count']) ?></span></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="course-details__sidebar__item">
                        <h3 class="course-details__sidebar__title">Butuh Akun?</h3>
                        <p>Daftar untuk mengikuti kelas, menyimpan progres, dan memperoleh sertifikat sesuai ketentuan.</p>
                        <a href="<?= site_url('dashboard/daftar') ?>" class="thm-btn">Daftar Sekarang</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => $class['title'],
    'breadcrumbs' => [
        ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
        ['label' => 'Kelas', 'url' => site_url('arena-belajar/kelas')],
        ['label' => 'Detail'],
    ],
]) ?>

<section class="course-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <div class="course-details__left">
                    <div class="course-details__img"><img src="<?= base_url('assets/main/' . $class['image']) ?>" alt="<?= esc($class['title']) ?>"></div>
                    <div class="course-details__content">
                        <p class="course-details__category"><?= esc($class['mode']) ?> · <?= esc($class['status']) ?></p>
                        <h2 class="course-details__title"><?= esc($class['title']) ?></h2>
                        <p>Kelas ini merupakan pelaksanaan course <strong><?= esc($class['course']) ?></strong>. Informasi di bawah adalah UI preview dan akan menggunakan data aktual setelah backend enrollment serta jadwal diimplementasikan.</p>
                        <h3>Informasi Kelas</h3>
                        <ul class="list-unstyled course-details__points"><li><i class="fa fa-check"></i> Trainer: <?= esc($class['trainer']) ?></li><li><i class="fa fa-check"></i> Periode: <?= esc($class['period']) ?></li><li><i class="fa fa-check"></i> Mode: <?= esc($class['mode']) ?></li><li><i class="fa fa-check"></i> Kapasitas: <?= esc($class['capacity']) ?></li><li><i class="fa fa-check"></i> Status: <?= esc($class['status']) ?></li></ul>
                        <h3 class="mt-5">Jadwal Pembelajaran</h3>
                        <div class="course-details__curriculum">
                            <?php foreach ($schedule as $index => $item): ?>
                                <div class="course-details__curriculum-item"><div class="course-details__curriculum-left"><span><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><a href="#"><?= esc($item['topic']) ?></a></div><div class="course-details__curriculum-right"><span><i class="fa fa-calendar"></i> <?= esc($item['date']) ?></span><span><i class="fa fa-clock"></i> <?= esc($item['time']) ?></span></div></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4"><aside class="course-details__sidebar"><div class="course-details__sidebar__item"><h3 class="course-details__sidebar__title"><?= esc($class['status']) ?></h3><p>Enrollment kelas memerlukan akun aktif dan akan diproses pada tahap backend.</p><a href="<?= site_url('dashboard/masuk') ?>" class="thm-btn">Masuk untuk Enrollment</a></div><div class="course-details__sidebar__item"><h3 class="course-details__sidebar__title">Ringkasan</h3><ul class="list-unstyled course-details__sidebar__list"><li><span><i class="fa fa-users"></i> Kapasitas</span><strong><?= esc($class['capacity']) ?></strong></li><li><span><i class="fa fa-laptop"></i> Mode</span><strong><?= esc($class['mode']) ?></strong></li><li><span><i class="fa fa-calendar"></i> Periode</span><strong><?= esc($class['period']) ?></strong></li></ul></div></aside></div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
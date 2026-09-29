<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => $instructor['name'],
    'breadcrumbs' => [
        ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
        ['label' => 'Instruktur', 'url' => site_url('arena-belajar/instruktur')],
        ['label' => 'Profil'],
    ],
]) ?>

<section class="team-details">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-5 col-lg-5"><div class="team-details__img"><img src="<?= base_url('assets/main/' . $instructor['detailImage']) ?>" alt="<?= esc($instructor['name']) ?>"></div></div>
            <div class="col-xl-7 col-lg-7"><div class="team-details__content"><p class="team-details__designation"><?= esc($instructor['role']) ?></p><h2 class="team-details__title"><?= esc($instructor['name']) ?></h2><p class="team-details__text"><?= esc($instructor['bio']) ?></p><ul class="list-unstyled team-details__list"><li><span>Keahlian</span><strong><?= esc($instructor['specialization']) ?></strong></li><li><span>Course</span><strong><?= esc($instructor['courses']) ?></strong></li><li><span>Peserta</span><strong><?= esc($instructor['students']) ?></strong></li></ul><div class="team-details__social"><a href="#" aria-label="Facebook"><i class="fa fa-facebook"></i></a><a href="#" aria-label="Twitter"><i class="fa fa-twitter"></i></a><a href="#" aria-label="LinkedIn"><i class="fa fa-linkedin"></i></a></div></div></div>
        </div>
    </div>
</section>

<section class="course-one course-one--related">
    <div class="container"><div class="section-title text-center"><span class="section-title__tagline">Course Pilihan</span><h2 class="section-title__title">Materi pembelajaran terkait.</h2></div><div class="row"><?php foreach ($courses as $course): ?><div class="col-xl-4 col-lg-4 col-md-6"><div class="course-one__single"><div class="course-one__img"><img src="<?= base_url('assets/main/' . $course['image']) ?>" alt="<?= esc($course['title']) ?>"></div><div class="course-one__content"><p class="course-one__category"><?= esc($course['category']) ?></p><h3 class="course-one__title"><a href="<?= site_url('arena-belajar/course/' . $course['slug']) ?>"><?= esc($course['title']) ?></a></h3></div></div></div><?php endforeach; ?></div></div>
</section>
<?= $this->endSection() ?>
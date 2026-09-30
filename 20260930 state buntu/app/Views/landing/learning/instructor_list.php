<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?= view('landing/components/page_header', [
    'title' => 'Instruktur',
    'breadcrumbs' => [
        ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
        ['label' => 'Instruktur'],
    ],
]) ?>

<section class="team-one team-one--inner">
    <div class="container">
        <div class="section-title text-center"><span class="section-title__tagline">Fasilitator Pembelajaran</span><h2 class="section-title__title">Belajar bersama instruktur PBJ.</h2></div>
        <div class="row">
            <?php foreach ($instructors as $instructor): ?>
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="team-one__single">
                        <div class="team-one__img"><img src="<?= base_url('assets/main/' . $instructor['image']) ?>" alt="<?= esc($instructor['name']) ?>"></div>
                        <div class="team-one__content">
                            <h3 class="team-one__name"><a href="<?= site_url('arena-belajar/instruktur/' . $instructor['slug']) ?>"><?= esc($instructor['name']) ?></a></h3>
                            <p class="team-one__designation"><?= esc($instructor['role']) ?></p>
                            <p><?= esc($instructor['courses']) ?> · <?= esc($instructor['students']) ?></p>
                            <a href="<?= site_url('arena-belajar/instruktur/' . $instructor['slug']) ?>" class="team-one__read-more">Lihat Profil <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
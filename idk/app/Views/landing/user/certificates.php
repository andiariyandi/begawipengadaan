<?= $this->extend('landing/parts/layout/user') ?>
<?= $this->section('content') ?>
<section class="landing-user-page courses-one">
    <div class="section-title text-center">
        <p class="section-title__tagline">Pencapaian Anda</p>
        <h2 class="section-title__title">Sertifikat</h2>
    </div>
    <?php if (empty($certificates)): ?>
        <div class="landing-user-empty">
            <span class="icon-award landing-user-empty__icon"></span>
            <h3>Belum ada sertifikat</h3>
            <p>Selesaikan pelatihan dan assessment untuk mendapatkan sertifikat.</p>
            <a class="thm-btn" href="<?= route_to('user.arena') ?>">Buka Arena Belajar</a>
        </div>
    <?php else: ?>
        <div class="row landing-card-grid">
            <?php foreach ($certificates as $cert): ?>
                <div class="col-xl-4 col-md-6 landing-card-column">
                    <article class="courses-one__single certificate-card">
                        <div class="courses-one__content">
                            <p class="courses-one__tagline">Sertifikat Kompetensi</p>
                            <h3 class="courses-one__title"><?= esc($cert['course_title']) ?></h3>
                            <p class="courses-one__text"><?= esc($cert['certificate_number']) ?><br><?= esc(date('d M Y', strtotime($cert['issued_date']))) ?></p>
                            <span class="landing-status <?= $cert['status'] === 'issued' ? 'landing-status--success' : 'landing-status--danger' ?>"><?= $cert['status'] === 'issued' ? 'Aktif' : 'Dicabut' ?></span>
                            <?php if ($cert['status'] === 'issued'): ?>
                                <div class="courses-one__btn-box"><a class="thm-btn-two" href="<?= route_to('certificate.download', $cert['id']) ?>">Unduh <i class="icon-download"></i></a></div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>

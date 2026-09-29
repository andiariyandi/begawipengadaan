<?= $this->extend('landing/parts/layout/user') ?>
<?= $this->section('content') ?>
<section class="landing-user-page courses-one">
    <div class="section-title text-center">
        <p class="section-title__tagline">Pembelajaran PBJ</p>
        <h2 class="section-title__title">Arena Belajar</h2>
    </div>
    <?php if (empty($enrollments)): ?>
        <div class="landing-user-empty">
            <span class="icon-book-open landing-user-empty__icon"></span>
            <h3>Belum ada kelas yang diikuti</h3>
            <p>Temukan program pembelajaran yang sesuai untuk meningkatkan kompetensi PBJ.</p>
            <a class="thm-btn" href="<?= route_to('learning.enrollments.browse') ?>">Cari Kelas</a>
        </div>
    <?php else: ?>
        <div class="row landing-card-grid">
            <?php foreach ($enrollments as $enrollment): ?>
                <div class="col-xl-4 col-md-6">
                    <article class="courses-one__single landing-user-card-item">
                        <div class="courses-one__content">
                            <p class="courses-one__tagline">Program Pelatihan</p>
                            <h3 class="courses-one__title"><?= esc($enrollment['course_title']) ?></h3>
                            <p class="courses-one__text"><?= esc(date('d M Y', strtotime($enrollment['start_date']))) ?> - <?= esc(date('d M Y', strtotime($enrollment['end_date']))) ?></p>
                            <span class="landing-status <?= $enrollment['final_status'] === 'passed' ? 'landing-status--success' : 'landing-status--warning' ?>"><?= $enrollment['final_status'] === 'passed' ? 'Selesai' : 'Sedang Berjalan' ?></span>
                            <div class="courses-one__btn-box"><a class="thm-btn-two" href="<?= route_to('learning.study.modules', $enrollment['id']) ?>">Buka Materi <i class="icon-angles-right"></i></a></div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>

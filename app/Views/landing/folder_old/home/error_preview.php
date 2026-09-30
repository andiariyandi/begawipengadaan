<?= $this->extend('landing/layouts/landing') ?>

<?= $this->section('content') ?>
<section class="error-page">
    <div class="container"><div class="error-page__inner"><div class="error-page__img"><img src="<?= base_url('assets/main/images/resources/error-page-img1.png') ?>" alt="Error <?= esc($statusCode ?? '404') ?>"></div><h1 class="error-page__title"><?= esc($statusCode ?? '404') ?></h1><h2 class="error-page__sub-title"><?= esc($pageTitle ?? 'Terjadi Kesalahan') ?></h2><p class="error-page__text"><?= esc($message ?? 'Halaman tidak dapat ditampilkan saat ini.') ?></p><a href="<?= site_url('/') ?>" class="thm-btn"><span class="icon-angles-right"></span>Kembali ke Beranda</a></div></div>
</section>
<?= $this->endSection() ?>

<?= $this->extend('landing/layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-header">
    <div class="page-header__bg" style="background-image: url(<?= base_url('assets/main/images/backgrounds/page-header-bg.jpg') ?>);"></div>
    <div class="container"><div class="page-header__inner"><h2>Kontak</h2><ul class="thm-breadcrumb list-unstyled"><li><a href="<?= route_to('landing.home') ?>">Beranda</a></li><li><span class="icon-angle-right"></span></li><li>Kontak</li></ul></div></div>
</section>

<section class="contact-page">
    <div class="container">
        <div class="row">
            <div class="col-xl-4 col-lg-5"><div class="contact-page__left"><div class="section-title text-left"><div class="section-title__tagline-box"><span class="section-title__tagline">Hubungi Kami</span></div><h2 class="section-title__title">Kami siap menerima masukan Anda</h2></div><p class="contact-page__text">Gunakan formulir ini untuk menyampaikan pertanyaan, saran, atau informasi terkait layanan BegawiPBJ.</p><ul class="contact-page__contact-list list-unstyled"><li><div class="icon"><span class="icon-email"></span></div><div class="content"><h4>Email</h4><p><a href="mailto:info@begawipbj.lampungprov.go.id">info@begawipbj.lampungprov.go.id</a></p></div></li><li><div class="icon"><span class="icon-contact"></span></div><div class="content"><h4>Telepon</h4><p><a href="tel:+62721481107">(0721) 481107</a></p></div></li><li><div class="icon"><span class="icon-location"></span></div><div class="content"><h4>Lokasi</h4><p>Provinsi Lampung, Indonesia</p></div></li></ul></div></div>
            <div class="col-xl-8 col-lg-7"><div class="contact-page__right">
                <?php if (session('success')): ?><div class="alert alert-success" role="alert"><?= esc(session('success')) ?></div><?php endif; ?>
                <?php if (session('errors')): ?><div class="alert alert-danger" role="alert"><ul class="mb-0"><?php foreach ((array) session('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                <form action="<?= route_to('landing.pages.contact.send') ?>" method="post" class="contact-page__form contact-form-validated">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-xl-6"><div class="contact-page__input-box"><input type="text" name="name" value="<?= esc(old('name')) ?>" placeholder="Nama lengkap" required><div class="contact-page__input-icon"><span class="icon-user"></span></div></div></div>
                        <div class="col-xl-6"><div class="contact-page__input-box"><input type="email" name="email" value="<?= esc(old('email')) ?>" placeholder="Alamat email" required><div class="contact-page__input-icon"><span class="icon-email"></span></div></div></div>
                        <div class="col-xl-12"><div class="contact-page__input-box"><input type="text" name="subject" value="<?= esc(old('subject')) ?>" placeholder="Subjek" required><div class="contact-page__input-icon"><span class="icon-book"></span></div></div></div>
                        <div class="col-xl-12"><div class="contact-page__input-box text-message-box"><textarea name="message" placeholder="Tuliskan pesan Anda" required><?= esc(old('message')) ?></textarea></div><div class="contact-page__btn-box"><button type="submit" class="thm-btn">Kirim Pesan <span class="icon-angles-right"></span></button></div></div>
                    </div>
                </form>
            </div></div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
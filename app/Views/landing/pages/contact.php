<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<section class="page__title-area pt-120 pb-90" data-background="<?= base_url('assets/main/assets/img/page-title/page-title.jpg') ?>">
    <div class="page__title-shape"><img class="page-title-shape-5" src="<?= base_url('assets/main/assets/img/page-title/page-title-shape-1.png') ?>" alt=""></div>
    <div class="container"><div class="row"><div class="col-xxl-12"><div class="page__title-content text-center"><h1 class="page__title">Kontak</h1><?= view('landing/components/breadcrumb', ['breadcrumbs' => [['label' => 'Kontak']]]) ?></div></div></div></div>
</section>

<section class="contact__area pt-115 pb-120">
    <div class="container">
        <div class="row">
            <div class="col-xxl-4 col-xl-4 col-lg-4">
                <div class="contact__info mr-70">
                    <h3>Hubungi Kami</h3>
                    <p>Gunakan formulir ini untuk pertanyaan umum mengenai layanan BegawiPBJ.</p>
                    <ul>
                        <li><div class="contact__info-icon"><span><i class="fal fa-envelope"></i></span></div><div class="contact__info-content"><h4>Email</h4><a href="mailto:info@begawipbj.go.id">info@begawipbj.go.id</a></div></li>
                        <li><div class="contact__info-icon"><span><i class="fal fa-phone-alt"></i></span></div><div class="contact__info-content"><h4>Telepon</h4><a href="tel:+620000000000">(000) 0000 0000</a></div></li>
                        <li><div class="contact__info-icon"><span><i class="fal fa-map-marker-alt"></i></span></div><div class="contact__info-content"><h4>Alamat</h4><p>Alamat instansi akan ditampilkan pada konfigurasi produksi.</p></div></li>
                    </ul>
                </div>
            </div>
            <div class="col-xxl-8 col-xl-8 col-lg-8">
                <div class="contact__form">
                    <h3>Kirim Pesan</h3>
                    <?= $this->include('landing/components/flash_message') ?>
                    <form action="<?= site_url('kontak') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-xxl-6 col-xl-6 col-lg-6"><div class="contact__input"><input type="text" name="name" placeholder="Nama lengkap" autocomplete="name"></div></div>
                            <div class="col-xxl-6 col-xl-6 col-lg-6"><div class="contact__input"><input type="email" name="email" placeholder="Alamat email" autocomplete="email"></div></div>
                            <div class="col-xxl-12"><div class="contact__input"><input type="text" name="subject" placeholder="Subjek pesan"></div></div>
                            <div class="col-xxl-12"><div class="contact__input"><textarea name="message" placeholder="Tulis pesan Anda"></textarea></div></div>
                            <div class="col-xxl-12"><div class="contact__btn"><button type="submit" class="e-btn">Kirim Pesan</button></div></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
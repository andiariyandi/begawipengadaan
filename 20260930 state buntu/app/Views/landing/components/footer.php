<footer class="site-footer">
    <div class="site-footer__shape-1">
        <img src="<?= base_url('assets/main/images/shapes/site-footer-shape-bg.png') ?>" alt="">
    </div>
    <div class="container">
        <div class="site-footer__top">
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                    <div class="footer-widget__column footer-widget__about">
                        <div class="footer-widget__logo"><a href="<?= site_url('/') ?>"><img src="<?= base_url('assets/main/images/resources/site-footer-logo-2.png') ?>" alt="BegawiPBJ"></a></div>
                        <p class="footer-widget__about-text">Portal pembelajaran, pengetahuan, regulasi, event, dan sertifikat digital Pengadaan Barang/Jasa.</p>
                        <div class="site-footer__social">
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                    <div class="footer-widget__column footer-widget__links">
                        <h3 class="footer-widget__title">Layanan</h3>
                        <ul class="footer-widget__links-list list-unstyled">
                            <li><a href="<?= site_url('arena-belajar') ?>">Arena Belajar</a></li>
                            <li><a href="<?= site_url('bunga-rampai') ?>">Bunga Rampai</a></li>
                            <li><a href="<?= site_url('regulasi') ?>">Regulasi</a></li>
                            <li><a href="<?= site_url('event') ?>">Event</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                    <div class="footer-widget__column footer-widget__links">
                        <h3 class="footer-widget__title">Informasi</h3>
                        <ul class="footer-widget__links-list list-unstyled">
                            <li><a href="<?= site_url('tentang') ?>">Tentang</a></li>
                            <li><a href="<?= site_url('kontak') ?>">Kontak</a></li>
                            <li><a href="<?= site_url('statistik') ?>">Statistik</a></li>
                            <li><a href="<?= site_url('sertifikat/verifikasi') ?>">Verifikasi Sertifikat</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="400ms">
                    <div class="footer-widget__column footer-widget__contact">
                        <h3 class="footer-widget__title">Hubungi Kami</h3>
                        <p class="footer-widget__contact-text">Informasi layanan BegawiPBJ dan dukungan umum.</p>
                        <ul class="footer-widget__contact-list list-unstyled">
                            <li>
                                <div class="icon"><i class="icon-email"></i></div>
                                <div class="text">
                                    <p><a href="mailto:info@begawipbj.go.id">info@begawipbj.go.id</a></p>
                                </div>
                            </li>
                            <li>
                                <div class="icon"><i class="icon-contact"></i></div>
                                <div class="text">
                                    <p><a href="tel:+620000000000">(000) 0000 0000</a></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="site-footer__bottom">
            <div class="site-footer__bottom-inner">
                <p class="site-footer__bottom-text">© <?= date('Y') ?> BegawiPBJ. Semua hak dilindungi.</p>
                <ul class="list-unstyled site-footer__bottom-menu">
                    <li><a href="<?= site_url('privasi') ?>">Privasi</a></li>
                    <li><a href="<?= site_url('syarat-ketentuan') ?>">Syarat dan Ketentuan</a></li>
                    <li><a href="<?= site_url('aksesibilitas') ?>">Aksesibilitas</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
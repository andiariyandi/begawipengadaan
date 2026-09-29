<footer class="site-footer">
    <div class="site-footer__top">
        <div class="container">
            <div class="row">
                <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                    <div class="footer-widget__column footer-widget__about">
                        <div class="footer-widget__logo">
                            <a href="<?= route_to('landing.home') ?>"><img src="<?= base_url('assets/main/images/resources/site-footer-logo-2.png') ?>" alt="BegawiPBJ"></a>
                        </div>
                        <p class="footer-widget__about-text">Ekosistem digital Pengadaan Barang/Jasa Pemerintah Provinsi Lampung untuk pembelajaran, pengetahuan, regulasi, event, dan layanan publik PBJ.</p>
                        <div class="site-footer__social">
                            <a href="#" aria-label="Facebook"><i class="icon-facebook"></i></a>
                            <a href="#" aria-label="Twitter"><i class="icon-twitter"></i></a>
                            <a href="#" aria-label="Instagram"><i class="icon-instagram"></i></a>
                            <a href="#" aria-label="Youtube"><i class="icon-youtube"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                    <div class="footer-widget__column footer-widget__links">
                        <div class="footer-widget__title-box">
                            <h3 class="footer-widget__title">Navigasi</h3>
                        </div>
                        <ul class="footer-widget__links-list list-unstyled">
                            <li><a href="<?= route_to('landing.home') ?>">Beranda</a></li>
                            <li><a href="<?= route_to('landing.pages.about') ?>">Tentang Kami</a></li>
                            <li><a href="<?= route_to('landing.learning.index') ?>">Arena Belajar</a></li>
                            <li><a href="<?= route_to('landing.knowledge.index') ?>">Bunga Rampai</a></li>
                            <li><a href="<?= route_to('landing.pages.contact') ?>">Kontak</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                    <div class="footer-widget__column footer-widget__links">
                        <div class="footer-widget__title-box">
                            <h3 class="footer-widget__title">Layanan</h3>
                        </div>
                        <ul class="footer-widget__links-list list-unstyled">
                            <li><a href="<?= route_to('landing.regulations.index') ?>">Regulasi PBJ</a></li>
                            <li><a href="<?= route_to('landing.news.index') ?>">Berita</a></li>
                            <li><a href="<?= route_to('landing.events.index') ?>">Event dan Agenda</a></li>
                            <li><a href="<?= route_to('landing.statistics.index') ?>">Statistik Publik</a></li>
                            <li><a href="<?= route_to('landing.certificates.verify.form') ?>">Verifikasi Sertifikat</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="400ms">
                    <div class="footer-widget__column footer-widget__contact">
                        <div class="footer-widget__title-box">
                            <h3 class="footer-widget__title">Kontak</h3>
                        </div>
                        <ul class="footer-widget__contact-list list-unstyled">
                            <li>
                                <div class="icon"><span class="icon-email"></span></div>
                                <div class="text">
                                    <p><a href="mailto:info@begawipbj.lampungprov.go.id">info@begawipbj.lampungprov.go.id</a></p>
                                </div>
                            </li>
                            <li>
                                <div class="icon"><span class="icon-contact"></span></div>
                                <div class="text">
                                    <p><a href="tel:+62721481107">(0721) 481107</a></p>
                                </div>
                            </li>
                            <li>
                                <div class="icon"><span class="icon-location"></span></div>
                                <div class="text">
                                    <p>Provinsi Lampung, Indonesia</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container">
            <div class="site-footer__bottom-inner">
                <p class="site-footer__bottom-text">© <?= date('Y') ?> BegawiPBJ. Pemerintah Provinsi Lampung.</p>
                <ul class="list-unstyled site-footer__bottom-menu">
                    <li><a href="<?= route_to('landing.pages.privacy') ?>">Privasi</a></li>
                    <li><a href="<?= route_to('landing.pages.terms') ?>">Syarat Ketentuan</a></li>
                    <li><a href="<?= route_to('landing.pages.accessibility') ?>">Aksesibilitas</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
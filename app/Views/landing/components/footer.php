<footer class="site-footer">
    <div class="site-footer__shape-bg" style="background-image: url(<?= base_url('assets/main/images/shapes/site-footer-shape-bg.png') ?>);"></div>
    <div class="container">
        <div class="site-footer__top">
            <div class="row">
                <div class="col-xl-6 col-lg-6 wow fadeInUp" data-wow-delay="100ms">
                    <div class="site-footer__top-left">
                        <div class="site-footer__logo">
                            <a href="<?= site_url('/') ?>"><img src="<?= base_url('assets/main/images/resources/site-footer-logo-2.png') ?>" alt="BegawiPengadaan"></a>
                        </div>
                        <p class="site-footer__top-left-text"><?= esc(setting('organization.description', 'BEGAWI PBJ (Bimbingan Edukasi & Gerakan Akselerasi Wawasan PBJ) Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.')) ?></p>
                        <div class="site-footer__contact-info">
                            <ul class="list-unstyled site-footer__contact-info-list">
                                <li>
                                    <div class="site-footer__contact-info-icon-box">
                                        <div class="site-footer__contact-info-icon"><span class="icon-email"></span>
                                            <p class="site-footer__contact-info-icon-text">Email:</p>
                                        </div>
                                        <p class="site-footer__contact-info-text"><a href="mailto:<?= esc(setting('organization.email', 'biropbj@lampungprov.go.id')) ?>"><?= esc(setting('organization.email', 'biropbj@lampungprov.go.id')) ?></a></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="site-footer__contact-info-icon-box">
                                        <div class="site-footer__contact-info-icon"><span class="icon-location"></span>
                                            <p class="site-footer__contact-info-icon-text">Alamat:</p>
                                        </div>
                                        <p class="site-footer__contact-info-text"><?= nl2br(esc(setting('organization.address', "Biro PBJ Setda Provinsi Lampung\nBandar Lampung"))) ?></p>
                                    </div>
                                </li>
                            </ul>
                            <ul class="list-unstyled site-footer__contact-info-list site-footer__contact-info-list--two">
                                <li>
                                    <div class="site-footer__contact-info-icon-box">
                                        <div class="site-footer__contact-info-icon"><span class="icon-phone"></span>
                                            <p class="site-footer__contact-info-icon-text">Telepon:</p>
                                        </div>
                                        <p class="site-footer__contact-info-text"><a href="tel:<?= preg_replace('/[^0-9+]/', '', setting('organization.phone', '+6285117779783')) ?>"><?= esc(setting('organization.phone', '+62 851-1777-9783')) ?></a></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="site-footer__contact-info-icon-box">
                                        <div class="site-footer__contact-info-icon"><span class="icon-clock"></span>
                                            <p class="site-footer__contact-info-icon-text">Jam Layanan:</p>
                                        </div>
                                        <p class="site-footer__contact-info-text"><?= esc(setting('organization.operational_hours', 'Senin - Jumat, 08.00 - 16.00 WIB')) ?></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="site-footer__app-and-social-box">
                            <div class="site-footer__social-box">
                                <h4 class="site-footer__app-and-social-title">Ikuti Kami:</h4>
                                <div class="site-footer__social-box-inner">
                                    <a href="<?= esc(setting('social.linkedin_url', '#')) ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><span class="fab fa-linkedin-in"></span></a>
                                    <a href="<?= esc(setting('social.facebook_url', '#')) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><span class="fab fa-facebook-f"></span></a>
                                    <a href="<?= esc(setting('social.instagram_url', '#')) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><span class="fab fa-instagram"></span></a>
                                    <a href="<?= esc(setting('social.youtube_url', '#')) ?>" target="_blank" rel="noopener noreferrer" aria-label="Youtube"><span class="fab fa-youtube"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6 wow fadeInUp" data-wow-delay="300ms">
                    <div class="site-footer__top-right">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <div class="site-footer__links">
                                    <h4 class="site-footer__title">Tautan Cepat</h4>
                                    <ul class="site-footer__links-list list-unstyled">
                                        <li><a href="<?= site_url('/') ?>"><span class="icon-plus"></span> Beranda</a></li>
                                        <li><a href="<?= site_url('tentang') ?>"><span class="icon-plus"></span> Tentang Kami</a></li>
                                        <li><a href="<?= site_url('/#section-testimonies') ?>"><span class="icon-plus"></span> Testimoni Pengguna</a></li>
                                        <li><a href="<?= site_url('/#section-team') ?>"><span class="icon-plus"></span> Tim Pengembang</a></li>
                                        <li><a href="<?= site_url('arena-belajar') ?>"><span class="icon-plus"></span> Arena Belajar</a></li>
                                        <li><a href="<?= site_url('event') ?>"><span class="icon-plus"></span> Event</a></li>
                                        <li><a href="<?= site_url('kontak') ?>"><span class="icon-plus"></span> Kontak</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <div class="site-footer__useful-links">
                                    <h4 class="site-footer__title">Layanan</h4>
                                    <ul class="site-footer__links-list list-unstyled">
                                        <li><a href="<?= site_url('bunga-rampai') ?>"><span class="icon-plus"></span> Bunga Rampai</a></li>
                                        <li><a href="<?= site_url('regulasi') ?>"><span class="icon-plus"></span> Regulasi PBJ</a></li>
                                        <li><a href="<?= site_url('statistik') ?>"><span class="icon-plus"></span> Statistik</a></li>
                                        <li><a href="<?= site_url('verifikasi-sertifikat') ?>"><span class="icon-plus"></span> Verifikasi Sertifikat</a></li>
                                        <li><a href="<?= site_url('bunga-rampai/faq') ?>"><span class="icon-plus"></span> FAQ</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="site-footer__newsletter-box">
                            <h4 class="site-footer__title">Newsletter</h4>
                            <form class="site-footer__newsletter-form" action="<?= site_url('newsletter/subscribe') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="site-footer__newsletter-input"><input type="email" name="email" required placeholder="Masukkan alamat email" aria-label="Alamat email"></div>
                                <button type="submit" class="thm-btn"><span class="icon-angles-right"></span> Berlangganan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-footer__bottom-inner">
                        <div class="site-footer__copyright">
                            <p class="site-footer__copyright-text">Copyright &copy; <?= date('Y') ?> <a href="<?= site_url('/') ?>">BegawiPengadaan</a>. <?= esc(setting('footer.copyright_text', 'Pemerintah Provinsi Lampung.')) ?></p>
                        </div>
                        <div class="site-footer__bottom-card-box">
                            <p class="site-footer__copyright-text"><?= esc(setting('footer.subtext', setting('general.tagline', 'Pengadaan kompeten, transparan, dan berdampak.'))) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
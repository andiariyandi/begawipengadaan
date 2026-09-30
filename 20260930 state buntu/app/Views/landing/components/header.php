<?php $activeMenu = $activeMenu ?? ''; ?>
<header class="main-header main-header-three">
    <div class="main-menu__top">
        <div class="container">
            <div class="main-menu__top-inner">
                <ul class="list-unstyled main-menu__contact-list">
                    <li>
                        <div class="icon"><i class="icon-email"></i></div>
                        <div class="text"><p><a href="mailto:info@begawipbj.go.id">info@begawipbj.go.id</a></p></div>
                    </li>
                    <li>
                        <div class="icon"><i class="icon-contact"></i></div>
                        <div class="text"><p><a href="tel:+620000000000">(000) 0000 0000</a></p></div>
                    </li>
                </ul>
                <ul class="list-unstyled main-menu__contact-list main-menu__location">
                    <li>
                        <div class="icon"><i class="icon-location"></i></div>
                        <div class="text"><p>Portal Pengadaan Barang/Jasa</p></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <nav class="main-menu">
        <div class="main-menu__wrapper">
            <div class="container">
                <div class="main-menu__wrapper-inner">
                    <div class="main-menu__left">
                        <div class="main-menu__logo">
                            <a href="<?= site_url('/') ?>"><img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="BegawiPBJ"></a>
                        </div>

                        <div class="main-menu__category-box">
                            <div class="main-menu__category-btn">
                                <i class="fas fa-th"></i>
                                <p>Layanan</p>
                                <span class="icon-down-arrow"></span>
                            </div>
                            <ul class="list-unstyled main-menu__category-sub-menu">
                                <li><a href="<?= site_url('arena-belajar') ?>"><div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-1.png') ?>" alt=""></div><div class="main-menu__category-content"><h5>Arena<br>Belajar</h5><p>Program & Course</p></div></a></li>
                                <li><a href="<?= site_url('bunga-rampai') ?>"><div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-2.png') ?>" alt=""></div><div class="main-menu__category-content"><h5>Bunga<br>Rampai</h5><p>Artikel & Media</p></div></a></li>
                                <li><a href="<?= site_url('regulasi') ?>"><div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-3.png') ?>" alt=""></div><div class="main-menu__category-content"><h5>Regulasi<br>PBJ</h5><p>Repository</p></div></a></li>
                                <li><a href="<?= site_url('berita') ?>"><div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-4.png') ?>" alt=""></div><div class="main-menu__category-content"><h5>Berita &<br>Agenda</h5><p>Informasi Publik</p></div></a></li>
                                <li><a href="<?= site_url('event') ?>"><div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-5.png') ?>" alt=""></div><div class="main-menu__category-content"><h5>Event<br>PBJ</h5><p>Agenda & Kehadiran</p></div></a></li>
                                <li><a href="<?= site_url('sertifikat/verifikasi') ?>"><div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-6.png') ?>" alt=""></div><div class="main-menu__category-content"><h5>Sertifikat<br>Digital</h5><p>Verifikasi</p></div></a></li>
                                <li><a href="<?= site_url('statistik') ?>"><div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-7.png') ?>" alt=""></div><div class="main-menu__category-content"><h5>Statistik<br>Publik</h5><p>Indikator Aman</p></div></a></li>
                                <li><a href="<?= site_url('tentang') ?>"><div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-8.png') ?>" alt=""></div><div class="main-menu__category-content"><h5>Tentang<br>Kami</h5><p>BegawiPBJ</p></div></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="main-menu__main-menu-box">
                        <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                        <ul class="main-menu__list">
                            <li><a class="<?= $activeMenu === 'home' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Beranda</a></li>
                            <li><a class="<?= $activeMenu === 'learning' ? 'active' : '' ?>" href="<?= site_url('arena-belajar') ?>">Arena Belajar</a></li>
                            <li><a class="<?= $activeMenu === 'knowledge' ? 'active' : '' ?>" href="<?= site_url('bunga-rampai') ?>">Bunga Rampai</a></li>
                            <li><a class="<?= $activeMenu === 'regulations' ? 'active' : '' ?>" href="<?= site_url('regulasi') ?>">Regulasi</a></li>
                            <li><a class="<?= $activeMenu === 'events' ? 'active' : '' ?>" href="<?= site_url('event') ?>">Event</a></li>
                            <li><a class="<?= $activeMenu === 'contact' ? 'active' : '' ?>" href="<?= site_url('kontak') ?>">Kontak</a></li>
                        </ul>
                    </div>

                    <div class="main-menu__right">
                        <div class="main-menu__search-cart-box">
                            <div class="main-menu__search-box"><a href="#" class="main-menu__search searcher-toggler-box icon-search"></a></div>
                        </div>
                        <div class="main-menu__btn-boxes">
                            <div class="main-menu__btn-box-1"><a href="<?= site_url('dashboard/masuk') ?>" class="thm-btn">Masuk</a></div>
                            <div class="main-menu__btn-box-2"><a href="<?= site_url('dashboard/daftar') ?>" class="thm-btn">Daftar</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<div class="stricky-header stricked-menu main-menu">
    <div class="sticky-header__content"></div>
</div>

<div class="mobile-nav__wrapper">
    <div class="mobile-nav__overlay mobile-nav__toggler"></div>
    <div class="mobile-nav__content">
        <span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>
        <div class="logo-box"><a href="<?= site_url('/') ?>" aria-label="BegawiPBJ"><img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="BegawiPBJ"></a></div>
        <div class="mobile-nav__container"></div>
        <ul class="mobile-nav__contact list-unstyled">
            <li><i class="fa fa-envelope"></i><a href="mailto:info@begawipbj.go.id">info@begawipbj.go.id</a></li>
            <li><i class="fa fa-phone-alt"></i><a href="tel:+620000000000">(000) 0000 0000</a></li>
        </ul>
        <div class="mobile-nav__social">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
    </div>
</div>

<div class="search-popup">
    <div class="search-popup__overlay search-toggler"></div>
    <div class="search-popup__content">
        <form action="<?= site_url('bunga-rampai/cari') ?>" method="get">
            <label for="public-search" class="sr-only">Cari</label>
            <input id="public-search" type="text" name="q" placeholder="Cari informasi PBJ...">
            <button type="submit" aria-label="Cari"><i class="icon-search"></i></button>
        </form>
    </div>
</div>

<?php $activeMenu = $activeMenu ?? ''; ?>
<header class="main-header main-header-three">
    <div class="main-menu__top">
        <div class="container">
            <div class="main-menu__top-inner">
                <ul class="list-unstyled main-menu__contact-list">
                    <li>
                        <div class="icon"><i class="icon-email"></i></div>
                        <div class="text"><p><a href="mailto:info@begawipbj.lampungprov.go.id">info@begawipbj.lampungprov.go.id</a></p></div>
                    </li>
                    <li>
                        <div class="icon"><i class="icon-contact"></i></div>
                        <div class="text"><p><a href="tel:+62721481107">(0721) 481107</a></p></div>
                    </li>
                </ul>
                <ul class="list-unstyled main-menu__contact-list main-menu__location">
                    <li>
                        <div class="icon"><i class="icon-location"></i></div>
                        <div class="text"><p>Provinsi Lampung, Indonesia</p></div>
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
                            <a href="<?= route_to('landing.home') ?>">
                                <img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="BegawiPBJ">
                            </a>
                        </div>
                        <div class="main-menu__category-box">
                            <div class="main-menu__category-btn">
                                <i class="fas fa-th"></i>
                                <p>Layanan</p>
                                <span class="icon-down-arrow"></span>
                            </div>
                            <ul class="list-unstyled main-menu__category-sub-menu">
                                <li>
                                    <a href="<?= route_to('landing.learning.programs.index') ?>">
                                        <div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-1.png') ?>" alt=""></div>
                                        <div class="main-menu__category-content"><h5>Program<br>Pembelajaran</h5><p>Arena Belajar</p></div>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= route_to('landing.knowledge.articles.index') ?>">
                                        <div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-2.png') ?>" alt=""></div>
                                        <div class="main-menu__category-content"><h5>Bunga<br>Rampai</h5><p>Pusat Pengetahuan</p></div>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= route_to('landing.regulations.index') ?>">
                                        <div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-3.png') ?>" alt=""></div>
                                        <div class="main-menu__category-content"><h5>Regulasi<br>PBJ</h5><p>Repository Publik</p></div>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= route_to('landing.events.index') ?>">
                                        <div class="main-menu__category-icon"><img src="<?= base_url('assets/main/images/icon/categoyr-two-icon-4.png') ?>" alt=""></div>
                                        <div class="main-menu__category-content"><h5>Event dan<br>Agenda</h5><p>Kegiatan PBJ</p></div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="main-menu__main-menu-box">
                        <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                        <ul class="main-menu__list">
                            <li class="<?= $activeMenu === 'home' ? 'current' : '' ?>"><a href="<?= route_to('landing.home') ?>">Beranda</a></li>
                            <li class="<?= $activeMenu === 'about' ? 'current' : '' ?>"><a href="<?= route_to('landing.pages.about') ?>">Tentang</a></li>
                            <li class="dropdown <?= $activeMenu === 'learning' ? 'current' : '' ?>">
                                <a href="<?= route_to('landing.learning.index') ?>">Arena Belajar</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= route_to('landing.learning.index') ?>">Beranda Arena</a></li>
                                    <li><a href="<?= route_to('landing.learning.programs.index') ?>">Program</a></li>
                                    <li><a href="<?= route_to('landing.learning.courses.index') ?>">Course</a></li>
                                    <li><a href="<?= route_to('landing.learning.classes.index') ?>">Kelas</a></li>
                                    <li><a href="<?= route_to('landing.learning.instructors.index') ?>">Instruktur</a></li>
                                </ul>
                            </li>
                            <li class="dropdown <?= $activeMenu === 'knowledge' ? 'current' : '' ?>">
                                <a href="<?= route_to('landing.knowledge.index') ?>">Bunga Rampai</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= route_to('landing.knowledge.articles.index') ?>">Artikel</a></li>
                                    <li><a href="<?= route_to('landing.knowledge.faq') ?>">FAQ</a></li>
                                    <li><a href="<?= route_to('landing.knowledge.case-studies.index') ?>">Studi Kasus</a></li>
                                    <li><a href="<?= route_to('landing.knowledge.templates.index') ?>">Template</a></li>
                                    <li><a href="<?= route_to('landing.knowledge.media.index') ?>">Media</a></li>
                                    <li><a href="<?= route_to('landing.knowledge.videos.index') ?>">Video</a></li>
                                    <li><a href="<?= route_to('landing.knowledge.gallery.index') ?>">Galeri</a></li>
                                </ul>
                            </li>
                            <li class="dropdown <?= $activeMenu === 'regulations' ? 'current' : '' ?>">
                                <a href="<?= route_to('landing.regulations.index') ?>">Regulasi</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= route_to('landing.regulations.index') ?>">Repository Regulasi</a></li>
                                    <li><a href="<?= route_to('landing.regulations.search') ?>">Cari Regulasi</a></li>
                                </ul>
                            </li>
                            <li class="dropdown <?= $activeMenu === 'events' ? 'current' : '' ?>">
                                <a href="<?= route_to('landing.events.index') ?>">Event</a>
                                <ul class="shadow-box">
                                    <li><a href="<?= route_to('landing.events.index') ?>">Event</a></li>
                                    <li><a href="<?= route_to('landing.events.agenda') ?>">Agenda</a></li>
                                    <li><a href="<?= route_to('landing.events.calendar') ?>">Kalender</a></li>
                                </ul>
                            </li>
                            <li class="<?= $activeMenu === 'contact' ? 'current' : '' ?>"><a href="<?= route_to('landing.pages.contact') ?>">Kontak</a></li>
                        </ul>
                    </div>

                    <div class="main-menu__right">
                        <div class="main-menu__search-cart-box">
                            <div class="main-menu__search-box"><a href="#" class="main-menu__search searcher-toggler-box icon-search"></a></div>
                        </div>
                        <div class="main-menu__btn-boxes">
                            <div class="main-menu__btn-box-1"><a href="<?= route_to('dashboard.auth.login') ?>" class="thm-btn">Masuk</a></div>
                            <div class="main-menu__btn-box-2"><a href="<?= route_to('dashboard.auth.register.account') ?>" class="thm-btn">Daftar</a></div>
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
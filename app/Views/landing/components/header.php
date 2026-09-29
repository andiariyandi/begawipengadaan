<?php $activeMenu = $activeMenu ?? ''; ?>
<header>
    <div class="header__area header__transparent header__padding">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-6">
                    <div class="header__logo">
                        <a href="<?= site_url('/') ?>" class="text-white fw-bold fs-4">BegawiPBJ</a>
                    </div>
                </div>

                <div class="col-xxl-7 col-xl-7 col-lg-8 d-none d-lg-block">
                    <div class="main-menu main-menu-3">
                        <nav id="mobile-menu">
                            <ul>
                                <li><a class="<?= $activeMenu === 'home' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Beranda</a></li>
                                <li><a class="<?= $activeMenu === 'learning' ? 'active' : '' ?>" href="<?= site_url('arena-belajar') ?>">Arena Belajar</a></li>
                                <li><a class="<?= $activeMenu === 'knowledge' ? 'active' : '' ?>" href="<?= site_url('bunga-rampai') ?>">Bunga Rampai</a></li>
                                <li><a class="<?= $activeMenu === 'regulations' ? 'active' : '' ?>" href="<?= site_url('regulasi') ?>">Regulasi</a></li>
                                <li><a class="<?= $activeMenu === 'news' ? 'active' : '' ?>" href="<?= site_url('berita') ?>">Berita</a></li>
                                <li><a class="<?= $activeMenu === 'events' ? 'active' : '' ?>" href="<?= site_url('event') ?>">Event</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-3 col-lg-2 col-md-6 col-6">
                    <div class="header__right d-flex align-items-center justify-content-end">
                        <div class="header__btn d-none d-md-block">
                            <a href="<?= site_url('dashboard/masuk') ?>" class="e-btn e-btn-border">Masuk</a>
                            <a href="<?= site_url('dashboard/daftar') ?>" class="e-btn">Daftar</a>
                        </div>
                        <div class="sidebar__menu d-lg-none">
                            <div class="sidebar-toggle-btn ml-30" id="sidebar-toggle">
                                <span class="line"></span>
                                <span class="line"></span>
                                <span class="line"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="sidebar__area">
    <div class="sidebar__wrapper">
        <div class="sidebar__close"><button class="sidebar__close-btn" id="sidebar__close-btn"><span><i class="fal fa-times"></i></span><span>tutup</span></button></div>
        <div class="sidebar__content">
            <div class="mobile-menu fix"></div>
            <div class="sidebar__btn mt-20">
                <a href="<?= site_url('dashboard/masuk') ?>" class="e-btn e-btn-border">Masuk</a>
                <a href="<?= site_url('dashboard/daftar') ?>" class="e-btn">Daftar</a>
            </div>
        </div>
    </div>
</div>
<div class="body-overlay"></div>
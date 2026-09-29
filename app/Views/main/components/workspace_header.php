<?php $activeMenu = $activeMenu ?? ''; ?>
<header class="main-header main-header-three">
    <div class="main-menu__top">
        <div class="container">
            <div class="main-menu__top-inner">
                <ul class="list-unstyled main-menu__contact-list">
                    <li><div class="icon"><i class="icon-email"></i></div><div class="text"><p><a href="mailto:info@begawipbj.go.id">info@begawipbj.go.id</a></p></div></li>
                    <li><div class="icon"><i class="icon-contact"></i></div><div class="text"><p><a href="tel:+620000000000">(000) 0000 0000</a></p></div></li>
                </ul>
                <ul class="list-unstyled main-menu__contact-list main-menu__location"><li><div class="icon"><i class="icon-location"></i></div><div class="text"><p>Workspace BegawiPBJ</p></div></li></ul>
            </div>
        </div>
    </div>

    <nav class="main-menu">
        <div class="main-menu__wrapper">
            <div class="container">
                <div class="main-menu__wrapper-inner">
                    <div class="main-menu__left">
                        <div class="main-menu__logo"><a href="<?= site_url('workspace') ?>"><img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="BegawiPBJ"></a></div>
                    </div>
                    <div class="main-menu__main-menu-box">
                        <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                        <ul class="main-menu__list">
                            <li><a class="<?= $activeMenu === 'workspace-home' ? 'active' : '' ?>" href="<?= site_url('workspace') ?>">Workspace</a></li>
                            <li><a class="<?= $activeMenu === 'participant' ? 'active' : '' ?>" href="<?= site_url('workspace/peserta') ?>">Pembelajaran Saya</a></li>
                            <li><a class="<?= $activeMenu === 'bookmarks' ? 'active' : '' ?>" href="<?= site_url('workspace/bookmark') ?>">Bookmark</a></li>
                            <li><a class="<?= $activeMenu === 'notifications' ? 'active' : '' ?>" href="<?= site_url('workspace/notifikasi') ?>">Notifikasi</a></li>
                        </ul>
                    </div>
                    <div class="main-menu__right"><div class="main-menu__btn-boxes"><div class="main-menu__btn-box-1"><a href="<?= site_url('workspace/profil') ?>" class="thm-btn">Profil</a></div></div></div>
                </div>
            </div>
        </div>
    </nav>
</header>

<div class="stricky-header stricked-menu main-menu"><div class="sticky-header__content"></div></div>

<div class="mobile-nav__wrapper">
    <div class="mobile-nav__overlay mobile-nav__toggler"></div>
    <div class="mobile-nav__content">
        <span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>
        <div class="logo-box"><a href="<?= site_url('workspace') ?>"><img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" alt="BegawiPBJ"></a></div>
        <div class="mobile-nav__container"></div>
    </div>
</div>

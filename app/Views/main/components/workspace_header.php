<?php $activeMenu = $activeMenu ?? ''; ?>
<header>
    <div class="header__area header__padding header__shadow">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-6">
                    <div class="header__logo"><a href="<?= site_url('workspace') ?>" class="fw-bold fs-4">BegawiPBJ</a></div>
                </div>

                <div class="col-xxl-7 col-xl-7 col-lg-8 d-none d-lg-block">
                    <div class="main-menu">
                        <nav id="workspace-mobile-menu">
                            <ul>
                                <li><a class="<?= $activeMenu === 'workspace-home' ? 'active' : '' ?>" href="<?= site_url('workspace') ?>">Workspace</a></li>
                                <li><a class="<?= $activeMenu === 'participant' ? 'active' : '' ?>" href="<?= site_url('workspace/peserta') ?>">Pembelajaran Saya</a></li>
                                <li><a class="<?= $activeMenu === 'bookmarks' ? 'active' : '' ?>" href="<?= site_url('workspace/bookmark') ?>">Bookmark</a></li>
                                <li><a class="<?= $activeMenu === 'notifications' ? 'active' : '' ?>" href="<?= site_url('workspace/notifikasi') ?>">Notifikasi</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-3 col-lg-2 col-md-6 col-6">
                    <div class="header__right d-flex align-items-center justify-content-end">
                        <div class="header__btn d-none d-md-block"><a href="<?= site_url('workspace/profil') ?>" class="e-btn e-btn-border">Profil</a></div>
                        <div class="sidebar__menu d-lg-none"><div class="sidebar-toggle-btn ml-30" id="workspace-sidebar-toggle"><span class="line"></span><span class="line"></span><span class="line"></span></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
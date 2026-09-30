<header class="main-header main-header-three">
    <div class="main-menu__top">
        <div class="container">
            <div class="main-menu__top-inner">
                <ul class="list-unstyled main-menu__contact-list">
                    <li>
                        <div class="icon">
                            <i class="icon-email"></i>
                        </div>
                        <div class="text">
                            <p><a href="mailto:support@example.com">support@example.com</a>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="icon">
                            <i class="icon-contact"></i>
                        </div>
                        <div class="text">
                            <p><a href="tel:1212345678900">+12 (123) 456 78900</a></p>
                        </div>
                    </li>
                </ul>
                <ul class="list-unstyled main-menu__contact-list main-menu__location">
                    <li>
                        <div class="icon">
                            <i class="icon-location"></i>
                        </div>
                        <div class="text">
                            <p>684 West College St. Sun City, USA</p>
                        </div>
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
                            <a href="index.php"><img src="<?= base_url('assets/landing/') ?>images/resources/logo-1.png" alt=""></a>
                        </div>
                        <div class="main-menu__category-box">
                            <div class="main-menu__category-btn">
                                <i class="fas fa-th"></i>
                                <p>Category</p>
                                <span class="icon-down-arrow"></span>
                            </div>
                            <ul class="list-unstyled main-menu__category-sub-menu">
                                <li>
                                    <a href="#">
                                        <div class="main-menu__category-icon">
                                            <img src="<?= base_url('assets/landing/') ?>images/icon/categoyr-two-icon-1.png" alt="">
                                        </div>
                                        <div class="main-menu__category-content">
                                            <h5>Tech & <br> Programming</h5>
                                            <p>3+ Courses</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="main-menu__category-icon">
                                            <img src="<?= base_url('assets/landing/') ?>images/icon/categoyr-two-icon-2.png" alt="">
                                        </div>
                                        <div class="main-menu__category-content">
                                            <h5>Creative <br> Art</h5>
                                            <p>3+ Courses</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="main-menu__category-icon">
                                            <img src="<?= base_url('assets/landing/') ?>images/icon/categoyr-two-icon-3.png" alt="">
                                        </div>
                                        <div class="main-menu__category-content">
                                            <h5>Business & <br> Finance</h5>
                                            <p>3+ Courses</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="main-menu__category-icon">
                                            <img src="<?= base_url('assets/landing/') ?>images/icon/categoyr-two-icon-4.png" alt="">
                                        </div>
                                        <div class="main-menu__category-content">
                                            <h5>Health & <br> Wellness</h5>
                                            <p>3+ Courses</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="main-menu__category-icon">
                                            <img src="<?= base_url('assets/landing/') ?>images/icon/categoyr-two-icon-5.png" alt="">
                                        </div>
                                        <div class="main-menu__category-content">
                                            <h5>Writing & <br> Communication</h5>
                                            <p>3+ Courses</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="main-menu__category-icon">
                                            <img src="<?= base_url('assets/landing/') ?>images/icon/categoyr-two-icon-6.png" alt="">
                                        </div>
                                        <div class="main-menu__category-content">
                                            <h5>User Research & <br> Analytics</h5>
                                            <p>3+ Courses</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="main-menu__category-icon">
                                            <img src="<?= base_url('assets/landing/') ?>images/icon/categoyr-two-icon-7.png" alt="">
                                        </div>
                                        <div class="main-menu__category-content">
                                            <h5>Digital <br> Marketing</h5>
                                            <p>3+ Courses</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="main-menu__category-icon">
                                            <img src="<?= base_url('assets/landing/') ?>images/icon/categoyr-two-icon-8.png" alt="">
                                        </div>
                                        <div class="main-menu__category-content">
                                            <h5>Lifestyle & <br> Productivity</h5>
                                            <p>3+ Courses</p>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="main-menu__main-menu-box">
                        <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                        <?php require_once($landingParts . 'header/menu.php'); ?>
                    </div>
                    <div class="main-menu__right">
                        <div class="main-menu__search-cart-box">
                            <div class="main-menu__search-box">
                                <a href="#" class="main-menu__search searcher-toggler-box icon-search"></a>
                            </div>
                            <div class="main-menu__cart">
                                <a href="#"><span class="fas fa-shopping-cart"></span></a>
                            </div>
                        </div>
                        <?php if (session()->get('isLoggedIn')): ?>
                            <?php
                            $accountName = (string) (session()->get('username') ?: 'Akun');
                            $accountInitial = strtoupper(substr($accountName, 0, 1));
                            ?>
                            <div class="main-menu__btn-boxes">
                                <div class="landing-account">
                                    <button type="button" class="thm-btn landing-account__toggle" aria-expanded="false">
                                        <span class="landing-account__avatar"><?= esc($accountInitial) ?></span>
                                        <span><?= esc($accountName) ?></span>
                                        <span class="icon-down-arrow"></span>
                                    </button>
                                    <div class="landing-account__menu">
                                        <a href="<?= route_to('profile') ?>">Profil</a>
                                        <a href="<?= route_to('user.arena') ?>">Arena Belajar</a>
                                        <a href="<?= route_to('user.bukuPintar') ?>">Buku Pintar</a>
                                        <a href="<?= route_to('user.regulasi') ?>">Regulasi</a>
                                        <a href="<?= route_to('user.sertifikat') ?>">Sertifikat</a>
                                        <form action="<?= route_to('logout') ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button type="submit">Logout</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="main-menu__btn-boxes">
                                <div class="main-menu__btn-box-1">
                                    <a href="<?= route_to('signin') ?>" class="thm-btn">Login</a>
                                </div>
                                <div class="main-menu__btn-box-2">
                                    <a href="<?= route_to('signup') ?>" class="thm-btn">Register</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<div class="stricky-header stricked-menu main-menu">
    <div class="sticky-header__content"></div><!-- /.sticky-header__content -->
</div><!-- /.stricky-header -->
<script>
    document.querySelectorAll('.landing-account__toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            var menu = toggle.nextElementSibling;
            var isOpen = menu.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });
</script>
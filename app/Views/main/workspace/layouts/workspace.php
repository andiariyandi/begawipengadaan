<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? $title ?? 'Workspace | BegawiPBJ') ?></title>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/main/images/favicons/apple-touch-icon.png') ?>" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/main/images/favicons/favicon-32x32.png') ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/main/images/favicons/favicon-16x16.png') ?>" />
    <link rel="manifest" href="<?= base_url('assets/main/images/favicons/site.webmanifest') ?>" />
    <meta name="description" content="<?= esc($metaDescription ?? 'Ruang Kerja Pembelajaran dan Operasional Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.') ?>" />

    <!-- Google Fonts (FiStudy Ecosystem Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Serif:ital,opsz,wght@0,8..144,100..900;1,8..144,100..900&display=swap" rel="stylesheet">

    <!-- FiStudy Core Stylesheets -->
    <link rel="stylesheet" href="<?= base_url('assets/main/css/bootstrap.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/animate.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/custom-animate.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/swiper.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/font-awesome-all.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/footer.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/page-header.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/courses.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/event.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/style.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/responsive.css') ?>" />

    <!-- Dedicated Workspace Customization Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/main/css/workspace.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/workspace.css') ? filemtime(FCPATH . 'assets/main/css/workspace.css') : '1.0' ?>" />

    <?= $this->renderSection('pageStyles') ?>
</head>
<body class="custom-cursor landing-internal">
    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>

    <div class="page-wrapper">
        <!-- Dedicated FiStudy Workspace Header -->
        <?= $this->include('main/workspace/components/header') ?>

        <!-- FiStudy Sticky Header Anchor Container -->
        <div class="stricky-header stricked-menu main-menu">
            <div class="sticky-header__content"></div>
        </div>

        <!-- Breadcrumb Section -->
        <section class="workspace-breadcrumb">
            <div class="container">
                <h2><?= esc($breadcrumbTitle ?? $pageTitle ?? 'Workspace') ?></h2>
                <ul class="thm-breadcrumb">
                    <li><a href="<?= route_to('public.home') ?>">Beranda</a></li>
                    <li><span>//</span></li>
                    <li><a href="<?= route_to('workspace.home') ?>">Workspace</a></li>
                    <?php if (! empty($breadcrumbTail) && is_array($breadcrumbTail)): ?>
                        <?php foreach ($breadcrumbTail as $tail): ?>
                            <li><span>//</span></li>
                            <li class="<?= empty($tail['url']) ? 'current' : '' ?>">
                                <?php if (! empty($tail['url'])): ?>
                                    <a href="<?= esc($tail['url']) ?>"><?= esc($tail['label']) ?></a>
                                <?php else: ?>
                                    <?= esc($tail['label']) ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </section>

        <!-- Main Workspace Shell: Left Sidebar + Content Area -->
        <section class="workspace-shell">
            <div class="container">
                <div class="row g-4">
                    <!-- Workspace Left Sidebar -->
                    <?= $this->include('main/workspace/components/sidebar', [
                        'userName'  => $userName ?? 'Pengguna',
                        'roleLabel' => $roleLabel ?? 'Pengguna',
                    ]) ?>

                    <!-- Workspace Main Content -->
                    <main class="col-lg-9 col-xl-9 workspace-content">
                        <?= $this->renderSection('content') ?>
                    </main>
                </div>
            </div>
        </section>

        <!-- Shared FiStudy Footer -->
        <?= $this->include('landing/components/footer') ?>
    </div>

    <!-- FiStudy Mobile Navigation & Search Popup Modals -->
    <?= $this->include('landing/components/mobile_menu') ?>
    <?= $this->include('landing/components/search_popup') ?>

    <!-- Scroll to Top -->
    <a href="#" data-target="html" class="scroll-to-target scroll-to-top">
        <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
        <span class="scroll-to-top__text">Kembali ke Atas</span>
    </a>

    <!-- FiStudy Runtime Core Scripts -->
    <script src="<?= base_url('assets/main/js/jquery-3.6.0.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/swiper.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/script.js') ?>"></script>

    <?= $this->renderSection('pageScripts') ?>
</body>
</html>
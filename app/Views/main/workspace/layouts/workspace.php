<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? $title ?? 'Workspace | BegawiPengadaan') ?></title>

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/footer.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/page-header.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/courses.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/event.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/style.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/main/css/responsive.css') ?>" />

    <!-- BegawiPengadaan Landing Design System & Core Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/main/css/landing/landing-core.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/landing/landing-core.css') ? filemtime(FCPATH . 'assets/main/css/landing/landing-core.css') : '2.0' ?>" />

    <!-- Dedicated Workspace Customization Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/main/css/workspace.css') ?>?v=<?= file_exists(FCPATH . 'assets/main/css/workspace.css') ? filemtime(FCPATH . 'assets/main/css/workspace.css') : '2.0' ?>" />

    <?= $this->renderSection('pageStyles') ?>
</head>

<body class="custom-cursor landing-internal workspace-body">
    <script>
        (function () {
            try {
                if (localStorage.getItem('begawi_ws_sidebar_collapsed') === '1') {
                    document.body.classList.add('is-sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>

    <div class="page-wrapper">
        <!-- Unified BegawiPengadaan Navbar (Identical to Landing) -->
        <?= $this->include('landing/components/header') ?>

        <?php $hideSidebar = ! empty($hideSidebar); ?>

        <!-- Breadcrumb Section (FiStudy / Berita & Event Design System) -->
        <section class="workspace-breadcrumb">
            <div class="container workspace-container">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <?php if (! $hideSidebar): ?>
                            <button type="button"
                                    id="wsDesktopSidebarToggle"
                                    class="btn btn-sm btn-white text-navy rounded-3 shadow-sm border d-none d-lg-inline-flex align-items-center justify-content-center ws-sidebar-toggle-btn"
                                    title="Sembunyikan / Tampilkan Navigasi Samping (Ctrl + B)"
                                    aria-label="Toggle Sidebar">
                                <i class="fas fa-columns"></i>
                            </button>
                        <?php endif; ?>
                        <div>
                            <h2><?= esc($breadcrumbTitle ?? $pageTitle ?? 'Workspace') ?></h2>
                            <ul class="thm-breadcrumb">
                                <li><a href="<?= route_to('public.home') ?>"><i class="fas fa-home me-1"></i> Beranda</a></li>
                                <li><span class="sep">/</span></li>
                                <li><a href="<?= route_to('workspace.home') ?>">Workspace</a></li>
                                <?php if (! empty($breadcrumbTail) && is_array($breadcrumbTail)): ?>
                                    <?php foreach ($breadcrumbTail as $tail): ?>
                                        <li><span class="sep">/</span></li>
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
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Spotlight Search Trigger (Ctrl + K) -->
                        <button type="button" 
                                class="btn btn-sm btn-white text-navy rounded-pill shadow-sm border ws-spotlight-btn px-3 py-1.5 fw-semibold" 
                                data-bs-toggle="modal" 
                                data-bs-target="#wsSpotlightModal"
                                title="Pencarian Cepat Spotlight (Ctrl + K)">
                            <i class="fas fa-search me-1 text-primary"></i>
                            <span class="d-none d-sm-inline">Cari Cepat</span>
                            <kbd class="bg-light text-muted border ms-1 px-1.5 py-0.5 rounded font-monospace" style="font-size: 0.7rem;">Ctrl+K</kbd>
                        </button>

                        <?php if (! empty($userName)): ?>
                            <div class="d-none d-md-flex align-items-center">
                                <span class="badge bg-white text-navy px-3 py-2 rounded-pill small fw-bold shadow-sm border">
                                    <i class="fas fa-user-circle me-1.5 text-warning"></i> <?= esc($userName) ?> &middot; <span class="text-muted fw-normal"><?= esc($roleLabel ?? 'Pengguna') ?></span>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Workspace Shell: Left Sidebar + Wide Content Area -->
        <section class="workspace-shell<?= $hideSidebar ? ' workspace-shell--learning-room' : '' ?>" id="wsMainShell">
            <div class="container workspace-container">
                <div class="row g-4 workspace-layout-row">
                    <?php if (! $hideSidebar): ?>
                        <!-- Workspace Left Sidebar -->
                        <?= $this->include('main/workspace/components/sidebar', [
                            'userName'  => $userName ?? 'Pengguna',
                            'roleLabel' => $roleLabel ?? 'Pengguna',
                        ]) ?>
                    <?php endif; ?>

                    <!-- Workspace Main Content -->
                    <main class="<?= $hideSidebar ? 'col-12' : 'col-lg-9 col-xl-9' ?> workspace-content">
                        <?php if (!isset($GLOBALS['__begawi_workspace_flash_rendered'])): ?>
                            <?php if (session()->getFlashdata('message') || session()->getFlashdata('success')): ?>
                                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?= esc(session()->getFlashdata('message') ?: session()->getFlashdata('success')) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?= esc(session()->getFlashdata('error')) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php $GLOBALS['__begawi_workspace_flash_rendered'] = true; ?>
                        <?php endif; ?>
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
    <?= $this->include('landing/components/satisfaction_survey_modal') ?>
    <?= $this->include('main/workspace/components/spotlight_modal') ?>

    <!-- Scroll to Top -->
    <a href="#" data-target="html" class="scroll-to-target scroll-to-top">
        <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
        <span class="scroll-to-top__text">Kembali ke Atas</span>
    </a>

    <!-- FiStudy Runtime Core Scripts -->
    <script src="<?= base_url('assets/main/js/jquery-3.6.0.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.appear.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/swiper.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.magnific-popup.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/wow.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.nice-select.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/gsap/gsap.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/gsap/ScrollTrigger.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/gsap/SplitText.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/script.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/workspace-pagination.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('wsDesktopSidebarToggle');
            function toggleWorkspaceSidebar() {
                var isCollapsed = document.body.classList.toggle('is-sidebar-collapsed');
                try {
                    localStorage.setItem('begawi_ws_sidebar_collapsed', isCollapsed ? '1' : '0');
                } catch (e) {}
            }
            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleWorkspaceSidebar);
            }
            document.addEventListener('keydown', function (ev) {
                if ((ev.ctrlKey || ev.metaKey) && (ev.key === 'b' || ev.key === 'B')) {
                    if (toggleBtn) {
                        ev.preventDefault();
                        toggleWorkspaceSidebar();
                    }
                }
            });
        });
    </script>

    <?= $this->renderSection('pageScripts') ?>
</body>

</html>

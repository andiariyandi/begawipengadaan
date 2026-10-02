<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? $title ?? 'Workspace | BegawiPBJ') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/main/images/favicons/favicon-32x32.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/font-awesome-all.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/footer.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/module-css/page-header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/main/css/responsive.css') ?>">
    <style>
        .workspace-shell { background: #f5f7fb; min-height: 520px; padding: 32px 0 72px; }
        .workspace-sidebar { background: #fff; border: 1px solid #e6ebf2; border-radius: 16px; padding: 22px; box-shadow: 0 8px 24px rgba(8, 47, 92, .06); }
        .workspace-sidebar__identity { border-bottom: 1px solid #e6ebf2; margin-bottom: 20px; padding-bottom: 18px; }
        .workspace-sidebar__identity strong, .workspace-sidebar__identity span { display: block; }
        .workspace-sidebar__identity strong { color: #082f5c; font-size: 19px; margin: 5px 0; }
        .workspace-sidebar__identity span:last-child { color: #6b778c; font-size: 13px; }
        .workspace-sidebar__eyebrow, .workspace-sidebar__group h2 { color: #0c5aa6; font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .workspace-sidebar__group { border-bottom: 1px solid #edf0f5; margin-bottom: 18px; padding-bottom: 12px; }
        .workspace-sidebar__group:last-child { border-bottom: 0; margin-bottom: 0; }
        .workspace-sidebar__group h2 { color: #8390a3; margin-bottom: 8px; }
        .workspace-sidebar__link { align-items: center; border-radius: 8px; color: #42526b; display: flex; font-size: 14px; justify-content: space-between; padding: 9px 11px; text-decoration: none; }
        .workspace-sidebar__link:hover, .workspace-sidebar__link.is-active { background: #eef6ff; color: #0c5aa6; }
        .workspace-sidebar__link.is-disabled { color: #a3adba; cursor: default; }
        .workspace-sidebar__link small { color: #b0b7c2; font-size: 10px; }
        .workspace-content { min-height: 420px; }
        .workspace-breadcrumb { background: #f5f7fb; border-bottom: 1px solid #e6ebf2; padding: 16px 0 14px; }
        @media (max-width: 991px) {
            .workspace-shell { padding-top: 24px; }
            .workspace-sidebar { margin-bottom: 4px; }
        }
    </style>
    <?= $this->renderSection('pageStyles') ?>
</head>
<body class="custom-cursor landing-internal">
    <div class="page-wrapper">
        <?= $this->include('landing/components/header') ?>
        <div class="stricky-header stricked-menu main-menu"><div class="sticky-header__content"></div></div>
        <section class="workspace-breadcrumb">
            <div class="container">
                <a class="small text-muted text-decoration-none" href="<?= route_to('public.home') ?>">Beranda</a>
                <span class="small text-muted"> / Workspace</span>
            </div>
        </section>
        <section class="workspace-shell">
            <div class="container">
                <div class="row g-4">
                    <?= $this->include('main/workspace/components/sidebar', [
                        'userName' => $userName ?? 'Pengguna',
                        'roleLabel' => $roleLabel ?? 'Pengguna',
                    ]) ?>
                    <main class="col-lg-9 col-xl-9 workspace-content">
                        <?= $this->renderSection('content') ?>
                    </main>
                </div>
            </div>
        </section>
        <?= $this->include('landing/components/footer') ?>
    </div>
    <?= $this->include('landing/components/mobile_menu') ?>
    <?= $this->include('landing/components/search_popup') ?>
    <script src="<?= base_url('assets/main/js/jquery-3.6.0.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/script.js') ?>"></script>
    <?= $this->renderSection('pageScripts') ?>
</body>
</html>

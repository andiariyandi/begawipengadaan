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
        :root {
            --ws-primary: #ff6b35;
            --ws-primary-dark: #e85a26;
            --ws-secondary: #082f5c;
            --ws-text: #42526b;
            --ws-muted: #8390a3;
            --ws-bg: #f5f7fb;
            --ws-border: #e6ebf2;
            --ws-card: #ffffff;
            --ws-accent-bg: #fff8f3;
        }
        .workspace-shell { background: var(--ws-bg); padding: 40px 0 72px; min-height: 520px; }
        .workspace-shell > .container > .row { display: flex; flex-wrap: wrap; align-items: flex-start; }
        .workspace-shell > .container > .row > aside,
        .workspace-shell > .container > .row > main { min-width: 0; }
        .workspace-breadcrumb { background: var(--ws-secondary); padding: 28px 0; color: #ffffff; }
        .workspace-breadcrumb h2 { color: #ffffff; font-size: 26px; font-weight: 700; margin: 0; }
        .workspace-breadcrumb .thm-breadcrumb { margin: 10px 0 0; padding: 0; list-style: none; display: flex; gap: 8px; font-size: 14px; flex-wrap: wrap; }
        .workspace-breadcrumb .thm-breadcrumb a { color: rgba(255,255,255,.75); text-decoration: none; }
        .workspace-breadcrumb .thm-breadcrumb li span { color: rgba(255,255,255,.55); }
        .workspace-breadcrumb .thm-breadcrumb li.current { color: var(--ws-primary); }

        aside.col-lg-3.col-xl-3.workspace-sidebar-wrap,
        aside.workspace-sidebar-wrap {
            flex: 0 0 25%;
            max-width: 25%;
            padding-left: 0;
            padding-right: 12px;
        }
        main.col-lg-9.col-xl-9.workspace-content,
        main.workspace-content {
            flex: 0 0 75%;
            max-width: 75%;
            padding-left: 12px;
            padding-right: 0;
        }

        .workspace-sidebar {
            position: relative;
            background: var(--ws-card);
            border: 1px solid var(--ws-border);
            border-radius: 12px;
            padding: 24px 20px;
            box-shadow: 0 8px 28px rgba(8, 47, 92, .06);
            display: flex;
            flex-direction: column;
            gap: 0;
            width: 100%;
            max-width: none;
        }
        .workspace-sidebar nav,
        .workspace-sidebar nav ul { list-style: none; padding: 0; margin: 0; }
        .workspace-sidebar__identity {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 18px;
            margin-bottom: 18px;
            border-bottom: 1px solid var(--ws-border);
            min-width: 0;
        }
        .workspace-sidebar__identity .avatar {
            flex: 0 0 44px;
            width: 44px; height: 44px;
            border-radius: 50%;
            background: var(--ws-primary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 17px;
        }
        .workspace-sidebar__identity > div { flex: 1 1 auto; min-width: 0; }
        .workspace-sidebar__identity strong {
            color: var(--ws-secondary);
            font-size: 15px;
            font-weight: 700;
            line-height: 1.3;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .workspace-sidebar__identity span {
            color: var(--ws-muted);
            font-size: 12px;
            line-height: 1.3;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .workspace-sidebar__group {
            margin: 0 0 16px;
            padding: 0 0 14px;
            border-bottom: 1px solid #edf0f5;
        }
        .workspace-sidebar__group:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: 0;
        }
        .workspace-sidebar__group > h2 {
            color: var(--ws-secondary);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin: 0 0 10px;
            line-height: 1.4;
        }
        .workspace-sidebar__group > nav > ul,
        .workspace-sidebar__group > ul { margin: 0; padding: 0; list-style: none; }

        .workspace-sidebar__subgroup {
            margin: 0 0 10px;
            padding: 0 0 8px;
            border-left: 1px dashed var(--ws-border);
            padding-left: 10px;
        }
        .workspace-sidebar__subgroup:last-child { margin-bottom: 0; padding-bottom: 0; border-left-color: transparent; padding-left: 10px; }
        .workspace-sidebar__subgroup > h3 {
            color: var(--ws-text);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: none;
            margin: 0 0 6px;
            line-height: 1.3;
        }

        .workspace-sidebar__link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            padding: 9px 12px;
            margin: 0 0 3px;
            border-radius: 8px;
            border-left: 3px solid transparent;
            color: var(--ws-text);
            font-size: 14px;
            line-height: 1.3;
            text-decoration: none;
            background: transparent;
            transition: background-color .15s, color .15s, border-color .15s;
            box-sizing: border-box;
        }
        .workspace-sidebar__link--sub {
            padding: 7px 10px;
            font-size: 13px;
            margin: 0 0 2px;
            border-left-width: 2px;
        }
        .workspace-sidebar__link:hover {
            background: var(--ws-accent-bg);
            color: var(--ws-primary-dark);
        }
        .workspace-sidebar__link.is-active {
            background: var(--ws-accent-bg);
            color: var(--ws-primary);
            border-left-color: var(--ws-primary);
            font-weight: 600;
        }
        .workspace-sidebar__link.is-disabled {
            color: #a3adba;
            cursor: default;
        }
        .workspace-sidebar__link.is-disabled small {
            color: #b0b7c2;
            font-size: 11px;
        }

        .workspace-content { min-height: 420px; }
        .workspace-content .bg-white { border-radius: 12px; }

        @media (max-width: 1199px) {
            aside.workspace-sidebar-wrap { flex: 0 0 33.33%; max-width: 33.33%; }
            main.workspace-content { flex: 0 0 66.67%; max-width: 66.67%; }
        }
        @media (max-width: 991px) {
            .workspace-shell { padding-top: 24px; }
            .workspace-shell > .container > .row { display: block; }
            aside.workspace-sidebar-wrap,
            main.workspace-content { flex: 0 0 100%; max-width: 100%; padding: 0; }
            .workspace-sidebar { margin-bottom: 16px; }
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
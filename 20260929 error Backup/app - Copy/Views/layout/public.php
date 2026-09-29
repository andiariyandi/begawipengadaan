<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Begawipbj') ?></title>
    <meta name="description" content="Begawipbj - Ekosistem digital Pengadaan Barang/Jasa.">
    <link rel="icon" type="image/png" href="<?= base_url('assets/landing/images/favicons/favicon-32x32.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/animate.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/custom-animate.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/swiper.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/font-awesome-all.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/jarallax.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/jquery.magnific-popup.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/odometer.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/flaticon.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/owl.carousel.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/owl.theme.default.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/nice-select.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/aos.css') ?>">
    <?php foreach (['banner', 'slider', 'footer', 'sliding-text', 'category', 'about', 'courses', 'why-choose', 'live-class', 'video-one', 'blog', 'counter', 'team', 'newsletter', 'testimonial', 'contact', 'courses-search', 'pricing', 'process', 'event', 'page-header', 'error', 'become-a-teacher', 'shop', 'coming-soon', 'faq', 'gallery'] as $module): ?>
        <link rel="stylesheet" href="<?= base_url('assets/landing/css/module-css/' . $module . '.css') ?>">
    <?php endforeach; ?>
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/landing/css/responsive.css') ?>">
    <style>
        :root { --begawi-blue: #155eef; --begawi-ink: #14213d; }
        body { color: var(--begawi-ink); }
        .public-header { background:#fff; border-bottom:1px solid #e8edf5; position:relative; z-index:10; }
        .public-header__inner { min-height:82px; display:flex; align-items:center; justify-content:space-between; gap:24px; }
        .public-logo { color:var(--begawi-ink); font-size:25px; font-weight:800; letter-spacing:-.6px; }
        .public-logo span { color:var(--begawi-blue); }
        .public-nav { display:flex; align-items:center; gap:22px; list-style:none; margin:0; padding:0; }
        .public-nav a { color:var(--begawi-ink); font-size:14px; font-weight:600; }
        .public-nav a:hover { color:var(--begawi-blue); }
        .public-main { min-height:65vh; padding:55px 0 85px; background:#f8fbff; }
        .public-heading { margin-bottom:30px; }
        .public-heading h1 { font-size:38px; font-weight:800; margin-bottom:10px; }
        .public-heading p { color:#68758a; }
        .public-card { height:100%; background:#fff; border:1px solid #e8edf5; border-radius:16px; padding:26px; box-shadow:0 12px 35px rgba(30,65,120,.06); }
        .public-card h2, .public-card h3, .public-card h4 { font-weight:750; }
        .public-card a { color:var(--begawi-ink); }
        .public-card a:hover { color:var(--begawi-blue); }
        .public-footer { background:#101c35; color:#c8d2e5; padding:42px 0; }
        .public-footer a { color:#fff; }
        @media (max-width: 991px) { .public-nav { display:none; } }
    </style>
</head>
<body>
<header class="public-header">
    <div class="container">
        <div class="public-header__inner">
            <a class="public-logo" href="<?= route_to('landing') ?>">begawi<span>pbj</span></a>
            <nav>
                <ul class="public-nav">
                    <li><a href="<?= route_to('landing') ?>">Beranda</a></li>
                    <li><a href="<?= route_to('knowledge.articles.browse') ?>">Artikel</a></li>
                    <li><a href="<?= route_to('regulation.browse') ?>">Regulasi</a></li>
                    <li><a href="<?= route_to('knowledge.caseStudies.browse') ?>">Studi Kasus</a></li>
                    <li><a href="<?= route_to('knowledge.faq') ?>">FAQ</a></li>
                    <li><a href="<?= route_to('knowledge.templates.browse') ?>">Template</a></li>
                </ul>
            </nav>
            <div class="d-flex gap-2">
                <?php if (session()->get('isLoggedIn')): ?>
                    <a href="<?= route_to('dashboard.pbj') ?>" class="btn btn-outline-primary btn-sm">Dashboard</a>
                <?php else: ?>
                    <a href="<?= route_to('signin') ?>" class="btn btn-outline-primary btn-sm">Masuk</a>
                    <a href="<?= route_to('signup') ?>" class="btn btn-primary btn-sm">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<main class="public-main">
    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>
</main>
<footer class="public-footer">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div><strong class="text-white">begawipbj</strong><br><span>Ekosistem digital Pengadaan Barang/Jasa.</span></div>
        <div><a href="<?= route_to('landing') ?>">Beranda</a> · <a href="<?= route_to('knowledge.articles.browse') ?>">Artikel</a> · <a href="<?= route_to('regulation.browse') ?>">Regulasi</a></div>
    </div>
</footer>
<script src="<?= base_url('assets/landing/js/jquery-3.6.0.min.js') ?>"></script>
<script src="<?= base_url('assets/landing/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/landing/js/gsap/gsap.js') ?>"></script>
<script src="<?= base_url('assets/landing/js/gsap/ScrollTrigger.js') ?>"></script>
<script src="<?= base_url('assets/landing/js/script.js') ?>"></script>
<?= $this->renderSection('script') ?>
</body>
</html>

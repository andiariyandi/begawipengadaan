<!doctype html>
<html class="no-js" lang="id">
<head>
    <?= $this->include('main/components/head') ?>
</head>
<body>
    <div id="loading"><div id="loading-center"><div id="loading-center-absolute"><div class="object" id="object_one"></div><div class="object" id="object_two"></div><div class="object" id="object_three"></div><div class="object" id="object_four"></div></div></div></div>

    <?= $this->include('main/components/workspace_header') ?>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('main/components/workspace_footer') ?>

    <script src="<?= base_url('assets/main/assets/js/vendor/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/vendor/waypoints.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/jquery.meanmenu.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/owl.carousel.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/swiper-bundle.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/jquery.fancybox.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/isotope.pkgd.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/parallax.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/backToTop.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/jquery.counterup.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/slick.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/ajax-form.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/wow.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/imagesloaded.pkgd.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/assets/js/main.js') ?>"></script>
</body>
</html>
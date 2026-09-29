<!doctype html>
<html class="no-js" lang="id">

<head>
    <?= $this->include('landing/components/head') ?>
</head>

<body>
    <div id="loading">
        <div id="loading-center">
            <div id="loading-center-absolute">
                <div class="object" id="object_one"></div>
                <div class="object" id="object_two"></div>
                <div class="object" id="object_three"></div>
                <div class="object" id="object_four"></div>
            </div>
        </div>
    </div>

    <?= $this->include('landing/components/header') ?>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('landing/components/footer') ?>

    <script src="<?= base_url('assets/main/js/vendor/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/vendor/waypoints.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.meanmenu.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/owl.carousel.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/swiper-bundle.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.fancybox.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/isotope.pkgd.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/parallax.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/backToTop.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/jquery.counterup.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/slick.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/ajax-form.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/wow.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/imagesloaded.pkgd.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/main.js') ?>"></script>
</body>

</html>
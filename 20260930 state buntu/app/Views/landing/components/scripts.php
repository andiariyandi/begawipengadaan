<?php $pageScripts = $pageScripts ?? []; ?>
<script src="<?= base_url('assets/main/js/jquery-3.6.0.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/jarallax.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/jquery.ajaxchimp.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/jquery.appear.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/jquery.circle-progress.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/jquery.magnific-popup.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/nouislider.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/odometer.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/swiper.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/tiny-slider.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/wnumb.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/wow.js') ?>"></script>
<script src="<?= base_url('assets/main/js/isotope.js') ?>"></script>
<script src="<?= base_url('assets/main/js/countdown.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/owl.carousel.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/jquery-ui.js') ?>"></script>
<script src="<?= base_url('assets/main/js/tilt.jquery.js') ?>"></script>
<script src="<?= base_url('assets/main/js/jquery.circleType.js') ?>"></script>
<script src="<?= base_url('assets/main/js/jquery.lettering.min.js') ?>"></script>
<script src="<?= base_url('assets/main/js/custom.js') ?>"></script>

<?php foreach ($pageScripts as $script): ?>
    <script src="<?= base_url('assets/main/js/' . esc($script)) ?>"></script>
<?php endforeach; ?>
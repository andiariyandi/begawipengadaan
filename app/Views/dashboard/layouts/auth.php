<!doctype html>
<html lang="id">

<head>
    <?= $this->include('dashboard/components/head') ?>
</head>

<body>
    <section class="auth bg-base d-flex flex-wrap">
        <?= $this->renderSection('content') ?>
    </section>

    <script src="<?= base_url('assets/dashboard/js/lib/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/app.js') ?>"></script>
</body>

</html>
<!doctype html>
<html class="no-js" lang="id">

<head>
    <?= $this->include('landing/components/head') ?>
</head>

<body>
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <script src="<?= base_url('assets/main/js/vendor/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/main/js/main.js') ?>"></script>
</body>

</html>
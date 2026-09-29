<!doctype html>
<html lang="id">
<head>
    <?= $this->include('dashboard/components/head') ?>
</head>
<body>
    <main class="min-h-100vh d-flex align-items-center justify-content-center p-24">
        <?= $this->renderSection('content') ?>
    </main>

    <script src="<?= base_url('assets/dashboard/assets/js/lib/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/assets/js/lib/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/assets/js/lib/iconify-icon.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/assets/js/app.js') ?>"></script>
</body>
</html>
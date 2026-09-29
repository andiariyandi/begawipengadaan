<!doctype html>
<html lang="id">

<head>
    <?= $this->include('dashboard/components/head') ?>
</head>

<body>
    <?= $this->include('dashboard/components/sidebar') ?>

    <main class="dashboard-main">
        <?= $this->include('dashboard/components/topbar') ?>

        <div class="dashboard-main-body">
            <?= $this->renderSection('content') ?>
        </div>

        <?= $this->include('dashboard/components/footer') ?>
    </main>

    <script src="<?= base_url('assets/dashboard/js/lib/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/apexcharts.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/dataTables.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/full-calendar.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/jquery-jvectormap-2.0.5.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/jquery-jvectormap-world-mill-en.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/slick.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/app.js') ?>"></script>
</body>

</html>
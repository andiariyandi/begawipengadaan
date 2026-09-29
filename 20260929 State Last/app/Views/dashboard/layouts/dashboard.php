<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <?= $this->include('dashboard/components/head') ?>
</head>

<body>
    <?= $this->include('dashboard/components/sidebar') ?>

    <main class="dashboard-main">
        <?= $this->include('dashboard/components/header') ?>

        <div class="dashboard-main-body">
            <?= $this->renderSection('content') ?>
        </div>

        <?= $this->include('dashboard/components/footer') ?>
    </main>

    <?= $this->include('dashboard/components/scripts') ?>
</body>

</html>
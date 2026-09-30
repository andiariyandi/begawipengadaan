<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <?= $this->include('dashboard/components/head') ?>
</head>
<body>
    <?= $this->include('dashboard/components/theme_customization') ?>

    <?= $this->renderSection('content') ?>

    <?= $this->include('dashboard/components/scripts') ?>
</body>
</html>

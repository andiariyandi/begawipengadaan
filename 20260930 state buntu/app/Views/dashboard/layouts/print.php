<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'BegawiPBJ') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/css/style.css') ?>">
</head>
<body>
    <?= $this->renderSection('content') ?>
</body>
</html>

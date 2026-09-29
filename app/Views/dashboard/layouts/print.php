<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle ?? 'BegawiPBJ') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/assets/css/lib/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/assets/css/style.css') ?>">
</head>
<body class="bg-white">
    <main class="p-24">
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>
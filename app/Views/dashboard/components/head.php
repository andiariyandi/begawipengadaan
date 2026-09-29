<?php
$pageTitle = $pageTitle ?? 'BegawiPBJ';
$pageStyles = $pageStyles ?? [];
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($pageTitle) ?></title>
<link rel="icon" type="image/png" href="<?= base_url('assets/dashboard/images/favicon.png') ?>" sizes="16x16">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/remixicon.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/apexcharts.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/dataTables.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/editor-katex.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/editor.atom-one-dark.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/editor.quill.snow.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/flatpickr.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/full-calendar.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/jquery-jvectormap-2.0.5.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/magnific-popup.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/slick.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/prism.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/file-upload.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/audioplayer.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/style.css') ?>">

<?php foreach ($pageStyles as $style): ?>
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/' . esc($style)) ?>">
<?php endforeach; ?>

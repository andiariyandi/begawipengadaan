<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<?= view($innerView ?? 'main/workspace/pages/home', get_defined_vars()) ?>
<?= $this->endSection() ?>
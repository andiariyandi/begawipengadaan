<!DOCTYPE html>
<html lang="id">
<head>
    <?= $this->include('landing/components/head') ?>
</head>
<body class="custom-cursor">
    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>

    <div class="loader js-preloader">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <?= $this->renderSection('content') ?>

    <?= $this->include('landing/components/scripts') ?>
</body>
</html>

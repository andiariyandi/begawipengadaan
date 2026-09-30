<!DOCTYPE html>
<html lang="id">
<head>
    <?= $this->include('main/components/head') ?>
</head>
<body class="custom-cursor">
    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>
    <div class="loader js-preloader"><div></div><div></div><div></div></div>

    <?= $this->renderSection('content') ?>

    <?= view('landing/components/scripts', ['pageScripts' => $pageScripts ?? []]) ?>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <?= $this->include('main/components/head') ?>
</head>
<body class="custom-cursor">
    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>
    <div class="loader js-preloader"><div></div><div></div><div></div></div>

    <div class="page-wrapper">
        <?= $this->include('main/components/workspace_header') ?>

        <?= $this->renderSection('content') ?>

        <?= $this->include('main/components/workspace_footer') ?>
    </div>

    <?= view('landing/components/scripts', ['pageScripts' => $pageScripts ?? []]) ?>
</body>
</html>

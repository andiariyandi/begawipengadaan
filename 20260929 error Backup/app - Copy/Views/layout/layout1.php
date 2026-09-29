<?php
$isAdminLayout = (bool) array_intersect(session()->get('roles') ?? [], ['super_administrator', 'administrator']);
if (! $isAdminLayout):
    $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR;
    $head_title = $title ?? 'Begawi PBJ';
    require_once($landingParts . 'layout/top-layout.php');
    require_once($landingParts . 'header/header.php');
?>
<main class="landing-user-area">
    <section class="page-header">
        <div class="page-header__bg" style="background-image: url(<?= base_url('assets/landing/images/backgrounds/counter-one-bg.jpg') ?>);"></div>
        <div class="container"><h2><?= esc($title ?? 'Area Pengguna') ?></h2></div>
    </section>
    <section class="landing-user-content"><div class="container"><?= $this->renderSection('content') ?></div></section>
</main>
<?php
    require_once($landingParts . 'footer/footer.php');
    require_once($landingParts . 'layout/bottom-layout.php');
    return;
endif;
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">

    <?= $this->include('partials/head') ?> 

<body>
    <!-- ..::  header area start ::.. -->
    <?= $this->include('partials/sidebar') ?> 
    <!-- ..::  header area end ::.. -->

    <main class="dashboard-main">      
        <!-- ..::  navbar start ::.. -->
        <?= $this->include('partials/navbar') ?>
        <!-- ..::  navbar end ::.. -->

        <div class="dashboard-main-body">
            <!-- ..::  breadcrumb  start ::.. -->
            <?= $this->include('partials/breadcrumb') ?>
            <!-- ..::  header area end ::.. -->    
            
            <?= $this->renderSection('content') ?> <!-- Main content from the page -->
        </div>      

        <?= $this->include('partials/footer') ?>

    </main>
    <?= $this->include('partials/scripts') ?> <!-- Scripts -->
    <!-- This is a comment about fetching the 'script' block -->
    <?= $this->renderSection('script') ?> <!-- Page-specific scripts -->
</body>
</html>

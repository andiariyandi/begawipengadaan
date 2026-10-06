<?php
$head_title = $head_title ?? ($title ?? 'Begawi PBJ');
$page_title = $title ?? 'Area Pengguna';
$landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR;
require_once($landingParts . 'layout/top-layout.php');
require_once($landingParts . 'header/header.php');
?>
<main class="landing-user-area">
    <?php require_once($landingParts . 'page_header.php'); ?>
    <section class="landing-user-content">
        <div class="container">
            <?php if ($message = session()->getFlashdata('message')): ?>
                <div class="landing-alert landing-alert--success"><?= esc($message) ?></div>
            <?php endif; ?>
            <?php if ($error = session()->getFlashdata('error')): ?>
                <div class="landing-alert landing-alert--error"><?= esc($error) ?></div>
            <?php endif; ?>
            <?= $this->renderSection('content') ?>
        </div>
    </section>
</main>
<?php
require_once($landingParts . 'footer/footer.php');
require_once($landingParts . 'layout/bottom-layout.php');
?>

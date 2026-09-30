<?php
$tagline = $tagline ?? 'BegawiPBJ';
$title = $title ?? '';
$description = $description ?? null;
$alignment = $alignment ?? 'left';
?>
<div class="section-title <?= $alignment === 'center' ? 'text-center' : 'text-left' ?>">
    <div class="section-title__tagline-box"><span class="section-title__tagline"><?= esc($tagline) ?></span></div>
    <h2 class="section-title__title"><?= esc($title) ?></h2>
    <?php if ($description): ?><p class="mt-3 text-muted"><?= esc($description) ?></p><?php endif; ?>
</div>

<?php
$title = $title ?? 'Belum ada data';
$description = $description ?? 'Data yang Anda cari belum tersedia saat ini.';
$actionUrl = $actionUrl ?? null;
$actionLabel = $actionLabel ?? 'Kembali';
?>
<div class="text-center py-5"><img src="<?= base_url('assets/main/images/resources/error-page-img1.png') ?>" alt="" style="width:160px;max-width:100%;"><h3 class="mt-4 mb-3"><?= esc($title) ?></h3><p class="text-muted mx-auto mb-4" style="max-width:560px;"><?= esc($description) ?></p><?php if ($actionUrl): ?><a href="<?= esc($actionUrl) ?>" class="thm-btn"><span class="icon-angles-right"></span><?= esc($actionLabel) ?></a><?php endif; ?></div>

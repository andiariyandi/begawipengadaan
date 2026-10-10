<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 * @var string $pagerGroup
 */
$pager->setSurroundCount(2);

$req = service('request');
$currentGet = $req ? $req->getGet() : ($_GET ?? []);

$buildUri = static function (?string $uri) use ($currentGet): string {
    if (empty($uri)) {
        return '#';
    }
    $parsed = parse_url($uri);
    $query = [];
    if (!empty($parsed['query'])) {
        parse_str($parsed['query'], $query);
    }
    $merged = array_merge($currentGet, $query);
    $queryString = http_build_query($merged);
    return ($parsed['path'] ?? '') . ($queryString !== '' ? '?' . $queryString : '');
};

$start = $pager->getPerPageStart() ?? 1;
$end = $pager->getPerPageEnd() ?? ($pager->getTotal() ?? 0);
$total = $pager->getTotal() ?? 0;
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-20">
    <span class="text-sm text-secondary-light">
        Menampilkan <?= number_format($start) ?> - <?= number_format($end) ?> dari <?= number_format($total) ?> data
    </span>
    <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center mb-0">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="<?= esc($buildUri($pager->getFirst())) ?>" aria-label="First" title="Halaman Pertama">
                    <iconify-icon icon="ep:d-arrow-left"></iconify-icon>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="<?= esc($buildUri($pager->getPrevious())) ?>" aria-label="Previous" title="Halaman Sebelumnya">
                    <iconify-icon icon="solar:alt-arrow-left-outline"></iconify-icon>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link <?= $link['active'] ? 'bg-primary-600 text-white' : 'bg-neutral-200 text-secondary-light' ?> fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="<?= esc($buildUri($link['uri'])) ?>">
                    <?= esc($link['title']) ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="<?= esc($buildUri($pager->getNext())) ?>" aria-label="Next" title="Halaman Selanjutnya">
                    <iconify-icon icon="solar:alt-arrow-right-outline"></iconify-icon>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="<?= esc($buildUri($pager->getLast())) ?>" aria-label="Last" title="Halaman Terakhir">
                    <iconify-icon icon="ep:d-arrow-right"></iconify-icon>
                </a>
            </li>
        <?php endif ?>
    </ul>
</div>

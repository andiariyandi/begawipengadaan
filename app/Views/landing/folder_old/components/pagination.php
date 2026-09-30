<?php
$currentPage = max(1, (int) ($currentPage ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$baseUrl = $baseUrl ?? current_url();
$queryParameter = $queryParameter ?? 'page';
$buildUrl = static function (int $page) use ($baseUrl, $queryParameter): string {
    return $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . http_build_query([$queryParameter => $page]);
};
?>
<?php if ($totalPages > 1): ?>
<div class="pagination-wrapper text-center mt-5"><ul class="pg-pagination list-unstyled"><li class="prev <?= $currentPage <= 1 ? 'disabled' : '' ?>"><a href="<?= $currentPage <= 1 ? '#' : esc($buildUrl($currentPage - 1)) ?>"><i class="fa fa-angle-left"></i></a></li><?php for ($page = 1; $page <= $totalPages; $page++): ?><li class="<?= $page === $currentPage ? 'active' : '' ?>"><a href="<?= esc($buildUrl($page)) ?>"><?= $page ?></a></li><?php endfor; ?><li class="next <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"><a href="<?= $currentPage >= $totalPages ? '#' : esc($buildUrl($currentPage + 1)) ?>"><i class="fa fa-angle-right"></i></a></li></ul></div>
<?php endif; ?>

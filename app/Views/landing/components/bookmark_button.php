<?php
/**
 * @var string $type   bookmarkable_type (e.g. 'knowledge_article')
 * @var int    $id     bookmarkable_id
 * @var string|null $title judul resource (untuk aria-label)
 * @var bool   $isBookmarked state awal (opsional; default auto-detect untuk user login)
 * @var bool   $isLoggedIn   apakah user login
 * @var string $size        'sm' | 'md' (default md)
 *
 * Dipakai dengan:
 *   echo view('landing/components/bookmark_button', [
 *       'type' => 'knowledge_article',
 *       'id'   => 42,
 *       'title'=> 'Prinsip PBJ',
 *   ]);
 */

$type     = $type ?? 'news';
$id       = (int) ($id ?? 0);
$title    = $title ?? null;
$size     = $size ?? 'md';
$sizeCls  = $size === 'sm' ? 'btn-sm' : '';

$isLoggedIn = $isLoggedIn ?? (session()->get('isLoggedIn') === true);
$userId = (int) session()->get('user_id');
$isBookmarked = $isBookmarked ?? null;
if ($isBookmarked === null && $isLoggedIn && $userId > 0 && $id > 0) {
    $isBookmarked = (new \App\Models\Knowledge\BookmarkModel())->exists($userId, $type, $id);
}

$ariaLabel = $isBookmarked ? 'Hapus bookmark' : 'Simpan bookmark';
if ($title !== null) {
    $ariaLabel .= ': ' . $title;
}
$iconClass = $isBookmarked ? 'solar:bookmark-bold text-warning-600' : 'solar:bookmark-outline';
$btnClass  = $isBookmarked ? 'btn-warning-600' : 'btn-outline-neutral-300';
?>
<?php if ($isLoggedIn): ?>
    <form method="post" action="<?= route_to('public.bookmark.toggle') ?>" class="d-inline m-0">
        <?= csrf_field() ?>
        <input type="hidden" name="type" value="<?= esc($type) ?>">
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <button type="submit" class="btn <?= esc($btnClass) ?> radius-8 <?= esc($sizeCls) ?>" aria-label="<?= esc($ariaLabel) ?>" title="<?= esc($ariaLabel) ?>">
            <iconify-icon icon="<?= esc($iconClass) ?>" class="me-4"></iconify-icon>
            <?= $isBookmarked ? 'Tersimpan' : 'Simpan' ?>
        </button>
    </form>
<?php else: ?>
    <a href="<?= route_to('dashboard.auth.login') ?>" class="btn btn-outline-neutral-300 radius-8 <?= esc($sizeCls) ?>" aria-label="<?= esc($ariaLabel) ?>" title="<?= esc($ariaLabel) ?> (login diperlukan)" onclick="return confirm('Silakan masuk untuk menyimpan bookmark.');">
        <iconify-icon icon="<?= esc($iconClass) ?>" class="me-4"></iconify-icon>
        Simpan
    </a>
<?php endif; ?>
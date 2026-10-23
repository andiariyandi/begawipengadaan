<?php
/**
 * @var string      $type        bookmarkable_type ('program', 'course', 'learning_class', 'knowledge_article', etc.)
 * @var int         $id          bookmarkable_id
 * @var string|null $title       Judul resource (untuk aria-label / title)
 * @var bool|null   $isBookmarked Status bookmark (opsional; auto-detect jika null)
 * @var bool|null   $isLoggedIn  Status login (opsional; default deteksi sesi)
 * @var string      $size        'sm' | 'md' (default md)
 * @var bool        $iconOnly    Apakah hanya menampilkan ikon saja (default false)
 * @var string      $btnClass    Tambahan CSS class untuk tombol
 */

$type      = $type ?? 'news';
$id        = (int) ($id ?? 0);
$title     = $title ?? null;
$size      = $size ?? 'md';
$iconOnly  = (bool) ($iconOnly ?? false);
$extraCls  = $btnClass ?? '';

$isLoggedIn = $isLoggedIn ?? (session()->get('isLoggedIn') === true);
$userId     = (int) session()->get('user_id');

// Sesuai kesepakatan spesifikasi:
// Landing page adalah tempat publik (dapat diakses tanpa login).
// Fitur bookmark (save) HANYA muncul jika pengguna dalam status LOGIN.
if (! $isLoggedIn || $userId < 1 || $id < 1) {
    return;
}

if (! isset($isBookmarked)) {
    try {
        $isBookmarked = (new \App\Models\Knowledge\BookmarkModel())->exists($userId, $type, $id);
    } catch (\Throwable $e) {
        $isBookmarked = false;
    }
}

$ariaLabel = $isBookmarked ? 'Hapus dari Tersimpan' : 'Simpan ke Bookmark';
if ($title !== null) {
    $ariaLabel .= ': ' . $title;
}

$btnSizeClass = $size === 'sm' ? 'arena-bookmark-btn--sm' : '';
$iconOnlyClass = $iconOnly ? 'arena-bookmark-btn--icon' : '';
$savedClass   = $isBookmarked ? 'is-saved' : '';
?>
<form method="post" action="<?= route_to('public.bookmark.toggle') ?>" class="arena-bookmark-form d-inline-block m-0 p-0" onclick="event.stopPropagation();">
    <?= csrf_field() ?>
    <input type="hidden" name="type" value="<?= esc($type) ?>">
    <input type="hidden" name="id" value="<?= (int) $id ?>">
    <button type="submit" 
            class="arena-bookmark-btn <?= esc($btnSizeClass) ?> <?= esc($iconOnlyClass) ?> <?= esc($savedClass) ?> <?= esc($extraCls) ?>" 
            aria-label="<?= esc($ariaLabel) ?>" 
            title="<?= esc($ariaLabel) ?>">
        <i class="<?= $isBookmarked ? 'fas fa-bookmark' : 'far fa-bookmark' ?>" aria-hidden="true"></i>
        <?php if (! $iconOnly): ?>
            <span><?= $isBookmarked ? 'Tersimpan' : 'Simpan' ?></span>
        <?php endif; ?>
    </button>
</form>
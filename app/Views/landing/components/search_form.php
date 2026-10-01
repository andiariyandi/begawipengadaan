<?php
$action = $action ?? current_url();
$queryName = $queryName ?? 'q';
$queryValue = $queryValue ?? service('request')->getGet($queryName);
$placeholder = $placeholder ?? 'Cari informasi...';
?>
<form action="<?= esc($action) ?>" method="get" class="courses-search__form">
    <div class="courses-search__input-box">
        <input type="search" name="<?= esc($queryName) ?>" value="<?= esc((string) $queryValue) ?>" placeholder="<?= esc($placeholder) ?>">
        <button type="submit"><span class="icon-search"></span></button>
    </div>
</form>

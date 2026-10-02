<?= $this->extend('landing/layouts/public') ?>

<?= $this->section('content') ?>
<?php
$workspaceName = $userName ?? 'Pengguna';
$workspaceRole = $roleLabel ?? 'Pengguna';
$workspaceMenu = $menuItems ?? [];
?>
<section class="bg-light border-bottom" style="padding-top: 178px;">
    <div class="container">
        <div class="d-flex align-items-center gap-2 py-2">
            <a class="small text-muted text-decoration-none" href="<?= site_url('/') ?>">Beranda</a>
            <span class="small text-muted">/</span>
            <span class="small text-muted">Workspace</span>
        </div>
    </div>
</section>
<section class="py-4">
    <div class="container">
        <div class="row g-4">
            <?= $this->include('main/workspace/components/sidebar', [
                'userName' => $workspaceName,
                'roleLabel' => $workspaceRole,
                'menuItems' => $workspaceMenu,
            ]) ?>
            <main class="col-lg-9 col-xl-9">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

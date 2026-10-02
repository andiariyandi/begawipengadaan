<?= $this->extend('main/workspace/layouts/workspace') ?>

<?= $this->section('content') ?>
<div class="bg-white border rounded p-4 p-lg-5 mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <p class="text-uppercase text-muted small mb-2"><?= esc($sectionContent['eyebrow']) ?></p>
            <h1 class="mb-3"><?= esc($sectionContent['title']) ?></h1>
            <p class="mb-0"><?= esc($sectionContent['description']) ?></p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="badge bg-success-subtle text-success px-3 py-2"><?= esc($roleLabel) ?></span>
        </div>
    </div>
</div>
<div class="row g-4 mb-4">
    <?php foreach ([
        ...$sectionContent['cards'],
    ] as $stat): ?>
        <div class="col-md-4">
            <div class="bg-white border rounded p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <p class="text-muted mb-3"><?= esc($stat['title']) ?></p>
                    <span class="icon-book-open text-success fs-4" aria-hidden="true"></span>
                </div>
                <strong class="display-6"><?= esc($stat['value']) ?></strong>
                <small class="d-block text-muted mt-2"><?= esc($stat['meta']) ?></small>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="bg-white border rounded p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 mb-0">Daftar aktivitas</h2>
                <span class="text-muted small">Prototype UI</span>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($sectionContent['items'] as $item): ?>
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span><?= esc($item) ?></span>
                        <span class="badge bg-light text-success">Prototype</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="bg-white border rounded p-4 h-100">
            <h2 class="h4 mb-3">Status Workspace</h2>
            <div class="list-group list-group-flush">
                <div class="list-group-item px-0">Nama pengguna <strong class="float-end"><?= esc($userName) ?></strong></div>
                <div class="list-group-item px-0">Role <strong class="float-end"><?= esc($roleLabel) ?></strong></div>
                <div class="list-group-item px-0">Status <strong class="float-end text-success">Prototype</strong></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

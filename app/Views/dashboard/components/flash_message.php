<?php
$sess = null;
if (session_status() === PHP_SESSION_ACTIVE) {
    $sess = session();
} elseif (!headers_sent()) {
    try {
        $sess = session();
    } catch (\Throwable $e) {
        $sess = null;
    }
}

$flashMsg = $sess ? ($sess->getFlashdata('message') ?? $sess->getFlashdata('success')) : ($message ?? null);
$flashErr = $sess ? ($sess->getFlashdata('error') ?? $sess->getFlashdata('danger')) : ($error ?? null);
$flashWarn = $sess ? ($sess->getFlashdata('warning')) : ($warning ?? null);
$flashInfo = $sess ? ($sess->getFlashdata('info')) : ($info ?? null);
$flashErrors = $sess ? $sess->getFlashdata('errors') : ($errors ?? null);
?>

<?php if (!empty($flashMsg)): ?>
    <div class="alert alert-success bg-success-100 text-success-600 border-success-100 px-24 py-11 mb-20 fw-semibold text-md radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:check-circle-bold" class="text-xl line-height-1 flex-shrink-0"></iconify-icon>
            <div><?= esc($flashMsg) ?></div>
        </div>
        <button type="button" class="remove-button text-success-600 text-xxl line-height-1 bg-transparent border-0 p-0" data-bs-dismiss="alert" aria-label="Close">
            <iconify-icon icon="iconamoon:sign-times-light"></iconify-icon>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($flashErr)): ?>
    <div class="alert alert-danger bg-danger-100 text-danger-600 border-danger-100 px-24 py-11 mb-20 fw-semibold text-md radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:danger-triangle-bold" class="text-xl line-height-1 flex-shrink-0"></iconify-icon>
            <div><?= esc($flashErr) ?></div>
        </div>
        <button type="button" class="remove-button text-danger-600 text-xxl line-height-1 bg-transparent border-0 p-0" data-bs-dismiss="alert" aria-label="Close">
            <iconify-icon icon="iconamoon:sign-times-light"></iconify-icon>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($flashWarn)): ?>
    <div class="alert alert-warning bg-warning-100 text-warning-600 border-warning-100 px-24 py-11 mb-20 fw-semibold text-md radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:bell-bold" class="text-xl line-height-1 flex-shrink-0"></iconify-icon>
            <div><?= esc($flashWarn) ?></div>
        </div>
        <button type="button" class="remove-button text-warning-600 text-xxl line-height-1 bg-transparent border-0 p-0" data-bs-dismiss="alert" aria-label="Close">
            <iconify-icon icon="iconamoon:sign-times-light"></iconify-icon>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($flashInfo)): ?>
    <div class="alert alert-info bg-info-100 text-info-600 border-info-100 px-24 py-11 mb-20 fw-semibold text-md radius-8 d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center gap-2">
            <iconify-icon icon="solar:info-circle-bold" class="text-xl line-height-1 flex-shrink-0"></iconify-icon>
            <div><?= esc($flashInfo) ?></div>
        </div>
        <button type="button" class="remove-button text-info-600 text-xxl line-height-1 bg-transparent border-0 p-0" data-bs-dismiss="alert" aria-label="Close">
            <iconify-icon icon="iconamoon:sign-times-light"></iconify-icon>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($flashErrors)): ?>
    <div class="alert alert-danger bg-danger-100 text-danger-600 border-danger-100 px-24 py-11 mb-20 fw-semibold text-md radius-8 d-flex align-items-start justify-content-between" role="alert">
        <div class="d-flex align-items-start gap-2">
            <iconify-icon icon="solar:danger-triangle-bold" class="text-xl line-height-1 flex-shrink-0 mt-2"></iconify-icon>
            <div>
                <ul class="mb-0 ps-16">
                    <?php foreach ((array) $flashErrors as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <button type="button" class="remove-button text-danger-600 text-xxl line-height-1 bg-transparent border-0 p-0" data-bs-dismiss="alert" aria-label="Close">
            <iconify-icon icon="iconamoon:sign-times-light"></iconify-icon>
        </button>
    </div>
<?php endif; ?>

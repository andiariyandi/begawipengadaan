<?php
$currentStep = (int) ($currentStep ?? 1);
$currentKey = $currentKey ?? 'akun';
$steps = [
    ['number' => 1, 'key' => 'akun', 'label' => 'Akun Dasar'],
    ['number' => 2, 'key' => 'wilayah', 'label' => 'Wilayah'],
    ['number' => 3, 'key' => 'profil', 'label' => 'Profil'],
    ['number' => 4, 'key' => 'tinjau', 'label' => 'Tinjau'],
];
?>

<div class="mb-32">
    <div class="d-flex align-items-start justify-content-between gap-4 position-relative">
        <?php foreach ($steps as $index => $step): ?>
            <?php $isActive = $step['number'] <= $currentStep; ?>
            <?php $isCurrent = $step['key'] === $currentKey; ?>
            <div class="d-flex flex-column align-items-center text-center position-relative z-1" style="min-width: 58px;">
                <span class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center text-xs fw-bold <?= $isActive ? 'bg-primary-600 text-white' : 'bg-neutral-100 text-secondary-light' ?>">
                    <?= $isActive && !$isCurrent ? '<i class="ri-check-line"></i>' : esc((string) $step['number']) ?>
                </span>
                <span class="text-xs mt-6 <?= $isCurrent ? 'text-primary-600 fw-semibold' : 'text-secondary-light' ?>">
                    <?= esc($step['label']) ?>
                </span>
            </div>

            <?php if ($index < count($steps) - 1): ?>
                <span class="flex-grow-1 h-2-px mt-16 <?= $step['number'] < $currentStep ? 'bg-primary-600' : 'bg-neutral-200' ?>"></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
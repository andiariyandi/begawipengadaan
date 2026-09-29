<?php
$status = strtolower($status ?? 'draft');
$map = [
    'active' => ['class' => 'bg-success-focus text-success-main', 'label' => 'Aktif'],
    'inactive' => ['class' => 'bg-neutral-200 text-secondary-light', 'label' => 'Tidak Aktif'],
    'pending' => ['class' => 'bg-warning-focus text-warning-main', 'label' => 'Menunggu'],
    'review' => ['class' => 'bg-info-focus text-info-main', 'label' => 'Ditinjau'],
    'approved' => ['class' => 'bg-success-focus text-success-main', 'label' => 'Disetujui'],
    'rejected' => ['class' => 'bg-danger-focus text-danger-main', 'label' => 'Ditolak'],
    'revision' => ['class' => 'bg-warning-focus text-warning-main', 'label' => 'Perlu Revisi'],
    'draft' => ['class' => 'bg-neutral-200 text-secondary-light', 'label' => 'Draft'],
];
$current = $map[$status] ?? $map['draft'];
?>
<span class="<?= esc($current['class']) ?> px-12 py-4 rounded-pill fw-medium text-sm"><?= esc($label ?? $current['label']) ?></span>

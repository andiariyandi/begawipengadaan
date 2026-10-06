<?php
$status = strtolower((string) ($status ?? 'default'));
$label = $label ?? ucfirst($status);
$map = [
    'active' => ['bg' => '#e8f8f0', 'color' => '#16803c'],
    'approved' => ['bg' => '#e8f8f0', 'color' => '#16803c'],
    'review' => ['bg' => '#fff5e5', 'color' => '#bd7400'],
    'pending' => ['bg' => '#fff5e5', 'color' => '#bd7400'],
    'rejected' => ['bg' => '#fff0f0', 'color' => '#c62828'],
    'inactive' => ['bg' => '#f1f3f5', 'color' => '#667085'],
    'default' => ['bg' => '#edf5ff', 'color' => '#0c5aa6'],
];
$style = $map[$status] ?? $map['default'];
?>
<span style="display:inline-block;padding:5px 11px;border-radius:999px;font-size:12px;font-weight:700;background:<?= esc($style['bg']) ?>;color:<?= esc($style['color']) ?>;"><?= esc($label) ?></span>

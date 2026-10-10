<?php
$rawStatus = (string) ($status ?? 'draft');
$normStatus = strtolower(trim(str_replace(['-', ' '], '_', $rawStatus)));

$map = [
    // Account / General status
    'active'                => ['class' => 'bg-success-focus text-success-main', 'label' => 'Aktif'],
    'inactive'              => ['class' => 'bg-neutral-200 text-secondary-light', 'label' => 'Tidak Aktif'],
    'pending'               => ['class' => 'bg-warning-focus text-warning-main', 'label' => 'Menunggu'],
    'pending_approval'      => ['class' => 'bg-warning-focus text-warning-main', 'label' => 'Menunggu Persetujuan'],
    'pending_verification'  => ['class' => 'bg-warning-focus text-warning-main', 'label' => 'Menunggu Verifikasi'],
    'verified'              => ['class' => 'bg-success-focus text-success-main', 'label' => 'Terverifikasi'],
    'review'                => ['class' => 'bg-info-focus text-info-main', 'label' => 'Ditinjau'],
    'in_review'             => ['class' => 'bg-info-focus text-info-main', 'label' => 'Dalam Review'],
    'approved'              => ['class' => 'bg-success-focus text-success-main', 'label' => 'Disetujui'],
    'rejected'              => ['class' => 'bg-danger-focus text-danger-main', 'label' => 'Ditolak'],
    'revision'              => ['class' => 'bg-warning-focus text-warning-main', 'label' => 'Perlu Revisi'],
    'draft'                 => ['class' => 'bg-neutral-200 text-secondary-light', 'label' => 'Draft'],
    
    // Publication / Content status
    'published'             => ['class' => 'bg-success-focus text-success-main', 'label' => 'Dipublikasikan'],
    'archived'              => ['class' => 'bg-neutral-200 text-secondary-light', 'label' => 'Diarsipkan'],
    'scheduled'             => ['class' => 'bg-info-focus text-info-main', 'label' => 'Dijadwalkan'],
    'open'                  => ['class' => 'bg-success-focus text-success-main', 'label' => 'Buka'],
    'closed'                => ['class' => 'bg-neutral-200 text-secondary-light', 'label' => 'Tutup'],
    'cancelled'             => ['class' => 'bg-danger-focus text-danger-main', 'label' => 'Dibatalkan'],
    'valid'                 => ['class' => 'bg-success-focus text-success-main', 'label' => 'Berlaku'],
    'repealed'              => ['class' => 'bg-danger-focus text-danger-main', 'label' => 'Dicabut'],
    'amended'               => ['class' => 'bg-warning-focus text-warning-main', 'label' => 'Diubah'],

    // Learning / Activity status
    'enrolled'              => ['class' => 'bg-primary-50 text-primary-600', 'label' => 'Terdaftar'],
    'in_progress'           => ['class' => 'bg-info-focus text-info-main', 'label' => 'Berjalan'],
    'ongoing'               => ['class' => 'bg-info-focus text-info-main', 'label' => 'Berlangsung'],
    'completed'             => ['class' => 'bg-success-focus text-success-main', 'label' => 'Selesai'],
    'submitted'             => ['class' => 'bg-info-focus text-info-main', 'label' => 'Diajukan'],
    'graded'                => ['class' => 'bg-success-focus text-success-main', 'label' => 'Dinilai'],
    'passed'                => ['class' => 'bg-success-focus text-success-main', 'label' => 'Lulus'],
    'failed'                => ['class' => 'bg-danger-focus text-danger-main', 'label' => 'Tidak Lulus'],
    'eligible'              => ['class' => 'bg-success-focus text-success-main', 'label' => 'Eligible'],
    'not_eligible'          => ['class' => 'bg-neutral-200 text-secondary-light', 'label' => 'Belum Eligible'],

    // Certificate status
    'issued'                => ['class' => 'bg-success-focus text-success-main', 'label' => 'Diterbitkan'],
    'revoked'               => ['class' => 'bg-danger-focus text-danger-main', 'label' => 'Dicabut'],
    'expired'               => ['class' => 'bg-neutral-200 text-secondary-light', 'label' => 'Kedaluwarsa'],

    // Event & Attendance status
    'attended'              => ['class' => 'bg-success-focus text-success-main', 'label' => 'Hadir'],
    'absent'                => ['class' => 'bg-danger-focus text-danger-main', 'label' => 'Tidak Hadir'],
    'waitlist'              => ['class' => 'bg-warning-focus text-warning-main', 'label' => 'Waitlist'],

    // Role tags
    'super_administrator'   => ['class' => 'bg-danger-focus text-danger-main', 'label' => 'Super Admin'],
    'administrator'         => ['class' => 'bg-primary-50 text-primary-600', 'label' => 'Administrator'],
    'participant'           => ['class' => 'bg-info-focus text-info-main', 'label' => 'Peserta'],
    'trainer'               => ['class' => 'bg-success-focus text-success-main', 'label' => 'Trainer'],
    'evaluator'             => ['class' => 'bg-warning-focus text-warning-main', 'label' => 'Evaluator'],
    'operator'              => ['class' => 'bg-secondary-focus text-secondary-main', 'label' => 'Operator'],
];

if (isset($map[$normStatus])) {
    $current = $map[$normStatus];
} else {
    $fallbackLabel = ucwords(str_replace(['_', '-'], ' ', $rawStatus !== '' ? $rawStatus : 'Draft'));
    $current = [
        'class' => 'bg-neutral-200 text-secondary-light',
        'label' => $fallbackLabel,
    ];
}
?>
<span class="<?= esc($current['class']) ?> px-12 py-4 rounded-pill fw-medium text-sm"><?= esc($label ?? $current['label']) ?></span>

<?php
/**
 * Reusable Operational Sub-Navigation for Arena Belajar (Learning Management)
 *
 * @var string $activeSub 'overview'|'classes'|'curriculum'|'assessments'|'bank-soal'|'certificates'|'feedback'|'reports'
 */
$activeSub = $activeSub ?? 'classes';
?>

<div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills gap-2 flex-wrap mb-0">
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'overview' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/pembelajaran') ?>">
                    <iconify-icon icon="solar:chart-square-outline" class="text-base"></iconify-icon>
                    <span>Overview &amp; Kursus</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'classes' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/pembelajaran/kelas') ?>">
                    <iconify-icon icon="solar:book-2-outline" class="text-base"></iconify-icon>
                    <span>Kelas &amp; Peserta</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'curriculum' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/pembelajaran/kurikulum') ?>">
                    <iconify-icon icon="solar:layers-minimalistic-outline" class="text-base"></iconify-icon>
                    <span>Kurikulum &amp; Silabus</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'assessments' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/pembelajaran/assessment') ?>">
                    <iconify-icon icon="solar:check-read-outline" class="text-base"></iconify-icon>
                    <span>Evaluasi &amp; Kuis</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'bank-soal' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/pembelajaran/assessment/bank-soal') ?>">
                    <iconify-icon icon="solar:checklist-minimalistic-outline" class="text-base"></iconify-icon>
                    <span>Bank Soal</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'certificates' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat') ?>">
                    <iconify-icon icon="solar:diploma-verified-outline" class="text-base"></iconify-icon>
                    <span>Monitoring Kelulusan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'feedback' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/pembelajaran/feedback-peserta') ?>">
                    <iconify-icon icon="solar:chat-round-line-outline" class="text-base"></iconify-icon>
                    <span>Umpan Balik</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'reports' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/pembelajaran/laporan') ?>">
                    <iconify-icon icon="solar:chart-2-outline" class="text-base"></iconify-icon>
                    <span>Laporan Belajar</span>
                </a>
            </li>
        </ul>
        <div class="d-none d-xxl-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:mortarboard-outline" class="align-middle me-1"></iconify-icon>
            Pusat Arena Belajar PBJ
        </div>
    </div>
</div>

<?= $this->extend('layout/public'); ?>

<?= $this->section('content'); ?>

<div class="card p-24 radius-12">
    <?php $authorContext = match ($caseStudy['author_profile_type'] ?? null) {
        'asn' => trim(($caseStudy['author_asn_institution'] ?? '') . ' / ' . ($caseStudy['author_asn_position'] ?? ''), ' /'),
        'provider' => $caseStudy['author_provider_business'] ?? '',
        'academic_practitioner' => $caseStudy['author_academic_institution'] ?? '',
        default => 'Masyarakat umum',
    }; ?>
    <span class="bg-neutral-200 text-neutral-600 px-16 py-4 radius-4 text-sm mb-12 d-inline-block"><?= esc($caseStudy['category_name']) ?></span>
    <h5 class="mb-12"><?= esc($caseStudy['title']) ?></h5>
    <p class="text-secondary-light text-sm mb-24">
        Oleh <?= esc($caseStudy['author_name']) ?><?php if ($authorContext !== ''): ?> (<?= esc($authorContext) ?>)<?php endif; ?> &middot; <?= esc(date('d M Y', strtotime($caseStudy['created_at']))) ?>
    </p>
    <div><?= nl2br(esc($caseStudy['content'])) ?></div>
</div>

<?= $this->endSection(); ?>

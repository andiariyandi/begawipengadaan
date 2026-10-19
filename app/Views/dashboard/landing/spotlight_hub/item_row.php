<?php
/**
 * Item Row Partial for Centralized Spotlight & Carousel Hub (100% Native WowDash)
 * 
 * @var array $item
 */

$featureIcons = [
    'program'           => 'solar:graduation-cap-bold',
    'course'            => 'solar:book-bookmark-bold',
    'class'             => 'solar:users-group-two-rounded-bold',
    'knowledge_article' => 'solar:document-bold',
    'knowledge_media'   => 'solar:videocamera-record-bold',
    'regulation'        => 'solar:document-text-bold',
    'news'              => 'solar:newspaper-bold',
    'event'             => 'solar:calendar-bold',
];

$featureIcon = $featureIcons[$item['feature'] ?? ''] ?? 'solar:slider-vertical-bold';
?>
<tr>
    <!-- Cover / Thumbnail -->
    <td style="width: 80px;">
        <div class="w-64-px h-40-px radius-8 overflow-hidden bg-neutral-100 flex-shrink-0 d-flex align-items-center justify-content-center border border-neutral-200 position-relative">
            <?php if (! empty($item['cover_url'])): ?>
                <img src="<?= esc($item['cover_url']) ?>" alt="<?= esc($item['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.style.display='none';if(this.nextElementSibling)this.nextElementSibling.classList.remove('d-none');">
                <div class="w-100 h-100 d-none align-items-center justify-content-center bg-neutral-100 text-secondary-light">
                    <iconify-icon icon="<?= $featureIcon ?>" class="text-xl"></iconify-icon>
                </div>
            <?php else: ?>
                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-neutral-100 text-secondary-light">
                    <iconify-icon icon="<?= $featureIcon ?>" class="text-xl"></iconify-icon>
                </div>
            <?php endif; ?>
        </div>
    </td>

    <!-- Title & Information -->
    <td>
        <div class="d-flex flex-column gap-1">
            <h6 class="text-md fw-semibold mb-0">
                <a href="<?= esc($item['public_url']) ?>" target="_blank" class="text-primary-light hover-text-primary text-decoration-none" title="<?= esc($item['title']) ?>">
                    <?= esc($item['title']) ?>
                </a>
            </h6>
            <?php if (! empty($item['subtitle'])): ?>
                <span class="text-secondary-light text-xs fw-medium">
                    <?= html_entity_decode(esc($item['subtitle']), ENT_QUOTES, 'UTF-8') ?>
                </span>
            <?php endif; ?>
            <?php if (! empty($item['summary'])): ?>
                <p class="text-secondary-light text-xs mb-0 text-truncate" style="max-width: 460px;" title="<?= esc(strip_tags((string) $item['summary'])) ?>">
                    <?= esc(mb_strimwidth(strip_tags((string) $item['summary']), 0, 95, '...')) ?>
                </p>
            <?php endif; ?>
        </div>
    </td>

    <!-- Type / Module Badge -->
    <td style="width: 150px;">
        <span class="badge text-xs fw-semibold radius-4 px-12 py-6 <?= esc($item['badge_class']) ?>">
            <?= esc($item['type_label']) ?>
        </span>
    </td>

    <!-- Carousel Slider Switch -->
    <td style="width: 130px;" class="text-center">
        <div class="form-switch switch-primary d-inline-flex justify-content-center align-items-center">
            <input class="form-check-input spotlight-toggle-switch cursor-pointer" type="checkbox"
                   data-feature="<?= esc($item['feature']) ?>"
                   data-id="<?= (int) $item['id'] ?>"
                   data-field="carousel_enabled"
                   <?= ! empty($item['carousel_enabled']) ? 'checked' : '' ?>
                   title="Aktifkan / Nonaktifkan di Hero Slider">
        </div>
    </td>

    <!-- Featured Switch -->
    <td style="width: 120px;" class="text-center">
        <div class="form-switch switch-warning d-inline-flex justify-content-center align-items-center">
            <input class="form-check-input spotlight-toggle-switch cursor-pointer" type="checkbox"
                   data-feature="<?= esc($item['feature']) ?>"
                   data-id="<?= (int) $item['id'] ?>"
                   data-field="is_featured"
                   <?= ! empty($item['is_featured']) ? 'checked' : '' ?>
                   title="Tandai sebagai Item Unggulan">
        </div>
    </td>

    <!-- Order Input -->
    <td style="width: 100px;" class="text-center">
        <input type="number" min="1" max="99"
               class="form-control form-control-sm text-center fw-semibold spotlight-order-input radius-8 mx-auto"
               style="width: 65px;"
               data-feature="<?= esc($item['feature']) ?>"
               data-id="<?= (int) $item['id'] ?>"
               value="<?= $item['carousel_order'] !== null ? (int) $item['carousel_order'] : '' ?>"
               placeholder="-"
               title="Nomor urut slide (klik di luar atau tekan Tab untuk simpan)">
    </td>

    <!-- Action Buttons -->
    <td style="width: 100px;" class="text-end text-nowrap">
        <div class="d-inline-flex align-items-center gap-2">
            <?php if (! empty($item['public_url'])): ?>
                <a href="<?= esc($item['public_url']) ?>" target="_blank"
                   class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-primary-600 hover-text-white"
                   title="Lihat Pratinjau di Landing Page">
                    <iconify-icon icon="iconamoon:eye-light" class="text-lg"></iconify-icon>
                </a>
            <?php endif; ?>
            <?php if (! empty($item['edit_url'])): ?>
                <a href="<?= esc($item['edit_url']) ?>"
                   class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white"
                   title="Ubah Konten di Dashboard">
                    <iconify-icon icon="lucide:edit" class="text-base"></iconify-icon>
                </a>
            <?php endif; ?>
        </div>
    </td>
</tr>

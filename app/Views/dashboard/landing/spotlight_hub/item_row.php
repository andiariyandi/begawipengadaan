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
$isCarouselActive = ! empty($item['carousel_enabled']);
$isFeaturedActive = ! empty($item['is_featured']);
$fallbackImg = base_url('assets/main/images/event/event-details-top-img-1.jpg');
?>
<tr class="spotlight-row"
    data-feature="<?= esc($item['feature']) ?>"
    data-feature-group="<?= esc($item['feature_group'] ?? '') ?>"
    data-carousel="<?= $isCarouselActive ? '1' : '0' ?>"
    data-featured="<?= $isFeaturedActive ? '1' : '0' ?>"
    data-id="<?= (int) $item['id'] ?>">

    <!-- Cover / Thumbnail with Lightbox Zoom -->
    <td style="width: 80px;" class="align-middle">
        <div class="w-64-px h-44-px radius-8 overflow-hidden bg-neutral-100 flex-shrink-0 d-flex align-items-center justify-content-center border border-neutral-200 position-relative spotlight-thumb-box cursor-pointer"
             onclick="openSpotlightImagePreview('<?= ! empty($item['cover_url']) ? esc($item['cover_url']) : $fallbackImg ?>', '<?= esc($item['title']) ?>', '<?= esc($item['type_label']) ?>')"
             title="Klik untuk perbesar gambar">
            <?php if (! empty($item['cover_url'])): ?>
                <img src="<?= esc($item['cover_url']) ?>" alt="<?= esc($item['title']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='<?= $fallbackImg ?>';">
            <?php else: ?>
                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary-50 text-primary-600">
                    <iconify-icon icon="<?= $featureIcon ?>" class="text-xl"></iconify-icon>
                </div>
            <?php endif; ?>
            <div class="thumb-zoom-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-40 text-white opacity-0 hover-opacity-100 transition-2">
                <iconify-icon icon="solar:magnifer-zoom-in-bold" class="text-sm"></iconify-icon>
            </div>
        </div>
    </td>

    <!-- Title & Detailed Information -->
    <td class="align-middle">
        <div class="d-flex flex-column gap-1">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h6 class="text-sm fw-semibold mb-0">
                    <a href="<?= esc($item['public_url']) ?>" target="_blank" class="text-primary-light hover-text-primary text-decoration-none" title="<?= esc($item['title']) ?>">
                        <?= esc($item['title']) ?>
                    </a>
                </h6>
                <?php if ($isCarouselActive): ?>
                    <span class="badge text-2xs bg-success-50 text-success-600 border border-success-200 radius-4 px-6 py-2 d-inline-flex align-items-center gap-1">
                        <span class="w-6-px h-6-px bg-success-main rounded-circle"></span>
                        Slide Hero<?= $item['carousel_order'] !== null ? ' #' . (int) $item['carousel_order'] : '' ?>
                    </span>
                <?php endif; ?>
                <?php if ($isFeaturedActive): ?>
                    <span class="badge text-2xs bg-warning-50 text-warning-main border border-warning-200 radius-4 px-6 py-2 d-inline-flex align-items-center gap-1">
                        <iconify-icon icon="solar:star-bold" class="text-xs"></iconify-icon> Unggulan
                    </span>
                <?php endif; ?>
            </div>
            <?php if (! empty($item['subtitle'])): ?>
                <span class="text-secondary-light text-xs fw-medium d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:info-circle-linear" class="text-xs text-primary-600"></iconify-icon>
                    <?= html_entity_decode(esc($item['subtitle']), ENT_QUOTES, 'UTF-8') ?>
                </span>
            <?php endif; ?>
            <?php if (! empty($item['summary'])): ?>
                <p class="text-secondary-light text-xs mb-0 text-truncate" style="max-width: 440px;" title="<?= esc(strip_tags((string) $item['summary'])) ?>">
                    <?= esc(mb_strimwidth(strip_tags((string) $item['summary']), 0, 95, '...')) ?>
                </p>
            <?php endif; ?>
        </div>
    </td>

    <!-- Type / Module Badge -->
    <td style="width: 150px;" class="align-middle">
        <span class="badge text-xs fw-semibold radius-4 px-10 py-6 <?= esc($item['badge_class']) ?>">
            <?= esc($item['type_label']) ?>
        </span>
    </td>

    <!-- Carousel Slider Switch -->
    <td style="width: 130px;" class="text-center align-middle">
        <div class="form-switch switch-primary d-inline-flex justify-content-center align-items-center">
            <input class="form-check-input spotlight-toggle-switch cursor-pointer" type="checkbox"
                   data-feature="<?= esc($item['feature']) ?>"
                   data-id="<?= (int) $item['id'] ?>"
                   data-field="carousel_enabled"
                   <?= $isCarouselActive ? 'checked' : '' ?>
                   title="Aktifkan / Nonaktifkan di Hero Slider">
        </div>
    </td>

    <!-- Featured Switch -->
    <td style="width: 120px;" class="text-center align-middle">
        <div class="form-switch switch-warning d-inline-flex justify-content-center align-items-center">
            <input class="form-check-input spotlight-toggle-switch cursor-pointer" type="checkbox"
                   data-feature="<?= esc($item['feature']) ?>"
                   data-id="<?= (int) $item['id'] ?>"
                   data-field="is_featured"
                   <?= $isFeaturedActive ? 'checked' : '' ?>
                   title="Tandai sebagai Item Unggulan">
        </div>
    </td>

    <!-- Order Input with Step Controls -->
    <td style="width: 100px;" class="text-center align-middle">
        <div class="d-flex align-items-center justify-content-center gap-1 mx-auto" style="width: 75px;">
            <input type="number" min="1" max="99"
                   class="form-control form-control-sm text-center fw-semibold spotlight-order-input radius-8"
                   data-feature="<?= esc($item['feature']) ?>"
                   data-id="<?= (int) $item['id'] ?>"
                   value="<?= $item['carousel_order'] !== null ? (int) $item['carousel_order'] : '' ?>"
                   placeholder="-"
                   title="Nomor urut slide (otomatis disimpan saat diketik atau klik luar)">
        </div>
    </td>

    <!-- Action Buttons -->
    <td style="width: 110px;" class="text-end text-nowrap align-middle">
        <div class="d-inline-flex align-items-center gap-2">
            <?php if (! empty($item['public_url'])): ?>
                <a href="<?= esc($item['public_url']) ?>" target="_blank"
                   class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-primary-600 hover-text-white transition-2"
                   title="Lihat Pratinjau Publik">
                    <iconify-icon icon="iconamoon:eye-light" class="text-base"></iconify-icon>
                </a>
            <?php endif; ?>
            <?php if (! empty($item['edit_url'])): ?>
                <a href="<?= esc($item['edit_url']) ?>"
                   class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center hover-bg-success-main hover-text-white transition-2"
                   title="Ubah Konten di Dashboard">
                    <iconify-icon icon="lucide:edit" class="text-sm"></iconify-icon>
                </a>
            <?php endif; ?>
            <div class="dropdown d-inline-block">
                <button class="w-32-px h-32-px bg-neutral-100 text-secondary-light rounded-circle d-inline-flex align-items-center justify-content-center border-0 hover-bg-neutral-200"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Opsi">
                    <iconify-icon icon="solar:menu-dots-bold" class="text-sm"></iconify-icon>
                </button>
                <ul class="dropdown-menu dropdown-menu-end radius-8 shadow border-0 py-8 text-xs">
                    <li><h6 class="dropdown-header text-secondary-light text-2xs fw-semibold text-uppercase">Aksi Pintas</h6></li>
                    <li>
                        <button class="dropdown-item py-6 d-flex align-items-center gap-2 btn-quick-top-slide"
                                data-feature="<?= esc($item['feature']) ?>"
                                data-id="<?= (int) $item['id'] ?>">
                            <iconify-icon icon="solar:crown-bold" class="text-warning-main"></iconify-icon>
                            <span>Jadikan Slide Teratas (#1)</span>
                        </button>
                    </li>
                    <?php if ($isCarouselActive): ?>
                        <li>
                            <button class="dropdown-item py-6 text-danger-600 d-flex align-items-center gap-2 btn-quick-remove-slide"
                                    data-feature="<?= esc($item['feature']) ?>"
                                    data-id="<?= (int) $item['id'] ?>">
                                <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                                <span>Keluarkan dari Slider</span>
                            </button>
                        </li>
                    <?php else: ?>
                        <li>
                            <button class="dropdown-item py-6 text-success-600 d-flex align-items-center gap-2 btn-quick-add-slide"
                                    data-feature="<?= esc($item['feature']) ?>"
                                    data-id="<?= (int) $item['id'] ?>">
                                <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                <span>Aktifkan ke Slider</span>
                            </button>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </td>
</tr>

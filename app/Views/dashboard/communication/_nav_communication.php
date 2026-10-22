<?php
/**
 * Reusable Sub-Navigation for Komunikasi (Berita, Event & Layanan Bantuan)
 *
 * @var string $activeSub 'news'|'news-categories'|'events'|'registrations'|'attendance'|'waitlist'|'inbox'|'whatsapp'|'newsletter'
 */
$activeSub = $activeSub ?? 'news';
?>

<div class="card border-0 radius-12 shadow-none border bg-base mb-24 p-12">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills gap-2 flex-wrap mb-0">
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'news' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/berita') ?>">
                    <iconify-icon icon="solar:newspaper-outline" class="text-base"></iconify-icon>
                    <span>Warta Berita</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'news-categories' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/kategori-berita') ?>">
                    <iconify-icon icon="solar:tag-outline" class="text-base"></iconify-icon>
                    <span>Kategori Berita</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= in_array($activeSub, ['events', 'registrations', 'attendance', 'waitlist'], true) ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/event-agenda') ?>">
                    <iconify-icon icon="solar:calendar-outline" class="text-base"></iconify-icon>
                    <span>Konsol Event &amp; Presensi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'inbox' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/pesan-masuk') ?>">
                    <iconify-icon icon="solar:inbox-outline" class="text-base"></iconify-icon>
                    <span>Pesan Masuk (Inbox)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'whatsapp' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/whatsapp-widget') ?>">
                    <iconify-icon icon="solar:phone-calling-rounded-outline" class="text-base"></iconify-icon>
                    <span>WhatsApp Helpdesk</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'newsletter' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/newsletter') ?>">
                    <iconify-icon icon="solar:letter-unread-outline" class="text-base"></iconify-icon>
                    <span>Newsletter</span>
                </a>
            </li>
        </ul>
        <div class="d-none d-xl-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:chat-round-dots-outline" class="align-middle me-1"></iconify-icon>
            Pusat Publikasi &amp; Komunikasi
        </div>
    </div>
</div>

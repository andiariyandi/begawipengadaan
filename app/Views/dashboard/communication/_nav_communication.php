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
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= in_array($activeSub, ['news-categories', 'categories'], true) ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/kategori-berita') ?>">
                    <iconify-icon icon="solar:tag-outline" class="text-base"></iconify-icon>
                    <span>Kategori Berita</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'events' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/event-agenda') ?>">
                    <iconify-icon icon="solar:calendar-outline" class="text-base"></iconify-icon>
                    <span>Event &amp; Agenda</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'registrations' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/registrasi-event') ?>">
                    <iconify-icon icon="solar:users-group-rounded-outline" class="text-base"></iconify-icon>
                    <span>Pendaftar Event</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'attendance' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/kehadiran') ?>">
                    <iconify-icon icon="solar:user-check-outline" class="text-base"></iconify-icon>
                    <span>Log Presensi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link radius-8 px-14 py-8 text-xs fw-semibold d-flex align-items-center gap-2 <?= $activeSub === 'waitlist' ? 'active bg-primary-600 text-white' : 'text-primary-light hover-bg-neutral-100' ?>" href="<?= site_url('dashboard/komunikasi/waitlist-pembatalan') ?>">
                    <iconify-icon icon="solar:clock-circle-outline" class="text-base"></iconify-icon>
                    <span>Waitlist &amp; Batal</span>
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
        <!-- <div class="d-none d-xxl-block text-2xs text-secondary-light">
            <iconify-icon icon="solar:chat-round-dots-outline" class="align-middle me-1"></iconify-icon>
            Pusat Publikasi &amp; Komunikasi
        </div> -->
    </div>
</div>
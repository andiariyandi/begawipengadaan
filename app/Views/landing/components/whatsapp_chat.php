<?php
/**
 * WhatsApp Floating Chat Widget for BegawiPengadaan Lampung
 * Position: Bottom-Left Corner
 * Directs to: Official Helpdesk PBJ WhatsApp (+62 851-1777-9783)
 */
$waPhone        = '6285117779783';
$defaultMessage = 'Halo Helpdesk BegawiPengadaan Provinsi Lampung, saya ingin berkonsultasi mengenai pengadaan barang dan jasa.';
$defaultWaUrl   = 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($defaultMessage);
?>

<!-- =========================================================
     FLOATING WHATSAPP CHAT POP-UP (POJOK KIRI BAWAH)
     ========================================================= -->
<div class="bp-wa-widget" id="bpWaWidget">
    <!-- Chat Pop-up Box Window -->
    <div class="bp-wa-box" id="bpWaBox" aria-hidden="true">
        <!-- Header -->
        <div class="bp-wa-header">
            <div class="bp-wa-header-avatar">
                <div class="bp-wa-avatar-img">
                    <iconify-icon icon="solar:headphones-round-bold" class="bp-wa-avatar-icon"></iconify-icon>
                </div>
                <span class="bp-wa-status-dot" title="Online"></span>
            </div>
            <div class="bp-wa-header-info">
                <h6 class="bp-wa-title">Helpdesk BegawiPengadaan</h6>
                <p class="bp-wa-subtitle">
                    <span class="bp-wa-badge-online">Online</span>
                    Biro PBJ Setda Prov. Lampung
                </p>
            </div>
            <button type="button" class="bp-wa-close-btn" id="bpWaCloseBtn" aria-label="Tutup Chat WhatsApp">
                <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
            </button>
        </div>

        <!-- Chat Conversation Area -->
        <div class="bp-wa-body">
            <div class="bp-wa-time-badge">
                <span>Hari ini &bull; Biro PBJ Siap Melayani</span>
            </div>

            <!-- Inbound Chat Bubble -->
            <div class="bp-wa-bubble">
                <div class="bp-wa-bubble-sender">Helpdesk PBJ Lampung</div>
                <div class="bp-wa-bubble-text">
                    Halo! Selamat datang di <strong>BegawiPengadaan</strong> 👋
                    <br><br>
                    Ada yang bisa kami bantu terkait regulasi PBJ, pelatihan di Arena Belajar, atau template dokumen kerja?
                </div>
                <div class="bp-wa-bubble-meta">
                    <span class="bp-wa-time" id="bpWaTime">Sekarang</span>
                    <iconify-icon icon="solar:check-read-bold" class="bp-wa-read-check"></iconify-icon>
                </div>
            </div>

            <!-- Quick Question Chips -->
            <div class="bp-wa-quick-chips">
                <p class="bp-wa-chips-title">Pilih topik cepat:</p>
                <button type="button" class="bp-wa-chip" data-msg="Halo Helpdesk BegawiPengadaan, saya ingin konsultasi mengenai regulasi PBJ terbaru (Perpres/PerLKPP).">
                    <iconify-icon icon="solar:document-text-bold"></iconify-icon> Regulasi &amp; Payung Hukum
                </button>
                <button type="button" class="bp-wa-chip" data-msg="Halo Helpdesk BegawiPengadaan, saya butuh informasi seputar kursus dan sertifikasi di Arena Belajar.">
                    <iconify-icon icon="solar:graduation-cap-bold"></iconify-icon> Arena Belajar &amp; Kursus
                </button>
                <button type="button" class="bp-wa-chip" data-msg="Halo Helpdesk BegawiPengadaan, saya ingin bertanya tentang template dokumen kerja di Bunga Rampai.">
                    <iconify-icon icon="solar:folder-with-files-bold"></iconify-icon> Template Bunga Rampai
                </button>
                <button type="button" class="bp-wa-chip" data-msg="Halo Helpdesk BegawiPengadaan, saya butuh bantuan teknis terkait akun atau portal LPSE/SPSE.">
                    <iconify-icon icon="solar:shield-check-bold"></iconify-icon> Bantuan Akun &amp; SPSE
                </button>
            </div>
        </div>

        <!-- Footer / Input Form -->
        <div class="bp-wa-footer">
            <div class="bp-wa-input-wrap">
                <input type="text" class="bp-wa-input" id="bpWaInput" placeholder="Ketik pesan Anda di sini..." autocomplete="off">
                <button type="button" class="bp-wa-send-btn" id="bpWaSendBtn" aria-label="Kirim ke WhatsApp">
                    <iconify-icon icon="solar:plain-bold"></iconify-icon>
                </button>
            </div>
            <a href="<?= $defaultWaUrl ?>" target="_blank" rel="noopener noreferrer" class="bp-wa-direct-link" id="bpWaDirectLink">
                <iconify-icon icon="logos:whatsapp-icon" class="bp-wa-logo-sm"></iconify-icon>
                <span>Buka Percakapan WhatsApp Langsung</span>
            </a>
        </div>
    </div>

    <!-- Floating Trigger Button (Bottom-Left) -->
    <div class="bp-wa-trigger-wrap">
        <button type="button" class="bp-wa-trigger-btn" id="bpWaTriggerBtn" aria-label="Buka Chat WhatsApp Helpdesk PBJ">
            <span class="bp-wa-pulse-ring"></span>
            <span class="bp-wa-unread-badge" id="bpWaBadge">1</span>
            <iconify-icon icon="logos:whatsapp-icon" class="bp-wa-icon-main"></iconify-icon>
            <span class="bp-wa-trigger-label d-none d-md-inline-block">Chat Helpdesk</span>
        </button>

        <!-- Teaser Speech Bubble (Displays briefly or until closed) -->
        <div class="bp-wa-teaser" id="bpWaTeaser">
            <span>Butuh bantuan seputar PBJ? Chat kami! 👋</span>
            <button type="button" class="bp-wa-teaser-close" id="bpWaTeaserClose" aria-label="Tutup Pesan">&times;</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const waPhone = '<?= $waPhone ?>';
    const triggerBtn = document.getElementById('bpWaTriggerBtn');
    const waBox = document.getElementById('bpWaBox');
    const closeBtn = document.getElementById('bpWaCloseBtn');
    const teaser = document.getElementById('bpWaTeaser');
    const teaserClose = document.getElementById('bpWaTeaserClose');
    const unreadBadge = document.getElementById('bpWaBadge');
    const inputEl = document.getElementById('bpWaInput');
    const sendBtn = document.getElementById('bpWaSendBtn');
    const chips = document.querySelectorAll('.bp-wa-chip');
    const timeEl = document.getElementById('bpWaTime');

    // Update real current time
    if (timeEl) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        timeEl.textContent = hours + ':' + minutes;
    }

    // Toggle Box
    function toggleBox() {
        const isActive = waBox.classList.contains('active');
        if (isActive) {
            waBox.classList.remove('active');
            waBox.setAttribute('aria-hidden', 'true');
        } else {
            waBox.classList.add('active');
            waBox.setAttribute('aria-hidden', 'false');
            if (unreadBadge) unreadBadge.style.display = 'none';
            if (teaser) teaser.style.display = 'none';
            setTimeout(() => {
                if (inputEl) inputEl.focus();
            }, 200);
        }
    }

    if (triggerBtn) {
        triggerBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleBox();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            waBox.classList.remove('active');
            waBox.setAttribute('aria-hidden', 'true');
        });
    }

    if (teaserClose && teaser) {
        teaserClose.addEventListener('click', function (e) {
            e.stopPropagation();
            teaser.style.display = 'none';
        });
    }

    // Send Message Handler
    function sendWaMessage(customText) {
        const message = (customText || (inputEl ? inputEl.value : '')).trim();
        const textToSend = message || 'Halo Helpdesk BegawiPengadaan Provinsi Lampung, saya ingin berkonsultasi mengenai pengadaan barang dan jasa.';
        const waUrl = 'https://wa.me/' + waPhone + '?text=' + encodeURIComponent(textToSend);
        window.open(waUrl, '_blank', 'noopener,noreferrer');
        if (inputEl) inputEl.value = '';
        waBox.classList.remove('active');
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', function () {
            sendWaMessage();
        });
    }

    if (inputEl) {
        inputEl.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendWaMessage();
            }
        });
    }

    // Quick chips click
    chips.forEach(chip => {
        chip.addEventListener('click', function () {
            const msg = this.getAttribute('data-msg');
            sendWaMessage(msg);
        });
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
        if (waBox && waBox.classList.contains('active')) {
            if (!waBox.contains(e.target) && !triggerBtn.contains(e.target)) {
                waBox.classList.remove('active');
                waBox.setAttribute('aria-hidden', 'true');
            }
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && waBox && waBox.classList.contains('active')) {
            waBox.classList.remove('active');
            waBox.setAttribute('aria-hidden', 'true');
        }
    });

    // Auto-dismiss teaser after 8 seconds
    if (teaser) {
        setTimeout(() => {
            if (teaser && !waBox.classList.contains('active')) {
                teaser.style.opacity = '0';
                teaser.style.transition = 'opacity 0.6s ease';
                setTimeout(() => { teaser.style.display = 'none'; }, 600);
            }
        }, 8000);
    }
});
</script>

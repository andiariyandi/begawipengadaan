<!-- =========================================================
     BEGAWIPENGADAAN — POPUP PROMO & PENGUMUMAN KHUSUS HOMEPAGE
     Responsive Image Slider Carousel dengan Tombol Close
     ========================================================= -->
<div id="homePromoOverlay" class="home-promo-overlay" role="dialog" aria-modal="true" aria-labelledby="homePromoTitle" style="display: none;">
    <div class="home-promo-backdrop" id="homePromoBackdrop"></div>

    <div class="home-promo-dialog">
        <div class="home-promo-card">
            <!-- Close Button Corner -->
            <button type="button" class="home-promo-close" id="homePromoCloseBtn" aria-label="Tutup Pengumuman">
                <i class="fas fa-times"></i>
            </button>

            <!-- Popup Header Bar -->
            <div class="home-promo-header">
                <div class="home-promo-header-badge">
                    <span class="home-promo-pulse"></span>
                    <span class="home-promo-badge-text">PENGUMUMAN RESMI PBJ PROVINSI LAMPUNG</span>
                </div>
            </div>

            <!-- Responsive Image Slider (Bootstrap Carousel) -->
            <div id="homePromoCarousel" class="carousel slide home-promo-carousel" data-bs-ride="carousel" data-bs-interval="5500" data-bs-pause="hover">
                <!-- Carousel Indicators -->
                <div class="carousel-indicators home-promo-indicators">
                    <button type="button" data-bs-target="#homePromoCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#homePromoCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#homePromoCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>

                <!-- Carousel Slides -->
                <div class="carousel-inner home-promo-slides">
                    <!-- Slide 1: Agenda & Bimtek -->
                    <div class="carousel-item active">
                        <div class="home-promo-slide-media">
                            <img src="<?= base_url('assets/main/images/event/event-details-top-img-1.jpg') ?>" class="home-promo-img" alt="Bimtek & Uji Kompetensi PBJ Lampung" loading="lazy">
                            <div class="home-promo-img-gradient"></div>
                            <span class="home-promo-category-pill bg-amber">AGENDA &amp; BIMTEK 2026</span>
                        </div>
                        <div class="home-promo-content">
                            <h3 class="home-promo-title" id="homePromoTitle">Bimbingan Teknis &amp; Uji Sertifikasi PBJ Level-1 Terpadu</h3>
                            <p class="home-promo-desc">
                                Pendaftaran gelombang baru program sertifikasi dan pelatihan kompetensi pengadaan barang/jasa bagi aparatur Pemprov Lampung kini dibuka.
                            </p>
                            <div class="home-promo-actions">
                                <a href="<?= site_url('event') ?>" class="home-promo-btn-primary">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Lihat Jadwal Agenda</span>
                                </a>
                                <button type="button" class="home-promo-btn-dismiss home-promo-dismiss-action">Tutup</button>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2: Arena Belajar -->
                    <div class="carousel-item">
                        <div class="home-promo-slide-media">
                            <img src="<?= base_url('assets/main/images/resources/course-details-img-1.jpg') ?>" class="home-promo-img" alt="Arena Belajar Mandiri BegawiPBJ" loading="lazy">
                            <div class="home-promo-img-gradient"></div>
                            <span class="home-promo-category-pill bg-sky">ARENA BELAJAR APARATUR</span>
                        </div>
                        <div class="home-promo-content">
                            <h3 class="home-promo-title">Tingkatkan Keahlian Mandiri Melalui Silabus &amp; Kursus Terakreditasi</h3>
                            <p class="home-promo-desc">
                                Akses materi silabus berjenjang, modul manajemen kontrak, latihan soal ujian berkala, dan kurikulum standar LKPP secara gratis.
                            </p>
                            <div class="home-promo-actions">
                                <a href="<?= site_url('arena-belajar') ?>" class="home-promo-btn-primary">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>Eksplorasi Kursus</span>
                                </a>
                                <button type="button" class="home-promo-btn-dismiss home-promo-dismiss-action">Tutup</button>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3: Regulasi & Bunga Rampai -->
                    <div class="carousel-item">
                        <div class="home-promo-slide-media">
                            <img src="<?= base_url('assets/main/images/resources/video-one-img-1.jpg') ?>" class="home-promo-img" alt="Pusat Regulasi & Template Dokumen" loading="lazy">
                            <div class="home-promo-img-gradient"></div>
                            <span class="home-promo-category-pill bg-emerald">REGULASI &amp; BUNGA RAMPAI</span>
                        </div>
                        <div class="home-promo-content">
                            <h3 class="home-promo-title">Repositori Dokumen Kerja KAK, HPS &amp; Kumpulan Regulasi PBJ</h3>
                            <p class="home-promo-desc">
                                Dapatkan template dokumen pengadaan siap pakai (DOCX/XLSX), telaah silsilah aturan Perpres/PerLKPP, serta infografis panduan mitigasi risiko.
                            </p>
                            <div class="home-promo-actions">
                                <a href="<?= site_url('bunga-rampai') ?>" class="home-promo-btn-primary">
                                    <i class="fas fa-file-download"></i>
                                    <span>Unduh Dokumen Kerja</span>
                                </a>
                                <button type="button" class="home-promo-btn-dismiss home-promo-dismiss-action">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carousel Controls (Prev & Next) -->
                <button class="carousel-control-prev home-promo-nav-btn home-promo-prev" type="button" data-bs-target="#homePromoCarousel" data-bs-slide="prev" aria-label="Slide Sebelumnya">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-control-next home-promo-nav-btn home-promo-next" type="button" data-bs-target="#homePromoCarousel" data-bs-slide="next" aria-label="Slide Selanjutnya">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- Popup Footer Controls -->
            <div class="home-promo-footer">
                <label class="home-promo-dont-show">
                    <input type="checkbox" id="homePromoDontShowToday">
                    <span>Jangan tampilkan lagi hari ini</span>
                </label>
                <button type="button" class="home-promo-footer-close home-promo-dismiss-action">
                    <i class="fas fa-check"></i>
                    <span>Tutup Pengumuman</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Discreet Re-Open Trigger Pill on Bottom Right -->
<button type="button" id="homePromoReopenBtn" class="home-promo-reopen-btn" style="display: none;" title="Buka Pengumuman BegawiPengadaan" aria-label="Buka Pengumuman">
    <span class="home-promo-reopen-icon"><i class="fas fa-bullhorn"></i></span>
    <span class="home-promo-reopen-label">Info &amp; Pengumuman</span>
</button>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('homePromoOverlay');
    const backdrop = document.getElementById('homePromoBackdrop');
    const closeBtn = document.getElementById('homePromoCloseBtn');
    const reopenBtn = document.getElementById('homePromoReopenBtn');
    const dismissBtns = document.querySelectorAll('.home-promo-dismiss-action');
    const dontShowCheckbox = document.getElementById('homePromoDontShowToday');

    const STORAGE_KEY_DISMISSED = 'begawi_home_promo_dismissed_date';
    const SESSION_KEY_CLOSED = 'begawi_home_promo_closed_session';

    function getTodayString() {
        const now = new Date();
        return now.getFullYear() + '-' + (now.getMonth() + 1) + '-' + now.getDate();
    }

    function isDismissedToday() {
        try {
            const savedDate = localStorage.getItem(STORAGE_KEY_DISMISSED);
            return savedDate === getTodayString();
        } catch (e) {
            return false;
        }
    }

    function isClosedThisSession() {
        try {
            return sessionStorage.getItem(SESSION_KEY_CLOSED) === 'true';
        } catch (e) {
            return false;
        }
    }

    function openPromoPopup() {
        if (!overlay) return;
        overlay.style.display = 'flex';
        // Force reflow
        void overlay.offsetWidth;
        overlay.classList.add('is-active');
        overlay.classList.remove('is-closing');
        if (reopenBtn) reopenBtn.style.display = 'none';
        document.body.style.overflow = 'hidden';
    }

    function closePromoPopup() {
        if (!overlay) return;
        overlay.classList.add('is-closing');
        overlay.classList.remove('is-active');

        try {
            sessionStorage.setItem(SESSION_KEY_CLOSED, 'true');
            if (dontShowCheckbox && dontShowCheckbox.checked) {
                localStorage.setItem(STORAGE_KEY_DISMISSED, getTodayString());
            }
        } catch (e) {}

        setTimeout(function () {
            overlay.style.display = 'none';
            overlay.classList.remove('is-closing');
            document.body.style.overflow = '';
            if (reopenBtn) reopenBtn.style.display = 'inline-flex';
        }, 300);
    }

    // Auto-show popup after gentle delay if not dismissed
    if (!isDismissedToday() && !isClosedThisSession()) {
        setTimeout(function () {
            openPromoPopup();
        }, 900);
    } else {
        if (reopenBtn) reopenBtn.style.display = 'inline-flex';
    }

    if (closeBtn) closeBtn.addEventListener('click', closePromoPopup);
    if (backdrop) backdrop.addEventListener('click', closePromoPopup);
    dismissBtns.forEach(function (btn) {
        btn.addEventListener('click', closePromoPopup);
    });

    if (reopenBtn) {
        reopenBtn.addEventListener('click', function () {
            openPromoPopup();
        });
    }

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay && overlay.classList.contains('is-active')) {
            closePromoPopup();
        }
    });
});
</script>

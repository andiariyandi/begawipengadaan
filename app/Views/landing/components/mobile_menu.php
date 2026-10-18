<div class="mobile-nav__wrapper">
    <div class="mobile-nav__overlay mobile-nav__toggler"></div>
    <div class="mobile-nav__content">
        <span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>
        <div class="logo-box"><a href="<?= site_url('/') ?>" aria-label="BegawiPengadaan"><img src="<?= base_url('assets/main/images/resources/logo-1.png') ?>" width="115" alt="BegawiPengadaan"></a></div>
        <div class="mobile-nav__actions px-4 pt-2 pb-3">
            <button type="button" class="btn btn-warning w-100 mb-2 py-2 fw-bold text-dark rounded-3 shadow-sm mobile-survey-btn" data-bs-toggle="modal" data-bs-target="#modalSurveyKepuasan">
                <i class="fas fa-star me-2"></i>Isi Survei Kepuasan
            </button>
            <?php if (! session()->get('isLoggedIn')): ?>
                <div class="d-flex gap-2">
                    <a href="<?= route_to('dashboard.auth.login') ?>" class="btn btn-outline-light w-50 py-2 fw-semibold rounded-3 text-center">Masuk</a>
                    <a href="<?= route_to('dashboard.auth.register') ?>" class="btn btn-warning w-50 py-2 fw-bold text-dark rounded-3 text-center">Daftar</a>
                </div>
            <?php else: ?>
                <a href="<?= route_to('workspace.home') ?>" class="btn btn-primary w-100 py-2 fw-semibold rounded-3 text-center">
                    <i class="fas fa-th-large me-2"></i>Workspace Saya
                </a>
            <?php endif; ?>
        </div>
        <div class="mobile-nav__container"></div>
        <ul class="mobile-nav__contact list-unstyled">
            <li><i class="fa fa-envelope"></i><a href="mailto:biropbj@lampungprov.go.id">biropbj@lampungprov.go.id</a></li>
            <li><i class="fas fa-phone"></i><a href="tel:+62721123456">+62 851-1777-9783</a></li>
        </ul>
        <div class="mobile-nav__top">
            <div class="mobile-nav__social"><a href="#" class="fab fa-linkedin-in" aria-label="LinkedIn"></a><a href="#" class="fab fa-facebook-square" aria-label="Facebook"></a><a href="#" class="fab fa-instagram" aria-label="Instagram"></a><a href="#" class="fab fa-youtube" aria-label="Youtube"></a></div>
        </div>
    </div>
</div>
<?= $this->extend('dashboard/layouts/auth') ?>

<?= $this->section('content') ?>
<section class="auth bg-base d-flex flex-wrap">
    <div class="auth-left d-lg-block d-none">
        <div class="d-flex align-items-center flex-column h-100 justify-content-center">
            <img src="<?= base_url('assets/dashboard/images/auth/auth-img.png') ?>" alt="BegawiPBJ Dashboard">
        </div>
    </div>

    <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
        <div class="max-w-464-px mx-auto w-100">
            <div>
                <a href="<?= site_url('dashboard') ?>" class="mb-40 max-w-290-px d-inline-block">
                    <img src="<?= base_url('assets/dashboard/images/logo.png') ?>" alt="BegawiPBJ">
                </a>
                <h4 class="mb-12">Masuk ke Dashboard</h4>
                <p class="mb-32 text-secondary-light text-lg">Kelola layanan dan administrasi BegawiPBJ.</p>
            </div>

            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= esc($flash['type'] ?? 'info') ?> mb-20" role="alert">
                    <?= esc($flash['message'] ?? '') ?>
                </div>
            <?php endif; ?>

            <form action="#" method="post">
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mage:email"></iconify-icon>
                    </span>
                    <input type="email" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Email admin" autocomplete="email">
                </div>

                <div class="position-relative mb-20">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="password" class="form-control h-56-px bg-neutral-50 radius-12" id="admin-password" placeholder="Kata sandi" autocomplete="current-password">
                    </div>
                    <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light" data-toggle="#admin-password"></span>
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <div class="form-check style-check d-flex align-items-center">
                        <input class="form-check-input border border-neutral-300" type="checkbox" id="remember-admin">
                        <label class="form-check-label" for="remember-admin">Ingat saya</label>
                    </div>
                    <a href="<?= site_url('dashboard/lupa-kata-sandi') ?>" class="text-primary-600 fw-medium">Lupa kata sandi?</a>
                </div>

                <a href="<?= site_url('dashboard') ?>" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">Masuk ke Dashboard</a>
            </form>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    $('.toggle-password').on('click', function() {
        $(this).toggleClass('ri-eye-off-line');
        const input = $($(this).attr('data-toggle'));
        input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    });
</script>
<?= $this->endSection() ?>
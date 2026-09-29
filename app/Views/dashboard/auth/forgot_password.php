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
            <a href="<?= site_url('dashboard/masuk') ?>" class="mb-40 max-w-290-px d-inline-block">
                <img src="<?= base_url('assets/dashboard/images/logo.png') ?>" alt="BegawiPBJ">
            </a>
            <h4 class="mb-12">Lupa Kata Sandi</h4>
            <p class="mb-32 text-secondary-light text-lg">Masukkan email administrator untuk menerima instruksi pemulihan.</p>
            <form action="#" method="post">
                <div class="icon-field mb-20">
                    <span class="icon top-50 translate-middle-y"><iconify-icon icon="mage:email"></iconify-icon></span>
                    <input type="email" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Email admin">
                </div>
                <a href="<?= site_url('dashboard/masuk') ?>" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12">Kirim Instruksi</a>
                <a href="<?= site_url('dashboard/masuk') ?>" class="d-block text-center text-primary-600 fw-medium mt-24">Kembali ke halaman masuk</a>
            </form>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'BegawiPengadaan') ?></title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/dashboard/images/favicon.png') ?>" sizes="16x16">
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/css/remixicon.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/css/style.css') ?>">

    <style>
        .auth-brand-text {
            color: #487fff;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
        }

        .auth-brand-subtext {
            color: #667085;
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .5px;
            margin-top: 5px;
            text-transform: uppercase;
        }

        .auth-left__content {
            max-width: 490px;
            padding: 48px;
            text-align: center;
        }

        .auth-left__content h2 {
            color: #101828;
            font-weight: 700;
            margin-top: 24px;
        }

        .auth-left__content p {
            color: #667085;
            font-size: 16px;
            margin-bottom: 0;
        }

        .auth-left__image {
            max-height: 500px;
            max-width: 100%;
            object-fit: contain;
        }

        .auth-right {
            min-height: 100vh;
            overflow-y: auto;
        }
    </style>

    <?= $this->renderSection('pageStyles') ?>
</head>

<body>
    <section class="auth bg-base d-flex flex-wrap">
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center">
                <div class="auth-left__content">
                    <img
                        src="<?= base_url('assets/dashboard/images/auth/auth-img.png') ?>"
                        class="auth-left__image"
                        alt="BegawiPengadaan">
                    <h2>BegawiPengadaan</h2>
                    <p>Platform informasi, pembelajaran, dan ekosistem Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.</p>
                </div>
            </div>
        </div>

        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </section>

    <script src="<?= base_url('assets/dashboard/js/lib/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/lib/iconify-icon.min.js') ?>"></script>
    <script src="<?= base_url('assets/dashboard/js/app.js') ?>"></script>

    <script>
        function initializePasswordToggle(selector) {
            $(selector).on('click', function() {
                $(this).toggleClass('ri-eye-off-line');

                const input = $($(this).attr('data-toggle'));
                input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
            });
        }

        initializePasswordToggle('.toggle-password');
    </script>

    <?= $this->renderSection('pageScripts') ?>
</body>

</html>
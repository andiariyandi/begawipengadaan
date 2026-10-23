<?php $pageTitle = $title ?? 'Dashboard BegawiPengadaan'; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($pageTitle) ?></title>
<meta name="csrf-token-name" content="<?= csrf_token() ?>">
<meta name="csrf-token-hash" content="<?= csrf_hash() ?>">
<meta name="csrf-header-name" content="<?= config('Security')->headerName ?? 'X-CSRF-TOKEN' ?>">
<script>
    window.csrfTokenName = '<?= csrf_token() ?>';
    window.csrfTokenHash = '<?= csrf_hash() ?>';
    window.csrfHeaderName = '<?= config('Security')->headerName ?? 'X-CSRF-TOKEN' ?>';
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery !== 'undefined') {
            jQuery.ajaxSetup({
                headers: {
                    [window.csrfHeaderName]: window.csrfTokenHash
                }
            });
        }
    });
    (function() {
        const originalFetch = window.fetch;
        window.fetch = function(url, options) {
            options = options || {};
            const method = (options.method || 'GET').toUpperCase();
            if (method !== 'GET' && method !== 'HEAD') {
                options.headers = options.headers || {};
                if (options.headers instanceof Headers) {
                    if (!options.headers.has(window.csrfHeaderName)) {
                        options.headers.set(window.csrfHeaderName, window.csrfTokenHash);
                    }
                } else if (Array.isArray(options.headers)) {
                    options.headers.push([window.csrfHeaderName, window.csrfTokenHash]);
                } else {
                    if (!options.headers[window.csrfHeaderName]) {
                        options.headers[window.csrfHeaderName] = window.csrfTokenHash;
                    }
                }
            }
            return originalFetch(url, options);
        };
    })();
</script>
<link rel="icon" type="image/png" href="<?= base_url('assets/dashboard/images/favicon.png') ?>" sizes="16x16">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/remixicon.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/apexcharts.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/dataTables.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/begawi-datatable.css?v=' . (file_exists(FCPATH . 'assets/dashboard/css/begawi-datatable.css') ? filemtime(FCPATH . 'assets/dashboard/css/begawi-datatable.css') : '2.0')) ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/editor-katex.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/editor.atom-one-dark.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/editor.quill.snow.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/flatpickr.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/full-calendar.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/jquery-jvectormap-2.0.5.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/magnific-popup.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/slick.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/prism.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/file-upload.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/audioplayer.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/dashboard/css/style.css?v=' . (file_exists(FCPATH . 'assets/dashboard/css/style.css') ? filemtime(FCPATH . 'assets/dashboard/css/style.css') : '1.0')) ?>">
<style>
@media print {
    aside.sidebar, header.topbar, .sidebar, .header, .d-footer, .breadcrumb, .d-print-none, .btn, .dropdown, select, input[type="text"], nav, .pagination, .navbar, .action-col, .no-print {
        display: none !important;
    }
    .dashboard-main {
        margin: 0 !important;
        margin-left: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .card {
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
        margin-bottom: 20px !important;
    }
    body {
        background: #ffffff !important;
        color: #0f172a !important;
    }
    .text-primary-light, .text-secondary-light, .text-base, h1, h2, h3, h4, h5, h6, .card-title, .fw-bold, .fw-semibold, th, td, span, p {
        color: #0f172a !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .badge {
        border: 1px solid #cbd5e1 !important;
        color: #0f172a !important;
        background: transparent !important;
    }
    .print-only {
        display: block !important;
    }
}
@media screen {
    .print-only {
        display: none !important;
    }
}
</style>
<?= $this->renderSection('pageStyles') ?>
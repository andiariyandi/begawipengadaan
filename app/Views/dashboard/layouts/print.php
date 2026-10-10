<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Cetak Dokumen | BegawiPBJ') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/css/remixicon.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dashboard/css/lib/bootstrap.min.css') ?>">
    <style>
        body { background: #fff; color: #111; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body class="p-24">
    <div class="no-print mb-24 d-flex justify-content-end gap-12">
        <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak</button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
    </div>
    <?= $this->renderSection('content') ?>
</body>
</html>

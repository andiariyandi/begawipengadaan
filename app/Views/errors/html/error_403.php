<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | BegawiPBJ</title>
    <style>
        body {
            height: 100%;
            background: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #555;
            font-weight: 400;
            margin: 0;
            padding: 0;
        }
        .wrap {
            max-width: 640px;
            margin: 6rem auto;
            padding: 2.5rem;
            background: #fff;
            text-align: center;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin: 0;
            color: #dc2626;
            line-height: 1.1;
        }
        h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 1rem 0 0.5rem;
            color: #1f2937;
        }
        p {
            margin: 1rem 0 1.5rem;
            color: #6b7280;
            line-height: 1.6;
        }
        .actions {
            margin-top: 2rem;
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }
        .btn {
            display: inline-block;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: #2563eb;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .btn-outline {
            border: 1px solid #d1d5db;
            color: #374151;
            background: #fff;
        }
        .btn-outline:hover {
            background: #f3f4f6;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>403</h1>
        <h2>Akses Ditolak</h2>
        <p>Anda tidak memiliki izin atau peran yang sesuai untuk mengakses halaman ini. Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator.</p>
        <div class="actions">
            <a href="<?= site_url('/') ?>" class="btn btn-outline">Beranda</a>
            <a href="<?= site_url('workspace') ?>" class="btn btn-primary">Ke Workspace</a>
        </div>
    </div>
</body>
</html>

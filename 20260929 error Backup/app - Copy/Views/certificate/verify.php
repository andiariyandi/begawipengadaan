<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Verifikasi Sertifikat</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #f4f6fb;
            margin: 0;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .valid {
            color: #1a7f37;
        }

        .invalid,
        .revoked {
            color: #c0392b;
        }
    </style>
</head>

<body>
    <div class="card">
        <?php if (! $certificate): ?>
            <h2 class="invalid">Sertifikat Tidak Ditemukan</h2>
            <p>Kode QR tidak valid atau sertifikat tidak pernah diterbitkan.</p>
        <?php elseif ($certificate['status'] === 'revoked'): ?>
            <h2 class="revoked">Sertifikat Telah Dicabut</h2>
            <p><?= esc($certificate['certificate_number']) ?></p>
        <?php else: ?>
            <h2 class="valid">Sertifikat Valid</h2>
            <p><strong><?= esc($certificate['username']) ?></strong></p>
            <p><?= esc($certificate['course_title']) ?></p>
            <p>Nomor: <?= esc($certificate['certificate_number']) ?></p>
            <p>Terbit: <?= esc(date('d F Y', strtotime($certificate['issued_date']))) ?></p>
        <?php endif; ?>
    </div>
</body>

</html>
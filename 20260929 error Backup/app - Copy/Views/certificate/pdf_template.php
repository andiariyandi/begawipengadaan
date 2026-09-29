<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            text-align: center;
            padding: 60px;
        }

        .border-box {
            border: 6px double #1a3c8f;
            padding: 50px;
        }

        h1 {
            font-size: 28px;
            color: #1a3c8f;
            margin-bottom: 0;
        }

        .subtitle {
            font-size: 14px;
            color: #555;
            margin-top: 4px;
        }

        .name {
            font-size: 32px;
            font-weight: bold;
            margin: 30px 0 10px;
        }

        .course {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .meta {
            font-size: 12px;
            color: #555;
            margin-top: 40px;
        }

        .qr {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="border-box">
        <h1>SERTIFIKAT PELATIHAN</h1>
        <p class="subtitle">Pengadaan Barang/Jasa Pemerintah — Begawipbj</p>
        <p style="margin-top:40px;">Diberikan kepada:</p>
        <div class="name"><?= esc($certificate['username']) ?></div>
        <p>Atas keberhasilan menyelesaikan program pelatihan:</p>
        <div class="course"><?= esc($certificate['course_title']) ?></div>
        <p class="meta">
            Nomor Sertifikat: <?= esc($certificate['certificate_number']) ?><br>
            Tanggal Terbit: <?= esc(date('d F Y', strtotime($certificate['issued_date']))) ?>
        </p>
        <div class="qr">
            <img src="<?= $qrDataUri ?>" width="120" height="120">
            <p style="font-size:10px; color:#888;">Pindai untuk verifikasi keaslian sertifikat</p>
        </div>
    </div>
</body>

</html>
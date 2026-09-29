<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 0;
        }

        .muted {
            color: #666;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: left;
        }

        th {
            background: #f0f2f8;
        }
    </style>
</head>

<body>
    <h1>Laporan Statistik PBJ</h1>
    <p class="muted">Dihasilkan pada <?= esc($generatedAt) ?></p>

    <h3>Statistik Pengguna</h3>
    <table>
        <tr>
            <th>Total Pengguna</th>
            <td><?= esc($userStats['total']) ?></td>
        </tr>
        <tr>
            <th>ASN</th>
            <td><?= esc($userStats['asn']) ?></td>
        </tr>
        <tr>
            <th>Umum</th>
            <td><?= esc($userStats['umum']) ?></td>
        </tr>
    </table>

    <h3>Statistik Pelatihan</h3>
    <table>
        <tr>
            <th>Total Program</th>
            <td><?= esc($training['total_courses']) ?></td>
        </tr>
        <tr>
            <th>Total Kelas</th>
            <td><?= esc($training['total_classes']) ?></td>
        </tr>
        <tr>
            <th>Peserta Aktif</th>
            <td><?= esc($training['active_participants']) ?></td>
        </tr>
        <tr>
            <th>Tingkat Kelulusan</th>
            <td><?= esc($training['pass_rate']) ?>%</td>
        </tr>
        <tr>
            <th>Nilai Rata-rata</th>
            <td><?= esc($training['average_score']) ?></td>
        </tr>
    </table>

    <h3>Statistik Sertifikat</h3>
    <table>
        <tr>
            <th>Total</th>
            <td><?= esc($certificate['total']) ?></td>
        </tr>
        <tr>
            <th>Aktif</th>
            <td><?= esc($certificate['active']) ?></td>
        </tr>
        <tr>
            <th>Tervalidasi</th>
            <td><?= esc($certificate['verified']) ?></td>
        </tr>
    </table>

    <h3>Statistik Regulasi</h3>
    <table>
        <tr>
            <th>Total Dokumen</th>
            <td><?= esc($regulation['total']) ?></td>
        </tr>
    </table>
</body>

</html>
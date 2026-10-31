<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Hasil Reviu / Skrining Data RUP</title>
    <style>
        @page {
            margin: 1.5cm 1.2cm 1.5cm 1.2cm;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #111827;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2.5px solid #111827;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header-kop h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-kop h3 {
            margin: 2px 0 0 0;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-kop p {
            margin: 3px 0 0 0;
            font-size: 8pt;
            color: #4b5563;
        }
        .title-doc {
            text-align: center;
            margin-bottom: 12px;
        }
        .title-doc h4 {
            margin: 0;
            font-size: 11pt;
            text-decoration: underline;
            text-transform: uppercase;
            font-weight: bold;
        }
        .title-doc span {
            font-size: 8.5pt;
            color: #374151;
        }
        .disclaimer-box {
            border: 1.5px solid #dc2626;
            background-color: #fef2f2;
            padding: 8px 12px;
            margin-bottom: 12px;
            border-radius: 4px;
            font-size: 8pt;
            line-height: 1.4;
            color: #991b1b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table-meta td {
            padding: 2.5px 4px;
            font-size: 9pt;
            vertical-align: top;
        }
        .table-data {
            border: 1px solid #9ca3af;
            font-size: 8pt;
        }
        .table-data th {
            background-color: #f3f4f6;
            border: 1px solid #9ca3af;
            padding: 5px 4px;
            font-weight: bold;
            text-align: center;
        }
        .table-data td {
            border: 1px solid #9ca3af;
            padding: 4px;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-kritis { background-color: #fee2e2; color: #991b1b; }
        .badge-tinggi { background-color: #ffedd5; color: #9a3412; }
        .badge-sedang { background-color: #fef3c7; color: #92400e; }
        .badge-rendah { background-color: #e0f2fe; color: #075985; }
        .badge-info   { background-color: #f3f4f6; color: #374151; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signature-wrap {
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-wrap td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9pt;
        }
        .sig-space {
            height: 55px;
        }
    </style>
</head>
<body>

    <!-- KOP RESMI -->
    <div class="header-kop">
        <h2><?= esc($batch['nama_instansi'] ?? 'Pemerintah Daerah') ?></h2>
        <h3>Sekretariat Daerah &middot; Biro / Bagian Pengadaan Barang dan Jasa</h3>
        <p>Layanan Pengadaan Secara Elektronik (LPSE) &middot; Sistem Pengawasan dan Skrining RUP v4.0</p>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="title-doc">
        <h4>Berita Acara Hasil Reviu / Skrining Data RUP</h4>
        <span>Nomor: BA.RUP/027/<?= esc($batch['id']) ?>/<?= esc($opd['id']) ?>/<?= esc($batch['tahun_anggaran']) ?></span>
    </div>

    <!-- DISCLAIMER BOX MANDATORI ENGINE v4.0 (12-COLUMN CSV DISCIPLINE) -->
    <div class="disclaimer-box">
        <strong>DISCLAIMER RUANG LINGKUP &middot; SKRINING ANALITIK RUP-ONLY (CSV 12 KOLOM):</strong><br>
        Pemeriksaan ini dilakukan secara analitik atas data RUP dalam satu berkas CSV dengan 12 kolom inti tanpa mengasumsikan fakta eksternal (DPA, KAK, HPS, SIKaP, ketersediaan katalog aktual, atau kontrak). Hasil analisis menunjukkan potensi ketidaksesuaian rencana dan sinyal risiko berdasarkan data yang diunggah, bukan pembuktian pelaksanaan pemilihan, transaksi, atau realisasi belanja. Seluruh temuan yang membutuhkan pembuktian di luar data CSV wajib diverifikasi dan dikonfirmasi lebih lanjut oleh Pengguna Anggaran (PA) / Pejabat Pembuat Komitmen (PPK).
    </div>

    <p style="text-align: justify; margin-bottom: 8px; font-size: 8.5pt;">
        Pada hari ini, <strong><?= esc($printedAt) ?></strong>, telah dilaksanakan reviu dan penapisan otomatis atas Rencana Umum Pengadaan (RUP) Tahun Anggaran <?= esc($batch['tahun_anggaran']) ?> menggunakan <em>RUP Compliance &amp; Risk Screening Engine v4.0</em> berpedoman pada Perpres 16/2018 jo. Perpres 46/2025, UU 20/2023, dan Peraturan LKPP Nomor 3 Tahun 2026 terhadap satuan kerja:
    </p>

    <!-- PROFIL SATKER & HASIL AUDIT -->
    <table class="table-meta">
        <tr>
            <td style="width: 28%;"><strong>Nama Satuan Kerja (OPD)</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 70%;"><strong><?= esc($opd['nama_opd_clean']) ?></strong></td>
        </tr>
        <tr>
            <td><strong>Kode Organisasi SIPD</strong></td>
            <td>:</td>
            <td><?= esc($opd['kode_satker_sipd'] ?: '-') ?></td>
        </tr>
        <tr>
            <td><strong>Total Pagu &amp; Paket RUP</strong></td>
            <td>:</td>
            <td>Rp <?= number_format($opd['total_pagu'], 0, ',', '.') ?> &middot; (<?= number_format($opd['total_paket']) ?> Paket)</td>
        </tr>
        <tr>
            <td><strong>Pagu Penyedia / Swakelola</strong></td>
            <td>:</td>
            <td>Rp <?= number_format($opd['pagu_penyedia'], 0, ',', '.') ?> (Penyedia) &middot; Rp <?= number_format($opd['pagu_swakelola'], 0, ',', '.') ?> (Swakelola)</td>
        </tr>
        <tr>
            <td><strong>Pagu Potensi Tidak Sesuai</strong></td>
            <td>:</td>
            <td>
                <strong style="color: #b91c1c;">Rp <?= number_format($opd['pagu_bermasalah_kritis_tinggi'] ?? 0, 0, ',', '.') ?></strong>
                (<?= number_format(($opd['temuan_kritis'] ?? 0) + ($opd['temuan_tinggi'] ?? 0)) ?> Paket Berisiko Tinggi)
            </td>
        </tr>
        <tr>
            <td><strong>Indikator Komposisi CSV</strong></td>
            <td>:</td>
            <td>
                E-Purchasing: <strong><?= number_format($opd['persen_epurchasing'], 1) ?>%</strong> &middot;
                UMK Penyedia: <strong><?= number_format($opd['persen_umk_penyedia'], 1) ?>%</strong> &middot;
                PDN: <strong><?= number_format($opd['persen_pdn'], 1) ?>%</strong>
                <span style="font-size: 7.5pt; color: #6b7280;">*(Dihitung dari data CSV yang diunggah)</span>
            </td>
        </tr>
    </table>

    <!-- RINGKASAN TEMUAN PRIORITAS -->
    <h5 style="margin: 10px 0 5px 0; font-size: 9pt; text-transform: uppercase;">Daftar Paket yang Memerlukan Reviu / Tindak Lanjut:</h5>
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 13%;">Kode RUP</th>
                <th style="width: 33%;">Nama Paket Pengadaan</th>
                <th style="width: 14%;">Metode / Jenis</th>
                <th style="width: 14%;">Pagu RUP (Rp)</th>
                <th style="width: 22%;">Status &amp; Rekomendasi RUP</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $countShown = 0;
            foreach ($packages as $idx => $pkg):
                if (($pkg['max_severity'] ?? 'AMAN') === 'AMAN') continue;
                $countShown++;
                if ($countShown > 25) break;
            ?>
            <tr>
                <td class="text-center"><?= $countShown ?></td>
                <td><strong><?= esc($pkg['kode_rup']) ?></strong><br><small><?= esc($pkg['source_funding_profile'] ?? $pkg['sumber_dana']) ?></small></td>
                <td><?= esc($pkg['nama_paket']) ?></td>
                <td><?= esc($pkg['metode_pengadaan']) ?><br><small style="color: #6b7280;"><?= esc($pkg['jenis_pengadaan']) ?></small></td>
                <td class="text-right">Rp <?= number_format($pkg['total_nilai'], 0, ',', '.') ?></td>
                <td>
                    <?php
                    $sev = $pkg['max_severity'] ?? 'INFO';
                    $badgeCls = match ($sev) {
                        'KRITIS', 'TINGGI' => 'badge-tinggi',
                        'SEDANG'           => 'badge-sedang',
                        'RENDAH'           => 'badge-rendah',
                        default            => 'badge-info',
                    };
                    ?>
                    <span class="badge <?= $badgeCls ?>"><?= esc($sev) ?></span>
                    <br><small style="color: #374151;"><?= $pkg['violation_count'] ?> Catatan Aturan</small>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if ($countShown === 0): ?>
            <tr>
                <td colspan="6" class="text-center" style="padding: 12px; color: #065f46;">
                    <strong>Tidak ditemukan potensi ketidaksesuaian pada data RUP yang diunggah.</strong>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p style="font-size: 8pt; color: #4b5563; margin-top: 3px; margin-bottom: 5px;">
        <em>* Lembar kerja lengkap seluruh paket dan matriks klaster dapat diunduh pada portal BegawiPengadaan format Excel 5-Sheet (.xlsx).</em>
    </p>

    <!-- LEMBAR PENGESAHAN / TANDA TANGAN -->
    <div class="signature-wrap">
        <table>
            <tr>
                <td>
                    Mengetahui / Mengonfirmasi,<br>
                    <strong>Kepala Satuan Kerja / Pengguna Anggaran</strong><br>
                    <?= esc($opd['nama_opd_clean']) ?>
                    <div class="sig-space"></div>
                    <strong><u>( .................................................... )</u></strong><br>
                    NIP. .................................................
                </td>
                <td>
                    Diverifikasi oleh Tim Reviu,<br>
                    <strong>Kepala UKPBJ / Biro Pengadaan Barang dan Jasa</strong><br>
                    <?= esc($batch['nama_instansi'] ?? 'Pemerintah Daerah') ?>
                    <div class="sig-space"></div>
                    <strong><u>( .................................................... )</u></strong><br>
                    NIP. .................................................
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

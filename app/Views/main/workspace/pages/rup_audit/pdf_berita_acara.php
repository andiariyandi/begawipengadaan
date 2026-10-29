<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Reviu Kepatuhan RUP</title>
    <style>
        @page {
            margin: 1.8cm 1.5cm 1.8cm 1.5cm;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #111827;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2.5px solid #111827;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .header-kop h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-kop h3 {
            margin: 2px 0 0 0;
            font-size: 11pt;
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
            margin-bottom: 16px;
        }
        .title-doc h4 {
            margin: 0;
            font-size: 11pt;
            text-decoration: underline;
            text-transform: uppercase;
            font-weight: bold;
        }
        .title-doc span {
            font-size: 9pt;
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .table-meta td {
            padding: 3px 6px;
            font-size: 9.5pt;
            vertical-align: top;
        }
        .table-data {
            border: 1px solid #9ca3af;
            font-size: 8.5pt;
        }
        .table-data th {
            background-color: #f3f4f6;
            border: 1px solid #9ca3af;
            padding: 5px 6px;
            font-weight: bold;
            text-align: center;
        }
        .table-data td {
            border: 1px solid #9ca3af;
            padding: 4px 6px;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-hijau { background-color: #d1fae5; color: #065f46; }
        .badge-kuning { background-color: #fef3c7; color: #92400e; }
        .badge-oranye { background-color: #ffedd5; color: #9a3412; }
        .badge-merah { background-color: #fee2e2; color: #991b1b; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signature-wrap {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-wrap td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .sig-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- KOP RESMI -->
    <div class="header-kop">
        <h2>Pemerintah Provinsi Lampung</h2>
        <h3>Sekretariat Daerah &middot; Biro Pengadaan Barang dan Jasa</h3>
        <p>Jl. Wolter Monginsidi No. 69, Teluk Betung, Bandar Lampung 35211 &middot; Email: biropbj@lampungprov.go.id</p>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="title-doc">
        <h4>Berita Acara Hasil Reviu Kepatuhan RUP</h4>
        <span>Nomor: BA.RUP/027/<?= esc($batch['id']) ?>/<?= esc($opd['id']) ?>/<?= esc($batch['tahun_anggaran']) ?></span>
    </div>

    <p style="text-align: justify; margin-bottom: 10px; font-size: 9.5pt;">
        Pada hari ini, <strong><?= esc($printedAt) ?></strong>, telah dilaksanakan reviu kepatuhan Rencana Umum Pengadaan (RUP) Tahun Anggaran <?= esc($batch['tahun_anggaran']) ?> secara otomatis menggunakan <em>Universal SIRUP Compliance Engine v3.1</em> berpedoman pada Peraturan Presiden Nomor 46 Tahun 2025, Undang-Undang Nomor 20 Tahun 2023, dan Peraturan LKPP Nomor 3 Tahun 2026 terhadap data pengadaan satuan kerja sebagai berikut:
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
            <td><strong>Rasio Pagu Penyedia / Swakelola</strong></td>
            <td>:</td>
            <td>Rp <?= number_format($opd['pagu_penyedia'], 0, ',', '.') ?> (Penyedia) &middot; Rp <?= number_format($opd['pagu_swakelola'], 0, ',', '.') ?> (Swakelola)</td>
        </tr>
        <tr>
            <td><strong>Skor Kepatuhan RCI v3.1</strong></td>
            <td>:</td>
            <td>
                <strong><?= number_format($opd['skor_rci'], 2) ?> / 100.00</strong>
                <?php
                $badgeCls = match ($opd['predikat_risiko']) {
                    'HIJAU' => 'badge-hijau',
                    'KUNING' => 'badge-kuning',
                    'ORANYE' => 'badge-oranye',
                    default => 'badge-merah',
                };
                ?>
                <span class="badge <?= $badgeCls ?>"><?= esc($opd['predikat_risiko']) ?></span>
            </td>
        </tr>
        <tr>
            <td><strong>Pencapaian Indikator Makro</strong></td>
            <td>:</td>
            <td>
                E-Purchasing: <strong><?= number_format($opd['persen_epurchasing'], 1) ?>%</strong> &middot;
                Alokasi UMK: <strong><?= number_format($opd['persen_umk_penyedia'], 1) ?>%</strong> &middot;
                UMK Eligible &le;15M: <strong><?= number_format($opd['persen_umk_eligible_le15m'], 1) ?>%</strong> &middot;
                PDN: <strong><?= number_format($opd['persen_pdn'], 1) ?>%</strong>
            </td>
        </tr>
    </table>

    <!-- RINGKASAN TEMUAN PRIORITAS -->
    <h5 style="margin: 12px 0 6px 0; font-size: 10pt; text-transform: uppercase;">Daftar Paket Berisiko Kritis &amp; Tinggi yang Memerlukan Tindak Lanjut:</h5>
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 14%;">Kode RUP</th>
                <th style="width: 33%;">Nama Paket Pengadaan</th>
                <th style="width: 15%;">Metode / Jenis</th>
                <th style="width: 15%;">Pagu RUP (Rp)</th>
                <th style="width: 18%;">Tingkat Risiko &amp; Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $countShown = 0;
            foreach ($packages as $idx => $pkg):
                if ($pkg['max_severity'] === 'AMAN') continue;
                $countShown++;
                if ($countShown > 25) break; // Batasi max 25 baris di PDF agar tidak overflow
            ?>
            <tr>
                <td class="text-center"><?= $countShown ?></td>
                <td><strong><?= esc($pkg['kode_rup']) ?></strong><br><small><?= esc($pkg['budget_cycle']) ?></small></td>
                <td><?= esc($pkg['nama_paket']) ?></td>
                <td><?= esc($pkg['metode_pengadaan']) ?><br><small style="color: #6b7280;"><?= esc($pkg['jenis_pengadaan']) ?></small></td>
                <td class="text-right">Rp <?= number_format($pkg['total_nilai'], 0, ',', '.') ?></td>
                <td>
                    <span class="badge <?= $pkg['max_severity'] === 'KRITIS' ? 'badge-merah' : ($pkg['max_severity'] === 'TINGGI' ? 'badge-oranye' : 'badge-kuning') ?>">
                        <?= esc($pkg['max_severity']) ?>
                    </span>
                    <br><small><?= $pkg['violation_count'] ?> Catatan Aturan</small>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if ($countShown === 0): ?>
            <tr>
                <td colspan="6" class="text-center" style="padding: 15px; color: #065f46;">
                    <strong>Tidak ditemukan anomali berisiko tinggi. RUP dinyatakan memenuhi standar kepatuhan regulasi.</strong>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p style="font-size: 8.5pt; color: #4b5563; margin-top: 4px; margin-bottom: 6px;">
        <em>* Kertas kerja lengkap seluruh paket dan matriks konsolidasi pemecahan paket (splitting) dapat diunduh pada portal BegawiPengadaan format Excel (.xlsx).</em>
    </p>

    <!-- CATATAN MANDATORI EVALUASI PEMILIK MANFAAT (BENEFICIAL OWNERSHIP) PASAL 8 PERLEM LKPP 3/2026 -->
    <div style="border: 1px solid #b45309; background-color: #fffbeb; padding: 6px 10px; margin-top: 6px; margin-bottom: 10px; border-radius: 4px; font-size: 8pt; line-height: 1.35; color: #78350f;">
        <strong>CATATAN MANDATORI PENGAWASAN &middot; PASAL 8 PERATURAN LKPP NOMOR 3 TAHUN 2026:</strong><br>
        Untuk paket pengadaan yang tahap pengumuman/undangannya dimulai sejak <strong>1 November 2026 s.d. 31 Desember 2026</strong> (dan seterusnya), Pokja Pemilihan / Pejabat Pengadaan <strong>WAJIB melakukan evaluasi data Pemilik Manfaat (<em>Beneficial Ownership</em>)</strong> pada SPSE. Peserta pemilihan yang tidak mengunggah data Pemilik Manfaat atau memiliki kesamaan Pemilik Manfaat dengan peserta lain pada paket yang sama <strong>WAJIB DIGUGURKAN</strong>.
    </div>

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
                    Disusun &amp; Diverifikasi oleh,<br>
                    <strong>Kepala Biro Pengadaan Barang dan Jasa</strong><br>
                    Sekretariat Daerah Provinsi Lampung
                    <div class="sig-space"></div>
                    <strong><u>( .................................................... )</u></strong><br>
                    NIP. .................................................
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

<?php

namespace App\Services\RupAudit;

use Dompdf\Dompdf;
use Dompdf\Options;
use ZipArchive;

class RupExportService
{
    protected RupAuditService $auditService;

    public function __construct()
    {
        $this->auditService = new RupAuditService();
    }

    /**
     * Menghasilkan berkas Excel 5-Sheet (.xlsx) murni menggunakan ZipArchive OpenXML
     * berpedoman pada Master Plan v4.0 (Ringkasan, Temuan_Paket, Cluster_Risiko, Profil_OPD, Tidak_Dapat_Dinilai).
     *
     * @param int $batchId
     * @return string Path absolut ke berkas .xlsx sementara
     */
    public function generateExcel5Sheet(int $batchId): string
    {
        $batch = $this->auditService->getBatch($batchId);
        if (! $batch) {
            throw new \RuntimeException("Batch ID {$batchId} tidak ditemukan.");
        }

        $db = \Config\Database::connect();
        $opdList     = $this->auditService->getOpdLeaderboard($batchId);
        $clusters    = $this->auditService->getClusters($batchId);
        $allPackages = $this->auditService->getPackagesFiltered($batchId, [], 10000, 0);

        // Ambil temuan pelanggaran per paket
        $violationsRaw = $db->table('rup_audit_violations v')
            ->select('v.*, p.row_index_csv, p.kode_rup, p.nama_opd_clean, p.nama_paket, p.total_nilai, p.cara_pengadaan, p.metode_pengadaan, p.jenis_pengadaan')
            ->join('rup_audit_packages p', 'p.id = v.package_id', 'left')
            ->where('v.batch_id', $batchId)
            ->get()->getResultArray();

        $tempPath = WRITEPATH . 'uploads/Hasil_Audit_RUP_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $batch['batch_uuid']) . '_' . date('Ymd_His') . '.xlsx';
        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0775, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat berkas arsip zip untuk Excel.');
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet4.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet5.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rootRels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/>
  <Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet4.xml"/>
  <Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet5.xml"/>
  <Relationship Id="rId6" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml (5 Sheets: Ringkasan, Temuan_Paket, Cluster_Risiko, Profil_OPD, Tidak_Dapat_Dinilai)
        $wbXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Ringkasan" sheetId="1" r:id="rId1"/>
    <sheet name="Temuan_Paket" sheetId="2" r:id="rId2"/>
    <sheet name="Cluster_Risiko" sheetId="3" r:id="rId3"/>
    <sheet name="Profil_OPD" sheetId="4" r:id="rId4"/>
    <sheet name="Tidak_Dapat_Dinilai" sheetId="5" r:id="rId5"/>
  </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $wbXml);

        // 5. xl/styles.xml
        $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><name val="Calibri"/><sz val="11"/></font>
    <font><b/><name val="Calibri"/><sz val="11"/></font>
  </fonts>
  <fills count="2">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
  </fills>
  <borders count="1">
    <border><left/><right/><top/><bottom/></border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="2">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>
  </cellXfs>
</styleSheet>';
        $zip->addFromString('xl/styles.xml', $stylesXml);

        // 6. Build Sheet 1: Ringkasan
        $macro = json_decode((string) ($batch['macro_summary'] ?? '{}'), true);
        $dist  = $macro['distribusi_temuan_unik'] ?? [];
        $sheet1Rows = [
            ['Parameter Ringkasan Skrining RUP v4.0', 'Nilai / Status'],
            ['Batch UUID', $batch['batch_uuid']],
            ['Tahun Anggaran', $batch['tahun_anggaran']],
            ['Instansi Pemerintah', $batch['nama_instansi']],
            ['File CSV Sumber', $batch['filename']],
            ['SHA-256 File', $batch['file_sha256'] ?? '-'],
            ['Skema Input', '12 Kolom Inti CSV SIRUP (RUP-Only)'],
            ['Versi Rule Set Engine', $batch['rule_set_version'] ?? '4.0.0'],
            ['Total Satker / OPD', $batch['total_opd']],
            ['Total Baris Paket', $batch['total_packages']],
            ['Total Nilai Pagu RUP (Rp)', $batch['total_pagu']],
            ['Total Pagu Penyedia (Rp)', $batch['total_pagu_penyedia']],
            ['Total Pagu Swakelola (Rp)', $batch['total_pagu_swakelola']],
            ['Proporsi E-Purchasing Penyedia', ($macro['persen_e_purchasing_penyedia'] ?? 0) . '%'],
            ['Proporsi UMK Penyedia', ($macro['persen_umk_terhadap_penyedia_non_dikecualikan'] ?? 0) . '%'],
            ['Proporsi PDN Penyedia', ($macro['persen_pdn_penyedia'] ?? 0) . '%'],
            ['Jumlah Paket Potensi Tidak Sesuai (Tinggi)', $dist['tinggi']['jumlah_paket'] ?? 0],
            ['Pagu Potensi Tidak Sesuai (Tinggi) (Rp)', $dist['tinggi']['total_pagu_terdampak'] ?? 0],
            ['Jumlah Paket Perlu Verifikasi (Sedang)', $dist['sedang']['jumlah_paket'] ?? 0],
            ['Pagu Perlu Verifikasi (Sedang) (Rp)', $dist['sedang']['total_pagu_terdampak'] ?? 0],
            ['Jumlah Paket Sinyal Risiko (Rendah)', $dist['rendah']['jumlah_paket'] ?? 0],
            ['Pagu Sinyal Risiko (Rendah) (Rp)', $dist['rendah']['total_pagu_terdampak'] ?? 0],
            ['Waktu Eksekusi Audit', $batch['created_at']],
        ];
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildSheetXml($sheet1Rows));

        // 7. Build Sheet 2: Temuan_Paket
        $sheet2Rows = [
            ['No', 'Row CSV', 'Kode RUP', 'Nama OPD', 'Nama Paket Pengadaan', 'Pagu RUP (Rp)', 'Cara', 'Metode', 'Jenis', 'Status Temuan', 'Severity', 'Confidence', 'ID Rule', 'Regulasi Rujukan', 'Alasan / Bukti Pemicu', 'Rekomendasi Koreksi RUP', 'Batas Inferensi CSV'],
        ];
        $no = 1;
        foreach ($violationsRaw as $v) {
            $payload = ! empty($v['target_perubahan']) ? json_decode((string) $v['target_perubahan'], true) : [];
            $findingStatus = $v['finding_status'] ?? ($payload['finding_status'] ?? 'POTENSI_TIDAK_SESUAI');
            $severity      = $payload['severity'] ?? ($v['severity'] ?? 'SEDANG');
            $confidence    = $v['confidence_level'] ?? ($payload['confidence_level'] ?? 'TINGGI');
            $recommendation= $v['recommendation_text'] ?? ($payload['recommendation_text'] ?? ($v['rekomendasi_perbaikan'] ?? '-'));
            $limitation    = $v['limitation_note'] ?? ($payload['limitation_note'] ?? 'Temuan menguji rencana pada data CSV; bukan pembuktian proses pemilihan riil.');

            $sheet2Rows[] = [
                $no++,
                $v['row_index_csv'] ?? '-',
                $v['kode_rup'] ?? '-',
                $v['nama_opd_clean'] ?? '-',
                $v['nama_paket'] ?? '-',
                $v['total_nilai'] ?? 0,
                $v['cara_pengadaan'] ?? '-',
                $v['metode_pengadaan'] ?? '-',
                $v['jenis_pengadaan'] ?? '-',
                $findingStatus,
                $severity,
                $confidence,
                $v['rule_id'] ?? '-',
                $v['regulasi'] ?? '-',
                $v['deskripsi_pelanggaran'] ?? '-',
                $recommendation,
                $limitation,
            ];
        }
        $zip->addFromString('xl/worksheets/sheet2.xml', $this->buildSheetXml($sheet2Rows));

        // 8. Build Sheet 3: Cluster_Risiko
        $sheet3Rows = [
            ['No', 'ID Klaster', 'Nama OPD', 'Komoditas Inti', 'Penanda Lokasi', 'Jenis', 'Jumlah Paket Terpecah', 'Total Pagu Terakumulasi (Rp)', 'Batas PL (Rp)', 'Tipe Klaster', 'Confidence Klaster', 'Severity', 'Daftar Kode RUP', 'Rekomendasi Konsolidasi', 'Catatan Batasan Klaster'],
        ];
        $no = 1;
        foreach ($clusters as $cls) {
            $dk = ! empty($cls['daftar_kode_rup']) ? json_decode((string) $cls['daftar_kode_rup'], true) : [];
            $clusterType = $cls['cluster_type'] ?? ($dk['cluster_type'] ?? 'KOMODITAS_SEJENIS');
            $confidence  = $cls['cluster_confidence'] ?? ($dk['cluster_confidence'] ?? 'SEDANG');
            $limitation  = $cls['cluster_limitation_note'] ?? ($dk['cluster_limitation_note'] ?? 'Kemiripan teks nama bukan bukti mutlak splitting; periksa kesamaan jadwal, lokasi, dan dokumen perencanaan.');
            $rupsText    = isset($dk['rups']) ? (is_array($dk['rups']) ? implode(', ', $dk['rups']) : $dk['rups']) : ($cls['daftar_kode_rup'] ?? '-');

            $sheet3Rows[] = [
                $no++,
                $cls['cluster_uuid'] ?? '-',
                $cls['nama_opd_clean'] ?? '-',
                $cls['nama_komoditas_klaster'] ?? '-',
                $cls['lokasi_identity_key'] ?? ($cls['lokasi_klaster'] ?? '-'),
                $cls['jenis_pengadaan'] ?? '-',
                $cls['jumlah_paket_terpecah'] ?? 0,
                $cls['total_pagu_klaster'] ?? 0,
                $cls['batas_regulasi_pl'] ?? 0,
                $clusterType,
                $confidence,
                $cls['severity'] ?? 'SEDANG',
                $rupsText,
                $cls['instruksi_konsolidasi'] ?? 'Konsolidasikan paket-paket sejenis bila kebutuhan dapat direncanakan sejak awal.',
                $limitation,
            ];
        }
        $zip->addFromString('xl/worksheets/sheet3.xml', $this->buildSheetXml($sheet3Rows));

        // 9. Build Sheet 4: Profil_OPD
        $sheet4Rows = [
            ['Peringkat Perhatian', 'Kode SIPD', 'Nama Satuan Kerja (OPD)', 'Total Paket', 'Total Pagu (Rp)', 'Pagu Penyedia (Rp)', 'Pagu Swakelola (Rp)', '% E-Purchasing', '% UMK Penyedia', '% PDN', 'Pagu Potensi Tidak Sesuai (Rp)', 'Jumlah Potensi Tidak Sesuai', 'Pagu Perlu Verifikasi (Rp)', 'Jumlah Perlu Verifikasi'],
        ];
        $rank = 1;
        foreach ($opdList as $opd) {
            $rp = json_decode((string) ($opd['risk_profile_json'] ?? '{}'), true);
            $sheet4Rows[] = [
                $rank++,
                $opd['kode_satker_sipd'] ?? '-',
                $opd['nama_opd_clean'] ?? '-',
                $opd['total_paket'] ?? 0,
                $opd['total_pagu'] ?? 0,
                $opd['pagu_penyedia'] ?? 0,
                $opd['pagu_swakelola'] ?? 0,
                ($opd['persen_epurchasing'] ?? 0) . '%',
                ($opd['persen_umk_penyedia'] ?? 0) . '%',
                ($opd['persen_pdn'] ?? 0) . '%',
                $rp['pagu_potensi_tidak_sesuai'] ?? ($opd['pagu_bermasalah_kritis_tinggi'] ?? 0),
                $rp['count_potensi_tidak_sesuai'] ?? ($opd['temuan_tinggi'] ?? 0),
                $rp['pagu_perlu_verifikasi'] ?? 0,
                $rp['count_perlu_verifikasi'] ?? ($opd['temuan_sedang'] ?? 0),
            ];
        }
        $zip->addFromString('xl/worksheets/sheet4.xml', $this->buildSheetXml($sheet4Rows));

        // 10. Build Sheet 5: Tidak_Dapat_Dinilai
        $sheet5Rows = [
            ['No', 'Tema Pemeriksaan', 'Dasar Regulasi Terkait', 'Fakta / Informasi yang Tidak Ada di CSV 12 Kolom', 'Perlakuan Engine v4.0'],
            [1, 'Alasan Keadaan Tertentu Penunjukan Langsung', 'Pasal 38 ayat (4)-(5) & Pasal 41 ayat (4)-(5) Perpres 46/2025', 'Tidak ada kolom justifikasi keadaan darurat, paten, repeat order, dll.', 'R-08 ditandai PERLU_VERIFIKASI; sistem tidak menuduh pelanggaran.'],
            [2, 'Ketersediaan Riil pada Katalog Elektronik', 'Pasal 38 ayat (2) Perpres 46/2025 jo. PerLKPP 2/2026', 'Tidak ada data ketersediaan produk live atau ID katalog barang/jasa.', 'R-15 ditandai PERLU_VERIFIKASI (kandidat cek katalog); bukan pelanggaran terbukti.'],
            [3, 'Tipe I s.d. IV Pelaksanaan Swakelola', 'PerLKPP Nomor 3 Tahun 2021 tentang Pedoman Swakelola', 'Tidak ada kolom tipe swakelola pada format ekspor 12 kolom.', 'R-12 ditandai PERLU_VERIFIKASI; evaluasi tipe diserahkan ke OPD.'],
            [4, 'Penggunaan Fitur Transaksional SPSE', 'Pasal 3 Perlem LKPP Nomor 3 Tahun 2026', 'Tidak ada kolom pencatatan transaksi SPSE dalam file RUP.', 'R-04b ditandai PERLU_VERIFIKASI; tidak dinilai sebagai pelanggaran otomatis.'],
            [5, 'Cakupan RUP Terhadap Pagu DPA / DPPA', 'PP 12/2019 & Permendagri 77/2020', 'Pagu DPA/DPPA tidak ada dalam CSV RUP.', 'R-26.4 dinonaktifkan (NON-AKTIF); tidak dihitung atau diberi penalti.'],
            [6, 'Kronologi Jadwal Pengumuman dan Kontrak', 'Pasal 22 Perpres 16/2018 jo. Perpres 46/2025', 'Tidak ada kolom tanggal pengumuman/pemilihan/kontrak pada 12 kolom CSV.', 'R-31 dinonaktifkan (NON-AKTIF); tidak dapat dinilai.'],
            [7, 'Pengawasan Riil Pekerjaan Konstruksi', 'Pasal 7 Perlem LKPP 3/2026 & PP 16/2021', 'Tidak ada data personel pengawas atau surat tugas tim teknis internal.', 'R-32 ditandai INFORMATIF; tidak menyimpulkan pekerjaan tanpa pengawasan.'],
            [8, 'Pengangkatan Tenaga Non-ASN', 'Pasal 65-66 UU Nomor 20 Tahun 2023 (ASN)', 'Tidak ada data status kepegawaian atau SK pengangkatan personel.', 'R-11 ditandai PERLU_VERIFIKASI; tidak mengklaim pelanggaran UU ASN dari nama paket.'],
        ];
        $zip->addFromString('xl/worksheets/sheet5.xml', $this->buildSheetXml($sheet5Rows));

        $zip->close();

        return $tempPath;
    }

    /**
     * Membangun XML Worksheet OpenXML dari baris data array.
     */
    private function buildSheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";
        $xml .= '  <sheetData>' . "\n";

        $rIndex = 1;
        foreach ($rows as $row) {
            $isHeader = ($rIndex === 1);
            $xml .= '    <row r="' . $rIndex . '">' . "\n";
            $cIndex = 1;
            foreach ($row as $val) {
                $colLetter = $this->getColumnLetter($cIndex);
                $cellRef   = $colLetter . $rIndex;
                $sAttr     = $isHeader ? ' s="1"' : '';

                if (is_numeric($val) && ! str_starts_with((string) $val, '0')) {
                    $xml .= '      <c r="' . $cellRef . '"' . $sAttr . '><v>' . $val . '</v></c>' . "\n";
                } else {
                    $escaped = htmlspecialchars((string) $val, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                    $xml .= '      <c r="' . $cellRef . '" t="inlineStr"' . $sAttr . '><is><t>' . $escaped . '</t></is></c>' . "\n";
                }
                $cIndex++;
            }
            $xml .= '    </row>' . "\n";
            $rIndex++;
        }

        $xml .= '  </sheetData>' . "\n";
        $xml .= '</worksheet>';

        return $xml;
    }

    private function getColumnLetter(int $colIndex): string
    {
        $letter = '';
        while ($colIndex > 0) {
            $mod = ($colIndex - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $colIndex = (int) (($colIndex - $mod) / 26);
        }
        return $letter;
    }

    /**
     * Menghasilkan dokumen PDF Berita Acara Reviu/Skrining RUP per OPD siap cetak/tanda tangan.
     *
     * @param int $batchId
     * @param string $kodeSatkerOrOpd
     * @return string Konten biner PDF
     */
    public function generateBeritaAcaraPdf(int $batchId, string $kodeSatkerOrOpd): string
    {
        $batch = $this->auditService->getBatch($batchId);
        if (! $batch) {
            throw new \RuntimeException('Batch audit tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $opd = $db->table('rup_audit_opd_summaries')
            ->where('batch_id', $batchId)
            ->groupStart()
                ->where('kode_satker_sipd', $kodeSatkerOrOpd)
                ->orWhere('nama_opd_clean', $kodeSatkerOrOpd)
            ->groupEnd()
            ->get()->getRowArray();

        if (! $opd) {
            throw new \RuntimeException('Data satuan kerja tidak ditemukan dalam batch audit ini.');
        }

        $packages = $this->auditService->getPackagesFiltered($batchId, [
            'opd'      => $opd['nama_opd_clean'],
            'severity' => 'ALL',
        ], 200, 0);

        $html = view('main/workspace/pages/rup_audit/pdf_berita_acara', [
            'batch'    => $batch,
            'opd'      => $opd,
            'packages' => $packages,
            'printedAt'=> date('d F Y'),
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->addInfo('Title', 'Berita Acara Skrining RUP - ' . $opd['nama_opd_clean']);
        $dompdf->addInfo('Author', 'Pemerintah Daerah - Unit Kerja Pengadaan Barang/Jasa');
        $dompdf->addInfo('Subject', 'Dokumen Pengawasan Tata Kelola RUP Sesuai Perpres 46/2025');

        return (string) $dompdf->output();
    }
}

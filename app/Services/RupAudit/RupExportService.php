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
     * tanpa memerlukan pustaka PhpSpreadsheet eksternal.
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

        $opdList    = $this->auditService->getOpdLeaderboard($batchId);
        $kritisTinggi = $this->auditService->getPackagesFiltered($batchId, ['severity' => 'KRITIS'], 1000, 0);
        $tinggiList   = $this->auditService->getPackagesFiltered($batchId, ['severity' => 'TINGGI'], 1000, 0);
        $mergedKritisTinggi = array_merge($kritisTinggi, $tinggiList);

        $clusters   = $this->auditService->getClusters($batchId);
        $allPackages = $this->auditService->getPackagesFiltered($batchId, [], 5000, 0);

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

        // 4. xl/workbook.xml
        $wbXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Leaderboard RCI OPD" sheetId="1" r:id="rId1"/>
    <sheet name="Temuan Kritis &amp; Tinggi" sheetId="2" r:id="rId2"/>
    <sheet name="Matriks Klaster Splitting" sheetId="3" r:id="rId3"/>
    <sheet name="Daftar Lengkap Anomali" sheetId="4" r:id="rId4"/>
    <sheet name="Diff-Tracking Log" sheetId="5" r:id="rId5"/>
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

        // 6. Build Sheet 1: Leaderboard RCI OPD
        $sheet1Rows = [
            ['Peringkat', 'Kode SIPD', 'Nama Satuan Kerja (OPD)', 'Skor RCI', 'Predikat Risiko', 'Total Paket', 'Total Pagu (Rp)', 'Pagu Penyedia (Rp)', 'Pagu Swakelola (Rp)', '% E-Purchasing', '% UMK Penyedia', '% UMK Eligible <=15M', 'Temuan Kritis', 'Temuan Tinggi', 'Pagu Berisiko Kritis/Tinggi (Rp)', 'Macro Flags'],
        ];
        $rank = 1;
        foreach ($opdList as $opd) {
            $sheet1Rows[] = [
                $rank++,
                $opd['kode_satker_sipd'],
                $opd['nama_opd_clean'],
                $opd['skor_rci'],
                $opd['predikat_risiko'],
                $opd['total_paket'],
                $opd['total_pagu'],
                $opd['pagu_penyedia'],
                $opd['pagu_swakelola'],
                $opd['persen_epurchasing'] . '%',
                $opd['persen_umk_penyedia'] . '%',
                $opd['persen_umk_eligible_le15m'] . '%',
                $opd['temuan_kritis'],
                $opd['temuan_tinggi'],
                $opd['pagu_bermasalah_kritis_tinggi'],
                $opd['macro_flags'],
            ];
        }
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildSheetXml($sheet1Rows));

        // 7. Build Sheet 2: Temuan Kritis & Tinggi
        $sheet2Rows = [
            ['No', 'Kode RUP', 'Nama Satuan Kerja (OPD)', 'Siklus', 'Nama Paket Pengadaan', 'Metode', 'Jenis', 'Pagu RUP (Rp)', 'Tingkat Risiko', 'Status UMK', 'PDN', 'Status Diff'],
        ];
        $no = 1;
        foreach ($mergedKritisTinggi as $pkg) {
            $sheet2Rows[] = [
                $no++,
                $pkg['kode_rup'],
                $pkg['nama_opd_clean'],
                $pkg['budget_cycle'],
                $pkg['nama_paket'],
                $pkg['metode_pengadaan'],
                $pkg['jenis_pengadaan'],
                $pkg['total_nilai'],
                $pkg['max_severity'],
                $pkg['status_umk'],
                $pkg['produk_dalam_negeri'],
                $pkg['diff_status'],
            ];
        }
        $zip->addFromString('xl/worksheets/sheet2.xml', $this->buildSheetXml($sheet2Rows));

        // 8. Build Sheet 3: Matriks Klaster Splitting & Konsolidasi
        $sheet3Rows = [
            ['No', 'ID Klaster', 'Nama Satuan Kerja (OPD)', 'Siklus', 'Komoditas Klaster', 'Jenis', 'Jumlah Paket', 'Total Pagu Klaster (Rp)', 'Batas Regulasi PL (Rp)', 'Kategori Temuan', 'Tingkat Risiko', 'Daftar Kode RUP Terpecah', 'Instruksi Konsolidasi'],
        ];
        $no = 1;
        foreach ($clusters as $cls) {
            $sheet3Rows[] = [
                $no++,
                $cls['cluster_uuid'],
                $cls['nama_opd_clean'],
                $cls['budget_cycle'],
                $cls['nama_komoditas_klaster'],
                $cls['jenis_pengadaan'],
                $cls['jumlah_paket_terpecah'],
                $cls['total_pagu_klaster'],
                $cls['batas_regulasi_pl'],
                $cls['kategori_temuan'],
                $cls['severity'],
                $cls['daftar_kode_rup'],
                $cls['instruksi_konsolidasi'],
            ];
        }
        $zip->addFromString('xl/worksheets/sheet3.xml', $this->buildSheetXml($sheet3Rows));

        // 9. Build Sheet 4: Daftar Lengkap Anomali
        $sheet4Rows = [
            ['No', 'Row CSV', 'Kode RUP', 'Nama Satuan Kerja (OPD)', 'Nama Paket', 'Objek Belanja Utama', 'Cara', 'Metode', 'Jenis', 'Pagu (Rp)', 'Tingkat Risiko', 'Jumlah Pelanggaran', 'Status Diff'],
        ];
        $no = 1;
        foreach ($allPackages as $pkg) {
            if ($pkg['max_severity'] === 'AMAN') {
                continue;
            }
            $sheet4Rows[] = [
                $no++,
                $pkg['row_index_csv'],
                $pkg['kode_rup'],
                $pkg['nama_opd_clean'],
                $pkg['nama_paket'],
                $pkg['nama_paket_objek'],
                $pkg['cara_pengadaan'],
                $pkg['metode_pengadaan'],
                $pkg['jenis_pengadaan'],
                $pkg['total_nilai'],
                $pkg['max_severity'],
                $pkg['violation_count'],
                $pkg['diff_status'],
            ];
        }
        $zip->addFromString('xl/worksheets/sheet4.xml', $this->buildSheetXml($sheet4Rows));

        // 10. Build Sheet 5: Diff-Tracking Log
        $macro = json_decode((string) ($batch['macro_summary'] ?? '{}'), true);
        $diff = $macro['diff_tracking'] ?? [];
        $sheet5Rows = [
            ['Indikator Diff-Tracking Antar-Versi', 'Nilai / Status'],
            ['Batch ID Terkini', $batch['batch_uuid']],
            ['Batch ID Pembanding Sebelumnya', $batch['previous_batch_id'] ?: 'Tidak Ada (Unggahan Pertama)'],
            ['Status Pembandingan Aktif', ! empty($diff['is_comparison_active']) ? 'AKTIF' : 'NON-AKTIF'],
            ['Total Anomali Batch Sebelumnya', $diff['total_anomali_batch_lama'] ?? 0],
            ['Total Anomali Berhasil Diperbaiki (FIXED)', $diff['total_anomali_telah_diperbaiki_fixed'] ?? 0],
            ['Total Anomali Masih Bertahan (PERSISTENT)', $diff['total_anomali_masih_bertahan_persistent'] ?? 0],
            ['Total Anomali Baru Terdeteksi (NEW)', $diff['total_anomali_baru_muncul_new'] ?? 0],
            ['Delta Skor RCI Instansi', $diff['delta_skor_rci_instansi'] ?? '+0.00'],
            ['Waktu Audit Dilakukan', $batch['created_at']],
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
     * Menghasilkan dokumen PDF Berita Acara Reviu RUP per OPD siap cetak/tanda tangan.
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

        // Ambil anomali kritis dan tinggi pada OPD ini
        $packages = $this->auditService->getPackagesFiltered($batchId, [
            'opd'      => $opd['nama_opd_clean'],
            'severity' => 'ALL',
        ], 200, 0);

        // Ambil data pejabat penandatangan default
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

        $dompdf->addInfo('Title', 'Berita Acara Reviu Kepatuhan RUP - ' . $opd['nama_opd_clean']);
        $dompdf->addInfo('Author', 'Pemerintah Provinsi Lampung - Biro Pengadaan Barang dan Jasa');
        $dompdf->addInfo('Subject', 'Dokumen Pengawasan Tata Kelola RUP Sesuai Perpres 46/2025');

        return (string) $dompdf->output();
    }
}

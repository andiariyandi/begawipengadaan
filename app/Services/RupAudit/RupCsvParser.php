<?php

namespace App\Services\RupAudit;

class RupCsvParser
{
    private const CORE_HEADERS = [
        'nama_instansi'       => ['nama_instansi', 'instansi', 'kldi', 'nama_kldi'],
        'nama_satuan_kerja'   => ['nama_satuan_kerja', 'satuan_kerja', 'satker', 'nama_satker', 'opd', 'skpd'],
        'tahun_anggaran'      => ['tahun_anggaran', 'tahun', 'ta'],
        'cara_pengadaan'      => ['cara_pengadaan', 'swakelola_penyedia', 'tipe_pengadaan'],
        'metode_pengadaan'    => ['metode_pengadaan', 'metode', 'metode_pemilihan'],
        'jenis_pengadaan'     => ['jenis_pengadaan', 'jenis', 'jenis_belanja'],
        'nama_paket'          => ['nama_paket', 'paket', 'nama_kegiatan', 'uraian_pekerjaan'],
        'kode_rup'            => ['kode_rup', 'id_rup', 'rup_id', 'id_paket'],
        'sumber_dana'         => ['sumber_dana', 'dana', 'asal_dana'],
        'produk_dalam_negeri' => ['produk_dalam_negeri', 'pdn', 'status_pdn', 'dalam_negeri'],
        'status_umk'          => ['status_umk', 'umk', 'usaha_kecil', 'alokasi_umk'],
        'total_nilai'         => ['total_nilai', 'pagu', 'nilai_pagu', 'pagu_rup', 'total_pagu', 'nilai'],
    ];

    private const EXTENDED_HEADERS = [
        'tanggal_umumkan'      => ['tanggal_umumkan', 'tgl_umumkan', 'tgl_pengumuman', 'tanggal_pengumuman'],
        'jadwal_pemilihan_awal'=> ['jadwal_pemilihan_awal', 'pemilihan_awal', 'awal_pemilihan'],
        'jadwal_kontrak_awal'  => ['jadwal_kontrak_awal', 'kontrak_awal', 'awal_kontrak'],
        'jadwal_kontrak_akhir' => ['jadwal_kontrak_akhir', 'kontrak_akhir', 'akhir_kontrak'],
        'tipe_swakelola'       => ['tipe_swakelola', 'tipe_swakelola_nama'],
        'lokasi_pekerjaan'     => ['lokasi_pekerjaan', 'lokasi', 'tempat_pelaksanaan'],
        'kode_rekening_mak'    => ['kode_rekening_mak', 'kode_rekening', 'mak', 'kode_mak', 'rekening_belanja'],
        'pagu_pengadaan_dpa'   => ['pagu_pengadaan_dpa', 'pagu_dpa', 'nilai_dpa', 'target_dpa'],
    ];

    /**
     * Parse CSV langsung dari raw string konten (berguna untuk testing atau stream).
     *
     * @param string $csvContent
     * @return array
     */
    public static function parseString(string $csvContent): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'rup_csv_');
        file_put_contents($tmp, $csvContent);
        $parser = new self();
        $res = $parser->parse($tmp);
        @unlink($tmp);
        return $res['rows'] ?? [];
    }

    /**
     * Parse file CSV dan validasi skema.
     * Mengembalikan data ternormalisasi dan metadata parsing.
     *
     * @param string $filePath
     * @return array{
     *   success: bool,
     *   schema_mode: string,
     *   headers: array,
     *   rows: array,
     *   total_raw_rows: int,
     *   error?: string
     * }
     */
    public function parse(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [
                'success'        => false,
                'schema_mode'    => 'INVALID',
                'headers'        => [],
                'rows'           => [],
                'total_raw_rows' => 0,
                'error'          => 'Berkas CSV tidak ditemukan atau tidak dapat dibaca.',
            ];
        }

        $delimiter = $this->detectDelimiter($filePath);
        $handle    = fopen($filePath, 'r');
        if (! $handle) {
            return [
                'success'        => false,
                'schema_mode'    => 'INVALID',
                'headers'        => [],
                'rows'           => [],
                'total_raw_rows' => 0,
                'error'          => 'Gagal membuka stream berkas CSV.',
            ];
        }

        // Baca baris header
        $rawHeader = fgetcsv($handle, 0, $delimiter);
        if (! $rawHeader) {
            fclose($handle);
            return [
                'success'        => false,
                'schema_mode'    => 'INVALID',
                'headers'        => [],
                'rows'           => [],
                'total_raw_rows' => 0,
                'error'          => 'Berkas CSV kosong atau tidak memiliki baris header yang valid.',
            ];
        }

        // Bersihkan UTF-8 BOM jika ada
        if (isset($rawHeader[0])) {
            $rawHeader[0] = preg_replace('/^\xEF\xBB\xBF/', '', $rawHeader[0]);
        }

        // Petakan header ke canonical tags
        $headerMap = $this->mapHeaders($rawHeader);
        $missingCore = [];
        foreach (self::CORE_HEADERS as $tag => $aliases) {
            if (! isset($headerMap[$tag])) {
                $missingCore[] = $tag;
            }
        }

        if (! empty($missingCore)) {
            fclose($handle);
            return [
                'success'        => false,
                'schema_mode'    => 'INVALID',
                'headers'        => $headerMap,
                'rows'           => [],
                'total_raw_rows' => 0,
                'error'          => 'Berkas CSV tidak memenuhi 12 kolom wajib SIRUP. Kolom yang hilang: ' . implode(', ', $missingCore),
            ];
        }

        // Tentukan apakah EXTENDED_20_COLS atau CORE_12_COLS
        $hasExtended = false;
        foreach (self::EXTENDED_HEADERS as $tag => $aliases) {
            if (isset($headerMap[$tag])) {
                $hasExtended = true;
                break;
            }
        }
        $schemaMode = $hasExtended ? 'EXTENDED_20_COLS' : 'CORE_12_COLS';

        $parsedRows  = [];
        $rawRowCount = 0;
        $rupTruncatedCounts = []; // Untuk mengecek anomali scientific truncated

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rawRowCount++;
            // Lewati baris kosong
            if (empty(array_filter($row, static fn($v) => trim((string) $v) !== ''))) {
                continue;
            }

            $extracted = $this->extractRowData($row, $headerMap, $rawRowCount);
            $parsedRows[] = $extracted;

            // Catat hitungan akhiran 00 untuk scientific check
            $opdClean = $extracted['nama_opd_clean'];
            if (! isset($rupTruncatedCounts[$opdClean])) {
                $rupTruncatedCounts[$opdClean] = ['total' => 0, 'ends_00' => 0];
            }
            $rupTruncatedCounts[$opdClean]['total']++;
            if (strlen($extracted['kode_rup']) >= 8 && str_ends_with($extracted['kode_rup'], '00')) {
                $rupTruncatedCounts[$opdClean]['ends_00']++;
            }
        }

        fclose($handle);

        // Tandai flag is_scientific_truncated jika proporsi kode_rup berakhiran "00" dalam OPD > 50%
        foreach ($parsedRows as &$pRow) {
            $opd = $pRow['nama_opd_clean'];
            $st = $rupTruncatedCounts[$opd] ?? null;
            if ($st && $st['total'] >= 5 && ($st['ends_00'] / $st['total']) > 0.50) {
                if (strlen($pRow['kode_rup']) >= 8 && str_ends_with($pRow['kode_rup'], '00')) {
                    $pRow['is_scientific_truncated'] = 1;
                }
            }
        }
        unset($pRow);

        return [
            'success'        => true,
            'schema_mode'    => $schemaMode,
            'headers'        => $headerMap,
            'rows'           => $parsedRows,
            'total_raw_rows' => $rawRowCount,
        ];
    }

    /**
     * Ekstraksi dan sanitasi satu baris data CSV.
     */
    private function extractRowData(array $row, array $headerMap, int $rowIndex): array
    {
        $getVal = static function (string $tag, string $default = '') use ($row, $headerMap): string {
            if (! isset($headerMap[$tag])) {
                return $default;
            }
            $idx = $headerMap[$tag];
            return isset($row[$idx]) ? trim((string) $row[$idx]) : $default;
        };

        $namaInstansi = strtoupper($getVal('nama_instansi', 'PEMERINTAH PROVINSI LAMPUNG'));
        $rawSatker    = $getVal('nama_satuan_kerja');
        $opdSipd      = RupNlpExtractor::extractOpdAndSipd($rawSatker);

        $rawTahun     = $getVal('tahun_anggaran', (string) date('Y'));
        $tahunAnggaran = (int) preg_replace('/[^0-9]/', '', substr($rawTahun, 0, 4)) ?: (int) date('Y');
        $isMultiyears = str_contains($rawTahun, ';') ? 1 : 0;

        $caraPengadaan = ucwords(strtolower($getVal('cara_pengadaan', 'Penyedia')));
        if ($caraPengadaan !== 'Swakelola') {
            $caraPengadaan = 'Penyedia';
        }

        // Metode pengadaan
        $rawMetode = $getVal('metode_pengadaan', '-');
        $metodeArr = array_values(array_filter(array_map('trim', explode(';', $rawMetode))));
        if (empty($metodeArr)) {
            $metodeArr = [$rawMetode];
        }

        // Jenis pengadaan
        $rawJenis = $getVal('jenis_pengadaan', '-');
        $jenisArr = array_values(array_filter(array_map('trim', explode(';', $rawJenis))));
        if (empty($jenisArr)) {
            $jenisArr = [$rawJenis];
        }
        $isIntegrated = (count($jenisArr) > 1 || in_array('Terintegrasi', $jenisArr, true)) ? 1 : 0;

        // Kode RUP diproses strictly sebagai string
        $rawKodeRup = $getVal('kode_rup', '');
        // Bersihkan desimal .0 dari export float Excel
        $kodeRupClean = preg_replace('/\.0+$/', '', $rawKodeRup);
        $kodeRupClean = trim((string) $kodeRupClean);

        // Sumber Dana
        $rawSumberDana = $getVal('sumber_dana', 'APBD');
        $sumberDanaArr = array_values(array_filter(array_map('trim', explode(';', $rawSumberDana))));
        if (empty($sumberDanaArr)) {
            $sumberDanaArr = [$rawSumberDana];
        }

        $sumberDanaUpper = strtoupper($rawSumberDana);
        $budgetCycle     = (str_contains($sumberDanaUpper, 'APBDP') || str_contains($sumberDanaUpper, 'APBD-P')) ? 'PERUBAHAN' : 'MURNI';
        $isBlud          = str_contains($sumberDanaUpper, 'BLUD') ? 1 : 0;
        $isDak           = (str_contains($sumberDanaUpper, 'DAK') || str_contains($sumberDanaUpper, 'BOK')) ? 1 : 0;

        // Nama paket & NLP
        $namaPaket = $getVal('nama_paket', '');
        $namaPaketClean = RupNlpExtractor::clean($namaPaket);
        $namaPaketObjek = RupNlpExtractor::extractObject($namaPaket);
        $coreData       = RupNlpExtractor::extractCoreAndLocation($namaPaketObjek);

        // Sanitasi total_nilai
        $rawTotal = trim($getVal('total_nilai', '0'));
        $totalNilai = 0.00;
        if (preg_match('/^[+-]?\d+(\.\d+)?[eE][+-]?\d+$/', $rawTotal)) {
            $totalNilai = (float) $rawTotal;
        } else {
            // Bersihkan simbol Rp, spasi
            $cleanTotal = str_replace(['Rp', 'rp', 'RP', ' '], '', $rawTotal);
            // Tangani titik ribuan vs koma desimal
            if (substr_count($cleanTotal, '.') > 1 || (str_contains($cleanTotal, '.') && str_contains($cleanTotal, ','))) {
                $cleanTotal = str_replace('.', '', $cleanTotal);
                $cleanTotal = str_replace(',', '.', $cleanTotal);
            } elseif (str_contains($cleanTotal, ',')) {
                $cleanTotal = str_replace(',', '.', $cleanTotal);
            }
            $totalNilai = is_numeric($cleanTotal) ? (float) $cleanTotal : 0.00;
        }

        $pdn = ucwords(strtolower($getVal('produk_dalam_negeri', 'Ya')));
        $umk = ucwords(strtolower($getVal('status_umk', 'Ya')));

        // Kolom opsional
        $tglUmumkan = $getVal('tanggal_umumkan');
        $jadwalPilih = $getVal('jadwal_pemilihan_awal');
        $kontrakAwal = $getVal('jadwal_kontrak_awal');
        $kontrakAkhir = $getVal('jadwal_kontrak_akhir');
        $tipeSwakelola = $getVal('tipe_swakelola') ?: null;
        $lokasiPekerjaan = $getVal('lokasi_pekerjaan') ?: null;
        $kodeMak = $getVal('kode_rekening_mak') ?: null;
        $paguDpa = $getVal('pagu_pengadaan_dpa') !== '' && is_numeric($getVal('pagu_pengadaan_dpa')) ? (float) $getVal('pagu_pengadaan_dpa') : null;

        return [
            'row_index_csv'          => $rowIndex,
            'kode_rup'               => $kodeRupClean,
            'nama_instansi'          => $namaInstansi,
            'nama_satuan_kerja'      => $rawSatker,
            'nama_opd_clean'         => $opdSipd['nama_opd_clean'],
            'kode_satker_sipd'       => $opdSipd['kode_satker_sipd'],
            'tahun_anggaran'         => $tahunAnggaran,
            'cara_pengadaan'         => $caraPengadaan,
            'metode_pengadaan'       => $rawMetode,
            'metode_arr'             => $metodeArr,
            'jenis_pengadaan'        => $rawJenis,
            'jenis_arr'              => $jenisArr,
            'nama_paket'             => $namaPaket,
            'nama_paket_clean'       => $namaPaketClean,
            'nama_paket_objek'       => $namaPaketObjek,
            'nama_paket_core'        => $coreData['nama_paket_core'],
            'tokens'                 => $coreData['tokens'],
            'token_count'            => $coreData['token_count'],
            'lokasi_extracted'       => $coreData['lokasi_extracted'],
            'sumber_dana'            => $rawSumberDana,
            'sumber_dana_arr'        => $sumberDanaArr,
            'budget_cycle'           => $budgetCycle,
            'is_blud'                => $isBlud,
            'is_dak'                 => $isDak,
            'is_multiyears'          => $isMultiyears,
            'is_scientific_truncated'=> 0, // dihitung kemudian di batch level
            'is_integrated_package'  => $isIntegrated,
            'produk_dalam_negeri'    => $pdn,
            'status_umk'             => $umk,
            'total_nilai'            => $totalNilai,
            'cluster_id'             => null,
            'max_severity'           => 'AMAN',
            'violation_count'        => 0,
            'diff_status'            => 'NEW',
            'tanggal_umumkan'        => ! empty($tglUmumkan) ? date('Y-m-d', strtotime($tglUmumkan)) : null,
            'jadwal_pemilihan_awal'  => ! empty($jadwalPilih) ? date('Y-m-d', strtotime($jadwalPilih)) : null,
            'jadwal_kontrak_awal'    => ! empty($kontrakAwal) ? date('Y-m-d', strtotime($kontrakAwal)) : null,
            'jadwal_kontrak_akhir'   => ! empty($kontrakAkhir) ? date('Y-m-d', strtotime($kontrakAkhir)) : null,
            'tipe_swakelola'         => $tipeSwakelola,
            'lokasi_pekerjaan'       => $lokasiPekerjaan,
            'kode_rekening_mak'      => $kodeMak,
            'pagu_pengadaan_dpa'     => $paguDpa,
        ];
    }

    /**
     * Deteksi pemisah CSV (, atau ;)
     */
    private function detectDelimiter(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return ',';
        }
        $firstLine = fgets($handle, 4096) ?: '';
        fclose($handle);

        $commaCount     = substr_count($firstLine, ',');
        $semicolonCount = substr_count($firstLine, ';');

        return $semicolonCount > $commaCount ? ';' : ',';
    }

    /**
     * Memetakan nama kolom CSV asli ke canonical tags
     */
    private function mapHeaders(array $rawHeaders): array
    {
        $map = [];
        $allDefinitions = array_merge(self::CORE_HEADERS, self::EXTENDED_HEADERS);

        foreach ($rawHeaders as $idx => $headerName) {
            $norm = strtolower(trim((string) $headerName));
            $norm = preg_replace('/[^a-z0-9_]/', '_', $norm);
            $norm = trim((string) preg_replace('/_+/', '_', $norm), '_');

            foreach ($allDefinitions as $tag => $aliases) {
                if (in_array($norm, $aliases, true) || $norm === $tag) {
                    $map[$tag] = $idx;
                    break;
                }
            }
        }

        return $map;
    }
}

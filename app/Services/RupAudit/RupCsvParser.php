<?php

namespace App\Services\RupAudit;

class RupCsvParser
{
    /**
     * Kontrak 12 Kolom Inti Wajib SIRUP (Single Source of Truth v4.0)
     */
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

    /**
     * Parse CSV langsung dari raw string konten (berguna untuk testing atau stream).
     *
     * @param string $csvContent
     * @return array
     */
    public static function parseString(string $csvContent): array
    {
        $parser = new self();
        $res = $parser->parseContent($csvContent);
        return $res['rows'] ?? [];
    }

    /**
     * Parse konten string CSV dan mengembalikan seluruh hasil parse (termasuk status dan schema report).
     *
     * @param string $csvContent
     * @return array
     */
    public function parseContent(string $csvContent): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'rup_csv_');
        file_put_contents($tmp, $csvContent);
        $res = $this->parse($tmp);
        @unlink($tmp);
        return $res;
    }

    /**
     * Parse file CSV dan validasi skema 12 kolom inti.
     * Mengembalikan data ternormalisasi dan metadata parsing v4.0.
     *
     * @param string $filePath
     * @return array{
     *   success: bool,
     *   schema_mode: string,
     *   input_schema_version: string,
     *   file_sha256: string,
     *   headers: array,
     *   rows: array,
     *   total_raw_rows: int,
     *   source_row_count: int,
     *   data_quality_summary_json: string,
     *   error?: string
     * }
     */
    public function parse(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [
                'success'                   => false,
                'schema_mode'               => 'INVALID',
                'input_schema_version'      => '4.0',
                'file_sha256'               => '',
                'headers'                   => [],
                'rows'                      => [],
                'total_raw_rows'            => 0,
                'source_row_count'          => 0,
                'data_quality_summary_json' => '{}',
                'error'                     => 'Berkas CSV tidak ditemukan atau tidak dapat dibaca.',
            ];
        }

        $fileSha256 = hash_file('sha256', $filePath) ?: '';
        $delimiter  = $this->detectDelimiter($filePath);
        $handle     = fopen($filePath, 'r');
        if (! $handle) {
            return [
                'success'                   => false,
                'schema_mode'               => 'INVALID',
                'input_schema_version'      => '4.0',
                'file_sha256'               => $fileSha256,
                'headers'                   => [],
                'rows'                      => [],
                'total_raw_rows'            => 0,
                'source_row_count'          => 0,
                'data_quality_summary_json' => '{}',
                'error'                     => 'Gagal membuka stream berkas CSV.',
            ];
        }

        // Baca baris header
        $rawHeader = fgetcsv($handle, 0, $delimiter);
        if (! $rawHeader) {
            fclose($handle);
            return [
                'success'                   => false,
                'schema_mode'               => 'INVALID',
                'input_schema_version'      => '4.0',
                'file_sha256'               => $fileSha256,
                'headers'                   => [],
                'rows'                      => [],
                'total_raw_rows'            => 0,
                'source_row_count'          => 0,
                'data_quality_summary_json' => '{}',
                'error'                     => 'Berkas CSV kosong atau tidak memiliki baris header yang valid.',
            ];
        }

        // Bersihkan UTF-8 BOM jika ada
        if (isset($rawHeader[0])) {
            $rawHeader[0] = preg_replace('/^\xEF\xBB\xBF/', '', $rawHeader[0]);
        }

        // Petakan header ke 12 canonical tags inti
        $headerMap = $this->mapHeaders($rawHeader);
        $missingCore = [];
        foreach (self::CORE_HEADERS as $tag => $aliases) {
            if (! isset($headerMap[$tag])) {
                $missingCore[] = $tag;
            }
        }

        // Gate Wajib 12 Kolom Inti: Tolak batch jika ada kolom inti yang hilang
        if (! empty($missingCore)) {
            fclose($handle);
            return [
                'success'                   => false,
                'schema_mode'               => 'INVALID',
                'input_schema_version'      => '4.0',
                'file_sha256'               => $fileSha256,
                'headers'                   => $headerMap,
                'rows'                      => [],
                'total_raw_rows'            => 0,
                'source_row_count'          => 0,
                'data_quality_summary_json' => '{}',
                'error'                     => 'Berkas CSV tidak memenuhi 12 kolom wajib SIRUP. Kolom yang hilang: ' . implode(', ', $missingCore),
            ];
        }

        $schemaMode = 'RUP_CORE_12_COLS';

        // Deteksi kolom ekstra yang diabaikan
        $extraColumns = [];
        $mappedIndices = array_values($headerMap);
        foreach ($rawHeader as $idx => $headerName) {
            if (! in_array($idx, $mappedIndices, true)) {
                $extraColumns[] = trim((string) $headerName);
            }
        }

        $schemaReport = [
            'detected_headers'      => $rawHeader,
            'mapped_core_headers'   => $headerMap,
            'extra_columns_ignored' => $extraColumns,
            'detected_delimiter'    => $delimiter,
        ];

        $parsedRows  = [];
        $rawRowCount = 0;
        $rupTruncatedCounts = [];
        $zeroOrNegativeCount = 0;
        $multiSourceCount = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rawRowCount++;
            // Lewati baris kosong
            if (empty(array_filter($row, static fn($v) => trim((string) $v) !== ''))) {
                continue;
            }

            $extracted = $this->extractRowData($row, $headerMap, $rawRowCount);
            $parsedRows[] = $extracted;

            if ($extracted['total_nilai'] <= 0) {
                $zeroOrNegativeCount++;
            }
            if ($extracted['is_multi_source'] === 1) {
                $multiSourceCount++;
            }

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

        $scientificTruncatedCount = 0;
        // Tandai flag is_scientific_truncated jika proporsi kode_rup berakhiran "00" dalam OPD > 50%
        foreach ($parsedRows as &$pRow) {
            $opd = $pRow['nama_opd_clean'];
            $st = $rupTruncatedCounts[$opd] ?? null;
            if ($st && $st['total'] >= 5 && ($st['ends_00'] / $st['total']) > 0.50) {
                if (strlen($pRow['kode_rup']) >= 8 && str_ends_with($pRow['kode_rup'], '00')) {
                    $pRow['is_scientific_truncated'] = 1;
                    $scientificTruncatedCount++;
                }
            }
        }
        unset($pRow);

        $dataQualitySummary = [
            'total_raw_rows'           => $rawRowCount,
            'valid_package_rows'       => count($parsedRows),
            'pagu_zero_or_negative'    => $zeroOrNegativeCount,
            'multi_source_packages'    => $multiSourceCount,
            'scientific_truncated_rup' => $scientificTruncatedCount,
        ];

        return [
            'success'                   => true,
            'schema_mode'               => $schemaMode,
            'input_schema_version'      => '4.0',
            'file_sha256'               => $fileSha256,
            'headers'                   => $headerMap,
            'schema_report'             => $schemaReport,
            'rows'                      => $parsedRows,
            'total_raw_rows'            => $rawRowCount,
            'source_row_count'          => count($parsedRows),
            'data_quality_summary_json' => json_encode($dataQualitySummary),
        ];
    }

    /**
     * Ekstraksi dan sanitasi satu baris data CSV berbasis 12 Kolom Inti.
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

        // Penentuan ketentuan regulasi yang berlaku berdasarkan tahun anggaran
        $regimeStatus = match (true) {
            $tahunAnggaran === 2026 => 'KETENTUAN_2026_PERPRES_46_2025',
            $tahunAnggaran === 2025 => 'KETENTUAN_2025_TRANSISI',
            $tahunAnggaran >= 2021 && $tahunAnggaran <= 2024 => 'KETENTUAN_2021_PERPRES_12_2021',
            default => 'KETENTUAN_LAINNYA',
        };

        $rawCara = $getVal('cara_pengadaan', 'Penyedia');
        $caraPengadaan = ucwords(strtolower($rawCara));
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

        // Sumber Dana & Profil Pendanaan Multi-Tag
        $rawSumberDana = $getVal('sumber_dana', 'APBD');
        $sumberDanaArr = array_values(array_filter(array_map('trim', explode(';', $rawSumberDana))));
        if (empty($sumberDanaArr)) {
            $sumberDanaArr = [$rawSumberDana];
        }

        // Pertahankan kombinasi multi-tag seperti APBD;APBDP -> APBD+APBDP
        $cleanTags = array_unique(array_map('strtoupper', $sumberDanaArr));
        sort($cleanTags);
        $sourceFundingProfile = implode('+', $cleanTags) ?: 'APBD';
        $isMultiSource = count($cleanTags) > 1 ? 1 : 0;

        $sumberDanaUpper = strtoupper($rawSumberDana);
        $budgetCycle     = (str_contains($sumberDanaUpper, 'APBDP') || str_contains($sumberDanaUpper, 'APBD-P')) ? 'PERUBAHAN' : 'MURNI';
        $isBlud          = str_contains($sumberDanaUpper, 'BLUD') ? 1 : 0;
        $isDak           = (str_contains($sumberDanaUpper, 'DAK') || str_contains($sumberDanaUpper, 'BOK')) ? 1 : 0;

        // Nama paket & NLP
        $namaPaket = $getVal('nama_paket', '');
        $serialAndLoc = RupNlpExtractor::extractSerialAndLocation($namaPaket);
        $namaPaketClean = RupNlpExtractor::clean($namaPaket);
        $namaPaketObjek = RupNlpExtractor::extractObject($namaPaket);
        $coreData       = RupNlpExtractor::extractCoreAndLocation($namaPaketObjek);

        $serialSeqNumber     = $serialAndLoc['serial_sequence_number'];
        $locationIdentityKey = $serialAndLoc['location_identity_key'] ?: $coreData['lokasi_extracted'];
        $serialFamilyKey     = RupNlpExtractor::generateSerialFamilyKey(
            $opdSipd['nama_opd_clean'],
            $rawJenis,
            $sourceFundingProfile,
            $coreData['nama_paket_core'],
            $locationIdentityKey
        );

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

        return [
            'row_index_csv'                  => $rowIndex,
            'kode_rup'                       => $kodeRupClean,
            'nama_instansi'                  => $namaInstansi,
            'nama_satuan_kerja'              => $rawSatker,
            'nama_opd_clean'                 => $opdSipd['nama_opd_clean'],
            'kode_satker_sipd'               => $opdSipd['kode_satker_sipd'],
            'tahun_anggaran'                 => $tahunAnggaran,
            'regime_status'                  => $regimeStatus,
            'cara_pengadaan'                 => $caraPengadaan,
            'metode_pengadaan'               => $rawMetode,
            'metode_arr'                     => $metodeArr,
            'jenis_pengadaan'                => $rawJenis,
            'jenis_arr'                      => $jenisArr,
            'nama_paket'                     => $namaPaket,
            'nama_paket_clean'               => $namaPaketClean,
            'nama_paket_objek'               => $namaPaketObjek,
            'nama_paket_core'                => $coreData['nama_paket_core'],
            'tokens'                         => $coreData['tokens'],
            'token_count'                    => $coreData['token_count'],
            'lokasi_extracted'               => $coreData['lokasi_extracted'],
            'serial_sequence_number'         => $serialSeqNumber,
            'location_identity_key'          => $locationIdentityKey,
            'serial_family_key'              => $serialFamilyKey,
            'sumber_dana'                    => $rawSumberDana,
            'sumber_dana_arr'                => $sumberDanaArr,
            'source_funding_profile'         => $sourceFundingProfile,
            'is_multi_source'                => $isMultiSource,
            'budget_cycle'                   => $budgetCycle,
            'is_blud'                        => $isBlud,
            'is_dak'                         => $isDak,
            'is_multiyears'                  => $isMultiyears,
            'is_scientific_truncated'        => 0, // dihitung kemudian di batch level
            'is_integrated_package'          => $isIntegrated,
            'produk_dalam_negeri'            => $pdn,
            'status_umk'                     => $umk,
            'total_nilai'                    => $totalNilai,
            'cluster_id'                     => null,
            'max_severity'                   => 'AMAN',
            'violation_count'                => 0,
            'diff_status'                    => 'NEW',
            'previous_kode_rup'              => null,
            'normalized_classification_json' => json_encode([
                'cara_norm'    => $caraPengadaan,
                'metode_norm'  => $metodeArr,
                'jenis_norm'   => $jenisArr,
                'funding_norm' => $sourceFundingProfile,
            ]),
            'data_quality_flags_json'        => json_encode([
                'pagu_is_invalid' => ($totalNilai <= 0) ? 1 : 0,
                'is_multi_source' => $isMultiSource,
                'has_serial'      => ! empty($serialSeqNumber) ? 1 : 0,
                'has_location'    => ! empty($locationIdentityKey) ? 1 : 0,
            ]),
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
     * Memetakan nama kolom CSV asli ke 12 canonical tags inti
     */
    private function mapHeaders(array $rawHeaders): array
    {
        $map = [];

        foreach ($rawHeaders as $idx => $headerName) {
            $norm = strtolower(trim((string) $headerName));
            $norm = preg_replace('/[^a-z0-9_]/', '_', $norm);
            $norm = trim((string) preg_replace('/_+/', '_', $norm), '_');

            foreach (self::CORE_HEADERS as $tag => $aliases) {
                if (in_array($norm, $aliases, true) || $norm === $tag) {
                    $map[$tag] = $idx;
                    break;
                }
            }
        }

        return $map;
    }
}

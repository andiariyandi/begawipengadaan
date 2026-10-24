<?php

namespace App\Services;

/**
 * CertificatePdfPlaceholderService — mesin pendeteksi & pembersih {{placeholder}} di dalam file PDF.
 *
 * Cara kerja:
 * 1. `inspectPdf(string $pdfPath)`:
 *    - Membaca halaman pertama PDF menggunakan `CleanTemplateFpdi` + `ExtendedPdfParser`
 *      (mendukung PDF 1.4 s.d. PDF 1.7 termasuk Cross-Reference Stream `/Type /XRef` dan `/Type /ObjStm`
 *      hasil ekspor Canva, Microsoft Word, PowerPoint, Figma, maupun Adobe Illustrator).
 *    - Mengekstrak ukuran halaman (`width_pt`, `height_pt`, `width_mm`, `height_mm`, `orientation`).
 *    - Mengekstrak peta karakter `/ToUnicode` CMap dan lebar glyph `/W` pada setiap font PDF.
 *    - Menjalankan state-machine parser pada operator PDF (`q`, `Q`, `cm`, `BT`, `ET`, `Tf`, `Tm`,
 *      `Td`, `TD`, `T*`, `Tj`, `TJ`, `rg`, `g`, `k`, `sc`, `scn`) untuk menghitung posisi X & Y (dalam %),
 *      ukuran font (pt), ketebalan (normal/bold), warna teks (hex), dan perataan (left/center/right)
 *      dari setiap tag `{{...}}` yang diketik di dalam PDF.
 *
 * 2. `stripPlaceholdersFromStream(string $stream, array $fontMaps, array $fontWidths)`:
 *    - Menghapus semua teks `{{placeholder}}` (termasuk `{{qr_code}}` / `__QR_BLOCK__`) langsung dari
 *      content stream PDF dasar — baik yang tersimpan sebagai literal ASCII `({{nama_peserta}})`,
 *      2-byte CID octal `(\000\007)...(\000\021)` (Canva/Word/Chrome), maupun `<hex>` string —
 *      sehingga saat sertifikat digenerate, teks `{{...}}` asli hilang bersih dan digantikan oleh
 *      data peserta asli tanpa bertumpuk.
 */
class CertificatePdfPlaceholderService
{
    /**
     * Daftar placeholder standar yang secara alami menggunakan huruf tebal (bold).
     */
    private const DEFAULT_BOLD_KEYS = [
        'nama_peserta',
        'nama_lengkap',
        'participant_name',
        'nomor_sertifikat',
        'certificate_number',
        'judul_kegiatan',
        'nama_kegiatan',
        'certificate_title',
        'penandatangan_nama',
        'signer_name',
    ];

    /**
     * Daftar tag yang merepresentasikan blok gambar QR Code.
     */
    private const QR_TAGS = [
        '{{qr_code}}',
        '{{qr_block}}',
        '__QR_BLOCK__',
    ];

    /**
     * Inspeksi file PDF dan kembalikan metadata halaman beserta daftar placeholder `{{...}}`
     * yang ditemukan di dalam PDF lengkap dengan koordinat X/Y (%) dan properti tipografinya.
     *
     * @return array{
     *     page_width_pt: float,
     *     page_height_pt: float,
     *     page_width_mm: float,
     *     page_height_mm: float,
     *     orientation: string,
     *     placeholders: array<int, array<string, mixed>>,
     *     raw_text_sample: string
     * }
     */
    public function inspectPdf(string $pdfPath): array
    {
        $defaultResult = [
            'page_width_pt'   => 841.89,
            'page_height_pt'  => 595.28,
            'page_width_mm'   => 297.0,
            'page_height_mm'  => 210.0,
            'orientation'     => 'L',
            'placeholders'    => [],
            'raw_text_sample' => '',
        ];

        if (! is_file($pdfPath)) {
            return $defaultResult;
        }

        try {
            $fpdi      = new CleanTemplateFpdi();
            $pageCount = $fpdi->setSourceFile($pdfPath);
            if ($pageCount < 1) {
                return $defaultResult;
            }

            $pageInfo   = $fpdi->extractFirstPageInspectionData();
            $widthPt    = (float) ($pageInfo['width_pt'] ?? 841.89);
            $heightPt   = (float) ($pageInfo['height_pt'] ?? 595.28);
            $llx        = (float) ($pageInfo['llx'] ?? 0.0);
            $lly        = (float) ($pageInfo['lly'] ?? 0.0);
            $fontMaps   = $pageInfo['font_maps'] ?? [];
            $fontNames  = $pageInfo['font_names'] ?? [];
            $fontWidths = $pageInfo['font_widths'] ?? [];
            $streams    = $pageInfo['streams'] ?? [];

            if ($widthPt <= 10.0) {
                $widthPt = 841.89;
            }
            if ($heightPt <= 10.0) {
                $heightPt = 595.28;
            }

            $widthMm  = round($widthPt * 25.4 / 72.0, 2);
            $heightMm = round($heightPt * 25.4 / 72.0, 2);

            $allRuns = [];
            foreach ($streams as $streamData) {
                $runs = $this->extractTextRunsFromStream(
                    (string) ($streamData['content'] ?? ''),
                    $fontMaps,
                    $fontNames,
                    $fontWidths,
                    $streamData['ctm'] ?? [1.0, 0.0, 0.0, 1.0, 0.0, 0.0],
                    $llx,
                    $lly
                );
                foreach ($runs as $r) {
                    $allRuns[] = $r;
                }
            }

            // Tambahkan juga jika ada AcroForm Widget di halaman 1
            foreach (($pageInfo['acroform_fields'] ?? []) as $field) {
                $allRuns[] = $field;
            }

            $mergedLines  = $this->mergeAdjacentTextRuns($allRuns);
            $placeholders = $this->extractPlaceholdersFromLines($mergedLines, $widthPt, $heightPt);

            // Fallback: bila stream memiliki pola {{...}} yang belum tertangkap oleh state machine
            if (empty($placeholders)) {
                $rawCombined = implode("\n", array_map(static fn ($s) => (string) ($s['content'] ?? ''), $streams));
                $fallbackPh  = $this->fallbackScanRawStream($rawCombined, $widthPt, $heightPt);
                if (! empty($fallbackPh)) {
                    $placeholders = $fallbackPh;
                }
            }

            $sampleTexts = array_map(static fn ($l) => $l['text'], array_slice($mergedLines, 0, 30));

            return [
                'page_width_pt'   => round($widthPt, 2),
                'page_height_pt'  => round($heightPt, 2),
                'page_width_mm'   => $widthMm,
                'page_height_mm'  => $heightMm,
                'orientation'     => $widthPt >= $heightPt ? 'L' : 'P',
                'placeholders'    => $placeholders,
                'raw_text_sample' => implode(' | ', $sampleTexts),
            ];
        } catch (\Throwable $e) {
            log_message('warning', 'CertificatePdfPlaceholderService::inspectPdf fallback: ' . $e->getMessage());
            return $this->inspectPdfRawFallback($pdfPath, $defaultResult);
        }
    }

    /**
     * Membersihkan seluruh token {{placeholder}} dan __QR_BLOCK__ dari PDF content stream.
     * Mendukung:
     * - Literal string ASCII: `(Nomor: {{nomor_sertifikat}}) Tj`
     * - CID 2-byte / 1-byte di dalam TJ array (Canva / Word / Chrome): `[(\000\007)-20(\000\007)...] TJ`
     * - Hex string di dalam TJ / Tj: `[<0007>-20<0007>...] TJ`
     * - Placeholder yang terpecah lintas beberapa operator Tj/TJ dalam satu blok BT ... ET.
     *
     * @param string                            $stream
     * @param array<string, array<int, string>> $fontMaps   [fontKey => [cidInt => utf8Char]]
     * @param array<string, array<int, float>>  $fontWidths [fontKey => [cidInt => widthIn1000Em]]
     */
    public function stripPlaceholdersFromStream(string $stream, array $fontMaps = [], array $fontWidths = []): string
    {
        if ($stream === '') {
            return '';
        }

        // Proses setiap blok BT ... ET dengan melacak font aktif (/f1, /F1, dsb.)
        $stream = preg_replace_callback(
            '/\bBT\b(.*?)\bET\b/s',
            function (array $m) use ($fontMaps, $fontWidths): string {
                return 'BT' . $this->cleanBtBlock($m[1], $fontMaps, $fontWidths) . 'ET';
            },
            $stream
        ) ?? $stream;

        // Bersihkan juga literal ASCII {{...}} yang mungkin berada di luar BT...ET standar
        $stream = preg_replace_callback(
            '/\((?:\\\\.|[^\\\\()])*\)/s',
            function (array $m): string {
                $inner     = substr($m[0], 1, -1);
                $unescaped = $this->unescapePdfLiteral($inner);
                if (! preg_match('/(\{\{\s*[a-zA-Z0-9_.]+\s*\}\}|__QR_BLOCK__)/', $unescaped)) {
                    return $m[0];
                }
                $cleaned = preg_replace('/\{\{\s*[a-zA-Z0-9_.]+\s*\}\}|__QR_BLOCK__/', '', $unescaped);
                return '(' . $this->escapePdfLiteral((string) $cleaned) . ')';
            },
            $stream
        ) ?? $stream;

        return $stream;
    }

    /**
     * Bersihkan seluruh token {{placeholder}} di dalam satu blok BT ... ET.
     *
     * @param array<string, array<int, string>> $fontMaps
     * @param array<string, array<int, float>>  $fontWidths
     */
    private function cleanBtBlock(string $btContent, array $fontMaps, array $fontWidths): string
    {
        // Deteksi font aktif di dalam blok BT ... ET (misal: /f1 17.33 Tf)
        $activeFont = '';
        if (preg_match('~/([A-Za-z0-9_.\-+#]+)\s+[+-]?(?:\d+\.?\d*|\.\d+)\s+Tf\b~s', $btContent, $tfMatch)) {
            $activeFont = $tfMatch[1];
        }

        $activeMap    = ($activeFont !== '' && isset($fontMaps[$activeFont])) ? $fontMaps[$activeFont] : [];
        $activeWidths = ($activeFont !== '' && isset($fontWidths[$activeFont])) ? $fontWidths[$activeFont] : [];

        // 1. Bersihkan array [...] TJ di dalam blok BT ... ET
        $btContent = preg_replace_callback(
            '/\[(.*?)\]\s*TJ/s',
            function (array $m) use ($activeMap, $activeWidths, $fontMaps): string {
                return $this->cleanTjArrayWithFontMap($m[0], $m[1], $activeMap, $activeWidths, $fontMaps);
            },
            $btContent
        ) ?? $btContent;

        // 2. Bersihkan operator tunggal (...) Tj atau <...> Tj di dalam blok BT ... ET
        $btContent = preg_replace_callback(
            '/(\((?:\\\\.|[^\\\\()])*\)|<[0-9a-fA-F\s]+>)\s*Tj\b/s',
            function (array $m) use ($activeMap, $fontMaps): string {
                return $this->cleanSingleTjOperator($m[0], $m[1], $activeMap, $fontMaps);
            },
            $btContent
        ) ?? $btContent;

        // 3. Bersihkan jika placeholder terpecah lintas beberapa operator Tj dalam satu BT ... ET
        return $this->cleanSplitOperatorsInBtBlock($btContent, $activeMap, $fontMaps);
    }

    /**
     * Bersihkan array `[...] TJ` menggunakan peta ToUnicode & lebar glyph `/W` font aktif.
     *
     * Bila ada teks statis SETELAH `{{placeholder}}` pada baris `TJ` yang sama
     * (contoh: `pada tanggal {{tanggal_mulai}} s.d. {{tanggal_selesai}} meliputi {{jam_pelajaran}} Jam Pelajaran.`),
     * maka setiap karakter `{{placeholder}}` yang dihapus diganti dengan angka kerning negatif `-width`
     * sebesar lebar glyph aslinya sehingga teks setelahnya (`s.d.`, `meliputi`, `Jam Pelajaran.`)
     * tetap berada di koordinat X aslinya tanpa bergeser!
     *
     * @param array<int, string>                $activeMap
     * @param array<int, float>                 $activeWidths
     * @param array<string, array<int, string>> $allFontMaps
     */
    private function cleanTjArrayWithFontMap(
        string $fullMatch,
        string $arrayBody,
        array $activeMap,
        array $activeWidths,
        array $allFontMaps
    ): string {
        if (! preg_match_all('/(\((?:\\\\.|[^\\\\()])*\)|<[0-9a-fA-F\s]*>)/s', $arrayBody, $tokenMatches, PREG_OFFSET_CAPTURE)) {
            return $fullMatch;
        }

        $pieces   = [];
        $fullText = '';

        foreach ($tokenMatches[0] as $idx => $tm) {
            $rawToken = $tm[0];
            $offset   = (int) $tm[1];
            $charData = $this->decodeTokenToChars($rawToken, $activeMap, $allFontMaps, $activeWidths);

            $decodedStr = '';
            foreach ($charData as $cd) {
                $decodedStr .= $cd['char'];
            }

            $startChar = strlen($fullText);
            $fullText .= $decodedStr;
            $endChar   = strlen($fullText);

            $pieces[] = [
                'idx'       => $idx,
                'raw'       => $rawToken,
                'offset'    => $offset,
                'length'    => strlen($rawToken),
                'isHex'     => str_starts_with($rawToken, '<'),
                'chars'     => $charData,
                'decoded'   => $decodedStr,
                'startChar' => $startChar,
                'endChar'   => $endChar,
            ];
        }

        if (! preg_match_all('/\{\{\s*[a-zA-Z0-9_.]+\s*\}\}|__QR_BLOCK__/', $fullText, $phMatches, PREG_OFFSET_CAPTURE)) {
            return $fullMatch;
        }

        $totalChars = strlen($fullText);
        $removeMask = array_fill(0, $totalChars, false);

        foreach ($phMatches[0] as $pm) {
            $pStart = (int) $pm[1];
            $pLen   = strlen($pm[0]);
            for ($i = $pStart; $i < $pStart + $pLen; $i++) {
                $removeMask[$i] = true;
            }
        }

        // Cari posisi karakter terakhir yang TIDAK dihapus di dalam TJ array ini
        $lastKeptCharIdx = -1;
        for ($i = $totalChars - 1; $i >= 0; $i--) {
            if (! $removeMask[$i]) {
                $lastKeptCharIdx = $i;
                break;
            }
        }

        // Jika seluruh karakter di dalam TJ array ini adalah placeholder (misal berdiri sendiri `{{nama_peserta}}`)
        if ($lastKeptCharIdx < 0) {
            return '[()]TJ';
        }

        // Bangun ulang $arrayBody dari piece terakhir ke piece pertama agar offset string tetap valid
        $newBody = $arrayBody;
        for ($p = count($pieces) - 1; $p >= 0; $p--) {
            $piece     = $pieces[$p];
            $charCount = count($piece['chars']);
            if ($charCount === 0) {
                continue;
            }

            $anyRemoved = false;
            $allRemoved = true;
            $removedW   = 0.0;

            for ($c = 0; $c < $charCount; $c++) {
                $globalIdx = $piece['startChar'] + $c;
                if (! empty($removeMask[$globalIdx])) {
                    $anyRemoved = true;
                    $removedW  += (float) ($piece['chars'][$c]['width'] ?? 520.0);
                } else {
                    $allRemoved = false;
                }
            }

            if (! $anyRemoved) {
                continue;
            }

            // Apakah masih ada teks statis setelah piece ini di baris TJ yang sama?
            $hasStaticTextAfter = ($piece['endChar'] <= $lastKeptCharIdx);

            if ($allRemoved) {
                // Seluruh piece ini (misal `(\000\007)`) merupakan bagian dari {{placeholder}}
                if ($hasStaticTextAfter && $removedW > 0) {
                    // Ganti dengan angka kerning negatif sebesar lebar glyph agar teks di kanannya tidak bergeser
                    $kernReplacement = ' ' . round(-$removedW, 2) . ' ';
                    $newBody         = substr_replace($newBody, $kernReplacement, $piece['offset'], $piece['length']);
                } else {
                    $newBody = substr_replace($newBody, '()', $piece['offset'], $piece['length']);
                }
                continue;
            }

            // Piece campuran (sebagian karakter disimpan, sebagian dihapus, misal `(Nomor: {{nomor_sertifikat}})`)
            if ($piece['isHex']) {
                $newHex = '';
                for ($c = 0; $c < $charCount; $c++) {
                    $globalIdx = $piece['startChar'] + $c;
                    if (empty($removeMask[$globalIdx])) {
                        $newHex .= $piece['chars'][$c]['raw_hex'];
                    }
                }
                $rep = '<' . $newHex . '>';
                if ($hasStaticTextAfter && $removedW > 0) {
                    $rep .= ' ' . round(-$removedW, 2) . ' ';
                }
                $newBody = substr_replace($newBody, $rep, $piece['offset'], $piece['length']);
            } else {
                $newBytes = '';
                for ($c = 0; $c < $charCount; $c++) {
                    $globalIdx = $piece['startChar'] + $c;
                    if (empty($removeMask[$globalIdx])) {
                        $newBytes .= $piece['chars'][$c]['raw_bytes'];
                    }
                }
                $rep = '(' . $this->escapePdfLiteral($newBytes) . ')';
                if ($hasStaticTextAfter && $removedW > 0) {
                    $rep .= ' ' . round(-$removedW, 2) . ' ';
                }
                $newBody = substr_replace($newBody, $rep, $piece['offset'], $piece['length']);
            }
        }

        return '[' . $newBody . ']TJ';
    }

    /**
     * Bersihkan operator tunggal `(...) Tj` atau `<...> Tj`.
     *
     * @param array<int, string>                $activeMap
     * @param array<string, array<int, string>> $allFontMaps
     */
    private function cleanSingleTjOperator(
        string $fullMatch,
        string $rawToken,
        array $activeMap,
        array $allFontMaps
    ): string {
        $charData   = $this->decodeTokenToChars($rawToken, $activeMap, $allFontMaps, []);
        $decodedStr = '';
        foreach ($charData as $cd) {
            $decodedStr .= $cd['char'];
        }

        if (! preg_match_all('/\{\{\s*[a-zA-Z0-9_.]+\s*\}\}|__QR_BLOCK__/', $decodedStr, $phMatches, PREG_OFFSET_CAPTURE)) {
            return $fullMatch;
        }

        $totalChars = strlen($decodedStr);
        $removeMask = array_fill(0, $totalChars, false);
        foreach ($phMatches[0] as $pm) {
            $pStart = (int) $pm[1];
            $pLen   = strlen($pm[0]);
            for ($i = $pStart; $i < $pStart + $pLen; $i++) {
                $removeMask[$i] = true;
            }
        }

        if (! in_array(false, $removeMask, true)) {
            return '() Tj';
        }

        if (str_starts_with($rawToken, '<')) {
            $newHex = '';
            foreach ($charData as $c => $cd) {
                if (empty($removeMask[$c])) {
                    $newHex .= $cd['raw_hex'];
                }
            }
            return '<' . $newHex . '> Tj';
        }

        $newBytes = '';
        foreach ($charData as $c => $cd) {
            if (empty($removeMask[$c])) {
                $newBytes .= $cd['raw_bytes'];
            }
        }
        return '(' . $this->escapePdfLiteral($newBytes) . ') Tj';
    }

    /**
     * Bersihkan placeholder yang terpecah lintas beberapa operator Tj di dalam satu blok BT ... ET.
     *
     * @param array<int, string>                $activeMap
     * @param array<string, array<int, string>> $allFontMaps
     */
    private function cleanSplitOperatorsInBtBlock(string $btContent, array $activeMap, array $allFontMaps): string
    {
        if (! preg_match_all('/(\((?:\\\\.|[^\\\\()])*\)|<[0-9a-fA-F\s]+>)/s', $btContent, $matches, PREG_OFFSET_CAPTURE)) {
            return $btContent;
        }

        $tokens   = [];
        $fullText = '';
        foreach ($matches[0] as $m) {
            $raw      = $m[0];
            $offset   = (int) $m[1];
            $charData = $this->decodeTokenToChars($raw, $activeMap, $allFontMaps, []);
            $decoded  = '';
            foreach ($charData as $cd) {
                $decoded .= $cd['char'];
            }

            $startChar = strlen($fullText);
            $fullText .= $decoded;
            $endChar   = strlen($fullText);

            $tokens[] = [
                'raw'       => $raw,
                'offset'    => $offset,
                'length'    => strlen($raw),
                'isHex'     => str_starts_with($raw, '<'),
                'chars'     => $charData,
                'decoded'   => $decoded,
                'startChar' => $startChar,
                'endChar'   => $endChar,
            ];
        }

        if (! preg_match_all('/\{\{\s*[a-zA-Z0-9_.]+\s*\}\}|__QR_BLOCK__/', $fullText, $phMatches, PREG_OFFSET_CAPTURE)) {
            return $btContent;
        }

        $removeMask = array_fill(0, strlen($fullText), false);
        foreach ($phMatches[0] as $pm) {
            $pStart = (int) $pm[1];
            $pLen   = strlen($pm[0]);
            for ($i = $pStart; $i < $pStart + $pLen; $i++) {
                $removeMask[$i] = true;
            }
        }

        $newBt = $btContent;
        for ($t = count($tokens) - 1; $t >= 0; $t--) {
            $tok        = $tokens[$t];
            $charCount  = count($tok['chars']);
            $hasRemoved = false;
            for ($c = 0; $c < $charCount; $c++) {
                if (! empty($removeMask[$tok['startChar'] + $c])) {
                    $hasRemoved = true;
                    break;
                }
            }
            if (! $hasRemoved) {
                continue;
            }

            if ($tok['isHex']) {
                $newHex = '';
                for ($c = 0; $c < $charCount; $c++) {
                    if (empty($removeMask[$tok['startChar'] + $c])) {
                        $newHex .= $tok['chars'][$c]['raw_hex'];
                    }
                }
                $newBt = substr_replace($newBt, '<' . $newHex . '>', $tok['offset'], $tok['length']);
            } else {
                $newBytes = '';
                for ($c = 0; $c < $charCount; $c++) {
                    if (empty($removeMask[$tok['startChar'] + $c])) {
                        $newBytes .= $tok['chars'][$c]['raw_bytes'];
                    }
                }
                $newBt = substr_replace($newBt, '(' . $this->escapePdfLiteral($newBytes) . ')', $tok['offset'], $tok['length']);
            }
        }

        return $newBt;
    }

    /**
     * Decode satu token string PDF `(...)` atau `<...>` menjadi daftar karakter individual
     * beserta byte/hex aslinya, CID, dan lebar glyph-nya (dalam 1/1000 em).
     *
     * @param array<int, string>                $activeMap
     * @param array<string, array<int, string>> $allFontMaps
     * @param array<int, float>                 $activeWidths
     * @return array<int, array{char: string, cid: int, width: float, raw_bytes: string, raw_hex: string}>
     */
    private function decodeTokenToChars(
        string $rawToken,
        array $activeMap,
        array $allFontMaps,
        array $activeWidths
    ): array {
        $isHex = str_starts_with($rawToken, '<');
        if ($isHex) {
            $hexClean = preg_replace('/\s+/', '', substr($rawToken, 1, -1)) ?? '';
            $rawBytes = @hex2bin($hexClean);
            if ($rawBytes === false) {
                $rawBytes = '';
            }
        } else {
            $rawBytes = $this->unescapePdfLiteral(substr($rawToken, 1, -1));
            $hexClean = bin2hex($rawBytes);
        }

        if ($rawBytes === '' && $hexClean === '') {
            return [];
        }

        // Pilih fontMap yang cocok (prioritaskan $activeMap)
        $chosenMap = $activeMap;
        if (empty($chosenMap) && ! empty($allFontMaps)) {
            foreach ($allFontMaps as $tryMap) {
                $tryDec = $this->decodeHexWithMap($hexClean, $tryMap);
                if ($tryDec !== '') {
                    $chosenMap = $tryMap;
                    break;
                }
            }
        }

        // Jika menggunakan CMap ToUnicode (2-byte atau 1-byte CID)
        if (! empty($chosenMap)) {
            // Cek apakah 2-byte CID (panjang byte genap dan mayoritas CID cocok di 2-byte)
            $lenBytes = strlen($rawBytes);
            if ($lenBytes >= 2 && ($lenBytes % 2 === 0)) {
                $chars2 = [];
                $hits2  = 0;
                for ($i = 0; $i < $lenBytes; $i += 2) {
                    $b2  = substr($rawBytes, $i, 2);
                    $h4  = substr($hexClean, $i * 2, 4);
                    $cid = (ord($b2[0]) << 8) | ord($b2[1]);
                    if (isset($chosenMap[$cid])) {
                        $hits2++;
                        $ch = $chosenMap[$cid];
                    } else {
                        $ch = '';
                    }
                    // Hanya ambil karakter 1-byte ASCII untuk menjaga indeks strlen($decoded) 1:1
                    if (strlen($ch) !== 1) {
                        $ch = '?';
                    }
                    $chars2[] = [
                        'char'      => $ch,
                        'cid'       => $cid,
                        'width'     => $activeWidths[$cid] ?? 520.0,
                        'raw_bytes' => $b2,
                        'raw_hex'   => $h4,
                    ];
                }
                if ($hits2 > 0) {
                    return $chars2;
                }
            }

            // Cek 1-byte CID
            $chars1 = [];
            $hits1  = 0;
            for ($i = 0; $i < $lenBytes; $i++) {
                $b1  = $rawBytes[$i];
                $h2  = substr($hexClean, $i * 2, 2);
                $cid = ord($b1);
                if (isset($chosenMap[$cid])) {
                    $hits1++;
                    $ch = $chosenMap[$cid];
                } else {
                    $ch = $b1;
                }
                if (strlen($ch) !== 1) {
                    $ch = '?';
                }
                $chars1[] = [
                    'char'      => $ch,
                    'cid'       => $cid,
                    'width'     => $activeWidths[$cid] ?? 520.0,
                    'raw_bytes' => $b1,
                    'raw_hex'   => $h2,
                ];
            }
            if ($hits1 > 0) {
                return $chars1;
            }
        }

        // Fallback: ASCII / Windows-1252 1-byte per karakter
        $chars  = [];
        $bCount = strlen($rawBytes);
        for ($i = 0; $i < $bCount; $i++) {
            $b   = $rawBytes[$i];
            $cid = ord($b);
            $chars[] = [
                'char'      => $b,
                'cid'       => $cid,
                'width'     => $activeWidths[$cid] ?? 520.0,
                'raw_bytes' => $b,
                'raw_hex'   => bin2hex($b),
            ];
        }

        return $chars;
    }

    // =========================================================================
    // Ekstraksi Text Runs & Koordinat Presisi dari PDF Content Stream
    // =========================================================================

    /**
     * Parse PDF content stream dan ekstrak semua text run beserta posisi (xPt, yPt),
     * offset per karakter (`char_offsets_pt`), ukuran font, dan warna.
     *
     * @param array<string, array<int, string>> $fontMaps   [fontName => [cidInt => utf8Char]]
     * @param array<string, string>             $fontNames  [fontName => baseFontName]
     * @param array<string, array<int, float>>  $fontWidths [fontName => [cidInt => widthIn1000Em]]
     * @param array<int, float>                 $initialCtm
     * @return array<int, array<string, mixed>>
     */
    private function extractTextRunsFromStream(
        string $stream,
        array $fontMaps,
        array $fontNames,
        array $fontWidths = [],
        array $initialCtm = [1.0, 0.0, 0.0, 1.0, 0.0, 0.0],
        float $llx = 0.0,
        float $lly = 0.0
    ): array {
        $runs = [];
        if (trim($stream) === '') {
            return $runs;
        }

        $ctmStack   = [];
        $ctm        = $initialCtm;
        $fillColor  = '#0f172a';
        $colorStack = [];

        $inText     = false;
        $tm         = [1.0, 0.0, 0.0, 1.0, 0.0, 0.0];
        $tlm        = [1.0, 0.0, 0.0, 1.0, 0.0, 0.0];
        $fontKey    = 'F1';
        $fontSize   = 12.0;
        $leading    = 14.0;
        $charSpace  = 0.0;
        $horizScale = 1.0;

        // Tokenize PDF content stream commands
        $pattern = '~'
            . '(\[(?:[^\[\]]|\((?:\\\\.|[^\\\\()])*\)|<[0-9a-fA-F\s]*>)*\])|'
            . '(\((?:\\\\.|[^\\\\()])*\))|'
            . '(<[0-9a-fA-F\s]+>)|'
            . '(/[A-Za-z0-9_.\-+#]+)|'
            . '([+-]?(?:\d+\.?\d*|\.\d+))|'
            . '([A-Za-z\'\"*]+)'
            . '~s';

        if (! preg_match_all($pattern, $stream, $tokens)) {
            return $runs;
        }

        $operands = [];
        foreach ($tokens[0] as $tok) {
            $first = $tok[0];
            if ($first === '[' || $first === '(' || $first === '<' || $first === '/' || is_numeric($tok)) {
                $operands[] = $tok;
                continue;
            }

            $op = $tok;
            switch ($op) {
                case 'q':
                    $ctmStack[]   = $ctm;
                    $colorStack[] = $fillColor;
                    break;

                case 'Q':
                    if (! empty($ctmStack)) {
                        $ctm = array_pop($ctmStack);
                    }
                    if (! empty($colorStack)) {
                        $fillColor = array_pop($colorStack);
                    }
                    break;

                case 'cm':
                    if (count($operands) >= 6) {
                        $vals = array_slice($operands, -6);
                        $m    = array_map('floatval', $vals);
                        $ctm  = $this->multiplyMatrix($m, $ctm);
                    }
                    break;

                case 'rg':
                case 'sc':
                case 'scn':
                    // Ambil operand numerik terakhir (3 untuk RGB, 1 untuk Gray, 4 untuk CMYK)
                    $numOps = array_values(array_filter($operands, 'is_numeric'));
                    if (count($numOps) >= 3) {
                        $vals      = array_slice($numOps, -3);
                        $r         = max(0, min(255, (int) round(((float) $vals[0]) * 255)));
                        $g         = max(0, min(255, (int) round(((float) $vals[1]) * 255)));
                        $b         = max(0, min(255, (int) round(((float) $vals[2]) * 255)));
                        $fillColor = sprintf('#%02x%02x%02x', $r, $g, $b);
                    } elseif (count($numOps) === 1) {
                        $v         = max(0, min(255, (int) round(((float) $numOps[0]) * 255)));
                        $fillColor = sprintf('#%02x%02x%02x', $v, $v, $v);
                    }
                    break;

                case 'g':
                    if (count($operands) >= 1) {
                        $gray      = (float) end($operands);
                        $v         = max(0, min(255, (int) round($gray * 255)));
                        $fillColor = sprintf('#%02x%02x%02x', $v, $v, $v);
                    }
                    break;

                case 'k':
                    if (count($operands) >= 4) {
                        $vals      = array_slice($operands, -4);
                        $c         = (float) $vals[0];
                        $m         = (float) $vals[1];
                        $y         = (float) $vals[2];
                        $k         = (float) $vals[3];
                        $r         = max(0, min(255, (int) round(255 * (1 - $c) * (1 - $k))));
                        $g         = max(0, min(255, (int) round(255 * (1 - $m) * (1 - $k))));
                        $b         = max(0, min(255, (int) round(255 * (1 - $y) * (1 - $k))));
                        $fillColor = sprintf('#%02x%02x%02x', $r, $g, $b);
                    }
                    break;

                case 'BT':
                    $inText = true;
                    $tm     = [1.0, 0.0, 0.0, 1.0, 0.0, 0.0];
                    $tlm    = [1.0, 0.0, 0.0, 1.0, 0.0, 0.0];
                    break;

                case 'ET':
                    $inText = false;
                    break;

                case 'Tf':
                    if (count($operands) >= 2) {
                        $fSize   = (float) array_pop($operands);
                        $fName   = (string) array_pop($operands);
                        $fontKey = ltrim($fName, '/');
                        if ($fSize != 0.0) {
                            $fontSize = abs($fSize);
                        }
                    }
                    break;

                case 'TL':
                    if (! empty($operands)) {
                        $leading = (float) end($operands);
                    }
                    break;

                case 'Tc':
                    if (! empty($operands)) {
                        $charSpace = (float) end($operands);
                    }
                    break;

                case 'Tz':
                    if (! empty($operands)) {
                        $horizScale = ((float) end($operands)) / 100.0;
                    }
                    break;

                case 'Tm':
                    if (count($operands) >= 6) {
                        $vals = array_slice($operands, -6);
                        $tm   = array_map('floatval', $vals);
                        $tlm  = $tm;
                    }
                    break;

                case 'Td':
                    if (count($operands) >= 2) {
                        $ty  = (float) array_pop($operands);
                        $tx  = (float) array_pop($operands);
                        $tlm = $this->multiplyMatrix([1.0, 0.0, 0.0, 1.0, $tx, $ty], $tlm);
                        $tm  = $tlm;
                    }
                    break;

                case 'TD':
                    if (count($operands) >= 2) {
                        $ty      = (float) array_pop($operands);
                        $tx      = (float) array_pop($operands);
                        $leading = -$ty;
                        $tlm     = $this->multiplyMatrix([1.0, 0.0, 0.0, 1.0, $tx, $ty], $tlm);
                        $tm      = $tlm;
                    }
                    break;

                case 'T*':
                    $tlm = $this->multiplyMatrix([1.0, 0.0, 0.0, 1.0, 0.0, -$leading], $tlm);
                    $tm  = $tlm;
                    break;

                case '\'':
                case '"':
                    $tlm = $this->multiplyMatrix([1.0, 0.0, 0.0, 1.0, 0.0, -$leading], $tlm);
                    $tm  = $tlm;
                    // Fallthrough to Tj
                case 'Tj':
                case 'TJ':
                    if ($inText && ! empty($operands)) {
                        $arg      = (string) end($operands);
                        $combined = $this->multiplyMatrix($tm, $ctm);
                        $scaleX   = sqrt($combined[0] * $combined[0] + $combined[1] * $combined[1]);
                        $scaleY   = sqrt($combined[2] * $combined[2] + $combined[3] * $combined[3]);
                        $effScale = ($scaleX > 0.001 ? $scaleX : 1.0) * $horizScale;
                        $effFont  = max(6.0, round($fontSize * ($scaleY > 0.001 ? $scaleY : 1.0), 1));

                        [$decodedText, $advanceEm, $charOffsetsEm] = $this->decodeTextOperandWithMetrics(
                            $arg,
                            $fontMaps[$fontKey] ?? [],
                            $fontMaps,
                            $fontWidths[$fontKey] ?? []
                        );

                        if ($decodedText !== '') {
                            $xPt     = $combined[4] - $llx;
                            $yPt     = $combined[5] - $lly;
                            $widthPt = max(4.0, $advanceEm * $fontSize * $effScale);

                            $charOffsetsPt = [];
                            foreach ($charOffsetsEm as $emOff) {
                                $charOffsetsPt[] = $emOff * $fontSize * $effScale;
                            }

                            $baseFontName = $fontNames[$fontKey] ?? $fontKey;
                            $isBold       = (bool) preg_match('/(Bold|Black|Heavy|SemiBold|Demi|Extrabold)/i', $baseFontName);

                            $runs[] = [
                                'text'            => $decodedText,
                                'x_pt'            => $xPt,
                                'y_pt'            => $yPt,
                                'width_pt'        => $widthPt,
                                'char_offsets_pt' => $charOffsetsPt,
                                'font_size'       => $effFont,
                                'font_weight'     => $isBold ? 'bold' : 'normal',
                                'font_name'       => $baseFontName,
                                'color'           => $fillColor,
                            ];

                            // Majukan posisi X pada matriks teks $tm
                            $txAdvance = ($advanceEm * $fontSize + strlen($decodedText) * $charSpace) * $horizScale;
                            $tm        = $this->multiplyMatrix([1.0, 0.0, 0.0, 1.0, $txAdvance, 0.0], $tm);
                        }
                    }
                    break;
            }

            $operands = [];
        }

        return $runs;
    }

    /**
     * Gabungkan text run yang bersebelahan pada baseline Y yang sama menjadi baris utuh.
     *
     * @param array<int, array<string, mixed>> $runs
     * @return array<int, array<string, mixed>>
     */
    private function mergeAdjacentTextRuns(array $runs): array
    {
        if (empty($runs)) {
            return [];
        }

        $merged = [];
        foreach ($runs as $run) {
            if (empty($merged)) {
                $merged[] = $run;
                continue;
            }

            $lastIdx = count($merged) - 1;
            $last    = $merged[$lastIdx];

            $sameBaseline  = abs($run['y_pt'] - $last['y_pt']) <= max(2.5, $last['font_size'] * 0.25);
            $expectedNextX = $last['x_pt'] + $last['width_pt'];
            $xGap          = $run['x_pt'] - $expectedNextX;
            $maxAllowedGap = max(18.0, $last['font_size'] * 1.5);

            if ($sameBaseline && $xGap >= -($last['font_size'] * 0.8) && $xGap <= $maxAllowedGap) {
                $spacer = ($xGap > $last['font_size'] * 0.32 && ! str_ends_with($last['text'], ' ') && ! str_starts_with($run['text'], ' '))
                    ? ' '
                    : '';
                if (
                    str_ends_with($last['text'], '{')
                    || str_starts_with($run['text'], '}')
                    || str_ends_with($last['text'], '_')
                    || str_starts_with($run['text'], '_')
                ) {
                    $spacer = '';
                }

                $baseOff = $run['x_pt'] - $last['x_pt'];
                if ($spacer !== '') {
                    $merged[$lastIdx]['char_offsets_pt'][] = $last['width_pt'];
                }
                foreach (($run['char_offsets_pt'] ?? []) as $co) {
                    $merged[$lastIdx]['char_offsets_pt'][] = $baseOff + $co;
                }

                $merged[$lastIdx]['text']    .= $spacer . $run['text'];
                $merged[$lastIdx]['width_pt'] = max($last['width_pt'], ($run['x_pt'] + $run['width_pt']) - $last['x_pt']);
                if ($run['font_weight'] === 'bold') {
                    $merged[$lastIdx]['font_weight'] = 'bold';
                }
            } else {
                $merged[] = $run;
            }
        }

        return $merged;
    }

    /**
     * Ekstrak daftar placeholder {{...}} dari baris-baris teks yang sudah digabungkan
     * dengan akurasi sub-piksel dari `char_offsets_pt`.
     *
     * @param array<int, array<string, mixed>> $lines
     * @return array<int, array<string, mixed>>
     */
    private function extractPlaceholdersFromLines(array $lines, float $pageWidthPt, float $pageHeightPt): array
    {
        $catalog = $this->buildPlaceholderCatalogMap();
        $result  = [];
        $idx     = 1;

        foreach ($lines as $line) {
            $lineText = (string) ($line['text'] ?? '');
            if (! preg_match_all('/(\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}|__QR_BLOCK__)/', $lineText, $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            $lineLen       = max(1, strlen($lineText));
            $lineXPt       = (float) $line['x_pt'];
            $lineYPt       = (float) $line['y_pt'];
            $lineWidthPt   = (float) $line['width_pt'];
            $fontSizePt    = (float) $line['font_size'];
            $charOffsetsPt = $line['char_offsets_pt'] ?? [];

            foreach ($matches[0] as $mIdx => $fullMatchInfo) {
                $rawMatch   = (string) $fullMatchInfo[0];
                $charOffset = (int) $fullMatchInfo[1];
                $matchLen   = strlen($rawMatch);
                $endOffset  = $charOffset + $matchLen;

                $key = $rawMatch === '__QR_BLOCK__'
                    ? 'qr_code'
                    : strtolower(trim((string) ($matches[2][$mIdx][0] ?? '')));
                $normalizedTag = '{{' . $key . '}}';
                $isQr          = in_array($normalizedTag, self::QR_TAGS, true) || $key === 'qr_code' || $key === 'qr_block';

                // Gunakan char_offsets_pt jika tersedia untuk mendapatkan posisi X & lebar yang 100% presisi
                if (isset($charOffsetsPt[$charOffset])) {
                    $startRelPt = (float) $charOffsetsPt[$charOffset];
                    $endRelPt   = isset($charOffsetsPt[$endOffset])
                        ? (float) $charOffsetsPt[$endOffset]
                        : $lineWidthPt;
                    $phXPt     = $lineXPt + $startRelPt;
                    $phWidthPt = max($fontSizePt * 2.0, $endRelPt - $startRelPt);
                } else {
                    $prefixRatio = $charOffset / $lineLen;
                    $widthRatio  = $matchLen / $lineLen;
                    $phXPt       = $lineXPt + ($lineWidthPt * $prefixRatio);
                    $phWidthPt   = max($fontSizePt * 2.5, $lineWidthPt * $widthRatio);
                }

                // Konversi baseline PDF (bawah-ke-atas) menjadi koordinat top-down (atas-ke-bawah)
                $topPt = $pageHeightPt - ($lineYPt + ($fontSizePt * 0.88));
                $topPt = max(0.0, min($pageHeightPt, $topPt));

                $origXPct = round(($phXPt / $pageWidthPt) * 100.0, 2);
                $origYPct = round(($topPt / $pageHeightPt) * 100.0, 2);
                $origWPct = round(($phWidthPt / $pageWidthPt) * 100.0, 2);
                $origHPct = round((($fontSizePt * 1.35) / $pageHeightPt) * 100.0, 2);

                $trimmedLine  = trim($lineText);
                $isStandalone = ($trimmedLine === $rawMatch);
                $centerXPct   = $origXPct + ($origWPct / 2.0);
                $nearCenter   = abs($centerXPct - 50.0) <= 10.0;

                if ($isQr) {
                    $align = 'center';
                    $xPct  = round($centerXPct, 2);
                } elseif ($isStandalone && $nearCenter) {
                    $align = 'center';
                    $xPct  = 50.0;
                } else {
                    $align = 'left';
                    $xPct  = $origXPct;
                }

                $fontWeight = (string) ($line['font_weight'] ?? 'normal');
                if ($fontWeight === 'normal' && in_array($key, self::DEFAULT_BOLD_KEYS, true)) {
                    $fontWeight = 'bold';
                }

                $meta = $catalog[$normalizedTag] ?? [
                    'label'   => ucwords(str_replace('_', ' ', $key)),
                    'example' => $normalizedTag,
                ];

                $effectiveFontSize = (int) round(max(8.0, min(64.0, $fontSizePt)));
                if (in_array($key, ['url_verifikasi', 'verification_url'], true)) {
                    $effectiveFontSize = min(8, $effectiveFontSize);
                }

                $effectiveYPct = max(0.0, min(100.0, $origYPct));
                if ($isQr) {
                    // Pusatkan posisi vertikal QR code terhadap posisi teks {{qr_code}}
                    $effectiveYPct = max(0.0, round($origYPct - 4.0, 2));
                }

                $result[] = [
                    'id'          => 'ph_' . ($idx++),
                    'tag'         => $normalizedTag,
                    'key'         => $key,
                    'label'       => $meta['label'],
                    'sample'      => $meta['example'],
                    'type'        => $isQr ? 'qr' : 'text',
                    'x_pct'       => max(0.0, min(100.0, $xPct)),
                    'y_pct'       => $effectiveYPct,
                    'orig_x_pct'  => max(0.0, min(100.0, $origXPct)),
                    'orig_y_pct'  => max(0.0, min(100.0, $origYPct)),
                    'orig_w_pct'  => max(2.0, min(100.0, $origWPct)),
                    'orig_h_pct'  => max(1.5, min(50.0, $origHPct)),
                    'font_size'   => $effectiveFontSize,
                    'font_weight' => $fontWeight,
                    'color'       => (string) ($line['color'] ?? '#0f172a'),
                    'align'       => $align,
                    'mask_bg'     => false,
                    'mask_color'  => '#ffffff',
                    'qr_size'     => 95,
                ];
            }
        }

        return $result;
    }

    /**
     * Fallback scan jika PDF memiliki struktur operator non-standar.
     */
    private function fallbackScanRawStream(string $rawStream, float $widthPt, float $heightPt): array
    {
        $catalog = $this->buildPlaceholderCatalogMap();
        if (! preg_match_all('/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/', $rawStream, $matches)) {
            return [];
        }

        $uniqueKeys = array_values(array_unique(array_map('strtolower', $matches[1])));
        $result     = [];
        $defaultY   = 28.0;

        foreach ($uniqueKeys as $i => $key) {
            $tag  = '{{' . $key . '}}';
            $isQr = in_array($tag, self::QR_TAGS, true);
            $meta = $catalog[$tag] ?? [
                'label'   => ucwords(str_replace('_', ' ', $key)),
                'example' => $tag,
            ];

            $result[] = [
                'id'          => 'ph_' . ($i + 1),
                'tag'         => $tag,
                'key'         => $key,
                'label'       => $meta['label'],
                'sample'      => $meta['example'],
                'type'        => $isQr ? 'qr' : 'text',
                'x_pct'       => $isQr ? 80.0 : 50.0,
                'y_pct'       => $isQr ? 72.0 : min(85.0, $defaultY + ($i * 8.0)),
                'orig_x_pct'  => $isQr ? 75.0 : 35.0,
                'orig_y_pct'  => $isQr ? 72.0 : min(85.0, $defaultY + ($i * 8.0)),
                'orig_w_pct'  => $isQr ? 12.0 : 30.0,
                'orig_h_pct'  => $isQr ? 18.0 : 4.0,
                'font_size'   => in_array($key, ['nama_peserta', 'participant_name'], true) ? 24 : 14,
                'font_weight' => in_array($key, self::DEFAULT_BOLD_KEYS, true) ? 'bold' : 'normal',
                'color'       => '#0f172a',
                'align'       => 'center',
                'mask_bg'     => false,
                'mask_color'  => '#ffffff',
                'qr_size'     => 110,
            ];
        }

        return $result;
    }

    /**
     * Raw binary fallback inspection jika FPDI gagal membaca struktur PDF tertentu.
     */
    private function inspectPdfRawFallback(string $pdfPath, array $defaultResult): array
    {
        $raw = @file_get_contents($pdfPath);
        if (! is_string($raw) || $raw === '') {
            return $defaultResult;
        }

        if (preg_match('/\/MediaBox\s*\[\s*([0-9.\-]+)\s+([0-9.\-]+)\s+([0-9.\-]+)\s+([0-9.\-]+)\s*\]/', $raw, $mb)) {
            $w = abs((float) $mb[3] - (float) $mb[1]);
            $h = abs((float) $mb[4] - (float) $mb[2]);
            if ($w > 0 && $h > 0) {
                $defaultResult['page_width_pt']  = round($w, 2);
                $defaultResult['page_height_pt'] = round($h, 2);
                $defaultResult['page_width_mm']  = round($w * 25.4 / 72.0, 2);
                $defaultResult['page_height_mm'] = round($h * 25.4 / 72.0, 2);
                $defaultResult['orientation']    = $w >= $h ? 'L' : 'P';
            }
        }

        $decompressedStreams = [];
        if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $raw, $matches)) {
            foreach ($matches[1] as $s) {
                $inf = @gzuncompress($s);
                if ($inf === false) {
                    $inf = @gzinflate($s);
                }
                $decompressedStreams[] = is_string($inf) ? $inf : $s;
            }
        }

        $combined = implode("\n", $decompressedStreams);
        $runs     = $this->extractTextRunsFromStream($combined, [], []);
        $lines    = $this->mergeAdjacentTextRuns($runs);
        $phs      = $this->extractPlaceholdersFromLines($lines, $defaultResult['page_width_pt'], $defaultResult['page_height_pt']);

        if (empty($phs)) {
            $phs = $this->fallbackScanRawStream($combined, $defaultResult['page_width_pt'], $defaultResult['page_height_pt']);
        }

        $defaultResult['placeholders'] = $phs;
        return $defaultResult;
    }

    // =========================================================================
    // Helper Decoding PDF String, Hex, CMap, Glyph Metrics, dan Matriks
    // =========================================================================

    /**
     * Decode operand dari Tj / TJ menjadi `[string $utf8Text, float $advanceEm, array<int, float> $charOffsetsEm]`.
     *
     * @param array<int, string>                $fontMap     [cidInt => utf8Char]
     * @param array<string, array<int, string>> $allFontMaps
     * @param array<int, float>                 $fontWidths  [cidInt => widthIn1000Em]
     * @return array{0: string, 1: float, 2: array<int, float>}
     */
    private function decodeTextOperandWithMetrics(
        string $operand,
        array $fontMap,
        array $allFontMaps = [],
        array $fontWidths = []
    ): array {
        $operand = trim($operand);
        if ($operand === '') {
            return ['', 0.0, []];
        }

        // Literal string (...) atau Hex string <...>
        if (
            (str_starts_with($operand, '(') && str_ends_with($operand, ')'))
            || (str_starts_with($operand, '<') && str_ends_with($operand, '>'))
        ) {
            $chars   = $this->decodeTokenToChars($operand, $fontMap, $allFontMaps, $fontWidths);
            $text    = '';
            $offsets = [];
            $curEm   = 0.0;
            foreach ($chars as $cd) {
                $offsets[] = $curEm;
                $text     .= $cd['char'];
                $curEm    += ((float) $cd['width']) / 1000.0;
            }
            return [$text, max(0.05, $curEm), $offsets];
        }

        // Array [...] untuk TJ
        if (str_starts_with($operand, '[') && str_ends_with($operand, ']')) {
            $inner   = substr($operand, 1, -1);
            $text    = '';
            $offsets = [];
            $curEm   = 0.0;

            $pattern = '/(\((?:\\\\.|[^\\\\()])*\)|<[0-9a-fA-F\s]*>|[+-]?(?:\d+\.?\d*|\.\d+))/s';
            if (preg_match_all($pattern, $inner, $parts)) {
                foreach ($parts[0] as $p) {
                    if (str_starts_with($p, '(') || str_starts_with($p, '<')) {
                        $chars = $this->decodeTokenToChars($p, $fontMap, $allFontMaps, $fontWidths);
                        foreach ($chars as $cd) {
                            $offsets[] = $curEm;
                            $text     .= $cd['char'];
                            $curEm    += ((float) $cd['width']) / 1000.0;
                        }
                    } elseif (is_numeric($p)) {
                        $k = (float) $p;
                        // Di PDF TJ, angka negatif besar (misal -250) berarti geser ke kanan (spasi)
                        if ($k < -180 && $text !== '' && ! str_ends_with($text, ' ') && ! str_ends_with($text, '{')) {
                            $offsets[] = $curEm;
                            $text     .= ' ';
                        }
                        $curEm += (-$k / 1000.0);
                    }
                }
            }

            return [$text, max(0.05, $curEm), $offsets];
        }

        return ['', 0.0, []];
    }

    /**
     * @param array<int, string> $cidToCharMap
     */
    private function decodeHexWithMap(string $hex, array $cidToCharMap): string
    {
        if (empty($cidToCharMap) || $hex === '') {
            return '';
        }

        $out4 = '';
        $hit4 = 0;
        if (strlen($hex) % 4 === 0) {
            for ($i = 0, $n = strlen($hex); $i < $n; $i += 4) {
                $cid = (int) hexdec(substr($hex, $i, 4));
                if (isset($cidToCharMap[$cid])) {
                    $out4 .= $cidToCharMap[$cid];
                    $hit4++;
                }
            }
        }

        $out2 = '';
        $hit2 = 0;
        if (strlen($hex) % 2 === 0) {
            for ($i = 0, $n = strlen($hex); $i < $n; $i += 2) {
                $cid = (int) hexdec(substr($hex, $i, 2));
                if (isset($cidToCharMap[$cid])) {
                    $out2 .= $cidToCharMap[$cid];
                    $hit2++;
                }
            }
        }

        if ($hit4 > 0 && $hit4 >= $hit2) {
            return $out4;
        }
        if ($hit2 > 0) {
            return $out2;
        }

        return '';
    }

    /**
     * Unescape PDF literal string (...) dalam satu pass agar byte octal (\000) dan escape (\(, \t, \\)
     * tidak saling menimpa.
     */
    public function unescapePdfLiteral(string $str): string
    {
        $unescaped = preg_replace_callback(
            '/\\\\([0-7]{1,3}|[nrtbf()\\\\]|\r\n|\r|\n)/s',
            static function (array $m): string {
                $seq = $m[1];
                if ($seq === 'n') {
                    return "\n";
                }
                if ($seq === 'r') {
                    return "\r";
                }
                if ($seq === 't') {
                    return "\t";
                }
                if ($seq === 'b') {
                    return "\x08";
                }
                if ($seq === 'f') {
                    return "\x0C";
                }
                if ($seq === '(') {
                    return '(';
                }
                if ($seq === ')') {
                    return ')';
                }
                if ($seq === '\\') {
                    return '\\';
                }
                if ($seq === "\r\n" || $seq === "\r" || $seq === "\n") {
                    return '';
                }
                return chr(((int) octdec($seq)) & 0xFF);
            },
            $str
        ) ?? $str;

        // Jika string diawali BOM UTF-16BE (\xFE\xFF)
        if (str_starts_with($unescaped, "\xFE\xFF")) {
            $utf8 = @mb_convert_encoding(substr($unescaped, 2), 'UTF-8', 'UTF-16BE');
            if (is_string($utf8)) {
                return $utf8;
            }
        }

        return $unescaped;
    }

    /**
     * Escape string/binary menjadi PDF literal string yang aman di dalam (...).
     */
    public function escapePdfLiteral(string $str): string
    {
        $out = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $b   = $str[$i];
            $ord = ord($b);
            if ($b === '\\') {
                $out .= '\\\\';
            } elseif ($b === '(') {
                $out .= '\\(';
            } elseif ($b === ')') {
                $out .= '\\)';
            } elseif ($b === "\r") {
                $out .= '\\r';
            } elseif ($b === "\n") {
                $out .= '\\n';
            } elseif ($b === "\t") {
                $out .= '\\t';
            } elseif ($ord < 32 || $ord > 126) {
                $out .= sprintf('\\%03o', $ord);
            } else {
                $out .= $b;
            }
        }
        return $out;
    }

    /**
     * Parse stream /ToUnicode CMap pada PDF menjadi peta [cidInt => utf8Char].
     *
     * @return array<int, string>
     */
    public static function parseToUnicodeCMap(string $cmapStream): array
    {
        $map = [];
        if ($cmapStream === '') {
            return $map;
        }

        // 1. beginbfchar ... endbfchar
        if (preg_match_all('/beginbfchar(.*?)endbfchar/s', $cmapStream, $bfCharBlocks)) {
            foreach ($bfCharBlocks[1] as $block) {
                if (preg_match_all('/<([0-9a-fA-F]+)>\s*<([0-9a-fA-F]+)>/', $block, $pairs, PREG_SET_ORDER)) {
                    foreach ($pairs as $p) {
                        $srcCid     = (int) hexdec($p[1]);
                        $dstUnicode = self::hexToUtf8($p[2]);
                        if ($dstUnicode !== '') {
                            $map[$srcCid] = $dstUnicode;
                        }
                    }
                }
            }
        }

        // 2. beginbfrange ... endbfrange
        if (preg_match_all('/beginbfrange(.*?)endbfrange/s', $cmapStream, $bfRangeBlocks)) {
            foreach ($bfRangeBlocks[1] as $block) {
                if (preg_match_all('/<([0-9a-fA-F]+)>\s*<([0-9a-fA-F]+)>\s*<([0-9a-fA-F]+)>/', $block, $ranges, PREG_SET_ORDER)) {
                    foreach ($ranges as $r) {
                        $startCid = (int) hexdec($r[1]);
                        $endCid   = (int) hexdec($r[2]);
                        $dstStart = (int) hexdec($r[3]);
                        $limit    = min($endCid, $startCid + 512);
                        for ($cid = $startCid; $cid <= $limit; $cid++) {
                            $codepoint = $dstStart + ($cid - $startCid);
                            $map[$cid] = mb_chr($codepoint, 'UTF-8') ?: '';
                        }
                    }
                }
            }
        }

        return $map;
    }

    private static function hexToUtf8(string $hex): string
    {
        $hex = preg_replace('/\s+/', '', $hex) ?? '';
        if ($hex === '') {
            return '';
        }
        if (strlen($hex) <= 4) {
            $cp = (int) hexdec($hex);
            return mb_chr($cp, 'UTF-8') ?: '';
        }
        $bin = @hex2bin($hex);
        if ($bin === false) {
            return '';
        }
        return @mb_convert_encoding($bin, 'UTF-8', 'UTF-16BE') ?: '';
    }

    /**
     * Perkalian dua matriks transformasi affine 2D PDF [a, b, c, d, e, f].
     *
     * @param array<int, float> $m1
     * @param array<int, float> $m2
     * @return array<int, float>
     */
    private function multiplyMatrix(array $m1, array $m2): array
    {
        return [
            $m1[0] * $m2[0] + $m1[1] * $m2[2],
            $m1[0] * $m2[1] + $m1[1] * $m2[3],
            $m1[2] * $m2[0] + $m1[3] * $m2[2],
            $m1[2] * $m2[1] + $m1[3] * $m2[3],
            $m1[4] * $m2[0] + $m1[5] * $m2[2] + $m2[4],
            $m1[4] * $m2[1] + $m1[5] * $m2[3] + $m2[5],
        ];
    }

    /**
     * @return array<string, array{label: string, example: string}>
     */
    private function buildPlaceholderCatalogMap(): array
    {
        $grouped = CertificateTemplateRenderer::getAvailablePlaceholders();
        $flat    = [];
        foreach ($grouped as $items) {
            foreach ($items as $item) {
                $flat[$item['tag']] = [
                    'label'   => $item['label'],
                    'example' => $item['example'],
                ];
            }
        }
        return $flat;
    }
}

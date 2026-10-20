<?php

namespace App\Services\Learning;

use App\Models\Learning\QuestionModel;
use App\Models\Learning\QuestionOptionModel;
use RuntimeException;
use SimpleXMLElement;
use Throwable;
use ZipArchive;

class QuizQuestionImportService
{
    /**
     * Menghasilkan isi template CSV untuk impor soal pilihan ganda.
     * Menggunakan UTF-8 BOM dan pemisah semicolon (;) agar otomatis rapi saat dibuka di Microsoft Excel.
     */
    public function generateTemplateCsv(): string
    {
        $bom = "\xEF\xBB\xBF";
        $header = "No;Pertanyaan;Pilihan_A;Pilihan_B;Pilihan_C;Pilihan_D;Pilihan_E;Kunci_Jawaban;Poin;Pembahasan\r\n";

        $rows = [
            [
                '1',
                'Menurut Perpres No. 16 Tahun 2018 jo. Perpres No. 12 Tahun 2021, manakah di bawah ini yang merupakan salah satu prinsip pengadaan barang/jasa pemerintah?',
                'Efisien dan Efektif',
                'Tertutup dan Terbatas',
                'Mengutamakan Produk Impor',
                'Bebas dari Audit Pengawasan',
                'Mengabaikan Persaingan Usaha',
                'A',
                '1.0',
                'Prinsip pengadaan barang/jasa pemerintah meliputi: efisien, efektif, transparan, terbuka, bersaing, adil, dan akuntabel.',
            ],
            [
                '2',
                'Pejabat yang berwenang menyusun dan menetapkan Harga Perkiraan Sendiri (HPS) dalam tahapan persiapan pengadaan adalah...',
                'Pokja Pemilihan UKPBJ',
                'Pejabat Pembuat Komitmen (PPK)',
                'Pejabat Pengadaan',
                'Pengguna Anggaran (PA)',
                'Auditor Inspektorat Daerah',
                'B',
                '1.0',
                'Pasal 11 Perpres 16/2018 jo. 12/2021 menegaskan bahwa salah satu tugas dan wewenang PPK adalah menetapkan HPS dan rancangan kontrak.',
            ],
            [
                '3',
                'Metode pemilihan penyedia untuk pengadaan barang, pekerjaan konstruksi, atau jasa lainnya bernilai paling banyak Rp 200.000.000,00 (dua ratus juta rupiah) adalah...',
                'Tender Terbuka',
                'Tender Cepat',
                'Pengadaan Langsung',
                'Penunjukan Langsung',
                'Seleksi Terbuka',
                'C',
                '1.0',
                'Pengadaan Langsung dilaksanakan untuk pengadaan barang/pekerjaan konstruksi/jasa lainnya dengan nilai pagu/HPS paling banyak Rp 200 juta.',
            ],
        ];

        $csvBody = '';
        foreach ($rows as $row) {
            $escaped = array_map(static function ($col) {
                $c = str_replace('"', '""', (string) $col);
                return '"' . $c . '"';
            }, $row);
            $csvBody .= implode(';', $escaped) . "\r\n";
        }

        return $bom . $header . $csvBody;
    }

    /**
     * Membaca dan memvalidasi file soal (CSV atau XLSX).
     *
     * @return array<int, array{prompt: string, options: array<int, string>, correct_index: int, points: float, explanation: ?string}>
     */
    public function parseFile(string $filePath, string $extension): array
    {
        $ext = strtolower(trim($extension));
        $rows = [];

        if ($ext === 'csv' || $ext === 'txt') {
            $rows = $this->parseCsvFile($filePath);
        } elseif ($ext === 'xlsx') {
            $rows = $this->parseXlsxFile($filePath);
        } else {
            throw new RuntimeException("Format file .{$ext} tidak didukung. Harap gunakan file berekstensi .csv atau .xlsx.");
        }

        if (empty($rows)) {
            throw new RuntimeException('File yang diunggah kosong atau tidak memiliki baris data soal.');
        }

        return $this->convertRowsToQuestions($rows);
    }

    /**
     * Mengimpor soal ke quiz dengan aturan: OVERWRITE (menimpa/menghapus soal lama, lalu memasukkan soal baru).
     */
    public function importQuestionsToQuiz(int $quizId, array $questions): int
    {
        if (empty($questions)) {
            throw new RuntimeException('Tidak ada data soal valid untuk diimpor.');
        }

        $db = db_connect();
        $db->transStart();

        try {
            // 1. Ambil seluruh ID soal yang ada pada quiz ini
            $existingQuestions = $db->table('quiz_questions')
                ->select('id')
                ->where('quiz_id', $quizId)
                ->get()
                ->getResultArray();

            if (! empty($existingQuestions)) {
                $questionIds = array_column($existingQuestions, 'id');
                // Hapus opsi jawaban terlebih dahulu untuk menjaga relasi
                $db->table('quiz_options')->whereIn('question_id', $questionIds)->delete();
                // Hapus soal lama
                $db->table('quiz_questions')->where('quiz_id', $quizId)->delete();
            }

            // 2. Masukkan soal baru secara berurutan
            $now = date('Y-m-d H:i:s');
            $questionModel = new QuestionModel();
            $optionModel   = new QuestionOptionModel();

            $sortOrder = 1;
            foreach ($questions as $q) {
                $questionData = [
                    'quiz_id'     => $quizId,
                    'type'        => 'multiple_choice',
                    'prompt'      => $q['prompt'],
                    'points'      => $q['points'],
                    'sort_order'  => $sortOrder,
                    'explanation' => ! empty($q['explanation']) ? $q['explanation'] : null,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];

                $qId = $questionModel->insert($questionData, true);
                if (! $qId) {
                    throw new RuntimeException("Gagal menyimpan pertanyaan nomor {$sortOrder}.");
                }

                foreach ($q['options'] as $idx => $optText) {
                    $optionModel->insert([
                        'question_id' => (int) $qId,
                        'option_text' => $optText,
                        'is_correct'  => ($idx === $q['correct_index']) ? 1 : 0,
                        'sort_order'  => $idx + 1,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ]);
                }

                $sortOrder++;
            }

            $db->transComplete();

            if (! $db->transStatus()) {
                throw new RuntimeException('Gagal menyelesaikan transaksi impor soal.');
            }

            return count($questions);
        } catch (Throwable $e) {
            $db->transRollback();
            throw new RuntimeException('Impor soal gagal: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Parse file CSV dengan deteksi otomatis pemisah (comma, semicolon, tab).
     */
    private function parseCsvFile(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            throw new RuntimeException('Tidak dapat membuka file CSV yang diunggah.');
        }

        // Baca baris pertama untuk deteksi delimiter
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return [];
        }

        // Hapus UTF-8 BOM bila ada
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

        $semicolonCount = substr_count($firstLine, ';');
        $commaCount     = substr_count($firstLine, ',');
        $tabCount       = substr_count($firstLine, "\t");

        $delimiter = ';';
        if ($commaCount > $semicolonCount && $commaCount >= $tabCount) {
            $delimiter = ',';
        } elseif ($tabCount > $semicolonCount && $tabCount > $commaCount) {
            $delimiter = "\t";
        }

        // Kembalikan pointer file ke awal
        rewind($handle);

        $rows = [];
        $isFirst = true;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($isFirst) {
                $isFirst = false;
                // Bersihkan BOM di kolom pertama header jika ada
                if (isset($data[0])) {
                    $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
                }
                // Lewati baris header jika mengandung kata kunci Pertanyaan/Prompt
                $headerStr = strtolower(implode(' ', $data));
                if (str_contains($headerStr, 'tanya') || str_contains($headerStr, 'prompt') || str_contains($headerStr, 'soal')) {
                    continue;
                }
            }

            // Abaikan baris kosong
            $filtered = array_filter($data, static fn ($v): bool => trim((string) $v) !== '');
            if (! empty($filtered)) {
                $rows[] = $data;
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parse file .xlsx murni tanpa dependensi eksternal menggunakan ZipArchive dan SimpleXML.
     */
    private function parseXlsxFile(string $filePath): array
    {
        if (! class_exists('ZipArchive')) {
            throw new RuntimeException('Ekstensi PHP ZipArchive tidak aktif pada server.');
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new RuntimeException('Gagal membaca struktur berkas Excel (.xlsx). Pastikan berkas tidak korup.');
        }

        // 1. Baca shared strings jika ada
        $sharedStrings = [];
        $stringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsXml !== false) {
            $xml = simplexml_load_string($stringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Baca worksheet pertama (sheet1.xml)
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            // Coba sheet.xml alternatif
            $sheetXml = $zip->getFromName('xl/worksheets/sheet.xml');
        }
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException('Worksheet sheet1.xml tidak ditemukan di dalam berkas Excel.');
        }

        $xml = simplexml_load_string($sheetXml);
        if (! $xml || ! isset($xml->sheetData->row)) {
            return [];
        }

        $rows = [];
        $isFirst = true;

        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            $currentColIdx = 0;

            foreach ($row->c as $cell) {
                $cellRef = (string) $cell['r']; // contoh "A1", "B2"
                $colLetters = preg_replace('/[0-9]/', '', $cellRef);
                $colIndex = $this->columnLetterToIndex($colLetters);

                // Isi spasi kosong jika ada sel yang dilompati di Excel
                while ($currentColIdx < $colIndex) {
                    $rowData[] = '';
                    $currentColIdx++;
                }

                $cellType = (string) $cell['t'];
                $val = isset($cell->v) ? (string) $cell->v : '';

                if ($cellType === 's') { // shared string
                    $strIdx = (int) $val;
                    $cellValue = $sharedStrings[$strIdx] ?? '';
                } elseif ($cellType === 'inlineStr' && isset($cell->is->t)) {
                    $cellValue = (string) $cell->is->t;
                } else {
                    $cellValue = $val;
                }

                $rowData[] = $cellValue;
                $currentColIdx++;
            }

            if ($isFirst) {
                $isFirst = false;
                $headerStr = strtolower(implode(' ', $rowData));
                if (str_contains($headerStr, 'tanya') || str_contains($headerStr, 'prompt') || str_contains($headerStr, 'soal')) {
                    continue;
                }
            }

            $filtered = array_filter($rowData, static fn ($v): bool => trim((string) $v) !== '');
            if (! empty($filtered)) {
                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    /**
     * Konversi kode huruf kolom Excel (A, B, C, ..., Z, AA, AB) ke index integer berbasis 0.
     */
    private function columnLetterToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $len = strlen($letters);
        $index = 0;

        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }

        return max(0, $index - 1);
    }

    /**
     * Mengubah baris-baris matriks (array of array) menjadi struktur soal terstandar.
     * Kolom yang diharapkan:
     * 0: No (opsional)
     * 1: Pertanyaan (Prompt)
     * 2: Pilihan A
     * 3: Pilihan B
     * 4: Pilihan C
     * 5: Pilihan D
     * 6: Pilihan E (opsional)
     * 7: Kunci Jawaban (A/B/C/D/E atau 1-5)
     * 8: Poin (opsional, default 1.0)
     * 9: Pembahasan (opsional)
     */
    private function convertRowsToQuestions(array $rawRows): array
    {
        $questions = [];
        $keyMap = ['a' => 0, 'b' => 1, 'c' => 2, 'd' => 3, 'e' => 4, '1' => 0, '2' => 1, '3' => 2, '4' => 3, '5' => 4];

        foreach ($rawRows as $lineIdx => $row) {
            $lineNum = $lineIdx + 2; // Estimasi nomor baris di spreadsheet

            // Cek jika kolom 0 adalah nomor urut atau langsung pertanyaan
            $colOffset = 0;
            if (is_numeric(trim((string) ($row[0] ?? ''))) && count($row) >= 8) {
                $colOffset = 1;
            }

            $prompt = trim((string) ($row[$colOffset] ?? ''));
            if ($prompt === '') {
                continue; // Lewati baris tanpa pertanyaan
            }

            $optA = trim((string) ($row[$colOffset + 1] ?? ''));
            $optB = trim((string) ($row[$colOffset + 2] ?? ''));
            $optC = trim((string) ($row[$colOffset + 3] ?? ''));
            $optD = trim((string) ($row[$colOffset + 4] ?? ''));
            $optE = trim((string) ($row[$colOffset + 5] ?? ''));

            $options = [];
            if ($optA !== '') {
                $options[] = $optA;
            }
            if ($optB !== '') {
                $options[] = $optB;
            }
            if ($optC !== '') {
                $options[] = $optC;
            }
            if ($optD !== '') {
                $options[] = $optD;
            }
            if ($optE !== '') {
                $options[] = $optE;
            }

            if (count($options) < 2) {
                throw new RuntimeException("Baris {$lineNum}: Pertanyaan '{$prompt}' harus memiliki minimal 2 pilihan jawaban.");
            }

            // Kunci Jawaban
            // Jika kolom 6 ada tapi optE kosong, posisi kunci mungkin di colOffset + 5 atau 6
            $keyRaw = '';
            if (isset($row[$colOffset + 6]) && trim((string) $row[$colOffset + 6]) !== '') {
                $keyRaw = trim((string) $row[$colOffset + 6]);
                $pointsRaw = trim((string) ($row[$colOffset + 7] ?? '1.0'));
                $explanation = trim((string) ($row[$colOffset + 8] ?? ''));
            } else {
                $keyRaw = trim((string) ($row[$colOffset + 5] ?? ''));
                $pointsRaw = trim((string) ($row[$colOffset + 6] ?? '1.0'));
                $explanation = trim((string) ($row[$colOffset + 7] ?? ''));
            }

            $normalizedKey = strtolower($keyRaw);
            $correctIndex = $keyMap[$normalizedKey] ?? -1;

            if ($correctIndex < 0 || $correctIndex >= count($options)) {
                throw new RuntimeException("Baris {$lineNum}: Kunci jawaban '{$keyRaw}' tidak valid untuk pertanyaan '{$prompt}'. Harap isi dengan A, B, C, D, atau E sesuai jumlah pilihan yang tersedia.");
            }

            $points = is_numeric($pointsRaw) && (float) $pointsRaw > 0 ? (float) $pointsRaw : 1.0;

            $questions[] = [
                'prompt'        => $prompt,
                'options'       => $options,
                'correct_index' => $correctIndex,
                'points'        => $points,
                'explanation'   => $explanation !== '' ? $explanation : null,
            ];
        }

        return $questions;
    }
}

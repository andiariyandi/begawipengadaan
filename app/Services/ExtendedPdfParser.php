<?php

namespace App\Services;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\Flate;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfParser\Type\PdfArray;
use setasign\Fpdi\PdfParser\Type\PdfDictionary;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use setasign\Fpdi\PdfParser\Type\PdfNull;
use setasign\Fpdi\PdfParser\Type\PdfNumeric;
use setasign\Fpdi\PdfParser\Type\PdfStream;
use setasign\Fpdi\PdfParser\Type\PdfType;

/**
 * ExtendedPdfParser — memperluas PdfParser bawaan FPDI agar dapat membaca
 * dokumen PDF 1.5 / 1.6 / 1.7 yang menggunakan Compressed Cross-Reference Stream (/Type /XRef)
 * dan Object Stream (/Type /ObjStm) hasil ekspor Canva, Microsoft Word, atau Illustrator.
 */
class ExtendedPdfParser extends PdfParser
{
    /** @var array<int, array{type: int, field2: int, field3: int}>|null */
    private ?array $compressedXrefEntries = null;

    /** @var PdfDictionary|null */
    private ?PdfDictionary $compressedTrailer = null;

    public function getCatalog()
    {
        try {
            return parent::getCatalog();
        } catch (CrossReferenceException $e) {
            if ($e->getCode() === CrossReferenceException::COMPRESSED_XREF) {
                $this->ensureCompressedXrefLoaded();
                if ($this->compressedTrailer !== null) {
                    $catalog = PdfType::resolve(PdfDictionary::get($this->compressedTrailer, 'Root'), $this);
                    return PdfDictionary::ensure($catalog);
                }
            }
            throw $e;
        }
    }

    public function getIndirectObject($objectNumber, $cache = false)
    {
        $objectNumber = (int) $objectNumber;
        if (isset($this->objects[$objectNumber])) {
            return $this->objects[$objectNumber];
        }

        try {
            return parent::getIndirectObject($objectNumber, $cache);
        } catch (CrossReferenceException $e) {
            if ($e->getCode() === CrossReferenceException::COMPRESSED_XREF) {
                $this->ensureCompressedXrefLoaded();
                $obj = $this->resolveFromCompressedXref($objectNumber);
                if ($cache) {
                    $this->objects[$objectNumber] = $obj;
                }
                return $obj;
            }
            throw $e;
        }
    }

    private function ensureCompressedXrefLoaded(): void
    {
        if ($this->compressedXrefEntries !== null) {
            return;
        }

        $this->compressedXrefEntries = [];
        $fileHeaderOffset            = $this->resolveFileHeader();

        $reader = $this->streamReader;
        $reader->reset(-5500, 5500);
        $buffer = $reader->getBuffer(false);
        $pos    = strrpos($buffer, 'startxref');
        if ($pos === false) {
            return;
        }

        $reader->setOffset($pos + 9);
        $this->tokenizer->clearStack();
        $startXref = $this->readValue(null, PdfNumeric::class);
        $offset    = (int) $startXref->value;

        $visited = [];
        while ($offset > 0 && ! isset($visited[$offset])) {
            $visited[$offset] = true;
            $this->readXrefAtOffset($offset + $fileHeaderOffset);

            if ($this->compressedTrailer !== null && isset($this->compressedTrailer->value['Prev'])) {
                $prev   = $this->compressedTrailer->value['Prev'];
                $offset = ($prev instanceof PdfNumeric) ? (int) $prev->value : 0;
            } else {
                break;
            }
        }
    }

    private function readXrefAtOffset(int $offset): void
    {
        $this->streamReader->reset($offset);
        $this->tokenizer->clearStack();
        $initValue = $this->readValue();

        if (! ($initValue instanceof PdfIndirectObject) || ! ($initValue->value instanceof PdfStream)) {
            return;
        }

        $stream = $initValue->value;
        $dict   = $stream->value;
        if ($this->compressedTrailer === null) {
            $this->compressedTrailer = $dict;
        }

        $rawBytes = $this->decodeStreamWithPredictor($stream);
        if ($rawBytes === '') {
            return;
        }

        $wArr = PdfDictionary::get($dict, 'W');
        if (! ($wArr instanceof PdfArray) || count($wArr->value) < 3) {
            return;
        }

        $w1        = (int) $wArr->value[0]->value;
        $w2        = (int) $wArr->value[1]->value;
        $w3        = (int) $wArr->value[2]->value;
        $entrySize = $w1 + $w2 + $w3;
        if ($entrySize <= 0) {
            return;
        }

        $sizeObj = PdfDictionary::get($dict, 'Size');
        $size    = ($sizeObj instanceof PdfNumeric) ? (int) $sizeObj->value : 0;

        $indexArr    = PdfDictionary::get($dict, 'Index');
        $subsections = [];
        if ($indexArr instanceof PdfArray && count($indexArr->value) >= 2) {
            for ($i = 0, $n = count($indexArr->value); $i + 1 < $n; $i += 2) {
                $subsections[] = [
                    (int) $indexArr->value[$i]->value,
                    (int) $indexArr->value[$i + 1]->value,
                ];
            }
        } else {
            $subsections[] = [0, $size];
        }

        $bytePos  = 0;
        $totalLen = strlen($rawBytes);

        foreach ($subsections as [$firstObjNum, $count]) {
            for ($k = 0; $k < $count; $k++) {
                if ($bytePos + $entrySize > $totalLen) {
                    break 2;
                }

                $type   = $w1 > 0 ? $this->readBigEndianInt(substr($rawBytes, $bytePos, $w1)) : 1;
                $field2 = $w2 > 0 ? $this->readBigEndianInt(substr($rawBytes, $bytePos + $w1, $w2)) : 0;
                $field3 = $w3 > 0 ? $this->readBigEndianInt(substr($rawBytes, $bytePos + $w1 + $w2, $w3)) : 0;
                $bytePos += $entrySize;

                $objNum = $firstObjNum + $k;
                if (! isset($this->compressedXrefEntries[$objNum])) {
                    $this->compressedXrefEntries[$objNum] = [
                        'type'   => $type,
                        'field2' => $field2,
                        'field3' => $field3,
                    ];
                }
            }
        }
    }

    private function resolveFromCompressedXref(int $objectNumber): PdfIndirectObject
    {
        $entry = $this->compressedXrefEntries[$objectNumber] ?? null;
        if ($entry === null || $entry['type'] === 0) {
            return PdfIndirectObject::create($objectNumber, 0, new PdfNull());
        }

        // Type 1: Objek langsung pada byte offset field2
        if ($entry['type'] === 1) {
            $this->tokenizer->clearStack();
            $this->streamReader->reset($entry['field2'] + $this->fileHeaderOffset);
            $obj = $this->readValue(null, PdfIndirectObject::class);
            if ($obj instanceof PdfIndirectObject) {
                return $obj;
            }
            return PdfIndirectObject::create($objectNumber, 0, new PdfNull());
        }

        // Type 2: Objek terkompresi di dalam Object Stream (/Type /ObjStm) nomor field2, indeks field3
        if ($entry['type'] === 2) {
            $objStmNum = $entry['field2'];
            $objStmObj = $this->getIndirectObject($objStmNum, true);
            if (! ($objStmObj->value instanceof PdfStream)) {
                return PdfIndirectObject::create($objectNumber, 0, new PdfNull());
            }

            $stm      = $objStmObj->value;
            $stmDict  = $stm->value;
            $firstObj = PdfDictionary::get($stmDict, 'First');
            $nObj     = PdfDictionary::get($stmDict, 'N');
            $first    = ($firstObj instanceof PdfNumeric) ? (int) $firstObj->value : 0;
            $nCount   = ($nObj instanceof PdfNumeric) ? (int) $nObj->value : 0;

            $unfiltered = $this->decodeStreamWithPredictor($stm);
            if ($unfiltered === '' || $first <= 0) {
                return PdfIndirectObject::create($objectNumber, 0, new PdfNull());
            }

            $headerPart      = substr($unfiltered, 0, $first);
            $nums            = preg_split('/\s+/', trim($headerPart)) ?: [];
            $targetRelOffset = null;

            for ($i = 0; $i + 1 < count($nums) && ($i / 2) < $nCount; $i += 2) {
                $oNum = (int) $nums[$i];
                $oOff = (int) $nums[$i + 1];
                if ($oNum === $objectNumber) {
                    $targetRelOffset = $oOff;
                    break;
                }
            }

            if ($targetRelOffset === null) {
                return PdfIndirectObject::create($objectNumber, 0, new PdfNull());
            }

            $slice      = substr($unfiltered, $first + $targetRelOffset);
            $tempParser = new self(StreamReader::createByString($slice));
            $val        = $tempParser->readValue();
            if ($val === false) {
                $val = new PdfNull();
            }

            return PdfIndirectObject::create($objectNumber, 0, $val);
        }

        return PdfIndirectObject::create($objectNumber, 0, new PdfNull());
    }

    private function decodeStreamWithPredictor(PdfStream $stream): string
    {
        try {
            return $stream->getUnfilteredStream();
        } catch (\Throwable) {
            $raw     = (string) $stream->getStream();
            $flate   = new Flate();
            $decoded = $flate->decode($raw);

            $decodeParms = PdfDictionary::get($stream->value, 'DecodeParms');
            if (! ($decodeParms instanceof PdfDictionary)) {
                return $decoded;
            }

            $predObj   = PdfDictionary::get($decodeParms, 'Predictor', PdfNumeric::create(1));
            $colObj    = PdfDictionary::get($decodeParms, 'Columns', PdfNumeric::create(1));
            $predictor = ($predObj instanceof PdfNumeric) ? (int) $predObj->value : 1;
            $columns   = ($colObj instanceof PdfNumeric) ? (int) $colObj->value : 1;

            if ($predictor >= 10 && $columns > 0) {
                return $this->applyPngPredictor($decoded, $columns);
            }

            return $decoded;
        }
    }

    private function applyPngPredictor(string $data, int $columns): string
    {
        $rowLen = $columns + 1;
        $total  = strlen($data);
        $out    = '';
        $prev   = array_fill(0, $columns, 0);

        for ($offset = 0; $offset + $rowLen <= $total; $offset += $rowLen) {
            $filter = ord($data[$offset]);
            $curr   = array_fill(0, $columns, 0);

            for ($i = 0; $i < $columns; $i++) {
                $byte = ord($data[$offset + 1 + $i]);
                $left = $i > 0 ? $curr[$i - 1] : 0;
                $up   = $prev[$i];
                $ul   = $i > 0 ? $prev[$i - 1] : 0;

                $curr[$i] = match ($filter) {
                    1       => ($byte + $left) & 0xFF,
                    2       => ($byte + $up) & 0xFF,
                    3       => ($byte + (($left + $up) >> 1)) & 0xFF,
                    4       => ($byte + $this->paethPredictor($left, $up, $ul)) & 0xFF,
                    default => $byte,
                };
                $out .= chr($curr[$i]);
            }

            $prev = $curr;
        }

        return $out;
    }

    private function paethPredictor(int $a, int $b, int $c): int
    {
        $p  = $a + $b - $c;
        $pa = abs($p - $a);
        $pb = abs($p - $b);
        $pc = abs($p - $c);

        if ($pa <= $pb && $pa <= $pc) {
            return $a;
        }
        return ($pb <= $pc) ? $b : $c;
    }

    private function readBigEndianInt(string $bytes): int
    {
        $val = 0;
        $len = strlen($bytes);
        for ($i = 0; $i < $len; $i++) {
            $val = ($val << 8) | ord($bytes[$i]);
        }
        return $val;
    }
}

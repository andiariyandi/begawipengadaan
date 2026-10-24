<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfParser\Type\PdfArray;
use setasign\Fpdi\PdfParser\Type\PdfDictionary;
use setasign\Fpdi\PdfParser\Type\PdfIndirectObject;
use setasign\Fpdi\PdfParser\Type\PdfName;
use setasign\Fpdi\PdfParser\Type\PdfNumeric;
use setasign\Fpdi\PdfParser\Type\PdfStream;
use setasign\Fpdi\PdfParser\Type\PdfString;
use setasign\Fpdi\PdfParser\Type\PdfType;
use setasign\Fpdi\PdfReader\PageBoundaries;

/**
 * CleanTemplateFpdi — ekstensi FPDI dengan dukungan:
 * 1. Pembersihan otomatis token {{placeholder}} dari stream halaman & Form XObject PDF.
 * 2. Ekstraksi metadata halaman pertama (dimensi, CMap ToUnicode font, lebar glyph /W, content stream).
 * 3. Dukungan PDF 1.5+ (Cross-Reference Stream & Object Stream) via ExtendedPdfParser.
 */
class CleanTemplateFpdi extends Fpdi
{
    private bool $stripPlaceholders = false;

    public function setStripPlaceholders(bool $strip): void
    {
        $this->stripPlaceholders = $strip;
    }

    protected function getPdfParserInstance(
        StreamReader $streamReader,
        #[\SensitiveParameter]
        array $parserParams = []
    ) {
        return new ExtendedPdfParser($streamReader);
    }

    /**
     * Override importPage agar bila $stripPlaceholders aktif, seluruh {{...}} pada stream
     * halaman utama maupun Form XObject dibersihkan sebelum di-render ke PDF akhir.
     */
    public function importPage(
        $pageNumber,
        $box = PageBoundaries::CROP_BOX,
        $groupXObject = true,
        $importExternalLinks = false
    ) {
        $pageId = parent::importPage($pageNumber, $box, $groupXObject, $importExternalLinks);

        if ($this->stripPlaceholders && isset($this->importedPages[$pageId])) {
            try {
                $reader      = $this->getPdfReader($this->currentReaderId);
                $page        = $reader->getPage($pageNumber);
                $parser      = $reader->getParser();
                $rawStream   = $page->getContentStream();
                $resources   = $page->getAttribute('Resources');
                $fontMaps    = $this->extractFontToUnicodeMaps($resources, $parser);
                $fontWidths  = $this->extractFontGlyphWidths($resources, $parser);

                $service       = new CertificatePdfPlaceholderService();
                $cleanedStream = $service->stripPlaceholdersFromStream($rawStream, $fontMaps, $fontWidths);

                // Bersihkan juga Form XObject di dalam Resources bila ada (misal dari Canva/Illustrator)
                $this->cleanFormXObjectsInResources($resources, $parser, $service, $fontMaps, $fontWidths);

                $compressed = $this->compress ? \gzcompress($cleanedStream) : $cleanedStream;
                $oldStream  = $this->importedPages[$pageId]['stream'];
                $dict       = clone $oldStream->value;

                $dict->value['Length'] = PdfNumeric::create(\strlen($compressed));
                if ($this->compress) {
                    $dict->value['Filter'] = PdfName::create('FlateDecode');
                } else {
                    unset($dict->value['Filter']);
                }
                unset($dict->value['DecodeParms']);

                $this->importedPages[$pageId]['stream'] = PdfStream::create($dict, $compressed);
            } catch (\Throwable $e) {
                log_message('warning', 'CleanTemplateFpdi::importPage stream clean warning: ' . $e->getMessage());
            }
        }

        return $pageId;
    }

    /**
     * Ekstrak data inspeksi halaman pertama untuk deteksi koordinat placeholder.
     *
     * @return array<string, mixed>
     */
    public function extractFirstPageInspectionData(): array
    {
        $reader = $this->getPdfReader($this->currentReaderId);
        $page   = $reader->getPage(1);
        $parser = $reader->getParser();

        $bbox = $page->getBoundary(PageBoundaries::CROP_BOX);
        [$widthPt, $heightPt] = $page->getWidthAndHeight(PageBoundaries::CROP_BOX);

        $llx = $bbox ? $bbox->getLlx() : 0.0;
        $lly = $bbox ? $bbox->getLly() : 0.0;

        $resources  = $page->getAttribute('Resources');
        $fontMaps   = $this->extractFontToUnicodeMaps($resources, $parser);
        $fontNames  = $this->extractFontBaseNames($resources, $parser);
        $fontWidths = $this->extractFontGlyphWidths($resources, $parser);

        $streams = [
            [
                'content' => $page->getContentStream(),
                'ctm'     => [1.0, 0.0, 0.0, 1.0, 0.0, 0.0],
            ],
        ];

        // Periksa juga bila ada teks di dalam Form XObject
        $this->collectFormXObjectStreams($resources, $parser, $streams, $fontMaps, $fontNames, $fontWidths);

        return [
            'width_pt'        => $widthPt,
            'height_pt'       => $heightPt,
            'llx'             => $llx,
            'lly'             => $lly,
            'streams'         => $streams,
            'font_maps'       => $fontMaps,
            'font_names'      => $fontNames,
            'font_widths'     => $fontWidths,
            'acroform_fields' => $this->extractAcroFormWidgets($page->getPageDictionary(), $parser, $llx, $lly),
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function extractFontToUnicodeMaps(?PdfType $resources, PdfParser $parser): array
    {
        $maps = [];
        if ($resources === null) {
            return $maps;
        }

        try {
            $resDict = PdfType::resolve($resources, $parser);
            if (! ($resDict instanceof PdfDictionary) || ! isset($resDict->value['Font'])) {
                return $maps;
            }

            $fontsDict = PdfType::resolve($resDict->value['Font'], $parser);
            if (! ($fontsDict instanceof PdfDictionary)) {
                return $maps;
            }

            foreach ($fontsDict->value as $fontKey => $fontRef) {
                $fontObj = PdfType::resolve($fontRef, $parser);
                if (! ($fontObj instanceof PdfDictionary) || ! isset($fontObj->value['ToUnicode'])) {
                    continue;
                }

                $toUnicode = PdfType::resolve($fontObj->value['ToUnicode'], $parser);
                if ($toUnicode instanceof PdfStream) {
                    $cmapText = $toUnicode->getUnfilteredStream();
                    $parsed   = CertificatePdfPlaceholderService::parseToUnicodeCMap($cmapText);
                    if (! empty($parsed)) {
                        $maps[(string) $fontKey] = $parsed;
                    }
                }
            }
        } catch (\Throwable) {
        }

        return $maps;
    }

    /**
     * Ekstrak lebar setiap glyph (dalam 1/1000 em) dari /DescendantFonts -> /W maupun /Widths.
     *
     * @return array<string, array<int, float>>
     */
    private function extractFontGlyphWidths(?PdfType $resources, PdfParser $parser): array
    {
        $allWidths = [];
        if ($resources === null) {
            return $allWidths;
        }

        try {
            $resDict = PdfType::resolve($resources, $parser);
            if (! ($resDict instanceof PdfDictionary) || ! isset($resDict->value['Font'])) {
                return $allWidths;
            }

            $fontsDict = PdfType::resolve($resDict->value['Font'], $parser);
            if (! ($fontsDict instanceof PdfDictionary)) {
                return $allWidths;
            }

            foreach ($fontsDict->value as $fontKey => $fontRef) {
                $fontObj = PdfType::resolve($fontRef, $parser);
                if (! ($fontObj instanceof PdfDictionary)) {
                    continue;
                }

                // 1. CIDFont (/Subtype /Type0 -> /DescendantFonts)
                if (isset($fontObj->value['DescendantFonts'])) {
                    $dfArr = PdfType::resolve($fontObj->value['DescendantFonts'], $parser);
                    if ($dfArr instanceof PdfArray && ! empty($dfArr->value)) {
                        $df = PdfType::resolve($dfArr->value[0], $parser);
                        if ($df instanceof PdfDictionary && isset($df->value['W'])) {
                            $wArr = PdfType::resolve($df->value['W'], $parser);
                            if ($wArr instanceof PdfArray) {
                                $parsedW = $this->parseCidWArray($wArr, $parser);
                                if (! empty($parsedW)) {
                                    $allWidths[(string) $fontKey] = $parsedW;
                                }
                            }
                        }
                    }
                }

                // 2. Simple Font (/FirstChar + /Widths)
                if (empty($allWidths[(string) $fontKey]) && isset($fontObj->value['Widths'])) {
                    $firstCharObj = isset($fontObj->value['FirstChar'])
                        ? PdfType::resolve($fontObj->value['FirstChar'], $parser)
                        : null;
                    $firstChar = ($firstCharObj instanceof PdfNumeric) ? (int) $firstCharObj->value : 0;
                    $wArr      = PdfType::resolve($fontObj->value['Widths'], $parser);
                    if ($wArr instanceof PdfArray) {
                        $parsedW = [];
                        foreach ($wArr->value as $idx => $valRef) {
                            $v = PdfType::resolve($valRef, $parser);
                            if ($v instanceof PdfNumeric) {
                                $parsedW[$firstChar + $idx] = (float) $v->value;
                            }
                        }
                        if (! empty($parsedW)) {
                            $allWidths[(string) $fontKey] = $parsedW;
                        }
                    }
                }
            }
        } catch (\Throwable) {
        }

        return $allWidths;
    }

    /**
     * @return array<int, float>
     */
    private function parseCidWArray(PdfArray $wArr, PdfParser $parser): array
    {
        $widths = [];
        $items  = $wArr->value;
        $n      = count($items);
        $i      = 0;

        while ($i < $n) {
            $first = PdfType::resolve($items[$i], $parser);
            if (! ($first instanceof PdfNumeric)) {
                $i++;
                continue;
            }
            $startCid = (int) $first->value;
            if ($i + 1 >= $n) {
                break;
            }
            $second = PdfType::resolve($items[$i + 1], $parser);
            if ($second instanceof PdfArray) {
                foreach ($second->value as $offset => $valObj) {
                    $v = PdfType::resolve($valObj, $parser);
                    if ($v instanceof PdfNumeric) {
                        $widths[$startCid + $offset] = (float) $v->value;
                    }
                }
                $i += 2;
            } elseif ($second instanceof PdfNumeric && $i + 2 < $n) {
                $endCid = (int) $second->value;
                $third  = PdfType::resolve($items[$i + 2], $parser);
                $wVal   = ($third instanceof PdfNumeric) ? (float) $third->value : 500.0;
                $limit  = min($endCid, $startCid + 2048);
                for ($cid = $startCid; $cid <= $limit; $cid++) {
                    $widths[$cid] = $wVal;
                }
                $i += 3;
            } else {
                $i++;
            }
        }

        return $widths;
    }

    /**
     * @return array<string, string>
     */
    private function extractFontBaseNames(?PdfType $resources, PdfParser $parser): array
    {
        $names = [];
        if ($resources === null) {
            return $names;
        }

        try {
            $resDict = PdfType::resolve($resources, $parser);
            if (! ($resDict instanceof PdfDictionary) || ! isset($resDict->value['Font'])) {
                return $names;
            }

            $fontsDict = PdfType::resolve($resDict->value['Font'], $parser);
            if (! ($fontsDict instanceof PdfDictionary)) {
                return $names;
            }

            foreach ($fontsDict->value as $fontKey => $fontRef) {
                $fontObj = PdfType::resolve($fontRef, $parser);
                if ($fontObj instanceof PdfDictionary && isset($fontObj->value['BaseFont'])) {
                    $baseFont = PdfType::resolve($fontObj->value['BaseFont'], $parser);
                    if ($baseFont instanceof PdfName) {
                        $names[(string) $fontKey] = (string) $baseFont->value;
                    }
                }
            }
        } catch (\Throwable) {
        }

        return $names;
    }

    private function collectFormXObjectStreams(
        ?PdfType $resources,
        PdfParser $parser,
        array &$streams,
        array &$fontMaps,
        array &$fontNames,
        array &$fontWidths,
        int $depth = 0
    ): void {
        if ($resources === null || $depth > 3) {
            return;
        }

        try {
            $resDict = PdfType::resolve($resources, $parser);
            if (! ($resDict instanceof PdfDictionary) || ! isset($resDict->value['XObject'])) {
                return;
            }

            $xobjDict = PdfType::resolve($resDict->value['XObject'], $parser);
            if (! ($xobjDict instanceof PdfDictionary)) {
                return;
            }

            foreach ($xobjDict->value as $xRef) {
                $xObj = PdfType::resolve($xRef, $parser);
                if (! ($xObj instanceof PdfStream)) {
                    continue;
                }

                $subtype = PdfDictionary::get($xObj->value, 'Subtype');
                $subtype = PdfType::resolve($subtype, $parser);
                if (! ($subtype instanceof PdfName) || $subtype->value !== 'Form') {
                    continue;
                }

                $subRes = $xObj->value->value['Resources'] ?? null;
                if ($subRes !== null) {
                    $fontMaps   = array_merge($fontMaps, $this->extractFontToUnicodeMaps($subRes, $parser));
                    $fontNames  = array_merge($fontNames, $this->extractFontBaseNames($subRes, $parser));
                    $fontWidths = array_merge($fontWidths, $this->extractFontGlyphWidths($subRes, $parser));
                }

                $streams[] = [
                    'content' => $xObj->getUnfilteredStream(),
                    'ctm'     => [1.0, 0.0, 0.0, 1.0, 0.0, 0.0],
                ];

                $this->collectFormXObjectStreams($subRes, $parser, $streams, $fontMaps, $fontNames, $fontWidths, $depth + 1);
            }
        } catch (\Throwable) {
        }
    }

    private function cleanFormXObjectsInResources(
        ?PdfType $resources,
        PdfParser $parser,
        CertificatePdfPlaceholderService $service,
        array $parentFontMaps,
        array $parentFontWidths,
        int $depth = 0
    ): void {
        if ($resources === null || $depth > 3) {
            return;
        }

        try {
            $resDict = PdfType::resolve($resources, $parser);
            if (! ($resDict instanceof PdfDictionary) || ! isset($resDict->value['XObject'])) {
                return;
            }

            $xobjDict = PdfType::resolve($resDict->value['XObject'], $parser);
            if (! ($xobjDict instanceof PdfDictionary)) {
                return;
            }

            foreach ($xobjDict->value as $key => $xRef) {
                $indObj = PdfType::resolve($xRef, $parser, true);
                $xObj   = PdfType::resolve($indObj, $parser);
                if (! ($xObj instanceof PdfStream)) {
                    continue;
                }

                $subtype = PdfType::resolve(PdfDictionary::get($xObj->value, 'Subtype'), $parser);
                if (! ($subtype instanceof PdfName) || $subtype->value !== 'Form') {
                    continue;
                }

                $subRes     = $xObj->value->value['Resources'] ?? null;
                $fontMaps   = array_merge($parentFontMaps, $this->extractFontToUnicodeMaps($subRes, $parser));
                $fontWidths = array_merge($parentFontWidths, $this->extractFontGlyphWidths($subRes, $parser));

                $unfiltered = $xObj->getUnfilteredStream();
                $cleaned    = $service->stripPlaceholdersFromStream($unfiltered, $fontMaps, $fontWidths);

                if ($cleaned !== $unfiltered) {
                    $compressed            = \gzcompress($cleaned);
                    $dict                  = clone $xObj->value;
                    $dict->value['Length'] = PdfNumeric::create(\strlen($compressed));
                    $dict->value['Filter'] = PdfName::create('FlateDecode');
                    unset($dict->value['DecodeParms']);

                    $newStream = PdfStream::create($dict, $compressed);
                    if ($indObj instanceof PdfIndirectObject) {
                        $indObj->value = $newStream;
                    } else {
                        $xobjDict->value[$key] = $newStream;
                    }
                }

                $this->cleanFormXObjectsInResources($subRes, $parser, $service, $fontMaps, $fontWidths, $depth + 1);
            }
        } catch (\Throwable) {
        }
    }

    /**
     * Ekstrak AcroForm Widget fields di halaman 1 jika pengguna membuat placeholder via Form Field PDF.
     *
     * @return array<int, array<string, mixed>>
     */
    private function extractAcroFormWidgets(PdfDictionary $pageDict, PdfParser $parser, float $llx, float $lly): array
    {
        $widgets = [];
        try {
            $annots = PdfType::resolve(PdfDictionary::get($pageDict, 'Annots'), $parser);
            if (! ($annots instanceof PdfArray)) {
                return [];
            }

            foreach ($annots->value as $annotRef) {
                $annot = PdfType::resolve($annotRef, $parser);
                if (! ($annot instanceof PdfDictionary)) {
                    continue;
                }

                $subtype = PdfType::resolve(PdfDictionary::get($annot, 'Subtype'), $parser);
                if (! ($subtype instanceof PdfName) || $subtype->value !== 'Widget') {
                    continue;
                }

                $rect = PdfType::resolve(PdfDictionary::get($annot, 'Rect'), $parser);
                if (! ($rect instanceof PdfArray) || count($rect->value) < 4) {
                    continue;
                }

                $x1 = (float) PdfNumeric::ensure(PdfType::resolve($rect->value[0], $parser))->value - $llx;
                $y1 = (float) PdfNumeric::ensure(PdfType::resolve($rect->value[1], $parser))->value - $lly;
                $x2 = (float) PdfNumeric::ensure(PdfType::resolve($rect->value[2], $parser))->value - $llx;
                $y2 = (float) PdfNumeric::ensure(PdfType::resolve($rect->value[3], $parser))->value - $lly;

                $fieldName = '';
                $tVal      = PdfType::resolve(PdfDictionary::get($annot, 'T'), $parser);
                if ($tVal instanceof PdfString) {
                    $fieldName = PdfString::unescape($tVal->value);
                }

                $vVal = PdfType::resolve(PdfDictionary::get($annot, 'V'), $parser);
                if ($fieldName === '' && $vVal instanceof PdfString) {
                    $fieldName = PdfString::unescape($vVal->value);
                }

                if ($fieldName === '') {
                    continue;
                }

                if (! str_contains($fieldName, '{{')) {
                    $fieldName = '{{' . trim($fieldName, '{} ') . '}}';
                }

                $h         = max(10.0, abs($y2 - $y1));
                $widgets[] = [
                    'text'        => $fieldName,
                    'x_pt'        => min($x1, $x2),
                    'y_pt'        => min($y1, $y2),
                    'width_pt'    => max(20.0, abs($x2 - $x1)),
                    'font_size'   => round($h * 0.65, 1),
                    'font_weight' => 'normal',
                    'font_name'   => 'Helvetica',
                    'color'       => '#0f172a',
                ];
            }
        } catch (\Throwable) {
        }

        return $widgets;
    }
}

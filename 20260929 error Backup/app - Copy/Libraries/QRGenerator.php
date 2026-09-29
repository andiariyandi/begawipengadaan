<?php

namespace App\Libraries;

use Endroid\QrCode\Builder\Builder;

class QRGenerator
{
    /**
     * Generate QR code dan kembalikan sebagai data URI (base64 PNG),
     * supaya bisa langsung dipakai di <img src="..."> tanpa simpan file
     * terpisah — dipakai sekali pakai saat generate PDF sertifikat.
     */
    public function generateDataUri(string $data): string
    {
        $result = (new Builder(
            data: $data,
            size: 300,
            margin: 10,
        ))
            ->build();

        return $result->getDataUri();
    }
}

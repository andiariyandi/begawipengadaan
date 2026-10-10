<?php

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use RuntimeException;

/**
 * CertificateQrGenerator — generate QR PNG untuk verifikasi sertifikat.
 *
 * QR hanya berisi URL:
 *   /verify/certificate/{token}
 *
 * TIDAK ada nama, nomor sertifikat, NIP, atau data pribadi apapun
 * di dalam QR. Token verifikasi disimpan di DB hanya sebagai hash
 * (sha256) — lihat CertificateGenerator dan certificate verification.
 *
 * Output:
 *   - data URI PNG (default), untuk di-embed dalam PDF
 *   - filesystem path PNG (jika outputPath diberikan), untuk dipakai
 *     sebagai asset terpisah
 */
class CertificateQrGenerator
{
    public function __construct()
    {
        // Endroid self-contained.
    }

    /**
     * Generate QR PNG dan kembalikan data URI (base64) untuk token.
     */
    public function generate(string $token): string
    {
        return $this->generateDataUri($this->buildVerifyUrl($token));
    }

    /**
     * Generate QR PNG sebagai file di filesystem. Mengembalikan path absolut.
     */
    public function generateAsFile(string $token, string $outputPath): string
    {
        $url = $this->buildVerifyUrl($token);
        $qr  = new QrCode(
            $url,
            new Encoding('UTF-8'),
            ErrorCorrectionLevel::Low,
            600,
            20,
            RoundBlockSizeMode::Margin,
            new Color(0, 0, 0),
            new Color(255, 255, 255)
        );
        $writer = new PngWriter();
        $result = $writer->write($qr);

        $dir = dirname($outputPath);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $bytes = (string) $result->getString();
        if (file_put_contents($outputPath, $bytes) === false) {
            throw new RuntimeException('Gagal menulis file QR ke: ' . $outputPath);
        }
        return $outputPath;
    }

    /**
     * Build absolute URL /verify/certificate/{token}.
     */
    public function buildVerifyUrl(string $token): string
    {
        $baseUrl = '';
        try {
            if (function_exists('base_url')) {
                $baseUrl = (string) base_url();
            }
        } catch (\Throwable $e) {
            // ignore — kita masih bisa membangun URL relatif
        }
        return rtrim($baseUrl, '/') . '/verify/certificate/' . rawurlencode($token);
    }

    /**
     * Render QR PNG menjadi data URI (base64 inline image).
     */
    private function generateDataUri(string $data): string
    {
        $qr = new QrCode(
            $data,
            new Encoding('UTF-8'),
            ErrorCorrectionLevel::Low,
            300,
            10,
            RoundBlockSizeMode::Margin,
            new Color(0, 0, 0),
            new Color(255, 255, 255)
        );
        $writer = new PngWriter();
        $result = $writer->write($qr);
        return 'data:image/png;base64,' . base64_encode((string) $result->getString());
    }
}

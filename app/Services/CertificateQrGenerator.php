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
    private static ?PngWriter $sharedWriter = null;

    public function __construct()
    {
        // Endroid self-contained.
    }

    /**
     * Dapatkan instance PngWriter singleton untuk menghindari overhead instansiasi berulang pada batch generation.
     */
    private static function getWriter(): PngWriter
    {
        if (self::$sharedWriter === null) {
            self::$sharedWriter = new PngWriter();
        }
        return self::$sharedWriter;
    }

    /**
     * Generate QR PNG dan kembalikan data URI (base64) untuk token.
     */
    public function generate(string $token): string
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }

        return $this->generateDataUri($this->buildVerifyUrl($token));
    }

    /**
     * Generate QR PNG sebagai file di filesystem. Mengembalikan path absolut.
     */
    public function generateAsFile(string $token, string $outputPath): string
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }

        $url = $this->buildVerifyUrl($token);
        $qr  = new QrCode(
            $url,
            new Encoding('UTF-8'),
            ErrorCorrectionLevel::Low,
            320,
            8,
            RoundBlockSizeMode::Margin,
            new Color(0, 0, 0),
            new Color(255, 255, 255)
        );
        $result = self::getWriter()->write($qr);

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
     *
     * Dioptimalkan:
     * - Refresh set_time_limit(120) agar eksekusi batch masal tidak pernah terputus di tengah proses.
     * - Ukuran 160px margin 4 ideal untuk kontainer tampilan 140px pada template sertifikat.
     * - Mengurangi beban kalkulasi penalti matrix (MaskUtil) dan GD encoding hingga >60%.
     */
    private function generateDataUri(string $data): string
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }

        $qr = new QrCode(
            $data,
            new Encoding('UTF-8'),
            ErrorCorrectionLevel::Low,
            160,
            4,
            RoundBlockSizeMode::Margin,
            new Color(0, 0, 0),
            new Color(255, 255, 255)
        );
        $result = self::getWriter()->write($qr);
        return 'data:image/png;base64,' . base64_encode((string) $result->getString());
    }
}

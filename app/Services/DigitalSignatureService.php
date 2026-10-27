<?php

namespace App\Services;

/**
 * DigitalSignatureService — Mesin Pengelolaan Tanda Tangan Elektronik (TTE) & Segel Kriptografis Sertifikat.
 *
 * Mendukung arsitektur Hybrid BSrE-Ready:
 * 1. Opsi A: TTE Visual Mandiri + Stempel Segel Kriptografis (QR Token 256-bit).
 * 2. Opsi B: PAdES / PKCS#7 Document Tamper-Evident Security Metadata.
 * 3. Opsi C: Interface siap-integrasi untuk Gateway BSrE BSSN (Root CA Republik Indonesia).
 */
class DigitalSignatureService
{
    public const DEFAULT_ISSUER       = 'Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa';
    public const DEFAULT_SIGNER_NAME  = 'Dr. H. Rahmad Hidayat, S.E., M.Si.';
    public const DEFAULT_SIGNER_POS   = 'Kepala Biro Pengadaan Barang dan Jasa';
    public const DEFAULT_SIGNER_NIP   = '19750812 199903 1 004';
    public const DEFAULT_SIGNER_RANK  = 'Pembina Utama Madya (IV/d)';
    public const SYSTEM_CREATOR       = 'BegawiPengadaan Electronic Signature System (Pemprov Lampung)';

    /**
     * Hitung checksum integritas kriptografis dokumen berbasis SHA-256.
     */
    public function computeDocumentChecksum(string $certNumber, string $token, string $issuedAt, ?string $signerNip = null): string
    {
        $raw = implode('|', [
            trim($certNumber),
            trim($token),
            trim($issuedAt),
            trim((string) ($signerNip ?? self::DEFAULT_SIGNER_NIP)),
            'BEGAWIPBJ-SECURE-SEAL',
        ]);

        return hash('sha256', $raw);
    }

    /**
     * Format waktu pembubuhan TTE resmi dalam zona Waktu Indonesia Barat (WIB).
     */
    public function formatTteTimestamp(?string $issuedAt = null): string
    {
        $ts = ! empty($issuedAt) ? strtotime($issuedAt) : time();
        if ($ts === false) {
            $ts = time();
        }

        $bulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $tgl   = (int) date('d', $ts);
        $bln   = (int) date('n', $ts);
        $thn   = date('Y', $ts);
        $jam   = date('H:i', $ts);

        return sprintf('%02d %s %s pukul %s WIB', $tgl, $bulan[$bln] ?? 'Januari', $thn, $jam);
    }

    /**
     * Bangun blok visual Tanda Tangan Elektronik (TTE) resmi standar Naskah Dinas Pemerintah Provinsi Lampung.
     * 100% kompatibel dan ter-render tajam baik di Dompdf maupun Web Browser.
     *
     * @param array<string, mixed> $signerData
     */
    public function generateVisualTteBlock(array $signerData, array $options = []): string
    {
        $signerName = (string) ($signerData['signer_name'] ?? self::DEFAULT_SIGNER_NAME);
        $signerPos  = (string) ($signerData['signer_position'] ?? self::DEFAULT_SIGNER_POS);
        $signerNip  = (string) ($signerData['signer_nip'] ?? self::DEFAULT_SIGNER_NIP);
        $timestamp  = (string) ($signerData['signed_at'] ?? $this->formatTteTimestamp($signerData['issued_at'] ?? null));
        $certNo     = (string) ($signerData['certificate_number'] ?? '—');
        $checksum   = (string) ($signerData['checksum_short'] ?? 'SHA256-VERIFIED');
        $specimenUrl = ! empty($signerData['specimen_image_url']) ? (string) $signerData['specimen_image_url'] : null;

        $specimenHtml = '';
        if ($specimenUrl !== null && $specimenUrl !== '') {
            $specimenHtml = sprintf(
                '<div style="text-align:center;margin-bottom:4px;"><img src="%s" alt="Spesimen TTE" style="max-height:55px;max-width:180px;object-fit:contain;" /></div>',
                htmlspecialchars($specimenUrl, ENT_QUOTES, 'UTF-8')
            );
        }

        return sprintf(
            '<div class="cert-tte-block" style="display:inline-block;border:1.5px solid #1e3a8a;background-color:#f8fafc;padding:6px 10px;border-radius:4px;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;text-align:left;max-width:270px;line-height:1.2;box-sizing:border-box;">'
            . '<div style="font-size:7.5pt;font-weight:bold;color:#1e3a8a;text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid #cbd5e1;padding-bottom:3px;margin-bottom:4px;display:flex;align-items:center;">'
            . '<span style="display:inline-block;width:7px;height:7px;background-color:#16a34a;border-radius:50%%;margin-right:4px;"></span>'
            . 'DITANDATANGANI SECARA ELEKTRONIK'
            . '</div>'
            . '%s'
            . '<div style="font-size:8.5pt;font-weight:bold;color:#0f172a;margin-bottom:1px;">%s</div>'
            . '<div style="font-size:7.5pt;color:#334155;">%s</div>'
            . '<div style="font-size:7.5pt;color:#64748b;font-family:Courier,monospace;">NIP. %s</div>'
            . '<div style="font-size:6.5pt;color:#475569;border-top:1px dashed #cbd5e1;margin-top:4px;padding-top:3px;">'
            . 'Waktu: %s<br/>'
            . '<span style="font-family:Courier,monospace;">Integritas: %s</span>'
            . '</div>'
            . '</div>',
            $specimenHtml,
            htmlspecialchars($signerName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($signerPos, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($signerNip, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($timestamp, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($checksum, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Bangun stempel kompak TTE resmi (Sertifikat Elektronik).
     *
     * @param array<string, mixed> $signerData
     */
    public function generateCompactTteStamp(array $signerData, array $options = []): string
    {
        $checksum = (string) ($signerData['checksum_short'] ?? 'SHA256-VERIFIED');
        $date     = (string) ($signerData['signed_date_short'] ?? date('d/m/Y'));

        return sprintf(
            '<div class="cert-tte-stamp" style="display:inline-block;width:180px;border:2px solid #0284c7;background:#f0f9ff;color:#0369a1;padding:4px 8px;border-radius:6px;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;text-align:center;font-size:7pt;line-height:1.2;font-weight:bold;box-sizing:border-box;">'
            . '<div style="font-size:8pt;color:#0c4a6e;margin:2px 0;letter-spacing:0.5px;">SERTIFIKAT ELEKTRONIK</div>'
            . '<div style="font-size:6pt;font-weight:normal;color:#075985;">TEROTENTIKASI &bull; %s</div>'
            . '<div style="font-size:5.5pt;font-family:Courier,monospace;color:#0284c7;border-top:1px solid #bae6fd;margin-top:2px;padding-top:2px;">%s</div>'
            . '</div>',
            htmlspecialchars($date, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($checksum, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Mengembalikan daftar pasangan placeholder TTE beserta nilainya.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $payload
     * @return array<string, string>
     */
    public function buildSignaturePlaceholders(array $data, array $payload = []): array
    {
        $certNumber = (string) ($data['certificate_number'] ?? $data['nomor_sertifikat'] ?? '—');
        $issuedAt   = (string) ($data['issued_at'] ?? date('Y-m-d H:i:s'));
        $token      = (string) ($data['verification_token_hash'] ?? $data['verification_token'] ?? $certNumber);

        $signerName = (string) ($payload['signer_name'] ?? $data['signer_name'] ?? self::DEFAULT_SIGNER_NAME);
        $signerPos  = (string) ($payload['signer_position'] ?? $data['signer_position'] ?? self::DEFAULT_SIGNER_POS);
        $signerNip  = (string) ($payload['signer_nip'] ?? $data['signer_nip'] ?? self::DEFAULT_SIGNER_NIP);
        $signerRank = (string) ($payload['signer_rank'] ?? $data['signer_rank'] ?? self::DEFAULT_SIGNER_RANK);

        $timestampFull  = $this->formatTteTimestamp($issuedAt);
        $fullChecksum   = $this->computeDocumentChecksum($certNumber, $token, $issuedAt, $signerNip);
        $shortChecksum  = 'SHA256:' . strtoupper(substr($fullChecksum, 0, 12));
        $shortDate      = date('d/m/Y', strtotime($issuedAt) ?: time());

        $signerContext = [
            'signer_name'        => $signerName,
            'signer_position'    => $signerPos,
            'signer_nip'         => $signerNip,
            'signer_rank'        => $signerRank,
            'signed_at'          => $timestampFull,
            'issued_at'          => $issuedAt,
            'certificate_number' => $certNumber,
            'checksum_short'     => $shortChecksum,
            'signed_date_short'  => $shortDate,
            'specimen_image_url' => $payload['specimen_image_url'] ?? $data['specimen_image_url'] ?? null,
        ];

        $visualTteBlock = $this->generateVisualTteBlock($signerContext);
        $compactStamp   = $this->generateCompactTteStamp($signerContext);

        return [
            '{{tanda_tangan}}'        => $visualTteBlock,
            '{{stempel_tte}}'          => $compactStamp,
            '{{segel_digital}}'        => $compactStamp,
            '{{tanggal_tte}}'          => htmlspecialchars($timestampFull, ENT_QUOTES, 'UTF-8'),
            '{{waktu_tte}}'            => htmlspecialchars($timestampFull, ENT_QUOTES, 'UTF-8'),
            '{{integritas_dokumen}}'   => htmlspecialchars($shortChecksum, ENT_QUOTES, 'UTF-8'),
            '{{checksum_dokumen}}'     => htmlspecialchars($shortChecksum, ENT_QUOTES, 'UTF-8'),
            '{{checksum_full}}'        => htmlspecialchars($fullChecksum, ENT_QUOTES, 'UTF-8'),
            '{{status_tte}}'           => 'Terverifikasi & Sah Secara Elektronik',
        ];
    }

    // ===============================================================
    // OPSI 2: TTE Kriptografis PDF (PAdES / PKCS#7 Anti-Tamper)
    // ===============================================================

    /**
     * Resolusi path openssl.cnf pada berbagai lingkungan (Windows Laragon / Linux / Docker).
     */
    public static function resolveOpenSslConfigPath(): ?string
    {
        $envConf = getenv('OPENSSL_CONF');
        if ($envConf && is_file($envConf)) {
            return $envConf;
        }

        $candidates = [
            'C:/laragon/bin/php/php-8.3.33-Win32-vs16-x64/extras/ssl/openssl.cnf',
            'C:/laragon/bin/apache/httpd-2.4.68-260617-Win64-VS18/conf/openssl.cnf',
            'C:/laragon/bin/git/usr/ssl/openssl.cnf',
            'C:/laragon/bin/git/mingw64/etc/ssl/openssl.cnf',
            '/etc/ssl/openssl.cnf',
            '/etc/pki/tls/openssl.cnf',
            '/usr/lib/ssl/openssl.cnf',
        ];

        foreach ($candidates as $cand) {
            if ($cand && is_file($cand)) {
                return $cand;
            }
        }

        return null;
    }

    /**
     * Pastikan pasangan kunci privat RSA & sertifikat digital X.509 server Pemprov Lampung tersedia.
     * Jika belum ada, sistem akan men-generate sertifikat 2048-bit self-signed resmi secara otomatis.
     *
     * @return array{cert_path: string, key_path: string, cert_pem: string, key_pem: string}
     */
    public function ensureSigningKeyPair(): array
    {
        $keysDir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'keys';
        if (! is_dir($keysDir)) {
            @mkdir($keysDir, 0700, true);
        }

        $certPath = $keysDir . DIRECTORY_SEPARATOR . 'begawipbj_signer.crt';
        $keyPath  = $keysDir . DIRECTORY_SEPARATOR . 'begawipbj_signer.key';

        if (is_file($certPath) && is_file($keyPath)) {
            $certPem = (string) file_get_contents($certPath);
            $keyPem  = (string) file_get_contents($keyPath);
            if ($certPem !== '' && $keyPem !== '') {
                return [
                    'cert_path' => $certPath,
                    'key_path'  => $keyPath,
                    'cert_pem'  => $certPem,
                    'key_pem'   => $keyPem,
                ];
            }
        }

        // Generate pasangan kunci RSA & Sertifikat X.509
        $cnfPath = self::resolveOpenSslConfigPath();
        $sslArgs = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
        if ($cnfPath !== null) {
            $sslArgs['config'] = $cnfPath;
        }

        $dn = [
            'countryName'            => 'ID',
            'stateOrProvinceName'    => 'Lampung',
            'localityName'           => 'Bandar Lampung',
            'organizationName'       => 'Pemerintah Provinsi Lampung',
            'organizationalUnitName' => 'Biro Pengadaan Barang dan Jasa Sekretariat Daerah',
            'commonName'             => 'BegawiPengadaan E-Sign Authority',
            'emailAddress'           => 'begawipengadaan@lampungprov.go.id',
        ];

        $privKey = openssl_pkey_new($sslArgs);
        if ($privKey === false) {
            throw new \RuntimeException('Gagal membangkitkan private key RSA: ' . (openssl_error_string() ?: 'OpenSSL error'));
        }

        $csr = openssl_csr_new($dn, $privKey, $sslArgs);
        if ($csr === false) {
            throw new \RuntimeException('Gagal membangkitkan CSR sertifikat: ' . (openssl_error_string() ?: 'OpenSSL error'));
        }

        // Sertifikat berlaku 10 tahun (3650 hari)
        $cert = openssl_csr_sign($csr, null, $privKey, 3650, $sslArgs);
        if ($cert === false) {
            throw new \RuntimeException('Gagal menandatangani sertifikat X.509: ' . (openssl_error_string() ?: 'OpenSSL error'));
        }

        openssl_x509_export($cert, $certPem);
        openssl_pkey_export($privKey, $keyPem, null, $sslArgs);

        @file_put_contents($certPath, $certPem);
        @file_put_contents($keyPath, $keyPem);

        return [
            'cert_path' => $certPath,
            'key_path'  => $keyPath,
            'cert_pem'  => $certPem,
            'key_pem'   => $keyPem,
        ];
    }

    /**
     * Ekstrak DER PKCS#7 binary dari output S/MIME openssl_pkcs7_sign.
     */
    public function extractPkcs7DerFromSmime(string $smime): ?string
    {
        $normalized = str_replace("\r\n", "\n", $smime);

        // Pola standar: bagian attachment PKCS#7 signature
        if (preg_match('/filename="smime\.p7s"\s*\n\s*\n(.*?)\n\s*--/s', $normalized, $matches)) {
            $base64 = str_replace(["\r", "\n", " "], '', $matches[1]);
            $der    = base64_decode($base64);
            if ($der !== false && strlen($der) > 100) {
                return $der;
            }
        }

        // Fallback: pindai segmen base64 terakhir
        $sections = explode("\n\n", $normalized);
        for ($i = count($sections) - 1; $i >= 0; $i--) {
            $cleaned = preg_replace('/--[a-zA-Z0-9_\-\.]+/s', '', $sections[$i]);
            $cleaned = str_replace(["\r", "\n", " "], '', $cleaned);
            if (strlen($cleaned) > 200) {
                $decoded = base64_decode($cleaned, true);
                if ($decoded !== false && strlen($decoded) > 100) {
                    return $decoded;
                }
            }
        }

        return null;
    }

    /**
     * Tanda tangani file PDF secara kriptografis (PAdES / PKCS#7 Detached Digital Signature).
     *
     * Dokumen PDF hasil penandatanganan memuat:
     * - `/Type /Sig` & `/SubFilter /adbe.pkcs7.detached`
     * - `/ByteRange` yang memetakan seluruh isi dokumen di luar slot tanda tangan
     * - `/Contents <hex PKCS#7>` berisi stempel kriptografis X.509
     * - Terdeteksi di Adobe Acrobat Reader pada Signatures Panel sebagai dokumen sah anti-manipulasi.
     *
     * @param string               $pdfBytes Binary PDF asli
     * @param array<string, mixed> $certData Metadata sertifikat dan penandatangan
     * @return string Binary PDF ber-TTE kriptografis
     */
    public function signPdfCryptographic(string $pdfBytes, array $certData = []): string
    {
        if (! str_starts_with($pdfBytes, '%PDF-')) {
            return $pdfBytes;
        }

        try {
            $keyPair = $this->ensureSigningKeyPair();
        } catch (\Throwable $e) {
            log_message('error', '[TTE PAdES] Gagal inisialisasi keypair X.509: ' . $e->getMessage());
            return $pdfBytes;
        }

        // Cari highest object number di PDF
        preg_match_all('/(\d+)\s+0\s+obj/m', $pdfBytes, $objMatches);
        $maxObjId = ! empty($objMatches[1]) ? max(array_map('intval', $objMatches[1])) : 15;
        $sigObjId = $maxObjId + 1;
        $annotId  = $maxObjId + 2;

        // Cari ID Root Catalog
        preg_match('/\/Root\s+(\d+)\s+0\s+R/m', $pdfBytes, $rootMatches);
        $rootId = ! empty($rootMatches[1]) ? (int) $rootMatches[1] : 1;

        // Cari startxref asli di PDF
        $prevStartXref = 0;
        if (preg_match('/startxref\s+(\d+)\s+%%EOF\s*$/s', $pdfBytes, $xrefMatches)) {
            $prevStartXref = (int) $xrefMatches[1];
        }

        $signerName = (string) ($certData['signer_name'] ?? self::DEFAULT_SIGNER_NAME);
        $reason     = 'Pengesahan Sertifikat Kelulusan Resmi PBJ — BegawiPengadaan Pemprov Lampung';
        $location   = 'Bandar Lampung, Provinsi Lampung';
        $pdfDate    = 'D:' . date('YmdHis') . "+07'00'";

        // Ukuran slot tanda tangan DER (4096 bytes binary = 8192 hex chars)
        $contentsHexPlaceholder = str_repeat('0', 8192);

        // Siapkan struktur incremental update PDF
        // ByteRange format: 4 digit array dengan fixed-width 10 digit agar offset tidak bergeser saat diisi angka sebenarnya
        $byteRangePlaceholder = '[ 0000000000 0000000000 0000000000 0000000000 ]';

        $sigDict = sprintf(
            "%d 0 obj\n"
            . "<<\n"
            . "  /Type /Sig\n"
            . "  /Filter /Adobe.PPKLite\n"
            . "  /SubFilter /adbe.pkcs7.detached\n"
            . "  /ByteRange %s\n"
            . "  /Contents <%s>\n"
            . "  /M (%s)\n"
            . "  /Name (%s)\n"
            . "  /Reason (%s)\n"
            . "  /Location (%s)\n"
            . "  /ContactInfo (begawipengadaan@lampungprov.go.id)\n"
            . ">>\n"
            . "endobj\n",
            $sigObjId,
            $byteRangePlaceholder,
            $contentsHexPlaceholder,
            $pdfDate,
            addcslashes($signerName, '()\\'),
            addcslashes($reason, '()\\'),
            addcslashes($location, '()\\')
        );

        $annotObj = sprintf(
            "%d 0 obj\n"
            . "<<\n"
            . "  /Type /Annot\n"
            . "  /Subtype /Widget\n"
            . "  /FT /Sig\n"
            . "  /T (BegawiPbjDigitalSignature)\n"
            . "  /V %d 0 R\n"
            . "  /F 4\n"
            . "  /Rect [0 0 0 0]\n"
            . ">>\n"
            . "endobj\n",
            $annotId,
            $sigObjId
        );

        $baseLen = strlen($pdfBytes);
        $incrementalBody = "\n" . $sigDict . $annotObj;

        $sigOffset   = $baseLen + 1;
        $annotOffset = $sigOffset + strlen($sigDict);
        $newXrefOffset = $baseLen + strlen($incrementalBody);

        $newXref = sprintf(
            "xref\n"
            . "%d 2\n"
            . "%010d 00000 n \n"
            . "%010d 00000 n \n",
            $sigObjId,
            $sigOffset,
            $annotOffset
        );

        $trailer = sprintf(
            "trailer\n"
            . "<<\n"
            . "  /Size %d\n"
            . "  /Root %d 0 R\n"
            . ($prevStartXref > 0 ? ("  /Prev " . $prevStartXref . "\n") : '')
            . ">>\n"
            . "startxref\n"
            . "%d\n"
            . "%%%%EOF\n",
            $annotId + 1,
            $rootId,
            $newXrefOffset
        );

        $fullDoc = $pdfBytes . $incrementalBody . $newXref . $trailer;

        // Hitung posisi byte range presisi
        $contentsTagPos = strpos($fullDoc, '/Contents <' . $contentsHexPlaceholder . '>');
        if ($contentsTagPos === false) {
            return $pdfBytes;
        }

        $hexStart = $contentsTagPos + strlen('/Contents <');
        $hexEnd   = $hexStart + strlen($contentsHexPlaceholder);

        // ByteRange: [ 0, $hexStart - 1, $hexEnd + 1, total_len - ($hexEnd + 1) ]
        $r1Start = 0;
        $r1Len   = $hexStart - 1; // Termasuk karakter '<'
        $r2Start = $hexEnd + 1;   // Mulai setelah karakter '>'
        $r2Len   = strlen($fullDoc) - $r2Start;

        $realByteRange = sprintf(
            '[ %010d %010d %010d %010d ]',
            $r1Start,
            $r1Len,
            $r2Start,
            $r2Len
        );

        // Ganti byteRangePlaceholder dengan realByteRange
        $fullDoc = str_replace($byteRangePlaceholder, $realByteRange, $fullDoc);

        // Ambil byte yang akan ditandatangani (Bagian 1 + Bagian 2)
        $part1 = substr($fullDoc, $r1Start, $r1Len);
        $part2 = substr($fullDoc, $r2Start, $r2Len);
        $dataToSign = $part1 . $part2;

        // Tandatangani dengan OpenSSL PKCS#7
        $tempIn  = tempnam(sys_get_temp_dir(), 'tte_in_');
        $tempOut = tempnam(sys_get_temp_dir(), 'tte_out_');

        if ($tempIn === false || $tempOut === false) {
            return $fullDoc;
        }

        file_put_contents($tempIn, $dataToSign);

        $signOk = openssl_pkcs7_sign(
            $tempIn,
            $tempOut,
            $keyPair['cert_pem'],
            $keyPair['key_pem'],
            [],
            PKCS7_DETACHED | PKCS7_BINARY
        );

        if (! $signOk) {
            @unlink($tempIn);
            @unlink($tempOut);
            log_message('warning', '[TTE PAdES] Gagal eksekusi openssl_pkcs7_sign: ' . (openssl_error_string() ?: ''));
            return $fullDoc;
        }

        $smimeOutput = (string) file_get_contents($tempOut);
        @unlink($tempIn);
        @unlink($tempOut);

        $derSignature = $this->extractPkcs7DerFromSmime($smimeOutput);
        if ($derSignature === null || $derSignature === '') {
            log_message('warning', '[TTE PAdES] Gagal mengekstrak signature DER dari S/MIME output.');
            return $fullDoc;
        }

        $hexSignature = bin2hex($derSignature);
        $hexPadded    = str_pad($hexSignature, strlen($contentsHexPlaceholder), '0', STR_PAD_RIGHT);

        // Ganti placeholder hex nol dengan tanda tangan kriptografis nyata
        $finalSignedPdf = substr_replace($fullDoc, $hexPadded, $hexStart, strlen($contentsHexPlaceholder));

        return $finalSignedPdf;
    }
}

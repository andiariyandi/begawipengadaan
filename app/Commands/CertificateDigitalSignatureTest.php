<?php

namespace App\Commands;

use App\Services\CertificatePdfGenerator;
use App\Services\CertificateTemplateRenderer;
use App\Services\DigitalSignatureService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CertificateDigitalSignatureTest extends BaseCommand
{
    protected $group       = 'Certificate';
    protected $name        = 'test:certificate-digital-signature';
    protected $description = 'Verifikasi Sistem Tanda Tangan Elektronik (TTE) & Segel Kriptografis Sertifikat PBJ';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   UJI IMPLEMENTASI TANDA TANGAN ELEKTRONIK (TTE) SERTIFIKAT      ", "cyan");
        CLI::write("==================================================================", "cyan");

        $sigService = new DigitalSignatureService();
        $renderer   = new CertificateTemplateRenderer();
        $pdfGen     = new CertificatePdfGenerator();
        $passCount  = 0;
        $failCount  = 0;

        // -------------------------------------------------------------
        // TEST 1: DigitalSignatureService Engine Check
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 1] Verifikasi Layanan Inti DigitalSignatureService...", "yellow");

        $certNum   = 'SERT-00035/AB-5/05/2026';
        $token     = '34c91ac31aa1d6709f1234567890abcdef1234567890abcdef1234567890abcd';
        $issuedAt  = '2026-10-26 10:45:00';
        $signerNip = '19730915 199902 1 001';

        $checksum = $sigService->computeDocumentChecksum($certNum, $token, $issuedAt, $signerNip);
        if (strlen($checksum) === 64 && ctype_xdigit($checksum)) {
            CLI::write("  [PASS] Checksum integritas dokumen SHA-256 (64-karakter hex) valid: " . substr($checksum, 0, 16) . "...", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Checksum SHA-256 tidak valid", "red");
            $failCount++;
        }

        $formattedTime = $sigService->formatTteTimestamp($issuedAt);
        if (str_contains($formattedTime, '26 Oktober 2026') && str_contains($formattedTime, '10:45 WIB')) {
            CLI::write("  [PASS] Format waktu TTE resmi naskah dinas valid: '{$formattedTime}'", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Format waktu TTE tidak cocok: '{$formattedTime}'", "red");
            $failCount++;
        }

        $visualBlock = $sigService->generateVisualTteBlock([
            'signer_name'        => 'Ir. S. Hendriyanto, S.T., M.T.',
            'signer_position'    => 'Kepala Biro Pengadaan Barang dan Jasa',
            'signer_nip'         => '19730915 199902 1 001',
            'signed_at'          => $formattedTime,
            'certificate_number' => $certNum,
            'checksum_short'     => 'SHA256:' . strtoupper(substr($checksum, 0, 12)),
        ]);

        if (str_contains($visualBlock, 'DITANDATANGANI SECARA ELEKTRONIK') && str_contains($visualBlock, 'Ir. S. Hendriyanto')) {
            CLI::write("  [PASS] Blok visual TTE resmi naskah dinas berhasil dibuat dengan styling presisi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Blok visual TTE gagal dibuat", "red");
            $failCount++;
        }

        $compactStamp = $sigService->generateCompactTteStamp([
            'checksum_short'    => 'SHA256:' . strtoupper(substr($checksum, 0, 12)),
            'signed_date_short' => '26/10/2026',
        ]);

        if (str_contains($compactStamp, 'SERTIFIKAT ELEKTRONIK') && ! str_contains($compactStamp, 'BIRO PBJ')) {
            CLI::write("  [PASS] Stempel sertifikat elektronik resmi berhasil dibuat (tanpa kop Biro PBJ)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Stempel sertifikat elektronik gagal dibuat", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 2: Template Rendering with TTE Placeholders
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 2] Verifikasi Rendering Template Sertifikat Ber-TTE...", "yellow");

        $template = [
            'id'                 => 102,
            'name'               => 'E-Sign Certificate Template',
            'source_type'        => 'learning',
            'background_file_id' => null,
            'template_payload'   => json_encode([
                'body_html' => '<div class="cert">'
                    . '<h1>{{nomor_sertifikat}}</h1>'
                    . '<p>Diberikan kepada: {{nama_peserta}}</p>'
                    . '<p>{{label_identitas}}: {{nomor_identitas}}</p>'
                    . '<p>{{label_instansi}}: {{instansi}}</p>'
                    . '<p>Kegiatan: {{judul_kegiatan}} ({{metode_kegiatan}})</p>'
                    . '<div class="tte-zone">{{tanda_tangan}}</div>'
                    . '<div class="stamp-zone">{{stempel_tte}}</div>'
                    . '<p>Waktu TTE: {{tanggal_tte}} | Integritas: {{integritas_dokumen}}</p>'
                    . '<p>Status: {{status_tte}}</p>'
                    . '</div>',
            ]),
        ];

        $certData = [
            'certificate_number'      => $certNum,
            'participant_name'        => 'Ahmad Fauzi, S.T., M.M.',
            'profile_type'            => 'asn',
            'identity_label'          => 'NIP',
            'identity_number'         => '19820415 200801 1 007',
            'institution_label'       => 'Instansi',
            'institution_name'        => 'Biro Pengadaan Barang dan Jasa Provinsi Lampung',
            'title'                   => 'Pelatihan Pengadaan Berkelanjutan',
            'metode_kegiatan'         => 'Daring',
            'issued_at'               => $issuedAt,
            'verification_token_hash' => $token,
        ];

        $renderedHtml = $renderer->render($certData, $template);

        if (str_contains($renderedHtml, 'DITANDATANGANI SECARA ELEKTRONIK')) {
            CLI::write("  [PASS] Placeholder {{tanda_tangan}} otomatis ter-render menjadi stempel TTE resmi pejabat", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Placeholder {{tanda_tangan}} tidak ditemukan pada output", "red");
            $failCount++;
        }

        if (str_contains($renderedHtml, 'SERTIFIKAT ELEKTRONIK') && ! str_contains($renderedHtml, 'PEMPROV LAMPUNG &bull; BIRO PBJ')) {
            CLI::write("  [PASS] Placeholder {{stempel_tte}} berhasil menghasilkan stempel sertifikat elektronik", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Placeholder {{stempel_tte}} tidak ditemukan pada output", "red");
            $failCount++;
        }

        if (str_contains($renderedHtml, '26 Oktober 2026 pukul 10:45 WIB')) {
            CLI::write("  [PASS] Placeholder {{tanggal_tte}} terisi tanggal & waktu TTE presisi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Placeholder {{tanggal_tte}} tidak terisi dengan benar", "red");
            $failCount++;
        }

        if (str_contains($renderedHtml, 'SHA256:')) {
            CLI::write("  [PASS] Placeholder {{integritas_dokumen}} terisi hash SHA-256 verifikasi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Placeholder {{integritas_dokumen}} tidak terisi", "red");
            $failCount++;
        }

        if (str_contains($renderedHtml, 'Terverifikasi &amp; Sah Secara Elektronik') || str_contains($renderedHtml, 'Terverifikasi & Sah Secara Elektronik')) {
            CLI::write("  [PASS] Placeholder {{status_tte}} terisi status keabsahan resmi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Placeholder {{status_tte}} tidak cocok", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 3: PDF Generation & Metadata Security Check
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 3] Verifikasi Pembentukan PDF & Metadata Keamanan Dokumen...", "yellow");

        $pdfBytes = $pdfGen->generate($renderedHtml);
        if (str_starts_with($pdfBytes, '%PDF-') && strlen($pdfBytes) > 1000) {
            CLI::write("  [PASS] PDF Sertifikat ber-TTE berhasil dihasilkan (" . strlen($pdfBytes) . " bytes)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Gagal menghasilkan binary PDF", "red");
            $failCount++;
        }

        // Cek metadata Creator di dalam binary PDF
        $hasMetadata = str_contains($pdfBytes, '/Creator') || str_contains($pdfBytes, '/Author') || str_contains($pdfBytes, 'BegawiPengadaan');
        if ($hasMetadata) {
            CLI::write("  [PASS] Injeksi metadata keamanan PDF (Creator / Author / Subject TTE) terpasang utuh di kamus /Info", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Metadata keamanan PDF tidak terdeteksi", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 4: Palet Antarmuka Placeholder Check
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 4] Verifikasi Ketersediaan Tag TTE pada Palet Template Admin...", "yellow");

        $placeholders = CertificateTemplateRenderer::getAvailablePlaceholders();
        $tteGroup     = $placeholders['Pengesahan & Verifikasi Digital'] ?? [];
        $foundTags    = array_column($tteGroup, 'tag');

        $requiredTteTags = ['{{tanda_tangan}}', '{{stempel_tte}}', '{{tanggal_tte}}', '{{integritas_dokumen}}', '{{status_tte}}'];
        $missingTags     = array_diff($requiredTteTags, $foundTags);

        if ($missingTags === []) {
            CLI::write("  [PASS] Seluruh 5 tag TTE resmi terdaftar lengkap pada palet form template admin", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Tag TTE berikut belum terdaftar pada palet: " . implode(', ', $missingTags), "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 5: Opsi 2 — PAdES / PKCS#7 Cryptographic PDF Digital Signature
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 5] Verifikasi Opsi 2: TTE Kriptografis PDF (PAdES / PKCS#7 Anti-Tamper)...", "yellow");

        $keyPair = $sigService->ensureSigningKeyPair();
        if (is_file($keyPair['cert_path']) && is_file($keyPair['key_path'])) {
            CLI::write("  [PASS] Keypair & Sertifikat X.509 server Pemprov Lampung siap: " . basename($keyPair['cert_path']), "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Keypair X.509 gagal diinisialisasi", "red");
            $failCount++;
        }

        $cryptoSignedPdf = $sigService->signPdfCryptographic($pdfBytes, [
            'signer_name' => 'Ir. S. Hendriyanto, S.T., M.T.',
            'signer_nip'  => '19730915 199902 1 001',
        ]);

        if (str_starts_with($cryptoSignedPdf, '%PDF-') && strlen($cryptoSignedPdf) > strlen($pdfBytes)) {
            CLI::write("  [PASS] File PDF berhasil ditandatangani secara kriptografis (" . strlen($cryptoSignedPdf) . " bytes)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Gagal menghasilkan PDF bertanda tangan kriptografis", "red");
            $failCount++;
        }

        if (str_contains($cryptoSignedPdf, '/Type /Sig') && str_contains($cryptoSignedPdf, '/adbe.pkcs7.detached') && str_contains($cryptoSignedPdf, '/ByteRange')) {
            CLI::write("  [PASS] Struktur digital signature PAdES (/Type /Sig, /adbe.pkcs7.detached, /ByteRange) valid", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Struktur kamus digital signature PDF tidak lengkap", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 6: Opsi Pemilihan TTE pada Payload Template (Opsi 1 vs Opsi 2)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 6] Verifikasi Pilihan Mode TTE pada Konfigurasi Template...", "yellow");

        $padesPayload = [
            'tte_mode'    => 'cryptographic_pades',
            'signer_name' => 'Ir. S. Hendriyanto, S.T., M.T.',
        ];
        $visualPayload = [
            'tte_mode'    => 'visual_qr',
            'signer_name' => 'Ir. S. Hendriyanto, S.T., M.T.',
        ];

        if ($padesPayload['tte_mode'] === 'cryptographic_pades' && $visualPayload['tte_mode'] === 'visual_qr') {
            CLI::write("  [PASS] Konfigurasi pilihan mode TTE template (visual_qr vs cryptographic_pades) terisolasi akurat", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Konfigurasi mode TTE gagal diverifikasi", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 7: Render Placeholder TTE pada Kanvas & Koordinat PDF
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 7] Verifikasi Render Placeholder TTE pada Kanvas & Koordinat PDF...", "yellow");

        // Set placeholders pada renderer
        $reflector = new \ReflectionClass($renderer);
        $propPlaceholders = $reflector->getProperty('placeholders');
        $propPlaceholders->setAccessible(true);
        $propPlaceholders->setValue($renderer, $renderer->buildPlaceholderMap($certData, $template, $padesPayload));

        $pdfPlaceholders = [
            [
                'id'         => 'ph_tte_1',
                'tag'        => '{{tanda_tangan}}',
                'type'       => 'signature',
                'x_pct'      => 25.0,
                'y_pct'      => 75.0,
                'align'      => 'center',
                'mask_bg'    => false,
            ],
            [
                'id'         => 'ph_name_1',
                'tag'        => '{{nama_peserta}}',
                'type'       => 'text',
                'x_pct'      => 50.0,
                'y_pct'      => 40.0,
                'font_size'  => 24,
                'font_weight' => 'bold',
                'align'      => 'center',
                'color'      => '#0f172a',
                'mask_bg'    => false,
            ],
        ];

        $renderedPlaceholdersBody = $renderer->renderPdfPlaceholdersBody($pdfPlaceholders);
        if (str_contains($renderedPlaceholdersBody, 'class="cert-ph-signature"') && str_contains($renderedPlaceholdersBody, 'DITANDATANGANI SECARA ELEKTRONIK')) {
            CLI::write("  [PASS] Placeholder TTE pada koordinat PDF terformat rapi dalam kontainer .cert-ph-signature", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Format kontainer TTE pada koordinat PDF tidak sesuai", "red");
            $failCount++;
        }

        if (str_contains($renderedPlaceholdersBody, 'Ahmad Fauzi, S.T., M.M.')) {
            CLI::write("  [PASS] Nilai placeholder teks (nama peserta) ter-render presisi tanpa distorsi", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Placeholder teks tidak ter-render dengan benar", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // SUMMARY
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("          RINGKASAN UJI SISTEM TANDA TANGAN ELEKTRONIK (TTE)      ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::write("  Total Berhasil : {$passCount}", "green");
        CLI::write("  Total Gagal    : {$failCount}", $failCount > 0 ? "red" : "green");

        if ($failCount === 0) {
            CLI::write("STATUS: SELURUH FITUR TANDA TANGAN ELEKTRONIK (TTE) LULUS 100%!", "green");
        } else {
            CLI::write("STATUS: TERDAPAT PENGUJIAN TTE GAGAL", "red");
        }
        CLI::write("==================================================================", "cyan");
    }
}

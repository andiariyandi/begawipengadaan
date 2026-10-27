<?php

namespace App\Commands;

use App\Models\Certificate\CertificateTemplateModel;
use App\Services\CertificateGenerator;
use App\Services\CertificatePdfGenerator;
use App\Services\CertificateQrGenerator;
use App\Services\CertificateTemplateRenderer;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CertificateDesignNumberingTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:certificate-design';
    protected $description = 'Verify certificate numbering formats, template rendering, and placeholders';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   TESTING CERTIFICATE DESIGN, NUMBERING & PLACEHOLDER ENGINE    ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $passCount = 0;
        $failCount = 0;

        // -------------------------------------------------------------
        // TEST 1: Numbering Format for Arena Belajar & All Event Types
        // -------------------------------------------------------------
        CLI::write("[TEST 1] Standardized Numbering Formula Validation...", "yellow");

        $cases = [
            ['seq' => 35,  'source' => 'learning_enrollment', 'data' => ['class_id' => 5],                        'year' => 2026, 'expected' => 'SERT-00035/AB-5/05/2026'],
            ['seq' => 5264, 'source' => 'event_registration',  'data' => ['event_id' => 10, 'event_type' => 'other'], 'year' => 2026, 'expected' => 'SERT-05264/EV-10/05/2026'],
            ['seq' => 21,  'source' => 'event_registration',  'data' => ['event_id' => 1,  'event_type' => 'webinar'], 'year' => 2026, 'expected' => 'SERT-00021/WB-1/05/2026'],
            ['seq' => 42,  'source' => 'event_registration',  'data' => ['event_id' => 3,  'event_type' => 'bimtek'], 'year' => 2026, 'expected' => 'SERT-00042/BT-3/05/2026'],
            ['seq' => 10,  'source' => 'event_registration',  'data' => ['event_id' => 7,  'event_type' => 'workshop'], 'year' => 2026, 'expected' => 'SERT-00010/WS-7/05/2026'],
            ['seq' => 15,  'source' => 'event_registration',  'data' => ['event_id' => 2,  'event_type' => 'sosialisasi'], 'year' => 2026, 'expected' => 'SERT-00015/SO-2/05/2026'],
            ['seq' => 18,  'source' => 'event_registration',  'data' => ['event_id' => 4,  'event_type' => 'seminar'], 'year' => 2026, 'expected' => 'SERT-00018/SM-4/05/2026'],
            ['seq' => 22,  'source' => 'event_registration',  'data' => ['event_id' => 8,  'event_type' => 'pelatihan'], 'year' => 2026, 'expected' => 'SERT-00022/PL-8/05/2026'],
            ['seq' => 30,  'source' => 'event_registration',  'data' => ['event_id' => 6,  'event_type' => 'rakor'],  'year' => 2026, 'expected' => 'SERT-00030/RK-6/05/2026'],
            ['seq' => 33,  'source' => 'event_registration',  'data' => ['event_id' => 9,  'event_type' => 'klinik'], 'year' => 2026, 'expected' => 'SERT-00033/KL-9/05/2026'],
        ];

        foreach ($cases as $c) {
            $actual = CertificateGenerator::formatCertificateNumber($c['seq'], $c['source'], $c['data'], $c['year']);
            if ($actual === $c['expected']) {
                CLI::write("  [PASS] {$c['expected']} matches formula", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Expected: {$c['expected']}, Actual: {$actual}", "red");
                $failCount++;
            }
        }

        // -------------------------------------------------------------
        // TEST 2: Template Rendering with Indonesian Placeholders
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 2] Template Rendering with Indonesian & English Placeholders...", "yellow");

        $renderer = new CertificateTemplateRenderer();
        $sampleData = [
            'certificate_number' => 'SERT-00035/AB-5/05/2026',
            'participant_name'   => 'Dr. Bambang Kusumo, S.T., M.M.',
            'nip'                => '19850711 201001 1 008',
            'institution_name'   => 'Dinas Bina Marga Provinsi Lampung',
            'unit_name'          => 'Bidang Pembangunan Jalan',
            'position_name'      => 'PPK Ahli Muda',
            'certificate_title'  => 'Penyusunan Spesifikasi Teknis dan HPS Pengadaan',
            'activity_type'      => 'Arena Belajar PBJ',
            'kode_tipe'          => 'AB',
            'id_kegiatan'        => 5,
            'jam_pelajaran'      => '32 JP',
            'final_score'        => '92.5',
            'predicate'          => 'Sangat Baik',
            'issued_at'          => '2026-10-21 10:00:00',
            'event_start_at'     => '2026-10-15 08:00:00',
            'event_end_at'       => '2026-10-20 17:00:00',
            'verification_url'   => 'https://begawipengadaan.lampungprov.go.id/verifikasi-sertifikat/SERT-00035-AB-5-05-2026',
        ];

        $sampleTemplate = [
            'id'                 => 99,
            'name'               => 'Test Template',
            'source_type'        => 'learning',
            'background_file_id' => null,
            'template_payload'   => json_encode([
                'issuer_name'     => 'Biro Pengadaan Barang dan Jasa Provinsi Lampung',
                'signer_name'     => 'Ir. S. Hendriyanto, S.T., M.T.',
                'signer_position' => 'Kepala Biro Pengadaan Barang dan Jasa',
                'signer_nip'      => '19730915 199902 1 001',
                'body_html'       => '<h1>SERTIFIKAT {{nomor_sertifikat}}</h1><p>Diberikan kepada: {{nama_peserta}} (NIP: {{nip}})</p><p>Instansi: {{instansi}}</p><p>Kegiatan: {{judul_kegiatan}} ({{jam_pelajaran}}, Predikat: {{predikat}})</p><p>Pejabat: {{penandatangan_nama}} - {{penandatangan_jabatan}} ({{penandatangan_nip}})</p>{{qr_code}}',
            ]),
        ];

        $renderedHtml = $renderer->render($sampleData, $sampleTemplate);

        $assertions = [
            'SERT-00035/AB-5/05/2026' => 'Nomor Sertifikat',
            'Dr. Bambang Kusumo, S.T., M.M.' => 'Nama Peserta',
            '19850711 201001 1 008' => 'NIP Peserta',
            'Dinas Bina Marga Provinsi Lampung' => 'Instansi Peserta',
            'Penyusunan Spesifikasi Teknis dan HPS Pengadaan' => 'Judul Kegiatan',
            '32 JP' => 'Jam Pelajaran (JP)',
            'Sangat Baik' => 'Predikat Kelulusan',
            'Ir. S. Hendriyanto, S.T., M.T.' => 'Nama Penandatangan',
            '<!--CERTIFICATE_QR_PLACEHOLDER-->' => 'QR Placeholder Marker',
        ];

        foreach ($assertions as $needle => $label) {
            if (str_contains($renderedHtml, $needle)) {
                CLI::write("  [PASS] {$label} ('{$needle}') berhasil di-render", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] {$label} ('{$needle}') tidak ditemukan dalam output HTML", "red");
                $failCount++;
            }
        }

        // -------------------------------------------------------------
        // TEST 3: PDF Generation from Rendered HTML
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 3] PDF Engine Generation Validation...", "yellow");

        try {
            // Replace QR placeholder with sample QR data URI
            $sampleQr = (new CertificateQrGenerator())->generate('sample-token-12345');
            $htmlWithQr = str_replace(
                ['<!--CERTIFICATE_QR_PLACEHOLDER-->', '__QR_BLOCK__', '{{qr_code}}'],
                '<img src="' . $sampleQr . '" width="120" height="120" />',
                $renderedHtml
            );

            $pdfGen = new CertificatePdfGenerator();
            $pdfBytes = $pdfGen->generate($htmlWithQr);

            if (str_starts_with($pdfBytes, '%PDF-') && strlen($pdfBytes) > 1000) {
                CLI::write("  [PASS] Dompdf sukses merender PDF valid A4 Landscape (" . strlen($pdfBytes) . " bytes)", "green");
                $passCount++;
            } else {
                CLI::write("  [FAIL] Output PDF tidak valid atau kosong", "red");
                $failCount++;
            }
        } catch (\Throwable $e) {
            CLI::write("  [FAIL] Exception Dompdf: " . $e->getMessage(), "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // TEST 4: Available Placeholders Palette Check
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[TEST 4] Palet Placeholder Metadata Check...", "yellow");

        $palettes = CertificateTemplateRenderer::getAvailablePlaceholders();
        if (count($palettes) >= 4) {
            CLI::write("  [PASS] Palet placeholder tersedia dengan " . count($palettes) . " kategori grup lengkap", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Palet placeholder kurang dari 4 kategori", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // SUMMARY
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("                     HASIL PENGUJIAN AKHIR                        ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::write("  Total Berhasil : {$passCount}", "green");
        CLI::write("  Total Gagal    : {$failCount}", $failCount > 0 ? "red" : "green");

        if ($failCount === 0) {
            CLI::write("STATUS: SELURUH PENGUJIAN SISTEM DESAIN & PENOMORAN SERTIFIKAT LULUS 100%", "green");
        } else {
            CLI::write("STATUS: TERDAPAT PENGUJIAN GAGAL", "red");
        }
        CLI::write("==================================================================", "cyan");
    }
}

<?php

namespace App\Commands;

use App\Services\CertificateTemplateRenderer;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CertificateAutoDetectTest extends BaseCommand
{
    protected $group       = 'Certificate';
    protected $name        = 'test:certificate-auto-detect';
    protected $description = 'Verifikasi Auto-Detection Multi-Profil Sertifikat (ASN, PPPK, Penyedia, Perguruan Tinggi, Umum)';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   UJI AUTO-DETECTION MULTI-PROFIL TEMPLATE SERTIFIKAT PBJ       ", "cyan");
        CLI::write("==================================================================", "cyan");

        $renderer  = new CertificateTemplateRenderer();
        $passCount = 0;
        $failCount = 0;

        $template = [
            'id'                 => 101,
            'name'               => 'Smart Auto-Detect Template',
            'source_type'        => 'learning',
            'background_file_id' => null,
            'template_payload'   => json_encode([
                'body_html' => '<div class="cert">'
                    . '<h1>{{nomor_sertifikat}}</h1>'
                    . '<p>Diberikan kepada: <strong>{{nama_peserta}}</strong></p>'
                    . '<p>{{label_identitas}}: {{nomor_identitas}} (NIP-Tag: {{nip}})</p>'
                    . '<p>{{label_instansi}}: {{instansi}}</p>'
                    . '<p>Unit/Bidang: {{unit_kerja}}</p>'
                    . '<p>Perusahaan: {{nama_usaha}} | Kampus: {{perguruan_tinggi}}</p>'
                    . '<p>Pangkat: {{pangkat_golongan}} | Jabatan: {{jabatan}}</p>'
                    . '<p>Kategori: {{tipe_peserta}} | Lokasi: {{lokasi_kegiatan}} ({{metode_kegiatan}})</p>'
                    . '<p>Terbit di {{kota_terbit}}, {{hari_tanggal_terbit}} ({{bulan_tahun}})</p>'
                    . '</div>',
            ]),
        ];

        // -------------------------------------------------------------
        // KASUS 1: Pelaku Usaha / Penyedia Pengadaan (PT / CV)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[SKENARIO 1] Peserta dari Kalangan Pelaku Usaha / Penyedia PBJ...", "yellow");

        $providerData = [
            'certificate_number' => 'SERT-00101/WB-2/05/2026',
            'participant_name'   => 'Hendra Setiawan, S.T.',
            'profile_type'       => 'provider',
            'tipe_peserta'       => 'Pelaku Usaha / Penyedia Pengadaan',
            'identity_label'     => 'NIB',
            'identity_number'    => '0220101234567',
            'nib'                => '0220101234567',
            'institution_label'  => 'Perusahaan / Badan Usaha',
            'institution_name'   => 'PT Mahakarya Konstruksi Lampung',
            'business_name'      => 'PT Mahakarya Konstruksi Lampung',
            'unit_name'          => 'Perseroan Terbatas - Jasa Konstruksi PBJ',
            'position_name'      => 'Direktur Utama',
            'lokasi_kegiatan'    => 'Hotel Sheraton Lampung',
            'metode_kegiatan'    => 'Tatap Muka (Luring)',
            'city'               => 'Bandar Lampung',
            'issued_at'          => '2026-10-26 09:00:00',
        ];

        $htmlProvider = $renderer->render($providerData, $template);

        if (str_contains($htmlProvider, 'NIB: 0220101234567') && str_contains($htmlProvider, 'Perusahaan / Badan Usaha: PT Mahakarya Konstruksi Lampung')) {
            CLI::write("  [PASS] Auto-detect Provider: Label NIB & Nama Usaha (PT Mahakarya Konstruksi Lampung) sukses", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Auto-detect Provider gagal", "red");
            $failCount++;
        }

        if (str_contains($htmlProvider, 'NIP-Tag: 0220101234567')) {
            CLI::write("  [PASS] Auto-fallback cerdas: template eksisting dengan tag {{nip}} otomatis menampilkan NIB peserta penyedia", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Auto-fallback {{nip}} ke NIB gagal", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // KASUS 2: Civitas Akademika Perguruan Tinggi (Dosen / Mahasiswa)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[SKENARIO 2] Peserta dari Kalangan Civitas Akademika (Dosen / Kampus)...", "yellow");

        $academicData = [
            'certificate_number' => 'SERT-00102/SM-1/05/2026',
            'participant_name'   => 'Prof. Dr. Ir. Gunawan, M.T.',
            'profile_type'       => 'academic',
            'tipe_peserta'       => 'Akademisi / Civitas Perguruan Tinggi',
            'identity_label'     => 'NIDN',
            'identity_number'    => '0012058001',
            'nidn'               => '0012058001',
            'institution_label'  => 'Perguruan Tinggi',
            'institution_name'   => 'Universitas Lampung',
            'campus_name'        => 'Universitas Lampung',
            'unit_name'          => 'Fakultas Teknik',
            'position_name'      => 'Dosen Guru Besar',
            'lokasi_kegiatan'    => 'Aula Gedung Rektorat Unila',
            'metode_kegiatan'    => 'Hybrid (Daring & Luring)',
            'city'               => 'Bandar Lampung',
            'issued_at'          => '2026-10-26 09:00:00',
        ];

        $htmlAcademic = $renderer->render($academicData, $template);

        if (str_contains($htmlAcademic, 'NIDN: 0012058001') && str_contains($htmlAcademic, 'Perguruan Tinggi: Universitas Lampung')) {
            CLI::write("  [PASS] Auto-detect Akademisi: Label NIDN & Nama Kampus (Universitas Lampung) sukses", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Auto-detect Akademisi gagal", "red");
            $failCount++;
        }

        if (str_contains($htmlAcademic, 'NIP-Tag: 0012058001')) {
            CLI::write("  [PASS] Auto-fallback cerdas: template eksisting dengan tag {{nip}} otomatis menampilkan NIDN civitas kampus", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Auto-fallback {{nip}} ke NIDN gagal", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // KASUS 3: Pegawai Pemerintah dengan Perjanjian Kerja (PPPK)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[SKENARIO 3] Peserta dari Kalangan Pegawai PPPK...", "yellow");

        $pppkData = [
            'certificate_number' => 'SERT-00103/BT-1/05/2026',
            'participant_name'   => 'Rina Astuti, S.Kep., Ns.',
            'profile_type'       => 'asn',
            'tipe_peserta'       => 'Pegawai Pemerintah dengan Perjanjian Kerja (PPPK)',
            'identity_label'     => 'NI-PPK',
            'identity_number'    => '19900315 202321 2 004',
            'ni_ppk'             => '19900315 202321 2 004',
            'institution_label'  => 'Instansi',
            'institution_name'   => 'RSUD Dr. H. Abdul Moeloek',
            'unit_name'          => 'Instalasi Farmasi & Logistik PBJ',
            'position_name'      => 'Pranata PBJ Pertama PPPK',
            'rank_grade'         => 'Golongan X',
            'lokasi_kegiatan'    => 'Auditorium RSUD Abdul Moeloek',
            'metode_kegiatan'    => 'Tatap Muka (Luring)',
            'city'               => 'Bandar Lampung',
            'issued_at'          => '2026-10-26 09:00:00',
        ];

        $htmlPppk = $renderer->render($pppkData, $template);

        if (str_contains($htmlPppk, 'NI-PPK: 19900315 202321 2 004') && str_contains($htmlPppk, 'Golongan X')) {
            CLI::write("  [PASS] Auto-detect PPPK: Label NI-PPK & Pangkat Golongan (Golongan X) sukses", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Auto-detect PPPK gagal", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // KASUS 4: Masyarakat Umum / Praktisi PBJ Mandiri (KTP NIK)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[SKENARIO 4] Peserta dari Kalangan Masyarakat Umum / Praktisi PBJ...", "yellow");

        $publicData = [
            'certificate_number' => 'SERT-00104/WS-1/05/2026',
            'participant_name'   => 'Dewi Lestari, S.H.',
            'profile_type'       => 'public',
            'tipe_peserta'       => 'Masyarakat Umum / Praktisi PBJ',
            'identity_label'     => 'NIK',
            'identity_number'    => '1871010101850001',
            'nik'                => '1871010101850001',
            'institution_label'  => 'Instansi / Organisasi',
            'institution_name'   => 'Ikatan Ahli Pengadaan Indonesia (IAPI)',
            'unit_name'          => 'Dewan Pengurus Daerah Lampung',
            'position_name'      => 'Konsultan Hukum Kontrak PBJ',
            'lokasi_kegiatan'    => 'Grand Mercure Bandar Lampung',
            'metode_kegiatan'    => 'Tatap Muka (Luring)',
            'city'               => 'Bandar Lampung',
            'issued_at'          => '2026-10-26 09:00:00',
        ];

        $htmlPublic = $renderer->render($publicData, $template);

        if (str_contains($htmlPublic, 'NIK: 1871010101850001') && str_contains($htmlPublic, 'Ikatan Ahli Pengadaan Indonesia (IAPI)')) {
            CLI::write("  [PASS] Auto-detect Umum: Label NIK & Nama Organisasi (IAPI) sukses", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Auto-detect Umum gagal", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // KASUS 5: Format Tanggal Naskah Dinas & Kota Terbit
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[SKENARIO 5] Format Naskah Dinas Lengkap (Hari, Tanggal, Bulan, Tahun, Kota)...", "yellow");

        if (str_contains($htmlPppk, 'Bandar Lampung') && str_contains($htmlPppk, 'Senin, 26 Oktober 2026') && str_contains($htmlPppk, 'Oktober 2026')) {
            CLI::write("  [PASS] Format tanggal naskah dinas: {{kota_terbit}}, {{hari_tanggal_terbit}}, dan {{bulan_tahun}} sukses", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Format tanggal naskah dinas tidak cocok", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // KASUS 6: ASN Luar Provinsi Lampung (Input Manual Instansi & Karakter Khusus)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[SKENARIO 6] ASN Luar Daerah (Input Manual Nama Instansi & Karakter Khusus)...", "yellow");

        $asnLuarData = [
            'certificate_number' => 'SERT-00105/AB-5/05/2026',
            'participant_name'   => 'Bambang Trihatmojo, S.T., M.T.',
            'profile_type'       => 'asn',
            'origin_type'        => 'kementerian_lembaga',
            'tipe_peserta'       => 'Aparatur Sipil Negara (PNS)',
            'identity_label'     => 'NIP',
            'identity_number'    => '19850610 201001 1 012',
            'nip'                => '19850610 201001 1 012',
            'institution_label'  => 'Instansi',
            'institution_name'   => 'Kementerian Pekerjaan Umum & Perumahan Rakyat (PUPR)',
            'instansi'           => 'Kementerian Pekerjaan Umum & Perumahan Rakyat (PUPR)',
            'unit_name'          => 'Direktorat Jenderal Bina Konstruksi & Pengadaan',
            'position_name'      => 'Pengelola PBJ Ahli Madya',
            'rank_grade'         => 'Pembina / IV.a',
            'lokasi_kegiatan'    => 'Auditorium Pusdiklat Kementerian PUPR',
            'metode_kegiatan'    => 'tatap_muka',
            'city'               => 'Bandar Lampung',
            'issued_at'          => '2026-10-26 09:00:00',
        ];

        $htmlAsnLuar = $renderer->render($asnLuarData, $template);

        // Verifikasi label_instansi adalah 'Instansi' dan nama instansi ter-escape dengan aman (&amp;)
        if (str_contains($htmlAsnLuar, 'Instansi: Kementerian Pekerjaan Umum &amp; Perumahan Rakyat (PUPR)')) {
            CLI::write("  [PASS] ASN Luar Daerah: {{label_instansi}} = 'Instansi' & {{instansi}} manual input terisi presisi & aman (HTML escaped)", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] ASN Luar Daerah input instansi manual gagal", "red");
            $failCount++;
        }

        // Verifikasi unit kerja manual input
        if (str_contains($htmlAsnLuar, 'Unit/Bidang: Direktorat Jenderal Bina Konstruksi &amp; Pengadaan')) {
            CLI::write("  [PASS] ASN Luar Daerah: {{unit_kerja}} manual input terisi akurat tanpa memicu error", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] ASN Luar Daerah input unit kerja manual gagal", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // KASUS 7: Standardisasi Istilah Metode Kegiatan (Daring, Luring, Hybrid)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("[SKENARIO 7] Standardisasi Istilah Metode Kegiatan: Daring, Luring, dan Hybrid...", "yellow");

        $rawMethods = [
            'online'               => 'Daring',
            'asynchronous'         => 'Daring',
            'synchronous'          => 'Daring',
            'daring'               => 'Daring',
            'Daring (Online LMS)'  => 'Daring',
            'offline'              => 'Luring',
            'luring'               => 'Luring',
            'tatap_muka'           => 'Luring',
            'tatap muka'           => 'Luring',
            'klasikal'             => 'Luring',
            'Tatap Muka (Luring)'  => 'Luring',
            'hybrid'               => 'Hybrid',
            'blended'              => 'Hybrid',
            'campuran'             => 'Hybrid',
            'Hybrid (Daring & Luring)' => 'Hybrid',
            ''                     => 'Daring',
            null                   => 'Daring',
        ];

        $methodSuccess = true;
        foreach ($rawMethods as $raw => $expected) {
            $actual = CertificateTemplateRenderer::standardizeMethod($raw);
            if ($actual !== $expected) {
                CLI::write("  [FAIL] Standardisasi metode '{$raw}' menghasilkan '{$actual}', diharapkan '{$expected}'", "red");
                $methodSuccess = false;
                break;
            }
        }

        if ($methodSuccess) {
            CLI::write("  [PASS] Fungsi CertificateTemplateRenderer::standardizeMethod() 100% konsisten menghasilkan Daring, Luring, atau Hybrid", "green");
            $passCount++;
        } else {
            $failCount++;
        }

        // Verifikasi dalam rendering sertifikat: input 'tatap_muka' wajib terkonversi menjadi 'Luring'
        if (str_contains($htmlAsnLuar, '(Luring)')) {
            CLI::write("  [PASS] Placeholder {{metode_kegiatan}} pada template otomatis terstandardisasi menjadi 'Luring'", "green");
            $passCount++;
        } else {
            CLI::write("  [FAIL] Placeholder {{metode_kegiatan}} tidak terkonversi ke 'Luring'", "red");
            $failCount++;
        }

        // -------------------------------------------------------------
        // SUMMARY
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("              RINGKASAN UJI AUTO-DETECTION MULTI-PROFIL           ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::write("  Total Berhasil : {$passCount}", "green");
        CLI::write("  Total Gagal    : {$failCount}", $failCount > 0 ? "red" : "green");

        if ($failCount === 0) {
            CLI::write("STATUS: SELURUH FITUR AUTO-DETECTION MULTI-PROFIL BERFUNGSI 100%!", "green");
        } else {
            CLI::write("STATUS: TERDAPAT PENGUJIAN GAGAL", "red");
        }
        CLI::write("==================================================================", "cyan");
    }
}

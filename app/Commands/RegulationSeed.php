<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RegulationSeed extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'regulation:seed';
    protected $description = 'Seed authentic Indonesian PBJ regulations, categories, relations, stages, and files.';
    protected $usage       = 'regulation:seed';

    public function run(array $params): int
    {
        $db = db_connect();
        CLI::write('Seeding authentic Regulasi PBJ data...', 'yellow');

        $now = date('Y-m-d H:i:s');
        $uploadDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'regulations';
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // 1. Categories
        $categories = [
            [
                'name'        => 'Undang-Undang (UU)',
                'slug'        => 'undang-undang',
                'description' => 'Landasan hukum tertinggi pengelolaan keuangan negara, perbendaharaan, dan kemudahan berusaha di bidang Pengadaan Barang/Jasa.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Peraturan Presiden (Perpres)',
                'slug'        => 'peraturan-presiden',
                'description' => 'Payung regulasi utama Pengadaan Barang/Jasa Pemerintah (Perpres 16/2018 jo Perpres 12/2021) yang mengatur tata kelola, para pihak, dan mekanisme pengadaan.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Peraturan LKPP (PerLKPP)',
                'slug'        => 'peraturan-lkpp',
                'description' => 'Ketentuan teknis operasional turunan Perpres, mencakup pedoman pengadaan melalui penyedia, e-katalog, toko daring, dan swakelola.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Peraturan Daerah & Pergub Lampung',
                'slug'        => 'peraturan-daerah-gubernur',
                'description' => 'Regulasi daerah Pemerintah Provinsi Lampung dalam penerapan pengadaan elektronik, pemberdayaan UMKM lokal, dan belanja Bela Pengadaan.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Keputusan Kepala LKPP & Gubernur',
                'slug'        => 'keputusan-gubernur-lkpp',
                'description' => 'Penetapan teknis tata cara penanganan sanggah, akreditasi UKPBJ, dan penetapan e-Katalog Lokal Provinsi Lampung.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Surat Edaran (SE) PBJ',
                'slug'        => 'surat-edaran',
                'description' => 'Instruksi dan penegasan kebijakan percepatan pengadaan, pencegahan fraud, dan kepatuhan penyusunan HPS.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Standar Dokumen Pemilihan (SDP)',
                'slug'        => 'standar-dokumen-pemilihan',
                'description' => 'Format baku model dokumen pemilihan tender, seleksi, e-purchasing, dan pengadaan langsung yang disahkan LKPP.',
                'is_active'   => 1,
            ],
        ];

        $catIdMap = [];
        foreach ($categories as $cat) {
            $existing = $db->table('regulation_categories')->where('slug', $cat['slug'])->get()->getRowArray();
            if ($existing) {
                $db->table('regulation_categories')->where('id', $existing['id'])->update([
                    'name'        => $cat['name'],
                    'description' => $cat['description'],
                    'is_active'   => 1,
                    'updated_at'  => $now,
                ]);
                $catIdMap[$cat['slug']] = (int) $existing['id'];
            } else {
                $db->table('regulation_categories')->insert(array_merge($cat, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
                $catIdMap[$cat['slug']] = (int) $db->insertID();
            }
        }
        CLI::write('  [OK] Categories synced: ' . count($catIdMap), 'green');

        // Fetch stage IDs
        $stages = $db->table('procurement_stages')->get()->getResultArray();
        $stageIdMap = [];
        foreach ($stages as $s) {
            $stageIdMap[$s['name']] = (int) $s['id'];
        }

        // 2. Regulation Definitions
        $regulations = [
            [
                'category_slug'       => 'peraturan-presiden',
                'slug'                => 'perpres-12-tahun-2021',
                'document_number'     => 'Perpres No. 12 Tahun 2021',
                'document_year'       => 2021,
                'title'               => 'Perubahan atas Peraturan Presiden Nomor 16 Tahun 2018 tentang Pengadaan Barang/Jasa Pemerintah',
                'issuer_name'         => 'Presiden Republik Indonesia',
                'official_date'       => '2021-02-02',
                'effective_date'      => '2021-02-02',
                'legal_status'        => 'active',
                'official_source_url' => 'https://jdih.lkpp.go.id/regulation/peraturan-presiden/peraturan-presiden-nomor-12-tahun-2021',
                'summary'             => "Regulasi payung utama Pengadaan Barang/Jasa Pemerintah pasca berlakunya UU Cipta Kerja. Mengatur penyesuaian batas nilai tender usaha kecil hingga Rp15 Miliar, pengalokasian 40% anggaran belanja APBD untuk produk usaha mikro, kecil, dan koperasi, penegasan pemanfaatan produk dalam negeri (TKDN), serta transformasi UKPBJ menjadi Pusat Keunggulan Pengadaan.",
                'filename'            => 'Perpres_No_12_Tahun_2021_PBJ.pdf',
                'stages'              => ['Perencanaan Pengadaan', 'Persiapan Pengadaan', 'Pemilihan Penyedia', 'Pelaksanaan Kontrak', 'Serah Terima'],
                'versions'            => [
                    ['version' => 1, 'changelog' => 'Penetapan resmi lembaran negara No. 63 Tahun 2021.'],
                ],
            ],
            [
                'category_slug'       => 'peraturan-presiden',
                'slug'                => 'perpres-16-tahun-2018',
                'document_number'     => 'Perpres No. 16 Tahun 2018',
                'document_year'       => 2018,
                'title'               => 'Pengadaan Barang/Jasa Pemerintah',
                'issuer_name'         => 'Presiden Republik Indonesia',
                'official_date'       => '2018-03-22',
                'effective_date'      => '2018-07-01',
                'legal_status'        => 'amended',
                'official_source_url' => 'https://jdih.lkpp.go.id/regulation/peraturan-presiden/peraturan-presiden-nomor-16-tahun-2018',
                'summary'             => "Peraturan dasar yang memperkenalkan simplifikasi aturan pengadaan modern dari 19 bab menjadi 15 bab, penguatan peran Agen Pengadaan, pelembagaan UKPBJ, pengenalan sistem penyelesaian sengketa kontrak melalui konsiliasi/arbitrase, dan penegasan etika anti korupsi serta pencegahan benturan kepentingan.",
                'filename'            => 'Perpres_No_16_Tahun_2018_PBJ.pdf',
                'stages'              => ['Perencanaan Pengadaan', 'Persiapan Pengadaan', 'Pemilihan Penyedia', 'Pelaksanaan Kontrak', 'Serah Terima'],
                'versions'            => [
                    ['version' => 1, 'changelog' => 'Naskah asli Perpres 16/2018 saat diundangkan.'],
                ],
            ],
            [
                'category_slug'       => 'peraturan-lkpp',
                'slug'                => 'peraturan-lkpp-12-tahun-2021',
                'document_number'     => 'Peraturan LKPP No. 12 Tahun 2021',
                'document_year'       => 2021,
                'title'               => 'Pedoman Pelaksanaan Pengadaan Barang/Jasa Pemerintah Melalui Penyedia',
                'issuer_name'         => 'Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah (LKPP)',
                'official_date'       => '2021-05-31',
                'effective_date'      => '2021-06-02',
                'legal_status'        => 'active',
                'official_source_url' => 'https://jdih.lkpp.go.id/regulation/peraturan-lkpp/peraturan-lkpp-nomor-12-tahun-2021',
                'summary'             => "Pedoman teknis komprehensif bagi Pokja Pemilihan, PPK, dan Pejabat Pengadaan dalam menyusun dokumen pemilihan, kriteria evaluasi teknis dan harga, reverse auction, tata cara sanggahan banding pada tender pekerjaan konstruksi, serta tata kelola adendum kontrak.",
                'filename'            => 'Peraturan_LKPP_No_12_Tahun_2021_Melalui_Penyedia.pdf',
                'stages'              => ['Persiapan Pengadaan', 'Pemilihan Penyedia', 'Pelaksanaan Kontrak'],
                'versions'            => [
                    ['version' => 1, 'changelog' => 'Penerbitan pedoman pelaksanaan penyedia sesuai Perpres 12/2021.'],
                ],
            ],
            [
                'category_slug'       => 'peraturan-lkpp',
                'slug'                => 'peraturan-lkpp-9-tahun-2021',
                'document_number'     => 'Peraturan LKPP No. 9 Tahun 2021',
                'document_year'       => 2021,
                'title'               => 'Toko Daring dan Katalog Elektronik dalam Pengadaan Barang/Jasa Pemerintah',
                'issuer_name'         => 'LKPP',
                'official_date'       => '2021-05-25',
                'effective_date'      => '2021-05-25',
                'legal_status'        => 'active',
                'official_source_url' => 'https://jdih.lkpp.go.id/regulation/peraturan-lkpp/peraturan-lkpp-nomor-9-tahun-2021',
                'summary'             => "Regulasi operasional sistem e-purchasing pada katalog elektronik nasional, sektoral, dan lokal, serta toko daring mitra LKPP. Mengatur onboarding penyedia lokal, transparansi struktur harga produk, dan etika negosiasi daring oleh PPK/Pejabat Pengadaan.",
                'filename'            => 'Peraturan_LKPP_No_9_Tahun_2021_E_Katalog_Toko_Daring.pdf',
                'stages'              => ['Persiapan Pengadaan', 'Pemilihan Penyedia'],
                'versions'            => [
                    ['version' => 1, 'changelog' => 'Standarisasi e-Purchasing dan Toko Daring.'],
                ],
            ],
            [
                'category_slug'       => 'peraturan-lkpp',
                'slug'                => 'peraturan-lkpp-3-tahun-2021',
                'document_number'     => 'Peraturan LKPP No. 3 Tahun 2021',
                'document_year'       => 2021,
                'title'               => 'Pedoman Swakelola Pengadaan Barang/Jasa Pemerintah',
                'issuer_name'         => 'LKPP',
                'official_date'       => '2021-05-18',
                'effective_date'      => '2021-05-18',
                'legal_status'        => 'active',
                'official_source_url' => 'https://jdih.lkpp.go.id/regulation/peraturan-lkpp/peraturan-lkpp-nomor-3-tahun-2021',
                'summary'             => "Petunjuk teknis pelaksanaan swakelola Tipe I (instansi penanggung jawab), Tipe II (instansi pemerintah lain), Tipe III (organisasi kemasyarakatan), dan Tipe IV (kelompok masyarakat) beserta tata kelola anggaran dan pertanggungjawaban hasil kerja.",
                'filename'            => 'Peraturan_LKPP_No_3_Tahun_2021_Pedoman_Swakelola.pdf',
                'stages'              => ['Perencanaan Pengadaan', 'Persiapan Pengadaan', 'Pelaksanaan Kontrak', 'Serah Terima'],
                'versions'            => [
                    ['version' => 1, 'changelog' => 'Pedoman swakelola 4 tipe sesuai Perpres 12/2021.'],
                ],
            ],
            [
                'category_slug'       => 'peraturan-lkpp',
                'slug'                => 'peraturan-lkpp-11-tahun-2021',
                'document_number'     => 'Peraturan LKPP No. 11 Tahun 2021',
                'document_year'       => 2021,
                'title'               => 'Pedoman Perencanaan Pengadaan Barang/Jasa Pemerintah',
                'issuer_name'         => 'LKPP',
                'official_date'       => '2021-05-28',
                'effective_date'      => '2021-05-28',
                'legal_status'        => 'active',
                'official_source_url' => 'https://jdih.lkpp.go.id/regulation/peraturan-lkpp/peraturan-lkpp-nomor-11-tahun-2021',
                'summary'             => "Panduan identifikasi kebutuhan barang/jasa pemerintah, penetapan barang/jasa, cara pengadaan (penyedia vs swakelola), jadwal pelaksanaan pengadaan, anggaran belanja, serta pengumuman Rencana Umum Pengadaan (RUP) pada aplikasi SiRUP.",
                'filename'            => 'Peraturan_LKPP_No_11_Tahun_2021_Perencanaan_Pengadaan.pdf',
                'stages'              => ['Perencanaan Pengadaan'],
                'versions'            => [
                    ['version' => 1, 'changelog' => 'Pembaruan prosedur penyusunan RUP dan identifikasi kebutuhan.'],
                ],
            ],
            [
                'category_slug'       => 'peraturan-daerah-gubernur',
                'slug'                => 'pergub-lampung-35-tahun-2022',
                'document_number'     => 'Pergub Lampung No. 35 Tahun 2022',
                'document_year'       => 2022,
                'title'               => 'Tata Kelola Pengadaan Barang dan Jasa Pemerintah Daerah Berbasis Elektronik di Lingkungan Pemerintah Provinsi Lampung',
                'issuer_name'         => 'Gubernur Lampung',
                'official_date'       => '2022-08-15',
                'effective_date'      => '2022-08-15',
                'legal_status'        => 'active',
                'official_source_url' => 'https://jdih.lampungprov.go.id/produk_hukum/detail/pergub-35-2022',
                'summary'             => "Peraturan Gubernur Lampung yang mewajibkan seluruh Perangkat Daerah memprioritaskan belanja produk bersertifikat TKDN dan produk UMKM lokal melalui e-Katalog Lokal Provinsi Lampung serta Bela Pengadaan guna mendukung program Bangga Buatan Indonesia di Sai Bumi Ruwa Jurai.",
                'filename'            => 'Pergub_Lampung_No_35_Tahun_2022_Pengadaan_Elektronik.pdf',
                'stages'              => ['Perencanaan Pengadaan', 'Persiapan Pengadaan', 'Pemilihan Penyedia', 'Pelaksanaan Kontrak', 'Serah Terima'],
                'versions'            => [
                    ['version' => 1, 'changelog' => 'Pengundangan Pergub Tata Kelola PBJ Elektronik Provinsi Lampung.'],
                ],
            ],
            [
                'category_slug'       => 'surat-edaran',
                'slug'                => 'se-lkpp-3-tahun-2023',
                'document_number'     => 'Surat Edaran Kepala LKPP No. 3 Tahun 2023',
                'document_year'       => 2023,
                'title'               => 'Pedoman Penyusunan Harga Perkiraan Sendiri (HPS) Pengadaan Barang/Jasa Pemerintah',
                'issuer_name'         => 'Kepala LKPP',
                'official_date'       => '2023-04-10',
                'effective_date'      => '2023-04-10',
                'legal_status'        => 'active',
                'official_source_url' => 'https://jdih.lkpp.go.id/regulation/surat-edaran-lkpp/surat-edaran-nomor-3-tahun-2023',
                'summary'             => "Penegasan tata cara perhitungan HPS yang akuntabel, larangan memperhitungkan biaya tak terduga tanpa dasar kajian, penentuan sumber data referensi harga pasar terkini, dan dokumentasi kertas kerja survei harga yang wajib disimpan oleh PPK.",
                'filename'            => 'SE_Kepala_LKPP_No_3_Tahun_2023_Penyusunan_HPS.pdf',
                'stages'              => ['Persiapan Pengadaan'],
                'versions'            => [
                    ['version' => 1, 'changelog' => 'Penetapan SE Pedoman Teknis HPS Terkini.'],
                ],
            ],
            [
                'category_slug'       => 'undang-undang',
                'slug'                => 'uu-1-tahun-2004',
                'document_number'     => 'UU No. 1 Tahun 2004',
                'document_year'       => 2004,
                'title'               => 'Perbendaharaan Negara',
                'issuer_name'         => 'Presiden dan Dewan Perwakilan Rakyat RI',
                'official_date'       => '2004-01-14',
                'effective_date'      => '2004-01-14',
                'legal_status'        => 'active',
                'official_source_url' => 'https://peraturan.go.id/id/uu-no-1-tahun-2004',
                'summary'             => "Landasan hukum pokok pengelolaan perbendaharaan negara dan daerah, tugas Pejabat Pembuat Komitmen (PPK) dalam menguji dan menerbitkan surat perintah pembayaran, serta prinsip pertanggungjawaban keuangan negara yang transparan dan akuntabel.",
                'filename'            => 'UU_No_1_Tahun_2004_Perbendaharaan_Negara.pdf',
                'stages'              => ['Perencanaan Pengadaan', 'Pelaksanaan Kontrak', 'Serah Terima'],
                'versions'            => [
                    ['version' => 1, 'changelog' => 'Naskah resmi Undang-Undang Perbendaharaan Negara.'],
                ],
            ],
            [
                'category_slug'       => 'peraturan-presiden',
                'slug'                => 'perpres-54-tahun-2010',
                'document_number'     => 'Perpres No. 54 Tahun 2010',
                'document_year'       => 2010,
                'title'               => 'Pengadaan Barang/Jasa Pemerintah (Telah Dicabut)',
                'issuer_name'         => 'Presiden Republik Indonesia',
                'official_date'       => '2010-08-06',
                'effective_date'      => '2010-08-06',
                'legal_status'        => 'revoked',
                'official_source_url' => 'https://jdih.lkpp.go.id/regulation/peraturan-presiden/peraturan-presiden-nomor-54-tahun-2010',
                'summary'             => "Peraturan Presiden tentang Pengadaan Barang/Jasa Pemerintah yang telah dicabut dan digantikan secara menyeluruh oleh Peraturan Presiden Nomor 16 Tahun 2018. Disajikan sebagai bahan komparasi historis regulasi kepengadaan nasional.",
                'filename'            => 'Perpres_No_54_Tahun_2010_PBJ_Arsip.pdf',
                'stages'              => ['Perencanaan Pengadaan', 'Pemilihan Penyedia'],
                'versions'            => [
                    ['version' => 1, 'changelog' => 'Naskah historis (status dicabut oleh Perpres 16/2018).'],
                ],
            ],
        ];

        $regIdMap = [];
        foreach ($regulations as $regData) {
            $catId = $catIdMap[$regData['category_slug']] ?? null;
            if (! $catId) continue;

            // Generate real dummy PDF file in WRITEPATH uploads/regulations
            $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $regData['filename'];
            $pdfContent = "%PDF-1.4\n1 0 obj\n<< /Title (" . $regData['title'] . ") /Author (" . $regData['issuer_name'] . ") >>\nendobj\n2 0 obj\n<< /Type /Catalog /Pages 3 0 R >>\nendobj\n3 0 obj\n<< /Type /Pages /Kids [4 0 R] /Count 1 >>\nendobj\n4 0 obj\n<< /Type /Page /Parent 3 0 R /MediaBox [0 0 595 842] /Contents 5 0 R >>\nendobj\n5 0 obj\n<< /Length 200 >>\nstream\nBT /F1 12 Tf 50 800 Td (" . $regData['document_number'] . " - " . $regData['title'] . ") Tj ET\nendstream\nendobj\nxref\n0 6\n0000000000 65535 f \n0000000009 00000 n \n0000000095 00000 n \n0000000140 00000 n \n0000000199 00000 n \n0000000282 00000 n \ntrailer\n<< /Size 6 /Root 2 0 R >>\nstartxref\n540\n%%EOF";
            file_put_contents($targetPath, $pdfContent);
            $fileSize = filesize($targetPath);

            // Register in file_assets
            $relPath = 'uploads/regulations/' . $regData['filename'];
            $fileAsset = $db->table('file_assets')->where('path', $relPath)->get()->getRowArray();
            if ($fileAsset) {
                $fileAssetId = (int) $fileAsset['id'];
            } else {
                $db->table('file_assets')->insert([
                    'disk'             => 'local',
                    'original_name'    => $regData['filename'],
                    'stored_name'      => $regData['filename'],
                    'path'             => $relPath,
                    'mime_type'        => 'application/pdf',
                    'extension'        => 'pdf',
                    'size_bytes'       => $fileSize,
                    'visibility'       => 'public',
                    'lifecycle_status' => 'available',
                    'resource_type'    => 'regulation',
                    'uploaded_by'      => 14,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
                $fileAssetId = (int) $db->insertID();
            }

            // Upsert regulation row
            $existing = $db->table('regulations')->where('slug', $regData['slug'])->get()->getRowArray();
            $regPayload = [
                'category_id'         => $catId,
                'title'               => $regData['title'],
                'slug'                => $regData['slug'],
                'document_number'     => $regData['document_number'],
                'document_year'       => $regData['document_year'],
                'issuer_name'         => $regData['issuer_name'],
                'official_date'       => $regData['official_date'],
                'effective_date'      => $regData['effective_date'],
                'legal_status'        => $regData['legal_status'],
                'summary'             => $regData['summary'],
                'official_source_url' => $regData['official_source_url'],
                'workflow_status'     => 'published',
                'visibility'          => 'public',
                'created_by'          => 14,
                'published_by'        => 14,
                'published_at'        => $regData['official_date'] . ' 08:00:00',
                'updated_at'          => $now,
            ];

            if ($existing) {
                $db->table('regulations')->where('id', $existing['id'])->update($regPayload);
                $regId = (int) $existing['id'];
            } else {
                $regPayload['created_at'] = $now;
                $db->table('regulations')->insert($regPayload);
                $regId = (int) $db->insertID();
            }
            $regIdMap[$regData['slug']] = $regId;

            // Insert/update regulation_versions
            $currentVer = $db->table('regulation_versions')->where('regulation_id', $regId)->where('is_current', 1)->get()->getRowArray();
            if ($currentVer) {
                $verId = (int) $currentVer['id'];
                $db->table('regulation_versions')->where('id', $verId)->update([
                    'file_id'   => $fileAssetId,
                    'changelog' => $regData['versions'][0]['changelog'] ?? 'Rilis resmi.',
                ]);
            } else {
                $db->table('regulation_versions')->insert([
                    'regulation_id'  => $regId,
                    'version_number' => 1,
                    'file_id'        => $fileAssetId,
                    'changelog'      => $regData['versions'][0]['changelog'] ?? 'Rilis resmi pertama.',
                    'is_current'     => 1,
                    'uploaded_by'    => 14,
                    'created_at'     => $now,
                ]);
                $verId = (int) $db->insertID();
            }

            // Link current_version_id to regulations
            $db->table('regulations')->where('id', $regId)->update(['current_version_id' => $verId]);

            // Sync procurement stages pivot
            $db->table('regulation_procurement_stages')->where('regulation_id', $regId)->delete();
            foreach ($regData['stages'] as $stName) {
                if (isset($stageIdMap[$stName])) {
                    $db->table('regulation_procurement_stages')->insert([
                        'regulation_id'        => $regId,
                        'procurement_stage_id' => $stageIdMap[$stName],
                    ]);
                }
            }

            // Seed sample downloads
            $dlCount = rand(45, 350);
            $existDl = $db->table('regulation_downloads')->where('regulation_id', $regId)->countAllResults();
            if ($existDl < 10) {
                for ($d = 0; $d < min(25, $dlCount); $d++) {
                    $db->table('regulation_downloads')->insert([
                        'regulation_id' => $regId,
                        'version_id'    => $verId,
                        'file_id'       => $fileAssetId,
                        'access_type'   => 'public_download',
                        'created_at'    => date('Y-m-d H:i:s', strtotime("-" . rand(1, 90) . " days")),
                    ]);
                }
            }
        }
        CLI::write('  [OK] Regulations synced: ' . count($regIdMap), 'green');

        // 3. Inter-regulation Relations
        $relations = [
            // Perpres 12/2021 amends Perpres 16/2018
            [
                'src'   => 'perpres-12-tahun-2021',
                'tgt'   => 'perpres-16-tahun-2018',
                'type'  => 'amends',
                'notes' => 'Mengubah ketentuan nilai paket usaha kecil hingga Rp15M, kewajiban 40% belanja produk lokal, dan peran UKPBJ.',
            ],
            // Perpres 16/2018 revokes Perpres 54/2010
            [
                'src'   => 'perpres-16-tahun-2018',
                'tgt'   => 'perpres-54-tahun-2010',
                'type'  => 'revokes',
                'notes' => 'Mencabut dan menyatakan tidak berlaku Perpres No. 54 Tahun 2010 beserta seluruh perubahannya.',
            ],
            // PerLKPP 12/2021 implements Perpres 12/2021
            [
                'src'   => 'peraturan-lkpp-12-tahun-2021',
                'tgt'   => 'perpres-12-tahun-2021',
                'type'  => 'implements',
                'notes' => 'Melaksanakan ketentuan Pasal 91 Perpres 16/2018 jo Perpres 12/2021 mengenai pedoman pelaksanaan pengadaan melalui penyedia.',
            ],
            // PerLKPP 9/2021 implements Perpres 12/2021
            [
                'src'   => 'peraturan-lkpp-9-tahun-2021',
                'tgt'   => 'perpres-12-tahun-2021',
                'type'  => 'implements',
                'notes' => 'Melaksanakan ketentuan pengadaan elektronik melalui Toko Daring dan Katalog Elektronik Lokal/Nasional.',
            ],
            // Pergub Lampung 35/2022 references Perpres 12/2021
            [
                'src'   => 'pergub-lampung-35-tahun-2022',
                'tgt'   => 'perpres-12-tahun-2021',
                'type'  => 'references',
                'notes' => 'Mendasarkan tata kelola pengadaan barang/jasa daerah pada ketentuan nasional Perpres 12/2021.',
            ],
            // SE LKPP 3/2023 references PerLKPP 12/2021
            [
                'src'   => 'se-lkpp-3-tahun-2023',
                'tgt'   => 'peraturan-lkpp-12-tahun-2021',
                'type'  => 'references',
                'notes' => 'Memberikan panduan teknis operasional penyusunan HPS melengkapi PerLKPP No. 12 Tahun 2021.',
            ],
        ];

        foreach ($relations as $rel) {
            $srcId = $regIdMap[$rel['src']] ?? null;
            $tgtId = $regIdMap[$rel['tgt']] ?? null;
            if ($srcId && $tgtId) {
                $exists = $db->table('regulation_relations')
                    ->where('source_regulation_id', $srcId)
                    ->where('target_regulation_id', $tgtId)
                    ->where('relation_type', $rel['type'])
                    ->get()->getRowArray();
                if (! $exists) {
                    $db->table('regulation_relations')->insert([
                        'source_regulation_id' => $srcId,
                        'target_regulation_id' => $tgtId,
                        'relation_type'        => $rel['type'],
                        'notes'                => $rel['notes'],
                        'created_by'           => 14,
                        'created_at'           => $now,
                    ]);
                }
            }
        }
        CLI::write('  [OK] Inter-regulation relations synced: ' . count($relations), 'green');

        CLI::newLine();
        CLI::write('==================================================================', 'green');
        CLI::write('REGULASI SEEDING COMPLETED SUCCESSFULLY!', 'green');
        CLI::write('==================================================================', 'green');

        return 0;
    }
}

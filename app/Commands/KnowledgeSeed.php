<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class KnowledgeSeed extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'knowledge:seed';
    protected $description = 'Seed authentic and comprehensive PBJ knowledge data for Bunga Rampai.';
    protected $usage       = 'knowledge:seed';

    public function run(array $params): int
    {
        $db = db_connect();
        CLI::write('Seeding Bunga Rampai data...', 'yellow');

        $adminId = 14;
        $now = date('Y-m-d H:i:s');

        // Ensure directory exists
        $uploadDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'knowledge';
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // -------------------------------------------------------------
        // 1. TEMPLATES (with real downloadable dummy files)
        // -------------------------------------------------------------
        $templatesData = [
            [
                'title'       => 'Template Kerangka Acuan Kerja (KAK) Pengadaan Barang & Jasa Lainnya',
                'slug'        => 'template-kak-pengadaan-barang-jasa-lainnya',
                'summary'     => 'Standar format KAK/TOR komprehensif memuat latar belakang, ruang lingkup, kualifikasi penyedia, jadwal pelaksanaan, dan spesifikasi teknis berbasis kinerja.',
                'content'     => "Dokumen Kerangka Acuan Kerja (KAK) ini dirancang sesuai ketentuan Perpres No. 12 Tahun 2021 dan panduan LKPP terkini.\n\nStruktur dokumen KAK mencakup:\n1. Latar Belakang dan Dasar Hukum Pengadaan\n2. Maksud dan Tujuan Kegiatan\n3. Target/Sasaran yang Ingin Dicapai\n4. Ruang Lingkup dan Lokasi Pekerjaan\n5. Sumber Pendanaan dan Perkiraan Total Biaya\n6. Jangka Waktu Pelaksanaan dan Jadwal Tahapan\n7. Tenaga Ahli dan Personel Kunci yang Dibutuhkan\n8. Spesifikasi Teknis Rinci dan Standar Kualitas\n9. Indikator Keberhasilan dan Format Laporan Hasil Pekerjaan.\n\nTemplate ini dapat disesuaikan untuk kebutuhan OPD di lingkungan Pemerintah Daerah.",
                'filename'    => 'Template_KAK_Pengadaan_Barang_Jasa_Lampung.docx',
                'extension'   => 'docx',
                'mime_type'   => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'dummy_text'  => 'DOKUMEN KERANGKA ACUAN KERJA (KAK) PENGADAAN BARANG/JASA PEMERINTAH PROVINSI LAMPUNG - STANDAR RESMI BIRO PBJ SETDA PROVINSI LAMPUNG.',
                'featured'    => 1,
            ],
            [
                'title'       => 'Template Kertas Kerja Perhitungan HPS & Analisis Harga Pasar',
                'slug'        => 'template-kertas-kerja-hps-analisis-pasar',
                'summary'     => 'Format lembar kerja spreadsheet untuk dokumentasi survei harga pasar, kalkulasi komponen biaya, inflasi, keuntungan wajar, dan pajak dalam penyusunan HPS.',
                'content'     => "Kertas Kerja Penyusunan Harga Perkiraan Sendiri (HPS) membantu PPK dan Tim Teknis mendokumentasikan bukti survei harga pasar secara akuntabel dan transparan guna mencegah temuan audit.\n\nFitur Template:\n1. Rekapitulasi perbandingan harga minimal 3 distributor/agen resmi\n2. Perhitungan biaya pengiriman, instalasi, dan overhead\n3. Pemisahan komponen PPN 11% dan keuntungan penyedia yang wajar\n4. Lembar verifikasi tanda tangan PPK dan Pejabat Pengadaan\n5. Referensi harga historis kontrak sejenis tahun sebelumnya.",
                'filename'    => 'Kertas_Kerja_HPS_dan_Analisis_Pasar.xlsx',
                'extension'   => 'xlsx',
                'mime_type'   => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'dummy_text'  => 'KERTAS KERJA PERHITUNGAN HARGA PERKIRAAN SENDIRI (HPS) - STANDAR BIRO PBJ PEMPROV LAMPUNG.',
                'featured'    => 1,
            ],
            [
                'title'       => 'Standar Surat Perintah Kerja (SPK) & Kontrak Pengadaan Konstruksi',
                'slug'        => 'standar-surat-perintah-kerja-kontrak-konstruksi',
                'summary'     => 'Klausul baku kontrak kerja konstruksi lengkap dengan syarat umum, syarat khusus, mekanisme pembayaran termin, denda keterlambatan, dan klausul arbitrase.',
                'content'     => "Template Kontrak Konstruksi standar yang mengadopsi klausul baku LPSE dan LKPP dengan penyesuaian regulasi daerah Provinsi Lampung.\n\nMuatan Kontrak:\n1. Bagian Pembuka dan Para Pihak yang Bersepakat\n2. Surat Perjanjian Utama dan Lingkup Pekerjaan Fisik\n3. Syarat-Syarat Umum Kontrak (SSUK)\n4. Syarat-Syarat Khusus Kontrak (SSKK) termasuk jaminan pelaksanaan\n5. Klausul Sanksi Deviasi Kritis dan Pemutusan Kontrak Sepihak\n6. Tata Cara Penyelesaian Sengketa Konstruksi.",
                'filename'    => 'Standar_SPK_Kontrak_Konstruksi_Lampung.docx',
                'extension'   => 'docx',
                'mime_type'   => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'dummy_text'  => 'SURAT PERINTAH KERJA (SPK) DAN SURAT PERJANJIAN KONSTRUKSI STANDAR BIRO PENGADAAN BARANG DAN JASA.',
                'featured'    => 0,
            ],
            [
                'title'       => 'Format Berita Acara Serah Terima (BAST) & Pemeriksaan Hasil Pekerjaan',
                'slug'        => 'format-berita-acara-serah-terima-bast-pekerjaan',
                'summary'     => 'Formulir resmi berita acara pemeriksaan fisik, uji fungsi, dan serah terima hasil pekerjaan dari penyedia ke PPK sebagai syarat mutlak pencairan anggaran.',
                'content'     => "Berita Acara Serah Terima (BAST) merupakan dokumen pertanggungjawaban final administrasi pengadaan yang membuktikan bahwa penyedia telah menyelesaikan seluruh kewajiban prestasinya.\n\nPaket Berkas BAST Meliputi:\n- Berita Acara Uji Fungsi dan Penerimaan Barang\n- Laporan Hasil Pemeriksaan Tim Teknis / Pejabat Pengadaan\n- Lembar Ceklis Kelengkapan Dokumen Garansi dan Manual\n- Berita Acara Pembayaran dan Pelepasan Jaminan.",
                'filename'    => 'Format_BAST_dan_Pemeriksaan_Pekerjaan.docx',
                'extension'   => 'docx',
                'mime_type'   => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'dummy_text'  => 'BERITA ACARA SERAH TERIMA HASIL PEKERJAAN (BAST) PEMERINTAH PROVINSI LAMPUNG.',
                'featured'    => 1,
            ],
            [
                'title'       => 'Template Form Evaluasi Kualifikasi & Penawaran Pokja Pemilihan',
                'slug'        => 'template-form-evaluasi-kualifikasi-penawaran-pokja',
                'summary'     => 'Lembar kerja sistematis untuk Pokja Pemilihan dalam mengevaluasi administrasi, teknis, harga, dan kualifikasi penyedia tender secara objektif.',
                'content'     => "Form evaluasi ini memandu anggota Pokja Pemilihan memeriksa dokumen penawaran tender sistem gugur dan nilai.\n\nUnsur Evaluasi:\n- Validasi NIB, Izin Usaha KBLI, dan SBU\n- Penilaian Sertifikat Badan Usaha & SKA/SKT Tenaga Kerja\n- Verifikasi Dokumen Dukungan Pabrikan dan Sertifikat Uji Lab\n- Rekapitulasi Berita Acara Klarifikasi Harga Timpang.",
                'filename'    => 'Form_Evaluasi_Kualifikasi_Pokja_Pemilihan.xlsx',
                'extension'   => 'xlsx',
                'mime_type'   => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'dummy_text'  => 'FORM EVALUASI KUALIFIKASI DAN PENAWARAN POKJA PEMILIHAN BIRO PBJ.',
                'featured'    => 0,
            ],
            [
                'title'       => 'Template Surat Peringatan & Berita Acara Show Cause Meeting (SCM)',
                'slug'        => 'template-surat-peringatan-berita-acara-scm',
                'summary'     => 'Panduan surat tegangan resmi SCM Tingkat I, II, dan III untuk penanganan deviasi negatif pada pelaksanaan kontrak kritis konstruksi.',
                'content'     => "Dokumen standar penanganan kontrak kritis sesuai Perpres Pengadaan.\n\nIsi Dokumen:\n1. Format Surat Panggilan SCM I, II, dan III\n2. Formulir Berita Acara Kesepakatan Uji Coba (Test Case)\n3. Format Rekomendasi Pemutusan Kontrak dan Pencairan Jaminan Pelaksanaan.",
                'filename'    => 'Template_Surat_Peringatan_dan_BA_SCM.docx',
                'extension'   => 'docx',
                'mime_type'   => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'dummy_text'  => 'STANDAR OPERASIONAL PROSEDUR DAN FORMAT SHOW CAUSE MEETING (SCM) KONTRAK KRITIS.',
                'featured'    => 0,
            ],
            [
                'title'       => 'Format Pakta Integritas PPK, Pokja, dan Penyedia Barang/Jasa',
                'slug'        => 'format-pakta-integritas-pengadaan-pbj',
                'summary'     => 'Komitmen bersama pencegahan gratifikasi, suap, benturan kepentingan, dan kolusi dalam seluruh tahapan PBJ Pemerintah Provinsi Lampung.',
                'content'     => "Pakta Integritas wajib ditandatangani oleh seluruh pelaku pengadaan sebelum dimulainya proses pemilihan penyedia.\n\nMemuat butir-butir pencegahan korupsi, kepatuhan kode etik, dan sanksi pencabutan sertifikat pengadaan jika terbukti melanggar.",
                'filename'    => 'Pakta_Integritas_Pengadaan_PBJ_Lampung.docx',
                'extension'   => 'docx',
                'mime_type'   => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'dummy_text'  => 'SURAT PERNYATAAN PAKTA INTEGRITAS PENGADAAN BARANG DAN JASA PEMERINTAH DAERAH.',
                'featured'    => 0,
            ],
            [
                'title'       => 'Checklist Dokumen Kelengkapan Pengajuan Pencairan Dana PBJ',
                'slug'        => 'checklist-dokumen-pencairan-dana-pbj',
                'summary'     => 'Daftar periksa verifikasi dokumen keuangan dan administrasi pengadaan bagi PPK, PPTK, dan Bendahara Pengeluaran sebelum menerbitkan SPP-LS.',
                'content'     => "Lembar kendali dokumen untuk memastikan kelengkapan berkas tagihan SPM/SP2D aman dari potensi audit BPK.\n\nMemeriksa: Surat Permohonan Pembayaran, BAST, Faktur Pajak e-Faktur, Bukti Setor Pajak, Laporan Pengawasan Konsultan, dan Jaminan Pemeliharaan.",
                'filename'    => 'Checklist_Dokumen_Pencairan_Dana_PBJ.pdf',
                'extension'   => 'pdf',
                'mime_type'   => 'application/pdf',
                'dummy_text'  => '%PDF-1.4 CHECKLIST KELENGKAPAN PENGAJUAN PENCAIRAN DANA PENGADAAN PEMPROV LAMPUNG.',
                'featured'    => 0,
            ],
        ];

        foreach ($templatesData as $tpl) {
            $storedName = time() . '_' . md5($tpl['slug']) . '.' . $tpl['extension'];
            $relPath    = 'uploads/knowledge/' . $storedName;
            $fullPath   = $uploadDir . DIRECTORY_SEPARATOR . $storedName;

            file_put_contents($fullPath, $tpl['dummy_text']);

            // Insert file_assets
            $db->table('file_assets')->insert([
                'disk'             => 'local',
                'path'             => $relPath,
                'original_name'    => $tpl['filename'],
                'stored_name'      => $storedName,
                'extension'        => $tpl['extension'],
                'mime_type'        => $tpl['mime_type'],
                'size_bytes'       => filesize($fullPath),
                'visibility'       => 'public',
                'lifecycle_status' => 'available',
                'resource_type'    => 'knowledge',
                'uploaded_by'      => $adminId,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $fileId = $db->insertID();

            // Insert or update knowledge_articles
            $existing = $db->table('knowledge_articles')->where('slug', $tpl['slug'])->get()->getRowArray();
            if ($existing) {
                $db->table('knowledge_articles')->where('id', $existing['id'])->update([
                    'title'           => $tpl['title'],
                    'summary'         => $tpl['summary'],
                    'content'         => $tpl['content'],
                    'type'            => 'template',
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'featured'        => $tpl['featured'],
                    'cover_file_id'   => $fileId,
                    'author_id'       => $adminId,
                    'published_by'    => $adminId,
                    'published_at'    => $now,
                    'deleted_at'      => null,
                    'updated_at'      => $now,
                ]);
            } else {
                $db->table('knowledge_articles')->insert([
                    'type'            => 'template',
                    'title'           => $tpl['title'],
                    'slug'            => $tpl['slug'],
                    'summary'         => $tpl['summary'],
                    'content'         => $tpl['content'],
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'featured'        => $tpl['featured'],
                    'cover_file_id'   => $fileId,
                    'author_id'       => $adminId,
                    'published_by'    => $adminId,
                    'published_at'    => $now,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
            }
        }
        CLI::write('  [OK] Templates seeded with real physical downloads', 'green');

        // -------------------------------------------------------------
        // 2. ARTICLES (Wawasan & Praktik Baik PBJ)
        // -------------------------------------------------------------
        $articlesData = [
            [
                'title'    => 'Panduan Komprehensif Penyusunan HPS Berdasarkan Perpres 12/2021',
                'slug'     => 'panduan-komprehensif-penyusunan-hps-perpres-12-2021',
                'summary'  => 'Langkah-langkah strategis PPK dalam mengumpulkan data survei pasar, memperhitungkan overhead, dan menyusun HPS yang akuntabel bebas dari mark-up.',
                'content'  => "Harga Perkiraan Sendiri (HPS) merupakan instrumen krusial yang berfungsi sebagai alat penilai kewajaran harga penawaran dan batas tertinggi penawaran yang sah pada tender pengadaan pemerintah.\n\nDalam Perpres No. 12 Tahun 2021, ditekankan bahwa penyusunan HPS harus didasarkan pada keahlian dan data yang dapat dipertanggungjawabkan, meliputi:\n1. Harga pasar setempat menjelang pelaksanaan pengadaan\n2. Informasi biaya satuan yang diterbitkan oleh instansi teknis yang berwenang\n3. Biaya kontrak sejenis terdahulu yang sedang berjalan atau telah selesai\n4. Bukti pembelian/faktur riil dari distributor resmi.\n\nPPK tidak diperkenankan memperhitungkan keuntungan dan biaya tidak terduga pada pekerjaan yang sifatnya pengadaan barang murni di luar keuntungan yang telah terkandung dalam harga pasar.",
                'featured' => 1,
            ],
            [
                'title'    => 'Optimalisasi Pengadaan Barang/Jasa Melalui e-Katalog Versi 6',
                'slug'     => 'optimalisasi-pengadaan-melalui-e-katalog-versi-6',
                'summary'  => 'Memahami fitur baru e-Katalog V6: mekanisme negosiasi daring bertahap, mini kompetisi cepat, dan integrasi pembayaran Kartu Kredit Pemerintah Daerah (KKPD).',
                'content'  => "Peluncuran e-Katalog Versi 6 oleh LKPP membawa transformasi digital yang signifikan bagi seluruh Pejabat Pengadaan dan PPK di Provinsi Lampung.\n\nFitur-fitur unggulan meliputi:\n- Antarmuka pencarian cerdas berbasis AI untuk membandingkan spesifikasi produk sejenis secara instan.\n- Fitur negosiasi bertahap dengan pencatatan histori penawaran harga yang transparan.\n- Mini kompetisi otomatis apabila terdapat lebih dari dua penyedia dengan komoditas yang identik.\n- Integrasi langsung dengan modul SPAN dan Cash Management System Pemerintah Daerah.",
                'featured' => 1,
            ],
            [
                'title'    => 'Strategi Mitigasi Risiko Fraud pada Tahap Perencanaan Pengadaan',
                'slug'     => 'strategi-mitigasi-risiko-fraud-tahap-perencanaan',
                'summary'  => 'Cara mendeteksi dan mencegah pemecahan paket pekerjaan (splittting), spesifikasi yang mengarah pada merek tertentu, serta penggelembungan anggaran.',
                'content'  => "Tahap perencanaan merupakan titik paling rawan terjadinya penyimpangan PBJ. Seringkali pengadaan bermasalah bukan karena proses tender yang salah, melainkan karena identifikasi kebutuhan yang tidak objektif sejak awal.\n\nLangkah Mitigasi Risiko:\n1. Verifikasi kelayakan dokumen RUP sebelum diumumkan ke SiRUP\n2. Melarang keras pemecahan paket pekerjaan untuk menghindari tender\n3. Memastikan spesifikasi teknis menyebutkan fungsi dan kinerja, bukan merek tertutup tanpa justifikasi teknis\n4. Melibatkan APIP Inspektorat dalam proses reviu perencanaan pengadaan strategis.",
                'featured' => 1,
            ],
            [
                'title'    => 'Peran PPK dalam Pengendalian Kontrak Kritis dan Show Cause Meeting',
                'slug'     => 'peran-ppk-dalam-pengendalian-kontrak-kritis-scm',
                'summary'  => 'Panduan operasional PPK ketika kontraktor mengalami deviasi fisik minus: prosedur SCM 1, 2, 3 hingga pemutusan kontrak sepihak dan pencairan jaminan.',
                'content'  => "Kontrak kritis terjadi jika realisasi fisik pekerjaan terlambat dibandingkan jadwal rencana pelaksanaan dengan deviasi tertentu.\n\nBerdasarkan standar kontrak PBJ:\n- Periode I (0-70%): deviasi minus > 10%\n- Periode II (70-100%): deviasi minus > 5%\n\nKetika kontrak dinyatakan kritis, PPK wajib menerbitkan Surat Peringatan dan menyelenggarakan Show Cause Meeting (SCM) untuk menyepakati Uji Coba (Test Case). Jika penyedia gagal pada SCM III, PPK harus mengambil langkah tegas berupa pemutusan kontrak dan pencairan jaminan pelaksanaan ke kas daerah.",
                'featured' => 0,
            ],
            [
                'title'    => 'Peningkatan Penggunaan Produk Dalam Negeri (P3DN) di Lingkungan Pemda',
                'slug'     => 'peningkatan-penggunaan-produk-dalam-negeri-p3dn-pemda',
                'summary'  => 'Ketentuan kewajiban alokasi belanja PDN minimal 40%, preferensi harga TKDN, dan verifikasi sertifikat tanda sah Kemenperin pada sistem SPSE.',
                'content'  => "Pemerintah Daerah Provinsi Lampung berkomitmen mengalokasikan anggaran belanja pengadaan untuk produk dalam negeri dan usaha mikro kecil koperatif (UMK-Kop).\n\nKetentuan Utama:\n- Wajib membeli produk dalam negeri jika terdapat produk dengan nilai penjumlahan TKDN dan Bobot Manfaat Perusahaan (BMP) minimal 40%.\n- Pemberian Preferensi Harga maksimal 25% bagi penyedia yang memiliki sertifikat TKDN di atas 25% pada tender barang/jasa bernilai di atas 1 miliar rupiah.",
                'featured' => 0,
            ],
            [
                'title'    => 'Tips Sukses Menghadapi Audit Pengadaan oleh APIP dan BPK',
                'slug'     => 'tips-sukses-menghadapi-audit-pengadaan-apip-bpk',
                'summary'  => 'Ceklis kelengkapan arsip SPJ pengadaan, dokumentasi pembuktian kualifikasi, catatan negosiasi, dan justifikasi teknis perubahan kontrak (Addendum).',
                'content'  => "Menghadapi audit kepatuhan PBJ memerlukan ketertiban arsip sejak awal kegiatan dimulai.\n\nDokumen vital yang selalu diuji auditor:\n1. Bukti riil survei harga pasar saat menyusun HPS\n2. Notula dan dokumentasi foto saat pembuktian kualifikasi fisik penyedia\n3. Justifikasi teknis tim ahli saat menerbitkan Addendum Tambah Kurang (CCO)\n4. Laporan pengawasan berkala dan hasil pengujian mutu laboratorium independen.",
                'featured' => 0,
            ],
            [
                'title'    => 'Tata Cara Tender Cepat untuk Pekerjaan Standar dengan Kualifikasi SIKaP',
                'slug'     => 'tata-cara-tender-cepat-sikap-lpse',
                'summary'  => 'Mekanisme tender cepat dalam waktu 3 hari kerja memanfaatkan Sistem Informasi Kinerja Penyedia (SIKaP) tanpa perlu evaluasi teknis manual.',
                'content'  => "Tender Cepat adalah solusi pengadaan hemat waktu untuk komoditas barang/konstruksi/jasa lainnya yang standar dan spesifikasinya telah baku di pasaran.\n\nPrinsip Tender Cepat:\n- Peserta telah terverifikasi kinerja dan izin usahanya di SIKaP LKPP\n- Penilaian murni berdasarkan urutan harga penawaran terendah yang masuk dalam batas waktu yang ditentukan\n- Tidak ada sanggahan banding pada metode tender cepat.",
                'featured' => 0,
            ],
            [
                'title'    => 'Penerapan Best Value for Money dalam Pengadaan Publik Daerah',
                'slug'     => 'penerapan-best-value-for-money-pengadaan-publik',
                'summary'  => 'Bukan sekadar mencari harga terendah, tetapi menyeimbangkan antara kualitas, keandalan siklus hidup aset, dan dampak kemanfaatan sosial masyarakat.',
                'content'  => "Paradigma pengadaan modern beralih dari 'least price' (harga paling murah) menuju 'best value for money'. Pengadaan yang murah di awal namun sering rusak justru menimbulkan beban biaya pemeliharaan yang memboroskan keuangan daerah.",
                'featured' => 0,
            ],
        ];

        foreach ($articlesData as $art) {
            $existing = $db->table('knowledge_articles')->where('slug', $art['slug'])->get()->getRowArray();
            if ($existing) {
                $db->table('knowledge_articles')->where('id', $existing['id'])->update([
                    'title'           => $art['title'],
                    'summary'         => $art['summary'],
                    'content'         => $art['content'],
                    'type'            => 'article',
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'featured'        => $art['featured'],
                    'author_id'       => $adminId,
                    'published_by'    => $adminId,
                    'published_at'    => $now,
                    'deleted_at'      => null,
                    'updated_at'      => $now,
                ]);
            } else {
                $db->table('knowledge_articles')->insert([
                    'type'            => 'article',
                    'title'           => $art['title'],
                    'slug'            => $art['slug'],
                    'summary'         => $art['summary'],
                    'content'         => $art['content'],
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'featured'        => $art['featured'],
                    'author_id'       => $adminId,
                    'published_by'    => $adminId,
                    'published_at'    => $now,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
            }
        }
        CLI::write('  [OK] Articles seeded successfully', 'green');

        // -------------------------------------------------------------
        // 3. CASE STUDIES (Studi Kasus Lapangan)
        // -------------------------------------------------------------
        $casesData = [
            [
                'title'    => 'Penyelesaian Sanggahan Banding pada Pengadaan Fisik Gedung RSUD',
                'slug'     => 'penyelesaian-sanggahan-banding-pengadaan-gedung-rsud',
                'summary'  => 'Kajian studi kasus penanganan gugatan sanggahan peserta tender terkait dugaan kesamaan dokumen IP Address dan surat dukungan bank.',
                'content'  => "Pada paket pembangunan gedung rawat inap terpadu RSUD senilai Rp 28 Miliar, terjadi sanggahan dari peserta peringkat kedua terhadap penetapan pemenang.\n\nKronologi Kasus:\nSanggahan mempersoalkan kesamaan dokumen penawaran teknis dan indikasi persaingan usaha tidak sehat antar-rekanan.\n\nLangkah Analisis Pokja & KPA:\n1. Audit forensik berkas penawaran di LPSE menunjukkan submit penawaran dilakukan dari jaringan berbeda.\n2. Klarifikasi langsung ke pihak bank penerbit jaminan penawaran mengonfirmasi keaslian warkat.\n3. Pokja menjawab sanggahan disertai bukti verifikasi faktual sehingga tender dinyatakan sah dan proyek dapat berjalan tepat waktu.",
                'featured' => 1,
            ],
            [
                'title'    => 'Penanganan Kontrak Kritis Deviasi Minus 14% Pembangunan Jembatan',
                'slug'     => 'penanganan-kontrak-kritis-pembangunan-jembatan',
                'summary'  => 'Penerapan langkah darurat SCM dan mobilisasi alat berat tambahan untuk mengejar keterlambatan pekerjaan sebelum pergantian tahun anggaran.',
                'content'  => "Proyek pembangunan jembatan penghubung antar-kabupaten mengalami deviasi fisik minus 14,2% pada minggu ke-18 akibat kelangkaan material tiang pancang dan cuaca ekstrem.\n\nTindakan PPK:\n- Menerbitkan Surat Peringatan I dan menggelar SCM Tingkat I bersama Konsultan Pengawas dan Direktur Rekanan.\n- Membuat kesepakatan uji coba (Test Case) selama 14 hari dengan target percepatan penambahan shift kerja malam dan penambahan 2 unit crane.\n- Rekanan berhasil memenuhi target uji coba sehingga kontrak tidak diputus dan jembatan rampung sebelum batas akhir kontrak.",
                'featured' => 1,
            ],
            [
                'title'    => 'Evaluasi Kualifikasi SIPP dan Klarifikasi Kewajaran Harga Pengadaan IT',
                'slug'     => 'evaluasi-kualifikasi-sipp-klarifikasi-harga-pengadaan-it',
                'summary'  => 'Prosedur klarifikasi harga penawaran di bawah 80% HPS untuk menjaga kualitas perangkat keras server data center pemerintah daerah.',
                'content'  => "Pada pengadaan perangkat server data center terintegrasi, peserta menawarkan harga 76% dari HPS.\n\nSesuai regulasi LKPP, jika harga penawaran di bawah 80% HPS, Pokja wajib melakukan klarifikasi kewajaran harga.\n\nHasil Klarifikasi:\nPeserta mampu membuktikan Surat Dukungan Resmi (MAF) langsung dari manufaktur principal global dengan harga diskon khusus sektor publik, serta menyertakan rincian biaya garansi resmi selama 5 tahun di lokasi (on-site). Penawaran dinyatakan wajar dan diterima.",
                'featured' => 0,
            ],
            [
                'title'    => 'Manajemen Pengadaan Tanggap Darurat Bencana Banjir Lampung Selatan',
                'slug'     => 'manajemen-pengadaan-tanggap-darurat-bencana-banjir',
                'summary'  => 'Praktik penunjukan langsung penyedia logistik dan tanggul darurat dalam status tanggap darurat bencana sesuai ketentuan LKPP No. 13/2018.',
                'content'  => "Kajian penerapan pengadaan darurat ketika terjadi bencana banjir bandang. PPK menunjuk penyedia terdekat tanpa tender untuk segera mengirimkan bronjong kawat dan bahan pangan dengan audit pos-kegiatan oleh BPKP.",
                'featured' => 0,
            ],
            [
                'title'    => 'Negosiasi Harga Berulang pada e-Purchasing Obat & Alkes RS Daerah',
                'slug'     => 'negosiasi-harga-e-purchasing-obat-alkes-rs',
                'summary'  => 'Strategi optimalisasi negosiasi volume pembelian besar (bulk buying) untuk memperoleh diskon tambahan 8% di atas harga tayang e-Katalog.',
                'content'  => "Pejabat Pengadaan rumah sakit daerah mengonsolidasikan kebutuhan obat-obatan selama satu tahun anggaran dan mengajukan negosiasi harga volume pada e-Katalog, menghemat anggaran daerah sebesar Rp 420 Juta.",
                'featured' => 0,
            ],
            [
                'title'    => 'Mitigasi Sengketa Pembayaran Uang Muka Pengadaan Kendaraan Khusus',
                'slug'     => 'mitigasi-sengketa-uang-muka-kendaraan-khusus',
                'summary'  => 'Penatausahaan jaminan uang muka dari asuransi BUMN terpercaya guna melindungi kas daerah dari risiko wanprestasi penyedia.',
                'content'  => "Analisis perlindungan keuangan daerah melalui verifikasi keaslian polis jaminan uang muka (Advance Payment Bond) sebelum transfer dana dilakukan.",
                'featured' => 0,
            ],
        ];

        foreach ($casesData as $cs) {
            $existing = $db->table('knowledge_articles')->where('slug', $cs['slug'])->get()->getRowArray();
            if ($existing) {
                $db->table('knowledge_articles')->where('id', $existing['id'])->update([
                    'title'           => $cs['title'],
                    'summary'         => $cs['summary'],
                    'content'         => $cs['content'],
                    'type'            => 'case_study',
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'featured'        => $cs['featured'],
                    'author_id'       => $adminId,
                    'published_by'    => $adminId,
                    'published_at'    => $now,
                    'deleted_at'      => null,
                    'updated_at'      => $now,
                ]);
            } else {
                $db->table('knowledge_articles')->insert([
                    'type'            => 'case_study',
                    'title'           => $cs['title'],
                    'slug'            => $cs['slug'],
                    'summary'         => $cs['summary'],
                    'content'         => $cs['content'],
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'featured'        => $cs['featured'],
                    'author_id'       => $adminId,
                    'published_by'    => $adminId,
                    'published_at'    => $now,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
            }
        }
        CLI::write('  [OK] Case studies seeded successfully', 'green');

        // -------------------------------------------------------------
        // 4. FAQS (Tanya Jawab PBJ)
        // -------------------------------------------------------------
        $faqsData = [
            [
                'title'   => 'Apa perbedaan mendasar antara HPS (Harga Perkiraan Sendiri) dan Pagu Anggaran?',
                'slug'    => 'perbedaan-hps-dan-pagu-anggaran',
                'summary' => 'Pagu anggaran adalah alokasi dana maksimal dalam DPA, sedangkan HPS adalah estimasi biaya wajar pasar yang dihitung oleh PPK menjelang pemilihan penyedia.',
                'content' => "Pagu Anggaran adalah batas tertinggi alokasi anggaran yang tercantum dalam Dokumen Pelaksanaan Anggaran (DPA) OPD. Sedangkan HPS disusun oleh PPK berdasarkan hasil survei harga pasar terkini, biaya pengiriman, dan pajak. Nilai HPS tidak boleh melebihi Pagu Anggaran, dan HPS bersifat terbuka (tidak rahasia) kecuali rincian harga satuannya.",
            ],
            [
                'title'   => 'Kapan Pejabat Pembuat Komitmen (PPK) wajib menggelar Show Cause Meeting (SCM)?',
                'slug'    => 'kapan-ppk-wajib-menggelar-scm',
                'summary' => 'SCM wajib diadakan saat pekerjaan konstruksi mengalami deviasi fisik minus melebihi ambang batas kontrak kritis.',
                'content' => "SCM diselenggarakan jika terjadi deviasi keterlambatan fisik pekerjaan konstruksi: (1) Periode I (0-70% masa pelaksanaan) deviasi fisik minus > 10%; (2) Periode II (70-100% masa pelaksanaan) deviasi fisik minus > 5%. SCM bertujuan memberi kesempatan penyedia menyepakati target uji coba perbaikan (test case).",
            ],
            [
                'title'   => 'Bagaimana ketentuan pemberian kesempatan menyelesaikan pekerjaan melampaui tahun anggaran?',
                'slug'    => 'ketentuan-pemberian-kesempatan-melewati-tahun-anggaran',
                'summary' => 'Dapat diberikan maksimal 50 hari kalender dengan denda keterlambatan 1 permil per hari jika PPK meyakini penyedia mampu menyelesaikannya.',
                'content' => "Berdasarkan Perpres No. 12 Tahun 2021 Pasal 56, PPK dapat memberikan kesempatan kepada penyedia untuk menyelesaikan pekerjaan apabila dinilai mampu menyelesaikan sisa pekerjaan. Pemberian kesempatan dapat melampaui tahun anggaran (maksimal 50 hari kalender) dengan dikenakan sanksi denda 1/1000 (satu permil) per hari dari sisa nilai kontrak atau nilai kontrak yang belum diselesaikan.",
            ],
            [
                'title'   => 'Bolehkah Pokja menggugurkan peserta tender hanya karena format surat tidak persis seperti contoh?',
                'slug'    => 'apakah-boleh-menggugurkan-karena-format-surat-berbeda',
                'summary' => 'Tidak boleh, selama substansi data pokok yang dipersyaratkan tetap terpenuhi secara esensial.',
                'content' => "Pokja Pemilihan dilarang menggugurkan penawaran hanya karena kesalahan redaksional ringan atau format surat yang tidak identik dengan contoh, sepanjang substansi informasi pokok yang dipersyaratkan (seperti masa laku penawaran, jaminan, dan komitmen harga) terpenuhi secara sah dan mengikat menurut hukum.",
            ],
            [
                'title'   => 'Apa saja kriteria pengadaan yang dapat dilakukan melalui Pengadaan Langsung?',
                'slug'    => 'kriteria-pengadaan-langsung',
                'summary' => 'Pengadaan barang, jasa lainnya, dan pekerjaan konstruksi bernilai sampai dengan Rp 200 Juta, atau konsultansi sampai dengan Rp 100 Juta.',
                'content' => "Pengadaan Langsung dilakukan oleh Pejabat Pengadaan untuk: (1) Pengadaan Barang/Pekerjaan Konstruksi/Jasa Lainnya dengan nilai paling banyak Rp 200.000.000,00 (dua ratus juta rupiah); (2) Jasa Konsultansi dengan nilai paling banyak Rp 100.000.000,00 (seratus juta rupiah).",
            ],
            [
                'title'   => 'Bagaimana cara menghitung Tingkat Komponen Dalam Negeri (TKDN)?',
                'slug'    => 'cara-menghitung-tkdn',
                'summary' => 'Dihitung berdasarkan proporsi komponen bahan baku lokal, tenaga kerja WNI, dan mesin produksi dalam negeri berbanding total biaya produk.',
                'content' => "Penghitungan TKDN mengacu pada sertifikat resmi yang dikeluarkan oleh Kementerian Perindustrian. Nilai TKDN mencerminkan persentase kandungan komponen lokal dari biaya produksi. Pada tender di atas Rp 1 Miliar, penyedia dengan TKDN minimal 25% berhak memperoleh preferensi harga maksimal 25%.",
            ],
            [
                'title'   => 'Apa yang harus dilakukan Pokja jika hanya ada 1 peserta tender yang memasukkan dokumen?',
                'slug'    => 'tindakan-pokja-jika-hanya-1-peserta-tender',
                'summary' => 'Lakukan evaluasi terhadap satu peserta tersebut; jika lulus evaluasi, lanjutkan dengan negosiasi teknis dan harga.',
                'content' => "Apabila setelah batas akhir pemasukan penawaran tender hanya ada 1 (satu) peserta yang memasukkan penawaran, Pokja tetap melakukan evaluasi administrasi, teknis, dan kualifikasi. Apabila peserta tersebut memenuhi seluruh persyaratan, Pokja melanjutkan proses dengan negosiasi teknis dan harga tanpa harus mengulang tender.",
            ],
            [
                'title'   => 'Apakah penyedia yang kena sanksi Blacklist di instansi lain otomatis ditolak di Pemprov Lampung?',
                'slug'    => 'apakah-penyedia-blacklist-otomatis-ditolak',
                'summary' => 'Ya, daftar hitam yang tayang di portal Inaproc LKPP berlaku nasional di seluruh instansi pemerintah.',
                'content' => "Sanksi Daftar Hitam (Blacklist) yang telah diverifikasi dan ditayangkan secara resmi pada Portal Nasional LKPP (Inaproc) berlaku secara nasional di seluruh kementerian, lembaga, dan pemerintah daerah selama masa sanksi masih berlangsung.",
            ],
        ];

        foreach ($faqsData as $faq) {
            $existing = $db->table('knowledge_articles')->where('slug', $faq['slug'])->get()->getRowArray();
            if ($existing) {
                $db->table('knowledge_articles')->where('id', $existing['id'])->update([
                    'title'           => $faq['title'],
                    'summary'         => $faq['summary'],
                    'content'         => $faq['content'],
                    'type'            => 'faq',
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'author_id'       => $adminId,
                    'published_by'    => $adminId,
                    'published_at'    => $now,
                    'deleted_at'      => null,
                    'updated_at'      => $now,
                ]);
            } else {
                $db->table('knowledge_articles')->insert([
                    'type'            => 'faq',
                    'title'           => $faq['title'],
                    'slug'            => $faq['slug'],
                    'summary'         => $faq['summary'],
                    'content'         => $faq['content'],
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'author_id'       => $adminId,
                    'published_by'    => $adminId,
                    'published_at'    => $now,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
            }
        }
        CLI::write('  [OK] FAQs seeded successfully', 'green');

        // -------------------------------------------------------------
        // 5. MEDIA & VIDEOS & GALLERY (knowledge_media)
        // -------------------------------------------------------------
        $mediaData = [
            // INFOGRAPHICS (type: image)
            [
                'type'         => 'image',
                'title'        => 'Infografik: Alur Siklus Pengadaan Barang/Jasa Pemerintah (Perpres 12/2021)',
                'caption'      => 'Panduan visual bagan alur tahapan PBJ mulai dari Perencanaan, Persiapan, Pemilihan Penyedia, Pelaksanaan Kontrak, hingga Serah Terima.',
                'external_url' => '',
                'featured'     => 1,
                'sort_order'   => 1,
            ],
            [
                'type'         => 'image',
                'title'        => 'Infografik: 5 Langkah Menghitung TKDN Pengadaan Barang',
                'caption'      => 'Diagram alir penentuan kewajiban pembelian produk dalam negeri dan simulasi penerapan preferensi harga tender.',
                'external_url' => '',
                'featured'     => 1,
                'sort_order'   => 2,
            ],
            [
                'type'         => 'image',
                'title'        => 'Infografik: Skema Kontrak Kritis dan Uji Coba Show Cause Meeting (SCM)',
                'caption'      => 'Visualisasi timeline peringatan SCM I, II, dan III serta konsekuensi hukum pemutusan kontrak sepihak.',
                'external_url' => '',
                'featured'     => 0,
                'sort_order'   => 3,
            ],
            [
                'type'         => 'image',
                'title'        => 'Infografik: Diagram Alir e-Purchasing Melalui Katalog Elektronik V6',
                'caption'      => 'Skema belanja cepat daring mulai dari pembuatan paket, negosiasi harga, persetujuan PPK, hingga pengiriman pesanan.',
                'external_url' => '',
                'featured'     => 0,
                'sort_order'   => 4,
            ],

            // VIDEOS (type: youtube)
            [
                'type'         => 'youtube',
                'title'        => 'Tutorial Praktis: Pengoperasian Aplikasi SPSE 4.5 bagi Pejabat Pengadaan',
                'caption'      => 'Panduan video langkah demi langkah membuat paket pengadaan langsung, undangan evaluasi, dan penyusunan berita acara di SPSE.',
                'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'featured'     => 1,
                'sort_order'   => 5,
            ],
            [
                'type'         => 'youtube',
                'title'        => 'Webinar Bedah Kasus: Strategi Mitigasi Risiko Hukum Pengadaan Barang/Jasa',
                'caption'      => 'Rekaman webinar bersama narasumber ahli LKPP membahas penanganan sengketa kontrak dan pencegahan tindak pidana korupsi PBJ.',
                'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'featured'     => 1,
                'sort_order'   => 6,
            ],
            [
                'type'         => 'youtube',
                'title'        => 'Video Pembelajaran: Manajemen Kontrak & Serah Terima Konstruksi',
                'caption'      => 'Teknik pengawasan pekerjaan fisik lapangan, reviu kurva S, uji coba mutu material, dan penerbitan BAST yang akuntabel.',
                'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'featured'     => 0,
                'sort_order'   => 7,
            ],
            [
                'type'         => 'youtube',
                'title'        => 'Simulasi Negosiasi Harga e-Katalog V6 Bersama Fasilitator Biro PBJ',
                'caption'      => 'Praktik langsung trik negosiasi harga volume dan penjaminan layanan purna jual pada platform katalog elektronik nasional.',
                'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'featured'     => 0,
                'sort_order'   => 8,
            ],

            // GALLERY (type: gallery)
            [
                'type'         => 'gallery',
                'title'        => 'Bimtek Peningkatan Kompetensi PPK dan PPTK Lingkup Pemprov Lampung',
                'caption'      => 'Pelatihan intensif sertifikasi kompetensi pengadaan barang dan jasa pemerintah bertempat di Gedung Pusiban Bandar Lampung.',
                'external_url' => '',
                'featured'     => 1,
                'sort_order'   => 9,
            ],
            [
                'type'         => 'gallery',
                'title'        => 'Workshop Onboarding e-Katalog Lokal Bersama Ratusan Pelaku UMKM Lampung',
                'caption'      => 'Pendampingan pembuatan akun LPSE dan penayangan produk etalase lokal untuk mendukung pemberdayaan pengusaha daerah.',
                'external_url' => '',
                'featured'     => 1,
                'sort_order'   => 10,
            ],
            [
                'type'         => 'gallery',
                'title'        => 'Forum Koordinasi Pejabat Pengadaan se-Provinsi Lampung',
                'caption'      => 'Pertemuan berkala sinkronisasi kebijakan PBJ, penyelesaian kendala SPSE, dan peningkatan capaian belanja PDN tingkat kabupaten/kota.',
                'external_url' => '',
                'featured'     => 0,
                'sort_order'   => 11,
            ],
            [
                'type'         => 'gallery',
                'title'        => 'Sosialisasi Pencegahan Tipikor Pengadaan Bersama KPK & Inspektorat',
                'caption'      => 'Penguatan integritas aparatur pengadaan dalam rangka mewujudkan tata kelola pengadaan barang dan jasa yang bersih dan melayani.',
                'external_url' => '',
                'featured'     => 0,
                'sort_order'   => 12,
            ],
        ];

        foreach ($mediaData as $med) {
            $existing = $db->table('knowledge_media')->where('title', $med['title'])->get()->getRowArray();
            if ($existing) {
                $db->table('knowledge_media')->where('id', $existing['id'])->update([
                    'type'             => $med['type'],
                    'caption'          => $med['caption'],
                    'external_url'     => $med['external_url'],
                    'workflow_status'  => 'published',
                    'visibility'       => 'public',
                    'sort_order'       => $med['sort_order'],
                    'featured'         => $med['featured'],
                    'carousel_enabled' => 1,
                    'created_by'       => $adminId,
                    'published_by'     => $adminId,
                    'published_at'     => $now,
                    'updated_at'       => $now,
                ]);
            } else {
                $db->table('knowledge_media')->insert([
                    'type'             => $med['type'],
                    'title'            => $med['title'],
                    'caption'          => $med['caption'],
                    'external_url'     => $med['external_url'],
                    'workflow_status'  => 'published',
                    'visibility'       => 'public',
                    'sort_order'       => $med['sort_order'],
                    'featured'         => $med['featured'],
                    'carousel_enabled' => 1,
                    'created_by'       => $adminId,
                    'published_by'     => $adminId,
                    'published_at'     => $now,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            }
        }
        CLI::write('  [OK] Media, Videos, and Gallery seeded successfully', 'green');

        CLI::write('Bunga Rampai seeding complete!', 'green');
        return 0;
    }
}

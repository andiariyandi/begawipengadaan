<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NewsSeed extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'news:seed';
    protected $description = 'Seed authentic Indonesian PBJ news, categories, and media assets for BegawiPBJ.';
    protected $usage       = 'news:seed';

    public function run(array $params): int
    {
        $db = db_connect();
        CLI::write('Seeding authentic Berita PBJ data...', 'yellow');

        $now = date('Y-m-d H:i:s');

        // 1. Categories
        $categories = [
            [
                'name'        => 'Kebijakan & Regulasi PBJ',
                'slug'        => 'kebijakan-regulasi',
                'description' => 'Informasi kebijakan nasional LKPP, surat edaran gubernur, instruksi pengadaan, dan pembaruan aturan PBJ.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Tata Kelola & Integritas',
                'slug'        => 'tata-kelola-integritas',
                'description' => 'Penguatan probity audit, pencegahan benturan kepentingan, kepatuhan audit APIP, dan anti-korupsi di Lampung.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'E-Katalog & Pengadaan Digital',
                'slug'        => 'e-katalog-digital',
                'description' => 'Digitalisasi belanja pengadaan, katalog elektronik lokal Pemprov Lampung, dan kemudahan bagi UMKM-Koperasi.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Bimtek & Sertifikasi PBJ',
                'slug'        => 'bimtek-pelatihan',
                'description' => 'Pelatihan aparatur pengadaan, sertifikasi kompetensi PBJ level-1, peningkatan kapasitas PPK dan Pokja Pemilihan.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Capaian & Prestasi UKPBJ',
                'slug'        => 'capaian-prestasi',
                'description' => 'Tingkat kematangan UKPBJ Provinsi Lampung, realisasi P3DN, penghargaan nasional LKPP, dan inovasi pelayanan.',
                'is_active'   => 1,
            ],
            [
                'name'        => 'Pengumuman Resmi',
                'slug'        => 'pengumuman-resmi',
                'description' => 'Siaran pers resmi, agenda pembinaan, jadwal pemeliharaan server SPSE/LPSE, dan layanan helpdesk Biro PBJ.',
                'is_active'   => 1,
            ],
        ];

        $categoryMap = [];
        foreach ($categories as $cat) {
            $existing = $db->table('news_categories')->where('slug', $cat['slug'])->get()->getRowArray();
            if ($existing) {
                $db->table('news_categories')->where('id', $existing['id'])->update([
                    'name'        => $cat['name'],
                    'description' => $cat['description'],
                    'is_active'   => 1,
                    'updated_at'  => $now,
                ]);
                $categoryMap[$cat['slug']] = (int) $existing['id'];
            } else {
                $db->table('news_categories')->insert([
                    'name'        => $cat['name'],
                    'slug'        => $cat['slug'],
                    'description' => $cat['description'],
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
                $categoryMap[$cat['slug']] = (int) $db->insertID();
            }
        }
        CLI::write('✓ ' . count($categories) . ' news categories synced.', 'green');

        // 2. Fetch or create a system author user
        $author = $db->table('users')->where('email', 'admin@begawipbj.lampungprov.go.id')->get()->getRowArray()
            ?? $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $authorId = $author ? (int) $author['id'] : null;

        // 3. Articles dataset
        $articles = [
            [
                'title'       => 'Biro PBJ Setda Provinsi Lampung Raih Kategori Proaktif Maturitas UKPBJ dari LKPP RI',
                'slug'        => 'biro-pbj-lampung-raih-kategori-proaktif-maturitas-ukpbj-lkpp-ri',
                'category'    => 'capaian-prestasi',
                'summary'     => 'Pemerintah Provinsi Lampung melalui Biro Pengadaan Barang dan Jasa mencatatkan lonjakan kinerja signifikan dengan meraih predikat Kematangan UKPBJ Level 3 (Proaktif) dari Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah (LKPP RI).',
                'content'     => "BANDAR LAMPUNG — Pemerintah Provinsi Lampung melalui Biro Pengadaan Barang dan Jasa Sekretariat Daerah mencatatkan tonggak sejarah baru dalam tata kelola pengadaan barang dan jasa pemerintah. Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah (LKPP RI) secara resmi menganugerahkan predikat Tingkat Kematangan UKPBJ Level 3 (Proaktif).\n\nPenilaian ini mengacu pada 9 variabel strategis tata kelola pengadaan modern, mencakup manajemen kelembagaan, pengelolaan sumber daya manusia (SDM) pengadaan, tata kelola proses pemilihan, hingga implementasi sistem pengadaan berbasis elektronik terintegrasi.\n\nKepala Biro PBJ Setda Provinsi Lampung menyampaikan bahwa pencapaian ini merupakan buah kerja keras seluruh jajaran aparatur dan komitmen pimpinan daerah dalam mentransformasikan pengadaan dari sekadar proses administratif menjadi instrumen strategis pembangunan ekonomi daerah.\n\n\"Predikat Proaktif ini menuntut kami untuk terus meningkatkan mitigasi risiko sengketa, probity audit bersama APIP, serta mendorong keterlibatan optimal pelaku usaha mikro, kecil, dan koperasi (UMKM-K) lokal dalam seluruh etalase e-purchasing,\" ungkapnya dalam sambutan penerimaan penghargaan di Gedung Pusiban Kantor Gubernur Lampung.\n\nDengan capaian ini, UKPBJ Provinsi Lampung kini menjadi rujukan pendampingan dan pembinaan bagi UKPBJ di 15 Kabupaten/Kota se-Provinsi Lampung menuju standarisasi layanan pengadaan yang bersih, transparan, dan akuntabel.",
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'published_at'    => '2026-10-12 09:30:00',
                'view_count'      => 1420,
            ],
            [
                'title'       => 'Akselerasi Penggunaan Produk Dalam Negeri (P3DN), Pemprov Lampung Catat Transaksi E-Katalog Tembus Rekor Baru',
                'slug'        => 'akselerasi-p3dn-pemprov-lampung-transaksi-e-katalog-rekor-baru',
                'category'    => 'e-katalog-digital',
                'summary'     => 'Realisasi belanja pengadaan barang dan jasa Pemerintah Provinsi Lampung untuk produk ber-Tingkat Komponen Dalam Negeri (TKDN) pada kuartal berjalan melampaui target nasional hingga mencapai 88,4 persen.',
                'content'     => "BANDAR LAMPUNG — Komitmen nyata Pemerintah Provinsi Lampung dalam menyukseskan Gerakan Nasional Bangga Buatan Indonesia (BBI) kembali membuahkan hasil menggembirakan. Berdasarkan data pemantauan transaksi Katalog Elektronik Lokal, komitmen belanja Produk Dalam Negeri (P3DN) Pemprov Lampung menembus rekor baru dengan serapan mencapai 88,4% dari total paket pengadaan barang dan jasa pemerintah daerah.\n\nBiro PBJ Setda Provinsi Lampung terus melakukan pembukaan etalase baru pada platform E-Katalog Nasional dan Lokal LKPP. Hingga saat ini, tercatat lebih dari 45 etalase komoditas unggulan telah aktif, mulai dari produk makan-minum rapat, jasa kebersihan, seragam dinas, hingga material konstruksi jalan dan jembatan berstandar SNI.\n\n\"Katalog elektronik lokal bukan hanya memangkas birokrasi pengadaan konvensional, melainkan menjadi pendorong utama roda perputaran uang di Provinsi Lampung. Penyedia lokal tidak perlu lagi bersaing dalam tender rumit untuk komoditas rutin, melainkan cukup menayangkan produk bersertifikat TKDN di etalase resmi,\" jelas Kepala Bagian Pengelolaan PBJ.\n\nPemerintah Provinsi Lampung juga mengimbau seluruh Pejabat Pembuat Komitmen (PPK) di lingkungan OPD untuk memprioritaskan penyedia bersertifikat TKDN minimal 25% sesuai amanat Instruksi Presiden Nomor 2 Tahun 2022.",
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'published_at'    => '2026-10-10 14:15:00',
                'view_count'      => 980,
            ],
            [
                'title'       => 'Bimtek Intensif Pokja Pemilihan: Mitigasi Risiko Sanggah dan Penilaian Kualifikasi Penyedia Jasa Konstruksi',
                'slug'        => 'bimtek-intensif-pokja-pemilihan-mitigasi-risiko-sanggah-konstruksi',
                'category'    => 'bimtek-pelatihan',
                'summary'     => 'Sebanyak 75 anggota Pokja Pemilihan UKPBJ se-Lampung mengikuti bimbingan teknis intensif telaah dokumen pemilihan, tata cara evaluasi kewajaran harga, dan penanganan sanggahan sesuai Perpres 12/2021.',
                'content'     => "BANDAR LAMPUNG — Untuk memperkuat integritas dan kepatuhan hukum personil pengadaan, Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung menggelar Bimbingan Teknis (Bimtek) Intensif bertajuk \"Mitigasi Risiko Sanggah dan Penguatan Evaluasi Kualifikasi Penyedia Jasa Konstruksi\".\n\nKegiatan yang berlangsung selama tiga hari di Hotel Novotel Bandar Lampung ini menghadirkan narasumber utama dari Direktorat Advokasi Pemerintah Daerah LKPP RI dan Inspektorat Daerah Provinsi Lampung.\n\nFokus pembahasan mencakup mitigasi potensi sengketa tender konstruksi, teknik klarifikasi tenaga ahli dan peralatan utama, perhitungan analisis harga satuan pekerjaan (AHSP), serta tata cara penyusunan tanggapan sanggah yang objektif berbasis bukti faktual.\n\nDalam sesi studi kasus, para peserta melakukan simulasi evaluasi penawaran yang terindikasi persekongkolan tender (bid rigging) serta mitigasi koreksi aritmetik. Hasil bimtek ini diharapkan menurunkan angka sanggahan tidak beralasan dan mempercepat realisasi kontrak fisik di seluruh dinas teknis.",
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'published_at'    => '2026-10-08 10:00:00',
                'view_count'      => 765,
            ],
            [
                'title'       => 'Surat Edaran Bersama Pencegahan Fraud dan Gratifikasi pada Seluruh Tahapan PBJ Pemprov Lampung',
                'slug'        => 'surat-edaran-bersama-pencegahan-fraud-gratifikasi-pbj-lampung',
                'category'    => 'tata-kelola-integritas',
                'summary'     => 'Inspektorat Daerah bersama Biro PBJ Setda Provinsi Lampung menerbitkan Surat Edaran Bersama penegasan pakta integritas, larangan gratifikasi, serta kanal pengaduan Whistleblowing System (WBS).',
                'content'     => "BANDAR LAMPUNG — Menegakkan prinsip akuntabilitas dan tata kelola pemerintahan yang bersih (good governance), Sekretaris Daerah Provinsi Lampung bersama Inspektur Daerah dan Kepala Biro PBJ secara resmi merilis Surat Edaran Bersama Nomor 000.3/SE/PBJ-INSP/2026 tentang Pencegahan Tindak Pidana Korupsi, Fraud, dan Gratifikasi dalam Penyelenggaraan Pengadaan Barang/Jasa Pemerintah Daerah.\n\nSurat Edaran ini memuat empat poin krusial yang wajib dipatuhi oleh Pengguna Anggaran (PA), Kuasa Pengguna Anggaran (KPA), Pejabat Pembuat Komitmen (PPK), Pokja Pemilihan, Pejabat Pengadaan, serta seluruh asosiasi penyedia mitra Pemprov Lampung:\n\n1. Kewajiban penandatanganan Pakta Integritas Elektronik sebelum proses pemilihan dan penandatanganan SPK/Kontrak dimulai.\n2. Larangan keras menerima maupun memberi pemberian dalam bentuk apa pun (hadiah, komisi, fasilitas tiket, akomodasi, atau bingkisan hari raya) yang berkaitan dengan jabatan pengadaan.\n3. Implementasi Probity Audit oleh Tim Inspektorat Daerah terhadap paket pekerjaan bernilai strategis di atas Rp 5 Miliar sejak tahap perencanaan RUP hingga serah terima hasil pekerjaan (PHO/FHO).\n4. Penyediaan kanal pelaporan pelanggaran terenkripsi melalui Whistleblowing System (WBS) Provinsi Lampung dengan perlindungan identitas pelapor (whistleblower).\n\nLangkah terstruktur ini diharapkan menciptakan iklim persaingan usaha yang sehat dan menjaga marwah aparatur pengadaan di Bumi Ruwa Jurai.",
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'published_at'    => '2026-10-05 11:20:00',
                'view_count'      => 890,
            ],
            [
                'title'       => 'Sosialisasi Tata Kelola Katalog Elektronik Versi 6: Panduan Pendaftaran Etalase bagi Pelaku UMKM Lokal',
                'slug'        => 'sosialisasi-tata-kelola-katalog-elektronik-v6-panduan-umkm-lokal',
                'category'    => 'kebijakan-regulasi',
                'summary'     => 'Biro PBJ Lampung menyelenggarakan sosialisasi transisi menyeluruh ke platform E-Katalog Versi 6 nasional, memberikan kemudahan onboarding dan verifikasi izin usaha bagi pelaku usaha daerah.',
                'content'     => "BANDAR LAMPUNG — Transisi platform pengadaan nasional ke Katalog Elektronik Versi 6 (E-Katalog V6) membawa terobosan besar dalam kemudahan pencarian produk, transparansi perbandingan harga, dan kecepatan transaksi pembayaran pemerintah melalui Kartu Kredit Pemerintah Daerah (KKPD).\n\nGuna memastikan para pelaku Usaha Mikro, Kecil, dan Koperasi (UMKM-K) di Lampung tidak tertinggal dalam peluang belanja APBD, Biro PBJ Setda Provinsi Lampung membuka loket pendampingan klinik e-katalog di Ruang Rapat Biro PBJ Kompleks Kantor Gubernur Lampung.\n\nDalam paparannya, Tim Pengelola LPSE menjelaskan keunggulan sistem V6, di antaranya pencatatan riwayat transaksi secara real-time, integrasi otomatis dengan sistem perpajakan DJP melalui Konfirmasi Status Wajib Pajak (KSWP), serta verifikasi Nomor Induk Berusaha (NIB) langsung via OSS RBA.\n\n\"Kami ingin menghapus stigma bahwa masuk ke katalog pemerintah itu rumit. Dengan sistem baru, pendaftaran murni daring, bebas biaya retribusi apa pun, dan tim helpdesk siap mendampingi input data spesifikasi produk hingga tayang,\" tegas pejabat fungsional PBJ.",
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'published_at'    => '2026-10-02 08:45:00',
                'view_count'      => 620,
            ],
            [
                'title'       => 'Uji Sertifikasi Kompetensi PBJ Level 1 Batch IV Dibuka: Cetak 120 Aparatur Pengadaan Terakreditasi',
                'slug'        => 'uji-sertifikasi-kompetensi-pbj-level-1-batch-iv-dibuka-120-aparatur',
                'category'    => 'bimtek-pelatihan',
                'summary'     => 'Biro PBJ Setda Lampung kembali membuka registrasi pembelajaran blended learning dan uji sertifikasi keahlian pengadaan barang/jasa tingkat dasar bagi aparatur sipil negara di 15 kabupaten/kota.',
                'content'     => "BANDAR LAMPUNG — Memenuhi target pemenuhan Pejabat Fungsional Pengelola Pengadaan Barang/Jasa (JF-PPBJ) dan standarisasi kompetensi personil PPK/Pokja, Biro PBJ Setda Provinsi Lampung bekerja sama dengan Pusat Pendidikan dan Pelatihan LKPP RI membuka Uji Sertifikasi Kompetensi PBJ Level 1 Batch IV Tahun Anggaran 2026.\n\nProgram ini mengadopsi model Blended Learning yang menggabungkan pembelajaran mandiri secara e-learning melalui platform Arena Belajar BegawiPBJ, sesi tatap muka interaktif bersama instruktur master trainer LKPP, serta ujian berbasis komputer (CBT) di laboratorium komputer BPSDM Provinsi Lampung.\n\nPara peserta akan mendalami 4 modul utama kurikulum nasional:\n1. Ketentuan umum, prinsip, etika, dan kelembagaan PBJ Pemerintah.\n2. Perencanaan Pengadaan (identifikasi kebutuhan, penyusunan KAK, HPS, dan spesifikasi teknis).\n3. Pemilihan Penyedia Barang/Jasa (evaluasi kualifikasi, metode tender, e-purchasing, swakelola).\n4. Pelaksanaan dan Pengendalian Kontrak (SPK, surat perjanjian, serah terima hasil pekerjaan, penyelesaian sengketa kontrak).\n\nPendaftaran dibuka bagi seluruh ASN Provinsi dan Kabupaten/Kota di wilayah Lampung melalui portal resmi BegawiPBJ tanpa dipungut biaya pembinaan.",
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'published_at'    => '2026-09-28 13:00:00',
                'view_count'      => 1150,
            ],
            [
                'title'       => 'Workshop Probity Audit Bersama BPKP: Kawal Proyek Strategis Daerah Bebas Penyimpangan',
                'slug'        => 'workshop-probity-audit-bpkp-kawal-proyek-strategis-bebas-penyimpangan',
                'category'    => 'tata-kelola-integritas',
                'summary'     => 'Kolaborasi sinergis antara Perwakilan BPKP Provinsi Lampung dan Biro PBJ memperkuat pendampingan probity audit pada 12 proyek strategis infrastruktur dan konektivitas daerah.',
                'content'     => "BANDAR LAMPUNG — Menjaga agar proyek-proyek infrastruktur prioritas daerah terselenggara tepat mutu, tepat waktu, dan tepat sasaran tanpa kendala hukum di kemudian hari, Biro PBJ Setda Provinsi Lampung menggelar Workshop Pendampingan Probity Audit bersama Badan Pengawasan Keuangan dan Pembangunan (BPKP) Perwakilan Provinsi Lampung.\n\nFokus pengawalan mencakup 12 paket pekerjaan strategis tahun jamak (multiyears) pada Dinas Bina Marga dan Bina Konstruksi serta Dinas Pengelolaan Sumber Daya Air Provinsi Lampung.\n\nMelalui probity audit berkelanjutan, auditor internal memberikan masukan pencegahan (early warning system) sejak tahap reviu HPS, telaah klausul kontrak khusus, hingga pengawasan masa pemeliharaan konstruksi. Pendekatan ini terbukti menekan temuan berulang dan memastikan setiap rupiah anggaran publik memberi manfaat maksimal bagi masyarakat Lampung.",
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'published_at'    => '2026-09-24 15:30:00',
                'view_count'      => 540,
            ],
            [
                'title'       => 'Pengumuman Jadwal Pemeliharaan Berkala Server SPSE dan Pusat Data Nasional PBJ Lampung',
                'slug'        => 'pengumuman-jadwal-pemeliharaan-berkala-server-spse-lampung',
                'category'    => 'pengumuman-resmi',
                'summary'     => 'Pemberitahuan resmi pemeliharaan preventif perangkat keras, pembaruan sertifikat SSL, dan optimasi database server SPSE Provinsi Lampung pada akhir pekan.',
                'content'     => "BANDAR LAMPUNG — Dalam rangka meningkatkan performa, ketahanan keamanan jaringan, dan stabilitas akses transaksi pengadaan barang dan jasa secara elektronik, Unit Layanan Pengadaan Secara Elektronik (LPSE) Biro PBJ Setda Provinsi Lampung akan melaksanakan Pemeliharaan Berkala (Scheduled Maintenance) pada infrastruktur server SPSE.\n\nAdapun jadwal pemeliharaan terencana sebagai berikut:\n• Hari / Tanggal : Sabtu, 17 Oktober 2026 s/d Minggu, 18 Oktober 2026\n• Waktu Mulai : Pukul 22.00 WIB\n• Waktu Selesai : Pukul 06.00 WIB (8 jam)\n• Dampak Layanan : Akses ke portal lpse.lampungprov.go.id dan layanan e-katalog lokal akan mengalami penonaktifan sementara (downtime).\n\nBagi Pokja Pemilihan dan Pejabat Pengadaan yang memiliki tahapan pemasukan dokumen penawaran atau batas akhir sanggah bertepatan dengan jadwal pemeliharaan, diimbau untuk melakukan perpanjangan jadwal tahapan pengadaan pada aplikasi SPSE sesuai ketentuan Perka LKPP Nomor 12 Tahun 2021.\n\nLayanan Helpdesk SPSE tetap melayani konsultasi darurat melalui pesan WhatsApp di nomor +62 851-1777-9783.",
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'published_at'    => '2026-09-20 09:00:00',
                'view_count'      => 430,
            ],
            [
                'title'       => 'Kunjungan Studi Tiru UKPBJ Daerah Lain: Apresiasi Ekosistem Digital BegawiPBJ Lampung',
                'slug'        => 'kunjungan-studi-tiru-ukpbj-apresiasi-ekosistem-digital-begawipbj',
                'category'    => 'capaian-prestasi',
                'summary'     => 'Delegasi Bagian Pengadaan Barang dan Jasa sejumlah pemerintah kabupaten/kota dari luar provinsi melakukan benchmarking implementasi platform BegawiPBJ Lampung.',
                'content'     => "BANDAR LAMPUNG — Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung menerima kunjungan kerja dan studi tiru dari perwakilan Bagian PBJ beberapa pemerintah daerah di Pulau Sumatera dan Jawa.\n\nFokus studi tiru ini adalah mendalami strategi integrasi platform digital BegawiPBJ yang berhasil menyatukan Arena Belajar aparatur, repositori hukum Regulasi PBJ, hingga bank referensi dokumen Bunga Rampai ke dalam satu pintu layanan terpadu.\n\nPimpinan delegasi tamu mengapresiasi inovasi Pemprov Lampung yang tidak hanya fokus pada proses lelang formal, melainkan membangun ekosistem komprehensif yang mendampingi pejabat pengadaan dari sebelum tender hingga mitigasi audit pascakontrak.\n\n\"BegawiPBJ menjadi inspirasi nyata bagaimana pemerintah provinsi mengorkestrasi pembinaan SDM pengadaan secara digital, berkesinambungan, dan terukur capaian pembelajarannya,\" pungkas pimpinan rombongan.",
                'workflow_status' => 'published',
                'visibility'      => 'public',
                'published_at'    => '2026-09-15 11:00:00',
                'view_count'      => 710,
            ],
        ];

        $insertedCount = 0;
        $updatedCount  = 0;

        foreach ($articles as $art) {
            $catId = $categoryMap[$art['category']] ?? null;

            $existing = $db->table('news')->where('slug', $art['slug'])->get()->getRowArray();
            $data = [
                'category_id'     => $catId,
                'title'           => $art['title'],
                'slug'            => $art['slug'],
                'summary'         => $art['summary'],
                'content'         => $art['content'],
                'workflow_status' => $art['workflow_status'],
                'visibility'      => $art['visibility'],
                'published_at'    => $art['published_at'],
                'view_count'      => $art['view_count'],
                'author_id'       => $authorId,
                'updated_at'      => $now,
            ];

            if ($existing) {
                $db->table('news')->where('id', $existing['id'])->update($data);
                $updatedCount++;
            } else {
                $data['created_at'] = $art['published_at'];
                $db->table('news')->insert($data);
                $insertedCount++;
            }
        }

        CLI::write("✓ Finished news seeding: {$insertedCount} inserted, {$updatedCount} updated.", 'green');
        return 0;
    }
}

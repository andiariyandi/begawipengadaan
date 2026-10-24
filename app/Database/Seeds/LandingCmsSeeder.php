<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LandingCmsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. SITE SETTINGS
        $settings = [
            // General & Institution
            ['setting_group' => 'general', 'setting_key' => 'app_name', 'setting_value' => 'BegawiPengadaan', 'field_type' => 'text', 'description' => 'Nama Platform'],
            ['setting_group' => 'general', 'setting_key' => 'tagline', 'setting_value' => 'Transformasi Digital Terpadu Pengadaan Barang & Jasa', 'field_type' => 'text', 'description' => 'Tagline Hero'],
            ['setting_group' => 'general', 'setting_key' => 'hero_description', 'setting_value' => 'Integrasi Arena Belajar aparatur, repositori dokumen Bunga Rampai, dan pusat regulasi PBJ terpadu Pemerintah Provinsi Lampung.', 'field_type' => 'textarea', 'description' => 'Deskripsi Hero'],

            ['setting_group' => 'organization', 'setting_key' => 'agency_name', 'setting_value' => 'Biro Pengadaan Barang & Jasa Sekretariat Daerah Provinsi Lampung', 'field_type' => 'text', 'description' => 'Nama Instansi'],
            ['setting_group' => 'organization', 'setting_key' => 'institution_level', 'setting_value' => 'UKPBJ Level Proaktif (Tingkat Kematangan Proaktif)', 'field_type' => 'text', 'description' => 'Status Kematangan UKPBJ'],
            ['setting_group' => 'organization', 'setting_key' => 'institution_zone', 'setting_value' => 'Zona Integritas Wilayah Bebas Korupsi & Gratifikasi', 'field_type' => 'text', 'description' => 'Predikat Integritas'],
            ['setting_group' => 'organization', 'setting_key' => 'address', 'setting_value' => 'Jl. Wolter Monginsidi No. 69, Teluk Betung, Kota Bandar Lampung, Lampung 35211', 'field_type' => 'textarea', 'description' => 'Alamat Kantor'],
            ['setting_group' => 'organization', 'setting_key' => 'phone', 'setting_value' => '+62 851-1777-9783', 'field_type' => 'text', 'description' => 'Nomor Telepon'],
            ['setting_group' => 'organization', 'setting_key' => 'email', 'setting_value' => 'biropbj@lampungprov.go.id', 'field_type' => 'text', 'description' => 'Email Resmi'],
            ['setting_group' => 'organization', 'setting_key' => 'operational_hours', 'setting_value' => 'Senin - Jumat, 08.00 - 16.00 WIB', 'field_type' => 'text', 'description' => 'Jam Layanan'],
            ['setting_group' => 'organization', 'setting_key' => 'google_maps_url', 'setting_value' => 'https://maps.google.com/?q=Biro+Pengadaan+Barang+dan+Jasa+Provinsi+Lampung', 'field_type' => 'text', 'description' => 'Peta Lokasi Google Maps'],

            // Social Media
            ['setting_group' => 'social', 'setting_key' => 'facebook_url', 'setting_value' => 'https://facebook.com/biropbjlampung', 'field_type' => 'text', 'description' => 'Facebook URL'],
            ['setting_group' => 'social', 'setting_key' => 'instagram_url', 'setting_value' => 'https://instagram.com/biropbj_lampung', 'field_type' => 'text', 'description' => 'Instagram URL'],
            ['setting_group' => 'social', 'setting_key' => 'youtube_url', 'setting_value' => 'https://youtube.com/@biropbjsetdaprovinsilampung', 'field_type' => 'text', 'description' => 'YouTube URL'],
            ['setting_group' => 'social', 'setting_key' => 'linkedin_url', 'setting_value' => 'https://linkedin.com/company/biropbj-lampung', 'field_type' => 'text', 'description' => 'LinkedIn URL'],

            // Footer & Copyright
            ['setting_group' => 'footer', 'setting_key' => 'copyright_text', 'setting_value' => 'Copyright © {year} BegawiPengadaan. Pemerintah Provinsi Lampung.', 'field_type' => 'text', 'description' => 'Teks Hak Cipta'],
            ['setting_group' => 'footer', 'setting_key' => 'subtext', 'setting_value' => 'Pengadaan kompeten, transparan, dan berdampak.', 'field_type' => 'text', 'description' => 'Slogan Footer'],

            // Vision & Mission & Philosophy
            ['setting_group' => 'vision_mission', 'setting_key' => 'vision_title', 'setting_value' => 'Visi Utama 2024–2029', 'field_type' => 'text', 'description' => 'Judul Visi'],
            ['setting_group' => 'vision_mission', 'setting_key' => 'vision_statement', 'setting_value' => 'Terwujudnya Tata Kelola Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung yang Profesional, Transparan, Akuntabel, dan Menjadi Penggerak Utama Pertumbuhan Ekonomi Lokal yang Inklusif.', 'field_type' => 'textarea', 'description' => 'Pernyataan Visi'],
            ['setting_group' => 'vision_mission', 'setting_key' => 'vision_focus', 'setting_value' => 'Fokus: Kematangan Proaktif & E-Katalog Lokal', 'field_type' => 'text', 'description' => 'Fokus Strategis'],
            ['setting_group' => 'vision_mission', 'setting_key' => 'mission_items', 'setting_value' => json_encode([
                'Peningkatan Kompetensi Berkelanjutan: Mengembangkan kapasitas SDM PBJ melalui Arena Belajar digital, bimtek teknis, dan sertifikasi terstandarisasi.',
                'Penguatan Tata Kelola & Advokasi: Memberikan layanan pembinaan, pendampingan hukum, dan mitigasi risiko pengadaan bagi seluruh OPD.',
                'Akselerasi E-Purchasing & UMKM: Mengoptimalkan E-Katalog Lokal Lampung dan Toko Daring guna menyerap produk dalam negeri (P3DN).',
                'Pusat Literasi & Regulasi Terbuka: Menjamin kemudahan akses regulasi, standar dokumen, dan telaah hukum melalui Bunga Rampai.',
                'Government Intelligence PBJ: Mengintegrasikan data pengadaan untuk pelaporan analitik, monitoring kinerja, dan pengambilan keputusan pimpinan.'
            ], JSON_UNESCAPED_UNICODE), 'field_type' => 'json', 'description' => 'Daftar Misi Strategis'],

            ['setting_group' => 'philosophy', 'setting_key' => 'philosophy_title', 'setting_value' => 'Makna "Begawi" dalam Pengadaan Modern', 'field_type' => 'text', 'description' => 'Judul Filosofi'],
            ['setting_group' => 'philosophy', 'setting_key' => 'philosophy_p1', 'setting_value' => 'Kata "Begawi" berakar dari kearifan bahasa dan adat budaya Lampung yang mencerminkan etos kerja luhur, bergotong-royong, dan mengabdi dengan penuh kesungguhan serta kehormatan.', 'field_type' => 'textarea', 'description' => 'Paragraf 1 Filosofi'],
            ['setting_group' => 'philosophy', 'setting_key' => 'philosophy_p2', 'setting_value' => 'Dalam konteks pengadaan barang dan jasa modern, BegawiPengadaan hadir mengintegrasikan semangat pengabdian tersebut dengan teknologi digital tingkat lanjut. Tujuannya adalah menjadikan seluruh proses pengadaan di Provinsi Lampung tidak sekadar taat administratif, melainkan memberikan nilai manfaat nyata bagi kemakmuran masyarakat Bumi Ruwa Jurai.', 'field_type' => 'textarea', 'description' => 'Paragraf 2 Filosofi'],
            ['setting_group' => 'philosophy', 'setting_key' => 'philosophy_quote', 'setting_value' => '"Pengadaan Barang dan Jasa bukan sekadar membelanjakan anggaran negara, melainkan instrumen strategis pembangunan yang menggerakkan ekonomi lokal dan mensejahterakan rakyat."', 'field_type' => 'textarea', 'description' => 'Kutipan Filosofi'],

            ['setting_group' => 'pledge', 'setting_key' => 'maklumat_pelayanan', 'setting_value' => '"Dengan ini, kami pimpinan dan seluruh aparatur Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung menyatakan sanggup menyelenggarakan pelayanan pengadaan barang dan jasa sesuai standar pelayanan yang telah ditetapkan, memberikan pelayanan yang profesional, transparan, akuntabel, bebas dari gratifikasi dan korupsi, serta siap menerima sanksi sesuai peraturan perundang-undangan apabila tidak menepati janji ini."', 'field_type' => 'textarea', 'description' => 'Maklumat Pelayanan'],

            // Announcement popup setting
            ['setting_group' => 'announcement', 'setting_key' => 'popup_enabled', 'setting_value' => '1', 'field_type' => 'boolean', 'description' => 'Aktifkan Modal Popup Promo di Beranda'],
            ['setting_group' => 'announcement', 'setting_key' => 'popup_badge', 'setting_value' => 'PENGUMUMAN RESMI PBJ PROVINSI LAMPUNG', 'field_type' => 'text', 'description' => 'Badge Header Popup Promo'],

            // WhatsApp Helpdesk Widget
            ['setting_group' => 'helpdesk', 'setting_key' => 'wa_enabled', 'setting_value' => '1', 'field_type' => 'boolean', 'description' => 'Aktifkan Widget WhatsApp Chat Melayang'],
            ['setting_group' => 'helpdesk', 'setting_key' => 'wa_phone', 'setting_value' => '6285117779783', 'field_type' => 'text', 'description' => 'Nomor WhatsApp Helpdesk'],
            ['setting_group' => 'helpdesk', 'setting_key' => 'wa_title', 'setting_value' => 'Helpdesk BegawiPengadaan', 'field_type' => 'text', 'description' => 'Judul Widget Helpdesk'],
            ['setting_group' => 'helpdesk', 'setting_key' => 'wa_subtitle', 'setting_value' => 'Biro PBJ Setda Prov. Lampung', 'field_type' => 'text', 'description' => 'Subjudul Widget Helpdesk'],
            ['setting_group' => 'helpdesk', 'setting_key' => 'wa_welcome_text', 'setting_value' => "Halo! Selamat datang di BegawiPengadaan 👋\n\nAda yang bisa kami bantu terkait regulasi PBJ, pelatihan di Arena Belajar, atau template dokumen kerja?", 'field_type' => 'textarea', 'description' => 'Pesan Sambutan Chat Bubble'],
            ['setting_group' => 'helpdesk', 'setting_key' => 'wa_teaser_text', 'setting_value' => 'Butuh bantuan seputar PBJ? Chat kami! 👋', 'field_type' => 'text', 'description' => 'Teks Bubble Teaser'],
        ];

        foreach ($settings as $s) {
            $existing = $db->table('site_settings')
                ->where('setting_group', $s['setting_group'])
                ->where('setting_key', $s['setting_key'])
                ->get()->getRow();

            if ($existing) {
                $db->table('site_settings')
                    ->where('setting_group', $s['setting_group'])
                    ->where('setting_key', $s['setting_key'])
                    ->update([
                        'setting_value' => $s['setting_value'],
                        'field_type'    => $s['field_type'],
                        'description'   => $s['description'],
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ]);
            } else {
                $db->table('site_settings')->insert(array_merge($s, ['created_at' => date('Y-m-d H:i:s')]));
            }
        }

        // 2. HOME SLIDERS
        $sliders = [
            [
                'badge_text'             => 'Pemerintah Provinsi Lampung · Biro PBJ Setda',
                'title'                  => 'Transformasi Digital Terpadu <span>Pengadaan Barang & Jasa</span>',
                'subtitle'               => 'Integrasi Arena Belajar aparatur, repositori dokumen Bunga Rampai, dan pusat regulasi PBJ terpadu.',
                'image_url'              => 'assets/main/images/backgrounds/slider-1-1.png',
                'button_text'            => 'Pusat Regulasi PBJ',
                'button_url'             => 'regulasi',
                'secondary_button_text'  => 'Arena Belajar',
                'secondary_button_url'   => 'arena-belajar',
                'sort_order'             => 1,
                'is_active'              => 1,
            ],
            [
                'badge_text'             => 'Arena Belajar · Sertifikasi Kompetensi PBJ',
                'title'                  => 'Tingkatkan Keahlian PBJ <span>Bersama Instruktur Ahli LKPP</span>',
                'subtitle'               => 'Modul berjenjang terakreditasi LKPP, pembelajaran terjadwal, dan sertifikat resmi Pemprov Lampung.',
                'image_url'              => 'assets/main/images/backgrounds/slider-1-2.png',
                'button_text'            => 'Katalog Kursus',
                'button_url'             => 'arena-belajar/course',
                'secondary_button_text'  => 'Sertifikasi PBJ',
                'secondary_button_url'   => 'arena-belajar/program',
                'sort_order'             => 2,
                'is_active'              => 1,
            ],
            [
                'badge_text'             => 'Bunga Rampai · Dokumen & Praktik Baik',
                'title'                  => 'Template Dokumen KAK, HPS <span>& Mitigasi Risiko Audit Pengadaan</span>',
                'subtitle'               => 'Unduh format dokumen kerja siap pakai (DOCX/XLSX), telaah sengketa sanggahan, dan alur tata kelola PBJ.',
                'image_url'              => 'assets/main/images/backgrounds/slider-1-3.png',
                'button_text'            => 'Template Dokumen',
                'button_url'             => 'bunga-rampai/template-dokumen',
                'secondary_button_text'  => 'Studi Kasus',
                'secondary_button_url'   => 'bunga-rampai/studi-kasus',
                'sort_order'             => 3,
                'is_active'              => 1,
            ],
        ];

        $db->table('home_sliders')->truncate();
        foreach ($sliders as $slide) {
            $slide['created_at'] = date('Y-m-d H:i:s');
            $db->table('home_sliders')->insert($slide);
        }

        // 3. SITE ANNOUNCEMENTS (POPUP PROMO)
        $announcements = [
            [
                'badge_label' => 'AGENDA & BIMTEK 2026',
                'title'       => 'Bimbingan Teknis & Uji Sertifikasi PBJ Level-1 Terpadu',
                'description' => 'Pendaftaran gelombang baru program sertifikasi dan pelatihan kompetensi pengadaan barang/jasa bagi aparatur Pemprov Lampung kini dibuka.',
                'image_url'   => 'assets/main/images/event/event-details-top-img-1.jpg',
                'cta_label'   => 'Lihat Jadwal Agenda',
                'cta_url'     => 'event',
                'sort_order'  => 1,
                'is_active'   => 1,
            ],
            [
                'badge_label' => 'ARENA BELAJAR APARATUR',
                'title'       => 'Tingkatkan Keahlian Mandiri Melalui Silabus & Kursus Terakreditasi',
                'description' => 'Akses materi silabus berjenjang, modul manajemen kontrak, latihan soal ujian berkala, dan kurikulum standar LKPP secara gratis.',
                'image_url'   => 'assets/main/images/resources/course-details-img-1.jpg',
                'cta_label'   => 'Eksplorasi Kursus',
                'cta_url'     => 'arena-belajar',
                'sort_order'  => 2,
                'is_active'   => 1,
            ],
            [
                'badge_label' => 'REGULASI & BUNGA RAMPAI',
                'title'       => 'Repositori Dokumen Kerja KAK, HPS & Kumpulan Regulasi PBJ',
                'description' => 'Dapatkan template dokumen pengadaan siap pakai (DOCX/XLSX), telaah silsilah aturan Perpres/PerLKPP, serta infografis panduan mitigasi risiko.',
                'image_url'   => 'assets/main/images/resources/video-one-img-1.jpg',
                'cta_label'   => 'Unduh Dokumen Kerja',
                'cta_url'     => 'bunga-rampai',
                'sort_order'  => 3,
                'is_active'   => 1,
            ],
        ];

        $db->table('site_announcements')->truncate();
        foreach ($announcements as $ann) {
            $ann['created_at'] = date('Y-m-d H:i:s');
            $db->table('site_announcements')->insert($ann);
        }

        // 4. FEATURE HIGHLIGHTS
        $highlights = [
            // 5 Pillars
            [
                'section_key' => 'pilar_begawi',
                'badge_text'  => 'Pilar 01',
                'code'        => '01',
                'title'       => 'Arena Belajar LMS',
                'subtitle'    => 'Silabus LKPP',
                'description' => 'Platform diklat daring mandiri, sertifikasi kompetensi, kuis interaktif, dan tracking kelulusan aparatur.',
                'icon_class'  => 'solar:graduation-cap-bold',
                'color_hex'   => '#0c5aa6',
                'link_url'    => 'arena-belajar',
                'sort_order'  => 1,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'pilar_begawi',
                'badge_text'  => 'Pilar 02',
                'code'        => '02',
                'title'       => 'Bunga Rampai Dokumen',
                'subtitle'    => 'Template & Praktek Baik',
                'description' => 'Repositori template KAK, HPS, rancangan kontrak siap pakai, infografis alur, serta studi kasus sengketa PBJ.',
                'icon_class'  => 'solar:folder-with-files-bold',
                'color_hex'   => '#0284c7',
                'link_url'    => 'bunga-rampai',
                'sort_order'  => 2,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'pilar_begawi',
                'badge_text'  => 'Pilar 03',
                'code'        => '03',
                'title'       => 'Pusat Regulasi PBJ',
                'subtitle'    => 'Hierarki Hukum',
                'description' => 'Pangkalan data regulasi PBJ terpadu: Perpres 16/2018 & 12/2021, PerLKPP, SE Kepala LKPP, hingga Peraturan Gubernur Lampung.',
                'icon_class'  => 'solar:document-text-bold',
                'color_hex'   => '#10b981',
                'link_url'    => 'regulasi',
                'sort_order'  => 3,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'pilar_begawi',
                'badge_text'  => 'Pilar 04',
                'code'        => '04',
                'title'       => 'Klinik Konsultasi & Tanya Jawab',
                'subtitle'    => 'Bantuan Interaktif',
                'description' => 'Layanan pendampingan teknis bagi PPK, Pokja Pemilihan, Pejabat Pengadaan, dan Penyedia barang/jasa.',
                'icon_class'  => 'solar:chat-round-dots-bold',
                'color_hex'   => '#f59e0b',
                'link_url'    => 'kontak',
                'sort_order'  => 4,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'pilar_begawi',
                'badge_text'  => 'Pilar 05',
                'code'        => '05',
                'title'       => 'Verifikasi Sertifikat Digital',
                'subtitle'    => 'Integritas QR Code',
                'description' => 'Validasi keaslian sertifikat kelulusan diklat dan workshop pengadaan secara instan dengan proteksi kode hash SHA-256.',
                'icon_class'  => 'solar:verified-check-bold',
                'color_hex'   => '#6366f1',
                'link_url'    => 'verifikasi-sertifikat',
                'sort_order'  => 5,
                'is_active'   => 1,
            ],

            // 5 PATIK Values
            [
                'section_key' => 'budaya_patik',
                'badge_text'  => 'P',
                'code'        => 'P',
                'title'       => 'Profesional',
                'subtitle'    => 'Kompetensi Tinggi',
                'description' => 'Menjalankan tugas pengadaan berlandaskan keahlian, standar kompetensi LKPP, dan kode etik ASN.',
                'icon_class'  => 'solar:star-bold',
                'color_hex'   => '#0c5aa6',
                'sort_order'  => 1,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'budaya_patik',
                'badge_text'  => 'A',
                'code'        => 'A',
                'title'       => 'Akuntabel',
                'subtitle'    => 'Dapat Dipertanggungjawabkan',
                'description' => 'Setiap keputusan pengadaan dapat dipertanggungjawabkan secara hukum, administratif, dan finansial.',
                'icon_class'  => 'solar:shield-check-bold',
                'color_hex'   => '#0284c7',
                'sort_order'  => 2,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'budaya_patik',
                'badge_text'  => 'T',
                'code'        => 'T',
                'title'       => 'Transparan',
                'subtitle'    => 'Keterbukaan Informasi',
                'description' => 'Keterbukaan informasi proses dan hasil pengadaan bagi seluruh pelaku usaha dan masyarakat luas.',
                'icon_class'  => 'solar:eye-bold',
                'color_hex'   => '#10b981',
                'sort_order'  => 3,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'budaya_patik',
                'badge_text'  => 'I',
                'code'        => 'I',
                'title'       => 'Integritas',
                'subtitle'    => 'Bebas Gratifikasi',
                'description' => 'Menolak segala bentuk gratifikasi, benturan kepentingan, dan praktik suap demi zona integritas bersih.',
                'icon_class'  => 'solar:lock-keyhole-bold',
                'color_hex'   => '#f59e0b',
                'sort_order'  => 4,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'budaya_patik',
                'badge_text'  => 'K',
                'code'        => 'K',
                'title'       => 'Kredibel',
                'subtitle'    => 'Terpercaya & Sahih',
                'description' => 'Menjaga kepercayaan publik melalui proses pengadaan yang adil, objektif, dan memberikan nilai manfaat terbaik.',
                'icon_class'  => 'solar:medal-ribbon-star-bold',
                'color_hex'   => '#ef4444',
                'sort_order'  => 5,
                'is_active'   => 1,
            ],

            // 4 Legal Hierarchy Tiers
            [
                'section_key' => 'hierarki_hukum',
                'badge_text'  => 'Tingkat I',
                'code'        => 'UU',
                'title'       => 'Undang-Undang & Perppu',
                'subtitle'    => 'Landasan Pokok',
                'description' => 'UU No. 1 Tahun 2004 (Perbendaharaan Negara) dan UU Cipta Kerja Klaster Pengadaan & Kemudahan Usaha.',
                'icon_class'  => 'solar:diploma-verified-bold',
                'color_hex'   => '#0c5aa6',
                'sort_order'  => 1,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'hierarki_hukum',
                'badge_text'  => 'Tingkat II',
                'code'        => 'PERPRES',
                'title'       => 'Peraturan Presiden (Perpres)',
                'subtitle'    => 'Payung Operasional',
                'description' => 'Perpres No. 16 Tahun 2018 jo. Perpres No. 12 Tahun 2021 tentang Pengadaan Barang/Jasa Pemerintah.',
                'icon_class'  => 'solar:document-bold',
                'color_hex'   => '#0284c7',
                'sort_order'  => 2,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'hierarki_hukum',
                'badge_text'  => 'Tingkat III',
                'code'        => 'PERLKPP',
                'title'       => 'Peraturan LKPP & SE Kepala',
                'subtitle'    => 'Pedoman Teknis',
                'description' => 'Peraturan LKPP No. 9 s.d. 12 Tahun 2021 dan Surat Edaran LKPP terkait e-purchasing serta mitigasi kontrak.',
                'icon_class'  => 'solar:book-bookmark-bold',
                'color_hex'   => '#10b981',
                'sort_order'  => 3,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'hierarki_hukum',
                'badge_text'  => 'Tingkat IV',
                'code'        => 'PERGUB',
                'title'       => 'Regulasi Daerah Provinsi Lampung',
                'subtitle'    => 'Instruksi & Kepgub',
                'description' => 'Pergub dan Keputusan Gubernur Lampung tentang Tata Kelola Pengadaan dan P3DN UMKM Lokal Lampung.',
                'icon_class'  => 'solar:city-bold',
                'color_hex'   => '#f59e0b',
                'sort_order'  => 4,
                'is_active'   => 1,
            ],

            // 4 LKPP Competency Roadmap
            [
                'section_key' => 'roadmap_lkpp',
                'badge_text'  => 'Level 1',
                'code'        => 'L1',
                'title'       => 'Kompetensi Dasar PBJ',
                'subtitle'    => 'Fondasi Pengadaan',
                'description' => 'Pemahaman prinsip dasar, etika, perencanaan, pemilihan penyedia, dan swakelola pengadaan pemerintah.',
                'icon_class'  => 'solar:medal-star-bold',
                'color_hex'   => '#0c5aa6',
                'sort_order'  => 1,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'roadmap_lkpp',
                'badge_text'  => 'Level 2',
                'code'        => 'L2',
                'title'       => 'Kompetensi Teknis Operasional',
                'subtitle'    => 'Pokja & Pejabat Pengadaan',
                'description' => 'Penyusunan KAK/Spesifikasi, evaluasi penawaran e-tendering, negosiasi teknis, dan manajemen risiko awal.',
                'icon_class'  => 'solar:wrench-bold',
                'color_hex'   => '#0284c7',
                'sort_order'  => 2,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'roadmap_lkpp',
                'badge_text'  => 'Level 3',
                'code'        => 'L3',
                'title'       => 'Kompetensi Manajerial Kontrak',
                'subtitle'    => 'Pejabat Pembuat Komitmen (PPK)',
                'description' => 'Pengendalian kontrak kritis, perubahan kontrak (adendum), serah terima pekerjaan, dan mitigasi dispute.',
                'icon_class'  => 'solar:shield-check-bold',
                'color_hex'   => '#10b981',
                'sort_order'  => 3,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'roadmap_lkpp',
                'badge_text'  => 'Level 4',
                'code'        => 'L4',
                'title'       => 'Kompetensi Strategis & Advokasi',
                'subtitle'    => 'PA / KPA & Konsultan Ahli',
                'description' => 'Penyusunan kebijakan pengadaan strategis, pembelaan hukum/advokasi sanggahan, dan tata kelola UKPBJ mandiri.',
                'icon_class'  => 'solar:crown-line-duotone',
                'color_hex'   => '#f59e0b',
                'sort_order'  => 4,
                'is_active'   => 1,
            ],

            // 4 Certificate Verification Security Features
            [
                'section_key' => 'keamanan_sertifikat',
                'badge_text'  => 'Kriptografi',
                'code'        => 'QR',
                'title'       => 'Kode QR Dinamis',
                'subtitle'    => 'Pencegahan Manipulasi',
                'description' => 'Tiap sertifikat memuat tautan verifikasi unik yang langsung memeriksa integritas data di basis data resmi.',
                'icon_class'  => 'solar:qr-code-bold',
                'color_hex'   => '#0c5aa6',
                'sort_order'  => 1,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'keamanan_sertifikat',
                'badge_text'  => 'Integritas',
                'code'        => 'SHA',
                'title'       => 'Hash Digital SHA-256',
                'subtitle'    => 'Kunci Validitas',
                'description' => 'Tanda tangan digital dan hash algoritma SHA-256 memastikan berkas sertifikat tidak dapat diubah.',
                'icon_class'  => 'solar:lock-keyhole-bold',
                'color_hex'   => '#10b981',
                'sort_order'  => 2,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'keamanan_sertifikat',
                'badge_text'  => 'Publik',
                'code'        => 'PUB',
                'title'       => 'Verifikasi Publik Real-time',
                'subtitle'    => 'Akses Transparan 24 Jam',
                'description' => 'Instansi, auditor, dan publik dapat memeriksa masa berlaku sertifikat tanpa perlu login.',
                'icon_class'  => 'solar:eye-bold',
                'color_hex'   => '#0284c7',
                'sort_order'  => 3,
                'is_active'   => 1,
            ],
            [
                'section_key' => 'keamanan_sertifikat',
                'badge_text'  => 'Otoritas',
                'code'        => 'TTE',
                'title'       => 'Tanda Tangan Elektronik Sah',
                'subtitle'    => 'Biro PBJ Setda Lampung',
                'description' => 'Diterbitkan secara sah oleh Unit Kerja Pengadaan Barang/Jasa Provinsi Lampung dengan registrasi resmi.',
                'icon_class'  => 'solar:diploma-verified-bold',
                'color_hex'   => '#f59e0b',
                'sort_order'  => 4,
                'is_active'   => 1,
            ],
        ];

        $db->table('feature_highlights')->truncate();
        foreach ($highlights as $h) {
            $h['created_at'] = date('Y-m-d H:i:s');
            $db->table('feature_highlights')->insert($h);
        }

        // 5. EXTERNAL PORTALS
        $portals = [
            [
                'name'        => 'LPSE Provinsi Lampung',
                'acronym'     => 'SPSE',
                'category'    => 'daerah',
                'description' => 'Sistem Pengadaan Secara Elektronik (SPSE) tender, seleksi, dan non-tender Pemprov Lampung.',
                'url'         => 'https://lpse.lampungprov.go.id',
                'logo_url'    => 'solar:server-square-bold',
                'sort_order'  => 1,
                'is_active'   => 1,
            ],
            [
                'name'        => 'SiRUP LKPP',
                'acronym'     => 'SiRUP',
                'category'    => 'nasional',
                'description' => 'Sistem Informasi Rencana Umum Pengadaan seluruh paket belanja anggaran Pemprov Lampung.',
                'url'         => 'https://sirup.lkpp.go.id',
                'logo_url'    => 'solar:document-add-bold',
                'sort_order'  => 2,
                'is_active'   => 1,
            ],
            [
                'name'        => 'Katalog Elektronik (E-Katalog LKPP)',
                'acronym'     => 'E-Katalog',
                'category'    => 'nasional',
                'description' => 'Belanja pengadaan daring komoditas katalog nasional, sektoral, dan Katalog Elektronik Lokal Lampung.',
                'url'         => 'https://e-katalog.lkpp.go.id',
                'logo_url'    => 'solar:cart-large-4-bold',
                'sort_order'  => 3,
                'is_active'   => 1,
            ],
            [
                'name'        => 'Bela Pengadaan LKPP',
                'acronym'     => 'Bela',
                'category'    => 'nasional',
                'description' => 'Marketplace pengadaan langsung untuk belanja UMKM lokal di bawah 200 juta rupiah.',
                'url'         => 'https://belapengadaan.lkpp.go.id',
                'logo_url'    => 'solar:shop-2-bold',
                'sort_order'  => 4,
                'is_active'   => 1,
            ],
            [
                'name'        => 'Portal Resmi LKPP RI',
                'acronym'     => 'LKPP',
                'category'    => 'nasional',
                'description' => 'Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah Republik Indonesia.',
                'url'         => 'https://www.lkpp.go.id',
                'logo_url'    => 'solar:city-bold',
                'sort_order'  => 5,
                'is_active'   => 1,
            ],
            [
                'name'        => 'JDIH Pemerintah Provinsi Lampung',
                'acronym'     => 'JDIH',
                'category'    => 'daerah',
                'description' => 'Pusat regulasi daerah, Peraturan Gubernur, dan Keputusan Gubernur Provinsi Lampung.',
                'url'         => 'https://jdih.lampungprov.go.id',
                'logo_url'    => 'solar:book-bookmark-bold',
                'sort_order'  => 6,
                'is_active'   => 1,
            ],
        ];

        $db->table('external_portals')->truncate();
        foreach ($portals as $portal) {
            $portal['created_at'] = date('Y-m-d H:i:s');
            $db->table('external_portals')->insert($portal);
        }

        // 6. TESTIMONIALS
        $testimonials = [
            [
                'name'             => 'Ahmad Zulkarnaen, S.T., M.T.',
                'role_title'       => 'Pejabat Pembuat Komitmen (PPK)',
                'organization'     => 'Dinas Bina Marga & Bina Konstruksi Provinsi Lampung',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-1-1.jpg',
                'rating'           => 5,
                'quote'            => 'Penyusunan HPS dan spesifikasi teknis untuk paket infrastruktur jalan kini jauh lebih terstandarisasi berkat template dokumen di Bunga Rampai dan sinkronisasi regulasi terpusat. Risiko deviasi hukum saat audit probity berkurang drastis.',
                'impact_statement' => 'Efisiensi persiapan lelang 55% & probity nihil temuan',
                'category'         => 'PPK Pengadaan',
                'sort_order'       => 1,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Rina Widyastuti, S.E., M.Si.',
                'role_title'       => 'Pokja Pemilihan / UKPBJ',
                'organization'     => 'Biro Pengadaan Barang & Jasa Setda Provinsi Lampung',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-1-2.jpg',
                'rating'           => 5,
                'quote'            => 'Arena Belajar dan simulasi evaluasi penawaran sangat membantu peningkatan kapasitas anggota Pokja. Ujian sertifikasi dan materi terstruktur meningkatkan kepercayaan diri kami dalam tender bernilai strategis di tingkat provinsi.',
                'impact_statement' => 'Evaluasi tender 100% tepat waktu dan bebas sanggahan banding',
                'category'         => 'Pokja Pemilihan',
                'sort_order'       => 2,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Hendra Kusuma, S.Kom.',
                'role_title'       => 'Mitra Penyedia & UMKM',
                'organization'     => 'PT Lampung Cipta Mandiri',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-2-1.jpg',
                'rating'           => 5,
                'quote'            => 'Sebagai pelaku usaha lokal di Lampung, portal BegawiPengadaan membuka transparansi penuh. Kami mudah memahami kriteria kualifikasi, etika penawaran, hingga tata cara etalase produk pada e-katalog lokal Pemprov Lampung.',
                'impact_statement' => 'Onboarding E-Katalog Lokal lebih cepat dan transparan',
                'category'         => 'Penyedia UMKM',
                'sort_order'       => 3,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Drs. Bambang Irawan, Ak., CA.',
                'role_title'       => 'Pengawas APIP & Auditor',
                'organization'     => 'Inspektorat Provinsi Lampung',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-1-3.jpg',
                'rating'           => 5,
                'quote'            => 'BegawiPengadaan memfasilitasi audit kepatuhan secara objektif. Riwayat versi regulasi dan standar dokumen baku memudahkan kami memverifikasi kesesuaian prosedur dari tahap perencanaan sampai serah terima hasil.',
                'impact_statement' => 'Mitigasi deviasi hukum dan penguatan integritas pengadaan',
                'category'         => 'Auditor APIP',
                'sort_order'       => 4,
                'is_active'        => 1,
            ],
            [
                'name'             => 'dr. Nurul Hidayati, M.Kes.',
                'role_title'       => 'PPK Pengadaan Alkes',
                'organization'     => 'RSUD Dr. H. Abdul Moeloek',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-2-2.jpg',
                'rating'           => 5,
                'quote'            => 'Pengadaan obat dan alat medis memerlukan ketepatan waktu serta kepatuhan e-katalog nasional yang sangat ketat. Fitur konsultasi dan referensi regulasi di BegawiPengadaan memastikan ketersediaan logistik rumah sakit selalu terjamin.',
                'impact_statement' => 'Ketepatan pasokan medis 99.2% dengan e-katalog akurat',
                'category'         => 'PPK Rumah Sakit',
                'sort_order'       => 5,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Surya Darmawan, S.T.',
                'role_title'       => 'Tim Teknis & Pengawas',
                'organization'     => 'Dinas Perumahan & Kawasan Permukiman Lampung',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-2-3.jpg',
                'rating'           => 5,
                'quote'            => 'Standarisasi berita acara dan format pemeriksaan pekerjaan di Bunga Rampai mempermudah tim teknis melakukan serah terima fisik di lapangan secara presisi dan sesuai kontrak kerja.',
                'impact_statement' => '100% Berita Acara terverifikasi digital',
                'category'         => 'Tim Teknis',
                'sort_order'       => 6,
                'is_active'        => 1,
            ],
        ];

        $db->table('testimonials')->truncate();
        foreach ($testimonials as $t) {
            $t['created_at'] = date('Y-m-d H:i:s');
            $db->table('testimonials')->insert($t);
        }

        // 7. TEAM MEMBERS
        $team = [
            [
                'name'         => 'Dr. Ir. Fahrizal Darminto, M.A.',
                'role_title'   => 'Sekretaris Daerah Provinsi Lampung',
                'category'     => 'pembina',
                'bio'          => 'Pengarah strategis percepatan transformasi digital dan penguatan integritas tata kelola pengadaan barang dan jasa Pemerintah Provinsi Lampung.',
                'photo_url'    => 'assets/main/images/team/team-1-1.jpg',
                'social_links' => json_encode(['badge' => 'Pengarah Strategis']),
                'sort_order'   => 1,
                'is_active'    => 1,
            ],
            [
                'name'         => 'Slamet Riadi, S.Sos., M.M.',
                'role_title'   => 'Kepala Biro Pengadaan Barang & Jasa',
                'category'     => 'pembina',
                'bio'          => 'Inisiator ekosistem BegawiPengadaan, memimpin transformasi UKPBJ Lampung menuju Level 3 (Proaktif) dan pusat keunggulan PBJ.',
                'photo_url'    => 'assets/main/images/team/team-1-2.jpg',
                'social_links' => json_encode(['badge' => 'Penanggung Jawab']),
                'sort_order'   => 2,
                'is_active'    => 1,
            ],
            [
                'name'         => 'Software Engineering & UI/UX Crew',
                'role_title'   => 'Software Engineering & UX System',
                'category'     => 'pengembang',
                'bio'          => 'Merancang arsitektur MVC CodeIgniter 4 yang gesit, integrasi database MySQL teroptimasi, dan tata letak responsif modern WowDash.',
                'photo_url'    => 'assets/main/images/team/team-1-5.jpg',
                'social_links' => json_encode(['badge' => 'Tim Pengembang']),
                'sort_order'   => 3,
                'is_active'    => 1,
            ],
            [
                'name'         => 'Tim LPSE & Pengelola SPSE',
                'role_title'   => 'Infrastruktur & Layanan LPSE',
                'category'     => 'pengembang',
                'bio'          => 'Menjamin ketersediaan server 24/7 dengan standardisasi ISO 27001 dan keamanan siber pengadaan.',
                'photo_url'    => 'assets/main/images/team/team-1-4.jpg',
                'social_links' => json_encode(['badge' => 'Infrastruktur SPSE']),
                'sort_order'   => 4,
                'is_active'    => 1,
            ],
            [
                'name'         => 'Tim Pembinaan & Advokasi Hukum PBJ',
                'role_title'   => 'Advokasi & Regulasi PBJ',
                'category'     => 'narasumber',
                'bio'          => 'Harmonisasi peraturan, telaah hukum pengadaan, mitigasi sanggahan tender, dan penyusunan panduan teknis.',
                'photo_url'    => 'assets/main/images/team/team-1-3.jpg',
                'social_links' => json_encode(['badge' => 'Advokasi Hukum']),
                'sort_order'   => 5,
                'is_active'    => 1,
            ],
            [
                'name'         => 'Tim Fasilitator & Master Trainer PBJ',
                'role_title'   => 'Instruktur & Penjamin Mutu',
                'category'     => 'narasumber',
                'bio'          => 'Penyusun silabus pembelajaran LMS berbasis kompetensi LKPP dan bank soal sertifikasi level-1.',
                'photo_url'    => 'assets/main/images/team/team-1-6.jpg',
                'social_links' => json_encode(['badge' => 'Master Trainer']),
                'sort_order'   => 6,
                'is_active'    => 1,
            ],
        ];

        $db->table('team_members')->truncate();
        foreach ($team as $member) {
            $member['created_at'] = date('Y-m-d H:i:s');
            $db->table('team_members')->insert($member);
        }

        // 8. HELPDESK TOPICS
        $topics = [
            [
                'topic_title'     => 'Regulasi & Payung Hukum',
                'default_message' => 'Halo Helpdesk BegawiPengadaan, saya ingin berkonsultasi mengenai regulasi PBJ terbaru (Perpres/PerLKPP).',
                'icon_class'      => 'solar:document-text-bold',
                'sort_order'      => 1,
                'is_active'       => 1,
            ],
            [
                'topic_title'     => 'Arena Belajar & Kursus',
                'default_message' => 'Halo Helpdesk BegawiPengadaan, saya butuh informasi seputar kursus dan sertifikasi di Arena Belajar.',
                'icon_class'      => 'solar:graduation-cap-bold',
                'sort_order'      => 2,
                'is_active'       => 1,
            ],
            [
                'topic_title'     => 'Template Bunga Rampai',
                'default_message' => 'Halo Helpdesk BegawiPengadaan, saya ingin bertanya tentang template dokumen kerja di Bunga Rampai.',
                'icon_class'      => 'solar:folder-with-files-bold',
                'sort_order'      => 3,
                'is_active'       => 1,
            ],
            [
                'topic_title'     => 'Bantuan Akun & SPSE',
                'default_message' => 'Halo Helpdesk BegawiPengadaan, saya butuh bantuan teknis terkait akun atau portal LPSE/SPSE.',
                'icon_class'      => 'solar:shield-check-bold',
                'sort_order'      => 4,
                'is_active'       => 1,
            ],
        ];

        $db->table('helpdesk_topics')->truncate();
        foreach ($topics as $top) {
            $top['created_at'] = date('Y-m-d H:i:s');
            $db->table('helpdesk_topics')->insert($top);
        }

        // 9. SERVICE CATEGORIES
        $categories = [
            ['name' => 'Pengadaan Barang', 'code' => 'pengadaan-barang', 'description' => 'Komoditas perlengkapan kantor, mesin, bibit, alat kesehatan, dan sarana prasarana.', 'sort_order' => 1, 'is_active' => 1],
            ['name' => 'Pekerjaan Konstruksi', 'code' => 'pekerjaan-konstruksi', 'description' => 'Pembangunan gedung, infrastruktur jalan jembatan, irigasi, dan pemeliharaan fisik.', 'sort_order' => 2, 'is_active' => 1],
            ['name' => 'Jasa Konsultansi', 'code' => 'jasa-konsultansi', 'description' => 'Kajian teknis, perencanaan fisik, pengawasan konstruksi, dan studi kelayakan.', 'sort_order' => 3, 'is_active' => 1],
            ['name' => 'Jasa Lainnya', 'code' => 'jasa-lainnya', 'description' => 'Layanan kebersihan, keamanan gedung, event organizer, katering, dan sewa sarana.', 'sort_order' => 4, 'is_active' => 1],
            ['name' => 'E-Purchasing Katalog', 'code' => 'e-purchasing-katalog', 'description' => 'Belanja cepat via E-Katalog Nasional, E-Katalog Lokal Lampung, dan Toko Daring.', 'sort_order' => 5, 'is_active' => 1],
            ['name' => 'Swakelola PBJ', 'code' => 'swakelola-pbj', 'description' => 'Pelaksanaan pekerjaan swakelola Tipe I, II, III (Ormas), dan Tipe IV (Pokmas).', 'sort_order' => 6, 'is_active' => 1],
        ];

        $db->table('service_categories')->truncate();
        foreach ($categories as $cat) {
            $cat['created_at'] = date('Y-m-d H:i:s');
            $db->table('service_categories')->insert($cat);
        }

        // 10. SURVEY ASPECTS
        $aspects = [
            ['aspect_key' => 'kesesuaian_persyaratan', 'title' => 'Kesesuaian Persyaratan & Keterbukaan Informasi', 'description' => 'Kemudahan memahami regulasi dan persyaratan pengadaan yang dipublikasikan.', 'icon_class' => 'solar:document-text-bold', 'weight' => 25.00, 'sort_order' => 1, 'is_active' => 1],
            ['aspect_key' => 'kemudahan_sistem', 'title' => 'Kemudahan Akses & Keandalan Sistem Portal', 'description' => 'Kenyamanan navigasi, kecepatan load halaman, dan kemudahan download berkas kerja.', 'icon_class' => 'solar:server-square-bold', 'weight' => 25.00, 'sort_order' => 2, 'is_active' => 1],
            ['aspect_key' => 'kecepatan_layanan', 'title' => 'Kecepatan Respon & Solutifitas Helpdesk', 'description' => 'Ketanggapan tim Biro PBJ dalam menjawab konsultasi teknis melalui WhatsApp dan formulir.', 'icon_class' => 'solar:chat-round-dots-bold', 'weight' => 25.00, 'sort_order' => 3, 'is_active' => 1],
            ['aspect_key' => 'kualitas_materi', 'title' => 'Kualitas Materi Modul & Kebermanfaatan Dokumen', 'description' => 'Kelengkapan template Bunga Rampai dan kurikulum pada Arena Belajar.', 'icon_class' => 'solar:folder-with-files-bold', 'weight' => 25.00, 'sort_order' => 4, 'is_active' => 1],
        ];

        $db->table('survey_aspects')->truncate();
        foreach ($aspects as $asp) {
            $asp['created_at'] = date('Y-m-d H:i:s');
            $db->table('survey_aspects')->insert($asp);
        }

        // 11. PAGE BANNERS
        $banners = [
            [
                'page_key'               => 'about',
                'hero_badge'             => 'Biro Pengadaan Barang & Jasa Setda Provinsi Lampung',
                'hero_title'             => 'Membangun Ekosistem Pengadaan Digital <span>Berintegritas & Berdampak</span>',
                'hero_subtitle'          => 'BegawiPengadaan merupakan pusat inovasi, pembelajaran digital, regulasi hukum, dan intelijen pengadaan terpadu yang dirancang untuk mewujudkan tata kelola Pengadaan Barang/Jasa Pemerintah Provinsi Lampung yang transparan, profesional, dan akuntabel.',
                'cta_badge'              => 'Layanan Konsultasi & Pendampingan',
                'cta_title'              => 'Butuh Konsultasi atau Pendampingan PBJ?',
                'cta_lead'               => 'Tim Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung siap membantu konsultasi teknis, bimbingan penggunaan SPSE, dan peningkatan kompetensi pengadaan.',
                'cta_primary_btn_text'   => 'Hubungi Helpdesk PBJ',
                'cta_primary_btn_url'    => 'kontak',
                'cta_secondary_btn_text' => 'Cek Keabsahan Sertifikat',
                'cta_secondary_btn_url'  => 'verifikasi-sertifikat',
            ],
            [
                'page_key'               => 'learning',
                'hero_badge'             => 'Arena Belajar PBJ',
                'hero_title'             => 'Kembangkan Kompetensi Pengadaan <span>Bersama Ahli Terstandar LKPP</span>',
                'hero_subtitle'          => 'Akses modul silabus komprehensif, video pembelajaran interaktif, latihan soal berbasis studi kasus, dan dapatkan sertifikat resmi dari Pemerintah Provinsi Lampung.',
                'cta_badge'              => 'Sertifikasi Terakreditasi',
                'cta_title'              => 'Siap Tingkatkan Jenjang Karier Pengadaan Anda?',
                'cta_lead'               => 'Daftarkan diri Anda hari ini dan ikuti program sertifikasi kompetensi pengadaan terpadu.',
                'cta_primary_btn_text'   => 'Katalog Kursus',
                'cta_primary_btn_url'    => 'arena-belajar/course',
                'cta_secondary_btn_text' => 'Jadwal Agenda Bimtek',
                'cta_secondary_btn_url'  => 'event',
            ],
            [
                'page_key'               => 'knowledge',
                'hero_badge'             => 'Bunga Rampai PBJ',
                'hero_title'             => 'Repositori Dokumen Kerja, Kajian Ilmiah <span>& Praktik Baik Pengadaan</span>',
                'hero_subtitle'          => 'Unduh template KAK, HPS, rancangan kontrak, infografis alur tahapan kerja, serta studi kasus penanganan sanggahan pengadaan barang dan jasa.',
                'cta_badge'              => 'Partisipasi Komunitas',
                'cta_title'              => 'Mempunyai Best Practice Pengadaan untuk Dibagikan?',
                'cta_lead'               => 'Kirimkan karya ilmiah, format inovasi dokumen, atau telaah hukum Anda ke redaksi Bunga Rampai PBJ Lampung.',
                'cta_primary_btn_text'   => 'Hubungi Redaksi',
                'cta_primary_btn_url'    => 'kontak',
                'cta_secondary_btn_text' => 'Jelajahi Artikel',
                'cta_secondary_btn_url'  => 'bunga-rampai/artikel',
            ],
            [
                'page_key'               => 'regulation',
                'hero_badge'             => 'Pangkalan Data Regulasi',
                'hero_title'             => 'Pusat Dokumentasi Regulasi <span>Pengadaan Barang & Jasa Terpadu</span>',
                'hero_subtitle'          => 'Telusuri silsilah undang-undang, Perpres, Peraturan LKPP, Surat Edaran, hingga Peraturan Daerah Provinsi Lampung dengan fitur pencarian pintar.',
                'cta_badge'              => 'Klinik Regulasi & Advokasi',
                'cta_title'              => 'Butuh Pendampingan Hukum & Advokasi Kontrak?',
                'cta_lead'               => 'Bagian Pembinaan & Advokasi PBJ siap memberikan kajian telaah yuridis bagi seluruh OPD di lingkungan Pemprov Lampung.',
                'cta_primary_btn_text'   => 'Konsultasi Hukum PBJ',
                'cta_primary_btn_url'    => 'kontak',
                'cta_secondary_btn_text' => 'FAQ Regulasi',
                'cta_secondary_btn_url'  => 'bunga-rampai/faq',
            ],
            [
                'page_key'               => 'news',
                'hero_badge'             => 'Warta & Siaran Pers',
                'hero_title'             => 'Kabar Terkini & Informasi Strategis <span>Ekosistem Pengadaan Lampung</span>',
                'hero_subtitle'          => 'Update berita terbaru seputar kebijakan pengadaan, capaian E-Katalog Lokal, kegiatan bimtek, dan siaran pers resmi Biro PBJ Pemprov Lampung.',
                'cta_badge'              => 'Pembaruan Berkelanjutan',
                'cta_title'              => 'Dapatkan Pembaruan Informasi Pengadaan Setiap Pekan',
                'cta_lead'               => 'Berlangganan newsletter BegawiPengadaan agar tidak ketinggalan jadwal tender dan regulasi terkini.',
                'cta_primary_btn_text'   => 'Lihat Agenda Bimtek',
                'cta_primary_btn_url'    => 'event',
                'cta_secondary_btn_text' => 'Pusat Regulasi',
                'cta_secondary_btn_url'  => 'regulasi',
            ],
            [
                'page_key'               => 'events',
                'hero_badge'             => 'Agenda & Workshop',
                'hero_title'             => 'Jadwal Bimbingan Teknis, Seminar <span>& Uji Kompetensi PBJ</span>',
                'hero_subtitle'          => 'Ikuti agenda pelatihan langsung tatap muka (luring) maupun daring bersama narasumber terakreditasi LKPP dan praktisi berpengalaman.',
                'cta_badge'              => 'Pelatihan Kustom OPD',
                'cta_title'              => 'Ingin Mengadakan Pelatihan Khusus OPD Anda?',
                'cta_lead'               => 'Biro PBJ Setda Prov. Lampung memfasilitasi in-house training dan pendampingan e-katalog sesuai kebutuhan dinas.',
                'cta_primary_btn_text'   => 'Ajukan Pelatihan',
                'cta_primary_btn_url'    => 'kontak',
                'cta_secondary_btn_text' => 'Arena Belajar Online',
                'cta_secondary_btn_url'  => 'arena-belajar',
            ],
            [
                'page_key'               => 'statistics',
                'hero_badge'             => 'Transparansi & Intelijen Data',
                'hero_title'             => 'Dasbor Statistik & Metrik Kinerja <span>Pengadaan Publik Terbuka</span>',
                'hero_subtitle'          => 'Visualisasi data real-time capaian pengadaan, indeks kepuasan pengguna, distribusi sertifikasi aparatur, dan trafik pemanfaatan portal.',
                'cta_badge'              => 'Survei Kepuasan',
                'cta_title'              => 'Dukung Peningkatan Kualitas Layanan Kami',
                'cta_lead'               => 'Berikan masukan dan penilaian Anda melalui Survei Kepuasan Masyarakat (IKM) secara berkala.',
                'cta_primary_btn_text'   => 'Isi Survei Kepuasan',
                'cta_primary_btn_url'    => 'survei-kepuasan',
                'cta_secondary_btn_text' => 'Katalog Regulasi',
                'cta_secondary_btn_url'  => 'regulasi',
            ],
            [
                'page_key'               => 'certificate_verify',
                'hero_badge'             => 'Keaslian Dokumen Digital',
                'hero_title'             => 'Verifikasi Keabsahan Sertifikat <span>Pelatihan & Uji Kompetensi PBJ</span>',
                'hero_subtitle'          => 'Sistem validasi instan berbasis kode identifikasi unik dan hash SHA-256 untuk memastikan keaslian sertifikat yang diterbitkan Biro PBJ Lampung.',
                'cta_badge'              => 'Kembangkan Portofolio',
                'cta_title'              => 'Belum Mengikuti Sertifikasi Pengadaan?',
                'cta_lead'               => 'Tingkatkan kualifikasi profesi Anda dengan mendaftar di Arena Belajar atau mengikuti agenda bimtek resmi.',
                'cta_primary_btn_text'   => 'Daftar Kursus Sekarang',
                'cta_primary_btn_url'    => 'arena-belajar',
                'cta_secondary_btn_text' => 'Jadwal Ujian Sertifikasi',
                'cta_secondary_btn_url'  => 'event',
            ],
            [
                'page_key'               => 'contact',
                'hero_badge'             => 'Layanan Terpadu Satu Pintu',
                'hero_title'             => 'Hubungi Biro Pengadaan Barang & Jasa <span>Setda Provinsi Lampung</span>',
                'hero_subtitle'          => 'Sampaikan pertanyaan, permintaan pendampingan teknis, konsultasi regulasi, atau pengaduan layanan secara cepat melalui kanal resmi kami.',
                'cta_badge'              => 'Respon Instan',
                'cta_title'              => 'Butuh Respon Cepat dari Tim Helpdesk?',
                'cta_lead'               => 'Hubungi layanan konsultasi instan melalui WhatsApp resmi helpdesk kami pada hari dan jam kerja.',
                'cta_primary_btn_text'   => 'Chat WhatsApp Langsung',
                'cta_primary_btn_url'    => 'https://wa.me/6285117779783',
                'cta_secondary_btn_text' => 'Koleksi FAQ PBJ',
                'cta_secondary_btn_url'  => 'bunga-rampai/faq',
            ],
            [
                'page_key'               => 'survey',
                'hero_badge'             => 'Indeks Kepuasan Masyarakat (IKM)',
                'hero_title'             => 'Survei Kepuasan Layanan <span>Pengadaan Barang & Jasa</span>',
                'hero_subtitle'          => 'Partisipasi dan penilaian jujur Anda sangat berharga dalam mewujudkan transformasi layanan pengadaan yang bersih, responsif, dan profesional.',
                'cta_badge'              => 'Apresiasi & Komitmen',
                'cta_title'              => 'Terima Kasih Atas Partisipasi Anda',
                'cta_lead'               => 'Penilaian Anda membantu kami terus berbenah memberikan pelayanan prima bagi seluruh masyarakat Lampung.',
                'cta_primary_btn_text'   => 'Lihat Hasil Indeks IKM',
                'cta_primary_btn_url'    => 'statistik',
                'cta_secondary_btn_text' => 'Kembali ke Beranda',
                'cta_secondary_btn_url'  => '/',
            ],
        ];

        $db->table('page_banners')->truncate();
        foreach ($banners as $banner) {
            $banner['created_at'] = date('Y-m-d H:i:s');
            $db->table('page_banners')->insert($banner);
        }
    }
}

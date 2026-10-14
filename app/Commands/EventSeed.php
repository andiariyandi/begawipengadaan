<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class EventSeed extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'event:seed';
    protected $description = 'Seed authentic Indonesian PBJ events, workshops, webinars, and training agendas for BegawiPBJ.';
    protected $usage       = 'event:seed';

    public function run(array $params): int
    {
        $db = db_connect();
        CLI::write('Seeding authentic Event & Agenda PBJ data...', 'yellow');

        $now = date('Y-m-d H:i:s');

        // Fetch creator user
        $creator = $db->table('users')->where('email', 'admin@begawipbj.lampungprov.go.id')->get()->getRowArray()
            ?? $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        $creatorId = $creator ? (int) $creator['id'] : null;

        $events = [
            [
                'title'                 => 'Webinar Nasional: Akselerasi E-Katalog Versi 6 & Peluang Belanja Pengadaan bagi UMKM Lokal',
                'slug'                  => 'webinar-akselerasi-e-katalog-v6-umkm-lokal',
                'type'                  => 'webinar',
                'mode'                  => 'online',
                'status'                => 'open_registration',
                'visibility'            => 'public',
                'summary'               => 'Sosialisasi teknis pendaftaran etalase produk lokal, verifikasi instan KSWP/OSS, dan strategi transaksi e-purchasing APBD Provinsi Lampung tahun 2026.',
                'description'           => "Pemerintah Provinsi Lampung melalui Biro Pengadaan Barang dan Jasa Sekretariat Daerah menyelenggarakan Webinar Nasional dengan fokus percepatan adopsi Katalog Elektronik Versi 6 (E-Katalog V6).\n\nKegiatan ini ditujukan bagi Pejabat Pembuat Komitmen (PPK), Pejabat Pengadaan (PP), serta pelaku usaha mikro, kecil, dan menengah (UMKM) serta Koperasi di seluruh Kabupaten/Kota se-Lampung.\n\nNarasumber:\n1. Direktur Pengembangan Sistem Katalog LKPP RI\n2. Kepala Biro PBJ Setda Provinsi Lampung\n3. Tenaga Ahli Pengadaan Digital LKPP\n\nMateri Pokok:\n• Pemahaman fitur terbaru E-Katalog V6 (pencarian komoditas, multi-vendor cart, pembayaran KKPD).\n• Tata cara onboarding dan upload katalog produk lokal tanpa biaya retribusi.\n• Kebijakan afirmasi minimal 40% alokasi anggaran APBD untuk Produk Dalam Negeri (PDN).\n• Sesi live demo transaksi dan tanya jawab interaktif.",
                'start_at'              => '2026-10-28 09:00:00',
                'end_at'                => '2026-10-28 12:30:00',
                'location'              => 'Daring via Zoom Cloud Meetings & Live YouTube BegawiPBJ',
                'meeting_url'           => 'https://zoom.us/j/85117779783?pwd=BegawiPBJ2026',
                'registration_open_at'  => '2026-10-14 00:00:00',
                'registration_close_at' => '2026-10-27 23:59:00',
                'capacity'              => 500,
                'waitlist_enabled'      => 1,
                'certificate_enabled'   => 1,
            ],
            [
                'title'                 => 'Bimbingan Teknis Intensif: Penyusunan Dokumen Pemilihan & Mitigasi Risiko Sanggah Jasa Konstruksi',
                'slug'                  => 'bimtek-penyusunan-dokumen-pemilihan-mitigasi-sanggah-konstruksi',
                'type'                  => 'bimtek',
                'mode'                  => 'offline',
                'status'                => 'open_registration',
                'visibility'            => 'public',
                'summary'               => 'Pelatihan tatap muka intensif bagi Pokja Pemilihan UKPBJ se-Lampung dalam menelaah kualifikasi penyedia, evaluasi kewajaran harga, dan penyusunan tanggapan sanggah konstruksi.',
                'description'           => "Bimtek tatap muka selama 3 hari diselenggarakan di Ballroom Hotel Novotel Bandar Lampung, mempertemukan personil Pokja Pemilihan dari 15 Kabupaten/Kota.\n\nFasilitator:\n• Fasilitator Senior Pengadaan LKPP RI (TOC Certified)\n• Tim Auditor Investigasi Inspektorat Daerah Provinsi Lampung\n\nPokok Bahasan:\n1. Reviu dokumen persiapan pengadaan (HPS, spesifikasi teknis, rancangan kontrak konstruksi).\n2. Teknik evaluasi dokumen penawaran kualifikasi teknis dan analisis harga satuan pekerjaan (AHSP).\n3. Tata cara penyusunan tanggapan sanggah dan sanggah banding yang akuntabel dan berbasis data.\n4. Simulasi kasus penanganan indikasi persekongkolan tender (bid rigging).\n\nFasilitas: Sertifikat Bimtek 24 JP, Modul Pelatihan, Konsumsi & Akomodasi selama pelatihan.",
                'start_at'              => '2026-11-04 08:30:00',
                'end_at'                => '2026-11-06 17:00:00',
                'location'              => 'Ballroom Hotel Novotel Bandar Lampung, Jl. Gatot Subroto No. 136',
                'meeting_url'           => null,
                'registration_open_at'  => '2026-10-14 00:00:00',
                'registration_close_at' => '2026-11-02 18:00:00',
                'capacity'              => 80,
                'waitlist_enabled'      => 1,
                'certificate_enabled'   => 1,
            ],
            [
                'title'                 => 'Rapat Koordinasi PBJ se-Lampung: Evaluasi Realisasi Serapan RUP & Percepatan Kontrak Triwulan IV',
                'slug'                  => 'rakor-pbj-evaluasi-serapan-rup-kontrak-triwulan-iv',
                'type'                  => 'rakor',
                'mode'                  => 'hybrid',
                'status'                => 'open_registration',
                'visibility'            => 'public',
                'summary'               => 'Rapat koordinasi strategis yang dipimpin langsung oleh Sekretaris Daerah Provinsi Lampung bersama seluruh Kepala OPD, PPK, dan UKPBJ Kabupaten/Kota.',
                'description'           => "Rapat Koordinasi Triwulanan Pengadaan Barang dan Jasa Provinsi Lampung dilaksanakan secara Hybrid untuk mengevaluasi percepatan realisasi fisik dan keuangan anggaran APBD 2026.\n\nAgenda Rapat:\n• Paparan Laporan Kinerja Pengadaan Triwulan III oleh Kepala Biro PBJ Setda Lampung.\n• Evaluasi kepatuhan penginputan SiRUP dan realisasi transaksi P3DN.\n• Arahan Sekretaris Daerah Provinsi Lampung mengenai percepatan kontrak fisik sebelum akhir tahun anggaran.\n• Diskusi kendala teknis pelaksanaan kontrak dan mitigasi keterlambatan serah terima (PHO).\n\nUndangan resmi berlaku bagi Pengguna Anggaran (PA), Kuasa Pengguna Anggaran (KPA), dan PPK seluruh dinas instansi.",
                'start_at'              => '2026-11-12 09:00:00',
                'end_at'                => '2026-11-12 15:30:00',
                'location'              => 'Gedung Pusiban Kompleks Kantor Gubernur Lampung (Luring) & Ruang Virtual Zoom (Daring)',
                'meeting_url'           => 'https://zoom.us/j/85117779783?pwd=RakorPBJ2026',
                'registration_open_at'  => '2026-10-14 00:00:00',
                'registration_close_at' => '2026-11-10 17:00:00',
                'capacity'              => 200,
                'waitlist_enabled'      => 1,
                'certificate_enabled'   => 1,
            ],
            [
                'title'                 => 'Workshop Probity Audit Pengadaan Strategis: Kolaborasi Biro PBJ bersama BPKP Perwakilan Lampung',
                'slug'                  => 'workshop-probity-audit-pengadaan-strategis-bpkp-lampung',
                'type'                  => 'workshop',
                'mode'                  => 'offline',
                'status'                => 'open_registration',
                'visibility'            => 'public',
                'summary'               => 'Peningkatan kapabilitas Pejabat Pembuat Komitmen dan APIP Daerah dalam pengawalan paket pekerjaan infrastruktur prioritas bebas fraud dan temuan berulang.',
                'description'           => "Workshop teknis implementasi Probity Audit pada proyek-proyek strategis daerah di Provinsi Lampung, menghadirkan narasumber dari Badan Pengawasan Keuangan dan Pembangunan (BPKP) Perwakilan Provinsi Lampung.\n\nFokus Pembelajaran:\n1. Ruang lingkup dan metodologi probity audit pada tahap perencanaan, pemilihan, hingga pelaksanaan kontrak.\n2. Identifikasi titik rawan (red flags) penyimpangan pengadaan barang/jasa.\n3. Peran APIP sebagai early warning system dalam mencegah sengketa hukum dan tindak pidana korupsi.\n4. Penyusunan laporan probity audit yang konstruktif dan solutif.",
                'start_at'              => '2026-11-18 08:30:00',
                'end_at'                => '2026-11-19 16:30:00',
                'location'              => 'Aula BPSDM Provinsi Lampung, Jl. Raya Hajimena No. 10, Natar',
                'meeting_url'           => null,
                'registration_open_at'  => '2026-10-14 00:00:00',
                'registration_close_at' => '2026-11-16 12:00:00',
                'capacity'              => 60,
                'waitlist_enabled'      => 1,
                'certificate_enabled'   => 1,
            ],
            [
                'title'                 => 'Klinik Konsultasi & Pendampingan E-Purchasing Katalog Lokal bagi Pelaku Usaha Mikro & Kecil',
                'slug'                  => 'klinik-konsultasi-pendampingan-e-purchasing-umkm',
                'type'                  => 'klinik',
                'mode'                  => 'offline',
                'status'                => 'open_registration',
                'visibility'            => 'public',
                'summary'               => 'Layanan tatap muka meja konsultasi langsung untuk pembuatan akun LPSE, pendaftaran sertifikat standar, dan penayangan etalase produk makan minum & ATK.',
                'description'           => "Biro PBJ Setda Provinsi Lampung membuka loket Klinik Konsultasi bagi para pelaku usaha lokal (UMKM & Koperasi) di Provinsi Lampung.\n\nLayanan Klinik Meliputi:\n• Pendampingan verifikasi akun SPSE dan integrasi data OSS RBA.\n• Bimbingan input produk pada etalase makanan-minuman, alat tulis kantor, seragam kerja, dan jasa sewa kendaraan dinas.\n• Konsultasi penetapan harga penawaran bersaing dan syarat kualifikasi teknis.\n\nKegiatan ini bebas biaya dan dibuka setiap hari kerja selama masa klinik berlangsung.",
                'start_at'              => '2026-11-24 09:00:00',
                'end_at'                => '2026-11-25 15:00:00',
                'location'              => 'Ruang Helpdesk LPSE Biro PBJ Setda Provinsi Lampung, Lantai 1 Gedung Kantor Gubernur Lampung',
                'meeting_url'           => null,
                'registration_open_at'  => '2026-10-14 00:00:00',
                'registration_close_at' => '2026-11-23 17:00:00',
                'capacity'              => 100,
                'waitlist_enabled'      => 1,
                'certificate_enabled'   => 0,
            ],
            [
                'title'                 => 'Sosialisasi Pencegahan Benturan Kepentingan & Penerapan Pakta Integritas Elektronik PBJ',
                'slug'                  => 'sosialisasi-pencegahan-benturan-kepentingan-pakta-integritas-pbj',
                'type'                  => 'sosialisasi',
                'mode'                  => 'online',
                'status'                => 'open_registration',
                'visibility'            => 'public',
                'summary'               => 'Webinar sosialisasi Surat Edaran pencegahan fraud, larangan gratifikasi, serta integrasi pakta integritas digital pada sistem BegawiPBJ.',
                'description'           => "Sosialisasi daring bagi seluruh aparatur pengadaan di lingkungan Pemerintah Provinsi dan Pemerintah Kabupaten/Kota se-Lampung.\n\nNarasumber:\n• Tim Satgassus Pencegahan Korupsi Mabes Polri / KPK RI\n• Inspektur Daerah Provinsi Lampung\n• Kepala Bagian Pembinaan & Advokasi PBJ\n\nFasilitas: E-Sertifikat resmi dan materi presentasi digital.",
                'start_at'              => '2026-12-02 09:00:00',
                'end_at'                => '2026-12-02 12:00:00',
                'location'              => 'Daring via Zoom Cloud Meetings',
                'meeting_url'           => 'https://zoom.us/j/85117779783?pwd=IntegritasPBJ2026',
                'registration_open_at'  => '2026-10-14 00:00:00',
                'registration_close_at' => '2026-12-01 23:59:00',
                'capacity'              => 1000,
                'waitlist_enabled'      => 0,
                'certificate_enabled'   => 1,
            ],
            [
                'title'                 => 'Pelatihan Teknis Pengelolaan Kontrak Konstruksi Berstandar FIDIC & Mitigasi Risiko Keterlambatan',
                'slug'                  => 'pelatihan-pengelolaan-kontrak-konstruksi-fidic-mitigasi-keterlambatan',
                'type'                  => 'bimtek',
                'mode'                  => 'offline',
                'status'                => 'coming_soon',
                'visibility'            => 'public',
                'summary'               => 'Pelatihan terstruktur bagi Pejabat Pembuat Komitmen (PPK) bidang kebinamargaan, cipta karya, dan pengairan mengenai klausul addendum, denda keterlambatan, dan pemutusan kontrak.',
                'description'           => "Pelatihan khusus bagi PPK teknis dan pengawas lapangan dalam mengawal pelaksanaan kontrak konstruksi bernilai strategis di Provinsi Lampung.\n\nInstruktur:\n• Ahli Kontrak Pengadaan Konstruksi Nasional (Ahli Kontrak PBJ Level Utama)\n• Praktisi Hukum Konstruksi dan Arbitrase Lembaga BAPEK\n\nPendaftaran dibuka awal Desember 2026.",
                'start_at'              => '2026-12-10 08:30:00',
                'end_at'                => '2026-12-11 16:30:00',
                'location'              => 'Pusat Diklat BPSDM Provinsi Lampung',
                'meeting_url'           => null,
                'registration_open_at'  => '2026-11-20 00:00:00',
                'registration_close_at' => '2026-12-08 17:00:00',
                'capacity'              => 50,
                'waitlist_enabled'      => 1,
                'certificate_enabled'   => 1,
            ],
            [
                'title'                 => 'Webinar Evaluasi Kewajaran Harga & Analisis Standar Biaya Masukan (SBM) PBJ Tahun 2027',
                'slug'                  => 'webinar-evaluasi-kewajaran-harga-analisis-sbm-pbj-2027',
                'type'                  => 'webinar',
                'mode'                  => 'online',
                'status'                => 'coming_soon',
                'visibility'            => 'public',
                'summary'               => 'Pembahasan komprehensif pedoman penyusunan HPS dan evaluasi penawaran di bawah 80 persen nilai pagu anggaran sesuai Peraturan LKPP.',
                'description'           => "Webinar edukasi akhir tahun menyambut penyusunan perencanaan pengadaan tahun anggaran 2027.\n\nNarasumber dari Direktorat Pengembangan Kebijakan Pengadaan Umum LKPP RI.",
                'start_at'              => '2026-12-17 09:00:00',
                'end_at'                => '2026-12-17 12:00:00',
                'location'              => 'Daring via Zoom Cloud Meetings',
                'meeting_url'           => 'https://zoom.us/j/85117779783?pwd=SBM2027PBJ',
                'registration_open_at'  => '2026-12-01 00:00:00',
                'registration_close_at' => '2026-12-16 18:00:00',
                'capacity'              => 500,
                'waitlist_enabled'      => 0,
                'certificate_enabled'   => 1,
            ],
        ];

        $inserted = 0;
        $updated  = 0;

        foreach ($events as $evt) {
            $existing = $db->table('events')->where('slug', $evt['slug'])->get()->getRowArray();
            $data = [
                'title'                 => $evt['title'],
                'slug'                  => $evt['slug'],
                'type'                  => $evt['type'],
                'mode'                  => $evt['mode'],
                'status'                => $evt['status'],
                'visibility'            => $evt['visibility'],
                'summary'               => $evt['summary'],
                'description'           => $evt['description'],
                'start_at'              => $evt['start_at'],
                'end_at'                => $evt['end_at'],
                'location'              => $evt['location'],
                'meeting_url'           => $evt['meeting_url'],
                'registration_open_at'  => $evt['registration_open_at'],
                'registration_close_at' => $evt['registration_close_at'],
                'capacity'              => $evt['capacity'],
                'waitlist_enabled'      => $evt['waitlist_enabled'],
                'certificate_enabled'   => $evt['certificate_enabled'],
                'created_by'            => $creatorId,
                'updated_at'            => $now,
            ];

            if ($existing) {
                $db->table('events')->where('id', $existing['id'])->update($data);
                $updated++;
            } else {
                $data['created_at'] = $now;
                $db->table('events')->insert($data);
                $inserted++;
            }
        }

        CLI::write("✓ Finished event seeding: {$inserted} inserted, {$updated} updated.", 'green');
        return 0;
    }
}

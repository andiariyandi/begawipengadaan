<!-- Universal Spotlight & Command Palette Modal for Admin Dashboard (Ctrl + K) -->
<div class="modal fade" id="adminSpotlightModal" tabindex="-1" aria-labelledby="adminSpotlightModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-16 bg-base border border-neutral-200 shadow-xl overflow-hidden">
            
            <!-- Spotlight Input Box -->
            <div class="p-16 border-bottom border-neutral-200 d-flex align-items-center gap-3 bg-base">
                <span class="text-primary-600 d-flex align-items-center ms-4">
                    <iconify-icon icon="ion:search-outline" class="text-2xl"></iconify-icon>
                </span>
                <input type="text" 
                       id="adminSpotlightInput" 
                       class="form-control border-0 shadow-none px-0 text-md fw-medium bg-transparent text-primary-light" 
                       placeholder="Ketik nama menu, fitur, aksi cepat, atau halaman (e.g. regulasi, kelas, user)..." 
                       autocomplete="off">
                <button type="button" class="btn btn-sm btn-outline-neutral-300 rounded-circle text-secondary-light d-flex justify-content-center align-items-center p-0" id="adminSpotlightClearBtn" style="width: 28px; height: 28px; display: none;">
                    <iconify-icon icon="radix-icons:cross-2" class="text-sm"></iconify-icon>
                </button>
                <kbd class="badge bg-neutral-100 text-neutral-600 border border-neutral-200 px-8 py-4 radius-6 text-xs font-monospace d-none d-sm-inline">ESC</kbd>
            </div>

            <!-- Spotlight Results Body -->
            <div class="modal-body p-0" id="adminSpotlightResults" style="max-height: 460px; overflow-y: auto;">
                <div class="p-24 text-center text-secondary-light" id="adminSpotlightInitial">
                    <div class="w-48-px h-48-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center mx-auto mb-12">
                        <iconify-icon icon="solar:compass-bold" class="text-2xl"></iconify-icon>
                    </div>
                    <h6 class="text-md fw-semibold text-primary-light mb-4">Pencarian Navigasi & Aksi Cepat</h6>
                    <p class="text-xs text-secondary-light mb-0 mx-auto" style="max-width: 440px;">
                        Ketik kata kunci untuk melompat langsung ke 90+ halaman dashboard, kelola konten, unduh laporan, atau eksekusi aksi tanpa harus mencari di sidebar.
                    </p>
                </div>
                
                <div id="adminSpotlightList" class="p-12 d-flex flex-column gap-1"></div>
                
                <div id="adminSpotlightEmpty" class="p-32 text-center text-secondary-light d-none">
                    <iconify-icon icon="solar:folder-error-outline" class="text-4xl text-neutral-400 mb-8"></iconify-icon>
                    <h6 class="text-sm fw-medium text-secondary-light mb-0">Tidak ada menu atau aksi yang sesuai.</h6>
                    <p class="text-xs text-secondary-light mt-4 mb-0">Coba kata kunci lain seperti <em>user</em>, <em>kelas</em>, <em>sertifikat</em>, atau <em>pengaturan</em>.</p>
                </div>
            </div>

            <!-- Spotlight Keyboard Shortcuts Footer -->
            <div class="p-12 px-16 bg-neutral-50 border-top border-neutral-200 d-flex justify-content-between align-items-center text-secondary-light text-xs">
                <div class="d-flex align-items-center gap-2">
                    <span><kbd class="badge bg-base border border-neutral-200 text-neutral-600 px-6 py-2 font-monospace">↑</kbd> <kbd class="badge bg-base border border-neutral-200 text-neutral-600 px-6 py-2 font-monospace">↓</kbd> Pilih</span>
                    <span class="text-neutral-400">&middot;</span>
                    <span><kbd class="badge bg-base border border-neutral-200 text-neutral-600 px-6 py-2 font-monospace">↵</kbd> Buka</span>
                    <span class="text-neutral-400">&middot;</span>
                    <span><kbd class="badge bg-base border border-neutral-200 text-neutral-600 px-6 py-2 font-monospace">ESC</kbd> Tutup</span>
                </div>
                <div class="d-flex align-items-center gap-1 text-primary-600 fw-semibold text-2xs">
                    <iconify-icon icon="solar:bolt-bold" class="text-warning-600 text-sm"></iconify-icon>
                    <span>Begawi Admin Spotlight</span>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const spotlightModalEl = document.getElementById('adminSpotlightModal');
    if (!spotlightModalEl) return;

    let spotlightBsModal = null;
    const inputEl = document.getElementById('adminSpotlightInput');
    const clearBtnEl = document.getElementById('adminSpotlightClearBtn');
    const initialEl = document.getElementById('adminSpotlightInitial');
    const listEl = document.getElementById('adminSpotlightList');
    const emptyEl = document.getElementById('adminSpotlightEmpty');

    let selectedIndex = -1;
    let filteredItems = [];

    // Master Catalog of 90+ Dashboard Menus & Quick Actions
    const adminCatalog = [
        // Aksi Cepat (Quick Actions)
        { title: 'Tambah Pengguna Baru', category: 'Aksi Cepat', icon: 'solar:user-plus-bold', url: '<?= site_url('dashboard/akses/pengguna/tambah') ?>', desc: 'Daftarkan akun pengguna ASN, Penyedia, atau Pegawai' },
        { title: 'Buat Kelas Pembelajaran Baru', category: 'Aksi Cepat', icon: 'solar:book-2-bold', url: '<?= site_url('dashboard/pembelajaran/kelas/tambah') ?>', desc: 'Buka angkatan / batch kelas pelatihan PBJ' },
        { title: 'Buat Event & Agenda Baru', category: 'Aksi Cepat', icon: 'solar:calendar-add-bold', url: '<?= site_url('dashboard/komunikasi/event-agenda/tambah') ?>', desc: 'Rancang bimtek, workshop, atau sosialisasi PBJ' },
        { title: 'Unggah Dokumen Regulasi', category: 'Aksi Cepat', icon: 'solar:document-add-bold', url: '<?= site_url('dashboard/regulasi/dokumen/tambah') ?>', desc: 'Publikasi Perpres, Perlem, atau SK PBJ Lampung' },
        { title: 'Buat Popup Pengumuman', category: 'Aksi Cepat', icon: 'solar:bell-bing-bold', url: '<?= site_url('dashboard/landing/pengumuman') ?>', desc: 'Atur pesan pengumuman modal di halaman publik' },
        { title: 'Buka Ruang Kerja (Workspace)', category: 'Aplikasi', icon: 'solar:laptop-minimalistic-bold', url: '<?= site_url('workspace') ?>', desc: 'Beralih ke antarmuka ruang belajar/kerja operasional' },
        { title: 'Kunjungi Portal Publik', category: 'Aplikasi', icon: 'solar:globe-bold', url: '<?= site_url('/') ?>', desc: 'Buka halaman depan landing page BegawiPengadaan' },

        // Utama
        { title: 'Ringkasan Dashboard', category: 'Utama', icon: 'solar:home-smile-angle-outline', url: '<?= site_url('dashboard') ?>', desc: 'Beranda utama, KPI platform, dan grafik bulanan' },
        { title: 'KPI Internal', category: 'Utama', icon: 'solar:chart-2-outline', url: '<?= site_url('dashboard/kpi-internal') ?>', desc: 'Metrik indikator kinerja internal Biro PBJ' },
        { title: 'Antrean Persetujuan Ringkasan', category: 'Utama', icon: 'solar:clock-circle-outline', url: '<?= site_url('dashboard/antrean-persetujuan') ?>', desc: 'Pemantauan antrean approval berkas dan akun' },
        { title: 'Aktivitas Terbaru', category: 'Utama', icon: 'solar:history-2-outline', url: '<?= site_url('dashboard/aktivitas-terbaru') ?>', desc: 'Log aktivitas pengguna dan perubahan data terkini' },
        { title: 'Indikator Sistem', category: 'Utama', icon: 'solar:server-minimalistic-outline', url: '<?= site_url('dashboard/indikator-sistem') ?>', desc: 'Distribusi profil, tipe akun, dan status sistem' },

        // Kelola Landing Page
        { title: 'Pusat Carousel & Sorotan', category: 'Landing Page', icon: 'solar:star-fall-outline', url: '<?= site_url('dashboard/landing/sorotan-carousel') ?>', desc: 'Manajemen konten unggulan slider hero 5 modul' },
        { title: 'Slider & Hero Beranda', category: 'Landing Page', icon: 'solar:slider-vertical-outline', url: '<?= site_url('dashboard/landing/slider') ?>', desc: 'Banner slider utama halaman depan' },
        { title: 'Popup Pengumuman', category: 'Landing Page', icon: 'solar:bell-bing-outline', url: '<?= site_url('dashboard/landing/pengumuman') ?>', desc: 'Konfigurasi popup pengumuman publik' },
        { title: 'Profil & Nilai PATIK', category: 'Landing Page', icon: 'solar:buildings-outline', url: '<?= site_url('dashboard/landing/profil-lembaga') ?>', desc: 'Visi, misi, dan pilar kebudayaan kerja PBJ' },
        { title: 'Pilar & Hierarki Hukum', category: 'Landing Page', icon: 'solar:diploma-outline', url: '<?= site_url('dashboard/landing/pilar-hierarki') ?>', desc: 'Hierarki hukum dan landasan pengadaan daerah' },
        { title: 'Portal Eksternal', category: 'Landing Page', icon: 'solar:link-circle-outline', url: '<?= site_url('dashboard/landing/portal-eksternal') ?>', desc: 'Tautan LKPP, Sirup, SPSE, dan E-Katalog' },
        { title: 'Testimoni Pengguna', category: 'Landing Page', icon: 'solar:chat-line-outline', url: '<?= site_url('dashboard/landing/testimoni') ?>', desc: 'Ulasan dan masukan peserta pembelajaran' },
        { title: 'Tim Inovator', category: 'Landing Page', icon: 'solar:users-group-rounded-outline', url: '<?= site_url('dashboard/landing/tim') ?>', desc: 'Daftar pengembang dan tim pengelola platform' },
        { title: 'Banner Halaman Publik', category: 'Landing Page', icon: 'solar:gallery-wide-outline', url: '<?= site_url('dashboard/landing/banner-halaman') ?>', desc: 'Banner header pada sub-halaman publik' },
        { title: 'Kategori Layanan', category: 'Landing Page', icon: 'solar:layers-outline', url: '<?= site_url('dashboard/landing/kategori-layanan') ?>', desc: 'Kelompok layanan konsultasi dan advokasi' },

        // Manajemen Akses
        { title: 'Daftar Pengguna', category: 'Manajemen Akses', icon: 'solar:users-group-two-rounded-outline', url: '<?= site_url('dashboard/akses/pengguna') ?>', desc: 'Kelola akun ASN, Penyedia, Admin, dan Trainer' },
        { title: 'Verifikasi Profil', category: 'Manajemen Akses', icon: 'solar:user-check-rounded-outline', url: '<?= site_url('dashboard/akses/verifikasi-profil') ?>', desc: 'Tinjau permohonan aktivasi akun baru' },
        { title: 'Perubahan Profil', category: 'Manajemen Akses', icon: 'solar:user-id-outline', url: '<?= site_url('dashboard/akses/permohonan-perubahan-profil') ?>', desc: 'Verifikasi perubahan instansi, NIP, atau identitas' },
        { title: 'Role dan Permission', category: 'Manajemen Akses', icon: 'solar:shield-check-outline', url: '<?= site_url('dashboard/akses/role') ?>', desc: 'Hak akses peran Super Admin, Operator, dll' },
        { title: 'Permohonan Role', category: 'Manajemen Akses', icon: 'solar:key-outline', url: '<?= site_url('dashboard/akses/permohonan-role') ?>', desc: 'Pengajuan hak akses peran tambahan' },
        { title: 'Unit Organisasi (OPD)', category: 'Manajemen Akses', icon: 'solar:buildings-2-outline', url: '<?= site_url('dashboard/akses/unit-organisasi') ?>', desc: 'Hierarki OPD, biro, dinas, dan badan Pemprov' },
        { title: 'Peran Pengadaan', category: 'Manajemen Akses', icon: 'solar:diploma-verified-outline', url: '<?= site_url('dashboard/akses/peran-pengadaan') ?>', desc: 'Klasifikasi PA, KPA, PPK, Pejabat Pengadaan, Pokja' },

        // Arena Belajar
        { title: 'Overview Pembelajaran', category: 'Arena Belajar', icon: 'solar:book-2-outline', url: '<?= site_url('dashboard/pembelajaran') ?>', desc: 'Statistik kurikulum, kelulusan, dan kelas berjalan' },
        { title: 'Program Pembelajaran', category: 'Arena Belajar', icon: 'solar:notebook-outline', url: '<?= site_url('dashboard/pembelajaran/program') ?>', desc: 'Master program diklat / bimtek pengadaan' },
        { title: 'Course / Kursus PBJ', category: 'Arena Belajar', icon: 'solar:folder-with-files-outline', url: '<?= site_url('dashboard/pembelajaran/course') ?>', desc: 'Struktur modul, silabus, dan materi kursus' },
        { title: 'Kelas & Batch Pelatihan', category: 'Arena Belajar', icon: 'solar:square-academic-cap-outline', url: '<?= site_url('dashboard/pembelajaran/kelas') ?>', desc: 'Angkatan kelas, tanggal mulai-selesai, dan kuota' },
        { title: 'Penugasan Trainer', category: 'Arena Belajar', icon: 'solar:user-speak-outline', url: '<?= site_url('dashboard/pembelajaran/trainer') ?>', desc: 'Penetapan instruktur pengajar pada tiap kelas' },
        { title: 'Peserta & Enrollment', category: 'Arena Belajar', icon: 'solar:user-plus-rounded-outline', url: '<?= site_url('dashboard/pembelajaran/enrollment') ?>', desc: 'Daftar peserta terdaftar dan progres belajar' },
        { title: 'Manajemen Kurikulum', category: 'Arena Belajar', icon: 'solar:document-medicine-outline', url: '<?= site_url('dashboard/pembelajaran/kurikulum') ?>', desc: 'Materi pelajaran, lampiran PDF, dan durasi' },
        { title: 'Assessment & Ujian', category: 'Arena Belajar', icon: 'solar:clipboard-check-outline', url: '<?= site_url('dashboard/pembelajaran/assessment') ?>', desc: 'Pre-test, kuis modul, tugas praktik, dan post-test' },
        { title: 'Progres Pembelajaran', category: 'Arena Belajar', icon: 'solar:chart-2-outline', url: '<?= site_url('dashboard/pembelajaran/progres') ?>', desc: 'Monitoring capaian belajar per peserta' },
        { title: 'Kelayakan Sertifikat Belajar', category: 'Arena Belajar', icon: 'solar:medal-ribbons-star-outline', url: '<?= site_url('dashboard/pembelajaran/kelayakan-sertifikat') ?>', desc: 'Antrean peserta yang lulus passing grade' },
        { title: 'Laporan Pembelajaran Detail', category: 'Arena Belajar', icon: 'solar:chart-square-outline', url: '<?= site_url('dashboard/pembelajaran/laporan') ?>', desc: 'Rekapitulasi nilai dan metrik kelulusan' },

        // Bunga Rampai
        { title: 'Overview Bunga Rampai', category: 'Bunga Rampai', icon: 'solar:document-text-outline', url: '<?= site_url('dashboard/bunga-rampai') ?>', desc: 'Ringkasan literatur, artikel, dan materi edukasi' },
        { title: 'Artikel PBJ', category: 'Bunga Rampai', icon: 'solar:notes-outline', url: '<?= site_url('dashboard/bunga-rampai/artikel') ?>', desc: 'Kumpulan artikel dan telaah pengadaan' },
        { title: 'Tanya Jawab (FAQ)', category: 'Bunga Rampai', icon: 'solar:question-circle-outline', url: '<?= site_url('dashboard/bunga-rampai/faq') ?>', desc: 'Daftar tanya jawab umum seputar PBJ' },
        { title: 'Studi Kasus PBJ', category: 'Bunga Rampai', icon: 'solar:case-outline', url: '<?= site_url('dashboard/bunga-rampai/studi-kasus') ?>', desc: 'Pembelajaran dari kasus pengadaan riil' },
        { title: 'Template Dokumen', category: 'Bunga Rampai', icon: 'solar:file-download-outline', url: '<?= site_url('dashboard/bunga-rampai/template-dokumen') ?>', desc: 'Format standar HPS, KAK, rancangan kontrak' },
        { title: 'Media & Infografik', category: 'Bunga Rampai', icon: 'solar:gallery-wide-outline', url: '<?= site_url('dashboard/bunga-rampai/media') ?>', desc: 'Infografik alur dan panduan visual PBJ' },
        { title: 'Video Edukasi PBJ', category: 'Bunga Rampai', icon: 'solar:video-library-outline', url: '<?= site_url('dashboard/bunga-rampai/video') ?>', desc: 'Rekaman seminar dan panduan video' },
        { title: 'Galeri Kegiatan', category: 'Bunga Rampai', icon: 'solar:camera-outline', url: '<?= site_url('dashboard/bunga-rampai/galeri') ?>', desc: 'Dokumentasi foto kegiatan Biro PBJ' },
        { title: 'Kategori Konten', category: 'Bunga Rampai', icon: 'solar:folder-favourite-bookmark-outline', url: '<?= site_url('dashboard/bunga-rampai/kategori') ?>', desc: 'Kategori rubrik literasi pengadaan' },
        { title: 'Tagar & Topik', category: 'Bunga Rampai', icon: 'solar:hashtag-outline', url: '<?= site_url('dashboard/bunga-rampai/tag') ?>', desc: 'Topik bahasan kata kunci artikel' },

        // Regulasi PBJ
        { title: 'Daftar Regulasi PBJ', category: 'Regulasi', icon: 'solar:clipboard-text-outline', url: '<?= site_url('dashboard/regulasi/dokumen') ?>', desc: 'Database produk hukum pengadaan barang/jasa' },
        { title: 'Kategori Regulasi', category: 'Regulasi', icon: 'solar:folder-outline', url: '<?= site_url('dashboard/regulasi/kategori') ?>', desc: 'Undang-undang, Perpres, Perlem, Pergub, Kepgub' },
        { title: 'Versi Dokumen Regulasi', category: 'Regulasi', icon: 'solar:file-corrupted-outline', url: '<?= site_url('dashboard/regulasi/versi') ?>', desc: 'Riwayat amandemen dan pembaruan dokumen' },
        { title: 'Relasi Regulasi', category: 'Regulasi', icon: 'solar:link-broken-minimalistic-outline', url: '<?= site_url('dashboard/regulasi/relasi') ?>', desc: 'Status mencabut, mengubah, atau diubah' },
        { title: 'Tahapan PBJ', category: 'Regulasi', icon: 'solar:sort-by-time-outline', url: '<?= site_url('dashboard/regulasi/tahapan-pbj') ?>', desc: 'Perencanaan, Persiapan, Pemilihan, Pelaksanaan' },
        { title: 'Kompetensi Terkait Regulasi', category: 'Regulasi', icon: 'solar:medal-star-outline', url: '<?= site_url('dashboard/regulasi/kompetensi-terkait') ?>', desc: 'Pemetaan pasal terhadap standar kompetensi' },
        { title: 'Log Unduh Regulasi', category: 'Regulasi', icon: 'solar:download-minimalistic-outline', url: '<?= site_url('dashboard/regulasi/log-download') ?>', desc: 'Statistik pengunduhan dokumen hukum oleh publik' },

        // Komunikasi
        { title: 'Berita & Siaran Pers', category: 'Komunikasi', icon: 'solar:letter-opened-outline', url: '<?= site_url('dashboard/komunikasi/berita') ?>', desc: 'Warta kegiatan dan pengumuman Biro PBJ' },
        { title: 'Kategori Berita', category: 'Komunikasi', icon: 'solar:folder-2-outline', url: '<?= site_url('dashboard/komunikasi/kategori-berita') ?>', desc: 'Klasifikasi pos warta pengadaan' },
        { title: 'Event dan Agenda PBJ', category: 'Komunikasi', icon: 'solar:calendar-date-outline', url: '<?= site_url('dashboard/komunikasi/event-agenda') ?>', desc: 'Kalender bimtek, sosialisasi, dan webinar' },
        { title: 'Registrasi Peserta Event', category: 'Komunikasi', icon: 'solar:user-id-outline', url: '<?= site_url('dashboard/komunikasi/registrasi-event') ?>', desc: 'Daftar pemohon kepesertaan acara' },
        { title: 'Presensi & Kehadiran Event', category: 'Komunikasi', icon: 'solar:check-read-outline', url: '<?= site_url('dashboard/komunikasi/kehadiran') ?>', desc: 'Verifikasi check-in dan tanda tangan hadir' },
        { title: 'Waitlist dan Pembatalan', category: 'Komunikasi', icon: 'solar:clock-circle-outline', url: '<?= site_url('dashboard/komunikasi/waitlist-pembatalan') ?>', desc: 'Antrean kuota cadangan dan pembatalan tiket' },
        { title: 'Pesan Masuk (Inbox Helpdesk)', category: 'Komunikasi', icon: 'solar:inbox-line-outline', url: '<?= site_url('dashboard/komunikasi/pesan-masuk') ?>', desc: 'Tiket pertanyaan dan permohonan dari masyarakat' },
        { title: 'Widget Bantuan WhatsApp', category: 'Komunikasi', icon: 'solar:chat-round-call-outline', url: '<?= site_url('dashboard/komunikasi/whatsapp-widget') ?>', desc: 'Pengaturan nomor kontak administrator helpdesk live' },
        { title: 'Pelanggan Newsletter', category: 'Komunikasi', icon: 'solar:mailbox-outline', url: '<?= site_url('dashboard/komunikasi/newsletter') ?>', desc: 'Daftar email pelanggan buletin berita PBJ' },

        // Sertifikat
        { title: 'Overview Sertifikat', category: 'Sertifikat', icon: 'solar:medal-ribbons-star-outline', url: '<?= site_url('dashboard/sertifikat') ?>', desc: 'Statistik penerbitan sertifikat digital PBJ' },
        { title: 'Sertifikat Pembelajaran', category: 'Sertifikat', icon: 'solar:diploma-outline', url: '<?= site_url('dashboard/sertifikat/learning') ?>', desc: 'Sertifikat kelulusan kelas diklat' },
        { title: 'Sertifikat Event / Bimtek', category: 'Sertifikat', icon: 'solar:cup-star-outline', url: '<?= site_url('dashboard/sertifikat/event') ?>', desc: 'Sertifikat kepesertaan seminar dan workshop' },
        { title: 'Antrean Persetujuan Sertifikat', category: 'Sertifikat', icon: 'solar:check-circle-bold', url: '<?= site_url('dashboard/sertifikat/approval') ?>', desc: 'Verifikasi dan penandatanganan elektronik sertifikat' },
        { title: 'Batch Generation Sertifikat', category: 'Sertifikat', icon: 'solar:layers-minimalistic-outline', url: '<?= site_url('dashboard/sertifikat/batch') ?>', desc: 'Penerbitan nomor dan QR code sertifikat massal' },
        { title: 'Template Sertifikat', category: 'Sertifikat', icon: 'solar:palette-outline', url: '<?= site_url('dashboard/sertifikat/template') ?>', desc: 'Desain format sertifikat program, course, dan kelas' },
        { title: 'Verifikasi Sertifikat Online', category: 'Sertifikat', icon: 'solar:qr-code-outline', url: '<?= site_url('dashboard/sertifikat/verifikasi') ?>', desc: 'Uji validasi token QR code keaslian sertifikat' },
        { title: 'Riwayat Sertifikat Terbit', category: 'Sertifikat', icon: 'solar:history-outline', url: '<?= site_url('dashboard/sertifikat/riwayat') ?>', desc: 'Arsip seluruh sertifikat yang pernah diterbitkan' },

        // Government Intelligence
        { title: 'Dashboard Intelligence', category: 'Intelligence', icon: 'solar:chart-2-outline', url: '<?= site_url('dashboard/intelligence') ?>', desc: 'Wawasan analitik aparatur dan pemenuhan SDM PBJ' },
        { title: 'Pemetaan OPD / Unit', category: 'Intelligence', icon: 'solar:buildings-2-outline', url: '<?= site_url('dashboard/intelligence/opd-unit') ?>', desc: 'Sebaran fungsional pengadaan pada dinas/badan' },
        { title: 'SDM JF PPBJ Provinsi', category: 'Intelligence', icon: 'solar:users-group-rounded-outline', url: '<?= site_url('dashboard/intelligence/sdm-jf-ppbj') ?>', desc: 'Data aparatur jabatan fungsional pengelola PBJ' },
        { title: 'Riwayat Jenjang JF PPBJ', category: 'Intelligence', icon: 'solar:history-2-outline', url: '<?= site_url('dashboard/intelligence/riwayat-jf-ppbj') ?>', desc: 'Riwayat promosi dan kepangkatan personel' },
        { title: 'Jenjang Jabatan JF PPBJ', category: 'Intelligence', icon: 'solar:ranking-outline', url: '<?= site_url('dashboard/intelligence/jenjang-jf-ppbj') ?>', desc: 'Tingkatan Pertama, Muda, Madya, Utama' },
        { title: 'Sertifikat Kompetensi PBJ', category: 'Intelligence', icon: 'solar:medal-ribbons-star-outline', url: '<?= site_url('dashboard/intelligence/sertifikat-kompetensi') ?>', desc: 'Sertifikat PBJ Level-1, Auditor, Kontrak Kerja' },
        { title: 'Gap Kompetensi Pengadaan OPD', category: 'Intelligence', icon: 'solar:danger-triangle-outline', url: '<?= site_url('dashboard/intelligence/gap-kompetensi') ?>', desc: 'Analisis kebutuhan vs ketersediaan personel PBJ' },

        // Laporan
        { title: 'Laporan Pembelajaran & Diklat', category: 'Laporan', icon: 'solar:chart-square-outline', url: '<?= site_url('dashboard/laporan/pembelajaran') ?>', desc: 'Rekapitulasi tahunan peserta, nilai, dan kelulusan' },
        { title: 'Laporan Event & Bimtek', category: 'Laporan', icon: 'solar:calendar-minimalistic-outline', url: '<?= site_url('dashboard/laporan/event') ?>', desc: 'Statistik pendaftar dan tingkat kehadiran acara' },
        { title: 'Laporan Penerbitan Sertifikat', category: 'Laporan', icon: 'solar:diploma-verified-outline', url: '<?= site_url('dashboard/laporan/sertifikat') ?>', desc: 'Data rekap sertifikat terbit dan masa berlaku' },
        { title: 'Laporan Kompetensi Aparatur', category: 'Laporan', icon: 'solar:chart-pie-outline', url: '<?= site_url('dashboard/laporan/kompetensi') ?>', desc: 'Capaian sertifikasi kompetensi SDM pemerintah' },
        { title: 'Laporan Pengguna & Profil', category: 'Laporan', icon: 'solar:user-id-outline', url: '<?= site_url('dashboard/laporan/pengguna') ?>', desc: 'Demografi pengguna ASN, Penyedia, dan Umum' },
        { title: 'Riwayat Export Laporan', category: 'Laporan', icon: 'solar:download-minimalistic-outline', url: '<?= site_url('dashboard/laporan/riwayat-export') ?>', desc: 'Arsip file CSV, Excel, dan PDF yang pernah diunduh' },

        // Survei Kepuasan (IKM)
        { title: 'Survei Kepuasan Masyarakat (IKM)', category: 'Survei', icon: 'solar:like-outline', url: '<?= site_url('dashboard/survei-kepuasan') ?>', desc: 'Evaluasi kepuasan layanan dan skor mutu' },

        // Sistem
        { title: 'Antrean Persetujuan Sistem', category: 'Sistem', icon: 'solar:check-read-outline', url: '<?= site_url('dashboard/sistem/approval') ?>', desc: 'Approval sentral permohonan berkas dan profil' },
        { title: 'Audit Trail Aktivitas', category: 'Sistem', icon: 'solar:shield-warning-outline', url: '<?= site_url('dashboard/sistem/audit-trail') ?>', desc: 'Log lengkap aksi administrator untuk kepatuhan' },
        { title: 'Log & Siaran Notifikasi', category: 'Sistem', icon: 'solar:bell-outline', url: '<?= site_url('dashboard/sistem/notifikasi') ?>', desc: 'Kirim pengumuman broadcast in-app ke pengguna' },
        { title: 'Pusat Pengaturan Sistem', category: 'Sistem', icon: 'solar:settings-outline', url: '<?= site_url('dashboard/sistem/pengaturan') ?>', desc: '12 Kategori pengaturan global platform' },
        { title: 'Pengaturan Umum', category: 'Sistem', icon: 'solar:tuning-outline', url: '<?= site_url('dashboard/sistem/pengaturan-umum') ?>', desc: 'Nama aplikasi, zona waktu, format tanggal' },
        { title: 'Pengaturan Keamanan', category: 'Sistem', icon: 'solar:lock-outline', url: '<?= site_url('dashboard/sistem/pengaturan-keamanan') ?>', desc: 'Sesi, percobaan login, dan proteksi akun' },
        { title: 'Pengaturan Sertifikat', category: 'Sistem', icon: 'solar:medal-ribbons-star-outline', url: '<?= site_url('dashboard/sistem/pengaturan-sertifikat') ?>', desc: 'Format nomor sertifikat dan nama penandatangan' },
        { title: 'Mode Pemeliharaan (Maintenance)', category: 'Sistem', icon: 'solar:shield-minimalistic-outline', url: '<?= site_url('dashboard/sistem/maintenance') ?>', desc: 'Bypass IP dan penguncian akses sementara' }
    ];

    function getModalInstance() {
        if (!spotlightBsModal && window.bootstrap) {
            spotlightBsModal = new bootstrap.Modal(spotlightModalEl);
        }
        return spotlightBsModal;
    }

    // Toggle Modal via Ctrl + K or Cmd + K
    window.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const modal = getModalInstance();
            if (modal) {
                if (spotlightModalEl.classList.contains('show')) {
                    modal.hide();
                } else {
                    modal.show();
                }
            }
        }
    });

    // Auto-focus input on modal open
    spotlightModalEl.addEventListener('shown.bs.modal', function () {
        inputEl.focus();
        if (inputEl.value.trim() === '') {
            renderResults(adminCatalog.slice(0, 15));
        }
    });

    spotlightModalEl.addEventListener('hidden.bs.modal', function () {
        inputEl.value = '';
        clearBtnEl.style.display = 'none';
        selectedIndex = -1;
    });

    // Clear input button
    clearBtnEl.addEventListener('click', function () {
        inputEl.value = '';
        clearBtnEl.style.display = 'none';
        inputEl.focus();
        renderResults(adminCatalog.slice(0, 15));
    });

    // Realtime search filtering
    inputEl.addEventListener('input', function () {
        const query = inputEl.value.trim().toLowerCase();
        if (query === '') {
            clearBtnEl.style.display = 'none';
            renderResults(adminCatalog.slice(0, 15));
            return;
        }

        clearBtnEl.style.display = 'flex';

        const terms = query.split(/\s+/).filter(Boolean);
        const matches = adminCatalog.filter(function (item) {
            const searchable = (item.title + ' ' + item.category + ' ' + (item.desc || '')).toLowerCase();
            return terms.every(function (t) { return searchable.includes(t); });
        });

        renderResults(matches);
    });

    // Keyboard navigation (ArrowUp, ArrowDown, Enter)
    inputEl.addEventListener('keydown', function (e) {
        const items = listEl.querySelectorAll('.spotlight-item');
        if (items.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            updateSelection(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && selectedIndex < items.length) {
                const targetLink = items[selectedIndex].getAttribute('href');
                if (targetLink) window.location.href = targetLink;
            } else if (items.length > 0) {
                const firstLink = items[0].getAttribute('href');
                if (firstLink) window.location.href = firstLink;
            }
        }
    });

    function updateSelection(items) {
        items.forEach(function (el, idx) {
            if (idx === selectedIndex) {
                el.classList.add('bg-primary-50', 'border-primary-300');
                el.scrollIntoView({ block: 'nearest' });
            } else {
                el.classList.remove('bg-primary-50', 'border-primary-300');
            }
        });
    }

    function renderResults(items) {
        selectedIndex = -1;
        filteredItems = items;

        if (!items || items.length === 0) {
            initialEl.classList.add('d-none');
            listEl.innerHTML = '';
            emptyEl.classList.remove('d-none');
            return;
        }

        initialEl.classList.add('d-none');
        emptyEl.classList.add('d-none');

        let html = '';
        items.forEach(function (item, index) {
            const badgeClass = item.category === 'Aksi Cepat' ? 'bg-warning-50 text-warning-600' :
                              item.category === 'Aplikasi' ? 'bg-success-50 text-success-600' :
                              'bg-neutral-100 text-neutral-600';

            html += `
                <a href="${item.url}" class="spotlight-item d-flex align-items-center justify-content-between p-12 radius-8 border border-neutral-100 bg-base hover-bg-neutral-50 transition-2 text-decoration-none" data-index="${index}">
                    <div class="d-flex align-items-center gap-12 overflow-hidden">
                        <span class="w-36-px h-36-px bg-primary-50 text-primary-600 rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                            <iconify-icon icon="${item.icon}" class="text-lg"></iconify-icon>
                        </span>
                        <div class="overflow-hidden">
                            <div class="d-flex align-items-center gap-8 mb-2">
                                <span class="text-sm fw-semibold text-primary-light text-truncate">${item.title}</span>
                                <span class="badge ${badgeClass} px-6 py-2 radius-4 text-3xs fw-medium">${item.category}</span>
                            </div>
                            <span class="text-xs text-secondary-light text-truncate d-block">${item.desc}</span>
                        </div>
                    </div>
                    <iconify-icon icon="iconoir:arrow-right" class="text-secondary-light text-base flex-shrink-0 ms-8"></iconify-icon>
                </a>
            `;
        });

        listEl.innerHTML = html;

        // Mouse hover selection sync
        const itemEls = listEl.querySelectorAll('.spotlight-item');
        itemEls.forEach(function (el, idx) {
            el.addEventListener('mouseenter', function () {
                selectedIndex = idx;
                updateSelection(itemEls);
            });
        });
    }
});
</script>

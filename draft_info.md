Analyzed Routes.php:L1-150
Analyzed Routes.php:L151-300
Analyzed Routes.php:L301-450
Analyzed Routes.php:L451-600
Analyzed Routes.php:L601-750
Analyzed Routes.php:L751-900
Analyzed Routes.php:L901-1042

Berikut adalah daftar lengkap fitur yang ada di dalam platform **BegawiPengadaan** (Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa), dikelompokkan berdasarkan pilar ekosistem dan peran pengguna:

---

### 1. Portal Publik & Landing Page (Front-Office)
Halaman depan yang dapat diakses oleh masyarakat umum, ASN, dan pelaku usaha pengadaan:
- **Hero Slider & Sorotan Dinamis Terpadu (`/`)**: Slider interaktif dengan pengelolaan kuota hero dan prioritas item sorotan (*badge Unggulan*) yang seragam untuk 5 modul utama.
- **Arena Belajar Publik (`/arena-belajar`)**:
  - Katalog Program, Course, dan Kelas PBJ aktif.
  - Detail silabus, kurikulum, beban JP, instruktur, dan tombol pendaftaran langsung.
  - Profil direktori pengajar / widyaiswara PBJ.
- **Bunga Rampai Pengetahuan PBJ (`/bunga-rampai`)**:
  - Artikel & wawasan pengadaan barang/jasa.
  - FAQ interaktif seputar pengadaan.
  - Studi kasus & bedah praktik pengadaan.
  - Unduh template dokumen pengadaan (surat, kontrak, HPS, form lelang).
  - Galeri media edukasi (infografis & video panduan PBJ).
- **Regulasi & Produk Hukum PBJ (`/regulasi`)**:
  - Katalog regulasi lengkap dengan filter level hukum (Perpres, Permen, Kepka, Surat Edaran, Perda).
  - Pemfilteran menurut tahapan siklus PBJ (Perencanaan, Pemilihan, Kontrak, Serah Terima).
  - Riwayat versi regulasi & relasi hukum (mencabut, mengubah, turunan).
  - Pratinjau langsung dokumen & pelacak jumlah unduhan (*download counter*).
- **Berita & Komunikasi Pengadaan (`/berita`)**: Publikasi berita resmi, siaran pers, artikel opini, estimasi waktu baca, dan kategori berita PBJ.
- **Event & Agenda PBJ (`/event`)**:
  - Kalender agenda dan acara (Webinar, Bimtek, Workshop, Rakor, Sosialisasi).
  - Formulir pendaftaran event publik mandiri dengan kuota kursi dan batas waktu pendaftaran.
- **Verifikasi Keabsahan Sertifikat Publik (`/verifikasi-sertifikat`)**:
  - Pencarian nomor sertifikat resmi dan scan QR Code langsung.
  - Menampilkan validitas sertifikat, data peserta, NIP, tanggal terbit, pejabat penandatangan, jam pelajaran, dan tautan unduh dokumen asli.
- **Buku Tamu, Survei Kepuasan & Kontak (`/kontak`, `/survei-kepuasan`)**: Formulir pengaduan, tiket tanya jawab, dan survei kepuasan indeks pelayanan publik.

---

### 2. Autentikasi & Registrasi Multi-Skenario
Alur pendaftaran dan manajemen akun adaptif di [`/dashboard/daftar`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Dashboard/Auth/RegisterController.php):
- **Registrasi 4 Jalur Profil**:
  1. *ASN Pemprov Lampung* (terhubung dengan master OPD, NIP, pangkat/golongan, dan jabatan).
  2. *ASN Pemerintah Daerah Lain* (pilihan provinsi/kabupaten/kota asal di seluruh Indonesia).
  3. *Pelaku Usaha / Penyedia* (nama perusahaan, NIB, NPWP, kualifikasi pengadaan).
  4. *Umum / Akademisi / Non-ASN* (perguruan tinggi, institusi swasta, atau perorangan).
- **Pusat Masuk Akun Tunggal (`/dashboard/masuk`)**: Login terpadu dengan autentikasi berbasis sesi aman, proteksi brute-force, dan *remember me*.
- **Pemulihan Kata Sandi Otomatis**: Integrasi pengiriman email reset sandi via Google Mail SMTP.
- **Pemeriksaan Status Akun**: Antrean persetujuan verifikasi akun sebelum akun aktif penuh.

---

### 3. Workspace Peserta / Learner (FiStudy LMS)
Ruang belajar mandiri bagi ASN dan peserta terdaftar di [`/workspace/peserta`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/ParticipantController.php):
- **Dasbor Pembelajaran Pribadi**: Statistik kelas aktif, capaian kelulusan, dan jadwal kegiatan mendatang.
- **Ruang Belajar Interaktif (`/workspace/peserta/kelas/{id}/belajar`)**:
  - Akses modul & materi pelajaran (teks, video, dan lampiran file resmi dengan proteksi izin akses).
  - Penanda progres belajar otomatis per materi (*lesson completed*).
- **Mesin Ujian & Kuis**:
  - *Pre-Test* (tes awal kompetensi sebelum memulai materi).
  - Kuis per modul dengan timer hitung mundur dan pengacakan soal.
  - *Post-Test* akhir dengan riwayat percobaan (*attempt history*) dan penyimpanan skor tertinggi.
- **Pengumpulan Tugas & Evaluasi Praktik**: Pengunggahan file tugas proyek pengadaan, instruksi pengumpulan, revisi tugas, dan umpan balik instruktur.
- **Presensi Digital**: Check-in presensi kelas dan event menggunakan kode sesi.
- **Transkrip Akademik & Brankas Sertifikat (`/workspace/peserta/sertifikat`)**:
  - Melihat riwayat nilai evaluasi.
  - Tombol 1-klik untuk mengunduh file sertifikat kelulusan berformat PDF resmi.
- **Tiket Konsultasi / Helpdesk PBJ (`/workspace/konsultasi`)**: Mengajukan pertanyaan teknis PBJ langsung ke operator/tim pembina secara dua arah.

---

### 4. Workspace Trainer / Instruktur PBJ
Pusat manajemen pembelajaran untuk widyaiswara dan fasilitator di [`/workspace/trainer`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/TrainerController.php):
- **Manajemen Modul & Materi**: Tambah, ubah, nonaktifkan modul ajar dan lesson materi.
- **Bank Soal & Pembuat Kuis**:
  - Pembuatan soal kuis pilihan ganda dan kunci jawaban.
  - Fitur impor soal massal menggunakan template CSV/Excel.
- **Manajemen Peserta Kelas**: Persetujuan pendaftaran peserta kelas, pembatalan pendaftaran (*kick*), dan reaktivasi akun.
- **Penilaian Tugas & Evaluasi**: Antrean periksa tugas peserta (*submission review*) dan penginputan nilai serta catatan evaluasi.
- **Matriks Nilai & Ekspor Gradebook**: Tabel rekapitulasi nilai lengkap (*Pre-Test, Kuis, Tugas, Post-Test, Nilai Akhir*) dengan fitur ekspor CSV 1-klik.

---

### 5. Workspace Operator (Front-Office Operasional)
Pusat operasional dan administrasi harian di [`/workspace/operator`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/OperatorController.php):
- **Antrean Verifikasi Profil Pengguna**: Peninjauan kelengkapan data ASN/Penyedia dan persetujuan aktivasi akun baru.
- **Permohonan Perubahan Profil**: Validasi pengajuan ubah data pengguna (termasuk visual diff viewer sebelum/sesudah perubahan).
- **Penugasan Trainer**: Menugaskan widyaiswara/instruktur ke kelas PBJ tertentu.
- **Operasional Event & Presensi**:
  - Buka/tutup sesi presensi event dan generate token kode kehadiran unik.
  - Cetak daftar hadir resmi (*Official Roster Print*) dan ekspor data peserta acara.
- **Verifikasi & Penerbitan Sertifikat**:
  - Penerbitan sertifikat mandiri maupun penerbitan massal (*bulk issue*).
  - Pembatalan/pencabutan sertifikat bermasalah.
- **Executive Health Radar & Pelacak Kapasitas Penyimpanan**: Pemantauan visual utilisasi disk server dan integritas data.

---

### 6. Dashboard Administrator & Super Admin (Back-Office)
Pusat tata kelola penuh seluruh modul di [`/dashboard`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Admin/DashboardController.php):
- **Unified Single Master Dashboard**: Panel analitik 7 pilar (pertumbuhan pengguna, kelas selesai, penerbitan sertifikat, kehadiran acara, regulasi baru, tiket terselesaikan, utilisasi sistem).
- **Pusat Manajemen Sorotan & Hero Carousel (`/dashboard/landing/sorotan-carousel`)**:
  - Manajemen terpusat untuk 5 modul (Arena Belajar, Bunga Rampai, Regulasi, Berita, Event).
  - Pemisahan toggle independen: **Sorotan** (badge Unggulan pada card) vs **Slider** (masuk antrean Hero Slider).
  - Modal "Tambah Konten" pencarian cepat seluruh item terbaru.
  - Pengaturan mode slider (Tunggal / Kombinasi) dan kuota hero otomatis tanpa duplikasi.
- **Manajemen Konten Landing Page (CMS)**:
  - Banner slider beranda utama.
  - Popup pengumuman & info darurat (*modal announcement*).
  - Profil lembaga, visi-misi, nilai budaya kerja pengadaan.
  - Portal eksternal (tautan SPSE, SiRUP, Bela Pengadaan, LPSE Lampung).
  - Testimoni pengguna dan tim pembina PBJ.
- **Manajemen Pembelajaran (LMS Master)**:
  - Kelola Program PBJ (kurikulum induk).
  - Kelola Course PBJ.
  - Kelola Kelas / Batch PBJ (jadwal, kuota, durasi JP, duplikasi batch kelas).
- **Manajemen Pengguna & Hak Akses (RBAC)**:
  - Tabel pengguna server-side dengan filter multi-segi (OPD, tipe ASN, role, status akun) dan pencarian global.
  - Matriks Role & Permissions: Pembuatan peran baru (*custom roles*) dan pemberian izin akses terperinci.
  - Unit Organisasi & Master OPD se-Provinsi Lampung.
- **Manajemen Komunikasi & Siaran**:
  - Pusat siaran email massal (*Email Broadcast Center*) via Google Mail dengan sistem antrean dan pelacakan status kirim.
  - Pesan masuk buku tamu dan formulir kontak publik.
  - Pengaturan widget WhatsApp Helpdesk interaktif.

---

### 7. Sistem Sertifikat Digital PBJ (Dual-Layer PDF Vector)
Modul penerbitan dan pengesahan sertifikat resmi di [`/dashboard/sertifikat`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Certificate/CertificateController.php):
- **Generator Sertifikat Tunggal (`CertificateGenerator`)**: Mesin penerbitan berbasis approval yang aman dari duplikasi (*idempotent*).
- **Perancang Template Sertifikat (`/dashboard/sertifikat/template`)**:
  - **Dukungan Template Berformat Dokumen PDF**: Mengimpor halaman PDF dari Canva/Word/Illustrator sebagai kanvas vektor beresolusi tinggi tanpa kompresi buram.
  - **Dukungan Template Gambar (PNG / JPG 300 DPI)**: Mendukung gambar background A4 Landscape (3508 &times; 2480 px).
  - **Palet 24+ Placeholder Otomatis**: Nama peserta, NIP, instansi, jabatan, judul kegiatan, jam pelajaran (JP), predikat kelulusan, rentang tanggal, nama pejabat pengesah, NIP pengesah, QR code.
  - **Aturan Anti-Tumpang-Tindih Hierarki**: Pencegahan bentrok template antar tingkat Program, Course, Kelas, dan Event 1:1.
  - **Preset Tata Letak 1-Klik**: Tombol *"Preset Overlay Background"* untuk penataan posisi teks dan QR code secara instan.
- **Formula Baku Penomoran Sertifikat Resmi**:
  - Format: `SERT-{nomor_urut:5d}/{tipe}-{target_id}/05/{tahun}` (contoh: `SERT-00035/AB-5/05/2026`).
  - Sequence counter tahunan dengan proteksi race condition.
- **Keabsahan Digital Berbasis QR Code**:
  - QR code dinamis per sertifikat dengan verifikasi token SHA-256 yang langsung mengarahkan ke halaman verifikasi resmi portal Pemprov Lampung.

---

### 8. Sistem Notifikasi & Push Notification Terpadu
Sistem lonceng dan notifikasi terdistribusi di [`/dashboard/sistem/notifikasi`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Admin/NotificationController.php):
- **Arsitektur Berbagi Pakai (Shared Pool Notification Engine)**:
  - Notifikasi penting bagi Super Admin, Admin, dan Operator memakai antrean bersama (*Shared Pool*) di kanal `shared:admin_operator`.
  - Mekanisme *"Satu Membaca, Semua Terbaca"*: Ketika salah satu admin/operator membuka notifikasi, status langsung ditandai terbaca untuk semua pihak tanpa membuat duplikasi baris database.
- **Efek Push Real-Time Interaktif**:
  - *Audio Chime*: Nada notifikasi harmonik berbasis Web Audio API (bebas dependensi file eksternal).
  - *Floating In-App Toast*: Muncul pop-up di pojok kanan bawah saat ada event sistem baru.
  - *Flashing Tab Title*: Judul tab browser berkedip `(N) Notifikasi Baru!` saat pengguna sedang membuka tab lain.
  - *Live Sync Bell Counter*: Sinkronisasi berkala feed notifikasi dropdown bar tanpa refresh halaman.
- **Pengiriman Multi-Kanal**: Dukungan notifikasi in-app, notifikasi email, dan siaran broadcast manual oleh administrator.

---

### 9. Sistem Keamanan, Integritas Data & Audit Trail
Lapisan keamanan dan pemantauan sistem tingkat lanjut:
- **Centralized File Asset Management (`file_assets`)**: Seluruh file materi, tugas, dan sertifikat tercatat dengan status siklus hidup (*lifecycle*), verifikasi checksum SHA-256, dan pembatasan visibilitas (*public vs restricted*).
- **Audit Trail & Jejak Aktivitas (`/dashboard/sistem/audit-trail`)**: Pencatatan riwayat setiap aksi administratif (login, modifikasi data, terbit sertifikat, hapus data) lengkap dengan identitas aktor, IP address, waktu, dan payload perubahan.
- **Proteksi Akses (Security Hardening)**: Proteksi CSRF di seluruh form POST, pembersihan XSS otomatis, pembatasan ukuran berkas unggahan (*upload guards*), serta isolasi permission berbasis filter rute CodeIgniter 4.
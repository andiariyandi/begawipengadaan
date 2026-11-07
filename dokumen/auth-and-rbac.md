# BegawiPengadaan — Authentication and Modular RBAC Architecture

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Ringkasan Eksekutif Arsitektur Autentikasi & RBAC

BegawiPengadaan menerapkan sistem otorisasi **Role-Based Access Control (RBAC) Modular Granular** dengan **90 Permissions** yang terdistribusi di **12 Modul Domain**. Seluruh otentikasi disentralisasi pada controller WowDash (`App\Controllers\Dashboard\Auth\`), diawasi oleh filter keamanan berlapis (`AuthenticationFilter` dan `WorkspaceAuthFilter`), serta dilengkapi mekanisme **Zero Stale Session** dan **Dynamic Mutation Guard**.

### Pilar Utama Keamanan Akses:
1. **Single Entry Auth:** Pintu masuk autentikasi tunggal (login, register 9 jalur, lupa sandi, reset sandi, status akun) berada di `/dashboard/masuk`, `/dashboard/daftar`, `/dashboard/lupa-kata-sandi`.
2. **Zero Stale Session:** Setiap permintaan request HTTP secara real-time menyinkronkan array `roles` dan `permissions` langsung dari database (`user_roles` & `permissions`). Jika role dicabut atau diubah oleh admin, sesi pengguna langsung terbarui seketika tanpa perlu logout-login ulang.
3. **Dynamic Zone-to-Permission Mapping (`zoneMap`):** `AuthenticationFilter` memetakan URL dashboard ke 87+3 granular permission sets untuk memvalidasi hak akses modul.
4. **Dynamic Mutation Guard:** Memvalidasi kata kerja HTTP mutasi (`POST`, `PUT`, `DELETE`, `PATCH`). Pengguna dengan role read-only (seperti *Auditor*) otomatis diblokir (HTTP 403) jika mencoba mengeksekusi aksi mutasi data.
5. **Workspace Multi-Role Isolation (`WorkspaceAuthFilter`):** Menjaga isolasi sub-grup ruang kerja FiStudy (`/workspace/peserta`, `/workspace/trainer`, `/workspace/operator`, `/workspace/admin`).
6. **Smart Landing Redirect:** Memandu pengguna yang login langsung ke modul dashboard pertama yang diizinkan (`getFirstAllowedDashboardRoute`) jika tidak memiliki izin ringkasan umum `dashboard.view`.

---

## 2. Katalog Lengkap 90 Granular Permissions (12 Domain)

| No | Kode Permission (`name`) | Domain | Label Deskripsi | Scope / Keterangan |
|---|---|:---:|---|---|
| 1 | `Melihat Ringkasan Dashboard Utama` | `dashboard` | - | Akses membuka halaman beranda/ringkasan dashboard admin. |
| 2 | `Melihat Indikator Sistem & KPI Internal` | `dashboard` | - | Akses memantau metrik performa sistem, KPI, dan aktivitas terkini. |
| 3 | `Melihat Modul Landing Page CMS` | `landing` | - | Akses melihat daftar konten dinamis landing page publik. |
| 4 | `Mengelola Slider Hero & Promosi` | `landing` | - | Akses menambah, mengubah, dan menghapus banner carousel dan sorotan. |
| 5 | `Mengelola Profil Lembaga & Pilar PATIK` | `landing` | - | Akses mengedit deskripsi lembaga, pilar hierarki PBJ, dan ikon fitur. |
| 6 | `Mengelola Tautan Portal Eksternal` | `landing` | - | Akses mengelola link portal integrasi LKPP, SiRUP, LPSE, dan layanan eksternal. |
| 7 | `Mengelola Tim Inovator & Testimoni` | `landing` | - | Akses mengelola direktori tim pembina dan ulasan/testimoni pengguna. |
| 8 | `Mengelola Pengumuman & Banner CMS` | `landing` | - | Akses mengelola pengumuman berjalan dan banner halaman sekunder. |
| 9 | `Manajemen Akses & Otorisasi Pengguna` | `access` | - | Akses administratif menyeluruh ke modul manajemen akses pengguna. |
| 10 | `Melihat Data Pengguna Platform` | `access` | - | Akses melihat daftar seluruh akun pengguna dan detail profilnya. |
| 11 | `Mengelola Akun Pengguna Platform` | `access` | - | Akses membuat, memperbarui, mengaktifkan, dan menonaktifkan akun. |
| 12 | `Melihat Antrean Verifikasi Profil` | `access` | - | Akses melihat daftar pengguna dan permohonan perubahan profil yang menunggu review. |
| 13 | `Memproses Verifikasi & Perubahan Profil` | `access` | - | Akses menyetujui atau menolak verifikasi berkas ASN, Penyedia, atau Akademisi. |
| 14 | `Melihat Daftar Peran (Role) & Matriks` | `access` | - | Akses melihat katalog role dan matriks role-permission. |
| 15 | `Mengelola Peran (Role) & Hak Akses` | `access` | - | Akses membuat role baru, mengatur permission, dan memproses permohonan role. |
| 16 | `Melihat Unit Organisasi PBJ` | `access` | - | Akses melihat hierarki unit kerja perangkat daerah dan instansi. |
| 17 | `Mengelola Unit Organisasi PBJ` | `access` | - | Akses menambah, mengedit, dan mengarsipkan unit organisasi. |
| 18 | `Mengelola Peran & Penugasan Pengadaan` | `access` | - | Akses mengelola klasifikasi peran fungsional PBJ (PPK, PPTK, Pokja, dsb.). |
| 19 | `Kelola Akun Pengguna (Alias)` | `access` | - | Alias izin administratif untuk manajemen akun pengguna. |
| 20 | `Verifikasi Profil Akun (Alias)` | `access` | - | Alias izin persetujuan verifikasi akun pengguna. |
| 21 | `Kelola Konfigurasi Peran (Alias)` | `access` | - | Alias izin manajerial untuk role dan permission. |
| 22 | `Melihat Arena Pembelajaran PBJ` | `learning` | - | Akses melihat katalog program belajar dan kelas pelatihan PBJ. |
| 23 | `Mengelola Program Pembelajaran PBJ` | `learning` | - | Akses membuat dan mengelola taksonomi program kompetensi PBJ. |
| 24 | `Mengelola Kursus & Silabus` | `learning` | - | Akses mengelola materi kursus, kompetensi dasar, dan capaian pembelajaran. |
| 25 | `Melihat Kelas Pembelajaran PBJ` | `learning` | - | Akses melihat jadwal kelas dan daftar rombongan belajar peserta. |
| 26 | `Membuat Kelas Pembelajaran Baru` | `learning` | - | Akses membuka kelas baru dan menetapkan kuota pendaftaran. |
| 27 | `Memperbarui Informasi Kelas Pembelajaran` | `learning` | - | Akses mengubah silabus, jadwal, instruktur, dan status kelas. |
| 28 | `Menghapus Kelas Pembelajaran` | `learning` | - | Akses membatalkan atau menghapus rekaman kelas pelatihan. |
| 29 | `Mengelola Kurikulum & Modul Belajar` | `learning` | - | Akses menyusun modul silabus dan butir lesson bahan ajar. |
| 30 | `Melihat Kuis & Assessment Pembelajaran` | `learning` | - | Akses meninjau kuis, soal latihan, dan ujian kompetensi. |
| 31 | `Mengelola Kuis & Bank Soal` | `learning` | - | Akses menyusun bank butir soal ujian, passing grade, dan batas durasi. |
| 32 | `Melihat Pendaftaran Peserta Kelas` | `learning` | - | Akses melihat status pendaftaran peserta di setiap kelas pelatihan. |
| 33 | `Memverifikasi Pendaftaran Peserta` | `learning` | - | Akses menyetujui atau menolak pendaftaran calon peserta kelas. |
| 34 | `Menilai Tugas & Evaluasi Peserta` | `learning` | - | Akses memeriksa penugasan dan memberikan nilai akhir kelas. |
| 35 | `Monitoring Kelayakan Sertifikat Pembelajaran` | `learning` | - | Akses mengevaluasi kelulusan peserta untuk penerbitan sertifikat e-learning. |
| 36 | `Melihat Laporan Statistik Pembelajaran` | `learning` | - | Akses memantau progres ketuntasan dan statistik kelas. |
| 37 | `Manajemen Sistem Pembelajaran (LMS Alias)` | `learning` | - | Izin administratif penuh atas modul Arena Belajar LMS. |
| 38 | `Verifikasi Pendaftaran Kelas (Alias)` | `learning` | - | Alias verifikasi pendaftaran calon peserta diklat. |
| 39 | `Evaluasi Penilaian Peserta (Alias)` | `learning` | - | Alias evaluasi dan penilaian hasil ujian peserta. |
| 40 | `Melihat Direktori Bunga Rampai PBJ` | `bunga` | - | Akses membaca naskah, panduan, artikel, dan template PBJ. |
| 41 | `Mengelola Naskah & Artikel PBJ` | `bunga` | - | Akses membuat, menyunting, dan menerbitkan artikel naskah PBJ. |
| 42 | `Mengelola Tanya Jawab (FAQ) PBJ` | `bunga` | - | Akses mengelola basis pengetahuan tanya jawab konsultasi PBJ. |
| 43 | `Mengelola Studi Kasus PBJ` | `bunga` | - | Akses mengelola telaah dan studi kasus penanganan masalah PBJ. |
| 44 | `Mengelola Template Dokumen PBJ` | `bunga` | - | Akses mengunggah dan memperbarui berkas Standar Dokumen Pemilihan (SDP). |
| 45 | `Mengelola Pustaka Media & Galeri PBJ` | `bunga` | - | Akses mengunggah video infografis dan dokumentasi multimedia. |
| 46 | `Mengelola Kategori & Tag Bunga Rampai` | `bunga` | - | Akses mengelola taksonomi bidang dan penanda topik artikel. |
| 47 | `Manajemen Knowledge Management System (Alias)` | `bunga` | - | Izin administratif penuh atas modul Knowledge Management System (KMS). |
| 48 | `Melihat Direktori Regulasi PBJ` | `regulation` | - | Akses melihat daftar dan mengunduh berkas regulasi hukum PBJ. |
| 49 | `Menambah Dokumen Regulasi Baru` | `regulation` | - | Akses mengunggah peraturan perundang-undangan baru ke repositori. |
| 50 | `Mengubah Dokumen Regulasi PBJ` | `regulation` | - | Akses memperbarui metadata regulasi, konsideran, dan relasi aturan. |
| 51 | `Menghapus Dokumen Regulasi PBJ` | `regulation` | - | Akses menghapus atau mencabut dokumen regulasi dari katalog. |
| 52 | `Menyetujui & Mempublikasikan Regulasi` | `regulation` | - | Akses otorisasi rilis dokumen regulasi ke portal publik. |
| 53 | `Mengelola Kategori Regulasi PBJ` | `regulation` | - | Akses mengatur klasifikasi regulasi (Perpres, Perlem LKPP, Pergub, dsb.). |
| 54 | `Mengelola Tahapan PBJ & Kompetensi` | `regulation` | - | Akses memetakan regulasi ke tahapan siklus PBJ dan kompetensi JF PPBJ. |
| 55 | `Manajemen Penuh Regulasi PBJ (Alias)` | `regulation` | - | Izin administratif penuh atas modul repositori regulasi PBJ. |
| 56 | `Melihat Modul Komunikasi & Event` | `communication` | - | Akses membuka modul berita, event, dan layanan konsultasi. |
| 57 | `Mengelola Warta Berita PBJ` | `communication` | - | Akses menulis, menyunting, dan menerbitkan rilis berita PBJ. |
| 58 | `Melihat Agenda Event & Bimtek` | `communication` | - | Akses melihat kalender kegiatan sosialisasi dan bimbingan teknis. |
| 59 | `Mengelola Agenda Event & Bimtek` | `communication` | - | Akses membuat event, menetapkan kuota, dan mengatur sesi kegiatan. |
| 60 | `Mengelola Presensi & Kehadiran Event` | `communication` | - | Akses memvalidasi kehadiran peserta dan menutup sesi presensi. |
| 61 | `Mengelola Pesan Masuk & Helpdesk` | `communication` | - | Akses membaca tiket permohonan konsultasi dan pesan masuk publik. |
| 62 | `Mengelola Pengaturan Helpdesk Widget` | `communication` | - | Akses mengatur nomor kontak dan jam operasional WhatsApp Helpdesk. |
| 63 | `Mengelola Berlangganan Newsletter` | `communication` | - | Akses mengekspor daftar email pelanggan buletin berkala. |
| 64 | `Melihat Master & Riwayat Sertifikat` | `certificate` | - | Akses melihat rekap sertifikat pelatihan dan event yang telah terbit. |
| 65 | `Mengelola Penerbitan Sertifikat` | `certificate` | - | Akses menerbitkan, memperbarui, atau mencabut sertifikat peserta. |
| 66 | `Mengelola Penerbitan Batch Sertifikat` | `certificate` | - | Akses memproses penomoran otomatis dan generate e-sertifikat massal. |
| 67 | `Mengelola Desain Template Sertifikat` | `certificate` | - | Akses mengatur tata letak, tanda tangan digital, dan latar sertifikat. |
| 68 | `Menyetujui Penerbitan Sertifikat` | `certificate` | - | Akses otorisasi penandatanganan dan pengesahan sertifikat. |
| 69 | `Melihat Dashboard Intelligence SDM PBJ` | `intelligence` | - | Akses memantau sebaran SDM JF PPBJ dan analitik kesenjangan kompetensi. |
| 70 | `Mengelola Data & Riwayat SDM JF PPBJ` | `intelligence` | - | Akses memperbarui jenjang jabatan dan penugasan pejabat fungsional PBJ. |
| 71 | `Mengelola Data Sertifikat Kompetensi` | `intelligence` | - | Akses mencatat jenis dan masa berlaku sertifikasi pengadaan aparatur. |
| 72 | `Mengelola Analisis Pemetaan Unit Kerja/OPD` | `intelligence` | - | Akses analisis kebutuhan SDM pengadaan di setiap instansi perangkat daerah. |
| 73 | `Melihat Pusat Laporan Dashboard` | `reports` | - | Akses membuka konsol laporan terpadu seluruh aktivitas platform. |
| 74 | `Melihat Laporan Statistik Pembelajaran` | `reports` | - | Akses meninjau laporan capaian nilai dan ketuntasan kelas diklat. |
| 75 | `Melihat Laporan Keikutsertaan Event` | `reports` | - | Akses meninjau laporan partisipasi dan kehadiran kegiatan sosialisasi. |
| 76 | `Melihat Laporan Penerbitan Sertifikat` | `reports` | - | Akses meninjau rekapitulasi penerbitan sertifikat berkala. |
| 77 | `Melihat Laporan Kesenjangan Kompetensi` | `reports` | - | Akses meninjau laporan analisis gap kompetensi SDM pengadaan. |
| 78 | `Melihat Laporan Aktivitas Pengguna` | `reports` | - | Akses meninjau rekap pertumbuhan pengguna dan keaktifan akun. |
| 79 | `Mengekspor Berkas Laporan & Riwayat Ekspor` | `reports` | - | Akses mengunduh laporan format Excel/CSV dan melihat riwayat ekspor. |
| 80 | `Melihat Hasil & Statistik Survei Kepuasan` | `survey` | - | Akses memantau skor Indeks Kepuasan Masyarakat (IKM) dan umpan balik responden. |
| 81 | `Mengelola Pertanyaan & Konfigurasi Survei` | `survey` | - | Akses mengonfigurasi indikator survei kepuasan layanan PBJ. |
| 82 | `Mengelola Pengaturan Sistem & Platform` | `system` | - | Akses konfigurasi umum, identitas instansi, integrasi, dan keamanan sistem. |
| 83 | `Melihat Antrean Approval Sistem` | `system` | - | Akses melihat daftar antrean persetujuan perubahan profil dan pendaftaran. |
| 84 | `Meninjau Antrean Approval Sistem` | `system` | - | Akses memeriksa kelengkapan dokumen pendukung sebelum disetujui. |
| 85 | `Memutuskan (Setujui/Tolak) Approval` | `system` | - | Akses eksekusi persetujuan atau penolakan pengajuan pada Approval Hub. |
| 86 | `Melihat Jejak Audit Log Aktivitas` | `system` | - | Akses menelusuri rekam jejak aktivitas seluruh pengguna di sistem. |
| 87 | `Mengekspor Jejak Audit Log` | `system` | - | Akses mengekspor berkas CSV jejak audit sistem untuk keperluan forensik. |
| 88 | `Melihat Pusat Notifikasi Dashboard` | `system` | - | Akses membuka konsol notifikasi sistem dan peringatan dini. |
| 89 | `Mengirim Notifikasi Broadcast Sistem` | `system` | - | Akses menyebarkan notifikasi pengumuman ke seluruh atau sebagian pengguna. |
| 90 | `Mengelola Mode Pemeliharaan Platform` | `system` | - | Akses mengaktifkan mode maintenance platform dan flush cache sistem. |

---

## 3. Matriks Pemetaan Role Kanonikal & Custom Roles

| Peran (Role Key) | Tipe | Lingkup Akses & Permission Set |
|---|:---:|---|
| `super_administrator` | Sistem | Memiliki hak bypass 100% ke seluruh 90 permissions dan modul konfigurasi sistem. |
| `administrator` | Eksekutif | Akses manajerial penuh ke 12 modul domain (approval, user, master data, report, intelligence). |
| `admin_arenabelajar` | Kustom | Khusus modul Arena Belajar (`learning.*`, kelas, kurikulum, assessment, sertifikat). |
| `admin_regulasi` | Kustom | Khusus modul Regulasi PBJ (`regulation.*`, tambah/ubah naskah hukum, kelola versi). |
| `admin_event` | Kustom | Khusus modul Komunikasi & Event (`communication.*`, agenda, presensi, warta). |
| `operator` | Workspace | Pusat operasional workspace: asistensi kelas, antrean verifikasi, approval event. |
| `trainer` | Workspace | Ruang pengajar: kelas binaan, bank materi, kuis, evaluasi tugas peserta, gradebook matrix. |
| `participant` / `peserta` | Publik | Pembelajaran mandiri, pengerjaan tugas/kuis, pendaftaran event, klaim sertifikat. |
| `auditor` | Kustom | Akses read-only ke jejak audit (`audit_logs`), laporan analitik, dan visualisasi tanpa izin mutasi. |

---

## 4. Mekanisme Penanganan Sesi Multi-Role & Workspace Dispatcher

Saat pengguna memiliki lebih dari satu peran (misal: *Peserta* sekaligus *Trainer* atau *Operator*):
1. **Root Dispatcher (`/workspace`):** Mengarahkan otomatis ke antarmuka peran prioritas tertinggi yang dimiliki (`operator` -> `trainer` -> `peserta`), tanpa duplikasi tampilan.
2. **Stacked Modular Sidebar:** Menu sidebar menampilkan blok fitur operasional peran yang ditugaskan, disusul blok fitur personal pembelajaran sebagai hak dasar peserta.
3. **Dashboard Sterilization:** Peran murni workspace (`operator`, `trainer`, `peserta`) yang tidak memiliki role administratif dashboard otomatis disterilkan dan dialihkan ke `/workspace` dengan notifikasi informatif.

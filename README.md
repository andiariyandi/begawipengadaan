# BegawiPengadaan

<p align="center">
  <strong>Platform Transformasi Digital Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung</strong><br>
  Biro Pengadaan Barang dan Jasa — Sekretariat Daerah Provinsi Lampung
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Framework-CodeIgniter%204.7-EF4444?style=flat-square&logo=codeigniter&logoColor=white" alt="CodeIgniter 4">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Database-MySQL%208.4%2B-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL 8.4+">
  <img src="https://img.shields.io/badge/Architecture-Dual--Template%20Monolith-10B981?style=flat-square" alt="Monolith">
  <img src="https://img.shields.io/badge/RBAC-90%20Granular%20Permissions-6366F1?style=flat-square" alt="RBAC">
  <img src="https://img.shields.io/badge/Status-Production%20Ready-blue?style=flat-square" alt="Production Ready">
</p>

---

## Daftar Isi
1. [Tentang BegawiPengadaan](#tentang-begawipengadaan)
2. [Tiga Area Fungsional Sistem](#tiga-area-fungsional-sistem)
3. [Modul & Fitur Utama](#modul--fitur-utama)
4. [Arsitektur & Batasan Template](#arsitektur--batasan-template)
5. [Prasyarat Sistem & Dependensi](#prasyarat-sistem--dependensi)
6. [Panduan Instalasi & Konfigurasi](#panduan-instalasi--konfigurasi)
7. [Inisialisasi Database & Seeder](#inisialisasi-database--seeder)
8. [Akun Pengujian Lokal (Development)](#akun-pengujian-lokal-development)
9. [Perintah Operasional Spark CLI](#perintah-operasional-spark-cli)
10. [Struktur Direktori Proyek](#struktur-direktori-proyek)
11. [Keamanan & Integritas Sistem](#keamanan--integritas-sistem)
12. [Indeks Dokumentasi Teknis](#indeks-dokumentasi-teknis)
13. [Lisensi & Hak Cipta](#lisensi--hak-cipta)

---

## Tentang BegawiPengadaan

**BegawiPengadaan** adalah ekosistem digital terpadu milik Pemerintah Provinsi Lampung yang dikelola oleh Biro Pengadaan Barang dan Jasa (PBJ). Platform ini mentransformasikan proses pembelajaran, transfer pengetahuan, tata kelola regulasi, serta pemetaan kompetensi sumber daya manusia PBJ ke dalam satu sistem terintegrasi, transparan, dan akuntabel.

Platform menyatukan delapan pilar layanan:
- **LMS PBJ (*Arena Belajar*):** Pembelajaran mandiri dan terstruktur bersertifikat.
- **KMS PBJ (*Bunga Rampai*):** Repositori pengetahuan, studi kasus, dan template dokumen standar.
- **Repositori Hukum PBJ:** Bank naskah regulasi berjenjang dengan relasi status hukum.
- **Publikasi & Agenda (*Berita & Event*):** Informasi resmi, webinar, workshop, dan kode presensi interaktif.
- **E-Sertifikat Terverifikasi:** Penerbitan sertifikat digital resmi dan verifikasi publik mandiri (QR Code).
- **Government Intelligence PBJ:** Radar kematangan PBJ daerah dan analisis kesenjangan (*gap*) kompetensi OPD.
- **Flexible Modular RBAC Hub:** Manajemen otorisasi 90 permission granular lintas 12 modul domain.
- **Sentralisasi CMS & Audit Trail:** Kontrol konten beranda dinamis dan pencatatan jejak audit keamanan penuh.

---

## Tiga Area Fungsional Sistem

Sistem BegawiPengadaan membagi antarmuka menjadi tiga area independen sesuai dengan audiens, fungsi bisnis, dan hak akses:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                            BEGAWIPENGADAAN                             │
└───────────────────┬────────────────┬───────────────────┬───────────────┘
                    │                │                   │
         ┌──────────▼──────────┐     │        ┌──────────▼──────────┐
         │ 1. LANDING PAGE     │     │        │ 3. DASHBOARD        │
         │    & PORTAL PUBLIK  │     │        │    BACK-OFFICE      │
         ├─────────────────────┤     │        ├─────────────────────┤
         │ • Beranda Utama CMS │     │        │ • Auth Terpadu      │
         │ • Arena Belajar     │     │        │ • Master Dashboard  │
         │ • Bunga Rampai      │     │        │ • Modular RBAC Hub  │
         │ • Bank Regulasi     │     │        │ • Pengguna & Verif  │
         │ • Berita & Populer  │     │        │ • CMS & Spotlight   │
         │ • Event & Agenda    │     │        │ • LMS & Kelas PBJ   │
         │ • Verifikasi Sertif │     │        │ • Gov Intelligence  │
         │ • Statistik Publik  │     │        │ • Laporan & Audit   │
         └──────────┬──────────┘     │        └──────────┬──────────┘
                    │                │                   │
             Template: FiStudy       │           Template: WowDash
             Asset: assets/main/     │           Asset: assets/dashboard/
                                     │
                          ┌──────────▼──────────┐
                          │ 2. WORKSPACE        │
                          │    PENGGUNA         │
                          ├─────────────────────┤
                          │ • Multi-role Portal │
                          │ • Peserta (Learner) │
                          │ • Pengajar (Trainer)│
                          │ • Operator LMS/Desk │
                          │ • Eksekutif Admin   │
                          │ • Profil & Presensi │
                          │ • Kotak Notifikasi  │
                          └──────────┬──────────┘
                                     │
                              Template: FiStudy
                              Asset: assets/main/
```

1. **Landing Page & Portal Publik (FiStudy):** Area terbuka tanpa login untuk masyarakat umum, ASN, pelaku usaha/penyedia, dan akademisi guna mengakses publikasi berita, regulasi, silabus kelas, agenda kegiatan, serta layanan mandiri verifikasi keaslian e-sertifikat.
2. **Workspace Pengguna (FiStudy):** Portal kolaboratif pengguna terautentikasi dengan navigasi spesifik peran (*Peserta*, *Trainer*, *Operator*, *Administrator Eksekutif*) untuk mengikuti pembelajaran, penilaian tugas, absensi, hingga pengelolaan kelas binaan.
3. **Dashboard Back-Office & Auth Terpadu (WowDash):** Area administratif terpusat untuk verifikasi akun/profil, penugasan hak akses granular, tata kelola materi pembelajaran, administrasi event & sertifikat, instrumen analitik intelijen PBJ, serta audit log keamanan.

---

## Modul & Fitur Utama

### 1. Arena Belajar (Learning Management System)
- Silabus modul bertingkat: Dokumen panduan, video instruksional, dan tautan referensi.
- Evaluasi komprehensif: Pre-test, kuis berbatas waktu dengan bank soal acak, dan penugasan mandiri.
- State engine kelulusan: Syarat presensi, batas nilai minimum (*passing grade*), dan evaluasi kelas sebelum penerbitan sertifikat.
- *Trainer Gradebook:* Matriks nilai peserta, analisis butir soal, dan ekspor transkrip nilai.

### 2. Bunga Rampai (Knowledge Management System)
- Repositori pengetahuan PBJ: Artikel praktis, studi kasus pengadaan, dan tanya-jawab umum (FAQ).
- Dokumen template standar: Kerangka Acuan Kerja (KAK), dokumen pemilihan, HPS, dan draf kontrak pengadaan yang siap diunduh secara aman.
- Interaksi pengguna: Penanda bacaan (*bookmark*) lintas modul dan pencarian cerdas berbasis kata kunci/kategori.

### 3. Bank Naskah Hukum & Regulasi PBJ
- Klasifikasi regulasi berjenjang: Undang-Undang, Peraturan Pemerintah, Perpres, Permen, Peraturan LKPP, Perda, dan Pergub Lampung.
- Riwayat status hukum: Pelacakan status naskah (Berlaku, Mengubah, Diubah, Mencabut, atau Dicabut) dengan visualisasi relasi regulasi.
- Pratinjau dokumen: *Inline PDF reader* terproteksi dan unduhan lampiran aman dengan sanitasi header.

### 4. Event & Agenda PBJ
- Siklus hidup kegiatan: Dari *Draft*, *Coming Soon*, *Open Registration*, *Full*, hingga *Finished*.
- Pengelolaan kuota & *Waitlist*: Promosi otomatis peserta cadangan saat terjadi pembatalan registrasi.
- Presensi real-time berbasis kode unik: Input kode presensi terverifikasi dengan pencegahan absensi ganda.
- *Batch Certificate Issuance:* Penerbitan massal sertifikat elektronik resmi untuk peserta yang hadir pada sesi selesai.

### 5. E-Sertifikat Terverifikasi
- Penomoran seri otomatis dengan sequence format resmi Pemerintah Provinsi Lampung.
- QR Code dinamis untuk verifikasi instan integritas dokumen.
- Portal verifikasi publik mandiri (`/verifikasi-sertifikat`) dengan kepatuhan *minimum disclosure* (melindungi data sensitif seperti NIP/NIK pendaftar).

### 6. Flexible Modular RBAC Hub
- 90 permission granular yang terdistribusi ke dalam 12 modul domain: `dashboard`, `landing`, `access`, `learning`, `bunga`, `regulation`, `communication`, `certificate`, `intelligence`, `reports`, `survey`, dan `system`.
- Perlindungan mutasi dinamis (*Dynamic Mutation Guard*): Menolak aksi mutasi data (`POST`, `/tambah`, `/edit`, `/hapus`) pada pengguna dengan hak akses read-only.
- Matriks Hak Akses visual (`/dashboard/akses/role/matrix`) dengan aksi massal (*bulk grant/revoke*).
- Sinkronisasi katalog hak akses 1-klik yang bersifat *idempotent*.

### 7. Landing CMS & Spotlight Hub
- 100% konten beranda dikontrol dinamis (Hero Slider, Testimoni, Tim Lembaga, Mitra/Portal Eksternal, Pengumuman).
- Spotlight Hub terpadu (`/dashboard/landing/sorotan-carousel`) untuk mengontrol slider hero dan badge unggulan lintas 5 modul: Arena Belajar, Bunga Rampai, Regulasi, Berita, dan Event.

### 8. Government Intelligence & Laporan Eksekutif
- Radar Kematangan PBJ Daerah: Analisis indikator kelembagaan, proses pengadaan, dan SDM.
- Analisis kesenjangan (*competency gap*) formasi PBJ per Organisasi Perangkat Daerah (OPD).
- Pusat laporan universal ber-kop resmi Pemprov Lampung dengan ekspor dokumen CSV/PDF.

---

## Arsitektur & Batasan Template

Aplikasi menerapkan pemisahan dependensi antarmuka (*UI boundary*) secara tegas guna mencegah konflik CSS/JS:

| Area Sistem | Template Basis | Controller Namespace | View Directory | Asset Directory |
|---|---|---|---|---|
| **Landing Page** | FiStudy | `App\Controllers\Landing\` | `app/Views/landing/` | `public/assets/main/` |
| **Workspace** | FiStudy | `App\Controllers\Workspace\` | `app/Views/main/workspace/` | `public/assets/main/` |
| **Auth Terpadu** | WowDash | `App\Controllers\Dashboard\Auth\` | `app/Views/dashboard/auth/` | `public/assets/dashboard/` |
| **Dashboard Back-Office** | WowDash | `App\Controllers\Admin\`, `Communication\`, dll. | `app/Views/dashboard/` | `public/assets/dashboard/` |

> [!IMPORTANT]
> Aset `public/assets/main/` dan `public/assets/dashboard/` diisolasi sepenuhnya. Tidak diperkenankan memanggil stylesheet atau skrip lintas template tanpa kontrak arsitektur tertulis.

---

## Prasyarat Sistem & Dependensi

- **PHP:** Versi **8.3** atau lebih baru.
- **Ekstensi PHP Wajib:**
  `intl`, `mbstring`, `mysqli`, `curl`, `fileinfo`, `gd`, `openssl`, `json`, `pdo_mysql`.
- **Database Engine:** **MySQL 8.4+** atau MariaDB 10.11+.
- **Package Manager:** **Composer 2.x**.
- **Web Server:** Apache, Nginx, atau Laragon dengan *Document Root* diarahkan ke folder `public/`.

---

## Panduan Instalasi & Konfigurasi

### 1. Clone Repository
```bash
git clone https://github.com/begawipbj/begawipengadaan.git
cd begawipengadaan
```

### 2. Install Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin berkas contoh `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi koneksi database dan URL aplikasi:
```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = db_begawipbj
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBDebug  = true
```

### 4. Generate Kunci Enkripsi
```bash
php spark key:generate
```

---

## Inisialisasi Database & Seeder

### 1. Import Skema Awal & Master Wilayah
Import berkas DDL skema produksi dan master wilayah Indonesia ke database target:
```bash
mysql -u root -p db_begawipbj < schema-fresh-v4-3.sql
mysql -u root -p db_begawipbj < database/reference/wilayah.sql
```

### 2. Jalankan Migrasi Database
Pastikan seluruh 16 migrasi database berstatus termigrasi:
```bash
php spark migrate
```

### 3. Sinkronisasi Katalog Hak Akses (RBAC)
Daftarkan 90 permission katalog sistem dan hubungkan ke peran kanonikal (idempotent):
```bash
php spark permission:sync
```

### 4. Seed Data Awal & Konten Autentik PBJ
Isi data awal lembaga, OPD, peran PBJ, modul pembelajaran demo, dan konten berita/event:
```bash
php spark opd:seed            # Master Perangkat Daerah (OPD) & Peran Fungsional PBJ
php spark arena:seed:demo     # Kelas demo autentik PBJ, pengajar, kurikulum, dan tugas
php spark news:seed           # Publikasi berita & kategori berita PBJ
php spark event:seed          # Agenda kegiatan, workshop, dan webinar PBJ
php spark knowledge:seed      # Artikel pengetahuan Bunga Rampai & studi kasus
php spark regulation:seed     # Bank naskah regulasi hukum PBJ
```

### 5. Jalankan Development Server
```bash
php spark serve
```
Buka peramban pada alamat `http://localhost:8080`.

---

## Akun Pengujian Lokal (Development)

Untuk mempermudah pengujian alur bisnis di lingkungan lokal, akun demo berikut tersedia secara default (kata sandi standar: `Password123!`):

| Role / Peran | Username | Email | Lingkup Akses Utama |
|---|---|---|---|
| **Super Administrator** | `super.admin` | `superadmin@lampungprov.go.id` | Akses 100% penuh seluruh modul dan konfigurasi |
| **Administrator PBJ** | `admin.pbj` | `admin.pbj@lampungprov.go.id` | Manajemen operasional PBJ, konten, dan persetujuan |
| **Operator LMS** | `operator.lms` | `operator.lms@lampungprov.go.id` | Pengelolaan kurikulum, peserta, dan presensi event |
| **Trainer / Instruktur** | `trainer.pbj` | `trainer.pbj@lampungprov.go.id` | Ruang kelas pengajar, penilaian tugas, dan bank soal |
| **Peserta Pelatihan** | `ani.peserta` | `ani.peserta@lampungprov.go.id` | Mengikuti kelas, kuis, tugas, dan portofolio sertifikat |

---

## Perintah Operasional Spark CLI

BegawiPengadaan menyediakan perintah CLI khusus untuk memelihara dan mengaudit sistem:

```bash
# ----------------------------------------------------------------------
# AUDIT OTOMATIS SISTEM (END-TO-END)
# ----------------------------------------------------------------------
php spark landing:full:audit      # Audit 33 rute publik, CMS bindings, dan CSRF token
php spark dashboard:full:audit    # Audit navigasi sidebar, layout WowDash, dan RBAC gate
php spark workspace:full:audit    # Audit alur 4 peran workspace dan integritas tabel
php spark workspace:smoke         # Smoke test 41 titik interaksi layout workspace

# ----------------------------------------------------------------------
# SUITE PENGUJIAN FUNGSIONAL & KEAMANAN
# ----------------------------------------------------------------------
php spark test:registration       # Verifikasi 13 skenario pendaftaran (ASN, Penyedia, Akademisi, Umum)
php spark test:portal-security    # Audit 45 titik keamanan portal (XSS, Path Traversal, Rate Limit)
php spark test:hero-spotlight     # Verifikasi Hero Slider & Badge Unggulan pada 5 modul publik
php spark test:landing-cms        # Pengujian 97 assertions CRUD konten beranda CMS
php spark test:rbac-custom-roles  # Pengujian isolasi hak akses role kustom dan permission gate

# ----------------------------------------------------------------------
# PEMELIHARAAN & SINKRONISASI
# ----------------------------------------------------------------------
php spark permission:sync         # Sinkronisasi 90 katalog permission sistem ke basis data
php spark cache:clear             # Membersihkan seluruh cache sistem dan view runtime
```

---

## Struktur Direktori Proyek

```text
begawipengadaan/
├── app/
│   ├── Commands/              # Perintah kustom Spark CLI (Audit, Seeder, Testing)
│   ├── Config/                # Konfigurasi aplikasi (Database, Routes, Auth, Security)
│   ├── Controllers/
│   │   ├── Admin/             # Controller tata kelola dashboard back-office
│   │   ├── Dashboard/Auth/    # Controller autentikasi tunggal (Login, Register, Reset)
│   │   ├── Landing/           # Controller halaman portal publik (FiStudy)
│   │   └── Workspace/         # Controller ruang kerja interaktif pengguna (FiStudy)
│   ├── Database/
│   │   ├── Migrations/        # 16 file migrasi database resmi (Zero Migration Policy)
│   │   └── Seeds/             # Seeder data master dan katalog
│   ├── Filters/               # Filter otorisasi rute dan pengamanan HTTP
│   ├── Models/                # Model data CodeIgniter 4
│   ├── Services/              # Domain Services (Auth, Learning, Knowledge, Identity, dll.)
│   └── Views/
│       ├── dashboard/         # Antarmuka back-office & form auth (Template WowDash)
│       ├── landing/           # Antarmuka portal publik (Template FiStudy)
│       └── main/workspace/    # Antarmuka workspace terautentikasi (Template FiStudy)
├── database/
│   ├── reference/             # Data referensi master (wilayah administratif Indonesia)
│   ├── permissions_catalog.json # Katalog definisi 90 permission sistem format JSON
│   └── permissions_sync.sql   # Naskah SQL sinkronisasi hak akses
├── dokumen/                   # Dokumentasi teknis & spesifikasi arsitektur resmi
├── public/
│   ├── assets/
│   │   ├── dashboard/         # Aset khusus template WowDash (CSS, JS, Fonts, Icons)
│   │   └── main/              # Aset khusus template FiStudy (CSS, JS, Media)
│   └── index.php              # Pintu masuk utama aplikasi web
├── writable/                  # Direktori cache, sesi, upload terproteksi, dan log
├── schema-fresh-v4-3.sql      # DDL skema basis data produksi versi v4.3
├── composer.json              # Dependensi pustaka PHP
└── spark                      # Console runner CodeIgniter 4
```

---

## Keamanan & Integritas Sistem

1. **Zero Migration Policy:** Seluruh penyempurnaan fitur beroperasi di atas **tepat 16 berkas migrasi database** tanpa ada *schema drift* atau perubahan DDL yang merusak konsistensi data produksi.
2. **Path Traversal & Resource Containment:** Seluruh pengunduhan dokumen, materi pembelajaran, dan sertifikat diproteksi oleh method `resolveSafePath()` untuk mencegah serangan path traversal.
3. **Pembersihan Konten Berita (Stored XSS Protection):** Sanitasi otomatis tag HTML pada write-time (`CommunicationService::sanitizeHtmlContent()`) yang menetralkan skrip berbahaya dan atribut `onerror`/`javascript:`.
4. **Perlindungan Data Pribadi (Privacy by Design):** Data identitas terlindungi seperti NIK pendaftar, NIP pegawai, dan kontak pribadi tidak pernah dipaparkan pada antarmuka publik atau respon verifikasi QR Code sertifikat.
5. **Rate Limiting:** Proteksi *throttler* aktif pada endpoint login, registrasi, formulir kontak publik, kuis, dan permohonan pemulihan kata sandi.

---

## Indeks Dokumentasi Teknis

Dokumentasi arsitektur dan spesifikasi mendalam tersimpan pada direktori [`dokumen/`](file:///c:/laragon/www/begawipengadaan/dokumen):

- [Kontrak Autentikasi & RBAC (`auth-and-rbac.md`)](file:///c:/laragon/www/begawipengadaan/dokumen/auth-and-rbac.md): Matriks 90 permission, penegakan peran, dan siklus akun.
- [Kontrak Rute & Filter (`route-contract.md`)](file:///c:/laragon/www/begawipengadaan/dokumen/route-contract.md): Pemetaan lengkap metode HTTP, filter, dan nama rute.
- [Batasan Antarmuka (`ui-boundary.md`)](file:///c:/laragon/www/begawipengadaan/dokumen/ui-boundary.md): Isolasi template FiStudy vs WowDash.
- [Kamus Data Database (`data-dictionary.md`)](file:///c:/laragon/www/begawipengadaan/dokumen/data-dictionary.md): Definisi kolom, tipe data, dan constraint tabel.
- [Arsitektur Skema Database (`database-blueprint.md`)](file:///c:/laragon/www/begawipengadaan/dokumen/database-blueprint.md): Hubungan antar-tabel dan indeks performa.
- [Kebijakan Penyimpanan File (`file-storage-policy.md`)](file:///c:/laragon/www/begawipengadaan/dokumen/file-storage-policy.md): Manajemen berkas privat dan otorisasi akses.
- [Kontrak Alur Kerja & State Machine (`workflow-contract.md`)](file:///c:/laragon/www/begawipengadaan/dokumen/workflow-contract.md): State diagram kelas, registrasi, sertifikat, dan persetujuan.
- [Kebijakan Pendaftaran Akun (`registration-policy.md`)](file:///c:/laragon/www/begawipengadaan/dokumen/registration-policy.md): Aturan validasi profil ASN, Penyedia, Akademisi, dan Umum.

---

## Lisensi & Hak Cipta

- **Aplikasi BegawiPengadaan:** Hak Cipta © 2026 Pemerintah Provinsi Lampung. Hak cipta dilindungi undang-undang.
- **Aset & Pustaka Pihak Ketiga:** Template FiStudy, WowDash, Iconify Solar Icons, library JavaScript, serta paket Composer tunduk pada lisensi masing-masing penyedia.

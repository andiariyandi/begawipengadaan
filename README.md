# BegawiPengadaan

## Platform Transformasi Digital Pengadaan Barang/Jasa Pemerintah Provinsi Lampung

BegawiPengadaan adalah platform digital terpadu untuk mendukung ekosistem Pengadaan Barang/Jasa Pemerintah (PBJ) di lingkungan Pemerintah Provinsi Lampung.

Platform ini menyatukan pembelajaran digital (*Arena Belajar / LMS*), pusat pengetahuan (*Bunga Rampai / KMS*), repositori regulasi PBJ, publikasi dan agenda (*Berita & Event*), sertifikat digital terverifikasi (*E-Sertifikat*), data kompetensi SDM PBJ (*Government Intelligence*), statistik publik, serta sistem otorisasi modular fleksibel (*Flexible Modular RBAC*) dalam satu aplikasi monolitik yang aman, terstruktur, dan dapat diaudit secara penuh (*Full Audit Trail*).

BegawiPengadaan dibangun menggunakan **CodeIgniter 4 (AppStarter)** dengan adaptasi antarmuka produksi dari dua source template HTML:
- **FiStudy:** Didedikasikan untuk **Landing Page (Public Area)** dan **Workspace** pengguna.
- **WowDash:** Didedikasikan untuk **Auth Terpadu** dan **Dashboard Back-office**.

---

## Tiga Pilar Fungsional Utama Sistem

Sistem BegawiPengadaan terbagi secara tegas ke dalam 3 area fungsional utama berdasarkan target audiens, hak akses, layout, dan interaksinya:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                               BegawiPengadaan                          │
└───────────────────┬────────────────┬───────────────────┬───────────────┘
                    │                │                   │
         ┌──────────▼──────────┐     │        ┌──────────▼──────────┐
         │ 1. LANDING PAGE     │     │        │ 3. DASHBOARD        │
         │    & PUBLIC AREA    │     │        │    BACK-OFFICE      │
         ├─────────────────────┤     │        ├─────────────────────┤
         │ • Beranda Utama     │     │        │ • Auth Terpadu      │
         │ • Berita & Populer  │     │        │ • KPI Internal      │
         │ • Event & Agenda    │     │        │ • Modular RBAC Hub  │
         │ • Arena Belajar     │     │        │ • Pengguna & Verif  │
         │ • Bunga Rampai      │     │        │ • Komunikasi Berita │
         │ • Repositori Hukum  │     │        │ • Event & Presensi  │
         │ • Statistik Publik  │     │        │ • E-Sertifikat      │
         │ • Verifikasi Sertif │     │        │ • Arena Belajar LMS │
         │ • Tentang & Kontak  │     │        │ • Gov Intelligence  │
         └──────────┬──────────┘     │        │ • Laporan & Audit   │
                    │                │        └──────────┬──────────┘
             Template: FiStudy       │            Template: WowDash
             Asset: assets/main/     │            Asset: assets/dashboard/
                                     │
                          ┌──────────▼──────────┐
                          │ 2. WORKSPACE        │
                          │    PENGGUNA         │
                          ├─────────────────────┤
                          │ • Multi-role Portal │
                          │ • Participant Hub   │
                          │ • Trainer Portal    │
                          │ • Operator Desk     │
                          │ • Executive Admin   │
                          │ • Profil & Presensi │
                          │ • Bookmark Lintas   │
                          │ • Kotak Notifikasi  │
                          └──────────┬──────────┘
                                     │
                              Template: FiStudy
                              Asset: assets/main/
```

### 1. Landing Page dan Public Area (FiStudy)
* **Pengguna:** Masyarakat umum, aparatur pemerintah, penyedia barang/jasa, akademisi, dan publik luas tanpa kewajiban login.
* **Fungsi Utama:**
  * Akses informasi publik pengadaan barang/jasa Pemerintah Provinsi Lampung.
  * Pencarian dan pembacaan berita PBJ dengan counter baca dinamis, kurasi berita terpopuler, dan 5 berita utama (*Featured News*).
  * Penelusuran agenda, webinar, workshop, dan event PBJ (tampilan Grid, List, dan Kalender interaktif).
  * Eksplorasi katalog pembelajaran (*Arena Belajar*), artikel pengetahuan (*Bunga Rampai*), dan peraturan (*Regulasi PBJ*).
  * Layanan mandiri verifikasi keaslian e-sertifikat melalui nomor seri atau pemindaian QR code (`/verifikasi-sertifikat`).
  * Informasi profil organisasi dan formulir permohonan informasi/aspirasi publik (`/tentang` dan `/kontak`).

### 2. Workspace Pengguna Terautentikasi (FiStudy)
* **Pengguna:** Pengguna terdaftar dengan role operasional aktif (*Participant, Trainer, Operator, Administrator*).
* **Fungsi Utama:**
  * **Participant (Peserta):** Mengikuti kelas pembelajaran dengan modular state engine, materi silabus, pre-test, kuis, penugasan, post-test, presensi event, riwayat sertifikat resmi terbit, dan evaluasi kelas.
  * **Trainer (Pengajar):** Mengelola kelas binaan berbasis ruang kelas tab interaktif (*Overview*, *Materi*, *Peserta*, *Assessment*, *Assignment*, *Progres*), mengunggah silabus, membuat kuis, dan review penilaian tugas peserta.
  * **Operator:** Melakukan verifikasi administratif profil pendaftar, penugasan pengajar kelas, manajemen kehadiran event, validasi eligibilitas sertifikat, dan override evaluasi penugasan.
  * **Administrator:** Ringkasan eksekutif platform, monitoring capaian pembelajaran makro, dan jejak audit aktivitas sistem.
  * **Shared Fitur:** Manajemen profil terverifikasi, personalisasi bookmark lintas konten, dan pusat notifikasi masuk.

### 3. Dashboard Back-Office & Auth Terpadu (WowDash)
* **Pengguna:** Administrator, Super Admin, dan verifikator dengan hak akses (*permission*) back-office.
* **Fungsi Utama:**
  * **Auth Terpadu:** Pintu gerbang login, registrasi akun multi-step, verifikasi identitas, dan pemulihan kata sandi bagi seluruh pengguna.
  * **Manajemen Akses & Flexible Modular RBAC:** Pengelolaan pengguna dan profil terpadu, katalog 90 permission granular, matriks hak akses dinamis, manajemen unit organisasi (OPD), dan peran pengadaan.
  * **Pusat Komunikasi (Berita & Event-Agenda):** Kontrol penuh artikel berita, upload media, kurasi berita utama, siklus event (Draft s.d. Finished), kode presensi unik, batch generate sertifikat, dan automasi waitlist.
  * **Tata Kelola Konten & Data:** Manajemen Arena Belajar LMS, Bunga Rampai KMS, Repositori Regulasi PBJ, dan Template Sertifikat Digital.
  * **Government Intelligence & Laporan:** Pemetaan radar kematangan PBJ daerah, analisis gap kompetensi SDM PBJ per OPD, pusat laporan dengan kop resmi Pemprov, dan audit trail lengkap.

---

## Arsitektur Antarmuka & Kontrak Template

Sistem BegawiPengadaan memisahkan dependensi antarmuka secara mutlak untuk mencegah konflik CSS/JS antar-template:

| Area | Template | Controller Root | View Root | Asset Root |
|---|---|---|---|---|
| **Landing Page** | FiStudy | `app/Controllers/Landing/` | `app/Views/landing/` | `public/assets/main/` |
| **Workspace** | FiStudy | `app/Controllers/Workspace/` | `app/Views/main/workspace/` | `public/assets/main/` |
| **Auth Terpadu** | WowDash | `app/Controllers/Dashboard/Auth/` | `app/Views/dashboard/auth/` | `public/assets/dashboard/` |
| **Dashboard Admin**| WowDash | `app/Controllers/Admin/`, `Communication/`, `Learning/`, `Knowledge/`, `Regulation/`, `Certificate/`, `Intelligence/` | `app/Views/dashboard/` | `public/assets/dashboard/` |
| **Legacy Archives**| FiStudy / WowDash | `archive/controllers_legacy/` | `archive/views_legacy/` | *Template & controller pensiun* |
| **Source Reference**| Reference | *Non-production (dilarang dirender)* | `app/Views/reference/` | *Source template original* |

> [!IMPORTANT]
> - Asset `public/assets/main/` hanya digunakan untuk Landing Page dan Workspace.
> - Asset `public/assets/dashboard/` hanya digunakan untuk Auth dan Dashboard Back-office.
> - Tidak diperbolehkan menyilangkan asset antar-template tanpa dokumentasi arsitektur eksplisit.

---

## Panduan Instalasi & Pengembangan (*Quick Start*)

### Prasyarat Sistem
- **PHP:** Versi 8.3 atau lebih baru.
- **Ekstensi PHP Wajib:** `intl`, `mbstring`, `mysqli`, `openssl`, `curl`, `fileinfo`, `gd`, `json`, `pdo_mysql`.
- **Database Engine:** MySQL 8.4 atau MariaDB setara.
- **Dependency Manager:** Composer 2.x.
- **Web Server / Tooling:** Laragon, Apache, atau Nginx dengan document root mengarah ke folder `public/`.

### Langkah Instalasi

1. **Clone Repository:**
   ```bash
   git clone <repository-url> begawipengadaan
   cd begawipengadaan
   ```

2. **Install Dependensi PHP:**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment:**
   Salin file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database dan URL aplikasi:
   ```bash
   cp .env.example .env
   ```
   Atur parameter utama pada `.env`:
   ```ini
   CI_ENVIRONMENT = development

   app.baseURL = 'http://localhost:8080/'

   database.default.hostname = localhost
   database.default.database = db_begawipbj
   database.default.username = root
   database.default.password = 
   database.default.DBDriver = MySQLi
   database.default.DBDebug = true
   ```

4. **Generate Encryption Key:**
   ```bash
   php spark key:generate
   ```

5. **Inisialisasi Database:**
   Import skema SQL fresh dan data referensi wilayah ke database `db_begawipbj`:
   ```bash
   mysql -u root -p db_begawipbj < schema-fresh-v4-3.sql
   mysql -u root -p db_begawipbj < database/reference/wilayah.sql
   ```

6. **Sinkronisasi Katalog Hak Akses (RBAC Modular Catalog):**
   Jalankan seeder permission katalog sistem (idempotent, otomatis mendaftarkan 90 permission ke 12 modul domain):
   ```bash
   php spark permission:sync
   ```

7. **Seed Data Awal & Konten Dummy Autentik PBJ:**
   ```bash
   php spark event:seed        # Seed agenda, webinar, dan workshop PBJ
   php spark news:seed         # Seed berita pengadaan dan kategori
   php spark knowledge:seed    # Seed artikel Bunga Rampai
   php spark regulation:seed   # Seed regulasi dan peraturan PBJ
   ```

8. **Jalankan Development Server:**
   ```bash
   php spark serve
   ```
   Akses aplikasi di browser melalui `http://localhost:8080`.

---

## Kontrak Rute & Endpoint Utama

### 1. Rute Publik (Landing Page)
```text
/                              → Beranda Utama (Zero Hardcoded CMS)
/tentang                       → Profil Lembaga & Visi Misi
/kontak                        → Formulir Aspirasi & Helpdesk PBJ
/arena-belajar                 → Showcase & Katalog Pembelajaran Publik
/bunga-rampai                  → Pusat Pengetahuan & Artikel PBJ
/regulasi                      → Repositori Regulasi PBJ (Detail, Preview, Unduh)
/berita                        → Publikasi Berita PBJ (Populer, Kurasi, Kategori)
/event                         → Agenda, Webinar & Workshop (Grid, List, Kalender)
/statistik                     → Agregat Statistik Publik PBJ
/verifikasi-sertifikat         → Layanan Verifikasi Mandiri E-Sertifikat
/verify/certificate/(:segment) → Endpoint Pemindaian QR Code Sertifikat
```

### 2. Rute Workspace (FiStudy)
```text
/workspace                     → Canonical Role Dispatcher (Auto-route sesuai role)
/workspace/profil              → Pengaturan Profil Terpadu (Akun, ASN, Penyedia, Akademik, Umum)
/workspace/aktivitas           → Log Aktivitas Pengguna
/workspace/notifikasi          → Pusat Notifikasi Masuk
/workspace/bookmark            → Koleksi Konten & Regulasi Tersimpan
/workspace/peserta/*           → Pembelajaran Aktif, Tugas, Kuis, Event, Presensi & Sertifikat Saya
/workspace/trainer/*           → Manajemen Kelas Binaan Pengajar, Bank Materi, Soal & Review Tugas
/workspace/operator/*          → Pusat Operasional, Approval, Verifikasi Profil, Penugasan & Presensi
/workspace/admin               → Dashboard Pusat Eksekutif Workspace
```

### 3. Rute Dashboard Back-Office & Auth (WowDash)
```text
/dashboard/masuk               → Autentikasi Pengguna Terpadu
/dashboard/daftar              → Registrasi Multi-step Terpadu
/dashboard/lupa-kata-sandi     → Pemulihan Akun
/dashboard/status-akun         → Informasi Status Akun Pengguna

/dashboard                     → Ringkasan Eksekutif & KPI Internal
/dashboard/landing/sorotan-carousel → Spotlight Hub (Pusat Sorotan Lintas 5 Modul)
/dashboard/akses/pengguna      → Manajemen Pengguna Terpadu & Antrean Verifikasi Akun/Profil
/dashboard/akses/role          → Katalog Peran & Manajemen Role
/dashboard/akses/role/matrix   → Matriks Hak Akses Granular Role vs Permission
/dashboard/akses/permission    → Katalog Permission Sistem (Sync, Import, Edit, Deactive)
/dashboard/akses/permohonan-perubahan-profil → Review Independen Perubahan Profil
/dashboard/akses/permohonan-role → Review Permohonan Peningkatan Role
/dashboard/akses/unit-organisasi → Manajemen Perangkat Daerah / OPD
/dashboard/akses/peran-pengadaan → Klasifikasi & Penugasan Fungsional PBJ (PPK, Pokja, dsb.)
/dashboard/komunikasi/berita   → CRUD Berita, Kategori, Kurasi Berita Utama, Hit Counter
/dashboard/komunikasi/event-agenda → Manajemen Event, Kode Presensi, Batch Sertifikat
/dashboard/pembelajaran/*      → Unified Class Console, Kurikulum, Asesmen & Duplikasi Batch
/dashboard/bunga-rampai/*      → Manajemen Artikel, FAQ, Studi Kasus, Media & Galeri
/dashboard/regulasi/*          → Manajemen Regulasi, Versi & Relasi Hukum
/dashboard/sertifikat/*        → Direktori Master Sertifikat, Template Visual & Live PDF Preview
/dashboard/approvals/*         → Sentralisasi Antrean Persetujuan (Central Approval Hub)
/dashboard/intelligence/*      → Radar Kematangan PBJ Daerah & Simulasi Formasi Gap OPD
/dashboard/laporan/*           → Pusat Laporan & Universal Export (6 Kategori, Kop Resmi Pemprov)
/dashboard/survei-kepuasan     → Indeks Kepuasan Masyarakat (IKM) PermenPAN-RB No. 14/2017
/dashboard/sistem/pengaturan   → Pusat Pengaturan Terpadu (Umum, Keamanan, Sertifikat, Maintenance)
/dashboard/sistem/audit-trail  → Sentralisasi Audit Trail & Ekspor Jejak Aktivitas Keamanan
```

---

## Kapabilitas Riil & Penyempurnaan Arsitektur Terkini

### A. Flexible Modular Role-Based Access Control (RBAC) System
1. **Taksonomi Role Kanonikal Standar:**
   * Menyelaraskan seluruh role ke standar kanonikal database: `super_administrator`, `administrator`, `operator`, `trainer`, `evaluator`, `peserta`, `public_user`.
2. **Katalog 90 Permission Granular Lintas 12 Domain Modul:**
   * Terdiri dari 82 permission modular baru (`*.view`, `*.manage`, `*.approve`, `*.create`, `*.update`, `*.delete`) ditambah 8 permission alias untuk kompatibilitas penuh.
   * Dikelompokkan ke dalam 12 domain modul resmi: `dashboard`, `landing`, `access`, `learning`, `bunga`, `regulation`, `communication`, `certificate`, `intelligence`, `reports`, `survey`, `system`.
3. **Pemberlakuan Rute Dinamis (*Dynamic Zone Enforcement*):**
   * Filter `AuthenticationFilter` secara dinamis memetakan 25+ zona rute dashboard terhadap daftar permission pengguna.
   * Dilengkapi pengaman mutlak mutasi (*Mutation Guard*): pengguna dengan role read-only (hanya memiliki hak `*.view`) otomatis dicegat dengan respons `HTTP 403 Forbidden` saat mencoba mengakses aksi mutasi (`POST` atau rute `/tambah`, `/edit`, `/hapus`).
4. **Visibilitas Sidebar & Menu Adaptif:**
   * Sidebar WowDash (`sidebar.php`) secara cerdas menyembunyikan atau menampilkan modul, item menu, dan judul kategori sesuai izin yang dimiliki pengguna (`$authService->canModule()` dan `$authService->canAny()`).
5. **Super Administrator Full Bypass:**
   * Akun dengan peran `super_administrator` otomatis memperoleh akses penuh (100% bypass) ke seluruh fitur dan rute baru tanpa perlu konfigurasi tambahan.
6. **Matriks Hak Akses Granular (*Role vs Permission Matrix*):**
   * Antarmuka matriks di `/dashboard/akses/role/matrix` menyajikan seluruh permission yang dikelompokkan rapi per modul domain.
   * Dilengkapi tombol aksi massal (*Bulk Action*): centang semua izin per role ("Semua") dan centang per domain modul ("Modul").
   * Integrasi tombol pintasan **`[Edit]`** individual pada header masing-masing role untuk pengubahan hak akses yang terisolasi dan aman.
   * Proteksi mutlak Super Administrator agar hak aksesnya tidak pernah hilang atau terhapus secara tidak sengaja.
7. **Sinkronisasi Katalog 1-Klik untuk Server Deploy:**
   * Tombol **"Sinkronkan Katalog Sistem"** dan **"Import JSON / CSV"** langsung di Dashboard (`/dashboard/akses/permission`), memungkinkan pembaruan 90 hak akses di server produksi tanpa memerlukan akses SSH atau terminal.
   * Bersifat **100% Idempotent**: operasi dapat dijalankan berkali-kali tanpa menghasilkan duplikasi atau redundansi data di tabel `permissions` maupun `role_permissions`.
8. **Pengujian Khusus Role Kustom:**
   * Dilengkapi test suite `php spark test:rbac-custom-roles` yang menguji 4 role kustom (Role Khusus Bunga Rampai, Khusus Regulasi PBJ, Khusus Arena Belajar, dan Auditor Read-Only) dengan 14/14 assertions kelulusan.

---

### B. Penyatuan Verifikasi Pengguna & Profil (*Unified Verification*)
1. **Penyatuan Halaman & Tab Terpadu:**
   * Tab navigasi sub-menu disatukan menjadi **`Pengguna & Verifikasi Akun`** di `/dashboard/akses/pengguna`.
   * Tab **`Perubahan Profil`** (`/dashboard/akses/permohonan-perubahan-profil`) tetap dipertahankan independen.
2. **Logika Terpadu: Sekali Approve Akun = Otomatis Approve Profil:**
   * Diatur pada level service `UserManagementService::activate()`. Menyetujui akun pengguna otomatis memvalidasi email, mengaktifkan status akun (`users.account_status = 'active'`), dan menyetujui seluruh antrean berkas profil terkait (`profile_verifications.status = 'approved'`).
   * Tersedia pula aksi penolakan terpadu (`UserManagementService::reject()`) yang menolak akun sekaligus antrean profil dengan pencatatan alasan dan jejak audit.
3. **Segmentasi Tab & Tombol Aksi Cepat:**
   * Halaman pengguna menyajikan 4 filter sub-tab dengan counter real-time: **Semua Pengguna**, **Antrean Verifikasi (Akun & Profil)**, **Pengguna Aktif**, dan **Ditangguhkan / Ditolak**.
   * Tabel daftar menyertakan tombol cepat **`[Setujui]`** langsung di baris pengguna berstatus pending.
   * Halaman detail pengguna (`/dashboard/akses/pengguna/{id}`) menampilkan banner verifikasi, data profil spesifik (NIP, Unit Kerja, NIB, NPWP, Perusahaan), dan riwayat verifikasi profil dalam satu tampilan utuh.

---

### C. Detail Permission Komprehensif & Kontrol Deaktivasi
1. **Eliminasi Konten Statis / Hardcoded:**
   * Halaman detail permission (`/dashboard/akses/permission/{id}`) kini 100% dinamis menyajikan data riil dari database.
2. **Indikator & Metrik Status Operasional:**
   * 4 kartu metrik: Status Hak Akses (Aktif Digunakan vs Nonaktif/Dicabut), Modul Domain, Klasifikasi Izin (Sistem Inti vs Kustom), dan Jumlah Pengguna Terdampak.
3. **Tombol Deactive & Kontrol Fleksibel:**
   * **Nonaktifkan Permission (Deactive):** Mencabut izin dari seluruh peran di sistem dengan satu klik dan mencatat jejak audit `permission.deactivate`.
   * **Aktifkan ke Administrator:** Memasang kembali hak akses ke peran Administrator dan Super Administrator.
   * **Pencabutan Per-Peran (*Revoke Role*):** Mencabut izin dari satu peran tertentu secara selektif.
   * **Alat Pemberian Cepat (*Quick Grant Tool*):** Memberikan izin ke peran lain langsung dari halaman detail.
   * **Modal Ubah Data:** Mengedit Nama Izin, Domain Modul, dan Deskripsi Fungsional.
   * **Hapus Permission:** Tersedia secara aman khusus untuk permission kustom (`is_system = 0`).

---

### D. Fitur Komunikasi (Berita & Event-Agenda)
1. **Dynamic View Counter (+1 Hit Count):**
   * Setiap pembacaan berita menambah hit counter +1 berbasis sesi secara aman dan efisien, menjadi dasar kurasi **Berita Terpopuler**.
2. **Kurasi Hingga 5 Berita Utama (*Featured News*):**
   * Administrator dapat menandai dan mengatur hingga 5 artikel sebagai Berita Utama untuk ditayangkan pada carousel beranda dan banner headline.
3. **Siklus Hidup Event Komprehensif:**
   * Mendukung siklus status: `draft`, `coming_soon`, `open_registration`, `full`, `finished`, dan `cancelled`.
4. **Presensi Berbasis Kode Unik (*Attendance Code*):**
   * Admin men-generate kode presensi unik (misal: `BGW-EVT-XXXXX`), membuka presensi dengan pencatatan waktu buka, dan menutup presensi saat sesi berakhir.
   * Peserta melakukan input kode presensi secara mandiri dengan pencegahan absensi ganda (*double check-in prevention*).
5. **Penerbitan Sertifikat Massal (*Batch Certificate Generation*):**
   * Mengotomatisasi penerbitan e-sertifikat resmi dalam 1 klik khusus bagi peserta berstatus `registered` yang hadir (`present`) pada event berstatus `finished`.
6. **Sistem Antrean Waitlist & Promosi Otomatis:**
   * Pendaftar yang melebihi kuota masuk ke antrean `waitlisted`. Saat terjadi pembatalan (`cancelled`), sistem mempromosikan antrean waitlist secara otomatis dan atomik.
7. **Arsitektur Scroll Anti-Getar (*Jitter-Free View*):**
   * Mengeliminasi bug getaran/flicker di ujung bawah viewport pada layar resolusi tinggi Windows melalui penguncian single scroll container `html { overflow-y: scroll !important; }` dan pembersihan GPU layer thrashing.

---

### E. Sertifikat Digital & Verifikasi Mandiri
1. **Integritas Sumber Sertifikat:**
   * Mencegah duplikasi melalui aturan database `UNIQUE(source_type, source_id)` baik untuk pembelajaran (*enrollment*) maupun event (*registration*).
2. **Verifikasi Publik Minimum Disclosure:**
   * Halaman verifikasi publik (`/verifikasi-sertifikat`) dan pemindai QR code menampilkan keaslian sertifikat tanpa membocorkan data pribadi terproteksi (NIK/NIP/kontak).

---

### F. Stabilisasi Ekosistem & Zero Migration Policy
1. **Komitmen Zero Database Schema Migrations:**
   * Seluruh penyempurnaan fitur dan otorisasi beroperasi di atas **tepat 16 file migrasi database** di `app/Database/Migrations/`. Tidak ada perubahan struktur kolom yang merusak database produksi.
2. **Zero-Hardcoded Data Landing Page:**
   * 100% konten beranda dikontrol dinamis melalui 8 tabel CMS dan Spotlight Hub lintas 5 modul.
3. **Aksesibilitas Dark Mode & Zero Contrast Bug:**
   * Memenuhi standar kontras WCAG pada Light dan Dark mode sesuai token adaptif WowDash.
4. **Advanced DataTables Engine:**
   * Integrasi `begawi-datatable.js` dengan fitur multi-column sort, auto-unwrap responsive table, dan ekspor CSV UTF-8 ber-BOM.

---

## Suite Pengujian Otomatis & Spark Commands

BegawiPengadaan dilengkapi dengan rangkaian test suite berbasis CodeIgniter CLI Spark untuk memverifikasi fungsionalitas end-to-end secara otomatis:

```bash
# ====================================================================
# AUTOMATED FULL AUDIT ENGINES (LANDING, DASHBOARD, WORKSPACE)
# ====================================================================
php spark landing:full:audit       # Audit menyeluruh 33 rute publik, navigasi, CMS, CSRF, & view cleanup
php spark dashboard:full:audit     # Audit menyeluruh 92 rute sidebar, alur kerja, token dark mode, & DB
php spark workspace:full:audit     # Audit menyeluruh 58 rute workspace 4 role, security gate, & 22 tabel DB
php spark workspace:smoke          # Smoke test 41 checks layout FiStudy, modular journey, & live sync

# ====================================================================
# RBAC MODULAR & MANAJEMEN AKSES
# ====================================================================
php spark permission:sync          # Sinkronisasi 90 katalog permission sistem ke DB & role kanonikal
php spark test:rbac-custom-roles   # Pengujian 4 role kustom & penegakan rute dinamis AuthenticationFilter
php spark test:workspace-security  # Verifikasi 26 temuan audit keamanan & hardening ekosistem Workspace (12/12 Pass)

# ====================================================================
# PENGUJIAN KOMUNIKASI (EVENT & BERITA)
# ====================================================================
php spark event:admin:test         # Verifikasi 13 modul admin event (CRUD, modal, lifecycle)
php spark event:attendance:test    # Verifikasi 11 alur presensi kode & batch sertifikat
php spark event:landing:test       # Verifikasi 8 fitur landing page /event dan detail
php spark news:admin:test          # Verifikasi CRUD berita, view counter, dan kurasi
php spark news:landing:test        # Verifikasi tampilan publik berita dan kategori

# ====================================================================
# PENGUJIAN ARENA BELAJAR, BUNGA RAMPAI & REGULASI
# ====================================================================
php spark arena:admin:test         # Verifikasi 11 modul admin siklus pembelajaran
php spark arena:e2e                # Verifikasi end-to-end kelulusan & asesmen peserta
php spark arena:landing:test       # Verifikasi showcase katalog pembelajaran
php spark arena:parity:test        # Verifikasi Paritas Arena Belajar Dashboard vs Workspace (6/6 Pass)
php spark knowledge:admin:test     # Verifikasi admin Bunga Rampai
php spark knowledge:test           # Verifikasi publik Bunga Rampai
php spark regulation:admin:test    # Verifikasi admin Regulasi PBJ
php spark regulation:test          # Verifikasi publik Repositori Regulasi
php spark home:landing:test        # Verifikasi beranda utama BegawiPengadaan

# ====================================================================
# PENGUJIAN HALAMAN UMUM & SERTIFIKAT DIGITAL
# ====================================================================
php spark about:contact:cert:test  # Verifikasi halaman Tentang, Kontak, dan Verifikasi Sertifikat
php spark certificate:system:test  # Verifikasi integritas sertifikat digital dan QR token
php spark certificate:smoke        # Smoke test database sertifikat

# ====================================================================
# PENGUJIAN FASE ENHANCEMENT DASHBOARD & WORKSPACE
# ====================================================================
php spark test:admin-shell         # Verifikasi Fase 1: Shell Navigasi, Spotlight Modal, Quick Action Bar
php spark test:admin-reports       # Verifikasi Fase 2: Pusat Laporan & Ekspor Universal Data Riil (6 Kategori)
php spark test:admin-approvals     # Verifikasi Fase 3: Sentralisasi Antrean Persetujuan (Central Approval Hub)
php spark test:admin-intelligence  # Verifikasi Fase 4: Government Intelligence (Radar PBJ & Gap OPD)
php spark test:admin-system        # Verifikasi Fase 5: Penguatan Akses, Audit Trail CSV & Pengaturan Sistem
php spark test:admin-learning-cert # Verifikasi Fase D: Unified Class Console & Direktori Master Sertifikat
php spark test:admin-final-fase-e-f# Verifikasi Fase E/F: Event Console, Multi-Facet Users & Kalkulator IKM
php spark test:workspace-learner   # Verifikasi Workspace Fase 1: Rapor Akademik, Kalender, Focus Mode
php spark test:trainer-gradebook   # Verifikasi Workspace Fase 2: Gradebook Matrix, CSV Export, Item Analytics
php spark test:operator-bulk       # Verifikasi Workspace Fase 3: Bulk Actions, Visual Diff, Roster Print
php spark test:workspace-executive # Verifikasi Workspace Fase 4: Command Palette, Executive Radar, Storage
```

---

## Matriks Status Implementasi Terkini

Mengacu pada klasifikasi standar:
- **Planned:** Kontrak/struktur tersedia, belum fungsional.
- **UI Implemented:** Tampilan/layout siap, backend belum lengkap.
- **Backend Ready:** Alur aksi, model, validasi, dan otorisasi selesai.
- **Production Ready:** Fitur selesai, security/kontras lulus, dan lolos test suite otomatis 100%.

| Modul / Area | Status Riil | Hasil Verifikasi Test |
|---|---|---|
| **Landing Page: Beranda, Tentang, Kontak** | `Production Ready` | `home:landing:test`, `about:contact:cert:test` (100% Pass) |
| **Landing Page: Berita & Populer** | `Production Ready` | `news:landing:test` (100% Pass) |
| **Landing Page: Event & Agenda** | `Production Ready` | `event:landing:test` (100% Pass) |
| **Landing Page: Verifikasi Sertifikat** | `Production Ready` | `about:contact:cert:test` (100% Pass) |
| **Landing Page: Arena Belajar, Bunga Rampai, Regulasi**| `Production Ready` | `arena:landing:test`, `knowledge:test`, `regulation:test` (100% Pass) |
| **Landing Page: Centralized CMS & Spotlight Hub**| `Production Ready` | `landing:full:audit` (33/33 Rute 100% Pass) |
| **Dashboard: Flexible Modular RBAC Engine** | `Production Ready` | `test:rbac-custom-roles` (14/14 Pass), `permission:sync` (90 Perms) |
| **Dashboard: Pengguna & Verifikasi Terpadu** | `Production Ready` | `test:admin-approvals` (10/10 Pass), Zero Friction Single Approval |
| **Dashboard: Detail Permission & Kontrol Deactive** | `Production Ready` | Fully Dynamic UI, Deactive, Revoke & Quick Grant Tested |
| **Dashboard: Komunikasi Berita** | `Production Ready` | `news:admin:test` (100% Pass) |
| **Dashboard: Komunikasi Event, Presensi, Batch Sertifikat**| `Production Ready` | `event:admin:test`, `event:attendance:test` (100% Pass) |
| **Dashboard: Arena Belajar Lifecycle** | `Production Ready` | `arena:admin:test`, `arena:e2e` (100% Pass) |
| **Dashboard: Bunga Rampai & Regulasi** | `Production Ready` | `knowledge:admin:test`, `regulation:admin:test` (100% Pass) |
| **Dashboard: Sistem Sertifikat Digital & Approval** | `Production Ready` | `certificate:system:test`, `certificate:smoke` (100% Pass) |
| **Dashboard: WowDash Theme & Full Navigation** | `Production Ready` | `dashboard:full:audit` (36 Sidebar Groups 100% Pass, 0 Dead Links) |
| **Workspace: Multi-Role Engine (Peserta, Trainer, Operator, Admin)** | `Production Ready` | `workspace:full:audit` (58 rute 100% Pass), `workspace:smoke` (41/41 Pass) |
| **Workspace: Full Security Hardening (26 Findings)** | `Production Ready` | `test:workspace-security` (12/12 Pass), Zero RCE, Zero IDOR & Race-Safe |
| **Dashboard: Government Intelligence PBJ** | `Production Ready` | `test:admin-intelligence` (100% Pass) |
| **Dashboard: Central Approval Hub** | `Production Ready` | `test:admin-approvals` (10/10 Pass) |
| **Dashboard: Pusat Laporan & Universal Export** | `Production Ready` | `test:admin-reports` (100% Pass, Kop Pemprov Resmi) |
| **Dashboard: Survei Kepuasan (IKM) PermenPAN-RB** | `Production Ready` | `test:admin-final-fase-e-f` (100% Pass, Bobot 9 Unsur) |
| **Dashboard: Reusable Advanced DataTables Engine** | `Production Ready` | `begawi-datatable.js` (.begawi-table, Export CSV BOM) |

---

## Struktur Direktori & Dokumentasi Teknis

Seluruh dokumen spesifikasi teknis dan berkas sinkronisasi disimpan di direktori proyek:

```text
archive/
├── controllers_legacy/                → Arsip aman controller yang dipensiunkan
└── views_legacy/                      → Arsip aman 36 file view prototipe & legacy

database/
├── permissions_catalog.json           → Katalog 90 permission sistem format JSON
├── permissions_sync.sql               → DDL & DML sinkronisasi permission & role permissions
└── reference/
    └── wilayah.sql                    → Master referensi data wilayah administratif

dokumen/
├── ../laporan_audit_landingpage_begawipengadaan.md → Laporan audit 33 rute publik & CMS (Root)
├── ../laporan_audit_dashboard_begawipengadaan.md   → Laporan audit rute sidebar & dark mode (Root)
├── ../laporan_audit_workspace_begawipengadaan.md   → Laporan audit 58 rute workspace & multi-role (Root)
├── route-contract.md                  → Kontrak rute, HTTP method, permission, dan filter
├── ui-boundary.md                     → Batasan layout, template FiStudy vs WowDash, dan asset
├── auth-and-rbac.md                   → Matriks role, permission, status akun, dan keamanan
├── workflow-contract.md               → State machine registrasi, event, sertifikat, dan approval
├── file-storage-policy.md             → Tata kelola penyimpanan file privat dan proteksi unduhan
├── database-blueprint.md              → Arsitektur tabel, foreign key, index, dan constraint
├── database-erd.md                    → Entity Relationship Diagram sistem
├── data-dictionary.md                 → Kamus data lengkap tabel dan kolom
├── ../schema-fresh-v4-3.sql           → DDL skema database produksi versi terbaru v4.3 (Root)
├── ../schema-fresh-v4-3-dummy.sql     → DDL skema dan dataset lengkap autentik PBJ (Root)
├── implementation-status.md           → Register status detail per komponen
├── registration-policy.md             → Kebijakan dan validasi registrasi pengguna
├── assessment-and-certification-rules.md → Formula penilaian dan syarat kelulusan sertifikat
├── regulation-policy.md               → Tata kelola versi dan relasi hukum regulasi
├── privacy-and-data-classification.md  → Klasifikasi data publik vs data terproteksi (NIP/NIK)
└── public-statistics-policy.md       → Ketentuan agregasi data statistik publik
```

---

## Lisensi & Hak Cipta

* **Aplikasi BegawiPengadaan:** Hak Cipta © 2026 Pemerintah Provinsi Lampung. Hak cipta dilindungi undang-undang.
* **Lisensi Pihak Ketiga:** Template FiStudy, WowDash, Font, Iconify Solar Icons, library JavaScript, dan dependensi Composer tunduk pada lisensi masing-masing penyedia sebelum didistribusikan ke lingkungan produksi publik.

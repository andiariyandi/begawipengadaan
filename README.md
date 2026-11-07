# BegawiPengadaan

<p align="center">
  <strong>Platform Transformasi Digital Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung</strong><br>
  Biro Pengadaan Barang dan Jasa — Sekretariat Daerah Provinsi Lampung
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Framework-CodeIgniter%204.7.4-EF4444?style=flat-square&logo=codeigniter&logoColor=white" alt="CodeIgniter 4">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Database-MySQL%208.4%2B%20(95%20Tables)-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL 8.4+">
  <img src="https://img.shields.io/badge/Architecture-Dual--Template%20Monolith-10B981?style=flat-square" alt="Monolith">
  <img src="https://img.shields.io/badge/RBAC-90%20Granular%20Permissions-6366F1?style=flat-square" alt="RBAC">
  <img src="https://img.shields.io/badge/Audit%20Watchdog-Zero%20Schema%20%26%20UI%20Drift-059669?style=flat-square" alt="Zero Drift">
  <img src="https://img.shields.io/badge/Status-Production%20Ready%20v4.2-blue?style=flat-square" alt="Production Ready">
</p>

---

## Daftar Isi
1. [Tentang BegawiPengadaan](#tentang-begawipengadaan)
2. [Tiga Area Fungsional Sistem](#tiga-area-fungsional-sistem)
3. [Sembilan Pilar Modul & Fitur Utama](#sembilan-pilar-modul--fitur-utama)
4. [Arsitektur, Service Layer & Batasan Template](#arsitektur-service-layer--batasan-template)
5. [Prasyarat Sistem & Dependensi](#prasyarat-sistem--dependensi)
6. [Panduan Instalasi & Konfigurasi](#panduan-instalasi--konfigurasi)
7. [Inisialisasi Database & Seeder](#inisialisasi-database--seeder)
8. [Akun Pengujian Lokal (Development)](#akun-pengujian-lokal-development)
9. [Perintah Operasional, Audit & Pengujian Spark CLI](#perintah-operasional-audit--pengujian-spark-cli)
10. [Struktur Direktori Proyek](#struktur-direktori-proyek)
11. [Keamanan, Watchdog & Integritas Sistem](#keamanan-watchdog--integritas-sistem)
12. [Indeks Dokumentasi Teknis Lengkap (18 Dokumen)](#indeks-dokumentasi-teknis-lengkap-18-dokumen)
13. [Lisensi & Hak Cipta](#lisensi--hak-cipta)

---

## Tentang BegawiPengadaan

**BegawiPengadaan** adalah ekosistem digital terpadu milik Pemerintah Provinsi Lampung yang dikelola oleh Biro Pengadaan Barang dan Jasa (PBJ) Sekretariat Daerah Provinsi Lampung. Platform ini mentransformasikan proses pembelajaran, transfer pengetahuan, tata kelola regulasi, pemetaan kompetensi sumber daya manusia PBJ, hingga audit otomatis perencanaan pengadaan (RUP) ke dalam satu sistem terintegrasi, transparan, dan akuntabel.

Spesifikasi Skala Arsitektur Saat Ini:
- **Basis Data:** **95 Tabel Aktif**, **1.127 Kolom**, **22 CHECK Constraints Otoritatif (58 Checks Aktif)**, dan **17 Berkas Migrasi Resmi**.
- **Rute & Endpoint:** **388 Rute Web & API Aktif** terpetakan secara utuh tanpa *orphan route*.
- **Otorisasi RBAC:** **90 Permission Granular** lintas **12 Modul Domain** dengan *Zero Stale Session* dan *Dynamic Mutation Guard*.
- **Model & Service Layer:** **78 Class Model** tervalidasi skema dan **49 Domain Services** terstruktur.

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
         │ • Statistik Publik  │     │        │ • RUP Audit Engine  │
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
                          │ • RUP Audit & Analis│
                          │ • Konsultasi & Tiket│
                          └──────────┬──────────┘
                                     │
                               Template: FiStudy
                               Asset: assets/main/
```

1. **Landing Page & Portal Publik (FiStudy):** Area terbuka tanpa login untuk masyarakat umum, ASN, pelaku usaha/penyedia, dan akademisi guna mengakses publikasi berita, regulasi, silabus kelas, agenda kegiatan, hasil survei publik, serta layanan mandiri verifikasi keaslian e-sertifikat yang dilindungi oleh `PublicVisibilityGuard`.
2. **Workspace Pengguna (FiStudy):** Portal kolaboratif pengguna terautentikasi dengan navigasi spesifik peran (*Peserta*, *Trainer*, *Operator LMS*, *Administrator Eksekutif*) untuk mengikuti pembelajaran (termasuk *Zen/Focus Mode* & transkrip akademik), penilaian tugas (*Gradebook Matrix*), absensi event, konsultasi PBJ, hingga *Executive Health Radar*.
3. **Dashboard Back-Office & Auth Terpadu (WowDash):** Area administratif terpusat untuk verifikasi akun/profil 9 jalur registrasi, matriks hak akses 90 permission, tata kelola kurikulum & sertifikat, *Spotlight Hub* 5 modul, *Government Intelligence*, *Automated RUP Audit*, serta jejak audit keamanan (*audit trail*).

---

## Sembilan Pilar Modul & Fitur Utama

### 1. Arena Belajar (Learning Management System)
- Silabus modul bertingkat: Dokumen panduan PDF, video instruksional, dan tautan referensi.
- Evaluasi komprehensif: Pre-test, kuis berbatas waktu dengan *auto-submit* saat kedaluwarsa (`$force = true`), *review* pembahasan, *re-attempt* kuota, dan retensi skor tertinggi.
- State engine kelulusan (`CertificateEligibilityService` & `GradingService`): Syarat presensi, batas nilai minimum (*passing grade*), dan evaluasi kelas sebelum penerbitan sertifikat.
- *Trainer Gradebook & Wizard Duplikasi Kelas:* Matriks nilai peserta, analisis tingkat kesulitan butir soal, *canned feedback*, ekspor CSV UTF-8 BOM, dan salin kelas lengkap bersama bank soal (`quiz_questions` & `quiz_options`) via `duplicateBatch()`.

### 2. Bunga Rampai (Knowledge Management System)
- Repositori pengetahuan PBJ: Artikel praktis (`article`), studi kasus pengadaan (`case_study`), tanya-jawab umum (`faq`), dan dokumen template standar (`template`).
- Dokumen template standar: Kerangka Acuan Kerja (KAK), dokumen pemilihan, HPS, dan draf kontrak pengadaan yang siap diunduh secara aman.
- Interaksi pengguna: Penanda bacaan (*bookmark*) lintas modul dan pencarian cerdas terpadu (`SearchService`).

### 3. Bank Naskah Hukum & Regulasi PBJ
- Klasifikasi regulasi berjenjang: Undang-Undang, Peraturan Pemerintah, Perpres, Permen, Peraturan LKPP, Perda, dan Pergub Lampung.
- Riwayat status hukum (`legal_status` & `workflow_status`): Pelacakan status naskah (`active`, `amended`, `revoked`, `expired`) dengan manajemen versi (`regulation_versions`) dan visualisasi relasi regulasi (`regulation_relations`).
- Pratinjau dokumen: *Inline PDF reader* terproteksi dan unduhan lampiran aman dengan sanitasi header Content-Disposition.

### 4. Event, Agenda & Konsultasi PBJ
- Siklus hidup kegiatan (`events.status`): Dari `draft`, `coming_soon`, `open_registration`, `full`, hingga `finished` dan `cancelled`.
- State Machine Kuota & *Waitlist* Atomik (`CommunicationService::reconcileEventCapacityAndWaitlist`): Promosi otomatis peserta antrean (`waitlisted` -> `registered`) secara FIFO saat terjadi pembatalan/penolakan registrasi, disertai transisi otomatis status kapasitas (`full` <-> `open_registration`).
- Presensi real-time berbasis QR/kode unik: Validasi ketat hanya untuk pendaftar berstatus sah (`registered`) dalam transaksi atomik.

### 5. E-Sertifikat Terverifikasi & Tanda Tangan Digital
- Mesin pembuat PDF sertifikat (`CertificateGenerator`, `CertificatePdfGenerator`, `CertificateTemplateRenderer`, `CleanTemplateFpdi`, `DigitalSignatureService`) dengan penomoran seri otomatis berurutan (`certificate_sequences`).
- QR Code dinamis (`CertificateQrGenerator`) untuk verifikasi instan integritas dokumen pada `/verifikasi-sertifikat` dengan prinsip *minimum disclosure* (tanpa mengekspos NIK/NIP) dan anti-spam captcha.

### 6. Automated RUP Audit Engine (Audit Perencanaan Pengadaan)
- Mesin audit cerdas terintegrasi pada namespace `App\Services\RupAudit\*` untuk memeriksa kepatuhan Rencana Umum Pengadaan (RUP) terhadap regulasi pengadaan nasional (Perpres PBJ & Peraturan LKPP).
- 10 Komponen Layanan Utama:
  1. `RupAuditService`: Koordinator utama orkestrasi batch audit dan agregasi temuan.
  2. `RupRuleEngine`: Mesin pendeteksi pelanggaran aturan (pemecahan paket, batas pagu pengadaan langsung, afirmasi produk dalam negeri TKDN & UMK-Koperasi).
  3. `RupRciCalculator`: Penghitung indeks kepatuhan risiko (*Risk & Compliance Index* / RCI) per paket dan per OPD.
  4. `RupGraphClusterer`: Pendeteksi anomali pengelompokan paket pengadaan berdasarkan kesamaan lokasi, waktu, dan komoditas.
  5. `RupNlpExtractor`: Ekstraktor teks pemrosesan bahasa alami untuk membedah nama paket dan spesifikasi teknis.
  6. `RupDiffTracker`: Pelacak perbedaan riwayat revisi dan mutasi paket RUP antar-periode penarikan SiRUP.
  7. `RupCsvParser`: Parser berkas CSV SiRUP tahan-lonjakan memori dengan validasi header ketat.
  8. `RupExportService`: Generator laporan ekspor hasil audit format CSV/Excel UTF-8 BOM dan PDF eksekutif.
  9. `RupTaxonomy`: Standarisasi taksonomi komoditas KBKI/KBLI dan kategori belanja PBJ.
  10. `RupAccessHelper`: Pengendali otorisasi akses data audit berbasis unit kerja dan peran pemeriksa.

### 7. Government Intelligence & Laporan Eksekutif
- Radar Kematangan PBJ Daerah (`IntelligenceAnalyticsService`): Analisis indikator kelembagaan, proses pengadaan, dan SDM Fungsional Pengelola PBJ (`jf_ppbj_records`).
- Analisis kesenjangan (*competency gap*) formasi PBJ per Organisasi Perangkat Daerah (`OrganizationService` & `ProcurementRoleService`).
- Pusat laporan universal (`ReportAnalyticsService`) ber-kop resmi Pemprov Lampung dengan ekspor CSV/PDF dan statistik pengunjung real-time (`VisitorStatisticService` via `visitor_ip_logs`).

### 8. Flexible Modular RBAC Hub & 9 Jalur Registrasi Identitas
- 90 permission granular terdistribusi di 12 modul domain (`dashboard`, `landing`, `access`, `learning`, `bunga`, `regulation`, `communication`, `certificate`, `intelligence`, `reports`, `survey`, `system`).
- Mendukung multi-peran (*double/custom roles*) dengan *Zero Stale Session* (sinkronisasi izin real-time dari basis data) dan *Dynamic Mutation Guard*.
- Pendaftaran terverifikasi (`RegistrationService` & `ProfileVerificationService`) untuk 9 jalur profil: ASN Pemprov Lampung, ASN Pemda Lain, ASN K/L, Aparatur Desa, BUMN/BUMD, Pelaku Usaha/Penyedia, Akademisi, dan Masyarakat Umum.

### 9. Sentralisasi CMS, Spotlight Hub & File Asset Management
- Kontrol penuh elemen beranda dinamis dan *Spotlight Hub* (`/dashboard/landing/sorotan-carousel`) lintas 5 modul publik.
- Manajemen aset berkas terpusat (`FileStorageService::registerFileAsset`) dengan 13 kolom metadata kanonikal, validasi SHA-256 checksum, dan *Atomic File Rollback* (penghapusan otomatis berkas fisik jika transaksi basis data gagal).
- Proteksi akses materi pembelajaran dan sertifikat melalui `resolveSafePath` containment dan `.htaccess` hardening.

---

## Arsitektur, Service Layer & Batasan Template

Aplikasi menerapkan pemisahan dependensi antarmuka (*UI boundary*) secara ketat yang diawasi oleh *watchdog* otomatis `php spark audit:ui-boundary`:

| Area Sistem | Template Basis | Controller Namespace | View Directory | Asset Directory |
|---|---|---|---|---|
| **Landing Page** | FiStudy | `App\Controllers\Landing\` | `app/Views/landing/` | `public/assets/main/` |
| **Workspace** | FiStudy | `App\Controllers\Workspace\` | `app/Views/main/workspace/` | `public/assets/main/` |
| **Auth Terpadu** | WowDash | `App\Controllers\Dashboard\Auth\` | `app/Views/dashboard/auth/` | `public/assets/dashboard/` |
| **Dashboard Back-Office** | WowDash | `App\Controllers\Admin\`, `Learning\`, `Communication\`, `Certificate\` | `app/Views/dashboard/` | `public/assets/dashboard/` |

> [!IMPORTANT]
> Aset `public/assets/main/` dan `public/assets/dashboard/` diisolasi 100%. Referensi silang antar-template akan menggagalkan gate `php spark audit:ui-boundary`.

---

## Prasyarat Sistem & Dependensi

- **PHP:** Versi **8.3** atau lebih baru (kompatibel penuh dengan strict typing PHP 8.3.33+).
- **Ekstensi PHP Wajib:** `intl`, `mbstring`, `mysqli`, `curl`, `fileinfo`, `gd`, `openssl`, `json`, `pdo_mysql`.
- **Database Engine:** **MySQL 8.4+** atau MariaDB 10.11+ (mendukung evaluasi `CHECK` constraint aktif).
- **Package Manager:** **Composer 2.x**.
- **Web Server:** Apache, Nginx, atau Laragon dengan *Document Root* diarahkan ke folder `public/`.

---

## Panduan Instalasi & Konfigurasi

### 1. Clone Repository & Install Dependensi
```bash
git clone https://github.com/begawipbj/begawipengadaan.git
cd begawipengadaan
composer install
```

### 2. Konfigurasi Environment (`.env`)
Salin berkas `.env.example` menjadi `.env` dan sesuaikan parameter basis data:
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

### 3. Generate Kunci Enkripsi
```bash
php spark key:generate
```

---

## Inisialisasi Database & Seeder

### 1. Import Skema Dasar & Master Wilayah
```bash
mysql -u root -p db_begawipbj < schema-fresh-v4-3.sql
mysql -u root -p db_begawipbj < database/reference/wilayah.sql
```

### 2. Jalankan 17 Migrasi Database Resmi
```bash
php spark migrate
```

### 3. Sinkronisasi Katalog 90 Hak Akses (RBAC)
```bash
php spark permission:sync
```

### 4. Seed Data Master & Konten Autentik PBJ
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

---

## Akun Pengujian Lokal (Development)

Kata sandi standar untuk seluruh akun demo di bawah ini adalah `Password123!`:

| Role / Peran | Username | Email | Lingkup Akses Utama |
|---|---|---|---|
| **Super Administrator** | `super.admin` | `superadmin@lampungprov.go.id` | Akses 100% penuh seluruh modul, RBAC, dan konfigurasi sistem |
| **Administrator PBJ** | `admin.pbj` | `admin.pbj@lampungprov.go.id` | Manajemen operasional PBJ, konten, verifikasi, dan persetujuan |
| **Operator LMS** | `operator.lms` | `operator.lms@lampungprov.go.id` | Pengelolaan kurikulum, peserta, antrean event, dan sertifikat massal |
| **Trainer / Instruktur** | `trainer.pbj` | `trainer.pbj@lampungprov.go.id` | Ruang kelas pengajar, penilaian tugas, *gradebook*, dan bank soal |
| **Peserta Pelatihan** | `ani.peserta` | `ani.peserta@lampungprov.go.id` | Mengikuti kelas, kuis, tugas, event, dan portofolio e-sertifikat |

---

## Perintah Operasional, Audit & Pengujian Spark CLI

BegawiPengadaan dilengkapi dengan **20 perintah audit & automated testing suite** untuk menjamin kualitas sistem (*Zero Defect & Zero Drift*):

```bash
# ----------------------------------------------------------------------
# 1. WATCHDOG ARSITEKTUR & SKEMA (CI/CD GATES)
# ----------------------------------------------------------------------
php spark audit:schema-drift         # Validasi 95 tabel, 1127 kolom, 78 Model $allowedFields, 0 Runtime DDL, & PublicVisibilityGuard
php spark audit:ui-boundary          # Validasi isolasi aset FiStudy (assets/main) vs WowDash (assets/dashboard)

# ----------------------------------------------------------------------
# 2. AUDIT OTOMATIS LINTAS AREA (END-TO-END)
# ----------------------------------------------------------------------
php spark landing:full:audit         # Audit 44 rute publik, CMS bindings, dan token CSRF
php spark dashboard:full:audit       # Audit 34 navigasi sidebar, route Closure, layout WowDash, dan RBAC gate
php spark workspace:full:audit       # Audit 58 rute lintas 4 peran workspace dan integritas komponen
php spark workspace:smoke            # Smoke test 41 assertions interaksi layout dan responsivitas workspace
php spark certificate:smoke          # Smoke test integritas sertifikat, sequence, enrollment, dan template
php spark audit:arena:home           # Audit komprehensif beranda Arena Belajar (/arena-belajar)

# ----------------------------------------------------------------------
# 3. SUITE PENGUJIAN FUNGSIONAL, RBAC & KEAMANAN
# ----------------------------------------------------------------------
php spark test:registration          # Verifikasi 13 skenario pada 9 jalur pendaftaran profil pengguna
php spark test:portal-security       # Audit 45 titik keamanan portal (XSS, Path Traversal, Rate Limit, Transaksi Atomik)
php spark test:workspace-security    # Audit 17 titik keamanan workspace (.htaccess upload, anti-IDOR, quiz timeout, rate limit)
php spark test:rbac-custom-roles     # Pengujian 14 assertions isolasi hak akses role kustom modular
php spark test:rbac-double-role      # Pengujian 41 assertions multi-role, Zero Stale Session, dan kepatuhan 17 migrasi
php spark test:hero-spotlight        # Verifikasi 12 assertions Hero Slider & Badge Unggulan pada 5 modul publik
php spark test:landing-cms           # Pengujian 97 assertions CRUD konten beranda CMS & pipeline upload
php spark test:admin-learning-cert   # Verifikasi 10 assertions duplikasi kelas, bank soal, dan ekspor CSV BOM
php spark test:trainer-gradebook     # Verifikasi 13 assertions Gradebook Matrix, analisis butir soal, dan feedback
php spark test:quiz-review-reattempt # Verifikasi 5 assertions kuis, review kunci, re-attempt, dan skor tertinggi
php spark test:operator-bulk         # Verifikasi 12 assertions bulk approval, diff viewer, dan live attendance gauge
php spark test:workspace-learner     # Verifikasi 13 assertions transkrip akademik PBJ, kalender, dan Zen Mode
php spark test:workspace-executive   # Verifikasi 11 assertions Command Palette (Ctrl+K) & Executive Health Radar

# ----------------------------------------------------------------------
# 4. PEMELIHARAAN & SINKRONISASI
# ----------------------------------------------------------------------
php spark permission:sync            # Sinkronisasi idempotent 90 katalog permission sistem ke basis data
php spark cache:clear                # Membersihkan seluruh cache sistem dan view runtime
```

---

## Struktur Direktori Proyek

```text
begawipengadaan/
├── app/
│   ├── Commands/                  # Perintah kustom Spark CLI (Watchdog Audit, Seeder, 20+ Test Suites)
│   ├── Config/                    # Konfigurasi aplikasi (Database, 388 Routes, Filters, Security)
│   ├── Controllers/
│   │   ├── Admin/                 # Controller tata kelola dashboard back-office & CMS Spotlight
│   │   ├── Certificate/           # Controller unduhan, verifikasi publik, dan batch sertifikat
│   │   ├── Communication/         # Controller pengelolaan berita, event, dan pengumuman
│   │   ├── Dashboard/Auth/        # Controller autentikasi tunggal (Login, Register 9 Jalur, Reset)
│   │   ├── Landing/               # Controller portal publik FiStudy (Home, Arena, Bunga Rampai, Event, Regulasi)
│   │   ├── Learning/              # Controller manajemen LMS (Kelas, Kurikulum, Assessment, Monitoring, Kuis)
│   │   └── Workspace/             # Controller ruang kerja 4 peran (Participant, Trainer, Operator, Executive, Consultation)
│   ├── Database/
│   │   ├── Migrations/            # 17 berkas migrasi database resmi (Zero Migration Drift Policy)
│   │   └── Seeds/                 # Seeder data master, demo PBJ, dan remediasi audit
│   ├── Filters/                   # Filter autentikasi, RBAC zoneMap, WorkspaceAuthFilter, dan throttle
│   ├── Models/                    # 78 Model data CodeIgniter 4 terorganisasi per domain
│   ├── Services/                  # 49 Domain Services:
│   │   ├── Admin/                 # Intelligence, Organization, ProcurementRole, Profile, Report, Role, User
│   │   ├── Communication/         # CommunicationService (State Machine Waitlist) & EventRegistrationStatus
│   │   ├── Email/                 # EmailService, EmailQueueService, EmailTemplateService
│   │   ├── Identity/              # AuthenticationService, AuthorizationService, RegistrationService, PasswordReset
│   │   ├── Learning/              # QuizQuestionImportService
│   │   ├── Regulation/            # RegulationService & RegulationPublicService
│   │   ├── RupAudit/              # 10 Service Mesin Audit RUP (RuleEngine, RCI, GraphClusterer, NLP, Diff, CSV, dll.)
│   │   ├── Workflow/              # ApprovalService
│   │   └── *.php                  # FileStorageService, PublicVisibilityGuard, LearningService, GradingService, Certificate*
│   └── Views/
│       ├── dashboard/             # Antarmuka back-office & form auth (Template WowDash)
│       ├── landing/               # Antarmuka portal publik (Template FiStudy)
│       └── main/workspace/        # Antarmuka workspace terautentikasi (Template FiStudy)
├── database/
│   ├── reference/                 # Data referensi master (wilayah administratif Indonesia)
│   ├── permissions_catalog.json   # Katalog definisi 90 permission sistem format JSON
│   └── permissions_sync.sql       # Naskah SQL sinkronisasi hak akses
├── dokumen/                       # 18 Dokumentasi teknis & spesifikasi arsitektur resmi
├── public/
│   ├── assets/
│   │   ├── dashboard/             # Aset terisolasi khusus template WowDash
│   │   └── main/                  # Aset terisolasi khusus template FiStudy
│   ├── uploads/                   # Direktori lampiran dengan proteksi .htaccess (php_flag engine off)
│   └── index.php                  # Entry point aplikasi web
├── writable/                      # Direktori cache, sesi, file privat, dan log aplikasi
├── schema-fresh-v4-3.sql          # DDL skema basis data produksi (95 tabel)
├── composer.json                  # Dependensi pustaka PHP
└── spark                          # Console runner CodeIgniter 4
```

---

## Keamanan, Watchdog & Integritas Sistem

1. **Zero Schema & Migration Drift Policy:** Seluruh 95 tabel dan 1.127 kolom dikawal oleh `php spark audit:schema-drift` yang memvalidasi kecocokan `$allowedFields` pada 78 Model serta melarang eksekusi DDL runtime di luar 17 berkas migrasi resmi.
2. **Public Visibility Guard (`PublicVisibilityGuard`):** Menjamin isolasi ketat agar entitas berstatus `draft`, `cancelled`, atau `internal` tidak pernah bocor ke halaman katalog publik maupun *Hero Spotlight Slider*.
3. **Atomic File Storage & Path Containment:** Seluruh penyimpanan berkas menggunakan `FileStorageService::registerFileAsset()` dengan *atomic file rollback*, pencegahan *path traversal* (`realpath` prefix check), serta pemblokiran eksekusi skrip di direktori unggahan via `.htaccess`.
4. **Stored XSS & Atomisitas Transaksi:** Sanitasi otomatis konten HTML (`CommunicationService::sanitizeHtmlContent()`) dan pembungkusan transaksi basis data (`transBegin`/`transComplete`) bersama pencatatan `AuditService::log()`.
5. **Privacy by Design & Rate Limiting:** Perlindungan penuh terhadap NIK/NIP pada respon publik dan pembatasan laju permintaan (*throttler*) pada endpoint autentikasi, kuis, survei, dan formulir publik.

---

## Indeks Dokumentasi Teknis Lengkap (18 Dokumen)

Seluruh dokumentasi spesifikasi teknis tersimpan pada direktori [`dokumen/`](dokumen/):

### A. Arsitektur Data & Basis Data
1. [Arsitektur Skema Database (`database-blueprint.md`)](dokumen/database-blueprint.md): Spesifikasi DDL 95 tabel, indeks performa, dan 22 CHECK constraint.
2. [Kamus Data Database (`data-dictionary.md`)](dokumen/data-dictionary.md): Definisi lengkap 1.127 kolom, tipe data, dan aturan *nullability* per tabel.
3. [Diagram Relasi Entitas (`database-erd.md`)](dokumen/database-erd.md): Peta relasi *foreign key* lintas domain (Identitas, LMS, KMS, Regulasi, Event, Sertifikat, dan RUP Audit).
4. [Register Berkas Dokumentasi & Database (`documentation-and-database-file-register.md`)](dokumen/documentation-and-database-file-register.md): Inventarisasi berkas SQL, seeder, dan katalog 17 migrasi resmi.

### B. Otorisasi, Keamanan & Kebijakan Privasi
5. [Kontrak Autentikasi & RBAC (`auth-and-rbac.md`)](dokumen/auth-and-rbac.md): Matriks 90 permission, `AuthenticationFilter::zoneMap`, *Zero Stale Session*, dan *Dynamic Mutation Guard*.
6. [Rencana & Arsitektur Transisi Auth (`auth-migration-plan.md`)](dokumen/auth-migration-plan.md): Spesifikasi penyatuan pintu autentikasi dan manajemen sesi multi-peran.
7. [Kebijakan Pendaftaran Akun (`registration-policy.md`)](dokumen/registration-policy.md): Aturan validasi dan verifikasi untuk 9 jalur profil pendaftar.
8. [Privasi & Klasifikasi Data (`privacy-and-data-classification.md`)](dokumen/privacy-and-data-classification.md): Kebijakan perlindungan PII (NIK/NIP) dan *minimum disclosure*.
9. [Kebijakan Penyimpanan Berkas (`file-storage-policy.md`)](dokumen/file-storage-policy.md): Standar 13 kolom kanonikal `file_assets`, `FileStorageService`, dan pengamanan unduhan.

### C. Kontrak Rute, Alur Kerja & Aturan Bisnis Domain
10. [Kontrak Rute & Filter (`route-contract.md`)](dokumen/route-contract.md): Pemetaan 388 rute aktif, metode HTTP, filter pelindung, dan *named routes*.
11. [Kontrak Alur Kerja & State Machine (`workflow-contract.md`)](dokumen/workflow-contract.md): Diagram transisi status untuk kursus, kelas, kuota/*waitlist* event, dan persetujuan konten.
12. [Aturan Penilaian & Sertifikasi (`assessment-and-certification-rules.md`)](dokumen/assessment-and-certification-rules.md): Rumus pembobotan nilai LMS, syarat kelulusan, dan penerbitan e-sertifikat.
13. [Kebijakan Tata Kelola Regulasi (`regulation-policy.md`)](dokumen/regulation-policy.md): Hierarki produk hukum PBJ, relasi status hukum, dan manajemen versi dokumen.
14. [Kebijakan Statistik Publik (`public-statistics-policy.md`)](dokumen/public-statistics-policy.md): Metodologi agregasi KPI beranda, analitik pengunjung (`visitor_ip_logs`), dan hasil survei publik.

### D. Batasan Antarmuka, Struktur Pohon & Status Implementasi
15. [Batasan Antarmuka (`ui-boundary.md`)](dokumen/ui-boundary.md): Aturan isolasi template FiStudy vs WowDash dan penegakan `audit:ui-boundary`.
16. [Struktur Pohon Direktori (`directory-tree.md`)](dokumen/directory-tree.md): Pemetaan anatomi folder dan berkas aplikasi produksi.
17. [Spesifikasi Pembentukan Direktori (`directory-tree-create.md`)](dokumen/directory-tree-create.md): Standar penempatan modul baru (Controller, Service, Model, View, Command).
18. [Status Implementasi Sistem (`implementation-status.md`)](dokumen/implementation-status.md): Matriks kesiapan produksi seluruh modul dan rekapitulasi kelulusan 20 suite audit CLI.

---

## Lisensi & Hak Cipta

- **Aplikasi BegawiPengadaan:** Hak Cipta © 2026 Pemerintah Provinsi Lampung. Hak cipta dilindungi undang-undang.
- **Aset & Pustaka Pihak Ketiga:** Template FiStudy, WowDash, Iconify Solar Icons, library JavaScript, serta paket Composer tunduk pada lisensi masing-masing penyedia.

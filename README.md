# BegawiPengadaan

## Platform Transformasi Digital Pengadaan Barang/Jasa Pemerintah Provinsi Lampung

BegawiPengadaan adalah platform digital terpadu untuk mendukung ekosistem Pengadaan Barang/Jasa Pemerintah (PBJ) di lingkungan Pemerintah Provinsi Lampung.

Platform ini menyatukan pembelajaran digital (*Arena Belajar*), pusat pengetahuan (*Bunga Rampai*), repositori regulasi PBJ, publikasi dan agenda (*Berita & Event*), sertifikat digital terverifikasi, data kompetensi SDM PBJ (*Government Intelligence*), statistik publik, serta workflow persetujuan berbasis peran (*Role-Based Access Control*) dalam satu aplikasi monolitik yang aman, terstruktur, dan dapat diaudit secara penuh.

BegawiPengadaan dibangun menggunakan **CodeIgniter 4 (AppStarter)** dengan adaptasi antarmuka produksi dari dua source template HTML:
- **FiStudy:** Didedikasikan untuk **Landing Page (Public Area)** dan **Workspace** pengguna.
- **WowDash:** Didedikasikan untuk **Auth Terpadu** dan **Dashboard Back-office**.

---

## Tiga Pilar Fungsional Utama Sistem

Sistem BegawiPengadaan terbagi secara tegas ke dalam 3 area fungsional utama berdasarkan target audiens, hak akses, layout, dan interaksinya:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                               BegawiPengadaan                                │
└───────────────────┬────────────────┬───────────────────┬───────────────┘
                    │                │                   │
         ┌──────────▼──────────┐     │        ┌──────────▼──────────┐
         │ 1. LANDING PAGE     │     │        │ 3. DASHBOARD        │
         │    & PUBLIC AREA    │     │        │    BACK-OFFICE      │
         ├─────────────────────┤     │        ├─────────────────────┤
         │ • Beranda Utama     │     │        │ • Auth Terpadu      │
         │ • Berita & Populer  │     │        │ • KPI Internal      │
         │ • Event & Agenda    │     │        │ • Manajemen Akses   │
         │ • Arena Belajar     │     │        │ • Komunikasi (Berita│
         │ • Bunga Rampai      │     │        │   & Event-Agenda)   │
         │ • Repositori Hukum  │     │        │ • E-Sertifikat      │
         │ • Statistik Publik  │     │        │ • Kelola Pembelajaran│
         │ • Verifikasi Sertif │     │        │ • Gov Intelligence  │
         │ • Tentang & Kontak  │     │        │ • Laporan & Audit   │
         └──────────┬──────────┘     │        └──────────┬──────────┘
                    │                │                   │
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
                          │ • Evaluator Grading │
                          │ • Operator Approval │
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
  * Akses informasi publik pengadaan barang/jasa Provinsi Lampung.
  * Pencarian dan pembacaan berita PBJ dengan counter baca otomatis, berita populer, dan kurasi 5 berita utama.
  * Penelusuran agenda, webinar, workshop, dan event PBJ (tampilan Grid, List, dan Kalender).
  * Eksplorasi katalog pembelajaran (*Arena Belajar*), artikel pengetahuan (*Bunga Rampai*), dan peraturan (*Regulasi PBJ*).
  * Layanan mandiri verifikasi keaslian e-sertifikat melalui nomor seri atau pemindaian QR code (`/verifikasi-sertifikat`).
  * Informasi profil organisasi dan formulir permohonan informasi/aspirasi publik (`/tentang` dan `/kontak`).

### 2. Workspace Pengguna Terautentikasi (FiStudy)
* **Pengguna:** Pengguna terdaftar dengan role operasional aktif (*Participant, Trainer, Evaluator, Operator*).
* **Fungsi Utama:**
  * **Participant:** Mengikuti kelas pembelajaran, mengerjakan materi, pre-test, kuis, penugasan, post-test, presensi event, riwayat sertifikat, serta evaluasi kelas.
  * **Trainer:** Mengelola kelas yang ditugaskan, memantau kemajuan peserta, mengunggah materi, dan berinteraksi.
  * **Evaluator:** Menilai penugasan peserta secara objektif dengan rubrik penilaian, memberikan catatan revisi, dan final grading.
  * **Operator:** Melakukan verifikasi administratif data pendaftar, monitoring presensi event, dan verifikasi sertifikat terdelegasi.
  * **Shared Fitur:** Manajemen profil terverifikasi, personalisasi bookmark lintas konten, dan pusat notifikasi.

### 3. Dashboard Back-Office & Auth Terpadu (WowDash)
* **Pengguna:** Administrator, Super Admin, dan verifikator dengan hak akses (*permission*) back-office.
* **Fungsi Utama:**
  * **Auth Terpadu:** Pintu gerbang login, registrasi akun multi-step, verifikasi identitas, dan pemulihan kata sandi bagi seluruh pengguna.
  * **Manajemen Akses & RBAC:** Pengelolaan pengguna, persetujuan verifikasi profil ASN/Penyedia/Akademisi, pengaturan role dan permission matrix.
  * **Pusat Komunikasi (Berita & Event-Agenda):**
    * Kontrol penuh artikel berita, upload media, kurasi berita utama (hingga 5 featured), status publikasi, dan monitoring pembaca.
    * Manajemen siklus hidup event (Draft s.d. Finished), generate kode presensi unik, pembukaan/penutupan presensi instan, batch generate sertifikat otomatis untuk peserta hadir, dan automasi waitlist kuota.
  * **Tata Kelola Konten & Data:** Manajemen Arena Belajar, Bunga Rampai, Repositori Regulasi PBJ, dan Template Sertifikat Digital.
  * **Government Intelligence & Laporan:** Analisis pemetaan gap kompetensi SDM PBJ, distribusi JF PPBJ, audit trail lengkap, dan statistik internal.

---

## Arsitektur Antarmuka & Kontrak Template

Sistem BegawiPengadaan memisahkan dependensi antarmuka secara mutlak untuk mencegah konflik CSS/JS antar-template:

| Area | Template | Controller Root | View Root | Asset Root |
|---|---|---|---|---|
| **Landing Page** | FiStudy | `app/Controllers/Landing/` | `app/Views/landing/` | `public/assets/main/` |
| **Workspace** | FiStudy | `app/Controllers/Workspace/` | `app/Views/main/workspace/` | `public/assets/main/` |
| **Auth Terpadu** | WowDash | `app/Controllers/Dashboard/Auth/` | `app/Views/dashboard/auth/` | `public/assets/dashboard/` |
| **Dashboard Admin**| WowDash | `app/Controllers/Dashboard/` & `App\Controllers\Communication\` | `app/Views/dashboard/` | `public/assets/dashboard/` |
| **Source Reference**| Reference | *Non-production (dilarang dirender)* | `app/Views/reference/` | *Source template original* |

> [!IMPORTANT]
> - Asset `public/assets/main/` hanya digunakan untuk Landing Page dan Workspace.
> - Asset `public/assets/dashboard/` hanya digunakan untuk Auth dan Dashboard Back-office.
> - Tidak diperbolehkan menyilangkan asset antar-template tanpa dokumentasi arsitektur eksplisit.

---

## Panduan Instalasi & Pengembangan (*Quick Start*)

### Prasyarat Sistem
- **PHP:** Versi 8.3 atau lebih baru.
- **Ekstensi PHP Wajib:** `intl`, `mbstring`, `mysqli`, `openssl`, `curl`, `fileinfo`, `gd`, `json`.
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
   mysql -u root -p db_begawipbj < dokumen/schema-fresh-v4-1.sql
   mysql -u root -p db_begawipbj < database/reference/wilayah.sql
   ```

6. **Seed Data Awal & Dummy Autentik PBJ:**
   Jalankan seed command yang telah disiapkan:
   ```bash
   php spark event:seed        # Seed agenda, webinar, dan workshop PBJ
   php spark news:seed         # Seed berita pengadaan dan kategori
   php spark knowledge:seed    # Seed artikel Bunga Rampai
   php spark regulation:seed   # Seed regulasi dan peraturan PBJ
   ```

7. **Jalankan Development Server:**
   ```bash
   php spark serve
   ```
   Akses aplikasi di browser melalui `http://localhost:8080`.

---

## Kontrak Rute & Endpoint

### 1. Rute Publik (Landing Page)
```text
/                              → Beranda Utama
/tentang                       → Profil Instansi & Visi Misi
/kontak                        → Formulir Aspirasi & Kontak
/arena-belajar                 → Katalog Pembelajaran Publik
/bunga-rampai                  → Pusat Pengetahuan & Artikel PBJ
/regulasi                      → Repositori Regulasi PBJ (Detail, Preview, Unduh)
/berita                        → Publikasi Berita PBJ (Kategori & Detail)
/event                         → Agenda, Webinar & Workshop (List, Grid, Kalender)
/statistik                     → Agregat Statistik Publik PBJ
/verifikasi-sertifikat         → Layanan Verifikasi Mandiri E-Sertifikat
/verify/certificate/(:segment) → Endpoint Pemindaian QR Code Sertifikat
```

### 2. Rute Workspace (FiStudy)
```text
/workspace                     → Dashboard Ringkasan Pengguna
/workspace/profil              → Pengaturan Profil (Akun, ASN, Penyedia, Akademik)
/workspace/aktivitas           → Log Aktivitas Pengguna
/workspace/notifikasi          → Pusat Notifikasi Masuk
/workspace/bookmark            → Koleksi Konten Tersimpan
/workspace/partisipan/*        → Pembelajaran Aktif, Tugas, Ujian & Sertifikat Saya
/workspace/trainer/*           → Manajemen Kelas Penugasan Pengajar
/workspace/evaluator/*         → Antrean Penilaian Penugasan & Rubrik
/workspace/operator/*          → Verifikasi Profil & Kehadiran Operasional
```

### 3. Rute Dashboard Back-Office & Auth (WowDash)
```text
/dashboard/masuk               → Autentikasi Pengguna Terpadu
/dashboard/daftar              → Registrasi Multi-step Terpadu
/dashboard/lupa-kata-sandi     → Pemulihan Akun
/dashboard/status-akun         → Informasi Status Akun Pengguna

/dashboard                     → Ringkasan Eksekutif & KPI Internal
/dashboard/akses/*             → Manajemen Pengguna, Role, Unit Organisasi, Verifikasi
/dashboard/komunikasi/berita   → CRUD Berita, Kategori, Kurasi Berita Utama, Hit Counter
/dashboard/komunikasi/event-agenda → Manajemen Event, Kode Presensi, Batch Sertifikat
/dashboard/komunikasi/registrasi-event → Detail Peserta & Status Pendaftaran
/dashboard/komunikasi/kehadiran → Monitoring Presensi Peserta
/dashboard/komunikasi/waitlist-pembatalan → Antrean Waitlist & Promosi Kuota
/dashboard/sertifikat/*        → Overview, Template, Riwayat, Verifikasi Internal
/dashboard/arena-belajar/*     → Manajemen Program, Kursus, Kelas, Modul & Ujian
/dashboard/bunga-rampai/*      → Manajemen Artikel, FAQ, Studi Kasus, Media
/dashboard/regulasi/*          → Manajemen Regulasi, Versi & Relasi Hukum
/dashboard/intelijen/*         → Pemetaan JF PPBJ & Analisis Gap Kompetensi
```

---

## Kapabilitas Riil & Penyempurnaan Terkini

### A. Fitur Berita & Kategori Berita
1. **Dynamic View Counter (+1 Hit Count):**
   * Setiap pembacaan berita menambah hit counter +1 (berbasis sesi, aman, dan efisien), menjadi dasar query kurasi **Berita Terpopuler**.
2. **Kurasi Hingga 5 Berita Utama (*Featured News*):**
   * Administrator dapat menandai dan mengatur hingga 5 artikel sebagai Berita Utama untuk ditayangkan pada carousel beranda dan banner headline.
3. **Penyempurnaan Aksesibilitas & UI Kontras Tinggi:**
   * Teks dan ikon dioptimalkan memenuhi standar kontras WCAG (`#0f172a`, `#1e293b`, `#475569`).
   * Seluruh ikon FontAwesome lama telah dimigrasikan ke native `<iconify-icon icon="solar:*">`.

### B. Fitur Event & Agenda
1. **Siklus Hidup Event Komprehensif:**
   * Mendukung siklus status: `draft`, `coming_soon`, `open_registration`, `full`, `finished`, dan `cancelled`.
2. **Presensi Berbasis Kode Unik (*Attendance Code*):**
   * Admin dapat men-generate kode presensi unik (misal: `BGW-EVT-XXXXX`), membuka presensi dengan pencatatan waktu buka, dan menutup presensi saat sesi berakhir.
   * Peserta melakukan input kode presensi secara mandiri dengan pencegahan ganda (*double check-in prevention*).
3. **Penerbitan Sertifikat Massal (*Batch Certificate Generation*):**
   * Mengotomatisasi penerbitan e-sertifikat resmi dalam 1 klik khusus bagi peserta berstatus `registered` yang hadir (`present`) pada event berstatus `finished`.
4. **Sistem Antrean Waitlist & Promosi Otomatis:**
   * Mengatur pendaftar yang melebihi kuota masuk ke antrean `waitlisted`. Saat terjadi pembatalan (`cancelled`), sistem mempromosikan antrean waitlist secara otomatis dan atomik.
5. **Arsitektur Scroll Anti-Getar (*Jitter-Free View*):**
   * Mengeliminasi bug getaran/flicker di ujung bawah viewport pada layar resolusi tinggi Windows:
     * Mengunci single scroll container pada `html { overflow-y: scroll !important; overflow-anchor: none !important; }`.
     * Menghapus competing scroll container pada `body { overflow-y: visible !important; }`.
     * Menstabilkan margin footer flexbox (`margin-block-start: 24px !important;`).
     * Menghapus GPU compositing layer thrashing (`transform: translateZ(0)`).
     * Memasang boundary clamp script aktif pada `pageScripts`.

### C. Sertifikat Digital & Verifikasi
1. **Integritas Sumber Sertifikat:**
   * Mencegah duplikasi melalui aturan database `UNIQUE(source_type, source_id)` baik untuk pembelajaran (*enrollment*) maupun event (*registration*).
2. **Verifikasi Publik Minimum Disclosure:**
   * Halaman verifikasi publik (`/verifikasi-sertifikat`) dan QR reader menampilkan keaslian sertifikat tanpa membocorkan data pribadi terproteksi (NIK/NIP/kontak).

---

## Suite Pengujian Otomatis & Spark Commands

BegawiPengadaan dilengkapi dengan rangkaian test suite berbasis CodeIgniter CLI Spark untuk memverifikasi fungsionalitas end-to-end tanpa bergantung pada browser manual:

```bash
# ====================================================================
# PENGUJIAN KOMUNIKASI (EVENT & BERITA)
# ====================================================================
php spark event:admin:test         # Verifikasi 13 modul admin event (CRUD, modal, lifecycle)
php spark event:attendance:test    # Verifikasi 11 alur presensi kode & batch sertifikat
php spark event:landing:test       # Verifikasi 8 fitur landing page /event dan detail
php spark news:admin:test          # Verifikasi CRUD berita, view counter, dan kurasi
php spark news:landing:test        # Verifikasi tampilan publik berita dan kategori

# ====================================================================
# PENGUJIAN HALAMAN UMUM, SERTIFIKAT & WORKSPACE
# ====================================================================
php spark about:contact:cert:test  # Verifikasi halaman Tentang, Kontak, dan Verifikasi Sertifikat
php spark certificate:system:test  # Verifikasi integritas sertifikat digital dan QR token
php spark certificate:smoke        # Smoke test database sertifikat
php spark workspace:smoke          # Smoke test integrasi layout dan header workspace

# ====================================================================
# PENGUJIAN ARENA BELAJAR, BUNGA RAMPAI & REGULASI
# ====================================================================
php spark arena:admin:test         # Verifikasi 11 modul admin siklus pembelajaran
php spark arena:e2e                # Verifikasi end-to-end kelulusan & asesmen peserta
php spark arena:landing:test       # Verifikasi showcase katalog pembelajaran
php spark knowledge:admin:test     # Verifikasi admin Bunga Rampai
php spark knowledge:test           # Verifikasi publik Bunga Rampai
php spark regulation:admin:test    # Verifikasi admin Regulasi PBJ
php spark regulation:test          # Verifikasi publik Repositori Regulasi
php spark home:landing:test        # Verifikasi beranda utama BegawiPengadaan
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
| **Dashboard: Komunikasi Berita** | `Production Ready` | `news:admin:test` (100% Pass) |
| **Dashboard: Komunikasi Event, Presensi, Batch Sertifikat**| `Production Ready` | `event:admin:test`, `event:attendance:test` (100% Pass) |
| **Dashboard: Arena Belajar Lifecycle** | `Production Ready` | `arena:admin:test`, `arena:e2e` (100% Pass) |
| **Dashboard: Bunga Rampai & Regulasi** | `Production Ready` | `knowledge:admin:test`, `regulation:admin:test` (100% Pass) |
| **Dashboard: Sistem Sertifikat Digital** | `Production Ready` | `certificate:system:test`, `certificate:smoke` (100% Pass) |
| **Workspace: Integrasi Multi-Role & Bookmark** | `Backend Ready` | `workspace:smoke` (100% Pass) |
| **Dashboard: Government Intelligence PBJ** | `Backend Ready` | Siap skema dan model query internal |

---

## Struktur Direktori & Dokumentasi Teknis

Seluruh dokumen spesifikasi teknis disimpan di dalam folder **`dokumen/`**:

```text
dokumen/
├── route-contract.md                  → Kontrak rute, HTTP method, permission, dan filter
├── ui-boundary.md                     → Batasan layout, template FiStudy vs WowDash, dan asset
├── auth-and-rbac.md                   → Matriks role, permission, status akun, dan keamanan
├── workflow-contract.md               → State machine registrasi, event, sertifikat, dan approval
├── file-storage-policy.md             → Tata kelola penyimpanan file privat dan proteksi unduhan
├── database-blueprint.md              → Arsitektur tabel, foreign key, index, dan constraint
├── database-erd.md                    → Entity Relationship Diagram sistem
├── data-dictionary.md                 → Kamus data lengkap tabel dan kolom
├── schema-fresh-v4-1.sql              → DDL skema database produksi versi terbaru
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
# Laporan Audit & Implementasi Hak Akses, Paradigma Double-Role & Desain Modular Workspace
**Sistem Informasi Pelatihan & Pengadaan Barang/Jasa — BegawiPengadaan**  
*Tanggal Audit & Pembaruan Arsitektur: 25 Oktober 2026*  
*Kepatuhan Sistem: Zero-Migration & Schema-Preservation Compliant (Tepat 16/16 Berkas Migrasi Terjaga Utuh)*

---

## 1. Eksekutif & Paradigma Baru Otorisasi

Berdasarkan penegasan strategis terkini, BegawiPengadaan menetapkan filosofi pemisahan tegas antara **Dashboard Admin** dan **Workspace Operasional**:

```mermaid
flowchart TD
    subgraph A["Alur Pendaratan Login (Universal Login Dispatcher)"]
        L[Pengguna Berhasil Masuk / Login] --> CHK{Evaluasi Peran Akun}
        CHK -->|super_administrator / administrator| D_ADMIN["DASHBOARD ADMIN (/dashboard)\n*Steril Khusus Eksekutif Platform*"]
        CHK -->|peserta / trainer / operator / SELURUH admin_{...}| WS["WORKSPACE OPERASIONAL (/workspace)\n*Sentral Seluruh Kegiatan & Operasional*"]
    end

    subgraph B["Konsep Double-Role Melekat di Workspace"]
        WS --> BASIC["1. PERAN DASAR MELEKAT (Inherent Basic Role)\nMode Peserta: Kelas, Tugas, Ujian, Event, Presensi, Sertifikat Diri, & Fitur Personal"]
        WS --> SUPP["2. PERAN TAMBAHAN OPERASIONAL (Supplemental Role)\nDitumpuk (Stacked) di atas Menu Dasar Sesuai Hak Wewenang"]
    end

    subgraph C["Diferensiasi Peran Tambahan di Workspace"]
        SUPP -->|admin_pembelajaran| AP["Admin Pembelajaran (Hybrid Full Scope)\nSemua Kelas + Penugasan Trainer/Operator + Pintas Dashboard Belajar"]
        SUPP -->|admin_event| AE["Admin Event (Hybrid Full Scope)\nSemua Event + Presensi/Sertifikat Event + Pintas Dashboard Event"]
        SUPP -->|admin_{modul_lain}| AO["Admin Modular Lainnya (admin_regulasi, admin_berita, dll.)\nMode Peserta Basic + Tombol Pintas ke Dashboard Modulnya"]
        SUPP -->|operator| OP["Operator (Assigned Scope Only)\nKelas & Event Ditugaskan + Hak Assign Trainer di Kelas/Event Tersebut"]
        SUPP -->|trainer| TR["Trainer (Assigned Scope Only)\nHanya Kelas & Event yang Ditugaskan Mengajar"]
    end
```

### 1.1. Empat Pilar Filosofi Baru:
1. **Dashboard Steril untuk Administrator & Super Administrator**:
   - Dasbor administratif tingkat tinggi (`/dashboard`) disterilkan saat proses *login landing*.
   - Hanya pengguna dengan peran murni `administrator` dan `super_administrator` yang mendarat langsung di `/dashboard`.
   - **Seluruh peran lainnya** (termasuk seluruh admin modular berprefix `admin_{...}`, `operator`, `trainer`, dan `peserta`) **langsung mendarat di Workspace (`/workspace`)**.
2. **Universalitas Seluruh Peran Berprefix `admin_{...}`**:
   - Peran berprefix `admin_` bersifat dinamis dan tidak terbatas pada `admin_pembelajaran` atau `admin_event` saja, melainkan mencakup seluruh modul seperti `admin_regulasi`, `admin_berita`, `admin_sertifikat`, `admin_kms`, `admin_survey`, dll.
   - Seluruh pemegang peran `admin_{...}` mendapatkan:
     - Akses dasar sebagai **Peserta (Basic)** di Workspace.
     - **Menu pintas (*quick shortcut*) ke Dashboard** di sidebar Workspace.
     - Saat membuka Dashboard, antarmuka sidebar dashboard secara otomatis menyaring (*filtering*) menu dan hanya menampilkan modul yang izinnya dimiliki oleh role tersebut.
3. **Double Role yang Melekat (*Inherent Baseline Role + Supplemental Role*)**:
   - Seluruh akun non-administrator secara inheren memiliki peran dasar sebagai **Peserta (Basic)**.
   - Mereka berhak mengikuti pelatihan, mendaftar event, mengerjakan evaluasi mandiri, presensi diri, serta mengklaim sertifikat pribadi.
   - Di samping peran dasar tersebut, mereka mendapatkan **peran operasional tambahan (*supplemental role*)** yang memunculkan grup menu operasional secara dinamis.
4. **Wewenang Penugasan Trainer oleh Operator pada Entitas yang Ditugaskan**:
   - Operator yang telah resmi ditugaskan (*assigned*) ke suatu kelas pembelajaran memiliki wewenang untuk **menugaskan (*assign*) Trainer** (`role`: `'primary'`, `'co_trainer'`, `'guest'`) pada kelas tersebut guna mendampingi pengajaran.
   - Operator yang ditugaskan ke suatu event memiliki wewenang mengelola narasumber/trainer pada event tersebut.
   - Operator tetap berada pada batas *assigned scope* (tidak dapat menugaskan trainer pada kelas/event yang tidak ditugaskan kepadanya, dan penugasan operator lain tetap wewenang Admin).

---

## 2. Pemetaan Komparatif Peran Pengguna di Platform

| Parameter Otorisasi | Peserta Murni | Trainer (Pengajar) | Operator (Asisten) | Admin Pembelajaran (Hybrid) | Admin Event (Hybrid) | Admin Modular Lain (`admin_regulasi`, dll.) | Administrator & Super Admin |
|:---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| **Pendaratan Awal Login** | `/workspace` | `/workspace` | `/workspace` | `/workspace` | `/workspace` | `/workspace` | `/dashboard` |
| **Akses Dashboard Admin** | ❌ Ditolak | ❌ Ditolak | ❌ Ditolak | ✅ Ya (Pintas Arena Belajar) | ✅ Ya (Pintas Modul Event) | ✅ Ya (Pintas Modul Terkait) | ✅ Penuh (Seluruh Modul) |
| **Penyaringan Sidebar Dashboard** | N/A | N/A | N/A | Hanya Arena Belajar | Hanya Komunikasi/Event | Hanya Modul yang Dimiliki | Menampilkan Seluruh Menu |
| **Ikut Belajar & Event (Mode Peserta)** | ✅ Penuh | ✅ Penuh | ✅ Penuh | ✅ Penuh | ✅ Penuh | ✅ Penuh | Sesuai Kebutuhan |
| **Cakupan Akses Kelas Belajar** | Kelas Terdaftar | **Kelas Ditugaskan (Assigned Only)** | **Kelas Ditugaskan (Assigned Only)** | **Seluruh Kelas PBJ (Full Scope)** | Kelas Terdaftar | Kelas Terdaftar | Seluruh Kelas PBJ |
| **Menugaskan Trainer ke Kelas** | ❌ Tidak | ❌ Tidak | ✅ **Bisa di Kelas yang Ditugaskan** | ✅ **Bisa di Semua Kelas** | ❌ Tidak | ❌ Tidak | ✅ Wewenang Penuh |
| **Menugaskan Operator ke Kelas** | ❌ Tidak | ❌ Tidak | ❌ Dibatasi (Wewenang Admin) | ✅ **Bisa di Semua Kelas** | ❌ Tidak | ❌ Tidak | ✅ Wewenang Penuh |
| **Cakupan Akses Event PBJ** | Event Terdaftar | **Event Ditugaskan (Narasumber)** | **Event Ditugaskan (Operasional)** | Event Terdaftar | **Seluruh Event PBJ (Full Scope)** | Event Terdaftar | Seluruh Event PBJ |
| **Kelola Presensi & Sertifikat Event** | Unduh Pribadi | Presensi Diri | ✅ Pada Event Ditugaskan | Peserta Pribadi | ✅ Pada Seluruh Event | Peserta Pribadi | ✅ Wewenang Penuh |
| **Approval Akun & Profil Pengguna** | ❌ Tidak | ❌ Tidak | ❌ **Dicabut (Kembali ke Admin)** | ❌ Tidak | ❌ Tidak | ❌ Tidak | ✅ Wewenang Penuh |

---

## 3. Desain Susunan Sidebar Workspace (Stacked Modular Sidebar)

Sidebar Workspace dirancang bertumpuk (*stacked*):
- **Komponen Wajib (Basic)** selalu tampil untuk semua peran non-administrator.
- **Komponen Tambahan (Supplemental)** disematkan di bagian atas sesuai peran aktif yang dimiliki.

```
SIDEBAR WORKSPACE BEGAWIPENGADAAN
│
├── [HEADER] Identitas Pengguna, Avatar Inisial, & Badge Multi-Role
│
├── [BLOK TAMBAHAN: Peran ADMIN_PEMBELAJARAN] (Jika Akun Memiliki Peran Ini)
│   └── Kelola Arena Belajar (Full Kelas)
│       ├── Beranda Operasional (`/workspace/operator`)
│       ├── Semua Kelas & Penugasan (`/workspace/operator/penugasan`)
│       ├── Ruang Asisten & Kurikulum (`/workspace/trainer/kelas`)
│       ├── Monitoring & Evaluasi Nilai (`/workspace/operator/monitoring`)
│       ├── Penerbitan Sertifikat Kelas (`/workspace/operator/sertifikat`)
│       └── [Tombol Aksi] Buka Dashboard Arena Belajar (`/dashboard/pembelajaran`)
│
├── [BLOK TAMBAHAN: Peran ADMIN_EVENT] (Jika Akun Memiliki Peran Ini)
│   └── Kelola Event PBJ (Full Event)
│       ├── Semua Agenda Event & Bimtek (`/workspace/operator/event`)
│       ├── Presensi & Kehadiran Event (`/workspace/operator/kehadiran`)
│       └── [Tombol Aksi] Buka Dashboard Event PBJ (`/dashboard/komunikasi/event-agenda`)
│
├── [BLOK TAMBAHAN: Peran ADMIN MODULAR LAINNYA] (admin_regulasi, admin_berita, dll.)
│   └── Akses Dashboard Modul
│       └── [Tombol Aksi] Buka Dashboard Admin -> Mengarah langsung ke modul yang diizinkan
│
├── [BLOK TAMBAHAN: Peran TRAINER] (Jika Akun Memiliki Peran Ini)
│   └── Pengajaran & Kelas (Ditugaskan)
│       ├── Beranda Pengajar (`/workspace/trainer`)
│       ├── Kelas & Kurikulum Saya (Assigned Only) (`/workspace/trainer/kelas`)
│       ├── Bank Materi Kelas (`/workspace/trainer/materi`)
│       ├── Soal & Assessment (`/workspace/trainer/soal`)
│       ├── Peserta & Progress (`/workspace/trainer/peserta`)
│       ├── Event Narasumber Saya (Assigned Only) (`/workspace/trainer/event`)
│       └── Presensi Kegiatan Saya (`/workspace/trainer/presensi`)
│
├── [BLOK TAMBAHAN: Peran OPERATOR] (Jika Akun Memiliki Peran Ini)
│   └── Asisten Operasional (Ditugaskan)
│       ├── Beranda Operasional (`/workspace/operator`)
│       ├── Kelas & Penugasan Trainer (Assigned Only) (`/workspace/operator/penugasan`)
│       ├── Ruang Asisten Trainer (`/workspace/trainer/kelas`)
│       ├── Monitoring Kelas Ditugaskan (`/workspace/operator/monitoring`)
│       ├── Verifikasi Peserta & Evaluasi (`/workspace/operator/evaluasi`)
│       ├── Sertifikat Kelas Ditugaskan (`/workspace/operator/sertifikat`)
│       ├── Event Pendampingan Saya (Assigned Only) (`/workspace/operator/event`)
│       └── Presensi Event Ditugaskan (`/workspace/operator/kehadiran`)
│
├── [BLOK TAMBAHAN: Peran ADMINISTRATOR / SUPER_ADMINISTRATOR]
│   ├── Komando Eksekutif & RBAC
│   │   ├── Pusat Eksekutif (`/workspace/admin`)
│   │   ├── Penugasan Kelas (RBAC) (`/workspace/operator/penugasan`)
│   │   ├── Monitoring Pembelajaran (`/workspace/operator/monitoring`)
│   │   ├── Operasional Event & Presensi (`/workspace/operator/event`)
│   │   ├── Verifikasi Sertifikat (`/workspace/operator/sertifikat`)
│   │   └── Dashboard Utama (`/dashboard`)
│   └── Pusat Approval & Verifikasi
│       ├── Antrean Approval Sistem (`/workspace/operator/approval`)
│       ├── Verifikasi Akun Baru (`/workspace/operator/verifikasi-profil`)
│       ├── Approval Perubahan Profil (`/workspace/operator/perubahan-profil`)
│       └── Audit Aktivitas Sistem (`/workspace/operator/monitoring-aktivitas`)
│
├── =========================================================================
├── [BLOK DASAR MELEKAT: AKTIVITAS PESERTA (BASIC)] (Tampil untuk Semua Non-Admin)
│   └── Pembelajaran & Kegiatan (Peserta)
│       ├── Beranda Workspace (`/workspace`)
│       ├── Kelas Belajar Saya (`/workspace/peserta/kelas-saya`)
│       ├── Tugas Mandiri (`/workspace/peserta/tugas`)
│       ├── Assessment & Ujian (`/workspace/peserta/assessment`)
│       ├── Event & Agenda Saya (`/workspace/peserta/event-saya`)
│       ├── Presensi Kehadiran (`/workspace/peserta/presensi-saya`)
│       └── Sertifikat Saya (`/workspace/peserta/sertifikat`)
│
└── [BLOK DASAR MELEKAT: FITUR PERSONAL & HELPDESK] (Tampil untuk Semua Akun)
    └── Fitur Personal & Helpdesk
        ├── Konsultasi & Room Chat (`/workspace/konsultasi`)
        ├── Bookmark Tersimpan (`/workspace/bookmark`)
        ├── Pusat Notifikasi (`/workspace/notifikasi`)
        └── Profil & Keamanan (`/workspace/profil`)
```

---

## 4. Mekanisme Penugasan & Isolasi Data (Zero-Migration Compliance)

Seluruh logika hak akses ini diimplementasikan tanpa migrasi baru atau perubahan tabel MySQL:

### 4.1. Penugasan Trainer oleh Operator pada Kelas yang Ditugaskan
- **Tabel Eksisting**: [`class_trainers`](file:///c:/laragon/www/begawipengadaan/app/Models/Learning/ClassTrainerModel.php)
- **Implementasi**:
  - Pada [`OperatorController::assignTrainer()`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/OperatorController.php), dilakukan pemeriksaan bahwa Operator yang sedang aktif memiliki relasi aktif di `class_trainers` untuk kelas terkait via `assertCanManageClass($classId)`.
  - Operator diizinkan menugaskan Trainer (`role`: `'primary'`, `'co_trainer'`, `'guest'`) dengan mencatat `assigned_by = $actorId`.
  - Operator dibatasi sehingga tidak bisa menugaskan Operator lain (penugasan Operator tetap wewenang Administrator/Admin Pembelajaran).

### 4.2. Penugasan Trainer pada Event yang Ditugaskan
- **Tabel Eksisting**: [`event_registrations`](file:///c:/laragon/www/begawipengadaan/app/Models/Communication/EventRegistrationModel.php) dan [`events`](file:///c:/laragon/www/begawipengadaan/app/Models/Communication/EventModel.php)
- **Implementasi**:
  - Metode [`OperatorController::assignEventTrainer()`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/OperatorController.php) dan rute POST `workspace/operator/event/penugasan-trainer` di [`Routes.php`](file:///c:/laragon/www/begawipengadaan/app/Config/Routes.php).
  - Operator yang ditugaskan ke event berhak menetapkan instruktur/narasumber pada event tersebut (`status: 'trainer'`, `status_reason: 'assigned_trainer'`).

### 4.3. Sterilisasi Akses Dashboard & Penarikan Approval Global
- **Akses Dashboard**:
  - [`AuthorizationService::hasDashboardAccess`](file:///c:/laragon/www/begawipengadaan/app/Services/Identity/AuthorizationService.php): Disterilkan sehingga `operator`, `trainer`, dan `peserta` murni bernilai `false`.
  - Hanya `super_administrator`, `administrator`, dan peran dinamis berprefix `admin_{...}` yang berhak mengakses Dashboard.
- **Penarikan Approval Global**:
  - Metode persetujuan akun (`approvals()`, `bulkApproval()`, `approveApproval()`, `rejectApproval()`, `profileVerifications()`, `bulkProfileVerification()`, `approveVerification()`, `rejectVerification()`, `profileChangeRequests()`, `profileChangeRequestDetail()`, `approveProfileChangeRequest()`, `rejectProfileChangeRequest()`, `revisionProfileChangeRequest()`, dan `activityMonitoring()`) di [`OperatorController.php`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/OperatorController.php) dikunci rapat menggunakan `assertAdminAccess()` (hanya `administrator` dan `super_administrator`).

---

## 5. Ringkasan Modifikasi Berkas

1. [`app/Controllers/Dashboard/Auth/LoginController.php`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Dashboard/Auth/LoginController.php):
   - Redirection disetel tegas: Hanya `super_administrator` dan `administrator` yang diarahkan ke `/dashboard`. Semua peran lain (termasuk `admin_*`, `operator`, `trainer`, `peserta`) dialihkan langsung ke `route_to('workspace.home')`.
2. [`app/Services/Identity/AuthorizationService.php`](file:///c:/laragon/www/begawipengadaan/app/Services/Identity/AuthorizationService.php):
   - Sterilisasi dashboard dari `operator` dan `trainer`.
   - Penyediaan pemetaan rute otomatis `getFirstAllowedDashboardRoute()` untuk seluruh prefix `admin_{...}` (`admin_pembelajaran` -> `/dashboard/pembelajaran`, `admin_event` -> `/dashboard/komunikasi/event-agenda`, `admin_regulasi` -> `/dashboard/regulasi/dokumen`, dll.).
3. [`app/Filters/WorkspaceAuthFilter.php`](file:///c:/laragon/www/begawipengadaan/app/Filters/WorkspaceAuthFilter.php):
   - Mengizinkan grup rute `/workspace/operator/*` untuk `operator`, `administrator`, `super_administrator`, serta seluruh role berprefix `admin_`.
   - Mengizinkan grup rute `/workspace/trainer/*` untuk `trainer`, `operator`, `administrator`, `super_administrator`, dan `admin_pembelajaran`.
4. [`app/Controllers/Workspace/OperatorController.php`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/OperatorController.php):
   - Mengisolasi skop kelas & event bagi Operator (hanya kelas & event yang ditugaskan).
   - Membuka fitur penugasan Trainer oleh Operator pada kelas/event yang ditugaskan.
   - Mengunci seluruh aksi persetujuan sistem, verifikasi akun, dan audit aktivitas agar hanya bisa dijalankan oleh Administrator penuh.
5. [`app/Controllers/Workspace/TrainerController.php`](file:///c:/laragon/www/begawipengadaan/app/Controllers/Workspace/TrainerController.php):
   - Memberikan hak kelola kelas penuh bagi `admin_pembelajaran`.
6. [`app/Views/main/workspace/components/sidebar.php`](file:///c:/laragon/www/begawipengadaan/app/Views/main/workspace/components/sidebar.php):
   - Mengimplementasikan Stacked Modular Sidebar dengan pembagian grup peran tambahan di atas, dan grup dasar (Peserta Basic + Fitur Personal) di bawah.
7. [`app/Config/Routes.php`](file:///c:/laragon/www/begawipengadaan/app/Config/Routes.php):
   - Mendaftarkan rute penugasan trainer event untuk operator: `workspace/operator/event/penugasan-trainer`.

---

## 6. Hasil Pengujian & Verifikasi Terpadu

Pengujian komprehensif dieksekusi melalui serangkaian test runner otomatis:

### 6.1. RbacDoubleRoleTest (`php spark test:rbac-double-role`)
```
==================================================================
     BEGAWIPENGADAAN RBAC DOUBLE-ROLE & WORKSPACE TEST SUITE      
==================================================================

--- [TEST 1] Login Redirection & Dashboard Sterilization ---
  [PASS] Administrator login mendarat langsung di Dashboard (/dashboard)
  [PASS] Super Administrator login mendarat langsung di Dashboard (/dashboard)
  [PASS] Operator login direct ke Workspace (/workspace)
  [PASS] Trainer login direct ke Workspace (/workspace)
  [PASS] Admin Pembelajaran login direct ke Workspace (/workspace)
  [PASS] Admin Event login direct ke Workspace (/workspace)
  [PASS] Admin Regulasi login direct ke Workspace (/workspace)
  [PASS] Peserta murni login direct ke Workspace (/workspace)

--- [TEST 2] Dashboard Access Guarding (AuthorizationService) ---
  [PASS] Super Administrator berhak mengakses Dashboard
  [PASS] Administrator berhak mengakses Dashboard
  [PASS] admin_pembelajaran berhak mengakses Dashboard
  [PASS] admin_event berhak mengakses Dashboard
  [PASS] admin_regulasi berhak mengakses Dashboard
  [PASS] Operator DITOLAK dari Dashboard (Steril)
  [PASS] Trainer DITOLAK dari Dashboard (Steril)
  [PASS] Peserta DITOLAK dari Dashboard (Steril)

--- [TEST 3] Dynamic Modular Route Mappings ---
  [PASS] admin_pembelajaran memetakan ke 'dashboard/pembelajaran'
  [PASS] admin_event memetakan ke 'dashboard/komunikasi/event-agenda'
  [PASS] admin_regulasi memetakan ke 'dashboard/regulasi/dokumen'

--- [TEST 4] Stacked Modular Sidebar in Workspace ---
  [PASS] Sidebar Operator memuat blok 'Asisten Operasional (Ditugaskan)'
  [PASS] Sidebar Operator memuat peran dasar melekat 'Pembelajaran & Kegiatan (Peserta)'
  [PASS] Sidebar Operator memuat 'Fitur Personal & Helpdesk'
  [PASS] Sidebar Operator TIDAK memuat 'Pusat Approval & Verifikasi' (Steril)
  [PASS] Sidebar Trainer memuat blok 'Pengajaran & Kelas (Ditugaskan)'
  [PASS] Sidebar Trainer memuat peran dasar melekat 'Pembelajaran & Kegiatan (Peserta)'
  [PASS] Sidebar Admin Pembelajaran memuat blok 'Kelola Arena Belajar (Full Kelas)'
  [PASS] Sidebar Admin Pembelajaran memuat tombol 'Buka Dashboard Arena Belajar'
  [PASS] Sidebar Admin Pembelajaran memuat peran dasar melekat 'Pembelajaran & Kegiatan (Peserta)'
  [PASS] Sidebar Admin Event memuat blok 'Kelola Event PBJ (Full Event)'
  [PASS] Sidebar Admin Event memuat tombol 'Buka Dashboard Event PBJ'
  [PASS] Sidebar Admin Regulasi memuat tombol pintas 'Buka Dashboard Admin'
  [PASS] Sidebar Admin Regulasi memuat peran dasar melekat 'Pembelajaran & Kegiatan (Peserta)'
  [PASS] Sidebar Administrator memuat blok 'Komando Eksekutif & RBAC'
  [PASS] Sidebar Administrator memuat blok 'Pusat Approval & Verifikasi'

--- [TEST 5] Operator Controller Scope & Action Guards ---
  [PASS] Operator murni TIDAK memiliki Full Class Scope (Assigned Only)
  [PASS] Operator murni TIDAK memiliki Full Event Scope (Assigned Only)
  [PASS] admin_pembelajaran MEMILIKI Full Class Scope (Seluruh Kelas)
  [PASS] admin_event MEMILIKI Full Event Scope (Seluruh Event)
  [PASS] administrator MEMILIKI Full Class Scope
  [PASS] administrator MEMILIKI Full Event Scope

--- [TEST 6] Zero-Migration & Schema-Preservation Compliance ---
  [PASS] Tepat 16 berkas migrasi database eksisting terjaga utuh (Zero Migration = 100%)

==================================================================
 Total Assertions : 41 | Passed : 41 | Failed : 0 (100% PASS)
==================================================================
```

### 6.2. Uji Regresi Lainnya
- **Uji Sistem Notifikasi** (`php spark test:notification-system`): 24/24 Berhasil (100%).
- **Uji Notifikasi Berbagi Pakai / Shared Pool** (`php spark test:shared-notifications`): 15/15 Berhasil (100%).
- **Uji Workflow Approval Pendaftaran Event** (`php spark test:event-approval-workflow`): Berhasil (100%).
- **Status Migrasi Basis Data** (`php spark migrate:status`): Tepat 16 berkas migrasi eksisting, 0 migrasi baru, 0 modifikasi skema tabel.

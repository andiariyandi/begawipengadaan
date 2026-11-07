# BegawiPengadaan — Developer Onboarding & Scaffolding Standards

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Panduan Standar Penambahan Modul Baru

Saat menambahkan modul atau fitur baru di BegawiPengadaan, seluruh pengembang wajib mengikuti konvensi penempatan file, validasi skema basis data, dan aturan isolasi antarmuka:

### Aturan 1: Controller Placement & Naming
- Controller publik diletakkan di `App\Controllers\Landing\`.
- Controller ruang kerja diletakkan di `App\Controllers\Workspace\`.
- Controller back-office diletakkan di `App\Controllers\Admin\` atau sub-namespace domain (`Learning`, `Communication`, `Certificate`).
- Dilarang membuat controller otentikasi baru di luar `App\Controllers\Dashboard\Auth\`.

### Aturan 2: Service Layer & Business Logic
- Controller hanya bertindak sebagai pengendali alur HTTP (request validation & response formatting).
- Seluruh manipulasi data, transaksi atomik, dan kalkulasi wajib diletakkan di `App\Services\`.
- Penyimpanan berkas wajib menggunakan `FileStorageService::registerFileAsset()`.
- Query publik wajib diproteksi menggunakan `PublicVisibilityGuard`.

### Aturan 3: Model & Skema Database (`audit:schema-drift`)
- Setiap model di `App\Models\` wajib mendeklarasikan `$table` dan `$allowedFields` yang 100% selaras dengan skema tabel MySQL.
- Dilarang mencantumkan kolom fiktif pada `$allowedFields`.
- Dilarang membuat file migrasi baru (tetap 17 berkas migrasi resmi).
- Jalankan `php spark audit:schema-drift` untuk memvalidasi model baru.

### Aturan 4: View & UI Boundary (`audit:ui-boundary`)
- View landing wajib menggunakan aset `assets/main/`.
- View dashboard wajib menggunakan aset `assets/dashboard/`.
- Dilarang menggunakan folder `app/Views/reference/`.
- Jalankan `php spark audit:ui-boundary` untuk memverifikasi kebersihan view.

### Aturan 5: Pendaftaran Izin RBAC
- Setiap endpoint baru yang memerlukan otorisasi wajib mendaftarkan permission di `database/permissions_catalog.json`.
- Jalankan `php spark permission:sync` untuk menyinkronkan izin ke database.

# BegawiPengadaan — UI Boundary Contract & Template Isolation

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Prinsip Isolasi Mutlak Antarmuka (Dual-Template Monolith)

Aplikasi BegawiPengadaan menerapkan pemisahan ketat antara dua template basis:
1. **FiStudy (Frontend / Workspace):** Digunakan secara eksklusif untuk **Landing Page Publik** (`app/Views/landing/`) dan **Ruang Kerja Pengguna Terautentikasi** (`app/Views/main/workspace/`). Seluruh aset statis berada di bawah `public/assets/main/`.
2. **WowDash (Back-Office / Auth):** Digunakan secara eksklusif untuk **Pintu Masuk Autentikasi** (`app/Views/dashboard/auth/`) dan **Dashboard Administratif Back-Office** (`app/Views/dashboard/`). Seluruh aset statis berada di bawah `public/assets/dashboard/`.

### Matriks Batasan Antarmuka & Aset Statis:

| Zona Sistem | Template | Controller Namespace | Root View Path | Root Asset Path | Pustaka Mandiri |
|---|---|---|---|---|---|
| **Landing Public** | FiStudy | `App\Controllers\Landing\` | `app/Views/landing/` | `public/assets/main/` | `iconify-icon.min.js`, Swiper, AOS |
| **Workspace** | FiStudy | `App\Controllers\Workspace\` | `app/Views/main/workspace/` | `public/assets/main/` | `apexcharts.min.js`, `begawi-batch-runner.js` |
| **Auth Terpadu** | WowDash | `App\Controllers\Dashboard\Auth\` | `app/Views/dashboard/auth/` | `public/assets/dashboard/` | WowDash Core CSS/JS |
| **Dashboard** | WowDash | `App\Controllers\Admin\`, `Learning\`, dll. | `app/Views/dashboard/` | `public/assets/dashboard/` | WowDash ApexCharts, DataTables |

---

## 2. Larangan Folder Reference & Isolasi Dead Code

Folder `app/Views/reference/` telah **DIHAPUS SECARA PERMANEN** dari sistem file proyek:
- Template mentah reference tidak boleh berada di direktori aplikasi produksi guna mencegah celah keamanan, *dead code*, dan kebocoran rute.
- Keberadaan kembali folder `app/Views/reference/` akan memicu kegagalan audit *fail-closed* pada pengujian otomatis.

---

## 3. Cara Kerja Watchdog `php spark audit:ui-boundary`

Pemeriksaan otomatis batas antarmuka dieksekusi melalui command Spark CLI khusus:
```bash
php spark audit:ui-boundary
```
Watchdog ini menjalankan 4 kriteria sterilisasi:
1. Memverifikasi bahwa folder `app/Views/reference/` **tidak ada di sistem file** (`PASS`).
2. Memindai seluruh berkas PHP di `app/Views/landing/`: memastikan **0 kemunculan token `assets/dashboard/`** (`PASS`).
3. Memindai seluruh berkas PHP di `app/Views/main/workspace/`: memastikan **0 kemunculan token `assets/dashboard/`** (`PASS`).
4. Memindai seluruh berkas PHP di `app/Views/dashboard/`: memastikan **0 kemunculan token `assets/main/`** (`PASS`).

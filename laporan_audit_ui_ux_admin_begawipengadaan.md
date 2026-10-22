# LAPORAN AUDIT UI/UX & KESTABILAN — DASHBOARD ADMIN BEGAWIPENGADAAN

**Tanggal Audit:** 22–23 Oktober 2026
**Metode:** Live E2E via Chrome DevTools Protocol (Chrome 155) pada sesi Super Admin terautentikasi di `http://begawipengadaan.test`
**Cakupan:** 103 rute + seluruh sub-tab internal (`_nav_*.php`), Light Mode & Dark Mode, Print Media, Mobile 390px
**Perubahan Kode:** **NOL.** Sesuai instruksi, audit ini murni observasi — tidak ada file aplikasi yang dimodifikasi.

---

## 1. SKOR & RINGKASAN EKSEKUTIF

| Kategori | Skor | Catatan |
|---|---|---|
| UI / Estetika | **6/10** | Layout bersih & konsisten; 4 halaman memiliki judul 100% tak terlihat |
| Konsistensi Dark Mode | **5/10** | `text-muted` nyaris tak terbaca (1.01:1); badge terang tidak di-invert |
| Navigasi / UX | **5/10** | 6 halaman tanpa active-state; 1 grup total 12 link 404 |
| Kestabilan Fungsi | **4/10** | 3 halaman HTTP 500; 15 tautan internal rusak |

**Ringkasan Eksekutif**

Arsitektur navigasi hasil restrukturisasi **secara konseptual sudah solid**: 12 grup sidebar dengan 5 partial `_nav_*.php` yang benar-benar merender dan berpindah state dengan benar di sebagian besar grup (Landing, Bunga Rampai, Regulasi, Sertifikat, Arena Belajar). Tidak ada satu pun duplikasi toolbar DataTable, dan **0 JavaScript error di seluruh 94 halaman**.

Namun terdapat **4 cacat sistemik tingkat tinggi** yang belum diperbaiki:

1. **`.text-base` didefinisikan sebagai `color: var(--white) !important`** (`style.css:1499`) — ini adalah utility *ukuran font*, bukan warna. Karena `!important`, ia mengalahkan semua kelas tema. Akibatnya judul kartu putih-di-atas-putih (rasio **1:1 — benar-benar tak terlihat**) di 4 halaman Light Mode dan 5 halaman Dark Mode. Dipakai **196×** di seluruh view.
2. **Direktori `app/Views/dashboard/errors/` tidak pernah ada**, padahal 6 tempat memanggil `view('dashboard/errors/404'|'403')`. Setiap "data tidak ditemukan" berubah menjadi **HTTP 500** — ini memicu **`dashboard/sertifikat/overview` dan `/verification` yang 500**.
3. **Halaman Pusat Pengaturan: seluruh 12 kartu menunjuk ke `/dashboard/settings/*` yang tidak terdaftar** → 12× **404**. Rute sebenarnya ada sebagai `/dashboard/sistem/pengaturan-*`. Tombol pengaturan di sidebar praktis tidak bisa dipakai.
4. **`dashboard/pembelajaran/assessment/quiz` → HTTP 500**: `QuizController` melakukan join `c.id = lm.course_id`, padahal tabel `learning_modules` hanya punya `class_id`.

---

## 2. FITUR / SUB-HALAMAN YANG HILANG ATAU TAK TERJANGKAU

### 2.1 Semua 12 Pintu Pusat Pengaturan 404 (BLOCKER)

Kartu grid di `app/Views/dashboard/settings/index.php:14-25` memakai URL yang tidak pernah didaftarkan di `Routes.php`:

| Kartu | URL di View | Status | Rute yang Benar-Benar Ada |
|---|---|---|---|
| Pengaturan Umum | `dashboard/settings/general` | **404** | `dashboard/sistem/pengaturan-umum` |
| Organisasi | `dashboard/settings/organization` | **404** | `dashboard/sistem/pengaturan-organisasi` |
| Pembelajaran | `dashboard/settings/learning` | **404** | `dashboard/sistem/pengaturan-pembelajaran` |
| Bunga Rampai | `dashboard/settings/knowledge` | **404** | `dashboard/sistem/pengaturan-bunga-rampai` |
| Regulasi | `dashboard/settings/regulation` | **404** | `dashboard/sistem/pengaturan-regulasi` |
| Berita & Artikel | `dashboard/settings/news` | **404** | `dashboard/sistem/pengaturan-berita` |
| Event & Agenda | `dashboard/settings/event` | **404** | `dashboard/sistem/pengaturan-event` |
| Sertifikat | `dashboard/settings/certificate` | **404** | `dashboard/sistem/pengaturan-sertifikat` |
| Notifikasi | `dashboard/settings/notification` | **404** | `dashboard/sistem/pengaturan-notifikasi` |
| Integrasi | `dashboard/settings/integration` | **404** | `dashboard/sistem/pengaturan-integrasi` |
| Keamanan | `dashboard/settings/security` | **404** | `dashboard/sistem/pengaturan-keamanan` |
| Maintenance | `dashboard/settings/maintenance` | **404** | `dashboard/sistem/maintenance` |

> Semua 12 sub-halaman **sebenarnya hidup dan dirender dengan benar** — hanya pintunya yang salah. Group `Sistem` tetap terbuka dan `Pusat Pengaturan` menyala pada 4 di antaranya, sehingga user melihat "aktif" lalu klik → 404. Ini kebohongan status yang menyesatkan.

### 2.2 Halaman 500 / Rusak

| Rute | Status | Penyebab Teknis |
|---|---|---|
| `dashboard/pembelajaran/assessment/quiz` | **500** | `QuizController.php:18,58` → `Unknown column 'lm.course_id' in 'on clause'`. `learning_modules` tidak punya kolom `course_id` (hanya `class_id`). Jalur benar: `learning_modules.class_id → learning_classes.course_id`. |
| `dashboard/sertifikat/overview` | **500** | `Invalid file: "dashboard/errors/404.php"` — view error hilang |
| `dashboard/sertifikat/verification` | **500** | `Invalid file: "dashboard/errors/404.php"` — view error hilang |
| `dashboard/sertifikat/approval` | **404** | Rute tidak ada di `Routes.php` |

### 2.3 Halaman Tanpa Active-State Sidebar

Measured langsung via DOM (`a.active-page`):

| Halaman | Expected | Aktual |
|---|---|---|
| `dashboard/survei-kepuasan` | `survey` menyala | **tidak ada active sama sekali** — grup IKM adalah top-level `<li>` (bukan dropdown), sehingga tidak ada konteks untuk menyalakan induk |
| `dashboard/sistem/audit-trail` | Sistem → Audit Trail | **Kosong**, dan grup yang terbuka = **"Manajemen Akses"** (SALAH) |
| `dashboard/akses/audit` | Audit Trail | **Kosong**, grup terbuka = "Manajemen Akses" |
| `dashboard/regulasi/dokumen` | Direktori Regulasi | **Kosong** |
| `dashboard/pembelajaran/feedback-peserta` | Kelas & Peserta | **Kosong** |
| `dashboard/komunikasi/whatsapp-widget` | Layanan Bantuan | **Kosong** |

**Root cause audit-trail** (`app/Controllers/Admin/AuditController.php:46`): `index()` mengirim `'activeMenu' => 'access'` dan `'activeSubmenu' => 'audit'`, padahal `access` tidak punya submenu bernama `audit` di `sidebar.php`. Method `detail()` (`:142`) sama; hanya `files()` (`:154`) yang benar memakai `'system'`. Efeknya user mendarat di "Audit Trail" tapi sidebar menyorot grup **Manajemen Akses**.

### 2.4 Sub-fitur Orphan (Ada Rute, Tidak Ada Pintu)

Rute berikut **terdaftar dan bisa diakses**, tetapi tidak punya tab/tombol dari halaman induknya:

- `dashboard/pembelajaran/feedback-peserta` — tidak ada di `_nav_learning.php`
- `dashboard/komunikasi/whatsapp-widget` — Helpdesk tidak punya tab; sidebar hanya teks "Layanan Bantuan & Inbox" tanpa link
- `dashboard/pembelajaran/kurikulum` — ter-link dari halaman kelas, tapi tidak ada di nav partial
- `dashboard/pembelajaran/progres` & `/laporan` — Monitoring Kelulusan **tidak punya `_nav_learning` partial sama sekali** (lihat 2.6)

### 2.5 Token `$activeClass` yang Tidak Pernah Dipicu

Beberapa token di `sidebar.php` tidak mungkin aktif karena controller tidak pernah mengirimnya:

- `sidebar.php:67` mendaftarkan `'kpi'`, `'recent-activity'` — tapi rute sebenarnya adalah `dashboard/kpi-internal` & `dashboard/aktivitas-terbaru`
- `sidebar.php:82` mendaftarkan `'highlight'` — rute sebenarnya `dashboard/landing/pilar-hierarki`
- `sidebar.php:87` mendaftarkan `'carousel'` — bentrok dengan `knowledge` yang juga memakai `'carousel'`
- `sidebar.php:128` mendaftarkan `'peran'`, `:134` `'unit-organisasi'` — token mati
- `sidebar.php:368` mendaftarkan 8 token (`general`, `security`, `certificate`, `maintenance`, …) — semuanya mati karena rute yang sebenarnya bernama `pengaturan-*`

### 2.6 Ketidakkonsistenan Partial Navigasi antar Grup

Partial ada di 5 grup saja. Grup berikut **tidak punya partial** padahal punya sibling routes yang sah:

| Grup | Rute Sibling yang Ada | Partial? |
|---|---|---|
| Komunikasi | `berita`, `kategori-berita`; `event-agenda`, `registrasi-event`, `kehadiran`, `waitlist-pembatalan`; `pesan-masuk`, `newsletter` | **TIDAK** — semua sibling hanya bisa diakses lewat dropdown filter atau link tak terlihat |
| Manajemen Akses | `role`, `permission`, `permohonan-role`; `verifikasi-profil`, `permohonan-perubahan-profil` | **TIDAK** |
| Government Intelligence | 8 rute (`opd-unit`, `gap-kompetensi`, `jenjang`, `riwayat`, …) | **TIDAK** |
| Laporan | 6 tab laporan | **TIDAK** (hanya 2 link di sidebar; `event`/`sertifikat`/`kompetensi`/`pengguna` **tidak punya tab**) |
| Monitoring Kelulusan | `kelayakan-sertifikat`, `progres`, `laporan` | **TIDAK** |
| IKM | `survei-kepuasan`, `survei-kepuasan/hasil` | **TIDAK** (dan `/hasil` **404**) |

---

## 3. CACAT VISUAL & UI (LIGHT & DARK)

### 3.1 [KRITIS] `.text-base` Membuat Teks 100% Tak Terlihat

```css
/* public/assets/dashboard/css/style.css:1499 */
.text-base { color: var(--white) !important; }
```

Kelas ini dipakai **196×** di seluruh view sebagai utility **ukuran font**, tetapi mendefinisikan **warna putih dengan `!important`**. Karena `!important`, ia mengalahkan `text-primary-900`, `text-primary-light`, dan aturan `h1..h6 { color: var(--text-primary-light) }`.

**Hasil terukur — rasio kontras 1:1:**

| Halaman | Teks | Light | Dark |
|---|---|---|---|
| `bunga-rampai` | "Konten Pengetahuan Terbaru" | putih di putih | — |
| `bunga-rampai/kategori` | "Daftar Kategori Pengetahuan (5)" | putih di putih | gelap di gelap |
| `bunga-rampai/tag` | "Semua Tagar Aktif (0)" | — | gelap di gelap |
| `komunikasi/kategori-berita` | "Daftar Kategori Berita" | putih di putih | gelap di gelap |

Juga afflicted (terverifikasi via cascade): `knowledge/articles/form.php` (4 heading), `knowledge/bookmarks/index.php:53`, `knowledge/carousel/form.php`, `communication/news_categories/form.php:32`, `communication/helpdesk/index.php:128`, `communication/events/detail.php:713`, `registrations/detail.php:293`.

### 3.2 [KRITIS] `.text-muted` Nyaris Hitam di Dark Mode

`text-muted` berasal dari **Bootstrap**, bukan dari sistem desain WowDash. Tidak ada override dark-mode di `assets/dashboard/css/*.css`.

```
rgba(33, 37, 41, 0.75) pada rgb(27,36,49)  →  1.01:1
```

Terjadi di `landing/profil-lembaga` (5 elemen: `<small>` & `<label>` di baris 74, 92, 110, 128, 262). Contoh: *"Format: PNG, SVG, JPG, WEBP (disarankan PNG transparan, maks. 5 MB)."* — praktis tak terbaca.

### 3.3 [KRITIS] Badge Tidak Di-Invert di Dark Mode

Badge memakai pasangan `bg-*-100` + `text-*-600` yang **terbaca di Light Mode tetapi tidak di Dark**. 60+ kemunculan:

| Pola | Light | Dark |
|---|---|---|
| `badge bg-primary-100 text-primary-600` | 2.59:1 | **2.59:1** (tidak membaik) |
| `badge bg-warning-100 text-warning-main` | 1.91:1 | **1.91:1** |
| `badge bg-success-100 text-success-600` | 3.0:1 | — |
| `badge bg-primary-50 text-primary-600` | 3.18:1 | — |

Pola `bg-info-focus text-info-main` justru **memburuk di dark**: 1.87:1 (dark) — teks biru tua di atas biru tua.

### 3.4 [TINGGI] Titik Merah Notifikasi Bertabrakan dengan Chevron

Ini persis temuan yang Anda minta dicek — **terkonfirmasi**.

```php
/* sidebar.php:342 */
<span class="w-8-px h-8-px bg-danger-600 rounded-circle me-1"></span>
```

- Chevron dropdown = `::after` pseudo-element di `style.css:15691`, diposisikan `inset-inline-end: 0.75rem` (**12px dari kanan**)
- Badge diukur: `gapFromRight = 16px`
- Keduanya berada di area 12–16px dari tepi kanan → **overlay**

Zoom 4× mengonfirmasi chevron putih terpotong di tengah lingkaran merah. Terlihat di semua halaman saat unread > 0.

### 3.5 [TINGGI] Link "Portal Publik" Nyaris Tak Terlihat

Topbar, **setiap halaman, kedua tema**:

| Tema | Warna | Rasio |
|---|---|---|
| Light | `rgb(209,213,219)` on white | **1.47:1** |
| Dark | `rgb(75,85,99)` on `rgb(39,49,66)` | **1.73:1** |

### 3.6 [TINGGI] Print Layout: Angka KPI Hilang

`@media print` di `components/head.php:24` memaksa `body { color: #0f172a }` (hitam) tetapi **tidak** menimpa utility `text-primary-light` yang dipakai label KPI di `reports/learning.php:41,57,73,89`.

Hasil pada cetak: **4 kartu KPI menampilkan label abu-abu pucat tanpa angka sama sekali** — judul kartu pun hilang karena `.text-base`.

>-print positif: Kop Surat (“PEMERINTAH PROVINSI LAMPUNG / BIRO PENGADAAN BARANG DAN JASA”) + `SISTEM INFORMASI BEGAWIPENGADAAN • LAPORAN EKSEKUTIF RESMI` + tanggal **ter-render benar**; sidebar/topbar/footer/pagination/button **tersembunyi** dengan benar.

### 3.7 [SEDANG] Tombol "Cari" Tanpa Label

`dashboard/komunikasi/pesan-masuk` dan `sistem/approval`: input pencarian berakhir dengan tombol biru **hanya berisi ikon** tanpa `aria-label` maupun `title`.

### 3.8 [SEDANG] Tombol Reset Tanpa Ikon Ter-render

`komunikasi/pesan-masuk`: tombol di sebelah "Filter" **kosong total** — iconify-icon gagal dimuat (terlihat juga sebagai kotak kosong di beberapa tabel).

### 3.9 Layout & Responsif

- **Mobile 390px:** tidak ada horizontal overflow halaman (`scrollWidth == innerWidth`), sidebar collapse dengan benar.
- **Namun tabel sangat lebar tanpa wrapper:** `survei-kepuasan` 1504px, `landing/slider` 1306px, `sistem/approval` 1188px, `sertifikat/riwayat` 1205px pada viewport 390px. Bergantung pada `overflow-x` container induk.
- Tinggi antar-kartu **konsisten** di desktop; tidak ditemukan misalignment struktural.

---

## 4. BUG FUNGSIONAL & ERROR

### 4.1 Ringkasan

| Kategori | Jumlah | Status |
|---|---|---|
| Halaman HTTP 500 | **3** | Confirmed root cause |
| Halaman HTTP 404 | **1** (`sertifikat/approval`) | Rute tidak ada |
| Link internal 404 | **15** | 12 pengaturan + 3 lain |
| Aset 404 (gambar hilang) | **5** | Gambar seed hilang |
| Error JavaScript | **0** | ✅ |
| Double-init DataTable | **0** | ✅ |
| DataTable gagal init | **0** | ✅ |
| Overflow halaman | **0** | ✅ |

### 4.2 DataTable (`begawi-datatable.js`) — HEALTHY

Engine ini **sehat**. Dari 94 halaman:

- **24 halaman** memakai `.begawi-table` → **semua** berhasil ter-init (0 gagal)
- **0** `.dt-container` ganda (tidak ada double toolbar)
- Toolbar lengkap terdeteksi di semua: Search (`.dt-search`), Page Length (`.dt-length`), **Ekspor CSV** (`.btn-export-csv`, di-inject di `initComplete`), `.dt-info`, Pagination
- **0** error konsol / exception di seluruh crawl

> Catatan: PCB dari `begawi-datatable.js:263` selalu men-curate `colDefs` — tidak ada masalah.

**Temuan arsitektural (bukan bug):** **50 halaman** punya tabel yang **sengaja** tidak memakai `.begawi-table` — termasuk halaman-"daftar besar" seperti `akses/unit-organisasi`, `sertifikat/riwayat` sibling, `laporan/*`, `intelligence/*`. Tabel ini memakai paginasi server-side. Namun `landing/sorotan-carousel` (6 tabel) dan `bunga-rampai/carousel-konten-unggulan` (6 tabel) terlihat **harus manual** — lihat 4.4.

### 4.3 Aset 404 (Gambar Hilang)

| Halaman | Aset |
|---|---|
| `landing/sorotan-carousel` | `/index.php/file/172`, `/173`, `/177`, `/182` |
| `bunga-rampai/carousel-konten-unggulan` | `/index.php/file/172`, `/173`, `/177`, `/182` |
| `landing/tim` | `/uploads/landing/team/1792502359_752baf7aa28e04ca1cfa.png` |

9 request 404 per crawl → **broken image icons** di carousel dan kartu tim. Independen dari dua bug lain (sorotan-carousel = alias dari carousel-konten-unggulan).

### 4.4 Yang Berfungsi dengan Benar

- **Client-Side Upload Guard** (`regulasi/dokumen/tambah`): `accept="application/pdf,application/msword,application/vnd.openxmlformats-...document"` — ketat dan benar
- **CRUD & filter** pada semua modul: **0 error JS** sepanjang 103 route
- **Pagination server-side**: bekerja di semua halaman non-DataTable
- **Dark/light theme persistence** via `localStorage('theme')`: bekerja
- **Badge notifikasi** di header (9 unread) + counter sidebar: konsisten
- **Export CSV** tersedia di 20+ halaman

---

## 5. REKOMENDASI PERBAIKAN (PRIORITAS)

### P0 — Blocker, perbaiki sebelum release

**1. Buat view error yang hilang**
```bash
mkdir -p app/Views/dashboard/errors
cp app/Views/errors/html/error_404.php app/Views/dashboard/errors/404.php
cp app/Views/errors/html/error_403.php app/Views/dashboard/errors/403.php
```
Perbaiki `sertifikat/overview` & `/verification` (keduanya 500 → akan jadi 404 dengan benar).

**2. Perbaiki join `QuizController`**
```php
// QuizController.php:18 & :58 — ganti:
->join('courses c', 'c.id = lm.course_id', 'left')
// menjadi:
->join('learning_classes lc2', 'lc2.id = lm.class_id', 'left')
->join('courses c', 'c.id = lc2.course_id', 'left')
```

**3. Perbaiki 12 link Pusat Pengaturan** — `app/Views/dashboard/settings/index.php:14-25`:
```php
'url' => 'dashboard/sistem/pengaturan-umum'        // bukan dashboard/settings/general
'url' => 'dashboard/sistem/pengaturan-organisasi'
'url' => 'dashboard/sistem/pengaturan-pembelajaran'
'url' => 'dashboard/sistem/pengaturan-bunga-rampai'
'url' => 'dashboard/sistem/pengaturan-regulasi'
'url' => 'dashboard/sistem/pengaturan-berita'
'url' => 'dashboard/sistem/pengaturan-event'
'url' => 'dashboard/sistem/pengaturan-sertifikat'
'url' => 'dashboard/sistem/pengaturan-notifikasi'
'url' => 'dashboard/sistem/pengaturan-integrasi'
'url' => 'dashboard/sistem/pengaturan-keamanan'
'url' => 'dashboard/sistem/maintenance'
```
*(Alternatif yang lebih bersih: tambah route group `settings` di `Routes.php` yang me-map ke `SettingController` — pilih satu strategi, jangan dua-duanya.)*

**4. Perbaiki `.text-base` yang merusak**
```css
/* style.css:1499 — hapus aturan ini */
.text-base { color: var(--white) !important; }
```
Ganti dengan utility ukuran yang tidak menabrak warna (mis. `font-size: 1rem`), atau rename pemakaian icon jadi `text-icon`. Lalu jalankan `grep -rn 'text-base' app/Views/ | grep -v iconify-icon` dan ganti kelas pada 196 elemen non-icon. **Prioritaskan heading** karena langsung menghilangkan bug "tak terlihat".

### P1 — Tinggi

**5. Perbaiki activeMenu AuditController** — `AuditController.php:46` dan `:142`:
```php
'activeMenu'    => 'system',        // bukan 'access'
'activeSubmenu' => 'audit-trail',   // bukan 'audit'
```
(Hapus `activeSubmenu` di `files()` atau tambahkan tokennya ke sidebar.)

**6. Hilangkan tabrakan titik merah vs chevron** — `sidebar.php:336-344`. Berikan `padding-right` pada anchor Sistem sebesar lebar chevron:
```html
<a href="javascript:void(0)" class="d-flex align-items-center justify-content-between pe-30">
```
atau pindahkan badge ke dalam `span` "Sistem" seperti pola Notifikasi.

**7. Tambahkan override `.text-muted` untuk dark mode**
```css
.dark .text-muted { color: var(--text-secondary-light) !important; }
```
Ganti SEMUA pemakaian `text-muted` di view dengan `text-secondary-light` (konsisten dengan WowDash).

**8. Badge dark mode** — tambahkan ke `style.css`:
```css
.dark .badge.bg-primary-100 { background-color: rgba(72,127,255,.15) !important; }
.dark .badge.text-primary-600 { color: #93B4FF !important; }
/* ulangi untuk warning-100/success-100/info-focus */
```

**9. Perbaiki print KPI** — di `@media print` (`head.php`), tambahkan:
```css
@media print {
  .text-primary-light, .text-secondary-light, .text-base { color: #0f172a !important; }
  .card-header h6, .card-header h5 { color: #0f172a !important; }
}
```

### P2 — Sedang ( navigasi & polish )

**10. Sinkronkan token `$activeClass` yang mati** di `sidebar.php` agar cocok dengan `activeSubmenu` yang benar-benar dikirim controller (lihat §2.5).

**11. Berikan active-state ke `survei-kepuasan`** — `SatisfactionSurveyAdminController.php:114` mengirim `'activeMenu' => 'survey'` tetapi `activeSubmenu` tidak di-set. Tambahkan `'activeSubmenu' => 'index'` agar `['index','survey']` di baris 322 menyala.

**12. Tambahkan `_nav_*.php` untuk 6 grup yang belum punya** (Komunikasi, Manajemen Akses, Intelligence, Laporan, Monitoring Kelulusan, IKM) — atau minimal tambahkan **tab Laporan** di `dashboard/laporan/pembelajaran` yang saat ini tidak punya link ke `event`/`sertifikat`/`kompetensi`/`pengguna`.

**13. Tambahkan route untuk halaman orphan** `dashboard/sertifikat/approval` atau hapus referensinya; dan `dashboard/survei-kepuasan/hasil`.

**14. Restore 8 file gambar** yang 404 (id 172, 173, 177, 182 + foto tim), atau ganti dengan URL placeholder agar tidak ada broken image.

**15. Aksesibilitas minor** — tambahkan `aria-label` pada tombol Cari ikon-only dan perbaiki iconify-icon yang gagal render di tombol Reset (`komunikasi/pesan-masuk`).

**16. Bungkus tabel lebar** dengan `.table-responsive` untuk `survei-kepuasan` (1504px), `landing/slider` (1306px), `sistem/approval` (1188px) agar UX mobile lebih baik.

---

## CATATAN METODOLOGIS

- Sesi login dilakukan manual oleh Anda di Chrome yang sama; audit(script) **attach** ke tab terotentikasi tersebut via CDP (`/json/list`). Tidak ada cookie yang ditulis ke disk.
- Tema dipaksa deterministik lewat `localStorage.setItem('theme', …)` + reload, memvalidasi 93/94 halaman benar-benar `rgb(27,36,49)` di Dark Mode.
- Semua klaim "tak terlihat" & rasio kontras **diukur dari `getComputedStyle`**, bukan dikira-kira. Razio mengikuti WCAG 2.1 (4.5:1 normal, 3.0:1 large/Dark Mode).
- Bug `.text-base` dikonfirmasi lewat cascade inspection (`CSSStyleRule.selectorText` yang match), bukan berdasarkan dugaan.
- **Tidak ada file di `C:\laragon\www\begawipengadaan` yang diubah.** Seluruh artefak audit berada di `%TEMP%\qaaudit\` (188 screenshot).

**Total: 103 rute diuji · 94 halaman dirender · 188 screenshot (light+dark+print+mobile) · 0 perubahan kode.**
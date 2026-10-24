# BegawiPengadaan — UI Boundary Contract

## Tujuan

Dokumen ini menetapkan batas antarmuka pengguna BegawiPengadaan: pemilihan template, root controller, root view, layout, asset, pengguna, tujuan akses, dan aturan perpindahan antar-area.

Dokumen ini melengkapi `README.md`, `dokumen/directory-tree.md`, dan `dokumen/route-contract.md`. Dokumen ini tidak menetapkan route baru, permission baru, atau domain baru; seluruh path dan boundary di bawah mengikuti ketiga dokumen tersebut.

---

## Prinsip Boundary

- Satu halaman produksi hanya memiliki satu boundary UI utama: Landing/Public, Auth, Workspace, Dashboard Back-office, atau Error.
- FiStudy hanya digunakan pada Landing/Public Area dan Workspace.
- WowDash hanya digunakan pada Auth terpadu dan Dashboard Back-office.
- Semua pengguna memakai Auth WowDash ketika masuk, mendaftar, memverifikasi akun, atau memulihkan password.
- Menggunakan Auth WowDash tidak memberikan akses Dashboard Back-office secara otomatis.
- Akses setelah login ditentukan oleh account status, role, permission, ownership, assignment, dan workflow state.
- Landing/Public, Workspace, dan Dashboard tidak boleh berbagi layout utama secara silang.
- Asset FiStudy dan WowDash tidak dicampur dalam satu halaman produksi.
- Source HTML reference tidak memiliki route produksi dan tidak boleh dirender langsung.
- Error view bersifat minimal/custom, bebas dari ketergantungan layout Auth/Workspace/Dashboard.

---

## Peta Boundary

| Boundary | Template | Pengguna | Controller root | View root | Layout utama | Asset root |
|---|---|---|---|---|---|---|
| Landing dan Public Area | FiStudy | Pengunjung publik dan Public User | `app/Controllers/Landing/` | `app/Views/landing/` | `landing/layouts/public.php` | `public/assets/main/` |
| Auth terpadu | WowDash | Semua pengguna yang masuk, mendaftar, memverifikasi akun, atau memulihkan akses | `app/Controllers/Dashboard/Auth/` | `app/Views/dashboard/auth/` | `dashboard/auth/layouts/auth.php` | `public/assets/dashboard/` |
| Workspace | FiStudy | Participant, Trainer, Evaluator, Operator | `app/Controllers/Workspace/` dan controller domain berizin | `app/Views/main/workspace/` | `main/layouts/workspace.php` | `public/assets/main/` |
| Dashboard Back-office | WowDash | Admin/Super Admin atau role dengan dashboard permission | `app/Controllers/Dashboard/` di luar `Auth` | `app/Views/dashboard/` di luar `auth/` | `dashboard/layouts/dashboard.php` | `public/assets/dashboard/` |
| Error | Minimal/custom | Semua pengguna | HTTP error/exception handler | `app/Views/error/` | Minimal/custom | Asset minimum bila diperlukan |
| Reference | Source FiStudy/WowDash | Tidak ada pengguna produksi | Tidak ada controller produksi | `app/Views/reference/` | Tidak ada | Source template |

---

## Landing dan Public Area

### Fungsi

Landing dan Public Area menyediakan presentasi publik dan read-only untuk informasi BegawiPengadaan yang telah memiliki status publikasi/visibility sesuai kebijakan domain.

Area ini mencakup:

- Landing Page.
- Katalog Arena Belajar.
- Bunga Rampai.
- Repository Regulasi PBJ.
- Berita, agenda, dan event publik.
- Statistik publik teragregasi.
- Halaman verifikasi sertifikat.
- Halaman umum: Tentang, Kontak, Privasi, Syarat dan Ketentuan, serta Aksesibilitas.

### Kontrak teknis

```text
Controller root
app/Controllers/Landing/

View root
app/Views/landing/

Layout utama
app/Views/landing/layouts/public.php

Layout alternatif minimal
app/Views/landing/layouts/minimal.php

Komponen bersama
app/Views/landing/components/

Asset
public/assets/main/
```

### Aturan

- Hanya data berstatus `published` dan ber-visibility public yang boleh dirender.
- Data internal/restricted tidak boleh disisipkan dalam HTML, JSON, atribut data, pagination metadata, filter, atau response error.
- Halaman publik yang dapat dicache tidak boleh memasukkan data personal pengguna.
- Tombol Masuk dan Daftar selalu mengarah ke Auth terpadu:

```text
/dashboard/masuk
/dashboard/daftar
```

- Jika pengguna terautentikasi membuka halaman publik, area tetap memakai layout Landing/FiStudy kecuali route secara eksplisit mengarah ke Workspace atau Dashboard.
- Form kontak merupakan satu-satunya form publik yang dapat mengubah state pada boundary ini; form tersebut tetap memakai validasi, rate limit, dan CSRF.
- Route publik tidak boleh merender view dari `main/workspace/`, `dashboard/`, atau `reference/`.

---

## Auth Terpadu

### Fungsi

Auth terpadu menyediakan satu pengalaman autentikasi bagi seluruh tipe pengguna, tanpa membedakan login Participant, Trainer, Evaluator, Operator, Admin, maupun Super Admin.

Area ini mencakup:

- Login.
- Logout.
- Registrasi multi-step.
- Pemilihan tipe profil.
- Pengisian wilayah/alamat.
- Pengisian profil ASN, Penyedia, Akademisi, atau Masyarakat Umum.
- Tinjau dan submit registrasi.
- Verifikasi email.
- Status akun pending/revision/rejected.
- Lupa password dan reset password.

### Kontrak teknis

```text
Controller root
app/Controllers/Dashboard/Auth/

View root
app/Views/dashboard/auth/

Layout utama
app/Views/dashboard/auth/layouts/auth.php

Komponen bersama
app/Views/dashboard/components/

Asset
public/assets/dashboard/
```

### View auth

```text
dashboard/auth/login.php
dashboard/auth/register_account.php
dashboard/auth/register_region.php
dashboard/auth/register_asn.php
dashboard/auth/register_provider.php
dashboard/auth/register_academic.php
dashboard/auth/register_public.php
dashboard/auth/register_review.php
dashboard/auth/register_success.php
dashboard/auth/verify_email.php
dashboard/auth/verify_email_success.php
dashboard/auth/account_pending.php
dashboard/auth/account_revision_required.php
dashboard/auth/account_rejected.php
dashboard/auth/forgot_password.php
dashboard/auth/reset_password.php
dashboard/auth/reset_password_success.php
```

### Aturan

- Semua auth route memakai prefix `/dashboard` sesuai route contract.
- Prefix `/dashboard` pada Auth adalah kontrak lokasi UI/controller WowDash, bukan penanda bahwa hanya Admin/Super Admin yang dapat menggunakannya.
- Halaman auth hanya memakai asset dari `public/assets/dashboard/`.
- Halaman auth tidak memakai header/footer Landing dan tidak menggunakan sidebar Dashboard Back-office.
- Form registrasi wajib mempertahankan state langkah sebelumnya secara aman melalui server-side session atau mekanisme setara yang terlindungi.
- Parameter tipe profil hanya menerima nilai yang diizinkan:

```text
asn
provider
academic
public
```

- Login dan reset password harus memberi pesan generik untuk mencegah enumeration account/email.
- Token verifikasi dan reset password tidak boleh dimasukkan ke log, flash message, atau audit snapshot dalam bentuk plain token.
- Logout hanya menggunakan `POST` dan selalu melakukan invalidasi session sesuai policy.

---

## Redirect Setelah Auth

Setelah autentikasi berhasil, sistem menentukan tujuan berdasarkan account status, role, permission, assignment/ownership, dan route awal yang diminta.

### Aturan status akun

| Account status | Tujuan awal |
|---|---|
| `draft` | Lanjutkan registrasi pada langkah yang relevan |
| `submitted` | Halaman status akun atau menunggu proses berikutnya |
| `pending_verification` | Halaman status pending/verifikasi |
| `revision_required` | Halaman status revisi dan alur perbaikan profil/registrasi |
| `active` | Workspace atau Dashboard sesuai akses efektif |
| `rejected` | Halaman status penolakan |
| `inactive` | Halaman status akun; tidak masuk Workspace/Dashboard |
| `suspended` | Halaman status akun; tidak masuk Workspace/Dashboard |
| `archived` | Halaman status akun; tidak masuk Workspace/Dashboard |

### Aturan role dan permission

```text
User aktif dengan dashboard permission yang sah
→ Dashboard Back-office WowDash

User aktif tanpa dashboard permission, tetapi memiliki role Workspace
→ Workspace FiStudy

User aktif dengan lebih dari satu konteks yang valid
→ Route awal yang sah bila ada; jika tidak, gunakan default yang
  ditetapkan oleh WorkspaceNavigationService.
```

Dashboard permission lebih spesifik daripada sekadar role `admin` atau `super_administrator`. Sistem tetap harus memeriksa permission granular pada setiap route Back-office.

---

## Workspace

### Fungsi

Workspace adalah area pengguna terautentikasi non-back-office untuk kegiatan sesuai role, ownership, enrollment, assignment, dan workflow domain.

Role utama Workspace:

```text
participant
trainer
evaluator
operator
```

Area Workspace mencakup:

- Home Workspace.
- Profile dan perubahan profil.
- Pengajuan role tambahan.
- Bookmark dan notifikasi.
- Aktivitas pembelajaran Participant.
- Pengelolaan kelas/materi Trainer.
- Penilaian Evaluator.
- Verifikasi dan operasi terdelegasi Operator.

### Kontrak teknis

```text
Controller root
app/Controllers/Workspace/

View root
app/Views/main/workspace/

Layout utama
app/Views/main/layouts/workspace.php

Layout alternatif minimal
app/Views/main/layouts/minimal.php

Komponen bersama
app/Views/main/components/

Partial bersama
app/Views/main/partials/

Asset
public/assets/main/
```

### Struktur view per role

```text
main/workspace/home/
main/workspace/participant/
main/workspace/trainer/
main/workspace/evaluator/
main/workspace/operator/
main/workspace/profile/
main/workspace/bookmarks/
main/workspace/notifications/
```

### Aturan

- Workspace memerlukan autentikasi, account status `active`, dan `workspaceAccess`.
- Setiap resource Workspace wajib diperiksa terhadap ownership, enrollment, class assignment, delegated scope, dan workflow state.
- Role `participant` tidak otomatis memberi akses terhadap materi; materi hanya dapat diakses dengan enrollment yang sah dan izin class/lesson yang sesuai.
- Role `trainer` tidak dapat mengelola kelas yang tidak ditugaskan kepadanya.
- Role `evaluator` tidak dapat mengakses submission di luar assignment penilaiannya.
- Role `operator` hanya dapat melakukan verifikasi, enrollment, absensi, atau approval dalam delegated scope serta permission yang dimiliki.
- Workspace memakai navigasi/sidebar FiStudy sendiri dan tidak memakai sidebar WowDash.
- Workspace tidak merender view dari `landing/`, `dashboard/`, atau `reference/` sebagai halaman utama.
- Controller domain dapat dipakai untuk aksi Learning/Knowledge/Regulation/Communication/Certificate bila route contract menetapkan filter dan policy Workspace yang benar.

### Dashboard di Workspace

Nama file `dashboard.php` di bawah direktori role Workspace berarti ringkasan aktivitas Workspace bagi role tersebut, bukan Dashboard Back-office.

```text
main/workspace/participant/dashboard.php
→ Participant Workspace Dashboard

main/workspace/trainer/dashboard.php
→ Trainer Workspace Dashboard

main/workspace/evaluator/dashboard.php
→ Evaluator Workspace Dashboard

main/workspace/operator/dashboard.php
→ Operator Workspace Dashboard
```

---

## Dashboard Back-office

### Fungsi

Dashboard Back-office adalah area administrasi internal untuk role dengan dashboard permission yang sah. Area ini menggunakan WowDash dan digunakan terutama oleh Admin serta Super Admin.

Area ini mencakup:

- Dashboard Back-office Home dan KPI internal.
- Manajemen user, verifikasi profil, dan review role request.
- Role, permission, serta matrix RBAC.
- Approval dan audit trail.
- Organisasi, setting aplikasi, notifikasi, storage, dan keamanan.
- Administrasi Arena Belajar.
- Administrasi Bunga Rampai.
- Administrasi Regulasi PBJ.
- Administrasi berita, event, registrasi, dan absensi.
- Administrasi sertifikat dan template sertifikat.
- Government Intelligence, data kompetensi, gap kompetensi, dan export internal.
- Laporan serta statistik internal.

### Kontrak teknis

```text
Controller root
app/Controllers/Dashboard/ di luar namespace Auth

View root
app/Views/dashboard/ di luar auth/

Layout utama
app/Views/dashboard/layouts/dashboard.php

Layout cetak
app/Views/dashboard/layouts/print.php

Layout minimal
app/Views/dashboard/layouts/minimal.php

Komponen bersama
app/Views/dashboard/components/

Asset
public/assets/dashboard/
```

### Struktur view Back-office

```text
dashboard/home/
dashboard/users/
dashboard/rbac/
dashboard/approvals/
dashboard/audits/
dashboard/organizations/
dashboard/learning/
dashboard/knowledge/
dashboard/regulations/
dashboard/communication/
dashboard/certificates/
dashboard/intelligence/
dashboard/reports/
dashboard/settings/
```

### Aturan

- Dashboard Back-office memerlukan `auth`, `verifiedEmail`, `accountStatus:active`, `dashboardAccess`, dan permission granular sesuai tindakan.
- Role `admin` atau `super_administrator` tidak menggantikan pemeriksaan permission granular.
- Dashboard Back-office tidak boleh merender view Workspace sebagai halaman Back-office.
- Dashboard Back-office tidak boleh memakai asset dari `public/assets/main/`.
- Aksi CRUD, approval, pengaturan, RBAC, export, dan operasi data restricted wajib memiliki audit context.
- Pengguna dengan role Workspace dapat masuk Dashboard Back-office hanya bila memiliki dashboard permission yang sah; akses tersebut harus eksplisit dan dapat diaudit.

### Dashboard Back-office Home

```text
app/Views/dashboard/home/index.php
```

File ini adalah halaman ringkasan internal Back-office. Berbeda dengan file `dashboard.php` di dalam direktori role Workspace.

---

## Error Boundary

### Fungsi

Error Boundary menampilkan halaman error HTTP dan exception dengan dependensi minimal agar tetap aman ketika layout utama, session, atau dependency domain bermasalah.

### Kontrak teknis

```text
View root
app/Views/error/

View minimum
400.php
403.php
404.php
419.php
429.php
500.php
exception.php
maintenance.php
```

### Aturan

- Error view tidak menggunakan layout Auth, Workspace, atau Dashboard sebagai dependency wajib.
- Error response tidak boleh memaparkan stack trace, secret, token, query database, path private, atau data restricted di lingkungan produksi.
- Resource restricted dapat merespons 404 alih-alih 403 jika mengungkap keberadaan resource menimbulkan risiko privasi/keamanan.
- Error 419 digunakan untuk CSRF/session invalid pada alur browser state-changing.
- Error 429 digunakan untuk rate limit terlampaui.
- Maintenance memakai status HTTP 503.

---

## Reference Boundary

### Fungsi

Reference Boundary menyimpan source template FiStudy dan WowDash sebagai katalog desain, referensi markup, serta sumber asset yang dapat diadaptasi.

### Kontrak teknis

```text
app/Views/reference/fistudy/
→ Source FiStudy untuk Landing/Public/Workspace

app/Views/reference/wowdash/
→ Source WowDash untuk Auth/Dashboard
```

### Aturan

- Tidak ada route produksi menuju `app/Views/reference/`.
- Controller produksi dilarang memanggil `view('reference/...')`.
- Source HTML dipecah menjadi layout, component, partial, dan domain view pada root view produksi yang sesuai.
- Asset yang dibutuhkan dipindahkan/disesuaikan ke `public/assets/main/` atau `public/assets/dashboard/`; source asset tidak dipakai langsung dari Reference Boundary.
- File login/sign-up FiStudy hanya reference dan tidak menjadi Auth produksi.
- File sign-in/sign-up/forgot-password WowDash hanya reference dan harus diadaptasi ke view Auth produksi.
- `sendemail.php` serta PHPMailer bawaan source template tidak digunakan untuk email produksi.

---

## Asset Boundary

### FiStudy asset

```text
public/assets/main/
├── css/
├── fonts/
├── icons/
├── images/
├── js/
├── vendor/
└── uploads-public/
```

FiStudy asset hanya digunakan oleh:

```text
app/Views/landing/
app/Views/main/
app/Views/main/workspace/
```

### WowDash asset

```text
public/assets/dashboard/
├── css/
├── fonts/
├── icons/
├── images/
├── js/
└── vendor/
```

WowDash asset hanya digunakan oleh:

```text
app/Views/dashboard/auth/
app/Views/dashboard/ di luar auth/
```

### Aturan asset

- Jangan memuat CSS/JS FiStudy pada Auth/Dashboard WowDash.
- Jangan memuat CSS/JS WowDash pada Landing/Workspace FiStudy.
- Shared browser library hanya boleh diduplikasi atau diekstrak setelah dicatat pada implementasi terkait; tidak boleh menciptakan asset root ketiga.
- `public/assets/main/uploads-public/` hanya menyimpan konten yang secara eksplisit public.
- File internal/restricted tidak boleh disimpan di bawah `public/assets/`.

---

## Storage Boundary

```text
public/assets/main/uploads-public/
→ Konten eksplisit public.

writable/uploads/
→ File internal/restricted dan runtime private.

writable/exports/
→ Export sementara yang tetap memerlukan authorization saat diunduh.
```

### Aturan

- Preview/download file private dilakukan melalui `Shared\FileController` dan `FileAccessService`.
- File access selalu memeriksa visibility, permission, ownership, assignment, dan workflow state.
- Export dilakukan melalui `Shared\ExportController` dan `ExportService`; file export tidak boleh dibuka hanya berdasarkan tebakan path.
- File upload melalui `Shared\FileController` harus divalidasi terhadap MIME, ekstensi, ukuran, keamanan konten, dan policy domain.
- File sementara serta export harus mengikuti retention policy dan pembersihan terjadwal.

---

## Boundary yang Dilarang

Path berikut tidak boleh dipakai sebagai area produksi baru:

```text
app/Controllers/Auth/
app/Controllers/Admin/
app/Controllers/Admin/AdminAuthController.php
app/Views/auth/
app/Views/workspace/
app/Views/main/layouts/auth.php
public/assets/workspace/
```

Pengganti yang wajib digunakan:

```text
app/Controllers/Dashboard/Auth/
app/Controllers/Dashboard/
app/Views/dashboard/auth/
app/Views/dashboard/auth/layouts/auth.php
app/Views/main/workspace/
public/assets/main/
public/assets/dashboard/
```

---

## Checklist Implementasi UI

Sebelum sebuah halaman dinyatakan `UI Implemented`, pastikan:

- [ ] Controller berada pada boundary yang benar.
- [ ] Route terdaftar pada `dokumen/route-contract.md`.
- [ ] View berada pada root yang benar.
- [ ] Layout sesuai boundary: `public.php`, `workspace.php`, `auth.php`, atau `dashboard.php`.
- [ ] Asset hanya berasal dari root asset template yang benar.
- [ ] Tidak ada pemanggilan `view('reference/...')`.
- [ ] Tidak ada asset internal/restricted yang dimuat dari public path.
- [ ] Navigation hanya menampilkan menu yang relevan terhadap role/permission.
- [ ] Server-side filter/policy tetap berlaku meski menu/tombol disembunyikan.
- [ ] Halaman error dan empty state tersedia sesuai kebutuhan.
- [ ] View tidak mengekspos data restricted di HTML, JavaScript, data attribute, atau response.

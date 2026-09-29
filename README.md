# BegawiPBJ

## Platform Ekosistem Digital Pengadaan Barang/Jasa Pemerintah Provinsi Lampung

BegawiPBJ adalah platform digital terpadu untuk mendukung ekosistem Pengadaan Barang/Jasa Pemerintah (PBJ) di lingkungan Pemerintah Provinsi Lampung.

Platform ini menyatukan pembelajaran digital, pusat pengetahuan PBJ, repository regulasi, berita dan event, sertifikat digital, data kompetensi SDM PBJ, statistik publik, serta Government Intelligence PBJ dalam satu aplikasi yang aman, terstruktur, dapat diaudit, dan berbasis peran.

BegawiPBJ dibangun sebagai aplikasi web monolitik full-stack menggunakan CodeIgniter 4 AppStarter. Antarmuka produksi diadaptasi secara terstruktur dari dua source template HTML lengkap:

- **FiStudy** digunakan untuk Landing Page, Public Area, dan Workspace pengguna.
- **WowDash** digunakan untuk Auth Layout terpadu seluruh pengguna dan Dashboard Back-office.

README ini adalah acuan utama proyek. Dokumen ini menetapkan scope, arsitektur, boundary antarmuka, kontrak autentikasi, model akses, domain, privasi, keamanan, penyimpanan file, serta arah implementasi. `directory-tree.md` menerjemahkan kontrak ini menjadi lokasi direktori dan file target.

---

## Prinsip Proyek

BegawiPBJ mengikuti prinsip berikut:

- Dibangun sebagai satu aplikasi monolitik CodeIgniter 4 dengan domain yang terstruktur.
- Menggunakan satu mekanisme autentikasi produksi bagi seluruh tipe pengguna.
- Memisahkan Landing dan Public Area, Workspace, serta Dashboard Back-office secara tegas.
- Melakukan authorization pada server-side untuk setiap resource dan aksi sensitif.
- Memisahkan data public, internal, dan restricted secara eksplisit.
- Mencatat tindakan penting melalui audit trail.
- Menyimpan file internal/restricted di storage privat, bukan URL publik langsung.
- Mengadaptasi template menjadi view produksi tanpa merender file reference HTML secara langsung.
- Menetapkan status implementasi secara eksplisit; keberadaan file/folder tidak berarti fitur selesai.
- Mengubah route, namespace, path, permission, workflow, atau struktur data hanya melalui kontrak dokumentasi proyek yang diperbarui secara konsisten.

---

## Tujuan

BegawiPBJ dirancang untuk:

- Menjadi pusat pembelajaran PBJ berbasis digital.
- Meningkatkan kompetensi SDM PBJ melalui program, course, class, materi, assessment, dan sertifikat terukur.
- Menyediakan Bunga Rampai sebagai pusat artikel, FAQ, studi kasus, template, media visual, video, dan praktik baik PBJ.
- Mengelola regulasi PBJ secara terstruktur, berversi, mudah dicari, dapat dipreview atau diunduh, serta dapat ditelusuri status dan hubungan hukumnya.
- Menyediakan berita, agenda, webinar, sosialisasi, workshop, pelatihan, serta kegiatan PBJ lainnya.
- Menerbitkan sertifikat digital yang dapat diverifikasi publik melalui token dan QR code.
- Menyediakan statistik pembelajaran, kegiatan, kompetensi, dan kondisi SDM PBJ secara aman.
- Mendukung pengambilan keputusan berbasis data melalui Government Intelligence PBJ.
- Menjalankan workflow persetujuan, audit trail, file storage terproteksi, serta kontrol akses granular.

---

## Ruang Lingkup

```text
+ Arena Belajar
+ Bunga Rampai
+ Regulasi PBJ
+ Berita, Agenda, dan Event
+ Sertifikat Digital
+ Government Intelligence PBJ
+ Statistik Publik PBJ
+ Identity, RBAC, Approval, Audit, Notification, Bookmark, dan File Storage
```

Government Intelligence PBJ berfokus pada data organisasi Pemerintah Provinsi Lampung, termasuk OPD/unit, SDM JF PPBJ, pemilik sertifikat kompetensi PBJ, distribusi kompetensi, kebutuhan SDM, serta gap kompetensi.

Statistik publik menggunakan data agregat. Statistik tersebut tidak boleh menampilkan NIP, NIK, nama individu ASN, email, nomor telepon, dokumen bukti, identifier personal, atau informasi restricted lainnya.

---

## Arsitektur Antarmuka

BegawiPBJ memisahkan antarmuka menurut tujuan pengguna, jenis akses, layout, controller, view, asset, serta sensitivitas data. Semua autentikasi produksi dipusatkan di area Dashboard/Auth berbasis WowDash.

| Area | Template | Pengguna utama | Controller root | View root | Asset root | Fungsi |
|---|---|---|---|---|---|---|
| Landing dan Public Area | FiStudy | Pengunjung publik dan Public User | `app/Controllers/Landing/` | `app/Views/landing/` | `public/assets/main/` | Beranda, katalog publik, Bunga Rampai, regulasi, berita, event, statistik agregat, dan verifikasi sertifikat |
| Auth terpadu | WowDash | Semua pengguna yang masuk, mendaftar, memverifikasi akun, atau memulihkan akses | `app/Controllers/Dashboard/Auth/` | `app/Views/dashboard/auth/` | `public/assets/dashboard/` | Login, registrasi multi-step, verifikasi email, status akun, lupa/reset password, dan logout |
| Workspace | FiStudy | Participant, Trainer, Evaluator, Operator | `app/Controllers/Workspace/` dan controller domain berizin | `app/Views/main/workspace/` | `public/assets/main/` | Pembelajaran, tugas, penilaian, bookmark, notifikasi, profil, verifikasi operasional, dan aktivitas berbasis role |
| Dashboard Back-office | WowDash | Admin dan Super Admin; role lain hanya bila memiliki permission dashboard yang sah | `app/Controllers/Dashboard/` di luar namespace `Auth` | `app/Views/dashboard/` di luar `auth/` | `public/assets/dashboard/` | Administrasi, RBAC, approval, audit, konfigurasi, laporan, statistik internal, dan pengelolaan data |
| Error | Minimal/custom | Semua pengguna | Exception dan HTTP error handler | `app/Views/error/` | Asset minimum bila diperlukan | 400, 403, 404, 419, 429, 500, dan maintenance |
| Reference | Source FiStudy/WowDash | Tidak ada pengguna produksi | Tidak ada route produksi | `app/Views/reference/` | Source reference | Katalog source template dan bahan adaptasi |

### Kontrak template, view, dan asset

```text
FiStudy
├── Landing Page dan Public Area
│   └── app/Views/landing/
├── Workspace Participant / Trainer / Evaluator / Operator
│   └── app/Views/main/workspace/
└── Asset produksi bersama
    └── public/assets/main/

WowDash
├── Auth terpadu untuk seluruh pengguna
│   └── app/Views/dashboard/auth/
├── Dashboard Back-office untuk Admin / Super Admin
│   └── app/Views/dashboard/
└── Asset produksi bersama
    └── public/assets/dashboard/

Non-production source template
└── app/Views/reference/
```

Aturan wajib:

- Landing dan Public Area serta Workspace memakai asset dari `public/assets/main/`.
- Auth terpadu dan Dashboard Back-office memakai asset dari `public/assets/dashboard/`.
- Landing berada pada `app/Views/landing/`; tidak digabung menjadi `app/Views/main/landing/`.
- Workspace berada pada `app/Views/main/workspace/`; tidak dibuat sebagai `app/Views/workspace/`.
- Auth hanya berada pada `app/Views/dashboard/auth/`; tidak dibuat sebagai `app/Views/auth/`, `app/Views/main/auth/`, atau `app/Views/main/layouts/auth.php`.
- Layout auth produksi hanya `app/Views/dashboard/layouts/auth.php`.
- Source di `app/Views/reference/` bersifat non-production dan dilarang dirender oleh controller/route produksi.
- Asset FiStudy tidak dipakai oleh Auth/Dashboard WowDash, dan asset WowDash tidak dipakai oleh Landing/Workspace FiStudy, kecuali perubahan tersebut didokumentasikan secara eksplisit.

### Alur akses utama

```text
Pengunjung Publik
↓
Landing / Public Area FiStudy
↓
/dashboard/masuk atau /dashboard/daftar
↓
Auth UI terpadu WowDash
↓
Authentication + account status + role + permission + assignment validation
↓
Workspace FiStudy atau Dashboard WowDash sesuai akses efektif
```

```text
Public User / Participant / Trainer / Evaluator / Operator
↓
Auth WowDash
↓
Workspace FiStudy
↓
app/Views/main/workspace/
public/assets/main/
```

```text
Admin / Super Admin
↓
Auth WowDash
↓
Dashboard Back-office WowDash
↓
app/Views/dashboard/
public/assets/dashboard/
```

Prefix `/dashboard` pada route auth menunjukkan lokasi kontrak controller/view WowDash. Prefix tersebut tidak membatasi halaman login atau registrasi hanya untuk Admin/Super Admin.

---

## Kontrak Controller

Controller dipisahkan berdasarkan boundary antarmuka dan tanggung jawab domain. Semua route tetap wajib menggunakan filter, authorization, ownership, assignment, dan policy server-side yang sesuai.

```text
Landing controller
→ Presentasi read-only data yang berstatus published/public.
→ Tidak mengekspos data internal/restricted.

Workspace controller
→ Aktivitas pengguna terautentikasi berdasarkan role, ownership,
  enrollment, assignment, profil, bookmark, dan notifikasi.

Dashboard controller
→ Administrasi, CRUD back-office, verifikasi, moderasi, review,
  approval, konfigurasi, audit, laporan, serta statistik internal.

Domain controller
→ Aksi domain spesifik yang digunakan oleh konteks Workspace atau
  Dashboard sesuai route, filter, permission, ownership, dan workflow.
→ Tidak memberi akses lintas boundary antarmuka secara otomatis.
```

Root controller yang dikunci:

```text
app/Controllers/Landing/        # Public Area FiStudy
app/Controllers/Dashboard/Auth/ # Seluruh autentikasi produksi WowDash
app/Controllers/Dashboard/      # Dashboard Back-office WowDash
app/Controllers/Workspace/      # Workspace FiStudy

app/Controllers/Learning/
app/Controllers/Knowledge/
app/Controllers/Regulation/
app/Controllers/Communication/
app/Controllers/Certificate/
app/Controllers/Intelligence/
app/Controllers/Shared/
```

Controller domain tidak boleh digunakan untuk melewati filter `Auth`, `AccountStatus`, `Permission`, `WorkspaceAccess`, atau `DashboardAccess` yang relevan.

---

## Auth Terpadu

Seluruh autentikasi produksi berada pada namespace `App\Controllers\Dashboard\Auth` dan menggunakan view `app/Views/dashboard/auth/`.

```text
app/Controllers/Dashboard/Auth/
├── LoginController.php
├── LogoutController.php
├── RegisterController.php
├── RegisterProfileController.php
├── EmailVerificationController.php
├── PasswordController.php
└── AccountStatusController.php
```

Auth mengelola autentikasi, registrasi, verifikasi email, pemulihan/reset password, logout, dan tampilan status akun. Auth tidak memberikan Dashboard privilege secara otomatis.

### Route auth

| Kebutuhan | Route | Controller |
|---|---|---|
| Masuk | `GET|POST /dashboard/masuk` | `Dashboard\Auth\LoginController` |
| Keluar | `POST /dashboard/keluar` | `Dashboard\Auth\LogoutController` |
| Mulai daftar | `GET|POST /dashboard/daftar` | `Dashboard\Auth\RegisterController` |
| Wilayah/alamat | `GET|POST /dashboard/daftar/wilayah` | `Dashboard\Auth\RegisterController` |
| Profil spesifik | `GET|POST /dashboard/daftar/profil/{tipe}` | `Dashboard\Auth\RegisterProfileController` |
| Tinjau registrasi | `GET|POST /dashboard/daftar/tinjau` | `Dashboard\Auth\RegisterController` |
| Daftar berhasil | `GET /dashboard/daftar/berhasil` | `Dashboard\Auth\RegisterController` |
| Verifikasi email | `GET|POST /dashboard/verifikasi/{token}` | `Dashboard\Auth\EmailVerificationController` |
| Lupa kata sandi | `GET|POST /dashboard/lupa-kata-sandi` | `Dashboard\Auth\PasswordController` |
| Reset kata sandi | `GET|POST /dashboard/reset-kata-sandi/{token}` | `Dashboard\Auth\PasswordController` |
| Status akun | `GET /dashboard/status-akun` | `Dashboard\Auth\AccountStatusController` |

Redirect pascalogin ditentukan oleh account status, role, permission, assignment/ownership, serta route awal yang diminta. Redirect diorkestrasi oleh `AuthenticationService` dan `WorkspaceNavigationService` tanpa membuat jalur login kedua.

---

## Identity, RBAC, dan Lifecycle Akun

Empat konsep berikut harus dipisahkan secara tegas:

| Konsep | Fungsi | Contoh |
|---|---|---|
| Tipe profil | Mengelompokkan data profil pengguna saat registrasi | ASN, Penyedia Barang/Jasa, Akademisi, Masyarakat Umum |
| Account status | Menentukan lifecycle dan kelayakan akses akun | `draft`, `submitted`, `pending_verification`, `revision_required`, `active`, `rejected`, `inactive`, `suspended`, `archived` |
| Role | Menjelaskan tanggung jawab bisnis pengguna | `public_user`, `participant`, `trainer`, `evaluator`, `operator`, `admin`, `super_admin` |
| Permission | Memberi hak granular terhadap aksi/resource | `learning.study`, `learning.assignment.grade`, `certificate.issue` |

Tipe profil bukan role. Memilih profil ASN, Penyedia Barang/Jasa, Akademisi, atau Masyarakat Umum tidak memberi kewenangan administratif secara otomatis.

### Account status

Account status teknis harus digunakan konsisten pada database, model, service, filter, controller, API, audit, dan workflow.

| Enum teknis | Label UI | Makna |
|---|---|---|
| `draft` | Draf | Registrasi belum dikirim atau belum lengkap |
| `submitted` | Diajukan | Registrasi telah dikirim dan menunggu proses berikutnya |
| `pending_verification` | Menunggu Verifikasi | Menunggu verifikasi email, profil, atau pemeriksaan yang dibutuhkan |
| `revision_required` | Perlu Perbaikan | Pengguna harus melengkapi atau memperbaiki data |
| `active` | Aktif | Akun dapat mengakses layanan sesuai role dan permission |
| `rejected` | Ditolak | Pengajuan tidak diterima sesuai kebijakan |
| `inactive` | Tidak Aktif | Akun tidak aktif tanpa status penangguhan khusus |
| `suspended` | Ditangguhkan | Akses dibatasi atau dihentikan sementara berdasarkan tindakan/kebijakan |
| `archived` | Diarsipkan | Record dipertahankan sebagai arsip dan tidak digunakan aktif |

Akses efektif diperiksa melalui:

```text
Authentication
+ Account Status
+ Role
+ Permission
+ Ownership atau Class Assignment
+ Status workflow/domain
```

Menyembunyikan menu bukan mekanisme keamanan. Seluruh route, controller action, service sensitif, unduhan file privat, preview, export, dan operasi perubahan data wajib memeriksa authorization server-side.

### Role sistem

| Label UI | Role teknis | Area utama | Contoh akses |
|---|---|---|---|
| Public User | `public_user` | Landing dan Public Area | Konten publik, katalog, regulasi, agenda, statistik agregat, verifikasi sertifikat |
| Participant | `participant` | Workspace | Enrollment, pembelajaran, quiz, tugas, progres, bookmark, feedback, sertifikat pribadi |
| Trainer | `trainer` | Workspace | Kelas yang ditugaskan, materi, peserta, quiz, assignment, dan progres sesuai assignment |
| Evaluator | `evaluator` | Workspace | Penilaian manual, feedback, revisi, dan final grading sesuai assignment |
| Operator | `operator` | Workspace | Verifikasi data, dukungan administrasi, enrollment, absensi, dan approval terdelegasi |
| Admin | `admin` | Dashboard Back-office | Manajemen konten, pembelajaran, regulasi, event, sertifikat, approval, serta laporan sesuai permission |
| Super Admin | `super_admin` | Dashboard Back-office | Konfigurasi strategis, user, role, permission, audit, monitoring, serta scope berizin |

Satu akun dapat memiliki lebih dari satu role. Role tidak menggantikan permission, assignment, ownership, atau status workflow.

---

## Registrasi dan Approval

Registrasi mendukung berbagai kelompok pengguna tanpa menjadikan upload dokumen sebagai hambatan tahap awal. Sistem mengumpulkan data secukupnya dan menerapkan verifikasi tambahan secara proporsional terhadap risiko akses yang dimohonkan.

### Tipe profil registrasi

```text
1. ASN / Pegawai Pemerintah
2. Penyedia Barang/Jasa
3. Akademisi
4. Masyarakat Umum
```

### Alur registrasi multi-step

```text
1. Akun dasar dan pemilihan tipe profil
2. Wilayah/domisili dan alamat ringkas
3. Profil khusus berdasarkan tipe
4. Tinjau data, syarat penggunaan, dan kebijakan privasi
5. Submit registrasi
6. Verifikasi email
7. Verifikasi Operator/Admin bila diperlukan
8. Aktivasi akun atau status revisi/penolakan
```

### Akun dasar untuk semua tipe

| Field | Wajib | Ketentuan |
|---|---:|---|
| Nama lengkap | Ya | Sesuai identitas pengguna |
| Email | Ya | Unik dan valid; digunakan untuk login/verifikasi |
| Username | Ya | Unik; karakter mengikuti kebijakan sistem |
| Password | Ya | Minimal 12 karakter; huruf besar, huruf kecil, angka, dan simbol |
| Konfirmasi password | Ya | Harus sama dengan password |
| Nomor telepon | Ya | Nomor aktif dengan format valid |
| Tipe profil | Ya | ASN, Penyedia, Akademisi, atau Masyarakat Umum |
| Provinsi | Ya | Menggunakan master `wilayah` |
| Kabupaten/Kota | Ya | Konsisten dengan provinsi |
| Kecamatan | Ya | Konsisten dengan kabupaten/kota |
| Desa/Kelurahan | Opsional | Menggunakan master `wilayah` bila diisi |
| Alamat ringkas | Ya | Secukupnya untuk kebutuhan layanan |
| Persetujuan syarat penggunaan | Ya | Checkbox eksplisit |
| Persetujuan kebijakan privasi | Ya | Checkbox eksplisit |
| CAPTCHA/rate limiting | Ya | Proteksi spam dan automated registration |

Password tidak boleh disimpan dalam bentuk teks asli. Sistem hanya menyimpan password hash dengan mekanisme keamanan framework yang didukung.

### Profil ASN/Pegawai Pemerintah

ASN menggunakan NIP atau NIPPK sebagai identifier utama apabila tersedia dan sesuai kebijakan. ASN tidak wajib mengisi NIK dan tidak wajib mengunggah Surat Tugas pada registrasi awal.

Pilihan asal instansi:

```text
ASN Pemprov Lampung
ASN Luar Pemprov Lampung
```

Nilai teknis asal instansi:

```text
pemprov_lampung
kementerian_lembaga
pemerintah_daerah_lain
pemerintah_desa
bumn_bumd
lainnya
```

Profil ASN dapat mencakup status kepegawaian, OPD/unit kerja, jabatan, pangkat/golongan bila relevan, email kedinasan, serta peran pengadaan. Peran pengadaan bersifat multi-role dan memerlukan verifikasi/approval apabila memengaruhi hak akses sistem.

Perangkat Desa tanpa NIP/NIPPK tidak boleh dipaksa memakai tipe ASN. Mereka memakai tipe Masyarakat Umum dengan pekerjaan/status `Perangkat Desa`, kecuali kelak ditambahkan tipe profil khusus.

### Peran dalam pengadaan

Satu pengguna dapat memiliki lebih dari satu peran pengadaan:

```text
PA                         Pengguna Anggaran
KPA                        Kuasa Pengguna Anggaran
PPK                        Pejabat Pembuat Komitmen
PP                         Pejabat Pengadaan
POKJA_PEMILIHAN            Kelompok Kerja Pemilihan
PENGELOLA_PENGADAAN        Pengelola Pengadaan Barang/Jasa
PPTK                       Pejabat Pelaksana Teknis Kegiatan
PEJABAT_PENERIMA_HASIL     Pejabat/Panitia Penerima Hasil Pekerjaan
TIM_TEKNIS                 Tim Teknis
TIM_PENDUKUNG              Tim Pendukung
PENGAWAS_KONTRAK           Pengawas Kontrak
APIP                       Aparat Pengawasan Intern Pemerintah
LAINNYA                    Peran lainnya
```

Pada registrasi awal, sistem dapat mengumpulkan jawaban Ya/Tidak serta daftar peran. Periode penugasan, nomor SK, bukti penugasan, dan verifikasi lanjutan dapat dilengkapi setelah akun aktif melalui pengajuan perubahan profil atau workflow khusus.

### Profil Penyedia Barang/Jasa

Profil Penyedia dapat mencakup nama badan usaha, bentuk badan usaha, bidang usaha, lokasi/kontak usaha, jabatan pendaftar, penanggung jawab, NIB bila dibutuhkan, serta alamat usaha. NIK/NIB dan dokumen legalitas adalah data restricted sesuai konteks.

Tidak ada upload dokumen wajib pada registrasi awal, kecuali akses atau fitur tertentu memiliki kebutuhan verifikasi yang terdokumentasi.

### Profil Akademisi

Profil Akademisi dapat mencakup institusi pendidikan/riset, jenis institusi, jabatan/peran, bidang keahlian, email institusi, dan identifier profesi bila relevan.

CV, portofolio, atau bukti kompetensi tidak diwajibkan pada registrasi awal. Bukti tambahan dapat diminta ketika pengguna mengajukan role Trainer, Evaluator, atau akses lain yang berisiko.

### Profil Masyarakat Umum

Profil Masyarakat Umum termasuk warga, komunitas, pelaku pendukung, serta Perangkat Desa. Data dapat mencakup NIK bila diperlukan sesuai kebijakan, pekerjaan/status saat ini, organisasi, bidang minat PBJ, dan tujuan penggunaan.

Data yang tidak diperlukan untuk layanan tidak boleh dipaksa pada tahap registrasi.

### Role awal dan pengajuan akses

Setelah akun aktif, role default adalah:

```text
public_user
participant
```

Role `participant` tidak otomatis membuka materi kelas. Akses materi mengikuti enrollment yang sah pada class terkait.

Pengguna dapat mengajukan role tambahan:

```text
trainer
evaluator
```

Pengguna tidak dapat menetapkan sendiri role berikut:

```text
operator
admin
super_admin
```

Role tersebut hanya diberikan melalui penetapan internal Admin/Super Admin dan wajib dicatat dalam audit trail.

---

## Privasi dan Data Restricted

BegawiPBJ menerapkan prinsip data minimization, purpose limitation, least privilege, serta pemisahan data public, internal, dan restricted.

Data berikut diperlakukan sebagai restricted atau sensitif sesuai konteks:

```text
NIP/NIPPK
NIK
NIB
Nomor telepon
Email personal
Identifier SDM
Dokumen bukti
Token verifikasi/reset
Credential dan secret
Metadata keamanan
```

Ketentuan penggunaan data:

- Tidak ditampilkan penuh pada daftar pengguna atau Statistik Publik.
- Menggunakan masking ketika informasi lengkap tidak dibutuhkan.
- Tidak dimasukkan ke snapshot audit dalam bentuk lengkap.
- Tidak dikirim melalui notifikasi biasa secara berlebihan.
- Export memerlukan permission dan dicatat dalam audit trail.
- File/data restricted hanya dapat diakses melalui authorization server-side.
- Statistik publik menggunakan agregasi dan tidak boleh memungkinkan re-identifikasi individu.

Aturan klasifikasi, masking, export, retensi, dan audit redaction dirinci pada `docs/domain/privacy-and-data-classification.md`.

---

## Modul Utama

### Arena Belajar

Arena Belajar adalah Learning Management System (LMS) BegawiPBJ.

```text
Program
↓
Course
↓
Class
↓
Module
↓
Lesson
↓
Pre-test / Lesson Quiz / Assignment / Post-test
↓
Grading dan Completion
↓
Feedback
↓
Certificate Eligibility dan Certificate
```

Fitur inti:

- Program, course, class, Trainer, Evaluator, kuota, periode, dan jadwal.
- Enrollment dengan validasi kapasitas, periode, prerequisite, dan status peserta.
- Module dan lesson dengan materi teks, dokumen, video, tautan, serta file pembelajaran.
- Progress tracking berdasarkan penyelesaian lesson dan komponen wajib.
- Pre-test satu kali untuk baseline kemampuan bila diaktifkan.
- Quiz per lesson dengan auto-grading dan nilai attempt terbaik.
- Assignment essay dan/atau upload berkas jika diaktifkan pada class.
- Manual grading, feedback, status revisi, dan upload ulang assignment.
- Post-test dengan auto-grading serta best attempt.
- Nilai akhir, passing grade, kelulusan, feedback peserta, serta certificate eligibility.

Aturan evaluasi formal berada pada `docs/domain/assessment-and-certification-rules.md`.

| Komponen | Status | Mekanisme nilai | Kontribusi nilai akhir |
|---|---|---|---|
| Pre-test | Wajib satu kali bila aktif | Auto-grading 0–100 | Tidak dihitung |
| Quiz per lesson | Sesuai konfigurasi | Auto-grading; best attempt | Mengikuti `quiz_weight` class |
| Assignment | Opsional; wajib jika tersedia | Manual grading; revisi/upload ulang | Mengikuti `assignment_weight` class |
| Post-test | Wajib bila aktif | Auto-grading; best attempt | 60% |
| Feedback peserta | Wajib bila dikonfigurasi untuk sertifikat | Rating/feedback | Tidak bernilai |

Formula nilai akhir:

\[
final\_score =
(quiz\_score \times quiz\_weight / 100) +
(assignment\_score \times assignment\_weight / 100) +
(post\_test\_score \times 60 / 100)
\]

```text
post_test_weight = 60
quiz_weight + assignment_weight = 40
passing_grade minimum = 70
```

Jika assignment tidak tersedia:

```text
assignment_enabled = false
assignment_weight = 0
quiz_weight = 40
post_test_weight = 60
```

Data nilai, jawaban assessment, assignment, feedback internal, dan bukti peserta merupakan data internal/restricted. Data tersebut tidak boleh tersedia di Landing maupun Public Area.

### Bunga Rampai

Bunga Rampai adalah pusat pengetahuan dan publikasi multimedia PBJ.

Konten yang didukung:

```text
Artikel
FAQ
Studi kasus
Template dokumen
Panduan teknis
Best practice
Poster
Flyer
Infografik
Foto kegiatan
Gallery
Video upload
Video YouTube
Link sosial media
Tautan eksternal resmi
```

Fitur inti:

- Kategori bertingkat dan tag.
- Artikel, FAQ, studi kasus, template dokumen, serta materi kurasi.
- Media poster, flyer, infografik, gallery, video, YouTube, sosial media, dan external link.
- Search, filter, konten terkait, serta bookmark.
- Workflow `draft`, `submitted`, `in_review`, `approved`, `published`, `revision_required`, `rejected`, serta `archived`.
- Metadata dan file asset sesuai status publikasi/permission.
- Konten unggulan dan carousel terkelola.

Carousel tidak boleh menjadi satu-satunya cara menemukan konten. Bila autoplay digunakan, autoplay berhenti saat hover, focus, atau interaksi pengguna; mendukung keyboard dan kontrol sebelumnya/berikutnya; setiap gambar memiliki `alt_text`; serta menyediakan tautan **Lihat Semua**.

Video menggunakan thumbnail dan player dimuat setelah pengguna memilih play atau membuka detail. URL YouTube harus divalidasi terhadap domain resmi dan sistem tidak menyimpan HTML iframe bebas dari pengguna. Tautan eksternal memakai `target="_blank"` dan `rel="noopener noreferrer"`.

### Regulasi PBJ

Regulasi PBJ adalah repository regulasi dan dokumen rujukan pengadaan berbasis metadata, relasi hukum, versioning file, PDF preview, dan download terkontrol.

Fitur inti:

- Kategori regulasi, nomor, tahun, instansi penerbit, tanggal resmi/berlaku, ringkasan, serta sumber resmi.
- Status hukum `active`, `amended`, `revoked`, atau `expired`.
- Tahapan PBJ, kompetensi, kata kunci, bookmark, pencarian, dan filter.
- Versioning dokumen, changelog, serta penanda current version.
- PDF preview inline, buka di tab baru, serta download PDF terkontrol.
- Log download regulasi.
- Relasi hukum seperti `amends`, `revokes`, `replaces`, `implements`, serta `references`.

Semua regulasi dengan `workflow_status = published` dapat tampil pada repository publik sesuai visibility, termasuk regulasi yang status hukumnya telah diubah, dicabut, atau berakhir. Status workflow berbeda dengan status hukum.

```text
workflow_status:
draft
submitted
in_review
approved
published
revision_required
rejected
archived
```

Jika metadata regulasi tersedia tetapi file belum tersedia, metadata tetap dapat tampil dengan status **File belum tersedia**; preview dan download dinonaktifkan.

Aturan detail regulasi, versioning, relasi hukum, visibility, serta publication policy ditetapkan dalam `docs/domain/regulation-policy.md`.

### Berita, Agenda, dan Event

Domain ini mengelola berita, agenda, webinar, sosialisasi, workshop, pelatihan, dan kegiatan PBJ lainnya.

Fitur inti:

- Berita dengan kategori, judul, slug, cover, ringkasan, konten, penulis, workflow, waktu terbit, serta view count.
- Agenda publik dan kalender kegiatan.
- Event dengan mode `online`, `offline`, serta `hybrid`.
- Jadwal, lokasi atau meeting URL, periode registrasi, kuota, narasumber, serta ketentuan keikutsertaan.
- Registrasi event `registered`, `waitlisted`, `cancelled`, atau `rejected`.
- Pengendalian kuota serta promosi waitlist menggunakan transaction.
- Absensi `present`, `absent`, atau `excused`.
- Sertifikat event jika diaktifkan dan peserta memenuhi syarat.
- Notifikasi pendaftaran, waitlist, perubahan jadwal, pembatalan, absensi, serta sertifikat.

Status event:

```text
draft
coming_soon
open_registration
full
finished
cancelled
```

Daftar peserta, kehadiran, dan catatan internal event bukan data publik secara default.

### Sertifikat Digital

Sertifikat Digital menerbitkan, mengelola, memverifikasi, serta membatalkan sertifikat pembelajaran maupun event.

Fitur inti:

- Template sertifikat untuk `learning` dan `event`.
- Validasi eligibility sebelum penerbitan.
- Nomor sertifikat unik berbasis scope/tahun.
- Generate PDF sertifikat.
- QR code dan verification token unik.
- Halaman verifikasi publik dengan informasi minimum yang aman.
- Riwayat sertifikat peserta.
- Status `pending`, `issued`, `revoked`, serta `expired`.
- Expiry bila masa berlaku digunakan.
- Revocation tanpa menghapus record sertifikat.
- Audit trail untuk issue, regenerate, verify, expiry, revoke, serta replacement.

Satu sumber hanya dapat menghasilkan satu sertifikat resmi:

```text
UNIQUE(source_type, source_id)
```

Sumber sertifikat:

```text
learning_enrollment
event_registration
```

Syarat sertifikat learning:

```text
class.certificate_enabled
+ enrollment sah dan tidak withdrawn/cancelled
+ seluruh lesson wajib selesai
+ pre-test selesai bila aktif
+ quiz wajib selesai
+ assignment wajib graded bila aktif
+ post-test selesai bila aktif
+ final_score >= passing_grade
+ feedback peserta selesai bila diwajibkan
+ tidak ada administrative hold
+ belum ada certificate dari enrollment yang sama
```

Syarat minimum sertifikat event:

```text
event.certificate_enabled
+ event.status = finished
+ registration.status = registered
+ attendance.status = present
+ belum ada certificate dari event registration yang sama
```

Halaman verifikasi publik hanya menampilkan informasi yang cukup untuk memvalidasi sertifikat. Data personal dan dokumen bukti tidak boleh terekspos melebihi kebutuhan verifikasi.

### Government Intelligence PBJ

Government Intelligence PBJ adalah area internal untuk pengelolaan dan analisis data strategis SDM PBJ Pemerintah Provinsi Lampung.

Fitur inti:

- Master organisasi Pemerintah Daerah Provinsi Lampung dan OPD/unit.
- Data JF PPBJ dan jenjang `PERTAMA`, `MUDA`, `MADYA`, serta `UTAMA`.
- Riwayat perubahan JF PPBJ: penambahan, promosi, mutasi masuk/keluar, pensiun, penonaktifan, serta pembaruan.
- Sertifikat kompetensi PBJ yang berbeda dari sertifikat digital BegawiPBJ.
- Jenis sertifikat awal: `PBJ_LEVEL_1`, `PPK_TYPE_A`, `PPK_TYPE_B`, serta `PPK_TYPE_C`.
- Verifikasi bukti sertifikat kompetensi, status, masa berlaku, serta verifikator.
- Snapshot gap kompetensi per organisasi, periode, serta metrik.
- Dashboard internal, filter, dan export terbatas yang diaudit.

Formula gap:

\[
gap\_count = required\_count - available\_count
\]

Data individual seperti NIP, identifier, nama personel, serta bukti sertifikat hanya tersedia pada area internal dengan permission yang sesuai.

### Statistik Publik PBJ

Statistik Publik PBJ menampilkan indikator agregat ekosistem PBJ tanpa membuka data pribadi atau bukti individual.

Fitur inti:

- Jumlah dan distribusi JF PPBJ.
- Statistik sertifikat kompetensi secara agregat.
- Kebutuhan, ketersediaan, serta gap kompetensi per OPD dalam bentuk agregat.
- Statistik program/course/class, enrollment, completion, kelulusan, serta sertifikat pembelajaran.
- Statistik event, registrasi, kehadiran, serta sertifikat event.
- Statistik sertifikat digital berdasarkan status.
- KPI, chart, serta tren dengan periode dan definisi indikator yang jelas.

Statistik publik wajib menerapkan agregasi, suppression, grouping, atau threshold apabila ukuran kelompok terlalu kecil dan berisiko menimbulkan re-identifikasi. Aturan detail indikator, klasifikasi data, dan disclosure control diatur dalam `docs/domain/public-statistics-policy.md` serta `docs/domain/privacy-and-data-classification.md`.

---

## Approval, Audit, Notifikasi, dan File Storage

### Workflow approval

Workflow approval digunakan untuk tindakan berisiko, berdampak publik, atau mengubah akses pengguna, termasuk:

- Aktivasi/penolakan akun tertentu dan verifikasi profil.
- Pemberian role Trainer, Evaluator, Operator, Admin, Super Admin, atau role berisiko lain.
- Publikasi konten, regulasi, berita, event, serta media.
- Penerbitan, pembatalan, pencabutan, atau penggantian sertifikat.
- Perubahan konfigurasi strategis serta permission.

Workflow harus memiliki state jelas, transisi valid, pelaku berwenang, alasan, timestamp, serta audit trail. Aturan state machine lintas domain ditetapkan di `docs/architecture/workflow-contract.md`.

### Audit trail

Audit trail merekam tindakan signifikan, khususnya autentikasi, perubahan akses, approval, penerbitan/pencabutan, perubahan data restricted, dan operasi administratif.

Audit record minimal memuat actor, aksi, objek/resource, waktu, hasil, serta konteks relevan—tanpa menyimpan password, plain token, secret, atau data pribadi secara berlebihan.

### Notifikasi

Notifikasi dapat digunakan untuk status registrasi, approval, assignment, jadwal, hasil assessment, penerbitan sertifikat, dan event. Kanal notifikasi mengikuti konfigurasi dan preferensi yang diizinkan serta tidak boleh mengungkap data restricted secara berlebihan.

### File storage

File storage membedakan file public, internal, serta restricted.

```text
public/assets/main/uploads-public/
→ Hanya file yang secara eksplisit berstatus public

writable/uploads/
→ File runtime/private
→ Tidak boleh diakses langsung melalui URL
→ Preview/download melalui FileController + FileAccessService
```

Download atau preview file internal/restricted wajib memeriksa authorization server-side. URL file bukan satu-satunya mekanisme kontrol akses. Kebijakan disk, visibility, upload, MIME/size validation, retention, preview, download, dan audit dirinci di `docs/architecture/file-storage-policy.md`.

---

## Security Baseline

Implementasi wajib mengikuti baseline keamanan berikut:

- Menggunakan HTTPS pada lingkungan produksi.
- Menggunakan password hash modern yang didukung framework; tidak pernah menyimpan password asli.
- Menerapkan minimum password 12 karakter serta rate limiting untuk login, reset password, registrasi, dan endpoint sensitif.
- Menerapkan CSRF protection untuk request browser yang mengubah state.
- Melakukan validasi input server-side, output encoding, serta proteksi terhadap XSS, SQL injection, mass assignment, dan upload file berbahaya.
- Menerapkan authorization server-side untuk route, resource, aksi administratif, download, preview, export, dan perubahan data.
- Menggunakan session cookie aman, timeout proporsional, rotasi session ketika autentikasi, serta invalidasi session bila diperlukan.
- Menjadikan token verifikasi/reset sekali pakai, memiliki masa kedaluwarsa, disimpan aman, dan tidak dicatat sebagai plain token pada log/audit.
- Menyimpan secret serta konfigurasi lingkungan pada environment configuration; tidak melakukan hard-code secret dalam repository.
- Menjalankan logging/audit tanpa mengekspos password, token, secret, maupun data pribadi berlebihan.
- Melakukan backup, monitoring, pengujian, serta review hak akses secara berkala.

---

## Struktur Direktori

README ini menetapkan alasan dan boundary arsitektur. `directory-tree.md` menetapkan struktur target serta lokasi file/folder final.

Aturan kerja:

1. Buat atau isi file hanya pada path yang ditetapkan dalam `directory-tree.md`.
2. Jika file target telah tersedia sebagai placeholder, isi file tersebut; jangan membuat path paralel.
3. Jika implementasi membutuhkan perubahan struktur, perbarui README dan `directory-tree.md` secara konsisten sebelum membuat path baru.
4. Keberadaan path/file tidak berarti fitur sudah diimplementasikan atau aman digunakan.
5. Source template reference tidak boleh menjadi view yang dirender controller produksi.

### Root controller

```text
app/Controllers/Landing/        # Route publik FiStudy
app/Controllers/Dashboard/Auth/ # Seluruh autentikasi produksi WowDash
app/Controllers/Dashboard/      # Dashboard Back-office WowDash
app/Controllers/Workspace/      # Aktivitas Workspace FiStudy
```

### Root view

```text
app/Views/landing/              # Landing Page dan Public Area FiStudy
app/Views/main/                 # Layout/component FiStudy untuk aplikasi non-back-office
app/Views/main/workspace/       # Workspace FiStudy
app/Views/dashboard/auth/       # Satu-satunya Auth UI produksi, WowDash
app/Views/dashboard/            # Dashboard Back-office WowDash
app/Views/error/                # Error pages
app/Views/reference/            # Source template non-production
```

### Root asset dan storage

```text
public/assets/main/             # FiStudy produksi: Landing/Public/Workspace
public/assets/dashboard/        # WowDash produksi: Auth/Dashboard
public/assets/main/uploads-public/
                                # Konten eksplisit berstatus public
writable/uploads/               # Private/internal/restricted runtime files
```

### Path yang dilarang

```text
app/Controllers/Auth/
app/Controllers/Admin/
app/Controllers/Admin/AdminAuthController.php
app/Views/auth/
app/Views/workspace/
app/Views/main/layouts/auth.php
public/assets/workspace/
```

Jika path tersebut ditemukan dalam implementasi, migrasikan ke lokasi target pada `directory-tree.md`, perbarui namespace, route, import, filter, test, serta pemanggilan `view()`, kemudian hentikan path lama agar tidak menciptakan dua sumber kebenaran.

---

## Source Template dan Integrasi

Source template disimpan pada `app/Views/reference/` sebagai katalog referensi dan bahan adaptasi.

```text
app/Views/reference/fistudy/
→ Flat HTML pages + assets/ + js/ + inc/
→ Reference untuk Landing/Public/Workspace FiStudy

app/Views/reference/wowdash/
→ Flat HTML pages + assets/
→ Reference untuk Auth/Dashboard WowDash
```

Aturan integrasi:

- File HTML source tidak memiliki route produksi.
- Controller produksi dilarang memanggil `view('reference/...')`.
- HTML source dipecah dan diadaptasi menjadi layout, component, partial, serta domain view CodeIgniter pada `app/Views/landing/`, `app/Views/main/`, atau `app/Views/dashboard/`.
- Asset yang benar-benar digunakan disalin/disesuaikan ke `public/assets/main/` atau `public/assets/dashboard/`.
- Tidak seluruh source asset/template harus dibawa ke produksi; hanya dependensi yang benar-benar dipakai oleh fitur BegawiPBJ.
- Route produksi memakai kontrak bisnis BegawiPBJ, bukan nama file HTML template seperti `course.html`, `table-data.html`, atau `index-12.html`.
- `app/Views/reference/fistudy/inc/sendemail.php` dan PHPMailer bawaan template tidak digunakan untuk email produksi. Email produksi memakai layanan CodeIgniter 4 serta dependency Composer proyek.
- Lisensi template, font, icon, image, dan library pihak ketiga wajib ditinjau serta dicatat sebelum deployment.

---

## Dokumentasi Teknis

Dokumen berikut adalah bagian dari kontrak implementasi dan harus dibuat/diisi sebelum area terkait dikembangkan secara luas.

| Dokumen | Fungsi | Diperlukan sebelum |
|---|---|---|
| `docs/architecture/ui-boundary.md` | Detail boundary UI, template, layout, asset, dan redirect lintas area | Integrasi layout dan asset |
| `docs/architecture/route-contract.md` | Route, HTTP method, controller, view/API response, filter, permission, serta route name | Implementasi route/controller dalam jumlah besar |
| `docs/architecture/auth-and-rbac.md` | Auth, session, account status, role, permission, redirect, filter, serta access matrix | Auth, RBAC, Dashboard, Workspace |
| `docs/architecture/workflow-contract.md` | State machine, transisi, actor, approval, alasan, dan audit | Registrasi, publikasi, event, sertifikat, dan approval |
| `docs/architecture/file-storage-policy.md` | Disk, visibility, upload, preview/download, retention, dan audit file | Upload, regulasi, tugas, sertifikat, dan bukti |
| `docs/database/database-blueprint.md` | Tabel, PK/FK, enum, constraint, index, ownership, migration order, serta seed strategy | Migration dan model |
| `docs/database/database-erd.md` | ERD konseptual serta relasi antar-domain | Migration dan database review |
| `docs/database/data-dictionary.md` | Definisi tabel/kolom, tipe data, nullable, default, klasifikasi data, serta validasi | Migration, model, form, dan API |
| `docs/domain/registration-policy.md` | Field, validasi, verifikasi, perubahan profil, dan pengajuan role per tipe profil | Registrasi multi-step |
| `docs/domain/assessment-and-certification-rules.md` | Aturan assessment, nilai, kelulusan, eligibility, dan sertifikat | Arena Belajar dan Sertifikat |
| `docs/domain/regulation-policy.md` | Versioning, legal status, relation, publication, visibility, preview, dan download regulasi | Modul Regulasi |
| `docs/domain/privacy-and-data-classification.md` | Klasifikasi public/internal/restricted, masking, export, retention, dan audit redaction | Semua modul yang mengolah data pengguna/file |
| `docs/domain/public-statistics-policy.md` | Definisi indikator, aggregation, suppression, threshold, dan disclosure control | Statistik Publik dan Government Intelligence |
| `docs/project/implementation-status.md` | Register status Planned/UI Implemented/Backend Ready/Production Ready per area | Setiap batch pekerjaan |

### Baseline route non-auth

Route final beserta method, controller, filter, permission, route name, serta response/view ditetapkan rinci di `docs/architecture/route-contract.md`. Prefix baseline berikut dikunci:

```text
/                         Landing
/arena-belajar            Katalog pembelajaran publik
/bunga-rampai             Knowledge/publication center
/regulasi                 Repository regulasi publik
/berita                   Berita publik
/event                    Agenda dan event publik
/statistik                Statistik publik teragregasi
/sertifikat/verifikasi    Verifikasi sertifikat publik

/workspace/...            Workspace FiStudy terautentikasi
/dashboard/...            Auth WowDash dan Dashboard Back-office WowDash
```

---

## Status Implementasi

Keberadaan folder/file bukan indikator fitur sudah selesai. Gunakan klasifikasi berikut dalam laporan pekerjaan, changelog, dan `docs/project/implementation-status.md`:

| Status | Arti |
|---|---|
| Planned | Folder/file tersedia sebagai placeholder; belum diisi atau belum berfungsi |
| UI Implemented | Layout/view/component tersedia; dapat masih memakai data dummy atau backend belum lengkap |
| Backend Ready | Route aksi, validation, service, model, persistence, policy, serta authorization utama tersedia |
| Production Ready | Test, audit, security review, observability, dokumentasi operasional, serta acceptance test telah dipenuhi |

Tidak boleh menyatakan fitur selesai hanya karena folder, controller, model, migration, route GET, atau view HTML tersedia.

---

## Tahapan Pengembangan

Urutan implementasi yang direkomendasikan:

1. Fondasi CodeIgniter, environment, konfigurasi, migration, seeder, serta kontrak database/domain.
2. Fondasi asset dan layout FiStudy/WowDash sesuai boundary yang dikunci.
3. Auth terpadu WowDash, lifecycle akun, verifikasi email, reset password, RBAC, filter akses, serta audit autentikasi.
4. Landing dan Public Area FiStudy, termasuk katalog publik serta verifikasi sertifikat.
5. Workspace FiStudy untuk Participant, Trainer, Evaluator, serta Operator.
6. Dashboard WowDash untuk Admin/Super Admin, approval, audit, konfigurasi, serta laporan internal.
7. Modul domain: Arena Belajar, Bunga Rampai, Regulasi, Berita/Event, Sertifikat, Government Intelligence, serta Statistik Publik.
8. Integrasi file storage, notifikasi, export, observability, testing, hardening keamanan, serta review akses.

Setiap batch implementasi wajib menjaga tiga kontrak berikut:

```text
1. Path mengikuti directory-tree.md.
2. FiStudy dan WowDash digunakan sesuai batas area antarmuka.
3. Akses serta data sensitif selalu dikendalikan di server-side.
```

---

## Referensi Proyek

```text
README.md
→ Acuan utama arsitektur, domain, akses, privasi, keamanan, dan implementasi.

directory-tree.md
→ Tree target lengkap serta lokasi file/folder final.

docs/architecture/
→ Kontrak route, auth/RBAC, workflow, UI boundary, dan file storage.

docs/database/
→ Blueprint database, ERD, data dictionary, dan schema fresh.

docs/domain/
→ Policy registrasi, assessment/sertifikat, regulasi, privasi, dan statistik publik.

docs/project/
→ Register status implementasi dan dokumentasi proyek.

database/reference/wilayah.sql
→ Master resmi wilayah; tidak diubah oleh seeder dummy/development.

app/Views/reference/
→ Source template FiStudy/WowDash non-production.
```

---

## Lisensi

Lisensi aplikasi ditentukan oleh Pemerintah Provinsi Lampung atau pemilik resmi sistem BegawiPBJ.

Lisensi FiStudy, WowDash, font, icon, image, JavaScript library, CSS library, dan asset pihak ketiga yang digunakan harus dipatuhi sesuai ketentuan masing-masing sebelum aplikasi didistribusikan atau dideploy.
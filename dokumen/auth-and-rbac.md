# BegawiPengadaan — Authentication and RBAC Contract

## Tujuan

Dokumen ini menetapkan kontrak autentikasi, lifecycle akun, session, role, permission, authorization, filter, assignment, ownership, redirect pascalogin, serta audit akses untuk BegawiPengadaan.

Dokumen ini melengkapi dan tidak mengubah kontrak yang telah ditetapkan dalam:

```text
README.md
dokumen/directory-tree.md
dokumen/route-contract.md
dokumen/ui-boundary.md
```

Seluruh implementasi Auth dan RBAC wajib mengikuti kontrak ini. Perubahan pada account status, role, permission, filter, route Auth, atau boundary akses harus memperbarui dokumen ini serta dokumen yang terkait secara konsisten.

---

## Prinsip Auth dan RBAC

- Seluruh pengguna memakai satu mekanisme autentikasi produksi.
- Auth produksi hanya berada pada `app/Controllers/Dashboard/Auth/` dan memakai UI WowDash pada `app/Views/dashboard/auth/`.
- Prefix route `/dashboard` untuk Auth tidak memberikan privilege Dashboard Back-office.
- Role tidak menggantikan permission; permission tidak menggantikan ownership, assignment, account status, atau workflow state.
- Authorization selalu diperiksa pada server-side di level filter, controller, service, dan policy/resource check yang sesuai.
- Menyembunyikan menu, tombol, atau link bukan mekanisme keamanan.
- Semua perubahan akses, pemberian/pencabutan role, perubahan permission, login, logout, reset password, dan approval akses harus dapat diaudit.
- Password, plain reset token, plain verification token, session ID, secret, dan credential tidak boleh dicatat dalam audit/log.
- Akses data restricted memerlukan least privilege dan validasi konteks resource.

---

## Boundary Auth

```text
Controller auth
app/Controllers/Dashboard/Auth/

View auth
app/Views/dashboard/auth/

Layout auth
app/Views/dashboard/auth/layouts/auth.php

Asset auth
public/assets/dashboard/

Route auth
/dashboard/...
```

Controller Auth yang digunakan:

```text
LoginController.php
LogoutController.php
RegisterController.php
RegisterProfileController.php
EmailVerificationController.php
PasswordController.php
AccountStatusController.php
```

Tidak ada controller atau view Auth produksi di luar boundary tersebut.

---

## Model Identitas dan Akses

Empat konsep harus dipisahkan:

| Konsep | Fungsi | Contoh |
|---|---|---|
| Tipe profil | Mengelompokkan data profil saat registrasi | ASN, Penyedia Barang/Jasa, Akademisi, Masyarakat Umum |
| Account status | Menentukan lifecycle dan kelayakan akun mengakses area aplikasi | `draft`, `submitted`, `pending_verification`, `revision_required`, `active`, `rejected`, `inactive`, `suspended`, `archived` |
| Role | Menjelaskan tanggung jawab bisnis pengguna | `public_user`, `participant`, `trainer`, `evaluator`, `operator`, `admin`, `super_administrator` |
| Permission | Hak granular atas resource dan aksi | `learning.study`, `learning.assignment.grade`, `certificate.issue` |

Akses efektif selalu ditentukan oleh:

```text
Authentication
+ Account Status
+ Role
+ Permission
+ Ownership atau Assignment
+ Visibility
+ Workflow State
```

Contoh: user ber-role `trainer` tidak otomatis boleh mengubah semua lesson. Ia harus memiliki permission yang dibutuhkan dan assignment pada class yang terkait.

---

## Account Status

### Enum teknis dan label UI

| Enum teknis | Label UI | Makna | Akses utama |
|---|---|---|---|
| `draft` | Draf | Registrasi belum selesai/dikirim | Hanya melanjutkan registrasi bila session flow valid |
| `submitted` | Diajukan | Registrasi telah dikirim dan menunggu proses berikutnya | Status akun; tidak masuk Workspace/Dashboard |
| `pending_verification` | Menunggu Verifikasi | Menunggu verifikasi email/profil atau pemeriksaan yang diperlukan | Status akun dan verifikasi; tidak masuk Workspace/Dashboard |
| `revision_required` | Perlu Perbaikan | Data perlu dilengkapi/diperbaiki | Alur perbaikan registrasi/profil yang diizinkan |
| `active` | Aktif | Akun dapat mengakses layanan menurut role/permission | Workspace/Dashboard sesuai akses efektif |
| `rejected` | Ditolak | Pengajuan tidak diterima | Status akun; tidak masuk Workspace/Dashboard |
| `inactive` | Tidak Aktif | Akun tidak aktif tanpa penangguhan khusus | Status akun; tidak masuk Workspace/Dashboard |
| `suspended` | Ditangguhkan | Akses dibatasi sementara berdasarkan tindakan/kebijakan | Status akun; tidak masuk Workspace/Dashboard |
| `archived` | Diarsipkan | Record disimpan sebagai arsip dan tidak digunakan aktif | Tidak dapat mengakses area terautentikasi |

### Aturan lifecycle

```text
draft
→ submitted
→ pending_verification
→ active

submitted atau pending_verification
→ revision_required
→ submitted atau pending_verification

submitted atau pending_verification
→ rejected

active
→ inactive
→ active

active
→ suspended
→ active

inactive atau rejected atau suspended
→ archived
```

Transisi hanya boleh dilakukan melalui service/workflow yang memvalidasi actor, alasan, policy, dan audit. Controller tidak boleh mengubah account status langsung tanpa service yang relevan.

### Ketentuan access status

- Hanya `active` yang dapat mengakses Workspace atau Dashboard Back-office.
- `active` tetap belum cukup untuk Dashboard; user memerlukan dashboard permission.
- `active` tetap belum cukup untuk resource Workspace; user memerlukan role, ownership, enrollment, atau assignment sesuai resource.
- `revision_required` dapat mengakses hanya route perbaikan yang secara eksplisit diizinkan; tidak memperoleh akses Workspace/Dashboard umum.
- `submitted`, `pending_verification`, `rejected`, `inactive`, `suspended`, dan `archived` diarahkan ke status akun atau route Auth yang relevan.

---

## Tipe Profil

Tipe profil digunakan untuk menentukan data registrasi dan profil, bukan hak akses administratif.

| Label | Nilai teknis | Kegunaan |
|---|---|---|
| ASN/Pegawai Pemerintah | `asn` | Profil ASN, OPD/unit, jabatan, dan konteks pengadaan bila relevan |
| Penyedia Barang/Jasa | `provider` | Profil badan usaha/penyedia |
| Akademisi | `academic` | Profil institusi pendidikan/riset serta bidang keahlian |
| Masyarakat Umum | `public` | Profil warga, komunitas, pelaku pendukung, atau Perangkat Desa |

Tipe profil tidak boleh menjadi dasar tunggal untuk memberikan role `operator`, `admin`, atau `super_administrator`.

---

## Role Sistem

### Role dan area utama

| Label UI | Role teknis | Area utama | Kemampuan utama |
|---|---|---|---|
| Public User | `public_user` | Landing/Public | Akses konten publik dan memulai registrasi |
| Participant | `participant` | Workspace | Enrollment, belajar, quiz, tugas, progres, feedback, bookmark, sertifikat pribadi |
| Trainer | `trainer` | Workspace | Kelas yang ditugaskan, materi, quiz, assignment, peserta, dan progres kelas |
| Evaluator | `evaluator` | Workspace | Penilaian manual, feedback, revisi, serta final grading sesuai assignment |
| Operator | `operator` | Workspace | Verifikasi, administrasi operasional, enrollment, absensi, dan approval terdelegasi |
| Admin | `admin` | Dashboard Back-office | Administrasi domain sesuai permission yang diberikan |
| Super Admin | `super_administrator` | Dashboard Back-office | Konfigurasi strategis, RBAC, audit, serta administrasi sistem sesuai permission |

Satu account dapat memiliki lebih dari satu role.

### Role default

Setelah account menjadi `active`, role default yang dapat diberikan adalah:

```text
public_user
participant
```

`participant` tidak otomatis memberikan akses materi pembelajaran. Akses materi tetap memerlukan enrollment sah dan policy class/lesson yang berlaku.

### Role request

Pengguna aktif dapat mengajukan:

```text
trainer
evaluator
```

Pengguna tidak dapat menetapkan sendiri:

```text
operator
admin
super_administrator
```

Role `operator`, `admin`, dan `super_administrator` hanya diberikan melalui penetapan internal oleh actor yang memiliki permission relevan. Setiap grant/revoke role harus memiliki actor, target, alasan, waktu, state keputusan, dan audit record.

---

## Permission Model

Permission adalah hak granular yang menentukan apakah actor dapat melakukan aksi tertentu. Role mengelompokkan permission, tetapi pemeriksaan permission tetap wajib dilakukan untuk aksi sensitif.

### Konvensi key permission

```text
{domain}.{resource}.{action}
```

Terdapat 90 permission kanonis yang terdistribusi ke dalam 12 domain sistem (selaras dengan `database/permissions_catalog.json`):

#### 1. Domain Dashboard (`dashboard`)
- `dashboard.view`: Melihat Ringkasan Dashboard Utama
- `dashboard.indicators.view`: Melihat Indikator Sistem & KPI Internal

#### 2. Domain Kelola Landing Page (`landing`)
- `landing.view`: Melihat Modul Landing Page CMS
- `landing.slider.manage`: Mengelola Slider Hero & Promosi
- `landing.profile.manage`: Mengelola Profil Lembaga & Pilar PATIK
- `landing.portal.manage`: Mengelola Tautan Portal Eksternal
- `landing.team.manage`: Mengelola Tim Inovator & Testimoni
- `landing.announcement.manage`: Mengelola Pengumuman & Banner CMS

#### 3. Domain Akses & Pengguna (`access`)
- `access.manage`: Manajemen Akses & Otorisasi Pengguna
- `access.user.view`: Melihat Data Pengguna Platform
- `access.user.manage`: Mengelola Akun Pengguna Platform
- `access.verification.view`: Melihat Antrean Verifikasi Profil
- `access.verification.approve`: Memproses Verifikasi & Perubahan Profil
- `access.role.view`: Melihat Daftar Peran (Role) & Matriks
- `access.role.manage`: Mengelola Peran (Role) & Hak Akses
- `access.organization.view`: Melihat Unit Organisasi PBJ
- `access.organization.manage`: Mengelola Unit Organisasi PBJ
- `access.procurementrole.manage`: Mengelola Peran & Penugasan Pengadaan
- `user.manage`: Kelola Akun Pengguna (Alias)
- `user.verify`: Verifikasi Profil Akun (Alias)
- `role.manage`: Kelola Konfigurasi Peran (Alias)

#### 4. Domain Pembelajaran LMS (`learning`)
- `learning.view`: Melihat Arena Pembelajaran PBJ
- `learning.program.manage`: Mengelola Program Pembelajaran PBJ
- `learning.course.manage`: Mengelola Kursus & Silabus
- `learning.class.view`: Melihat Kelas Pembelajaran PBJ
- `learning.class.create`: Membuat Kelas Pembelajaran Baru
- `learning.class.update`: Memperbarui Informasi Kelas Pembelajaran
- `learning.class.delete`: Menghapus Kelas Pembelajaran
- `learning.curriculum.manage`: Mengelola Kurikulum & Modul Belajar
- `learning.assessment.view`: Melihat Kuis & Assessment Pembelajaran
- `learning.assessment.manage`: Mengelola Kuis & Bank Soal
- `learning.enrollment.view`: Melihat Pendaftaran Peserta Kelas
- `learning.enrollment.verify`: Memverifikasi Pendaftaran Peserta
- `learning.grading.manage`: Menilai Tugas & Evaluasi Peserta
- `learning.certificate.manage`: Monitoring Kelayakan Sertifikat Pembelajaran
- `learning.report.view`: Melihat Laporan Statistik Pembelajaran
- `lms.manage`: Manajemen Sistem Pembelajaran (LMS Alias)
- `enrollment.verify`: Verifikasi Pendaftaran Kelas (Alias)
- `assessment.evaluate`: Evaluasi Penilaian Peserta (Alias)

#### 5. Domain Bunga Rampai KMS (`bunga`)
- `bunga.view`: Melihat Direktori Bunga Rampai PBJ
- `bunga.article.manage`: Mengelola Naskah & Artikel PBJ
- `bunga.faq.manage`: Mengelola Tanya Jawab (FAQ) PBJ
- `bunga.casestudy.manage`: Mengelola Studi Kasus PBJ
- `bunga.template.manage`: Mengelola Template Dokumen PBJ
- `bunga.media.manage`: Mengelola Pustaka Media & Galeri PBJ
- `bunga.taxonomy.manage`: Mengelola Kategori & Tag Bunga Rampai
- `kms.manage`: Manajemen Knowledge Management System (Alias)

#### 6. Domain Regulasi PBJ (`regulation`)
- `regulation.view`: Melihat Direktori Regulasi PBJ
- `regulation.create`: Menambah Dokumen Regulasi Baru
- `regulation.edit`: Mengubah Dokumen Regulasi PBJ
- `regulation.delete`: Menghapus Dokumen Regulasi PBJ
- `regulation.approve`: Menyetujui & Mempublikasikan Regulasi
- `regulation.category.manage`: Mengelola Kategori Regulasi PBJ
- `regulation.stage.manage`: Mengelola Tahapan PBJ & Kompetensi
- `regulation.manage`: Manajemen Penuh Regulasi PBJ (Alias)

#### 7. Domain Komunikasi & Event (`communication`)
- `communication.view`: Melihat Modul Komunikasi & Event
- `communication.news.manage`: Mengelola Warta Berita PBJ
- `communication.event.view`: Melihat Agenda Event & Bimtek
- `communication.event.manage`: Mengelola Agenda Event & Bimtek
- `communication.attendance.manage`: Mengelola Presensi & Kehadiran Event
- `communication.inbox.manage`: Mengelola Pesan Masuk & Helpdesk
- `communication.helpdesk.manage`: Mengelola Pengaturan Helpdesk Widget
- `communication.newsletter.manage`: Mengelola Berlangganan Newsletter

#### 8. Domain Sertifikat Digital (`certificate`)
- `certificate.view`: Melihat Master & Riwayat Sertifikat
- `certificate.manage`: Mengelola Penerbitan Sertifikat
- `certificate.batch.manage`: Mengelola Penerbitan Batch Sertifikat
- `certificate.template.manage`: Mengelola Desain Template Sertifikat
- `certificate.approve`: Menyetujui Penerbitan Sertifikat

#### 9. Domain Government Intelligence (`intelligence`)
- `intelligence.view`: Melihat Dashboard Intelligence SDM PBJ
- `intelligence.jfppbj.manage`: Mengelola Data & Riwayat SDM JF PPBJ
- `intelligence.competency.manage`: Mengelola Data Sertifikat Kompetensi
- `intelligence.opd.manage`: Mengelola Analisis Pemetaan Unit Kerja/OPD

#### 10. Domain Pusat Laporan (`reports`)
- `reports.view`: Melihat Pusat Laporan Dashboard
- `reports.learning.view`: Melihat Laporan Statistik Pembelajaran
- `reports.event.view`: Melihat Laporan Keikutsertaan Event
- `reports.certificate.view`: Melihat Laporan Penerbitan Sertifikat
- `reports.competency.view`: Melihat Laporan Kesenjangan Kompetensi
- `reports.user.view`: Melihat Laporan Aktivitas Pengguna
- `reports.export`: Mengekspor Berkas Laporan & Riwayat Ekspor

#### 11. Domain Survei Kepuasan (`survey`)
- `survey.view`: Melihat Hasil & Statistik Survei Kepuasan
- `survey.manage`: Mengelola Pertanyaan & Konfigurasi Survei

#### 12. Domain Pengaturan & Sistem (`system`)
- `system.settings`: Mengelola Pengaturan Sistem & Platform
- `approval.view`: Melihat Antrean Approval Sistem
- `approvals.review`: Meninjau Antrean Approval Sistem
- `approval.decide`: Memutuskan (Setujui/Tolak) Approval
- `audit.view`: Melihat Jejak Audit Log Aktivitas
- `audit.export`: Mengekspor Jejak Audit Log
- `notification.view`: Melihat Pusat Notifikasi Dashboard
- `notification.send`: Mengirim Notifikasi Broadcast Sistem
- `system.maintenance`: Mengelola Mode Pemeliharaan Platform

Permission detail dapat diperluas pada seed/reference sesuai kebutuhan domain, tetapi key baru wajib mengikuti pola penamaan ini dan didokumentasikan pada route contract bila dipakai oleh route.

### Prinsip permission

- Permission bersifat additive; grant tidak boleh tersirat hanya dari nama role.
- Permission tidak boleh mengabaikan account status.
- Permission tidak boleh mengabaikan ownership, enrollment, assignment, delegated scope, visibility, atau workflow state.
- Permission berisiko tinggi harus diberikan melalui approval/penetapan internal yang diaudit.
- Super Admin tidak menghapus kebutuhan audit atau workflow; role tersebut tetap mengikuti policy sistem.

---

## Ownership, Assignment, dan Delegated Scope

Permission saja tidak cukup untuk mengakses resource domain. Sistem harus memeriksa konteks berikut.

| Konteks | Contoh | Pemeriksaan minimum |
|---|---|---|
| Ownership | Profile, bookmark, notifikasi, submission, sertifikat pribadi | Resource dimiliki oleh user aktif |
| Enrollment | Lesson, quiz, assignment, feedback, course/class peserta | User memiliki enrollment sah pada class terkait |
| Trainer assignment | Lesson, quiz, assignment, progres peserta | Trainer ditugaskan pada class yang terkait |
| Evaluator assignment | Submission dan grading | Evaluator ditugaskan untuk class/assessment/submission terkait |
| Operator delegated scope | Verifikasi, enrollment, absensi, approval | Operator diberi scope organisasi/proses yang sesuai |
| Dashboard scope | Data organisasi, laporan, intelligence, export | Permission dan scope internal yang sah |
| Visibility | Konten publik, file public/internal/restricted | Visibility resource sesuai boundary dan actor |
| Workflow state | Approval/publikasi/sertifikat | Transisi atau tindakan diizinkan pada state saat ini |

Policy ini harus diterapkan pada service atau policy/resource authorization, bukan hanya pada UI atau filter global.

---

## Filter Contract dan Enforcement Arsitektur

Filter yang tersedia dan fungsi minimalnya:

| Filter | Tujuan | Pemakaian utama |
|---|---|---|
| `guest` | Menolak user yang sudah login dari halaman guest-only | Login, registrasi, lupa/reset password |
| `auth` | `App\Filters\AuthenticationFilter`: Gateway utama pengamanan seluruh rute `/dashboard/*` | Dashboard Back-office dan boundary Auth |
| `workspace-auth` | `App\Filters\WorkspaceAuthFilter`: Gateway utama pengamanan seluruh rute `/workspace/*` | Workspace (Peserta, Trainer, Evaluator, Operator) |
| `verifiedEmail` | Memastikan email terverifikasi bila diperlukan | Workspace/Dashboard aktif dan aksi sensitif |
| `accountStatus` | Memeriksa account status yang diperlukan, terutama `active` | Workspace/Dashboard dan aksi terproteksi |
| `role` | Memastikan salah satu role yang diizinkan | Participant/Trainer/Evaluator/Operator route spesifik |
| `permission` | Memastikan permission granular | CRUD Back-office, approval, audit, export, operasi sensitif |
| `workspaceAccess` | Memastikan user aktif berhak ke boundary Workspace | Semua route `/workspace/...` |
| `dashboardAccess` | Memastikan user aktif memiliki dashboard permission | Semua Back-office `/dashboard/...` di luar Auth |
| `rateLimit` | Membatasi abuse pada endpoint publik/sensitif | Login, registrasi, reset, verifikasi, pencarian, statistik, kontak |
| `auditContext` | Menetapkan context actor/request untuk audit aksi sensitif | Aksi perubahan state terautentikasi |

### Implementasi Runtime Gateway: `AuthenticationFilter`

Pengamanan pada area `/dashboard/*` ditegakkan secara berlapis oleh `App\Filters\AuthenticationFilter`:

1. **Bypass Publik (Whitelist Auth)**:
   Endpoint publik autentikasi (`dashboard/masuk`, `dashboard/lupa-kata-sandi`, `dashboard/keluar`, `dashboard/status-akun`, `dashboard/daftar*`, `dashboard/reset-kata-sandi/*`) dibypass tanpa mewajibkan session login.
2. **Validasi Sesi Aktif**:
   Memastikan `session()->get('isLoggedIn') === true`. Bila tidak valid, pengunjung dialihkan ke `dashboard/masuk`.
3. **Verifikasi Real-time Status Akun ke Database**:
   Memeriksa data aktual `users.account_status = 'active'`. Akun berstatus selain active (`draft`, `pending_verification`, `revision_required`, `suspended`, dll.) otomatis dihancurkan sesinya dan diarahkan ke `dashboard/status-akun`.
4. **Pemisahan Boundary Dashboard vs Workspace**:
   Memeriksa `AuthorizationService::hasDashboardAccess()`. Pengguna dengan peran Workspace murni (`peserta`, `trainer`, `evaluator`, `operator`) dialihkan secara mulus ke `/workspace` tanpa akses ke Back-office.
5. **Super Administrator Full Bypass**:
   Peran `super_administrator` (`AuthorizationService::isSuperAdmin()`) memperoleh bypass penuh terhadap pemeriksaan zona granular.
6. **Dynamic Zone-to-Permission Mapping (18 Zona)**:
   Setiap path URL dipetakan ke himpunan permission domain terkait (Role & Hak Akses, Verifikasi Profil, Permohonan Role, Manajemen Pengguna, Unit Organisasi & Peran Pengadaan, Kelola Landing Page, Arena Belajar, Bunga Rampai, Regulasi PBJ, Komunikasi & Event, Sertifikat, Government Intelligence, Pusat Laporan, Survei Kepuasan, Pengaturan Sistem, Approval Hub, Audit Trail, dan Pusat Notifikasi).
7. **Isolasi Peran Administrator**:
   Peran `admin` diizinkan mengelola seluruh zona operasional, namun secara ketat diblokir dari pengelolaan Role & Permission (`dashboard/akses/role`, `dashboard/akses/permission`) kecuali memiliki izin eksplisit `access.role.manage` atau `role.manage`.
8. **Dynamic Mutation Guard**:
   Secara otomatis memproteksi metode HTTP bermutasi (`POST`, `PUT`, `PATCH`, `DELETE`) dan pola URL mutasi (`tambah|simpan|edit|update|hapus|delete|toggle|approve|reject|batal|create|store|sync`). Jika pengguna hanya memegang izin read-only (`*.view`), aksi mutasi ditolak dengan HTTP 403 Forbidden.

### Implementasi Runtime Gateway: `WorkspaceAuthFilter`

Pengamanan pada area `/workspace/*` ditegakkan oleh `App\Filters\WorkspaceAuthFilter`:
1. Memastikan sesi login valid.
2. Sinkronisasi real-time peran pengguna (`user_roles` dan `roles`) langsung dari database MySQL (fail-closed single source of truth).
3. Memastikan akun berstatus `active`.
4. Isolasi sub-grup fungsional (Peserta, Trainer, Operator, Administrator) dengan fail-safe redirect.

### Urutan filter rekomendasi

#### Auth guest route

```text
guest
+ rateLimit sesuai endpoint
```

#### Workspace route

```text
auth
+ verifiedEmail bila diperlukan
+ accountStatus:active
+ workspaceAccess
+ role/permission sesuai route
+ auditContext untuk aksi perubahan state
```

#### Dashboard Back-office route

```text
auth
+ verifiedEmail
+ accountStatus:active
+ dashboardAccess
+ permission sesuai aksi
+ auditContext untuk aksi perubahan state
```

#### File dan export route

```text
auth
+ accountStatus:active
+ FileAccessService atau ExportService
+ ownership/assignment/permission sesuai resource
+ auditContext bila dibutuhkan
```

Filter tidak menggantikan pemeriksaan objek spesifik pada service. Contoh: filter `permission:learning.assignment.grade` tidak cukup tanpa memastikan Evaluator memang ditugaskan pada submission yang dinilai.

---

## Session dan Login Security

### Session minimum

Session autentikasi minimal menyimpan:

```text
user_id
session_version atau session marker yang aman
authenticated_at
last_activity_at
```

Data restricted, permission matrix lengkap, password hash, token, atau profil lengkap tidak boleh disimpan sebagai session payload jika tidak diperlukan.

### Aturan session

- Session ID wajib diregenerasi setelah login berhasil.
- Session wajib diinvalidasi saat logout.
- Session dapat diinvalidasi saat reset password, perubahan password, penangguhan akun, pencabutan akses sensitif, atau perubahan security policy yang relevan.
- Cookie session harus menggunakan konfigurasi secure, HttpOnly, serta SameSite yang sesuai dengan deployment.
- Session timeout harus proporsional terhadap risiko area; Dashboard Back-office dapat memakai timeout yang lebih ketat daripada Landing/Public.
- Semua redirect `next`/return URL harus divalidasi agar tidak menimbulkan open redirect.

### Login

- Login harus menggunakan identifier yang didukung kebijakan sistem, misalnya email atau username.
- Pesan gagal login harus generik dan tidak membedakan identifier salah, password salah, atau account tidak ditemukan.
- Endpoint login memakai rate limit dan pencatatan audit tanpa menyimpan password.
- Account yang tidak `active` tidak boleh diarahkan ke Workspace/Dashboard umum setelah autentikasi.

---

## Password dan Recovery

### Password policy

- Panjang minimum password adalah 12 karakter.
- Password harus memenuhi kombinasi huruf besar, huruf kecil, angka, dan simbol.
- Password disimpan sebagai hash menggunakan mekanisme keamanan modern yang didukung framework.
- Password asli, password confirmation, dan hash password tidak boleh dicatat pada log/audit/flash message.
- Perubahan password wajib menginvalidasi token reset yang aktif dan dapat menginvalidasi session lain sesuai policy.

### Password reset

- Request reset memakai pesan generic agar tidak mengungkap apakah email/username terdaftar.
- Reset token bersifat acak, sekali pakai, kedaluwarsa, dan disimpan aman.
- Plain token hanya dikirim pada kanal reset dan tidak disimpan dalam audit/log.
- Endpoint reset memakai rate limit.
- Setelah reset berhasil, user diarahkan ke login atau halaman sukses sesuai route contract.

---

## Verifikasi Email

- Verifikasi email diperlukan dalam lifecycle account sebelum akses aktif yang mensyaratkan email terverifikasi.
- Token verifikasi bersifat acak, sekali pakai, kedaluwarsa, dan tersimpan aman.
- Resend verifikasi memakai rate limit.
- Proses verifikasi tidak boleh membocorkan account existence secara tidak perlu.
- Verifikasi email tidak dengan sendirinya memberikan role atau dashboard permission.
- Perubahan email harus mengikuti verification flow baru serta audit yang sesuai.

---

## Registrasi dan Profile Change

### Registrasi

Registrasi memakai Auth WowDash dan route multi-step pada `/dashboard/daftar`.

Urutan minimum:

```text
1. Akun dasar + tipe profil
2. Wilayah/domisili + alamat ringkas
3. Profil spesifik: ASN/provider/academic/public
4. Review + persetujuan syarat dan kebijakan privasi
5. Submit
6. Verifikasi email
7. Verifikasi profil bila diperlukan
8. Active, revision_required, atau rejected
```

Akun dasar memerlukan nama lengkap, email unik/valid, username unik, password, konfirmasi password, nomor telepon, tipe profil, wilayah/alamat yang diperlukan, serta persetujuan kebijakan yang relevan.

### Profile change request

Perubahan profil yang memengaruhi data terverifikasi, tipe profil, organisasi, data kepegawaian, peran pengadaan, atau akses harus memakai `ProfileChangeRequestService` dan dapat memerlukan review/approval.

User tidak boleh mengubah sendiri data yang sudah ditandai verified/restricted bila perubahan tersebut memerlukan audit atau verifikasi ulang.

### Role request

Role request untuk `trainer` atau `evaluator` dibuat oleh user aktif melalui Workspace. Review keputusan dilakukan oleh Back-office melalui permission `role.request.review` atau policy yang setara.

---

## Redirect Contract

### Guest membuka route terproteksi

```text
Guest
→ simpan intended route yang aman
→ redirect ke /dashboard/masuk
→ setelah login, validasi account status dan akses route
→ kembali ke intended route bila sah
→ jika tidak sah, redirect ke tujuan default yang diizinkan
```

### User aktif setelah login

```text
Memiliki dashboard permission dan intended route Dashboard sah
→ intended Dashboard route

Memiliki Workspace role dan intended route Workspace sah
→ intended Workspace route

Memiliki dashboard permission tanpa intended route
→ /dashboard

Memiliki Workspace role tanpa intended route
→ /workspace

Hanya public_user atau belum memenuhi akses aktif
→ status/account flow atau Landing sesuai policy
```

### Status nonaktif

```text
submitted / pending_verification
→ /dashboard/status-akun

revision_required
→ status akun dan alur perbaikan yang diizinkan

rejected / inactive / suspended / archived
→ /dashboard/status-akun
```

Redirect tidak boleh mengarahkan user ke URL eksternal atau route yang tidak lolos authorization.

---

## Audit Access dan Keamanan

### Kejadian yang wajib dicatat

- Login berhasil, gagal, dan rate limit/security event yang relevan.
- Logout dan invalidasi session bila relevan.
- Registrasi submitted dan perubahan account status.
- Verifikasi email dan request resend tanpa menyimpan plain token.
- Password reset request dan password reset berhasil tanpa menyimpan plain token.
- Grant/revoke role, perubahan permission, dan perubahan role-permission matrix.
- Role request, profile change request, review, approval, rejection, atau revision request.
- Akses/unduh/export data restricted bila policy mensyaratkannya.
- Perubahan setting keamanan, storage, notifikasi, dan konfigurasi strategis.
- Upaya akses dilarang atau anomaly yang perlu dicatat menurut security policy.

### Minimal field audit

```text
actor_id
actor_role_snapshot bila diperlukan
action
resource_type
resource_id
result
timestamp
request_context yang aman
reason atau notes bila diwajibkan
```

Audit tidak boleh menyimpan:

```text
password
password hash
plain verification token
plain reset token
session ID
secret
credential
full restricted payload yang tidak dibutuhkan
```

---

## Matriks Akses Ringkas

| Area/Aksi | Guest | Public User | Participant | Trainer | Evaluator | Operator | Admin | Super Admin |
|---|---:|---:|---:|---:|---:|---:|---:|---:|
| Landing/Public published content | Ya | Ya | Ya | Ya | Ya | Ya | Ya | Ya |
| Auth login/registration | Ya | Tidak sebagai guest | Tidak sebagai guest | Tidak sebagai guest | Tidak sebagai guest | Tidak sebagai guest | Tidak sebagai guest | Tidak sebagai guest |
| Workspace home | Tidak | Berdasarkan role aktif | Ya | Ya | Ya | Ya | Hanya bila role Workspace juga ada | Hanya bila role Workspace juga ada |
| Participant learning activity | Tidak | Tidak | Sesuai enrollment | Tidak kecuali enrollment/role terkait | Tidak kecuali role terkait | Sesuai delegated scope | Hanya bila policy mengizinkan | Hanya bila policy mengizinkan |
| Trainer class activity | Tidak | Tidak | Tidak | Sesuai assignment | Tidak kecuali assignment | Sesuai delegated scope | Sesuai permission/policy | Sesuai permission/policy |
| Evaluator grading | Tidak | Tidak | Tidak | Tidak kecuali assignment | Sesuai assignment | Sesuai delegated scope | Sesuai permission/policy | Sesuai permission/policy |
| Operator operational activity | Tidak | Tidak | Tidak | Tidak kecuali scope | Tidak kecuali scope | Sesuai delegated scope | Sesuai permission/policy | Sesuai permission/policy |
| Dashboard Back-office | Tidak | Tidak | Tidak | Tidak kecuali dashboard permission | Tidak kecuali dashboard permission | Tidak kecuali dashboard permission | Sesuai permission | Sesuai permission |
| RBAC management | Tidak | Tidak | Tidak | Tidak | Tidak | Tidak kecuali explicit permission | Sesuai permission | Sesuai permission |
| Restricted file/export | Tidak | Tidak kecuali ownership/permission | Sesuai ownership/enrollment | Sesuai assignment | Sesuai assignment | Sesuai delegated scope | Sesuai permission | Sesuai permission |

Matriks ini adalah ringkasan. Permission granular, ownership, assignment, visibility, workflow state, dan account status tetap menentukan akses akhir.

---

## Checklist Implementasi Auth dan RBAC

Sebelum sebuah bagian Auth/RBAC dinyatakan `Backend Ready`, pastikan:

- [ ] Controller Auth hanya berada di `app/Controllers/Dashboard/Auth/`.
- [ ] View Auth hanya berada di `app/Views/dashboard/auth/`.
- [ ] Layout Auth hanya memakai `app/Views/dashboard/auth/layouts/auth.php`.
- [ ] Route Auth memakai prefix `/dashboard` dan route name `dashboard.auth.*`.
- [ ] Password memakai hashing modern dan tidak pernah disimpan/log sebagai plain text.
- [ ] Token reset/verifikasi bersifat sekali pakai, kedaluwarsa, dan tidak dicatat plain text.
- [ ] Account status memakai enum teknis yang ditetapkan pada dokumen ini.
- [ ] Workspace dan Dashboard memerlukan account status `active`.
- [ ] Dashboard memerlukan `dashboardAccess` dan permission granular.
- [ ] Workspace memerlukan `workspaceAccess`, role, dan ownership/enrollment/assignment sesuai resource.
- [ ] Role/permission change memiliki approval/policy yang sesuai dan audit trail.
- [ ] Guest route memakai rate limit yang relevan.
- [ ] Session diregenerasi setelah login dan diinvalidasi saat logout.
- [ ] Redirect pascalogin tervalidasi dan tidak memungkinkan open redirect.
- [ ] Feature test mencakup login, logout, status account, access denial, role/permission, ownership, assignment, reset password, dan verifikasi email.

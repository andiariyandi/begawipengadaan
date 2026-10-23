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
app/Views/dashboard/layouts/auth.php

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

Contoh:

```text
dashboard.view
user.view
user.create
user.update
profile.verify
role.view
role.create
role.update
role.request.review
permission.view
permission.create
permission.update
permission.assign
approval.view
approval.decide
audit.view
organization.view
organization.create
organization.update
report.view
report.statistics.view
setting.view
setting.application.update
setting.notification.update
setting.storage.update
setting.security.update
system.health.view

learning.program.view
learning.program.create
learning.program.update
learning.course.view
learning.course.create
learning.course.update
learning.class.view
learning.class.create
learning.class.update
learning.module.view
learning.lesson.view
learning.lesson.manage
learning.enrollment.view
learning.enrollment.manage
learning.quiz.manage
learning.assignment.manage
learning.assignment.grade
learning.grade.view
learning.report.view
learning.study

knowledge.article.view
knowledge.article.create
knowledge.article.update
knowledge.faq.view
knowledge.media.view
knowledge.category.view
knowledge.publish.review

regulation.view
regulation.create
regulation.update
regulation.version.view
regulation.relation.view
regulation.publish.review

news.view
news.create
news.update
event.view
event.create
event.update
event.registration.view
event.attendance.view
event.attendance.manage

certificate.view
certificate.issue
certificate.approve
certificate.template.view
certificate.template.create

intelligence.view
intelligence.organization.view
intelligence.jf_ppbj.view
intelligence.certificate.view
intelligence.gap.view
intelligence.export

file.upload
file.preview
file.download
export.download
```

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

## Filter Contract

Filter yang tersedia dan fungsi minimalnya:

| Filter | Tujuan | Pemakaian utama |
|---|---|---|
| `guest` | Menolak user yang sudah login dari halaman guest-only | Login, registrasi, lupa/reset password |
| `auth` | Memastikan session autentikasi valid | Workspace, Dashboard, file, export, logout |
| `verifiedEmail` | Memastikan email terverifikasi bila diperlukan | Workspace/Dashboard aktif dan aksi sensitif |
| `accountStatus` | Memeriksa account status yang diperlukan, terutama `active` | Workspace/Dashboard dan aksi terproteksi |
| `role` | Memastikan salah satu role yang diizinkan | Participant/Trainer/Evaluator/Operator route spesifik |
| `permission` | Memastikan permission granular | CRUD Back-office, approval, audit, export, operasi sensitif |
| `workspaceAccess` | Memastikan user aktif berhak ke boundary Workspace | Semua route `/workspace/...` |
| `dashboardAccess` | Memastikan user aktif memiliki dashboard permission | Semua Back-office `/dashboard/...` di luar Auth |
| `rateLimit` | Membatasi abuse pada endpoint publik/sensitif | Login, registrasi, reset, verifikasi, pencarian, statistik, kontak |
| `auditContext` | Menetapkan context actor/request untuk audit aksi sensitif | Aksi perubahan state terautentikasi |

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
- [ ] Layout Auth hanya memakai `app/Views/dashboard/layouts/auth.php`.
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

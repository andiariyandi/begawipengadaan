# BegawiPBJ — Claude Code Project Context

> Dokumen ini adalah context kerja utama untuk Claude Code pada project BegawiPBJ.
>
> Baca dokumen ini sebelum melakukan analisis, perbaikan bug, implementasi route, controller, service, model, view, workflow, authorization, database, atau asset.
>
> Project ini masih berada pada tahap **UI Draft / integration awal**. Sebagian besar menu, route, controller, dan view mungkin telah ada sebagai struktur atau draft visual, tetapi belum boleh dianggap fungsional hanya karena file atau halaman tersedia.

---

# 1. Identitas Project

## Nama

```text
BegawiPBJ
```

## Deskripsi

BegawiPBJ adalah platform ekosistem digital Pengadaan Barang/Jasa Pemerintah untuk Pemerintah Provinsi Lampung.

Platform ini bukan hanya LMS dan bukan hanya dashboard administrasi. BegawiPBJ menyatukan:

```text
Arena Belajar
Bunga Rampai
Regulasi PBJ
Berita, Agenda, dan Event
Sertifikat Digital
Government Intelligence PBJ
Statistik Publik PBJ
Identity dan RBAC
Approval Workflow
Audit Trail
Notification
Bookmark
File Storage
Export
```

## Framework

```text
CodeIgniter 4 AppStarter
PHP
MySQL/MariaDB
```

Project ini **bukan Laravel**.

Jangan menggunakan asumsi Laravel seperti:

```text
routes/web.php
resources/views/
php artisan
Eloquent
Blade syntax
Laravel middleware syntax
```

Gunakan pola CodeIgniter 4.

Command utama:

```bash
php spark routes
php spark cache:clear
php spark migrate
php spark db:seed
php spark make:controller
php spark make:model
```

---

# 2. Status Implementasi Saat Ini

## Kondisi Aktual

Project saat ini belum dapat dianggap backend-ready.

Status umum saat ini:

| Area | Kondisi saat ini |
|---|---|
| Dokumentasi arsitektur | Sudah tersedia dan menjadi pedoman |
| Route contract | Sudah tersedia |
| UI Landing/Public | Sebagian besar masih draft |
| UI Auth | Draft tersedia |
| UI Workspace | Sebagian besar draft |
| UI Dashboard | Sebagian besar draft |
| Schema database | Sudah tersedia sebagai blueprint/schema fresh |
| Migration | Belum dianggap selesai/teruji |
| Seeder | Belum dianggap lengkap/teruji |
| Test suite | Belum dianggap tersedia/lengkap |
| Backend Login | Sudah mulai ada, perlu diaudit |
| Backend Registrasi | Sudah mulai ada, masih bermasalah |
| Backend modul lain | Jangan diasumsikan sudah berjalan |

## Masalah Utama Saat Ini

Login dan registrasi telah mulai diimplementasikan, tetapi terdapat kemungkinan konflik dari implementasi lama/legacy.

Masalah yang harus diantisipasi:

```text
Controller Auth lama dan baru hidup bersamaan
Route lama dan route baru sama-sama aktif
Service auth lama dan service baru berbeda
View lama masih mengarah pada route lama
Redirect masih memakai path legacy
Form registration memakai controller/action lama
Namespace Controller tidak konsisten
Session key lama dan session key baru berbeda
Database insert registration tidak konsisten dengan schema
Role/status account tidak dibuat secara atomik
```

Jangan langsung menghapus file legacy tanpa audit.

Target awal adalah:

```text
Satu sistem Auth produksi
Satu registration flow produksi
Satu sumber route aktif
Satu session contract
Satu service layer utama
```

---

# 3. Urutan Otoritas Dokumen

Jika terdapat konflik antara kode, route, dokumentasi, dan UI draft, gunakan urutan berikut:

```text
1. app/Config/Routes.php yang benar-benar aktif pada runtime
2. dokumen/README.md
3. dokumen/route-contract.md
4. dokumen/ui-boundary.md
5. dokumen/auth-and-rbac.md
6. dokumen/registration-policy.md
7. dokumen/workflow-contract.md
8. dokumen/file-storage-policy.md
9. dokumen/privacy-and-data-classification.md
10. dokumen/assessment-and-certification-rules.md
11. dokumen/regulation-policy.md
12. dokumen/public-statistics-policy.md
13. dokumen/database-blueprint.md
14. dokumen/database-erd.md
15. dokumen/data-dictionary.md
16. dokumen/schema-fresh-v4.sql
17. Existing implementation code
18. Existing draft UI/view
```

## Prinsip Penting

Kode lama bukan otomatis sumber kebenaran.

View lama bukan otomatis sumber kebenaran.

Route lama bukan otomatis sumber kebenaran.

Jika controller/view lama bertentangan dengan route aktif dan kontrak project:

```text
Perbaiki controller/view/path lama agar mengikuti route aktif.
```

Jangan mengubah route aktif agar cocok dengan view atau controller lama, kecuali memang ada kesalahan nyata pada route contract dan perubahan tersebut disetujui.

---

# 4. Batas Antarmuka Produksi

BegawiPBJ memiliki lima boundary produksi.

Setiap halaman produksi hanya boleh berada pada satu boundary utama.

| Boundary | Template | Controller Root | View Root | Layout | Asset |
|---|---|---|---|---|---|
| Landing/Public | FiStudy | `app/Controllers/Landing/` | `app/Views/landing/` | `landing/layouts/public.php` | `public/assets/main/` |
| Auth | WowDash | `app/Controllers/Dashboard/Auth/` | `app/Views/dashboard/auth/` | `dashboard/layouts/auth.php` | `public/assets/dashboard/` |
| Workspace | FiStudy | `app/Controllers/Workspace/` | `app/Views/main/workspace/` | `main/layouts/workspace.php` | `public/assets/main/` |
| Dashboard Back-office | WowDash | `app/Controllers/Dashboard/` | `app/Views/dashboard/` | `dashboard/layouts/dashboard.php` | `public/assets/dashboard/` |
| Error | Minimal/custom | Handler error/exception | `app/Views/error/` | Minimal/custom | Minimum asset |

## Reference Boundary

```text
app/Views/reference/
```

Berisi source HTML FiStudy dan WowDash sebagai referensi.

Reference boundary bukan production.

Dilarang:

```text
Membuat route produksi ke reference/
Memanggil view('reference/...')
Menggunakan source HTML reference langsung sebagai halaman final
Menggunakan asset source reference secara langsung
```

Gunakan reference hanya untuk mengadaptasi layout, component, markup, dan asset ke boundary produksi yang benar.

---

# 5. Aturan Template dan Asset

## FiStudy

FiStudy hanya digunakan untuk:

```text
Landing/Public Area
Workspace
```

Asset:

```text
public/assets/main/
```

View utama:

```text
app/Views/landing/
app/Views/main/
app/Views/main/workspace/
```

## WowDash

WowDash hanya digunakan untuk:

```text
Unified Authentication
Dashboard Back-office
```

Asset:

```text
public/assets/dashboard/
```

View utama:

```text
app/Views/dashboard/auth/
app/Views/dashboard/
```

## Larangan Asset

Dilarang:

```text
FiStudy CSS/JS pada Auth WowDash
FiStudy CSS/JS pada Dashboard Back-office
WowDash CSS/JS pada Landing
WowDash CSS/JS pada Workspace
Asset root ketiga seperti public/assets/workspace/
File internal/restricted pada public/assets/
```

## File Storage

```text
public/assets/main/uploads-public/
→ Hanya file yang benar-benar public

writable/uploads/
→ File internal/restricted/private

writable/exports/
→ File export sementara yang tetap protected
```

File private tidak boleh dapat dibuka hanya karena user mengetahui path file.

Download dan preview private harus melalui controller/service yang memeriksa:

```text
visibility
lifecycle_status
ownership
assignment
permission
workflow state
```

---

# 6. Area Aplikasi

## 6.1 Landing dan Public Area

Boundary:

```text
Controller:
app/Controllers/Landing/

View:
app/Views/landing/

Layout:
app/Views/landing/layouts/public.php

Asset:
public/assets/main/
```

Landing/Public adalah area untuk pengunjung publik dan content yang benar-benar:

```text
published
public
```

Menu publik meliputi:

```text
Beranda
Tentang
Kontak
Privasi
Syarat dan Ketentuan
Aksesibilitas

Arena Belajar
Bunga Rampai
Regulasi PBJ
Berita
Event
Statistik Publik
Verifikasi Sertifikat
```

Landing/Public tidak boleh membocorkan:

```text
Data internal
Data restricted
Data draft
Data in_review
Data revision_required
Data profile pengguna
Data peserta
Nilai assessment
Submission assignment
Dokumen private
Audit log
Data JF PPBJ individual
NIP/NIK/email/phone
```

---

## 6.2 Auth Terpadu

Boundary:

```text
Controller:
app/Controllers/Dashboard/Auth/

View:
app/Views/dashboard/auth/

Layout:
app/Views/dashboard/layouts/auth.php

Asset:
public/assets/dashboard/
```

Auth dipakai oleh semua jenis user:

```text
Public User
Participant
Trainer
Evaluator
Operator
Admin
Super Admin
```

Tidak ada login terpisah untuk Admin, Participant, Trainer, atau role lain.

Prefix `/dashboard` pada Auth hanya merupakan lokasi route/UI WowDash.

Prefix itu bukan tanda bahwa Auth hanya boleh digunakan Admin.

Auth menu/fitur:

```text
Login
Logout
Registrasi multi-step
Status akun
Perbaikan registrasi
Forgot Password
Reset Password
```

---

## 6.3 Workspace

Boundary:

```text
Controller:
app/Controllers/Workspace/

View:
app/Views/main/workspace/

Layout:
app/Views/main/layouts/workspace.php

Asset:
public/assets/main/
```

Workspace adalah area pengguna aktif non-back-office.

Role utama:

```text
participant
trainer
evaluator
operator
```

Workspace berisi:

```text
Home Workspace
Profile
Perubahan Profil
Peran Pengadaan
Pengajuan Role
Bookmark
Notifikasi

Participant Learning
Trainer Class Management
Evaluator Grading
Operator Delegated Operation
```

Workspace memerlukan:

```text
auth
account_status active
workspaceAccess
role yang sesuai
ownership/enrollment/assignment/delegated scope
permission jika diperlukan
workflow state yang valid
```

---

## 6.4 Dashboard Back-office

Boundary:

```text
Controller:
app/Controllers/Dashboard/

View:
app/Views/dashboard/

Layout:
app/Views/dashboard/layouts/dashboard.php

Asset:
public/assets/dashboard/
```

Dashboard Back-office digunakan oleh:

```text
admin
super_admin
role lain yang memiliki dashboard permission eksplisit
```

Dashboard Back-office mencakup:

```text
Dashboard KPI
Manajemen Akses
Approval
Audit
Organisasi
Arena Belajar
Bunga Rampai
Regulasi PBJ
Berita dan Event
Sertifikat
Government Intelligence
Laporan
Pengaturan
System Health
```

Akses Dashboard memerlukan:

```text
auth
account_status active
dashboardAccess
permission granular
scope bila diperlukan
auditContext untuk aksi sensitif
```

Role `admin` atau `super_admin` tidak cukup bila permission granular tidak ada.

---

# 7. Route Contract

Route aktif harus diperiksa melalui:

```bash
php spark routes
```

File utama:

```text
app/Config/Routes.php
```

## Prinsip Route

```text
GET/HEAD tidak mengubah state
POST/PUT/PATCH/DELETE digunakan untuk perubahan state
Browser state-changing request memakai CSRF
Route memakai URI Bahasa Indonesia
Route name memakai English dot notation
Route aktif adalah pedoman controller/view/link/form
```

## Prefix Route Name

| Area | Prefix |
|---|---|
| Landing/Public | `landing.*` |
| Auth | `dashboard.auth.*` |
| Workspace | `workspace.*` |
| Dashboard Back-office | `dashboard.*` |
| Shared File | `shared.file.*` |
| Shared Export | `shared.export.*` |

## Aturan Perbaikan Legacy

Jika ditemukan:

```text
href hard-coded lama
site_url lama
route name lama
redirect lama
form action lama
controller legacy
namespace legacy
```

Maka:

```text
Perbaiki kode lama mengikuti route aktif.
```

Jangan:

```text
Menambah duplicate route
Menambah alias hanya untuk menjaga path lama
Mengubah route aktif agar cocok dengan view lama
Membuat route baru tanpa kebutuhan nyata
```

---

# 8. Authentication

## Login

Route target:

```text
GET  /dashboard/masuk
POST /dashboard/masuk
POST /dashboard/keluar
```

Controller produksi:

```text
app/Controllers/Dashboard/Auth/LoginController.php
app/Controllers/Dashboard/Auth/LogoutController.php
```

Service target:

```text
app/Services/Identity/AuthenticationService.php
```

Flow login:

```text
User
↓
/dashboard/masuk
↓
LoginController
↓
AuthenticationService
↓
User lookup
↓
Password verification
↓
Account status validation
↓
Role + permission + access context evaluation
↓
Session regeneration
↓
Redirect aman ke Dashboard / Workspace / Status Account
```

## Aturan Login

- Login memakai identifier yang didukung project, misalnya email atau username.
- Error login harus generik.
- Jangan membocorkan apakah akun/email/username ditemukan.
- Password harus diverifikasi dari `password_hash`.
- Session ID harus diregenerasi setelah login sukses.
- Logout wajib POST.
- Logout wajib invalidasi session.
- Return URL/intended URL wajib divalidasi untuk mencegah open redirect.
- User non-active tidak boleh masuk Workspace/Dashboard umum.

---

# 9. Registrasi

## Kebijakan Aktivasi Saat Ini

BegawiPBJ saat ini tidak memakai email verification berbasis token.

Jangan implementasikan:

```text
EmailVerificationController
email verification token
email verification link
email verification resend endpoint
email_verifications
verifiedEmail filter sebagai gate akses
```

Kolom `users.email_verified_at` boleh tetap ada karena schema compatibility, tetapi tidak digunakan sebagai syarat account active.

## Flow Registrasi Final

```text
1. Akun dasar + pemilihan tipe profil
2. Wilayah/domisili + alamat ringkas
3. Profil spesifik berdasarkan tipe
4. Review data + persetujuan syarat/kebijakan privasi
5. Submit registrasi
6. Admin/Operator melakukan verifikasi profil manual
7. Keputusan:
   - approve
   - revision_required
   - reject
8. Jika approve:
   account_status = active
9. Sistem memberikan role default sesuai policy
10. User dapat login
11. Akses akhir ditentukan oleh role, permission, ownership, assignment, visibility, dan workflow state
```

Ringkasan:

```text
Submit
↓
Profile verification by Admin/Operator
↓
Approval
↓
Account active
↓
Login
```

## Route Registrasi

```text
GET/POST /dashboard/daftar
GET/POST /dashboard/daftar/wilayah
GET/POST /dashboard/daftar/profil/{tipe}
GET/POST /dashboard/daftar/tinjau
GET      /dashboard/daftar/berhasil
GET      /dashboard/status-akun
```

## Controller Produksi

```text
app/Controllers/Dashboard/Auth/RegisterController.php
app/Controllers/Dashboard/Auth/RegisterProfileController.php
app/Controllers/Dashboard/Auth/AccountStatusController.php
```

## Service Target

```text
app/Services/Identity/RegistrationService.php
app/Services/Identity/ProfileVerificationService.php
app/Services/Identity/RoleAssignmentService.php
```

## State Registrasi

| Status | Fungsi |
|---|---|
| `draft` | Data registrasi belum lengkap |
| `submitted` | Registrasi selesai dikirim |
| `pending_verification` | Menunggu review profil Admin/Operator |
| `revision_required` | User perlu memperbaiki data |
| `active` | Akun disetujui dan dapat mengakses layanan sesuai role |
| `rejected` | Registrasi ditolak |
| `inactive` | Akun tidak aktif |
| `suspended` | Akun ditangguhkan |
| `archived` | Record arsip |

## Validasi Registrasi

Akun dasar:

```text
name
email
username
password
password confirmation
phone
profile_type
consent terms
consent privacy
```

Aturan password:

```text
Minimal 12 karakter
Memiliki huruf besar
Memiliki huruf kecil
Memiliki angka
Memiliki simbol
```

Profile type:

```text
asn
provider
academic
public
```

Jangan menerima profile type bebas dari request atau route.

---

# 10. Wilayah dan Alamat

## Master Wilayah

Tabel master:

```text
wilayah
```

Kolom utama:

```text
kode VARCHAR(13) PRIMARY KEY
nama VARCHAR(100)
```

Data wilayah diimport manual dari:

```text
database/reference/wilayah.sql
```

Master wilayah bersifat:

```text
read-only
```

Registrasi tidak boleh menambah, mengubah, atau menghapus wilayah.

## Data Wilayah pada User Profile

Data disimpan sebagai kode:

```text
province_code
regency_code
district_code
village_code
address_line
```

## Aturan Tampilan

Database menyimpan kode.

UI review/detail menampilkan nama.

Contoh:

```text
province_code: 18
province_name: Lampung

regency_code: 18.71
regency_name: nama kabupaten/kota dari wilayah.nama
```

Dilarang:

```text
Hard-code kode → nama di view
Menyimpan nama wilayah pada kolom kode
Menampilkan kode mentah ketika user membutuhkan nama
Mengubah master wilayah dari flow registrasi
```

## State Multi-step Registration

Data wilayah harus bertahan saat user:

```text
Klik Back
Kembali dari review
Gagal validasi
Melanjutkan kembali form
```

Gunakan state persisten yang aman:

```text
Server-side session registration draft
Atau draft database jika memang sudah diterapkan
```

Jangan memakai flashdata sebagai satu-satunya state karena hanya bertahan singkat.

Prioritas nilai pada form wilayah:

```text
1. POST input yang baru gagal validasi
2. Draft/session registration state
3. Default kosong
```

Aturan dropdown dependent:

```text
Provinsi dipilih
↓
Kabupaten/Kota dimuat sesuai provinsi
↓
Kecamatan dimuat sesuai kabupaten/kota
↓
Desa/Kelurahan dimuat sesuai kecamatan
```

Jika parent tidak berubah, child selection tidak boleh reset.

Child dropdown hanya di-reset jika user benar-benar mengubah parent.

---

# 11. Profile Type

## ASN

Table:

```text
asn_profiles
```

Contoh data:

```text
employee_identifier
employment_status
origin_type
organization_unit_id
institution_name
unit_name
position_name
rank_grade
official_email
```

`origin_type`:

```text
pemprov_lampung
kementerian_lembaga
pemerintah_daerah_lain
pemerintah_desa
bumn_bumd
lainnya
```

## Provider

Table:

```text
provider_profiles
```

Contoh data:

```text
business_name
business_form
business_field
business_identifier
registrant_position
responsible_person
business_email
business_address
```

## Academic

Table:

```text
academic_profiles
```

Contoh data:

```text
institution_name
institution_type
role_name
expertise
institution_email
professional_identifier
portfolio_url
```

## Public

Table:

```text
public_profiles
```

Contoh data:

```text
national_identifier
occupation_status
organization_name
interest_areas
usage_purpose
```

Profile type bukan role aplikasi.

Profile type tidak boleh otomatis memberi:

```text
operator
admin
super_admin
```

---

# 12. Profile Verification

## Table

```text
profile_verifications
```

## Status

```text
pending
in_review
approved
revision_required
rejected
```

## Admin/Operator Verification Flow

```text
User submits registration
↓
profile_verifications = pending
↓
Admin/Operator opens verification queue
↓
Admin/Operator reviews profile
↓
Decision:
approve / revision_required / reject
↓
Workflow service updates:
profile_verifications
users.account_status
audit_logs
notifications
role default when active
```

## Rules

- Approval requires permission and scope.
- Revision/reject require reason.
- Approval must use transaction if multiple tables change.
- User account becomes `active` only through valid approval.
- Restricted profile evidence must be private/restricted.
- Do not update `users.account_status` directly from controller without service/workflow.
- Do not expose full restricted data unnecessarily in queue list.

---

# 13. Profile Change Request

## Table

```text
profile_change_requests
```

## Status

```text
draft
submitted
in_review
revision_required
approved
rejected
cancelled
archived
```

## Use Case

Gunakan profile change request untuk perubahan data yang bersifat:

```text
verified
restricted
organization-related
employment-related
profile type-related
procurement role-related
identity-related
```

## Rules

```text
User submits request
↓
Admin/Operator reviews
↓
Approve:
apply allowed payload atomically
↓
Revision/reject:
store reason
do not change master profile
```

Dilarang:

```text
Mass assign raw requested_payload ke user/profile
Menyimpan full restricted payload di audit log
Menerapkan perubahan sebelum approval
```

---

# 14. Role, Permission, dan Akses

## Role Sistem

```text
public_user
participant
trainer
evaluator
operator
admin
super_admin
```

## Permission Pattern

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
permission.assign
organization.view
organization.create
organization.update
learning.program.create
learning.class.update
learning.lesson.manage
learning.assignment.grade
knowledge.article.create
knowledge.publish.review
regulation.create
regulation.publish.review
news.create
event.attendance.manage
certificate.issue
intelligence.export
file.upload
file.download
export.download
audit.view
```

## Effective Access

```text
Authentication
+ Account Status
+ Role
+ Permission
+ Ownership / Enrollment / Assignment / Delegated Scope
+ Visibility
+ Workflow State
```

Contoh:

```text
Trainer role
≠ dapat mengubah semua kelas

Trainer role
+ assignment pada class
+ permission yang sesuai
= dapat mengelola class tersebut
```

---

# 15. Role Request

## Table

```text
role_requests
```

## User Can Request

```text
trainer
evaluator
```

## User Cannot Self-Request

```text
operator
admin
super_admin
```

## Flow

```text
draft
↓
submitted
↓
in_review
↓
approved / revision_required / rejected
↓
archived
```

## Approval

```text
role_requests approved
↓
RoleAssignmentService
↓
create/update user_roles
↓
status active
granted_by
granted_at
reason
audit_logs
notification
```

Approval role request tidak otomatis membuat:

```text
class_trainers
class_evaluators
```

Trainer/Evaluator tetap butuh class assignment untuk mengakses class tertentu.

---

# 16. Peran Pengadaan

Peran Pengadaan bukan role RBAC.

Table:

```text
user_procurement_roles
```

Contoh peran:

```text
PA
KPA
PPK
PP
POKJA_PEMILIHAN
PENGELOLA_PENGADAAN
PPTK
PEJABAT_PENERIMA_HASIL
TIM_TEKNIS
TIM_PENDUKUNG
PENGAWAS_KONTRAK
APIP
LAINNYA
```

Status deklarasi dapat mencakup:

```text
self_declared
pending
verified
rejected
```

Peran pengadaan:

```text
Tidak memberikan permission aplikasi otomatis
Tidak memberikan akses Dashboard otomatis
Tidak sama dengan role trainer/admin/operator
```

---

# 17. Manajemen Akses Dashboard

Menu utama:

```text
Dashboard
→ Manajemen Akses
```

Menu terkait:

```text
Pengguna
Verifikasi Profil
Perubahan Profil
Role dan Permission
Permohonan Role
Unit Organisasi
Peran Pengadaan
```

## Pengguna

Data utama:

```text
users
user_profiles
profile-specific tables
user_roles
```

Kemampuan:

```text
List user
View detail user
Create user jika route/workflow mendukung
Edit data administratif yang diizinkan
Ubah status account melalui workflow
Lihat role dan profile summary
```

Gunakan status/soft delete, bukan physical delete, bila user telah memiliki relasi sejarah.

## Verifikasi Profil

Data utama:

```text
profile_verifications
users
user_profiles
profile specific tables
file_assets
```

Kemampuan:

```text
List pending review
View profile detail
Approve
Request revision
Reject
```

## Perubahan Profil

Data utama:

```text
profile_change_requests
users
user_profiles
profile specific tables
```

Kemampuan:

```text
List request
View old/new value
Approve
Request revision
Reject
```

## Role dan Permission

Data utama:

```text
roles
permissions
role_permissions
user_roles
```

Kemampuan:

```text
List role
View role permission
Create/edit role jika didukung
Assign/remove permission
Protect system roles
Audit changes
```

## Permohonan Role

Data utama:

```text
role_requests
user_roles
roles
users
```

Kemampuan:

```text
List request
View request detail
Approve
Request revision
Reject
Grant role transactionally
Audit decision
```

## Unit Organisasi

Table:

```text
organization_units
```

Fields:

```text
id
parent_id
code
name
type
is_active
sort_order
created_at
updated_at
```

Gunakan `type`, bukan `unit_type`.

Rules:

```text
code unique
parent valid
no self-parent
no circular hierarchy
prefer deactivate rather than physical delete
```

## Peran Pengadaan

Kelola domain procurement role terpisah dari RBAC.

Jangan memakai tabel `roles` untuk peran pengadaan kecuali schema existing memang secara eksplisit menyatakannya.

---

# 18. Arena Belajar

## Hierarki

```text
Program
↓
Course
↓
Learning Class
↓
Learning Module
↓
Lesson
```

## Data Utama

```text
programs
courses
learning_classes
class_trainers
class_evaluators
learning_modules
lessons
enrollments
pre_tests
quizzes
quiz_questions
quiz_options
quiz_attempts
quiz_answers
assignments
assignment_submissions
assignment_submission_files
learning_progress
feedbacks
```

## Public Arena Belajar

Publik dapat melihat:

```text
Program published/public
Course published/public
Instructor profile yang diizinkan
```

Publik tidak boleh melihat:

```text
Internal class
Enrollment user
Quiz answers
Assessment score
Assignment submissions
Internal lesson
Restricted file
```

## Participant

Fitur:

```text
Enrollment list
Class learning detail
Lesson access
Progress
Pre-test
Quiz
Post-test
Assignment submission
Feedback
Personal certificate
```

Akses harus memeriksa:

```text
active account
participant role
valid enrollment
lesson/class access
attempt limit
assignment deadline
ownership
workflow state
```

## Trainer

Fitur:

```text
Assigned class list
Class detail
Lesson management
Quiz management
Assignment management
Participant progress
```

Trainer hanya dapat mengelola class yang ditugaskan.

## Evaluator

Fitur:

```text
Grading queue
Submission detail
Score
Feedback
Revision request
Grading history
```

Evaluator hanya dapat menilai submission yang berada dalam assignment scope-nya.

## Constraints

```text
quiz_weight + assignment_weight + post_test_weight = 100.00
post_test_weight = 60.00
passing_grade = 0..100
```

Quiz type:

```text
pre_test
lesson_quiz
post_test
```

Final score dihitung server-side.

Jangan menghitung atau menetapkan kelulusan hanya dari browser.

---

# 19. Bunga Rampai

Bunga Rampai adalah knowledge center PBJ.

## Content Type

```text
article
faq
case_study
template
```

## Data Utama

```text
knowledge_categories
knowledge_tags
knowledge_articles
knowledge_article_categories
knowledge_article_tags
knowledge_media
```

## Workflow

```text
draft
submitted
in_review
approved
published
revision_required
rejected
archived
```

## Visibility

```text
public
internal
restricted
```

## Fitur

```text
Kategori
Tag
Artikel
FAQ
Studi Kasus
Template
Media
Gallery
Video
YouTube
External Link
Featured Content
Carousel
Public Search
```

Public hanya melihat:

```text
workflow_status = published
visibility = public
```

---

# 20. Regulasi PBJ

## Data Utama

```text
regulation_categories
regulations
regulation_versions
regulation_relations
regulation_downloads
```

## Legal Status

```text
active
amended
revoked
expired
```

## Workflow Status

```text
draft
submitted
in_review
approved
published
revision_required
rejected
archived
```

## Fitur

```text
Kategori regulasi
Metadata regulasi
Nomor dokumen
Tahun dokumen
Penerbit
Tanggal resmi
Tanggal berlaku
Summary
Official URL
Versioning
Current version
Changelog
Legal relation
Preview file
Download file
Bookmark
Search
```

## Versioning Flow

```text
Create regulation
↓
current_version_id = NULL
↓
Create regulation version
↓
Mark one version is_current = 1
↓
Update regulations.current_version_id
```

Gunakan transaction bila perubahan berkaitan.

## Regulation Relation

Contoh:

```text
amends
amended_by
revokes
revoked_by
replaces
replaced_by
implements
implemented_by
references
referenced_by
```

Tidak boleh ada:

```text
self relation
duplicate relation type
```

---

# 21. Berita, Agenda, dan Event

## News

Data:

```text
news_categories
news
```

Fitur:

```text
Category
Draft
Review
Approval
Publish
Schedule
Archive
Public list
Public detail
```

## Event

Data:

```text
events
event_registrations
attendances
```

Mode:

```text
online
offline
hybrid
```

Status:

```text
draft
coming_soon
open_registration
full
finished
cancelled
```

Registration status:

```text
registered
waitlisted
cancelled
rejected
```

Attendance status:

```text
present
absent
excused
```

## Event Rules

```text
end_at >= start_at
capacity tidak boleh terlampaui
waitlist promotion harus transactional
attendance harus sesuai registration
event certificate eligibility harus dihitung server-side
```

---

# 22. Sertifikat Digital

## Data Utama

```text
certificate_templates
certificates
file_assets
```

## Source Type

```text
learning_enrollment
event_registration
```

## Certificate Status

```text
pending
issued
revoked
expired
```

## Fitur

```text
Template certificate
Background certificate
Eligibility evaluation
Issue certificate
Certificate number
Verification token hash
QR verification
PDF output
Public verification
Revoke
Replacement
Download protected PDF
```

## Rules

```text
Satu certificate per source_type + source_id
Token disimpan sebagai hash
Issue/revoke/replacement memakai transaction
PDF mengikuti file storage policy
Public verification hanya tampilkan informasi minimum aman
```

---

# 23. Government Intelligence PBJ

Government Intelligence adalah area internal untuk analisis SDM PBJ dan kompetensi.

## Data Utama

```text
competencies
jf_ppbj_levels
competency_certificate_types
jf_ppbj_records
competency_certificates
competency_gap_snapshots
organization_units
```

## Fitur

```text
Organization intelligence
JF PPBJ records
Competency certificate verification
Competency gap
Internal dashboard
Internal report
Restricted export
```

## Gap Formula

```text
gap_count = required_count - available_count
```

## Privacy

Data individual seperti:

```text
NIP
nama individu
dokumen bukti
email
phone
identifier
```

Tidak boleh muncul pada Statistik Publik.

---

# 24. Statistik Publik

Statistik publik hanya menggunakan data agregat.

Tidak boleh menampilkan:

```text
NIP
NIK
nama individu
email
nomor telepon
dokumen evidence
record personal
payload restricted
```

Fitur:

```text
Public KPI
Chart agregat
Statistik kompetensi agregat
Statistik pembelajaran agregat
Statistik event agregat
Metadata methodology bila tersedia
```

Harus menerapkan:

```text
Aggregation
Threshold
Suppression
Response sanitization
Rate limit
```

---

# 25. File Storage

## file_assets

File metadata mencakup:

```text
disk
path
original_name
stored_name
extension
mime_type
size_bytes
checksum
visibility
lifecycle_status
resource_type
resource_id
uploaded_by
available_at
expires_at
metadata
```

## Visibility

```text
public
internal
restricted
```

## Lifecycle

```text
pending_upload
stored
scanned
available
rejected
quarantined
deleted
```

## Rules

- Database asset record tidak otomatis membuat file fisik.
- File restricted tidak boleh disimpan di public directory.
- File access harus divalidasi server-side.
- File URL bukan authorization.
- Upload harus memvalidasi MIME, extension, size, ownership/context, dan policy domain.
- Quarantined/rejected/deleted file tidak dapat diakses normal.

---

# 26. Export

Data:

```text
exports
file_assets
```

Status:

```text
queued
generating
ready
failed
expired
deleted
```

Rules:

```text
Export harus permission/scoped
File export private
Download harus authorized
Export dapat expired
Access/export harus diaudit jika restricted
```

---

# 27. Audit dan Notification

## Audit

Data:

```text
audit_logs
```

Audit harus mencatat action penting:

```text
Login success/failure
Logout
Registration submit
Account status transition
Profile verification decision
Profile change request decision
Role request decision
Role grant/revoke
Role-permission change
Publication decision
Certificate issue/revoke
Restricted file access
Export creation/download
Security/configuration change
```

Audit tidak boleh menyimpan:

```text
password
password_hash
plain reset token
plain verification token
session ID
secret
credential
full restricted payload
```

## Notification

Data:

```text
notifications
```

Notifikasi dapat digunakan untuk:

```text
Registration submitted
Revision required
Profile approved/rejected
Account active
Role request status
Profile change request result
Event registration status
Certificate issued
Export ready
```

Payload notifikasi harus aman dan tidak memuat data restricted berlebihan.

---

# 28. Security Rules

Seluruh fitur harus mengikuti:

```text
Server-side validation
Server-side authorization
Least privilege
CSRF protection
Rate limit
Input allowlist
Output escaping
Safe redirect
Session security
Transaction untuk multi-table update
Auditability
Privacy and data minimization
```

## Tidak Cukup

Hal berikut bukan authorization:

```text
Menyembunyikan menu
Menyembunyikan tombol
Menyembunyikan link
Mengandalkan URL tidak diketahui
Mengandalkan hidden input
Mengandalkan JavaScript validation
```

## Authorization Context

Sebelum action sensitif, cek:

```text
Authenticated?
Account active?
Role valid?
Permission valid?
Ownership?
Enrollment?
Trainer/Evaluator assignment?
Operator delegated scope?
Visibility?
Workflow state?
```

---

# 29. Legacy Cleanup Strategy

Project memiliki indikasi legacy duplicate implementation.

Jangan langsung menghapus.

## Audit Legacy

Cari:

```text
app/Controllers/Auth/
app/Controllers/Admin/
app/Controllers/Admin/AdminAuthController.php
controller login lama
controller register lama
service auth lama
route login/register lama
view auth lama
session key lama
redirect lama
```

## Target Final

Auth production hanya boleh berada pada:

```text
app/Controllers/Dashboard/Auth/
app/Views/dashboard/auth/
app/Views/dashboard/layouts/auth.php
public/assets/dashboard/
```

Path yang tidak boleh menjadi production boundary baru:

```text
app/Controllers/Auth/
app/Controllers/Admin/
app/Controllers/Admin/AdminAuthController.php
app/Views/auth/
app/Views/workspace/
app/Views/main/layouts/auth.php
public/assets/workspace/
```

## Migration Rule

Jika legacy masih dibutuhkan sementara:

```text
Gunakan adapter kecil
Redirect/route compatibility seperlunya
Catat status Deprecated
Jangan membuat auth kedua
Jangan menduplikasi session contract
Jangan menduplikasi registration persistence
```

---

# 30. Implementation Workflow untuk Claude Code

## Sebelum Mengubah Kode

Wajib lakukan urutan berikut:

```text
1. Baca dokumen context ini
2. Baca dokumen domain yang relevan
3. Baca app/Config/Routes.php
4. Jalankan php spark routes
5. Identifikasi controller target
6. Identifikasi view target
7. Identifikasi service/workflow
8. Identifikasi model/table/schema
9. Cari legacy duplicate yang langsung terkait
10. Tentukan root cause
11. Tentukan perubahan minimal
```

## Trace Dependency

Gunakan alur:

```text
Route
↓
Filter
↓
Controller
↓
Service / Workflow
↓
Model / Query
↓
Database schema
↓
View
↓
JavaScript jika flow bergantung padanya
```

## Aturan Perubahan

- Preserve feature yang sudah bekerja.
- Ubah file sesedikit mungkin.
- Jangan refactor besar untuk memperbaiki bug kecil.
- Jangan membuat table/schema baru tanpa alasan terbukti.
- Jangan membuat route baru bila route existing dapat digunakan.
- Jangan membuat view baru sebelum memastikan view existing tidak dapat dipakai.
- Jangan mengubah layout/asset boundary.
- Jangan membuat controller baru sebelum mengecek controller existing yang relevan.
- Jangan mengubah workflow status langsung dari controller.
- Jangan menghapus legacy file sebelum dependency sudah diketahui.
- Jangan menyatakan success tanpa validasi route/action yang benar-benar dijalankan.

## Validasi Minimum

```bash
php spark routes
php spark cache:clear
```

Kemudian validasi hanya flow yang terdampak.

Contoh:

```text
Login:
GET login
POST login valid
POST login invalid
Redirect active user
Redirect non-active user
Logout

Registration:
Account step
Region step
Profile step
Review
Submit
Profile verification queue
Approve/revision/reject
Account active
Login setelah active
```

---

# 31. Current Development Priority

Urutan pengerjaan saat ini:

```text
1. Stabilkan Auth production boundary
2. Hapus/isolasi conflict legacy Login/Register
3. Perbaiki multi-step Registration
4. Perbaiki state wilayah/alamat
5. Perbaiki review nama wilayah dari kode
6. Implement manual profile approval
7. Buat akun dummy Admin dan Super Admin untuk testing
8. Pastikan Dashboard Access dan RBAC dasar
9. Pastikan GET route UI draft dapat dirender
10. Implement backend domain bertahap
```

## Jangan Memulai dari

```text
Full CRUD semua modul
Full analytics
Full export
Full file upload
Full certificate PDF
Full learning assessment
Full public statistics
Refactor besar seluruh project
```

Sampai Auth, registration, approval, session, RBAC dasar, route alignment, dan dashboard access stabil.

---

# 32. Expected Claude Code Output

Saat diminta memperbaiki atau mengimplementasikan fitur, Claude Code harus memberikan output ringkas:

```text
1. Root cause/current condition
2. Files affected
3. Minimal change plan
4. Changes made
5. Route/action validated
6. Test/manual validation performed
7. Remaining risk/blocker
```

Jangan memberi klaim “selesai” bila:

```text
Route belum diuji
Controller belum terpanggil
View belum dirender
Form belum tersimpan
Transaction belum terverifikasi
Authorization belum diperiksa
Workflow status belum valid
```

---

# 33. Common Mistakes yang Dilarang

Dilarang:

```text
Menganggap project Laravel
Menggunakan php artisan
Membuat route di routes/web.php
Memakai resources/views
Menggunakan FiStudy pada Dashboard/Auth
Menggunakan WowDash pada Landing/Workspace
Merender app/Views/reference di production
Menganggap profile_type = role
Menganggap procurement role = RBAC role
Menganggap account active = akses semua
Mengandalkan hidden menu sebagai security
Menggunakan GET untuk update/delete/approval
Membuat auth system kedua
Membuat registration system kedua
Mengubah route agar mengikuti view/controller lama
Menyimpan nama wilayah ke kolom kode
Menampilkan kode wilayah mentah pada review user
Menggunakan flashdata sebagai satu-satunya state multi-step
Menghapus user/unit organisasi secara fisik tanpa cek relasi
Menyimpan password/token/session pada log/audit
Membuat full backend untuk modul di luar scope bug
Menganggap draft UI sebagai fitur selesai
```

---

# 34. Final Principle

BegawiPBJ harus berkembang secara bertahap dan konsisten:

```text
UI boundary benar
↓
Route contract benar
↓
Auth dan registration stabil
↓
Manual approval berjalan
↓
RBAC dasar berjalan
↓
GET UI route dapat diakses
↓
Backend domain dibangun bertahap
↓
Workflow, audit, file policy, privacy, dan test ditambahkan
↓
Production readiness
```

Selalu utamakan:

```text
Correct boundary
Correct route
Correct authorization
Correct workflow
Correct data handling
Minimal safe change
```

## Database Schema Safety

Before querying a table column:
- inspect actual database schema
- inspect migration/schema documentation
- do not assume column existence from code

If query fails because of missing column:
- identify whether code or schema is wrong
- do not blindly add columns
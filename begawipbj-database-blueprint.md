# BegawiPBJ Database Blueprint

## Blueprint Database Platform Ekosistem Digital PBJ Pemerintah Provinsi Lampung

Dokumen ini menetapkan rancangan database resmi untuk **BegawiPBJ**, platform digital terintegrasi Pengadaan Barang/Jasa Pemerintah (PBJ) di lingkungan Pemerintah Provinsi Lampung.

BegawiPBJ mengintegrasikan pembelajaran digital, Bunga Rampai, regulasi, berita dan event, sertifikat digital, pengelolaan kompetensi SDM, serta Government Intelligence PBJ. Database dirancang sebagai fondasi yang aman, konsisten, dapat diaudit, menjaga privasi, dan dapat berkembang tanpa mengorbankan integritas data.

Dokumen ini menjadi acuan untuk:

- Entity Relationship Diagram (ERD).
- Migration CodeIgniter 4.
- Model, service, validation, dan transaction boundary aplikasi.
- Seeder data referensi, import master resmi, dan data development terpisah.
- Standar integritas, keamanan, audit, retensi, dan privasi data.
- Pengembangan laporan, statistik publik, dan Government Intelligence PBJ.

Dokumen ini konsisten dengan `README.md`, `begawipbj-directory-architecture.md`, dan `assessment-and-certification-rules.md`.

---

# 1. Tujuan Database

Database BegawiPBJ dirancang untuk mendukung tujuan berikut:

1. Menyimpan identitas pengguna, profil, organisasi, role, dan permission dengan aman.
2. Mendukung Learning Management System PBJ dari program sampai sertifikat, termasuk pre-test, quiz, assignment, post-test, grading, kelulusan, feedback, dan eligibility sertifikat.
3. Menjadi pusat konten Bunga Rampai untuk artikel, FAQ, studi kasus, template, kategori, tag, dan bookmark.
4. Mengelola regulasi PBJ beserta metadata, versi dokumen, status hukum, tahapan PBJ, kompetensi terkait, kata kunci, dan jejak download.
5. Mendukung berita, agenda, event, registrasi, waitlist, absensi, dan sertifikat kegiatan.
6. Menghasilkan sertifikat digital dengan nomor unik, token verifikasi, QR code, status, expiry, dan riwayat revocation.
7. Menyimpan data Government Intelligence PBJ tingkat Pemerintah Provinsi Lampung: OPD, JF PPBJ, sertifikat kompetensi, serta gap kompetensi agregat.
8. Menyediakan approval workflow, audit log, notifikasi, dan metadata file lintas domain.
9. Menjaga kerahasiaan data pribadi ASN dan pengguna, terutama pada statistik serta halaman publik.

---

# 2. Ruang Lingkup Domain

```text
Identity dan RBAC
├── User dan profil kategori
├── Role dan permission
├── Organisasi/OPD
└── Master wilayah

Arena Belajar
├── Program dan course
├── Kelas, trainer, evaluator, dan enrollment
├── Modul, lesson, dan materi
├── Konfigurasi assessment
├── Pre-test satu kali
├── Quiz per lesson dan best attempt
├── Assignment, revisi, upload ulang, dan manual grading
├── Post-test dan best attempt
├── Progress, feedback, final grade, completion
└── Certificate eligibility

Bunga Rampai
├── Kategori dan tag
├── Artikel
├── FAQ
├── Studi kasus
├── Template dokumen
└── Bookmark

Regulasi PBJ
├── Kategori regulasi
├── Regulasi dan versi dokumen
├── Tahapan PBJ
├── Kompetensi terkait
├── Kata kunci
└── Log download

Komunikasi
├── Kategori berita
├── Berita
├── Event dan agenda
├── Registrasi, waitlist, dan pembatalan
└── Absensi event

Sertifikat Digital
├── Template sertifikat
├── Sertifikat
├── Nomor sequence
├── Log verifikasi
├── Revocation
└── Sertifikat event melalui sumber registrasi

Government Intelligence PBJ
├── Jenjang JF PPBJ
├── Personel JF PPBJ
├── Riwayat JF PPBJ
├── Jenis sertifikat kompetensi
├── Pemilik sertifikat kompetensi
└── Snapshot gap kompetensi

Shared Platform Services
├── Approval workflow
├── Audit log
├── Notifikasi
└── File asset metadata
```

---

# 3. Standar Teknis

## 3.1 Platform database

| Aspek | Standar |
|---|---|
| DBMS | MySQL 8.4+ atau versi kompatibel yang mendukung InnoDB, `CHECK`, `JSON`, generated column bila diperlukan, dan index modern |
| Storage engine | InnoDB |
| Character set | `utf8mb4` |
| Collation | `utf8mb4_0900_ai_ci` atau collation Unicode konsisten yang tersedia pada server target |
| Zona waktu penyimpanan | UTC |
| Primary key aplikasi | `BIGINT UNSIGNED AUTO_INCREMENT` kecuali master berkode bisnis eksplisit |
| Foreign key aplikasi | `BIGINT UNSIGNED` dengan unsigned yang konsisten |
| Master wilayah | `VARCHAR(13)` untuk `wilayah.kode` |
| Nama tabel | plural `snake_case` |
| Nama kolom | `snake_case` |
| Soft delete | `deleted_at` hanya pada entitas yang memerlukan pemulihan/arsip aplikasi |
| File fisik | `writable/uploads/` atau private object storage yang dikelola aplikasi |
| Metadata file | `file_assets` |
| Migration | CodeIgniter 4 migration; perubahan schema selalu melalui migration baru |

## 3.2 Aturan penamaan

```text
Tabel               : users, learning_classes, regulation_versions
Primary key         : id
Foreign key         : user_id, course_id, file_asset_id
Kode sistem         : code
Slug URL publik     : slug
Status proses       : status / workflow_status
Waktu dibuat        : created_at
Waktu diubah        : updated_at
Soft delete         : deleted_at
```

Nama tabel harus menjelaskan domain. Hindari nama generik yang ambigu:

```text
categories
classes
modules
templates
progress
answers
results
```

Gunakan nama spesifik:

```text
knowledge_categories
learning_classes
learning_modules
document_templates
learning_progress
quiz_answers
final_grades
```

## 3.3 Tipe data umum

| Kebutuhan | Tipe rekomendasi |
|---|---|
| Identifier aplikasi | `BIGINT UNSIGNED` |
| Kode bisnis pendek | `VARCHAR(50)` atau sesuai kebutuhan domain |
| Slug | `VARCHAR(191)` dengan unique index |
| Judul/nama | `VARCHAR(255)` |
| Konten panjang | `TEXT`, `MEDIUMTEXT`, atau `LONGTEXT` sesuai kebutuhan |
| Nilai 0–100 | `DECIMAL(5,2)` |
| Bobot persen | `DECIMAL(5,2)` |
| Jumlah/kuota | `INT UNSIGNED` |
| Ukuran file | `BIGINT UNSIGNED` |
| Waktu UTC | `DATETIME` |
| Data fleksibel terbatas | `JSON` |
| Status terbatas | `VARCHAR(50)` + validation aplikasi/check constraint bila layak |

## 3.4 Timestamp, timezone, dan audit

- Semua `DATETIME` disimpan dalam UTC.
- Aplikasi menampilkan waktu pada zona waktu pengguna atau default `Asia/Jakarta`.
- Tabel transaksional umumnya mempunyai `created_at` dan `updated_at`.
- Entitas yang perlu diarsipkan dapat menggunakan `deleted_at`; soft delete bukan pengganti audit log.
- Aksi bisnis sensitif dicatat pada `audit_logs`.
- Transisi workflow dicatat pada `approval_histories`.
- Penerbitan, pembatalan, verifikasi, expiry, dan regenerasi dokumen sertifikat wajib dapat ditelusuri.

---

# 4. Master Wilayah

## 4.1 Status master wilayah

Master wilayah adalah data resmi yang tersedia lebih dahulu sebelum seeder demo/development dijalankan. Master tersebut tidak diperlakukan sebagai dummy data.

Struktur minimal:

```sql
wilayah (
    kode VARCHAR(13) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    PRIMARY KEY (kode),
    KEY wilayah_name_idx (nama)
)
```

```text
wilayah(kode, nama)
```

Data wilayah resmi disimpan sebagai reference import pada:

```text
database/reference/wilayah.sql
```

Aturan wajib:

- Seeder development/dummy tidak boleh membuat, mengubah, menghapus, truncate, atau mengganti data tabel `wilayah`.
- Data yang merujuk wilayah hanya boleh menggunakan kode yang ada pada master resmi.
- Proses import master wilayah dipisahkan dari seed demo dan dijalankan terkontrol.
- Jika struktur tabel dikelola migration aplikasi, migration hanya membuat struktur; data resmi tetap berasal dari import/reference yang disetujui.

## 4.2 Standar kode wilayah

Kode administratif bersifat bertingkat.

```text
18              → Provinsi Lampung
18.01           → Kabupaten Lampung Selatan
18.01.04        → Kecamatan Natar
18.01.04.2001   → Desa/Kelurahan Hajimena
```

Level wilayah ditentukan dari format kode, bukan kolom level tambahan.

| Panjang kode | Tingkat | Contoh |
|---:|---|---|
| 2 | Provinsi | `18` |
| 5 | Kabupaten/Kota | `18.01` |
| 8 | Kecamatan | `18.01.04` |
| 13 | Desa/Kelurahan | `18.01.04.2001` |

## 4.3 Penggunaan wilayah

`user_profiles` dapat menyimpan kode wilayah secara opsional:

```text
province_code → wilayah.kode
regency_code  → wilayah.kode
district_code → wilayah.kode
village_code  → wilayah.kode
```

Foreign key menjamin kode tersedia dalam master. Konsistensi hierarki—misalnya kecamatan harus berada di bawah kabupaten/kota yang dipilih—divalidasi pada `WilayahService` dengan pola prefix kode.

Contoh query dropdown berjenjang:

```sql
-- Kabupaten/kota di Provinsi Lampung
SELECT kode, nama
FROM wilayah
WHERE kode LIKE '18.%'
  AND CHAR_LENGTH(kode) = 5
ORDER BY nama;

-- Kecamatan dalam Kabupaten Lampung Selatan
SELECT kode, nama
FROM wilayah
WHERE kode LIKE '18.01.%'
  AND CHAR_LENGTH(kode) = 8
ORDER BY nama;
```

---

# 5. Identity, Profil, dan RBAC

## 5.1 Tabel inti

```text
users
user_profiles
user_asn_profiles
user_provider_profiles
user_academic_profiles
user_public_profiles
roles
permissions
role_permissions
user_roles
password_reset_tokens
login_attempts
```

## 5.2 `users`

`users` menyimpan kredensial akun dan status akses; tabel ini tidak menyimpan NIP, NIK, data organisasi, atau profil rinci.

Kolom inti:

```text
id
username                         UNIQUE
email                            UNIQUE
password_hash
account_status
email_verified_at
last_login_at
created_at
updated_at
deleted_at
```

Status akun:

```text
pending
active
inactive
rejected
suspended
```

Ketentuan:

- `password_hash` wajib berasal dari `password_hash()` dan tidak pernah memuat plaintext password.
- Unique email/username harus mempertimbangkan strategi soft delete yang dipilih. Jika akun lama tidak boleh dipakai ulang, unique index biasa cukup. Jika boleh dipakai ulang setelah anonymization, prosedurnya harus eksplisit.
- Login dan pengubahan status akun wajib diaudit sesuai kebijakan.

## 5.3 `user_profiles` dan profil kategori

Setiap user memiliki satu profil umum pada `user_profiles`.

```text
user_id                           UNIQUE
profile_category
full_name
title_prefix
title_suffix
phone
avatar_file_asset_id
province_code
regency_code
district_code
village_code
created_at
updated_at
```

Kategori profil:

```text
asn
provider
academic_practitioner
public
```

Profil spesifik disimpan pada satu tabel kategori yang sesuai.

| Kategori | Tabel | Contoh data utama |
|---|---|---|
| ASN | `user_asn_profiles` | NIP, status kepegawaian, jabatan, pangkat, golongan, OPD aktif |
| Penyedia | `user_provider_profiles` | NIB, badan usaha, penanggung jawab |
| Akademisi/praktisi | `user_academic_profiles` | Institusi, bidang keahlian |
| Publik | `user_public_profiles` | Data tambahan minimal jika diperlukan |

Validasi service memastikan kategori umum sesuai dengan satu profil spesifik yang valid. Informasi personal sensitif hanya dipilih pada query internal berizin.

## 5.4 Role dan permission

```text
roles
permissions
role_permissions
user_roles
```

Role sistem awal:

```text
super_admin
admin
operator
trainer
evaluator
participant
public_user
```

Contoh format permission:

```text
users.view
users.assign_role
learning.course.manage
learning.course.publish
learning.assignment.grade
knowledge.article.publish
regulation.version.upload
communication.event.attendance
certificate.issue
certificate.revoke
intelligence.export
approval.review
audit.view
settings.manage
```

Satu user dapat mempunyai beberapa role. Role dan kategori profil adalah konsep yang berbeda: seorang ASN dapat menjadi Trainer sekaligus Evaluator jika memenuhi penugasan dan permission.

Aturan bisnis:

- Role `operator`, `admin`, dan `super_admin` hanya boleh diberikan kepada user dengan kategori profil `asn`.
- Keunikan `user_roles` dijaga dengan `UNIQUE(user_id, role_id)`.
- Keunikan `role_permissions` dijaga dengan `UNIQUE(role_id, permission_id)`.
- Validasi pemberian role dilakukan pada `AccessControlService`, bukan hanya di antarmuka.

## 5.5 Organisasi/OPD

```text
organization_units
user_organizations
```

`organization_units` menyimpan struktur organisasi Pemerintah Provinsi Lampung.

Kolom utama:

```text
id
parent_id
code                              UNIQUE
name
unit_type
is_active
sort_order
created_at
updated_at
```

`user_organizations` menyimpan hubungan historis user dengan organisasi.

```text
user_id
organization_unit_id
is_primary
start_date
end_date
created_at
updated_at
```

Constraint utama:

```text
UNIQUE(user_id, organization_unit_id, start_date)
```

Kebijakan “satu organisasi utama aktif” dijaga melalui service/transaction dan, bila diperlukan, generated column/index yang kompatibel dengan MySQL target.

---

# 6. Shared Platform Services

## 6.1 File asset

Semua metadata file berada pada `file_assets`.

Kolom inti:

```text
id
disk
path
original_name
stored_name
mime_type
extension
size_bytes
checksum_sha256
visibility
uploaded_by
created_at
updated_at
deleted_at
```

Nilai `path` dapat `NULL`. Artinya metadata placeholder atau catatan file sudah dibuat, tetapi berkas fisik belum tersedia.

```text
path = NULL
→ File belum tersedia
→ UI/API tidak menyajikan download atau preview aktif
```

Kondisi ini digunakan untuk draft regulasi, metadata dokumen yang belum diunggah, template yang belum tersedia, atau sertifikat yang record-nya sudah terbentuk namun PDF belum dihasilkan.

Constraint dan aturan:

```text
UNIQUE(path)
```

MySQL mengizinkan beberapa `NULL` pada unique index, sehingga placeholder tidak saling bertabrakan.

- File fisik dikelola melalui `FileStorageService` di `writable/uploads/` atau storage privat yang setara.
- Nama fisik menggunakan UUID/hash; `original_name` bukan path.
- `checksum_sha256` digunakan untuk integritas/deduplikasi terkontrol, bukan sebagai pengganti authorization.
- File protected/private hanya dapat dilayani setelah pemeriksaan authorization.
- Penghapusan fisik dilakukan setelah pemeriksaan seluruh referensi dan mengikuti retensi/audit policy.

## 6.2 Approval workflow

```text
approvals
approval_histories
```

Workflow standar:

```text
Draft
↓
Submit
↓
Review
↓
Approve
↓
Publish
```

Status yang dapat digunakan sesuai domain:

```text
draft
submitted
in_review
approved
published
rejected
revision_required
archived
cancelled
```

`approvals` memakai relasi polymorphic:

```text
subject_type
subject_id
```

Contoh `subject_type` yang diizinkan:

```text
learning_program
learning_course
learning_class
article
case_study
document_template
regulation
regulation_version
news
event
certificate
```

Karena polymorphic relation tidak dapat memakai foreign key umum, integritas subject divalidasi oleh `ApprovalService`. Setiap transisi workflow wajib dicatat pada `approval_histories` beserta actor, keputusan, catatan, dan timestamp.

## 6.3 Audit log

`audit_logs` mencatat aktivitas penting.

```text
id
actor_user_id
action
subject_type
subject_id
route_name
request_id
ip_address
user_agent
before_data JSON
after_data JSON
context_data JSON
created_at
```

Contoh tindakan:

```text
users.assign_role
learning.class.configure_assessment
learning.assignment.grade
learning.final_grade.calculate
certificate.issue
certificate.revoke
regulation.version.upload
intelligence.export
settings.manage
```

Audit log adalah append-only secara fungsional. Update/penghapusan langsung dibatasi ketat oleh kebijakan retensi dan akses administratif khusus.

## 6.4 Notifikasi

`notifications` mendukung notifikasi internal dan email.

```text
id
user_id
channel                            in_app | email
notification_type
title
message
data JSON
read_at
sent_at
failed_at
created_at
updated_at
```

Notifikasi tidak boleh memuat secret, token reset, atau data pribadi sensitif secara berlebihan. Untuk email yang gagal, data error teknis disimpan secara terbatas dan tidak ditampilkan publik.

---

# 7. Arena Belajar

## 7.1 Struktur pembelajaran

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
Final Grade dan Completion
↓
Participant Feedback
↓
Certificate Eligibility
↓
Certificate
```

Tabel utama:

```text
programs
courses
learning_classes
class_trainers
class_evaluators
learning_modules
lessons
lesson_materials
enrollments
learning_progress
assessment_configurations
pre_test_attempts
quizzes
questions
question_options
quiz_attempts
quiz_answers
quiz_answer_options
assignments
assignment_submissions
assignment_submission_files
assignment_grades
post_test_attempts
participant_feedbacks
final_grades
```

## 7.2 Program dan course

`programs` mengelompokkan course pembelajaran.

```text
id
code                              UNIQUE
title
slug                              UNIQUE
summary
description
cover_file_asset_id
workflow_status
created_by
published_at
created_at
updated_at
deleted_at
```

`courses` adalah unit pembelajaran utama. Course dapat berada dalam program atau berdiri sendiri melalui `program_id` nullable.

```text
id
program_id                         NULLABLE
code                               UNIQUE
title
slug                               UNIQUE
summary
description
learning_outcomes
estimated_duration_minutes
workflow_status
created_by
published_at
created_at
updated_at
deleted_at
```

## 7.3 Kelas dan penugasan

`learning_classes` menyimpan pelaksanaan course per jadwal/angkatan.

Kolom inti:

```text
id
course_id
code                               UNIQUE
title
delivery_mode                      online | offline | blended
start_at
end_at
enrollment_open_at
enrollment_close_at
quota
status
passing_grade
quiz_weight
assignment_weight
post_test_weight
pre_test_enabled
post_test_enabled
participant_feedback_required
certificate_enabled
created_by
created_at
updated_at
deleted_at
```

Aturan database/service:

```text
end_at >= start_at
passing_grade >= 70 dan <= 100
quiz_weight >= 0
assignment_weight >= 0
post_test_weight = 60 apabila post_test_enabled = true
quiz_weight + assignment_weight = 40 apabila post_test_enabled = true
assignment_weight = 0 apabila assignment tidak diaktifkan
quiz_weight = 40 apabila assignment tidak diaktifkan dan post-test aktif
```

Untuk menjaga fleksibilitas struktur assessment, keberadaan assignment di class ditentukan oleh `assignments` aktif yang terkait class/course dan konfigurasi assessment. Service `AssessmentConfigurationService` wajib memvalidasi kombinasi bobot serta komponen sebelum class dibuka untuk enrollment.

Trainer dan evaluator menggunakan pivot:

```text
class_trainers
class_evaluators
```

Keduanya minimal menyimpan:

```text
learning_class_id
user_id
assignment_role / is_primary bila diperlukan
assigned_at
assigned_by
```

Constraint utama:

```text
UNIQUE(learning_class_id, user_id)
```

## 7.4 Modul, lesson, dan materi

`learning_modules` terkait dengan course agar struktur materi dapat digunakan pada setiap kelas course.

```text
course_id
title
description
sequence_order
is_required
```

Constraint:

```text
UNIQUE(course_id, sequence_order)
```

`lessons` terkait ke module dan mendukung tipe konten:

```text
text
document
video
link
```

Kolom minimum:

```text
learning_module_id
title
slug
content_type
content_body
external_url
sequence_order
is_required
estimated_duration_minutes
is_published
```

`lesson_materials` menghubungkan lesson dengan `file_assets` untuk lampiran dokumen.

## 7.5 Enrollment dan progress

`enrollments` menghubungkan peserta dengan kelas.

```text
id
user_id
learning_class_id
enrollment_status
enrolled_at
approved_by
approved_at
completed_at
final_status
created_at
updated_at
```

Constraint:

```text
UNIQUE(user_id, learning_class_id)
```

Status enrollment:

```text
pending
approved
rejected
cancelled
completed
```

Status akhir pembelajaran:

```text
in_progress
passed
failed
withdrawn
```

`learning_progress` mencatat akses/penyelesaian lesson per enrollment.

```text
UNIQUE(enrollment_id, lesson_id)
```

Kolom penting:

```text
enrollment_id
lesson_id
started_at
completed_at
progress_percent
last_accessed_at
```

## 7.6 Konfigurasi assessment

`assessment_configurations` menyimpan konfigurasi eksplisit level class bila diperlukan untuk audit dan versioning aturan assessment.

Kolom inti:

```text
learning_class_id                  UNIQUE
pre_test_enabled
pre_test_attempt_limit
quiz_enabled
quiz_attempt_limit
quiz_best_attempt_enabled
assignment_enabled
assignment_required_when_enabled
post_test_enabled
post_test_attempt_limit
post_test_best_attempt_enabled
quiz_weight
assignment_weight
post_test_weight
passing_grade
participant_feedback_required
certificate_enabled
created_by
updated_by
created_at
updated_at
```

Nilai baseline yang ditetapkan:

```text
pre_test_attempt_limit = 1
quiz_best_attempt_enabled = true
assignment_required_when_enabled = true
post_test_best_attempt_enabled = true
post_test_weight = 60
passing_grade >= 70
```

Jika konfigurasi ini dipakai, field assessment yang berulang pada `learning_classes` dapat direduksi menjadi hanya atribut ringkasan/cached. Satu sumber kebenaran normatif harus dipilih pada implementasi final; blueprint ini menetapkan `assessment_configurations` sebagai sumber aturan assessment agar perubahan dapat diaudit secara jelas.

## 7.7 Pre-test

Pre-test mengukur baseline dan tidak dihitung dalam nilai akhir.

```text
pre_test_attempts
```

Kolom inti:

```text
id
enrollment_id
started_at
submitted_at
score
is_completed
created_at
updated_at
```

Constraint:

```text
UNIQUE(enrollment_id)
```

Aturan:

- Jika pre-test diaktifkan, peserta hanya memiliki satu attempt.
- Nilai auto-grading berada pada rentang 0–100.
- `score` pre-test tidak digunakan pada formula `final_grades.final_score`.
- Detail jawaban dapat menggunakan tabel jawaban terpisah atau mekanisme quiz reusable dengan `assessment_type = pre_test`; implementasi dipilih konsisten pada ERD/data dictionary.

## 7.8 Quiz per lesson

Quiz awal mendukung pertanyaan objektif untuk auto-grading.

```text
multiple_choice
multiple_response
true_false
```

Relasi:

```text
quizzes
└── questions
    └── question_options

quiz_attempts
└── quiz_answers
    └── quiz_answer_options
```

`quizzes` terkait dengan lesson dan dapat dikonfigurasikan sebagai quiz lesson.

Kolom penting:

```text
lesson_id
assessment_type                    lesson_quiz
attempt_limit
passing_score
is_required
is_published
```

Constraint:

```text
UNIQUE(quiz_id, enrollment_id, attempt_number)
UNIQUE(quiz_attempt_id, question_id)
```

Aturan:

- Auto-grading menghitung `score` 0–100 saat attempt dikirim.
- Nilai quiz yang dipakai untuk nilai akhir adalah best attempt sesuai konfigurasi class.
- Agregasi beberapa quiz lesson didefinisikan eksplisit, misalnya rerata best attempt seluruh quiz wajib yang telah published.
- Quiz yang tidak wajib dapat dicatat untuk progres tetapi tidak masuk agregasi bila konfigurasi menetapkannya demikian.

## 7.9 Assignment dan manual grading

Assignment bersifat opsional pada level class. Jika assignment tersedia/diaktifkan, assignment wajib diselesaikan untuk menyelesaikan class dan menjadi eligible sertifikat.

```text
assignments
assignment_submissions
assignment_submission_files
assignment_grades
```

`assignments` minimal memuat:

```text
id
learning_class_id
lesson_id                           NULLABLE
title
instruction
due_at
max_score
is_required
is_active
allow_resubmission
max_submission_count
created_by
created_at
updated_at
```

`assignment_submissions` menyimpan setiap versi pengumpulan peserta.

```text
id
assignment_id
enrollment_id
submission_number
submission_text
status
submitted_at
revision_requested_at
resubmission_due_at
created_at
updated_at
```

Status submission:

```text
draft
submitted
under_review
revision_required
resubmitted
graded
late
cancelled
```

Constraint:

```text
UNIQUE(assignment_id, enrollment_id, submission_number)
```

`assignment_submission_files` menghubungkan submission dan file asset.

`assignment_grades` menyimpan nilai evaluator untuk satu submission.

```text
assignment_submission_id            UNIQUE
grader_user_id
score
feedback
graded_at
created_at
updated_at
```

Aturan:

- Nilai assignment adalah manual grading oleh evaluator yang ditugaskan atau actor dengan permission sah.
- Jika evaluator meminta revisi, status menjadi `revision_required`; peserta dapat upload ulang sebagai submission number baru, sesuai batas yang dikonfigurasi.
- Nilai assignment yang digunakan untuk nilai akhir adalah nilai final approved pada submission terbaru/terpilih sesuai aturan class.
- Bila lebih dari satu assignment wajib, aggregation rule harus eksplisit, misalnya rata-rata nilai assignment wajib yang sudah graded.

## 7.10 Post-test

Post-test menggunakan auto-grading dan berkontribusi 60% terhadap nilai akhir.

```text
post_test_attempts
```

Kolom inti:

```text
id
enrollment_id
attempt_number
started_at
submitted_at
score
is_completed
created_at
updated_at
```

Constraint:

```text
UNIQUE(enrollment_id, attempt_number)
```

Aturan:

- Post-test hanya tersedia bila `post_test_enabled = true`.
- Nilai berada pada rentang 0–100.
- Nilai yang dipakai adalah best attempt.
- Post-test memiliki bobot tetap 60%.
- Detail soal/jawaban dapat menggunakan engine question/option/answer yang sama dengan quiz; tabel attempt dapat menyimpan referensi assessment/quiz yang digunakan untuk menjaga versi evaluasi.

## 7.11 Feedback peserta, nilai akhir, dan completion

`participant_feedbacks` menyimpan feedback peserta terhadap class bila diwajibkan.

```text
id
enrollment_id                       UNIQUE
rating                              NULLABLE
feedback_text                       NULLABLE
submitted_at
created_at
updated_at
```

`final_grades` menyimpan snapshot hasil akhir per enrollment.

```text
id
enrollment_id                       UNIQUE
quiz_score
assignment_score
post_test_score
final_score
passing_grade
is_passed
all_required_lessons_completed
all_required_assessments_completed
participant_feedback_completed
certificate_eligible
calculated_at
calculated_by
notes
created_at
updated_at
```

Formula baseline:

```text
final_score = (quiz_score × quiz_weight / 100)
            + (assignment_score × assignment_weight / 100)
            + (post_test_score × post_test_weight / 100)
```

Dengan ketentuan standar:

```text
post_test_weight = 60
quiz_weight + assignment_weight = 40
```

Jika assignment tidak tersedia:

```text
assignment_weight = 0
quiz_weight = 40
post_test_weight = 60
```

`certificate_eligible = true` hanya bila seluruh syarat berlaku telah terpenuhi, minimal:

- Enrollment berstatus sah/approved dan tidak withdrawn/cancelled.
- Semua lesson wajib selesai.
- Pre-test selesai jika pre-test diaktifkan.
- Semua quiz/assessment wajib selesai.
- Semua assignment wajib memiliki submission final yang telah graded dan memenuhi aturan revisi.
- Post-test selesai jika diaktifkan.
- `final_score >= passing_grade`, dengan passing grade minimal 70.
- Feedback peserta telah dikirim bila diwajibkan.
- Certificate diaktifkan pada class dan tidak ada kondisi administratif yang memblokir penerbitan.

Perhitungan dilakukan pada `LearningCompletionService` dalam transaction. Nilai mentah/komponen tidak boleh ditimpa tanpa audit; recalculation harus mencatat actor, waktu, alasan, dan perubahan hasil.

---

# 8. Bunga Rampai

Bunga Rampai adalah pusat pengetahuan PBJ.

```text
knowledge_categories
tags
articles
article_tags
faqs
case_studies
document_templates
bookmarks
```

## 8.1 Kategori dan tag

`knowledge_categories` mendukung kategori bertingkat dengan `parent_id`.

```text
id
parent_id
name
slug                              UNIQUE
description
sort_order
is_active
created_at
updated_at
```

`tags` adalah label reusable. `article_tags` adalah pivot dengan `UNIQUE(article_id, tag_id)`.

## 8.2 Konten pengetahuan

Setiap konten memiliki lifecycle publikasi melalui `workflow_status` dan, jika proses approval aktif, relasi ke `approvals`.

| Tabel | Fungsi |
|---|---|
| `articles` | Artikel PBJ, panduan, best practice, dan wawasan |
| `faqs` | Pertanyaan dan jawaban PBJ |
| `case_studies` | Studi kasus untuk pembelajaran/praktik baik |
| `document_templates` | Template dokumen PBJ yang dapat diunduh |

Artikel dan studi kasus memiliki slug unik. Template dokumen menggunakan `file_asset_id`, yang boleh merujuk metadata dengan `path = NULL` pada tahap draft.

## 8.3 Bookmark

`bookmarks` memakai relasi polymorphic:

```text
user_id
bookmarkable_type
bookmarkable_id
created_at
```

Tipe yang diizinkan:

```text
article
case_study
document_template
regulation
```

Constraint:

```text
UNIQUE(user_id, bookmarkable_type, bookmarkable_id)
```

Integritas target polymorphic dijaga oleh `BookmarkService`.

---

# 9. Regulasi PBJ

## 9.1 Tabel regulasi

```text
regulation_categories
procurement_stages
competencies
regulations
regulation_versions
regulation_procurement_stages
regulation_competencies
regulation_keywords
regulation_download_logs
```

## 9.2 Metadata regulasi

`regulations` menyimpan identitas dan metadata utama.

```text
id
regulation_category_id
title
regulation_number
year
official_date
effective_date
legal_status
workflow_status
summary
source_url
created_by
published_at
created_at
updated_at
deleted_at
```

Status hukum:

```text
active
revoked
amended
expired
```

Constraint identitas:

```text
UNIQUE(regulation_category_id, regulation_number, year)
```

## 9.3 Versioning dokumen

Satu regulasi dapat memiliki beberapa versi.

```text
regulation_versions
├── regulation_id
├── version_number
├── file_asset_id
├── change_notes
├── is_current
├── workflow_status
├── uploaded_by
├── published_at
├── created_at
└── updated_at
```

Constraint:

```text
UNIQUE(regulation_id, version_number)
```

Hanya satu versi current untuk satu regulasi. MySQL tidak menyediakan partial unique index secara langsung; penegakan dilakukan oleh `RegulationVersionService` dalam transaction/locking. Jika diperlukan, gunakan generated column yang tervalidasi pada versi MySQL target.

## 9.4 Metadata PBJ

Regulasi dapat terkait banyak tahapan PBJ dan kompetensi.

```text
regulation_procurement_stages
regulation_competencies
```

Keduanya memakai composite unique key terhadap dua foreign key. Kata kunci disimpan pada `regulation_keywords` untuk pencarian fleksibel.

`regulation_download_logs` menyimpan event download untuk monitoring terkontrol dan tidak boleh menjadi mekanisme pelacakan publik berlebihan.

---

# 10. Berita, Agenda, dan Event

## 10.1 Berita

```text
news_categories
news
```

`news` mempunyai `workflow_status`, `author_id`, `published_at`, dan `view_count`. Hanya berita berstatus `published` yang dapat muncul pada Landing Page/public area.

## 10.2 Event

```text
events
event_registrations
event_attendances
```

Jenis event:

```text
webinar
socialization
workshop
training
other
```

Mode pelaksanaan:

```text
online
offline
hybrid
```

Status event:

```text
draft
coming_soon
open_registration
full
finished
cancelled
```

Kolom penting `events`:

```text
id
title
slug                              UNIQUE
event_type
delivery_mode
start_at
end_at
registration_open_at
registration_close_at
quota
location_text
meeting_url
workflow_status
event_status
certificate_enabled
created_by
published_at
created_at
updated_at
```

## 10.3 Registrasi, waitlist, dan absensi

`event_registrations` memiliki constraint:

```text
UNIQUE(event_id, user_id)
```

Status registrasi:

```text
registered
waitlisted
cancelled
rejected
```

Kolom tambahan dapat menyimpan `registered_at`, `cancelled_at`, `approved_by`, dan catatan administratif.

`event_attendances` menyimpan satu data absensi per registrasi.

```text
UNIQUE(event_registration_id)
```

Status absensi:

```text
present
absent
excused
```

Pemeriksaan kuota, promosi waitlist, dan pembatalan harus berada dalam transaction pada `EventRegistrationService`, bukan hanya pada validasi UI.

---

# 11. Sertifikat Digital

## 11.1 Tabel sertifikat

```text
certificate_templates
certificates
certificate_sequences
certificate_verification_logs
certificate_revocations
```

Sertifikat event tidak memerlukan tabel sertifikat terpisah. Sertifikat event diterbitkan melalui `certificates` dengan:

```text
source_type = event_registration
source_id   = event_registrations.id
```

Sertifikat learning diterbitkan melalui:

```text
source_type = learning_enrollment
source_id   = enrollments.id
```

## 11.2 Template

`certificate_templates` mendukung template penerbitan:

```text
learning
event
```

Kolom utama:

```text
id
code                               UNIQUE
name
certificate_type
html_template
layout_file_asset_id
is_active
created_by
created_at
updated_at
```

`html_template` digunakan generator PDF. `layout_file_asset_id` dapat dipakai untuk asset pendukung seperti background/brand element yang sah.

## 11.3 Sertifikat

Kolom inti `certificates`:

```text
id
recipient_user_id
certificate_template_id
source_type
source_id
certificate_number
issued_at
status
file_asset_id
verification_token
issued_by
expires_at
created_at
updated_at
```

Sumber sertifikat yang diizinkan:

```text
learning_enrollment
event_registration
```

Constraint wajib:

```text
UNIQUE(certificate_number)
UNIQUE(verification_token)
UNIQUE(source_type, source_id)
```

Satu enrollment atau event registration hanya dapat menghasilkan satu sertifikat resmi. Jika PDF perlu diregenerasi, record sertifikat tidak diduplikasi. File asset yang dirujuk dapat diperbarui melalui proses terkontrol dan audit.

Status:

```text
pending
issued
revoked
expired
```

## 11.4 Nomor, token, QR, dan verifikasi

`certificate_sequences` menyimpan nomor terakhir menurut scope dan tahun.

```text
sequence_scope
year
last_number
updated_at

UNIQUE(sequence_scope, year)
```

Penerbitan sertifikat menggunakan transaction dan locking pada sequence agar nomor unik pada request paralel.

`verification_token` harus acak, unik, cukup panjang, serta tidak memuat NIP, NIK, email, atau data pribadi penerima. QR code berisi URL/token verifikasi publik, bukan data personal lengkap.

`certificate_verification_logs` dapat menyimpan:

```text
certificate_id                       NULLABLE
verification_token
result                               valid | revoked | expired | not_found
ip_address
user_agent
verified_at
```

## 11.5 Revocation

Sertifikat tidak dihapus saat dibatalkan. `certificate_revocations` menyimpan:

```text
certificate_id                       UNIQUE
revoked_by
revoked_at
reason
replacement_certificate_id           NULLABLE
created_at
```

Status sertifikat diperbarui menjadi `revoked`, dan semua tindakan direkam pada audit log.

---

# 12. Government Intelligence PBJ

Scope Government Intelligence dibatasi pada **Pemerintah Provinsi Lampung**. Data kabupaten/kota tidak termasuk dalam cakupan intelligence organisasi kecuali kebijakan resmi memperluas scope di masa depan.

## 12.1 JF PPBJ

```text
jf_ppbj_levels
jf_ppbj_personnel
jf_ppbj_histories
```

`jf_ppbj_levels` menyimpan jenjang awal:

```text
PERTAMA
MUDA
MADYA
UTAMA
```

`jf_ppbj_personnel` menyimpan SDM JF PPBJ, terutama untuk unit yang relevan seperti Biro Pengadaan Barang dan Jasa.

```text
id
user_id                              NULLABLE
organization_unit_id
employee_name
employee_identifier
jf_ppbj_level_id
employment_status
effective_start_date
effective_end_date
notes
created_at
updated_at
deleted_at
```

`user_id` nullable karena tidak semua data SDM intelligence harus mempunyai akun aplikasi.

Status SDM:

```text
active
inactive
retired
transferred
```

`jf_ppbj_histories` mencatat perubahan, misalnya:

```text
added
transferred_in
transferred_out
promoted
retired
deactivated
updated
```

## 12.2 Sertifikat kompetensi PBJ

```text
competency_certificate_types
competency_certificates
```

Kategori awal:

```text
PBJ_LEVEL_1
PPK_TYPE_A
PPK_TYPE_B
PPK_TYPE_C
```

`competency_certificates` dapat merujuk ke akun user atau data master SDM non-user.

```text
id
user_id                              NULLABLE
organization_unit_id
holder_name
holder_identifier
competency_certificate_type_id
certificate_number
issued_at
expires_at
status
evidence_file_asset_id
verified_by
verified_at
created_at
updated_at
deleted_at
```

Constraint:

```text
UNIQUE(competency_certificate_type_id, certificate_number)
```

Status:

```text
active
expired
revoked
unknown
```

Data ini terbatas dan tidak boleh diekspos pada statistik publik secara individual.

## 12.3 Gap kompetensi

`competency_gap_snapshots` menyimpan data agregat per organisasi dan periode.

```text
id
organization_unit_id
period_date
metric_code
required_count
available_count
gap_count
generated_at
generated_by
created_at
updated_at
```

Constraint:

```text
UNIQUE(organization_unit_id, period_date, metric_code)
```

Formula:

```text
gap_count = required_count - available_count
```

Formula dapat dijaga dengan `CHECK` jika didukung dan diaktifkan pada environment target, serta selalu divalidasi oleh `CompetencyGapService` sebelum simpan.

Statistik publik hanya menggunakan agregat seperti jumlah, distribusi, atau gap. Nama personel, identifier, NIP, dan bukti sertifikat tidak boleh dipublikasikan.

---

# 13. Foreign Key dan Delete Policy

## 13.1 Prinsip

BegawiPBJ mengutamakan jejak historis, auditability, dan perlindungan bukti pembelajaran. Kebijakan default untuk relasi bisnis penting adalah `RESTRICT` atau perubahan status/soft delete, bukan hard delete/cascade.

| Entitas | Kebijakan |
|---|---|
| User | Soft delete/anonymization terkontrol; hard delete dihindari |
| Role dan permission sistem | `RESTRICT` |
| Course dan class | Archive/cancel, bukan delete jika memiliki enrollment |
| Enrollment dan final grade | `RESTRICT` |
| Sertifikat | Revoke, bukan delete |
| Regulasi | Ubah legal status/archive, bukan hapus fisik bila telah dipublikasikan |
| Approval history dan audit log | Tidak cascade delete |
| File asset | Hapus fisik hanya melalui service setelah pemeriksaan seluruh referensi |
| Wilayah | Master read-only; tidak dihapus oleh aplikasi |

`ON DELETE SET NULL` dapat digunakan pada referensi actor historis yang perlu dipertahankan bila akun aktor dinonaktifkan/anonymized, misalnya `audit_logs.actor_user_id`, `file_assets.uploaded_by`, atau `certificates.issued_by`. Keputusan ini tetap harus kompatibel dengan model retensi dan keamanan akun.

## 13.2 Polymorphic relation

Tabel berikut menggunakan relasi polymorphic dan tidak memiliki foreign key generik:

| Tabel | Kolom | Integritas dijaga oleh |
|---|---|---|
| `approvals` | `subject_type`, `subject_id` | `ApprovalService` |
| `bookmarks` | `bookmarkable_type`, `bookmarkable_id` | `BookmarkService` |
| `certificates` | `source_type`, `source_id` | `CertificateEligibilityService` |
| `audit_logs` | `subject_type`, `subject_id` | `AuditService` |

Setiap service harus memvalidasi tipe yang diizinkan, keberadaan sumber, permission actor, ownership/penugasan, dan status objek sebelum data disimpan.

---

# 14. Indeks Utama

Indeks disiapkan untuk halaman daftar, dashboard, pencarian, monitoring, dan query ownership yang paling sering.

```text
users(account_status, created_at)
users(email)
users(username)
permissions(module, action)
enrollments(learning_class_id, enrollment_status)
enrollments(user_id, final_status)
learning_progress(enrollment_id, completed_at)
pre_test_attempts(enrollment_id)
quiz_attempts(enrollment_id, quiz_id, score)
assignment_submissions(enrollment_id, assignment_id, status)
post_test_attempts(enrollment_id, score)
final_grades(enrollment_id, certificate_eligible)
articles(workflow_status, published_at)
regulations(legal_status, year)
regulations(workflow_status, published_at)
regulation_versions(regulation_id, is_current)
events(event_status, start_at)
event_registrations(event_id, registration_status)
certificates(recipient_user_id, status)
certificates(verification_token)
approvals(subject_type, subject_id, workflow_status)
audit_logs(subject_type, subject_id, created_at)
notifications(user_id, read_at, created_at)
competency_gap_snapshots(organization_unit_id, period_date, metric_code)
```

Optimasi full-text search dapat dievaluasi untuk artikel, regulasi, dan berita setelah pola pencarian aktual serta kebutuhan bahasa Indonesia tervalidasi. Full-text index tidak boleh menggantikan authorization pada hasil pencarian internal.

---

# 15. Data Privacy dan Keamanan

## 15.1 Klasifikasi data

| Klasifikasi | Contoh | Aturan akses |
|---|---|---|
| Publik | Berita/regulasi published, statistik agregat, hasil verifikasi sertifikat minimum | Landing Page/Public Area |
| Internal | Approval, audit, laporan, data operasional | Permission internal |
| Terbatas | Nama lengkap, email, telepon, NIP, NIK, identifier SDM, dokumen bukti | Permission khusus, purpose limitation, dan audit |
| Rahasia sistem | Password hash, token reset, encryption key, secret environment | Tidak diekspor/dipublikasikan |

## 15.2 Perlindungan data

- Password hanya disimpan sebagai hash menggunakan `password_hash()`.
- NIK, NIP, dan identifier SDM tidak digunakan sebagai token publik atau bagian token sertifikat.
- Token verifikasi sertifikat harus acak, unik, dan tidak reversible menjadi data personal.
- File privat disajikan melalui controller/service yang melakukan authorization.
- Upload memvalidasi MIME type, ekstensi, ukuran, checksum, dan nama fisik unik.
- Data demo tidak boleh menggunakan data ASN, NIP, NIK, dokumen, atau data production nyata.
- Database dump production tidak disimpan pada repository.
- Query statistik publik harus bekerja dari view/query agregat yang secara eksplisit tidak memilih kolom personal.
- Export data terbatas memerlukan permission, filter scope, audit, dan mekanisme pengamanan file hasil export.

---

# 16. Migration Blueprint

Migration CodeIgniter 4 dipecah berdasarkan domain dan dependensi.

```text
2026-09-28-000001_CreateIdentityAndRbacTables.php
2026-09-28-000002_CreateOrganizationTables.php
2026-09-28-000003_CreateSharedWorkflowAuditFileTables.php
2026-09-28-000004_CreateLearningCoreTables.php
2026-09-28-000005_CreateLearningAssessmentTables.php
2026-09-28-000006_CreateKnowledgeTables.php
2026-09-28-000007_CreateRegulationTables.php
2026-09-28-000008_CreateCommunicationTables.php
2026-09-28-000009_CreateCertificateTables.php
2026-09-28-000010_CreateGovernmentIntelligenceTables.php
2026-09-28-000011_AddPerformanceIndexesAndConstraints.php
```

Nama/timestamp di atas adalah baseline dokumentasi. Saat implementasi, timestamp migration dibuat oleh CodeIgniter 4. Migration yang telah diterapkan pada environment bersama tidak boleh diubah nama atau isinya secara destruktif.

Urutan dependensi:

```text
Identity dan RBAC
↓
Organisasi dan struktur master wilayah
↓
File asset, approval, audit, notifikasi
↓
Arena Belajar core
↓
Assessment, grading, feedback, completion
↓
Bunga Rampai
↓
Regulasi
↓
Berita dan event
↓
Sertifikat digital
↓
Government Intelligence
↓
Index dan constraint tambahan
```

Aturan migration:

- Satu migration memiliki tanggung jawab jelas dan dapat direview.
- Perubahan schema baru selalu menggunakan migration baru.
- `down()` disiapkan untuk development/testing sejauh aman; production rollback mengikuti prosedur deployment yang disetujui.
- Uji pada database kosong menggunakan `php spark migrate:fresh`.
- Migration tidak mengimpor dummy wilayah; import master wilayah adalah prosedur terpisah.
- Constraint yang tidak dapat diekspresikan portabel pada MySQL, seperti conditional uniqueness tertentu, harus dijaga service/transaction dan diuji secara eksplisit.

---

# 17. Seeder dan Import Blueprint

Struktur seeder yang direkomendasikan:

```text
app/Database/Seeds/
├── DatabaseSeeder.php
├── Reference/
│   ├── RoleSeeder.php
│   ├── PermissionSeeder.php
│   ├── RolePermissionSeeder.php
│   ├── OrganizationSeeder.php
│   ├── KnowledgeCategorySeeder.php
│   ├── RegulationCategorySeeder.php
│   ├── ProcurementStageSeeder.php
│   ├── CompetencySeeder.php
│   ├── JfPpbjLevelSeeder.php
│   └── CompetencyCertificateTypeSeeder.php
├── System/
│   ├── InitialSuperAdminSeeder.php
│   └── SystemSettingSeeder.php
├── Development/
│   ├── DevelopmentUserSeeder.php
│   ├── LearningDemoSeeder.php
│   ├── KnowledgeDemoSeeder.php
│   ├── RegulationDemoSeeder.php
│   ├── CommunicationDemoSeeder.php
│   └── IntelligenceDemoSeeder.php
└── Import/
    ├── WilayahImportSeeder.php
    └── OpdImportSeeder.php
```

Aturan:

- `DatabaseSeeder` hanya menjalankan reference/system seeder yang aman dan tidak memasukkan data demo secara otomatis.
- Data demo hanya berjalan secara eksplisit pada environment development/testing.
- `InitialSuperAdminSeeder` membaca credential awal dari environment aman; password default tidak boleh hard-code dalam repository.
- `WilayahImportSeeder` hanya digunakan untuk import master resmi dari `database/reference/wilayah.sql` atau sumber resmi yang disetujui.
- Development seeder tidak boleh menyentuh tabel `wilayah`.
- Import atau perubahan master organisasi/wilayah harus dapat diaudit dan mengikuti prosedur data governance.

---

# 18. Validasi Baseline

## 18.1 Perintah pengujian

```bash
php spark migrate:fresh
php spark db:seed DatabaseSeeder
php spark test
```

Setelah schema dimigrasikan, import master wilayah resmi dilakukan terpisah sesuai prosedur lingkungan:

```bash
mysql -u <username> -p <database_name> < database/reference/wilayah.sql
```

## 18.2 Checklist schema

- Semua tabel aplikasi menggunakan InnoDB dan `utf8mb4`.
- PK/FK aplikasi menggunakan `BIGINT UNSIGNED` secara konsisten, kecuali `wilayah.kode` yang menggunakan `VARCHAR(13)`.
- `wilayah.kode` digunakan sebagai key referensi profil dan tidak disentuh seeder dummy.
- Tidak ada tabel duplikat untuk konsep bisnis yang sama.
- Tabel pivot utama memiliki unique constraint.
- Sertifikat memiliki nomor, token, dan sumber unik.
- Placeholder file memakai `file_assets.path = NULL`.
- Audit log, approval history, final grade, dan sertifikat tidak terhapus oleh cascade tidak disengaja.
- Assessment mengakomodasi pre-test, quiz best attempt, assignment revision, post-test 60%, feedback, passing grade, dan certificate eligibility.
- Statistik publik tidak memilih atau menampilkan data pribadi ASN.

## 18.3 Test bisnis minimum

- User tidak dapat memiliki role duplikat.
- Peserta tidak dapat enrollment dua kali pada class yang sama.
- Pre-test tidak dapat dibuat lebih dari satu kali untuk enrollment yang sama.
- Attempt quiz tidak dapat memiliki nomor attempt yang sama pada enrollment dan quiz yang sama.
- Submission assignment tidak dapat duplikat pada assignment, enrollment, dan submission number yang sama.
- Submission revisi mengikuti batas submit/revision yang dikonfigurasi.
- Satu enrollment hanya memiliki satu final grade aktif.
- Final score tidak memasukkan pre-test dan memakai post-test 60%.
- Satu enrollment/event registration hanya menghasilkan satu sertifikat resmi.
- Nomor sertifikat dan token verifikasi selalu unik.
- Registrasi event tidak dapat lebih dari satu per user-event.
- Transisi approval tidak dapat melewati state yang tidak valid.
- File dengan `path = NULL` tidak menghasilkan download link aktif.
- Statistik publik tidak mengembalikan NIP, email, telepon, identifier, bukti dokumen, atau nama individual ASN.
- Seeder development tidak memodifikasi master `wilayah`.

---

# 19. Ringkasan Keputusan Arsitektur Database

```text
Database             : MySQL 8.4+ + InnoDB + utf8mb4
Pola aplikasi        : CodeIgniter 4 monolith
Identitas            : User, profile category, RBAC, organisasi
Wilayah              : wilayah(kode, nama), kode hierarkis, data resmi
LMS                  : Program → Course → Class → Module → Lesson → Assessment → Grade
Pre-test             : Satu kali, auto-graded 0–100, tidak dihitung
Quiz                 : Per lesson, auto-graded, best attempt
Assignment           : Opsional per class; wajib jika tersedia; manual grade dan revision/upload ulang
Post-test            : Auto-graded, best attempt, kontribusi 60%
Final score          : Quiz + assignment = 40%; post-test = 60%; passing grade minimal 70
Certificate          : Eligibility-based, satu per source, nomor/token unik, verifiable, revocable
Pengetahuan          : Bunga Rampai
Regulasi             : Metadata, versioning, tahapan PBJ, kompetensi
Komunikasi           : Berita, event, registrasi, waitlist, absensi
Intelligence         : OPD, JF PPBJ, kompetensi, gap agregat
Shared services      : File asset, approval, audit, notification
Privasi publik       : Data agregat tanpa data pribadi ASN
```

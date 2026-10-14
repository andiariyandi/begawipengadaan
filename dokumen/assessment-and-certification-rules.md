# BegawiPBJ — Assessment and Certification Rules

## Tujuan

Dokumen ini menetapkan aturan domain untuk assessment Arena Belajar, enrollment, progress, quiz, assignment, grading, feedback, final score, completion, certificate eligibility, penerbitan sertifikat, verifikasi publik, revocation, replacement, serta audit.

Dokumen ini melengkapi dan tidak mengubah kontrak yang telah ditetapkan dalam:

```text
README.md
dokumen/directory-tree.md
dokumen/route-contract.md
dokumen/ui-boundary.md
dokumen/auth-and-rbac.md
dokumen/workflow-contract.md
dokumen/file-storage-policy.md
dokumen/database-blueprint.md
dokumen/database-erd.md
dokumen/data-dictionary.md
dokumen/schema-fresh-v4-1.sql
dokumen/registration-policy.md
```

Aturan ini berlaku bagi sumber sertifikat:

```text
learning_enrollment
event_registration
```

---

## Prinsip Assessment dan Sertifikat

- Assessment hanya dapat diakses oleh Participant dengan enrollment sah dan aktif pada class terkait.
- Trainer dan Evaluator hanya dapat mengakses class, submission, atau penilaian yang ditugaskan kepada mereka.
- Nilai, jawaban quiz, submission tugas, feedback penilai, dan file assignment adalah data internal/restricted.
- Semua perhitungan nilai dan eligibility dilakukan server-side; browser tidak menjadi sumber kebenaran.
- Attempt, submission, grade, completion, eligibility, issue, revoke, dan replacement harus dapat diaudit.
- Sertifikat tidak diterbitkan hanya karena user membuka halaman; eligibility harus tervalidasi pada saat issue.
- Satu sumber pembelajaran atau event hanya dapat menghasilkan satu sertifikat resmi aktif melalui constraint `UNIQUE(source_type, source_id)` dan workflow penggantian yang terkendali.
- Revocation tidak menghapus record sertifikat atau riwayat audit.
- Verifikasi publik hanya menampilkan data minimum yang aman untuk mengonfirmasi keabsahan sertifikat.

---

## Boundary dan Akses

### Workspace FiStudy

Assessment Participant, Trainer, Evaluator, dan Operator menggunakan:

```text
Controller
app/Controllers/Workspace/
app/Controllers/Learning/

View
app/Views/main/workspace/

Layout
app/Views/main/layouts/workspace.php

Asset
public/assets/main/
```

### Dashboard Back-office WowDash

Administrasi class, assessment, nilai, approval, template sertifikat, sertifikat, dan laporan internal menggunakan:

```text
Controller
app/Controllers/Learning/
app/Controllers/Certificate/
app/Controllers/Dashboard/

View
app/Views/dashboard/

Layout
app/Views/dashboard/layouts/dashboard.php

Asset
public/assets/dashboard/
```

### Filter minimum

```text
Workspace assessment
→ auth + accountStatus:active + workspaceAccess
→ role/assignment/enrollment/ownership sesuai resource

Dashboard assessment/certificate
→ auth + verifiedEmail + accountStatus:active + dashboardAccess
→ permission granular + auditContext
```

---

## Entitas Utama

| Entitas | Peran |
|---|---|
| `learning_classes` | Konfigurasi class, bobot nilai, passing grade, certificate enabled |
| `enrollments` | Hubungan Participant dengan class serta status/hasil akhir |
| `learning_modules` dan `lessons` | Struktur materi dan progress wajib |
| `pre_tests` | Baseline assessment awal bila diaktifkan |
| `quizzes`, `quiz_questions`, `quiz_options` | Quiz lesson atau post-test |
| `quiz_attempts`, `quiz_answers` | Riwayat attempt serta jawaban peserta |
| `assignments` | Tugas peserta |
| `assignment_submissions` | Jawaban/submission, revisi, nilai, feedback |
| `assignment_submission_files` | Lampiran submission melalui file asset restricted |
| `learning_progress` | Status progress lesson per enrollment |
| `feedbacks` | Feedback peserta terhadap class/enrollment |
| `certificates` | Sertifikat digital learning/event |
| `certificate_templates` | Template sertifikat learning/event |

---

## Enrollment

### Status enrollment

```text
pending
active
completed
withdrawn
cancelled
```

### Transisi enrollment

| Dari | Aksi | Ke | Actor | Ketentuan |
|---|---|---|---|---|
| — | Buat enrollment | `pending` atau `active` | Participant/Admin/System | Mengikuti policy class, kapasitas, periode, prerequisite, dan approval bila ada |
| `pending` | Aktifkan | `active` | System/Admin/approval policy | Kuota, periode, prerequisite, dan syarat domain terpenuhi |
| `active` | Tandai selesai | `completed` | System | Completion policy class terpenuhi |
| `pending` / `active` | Withdraw | `withdrawn` | Participant owner/Admin | Mengikuti batas waktu dan policy class |
| `pending` / `active` | Cancel | `cancelled` | Admin/System | Alasan/audit bila diperlukan |

### Aturan enrollment

- Satu user hanya memiliki satu enrollment per class:

```text
UNIQUE(class_id, user_id)
```

- Participant hanya dapat mengakses lesson, quiz, assignment, feedback, dan sertifikat pribadi untuk enrollment miliknya.
- Enrollment `pending`, `withdrawn`, atau `cancelled` tidak memberi akses normal terhadap kegiatan pembelajaran yang memerlukan status `active`.
- Enrollment `completed` tetap dapat melihat riwayat pembelajaran sesuai policy, tetapi akses konten aktif dapat dibatasi oleh policy class.
- Perubahan enrollment yang memengaruhi kapasitas harus transaction-safe agar tidak terjadi overbooking.

---

## Progress Lesson

### Status progress

```text
not_started
in_progress
completed
```

### Aturan progress

- Progress disimpan pada `learning_progress` berdasarkan kombinasi unik:

```text
UNIQUE(enrollment_id, lesson_id)
```

- Participant hanya dapat memperbarui progress untuk lesson yang berada pada class dari enrollment miliknya dan sedang dapat diakses.
- Progress tidak boleh diterima hanya dari angka yang dikirim browser; server memvalidasi lesson, enrollment, rule completion, serta komponen yang diperlukan.
- Lesson required harus berstatus `completed` untuk memenuhi completion class dan eligibility sertifikat.
- Nilai `progress_percent` berada pada rentang 0–100.
- Completion lesson dapat dihasilkan otomatis oleh system ketika syarat lesson terpenuhi.

### Syarat completion lesson

Syarat final ditentukan oleh konfigurasi lesson/class. Contoh:

```text
Lesson teks
→ materi telah dibuka dan completion action tervalidasi.

Lesson dokumen/video
→ kondisi consumption minimum yang ditetapkan policy terpenuhi.

Lesson dengan quiz wajib
→ quiz required telah submitted/graded sesuai policy.

Lesson dengan assignment wajib
→ assignment telah graded atau memenuhi status yang ditetapkan class.
```

---

## Pre-test

Pre-test merupakan assessment baseline dan tidak selalu dihitung pada nilai akhir.

### Konfigurasi

| Field | Aturan |
|---|---|
| `is_enabled` | Jika false, pre-test tidak diwajibkan |
| `attempt_limit` | Baseline satu attempt |
| `status` | Harus tersedia/aktif sesuai policy untuk dapat diakses |

### Aturan pre-test

- Pre-test hanya dapat dimulai oleh Participant dengan enrollment `active`.
- Jika pre-test diaktifkan, peserta harus menyelesaikan pre-test sesuai policy untuk eligibility sertifikat.
- Skor pre-test digunakan sebagai baseline kemampuan awal dan tidak dihitung ke nilai akhir.
- Attempt pre-test menggunakan mekanisme `quiz_attempts` dan `quiz_answers` dengan type quiz `pre_test`.
- Attempt yang sudah `submitted` tidak boleh diubah langsung.
- System/Admin dapat menginvalidasi attempt dengan alasan dan audit bila terjadi anomali atau koreksi yang sah.

---

## Quiz dan Post-test

### Tipe quiz

```text
pre_test
lesson_quiz
post_test
```

### Status quiz attempt

```text
started
submitted
graded
invalidated
```

### Aturan attempt

- Participant hanya dapat membuat attempt untuk quiz yang tersedia, berada pada class/enrollment yang sah, dan belum melampaui `attempt_limit`.
- `attempt_number` dimulai dari 1 dan unik per quiz/enrollment:

```text
UNIQUE(quiz_id, enrollment_id, attempt_number)
```

- Attempt `started` dapat menyimpan jawaban sementara jika policy mengizinkan.
- Submit mengunci jawaban attempt; perubahan setelah submit hanya melalui workflow yang berwenang.
- Auto-grading dijalankan server-side untuk question type yang mendukung.
- Attempt yang sudah diproses menjadi `graded`.
- `invalidated` hanya dapat dilakukan oleh actor berwenang dengan alasan dan audit.

### Best attempt

Untuk quiz lesson dan post-test dengan lebih dari satu attempt, nilai yang dipakai adalah nilai attempt valid terbaik:

\[
quiz\_score = \max(score\_attempt\_valid)
\]

Attempt `invalidated` tidak masuk perhitungan best attempt maupun final score.

### Post-test

- Post-test wajib jika diaktifkan pada class.
- Nilai post-test berkontribusi **60%** dari nilai akhir.
- Post-test tidak dapat menjadi dasar kelulusan bila enrollment tidak memenuhi lesson/assessment wajib lain.

---

## Assignment

Assignment dapat berupa jawaban essay, upload berkas, atau kombinasi keduanya.

### Status submission

```text
draft
submitted
in_review
revision_required
graded
cancelled
```

### Aturan submission

- Participant hanya dapat membuat submission untuk assignment yang tersedia pada class dari enrollment aktif miliknya.
- `attempt_number` unik per assignment/enrollment:

```text
UNIQUE(assignment_id, enrollment_id, attempt_number)
```

- Submission draft dapat diedit oleh owner sebelum submit jika policy dan deadline mengizinkan.
- Submission `submitted` tidak dapat diubah kecuali Evaluator meminta revisi atau policy membuka attempt baru.
- Evaluator yang ditugaskan dapat mengambil submission untuk `in_review`.
- Evaluator dapat meminta `revision_required` dengan feedback/alasan yang wajib diisi.
- Participant owner dapat membuat revisi dan submit ulang selama masih berada pada batas `max_attempts`, deadline, dan policy class.
- Evaluator memberikan nilai dan feedback untuk mengubah status menjadi `graded`.
- Submission `graded` tidak dapat diubah oleh Participant.
- Koreksi nilai setelah `graded` hanya melalui actor berwenang, alasan, audit, dan policy koreksi yang terdokumentasi.

### File submission

- Lampiran assignment disimpan di `writable/uploads/submissions/` melalui `FileStorageService`.
- Visibility file submission adalah `restricted`.
- Akses file hanya untuk Participant owner, Evaluator assigned, Trainer/Admin sesuai policy, dan tidak boleh tersedia melalui URL public.
- Validasi MIME, ukuran, ekstensi, lifecycle file, dan policy assignment mengikuti `file-storage-policy.md`.

---

## Grading

### Auto-grading

Auto-grading digunakan pada quiz sesuai tipe pertanyaan yang mendukung. Server menghitung `is_correct` dan `score_awarded`; client tidak boleh mengirim nilai final sebagai sumber kebenaran.

### Manual grading

Manual grading berlaku terutama pada assignment dan dapat berlaku pada assessment lain bila dikonfigurasi.

| Actor | Kewenangan |
|---|---|
| Evaluator | Menilai submission dalam assignment yang ditugaskan |
| Trainer | Melihat progres/hasil kelas yang ditugaskan; memberi nilai hanya jika policy/permission mengizinkan |
| Admin | Melihat atau mengoreksi sesuai permission dan workflow |
| Super Admin | Akses hanya sesuai permission; tetap tercatat dalam audit |

### Nilai

- Nilai berada pada rentang 0–100 kecuali suatu assessment mendefinisikan skala lain yang kemudian dinormalisasi sebelum final score.
- Nilai `NULL` berarti belum tersedia/belum graded, bukan nilai nol.
- Grade dan feedback adalah data internal/restricted.
- Perubahan nilai setelah publish/eligibility sertifikat harus memicu re-evaluasi eligibility dan audit yang sesuai.

---

## Final Score

### Konfigurasi bobot class

Nilai akhir ditentukan oleh `learning_classes`.

```text
post_test_weight = 60
quiz_weight + assignment_weight = 40
quiz_weight + assignment_weight + post_test_weight = 100
passing_grade minimum = 70
```

Formula:

\[
final\_score =
(quiz\_score \times quiz\_weight / 100) +
(assignment\_score \times assignment\_weight / 100) +
(post\_test\_score \times post\_test\_weight / 100)
\]

Dengan baseline:

\[
final\_score =
(quiz\_score \times quiz\_weight / 100) +
(assignment\_score \times assignment\_weight / 100) +
(post\_test\_score \times 60 / 100)
\]

### Jika assignment tidak tersedia

```text
assignment_enabled = false
assignment_weight = 0
quiz_weight = 40
post_test_weight = 60
```

Formula menjadi:

\[
final\_score =
(quiz\_score \times 40 / 100) +
(post\_test\_score \times 60 / 100)
\]

### Ketentuan perhitungan

- `quiz_score` adalah agregasi score quiz required sesuai policy class; bila terdapat beberapa attempt, gunakan best valid attempt.
- `assignment_score` berasal dari submission `graded` yang valid; bila terdapat beberapa submission/revisi, gunakan nilai submission final yang ditetapkan policy.
- `post_test_score` berasal dari best valid post-test attempt.
- Komponen yang tidak aktif tidak boleh dihitung sebagai nol secara diam-diam; bobotnya harus dikonfigurasi ulang agar total tetap 100.
- Komponen yang diwajibkan tetapi belum selesai/graded menjadikan final score belum final dan eligibility belum terpenuhi.
- Perhitungan dijalankan server-side dan hasil disimpan pada `enrollments.final_score` hanya setelah komponen yang diperlukan tersedia.
- Nilai akhir dibatasi 0–100.

### Kelulusan

Participant dinyatakan lulus jika:

```text
final_score >= learning_classes.passing_grade
```

Baseline `passing_grade` adalah 70, tetapi class dapat menetapkan passing grade lain dalam rentang 0–100 sesuai policy.

`enrollments.passed_at` diisi ketika kelulusan tervalidasi. Kelulusan tidak otomatis menerbitkan sertifikat jika syarat eligibility lain belum lengkap.

---

## Completion dan Certificate Eligibility Learning

### Completion class

Enrollment dapat diproses menuju `completed` jika seluruh aturan completion class telah dipenuhi. Minimal mencakup komponen wajib yang diaktifkan pada class.

### Eligibility sertifikat learning

Sertifikat learning hanya eligible jika seluruh syarat berikut terpenuhi:

```text
class.certificate_enabled = true
+ enrollment.status sah dan bukan withdrawn/cancelled
+ seluruh lesson wajib completed
+ pre-test selesai bila pre-test aktif
+ quiz wajib selesai
+ assignment wajib graded bila assignment aktif/wajib
+ post-test selesai bila post-test aktif
+ final_score >= passing_grade
+ feedback peserta submitted bila diwajibkan
+ tidak ada administrative hold
+ belum ada certificate dari enrollment yang sama
```

Jika eligibility terpenuhi:

```text
enrollments.certificate_eligible_at = timestamp UTC
```

Eligibility harus dihitung ulang jika terjadi perubahan grade, progress, enrollment status, class policy, certificate enablement, atau administrative hold.

### Administrative hold

Administrative hold adalah kondisi internal yang mencegah issue sertifikat meskipun nilai dan completion terpenuhi, misalnya investigasi, data wajib belum valid, atau workflow tertentu belum selesai. Hold harus memiliki alasan, actor, state, dan audit sesuai policy approval/workflow.

---

## Sertifikat Event

### Eligibility event

Sertifikat event hanya eligible jika:

```text
event.certificate_enabled = true
+ event.status = finished
+ event_registration.status = registered
+ attendance.status = present
+ belum ada certificate dari event_registration yang sama
```

Status `waitlisted`, `cancelled`, atau `rejected` tidak eligible.

Jika attendance dikoreksi setelah sertifikat diterbitkan, system/admin harus melakukan re-evaluasi dan dapat memicu revocation sesuai policy serta audit.

---

## Lifecycle Sertifikat

### Status sertifikat

```text
pending
issued
revoked
expired
```

### Transisi

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — | Evaluasi eligibility | `pending` | System/Certificate service | Source learning/event valid dan eligible |
| `pending` | Issue | `issued` | System/Admin dengan `certificate.issue` | Eligibility dicek ulang; number/token/PDF valid |
| `issued` | Expire | `expired` | System | Hanya bila expiry digunakan dan masa berlaku berakhir |
| `issued` / `expired` | Revoke | `revoked` | Admin berwenang | Alasan wajib; audit wajib |
| `revoked` | Replace | Record pengganti `issued` | System/Admin berwenang | Riwayat replacement dan alasan tercatat |

### Penerbitan

Penerbitan sertifikat dilakukan oleh `CertificateGenerationService` setelah `CertificateEligibilityService` menyatakan eligible.

Urutan minimum:

```text
1. Lock/evaluate source eligibility.
2. Pastikan belum ada certificate untuk (source_type, source_id).
3. Buat certificate status pending.
4. Generate nomor sertifikat unik.
5. Generate verification token acak dan simpan hash token.
6. Generate QR code dari data verifikasi yang aman.
7. Generate PDF sertifikat.
8. Simpan PDF sebagai FileAsset restricted.
9. Ubah certificate menjadi issued.
10. Commit transaction, tulis audit, dan kirim notifikasi aman bila dikonfigurasi.
```

Constraint utama:

```text
UNIQUE(source_type, source_id)
UNIQUE(certificate_number)
UNIQUE(verification_token_hash)
```

### Nomor sertifikat

Format final nomor sertifikat ditetapkan oleh konfigurasi `Certificate.php`. Nomor wajib unik, tidak dapat dipakai ulang, dan harus dapat ditelusuri melalui record sertifikat serta audit.

### PDF dan QR code

- PDF sertifikat disimpan sebagai `file_assets` dengan visibility `restricted` secara default.
- Public verification token yang valid tidak otomatis memberikan file download PDF.
- QR code tidak boleh memuat data personal/restricted secara berlebihan; QR dapat menyimpan URL verifikasi publik atau token terkontrol.
- PDF tidak boleh diregenerasi secara diam-diam tanpa audit. Regenerate atau replacement harus meninggalkan jejak audit/workflow.

---

## Verifikasi Sertifikat Publik

### Route

```text
GET  /sertifikat/verifikasi
POST /sertifikat/verifikasi
GET  /sertifikat/verifikasi/{token}
```

### Aturan verifikasi

- Token input harus diproses melalui hash lookup; plain token tidak disimpan database.
- Rate limit wajib diterapkan untuk mengurangi enumeration/abuse.
- Hasil verifikasi hanya menampilkan data minimum yang diperlukan, misalnya:

```text
status sertifikat
nomor sertifikat
nama pemilik secara proporsional sesuai policy
jenis/sumber sertifikat
program/course/class atau event bila relevan
tanggal terbit
masa berlaku bila ada
status revoked/expired bila berlaku
```

- Hasil verifikasi tidak menampilkan NIK, NIP, email, nomor telepon, dokumen bukti, nilai detail, submission, atau metadata file private.
- Sertifikat `issued` dan belum expired/revoked dinyatakan valid.
- Sertifikat `revoked` atau `expired` tetap dapat menghasilkan halaman verifikasi dengan status yang jelas; tidak diperlakukan sebagai valid aktif.
- Verification public tidak memberikan akses Dashboard/Workspace ataupun file PDF restricted.

---

## Revocation dan Replacement

### Revocation

Sertifikat dapat dicabut jika terdapat alasan sah, misalnya kesalahan penerbitan, pembatalan kelulusan akibat koreksi resmi, fraud, atau kebijakan administratif yang didokumentasikan.

Ketentuan:

```text
certificate.status = revoked
certificate.revoked_at = timestamp UTC
certificate.revoked_by = actor berwenang
certificate.revocation_reason = alasan wajib
```

- Record sertifikat tidak dihapus.
- PDF lama tidak boleh dianggap valid; akses file mengikuti policy certificate/file.
- Halaman verifikasi publik menampilkan status dicabut secara proporsional.
- Audit dan notifikasi aman wajib dibuat bila relevan.

### Replacement

Jika sertifikat pengganti harus diterbitkan:

```text
sertifikat lama
→ revoked
→ replaced_by_certificate_id mengarah ke sertifikat baru

sertifikat baru
→ issued
→ source sama hanya melalui policy replacement yang terkendali
```

Karena terdapat `UNIQUE(source_type, source_id)`, implementasi replacement harus menggunakan strategi transaction yang menjaga uniqueness, misalnya memperbarui record/relasi sesuai rancangan migration atau menggunakan source alias/version policy yang terdokumentasi. Tidak boleh membuat dua sertifikat aktif untuk sumber yang sama.

---

## Audit dan Notifikasi

### Audit wajib

- Aktivasi/cancel/withdraw/completion enrollment.
- Perubahan class assessment policy yang berdampak nilai/eligibility.
- Quiz attempt start/submit/grade/invalidate bila policy audit mensyaratkan.
- Submission create/submit/revision/grade/cancel.
- Perubahan nilai atau feedback setelah submission telah graded.
- Perhitungan final score, pass/fail, dan certificate eligibility.
- Certificate pending/issued/expired/revoked/replaced.
- Public verification event bila policy audit mensyaratkan.
- Download PDF sertifikat restricted bila policy file access mensyaratkan.

Audit tidak boleh menyimpan isi jawaban quiz, assignment, plain verification token, PDF content, password, atau data restricted berlebihan.

### Notifikasi yang dapat dikirim

- Enrollment active/cancelled/withdrawn.
- Assignment tersedia, deadline, revisi, atau graded.
- Quiz/post-test tersedia atau hasil tersedia bila policy mengizinkan.
- Completion/kelulusan/eligibility sertifikat.
- Sertifikat issued, expired, revoked, atau replaced.
- Perubahan event registration/waitlist/attendance yang memengaruhi sertifikat event.

Notifikasi tidak menjadi sumber kebenaran status. Status selalu dibaca dari record domain dan workflow yang sah.

---

## Data Privacy dan Retention

### Data restricted

```text
quiz_options.is_correct
quiz_answers.answer_payload
quiz_attempts.score
assignment_submissions.content
assignment_submissions.score
assignment_submissions.feedback
assignment_submission_files
learning enrollment progress detail bila bersifat personal
certificate verification_token_hash
certificate PDF file asset
revocation reason bila mengandung data sensitif
```

### Retention

- Assessment, submission, grade, dan feedback dipertahankan sesuai policy pembelajaran, audit, dan kebutuhan sertifikasi.
- Sertifikat dan riwayat revocation/replacement tidak dihapus hanya untuk menyembunyikan riwayat.
- File assignment mengikuti file-storage policy dan tidak boleh dipindahkan ke public asset root.
- Data agregat dapat digunakan untuk Statistik Publik hanya setelah disclosure control; data individual tidak diekspos.

---

## Error dan Edge Case

| Kondisi | Perilaku wajib |
|---|---|
| Participant membuka assessment tanpa enrollment aktif | Tolak dengan authorization/error sesuai route policy |
| Attempt melebihi limit | Tolak tanpa membuat attempt baru |
| Submit setelah deadline | Tolak kecuali late submission policy eksplisit mengizinkan |
| Evaluator menilai submission di luar assignment | Tolak dan catat anomaly bila policy mengharuskan |
| Nilai berubah setelah certificate issued | Re-evaluasi eligibility; gunakan workflow koreksi/revocation bila diperlukan |
| Event over capacity | Gunakan transaction/lock; registered atau waitlisted secara atomik |
| Sertifikat source sama diterbitkan bersamaan | Unique constraint + transaction mencegah duplicate issue |
| Token verifikasi tidak valid/kedaluwarsa | Tampilkan hasil aman tanpa membocorkan data sertifikat |
| PDF certificate tidak tersedia | Status sertifikat tetap dapat diverifikasi; download PDF mengikuti file lifecycle/policy |

---

## Checklist Implementasi

Sebelum assessment/certificate dinyatakan `Backend Ready`, pastikan:

- [ ] Enrollment, assignment Trainer/Evaluator, dan ownership dipastikan sebelum akses assessment.
- [ ] Attempt limit, deadline, status, dan availability divalidasi server-side.
- [ ] Auto-grading dan final score dihitung server-side.
- [ ] Bobot class selalu total 100 dan post-test weight baseline 60.
- [ ] Assignment inactive tidak dihitung sebagai nol; bobot class disesuaikan.
- [ ] Eligibility certificate memeriksa semua lesson/assessment/feedback/admin hold yang diwajibkan.
- [ ] Penerbitan sertifikat memakai transaction, unique constraint, audit, dan secure token hash.
- [ ] PDF sertifikat disimpan restricted dan tidak otomatis public.
- [ ] Verifikasi publik memakai rate limit dan hanya menampilkan data minimum aman.
- [ ] Revocation/replacement mempertahankan riwayat dan tidak membuat dua sertifikat aktif untuk sumber sama.
- [ ] Perubahan nilai/attendance yang memengaruhi sertifikat memicu re-evaluation.
- [ ] Feature test mencakup enrollment, attempt limit, grading authorization, final score, eligibility, issue, verification, revoke, replacement, dan event certificate.

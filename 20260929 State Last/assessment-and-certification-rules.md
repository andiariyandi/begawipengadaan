# BegawiPBJ Assessment and Certification Rules

## Aturan Assessment, Kelulusan, dan Sertifikat Arena Belajar BegawiPBJ

Dokumen ini menetapkan aturan formal assessment, kelulusan, completion, feedback, dan penerbitan sertifikat untuk **Arena Belajar BegawiPBJ**. Aturan ini berlaku untuk seluruh class pembelajaran yang menggunakan mekanisme evaluasi digital di lingkungan BegawiPBJ.

Dokumen ini menjadi sumber aturan bisnis untuk controller, service, migration, model, test, UI peserta, UI Trainer/Evaluator/Operator, Dashboard Admin, serta proses penerbitan sertifikat. Dokumen ini harus konsisten dengan:

```text
README.md
begawipbj-directory-architecture.md
begawipbj-database-blueprint.md
begawipbj-database-erd.md
begawipbj-data-dictionary.md
```

---

# 1. Tujuan dan Prinsip

## 1.1 Tujuan

Aturan ini dibuat untuk memastikan bahwa evaluasi pembelajaran:

- Memiliki hasil yang adil, dapat diulang, dan dapat diaudit.
- Memisahkan fungsi diagnostik pre-test dari nilai kelulusan.
- Menggunakan auto-grading untuk assessment objektif.
- Menggunakan manual grading dan feedback evaluator untuk assignment.
- Mengakomodasi revisi dan upload ulang assignment secara tertib.
- Menghasilkan nilai akhir yang transparan dan konsisten.
- Menerbitkan sertifikat hanya kepada peserta yang benar-benar memenuhi seluruh syarat eligibility.
- Menjaga jejak data assessment, grading, dan sertifikat tanpa penghapusan riwayat yang tidak sah.

## 1.2 Prinsip normatif

- Nilai berada pada skala **0 sampai 100**.
- Passing grade class minimal adalah **70**. Class dapat menetapkan passing grade di atas 70, tetapi tidak boleh di bawah 70.
- Pre-test hanya bersifat diagnostik dan **tidak dihitung** ke nilai akhir.
- Quiz lesson dan post-test menggunakan **best attempt**.
- Post-test memiliki kontribusi tetap **60%** pada nilai akhir ketika post-test diaktifkan.
- Sisa bobot **40%** dibagi antara quiz dan assignment.
- Assignment bersifat opsional pada level class. Jika assignment diaktifkan/tersedia, assignment menjadi **wajib** untuk completion dan certificate eligibility.
- Assignment dinilai manual oleh evaluator yang ditugaskan atau actor lain yang memiliki permission sah.
- Peserta dapat melakukan revisi/upload ulang jika evaluator meminta revisi dan konfigurasi assignment mengizinkan resubmission.
- Sertifikat bukan otomatis hanya karena peserta pernah enrollment; sertifikat diterbitkan setelah semua syarat eligibility dipenuhi dan proses penerbitan selesai.
- Seluruh perubahan signifikan harus memiliki audit trail.

---

# 2. Istilah dan Entitas

| Istilah | Definisi |
|---|---|
| Program | Kelompok course pembelajaran |
| Course | Unit pembelajaran utama yang mempunyai materi dan dapat dilaksanakan dalam beberapa class |
| Class | Pelaksanaan course pada angkatan/periode/jadwal tertentu |
| Enrollment | Keikutsertaan seorang peserta pada satu class |
| Lesson | Unit materi di dalam module course |
| Pre-test | Evaluasi baseline sebelum/awal pembelajaran; satu kali; tidak dihitung |
| Quiz lesson | Evaluasi objektif yang terkait lesson dan dinilai otomatis |
| Assignment | Tugas teks dan/atau upload file yang dinilai manual |
| Submission | Satu versi pengumpulan assignment oleh peserta |
| Revision | Permintaan evaluator agar peserta memperbaiki tugas dan melakukan submission baru |
| Post-test | Evaluasi akhir objektif; best attempt; bobot 60% |
| Final grade | Snapshot nilai akhir, status lulus, completion, dan certificate eligibility per enrollment |
| Completion | Pemenuhan seluruh aktivitas/komponen wajib pada class |
| Participant feedback | Feedback peserta untuk class; dapat menjadi syarat sertifikat |
| Certificate eligibility | Status bahwa enrollment memenuhi syarat substantif dan administratif untuk sertifikat |

---

# 3. Konfigurasi Assessment Class

## 3.1 Sumber konfigurasi

Setiap class memiliki tepat satu konfigurasi pada `assessment_configurations`.

```text
learning_classes 1 ── 1 assessment_configurations
```

Konfigurasi assessment harus sudah valid sebelum class dibuka untuk enrollment atau sebelum peserta dapat memulai assessment pertama.

Kolom konfigurasi penting:

```text
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
```

## 3.2 Konfigurasi baseline wajib

| Parameter | Nilai/aturan baseline |
|---|---|
| `pre_test_attempt_limit` | `1` |
| `quiz_best_attempt_enabled` | `true` |
| `assignment_required_when_enabled` | `true` |
| `post_test_best_attempt_enabled` | `true` |
| `post_test_weight` | `60.00` saat post-test diaktifkan |
| `passing_grade` | Minimal `70.00` |
| Nilai assessment | Selalu 0–100 |

## 3.3 Aturan bobot

Jika post-test aktif, bobot harus memenuhi:

```text
post_test_weight = 60
quiz_weight + assignment_weight = 40
```

Jika assignment tidak diaktifkan:

```text
assignment_enabled = false
assignment_weight = 0
quiz_weight = 40
post_test_weight = 60
```

Jika assignment diaktifkan, pembagian bobot quiz dan assignment ditentukan pada konfigurasi class, dengan syarat:

```text
quiz_weight >= 0
assignment_weight >= 0
quiz_weight + assignment_weight = 40
```

Contoh konfigurasi valid:

| Model class | Quiz | Assignment | Post-test | Total |
|---|---:|---:|---:|---:|
| Tanpa assignment | 40% | 0% | 60% | 100% |
| Dengan assignment seimbang | 20% | 20% | 60% | 100% |
| Dengan dominasi quiz | 30% | 10% | 60% | 100% |
| Dengan dominasi assignment | 10% | 30% | 60% | 100% |

Konfigurasi tidak valid:

```text
quiz_weight + assignment_weight + post_test_weight != 100
post_test_weight != 60 ketika post-test aktif
passing_grade < 70
assignment_enabled = false tetapi assignment_weight > 0
assignment_enabled = false tetapi quiz_weight != 40 saat post-test aktif
```

## 3.4 Perubahan konfigurasi

- Konfigurasi assessment hanya dapat diubah oleh actor berizin, misalnya Admin atau Trainer dengan permission yang sesuai dan sesuai workflow class.
- Perubahan sebelum peserta melakukan assessment dapat diterapkan setelah validasi dan audit.
- Perubahan bobot atau passing grade setelah peserta memiliki attempt/submission/grade harus dibatasi ketat.
- Jika perubahan setelah assessment berjalan memang diizinkan kebijakan, sistem wajib menyimpan audit before/after, actor, alasan, waktu perubahan, dan menjalankan recalculation final grade terkontrol.
- Perubahan tidak boleh menghapus riwayat attempt, submission, maupun nilai evaluator.

---

# 4. Pre-test

## 4.1 Tujuan

Pre-test digunakan untuk mengukur kemampuan awal/baseline peserta. Pre-test dapat dipakai untuk analisis peningkatan kompetensi, tetapi tidak digunakan sebagai komponen nilai akhir.

## 4.2 Aturan utama

| Aturan | Ketentuan |
|---|---|
| Aktivasi | Berlaku bila `pre_test_enabled = true` |
| Attempt | Maksimal satu attempt untuk setiap enrollment |
| Penilaian | Auto-grading |
| Rentang nilai | 0–100 |
| Nilai akhir | Tidak dihitung |
| Completion | Harus selesai bila diaktifkan dan class menjadikannya komponen wajib |
| Sertifikat | Dapat menjadi syarat completion, tetapi tidak memberi bobot nilai |

Constraint data:

```text
UNIQUE(pre_test_attempts.enrollment_id)
```

## 4.3 Alur pre-test

```text
Enrollment disetujui
↓
Pre-test tersedia bila diaktifkan
↓
Peserta memulai satu attempt
↓
Peserta mengirim jawaban
↓
Sistem menjalankan auto-grading
↓
Nilai baseline 0–100 tersimpan
↓
Pre-test ditandai selesai dan tidak dapat diulang
```

## 4.4 Larangan

- Peserta tidak dapat membuat attempt pre-test kedua.
- Nilai pre-test tidak boleh ditambahkan ke formula `final_score`.
- Pre-test tidak boleh diubah menjadi post-test atau quiz lesson setelah attempt peserta dimulai tanpa prosedur migrasi/audit yang disetujui.
- Actor tidak boleh mengubah nilai pre-test manual kecuali terdapat kebijakan koreksi insiden yang terdokumentasi, dengan audit wajib.

---

# 5. Quiz per Lesson

## 5.1 Tujuan

Quiz mengukur pemahaman peserta terhadap lesson tertentu. Quiz menggunakan pertanyaan objektif dan dinilai otomatis.

Jenis pertanyaan baseline:

```text
multiple_choice
multiple_response
true_false
```

## 5.2 Aturan utama

| Aturan | Ketentuan |
|---|---|
| Kepemilikan | Quiz lesson terhubung ke `lessons` |
| Penilaian | Auto-grading |
| Rentang nilai | 0–100 |
| Attempt | Mengikuti `quiz_attempt_limit` class/quiz |
| Nilai yang dipakai | Best attempt |
| Quiz wajib | Masuk completion dan agregasi sesuai konfigurasi |
| Quiz tidak wajib | Dapat dicatat untuk progres; masuk nilai akhir hanya jika konfigurasi menyatakannya |

Constraint:

```text
UNIQUE(quiz_id, enrollment_id, attempt_number)
UNIQUE(quiz_attempt_id, question_id)
```

## 5.3 Best attempt

Nilai quiz yang dipakai untuk agregasi adalah nilai attempt tertinggi yang telah selesai dan sah.

```text
best_quiz_attempt_score = MAX(score)
```

Jika dua atau lebih attempt memiliki skor tertinggi yang sama, sistem dapat memakai attempt paling awal sebagai referensi tampilan; nilai tetap sama.

Attempt yang tidak selesai, expired, dibatalkan, atau dinyatakan invalid tidak boleh masuk perhitungan best attempt.

## 5.4 Agregasi quiz class

Jika sebuah class mempunyai lebih dari satu quiz yang masuk nilai akhir, nilai `quiz_score` dihitung sebagai rerata nilai best attempt dari seluruh quiz wajib yang eligible untuk dihitung.

```text
quiz_score = SUM(best_attempt_score setiap quiz yang dihitung)
             / jumlah_quiz_yang_dihitung
```

Syarat agregasi:

- Quiz harus published dan relevan dengan course/class peserta.
- Quiz wajib yang diaktifkan harus telah selesai agar `all_required_assessments_completed = true`.
- Jika class tidak memiliki quiz yang dihitung, maka `quiz_weight` harus `0` atau konfigurasi class harus ditolak sebelum dibuka.

## 5.5 Auto-grading

- Pertanyaan `multiple_choice` mempunyai satu opsi benar.
- Pertanyaan `multiple_response` dapat mempunyai lebih dari satu opsi benar.
- Pertanyaan `true_false` menggunakan opsi/kunci objektif.
- Sistem menghitung skor berdasarkan `score_weight` setiap question dan menormalkan nilai menjadi 0–100.
- Setelah attempt submitted, jawaban dan hasil grading disimpan sebagai bukti audit. Perubahan question bank sesudahnya tidak boleh mengubah attempt historis tanpa proses versioning/recalculation yang disetujui.

---

# 6. Assignment

## 6.1 Status assignment

Assignment bersifat opsional pada level class.

```text
assignment_enabled = false
→ Tidak ada assignment wajib
→ assignment_weight = 0
→ quiz_weight = 40 saat post-test aktif

assignment_enabled = true
→ Assignment tersedia
→ Assignment yang berlaku wajib diselesaikan
→ assignment_weight dapat lebih dari 0
→ Manual grading dan revisi berlaku
```

Jika assignment diaktifkan tetapi assignment belum dibuat atau belum aktif, class tidak boleh dibuka untuk peserta apabila bobot assignment lebih dari 0 atau assignment diwajibkan untuk eligibility.

## 6.2 Assignment wajib

Ketika assignment aktif, setiap assignment yang berstatus aktif dan `is_required = true` wajib diselesaikan oleh peserta.

Sebuah assignment dianggap selesai jika:

1. Peserta mempunyai minimal satu submission valid.
2. Submission final telah dinilai manual.
3. Status submission final adalah `graded`.
4. Tidak ada permintaan revisi aktif yang belum dipenuhi.
5. Nilai assignment final tersedia pada `assignment_grades.score`.

## 6.3 Submission dan revision

Setiap pengumpulan diberi nomor versi (`submission_number`). Submission lama tidak ditimpa.

```text
Submission 1
↓
Evaluator menilai
↓
Jika perlu: revision_required
↓
Peserta membuat Submission 2
↓
Evaluator menilai ulang
↓
Status graded
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

Aturan revision:

- Revisi hanya dapat diminta oleh evaluator/actor berizin.
- Permintaan revisi harus menyertakan feedback atau alasan yang cukup bagi peserta.
- `revision_requested_at` dan, bila berlaku, `resubmission_due_at` harus disimpan.
- Peserta dapat upload ulang jika `allow_resubmission = true` dan belum melampaui `max_submission_count` atau aturan class.
- Submission revisi disimpan sebagai baris baru dengan `submission_number` berikutnya.
- Nilai pada submission terdahulu tetap tersimpan untuk audit, tetapi nilai akhir assignment menggunakan submission final yang dipilih sesuai aturan.

Constraint:

```text
UNIQUE(assignment_id, enrollment_id, submission_number)
UNIQUE(assignment_submission_id) pada assignment_grades
```

## 6.4 Manual grading

Manual grading dilakukan oleh Evaluator yang ditugaskan di `class_evaluators` atau actor yang memiliki permission grading sah.

Evaluator mengisi minimal:

```text
score       0–100
feedback    opsional secara teknis, tetapi wajib bila meminta revisi
graded_at   timestamp UTC
```

Aturan:

- Nilai manual harus berada pada rentang 0–100.
- Nilai tidak boleh diterbitkan untuk submission milik enrollment pada class di luar penugasan evaluator, kecuali actor memiliki permission override yang terdokumentasi.
- Setelah nilai diberikan, status submission menjadi `graded` atau `revision_required`.
- Koreksi nilai sesudah status `graded` wajib mencatat audit before/after, alasan, actor, dan menjalankan recalculation final grade.

## 6.5 Agregasi assignment

Jika class mempunyai lebih dari satu assignment yang dihitung, nilai `assignment_score` adalah rerata nilai final seluruh assignment wajib yang aktif dan dihitung.

```text
assignment_score = SUM(final_assignment_score setiap assignment yang dihitung)
                   / jumlah_assignment_yang_dihitung
```

Jika assignment tidak diaktifkan:

```text
assignment_score = 0 untuk tujuan penyimpanan/tampilan
assignment_weight = 0
```

Nilai `0` pada kondisi assignment nonaktif tidak boleh ditafsirkan sebagai kegagalan peserta; final score menggunakan bobot assignment `0`.

---

# 7. Post-test

## 7.1 Tujuan

Post-test mengukur capaian peserta setelah menyelesaikan pembelajaran. Post-test menggunakan auto-grading dan merupakan komponen nilai terbesar pada baseline BegawiPBJ.

## 7.2 Aturan utama

| Aturan | Ketentuan |
|---|---|
| Aktivasi | Berlaku bila `post_test_enabled = true` |
| Penilaian | Auto-grading |
| Rentang nilai | 0–100 |
| Attempt | Mengikuti `post_test_attempt_limit` |
| Nilai yang dipakai | Best attempt |
| Bobot | Tepat 60% |
| Completion | Harus selesai bila diaktifkan |
| Eligibility sertifikat | Menjadi syarat bila aktif |

Constraint:

```text
UNIQUE(enrollment_id, attempt_number)
```

## 7.3 Best attempt

```text
best_post_test_score = MAX(score dari attempt post-test yang completed dan valid)
```

Attempt `in_progress`, `expired`, `cancelled`, atau invalid tidak dapat dipakai untuk nilai akhir.

## 7.4 Akses post-test

Aturan pembukaan post-test dapat dikonfigurasi class, namun baseline merekomendasikan post-test baru tersedia setelah:

- Lesson wajib selesai.
- Quiz wajib selesai.
- Assignment wajib berstatus `graded`, jika assignment aktif.
- Pre-test selesai, jika pre-test aktif.

Operator/Admin dapat memiliki permission untuk membuka atau mereset akses post-test pada kasus administratif yang terdokumentasi. Tindakan ini harus diaudit.

---

# 8. Nilai Akhir dan Kelulusan

## 8.1 Komponen nilai

| Komponen | Nilai | Bobot | Catatan |
|---|---:|---:|---|
| Pre-test | 0–100 | 0% | Diagnostik; tidak dihitung |
| Quiz lesson | 0–100 | 0–40% | Best attempt dan agregasi quiz class |
| Assignment | 0–100 | 0–40% | Manual grading; 0% bila tidak tersedia |
| Post-test | 0–100 | 60% | Best attempt bila diaktifkan |

## 8.2 Formula final score

Formula umum:

\[
final\_score =
(quiz\_score \times quiz\_weight / 100) +
(assignment\_score \times assignment\_weight / 100) +
(post\_test\_score \times post\_test\_weight / 100)
\]

Dengan baseline post-test aktif:

\[
post\_test\_weight = 60
\]

\[
quiz\_weight + assignment\_weight = 40
\]

## 8.3 Contoh perhitungan

### Class tanpa assignment

Konfigurasi:

```text
quiz_weight = 40
assignment_weight = 0
post_test_weight = 60
```

Nilai peserta:

```text
quiz_score = 80
post_test_score = 75
```

Perhitungan:

\[
final\_score = (80 \times 40 / 100) + (0 \times 0 / 100) + (75 \times 60 / 100)
\]

\[
final\_score = 32 + 0 + 45 = 77
\]

### Class dengan assignment

Konfigurasi:

```text
quiz_weight = 20
assignment_weight = 20
post_test_weight = 60
```

Nilai peserta:

```text
quiz_score = 85
assignment_score = 70
post_test_score = 80
```

Perhitungan:

\[
final\_score = (85 \times 20 / 100) + (70 \times 20 / 100) + (80 \times 60 / 100)
\]

\[
final\_score = 17 + 14 + 48 = 79
\]

## 8.4 Kelulusan

Peserta dinyatakan lulus hanya jika seluruh syarat berikut terpenuhi:

1. Enrollment berstatus `approved` atau `completed` dan tidak `cancelled`/`withdrawn`.
2. Semua lesson wajib telah selesai.
3. Pre-test telah selesai jika diaktifkan.
4. Semua quiz wajib telah selesai jika diaktifkan.
5. Semua assignment wajib telah memperoleh status `graded` jika assignment diaktifkan.
6. Post-test telah selesai jika diaktifkan.
7. Final score lebih besar atau sama dengan passing grade class.
8. Passing grade class minimal 70.

Aturan nilai:

\[
is\_passed = final\_score \ge passing\_grade
\]

Peserta dengan nilai akhir cukup tetapi masih memiliki komponen wajib yang belum selesai belum boleh dianggap complete atau certificate eligible.

## 8.5 Final grade snapshot

`final_grades` menyimpan snapshot hasil akhir per enrollment.

```text
UNIQUE(final_grades.enrollment_id)
```

Kolom kunci:

```text
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
```

Perhitungan dijalankan oleh `LearningCompletionService`. Sistem dapat melakukan recalculation setelah perubahan nilai yang sah, tetapi perubahan hasil wajib diaudit.

---

# 9. Completion dan Feedback Peserta

## 9.1 Completion

Completion bukan hanya nilai akhir. Enrollment dianggap complete bila seluruh aktivitas wajib telah selesai sesuai konfigurasi class.

Syarat minimal completion:

```text
Semua lesson wajib selesai
+ Pre-test selesai jika aktif
+ Quiz wajib selesai jika aktif
+ Assignment wajib graded jika aktif
+ Post-test selesai jika aktif
```

`all_required_assessments_completed` bernilai `true` hanya jika semua komponen assessment wajib terpenuhi.

## 9.2 Feedback peserta

Feedback peserta disimpan pada `participant_feedbacks`, maksimum satu per enrollment.

```text
UNIQUE(participant_feedbacks.enrollment_id)
```

Feedback dapat memuat:

```text
rating          opsional sesuai desain survei
feedback_text   opsional/required sesuai konfigurasi survei
submitted_at    wajib saat dikirim
```

Jika `participant_feedback_required = true`, maka peserta harus mengirim feedback sebelum menjadi certificate eligible.

Aturan:

- Feedback peserta tidak memengaruhi final score.
- Feedback peserta dapat diubah sebelum final submit jika UI mendukung draft; setelah submit, perubahan harus mengikuti kebijakan audit.
- Data feedback hanya dapat dibaca sesuai permission dan kebijakan anonimisasi/privasi yang berlaku.

---

# 10. Certificate Eligibility

## 10.1 Prinsip

Certificate eligibility adalah validasi substantif sebelum sertifikat diterbitkan. Status ini tidak sama dengan status penerbitan sertifikat.

```text
certificate_eligible = true
≠
certificate sudah issued
```

Eligibility menjawab: “Apakah peserta memenuhi semua syarat?”

Penerbitan menjawab: “Apakah sertifikat resmi sudah dibuat dan diterbitkan?”

## 10.2 Syarat eligibility learning

Enrollment dapat menjadi certificate eligible bila seluruh kondisi berikut bernilai benar:

```text
assessment_configurations.certificate_enabled = true
AND enrollment valid serta tidak cancelled/withdrawn
AND all_required_lessons_completed = true
AND all_required_assessments_completed = true
AND final_grades.is_passed = true
AND final_score >= passing_grade
AND participant_feedback_completed = true jika feedback diwajibkan
AND tidak ada administrative hold yang aktif
AND belum ada certificate resmi dari source enrollment yang sama
```

Aturan duplicate prevention:

```text
UNIQUE(certificates.source_type, certificates.source_id)
```

Untuk learning:

```text
source_type = learning_enrollment
source_id = enrollments.id
```

## 10.3 Eligibility event

Untuk event, eligibility ditentukan aturan event, minimal:

```text
event.certificate_enabled = true
AND event_registration.registration_status = registered
AND event_attendances.attendance_status = present
AND event.event_status = finished
AND belum ada certificate resmi dari source event registration yang sama
```

Untuk event:

```text
source_type = event_registration
source_id = event_registrations.id
```

## 10.4 Administrative hold

Administrative hold adalah kondisi pembatasan penerbitan yang sah, misalnya:

- Enrollment sedang dalam sengketa/verifikasi data.
- Assignment atau nilai sedang dikoreksi melalui prosedur resmi.
- Sertifikat class/event belum memperoleh approval penerbitan yang diwajibkan.
- Akun peserta mengalami status yang memerlukan pemeriksaan administratif, tanpa menghapus bukti pembelajaran.

Hold harus memiliki alasan, actor, waktu, status, dan audit trail. Hold tidak boleh dipakai untuk mengubah nilai peserta secara sembunyi-sembunyi.

---

# 11. Penerbitan Sertifikat

## 11.1 Sumber sertifikat

Tabel `certificates` menangani sertifikat pembelajaran dan event.

```text
learning_enrollment
 event_registration
```

Tidak ada tabel sertifikat event terpisah.

## 11.2 Status sertifikat

```text
pending
issued
revoked
expired
```

| Status | Arti |
|---|---|
| `pending` | Record sertifikat telah dipersiapkan tetapi belum resmi diterbitkan atau PDF belum selesai dibuat |
| `issued` | Sertifikat resmi diterbitkan dan dapat diverifikasi sesuai kebijakan publik |
| `revoked` | Sertifikat dibatalkan; record tetap ada untuk verifikasi dan audit |
| `expired` | Masa berlaku sertifikat telah berakhir bila `expires_at` digunakan |

## 11.3 Alur penerbitan learning

```text
Peserta menyelesaikan semua komponen wajib
↓
LearningCompletionService menghitung ulang final grade
↓
CertificateEligibilityService memvalidasi eligibility
↓
Approval penerbitan dijalankan bila diwajibkan
↓
Certificate number dibuat secara transaction dan locking sequence
↓
Record certificate dibuat/diubah menjadi pending
↓
PDF dan QR code dihasilkan
↓
file_assets disimpan atau diperbarui
↓
Certificate status menjadi issued
↓
Peserta menerima notifikasi
```

## 11.4 Nomor, token, dan QR code

Ketentuan:

- `certificate_number` harus unik.
- `verification_token` harus unik, acak, cukup panjang, dan tidak memuat NIP, NIK, email, atau data personal.
- QR code memuat URL/token verifikasi, bukan data personal lengkap.
- Pembuatan sequence nomor dilakukan dalam database transaction dan locking untuk mencegah nomor ganda pada request paralel.

Constraint:

```text
UNIQUE(certificate_number)
UNIQUE(verification_token)
UNIQUE(source_type, source_id)
UNIQUE(certificate_sequences.sequence_scope, certificate_sequences.year)
```

## 11.5 Dokumen sertifikat

- PDF sertifikat disimpan sebagai `file_assets` dengan storage privat/protected.
- Jika record sertifikat telah ada tetapi PDF belum siap, `file_asset_id` dapat masih `NULL` atau merujuk file asset dengan `path = NULL`.
- UI peserta tidak boleh menampilkan tombol download aktif bila file belum tersedia.
- Regenerasi PDF tidak menciptakan sertifikat resmi kedua dan tidak mengubah nomor/token tanpa alasan kebijakan yang kuat.
- Regenerasi wajib dicatat pada audit log.

---

# 12. Verifikasi, Expiry, dan Revocation

## 12.1 Verifikasi publik

Verifikasi sertifikat dilakukan melalui token atau QR code pada area publik.

Hasil minimum yang dapat ditampilkan mengikuti klasifikasi data dan kebijakan privasi, misalnya:

```text
status sertifikat
nomor sertifikat
jenis sertifikat
waktu penerbitan
masa berlaku bila ada
nama penerima dalam bentuk yang diizinkan kebijakan
```

Sistem mencatat hasil verifikasi pada `certificate_verification_logs`:

```text
valid
revoked
expired
not_found
```

## 12.2 Expiry

Jika sertifikat memiliki masa berlaku, sistem mengevaluasi:

```text
current_time > expires_at
→ status efektif expired
```

Perubahan status expiry dapat dijalankan terjadwal atau dihitung saat verifikasi, sesuai desain implementasi. Riwayat penerbitan tetap dipertahankan.

## 12.3 Revocation

Sertifikat tidak dihapus ketika dibatalkan. Revocation disimpan pada `certificate_revocations`.

```text
certificate_id                  UNIQUE
revoked_by
revoked_at
reason
replacement_certificate_id
```

Aturan:

- Revocation hanya dilakukan actor berizin.
- Alasan pembatalan wajib diisi.
- Status sertifikat menjadi `revoked`.
- Halaman verifikasi publik harus menunjukkan bahwa sertifikat tidak lagi valid.
- Jika sertifikat pengganti diterbitkan, relasi `replacement_certificate_id` dicatat.
- Semua tindakan revoke/reissue dicatat pada audit log dan, bila digunakan, approval workflow.

---

# 13. Role dan Tanggung Jawab

| Actor | Tanggung jawab assessment/sertifikat |
|---|---|
| Peserta | Menyelesaikan lesson, pre-test, quiz, assignment, revision, post-test, feedback, dan mengakses sertifikat pribadi |
| Trainer | Mengelola materi/quiz/assignment sesuai penugasan, memantau progres, tanpa mengambil alih grading evaluator kecuali diberi permission |
| Evaluator | Menilai assignment, memberi feedback, meminta revisi, dan melakukan koreksi sesuai penugasan/permission |
| Operator | Mendukung enrollment, verifikasi administratif, absensi, dan proses delegasi yang sah |
| Admin | Mengelola konfigurasi, monitor assessment, approval, sertifikat, koreksi administratif sesuai permission |
| Super Admin | Mengelola konfigurasi sistem, role/permission, audit, dan override terbatas sesuai tata kelola |
| Sistem | Menjalankan auto-grading, aggregation, recalculation, token/nomor sertifikat, serta notifikasi terjadwal |

Aturan keamanan:

- Role tidak menggantikan permission.
- Permission tidak menggantikan ownership/penugasan class.
- Evaluator hanya boleh menilai submission pada class yang ditugaskan, kecuali override eksplisit yang diaudit.
- Trainer/Evaluator tidak boleh menilai atau mengubah assessment milik sendiri jika kebijakan konflik kepentingan melarangnya.

---

# 14. Audit, Koreksi, dan Exception Handling

## 14.1 Peristiwa yang wajib diaudit

Minimal tindakan berikut dicatat pada `audit_logs`:

```text
learning.class.configure_assessment
learning.pre_test.submit
learning.quiz.submit
learning.assignment.submit
learning.assignment.request_revision
learning.assignment.grade
learning.assignment.grade_correct
learning.post_test.submit
learning.final_grade.calculate
learning.final_grade.recalculate
learning.feedback.submit
certificate.eligibility.check
certificate.issue
certificate.pdf.regenerate
certificate.revoke
certificate.verify
```

## 14.2 Koreksi assessment

Koreksi dapat terjadi karena kesalahan konfigurasi, kesalahan question bank, kesalahan grading, atau insiden teknis. Koreksi harus:

1. Dilakukan actor berizin.
2. Memiliki alasan tertulis.
3. Menyimpan before/after data yang tersanitasi.
4. Menentukan scope peserta/class yang terdampak.
5. Menjalankan recalculation bila komponen nilai akhir berubah.
6. Mengevaluasi ulang eligibility/sertifikat yang sudah pending atau issued.
7. Merekam seluruh langkah pada audit log.

Koreksi tidak boleh menghapus attempt/submission historis untuk menyembunyikan perubahan.

## 14.3 Insiden sertifikat terbit

Jika perubahan nilai menyebabkan sertifikat issued menjadi tidak valid, tindakan harus mengikuti kebijakan administrasi resmi:

```text
validasi perubahan
↓
review/approval bila diwajibkan
↓
revocation sertifikat lama bila diperlukan
↓
penerbitan replacement certificate bila memenuhi syarat
↓
pembaruan halaman verifikasi dan notifikasi peserta
```

---

# 15. Aturan Implementasi Teknis

## 15.1 Transaction boundary

Gunakan transaction untuk minimal proses berikut:

- Approval enrollment yang memengaruhi kuota.
- Penyimpanan/penutupan attempt assessment bila mencakup beberapa jawaban dan skor.
- Request revision dan submission revisi bila mengubah beberapa tabel/file.
- Manual grading dan perubahan status submission.
- Perhitungan/recalculation `final_grades` serta update `enrollments.final_status`.
- Pengecekan eligibility dan penerbitan sertifikat.
- Pengambilan nomor sertifikat dari `certificate_sequences`.
- Revocation dan penerbitan sertifikat pengganti.

## 15.2 Idempotensi

Operasi berikut harus aman terhadap pengulangan request yang tidak disengaja:

- Submit assessment yang sama setelah respons jaringan gagal.
- Recalculation final grade.
- Generate PDF sertifikat.
- Certificate issuance untuk source yang sama.

Constraint database yang mendukung idempotensi:

```text
UNIQUE(pre_test_attempts.enrollment_id)
UNIQUE(final_grades.enrollment_id)
UNIQUE(participant_feedbacks.enrollment_id)
UNIQUE(certificates.source_type, certificates.source_id)
UNIQUE(certificates.certificate_number)
UNIQUE(certificates.verification_token)
```

## 15.3 Otorisasi

Semua operasi assessment dan sertifikat wajib memeriksa:

```text
Authentication
+ Account Status
+ Role
+ Permission
+ Ownership atau Class Assignment
+ Status workflow/domain
```

Menyembunyikan tombol UI tidak cukup sebagai mekanisme keamanan.

## 15.4 Penyimpanan file assignment dan sertifikat

- Upload assignment disimpan pada `writable/uploads/learning/assignments/` atau storage privat setara.
- PDF sertifikat disimpan pada `writable/uploads/certificates/generated/` atau storage privat setara.
- Metadata file memakai `file_assets`.
- `file_assets.path = NULL` berarti file belum tersedia dan download wajib dinonaktifkan.
- Nama fisik file menggunakan UUID/hash, bukan nama dari input pengguna.

---

# 16. Skenario Uji Minimum

## 16.1 Pre-test

- Peserta hanya dapat membuat satu pre-test attempt.
- Nilai pre-test auto-graded berada pada 0–100.
- Nilai pre-test tidak mengubah final score.

## 16.2 Quiz

- Attempt number unik pada quiz/enrollment.
- Best attempt dipakai bila peserta memiliki beberapa attempt.
- Attempt tidak selesai tidak masuk agregasi.
- Quiz wajib yang belum selesai membuat completion assessment gagal.

## 16.3 Assignment

- Assignment nonaktif tidak menjadi syarat completion dan memiliki bobot 0.
- Assignment aktif/wajib belum graded membuat certificate eligibility gagal.
- Evaluator tidak berhak tidak dapat memberi nilai.
- Revision menghasilkan submission baru, bukan menimpa submission lama.
- Nilai manual di luar rentang 0–100 ditolak.

## 16.4 Post-test dan final grade

- Best post-test attempt dipakai pada final score.
- Post-test menyumbang tepat 60% ketika aktif.
- Bobot quiz + assignment harus tepat 40% ketika post-test aktif.
- Passing grade di bawah 70 ditolak.
- Peserta dengan nilai cukup tetapi lesson wajib belum selesai tidak dinyatakan complete/eligible.

## 16.5 Sertifikat

- Sertifikat tidak dapat diterbitkan jika `certificate_eligible = false`.
- Satu enrollment/event registration tidak dapat menghasilkan dua sertifikat resmi.
- Token dan nomor sertifikat unik.
- Sertifikat revoked tetap dapat ditemukan melalui verifikasi sebagai `revoked`.
- Sertifikat dengan file `path = NULL` tidak memiliki download aktif.

---

# 17. Ringkasan Aturan Final

```text
Pre-test
- Satu kali
- Auto-grading 0–100
- Tidak dihitung ke final score

Quiz per lesson
- Auto-grading
- Best attempt
- Bobot mengikuti konfigurasi class

Assignment
- Opsional per class
- Jika tersedia, wajib diselesaikan
- Manual grading
- Mendukung revision dan upload ulang
- Bobot mengikuti konfigurasi class

Post-test
- Auto-grading
- Best attempt
- Bobot tetap 60%

Nilai akhir
- Quiz + assignment = 40%
- Post-test = 60%
- Passing grade minimal 70

Completion dan feedback
- Semua lesson/assessment wajib harus selesai
- Feedback peserta wajib bila diaktifkan

Sertifikat
- Hanya setelah seluruh eligibility terpenuhi
- Satu sertifikat resmi per source
- Memiliki nomor unik, token/QR verifikasi, audit trail, dan mekanisme revocation
```

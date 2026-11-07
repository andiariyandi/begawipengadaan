# BegawiPengadaan — Aturan Penilaian, Evaluasi & Penerbitan E-Sertifikat

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Arsitektur Komponen Evaluasi Pembelajaran (LMS)

Sistem evaluasi pembelajaran dikelola oleh `LearningService`, `GradingService`, dan `CertificateEligibilityService` dengan struktur data baku:
- **Bank Soal Kuis (`quiz_questions`):** Menggunakan kolom kanonikal `prompt` (teks pertanyaan), `points` (bobot nilai), `sort_order`, dan `explanation` (pembahasan kunci).
- **Pilihan Jawaban (`quiz_options`):** Terhubung ke `question_id`, memuat `option_text`, `is_correct` (flag kebenaran), dan `sort_order`.
- **Struktur Jenis Kuis (`quizzes.type`):** Sesuai CHECK constraint: `pre_test`, `lesson_quiz`, dan `post_test`.

---

## 2. Ketahanan Ujian, Server-Side Timer & Retensi Nilai Tertinggi

1. **Penanganan Timer Kedaluwarsa (`$force = true`):** Saat batas durasi pengerjaan kuis (`duration_minutes`) terlampaui di server, sistem otomatis mengeksekusi penutupan paksa (*auto-submit*) dan memberikan penilaian atas jawaban yang telah tersimpan.
2. **Attempt Idempotency & Reload Resilience:** Jika peserta me-reload browser saat kuis sedang berlangsung, sistem melanjutkan attempt aktif yang sama (auto-resume via server timestamp & localStorage) tanpa membuat attempt baru yang duplikat.
3. **Retensi Nilai Tertinggi (*Best Score Policy*):** Pada ujian yang memperbolehkan pengerjaan ulang (*re-attempt*), sistem menyimpan nilai tertinggi yang pernah diperoleh peserta sebagai acuan akhir kelulusan.

---

## 3. Rumus Pembobotan Nilai Akhir & Syarat Kelulusan

Sesuai CHECK constraint `learning_classes_weights_chk` dan `learning_classes_post_test_weight_chk`:
- **Post-Test:** Wajib berbobot **60.00%** (`post_test_weight = 60.00`).
- **Kuis Modul / Tugas:** Berbobot **40.00%** gabungan (`quiz_weight + assignment_weight = 40.00`).
- **Total Bobot:** Mutlak bernilai **100.00%**.
- **Batas Nilai Kelulusan (*Passing Grade*):** Default 70.00 s.d. 80.00 sesuai konfigurasi masing-masing kelas.

---

## 4. Mekanisme Penerbitan E-Sertifikat & TTE Digital

Sertifikat diterbitkan secara otomatis atau melalui antrean batch operasional:
1. **Kelayakan Pembelajaran (`learning_enrollment`):** Status enrollment harus `completed`, kehadiran presensi >= 100%, dan nilai akhir >= passing grade.
2. **Kelayakan Kegiatan Event (`event_registration`):** Status pendaftaran harus `registered` dan presensi terverifikasi `present`.
3. **Penomoran Otomatis:** Menggunakan tabel `certificate_sequences` untuk menghasilkan format nomor resmi (misal: `BGW-CERT/LMS/2026/0001`).
4. **Segel Kriptografis & TTE:** `DigitalSignatureService` menyematkan segel hash SHA-256 dan QR Code verifikasi instan tanpa mengekspos PII peserta.

---

## 5. Fitur Duplikasi Kelas Cepat (`duplicateBatch`)

Operator atau Trainer dapat menduplikasi kelas yang telah selesai menjadi kelas batch baru secara utuh:
- Menyalin struktur silabus modul (`learning_modules`) dan materi (`lessons`).
- Menyalin seluruh kuis (`quizzes`), bank soal (`quiz_questions`), dan opsi pilihan jawaban (`quiz_options`) secara atomik.
- Mereset data pendaftaran peserta dan attempt ujian untuk batch baru.

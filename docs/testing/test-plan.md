# BegawiPBJ Test Plan

Rencana pengujian ini memvalidasi BegawiPBJ sebagai platform PBJ Pemerintah Provinsi Lampung: Main UI untuk Landingpage + Workspace, Dashboard Admin terpisah, RBAC, Arena Belajar, Bunga Rampai, regulasi, event, sertifikat, dan Government Intelligence. Dokumen ini adalah kriteria pengujian, bukan laporan bahwa fitur telah lulus.

## Tujuan dan cakupan

| Lapisan | Tujuan | Contoh |
|---|---|---|
| Unit | Aturan bisnis satu service/komponen | Bobot nilai, transition state, prefix kode wilayah |
| Feature HTTP | Route, filter, view/JSON, input, status | Guest ke `/admin`, login, registrasi, QR verify |
| Integration DB | FK, unique, transaction, seeder | Source sertifikat unik, event quota, FK profil wilayah |
| File integration | Storage privat, metadata, izin | `path NULL`, MIME palsu, traversal, missing physical file |
| Security | Auth, CSRF, ownership, privasi | Peserta membaca submission orang lain, akses template referensi |
| Performance | Waktu respons dan indeks | Katalog/search, agregasi 49 unit, notifikasi |
| UAT | Alur nyata menurut role | Pendaftaran kelas hingga sertifikat; event hingga absensi |

## Prasyarat lingkungan

- Database testing terisolasi; jangan memakai database production.
- Migration schema telah berjalan dan master `wilayah(kode,nama)` berisi kode `18` untuk fixture Lampung.
- Seeder/factory test membuat akun sintetik dan organisasi sesuai cakupan provinsi; tidak memakai data ASN nyata.
- `app/Views/reference/fistudy/` dan `reference/wowdash/` tidak dirender route produksi.
- File test disimpan di direktori sementara dengan cleanup setelah test.
- Password test dibuat dengan `password_hash()` dan tidak memakai dummy hash non-login.
- Waktu test dapat dibekukan/fake untuk kuota, deadline, tanggal kelas, expired sertifikat, dan event.
- CI/CD menjalankan test otomatis sebelum merge dan sebelum deployment.

## Prioritas risiko

| Prioritas | Kategori | Alasan |
|---|---|---|
| P0 | Akses Admin/Workspace, ownership, NIP/NIK, sertifikat ganda, import schema | Kebocoran/kerusakan data dan integritas layanan |
| P1 | Grading, approval, kuota event, regulasi versi, file akses | Dampak langsung ke operasi PBJ |
| P2 | Pencarian, pagination, notifikasi, tampilan responsif | Kualitas dan kegunaan |

P0 dan P1 harus lulus sebelum rilis. P2 dapat memiliki pengecualian tertulis dengan penanggung jawab dan target perbaikan.

## Matriks test per domain

| ID | Domain | Skenario minimum | Hasil diharapkan |
|---|---|---|---|
| AUTH-01 | Auth | Password benar/salah, logout, session regenerasi | Hanya akun active dapat login; session lama tidak digunakan |
| AUTH-02 | Auth | Pending/inactive/rejected/suspended memakai session | Akses Workspace/Dashboard ditolak |
| AUTH-03 | Auth | Reset password valid, kedaluwarsa, dipakai ulang | Token sekali pakai, tidak enumerasi email |
| RBAC-01 | Dashboard | Semua role non-admin mengakses `/admin/*` langsung | 403/redirect aman |
| RBAC-02 | Workspace | Guest dan Public User mengakses `/workspace/*` | Ditolak |
| RBAC-03 | Ownership | Peserta mengubah ID enrollment/attempt/submission/certificate | Akses ditolak; data tak berubah |
| RBAC-04 | Penugasan | Trainer/Evaluator mengakses kelas lain | Ditolak termasuk endpoint AJAX/download |
| RBAC-05 | Permission | Admin tanpa permission melakukan action terlarang | Ditolak di route dan service |
| MAIN-01 | UI | Landing/Workspace memakai asset Main, Dashboard memakai asset berbeda | Tidak ada asset/view reference di produksi |
| WIL-01 | Wilayah | Provinsi 18, dropdown anak dari prefix/panjang kode | Hanya hasil hierarkis yang valid |
| WIL-02 | Profil | Kode wilayah tidak ada atau lintas hierarki | Validasi gagal dan FK tidak terlanggar |
| LMS-01 | Kelas | Jadwal tidak valid, bobot tidak 100, passing grade di luar 0–100 | Ditolak tanpa perubahan parsial |
| LMS-02 | Enrollment | Double enrollment, kelas penuh, periode tutup | Unique/guard bekerja; quota aman saat paralel |
| LMS-03 | Lesson | Akses lesson kelas bukan miliknya | Ditolak |
| LMS-04 | Quiz | Attempt limit, deadline, submit ganda, pilihan multiresponse | Skor dan status akurat, jawaban terkunci |
| LMS-05 | Tugas | Essay/upload, MIME palsu, deadline, resubmit | Guard dan storage benar |
| LMS-06 | Grading | Evaluator ditugaskan/tidak; skor > max; final grade | Hanya evaluator sah, bobot benar, audit tercatat |
| KNOW-01 | Pengetahuan | Draft/review vs published pada katalog | Hanya published tampak publik |
| KNOW-02 | Bookmark | Duplikat dan bookmark objek nonpublik/tidak ada | Constraint dan guard bekerja |
| REG-01 | Regulasi | Legal revoked vs workflow published | Publik melihat metadata dengan label revoked |
| REG-02 | Version | Dua versi current secara bersamaan | Service menonaktifkan sebelumnya atomik |
| REG-03 | Download | `path NULL`, file hilang, dokumen belum published | Tidak ada streaming, log aman |
| EVT-01 | Event | coming soon/open/full/finished/cancelled | Registrasi hanya pada state sah |
| EVT-02 | Event | Dua request terakhir memperebutkan satu slot | Kuota tidak terlampaui |
| EVT-03 | Attendance | Hanya registrasi valid dan petugas berizin | Satu record per registrasi |
| CERT-01 | Learning | Enrollment lulus vs belum lulus | Hanya lulus memenuhi syarat issue |
| CERT-02 | Event | Finished + hadir vs absent | Hanya yang memenuhi syarat memperoleh sertifikat |
| CERT-03 | Integritas | Issue ganda untuk source sama dan nomor bersamaan | Satu source satu sertifikat; nomor/token unik |
| CERT-04 | Verifikasi | Valid/revoked/expired/not found | Status tepat, rate limit, tanpa NIP/NIK |
| CERT-05 | File | PDF placeholder, reissue, revoke | `path NULL` tidak bisa download; audit ada |
| INT-01 | Intelligence | Data JF PPBJ biro PBJ, user_id nullable | Data personel tanpa akun didukung |
| INT-02 | Kompetensi | Jenis Level 1/PPK A-B-C, nomor duplikat | Constraint unik per jenis |
| INT-03 | Statistik | Agregat OPD Lampung; data individu ada dalam sumber | Publik tidak berisi nama/NIP/email |
| AUD-01 | Audit | Role, nilai, approval, issue, revoke, export | Actor/action/time/subject terekam; secret dimasking |
| DB-01 | Schema | Migrasi dari DB kosong dan seeder reference | Seluruh FK/index, data master, role terpasang |
| DB-02 | Migration | Rollback development dan rerun migrasi | Tidak ada schema drift atau data palsu |

## Fixture dan representasi

- Role: tujuh role sistem; uji user dengan multi-role dan tanpa permission tertentu.
- Akun: active, pending, inactive, rejected, suspended.
- Course/konten: draft, submitted, in_review, approved, published, archived, rejected, revision_required.
- Kelas: draft, scheduled, ongoing, completed, cancelled; bobot 50/50 dan 0/100.
- Quiz: multiple_choice, multiple_response, true_false; attempt in_progress, graded, expired.
- Assignment: draft, submitted, late, graded, returned.
- Event: coming_soon, open_registration, full, finished, cancelled.
- Sertifikat: pending, issued, revoked, expired; source learning_enrollment/event_registration berbeda.
- File: `path=NULL`, path valid/file ada, path terisi/file hilang; public/protected/private.
- Intelligence: JF level Pertama/Muda/Madya/Utama; sertifikat PBJ Level 1, PPK A/B/C; personel tanpa akun.
- Organisasi: root Pemerintah Provinsi Lampung + 49 unit bernama sesuai master yang disetujui; kode dummy bukan kode resmi.
- Wilayah: master asli; fixture profil hanya merujuk kode `18` jika anak tidak dijamin tersedia.

## Test transaksi dan konkurensi

| Proses | Pengujian |
|---|---|
| Registrasi event | Paralel request pada slot terakhir; sukses maksimal sesuai kuota |
| Enrollment | Dua request user yang sama tidak menghasilkan duplikat |
| Quiz | Attempt number unik saat request paralel |
| Nilai akhir | Perhitungan ulang tidak menghasilkan dua `final_grades` |
| Sertifikat | Sequence atomik; nomor dan `(source_type,source_id)` unik |
| Regulasi | Satu versi current; update dua versi bersamaan tidak menyisakan dua current |
| Upload | Simpan file gagal → tidak ada metadata orphan; DB gagal → file sementara dibersihkan |
| Approval | Dua approver pada state sama → satu transition berlaku, history konsisten |

## Test privasi dan keamanan

- Enumerasi ID pada route Workspace, file, dan sertifikat diuji lintas user.
- Login/reset/verification rate limit dan CSRF pada write diuji.
- XSS diuji pada artikel, FAQ, feedback, metadata, dan nama file.
- SQL injection diuji pada search, sort, filter, dan export (whitelist sort field).
- Statistik publik diperiksa dengan recursive JSON field scan untuk NIP/NIK, email, telepon, identifier, personel names, file path.
- Audit payload dan log tidak boleh mengandung password, reset token, cookie, QR token, NIP/NIK utuh.
- Verifikasi sertifikat tidak otomatis membuka PDF private.

## Kriteria selesai dan bukti

- Semua test P0/P1 lulus pada testing dan staging; tidak ada defect kritis terbuka.
- Route, permission, data dictionary, workflow state machine, dan implementasi konsisten.
- Hasil migration/seed, test report, coverage relevan, dan bukti UAT disimpan pada sistem/artefak yang disetujui tanpa data rahasia.
- Performance baseline, target respons, serta target beban harus ditetapkan pemilik layanan sebelum keputusan go-live; tidak mengarang SLA.
- Setiap bug memiliki ID, severity, owner, dan hasil retest.

## Perintah dasar

```bash
php spark migrate:status
php spark test
```

Untuk DB test bersih, gunakan mekanisme pengujian CI4/DB test terisolasi; jangan pernah menjalankan `migrate:fresh` pada production.

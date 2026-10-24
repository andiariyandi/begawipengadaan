# BegawiPengadaan — Privacy and Data Classification Policy

## Tujuan

Dokumen ini menetapkan klasifikasi data, prinsip privasi, batas pemrosesan, akses, masking, tampilan UI, file access, logging, audit, notifikasi, export, statistik, retention, serta respons insiden untuk BegawiPengadaan.

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
schema-fresh-v4-3.sql
dokumen/registration-policy.md
dokumen/assessment-and-certification-rules.md
dokumen/regulation-policy.md
```

---

## Prinsip Privasi

BegawiPengadaan menerapkan prinsip berikut:

- **Data minimization**: kumpulkan hanya data yang diperlukan untuk layanan, verifikasi, keamanan, atau kewajiban yang sah.
- **Purpose limitation**: gunakan data hanya untuk tujuan yang dijelaskan oleh layanan dan policy domain.
- **Least privilege**: akses dibatasi berdasarkan account status, role, permission, ownership, assignment, delegated scope, visibility, dan workflow state.
- **Server-side enforcement**: UI tidak menjadi satu-satunya pengendali akses.
- **Separation of concerns**: data public, internal, restricted, dan system dipisahkan pada database, service, view, file storage, export, dan log.
- **Privacy by default**: data baru dianggap internal/restricted sampai policy secara eksplisit menentukan bahwa data boleh public.
- **Auditability**: akses/perubahan yang penting, khususnya pada data restricted, harus dapat ditelusuri secara proporsional.
- **Secure disclosure**: informasi yang ditampilkan, diekspor, dinotifikasi, atau dicatat harus minimum sesuai kebutuhan.

---

## Klasifikasi Data

| Kode | Klasifikasi | Definisi | Default akses |
|---|---|---|---|
| `P` | Public | Data yang boleh tersedia pada Landing/Public Area setelah memenuhi visibility dan workflow | Publik sesuai policy resource |
| `I` | Internal | Data operasional yang tidak ditujukan bagi publik | User terautentikasi dengan role/permission/scope relevan |
| `R` | Restricted | Data personal, identitas, keamanan, assessment, bukti, atau data yang memerlukan perlindungan ketat | Hanya actor dengan kebutuhan sah, permission, dan konteks resource |
| `S` | System | Metadata teknis, audit, token hash, lifecycle, konfigurasi, atau observability | System/actor berwenang sesuai kebutuhan operasional |

### Aturan klasifikasi

- Klasifikasi paling ketat berlaku ketika satu record atau response memuat beberapa jenis data.
- Data `P` tetap tidak otomatis bebas diubah; perubahan tetap mengikuti role, permission, dan workflow.
- Data `I` tidak boleh dirender pada route publik tanpa agregasi/anonymization atau keputusan policy eksplisit.
- Data `R` tidak boleh dibuka berdasarkan URL, slug, ID yang dapat ditebak, atau karena user berhasil login saja.
- Data `S` tidak boleh ditampilkan pada UI biasa kecuali dibutuhkan untuk aktor berwenang dan tidak mengungkap risiko keamanan.

---

## Data Personal dan Restricted

Data berikut minimal diperlakukan sebagai restricted sesuai konteks:

```text
Nama lengkap bila dikaitkan dengan data layanan internal
Email personal atau kedinasan
Nomor telepon
Alamat rinci
NIP/NIPPK
NIK
NIB/identifier legal badan usaha
Identifier profesi tertentu
Identifier SDM dan nama snapshot internal
Dokumen profile verification
Bukti kompetensi dan sertifikat kompetensi internal
Jawaban quiz dan assessment
Submission tugas, file submission, score, feedback evaluator
Token verifikasi/reset dalam bentuk plain token
Password dan password hash
Session identifier
Secret, credential, API key, dan konfigurasi lingkungan
File path private, checksum security, hasil malware scan
Audit snapshot yang dapat memuat data personal
Filter export yang dapat mengungkap data individual
```

Data berikut dapat menjadi public hanya jika resource sudah published/public dan tidak mengandung data restricted:

```text
Judul/ringkasan konten publik
Kategori/tag publik
Poster/flyer/infografik yang telah disetujui publik
Metadata regulasi publik
Berita/event publik
Statistik agregat yang telah melewati disclosure control
Status verifikasi sertifikat dengan data minimum aman
```

---

## Klasifikasi per Domain

### Identity dan Registrasi

| Data | Klasifikasi | Aturan utama |
|---|---|---|
| `users.name` | R | Tampilkan hanya sesuai kebutuhan layanan/role; masking atau minimization pada daftar internal |
| `users.email` | R | Tidak tampil publik; tidak dikirim dalam notifikasi yang tidak aman |
| `users.phone` | R | Tidak tampil publik; masking pada UI bila diperlukan |
| `users.username` | I | Tidak dianggap identitas publik default |
| `users.password_hash` | R | Tidak pernah ditampilkan/log/export |
| `profile_type` | I | Tidak memberi privilege; tampil sesuai kebutuhan internal |
| `account_status` | I/S | Tampil kepada owner dan actor reviewer sesuai scope |
| Alamat/wilayah detail | R | Tidak tampil publik; gunakan agregasi untuk statistik |
| NIP/NIPPK/NIK/NIB | R | Masking, access ketat, tidak masuk statistik publik |
| Dokumen verifikasi profil | R | Private storage, authorization ketat, audit sesuai policy |
| Role/permission assignment | I/S | Back-office berizin; audit perubahan wajib |

### Learning dan Assessment

| Data | Klasifikasi | Aturan utama |
|---|---|---|
| Program/course published | P | Hanya bila visibility public/published |
| Class internal | I | Bergantung visibility class dan enrollment |
| Enrollment | I/R | Owner, Trainer/Evaluator assigned, Operator/Admin scope |
| Progress belajar | R | Hanya owner/actor class berwenang |
| Quiz question sebelum dikerjakan | I | Participant enrollment sah; jawaban benar tidak diekspos |
| `quiz_options.is_correct` | R | Tidak dikirim ke Participant sebelum policy mengizinkan |
| Jawaban quiz | R | Owner terbatas, evaluator/admin sesuai policy |
| Assignment submission/file | R | Owner, evaluator assigned, trainer/admin berwenang |
| Nilai/final score/feedback | R | Tidak public; tidak masuk statistik individual |
| Sertifikat pribadi/PDF | R | Owner/issuer berwenang; verification publik minimum |

### Bunga Rampai

| Data | Klasifikasi | Aturan utama |
|---|---|---|
| Artikel/FAQ/studi kasus/template published | P | Harus `published` + visibility `public` |
| Draft/review content | I | Author/reviewer/admin berwenang |
| Media source/asset internal | I/R | Mengikuti file visibility/lifecycle |
| External URL provider | I/P | Validasi provider/domain sebelum public |
| Author internal metadata | I/R | Tampilkan publik hanya jika policy konten mengizinkan |

### Regulasi PBJ

| Data | Klasifikasi | Aturan utama |
|---|---|---|
| Metadata regulasi published/public | P | Legal status tetap tampil sebagai konteks historis |
| Draft/review regulasi | I | Back-office berizin |
| File regulasi public | P | Hanya `available` + visibility `public` |
| File internal/restricted | I/R | FileAccessService + permission/scope |
| Legal relation/version changelog | P/I | Bergantung visibility kedua resource |
| Download log | S/I | Tidak public; access log sesuai policy |

### Berita, Event, dan Komunikasi

| Data | Klasifikasi | Aturan utama |
|---|---|---|
| Berita/event published/public | P | Public visibility dan status sesuai policy |
| Data event draft/internal | I | Admin/actor berwenang |
| Event registration | I/R | Owner dan operator/admin event scope |
| Attendance | R | Owner secara terbatas; operator/admin scope |
| Waitlist/registration reason | I/R | Tidak ditampilkan publik |

### Sertifikat

| Data | Klasifikasi | Aturan utama |
|---|---|---|
| Certificate number | P/I | Dapat ditampilkan pada verifikasi bila aman/valid |
| Verification token hash | R | Tidak pernah ditampilkan/logged |
| Plain verification token | R | Hanya diproses saat request; tidak disimpan sebagai plain value |
| Certificate status | P/I | Hasil verifikasi publik minimum aman |
| Certificate PDF | R | Owner/issuer authorized; token verifikasi publik tidak otomatis membuka PDF |
| Revocation reason | I/R | Public disclosure minimum sesuai policy |

### Government Intelligence

| Data | Klasifikasi | Aturan utama |
|---|---|---|
| Organization aggregate | I/P terbatas | Hanya agregat yang aman dapat masuk statistik publik |
| JF PPBJ individual record | R | Internal actor dengan permission Intelligence |
| NIP/employee identifier | R | Tidak boleh public atau diekspor tanpa scope sah |
| Competency certificate evidence | R | Private storage dan review berwenang |
| Gap competency snapshot | I | Agregat internal; publik hanya melalui disclosure control |
| Export intelligence | R | Private export, owner/scope/permission, expiry, audit |

### Shared Governance

| Data | Klasifikasi | Aturan utama |
|---|---|---|
| Audit log | S/R | Hanya actor berwenang; snapshot ter-redaksi |
| Workflow transition | S/I | Internal; tampil sesuai kebutuhan review/audit |
| Notification | I | Owner penerima; data minimum aman |
| File path private | R | Tidak pernah tampil pada UI/response tanpa kebutuhan sangat khusus |
| Export filters/file | R | Owner/scope/permission; TTL/retention |
| Bookmark | I | Owner saja; tidak memberi permission tambahan |

---

## Aturan Akses Data

Akses terhadap data ditentukan oleh kombinasi berikut:

```text
Authentication
+ Account Status
+ Role
+ Permission
+ Ownership
+ Assignment
+ Delegated Scope
+ Resource Visibility
+ Workflow State
+ Data Classification
```

### Contoh

```text
Participant dengan enrollment aktif
→ dapat melihat progress dan submission miliknya sendiri.

Evaluator dengan assignment class
→ dapat mengakses submission yang termasuk scope penilaiannya.

Admin dengan permission regulation.view
→ dapat melihat regulasi internal sesuai scope, tetapi bukan otomatis semua file restricted.

User yang mengetahui file ID
→ tidak dapat mengunduh file restricted tanpa FileAccessService authorization.
```

### Aturan deny-by-default

- Bila policy untuk sebuah resource belum didefinisikan, akses ditolak.
- Data restricted tidak boleh diberikan melalui fallback API/UI.
- Query internal tidak boleh menggunakan `SELECT *` untuk response yang dibagikan lintas boundary tanpa projection/serialization aman.
- Controller dan service harus memakai data projection sesuai aktor/response, bukan hanya menyembunyikan field di view.

---

## Aturan Tampilan UI

### Landing dan Public Area

Landing/Public Area hanya dapat menampilkan data:

```text
workflow/publish state valid
+ visibility public
+ klasifikasi P
+ tidak memuat data restricted
```

Aturan:

- Tidak menampilkan NIP, NIK, email, nomor telepon, alamat, dokumen bukti, nilai peserta, daftar peserta, attendance individual, atau metadata internal.
- Statistik harus teragregasi dan mengikuti policy statistik publik.
- Sertifikat verification menampilkan hasil minimum aman, bukan profil lengkap pemilik.
- Error publik tidak boleh mengungkap keberadaan resource restricted.

### Workspace

Workspace dapat menampilkan data internal/restricted hanya jika user memiliki ownership, enrollment, assignment, atau role/scope yang sah.

Aturan:

- Profile owner dapat melihat data dirinya sesuai policy.
- Trainer/Evaluator tidak boleh melihat data profile/detail peserta melebihi kebutuhan kegiatan class.
- Assignment/grade tidak dapat dibuka Participant lain.
- File private tetap dibuka lewat endpoint protected, bukan URL storage.

### Dashboard Back-office

Dashboard dapat menampilkan data internal/restricted sesuai permission dan scope.

Aturan:

- Daftar user default menggunakan masked field untuk data sensitif bila nilai lengkap tidak diperlukan.
- Detail restricted membutuhkan permission/need-to-know yang lebih spesifik bila diperlukan.
- Dashboard tidak boleh menyisipkan token, password hash, path file private, atau audit snapshot penuh dalam HTML/JavaScript.
- Akses ke Government Intelligence individual dan export harus diperlakukan lebih ketat daripada dashboard metric umum.

---

## Masking

Masking diterapkan ketika user perlu mengidentifikasi record tanpa membutuhkan nilai lengkap.

### Aturan mask contoh

| Data | Contoh mask | Catatan |
|---|---|---|
| Email | `an***@contoh.go.id` | Pertahankan konteks cukup untuk identifikasi terbatas |
| Nomor telepon | `08****1234` | Tampilkan sebagian akhir bila perlu |
| NIP/NIPPK | `1987**********01` | Terapkan pola yang konsisten |
| NIK | `************1234` | Hanya 4 digit akhir bila perlu |
| NIB | `***********7890` | Hanya bagian minimum |
| Certificate number | Dapat tampil penuh pada verification jika policy mengizinkan | Nomor sertifikat bukan berarti seluruh profil terbuka |
| File path private | Tidak ditampilkan | Gunakan nama display aman atau file ID internal bila berwenang |

Aturan masking:

- Nilai penuh hanya ditampilkan jika diperlukan untuk tindakan yang sah dan actor memiliki permission/scope sesuai.
- Masking pada UI tidak menggantikan authorization pada API/controller/service.
- Nilai ter-mask tetap tidak boleh dipakai sebagai pengganti policy access terhadap data raw.

---

## Logging dan Audit

### Data yang tidak boleh masuk log/audit plain

```text
Password
Password hash
Konfirmasi password
Plain email verification token
Plain password reset token
Session ID
Cookie value
Secret/API key
Credential layanan
Private storage path mentah
Isi penuh dokumen restricted
Isi penuh jawaban quiz/submission
Full NIK/NIP/NIB tanpa kebutuhan audit sah
```

### Audit yang diizinkan

Audit dapat mencatat metadata minimum:

```text
actor_id
actor role/permission snapshot bila diperlukan
action
resource_type
resource_id
result
timestamp
reason/notes yang aman
request_id
IP/user-agent dalam bentuk hash/minimized sesuai policy
```

### Redaction

Sebelum `before_snapshot` atau `after_snapshot` disimpan:

- Hapus password, token, credential, session, cookie, dan secret.
- Mask/hapus NIK, NIP, NIB, telepon, email, alamat, dan payload profile bila tidak diperlukan.
- Hapus content assessment/submission dan file path private.
- Simpan hanya field yang dibutuhkan untuk menjelaskan perubahan.

---

## Notifikasi dan Kanal Komunikasi

Notifikasi menggunakan data minimum yang aman.

### Tidak boleh dikirim melalui notifikasi biasa

```text
Password atau reset token plain selain URL reset yang dibangun aman
NIK/NIP/NIB penuh
Dokumen bukti
Nilai detail/submission lengkap
Path file private
Secret atau credential
Audit snapshot
```

### Boleh dikirim secara minimum

```text
Status registrasi berubah
Permintaan perbaikan profil
Role request diproses
Assignment tersedia atau perlu revisi
Sertifikat tersedia/dicabut
Event registration/waitlist/attendance berubah
Export siap diunduh melalui route protected
```

Notifikasi harus mengarahkan user ke route aplikasi yang memeriksa authorization kembali, bukan mengirimkan data restricted langsung dalam payload.

---

## File dan Storage

### Public file

File boleh berada pada `public/assets/main/uploads-public/` hanya jika:

```text
lifecycle_status = available
+ visibility = public
+ resource domain published/public bila relevan
+ file telah melalui validation/security policy
```

### Internal/restricted file

```text
writable/uploads/
→ internal/restricted file
→ tidak dapat diakses langsung dari URL publik
→ preview/download melalui FileController + FileAccessService
```

Aturan:

- File evidence profile, submission, certificate PDF, competency evidence, file internal class, dan export bersifat internal/restricted sesuai domain.
- Public verification certificate tidak otomatis memberi akses PDF certificate.
- File lifecycle `quarantined`, `rejected`, atau `deleted` tidak dapat diakses normal.
- Metadata file tidak boleh mengungkap path private atau informasi security scan kepada actor tanpa kebutuhan sah.

---

## Export dan Data Sharing

### Export

Export adalah data restricted secara default.

```text
writable/exports/
→ private
→ owner/scope/permission validation
→ expiry/retention policy
→ audit request/download/expiry/delete bila relevan
```

Aturan:

- Export hanya mencakup kolom yang diperbolehkan bagi actor dan scope permintaannya.
- Export tidak boleh menggunakan query raw tanpa projection/column allowlist.
- Export data personal/restricted memerlukan permission spesifik dan audit.
- Export harus memiliki status/lifecycle, expiry, serta cleanup terjadwal.
- Link export harus kembali memeriksa authorization saat download.

### Data sharing

- Data internal/restricted tidak dibagikan ke pihak lain tanpa dasar/approval/policy yang sesuai.
- Jika agregasi data dibagikan, lakukan pemeriksaan disclosure control terlebih dahulu.
- File tidak dikirim/di-share hanya berdasarkan path; gunakan authorization dan visibility policy.

---

## Statistik Publik dan Disclosure Control

Statistik publik hanya menggunakan data agregat yang aman.

Dilarang dalam Statistik Publik:

```text
NIP
NIK
Nama individu
Email
Nomor telepon
Dokumen bukti
Identifier personal
Daftar peserta
Nilai individual
Attendance individual
Data profile individual
Data intelligence individual
```

Aturan minimum:

- Agregasikan data sebelum dikirim ke Public Area.
- Terapkan suppression atau grouping jika ukuran kelompok terlalu kecil dan berisiko re-identification.
- Gunakan threshold minimum yang ditentukan policy statistik publik.
- Jangan menyediakan drill-down publik yang dapat mengembalikan identitas individu melalui kombinasi filter.
- Pisahkan query Statistik Publik dari query Government Intelligence detail.
- Dokumentasikan definisi indikator, periode, sumber, dan transformasi agregasi.

Aturan indikator, threshold, suppression, serta disclosure control dirinci pada `dokumen/public-statistics-policy.md`.

---

## Retention dan Penghapusan

Retention mengikuti policy domain, audit, keamanan, dan kebutuhan operasional. Baseline:

| Data | Aturan |
|---|---|
| Account/profile | Archive/soft delete sesuai policy; riwayat workflow dan audit dipertahankan |
| Token email/reset | Kedaluwarsa/used dibersihkan sesuai policy; tidak pernah plain token |
| Audit logs | Append-only secara fungsional; retensi sesuai audit/security policy |
| File temporary | TTL pendek dan cleanup terjadwal |
| Export | TTL/expiry dan delete setelah masa download berakhir |
| Assignment/assessment | Dipertahankan sesuai policy pembelajaran/audit |
| Regulasi | Historis/version/relasi dipertahankan; revoked/expired tidak otomatis dihapus |
| Certificate | Revocation/replacement tidak menghapus riwayat |
| Intelligence restricted | Retensi mengikuti kebutuhan administrasi, privacy, dan legal basis |

Hard delete hanya dilakukan setelah memeriksa foreign key, audit, backup, legal hold, dan policy retensi. Hard delete tidak boleh digunakan sebagai cara menghilangkan jejak audit atau workflow.

---

## Incident dan Pelanggaran Data

Jika ditemukan akses tidak sah, file mencurigakan, kebocoran data, atau pelanggaran policy:

```text
1. Batasi atau cabut akses resource/account/file yang terdampak.
2. Karantina file jika terkait file asset.
3. Catat security/audit event tanpa menulis data sensitif tambahan.
4. Evaluasi scope, actor, route, export, token, dan resource terkait.
5. Invalidate session/token bila diperlukan.
6. Lakukan remediation dan review permission/policy.
7. Beri notifikasi sesuai kebutuhan operasional dan policy yang berlaku.
```

Incident response detail dapat berkembang melalui prosedur operasional, tetapi tidak boleh mengurangi prinsip data minimization dan keamanan audit.

---

## Checklist Implementasi

Sebelum fitur yang mengolah data dinyatakan `Backend Ready`, pastikan:

- [ ] Setiap tabel/kolom/resource memiliki klasifikasi P/I/R/S yang dipahami oleh service dan UI.
- [ ] Data restricted hanya diberikan melalui authorization server-side.
- [ ] UI menggunakan projection/masking sesuai kebutuhan actor.
- [ ] Public Area hanya menerima data published/public yang bebas dari restricted data.
- [ ] Tidak ada password, plain token, secret, session ID, atau private path pada log/audit/notification/response.
- [ ] File internal/restricted berada di private storage dan memakai FileAccessService.
- [ ] Export bersifat private, scoped, expired, dan diaudit bila relevan.
- [ ] Statistik publik menggunakan agregasi serta disclosure control.
- [ ] Audit snapshot melalui redaction.
- [ ] Retention/cleanup berlaku untuk token, temporary file, export, dan data lifecycle terkait.
- [ ] Test mencakup access denial, ownership/assignment, masking, export scope, public visibility, restricted file access, dan disclosure control.

# BegawiPBJ — File Storage Policy

## Tujuan

Dokumen ini menetapkan kebijakan penyimpanan, upload, klasifikasi, validasi, visibility, akses, preview, download, export, retention, audit, dan lifecycle file untuk BegawiPBJ.

Dokumen ini melengkapi dan tidak mengubah kontrak yang telah ditetapkan dalam:

```text
README.md
dokumen/directory-tree.md
dokumen/route-contract.md
dokumen/ui-boundary.md
dokumen/auth-and-rbac.md
dokumen/workflow-contract.md
```

Kebijakan ini berlaku untuk file regulasi, sertifikat, materi pembelajaran, submission assignment, profile verification, Bunga Rampai/media, export, dan file domain lain yang dikelola sistem.

---

## Prinsip Storage

- File diklasifikasikan sebagai `public`, `internal`, atau `restricted` sebelum tersedia untuk pengguna.
- File internal/restricted tidak boleh diakses langsung melalui URL publik.
- File private disimpan di `writable/uploads/` dan hanya dilayani melalui controller/service akses file.
- File public hanya dapat berada di `public/assets/main/uploads-public/` setelah melewati validasi, lifecycle file, dan policy visibility.
- File access selalu menggunakan authorization server-side; path file bukan mekanisme authorization.
- Upload tidak boleh dipercaya hanya dari extension, MIME header client, atau nama file asli.
- Semua upload harus melalui validasi MIME, ukuran, ekstensi, konten, ownership/context domain, dan policy storage.
- File tidak boleh dianggap tersedia sebelum lifecycle mencapai status `available`.
- Download, preview, upload, delete, quarantine, serta export mengikuti audit policy.
- Password, secret, plain token, atau credential tidak boleh disimpan sebagai file atau dicatat pada metadata/audit.

---

## Root Storage

```text
public/assets/main/uploads-public/
→ File yang secara eksplisit public dan tersedia untuk Landing/Public Area.

writable/uploads/
→ File private runtime: internal dan restricted.

writable/uploads/certificates/
→ File sertifikat digital yang dikelola sistem.

writable/uploads/knowledge/
→ File Bunga Rampai/media internal atau restricted.

writable/uploads/learning/
→ Materi pembelajaran yang tidak public.

writable/uploads/profiles/
→ Dokumen profile verification dan perubahan profil.

writable/uploads/regulations/
→ File regulasi internal/restricted atau belum public.

writable/uploads/submissions/
→ Submission assignment peserta.

writable/uploads/temporary/
→ File sementara upload/proses; tidak boleh diperlakukan sebagai file final.

writable/exports/
→ Export sementara yang tetap memerlukan authorization ketika diunduh.
```

Tidak boleh membuat storage root baru tanpa memperbarui kontrak arsitektur dan struktur direktori.

---

## Klasifikasi File

### Visibility

| Visibility | Lokasi normal | Akses | Contoh |
|---|---|---|---|
| `public` | `public/assets/main/uploads-public/` setelah finalisasi | Dapat diakses publik bila resource domain juga public/published | Cover berita, poster publik, media gallery publik, file regulasi publik bila policy mengizinkan |
| `internal` | `writable/uploads/` | Hanya user terautentikasi dengan permission/scope yang sesuai | Materi kelas internal, laporan operasional, file event internal |
| `restricted` | `writable/uploads/` | Hanya actor dengan permission, ownership/assignment, serta policy khusus | Dokumen verifikasi profil, assignment submission, bukti kompetensi, file sertifikat internal |

Visibility file berbeda dari status workflow resource domain. Contoh: regulasi dapat berstatus `published`, tetapi file tertentu tetap `restricted` apabila tidak boleh dibuka publik.

### Klasifikasi data file

| Kategori | Contoh | Ketentuan minimum |
|---|---|---|
| Public content | Gambar cover, poster publik, thumbnail, infographic publik | Dapat dilayani publik setelah workflow publish dan visibility `public` |
| Public document | File regulasi publik, dokumen template publik | Visibility `public`, policy domain mengizinkan, malware/content validation selesai |
| Internal content | Materi kelas internal, file event internal, laporan domain | Auth + permission/role/context resource |
| Restricted personal | Dokumen identitas, bukti profil, NIP/NIK/NIB support document | Private storage, strong authorization, masking metadata, audit akses bila relevan |
| Restricted assessment | Submission tugas, jawaban/attachment assessment, grading evidence | Enrollment/assignment/ownership authorization; private storage |
| Generated certificate | PDF sertifikat dan aset pendukung | Private storage; public verification tidak memberikan akses PDF kecuali policy mengizinkan |
| Temporary | Upload chunk, preview sementara, file proses | TTL singkat; tidak dapat diakses sebagai resource final |
| Export | CSV/XLSX/PDF laporan internal | Private export storage; owner/scope/permission authorization; retention ketat |

---

## Lifecycle File

Lifecycle file teknis mengikuti workflow contract:

```text
pending_upload
stored
scanned
available
rejected
quarantined
deleted
```

### Transisi lifecycle

| Dari | Aksi | Ke | Actor/Service | Ketentuan |
|---|---|---|---|---|
| — | Mulai upload | `pending_upload` | User/System | Context domain, policy awal, dan batas upload diperiksa |
| `pending_upload` | Simpan bytes | `stored` | FileStorageService | Nama server-side dan path aman dibuat |
| `stored` | Scan/validasi | `scanned` | System/File service | MIME, signature, ukuran, dan scan keamanan bila tersedia |
| `scanned` | Finalisasi | `available` | System/File service | Metadata, visibility, relasi domain, dan policy valid |
| `stored` / `scanned` | Tolak | `rejected` | System/authorized actor | Alasan aman dicatat bila user-visible |
| `stored` / `scanned` / `available` | Karantina | `quarantined` | System/authorized actor | Indikasi malware, policy violation, atau security issue |
| `available` / `rejected` / `quarantined` | Hapus | `deleted` | System/authorized actor | Retention/policy/audit berlaku |

File berstatus `pending_upload`, `stored`, `scanned`, `rejected`, `quarantined`, atau `deleted` tidak boleh dilayani sebagai download/preview normal.

---

## File Metadata Minimum

Setiap file yang dikelola sistem memiliki record metadata, misalnya melalui `FileAssetModel` dan entity `FileAsset`.

| Field konseptual | Keterangan |
|---|---|
| `id` | Identifier internal file asset |
| `disk` | Disk/storage backend yang digunakan |
| `path` | Path internal server-side, bukan URL public langsung |
| `original_name` | Nama file dari pengguna; diperlakukan sebagai untrusted display data |
| `stored_name` | Nama file unik yang dibuat server-side |
| `extension` | Ekstensi yang tervalidasi |
| `mime_type` | MIME hasil validasi server-side |
| `size_bytes` | Ukuran bytes tervalidasi |
| `checksum` | Checksum/hash integritas bila digunakan |
| `visibility` | `public`, `internal`, atau `restricted` |
| `lifecycle_status` | Status lifecycle file |
| `resource_type` | Domain/resource pemilik, misalnya regulation, lesson, assignment_submission |
| `resource_id` | Identifier resource pemilik |
| `uploaded_by` | Actor upload atau system actor |
| `available_at` | Waktu file tersedia |
| `expires_at` | Batas waktu bila file/export temporary |
| `deleted_at` | Waktu soft delete/retention delete bila digunakan |
| `metadata` | Metadata aman yang tidak menyimpan secret atau restricted payload berlebihan |

Nama file asli tidak boleh digunakan sebagai path server-side tanpa sanitasi. Path yang disimpan pada metadata tidak boleh diekspos kepada user yang tidak berwenang.

---

## Upload Policy

### Endpoint dan service

Upload harus melalui:

```text
Route
POST /file/upload

Controller
Shared\FileController::upload

Service
FileStorageService

Authorization
Auth + account status active + permission/domain policy + context resource
```

Controller domain dapat memulai upload dalam context form domain, tetapi penyimpanan final tetap melalui `FileStorageService` dan validasi file policy.

### Validasi wajib

Setiap upload memeriksa:

- User terautentikasi dan account status sesuai policy.
- Permission, ownership, enrollment, assignment, atau delegated scope terhadap resource tujuan.
- Resource domain valid dan berada pada workflow state yang mengizinkan upload.
- Ukuran file maksimum sesuai konfigurasi/domain.
- Ekstensi dalam allowlist domain.
- MIME type hasil inspeksi server-side dalam allowlist domain.
- Signature/magic bytes bila library/engine tersedia.
- Nama file disanitasi dan tidak dipakai sebagai path.
- File tidak mengandung executable payload, macro/active content berbahaya, atau konten yang melanggar policy.
- Jumlah file per resource serta kuota user/domain bila diberlakukan.
- Policy storage disk dan visibility sesuai domain.

### Larangan upload

- Tidak menerima executable, script server-side, binary tidak dikenal, atau file dengan ekstensi ganda yang berbahaya.
- Tidak mengandalkan `Content-Type` dari browser sebagai validasi final.
- Tidak menyimpan file upload langsung dengan nama asli pada public directory.
- Tidak menampilkan file upload sebelum lifecycle mencapai `available`.
- Tidak mengizinkan upload pada resource yang telah final/archived/revoked/cancelled kecuali policy domain mengizinkan.

### Allowlist baseline per domain

| Domain | Tipe file yang umum diizinkan | Visibility default |
|---|---|---|
| Profile verification | PDF, JPG, JPEG, PNG sesuai policy | `restricted` |
| Learning material | PDF, JPG, JPEG, PNG, MP4, dokumen yang diizinkan | `internal` |
| Assignment submission | PDF, DOCX, XLSX, PPTX, JPG, JPEG, PNG sesuai assignment policy | `restricted` |
| Knowledge media | JPG, JPEG, PNG, WEBP, PDF, MP4 sesuai tipe media | `internal` atau `public` setelah publish |
| Regulation | PDF dan format dokumen resmi yang diizinkan | `internal` atau `public` berdasarkan policy/visibility |
| Certificate | PDF generated system; image/asset template terkontrol | `restricted` secara default |
| Export | CSV, XLSX, PDF generated system | `restricted` |

Batas ukuran, ekstensi spesifik, codec video, dan provider media eksternal ditetapkan melalui konfigurasi serta policy domain yang relevan. Allowlist dapat diperketat; tidak boleh dilonggarkan tanpa review keamanan.

---

## Public File Policy

File hanya boleh dipindahkan/ditulis ke `public/assets/main/uploads-public/` jika semua kondisi berikut terpenuhi:

```text
lifecycle_status = available
+ visibility = public
+ resource domain berstatus published bila relevan
+ policy domain mengizinkan publikasi file
+ content/security validation selesai
+ metadata public aman untuk ditampilkan
```

Contoh yang dapat menjadi public:

- Cover berita yang telah published.
- Poster/flyer Bunga Rampai yang telah published.
- Thumbnail media gallery public.
- Infografik public.
- File regulasi public bila visibility dokumen mengizinkan.

File public harus tetap dapat ditarik/ditutup aksesnya bila visibility resource berubah, file dikarantina, atau resource diarsipkan sesuai policy.

---

## Internal dan Restricted File Access

### Endpoint akses

```text
GET /file/{fileId}/preview
GET /file/{fileId}/download
GET /file/{fileId}/metadata
```

Endpoint ditangani oleh `Shared\FileController` dan memakai `FileAccessService`.

### Pemeriksaan akses minimum

Sebelum preview/download/metadata, `FileAccessService` memeriksa:

```text
1. Session autentikasi valid.
2. Account status sesuai policy, umumnya active.
3. File lifecycle_status = available.
4. File visibility sesuai dengan actor.
5. Resource domain pemilik file ada dan berada pada state yang mengizinkan akses.
6. Ownership, enrollment, assignment, delegated scope, atau permission yang relevan.
7. Audit requirement untuk akses restricted/download/export bila policy mengharuskan.
```

### Contoh authorization file

| Resource file | Actor yang dapat mengakses | Ketentuan |
|---|---|---|
| Assignment submission | Participant owner, Evaluator assigned, Trainer/Admin sesuai policy | Enrollment/assignment/resource relation harus valid |
| Dokumen profile verification | Owner terbatas, Operator/Admin reviewer berwenang | Data restricted; audit akses bila relevan |
| Materi class internal | Participant dengan enrollment aktif, Trainer assigned, Admin berizin | Class/lesson visibility dan enrollment diperiksa |
| Regulasi restricted | Admin/role berizin | Permission, visibility, dan relation regulasi diperiksa |
| Sertifikat PDF | Owner, issuer/Admin berwenang | Public verification token tidak otomatis memberi akses download PDF |
| Export intelligence | Request owner atau actor dengan scope/permission export | Export belum expired dan file masih available |

### Metadata response

Response metadata hanya memuat atribut yang dibutuhkan oleh actor, misalnya nama display aman, MIME, ukuran, status ketersediaan, serta capability preview/download. Path disk/internal, checksum sensitif, malware result, atau metadata restricted tidak boleh dikirim tanpa kebutuhan dan permission yang sah.

---

## Preview Policy

Preview hanya tersedia jika:

```text
file.lifecycle_status = available
+ file visibility diizinkan
+ resource domain diizinkan
+ MIME type dapat dipreview dengan aman
+ actor lolos FileAccessService
```

### Ketentuan preview

- Preview PDF menggunakan response inline yang terproteksi atau viewer yang memanggil endpoint preview terproteksi.
- Preview gambar memakai content disposition yang aman dan header yang sesuai.
- File HTML, SVG aktif, script, atau format yang dapat menjalankan konten aktif tidak boleh dipreview inline tanpa sanitasi/isolasi yang aman.
- Video internal hanya dapat diputar melalui endpoint/streaming yang memeriksa authorization.
- Preview tidak boleh menjadi cara untuk melewati download/access policy.
- File `quarantined`, `rejected`, atau `deleted` tidak dapat dipreview.

---

## Download Policy

Download melalui:

```text
GET /file/{fileId}/download
```

### Ketentuan download

- Authorization sama ketatnya dengan preview.
- Server menentukan file berdasarkan `fileId`; client tidak pernah mengirim path file server-side.
- Nama download dipilih dari metadata aman dan disanitasi.
- Content-Disposition dan Content-Type ditetapkan server-side.
- Download file restricted dapat dicatat di audit dengan actor, file, resource, waktu, hasil, dan konteks aman.
- Download publik hanya berlaku bagi file yang benar-benar `public` dan lifecycle `available`.
- URL sementara/presigned URL dapat digunakan bila backend storage mendukung, tetapi URL tersebut harus berumur pendek, scoped, dan tidak menggantikan authorization awal.

---

## File Domain Policy

### Profile verification dan profile change

```text
Storage
writable/uploads/profiles/

Visibility
restricted

Access
Owner secara terbatas; Operator/Admin reviewer dengan scope/permission.

Audit
Upload, preview/download oleh reviewer bila policy mengharuskan,
keputusan approval/revision/rejection, dan delete/quarantine.
```

### Learning material

```text
Storage
writable/uploads/learning/

Visibility
internal secara default.

Access
Participant dengan enrollment sah, Trainer assigned, Evaluator bila assignment
memerlukan, dan Admin berizin.

Policy
Akses mengikuti class, lesson, enrollment, schedule, dan visibility lesson.
```

### Assignment submission

```text
Storage
writable/uploads/submissions/

Visibility
restricted.

Access
Participant owner, Evaluator assigned, Trainer/Admin sesuai assignment/policy.

Policy
Tidak dapat diakses silang oleh Participant lain; revision/grade workflow diperiksa.
```

### Knowledge dan media

```text
Storage
writable/uploads/knowledge/ atau public/uploads-public setelah publish.

Visibility
internal saat draft/review; public hanya setelah publish dan policy mengizinkan.

Access
Author/reviewer/admin selama internal; publik setelah public/published.
```

### Regulasi

```text
Storage
writable/uploads/regulations/ atau public/uploads-public bila file public.

Visibility
Ditentukan per file/version, terpisah dari workflow_status record regulasi.

Access
Public bila workflow published + visibility public; otherwise permission/file policy.
```

### Sertifikat

```text
Storage
writable/uploads/certificates/

Visibility
restricted secara default.

Access
Owner, issuer/Admin berwenang, serta system verification process yang hanya
menampilkan data minimum.

Policy
PDF sertifikat tidak otomatis tersedia publik hanya karena token verifikasi valid.
```

### Export

```text
Storage
writable/exports/

Visibility
restricted.

Access
Owner export atau actor dengan permission/scope yang relevan.

Policy
Memiliki expiry dan dihapus setelah retention berakhir.
```

---

## Retention dan Deletion

Retention detail dapat ditetapkan melalui konfigurasi/policy domain, tetapi aturan minimum berikut berlaku:

| Kategori | Retention minimum | Delete policy |
|---|---|---|
| Temporary upload | Pendek/TTL | Auto-cleanup jika tidak finalisasi |
| Export | Terbatas/TTL | Expire lalu delete melalui job terjadwal |
| Public media | Selama resource published atau sesuai policy arsip | Cabut akses bila visibility berubah; hapus sesuai retention |
| Learning material | Selama class/resource relevan | Arsip/hapus sesuai policy course/class |
| Assignment submission | Mengikuti policy pembelajaran, audit, dan retensi | Tidak dihapus sebelum kewajiban akademik/audit terpenuhi |
| Profile verification document | Mengikuti kebutuhan verifikasi dan retention data personal | Restricted delete/erase sesuai policy dan legal basis |
| Regulation file | Mengikuti archival/legal retention | Historis tidak dihapus hanya karena legal status revoked/expired |
| Certificate PDF | Mengikuti lifecycle sertifikat dan retention record | Revocation tidak menghapus history tanpa policy khusus |
| Audit-supporting file | Mengikuti audit/retention policy | Delete hanya sesuai policy yang disetujui |

Penghapusan dapat berupa soft delete metadata dan hard delete bytes sesuai retention policy. Hard delete harus mempertimbangkan backup, legal hold, audit, dan kebutuhan historis.

---

## Quarantine dan Incident Handling

File harus dikarantina ketika terdeteksi atau diduga:

- Malware atau signature mencurigakan.
- MIME/content mismatch.
- Payload executable/script aktif yang tidak diizinkan.
- Pelanggaran ukuran, ekstensi, atau policy domain.
- Konten yang melanggar keamanan/privasi.
- File yang terkait incident keamanan.

### Tindakan karantina

```text
1. Ubah lifecycle_status menjadi quarantined.
2. Cabut preview/download normal.
3. Batasi akses hanya bagi actor incident/admin berwenang.
4. Catat audit/security event tanpa mengekspos konten sensitif.
5. Beri notifikasi aman kepada uploader bila diperlukan.
6. Lakukan delete, remediation, atau release hanya melalui policy berwenang.
```

File tidak boleh dipulihkan dari karantina tanpa hasil pemeriksaan dan actor berwenang yang tercatat.

---

## Audit File dan Export

### Kejadian audit minimum

- Upload dimulai, disimpan, tersedia, ditolak, dikarantina, dan dihapus bila relevan.
- Perubahan visibility public/internal/restricted.
- Preview/download file restricted bila policy mensyaratkan.
- Request/generasi/download/expiry/delete export.
- Perubahan file regulasi/version/certificate/profile evidence yang berdampak domain.
- Failed access yang relevan atau anomaly menurut security policy.

### Field audit minimum

```text
actor_id atau system actor
action
file_id atau export_id
resource_type
resource_id
result
timestamp
reason bila diwajibkan
request context aman
```

Audit tidak boleh menyimpan path private mentah, plain token, credential, session ID, atau isi dokumen restricted kecuali metadata minimum benar-benar diperlukan dan diizinkan.

---

## Konfigurasi dan Tanggung Jawab Service

### Konfigurasi

| Konfigurasi | Tanggung jawab |
|---|---|
| `app/Config/FileStorage.php` | Disk, root storage, visibility, max size, allowlist baseline, retention, temporary/export policy |
| `app/Config/Mimes.php` | Referensi MIME; bukan satu-satunya validasi file |
| `app/Config/Images.php` | Pengolahan image yang diizinkan |
| `app/Config/Security.php` | Kebijakan keamanan framework yang relevan |
| `app/Config/Notification.php` | Channel notifikasi file/export bila dibutuhkan |

### Service dan controller

| Komponen | Tanggung jawab |
|---|---|
| `Shared\FileController` | Menerima route upload/preview/download/metadata; tidak mengambil keputusan akses final sendiri |
| `FileStorageService` | Menyimpan file, membuat nama/path aman, mengelola lifecycle dan metadata storage |
| `FileAccessService` | Memeriksa visibility, lifecycle, ownership, assignment, permission, workflow, dan capability akses |
| `FilePreviewService` | Menyajikan preview aman sesuai MIME/policy |
| `FileDownloadService` | Mengirim download dengan header dan authorization aman |
| `ExportService` | Membuat dan mengelola lifecycle export |
| `Shared\ExportController` | Melayani download export setelah ExportService/access validation |
| `AuditService` | Mencatat audit event file/export sesuai policy |
| `NotificationService` | Mengirim notifikasi status file/export tanpa data restricted berlebihan |

---

## Checklist Implementasi

Sebelum storage/file feature dinyatakan `Backend Ready`, pastikan:

- [ ] Semua root storage sesuai directory tree dan tidak ada root liar.
- [ ] File internal/restricted tidak disimpan di public root.
- [ ] Public file hanya dipromosikan setelah lifecycle `available` dan visibility `public`.
- [ ] Upload memvalidasi context domain, authorization, MIME, signature bila tersedia, ekstensi, ukuran, dan nama file.
- [ ] File disimpan dengan nama/path server-side yang aman.
- [ ] `FileAsset` metadata menyimpan visibility, lifecycle, resource relation, dan actor upload.
- [ ] Preview/download melalui `FileController` dan `FileAccessService`.
- [ ] Access file memeriksa account status, lifecycle, visibility, ownership/assignment/permission, dan workflow resource.
- [ ] Preview tidak menjalankan konten aktif yang tidak aman.
- [ ] Export bersifat private, scoped, memiliki expiry, dan tidak dapat diunduh berdasarkan tebakan path.
- [ ] Quarantine mencabut akses normal dan menghasilkan audit/security event.
- [ ] Retention/cleanup tersedia untuk temporary upload dan export.
- [ ] Audit tidak merekam plain token, credential, session ID, path private mentah, atau isi restricted.
- [ ] Feature test mencakup upload valid/invalid, access denial, ownership, assignment, visibility, preview, download, quarantine, export expiry, dan retention cleanup.

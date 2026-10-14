# BegawiPBJ — Workflow Contract

## Tujuan

Dokumen ini menetapkan kontrak workflow dan state machine untuk proses bisnis BegawiPBJ. Kontrak mencakup status, transisi valid, actor, permission, alasan, audit, side effect, serta aturan umum eksekusi workflow.

Dokumen ini melengkapi dan tidak mengubah kontrak yang telah ditetapkan dalam:

```text
README.md
dokumen/directory-tree.md
dokumen/route-contract.md
dokumen/ui-boundary.md
dokumen/auth-and-rbac.md
```

Workflow berlaku pada registrasi/account lifecycle, profile change, role request, approval, konten Bunga Rampai, regulasi, berita, event, sertifikat, serta file dan export yang terkait proses domain.

---

## Prinsip Workflow

- Workflow adalah state machine; perubahan status hanya boleh melalui transisi yang didefinisikan.
- Controller tidak boleh melakukan perubahan status secara langsung tanpa service/workflow policy.
- Setiap transisi memeriksa actor, account status, role, permission, ownership, assignment, delegated scope, dan state resource.
- Aksi workflow yang mengubah state wajib memakai request state-changing, CSRF untuk flow browser, dan audit context.
- Transisi sensitif wajib menyimpan alasan/notes bila kebijakan mewajibkannya.
- Audit merekam actor, action, resource, state sebelum/sesudah, hasil, timestamp, dan konteks aman.
- Password, token, secret, session ID, serta data restricted berlebihan tidak boleh dimasukkan dalam audit snapshot.
- Publikasi tidak boleh terjadi hanya karena record berhasil dibuat; resource harus melewati workflow yang sah.
- Reject, revoke, archive, dan cancellation tidak menghapus record historis kecuali retention policy secara eksplisit mengizinkan.
- Workflow harus idempotent untuk request yang berpotensi diulang dan aman terhadap race condition pada aksi kuota, approval, penerbitan, atau transisi serentak.

---

## Konsep Workflow Bersama

### Status workflow umum

Beberapa domain memakai status umum berikut, dengan arti yang dapat disesuaikan oleh domain:

| Status | Makna |
|---|---|
| `draft` | Data sedang disiapkan dan belum diajukan/publikasi |
| `submitted` | Data telah diajukan untuk proses berikutnya |
| `in_review` | Data sedang ditinjau actor berwenang |
| `approved` | Data disetujui tetapi belum selalu tersedia publik |
| `published` | Data tersedia sesuai visibility publik yang ditetapkan |
| `revision_required` | Data harus diperbaiki oleh pihak pengaju/pemilik |
| `rejected` | Data/pengajuan ditolak dengan alasan yang tercatat |
| `archived` | Data disimpan sebagai arsip dan tidak aktif untuk proses normal |

Status umum tidak boleh dipakai secara otomatis pada domain yang mempunyai lifecycle khusus, seperti account, event registration, certificate, atau event.

### Actor workflow

| Actor | Tanggung jawab workflow |
|---|---|
| Guest | Tidak memiliki transisi internal; dapat mengirim form publik yang diizinkan |
| Owner | Membuat, menyimpan draft, mengirim, memperbaiki, atau membatalkan resource miliknya bila diizinkan |
| Participant | Menjalankan aktivitas pembelajaran, submission, feedback, atau registrasi event sesuai ownership/enrollment |
| Trainer | Mengelola resource kelas yang ditugaskan sesuai permission/assignment |
| Evaluator | Menilai resource yang ditugaskan sesuai assignment |
| Operator | Menjalankan verifikasi/operasional dalam delegated scope |
| Admin | Meninjau, menyetujui, menolak, mempublikasikan, dan mengelola domain sesuai permission |
| Super Admin | Menjalankan administrasi strategis/system-level sesuai permission; tetap diaudit |
| System | Menjalankan transisi otomatis terjadwal, misalnya expiry, auto-close, atau cleanup; actor dicatat sebagai system |

### Bukti transisi

Setiap transisi yang relevan menyimpan:

```text
resource_type
resource_id
from_state
to_state
action
actor_id atau system actor
actor_role/permission snapshot bila diperlukan
reason atau notes bila diwajibkan
created_at
request context aman
```

---

## Account Lifecycle Workflow

Account status teknis mengikuti kontrak Auth/RBAC:

```text
draft
submitted
pending_verification
revision_required
active
rejected
inactive
suspended
archived
```

### Transisi account

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — | Buat draft registrasi | `draft` | Guest/System | Data akun dasar dapat belum lengkap |
| `draft` | Simpan/update registrasi | `draft` | Owner/Guest flow valid | Validasi per langkah registrasi |
| `draft` | Submit registrasi | `submitted` | Owner/Guest flow valid | Syarat registrasi lengkap dan consent tersedia |
| `submitted` | Kirim verifikasi email | `pending_verification` | System | Token verifikasi aman dan kedaluwarsa |
| `pending_verification` | Verifikasi email | `pending_verification` atau `active` | Owner/System | Tetap pending bila verifikasi profil diperlukan |
| `submitted` / `pending_verification` | Minta revisi | `revision_required` | Operator/Admin dengan scope | Alasan wajib |
| `revision_required` | Perbaiki dan submit ulang | `submitted` | Owner | Validasi perubahan dan audit |
| `submitted` / `pending_verification` | Setujui/aktifkan | `active` | Operator/Admin dengan scope | Verifikasi selesai, role default dapat diberikan |
| `submitted` / `pending_verification` / `revision_required` | Tolak | `rejected` | Operator/Admin dengan scope | Alasan wajib |
| `active` | Nonaktifkan | `inactive` | Admin dengan permission | Alasan wajib bila tindakan administratif |
| `inactive` | Aktifkan kembali | `active` | Admin dengan permission | Audit wajib |
| `active` | Tangguhkan | `suspended` | Admin dengan permission | Alasan wajib; sesi dapat diinvalidasi |
| `suspended` | Pulihkan | `active` | Admin dengan permission | Audit wajib |
| `inactive` / `rejected` / `suspended` | Arsipkan | `archived` | Admin dengan permission/System policy | Mengikuti retention dan policy |

### Side effect account

- Saat `active`, sistem dapat memberikan role default `public_user` dan `participant` sesuai kebijakan.
- Saat `suspended`, session aktif dapat diinvalidasi dan token sensitif dapat dicabut.
- Saat `rejected`, alasan ditampilkan secara proporsional tanpa mengungkap data reviewer yang tidak perlu.
- Saat `archived`, account tidak dapat memakai area terautentikasi.
- Setiap perubahan account status menghasilkan audit record dan notifikasi yang aman bila dikonfigurasi.

---

## Profile Change Workflow

Profile change request digunakan untuk perubahan profil yang memengaruhi data terverifikasi, tipe profil, organisasi, data kepegawaian, peran pengadaan, atau data restricted lain yang memerlukan review.

### Status

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

### Transisi

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — / `draft` | Buat/simpan request | `draft` | Owner | Hanya perubahan data milik owner |
| `draft` | Ajukan request | `submitted` | Owner | Payload tervalidasi; alasan perubahan bila diperlukan |
| `submitted` | Ambil untuk review | `in_review` | Operator/Admin dengan scope | Tidak boleh diproses actor tak berwenang |
| `submitted` / `in_review` | Minta revisi | `revision_required` | Operator/Admin dengan scope | Alasan wajib |
| `revision_required` | Perbaiki dan ajukan ulang | `submitted` | Owner | Versi perubahan harus dapat ditelusuri |
| `submitted` / `in_review` | Setujui | `approved` | Operator/Admin dengan scope | Terapkan perubahan atomik setelah approval |
| `submitted` / `in_review` | Tolak | `rejected` | Operator/Admin dengan scope | Alasan wajib |
| `draft` / `submitted` / `revision_required` | Batalkan | `cancelled` | Owner | Tidak boleh bila sudah final/diterapkan |
| `approved` / `rejected` / `cancelled` | Arsipkan | `archived` | System/Admin | Mengikuti retention policy |

### Side effect

- Perubahan profile yang disetujui diterapkan melalui `ProfileChangeRequestService` dan dicatat pada audit.
- Perubahan data terverifikasi dapat memicu re-verification dan pembatasan sementara pada akses tertentu bila policy mengharuskan.
- Dokumen bukti yang terlampir tetap private dan mengikuti file access policy.

---

## Role Request Workflow

Role request hanya digunakan untuk permohonan role yang dapat diajukan sendiri oleh user aktif:

```text
trainer
evaluator
```

Role `operator`, `admin`, dan `super_admin` tidak dibuat melalui self-service role request.

### Status

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

### Transisi

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — / `draft` | Buat/simpan request | `draft` | Owner aktif | Target role harus `trainer` atau `evaluator` |
| `draft` | Ajukan request | `submitted` | Owner aktif | Alasan/kelayakan tersedia sesuai policy |
| `submitted` | Mulai review | `in_review` | Admin dengan `role.request.review` | Reviewer tidak boleh memproses request sendiri bila segregation policy berlaku |
| `submitted` / `in_review` | Minta revisi | `revision_required` | Reviewer berwenang | Alasan wajib |
| `revision_required` | Perbaiki dan ajukan ulang | `submitted` | Owner aktif | Payload tervalidasi |
| `submitted` / `in_review` | Setujui | `approved` | Reviewer berwenang | Role assignment dilakukan atomik dan diaudit |
| `submitted` / `in_review` | Tolak | `rejected` | Reviewer berwenang | Alasan wajib |
| `draft` / `submitted` / `revision_required` | Batalkan | `cancelled` | Owner | Tidak boleh setelah final |
| `approved` / `rejected` / `cancelled` | Arsipkan | `archived` | System/Admin | Retention policy |

### Side effect

- Approval menghasilkan `UserRole` baru atau mengaktifkan assignment role yang relevan.
- Approval role tidak otomatis memberi permission di luar matrix role-permission yang berlaku.
- Role grant/revoke harus dicatat oleh `RoleAssignmentService` dan `AuditService`.
- Jika role memerlukan assignment class, assignment tetap dibuat melalui proses terpisah; grant role saja tidak otomatis memberi akses semua class.

---

## Generic Approval Workflow

Approval generik digunakan oleh resource yang memerlukan keputusan terstruktur, misalnya verifikasi profil, publikasi konten, regulasi, event, sertifikat, atau konfigurasi tertentu.

### Status

```text
pending
in_review
approved
rejected
revision_required
cancelled
expired
```

### Transisi

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — | Buat approval request | `pending` | System/Owner/Domain service | Resource dan requested action valid |
| `pending` | Mulai review | `in_review` | Reviewer berwenang | Scope dan segregation of duties diperiksa |
| `pending` / `in_review` | Setujui | `approved` | Reviewer berwenang | Catat keputusan, alasan bila diwajibkan |
| `pending` / `in_review` | Tolak | `rejected` | Reviewer berwenang | Alasan wajib |
| `pending` / `in_review` | Minta revisi | `revision_required` | Reviewer berwenang | Alasan wajib |
| `pending` / `in_review` / `revision_required` | Batalkan | `cancelled` | Owner/System sesuai policy | Tidak berlaku bila keputusan final sudah diterapkan |
| `pending` / `in_review` | Kedaluwarsa | `expired` | System | Deadline approval tercapai |

Generic approval tidak menggantikan state resource domain. Approval `approved` hanya memicu transisi resource apabila domain policy mengizinkan.

---

## Publication Workflow: Bunga Rampai

Bunga Rampai mencakup artikel, FAQ, studi kasus, template dokumen, media, poster, flyer, infografik, gallery, video, dan tautan eksternal.

### Status publikasi

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

### Transisi

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — / `draft` | Buat/simpan konten | `draft` | Author berizin/Admin | Asset dan metadata dapat belum lengkap |
| `draft` | Ajukan publikasi | `submitted` | Author berizin/Admin | Metadata, kategori, visibility, dan asset tervalidasi |
| `submitted` | Mulai review | `in_review` | Reviewer dengan `knowledge.publish.review` | Cek konten, source, media, dan policy |
| `submitted` / `in_review` | Setujui | `approved` | Reviewer berwenang | Belum tampil publik sampai publish |
| `approved` | Publikasikan | `published` | Admin dengan publish authority/System schedule | Visibility public dan publication time valid |
| `submitted` / `in_review` / `approved` | Minta revisi | `revision_required` | Reviewer berwenang | Alasan wajib |
| `revision_required` | Perbaiki dan ajukan ulang | `submitted` | Owner/Author | Revisi tercatat |
| `submitted` / `in_review` / `approved` | Tolak | `rejected` | Reviewer berwenang | Alasan wajib |
| `published` | Arsipkan | `archived` | Admin dengan publish authority | Tidak lagi dipromosikan/normal listing sesuai policy |
| `archived` | Publikasikan kembali | `published` | Admin dengan publish authority | Visibility dan relevansi dicek ulang |

### Side effect publikasi

- Konten hanya dapat muncul di Landing/Public Area ketika berstatus `published` dan visibility mengizinkan.
- Pencarian, carousel, featured content, dan media gallery hanya mengambil konten public/published.
- Asset media public harus memiliki status visibility yang sesuai sebelum tersedia di public asset area.
- Konten dengan iframe/link eksternal harus melewati validasi provider/policy sebelum publish.

---

## Regulation Workflow

Regulasi memiliki dua dimensi status yang harus dipisahkan:

```text
workflow_status
→ lifecycle editorial/publikasi record regulasi.

legal_status
→ status hukum regulasi.
```

### Workflow status regulasi

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

### Legal status regulasi

```text
active
amended
revoked
expired
```

### Transisi workflow regulasi

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — / `draft` | Buat/simpan regulasi | `draft` | Admin berizin | Metadata awal dapat belum lengkap |
| `draft` | Ajukan review | `submitted` | Admin berizin | Nomor, penerbit, metadata, kategori, dan source tervalidasi |
| `submitted` | Mulai review | `in_review` | Reviewer `regulation.publish.review` | Validasi metadata, legal relation, visibility, file |
| `submitted` / `in_review` | Setujui | `approved` | Reviewer berwenang | Belum tampil publik sampai publish |
| `approved` | Publikasikan | `published` | Admin berwenang/System schedule | Visibility public dan file policy diperiksa |
| `submitted` / `in_review` / `approved` | Minta revisi | `revision_required` | Reviewer berwenang | Alasan wajib |
| `revision_required` | Perbaiki dan ajukan ulang | `submitted` | Owner/Admin berizin | Revisi metadata/version/asset tercatat |
| `submitted` / `in_review` / `approved` | Tolak | `rejected` | Reviewer berwenang | Alasan wajib |
| `published` | Arsipkan | `archived` | Admin berwenang | Mengikuti kebijakan arsip historis |
| `archived` | Publikasikan kembali | `published` | Admin berwenang | Validasi visibility dan legal metadata |

### Aturan legal status

- `legal_status` dapat berubah pada regulasi berstatus `published` tanpa mengubah `workflow_status` menjadi tidak publik.
- Regulasi `amended`, `revoked`, atau `expired` tetap dapat terlihat sebagai referensi historis bila workflow tetap `published` dan visibility mengizinkan.
- Legal relation seperti `amends`, `revokes`, `replaces`, `implements`, dan `references` harus tervalidasi dan tidak boleh menciptakan relasi konflik/duplikat.
- Penambahan version file, perubahan current version, dan legal relation harus memiliki audit record.

### File regulasi

- Metadata regulasi dapat tetap dipublikasikan ketika file belum tersedia.
- Jika file belum tersedia, status UI adalah **File belum tersedia** dan preview/download dinonaktifkan.
- File regulasi internal/restricted tidak boleh dipublikasikan hanya karena record regulasi berstatus `published`; visibility file diperiksa secara terpisah.

---

## News Workflow

### Status

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

### Transisi

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — / `draft` | Buat/simpan berita | `draft` | Author/Admin berizin | Judul/slug/konten dapat belum lengkap |
| `draft` | Ajukan publikasi | `submitted` | Author/Admin berizin | Konten dan metadata tervalidasi |
| `submitted` | Mulai review | `in_review` | Reviewer berwenang | Cek konten, cover, schedule, visibility |
| `submitted` / `in_review` | Setujui | `approved` | Reviewer berwenang | Siap publish/schedule |
| `approved` | Publikasikan | `published` | Admin berwenang/System schedule | Publication time telah tercapai |
| `submitted` / `in_review` / `approved` | Minta revisi | `revision_required` | Reviewer berwenang | Alasan wajib |
| `revision_required` | Perbaiki dan ajukan ulang | `submitted` | Owner/Author | Revisi dicatat |
| `submitted` / `in_review` / `approved` | Tolak | `rejected` | Reviewer berwenang | Alasan wajib |
| `published` | Arsipkan | `archived` | Admin berwenang | Tetap historis sesuai policy |

Berita publik hanya menampilkan status `published` dan visibility public.

---

## Event Workflow

Event memiliki status operasional sendiri yang berbeda dari generic publication workflow.

### Status event

```text
draft
coming_soon
open_registration
full
finished
cancelled
```

### Transisi event

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — / `draft` | Buat/simpan event | `draft` | Admin berizin | Metadata dapat belum lengkap |
| `draft` | Umumkan event | `coming_soon` | Admin berizin | Informasi dasar dan visibility valid |
| `draft` / `coming_soon` | Buka registrasi | `open_registration` | Admin berizin/System schedule | Periode registrasi aktif, kuota valid |
| `open_registration` | Kuota penuh | `full` | System/Admin | Kuota terpenuhi secara transaction-safe |
| `full` | Buka kembali | `open_registration` | System/Admin | Kuota bertambah/cancellation tersedia |
| `open_registration` / `full` / `coming_soon` | Selesaikan event | `finished` | Admin/System | Waktu/event completion policy terpenuhi |
| `draft` / `coming_soon` / `open_registration` / `full` | Batalkan event | `cancelled` | Admin berizin | Alasan wajib; peserta diberi notifikasi bila relevan |

### Aturan event

- Event `coming_soon`, `open_registration`, `full`, atau `finished` dapat tampil publik sesuai visibility.
- Pendaftaran peserta hanya diterima pada status `open_registration` dan dalam periode registrasi yang valid.
- Event status `full` tidak menerima pendaftaran normal; policy waitlist dapat tetap mengizinkan pendaftaran waitlist.
- Event `finished` tidak menerima registrasi baru.
- Event `cancelled` tidak menerima registrasi dan harus menampilkan informasi pembatalan sesuai policy.

---

## Event Registration Workflow

### Status pendaftaran

```text
registered
waitlisted
cancelled
rejected
```

### Transisi

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — | Daftar event | `registered` | Participant/User aktif | Event open, kapasitas tersedia, syarat terpenuhi |
| — | Daftar saat kuota penuh | `waitlisted` | Participant/User aktif | Event full dan waitlist diaktifkan |
| `waitlisted` | Promosikan peserta | `registered` | System/Admin | Kapasitas tersedia; transaction-safe |
| `registered` / `waitlisted` | Batalkan pendaftaran | `cancelled` | Owner/Admin | Mengikuti batas waktu cancellation |
| `registered` / `waitlisted` | Tolak pendaftaran | `rejected` | Admin berizin | Alasan wajib bila review diperlukan |

### Side effect

- Kuota dan waitlist harus dikelola secara atomic/transaction-safe.
- Promosi waitlist tidak boleh menghasilkan overbooking.
- Perubahan status dapat memicu notifikasi aman.
- Sertifikat event hanya dievaluasi untuk pendaftaran yang memenuhi status/attendance policy.

---

## Attendance Workflow

### Status kehadiran

```text
present
absent
excused
```

### Aturan

| Aksi | Actor minimum | Ketentuan |
|---|---|---|
| Catat/ubah kehadiran | Operator/Admin dengan scope dan permission | Event terkait harus valid; peserta harus memiliki pendaftaran yang relevan |
| Koreksi kehadiran | Operator/Admin berwenang | Alasan dan audit wajib untuk koreksi setelah event selesai bila policy mengharuskan |
| Lihat kehadiran sendiri | Owner/Participant | Hanya record dirinya sendiri |

Kehadiran `present` dapat menjadi syarat sertifikat event bila event mengaktifkan sertifikat.

---

## Certificate Workflow

Certificate memiliki lifecycle khusus dan tidak boleh dihapus untuk menyembunyikan riwayat revocation.

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
| — | Evaluasi eligibility | `pending` | System/Certificate service | Source learning/event valid dan memenuhi policy minimum |
| `pending` | Terbitkan | `issued` | System/Admin dengan `certificate.issue` | Eligibility valid dan nomor/token unik tersedia |
| `issued` | Kedaluwarsa | `expired` | System | Masa berlaku berakhir bila expiry digunakan |
| `issued` / `expired` | Cabut | `revoked` | Admin dengan authority | Alasan wajib dan audit wajib |
| `revoked` | Ganti sertifikat | `issued` untuk record pengganti | Admin/System | Relasi replacement dan riwayat harus dicatat |

### Aturan sertifikat

- Satu sumber hanya memiliki satu sertifikat resmi aktif berdasarkan `UNIQUE(source_type, source_id)` atau policy replacement yang setara.
- Source certificate adalah `learning_enrollment` atau `event_registration`.
- Sertifikat learning hanya dapat diterbitkan bila seluruh syarat eligibility terpenuhi.
- Sertifikat event hanya dapat diterbitkan bila event selesai, pendaftaran sah, dan attendance memenuhi syarat.
- Verification token dan QR code dibuat ketika sertifikat diterbitkan; token harus unik dan aman.
- Halaman verifikasi publik hanya menampilkan data minimum yang diperlukan untuk validasi.
- Revocation tidak menghapus certificate record atau audit history.

### Eligibility learning

```text
class.certificate_enabled
+ enrollment sah dan tidak withdrawn/cancelled
+ seluruh lesson wajib selesai
+ pre-test selesai bila aktif
+ quiz wajib selesai
+ assignment wajib graded bila aktif
+ post-test selesai bila aktif
+ final_score >= passing_grade
+ feedback peserta selesai bila diwajibkan
+ tidak ada administrative hold
+ belum ada certificate dari enrollment yang sama
```

### Eligibility event

```text
event.certificate_enabled
+ event.status = finished
+ registration.status = registered
+ attendance.status = present
+ belum ada certificate dari event registration yang sama
```

---

## Learning Assessment Workflow

### Enrollment

Enrollment lifecycle minimum untuk mendukung akses pembelajaran:

```text
pending
active
completed
withdrawn
cancelled
```

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — | Ajukan/buat enrollment | `pending` atau `active` | Participant/Admin/System | Mengikuti policy enrollment/class |
| `pending` | Aktifkan enrollment | `active` | System/Admin/approval policy | Kapasitas, periode, prerequisite, dan payment/approval bila ada |
| `active` | Tandai selesai | `completed` | System | Completion policy terpenuhi |
| `pending` / `active` | Withdraw/cancel | `withdrawn` atau `cancelled` | Owner/Admin/System | Mengikuti waktu/policy class |

### Lesson progress

```text
not_started
in_progress
completed
```

- Participant hanya dapat memperbarui progress pada lesson yang dapat diakses melalui enrollment sah.
- Completion wajib memvalidasi komponen lesson yang diperlukan; tidak boleh hanya dipercaya dari input client.
- Perubahan progress yang relevan untuk eligibility sertifikat harus dapat ditelusuri.

### Quiz attempt

```text
started
submitted
graded
invalidated
```

- Participant dapat memulai attempt bila quiz tersedia, enrollment aktif, dan attempt policy mengizinkan.
- Submit mengunci jawaban sesuai policy dan menjalankan auto-grading bila berlaku.
- Hanya attempt yang valid diperhitungkan untuk best attempt/final score.
- Admin/System dapat invalidate attempt dengan alasan dan audit bila diperlukan.

### Assignment submission

```text
draft
submitted
in_review
revision_required
graded
cancelled
```

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — / `draft` | Simpan submission | `draft` | Participant dengan enrollment | Assignment tersedia |
| `draft` | Submit | `submitted` | Participant dengan enrollment | Deadline dan validation dipenuhi |
| `submitted` | Ambil review | `in_review` | Evaluator assigned | Assignment evaluator diperiksa |
| `submitted` / `in_review` | Minta revisi | `revision_required` | Evaluator assigned | Feedback/alasan wajib |
| `revision_required` | Revisi dan submit ulang | `submitted` | Participant owner | Mengikuti policy attempt/revision |
| `submitted` / `in_review` | Beri nilai | `graded` | Evaluator assigned | Grade/feedback tervalidasi |
| `draft` / `submitted` / `revision_required` | Batalkan | `cancelled` | Participant/Admin sesuai policy | Tidak dapat membatalkan setelah graded |

### Feedback peserta

```text
not_started
submitted
```

- Feedback hanya dapat dikirim oleh owner enrollment.
- Bila feedback diwajibkan untuk sertifikat, status `submitted` menjadi salah satu syarat eligibility.

---

## File Workflow

File metadata dan visibility dikelola terpisah dari record domain agar akses file tetap aman.

### Visibility file

```text
public
internal
restricted
```

### Lifecycle file minimum

```text
pending_upload
stored
scanned
available
rejected
quarantined
deleted
```

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — | Mulai upload | `pending_upload` | Actor berizin | MIME/size policy diperiksa awal |
| `pending_upload` | Simpan file | `stored` | File service | Path private/public sesuai visibility |
| `stored` | Scan/validate | `scanned` | System | Malware/content validation bila tersedia |
| `scanned` | Sediakan file | `available` | System | Metadata dan visibility valid |
| `stored` / `scanned` | Tolak | `rejected` | System/Admin | Alasan wajib bila user-visible |
| `stored` / `scanned` / `available` | Karantina | `quarantined` | System/Admin | Security/validation issue |
| `available` / `rejected` / `quarantined` | Hapus | `deleted` | Authorized actor/System retention | Audit dan retention policy |

### Aturan file

- File `public` dapat ditempatkan pada public asset area hanya setelah dinyatakan `available` dan visibility public.
- File `internal`/`restricted` berada di `writable/uploads/` dan hanya dapat melalui FileAccessService.
- Preview/download memeriksa lifecycle `available`, visibility, ownership/assignment/permission, dan resource workflow.
- File yang `quarantined`, `rejected`, atau `deleted` tidak dapat diakses untuk preview/download normal.

---

## Export Workflow

### Status export

```text
queued
generating
ready
failed
expired
deleted
```

### Transisi

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — | Request export | `queued` | Actor dengan permission export | Scope/filter export tervalidasi |
| `queued` | Mulai generate | `generating` | System | Job/worker tersedia |
| `generating` | Generate berhasil | `ready` | System | File disimpan private; owner/scope dicatat |
| `generating` | Generate gagal | `failed` | System | Error aman dicatat |
| `ready` | Kedaluwarsa | `expired` | System | Retention berakhir |
| `ready` / `failed` / `expired` | Hapus | `deleted` | System/authorized actor | Retention/audit policy |

Export download hanya diizinkan untuk owner export atau actor dengan permission/scope yang sesuai.

---

## Notifikasi Workflow

Notifikasi bukan sumber kebenaran state. Notifikasi merefleksikan transisi domain yang telah berhasil dilakukan.

| Peristiwa | Notifikasi yang dapat dikirim |
|---|---|
| Registrasi submitted/pending/revision/approved/rejected | Status registrasi dan tindakan berikutnya |
| Email verification | Permintaan verifikasi atau status verifikasi |
| Role/profile request | Status request, revisi, approval, atau rejection |
| Enrollment/class/assignment | Konfirmasi enrollment, penugasan, deadline, atau perubahan kelas |
| Assessment | Hasil grading, feedback, atau permintaan revisi |
| Publication | Status review/publish konten atau regulasi |
| Event | Registrasi, waitlist, promosi, perubahan jadwal, pembatalan, attendance |
| Certificate | Eligibility, certificate issued, revoked, expired, replacement |
| Export | Export ready/failed/expired |

Notifikasi tidak boleh memuat secret, token, atau data restricted yang tidak diperlukan.

---

## Audit Workflow

Aksi berikut minimal harus menghasilkan audit event:

- Account status transition.
- Profile change request transition.
- Role request transition dan role assignment/revocation.
- Generic approval decision.
- Publish/archive/unpublish domain content bila tersedia.
- Regulation workflow, legal status, version, relation, dan file change.
- Event status, event registration status, attendance correction.
- Certificate issue, verify, revoke, expire, replace.
- Grade/revision/invalidated assessment jika policy mensyaratkan.
- File quarantine/delete dan akses file restricted bila policy mensyaratkan.
- Export request, download, expire, dan delete bila policy mensyaratkan.
- Configuration, permission, dan role-permission changes.

---

## Checklist Implementasi Workflow

Sebelum workflow dinyatakan `Backend Ready`, pastikan:

- [ ] State enum terdokumentasi dan dipakai konsisten pada migration/model/service/view.
- [ ] Transisi diletakkan pada service/workflow layer, bukan controller langsung.
- [ ] Setiap transisi memvalidasi actor, permission, ownership/assignment, dan state saat ini.
- [ ] Aksi reject/revision/revoke/suspend/cancel yang memerlukan alasan memvalidasi alasan tersebut.
- [ ] Transisi menghasilkan audit record tanpa secret atau data restricted berlebihan.
- [ ] Side effect seperti notifikasi, role assignment, session invalidation, atau file visibility dijalankan setelah transisi berhasil.
- [ ] Transaksi database digunakan ketika perubahan harus atomik, terutama quota/waitlist, approval, role assignment, certificate issue, dan state yang saling terkait.
- [ ] Retry/idempotency ditangani untuk task system, token, job, export, dan request yang dapat terulang.
- [ ] Feature test mencakup transisi valid, transisi invalid, actor tidak berwenang, race condition relevan, dan audit result.

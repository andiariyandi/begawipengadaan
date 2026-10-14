# BegawiPengadaan — Registration Policy

## Tujuan

Dokumen ini menetapkan kebijakan registrasi, data akun dasar, tipe profil, validasi, verifikasi, lifecycle account, perubahan profil, pengajuan role, privacy, dan audit untuk BegawiPengadaan.

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
```

Registrasi menggunakan UI Auth WowDash, controller `App\Controllers\Dashboard\Auth`, dan route di bawah prefix `/dashboard`.

---

## Prinsip Registrasi

- Semua tipe pengguna memakai satu alur autentikasi dan registrasi terpadu.
- Pemilihan tipe profil menentukan data profil lanjutan, bukan role atau privilege administratif.
- Registrasi awal mengumpulkan data secukupnya dan tidak mewajibkan upload dokumen tanpa alasan risiko yang jelas.
- Data personal/restricted hanya dikumpulkan bila diperlukan untuk tujuan layanan, verifikasi, atau policy yang sah.
- Password disimpan sebagai hash; token verifikasi/reset tidak disimpan sebagai plain text.
- Account tidak memperoleh akses Workspace atau Dashboard Back-office hanya karena registrasi berhasil dibuat.
- Akses aktif ditentukan oleh account status, verifikasi, role, permission, assignment, ownership, serta workflow terkait.
- Semua perubahan lifecycle account, verifikasi, role request, dan perubahan profile restricted harus dapat diaudit.

---

## Boundary dan Route

```text
Controller
app/Controllers/Dashboard/Auth/

View
app/Views/dashboard/auth/

Layout
app/Views/dashboard/layouts/auth.php

Asset
public/assets/dashboard/
```

Route registrasi:

| Method | Path | Route name | Tujuan |
|---|---|---|---|
| GET/POST | `/dashboard/daftar` | `dashboard.auth.register.account` | Akun dasar + tipe profil |
| GET/POST | `/dashboard/daftar/wilayah` | `dashboard.auth.register.region` | Wilayah/domisili dan alamat |
| GET/POST | `/dashboard/daftar/profil/{tipe}` | `dashboard.auth.register.profile` | Profil spesifik |
| GET/POST | `/dashboard/daftar/tinjau` | `dashboard.auth.register.review` | Review dan submit |
| GET | `/dashboard/daftar/berhasil` | `dashboard.auth.register.success` | Konfirmasi submit |
| GET/POST | `/dashboard/verifikasi/{token}` | `dashboard.auth.verify-email` | Verifikasi email |
| POST | `/dashboard/verifikasi/kirim-ulang` | `dashboard.auth.verify-email.resend` | Kirim ulang verifikasi |
| GET | `/dashboard/status-akun` | `dashboard.auth.account-status` | Status account |

Route registrasi memakai filter `guest` dan `rateLimit:registration` pada endpoint state-changing/berisiko. Request browser yang mengubah state wajib menggunakan CSRF protection.

---

## Alur Registrasi

```text
1. Akun dasar dan pemilihan tipe profil
2. Wilayah/domisili dan alamat ringkas
3. Profil spesifik berdasarkan tipe
4. Tinjau data, persetujuan syarat penggunaan, dan kebijakan privasi
5. Submit registrasi
6. Kirim dan verifikasi email
7. Verifikasi profil oleh Operator/Admin bila diperlukan
8. Active, revision_required, atau rejected
```

### State account selama registrasi

| Tahap | Account status minimum | Catatan |
|---|---|---|
| Data belum lengkap | `draft` | Draft dapat diteruskan hanya dengan registration flow yang valid |
| Data dikirim | `submitted` | Menunggu proses berikutnya |
| Menunggu verifikasi | `pending_verification` | Email/profil/pemeriksaan belum selesai |
| Perlu perbaikan | `revision_required` | User harus memperbaiki data yang diminta |
| Selesai dan disetujui | `active` | Role default dapat diberikan |
| Tidak diterima | `rejected` | Alasan ditampilkan proporsional |

Transisi account mengikuti `dokumen/workflow-contract.md`. Controller tidak boleh mengubah status langsung di luar service/workflow layer.

---

## Tipe Profil

| Label | Nilai teknis | View profil | Tabel profil |
|---|---|---|---|
| ASN/Pegawai Pemerintah | `asn` | `dashboard/auth/register_asn` | `asn_profiles` |
| Penyedia Barang/Jasa | `provider` | `dashboard/auth/register_provider` | `provider_profiles` |
| Akademisi | `academic` | `dashboard/auth/register_academic` | `academic_profiles` |
| Masyarakat Umum | `public` | `dashboard/auth/register_public` | `public_profiles` |

Parameter `{tipe}` wajib di-whitelist terhadap empat nilai tersebut. Nilai bebas, nama kelas PHP, nama tabel, atau path view tidak boleh diterima dari input route.

---

## Data Akun Dasar

Data berikut dikumpulkan pada tahap pertama registrasi dan disimpan pada `users`.

| Field | Kolom | Wajib | Validasi | Klasifikasi |
|---|---|---:|---|---|
| Nama lengkap | `users.name` | Ya | Trim; tidak kosong; maksimum 255 karakter | Restricted |
| Email | `users.email` | Ya | Format valid; lowercase; unik; maksimum 255 karakter | Restricted |
| Username | `users.username` | Ya | Unik; karakter sesuai policy; maksimum 255 karakter | Internal |
| Password | Tidak disimpan plain | Ya | Minimum 12 karakter; huruf besar, kecil, angka, simbol | Restricted |
| Konfirmasi password | Tidak disimpan | Ya | Harus sama dengan password | Restricted transient |
| Nomor telepon | `users.phone` | Ya | Nomor aktif dengan format valid; maksimum 32 karakter | Restricted |
| Tipe profil | `users.profile_type` | Ya | `asn`, `provider`, `academic`, `public` | Internal |
| Persetujuan syarat | Bukti consent | Ya | Checkbox eksplisit; versi kebijakan dicatat pada audit/metadata bila diterapkan | Internal |
| Persetujuan privasi | Bukti consent | Ya | Checkbox eksplisit; versi kebijakan dicatat pada audit/metadata bila diterapkan | Internal |

Aturan password:

```text
Minimal 12 karakter
+ minimal satu huruf besar
+ minimal satu huruf kecil
+ minimal satu angka
+ minimal satu simbol
```

Password hanya disimpan dalam `users.password_hash` menggunakan hashing modern yang didukung framework. Password, konfirmasi password, dan hash tidak boleh dipasang pada flash message, audit snapshot, log, notification, atau response API.

---

## Wilayah dan Alamat

Data wilayah dan alamat disimpan pada `user_profiles`.

| Field | Kolom | Wajib | Validasi |
|---|---|---:|---|
| Provinsi | `province_code` | Ya | Harus ada pada `wilayah.kode` |
| Kabupaten/Kota | `regency_code` | Ya | Harus ada pada `wilayah.kode` dan konsisten dengan provinsi |
| Kecamatan | `district_code` | Ya | Harus ada pada `wilayah.kode` dan konsisten dengan kabupaten/kota |
| Desa/Kelurahan | `village_code` | Tidak | Bila diisi, harus ada pada `wilayah.kode` dan konsisten dengan kecamatan |
| Alamat ringkas | `address_line` | Ya | Tidak kosong; tidak mengumpulkan detail berlebihan yang tidak diperlukan |

### Master wilayah

Tabel `wilayah` adalah master resmi yang dibuat oleh schema fresh dan diisi melalui import manual dari `database/reference/wilayah.sql`.

```sql
CREATE TABLE wilayah (
    kode VARCHAR(13) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    PRIMARY KEY (kode),
    KEY wilayah_name_idx (nama)
) ENGINE=InnoDB;
```

Aturan:

- Aplikasi memperlakukan `wilayah` sebagai master read-only.
- Registrasi tidak membuat, mengubah, atau menghapus data wilayah.
- Validasi hierarchy provinsi → kabupaten/kota → kecamatan → desa/kelurahan dilakukan pada service/validation layer berdasarkan struktur kode master yang digunakan.
- `province_code`, `regency_code`, `district_code`, dan `village_code` memiliki foreign key ke `wilayah.kode`.

---

## Profil ASN/Pegawai Pemerintah

### Pilihan asal instansi

```text
ASN Pemprov Lampung
ASN Luar Pemprov Lampung
```

Nilai teknis `origin_type`:

```text
pemprov_lampung
kementerian_lembaga
pemerintah_daerah_lain
pemerintah_desa
bumn_bumd
lainnya
```

### Field ASN

| Field | Kolom | Wajib | Ketentuan |
|---|---|---:|---|
| NIP/NIPPK | `employee_identifier` | Bersyarat | Diisi bila tersedia sesuai policy; restricted |
| Status kepegawaian | `employment_status` | Bersyarat | PNS/PPPK atau nilai terkendali lain |
| Asal instansi | `origin_type` | Ya | Salah satu nilai teknis yang diizinkan |
| OPD/unit master | `organization_unit_id` | Bersyarat | Wajib bila memakai unit master Pemprov Lampung |
| Nama instansi | `institution_name` | Bersyarat | Wajib bila tidak memakai unit master yang sesuai |
| Nama unit kerja | `unit_name` | Bersyarat | Wajib bila diperlukan untuk konteks instansi luar |
| Jabatan | `position_name` | Bersyarat | Input terkontrol atau validasi maksimum 255 karakter |
| Pangkat/golongan | `rank_grade` | Tidak | Maksimum 128 karakter |
| Email kedinasan | `official_email` | Tidak | Email valid bila diisi; restricted |

ASN tidak wajib mengisi NIK dan tidak wajib mengunggah Surat Tugas pada registrasi awal.

Perangkat Desa tanpa NIP/NIPPK tidak dipaksa menggunakan tipe ASN. Mereka menggunakan tipe Masyarakat Umum dengan pekerjaan/status `Perangkat Desa`, kecuali kebijakan masa depan menyediakan tipe profile terpisah.

---

## Peran Pengadaan

Peran pengadaan berbeda dari role aplikasi. Peran ini disimpan pada `user_procurement_roles` dan tidak otomatis memberikan permission atau akses administratif.

### Master peran

```text
PA                         Pengguna Anggaran
KPA                        Kuasa Pengguna Anggaran
PPK                        Pejabat Pembuat Komitmen
PP                         Pejabat Pengadaan
POKJA_PEMILIHAN            Kelompok Kerja Pemilihan
PENGELOLA_PENGADAAN        Pengelola Pengadaan Barang/Jasa
PPTK                       Pejabat Pelaksana Teknis Kegiatan
PEJABAT_PENERIMA_HASIL     Pejabat/Panitia Penerima Hasil Pekerjaan
TIM_TEKNIS                 Tim Teknis
TIM_PENDUKUNG              Tim Pendukung
PENGAWAS_KONTRAK           Pengawas Kontrak
APIP                       Aparat Pengawasan Intern Pemerintah
LAINNYA                    Peran lainnya
```

### Ketentuan

- User dapat memiliki lebih dari satu peran pengadaan.
- Registrasi awal dapat menerima deklarasi peran tanpa upload SK/bukti wajib.
- Status awal dapat `self_declared`.
- Verifikasi lanjutan dapat menggunakan `pending`, `verified`, atau `rejected`.
- Bukti penugasan bila diminta kemudian disimpan sebagai file restricted.
- Peran pengadaan tidak sama dengan role aplikasi `participant`, `trainer`, `admin`, dan seterusnya.

---

## Profil Penyedia Barang/Jasa

Profil disimpan pada `provider_profiles`.

| Field | Kolom | Wajib | Ketentuan |
|---|---|---:|---|
| Nama badan usaha | `business_name` | Ya | Maksimum 255 karakter |
| Bentuk badan usaha | `business_form` | Ya | Perseorangan/CV/PT/Koperasi/Firma/lainnya |
| Bidang usaha | `business_field` | Tidak | Maksimum 255 karakter |
| NIB/identifier legal | `business_identifier` | Tidak | Restricted; wajib hanya bila policy fitur memerlukannya |
| Jabatan pendaftar | `registrant_position` | Ya | Maksimum 255 karakter |
| Penanggung jawab | `responsible_person` | Ya | Restricted; maksimum 255 karakter |
| Email badan usaha | `business_email` | Tidak | Email valid bila diisi |
| Alamat badan usaha | `business_address` | Tidak | Restricted; data seperlunya |

Tidak ada upload dokumen legalitas wajib pada registrasi awal, kecuali fitur tertentu menetapkan kebutuhan verifikasi secara eksplisit.

---

## Profil Akademisi

Profil disimpan pada `academic_profiles`.

| Field | Kolom | Wajib | Ketentuan |
|---|---|---:|---|
| Nama institusi | `institution_name` | Ya | Maksimum 255 karakter |
| Jenis institusi | `institution_type` | Ya | Nilai terkendali |
| Jabatan/peran | `role_name` | Ya | Dosen/peneliti/praktisi/konsultan/lainnya |
| Bidang keahlian | `expertise` | Tidak | JSON daftar nilai terkendali/input tervalidasi |
| Email institusi | `institution_email` | Tidak | Email valid bila diisi |
| Identifier profesi | `professional_identifier` | Tidak | NIDN/NUPTK/identifier lain; restricted |
| URL portofolio | `portfolio_url` | Tidak | URL HTTPS tervalidasi |

CV, portofolio, atau bukti kompetensi tidak diwajibkan pada registrasi awal. Bukti dapat diminta dalam workflow role request Trainer/Evaluator bila diperlukan.

---

## Profil Masyarakat Umum

Profil disimpan pada `public_profiles`.

| Field | Kolom | Wajib | Ketentuan |
|---|---|---:|---|
| NIK | `national_identifier` | Tidak | Diisi bila policy memerlukan; 16 digit bila digunakan; restricted |
| Pekerjaan/status | `occupation_status` | Ya | Mahasiswa/pegawai swasta/wiraswasta/pencari kerja/perangkat desa/masyarakat/lainnya |
| Organisasi | `organization_name` | Tidak | Maksimum 255 karakter |
| Bidang minat PBJ | `interest_areas` | Tidak | JSON nilai terkendali |
| Tujuan penggunaan | `usage_purpose` | Tidak | JSON nilai terkendali |

Data yang tidak diperlukan untuk layanan tidak boleh dipaksa pada tahap registrasi.

---

## Verifikasi Email

Verifikasi email memakai `email_verifications` dan controller `EmailVerificationController`.

### Ketentuan token

- Token dibuat acak menggunakan cryptographically secure random generator.
- Plain token hanya dikirim melalui kanal verifikasi; database menyimpan `token_hash`.
- Token bersifat sekali pakai dan memiliki `expires_at`.
- Token yang berhasil digunakan mencatat `verified_at`.
- Kirim ulang verifikasi memakai rate limit.
- Respon tidak boleh membocorkan keberadaan akun secara tidak perlu.
- Verifikasi email tidak memberikan role atau dashboard permission secara otomatis.

### Dampak verifikasi

```text
Email verified
→ users.email_verified_at terisi
→ lifecycle account melanjutkan verifikasi profile/policy bila diperlukan
→ account hanya menjadi active melalui workflow yang sah
```

---

## Verifikasi Profil

Verifikasi profile menggunakan `profile_verifications` dan dapat dilakukan oleh Operator/Admin sesuai delegated scope dan permission.

| Status | Makna |
|---|---|
| `pending` | Verifikasi dibuat dan menunggu review |
| `in_review` | Sedang ditinjau |
| `approved` | Data/profile disetujui |
| `revision_required` | User harus memperbaiki/melengkapi data |
| `rejected` | Data/profile ditolak |

Aturan:

- Reviewer harus memiliki scope dan permission yang relevan.
- Alasan wajib untuk `revision_required` dan `rejected`.
- Dokumen bukti profile disimpan sebagai file restricted.
- Keputusan verifikasi dicatat pada workflow transition dan audit log.
- Approval profile dapat menjadi prasyarat account `active` bila policy tipe profile/akses mensyaratkannya.

---

## Role Default dan Role Request

### Role default

Setelah account berstatus `active`, sistem dapat memberikan:

```text
public_user
participant
```

Pemberian role default harus atomik, idempotent, dan tercatat pada `user_roles` serta audit log.

### Role request

Pengguna aktif dapat mengajukan role tambahan:

```text
trainer
evaluator
```

Role berikut tidak dapat diajukan sendiri:

```text
operator
admin
super_admin
```

Workflow role request:

```text
draft
→ submitted
→ in_review
→ approved | revision_required | rejected
→ archived
```

Approval role request:

```text
role_requests.status = approved
→ RoleAssignmentService membuat/mengaktifkan user_roles
→ Audit role assignment dibuat
→ Notifikasi aman dapat dikirim
```

Role grant tidak otomatis memberi assignment pada class. Trainer/Evaluator tetap memerlukan assignment class pada `class_trainers` atau `class_evaluators` untuk mengakses resource class tertentu.

---

## Perubahan Profil

Perubahan profile aktif menggunakan `profile_change_requests` apabila menyangkut data terverifikasi, restricted, organisasi, tipe profile, peran pengadaan, atau data lain yang memerlukan review.

| Status | Makna |
|---|---|
| `draft` | Perubahan disiapkan |
| `submitted` | Perubahan diajukan |
| `in_review` | Sedang diperiksa |
| `revision_required` | User harus memperbaiki payload/dokumen |
| `approved` | Perubahan disetujui dan diterapkan secara atomik |
| `rejected` | Perubahan tidak diterima |
| `cancelled` | Dibatalkan oleh owner sesuai policy |
| `archived` | Riwayat diarsipkan |

`requested_payload` adalah data restricted bila memuat data personal. Payload tidak boleh dicatat penuh di audit/log; audit hanya menyimpan snapshot ter-redaksi atau referensi perubahan yang aman.

---

## Privacy dan Pengelolaan Data

### Data restricted registrasi

```text
Nama lengkap
Email
Nomor telepon
Alamat
NIP/NIPPK
NIK
NIB/identifier legal
Email kedinasan/institusi
Identifier profesi
Dokumen profile verification
Token verifikasi
Password/hash password
```

### Aturan privacy

- Terapkan data minimization: hanya minta field yang diperlukan.
- Terapkan purpose limitation: gunakan data hanya untuk tujuan layanan/verification yang sah.
- Masking diterapkan pada daftar user, review queue, dan UI yang tidak membutuhkan nilai lengkap.
- Data restricted tidak ditampilkan pada Statistik Publik.
- Export profile/user memerlukan permission dan audit.
- Payload request/profile evidence tidak dikirim melalui notification biasa.
- Audit snapshot wajib melalui redaction.

---

## Audit dan Notifikasi

### Audit wajib

- Pembuatan dan submit draft registrasi bila policy audit mengharuskan.
- Email verification request, verify success/failure, dan resend yang relevan.
- Account status transition.
- Profile verification decision.
- Profile change request dan decision.
- Role request, approval/rejection/revision, grant/revoke role.
- Perubahan peran pengadaan dan status verifikasi.
- Login/logout/reset password mengikuti kontrak Auth/RBAC.

Audit tidak boleh memuat password, plain token, session ID, secret, atau full restricted payload.

### Notifikasi yang dapat dikirim

- Permintaan verifikasi email.
- Registrasi diterima/pending.
- Permintaan perbaikan profile.
- Account active/rejected/suspended.
- Role request received/revision/approved/rejected.
- Profile change request decision.

Notifikasi hanya memuat informasi minimum aman dan tidak mengungkap data restricted secara berlebihan.

---

## Validasi dan Keamanan

- Semua validasi akhir dilakukan di server-side.
- Validasi client hanya untuk UX dan tidak menjadi sumber kebenaran.
- Endpoint registrasi, login, verifikasi, resend, dan reset memakai rate limit.
- Semua request browser yang mengubah state memakai CSRF protection.
- Database unique constraint menangani race condition email/username duplicate.
- Data profile spesifik hanya boleh dibuat untuk `profile_type` yang sesuai.
- Perubahan `profile_type` setelah active memakai profile change workflow dan dapat memerlukan verifikasi ulang.
- Return URL/intended route divalidasi agar tidak terjadi open redirect.
- Kegagalan login, reset password, dan verifikasi tidak boleh membocorkan account existence secara berlebihan.

---

## Checklist Implementasi

Sebelum registrasi dinyatakan `Backend Ready`, pastikan:

- [ ] Semua route registrasi sesuai route contract dan memakai Auth WowDash.
- [ ] Auth hanya memakai `app/Controllers/Dashboard/Auth/` dan `app/Views/dashboard/auth/`.
- [ ] Account status memakai enum teknis yang ditetapkan pada Auth/RBAC dan Workflow Contract.
- [ ] User/profile/tabel profil spesifik memiliki validasi dan foreign key yang sesuai.
- [ ] `wilayah` dibuat oleh schema fresh, diisi manual dari `database/reference/wilayah.sql`, dan dipakai read-only.
- [ ] Kode wilayah tervalidasi terhadap master `wilayah` dan hierarchy wilayah diperiksa server-side.
- [ ] Password dan token hanya disimpan sebagai hash.
- [ ] Registrasi tidak memberi Dashboard privilege otomatis.
- [ ] Workspace/Dashboard hanya dapat diakses account `active` dengan role/permission yang relevan.
- [ ] Role default dan role request mengikuti policy serta audit.
- [ ] Profile change restricted memakai request/review workflow.
- [ ] Dokumen evidence disimpan private/restricted melalui file storage policy.
- [ ] Feature test mencakup seluruh tipe profile, validasi wilayah, duplicate email/username, verifikasi email, account status, role request, access denial, dan audit event.

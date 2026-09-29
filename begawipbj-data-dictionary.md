# BegawiPBJ Data Dictionary

## Kamus Data Platform Ekosistem Digital PBJ Pemerintah Provinsi Lampung

Dokumen ini menetapkan kamus data resmi untuk **BegawiPBJ**, platform ekosistem digital Pengadaan Barang/Jasa Pemerintah (PBJ) Pemerintah Provinsi Lampung.

Kamus data ini mendefinisikan tabel, tujuan, kolom inti, tipe data, primary key, foreign key, unique constraint, status, dan aturan validasi bisnis. Dokumen ini harus dibaca bersama dengan:

```text
README.md
begawipbj-directory-architecture.md
assessment-and-certification-rules.md
begawipbj-database-blueprint.md
begawipbj-database-erd.md
```

Aturan yang bersifat implementatif pada migration tetap menjadi sumber teknis akhir. Jika terjadi perbedaan, perubahan harus diperbaiki pada dokumentasi, migration, ERD, dan test dalam satu perubahan yang konsisten.

---

# 1. Konvensi Data

## 1.1 Standar umum

| Elemen | Standar |
|---|---|
| DBMS | MySQL 8.4+ |
| Engine | InnoDB |
| Character set | `utf8mb4` |
| Collation | `utf8mb4_0900_ai_ci` atau collation Unicode konsisten pada server target |
| Zona waktu | UTC untuk seluruh `DATETIME` |
| PK aplikasi | `BIGINT UNSIGNED AUTO_INCREMENT` kecuali disebut lain |
| FK aplikasi | `BIGINT UNSIGNED` dengan unsigned konsisten |
| Nama tabel | plural `snake_case` |
| Nama kolom | `snake_case` |
| Timestamps | `created_at`, `updated_at` bila entitas dapat berubah |
| Soft delete | `deleted_at` hanya untuk entitas yang membutuhkan pemulihan/arsip |
| Nilai numerik | Nilai 0–100 memakai `DECIMAL(5,2)` |
| Metadata fleksibel | `JSON`, hanya untuk data yang tidak tepat dimodelkan sebagai relasi |

## 1.2 Notasi kolom

| Notasi | Arti |
|---|---|
| PK | Primary key |
| FK | Foreign key |
| UQ | Unique constraint/index |
| NN | Not null |
| NULL | Boleh kosong |
| IDX | Indexed untuk query/relasi |
| JSON | Kolom JSON |

## 1.3 Aturan waktu dan actor

- Semua timestamp disimpan dalam UTC.
- Kolom actor seperti `created_by`, `updated_by`, `issued_by`, `approved_by`, atau `grader_user_id` merujuk ke `users.id` jika actor tersedia.
- Relasi actor historis dapat memakai `ON DELETE SET NULL` agar jejak historis tetap tersimpan ketika akun dinonaktifkan/anonymized.
- Soft delete tidak boleh diterapkan pada audit log, approval history, dan revocation certificate sebagai cara menyembunyikan riwayat.

## 1.4 Status umum

Status disimpan sebagai `VARCHAR(50)` dan divalidasi oleh service/validation rule. `CHECK` constraint dapat ditambahkan bila implementasi MySQL target mendukung dan tim menyepakati pengelolaannya.

```text
workflow_status : draft | submitted | in_review | approved | published | rejected | revision_required | archived | cancelled
account_status  : pending | active | inactive | rejected | suspended
```

---

# 2. Master Wilayah dan Organisasi

## 2.1 `wilayah`

Master wilayah resmi. Data diimpor dari sumber resmi dan tidak disentuh oleh dummy/development seeder.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `kode` | `VARCHAR(13)` | NN | PK | Kode wilayah hierarkis, contoh `18`, `18.01`, `18.01.04`, `18.01.04.2001` |
| `nama` | `VARCHAR(100)` | NN | IDX | Nama wilayah resmi |

Constraint:

```text
PRIMARY KEY (kode)
INDEX wilayah_name_idx (nama)
```

Aturan:

- Panjang 2, 5, 8, atau 13 menunjukkan level provinsi, kabupaten/kota, kecamatan, atau desa/kelurahan.
- Tidak ada kolom level; level diturunkan dari format/panjang kode.
- Tidak boleh ada migration/seeder dummy yang melakukan `TRUNCATE`, `DELETE`, `UPDATE`, atau insert data fiktif pada tabel ini.

## 2.2 `organization_units`

Master OPD/unit organisasi.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier organisasi |
| `parent_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Parent pada `organization_units.id` untuk struktur hierarki |
| `code` | `VARCHAR(50)` | NN | UQ | Kode unit/OPD |
| `name` | `VARCHAR(255)` | NN | IDX | Nama resmi organisasi/unit |
| `unit_type` | `VARCHAR(50)` | NN | IDX | Jenis unit, misalnya `province`, `bureau`, `agency`, `department`, `unit` |
| `is_active` | `TINYINT(1)` | NN | IDX | `1` aktif, `0` tidak aktif |
| `sort_order` | `INT UNSIGNED` | NN |  | Urutan tampilan, default `0` |
| `created_at` | `DATETIME` | NN |  | Waktu dibuat UTC |
| `updated_at` | `DATETIME` | NN |  | Waktu diubah UTC |

Foreign key:

```text
parent_id → organization_units.id
```

Delete policy: `RESTRICT` untuk unit yang masih memiliki child, user relation, personel intelligence, atau data kompetensi.

## 2.3 `user_organizations`

Riwayat afiliasi user dengan organisasi.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier relasi |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, IDX | User terkait |
| `organization_unit_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Organisasi terkait |
| `is_primary` | `TINYINT(1)` | NN | IDX | Menandai organisasi utama pada periode relasi |
| `start_date` | `DATE` | NN |  | Tanggal mulai efektif |
| `end_date` | `DATE` | NULL |  | Tanggal akhir efektif; `NULL` berarti masih berlaku |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(user_id, organization_unit_id, start_date)
```

Aturan:

- `end_date >= start_date` bila `end_date` tidak `NULL`.
- Hanya satu organisasi utama aktif per user; validasi dilakukan `OrganizationService` dalam transaction.

---

# 3. Identity, Profil, dan RBAC

## 3.1 `users`

Kredensial akun dan status akses. Tidak menyimpan NIP/NIK atau profil rinci.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier user |
| `username` | `VARCHAR(100)` | NN | UQ | Username login; normalisasi/policy karakter dikelola aplikasi |
| `email` | `VARCHAR(191)` | NN | UQ | Email login; disimpan/diindeks dalam bentuk normalisasi yang konsisten |
| `password_hash` | `VARCHAR(255)` | NN |  | Hash password dari `password_hash()` |
| `account_status` | `VARCHAR(20)` | NN | IDX | `pending`, `active`, `inactive`, `rejected`, atau `suspended` |
| `email_verified_at` | `DATETIME` | NULL |  | Waktu verifikasi email |
| `last_login_at` | `DATETIME` | NULL |  | Waktu login sukses terakhir |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL | IDX | Soft delete bila kebijakan akun memerlukannya |

Index/constraint:

```text
UNIQUE(username)
UNIQUE(email)
INDEX(account_status, created_at)
```

## 3.2 `user_profiles`

Profil umum satu user.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier profil |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, UQ | Satu profil umum untuk satu user |
| `profile_category` | `VARCHAR(50)` | NN | IDX | `asn`, `provider`, `academic_practitioner`, `public` |
| `full_name` | `VARCHAR(255)` | NN | IDX | Nama lengkap pengguna |
| `title_prefix` | `VARCHAR(50)` | NULL |  | Gelar depan |
| `title_suffix` | `VARCHAR(100)` | NULL |  | Gelar belakang |
| `phone` | `VARCHAR(30)` | NULL |  | Nomor telepon; data terbatas |
| `avatar_file_asset_id` | `BIGINT UNSIGNED` | NULL | FK | Avatar pada `file_assets.id` |
| `province_code` | `VARCHAR(13)` | NULL | FK | Referensi `wilayah.kode` level provinsi |
| `regency_code` | `VARCHAR(13)` | NULL | FK | Referensi kabupaten/kota |
| `district_code` | `VARCHAR(13)` | NULL | FK | Referensi kecamatan |
| `village_code` | `VARCHAR(13)` | NULL | FK | Referensi desa/kelurahan |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Foreign key:

```text
user_id → users.id
avatar_file_asset_id → file_assets.id
province_code/regency_code/district_code/village_code → wilayah.kode
```

## 3.3 `user_asn_profiles`

Profil khusus ASN.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, UQ | User ASN |
| `nip` | `VARCHAR(30)` | NN | UQ | NIP; data terbatas, tidak dipublikasikan |
| `employment_status` | `VARCHAR(50)` | NULL |  | Status kepegawaian |
| `position_name` | `VARCHAR(255)` | NULL |  | Nama jabatan |
| `rank_name` | `VARCHAR(100)` | NULL |  | Pangkat |
| `grade_name` | `VARCHAR(100)` | NULL |  | Golongan |
| `organization_unit_id` | `BIGINT UNSIGNED` | NULL | FK | OPD/unit aktif bila diketahui |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 3.4 `user_provider_profiles`

Profil khusus penyedia.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, UQ | User penyedia |
| `nib` | `VARCHAR(50)` | NULL | UQ | Nomor Induk Berusaha, bila dikumpulkan |
| `company_name` | `VARCHAR(255)` | NULL | IDX | Nama badan usaha |
| `responsible_person` | `VARCHAR(255)` | NULL |  | Penanggung jawab |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 3.5 `user_academic_profiles`

Profil akademisi/praktisi.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, UQ | User akademisi/praktisi |
| `institution_name` | `VARCHAR(255)` | NULL |  | Institusi asal |
| `expertise` | `TEXT` | NULL |  | Bidang keahlian |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 3.6 `user_public_profiles`

Profil minimum untuk kategori publik bila diperlukan.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, UQ | User publik |
| `additional_information` | `JSON` | NULL | JSON | Informasi tambahan yang telah dibatasi/validasi |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 3.7 `roles`

Master role sistem.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier role |
| `code` | `VARCHAR(100)` | NN | UQ | Contoh `super_admin`, `admin`, `trainer` |
| `name` | `VARCHAR(150)` | NN |  | Nama tampilan |
| `description` | `TEXT` | NULL |  | Penjelasan role |
| `is_system` | `TINYINT(1)` | NN |  | Role sistem tidak dihapus sembarangan |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 3.8 `permissions`

Master permission granular.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier permission |
| `module` | `VARCHAR(100)` | NN | IDX | Domain, misalnya `learning`, `certificate` |
| `action` | `VARCHAR(100)` | NN | IDX | Aksi, misalnya `publish`, `grade` |
| `code` | `VARCHAR(191)` | NN | UQ | Bentuk `domain.resource.action` |
| `name` | `VARCHAR(191)` | NN |  | Nama tampilan |
| `description` | `TEXT` | NULL |  | Penjelasan |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 3.9 `role_permissions`

Pivot role-permission.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `role_id` | `BIGINT UNSIGNED` | NN | PK/FK | Role |
| `permission_id` | `BIGINT UNSIGNED` | NN | PK/FK | Permission |
| `created_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
PRIMARY KEY(role_id, permission_id)
```

## 3.10 `user_roles`

Pivot user-role.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `user_id` | `BIGINT UNSIGNED` | NN | PK/FK | User |
| `role_id` | `BIGINT UNSIGNED` | NN | PK/FK | Role |
| `assigned_by` | `BIGINT UNSIGNED` | NULL | FK | Actor pemberi role |
| `assigned_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
PRIMARY KEY(user_id, role_id)
```

## 3.11 `password_reset_tokens`

Token reset password; token mentah tidak disimpan.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, IDX | User pemilik token |
| `token_hash` | `VARCHAR(255)` | NN | UQ | Hash token reset |
| `expires_at` | `DATETIME` | NN | IDX | Batas waktu token |
| `used_at` | `DATETIME` | NULL |  | Waktu token digunakan |
| `created_at` | `DATETIME` | NN |  | UTC |

## 3.12 `login_attempts`

Pencatatan attempt login untuk keamanan/rate limit.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `identifier` | `VARCHAR(191)` | NN | IDX | Username/email yang dicoba, dinormalisasi |
| `user_id` | `BIGINT UNSIGNED` | NULL | FK | User bila teridentifikasi |
| `ip_address` | `VARCHAR(45)` | NULL | IDX | IPv4/IPv6 |
| `was_successful` | `TINYINT(1)` | NN | IDX | Status attempt |
| `attempted_at` | `DATETIME` | NN | IDX | UTC |

---

# 4. Shared Platform Services

## 4.1 `file_assets`

Metadata seluruh file; file fisik dikelola di storage privat.

| Kolom | Tipe | Null | Key | Deskripsi/aturan |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier file asset |
| `disk` | `VARCHAR(50)` | NN | IDX | Nama disk/storage, misalnya `local_private` |
| `path` | `VARCHAR(500)` | NULL | UQ | Path fisik relatif; `NULL` berarti file belum tersedia |
| `original_name` | `VARCHAR(255)` | NN |  | Nama file saat diunggah |
| `stored_name` | `VARCHAR(255)` | NULL |  | Nama fisik UUID/hash |
| `mime_type` | `VARCHAR(150)` | NULL | IDX | MIME yang tervalidasi |
| `extension` | `VARCHAR(20)` | NULL |  | Ekstensi yang tervalidasi |
| `size_bytes` | `BIGINT UNSIGNED` | NULL |  | Ukuran file |
| `checksum_sha256` | `CHAR(64)` | NULL | IDX | Checksum SHA-256 |
| `visibility` | `VARCHAR(20)` | NN | IDX | `private`, `protected`, atau `public` sesuai kebijakan |
| `uploaded_by` | `BIGINT UNSIGNED` | NULL | FK | User pengunggah |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL | IDX | Soft delete metadata bila diizinkan |

Constraint:

```text
UNIQUE(path)
```

Aturan: `path = NULL` wajib menghasilkan status UI **File belum tersedia** dan download/preview dinonaktifkan.

## 4.2 `approvals`

Status approval saat ini untuk subject polymorphic.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier approval |
| `subject_type` | `VARCHAR(100)` | NN | IDX | Tipe subject yang diizinkan |
| `subject_id` | `BIGINT UNSIGNED` | NN | IDX | ID subject |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Status workflow saat ini |
| `submitted_by` | `BIGINT UNSIGNED` | NULL | FK | Pengaju |
| `submitted_at` | `DATETIME` | NULL |  | Waktu submit |
| `reviewed_by` | `BIGINT UNSIGNED` | NULL | FK | Reviewer terakhir |
| `reviewed_at` | `DATETIME` | NULL |  | Waktu review |
| `approved_by` | `BIGINT UNSIGNED` | NULL | FK | Approver terakhir |
| `approved_at` | `DATETIME` | NULL |  | Waktu approve |
| `published_at` | `DATETIME` | NULL |  | Waktu publish |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Index:

```text
INDEX(subject_type, subject_id, workflow_status)
```

Integritas subject dijaga `ApprovalService`.

## 4.3 `approval_histories`

Riwayat transisi approval.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `approval_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Approval terkait |
| `from_status` | `VARCHAR(50)` | NULL |  | Status sebelum transisi |
| `to_status` | `VARCHAR(50)` | NN |  | Status sesudah transisi |
| `action` | `VARCHAR(100)` | NN |  | Aksi workflow |
| `notes` | `TEXT` | NULL |  | Catatan reviewer/approver |
| `acted_by` | `BIGINT UNSIGNED` | NULL | FK | Actor |
| `acted_at` | `DATETIME` | NN | IDX | UTC |

## 4.4 `audit_logs`

Audit append-only untuk aksi penting.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `actor_user_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Actor bila terautentikasi |
| `action` | `VARCHAR(191)` | NN | IDX | Kode aksi, misalnya `certificate.issue` |
| `subject_type` | `VARCHAR(100)` | NN | IDX | Tipe subjek |
| `subject_id` | `BIGINT UNSIGNED` | NULL | IDX | ID subjek bila tersedia |
| `route_name` | `VARCHAR(191)` | NULL |  | Named route |
| `request_id` | `VARCHAR(100)` | NULL | IDX | Korelasi request |
| `ip_address` | `VARCHAR(45)` | NULL |  | IPv4/IPv6 |
| `user_agent` | `VARCHAR(500)` | NULL |  | User agent terpotong/terbatas |
| `before_data` | `JSON` | NULL | JSON | Snapshot sebelum, tersanitasi |
| `after_data` | `JSON` | NULL | JSON | Snapshot sesudah, tersanitasi |
| `context_data` | `JSON` | NULL | JSON | Konteks tambahan tersanitasi |
| `created_at` | `DATETIME` | NN | IDX | UTC |

## 4.5 `notifications`

Notifikasi in-app/email.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Penerima |
| `channel` | `VARCHAR(20)` | NN |  | `in_app` atau `email` |
| `notification_type` | `VARCHAR(100)` | NN | IDX | Jenis notifikasi |
| `title` | `VARCHAR(255)` | NN |  | Judul |
| `message` | `TEXT` | NN |  | Isi pesan |
| `data` | `JSON` | NULL | JSON | Payload terbatas |
| `read_at` | `DATETIME` | NULL | IDX | Waktu dibaca |
| `sent_at` | `DATETIME` | NULL |  | Waktu dikirim |
| `failed_at` | `DATETIME` | NULL |  | Waktu gagal kirim |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

---

# 5. Arena Belajar

## 5.1 `programs`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier program |
| `code` | `VARCHAR(50)` | NN | UQ | Kode program |
| `title` | `VARCHAR(255)` | NN | IDX | Judul program |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug publik |
| `summary` | `TEXT` | NULL |  | Ringkasan |
| `description` | `LONGTEXT` | NULL |  | Deskripsi |
| `cover_file_asset_id` | `BIGINT UNSIGNED` | NULL | FK | Cover program |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Lifecycle publikasi |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat |
| `published_at` | `DATETIME` | NULL | IDX | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete |

## 5.2 `courses`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier course |
| `program_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Program induk; boleh `NULL` |
| `code` | `VARCHAR(50)` | NN | UQ | Kode course |
| `title` | `VARCHAR(255)` | NN | IDX | Judul course |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug publik |
| `summary` | `TEXT` | NULL |  | Ringkasan |
| `description` | `LONGTEXT` | NULL |  | Deskripsi |
| `learning_outcomes` | `LONGTEXT` | NULL |  | Capaian pembelajaran |
| `estimated_duration_minutes` | `INT UNSIGNED` | NULL |  | Estimasi durasi |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Lifecycle publikasi |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat |
| `published_at` | `DATETIME` | NULL | IDX | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete |

## 5.3 `learning_classes`

Pelaksanaan course per angkatan/jadwal.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier class |
| `course_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Course induk |
| `code` | `VARCHAR(50)` | NN | UQ | Kode class |
| `title` | `VARCHAR(255)` | NN | IDX | Judul pelaksanaan |
| `delivery_mode` | `VARCHAR(20)` | NN |  | `online`, `offline`, `blended` |
| `start_at` | `DATETIME` | NN | IDX | Waktu mulai UTC |
| `end_at` | `DATETIME` | NN | IDX | Waktu selesai UTC |
| `enrollment_open_at` | `DATETIME` | NULL |  | Pembukaan pendaftaran |
| `enrollment_close_at` | `DATETIME` | NULL |  | Penutupan pendaftaran |
| `quota` | `INT UNSIGNED` | NULL |  | Kapasitas; `NULL` berarti tidak dibatasi bila kebijakan mengizinkan |
| `status` | `VARCHAR(50)` | NN | IDX | Status operasional class |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete terbatas |

Aturan:

```text
end_at >= start_at
enrollment_close_at >= enrollment_open_at bila keduanya terisi
```

Konfigurasi assessment dan bobot tidak disimpan ganda sebagai sumber kebenaran di tabel ini; gunakan `assessment_configurations`.

## 5.4 `class_trainers`

Penugasan trainer ke class.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `learning_class_id` | `BIGINT UNSIGNED` | NN | PK/FK | Class |
| `user_id` | `BIGINT UNSIGNED` | NN | PK/FK | Trainer |
| `is_primary` | `TINYINT(1)` | NN |  | Trainer utama atau co-trainer |
| `assigned_by` | `BIGINT UNSIGNED` | NULL | FK | Pemberi tugas |
| `assigned_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
PRIMARY KEY(learning_class_id, user_id)
```

## 5.5 `class_evaluators`

Penugasan evaluator ke class.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `learning_class_id` | `BIGINT UNSIGNED` | NN | PK/FK | Class |
| `user_id` | `BIGINT UNSIGNED` | NN | PK/FK | Evaluator |
| `assigned_by` | `BIGINT UNSIGNED` | NULL | FK | Pemberi tugas |
| `assigned_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
PRIMARY KEY(learning_class_id, user_id)
```

## 5.6 `learning_modules`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier modul |
| `course_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Course |
| `title` | `VARCHAR(255)` | NN |  | Judul modul |
| `description` | `TEXT` | NULL |  | Deskripsi |
| `sequence_order` | `INT UNSIGNED` | NN |  | Urutan modul |
| `is_required` | `TINYINT(1)` | NN |  | Wajib diselesaikan atau tidak |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(course_id, sequence_order)
```

## 5.7 `lessons`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier lesson |
| `learning_module_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Modul induk |
| `title` | `VARCHAR(255)` | NN |  | Judul lesson |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug lesson |
| `content_type` | `VARCHAR(20)` | NN |  | `text`, `document`, `video`, `link` |
| `content_body` | `LONGTEXT` | NULL |  | Konten teks/HTML terkontrol |
| `external_url` | `VARCHAR(2048)` | NULL |  | URL video/tautan bila relevan |
| `sequence_order` | `INT UNSIGNED` | NN |  | Urutan lesson dalam modul |
| `is_required` | `TINYINT(1)` | NN |  | Lesson wajib |
| `estimated_duration_minutes` | `INT UNSIGNED` | NULL |  | Estimasi durasi |
| `is_published` | `TINYINT(1)` | NN | IDX | Ketersediaan lesson |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(learning_module_id, sequence_order)
```

## 5.8 `lesson_materials`

Lampiran materi lesson.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `lesson_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Lesson |
| `file_asset_id` | `BIGINT UNSIGNED` | NN | FK | File lampiran |
| `title` | `VARCHAR(255)` | NULL |  | Judul tampil |
| `sequence_order` | `INT UNSIGNED` | NN |  | Urutan lampiran |
| `is_required` | `TINYINT(1)` | NN |  | Lampiran wajib dibaca/download bila diterapkan |
| `created_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(lesson_id, sequence_order)
```

## 5.9 `enrollments`

Hubungan peserta dengan class.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier enrollment |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Peserta |
| `learning_class_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Class |
| `enrollment_status` | `VARCHAR(30)` | NN | IDX | `pending`, `approved`, `rejected`, `cancelled`, `completed` |
| `enrolled_at` | `DATETIME` | NN |  | Waktu registrasi/enrollment |
| `approved_by` | `BIGINT UNSIGNED` | NULL | FK | Actor approval |
| `approved_at` | `DATETIME` | NULL |  | Waktu approval |
| `completed_at` | `DATETIME` | NULL |  | Waktu completion |
| `final_status` | `VARCHAR(30)` | NN | IDX | `in_progress`, `passed`, `failed`, `withdrawn` |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(user_id, learning_class_id)
```

## 5.10 `learning_progress`

Progres lesson per enrollment.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `enrollment_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Enrollment |
| `lesson_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Lesson |
| `started_at` | `DATETIME` | NULL |  | Waktu mulai |
| `completed_at` | `DATETIME` | NULL | IDX | Waktu selesai |
| `progress_percent` | `DECIMAL(5,2)` | NN |  | 0–100 |
| `last_accessed_at` | `DATETIME` | NULL |  | Akses terakhir |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(enrollment_id, lesson_id)
```

## 5.11 `assessment_configurations`

Sumber kebenaran konfigurasi assessment per class.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier konfigurasi |
| `learning_class_id` | `BIGINT UNSIGNED` | NN | FK, UQ | Satu konfigurasi per class |
| `pre_test_enabled` | `TINYINT(1)` | NN |  | Mengaktifkan pre-test |
| `pre_test_attempt_limit` | `INT UNSIGNED` | NN |  | Default dan wajib `1` |
| `quiz_enabled` | `TINYINT(1)` | NN |  | Mengaktifkan quiz lesson |
| `quiz_attempt_limit` | `INT UNSIGNED` | NULL |  | Batas attempt quiz; `NULL` bila tidak dibatasi kebijakan |
| `quiz_best_attempt_enabled` | `TINYINT(1)` | NN |  | Harus `1` pada baseline |
| `assignment_enabled` | `TINYINT(1)` | NN |  | Assignment tersedia pada class |
| `assignment_required_when_enabled` | `TINYINT(1)` | NN |  | Harus `1` pada baseline |
| `post_test_enabled` | `TINYINT(1)` | NN |  | Mengaktifkan post-test |
| `post_test_attempt_limit` | `INT UNSIGNED` | NULL |  | Batas attempt post-test |
| `post_test_best_attempt_enabled` | `TINYINT(1)` | NN |  | Harus `1` pada baseline |
| `quiz_weight` | `DECIMAL(5,2)` | NN |  | Bobot quiz; 0–40 saat post-test aktif |
| `assignment_weight` | `DECIMAL(5,2)` | NN |  | Bobot assignment; 0–40 |
| `post_test_weight` | `DECIMAL(5,2)` | NN |  | Baseline `60.00` bila post-test aktif |
| `passing_grade` | `DECIMAL(5,2)` | NN |  | Minimal `70.00` |
| `participant_feedback_required` | `TINYINT(1)` | NN |  | Feedback menjadi syarat eligibility bila `1` |
| `certificate_enabled` | `TINYINT(1)` | NN |  | Class dapat menerbitkan sertifikat bila `1` |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat konfigurasi |
| `updated_by` | `BIGINT UNSIGNED` | NULL | FK | Pengubah terakhir |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint baseline:

```text
UNIQUE(learning_class_id)
pre_test_attempt_limit = 1
quiz_best_attempt_enabled = 1
assignment_required_when_enabled = 1
post_test_best_attempt_enabled = 1
passing_grade >= 70 AND passing_grade <= 100
quiz_weight >= 0 AND assignment_weight >= 0 AND post_test_weight >= 0
post_test_weight = 60 bila post_test_enabled = 1
quiz_weight + assignment_weight = 40 bila post_test_enabled = 1
assignment_weight = 0 bila assignment_enabled = 0
quiz_weight = 40 bila assignment_enabled = 0 dan post_test_enabled = 1
```

## 5.12 `pre_test_attempts`

Pre-test satu kali per enrollment; tidak masuk nilai akhir.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier attempt |
| `enrollment_id` | `BIGINT UNSIGNED` | NN | FK, UQ | Satu pre-test per enrollment |
| `quiz_id` | `BIGINT UNSIGNED` | NULL | FK | Definisi soal/objective test yang dipakai |
| `started_at` | `DATETIME` | NULL |  | Mulai attempt |
| `submitted_at` | `DATETIME` | NULL |  | Submit attempt |
| `score` | `DECIMAL(5,2)` | NULL |  | Nilai 0–100 |
| `is_completed` | `TINYINT(1)` | NN | IDX | Selesai atau belum |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(enrollment_id)
```

## 5.13 `quizzes`

Definisi quiz objektif. Digunakan untuk quiz lesson dan dapat menjadi engine soal pre-test/post-test melalui `assessment_type`.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier quiz |
| `lesson_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Lesson pemilik untuk `lesson_quiz` |
| `learning_class_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Class pemilik untuk `pre_test`/`post_test` |
| `title` | `VARCHAR(255)` | NN |  | Judul quiz/test |
| `assessment_type` | `VARCHAR(30)` | NN | IDX | `lesson_quiz`, `pre_test`, `post_test` |
| `attempt_limit` | `INT UNSIGNED` | NULL |  | Batas attempt pada quiz ini |
| `passing_score` | `DECIMAL(5,2)` | NULL |  | Nilai lulus komponen bila digunakan |
| `is_required` | `TINYINT(1)` | NN |  | Wajib atau opsional |
| `is_published` | `TINYINT(1)` | NN | IDX | Tersedia bagi peserta |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Aturan:

- `lesson_quiz` wajib memiliki `lesson_id` dan mengikuti course/class peserta.
- `pre_test`/`post_test` wajib memiliki `learning_class_id` dan `lesson_id = NULL`.
- Baseline membatasi satu pre-test aktif dan satu post-test aktif per class; validasi dilakukan service/unique strategy implementasi.

## 5.14 `questions`

Pertanyaan objektif milik quiz.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier pertanyaan |
| `quiz_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Quiz pemilik |
| `question_type` | `VARCHAR(30)` | NN |  | `multiple_choice`, `multiple_response`, `true_false` |
| `question_text` | `LONGTEXT` | NN |  | Isi pertanyaan |
| `explanation` | `TEXT` | NULL |  | Penjelasan setelah penilaian bila diizinkan |
| `score_weight` | `DECIMAL(8,2)` | NN |  | Bobot relatif pertanyaan |
| `sequence_order` | `INT UNSIGNED` | NN |  | Urutan |
| `is_active` | `TINYINT(1)` | NN |  | Aktif/nonaktif |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(quiz_id, sequence_order)
```

## 5.15 `question_options`

Pilihan jawaban objektif.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier opsi |
| `question_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Pertanyaan |
| `option_text` | `TEXT` | NN |  | Isi opsi |
| `is_correct` | `TINYINT(1)` | NN |  | Kunci jawaban |
| `sequence_order` | `INT UNSIGNED` | NN |  | Urutan opsi |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(question_id, sequence_order)
```

## 5.16 `quiz_attempts`

Attempt untuk quiz lesson.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier attempt |
| `quiz_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Quiz |
| `enrollment_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Enrollment peserta |
| `attempt_number` | `INT UNSIGNED` | NN |  | Nomor attempt mulai 1 |
| `started_at` | `DATETIME` | NULL |  | Waktu mulai |
| `submitted_at` | `DATETIME` | NULL |  | Waktu submit |
| `score` | `DECIMAL(5,2)` | NULL | IDX | Nilai auto-grading 0–100 |
| `is_passed` | `TINYINT(1)` | NULL |  | Hasil terhadap passing score quiz bila diterapkan |
| `status` | `VARCHAR(30)` | NN | IDX | `in_progress`, `submitted`, `graded`, `expired` |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(quiz_id, enrollment_id, attempt_number)
```

## 5.17 `quiz_answers`

Jawaban peserta per question pada quiz attempt.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier jawaban |
| `quiz_attempt_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Attempt |
| `question_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Question |
| `answer_text` | `TEXT` | NULL |  | Untuk jawaban text jika engine berkembang |
| `is_correct` | `TINYINT(1)` | NULL |  | Hasil auto-grading per question |
| `score_awarded` | `DECIMAL(8,2)` | NULL |  | Nilai diberikan |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(quiz_attempt_id, question_id)
```

## 5.18 `quiz_answer_options`

Pilihan opsi yang dipilih peserta; mendukung multiple response.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `quiz_answer_id` | `BIGINT UNSIGNED` | NN | PK/FK | Jawaban peserta |
| `question_option_id` | `BIGINT UNSIGNED` | NN | PK/FK | Opsi dipilih |
| `created_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
PRIMARY KEY(quiz_answer_id, question_option_id)
```

## 5.19 `assignment_submissions` and related tables

### `assignments`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier assignment |
| `learning_class_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Class pemilik; assignment bersifat class-specific |
| `lesson_id` | `BIGINT UNSIGNED` | NULL | FK | Lesson terkait bila ada |
| `title` | `VARCHAR(255)` | NN |  | Judul |
| `instruction` | `LONGTEXT` | NN |  | Instruksi |
| `due_at` | `DATETIME` | NULL | IDX | Deadline |
| `max_score` | `DECIMAL(5,2)` | NN |  | Maksimum 0–100; baseline gunakan 100 |
| `is_required` | `TINYINT(1)` | NN |  | Harus `1` bila assignment diaktifkan pada class |
| `is_active` | `TINYINT(1)` | NN | IDX | Tersedia untuk peserta |
| `allow_resubmission` | `TINYINT(1)` | NN |  | Mengizinkan revisi/upload ulang |
| `max_submission_count` | `INT UNSIGNED` | NULL |  | Batas submission; `NULL` mengikuti kebijakan class |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

### `assignment_submissions`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier submission |
| `assignment_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Assignment |
| `enrollment_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Enrollment peserta |
| `submission_number` | `INT UNSIGNED` | NN |  | Versi submit/revisi, mulai 1 |
| `submission_text` | `LONGTEXT` | NULL |  | Jawaban teks |
| `status` | `VARCHAR(30)` | NN | IDX | `draft`, `submitted`, `under_review`, `revision_required`, `resubmitted`, `graded`, `late`, `cancelled` |
| `submitted_at` | `DATETIME` | NULL |  | Waktu submit |
| `revision_requested_at` | `DATETIME` | NULL |  | Waktu revisi diminta |
| `resubmission_due_at` | `DATETIME` | NULL |  | Tenggat revisi |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(assignment_id, enrollment_id, submission_number)
```

### `assignment_submission_files`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `assignment_submission_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Submission |
| `file_asset_id` | `BIGINT UNSIGNED` | NN | FK | File terunggah |
| `created_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(assignment_submission_id, file_asset_id)
```

### `assignment_grades`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier nilai |
| `assignment_submission_id` | `BIGINT UNSIGNED` | NN | FK, UQ | Submission yang dinilai |
| `grader_user_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Evaluator/actor berizin |
| `score` | `DECIMAL(5,2)` | NN |  | Nilai 0–100 |
| `feedback` | `LONGTEXT` | NULL |  | Umpan balik evaluator |
| `graded_at` | `DATETIME` | NN |  | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(assignment_submission_id)
```

## 5.20 `post_test_attempts`

Snapshot attempt post-test untuk class. Detail jawaban memakai engine quiz/answer yang direferensikan `quiz_id`.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier attempt |
| `enrollment_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Enrollment |
| `quiz_id` | `BIGINT UNSIGNED` | NN | FK | Quiz bertipe `post_test` |
| `attempt_number` | `INT UNSIGNED` | NN |  | Nomor attempt |
| `started_at` | `DATETIME` | NULL |  | Waktu mulai |
| `submitted_at` | `DATETIME` | NULL |  | Waktu submit |
| `score` | `DECIMAL(5,2)` | NULL | IDX | Auto-grading 0–100 |
| `is_completed` | `TINYINT(1)` | NN | IDX | Selesai atau belum |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(enrollment_id, attempt_number)
```

## 5.21 `participant_feedbacks`

Feedback peserta tentang class; syarat sertifikat bila konfigurasi mewajibkan.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `enrollment_id` | `BIGINT UNSIGNED` | NN | FK, UQ | Satu feedback per enrollment |
| `rating` | `TINYINT UNSIGNED` | NULL |  | Skala yang diterapkan, misalnya 1–5 |
| `feedback_text` | `LONGTEXT` | NULL |  | Ulasan peserta |
| `submitted_at` | `DATETIME` | NN |  | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 5.22 `final_grades`

Snapshot hasil akhir dan eligibility sertifikat per enrollment.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `enrollment_id` | `BIGINT UNSIGNED` | NN | FK, UQ | Satu final grade aktif per enrollment |
| `quiz_score` | `DECIMAL(5,2)` | NULL |  | Agregasi best attempt quiz |
| `assignment_score` | `DECIMAL(5,2)` | NULL |  | Agregasi nilai assignment final |
| `post_test_score` | `DECIMAL(5,2)` | NULL |  | Best attempt post-test |
| `final_score` | `DECIMAL(5,2)` | NULL | IDX | Nilai akhir 0–100 |
| `passing_grade` | `DECIMAL(5,2)` | NN |  | Snapshot threshold class, minimal 70 |
| `is_passed` | `TINYINT(1)` | NN | IDX | Lulus/gagal |
| `all_required_lessons_completed` | `TINYINT(1)` | NN |  | Completion lesson wajib |
| `all_required_assessments_completed` | `TINYINT(1)` | NN |  | Completion assessment wajib |
| `participant_feedback_completed` | `TINYINT(1)` | NN |  | Feedback terpenuhi/dianggap tidak wajib |
| `certificate_eligible` | `TINYINT(1)` | NN | IDX | Eligibility sertifikat |
| `calculated_at` | `DATETIME` | NN |  | UTC |
| `calculated_by` | `BIGINT UNSIGNED` | NULL | FK | Actor/sistem pemicu kalkulasi |
| `notes` | `TEXT` | NULL |  | Catatan administratif |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Formula:

```text
final_score = (quiz_score × quiz_weight / 100)
            + (assignment_score × assignment_weight / 100)
            + (post_test_score × post_test_weight / 100)
```

`pre_test_score` sengaja tidak ada karena pre-test tidak dihitung pada nilai akhir.

---

# 6. Bunga Rampai

## 6.1 `knowledge_categories`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `parent_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Kategori induk |
| `name` | `VARCHAR(255)` | NN |  | Nama kategori |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug |
| `description` | `TEXT` | NULL |  | Deskripsi |
| `sort_order` | `INT UNSIGNED` | NN |  | Urutan |
| `is_active` | `TINYINT(1)` | NN | IDX | Status aktif |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 6.2 `tags`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `name` | `VARCHAR(100)` | NN | UQ | Nama tag |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug tag |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 6.3 `articles`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `knowledge_category_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Kategori |
| `title` | `VARCHAR(255)` | NN | IDX | Judul |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug publik |
| `excerpt` | `TEXT` | NULL |  | Ringkasan |
| `content` | `LONGTEXT` | NN |  | Konten |
| `cover_file_asset_id` | `BIGINT UNSIGNED` | NULL | FK | Cover |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Status publikasi |
| `author_id` | `BIGINT UNSIGNED` | NULL | FK | Penulis |
| `published_at` | `DATETIME` | NULL | IDX | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete |

## 6.4 `article_tags`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `article_id` | `BIGINT UNSIGNED` | NN | PK/FK | Artikel |
| `tag_id` | `BIGINT UNSIGNED` | NN | PK/FK | Tag |

Constraint:

```text
PRIMARY KEY(article_id, tag_id)
```

## 6.5 `faqs`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `knowledge_category_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Kategori |
| `question` | `TEXT` | NN |  | Pertanyaan |
| `answer` | `LONGTEXT` | NN |  | Jawaban |
| `sort_order` | `INT UNSIGNED` | NN |  | Urutan |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Status publikasi |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat |
| `published_at` | `DATETIME` | NULL |  | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 6.6 `case_studies`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `knowledge_category_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Kategori |
| `title` | `VARCHAR(255)` | NN | IDX | Judul |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug |
| `summary` | `TEXT` | NULL |  | Ringkasan |
| `content` | `LONGTEXT` | NN |  | Isi studi kasus |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Status publikasi |
| `author_id` | `BIGINT UNSIGNED` | NULL | FK | Penulis |
| `published_at` | `DATETIME` | NULL |  | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete |

## 6.7 `document_templates`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `knowledge_category_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Kategori |
| `title` | `VARCHAR(255)` | NN | IDX | Nama template |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug |
| `description` | `LONGTEXT` | NULL |  | Deskripsi/petunjuk |
| `file_asset_id` | `BIGINT UNSIGNED` | NULL | FK | File; dapat `path = NULL` saat belum tersedia |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Status publikasi |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat |
| `published_at` | `DATETIME` | NULL |  | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete |

## 6.8 `bookmarks`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Pemilik bookmark |
| `bookmarkable_type` | `VARCHAR(50)` | NN | IDX | `article`, `case_study`, `document_template`, `regulation` |
| `bookmarkable_id` | `BIGINT UNSIGNED` | NN | IDX | ID target |
| `created_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(user_id, bookmarkable_type, bookmarkable_id)
```

---

# 7. Regulasi PBJ

## 7.1 `regulation_categories`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `parent_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Kategori induk |
| `name` | `VARCHAR(255)` | NN |  | Nama |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug |
| `description` | `TEXT` | NULL |  | Deskripsi |
| `sort_order` | `INT UNSIGNED` | NN |  | Urutan |
| `is_active` | `TINYINT(1)` | NN | IDX | Aktif |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 7.2 `procurement_stages`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `code` | `VARCHAR(50)` | NN | UQ | Kode tahap PBJ |
| `name` | `VARCHAR(255)` | NN |  | Nama tahap |
| `description` | `TEXT` | NULL |  | Deskripsi |
| `sort_order` | `INT UNSIGNED` | NN |  | Urutan |
| `is_active` | `TINYINT(1)` | NN |  | Aktif |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 7.3 `competencies`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `code` | `VARCHAR(50)` | NN | UQ | Kode kompetensi |
| `name` | `VARCHAR(255)` | NN |  | Nama kompetensi |
| `description` | `TEXT` | NULL |  | Deskripsi |
| `is_active` | `TINYINT(1)` | NN |  | Aktif |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 7.4 `regulations`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `regulation_category_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Kategori |
| `title` | `VARCHAR(500)` | NN | IDX | Judul regulasi |
| `regulation_number` | `VARCHAR(100)` | NN |  | Nomor regulasi |
| `year` | `SMALLINT UNSIGNED` | NN | IDX | Tahun regulasi |
| `official_date` | `DATE` | NULL |  | Tanggal penetapan |
| `effective_date` | `DATE` | NULL |  | Tanggal berlaku |
| `legal_status` | `VARCHAR(30)` | NN | IDX | `active`, `revoked`, `amended`, `expired` |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Status publikasi |
| `summary` | `LONGTEXT` | NULL |  | Ringkasan |
| `source_url` | `VARCHAR(2048)` | NULL |  | URL sumber resmi |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat |
| `published_at` | `DATETIME` | NULL | IDX | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete terbatas |

Constraint:

```text
UNIQUE(regulation_category_id, regulation_number, year)
```

## 7.5 `regulation_versions`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `regulation_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Regulasi induk |
| `version_number` | `INT UNSIGNED` | NN |  | Nomor versi mulai 1 |
| `file_asset_id` | `BIGINT UNSIGNED` | NULL | FK | Dokumen versi; placeholder diperbolehkan |
| `change_notes` | `TEXT` | NULL |  | Changelog |
| `is_current` | `TINYINT(1)` | NN | IDX | Menandai versi aktif saat ini |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Status versi |
| `uploaded_by` | `BIGINT UNSIGNED` | NULL | FK | Pengunggah |
| `published_at` | `DATETIME` | NULL |  | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(regulation_id, version_number)
```

Hanya satu `is_current = 1` per regulation; dijaga service/transaction.

## 7.6 `regulation_procurement_stages`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `regulation_id` | `BIGINT UNSIGNED` | NN | PK/FK | Regulasi |
| `procurement_stage_id` | `BIGINT UNSIGNED` | NN | PK/FK | Tahap PBJ |

Constraint:

```text
PRIMARY KEY(regulation_id, procurement_stage_id)
```

## 7.7 `regulation_competencies`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `regulation_id` | `BIGINT UNSIGNED` | NN | PK/FK | Regulasi |
| `competency_id` | `BIGINT UNSIGNED` | NN | PK/FK | Kompetensi |

Constraint:

```text
PRIMARY KEY(regulation_id, competency_id)
```

## 7.8 `regulation_keywords`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `regulation_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Regulasi |
| `keyword` | `VARCHAR(191)` | NN | IDX | Kata kunci normalisasi |
| `created_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(regulation_id, keyword)
```

## 7.9 `regulation_download_logs`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `regulation_version_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Versi yang diunduh |
| `user_id` | `BIGINT UNSIGNED` | NULL | FK | User bila login |
| `ip_address` | `VARCHAR(45)` | NULL |  | IPv4/IPv6 |
| `downloaded_at` | `DATETIME` | NN | IDX | UTC |

---

# 8. Berita, Agenda, dan Event

## 8.1 `news_categories`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `name` | `VARCHAR(255)` | NN |  | Nama kategori |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug |
| `description` | `TEXT` | NULL |  | Deskripsi |
| `sort_order` | `INT UNSIGNED` | NN |  | Urutan |
| `is_active` | `TINYINT(1)` | NN |  | Aktif |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 8.2 `news`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `news_category_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Kategori berita |
| `title` | `VARCHAR(255)` | NN | IDX | Judul |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug |
| `excerpt` | `TEXT` | NULL |  | Ringkasan |
| `content` | `LONGTEXT` | NN |  | Isi berita |
| `cover_file_asset_id` | `BIGINT UNSIGNED` | NULL | FK | Cover |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Status publikasi |
| `author_id` | `BIGINT UNSIGNED` | NULL | FK | Penulis |
| `published_at` | `DATETIME` | NULL | IDX | UTC |
| `view_count` | `BIGINT UNSIGNED` | NN |  | Counter tampilan terkontrol |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete |

## 8.3 `events`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `title` | `VARCHAR(255)` | NN | IDX | Judul event |
| `slug` | `VARCHAR(191)` | NN | UQ | Slug |
| `event_type` | `VARCHAR(30)` | NN | IDX | `webinar`, `socialization`, `workshop`, `training`, `other` |
| `delivery_mode` | `VARCHAR(20)` | NN |  | `online`, `offline`, `hybrid` |
| `description` | `LONGTEXT` | NULL |  | Deskripsi |
| `start_at` | `DATETIME` | NN | IDX | Mulai UTC |
| `end_at` | `DATETIME` | NN | IDX | Selesai UTC |
| `registration_open_at` | `DATETIME` | NULL |  | Buka registrasi |
| `registration_close_at` | `DATETIME` | NULL |  | Tutup registrasi |
| `quota` | `INT UNSIGNED` | NULL |  | Kuota |
| `location_text` | `VARCHAR(500)` | NULL |  | Lokasi offline |
| `meeting_url` | `VARCHAR(2048)` | NULL |  | URL online; akses dibatasi sesuai kebutuhan |
| `workflow_status` | `VARCHAR(50)` | NN | IDX | Status approval/publish |
| `event_status` | `VARCHAR(30)` | NN | IDX | `draft`, `coming_soon`, `open_registration`, `full`, `finished`, `cancelled` |
| `certificate_enabled` | `TINYINT(1)` | NN |  | Event dapat menerbitkan sertifikat |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat |
| `published_at` | `DATETIME` | NULL |  | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete |

Aturan: `end_at >= start_at`; jendela registrasi harus valid bila diisi.

## 8.4 `event_registrations`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `event_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Event |
| `user_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Pendaftar |
| `registration_status` | `VARCHAR(30)` | NN | IDX | `registered`, `waitlisted`, `cancelled`, `rejected` |
| `registered_at` | `DATETIME` | NN |  | UTC |
| `cancelled_at` | `DATETIME` | NULL |  | UTC |
| `approved_by` | `BIGINT UNSIGNED` | NULL | FK | Approver bila relevan |
| `notes` | `TEXT` | NULL |  | Catatan |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(event_id, user_id)
```

## 8.5 `event_attendances`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `event_registration_id` | `BIGINT UNSIGNED` | NN | FK, UQ | Registrasi |
| `attendance_status` | `VARCHAR(20)` | NN | IDX | `present`, `absent`, `excused` |
| `checked_in_at` | `DATETIME` | NULL |  | Check-in UTC |
| `checked_out_at` | `DATETIME` | NULL |  | Check-out UTC |
| `recorded_by` | `BIGINT UNSIGNED` | NULL | FK | Operator |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(event_registration_id)
```

---

# 9. Sertifikat Digital

## 9.1 `certificate_templates`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `code` | `VARCHAR(100)` | NN | UQ | Kode template |
| `name` | `VARCHAR(255)` | NN |  | Nama template |
| `certificate_type` | `VARCHAR(20)` | NN | IDX | `learning` atau `event` |
| `html_template` | `LONGTEXT` | NN |  | Template HTML untuk PDF; harus disanitasi/terkontrol |
| `layout_file_asset_id` | `BIGINT UNSIGNED` | NULL | FK | Asset layout/background |
| `is_active` | `TINYINT(1)` | NN | IDX | Template aktif |
| `created_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 9.2 `certificate_sequences`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `sequence_scope` | `VARCHAR(100)` | NN |  | Scope format nomor, misalnya `learning`/`event` |
| `year` | `SMALLINT UNSIGNED` | NN |  | Tahun sequence |
| `last_number` | `BIGINT UNSIGNED` | NN |  | Nomor terakhir |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(sequence_scope, year)
```

## 9.3 `certificates`

Sertifikat learning dan event memakai tabel sama melalui sumber polymorphic yang dibatasi.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `recipient_user_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Penerima |
| `certificate_template_id` | `BIGINT UNSIGNED` | NN | FK | Template |
| `source_type` | `VARCHAR(50)` | NN |  | `learning_enrollment` atau `event_registration` |
| `source_id` | `BIGINT UNSIGNED` | NN |  | ID enrollment/registrasi event |
| `certificate_number` | `VARCHAR(191)` | NN | UQ | Nomor sertifikat resmi |
| `issued_at` | `DATETIME` | NULL | IDX | Waktu terbit |
| `status` | `VARCHAR(20)` | NN | IDX | `pending`, `issued`, `revoked`, `expired` |
| `file_asset_id` | `BIGINT UNSIGNED` | NULL | FK | PDF; dapat placeholder sebelum generate |
| `verification_token` | `VARCHAR(191)` | NN | UQ | Token publik acak |
| `issued_by` | `BIGINT UNSIGNED` | NULL | FK | Penerbit/sistem actor |
| `expires_at` | `DATETIME` | NULL | IDX | Masa berlaku bila ada |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(certificate_number)
UNIQUE(verification_token)
UNIQUE(source_type, source_id)
```

## 9.4 `certificate_verification_logs`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `certificate_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Sertifikat bila ditemukan |
| `verification_token` | `VARCHAR(191)` | NN | IDX | Token yang diperiksa |
| `result` | `VARCHAR(20)` | NN | IDX | `valid`, `revoked`, `expired`, `not_found` |
| `ip_address` | `VARCHAR(45)` | NULL |  | IPv4/IPv6 |
| `user_agent` | `VARCHAR(500)` | NULL |  | User agent terbatas |
| `verified_at` | `DATETIME` | NN | IDX | UTC |

## 9.5 `certificate_revocations`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `certificate_id` | `BIGINT UNSIGNED` | NN | FK, UQ | Sertifikat dibatalkan |
| `revoked_by` | `BIGINT UNSIGNED` | NULL | FK | Actor pembatalan |
| `revoked_at` | `DATETIME` | NN |  | UTC |
| `reason` | `LONGTEXT` | NN |  | Alasan pembatalan |
| `replacement_certificate_id` | `BIGINT UNSIGNED` | NULL | FK | Sertifikat pengganti jika ada |
| `created_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(certificate_id)
```

---

# 10. Government Intelligence PBJ

## 10.1 `jf_ppbj_levels`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `code` | `VARCHAR(50)` | NN | UQ | `PERTAMA`, `MUDA`, `MADYA`, `UTAMA` |
| `name` | `VARCHAR(100)` | NN |  | Nama jenjang |
| `sort_order` | `INT UNSIGNED` | NN |  | Urutan jenjang |
| `is_active` | `TINYINT(1)` | NN |  | Aktif |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 10.2 `jf_ppbj_personnel`

Data SDM JF PPBJ terbatas; tidak dipublikasikan individual.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `user_id` | `BIGINT UNSIGNED` | NULL | FK, UQ | User terkait bila memiliki akun |
| `organization_unit_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Unit organisasi |
| `employee_name` | `VARCHAR(255)` | NN | IDX | Nama personel; data terbatas |
| `employee_identifier` | `VARCHAR(100)` | NULL | UQ | Identifier/NIP; data terbatas |
| `jf_ppbj_level_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | Jenjang |
| `employment_status` | `VARCHAR(30)` | NN | IDX | `active`, `inactive`, `retired`, `transferred` |
| `effective_start_date` | `DATE` | NULL |  | Mulai efektif |
| `effective_end_date` | `DATE` | NULL |  | Akhir efektif |
| `notes` | `TEXT` | NULL |  | Catatan terbatas |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete terbatas |

## 10.3 `jf_ppbj_histories`

Riwayat perubahan personel JF PPBJ.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `jf_ppbj_personnel_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Personel |
| `event_type` | `VARCHAR(30)` | NN | IDX | `added`, `transferred_in`, `transferred_out`, `promoted`, `retired`, `deactivated`, `updated` |
| `effective_date` | `DATE` | NN | IDX | Tanggal efektif |
| `from_organization_unit_id` | `BIGINT UNSIGNED` | NULL | FK | Unit asal |
| `to_organization_unit_id` | `BIGINT UNSIGNED` | NULL | FK | Unit tujuan |
| `from_level_id` | `BIGINT UNSIGNED` | NULL | FK | Jenjang asal |
| `to_level_id` | `BIGINT UNSIGNED` | NULL | FK | Jenjang tujuan |
| `notes` | `TEXT` | NULL |  | Catatan |
| `recorded_by` | `BIGINT UNSIGNED` | NULL | FK | Pencatat |
| `created_at` | `DATETIME` | NN |  | UTC |

## 10.4 `competency_certificate_types`

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `code` | `VARCHAR(50)` | NN | UQ | `PBJ_LEVEL_1`, `PPK_TYPE_A`, `PPK_TYPE_B`, `PPK_TYPE_C` |
| `name` | `VARCHAR(255)` | NN |  | Nama jenis sertifikat |
| `description` | `TEXT` | NULL |  | Deskripsi |
| `is_active` | `TINYINT(1)` | NN |  | Aktif |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

## 10.5 `competency_certificates`

Sertifikat kompetensi SDM; berbeda dari sertifikat digital BegawiPBJ.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `user_id` | `BIGINT UNSIGNED` | NULL | FK, IDX | User terkait bila ada |
| `organization_unit_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Organisasi pemilik/penempatan |
| `holder_name` | `VARCHAR(255)` | NN |  | Nama pemegang; terbatas |
| `holder_identifier` | `VARCHAR(100)` | NULL | IDX | NIP/identifier; terbatas |
| `competency_certificate_type_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Jenis sertifikat |
| `certificate_number` | `VARCHAR(191)` | NN |  | Nomor sertifikat kompetensi |
| `issued_at` | `DATE` | NULL |  | Tanggal terbit |
| `expires_at` | `DATE` | NULL | IDX | Tanggal berakhir |
| `status` | `VARCHAR(20)` | NN | IDX | `active`, `expired`, `revoked`, `unknown` |
| `evidence_file_asset_id` | `BIGINT UNSIGNED` | NULL | FK | Bukti dokumen privat |
| `verified_by` | `BIGINT UNSIGNED` | NULL | FK | Verifikator |
| `verified_at` | `DATETIME` | NULL |  | UTC |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |
| `deleted_at` | `DATETIME` | NULL |  | Soft delete terbatas |

Constraint:

```text
UNIQUE(competency_certificate_type_id, certificate_number)
```

## 10.6 `competency_gap_snapshots`

Snapshot agregat kebutuhan dan ketersediaan kompetensi.

| Kolom | Tipe | Null | Key | Deskripsi |
|---|---|---:|---|---|
| `id` | `BIGINT UNSIGNED` | NN | PK | Identifier |
| `organization_unit_id` | `BIGINT UNSIGNED` | NN | FK, IDX | Unit organisasi |
| `period_date` | `DATE` | NN | IDX | Tanggal/periode snapshot |
| `metric_code` | `VARCHAR(100)` | NN |  | Metrik kompetensi |
| `required_count` | `INT` | NN |  | Kebutuhan |
| `available_count` | `INT` | NN |  | Ketersediaan |
| `gap_count` | `INT` | NN |  | `required_count - available_count` |
| `generated_at` | `DATETIME` | NN |  | UTC |
| `generated_by` | `BIGINT UNSIGNED` | NULL | FK | Pembuat snapshot/sistem actor |
| `created_at` | `DATETIME` | NN |  | UTC |
| `updated_at` | `DATETIME` | NN |  | UTC |

Constraint:

```text
UNIQUE(organization_unit_id, period_date, metric_code)
```

---

# 11. Foreign Key dan Delete Policy

## 11.1 Prinsip

| Entitas | Kebijakan utama |
|---|---|
| User | Soft delete/anonymization terkontrol; hard delete dihindari |
| Role/permission | `RESTRICT` |
| Course/class | Archive/cancel setelah ada enrollment |
| Enrollment/final grade | `RESTRICT` |
| Sertifikat | Revoke, bukan delete |
| Regulasi published | Ubah status hukum/archive, bukan hard delete |
| Approval history/audit logs | Tidak cascade delete |
| File asset | Hapus fisik melalui service setelah referensi diperiksa |
| Wilayah | Read-only; tidak dihapus aplikasi |

## 11.2 Relasi polymorphic

| Tabel | Kolom polymorphic | Tipe yang diizinkan | Penjaga integritas |
|---|---|---|---|
| `approvals` | `subject_type`, `subject_id` | Domain approval yang terdaftar | `ApprovalService` |
| `bookmarks` | `bookmarkable_type`, `bookmarkable_id` | `article`, `case_study`, `document_template`, `regulation` | `BookmarkService` |
| `certificates` | `source_type`, `source_id` | `learning_enrollment`, `event_registration` | `CertificateEligibilityService` |
| `audit_logs` | `subject_type`, `subject_id` | Subject audit yang terdaftar | `AuditService` |

---

# 12. Aturan Integritas Kritis

## 12.1 Assessment dan kelulusan

- Pre-test maksimal satu attempt per enrollment dan nilainya tidak masuk `final_score`.
- Quiz memakai best attempt sesuai konfigurasi class.
- Assignment hanya wajib bila `assessment_configurations.assignment_enabled = 1`; ketika aktif, `is_required` assignment wajib bernilai `1` untuk komponen wajib.
- Submission revisi memakai `submission_number` baru, bukan menimpa bukti submission sebelumnya.
- Post-test memakai best attempt dan bobot 60%.
- Passing grade class minimal 70.
- `final_grades.certificate_eligible` hanya benar bila seluruh completion, assessment, nilai, feedback, dan aturan administratif terpenuhi.

## 12.2 Sertifikat

- Satu source menghasilkan satu sertifikat resmi: `UNIQUE(source_type, source_id)`.
- Nomor sertifikat dan verification token selalu unik.
- Sertifikat revoked tetap disimpan dan diverifikasi sebagai revoked, bukan dihapus.
- QR/token tidak memuat NIP, NIK, atau data personal penerima.

## 12.3 File

- `file_assets.path = NULL` berarti file belum tersedia.
- Metadata file tidak otomatis membuktikan keberadaan file fisik.
- Download/preview hanya aktif setelah path tersedia dan authorization lolos.
- Nama asli tidak digunakan sebagai path fisik.

## 12.4 Privasi publik

Query/statistik publik dilarang mengembalikan:

```text
NIP
NIK
email
nomor telepon
holder_identifier
employee_identifier
nama individual ASN
dokumen bukti
file privat
```

---

# 13. Indeks Rekomendasi

```text
users(account_status, created_at)
enrollments(learning_class_id, enrollment_status)
enrollments(user_id, final_status)
learning_progress(enrollment_id, completed_at)
quiz_attempts(enrollment_id, quiz_id, score)
assignment_submissions(enrollment_id, assignment_id, status)
post_test_attempts(enrollment_id, score)
final_grades(enrollment_id, certificate_eligible)
articles(workflow_status, published_at)
regulations(legal_status, year)
regulations(workflow_status, published_at)
events(event_status, start_at)
event_registrations(event_id, registration_status)
certificates(recipient_user_id, status)
certificates(verification_token)
approvals(subject_type, subject_id, workflow_status)
audit_logs(subject_type, subject_id, created_at)
notifications(user_id, read_at, created_at)
competency_gap_snapshots(organization_unit_id, period_date, metric_code)
```

---

# 14. Ringkasan Tabel

| Domain | Tabel utama |
|---|---|
| Master | `wilayah`, `organization_units`, `user_organizations` |
| Identity/RBAC | `users`, profile category tables, `roles`, `permissions`, pivots, reset/login logs |
| Shared | `file_assets`, `approvals`, `approval_histories`, `audit_logs`, `notifications` |
| Learning | Program/course/class, content, enrollment/progress, assessment, feedback, final grade |
| Knowledge | Categories, tags, article/FAQ/case/template, bookmarks |
| Regulation | Categories, regulations, versions, stage/competency pivots, keywords, downloads |
| Communication | News categories/news, events, registrations, attendances |
| Certificate | Templates, sequences, certificates, verification logs, revocations |
| Intelligence | JF levels/personnel/history, competency certificates/types, gap snapshots |

---

# 15. Checklist Implementasi

- Gunakan migration baru untuk setiap perubahan schema.
- Pastikan tipe PK dan FK kompatibel, termasuk unsigned dan panjang `VARCHAR` untuk master `wilayah`.
- Buat unique constraint seluruh relasi pivot dan business key yang disebutkan.
- Uji `php spark migrate:fresh`, `php spark db:seed DatabaseSeeder`, import master wilayah, dan seluruh test aplikasi.
- Pastikan development seeder tidak memodifikasi master `wilayah`.
- Uji race condition pada kuota enrollment/event, sequence sertifikat, approval, dan perhitungan final grade.
- Uji authorization file private serta kondisi `file_assets.path = NULL`.
- Uji statistik publik untuk memastikan tidak ada data pribadi ASN terekspos.
- Sinkronkan setiap perubahan schema dengan ERD, database blueprint, arsitektur direktori, dan test.

# BegawiPBJ — Data Dictionary

## Tujuan

Dokumen ini mendefinisikan struktur data BegawiPBJ pada level tabel dan kolom. Data dictionary menjelaskan tujuan tabel, kolom, tipe data, nullable, default, primary key, foreign key, unique/index/check constraint, nilai terkendali, klasifikasi data, validasi, serta aturan audit dan retensi utama.

Dokumen ini merupakan turunan operasional dari `dokumen/database-blueprint.md` dan `dokumen/database-erd.md`. Dokumen ini melengkapi dan tidak mengubah kontrak yang telah ditetapkan dalam:

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
```

Tipe data, constraint, dan relasi pada dokumen ini adalah baseline implementasi migration. Jika database engine memiliki batasan terhadap suatu check/partial unique constraint, migration harus menggunakan kombinasi constraint, index, transaction, dan service validation yang menghasilkan perilaku bisnis yang setara.

---

## Konvensi Umum

### Tipe data baseline

| Kebutuhan | Tipe baseline |
|---|---|
| Primary key | `BIGINT UNSIGNED AUTO_INCREMENT` |
| Foreign key | `BIGINT UNSIGNED` |
| Status/key teknis | `VARCHAR(64)` |
| Nama/judul | `VARCHAR(255)` |
| Slug | `VARCHAR(255)` |
| Ringkasan | `TEXT` |
| Konten panjang | `LONGTEXT` |
| JSON | `JSON` |
| Waktu | `DATETIME` UTC |
| Boolean | `TINYINT(1)` |
| Skor | `DECIMAL(5,2)` atau `DECIMAL(6,2)` |
| File size | `BIGINT UNSIGNED` |
| Token hash | `CHAR(64)` atau panjang sesuai algoritma hash |

### Klasifikasi data

| Kode | Klasifikasi | Pengertian |
|---|---|---|
| `P` | Public | Boleh dirender pada Landing/Public Area bila resource telah published/visible |
| `I` | Internal | Hanya untuk pengguna terautentikasi dengan role/permission/scope sesuai |
| `R` | Restricted | Data personal, keamanan, bukti, assessment, atau data sensitif; perlu authorization ketat |
| `S` | System | Metadata teknis/operasional; tidak dirender ke pengguna tanpa kebutuhan |

### Notasi kolom

| Notasi | Arti |
|---|---|
| PK | Primary key |
| FK | Foreign key |
| UK | Unique constraint |
| NN | Tidak boleh `NULL` |
| N | Boleh `NULL` |
| AI | Auto increment |
| D | Nilai default |

### Kolom timestamp

Tabel domain memakai kolom timestamp sesuai kebutuhan:

```text
created_at
updated_at
deleted_at
published_at
archived_at
approved_at
reviewed_at
```

Seluruh waktu disimpan dalam UTC. Kolom actor seperti `created_by`, `updated_by`, `published_by`, atau `reviewed_by` tidak menggantikan audit log lintas domain.

---

## Controlled Values

### Account status

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

### Profile type

```text
asn
provider
academic
public
```

### Role teknis

```text
public_user
participant
trainer
evaluator
operator
admin
super_admin
```

### Workflow publikasi

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

### Visibility file/konten

```text
public
internal
restricted
```

### File lifecycle

```text
pending_upload
stored
scanned
available
rejected
quarantined
deleted
```

---

# Identity dan RBAC

## `users`

Sumber identitas autentikasi utama.

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier internal user |
| `name` | VARCHAR(255) | NN | — | — | R | Nama lengkap; trim; tidak boleh kosong |
| `email` | VARCHAR(255) | NN | — | UK | R | Email unik, lowercase, valid RFC/application policy |
| `username` | VARCHAR(255) | NN | — | UK | I | Username unik; karakter mengikuti policy sistem |
| `password_hash` | VARCHAR(255) | NN | — | — | R | Hash password; tidak pernah plaintext/logged |
| `phone` | VARCHAR(32) | NN | — | — | R | Nomor telepon tervalidasi |
| `profile_type` | VARCHAR(64) | NN | — | IDX | I | `asn`, `provider`, `academic`, atau `public` |
| `account_status` | VARCHAR(64) | NN | `draft` | IDX | I | Account status controlled value |
| `email_verified_at` | DATETIME | N | `NULL` | IDX | S | Waktu email berhasil diverifikasi |
| `last_login_at` | DATETIME | N | `NULL` | — | S | Waktu login terakhir |
| `session_version` | BIGINT UNSIGNED | NN | `1` | — | S | Marker invalidasi session |
| `status_reason` | TEXT | N | `NULL` | — | R | Alasan aman status akun bila diperlukan |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |
| `deleted_at` | DATETIME | N | `NULL` | IDX | S | Soft delete |

Constraint/index:

```text
UNIQUE(email)
UNIQUE(username)
INDEX(account_status)
INDEX(profile_type)
INDEX(email_verified_at)
INDEX(deleted_at)
```

## `user_profiles`

Profil dasar lintas tipe pengguna.

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier profil |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, UK | S | Referensi `users.id` |
| `address_line` | TEXT | NN | — | — | R | Alamat ringkas |
| `province_code` | VARCHAR(32) | NN | — | IDX | I | Kode wilayah provinsi |
| `regency_code` | VARCHAR(32) | NN | — | IDX | I | Kode kabupaten/kota sesuai provinsi |
| `district_code` | VARCHAR(32) | NN | — | IDX | I | Kode kecamatan sesuai kabupaten/kota |
| `village_code` | VARCHAR(32) | N | `NULL` | IDX | I | Kode desa/kelurahan bila diisi |
| `profile_completion_status` | VARCHAR(64) | NN | `draft` | — | S | Status kelengkapan profil terkendali |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Constraint/index:

```text
UNIQUE(user_id)
INDEX(province_code, regency_code, district_code)
FK user_id → users.id
```

## `asn_profiles`

Profil khusus ASN/Pegawai Pemerintah.

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, UK | S | Referensi user dengan profile type `asn` |
| `organization_unit_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | I | Unit organisasi; digunakan terutama ASN Pemprov Lampung |
| `employee_identifier` | VARCHAR(64) | N | `NULL` | — | R | NIP/NIPPK; format/kebijakan tervalidasi |
| `employment_status` | VARCHAR(64) | N | `NULL` | — | I | Misalnya PNS/PPPK |
| `origin_type` | VARCHAR(64) | NN | — | IDX | I | `pemprov_lampung`, `kementerian_lembaga`, `pemerintah_daerah_lain`, `pemerintah_desa`, `bumn_bumd`, `lainnya` |
| `institution_name` | VARCHAR(255) | N | `NULL` | — | I | Instansi induk bila bukan organisasi master |
| `unit_name` | VARCHAR(255) | N | `NULL` | — | I | Nama unit manual bila diperlukan |
| `position_name` | VARCHAR(255) | N | `NULL` | — | I | Jabatan utama |
| `rank_grade` | VARCHAR(128) | N | `NULL` | — | I | Pangkat/golongan bila diisi |
| `official_email` | VARCHAR(255) | N | `NULL` | — | R | Email kedinasan bila tersedia |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Constraint:

```text
UNIQUE(user_id)
FK user_id → users.id
FK organization_unit_id → organization_units.id
INDEX(origin_type)
```

## `provider_profiles`

Profil Penyedia Barang/Jasa.

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, UK | S | Referensi user profile `provider` |
| `business_name` | VARCHAR(255) | NN | — | — | I | Nama badan usaha |
| `business_form` | VARCHAR(64) | NN | — | — | I | Perseorangan/CV/PT/Koperasi/Firma/lainnya |
| `business_field` | VARCHAR(255) | N | `NULL` | — | I | Bidang usaha |
| `business_identifier` | VARCHAR(64) | N | `NULL` | — | R | NIB/identifier legal bila ada |
| `registrant_position` | VARCHAR(255) | NN | — | — | I | Jabatan pendaftar |
| `responsible_person` | VARCHAR(255) | NN | — | — | R | Penanggung jawab |
| `business_email` | VARCHAR(255) | N | `NULL` | — | R | Email badan usaha |
| `business_address` | TEXT | N | `NULL` | — | R | Alamat usaha |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Constraint:

```text
UNIQUE(user_id)
FK user_id → users.id
```

## `academic_profiles`

Profil Akademisi.

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, UK | S | Referensi user profile `academic` |
| `institution_name` | VARCHAR(255) | NN | — | — | I | Institusi pendidikan/riset |
| `institution_type` | VARCHAR(64) | NN | — | — | I | Jenis institusi terkendali |
| `role_name` | VARCHAR(255) | NN | — | — | I | Dosen/peneliti/praktisi/konsultan/lainnya |
| `expertise` | JSON | N | `NULL` | — | I | Bidang keahlian multi-value tervalidasi |
| `institution_email` | VARCHAR(255) | N | `NULL` | — | R | Email institusi |
| `professional_identifier` | VARCHAR(128) | N | `NULL` | — | R | NIDN/NUPTK/identifier profesi bila ada |
| `portfolio_url` | VARCHAR(2048) | N | `NULL` | — | I | URL HTTPS tervalidasi |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Constraint:

```text
UNIQUE(user_id)
FK user_id → users.id
```

## `public_profiles`

Profil Masyarakat Umum.

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, UK | S | Referensi user profile `public` |
| `national_identifier` | VARCHAR(32) | N | `NULL` | — | R | NIK bila dibutuhkan kebijakan |
| `occupation_status` | VARCHAR(128) | NN | — | — | I | Mahasiswa/pegawai/wiraswasta/perangkat desa/lainnya |
| `organization_name` | VARCHAR(255) | N | `NULL` | — | I | Organisasi bila ada |
| `interest_areas` | JSON | N | `NULL` | — | I | Bidang minat PBJ |
| `usage_purpose` | JSON | N | `NULL` | — | I | Tujuan penggunaan |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Constraint:

```text
UNIQUE(user_id)
FK user_id → users.id
```

## `roles`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier role |
| `key` | VARCHAR(64) | NN | — | UK | S | Role teknis controlled value |
| `name` | VARCHAR(255) | NN | — | — | I | Label role UI |
| `description` | TEXT | N | `NULL` | — | I | Deskripsi role |
| `is_system` | TINYINT(1) | NN | `1` | — | S | Role sistem tidak dihapus sembarangan |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Constraint: `UNIQUE(key)`.

## `permissions`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier permission |
| `key` | VARCHAR(128) | NN | — | UK | S | Permission dot notation |
| `name` | VARCHAR(255) | NN | — | — | I | Label UI |
| `domain` | VARCHAR(64) | NN | — | IDX | S | Domain permission |
| `description` | TEXT | N | `NULL` | — | I | Deskripsi izin |
| `is_system` | TINYINT(1) | NN | `1` | — | S | Permission sistem |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Constraint/index: `UNIQUE(key)`, `INDEX(domain)`.

## `user_roles`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier assignment role |
| `user_id` | BIGINT UNSIGNED | NN | — | FK | S | User penerima role |
| `role_id` | BIGINT UNSIGNED | NN | — | FK | S | Role yang diberikan |
| `status` | VARCHAR(64) | NN | `active` | IDX | S | `active`, `revoked`, `expired` |
| `granted_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor pemberi; null untuk system |
| `granted_at` | DATETIME | NN | — | — | S | Waktu pemberian |
| `revoked_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor pencabut |
| `revoked_at` | DATETIME | N | `NULL` | — | S | Waktu pencabutan |
| `reason` | TEXT | N | `NULL` | — | I | Alasan grant/revoke |
| `created_at` | DATETIME | NN | — | — | S | Waktu record dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu record diperbarui |

Constraint/index:

```text
UNIQUE(user_id, role_id)
INDEX(user_id, status)
INDEX(role_id, status)
FK user_id → users.id
FK role_id → roles.id
FK granted_by → users.id
FK revoked_by → users.id
```

## `role_permissions`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `role_id` | BIGINT UNSIGNED | NN | — | PK, FK | S | Role |
| `permission_id` | BIGINT UNSIGNED | NN | — | PK, FK | S | Permission |
| `granted_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor pemberi |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |

Constraint: `PRIMARY KEY(role_id, permission_id)`.

## `email_verifications`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `user_id` | BIGINT UNSIGNED | NN | — | FK | S | Pemilik token |
| `token_hash` | CHAR(64) | NN | — | UK | R | Hash token; plain token tidak disimpan |
| `expires_at` | DATETIME | NN | — | IDX | S | Kedaluwarsa token |
| `verified_at` | DATETIME | N | `NULL` | — | S | Waktu token digunakan |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |

Constraint/index: `UNIQUE(token_hash)`, `INDEX(user_id, expires_at)`.

## `password_resets`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `user_id` | BIGINT UNSIGNED | NN | — | FK | S | Pemilik token |
| `token_hash` | CHAR(64) | NN | — | UK | R | Hash token reset |
| `expires_at` | DATETIME | NN | — | IDX | S | Kedaluwarsa |
| `used_at` | DATETIME | N | `NULL` | — | S | Waktu penggunaan |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |

Constraint/index: `UNIQUE(token_hash)`, `INDEX(user_id, expires_at)`.

## `role_requests`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier request |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Requester |
| `requested_role_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Hanya role self-service `trainer`/`evaluator` |
| `status` | VARCHAR(64) | NN | `draft` | IDX | I | Workflow request |
| `reason` | TEXT | N | `NULL` | — | I | Alasan request |
| `reviewed_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Reviewer |
| `reviewed_at` | DATETIME | N | `NULL` | — | S | Waktu review |
| `review_notes` | TEXT | N | `NULL` | — | I | Catatan review/revisi |
| `archived_at` | DATETIME | N | `NULL` | — | S | Waktu arsip |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Status: `draft`, `submitted`, `in_review`, `revision_required`, `approved`, `rejected`, `cancelled`, `archived`.

---

# Organisasi, Verifikasi, dan Peran Pengadaan

## `organization_units`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier unit |
| `parent_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | Parent unit untuk hierarchy |
| `code` | VARCHAR(64) | NN | — | UK | I | Kode organisasi unik |
| `name` | VARCHAR(255) | NN | — | — | I | Nama unit |
| `type` | VARCHAR(64) | NN | — | IDX | I | `province`, `agency`, `bureau`, `department`, `inspectorate`, `hospital`, `secretariat`, atau nilai terkendali |
| `is_active` | TINYINT(1) | NN | `1` | IDX | S | Status aktif |
| `sort_order` | INT UNSIGNED | NN | `0` | — | S | Urutan |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Constraint/index: `UNIQUE(code)`, `INDEX(parent_id)`, `INDEX(type, is_active)`.

## `procurement_roles`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier master peran |
| `key` | VARCHAR(64) | NN | — | UK | S | Kode teknis peran pengadaan |
| `name` | VARCHAR(255) | NN | — | — | I | Label peran |
| `description` | TEXT | N | `NULL` | — | I | Deskripsi |
| `is_active` | TINYINT(1) | NN | `1` | — | S | Status master |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

## `user_procurement_roles`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `user_id` | BIGINT UNSIGNED | NN | — | FK | S | User pemilik deklarasi |
| `procurement_role_id` | BIGINT UNSIGNED | NN | — | FK | S | Master peran pengadaan |
| `verification_status` | VARCHAR(64) | NN | `self_declared` | IDX | I | `self_declared`, `pending`, `verified`, `rejected` |
| `verified_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Reviewer |
| `verified_at` | DATETIME | N | `NULL` | — | S | Waktu verifikasi |
| `assignment_start_at` | DATETIME | N | `NULL` | — | I | Awal penugasan bila ada |
| `assignment_end_at` | DATETIME | N | `NULL` | — | I | Akhir penugasan bila ada |
| `evidence_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | R | Bukti file restricted |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Constraint: `UNIQUE(user_id, procurement_role_id)`.

## `profile_change_requests`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier request |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | User requester |
| `status` | VARCHAR(64) | NN | `draft` | IDX | I | Workflow profile change |
| `requested_payload` | JSON | NN | — | — | R | Payload perubahan; wajib redaction pada audit/log |
| `reason` | TEXT | N | `NULL` | — | I | Alasan perubahan |
| `reviewed_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Reviewer |
| `reviewed_at` | DATETIME | N | `NULL` | — | S | Waktu review |
| `review_notes` | TEXT | N | `NULL` | — | I | Catatan reviewer |
| `applied_at` | DATETIME | N | `NULL` | — | S | Waktu perubahan diterapkan |
| `archived_at` | DATETIME | N | `NULL` | — | S | Waktu diarsipkan |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

## `profile_verifications`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Subjek verifikasi |
| `verification_type` | VARCHAR(64) | NN | — | IDX | I | Tipe verifikasi terkendali |
| `status` | VARCHAR(64) | NN | `pending` | IDX | I | `pending`, `in_review`, `approved`, `rejected`, `revision_required` |
| `requested_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor request |
| `reviewed_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Reviewer |
| `reason` | TEXT | N | `NULL` | — | I | Alasan keputusan |
| `notes` | TEXT | N | `NULL` | — | I | Catatan aman |
| `reviewed_at` | DATETIME | N | `NULL` | — | S | Waktu review |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

---

# Shared Governance

## `approvals`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier approval |
| `resource_type` | VARCHAR(128) | NN | — | IDX | S | Polymorphic resource type whitelist |
| `resource_id` | BIGINT UNSIGNED | NN | — | IDX | S | Resource ID |
| `requested_action` | VARCHAR(128) | NN | — | — | I | Aksi yang diminta approval |
| `status` | VARCHAR(64) | NN | `pending` | IDX | I | `pending`, `in_review`, `approved`, `rejected`, `revision_required`, `cancelled`, `expired` |
| `requested_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Requester/system context |
| `reviewed_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Reviewer |
| `reason` | TEXT | N | `NULL` | — | I | Alasan request/keputusan |
| `notes` | TEXT | N | `NULL` | — | I | Catatan aman |
| `expires_at` | DATETIME | N | `NULL` | — | S | Deadline approval |
| `reviewed_at` | DATETIME | N | `NULL` | — | S | Waktu keputusan |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

Index: `INDEX(resource_type, resource_id)`, `INDEX(status, created_at)`, `INDEX(reviewed_by, status)`.

## `workflow_transitions`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier transition |
| `resource_type` | VARCHAR(128) | NN | — | IDX | S | Polymorphic resource type whitelist |
| `resource_id` | BIGINT UNSIGNED | NN | — | IDX | S | Resource ID |
| `from_state` | VARCHAR(64) | N | `NULL` | — | S | State sebelum transisi |
| `to_state` | VARCHAR(64) | NN | — | IDX | S | State sesudah transisi |
| `action` | VARCHAR(128) | NN | — | — | S | Nama aksi workflow |
| `actor_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | User actor; null untuk system |
| `actor_type` | VARCHAR(32) | NN | `user` | — | S | `user` atau `system` |
| `reason` | TEXT | N | `NULL` | — | I | Alasan bila diwajibkan |
| `notes` | TEXT | N | `NULL` | — | I | Catatan aman |
| `metadata` | JSON | N | `NULL` | — | S | Metadata ter-redaksi |
| `created_at` | DATETIME | NN | — | IDX | S | Waktu transisi |

Index: `INDEX(resource_type, resource_id, created_at)`, `INDEX(actor_id, created_at)`, `INDEX(to_state, created_at)`.

## `audit_logs`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier audit append-only |
| `actor_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | Actor user/system/guest context |
| `action` | VARCHAR(128) | NN | — | IDX | S | Action key |
| `resource_type` | VARCHAR(128) | N | `NULL` | IDX | S | Resource type |
| `resource_id` | BIGINT UNSIGNED | N | `NULL` | IDX | S | Resource ID |
| `result` | VARCHAR(32) | NN | — | IDX | S | `success`, `failed`, `denied`, `partial` |
| `before_snapshot` | JSON | N | `NULL` | — | R | Snapshot harus redacted |
| `after_snapshot` | JSON | N | `NULL` | — | R | Snapshot harus redacted |
| `reason` | TEXT | N | `NULL` | — | I | Alasan bila ada |
| `ip_hash` | CHAR(64) | N | `NULL` | — | R | Hash/minimized IP context |
| `user_agent_hash` | CHAR(64) | N | `NULL` | — | R | Hash/minimized user-agent |
| `request_id` | VARCHAR(128) | N | `NULL` | — | S | Correlation ID |
| `created_at` | DATETIME | NN | — | IDX | S | Waktu audit |

Audit tidak boleh menyimpan password, plain token, session ID, secret, credential, atau full restricted payload.

## `file_assets`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier file metadata |
| `disk` | VARCHAR(64) | NN | — | — | S | Storage disk key |
| `path` | VARCHAR(1024) | NN | — | — | R | Internal storage path; tidak diekspos publik |
| `original_name` | VARCHAR(512) | NN | — | — | I | Nama untrusted untuk display aman |
| `stored_name` | VARCHAR(255) | NN | — | — | S | Nama unik server-side |
| `extension` | VARCHAR(32) | NN | — | — | S | Ekstensi tervalidasi |
| `mime_type` | VARCHAR(255) | NN | — | — | S | MIME tervalidasi server-side |
| `size_bytes` | BIGINT UNSIGNED | NN | — | — | S | Ukuran bytes |
| `checksum` | CHAR(64) | N | `NULL` | — | S | Hash integritas bila digunakan |
| `visibility` | VARCHAR(64) | NN | `internal` | IDX | I | `public`, `internal`, `restricted` |
| `lifecycle_status` | VARCHAR(64) | NN | `pending_upload` | IDX | S | Lifecycle file |
| `resource_type` | VARCHAR(128) | N | `NULL` | IDX | S | Polymorphic owner type |
| `resource_id` | BIGINT UNSIGNED | N | `NULL` | IDX | S | Polymorphic owner ID |
| `uploaded_by` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | Actor uploader/system |
| `available_at` | DATETIME | N | `NULL` | — | S | Waktu file tersedia |
| `expires_at` | DATETIME | N | `NULL` | IDX | S | Expiry temporary/export |
| `deleted_at` | DATETIME | N | `NULL` | — | S | Soft delete/lifecycle delete |
| `metadata` | JSON | N | `NULL` | — | R | Metadata aman; tidak secret/restricted berlebihan |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |

## `notifications`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Penerima |
| `channel` | VARCHAR(64) | NN | `database` | IDX | S | `database`, `email`, atau channel terkendali |
| `type` | VARCHAR(128) | NN | — | — | S | Notification type key |
| `title` | VARCHAR(255) | NN | — | — | I | Judul aman |
| `body` | TEXT | N | `NULL` | — | I | Isi minimum tanpa secret/restricted berlebihan |
| `data` | JSON | N | `NULL` | — | I | Payload aman |
| `read_at` | DATETIME | N | `NULL` | IDX | S | Waktu dibaca |
| `sent_at` | DATETIME | N | `NULL` | IDX | S | Waktu dikirim |
| `created_at` | DATETIME | NN | — | IDX | S | Waktu dibuat |

Index: `INDEX(user_id, read_at, created_at)`, `INDEX(channel, sent_at)`.

## `bookmarks`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `user_id` | BIGINT UNSIGNED | NN | — | FK | S | Pemilik bookmark |
| `bookmarkable_type` | VARCHAR(128) | NN | — | IDX | S | Resource type whitelist |
| `bookmarkable_id` | BIGINT UNSIGNED | NN | — | IDX | S | Resource ID |
| `created_at` | DATETIME | NN | — | — | S | Waktu bookmark |

Constraint: `UNIQUE(user_id, bookmarkable_type, bookmarkable_id)`.

## `exports`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier export |
| `requested_by` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Requester |
| `type` | VARCHAR(128) | NN | — | — | I | Tipe export terkendali |
| `status` | VARCHAR(64) | NN | `queued` | IDX | S | `queued`, `generating`, `ready`, `failed`, `expired`, `deleted` |
| `filters` | JSON | N | `NULL` | — | R | Filter/scope export |
| `file_id` | BIGINT UNSIGNED | N | `NULL` | FK | S | File hasil export |
| `error_message` | TEXT | N | `NULL` | — | I | Pesan aman gagal generate |
| `expires_at` | DATETIME | N | `NULL` | IDX | S | Expiry download |
| `completed_at` | DATETIME | N | `NULL` | — | S | Waktu selesai |
| `deleted_at` | DATETIME | N | `NULL` | — | S | Waktu delete |
| `created_at` | DATETIME | NN | — | — | S | Waktu request |
| `updated_at` | DATETIME | NN | — | — | S | Waktu update |

---

# Arena Belajar

## `programs`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier program |
| `title` | VARCHAR(255) | NN | — | — | P/I | Judul program |
| `slug` | VARCHAR(255) | NN | — | UK | P/I | Slug unik |
| `summary` | TEXT | N | `NULL` | — | P/I | Ringkasan |
| `description` | LONGTEXT | N | `NULL` | — | P/I | Konten program tersanitasi |
| `status` | VARCHAR(64) | NN | `draft` | IDX | I | Workflow/publication status |
| `visibility` | VARCHAR(64) | NN | `internal` | IDX | I | `public`, `internal`, `restricted` |
| `featured` | TINYINT(1) | NN | `0` | — | I | Program unggulan |
| `cover_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | I | Cover file |
| `created_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Creator |
| `updated_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Last updater |
| `published_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Publisher |
| `published_at` | DATETIME | N | `NULL` | IDX | S | Waktu publish |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |
| `updated_at` | DATETIME | NN | — | — | S | Waktu diperbarui |
| `deleted_at` | DATETIME | N | `NULL` | — | S | Soft delete |

Constraint/index: `UNIQUE(slug)`, `INDEX(status, visibility, published_at)`.

## `courses`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier course |
| `program_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | Program induk bila ada |
| `title` | VARCHAR(255) | NN | — | — | P/I | Judul course |
| `slug` | VARCHAR(255) | NN | — | UK | P/I | Slug unik |
| `summary` | TEXT | N | `NULL` | — | P/I | Ringkasan |
| `description` | LONGTEXT | N | `NULL` | — | P/I | Konten tersanitasi |
| `level` | VARCHAR(64) | N | `NULL` | — | I | Level course |
| `duration_minutes` | INT UNSIGNED | N | `NULL` | — | I | Estimasi durasi |
| `status` | VARCHAR(64) | NN | `draft` | IDX | I | Status publikasi |
| `visibility` | VARCHAR(64) | NN | `internal` | IDX | I | Visibility |
| `cover_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | I | Cover file |
| `created_by`, `updated_by`, `published_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor metadata |
| `published_at` | DATETIME | N | `NULL` | IDX | S | Waktu publish |
| `created_at`, `updated_at`, `deleted_at` | DATETIME | Sesuai | — | — | S | Timestamp lifecycle |

Constraint/index: `UNIQUE(slug)`, `INDEX(program_id)`, `INDEX(status, visibility, published_at)`.

## `learning_classes`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier class |
| `course_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Course induk |
| `code` | VARCHAR(64) | NN | — | UK scope | I | Kode class unik dalam course |
| `name` | VARCHAR(255) | NN | — | — | I | Nama kelas |
| `status` | VARCHAR(64) | NN | `draft` | IDX | I | Lifecycle class terkendali |
| `registration_open_at` | DATETIME | N | `NULL` | IDX | S | Awal pendaftaran |
| `registration_close_at` | DATETIME | N | `NULL` | IDX | S | Akhir pendaftaran |
| `start_at` | DATETIME | N | `NULL` | IDX | S | Mulai kelas |
| `end_at` | DATETIME | N | `NULL` | IDX | S | Akhir kelas |
| `capacity` | INT UNSIGNED | N | `NULL` | — | I | Kuota; null berarti sesuai policy tanpa limit |
| `certificate_enabled` | TINYINT(1) | NN | `0` | — | S | Aktivasi sertifikat |
| `passing_grade` | DECIMAL(5,2) | NN | `70.00` | — | S | Rentang 0–100 |
| `quiz_weight` | DECIMAL(5,2) | NN | `40.00` | — | S | Bobot quiz |
| `assignment_weight` | DECIMAL(5,2) | NN | `0.00` | — | S | Bobot assignment |
| `post_test_weight` | DECIMAL(5,2) | NN | `60.00` | — | S | Bobot post-test |
| `created_by`, `updated_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor metadata |
| `created_at`, `updated_at`, `deleted_at` | DATETIME | Sesuai | — | — | S | Timestamp lifecycle |

Constraint:

```text
UNIQUE(course_id, code)
CHECK(quiz_weight + assignment_weight + post_test_weight = 100)
CHECK(post_test_weight = 60)
CHECK(passing_grade >= 0 AND passing_grade <= 100)
```

## `class_trainers` dan `class_evaluators`

Kedua tabel memiliki bentuk yang sama; pembeda hanya jenis assignment.

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `class_id` | BIGINT UNSIGNED | NN | — | PK, FK | S | Class terkait |
| `user_id` | BIGINT UNSIGNED | NN | — | PK, FK | S | Trainer/Evaluator terkait |
| `assigned_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor penugasan |
| `assigned_at` | DATETIME | NN | — | — | S | Waktu penugasan |
| `status` | VARCHAR(64) | NN | `active` | — | S | `active` atau `revoked` |

Constraint: `PRIMARY KEY(class_id, user_id)`.

## `learning_modules`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier module |
| `class_id` | BIGINT UNSIGNED | NN | — | FK | S | Class induk |
| `title` | VARCHAR(255) | NN | — | — | I | Judul module |
| `description` | TEXT | N | `NULL` | — | I | Deskripsi |
| `sort_order` | INT UNSIGNED | NN | — | UK scope | S | Urutan dalam class |
| `is_required` | TINYINT(1) | NN | `1` | — | S | Komponen wajib |
| `status` | VARCHAR(64) | NN | `draft` | — | I | Status terkendali |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint: `UNIQUE(class_id, sort_order)`.

## `lessons`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier lesson |
| `module_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Module induk |
| `title` | VARCHAR(255) | NN | — | — | I | Judul lesson |
| `slug` | VARCHAR(255) | N | `NULL` | IDX | I | Slug bila dibutuhkan |
| `content_type` | VARCHAR(64) | NN | — | — | I | `text`, `document`, `video`, `link`, atau controlled value |
| `content` | LONGTEXT | N | `NULL` | — | I | Konten teks tersanitasi |
| `file_id` | BIGINT UNSIGNED | N | `NULL` | FK | I | Materi file |
| `external_url` | VARCHAR(2048) | N | `NULL` | — | I | URL tervalidasi |
| `sort_order` | INT UNSIGNED | NN | — | UK scope | S | Urutan dalam module |
| `is_required` | TINYINT(1) | NN | `1` | — | S | Lesson wajib |
| `status` | VARCHAR(64) | NN | `draft` | IDX | I | Availability status |
| `visibility` | VARCHAR(64) | NN | `internal` | IDX | I | Visibility materi |
| `created_by`, `updated_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor metadata |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint: `UNIQUE(module_id, sort_order)`.

## `enrollments`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier enrollment |
| `class_id` | BIGINT UNSIGNED | NN | — | FK | S | Class |
| `user_id` | BIGINT UNSIGNED | NN | — | FK | S | Participant |
| `status` | VARCHAR(64) | NN | `pending` | IDX | I | `pending`, `active`, `completed`, `withdrawn`, `cancelled` |
| `enrolled_at` | DATETIME | NN | — | — | S | Waktu daftar |
| `activated_at` | DATETIME | N | `NULL` | — | S | Waktu aktif |
| `completed_at` | DATETIME | N | `NULL` | — | S | Waktu selesai |
| `final_score` | DECIMAL(5,2) | N | `NULL` | — | R | Skor akhir 0–100 |
| `passed_at` | DATETIME | N | `NULL` | — | S | Waktu lulus |
| `certificate_eligible_at` | DATETIME | N | `NULL` | IDX | S | Saat memenuhi eligibility |
| `withdrawn_at` | DATETIME | N | `NULL` | — | S | Waktu withdraw |
| `cancelled_at` | DATETIME | N | `NULL` | — | S | Waktu cancel |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint: `UNIQUE(class_id, user_id)`.

---

# Learning Assessment

## `pre_tests`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `class_id` | BIGINT UNSIGNED | NN | — | FK, UK | S | Satu pre-test per class |
| `is_enabled` | TINYINT(1) | NN | `0` | — | S | Aktivasi pre-test |
| `attempt_limit` | INT UNSIGNED | NN | `1` | — | S | Baseline satu attempt |
| `status` | VARCHAR(64) | NN | `draft` | — | I | Availability status |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `quizzes`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier quiz |
| `class_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Class terkait |
| `lesson_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | Lesson terkait; null untuk quiz tingkat class |
| `type` | VARCHAR(64) | NN | — | IDX | I | `pre_test`, `lesson_quiz`, `post_test` |
| `title` | VARCHAR(255) | NN | — | — | I | Judul |
| `instructions` | TEXT | N | `NULL` | — | I | Instruksi |
| `attempt_limit` | INT UNSIGNED | NN | `1` | — | S | Batas attempt |
| `is_required` | TINYINT(1) | NN | `0` | — | S | Wajib/tidak |
| `status` | VARCHAR(64) | NN | `draft` | IDX | I | Availability status |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `quiz_questions`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `quiz_id` | BIGINT UNSIGNED | NN | — | FK | S | Quiz induk |
| `type` | VARCHAR(64) | NN | — | — | I | Tipe pertanyaan terkendali |
| `prompt` | LONGTEXT | NN | — | — | I | Pertanyaan |
| `points` | DECIMAL(5,2) | NN | `1.00` | — | S | Poin >= 0 |
| `sort_order` | INT UNSIGNED | NN | — | UK scope | S | Urutan |
| `explanation` | TEXT | N | `NULL` | — | I | Penjelasan setelah policy mengizinkan |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint: `UNIQUE(quiz_id, sort_order)`.

## `quiz_options`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `question_id` | BIGINT UNSIGNED | NN | — | FK | S | Question induk |
| `option_text` | TEXT | NN | — | — | I | Isi opsi |
| `is_correct` | TINYINT(1) | NN | `0` | — | R | Jawaban benar; tidak dikirim sebelum policy mengizinkan |
| `sort_order` | INT UNSIGNED | NN | — | UK scope | S | Urutan |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint: `UNIQUE(question_id, sort_order)`.

## `quiz_attempts`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier attempt |
| `quiz_id` | BIGINT UNSIGNED | NN | — | FK | S | Quiz |
| `enrollment_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Ownership peserta |
| `attempt_number` | INT UNSIGNED | NN | — | UK scope | S | Mulai dari 1 |
| `status` | VARCHAR(64) | NN | `started` | IDX | I | `started`, `submitted`, `graded`, `invalidated` |
| `started_at` | DATETIME | NN | — | — | S | Mulai attempt |
| `submitted_at` | DATETIME | N | `NULL` | — | S | Submit attempt |
| `graded_at` | DATETIME | N | `NULL` | — | S | Grading selesai |
| `score` | DECIMAL(5,2) | N | `NULL` | — | R | Skor 0–100 |
| `invalidated_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor invalidasi |
| `invalidated_reason` | TEXT | N | `NULL` | — | I | Alasan invalidasi |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint: `UNIQUE(quiz_id, enrollment_id, attempt_number)`.

## `quiz_answers`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `attempt_id` | BIGINT UNSIGNED | NN | — | FK | S | Quiz attempt |
| `question_id` | BIGINT UNSIGNED | NN | — | FK | S | Question |
| `answer_payload` | JSON | N | `NULL` | — | R | Jawaban peserta |
| `is_correct` | TINYINT(1) | N | `NULL` | — | R | Hasil correctness |
| `score_awarded` | DECIMAL(5,2) | N | `NULL` | — | R | Poin diberikan |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint: `UNIQUE(attempt_id, question_id)`.

## `assignments`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier assignment |
| `class_id` | BIGINT UNSIGNED | NN | — | FK | S | Class |
| `lesson_id` | BIGINT UNSIGNED | N | `NULL` | FK | S | Lesson bila spesifik |
| `title` | VARCHAR(255) | NN | — | — | I | Judul tugas |
| `instructions` | LONGTEXT | N | `NULL` | — | I | Instruksi |
| `due_at` | DATETIME | N | `NULL` | IDX | S | Deadline |
| `is_required` | TINYINT(1) | NN | `0` | — | S | Wajib/tidak |
| `max_attempts` | INT UNSIGNED | NN | `1` | — | S | Batas submit/revisi |
| `allowed_mimes` | JSON | N | `NULL` | — | S | Override allowlist file |
| `max_file_size_bytes` | BIGINT UNSIGNED | N | `NULL` | — | S | Override batas file |
| `status` | VARCHAR(64) | NN | `draft` | IDX | I | Availability status |
| `created_by`, `updated_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor metadata |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `assignment_submissions`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier submission |
| `assignment_id` | BIGINT UNSIGNED | NN | — | FK | S | Assignment |
| `enrollment_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Enrollment owner |
| `attempt_number` | INT UNSIGNED | NN | — | UK scope | S | Nomor revisi/submission |
| `status` | VARCHAR(64) | NN | `draft` | IDX | I | `draft`, `submitted`, `in_review`, `revision_required`, `graded`, `cancelled` |
| `content` | LONGTEXT | N | `NULL` | — | R | Jawaban teks peserta |
| `submitted_at` | DATETIME | N | `NULL` | — | S | Waktu submit |
| `graded_at` | DATETIME | N | `NULL` | — | S | Waktu grading |
| `graded_by` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | Evaluator |
| `score` | DECIMAL(5,2) | N | `NULL` | — | R | Nilai |
| `feedback` | LONGTEXT | N | `NULL` | — | R | Feedback evaluator |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint: `UNIQUE(assignment_id, enrollment_id, attempt_number)`.

## `assignment_submission_files`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `submission_id` | BIGINT UNSIGNED | NN | — | PK, FK | S | Submission |
| `file_id` | BIGINT UNSIGNED | NN | — | PK, FK | S | File asset restricted |
| `created_at` | DATETIME | NN | — | — | S | Waktu attach |

Constraint: `PRIMARY KEY(submission_id, file_id)`.

## `learning_progress`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier progress |
| `enrollment_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Enrollment |
| `lesson_id` | BIGINT UNSIGNED | NN | — | FK | S | Lesson |
| `status` | VARCHAR(64) | NN | `not_started` | IDX | I | `not_started`, `in_progress`, `completed` |
| `started_at` | DATETIME | N | `NULL` | — | S | Mulai belajar |
| `completed_at` | DATETIME | N | `NULL` | — | S | Selesai lesson |
| `progress_percent` | DECIMAL(5,2) | NN | `0.00` | — | S | 0–100 |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint: `UNIQUE(enrollment_id, lesson_id)`.

## `feedbacks`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier feedback |
| `enrollment_id` | BIGINT UNSIGNED | NN | — | FK, UK | S | Satu feedback per enrollment |
| `rating` | TINYINT UNSIGNED | N | `NULL` | — | I | Rating terkendali, misalnya 1–5 |
| `comment` | TEXT | N | `NULL` | — | I | Komentar peserta |
| `status` | VARCHAR(64) | NN | `not_started` | — | S | `not_started`, `submitted` |
| `submitted_at` | DATETIME | N | `NULL` | — | S | Waktu submit |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

---

# Bunga Rampai / Knowledge

## `knowledge_categories`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier kategori |
| `parent_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | Kategori parent |
| `name` | VARCHAR(255) | NN | — | — | P/I | Nama kategori |
| `slug` | VARCHAR(255) | NN | — | UK | P/I | Slug unik |
| `description` | TEXT | N | `NULL` | — | P/I | Deskripsi |
| `sort_order` | INT UNSIGNED | NN | `0` | — | S | Urutan |
| `is_active` | TINYINT(1) | NN | `1` | IDX | S | Status aktif |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `knowledge_tags`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier tag |
| `name` | VARCHAR(255) | NN | — | — | P/I | Nama tag |
| `slug` | VARCHAR(255) | NN | — | UK | P/I | Slug unik |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `knowledge_articles`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier konten |
| `type` | VARCHAR(64) | NN | `article` | IDX | I | `article`, `faq`, `case_study`, `template` |
| `title` | VARCHAR(255) | NN | — | — | P/I | Judul |
| `slug` | VARCHAR(255) | NN | — | UK | P/I | Slug unik |
| `summary` | TEXT | N | `NULL` | — | P/I | Ringkasan |
| `content` | LONGTEXT | N | `NULL` | — | P/I | Konten tersanitasi |
| `workflow_status` | VARCHAR(64) | NN | `draft` | IDX | I | Workflow publikasi |
| `visibility` | VARCHAR(64) | NN | `internal` | IDX | I | Visibility konten |
| `featured` | TINYINT(1) | NN | `0` | IDX | I | Konten unggulan |
| `carousel_enabled` | TINYINT(1) | NN | `0` | IDX | I | Tampil carousel |
| `carousel_order` | INT UNSIGNED | N | `NULL` | — | S | Urutan carousel |
| `cover_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | I | File cover |
| `author_id` | BIGINT UNSIGNED | NN | — | FK | S | Penulis |
| `reviewed_by`, `published_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor review/publish |
| `published_at`, `archived_at` | DATETIME | N | `NULL` | IDX | S | Lifecycle content |
| `created_at`, `updated_at`, `deleted_at` | DATETIME | Sesuai | — | — | S | Timestamp |

Constraint/index:

```text
UNIQUE(slug)
INDEX(type, workflow_status, visibility, published_at)
INDEX(featured, carousel_enabled, carousel_order)
```

## `knowledge_article_categories`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi |
|---|---|---:|---|---|---|---|
| `article_id` | BIGINT UNSIGNED | NN | — | PK, FK | S | Knowledge article |
| `category_id` | BIGINT UNSIGNED | NN | — | PK, FK | S | Kategori |
| `created_at` | DATETIME | NN | — | — | S | Waktu relasi dibuat |

## `knowledge_article_tags`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi |
|---|---|---:|---|---|---|---|
| `article_id` | BIGINT UNSIGNED | NN | — | PK, FK | S | Knowledge article |
| `tag_id` | BIGINT UNSIGNED | NN | — | PK, FK | S | Tag |
| `created_at` | DATETIME | NN | — | — | S | Waktu relasi dibuat |

## `knowledge_media`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier media |
| `article_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | Konten parent bila ada |
| `type` | VARCHAR(64) | NN | — | IDX | I | `image`, `poster`, `flyer`, `infographic`, `gallery`, `video_file`, `youtube`, `social_link`, `external_link` |
| `title` | VARCHAR(255) | N | `NULL` | — | P/I | Judul media |
| `caption` | TEXT | N | `NULL` | — | P/I | Caption |
| `alt_text` | VARCHAR(500) | N | `NULL` | — | P/I | Teks alternatif accessibility |
| `file_id` | BIGINT UNSIGNED | N | `NULL` | FK | I | File media |
| `external_url` | VARCHAR(2048) | N | `NULL` | — | I | URL tervalidasi provider/policy |
| `thumbnail_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | I | Thumbnail |
| `workflow_status` | VARCHAR(64) | NN | `draft` | IDX | I | Workflow media |
| `visibility` | VARCHAR(64) | NN | `internal` | IDX | I | Visibility |
| `sort_order` | INT UNSIGNED | NN | `0` | — | S | Urutan |
| `featured`, `carousel_enabled` | TINYINT(1) | NN | `0` | — | I | Kurasi tampilan |
| `created_by`, `published_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor metadata |
| `published_at`, `created_at`, `updated_at` | DATETIME | Sesuai | — | — | S | Timestamp |

---

# Regulasi PBJ

## `regulation_categories`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier kategori |
| `name` | VARCHAR(255) | NN | — | — | P/I | Nama kategori |
| `slug` | VARCHAR(255) | NN | — | UK | P/I | Slug unik |
| `description` | TEXT | N | `NULL` | — | P/I | Deskripsi |
| `is_active` | TINYINT(1) | NN | `1` | — | S | Status aktif |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `regulations`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier regulasi |
| `category_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Kategori |
| `title` | VARCHAR(255) | NN | — | — | P/I | Judul regulasi |
| `slug` | VARCHAR(255) | NN | — | UK | P/I | Slug unik |
| `document_number` | VARCHAR(255) | NN | — | UK scope | P/I | Nomor dokumen |
| `document_year` | SMALLINT UNSIGNED | NN | — | UK scope | P/I | Tahun dokumen valid |
| `issuer_name` | VARCHAR(255) | NN | — | UK scope | P/I | Instansi penerbit |
| `official_date`, `effective_date` | DATETIME | N | `NULL` | — | P/I | Tanggal resmi/berlaku |
| `summary` | TEXT | N | `NULL` | — | P/I | Ringkasan |
| `official_source_url` | VARCHAR(2048) | N | `NULL` | — | P/I | URL resmi tervalidasi |
| `workflow_status` | VARCHAR(64) | NN | `draft` | IDX | I | Workflow editorial |
| `legal_status` | VARCHAR(64) | NN | `active` | IDX | P/I | `active`, `amended`, `revoked`, `expired` |
| `visibility` | VARCHAR(64) | NN | `internal` | IDX | I | Visibility record |
| `current_version_id` | BIGINT UNSIGNED | N | `NULL` | FK | S | Pointer versi aktif |
| `created_by`, `reviewed_by`, `published_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor metadata |
| `published_at`, `archived_at` | DATETIME | N | `NULL` | IDX | S | Lifecycle |
| `created_at`, `updated_at`, `deleted_at` | DATETIME | Sesuai | — | — | S | Timestamp |

Constraint/index:

```text
UNIQUE(slug)
UNIQUE(document_number, document_year, issuer_name)
INDEX(category_id, workflow_status, legal_status, visibility)
INDEX(published_at)
```

## `regulation_versions`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier versi |
| `regulation_id` | BIGINT UNSIGNED | NN | — | FK | S | Regulasi induk |
| `version_number` | INT UNSIGNED | NN | — | UK scope | I | Nomor versi >= 1 |
| `file_id` | BIGINT UNSIGNED | N | `NULL` | FK | I/R | File regulasi; nullable jika belum tersedia |
| `changelog` | TEXT | N | `NULL` | — | I | Ringkasan perubahan |
| `is_current` | TINYINT(1) | NN | `0` | IDX | S | Penanda versi current |
| `uploaded_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Uploader |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |

Constraint: `UNIQUE(regulation_id, version_number)`; maksimum satu `is_current = 1` per regulation melalui partial unique index atau service transaction.

## `regulation_relations`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier relasi |
| `source_regulation_id` | BIGINT UNSIGNED | NN | — | FK | S | Regulasi sumber |
| `target_regulation_id` | BIGINT UNSIGNED | NN | — | FK | S | Regulasi target |
| `relation_type` | VARCHAR(64) | NN | — | — | I | `amends`, `amended_by`, `revokes`, `revoked_by`, `replaces`, `replaced_by`, `implements`, `implemented_by`, `references`, `referenced_by` |
| `notes` | TEXT | N | `NULL` | — | I | Catatan |
| `created_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor |
| `created_at` | DATETIME | NN | — | — | S | Waktu dibuat |

Constraint:

```text
UNIQUE(source_regulation_id, target_regulation_id, relation_type)
CHECK(source_regulation_id <> target_regulation_id)
```

## `regulation_downloads`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier log |
| `regulation_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Regulasi |
| `version_id` | BIGINT UNSIGNED | N | `NULL` | FK | S | Versi diakses |
| `file_id` | BIGINT UNSIGNED | N | `NULL` | FK | S | File diakses |
| `user_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | User jika terautentikasi |
| `access_type` | VARCHAR(64) | NN | — | — | S | `preview`, `download`, atau controlled value |
| `created_at` | DATETIME | NN | — | IDX | S | Waktu akses |

---

# Berita, Agenda, dan Event

## `news_categories`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `name` | VARCHAR(255) | NN | — | — | P/I | Nama kategori |
| `slug` | VARCHAR(255) | NN | — | UK | P/I | Slug unik |
| `description` | TEXT | N | `NULL` | — | P/I | Deskripsi |
| `is_active` | TINYINT(1) | NN | `1` | — | S | Status |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `news`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier berita |
| `category_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | Kategori |
| `title` | VARCHAR(255) | NN | — | — | P/I | Judul |
| `slug` | VARCHAR(255) | NN | — | UK | P/I | Slug unik |
| `summary` | TEXT | N | `NULL` | — | P/I | Ringkasan |
| `content` | LONGTEXT | N | `NULL` | — | P/I | Konten tersanitasi |
| `cover_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | I | Cover |
| `workflow_status` | VARCHAR(64) | NN | `draft` | IDX | I | Workflow publikasi |
| `visibility` | VARCHAR(64) | NN | `internal` | IDX | I | Visibility |
| `published_at`, `scheduled_at` | DATETIME | N | `NULL` | IDX | S | Waktu publikasi |
| `view_count` | BIGINT UNSIGNED | NN | `0` | — | S | Counter terkendali |
| `author_id`, `reviewed_by`, `published_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor metadata |
| `created_at`, `updated_at`, `deleted_at` | DATETIME | Sesuai | — | — | S | Timestamp |

Constraint: `UNIQUE(slug)`, `INDEX(category_id, workflow_status, visibility, published_at)`.

## `events`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier event |
| `title` | VARCHAR(255) | NN | — | — | P/I | Judul event |
| `slug` | VARCHAR(255) | NN | — | UK | P/I | Slug unik |
| `type` | VARCHAR(64) | NN | — | — | I | Webinar/sosialisasi/workshop/pelatihan/lainnya |
| `mode` | VARCHAR(32) | NN | — | — | I | `online`, `offline`, `hybrid` |
| `status` | VARCHAR(64) | NN | `draft` | IDX | I | `draft`, `coming_soon`, `open_registration`, `full`, `finished`, `cancelled` |
| `visibility` | VARCHAR(64) | NN | `internal` | IDX | I | Visibility |
| `summary`, `description` | TEXT/LONGTEXT | N | `NULL` | — | P/I | Konten event |
| `cover_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | I | Cover |
| `start_at`, `end_at` | DATETIME | NN | — | IDX | S | Waktu event; `end_at >= start_at` |
| `location` | VARCHAR(500) | N | `NULL` | — | I | Lokasi offline |
| `meeting_url` | VARCHAR(2048) | N | `NULL` | — | R | URL online tervalidasi |
| `registration_open_at`, `registration_close_at` | DATETIME | N | `NULL` | IDX | S | Periode daftar |
| `capacity` | INT UNSIGNED | N | `NULL` | — | I | Kuota |
| `waitlist_enabled` | TINYINT(1) | NN | `0` | — | S | Waitlist |
| `certificate_enabled` | TINYINT(1) | NN | `0` | — | S | Sertifikat event |
| `created_by`, `updated_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor metadata |
| `cancelled_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor cancel |
| `cancelled_at` | DATETIME | N | `NULL` | — | S | Waktu cancel |
| `cancellation_reason` | TEXT | N | `NULL` | — | I | Alasan cancel |
| `created_at`, `updated_at`, `deleted_at` | DATETIME | Sesuai | — | — | S | Timestamp |

Constraint/index: `UNIQUE(slug)`, `INDEX(status, visibility, start_at)`, `INDEX(registration_open_at, registration_close_at)`.

## `event_registrations`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier registrasi |
| `event_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Event |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Pendaftar |
| `status` | VARCHAR(64) | NN | `registered` | IDX | I | `registered`, `waitlisted`, `cancelled`, `rejected` |
| `registered_at` | DATETIME | NN | — | — | S | Waktu daftar |
| `cancelled_at` | DATETIME | N | `NULL` | — | S | Waktu cancel |
| `promoted_at` | DATETIME | N | `NULL` | — | S | Waktu promosi waitlist |
| `status_reason` | TEXT | N | `NULL` | — | I | Alasan status |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint: `UNIQUE(event_id, user_id)`.

## `attendances`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `event_registration_id` | BIGINT UNSIGNED | NN | — | FK, UK | S | Registrasi event |
| `status` | VARCHAR(64) | NN | — | — | I | `present`, `absent`, `excused` |
| `recorded_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor pencatat |
| `recorded_at` | DATETIME | NN | — | — | S | Waktu pencatatan |
| `notes` | TEXT | N | `NULL` | — | I | Catatan aman |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

---

# Sertifikat Digital

## `certificate_templates`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier template |
| `name` | VARCHAR(255) | NN | — | — | I | Nama template |
| `source_type` | VARCHAR(64) | NN | — | IDX | S | `learning` atau `event` |
| `template_payload` | JSON | NN | — | — | I | Data layout aman |
| `background_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | I | Background template |
| `is_active` | TINYINT(1) | NN | `1` | IDX | S | Status template |
| `created_by`, `updated_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor metadata |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `certificates`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier sertifikat |
| `source_type` | VARCHAR(64) | NN | — | UK scope | S | `learning_enrollment` atau `event_registration` |
| `source_id` | BIGINT UNSIGNED | NN | — | UK scope | S | ID sumber |
| `user_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Pemilik sertifikat |
| `template_id` | BIGINT UNSIGNED | N | `NULL` | FK | S | Template digunakan |
| `certificate_number` | VARCHAR(128) | NN | — | UK | P/I | Nomor sertifikat unik |
| `verification_token_hash` | CHAR(64) | NN | — | UK | R | Hash token verifikasi |
| `status` | VARCHAR(64) | NN | `pending` | IDX | I | `pending`, `issued`, `revoked`, `expired` |
| `issued_at`, `expires_at`, `revoked_at` | DATETIME | N | `NULL` | IDX | S | Lifecycle sertifikat |
| `revoked_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor revoke |
| `revocation_reason` | TEXT | N | `NULL` | — | I | Alasan cabut |
| `pdf_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | R | PDF sertifikat private |
| `replaced_by_certificate_id` | BIGINT UNSIGNED | N | `NULL` | FK self | S | Sertifikat pengganti |
| `issued_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Issuer/system actor |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

Constraint:

```text
UNIQUE(certificate_number)
UNIQUE(verification_token_hash)
UNIQUE(source_type, source_id)
```

---

# Government Intelligence PBJ

## `competencies`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier kompetensi |
| `key` | VARCHAR(64) | NN | — | UK | S | Kode kompetensi |
| `name` | VARCHAR(255) | NN | — | — | I | Nama kompetensi |
| `description` | TEXT | N | `NULL` | — | I | Deskripsi |
| `is_active` | TINYINT(1) | NN | `1` | — | S | Status aktif |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `jf_ppbj_levels`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier level |
| `key` | VARCHAR(64) | NN | — | UK | S | `PERTAMA`, `MUDA`, `MADYA`, `UTAMA` |
| `name` | VARCHAR(255) | NN | — | — | I | Label level |
| `sort_order` | INT UNSIGNED | NN | `0` | — | S | Urutan |
| `is_active` | TINYINT(1) | NN | `1` | — | S | Status |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `jf_ppbj_records`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier record internal |
| `user_id` | BIGINT UNSIGNED | N | `NULL` | FK, IDX | S | Keterkaitan account bila ada |
| `organization_unit_id` | BIGINT UNSIGNED | NN | — | FK, IDX | I | Unit organisasi |
| `employee_identifier` | VARCHAR(128) | NN | — | — | R | NIP/identifier SDM |
| `name_snapshot` | VARCHAR(255) | NN | — | — | R | Nama snapshot |
| `level_id` | BIGINT UNSIGNED | NN | — | FK | I | Level JF PPBJ |
| `status` | VARCHAR(64) | NN | `active` | IDX | I | Status record terkendali |
| `effective_at` | DATETIME | NN | — | IDX | S | Mulai berlaku |
| `ended_at` | DATETIME | N | `NULL` | IDX | S | Akhir berlaku |
| `change_type` | VARCHAR(64) | NN | — | I | `addition`, `promotion`, `transfer_in`, `transfer_out`, `retirement`, `deactivation`, `update` |
| `evidence_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | R | Bukti restricted |
| `created_by`, `updated_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Actor metadata |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `competency_certificate_types`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier jenis |
| `key` | VARCHAR(64) | NN | — | UK | S | `PBJ_LEVEL_1`, `PPK_TYPE_A`, `PPK_TYPE_B`, `PPK_TYPE_C`, atau nilai terkendali lain |
| `name` | VARCHAR(255) | NN | — | — | I | Label |
| `description` | TEXT | N | `NULL` | — | I | Deskripsi |
| `is_active` | TINYINT(1) | NN | `1` | — | S | Status |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `competency_certificates`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier |
| `jf_ppbj_record_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Record JF PPBJ |
| `certificate_type_id` | BIGINT UNSIGNED | NN | — | FK, IDX | S | Jenis sertifikat kompetensi |
| `certificate_number` | VARCHAR(128) | N | `NULL` | — | R | Nomor sertifikat kompetensi |
| `issued_at`, `expires_at` | DATETIME | N | `NULL` | IDX | S | Masa berlaku |
| `status` | VARCHAR(64) | NN | `pending` | IDX | I | `pending`, `verified`, `rejected`, `expired` |
| `evidence_file_id` | BIGINT UNSIGNED | N | `NULL` | FK | R | Bukti restricted |
| `verified_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Verifikator |
| `verified_at` | DATETIME | N | `NULL` | — | S | Waktu verifikasi |
| `created_at`, `updated_at` | DATETIME | NN | — | — | S | Timestamp |

## `competency_gap_snapshots`

| Kolom | Tipe | Null | Default | Key | Klasifikasi | Definisi/validasi |
|---|---|---:|---|---|---|---|
| `id` | BIGINT UNSIGNED | NN | AI | PK | S | Identifier snapshot |
| `organization_unit_id` | BIGINT UNSIGNED | NN | — | FK | I | Unit organisasi |
| `competency_id` | BIGINT UNSIGNED | N | `NULL` | FK | I | Kompetensi bila dipisahkan per kompetensi |
| `period_date` | DATE | NN | — | UK scope | S | Tanggal/periode snapshot |
| `required_count` | INT UNSIGNED | NN | `0` | — | I | Kebutuhan |
| `available_count` | INT UNSIGNED | NN | `0` | — | I | Ketersediaan |
| `gap_count` | INT | NN | `0` | — | I | `required_count - available_count` |
| `metadata` | JSON | N | `NULL` | — | I | Agregasi aman |
| `generated_by` | BIGINT UNSIGNED | N | `NULL` | FK | S | Generator user/system |
| `created_at` | DATETIME | NN | — | — | S | Waktu generate |

Constraint: `UNIQUE(organization_unit_id, competency_id, period_date)`.

---

# Foreign Key Reference

Foreign key utama menggunakan `BIGINT UNSIGNED` dan index pada kolom FK. Aksi delete mengikuti database blueprint: restrict/soft delete/archive untuk histori penting, `SET NULL` pada actor metadata bila audit/riwayat harus dipertahankan, dan hard delete hanya mengikuti retention policy.

| Tabel anak | Kolom FK | Tabel induk |
|---|---|---|
| `user_profiles` | `user_id` | `users` |
| Profil spesifik | `user_id` | `users` |
| `user_roles` | `user_id`, `role_id` | `users`, `roles` |
| `role_permissions` | `role_id`, `permission_id` | `roles`, `permissions` |
| Token/request/verifikasi | user/reviewer ID | `users` |
| `organization_units` | `parent_id` | `organization_units` |
| `user_procurement_roles` | user/role/file ID | `users`, `procurement_roles`, `file_assets` |
| Shared governance | actor/file/user ID | `users`, `file_assets` |
| Learning | program/course/class/module/lesson/enrollment/user/file ID | Domain induk masing-masing |
| Knowledge | category/tag/article/media/file/user ID | Domain induk masing-masing |
| Regulation | category/regulation/version/file/user ID | Domain induk masing-masing |
| Communication | category/event/registration/user/file ID | Domain induk masing-masing |
| Certificate | template/user/file/self replacement ID | Certificate/domain induk |
| Intelligence | organization/level/competency/certificate type/file/user ID | Domain induk masing-masing |

---

# Privacy, Audit, dan Retention

## Kolom restricted utama

```text
users.password_hash
users.phone
users.email
asn_profiles.employee_identifier
provider_profiles.business_identifier
public_profiles.national_identifier
profile_change_requests.requested_payload
email_verifications.token_hash
password_resets.token_hash
file_assets.path
file_assets.metadata bila memuat informasi sensitif
quiz_options.is_correct
quiz_answers.answer_payload
quiz_attempts.score
assignment_submissions.content/score/feedback
certificate verification_token_hash dan pdf_file_id
jf_ppbj_records.employee_identifier/name_snapshot/evidence_file_id
competency_certificates.certificate_number/evidence_file_id
audit snapshot dan security context
```

## Retention baseline

| Data | Aturan baseline |
|---|---|
| Audit log | Append-only secara fungsional; retensi sesuai audit/security policy |
| Account/profile | Soft delete/archive sesuai policy; historis workflow dipertahankan |
| Token verifikasi/reset | Expire/used record dibersihkan sesuai policy tanpa menyimpan plain token |
| Temporary file/export | TTL dan cleanup terjadwal |
| Assignment/assessment | Dipertahankan sesuai policy pembelajaran/audit |
| Regulasi | Riwayat dan versi dipertahankan; revoked/expired tidak otomatis dihapus |
| Certificate | Revocation/replacement tidak menghapus riwayat sertifikat |
| Intelligence restricted data | Retensi sesuai kebutuhan administrasi, privacy, dan legal basis |

---

# Checklist Implementasi

- [ ] Semua tabel menggunakan PK/FK, index, dan constraint yang dicantumkan.
- [ ] Semua status mengikuti controlled value pada Auth/RBAC dan Workflow Contract.
- [ ] Email, username, token hash, slug, nomor sertifikat, dan pivot relation memiliki unique constraint sesuai dokumen.
- [ ] Password dan token hanya disimpan sebagai hash.
- [ ] Data restricted diberi klasifikasi dan tidak terekspos melalui query/export tanpa authorization.
- [ ] File disimpan di storage; tabel hanya menyimpan metadata/relasi `file_assets`.
- [ ] Soft delete/archive digunakan untuk record yang membutuhkan riwayat.
- [ ] Audit snapshot melewati redaction.
- [ ] Migration, model, validation, service, ERD, dan database test diperbarui bersama jika data dictionary berubah.

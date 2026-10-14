# BegawiPengadaan — Documentation and Database File Register

## Tujuan

Dokumen ini adalah register resmi untuk dokumentasi proyek dan artefak database BegawiPengadaan. Register mencatat path, jenis, status, sumber kebenaran, fungsi, dependency, dan aturan perubahan setiap dokumen atau file database yang menjadi bagian kontrak proyek.

Dokumen ini tidak menggantikan isi dokumen yang diregistrasi. Untuk aturan substantif, gunakan dokumen sumber yang tercantum pada register.

---

## Prinsip Register

- Setiap dokumen kontrak dan artefak database resmi harus tercatat di register ini.
- Satu file memiliki satu fungsi utama dan satu path kanonis.
- Dokumen tidak boleh diduplikasi pada path lain tanpa alasan, status, dan aturan sinkronisasi yang eksplisit.
- Perubahan file yang berdampak pada arsitektur, route, database, workflow, privacy, atau storage harus memperbarui dokumen terkait dan register ini.
- Status “Sudah dibuat” hanya berarti artefak dokumentasi tersedia; status implementasi kode/database dicatat terpisah pada `dokumen/implementation-status.md`.
- Dokumen yang belum dibuat dapat dicatat sebagai target, tetapi tidak boleh dipakai sebagai sumber aturan sebelum tersedia.

---

## Status Register

| Status | Arti |
|---|---|
| `Sudah dibuat` | File dokumentasi/artefak tersedia dan menjadi bagian baseline proyek |
| `Belum dibuat` | Path telah direncanakan, tetapi file belum tersedia dan tidak boleh dijadikan sumber aturan |
| `Deprecated` | File tidak lagi menjadi sumber kebenaran; hanya disimpan sementara untuk migrasi/histori bila diperlukan |
| `Archived` | File historis yang tidak lagi aktif, tetapi dipertahankan sesuai retention/documentation policy |

---

## Hierarki Sumber Kebenaran

| Prioritas | Artefak | Fungsi |
|---:|---|---|
| 1 | `README.md` / `dokumen/README.md` | Acuan utama scope, arsitektur, boundary, domain, security, dan arah implementasi |
| 2 | `dokumen/directory-tree.md` | Struktur target direktori dan file final |
| 3 | Dokumen Arsitektur (`dokumen/route-contract.md`, `ui-boundary.md`, dll.) | Kontrak implementasi antarmuka, route, auth/RBAC, workflow, dan storage |
| 4 | Dokumen Database (`dokumen/database-blueprint.md`, `schema-fresh-v4-1.sql`, dll.) | Kontrak struktur database, relasi, kolom, dan schema fresh (74 tabel) |
| 5 | Dokumen Domain (`dokumen/registration-policy.md`, `regulation-policy.md`, dll.) | Policy bisnis/domain yang detail |
| 6 | Dokumen Project (`dokumen/documentation-and-database-file-register.md`, `implementation-status.md`) | Register dokumentasi dan status implementasi |

Jika terjadi konflik, perubahan tidak boleh diputuskan hanya dengan memilih satu dokumen. Dokumen pada level yang terdampak harus diperbarui bersama sehingga kembali konsisten.

---

## Register Dokumen Utama

| No. | Path kanonis | Jenis | Status | Fungsi utama | Dependency utama |
|---:|---|---|---|---|---|
| 1 | `README.md` | Markdown | Sudah dibuat | Acuan utama arsitektur, scope, domain, access control, privacy, security, dan tahapan implementasi | — |
| 2 | `directory-tree.md` | Markdown | Sudah dibuat | Struktur target seluruh direktori/file proyek | `README.md` |

### Aturan perubahan

| Jika berubah | Dokumen minimal yang diperbarui |
|---|---|
| Scope/domain utama | `README.md` / `dokumen/README.md`, `dokumen/directory-tree.md`, policy domain terkait |
| Boundary UI/controller/view/asset | `README.md`, `dokumen/ui-boundary.md`, `dokumen/route-contract.md` |
| Path file/folder target | `dokumen/directory-tree.md`, register ini, dokumen yang merujuk path tersebut |
| Status implementasi modul | `dokumen/implementation-status.md` |

---

## Register Arsitektur

| No. | Path kanonis | Jenis | Status | Fungsi utama | Bergantung pada |
|---:|---|---|---|---|---|
| 3 | `dokumen/route-contract.md` | Markdown | Sudah dibuat | Route HTTP, method, controller, filter, permission, route name, view/response | README, directory tree |
| 4 | `dokumen/ui-boundary.md` | Markdown | Sudah dibuat | Boundary Landing/Public, Auth, Workspace, Dashboard, Error, Reference, layout, dan asset | README, directory tree, route contract |
| 5 | `dokumen/auth-and-rbac.md` | Markdown | Sudah dibuat | Auth, session, account status, role, permission, filter, ownership, assignment, redirect, audit akses | README, directory tree, route contract, UI boundary |
| 6 | `dokumen/workflow-contract.md` | Markdown | Sudah dibuat | State machine, transisi, actor, approval, side effect, audit, dan lifecycle domain | README, route contract, auth/RBAC |
| 7 | `dokumen/file-storage-policy.md` | Markdown | Sudah dibuat | Storage root, upload, file lifecycle, visibility, preview/download, export, retention, audit | README, directory tree, workflow, auth/RBAC |

### Aturan perubahan arsitektur

| Perubahan | Dokumen yang diperbarui |
|---|---|
| Path/method/route name/filter/permission route | `route-contract.md`, `auth-and-rbac.md` bila akses berubah, register ini |
| Layout/template/view/asset boundary | `ui-boundary.md`, `directory-tree.md`, `route-contract.md` bila route/view berubah |
| Account status/role/permission/session | `auth-and-rbac.md`, `workflow-contract.md`, database dokumen terkait |
| Status workflow/transisi/approval | `workflow-contract.md`, policy domain terkait, database dokumen terkait |
| File visibility/lifecycle/access | `file-storage-policy.md`, policy domain terkait, database dokumen terkait |

---

## Register Database

| No. | Path kanonis | Jenis | Status | Fungsi utama | Bergantung pada |
|---:|---|---|---|---|---|
| 8 | `dokumen/database-blueprint.md` | Markdown | Sudah dibuat | Tabel, PK/FK, status, constraint, index, migration order, seeder strategy | Architecture contracts |
| 9 | `dokumen/database-erd.md` | Markdown | Sudah dibuat | ERD konseptual dan relasional serta kardinalitas | Database blueprint |
| 10 | `dokumen/data-dictionary.md` | Markdown | Sudah dibuat | Definisi tabel/kolom, tipe, nullability, default, klasifikasi data, validasi | Blueprint, ERD, domain/architecture contracts |
| 11 | `dokumen/schema-fresh-v4-1.sql` | SQL | Sudah dibuat | Schema fresh executable V4.1 (74 tabel, 1:1 match live database): create table, index, constraint, foreign key | Blueprint, ERD, data dictionary |

### Tabel master wilayah

| Artefak | Path | Status | Fungsi |
|---|---|---|---|
| Struktur tabel master wilayah | `dokumen/schema-fresh-v4-1.sql` | Sudah dibuat | Membuat tabel `wilayah` tanpa data seed |
| Data master wilayah resmi | `database/reference/wilayah.sql` | Required external import | Mengisi tabel `wilayah` setelah schema dibuat |

Kontrak `wilayah`:

```sql
CREATE TABLE wilayah (
    kode VARCHAR(13) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    PRIMARY KEY (kode),
    KEY wilayah_name_idx (nama)
) ENGINE=InnoDB;
```

Aturan:

- `schema-fresh-v4-1.sql` membuat tabel `wilayah`.
- `schema-fresh-v4-1.sql` tidak mengisi data master wilayah.
- `database/reference/wilayah.sql` diimpor manual setelah schema fresh dijalankan.
- Aplikasi memperlakukan data `wilayah` sebagai master read-only.
- `user_profiles.province_code`, `regency_code`, `district_code`, dan `village_code` mengacu ke `wilayah.kode`.

### Urutan penggunaan database

```text
1. Buat database kosong dengan charset utf8mb4.
2. Jalankan dokumen/schema-fresh-v4-1.sql.
3. Import database/reference/wilayah.sql secara manual.
4. Jalankan migration/seed sesuai environment ketika implementasi tersedia.
5. Jalankan database test untuk constraint, FK, index, dan lifecycle kritis.
```

### Aturan perubahan database

| Perubahan | Dokumen/artifak yang diperbarui |
|---|---|
| Tabel/kolom/tipe/null/default | Blueprint, ERD, data dictionary, schema fresh, migration terkait, register ini |
| FK/constraint/index | Blueprint, ERD, data dictionary, schema fresh, database test |
| Enum/status | Auth/RBAC atau workflow contract, blueprint, data dictionary, schema fresh, policy domain |
| Klasifikasi data/retention | Privacy policy, data dictionary, file storage policy bila file terkait |
| Master wilayah | Schema fresh dan dokumentasi import; tidak membuat seeder dummy untuk data resmi |

---

## Register Domain

| No. | Path kanonis | Jenis | Status | Fungsi utama | Bergantung pada |
|---:|---|---|---|---|---|
| 12 | `dokumen/registration-policy.md` | Markdown | Sudah dibuat | Registrasi, tipe profil, verifikasi, wilayah, profile change, role request | Auth/RBAC, workflow, storage, database |
| 13 | `dokumen/assessment-and-certification-rules.md` | Markdown | Sudah dibuat | Enrollment, assessment, grading, final score, eligibility, certificate issue/revoke/verification | Workflow, storage, route, database |
| 14 | `dokumen/regulation-policy.md` | Markdown | Sudah dibuat | Metadata regulasi, legal status, workflow, versioning, relation, file, preview/download | Workflow, storage, privacy, database |
| 15 | `dokumen/privacy-and-data-classification.md` | Markdown | Sudah dibuat | Klasifikasi P/I/R/S, masking, disclosure, logging, export, retention, incident | Auth/RBAC, storage, database |
| 16 | `dokumen/public-statistics-policy.md` | Markdown | Sudah dibuat | Indikator publik, agregasi, suppression, threshold, disclosure control | Privacy policy, database, route/UI boundary |

### Aturan perubahan domain

| Perubahan | Dokumen yang diperbarui |
|---|---|
| Field registrasi/profile/verifikasi | Registration policy, data dictionary, blueprint, ERD, schema fresh, route/auth bila relevan |
| Assessment/nilai/eligibility sertifikat | Assessment rules, workflow, blueprint, ERD, data dictionary, schema fresh, route contract bila relevan |
| Regulasi/legal relation/version/file | Regulation policy, workflow, storage, database dokumen, route/UI bila relevan |
| Privacy/masking/export/statistik | Privacy policy, public statistics policy, data dictionary, storage, route/UI bila relevan |

---

## Register Project Governance

| No. | Path kanonis | Jenis | Status | Fungsi utama | Bergantung pada |
|---:|---|---|---|---|---|
| 17 | `dokumen/documentation-and-database-file-register.md` | Markdown | Sudah dibuat | Register seluruh dokumentasi kontrak dan artefak database | Semua dokumen/artefak terdaftar |
| 18 | `dokumen/implementation-status.md` | Markdown | Sudah dibuat | Register status Planned/UI Implemented/Backend Ready/Production Ready per area | README, directory tree, seluruh kontrak implementasi |
| 19 | `dokumen/auth-migration-plan.md` | Markdown | Archived | Rencana migrasi auth terpadu (Selesai diimplementasikan) | Auth/RBAC, schema-fresh-v4-1.sql |

`dokumen/directory-tree.md` adalah satu-satunya dokumen kanonis struktur direktori/file target.

---

## Register Source Template

| Artefak | Path | Status | Fungsi | Aturan |
|---|---|---|---|---|
| Reference FiStudy | `app/Views/reference/fistudy/` | Required source reference | Bahan adaptasi Landing/Public/Workspace | Tidak memiliki route produksi; tidak dirender controller produksi |
| Reference WowDash | `app/Views/reference/wowdash/` | Required source reference | Bahan adaptasi Auth/Dashboard | Tidak memiliki route produksi; tidak dirender controller produksi |
| Produksi asset FiStudy | `public/assets/main/` | Target produksi | Asset Landing/Public/Workspace | Hanya asset yang benar-benar digunakan |
| Produksi asset WowDash | `public/assets/dashboard/` | Target produksi | Asset Auth/Dashboard | Hanya asset yang benar-benar digunakan |

File source template tidak menjadi source of truth business rule atau route. Business rule berada pada dokumen kontrak proyek dan implementasi aplikasi.

---

## Register Runtime Storage

| Path | Klasifikasi | Status | Fungsi | Aturan |
|---|---|---|---|---|
| `public/assets/main/uploads-public/` | Public | Target produksi | Konten eksplisit berstatus public | Hanya file `available` + visibility public + resource published bila relevan |
| `writable/uploads/` | Internal/Restricted | Target produksi | File private domain | Tidak memiliki URL publik langsung |
| `writable/uploads/certificates/` | Restricted | Target produksi | PDF sertifikat | Owner/issuer sesuai policy |
| `writable/uploads/knowledge/` | Internal/Restricted | Target produksi | Media/knowledge non-public | Promotion ke public hanya setelah policy publish |
| `writable/uploads/learning/` | Internal | Target produksi | Materi kelas | Enrollment/assignment policy |
| `writable/uploads/profiles/` | Restricted | Target produksi | Evidence profile/verifikasi | Authorization ketat |
| `writable/uploads/regulations/` | Internal/Restricted | Target produksi | File regulasi non-public | File visibility terpisah dari record regulasi |
| `writable/uploads/submissions/` | Restricted | Target produksi | Assignment submission | Owner/evaluator/assignment policy |
| `writable/uploads/temporary/` | System | Target produksi | Upload/proses sementara | TTL dan cleanup |
| `writable/exports/` | Restricted | Target produksi | Export sementara | Owner/scope/permission + expiry |

---

## Baseline File Referensi yang Dilarang

Path berikut tidak menjadi source produksi dan tidak boleh dibuat sebagai alternatif boundary:

```text
app/Controllers/Auth/
app/Controllers/Admin/
app/Controllers/Admin/AdminAuthController.php
app/Views/auth/
app/Views/workspace/
app/Views/main/layouts/auth.php
public/assets/workspace/
```

Lokasi kanonis yang digunakan:

```text
app/Controllers/Dashboard/Auth/
app/Controllers/Dashboard/
app/Views/dashboard/auth/
app/Views/dashboard/layouts/auth.php
app/Views/main/workspace/
public/assets/main/
public/assets/dashboard/
```

---

## Prosedur Perubahan Dokumentasi

### Perubahan minor editorial

Contoh: typo, format tabel, penjelasan tanpa dampak policy.

```text
1. Perbarui dokumen sumber.
2. Perbarui register bila path/status/fungsi file berubah.
3. Tidak mengubah schema/route/code tanpa alasan.
```

### Perubahan kontrak implementasi

Contoh: route baru, status baru, tabel/kolom baru, permission baru, workflow baru, storage visibility baru.

```text
1. Identifikasi dokumen sumber yang terdampak.
2. Perbarui README bila boundary/scope/prinsip berubah.
3. Perbarui directory-tree bila path/file baru atau dipindahkan.
4. Perbarui architecture/domain/database contract terkait.
5. Perbarui schema fresh, migration, model, service, validation, test jika database/implementasi terdampak.
6. Perbarui register ini.
7. Catat status implementasi pada implementation-status.md.
```

### Aturan anti-duplikasi

- Jangan membuat dokumen kedua dengan isi kontrak yang sama pada path berbeda.
- Jangan menyimpan copy manual tanpa status `Archived` atau `Deprecated` bila copy tersebut bukan sumber kebenaran.
- Gunakan tautan/referensi ke path kanonis ketika dokumen lain perlu menunjuk aturan yang sudah ada.

---

## Checklist Register

Sebelum artefak baru dinyatakan resmi, pastikan:

- [ ] Path kanonis telah ditentukan pada `directory-tree.md`.
- [ ] Fungsi artefak tidak menduplikasi dokumen yang sudah ada.
- [ ] Dependency dan dokumen sumber sudah diidentifikasi.
- [ ] Status artefak dicatat di register ini.
- [ ] Jika database terkait, blueprint, ERD, data dictionary, schema fresh, migration, dan test terdampak telah ditinjau.
- [ ] Jika route/akses terkait, route contract, auth/RBAC, UI boundary, workflow, dan test terdampak telah ditinjau.
- [ ] Jika privacy/file terkait, privacy policy dan file storage policy terdampak telah ditinjau.
- [ ] Status implementasi tercatat pada `dokumen/implementation-status.md` ketika file tersebut tersedia.

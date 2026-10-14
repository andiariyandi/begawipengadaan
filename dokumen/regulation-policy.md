# BegawiPBJ — Regulation Policy

## Tujuan

Dokumen ini menetapkan kebijakan pengelolaan Regulasi PBJ: metadata, kategori, lifecycle editorial, status hukum, visibility, versioning, relasi hukum, file, preview, download, bookmark, publikasi, arsip, audit, dan keamanan akses.

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
dokumen/registration-policy.md
dokumen/assessment-and-certification-rules.md
```

---

## Prinsip Regulasi PBJ

- Regulasi adalah repository dokumen rujukan PBJ berbasis metadata, status hukum, versioning, relasi hukum, file, preview, serta download terkontrol.
- Status workflow/editorial berbeda dari status hukum dan tidak boleh dicampur.
- Regulasi yang telah dipublikasikan tetap dapat ditampilkan sebagai referensi historis walaupun status hukumnya telah berubah, dicabut, atau berakhir, selama visibility mengizinkan.
- File regulasi memiliki visibility terpisah dari record regulasi; record published tidak otomatis membuat seluruh lampirannya public.
- Metadata regulasi dapat tetap tersedia ketika file belum tersedia.
- Tidak ada regulasi, file, version, relation, atau status hukum yang dihapus untuk menyembunyikan sejarah legal tanpa dasar policy/retention yang sah.
- Preview/download tidak boleh menggunakan path file langsung sebagai authorization; semua akses melalui FileAccessService dan policy resource.
- Perubahan metadata hukum, legal status, versi, relasi, visibility, publikasi, download, dan file penting harus dapat diaudit.

---

## Boundary dan Akses

### Landing dan Public Area

Repository publik memakai FiStudy:

```text
Controller
app/Controllers/Landing/RegulationPublicController.php

View
app/Views/landing/regulations/

Layout
app/Views/landing/layouts/public.php

Asset
public/assets/main/
```

Route publik:

```text
GET /regulasi
GET /regulasi/{slug}
GET /regulasi/{slug}/versi
GET /regulasi/{slug}/relasi
GET /regulasi/cari
```

### Dashboard Back-office

Administrasi regulasi memakai WowDash:

```text
Controller
app/Controllers/Regulation/
app/Controllers/Dashboard/

View
app/Views/dashboard/regulations/

Layout
app/Views/dashboard/layouts/dashboard.php

Asset
public/assets/dashboard/
```

Route Back-office membutuhkan:

```text
auth
+ verifiedEmail
+ accountStatus:active
+ dashboardAccess
+ permission regulasi sesuai aksi
+ auditContext pada aksi perubahan state
```

### Workspace

Workspace dapat memanfaatkan bookmark regulasi atau akses file sesuai permission, tetapi tidak menjadi area publikasi/administrasi regulasi utama.

```text
Bookmark regulasi
→ Workspace BookmarkController

Preview/download protected
→ Shared FileController + FileAccessService
```

---

## Entitas Regulasi

| Entitas | Tanggung jawab |
|---|---|
| `regulation_categories` | Klasifikasi regulasi |
| `regulations` | Metadata utama, workflow status, legal status, visibility, current version |
| `regulation_versions` | Versi file/dokumen dan changelog |
| `regulation_relations` | Hubungan hukum antarregulasi |
| `regulation_downloads` | Riwayat preview/download regulasi bila dicatat |
| `file_assets` | Metadata file regulasi dan visibility/lifecycle file |
| `bookmarks` | Bookmark user terhadap regulasi |
| `approvals` | Generic approval bila dibutuhkan domain |
| `workflow_transitions` | Riwayat state regulasi |
| `audit_logs` | Audit aksi administratif, file, dan akses yang relevan |

---

## Metadata Regulasi

Metadata utama disimpan pada tabel `regulations`.

| Field | Aturan |
|---|---|
| Kategori | Wajib; mengacu `regulation_categories` aktif |
| Judul | Wajib; jelas dan sesuai dokumen |
| Slug | Wajib; unik dan stabil untuk URL publik |
| Nomor dokumen | Wajib; dipertahankan sesuai sumber resmi |
| Tahun dokumen | Wajib; valid sebagai tahun dokumen |
| Instansi penerbit | Wajib; nama resmi penerbit |
| Tanggal resmi | Opsional bila belum tersedia pada metadata awal |
| Tanggal berlaku | Opsional sesuai dokumen |
| Ringkasan | Opsional tetapi direkomendasikan untuk discoverability |
| Sumber resmi | URL resmi tervalidasi bila tersedia |
| Workflow status | Wajib; lifecycle editorial/publikasi |
| Legal status | Wajib; lifecycle/hubungan hukum |
| Visibility | Wajib; public/internal/restricted |
| Current version | Nullable ketika file versi belum tersedia |

Constraint utama:

```text
UNIQUE(slug)
UNIQUE(document_number, document_year, issuer_name)
```

Sistem harus menghindari duplikasi regulasi. Jika dokumen sama memiliki koreksi metadata atau file baru, gunakan update/versioning yang sah, bukan record duplikat.

---

## Kategori Regulasi

Kategori disimpan pada `regulation_categories`.

Aturan:

- Kategori mempunyai `name`, `slug`, deskripsi, dan `is_active`.
- Slug kategori unik.
- Kategori nonaktif tidak boleh dipilih untuk regulasi baru, tetapi tetap dapat dipertahankan agar record historis tidak rusak.
- Penghapusan kategori yang sudah direferensikan regulasi tidak boleh dilakukan secara hard delete; gunakan `is_active = false` atau policy archival.

---

## Status Workflow Editorial

### Nilai status

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

### Arti status

| Status | Makna | Tampil Public Area |
|---|---|---:|
| `draft` | Metadata/file sedang disiapkan | Tidak |
| `submitted` | Diajukan untuk review | Tidak |
| `in_review` | Sedang diperiksa reviewer | Tidak |
| `approved` | Disetujui, belum dipublikasikan/schedule belum tercapai | Tidak |
| `published` | Dipublikasikan sesuai visibility | Ya, bila visibility `public` |
| `revision_required` | Perlu perbaikan metadata/file/relasi | Tidak |
| `rejected` | Ditolak | Tidak |
| `archived` | Riwayat arsip | Bergantung kebijakan arsip/visibility |

### Transisi workflow

| Dari | Aksi | Ke | Actor minimum | Ketentuan |
|---|---|---|---|---|
| — / `draft` | Buat atau simpan regulasi | `draft` | Admin berizin | Metadata dapat belum lengkap |
| `draft` | Ajukan review | `submitted` | Admin dengan `regulation.create`/`regulation.update` | Metadata minimum tervalidasi |
| `submitted` | Mulai review | `in_review` | Reviewer dengan `regulation.publish.review` | Scope dan policy diperiksa |
| `submitted` / `in_review` | Setujui | `approved` | Reviewer berwenang | Belum public sebelum publish |
| `approved` | Publikasikan | `published` | Actor publish berwenang/System schedule | Visibility, metadata, dan policy file diperiksa |
| `submitted` / `in_review` / `approved` | Minta revisi | `revision_required` | Reviewer berwenang | Alasan wajib |
| `revision_required` | Perbaiki dan ajukan ulang | `submitted` | Owner/Admin berizin | Perubahan tercatat |
| `submitted` / `in_review` / `approved` | Tolak | `rejected` | Reviewer berwenang | Alasan wajib |
| `published` | Arsipkan | `archived` | Admin berwenang | Mengikuti policy arsip/legal retention |
| `archived` | Publikasikan kembali | `published` | Admin berwenang | Visibility/legal metadata diperiksa ulang |

Semua transisi dilakukan melalui service/workflow layer dan dicatat pada `workflow_transitions` serta audit yang relevan.

---

## Status Hukum

Status hukum disimpan pada `regulations.legal_status` dan tidak sama dengan workflow editorial.

### Nilai status hukum

```text
active
amended
revoked
expired
```

| Legal status | Label UI | Makna | Public display bila published/public |
|---|---|---|---:|
| `active` | Berlaku | Regulasi masih berlaku | Ya |
| `amended` | Telah Diubah | Sebagian ketentuan telah diubah oleh regulasi lain | Ya |
| `revoked` | Tidak Berlaku / Dicabut | Regulasi telah dicabut | Ya sebagai referensi historis |
| `expired` | Berakhir | Masa berlaku berakhir | Ya sebagai referensi historis |

### Aturan legal status

- Perubahan legal status tidak otomatis mengubah `workflow_status`.
- Regulasi `published` dengan legal status `amended`, `revoked`, atau `expired` tetap dapat ditampilkan sebagai referensi historis apabila visibility `public`.
- UI wajib menampilkan badge legal status yang jelas pada list dan detail.
- Halaman detail harus menampilkan regulasi terkait yang mengubah, mencabut, menggantikan, atau merujuk bila relasi tersedia.
- Legal status hanya boleh diubah actor berwenang dan wajib memiliki audit reason bila policy mengharuskan.

---

## Visibility Regulasi dan File

### Visibility record regulasi

| Visibility | Public Area | Back-office | Keterangan |
|---|---:|---:|---|
| `public` | Ya jika workflow `published` | Ya sesuai permission | Metadata dapat dirender publik |
| `internal` | Tidak | Ya sesuai permission | Hanya internal/terautentikasi berizin |
| `restricted` | Tidak | Terbatas sesuai permission/scope | Data/file sensitif atau akses terbatas |

### Visibility file regulasi

Visibility file pada `file_assets.visibility` diperiksa secara terpisah.

| Record regulasi | File | Hasil |
|---|---|---|
| `published` + `public` | `public` + `available` | Metadata dan file dapat diakses publik sesuai policy |
| `published` + `public` | `internal`/`restricted` | Metadata publik; preview/download file tidak tersedia untuk publik |
| Non-published | `public` | File tidak boleh dipromosikan sebagai file publik sampai policy publish dipenuhi |
| Semua status | `quarantined`/`rejected`/`deleted` | File tidak tersedia untuk preview/download normal |

### File belum tersedia

Jika regulasi memiliki metadata tetapi tidak memiliki file version tersedia:

```text
Metadata regulasi tetap dapat ditampilkan sesuai visibility.
Status UI: File belum tersedia.
Preview PDF: nonaktif.
Download: nonaktif.
```

---

## Versioning Regulasi

### Prinsip

Setiap perubahan file dokumen regulasi disimpan sebagai record baru pada `regulation_versions`. Record regulasi utama menyimpan metadata hukum/editorial dan pointer ke versi current.

### Field utama versi

```text
regulation_id
version_number
file_id
changelog
is_current
uploaded_by
created_at
```

### Aturan versi

- `version_number` dimulai dari 1 dan unik per regulasi:

```text
UNIQUE(regulation_id, version_number)
```

- Satu regulasi memiliki paling banyak satu current version.
- `current_version_id` pada `regulations` menunjuk ke versi current setelah versi berhasil dibuat/finalized.
- `is_current = true` dijaga melalui transaction/service; database engine dapat memakai partial unique index jika mendukung.
- Version file dapat `NULL` bila metadata versi dicatat sebelum file tersedia, tetapi action preview/download harus nonaktif.
- `changelog` menjelaskan perubahan dokumen/file dari versi sebelumnya.
- Version lama tidak dihapus hanya karena ada current version baru; ia tetap menjadi riwayat.
- Perubahan versi current memerlukan audit, termasuk actor, timestamp, alasan/changelog, dan file relation.

### Current version update transaction

```text
1. Validasi regulation dan actor permission.
2. Simpan/finalisasi file melalui FileStorageService.
3. Buat regulation_versions record baru.
4. Nonaktifkan is_current versi lama bila ada.
5. Aktifkan is_current versi baru.
6. Update regulations.current_version_id.
7. Commit transaction.
8. Tulis workflow/audit event.
```

Apabila transaction gagal, tidak boleh ada lebih dari satu versi current atau pointer current yang mengarah ke versi yang tidak valid.

---

## Relasi Hukum Regulasi

Relasi disimpan pada `regulation_relations` sebagai hubungan terstruktur, bukan sekadar teks bebas.

### Tipe relasi

```text
amends
amended_by
revokes
revoked_by
replaces
replaced_by
implements
implemented_by
references
referenced_by
```

### Aturan relasi

- Source dan target regulasi tidak boleh sama.
- Relasi tidak boleh duplikat:

```text
UNIQUE(source_regulation_id, target_regulation_id, relation_type)
```

- Relasi inverse dapat dibuat otomatis atau dikelola explicit sesuai service policy. Implementasi harus memilih satu strategi dan mencegah relasi inverse duplikat yang tidak konsisten.
- Perubahan legal status dapat dipicu/ditinjau berdasarkan relasi, tetapi tidak boleh terjadi otomatis tanpa policy yang jelas.
- Hubungan hukum harus dapat ditampilkan pada detail regulasi jika visibility kedua resource mengizinkan.
- Catatan relasi (`notes`) tidak boleh memuat data restricted yang tidak perlu.

### Tabel relasi contoh

| Regulasi sumber | Tipe | Regulasi target | Makna |
|---|---|---|---|
| A | `amends` | B | A mengubah B |
| B | `amended_by` | A | B diubah oleh A |
| A | `revokes` | B | A mencabut B |
| B | `revoked_by` | A | B dicabut oleh A |
| A | `replaces` | B | A menggantikan B |
| A | `implements` | B | A mengimplementasikan B |
| A | `references` | B | A merujuk B |

---

## Preview dan Download

### View detail publik

Detail regulasi publik dapat memuat:

```text
Metadata hukum
↓
Badge status hukum
↓
Ringkasan
↓
Keterkaitan regulasi
↓
Versi dokumen current
↓
Preview PDF / Buka di Tab Baru / Download bila file dan akses tersedia
↓
Riwayat versi dan changelog
↓
Regulasi terkait
↓
Bookmark untuk user terautentikasi
```

### Aturan preview

- Preview file memakai route file protected dan `FilePreviewService`.
- File PDF public yang `available` dapat dipreview sesuai policy public.
- File internal/restricted hanya dipreview setelah actor lolos `FileAccessService`.
- File HTML, script, atau format active content tidak dipreview inline tanpa sanitasi/isolasi yang aman.
- Preview tidak boleh melewati visibility file atau workflow record regulasi.

### Aturan download

- Download dilayani melalui `Shared\FileController::download`.
- Server memilih file berdasarkan `file_id`, bukan path dari user.
- Authorization memeriksa lifecycle file, visibility, status regulasi, ownership/scope/permission, dan policy domain.
- Download dapat dicatat pada `regulation_downloads` dengan `access_type` seperti `preview` atau `download`.
- Download publik tidak membutuhkan account bila file benar-benar public; user ID dapat `NULL` pada log.
- Download internal/restricted membutuhkan auth dan permission/scope relevan.

---

## Bookmark Regulasi

User aktif dapat membuat bookmark regulasi melalui Workspace.

Aturan:

```text
UNIQUE(user_id, bookmarkable_type, bookmarkable_id)
```

- Bookmark tidak memberikan hak akses tambahan.
- Bookmark terhadap regulasi yang berubah visibility atau tidak lagi dapat diakses dapat tetap tercatat, tetapi detail/file tidak boleh dirender tanpa policy akses yang lolos.
- Bookmark regulasi memakai `bookmarkable_type` yang di-whitelist, misalnya `regulation`.

---

## Search, Filter, dan Discoverability

### Filter publik minimum

```text
kategori
jenis/instansi penerbit bila tersedia
nomor/tahun
status hukum
topik/kata kunci
periode
tahap PBJ
kompetensi
```

### Aturan search

- Public search hanya menampilkan regulasi `published` dengan visibility `public`.
- Filter legal status tidak boleh menyembunyikan record yang relevan; ia hanya mempersempit hasil berdasarkan status yang dipilih.
- Search query diperlakukan sebagai untrusted input, dibatasi rate limit, dan harus menggunakan query parameterized/full-text policy yang aman.
- Highlight search result tidak boleh menghasilkan HTML tidak tersanitasi.
- Search internal dapat menampilkan record internal/restricted hanya setelah permission/scope diperiksa.

---

## Permission Baseline

| Permission | Fungsi |
|---|---|
| `regulation.view` | Melihat regulasi Back-office/internal sesuai scope |
| `regulation.create` | Membuat regulasi draft |
| `regulation.update` | Mengubah metadata/file regulasi sesuai state/policy |
| `regulation.version.view` | Melihat/kelola riwayat versi sesuai scope |
| `regulation.relation.view` | Melihat/kelola relasi hukum sesuai scope |
| `regulation.publish.review` | Review, approve, reject, request revision, publish sesuai workflow authority |
| `file.preview` | Preview file sesuai FileAccessService |
| `file.download` | Download file sesuai FileAccessService |

Permission tidak menggantikan pemeriksaan workflow status, visibility, file lifecycle, atau scope resource.

---

## Audit dan Notifikasi

### Audit wajib

- Buat/update regulasi dan metadata hukum.
- Submit, review, approve, publish, revision request, reject, archive, dan republish.
- Perubahan legal status.
- Tambah/update current version, changelog, dan file version.
- Tambah/hapus relasi hukum.
- Preview/download regulasi restricted bila policy mengharuskan.
- Perubahan visibility regulasi atau file.
- File tidak tersedia, quarantine, atau delete yang memengaruhi regulasi.

Audit minimal mencatat actor, action, resource, from/to state bila relevan, timestamp, hasil, dan alasan tanpa menyimpan file path private mentah atau content dokumen restricted.

### Notifikasi yang dapat dikirim

- Regulasi submitted untuk review.
- Request revision.
- Approval/rejection.
- Regulasi published/archived.
- Perubahan legal status yang memengaruhi pihak terkait bila policy mengizinkan.
- Kegagalan/hasil upload file regulasi.

Notifikasi hanya memuat informasi minimum aman dan tidak menggantikan sumber kebenaran state regulasi.

---

## Retention dan Arsip

- Regulasi historis tidak dihapus hanya karena legal status `revoked` atau `expired`.
- Version lama, changelog, relation, dan audit dipertahankan sesuai retention/legal policy.
- `archived` menandai lifecycle editorial/arsip dan tidak sama dengan hard delete.
- Soft delete record regulasi hanya dilakukan sesuai policy dan tidak boleh merusak relasi, file, download log, audit, atau kebutuhan historis.
- File regulasi mengikuti file-storage policy; hard delete bytes mempertimbangkan retention, backup, legal hold, dan kebutuhan arsip.

---

## Error dan Edge Case

| Kondisi | Perilaku |
|---|---|
| Slug tidak ditemukan atau record tidak public | Public Area menampilkan 404 |
| Regulasi published tetapi file belum ada | Metadata tampil; preview/download nonaktif |
| File current quarantined/rejected/deleted | Metadata regulasi tetap sesuai visibility; file tidak tersedia |
| Dua request membuat current version bersamaan | Transaction/service mencegah lebih dari satu current version |
| Relasi regulasi dengan source=target | Ditolak constraint/service |
| Actor tanpa scope mengubah legal status | Ditolak authorization dan dapat dicatat sebagai denied action |
| File internal diakses publik | Ditolak dengan 404/403 sesuai policy keamanan |
| Public download tanpa file public/available | Tidak tersedia; tidak mengungkap path private |
| Record archived | Tampilan sesuai policy arsip; tidak kembali ke listing normal tanpa policy republish |

---

## Checklist Implementasi

Sebelum modul Regulasi PBJ dinyatakan `Backend Ready`, pastikan:

- [ ] Metadata regulasi memiliki slug unik dan document uniqueness sesuai blueprint.
- [ ] Workflow editorial dan legal status dipisahkan pada database, service, UI, serta route.
- [ ] Hanya `published` + `public` yang tersedia di Public Area.
- [ ] Legal status amended/revoked/expired tetap dapat tampil sebagai referensi historis bila policy mengizinkan.
- [ ] File visibility/lifecycle diperiksa terpisah dari record regulasi.
- [ ] Metadata tetap dapat tampil ketika file belum tersedia.
- [ ] Versioning menggunakan unique version number, satu current version, transaction, dan changelog.
- [ ] Relasi hukum tervalidasi, tidak self-reference, dan tidak duplikat.
- [ ] Preview/download memakai FileController + FileAccessService, bukan direct path.
- [ ] Bookmark tidak memberi hak akses tambahan.
- [ ] Semua publish/legal status/version/relation/file visibility action tercatat audit.
- [ ] Feature test mencakup public visibility, historical status, version concurrency, relation validation, file access, bookmark, search, archive, dan audit.

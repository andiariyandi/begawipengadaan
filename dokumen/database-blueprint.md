# BegawiPengadaan — Database Blueprint

## Tujuan

Dokumen ini menetapkan blueprint database relasional untuk BegawiPengadaan. Blueprint mencakup kelompok tabel, tanggung jawab data, relasi utama, primary key, foreign key, enum/status, constraint, index, ownership, auditability, privacy, urutan migration, dan strategi seeding.

Dokumen ini melengkapi dan tidak mengubah kontrak yang telah ditetapkan dalam:

```text
README.md
directory-tree.md
dokumen/route-contract.md
dokumen/ui-boundary.md
dokumen/auth-and-rbac.md
dokumen/workflow-contract.md
dokumen/file-storage-policy.md
```

Dokumen ini menjadi dasar implementasi migration, model, entity, service, validation, seeder, database test, ERD, dan data dictionary.

---

## Prinsip Database

- Database bersifat relasional dan menjadi sumber kebenaran persistence domain.
- Primary key internal menggunakan `BIGINT UNSIGNED` auto-increment atau strategi UUID yang konsisten; blueprint ini menggunakan `BIGINT UNSIGNED` sebagai baseline.
- Foreign key dipakai untuk menjaga integritas relasi kritis; aksi `ON DELETE` disesuaikan dengan kebutuhan historis/audit.
- Record domain penting menggunakan soft delete atau archival state bila riwayat perlu dipertahankan.
- Password hash, token hash, secret, dan data restricted tidak boleh diduplikasi secara tidak perlu.
- Semua kolom status memakai enum/check constraint atau controlled string yang divalidasi pada migration/model/service.
- Tabel pivot harus memiliki unique constraint untuk mencegah relasi ganda yang tidak sah.
- Query yang digunakan untuk filter, foreign key, lookup status, slug, token hash, dan report membutuhkan index yang sesuai.
- Audit log bersifat append-only secara fungsional; perubahan/penghapusan audit dibatasi secara ketat.
- File disimpan pada storage; database menyimpan metadata dan hubungan resource, bukan byte file utama.
- Data public, internal, dan restricted harus dapat dibedakan pada level tabel/kolom/visibility serta policy akses.
- Waktu menggunakan UTC pada database; presentasi zona waktu dilakukan pada application layer.

---

## Konvensi Umum

### Tipe kolom baseline

| Kebutuhan | Baseline |
|---|---|
| Primary key | `BIGINT UNSIGNED` |
| Foreign key | `BIGINT UNSIGNED` dengan index |
| UUID/token public | `CHAR(36)` untuk UUID bila digunakan; token disimpan sebagai hash `CHAR(64)`/sesuai algoritma |
| Status/key teknis | `VARCHAR(64)` dengan controlled values/check constraint |
| Slug | `VARCHAR(255)` dengan unique/index sesuai scope |
| Judul/nama | `VARCHAR(255)` |
| Ringkasan | `TEXT` |
| Konten panjang | `LONGTEXT` |
| Metadata fleksibel | `JSON` bila relasi normalisasi tidak diperlukan |
| Waktu | `DATETIME` UTC |
| Boolean | `TINYINT(1)` |
| Nilai/skor | `DECIMAL(5,2)` atau `DECIMAL(6,2)` sesuai kebutuhan |
| File size | `BIGINT UNSIGNED` |

### Kolom audit baseline

Tabel domain yang relevan menggunakan kombinasi berikut sesuai kebutuhan:

```text
created_at
updated_at
deleted_at
created_by
updated_by
published_by
approved_by
archived_by
```

Kolom actor tidak boleh menggantikan `audit_logs`; keduanya digunakan untuk kebutuhan berbeda.

### Penamaan

```text
Tabel           : snake_case plural
Kolom           : snake_case
Foreign key     : {singular}_id
Pivot table     : {entity_a}_{entity_b} dengan urutan stabil
Status teknis   : snake_case
Permission key  : dot.notation pada data reference
```

---

## Skema Database dan Urutan Migration

Database `db_begawipbj` mencakup **89 tabel relasional** yang sepenuhnya tersinkronisasi pada `schema-fresh-v4-3.sql` dan dikelola melalui 16 migration aktif di `app/Database/Migrations/`:

| Urutan | File Migration | Cakupan & Penambahan Struktur |
|---:|---|---|
| 1 | `2026-10-05-000001_AddProcurementStagesAndPivotTables` | Tabel `procurement_stages`, pivot `regulation_procurement_stages`, dan `regulation_competencies` |
| 2 | `2026-10-09-000001_AddCertificateSequencesTable` | Tabel sequence penomoran atomik `certificate_sequences` |
| 3 | `2026-10-10-000001_AddAttendanceCodeToEvents` | Kolom kode presensi dinamis pada `events` |
| 4 | `2026-10-11-000001_AddLearningEnhancementFields` | Kolom pengayaan visual, cover, spotlight pada `courses` dan `learning_classes` |
| 5 | `2026-10-12-000001_AddKnowledgeEnhancementFields` | Kolom pengayaan spotlight, status review pada artikel dan media pengetahuan |
| 6 | `2026-10-12-000002_AddRegulationEnhancementFields` | Kolom spotlight, relasi hierarki pada modul regulasi |
| 7 | `2026-10-14-000001_AddNewsEnhancementFields` | Kolom slider hero, hit counter, tag pada modul warta berita |
| 8 | `2026-10-14-000002_CreateVisitorStatisticsTable` | Tabel analitik kunjungan `visitor_statistics` |
| 9 | `2026-10-18-000001_CreateSatisfactionSurveysTable` | Tabel isian instrumen survei `satisfaction_surveys` dan `survey_aspects` |
| 10 | `2026-10-19-000001_AddDurationMinutesToQuizzes` | Kolom alokasi durasi pengerjaan kuis pada `quizzes` |
| 11 | `2026-10-19-000002_CreateLandingCmsTables` | Tabel CMS Landing: `home_sliders`, `page_banners`, `site_announcements`, `feature_highlights`, `external_portals`, `testimonials`, `team_members`, `service_categories`, `site_settings` |
| 12 | `2026-10-19-000003_AddClassCoverAndSpotlightFields` | Kolom `cover_image`, `is_featured`, `is_carousel`, `carousel_order` pada `learning_classes` |
| 13 | `2026-10-19-000004_AddKnowledgeMediaSlidesAndSpotlightFields` | Kolom slider hero, carousel order pada media galeri dan artikel |
| 14 | `2026-10-19-000005_AddRegulationCarouselFields` | Kolom `is_carousel`, `carousel_order` pada `regulations` |
| 15 | `2026-10-19-000006_AddNewsAndEventCarouselFields` | Kolom `is_carousel`, `carousel_order` pada `news` dan `events` |
| 16 | `2026-10-19-000007_AddLearningCarouselFields` | Kolom `is_carousel`, `carousel_order` pada `programs` dan `courses` |

Schema executable mandiri V4.3 berada di root: `schema-fresh-v4-3.sql` dan merefleksikan seluruh 89 tabel lengkap beserta constraint dan index.

---

## Identity dan RBAC

### `users`

Sumber identitas autentikasi utama.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `name` | Nama lengkap display user |
| `email` | Email unik, normalisasi lowercase |
| `username` | Username unik |
| `password_hash` | Hash password; tidak pernah plaintext |
| `phone` | Nomor telepon; restricted |
| `profile_type` | `asn`, `provider`, `academic`, `public` |
| `account_status` | Enum status account dari Auth/RBAC contract |
| `email_verified_at` | Waktu verifikasi email |
| `last_login_at` | Waktu login terakhir bila diperlukan |
| `session_version` | Marker invalidasi session |
| `status_reason` | Alasan aman untuk status tertentu bila diperlukan |
| `created_at`, `updated_at`, `deleted_at` | Audit timestamp |

Constraint/index:

```text
UNIQUE(email)
UNIQUE(username)
INDEX(account_status)
INDEX(profile_type)
INDEX(email_verified_at)
INDEX(deleted_at)
```

`email`, `phone`, identifier profile, dan status reason diperlakukan sesuai klasifikasi data/akses.

### `user_profiles`

Profil dasar yang bersifat lintas tipe.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK ke users, unique untuk satu profil dasar per user |
| `address_line` | Alamat ringkas; restricted/internal sesuai policy |
| `province_code` | Kode master wilayah |
| `regency_code` | Kode master wilayah |
| `district_code` | Kode master wilayah |
| `village_code` | Kode master wilayah, nullable |
| `profile_completion_status` | Status kelengkapan profil bila diperlukan |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:

```text
UNIQUE(user_id)
INDEX(province_code, regency_code, district_code)
```

### Tabel profil spesifik

| Tabel | Relasi | Data utama |
|---|---|---|
| `asn_profiles` | `user_id` unique | NIP/NIPPK, status kepegawaian, asal instansi, OPD/unit, jabatan, pangkat, email kedinasan |
| `provider_profiles` | `user_id` unique | Nama badan usaha, bentuk usaha, bidang usaha, NIB, jabatan pendaftar, penanggung jawab, kontak/alamat usaha |
| `academic_profiles` | `user_id` unique | Institusi, jenis institusi, jabatan/peran, bidang keahlian, email institusi, identifier profesi/portofolio bila ada |
| `public_profiles` | `user_id` unique | Pekerjaan/status, organisasi, bidang minat PBJ, tujuan penggunaan |

Kolom NIP/NIPPK, NIK, NIB, kontak personal, dan dokumen bukti terkait adalah restricted. Identifier unik hanya diberi unique constraint jika kebijakan bisnis benar-benar mensyaratkannya dan data quality memungkinkan.

### `roles`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `key` | Role teknis unique, misalnya `participant` |
| `name` | Label UI, misalnya Participant |
| `description` | Deskripsi role |
| `is_system` | Role sistem yang tidak boleh dihapus sembarangan |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(key)
```

### `permissions`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `key` | Permission dot notation unique |
| `name` | Label UI |
| `domain` | Domain permission, misalnya learning/regulation |
| `description` | Deskripsi izin |
| `is_system` | Penanda permission sistem |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(key)
INDEX(domain)
```

### `user_roles`

Relasi role terhadap user dan riwayat grant/revoke.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK users |
| `role_id` | FK roles |
| `status` | `active`, `revoked`, `expired` bila lifecycle assignment dibutuhkan |
| `granted_by` | FK users nullable untuk system grant |
| `granted_at` | Waktu grant |
| `revoked_by` | FK users nullable |
| `revoked_at` | Waktu revoke |
| `reason` | Alasan grant/revoke bila diperlukan |

Constraint/index:

```text
UNIQUE(user_id, role_id)
INDEX(role_id, status)
INDEX(user_id, status)
```

### `role_permissions`

| Kolom utama | Keterangan |
|---|---|
| `role_id` | FK roles |
| `permission_id` | FK permissions |
| `granted_by` | Actor pemberi/pengubah bila diperlukan |
| `created_at` | Timestamp |

Constraint:

```text
PRIMARY KEY(role_id, permission_id)
```

### `email_verifications`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK users |
| `token_hash` | Hash token verifikasi; unique |
| `expires_at` | Kedaluwarsa |
| `verified_at` | Waktu digunakan |
| `created_at` | Timestamp |

Constraint/index:

```text
UNIQUE(token_hash)
INDEX(user_id, expires_at)
```

### `password_resets`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK users |
| `token_hash` | Hash token reset; unique |
| `expires_at` | Kedaluwarsa |
| `used_at` | Waktu penggunaan |
| `created_at` | Timestamp |

Constraint/index:

```text
UNIQUE(token_hash)
INDEX(user_id, expires_at)
```

### `role_requests`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK users sebagai requester |
| `requested_role_id` | FK roles; hanya `trainer`/`evaluator` melalui self-service |
| `status` | Workflow role request |
| `reason` | Alasan requester |
| `reviewed_by` | FK users nullable |
| `reviewed_at` | Timestamp nullable |
| `review_notes` | Catatan reviewer/revisi |
| `created_at`, `updated_at`, `archived_at` | Timestamp |

Index:

```text
INDEX(user_id, status)
INDEX(requested_role_id, status)
INDEX(status, created_at)
```

---

## Organisasi, Profil, dan Verifikasi

### `organization_units`

Master organisasi dan unit kerja untuk Pemprov Lampung serta struktur yang diperlukan.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `parent_id` | FK self-reference nullable |
| `code` | Kode organisasi unique bila tersedia |
| `name` | Nama organisasi/unit |
| `type` | `province`, `agency`, `bureau`, `department`, `inspectorate`, `hospital`, `secretariat`, atau nilai terkendali lain |
| `is_active` | Status aktif |
| `sort_order` | Urutan |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:

```text
UNIQUE(code)
INDEX(parent_id)
INDEX(type, is_active)
```

### `procurement_roles`

Master peran pengadaan.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `key` | PA, KPA, PPK, PP, POKJA_PEMILIHAN, dan seterusnya |
| `name` | Label peran |
| `description` | Deskripsi |
| `is_active` | Status aktif |

Constraint:

```text
UNIQUE(key)
```

### `user_procurement_roles`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK users |
| `procurement_role_id` | FK procurement_roles |
| `verification_status` | `self_declared`, `pending`, `verified`, `rejected` |
| `verified_by`, `verified_at` | Reviewer metadata |
| `assignment_start_at`, `assignment_end_at` | Optional period penugasan |
| `evidence_file_id` | FK file_assets nullable |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(user_id, procurement_role_id)
INDEX(verification_status)
```

### `profile_change_requests`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK users |
| `status` | Workflow profile change |
| `requested_payload` | JSON perubahan yang diminta; restricted bila mengandung data personal |
| `reason` | Alasan perubahan |
| `reviewed_by`, `reviewed_at` | Metadata review |
| `review_notes` | Catatan review/revisi |
| `applied_at` | Waktu perubahan diterapkan |
| `created_at`, `updated_at`, `archived_at` | Timestamp |

Index:

```text
INDEX(user_id, status)
INDEX(status, created_at)
```

### `profile_verifications`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK users |
| `verification_type` | Misalnya profile/asn/provider/competency sesuai policy |
| `status` | `pending`, `in_review`, `approved`, `rejected`, `revision_required` |
| `requested_by` | FK users nullable |
| `reviewed_by` | FK users nullable |
| `reason`, `notes` | Reason dan catatan review |
| `created_at`, `reviewed_at`, `updated_at` | Timestamp |

Index:

```text
INDEX(user_id, verification_type, status)
INDEX(status, created_at)
```

---

## Shared Workflow, Audit, File, Notification, Bookmark, Export

### `approvals`

Generic approval record untuk resource yang membutuhkan decision workflow.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `resource_type` | Polymorphic domain resource type |
| `resource_id` | Identifier resource |
| `requested_action` | Aksi yang memerlukan approval |
| `status` | `pending`, `in_review`, `approved`, `rejected`, `revision_required`, `cancelled`, `expired` |
| `requested_by` | FK users/System context |
| `reviewed_by` | FK users nullable |
| `reason` | Alasan pengajuan/keputusan |
| `notes` | Catatan aman |
| `expires_at` | Deadline bila ada |
| `reviewed_at` | Waktu keputusan |
| `created_at`, `updated_at` | Timestamp |

Index:

```text
INDEX(resource_type, resource_id)
INDEX(status, created_at)
INDEX(reviewed_by, status)
```

### `workflow_transitions`

Riwayat transisi state lintas domain.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `resource_type`, `resource_id` | Resource terkait |
| `from_state`, `to_state` | State transition |
| `action` | Nama aksi/transisi |
| `actor_id` | FK users nullable untuk system |
| `actor_type` | `user` atau `system` |
| `reason`, `notes` | Keterangan yang aman |
| `metadata` | JSON aman |
| `created_at` | Timestamp |

Index:

```text
INDEX(resource_type, resource_id, created_at)
INDEX(actor_id, created_at)
INDEX(to_state, created_at)
```

### `audit_logs`

Append-only audit log aplikasi.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `actor_id` | FK users nullable untuk system/guest event |
| `action` | Action key |
| `resource_type`, `resource_id` | Resource terkait nullable |
| `result` | `success`, `failed`, `denied`, `partial` |
| `before_snapshot` | JSON redacted nullable |
| `after_snapshot` | JSON redacted nullable |
| `reason` | Alasan bila diperlukan |
| `ip_hash` | Hash/minimized IP context sesuai privacy policy |
| `user_agent_hash` | Hash/minimized UA context |
| `request_id` | Correlation ID bila digunakan |
| `created_at` | Timestamp |

Index:

```text
INDEX(actor_id, created_at)
INDEX(resource_type, resource_id, created_at)
INDEX(action, created_at)
INDEX(result, created_at)
```

### `file_assets`

Metadata file sesuai file-storage policy.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `disk` | Storage disk key |
| `path` | Internal path, tidak diekspos publik |
| `original_name` | Untrusted display name |
| `stored_name` | Nama server-side unik |
| `extension`, `mime_type`, `size_bytes` | Metadata tervalidasi |
| `checksum` | Hash integritas bila digunakan |
| `visibility` | `public`, `internal`, `restricted` |
| `lifecycle_status` | Lifecycle file |
| `resource_type`, `resource_id` | Resource pemilik |
| `uploaded_by` | FK users nullable/system |
| `available_at`, `expires_at`, `deleted_at` | Lifecycle timestamp |
| `metadata` | JSON aman |
| `created_at`, `updated_at` | Timestamp |

Index:

```text
INDEX(resource_type, resource_id)
INDEX(visibility, lifecycle_status)
INDEX(uploaded_by, created_at)
INDEX(expires_at)
```

### `notifications`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK users |
| `channel` | `database`, `email`, atau channel terkendali |
| `type` | Notification type/key |
| `title`, `body` | Konten aman/minimum |
| `data` | JSON aman; tidak memuat secret/restricted berlebihan |
| `read_at` | Timestamp nullable |
| `sent_at` | Timestamp nullable |
| `created_at` | Timestamp |

Index:

```text
INDEX(user_id, read_at, created_at)
INDEX(channel, sent_at)
```

### `bookmarks`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK users |
| `bookmarkable_type`, `bookmarkable_id` | Resource yang di-bookmark |
| `created_at` | Timestamp |

Constraint/index:

```text
UNIQUE(user_id, bookmarkable_type, bookmarkable_id)
INDEX(bookmarkable_type, bookmarkable_id)
```

### `exports`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `requested_by` | FK users |
| `type` | Tipe export |
| `status` | `queued`, `generating`, `ready`, `failed`, `expired`, `deleted` |
| `filters` | JSON filter/scoping export |
| `file_id` | FK file_assets nullable |
| `error_message` | Pesan aman bila gagal |
| `expires_at` | Batas unduh |
| `created_at`, `completed_at`, `deleted_at` | Timestamp |

Index:

```text
INDEX(requested_by, status, created_at)
INDEX(status, expires_at)
```

---

## Arena Belajar

### Struktur inti

```text
programs
→ courses
→ learning_classes
→ learning_modules
→ lessons
→ enrollment
→ assessment/progress/feedback
→ certificate eligibility dan certificate
```

### `programs`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `title`, `slug` | Identitas program |
| `summary`, `description` | Konten |
| `status` | Workflow/publication status bila digunakan |
| `visibility` | Public/internal |
| `featured` | Penanda konten unggulan |
| `cover_file_id` | FK file_assets nullable |
| `created_by`, `updated_by`, `published_by` | Actor metadata |
| `published_at`, `created_at`, `updated_at`, `deleted_at` | Timestamp |

Constraint/index:

```text
UNIQUE(slug)
INDEX(status, visibility, published_at)
```

### `courses`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `program_id` | FK programs nullable bila course berdiri sendiri diizinkan |
| `title`, `slug` | Identitas course |
| `summary`, `description` | Konten |
| `level`, `duration_minutes` | Metadata pembelajaran |
| `status`, `visibility` | Publication/visibility |
| `cover_file_id` | FK file_assets nullable |
| `created_by`, `updated_by`, `published_by` | Actor metadata |
| `published_at`, `created_at`, `updated_at`, `deleted_at` | Timestamp |

Constraint/index:

```text
UNIQUE(slug)
INDEX(program_id)
INDEX(status, visibility, published_at)
```

### `learning_classes`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `course_id` | FK courses |
| `code` | Kode class unique dalam scope yang ditetapkan |
| `name` | Nama kelas |
| `status` | Lifecycle class, misalnya draft/open/ongoing/finished/cancelled |
| `registration_open_at`, `registration_close_at` | Periode enrollment |
| `start_at`, `end_at` | Periode kelas |
| `capacity` | Kuota nullable |
| `certificate_enabled` | Boolean |
| `passing_grade` | Baseline minimum 70 bila diaktifkan |
| `quiz_weight`, `assignment_weight`, `post_test_weight` | Bobot nilai; post-test baseline 60 |
| `created_by`, `updated_by` | Actor metadata |
| `created_at`, `updated_at`, `deleted_at` | Timestamp |

Constraint/index:

```text
UNIQUE(course_id, code)
INDEX(course_id, status)
INDEX(registration_open_at, registration_close_at)
INDEX(start_at, end_at)
CHECK(quiz_weight + assignment_weight + post_test_weight = 100)
CHECK(post_test_weight = 60)
CHECK(passing_grade >= 0 AND passing_grade <= 100)
```

### `class_trainers` dan `class_evaluators`

Assignment role terhadap class.

| Kolom utama | Keterangan |
|---|---|
| `class_id` | FK learning_classes |
| `user_id` | FK users |
| `assigned_by` | FK users |
| `assigned_at` | Timestamp |
| `status` | `active`, `revoked` bila dibutuhkan |

Constraint:

```text
PRIMARY KEY(class_id, user_id)
```

### `learning_modules`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `class_id` | FK learning_classes |
| `title`, `description` | Konten module |
| `sort_order` | Urutan unik dalam class |
| `is_required` | Boolean |
| `status` | Controlled status |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(class_id, sort_order)
```

### `lessons`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `module_id` | FK learning_modules |
| `title`, `slug` | Identitas lesson |
| `content_type` | `text`, `document`, `video`, `link`, atau controlled value |
| `content` | Text/embedded safe content/metadata link |
| `file_id` | FK file_assets nullable |
| `external_url` | URL tervalidasi nullable |
| `sort_order` | Urutan lesson |
| `is_required` | Boolean |
| `status`, `visibility` | Availability/visibility |
| `created_by`, `updated_by` | Actor metadata |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(module_id, sort_order)
INDEX(module_id, status, visibility)
```

### `enrollments`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `class_id` | FK learning_classes |
| `user_id` | FK users |
| `status` | `pending`, `active`, `completed`, `withdrawn`, `cancelled` |
| `enrolled_at`, `activated_at`, `completed_at` | Timestamp |
| `final_score` | Skor akhir nullable |
| `passed_at` | Timestamp nullable |
| `certificate_eligible_at` | Timestamp nullable |
| `withdrawn_at`, `cancelled_at` | Timestamp nullable |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:

```text
UNIQUE(class_id, user_id)
INDEX(user_id, status)
INDEX(class_id, status)
INDEX(certificate_eligible_at)
```

---

## Learning Assessment

### `pre_tests`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `class_id` | FK learning_classes |
| `is_enabled` | Boolean |
| `attempt_limit` | Default satu bila pre-test aktif |
| `status` | Availability status |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(class_id)
```

### `quizzes`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `class_id`, `lesson_id` | FK learning_classes/lessons; lesson nullable untuk post-test/class quiz |
| `type` | `pre_test`, `lesson_quiz`, `post_test` |
| `title`, `instructions` | Konten |
| `attempt_limit` | Policy attempt |
| `is_required` | Boolean |
| `status` | Availability |
| `created_at`, `updated_at` | Timestamp |

Index:

```text
INDEX(class_id, type, status)
INDEX(lesson_id)
```

### `quiz_questions` dan `quiz_options`

| Tabel | Data utama | Constraint penting |
|---|---|---|
| `quiz_questions` | quiz_id, type, prompt, points, sort_order, explanation | `UNIQUE(quiz_id, sort_order)` |
| `quiz_options` | question_id, option_text, is_correct, sort_order | `UNIQUE(question_id, sort_order)` |

Jawaban benar tidak boleh dikirim ke Participant sebelum policy assessment mengizinkan.

### `quiz_attempts`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `quiz_id` | FK quizzes |
| `enrollment_id` | FK enrollments |
| `attempt_number` | Nomor attempt |
| `status` | `started`, `submitted`, `graded`, `invalidated` |
| `started_at`, `submitted_at`, `graded_at` | Timestamp |
| `score` | Skor nullable |
| `invalidated_by`, `invalidated_reason` | Audit invalidasi |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(quiz_id, enrollment_id, attempt_number)
INDEX(enrollment_id, status)
```

### `quiz_answers`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `attempt_id` | FK quiz_attempts |
| `question_id` | FK quiz_questions |
| `answer_payload` | JSON jawaban; restricted |
| `is_correct`, `score_awarded` | Hasil grading |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(attempt_id, question_id)
```

### `assignments`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `class_id`, `lesson_id` | FK class/lesson nullable sesuai design |
| `title`, `instructions` | Konten |
| `due_at` | Deadline nullable |
| `is_required` | Boolean |
| `max_attempts` | Policy revision/submission |
| `allowed_mimes`, `max_file_size_bytes` | JSON/policy override |
| `status` | Availability |
| `created_by`, `updated_by` | Actor metadata |
| `created_at`, `updated_at` | Timestamp |

### `assignment_submissions`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `assignment_id` | FK assignments |
| `enrollment_id` | FK enrollments |
| `attempt_number` | Nomor revision/attempt |
| `status` | `draft`, `submitted`, `in_review`, `revision_required`, `graded`, `cancelled` |
| `content` | Jawaban teks; restricted |
| `submitted_at`, `graded_at` | Timestamp |
| `graded_by` | FK users nullable |
| `score`, `feedback` | Hasil evaluasi restricted |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(assignment_id, enrollment_id, attempt_number)
INDEX(enrollment_id, status)
INDEX(graded_by, status)
```

### `assignment_submission_files`

Pivot file submission.

```text
PRIMARY KEY(submission_id, file_id)
FK submission_id → assignment_submissions
FK file_id → file_assets
```

### `learning_progress`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `enrollment_id` | FK enrollments |
| `lesson_id` | FK lessons |
| `status` | `not_started`, `in_progress`, `completed` |
| `started_at`, `completed_at` | Timestamp |
| `progress_percent` | Controlled range 0–100 |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(enrollment_id, lesson_id)
INDEX(enrollment_id, status)
```

### `feedbacks`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `enrollment_id` | FK enrollments |
| `rating` | Nullable controlled rating |
| `comment` | Feedback user |
| `status` | `not_started`, `submitted` |
| `submitted_at` | Timestamp |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(enrollment_id)
```

---

## Bunga Rampai / Knowledge

### `knowledge_categories`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `parent_id` | FK self nullable |
| `name`, `slug` | Identitas kategori |
| `description` | Deskripsi |
| `sort_order`, `is_active` | Pengaturan tampilan |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(slug)
INDEX(parent_id, is_active)
```

### `knowledge_tags`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `name`, `slug` | Identitas tag |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(slug)
```

### `knowledge_articles`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `type` | `article`, `faq`, `case_study`, `template`, atau controlled content type |
| `title`, `slug` | Identitas publik |
| `summary`, `content` | Konten |
| `workflow_status` | Publication workflow |
| `visibility` | `public`, `internal`, `restricted` bila diperlukan |
| `featured`, `carousel_enabled`, `carousel_order` | Kurasi tampilan |
| `cover_file_id` | FK file_assets nullable |
| `author_id` | FK users |
| `reviewed_by`, `published_by` | FK users nullable |
| `published_at`, `archived_at` | Timestamp nullable |
| `created_at`, `updated_at`, `deleted_at` | Timestamp |

Constraint/index:

```text
UNIQUE(slug)
INDEX(type, workflow_status, visibility, published_at)
INDEX(featured, carousel_enabled, carousel_order)
```

### `knowledge_article_categories`, `knowledge_article_tags`

Pivot relasi konten terhadap kategori/tag.

```text
PRIMARY KEY(article_id, category_id)
PRIMARY KEY(article_id, tag_id)
```

### `knowledge_media`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `article_id` | FK knowledge_articles nullable |
| `type` | `image`, `poster`, `flyer`, `infographic`, `gallery`, `video_file`, `youtube`, `social_link`, `external_link` |
| `title`, `caption`, `alt_text` | Metadata accessibility |
| `file_id` | FK file_assets nullable |
| `external_url` | URL tervalidasi nullable |
| `thumbnail_file_id` | FK file_assets nullable |
| `workflow_status`, `visibility` | Publication/visibility |
| `sort_order`, `featured`, `carousel_enabled` | Kurasi |
| `created_by`, `published_by` | Actor metadata |
| `published_at`, `created_at`, `updated_at` | Timestamp |

Index:

```text
INDEX(article_id)
INDEX(type, workflow_status, visibility, published_at)
```

---

## Regulasi PBJ

### `regulation_categories`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `name`, `slug` | Identitas kategori |
| `description` | Deskripsi |
| `is_active` | Status |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(slug)
```

### `regulations`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `category_id` | FK regulation_categories |
| `title`, `slug` | Identitas publik |
| `document_number`, `document_year` | Metadata hukum |
| `issuer_name` | Instansi penerbit |
| `official_date`, `effective_date` | Tanggal resmi/berlaku |
| `summary` | Ringkasan |
| `official_source_url` | Sumber resmi tervalidasi |
| `workflow_status` | Editorial/publication workflow |
| `legal_status` | `active`, `amended`, `revoked`, `expired` |
| `visibility` | Visibility record |
| `current_version_id` | FK regulation_versions nullable, di-set setelah version dibuat |
| `created_by`, `reviewed_by`, `published_by` | Actor metadata |
| `published_at`, `archived_at`, `created_at`, `updated_at`, `deleted_at` | Timestamp |

Constraint/index:

```text
UNIQUE(slug)
UNIQUE(document_number, document_year, issuer_name)
INDEX(category_id, workflow_status, legal_status, visibility)
INDEX(published_at)
```

### `regulation_versions`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `regulation_id` | FK regulations |
| `version_number` | Nomor versi integer |
| `file_id` | FK file_assets nullable |
| `changelog` | Perubahan versi |
| `is_current` | Boolean |
| `uploaded_by` | FK users |
| `created_at` | Timestamp |

Constraint:

```text
UNIQUE(regulation_id, version_number)
UNIQUE(regulation_id, is_current) WHERE is_current = true
```

Bila database tidak mendukung partial unique index, current version dijaga melalui service/transaction dan constraint alternatif.

### `regulation_relations`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `source_regulation_id` | FK regulations |
| `target_regulation_id` | FK regulations |
| `relation_type` | `amends`, `amended_by`, `revokes`, `revoked_by`, `replaces`, `replaced_by`, `implements`, `implemented_by`, `references`, `referenced_by` |
| `notes` | Catatan |
| `created_by` | FK users |
| `created_at` | Timestamp |

Constraint:

```text
UNIQUE(source_regulation_id, target_regulation_id, relation_type)
CHECK(source_regulation_id <> target_regulation_id)
```

### `regulation_downloads`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `regulation_id`, `version_id`, `file_id` | FK terkait |
| `user_id` | FK users nullable untuk public download bila dicatat minimal |
| `access_type` | `preview`, `download`, `public`/controlled value |
| `created_at` | Timestamp |

Index:

```text
INDEX(regulation_id, created_at)
INDEX(user_id, created_at)
```

---

## Berita, Agenda, dan Event

### `news_categories`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `name`, `slug` | Identitas kategori |
| `description`, `is_active` | Metadata |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(slug)
```

### `news`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `category_id` | FK news_categories nullable |
| `title`, `slug` | Identitas publik |
| `summary`, `content` | Konten berita utama |
| `reading_time` | Estimasi waktu baca (misal: "3 Menit") |
| `tags` | Tag pencarian / kata kunci |
| `source_name`, `source_url` | Sumber berita / referensi |
| `cover_file_id` | FK file_assets nullable |
| `cover_caption` | Keterangan/caption foto cover |
| `is_featured` | Flag berita unggulan (Top 5 Featured Carousel) |
| `workflow_status` | Publication workflow (`draft`, `in_review`, `published`, `archived`) |
| `visibility` | Visibility (`public`, `internal`) |
| `published_at`, `scheduled_at` | Waktu publikasi |
| `view_count` | Counter terkendali (increment otomatis saat dibaca di publik) |
| `author_id`, `reviewed_by`, `published_by` | Actor metadata (FK users) |
| `created_at`, `updated_at`, `deleted_at` | Timestamp |

Constraint/index:

```text
UNIQUE(slug)
INDEX(category_id, workflow_status, visibility, published_at)
INDEX(is_featured, workflow_status, published_at)
```

### `events`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `title`, `slug` | Identitas event |
| `type` | Webinar, sosialisasi, workshop, pelatihan, atau controlled value |
| `mode` | `online`, `offline`, `hybrid` |
| `status` | `draft`, `coming_soon`, `open_registration`, `full`, `finished`, `cancelled` |
| `visibility` | Visibility public/internal |
| `summary`, `description` | Konten ringkasan dan deskripsi lengkap |
| `cover_file_id` | FK file_assets nullable |
| `start_at`, `end_at` | Waktu event |
| `location` | Lokasi offline nullable |
| `meeting_url` | URL online tervalidasi nullable |
| `registration_open_at`, `registration_close_at` | Periode registrasi |
| `capacity` | Kuota peserta utama |
| `waitlist_enabled` | Boolean status fitur antrean jika kuota terpenuhi |
| `certificate_enabled` | Boolean penerbitan e-sertifikat otomatis |
| `attendance_code` | Kode presensi 6-digit acak (tersimpan untuk display admin) |
| `attendance_code_hash` | Hash SHA-256 kode presensi untuk verifikasi check-in cepat |
| `attendance_open_at`, `attendance_close_at` | Jendela waktu presensi mandiri dibuka dan ditutup |
| `created_by`, `updated_by` | Actor metadata (FK users) |
| `cancelled_by`, `cancelled_at`, `cancellation_reason` | Metadata pembatalan event |
| `created_at`, `updated_at`, `deleted_at` | Timestamp |

Constraint/index:

```text
UNIQUE(slug)
INDEX(status, visibility, start_at)
INDEX(registration_open_at, registration_close_at)
INDEX(attendance_code)
```

### `event_registrations`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `event_id` | FK events |
| `user_id` | FK users |
| `status` | `registered`, `waitlisted`, `cancelled`, `rejected` |
| `registered_at`, `cancelled_at`, `promoted_at` | Timestamp registrasi, pembatalan, dan promosi otomatis dari waitlist |
| `status_reason` | Alasan pembatalan atau penolakan |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:

```text
UNIQUE(event_id, user_id)
INDEX(event_id, status, registered_at)
INDEX(user_id, status)
```

### `attendances`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `event_registration_id` | FK event_registrations |
| `status` | `present`, `absent`, `excused` |
| `recorded_by` | FK users (nullable jika melalui kode presensi mandiri) |
| `recorded_at` | Timestamp kehadiran |
| `notes` | Catatan kehadiran |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(event_registration_id)
```

---

## Sertifikat Digital

### `certificate_sequences`

Tabel sequence counter untuk menjamin penomoran sertifikat digital yang atomik, urut, dan bebas race condition (contoh format: `BPJ-SERT/{TIPE}/{TAHUN}/{BULAN}/{NOMOR_URUT_5_DIGIT}`).

| Kolom utama | Keterangan |
|---|---|
| `year` | Tahun penomoran (Primary Key, `SMALLINT UNSIGNED`) |
| `last_number` | Nomor urut terakhir yang diterbitkan (`INT UNSIGNED`, default: 0) |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
PRIMARY KEY(year)
```

### `certificate_templates`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `name` | Nama template |
| `source_type` | `learning`, `event` |
| `template_payload` | JSON/layout data yang aman |
| `background_file_id` | FK file_assets nullable |
| `is_active` | Boolean |
| `created_by`, `updated_by` | Actor metadata |
| `created_at`, `updated_at` | Timestamp |

Index:

```text
INDEX(source_type, is_active)
```

### `certificates`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `source_type` | `learning_enrollment`, `event_registration` |
| `source_id` | Identifier source |
| `user_id` | FK users sebagai pemilik |
| `template_id` | FK certificate_templates |
| `certificate_number` | Nomor unik |
| `verification_token_hash` | Hash token verifikasi unik |
| `status` | `pending`, `issued`, `revoked`, `expired` |
| `issued_at`, `expires_at`, `revoked_at` | Lifecycle timestamp |
| `revoked_by` | FK users nullable |
| `revocation_reason` | Alasan revoke |
| `pdf_file_id` | FK file_assets nullable |
| `replaced_by_certificate_id` | FK self nullable |
| `issued_by` | FK users nullable/system |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:

```text
UNIQUE(certificate_number)
UNIQUE(verification_token_hash)
UNIQUE(source_type, source_id)
INDEX(user_id, status)
INDEX(status, expires_at)
```

Penggantian sertifikat harus mempertahankan riwayat melalui relasi self-reference dan audit.

---

## Government Intelligence PBJ

### `competencies`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `key`, `name` | Identitas kompetensi |
| `description` | Deskripsi |
| `is_active` | Status |
| `created_at`, `updated_at` | Timestamp |

Constraint:

```text
UNIQUE(key)
```

### `jf_ppbj_levels`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `key` | `PERTAMA`, `MUDA`, `MADYA`, `UTAMA` |
| `name` | Label |
| `sort_order` | Urutan |
| `is_active` | Status |

Constraint:

```text
UNIQUE(key)
```

### `jf_ppbj_records`

Riwayat data JF PPBJ individu, bersifat restricted.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `user_id` | FK users nullable bila terhubung account |
| `organization_unit_id` | FK organization_units |
| `employee_identifier` | NIP/identifier; restricted dan terlindungi |
| `name_snapshot` | Nama; restricted |
| `level_id` | FK jf_ppbj_levels |
| `status` | Aktif/nonaktif atau controlled status |
| `effective_at`, `ended_at` | Periode record |
| `change_type` | penambahan/promosi/mutasi/pensiun/penonaktifan/pembaruan |
| `evidence_file_id` | FK file_assets nullable |
| `created_by`, `updated_by` | Actor metadata |
| `created_at`, `updated_at` | Timestamp |

Index:

```text
INDEX(organization_unit_id, level_id, status)
INDEX(user_id)
INDEX(effective_at, ended_at)
```

### `competency_certificate_types`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `key` | `PBJ_LEVEL_1`, `PPK_TYPE_A`, `PPK_TYPE_B`, `PPK_TYPE_C`, dan value terkendali lain |
| `name`, `description` | Metadata |
| `is_active` | Status |

Constraint:

```text
UNIQUE(key)
```

### `competency_certificates`

Berbeda dari `certificates` digital BegawiPengadaan.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `jf_ppbj_record_id` | FK jf_ppbj_records |
| `certificate_type_id` | FK competency_certificate_types |
| `certificate_number` | Nomor sertifikat kompetensi restricted |
| `issued_at`, `expires_at` | Masa berlaku |
| `status` | `pending`, `verified`, `rejected`, `expired`, atau controlled value |
| `evidence_file_id` | FK file_assets nullable |
| `verified_by`, `verified_at` | Metadata verifikasi |
| `created_at`, `updated_at` | Timestamp |

Index:

```text
INDEX(jf_ppbj_record_id, status)
INDEX(certificate_type_id, expires_at)
```

### `competency_gap_snapshots`

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key |
| `organization_unit_id` | FK organization_units |
| `competency_id` | FK competencies nullable |
| `period_date` | Periode snapshot |
| `required_count` | Kebutuhan |
| `available_count` | Ketersediaan |
| `gap_count` | Baseline `required_count - available_count` |
| `metadata` | JSON agregat aman |
| `generated_by` | FK users nullable/system |
| `created_at` | Timestamp |

Constraint:

```text
UNIQUE(organization_unit_id, competency_id, period_date)
INDEX(period_date)
```

---

## Master Wilayah dan Pipeline Regulasi & Assessment Tambahan

### `wilayah`

Master kode dan nama hierarki administrasi wilayah Indonesia (Provinsi, Kabupaten/Kota, Kecamatan, Kelurahan/Desa) read-only.

| Kolom utama | Keterangan |
|---|---|
| `kode` | Primary key `VARCHAR(13)` (e.g. `18` Prov Lampung, `18.71` Bandar Lampung) |
| `nama` | Nama wilayah `VARCHAR(100)` |

Constraint/index:
```text
PRIMARY KEY (kode)
INDEX wilayah_name_idx (nama)
```

### `procurement_stages`

Master tahapan Pengadaan Barang/Jasa (Perencanaan, Persiapan Pengadaan, Persiapan Pemilihan, Pemilihan Penyedia, Pelaksanaan Kontrak, Serah Terima).

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `key` | Unique slug/kode teknis tahapan `VARCHAR(64)` |
| `name` | Nama tahapan PBJ `VARCHAR(255)` |
| `description` | Deskripsi ruang lingkup tahapan `TEXT` |
| `sort_order` | Urutan tampilan `INT UNSIGNED DEFAULT 0` |
| `is_active` | Status aktif `TINYINT DEFAULT 1` |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY (key)
```

### `regulation_procurement_stages`

Tabel pivot relasi many-to-many antara regulasi dan tahapan PBJ terkait.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `regulation_id` | FK ke `regulations.id` |
| `procurement_stage_id` | FK ke `procurement_stages.id` |
| `relevance` | Derajat relevansi `VARCHAR(32)` |
| `created_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY rps_unique (regulation_id, procurement_stage_id)
```

### `regulation_competencies`

Tabel pivot relasi many-to-many antara regulasi dan standar kompetensi pengadaan.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `regulation_id` | FK ke `regulations.id` |
| `competency_id` | FK ke `competencies.id` |
| `relevance` | Derajat relevansi `VARCHAR(32)` |
| `created_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY rc_unique (regulation_id, competency_id)
```

### `knowledge_article_tags`

Tabel pivot relasi many-to-many antara naskah artikel Bunga Rampai dan tag pengetahuan.

| Kolom utama | Keterangan |
|---|---|
| `article_id` | FK ke `knowledge_articles.id` |
| `tag_id` | FK ke `knowledge_tags.id` |

Constraint/index:
```text
PRIMARY KEY (article_id, tag_id)
INDEX (tag_id)
```

### `quiz_options`

Pilihan jawaban pertanyaan kuis (A, B, C, D, E) pada assessment pembelajaran.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `question_id` | FK ke `quiz_questions.id` ON DELETE RESTRICT |
| `option_text` | Redaksi pilihan jawaban `TEXT` |
| `is_correct` | Penanda kunci jawaban benar `TINYINT(1) DEFAULT 0` |
| `sort_order` | Urutan abjad opsi `INT UNSIGNED` |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY (question_id, sort_order)
```

---

## Landing CMS dan Konfigurasi Portal

### `home_sliders`

Banner slider hero utama pada beranda portal publik.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `badge_text` | Label badge promosi `VARCHAR(100)` |
| `title` | Judul hero slider `VARCHAR(255)` |
| `subtitle` | Deskripsi/sub-judul hero `TEXT` |
| `image_url` | URL/path gambar banner `VARCHAR(255)` |
| `button_text` | Label tombol aksi utama `VARCHAR(100)` |
| `button_url` | Tautan tombol aksi utama `VARCHAR(255)` |
| `secondary_button_text` | Label tombol aksi sekunder `VARCHAR(100)` |
| `secondary_button_url` | Tautan tombol aksi sekunder `VARCHAR(255)` |
| `sort_order` | Urutan urut rotasi banner `INT DEFAULT 0` |
| `is_active` | Status tayang `TINYINT(1) DEFAULT 1` |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
INDEX (is_active, sort_order)
```

### `page_banners`

Konfigurasi hero banner dan CTA spesifik per halaman modul publik.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `page_key` | Identifier halaman `VARCHAR(50)` (e.g. `arena-belajar`, `bunga-rampai`, `regulasi`, `berita`, `event`) |
| `hero_badge`, `hero_title`, `hero_subtitle` | Metadata hero header |
| `hero_bg_image`, `search_placeholder` | Aset visual dan placeholder pencarian |
| `cta_badge`, `cta_title`, `cta_lead` | Blok ajakan aksi penutup |
| `cta_primary_btn_text`, `cta_primary_btn_url` | Tombol CTA utama |
| `cta_secondary_btn_text`, `cta_secondary_btn_url` | Tombol CTA sekunder |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY uk_page_banner_key (page_key)
```

### `site_announcements`

Popup pengumuman penting, maklumat, atau promosi agenda PBJ yang tampil mengambang bagi pengunjung.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `title` | Judul maklumat pengumuman `VARCHAR(255)` |
| `badge_text` | Badge kategori pengumuman `VARCHAR(100)` |
| `message` | Isi pesan pengumuman `TEXT` |
| `image_url` | Banner grafis pendukung `VARCHAR(255)` |
| `cta_label`, `cta_url` | Tombol tindak lanjut |
| `start_date`, `end_date` | Masa berlaku tayang otomatis |
| `sort_order`, `is_active` | Urutan dan toggle aktif |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
INDEX (is_active, sort_order)
```

### `feature_highlights`

Item pilar budaya kerja PATIK, hierarki kelembagaan PBJ, dan roadmap pengadaan pada beranda.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `section_key` | Kategori seksi `VARCHAR(50)` (`pilar_budaya`, `hierarki`, `roadmap`) |
| `code` | Kode unik item `VARCHAR(50)` (e.g. `P`, `A`, `T`, `I`, `K`) |
| `title`, `subtitle`, `description` | Teks penjelasan pilar/hierarki |
| `icon_class`, `badge_text`, `color_hex` | Atribut styling dan ikon FontAwesome/Remix |
| `link_url`, `sort_order`, `is_active` | Tautan navigasi dan urutan |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
INDEX (section_key, is_active, sort_order)
```

### `external_portals`

Tautan resmi portal eksternal pengadaan nasional dan daerah (INAPROC, SPSE, SiRUP, Bela Pengadaan, dll.).

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `name` | Nama portal eksternal `VARCHAR(150)` |
| `acronym` | Singkatan/akronim portal `VARCHAR(50)` |
| `url` | URL tujuan eksternal `VARCHAR(255)` |
| `logo_url` | Logo portal `VARCHAR(255)` |
| `description` | Deskripsi fungsi portal `TEXT` |
| `category` | Kategori lingkup `VARCHAR(50)` (`national`, `regional`, `internal`) |
| `sort_order`, `is_active` | Urutan dan toggle aktif |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
INDEX (is_active, sort_order)
```

### `testimonials`

Ulasan dan testimoni stakeholder pengadaan (OPD, Pokja, Penyedia, Akademisi).

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `name` | Nama pemberi testimoni `VARCHAR(150)` |
| `role_title` | Jabatan pemberi testimoni `VARCHAR(150)` |
| `organization` | Instansi/OPD asal `VARCHAR(150)` |
| `avatar_url` | Foto profil testimoni `VARCHAR(255)` |
| `rating` | Bintang kepuasan 1-5 `TINYINT DEFAULT 5` |
| `quote` | Redaksi ulasan `TEXT` |
| `impact_statement` | Manfaat nyata yang dirasakan `VARCHAR(255)` |
| `category` | Kategori reviewer `VARCHAR(50)` (`opd`, `penyedia`, `jf_ppbj`) |
| `sort_order`, `is_active` | Urutan dan toggle aktif |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
INDEX (is_active, sort_order)
```

### `team_members`

Daftar susunan tim pembina, pengarah, dan inovator platform BegawiPengadaan.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `name` | Nama lengkap anggota tim `VARCHAR(150)` |
| `role_title` | Jabatan dalam tim `VARCHAR(150)` |
| `nip` | NIP kedinasan `VARCHAR(50)` |
| `category` | Klasifikasi `VARCHAR(50)` (`pembina`, `pengarah`, `pelaksana`, `inovator`) |
| `photo_url`, `bio`, `social_links` | Foto, biografi, dan tautan jejaring JSON |
| `sort_order`, `is_active` | Urutan dan status aktif |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
INDEX (category, is_active, sort_order)
```

### `service_categories`

Kategori layanan terintegrasi landing page.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `name`, `code` | Nama layanan `VARCHAR(150)` dan kode unik `VARCHAR(50)` |
| `description` | Deskripsi layanan `TEXT` |
| `sort_order`, `is_active` | Urutan dan toggle aktif |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY uk_service_cat_code (code)
INDEX (is_active, sort_order)
```

### `site_settings`

Penyimpanan konfigurasi global key-value platform sistem (general, organization, learning, regulation, news, event, integration, maintenance).

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `setting_group` | Grup pengaturan `VARCHAR(50) DEFAULT 'general'` |
| `setting_key` | Kunci konfigurasi `VARCHAR(100)` |
| `setting_value` | Nilai konfigurasi `LONGTEXT` |
| `field_type` | Tipe data input UI `VARCHAR(30) DEFAULT 'text'` |
| `description` | Keterangan fungsi parameter `VARCHAR(255)` |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY uk_group_key (setting_group, setting_key)
INDEX (setting_group)
```

---

## Interaksi Publik, Helpdesk, dan Survei

### `contact_messages`

Pesan konsultasi dan pertanyaan publik yang dikirim melalui form `/kontak`.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `ticket_number` | Nomor tiket unik konsultasi `VARCHAR(50)` |
| `sender_name`, `sender_email`, `sender_phone` | Data pemohon konsultasi |
| `service_category` | Kategori layanan konsultasi `VARCHAR(100)` |
| `subject`, `message` | Perihal dan isi pesan `TEXT` |
| `status` | Status tindak lanjut (`unread`, `in_progress`, `replied`, `archived`) |
| `admin_notes`, `replied_at` | Catatan internal dan waktu respon admin |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY uk_contact_ticket (ticket_number)
INDEX (status, created_at)
```

### `helpdesk_topics`

Topik konsultasi terpandu untuk WhatsApp Floating Helpdesk Widget.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `topic_title` | Judul topik konsultasi `VARCHAR(150)` |
| `default_message` | Draf pesan otomatis konsultasi WhatsApp `TEXT` |
| `icon_class`, `sort_order`, `is_active` | Ikon visual, urutan, status aktif |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
INDEX (is_active, sort_order)
```

### `newsletter_subscribers`

Pendaftar langganan buletin warta pengadaan PBJ.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `email` | Alamat email terdaftar unik `VARCHAR(150)` |
| `status` | Status langganan (`subscribed`, `unsubscribed`) |
| `subscribed_at`, `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY uk_newsletter_email (email)
INDEX (status, subscribed_at)
```

### `visitor_statistics`

Pencatatan metrik volume kunjungan agregat per fitur/modul per hari untuk visualisasi tren trafik.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `feature` | Nama modul `VARCHAR(50) DEFAULT 'home'` |
| `visit_date` | Tanggal pencatatan `DATE` |
| `hit_count` | Akumulasi jumlah hit `INT UNSIGNED DEFAULT 1` |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY feature_visit_date (feature, visit_date)
INDEX (visit_date)
INDEX (feature)
```

### `satisfaction_surveys`

Hasil pengisian instrumen Indeks Kepuasan Masyarakat (IKM) terhadap layanan platform BegawiPengadaan.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `ticket_number` | Nomor unik respons survei `VARCHAR(32)` |
| `rating_overall` | Nilai kepuasan umum 1-5 `TINYINT DEFAULT 5` |
| `service_access_rating` | Penilaian kemudahan akses akun |
| `service_regulation_rating` | Penilaian kemudahan pencarian regulasi |
| `service_learning_rating` | Penilaian kualitas materi belajar |
| `service_helpdesk_rating` | Penilaian keramahan respon konsultasi |
| `respondent_role` | Peran responden (ASN, Penyedia, Umum) |
| `is_anonymous` | Opsi anonimitas responden `TINYINT(1) DEFAULT 1` |
| `respondent_name`, `respondent_institution`, `respondent_contact` | Data responden bila tidak anonim |
| `feedback`, `recommendation` | Saran perbaikan dan rekomendasi |
| `ip_address`, `user_agent` | Data telemetri verifikasi integritas respon |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY (ticket_number)
INDEX (rating_overall)
INDEX (respondent_role)
INDEX (created_at)
```

### `survey_aspects`

Master indikator mutu yang dinilai pada survei kepuasan.

| Kolom utama | Keterangan |
|---|---|
| `id` | Primary key `BIGINT UNSIGNED AUTO_INCREMENT` |
| `aspect_key` | Kode aspek unik `VARCHAR(50)` |
| `title`, `description` | Nama dan keterangan indikator |
| `icon_class`, `weight` | Ikon dan bobot penilaian |
| `sort_order`, `is_active` | Urutan dan toggle aktif |
| `created_at`, `updated_at` | Timestamp |

Constraint/index:
```text
PRIMARY KEY (id)
UNIQUE KEY uk_survey_aspect_key (aspect_key)
INDEX (is_active, sort_order)
```

---

## Privacy, Restricted Data, dan Masking

Kolom berikut minimal diperlakukan restricted sesuai context:

```text
users.phone
asn_profiles NIP/NIPPK dan data kepegawaian
provider_profiles NIK/NIB/legal data
academic_profiles identifier profesi bila sensitif
profile verification payload/file
jf_ppbj_records employee_identifier dan name_snapshot
competency_certificates certificate_number/evidence
assignment_submissions content/feedback/file
quiz_answers answer_payload
file_assets.path dan metadata security
email_verifications.token_hash
password_resets.token_hash
audit snapshot yang dapat memuat data personal
```

Aturan database:

- Hindari menyimpan nilai sensitif secara duplikat dalam banyak tabel.
- Gunakan encryption-at-rest/application encryption untuk kolom tertentu bila policy/stack mendukung dan diperlukan.
- Batasi select/export kolom restricted melalui service/query layer.
- Snapshot audit harus melalui redaction sebelum disimpan.
- Statistik publik hanya membaca data agregat yang telah melalui disclosure control; jangan query raw personal data ke response publik.

---

## Foreign Key dan Delete Policy

Baseline delete policy:

| Relasi | Policy baseline |
|---|---|
| User ke profile/role/request | Restrict atau soft delete; riwayat akses dipertahankan |
| User ke audit logs | `SET NULL` atau actor snapshot bila user dihapus/diarsipkan; audit tetap ada |
| Domain record ke file asset | Restrict/soft delete metadata; bytes mengikuti retention policy |
| Course/Class/Module/Lesson | Restrict ketika data enrollment/assessment ada; gunakan archive/status |
| Enrollment ke assessment/submission/progress | Restrict; histori pembelajaran dipertahankan |
| Regulation ke version/relation/download | Restrict; histori regulasi dipertahankan |
| Event ke registration/attendance | Restrict; event dibatalkan/diarsipkan, tidak hard delete normal |
| Certificate ke source | Restrict; sertifikat tidak hard delete untuk menyembunyikan riwayat |
| Organization ke intelligence record | Restrict atau archive organisasi |

Hard delete hanya digunakan sesuai retention policy, legal basis, dan setelah mempertimbangkan foreign key, audit, backup, legal hold, serta kebutuhan historis.

---

## Index dan Performance Baseline

Selain primary/foreign key index, migration akhir menambahkan/meninjau:

- Unique index email, username, role key, permission key, slug, token hash, certificate number, dan pivot relation.
- Composite index status + visibility + published date untuk public content.
- Composite index user/class/status untuk enrollment dan aktivitas learning.
- Composite index class/status untuk queue assessment/assignment.
- Composite index workflow status + created date untuk review/approval queue.
- Composite index resource type + resource ID + created date untuk workflow/audit/file polymorphic relation.
- Index expiration untuk token, export, temporary file, dan lifecycle cleanup.
- Index organization/period/status untuk query Government Intelligence.
- Full-text index opsional untuk konten artikel, regulasi, dan berita sesuai engine database serta search policy.

Index baru harus dibuktikan oleh kebutuhan query/report nyata dan diuji terhadap ukuran data yang relevan.

---

## Seeder Strategy

### Reference seeders

Reference seeders menyediakan data master stabil:

```text
RoleSeeder
PermissionSeeder
RolePermissionSeeder
OrganizationSeeder
ProcurementRoleSeeder
KnowledgeCategorySeeder
RegulationCategorySeeder
ProcurementStageSeeder
CompetencySeeder
JfPpbjLevelSeeder
CompetencyCertificateTypeSeeder
```

### System seeders

```text
InitialSuperAdminSeeder
SystemSettingSeeder
```

`InitialSuperAdminSeeder` harus menggunakan credential aman dari environment/deployment process, bukan password hard-code dalam repository.

### Development seeders

```text
DevelopmentUserSeeder
LearningDemoSeeder
KnowledgeDemoSeeder
RegulationDemoSeeder
CommunicationDemoSeeder
CertificateDemoSeeder
IntelligenceDemoSeeder
```

Development seeders tidak boleh menghasilkan data personal nyata, token valid yang dapat disalahgunakan, secret, atau dokumen restricted production.

---

## Database Test Contract

Database test minimal mencakup:

- Migration dapat dijalankan dari database kosong secara deterministik.
- Foreign key, unique constraint, check constraint, dan index kritis tersedia.
- Email/username/token/certificate unique constraint bekerja.
- Pivot role-permission, user-role, bookmark, enrollment, dan event registration mencegah duplikasi.
- Workflow state invalid ditolak pada service/policy layer.
- Race condition kuota event/waitlist tidak menghasilkan overbooking.
- Unique certificate source mencegah penerbitan ganda.
- Current regulation version tidak dapat memiliki lebih dari satu versi aktif.
- Restricted query/export tidak membocorkan kolom personal tanpa permission.
- Soft delete/archive tidak merusak foreign key/historical audit.

---

## Checklist Implementasi Database

Sebelum database dinyatakan `Backend Ready`, pastikan:

- [ ] Seluruh tabel inti dibuat melalui migration sesuai urutan.
- [ ] Foreign key, unique constraint, check constraint, dan index kritis diterapkan.
- [ ] Semua status enum divalidasi konsisten dengan Auth/RBAC dan Workflow Contract.
- [ ] Token disimpan sebagai hash, bukan plain token.
- [ ] Password hanya disimpan sebagai hash.
- [ ] File bytes tidak disimpan pada database kecuali ada keputusan eksplisit yang terdokumentasi.
- [ ] File metadata memakai `file_assets` dengan visibility dan lifecycle.
- [ ] Audit log memiliki redaction untuk data restricted.
- [ ] Data publik/internal/restricted dapat dibatasi pada query/service layer.
- [ ] Seeder reference/system/development terpisah dan aman.
- [ ] Migration dan seeder memiliki database test.
- [ ] ERD dan data dictionary diperbarui berdasarkan blueprint ini sebelum implementasi domain meluas.

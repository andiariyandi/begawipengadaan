# BegawiPengadaan — Data Dictionary

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual
> - **Total Tabel:** 95 Tabel Aktif
> - **Total Kolom:** 1127 Kolom Terdaftar

---

## Ringkasan Eksekutif Kamus Data

Kamus data ini mendokumentasikan spesifikasi teknis 100% ground-truth dari seluruh **95 tabel** dan **1.127 kolom** pada skema basis data produksi `db_begawipbj` Pemerintah Provinsi Lampung. Seluruh nama kolom, tipe data riil, nullability, nilai default, indeks kunci, dan atribut extra diekstrak secara otomatis dari `information_schema.COLUMNS`.

Dokumen ini menjamin **0 kolom fiktif warisan** (seperti `news.status`, `regulations.nomor_regulasi`, `file_assets.user_id`, `class_trainers.trainer_id`, dll.), serta menjadi rujukan kanonikal utama untuk pengembangan Model (`$allowedFields`), validasi form, dan eksekusi query.

---

## Daftar Isi Tabel (95 Tabel)

1. [`academic_profiles`](#tabel-academic-profiles) (11 kolom)
2. [`approvals`](#tabel-approvals) (13 kolom)
3. [`asn_profiles`](#tabel-asn-profiles) (13 kolom)
4. [`assignment_submission_files`](#tabel-assignment-submission-files) (3 kolom)
5. [`assignment_submissions`](#tabel-assignment-submissions) (13 kolom)
6. [`assignments`](#tabel-assignments) (15 kolom)
7. [`attendances`](#tabel-attendances) (8 kolom)
8. [`audit_logs`](#tabel-audit-logs) (13 kolom)
9. [`bookmarks`](#tabel-bookmarks) (5 kolom)
10. [`certificate_sequences`](#tabel-certificate-sequences) (4 kolom)
11. [`certificate_templates`](#tabel-certificate-templates) (10 kolom)
12. [`certificates`](#tabel-certificates) (18 kolom)
13. [`class_evaluators`](#tabel-class-evaluators) (5 kolom)
14. [`class_trainers`](#tabel-class-trainers) (6 kolom)
15. [`competencies`](#tabel-competencies) (7 kolom)
16. [`competency_certificate_types`](#tabel-competency-certificate-types) (7 kolom)
17. [`competency_certificates`](#tabel-competency-certificates) (12 kolom)
18. [`competency_gap_snapshots`](#tabel-competency-gap-snapshots) (10 kolom)
19. [`contact_messages`](#tabel-contact-messages) (13 kolom)
20. [`courses`](#tabel-courses) (25 kolom)
21. [`email_verifications`](#tabel-email-verifications) (6 kolom)
22. [`enrollments`](#tabel-enrollments) (14 kolom)
23. [`event_registrations`](#tabel-event-registrations) (10 kolom)
24. [`events`](#tabel-events) (34 kolom)
25. [`exports`](#tabel-exports) (12 kolom)
26. [`external_portals`](#tabel-external-portals) (11 kolom)
27. [`feature_highlights`](#tabel-feature-highlights) (14 kolom)
28. [`feedbacks`](#tabel-feedbacks) (8 kolom)
29. [`file_assets`](#tabel-file-assets) (20 kolom)
30. [`helpdesk_topics`](#tabel-helpdesk-topics) (8 kolom)
31. [`home_sliders`](#tabel-home-sliders) (13 kolom)
32. [`jf_ppbj_levels`](#tabel-jf-ppbj-levels) (7 kolom)
33. [`jf_ppbj_records`](#tabel-jf-ppbj-records) (15 kolom)
34. [`knowledge_article_categories`](#tabel-knowledge-article-categories) (3 kolom)
35. [`knowledge_article_tags`](#tabel-knowledge-article-tags) (3 kolom)
36. [`knowledge_articles`](#tabel-knowledge-articles) (32 kolom)
37. [`knowledge_categories`](#tabel-knowledge-categories) (9 kolom)
38. [`knowledge_media`](#tabel-knowledge-media) (28 kolom)
39. [`knowledge_tags`](#tabel-knowledge-tags) (5 kolom)
40. [`learning_classes`](#tabel-learning-classes) (29 kolom)
41. [`learning_modules`](#tabel-learning-modules) (9 kolom)
42. [`learning_progress`](#tabel-learning-progress) (9 kolom)
43. [`lessons`](#tabel-lessons) (16 kolom)
44. [`migrations`](#tabel-migrations) (7 kolom)
45. [`news`](#tabel-news) (26 kolom)
46. [`news_categories`](#tabel-news-categories) (7 kolom)
47. [`newsletter_subscribers`](#tabel-newsletter-subscribers) (6 kolom)
48. [`notifications`](#tabel-notifications) (10 kolom)
49. [`organization_units`](#tabel-organization-units) (9 kolom)
50. [`page_banners`](#tabel-page-banners) (16 kolom)
51. [`password_resets`](#tabel-password-resets) (6 kolom)
52. [`permissions`](#tabel-permissions) (8 kolom)
53. [`pre_tests`](#tabel-pre-tests) (7 kolom)
54. [`procurement_roles`](#tabel-procurement-roles) (7 kolom)
55. [`procurement_stages`](#tabel-procurement-stages) (8 kolom)
56. [`profile_change_requests`](#tabel-profile-change-requests) (12 kolom)
57. [`profile_verifications`](#tabel-profile-verifications) (11 kolom)
58. [`programs`](#tabel-programs) (22 kolom)
59. [`provider_profiles`](#tabel-provider-profiles) (12 kolom)
60. [`public_profiles`](#tabel-public-profiles) (9 kolom)
61. [`quiz_answers`](#tabel-quiz-answers) (8 kolom)
62. [`quiz_attempts`](#tabel-quiz-attempts) (13 kolom)
63. [`quiz_options`](#tabel-quiz-options) (7 kolom)
64. [`quiz_questions`](#tabel-quiz-questions) (9 kolom)
65. [`quizzes`](#tabel-quizzes) (12 kolom)
66. [`regulation_categories`](#tabel-regulation-categories) (9 kolom)
67. [`regulation_competencies`](#tabel-regulation-competencies) (5 kolom)
68. [`regulation_downloads`](#tabel-regulation-downloads) (7 kolom)
69. [`regulation_procurement_stages`](#tabel-regulation-procurement-stages) (5 kolom)
70. [`regulation_relations`](#tabel-regulation-relations) (7 kolom)
71. [`regulation_versions`](#tabel-regulation-versions) (8 kolom)
72. [`regulations`](#tabel-regulations) (30 kolom)
73. [`role_permissions`](#tabel-role-permissions) (4 kolom)
74. [`role_requests`](#tabel-role-requests) (11 kolom)
75. [`roles`](#tabel-roles) (7 kolom)
76. [`rup_audit_batches`](#tabel-rup-audit-batches) (21 kolom)
77. [`rup_audit_clusters`](#tabel-rup-audit-clusters) (20 kolom)
78. [`rup_audit_opd_summaries`](#tabel-rup-audit-opd-summaries) (25 kolom)
79. [`rup_audit_packages`](#tabel-rup-audit-packages) (44 kolom)
80. [`rup_audit_violations`](#tabel-rup-audit-violations) (11 kolom)
81. [`satisfaction_surveys`](#tabel-satisfaction-surveys) (18 kolom)
82. [`service_categories`](#tabel-service-categories) (8 kolom)
83. [`site_announcements`](#tabel-site-announcements) (13 kolom)
84. [`site_settings`](#tabel-site-settings) (8 kolom)
85. [`survey_aspects`](#tabel-survey-aspects) (10 kolom)
86. [`team_members`](#tabel-team-members) (12 kolom)
87. [`testimonials`](#tabel-testimonials) (13 kolom)
88. [`user_procurement_roles`](#tabel-user-procurement-roles) (11 kolom)
89. [`user_profiles`](#tabel-user-profiles) (10 kolom)
90. [`user_roles`](#tabel-user-roles) (11 kolom)
91. [`users`](#tabel-users) (15 kolom)
92. [`visitor_ip_logs`](#tabel-visitor-ip-logs) (6 kolom)
93. [`visitor_statistics`](#tabel-visitor-statistics) (6 kolom)
94. [`wilayah`](#tabel-wilayah) (2 kolom)
95. [`workflow_transitions`](#tabel-workflow-transitions) (12 kolom)

---

## Spesifikasi Rinci Kolom per Tabel

### Tabel: `academic_profiles`

- **Nama Tabel:** `academic_profiles`
- **Jumlah Kolom:** 11 kolom
- **Definisi:** Struktur persistence data untuk entitas `academic_profiles` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | UNI | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `institution_name` | `varchar(255)` | NO | NULL | - | - | Atribut data institution name |
| 4 | `institution_type` | `varchar(64)` | NO | NULL | - | - | Atribut data institution type |
| 5 | `role_name` | `varchar(255)` | NO | NULL | - | - | Atribut data role name |
| 6 | `expertise` | `json` | YES | NULL | - | - | Atribut data expertise |
| 7 | `institution_email` | `varchar(255)` | YES | NULL | - | - | Atribut data institution email |
| 8 | `professional_identifier` | `varchar(128)` | YES | NULL | - | - | Atribut data professional identifier |
| 9 | `portfolio_url` | `varchar(2048)` | YES | NULL | - | - | Atribut data portfolio url |
| 10 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 11 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `approvals`

- **Nama Tabel:** `approvals`
- **Jumlah Kolom:** 13 kolom
- **Definisi:** Struktur persistence data untuk entitas `approvals` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `resource_type` | `varchar(128)` | NO | NULL | MUL | - | Atribut data resource type |
| 3 | `resource_id` | `bigint unsigned` | NO | NULL | - | - | Atribut data resource id |
| 4 | `requested_action` | `varchar(128)` | NO | NULL | - | - | Atribut data requested action |
| 5 | `status` | `varchar(64)` | NO | `pending` | MUL | - | Status operasional record sesuai CHECK constraint |
| 6 | `requested_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data requested by |
| 7 | `reviewed_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data reviewed by |
| 8 | `reason` | `text` | YES | NULL | - | - | Atribut data reason |
| 9 | `notes` | `text` | YES | NULL | - | - | Atribut data notes |
| 10 | `expires_at` | `datetime` | YES | NULL | - | - | Atribut data expires at |
| 11 | `reviewed_at` | `datetime` | YES | NULL | - | - | Atribut data reviewed at |
| 12 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 13 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `asn_profiles`

- **Nama Tabel:** `asn_profiles`
- **Jumlah Kolom:** 13 kolom
- **Definisi:** Struktur persistence data untuk entitas `asn_profiles` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | UNI | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `organization_unit_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data organization unit id |
| 4 | `employee_identifier` | `varchar(64)` | YES | NULL | - | - | Atribut data employee identifier |
| 5 | `employment_status` | `varchar(64)` | YES | NULL | - | - | Atribut data employment status |
| 6 | `origin_type` | `varchar(64)` | NO | NULL | MUL | - | Atribut data origin type |
| 7 | `institution_name` | `varchar(255)` | YES | NULL | - | - | Atribut data institution name |
| 8 | `unit_name` | `varchar(255)` | YES | NULL | - | - | Atribut data unit name |
| 9 | `position_name` | `varchar(255)` | YES | NULL | - | - | Atribut data position name |
| 10 | `rank_grade` | `varchar(128)` | YES | NULL | - | - | Atribut data rank grade |
| 11 | `official_email` | `varchar(255)` | YES | NULL | - | - | Atribut data official email |
| 12 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 13 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `assignment_submission_files`

- **Nama Tabel:** `assignment_submission_files`
- **Jumlah Kolom:** 3 kolom
- **Definisi:** Struktur persistence data untuk entitas `assignment_submission_files` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `submission_id` | `bigint unsigned` | NO | NULL | PRI | - | Atribut data submission id |
| 2 | `file_id` | `bigint unsigned` | NO | NULL | PRI | - | Atribut data file id |
| 3 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `assignment_submissions`

- **Nama Tabel:** `assignment_submissions`
- **Jumlah Kolom:** 13 kolom
- **Definisi:** Struktur persistence data untuk entitas `assignment_submissions` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `assignment_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data assignment id |
| 3 | `enrollment_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data enrollment id |
| 4 | `attempt_number` | `int unsigned` | NO | NULL | - | - | Atribut data attempt number |
| 5 | `status` | `varchar(64)` | NO | `draft` | - | - | Status operasional record sesuai CHECK constraint |
| 6 | `content` | `longtext` | YES | NULL | - | - | Atribut data content |
| 7 | `submitted_at` | `datetime` | YES | NULL | - | - | Atribut data submitted at |
| 8 | `graded_at` | `datetime` | YES | NULL | - | - | Atribut data graded at |
| 9 | `graded_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data graded by |
| 10 | `score` | `decimal(5,2)` | YES | NULL | - | - | Atribut data score |
| 11 | `feedback` | `longtext` | YES | NULL | - | - | Atribut data feedback |
| 12 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 13 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `assignments`

- **Nama Tabel:** `assignments`
- **Jumlah Kolom:** 15 kolom
- **Definisi:** Struktur persistence data untuk entitas `assignments` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `class_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel learning_classes |
| 3 | `lesson_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data lesson id |
| 4 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 5 | `instructions` | `longtext` | YES | NULL | - | - | Atribut data instructions |
| 6 | `due_at` | `datetime` | YES | NULL | MUL | - | Atribut data due at |
| 7 | `is_required` | `tinyint(1)` | NO | `0` | - | - | Atribut data is required |
| 8 | `max_attempts` | `int unsigned` | NO | `1` | - | - | Atribut data max attempts |
| 9 | `allowed_mimes` | `json` | YES | NULL | - | - | Atribut data allowed mimes |
| 10 | `max_file_size_bytes` | `bigint unsigned` | YES | NULL | - | - | Atribut data max file size bytes |
| 11 | `status` | `varchar(64)` | NO | `draft` | - | - | Status operasional record sesuai CHECK constraint |
| 12 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 13 | `updated_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang memperbarui data |
| 14 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 15 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `attendances`

- **Nama Tabel:** `attendances`
- **Jumlah Kolom:** 8 kolom
- **Definisi:** Struktur persistence data untuk entitas `attendances` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `event_registration_id` | `bigint unsigned` | NO | NULL | UNI | - | Atribut data event registration id |
| 3 | `status` | `varchar(64)` | NO | NULL | - | - | Status operasional record sesuai CHECK constraint |
| 4 | `recorded_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data recorded by |
| 5 | `recorded_at` | `datetime` | NO | NULL | - | - | Atribut data recorded at |
| 6 | `notes` | `text` | YES | NULL | - | - | Atribut data notes |
| 7 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 8 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `audit_logs`

- **Nama Tabel:** `audit_logs`
- **Jumlah Kolom:** 13 kolom
- **Definisi:** Struktur persistence data untuk entitas `audit_logs` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `actor_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data actor id |
| 3 | `action` | `varchar(128)` | NO | NULL | MUL | - | Atribut data action |
| 4 | `resource_type` | `varchar(128)` | YES | NULL | MUL | - | Atribut data resource type |
| 5 | `resource_id` | `bigint unsigned` | YES | NULL | - | - | Atribut data resource id |
| 6 | `result` | `varchar(32)` | NO | NULL | MUL | - | Atribut data result |
| 7 | `before_snapshot` | `json` | YES | NULL | - | - | Atribut data before snapshot |
| 8 | `after_snapshot` | `json` | YES | NULL | - | - | Atribut data after snapshot |
| 9 | `reason` | `text` | YES | NULL | - | - | Atribut data reason |
| 10 | `ip_hash` | `char(64)` | YES | NULL | - | - | Atribut data ip hash |
| 11 | `user_agent_hash` | `char(64)` | YES | NULL | - | - | Atribut data user agent hash |
| 12 | `request_id` | `varchar(128)` | YES | NULL | - | - | Atribut data request id |
| 13 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `bookmarks`

- **Nama Tabel:** `bookmarks`
- **Jumlah Kolom:** 5 kolom
- **Definisi:** Struktur persistence data untuk entitas `bookmarks` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `bookmarkable_type` | `varchar(128)` | NO | NULL | MUL | - | Atribut data bookmarkable type |
| 4 | `bookmarkable_id` | `bigint unsigned` | NO | NULL | - | - | Atribut data bookmarkable id |
| 5 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `certificate_sequences`

- **Nama Tabel:** `certificate_sequences`
- **Jumlah Kolom:** 4 kolom
- **Definisi:** Struktur persistence data untuk entitas `certificate_sequences` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `year` | `smallint unsigned` | NO | NULL | PRI | - | Atribut data year |
| 2 | `last_number` | `int unsigned` | NO | `0` | - | - | Atribut data last number |
| 3 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 4 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `certificate_templates`

- **Nama Tabel:** `certificate_templates`
- **Jumlah Kolom:** 10 kolom
- **Definisi:** Struktur persistence data untuk entitas `certificate_templates` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 3 | `source_type` | `varchar(64)` | NO | NULL | MUL | - | Atribut data source type |
| 4 | `template_payload` | `json` | NO | NULL | - | - | Atribut data template payload |
| 5 | `background_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data background file id |
| 6 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 7 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 8 | `updated_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang memperbarui data |
| 9 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 10 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `certificates`

- **Nama Tabel:** `certificates`
- **Jumlah Kolom:** 18 kolom
- **Definisi:** Struktur persistence data untuk entitas `certificates` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `source_type` | `varchar(64)` | NO | NULL | MUL | - | Atribut data source type |
| 3 | `source_id` | `bigint unsigned` | NO | NULL | - | - | Atribut data source id |
| 4 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 5 | `template_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data template id |
| 6 | `certificate_number` | `varchar(128)` | NO | NULL | UNI | - | Atribut data certificate number |
| 7 | `verification_token_hash` | `char(64)` | NO | NULL | UNI | - | Atribut data verification token hash |
| 8 | `status` | `varchar(64)` | NO | `pending` | MUL | - | Status operasional record sesuai CHECK constraint |
| 9 | `issued_at` | `datetime` | YES | NULL | - | - | Atribut data issued at |
| 10 | `expires_at` | `datetime` | YES | NULL | - | - | Atribut data expires at |
| 11 | `revoked_at` | `datetime` | YES | NULL | - | - | Atribut data revoked at |
| 12 | `revoked_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data revoked by |
| 13 | `revocation_reason` | `text` | YES | NULL | - | - | Atribut data revocation reason |
| 14 | `pdf_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data pdf file id |
| 15 | `replaced_by_certificate_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data replaced by certificate id |
| 16 | `issued_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data issued by |
| 17 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 18 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `class_evaluators`

- **Nama Tabel:** `class_evaluators`
- **Jumlah Kolom:** 5 kolom
- **Definisi:** Struktur persistence data untuk entitas `class_evaluators` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `class_id` | `bigint unsigned` | NO | NULL | PRI | - | Foreign Key ke tabel learning_classes |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | PRI | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `assigned_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data assigned by |
| 4 | `assigned_at` | `datetime` | NO | NULL | - | - | Atribut data assigned at |
| 5 | `status` | `varchar(64)` | NO | `active` | - | - | Status operasional record sesuai CHECK constraint |

---

### Tabel: `class_trainers`

- **Nama Tabel:** `class_trainers`
- **Jumlah Kolom:** 6 kolom
- **Definisi:** Struktur persistence data untuk entitas `class_trainers` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `class_id` | `bigint unsigned` | NO | NULL | PRI | - | Foreign Key ke tabel learning_classes |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | PRI | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `assigned_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data assigned by |
| 4 | `assigned_at` | `datetime` | NO | NULL | - | - | Atribut data assigned at |
| 5 | `status` | `varchar(64)` | NO | `active` | - | - | Status operasional record sesuai CHECK constraint |
| 6 | `role` | `varchar(64)` | NO | `primary` | - | - | Atribut data role |

---

### Tabel: `competencies`

- **Nama Tabel:** `competencies`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `competencies` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `key` | `varchar(64)` | NO | NULL | UNI | - | Atribut data key |
| 3 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 4 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 5 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 7 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `competency_certificate_types`

- **Nama Tabel:** `competency_certificate_types`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `competency_certificate_types` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `key` | `varchar(64)` | NO | NULL | UNI | - | Atribut data key |
| 3 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 4 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 5 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 7 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `competency_certificates`

- **Nama Tabel:** `competency_certificates`
- **Jumlah Kolom:** 12 kolom
- **Definisi:** Struktur persistence data untuk entitas `competency_certificates` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `jf_ppbj_record_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data jf ppbj record id |
| 3 | `certificate_type_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data certificate type id |
| 4 | `certificate_number` | `varchar(128)` | YES | NULL | - | - | Atribut data certificate number |
| 5 | `issued_at` | `datetime` | YES | NULL | - | - | Atribut data issued at |
| 6 | `expires_at` | `datetime` | YES | NULL | - | - | Atribut data expires at |
| 7 | `status` | `varchar(64)` | NO | `pending` | - | - | Status operasional record sesuai CHECK constraint |
| 8 | `evidence_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data evidence file id |
| 9 | `verified_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data verified by |
| 10 | `verified_at` | `datetime` | YES | NULL | - | - | Atribut data verified at |
| 11 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 12 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `competency_gap_snapshots`

- **Nama Tabel:** `competency_gap_snapshots`
- **Jumlah Kolom:** 10 kolom
- **Definisi:** Struktur persistence data untuk entitas `competency_gap_snapshots` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `organization_unit_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data organization unit id |
| 3 | `competency_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data competency id |
| 4 | `period_date` | `date` | NO | NULL | MUL | - | Atribut data period date |
| 5 | `required_count` | `int unsigned` | NO | `0` | - | - | Atribut data required count |
| 6 | `available_count` | `int unsigned` | NO | `0` | - | - | Atribut data available count |
| 7 | `gap_count` | `int` | NO | `0` | - | - | Atribut data gap count |
| 8 | `metadata` | `json` | YES | NULL | - | - | Atribut data metadata |
| 9 | `generated_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data generated by |
| 10 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `contact_messages`

- **Nama Tabel:** `contact_messages`
- **Jumlah Kolom:** 13 kolom
- **Definisi:** Struktur persistence data untuk entitas `contact_messages` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `ticket_number` | `varchar(50)` | NO | NULL | UNI | - | Atribut data ticket number |
| 3 | `sender_name` | `varchar(150)` | NO | NULL | - | - | Atribut data sender name |
| 4 | `sender_email` | `varchar(150)` | NO | NULL | - | - | Atribut data sender email |
| 5 | `sender_phone` | `varchar(50)` | YES | NULL | - | - | Atribut data sender phone |
| 6 | `service_category` | `varchar(100)` | YES | NULL | - | - | Atribut data service category |
| 7 | `subject` | `varchar(255)` | NO | NULL | - | - | Atribut data subject |
| 8 | `message` | `text` | NO | NULL | - | - | Atribut data message |
| 9 | `status` | `varchar(50)` | NO | `unread` | MUL | - | Status operasional record sesuai CHECK constraint |
| 10 | `admin_notes` | `text` | YES | NULL | - | - | Atribut data admin notes |
| 11 | `replied_at` | `datetime` | YES | NULL | - | - | Atribut data replied at |
| 12 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 13 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `courses`

- **Nama Tabel:** `courses`
- **Jumlah Kolom:** 25 kolom
- **Definisi:** Struktur persistence data untuk entitas `courses` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `program_id` | `bigint unsigned` | YES | NULL | MUL | - | Foreign Key ke tabel programs |
| 3 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 4 | `slug` | `varchar(255)` | NO | NULL | UNI | - | Pengenal URL-friendly unik |
| 5 | `summary` | `text` | YES | NULL | - | - | Ringkasan singkat konten |
| 6 | `description` | `longtext` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 7 | `target_audience` | `text` | YES | NULL | - | - | Atribut data target audience |
| 8 | `prerequisites` | `text` | YES | NULL | - | - | Atribut data prerequisites |
| 9 | `learning_objectives` | `text` | YES | NULL | - | - | Atribut data learning objectives |
| 10 | `level` | `varchar(64)` | YES | NULL | - | - | Atribut data level |
| 11 | `duration_minutes` | `int unsigned` | YES | NULL | - | - | Atribut data duration minutes |
| 12 | `status` | `varchar(64)` | NO | `draft` | MUL | - | Status operasional record sesuai CHECK constraint |
| 13 | `visibility` | `varchar(64)` | NO | `internal` | - | - | Visibilitas data (public/internal/restricted) |
| 14 | `featured` | `tinyint(1)` | NO | `0` | - | - | Atribut data featured |
| 15 | `carousel_enabled` | `tinyint(1)` | NO | `0` | - | - | Flag penayangan pada Hero Slider / Carousel |
| 16 | `carousel_order` | `int` | YES | NULL | - | - | Nomor urut penayangan slide carousel |
| 17 | `cover_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Foreign Key ke file_assets (gambar sampul) |
| 18 | `cover_url` | `varchar(500)` | YES | NULL | - | - | URL relatif/absolut berkas gambar sampul |
| 19 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 20 | `updated_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang memperbarui data |
| 21 | `published_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data published by |
| 22 | `published_at` | `datetime` | YES | NULL | - | - | Atribut data published at |
| 23 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 24 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |
| 25 | `deleted_at` | `datetime` | YES | NULL | - | - | Penanda soft-delete data (NULL jika aktif) |

---

### Tabel: `email_verifications`

- **Nama Tabel:** `email_verifications`
- **Jumlah Kolom:** 6 kolom
- **Definisi:** Struktur persistence data untuk entitas `email_verifications` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `token_hash` | `char(64)` | NO | NULL | UNI | - | Atribut data token hash |
| 4 | `expires_at` | `datetime` | NO | NULL | - | - | Atribut data expires at |
| 5 | `verified_at` | `datetime` | YES | NULL | - | - | Atribut data verified at |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `enrollments`

- **Nama Tabel:** `enrollments`
- **Jumlah Kolom:** 14 kolom
- **Definisi:** Struktur persistence data untuk entitas `enrollments` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `class_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel learning_classes |
| 3 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 4 | `status` | `varchar(64)` | NO | `pending` | - | - | Status operasional record sesuai CHECK constraint |
| 5 | `enrolled_at` | `datetime` | NO | NULL | - | - | Atribut data enrolled at |
| 6 | `activated_at` | `datetime` | YES | NULL | - | - | Atribut data activated at |
| 7 | `completed_at` | `datetime` | YES | NULL | - | - | Atribut data completed at |
| 8 | `final_score` | `decimal(5,2)` | YES | NULL | - | - | Atribut data final score |
| 9 | `passed_at` | `datetime` | YES | NULL | - | - | Atribut data passed at |
| 10 | `certificate_eligible_at` | `datetime` | YES | NULL | MUL | - | Atribut data certificate eligible at |
| 11 | `withdrawn_at` | `datetime` | YES | NULL | - | - | Atribut data withdrawn at |
| 12 | `cancelled_at` | `datetime` | YES | NULL | - | - | Atribut data cancelled at |
| 13 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 14 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `event_registrations`

- **Nama Tabel:** `event_registrations`
- **Jumlah Kolom:** 10 kolom
- **Definisi:** Struktur persistence data untuk entitas `event_registrations` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `event_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel events |
| 3 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 4 | `status` | `varchar(64)` | NO | `registered` | - | - | Status operasional record sesuai CHECK constraint |
| 5 | `registered_at` | `datetime` | NO | NULL | - | - | Atribut data registered at |
| 6 | `cancelled_at` | `datetime` | YES | NULL | - | - | Atribut data cancelled at |
| 7 | `promoted_at` | `datetime` | YES | NULL | - | - | Atribut data promoted at |
| 8 | `status_reason` | `text` | YES | NULL | - | - | Atribut data status reason |
| 9 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 10 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `events`

- **Nama Tabel:** `events`
- **Jumlah Kolom:** 34 kolom
- **Definisi:** Struktur persistence data untuk entitas `events` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 3 | `slug` | `varchar(255)` | NO | NULL | UNI | - | Pengenal URL-friendly unik |
| 4 | `type` | `varchar(64)` | NO | NULL | - | - | Atribut data type |
| 5 | `mode` | `varchar(32)` | NO | NULL | - | - | Atribut data mode |
| 6 | `status` | `varchar(64)` | NO | `draft` | MUL | - | Status operasional record sesuai CHECK constraint |
| 7 | `visibility` | `varchar(64)` | NO | `internal` | - | - | Visibilitas data (public/internal/restricted) |
| 8 | `summary` | `text` | YES | NULL | - | - | Ringkasan singkat konten |
| 9 | `description` | `longtext` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 10 | `cover_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Foreign Key ke file_assets (gambar sampul) |
| 11 | `is_featured` | `tinyint(1)` | NO | `0` | - | - | Flag konten unggulan pada modul |
| 12 | `carousel_enabled` | `tinyint(1)` | NO | `0` | - | - | Flag penayangan pada Hero Slider / Carousel |
| 13 | `carousel_order` | `int` | YES | NULL | - | - | Nomor urut penayangan slide carousel |
| 14 | `start_at` | `datetime` | NO | NULL | - | - | Atribut data start at |
| 15 | `end_at` | `datetime` | NO | NULL | - | - | Atribut data end at |
| 16 | `location` | `varchar(500)` | YES | NULL | - | - | Atribut data location |
| 17 | `meeting_url` | `varchar(2048)` | YES | NULL | - | - | Atribut data meeting url |
| 18 | `registration_open_at` | `datetime` | YES | NULL | MUL | - | Atribut data registration open at |
| 19 | `registration_close_at` | `datetime` | YES | NULL | - | - | Atribut data registration close at |
| 20 | `capacity` | `int unsigned` | YES | NULL | - | - | Atribut data capacity |
| 21 | `waitlist_enabled` | `tinyint(1)` | NO | `0` | - | - | Atribut data waitlist enabled |
| 22 | `certificate_enabled` | `tinyint(1)` | NO | `0` | - | - | Atribut data certificate enabled |
| 23 | `attendance_code` | `varchar(64)` | YES | NULL | MUL | - | Atribut data attendance code |
| 24 | `attendance_code_hash` | `char(64)` | YES | NULL | - | - | Atribut data attendance code hash |
| 25 | `attendance_open_at` | `datetime` | YES | NULL | - | - | Atribut data attendance open at |
| 26 | `attendance_close_at` | `datetime` | YES | NULL | - | - | Atribut data attendance close at |
| 27 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 28 | `updated_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang memperbarui data |
| 29 | `cancelled_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data cancelled by |
| 30 | `cancelled_at` | `datetime` | YES | NULL | - | - | Atribut data cancelled at |
| 31 | `cancellation_reason` | `text` | YES | NULL | - | - | Atribut data cancellation reason |
| 32 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 33 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |
| 34 | `deleted_at` | `datetime` | YES | NULL | - | - | Penanda soft-delete data (NULL jika aktif) |

---

### Tabel: `exports`

- **Nama Tabel:** `exports`
- **Jumlah Kolom:** 12 kolom
- **Definisi:** Struktur persistence data untuk entitas `exports` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `requested_by` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data requested by |
| 3 | `type` | `varchar(128)` | NO | NULL | - | - | Atribut data type |
| 4 | `status` | `varchar(64)` | NO | `queued` | MUL | - | Status operasional record sesuai CHECK constraint |
| 5 | `filters` | `json` | YES | NULL | - | - | Atribut data filters |
| 6 | `file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data file id |
| 7 | `error_message` | `text` | YES | NULL | - | - | Atribut data error message |
| 8 | `expires_at` | `datetime` | YES | NULL | - | - | Atribut data expires at |
| 9 | `completed_at` | `datetime` | YES | NULL | - | - | Atribut data completed at |
| 10 | `deleted_at` | `datetime` | YES | NULL | - | - | Penanda soft-delete data (NULL jika aktif) |
| 11 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 12 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `external_portals`

- **Nama Tabel:** `external_portals`
- **Jumlah Kolom:** 11 kolom
- **Definisi:** Struktur persistence data untuk entitas `external_portals` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `name` | `varchar(150)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 3 | `acronym` | `varchar(50)` | YES | NULL | - | - | Atribut data acronym |
| 4 | `url` | `varchar(255)` | NO | NULL | - | - | Atribut data url |
| 5 | `logo_url` | `varchar(255)` | YES | NULL | - | - | Atribut data logo url |
| 6 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 7 | `category` | `varchar(50)` | NO | `national` | - | - | Atribut data category |
| 8 | `sort_order` | `int` | NO | `0` | - | - | Urutan tampilan data |
| 9 | `is_active` | `tinyint(1)` | NO | `1` | MUL | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 10 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 11 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `feature_highlights`

- **Nama Tabel:** `feature_highlights`
- **Jumlah Kolom:** 14 kolom
- **Definisi:** Struktur persistence data untuk entitas `feature_highlights` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `section_key` | `varchar(50)` | NO | NULL | MUL | - | Atribut data section key |
| 3 | `code` | `varchar(50)` | YES | NULL | - | - | Atribut data code |
| 4 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 5 | `subtitle` | `varchar(255)` | YES | NULL | - | - | Atribut data subtitle |
| 6 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 7 | `icon_class` | `varchar(100)` | YES | NULL | - | - | Atribut data icon class |
| 8 | `badge_text` | `varchar(100)` | YES | NULL | - | - | Atribut data badge text |
| 9 | `color_hex` | `varchar(50)` | YES | NULL | - | - | Atribut data color hex |
| 10 | `link_url` | `varchar(255)` | YES | NULL | - | - | Atribut data link url |
| 11 | `sort_order` | `int` | NO | `0` | - | - | Urutan tampilan data |
| 12 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 13 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 14 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `feedbacks`

- **Nama Tabel:** `feedbacks`
- **Jumlah Kolom:** 8 kolom
- **Definisi:** Struktur persistence data untuk entitas `feedbacks` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `enrollment_id` | `bigint unsigned` | NO | NULL | UNI | - | Atribut data enrollment id |
| 3 | `rating` | `tinyint unsigned` | YES | NULL | - | - | Atribut data rating |
| 4 | `comment` | `text` | YES | NULL | - | - | Atribut data comment |
| 5 | `status` | `varchar(64)` | NO | `not_started` | - | - | Status operasional record sesuai CHECK constraint |
| 6 | `submitted_at` | `datetime` | YES | NULL | - | - | Atribut data submitted at |
| 7 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 8 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `file_assets`

- **Nama Tabel:** `file_assets`
- **Jumlah Kolom:** 20 kolom
- **Definisi:** Struktur persistence data untuk entitas `file_assets` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `disk` | `varchar(64)` | NO | NULL | - | - | Atribut data disk |
| 3 | `path` | `varchar(1024)` | NO | NULL | - | - | Atribut data path |
| 4 | `original_name` | `varchar(512)` | NO | NULL | - | - | Atribut data original name |
| 5 | `stored_name` | `varchar(255)` | NO | NULL | - | - | Atribut data stored name |
| 6 | `extension` | `varchar(32)` | NO | NULL | - | - | Atribut data extension |
| 7 | `mime_type` | `varchar(255)` | NO | NULL | - | - | Atribut data mime type |
| 8 | `size_bytes` | `bigint unsigned` | NO | NULL | - | - | Atribut data size bytes |
| 9 | `checksum` | `char(64)` | YES | NULL | - | - | Atribut data checksum |
| 10 | `visibility` | `varchar(64)` | NO | `internal` | MUL | - | Visibilitas data (public/internal/restricted) |
| 11 | `lifecycle_status` | `varchar(64)` | NO | `pending_upload` | - | - | Atribut data lifecycle status |
| 12 | `resource_type` | `varchar(128)` | YES | NULL | MUL | - | Atribut data resource type |
| 13 | `resource_id` | `bigint unsigned` | YES | NULL | - | - | Atribut data resource id |
| 14 | `uploaded_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data uploaded by |
| 15 | `available_at` | `datetime` | YES | NULL | - | - | Atribut data available at |
| 16 | `expires_at` | `datetime` | YES | NULL | MUL | - | Atribut data expires at |
| 17 | `deleted_at` | `datetime` | YES | NULL | - | - | Penanda soft-delete data (NULL jika aktif) |
| 18 | `metadata` | `json` | YES | NULL | - | - | Atribut data metadata |
| 19 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 20 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `helpdesk_topics`

- **Nama Tabel:** `helpdesk_topics`
- **Jumlah Kolom:** 8 kolom
- **Definisi:** Struktur persistence data untuk entitas `helpdesk_topics` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `topic_title` | `varchar(150)` | NO | NULL | - | - | Atribut data topic title |
| 3 | `default_message` | `text` | NO | NULL | - | - | Atribut data default message |
| 4 | `icon_class` | `varchar(100)` | YES | NULL | - | - | Atribut data icon class |
| 5 | `sort_order` | `int` | NO | `0` | - | - | Urutan tampilan data |
| 6 | `is_active` | `tinyint(1)` | NO | `1` | MUL | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 7 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 8 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `home_sliders`

- **Nama Tabel:** `home_sliders`
- **Jumlah Kolom:** 13 kolom
- **Definisi:** Struktur persistence data untuk entitas `home_sliders` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `badge_text` | `varchar(100)` | YES | NULL | - | - | Atribut data badge text |
| 3 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 4 | `subtitle` | `text` | YES | NULL | - | - | Atribut data subtitle |
| 5 | `image_url` | `varchar(255)` | YES | NULL | - | - | Atribut data image url |
| 6 | `button_text` | `varchar(100)` | YES | NULL | - | - | Atribut data button text |
| 7 | `button_url` | `varchar(255)` | YES | NULL | - | - | Atribut data button url |
| 8 | `secondary_button_text` | `varchar(100)` | YES | NULL | - | - | Atribut data secondary button text |
| 9 | `secondary_button_url` | `varchar(255)` | YES | NULL | - | - | Atribut data secondary button url |
| 10 | `sort_order` | `int` | NO | `0` | - | - | Urutan tampilan data |
| 11 | `is_active` | `tinyint(1)` | NO | `1` | MUL | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 12 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 13 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `jf_ppbj_levels`

- **Nama Tabel:** `jf_ppbj_levels`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `jf_ppbj_levels` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `key` | `varchar(64)` | NO | NULL | UNI | - | Atribut data key |
| 3 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 4 | `sort_order` | `int unsigned` | NO | `0` | - | - | Urutan tampilan data |
| 5 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 7 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `jf_ppbj_records`

- **Nama Tabel:** `jf_ppbj_records`
- **Jumlah Kolom:** 15 kolom
- **Definisi:** Struktur persistence data untuk entitas `jf_ppbj_records` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | YES | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `organization_unit_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data organization unit id |
| 4 | `employee_identifier` | `varchar(128)` | NO | NULL | - | - | Atribut data employee identifier |
| 5 | `name_snapshot` | `varchar(255)` | NO | NULL | - | - | Atribut data name snapshot |
| 6 | `level_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data level id |
| 7 | `status` | `varchar(64)` | NO | `active` | - | - | Status operasional record sesuai CHECK constraint |
| 8 | `effective_at` | `datetime` | NO | NULL | MUL | - | Atribut data effective at |
| 9 | `ended_at` | `datetime` | YES | NULL | - | - | Atribut data ended at |
| 10 | `change_type` | `varchar(64)` | NO | NULL | - | - | Atribut data change type |
| 11 | `evidence_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data evidence file id |
| 12 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 13 | `updated_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang memperbarui data |
| 14 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 15 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `knowledge_article_categories`

- **Nama Tabel:** `knowledge_article_categories`
- **Jumlah Kolom:** 3 kolom
- **Definisi:** Struktur persistence data untuk entitas `knowledge_article_categories` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `article_id` | `bigint unsigned` | NO | NULL | PRI | - | Atribut data article id |
| 2 | `category_id` | `bigint unsigned` | NO | NULL | PRI | - | Atribut data category id |
| 3 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `knowledge_article_tags`

- **Nama Tabel:** `knowledge_article_tags`
- **Jumlah Kolom:** 3 kolom
- **Definisi:** Struktur persistence data untuk entitas `knowledge_article_tags` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `article_id` | `bigint unsigned` | NO | NULL | PRI | - | Atribut data article id |
| 2 | `tag_id` | `bigint unsigned` | NO | NULL | PRI | - | Atribut data tag id |
| 3 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `knowledge_articles`

- **Nama Tabel:** `knowledge_articles`
- **Jumlah Kolom:** 32 kolom
- **Definisi:** Struktur persistence data untuk entitas `knowledge_articles` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `type` | `varchar(64)` | NO | `article` | MUL | - | Atribut data type |
| 3 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 4 | `slug` | `varchar(255)` | NO | NULL | UNI | - | Pengenal URL-friendly unik |
| 5 | `summary` | `text` | YES | NULL | - | - | Ringkasan singkat konten |
| 6 | `content` | `longtext` | YES | NULL | - | - | Atribut data content |
| 7 | `document_version` | `varchar(50)` | YES | `v1.0` | - | - | Atribut data document version |
| 8 | `document_category` | `varchar(100)` | YES | NULL | - | - | Atribut data document category |
| 9 | `legal_basis` | `text` | YES | NULL | - | - | Atribut data legal basis |
| 10 | `resolution_summary` | `text` | YES | NULL | - | - | Atribut data resolution summary |
| 11 | `case_severity` | `varchar(50)` | YES | NULL | - | - | Atribut data case severity |
| 12 | `procurement_stage` | `varchar(100)` | YES | NULL | - | - | Atribut data procurement stage |
| 13 | `organization_unit` | `varchar(255)` | YES | NULL | - | - | Atribut data organization unit |
| 14 | `source_url` | `varchar(1024)` | YES | NULL | - | - | Atribut data source url |
| 15 | `reading_time` | `int unsigned` | YES | `5` | - | - | Atribut data reading time |
| 16 | `workflow_status` | `varchar(64)` | NO | `draft` | - | - | Status alur kerja persetujuan (draft/submitted/in_review/approved/published/dll) |
| 17 | `visibility` | `varchar(64)` | NO | `internal` | - | - | Visibilitas data (public/internal/restricted) |
| 18 | `featured` | `tinyint(1)` | NO | `0` | MUL | - | Atribut data featured |
| 19 | `carousel_enabled` | `tinyint(1)` | NO | `0` | - | - | Flag penayangan pada Hero Slider / Carousel |
| 20 | `carousel_order` | `int unsigned` | YES | NULL | - | - | Nomor urut penayangan slide carousel |
| 21 | `sort_order` | `int unsigned` | YES | `0` | - | - | Urutan tampilan data |
| 22 | `cover_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Foreign Key ke file_assets (gambar sampul) |
| 23 | `cover_url` | `varchar(1024)` | YES | NULL | - | - | URL relatif/absolut berkas gambar sampul |
| 24 | `attachment_file_id` | `bigint unsigned` | YES | NULL | - | - | Atribut data attachment file id |
| 25 | `author_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data author id |
| 26 | `reviewed_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data reviewed by |
| 27 | `published_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data published by |
| 28 | `published_at` | `datetime` | YES | NULL | - | - | Atribut data published at |
| 29 | `archived_at` | `datetime` | YES | NULL | - | - | Atribut data archived at |
| 30 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 31 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |
| 32 | `deleted_at` | `datetime` | YES | NULL | - | - | Penanda soft-delete data (NULL jika aktif) |

---

### Tabel: `knowledge_categories`

- **Nama Tabel:** `knowledge_categories`
- **Jumlah Kolom:** 9 kolom
- **Definisi:** Struktur persistence data untuk entitas `knowledge_categories` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `parent_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data parent id |
| 3 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 4 | `slug` | `varchar(255)` | NO | NULL | UNI | - | Pengenal URL-friendly unik |
| 5 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 6 | `sort_order` | `int unsigned` | NO | `0` | - | - | Urutan tampilan data |
| 7 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 8 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 9 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `knowledge_media`

- **Nama Tabel:** `knowledge_media`
- **Jumlah Kolom:** 28 kolom
- **Definisi:** Struktur persistence data untuk entitas `knowledge_media` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `article_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data article id |
| 3 | `type` | `varchar(64)` | NO | NULL | MUL | - | Atribut data type |
| 4 | `title` | `varchar(255)` | YES | NULL | - | - | Judul resmi entitas |
| 5 | `caption` | `text` | YES | NULL | - | - | Atribut data caption |
| 6 | `location` | `varchar(255)` | YES | NULL | - | - | Atribut data location |
| 7 | `event_date` | `date` | YES | NULL | - | - | Atribut data event date |
| 8 | `hashtags` | `varchar(500)` | YES | NULL | - | - | Atribut data hashtags |
| 9 | `alt_text` | `varchar(500)` | YES | NULL | - | - | Atribut data alt text |
| 10 | `file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data file id |
| 11 | `slides_data` | `longtext` | YES | NULL | - | - | Atribut data slides data |
| 12 | `cover_url` | `varchar(1024)` | YES | NULL | - | - | URL relatif/absolut berkas gambar sampul |
| 13 | `external_url` | `varchar(2048)` | YES | NULL | - | - | Atribut data external url |
| 14 | `duration` | `varchar(50)` | YES | NULL | - | - | Atribut data duration |
| 15 | `thumbnail_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data thumbnail file id |
| 16 | `workflow_status` | `varchar(64)` | NO | `draft` | - | - | Status alur kerja persetujuan (draft/submitted/in_review/approved/published/dll) |
| 17 | `visibility` | `varchar(64)` | NO | `internal` | - | - | Visibilitas data (public/internal/restricted) |
| 18 | `sort_order` | `int unsigned` | NO | `0` | - | - | Urutan tampilan data |
| 19 | `likes_count` | `int unsigned` | YES | `0` | - | - | Atribut data likes count |
| 20 | `views_count` | `int unsigned` | YES | `0` | - | - | Atribut data views count |
| 21 | `shares_count` | `int unsigned` | YES | `0` | - | - | Atribut data shares count |
| 22 | `featured` | `tinyint(1)` | NO | `0` | - | - | Atribut data featured |
| 23 | `carousel_enabled` | `tinyint(1)` | NO | `0` | - | - | Flag penayangan pada Hero Slider / Carousel |
| 24 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 25 | `published_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data published by |
| 26 | `published_at` | `datetime` | YES | NULL | - | - | Atribut data published at |
| 27 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 28 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `knowledge_tags`

- **Nama Tabel:** `knowledge_tags`
- **Jumlah Kolom:** 5 kolom
- **Definisi:** Struktur persistence data untuk entitas `knowledge_tags` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 3 | `slug` | `varchar(255)` | NO | NULL | UNI | - | Pengenal URL-friendly unik |
| 4 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 5 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `learning_classes`

- **Nama Tabel:** `learning_classes`
- **Jumlah Kolom:** 29 kolom
- **Definisi:** Struktur persistence data untuk entitas `learning_classes` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `course_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel courses |
| 3 | `code` | `varchar(64)` | NO | NULL | - | - | Atribut data code |
| 4 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 5 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 6 | `cover_file_id` | `int unsigned` | YES | NULL | - | - | Foreign Key ke file_assets (gambar sampul) |
| 7 | `cover_url` | `varchar(500)` | YES | NULL | - | - | URL relatif/absolut berkas gambar sampul |
| 8 | `status` | `varchar(64)` | NO | `draft` | - | - | Status operasional record sesuai CHECK constraint |
| 9 | `featured` | `tinyint(1)` | NO | `0` | - | - | Atribut data featured |
| 10 | `carousel_enabled` | `tinyint(1)` | NO | `0` | - | - | Flag penayangan pada Hero Slider / Carousel |
| 11 | `carousel_order` | `int` | YES | NULL | - | - | Nomor urut penayangan slide carousel |
| 12 | `learning_method` | `varchar(64)` | NO | `online` | - | - | Atribut data learning method |
| 13 | `location_or_link` | `varchar(500)` | YES | NULL | - | - | Atribut data location or link |
| 14 | `target_audience` | `text` | YES | NULL | - | - | Atribut data target audience |
| 15 | `registration_open_at` | `datetime` | YES | NULL | MUL | - | Atribut data registration open at |
| 16 | `registration_close_at` | `datetime` | YES | NULL | - | - | Atribut data registration close at |
| 17 | `start_at` | `datetime` | YES | NULL | MUL | - | Atribut data start at |
| 18 | `end_at` | `datetime` | YES | NULL | - | - | Atribut data end at |
| 19 | `capacity` | `int unsigned` | YES | NULL | - | - | Atribut data capacity |
| 20 | `certificate_enabled` | `tinyint(1)` | NO | `0` | - | - | Atribut data certificate enabled |
| 21 | `passing_grade` | `decimal(5,2)` | NO | `70.00` | - | - | Atribut data passing grade |
| 22 | `quiz_weight` | `decimal(5,2)` | NO | `40.00` | - | - | Atribut data quiz weight |
| 23 | `assignment_weight` | `decimal(5,2)` | NO | `0.00` | - | - | Atribut data assignment weight |
| 24 | `post_test_weight` | `decimal(5,2)` | NO | `60.00` | - | - | Atribut data post test weight |
| 25 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 26 | `updated_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang memperbarui data |
| 27 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 28 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |
| 29 | `deleted_at` | `datetime` | YES | NULL | - | - | Penanda soft-delete data (NULL jika aktif) |

---

### Tabel: `learning_modules`

- **Nama Tabel:** `learning_modules`
- **Jumlah Kolom:** 9 kolom
- **Definisi:** Struktur persistence data untuk entitas `learning_modules` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `class_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel learning_classes |
| 3 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 4 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 5 | `sort_order` | `int unsigned` | NO | NULL | - | - | Urutan tampilan data |
| 6 | `is_required` | `tinyint(1)` | NO | `1` | - | - | Atribut data is required |
| 7 | `status` | `varchar(64)` | NO | `draft` | - | - | Status operasional record sesuai CHECK constraint |
| 8 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 9 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `learning_progress`

- **Nama Tabel:** `learning_progress`
- **Jumlah Kolom:** 9 kolom
- **Definisi:** Struktur persistence data untuk entitas `learning_progress` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `enrollment_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data enrollment id |
| 3 | `lesson_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data lesson id |
| 4 | `status` | `varchar(64)` | NO | `not_started` | - | - | Status operasional record sesuai CHECK constraint |
| 5 | `started_at` | `datetime` | YES | NULL | - | - | Atribut data started at |
| 6 | `completed_at` | `datetime` | YES | NULL | - | - | Atribut data completed at |
| 7 | `progress_percent` | `decimal(5,2)` | NO | `0.00` | - | - | Atribut data progress percent |
| 8 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 9 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `lessons`

- **Nama Tabel:** `lessons`
- **Jumlah Kolom:** 16 kolom
- **Definisi:** Struktur persistence data untuk entitas `lessons` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `module_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data module id |
| 3 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 4 | `slug` | `varchar(255)` | YES | NULL | MUL | - | Pengenal URL-friendly unik |
| 5 | `content_type` | `varchar(64)` | NO | NULL | - | - | Atribut data content type |
| 6 | `content` | `longtext` | YES | NULL | - | - | Atribut data content |
| 7 | `file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data file id |
| 8 | `external_url` | `varchar(2048)` | YES | NULL | - | - | Atribut data external url |
| 9 | `sort_order` | `int unsigned` | NO | NULL | - | - | Urutan tampilan data |
| 10 | `is_required` | `tinyint(1)` | NO | `1` | - | - | Atribut data is required |
| 11 | `status` | `varchar(64)` | NO | `draft` | - | - | Status operasional record sesuai CHECK constraint |
| 12 | `visibility` | `varchar(64)` | NO | `internal` | - | - | Visibilitas data (public/internal/restricted) |
| 13 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 14 | `updated_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang memperbarui data |
| 15 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 16 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `migrations`

- **Nama Tabel:** `migrations`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `migrations` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `version` | `varchar(255)` | NO | NULL | - | - | Atribut data version |
| 3 | `class` | `varchar(255)` | NO | NULL | - | - | Atribut data class |
| 4 | `group` | `varchar(255)` | NO | NULL | - | - | Atribut data group |
| 5 | `namespace` | `varchar(255)` | NO | NULL | - | - | Atribut data namespace |
| 6 | `time` | `int` | NO | NULL | - | - | Atribut data time |
| 7 | `batch` | `int unsigned` | NO | NULL | - | - | Atribut data batch |

---

### Tabel: `news`

- **Nama Tabel:** `news`
- **Jumlah Kolom:** 26 kolom
- **Definisi:** Struktur persistence data untuk entitas `news` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `category_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data category id |
| 3 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 4 | `slug` | `varchar(255)` | NO | NULL | UNI | - | Pengenal URL-friendly unik |
| 5 | `summary` | `text` | YES | NULL | - | - | Ringkasan singkat konten |
| 6 | `reading_time` | `varchar(50)` | YES | NULL | - | - | Atribut data reading time |
| 7 | `tags` | `varchar(255)` | YES | NULL | - | - | Atribut data tags |
| 8 | `content` | `longtext` | YES | NULL | - | - | Atribut data content |
| 9 | `source_name` | `varchar(150)` | YES | NULL | - | - | Atribut data source name |
| 10 | `source_url` | `varchar(500)` | YES | NULL | - | - | Atribut data source url |
| 11 | `cover_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Foreign Key ke file_assets (gambar sampul) |
| 12 | `cover_caption` | `varchar(255)` | YES | NULL | - | - | Atribut data cover caption |
| 13 | `is_featured` | `tinyint(1)` | NO | `0` | - | - | Flag konten unggulan pada modul |
| 14 | `carousel_enabled` | `tinyint(1)` | NO | `0` | - | - | Flag penayangan pada Hero Slider / Carousel |
| 15 | `carousel_order` | `int` | YES | NULL | - | - | Nomor urut penayangan slide carousel |
| 16 | `workflow_status` | `varchar(64)` | NO | `draft` | - | - | Status alur kerja persetujuan (draft/submitted/in_review/approved/published/dll) |
| 17 | `visibility` | `varchar(64)` | NO | `internal` | - | - | Visibilitas data (public/internal/restricted) |
| 18 | `published_at` | `datetime` | YES | NULL | - | - | Atribut data published at |
| 19 | `scheduled_at` | `datetime` | YES | NULL | - | - | Atribut data scheduled at |
| 20 | `view_count` | `bigint unsigned` | NO | `0` | - | - | Atribut data view count |
| 21 | `author_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data author id |
| 22 | `reviewed_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data reviewed by |
| 23 | `published_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data published by |
| 24 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 25 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |
| 26 | `deleted_at` | `datetime` | YES | NULL | - | - | Penanda soft-delete data (NULL jika aktif) |

---

### Tabel: `news_categories`

- **Nama Tabel:** `news_categories`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `news_categories` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 3 | `slug` | `varchar(255)` | NO | NULL | UNI | - | Pengenal URL-friendly unik |
| 4 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 5 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 7 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `newsletter_subscribers`

- **Nama Tabel:** `newsletter_subscribers`
- **Jumlah Kolom:** 6 kolom
- **Definisi:** Struktur persistence data untuk entitas `newsletter_subscribers` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `email` | `varchar(150)` | NO | NULL | UNI | - | Alamat surel resmi unik |
| 3 | `status` | `varchar(50)` | NO | `subscribed` | MUL | - | Status operasional record sesuai CHECK constraint |
| 4 | `subscribed_at` | `datetime` | YES | NULL | - | - | Atribut data subscribed at |
| 5 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 6 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `notifications`

- **Nama Tabel:** `notifications`
- **Jumlah Kolom:** 10 kolom
- **Definisi:** Struktur persistence data untuk entitas `notifications` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `channel` | `varchar(64)` | NO | `database` | MUL | - | Atribut data channel |
| 4 | `type` | `varchar(128)` | NO | NULL | - | - | Atribut data type |
| 5 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 6 | `body` | `text` | YES | NULL | - | - | Atribut data body |
| 7 | `data` | `json` | YES | NULL | - | - | Atribut data data |
| 8 | `read_at` | `datetime` | YES | NULL | - | - | Atribut data read at |
| 9 | `sent_at` | `datetime` | YES | NULL | - | - | Atribut data sent at |
| 10 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `organization_units`

- **Nama Tabel:** `organization_units`
- **Jumlah Kolom:** 9 kolom
- **Definisi:** Struktur persistence data untuk entitas `organization_units` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `parent_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data parent id |
| 3 | `code` | `varchar(64)` | NO | NULL | UNI | - | Atribut data code |
| 4 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 5 | `type` | `varchar(64)` | NO | NULL | MUL | - | Atribut data type |
| 6 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 7 | `sort_order` | `int unsigned` | NO | `0` | - | - | Urutan tampilan data |
| 8 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 9 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `page_banners`

- **Nama Tabel:** `page_banners`
- **Jumlah Kolom:** 16 kolom
- **Definisi:** Struktur persistence data untuk entitas `page_banners` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `page_key` | `varchar(50)` | NO | NULL | UNI | - | Atribut data page key |
| 3 | `hero_badge` | `varchar(100)` | YES | NULL | - | - | Atribut data hero badge |
| 4 | `hero_title` | `varchar(255)` | YES | NULL | - | - | Atribut data hero title |
| 5 | `hero_subtitle` | `text` | YES | NULL | - | - | Atribut data hero subtitle |
| 6 | `hero_bg_image` | `varchar(255)` | YES | NULL | - | - | Atribut data hero bg image |
| 7 | `search_placeholder` | `varchar(255)` | YES | NULL | - | - | Atribut data search placeholder |
| 8 | `cta_badge` | `varchar(100)` | YES | NULL | - | - | Atribut data cta badge |
| 9 | `cta_title` | `varchar(255)` | YES | NULL | - | - | Atribut data cta title |
| 10 | `cta_lead` | `text` | YES | NULL | - | - | Atribut data cta lead |
| 11 | `cta_primary_btn_text` | `varchar(100)` | YES | NULL | - | - | Atribut data cta primary btn text |
| 12 | `cta_primary_btn_url` | `varchar(255)` | YES | NULL | - | - | Atribut data cta primary btn url |
| 13 | `cta_secondary_btn_text` | `varchar(100)` | YES | NULL | - | - | Atribut data cta secondary btn text |
| 14 | `cta_secondary_btn_url` | `varchar(255)` | YES | NULL | - | - | Atribut data cta secondary btn url |
| 15 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 16 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `password_resets`

- **Nama Tabel:** `password_resets`
- **Jumlah Kolom:** 6 kolom
- **Definisi:** Struktur persistence data untuk entitas `password_resets` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `token_hash` | `char(64)` | NO | NULL | UNI | - | Atribut data token hash |
| 4 | `expires_at` | `datetime` | NO | NULL | - | - | Atribut data expires at |
| 5 | `used_at` | `datetime` | YES | NULL | - | - | Atribut data used at |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `permissions`

- **Nama Tabel:** `permissions`
- **Jumlah Kolom:** 8 kolom
- **Definisi:** Struktur persistence data untuk entitas `permissions` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `key` | `varchar(128)` | NO | NULL | UNI | - | Atribut data key |
| 3 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 4 | `domain` | `varchar(64)` | NO | NULL | MUL | - | Atribut data domain |
| 5 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 6 | `is_system` | `tinyint(1)` | NO | `1` | - | - | Atribut data is system |
| 7 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 8 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `pre_tests`

- **Nama Tabel:** `pre_tests`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `pre_tests` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `class_id` | `bigint unsigned` | NO | NULL | UNI | - | Foreign Key ke tabel learning_classes |
| 3 | `is_enabled` | `tinyint(1)` | NO | `0` | - | - | Atribut data is enabled |
| 4 | `attempt_limit` | `int unsigned` | NO | `1` | - | - | Atribut data attempt limit |
| 5 | `status` | `varchar(64)` | NO | `draft` | - | - | Status operasional record sesuai CHECK constraint |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 7 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `procurement_roles`

- **Nama Tabel:** `procurement_roles`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `procurement_roles` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `key` | `varchar(64)` | NO | NULL | UNI | - | Atribut data key |
| 3 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 4 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 5 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 7 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `procurement_stages`

- **Nama Tabel:** `procurement_stages`
- **Jumlah Kolom:** 8 kolom
- **Definisi:** Struktur persistence data untuk entitas `procurement_stages` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `key` | `varchar(64)` | NO | NULL | UNI | - | Atribut data key |
| 3 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 4 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 5 | `sort_order` | `int unsigned` | NO | `0` | - | - | Urutan tampilan data |
| 6 | `is_active` | `tinyint` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 7 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 8 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `profile_change_requests`

- **Nama Tabel:** `profile_change_requests`
- **Jumlah Kolom:** 12 kolom
- **Definisi:** Struktur persistence data untuk entitas `profile_change_requests` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `status` | `varchar(64)` | NO | `draft` | MUL | - | Status operasional record sesuai CHECK constraint |
| 4 | `requested_payload` | `json` | NO | NULL | - | - | Atribut data requested payload |
| 5 | `reason` | `text` | YES | NULL | - | - | Atribut data reason |
| 6 | `reviewed_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data reviewed by |
| 7 | `reviewed_at` | `datetime` | YES | NULL | - | - | Atribut data reviewed at |
| 8 | `review_notes` | `text` | YES | NULL | - | - | Atribut data review notes |
| 9 | `applied_at` | `datetime` | YES | NULL | - | - | Atribut data applied at |
| 10 | `archived_at` | `datetime` | YES | NULL | - | - | Atribut data archived at |
| 11 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 12 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `profile_verifications`

- **Nama Tabel:** `profile_verifications`
- **Jumlah Kolom:** 11 kolom
- **Definisi:** Struktur persistence data untuk entitas `profile_verifications` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `verification_type` | `varchar(64)` | NO | NULL | - | - | Atribut data verification type |
| 4 | `status` | `varchar(64)` | NO | `pending` | MUL | - | Status operasional record sesuai CHECK constraint |
| 5 | `requested_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data requested by |
| 6 | `reviewed_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data reviewed by |
| 7 | `reason` | `text` | YES | NULL | - | - | Atribut data reason |
| 8 | `notes` | `text` | YES | NULL | - | - | Atribut data notes |
| 9 | `reviewed_at` | `datetime` | YES | NULL | - | - | Atribut data reviewed at |
| 10 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 11 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `programs`

- **Nama Tabel:** `programs`
- **Jumlah Kolom:** 22 kolom
- **Definisi:** Struktur persistence data untuk entitas `programs` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 3 | `slug` | `varchar(255)` | NO | NULL | UNI | - | Pengenal URL-friendly unik |
| 4 | `summary` | `text` | YES | NULL | - | - | Ringkasan singkat konten |
| 5 | `description` | `longtext` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 6 | `target_audience` | `text` | YES | NULL | - | - | Atribut data target audience |
| 7 | `prerequisites` | `text` | YES | NULL | - | - | Atribut data prerequisites |
| 8 | `learning_objectives` | `text` | YES | NULL | - | - | Atribut data learning objectives |
| 9 | `status` | `varchar(64)` | NO | `draft` | MUL | - | Status operasional record sesuai CHECK constraint |
| 10 | `visibility` | `varchar(64)` | NO | `internal` | - | - | Visibilitas data (public/internal/restricted) |
| 11 | `featured` | `tinyint(1)` | NO | `0` | - | - | Atribut data featured |
| 12 | `carousel_enabled` | `tinyint(1)` | NO | `0` | - | - | Flag penayangan pada Hero Slider / Carousel |
| 13 | `carousel_order` | `int` | YES | NULL | - | - | Nomor urut penayangan slide carousel |
| 14 | `cover_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Foreign Key ke file_assets (gambar sampul) |
| 15 | `cover_url` | `varchar(500)` | YES | NULL | - | - | URL relatif/absolut berkas gambar sampul |
| 16 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 17 | `updated_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang memperbarui data |
| 18 | `published_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data published by |
| 19 | `published_at` | `datetime` | YES | NULL | - | - | Atribut data published at |
| 20 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 21 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |
| 22 | `deleted_at` | `datetime` | YES | NULL | - | - | Penanda soft-delete data (NULL jika aktif) |

---

### Tabel: `provider_profiles`

- **Nama Tabel:** `provider_profiles`
- **Jumlah Kolom:** 12 kolom
- **Definisi:** Struktur persistence data untuk entitas `provider_profiles` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | UNI | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `business_name` | `varchar(255)` | NO | NULL | - | - | Atribut data business name |
| 4 | `business_form` | `varchar(64)` | NO | NULL | - | - | Atribut data business form |
| 5 | `business_field` | `varchar(255)` | YES | NULL | - | - | Atribut data business field |
| 6 | `business_identifier` | `varchar(64)` | YES | NULL | - | - | Atribut data business identifier |
| 7 | `registrant_position` | `varchar(255)` | NO | NULL | - | - | Atribut data registrant position |
| 8 | `responsible_person` | `varchar(255)` | NO | NULL | - | - | Atribut data responsible person |
| 9 | `business_email` | `varchar(255)` | YES | NULL | - | - | Atribut data business email |
| 10 | `business_address` | `text` | YES | NULL | - | - | Atribut data business address |
| 11 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 12 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `public_profiles`

- **Nama Tabel:** `public_profiles`
- **Jumlah Kolom:** 9 kolom
- **Definisi:** Struktur persistence data untuk entitas `public_profiles` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | UNI | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `national_identifier` | `varchar(32)` | YES | NULL | - | - | Atribut data national identifier |
| 4 | `occupation_status` | `varchar(128)` | NO | NULL | - | - | Atribut data occupation status |
| 5 | `organization_name` | `varchar(255)` | YES | NULL | - | - | Atribut data organization name |
| 6 | `interest_areas` | `json` | YES | NULL | - | - | Atribut data interest areas |
| 7 | `usage_purpose` | `json` | YES | NULL | - | - | Atribut data usage purpose |
| 8 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 9 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `quiz_answers`

- **Nama Tabel:** `quiz_answers`
- **Jumlah Kolom:** 8 kolom
- **Definisi:** Struktur persistence data untuk entitas `quiz_answers` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `attempt_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data attempt id |
| 3 | `question_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data question id |
| 4 | `answer_payload` | `json` | YES | NULL | - | - | Atribut data answer payload |
| 5 | `is_correct` | `tinyint(1)` | YES | NULL | - | - | Atribut data is correct |
| 6 | `score_awarded` | `decimal(5,2)` | YES | NULL | - | - | Atribut data score awarded |
| 7 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 8 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `quiz_attempts`

- **Nama Tabel:** `quiz_attempts`
- **Jumlah Kolom:** 13 kolom
- **Definisi:** Struktur persistence data untuk entitas `quiz_attempts` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `quiz_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data quiz id |
| 3 | `enrollment_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data enrollment id |
| 4 | `attempt_number` | `int unsigned` | NO | NULL | - | - | Atribut data attempt number |
| 5 | `status` | `varchar(64)` | NO | `started` | - | - | Status operasional record sesuai CHECK constraint |
| 6 | `started_at` | `datetime` | NO | NULL | - | - | Atribut data started at |
| 7 | `submitted_at` | `datetime` | YES | NULL | - | - | Atribut data submitted at |
| 8 | `graded_at` | `datetime` | YES | NULL | - | - | Atribut data graded at |
| 9 | `score` | `decimal(5,2)` | YES | NULL | - | - | Atribut data score |
| 10 | `invalidated_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data invalidated by |
| 11 | `invalidated_reason` | `text` | YES | NULL | - | - | Atribut data invalidated reason |
| 12 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 13 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `quiz_options`

- **Nama Tabel:** `quiz_options`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `quiz_options` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `question_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data question id |
| 3 | `option_text` | `text` | NO | NULL | - | - | Atribut data option text |
| 4 | `is_correct` | `tinyint(1)` | NO | `0` | - | - | Atribut data is correct |
| 5 | `sort_order` | `int unsigned` | NO | NULL | - | - | Urutan tampilan data |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 7 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `quiz_questions`

- **Nama Tabel:** `quiz_questions`
- **Jumlah Kolom:** 9 kolom
- **Definisi:** Struktur persistence data untuk entitas `quiz_questions` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `quiz_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data quiz id |
| 3 | `type` | `varchar(64)` | NO | NULL | - | - | Atribut data type |
| 4 | `prompt` | `longtext` | NO | NULL | - | - | Atribut data prompt |
| 5 | `points` | `decimal(5,2)` | NO | `1.00` | - | - | Atribut data points |
| 6 | `sort_order` | `int unsigned` | NO | NULL | - | - | Urutan tampilan data |
| 7 | `explanation` | `text` | YES | NULL | - | - | Atribut data explanation |
| 8 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 9 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `quizzes`

- **Nama Tabel:** `quizzes`
- **Jumlah Kolom:** 12 kolom
- **Definisi:** Struktur persistence data untuk entitas `quizzes` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `class_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel learning_classes |
| 3 | `lesson_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data lesson id |
| 4 | `type` | `varchar(64)` | NO | NULL | - | - | Atribut data type |
| 5 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 6 | `instructions` | `text` | YES | NULL | - | - | Atribut data instructions |
| 7 | `attempt_limit` | `int unsigned` | NO | `1` | - | - | Atribut data attempt limit |
| 8 | `duration_minutes` | `int unsigned` | YES | NULL | - | - | Atribut data duration minutes |
| 9 | `is_required` | `tinyint(1)` | NO | `0` | - | - | Atribut data is required |
| 10 | `status` | `varchar(64)` | NO | `draft` | - | - | Status operasional record sesuai CHECK constraint |
| 11 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 12 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `regulation_categories`

- **Nama Tabel:** `regulation_categories`
- **Jumlah Kolom:** 9 kolom
- **Definisi:** Struktur persistence data untuk entitas `regulation_categories` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 3 | `slug` | `varchar(255)` | NO | NULL | UNI | - | Pengenal URL-friendly unik |
| 4 | `icon_class` | `varchar(100)` | YES | `solar:document-outline` | - | - | Atribut data icon class |
| 5 | `icon_file_id` | `int unsigned` | YES | NULL | - | - | Atribut data icon file id |
| 6 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 7 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 8 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 9 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `regulation_competencies`

- **Nama Tabel:** `regulation_competencies`
- **Jumlah Kolom:** 5 kolom
- **Definisi:** Struktur persistence data untuk entitas `regulation_competencies` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `regulation_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data regulation id |
| 3 | `competency_id` | `bigint unsigned` | NO | NULL | - | - | Atribut data competency id |
| 4 | `relevance` | `varchar(32)` | YES | NULL | - | - | Atribut data relevance |
| 5 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `regulation_downloads`

- **Nama Tabel:** `regulation_downloads`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `regulation_downloads` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `regulation_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data regulation id |
| 3 | `version_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data version id |
| 4 | `file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data file id |
| 5 | `user_id` | `bigint unsigned` | YES | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 6 | `access_type` | `varchar(64)` | NO | NULL | - | - | Atribut data access type |
| 7 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `regulation_procurement_stages`

- **Nama Tabel:** `regulation_procurement_stages`
- **Jumlah Kolom:** 5 kolom
- **Definisi:** Struktur persistence data untuk entitas `regulation_procurement_stages` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `regulation_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data regulation id |
| 3 | `procurement_stage_id` | `bigint unsigned` | NO | NULL | - | - | Atribut data procurement stage id |
| 4 | `relevance` | `varchar(32)` | YES | NULL | - | - | Atribut data relevance |
| 5 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `regulation_relations`

- **Nama Tabel:** `regulation_relations`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `regulation_relations` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `source_regulation_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data source regulation id |
| 3 | `target_regulation_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data target regulation id |
| 4 | `relation_type` | `varchar(64)` | NO | NULL | - | - | Atribut data relation type |
| 5 | `notes` | `text` | YES | NULL | - | - | Atribut data notes |
| 6 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 7 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `regulation_versions`

- **Nama Tabel:** `regulation_versions`
- **Jumlah Kolom:** 8 kolom
- **Definisi:** Struktur persistence data untuk entitas `regulation_versions` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `regulation_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data regulation id |
| 3 | `version_number` | `int unsigned` | NO | NULL | - | - | Atribut data version number |
| 4 | `file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data file id |
| 5 | `changelog` | `text` | YES | NULL | - | - | Atribut data changelog |
| 6 | `is_current` | `tinyint(1)` | NO | `0` | - | - | Atribut data is current |
| 7 | `uploaded_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data uploaded by |
| 8 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `regulations`

- **Nama Tabel:** `regulations`
- **Jumlah Kolom:** 30 kolom
- **Definisi:** Struktur persistence data untuk entitas `regulations` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `category_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data category id |
| 3 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 4 | `slug` | `varchar(255)` | NO | NULL | UNI | - | Pengenal URL-friendly unik |
| 5 | `document_number` | `varchar(255)` | NO | NULL | MUL | - | Atribut data document number |
| 6 | `document_year` | `smallint unsigned` | NO | NULL | - | - | Atribut data document year |
| 7 | `issuer_name` | `varchar(255)` | NO | NULL | - | - | Atribut data issuer name |
| 8 | `official_date` | `datetime` | YES | NULL | - | - | Atribut data official date |
| 9 | `effective_date` | `datetime` | YES | NULL | - | - | Atribut data effective date |
| 10 | `summary` | `text` | YES | NULL | - | - | Ringkasan singkat konten |
| 11 | `keywords` | `varchar(500)` | YES | NULL | - | - | Atribut data keywords |
| 12 | `official_source_url` | `varchar(2048)` | YES | NULL | - | - | Atribut data official source url |
| 13 | `workflow_status` | `varchar(64)` | NO | `draft` | - | - | Status alur kerja persetujuan (draft/submitted/in_review/approved/published/dll) |
| 14 | `legal_status` | `varchar(64)` | NO | `active` | - | - | Atribut data legal status |
| 15 | `visibility` | `varchar(64)` | NO | `internal` | - | - | Visibilitas data (public/internal/restricted) |
| 16 | `is_featured` | `tinyint(1)` | YES | `0` | - | - | Flag konten unggulan pada modul |
| 17 | `carousel_enabled` | `tinyint(1)` | NO | `0` | - | - | Flag penayangan pada Hero Slider / Carousel |
| 18 | `carousel_order` | `int` | YES | NULL | - | - | Nomor urut penayangan slide carousel |
| 19 | `current_version_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data current version id |
| 20 | `cover_file_id` | `int unsigned` | YES | NULL | - | - | Foreign Key ke file_assets (gambar sampul) |
| 21 | `cover_image_path` | `varchar(500)` | YES | NULL | - | - | Atribut data cover image path |
| 22 | `created_by` | `bigint unsigned` | YES | NULL | MUL | - | ID user yang membuat data |
| 23 | `reviewed_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data reviewed by |
| 24 | `reviewed_at` | `datetime` | YES | NULL | - | - | Atribut data reviewed at |
| 25 | `published_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data published by |
| 26 | `published_at` | `datetime` | YES | NULL | MUL | - | Atribut data published at |
| 27 | `archived_at` | `datetime` | YES | NULL | - | - | Atribut data archived at |
| 28 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 29 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |
| 30 | `deleted_at` | `datetime` | YES | NULL | - | - | Penanda soft-delete data (NULL jika aktif) |

---

### Tabel: `role_permissions`

- **Nama Tabel:** `role_permissions`
- **Jumlah Kolom:** 4 kolom
- **Definisi:** Struktur persistence data untuk entitas `role_permissions` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `role_id` | `bigint unsigned` | NO | NULL | PRI | - | Foreign Key ke tabel roles |
| 2 | `permission_id` | `bigint unsigned` | NO | NULL | PRI | - | Atribut data permission id |
| 3 | `granted_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data granted by |
| 4 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `role_requests`

- **Nama Tabel:** `role_requests`
- **Jumlah Kolom:** 11 kolom
- **Definisi:** Struktur persistence data untuk entitas `role_requests` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `requested_role_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data requested role id |
| 4 | `status` | `varchar(64)` | NO | `draft` | MUL | - | Status operasional record sesuai CHECK constraint |
| 5 | `reason` | `text` | YES | NULL | - | - | Atribut data reason |
| 6 | `reviewed_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data reviewed by |
| 7 | `reviewed_at` | `datetime` | YES | NULL | - | - | Atribut data reviewed at |
| 8 | `review_notes` | `text` | YES | NULL | - | - | Atribut data review notes |
| 9 | `archived_at` | `datetime` | YES | NULL | - | - | Atribut data archived at |
| 10 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 11 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `roles`

- **Nama Tabel:** `roles`
- **Jumlah Kolom:** 7 kolom
- **Definisi:** Struktur persistence data untuk entitas `roles` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `key` | `varchar(64)` | NO | NULL | UNI | - | Atribut data key |
| 3 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 4 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 5 | `is_system` | `tinyint(1)` | NO | `1` | - | - | Atribut data is system |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 7 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `rup_audit_batches`

- **Nama Tabel:** `rup_audit_batches`
- **Jumlah Kolom:** 21 kolom
- **Definisi:** Struktur persistence data untuk entitas `rup_audit_batches` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `int unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `batch_uuid` | `varchar(36)` | NO | NULL | UNI | - | Atribut data batch uuid |
| 3 | `user_id` | `int unsigned` | YES | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 4 | `filename` | `varchar(255)` | NO | NULL | - | - | Atribut data filename |
| 5 | `file_size` | `bigint` | NO | `0` | - | - | Atribut data file size |
| 6 | `total_rows_raw` | `int` | NO | `0` | - | - | Atribut data total rows raw |
| 7 | `total_packages` | `int` | NO | `0` | - | - | Atribut data total packages |
| 8 | `total_pagu` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data total pagu |
| 9 | `total_pagu_penyedia` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data total pagu penyedia |
| 10 | `total_pagu_swakelola` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data total pagu swakelola |
| 11 | `total_opd` | `int` | NO | `0` | - | - | Atribut data total opd |
| 12 | `avg_rci_score` | `decimal(5,2)` | NO | `0.00` | - | - | Atribut data avg rci score |
| 13 | `status` | `enum('processing','completed','failed')` | NO | `processing` | - | - | Status operasional record sesuai CHECK constraint |
| 14 | `previous_batch_id` | `int unsigned` | YES | NULL | - | - | Atribut data previous batch id |
| 15 | `macro_summary` | `json` | YES | NULL | - | - | Atribut data macro summary |
| 16 | `schema_mode` | `varchar(30)` | NO | `CORE_12_COLS` | - | - | Atribut data schema mode |
| 17 | `tahun_anggaran` | `int` | NO | `2026` | - | - | Atribut data tahun anggaran |
| 18 | `nama_instansi` | `varchar(255)` | NO | `Pemerintah Provinsi Lampung` | - | - | Atribut data nama instansi |
| 19 | `notes` | `text` | YES | NULL | - | - | Atribut data notes |
| 20 | `created_at` | `datetime` | YES | NULL | MUL | - | Waktu pembuatan data (UTC timestamp) |
| 21 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `rup_audit_clusters`

- **Nama Tabel:** `rup_audit_clusters`
- **Jumlah Kolom:** 20 kolom
- **Definisi:** Struktur persistence data untuk entitas `rup_audit_clusters` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `batch_id` | `int unsigned` | NO | NULL | MUL | - | Foreign Key ke batch terkait |
| 3 | `cluster_uuid` | `varchar(50)` | NO | NULL | - | - | Atribut data cluster uuid |
| 4 | `kode_satker_sipd` | `varchar(50)` | NO | `` | - | - | Atribut data kode satker sipd |
| 5 | `nama_opd_clean` | `varchar(255)` | NO | NULL | - | - | Atribut data nama opd clean |
| 6 | `budget_cycle` | `enum('MURNI','PERUBAHAN')` | NO | `MURNI` | - | - | Atribut data budget cycle |
| 7 | `nama_komoditas_klaster` | `varchar(255)` | NO | NULL | - | - | Atribut data nama komoditas klaster |
| 8 | `lokasi_klaster` | `varchar(255)` | NO | `ALL` | - | - | Atribut data lokasi klaster |
| 9 | `jenis_pengadaan` | `varchar(100)` | NO | `Barang` | - | - | Atribut data jenis pengadaan |
| 10 | `metode_dominan` | `varchar(100)` | NO | `Pengadaan Langsung` | - | - | Atribut data metode dominan |
| 11 | `jumlah_paket_terpecah` | `int` | NO | `0` | - | - | Atribut data jumlah paket terpecah |
| 12 | `nilai_paket_terkecil` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data nilai paket terkecil |
| 13 | `nilai_paket_terbesar` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data nilai paket terbesar |
| 14 | `total_pagu_klaster` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data total pagu klaster |
| 15 | `batas_regulasi_pl` | `decimal(20,2)` | NO | `200000000.00` | - | - | Atribut data batas regulasi pl |
| 16 | `kategori_temuan` | `varchar(100)` | NO | NULL | - | - | Atribut data kategori temuan |
| 17 | `severity` | `enum('KRITIS','TINGGI','SEDANG','RENDAH','INFO')` | NO | `TINGGI` | - | - | Atribut data severity |
| 18 | `daftar_kode_rup` | `json` | YES | NULL | - | - | Atribut data daftar kode rup |
| 19 | `instruksi_konsolidasi` | `text` | NO | NULL | - | - | Atribut data instruksi konsolidasi |
| 20 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `rup_audit_opd_summaries`

- **Nama Tabel:** `rup_audit_opd_summaries`
- **Jumlah Kolom:** 25 kolom
- **Definisi:** Struktur persistence data untuk entitas `rup_audit_opd_summaries` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `batch_id` | `int unsigned` | NO | NULL | MUL | - | Foreign Key ke batch terkait |
| 3 | `kode_satker_sipd` | `varchar(50)` | NO | `` | - | - | Atribut data kode satker sipd |
| 4 | `nama_opd_clean` | `varchar(255)` | NO | NULL | - | - | Atribut data nama opd clean |
| 5 | `total_paket` | `int` | NO | `0` | - | - | Atribut data total paket |
| 6 | `total_pagu` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data total pagu |
| 7 | `pagu_penyedia` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data pagu penyedia |
| 8 | `pagu_swakelola` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data pagu swakelola |
| 9 | `pagu_dpa` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data pagu dpa |
| 10 | `persen_cakupan_dpa` | `decimal(5,2)` | NO | `100.00` | - | - | Atribut data persen cakupan dpa |
| 11 | `persen_epurchasing` | `decimal(5,2)` | NO | `0.00` | - | - | Atribut data persen epurchasing |
| 12 | `persen_umk_penyedia` | `decimal(5,2)` | NO | `0.00` | - | - | Atribut data persen umk penyedia |
| 13 | `persen_umk_eligible_le15m` | `decimal(5,2)` | NO | `0.00` | - | - | Atribut data persen umk eligible le15m |
| 14 | `persen_umk_total_rup` | `decimal(5,2)` | NO | `0.00` | - | - | Atribut data persen umk total rup |
| 15 | `persen_pdn` | `decimal(5,2)` | NO | `0.00` | - | - | Atribut data persen pdn |
| 16 | `rasio_penunjukan_langsung` | `decimal(5,2)` | NO | `0.00` | - | - | Atribut data rasio penunjukan langsung |
| 17 | `temuan_kritis` | `int` | NO | `0` | - | - | Atribut data temuan kritis |
| 18 | `temuan_tinggi` | `int` | NO | `0` | - | - | Atribut data temuan tinggi |
| 19 | `temuan_sedang` | `int` | NO | `0` | - | - | Atribut data temuan sedang |
| 20 | `temuan_rendah` | `int` | NO | `0` | - | - | Atribut data temuan rendah |
| 21 | `pagu_bermasalah_kritis_tinggi` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data pagu bermasalah kritis tinggi |
| 22 | `skor_rci` | `decimal(5,2)` | NO | `0.00` | - | - | Atribut data skor rci |
| 23 | `predikat_risiko` | `enum('HIJAU','KUNING','ORANYE','MERAH')` | NO | `HIJAU` | - | - | Atribut data predikat risiko |
| 24 | `macro_flags` | `json` | YES | NULL | - | - | Atribut data macro flags |
| 25 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `rup_audit_packages`

- **Nama Tabel:** `rup_audit_packages`
- **Jumlah Kolom:** 44 kolom
- **Definisi:** Struktur persistence data untuk entitas `rup_audit_packages` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `batch_id` | `int unsigned` | NO | NULL | MUL | - | Foreign Key ke batch terkait |
| 3 | `opd_summary_id` | `bigint unsigned` | YES | NULL | - | - | Atribut data opd summary id |
| 4 | `row_index_csv` | `int` | NO | `0` | - | - | Atribut data row index csv |
| 5 | `kode_rup` | `varchar(50)` | NO | `` | - | - | Atribut data kode rup |
| 6 | `nama_instansi` | `varchar(255)` | NO | `` | - | - | Atribut data nama instansi |
| 7 | `nama_satuan_kerja` | `varchar(255)` | NO | `` | - | - | Atribut data nama satuan kerja |
| 8 | `nama_opd_clean` | `varchar(255)` | NO | `` | - | - | Atribut data nama opd clean |
| 9 | `kode_satker_sipd` | `varchar(50)` | NO | `` | - | - | Atribut data kode satker sipd |
| 10 | `tahun_anggaran` | `int` | NO | `2026` | - | - | Atribut data tahun anggaran |
| 11 | `cara_pengadaan` | `varchar(50)` | NO | `Penyedia` | - | - | Atribut data cara pengadaan |
| 12 | `metode_pengadaan` | `varchar(100)` | NO | `-` | - | - | Atribut data metode pengadaan |
| 13 | `metode_arr` | `json` | YES | NULL | - | - | Atribut data metode arr |
| 14 | `jenis_pengadaan` | `varchar(100)` | NO | `-` | - | - | Atribut data jenis pengadaan |
| 15 | `jenis_arr` | `json` | YES | NULL | - | - | Atribut data jenis arr |
| 16 | `nama_paket` | `text` | NO | NULL | - | - | Atribut data nama paket |
| 17 | `nama_paket_clean` | `text` | YES | NULL | - | - | Atribut data nama paket clean |
| 18 | `nama_paket_objek` | `text` | YES | NULL | - | - | Atribut data nama paket objek |
| 19 | `nama_paket_core` | `text` | YES | NULL | - | - | Atribut data nama paket core |
| 20 | `lokasi_extracted` | `varchar(255)` | YES | NULL | - | - | Atribut data lokasi extracted |
| 21 | `sumber_dana` | `varchar(255)` | NO | `APBD` | - | - | Atribut data sumber dana |
| 22 | `sumber_dana_arr` | `json` | YES | NULL | - | - | Atribut data sumber dana arr |
| 23 | `budget_cycle` | `enum('MURNI','PERUBAHAN')` | NO | `MURNI` | - | - | Atribut data budget cycle |
| 24 | `is_blud` | `tinyint(1)` | NO | `0` | - | - | Atribut data is blud |
| 25 | `is_dak` | `tinyint(1)` | NO | `0` | - | - | Atribut data is dak |
| 26 | `is_multiyears` | `tinyint(1)` | NO | `0` | - | - | Atribut data is multiyears |
| 27 | `is_scientific_truncated` | `tinyint(1)` | NO | `0` | - | - | Atribut data is scientific truncated |
| 28 | `is_integrated_package` | `tinyint(1)` | NO | `0` | - | - | Atribut data is integrated package |
| 29 | `produk_dalam_negeri` | `varchar(20)` | NO | `Ya` | - | - | Atribut data produk dalam negeri |
| 30 | `status_umk` | `varchar(20)` | NO | `Ya` | - | - | Atribut data status umk |
| 31 | `total_nilai` | `decimal(20,2)` | NO | `0.00` | - | - | Atribut data total nilai |
| 32 | `cluster_id` | `varchar(100)` | YES | NULL | MUL | - | Atribut data cluster id |
| 33 | `max_severity` | `enum('AMAN','RENDAH','SEDANG','TINGGI','KRITIS','INFO')` | NO | `AMAN` | - | - | Atribut data max severity |
| 34 | `violation_count` | `int` | NO | `0` | - | - | Atribut data violation count |
| 35 | `diff_status` | `enum('NEW','PERSISTENT','FIXED','DELETED','CHANGED')` | NO | `NEW` | - | - | Atribut data diff status |
| 36 | `tanggal_umumkan` | `date` | YES | NULL | - | - | Atribut data tanggal umumkan |
| 37 | `jadwal_pemilihan_awal` | `date` | YES | NULL | - | - | Atribut data jadwal pemilihan awal |
| 38 | `jadwal_kontrak_awal` | `date` | YES | NULL | - | - | Atribut data jadwal kontrak awal |
| 39 | `jadwal_kontrak_akhir` | `date` | YES | NULL | - | - | Atribut data jadwal kontrak akhir |
| 40 | `tipe_swakelola` | `varchar(50)` | YES | NULL | - | - | Atribut data tipe swakelola |
| 41 | `lokasi_pekerjaan` | `text` | YES | NULL | - | - | Atribut data lokasi pekerjaan |
| 42 | `kode_rekening_mak` | `varchar(100)` | YES | NULL | - | - | Atribut data kode rekening mak |
| 43 | `pagu_pengadaan_dpa` | `decimal(20,2)` | YES | NULL | - | - | Atribut data pagu pengadaan dpa |
| 44 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `rup_audit_violations`

- **Nama Tabel:** `rup_audit_violations`
- **Jumlah Kolom:** 11 kolom
- **Definisi:** Struktur persistence data untuk entitas `rup_audit_violations` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `batch_id` | `int unsigned` | NO | NULL | MUL | - | Foreign Key ke batch terkait |
| 3 | `package_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data package id |
| 4 | `rule_id` | `varchar(20)` | NO | NULL | - | - | Atribut data rule id |
| 5 | `severity` | `enum('KRITIS','TINGGI','SEDANG','RENDAH','INFO')` | NO | `SEDANG` | - | - | Atribut data severity |
| 6 | `kategori` | `varchar(100)` | NO | `` | - | - | Atribut data kategori |
| 7 | `regulasi` | `varchar(255)` | NO | `` | - | - | Atribut data regulasi |
| 8 | `deskripsi_pelanggaran` | `text` | NO | NULL | - | - | Atribut data deskripsi pelanggaran |
| 9 | `rekomendasi_perbaikan` | `text` | NO | NULL | - | - | Atribut data rekomendasi perbaikan |
| 10 | `target_perubahan` | `json` | YES | NULL | - | - | Atribut data target perubahan |
| 11 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `satisfaction_surveys`

- **Nama Tabel:** `satisfaction_surveys`
- **Jumlah Kolom:** 18 kolom
- **Definisi:** Struktur persistence data untuk entitas `satisfaction_surveys` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `ticket_number` | `varchar(32)` | NO | NULL | UNI | - | Atribut data ticket number |
| 3 | `rating_overall` | `tinyint` | NO | `5` | MUL | - | Atribut data rating overall |
| 4 | `service_access_rating` | `tinyint` | YES | NULL | - | - | Atribut data service access rating |
| 5 | `service_regulation_rating` | `tinyint` | YES | NULL | - | - | Atribut data service regulation rating |
| 6 | `service_learning_rating` | `tinyint` | YES | NULL | - | - | Atribut data service learning rating |
| 7 | `service_helpdesk_rating` | `tinyint` | YES | NULL | - | - | Atribut data service helpdesk rating |
| 8 | `respondent_role` | `varchar(100)` | NO | `Masyarakat Umum / Pengguna Layanan` | MUL | - | Atribut data respondent role |
| 9 | `is_anonymous` | `tinyint(1)` | NO | `1` | - | - | Atribut data is anonymous |
| 10 | `respondent_name` | `varchar(150)` | YES | NULL | - | - | Atribut data respondent name |
| 11 | `respondent_institution` | `varchar(150)` | YES | NULL | - | - | Atribut data respondent institution |
| 12 | `respondent_contact` | `varchar(100)` | YES | NULL | - | - | Atribut data respondent contact |
| 13 | `feedback` | `text` | YES | NULL | - | - | Atribut data feedback |
| 14 | `recommendation` | `varchar(50)` | NO | `Pasti Merekomendasikan` | - | - | Atribut data recommendation |
| 15 | `ip_address` | `varchar(45)` | YES | NULL | - | - | Alamat IP client jaringan |
| 16 | `user_agent` | `varchar(255)` | YES | NULL | - | - | Informasi browser/klien pengguna |
| 17 | `created_at` | `datetime` | YES | NULL | MUL | - | Waktu pembuatan data (UTC timestamp) |
| 18 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `service_categories`

- **Nama Tabel:** `service_categories`
- **Jumlah Kolom:** 8 kolom
- **Definisi:** Struktur persistence data untuk entitas `service_categories` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `name` | `varchar(150)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 3 | `code` | `varchar(50)` | NO | NULL | UNI | - | Atribut data code |
| 4 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 5 | `sort_order` | `int` | NO | `0` | - | - | Urutan tampilan data |
| 6 | `is_active` | `tinyint(1)` | NO | `1` | MUL | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 7 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 8 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `site_announcements`

- **Nama Tabel:** `site_announcements`
- **Jumlah Kolom:** 13 kolom
- **Definisi:** Struktur persistence data untuk entitas `site_announcements` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `badge_label` | `varchar(100)` | YES | NULL | - | - | Atribut data badge label |
| 3 | `title` | `varchar(255)` | NO | NULL | - | - | Judul resmi entitas |
| 4 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 5 | `image_url` | `varchar(255)` | YES | NULL | - | - | Atribut data image url |
| 6 | `cta_label` | `varchar(100)` | YES | NULL | - | - | Atribut data cta label |
| 7 | `cta_url` | `varchar(255)` | YES | NULL | - | - | Atribut data cta url |
| 8 | `start_date` | `date` | YES | NULL | - | - | Atribut data start date |
| 9 | `end_date` | `date` | YES | NULL | - | - | Atribut data end date |
| 10 | `sort_order` | `int` | NO | `0` | - | - | Urutan tampilan data |
| 11 | `is_active` | `tinyint(1)` | NO | `1` | MUL | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 12 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 13 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `site_settings`

- **Nama Tabel:** `site_settings`
- **Jumlah Kolom:** 8 kolom
- **Definisi:** Struktur persistence data untuk entitas `site_settings` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `setting_group` | `varchar(50)` | NO | `general` | MUL | - | Atribut data setting group |
| 3 | `setting_key` | `varchar(100)` | NO | NULL | - | - | Atribut data setting key |
| 4 | `setting_value` | `longtext` | YES | NULL | - | - | Atribut data setting value |
| 5 | `field_type` | `varchar(30)` | NO | `text` | - | - | Atribut data field type |
| 6 | `description` | `varchar(255)` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 7 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 8 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `survey_aspects`

- **Nama Tabel:** `survey_aspects`
- **Jumlah Kolom:** 10 kolom
- **Definisi:** Struktur persistence data untuk entitas `survey_aspects` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `aspect_key` | `varchar(50)` | NO | NULL | UNI | - | Atribut data aspect key |
| 3 | `title` | `varchar(150)` | NO | NULL | - | - | Judul resmi entitas |
| 4 | `description` | `text` | YES | NULL | - | - | Deskripsi rinci konten atau entitas |
| 5 | `icon_class` | `varchar(100)` | YES | NULL | - | - | Atribut data icon class |
| 6 | `weight` | `decimal(5,2)` | NO | `1.00` | - | - | Atribut data weight |
| 7 | `sort_order` | `int` | NO | `0` | - | - | Urutan tampilan data |
| 8 | `is_active` | `tinyint(1)` | NO | `1` | MUL | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 9 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 10 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `team_members`

- **Nama Tabel:** `team_members`
- **Jumlah Kolom:** 12 kolom
- **Definisi:** Struktur persistence data untuk entitas `team_members` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `name` | `varchar(150)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 3 | `role_title` | `varchar(150)` | NO | NULL | - | - | Atribut data role title |
| 4 | `nip` | `varchar(50)` | YES | NULL | - | - | Atribut data nip |
| 5 | `category` | `varchar(50)` | NO | `pembina` | MUL | - | Atribut data category |
| 6 | `photo_url` | `varchar(255)` | YES | NULL | - | - | Atribut data photo url |
| 7 | `bio` | `text` | YES | NULL | - | - | Atribut data bio |
| 8 | `social_links` | `json` | YES | NULL | - | - | Atribut data social links |
| 9 | `sort_order` | `int` | NO | `0` | - | - | Urutan tampilan data |
| 10 | `is_active` | `tinyint(1)` | NO | `1` | - | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 11 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 12 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `testimonials`

- **Nama Tabel:** `testimonials`
- **Jumlah Kolom:** 13 kolom
- **Definisi:** Struktur persistence data untuk entitas `testimonials` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `name` | `varchar(150)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 3 | `role_title` | `varchar(150)` | YES | NULL | - | - | Atribut data role title |
| 4 | `organization` | `varchar(150)` | YES | NULL | - | - | Atribut data organization |
| 5 | `avatar_url` | `varchar(255)` | YES | NULL | - | - | Atribut data avatar url |
| 6 | `rating` | `tinyint` | NO | `5` | - | - | Atribut data rating |
| 7 | `quote` | `text` | NO | NULL | - | - | Atribut data quote |
| 8 | `impact_statement` | `varchar(255)` | YES | NULL | - | - | Atribut data impact statement |
| 9 | `category` | `varchar(50)` | NO | `opd` | - | - | Atribut data category |
| 10 | `sort_order` | `int` | NO | `0` | - | - | Urutan tampilan data |
| 11 | `is_active` | `tinyint(1)` | NO | `1` | MUL | - | Flag status aktif record (1 = aktif, 0 = nonaktif) |
| 12 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 13 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `user_procurement_roles`

- **Nama Tabel:** `user_procurement_roles`
- **Jumlah Kolom:** 11 kolom
- **Definisi:** Struktur persistence data untuk entitas `user_procurement_roles` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `procurement_role_id` | `bigint unsigned` | NO | NULL | MUL | - | Atribut data procurement role id |
| 4 | `verification_status` | `varchar(64)` | NO | `self_declared` | MUL | - | Atribut data verification status |
| 5 | `verified_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data verified by |
| 6 | `verified_at` | `datetime` | YES | NULL | - | - | Atribut data verified at |
| 7 | `assignment_start_at` | `datetime` | YES | NULL | - | - | Atribut data assignment start at |
| 8 | `assignment_end_at` | `datetime` | YES | NULL | - | - | Atribut data assignment end at |
| 9 | `evidence_file_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data evidence file id |
| 10 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 11 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `user_profiles`

- **Nama Tabel:** `user_profiles`
- **Jumlah Kolom:** 10 kolom
- **Definisi:** Struktur persistence data untuk entitas `user_profiles` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | UNI | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `address_line` | `text` | NO | NULL | - | - | Atribut data address line |
| 4 | `province_code` | `varchar(13)` | NO | NULL | MUL | - | Atribut data province code |
| 5 | `regency_code` | `varchar(13)` | NO | NULL | MUL | - | Atribut data regency code |
| 6 | `district_code` | `varchar(13)` | NO | NULL | MUL | - | Atribut data district code |
| 7 | `village_code` | `varchar(13)` | YES | NULL | MUL | - | Atribut data village code |
| 8 | `profile_completion_status` | `varchar(64)` | NO | `draft` | - | - | Atribut data profile completion status |
| 9 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 10 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `user_roles`

- **Nama Tabel:** `user_roles`
- **Jumlah Kolom:** 11 kolom
- **Definisi:** Struktur persistence data untuk entitas `user_roles` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `user_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel users (pemilik/aktor) |
| 3 | `role_id` | `bigint unsigned` | NO | NULL | MUL | - | Foreign Key ke tabel roles |
| 4 | `status` | `varchar(64)` | NO | `active` | - | - | Status operasional record sesuai CHECK constraint |
| 5 | `granted_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data granted by |
| 6 | `granted_at` | `datetime` | NO | NULL | - | - | Atribut data granted at |
| 7 | `revoked_by` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data revoked by |
| 8 | `revoked_at` | `datetime` | YES | NULL | - | - | Atribut data revoked at |
| 9 | `reason` | `text` | YES | NULL | - | - | Atribut data reason |
| 10 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 11 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `users`

- **Nama Tabel:** `users`
- **Jumlah Kolom:** 15 kolom
- **Definisi:** Struktur persistence data untuk entitas `users` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `name` | `varchar(255)` | NO | NULL | - | - | Nama entitas atau pengguna |
| 3 | `email` | `varchar(255)` | NO | NULL | UNI | - | Alamat surel resmi unik |
| 4 | `username` | `varchar(255)` | NO | NULL | UNI | - | Atribut data username |
| 5 | `password_hash` | `varchar(255)` | NO | NULL | - | - | Hash kata sandi terenkripsi (Argon2id/Bcrypt) |
| 6 | `phone` | `varchar(32)` | NO | NULL | - | - | Atribut data phone |
| 7 | `profile_type` | `varchar(64)` | NO | NULL | MUL | - | Atribut data profile type |
| 8 | `account_status` | `varchar(64)` | NO | `draft` | MUL | - | Atribut data account status |
| 9 | `email_verified_at` | `datetime` | YES | NULL | MUL | - | Atribut data email verified at |
| 10 | `last_login_at` | `datetime` | YES | NULL | - | - | Atribut data last login at |
| 11 | `session_version` | `bigint unsigned` | NO | `1` | - | - | Atribut data session version |
| 12 | `status_reason` | `text` | YES | NULL | - | - | Atribut data status reason |
| 13 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 14 | `updated_at` | `datetime` | NO | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |
| 15 | `deleted_at` | `datetime` | YES | NULL | MUL | - | Penanda soft-delete data (NULL jika aktif) |

---

### Tabel: `visitor_ip_logs`

- **Nama Tabel:** `visitor_ip_logs`
- **Jumlah Kolom:** 6 kolom
- **Definisi:** Struktur persistence data untuk entitas `visitor_ip_logs` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `ip_address` | `varchar(45)` | NO | NULL | MUL | - | Alamat IP client jaringan |
| 3 | `feature` | `varchar(50)` | NO | `home` | MUL | - | Atribut data feature |
| 4 | `visit_date` | `date` | NO | NULL | MUL | - | Atribut data visit date |
| 5 | `user_agent` | `varchar(255)` | YES | NULL | - | - | Informasi browser/klien pengguna |
| 6 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

### Tabel: `visitor_statistics`

- **Nama Tabel:** `visitor_statistics`
- **Jumlah Kolom:** 6 kolom
- **Definisi:** Struktur persistence data untuk entitas `visitor_statistics` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `feature` | `varchar(50)` | NO | `home` | MUL | - | Atribut data feature |
| 3 | `visit_date` | `date` | NO | NULL | MUL | - | Atribut data visit date |
| 4 | `hit_count` | `int unsigned` | NO | `1` | - | - | Atribut data hit count |
| 5 | `created_at` | `datetime` | YES | NULL | - | - | Waktu pembuatan data (UTC timestamp) |
| 6 | `updated_at` | `datetime` | YES | NULL | - | - | Waktu pembaruan data terakhir (UTC timestamp) |

---

### Tabel: `wilayah`

- **Nama Tabel:** `wilayah`
- **Jumlah Kolom:** 2 kolom
- **Definisi:** Struktur persistence data untuk entitas `wilayah` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `kode` | `varchar(13)` | NO | NULL | PRI | - | Atribut data kode |
| 2 | `nama` | `varchar(100)` | NO | NULL | MUL | - | Atribut data nama |

---

### Tabel: `workflow_transitions`

- **Nama Tabel:** `workflow_transitions`
- **Jumlah Kolom:** 12 kolom
- **Definisi:** Struktur persistence data untuk entitas `workflow_transitions` pada ekosistem BegawiPengadaan.

| No | Nama Kolom | Tipe Data | Nullable | Default | Key | Extra | Keterangan Fungsi |
|---|---|---|:---:|---|:---:|---|---|
| 1 | `id` | `bigint unsigned` | NO | NULL | PRI | auto_increment | Primary Key unik record (Auto-Increment) |
| 2 | `resource_type` | `varchar(128)` | NO | NULL | MUL | - | Atribut data resource type |
| 3 | `resource_id` | `bigint unsigned` | NO | NULL | - | - | Atribut data resource id |
| 4 | `from_state` | `varchar(64)` | YES | NULL | - | - | Atribut data from state |
| 5 | `to_state` | `varchar(64)` | NO | NULL | MUL | - | Atribut data to state |
| 6 | `action` | `varchar(128)` | NO | NULL | - | - | Atribut data action |
| 7 | `actor_id` | `bigint unsigned` | YES | NULL | MUL | - | Atribut data actor id |
| 8 | `actor_type` | `varchar(32)` | NO | `user` | - | - | Atribut data actor type |
| 9 | `reason` | `text` | YES | NULL | - | - | Atribut data reason |
| 10 | `notes` | `text` | YES | NULL | - | - | Atribut data notes |
| 11 | `metadata` | `json` | YES | NULL | - | - | Atribut data metadata |
| 12 | `created_at` | `datetime` | NO | NULL | - | - | Waktu pembuatan data (UTC timestamp) |

---

